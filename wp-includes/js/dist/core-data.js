/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 287:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   fL: () => (/* binding */ pascalCase),
/* harmony export */   l3: () => (/* binding */ pascalCaseTransform)
/* harmony export */ });
/* unused harmony export pascalCaseTransformMerge */
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(1635);
/* harmony import */ var no_case__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(2226);


function pascalCaseTransform(input, index) {
    var firstChar = input.charAt(0);
    var lowerChars = input.substr(1).toLowerCase();
    if (index > 0 && firstChar >= "0" && firstChar <= "9") {
        return "_" + firstChar + lowerChars;
    }
    return "" + firstChar.toUpperCase() + lowerChars;
}
function pascalCaseTransformMerge(input) {
    return input.charAt(0).toUpperCase() + input.slice(1).toLowerCase();
}
function pascalCase(input, options) {
    if (options === void 0) { options = {}; }
    return (0,no_case__WEBPACK_IMPORTED_MODULE_0__/* .noCase */ .W)(input, (0,tslib__WEBPACK_IMPORTED_MODULE_1__/* .__assign */ .Cl)({ delimiter: "", transform: pascalCaseTransform }, options));
}


/***/ }),

/***/ 533:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ get_normalized_comma_separable_default)
/* harmony export */ });
function getNormalizedCommaSeparable(value) {
  if (typeof value === "string") {
    return value.split(",");
  } else if (Array.isArray(value)) {
    return value;
  }
  return null;
}
var get_normalized_comma_separable_default = getNormalizedCommaSeparable;



/***/ }),

/***/ 1455:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["apiFetch"];

/***/ }),

/***/ 1635:
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Cl: () => (/* binding */ __assign)
/* harmony export */ });
/* unused harmony exports __extends, __rest, __decorate, __param, __esDecorate, __runInitializers, __propKey, __setFunctionName, __metadata, __awaiter, __generator, __createBinding, __exportStar, __values, __read, __spread, __spreadArrays, __spreadArray, __await, __asyncGenerator, __asyncDelegator, __asyncValues, __makeTemplateObject, __importStar, __importDefault, __classPrivateFieldGet, __classPrivateFieldSet, __classPrivateFieldIn, __addDisposableResource, __disposeResources, __rewriteRelativeImportExtension */
/******************************************************************************
Copyright (c) Microsoft Corporation.

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
PERFORMANCE OF THIS SOFTWARE.
***************************************************************************** */
/* global Reflect, Promise, SuppressedError, Symbol, Iterator */

var extendStatics = function(d, b) {
  extendStatics = Object.setPrototypeOf ||
      ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
      function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
  return extendStatics(d, b);
};

function __extends(d, b) {
  if (typeof b !== "function" && b !== null)
      throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
  extendStatics(d, b);
  function __() { this.constructor = d; }
  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
}

var __assign = function() {
  __assign = Object.assign || function __assign(t) {
      for (var s, i = 1, n = arguments.length; i < n; i++) {
          s = arguments[i];
          for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
      }
      return t;
  }
  return __assign.apply(this, arguments);
}

function __rest(s, e) {
  var t = {};
  for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p) && e.indexOf(p) < 0)
      t[p] = s[p];
  if (s != null && typeof Object.getOwnPropertySymbols === "function")
      for (var i = 0, p = Object.getOwnPropertySymbols(s); i < p.length; i++) {
          if (e.indexOf(p[i]) < 0 && Object.prototype.propertyIsEnumerable.call(s, p[i]))
              t[p[i]] = s[p[i]];
      }
  return t;
}

function __decorate(decorators, target, key, desc) {
  var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
  if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
  else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  return c > 3 && r && Object.defineProperty(target, key, r), r;
}

function __param(paramIndex, decorator) {
  return function (target, key) { decorator(target, key, paramIndex); }
}

function __esDecorate(ctor, descriptorIn, decorators, contextIn, initializers, extraInitializers) {
  function accept(f) { if (f !== void 0 && typeof f !== "function") throw new TypeError("Function expected"); return f; }
  var kind = contextIn.kind, key = kind === "getter" ? "get" : kind === "setter" ? "set" : "value";
  var target = !descriptorIn && ctor ? contextIn["static"] ? ctor : ctor.prototype : null;
  var descriptor = descriptorIn || (target ? Object.getOwnPropertyDescriptor(target, contextIn.name) : {});
  var _, done = false;
  for (var i = decorators.length - 1; i >= 0; i--) {
      var context = {};
      for (var p in contextIn) context[p] = p === "access" ? {} : contextIn[p];
      for (var p in contextIn.access) context.access[p] = contextIn.access[p];
      context.addInitializer = function (f) { if (done) throw new TypeError("Cannot add initializers after decoration has completed"); extraInitializers.push(accept(f || null)); };
      var result = (0, decorators[i])(kind === "accessor" ? { get: descriptor.get, set: descriptor.set } : descriptor[key], context);
      if (kind === "accessor") {
          if (result === void 0) continue;
          if (result === null || typeof result !== "object") throw new TypeError("Object expected");
          if (_ = accept(result.get)) descriptor.get = _;
          if (_ = accept(result.set)) descriptor.set = _;
          if (_ = accept(result.init)) initializers.unshift(_);
      }
      else if (_ = accept(result)) {
          if (kind === "field") initializers.unshift(_);
          else descriptor[key] = _;
      }
  }
  if (target) Object.defineProperty(target, contextIn.name, descriptor);
  done = true;
};

function __runInitializers(thisArg, initializers, value) {
  var useValue = arguments.length > 2;
  for (var i = 0; i < initializers.length; i++) {
      value = useValue ? initializers[i].call(thisArg, value) : initializers[i].call(thisArg);
  }
  return useValue ? value : void 0;
};

function __propKey(x) {
  return typeof x === "symbol" ? x : "".concat(x);
};

function __setFunctionName(f, name, prefix) {
  if (typeof name === "symbol") name = name.description ? "[".concat(name.description, "]") : "";
  return Object.defineProperty(f, "name", { configurable: true, value: prefix ? "".concat(prefix, " ", name) : name });
};

function __metadata(metadataKey, metadataValue) {
  if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(metadataKey, metadataValue);
}

function __awaiter(thisArg, _arguments, P, generator) {
  function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
  return new (P || (P = Promise))(function (resolve, reject) {
      function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
      function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
      function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
      step((generator = generator.apply(thisArg, _arguments || [])).next());
  });
}

function __generator(thisArg, body) {
  var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g = Object.create((typeof Iterator === "function" ? Iterator : Object).prototype);
  return g.next = verb(0), g["throw"] = verb(1), g["return"] = verb(2), typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
  function verb(n) { return function (v) { return step([n, v]); }; }
  function step(op) {
      if (f) throw new TypeError("Generator is already executing.");
      while (g && (g = 0, op[0] && (_ = 0)), _) try {
          if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
          if (y = 0, t) op = [op[0] & 2, t.value];
          switch (op[0]) {
              case 0: case 1: t = op; break;
              case 4: _.label++; return { value: op[1], done: false };
              case 5: _.label++; y = op[1]; op = [0]; continue;
              case 7: op = _.ops.pop(); _.trys.pop(); continue;
              default:
                  if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                  if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                  if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                  if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                  if (t[2]) _.ops.pop();
                  _.trys.pop(); continue;
          }
          op = body.call(thisArg, _);
      } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
      if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
  }
}

var __createBinding = Object.create ? (function(o, m, k, k2) {
  if (k2 === undefined) k2 = k;
  var desc = Object.getOwnPropertyDescriptor(m, k);
  if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
  }
  Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
  if (k2 === undefined) k2 = k;
  o[k2] = m[k];
});

function __exportStar(m, o) {
  for (var p in m) if (p !== "default" && !Object.prototype.hasOwnProperty.call(o, p)) __createBinding(o, m, p);
}

function __values(o) {
  var s = typeof Symbol === "function" && Symbol.iterator, m = s && o[s], i = 0;
  if (m) return m.call(o);
  if (o && typeof o.length === "number") return {
      next: function () {
          if (o && i >= o.length) o = void 0;
          return { value: o && o[i++], done: !o };
      }
  };
  throw new TypeError(s ? "Object is not iterable." : "Symbol.iterator is not defined.");
}

function __read(o, n) {
  var m = typeof Symbol === "function" && o[Symbol.iterator];
  if (!m) return o;
  var i = m.call(o), r, ar = [], e;
  try {
      while ((n === void 0 || n-- > 0) && !(r = i.next()).done) ar.push(r.value);
  }
  catch (error) { e = { error: error }; }
  finally {
      try {
          if (r && !r.done && (m = i["return"])) m.call(i);
      }
      finally { if (e) throw e.error; }
  }
  return ar;
}

/** @deprecated */
function __spread() {
  for (var ar = [], i = 0; i < arguments.length; i++)
      ar = ar.concat(__read(arguments[i]));
  return ar;
}

/** @deprecated */
function __spreadArrays() {
  for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
  for (var r = Array(s), k = 0, i = 0; i < il; i++)
      for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
          r[k] = a[j];
  return r;
}

function __spreadArray(to, from, pack) {
  if (pack || arguments.length === 2) for (var i = 0, l = from.length, ar; i < l; i++) {
      if (ar || !(i in from)) {
          if (!ar) ar = Array.prototype.slice.call(from, 0, i);
          ar[i] = from[i];
      }
  }
  return to.concat(ar || Array.prototype.slice.call(from));
}

function __await(v) {
  return this instanceof __await ? (this.v = v, this) : new __await(v);
}

function __asyncGenerator(thisArg, _arguments, generator) {
  if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
  var g = generator.apply(thisArg, _arguments || []), i, q = [];
  return i = Object.create((typeof AsyncIterator === "function" ? AsyncIterator : Object).prototype), verb("next"), verb("throw"), verb("return", awaitReturn), i[Symbol.asyncIterator] = function () { return this; }, i;
  function awaitReturn(f) { return function (v) { return Promise.resolve(v).then(f, reject); }; }
  function verb(n, f) { if (g[n]) { i[n] = function (v) { return new Promise(function (a, b) { q.push([n, v, a, b]) > 1 || resume(n, v); }); }; if (f) i[n] = f(i[n]); } }
  function resume(n, v) { try { step(g[n](v)); } catch (e) { settle(q[0][3], e); } }
  function step(r) { r.value instanceof __await ? Promise.resolve(r.value.v).then(fulfill, reject) : settle(q[0][2], r); }
  function fulfill(value) { resume("next", value); }
  function reject(value) { resume("throw", value); }
  function settle(f, v) { if (f(v), q.shift(), q.length) resume(q[0][0], q[0][1]); }
}

function __asyncDelegator(o) {
  var i, p;
  return i = {}, verb("next"), verb("throw", function (e) { throw e; }), verb("return"), i[Symbol.iterator] = function () { return this; }, i;
  function verb(n, f) { i[n] = o[n] ? function (v) { return (p = !p) ? { value: __await(o[n](v)), done: false } : f ? f(v) : v; } : f; }
}

function __asyncValues(o) {
  if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
  var m = o[Symbol.asyncIterator], i;
  return m ? m.call(o) : (o = typeof __values === "function" ? __values(o) : o[Symbol.iterator](), i = {}, verb("next"), verb("throw"), verb("return"), i[Symbol.asyncIterator] = function () { return this; }, i);
  function verb(n) { i[n] = o[n] && function (v) { return new Promise(function (resolve, reject) { v = o[n](v), settle(resolve, reject, v.done, v.value); }); }; }
  function settle(resolve, reject, d, v) { Promise.resolve(v).then(function(v) { resolve({ value: v, done: d }); }, reject); }
}

function __makeTemplateObject(cooked, raw) {
  if (Object.defineProperty) { Object.defineProperty(cooked, "raw", { value: raw }); } else { cooked.raw = raw; }
  return cooked;
};

var __setModuleDefault = Object.create ? (function(o, v) {
  Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
  o["default"] = v;
};

var ownKeys = function(o) {
  ownKeys = Object.getOwnPropertyNames || function (o) {
    var ar = [];
    for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
    return ar;
  };
  return ownKeys(o);
};

function __importStar(mod) {
  if (mod && mod.__esModule) return mod;
  var result = {};
  if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
  __setModuleDefault(result, mod);
  return result;
}

function __importDefault(mod) {
  return (mod && mod.__esModule) ? mod : { default: mod };
}

function __classPrivateFieldGet(receiver, state, kind, f) {
  if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a getter");
  if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot read private member from an object whose class did not declare it");
  return kind === "m" ? f : kind === "a" ? f.call(receiver) : f ? f.value : state.get(receiver);
}

function __classPrivateFieldSet(receiver, state, value, kind, f) {
  if (kind === "m") throw new TypeError("Private method is not writable");
  if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a setter");
  if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot write private member to an object whose class did not declare it");
  return (kind === "a" ? f.call(receiver, value) : f ? f.value = value : state.set(receiver, value)), value;
}

function __classPrivateFieldIn(state, receiver) {
  if (receiver === null || (typeof receiver !== "object" && typeof receiver !== "function")) throw new TypeError("Cannot use 'in' operator on non-object");
  return typeof state === "function" ? receiver === state : state.has(receiver);
}

function __addDisposableResource(env, value, async) {
  if (value !== null && value !== void 0) {
    if (typeof value !== "object" && typeof value !== "function") throw new TypeError("Object expected.");
    var dispose, inner;
    if (async) {
      if (!Symbol.asyncDispose) throw new TypeError("Symbol.asyncDispose is not defined.");
      dispose = value[Symbol.asyncDispose];
    }
    if (dispose === void 0) {
      if (!Symbol.dispose) throw new TypeError("Symbol.dispose is not defined.");
      dispose = value[Symbol.dispose];
      if (async) inner = dispose;
    }
    if (typeof dispose !== "function") throw new TypeError("Object not disposable.");
    if (inner) dispose = function() { try { inner.call(this); } catch (e) { return Promise.reject(e); } };
    env.stack.push({ value: value, dispose: dispose, async: async });
  }
  else if (async) {
    env.stack.push({ async: true });
  }
  return value;
}

var _SuppressedError = typeof SuppressedError === "function" ? SuppressedError : function (error, suppressed, message) {
  var e = new Error(message);
  return e.name = "SuppressedError", e.error = error, e.suppressed = suppressed, e;
};

function __disposeResources(env) {
  function fail(e) {
    env.error = env.hasError ? new _SuppressedError(e, env.error, "An error was suppressed during disposal.") : e;
    env.hasError = true;
  }
  var r, s = 0;
  function next() {
    while (r = env.stack.pop()) {
      try {
        if (!r.async && s === 1) return s = 0, env.stack.push(r), Promise.resolve().then(next);
        if (r.dispose) {
          var result = r.dispose.call(r.value);
          if (r.async) return s |= 2, Promise.resolve(result).then(next, function(e) { fail(e); return next(); });
        }
        else s |= 1;
      }
      catch (e) {
        fail(e);
      }
    }
    if (s === 1) return env.hasError ? Promise.reject(env.error) : Promise.resolve();
    if (env.hasError) throw env.error;
  }
  return next();
}

function __rewriteRelativeImportExtension(path, preserveJsx) {
  if (typeof path === "string" && /^\.\.?\//.test(path)) {
      return path.replace(/\.(tsx)$|((?:\.d)?)((?:\.[^./]+?)?)\.([cm]?)ts$/i, function (m, tsx, d, ext, cm) {
          return tsx ? preserveJsx ? ".jsx" : ".js" : d && (!ext || !cm) ? m : (d + ext + "." + cm.toLowerCase() + "js");
      });
  }
  return path;
}

/* unused harmony default export */ var __WEBPACK_DEFAULT_EXPORT__ = ({
  __extends,
  __assign,
  __rest,
  __decorate,
  __param,
  __esDecorate,
  __runInitializers,
  __propKey,
  __setFunctionName,
  __metadata,
  __awaiter,
  __generator,
  __createBinding,
  __exportStar,
  __values,
  __read,
  __spread,
  __spreadArrays,
  __spreadArray,
  __await,
  __asyncGenerator,
  __asyncDelegator,
  __asyncValues,
  __makeTemplateObject,
  __importStar,
  __importDefault,
  __classPrivateFieldGet,
  __classPrivateFieldSet,
  __classPrivateFieldIn,
  __addDisposableResource,
  __disposeResources,
  __rewriteRelativeImportExtension,
});


/***/ }),

/***/ 2226:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   W: () => (/* binding */ noCase)
/* harmony export */ });
/* harmony import */ var lower_case__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(7314);

// Support camel case ("camelCase" -> "camel Case" and "CAMELCase" -> "CAMEL Case").
var DEFAULT_SPLIT_REGEXP = [/([a-z0-9])([A-Z])/g, /([A-Z])([A-Z][a-z])/g];
// Remove all non-word characters.
var DEFAULT_STRIP_REGEXP = /[^A-Z0-9]+/gi;
/**
 * Normalize the string into something other libraries can manipulate easier.
 */
function noCase(input, options) {
    if (options === void 0) { options = {}; }
    var _a = options.splitRegexp, splitRegexp = _a === void 0 ? DEFAULT_SPLIT_REGEXP : _a, _b = options.stripRegexp, stripRegexp = _b === void 0 ? DEFAULT_STRIP_REGEXP : _b, _c = options.transform, transform = _c === void 0 ? lower_case__WEBPACK_IMPORTED_MODULE_0__/* .lowerCase */ .g : _c, _d = options.delimiter, delimiter = _d === void 0 ? " " : _d;
    var result = replace(replace(input, splitRegexp, "$1\0$2"), stripRegexp, "\0");
    var start = 0;
    var end = result.length;
    // Trim the delimiter from around the output string.
    while (result.charAt(start) === "\0")
        start++;
    while (result.charAt(end - 1) === "\0")
        end--;
    // Transform each token independently.
    return result.slice(start, end).split("\0").map(transform).join(delimiter);
}
/**
 * Replace `re` in the input string with the replacement value.
 */
function replace(input, re, value) {
    if (re instanceof RegExp)
        return input.replace(re, value);
    return re.reduce(function (input, re) { return input.replace(re, value); }, input);
}


/***/ }),

/***/ 2239:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  A: () => (/* binding */ createLocksActions)
});

;// ./node_modules/@wordpress/core-data/build-module/locks/utils.js
function deepCopyLocksTreePath(tree, path) {
  const newTree = { ...tree };
  let currentNode = newTree;
  for (const branchName of path) {
    currentNode.children = {
      ...currentNode.children,
      [branchName]: {
        locks: [],
        children: {},
        ...currentNode.children[branchName]
      }
    };
    currentNode = currentNode.children[branchName];
  }
  return newTree;
}
function getNode(tree, path) {
  let currentNode = tree;
  for (const branchName of path) {
    const nextNode = currentNode.children[branchName];
    if (!nextNode) {
      return null;
    }
    currentNode = nextNode;
  }
  return currentNode;
}
function* iteratePath(tree, path) {
  let currentNode = tree;
  yield currentNode;
  for (const branchName of path) {
    const nextNode = currentNode.children[branchName];
    if (!nextNode) {
      break;
    }
    yield nextNode;
    currentNode = nextNode;
  }
}
function* iterateDescendants(node) {
  const stack = Object.values(node.children);
  while (stack.length) {
    const childNode = stack.pop();
    yield childNode;
    stack.push(...Object.values(childNode.children));
  }
}
function hasConflictingLock({ exclusive }, locks) {
  if (exclusive && locks.length) {
    return true;
  }
  if (!exclusive && locks.filter((lock) => lock.exclusive).length) {
    return true;
  }
  return false;
}


;// ./node_modules/@wordpress/core-data/build-module/locks/reducer.js

const DEFAULT_STATE = {
  requests: [],
  tree: {
    locks: [],
    children: {}
  }
};
function locks(state = DEFAULT_STATE, action) {
  switch (action.type) {
    case "ENQUEUE_LOCK_REQUEST": {
      const { request } = action;
      return {
        ...state,
        requests: [request, ...state.requests]
      };
    }
    case "GRANT_LOCK_REQUEST": {
      const { lock, request } = action;
      const { store, path } = request;
      const storePath = [store, ...path];
      const newTree = deepCopyLocksTreePath(state.tree, storePath);
      const node = getNode(newTree, storePath);
      node.locks = [...node.locks, lock];
      return {
        ...state,
        requests: state.requests.filter((r) => r !== request),
        tree: newTree
      };
    }
    case "RELEASE_LOCK": {
      const { lock } = action;
      const storePath = [lock.store, ...lock.path];
      const newTree = deepCopyLocksTreePath(state.tree, storePath);
      const node = getNode(newTree, storePath);
      node.locks = node.locks.filter((l) => l !== lock);
      return {
        ...state,
        tree: newTree
      };
    }
  }
  return state;
}


;// ./node_modules/@wordpress/core-data/build-module/locks/selectors.js

function getPendingLockRequests(state) {
  return state.requests;
}
function isLockAvailable(state, store, path, { exclusive }) {
  const storePath = [store, ...path];
  const locks = state.tree;
  for (const node2 of iteratePath(locks, storePath)) {
    if (hasConflictingLock({ exclusive }, node2.locks)) {
      return false;
    }
  }
  const node = getNode(locks, storePath);
  if (!node) {
    return true;
  }
  for (const descendant of iterateDescendants(node)) {
    if (hasConflictingLock({ exclusive }, descendant.locks)) {
      return false;
    }
  }
  return true;
}


;// ./node_modules/@wordpress/core-data/build-module/locks/engine.js


function createLocks() {
  let state = locks(void 0, { type: "@@INIT" });
  function processPendingLockRequests() {
    for (const request of getPendingLockRequests(state)) {
      const { store, path, exclusive, notifyAcquired } = request;
      if (isLockAvailable(state, store, path, { exclusive })) {
        const lock = { store, path, exclusive };
        state = locks(state, {
          type: "GRANT_LOCK_REQUEST",
          lock,
          request
        });
        notifyAcquired(lock);
      }
    }
  }
  function acquire(store, path, exclusive) {
    return new Promise((resolve) => {
      state = locks(state, {
        type: "ENQUEUE_LOCK_REQUEST",
        request: { store, path, exclusive, notifyAcquired: resolve }
      });
      processPendingLockRequests();
    });
  }
  function release(lock) {
    state = locks(state, {
      type: "RELEASE_LOCK",
      lock
    });
    processPendingLockRequests();
  }
  return { acquire, release };
}


;// ./node_modules/@wordpress/core-data/build-module/locks/actions.js

function createLocksActions() {
  const locks = createLocks();
  function __unstableAcquireStoreLock(store, path, { exclusive }) {
    return () => locks.acquire(store, path, exclusive);
  }
  function __unstableReleaseStoreLock(lock) {
    return () => locks.release(lock);
  }
  return { __unstableAcquireStoreLock, __unstableReleaseStoreLock };
}



/***/ }),

/***/ 2278:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   E: () => (/* binding */ STORE_NAME)
/* harmony export */ });
const STORE_NAME = "core";



/***/ }),

/***/ 2577:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CO: () => (/* binding */ ALLOWED_RESOURCE_ACTIONS),
/* harmony export */   kC: () => (/* binding */ getUserPermissionCacheKey),
/* harmony export */   qY: () => (/* binding */ getUserPermissionsFromAllowHeader)
/* harmony export */ });
const ALLOWED_RESOURCE_ACTIONS = [
  "create",
  "read",
  "update",
  "delete"
];
function getUserPermissionsFromAllowHeader(allowedMethods) {
  const permissions = {};
  if (!allowedMethods) {
    return permissions;
  }
  const methods = {
    create: "POST",
    read: "GET",
    update: "PUT",
    delete: "DELETE"
  };
  for (const [actionName, methodName] of Object.entries(methods)) {
    permissions[actionName] = allowedMethods.includes(methodName);
  }
  return permissions;
}
function getUserPermissionCacheKey(action, resource, id) {
  const key = (typeof resource === "object" ? [action, resource.kind, resource.name, resource.id] : [action, resource, id]).filter(Boolean).join("/");
  return key;
}



/***/ }),

/***/ 2859:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   n: () => (/* binding */ Status)
/* harmony export */ });
var Status = /* @__PURE__ */ ((Status2) => {
  Status2["Idle"] = "IDLE";
  Status2["Resolving"] = "RESOLVING";
  Status2["Error"] = "ERROR";
  Status2["Success"] = "SUCCESS";
  return Status2;
})(Status || {});



/***/ }),

/***/ 3249:
/***/ ((module) => {

"use strict";


function _typeof(obj) {
  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function (obj) {
      return typeof obj;
    };
  } else {
    _typeof = function (obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}

/**
 * Given an instance of EquivalentKeyMap, returns its internal value pair tuple
 * for a key, if one exists. The tuple members consist of the last reference
 * value for the key (used in efficient subsequent lookups) and the value
 * assigned for the key at the leaf node.
 *
 * @param {EquivalentKeyMap} instance EquivalentKeyMap instance.
 * @param {*} key                     The key for which to return value pair.
 *
 * @return {?Array} Value pair, if exists.
 */
function getValuePair(instance, key) {
  var _map = instance._map,
      _arrayTreeMap = instance._arrayTreeMap,
      _objectTreeMap = instance._objectTreeMap; // Map keeps a reference to the last object-like key used to set the
  // value, which can be used to shortcut immediately to the value.

  if (_map.has(key)) {
    return _map.get(key);
  } // Sort keys to ensure stable retrieval from tree.


  var properties = Object.keys(key).sort(); // Tree by type to avoid conflicts on numeric object keys, empty value.

  var map = Array.isArray(key) ? _arrayTreeMap : _objectTreeMap;

  for (var i = 0; i < properties.length; i++) {
    var property = properties[i];
    map = map.get(property);

    if (map === undefined) {
      return;
    }

    var propertyValue = key[property];
    map = map.get(propertyValue);

    if (map === undefined) {
      return;
    }
  }

  var valuePair = map.get('_ekm_value');

  if (!valuePair) {
    return;
  } // If reached, it implies that an object-like key was set with another
  // reference, so delete the reference and replace with the current.


  _map.delete(valuePair[0]);

  valuePair[0] = key;
  map.set('_ekm_value', valuePair);

  _map.set(key, valuePair);

  return valuePair;
}
/**
 * Variant of a Map object which enables lookup by equivalent (deeply equal)
 * object and array keys.
 */


var EquivalentKeyMap =
/*#__PURE__*/
function () {
  /**
   * Constructs a new instance of EquivalentKeyMap.
   *
   * @param {Iterable.<*>} iterable Initial pair of key, value for map.
   */
  function EquivalentKeyMap(iterable) {
    _classCallCheck(this, EquivalentKeyMap);

    this.clear();

    if (iterable instanceof EquivalentKeyMap) {
      // Map#forEach is only means of iterating with support for IE11.
      var iterablePairs = [];
      iterable.forEach(function (value, key) {
        iterablePairs.push([key, value]);
      });
      iterable = iterablePairs;
    }

    if (iterable != null) {
      for (var i = 0; i < iterable.length; i++) {
        this.set(iterable[i][0], iterable[i][1]);
      }
    }
  }
  /**
   * Accessor property returning the number of elements.
   *
   * @return {number} Number of elements.
   */


  _createClass(EquivalentKeyMap, [{
    key: "set",

    /**
     * Add or update an element with a specified key and value.
     *
     * @param {*} key   The key of the element to add.
     * @param {*} value The value of the element to add.
     *
     * @return {EquivalentKeyMap} Map instance.
     */
    value: function set(key, value) {
      // Shortcut non-object-like to set on internal Map.
      if (key === null || _typeof(key) !== 'object') {
        this._map.set(key, value);

        return this;
      } // Sort keys to ensure stable assignment into tree.


      var properties = Object.keys(key).sort();
      var valuePair = [key, value]; // Tree by type to avoid conflicts on numeric object keys, empty value.

      var map = Array.isArray(key) ? this._arrayTreeMap : this._objectTreeMap;

      for (var i = 0; i < properties.length; i++) {
        var property = properties[i];

        if (!map.has(property)) {
          map.set(property, new EquivalentKeyMap());
        }

        map = map.get(property);
        var propertyValue = key[property];

        if (!map.has(propertyValue)) {
          map.set(propertyValue, new EquivalentKeyMap());
        }

        map = map.get(propertyValue);
      } // If an _ekm_value exists, there was already an equivalent key. Before
      // overriding, ensure that the old key reference is removed from map to
      // avoid memory leak of accumulating equivalent keys. This is, in a
      // sense, a poor man's WeakMap, while still enabling iterability.


      var previousValuePair = map.get('_ekm_value');

      if (previousValuePair) {
        this._map.delete(previousValuePair[0]);
      }

      map.set('_ekm_value', valuePair);

      this._map.set(key, valuePair);

      return this;
    }
    /**
     * Returns a specified element.
     *
     * @param {*} key The key of the element to return.
     *
     * @return {?*} The element associated with the specified key or undefined
     *              if the key can't be found.
     */

  }, {
    key: "get",
    value: function get(key) {
      // Shortcut non-object-like to get from internal Map.
      if (key === null || _typeof(key) !== 'object') {
        return this._map.get(key);
      }

      var valuePair = getValuePair(this, key);

      if (valuePair) {
        return valuePair[1];
      }
    }
    /**
     * Returns a boolean indicating whether an element with the specified key
     * exists or not.
     *
     * @param {*} key The key of the element to test for presence.
     *
     * @return {boolean} Whether an element with the specified key exists.
     */

  }, {
    key: "has",
    value: function has(key) {
      if (key === null || _typeof(key) !== 'object') {
        return this._map.has(key);
      } // Test on the _presence_ of the pair, not its value, as even undefined
      // can be a valid member value for a key.


      return getValuePair(this, key) !== undefined;
    }
    /**
     * Removes the specified element.
     *
     * @param {*} key The key of the element to remove.
     *
     * @return {boolean} Returns true if an element existed and has been
     *                   removed, or false if the element does not exist.
     */

  }, {
    key: "delete",
    value: function _delete(key) {
      if (!this.has(key)) {
        return false;
      } // This naive implementation will leave orphaned child trees. A better
      // implementation should traverse and remove orphans.


      this.set(key, undefined);
      return true;
    }
    /**
     * Executes a provided function once per each key/value pair, in insertion
     * order.
     *
     * @param {Function} callback Function to execute for each element.
     * @param {*}        thisArg  Value to use as `this` when executing
     *                            `callback`.
     */

  }, {
    key: "forEach",
    value: function forEach(callback) {
      var _this = this;

      var thisArg = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this;

      this._map.forEach(function (value, key) {
        // Unwrap value from object-like value pair.
        if (key !== null && _typeof(key) === 'object') {
          value = value[1];
        }

        callback.call(thisArg, value, key, _this);
      });
    }
    /**
     * Removes all elements.
     */

  }, {
    key: "clear",
    value: function clear() {
      this._map = new Map();
      this._arrayTreeMap = new Map();
      this._objectTreeMap = new Map();
    }
  }, {
    key: "size",
    get: function get() {
      return this._map.size;
    }
  }]);

  return EquivalentKeyMap;
}();

module.exports = EquivalentKeyMap;


/***/ }),

/***/ 3377:
/***/ (() => {



/***/ }),

/***/ 3440:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  __experimentalBatch: () => (/* binding */ __experimentalBatch),
  __experimentalReceiveCurrentGlobalStylesId: () => (/* binding */ __experimentalReceiveCurrentGlobalStylesId),
  __experimentalReceiveThemeBaseGlobalStyles: () => (/* binding */ __experimentalReceiveThemeBaseGlobalStyles),
  __experimentalReceiveThemeGlobalStyleVariations: () => (/* binding */ __experimentalReceiveThemeGlobalStyleVariations),
  __experimentalSaveSpecifiedEntityEdits: () => (/* binding */ __experimentalSaveSpecifiedEntityEdits),
  __unstableCreateUndoLevel: () => (/* binding */ __unstableCreateUndoLevel),
  addEntities: () => (/* binding */ addEntities),
  deleteEntityRecord: () => (/* binding */ deleteEntityRecord),
  editEntityRecord: () => (/* binding */ editEntityRecord),
  receiveAutosaves: () => (/* binding */ receiveAutosaves),
  receiveCurrentTheme: () => (/* binding */ receiveCurrentTheme),
  receiveCurrentUser: () => (/* binding */ receiveCurrentUser),
  receiveDefaultTemplateId: () => (/* binding */ receiveDefaultTemplateId),
  receiveEmbedPreview: () => (/* binding */ receiveEmbedPreview),
  receiveEntityRecords: () => (/* binding */ receiveEntityRecords),
  receiveNavigationFallbackId: () => (/* binding */ receiveNavigationFallbackId),
  receiveRevisions: () => (/* binding */ receiveRevisions),
  receiveThemeGlobalStyleRevisions: () => (/* binding */ receiveThemeGlobalStyleRevisions),
  receiveThemeSupports: () => (/* binding */ receiveThemeSupports),
  receiveUploadPermissions: () => (/* binding */ receiveUploadPermissions),
  receiveUserPermission: () => (/* binding */ receiveUserPermission),
  receiveUserPermissions: () => (/* binding */ receiveUserPermissions),
  receiveUserQuery: () => (/* binding */ receiveUserQuery),
  redo: () => (/* binding */ redo),
  saveEditedEntityRecord: () => (/* binding */ saveEditedEntityRecord),
  saveEntityRecord: () => (/* binding */ saveEntityRecord),
  undo: () => (/* binding */ undo)
});

// EXTERNAL MODULE: ./node_modules/fast-deep-equal/es6/index.js
var es6 = __webpack_require__(7734);
var es6_default = /*#__PURE__*/__webpack_require__.n(es6);
;// ./node_modules/@wordpress/core-data/node_modules/uuid/dist/esm-browser/native.js
const randomUUID = typeof crypto !== 'undefined' && crypto.randomUUID && crypto.randomUUID.bind(crypto);
/* harmony default export */ const esm_browser_native = ({
  randomUUID
});
;// ./node_modules/@wordpress/core-data/node_modules/uuid/dist/esm-browser/rng.js
// Unique ID creation requires a high quality random # generator. In the browser we therefore
// require the crypto API and do not support built-in fallback to lower quality random number
// generators (like Math.random()).
let getRandomValues;
const rnds8 = new Uint8Array(16);
function rng() {
  // lazy load so that environments that need to polyfill have a chance to do so
  if (!getRandomValues) {
    // getRandomValues needs to be invoked in a context where "this" is a Crypto implementation.
    getRandomValues = typeof crypto !== 'undefined' && crypto.getRandomValues && crypto.getRandomValues.bind(crypto);

    if (!getRandomValues) {
      throw new Error('crypto.getRandomValues() not supported. See https://github.com/uuidjs/uuid#getrandomvalues-not-supported');
    }
  }

  return getRandomValues(rnds8);
}
;// ./node_modules/@wordpress/core-data/node_modules/uuid/dist/esm-browser/stringify.js

/**
 * Convert array of 16 byte values to UUID string format of the form:
 * XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
 */

const byteToHex = [];

for (let i = 0; i < 256; ++i) {
  byteToHex.push((i + 0x100).toString(16).slice(1));
}

function unsafeStringify(arr, offset = 0) {
  // Note: Be careful editing this code!  It's been tuned for performance
  // and works in ways you may not expect. See https://github.com/uuidjs/uuid/pull/434
  return byteToHex[arr[offset + 0]] + byteToHex[arr[offset + 1]] + byteToHex[arr[offset + 2]] + byteToHex[arr[offset + 3]] + '-' + byteToHex[arr[offset + 4]] + byteToHex[arr[offset + 5]] + '-' + byteToHex[arr[offset + 6]] + byteToHex[arr[offset + 7]] + '-' + byteToHex[arr[offset + 8]] + byteToHex[arr[offset + 9]] + '-' + byteToHex[arr[offset + 10]] + byteToHex[arr[offset + 11]] + byteToHex[arr[offset + 12]] + byteToHex[arr[offset + 13]] + byteToHex[arr[offset + 14]] + byteToHex[arr[offset + 15]];
}

function stringify(arr, offset = 0) {
  const uuid = unsafeStringify(arr, offset); // Consistency check for valid UUID.  If this throws, it's likely due to one
  // of the following:
  // - One or more input array values don't map to a hex octet (leading to
  // "undefined" in the uuid)
  // - Invalid input values for the RFC `version` or `variant` fields

  if (!validate(uuid)) {
    throw TypeError('Stringified UUID is invalid');
  }

  return uuid;
}

/* harmony default export */ const esm_browser_stringify = ((/* unused pure expression or super */ null && (stringify)));
;// ./node_modules/@wordpress/core-data/node_modules/uuid/dist/esm-browser/v4.js




function v4(options, buf, offset) {
  if (esm_browser_native.randomUUID && !buf && !options) {
    return esm_browser_native.randomUUID();
  }

  options = options || {};
  const rnds = options.random || (options.rng || rng)(); // Per 4.4, set bits for version and `clock_seq_hi_and_reserved`

  rnds[6] = rnds[6] & 0x0f | 0x40;
  rnds[8] = rnds[8] & 0x3f | 0x80; // Copy bytes to buffer, if provided

  if (buf) {
    offset = offset || 0;

    for (let i = 0; i < 16; ++i) {
      buf[offset + i] = rnds[i];
    }

    return buf;
  }

  return unsafeStringify(rnds);
}

/* harmony default export */ const esm_browser_v4 = (v4);
// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__(1455);
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);
// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__(3832);
// EXTERNAL MODULE: external ["wp","deprecated"]
var external_wp_deprecated_ = __webpack_require__(4040);
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/set-nested-value.js
var set_nested_value = __webpack_require__(5003);
;// ./node_modules/@wordpress/core-data/build-module/utils/get-nested-value.js
function getNestedValue(object, path, defaultValue) {
  if (!object || typeof object !== "object" || typeof path !== "string" && !Array.isArray(path)) {
    return object;
  }
  const normalizedPath = Array.isArray(path) ? path : path.split(".");
  let value = object;
  normalizedPath.forEach((fieldName) => {
    value = value?.[fieldName];
  });
  return value !== void 0 ? value : defaultValue;
}


;// ./node_modules/@wordpress/core-data/build-module/queried-data/actions.js
function receiveItems(items, edits, meta) {
  return {
    type: "RECEIVE_ITEMS",
    items: Array.isArray(items) ? items : [items],
    persistedEdits: edits,
    meta
  };
}
function removeItems(kind, name, records, invalidateCache = false) {
  return {
    type: "REMOVE_ITEMS",
    itemIds: Array.isArray(records) ? records : [records],
    kind,
    name,
    invalidateCache
  };
}
function receiveQueriedItems(items, query = {}, edits, meta) {
  return {
    ...receiveItems(items, edits, meta),
    query
  };
}


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/entities.js + 2 modules
var entities = __webpack_require__(5914);
;// ./node_modules/@wordpress/core-data/build-module/batch/default-processor.js

let maxItems = null;
function chunk(arr, chunkSize) {
  const tmp = [...arr];
  const cache = [];
  while (tmp.length) {
    cache.push(tmp.splice(0, chunkSize));
  }
  return cache;
}
async function defaultProcessor(requests) {
  if (maxItems === null) {
    const preflightResponse = await external_wp_apiFetch_default()({
      path: "/batch/v1",
      method: "OPTIONS"
    });
    maxItems = preflightResponse.endpoints[0].args.requests.maxItems;
  }
  const results = [];
  for (const batchRequests of chunk(requests, maxItems)) {
    const batchResponse = await external_wp_apiFetch_default()({
      path: "/batch/v1",
      method: "POST",
      data: {
        validation: "require-all-validate",
        requests: batchRequests.map((request) => ({
          path: request.path,
          body: request.data,
          // Rename 'data' to 'body'.
          method: request.method,
          headers: request.headers
        }))
      }
    });
    let batchResults;
    if (batchResponse.failed) {
      batchResults = batchResponse.responses.map((response) => ({
        error: response?.body
      }));
    } else {
      batchResults = batchResponse.responses.map((response) => {
        const result = {};
        if (response.status >= 200 && response.status < 300) {
          result.output = response.body;
        } else {
          result.error = response.body;
        }
        return result;
      });
    }
    results.push(...batchResults);
  }
  return results;
}


;// ./node_modules/@wordpress/core-data/build-module/batch/create-batch.js

function createBatch(processor = defaultProcessor) {
  let lastId = 0;
  let queue = [];
  const pending = new ObservableSet();
  return {
    /**
     * Adds an input to the batch and returns a promise that is resolved or
     * rejected when the input is processed by `batch.run()`.
     *
     * You may also pass a thunk which allows inputs to be added
     * asynchronously.
     *
     * ```
     * // Both are allowed:
     * batch.add( { path: '/v1/books', ... } );
     * batch.add( ( add ) => add( { path: '/v1/books', ... } ) );
     * ```
     *
     * If a thunk is passed, `batch.run()` will pause until either:
     *
     * - The thunk calls its `add` argument, or;
     * - The thunk returns a promise and that promise resolves, or;
     * - The thunk returns a non-promise.
     *
     * @param {any|Function} inputOrThunk Input to add or thunk to execute.
     *
     * @return {Promise|any} If given an input, returns a promise that
     *                       is resolved or rejected when the batch is
     *                       processed. If given a thunk, returns the return
     *                       value of that thunk.
     */
    add(inputOrThunk) {
      const id = ++lastId;
      pending.add(id);
      const add = (input) => new Promise((resolve, reject) => {
        queue.push({
          input,
          resolve,
          reject
        });
        pending.delete(id);
      });
      if (typeof inputOrThunk === "function") {
        return Promise.resolve(inputOrThunk(add)).finally(() => {
          pending.delete(id);
        });
      }
      return add(inputOrThunk);
    },
    /**
     * Runs the batch. This calls `batchProcessor` and resolves or rejects
     * all promises returned by `add()`.
     *
     * @return {Promise<boolean>} A promise that resolves to a boolean that is true
     *                   if the processor returned no errors.
     */
    async run() {
      if (pending.size) {
        await new Promise((resolve) => {
          const unsubscribe = pending.subscribe(() => {
            if (!pending.size) {
              unsubscribe();
              resolve(void 0);
            }
          });
        });
      }
      let results;
      try {
        results = await processor(
          queue.map(({ input }) => input)
        );
        if (results.length !== queue.length) {
          throw new Error(
            "run: Array returned by processor must be same size as input array."
          );
        }
      } catch (error) {
        for (const { reject } of queue) {
          reject(error);
        }
        throw error;
      }
      let isSuccess = true;
      results.forEach((result, key) => {
        const queueItem = queue[key];
        if (result?.error) {
          queueItem?.reject(result.error);
          isSuccess = false;
        } else {
          queueItem?.resolve(result?.output ?? result);
        }
      });
      queue = [];
      return isSuccess;
    }
  };
}
class ObservableSet {
  constructor(...args) {
    this.set = new Set(...args);
    this.subscribers = /* @__PURE__ */ new Set();
  }
  get size() {
    return this.set.size;
  }
  add(value) {
    this.set.add(value);
    this.subscribers.forEach((subscriber) => subscriber());
    return this;
  }
  delete(value) {
    const isSuccess = this.set.delete(value);
    this.subscribers.forEach((subscriber) => subscriber());
    return isSuccess;
  }
  subscribe(subscriber) {
    this.subscribers.add(subscriber);
    return () => {
      this.subscribers.delete(subscriber);
    };
  }
}


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/name.js
var build_module_name = __webpack_require__(2278);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/log-entity-deprecation.js
var log_entity_deprecation = __webpack_require__(9410);
;// ./node_modules/@wordpress/core-data/build-module/actions.js












function receiveUserQuery(queryID, users) {
  return {
    type: "RECEIVE_USER_QUERY",
    users: Array.isArray(users) ? users : [users],
    queryID
  };
}
function receiveCurrentUser(currentUser) {
  return {
    type: "RECEIVE_CURRENT_USER",
    currentUser
  };
}
function addEntities(entities) {
  return {
    type: "ADD_ENTITIES",
    entities
  };
}
function receiveEntityRecords(kind, name, records, query, invalidateCache = false, edits, meta) {
  if (kind === "postType") {
    records = (Array.isArray(records) ? records : [records]).map(
      (record) => record.status === "auto-draft" ? { ...record, title: "" } : record
    );
  }
  let action;
  if (query) {
    action = receiveQueriedItems(records, query, edits, meta);
  } else {
    action = receiveItems(records, edits, meta);
  }
  return {
    ...action,
    kind,
    name,
    invalidateCache
  };
}
function receiveCurrentTheme(currentTheme) {
  return {
    type: "RECEIVE_CURRENT_THEME",
    currentTheme
  };
}
function __experimentalReceiveCurrentGlobalStylesId(currentGlobalStylesId) {
  return {
    type: "RECEIVE_CURRENT_GLOBAL_STYLES_ID",
    id: currentGlobalStylesId
  };
}
function __experimentalReceiveThemeBaseGlobalStyles(stylesheet, globalStyles) {
  return {
    type: "RECEIVE_THEME_GLOBAL_STYLES",
    stylesheet,
    globalStyles
  };
}
function __experimentalReceiveThemeGlobalStyleVariations(stylesheet, variations) {
  return {
    type: "RECEIVE_THEME_GLOBAL_STYLE_VARIATIONS",
    stylesheet,
    variations
  };
}
function receiveThemeSupports() {
  external_wp_deprecated_default()("wp.data.dispatch( 'core' ).receiveThemeSupports", {
    since: "5.9"
  });
  return {
    type: "DO_NOTHING"
  };
}
function receiveThemeGlobalStyleRevisions(currentId, revisions) {
  external_wp_deprecated_default()(
    "wp.data.dispatch( 'core' ).receiveThemeGlobalStyleRevisions()",
    {
      since: "6.5.0",
      alternative: "wp.data.dispatch( 'core' ).receiveRevisions"
    }
  );
  return {
    type: "RECEIVE_THEME_GLOBAL_STYLE_REVISIONS",
    currentId,
    revisions
  };
}
function receiveEmbedPreview(url, preview) {
  return {
    type: "RECEIVE_EMBED_PREVIEW",
    url,
    preview
  };
}
const deleteEntityRecord = (kind, name, recordId, query, { __unstableFetch = (external_wp_apiFetch_default()), throwOnError = false } = {}) => async ({ dispatch, resolveSelect }) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "deleteEntityRecord");
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.kind === kind && config.name === name
  );
  let error;
  let deletedRecord = false;
  if (!entityConfig) {
    return;
  }
  const lock = await dispatch.__unstableAcquireStoreLock(
    build_module_name/* STORE_NAME */.E,
    ["entities", "records", kind, name, recordId],
    { exclusive: true }
  );
  try {
    dispatch({
      type: "DELETE_ENTITY_RECORD_START",
      kind,
      name,
      recordId
    });
    let hasError = false;
    try {
      let path = `${entityConfig.baseURL}/${recordId}`;
      if (query) {
        path = (0,external_wp_url_.addQueryArgs)(path, query);
      }
      deletedRecord = await __unstableFetch({
        path,
        method: "DELETE"
      });
      await dispatch(removeItems(kind, name, recordId, true));
    } catch (_error) {
      hasError = true;
      error = _error;
    }
    dispatch({
      type: "DELETE_ENTITY_RECORD_FINISH",
      kind,
      name,
      recordId,
      error
    });
    if (hasError && throwOnError) {
      throw error;
    }
    return deletedRecord;
  } finally {
    dispatch.__unstableReleaseStoreLock(lock);
  }
};
const editEntityRecord = (kind, name, recordId, edits, options = {}) => ({ select, dispatch }) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "editEntityRecord");
  const entityConfig = select.getEntityConfig(kind, name);
  if (!entityConfig) {
    throw new Error(
      `The entity being edited (${kind}, ${name}) does not have a loaded config.`
    );
  }
  const { mergedEdits = {} } = entityConfig;
  const record = select.getRawEntityRecord(kind, name, recordId);
  const editedRecord = select.getEditedEntityRecord(
    kind,
    name,
    recordId
  );
  const edit = {
    kind,
    name,
    recordId,
    // Clear edits when they are equal to their persisted counterparts
    // so that the property is not considered dirty.
    edits: Object.keys(edits).reduce((acc, key) => {
      const recordValue = record[key];
      const editedRecordValue = editedRecord[key];
      const value = mergedEdits[key] ? { ...editedRecordValue, ...edits[key] } : edits[key];
      acc[key] = es6_default()(recordValue, value) ? void 0 : value;
      return acc;
    }, {})
  };
  if (window.__experimentalEnableSync && entityConfig.syncConfig) {
    if (false) {}
  }
  if (!options.undoIgnore) {
    select.getUndoManager().addRecord(
      [
        {
          id: { kind, name, recordId },
          changes: Object.keys(edits).reduce((acc, key) => {
            acc[key] = {
              from: editedRecord[key],
              to: edits[key]
            };
            return acc;
          }, {})
        }
      ],
      options.isCached
    );
  }
  dispatch({
    type: "EDIT_ENTITY_RECORD",
    ...edit
  });
};
const undo = () => ({ select, dispatch }) => {
  const undoRecord = select.getUndoManager().undo();
  if (!undoRecord) {
    return;
  }
  dispatch({
    type: "UNDO",
    record: undoRecord
  });
};
const redo = () => ({ select, dispatch }) => {
  const redoRecord = select.getUndoManager().redo();
  if (!redoRecord) {
    return;
  }
  dispatch({
    type: "REDO",
    record: redoRecord
  });
};
const __unstableCreateUndoLevel = () => ({ select }) => {
  select.getUndoManager().addRecord();
};
const saveEntityRecord = (kind, name, record, {
  isAutosave = false,
  __unstableFetch = (external_wp_apiFetch_default()),
  throwOnError = false
} = {}) => async ({ select, resolveSelect, dispatch }) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "saveEntityRecord");
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.kind === kind && config.name === name
  );
  if (!entityConfig) {
    return;
  }
  const entityIdKey = entityConfig.key ?? entities/* DEFAULT_ENTITY_KEY */.C_;
  const recordId = record[entityIdKey];
  const isNewRecord = !!entityIdKey && !recordId;
  const lock = await dispatch.__unstableAcquireStoreLock(
    build_module_name/* STORE_NAME */.E,
    ["entities", "records", kind, name, recordId || esm_browser_v4()],
    { exclusive: true }
  );
  try {
    for (const [key, value] of Object.entries(record)) {
      if (typeof value === "function") {
        const evaluatedValue = value(
          select.getEditedEntityRecord(kind, name, recordId)
        );
        dispatch.editEntityRecord(
          kind,
          name,
          recordId,
          {
            [key]: evaluatedValue
          },
          { undoIgnore: true }
        );
        record[key] = evaluatedValue;
      }
    }
    dispatch({
      type: "SAVE_ENTITY_RECORD_START",
      kind,
      name,
      recordId,
      isAutosave
    });
    let updatedRecord;
    let error;
    let hasError = false;
    try {
      const path = `${entityConfig.baseURL}${recordId ? "/" + recordId : ""}`;
      const persistedRecord = !isNewRecord ? select.getRawEntityRecord(kind, name, recordId) : {};
      if (isAutosave) {
        const currentUser = select.getCurrentUser();
        const currentUserId = currentUser ? currentUser.id : void 0;
        const autosavePost = await resolveSelect.getAutosave(
          persistedRecord.type,
          persistedRecord.id,
          currentUserId
        );
        let data = {
          ...persistedRecord,
          ...autosavePost,
          ...record
        };
        data = Object.keys(data).reduce(
          (acc, key) => {
            if ([
              "title",
              "excerpt",
              "content",
              "meta"
            ].includes(key)) {
              acc[key] = data[key];
            }
            return acc;
          },
          {
            // Do not update the `status` if we have edited it when auto saving.
            // It's very important to let the user explicitly save this change,
            // because it can lead to unexpected results. An example would be to
            // have a draft post and change the status to publish.
            status: data.status === "auto-draft" ? "draft" : void 0
          }
        );
        updatedRecord = await __unstableFetch({
          path: `${path}/autosaves`,
          method: "POST",
          data
        });
        if (persistedRecord.id === updatedRecord.id) {
          let newRecord = {
            ...persistedRecord,
            ...data,
            ...updatedRecord
          };
          newRecord = Object.keys(newRecord).reduce(
            (acc, key) => {
              if (["title", "excerpt", "content"].includes(
                key
              )) {
                acc[key] = newRecord[key];
              } else if (key === "status") {
                acc[key] = persistedRecord.status === "auto-draft" && newRecord.status === "draft" ? newRecord.status : persistedRecord.status;
              } else {
                acc[key] = persistedRecord[key];
              }
              return acc;
            },
            {}
          );
          dispatch.receiveEntityRecords(
            kind,
            name,
            newRecord,
            void 0,
            true
          );
        } else {
          dispatch.receiveAutosaves(
            persistedRecord.id,
            updatedRecord
          );
        }
      } else {
        let edits = record;
        if (entityConfig.__unstablePrePersist) {
          edits = {
            ...edits,
            ...entityConfig.__unstablePrePersist(
              persistedRecord,
              edits
            )
          };
        }
        updatedRecord = await __unstableFetch({
          path,
          method: recordId ? "PUT" : "POST",
          data: edits
        });
        dispatch.receiveEntityRecords(
          kind,
          name,
          updatedRecord,
          void 0,
          true,
          edits
        );
      }
    } catch (_error) {
      hasError = true;
      error = _error;
    }
    dispatch({
      type: "SAVE_ENTITY_RECORD_FINISH",
      kind,
      name,
      recordId,
      error,
      isAutosave
    });
    if (hasError && throwOnError) {
      throw error;
    }
    return updatedRecord;
  } finally {
    dispatch.__unstableReleaseStoreLock(lock);
  }
};
const __experimentalBatch = (requests) => async ({ dispatch }) => {
  const batch = createBatch();
  const api = {
    saveEntityRecord(kind, name, record, options) {
      return batch.add(
        (add) => dispatch.saveEntityRecord(kind, name, record, {
          ...options,
          __unstableFetch: add
        })
      );
    },
    saveEditedEntityRecord(kind, name, recordId, options) {
      return batch.add(
        (add) => dispatch.saveEditedEntityRecord(kind, name, recordId, {
          ...options,
          __unstableFetch: add
        })
      );
    },
    deleteEntityRecord(kind, name, recordId, query, options) {
      return batch.add(
        (add) => dispatch.deleteEntityRecord(kind, name, recordId, query, {
          ...options,
          __unstableFetch: add
        })
      );
    }
  };
  const resultPromises = requests.map((request) => request(api));
  const [, ...results] = await Promise.all([
    batch.run(),
    ...resultPromises
  ]);
  return results;
};
const saveEditedEntityRecord = (kind, name, recordId, options) => async ({ select, dispatch, resolveSelect }) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "saveEditedEntityRecord");
  if (!select.hasEditsForEntityRecord(kind, name, recordId)) {
    return;
  }
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.kind === kind && config.name === name
  );
  if (!entityConfig) {
    return;
  }
  const entityIdKey = entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_;
  const edits = select.getEntityRecordNonTransientEdits(
    kind,
    name,
    recordId
  );
  const record = { [entityIdKey]: recordId, ...edits };
  return await dispatch.saveEntityRecord(kind, name, record, options);
};
const __experimentalSaveSpecifiedEntityEdits = (kind, name, recordId, itemsToSave, options) => async ({ select, dispatch, resolveSelect }) => {
  (0,log_entity_deprecation/* default */.A)(
    kind,
    name,
    "__experimentalSaveSpecifiedEntityEdits"
  );
  if (!select.hasEditsForEntityRecord(kind, name, recordId)) {
    return;
  }
  const edits = select.getEntityRecordNonTransientEdits(
    kind,
    name,
    recordId
  );
  const editsToSave = {};
  for (const item of itemsToSave) {
    (0,set_nested_value/* default */.A)(editsToSave, item, getNestedValue(edits, item));
  }
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.kind === kind && config.name === name
  );
  const entityIdKey = entityConfig?.key || entities/* DEFAULT_ENTITY_KEY */.C_;
  if (recordId) {
    editsToSave[entityIdKey] = recordId;
  }
  return await dispatch.saveEntityRecord(
    kind,
    name,
    editsToSave,
    options
  );
};
function receiveUploadPermissions(hasUploadPermissions) {
  external_wp_deprecated_default()("wp.data.dispatch( 'core' ).receiveUploadPermissions", {
    since: "5.9",
    alternative: "receiveUserPermission"
  });
  return receiveUserPermission("create/media", hasUploadPermissions);
}
function receiveUserPermission(key, isAllowed) {
  return {
    type: "RECEIVE_USER_PERMISSION",
    key,
    isAllowed
  };
}
function receiveUserPermissions(permissions) {
  return {
    type: "RECEIVE_USER_PERMISSIONS",
    permissions
  };
}
function receiveAutosaves(postId, autosaves) {
  return {
    type: "RECEIVE_AUTOSAVES",
    postId,
    autosaves: Array.isArray(autosaves) ? autosaves : [autosaves]
  };
}
function receiveNavigationFallbackId(fallbackId) {
  return {
    type: "RECEIVE_NAVIGATION_FALLBACK_ID",
    fallbackId
  };
}
function receiveDefaultTemplateId(query, templateId) {
  return {
    type: "RECEIVE_DEFAULT_TEMPLATE",
    query,
    templateId
  };
}
const receiveRevisions = (kind, name, recordKey, records, query, invalidateCache = false, meta) => async ({ dispatch, resolveSelect }) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "receiveRevisions");
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.kind === kind && config.name === name
  );
  const key = entityConfig && entityConfig?.revisionKey ? entityConfig.revisionKey : entities/* DEFAULT_ENTITY_KEY */.C_;
  dispatch({
    type: "RECEIVE_ITEM_REVISIONS",
    key,
    items: Array.isArray(records) ? records : [records],
    recordKey,
    meta,
    query,
    kind,
    name,
    invalidateCache
  });
};



/***/ }),

/***/ 3832:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["url"];

/***/ }),

/***/ 4027:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  A: () => (/* binding */ get_query_parts_default)
});

// UNUSED EXPORTS: getQueryParts

// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__(3832);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/get-normalized-comma-separable.js
var get_normalized_comma_separable = __webpack_require__(533);
;// ./node_modules/@wordpress/core-data/build-module/utils/with-weak-map-cache.js
function withWeakMapCache(fn) {
  const cache = /* @__PURE__ */ new WeakMap();
  return (key) => {
    let value;
    if (cache.has(key)) {
      value = cache.get(key);
    } else {
      value = fn(key);
      if (key !== null && typeof key === "object") {
        cache.set(key, value);
      }
    }
    return value;
  };
}
var with_weak_map_cache_default = withWeakMapCache;


;// ./node_modules/@wordpress/core-data/build-module/queried-data/get-query-parts.js


function getQueryParts(query) {
  const parts = {
    stableKey: "",
    page: 1,
    perPage: 10,
    fields: null,
    include: null,
    context: "default"
  };
  const keys = Object.keys(query).sort();
  for (let i = 0; i < keys.length; i++) {
    const key = keys[i];
    let value = query[key];
    switch (key) {
      case "page":
        parts[key] = Number(value);
        break;
      case "per_page":
        parts.perPage = Number(value);
        break;
      case "context":
        parts.context = value;
        break;
      default:
        if (key === "_fields") {
          parts.fields = (0,get_normalized_comma_separable/* default */.A)(value) ?? [];
          value = parts.fields.join();
        }
        if (key === "include") {
          if (typeof value === "number") {
            value = value.toString();
          }
          parts.include = ((0,get_normalized_comma_separable/* default */.A)(value) ?? []).map(Number);
          value = parts.include.join();
        }
        parts.stableKey += (parts.stableKey ? "&" : "") + (0,external_wp_url_.addQueryArgs)("", { [key]: value }).slice(1);
    }
  }
  return parts;
}
var get_query_parts_default = with_weak_map_cache_default(getQueryParts);



/***/ }),

/***/ 4040:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["deprecated"];

/***/ }),

/***/ 4460:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  A: () => (/* binding */ EntityProvider)
});

;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__(6087);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/entity-context.js
var entity_context = __webpack_require__(8843);
;// ./node_modules/@wordpress/core-data/build-module/entity-provider.js



function EntityProvider({ kind, type: name, id, children }) {
  const parent = (0,external_wp_element_.useContext)(entity_context/* EntityContext */.D);
  const childContext = (0,external_wp_element_.useMemo)(
    () => ({
      ...parent,
      [kind]: {
        ...parent?.[kind],
        [name]: id
      }
    }),
    [parent, kind, name, id]
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(entity_context/* EntityContext */.D.Provider, { value: childContext, children });
}



/***/ }),

/***/ 4565:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EntityProvider: () => (/* reexport safe */ _entity_provider__WEBPACK_IMPORTED_MODULE_17__.A),
/* harmony export */   __experimentalFetchLinkSuggestions: () => (/* reexport safe */ _fetch__WEBPACK_IMPORTED_MODULE_14__.Y3),
/* harmony export */   __experimentalFetchUrlData: () => (/* reexport safe */ _fetch__WEBPACK_IMPORTED_MODULE_14__.gr),
/* harmony export */   __experimentalUseEntityRecord: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.qh),
/* harmony export */   __experimentalUseEntityRecords: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.bM),
/* harmony export */   __experimentalUseResourcePermissions: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__._),
/* harmony export */   fetchBlockPatterns: () => (/* reexport safe */ _fetch__WEBPACK_IMPORTED_MODULE_14__.l$),
/* harmony export */   privateApis: () => (/* reexport safe */ _private_apis__WEBPACK_IMPORTED_MODULE_16__.j),
/* harmony export */   store: () => (/* binding */ store),
/* harmony export */   useEntityBlockEditor: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.hg),
/* harmony export */   useEntityId: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.mV),
/* harmony export */   useEntityProp: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.S$),
/* harmony export */   useEntityRecord: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.MA),
/* harmony export */   useEntityRecords: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.$u),
/* harmony export */   useResourcePermissions: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.qs)
/* harmony export */ });
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(7143);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _reducer__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(5469);
/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(8368);
/* harmony import */ var _private_selectors__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(8741);
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(3440);
/* harmony import */ var _private_actions__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(9424);
/* harmony import */ var _resolvers__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(6384);
/* harmony import */ var _locks_actions__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(2239);
/* harmony import */ var _entities__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(5914);
/* harmony import */ var _name__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(2278);
/* harmony import */ var _lock_unlock__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(6378);
/* harmony import */ var _dynamic_entities__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(8582);
/* harmony import */ var _utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(9410);
/* harmony import */ var _entity_provider__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(4460);
/* harmony import */ var _entity_types__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(3377);
/* harmony import */ var _entity_types__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_entity_types__WEBPACK_IMPORTED_MODULE_13__);
/* harmony reexport (unknown) */ var __WEBPACK_REEXPORT_OBJECT__ = {};
/* harmony reexport (unknown) */ for(const __WEBPACK_IMPORT_KEY__ in _entity_types__WEBPACK_IMPORTED_MODULE_13__) if(["default","EntityProvider","store"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) __WEBPACK_REEXPORT_OBJECT__[__WEBPACK_IMPORT_KEY__] = () => _entity_types__WEBPACK_IMPORTED_MODULE_13__[__WEBPACK_IMPORT_KEY__]
/* harmony reexport (unknown) */ __webpack_require__.d(__webpack_exports__, __WEBPACK_REEXPORT_OBJECT__);
/* harmony import */ var _fetch__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(7006);
/* harmony import */ var _hooks__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(5891);
/* harmony import */ var _private_apis__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(7826);













const entitiesConfig = [
  ..._entities__WEBPACK_IMPORTED_MODULE_1__/* .rootEntitiesConfig */ .Mr,
  ..._entities__WEBPACK_IMPORTED_MODULE_1__/* .additionalEntityConfigLoaders */ .L2.filter((config) => !!config.name)
];
const entitySelectors = entitiesConfig.reduce((result, entity) => {
  const { kind, name, plural } = entity;
  const getEntityRecordMethodName = (0,_entities__WEBPACK_IMPORTED_MODULE_1__/* .getMethodName */ .zD)(kind, name);
  result[getEntityRecordMethodName] = (state, key, query) => {
    (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, name, getEntityRecordMethodName, {
      isShorthandSelector: true,
      alternativeFunctionName: "getEntityRecord"
    });
    return _selectors__WEBPACK_IMPORTED_MODULE_3__.getEntityRecord(state, kind, name, key, query);
  };
  if (plural) {
    const getEntityRecordsMethodName = (0,_entities__WEBPACK_IMPORTED_MODULE_1__/* .getMethodName */ .zD)(kind, plural, "get");
    result[getEntityRecordsMethodName] = (state, query) => {
      (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, name, getEntityRecordsMethodName, {
        isShorthandSelector: true,
        alternativeFunctionName: "getEntityRecords"
      });
      return _selectors__WEBPACK_IMPORTED_MODULE_3__.getEntityRecords(state, kind, name, query);
    };
  }
  return result;
}, {});
const entityResolvers = entitiesConfig.reduce((result, entity) => {
  const { kind, name, plural } = entity;
  const getEntityRecordMethodName = (0,_entities__WEBPACK_IMPORTED_MODULE_1__/* .getMethodName */ .zD)(kind, name);
  result[getEntityRecordMethodName] = (key, query) => {
    (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, name, getEntityRecordMethodName, {
      isShorthandSelector: true,
      alternativeFunctionName: "getEntityRecord"
    });
    return _resolvers__WEBPACK_IMPORTED_MODULE_4__.getEntityRecord(kind, name, key, query);
  };
  if (plural) {
    const getEntityRecordsMethodName = (0,_entities__WEBPACK_IMPORTED_MODULE_1__/* .getMethodName */ .zD)(kind, plural, "get");
    result[getEntityRecordsMethodName] = (...args) => {
      (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, plural, getEntityRecordsMethodName, {
        isShorthandSelector: true,
        alternativeFunctionName: "getEntityRecords"
      });
      return _resolvers__WEBPACK_IMPORTED_MODULE_4__.getEntityRecords(kind, name, ...args);
    };
    result[getEntityRecordsMethodName].shouldInvalidate = (action) => _resolvers__WEBPACK_IMPORTED_MODULE_4__.getEntityRecords.shouldInvalidate(action, kind, name);
  }
  return result;
}, {});
const entityActions = entitiesConfig.reduce((result, entity) => {
  const { kind, name } = entity;
  const saveEntityRecordMethodName = (0,_entities__WEBPACK_IMPORTED_MODULE_1__/* .getMethodName */ .zD)(kind, name, "save");
  result[saveEntityRecordMethodName] = (record, options) => {
    (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, name, saveEntityRecordMethodName, {
      isShorthandSelector: true,
      alternativeFunctionName: "saveEntityRecord"
    });
    return _actions__WEBPACK_IMPORTED_MODULE_5__.saveEntityRecord(kind, name, record, options);
  };
  const deleteEntityRecordMethodName = (0,_entities__WEBPACK_IMPORTED_MODULE_1__/* .getMethodName */ .zD)(kind, name, "delete");
  result[deleteEntityRecordMethodName] = (key, query, options) => {
    (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, name, deleteEntityRecordMethodName, {
      isShorthandSelector: true,
      alternativeFunctionName: "deleteEntityRecord"
    });
    return _actions__WEBPACK_IMPORTED_MODULE_5__.deleteEntityRecord(kind, name, key, query, options);
  };
  return result;
}, {});
const storeConfig = () => ({
  reducer: _reducer__WEBPACK_IMPORTED_MODULE_6__/* ["default"] */ .Ay,
  actions: {
    ..._dynamic_entities__WEBPACK_IMPORTED_MODULE_7__/* .dynamicActions */ .B,
    ..._actions__WEBPACK_IMPORTED_MODULE_5__,
    ...entityActions,
    ...(0,_locks_actions__WEBPACK_IMPORTED_MODULE_8__/* ["default"] */ .A)()
  },
  selectors: {
    ..._dynamic_entities__WEBPACK_IMPORTED_MODULE_7__/* .dynamicSelectors */ .A,
    ..._selectors__WEBPACK_IMPORTED_MODULE_3__,
    ...entitySelectors
  },
  resolvers: { ..._resolvers__WEBPACK_IMPORTED_MODULE_4__, ...entityResolvers }
});
const store = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createReduxStore)(_name__WEBPACK_IMPORTED_MODULE_9__/* .STORE_NAME */ .E, storeConfig());
(0,_lock_unlock__WEBPACK_IMPORTED_MODULE_10__/* .unlock */ .T)(store).registerPrivateSelectors(_private_selectors__WEBPACK_IMPORTED_MODULE_11__);
(0,_lock_unlock__WEBPACK_IMPORTED_MODULE_10__/* .unlock */ .T)(store).registerPrivateActions(_private_actions__WEBPACK_IMPORTED_MODULE_12__);
(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.register)(store);









/***/ }),

/***/ 4997:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["blocks"];

/***/ }),

/***/ 5003:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ setNestedValue)
/* harmony export */ });
function setNestedValue(object, path, value) {
  if (!object || typeof object !== "object") {
    return object;
  }
  const normalizedPath = Array.isArray(path) ? path : path.split(".");
  normalizedPath.reduce((acc, key, idx) => {
    if (acc[key] === void 0) {
      if (Number.isInteger(normalizedPath[idx + 1])) {
        acc[key] = [];
      } else {
        acc[key] = {};
      }
    }
    if (idx === normalizedPath.length - 1) {
      acc[key] = value;
    }
    return acc[key];
  }, object);
  return object;
}



/***/ }),

/***/ 5101:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Z: () => (/* binding */ RECEIVE_INTERMEDIATE_RESULTS)
/* harmony export */ });
const RECEIVE_INTERMEDIATE_RESULTS = Symbol(
  "RECEIVE_INTERMEDIATE_RESULTS"
);



/***/ }),

/***/ 5469:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  Ay: () => (/* binding */ reducer_reducer_default)
});

// UNUSED EXPORTS: autosaves, blockPatternCategories, blockPatterns, currentGlobalStylesId, currentTheme, currentUser, defaultTemplates, editsReference, embedPreviews, entities, entitiesConfig, navigationFallbackId, registeredPostMeta, themeBaseGlobalStyles, themeGlobalStyleRevisions, themeGlobalStyleVariations, undoManager, userPatternCategories, userPermissions, users

// EXTERNAL MODULE: ./node_modules/fast-deep-equal/es6/index.js
var es6 = __webpack_require__(7734);
var es6_default = /*#__PURE__*/__webpack_require__.n(es6);
;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__(7143);
;// external ["wp","isShallowEqual"]
const external_wp_isShallowEqual_namespaceObject = window["wp"]["isShallowEqual"];
var external_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_wp_isShallowEqual_namespaceObject);
;// ./node_modules/@wordpress/undo-manager/build-module/index.js

function mergeHistoryChanges(changes1, changes2) {
  const newChanges = { ...changes1 };
  Object.entries(changes2).forEach(([key, value]) => {
    if (newChanges[key]) {
      newChanges[key] = { ...newChanges[key], to: value.to };
    } else {
      newChanges[key] = value;
    }
  });
  return newChanges;
}
const addHistoryChangesIntoRecord = (record, changes) => {
  const existingChangesIndex = record?.findIndex(
    ({ id: recordIdentifier }) => {
      return typeof recordIdentifier === "string" ? recordIdentifier === changes.id : external_wp_isShallowEqual_default()(recordIdentifier, changes.id);
    }
  );
  const nextRecord = [...record];
  if (existingChangesIndex !== -1) {
    nextRecord[existingChangesIndex] = {
      id: changes.id,
      changes: mergeHistoryChanges(
        nextRecord[existingChangesIndex].changes,
        changes.changes
      )
    };
  } else {
    nextRecord.push(changes);
  }
  return nextRecord;
};
function createUndoManager() {
  let history = [];
  let stagedRecord = [];
  let offset = 0;
  const dropPendingRedos = () => {
    history = history.slice(0, offset || void 0);
    offset = 0;
  };
  const appendStagedRecordToLatestHistoryRecord = () => {
    const index = history.length === 0 ? 0 : history.length - 1;
    let latestRecord = history[index] ?? [];
    stagedRecord.forEach((changes) => {
      latestRecord = addHistoryChangesIntoRecord(latestRecord, changes);
    });
    stagedRecord = [];
    history[index] = latestRecord;
  };
  const isRecordEmpty = (record) => {
    const filteredRecord = record.filter(({ changes }) => {
      return Object.values(changes).some(
        ({ from, to }) => typeof from !== "function" && typeof to !== "function" && !external_wp_isShallowEqual_default()(from, to)
      );
    });
    return !filteredRecord.length;
  };
  return {
    addRecord(record, isStaged = false) {
      const isEmpty = !record || isRecordEmpty(record);
      if (isStaged) {
        if (isEmpty) {
          return;
        }
        record.forEach((changes) => {
          stagedRecord = addHistoryChangesIntoRecord(
            stagedRecord,
            changes
          );
        });
      } else {
        dropPendingRedos();
        if (stagedRecord.length) {
          appendStagedRecordToLatestHistoryRecord();
        }
        if (isEmpty) {
          return;
        }
        history.push(record);
      }
    },
    undo() {
      if (stagedRecord.length) {
        dropPendingRedos();
        appendStagedRecordToLatestHistoryRecord();
      }
      const undoRecord = history[history.length - 1 + offset];
      if (!undoRecord) {
        return;
      }
      offset -= 1;
      return undoRecord;
    },
    redo() {
      const redoRecord = history[history.length + offset];
      if (!redoRecord) {
        return;
      }
      offset += 1;
      return redoRecord;
    },
    hasUndo() {
      return !!history[history.length - 1 + offset];
    },
    hasRedo() {
      return !!history[history.length + offset];
    }
  };
}


;// ./node_modules/@wordpress/core-data/build-module/utils/if-matching-action.js
const ifMatchingAction = (isMatch) => (reducer) => (state, action) => {
  if (state === void 0 || isMatch(action)) {
    return reducer(state, action);
  }
  return state;
};
var if_matching_action_default = ifMatchingAction;


;// ./node_modules/@wordpress/core-data/build-module/utils/replace-action.js
const replaceAction = (replacer) => (reducer) => (state, action) => {
  return reducer(state, replacer(action));
};
var replace_action_default = replaceAction;


;// ./node_modules/@wordpress/core-data/build-module/utils/conservative-map-item.js

function conservativeMapItem(item, nextItem) {
  if (!item) {
    return nextItem;
  }
  let hasChanges = false;
  const result = {};
  for (const key in nextItem) {
    if (es6_default()(item[key], nextItem[key])) {
      result[key] = item[key];
    } else {
      hasChanges = true;
      result[key] = nextItem[key];
    }
  }
  if (!hasChanges) {
    return item;
  }
  for (const key in item) {
    if (!result.hasOwnProperty(key)) {
      result[key] = item[key];
    }
  }
  return result;
}


;// ./node_modules/@wordpress/core-data/build-module/utils/on-sub-key.js
const onSubKey = (actionProperty) => (reducer) => (state = {}, action) => {
  const key = action[actionProperty];
  if (key === void 0) {
    return state;
  }
  const nextKeyState = reducer(state[key], action);
  if (nextKeyState === state[key]) {
    return state;
  }
  return {
    ...state,
    [key]: nextKeyState
  };
};
var on_sub_key_default = onSubKey;


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/entities.js + 2 modules
var entities = __webpack_require__(5914);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/queried-data/get-query-parts.js + 1 modules
var get_query_parts = __webpack_require__(4027);
;// ./node_modules/@wordpress/core-data/build-module/queried-data/reducer.js





function getContextFromAction(action) {
  const { query } = action;
  if (!query) {
    return "default";
  }
  const queryParts = (0,get_query_parts/* default */.A)(query);
  return queryParts.context;
}
function getMergedItemIds(itemIds, nextItemIds, page, perPage) {
  const receivedAllIds = page === 1 && perPage === -1;
  if (receivedAllIds) {
    return nextItemIds;
  }
  const nextItemIdsStartIndex = (page - 1) * perPage;
  const size = Math.max(
    itemIds?.length ?? 0,
    nextItemIdsStartIndex + nextItemIds.length
  );
  const mergedItemIds = new Array(size);
  for (let i = 0; i < size; i++) {
    const isInNextItemsRange = i >= nextItemIdsStartIndex && i < nextItemIdsStartIndex + perPage;
    mergedItemIds[i] = isInNextItemsRange ? nextItemIds[i - nextItemIdsStartIndex] : itemIds?.[i];
  }
  return mergedItemIds;
}
function removeEntitiesById(entities, ids) {
  return Object.fromEntries(
    Object.entries(entities).filter(
      ([id]) => !ids.some((itemId) => {
        if (Number.isInteger(itemId)) {
          return itemId === +id;
        }
        return itemId === id;
      })
    )
  );
}
function items(state = {}, action) {
  switch (action.type) {
    case "RECEIVE_ITEMS": {
      const context = getContextFromAction(action);
      const key = action.key || entities/* DEFAULT_ENTITY_KEY */.C_;
      return {
        ...state,
        [context]: {
          ...state[context],
          ...action.items.reduce((accumulator, value) => {
            const itemId = value?.[key];
            accumulator[itemId] = conservativeMapItem(
              state?.[context]?.[itemId],
              value
            );
            return accumulator;
          }, {})
        }
      };
    }
    case "REMOVE_ITEMS":
      return Object.fromEntries(
        Object.entries(state).map(([itemId, contextState]) => [
          itemId,
          removeEntitiesById(contextState, action.itemIds)
        ])
      );
  }
  return state;
}
function itemIsComplete(state = {}, action) {
  switch (action.type) {
    case "RECEIVE_ITEMS": {
      const context = getContextFromAction(action);
      const { query, key = entities/* DEFAULT_ENTITY_KEY */.C_ } = action;
      const queryParts = query ? (0,get_query_parts/* default */.A)(query) : {};
      const isCompleteQuery = !query || !Array.isArray(queryParts.fields);
      return {
        ...state,
        [context]: {
          ...state[context],
          ...action.items.reduce((result, item) => {
            const itemId = item?.[key];
            result[itemId] = state?.[context]?.[itemId] || isCompleteQuery;
            return result;
          }, {})
        }
      };
    }
    case "REMOVE_ITEMS":
      return Object.fromEntries(
        Object.entries(state).map(([itemId, contextState]) => [
          itemId,
          removeEntitiesById(contextState, action.itemIds)
        ])
      );
  }
  return state;
}
const receiveQueries = (0,external_wp_compose_namespaceObject.compose)([
  // Limit to matching action type so we don't attempt to replace action on
  // an unhandled action.
  if_matching_action_default((action) => "query" in action),
  // Inject query parts into action for use both in `onSubKey` and reducer.
  replace_action_default((action) => {
    if (action.query) {
      return {
        ...action,
        ...(0,get_query_parts/* default */.A)(action.query)
      };
    }
    return action;
  }),
  on_sub_key_default("context"),
  // Queries shape is shared, but keyed by query `stableKey` part. Original
  // reducer tracks only a single query object.
  on_sub_key_default("stableKey")
])((state = {}, action) => {
  const { type, page, perPage, key = entities/* DEFAULT_ENTITY_KEY */.C_ } = action;
  if (type !== "RECEIVE_ITEMS") {
    return state;
  }
  return {
    itemIds: getMergedItemIds(
      state?.itemIds || [],
      action.items.map((item) => item?.[key]).filter(Boolean),
      page,
      perPage
    ),
    meta: action.meta
  };
});
const queries = (state = {}, action) => {
  switch (action.type) {
    case "RECEIVE_ITEMS":
      return receiveQueries(state, action);
    case "REMOVE_ITEMS":
      const removedItems = action.itemIds.reduce((result, itemId) => {
        result[itemId] = true;
        return result;
      }, {});
      return Object.fromEntries(
        Object.entries(state).map(
          ([queryGroup, contextQueries]) => [
            queryGroup,
            Object.fromEntries(
              Object.entries(contextQueries).map(
                ([query, queryItems]) => [
                  query,
                  {
                    ...queryItems,
                    itemIds: queryItems.itemIds.filter(
                      (queryId) => !removedItems[queryId]
                    )
                  }
                ]
              )
            )
          ]
        )
      );
    default:
      return state;
  }
};
var reducer_default = (0,external_wp_data_.combineReducers)({
  items,
  itemIsComplete,
  queries
});


;// ./node_modules/@wordpress/core-data/build-module/reducer.js







function users(state = { byId: {}, queries: {} }, action) {
  switch (action.type) {
    case "RECEIVE_USER_QUERY":
      return {
        byId: {
          ...state.byId,
          // Key users by their ID.
          ...action.users.reduce(
            (newUsers, user) => ({
              ...newUsers,
              [user.id]: user
            }),
            {}
          )
        },
        queries: {
          ...state.queries,
          [action.queryID]: action.users.map((user) => user.id)
        }
      };
  }
  return state;
}
function currentUser(state = {}, action) {
  switch (action.type) {
    case "RECEIVE_CURRENT_USER":
      return action.currentUser;
  }
  return state;
}
function currentTheme(state = void 0, action) {
  switch (action.type) {
    case "RECEIVE_CURRENT_THEME":
      return action.currentTheme.stylesheet;
  }
  return state;
}
function currentGlobalStylesId(state = void 0, action) {
  switch (action.type) {
    case "RECEIVE_CURRENT_GLOBAL_STYLES_ID":
      return action.id;
  }
  return state;
}
function themeBaseGlobalStyles(state = {}, action) {
  switch (action.type) {
    case "RECEIVE_THEME_GLOBAL_STYLES":
      return {
        ...state,
        [action.stylesheet]: action.globalStyles
      };
  }
  return state;
}
function themeGlobalStyleVariations(state = {}, action) {
  switch (action.type) {
    case "RECEIVE_THEME_GLOBAL_STYLE_VARIATIONS":
      return {
        ...state,
        [action.stylesheet]: action.variations
      };
  }
  return state;
}
const withMultiEntityRecordEdits = (reducer) => (state, action) => {
  if (action.type === "UNDO" || action.type === "REDO") {
    const { record } = action;
    let newState = state;
    record.forEach(({ id: { kind, name, recordId }, changes }) => {
      newState = reducer(newState, {
        type: "EDIT_ENTITY_RECORD",
        kind,
        name,
        recordId,
        edits: Object.entries(changes).reduce(
          (acc, [key, value]) => {
            acc[key] = action.type === "UNDO" ? value.from : value.to;
            return acc;
          },
          {}
        )
      });
    });
    return newState;
  }
  return reducer(state, action);
};
function entity(entityConfig) {
  return (0,external_wp_compose_namespaceObject.compose)([
    withMultiEntityRecordEdits,
    // Limit to matching action type so we don't attempt to replace action on
    // an unhandled action.
    if_matching_action_default(
      (action) => action.name && action.kind && action.name === entityConfig.name && action.kind === entityConfig.kind
    ),
    // Inject the entity config into the action.
    replace_action_default((action) => {
      return {
        key: entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_,
        ...action
      };
    })
  ])(
    (0,external_wp_data_.combineReducers)({
      queriedData: reducer_default,
      edits: (state = {}, action) => {
        switch (action.type) {
          case "RECEIVE_ITEMS":
            const context = action?.query?.context ?? "default";
            if (context !== "default") {
              return state;
            }
            const nextState = { ...state };
            for (const record of action.items) {
              const recordId = record?.[action.key];
              const edits = nextState[recordId];
              if (!edits) {
                continue;
              }
              const nextEdits2 = Object.keys(edits).reduce(
                (acc, key) => {
                  if (
                    // Edits are the "raw" attribute values, but records may have
                    // objects with more properties, so we use `get` here for the
                    // comparison.
                    !es6_default()(
                      edits[key],
                      record[key]?.raw ?? record[key]
                    ) && // Sometimes the server alters the sent value which means
                    // we need to also remove the edits before the api request.
                    (!action.persistedEdits || !es6_default()(
                      edits[key],
                      action.persistedEdits[key]
                    ))
                  ) {
                    acc[key] = edits[key];
                  }
                  return acc;
                },
                {}
              );
              if (Object.keys(nextEdits2).length) {
                nextState[recordId] = nextEdits2;
              } else {
                delete nextState[recordId];
              }
            }
            return nextState;
          case "EDIT_ENTITY_RECORD":
            const nextEdits = {
              ...state[action.recordId],
              ...action.edits
            };
            Object.keys(nextEdits).forEach((key) => {
              if (nextEdits[key] === void 0) {
                delete nextEdits[key];
              }
            });
            return {
              ...state,
              [action.recordId]: nextEdits
            };
        }
        return state;
      },
      saving: (state = {}, action) => {
        switch (action.type) {
          case "SAVE_ENTITY_RECORD_START":
          case "SAVE_ENTITY_RECORD_FINISH":
            return {
              ...state,
              [action.recordId]: {
                pending: action.type === "SAVE_ENTITY_RECORD_START",
                error: action.error,
                isAutosave: action.isAutosave
              }
            };
        }
        return state;
      },
      deleting: (state = {}, action) => {
        switch (action.type) {
          case "DELETE_ENTITY_RECORD_START":
          case "DELETE_ENTITY_RECORD_FINISH":
            return {
              ...state,
              [action.recordId]: {
                pending: action.type === "DELETE_ENTITY_RECORD_START",
                error: action.error
              }
            };
        }
        return state;
      },
      revisions: (state = {}, action) => {
        if (action.type === "RECEIVE_ITEM_REVISIONS") {
          const recordKey = action.recordKey;
          delete action.recordKey;
          const newState = reducer_default(state[recordKey], {
            ...action,
            type: "RECEIVE_ITEMS"
          });
          return {
            ...state,
            [recordKey]: newState
          };
        }
        if (action.type === "REMOVE_ITEMS") {
          return Object.fromEntries(
            Object.entries(state).filter(
              ([id]) => !action.itemIds.some((itemId) => {
                if (Number.isInteger(itemId)) {
                  return itemId === +id;
                }
                return itemId === id;
              })
            )
          );
        }
        return state;
      }
    })
  );
}
function entitiesConfig(state = entities/* rootEntitiesConfig */.Mr, action) {
  switch (action.type) {
    case "ADD_ENTITIES":
      return [...state, ...action.entities];
  }
  return state;
}
const reducer_entities = (state = {}, action) => {
  const newConfig = entitiesConfig(state.config, action);
  let entitiesDataReducer = state.reducer;
  if (!entitiesDataReducer || newConfig !== state.config) {
    const entitiesByKind = newConfig.reduce((acc, record) => {
      const { kind } = record;
      if (!acc[kind]) {
        acc[kind] = [];
      }
      acc[kind].push(record);
      return acc;
    }, {});
    entitiesDataReducer = (0,external_wp_data_.combineReducers)(
      Object.fromEntries(
        Object.entries(entitiesByKind).map(
          ([kind, subEntities]) => {
            const kindReducer = (0,external_wp_data_.combineReducers)(
              Object.fromEntries(
                subEntities.map((entityConfig) => [
                  entityConfig.name,
                  entity(entityConfig)
                ])
              )
            );
            return [kind, kindReducer];
          }
        )
      )
    );
  }
  const newData = entitiesDataReducer(state.records, action);
  if (newData === state.records && newConfig === state.config && entitiesDataReducer === state.reducer) {
    return state;
  }
  return {
    reducer: entitiesDataReducer,
    records: newData,
    config: newConfig
  };
};
function undoManager(state = createUndoManager()) {
  return state;
}
function editsReference(state = {}, action) {
  switch (action.type) {
    case "EDIT_ENTITY_RECORD":
    case "UNDO":
    case "REDO":
      return {};
  }
  return state;
}
function embedPreviews(state = {}, action) {
  switch (action.type) {
    case "RECEIVE_EMBED_PREVIEW":
      const { url, preview } = action;
      return {
        ...state,
        [url]: preview
      };
  }
  return state;
}
function userPermissions(state = {}, action) {
  switch (action.type) {
    case "RECEIVE_USER_PERMISSION":
      return {
        ...state,
        [action.key]: action.isAllowed
      };
    case "RECEIVE_USER_PERMISSIONS":
      return {
        ...state,
        ...action.permissions
      };
  }
  return state;
}
function autosaves(state = {}, action) {
  switch (action.type) {
    case "RECEIVE_AUTOSAVES":
      const { postId, autosaves: autosavesData } = action;
      return {
        ...state,
        [postId]: autosavesData
      };
  }
  return state;
}
function blockPatterns(state = [], action) {
  switch (action.type) {
    case "RECEIVE_BLOCK_PATTERNS":
      return action.patterns;
  }
  return state;
}
function blockPatternCategories(state = [], action) {
  switch (action.type) {
    case "RECEIVE_BLOCK_PATTERN_CATEGORIES":
      return action.categories;
  }
  return state;
}
function userPatternCategories(state = [], action) {
  switch (action.type) {
    case "RECEIVE_USER_PATTERN_CATEGORIES":
      return action.patternCategories;
  }
  return state;
}
function navigationFallbackId(state = null, action) {
  switch (action.type) {
    case "RECEIVE_NAVIGATION_FALLBACK_ID":
      return action.fallbackId;
  }
  return state;
}
function themeGlobalStyleRevisions(state = {}, action) {
  switch (action.type) {
    case "RECEIVE_THEME_GLOBAL_STYLE_REVISIONS":
      return {
        ...state,
        [action.currentId]: action.revisions
      };
  }
  return state;
}
function defaultTemplates(state = {}, action) {
  switch (action.type) {
    case "RECEIVE_DEFAULT_TEMPLATE":
      return {
        ...state,
        [JSON.stringify(action.query)]: action.templateId
      };
  }
  return state;
}
function registeredPostMeta(state = {}, action) {
  switch (action.type) {
    case "RECEIVE_REGISTERED_POST_META":
      return {
        ...state,
        [action.postType]: action.registeredPostMeta
      };
  }
  return state;
}
var reducer_reducer_default = (0,external_wp_data_.combineReducers)({
  users,
  currentTheme,
  currentGlobalStylesId,
  currentUser,
  themeGlobalStyleVariations,
  themeBaseGlobalStyles,
  themeGlobalStyleRevisions,
  entities: reducer_entities,
  editsReference,
  undoManager,
  embedPreviews,
  userPermissions,
  autosaves,
  blockPatterns,
  blockPatternCategories,
  userPatternCategories,
  navigationFallbackId,
  defaultTemplates,
  registeredPostMeta
});



/***/ }),

/***/ 5663:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   xQ: () => (/* binding */ camelCase)
/* harmony export */ });
/* unused harmony exports camelCaseTransform, camelCaseTransformMerge */
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(1635);
/* harmony import */ var pascal_case__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(287);


function camelCaseTransform(input, index) {
    if (index === 0)
        return input.toLowerCase();
    return (0,pascal_case__WEBPACK_IMPORTED_MODULE_0__/* .pascalCaseTransform */ .l3)(input, index);
}
function camelCaseTransformMerge(input, index) {
    if (index === 0)
        return input.toLowerCase();
    return pascalCaseTransformMerge(input);
}
function camelCase(input, options) {
    if (options === void 0) { options = {}; }
    return (0,pascal_case__WEBPACK_IMPORTED_MODULE_0__/* .pascalCase */ .fL)(input, (0,tslib__WEBPACK_IMPORTED_MODULE_1__/* .__assign */ .Cl)({ transform: camelCaseTransform }, options));
}


/***/ }),

/***/ 5891:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  qh: () => (/* reexport */ __experimentalUseEntityRecord),
  bM: () => (/* reexport */ use_entity_records/* __experimentalUseEntityRecords */.bM),
  _: () => (/* reexport */ __experimentalUseResourcePermissions),
  hg: () => (/* reexport */ useEntityBlockEditor),
  mV: () => (/* reexport */ useEntityId),
  S$: () => (/* reexport */ useEntityProp),
  MA: () => (/* reexport */ useEntityRecord),
  $u: () => (/* reexport */ use_entity_records/* default */.Ay),
  qs: () => (/* reexport */ use_resource_permissions_default)
});

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__(7143);
// EXTERNAL MODULE: external ["wp","deprecated"]
var external_wp_deprecated_ = __webpack_require__(4040);
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_);
// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__(6087);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/hooks/use-query-select.js + 2 modules
var use_query_select = __webpack_require__(7541);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/index.js
var build_module = __webpack_require__(4565);
;// ./node_modules/@wordpress/core-data/build-module/hooks/use-entity-record.js





const EMPTY_OBJECT = {};
function useEntityRecord(kind, name, recordId, options = { enabled: true }) {
  const { editEntityRecord, saveEditedEntityRecord } = (0,external_wp_data_.useDispatch)(build_module.store);
  const mutations = (0,external_wp_element_.useMemo)(
    () => ({
      edit: (record2, editOptions = {}) => editEntityRecord(kind, name, recordId, record2, editOptions),
      save: (saveOptions = {}) => saveEditedEntityRecord(kind, name, recordId, {
        throwOnError: true,
        ...saveOptions
      })
    }),
    [editEntityRecord, kind, name, recordId, saveEditedEntityRecord]
  );
  const { editedRecord, hasEdits, edits } = (0,external_wp_data_.useSelect)(
    (select) => {
      if (!options.enabled) {
        return {
          editedRecord: EMPTY_OBJECT,
          hasEdits: false,
          edits: EMPTY_OBJECT
        };
      }
      return {
        editedRecord: select(build_module.store).getEditedEntityRecord(
          kind,
          name,
          recordId
        ),
        hasEdits: select(build_module.store).hasEditsForEntityRecord(
          kind,
          name,
          recordId
        ),
        edits: select(build_module.store).getEntityRecordNonTransientEdits(
          kind,
          name,
          recordId
        )
      };
    },
    [kind, name, recordId, options.enabled]
  );
  const { data: record, ...querySelectRest } = (0,use_query_select/* default */.A)(
    (query) => {
      if (!options.enabled) {
        return {
          data: null
        };
      }
      return query(build_module.store).getEntityRecord(kind, name, recordId);
    },
    [kind, name, recordId, options.enabled]
  );
  return {
    record,
    editedRecord,
    hasEdits,
    edits,
    ...querySelectRest,
    ...mutations
  };
}
function __experimentalUseEntityRecord(kind, name, recordId, options) {
  external_wp_deprecated_default()(`wp.data.__experimentalUseEntityRecord`, {
    alternative: "wp.data.useEntityRecord",
    since: "6.1"
  });
  return useEntityRecord(kind, name, recordId, options);
}


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/hooks/use-entity-records.js
var use_entity_records = __webpack_require__(7078);
;// external ["wp","warning"]
const external_wp_warning_namespaceObject = window["wp"]["warning"];
var external_wp_warning_default = /*#__PURE__*/__webpack_require__.n(external_wp_warning_namespaceObject);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/hooks/constants.js
var constants = __webpack_require__(2859);
;// ./node_modules/@wordpress/core-data/build-module/hooks/use-resource-permissions.js





function useResourcePermissions(resource, id) {
  const isEntity = typeof resource === "object";
  const resourceAsString = isEntity ? JSON.stringify(resource) : resource;
  if (isEntity && typeof id !== "undefined") {
    external_wp_warning_default()(
      `When 'resource' is an entity object, passing 'id' as a separate argument isn't supported.`
    );
  }
  return (0,use_query_select/* default */.A)(
    (resolve) => {
      const hasId = isEntity ? !!resource.id : !!id;
      const { canUser } = resolve(build_module.store);
      const create = canUser(
        "create",
        isEntity ? { kind: resource.kind, name: resource.name } : resource
      );
      if (!hasId) {
        const read2 = canUser("read", resource);
        const isResolving2 = create.isResolving || read2.isResolving;
        const hasResolved2 = create.hasResolved && read2.hasResolved;
        let status2 = constants/* Status */.n.Idle;
        if (isResolving2) {
          status2 = constants/* Status */.n.Resolving;
        } else if (hasResolved2) {
          status2 = constants/* Status */.n.Success;
        }
        return {
          status: status2,
          isResolving: isResolving2,
          hasResolved: hasResolved2,
          canCreate: create.hasResolved && create.data,
          canRead: read2.hasResolved && read2.data
        };
      }
      const read = canUser("read", resource, id);
      const update = canUser("update", resource, id);
      const _delete = canUser("delete", resource, id);
      const isResolving = read.isResolving || create.isResolving || update.isResolving || _delete.isResolving;
      const hasResolved = read.hasResolved && create.hasResolved && update.hasResolved && _delete.hasResolved;
      let status = constants/* Status */.n.Idle;
      if (isResolving) {
        status = constants/* Status */.n.Resolving;
      } else if (hasResolved) {
        status = constants/* Status */.n.Success;
      }
      return {
        status,
        isResolving,
        hasResolved,
        canRead: hasResolved && read.data,
        canCreate: hasResolved && create.data,
        canUpdate: hasResolved && update.data,
        canDelete: hasResolved && _delete.data
      };
    },
    [resourceAsString, id]
  );
}
var use_resource_permissions_default = useResourcePermissions;
function __experimentalUseResourcePermissions(resource, id) {
  external_wp_deprecated_default()(`wp.data.__experimentalUseResourcePermissions`, {
    alternative: "wp.data.useResourcePermissions",
    since: "6.1"
  });
  return useResourcePermissions(resource, id);
}


// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__(4997);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/name.js
var build_module_name = __webpack_require__(2278);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/entity-context.js
var entity_context = __webpack_require__(8843);
;// ./node_modules/@wordpress/core-data/build-module/hooks/use-entity-id.js


function useEntityId(kind, name) {
  const context = (0,external_wp_element_.useContext)(entity_context/* EntityContext */.D);
  return context?.[kind]?.[name];
}


;// external ["wp","richText"]
const external_wp_richText_namespaceObject = window["wp"]["richText"];
;// external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/lock-unlock.js + 1 modules
var lock_unlock = __webpack_require__(6378);
;// ./node_modules/@wordpress/core-data/build-module/footnotes/get-rich-text-values-cached.js


let unlockedApis;
const cache = /* @__PURE__ */ new WeakMap();
function getRichTextValuesCached(block) {
  if (!unlockedApis) {
    unlockedApis = (0,lock_unlock/* unlock */.T)(external_wp_blockEditor_namespaceObject.privateApis);
  }
  if (!cache.has(block)) {
    const values = unlockedApis.getRichTextValues([block]);
    cache.set(block, values);
  }
  return cache.get(block);
}


;// ./node_modules/@wordpress/core-data/build-module/footnotes/get-footnotes-order.js

const get_footnotes_order_cache = /* @__PURE__ */ new WeakMap();
function getBlockFootnotesOrder(block) {
  if (!get_footnotes_order_cache.has(block)) {
    const order = [];
    for (const value of getRichTextValuesCached(block)) {
      if (!value) {
        continue;
      }
      value.replacements.forEach(({ type, attributes }) => {
        if (type === "core/footnote") {
          order.push(attributes["data-fn"]);
        }
      });
    }
    get_footnotes_order_cache.set(block, order);
  }
  return get_footnotes_order_cache.get(block);
}
function getFootnotesOrder(blocks) {
  return blocks.flatMap(getBlockFootnotesOrder);
}


;// ./node_modules/@wordpress/core-data/build-module/footnotes/index.js


let oldFootnotes = {};
function updateFootnotesFromMeta(blocks, meta) {
  const output = { blocks };
  if (!meta) {
    return output;
  }
  if (meta.footnotes === void 0) {
    return output;
  }
  const newOrder = getFootnotesOrder(blocks);
  const footnotes = meta.footnotes ? JSON.parse(meta.footnotes) : [];
  const currentOrder = footnotes.map((fn) => fn.id);
  if (currentOrder.join("") === newOrder.join("")) {
    return output;
  }
  const newFootnotes = newOrder.map(
    (fnId) => footnotes.find((fn) => fn.id === fnId) || oldFootnotes[fnId] || {
      id: fnId,
      content: ""
    }
  );
  function updateAttributes(attributes) {
    if (!attributes || Array.isArray(attributes) || typeof attributes !== "object") {
      return attributes;
    }
    attributes = { ...attributes };
    for (const key in attributes) {
      const value = attributes[key];
      if (Array.isArray(value)) {
        attributes[key] = value.map(updateAttributes);
        continue;
      }
      if (typeof value !== "string" && !(value instanceof external_wp_richText_namespaceObject.RichTextData)) {
        continue;
      }
      const richTextValue = typeof value === "string" ? external_wp_richText_namespaceObject.RichTextData.fromHTMLString(value) : new external_wp_richText_namespaceObject.RichTextData(value);
      let hasFootnotes = false;
      richTextValue.replacements.forEach((replacement) => {
        if (replacement.type === "core/footnote") {
          const id = replacement.attributes["data-fn"];
          const index = newOrder.indexOf(id);
          const countValue = (0,external_wp_richText_namespaceObject.create)({
            html: replacement.innerHTML
          });
          countValue.text = String(index + 1);
          countValue.formats = Array.from(
            { length: countValue.text.length },
            () => countValue.formats[0]
          );
          countValue.replacements = Array.from(
            { length: countValue.text.length },
            () => countValue.replacements[0]
          );
          replacement.innerHTML = (0,external_wp_richText_namespaceObject.toHTMLString)({
            value: countValue
          });
          hasFootnotes = true;
        }
      });
      if (hasFootnotes) {
        attributes[key] = typeof value === "string" ? richTextValue.toHTMLString() : richTextValue;
      }
    }
    return attributes;
  }
  function updateBlocksAttributes(__blocks) {
    return __blocks.map((block) => {
      return {
        ...block,
        attributes: updateAttributes(block.attributes),
        innerBlocks: updateBlocksAttributes(block.innerBlocks)
      };
    });
  }
  const newBlocks = updateBlocksAttributes(blocks);
  oldFootnotes = {
    ...oldFootnotes,
    ...footnotes.reduce((acc, fn) => {
      if (!newOrder.includes(fn.id)) {
        acc[fn.id] = fn;
      }
      return acc;
    }, {})
  };
  return {
    meta: {
      ...meta,
      footnotes: JSON.stringify(newFootnotes)
    },
    blocks: newBlocks
  };
}


;// ./node_modules/@wordpress/core-data/build-module/hooks/use-entity-block-editor.js






const EMPTY_ARRAY = [];
const parsedBlocksCache = /* @__PURE__ */ new WeakMap();
function useEntityBlockEditor(kind, name, { id: _id } = {}) {
  const providerId = useEntityId(kind, name);
  const id = _id ?? providerId;
  const { getEntityRecord, getEntityRecordEdits } = (0,external_wp_data_.useSelect)(build_module_name/* STORE_NAME */.E);
  const { content, editedBlocks, meta } = (0,external_wp_data_.useSelect)(
    (select) => {
      if (!id) {
        return {};
      }
      const { getEditedEntityRecord } = select(build_module_name/* STORE_NAME */.E);
      const editedRecord = getEditedEntityRecord(kind, name, id);
      return {
        editedBlocks: editedRecord.blocks,
        content: editedRecord.content,
        meta: editedRecord.meta
      };
    },
    [kind, name, id]
  );
  const { __unstableCreateUndoLevel, editEntityRecord } = (0,external_wp_data_.useDispatch)(build_module_name/* STORE_NAME */.E);
  const blocks = (0,external_wp_element_.useMemo)(() => {
    if (!id) {
      return void 0;
    }
    if (editedBlocks) {
      return editedBlocks;
    }
    if (!content || typeof content !== "string") {
      return EMPTY_ARRAY;
    }
    const edits = getEntityRecordEdits(kind, name, id);
    const isUnedited = !edits || !Object.keys(edits).length;
    const cackeKey = isUnedited ? getEntityRecord(kind, name, id) : edits;
    let _blocks = parsedBlocksCache.get(cackeKey);
    if (!_blocks) {
      _blocks = (0,external_wp_blocks_.parse)(content);
      parsedBlocksCache.set(cackeKey, _blocks);
    }
    return _blocks;
  }, [
    kind,
    name,
    id,
    editedBlocks,
    content,
    getEntityRecord,
    getEntityRecordEdits
  ]);
  const onChange = (0,external_wp_element_.useCallback)(
    (newBlocks, options) => {
      const noChange = blocks === newBlocks;
      if (noChange) {
        return __unstableCreateUndoLevel(kind, name, id);
      }
      const { selection, ...rest } = options;
      const edits = {
        selection,
        content: ({ blocks: blocksForSerialization = [] }) => (0,external_wp_blocks_.__unstableSerializeAndClean)(blocksForSerialization),
        ...updateFootnotesFromMeta(newBlocks, meta)
      };
      editEntityRecord(kind, name, id, edits, {
        isCached: false,
        ...rest
      });
    },
    [
      kind,
      name,
      id,
      blocks,
      meta,
      __unstableCreateUndoLevel,
      editEntityRecord
    ]
  );
  const onInput = (0,external_wp_element_.useCallback)(
    (newBlocks, options) => {
      const { selection, ...rest } = options;
      const footnotesChanges = updateFootnotesFromMeta(newBlocks, meta);
      const edits = { selection, ...footnotesChanges };
      editEntityRecord(kind, name, id, edits, {
        isCached: true,
        ...rest
      });
    },
    [kind, name, id, meta, editEntityRecord]
  );
  return [blocks, onInput, onChange];
}


;// ./node_modules/@wordpress/core-data/build-module/hooks/use-entity-prop.js




function useEntityProp(kind, name, prop, _id) {
  const providerId = useEntityId(kind, name);
  const id = _id ?? providerId;
  const { value, fullValue } = (0,external_wp_data_.useSelect)(
    (select) => {
      const { getEntityRecord, getEditedEntityRecord } = select(build_module_name/* STORE_NAME */.E);
      const record = getEntityRecord(kind, name, id);
      const editedRecord = getEditedEntityRecord(kind, name, id);
      return record && editedRecord ? {
        value: editedRecord[prop],
        fullValue: record[prop]
      } : {};
    },
    [kind, name, id, prop]
  );
  const { editEntityRecord } = (0,external_wp_data_.useDispatch)(build_module_name/* STORE_NAME */.E);
  const setValue = (0,external_wp_element_.useCallback)(
    (newValue) => {
      editEntityRecord(kind, name, id, {
        [prop]: newValue
      });
    },
    [editEntityRecord, kind, name, id, prop]
  );
  return [value, setValue, fullValue];
}


;// ./node_modules/@wordpress/core-data/build-module/hooks/index.js









/***/ }),

/***/ 5914:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  C_: () => (/* binding */ DEFAULT_ENTITY_KEY),
  L2: () => (/* binding */ additionalEntityConfigLoaders),
  TK: () => (/* binding */ deprecatedEntities),
  zD: () => (/* binding */ getMethodName),
  Mr: () => (/* binding */ rootEntitiesConfig)
});

// UNUSED EXPORTS: prePersistPostType

// EXTERNAL MODULE: ./node_modules/tslib/tslib.es6.mjs
var tslib_es6 = __webpack_require__(1635);
// EXTERNAL MODULE: ./node_modules/no-case/dist.es2015/index.js
var dist_es2015 = __webpack_require__(2226);
;// ./node_modules/upper-case-first/dist.es2015/index.js
/**
 * Upper case the first character of an input string.
 */
function upperCaseFirst(input) {
    return input.charAt(0).toUpperCase() + input.substr(1);
}

;// ./node_modules/capital-case/dist.es2015/index.js



function capitalCaseTransform(input) {
    return upperCaseFirst(input.toLowerCase());
}
function capitalCase(input, options) {
    if (options === void 0) { options = {}; }
    return (0,dist_es2015/* noCase */.W)(input, (0,tslib_es6/* __assign */.Cl)({ delimiter: " ", transform: capitalCaseTransform }, options));
}

// EXTERNAL MODULE: ./node_modules/pascal-case/dist.es2015/index.js
var pascal_case_dist_es2015 = __webpack_require__(287);
// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__(1455);
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);
// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__(4997);
// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__(7723);
;// ./node_modules/@wordpress/core-data/build-module/entities.js





const DEFAULT_ENTITY_KEY = "id";
const POST_RAW_ATTRIBUTES = ["title", "excerpt", "content"];
const blocksTransientEdits = {
  blocks: {
    read: (record) => (0,external_wp_blocks_.parse)(record.content?.raw ?? ""),
    write: (record) => ({
      content: (0,external_wp_blocks_.__unstableSerializeAndClean)(record.blocks)
    })
  }
};
const rootEntitiesConfig = [
  {
    label: (0,external_wp_i18n_.__)("Base"),
    kind: "root",
    key: false,
    name: "__unstableBase",
    baseURL: "/",
    baseURLParams: {
      // Please also change the preload path when changing this.
      // @see lib/compat/wordpress-6.8/preload.php
      _fields: [
        "description",
        "gmt_offset",
        "home",
        "name",
        "site_icon",
        "site_icon_url",
        "site_logo",
        "timezone_string",
        "url",
        "page_for_posts",
        "page_on_front",
        "show_on_front"
      ].join(",")
    },
    // The entity doesn't support selecting multiple records.
    // The property is maintained for backward compatibility.
    plural: "__unstableBases"
  },
  {
    label: (0,external_wp_i18n_.__)("Post Type"),
    name: "postType",
    kind: "root",
    key: "slug",
    baseURL: "/wp/v2/types",
    baseURLParams: { context: "edit" },
    plural: "postTypes"
  },
  {
    name: "media",
    kind: "root",
    baseURL: "/wp/v2/media",
    baseURLParams: { context: "edit" },
    plural: "mediaItems",
    label: (0,external_wp_i18n_.__)("Media"),
    rawAttributes: ["caption", "title", "description"],
    supportsPagination: true
  },
  {
    name: "taxonomy",
    kind: "root",
    key: "slug",
    baseURL: "/wp/v2/taxonomies",
    baseURLParams: { context: "edit" },
    plural: "taxonomies",
    label: (0,external_wp_i18n_.__)("Taxonomy")
  },
  {
    name: "sidebar",
    kind: "root",
    baseURL: "/wp/v2/sidebars",
    baseURLParams: { context: "edit" },
    plural: "sidebars",
    transientEdits: { blocks: true },
    label: (0,external_wp_i18n_.__)("Widget areas")
  },
  {
    name: "widget",
    kind: "root",
    baseURL: "/wp/v2/widgets",
    baseURLParams: { context: "edit" },
    plural: "widgets",
    transientEdits: { blocks: true },
    label: (0,external_wp_i18n_.__)("Widgets")
  },
  {
    name: "widgetType",
    kind: "root",
    baseURL: "/wp/v2/widget-types",
    baseURLParams: { context: "edit" },
    plural: "widgetTypes",
    label: (0,external_wp_i18n_.__)("Widget types")
  },
  {
    label: (0,external_wp_i18n_.__)("User"),
    name: "user",
    kind: "root",
    baseURL: "/wp/v2/users",
    getTitle: (record) => record?.name || record?.slug,
    baseURLParams: { context: "edit" },
    plural: "users",
    supportsPagination: true
  },
  {
    name: "comment",
    kind: "root",
    baseURL: "/wp/v2/comments",
    baseURLParams: { context: "edit" },
    plural: "comments",
    label: (0,external_wp_i18n_.__)("Comment"),
    supportsPagination: true
  },
  {
    name: "menu",
    kind: "root",
    baseURL: "/wp/v2/menus",
    baseURLParams: { context: "edit" },
    plural: "menus",
    label: (0,external_wp_i18n_.__)("Menu"),
    supportsPagination: true
  },
  {
    name: "menuItem",
    kind: "root",
    baseURL: "/wp/v2/menu-items",
    baseURLParams: { context: "edit" },
    plural: "menuItems",
    label: (0,external_wp_i18n_.__)("Menu Item"),
    rawAttributes: ["title"],
    supportsPagination: true
  },
  {
    name: "menuLocation",
    kind: "root",
    baseURL: "/wp/v2/menu-locations",
    baseURLParams: { context: "edit" },
    plural: "menuLocations",
    label: (0,external_wp_i18n_.__)("Menu Location"),
    key: "name"
  },
  {
    label: (0,external_wp_i18n_.__)("Global Styles"),
    name: "globalStyles",
    kind: "root",
    baseURL: "/wp/v2/global-styles",
    baseURLParams: { context: "edit" },
    plural: "globalStylesVariations",
    // Should be different from name.
    getTitle: () => (0,external_wp_i18n_.__)("Custom Styles"),
    getRevisionsUrl: (parentId, revisionId) => `/wp/v2/global-styles/${parentId}/revisions${revisionId ? "/" + revisionId : ""}`,
    supportsPagination: true
  },
  {
    label: (0,external_wp_i18n_.__)("Themes"),
    name: "theme",
    kind: "root",
    baseURL: "/wp/v2/themes",
    baseURLParams: { context: "edit" },
    plural: "themes",
    key: "stylesheet"
  },
  {
    label: (0,external_wp_i18n_.__)("Plugins"),
    name: "plugin",
    kind: "root",
    baseURL: "/wp/v2/plugins",
    baseURLParams: { context: "edit" },
    plural: "plugins",
    key: "plugin"
  },
  {
    label: (0,external_wp_i18n_.__)("Status"),
    name: "status",
    kind: "root",
    baseURL: "/wp/v2/statuses",
    baseURLParams: { context: "edit" },
    plural: "statuses",
    key: "slug"
  }
];
const deprecatedEntities = {
  root: {
    media: {
      since: "6.9",
      alternative: {
        kind: "postType",
        name: "attachment"
      }
    }
  }
};
const additionalEntityConfigLoaders = [
  { kind: "postType", loadEntities: loadPostTypeEntities },
  { kind: "taxonomy", loadEntities: loadTaxonomyEntities },
  {
    kind: "root",
    name: "site",
    plural: "sites",
    loadEntities: loadSiteEntity
  }
];
const prePersistPostType = (persistedRecord, edits) => {
  const newEdits = {};
  if (persistedRecord?.status === "auto-draft") {
    if (!edits.status && !newEdits.status) {
      newEdits.status = "draft";
    }
    if ((!edits.title || edits.title === "Auto Draft") && !newEdits.title && (!persistedRecord?.title || persistedRecord?.title === "Auto Draft")) {
      newEdits.title = "";
    }
  }
  return newEdits;
};
async function loadPostTypeEntities() {
  const postTypes = await external_wp_apiFetch_default()({
    path: "/wp/v2/types?context=view"
  });
  return Object.entries(postTypes ?? {}).map(([name, postType]) => {
    const isTemplate = ["wp_template", "wp_template_part"].includes(
      name
    );
    const namespace = postType?.rest_namespace ?? "wp/v2";
    const entity = {
      kind: "postType",
      baseURL: `/${namespace}/${postType.rest_base}`,
      baseURLParams: { context: "edit" },
      name,
      label: postType.name,
      transientEdits: {
        ...blocksTransientEdits,
        selection: true
      },
      mergedEdits: { meta: true },
      rawAttributes: POST_RAW_ATTRIBUTES,
      getTitle: (record) => record?.title?.rendered || record?.title || (isTemplate ? capitalCase(record.slug ?? "") : String(record.id)),
      __unstablePrePersist: isTemplate ? void 0 : prePersistPostType,
      __unstable_rest_base: postType.rest_base,
      supportsPagination: true,
      getRevisionsUrl: (parentId, revisionId) => `/${namespace}/${postType.rest_base}/${parentId}/revisions${revisionId ? "/" + revisionId : ""}`,
      revisionKey: isTemplate ? "wp_id" : DEFAULT_ENTITY_KEY
    };
    if (window.__experimentalEnableSync) {
      if (false) {}
    }
    return entity;
  });
}
async function loadTaxonomyEntities() {
  const taxonomies = await external_wp_apiFetch_default()({
    path: "/wp/v2/taxonomies?context=view"
  });
  return Object.entries(taxonomies ?? {}).map(([name, taxonomy]) => {
    const namespace = taxonomy?.rest_namespace ?? "wp/v2";
    return {
      kind: "taxonomy",
      baseURL: `/${namespace}/${taxonomy.rest_base}`,
      baseURLParams: { context: "edit" },
      name,
      label: taxonomy.name,
      getTitle: (record) => record?.name,
      supportsPagination: true
    };
  });
}
async function loadSiteEntity() {
  const entity = {
    label: (0,external_wp_i18n_.__)("Site"),
    name: "site",
    kind: "root",
    key: false,
    baseURL: "/wp/v2/settings",
    meta: {}
  };
  if (window.__experimentalEnableSync) {
    if (false) {}
  }
  const site = await external_wp_apiFetch_default()({
    path: entity.baseURL,
    method: "OPTIONS"
  });
  const labels = {};
  Object.entries(site?.schema?.properties ?? {}).forEach(
    ([key, value]) => {
      if (typeof value === "object" && value.title) {
        labels[key] = value.title;
      }
    }
  );
  return [{ ...entity, meta: { labels } }];
}
const getMethodName = (kind, name, prefix = "get") => {
  const kindPrefix = kind === "root" ? "" : (0,pascal_case_dist_es2015/* pascalCase */.fL)(kind);
  const suffix = (0,pascal_case_dist_es2015/* pascalCase */.fL)(name);
  return `${prefix}${kindPrefix}${suffix}`;
};



/***/ }),

/***/ 6087:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["element"];

/***/ }),

/***/ 6378:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  s: () => (/* binding */ lock),
  T: () => (/* binding */ unlock)
});

;// external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// ./node_modules/@wordpress/core-data/build-module/lock-unlock.js

const { lock, unlock } = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/core-data"
);



/***/ }),

/***/ 6384:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  __experimentalGetCurrentGlobalStylesId: () => (/* binding */ __experimentalGetCurrentGlobalStylesId),
  __experimentalGetCurrentThemeBaseGlobalStyles: () => (/* binding */ __experimentalGetCurrentThemeBaseGlobalStyles),
  __experimentalGetCurrentThemeGlobalStylesVariations: () => (/* binding */ __experimentalGetCurrentThemeGlobalStylesVariations),
  canUser: () => (/* binding */ canUser),
  canUserEditEntityRecord: () => (/* binding */ canUserEditEntityRecord),
  getAuthors: () => (/* binding */ getAuthors),
  getAutosave: () => (/* binding */ getAutosave),
  getAutosaves: () => (/* binding */ getAutosaves),
  getBlockPatternCategories: () => (/* binding */ getBlockPatternCategories),
  getBlockPatterns: () => (/* binding */ getBlockPatterns),
  getCurrentTheme: () => (/* binding */ getCurrentTheme),
  getCurrentThemeGlobalStylesRevisions: () => (/* binding */ getCurrentThemeGlobalStylesRevisions),
  getCurrentUser: () => (/* binding */ getCurrentUser),
  getDefaultTemplateId: () => (/* binding */ getDefaultTemplateId),
  getEditedEntityRecord: () => (/* binding */ getEditedEntityRecord),
  getEmbedPreview: () => (/* binding */ getEmbedPreview),
  getEntitiesConfig: () => (/* binding */ getEntitiesConfig),
  getEntityRecord: () => (/* binding */ getEntityRecord),
  getEntityRecords: () => (/* binding */ getEntityRecords),
  getEntityRecordsTotalItems: () => (/* binding */ getEntityRecordsTotalItems),
  getEntityRecordsTotalPages: () => (/* binding */ getEntityRecordsTotalPages),
  getNavigationFallbackId: () => (/* binding */ getNavigationFallbackId),
  getRawEntityRecord: () => (/* binding */ getRawEntityRecord),
  getRegisteredPostMeta: () => (/* binding */ getRegisteredPostMeta),
  getRevision: () => (/* binding */ getRevision),
  getRevisions: () => (/* binding */ getRevisions),
  getThemeSupports: () => (/* binding */ getThemeSupports),
  getUserPatternCategories: () => (/* binding */ getUserPatternCategories)
});

// EXTERNAL MODULE: ./node_modules/camel-case/dist.es2015/index.js
var dist_es2015 = __webpack_require__(5663);
// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__(3832);
// EXTERNAL MODULE: external ["wp","htmlEntities"]
var external_wp_htmlEntities_ = __webpack_require__(8537);
// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__(1455);
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/name.js
var build_module_name = __webpack_require__(2278);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/entities.js + 2 modules
var entities = __webpack_require__(5914);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/get-normalized-comma-separable.js
var get_normalized_comma_separable = __webpack_require__(533);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/user-permissions.js
var user_permissions = __webpack_require__(2577);
;// ./node_modules/@wordpress/core-data/build-module/utils/forward-resolver.js
const forwardResolver = (resolverName) => (...args) => async ({ resolveSelect }) => {
  await resolveSelect[resolverName](...args);
};
var forward_resolver_default = forwardResolver;


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/receive-intermediate-results.js
var receive_intermediate_results = __webpack_require__(5101);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/fetch/index.js + 2 modules
var fetch = __webpack_require__(7006);
;// ./node_modules/@wordpress/core-data/build-module/resolvers.js









const getAuthors = (query) => async ({ dispatch }) => {
  const path = (0,external_wp_url_.addQueryArgs)(
    "/wp/v2/users/?who=authors&per_page=100",
    query
  );
  const users = await external_wp_apiFetch_default()({ path });
  dispatch.receiveUserQuery(path, users);
};
const getCurrentUser = () => async ({ dispatch }) => {
  const currentUser = await external_wp_apiFetch_default()({ path: "/wp/v2/users/me" });
  dispatch.receiveCurrentUser(currentUser);
};
const getEntityRecord = (kind, name, key = "", query) => async ({ select, dispatch, registry, resolveSelect }) => {
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.name === name && config.kind === kind
  );
  if (!entityConfig) {
    return;
  }
  const lock = await dispatch.__unstableAcquireStoreLock(
    build_module_name/* STORE_NAME */.E,
    ["entities", "records", kind, name, key],
    { exclusive: false }
  );
  try {
    if (query !== void 0 && query._fields) {
      query = {
        ...query,
        _fields: [
          .../* @__PURE__ */ new Set([
            ...(0,get_normalized_comma_separable/* default */.A)(query._fields) || [],
            entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_
          ])
        ].join()
      };
    }
    if (query !== void 0 && query._fields) {
      const hasRecord = select.hasEntityRecord(
        kind,
        name,
        key,
        query
      );
      if (hasRecord) {
        return;
      }
    }
    const path = (0,external_wp_url_.addQueryArgs)(
      entityConfig.baseURL + (key ? "/" + key : ""),
      {
        ...entityConfig.baseURLParams,
        ...query
      }
    );
    const response = await external_wp_apiFetch_default()({ path, parse: false });
    const record = await response.json();
    const permissions = (0,user_permissions/* getUserPermissionsFromAllowHeader */.qY)(
      response.headers?.get("allow")
    );
    const canUserResolutionsArgs = [];
    const receiveUserPermissionArgs = {};
    for (const action of user_permissions/* ALLOWED_RESOURCE_ACTIONS */.CO) {
      receiveUserPermissionArgs[(0,user_permissions/* getUserPermissionCacheKey */.kC)(action, {
        kind,
        name,
        id: key
      })] = permissions[action];
      canUserResolutionsArgs.push([
        action,
        { kind, name, id: key }
      ]);
    }
    if (window.__experimentalEnableSync && entityConfig.syncConfig && !query) {
      if (false) {}
    }
    registry.batch(() => {
      dispatch.receiveEntityRecords(kind, name, record, query);
      dispatch.receiveUserPermissions(receiveUserPermissionArgs);
      dispatch.finishResolutions("canUser", canUserResolutionsArgs);
    });
  } finally {
    dispatch.__unstableReleaseStoreLock(lock);
  }
};
const getRawEntityRecord = forward_resolver_default("getEntityRecord");
const getEditedEntityRecord = forward_resolver_default("getEntityRecord");
const getEntityRecords = (kind, name, query = {}) => async ({ dispatch, registry, resolveSelect }) => {
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.name === name && config.kind === kind
  );
  if (!entityConfig) {
    return;
  }
  const lock = await dispatch.__unstableAcquireStoreLock(
    build_module_name/* STORE_NAME */.E,
    ["entities", "records", kind, name],
    { exclusive: false }
  );
  const rawQuery = { ...query };
  const key = entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_;
  function getResolutionsArgs(records, recordsQuery) {
    const queryArgs = Object.fromEntries(
      Object.entries(recordsQuery).filter(([k, v]) => {
        return ["context", "_fields"].includes(k) && !!v;
      })
    );
    return records.filter((record) => record?.[key]).map((record) => [
      kind,
      name,
      record[key],
      Object.keys(queryArgs).length > 0 ? queryArgs : void 0
    ]);
  }
  try {
    if (query._fields) {
      query = {
        ...query,
        _fields: [
          .../* @__PURE__ */ new Set([
            ...(0,get_normalized_comma_separable/* default */.A)(query._fields) || [],
            key
          ])
        ].join()
      };
    }
    const path = (0,external_wp_url_.addQueryArgs)(entityConfig.baseURL, {
      ...entityConfig.baseURLParams,
      ...query
    });
    let records = [], meta;
    if (entityConfig.supportsPagination && query.per_page !== -1) {
      const response = await external_wp_apiFetch_default()({ path, parse: false });
      records = Object.values(await response.json());
      meta = {
        totalItems: parseInt(
          response.headers.get("X-WP-Total")
        ),
        totalPages: parseInt(
          response.headers.get("X-WP-TotalPages")
        )
      };
    } else if (query.per_page === -1 && query[receive_intermediate_results/* RECEIVE_INTERMEDIATE_RESULTS */.Z] === true) {
      let page = 1;
      let totalPages;
      do {
        const response = await external_wp_apiFetch_default()({
          path: (0,external_wp_url_.addQueryArgs)(path, { page, per_page: 100 }),
          parse: false
        });
        const pageRecords = Object.values(await response.json());
        totalPages = parseInt(
          response.headers.get("X-WP-TotalPages")
        );
        if (!meta) {
          meta = {
            totalItems: parseInt(
              response.headers.get("X-WP-Total")
            ),
            totalPages: 1
          };
        }
        records.push(...pageRecords);
        registry.batch(() => {
          dispatch.receiveEntityRecords(
            kind,
            name,
            records,
            query,
            false,
            void 0,
            meta
          );
          dispatch.finishResolutions(
            "getEntityRecord",
            getResolutionsArgs(pageRecords, rawQuery)
          );
        });
        page++;
      } while (page <= totalPages);
    } else {
      records = Object.values(await external_wp_apiFetch_default()({ path }));
      meta = {
        totalItems: records.length,
        totalPages: 1
      };
    }
    if (query._fields) {
      records = records.map((record) => {
        query._fields.split(",").forEach((field) => {
          if (!record.hasOwnProperty(field)) {
            record[field] = void 0;
          }
        });
        return record;
      });
    }
    registry.batch(() => {
      dispatch.receiveEntityRecords(
        kind,
        name,
        records,
        query,
        false,
        void 0,
        meta
      );
      const targetHints = records.filter(
        (record) => !!record?.[key] && !!record?._links?.self?.[0]?.targetHints?.allow
      ).map((record) => ({
        id: record[key],
        permissions: (0,user_permissions/* getUserPermissionsFromAllowHeader */.qY)(
          record._links.self[0].targetHints.allow
        )
      }));
      const canUserResolutionsArgs = [];
      const receiveUserPermissionArgs = {};
      for (const targetHint of targetHints) {
        for (const action of user_permissions/* ALLOWED_RESOURCE_ACTIONS */.CO) {
          canUserResolutionsArgs.push([
            action,
            { kind, name, id: targetHint.id }
          ]);
          receiveUserPermissionArgs[(0,user_permissions/* getUserPermissionCacheKey */.kC)(action, {
            kind,
            name,
            id: targetHint.id
          })] = targetHint.permissions[action];
        }
      }
      if (targetHints.length > 0) {
        dispatch.receiveUserPermissions(
          receiveUserPermissionArgs
        );
        dispatch.finishResolutions(
          "canUser",
          canUserResolutionsArgs
        );
      }
      dispatch.finishResolutions(
        "getEntityRecord",
        getResolutionsArgs(records, rawQuery)
      );
      dispatch.__unstableReleaseStoreLock(lock);
    });
  } catch (e) {
    dispatch.__unstableReleaseStoreLock(lock);
  }
};
getEntityRecords.shouldInvalidate = (action, kind, name) => {
  return (action.type === "RECEIVE_ITEMS" || action.type === "REMOVE_ITEMS") && action.invalidateCache && kind === action.kind && name === action.name;
};
const getEntityRecordsTotalItems = forward_resolver_default("getEntityRecords");
const getEntityRecordsTotalPages = forward_resolver_default("getEntityRecords");
const getCurrentTheme = () => async ({ dispatch, resolveSelect }) => {
  const activeThemes = await resolveSelect.getEntityRecords(
    "root",
    "theme",
    { status: "active" }
  );
  dispatch.receiveCurrentTheme(activeThemes[0]);
};
const getThemeSupports = forward_resolver_default("getCurrentTheme");
const getEmbedPreview = (url) => async ({ dispatch }) => {
  try {
    const embedProxyResponse = await external_wp_apiFetch_default()({
      path: (0,external_wp_url_.addQueryArgs)("/oembed/1.0/proxy", { url })
    });
    dispatch.receiveEmbedPreview(url, embedProxyResponse);
  } catch (error) {
    dispatch.receiveEmbedPreview(url, false);
  }
};
const canUser = (requestedAction, resource, id) => async ({ dispatch, registry, resolveSelect }) => {
  if (!user_permissions/* ALLOWED_RESOURCE_ACTIONS */.CO.includes(requestedAction)) {
    throw new Error(`'${requestedAction}' is not a valid action.`);
  }
  const { hasStartedResolution } = registry.select(build_module_name/* STORE_NAME */.E);
  for (const relatedAction of user_permissions/* ALLOWED_RESOURCE_ACTIONS */.CO) {
    if (relatedAction === requestedAction) {
      continue;
    }
    const isAlreadyResolving = hasStartedResolution("canUser", [
      relatedAction,
      resource,
      id
    ]);
    if (isAlreadyResolving) {
      return;
    }
  }
  let resourcePath = null;
  if (typeof resource === "object") {
    if (!resource.kind || !resource.name) {
      throw new Error("The entity resource object is not valid.");
    }
    const configs = await resolveSelect.getEntitiesConfig(
      resource.kind
    );
    const entityConfig = configs.find(
      (config) => config.name === resource.name && config.kind === resource.kind
    );
    if (!entityConfig) {
      return;
    }
    resourcePath = entityConfig.baseURL + (resource.id ? "/" + resource.id : "");
  } else {
    resourcePath = `/wp/v2/${resource}` + (id ? "/" + id : "");
  }
  let response;
  try {
    response = await external_wp_apiFetch_default()({
      path: resourcePath,
      method: "OPTIONS",
      parse: false
    });
  } catch (error) {
    return;
  }
  const permissions = (0,user_permissions/* getUserPermissionsFromAllowHeader */.qY)(
    response.headers?.get("allow")
  );
  registry.batch(() => {
    for (const action of user_permissions/* ALLOWED_RESOURCE_ACTIONS */.CO) {
      const key = (0,user_permissions/* getUserPermissionCacheKey */.kC)(action, resource, id);
      dispatch.receiveUserPermission(key, permissions[action]);
      if (action !== requestedAction) {
        dispatch.finishResolution("canUser", [
          action,
          resource,
          id
        ]);
      }
    }
  });
};
const canUserEditEntityRecord = (kind, name, recordId) => async ({ dispatch }) => {
  await dispatch(canUser("update", { kind, name, id: recordId }));
};
const getAutosaves = (postType, postId) => async ({ dispatch, resolveSelect }) => {
  const {
    rest_base: restBase,
    rest_namespace: restNamespace = "wp/v2",
    supports
  } = await resolveSelect.getPostType(postType);
  if (!supports?.autosave) {
    return;
  }
  const autosaves = await external_wp_apiFetch_default()({
    path: `/${restNamespace}/${restBase}/${postId}/autosaves?context=edit`
  });
  if (autosaves && autosaves.length) {
    dispatch.receiveAutosaves(postId, autosaves);
  }
};
const getAutosave = (postType, postId) => async ({ resolveSelect }) => {
  await resolveSelect.getAutosaves(postType, postId);
};
const __experimentalGetCurrentGlobalStylesId = () => async ({ dispatch, resolveSelect }) => {
  const activeThemes = await resolveSelect.getEntityRecords(
    "root",
    "theme",
    { status: "active" }
  );
  const globalStylesURL = activeThemes?.[0]?._links?.["wp:user-global-styles"]?.[0]?.href;
  if (!globalStylesURL) {
    return;
  }
  const matches = globalStylesURL.match(/\/(\d+)(?:\?|$)/);
  const id = matches ? Number(matches[1]) : null;
  if (id) {
    dispatch.__experimentalReceiveCurrentGlobalStylesId(id);
  }
};
const __experimentalGetCurrentThemeBaseGlobalStyles = () => async ({ resolveSelect, dispatch }) => {
  const currentTheme = await resolveSelect.getCurrentTheme();
  const themeGlobalStyles = await external_wp_apiFetch_default()({
    path: `/wp/v2/global-styles/themes/${currentTheme.stylesheet}?context=view`
  });
  dispatch.__experimentalReceiveThemeBaseGlobalStyles(
    currentTheme.stylesheet,
    themeGlobalStyles
  );
};
const __experimentalGetCurrentThemeGlobalStylesVariations = () => async ({ resolveSelect, dispatch }) => {
  const currentTheme = await resolveSelect.getCurrentTheme();
  const variations = await external_wp_apiFetch_default()({
    path: `/wp/v2/global-styles/themes/${currentTheme.stylesheet}/variations?context=view`
  });
  dispatch.__experimentalReceiveThemeGlobalStyleVariations(
    currentTheme.stylesheet,
    variations
  );
};
const getCurrentThemeGlobalStylesRevisions = () => async ({ resolveSelect, dispatch }) => {
  const globalStylesId = await resolveSelect.__experimentalGetCurrentGlobalStylesId();
  const record = globalStylesId ? await resolveSelect.getEntityRecord(
    "root",
    "globalStyles",
    globalStylesId
  ) : void 0;
  const revisionsURL = record?._links?.["version-history"]?.[0]?.href;
  if (revisionsURL) {
    const resetRevisions = await external_wp_apiFetch_default()({
      url: revisionsURL
    });
    const revisions = resetRevisions?.map(
      (revision) => Object.fromEntries(
        Object.entries(revision).map(([key, value]) => [
          (0,dist_es2015/* camelCase */.xQ)(key),
          value
        ])
      )
    );
    dispatch.receiveThemeGlobalStyleRevisions(
      globalStylesId,
      revisions
    );
  }
};
getCurrentThemeGlobalStylesRevisions.shouldInvalidate = (action) => {
  return action.type === "SAVE_ENTITY_RECORD_FINISH" && action.kind === "root" && !action.error && action.name === "globalStyles";
};
const getBlockPatterns = () => async ({ dispatch }) => {
  const patterns = await (0,fetch/* fetchBlockPatterns */.l$)();
  dispatch({ type: "RECEIVE_BLOCK_PATTERNS", patterns });
};
const getBlockPatternCategories = () => async ({ dispatch }) => {
  const categories = await external_wp_apiFetch_default()({
    path: "/wp/v2/block-patterns/categories"
  });
  dispatch({ type: "RECEIVE_BLOCK_PATTERN_CATEGORIES", categories });
};
const getUserPatternCategories = () => async ({ dispatch, resolveSelect }) => {
  const patternCategories = await resolveSelect.getEntityRecords(
    "taxonomy",
    "wp_pattern_category",
    {
      per_page: -1,
      _fields: "id,name,description,slug",
      context: "view"
    }
  );
  const mappedPatternCategories = patternCategories?.map((userCategory) => ({
    ...userCategory,
    label: (0,external_wp_htmlEntities_.decodeEntities)(userCategory.name),
    name: userCategory.slug
  })) || [];
  dispatch({
    type: "RECEIVE_USER_PATTERN_CATEGORIES",
    patternCategories: mappedPatternCategories
  });
};
const getNavigationFallbackId = () => async ({ dispatch, select, registry }) => {
  const fallback = await external_wp_apiFetch_default()({
    path: (0,external_wp_url_.addQueryArgs)("/wp-block-editor/v1/navigation-fallback", {
      _embed: true
    })
  });
  const record = fallback?._embedded?.self;
  registry.batch(() => {
    dispatch.receiveNavigationFallbackId(fallback?.id);
    if (!record) {
      return;
    }
    const existingFallbackEntityRecord = select.getEntityRecord(
      "postType",
      "wp_navigation",
      fallback.id
    );
    const invalidateNavigationQueries = !existingFallbackEntityRecord;
    dispatch.receiveEntityRecords(
      "postType",
      "wp_navigation",
      record,
      void 0,
      invalidateNavigationQueries
    );
    dispatch.finishResolution("getEntityRecord", [
      "postType",
      "wp_navigation",
      fallback.id
    ]);
  });
};
const getDefaultTemplateId = (query) => async ({ dispatch, registry, resolveSelect }) => {
  const template = await external_wp_apiFetch_default()({
    path: (0,external_wp_url_.addQueryArgs)("/wp/v2/templates/lookup", query)
  });
  await resolveSelect.getEntitiesConfig("postType");
  if (template?.id) {
    registry.batch(() => {
      dispatch.receiveDefaultTemplateId(query, template.id);
      dispatch.receiveEntityRecords("postType", "wp_template", [
        template
      ]);
      dispatch.finishResolution("getEntityRecord", [
        "postType",
        "wp_template",
        template.id
      ]);
    });
  }
};
const getRevisions = (kind, name, recordKey, query = {}) => async ({ dispatch, registry, resolveSelect }) => {
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.name === name && config.kind === kind
  );
  if (!entityConfig) {
    return;
  }
  if (query._fields) {
    query = {
      ...query,
      _fields: [
        .../* @__PURE__ */ new Set([
          ...(0,get_normalized_comma_separable/* default */.A)(query._fields) || [],
          entityConfig.revisionKey || entities/* DEFAULT_ENTITY_KEY */.C_
        ])
      ].join()
    };
  }
  const path = (0,external_wp_url_.addQueryArgs)(
    entityConfig.getRevisionsUrl(recordKey),
    query
  );
  let records, response;
  const meta = {};
  const isPaginated = entityConfig.supportsPagination && query.per_page !== -1;
  try {
    response = await external_wp_apiFetch_default()({ path, parse: !isPaginated });
  } catch (error) {
    return;
  }
  if (response) {
    if (isPaginated) {
      records = Object.values(await response.json());
      meta.totalItems = parseInt(
        response.headers.get("X-WP-Total")
      );
    } else {
      records = Object.values(response);
    }
    if (query._fields) {
      records = records.map((record) => {
        query._fields.split(",").forEach((field) => {
          if (!record.hasOwnProperty(field)) {
            record[field] = void 0;
          }
        });
        return record;
      });
    }
    registry.batch(() => {
      dispatch.receiveRevisions(
        kind,
        name,
        recordKey,
        records,
        query,
        false,
        meta
      );
      if (!query?._fields && !query.context) {
        const key = entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_;
        const resolutionsArgs = records.filter((record) => record[key]).map((record) => [
          kind,
          name,
          recordKey,
          record[key]
        ]);
        dispatch.finishResolutions(
          "getRevision",
          resolutionsArgs
        );
      }
    });
  }
};
getRevisions.shouldInvalidate = (action, kind, name, recordKey) => action.type === "SAVE_ENTITY_RECORD_FINISH" && name === action.name && kind === action.kind && !action.error && recordKey === action.recordId;
const getRevision = (kind, name, recordKey, revisionKey, query) => async ({ dispatch, resolveSelect }) => {
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.name === name && config.kind === kind
  );
  if (!entityConfig) {
    return;
  }
  if (query !== void 0 && query._fields) {
    query = {
      ...query,
      _fields: [
        .../* @__PURE__ */ new Set([
          ...(0,get_normalized_comma_separable/* default */.A)(query._fields) || [],
          entityConfig.revisionKey || entities/* DEFAULT_ENTITY_KEY */.C_
        ])
      ].join()
    };
  }
  const path = (0,external_wp_url_.addQueryArgs)(
    entityConfig.getRevisionsUrl(recordKey, revisionKey),
    query
  );
  let record;
  try {
    record = await external_wp_apiFetch_default()({ path });
  } catch (error) {
    return;
  }
  if (record) {
    dispatch.receiveRevisions(kind, name, recordKey, record, query);
  }
};
const getRegisteredPostMeta = (postType) => async ({ dispatch, resolveSelect }) => {
  let options;
  try {
    const {
      rest_namespace: restNamespace = "wp/v2",
      rest_base: restBase
    } = await resolveSelect.getPostType(postType) || {};
    options = await external_wp_apiFetch_default()({
      path: `${restNamespace}/${restBase}/?context=edit`,
      method: "OPTIONS"
    });
  } catch (error) {
    return;
  }
  if (options) {
    dispatch.receiveRegisteredPostMeta(
      postType,
      options?.schema?.properties?.meta?.properties
    );
  }
};
const getEntitiesConfig = (kind) => async ({ dispatch }) => {
  const loader = entities/* additionalEntityConfigLoaders */.L2.find(
    (l) => l.kind === kind
  );
  if (!loader) {
    return;
  }
  try {
    const configs = await loader.loadEntities();
    if (!configs.length) {
      return;
    }
    dispatch.addEntities(configs);
  } catch {
  }
};



/***/ }),

/***/ 7006:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  Y3: () => (/* reexport */ fetchLinkSuggestions),
  gr: () => (/* reexport */ experimental_fetch_url_data_default),
  l$: () => (/* binding */ fetchBlockPatterns)
});

// EXTERNAL MODULE: ./node_modules/camel-case/dist.es2015/index.js
var dist_es2015 = __webpack_require__(5663);
// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__(1455);
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);
// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__(3832);
// EXTERNAL MODULE: external ["wp","htmlEntities"]
var external_wp_htmlEntities_ = __webpack_require__(8537);
// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__(7723);
;// ./node_modules/@wordpress/core-data/build-module/fetch/__experimental-fetch-link-suggestions.js




async function fetchLinkSuggestions(search, searchOptions = {}, editorSettings = {}) {
  const searchOptionsToUse = searchOptions.isInitialSuggestions && searchOptions.initialSuggestionsSearchOptions ? {
    ...searchOptions,
    ...searchOptions.initialSuggestionsSearchOptions
  } : searchOptions;
  const {
    type,
    subtype,
    page,
    perPage = searchOptions.isInitialSuggestions ? 3 : 20
  } = searchOptionsToUse;
  const { disablePostFormats = false } = editorSettings;
  const queries = [];
  if (!type || type === "post") {
    queries.push(
      external_wp_apiFetch_default()({
        path: (0,external_wp_url_.addQueryArgs)("/wp/v2/search", {
          search,
          page,
          per_page: perPage,
          type: "post",
          subtype
        })
      }).then((results2) => {
        return results2.map((result) => {
          return {
            id: result.id,
            url: result.url,
            title: (0,external_wp_htmlEntities_.decodeEntities)(result.title || "") || (0,external_wp_i18n_.__)("(no title)"),
            type: result.subtype || result.type,
            kind: "post-type"
          };
        });
      }).catch(() => [])
      // Fail by returning no results.
    );
  }
  if (!type || type === "term") {
    queries.push(
      external_wp_apiFetch_default()({
        path: (0,external_wp_url_.addQueryArgs)("/wp/v2/search", {
          search,
          page,
          per_page: perPage,
          type: "term",
          subtype
        })
      }).then((results2) => {
        return results2.map((result) => {
          return {
            id: result.id,
            url: result.url,
            title: (0,external_wp_htmlEntities_.decodeEntities)(result.title || "") || (0,external_wp_i18n_.__)("(no title)"),
            type: result.subtype || result.type,
            kind: "taxonomy"
          };
        });
      }).catch(() => [])
      // Fail by returning no results.
    );
  }
  if (!disablePostFormats && (!type || type === "post-format")) {
    queries.push(
      external_wp_apiFetch_default()({
        path: (0,external_wp_url_.addQueryArgs)("/wp/v2/search", {
          search,
          page,
          per_page: perPage,
          type: "post-format",
          subtype
        })
      }).then((results2) => {
        return results2.map((result) => {
          return {
            id: result.id,
            url: result.url,
            title: (0,external_wp_htmlEntities_.decodeEntities)(result.title || "") || (0,external_wp_i18n_.__)("(no title)"),
            type: result.subtype || result.type,
            kind: "taxonomy"
          };
        });
      }).catch(() => [])
      // Fail by returning no results.
    );
  }
  if (!type || type === "attachment") {
    queries.push(
      external_wp_apiFetch_default()({
        path: (0,external_wp_url_.addQueryArgs)("/wp/v2/media", {
          search,
          page,
          per_page: perPage
        })
      }).then((results2) => {
        return results2.map((result) => {
          return {
            id: result.id,
            url: result.source_url,
            title: (0,external_wp_htmlEntities_.decodeEntities)(result.title.rendered || "") || (0,external_wp_i18n_.__)("(no title)"),
            type: result.type,
            kind: "media"
          };
        });
      }).catch(() => [])
      // Fail by returning no results.
    );
  }
  const responses = await Promise.all(queries);
  let results = responses.flat();
  results = results.filter((result) => !!result.id);
  results = sortResults(results, search);
  results = results.slice(0, perPage);
  return results;
}
function sortResults(results, search) {
  const searchTokens = tokenize(search);
  const scores = {};
  for (const result of results) {
    if (result.title) {
      const titleTokens = tokenize(result.title);
      const exactMatchingTokens = titleTokens.filter(
        (titleToken) => searchTokens.some(
          (searchToken) => titleToken === searchToken
        )
      );
      const subMatchingTokens = titleTokens.filter(
        (titleToken) => searchTokens.some(
          (searchToken) => titleToken !== searchToken && titleToken.includes(searchToken)
        )
      );
      const exactMatchScore = exactMatchingTokens.length / titleTokens.length * 10;
      const subMatchScore = subMatchingTokens.length / titleTokens.length;
      scores[result.id] = exactMatchScore + subMatchScore;
    } else {
      scores[result.id] = 0;
    }
  }
  return results.sort((a, b) => scores[b.id] - scores[a.id]);
}
function tokenize(text) {
  return text.toLowerCase().match(/[\p{L}\p{N}]+/gu) || [];
}


;// ./node_modules/@wordpress/core-data/build-module/fetch/__experimental-fetch-url-data.js


const CACHE = /* @__PURE__ */ new Map();
const fetchUrlData = async (url, options = {}) => {
  const endpoint = "/wp-block-editor/v1/url-details";
  const args = {
    url: (0,external_wp_url_.prependHTTP)(url)
  };
  if (!(0,external_wp_url_.isURL)(url)) {
    return Promise.reject(`${url} is not a valid URL.`);
  }
  const protocol = (0,external_wp_url_.getProtocol)(url);
  if (!protocol || !(0,external_wp_url_.isValidProtocol)(protocol) || !protocol.startsWith("http") || !/^https?:\/\/[^\/\s]/i.test(url)) {
    return Promise.reject(
      `${url} does not have a valid protocol. URLs must be "http" based`
    );
  }
  if (CACHE.has(url)) {
    return CACHE.get(url);
  }
  return external_wp_apiFetch_default()({
    path: (0,external_wp_url_.addQueryArgs)(endpoint, args),
    ...options
  }).then((res) => {
    CACHE.set(url, res);
    return res;
  });
};
var experimental_fetch_url_data_default = fetchUrlData;


;// ./node_modules/@wordpress/core-data/build-module/fetch/index.js




async function fetchBlockPatterns() {
  const restPatterns = await external_wp_apiFetch_default()({
    path: "/wp/v2/block-patterns/patterns"
  });
  if (!restPatterns) {
    return [];
  }
  return restPatterns.map(
    (pattern) => Object.fromEntries(
      Object.entries(pattern).map(([key, value]) => [
        (0,dist_es2015/* camelCase */.xQ)(key),
        value
      ])
    )
  );
}



/***/ }),

/***/ 7078:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Ay: () => (/* binding */ useEntityRecords),
/* harmony export */   bM: () => (/* binding */ __experimentalUseEntityRecords),
/* harmony export */   pU: () => (/* binding */ useEntityRecordsWithPermissions)
/* harmony export */ });
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(3832);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(4040);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(7143);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(6087);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _use_query_select__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(7541);
/* harmony import */ var ___WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(4565);
/* harmony import */ var _lock_unlock__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(6378);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(533);








const EMPTY_ARRAY = [];
function useEntityRecords(kind, name, queryArgs = {}, options = { enabled: true }) {
  const queryAsString = (0,_wordpress_url__WEBPACK_IMPORTED_MODULE_0__.addQueryArgs)("", queryArgs);
  const { data: records, ...rest } = (0,_use_query_select__WEBPACK_IMPORTED_MODULE_5__/* ["default"] */ .A)(
    (query) => {
      if (!options.enabled) {
        return {
          // Avoiding returning a new reference on every execution.
          data: EMPTY_ARRAY
        };
      }
      return query(___WEBPACK_IMPORTED_MODULE_4__.store).getEntityRecords(kind, name, queryArgs);
    },
    [kind, name, queryAsString, options.enabled]
  );
  const { totalItems, totalPages } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(
    (select) => {
      if (!options.enabled) {
        return {
          totalItems: null,
          totalPages: null
        };
      }
      return {
        totalItems: select(___WEBPACK_IMPORTED_MODULE_4__.store).getEntityRecordsTotalItems(
          kind,
          name,
          queryArgs
        ),
        totalPages: select(___WEBPACK_IMPORTED_MODULE_4__.store).getEntityRecordsTotalPages(
          kind,
          name,
          queryArgs
        )
      };
    },
    [kind, name, queryAsString, options.enabled]
  );
  return {
    records,
    totalItems,
    totalPages,
    ...rest
  };
}
function __experimentalUseEntityRecords(kind, name, queryArgs, options) {
  _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1___default()(`wp.data.__experimentalUseEntityRecords`, {
    alternative: "wp.data.useEntityRecords",
    since: "6.1"
  });
  return useEntityRecords(kind, name, queryArgs, options);
}
function useEntityRecordsWithPermissions(kind, name, queryArgs = {}, options = { enabled: true }) {
  const entityConfig = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(
    (select) => select(___WEBPACK_IMPORTED_MODULE_4__.store).getEntityConfig(kind, name),
    [kind, name]
  );
  const { records: data, ...ret } = useEntityRecords(
    kind,
    name,
    {
      ...queryArgs,
      // If _fields is provided, we need to include _links in the request for permission caching to work.
      ...queryArgs._fields ? {
        _fields: [
          .../* @__PURE__ */ new Set([
            ...(0,_utils__WEBPACK_IMPORTED_MODULE_6__/* ["default"] */ .A)(
              queryArgs._fields
            ) || [],
            "_links"
          ])
        ].join()
      } : {}
    },
    options
  );
  const ids = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useMemo)(
    () => data?.map(
      // @ts-ignore
      (record) => record[entityConfig?.key ?? "id"]
    ) ?? [],
    [data, entityConfig?.key]
  );
  const permissions = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(
    (select) => {
      const { getEntityRecordsPermissions } = (0,_lock_unlock__WEBPACK_IMPORTED_MODULE_7__/* .unlock */ .T)(
        select(___WEBPACK_IMPORTED_MODULE_4__.store)
      );
      return getEntityRecordsPermissions(kind, name, ids);
    },
    [ids, kind, name]
  );
  const dataWithPermissions = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useMemo)(
    () => data?.map((record, index) => ({
      // @ts-ignore
      ...record,
      permissions: permissions[index]
    })) ?? [],
    [data, permissions]
  );
  return { records: dataWithPermissions, ...ret };
}



/***/ }),

/***/ 7143:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["data"];

/***/ }),

/***/ 7314:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   g: () => (/* binding */ lowerCase)
/* harmony export */ });
/* unused harmony export localeLowerCase */
/**
 * Source: ftp://ftp.unicode.org/Public/UCD/latest/ucd/SpecialCasing.txt
 */
var SUPPORTED_LOCALE = {
    tr: {
        regexp: /\u0130|\u0049|\u0049\u0307/g,
        map: {
            İ: "\u0069",
            I: "\u0131",
            İ: "\u0069",
        },
    },
    az: {
        regexp: /\u0130/g,
        map: {
            İ: "\u0069",
            I: "\u0131",
            İ: "\u0069",
        },
    },
    lt: {
        regexp: /\u0049|\u004A|\u012E|\u00CC|\u00CD|\u0128/g,
        map: {
            I: "\u0069\u0307",
            J: "\u006A\u0307",
            Į: "\u012F\u0307",
            Ì: "\u0069\u0307\u0300",
            Í: "\u0069\u0307\u0301",
            Ĩ: "\u0069\u0307\u0303",
        },
    },
};
/**
 * Localized lower case.
 */
function localeLowerCase(str, locale) {
    var lang = SUPPORTED_LOCALE[locale.toLowerCase()];
    if (lang)
        return lowerCase(str.replace(lang.regexp, function (m) { return lang.map[m]; }));
    return lowerCase(str);
}
/**
 * Lower case as a function.
 */
function lowerCase(str) {
    return str.toLowerCase();
}


/***/ }),

/***/ 7541:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  A: () => (/* binding */ useQuerySelect)
});

// UNUSED EXPORTS: META_SELECTORS

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__(7143);
;// ./node_modules/memize/dist/index.js
/**
 * Memize options object.
 *
 * @typedef MemizeOptions
 *
 * @property {number} [maxSize] Maximum size of the cache.
 */

/**
 * Internal cache entry.
 *
 * @typedef MemizeCacheNode
 *
 * @property {?MemizeCacheNode|undefined} [prev] Previous node.
 * @property {?MemizeCacheNode|undefined} [next] Next node.
 * @property {Array<*>}                   args   Function arguments for cache
 *                                               entry.
 * @property {*}                          val    Function result.
 */

/**
 * Properties of the enhanced function for controlling cache.
 *
 * @typedef MemizeMemoizedFunction
 *
 * @property {()=>void} clear Clear the cache.
 */

/**
 * Accepts a function to be memoized, and returns a new memoized function, with
 * optional options.
 *
 * @template {(...args: any[]) => any} F
 *
 * @param {F}             fn        Function to memoize.
 * @param {MemizeOptions} [options] Options object.
 *
 * @return {((...args: Parameters<F>) => ReturnType<F>) & MemizeMemoizedFunction} Memoized function.
 */
function memize(fn, options) {
	var size = 0;

	/** @type {?MemizeCacheNode|undefined} */
	var head;

	/** @type {?MemizeCacheNode|undefined} */
	var tail;

	options = options || {};

	function memoized(/* ...args */) {
		var node = head,
			len = arguments.length,
			args,
			i;

		searchCache: while (node) {
			// Perform a shallow equality test to confirm that whether the node
			// under test is a candidate for the arguments passed. Two arrays
			// are shallowly equal if their length matches and each entry is
			// strictly equal between the two sets. Avoid abstracting to a
			// function which could incur an arguments leaking deoptimization.

			// Check whether node arguments match arguments length
			if (node.args.length !== arguments.length) {
				node = node.next;
				continue;
			}

			// Check whether node arguments match arguments values
			for (i = 0; i < len; i++) {
				if (node.args[i] !== arguments[i]) {
					node = node.next;
					continue searchCache;
				}
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if (node !== head) {
				// As tail, shift to previous. Must only shift if not also
				// head, since if both head and tail, there is no previous.
				if (node === tail) {
					tail = node.prev;
				}

				// Adjust siblings to point to each other. If node was tail,
				// this also handles new tail's empty `next` assignment.
				/** @type {MemizeCacheNode} */ (node.prev).next = node.next;
				if (node.next) {
					node.next.prev = node.prev;
				}

				node.next = head;
				node.prev = null;
				/** @type {MemizeCacheNode} */ (head).prev = node;
				head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		// Create a copy of arguments (avoid leaking deoptimization)
		args = new Array(len);
		for (i = 0; i < len; i++) {
			args[i] = arguments[i];
		}

		node = {
			args: args,

			// Generate the result from original function
			val: fn.apply(null, args),
		};

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if (head) {
			head.prev = node;
			node.next = head;
		} else {
			// If no head, follows that there's no tail (at initial or reset)
			tail = node;
		}

		// Trim tail if we're reached max size and are pending cache insertion
		if (size === /** @type {MemizeOptions} */ (options).maxSize) {
			tail = /** @type {MemizeCacheNode} */ (tail).prev;
			/** @type {MemizeCacheNode} */ (tail).next = null;
		} else {
			size++;
		}

		head = node;

		return node.val;
	}

	memoized.clear = function () {
		head = null;
		tail = null;
		size = 0;
	};

	// Ignore reason: There's not a clear solution to create an intersection of
	// the function with additional properties, where the goal is to retain the
	// function signature of the incoming argument and add control properties
	// on the return value.

	// @ts-ignore
	return memoized;
}



;// ./node_modules/@wordpress/core-data/build-module/hooks/memoize.js

var memoize_default = memize;


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/hooks/constants.js
var constants = __webpack_require__(2859);
;// ./node_modules/@wordpress/core-data/build-module/hooks/use-query-select.js



const META_SELECTORS = [
  "getIsResolving",
  "hasStartedResolution",
  "hasFinishedResolution",
  "isResolving",
  "getCachedResolvers"
];
function useQuerySelect(mapQuerySelect, deps) {
  return (0,external_wp_data_.useSelect)((select, registry) => {
    const resolve = (store) => enrichSelectors(select(store));
    return mapQuerySelect(resolve, registry);
  }, deps);
}
const enrichSelectors = memoize_default(((selectors) => {
  const resolvers = {};
  for (const selectorName in selectors) {
    if (META_SELECTORS.includes(selectorName)) {
      continue;
    }
    Object.defineProperty(resolvers, selectorName, {
      get: () => (...args) => {
        const data = selectors[selectorName](...args);
        const resolutionStatus = selectors.getResolutionState(
          selectorName,
          args
        )?.status;
        let status;
        switch (resolutionStatus) {
          case "resolving":
            status = constants/* Status */.n.Resolving;
            break;
          case "finished":
            status = constants/* Status */.n.Success;
            break;
          case "error":
            status = constants/* Status */.n.Error;
            break;
          case void 0:
            status = constants/* Status */.n.Idle;
            break;
        }
        return {
          data,
          status,
          isResolving: status === constants/* Status */.n.Resolving,
          hasStarted: status !== constants/* Status */.n.Idle,
          hasResolved: status === constants/* Status */.n.Success || status === constants/* Status */.n.Error
        };
      }
    });
  }
  return resolvers;
}));



/***/ }),

/***/ 7723:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["i18n"];

/***/ }),

/***/ 7734:
/***/ ((module) => {

"use strict";


// do not edit .js files directly - edit src/index.jst


  var envHasBigInt64Array = typeof BigInt64Array !== 'undefined';


module.exports = function equal(a, b) {
  if (a === b) return true;

  if (a && b && typeof a == 'object' && typeof b == 'object') {
    if (a.constructor !== b.constructor) return false;

    var length, i, keys;
    if (Array.isArray(a)) {
      length = a.length;
      if (length != b.length) return false;
      for (i = length; i-- !== 0;)
        if (!equal(a[i], b[i])) return false;
      return true;
    }


    if ((a instanceof Map) && (b instanceof Map)) {
      if (a.size !== b.size) return false;
      for (i of a.entries())
        if (!b.has(i[0])) return false;
      for (i of a.entries())
        if (!equal(i[1], b.get(i[0]))) return false;
      return true;
    }

    if ((a instanceof Set) && (b instanceof Set)) {
      if (a.size !== b.size) return false;
      for (i of a.entries())
        if (!b.has(i[0])) return false;
      return true;
    }

    if (ArrayBuffer.isView(a) && ArrayBuffer.isView(b)) {
      length = a.length;
      if (length != b.length) return false;
      for (i = length; i-- !== 0;)
        if (a[i] !== b[i]) return false;
      return true;
    }


    if (a.constructor === RegExp) return a.source === b.source && a.flags === b.flags;
    if (a.valueOf !== Object.prototype.valueOf) return a.valueOf() === b.valueOf();
    if (a.toString !== Object.prototype.toString) return a.toString() === b.toString();

    keys = Object.keys(a);
    length = keys.length;
    if (length !== Object.keys(b).length) return false;

    for (i = length; i-- !== 0;)
      if (!Object.prototype.hasOwnProperty.call(b, keys[i])) return false;

    for (i = length; i-- !== 0;) {
      var key = keys[i];

      if (!equal(a[key], b[key])) return false;
    }

    return true;
  }

  // true if both NaN, false otherwise
  return a!==a && b!==b;
};


/***/ }),

/***/ 7826:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   j: () => (/* binding */ privateApis)
/* harmony export */ });
/* harmony import */ var _hooks_use_entity_records__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(7078);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(5101);
/* harmony import */ var _lock_unlock__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(6378);



const privateApis = {};
(0,_lock_unlock__WEBPACK_IMPORTED_MODULE_0__/* .lock */ .s)(privateApis, {
  useEntityRecordsWithPermissions: _hooks_use_entity_records__WEBPACK_IMPORTED_MODULE_1__/* .useEntityRecordsWithPermissions */ .pU,
  RECEIVE_INTERMEDIATE_RESULTS: _utils__WEBPACK_IMPORTED_MODULE_2__/* .RECEIVE_INTERMEDIATE_RESULTS */ .Z
});



/***/ }),

/***/ 8368:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  __experimentalGetCurrentGlobalStylesId: () => (/* binding */ __experimentalGetCurrentGlobalStylesId),
  __experimentalGetCurrentThemeBaseGlobalStyles: () => (/* binding */ __experimentalGetCurrentThemeBaseGlobalStyles),
  __experimentalGetCurrentThemeGlobalStylesVariations: () => (/* binding */ __experimentalGetCurrentThemeGlobalStylesVariations),
  __experimentalGetDirtyEntityRecords: () => (/* binding */ __experimentalGetDirtyEntityRecords),
  __experimentalGetEntitiesBeingSaved: () => (/* binding */ __experimentalGetEntitiesBeingSaved),
  __experimentalGetEntityRecordNoResolver: () => (/* binding */ __experimentalGetEntityRecordNoResolver),
  canUser: () => (/* binding */ canUser),
  canUserEditEntityRecord: () => (/* binding */ canUserEditEntityRecord),
  getAuthors: () => (/* binding */ getAuthors),
  getAutosave: () => (/* binding */ getAutosave),
  getAutosaves: () => (/* binding */ getAutosaves),
  getBlockPatternCategories: () => (/* binding */ getBlockPatternCategories),
  getBlockPatterns: () => (/* binding */ getBlockPatterns),
  getCurrentTheme: () => (/* binding */ getCurrentTheme),
  getCurrentThemeGlobalStylesRevisions: () => (/* binding */ getCurrentThemeGlobalStylesRevisions),
  getCurrentUser: () => (/* binding */ getCurrentUser),
  getDefaultTemplateId: () => (/* binding */ getDefaultTemplateId),
  getEditedEntityRecord: () => (/* binding */ getEditedEntityRecord),
  getEmbedPreview: () => (/* binding */ getEmbedPreview),
  getEntitiesByKind: () => (/* binding */ getEntitiesByKind),
  getEntitiesConfig: () => (/* binding */ getEntitiesConfig),
  getEntity: () => (/* binding */ getEntity),
  getEntityConfig: () => (/* binding */ getEntityConfig),
  getEntityRecord: () => (/* binding */ getEntityRecord),
  getEntityRecordEdits: () => (/* binding */ getEntityRecordEdits),
  getEntityRecordNonTransientEdits: () => (/* binding */ getEntityRecordNonTransientEdits),
  getEntityRecords: () => (/* binding */ getEntityRecords),
  getEntityRecordsTotalItems: () => (/* binding */ getEntityRecordsTotalItems),
  getEntityRecordsTotalPages: () => (/* binding */ getEntityRecordsTotalPages),
  getLastEntityDeleteError: () => (/* binding */ getLastEntityDeleteError),
  getLastEntitySaveError: () => (/* binding */ getLastEntitySaveError),
  getRawEntityRecord: () => (/* binding */ getRawEntityRecord),
  getRedoEdit: () => (/* binding */ getRedoEdit),
  getReferenceByDistinctEdits: () => (/* binding */ getReferenceByDistinctEdits),
  getRevision: () => (/* binding */ getRevision),
  getRevisions: () => (/* binding */ getRevisions),
  getThemeSupports: () => (/* binding */ getThemeSupports),
  getUndoEdit: () => (/* binding */ getUndoEdit),
  getUserPatternCategories: () => (/* binding */ getUserPatternCategories),
  getUserQueryResults: () => (/* binding */ getUserQueryResults),
  hasEditsForEntityRecord: () => (/* binding */ hasEditsForEntityRecord),
  hasEntityRecord: () => (/* binding */ hasEntityRecord),
  hasEntityRecords: () => (/* binding */ hasEntityRecords),
  hasFetchedAutosaves: () => (/* binding */ hasFetchedAutosaves),
  hasRedo: () => (/* binding */ hasRedo),
  hasUndo: () => (/* binding */ hasUndo),
  isAutosavingEntityRecord: () => (/* binding */ isAutosavingEntityRecord),
  isDeletingEntityRecord: () => (/* binding */ isDeletingEntityRecord),
  isPreviewEmbedFallback: () => (/* binding */ isPreviewEmbedFallback),
  isRequestingEmbedPreview: () => (/* binding */ isRequestingEmbedPreview),
  isSavingEntityRecord: () => (/* binding */ isSavingEntityRecord)
});

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__(7143);
// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__(3832);
// EXTERNAL MODULE: external ["wp","deprecated"]
var external_wp_deprecated_ = __webpack_require__(4040);
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/name.js
var build_module_name = __webpack_require__(2278);
// EXTERNAL MODULE: ./node_modules/equivalent-key-map/equivalent-key-map.js
var equivalent_key_map = __webpack_require__(3249);
var equivalent_key_map_default = /*#__PURE__*/__webpack_require__.n(equivalent_key_map);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/queried-data/get-query-parts.js + 1 modules
var get_query_parts = __webpack_require__(4027);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/set-nested-value.js
var set_nested_value = __webpack_require__(5003);
;// ./node_modules/@wordpress/core-data/build-module/queried-data/selectors.js




const queriedItemsCacheByState = /* @__PURE__ */ new WeakMap();
function getQueriedItemsUncached(state, query) {
  const { stableKey, page, perPage, include, fields, context } = (0,get_query_parts/* default */.A)(query);
  let itemIds;
  if (state.queries?.[context]?.[stableKey]) {
    itemIds = state.queries[context][stableKey].itemIds;
  }
  if (!itemIds) {
    return null;
  }
  const startOffset = perPage === -1 ? 0 : (page - 1) * perPage;
  const endOffset = perPage === -1 ? itemIds.length : Math.min(startOffset + perPage, itemIds.length);
  const items = [];
  for (let i = startOffset; i < endOffset; i++) {
    const itemId = itemIds[i];
    if (Array.isArray(include) && !include.includes(itemId)) {
      continue;
    }
    if (itemId === void 0) {
      continue;
    }
    if (!state.items[context]?.hasOwnProperty(itemId)) {
      return null;
    }
    const item = state.items[context][itemId];
    let filteredItem;
    if (Array.isArray(fields)) {
      filteredItem = {};
      for (let f = 0; f < fields.length; f++) {
        const field = fields[f].split(".");
        let value = item;
        field.forEach((fieldName) => {
          value = value?.[fieldName];
        });
        (0,set_nested_value/* default */.A)(filteredItem, field, value);
      }
    } else {
      if (!state.itemIsComplete[context]?.[itemId]) {
        return null;
      }
      filteredItem = item;
    }
    items.push(filteredItem);
  }
  return items;
}
const getQueriedItems = (0,external_wp_data_.createSelector)((state, query = {}) => {
  let queriedItemsCache = queriedItemsCacheByState.get(state);
  if (queriedItemsCache) {
    const queriedItems = queriedItemsCache.get(query);
    if (queriedItems !== void 0) {
      return queriedItems;
    }
  } else {
    queriedItemsCache = new (equivalent_key_map_default())();
    queriedItemsCacheByState.set(state, queriedItemsCache);
  }
  const items = getQueriedItemsUncached(state, query);
  queriedItemsCache.set(query, items);
  return items;
});
function getQueriedTotalItems(state, query = {}) {
  const { stableKey, context } = (0,get_query_parts/* default */.A)(query);
  return state.queries?.[context]?.[stableKey]?.meta?.totalItems ?? null;
}
function getQueriedTotalPages(state, query = {}) {
  const { stableKey, context } = (0,get_query_parts/* default */.A)(query);
  return state.queries?.[context]?.[stableKey]?.meta?.totalPages ?? null;
}


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/entities.js + 2 modules
var entities = __webpack_require__(5914);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/get-normalized-comma-separable.js
var get_normalized_comma_separable = __webpack_require__(533);
;// ./node_modules/@wordpress/core-data/build-module/utils/is-numeric-id.js
function isNumericID(id) {
  return /^\s*\d+\s*$/.test(id);
}


;// ./node_modules/@wordpress/core-data/build-module/utils/is-raw-attribute.js
function isRawAttribute(entity, attribute) {
  return (entity.rawAttributes || []).includes(attribute);
}


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/user-permissions.js
var user_permissions = __webpack_require__(2577);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/log-entity-deprecation.js
var log_entity_deprecation = __webpack_require__(9410);
;// ./node_modules/@wordpress/core-data/build-module/selectors.js








const EMPTY_OBJECT = {};
const isRequestingEmbedPreview = (0,external_wp_data_.createRegistrySelector)(
  (select) => (state, url) => {
    return select(build_module_name/* STORE_NAME */.E).isResolving("getEmbedPreview", [
      url
    ]);
  }
);
function getAuthors(state, query) {
  external_wp_deprecated_default()("select( 'core' ).getAuthors()", {
    since: "5.9",
    alternative: "select( 'core' ).getUsers({ who: 'authors' })"
  });
  const path = (0,external_wp_url_.addQueryArgs)(
    "/wp/v2/users/?who=authors&per_page=100",
    query
  );
  return getUserQueryResults(state, path);
}
function getCurrentUser(state) {
  return state.currentUser;
}
const getUserQueryResults = (0,external_wp_data_.createSelector)(
  (state, queryID) => {
    const queryResults = state.users.queries[queryID] ?? [];
    return queryResults.map((id) => state.users.byId[id]);
  },
  (state, queryID) => [
    state.users.queries[queryID],
    state.users.byId
  ]
);
function getEntitiesByKind(state, kind) {
  external_wp_deprecated_default()("wp.data.select( 'core' ).getEntitiesByKind()", {
    since: "6.0",
    alternative: "wp.data.select( 'core' ).getEntitiesConfig()"
  });
  return getEntitiesConfig(state, kind);
}
const getEntitiesConfig = (0,external_wp_data_.createSelector)(
  (state, kind) => state.entities.config.filter((entity) => entity.kind === kind),
  /* eslint-disable @typescript-eslint/no-unused-vars */
  (state, kind) => state.entities.config
  /* eslint-enable @typescript-eslint/no-unused-vars */
);
function getEntity(state, kind, name) {
  external_wp_deprecated_default()("wp.data.select( 'core' ).getEntity()", {
    since: "6.0",
    alternative: "wp.data.select( 'core' ).getEntityConfig()"
  });
  return getEntityConfig(state, kind, name);
}
function getEntityConfig(state, kind, name) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityConfig");
  return state.entities.config?.find(
    (config) => config.kind === kind && config.name === name
  );
}
const getEntityRecord = (0,external_wp_data_.createSelector)(
  ((state, kind, name, key, query) => {
    (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityRecord");
    const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
    if (!queriedState) {
      return void 0;
    }
    const context = query?.context ?? "default";
    if (!query || !query._fields) {
      if (!queriedState.itemIsComplete[context]?.[key]) {
        return void 0;
      }
      return queriedState.items[context][key];
    }
    const item = queriedState.items[context]?.[key];
    if (!item) {
      return item;
    }
    const filteredItem = {};
    const fields = (0,get_normalized_comma_separable/* default */.A)(query._fields) ?? [];
    for (let f = 0; f < fields.length; f++) {
      const field = fields[f].split(".");
      let value = item;
      field.forEach((fieldName) => {
        value = value?.[fieldName];
      });
      (0,set_nested_value/* default */.A)(filteredItem, field, value);
    }
    return filteredItem;
  }),
  (state, kind, name, recordId, query) => {
    const context = query?.context ?? "default";
    const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
    return [
      queriedState?.items[context]?.[recordId],
      queriedState?.itemIsComplete[context]?.[recordId]
    ];
  }
);
getEntityRecord.__unstableNormalizeArgs = (args) => {
  const newArgs = [...args];
  const recordKey = newArgs?.[2];
  newArgs[2] = isNumericID(recordKey) ? Number(recordKey) : recordKey;
  return newArgs;
};
function hasEntityRecord(state, kind, name, key, query) {
  const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
  if (!queriedState) {
    return false;
  }
  const context = query?.context ?? "default";
  if (!query || !query._fields) {
    return !!queriedState.itemIsComplete[context]?.[key];
  }
  const item = queriedState.items[context]?.[key];
  if (!item) {
    return false;
  }
  const fields = (0,get_normalized_comma_separable/* default */.A)(query._fields) ?? [];
  for (let i = 0; i < fields.length; i++) {
    const path = fields[i].split(".");
    let value = item;
    for (let p = 0; p < path.length; p++) {
      const part = path[p];
      if (!value || !Object.hasOwn(value, part)) {
        return false;
      }
      value = value[part];
    }
  }
  return true;
}
function __experimentalGetEntityRecordNoResolver(state, kind, name, key) {
  return getEntityRecord(state, kind, name, key);
}
const getRawEntityRecord = (0,external_wp_data_.createSelector)(
  (state, kind, name, key) => {
    (0,log_entity_deprecation/* default */.A)(kind, name, "getRawEntityRecord");
    const record = getEntityRecord(
      state,
      kind,
      name,
      key
    );
    return record && Object.keys(record).reduce((accumulator, _key) => {
      if (isRawAttribute(getEntityConfig(state, kind, name), _key)) {
        accumulator[_key] = record[_key]?.raw !== void 0 ? record[_key]?.raw : record[_key];
      } else {
        accumulator[_key] = record[_key];
      }
      return accumulator;
    }, {});
  },
  (state, kind, name, recordId, query) => {
    const context = query?.context ?? "default";
    return [
      state.entities.config,
      state.entities.records?.[kind]?.[name]?.queriedData?.items[context]?.[recordId],
      state.entities.records?.[kind]?.[name]?.queriedData?.itemIsComplete[context]?.[recordId]
    ];
  }
);
function hasEntityRecords(state, kind, name, query) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "hasEntityRecords");
  return Array.isArray(getEntityRecords(state, kind, name, query));
}
const getEntityRecords = ((state, kind, name, query) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityRecords");
  const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
  if (!queriedState) {
    return null;
  }
  return getQueriedItems(queriedState, query);
});
const getEntityRecordsTotalItems = (state, kind, name, query) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityRecordsTotalItems");
  const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
  if (!queriedState) {
    return null;
  }
  return getQueriedTotalItems(queriedState, query);
};
const getEntityRecordsTotalPages = (state, kind, name, query) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityRecordsTotalPages");
  const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
  if (!queriedState) {
    return null;
  }
  if (query?.per_page === -1) {
    return 1;
  }
  const totalItems = getQueriedTotalItems(queriedState, query);
  if (!totalItems) {
    return totalItems;
  }
  if (!query?.per_page) {
    return getQueriedTotalPages(queriedState, query);
  }
  return Math.ceil(totalItems / query.per_page);
};
const __experimentalGetDirtyEntityRecords = (0,external_wp_data_.createSelector)(
  (state) => {
    const {
      entities: { records }
    } = state;
    const dirtyRecords = [];
    Object.keys(records).forEach((kind) => {
      Object.keys(records[kind]).forEach((name) => {
        const primaryKeys = Object.keys(records[kind][name].edits).filter(
          (primaryKey) => (
            // The entity record must exist (not be deleted),
            // and it must have edits.
            getEntityRecord(state, kind, name, primaryKey) && hasEditsForEntityRecord(state, kind, name, primaryKey)
          )
        );
        if (primaryKeys.length) {
          const entityConfig = getEntityConfig(state, kind, name);
          primaryKeys.forEach((primaryKey) => {
            const entityRecord = getEditedEntityRecord(
              state,
              kind,
              name,
              primaryKey
            );
            dirtyRecords.push({
              // We avoid using primaryKey because it's transformed into a string
              // when it's used as an object key.
              key: entityRecord ? entityRecord[entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_] : void 0,
              title: entityConfig?.getTitle?.(entityRecord) || "",
              name,
              kind
            });
          });
        }
      });
    });
    return dirtyRecords;
  },
  (state) => [state.entities.records]
);
const __experimentalGetEntitiesBeingSaved = (0,external_wp_data_.createSelector)(
  (state) => {
    const {
      entities: { records }
    } = state;
    const recordsBeingSaved = [];
    Object.keys(records).forEach((kind) => {
      Object.keys(records[kind]).forEach((name) => {
        const primaryKeys = Object.keys(records[kind][name].saving).filter(
          (primaryKey) => isSavingEntityRecord(state, kind, name, primaryKey)
        );
        if (primaryKeys.length) {
          const entityConfig = getEntityConfig(state, kind, name);
          primaryKeys.forEach((primaryKey) => {
            const entityRecord = getEditedEntityRecord(
              state,
              kind,
              name,
              primaryKey
            );
            recordsBeingSaved.push({
              // We avoid using primaryKey because it's transformed into a string
              // when it's used as an object key.
              key: entityRecord ? entityRecord[entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_] : void 0,
              title: entityConfig?.getTitle?.(entityRecord) || "",
              name,
              kind
            });
          });
        }
      });
    });
    return recordsBeingSaved;
  },
  (state) => [state.entities.records]
);
function getEntityRecordEdits(state, kind, name, recordId) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityRecordEdits");
  return state.entities.records?.[kind]?.[name]?.edits?.[recordId];
}
const getEntityRecordNonTransientEdits = (0,external_wp_data_.createSelector)(
  (state, kind, name, recordId) => {
    (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityRecordNonTransientEdits");
    const { transientEdits } = getEntityConfig(state, kind, name) || {};
    const edits = getEntityRecordEdits(state, kind, name, recordId) || {};
    if (!transientEdits) {
      return edits;
    }
    return Object.keys(edits).reduce((acc, key) => {
      if (!transientEdits[key]) {
        acc[key] = edits[key];
      }
      return acc;
    }, {});
  },
  (state, kind, name, recordId) => [
    state.entities.config,
    state.entities.records?.[kind]?.[name]?.edits?.[recordId]
  ]
);
function hasEditsForEntityRecord(state, kind, name, recordId) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "hasEditsForEntityRecord");
  return isSavingEntityRecord(state, kind, name, recordId) || Object.keys(
    getEntityRecordNonTransientEdits(state, kind, name, recordId)
  ).length > 0;
}
const getEditedEntityRecord = (0,external_wp_data_.createSelector)(
  (state, kind, name, recordId) => {
    (0,log_entity_deprecation/* default */.A)(kind, name, "getEditedEntityRecord");
    const raw = getRawEntityRecord(state, kind, name, recordId);
    const edited = getEntityRecordEdits(state, kind, name, recordId);
    if (!raw && !edited) {
      return false;
    }
    return {
      ...raw,
      ...edited
    };
  },
  (state, kind, name, recordId, query) => {
    const context = query?.context ?? "default";
    return [
      state.entities.config,
      state.entities.records?.[kind]?.[name]?.queriedData.items[context]?.[recordId],
      state.entities.records?.[kind]?.[name]?.queriedData.itemIsComplete[context]?.[recordId],
      state.entities.records?.[kind]?.[name]?.edits?.[recordId]
    ];
  }
);
function isAutosavingEntityRecord(state, kind, name, recordId) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "isAutosavingEntityRecord");
  const { pending, isAutosave } = state.entities.records?.[kind]?.[name]?.saving?.[recordId] ?? {};
  return Boolean(pending && isAutosave);
}
function isSavingEntityRecord(state, kind, name, recordId) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "isSavingEntityRecord");
  return state.entities.records?.[kind]?.[name]?.saving?.[recordId]?.pending ?? false;
}
function isDeletingEntityRecord(state, kind, name, recordId) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "isDeletingEntityRecord");
  return state.entities.records?.[kind]?.[name]?.deleting?.[recordId]?.pending ?? false;
}
function getLastEntitySaveError(state, kind, name, recordId) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getLastEntitySaveError");
  return state.entities.records?.[kind]?.[name]?.saving?.[recordId]?.error;
}
function getLastEntityDeleteError(state, kind, name, recordId) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getLastEntityDeleteError");
  return state.entities.records?.[kind]?.[name]?.deleting?.[recordId]?.error;
}
function getUndoEdit(state) {
  external_wp_deprecated_default()("select( 'core' ).getUndoEdit()", {
    since: "6.3"
  });
  return void 0;
}
function getRedoEdit(state) {
  external_wp_deprecated_default()("select( 'core' ).getRedoEdit()", {
    since: "6.3"
  });
  return void 0;
}
function hasUndo(state) {
  return state.undoManager.hasUndo();
}
function hasRedo(state) {
  return state.undoManager.hasRedo();
}
function getCurrentTheme(state) {
  if (!state.currentTheme) {
    return null;
  }
  return getEntityRecord(state, "root", "theme", state.currentTheme);
}
function __experimentalGetCurrentGlobalStylesId(state) {
  return state.currentGlobalStylesId;
}
function getThemeSupports(state) {
  return getCurrentTheme(state)?.theme_supports ?? EMPTY_OBJECT;
}
function getEmbedPreview(state, url) {
  return state.embedPreviews[url];
}
function isPreviewEmbedFallback(state, url) {
  const preview = state.embedPreviews[url];
  const oEmbedLinkCheck = '<a href="' + url + '">' + url + "</a>";
  if (!preview) {
    return false;
  }
  return preview.html === oEmbedLinkCheck;
}
function canUser(state, action, resource, id) {
  const isEntity = typeof resource === "object";
  if (isEntity && (!resource.kind || !resource.name)) {
    return false;
  }
  if (isEntity) {
    (0,log_entity_deprecation/* default */.A)(resource.kind, resource.name, "canUser");
  }
  const key = (0,user_permissions/* getUserPermissionCacheKey */.kC)(action, resource, id);
  return state.userPermissions[key];
}
function canUserEditEntityRecord(state, kind, name, recordId) {
  external_wp_deprecated_default()(`wp.data.select( 'core' ).canUserEditEntityRecord()`, {
    since: "6.7",
    alternative: `wp.data.select( 'core' ).canUser( 'update', { kind, name, id } )`
  });
  return canUser(state, "update", { kind, name, id: recordId });
}
function getAutosaves(state, postType, postId) {
  return state.autosaves[postId];
}
function getAutosave(state, postType, postId, authorId) {
  if (authorId === void 0) {
    return;
  }
  const autosaves = state.autosaves[postId];
  return autosaves?.find(
    (autosave) => autosave.author === authorId
  );
}
const hasFetchedAutosaves = (0,external_wp_data_.createRegistrySelector)(
  (select) => (state, postType, postId) => {
    return select(build_module_name/* STORE_NAME */.E).hasFinishedResolution("getAutosaves", [
      postType,
      postId
    ]);
  }
);
function getReferenceByDistinctEdits(state) {
  return state.editsReference;
}
function __experimentalGetCurrentThemeBaseGlobalStyles(state) {
  const currentTheme = getCurrentTheme(state);
  if (!currentTheme) {
    return null;
  }
  return state.themeBaseGlobalStyles[currentTheme.stylesheet];
}
function __experimentalGetCurrentThemeGlobalStylesVariations(state) {
  const currentTheme = getCurrentTheme(state);
  if (!currentTheme) {
    return null;
  }
  return state.themeGlobalStyleVariations[currentTheme.stylesheet];
}
function getBlockPatterns(state) {
  return state.blockPatterns;
}
function getBlockPatternCategories(state) {
  return state.blockPatternCategories;
}
function getUserPatternCategories(state) {
  return state.userPatternCategories;
}
function getCurrentThemeGlobalStylesRevisions(state) {
  external_wp_deprecated_default()("select( 'core' ).getCurrentThemeGlobalStylesRevisions()", {
    since: "6.5.0",
    alternative: "select( 'core' ).getRevisions( 'root', 'globalStyles', ${ recordKey } )"
  });
  const currentGlobalStylesId = __experimentalGetCurrentGlobalStylesId(state);
  if (!currentGlobalStylesId) {
    return null;
  }
  return state.themeGlobalStyleRevisions[currentGlobalStylesId];
}
function getDefaultTemplateId(state, query) {
  return state.defaultTemplates[JSON.stringify(query)];
}
const getRevisions = (state, kind, name, recordKey, query) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getRevisions");
  const queriedStateRevisions = state.entities.records?.[kind]?.[name]?.revisions?.[recordKey];
  if (!queriedStateRevisions) {
    return null;
  }
  return getQueriedItems(queriedStateRevisions, query);
};
const getRevision = (0,external_wp_data_.createSelector)(
  (state, kind, name, recordKey, revisionKey, query) => {
    (0,log_entity_deprecation/* default */.A)(kind, name, "getRevision");
    const queriedState = state.entities.records?.[kind]?.[name]?.revisions?.[recordKey];
    if (!queriedState) {
      return void 0;
    }
    const context = query?.context ?? "default";
    if (!query || !query._fields) {
      if (!queriedState.itemIsComplete[context]?.[revisionKey]) {
        return void 0;
      }
      return queriedState.items[context][revisionKey];
    }
    const item = queriedState.items[context]?.[revisionKey];
    if (!item) {
      return item;
    }
    const filteredItem = {};
    const fields = (0,get_normalized_comma_separable/* default */.A)(query._fields) ?? [];
    for (let f = 0; f < fields.length; f++) {
      const field = fields[f].split(".");
      let value = item;
      field.forEach((fieldName) => {
        value = value?.[fieldName];
      });
      (0,set_nested_value/* default */.A)(filteredItem, field, value);
    }
    return filteredItem;
  },
  (state, kind, name, recordKey, revisionKey, query) => {
    const context = query?.context ?? "default";
    const queriedState = state.entities.records?.[kind]?.[name]?.revisions?.[recordKey];
    return [
      queriedState?.items?.[context]?.[revisionKey],
      queriedState?.itemIsComplete?.[context]?.[revisionKey]
    ];
  }
);



/***/ }),

/***/ 8537:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["htmlEntities"];

/***/ }),

/***/ 8582:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ dynamicSelectors),
/* harmony export */   B: () => (/* binding */ dynamicActions)
/* harmony export */ });
let dynamicActions;
let dynamicSelectors;



/***/ }),

/***/ 8741:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getBlockPatternsForPostType: () => (/* binding */ getBlockPatternsForPostType),
/* harmony export */   getEntityRecordPermissions: () => (/* binding */ getEntityRecordPermissions),
/* harmony export */   getEntityRecordsPermissions: () => (/* binding */ getEntityRecordsPermissions),
/* harmony export */   getHomePage: () => (/* binding */ getHomePage),
/* harmony export */   getNavigationFallbackId: () => (/* binding */ getNavigationFallbackId),
/* harmony export */   getPostsPageId: () => (/* binding */ getPostsPageId),
/* harmony export */   getRegisteredPostMeta: () => (/* binding */ getRegisteredPostMeta),
/* harmony export */   getTemplateId: () => (/* binding */ getTemplateId),
/* harmony export */   getUndoManager: () => (/* binding */ getUndoManager)
/* harmony export */ });
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(7143);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(8368);
/* harmony import */ var _name__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(2278);
/* harmony import */ var _lock_unlock__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(6378);
/* harmony import */ var _utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(9410);





function getUndoManager(state) {
  return state.undoManager;
}
function getNavigationFallbackId(state) {
  return state.navigationFallbackId;
}
const getBlockPatternsForPostType = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createRegistrySelector)(
  (select) => (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createSelector)(
    (state, postType) => select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getBlockPatterns().filter(
      ({ postTypes }) => !postTypes || Array.isArray(postTypes) && postTypes.includes(postType)
    ),
    () => [select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getBlockPatterns()]
  )
);
const getEntityRecordsPermissions = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createRegistrySelector)(
  (select) => (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createSelector)(
    (state, kind, name, ids) => {
      const normalizedIds = Array.isArray(ids) ? ids : [ids];
      return normalizedIds.map((id) => ({
        delete: select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).canUser("delete", {
          kind,
          name,
          id
        }),
        update: select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).canUser("update", {
          kind,
          name,
          id
        })
      }));
    },
    (state) => [state.userPermissions]
  )
);
function getEntityRecordPermissions(state, kind, name, id) {
  (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, name, "getEntityRecordPermissions");
  return getEntityRecordsPermissions(state, kind, name, id)[0];
}
function getRegisteredPostMeta(state, postType) {
  return state.registeredPostMeta?.[postType] ?? {};
}
function normalizePageId(value) {
  if (!value || !["number", "string"].includes(typeof value)) {
    return null;
  }
  if (Number(value) === 0) {
    return null;
  }
  return value.toString();
}
const getHomePage = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createRegistrySelector)(
  (select) => (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createSelector)(
    () => {
      const siteData = select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getEntityRecord(
        "root",
        "__unstableBase"
      );
      if (!siteData) {
        return null;
      }
      const homepageId = siteData?.show_on_front === "page" ? normalizePageId(siteData.page_on_front) : null;
      if (homepageId) {
        return { postType: "page", postId: homepageId };
      }
      const frontPageTemplateId = select(
        _name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E
      ).getDefaultTemplateId({
        slug: "front-page"
      });
      if (!frontPageTemplateId) {
        return null;
      }
      return { postType: "wp_template", postId: frontPageTemplateId };
    },
    (state) => [
      (0,_selectors__WEBPACK_IMPORTED_MODULE_3__.getEntityRecord)(state, "root", "__unstableBase"),
      (0,_selectors__WEBPACK_IMPORTED_MODULE_3__.getDefaultTemplateId)(state, {
        slug: "front-page"
      })
    ]
  )
);
const getPostsPageId = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createRegistrySelector)((select) => () => {
  const siteData = select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getEntityRecord(
    "root",
    "__unstableBase"
  );
  return siteData?.show_on_front === "page" ? normalizePageId(siteData.page_for_posts) : null;
});
const getTemplateId = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createRegistrySelector)(
  (select) => (state, postType, postId) => {
    const homepage = (0,_lock_unlock__WEBPACK_IMPORTED_MODULE_4__/* .unlock */ .T)(select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E)).getHomePage();
    if (!homepage) {
      return;
    }
    if (postType === "page" && postType === homepage?.postType && postId.toString() === homepage?.postId) {
      const templates = select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getEntityRecords(
        "postType",
        "wp_template",
        {
          per_page: -1
        }
      );
      if (!templates) {
        return;
      }
      const id = templates.find(({ slug }) => slug === "front-page")?.id;
      if (id) {
        return id;
      }
    }
    const editedEntity = select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getEditedEntityRecord(
      "postType",
      postType,
      postId
    );
    if (!editedEntity) {
      return;
    }
    const postsPageId = (0,_lock_unlock__WEBPACK_IMPORTED_MODULE_4__/* .unlock */ .T)(select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E)).getPostsPageId();
    if (postType === "page" && postsPageId === postId.toString()) {
      return select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getDefaultTemplateId({
        slug: "home"
      });
    }
    const currentTemplateSlug = editedEntity.template;
    if (currentTemplateSlug) {
      const currentTemplate = select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getEntityRecords("postType", "wp_template", {
        per_page: -1
      })?.find(({ slug }) => slug === currentTemplateSlug);
      if (currentTemplate) {
        return currentTemplate.id;
      }
    }
    let slugToCheck;
    if (editedEntity.slug) {
      slugToCheck = postType === "page" ? `${postType}-${editedEntity.slug}` : `single-${postType}-${editedEntity.slug}`;
    } else {
      slugToCheck = postType === "page" ? "page" : `single-${postType}`;
    }
    return select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getDefaultTemplateId({
      slug: slugToCheck
    });
  }
);



/***/ }),

/***/ 8843:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   D: () => (/* binding */ EntityContext)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(6087);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);

const EntityContext = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createContext)({});
EntityContext.displayName = "EntityContext";



/***/ }),

/***/ 9410:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ logEntityDeprecation)
/* harmony export */ });
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(4040);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _entities__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(5914);


let loggedAlready = false;
function logEntityDeprecation(kind, name, functionName, {
  alternativeFunctionName,
  isShorthandSelector = false
} = {}) {
  const deprecation = _entities__WEBPACK_IMPORTED_MODULE_1__/* .deprecatedEntities */ .TK[kind]?.[name];
  if (!deprecation) {
    return;
  }
  if (!loggedAlready) {
    const { alternative } = deprecation;
    const message = isShorthandSelector ? `'${functionName}'` : `The '${kind}', '${name}' entity (used via '${functionName}')`;
    let alternativeMessage = `the '${alternative.kind}', '${alternative.name}' entity`;
    if (alternativeFunctionName) {
      alternativeMessage += ` via the '${alternativeFunctionName}' function`;
    }
    _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_0___default()(message, {
      ...deprecation,
      alternative: alternativeMessage
    });
  }
  loggedAlready = true;
  setTimeout(() => {
    loggedAlready = false;
  }, 0);
}



/***/ }),

/***/ 9424:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   editMediaEntity: () => (/* binding */ editMediaEntity),
/* harmony export */   receiveRegisteredPostMeta: () => (/* binding */ receiveRegisteredPostMeta)
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(1455);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _name__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(2278);


function receiveRegisteredPostMeta(postType, registeredPostMeta) {
  return {
    type: "RECEIVE_REGISTERED_POST_META",
    postType,
    registeredPostMeta
  };
}
const editMediaEntity = (recordId, edits = {}, { __unstableFetch = (_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()), throwOnError = false } = {}) => async ({ dispatch, resolveSelect }) => {
  if (!recordId) {
    return;
  }
  const kind = "postType";
  const name = "attachment";
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.kind === kind && config.name === name
  );
  if (!entityConfig) {
    return;
  }
  const lock = await dispatch.__unstableAcquireStoreLock(
    _name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E,
    ["entities", "records", kind, name, recordId],
    { exclusive: true }
  );
  let updatedRecord;
  let error;
  let hasError = false;
  try {
    dispatch({
      type: "SAVE_ENTITY_RECORD_START",
      kind,
      name,
      recordId
    });
    try {
      const path = `${entityConfig.baseURL}/${recordId}/edit`;
      const newRecord = await __unstableFetch({
        path,
        method: "POST",
        data: {
          ...edits
        }
      });
      if (newRecord) {
        dispatch.receiveEntityRecords(
          kind,
          name,
          [newRecord],
          void 0,
          true,
          void 0,
          void 0
        );
        updatedRecord = newRecord;
      }
    } catch (e) {
      error = e;
      hasError = true;
    }
    dispatch({
      type: "SAVE_ENTITY_RECORD_FINISH",
      kind,
      name,
      recordId,
      error
    });
    if (hasError && throwOnError) {
      throw error;
    }
    return updatedRecord;
  } finally {
    dispatch.__unstableReleaseStoreLock(lock);
  }
};



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
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module is referenced by other modules so it can't be inlined
/******/ 	var __webpack_exports__ = __webpack_require__(4565);
/******/ 	(window.wp = window.wp || {}).coreData = __webpack_exports__;
/******/ 	
/******/ })()
;