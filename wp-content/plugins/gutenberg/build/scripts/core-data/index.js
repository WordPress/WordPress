"use strict";
var wp;
(wp ||= {}).coreData = (() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __require = /* @__PURE__ */ ((x) => typeof require !== "undefined" ? require : typeof Proxy !== "undefined" ? new Proxy(x, {
    get: (a, b) => (typeof require !== "undefined" ? require : a)[b]
  }) : x)(function(x) {
    if (typeof require !== "undefined") return require.apply(this, arguments);
    throw Error('Dynamic require of "' + x + '" is not supported');
  });
  var __commonJS = (cb, mod) => function __require2() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __export = (target, all2) => {
    for (var name in all2)
      __defProp(target, name, { get: all2[name], enumerable: true });
  };
  var __copyProps = (to, from2, except, desc) => {
    if (from2 && typeof from2 === "object" || typeof from2 === "function") {
      for (let key of __getOwnPropNames(from2))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from2[key], enumerable: !(desc = __getOwnPropDesc(from2, key)) || desc.enumerable });
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

  // node_modules/fast-deep-equal/es6/index.js
  var require_es6 = __commonJS({
    "node_modules/fast-deep-equal/es6/index.js"(exports, module) {
      "use strict";
      module.exports = function equal(a, b) {
        if (a === b) return true;
        if (a && b && typeof a == "object" && typeof b == "object") {
          if (a.constructor !== b.constructor) return false;
          var length3, i, keys2;
          if (Array.isArray(a)) {
            length3 = a.length;
            if (length3 != b.length) return false;
            for (i = length3; i-- !== 0; )
              if (!equal(a[i], b[i])) return false;
            return true;
          }
          if (a instanceof Map && b instanceof Map) {
            if (a.size !== b.size) return false;
            for (i of a.entries())
              if (!b.has(i[0])) return false;
            for (i of a.entries())
              if (!equal(i[1], b.get(i[0]))) return false;
            return true;
          }
          if (a instanceof Set && b instanceof Set) {
            if (a.size !== b.size) return false;
            for (i of a.entries())
              if (!b.has(i[0])) return false;
            return true;
          }
          if (ArrayBuffer.isView(a) && ArrayBuffer.isView(b)) {
            length3 = a.length;
            if (length3 != b.length) return false;
            for (i = length3; i-- !== 0; )
              if (a[i] !== b[i]) return false;
            return true;
          }
          if (a.constructor === RegExp) return a.source === b.source && a.flags === b.flags;
          if (a.valueOf !== Object.prototype.valueOf) return a.valueOf() === b.valueOf();
          if (a.toString !== Object.prototype.toString) return a.toString() === b.toString();
          keys2 = Object.keys(a);
          length3 = keys2.length;
          if (length3 !== Object.keys(b).length) return false;
          for (i = length3; i-- !== 0; )
            if (!Object.prototype.hasOwnProperty.call(b, keys2[i])) return false;
          for (i = length3; i-- !== 0; ) {
            var key = keys2[i];
            if (!equal(a[key], b[key])) return false;
          }
          return true;
        }
        return a !== a && b !== b;
      };
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/undo-manager
  var require_undo_manager = __commonJS({
    "package-external:@wordpress/undo-manager"(exports, module) {
      module.exports = window.wp.undoManager;
    }
  });

  // node_modules/equivalent-key-map/equivalent-key-map.js
  var require_equivalent_key_map = __commonJS({
    "node_modules/equivalent-key-map/equivalent-key-map.js"(exports, module) {
      "use strict";
      function _typeof(obj) {
        if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
          _typeof = function(obj2) {
            return typeof obj2;
          };
        } else {
          _typeof = function(obj2) {
            return obj2 && typeof Symbol === "function" && obj2.constructor === Symbol && obj2 !== Symbol.prototype ? "symbol" : typeof obj2;
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
      function getValuePair(instance, key) {
        var _map = instance._map, _arrayTreeMap = instance._arrayTreeMap, _objectTreeMap = instance._objectTreeMap;
        if (_map.has(key)) {
          return _map.get(key);
        }
        var properties = Object.keys(key).sort();
        var map2 = Array.isArray(key) ? _arrayTreeMap : _objectTreeMap;
        for (var i = 0; i < properties.length; i++) {
          var property = properties[i];
          map2 = map2.get(property);
          if (map2 === void 0) {
            return;
          }
          var propertyValue = key[property];
          map2 = map2.get(propertyValue);
          if (map2 === void 0) {
            return;
          }
        }
        var valuePair = map2.get("_ekm_value");
        if (!valuePair) {
          return;
        }
        _map.delete(valuePair[0]);
        valuePair[0] = key;
        map2.set("_ekm_value", valuePair);
        _map.set(key, valuePair);
        return valuePair;
      }
      var EquivalentKeyMap2 = /* @__PURE__ */ (function() {
        function EquivalentKeyMap3(iterable) {
          _classCallCheck(this, EquivalentKeyMap3);
          this.clear();
          if (iterable instanceof EquivalentKeyMap3) {
            var iterablePairs = [];
            iterable.forEach(function(value, key) {
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
        _createClass(EquivalentKeyMap3, [{
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
            if (key === null || _typeof(key) !== "object") {
              this._map.set(key, value);
              return this;
            }
            var properties = Object.keys(key).sort();
            var valuePair = [key, value];
            var map2 = Array.isArray(key) ? this._arrayTreeMap : this._objectTreeMap;
            for (var i = 0; i < properties.length; i++) {
              var property = properties[i];
              if (!map2.has(property)) {
                map2.set(property, new EquivalentKeyMap3());
              }
              map2 = map2.get(property);
              var propertyValue = key[property];
              if (!map2.has(propertyValue)) {
                map2.set(propertyValue, new EquivalentKeyMap3());
              }
              map2 = map2.get(propertyValue);
            }
            var previousValuePair = map2.get("_ekm_value");
            if (previousValuePair) {
              this._map.delete(previousValuePair[0]);
            }
            map2.set("_ekm_value", valuePair);
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
          value: function get2(key) {
            if (key === null || _typeof(key) !== "object") {
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
            if (key === null || _typeof(key) !== "object") {
              return this._map.has(key);
            }
            return getValuePair(this, key) !== void 0;
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
            }
            this.set(key, void 0);
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
          value: function forEach2(callback) {
            var _this = this;
            var thisArg = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this;
            this._map.forEach(function(value, key) {
              if (key !== null && _typeof(key) === "object") {
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
            this._map = /* @__PURE__ */ new Map();
            this._arrayTreeMap = /* @__PURE__ */ new Map();
            this._objectTreeMap = /* @__PURE__ */ new Map();
          }
        }, {
          key: "size",
          get: function get2() {
            return this._map.size;
          }
        }]);
        return EquivalentKeyMap3;
      })();
      module.exports = EquivalentKeyMap2;
    }
  });

  // package-external:@wordpress/url
  var require_url = __commonJS({
    "package-external:@wordpress/url"(exports, module) {
      module.exports = window.wp.url;
    }
  });

  // package-external:@wordpress/api-fetch
  var require_api_fetch = __commonJS({
    "package-external:@wordpress/api-fetch"(exports, module) {
      module.exports = window.wp.apiFetch;
    }
  });

  // package-external:@wordpress/blocks
  var require_blocks = __commonJS({
    "package-external:@wordpress/blocks"(exports, module) {
      module.exports = window.wp.blocks;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/hooks
  var require_hooks = __commonJS({
    "package-external:@wordpress/hooks"(exports, module) {
      module.exports = window.wp.hooks;
    }
  });

  // node_modules/simple-peer/simplepeer.min.js
  var require_simplepeer_min = __commonJS({
    "node_modules/simple-peer/simplepeer.min.js"(exports, module) {
      (function(e) {
        if ("object" == typeof exports && "undefined" != typeof module) module.exports = e();
        else if ("function" == typeof define && define.amd) define([], e);
        else {
          var t;
          t = "undefined" == typeof window ? "undefined" == typeof global ? "undefined" == typeof self ? this : self : global : window, t.SimplePeer = e();
        }
      })(function() {
        var t = Math.floor, n = Math.abs, r = Math.pow;
        return (/* @__PURE__ */ (function() {
          function d(s, e, n2) {
            function t2(o, i) {
              if (!e[o]) {
                if (!s[o]) {
                  var l = "function" == typeof __require && __require;
                  if (!i && l) return l(o, true);
                  if (r2) return r2(o, true);
                  var c = new Error("Cannot find module '" + o + "'");
                  throw c.code = "MODULE_NOT_FOUND", c;
                }
                var a2 = e[o] = { exports: {} };
                s[o][0].call(a2.exports, function(e2) {
                  var r3 = s[o][1][e2];
                  return t2(r3 || e2);
                }, a2, a2.exports, d, s, e, n2);
              }
              return e[o].exports;
            }
            for (var r2 = "function" == typeof __require && __require, a = 0; a < n2.length; a++) t2(n2[a]);
            return t2;
          }
          return d;
        })())({ 1: [function(e, t2, n2) {
          "use strict";
          function r2(e2) {
            var t3 = e2.length;
            if (0 < t3 % 4) throw new Error("Invalid string. Length must be a multiple of 4");
            var n3 = e2.indexOf("=");
            -1 === n3 && (n3 = t3);
            var r3 = n3 === t3 ? 0 : 4 - n3 % 4;
            return [n3, r3];
          }
          function a(e2, t3, n3) {
            return 3 * (t3 + n3) / 4 - n3;
          }
          function o(e2) {
            var t3, n3, o2 = r2(e2), d2 = o2[0], s2 = o2[1], l2 = new p(a(e2, d2, s2)), c2 = 0, f2 = 0 < s2 ? d2 - 4 : d2;
            for (n3 = 0; n3 < f2; n3 += 4) t3 = u[e2.charCodeAt(n3)] << 18 | u[e2.charCodeAt(n3 + 1)] << 12 | u[e2.charCodeAt(n3 + 2)] << 6 | u[e2.charCodeAt(n3 + 3)], l2[c2++] = 255 & t3 >> 16, l2[c2++] = 255 & t3 >> 8, l2[c2++] = 255 & t3;
            return 2 === s2 && (t3 = u[e2.charCodeAt(n3)] << 2 | u[e2.charCodeAt(n3 + 1)] >> 4, l2[c2++] = 255 & t3), 1 === s2 && (t3 = u[e2.charCodeAt(n3)] << 10 | u[e2.charCodeAt(n3 + 1)] << 4 | u[e2.charCodeAt(n3 + 2)] >> 2, l2[c2++] = 255 & t3 >> 8, l2[c2++] = 255 & t3), l2;
          }
          function d(e2) {
            return c[63 & e2 >> 18] + c[63 & e2 >> 12] + c[63 & e2 >> 6] + c[63 & e2];
          }
          function s(e2, t3, n3) {
            for (var r3, a2 = [], o2 = t3; o2 < n3; o2 += 3) r3 = (16711680 & e2[o2] << 16) + (65280 & e2[o2 + 1] << 8) + (255 & e2[o2 + 2]), a2.push(d(r3));
            return a2.join("");
          }
          function l(e2) {
            for (var t3, n3 = e2.length, r3 = n3 % 3, a2 = [], o2 = 16383, d2 = 0, l2 = n3 - r3; d2 < l2; d2 += o2) a2.push(s(e2, d2, d2 + o2 > l2 ? l2 : d2 + o2));
            return 1 === r3 ? (t3 = e2[n3 - 1], a2.push(c[t3 >> 2] + c[63 & t3 << 4] + "==")) : 2 === r3 && (t3 = (e2[n3 - 2] << 8) + e2[n3 - 1], a2.push(c[t3 >> 10] + c[63 & t3 >> 4] + c[63 & t3 << 2] + "=")), a2.join("");
          }
          n2.byteLength = function(e2) {
            var t3 = r2(e2), n3 = t3[0], a2 = t3[1];
            return 3 * (n3 + a2) / 4 - a2;
          }, n2.toByteArray = o, n2.fromByteArray = l;
          for (var c = [], u = [], p = "undefined" == typeof Uint8Array ? Array : Uint8Array, f = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789+/", g = 0, _ = f.length; g < _; ++g) c[g] = f[g], u[f.charCodeAt(g)] = g;
          u[45] = 62, u[95] = 63;
        }, {}], 2: [function() {
        }, {}], 3: [function(e, t2, n2) {
          (function() {
            (function() {
              "use strict";
              var t3 = String.fromCharCode, o = Math.min;
              function d(e2) {
                if (2147483647 < e2) throw new RangeError('The value "' + e2 + '" is invalid for option "size"');
                var t4 = new Uint8Array(e2);
                return t4.__proto__ = s.prototype, t4;
              }
              function s(e2, t4, n3) {
                if ("number" == typeof e2) {
                  if ("string" == typeof t4) throw new TypeError('The "string" argument must be of type string. Received type number');
                  return p(e2);
                }
                return l(e2, t4, n3);
              }
              function l(e2, t4, n3) {
                if ("string" == typeof e2) return f(e2, t4);
                if (ArrayBuffer.isView(e2)) return g(e2);
                if (null == e2) throw TypeError("The first argument must be one of type string, Buffer, ArrayBuffer, Array, or Array-like Object. Received type " + typeof e2);
                if (K(e2, ArrayBuffer) || e2 && K(e2.buffer, ArrayBuffer)) return _(e2, t4, n3);
                if ("number" == typeof e2) throw new TypeError('The "value" argument must not be of type number. Received type number');
                var r2 = e2.valueOf && e2.valueOf();
                if (null != r2 && r2 !== e2) return s.from(r2, t4, n3);
                var a = h(e2);
                if (a) return a;
                if ("undefined" != typeof Symbol && null != Symbol.toPrimitive && "function" == typeof e2[Symbol.toPrimitive]) return s.from(e2[Symbol.toPrimitive]("string"), t4, n3);
                throw new TypeError("The first argument must be one of type string, Buffer, ArrayBuffer, Array, or Array-like Object. Received type " + typeof e2);
              }
              function c(e2) {
                if ("number" != typeof e2) throw new TypeError('"size" argument must be of type number');
                else if (0 > e2) throw new RangeError('The value "' + e2 + '" is invalid for option "size"');
              }
              function u(e2, t4, n3) {
                return c(e2), 0 >= e2 ? d(e2) : void 0 === t4 ? d(e2) : "string" == typeof n3 ? d(e2).fill(t4, n3) : d(e2).fill(t4);
              }
              function p(e2) {
                return c(e2), d(0 > e2 ? 0 : 0 | m(e2));
              }
              function f(e2, t4) {
                if (("string" != typeof t4 || "" === t4) && (t4 = "utf8"), !s.isEncoding(t4)) throw new TypeError("Unknown encoding: " + t4);
                var n3 = 0 | b(e2, t4), r2 = d(n3), a = r2.write(e2, t4);
                return a !== n3 && (r2 = r2.slice(0, a)), r2;
              }
              function g(e2) {
                for (var t4 = 0 > e2.length ? 0 : 0 | m(e2.length), n3 = d(t4), r2 = 0; r2 < t4; r2 += 1) n3[r2] = 255 & e2[r2];
                return n3;
              }
              function _(e2, t4, n3) {
                if (0 > t4 || e2.byteLength < t4) throw new RangeError('"offset" is outside of buffer bounds');
                if (e2.byteLength < t4 + (n3 || 0)) throw new RangeError('"length" is outside of buffer bounds');
                var r2;
                return r2 = void 0 === t4 && void 0 === n3 ? new Uint8Array(e2) : void 0 === n3 ? new Uint8Array(e2, t4) : new Uint8Array(e2, t4, n3), r2.__proto__ = s.prototype, r2;
              }
              function h(e2) {
                if (s.isBuffer(e2)) {
                  var t4 = 0 | m(e2.length), n3 = d(t4);
                  return 0 === n3.length ? n3 : (e2.copy(n3, 0, 0, t4), n3);
                }
                return void 0 === e2.length ? "Buffer" === e2.type && Array.isArray(e2.data) ? g(e2.data) : void 0 : "number" != typeof e2.length || X(e2.length) ? d(0) : g(e2);
              }
              function m(e2) {
                if (e2 >= 2147483647) throw new RangeError("Attempt to allocate Buffer larger than maximum size: 0x" + 2147483647 .toString(16) + " bytes");
                return 0 | e2;
              }
              function b(e2, t4) {
                if (s.isBuffer(e2)) return e2.length;
                if (ArrayBuffer.isView(e2) || K(e2, ArrayBuffer)) return e2.byteLength;
                if ("string" != typeof e2) throw new TypeError('The "string" argument must be one of type string, Buffer, or ArrayBuffer. Received type ' + typeof e2);
                var n3 = e2.length, r2 = 2 < arguments.length && true === arguments[2];
                if (!r2 && 0 === n3) return 0;
                for (var a = false; ; ) switch (t4) {
                  case "ascii":
                  case "latin1":
                  case "binary":
                    return n3;
                  case "utf8":
                  case "utf-8":
                    return H(e2).length;
                  case "ucs2":
                  case "ucs-2":
                  case "utf16le":
                  case "utf-16le":
                    return 2 * n3;
                  case "hex":
                    return n3 >>> 1;
                  case "base64":
                    return z(e2).length;
                  default:
                    if (a) return r2 ? -1 : H(e2).length;
                    t4 = ("" + t4).toLowerCase(), a = true;
                }
              }
              function y(e2, t4, n3) {
                var r2 = false;
                if ((void 0 === t4 || 0 > t4) && (t4 = 0), t4 > this.length) return "";
                if ((void 0 === n3 || n3 > this.length) && (n3 = this.length), 0 >= n3) return "";
                if (n3 >>>= 0, t4 >>>= 0, n3 <= t4) return "";
                for (e2 || (e2 = "utf8"); ; ) switch (e2) {
                  case "hex":
                    return P(this, t4, n3);
                  case "utf8":
                  case "utf-8":
                    return x(this, t4, n3);
                  case "ascii":
                    return D(this, t4, n3);
                  case "latin1":
                  case "binary":
                    return I(this, t4, n3);
                  case "base64":
                    return A(this, t4, n3);
                  case "ucs2":
                  case "ucs-2":
                  case "utf16le":
                  case "utf-16le":
                    return M(this, t4, n3);
                  default:
                    if (r2) throw new TypeError("Unknown encoding: " + e2);
                    e2 = (e2 + "").toLowerCase(), r2 = true;
                }
              }
              function C(e2, t4, n3) {
                var r2 = e2[t4];
                e2[t4] = e2[n3], e2[n3] = r2;
              }
              function R(e2, t4, n3, r2, a) {
                if (0 === e2.length) return -1;
                if ("string" == typeof n3 ? (r2 = n3, n3 = 0) : 2147483647 < n3 ? n3 = 2147483647 : -2147483648 > n3 && (n3 = -2147483648), n3 = +n3, X(n3) && (n3 = a ? 0 : e2.length - 1), 0 > n3 && (n3 = e2.length + n3), n3 >= e2.length) {
                  if (a) return -1;
                  n3 = e2.length - 1;
                } else if (0 > n3) if (a) n3 = 0;
                else return -1;
                if ("string" == typeof t4 && (t4 = s.from(t4, r2)), s.isBuffer(t4)) return 0 === t4.length ? -1 : E(e2, t4, n3, r2, a);
                if ("number" == typeof t4) return t4 &= 255, "function" == typeof Uint8Array.prototype.indexOf ? a ? Uint8Array.prototype.indexOf.call(e2, t4, n3) : Uint8Array.prototype.lastIndexOf.call(e2, t4, n3) : E(e2, [t4], n3, r2, a);
                throw new TypeError("val must be string, number or Buffer");
              }
              function E(e2, t4, n3, r2, a) {
                function o2(e3, t5) {
                  return 1 === d2 ? e3[t5] : e3.readUInt16BE(t5 * d2);
                }
                var d2 = 1, s2 = e2.length, l2 = t4.length;
                if (void 0 !== r2 && (r2 = (r2 + "").toLowerCase(), "ucs2" === r2 || "ucs-2" === r2 || "utf16le" === r2 || "utf-16le" === r2)) {
                  if (2 > e2.length || 2 > t4.length) return -1;
                  d2 = 2, s2 /= 2, l2 /= 2, n3 /= 2;
                }
                var c2;
                if (a) {
                  var u2 = -1;
                  for (c2 = n3; c2 < s2; c2++) if (o2(e2, c2) !== o2(t4, -1 === u2 ? 0 : c2 - u2)) -1 !== u2 && (c2 -= c2 - u2), u2 = -1;
                  else if (-1 === u2 && (u2 = c2), c2 - u2 + 1 === l2) return u2 * d2;
                } else for (n3 + l2 > s2 && (n3 = s2 - l2), c2 = n3; 0 <= c2; c2--) {
                  for (var p2 = true, f2 = 0; f2 < l2; f2++) if (o2(e2, c2 + f2) !== o2(t4, f2)) {
                    p2 = false;
                    break;
                  }
                  if (p2) return c2;
                }
                return -1;
              }
              function w(e2, t4, n3, r2) {
                n3 = +n3 || 0;
                var a = e2.length - n3;
                r2 ? (r2 = +r2, r2 > a && (r2 = a)) : r2 = a;
                var o2 = t4.length;
                r2 > o2 / 2 && (r2 = o2 / 2);
                for (var d2, s2 = 0; s2 < r2; ++s2) {
                  if (d2 = parseInt(t4.substr(2 * s2, 2), 16), X(d2)) return s2;
                  e2[n3 + s2] = d2;
                }
                return s2;
              }
              function S(e2, t4, n3, r2) {
                return G(H(t4, e2.length - n3), e2, n3, r2);
              }
              function T(e2, t4, n3, r2) {
                return G(Y(t4), e2, n3, r2);
              }
              function v(e2, t4, n3, r2) {
                return T(e2, t4, n3, r2);
              }
              function k(e2, t4, n3, r2) {
                return G(z(t4), e2, n3, r2);
              }
              function L(e2, t4, n3, r2) {
                return G(V(t4, e2.length - n3), e2, n3, r2);
              }
              function A(e2, t4, n3) {
                return 0 === t4 && n3 === e2.length ? $.fromByteArray(e2) : $.fromByteArray(e2.slice(t4, n3));
              }
              function x(e2, t4, n3) {
                n3 = o(e2.length, n3);
                for (var r2 = [], a = t4; a < n3; ) {
                  var d2 = e2[a], s2 = null, l2 = 239 < d2 ? 4 : 223 < d2 ? 3 : 191 < d2 ? 2 : 1;
                  if (a + l2 <= n3) {
                    var c2, u2, p2, f2;
                    1 === l2 ? 128 > d2 && (s2 = d2) : 2 === l2 ? (c2 = e2[a + 1], 128 == (192 & c2) && (f2 = (31 & d2) << 6 | 63 & c2, 127 < f2 && (s2 = f2))) : 3 === l2 ? (c2 = e2[a + 1], u2 = e2[a + 2], 128 == (192 & c2) && 128 == (192 & u2) && (f2 = (15 & d2) << 12 | (63 & c2) << 6 | 63 & u2, 2047 < f2 && (55296 > f2 || 57343 < f2) && (s2 = f2))) : 4 === l2 ? (c2 = e2[a + 1], u2 = e2[a + 2], p2 = e2[a + 3], 128 == (192 & c2) && 128 == (192 & u2) && 128 == (192 & p2) && (f2 = (15 & d2) << 18 | (63 & c2) << 12 | (63 & u2) << 6 | 63 & p2, 65535 < f2 && 1114112 > f2 && (s2 = f2))) : void 0;
                  }
                  null === s2 ? (s2 = 65533, l2 = 1) : 65535 < s2 && (s2 -= 65536, r2.push(55296 | 1023 & s2 >>> 10), s2 = 56320 | 1023 & s2), r2.push(s2), a += l2;
                }
                return N(r2);
              }
              function N(e2) {
                var n3 = e2.length;
                if (n3 <= 4096) return t3.apply(String, e2);
                for (var r2 = "", a = 0; a < n3; ) r2 += t3.apply(String, e2.slice(a, a += 4096));
                return r2;
              }
              function D(e2, n3, r2) {
                var a = "";
                r2 = o(e2.length, r2);
                for (var d2 = n3; d2 < r2; ++d2) a += t3(127 & e2[d2]);
                return a;
              }
              function I(e2, n3, r2) {
                var a = "";
                r2 = o(e2.length, r2);
                for (var d2 = n3; d2 < r2; ++d2) a += t3(e2[d2]);
                return a;
              }
              function P(e2, t4, n3) {
                var r2 = e2.length;
                (!t4 || 0 > t4) && (t4 = 0), (!n3 || 0 > n3 || n3 > r2) && (n3 = r2);
                for (var a = "", o2 = t4; o2 < n3; ++o2) a += W(e2[o2]);
                return a;
              }
              function M(e2, n3, r2) {
                for (var a = e2.slice(n3, r2), o2 = "", d2 = 0; d2 < a.length; d2 += 2) o2 += t3(a[d2] + 256 * a[d2 + 1]);
                return o2;
              }
              function O(e2, t4, n3) {
                if (0 != e2 % 1 || 0 > e2) throw new RangeError("offset is not uint");
                if (e2 + t4 > n3) throw new RangeError("Trying to access beyond buffer length");
              }
              function F(e2, t4, n3, r2, a, o2) {
                if (!s.isBuffer(e2)) throw new TypeError('"buffer" argument must be a Buffer instance');
                if (t4 > a || t4 < o2) throw new RangeError('"value" argument is out of bounds');
                if (n3 + r2 > e2.length) throw new RangeError("Index out of range");
              }
              function B(e2, t4, n3, r2) {
                if (n3 + r2 > e2.length) throw new RangeError("Index out of range");
                if (0 > n3) throw new RangeError("Index out of range");
              }
              function U(e2, t4, n3, r2, a) {
                return t4 = +t4, n3 >>>= 0, a || B(e2, t4, n3, 4, 34028234663852886e22, -34028234663852886e22), J.write(e2, t4, n3, r2, 23, 4), n3 + 4;
              }
              function j(e2, t4, n3, r2, a) {
                return t4 = +t4, n3 >>>= 0, a || B(e2, t4, n3, 8, 17976931348623157e292, -17976931348623157e292), J.write(e2, t4, n3, r2, 52, 8), n3 + 8;
              }
              function q(e2) {
                if (e2 = e2.split("=")[0], e2 = e2.trim().replace(Q, ""), 2 > e2.length) return "";
                for (; 0 != e2.length % 4; ) e2 += "=";
                return e2;
              }
              function W(e2) {
                return 16 > e2 ? "0" + e2.toString(16) : e2.toString(16);
              }
              function H(e2, t4) {
                t4 = t4 || 1 / 0;
                for (var n3, r2 = e2.length, a = null, o2 = [], d2 = 0; d2 < r2; ++d2) {
                  if (n3 = e2.charCodeAt(d2), 55295 < n3 && 57344 > n3) {
                    if (!a) {
                      if (56319 < n3) {
                        -1 < (t4 -= 3) && o2.push(239, 191, 189);
                        continue;
                      } else if (d2 + 1 === r2) {
                        -1 < (t4 -= 3) && o2.push(239, 191, 189);
                        continue;
                      }
                      a = n3;
                      continue;
                    }
                    if (56320 > n3) {
                      -1 < (t4 -= 3) && o2.push(239, 191, 189), a = n3;
                      continue;
                    }
                    n3 = (a - 55296 << 10 | n3 - 56320) + 65536;
                  } else a && -1 < (t4 -= 3) && o2.push(239, 191, 189);
                  if (a = null, 128 > n3) {
                    if (0 > (t4 -= 1)) break;
                    o2.push(n3);
                  } else if (2048 > n3) {
                    if (0 > (t4 -= 2)) break;
                    o2.push(192 | n3 >> 6, 128 | 63 & n3);
                  } else if (65536 > n3) {
                    if (0 > (t4 -= 3)) break;
                    o2.push(224 | n3 >> 12, 128 | 63 & n3 >> 6, 128 | 63 & n3);
                  } else if (1114112 > n3) {
                    if (0 > (t4 -= 4)) break;
                    o2.push(240 | n3 >> 18, 128 | 63 & n3 >> 12, 128 | 63 & n3 >> 6, 128 | 63 & n3);
                  } else throw new Error("Invalid code point");
                }
                return o2;
              }
              function Y(e2) {
                for (var t4 = [], n3 = 0; n3 < e2.length; ++n3) t4.push(255 & e2.charCodeAt(n3));
                return t4;
              }
              function V(e2, t4) {
                for (var n3, r2, a, o2 = [], d2 = 0; d2 < e2.length && !(0 > (t4 -= 2)); ++d2) n3 = e2.charCodeAt(d2), r2 = n3 >> 8, a = n3 % 256, o2.push(a), o2.push(r2);
                return o2;
              }
              function z(e2) {
                return $.toByteArray(q(e2));
              }
              function G(e2, t4, n3, r2) {
                for (var a = 0; a < r2 && !(a + n3 >= t4.length || a >= e2.length); ++a) t4[a + n3] = e2[a];
                return a;
              }
              function K(e2, t4) {
                return e2 instanceof t4 || null != e2 && null != e2.constructor && null != e2.constructor.name && e2.constructor.name === t4.name;
              }
              function X(e2) {
                return e2 !== e2;
              }
              var $ = e("base64-js"), J = e("ieee754");
              n2.Buffer = s, n2.SlowBuffer = function(e2) {
                return +e2 != e2 && (e2 = 0), s.alloc(+e2);
              }, n2.INSPECT_MAX_BYTES = 50;
              n2.kMaxLength = 2147483647, s.TYPED_ARRAY_SUPPORT = (function() {
                try {
                  var e2 = new Uint8Array(1);
                  return e2.__proto__ = { __proto__: Uint8Array.prototype, foo: function() {
                    return 42;
                  } }, 42 === e2.foo();
                } catch (t4) {
                  return false;
                }
              })(), s.TYPED_ARRAY_SUPPORT || "undefined" == typeof console || "function" != typeof console.error || console.error("This browser lacks typed array (Uint8Array) support which is required by `buffer` v5.x. Use `buffer` v4.x if you require old browser support."), Object.defineProperty(s.prototype, "parent", { enumerable: true, get: function() {
                return s.isBuffer(this) ? this.buffer : void 0;
              } }), Object.defineProperty(s.prototype, "offset", { enumerable: true, get: function() {
                return s.isBuffer(this) ? this.byteOffset : void 0;
              } }), "undefined" != typeof Symbol && null != Symbol.species && s[Symbol.species] === s && Object.defineProperty(s, Symbol.species, { value: null, configurable: true, enumerable: false, writable: false }), s.poolSize = 8192, s.from = function(e2, t4, n3) {
                return l(e2, t4, n3);
              }, s.prototype.__proto__ = Uint8Array.prototype, s.__proto__ = Uint8Array, s.alloc = function(e2, t4, n3) {
                return u(e2, t4, n3);
              }, s.allocUnsafe = function(e2) {
                return p(e2);
              }, s.allocUnsafeSlow = function(e2) {
                return p(e2);
              }, s.isBuffer = function(e2) {
                return null != e2 && true === e2._isBuffer && e2 !== s.prototype;
              }, s.compare = function(e2, t4) {
                if (K(e2, Uint8Array) && (e2 = s.from(e2, e2.offset, e2.byteLength)), K(t4, Uint8Array) && (t4 = s.from(t4, t4.offset, t4.byteLength)), !s.isBuffer(e2) || !s.isBuffer(t4)) throw new TypeError('The "buf1", "buf2" arguments must be one of type Buffer or Uint8Array');
                if (e2 === t4) return 0;
                for (var n3 = e2.length, r2 = t4.length, d2 = 0, l2 = o(n3, r2); d2 < l2; ++d2) if (e2[d2] !== t4[d2]) {
                  n3 = e2[d2], r2 = t4[d2];
                  break;
                }
                return n3 < r2 ? -1 : r2 < n3 ? 1 : 0;
              }, s.isEncoding = function(e2) {
                switch ((e2 + "").toLowerCase()) {
                  case "hex":
                  case "utf8":
                  case "utf-8":
                  case "ascii":
                  case "latin1":
                  case "binary":
                  case "base64":
                  case "ucs2":
                  case "ucs-2":
                  case "utf16le":
                  case "utf-16le":
                    return true;
                  default:
                    return false;
                }
              }, s.concat = function(e2, t4) {
                if (!Array.isArray(e2)) throw new TypeError('"list" argument must be an Array of Buffers');
                if (0 === e2.length) return s.alloc(0);
                var n3;
                if (t4 === void 0) for (t4 = 0, n3 = 0; n3 < e2.length; ++n3) t4 += e2[n3].length;
                var r2 = s.allocUnsafe(t4), a = 0;
                for (n3 = 0; n3 < e2.length; ++n3) {
                  var o2 = e2[n3];
                  if (K(o2, Uint8Array) && (o2 = s.from(o2)), !s.isBuffer(o2)) throw new TypeError('"list" argument must be an Array of Buffers');
                  o2.copy(r2, a), a += o2.length;
                }
                return r2;
              }, s.byteLength = b, s.prototype._isBuffer = true, s.prototype.swap16 = function() {
                var e2 = this.length;
                if (0 != e2 % 2) throw new RangeError("Buffer size must be a multiple of 16-bits");
                for (var t4 = 0; t4 < e2; t4 += 2) C(this, t4, t4 + 1);
                return this;
              }, s.prototype.swap32 = function() {
                var e2 = this.length;
                if (0 != e2 % 4) throw new RangeError("Buffer size must be a multiple of 32-bits");
                for (var t4 = 0; t4 < e2; t4 += 4) C(this, t4, t4 + 3), C(this, t4 + 1, t4 + 2);
                return this;
              }, s.prototype.swap64 = function() {
                var e2 = this.length;
                if (0 != e2 % 8) throw new RangeError("Buffer size must be a multiple of 64-bits");
                for (var t4 = 0; t4 < e2; t4 += 8) C(this, t4, t4 + 7), C(this, t4 + 1, t4 + 6), C(this, t4 + 2, t4 + 5), C(this, t4 + 3, t4 + 4);
                return this;
              }, s.prototype.toString = function() {
                var e2 = this.length;
                return 0 === e2 ? "" : 0 === arguments.length ? x(this, 0, e2) : y.apply(this, arguments);
              }, s.prototype.toLocaleString = s.prototype.toString, s.prototype.equals = function(e2) {
                if (!s.isBuffer(e2)) throw new TypeError("Argument must be a Buffer");
                return this === e2 || 0 === s.compare(this, e2);
              }, s.prototype.inspect = function() {
                var e2 = "", t4 = n2.INSPECT_MAX_BYTES;
                return e2 = this.toString("hex", 0, t4).replace(/(.{2})/g, "$1 ").trim(), this.length > t4 && (e2 += " ... "), "<Buffer " + e2 + ">";
              }, s.prototype.compare = function(e2, t4, n3, r2, a) {
                if (K(e2, Uint8Array) && (e2 = s.from(e2, e2.offset, e2.byteLength)), !s.isBuffer(e2)) throw new TypeError('The "target" argument must be one of type Buffer or Uint8Array. Received type ' + typeof e2);
                if (void 0 === t4 && (t4 = 0), void 0 === n3 && (n3 = e2 ? e2.length : 0), void 0 === r2 && (r2 = 0), void 0 === a && (a = this.length), 0 > t4 || n3 > e2.length || 0 > r2 || a > this.length) throw new RangeError("out of range index");
                if (r2 >= a && t4 >= n3) return 0;
                if (r2 >= a) return -1;
                if (t4 >= n3) return 1;
                if (t4 >>>= 0, n3 >>>= 0, r2 >>>= 0, a >>>= 0, this === e2) return 0;
                for (var d2 = a - r2, l2 = n3 - t4, c2 = o(d2, l2), u2 = this.slice(r2, a), p2 = e2.slice(t4, n3), f2 = 0; f2 < c2; ++f2) if (u2[f2] !== p2[f2]) {
                  d2 = u2[f2], l2 = p2[f2];
                  break;
                }
                return d2 < l2 ? -1 : l2 < d2 ? 1 : 0;
              }, s.prototype.includes = function(e2, t4, n3) {
                return -1 !== this.indexOf(e2, t4, n3);
              }, s.prototype.indexOf = function(e2, t4, n3) {
                return R(this, e2, t4, n3, true);
              }, s.prototype.lastIndexOf = function(e2, t4, n3) {
                return R(this, e2, t4, n3, false);
              }, s.prototype.write = function(e2, t4, n3, r2) {
                if (void 0 === t4) r2 = "utf8", n3 = this.length, t4 = 0;
                else if (void 0 === n3 && "string" == typeof t4) r2 = t4, n3 = this.length, t4 = 0;
                else if (isFinite(t4)) t4 >>>= 0, isFinite(n3) ? (n3 >>>= 0, void 0 === r2 && (r2 = "utf8")) : (r2 = n3, n3 = void 0);
                else throw new Error("Buffer.write(string, encoding, offset[, length]) is no longer supported");
                var a = this.length - t4;
                if ((void 0 === n3 || n3 > a) && (n3 = a), 0 < e2.length && (0 > n3 || 0 > t4) || t4 > this.length) throw new RangeError("Attempt to write outside buffer bounds");
                r2 || (r2 = "utf8");
                for (var o2 = false; ; ) switch (r2) {
                  case "hex":
                    return w(this, e2, t4, n3);
                  case "utf8":
                  case "utf-8":
                    return S(this, e2, t4, n3);
                  case "ascii":
                    return T(this, e2, t4, n3);
                  case "latin1":
                  case "binary":
                    return v(this, e2, t4, n3);
                  case "base64":
                    return k(this, e2, t4, n3);
                  case "ucs2":
                  case "ucs-2":
                  case "utf16le":
                  case "utf-16le":
                    return L(this, e2, t4, n3);
                  default:
                    if (o2) throw new TypeError("Unknown encoding: " + r2);
                    r2 = ("" + r2).toLowerCase(), o2 = true;
                }
              }, s.prototype.toJSON = function() {
                return { type: "Buffer", data: Array.prototype.slice.call(this._arr || this, 0) };
              };
              s.prototype.slice = function(e2, t4) {
                var n3 = this.length;
                e2 = ~~e2, t4 = t4 === void 0 ? n3 : ~~t4, 0 > e2 ? (e2 += n3, 0 > e2 && (e2 = 0)) : e2 > n3 && (e2 = n3), 0 > t4 ? (t4 += n3, 0 > t4 && (t4 = 0)) : t4 > n3 && (t4 = n3), t4 < e2 && (t4 = e2);
                var r2 = this.subarray(e2, t4);
                return r2.__proto__ = s.prototype, r2;
              }, s.prototype.readUIntLE = function(e2, t4, n3) {
                e2 >>>= 0, t4 >>>= 0, n3 || O(e2, t4, this.length);
                for (var r2 = this[e2], a = 1, o2 = 0; ++o2 < t4 && (a *= 256); ) r2 += this[e2 + o2] * a;
                return r2;
              }, s.prototype.readUIntBE = function(e2, t4, n3) {
                e2 >>>= 0, t4 >>>= 0, n3 || O(e2, t4, this.length);
                for (var r2 = this[e2 + --t4], a = 1; 0 < t4 && (a *= 256); ) r2 += this[e2 + --t4] * a;
                return r2;
              }, s.prototype.readUInt8 = function(e2, t4) {
                return e2 >>>= 0, t4 || O(e2, 1, this.length), this[e2];
              }, s.prototype.readUInt16LE = function(e2, t4) {
                return e2 >>>= 0, t4 || O(e2, 2, this.length), this[e2] | this[e2 + 1] << 8;
              }, s.prototype.readUInt16BE = function(e2, t4) {
                return e2 >>>= 0, t4 || O(e2, 2, this.length), this[e2] << 8 | this[e2 + 1];
              }, s.prototype.readUInt32LE = function(e2, t4) {
                return e2 >>>= 0, t4 || O(e2, 4, this.length), (this[e2] | this[e2 + 1] << 8 | this[e2 + 2] << 16) + 16777216 * this[e2 + 3];
              }, s.prototype.readUInt32BE = function(e2, t4) {
                return e2 >>>= 0, t4 || O(e2, 4, this.length), 16777216 * this[e2] + (this[e2 + 1] << 16 | this[e2 + 2] << 8 | this[e2 + 3]);
              }, s.prototype.readIntLE = function(e2, t4, n3) {
                e2 >>>= 0, t4 >>>= 0, n3 || O(e2, t4, this.length);
                for (var a = this[e2], o2 = 1, d2 = 0; ++d2 < t4 && (o2 *= 256); ) a += this[e2 + d2] * o2;
                return o2 *= 128, a >= o2 && (a -= r(2, 8 * t4)), a;
              }, s.prototype.readIntBE = function(e2, t4, n3) {
                e2 >>>= 0, t4 >>>= 0, n3 || O(e2, t4, this.length);
                for (var a = t4, o2 = 1, d2 = this[e2 + --a]; 0 < a && (o2 *= 256); ) d2 += this[e2 + --a] * o2;
                return o2 *= 128, d2 >= o2 && (d2 -= r(2, 8 * t4)), d2;
              }, s.prototype.readInt8 = function(e2, t4) {
                return e2 >>>= 0, t4 || O(e2, 1, this.length), 128 & this[e2] ? -1 * (255 - this[e2] + 1) : this[e2];
              }, s.prototype.readInt16LE = function(e2, t4) {
                e2 >>>= 0, t4 || O(e2, 2, this.length);
                var n3 = this[e2] | this[e2 + 1] << 8;
                return 32768 & n3 ? 4294901760 | n3 : n3;
              }, s.prototype.readInt16BE = function(e2, t4) {
                e2 >>>= 0, t4 || O(e2, 2, this.length);
                var n3 = this[e2 + 1] | this[e2] << 8;
                return 32768 & n3 ? 4294901760 | n3 : n3;
              }, s.prototype.readInt32LE = function(e2, t4) {
                return e2 >>>= 0, t4 || O(e2, 4, this.length), this[e2] | this[e2 + 1] << 8 | this[e2 + 2] << 16 | this[e2 + 3] << 24;
              }, s.prototype.readInt32BE = function(e2, t4) {
                return e2 >>>= 0, t4 || O(e2, 4, this.length), this[e2] << 24 | this[e2 + 1] << 16 | this[e2 + 2] << 8 | this[e2 + 3];
              }, s.prototype.readFloatLE = function(e2, t4) {
                return e2 >>>= 0, t4 || O(e2, 4, this.length), J.read(this, e2, true, 23, 4);
              }, s.prototype.readFloatBE = function(e2, t4) {
                return e2 >>>= 0, t4 || O(e2, 4, this.length), J.read(this, e2, false, 23, 4);
              }, s.prototype.readDoubleLE = function(e2, t4) {
                return e2 >>>= 0, t4 || O(e2, 8, this.length), J.read(this, e2, true, 52, 8);
              }, s.prototype.readDoubleBE = function(e2, t4) {
                return e2 >>>= 0, t4 || O(e2, 8, this.length), J.read(this, e2, false, 52, 8);
              }, s.prototype.writeUIntLE = function(e2, t4, n3, a) {
                if (e2 = +e2, t4 >>>= 0, n3 >>>= 0, !a) {
                  var o2 = r(2, 8 * n3) - 1;
                  F(this, e2, t4, n3, o2, 0);
                }
                var d2 = 1, s2 = 0;
                for (this[t4] = 255 & e2; ++s2 < n3 && (d2 *= 256); ) this[t4 + s2] = 255 & e2 / d2;
                return t4 + n3;
              }, s.prototype.writeUIntBE = function(e2, t4, n3, a) {
                if (e2 = +e2, t4 >>>= 0, n3 >>>= 0, !a) {
                  var o2 = r(2, 8 * n3) - 1;
                  F(this, e2, t4, n3, o2, 0);
                }
                var d2 = n3 - 1, s2 = 1;
                for (this[t4 + d2] = 255 & e2; 0 <= --d2 && (s2 *= 256); ) this[t4 + d2] = 255 & e2 / s2;
                return t4 + n3;
              }, s.prototype.writeUInt8 = function(e2, t4, n3) {
                return e2 = +e2, t4 >>>= 0, n3 || F(this, e2, t4, 1, 255, 0), this[t4] = 255 & e2, t4 + 1;
              }, s.prototype.writeUInt16LE = function(e2, t4, n3) {
                return e2 = +e2, t4 >>>= 0, n3 || F(this, e2, t4, 2, 65535, 0), this[t4] = 255 & e2, this[t4 + 1] = e2 >>> 8, t4 + 2;
              }, s.prototype.writeUInt16BE = function(e2, t4, n3) {
                return e2 = +e2, t4 >>>= 0, n3 || F(this, e2, t4, 2, 65535, 0), this[t4] = e2 >>> 8, this[t4 + 1] = 255 & e2, t4 + 2;
              }, s.prototype.writeUInt32LE = function(e2, t4, n3) {
                return e2 = +e2, t4 >>>= 0, n3 || F(this, e2, t4, 4, 4294967295, 0), this[t4 + 3] = e2 >>> 24, this[t4 + 2] = e2 >>> 16, this[t4 + 1] = e2 >>> 8, this[t4] = 255 & e2, t4 + 4;
              }, s.prototype.writeUInt32BE = function(e2, t4, n3) {
                return e2 = +e2, t4 >>>= 0, n3 || F(this, e2, t4, 4, 4294967295, 0), this[t4] = e2 >>> 24, this[t4 + 1] = e2 >>> 16, this[t4 + 2] = e2 >>> 8, this[t4 + 3] = 255 & e2, t4 + 4;
              }, s.prototype.writeIntLE = function(e2, t4, n3, a) {
                if (e2 = +e2, t4 >>>= 0, !a) {
                  var o2 = r(2, 8 * n3 - 1);
                  F(this, e2, t4, n3, o2 - 1, -o2);
                }
                var d2 = 0, s2 = 1, l2 = 0;
                for (this[t4] = 255 & e2; ++d2 < n3 && (s2 *= 256); ) 0 > e2 && 0 === l2 && 0 !== this[t4 + d2 - 1] && (l2 = 1), this[t4 + d2] = 255 & (e2 / s2 >> 0) - l2;
                return t4 + n3;
              }, s.prototype.writeIntBE = function(e2, t4, n3, a) {
                if (e2 = +e2, t4 >>>= 0, !a) {
                  var o2 = r(2, 8 * n3 - 1);
                  F(this, e2, t4, n3, o2 - 1, -o2);
                }
                var d2 = n3 - 1, s2 = 1, l2 = 0;
                for (this[t4 + d2] = 255 & e2; 0 <= --d2 && (s2 *= 256); ) 0 > e2 && 0 === l2 && 0 !== this[t4 + d2 + 1] && (l2 = 1), this[t4 + d2] = 255 & (e2 / s2 >> 0) - l2;
                return t4 + n3;
              }, s.prototype.writeInt8 = function(e2, t4, n3) {
                return e2 = +e2, t4 >>>= 0, n3 || F(this, e2, t4, 1, 127, -128), 0 > e2 && (e2 = 255 + e2 + 1), this[t4] = 255 & e2, t4 + 1;
              }, s.prototype.writeInt16LE = function(e2, t4, n3) {
                return e2 = +e2, t4 >>>= 0, n3 || F(this, e2, t4, 2, 32767, -32768), this[t4] = 255 & e2, this[t4 + 1] = e2 >>> 8, t4 + 2;
              }, s.prototype.writeInt16BE = function(e2, t4, n3) {
                return e2 = +e2, t4 >>>= 0, n3 || F(this, e2, t4, 2, 32767, -32768), this[t4] = e2 >>> 8, this[t4 + 1] = 255 & e2, t4 + 2;
              }, s.prototype.writeInt32LE = function(e2, t4, n3) {
                return e2 = +e2, t4 >>>= 0, n3 || F(this, e2, t4, 4, 2147483647, -2147483648), this[t4] = 255 & e2, this[t4 + 1] = e2 >>> 8, this[t4 + 2] = e2 >>> 16, this[t4 + 3] = e2 >>> 24, t4 + 4;
              }, s.prototype.writeInt32BE = function(e2, t4, n3) {
                return e2 = +e2, t4 >>>= 0, n3 || F(this, e2, t4, 4, 2147483647, -2147483648), 0 > e2 && (e2 = 4294967295 + e2 + 1), this[t4] = e2 >>> 24, this[t4 + 1] = e2 >>> 16, this[t4 + 2] = e2 >>> 8, this[t4 + 3] = 255 & e2, t4 + 4;
              }, s.prototype.writeFloatLE = function(e2, t4, n3) {
                return U(this, e2, t4, true, n3);
              }, s.prototype.writeFloatBE = function(e2, t4, n3) {
                return U(this, e2, t4, false, n3);
              }, s.prototype.writeDoubleLE = function(e2, t4, n3) {
                return j(this, e2, t4, true, n3);
              }, s.prototype.writeDoubleBE = function(e2, t4, n3) {
                return j(this, e2, t4, false, n3);
              }, s.prototype.copy = function(e2, t4, n3, r2) {
                if (!s.isBuffer(e2)) throw new TypeError("argument should be a Buffer");
                if (n3 || (n3 = 0), r2 || 0 === r2 || (r2 = this.length), t4 >= e2.length && (t4 = e2.length), t4 || (t4 = 0), 0 < r2 && r2 < n3 && (r2 = n3), r2 === n3) return 0;
                if (0 === e2.length || 0 === this.length) return 0;
                if (0 > t4) throw new RangeError("targetStart out of bounds");
                if (0 > n3 || n3 >= this.length) throw new RangeError("Index out of range");
                if (0 > r2) throw new RangeError("sourceEnd out of bounds");
                r2 > this.length && (r2 = this.length), e2.length - t4 < r2 - n3 && (r2 = e2.length - t4 + n3);
                var a = r2 - n3;
                if (this === e2 && "function" == typeof Uint8Array.prototype.copyWithin) this.copyWithin(t4, n3, r2);
                else if (this === e2 && n3 < t4 && t4 < r2) for (var o2 = a - 1; 0 <= o2; --o2) e2[o2 + t4] = this[o2 + n3];
                else Uint8Array.prototype.set.call(e2, this.subarray(n3, r2), t4);
                return a;
              }, s.prototype.fill = function(e2, t4, n3, r2) {
                if ("string" == typeof e2) {
                  if ("string" == typeof t4 ? (r2 = t4, t4 = 0, n3 = this.length) : "string" == typeof n3 && (r2 = n3, n3 = this.length), void 0 !== r2 && "string" != typeof r2) throw new TypeError("encoding must be a string");
                  if ("string" == typeof r2 && !s.isEncoding(r2)) throw new TypeError("Unknown encoding: " + r2);
                  if (1 === e2.length) {
                    var a = e2.charCodeAt(0);
                    ("utf8" === r2 && 128 > a || "latin1" === r2) && (e2 = a);
                  }
                } else "number" == typeof e2 && (e2 &= 255);
                if (0 > t4 || this.length < t4 || this.length < n3) throw new RangeError("Out of range index");
                if (n3 <= t4) return this;
                t4 >>>= 0, n3 = n3 === void 0 ? this.length : n3 >>> 0, e2 || (e2 = 0);
                var o2;
                if ("number" == typeof e2) for (o2 = t4; o2 < n3; ++o2) this[o2] = e2;
                else {
                  var d2 = s.isBuffer(e2) ? e2 : s.from(e2, r2), l2 = d2.length;
                  if (0 === l2) throw new TypeError('The value "' + e2 + '" is invalid for argument "value"');
                  for (o2 = 0; o2 < n3 - t4; ++o2) this[o2 + t4] = d2[o2 % l2];
                }
                return this;
              };
              var Q = /[^+/0-9A-Za-z-_]/g;
            }).call(this);
          }).call(this, e("buffer").Buffer);
        }, { "base64-js": 1, buffer: 3, ieee754: 9 }], 4: [function(e, t2, n2) {
          (function(a) {
            (function() {
              function r2() {
                let e2;
                try {
                  e2 = n2.storage.getItem("debug");
                } catch (e3) {
                }
                return !e2 && "undefined" != typeof a && "env" in a && (e2 = a.env.DEBUG), e2;
              }
              n2.formatArgs = function(e2) {
                if (e2[0] = (this.useColors ? "%c" : "") + this.namespace + (this.useColors ? " %c" : " ") + e2[0] + (this.useColors ? "%c " : " ") + "+" + t2.exports.humanize(this.diff), !this.useColors) return;
                const n3 = "color: " + this.color;
                e2.splice(1, 0, n3, "color: inherit");
                let r3 = 0, a2 = 0;
                e2[0].replace(/%[a-zA-Z%]/g, (e3) => {
                  "%%" === e3 || (r3++, "%c" === e3 && (a2 = r3));
                }), e2.splice(a2, 0, n3);
              }, n2.save = function(e2) {
                try {
                  e2 ? n2.storage.setItem("debug", e2) : n2.storage.removeItem("debug");
                } catch (e3) {
                }
              }, n2.load = r2, n2.useColors = function() {
                return !!("undefined" != typeof window && window.process && ("renderer" === window.process.type || window.process.__nwjs)) || !("undefined" != typeof navigator && navigator.userAgent && navigator.userAgent.toLowerCase().match(/(edge|trident)\/(\d+)/)) && ("undefined" != typeof document && document.documentElement && document.documentElement.style && document.documentElement.style.WebkitAppearance || "undefined" != typeof window && window.console && (window.console.firebug || window.console.exception && window.console.table) || "undefined" != typeof navigator && navigator.userAgent && navigator.userAgent.toLowerCase().match(/firefox\/(\d+)/) && 31 <= parseInt(RegExp.$1, 10) || "undefined" != typeof navigator && navigator.userAgent && navigator.userAgent.toLowerCase().match(/applewebkit\/(\d+)/));
              }, n2.storage = (function() {
                try {
                  return localStorage;
                } catch (e2) {
                }
              })(), n2.destroy = /* @__PURE__ */ (() => {
                let e2 = false;
                return () => {
                  e2 || (e2 = true, console.warn("Instance method `debug.destroy()` is deprecated and no longer does anything. It will be removed in the next major version of `debug`."));
                };
              })(), n2.colors = ["#0000CC", "#0000FF", "#0033CC", "#0033FF", "#0066CC", "#0066FF", "#0099CC", "#0099FF", "#00CC00", "#00CC33", "#00CC66", "#00CC99", "#00CCCC", "#00CCFF", "#3300CC", "#3300FF", "#3333CC", "#3333FF", "#3366CC", "#3366FF", "#3399CC", "#3399FF", "#33CC00", "#33CC33", "#33CC66", "#33CC99", "#33CCCC", "#33CCFF", "#6600CC", "#6600FF", "#6633CC", "#6633FF", "#66CC00", "#66CC33", "#9900CC", "#9900FF", "#9933CC", "#9933FF", "#99CC00", "#99CC33", "#CC0000", "#CC0033", "#CC0066", "#CC0099", "#CC00CC", "#CC00FF", "#CC3300", "#CC3333", "#CC3366", "#CC3399", "#CC33CC", "#CC33FF", "#CC6600", "#CC6633", "#CC9900", "#CC9933", "#CCCC00", "#CCCC33", "#FF0000", "#FF0033", "#FF0066", "#FF0099", "#FF00CC", "#FF00FF", "#FF3300", "#FF3333", "#FF3366", "#FF3399", "#FF33CC", "#FF33FF", "#FF6600", "#FF6633", "#FF9900", "#FF9933", "#FFCC00", "#FFCC33"], n2.log = console.debug || console.log || (() => {
              }), t2.exports = e("./common")(n2);
              const { formatters: o } = t2.exports;
              o.j = function(e2) {
                try {
                  return JSON.stringify(e2);
                } catch (e3) {
                  return "[UnexpectedJSONParseError]: " + e3.message;
                }
              };
            }).call(this);
          }).call(this, e("_process"));
        }, { "./common": 5, _process: 12 }], 5: [function(e, t2) {
          t2.exports = function(t3) {
            function r2(e2) {
              function t4(...e3) {
                if (!t4.enabled) return;
                const a2 = t4, o3 = +/* @__PURE__ */ new Date(), i = o3 - (n2 || o3);
                a2.diff = i, a2.prev = n2, a2.curr = o3, n2 = o3, e3[0] = r2.coerce(e3[0]), "string" != typeof e3[0] && e3.unshift("%O");
                let d = 0;
                e3[0] = e3[0].replace(/%([a-zA-Z%])/g, (t5, n3) => {
                  if ("%%" === t5) return "%";
                  d++;
                  const o4 = r2.formatters[n3];
                  if ("function" == typeof o4) {
                    const n4 = e3[d];
                    t5 = o4.call(a2, n4), e3.splice(d, 1), d--;
                  }
                  return t5;
                }), r2.formatArgs.call(a2, e3);
                const s = a2.log || r2.log;
                s.apply(a2, e3);
              }
              let n2, o2 = null;
              return t4.namespace = e2, t4.useColors = r2.useColors(), t4.color = r2.selectColor(e2), t4.extend = a, t4.destroy = r2.destroy, Object.defineProperty(t4, "enabled", { enumerable: true, configurable: false, get: () => null === o2 ? r2.enabled(e2) : o2, set: (e3) => {
                o2 = e3;
              } }), "function" == typeof r2.init && r2.init(t4), t4;
            }
            function a(e2, t4) {
              const n2 = r2(this.namespace + ("undefined" == typeof t4 ? ":" : t4) + e2);
              return n2.log = this.log, n2;
            }
            function o(e2) {
              return e2.toString().substring(2, e2.toString().length - 2).replace(/\.\*\?$/, "*");
            }
            return r2.debug = r2, r2.default = r2, r2.coerce = function(e2) {
              return e2 instanceof Error ? e2.stack || e2.message : e2;
            }, r2.disable = function() {
              const e2 = [...r2.names.map(o), ...r2.skips.map(o).map((e3) => "-" + e3)].join(",");
              return r2.enable(""), e2;
            }, r2.enable = function(e2) {
              r2.save(e2), r2.names = [], r2.skips = [];
              let t4;
              const n2 = ("string" == typeof e2 ? e2 : "").split(/[\s,]+/), a2 = n2.length;
              for (t4 = 0; t4 < a2; t4++) n2[t4] && (e2 = n2[t4].replace(/\*/g, ".*?"), "-" === e2[0] ? r2.skips.push(new RegExp("^" + e2.substr(1) + "$")) : r2.names.push(new RegExp("^" + e2 + "$")));
            }, r2.enabled = function(e2) {
              if ("*" === e2[e2.length - 1]) return true;
              let t4, n2;
              for (t4 = 0, n2 = r2.skips.length; t4 < n2; t4++) if (r2.skips[t4].test(e2)) return false;
              for (t4 = 0, n2 = r2.names.length; t4 < n2; t4++) if (r2.names[t4].test(e2)) return true;
              return false;
            }, r2.humanize = e("ms"), r2.destroy = function() {
              console.warn("Instance method `debug.destroy()` is deprecated and no longer does anything. It will be removed in the next major version of `debug`.");
            }, Object.keys(t3).forEach((e2) => {
              r2[e2] = t3[e2];
            }), r2.names = [], r2.skips = [], r2.formatters = {}, r2.selectColor = function(e2) {
              let t4 = 0;
              for (let n2 = 0; n2 < e2.length; n2++) t4 = (t4 << 5) - t4 + e2.charCodeAt(n2), t4 |= 0;
              return r2.colors[n(t4) % r2.colors.length];
            }, r2.enable(r2.load()), r2;
          };
        }, { ms: 11 }], 6: [function(e, t2) {
          "use strict";
          function n2(e2, t3) {
            for (const n3 in t3) Object.defineProperty(e2, n3, { value: t3[n3], enumerable: true, configurable: true });
            return e2;
          }
          t2.exports = function(e2, t3, r2) {
            if (!e2 || "string" == typeof e2) throw new TypeError("Please pass an Error to err-code");
            r2 || (r2 = {}), "object" == typeof t3 && (r2 = t3, t3 = ""), t3 && (r2.code = t3);
            try {
              return n2(e2, r2);
            } catch (t4) {
              r2.message = e2.message, r2.stack = e2.stack;
              const a = function() {
              };
              a.prototype = Object.create(Object.getPrototypeOf(e2));
              const o = n2(new a(), r2);
              return o;
            }
          };
        }, {}], 7: [function(e, t2) {
          "use strict";
          function n2(e2) {
            console && console.warn && console.warn(e2);
          }
          function r2() {
            r2.init.call(this);
          }
          function a(e2) {
            if ("function" != typeof e2) throw new TypeError('The "listener" argument must be of type Function. Received type ' + typeof e2);
          }
          function o(e2) {
            return void 0 === e2._maxListeners ? r2.defaultMaxListeners : e2._maxListeners;
          }
          function i(e2, t3, r3, i2) {
            var d2, s2, l2;
            if (a(r3), s2 = e2._events, void 0 === s2 ? (s2 = e2._events = /* @__PURE__ */ Object.create(null), e2._eventsCount = 0) : (void 0 !== s2.newListener && (e2.emit("newListener", t3, r3.listener ? r3.listener : r3), s2 = e2._events), l2 = s2[t3]), void 0 === l2) l2 = s2[t3] = r3, ++e2._eventsCount;
            else if ("function" == typeof l2 ? l2 = s2[t3] = i2 ? [r3, l2] : [l2, r3] : i2 ? l2.unshift(r3) : l2.push(r3), d2 = o(e2), 0 < d2 && l2.length > d2 && !l2.warned) {
              l2.warned = true;
              var c2 = new Error("Possible EventEmitter memory leak detected. " + l2.length + " " + (t3 + " listeners added. Use emitter.setMaxListeners() to increase limit"));
              c2.name = "MaxListenersExceededWarning", c2.emitter = e2, c2.type = t3, c2.count = l2.length, n2(c2);
            }
            return e2;
          }
          function d() {
            if (!this.fired) return this.target.removeListener(this.type, this.wrapFn), this.fired = true, 0 === arguments.length ? this.listener.call(this.target) : this.listener.apply(this.target, arguments);
          }
          function s(e2, t3, n3) {
            var r3 = { fired: false, wrapFn: void 0, target: e2, type: t3, listener: n3 }, a2 = d.bind(r3);
            return a2.listener = n3, r3.wrapFn = a2, a2;
          }
          function l(e2, t3, n3) {
            var r3 = e2._events;
            if (r3 === void 0) return [];
            var a2 = r3[t3];
            return void 0 === a2 ? [] : "function" == typeof a2 ? n3 ? [a2.listener || a2] : [a2] : n3 ? f(a2) : u(a2, a2.length);
          }
          function c(e2) {
            var t3 = this._events;
            if (t3 !== void 0) {
              var n3 = t3[e2];
              if ("function" == typeof n3) return 1;
              if (void 0 !== n3) return n3.length;
            }
            return 0;
          }
          function u(e2, t3) {
            for (var n3 = Array(t3), r3 = 0; r3 < t3; ++r3) n3[r3] = e2[r3];
            return n3;
          }
          function p(e2, t3) {
            for (; t3 + 1 < e2.length; t3++) e2[t3] = e2[t3 + 1];
            e2.pop();
          }
          function f(e2) {
            for (var t3 = Array(e2.length), n3 = 0; n3 < t3.length; ++n3) t3[n3] = e2[n3].listener || e2[n3];
            return t3;
          }
          function g(e2, t3, n3) {
            "function" == typeof e2.on && _(e2, "error", t3, n3);
          }
          function _(e2, t3, n3, r3) {
            if ("function" == typeof e2.on) r3.once ? e2.once(t3, n3) : e2.on(t3, n3);
            else if ("function" == typeof e2.addEventListener) e2.addEventListener(t3, function a2(o2) {
              r3.once && e2.removeEventListener(t3, a2), n3(o2);
            });
            else throw new TypeError('The "emitter" argument must be of type EventEmitter. Received type ' + typeof e2);
          }
          var h, m = "object" == typeof Reflect ? Reflect : null, b = m && "function" == typeof m.apply ? m.apply : function(e2, t3, n3) {
            return Function.prototype.apply.call(e2, t3, n3);
          };
          h = m && "function" == typeof m.ownKeys ? m.ownKeys : Object.getOwnPropertySymbols ? function(e2) {
            return Object.getOwnPropertyNames(e2).concat(Object.getOwnPropertySymbols(e2));
          } : function(e2) {
            return Object.getOwnPropertyNames(e2);
          };
          var y = Number.isNaN || function(e2) {
            return e2 !== e2;
          };
          t2.exports = r2, t2.exports.once = function(e2, t3) {
            return new Promise(function(n3, r3) {
              function a2(n4) {
                e2.removeListener(t3, o2), r3(n4);
              }
              function o2() {
                "function" == typeof e2.removeListener && e2.removeListener("error", a2), n3([].slice.call(arguments));
              }
              _(e2, t3, o2, { once: true }), "error" !== t3 && g(e2, a2, { once: true });
            });
          }, r2.EventEmitter = r2, r2.prototype._events = void 0, r2.prototype._eventsCount = 0, r2.prototype._maxListeners = void 0;
          var C = 10;
          Object.defineProperty(r2, "defaultMaxListeners", { enumerable: true, get: function() {
            return C;
          }, set: function(e2) {
            if ("number" != typeof e2 || 0 > e2 || y(e2)) throw new RangeError('The value of "defaultMaxListeners" is out of range. It must be a non-negative number. Received ' + e2 + ".");
            C = e2;
          } }), r2.init = function() {
            (this._events === void 0 || this._events === Object.getPrototypeOf(this)._events) && (this._events = /* @__PURE__ */ Object.create(null), this._eventsCount = 0), this._maxListeners = this._maxListeners || void 0;
          }, r2.prototype.setMaxListeners = function(e2) {
            if ("number" != typeof e2 || 0 > e2 || y(e2)) throw new RangeError('The value of "n" is out of range. It must be a non-negative number. Received ' + e2 + ".");
            return this._maxListeners = e2, this;
          }, r2.prototype.getMaxListeners = function() {
            return o(this);
          }, r2.prototype.emit = function(e2) {
            for (var t3 = [], n3 = 1; n3 < arguments.length; n3++) t3.push(arguments[n3]);
            var r3 = "error" === e2, a2 = this._events;
            if (a2 !== void 0) r3 = r3 && a2.error === void 0;
            else if (!r3) return false;
            if (r3) {
              var o2;
              if (0 < t3.length && (o2 = t3[0]), o2 instanceof Error) throw o2;
              var d2 = new Error("Unhandled error." + (o2 ? " (" + o2.message + ")" : ""));
              throw d2.context = o2, d2;
            }
            var s2 = a2[e2];
            if (s2 === void 0) return false;
            if ("function" == typeof s2) b(s2, this, t3);
            else for (var l2 = s2.length, c2 = u(s2, l2), n3 = 0; n3 < l2; ++n3) b(c2[n3], this, t3);
            return true;
          }, r2.prototype.addListener = function(e2, t3) {
            return i(this, e2, t3, false);
          }, r2.prototype.on = r2.prototype.addListener, r2.prototype.prependListener = function(e2, t3) {
            return i(this, e2, t3, true);
          }, r2.prototype.once = function(e2, t3) {
            return a(t3), this.on(e2, s(this, e2, t3)), this;
          }, r2.prototype.prependOnceListener = function(e2, t3) {
            return a(t3), this.prependListener(e2, s(this, e2, t3)), this;
          }, r2.prototype.removeListener = function(e2, t3) {
            var n3, r3, o2, d2, s2;
            if (a(t3), r3 = this._events, void 0 === r3) return this;
            if (n3 = r3[e2], void 0 === n3) return this;
            if (n3 === t3 || n3.listener === t3) 0 == --this._eventsCount ? this._events = /* @__PURE__ */ Object.create(null) : (delete r3[e2], r3.removeListener && this.emit("removeListener", e2, n3.listener || t3));
            else if ("function" != typeof n3) {
              for (o2 = -1, d2 = n3.length - 1; 0 <= d2; d2--) if (n3[d2] === t3 || n3[d2].listener === t3) {
                s2 = n3[d2].listener, o2 = d2;
                break;
              }
              if (0 > o2) return this;
              0 === o2 ? n3.shift() : p(n3, o2), 1 === n3.length && (r3[e2] = n3[0]), void 0 !== r3.removeListener && this.emit("removeListener", e2, s2 || t3);
            }
            return this;
          }, r2.prototype.off = r2.prototype.removeListener, r2.prototype.removeAllListeners = function(e2) {
            var t3, n3, r3;
            if (n3 = this._events, void 0 === n3) return this;
            if (void 0 === n3.removeListener) return 0 === arguments.length ? (this._events = /* @__PURE__ */ Object.create(null), this._eventsCount = 0) : void 0 !== n3[e2] && (0 == --this._eventsCount ? this._events = /* @__PURE__ */ Object.create(null) : delete n3[e2]), this;
            if (0 === arguments.length) {
              var a2, o2 = Object.keys(n3);
              for (r3 = 0; r3 < o2.length; ++r3) a2 = o2[r3], "removeListener" !== a2 && this.removeAllListeners(a2);
              return this.removeAllListeners("removeListener"), this._events = /* @__PURE__ */ Object.create(null), this._eventsCount = 0, this;
            }
            if (t3 = n3[e2], "function" == typeof t3) this.removeListener(e2, t3);
            else if (void 0 !== t3) for (r3 = t3.length - 1; 0 <= r3; r3--) this.removeListener(e2, t3[r3]);
            return this;
          }, r2.prototype.listeners = function(e2) {
            return l(this, e2, true);
          }, r2.prototype.rawListeners = function(e2) {
            return l(this, e2, false);
          }, r2.listenerCount = function(e2, t3) {
            return "function" == typeof e2.listenerCount ? e2.listenerCount(t3) : c.call(e2, t3);
          }, r2.prototype.listenerCount = c, r2.prototype.eventNames = function() {
            return 0 < this._eventsCount ? h(this._events) : [];
          };
        }, {}], 8: [function(e, t2) {
          t2.exports = function() {
            if ("undefined" == typeof globalThis) return null;
            var e2 = { RTCPeerConnection: globalThis.RTCPeerConnection || globalThis.mozRTCPeerConnection || globalThis.webkitRTCPeerConnection, RTCSessionDescription: globalThis.RTCSessionDescription || globalThis.mozRTCSessionDescription || globalThis.webkitRTCSessionDescription, RTCIceCandidate: globalThis.RTCIceCandidate || globalThis.mozRTCIceCandidate || globalThis.webkitRTCIceCandidate };
            return e2.RTCPeerConnection ? e2 : null;
          };
        }, {}], 9: [function(e, a, o) {
          o.read = function(t2, n2, a2, o2, l) {
            var c, u, p = 8 * l - o2 - 1, f = (1 << p) - 1, g = f >> 1, _ = -7, h = a2 ? l - 1 : 0, b = a2 ? -1 : 1, d = t2[n2 + h];
            for (h += b, c = d & (1 << -_) - 1, d >>= -_, _ += p; 0 < _; c = 256 * c + t2[n2 + h], h += b, _ -= 8) ;
            for (u = c & (1 << -_) - 1, c >>= -_, _ += o2; 0 < _; u = 256 * u + t2[n2 + h], h += b, _ -= 8) ;
            if (0 === c) c = 1 - g;
            else {
              if (c === f) return u ? NaN : (d ? -1 : 1) * (1 / 0);
              u += r(2, o2), c -= g;
            }
            return (d ? -1 : 1) * u * r(2, c - o2);
          }, o.write = function(a2, o2, l, u, p, f) {
            var h, b, y, g = Math.LN2, _ = Math.log, C = 8 * f - p - 1, R = (1 << C) - 1, E = R >> 1, w = 23 === p ? r(2, -24) - r(2, -77) : 0, S = u ? 0 : f - 1, T = u ? 1 : -1, d = 0 > o2 || 0 === o2 && 0 > 1 / o2 ? 1 : 0;
            for (o2 = n(o2), isNaN(o2) || o2 === 1 / 0 ? (b = isNaN(o2) ? 1 : 0, h = R) : (h = t(_(o2) / g), 1 > o2 * (y = r(2, -h)) && (h--, y *= 2), o2 += 1 <= h + E ? w / y : w * r(2, 1 - E), 2 <= o2 * y && (h++, y /= 2), h + E >= R ? (b = 0, h = R) : 1 <= h + E ? (b = (o2 * y - 1) * r(2, p), h += E) : (b = o2 * r(2, E - 1) * r(2, p), h = 0)); 8 <= p; a2[l + S] = 255 & b, S += T, b /= 256, p -= 8) ;
            for (h = h << p | b, C += p; 0 < C; a2[l + S] = 255 & h, S += T, h /= 256, C -= 8) ;
            a2[l + S - T] |= 128 * d;
          };
        }, {}], 10: [function(e, t2) {
          t2.exports = "function" == typeof Object.create ? function(e2, t3) {
            t3 && (e2.super_ = t3, e2.prototype = Object.create(t3.prototype, { constructor: { value: e2, enumerable: false, writable: true, configurable: true } }));
          } : function(e2, t3) {
            if (t3) {
              e2.super_ = t3;
              var n2 = function() {
              };
              n2.prototype = t3.prototype, e2.prototype = new n2(), e2.prototype.constructor = e2;
            }
          };
        }, {}], 11: [function(e, t2) {
          var r2 = Math.round;
          function a(e2) {
            if (e2 += "", !(100 < e2.length)) {
              var t3 = /^(-?(?:\d+)?\.?\d+) *(milliseconds?|msecs?|ms|seconds?|secs?|s|minutes?|mins?|m|hours?|hrs?|h|days?|d|weeks?|w|years?|yrs?|y)?$/i.exec(e2);
              if (t3) {
                var r3 = parseFloat(t3[1]), n2 = (t3[2] || "ms").toLowerCase();
                return "years" === n2 || "year" === n2 || "yrs" === n2 || "yr" === n2 || "y" === n2 ? 315576e5 * r3 : "weeks" === n2 || "week" === n2 || "w" === n2 ? 6048e5 * r3 : "days" === n2 || "day" === n2 || "d" === n2 ? 864e5 * r3 : "hours" === n2 || "hour" === n2 || "hrs" === n2 || "hr" === n2 || "h" === n2 ? 36e5 * r3 : "minutes" === n2 || "minute" === n2 || "mins" === n2 || "min" === n2 || "m" === n2 ? 6e4 * r3 : "seconds" === n2 || "second" === n2 || "secs" === n2 || "sec" === n2 || "s" === n2 ? 1e3 * r3 : "milliseconds" === n2 || "millisecond" === n2 || "msecs" === n2 || "msec" === n2 || "ms" === n2 ? r3 : void 0;
              }
            }
          }
          function o(e2) {
            var t3 = n(e2);
            return 864e5 <= t3 ? r2(e2 / 864e5) + "d" : 36e5 <= t3 ? r2(e2 / 36e5) + "h" : 6e4 <= t3 ? r2(e2 / 6e4) + "m" : 1e3 <= t3 ? r2(e2 / 1e3) + "s" : e2 + "ms";
          }
          function i(e2) {
            var t3 = n(e2);
            return 864e5 <= t3 ? s(e2, t3, 864e5, "day") : 36e5 <= t3 ? s(e2, t3, 36e5, "hour") : 6e4 <= t3 ? s(e2, t3, 6e4, "minute") : 1e3 <= t3 ? s(e2, t3, 1e3, "second") : e2 + " ms";
          }
          function s(e2, t3, a2, n2) {
            return r2(e2 / a2) + " " + n2 + (t3 >= 1.5 * a2 ? "s" : "");
          }
          var l = 24 * (60 * 6e4);
          t2.exports = function(e2, t3) {
            t3 = t3 || {};
            var n2 = typeof e2;
            if ("string" == n2 && 0 < e2.length) return a(e2);
            if ("number" === n2 && isFinite(e2)) return t3.long ? i(e2) : o(e2);
            throw new Error("val is not a non-empty string or a valid number. val=" + JSON.stringify(e2));
          };
        }, {}], 12: [function(e, t2) {
          function n2() {
            throw new Error("setTimeout has not been defined");
          }
          function r2() {
            throw new Error("clearTimeout has not been defined");
          }
          function a(t3) {
            if (c === setTimeout) return setTimeout(t3, 0);
            if ((c === n2 || !c) && setTimeout) return c = setTimeout, setTimeout(t3, 0);
            try {
              return c(t3, 0);
            } catch (n3) {
              try {
                return c.call(null, t3, 0);
              } catch (n4) {
                return c.call(this, t3, 0);
              }
            }
          }
          function o(t3) {
            if (u === clearTimeout) return clearTimeout(t3);
            if ((u === r2 || !u) && clearTimeout) return u = clearTimeout, clearTimeout(t3);
            try {
              return u(t3);
            } catch (n3) {
              try {
                return u.call(null, t3);
              } catch (n4) {
                return u.call(this, t3);
              }
            }
          }
          function i() {
            _ && f && (_ = false, f.length ? g = f.concat(g) : h = -1, g.length && d());
          }
          function d() {
            if (!_) {
              var e2 = a(i);
              _ = true;
              for (var t3 = g.length; t3; ) {
                for (f = g, g = []; ++h < t3; ) f && f[h].run();
                h = -1, t3 = g.length;
              }
              f = null, _ = false, o(e2);
            }
          }
          function s(e2, t3) {
            this.fun = e2, this.array = t3;
          }
          function l() {
          }
          var c, u, p = t2.exports = {};
          (function() {
            try {
              c = "function" == typeof setTimeout ? setTimeout : n2;
            } catch (t3) {
              c = n2;
            }
            try {
              u = "function" == typeof clearTimeout ? clearTimeout : r2;
            } catch (t3) {
              u = r2;
            }
          })();
          var f, g = [], _ = false, h = -1;
          p.nextTick = function(e2) {
            var t3 = Array(arguments.length - 1);
            if (1 < arguments.length) for (var n3 = 1; n3 < arguments.length; n3++) t3[n3 - 1] = arguments[n3];
            g.push(new s(e2, t3)), 1 !== g.length || _ || a(d);
          }, s.prototype.run = function() {
            this.fun.apply(null, this.array);
          }, p.title = "browser", p.browser = true, p.env = {}, p.argv = [], p.version = "", p.versions = {}, p.on = l, p.addListener = l, p.once = l, p.off = l, p.removeListener = l, p.removeAllListeners = l, p.emit = l, p.prependListener = l, p.prependOnceListener = l, p.listeners = function() {
            return [];
          }, p.binding = function() {
            throw new Error("process.binding is not supported");
          }, p.cwd = function() {
            return "/";
          }, p.chdir = function() {
            throw new Error("process.chdir is not supported");
          }, p.umask = function() {
            return 0;
          };
        }, {}], 13: [function(e, t2) {
          (function(e2) {
            (function() {
              let n2;
              t2.exports = "function" == typeof queueMicrotask ? queueMicrotask.bind("undefined" == typeof window ? e2 : window) : (e3) => (n2 || (n2 = Promise.resolve())).then(e3).catch((e4) => setTimeout(() => {
                throw e4;
              }, 0));
            }).call(this);
          }).call(this, "undefined" == typeof global ? "undefined" == typeof self ? "undefined" == typeof window ? {} : window : self : global);
        }, {}], 14: [function(e, t2) {
          (function(n2, r2) {
            (function() {
              "use strict";
              var a = e("safe-buffer").Buffer, o = r2.crypto || r2.msCrypto;
              t2.exports = o && o.getRandomValues ? function(e2, t3) {
                if (e2 > 4294967295) throw new RangeError("requested too many random bytes");
                var r3 = a.allocUnsafe(e2);
                if (0 < e2) if (65536 < e2) for (var i = 0; i < e2; i += 65536) o.getRandomValues(r3.slice(i, i + 65536));
                else o.getRandomValues(r3);
                return "function" == typeof t3 ? n2.nextTick(function() {
                  t3(null, r3);
                }) : r3;
              } : function() {
                throw new Error("Secure random number generation is not supported by this browser.\nUse Chrome, Firefox or Internet Explorer 11");
              };
            }).call(this);
          }).call(this, e("_process"), "undefined" == typeof global ? "undefined" == typeof self ? "undefined" == typeof window ? {} : window : self : global);
        }, { _process: 12, "safe-buffer": 30 }], 15: [function(e, t2) {
          "use strict";
          function n2(e2, t3) {
            e2.prototype = Object.create(t3.prototype), e2.prototype.constructor = e2, e2.__proto__ = t3;
          }
          function r2(e2, t3, r3) {
            function a2(e3, n3, r4) {
              return "string" == typeof t3 ? t3 : t3(e3, n3, r4);
            }
            r3 || (r3 = Error);
            var o2 = (function(e3) {
              function t4(t5, n3, r4) {
                return e3.call(this, a2(t5, n3, r4)) || this;
              }
              return n2(t4, e3), t4;
            })(r3);
            o2.prototype.name = r3.name, o2.prototype.code = e2, s[e2] = o2;
          }
          function a(e2, t3) {
            if (Array.isArray(e2)) {
              var n3 = e2.length;
              return e2 = e2.map(function(e3) {
                return e3 + "";
              }), 2 < n3 ? "one of ".concat(t3, " ").concat(e2.slice(0, n3 - 1).join(", "), ", or ") + e2[n3 - 1] : 2 === n3 ? "one of ".concat(t3, " ").concat(e2[0], " or ").concat(e2[1]) : "of ".concat(t3, " ").concat(e2[0]);
            }
            return "of ".concat(t3, " ").concat(e2 + "");
          }
          function o(e2, t3, n3) {
            return e2.substr(!n3 || 0 > n3 ? 0 : +n3, t3.length) === t3;
          }
          function i(e2, t3, n3) {
            return (void 0 === n3 || n3 > e2.length) && (n3 = e2.length), e2.substring(n3 - t3.length, n3) === t3;
          }
          function d(e2, t3, n3) {
            return "number" != typeof n3 && (n3 = 0), !(n3 + t3.length > e2.length) && -1 !== e2.indexOf(t3, n3);
          }
          var s = {};
          r2("ERR_INVALID_OPT_VALUE", function(e2, t3) {
            return 'The value "' + t3 + '" is invalid for option "' + e2 + '"';
          }, TypeError), r2("ERR_INVALID_ARG_TYPE", function(e2, t3, n3) {
            var r3;
            "string" == typeof t3 && o(t3, "not ") ? (r3 = "must not be", t3 = t3.replace(/^not /, "")) : r3 = "must be";
            var s2;
            if (i(e2, " argument")) s2 = "The ".concat(e2, " ").concat(r3, " ").concat(a(t3, "type"));
            else {
              var l = d(e2, ".") ? "property" : "argument";
              s2 = 'The "'.concat(e2, '" ').concat(l, " ").concat(r3, " ").concat(a(t3, "type"));
            }
            return s2 += ". Received type ".concat(typeof n3), s2;
          }, TypeError), r2("ERR_STREAM_PUSH_AFTER_EOF", "stream.push() after EOF"), r2("ERR_METHOD_NOT_IMPLEMENTED", function(e2) {
            return "The " + e2 + " method is not implemented";
          }), r2("ERR_STREAM_PREMATURE_CLOSE", "Premature close"), r2("ERR_STREAM_DESTROYED", function(e2) {
            return "Cannot call " + e2 + " after a stream was destroyed";
          }), r2("ERR_MULTIPLE_CALLBACK", "Callback called multiple times"), r2("ERR_STREAM_CANNOT_PIPE", "Cannot pipe, not readable"), r2("ERR_STREAM_WRITE_AFTER_END", "write after end"), r2("ERR_STREAM_NULL_VALUES", "May not write null values to stream", TypeError), r2("ERR_UNKNOWN_ENCODING", function(e2) {
            return "Unknown encoding: " + e2;
          }, TypeError), r2("ERR_STREAM_UNSHIFT_AFTER_END_EVENT", "stream.unshift() after end event"), t2.exports.codes = s;
        }, {}], 16: [function(e, t2) {
          (function(n2) {
            (function() {
              "use strict";
              function r2(e2) {
                return this instanceof r2 ? void (d.call(this, e2), s.call(this, e2), this.allowHalfOpen = true, e2 && (false === e2.readable && (this.readable = false), false === e2.writable && (this.writable = false), false === e2.allowHalfOpen && (this.allowHalfOpen = false, this.once("end", a)))) : new r2(e2);
              }
              function a() {
                this._writableState.ended || n2.nextTick(o, this);
              }
              function o(e2) {
                e2.end();
              }
              var i = Object.keys || function(e2) {
                var t3 = [];
                for (var n3 in e2) t3.push(n3);
                return t3;
              };
              t2.exports = r2;
              var d = e("./_stream_readable"), s = e("./_stream_writable");
              e("inherits")(r2, d);
              for (var l, c = i(s.prototype), u = 0; u < c.length; u++) l = c[u], r2.prototype[l] || (r2.prototype[l] = s.prototype[l]);
              Object.defineProperty(r2.prototype, "writableHighWaterMark", { enumerable: false, get: function() {
                return this._writableState.highWaterMark;
              } }), Object.defineProperty(r2.prototype, "writableBuffer", { enumerable: false, get: function() {
                return this._writableState && this._writableState.getBuffer();
              } }), Object.defineProperty(r2.prototype, "writableLength", { enumerable: false, get: function() {
                return this._writableState.length;
              } }), Object.defineProperty(r2.prototype, "destroyed", { enumerable: false, get: function() {
                return void 0 !== this._readableState && void 0 !== this._writableState && this._readableState.destroyed && this._writableState.destroyed;
              }, set: function(e2) {
                void 0 === this._readableState || void 0 === this._writableState || (this._readableState.destroyed = e2, this._writableState.destroyed = e2);
              } });
            }).call(this);
          }).call(this, e("_process"));
        }, { "./_stream_readable": 18, "./_stream_writable": 20, _process: 12, inherits: 10 }], 17: [function(e, t2) {
          "use strict";
          function n2(e2) {
            return this instanceof n2 ? void r2.call(this, e2) : new n2(e2);
          }
          t2.exports = n2;
          var r2 = e("./_stream_transform");
          e("inherits")(n2, r2), n2.prototype._transform = function(e2, t3, n3) {
            n3(null, e2);
          };
        }, { "./_stream_transform": 19, inherits: 10 }], 18: [function(e, t2) {
          (function(n2, r2) {
            (function() {
              "use strict";
              function a(e2) {
                return P.from(e2);
              }
              function o(e2) {
                return P.isBuffer(e2) || e2 instanceof M;
              }
              function i(e2, t3, n3) {
                return "function" == typeof e2.prependListener ? e2.prependListener(t3, n3) : void (e2._events && e2._events[t3] ? Array.isArray(e2._events[t3]) ? e2._events[t3].unshift(n3) : e2._events[t3] = [n3, e2._events[t3]] : e2.on(t3, n3));
              }
              function d(t3, n3, r3) {
                A = A || e("./_stream_duplex"), t3 = t3 || {}, "boolean" != typeof r3 && (r3 = n3 instanceof A), this.objectMode = !!t3.objectMode, r3 && (this.objectMode = this.objectMode || !!t3.readableObjectMode), this.highWaterMark = H(this, t3, "readableHighWaterMark", r3), this.buffer = new j(), this.length = 0, this.pipes = null, this.pipesCount = 0, this.flowing = null, this.ended = false, this.endEmitted = false, this.reading = false, this.sync = true, this.needReadable = false, this.emittedReadable = false, this.readableListening = false, this.resumeScheduled = false, this.paused = true, this.emitClose = false !== t3.emitClose, this.autoDestroy = !!t3.autoDestroy, this.destroyed = false, this.defaultEncoding = t3.defaultEncoding || "utf8", this.awaitDrain = 0, this.readingMore = false, this.decoder = null, this.encoding = null, t3.encoding && (!F && (F = e("string_decoder/").StringDecoder), this.decoder = new F(t3.encoding), this.encoding = t3.encoding);
              }
              function s(t3) {
                if (A = A || e("./_stream_duplex"), !(this instanceof s)) return new s(t3);
                var n3 = this instanceof A;
                this._readableState = new d(t3, this, n3), this.readable = true, t3 && ("function" == typeof t3.read && (this._read = t3.read), "function" == typeof t3.destroy && (this._destroy = t3.destroy)), I.call(this);
              }
              function l(e2, t3, n3, r3, o2) {
                x("readableAddChunk", t3);
                var i2 = e2._readableState;
                if (null === t3) i2.reading = false, g(e2, i2);
                else {
                  var d2;
                  if (o2 || (d2 = u(i2, t3)), d2) X(e2, d2);
                  else if (!(i2.objectMode || t3 && 0 < t3.length)) r3 || (i2.reading = false, m(e2, i2));
                  else if ("string" == typeof t3 || i2.objectMode || Object.getPrototypeOf(t3) === P.prototype || (t3 = a(t3)), r3) i2.endEmitted ? X(e2, new K()) : c(e2, i2, t3, true);
                  else if (i2.ended) X(e2, new z());
                  else {
                    if (i2.destroyed) return false;
                    i2.reading = false, i2.decoder && !n3 ? (t3 = i2.decoder.write(t3), i2.objectMode || 0 !== t3.length ? c(e2, i2, t3, false) : m(e2, i2)) : c(e2, i2, t3, false);
                  }
                }
                return !i2.ended && (i2.length < i2.highWaterMark || 0 === i2.length);
              }
              function c(e2, t3, n3, r3) {
                t3.flowing && 0 === t3.length && !t3.sync ? (t3.awaitDrain = 0, e2.emit("data", n3)) : (t3.length += t3.objectMode ? 1 : n3.length, r3 ? t3.buffer.unshift(n3) : t3.buffer.push(n3), t3.needReadable && _(e2)), m(e2, t3);
              }
              function u(e2, t3) {
                var n3;
                return o(t3) || "string" == typeof t3 || void 0 === t3 || e2.objectMode || (n3 = new V("chunk", ["string", "Buffer", "Uint8Array"], t3)), n3;
              }
              function p(e2) {
                return 1073741824 <= e2 ? e2 = 1073741824 : (e2--, e2 |= e2 >>> 1, e2 |= e2 >>> 2, e2 |= e2 >>> 4, e2 |= e2 >>> 8, e2 |= e2 >>> 16, e2++), e2;
              }
              function f(e2, t3) {
                return 0 >= e2 || 0 === t3.length && t3.ended ? 0 : t3.objectMode ? 1 : e2 === e2 ? (e2 > t3.highWaterMark && (t3.highWaterMark = p(e2)), e2 <= t3.length ? e2 : t3.ended ? t3.length : (t3.needReadable = true, 0)) : t3.flowing && t3.length ? t3.buffer.head.data.length : t3.length;
              }
              function g(e2, t3) {
                if (x("onEofChunk"), !t3.ended) {
                  if (t3.decoder) {
                    var n3 = t3.decoder.end();
                    n3 && n3.length && (t3.buffer.push(n3), t3.length += t3.objectMode ? 1 : n3.length);
                  }
                  t3.ended = true, t3.sync ? _(e2) : (t3.needReadable = false, !t3.emittedReadable && (t3.emittedReadable = true, h(e2)));
                }
              }
              function _(e2) {
                var t3 = e2._readableState;
                x("emitReadable", t3.needReadable, t3.emittedReadable), t3.needReadable = false, t3.emittedReadable || (x("emitReadable", t3.flowing), t3.emittedReadable = true, n2.nextTick(h, e2));
              }
              function h(e2) {
                var t3 = e2._readableState;
                x("emitReadable_", t3.destroyed, t3.length, t3.ended), !t3.destroyed && (t3.length || t3.ended) && (e2.emit("readable"), t3.emittedReadable = false), t3.needReadable = !t3.flowing && !t3.ended && t3.length <= t3.highWaterMark, S(e2);
              }
              function m(e2, t3) {
                t3.readingMore || (t3.readingMore = true, n2.nextTick(b, e2, t3));
              }
              function b(e2, t3) {
                for (; !t3.reading && !t3.ended && (t3.length < t3.highWaterMark || t3.flowing && 0 === t3.length); ) {
                  var n3 = t3.length;
                  if (x("maybeReadMore read 0"), e2.read(0), n3 === t3.length) break;
                }
                t3.readingMore = false;
              }
              function y(e2) {
                return function() {
                  var t3 = e2._readableState;
                  x("pipeOnDrain", t3.awaitDrain), t3.awaitDrain && t3.awaitDrain--, 0 === t3.awaitDrain && D(e2, "data") && (t3.flowing = true, S(e2));
                };
              }
              function C(e2) {
                var t3 = e2._readableState;
                t3.readableListening = 0 < e2.listenerCount("readable"), t3.resumeScheduled && !t3.paused ? t3.flowing = true : 0 < e2.listenerCount("data") && e2.resume();
              }
              function R(e2) {
                x("readable nexttick read 0"), e2.read(0);
              }
              function E(e2, t3) {
                t3.resumeScheduled || (t3.resumeScheduled = true, n2.nextTick(w, e2, t3));
              }
              function w(e2, t3) {
                x("resume", t3.reading), t3.reading || e2.read(0), t3.resumeScheduled = false, e2.emit("resume"), S(e2), t3.flowing && !t3.reading && e2.read(0);
              }
              function S(e2) {
                var t3 = e2._readableState;
                for (x("flow", t3.flowing); t3.flowing && null !== e2.read(); ) ;
              }
              function T(e2, t3) {
                if (0 === t3.length) return null;
                var n3;
                return t3.objectMode ? n3 = t3.buffer.shift() : !e2 || e2 >= t3.length ? (n3 = t3.decoder ? t3.buffer.join("") : 1 === t3.buffer.length ? t3.buffer.first() : t3.buffer.concat(t3.length), t3.buffer.clear()) : n3 = t3.buffer.consume(e2, t3.decoder), n3;
              }
              function v(e2) {
                var t3 = e2._readableState;
                x("endReadable", t3.endEmitted), t3.endEmitted || (t3.ended = true, n2.nextTick(k, t3, e2));
              }
              function k(e2, t3) {
                if (x("endReadableNT", e2.endEmitted, e2.length), !e2.endEmitted && 0 === e2.length && (e2.endEmitted = true, t3.readable = false, t3.emit("end"), e2.autoDestroy)) {
                  var n3 = t3._writableState;
                  (!n3 || n3.autoDestroy && n3.finished) && t3.destroy();
                }
              }
              function L(e2, t3) {
                for (var n3 = 0, r3 = e2.length; n3 < r3; n3++) if (e2[n3] === t3) return n3;
                return -1;
              }
              t2.exports = s;
              var A;
              s.ReadableState = d;
              var x, N = e("events").EventEmitter, D = function(e2, t3) {
                return e2.listeners(t3).length;
              }, I = e("./internal/streams/stream"), P = e("buffer").Buffer, M = r2.Uint8Array || function() {
              }, O = e("util");
              x = O && O.debuglog ? O.debuglog("stream") : function() {
              };
              var F, B, U, j = e("./internal/streams/buffer_list"), q = e("./internal/streams/destroy"), W = e("./internal/streams/state"), H = W.getHighWaterMark, Y = e("../errors").codes, V = Y.ERR_INVALID_ARG_TYPE, z = Y.ERR_STREAM_PUSH_AFTER_EOF, G = Y.ERR_METHOD_NOT_IMPLEMENTED, K = Y.ERR_STREAM_UNSHIFT_AFTER_END_EVENT;
              e("inherits")(s, I);
              var X = q.errorOrDestroy, $ = ["error", "close", "destroy", "pause", "resume"];
              Object.defineProperty(s.prototype, "destroyed", { enumerable: false, get: function() {
                return void 0 !== this._readableState && this._readableState.destroyed;
              }, set: function(e2) {
                this._readableState && (this._readableState.destroyed = e2);
              } }), s.prototype.destroy = q.destroy, s.prototype._undestroy = q.undestroy, s.prototype._destroy = function(e2, t3) {
                t3(e2);
              }, s.prototype.push = function(e2, t3) {
                var n3, r3 = this._readableState;
                return r3.objectMode ? n3 = true : "string" == typeof e2 && (t3 = t3 || r3.defaultEncoding, t3 !== r3.encoding && (e2 = P.from(e2, t3), t3 = ""), n3 = true), l(this, e2, t3, false, n3);
              }, s.prototype.unshift = function(e2) {
                return l(this, e2, null, true, false);
              }, s.prototype.isPaused = function() {
                return false === this._readableState.flowing;
              }, s.prototype.setEncoding = function(t3) {
                F || (F = e("string_decoder/").StringDecoder);
                var n3 = new F(t3);
                this._readableState.decoder = n3, this._readableState.encoding = this._readableState.decoder.encoding;
                for (var r3 = this._readableState.buffer.head, a2 = ""; null !== r3; ) a2 += n3.write(r3.data), r3 = r3.next;
                return this._readableState.buffer.clear(), "" !== a2 && this._readableState.buffer.push(a2), this._readableState.length = a2.length, this;
              };
              s.prototype.read = function(e2) {
                x("read", e2), e2 = parseInt(e2, 10);
                var t3 = this._readableState, r3 = e2;
                if (0 !== e2 && (t3.emittedReadable = false), 0 === e2 && t3.needReadable && ((0 === t3.highWaterMark ? 0 < t3.length : t3.length >= t3.highWaterMark) || t3.ended)) return x("read: emitReadable", t3.length, t3.ended), 0 === t3.length && t3.ended ? v(this) : _(this), null;
                if (e2 = f(e2, t3), 0 === e2 && t3.ended) return 0 === t3.length && v(this), null;
                var a2 = t3.needReadable;
                x("need readable", a2), (0 === t3.length || t3.length - e2 < t3.highWaterMark) && (a2 = true, x("length less than watermark", a2)), t3.ended || t3.reading ? (a2 = false, x("reading or ended", a2)) : a2 && (x("do read"), t3.reading = true, t3.sync = true, 0 === t3.length && (t3.needReadable = true), this._read(t3.highWaterMark), t3.sync = false, !t3.reading && (e2 = f(r3, t3)));
                var o2;
                return o2 = 0 < e2 ? T(e2, t3) : null, null === o2 ? (t3.needReadable = t3.length <= t3.highWaterMark, e2 = 0) : (t3.length -= e2, t3.awaitDrain = 0), 0 === t3.length && (!t3.ended && (t3.needReadable = true), r3 !== e2 && t3.ended && v(this)), null !== o2 && this.emit("data", o2), o2;
              }, s.prototype._read = function() {
                X(this, new G("_read()"));
              }, s.prototype.pipe = function(e2, t3) {
                function r3(e3, t4) {
                  x("onunpipe"), e3 === p2 && t4 && false === t4.hasUnpiped && (t4.hasUnpiped = true, o2());
                }
                function a2() {
                  x("onend"), e2.end();
                }
                function o2() {
                  x("cleanup"), e2.removeListener("close", l2), e2.removeListener("finish", c2), e2.removeListener("drain", h2), e2.removeListener("error", s2), e2.removeListener("unpipe", r3), p2.removeListener("end", a2), p2.removeListener("end", u2), p2.removeListener("data", d2), m2 = true, f2.awaitDrain && (!e2._writableState || e2._writableState.needDrain) && h2();
                }
                function d2(t4) {
                  x("ondata");
                  var n3 = e2.write(t4);
                  x("dest.write", n3), false === n3 && ((1 === f2.pipesCount && f2.pipes === e2 || 1 < f2.pipesCount && -1 !== L(f2.pipes, e2)) && !m2 && (x("false write response, pause", f2.awaitDrain), f2.awaitDrain++), p2.pause());
                }
                function s2(t4) {
                  x("onerror", t4), u2(), e2.removeListener("error", s2), 0 === D(e2, "error") && X(e2, t4);
                }
                function l2() {
                  e2.removeListener("finish", c2), u2();
                }
                function c2() {
                  x("onfinish"), e2.removeListener("close", l2), u2();
                }
                function u2() {
                  x("unpipe"), p2.unpipe(e2);
                }
                var p2 = this, f2 = this._readableState;
                switch (f2.pipesCount) {
                  case 0:
                    f2.pipes = e2;
                    break;
                  case 1:
                    f2.pipes = [f2.pipes, e2];
                    break;
                  default:
                    f2.pipes.push(e2);
                }
                f2.pipesCount += 1, x("pipe count=%d opts=%j", f2.pipesCount, t3);
                var g2 = (!t3 || false !== t3.end) && e2 !== n2.stdout && e2 !== n2.stderr, _2 = g2 ? a2 : u2;
                f2.endEmitted ? n2.nextTick(_2) : p2.once("end", _2), e2.on("unpipe", r3);
                var h2 = y(p2);
                e2.on("drain", h2);
                var m2 = false;
                return p2.on("data", d2), i(e2, "error", s2), e2.once("close", l2), e2.once("finish", c2), e2.emit("pipe", p2), f2.flowing || (x("pipe resume"), p2.resume()), e2;
              }, s.prototype.unpipe = function(e2) {
                var t3 = this._readableState, n3 = { hasUnpiped: false };
                if (0 === t3.pipesCount) return this;
                if (1 === t3.pipesCount) return e2 && e2 !== t3.pipes ? this : (e2 || (e2 = t3.pipes), t3.pipes = null, t3.pipesCount = 0, t3.flowing = false, e2 && e2.emit("unpipe", this, n3), this);
                if (!e2) {
                  var r3 = t3.pipes, a2 = t3.pipesCount;
                  t3.pipes = null, t3.pipesCount = 0, t3.flowing = false;
                  for (var o2 = 0; o2 < a2; o2++) r3[o2].emit("unpipe", this, { hasUnpiped: false });
                  return this;
                }
                var d2 = L(t3.pipes, e2);
                return -1 === d2 ? this : (t3.pipes.splice(d2, 1), t3.pipesCount -= 1, 1 === t3.pipesCount && (t3.pipes = t3.pipes[0]), e2.emit("unpipe", this, n3), this);
              }, s.prototype.on = function(e2, t3) {
                var r3 = I.prototype.on.call(this, e2, t3), a2 = this._readableState;
                return "data" === e2 ? (a2.readableListening = 0 < this.listenerCount("readable"), false !== a2.flowing && this.resume()) : "readable" == e2 && !a2.endEmitted && !a2.readableListening && (a2.readableListening = a2.needReadable = true, a2.flowing = false, a2.emittedReadable = false, x("on readable", a2.length, a2.reading), a2.length ? _(this) : !a2.reading && n2.nextTick(R, this)), r3;
              }, s.prototype.addListener = s.prototype.on, s.prototype.removeListener = function(e2, t3) {
                var r3 = I.prototype.removeListener.call(this, e2, t3);
                return "readable" === e2 && n2.nextTick(C, this), r3;
              }, s.prototype.removeAllListeners = function(e2) {
                var t3 = I.prototype.removeAllListeners.apply(this, arguments);
                return ("readable" === e2 || void 0 === e2) && n2.nextTick(C, this), t3;
              }, s.prototype.resume = function() {
                var e2 = this._readableState;
                return e2.flowing || (x("resume"), e2.flowing = !e2.readableListening, E(this, e2)), e2.paused = false, this;
              }, s.prototype.pause = function() {
                return x("call pause flowing=%j", this._readableState.flowing), false !== this._readableState.flowing && (x("pause"), this._readableState.flowing = false, this.emit("pause")), this._readableState.paused = true, this;
              }, s.prototype.wrap = function(e2) {
                var t3 = this, r3 = this._readableState, a2 = false;
                for (var o2 in e2.on("end", function() {
                  if (x("wrapped end"), r3.decoder && !r3.ended) {
                    var e3 = r3.decoder.end();
                    e3 && e3.length && t3.push(e3);
                  }
                  t3.push(null);
                }), e2.on("data", function(n3) {
                  if ((x("wrapped data"), r3.decoder && (n3 = r3.decoder.write(n3)), !(r3.objectMode && (null === n3 || void 0 === n3))) && (r3.objectMode || n3 && n3.length)) {
                    var o3 = t3.push(n3);
                    o3 || (a2 = true, e2.pause());
                  }
                }), e2) void 0 === this[o2] && "function" == typeof e2[o2] && (this[o2] = /* @__PURE__ */ (function(t4) {
                  return function() {
                    return e2[t4].apply(e2, arguments);
                  };
                })(o2));
                for (var i2 = 0; i2 < $.length; i2++) e2.on($[i2], this.emit.bind(this, $[i2]));
                return this._read = function(t4) {
                  x("wrapped _read", t4), a2 && (a2 = false, e2.resume());
                }, this;
              }, "function" == typeof Symbol && (s.prototype[Symbol.asyncIterator] = function() {
                return void 0 === B && (B = e("./internal/streams/async_iterator")), B(this);
              }), Object.defineProperty(s.prototype, "readableHighWaterMark", { enumerable: false, get: function() {
                return this._readableState.highWaterMark;
              } }), Object.defineProperty(s.prototype, "readableBuffer", { enumerable: false, get: function() {
                return this._readableState && this._readableState.buffer;
              } }), Object.defineProperty(s.prototype, "readableFlowing", { enumerable: false, get: function() {
                return this._readableState.flowing;
              }, set: function(e2) {
                this._readableState && (this._readableState.flowing = e2);
              } }), s._fromList = T, Object.defineProperty(s.prototype, "readableLength", { enumerable: false, get: function() {
                return this._readableState.length;
              } }), "function" == typeof Symbol && (s.from = function(t3, n3) {
                return void 0 === U && (U = e("./internal/streams/from")), U(s, t3, n3);
              });
            }).call(this);
          }).call(this, e("_process"), "undefined" == typeof global ? "undefined" == typeof self ? "undefined" == typeof window ? {} : window : self : global);
        }, { "../errors": 15, "./_stream_duplex": 16, "./internal/streams/async_iterator": 21, "./internal/streams/buffer_list": 22, "./internal/streams/destroy": 23, "./internal/streams/from": 25, "./internal/streams/state": 27, "./internal/streams/stream": 28, _process: 12, buffer: 3, events: 7, inherits: 10, "string_decoder/": 31, util: 2 }], 19: [function(e, t2) {
          "use strict";
          function n2(e2, t3) {
            var n3 = this._transformState;
            n3.transforming = false;
            var r3 = n3.writecb;
            if (null === r3) return this.emit("error", new s());
            n3.writechunk = null, n3.writecb = null, null != t3 && this.push(t3), r3(e2);
            var a2 = this._readableState;
            a2.reading = false, (a2.needReadable || a2.length < a2.highWaterMark) && this._read(a2.highWaterMark);
          }
          function r2(e2) {
            return this instanceof r2 ? void (u.call(this, e2), this._transformState = { afterTransform: n2.bind(this), needTransform: false, transforming: false, writecb: null, writechunk: null, writeencoding: null }, this._readableState.needReadable = true, this._readableState.sync = false, e2 && ("function" == typeof e2.transform && (this._transform = e2.transform), "function" == typeof e2.flush && (this._flush = e2.flush)), this.on("prefinish", a)) : new r2(e2);
          }
          function a() {
            var e2 = this;
            "function" != typeof this._flush || this._readableState.destroyed ? o(this, null, null) : this._flush(function(t3, n3) {
              o(e2, t3, n3);
            });
          }
          function o(e2, t3, n3) {
            if (t3) return e2.emit("error", t3);
            if (null != n3 && e2.push(n3), e2._writableState.length) throw new c();
            if (e2._transformState.transforming) throw new l();
            return e2.push(null);
          }
          t2.exports = r2;
          var i = e("../errors").codes, d = i.ERR_METHOD_NOT_IMPLEMENTED, s = i.ERR_MULTIPLE_CALLBACK, l = i.ERR_TRANSFORM_ALREADY_TRANSFORMING, c = i.ERR_TRANSFORM_WITH_LENGTH_0, u = e("./_stream_duplex");
          e("inherits")(r2, u), r2.prototype.push = function(e2, t3) {
            return this._transformState.needTransform = false, u.prototype.push.call(this, e2, t3);
          }, r2.prototype._transform = function(e2, t3, n3) {
            n3(new d("_transform()"));
          }, r2.prototype._write = function(e2, t3, n3) {
            var r3 = this._transformState;
            if (r3.writecb = n3, r3.writechunk = e2, r3.writeencoding = t3, !r3.transforming) {
              var a2 = this._readableState;
              (r3.needTransform || a2.needReadable || a2.length < a2.highWaterMark) && this._read(a2.highWaterMark);
            }
          }, r2.prototype._read = function() {
            var e2 = this._transformState;
            null === e2.writechunk || e2.transforming ? e2.needTransform = true : (e2.transforming = true, this._transform(e2.writechunk, e2.writeencoding, e2.afterTransform));
          }, r2.prototype._destroy = function(e2, t3) {
            u.prototype._destroy.call(this, e2, function(e3) {
              t3(e3);
            });
          };
        }, { "../errors": 15, "./_stream_duplex": 16, inherits: 10 }], 20: [function(e, t2) {
          (function(n2, r2) {
            (function() {
              "use strict";
              function a(e2) {
                var t3 = this;
                this.next = null, this.entry = null, this.finish = function() {
                  v(t3, e2);
                };
              }
              function o(e2) {
                return x.from(e2);
              }
              function i(e2) {
                return x.isBuffer(e2) || e2 instanceof N;
              }
              function d() {
              }
              function s(t3, n3, r3) {
                k = k || e("./_stream_duplex"), t3 = t3 || {}, "boolean" != typeof r3 && (r3 = n3 instanceof k), this.objectMode = !!t3.objectMode, r3 && (this.objectMode = this.objectMode || !!t3.writableObjectMode), this.highWaterMark = P(this, t3, "writableHighWaterMark", r3), this.finalCalled = false, this.needDrain = false, this.ending = false, this.ended = false, this.finished = false, this.destroyed = false;
                var o2 = false === t3.decodeStrings;
                this.decodeStrings = !o2, this.defaultEncoding = t3.defaultEncoding || "utf8", this.length = 0, this.writing = false, this.corked = 0, this.sync = true, this.bufferProcessing = false, this.onwrite = function(e2) {
                  m(n3, e2);
                }, this.writecb = null, this.writelen = 0, this.bufferedRequest = null, this.lastBufferedRequest = null, this.pendingcb = 0, this.prefinished = false, this.errorEmitted = false, this.emitClose = false !== t3.emitClose, this.autoDestroy = !!t3.autoDestroy, this.bufferedRequestCount = 0, this.corkedRequestsFree = new a(this);
              }
              function l(t3) {
                k = k || e("./_stream_duplex");
                var n3 = this instanceof k;
                return n3 || V.call(l, this) ? void (this._writableState = new s(t3, this, n3), this.writable = true, t3 && ("function" == typeof t3.write && (this._write = t3.write), "function" == typeof t3.writev && (this._writev = t3.writev), "function" == typeof t3.destroy && (this._destroy = t3.destroy), "function" == typeof t3.final && (this._final = t3.final)), A.call(this)) : new l(t3);
              }
              function c(e2, t3) {
                var r3 = new W();
                Y(e2, r3), n2.nextTick(t3, r3);
              }
              function u(e2, t3, r3, a2) {
                var o2;
                return null === r3 ? o2 = new q() : "string" != typeof r3 && !t3.objectMode && (o2 = new O("chunk", ["string", "Buffer"], r3)), !o2 || (Y(e2, o2), n2.nextTick(a2, o2), false);
              }
              function p(e2, t3, n3) {
                return e2.objectMode || false === e2.decodeStrings || "string" != typeof t3 || (t3 = x.from(t3, n3)), t3;
              }
              function f(e2, t3, n3, r3, a2, o2) {
                if (!n3) {
                  var i2 = p(t3, r3, a2);
                  r3 !== i2 && (n3 = true, a2 = "buffer", r3 = i2);
                }
                var d2 = t3.objectMode ? 1 : r3.length;
                t3.length += d2;
                var s2 = t3.length < t3.highWaterMark;
                if (s2 || (t3.needDrain = true), t3.writing || t3.corked) {
                  var l2 = t3.lastBufferedRequest;
                  t3.lastBufferedRequest = { chunk: r3, encoding: a2, isBuf: n3, callback: o2, next: null }, l2 ? l2.next = t3.lastBufferedRequest : t3.bufferedRequest = t3.lastBufferedRequest, t3.bufferedRequestCount += 1;
                } else g(e2, t3, false, d2, r3, a2, o2);
                return s2;
              }
              function g(e2, t3, n3, r3, a2, o2, i2) {
                t3.writelen = r3, t3.writecb = i2, t3.writing = true, t3.sync = true, t3.destroyed ? t3.onwrite(new j("write")) : n3 ? e2._writev(a2, t3.onwrite) : e2._write(a2, o2, t3.onwrite), t3.sync = false;
              }
              function _(e2, t3, r3, a2, o2) {
                --t3.pendingcb, r3 ? (n2.nextTick(o2, a2), n2.nextTick(S, e2, t3), e2._writableState.errorEmitted = true, Y(e2, a2)) : (o2(a2), e2._writableState.errorEmitted = true, Y(e2, a2), S(e2, t3));
              }
              function h(e2) {
                e2.writing = false, e2.writecb = null, e2.length -= e2.writelen, e2.writelen = 0;
              }
              function m(e2, t3) {
                var r3 = e2._writableState, a2 = r3.sync, o2 = r3.writecb;
                if ("function" != typeof o2) throw new B();
                if (h(r3), t3) _(e2, r3, a2, t3, o2);
                else {
                  var i2 = R(r3) || e2.destroyed;
                  i2 || r3.corked || r3.bufferProcessing || !r3.bufferedRequest || C(e2, r3), a2 ? n2.nextTick(b, e2, r3, i2, o2) : b(e2, r3, i2, o2);
                }
              }
              function b(e2, t3, n3, r3) {
                n3 || y(e2, t3), t3.pendingcb--, r3(), S(e2, t3);
              }
              function y(e2, t3) {
                0 === t3.length && t3.needDrain && (t3.needDrain = false, e2.emit("drain"));
              }
              function C(e2, t3) {
                t3.bufferProcessing = true;
                var n3 = t3.bufferedRequest;
                if (e2._writev && n3 && n3.next) {
                  var r3 = t3.bufferedRequestCount, o2 = Array(r3), i2 = t3.corkedRequestsFree;
                  i2.entry = n3;
                  for (var d2 = 0, s2 = true; n3; ) o2[d2] = n3, n3.isBuf || (s2 = false), n3 = n3.next, d2 += 1;
                  o2.allBuffers = s2, g(e2, t3, true, t3.length, o2, "", i2.finish), t3.pendingcb++, t3.lastBufferedRequest = null, i2.next ? (t3.corkedRequestsFree = i2.next, i2.next = null) : t3.corkedRequestsFree = new a(t3), t3.bufferedRequestCount = 0;
                } else {
                  for (; n3; ) {
                    var l2 = n3.chunk, c2 = n3.encoding, u2 = n3.callback, p2 = t3.objectMode ? 1 : l2.length;
                    if (g(e2, t3, false, p2, l2, c2, u2), n3 = n3.next, t3.bufferedRequestCount--, t3.writing) break;
                  }
                  null === n3 && (t3.lastBufferedRequest = null);
                }
                t3.bufferedRequest = n3, t3.bufferProcessing = false;
              }
              function R(e2) {
                return e2.ending && 0 === e2.length && null === e2.bufferedRequest && !e2.finished && !e2.writing;
              }
              function E(e2, t3) {
                e2._final(function(n3) {
                  t3.pendingcb--, n3 && Y(e2, n3), t3.prefinished = true, e2.emit("prefinish"), S(e2, t3);
                });
              }
              function w(e2, t3) {
                t3.prefinished || t3.finalCalled || ("function" != typeof e2._final || t3.destroyed ? (t3.prefinished = true, e2.emit("prefinish")) : (t3.pendingcb++, t3.finalCalled = true, n2.nextTick(E, e2, t3)));
              }
              function S(e2, t3) {
                var n3 = R(t3);
                if (n3 && (w(e2, t3), 0 === t3.pendingcb && (t3.finished = true, e2.emit("finish"), t3.autoDestroy))) {
                  var r3 = e2._readableState;
                  (!r3 || r3.autoDestroy && r3.endEmitted) && e2.destroy();
                }
                return n3;
              }
              function T(e2, t3, r3) {
                t3.ending = true, S(e2, t3), r3 && (t3.finished ? n2.nextTick(r3) : e2.once("finish", r3)), t3.ended = true, e2.writable = false;
              }
              function v(e2, t3, n3) {
                var r3 = e2.entry;
                for (e2.entry = null; r3; ) {
                  var a2 = r3.callback;
                  t3.pendingcb--, a2(n3), r3 = r3.next;
                }
                t3.corkedRequestsFree.next = e2;
              }
              t2.exports = l;
              var k;
              l.WritableState = s;
              var L = { deprecate: e("util-deprecate") }, A = e("./internal/streams/stream"), x = e("buffer").Buffer, N = r2.Uint8Array || function() {
              }, D = e("./internal/streams/destroy"), I = e("./internal/streams/state"), P = I.getHighWaterMark, M = e("../errors").codes, O = M.ERR_INVALID_ARG_TYPE, F = M.ERR_METHOD_NOT_IMPLEMENTED, B = M.ERR_MULTIPLE_CALLBACK, U = M.ERR_STREAM_CANNOT_PIPE, j = M.ERR_STREAM_DESTROYED, q = M.ERR_STREAM_NULL_VALUES, W = M.ERR_STREAM_WRITE_AFTER_END, H = M.ERR_UNKNOWN_ENCODING, Y = D.errorOrDestroy;
              e("inherits")(l, A), s.prototype.getBuffer = function() {
                for (var e2 = this.bufferedRequest, t3 = []; e2; ) t3.push(e2), e2 = e2.next;
                return t3;
              }, (function() {
                try {
                  Object.defineProperty(s.prototype, "buffer", { get: L.deprecate(function() {
                    return this.getBuffer();
                  }, "_writableState.buffer is deprecated. Use _writableState.getBuffer instead.", "DEP0003") });
                } catch (e2) {
                }
              })();
              var V;
              "function" == typeof Symbol && Symbol.hasInstance && "function" == typeof Function.prototype[Symbol.hasInstance] ? (V = Function.prototype[Symbol.hasInstance], Object.defineProperty(l, Symbol.hasInstance, { value: function(e2) {
                return !!V.call(this, e2) || !(this !== l) && e2 && e2._writableState instanceof s;
              } })) : V = function(e2) {
                return e2 instanceof this;
              }, l.prototype.pipe = function() {
                Y(this, new U());
              }, l.prototype.write = function(e2, t3, n3) {
                var r3 = this._writableState, a2 = false, s2 = !r3.objectMode && i(e2);
                return s2 && !x.isBuffer(e2) && (e2 = o(e2)), "function" == typeof t3 && (n3 = t3, t3 = null), s2 ? t3 = "buffer" : !t3 && (t3 = r3.defaultEncoding), "function" != typeof n3 && (n3 = d), r3.ending ? c(this, n3) : (s2 || u(this, r3, e2, n3)) && (r3.pendingcb++, a2 = f(this, r3, s2, e2, t3, n3)), a2;
              }, l.prototype.cork = function() {
                this._writableState.corked++;
              }, l.prototype.uncork = function() {
                var e2 = this._writableState;
                e2.corked && (e2.corked--, !e2.writing && !e2.corked && !e2.bufferProcessing && e2.bufferedRequest && C(this, e2));
              }, l.prototype.setDefaultEncoding = function(e2) {
                if ("string" == typeof e2 && (e2 = e2.toLowerCase()), !(-1 < ["hex", "utf8", "utf-8", "ascii", "binary", "base64", "ucs2", "ucs-2", "utf16le", "utf-16le", "raw"].indexOf((e2 + "").toLowerCase()))) throw new H(e2);
                return this._writableState.defaultEncoding = e2, this;
              }, Object.defineProperty(l.prototype, "writableBuffer", { enumerable: false, get: function() {
                return this._writableState && this._writableState.getBuffer();
              } }), Object.defineProperty(l.prototype, "writableHighWaterMark", { enumerable: false, get: function() {
                return this._writableState.highWaterMark;
              } }), l.prototype._write = function(e2, t3, n3) {
                n3(new F("_write()"));
              }, l.prototype._writev = null, l.prototype.end = function(e2, t3, n3) {
                var r3 = this._writableState;
                return "function" == typeof e2 ? (n3 = e2, e2 = null, t3 = null) : "function" == typeof t3 && (n3 = t3, t3 = null), null !== e2 && void 0 !== e2 && this.write(e2, t3), r3.corked && (r3.corked = 1, this.uncork()), r3.ending || T(this, r3, n3), this;
              }, Object.defineProperty(l.prototype, "writableLength", { enumerable: false, get: function() {
                return this._writableState.length;
              } }), Object.defineProperty(l.prototype, "destroyed", { enumerable: false, get: function() {
                return void 0 !== this._writableState && this._writableState.destroyed;
              }, set: function(e2) {
                this._writableState && (this._writableState.destroyed = e2);
              } }), l.prototype.destroy = D.destroy, l.prototype._undestroy = D.undestroy, l.prototype._destroy = function(e2, t3) {
                t3(e2);
              };
            }).call(this);
          }).call(this, e("_process"), "undefined" == typeof global ? "undefined" == typeof self ? "undefined" == typeof window ? {} : window : self : global);
        }, { "../errors": 15, "./_stream_duplex": 16, "./internal/streams/destroy": 23, "./internal/streams/state": 27, "./internal/streams/stream": 28, _process: 12, buffer: 3, inherits: 10, "util-deprecate": 32 }], 21: [function(e, t2) {
          (function(n2) {
            (function() {
              "use strict";
              function r2(e2, t3, n3) {
                return t3 in e2 ? Object.defineProperty(e2, t3, { value: n3, enumerable: true, configurable: true, writable: true }) : e2[t3] = n3, e2;
              }
              function a(e2, t3) {
                return { value: e2, done: t3 };
              }
              function o(e2) {
                var t3 = e2[c];
                if (null !== t3) {
                  var n3 = e2[h].read();
                  null !== n3 && (e2[g] = null, e2[c] = null, e2[u] = null, t3(a(n3, false)));
                }
              }
              function i(e2) {
                n2.nextTick(o, e2);
              }
              function d(e2, t3) {
                return function(n3, r3) {
                  e2.then(function() {
                    return t3[f] ? void n3(a(void 0, true)) : void t3[_](n3, r3);
                  }, r3);
                };
              }
              var s, l = e("./end-of-stream"), c = Symbol("lastResolve"), u = Symbol("lastReject"), p = Symbol("error"), f = Symbol("ended"), g = Symbol("lastPromise"), _ = Symbol("handlePromise"), h = Symbol("stream"), m = Object.getPrototypeOf(function() {
              }), b = Object.setPrototypeOf((s = { get stream() {
                return this[h];
              }, next: function() {
                var e2 = this, t3 = this[p];
                if (null !== t3) return Promise.reject(t3);
                if (this[f]) return Promise.resolve(a(void 0, true));
                if (this[h].destroyed) return new Promise(function(t4, r4) {
                  n2.nextTick(function() {
                    e2[p] ? r4(e2[p]) : t4(a(void 0, true));
                  });
                });
                var r3, o2 = this[g];
                if (o2) r3 = new Promise(d(o2, this));
                else {
                  var i2 = this[h].read();
                  if (null !== i2) return Promise.resolve(a(i2, false));
                  r3 = new Promise(this[_]);
                }
                return this[g] = r3, r3;
              } }, r2(s, Symbol.asyncIterator, function() {
                return this;
              }), r2(s, "return", function() {
                var e2 = this;
                return new Promise(function(t3, n3) {
                  e2[h].destroy(null, function(e3) {
                    return e3 ? void n3(e3) : void t3(a(void 0, true));
                  });
                });
              }), s), m);
              t2.exports = function(e2) {
                var t3, n3 = Object.create(b, (t3 = {}, r2(t3, h, { value: e2, writable: true }), r2(t3, c, { value: null, writable: true }), r2(t3, u, { value: null, writable: true }), r2(t3, p, { value: null, writable: true }), r2(t3, f, { value: e2._readableState.endEmitted, writable: true }), r2(t3, _, { value: function(e3, t4) {
                  var r3 = n3[h].read();
                  r3 ? (n3[g] = null, n3[c] = null, n3[u] = null, e3(a(r3, false))) : (n3[c] = e3, n3[u] = t4);
                }, writable: true }), t3));
                return n3[g] = null, l(e2, function(e3) {
                  if (e3 && "ERR_STREAM_PREMATURE_CLOSE" !== e3.code) {
                    var t4 = n3[u];
                    return null !== t4 && (n3[g] = null, n3[c] = null, n3[u] = null, t4(e3)), void (n3[p] = e3);
                  }
                  var r3 = n3[c];
                  null !== r3 && (n3[g] = null, n3[c] = null, n3[u] = null, r3(a(void 0, true))), n3[f] = true;
                }), e2.on("readable", i.bind(null, n3)), n3;
              };
            }).call(this);
          }).call(this, e("_process"));
        }, { "./end-of-stream": 24, _process: 12 }], 22: [function(e, t2) {
          "use strict";
          function n2(e2, t3) {
            var n3 = Object.keys(e2);
            if (Object.getOwnPropertySymbols) {
              var r3 = Object.getOwnPropertySymbols(e2);
              t3 && (r3 = r3.filter(function(t4) {
                return Object.getOwnPropertyDescriptor(e2, t4).enumerable;
              })), n3.push.apply(n3, r3);
            }
            return n3;
          }
          function r2(e2) {
            for (var t3, r3 = 1; r3 < arguments.length; r3++) t3 = null == arguments[r3] ? {} : arguments[r3], r3 % 2 ? n2(Object(t3), true).forEach(function(n3) {
              a(e2, n3, t3[n3]);
            }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e2, Object.getOwnPropertyDescriptors(t3)) : n2(Object(t3)).forEach(function(n3) {
              Object.defineProperty(e2, n3, Object.getOwnPropertyDescriptor(t3, n3));
            });
            return e2;
          }
          function a(e2, t3, n3) {
            return t3 in e2 ? Object.defineProperty(e2, t3, { value: n3, enumerable: true, configurable: true, writable: true }) : e2[t3] = n3, e2;
          }
          function o(e2, t3) {
            if (!(e2 instanceof t3)) throw new TypeError("Cannot call a class as a function");
          }
          function i(e2, t3) {
            for (var n3, r3 = 0; r3 < t3.length; r3++) n3 = t3[r3], n3.enumerable = n3.enumerable || false, n3.configurable = true, "value" in n3 && (n3.writable = true), Object.defineProperty(e2, n3.key, n3);
          }
          function d(e2, t3, n3) {
            return t3 && i(e2.prototype, t3), n3 && i(e2, n3), e2;
          }
          function s(e2, t3, n3) {
            u.prototype.copy.call(e2, t3, n3);
          }
          var l = e("buffer"), u = l.Buffer, p = e("util"), f = p.inspect, g = f && f.custom || "inspect";
          t2.exports = (function() {
            function e2() {
              o(this, e2), this.head = null, this.tail = null, this.length = 0;
            }
            return d(e2, [{ key: "push", value: function(e3) {
              var t3 = { data: e3, next: null };
              0 < this.length ? this.tail.next = t3 : this.head = t3, this.tail = t3, ++this.length;
            } }, { key: "unshift", value: function(e3) {
              var t3 = { data: e3, next: this.head };
              0 === this.length && (this.tail = t3), this.head = t3, ++this.length;
            } }, { key: "shift", value: function() {
              if (0 !== this.length) {
                var e3 = this.head.data;
                return this.head = 1 === this.length ? this.tail = null : this.head.next, --this.length, e3;
              }
            } }, { key: "clear", value: function() {
              this.head = this.tail = null, this.length = 0;
            } }, { key: "join", value: function(e3) {
              if (0 === this.length) return "";
              for (var t3 = this.head, n3 = "" + t3.data; t3 = t3.next; ) n3 += e3 + t3.data;
              return n3;
            } }, { key: "concat", value: function(e3) {
              if (0 === this.length) return u.alloc(0);
              for (var t3 = u.allocUnsafe(e3 >>> 0), n3 = this.head, r3 = 0; n3; ) s(n3.data, t3, r3), r3 += n3.data.length, n3 = n3.next;
              return t3;
            } }, { key: "consume", value: function(e3, t3) {
              var n3;
              return e3 < this.head.data.length ? (n3 = this.head.data.slice(0, e3), this.head.data = this.head.data.slice(e3)) : e3 === this.head.data.length ? n3 = this.shift() : n3 = t3 ? this._getString(e3) : this._getBuffer(e3), n3;
            } }, { key: "first", value: function() {
              return this.head.data;
            } }, { key: "_getString", value: function(e3) {
              var t3 = this.head, r3 = 1, a2 = t3.data;
              for (e3 -= a2.length; t3 = t3.next; ) {
                var o2 = t3.data, i2 = e3 > o2.length ? o2.length : e3;
                if (a2 += i2 === o2.length ? o2 : o2.slice(0, e3), e3 -= i2, 0 === e3) {
                  i2 === o2.length ? (++r3, this.head = t3.next ? t3.next : this.tail = null) : (this.head = t3, t3.data = o2.slice(i2));
                  break;
                }
                ++r3;
              }
              return this.length -= r3, a2;
            } }, { key: "_getBuffer", value: function(e3) {
              var t3 = u.allocUnsafe(e3), r3 = this.head, a2 = 1;
              for (r3.data.copy(t3), e3 -= r3.data.length; r3 = r3.next; ) {
                var o2 = r3.data, i2 = e3 > o2.length ? o2.length : e3;
                if (o2.copy(t3, t3.length - e3, 0, i2), e3 -= i2, 0 === e3) {
                  i2 === o2.length ? (++a2, this.head = r3.next ? r3.next : this.tail = null) : (this.head = r3, r3.data = o2.slice(i2));
                  break;
                }
                ++a2;
              }
              return this.length -= a2, t3;
            } }, { key: g, value: function(e3, t3) {
              return f(this, r2({}, t3, { depth: 0, customInspect: false }));
            } }]), e2;
          })();
        }, { buffer: 3, util: 2 }], 23: [function(e, t2) {
          (function(e2) {
            (function() {
              "use strict";
              function n2(e3, t3) {
                a(e3, t3), r2(e3);
              }
              function r2(e3) {
                e3._writableState && !e3._writableState.emitClose || e3._readableState && !e3._readableState.emitClose || e3.emit("close");
              }
              function a(e3, t3) {
                e3.emit("error", t3);
              }
              t2.exports = { destroy: function(t3, o) {
                var i = this, d = this._readableState && this._readableState.destroyed, s = this._writableState && this._writableState.destroyed;
                return d || s ? (o ? o(t3) : t3 && (this._writableState ? !this._writableState.errorEmitted && (this._writableState.errorEmitted = true, e2.nextTick(a, this, t3)) : e2.nextTick(a, this, t3)), this) : (this._readableState && (this._readableState.destroyed = true), this._writableState && (this._writableState.destroyed = true), this._destroy(t3 || null, function(t4) {
                  !o && t4 ? i._writableState ? i._writableState.errorEmitted ? e2.nextTick(r2, i) : (i._writableState.errorEmitted = true, e2.nextTick(n2, i, t4)) : e2.nextTick(n2, i, t4) : o ? (e2.nextTick(r2, i), o(t4)) : e2.nextTick(r2, i);
                }), this);
              }, undestroy: function() {
                this._readableState && (this._readableState.destroyed = false, this._readableState.reading = false, this._readableState.ended = false, this._readableState.endEmitted = false), this._writableState && (this._writableState.destroyed = false, this._writableState.ended = false, this._writableState.ending = false, this._writableState.finalCalled = false, this._writableState.prefinished = false, this._writableState.finished = false, this._writableState.errorEmitted = false);
              }, errorOrDestroy: function(e3, t3) {
                var n3 = e3._readableState, r3 = e3._writableState;
                n3 && n3.autoDestroy || r3 && r3.autoDestroy ? e3.destroy(t3) : e3.emit("error", t3);
              } };
            }).call(this);
          }).call(this, e("_process"));
        }, { _process: 12 }], 24: [function(e, t2) {
          "use strict";
          function n2(e2) {
            var t3 = false;
            return function() {
              if (!t3) {
                t3 = true;
                for (var n3 = arguments.length, r3 = Array(n3), a2 = 0; a2 < n3; a2++) r3[a2] = arguments[a2];
                e2.apply(this, r3);
              }
            };
          }
          function r2() {
          }
          function a(e2) {
            return e2.setHeader && "function" == typeof e2.abort;
          }
          function o(e2, t3, d) {
            if ("function" == typeof t3) return o(e2, null, t3);
            t3 || (t3 = {}), d = n2(d || r2);
            var s = t3.readable || false !== t3.readable && e2.readable, l = t3.writable || false !== t3.writable && e2.writable, c = function() {
              e2.writable || p();
            }, u = e2._writableState && e2._writableState.finished, p = function() {
              l = false, u = true, s || d.call(e2);
            }, f = e2._readableState && e2._readableState.endEmitted, g = function() {
              s = false, f = true, l || d.call(e2);
            }, _ = function(t4) {
              d.call(e2, t4);
            }, h = function() {
              var t4;
              return s && !f ? (e2._readableState && e2._readableState.ended || (t4 = new i()), d.call(e2, t4)) : l && !u ? (e2._writableState && e2._writableState.ended || (t4 = new i()), d.call(e2, t4)) : void 0;
            }, m = function() {
              e2.req.on("finish", p);
            };
            return a(e2) ? (e2.on("complete", p), e2.on("abort", h), e2.req ? m() : e2.on("request", m)) : l && !e2._writableState && (e2.on("end", c), e2.on("close", c)), e2.on("end", g), e2.on("finish", p), false !== t3.error && e2.on("error", _), e2.on("close", h), function() {
              e2.removeListener("complete", p), e2.removeListener("abort", h), e2.removeListener("request", m), e2.req && e2.req.removeListener("finish", p), e2.removeListener("end", c), e2.removeListener("close", c), e2.removeListener("finish", p), e2.removeListener("end", g), e2.removeListener("error", _), e2.removeListener("close", h);
            };
          }
          var i = e("../../../errors").codes.ERR_STREAM_PREMATURE_CLOSE;
          t2.exports = o;
        }, { "../../../errors": 15 }], 25: [function(e, t2) {
          t2.exports = function() {
            throw new Error("Readable.from is not available in the browser");
          };
        }, {}], 26: [function(e, t2) {
          "use strict";
          function n2(e2) {
            var t3 = false;
            return function() {
              t3 || (t3 = true, e2.apply(void 0, arguments));
            };
          }
          function r2(e2) {
            if (e2) throw e2;
          }
          function a(e2) {
            return e2.setHeader && "function" == typeof e2.abort;
          }
          function o(t3, r3, o2, i2) {
            i2 = n2(i2);
            var d2 = false;
            t3.on("close", function() {
              d2 = true;
            }), l === void 0 && (l = e("./end-of-stream")), l(t3, { readable: r3, writable: o2 }, function(e2) {
              return e2 ? i2(e2) : void (d2 = true, i2());
            });
            var s2 = false;
            return function(e2) {
              if (!d2) return s2 ? void 0 : (s2 = true, a(t3) ? t3.abort() : "function" == typeof t3.destroy ? t3.destroy() : void i2(e2 || new p("pipe")));
            };
          }
          function i(e2) {
            e2();
          }
          function d(e2, t3) {
            return e2.pipe(t3);
          }
          function s(e2) {
            return e2.length ? "function" == typeof e2[e2.length - 1] ? e2.pop() : r2 : r2;
          }
          var l, c = e("../../../errors").codes, u = c.ERR_MISSING_ARGS, p = c.ERR_STREAM_DESTROYED;
          t2.exports = function() {
            for (var e2 = arguments.length, t3 = Array(e2), n3 = 0; n3 < e2; n3++) t3[n3] = arguments[n3];
            var r3 = s(t3);
            if (Array.isArray(t3[0]) && (t3 = t3[0]), 2 > t3.length) throw new u("streams");
            var a2, l2 = t3.map(function(e3, n4) {
              var d2 = n4 < t3.length - 1;
              return o(e3, d2, 0 < n4, function(e4) {
                a2 || (a2 = e4), e4 && l2.forEach(i), d2 || (l2.forEach(i), r3(a2));
              });
            });
            return t3.reduce(d);
          };
        }, { "../../../errors": 15, "./end-of-stream": 24 }], 27: [function(e, n2) {
          "use strict";
          function r2(e2, t2, n3) {
            return null == e2.highWaterMark ? t2 ? e2[n3] : null : e2.highWaterMark;
          }
          var a = e("../../../errors").codes.ERR_INVALID_OPT_VALUE;
          n2.exports = { getHighWaterMark: function(e2, n3, o, i) {
            var d = r2(n3, i, o);
            if (null != d) {
              if (!(isFinite(d) && t(d) === d) || 0 > d) {
                var s = i ? o : "highWaterMark";
                throw new a(s, d);
              }
              return t(d);
            }
            return e2.objectMode ? 16 : 16384;
          } };
        }, { "../../../errors": 15 }], 28: [function(e, t2) {
          t2.exports = e("events").EventEmitter;
        }, { events: 7 }], 29: [function(e, t2, n2) {
          n2 = t2.exports = e("./lib/_stream_readable.js"), n2.Stream = n2, n2.Readable = n2, n2.Writable = e("./lib/_stream_writable.js"), n2.Duplex = e("./lib/_stream_duplex.js"), n2.Transform = e("./lib/_stream_transform.js"), n2.PassThrough = e("./lib/_stream_passthrough.js"), n2.finished = e("./lib/internal/streams/end-of-stream.js"), n2.pipeline = e("./lib/internal/streams/pipeline.js");
        }, { "./lib/_stream_duplex.js": 16, "./lib/_stream_passthrough.js": 17, "./lib/_stream_readable.js": 18, "./lib/_stream_transform.js": 19, "./lib/_stream_writable.js": 20, "./lib/internal/streams/end-of-stream.js": 24, "./lib/internal/streams/pipeline.js": 26 }], 30: [function(e, t2, n2) {
          function r2(e2, t3) {
            for (var n3 in e2) t3[n3] = e2[n3];
          }
          function a(e2, t3, n3) {
            return i(e2, t3, n3);
          }
          var o = e("buffer"), i = o.Buffer;
          i.from && i.alloc && i.allocUnsafe && i.allocUnsafeSlow ? t2.exports = o : (r2(o, n2), n2.Buffer = a), a.prototype = Object.create(i.prototype), r2(i, a), a.from = function(e2, t3, n3) {
            if ("number" == typeof e2) throw new TypeError("Argument must not be a number");
            return i(e2, t3, n3);
          }, a.alloc = function(e2, t3, n3) {
            if ("number" != typeof e2) throw new TypeError("Argument must be a number");
            var r3 = i(e2);
            return void 0 === t3 ? r3.fill(0) : "string" == typeof n3 ? r3.fill(t3, n3) : r3.fill(t3), r3;
          }, a.allocUnsafe = function(e2) {
            if ("number" != typeof e2) throw new TypeError("Argument must be a number");
            return i(e2);
          }, a.allocUnsafeSlow = function(e2) {
            if ("number" != typeof e2) throw new TypeError("Argument must be a number");
            return o.SlowBuffer(e2);
          };
        }, { buffer: 3 }], 31: [function(e, t2, n2) {
          "use strict";
          function r2(e2) {
            if (!e2) return "utf8";
            for (var t3; ; ) switch (e2) {
              case "utf8":
              case "utf-8":
                return "utf8";
              case "ucs2":
              case "ucs-2":
              case "utf16le":
              case "utf-16le":
                return "utf16le";
              case "latin1":
              case "binary":
                return "latin1";
              case "base64":
              case "ascii":
              case "hex":
                return e2;
              default:
                if (t3) return;
                e2 = ("" + e2).toLowerCase(), t3 = true;
            }
          }
          function a(e2) {
            var t3 = r2(e2);
            if ("string" != typeof t3 && (m.isEncoding === b || !b(e2))) throw new Error("Unknown encoding: " + e2);
            return t3 || e2;
          }
          function o(e2) {
            this.encoding = a(e2);
            var t3;
            switch (this.encoding) {
              case "utf16le":
                this.text = u, this.end = p, t3 = 4;
                break;
              case "utf8":
                this.fillLast = c, t3 = 4;
                break;
              case "base64":
                this.text = f, this.end = g, t3 = 3;
                break;
              default:
                return this.write = _, void (this.end = h);
            }
            this.lastNeed = 0, this.lastTotal = 0, this.lastChar = m.allocUnsafe(t3);
          }
          function d(e2) {
            if (127 >= e2) return 0;
            return 6 == e2 >> 5 ? 2 : 14 == e2 >> 4 ? 3 : 30 == e2 >> 3 ? 4 : 2 == e2 >> 6 ? -1 : -2;
          }
          function s(e2, t3, n3) {
            var r3 = t3.length - 1;
            if (r3 < n3) return 0;
            var a2 = d(t3[r3]);
            return 0 <= a2 ? (0 < a2 && (e2.lastNeed = a2 - 1), a2) : --r3 < n3 || -2 === a2 ? 0 : (a2 = d(t3[r3]), 0 <= a2) ? (0 < a2 && (e2.lastNeed = a2 - 2), a2) : --r3 < n3 || -2 === a2 ? 0 : (a2 = d(t3[r3]), 0 <= a2 ? (0 < a2 && (2 === a2 ? a2 = 0 : e2.lastNeed = a2 - 3), a2) : 0);
          }
          function l(e2, t3) {
            if (128 != (192 & t3[0])) return e2.lastNeed = 0, "\uFFFD";
            if (1 < e2.lastNeed && 1 < t3.length) {
              if (128 != (192 & t3[1])) return e2.lastNeed = 1, "\uFFFD";
              if (2 < e2.lastNeed && 2 < t3.length && 128 != (192 & t3[2])) return e2.lastNeed = 2, "\uFFFD";
            }
          }
          function c(e2) {
            var t3 = this.lastTotal - this.lastNeed, n3 = l(this, e2, t3);
            return void 0 === n3 ? this.lastNeed <= e2.length ? (e2.copy(this.lastChar, t3, 0, this.lastNeed), this.lastChar.toString(this.encoding, 0, this.lastTotal)) : void (e2.copy(this.lastChar, t3, 0, e2.length), this.lastNeed -= e2.length) : n3;
          }
          function u(e2, t3) {
            if (0 == (e2.length - t3) % 2) {
              var n3 = e2.toString("utf16le", t3);
              if (n3) {
                var r3 = n3.charCodeAt(n3.length - 1);
                if (55296 <= r3 && 56319 >= r3) return this.lastNeed = 2, this.lastTotal = 4, this.lastChar[0] = e2[e2.length - 2], this.lastChar[1] = e2[e2.length - 1], n3.slice(0, -1);
              }
              return n3;
            }
            return this.lastNeed = 1, this.lastTotal = 2, this.lastChar[0] = e2[e2.length - 1], e2.toString("utf16le", t3, e2.length - 1);
          }
          function p(e2) {
            var t3 = e2 && e2.length ? this.write(e2) : "";
            if (this.lastNeed) {
              var n3 = this.lastTotal - this.lastNeed;
              return t3 + this.lastChar.toString("utf16le", 0, n3);
            }
            return t3;
          }
          function f(e2, t3) {
            var r3 = (e2.length - t3) % 3;
            return 0 == r3 ? e2.toString("base64", t3) : (this.lastNeed = 3 - r3, this.lastTotal = 3, 1 == r3 ? this.lastChar[0] = e2[e2.length - 1] : (this.lastChar[0] = e2[e2.length - 2], this.lastChar[1] = e2[e2.length - 1]), e2.toString("base64", t3, e2.length - r3));
          }
          function g(e2) {
            var t3 = e2 && e2.length ? this.write(e2) : "";
            return this.lastNeed ? t3 + this.lastChar.toString("base64", 0, 3 - this.lastNeed) : t3;
          }
          function _(e2) {
            return e2.toString(this.encoding);
          }
          function h(e2) {
            return e2 && e2.length ? this.write(e2) : "";
          }
          var m = e("safe-buffer").Buffer, b = m.isEncoding || function(e2) {
            switch (e2 = "" + e2, e2 && e2.toLowerCase()) {
              case "hex":
              case "utf8":
              case "utf-8":
              case "ascii":
              case "binary":
              case "base64":
              case "ucs2":
              case "ucs-2":
              case "utf16le":
              case "utf-16le":
              case "raw":
                return true;
              default:
                return false;
            }
          };
          n2.StringDecoder = o, o.prototype.write = function(e2) {
            if (0 === e2.length) return "";
            var t3, n3;
            if (this.lastNeed) {
              if (t3 = this.fillLast(e2), void 0 === t3) return "";
              n3 = this.lastNeed, this.lastNeed = 0;
            } else n3 = 0;
            return n3 < e2.length ? t3 ? t3 + this.text(e2, n3) : this.text(e2, n3) : t3 || "";
          }, o.prototype.end = function(e2) {
            var t3 = e2 && e2.length ? this.write(e2) : "";
            return this.lastNeed ? t3 + "\uFFFD" : t3;
          }, o.prototype.text = function(e2, t3) {
            var n3 = s(this, e2, t3);
            if (!this.lastNeed) return e2.toString("utf8", t3);
            this.lastTotal = n3;
            var r3 = e2.length - (n3 - this.lastNeed);
            return e2.copy(this.lastChar, 0, r3), e2.toString("utf8", t3, r3);
          }, o.prototype.fillLast = function(e2) {
            return this.lastNeed <= e2.length ? (e2.copy(this.lastChar, this.lastTotal - this.lastNeed, 0, this.lastNeed), this.lastChar.toString(this.encoding, 0, this.lastTotal)) : void (e2.copy(this.lastChar, this.lastTotal - this.lastNeed, 0, e2.length), this.lastNeed -= e2.length);
          };
        }, { "safe-buffer": 30 }], 32: [function(e, t2) {
          (function(e2) {
            (function() {
              function n2(t3) {
                try {
                  if (!e2.localStorage) return false;
                } catch (e3) {
                  return false;
                }
                var n3 = e2.localStorage[t3];
                return null != n3 && "true" === (n3 + "").toLowerCase();
              }
              t2.exports = function(e3, t3) {
                function r2() {
                  if (!a) {
                    if (n2("throwDeprecation")) throw new Error(t3);
                    else n2("traceDeprecation") ? console.trace(t3) : console.warn(t3);
                    a = true;
                  }
                  return e3.apply(this, arguments);
                }
                if (n2("noDeprecation")) return e3;
                var a = false;
                return r2;
              };
            }).call(this);
          }).call(this, "undefined" == typeof global ? "undefined" == typeof self ? "undefined" == typeof window ? {} : window : self : global);
        }, {}], "/": [function(e, t2) {
          function n2(e2) {
            return e2.replace(/a=ice-options:trickle\s\n/g, "");
          }
          function r2(e2) {
            console.warn(e2);
          }
          const a = e("debug")("simple-peer"), o = e("get-browser-rtc"), i = e("randombytes"), d = e("readable-stream"), s = e("queue-microtask"), l = e("err-code"), { Buffer: c } = e("buffer"), u = 65536;
          class p extends d.Duplex {
            constructor(e2) {
              if (e2 = Object.assign({ allowHalfOpen: false }, e2), super(e2), this._id = i(4).toString("hex").slice(0, 7), this._debug("new peer %o", e2), this.channelName = e2.initiator ? e2.channelName || i(20).toString("hex") : null, this.initiator = e2.initiator || false, this.channelConfig = e2.channelConfig || p.channelConfig, this.channelNegotiated = this.channelConfig.negotiated, this.config = Object.assign({}, p.config, e2.config), this.offerOptions = e2.offerOptions || {}, this.answerOptions = e2.answerOptions || {}, this.sdpTransform = e2.sdpTransform || ((e3) => e3), this.streams = e2.streams || (e2.stream ? [e2.stream] : []), this.trickle = void 0 === e2.trickle || e2.trickle, this.allowHalfTrickle = void 0 !== e2.allowHalfTrickle && e2.allowHalfTrickle, this.iceCompleteTimeout = e2.iceCompleteTimeout || 5e3, this.destroyed = false, this.destroying = false, this._connected = false, this.remoteAddress = void 0, this.remoteFamily = void 0, this.remotePort = void 0, this.localAddress = void 0, this.localFamily = void 0, this.localPort = void 0, this._wrtc = e2.wrtc && "object" == typeof e2.wrtc ? e2.wrtc : o(), !this._wrtc) if ("undefined" == typeof window) throw l(new Error("No WebRTC support: Specify `opts.wrtc` option in this environment"), "ERR_WEBRTC_SUPPORT");
              else throw l(new Error("No WebRTC support: Not a supported browser"), "ERR_WEBRTC_SUPPORT");
              this._pcReady = false, this._channelReady = false, this._iceComplete = false, this._iceCompleteTimer = null, this._channel = null, this._pendingCandidates = [], this._isNegotiating = false, this._firstNegotiation = true, this._batchedNegotiation = false, this._queuedNegotiation = false, this._sendersAwaitingStable = [], this._senderMap = /* @__PURE__ */ new Map(), this._closingInterval = null, this._remoteTracks = [], this._remoteStreams = [], this._chunk = null, this._cb = null, this._interval = null;
              try {
                this._pc = new this._wrtc.RTCPeerConnection(this.config);
              } catch (e3) {
                return void this.destroy(l(e3, "ERR_PC_CONSTRUCTOR"));
              }
              this._isReactNativeWebrtc = "number" == typeof this._pc._peerConnectionId, this._pc.oniceconnectionstatechange = () => {
                this._onIceStateChange();
              }, this._pc.onicegatheringstatechange = () => {
                this._onIceStateChange();
              }, this._pc.onconnectionstatechange = () => {
                this._onConnectionStateChange();
              }, this._pc.onsignalingstatechange = () => {
                this._onSignalingStateChange();
              }, this._pc.onicecandidate = (e3) => {
                this._onIceCandidate(e3);
              }, "object" == typeof this._pc.peerIdentity && this._pc.peerIdentity.catch((e3) => {
                this.destroy(l(e3, "ERR_PC_PEER_IDENTITY"));
              }), this.initiator || this.channelNegotiated ? this._setupData({ channel: this._pc.createDataChannel(this.channelName, this.channelConfig) }) : this._pc.ondatachannel = (e3) => {
                this._setupData(e3);
              }, this.streams && this.streams.forEach((e3) => {
                this.addStream(e3);
              }), this._pc.ontrack = (e3) => {
                this._onTrack(e3);
              }, this._debug("initial negotiation"), this._needsNegotiation(), this._onFinishBound = () => {
                this._onFinish();
              }, this.once("finish", this._onFinishBound);
            }
            get bufferSize() {
              return this._channel && this._channel.bufferedAmount || 0;
            }
            get connected() {
              return this._connected && "open" === this._channel.readyState;
            }
            address() {
              return { port: this.localPort, family: this.localFamily, address: this.localAddress };
            }
            signal(e2) {
              if (!this.destroying) {
                if (this.destroyed) throw l(new Error("cannot signal after peer is destroyed"), "ERR_DESTROYED");
                if ("string" == typeof e2) try {
                  e2 = JSON.parse(e2);
                } catch (t3) {
                  e2 = {};
                }
                this._debug("signal()"), e2.renegotiate && this.initiator && (this._debug("got request to renegotiate"), this._needsNegotiation()), e2.transceiverRequest && this.initiator && (this._debug("got request for transceiver"), this.addTransceiver(e2.transceiverRequest.kind, e2.transceiverRequest.init)), e2.candidate && (this._pc.remoteDescription && this._pc.remoteDescription.type ? this._addIceCandidate(e2.candidate) : this._pendingCandidates.push(e2.candidate)), e2.sdp && this._pc.setRemoteDescription(new this._wrtc.RTCSessionDescription(e2)).then(() => {
                  this.destroyed || (this._pendingCandidates.forEach((e3) => {
                    this._addIceCandidate(e3);
                  }), this._pendingCandidates = [], "offer" === this._pc.remoteDescription.type && this._createAnswer());
                }).catch((e3) => {
                  this.destroy(l(e3, "ERR_SET_REMOTE_DESCRIPTION"));
                }), e2.sdp || e2.candidate || e2.renegotiate || e2.transceiverRequest || this.destroy(l(new Error("signal() called with invalid signal data"), "ERR_SIGNALING"));
              }
            }
            _addIceCandidate(e2) {
              const t3 = new this._wrtc.RTCIceCandidate(e2);
              this._pc.addIceCandidate(t3).catch((e3) => {
                !t3.address || t3.address.endsWith(".local") ? r2("Ignoring unsupported ICE candidate.") : this.destroy(l(e3, "ERR_ADD_ICE_CANDIDATE"));
              });
            }
            send(e2) {
              if (!this.destroying) {
                if (this.destroyed) throw l(new Error("cannot send after peer is destroyed"), "ERR_DESTROYED");
                this._channel.send(e2);
              }
            }
            addTransceiver(e2, t3) {
              if (!this.destroying) {
                if (this.destroyed) throw l(new Error("cannot addTransceiver after peer is destroyed"), "ERR_DESTROYED");
                if (this._debug("addTransceiver()"), this.initiator) try {
                  this._pc.addTransceiver(e2, t3), this._needsNegotiation();
                } catch (e3) {
                  this.destroy(l(e3, "ERR_ADD_TRANSCEIVER"));
                }
                else this.emit("signal", { type: "transceiverRequest", transceiverRequest: { kind: e2, init: t3 } });
              }
            }
            addStream(e2) {
              if (!this.destroying) {
                if (this.destroyed) throw l(new Error("cannot addStream after peer is destroyed"), "ERR_DESTROYED");
                this._debug("addStream()"), e2.getTracks().forEach((t3) => {
                  this.addTrack(t3, e2);
                });
              }
            }
            addTrack(e2, t3) {
              if (this.destroying) return;
              if (this.destroyed) throw l(new Error("cannot addTrack after peer is destroyed"), "ERR_DESTROYED");
              this._debug("addTrack()");
              const n3 = this._senderMap.get(e2) || /* @__PURE__ */ new Map();
              let r3 = n3.get(t3);
              if (!r3) r3 = this._pc.addTrack(e2, t3), n3.set(t3, r3), this._senderMap.set(e2, n3), this._needsNegotiation();
              else if (r3.removed) throw l(new Error("Track has been removed. You should enable/disable tracks that you want to re-add."), "ERR_SENDER_REMOVED");
              else throw l(new Error("Track has already been added to that stream."), "ERR_SENDER_ALREADY_ADDED");
            }
            replaceTrack(e2, t3, n3) {
              if (this.destroying) return;
              if (this.destroyed) throw l(new Error("cannot replaceTrack after peer is destroyed"), "ERR_DESTROYED");
              this._debug("replaceTrack()");
              const r3 = this._senderMap.get(e2), a2 = r3 ? r3.get(n3) : null;
              if (!a2) throw l(new Error("Cannot replace track that was never added."), "ERR_TRACK_NOT_ADDED");
              t3 && this._senderMap.set(t3, r3), null == a2.replaceTrack ? this.destroy(l(new Error("replaceTrack is not supported in this browser"), "ERR_UNSUPPORTED_REPLACETRACK")) : a2.replaceTrack(t3);
            }
            removeTrack(e2, t3) {
              if (this.destroying) return;
              if (this.destroyed) throw l(new Error("cannot removeTrack after peer is destroyed"), "ERR_DESTROYED");
              this._debug("removeSender()");
              const n3 = this._senderMap.get(e2), r3 = n3 ? n3.get(t3) : null;
              if (!r3) throw l(new Error("Cannot remove track that was never added."), "ERR_TRACK_NOT_ADDED");
              try {
                r3.removed = true, this._pc.removeTrack(r3);
              } catch (e3) {
                "NS_ERROR_UNEXPECTED" === e3.name ? this._sendersAwaitingStable.push(r3) : this.destroy(l(e3, "ERR_REMOVE_TRACK"));
              }
              this._needsNegotiation();
            }
            removeStream(e2) {
              if (!this.destroying) {
                if (this.destroyed) throw l(new Error("cannot removeStream after peer is destroyed"), "ERR_DESTROYED");
                this._debug("removeSenders()"), e2.getTracks().forEach((t3) => {
                  this.removeTrack(t3, e2);
                });
              }
            }
            _needsNegotiation() {
              this._debug("_needsNegotiation"), this._batchedNegotiation || (this._batchedNegotiation = true, s(() => {
                this._batchedNegotiation = false, this.initiator || !this._firstNegotiation ? (this._debug("starting batched negotiation"), this.negotiate()) : this._debug("non-initiator initial negotiation request discarded"), this._firstNegotiation = false;
              }));
            }
            negotiate() {
              if (!this.destroying) {
                if (this.destroyed) throw l(new Error("cannot negotiate after peer is destroyed"), "ERR_DESTROYED");
                this.initiator ? this._isNegotiating ? (this._queuedNegotiation = true, this._debug("already negotiating, queueing")) : (this._debug("start negotiation"), setTimeout(() => {
                  this._createOffer();
                }, 0)) : this._isNegotiating ? (this._queuedNegotiation = true, this._debug("already negotiating, queueing")) : (this._debug("requesting negotiation from initiator"), this.emit("signal", { type: "renegotiate", renegotiate: true })), this._isNegotiating = true;
              }
            }
            destroy(e2) {
              this._destroy(e2, () => {
              });
            }
            _destroy(e2, t3) {
              this.destroyed || this.destroying || (this.destroying = true, this._debug("destroying (error: %s)", e2 && (e2.message || e2)), s(() => {
                if (this.destroyed = true, this.destroying = false, this._debug("destroy (error: %s)", e2 && (e2.message || e2)), this.readable = this.writable = false, this._readableState.ended || this.push(null), this._writableState.finished || this.end(), this._connected = false, this._pcReady = false, this._channelReady = false, this._remoteTracks = null, this._remoteStreams = null, this._senderMap = null, clearInterval(this._closingInterval), this._closingInterval = null, clearInterval(this._interval), this._interval = null, this._chunk = null, this._cb = null, this._onFinishBound && this.removeListener("finish", this._onFinishBound), this._onFinishBound = null, this._channel) {
                  try {
                    this._channel.close();
                  } catch (e3) {
                  }
                  this._channel.onmessage = null, this._channel.onopen = null, this._channel.onclose = null, this._channel.onerror = null;
                }
                if (this._pc) {
                  try {
                    this._pc.close();
                  } catch (e3) {
                  }
                  this._pc.oniceconnectionstatechange = null, this._pc.onicegatheringstatechange = null, this._pc.onsignalingstatechange = null, this._pc.onicecandidate = null, this._pc.ontrack = null, this._pc.ondatachannel = null;
                }
                this._pc = null, this._channel = null, e2 && this.emit("error", e2), this.emit("close"), t3();
              }));
            }
            _setupData(e2) {
              if (!e2.channel) return this.destroy(l(new Error("Data channel event is missing `channel` property"), "ERR_DATA_CHANNEL"));
              this._channel = e2.channel, this._channel.binaryType = "arraybuffer", "number" == typeof this._channel.bufferedAmountLowThreshold && (this._channel.bufferedAmountLowThreshold = u), this.channelName = this._channel.label, this._channel.onmessage = (e3) => {
                this._onChannelMessage(e3);
              }, this._channel.onbufferedamountlow = () => {
                this._onChannelBufferedAmountLow();
              }, this._channel.onopen = () => {
                this._onChannelOpen();
              }, this._channel.onclose = () => {
                this._onChannelClose();
              }, this._channel.onerror = (e3) => {
                const t4 = e3.error instanceof Error ? e3.error : new Error(`Datachannel error: ${e3.message} ${e3.filename}:${e3.lineno}:${e3.colno}`);
                this.destroy(l(t4, "ERR_DATA_CHANNEL"));
              };
              let t3 = false;
              this._closingInterval = setInterval(() => {
                this._channel && "closing" === this._channel.readyState ? (t3 && this._onChannelClose(), t3 = true) : t3 = false;
              }, 5e3);
            }
            _read() {
            }
            _write(e2, t3, n3) {
              if (this.destroyed) return n3(l(new Error("cannot write after peer is destroyed"), "ERR_DATA_CHANNEL"));
              if (this._connected) {
                try {
                  this.send(e2);
                } catch (e3) {
                  return this.destroy(l(e3, "ERR_DATA_CHANNEL"));
                }
                this._channel.bufferedAmount > u ? (this._debug("start backpressure: bufferedAmount %d", this._channel.bufferedAmount), this._cb = n3) : n3(null);
              } else this._debug("write before connect"), this._chunk = e2, this._cb = n3;
            }
            _onFinish() {
              if (!this.destroyed) {
                const e2 = () => {
                  setTimeout(() => this.destroy(), 1e3);
                };
                this._connected ? e2() : this.once("connect", e2);
              }
            }
            _startIceCompleteTimeout() {
              this.destroyed || this._iceCompleteTimer || (this._debug("started iceComplete timeout"), this._iceCompleteTimer = setTimeout(() => {
                this._iceComplete || (this._iceComplete = true, this._debug("iceComplete timeout completed"), this.emit("iceTimeout"), this.emit("_iceComplete"));
              }, this.iceCompleteTimeout));
            }
            _createOffer() {
              this.destroyed || this._pc.createOffer(this.offerOptions).then((e2) => {
                if (this.destroyed) return;
                this.trickle || this.allowHalfTrickle || (e2.sdp = n2(e2.sdp)), e2.sdp = this.sdpTransform(e2.sdp);
                const t3 = () => {
                  if (!this.destroyed) {
                    const t4 = this._pc.localDescription || e2;
                    this._debug("signal"), this.emit("signal", { type: t4.type, sdp: t4.sdp });
                  }
                };
                this._pc.setLocalDescription(e2).then(() => {
                  this._debug("createOffer success"), this.destroyed || (this.trickle || this._iceComplete ? t3() : this.once("_iceComplete", t3));
                }).catch((e3) => {
                  this.destroy(l(e3, "ERR_SET_LOCAL_DESCRIPTION"));
                });
              }).catch((e2) => {
                this.destroy(l(e2, "ERR_CREATE_OFFER"));
              });
            }
            _requestMissingTransceivers() {
              this._pc.getTransceivers && this._pc.getTransceivers().forEach((e2) => {
                e2.mid || !e2.sender.track || e2.requested || (e2.requested = true, this.addTransceiver(e2.sender.track.kind));
              });
            }
            _createAnswer() {
              this.destroyed || this._pc.createAnswer(this.answerOptions).then((e2) => {
                if (this.destroyed) return;
                this.trickle || this.allowHalfTrickle || (e2.sdp = n2(e2.sdp)), e2.sdp = this.sdpTransform(e2.sdp);
                const t3 = () => {
                  if (!this.destroyed) {
                    const t4 = this._pc.localDescription || e2;
                    this._debug("signal"), this.emit("signal", { type: t4.type, sdp: t4.sdp }), this.initiator || this._requestMissingTransceivers();
                  }
                };
                this._pc.setLocalDescription(e2).then(() => {
                  this.destroyed || (this.trickle || this._iceComplete ? t3() : this.once("_iceComplete", t3));
                }).catch((e3) => {
                  this.destroy(l(e3, "ERR_SET_LOCAL_DESCRIPTION"));
                });
              }).catch((e2) => {
                this.destroy(l(e2, "ERR_CREATE_ANSWER"));
              });
            }
            _onConnectionStateChange() {
              this.destroyed || "failed" === this._pc.connectionState && this.destroy(l(new Error("Connection failed."), "ERR_CONNECTION_FAILURE"));
            }
            _onIceStateChange() {
              if (this.destroyed) return;
              const e2 = this._pc.iceConnectionState, t3 = this._pc.iceGatheringState;
              this._debug("iceStateChange (connection: %s) (gathering: %s)", e2, t3), this.emit("iceStateChange", e2, t3), ("connected" === e2 || "completed" === e2) && (this._pcReady = true, this._maybeReady()), "failed" === e2 && this.destroy(l(new Error("Ice connection failed."), "ERR_ICE_CONNECTION_FAILURE")), "closed" === e2 && this.destroy(l(new Error("Ice connection closed."), "ERR_ICE_CONNECTION_CLOSED"));
            }
            getStats(e2) {
              const t3 = (e3) => ("[object Array]" === Object.prototype.toString.call(e3.values) && e3.values.forEach((t4) => {
                Object.assign(e3, t4);
              }), e3);
              0 === this._pc.getStats.length || this._isReactNativeWebrtc ? this._pc.getStats().then((n3) => {
                const r3 = [];
                n3.forEach((e3) => {
                  r3.push(t3(e3));
                }), e2(null, r3);
              }, (t4) => e2(t4)) : 0 < this._pc.getStats.length ? this._pc.getStats((n3) => {
                if (this.destroyed) return;
                const r3 = [];
                n3.result().forEach((e3) => {
                  const n4 = {};
                  e3.names().forEach((t4) => {
                    n4[t4] = e3.stat(t4);
                  }), n4.id = e3.id, n4.type = e3.type, n4.timestamp = e3.timestamp, r3.push(t3(n4));
                }), e2(null, r3);
              }, (t4) => e2(t4)) : e2(null, []);
            }
            _maybeReady() {
              if (this._debug("maybeReady pc %s channel %s", this._pcReady, this._channelReady), this._connected || this._connecting || !this._pcReady || !this._channelReady) return;
              this._connecting = true;
              const e2 = () => {
                this.destroyed || this.getStats((t3, n3) => {
                  if (this.destroyed) return;
                  t3 && (n3 = []);
                  const r3 = {}, a2 = {}, o2 = {};
                  let i2 = false;
                  n3.forEach((e3) => {
                    ("remotecandidate" === e3.type || "remote-candidate" === e3.type) && (r3[e3.id] = e3), ("localcandidate" === e3.type || "local-candidate" === e3.type) && (a2[e3.id] = e3), ("candidatepair" === e3.type || "candidate-pair" === e3.type) && (o2[e3.id] = e3);
                  });
                  const d2 = (e3) => {
                    i2 = true;
                    let t4 = a2[e3.localCandidateId];
                    t4 && (t4.ip || t4.address) ? (this.localAddress = t4.ip || t4.address, this.localPort = +t4.port) : t4 && t4.ipAddress ? (this.localAddress = t4.ipAddress, this.localPort = +t4.portNumber) : "string" == typeof e3.googLocalAddress && (t4 = e3.googLocalAddress.split(":"), this.localAddress = t4[0], this.localPort = +t4[1]), this.localAddress && (this.localFamily = this.localAddress.includes(":") ? "IPv6" : "IPv4");
                    let n4 = r3[e3.remoteCandidateId];
                    n4 && (n4.ip || n4.address) ? (this.remoteAddress = n4.ip || n4.address, this.remotePort = +n4.port) : n4 && n4.ipAddress ? (this.remoteAddress = n4.ipAddress, this.remotePort = +n4.portNumber) : "string" == typeof e3.googRemoteAddress && (n4 = e3.googRemoteAddress.split(":"), this.remoteAddress = n4[0], this.remotePort = +n4[1]), this.remoteAddress && (this.remoteFamily = this.remoteAddress.includes(":") ? "IPv6" : "IPv4"), this._debug("connect local: %s:%s remote: %s:%s", this.localAddress, this.localPort, this.remoteAddress, this.remotePort);
                  };
                  if (n3.forEach((e3) => {
                    "transport" === e3.type && e3.selectedCandidatePairId && d2(o2[e3.selectedCandidatePairId]), ("googCandidatePair" === e3.type && "true" === e3.googActiveConnection || ("candidatepair" === e3.type || "candidate-pair" === e3.type) && e3.selected) && d2(e3);
                  }), !i2 && (!Object.keys(o2).length || Object.keys(a2).length)) return void setTimeout(e2, 100);
                  if (this._connecting = false, this._connected = true, this._chunk) {
                    try {
                      this.send(this._chunk);
                    } catch (e4) {
                      return this.destroy(l(e4, "ERR_DATA_CHANNEL"));
                    }
                    this._chunk = null, this._debug('sent chunk from "write before connect"');
                    const e3 = this._cb;
                    this._cb = null, e3(null);
                  }
                  "number" != typeof this._channel.bufferedAmountLowThreshold && (this._interval = setInterval(() => this._onInterval(), 150), this._interval.unref && this._interval.unref()), this._debug("connect"), this.emit("connect");
                });
              };
              e2();
            }
            _onInterval() {
              this._cb && this._channel && !(this._channel.bufferedAmount > u) && this._onChannelBufferedAmountLow();
            }
            _onSignalingStateChange() {
              this.destroyed || ("stable" === this._pc.signalingState && (this._isNegotiating = false, this._debug("flushing sender queue", this._sendersAwaitingStable), this._sendersAwaitingStable.forEach((e2) => {
                this._pc.removeTrack(e2), this._queuedNegotiation = true;
              }), this._sendersAwaitingStable = [], this._queuedNegotiation ? (this._debug("flushing negotiation queue"), this._queuedNegotiation = false, this._needsNegotiation()) : (this._debug("negotiated"), this.emit("negotiated"))), this._debug("signalingStateChange %s", this._pc.signalingState), this.emit("signalingStateChange", this._pc.signalingState));
            }
            _onIceCandidate(e2) {
              this.destroyed || (e2.candidate && this.trickle ? this.emit("signal", { type: "candidate", candidate: { candidate: e2.candidate.candidate, sdpMLineIndex: e2.candidate.sdpMLineIndex, sdpMid: e2.candidate.sdpMid } }) : !e2.candidate && !this._iceComplete && (this._iceComplete = true, this.emit("_iceComplete")), e2.candidate && this._startIceCompleteTimeout());
            }
            _onChannelMessage(e2) {
              if (this.destroyed) return;
              let t3 = e2.data;
              t3 instanceof ArrayBuffer && (t3 = c.from(t3)), this.push(t3);
            }
            _onChannelBufferedAmountLow() {
              if (!this.destroyed && this._cb) {
                this._debug("ending backpressure: bufferedAmount %d", this._channel.bufferedAmount);
                const e2 = this._cb;
                this._cb = null, e2(null);
              }
            }
            _onChannelOpen() {
              this._connected || this.destroyed || (this._debug("on channel open"), this._channelReady = true, this._maybeReady());
            }
            _onChannelClose() {
              this.destroyed || (this._debug("on channel close"), this.destroy());
            }
            _onTrack(e2) {
              this.destroyed || e2.streams.forEach((t3) => {
                this._debug("on track"), this.emit("track", e2.track, t3), this._remoteTracks.push({ track: e2.track, stream: t3 }), this._remoteStreams.some((e3) => e3.id === t3.id) || (this._remoteStreams.push(t3), s(() => {
                  this._debug("on stream"), this.emit("stream", t3);
                }));
              });
            }
            _debug() {
              const e2 = [].slice.call(arguments);
              e2[0] = "[" + this._id + "] " + e2[0], a.apply(null, e2);
            }
          }
          p.WEBRTC_SUPPORT = !!o(), p.config = { iceServers: [{ urls: ["stun:stun.l.google.com:19302", "stun:global.stun.twilio.com:3478"] }], sdpSemantics: "unified-plan" }, p.channelConfig = {}, t2.exports = p;
        }, { buffer: 3, debug: 4, "err-code": 6, "get-browser-rtc": 8, "queue-microtask": 13, randombytes: 14, "readable-stream": 29 }] }, {}, [])("/");
      });
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

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // package-external:@wordpress/html-entities
  var require_html_entities = __commonJS({
    "package-external:@wordpress/html-entities"(exports, module) {
      module.exports = window.wp.htmlEntities;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // package-external:@wordpress/warning
  var require_warning = __commonJS({
    "package-external:@wordpress/warning"(exports, module) {
      module.exports = window.wp.warning;
    }
  });

  // package-external:@wordpress/block-editor
  var require_block_editor = __commonJS({
    "package-external:@wordpress/block-editor"(exports, module) {
      module.exports = window.wp.blockEditor;
    }
  });

  // packages/core-data/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    EntityProvider: () => EntityProvider,
    __experimentalFetchLinkSuggestions: () => fetchLinkSuggestions,
    __experimentalFetchUrlData: () => experimental_fetch_url_data_default,
    __experimentalUseEntityRecord: () => __experimentalUseEntityRecord,
    __experimentalUseEntityRecords: () => __experimentalUseEntityRecords,
    __experimentalUseResourcePermissions: () => __experimentalUseResourcePermissions,
    fetchBlockPatterns: () => fetchBlockPatterns,
    privateApis: () => privateApis,
    store: () => store,
    useEntityBlockEditor: () => useEntityBlockEditor,
    useEntityId: () => useEntityId,
    useEntityProp: () => useEntityProp,
    useEntityRecord: () => useEntityRecord,
    useEntityRecords: () => useEntityRecords,
    useResourcePermissions: () => use_resource_permissions_default
  });
  var import_data11 = __toESM(require_data());

  // packages/core-data/build-module/reducer.js
  var import_es64 = __toESM(require_es6());
  var import_compose2 = __toESM(require_compose());
  var import_data3 = __toESM(require_data());
  var import_undo_manager = __toESM(require_undo_manager());

  // packages/core-data/build-module/utils/conservative-map-item.js
  var import_es6 = __toESM(require_es6());
  function conservativeMapItem(item, nextItem) {
    if (!item) {
      return nextItem;
    }
    let hasChanges = false;
    const result = {};
    for (const key in nextItem) {
      if ((0, import_es6.default)(item[key], nextItem[key])) {
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

  // packages/core-data/build-module/utils/get-normalized-comma-separable.js
  function getNormalizedCommaSeparable(value) {
    if (typeof value === "string") {
      return value.split(",");
    } else if (Array.isArray(value)) {
      return value;
    }
    return null;
  }
  var get_normalized_comma_separable_default = getNormalizedCommaSeparable;

  // packages/core-data/build-module/utils/if-matching-action.js
  var ifMatchingAction = (isMatch) => (reducer) => (state, action) => {
    if (state === void 0 || isMatch(action)) {
      return reducer(state, action);
    }
    return state;
  };
  var if_matching_action_default = ifMatchingAction;

  // packages/core-data/build-module/utils/forward-resolver.js
  var forwardResolver = (resolverName) => (...args2) => async ({ resolveSelect }) => {
    await resolveSelect[resolverName](...args2);
  };
  var forward_resolver_default = forwardResolver;

  // packages/core-data/build-module/utils/on-sub-key.js
  var onSubKey = (actionProperty) => (reducer) => (state = {}, action) => {
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

  // packages/core-data/build-module/utils/replace-action.js
  var replaceAction = (replacer) => (reducer) => (state, action) => {
    return reducer(state, replacer(action));
  };
  var replace_action_default = replaceAction;

  // packages/core-data/build-module/utils/with-weak-map-cache.js
  function withWeakMapCache(fn) {
    const cache3 = /* @__PURE__ */ new WeakMap();
    return (key) => {
      let value;
      if (cache3.has(key)) {
        value = cache3.get(key);
      } else {
        value = fn(key);
        if (key !== null && typeof key === "object") {
          cache3.set(key, value);
        }
      }
      return value;
    };
  }
  var with_weak_map_cache_default = withWeakMapCache;

  // packages/core-data/build-module/utils/is-raw-attribute.js
  function isRawAttribute(entity2, attribute) {
    return (entity2.rawAttributes || []).includes(attribute);
  }

  // packages/core-data/build-module/utils/set-nested-value.js
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

  // packages/core-data/build-module/utils/get-nested-value.js
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

  // packages/core-data/build-module/utils/is-numeric-id.js
  function isNumericID(id2) {
    return /^\s*\d+\s*$/.test(id2);
  }

  // packages/core-data/build-module/utils/user-permissions.js
  var ALLOWED_RESOURCE_ACTIONS = [
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
  function getUserPermissionCacheKey(action, resource, id2) {
    const key = (typeof resource === "object" ? [action, resource.kind, resource.name, resource.id] : [action, resource, id2]).filter(Boolean).join("/");
    return key;
  }

  // packages/core-data/build-module/utils/receive-intermediate-results.js
  var RECEIVE_INTERMEDIATE_RESULTS = Symbol(
    "RECEIVE_INTERMEDIATE_RESULTS"
  );

  // packages/core-data/build-module/queried-data/actions.js
  function receiveItems(items2, edits, meta) {
    return {
      type: "RECEIVE_ITEMS",
      items: Array.isArray(items2) ? items2 : [items2],
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
  function receiveQueriedItems(items2, query = {}, edits, meta) {
    return {
      ...receiveItems(items2, edits, meta),
      query
    };
  }

  // packages/core-data/build-module/queried-data/selectors.js
  var import_equivalent_key_map = __toESM(require_equivalent_key_map());
  var import_data = __toESM(require_data());

  // packages/core-data/build-module/queried-data/get-query-parts.js
  var import_url = __toESM(require_url());
  function getQueryParts(query) {
    const parts = {
      stableKey: "",
      page: 1,
      perPage: 10,
      fields: null,
      include: null,
      context: "default"
    };
    const keys2 = Object.keys(query).sort();
    for (let i = 0; i < keys2.length; i++) {
      const key = keys2[i];
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
            parts.fields = get_normalized_comma_separable_default(value) ?? [];
            value = parts.fields.join();
          }
          if (key === "include") {
            if (typeof value === "number") {
              value = value.toString();
            }
            parts.include = (get_normalized_comma_separable_default(value) ?? []).map(Number);
            value = parts.include.join();
          }
          parts.stableKey += (parts.stableKey ? "&" : "") + (0, import_url.addQueryArgs)("", { [key]: value }).slice(1);
      }
    }
    return parts;
  }
  var get_query_parts_default = with_weak_map_cache_default(getQueryParts);

  // packages/core-data/build-module/queried-data/selectors.js
  var queriedItemsCacheByState = /* @__PURE__ */ new WeakMap();
  function getQueriedItemsUncached(state, query) {
    const { stableKey, page, perPage, include, fields, context } = get_query_parts_default(query);
    let itemIds;
    if (state.queries?.[context]?.[stableKey]) {
      itemIds = state.queries[context][stableKey].itemIds;
    }
    if (!itemIds) {
      return null;
    }
    const startOffset = perPage === -1 ? 0 : (page - 1) * perPage;
    const endOffset = perPage === -1 ? itemIds.length : Math.min(startOffset + perPage, itemIds.length);
    const items2 = [];
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
          setNestedValue(filteredItem, field, value);
        }
      } else {
        if (!state.itemIsComplete[context]?.[itemId]) {
          return null;
        }
        filteredItem = item;
      }
      items2.push(filteredItem);
    }
    return items2;
  }
  var getQueriedItems = (0, import_data.createSelector)((state, query = {}) => {
    let queriedItemsCache = queriedItemsCacheByState.get(state);
    if (queriedItemsCache) {
      const queriedItems = queriedItemsCache.get(query);
      if (queriedItems !== void 0) {
        return queriedItems;
      }
    } else {
      queriedItemsCache = new import_equivalent_key_map.default();
      queriedItemsCacheByState.set(state, queriedItemsCache);
    }
    const items2 = getQueriedItemsUncached(state, query);
    queriedItemsCache.set(query, items2);
    return items2;
  });
  function getQueriedTotalItems(state, query = {}) {
    const { stableKey, context } = get_query_parts_default(query);
    return state.queries?.[context]?.[stableKey]?.meta?.totalItems ?? null;
  }
  function getQueriedTotalPages(state, query = {}) {
    const { stableKey, context } = get_query_parts_default(query);
    return state.queries?.[context]?.[stableKey]?.meta?.totalPages ?? null;
  }

  // packages/core-data/build-module/queried-data/reducer.js
  var import_data2 = __toESM(require_data());
  var import_compose = __toESM(require_compose());

  // node_modules/tslib/tslib.es6.mjs
  var __assign = function() {
    __assign = Object.assign || function __assign2(t) {
      for (var s, i = 1, n = arguments.length; i < n; i++) {
        s = arguments[i];
        for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
      }
      return t;
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

  // node_modules/upper-case-first/dist.es2015/index.js
  function upperCaseFirst(input) {
    return input.charAt(0).toUpperCase() + input.substr(1);
  }

  // node_modules/capital-case/dist.es2015/index.js
  function capitalCaseTransform(input) {
    return upperCaseFirst(input.toLowerCase());
  }
  function capitalCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    return noCase(input, __assign({ delimiter: " ", transform: capitalCaseTransform }, options));
  }

  // packages/core-data/build-module/entities.js
  var import_api_fetch = __toESM(require_api_fetch());
  var import_blocks3 = __toESM(require_blocks());
  var import_i18n = __toESM(require_i18n());

  // node_modules/yjs/dist/yjs.mjs
  var yjs_exports = {};
  __export(yjs_exports, {
    AbsolutePosition: () => AbsolutePosition,
    AbstractConnector: () => AbstractConnector,
    AbstractStruct: () => AbstractStruct,
    AbstractType: () => AbstractType,
    Array: () => YArray,
    ContentAny: () => ContentAny,
    ContentBinary: () => ContentBinary,
    ContentDeleted: () => ContentDeleted,
    ContentEmbed: () => ContentEmbed,
    ContentFormat: () => ContentFormat,
    ContentJSON: () => ContentJSON,
    ContentString: () => ContentString,
    ContentType: () => ContentType,
    Doc: () => Doc,
    GC: () => GC,
    ID: () => ID,
    Item: () => Item,
    Map: () => YMap,
    PermanentUserData: () => PermanentUserData,
    RelativePosition: () => RelativePosition,
    Snapshot: () => Snapshot,
    Text: () => YText,
    Transaction: () => Transaction,
    UndoManager: () => UndoManager,
    UpdateEncoderV1: () => UpdateEncoderV1,
    XmlElement: () => YXmlElement,
    XmlFragment: () => YXmlFragment,
    XmlHook: () => YXmlHook,
    XmlText: () => YXmlText,
    YArrayEvent: () => YArrayEvent,
    YEvent: () => YEvent,
    YMapEvent: () => YMapEvent,
    YTextEvent: () => YTextEvent,
    YXmlEvent: () => YXmlEvent,
    applyUpdate: () => applyUpdate,
    applyUpdateV2: () => applyUpdateV2,
    cleanupYTextFormatting: () => cleanupYTextFormatting,
    compareIDs: () => compareIDs,
    compareRelativePositions: () => compareRelativePositions,
    convertUpdateFormatV1ToV2: () => convertUpdateFormatV1ToV2,
    convertUpdateFormatV2ToV1: () => convertUpdateFormatV2ToV1,
    createAbsolutePositionFromRelativePosition: () => createAbsolutePositionFromRelativePosition,
    createDeleteSet: () => createDeleteSet,
    createDeleteSetFromStructStore: () => createDeleteSetFromStructStore,
    createDocFromSnapshot: () => createDocFromSnapshot,
    createID: () => createID,
    createRelativePositionFromJSON: () => createRelativePositionFromJSON,
    createRelativePositionFromTypeIndex: () => createRelativePositionFromTypeIndex,
    createSnapshot: () => createSnapshot,
    decodeRelativePosition: () => decodeRelativePosition,
    decodeSnapshot: () => decodeSnapshot,
    decodeSnapshotV2: () => decodeSnapshotV2,
    decodeStateVector: () => decodeStateVector,
    decodeUpdate: () => decodeUpdate,
    decodeUpdateV2: () => decodeUpdateV2,
    diffUpdate: () => diffUpdate,
    diffUpdateV2: () => diffUpdateV2,
    emptySnapshot: () => emptySnapshot,
    encodeRelativePosition: () => encodeRelativePosition,
    encodeSnapshot: () => encodeSnapshot,
    encodeSnapshotV2: () => encodeSnapshotV2,
    encodeStateAsUpdate: () => encodeStateAsUpdate,
    encodeStateAsUpdateV2: () => encodeStateAsUpdateV2,
    encodeStateVector: () => encodeStateVector,
    encodeStateVectorFromUpdate: () => encodeStateVectorFromUpdate,
    encodeStateVectorFromUpdateV2: () => encodeStateVectorFromUpdateV2,
    equalDeleteSets: () => equalDeleteSets,
    equalSnapshots: () => equalSnapshots,
    findIndexSS: () => findIndexSS,
    findRootTypeKey: () => findRootTypeKey,
    getItem: () => getItem,
    getState: () => getState,
    getTypeChildren: () => getTypeChildren,
    isDeleted: () => isDeleted,
    isParentOf: () => isParentOf,
    iterateDeletedStructs: () => iterateDeletedStructs,
    logType: () => logType,
    logUpdate: () => logUpdate,
    logUpdateV2: () => logUpdateV2,
    mergeUpdates: () => mergeUpdates,
    mergeUpdatesV2: () => mergeUpdatesV2,
    obfuscateUpdate: () => obfuscateUpdate,
    obfuscateUpdateV2: () => obfuscateUpdateV2,
    parseUpdateMeta: () => parseUpdateMeta,
    parseUpdateMetaV2: () => parseUpdateMetaV2,
    readUpdate: () => readUpdate,
    readUpdateV2: () => readUpdateV2,
    relativePositionToJSON: () => relativePositionToJSON,
    snapshot: () => snapshot,
    snapshotContainsUpdate: () => snapshotContainsUpdate,
    transact: () => transact,
    tryGc: () => tryGc,
    typeListToArraySnapshot: () => typeListToArraySnapshot,
    typeMapGetSnapshot: () => typeMapGetSnapshot
  });

  // node_modules/lib0/map.js
  var create = () => /* @__PURE__ */ new Map();
  var copy = (m) => {
    const r = create();
    m.forEach((v, k) => {
      r.set(k, v);
    });
    return r;
  };
  var setIfUndefined = (map2, key, createT) => {
    let set = map2.get(key);
    if (set === void 0) {
      map2.set(key, set = createT());
    }
    return set;
  };
  var map = (m, f) => {
    const res = [];
    for (const [key, value] of m) {
      res.push(f(value, key));
    }
    return res;
  };
  var any = (m, f) => {
    for (const [key, value] of m) {
      if (f(value, key)) {
        return true;
      }
    }
    return false;
  };

  // node_modules/lib0/set.js
  var create2 = () => /* @__PURE__ */ new Set();

  // node_modules/lib0/array.js
  var last = (arr) => arr[arr.length - 1];
  var appendTo = (dest, src) => {
    for (let i = 0; i < src.length; i++) {
      dest.push(src[i]);
    }
  };
  var from = Array.from;
  var some = (arr, f) => {
    for (let i = 0; i < arr.length; i++) {
      if (f(arr[i], i, arr)) {
        return true;
      }
    }
    return false;
  };
  var unfold = (len, f) => {
    const array = new Array(len);
    for (let i = 0; i < len; i++) {
      array[i] = f(i, array);
    }
    return array;
  };
  var isArray = Array.isArray;

  // node_modules/lib0/observable.js
  var Observable = class {
    constructor() {
      this._observers = create();
    }
    /**
     * @param {N} name
     * @param {function} f
     */
    on(name, f) {
      setIfUndefined(this._observers, name, create2).add(f);
    }
    /**
     * @param {N} name
     * @param {function} f
     */
    once(name, f) {
      const _f = (...args2) => {
        this.off(name, _f);
        f(...args2);
      };
      this.on(name, _f);
    }
    /**
     * @param {N} name
     * @param {function} f
     */
    off(name, f) {
      const observers = this._observers.get(name);
      if (observers !== void 0) {
        observers.delete(f);
        if (observers.size === 0) {
          this._observers.delete(name);
        }
      }
    }
    /**
     * Emit a named event. All registered event listeners that listen to the
     * specified name will receive the event.
     *
     * @todo This should catch exceptions
     *
     * @param {N} name The event name.
     * @param {Array<any>} args The arguments that are applied to the event listener.
     */
    emit(name, args2) {
      return from((this._observers.get(name) || create()).values()).forEach((f) => f(...args2));
    }
    destroy() {
      this._observers = create();
    }
  };

  // node_modules/lib0/math.js
  var floor = Math.floor;
  var abs = Math.abs;
  var log10 = Math.log10;
  var min = (a, b) => a < b ? a : b;
  var max = (a, b) => a > b ? a : b;
  var isNaN2 = Number.isNaN;
  var isNegativeZero = (n) => n !== 0 ? n < 0 : 1 / n < 0;

  // node_modules/lib0/string.js
  var fromCharCode = String.fromCharCode;
  var fromCodePoint = String.fromCodePoint;
  var MAX_UTF16_CHARACTER = fromCharCode(65535);
  var toLowerCase = (s) => s.toLowerCase();
  var trimLeftRegex = /^\s*/g;
  var trimLeft = (s) => s.replace(trimLeftRegex, "");
  var fromCamelCaseRegex = /([A-Z])/g;
  var fromCamelCase = (s, separator) => trimLeft(s.replace(fromCamelCaseRegex, (match) => `${separator}${toLowerCase(match)}`));
  var _encodeUtf8Polyfill = (str) => {
    const encodedString = unescape(encodeURIComponent(str));
    const len = encodedString.length;
    const buf = new Uint8Array(len);
    for (let i = 0; i < len; i++) {
      buf[i] = /** @type {number} */
      encodedString.codePointAt(i);
    }
    return buf;
  };
  var utf8TextEncoder = (
    /** @type {TextEncoder} */
    typeof TextEncoder !== "undefined" ? new TextEncoder() : null
  );
  var _encodeUtf8Native = (str) => utf8TextEncoder.encode(str);
  var encodeUtf8 = utf8TextEncoder ? _encodeUtf8Native : _encodeUtf8Polyfill;
  var utf8TextDecoder = typeof TextDecoder === "undefined" ? null : new TextDecoder("utf-8", { fatal: true, ignoreBOM: true });
  if (utf8TextDecoder && utf8TextDecoder.decode(new Uint8Array()).length === 1) {
    utf8TextDecoder = null;
  }
  var repeat = (source, n) => unfold(n, () => source).join("");

  // node_modules/lib0/conditions.js
  var undefinedToNull = (v) => v === void 0 ? null : v;

  // node_modules/lib0/storage.js
  var VarStoragePolyfill = class {
    constructor() {
      this.map = /* @__PURE__ */ new Map();
    }
    /**
     * @param {string} key
     * @param {any} newValue
     */
    setItem(key, newValue) {
      this.map.set(key, newValue);
    }
    /**
     * @param {string} key
     */
    getItem(key) {
      return this.map.get(key);
    }
  };
  var _localStorage = new VarStoragePolyfill();
  var usePolyfill = true;
  try {
    if (typeof localStorage !== "undefined") {
      _localStorage = localStorage;
      usePolyfill = false;
    }
  } catch (e) {
  }
  var varStorage = _localStorage;
  var onChange = (eventHandler) => usePolyfill || addEventListener(
    "storage",
    /** @type {any} */
    eventHandler
  );
  var offChange = (eventHandler) => usePolyfill || removeEventListener(
    "storage",
    /** @type {any} */
    eventHandler
  );

  // node_modules/lib0/object.js
  var assign = Object.assign;
  var keys = Object.keys;
  var forEach = (obj, f) => {
    for (const key in obj) {
      f(obj[key], key);
    }
  };
  var length = (obj) => keys(obj).length;
  var isEmpty = (obj) => {
    for (const _k in obj) {
      return false;
    }
    return true;
  };
  var every = (obj, f) => {
    for (const key in obj) {
      if (!f(obj[key], key)) {
        return false;
      }
    }
    return true;
  };
  var hasProperty = (obj, key) => Object.prototype.hasOwnProperty.call(obj, key);
  var equalFlat = (a, b) => a === b || length(a) === length(b) && every(a, (val, key) => (val !== void 0 || hasProperty(b, key)) && b[key] === val);

  // node_modules/lib0/function.js
  var callAll = (fs, args2, i = 0) => {
    try {
      for (; i < fs.length; i++) {
        fs[i](...args2);
      }
    } finally {
      if (i < fs.length) {
        callAll(fs, args2, i + 1);
      }
    }
  };
  var nop = () => {
  };
  var id = (a) => a;
  var equalityStrict = (a, b) => a === b;
  var equalityDeep = (a, b) => {
    if (a == null || b == null) {
      return equalityStrict(a, b);
    }
    if (a.constructor !== b.constructor) {
      return false;
    }
    if (a === b) {
      return true;
    }
    switch (a.constructor) {
      case ArrayBuffer:
        a = new Uint8Array(a);
        b = new Uint8Array(b);
      // eslint-disable-next-line no-fallthrough
      case Uint8Array: {
        if (a.byteLength !== b.byteLength) {
          return false;
        }
        for (let i = 0; i < a.length; i++) {
          if (a[i] !== b[i]) {
            return false;
          }
        }
        break;
      }
      case Set: {
        if (a.size !== b.size) {
          return false;
        }
        for (const value of a) {
          if (!b.has(value)) {
            return false;
          }
        }
        break;
      }
      case Map: {
        if (a.size !== b.size) {
          return false;
        }
        for (const key of a.keys()) {
          if (!b.has(key) || !equalityDeep(a.get(key), b.get(key))) {
            return false;
          }
        }
        break;
      }
      case Object:
        if (length(a) !== length(b)) {
          return false;
        }
        for (const key in a) {
          if (!hasProperty(a, key) || !equalityDeep(a[key], b[key])) {
            return false;
          }
        }
        break;
      case Array:
        if (a.length !== b.length) {
          return false;
        }
        for (let i = 0; i < a.length; i++) {
          if (!equalityDeep(a[i], b[i])) {
            return false;
          }
        }
        break;
      default:
        return false;
    }
    return true;
  };
  var isOneOf = (value, options) => options.includes(value);

  // node_modules/lib0/environment.js
  var isNode = typeof process !== "undefined" && process.release && /node|io\.js/.test(process.release.name);
  var isBrowser = typeof window !== "undefined" && typeof document !== "undefined" && !isNode;
  var isMac = typeof navigator !== "undefined" ? /Mac/.test(navigator.platform) : false;
  var params;
  var args = [];
  var computeParams = () => {
    if (params === void 0) {
      if (isNode) {
        params = create();
        const pargs = process.argv;
        let currParamName = null;
        for (let i = 0; i < pargs.length; i++) {
          const parg = pargs[i];
          if (parg[0] === "-") {
            if (currParamName !== null) {
              params.set(currParamName, "");
            }
            currParamName = parg;
          } else {
            if (currParamName !== null) {
              params.set(currParamName, parg);
              currParamName = null;
            } else {
              args.push(parg);
            }
          }
        }
        if (currParamName !== null) {
          params.set(currParamName, "");
        }
      } else if (typeof location === "object") {
        params = create();
        (location.search || "?").slice(1).split("&").forEach((kv) => {
          if (kv.length !== 0) {
            const [key, value] = kv.split("=");
            params.set(`--${fromCamelCase(key, "-")}`, value);
            params.set(`-${fromCamelCase(key, "-")}`, value);
          }
        });
      } else {
        params = create();
      }
    }
    return params;
  };
  var hasParam = (name) => computeParams().has(name);
  var getVariable = (name) => isNode ? undefinedToNull(process.env[name.toUpperCase()]) : undefinedToNull(varStorage.getItem(name));
  var hasConf = (name) => hasParam("--" + name) || getVariable(name) !== null;
  var production = hasConf("production");
  var forceColor = isNode && isOneOf(process.env.FORCE_COLOR, ["true", "1", "2"]);
  var supportsColor = !hasParam("no-colors") && (!isNode || process.stdout.isTTY || forceColor) && (!isNode || hasParam("color") || forceColor || getVariable("COLORTERM") !== null || (getVariable("TERM") || "").includes("color"));

  // node_modules/lib0/binary.js
  var BIT1 = 1;
  var BIT2 = 2;
  var BIT3 = 4;
  var BIT4 = 8;
  var BIT6 = 32;
  var BIT7 = 64;
  var BIT8 = 128;
  var BIT18 = 1 << 17;
  var BIT19 = 1 << 18;
  var BIT20 = 1 << 19;
  var BIT21 = 1 << 20;
  var BIT22 = 1 << 21;
  var BIT23 = 1 << 22;
  var BIT24 = 1 << 23;
  var BIT25 = 1 << 24;
  var BIT26 = 1 << 25;
  var BIT27 = 1 << 26;
  var BIT28 = 1 << 27;
  var BIT29 = 1 << 28;
  var BIT30 = 1 << 29;
  var BIT31 = 1 << 30;
  var BIT32 = 1 << 31;
  var BITS5 = 31;
  var BITS6 = 63;
  var BITS7 = 127;
  var BITS17 = BIT18 - 1;
  var BITS18 = BIT19 - 1;
  var BITS19 = BIT20 - 1;
  var BITS20 = BIT21 - 1;
  var BITS21 = BIT22 - 1;
  var BITS22 = BIT23 - 1;
  var BITS23 = BIT24 - 1;
  var BITS24 = BIT25 - 1;
  var BITS25 = BIT26 - 1;
  var BITS26 = BIT27 - 1;
  var BITS27 = BIT28 - 1;
  var BITS28 = BIT29 - 1;
  var BITS29 = BIT30 - 1;
  var BITS30 = BIT31 - 1;
  var BITS31 = 2147483647;

  // node_modules/lib0/number.js
  var MAX_SAFE_INTEGER = Number.MAX_SAFE_INTEGER;
  var MIN_SAFE_INTEGER = Number.MIN_SAFE_INTEGER;
  var LOWEST_INT32 = 1 << 31;
  var isInteger = Number.isInteger || ((num) => typeof num === "number" && isFinite(num) && floor(num) === num);
  var isNaN3 = Number.isNaN;
  var parseInt2 = Number.parseInt;

  // node_modules/lib0/error.js
  var create3 = (s) => new Error(s);
  var methodUnimplemented = () => {
    throw create3("Method unimplemented");
  };
  var unexpectedCase = () => {
    throw create3("Unexpected case");
  };

  // node_modules/lib0/decoding.js
  var errorUnexpectedEndOfArray = create3("Unexpected end of array");
  var errorIntegerOutOfRange = create3("Integer out of Range");
  var Decoder = class {
    /**
     * @param {Uint8Array} uint8Array Binary data to decode
     */
    constructor(uint8Array) {
      this.arr = uint8Array;
      this.pos = 0;
    }
  };
  var createDecoder = (uint8Array) => new Decoder(uint8Array);
  var hasContent = (decoder) => decoder.pos !== decoder.arr.length;
  var readUint8Array = (decoder, len) => {
    const view = createUint8ArrayViewFromArrayBuffer(decoder.arr.buffer, decoder.pos + decoder.arr.byteOffset, len);
    decoder.pos += len;
    return view;
  };
  var readVarUint8Array = (decoder) => readUint8Array(decoder, readVarUint(decoder));
  var readUint8 = (decoder) => decoder.arr[decoder.pos++];
  var readVarUint = (decoder) => {
    let num = 0;
    let mult = 1;
    const len = decoder.arr.length;
    while (decoder.pos < len) {
      const r = decoder.arr[decoder.pos++];
      num = num + (r & BITS7) * mult;
      mult *= 128;
      if (r < BIT8) {
        return num;
      }
      if (num > MAX_SAFE_INTEGER) {
        throw errorIntegerOutOfRange;
      }
    }
    throw errorUnexpectedEndOfArray;
  };
  var readVarInt = (decoder) => {
    let r = decoder.arr[decoder.pos++];
    let num = r & BITS6;
    let mult = 64;
    const sign = (r & BIT7) > 0 ? -1 : 1;
    if ((r & BIT8) === 0) {
      return sign * num;
    }
    const len = decoder.arr.length;
    while (decoder.pos < len) {
      r = decoder.arr[decoder.pos++];
      num = num + (r & BITS7) * mult;
      mult *= 128;
      if (r < BIT8) {
        return sign * num;
      }
      if (num > MAX_SAFE_INTEGER) {
        throw errorIntegerOutOfRange;
      }
    }
    throw errorUnexpectedEndOfArray;
  };
  var _readVarStringPolyfill = (decoder) => {
    let remainingLen = readVarUint(decoder);
    if (remainingLen === 0) {
      return "";
    } else {
      let encodedString = String.fromCodePoint(readUint8(decoder));
      if (--remainingLen < 100) {
        while (remainingLen--) {
          encodedString += String.fromCodePoint(readUint8(decoder));
        }
      } else {
        while (remainingLen > 0) {
          const nextLen = remainingLen < 1e4 ? remainingLen : 1e4;
          const bytes = decoder.arr.subarray(decoder.pos, decoder.pos + nextLen);
          decoder.pos += nextLen;
          encodedString += String.fromCodePoint.apply(
            null,
            /** @type {any} */
            bytes
          );
          remainingLen -= nextLen;
        }
      }
      return decodeURIComponent(escape(encodedString));
    }
  };
  var _readVarStringNative = (decoder) => (
    /** @type any */
    utf8TextDecoder.decode(readVarUint8Array(decoder))
  );
  var readVarString = utf8TextDecoder ? _readVarStringNative : _readVarStringPolyfill;
  var readFromDataView = (decoder, len) => {
    const dv = new DataView(decoder.arr.buffer, decoder.arr.byteOffset + decoder.pos, len);
    decoder.pos += len;
    return dv;
  };
  var readFloat32 = (decoder) => readFromDataView(decoder, 4).getFloat32(0, false);
  var readFloat64 = (decoder) => readFromDataView(decoder, 8).getFloat64(0, false);
  var readBigInt64 = (decoder) => (
    /** @type {any} */
    readFromDataView(decoder, 8).getBigInt64(0, false)
  );
  var readAnyLookupTable = [
    (decoder) => void 0,
    // CASE 127: undefined
    (decoder) => null,
    // CASE 126: null
    readVarInt,
    // CASE 125: integer
    readFloat32,
    // CASE 124: float32
    readFloat64,
    // CASE 123: float64
    readBigInt64,
    // CASE 122: bigint
    (decoder) => false,
    // CASE 121: boolean (false)
    (decoder) => true,
    // CASE 120: boolean (true)
    readVarString,
    // CASE 119: string
    (decoder) => {
      const len = readVarUint(decoder);
      const obj = {};
      for (let i = 0; i < len; i++) {
        const key = readVarString(decoder);
        obj[key] = readAny(decoder);
      }
      return obj;
    },
    (decoder) => {
      const len = readVarUint(decoder);
      const arr = [];
      for (let i = 0; i < len; i++) {
        arr.push(readAny(decoder));
      }
      return arr;
    },
    readVarUint8Array
    // CASE 116: Uint8Array
  ];
  var readAny = (decoder) => readAnyLookupTable[127 - readUint8(decoder)](decoder);
  var RleDecoder = class extends Decoder {
    /**
     * @param {Uint8Array} uint8Array
     * @param {function(Decoder):T} reader
     */
    constructor(uint8Array, reader) {
      super(uint8Array);
      this.reader = reader;
      this.s = null;
      this.count = 0;
    }
    read() {
      if (this.count === 0) {
        this.s = this.reader(this);
        if (hasContent(this)) {
          this.count = readVarUint(this) + 1;
        } else {
          this.count = -1;
        }
      }
      this.count--;
      return (
        /** @type {T} */
        this.s
      );
    }
  };
  var UintOptRleDecoder = class extends Decoder {
    /**
     * @param {Uint8Array} uint8Array
     */
    constructor(uint8Array) {
      super(uint8Array);
      this.s = 0;
      this.count = 0;
    }
    read() {
      if (this.count === 0) {
        this.s = readVarInt(this);
        const isNegative = isNegativeZero(this.s);
        this.count = 1;
        if (isNegative) {
          this.s = -this.s;
          this.count = readVarUint(this) + 2;
        }
      }
      this.count--;
      return (
        /** @type {number} */
        this.s
      );
    }
  };
  var IntDiffOptRleDecoder = class extends Decoder {
    /**
     * @param {Uint8Array} uint8Array
     */
    constructor(uint8Array) {
      super(uint8Array);
      this.s = 0;
      this.count = 0;
      this.diff = 0;
    }
    /**
     * @return {number}
     */
    read() {
      if (this.count === 0) {
        const diff = readVarInt(this);
        const hasCount = diff & 1;
        this.diff = floor(diff / 2);
        this.count = 1;
        if (hasCount) {
          this.count = readVarUint(this) + 2;
        }
      }
      this.s += this.diff;
      this.count--;
      return this.s;
    }
  };
  var StringDecoder = class {
    /**
     * @param {Uint8Array} uint8Array
     */
    constructor(uint8Array) {
      this.decoder = new UintOptRleDecoder(uint8Array);
      this.str = readVarString(this.decoder);
      this.spos = 0;
    }
    /**
     * @return {string}
     */
    read() {
      const end = this.spos + this.decoder.read();
      const res = this.str.slice(this.spos, end);
      this.spos = end;
      return res;
    }
  };

  // node_modules/lib0/buffer.js
  var createUint8ArrayFromLen = (len) => new Uint8Array(len);
  var createUint8ArrayViewFromArrayBuffer = (buffer, byteOffset, length3) => new Uint8Array(buffer, byteOffset, length3);
  var createUint8ArrayFromArrayBuffer = (buffer) => new Uint8Array(buffer);
  var toBase64Browser = (bytes) => {
    let s = "";
    for (let i = 0; i < bytes.byteLength; i++) {
      s += fromCharCode(bytes[i]);
    }
    return btoa(s);
  };
  var toBase64Node = (bytes) => Buffer.from(bytes.buffer, bytes.byteOffset, bytes.byteLength).toString("base64");
  var fromBase64Browser = (s) => {
    const a = atob(s);
    const bytes = createUint8ArrayFromLen(a.length);
    for (let i = 0; i < a.length; i++) {
      bytes[i] = a.charCodeAt(i);
    }
    return bytes;
  };
  var fromBase64Node = (s) => {
    const buf = Buffer.from(s, "base64");
    return new Uint8Array(buf.buffer, buf.byteOffset, buf.byteLength);
  };
  var toBase64 = isBrowser ? toBase64Browser : toBase64Node;
  var fromBase64 = isBrowser ? fromBase64Browser : fromBase64Node;
  var copyUint8Array = (uint8Array) => {
    const newBuf = createUint8ArrayFromLen(uint8Array.byteLength);
    newBuf.set(uint8Array);
    return newBuf;
  };

  // node_modules/lib0/encoding.js
  var Encoder = class {
    constructor() {
      this.cpos = 0;
      this.cbuf = new Uint8Array(100);
      this.bufs = [];
    }
  };
  var createEncoder = () => new Encoder();
  var length2 = (encoder) => {
    let len = encoder.cpos;
    for (let i = 0; i < encoder.bufs.length; i++) {
      len += encoder.bufs[i].length;
    }
    return len;
  };
  var toUint8Array = (encoder) => {
    const uint8arr = new Uint8Array(length2(encoder));
    let curPos = 0;
    for (let i = 0; i < encoder.bufs.length; i++) {
      const d = encoder.bufs[i];
      uint8arr.set(d, curPos);
      curPos += d.length;
    }
    uint8arr.set(createUint8ArrayViewFromArrayBuffer(encoder.cbuf.buffer, 0, encoder.cpos), curPos);
    return uint8arr;
  };
  var verifyLen = (encoder, len) => {
    const bufferLen = encoder.cbuf.length;
    if (bufferLen - encoder.cpos < len) {
      encoder.bufs.push(createUint8ArrayViewFromArrayBuffer(encoder.cbuf.buffer, 0, encoder.cpos));
      encoder.cbuf = new Uint8Array(max(bufferLen, len) * 2);
      encoder.cpos = 0;
    }
  };
  var write = (encoder, num) => {
    const bufferLen = encoder.cbuf.length;
    if (encoder.cpos === bufferLen) {
      encoder.bufs.push(encoder.cbuf);
      encoder.cbuf = new Uint8Array(bufferLen * 2);
      encoder.cpos = 0;
    }
    encoder.cbuf[encoder.cpos++] = num;
  };
  var writeUint8 = write;
  var writeVarUint = (encoder, num) => {
    while (num > BITS7) {
      write(encoder, BIT8 | BITS7 & num);
      num = floor(num / 128);
    }
    write(encoder, BITS7 & num);
  };
  var writeVarInt = (encoder, num) => {
    const isNegative = isNegativeZero(num);
    if (isNegative) {
      num = -num;
    }
    write(encoder, (num > BITS6 ? BIT8 : 0) | (isNegative ? BIT7 : 0) | BITS6 & num);
    num = floor(num / 64);
    while (num > 0) {
      write(encoder, (num > BITS7 ? BIT8 : 0) | BITS7 & num);
      num = floor(num / 128);
    }
  };
  var _strBuffer = new Uint8Array(3e4);
  var _maxStrBSize = _strBuffer.length / 3;
  var _writeVarStringNative = (encoder, str) => {
    if (str.length < _maxStrBSize) {
      const written = utf8TextEncoder.encodeInto(str, _strBuffer).written || 0;
      writeVarUint(encoder, written);
      for (let i = 0; i < written; i++) {
        write(encoder, _strBuffer[i]);
      }
    } else {
      writeVarUint8Array(encoder, encodeUtf8(str));
    }
  };
  var _writeVarStringPolyfill = (encoder, str) => {
    const encodedString = unescape(encodeURIComponent(str));
    const len = encodedString.length;
    writeVarUint(encoder, len);
    for (let i = 0; i < len; i++) {
      write(
        encoder,
        /** @type {number} */
        encodedString.codePointAt(i)
      );
    }
  };
  var writeVarString = utf8TextEncoder && /** @type {any} */
  utf8TextEncoder.encodeInto ? _writeVarStringNative : _writeVarStringPolyfill;
  var writeBinaryEncoder = (encoder, append2) => writeUint8Array(encoder, toUint8Array(append2));
  var writeUint8Array = (encoder, uint8Array) => {
    const bufferLen = encoder.cbuf.length;
    const cpos = encoder.cpos;
    const leftCopyLen = min(bufferLen - cpos, uint8Array.length);
    const rightCopyLen = uint8Array.length - leftCopyLen;
    encoder.cbuf.set(uint8Array.subarray(0, leftCopyLen), cpos);
    encoder.cpos += leftCopyLen;
    if (rightCopyLen > 0) {
      encoder.bufs.push(encoder.cbuf);
      encoder.cbuf = new Uint8Array(max(bufferLen * 2, rightCopyLen));
      encoder.cbuf.set(uint8Array.subarray(leftCopyLen));
      encoder.cpos = rightCopyLen;
    }
  };
  var writeVarUint8Array = (encoder, uint8Array) => {
    writeVarUint(encoder, uint8Array.byteLength);
    writeUint8Array(encoder, uint8Array);
  };
  var writeOnDataView = (encoder, len) => {
    verifyLen(encoder, len);
    const dview = new DataView(encoder.cbuf.buffer, encoder.cpos, len);
    encoder.cpos += len;
    return dview;
  };
  var writeFloat32 = (encoder, num) => writeOnDataView(encoder, 4).setFloat32(0, num, false);
  var writeFloat64 = (encoder, num) => writeOnDataView(encoder, 8).setFloat64(0, num, false);
  var writeBigInt64 = (encoder, num) => (
    /** @type {any} */
    writeOnDataView(encoder, 8).setBigInt64(0, num, false)
  );
  var floatTestBed = new DataView(new ArrayBuffer(4));
  var isFloat32 = (num) => {
    floatTestBed.setFloat32(0, num);
    return floatTestBed.getFloat32(0) === num;
  };
  var writeAny = (encoder, data) => {
    switch (typeof data) {
      case "string":
        write(encoder, 119);
        writeVarString(encoder, data);
        break;
      case "number":
        if (isInteger(data) && abs(data) <= BITS31) {
          write(encoder, 125);
          writeVarInt(encoder, data);
        } else if (isFloat32(data)) {
          write(encoder, 124);
          writeFloat32(encoder, data);
        } else {
          write(encoder, 123);
          writeFloat64(encoder, data);
        }
        break;
      case "bigint":
        write(encoder, 122);
        writeBigInt64(encoder, data);
        break;
      case "object":
        if (data === null) {
          write(encoder, 126);
        } else if (isArray(data)) {
          write(encoder, 117);
          writeVarUint(encoder, data.length);
          for (let i = 0; i < data.length; i++) {
            writeAny(encoder, data[i]);
          }
        } else if (data instanceof Uint8Array) {
          write(encoder, 116);
          writeVarUint8Array(encoder, data);
        } else {
          write(encoder, 118);
          const keys2 = Object.keys(data);
          writeVarUint(encoder, keys2.length);
          for (let i = 0; i < keys2.length; i++) {
            const key = keys2[i];
            writeVarString(encoder, key);
            writeAny(encoder, data[key]);
          }
        }
        break;
      case "boolean":
        write(encoder, data ? 120 : 121);
        break;
      default:
        write(encoder, 127);
    }
  };
  var RleEncoder = class extends Encoder {
    /**
     * @param {function(Encoder, T):void} writer
     */
    constructor(writer) {
      super();
      this.w = writer;
      this.s = null;
      this.count = 0;
    }
    /**
     * @param {T} v
     */
    write(v) {
      if (this.s === v) {
        this.count++;
      } else {
        if (this.count > 0) {
          writeVarUint(this, this.count - 1);
        }
        this.count = 1;
        this.w(this, v);
        this.s = v;
      }
    }
  };
  var flushUintOptRleEncoder = (encoder) => {
    if (encoder.count > 0) {
      writeVarInt(encoder.encoder, encoder.count === 1 ? encoder.s : -encoder.s);
      if (encoder.count > 1) {
        writeVarUint(encoder.encoder, encoder.count - 2);
      }
    }
  };
  var UintOptRleEncoder = class {
    constructor() {
      this.encoder = new Encoder();
      this.s = 0;
      this.count = 0;
    }
    /**
     * @param {number} v
     */
    write(v) {
      if (this.s === v) {
        this.count++;
      } else {
        flushUintOptRleEncoder(this);
        this.count = 1;
        this.s = v;
      }
    }
    toUint8Array() {
      flushUintOptRleEncoder(this);
      return toUint8Array(this.encoder);
    }
  };
  var flushIntDiffOptRleEncoder = (encoder) => {
    if (encoder.count > 0) {
      const encodedDiff = encoder.diff * 2 + (encoder.count === 1 ? 0 : 1);
      writeVarInt(encoder.encoder, encodedDiff);
      if (encoder.count > 1) {
        writeVarUint(encoder.encoder, encoder.count - 2);
      }
    }
  };
  var IntDiffOptRleEncoder = class {
    constructor() {
      this.encoder = new Encoder();
      this.s = 0;
      this.count = 0;
      this.diff = 0;
    }
    /**
     * @param {number} v
     */
    write(v) {
      if (this.diff === v - this.s) {
        this.s = v;
        this.count++;
      } else {
        flushIntDiffOptRleEncoder(this);
        this.count = 1;
        this.diff = v - this.s;
        this.s = v;
      }
    }
    toUint8Array() {
      flushIntDiffOptRleEncoder(this);
      return toUint8Array(this.encoder);
    }
  };
  var StringEncoder = class {
    constructor() {
      this.sarr = [];
      this.s = "";
      this.lensE = new UintOptRleEncoder();
    }
    /**
     * @param {string} string
     */
    write(string) {
      this.s += string;
      if (this.s.length > 19) {
        this.sarr.push(this.s);
        this.s = "";
      }
      this.lensE.write(string.length);
    }
    toUint8Array() {
      const encoder = new Encoder();
      this.sarr.push(this.s);
      this.s = "";
      writeVarString(encoder, this.sarr.join(""));
      writeUint8Array(encoder, this.lensE.toUint8Array());
      return toUint8Array(encoder);
    }
  };

  // node_modules/lib0/webcrypto.js
  var subtle = crypto.subtle;
  var getRandomValues = crypto.getRandomValues.bind(crypto);

  // node_modules/lib0/random.js
  var rand = Math.random;
  var uint32 = () => getRandomValues(new Uint32Array(1))[0];
  var uuidv4Template = "10000000-1000-4000-8000" + -1e11;
  var uuidv4 = () => uuidv4Template.replace(
    /[018]/g,
    /** @param {number} c */
    (c) => (c ^ uint32() & 15 >> c / 4).toString(16)
  );

  // node_modules/lib0/time.js
  var getUnixTime = Date.now;

  // node_modules/lib0/promise.js
  var create4 = (f) => (
    /** @type {Promise<T>} */
    new Promise(f)
  );
  var all = Promise.all.bind(Promise);
  var reject = (reason) => Promise.reject(reason);
  var resolve = (res) => Promise.resolve(res);

  // node_modules/lib0/pair.js
  var Pair = class {
    /**
     * @param {L} left
     * @param {R} right
     */
    constructor(left, right) {
      this.left = left;
      this.right = right;
    }
  };
  var create5 = (left, right) => new Pair(left, right);

  // node_modules/lib0/dom.js
  var doc = (
    /** @type {Document} */
    typeof document !== "undefined" ? document : {}
  );
  var domParser = (
    /** @type {DOMParser} */
    typeof DOMParser !== "undefined" ? new DOMParser() : null
  );
  var mapToStyleString = (m) => map(m, (value, key) => `${key}:${value};`).join("");
  var ELEMENT_NODE = doc.ELEMENT_NODE;
  var TEXT_NODE = doc.TEXT_NODE;
  var CDATA_SECTION_NODE = doc.CDATA_SECTION_NODE;
  var COMMENT_NODE = doc.COMMENT_NODE;
  var DOCUMENT_NODE = doc.DOCUMENT_NODE;
  var DOCUMENT_TYPE_NODE = doc.DOCUMENT_TYPE_NODE;
  var DOCUMENT_FRAGMENT_NODE = doc.DOCUMENT_FRAGMENT_NODE;

  // node_modules/lib0/symbol.js
  var create6 = Symbol;

  // node_modules/lib0/logging.common.js
  var BOLD = create6();
  var UNBOLD = create6();
  var BLUE = create6();
  var GREY = create6();
  var GREEN = create6();
  var RED = create6();
  var PURPLE = create6();
  var ORANGE = create6();
  var UNCOLOR = create6();
  var computeNoColorLoggingArgs = (args2) => {
    const strBuilder = [];
    const logArgs = [];
    let i = 0;
    for (; i < args2.length; i++) {
      const arg = args2[i];
      if (arg.constructor === String || arg.constructor === Number) {
        strBuilder.push(arg);
      } else if (arg.constructor === Object) {
        logArgs.push(JSON.stringify(arg));
      }
    }
    return logArgs;
  };
  var loggingColors = [GREEN, PURPLE, ORANGE, BLUE];
  var nextColor = 0;
  var lastLoggingTime = getUnixTime();
  var createModuleLogger = (_print, moduleName) => {
    const color = loggingColors[nextColor];
    const debugRegexVar = getVariable("log");
    const doLogging = debugRegexVar !== null && (debugRegexVar === "*" || debugRegexVar === "true" || new RegExp(debugRegexVar, "gi").test(moduleName));
    nextColor = (nextColor + 1) % loggingColors.length;
    moduleName += ": ";
    return !doLogging ? nop : (...args2) => {
      const timeNow = getUnixTime();
      const timeDiff = timeNow - lastLoggingTime;
      lastLoggingTime = timeNow;
      _print(
        color,
        moduleName,
        UNCOLOR,
        ...args2.map(
          (arg) => typeof arg === "string" || typeof arg === "symbol" ? arg : JSON.stringify(arg)
        ),
        color,
        " +" + timeDiff + "ms"
      );
    };
  };

  // node_modules/lib0/logging.js
  var _browserStyleMap = {
    [BOLD]: create5("font-weight", "bold"),
    [UNBOLD]: create5("font-weight", "normal"),
    [BLUE]: create5("color", "blue"),
    [GREEN]: create5("color", "green"),
    [GREY]: create5("color", "grey"),
    [RED]: create5("color", "red"),
    [PURPLE]: create5("color", "purple"),
    [ORANGE]: create5("color", "orange"),
    // not well supported in chrome when debugging node with inspector - TODO: deprecate
    [UNCOLOR]: create5("color", "black")
  };
  var computeBrowserLoggingArgs = (args2) => {
    const strBuilder = [];
    const styles = [];
    const currentStyle = create();
    let logArgs = [];
    let i = 0;
    for (; i < args2.length; i++) {
      const arg = args2[i];
      const style = _browserStyleMap[arg];
      if (style !== void 0) {
        currentStyle.set(style.left, style.right);
      } else {
        if (arg.constructor === String || arg.constructor === Number) {
          const style2 = mapToStyleString(currentStyle);
          if (i > 0 || style2.length > 0) {
            strBuilder.push("%c" + arg);
            styles.push(style2);
          } else {
            strBuilder.push(arg);
          }
        } else {
          break;
        }
      }
    }
    if (i > 0) {
      logArgs = styles;
      logArgs.unshift(strBuilder.join(""));
    }
    for (; i < args2.length; i++) {
      const arg = args2[i];
      if (!(arg instanceof Symbol)) {
        logArgs.push(arg);
      }
    }
    return logArgs;
  };
  var computeLoggingArgs = supportsColor ? computeBrowserLoggingArgs : computeNoColorLoggingArgs;
  var print = (...args2) => {
    console.log(...computeLoggingArgs(args2));
    vconsoles.forEach((vc) => vc.print(args2));
  };
  var vconsoles = create2();
  var createModuleLogger2 = (moduleName) => createModuleLogger(print, moduleName);

  // node_modules/lib0/iterator.js
  var createIterator = (next) => ({
    /**
     * @return {IterableIterator<T>}
     */
    [Symbol.iterator]() {
      return this;
    },
    // @ts-ignore
    next
  });
  var iteratorFilter = (iterator, filter) => createIterator(() => {
    let res;
    do {
      res = iterator.next();
    } while (!res.done && !filter(res.value));
    return res;
  });
  var iteratorMap = (iterator, fmap) => createIterator(() => {
    const { done, value } = iterator.next();
    return { done, value: done ? void 0 : fmap(value) };
  });

  // node_modules/yjs/dist/yjs.mjs
  var AbstractConnector = class extends Observable {
    /**
     * @param {Doc} ydoc
     * @param {any} awareness
     */
    constructor(ydoc, awareness) {
      super();
      this.doc = ydoc;
      this.awareness = awareness;
    }
  };
  var DeleteItem = class {
    /**
     * @param {number} clock
     * @param {number} len
     */
    constructor(clock, len) {
      this.clock = clock;
      this.len = len;
    }
  };
  var DeleteSet = class {
    constructor() {
      this.clients = /* @__PURE__ */ new Map();
    }
  };
  var iterateDeletedStructs = (transaction, ds, f) => ds.clients.forEach((deletes, clientid) => {
    const structs = (
      /** @type {Array<GC|Item>} */
      transaction.doc.store.clients.get(clientid)
    );
    for (let i = 0; i < deletes.length; i++) {
      const del2 = deletes[i];
      iterateStructs(transaction, structs, del2.clock, del2.len, f);
    }
  });
  var findIndexDS = (dis, clock) => {
    let left = 0;
    let right = dis.length - 1;
    while (left <= right) {
      const midindex = floor((left + right) / 2);
      const mid = dis[midindex];
      const midclock = mid.clock;
      if (midclock <= clock) {
        if (clock < midclock + mid.len) {
          return midindex;
        }
        left = midindex + 1;
      } else {
        right = midindex - 1;
      }
    }
    return null;
  };
  var isDeleted = (ds, id2) => {
    const dis = ds.clients.get(id2.client);
    return dis !== void 0 && findIndexDS(dis, id2.clock) !== null;
  };
  var sortAndMergeDeleteSet = (ds) => {
    ds.clients.forEach((dels) => {
      dels.sort((a, b) => a.clock - b.clock);
      let i, j;
      for (i = 1, j = 1; i < dels.length; i++) {
        const left = dels[j - 1];
        const right = dels[i];
        if (left.clock + left.len >= right.clock) {
          left.len = max(left.len, right.clock + right.len - left.clock);
        } else {
          if (j < i) {
            dels[j] = right;
          }
          j++;
        }
      }
      dels.length = j;
    });
  };
  var mergeDeleteSets = (dss) => {
    const merged = new DeleteSet();
    for (let dssI = 0; dssI < dss.length; dssI++) {
      dss[dssI].clients.forEach((delsLeft, client) => {
        if (!merged.clients.has(client)) {
          const dels = delsLeft.slice();
          for (let i = dssI + 1; i < dss.length; i++) {
            appendTo(dels, dss[i].clients.get(client) || []);
          }
          merged.clients.set(client, dels);
        }
      });
    }
    sortAndMergeDeleteSet(merged);
    return merged;
  };
  var addToDeleteSet = (ds, client, clock, length3) => {
    setIfUndefined(ds.clients, client, () => (
      /** @type {Array<DeleteItem>} */
      []
    )).push(new DeleteItem(clock, length3));
  };
  var createDeleteSet = () => new DeleteSet();
  var createDeleteSetFromStructStore = (ss) => {
    const ds = createDeleteSet();
    ss.clients.forEach((structs, client) => {
      const dsitems = [];
      for (let i = 0; i < structs.length; i++) {
        const struct = structs[i];
        if (struct.deleted) {
          const clock = struct.id.clock;
          let len = struct.length;
          if (i + 1 < structs.length) {
            for (let next = structs[i + 1]; i + 1 < structs.length && next.deleted; next = structs[++i + 1]) {
              len += next.length;
            }
          }
          dsitems.push(new DeleteItem(clock, len));
        }
      }
      if (dsitems.length > 0) {
        ds.clients.set(client, dsitems);
      }
    });
    return ds;
  };
  var writeDeleteSet = (encoder, ds) => {
    writeVarUint(encoder.restEncoder, ds.clients.size);
    from(ds.clients.entries()).sort((a, b) => b[0] - a[0]).forEach(([client, dsitems]) => {
      encoder.resetDsCurVal();
      writeVarUint(encoder.restEncoder, client);
      const len = dsitems.length;
      writeVarUint(encoder.restEncoder, len);
      for (let i = 0; i < len; i++) {
        const item = dsitems[i];
        encoder.writeDsClock(item.clock);
        encoder.writeDsLen(item.len);
      }
    });
  };
  var readDeleteSet = (decoder) => {
    const ds = new DeleteSet();
    const numClients = readVarUint(decoder.restDecoder);
    for (let i = 0; i < numClients; i++) {
      decoder.resetDsCurVal();
      const client = readVarUint(decoder.restDecoder);
      const numberOfDeletes = readVarUint(decoder.restDecoder);
      if (numberOfDeletes > 0) {
        const dsField = setIfUndefined(ds.clients, client, () => (
          /** @type {Array<DeleteItem>} */
          []
        ));
        for (let i2 = 0; i2 < numberOfDeletes; i2++) {
          dsField.push(new DeleteItem(decoder.readDsClock(), decoder.readDsLen()));
        }
      }
    }
    return ds;
  };
  var readAndApplyDeleteSet = (decoder, transaction, store2) => {
    const unappliedDS = new DeleteSet();
    const numClients = readVarUint(decoder.restDecoder);
    for (let i = 0; i < numClients; i++) {
      decoder.resetDsCurVal();
      const client = readVarUint(decoder.restDecoder);
      const numberOfDeletes = readVarUint(decoder.restDecoder);
      const structs = store2.clients.get(client) || [];
      const state = getState(store2, client);
      for (let i2 = 0; i2 < numberOfDeletes; i2++) {
        const clock = decoder.readDsClock();
        const clockEnd = clock + decoder.readDsLen();
        if (clock < state) {
          if (state < clockEnd) {
            addToDeleteSet(unappliedDS, client, state, clockEnd - state);
          }
          let index = findIndexSS(structs, clock);
          let struct = structs[index];
          if (!struct.deleted && struct.id.clock < clock) {
            structs.splice(index + 1, 0, splitItem(transaction, struct, clock - struct.id.clock));
            index++;
          }
          while (index < structs.length) {
            struct = structs[index++];
            if (struct.id.clock < clockEnd) {
              if (!struct.deleted) {
                if (clockEnd < struct.id.clock + struct.length) {
                  structs.splice(index, 0, splitItem(transaction, struct, clockEnd - struct.id.clock));
                }
                struct.delete(transaction);
              }
            } else {
              break;
            }
          }
        } else {
          addToDeleteSet(unappliedDS, client, clock, clockEnd - clock);
        }
      }
    }
    if (unappliedDS.clients.size > 0) {
      const ds = new UpdateEncoderV2();
      writeVarUint(ds.restEncoder, 0);
      writeDeleteSet(ds, unappliedDS);
      return ds.toUint8Array();
    }
    return null;
  };
  var equalDeleteSets = (ds1, ds2) => {
    if (ds1.clients.size !== ds2.clients.size) return false;
    for (const [client, deleteItems1] of ds1.clients.entries()) {
      const deleteItems2 = (
        /** @type {Array<import('../internals.js').DeleteItem>} */
        ds2.clients.get(client)
      );
      if (deleteItems2 === void 0 || deleteItems1.length !== deleteItems2.length) return false;
      for (let i = 0; i < deleteItems1.length; i++) {
        const di1 = deleteItems1[i];
        const di2 = deleteItems2[i];
        if (di1.clock !== di2.clock || di1.len !== di2.len) {
          return false;
        }
      }
    }
    return true;
  };
  var generateNewClientId = uint32;
  var Doc = class _Doc extends Observable {
    /**
     * @param {DocOpts} opts configuration
     */
    constructor({ guid = uuidv4(), collectionid = null, gc = true, gcFilter = () => true, meta = null, autoLoad = false, shouldLoad = true } = {}) {
      super();
      this.gc = gc;
      this.gcFilter = gcFilter;
      this.clientID = generateNewClientId();
      this.guid = guid;
      this.collectionid = collectionid;
      this.share = /* @__PURE__ */ new Map();
      this.store = new StructStore();
      this._transaction = null;
      this._transactionCleanups = [];
      this.subdocs = /* @__PURE__ */ new Set();
      this._item = null;
      this.shouldLoad = shouldLoad;
      this.autoLoad = autoLoad;
      this.meta = meta;
      this.isLoaded = false;
      this.isSynced = false;
      this.whenLoaded = create4((resolve2) => {
        this.on("load", () => {
          this.isLoaded = true;
          resolve2(this);
        });
      });
      const provideSyncedPromise = () => create4((resolve2) => {
        const eventHandler = (isSynced) => {
          if (isSynced === void 0 || isSynced === true) {
            this.off("sync", eventHandler);
            resolve2();
          }
        };
        this.on("sync", eventHandler);
      });
      this.on("sync", (isSynced) => {
        if (isSynced === false && this.isSynced) {
          this.whenSynced = provideSyncedPromise();
        }
        this.isSynced = isSynced === void 0 || isSynced === true;
        if (!this.isLoaded) {
          this.emit("load", []);
        }
      });
      this.whenSynced = provideSyncedPromise();
    }
    /**
     * Notify the parent document that you request to load data into this subdocument (if it is a subdocument).
     *
     * `load()` might be used in the future to request any provider to load the most current data.
     *
     * It is safe to call `load()` multiple times.
     */
    load() {
      const item = this._item;
      if (item !== null && !this.shouldLoad) {
        transact(
          /** @type {any} */
          item.parent.doc,
          (transaction) => {
            transaction.subdocsLoaded.add(this);
          },
          null,
          true
        );
      }
      this.shouldLoad = true;
    }
    getSubdocs() {
      return this.subdocs;
    }
    getSubdocGuids() {
      return new Set(from(this.subdocs).map((doc2) => doc2.guid));
    }
    /**
     * Changes that happen inside of a transaction are bundled. This means that
     * the observer fires _after_ the transaction is finished and that all changes
     * that happened inside of the transaction are sent as one message to the
     * other peers.
     *
     * @template T
     * @param {function(Transaction):T} f The function that should be executed as a transaction
     * @param {any} [origin] Origin of who started the transaction. Will be stored on transaction.origin
     * @return T
     *
     * @public
     */
    transact(f, origin = null) {
      return transact(this, f, origin);
    }
    /**
     * Define a shared data type.
     *
     * Multiple calls of `y.get(name, TypeConstructor)` yield the same result
     * and do not overwrite each other. I.e.
     * `y.define(name, Y.Array) === y.define(name, Y.Array)`
     *
     * After this method is called, the type is also available on `y.share.get(name)`.
     *
     * *Best Practices:*
     * Define all types right after the Yjs instance is created and store them in a separate object.
     * Also use the typed methods `getText(name)`, `getArray(name)`, ..
     *
     * @example
     *   const y = new Y(..)
     *   const appState = {
     *     document: y.getText('document')
     *     comments: y.getArray('comments')
     *   }
     *
     * @param {string} name
     * @param {Function} TypeConstructor The constructor of the type definition. E.g. Y.Text, Y.Array, Y.Map, ...
     * @return {AbstractType<any>} The created type. Constructed with TypeConstructor
     *
     * @public
     */
    get(name, TypeConstructor = AbstractType) {
      const type = setIfUndefined(this.share, name, () => {
        const t = new TypeConstructor();
        t._integrate(this, null);
        return t;
      });
      const Constr = type.constructor;
      if (TypeConstructor !== AbstractType && Constr !== TypeConstructor) {
        if (Constr === AbstractType) {
          const t = new TypeConstructor();
          t._map = type._map;
          type._map.forEach(
            /** @param {Item?} n */
            (n) => {
              for (; n !== null; n = n.left) {
                n.parent = t;
              }
            }
          );
          t._start = type._start;
          for (let n = t._start; n !== null; n = n.right) {
            n.parent = t;
          }
          t._length = type._length;
          this.share.set(name, t);
          t._integrate(this, null);
          return t;
        } else {
          throw new Error(`Type with the name ${name} has already been defined with a different constructor`);
        }
      }
      return type;
    }
    /**
     * @template T
     * @param {string} [name]
     * @return {YArray<T>}
     *
     * @public
     */
    getArray(name = "") {
      return this.get(name, YArray);
    }
    /**
     * @param {string} [name]
     * @return {YText}
     *
     * @public
     */
    getText(name = "") {
      return this.get(name, YText);
    }
    /**
     * @template T
     * @param {string} [name]
     * @return {YMap<T>}
     *
     * @public
     */
    getMap(name = "") {
      return this.get(name, YMap);
    }
    /**
     * @param {string} [name]
     * @return {YXmlFragment}
     *
     * @public
     */
    getXmlFragment(name = "") {
      return this.get(name, YXmlFragment);
    }
    /**
     * Converts the entire document into a js object, recursively traversing each yjs type
     * Doesn't log types that have not been defined (using ydoc.getType(..)).
     *
     * @deprecated Do not use this method and rather call toJSON directly on the shared types.
     *
     * @return {Object<string, any>}
     */
    toJSON() {
      const doc2 = {};
      this.share.forEach((value, key) => {
        doc2[key] = value.toJSON();
      });
      return doc2;
    }
    /**
     * Emit `destroy` event and unregister all event handlers.
     */
    destroy() {
      from(this.subdocs).forEach((subdoc) => subdoc.destroy());
      const item = this._item;
      if (item !== null) {
        this._item = null;
        const content = (
          /** @type {ContentDoc} */
          item.content
        );
        content.doc = new _Doc({ guid: this.guid, ...content.opts, shouldLoad: false });
        content.doc._item = item;
        transact(
          /** @type {any} */
          item.parent.doc,
          (transaction) => {
            const doc2 = content.doc;
            if (!item.deleted) {
              transaction.subdocsAdded.add(doc2);
            }
            transaction.subdocsRemoved.add(this);
          },
          null,
          true
        );
      }
      this.emit("destroyed", [true]);
      this.emit("destroy", [this]);
      super.destroy();
    }
    /**
     * @param {string} eventName
     * @param {function(...any):any} f
     */
    on(eventName, f) {
      super.on(eventName, f);
    }
    /**
     * @param {string} eventName
     * @param {function} f
     */
    off(eventName, f) {
      super.off(eventName, f);
    }
  };
  var DSDecoderV1 = class {
    /**
     * @param {decoding.Decoder} decoder
     */
    constructor(decoder) {
      this.restDecoder = decoder;
    }
    resetDsCurVal() {
    }
    /**
     * @return {number}
     */
    readDsClock() {
      return readVarUint(this.restDecoder);
    }
    /**
     * @return {number}
     */
    readDsLen() {
      return readVarUint(this.restDecoder);
    }
  };
  var UpdateDecoderV1 = class extends DSDecoderV1 {
    /**
     * @return {ID}
     */
    readLeftID() {
      return createID(readVarUint(this.restDecoder), readVarUint(this.restDecoder));
    }
    /**
     * @return {ID}
     */
    readRightID() {
      return createID(readVarUint(this.restDecoder), readVarUint(this.restDecoder));
    }
    /**
     * Read the next client id.
     * Use this in favor of readID whenever possible to reduce the number of objects created.
     */
    readClient() {
      return readVarUint(this.restDecoder);
    }
    /**
     * @return {number} info An unsigned 8-bit integer
     */
    readInfo() {
      return readUint8(this.restDecoder);
    }
    /**
     * @return {string}
     */
    readString() {
      return readVarString(this.restDecoder);
    }
    /**
     * @return {boolean} isKey
     */
    readParentInfo() {
      return readVarUint(this.restDecoder) === 1;
    }
    /**
     * @return {number} info An unsigned 8-bit integer
     */
    readTypeRef() {
      return readVarUint(this.restDecoder);
    }
    /**
     * Write len of a struct - well suited for Opt RLE encoder.
     *
     * @return {number} len
     */
    readLen() {
      return readVarUint(this.restDecoder);
    }
    /**
     * @return {any}
     */
    readAny() {
      return readAny(this.restDecoder);
    }
    /**
     * @return {Uint8Array}
     */
    readBuf() {
      return copyUint8Array(readVarUint8Array(this.restDecoder));
    }
    /**
     * Legacy implementation uses JSON parse. We use any-decoding in v2.
     *
     * @return {any}
     */
    readJSON() {
      return JSON.parse(readVarString(this.restDecoder));
    }
    /**
     * @return {string}
     */
    readKey() {
      return readVarString(this.restDecoder);
    }
  };
  var DSDecoderV2 = class {
    /**
     * @param {decoding.Decoder} decoder
     */
    constructor(decoder) {
      this.dsCurrVal = 0;
      this.restDecoder = decoder;
    }
    resetDsCurVal() {
      this.dsCurrVal = 0;
    }
    /**
     * @return {number}
     */
    readDsClock() {
      this.dsCurrVal += readVarUint(this.restDecoder);
      return this.dsCurrVal;
    }
    /**
     * @return {number}
     */
    readDsLen() {
      const diff = readVarUint(this.restDecoder) + 1;
      this.dsCurrVal += diff;
      return diff;
    }
  };
  var UpdateDecoderV2 = class extends DSDecoderV2 {
    /**
     * @param {decoding.Decoder} decoder
     */
    constructor(decoder) {
      super(decoder);
      this.keys = [];
      readVarUint(decoder);
      this.keyClockDecoder = new IntDiffOptRleDecoder(readVarUint8Array(decoder));
      this.clientDecoder = new UintOptRleDecoder(readVarUint8Array(decoder));
      this.leftClockDecoder = new IntDiffOptRleDecoder(readVarUint8Array(decoder));
      this.rightClockDecoder = new IntDiffOptRleDecoder(readVarUint8Array(decoder));
      this.infoDecoder = new RleDecoder(readVarUint8Array(decoder), readUint8);
      this.stringDecoder = new StringDecoder(readVarUint8Array(decoder));
      this.parentInfoDecoder = new RleDecoder(readVarUint8Array(decoder), readUint8);
      this.typeRefDecoder = new UintOptRleDecoder(readVarUint8Array(decoder));
      this.lenDecoder = new UintOptRleDecoder(readVarUint8Array(decoder));
    }
    /**
     * @return {ID}
     */
    readLeftID() {
      return new ID(this.clientDecoder.read(), this.leftClockDecoder.read());
    }
    /**
     * @return {ID}
     */
    readRightID() {
      return new ID(this.clientDecoder.read(), this.rightClockDecoder.read());
    }
    /**
     * Read the next client id.
     * Use this in favor of readID whenever possible to reduce the number of objects created.
     */
    readClient() {
      return this.clientDecoder.read();
    }
    /**
     * @return {number} info An unsigned 8-bit integer
     */
    readInfo() {
      return (
        /** @type {number} */
        this.infoDecoder.read()
      );
    }
    /**
     * @return {string}
     */
    readString() {
      return this.stringDecoder.read();
    }
    /**
     * @return {boolean}
     */
    readParentInfo() {
      return this.parentInfoDecoder.read() === 1;
    }
    /**
     * @return {number} An unsigned 8-bit integer
     */
    readTypeRef() {
      return this.typeRefDecoder.read();
    }
    /**
     * Write len of a struct - well suited for Opt RLE encoder.
     *
     * @return {number}
     */
    readLen() {
      return this.lenDecoder.read();
    }
    /**
     * @return {any}
     */
    readAny() {
      return readAny(this.restDecoder);
    }
    /**
     * @return {Uint8Array}
     */
    readBuf() {
      return readVarUint8Array(this.restDecoder);
    }
    /**
     * This is mainly here for legacy purposes.
     *
     * Initial we incoded objects using JSON. Now we use the much faster lib0/any-encoder. This method mainly exists for legacy purposes for the v1 encoder.
     *
     * @return {any}
     */
    readJSON() {
      return readAny(this.restDecoder);
    }
    /**
     * @return {string}
     */
    readKey() {
      const keyClock = this.keyClockDecoder.read();
      if (keyClock < this.keys.length) {
        return this.keys[keyClock];
      } else {
        const key = this.stringDecoder.read();
        this.keys.push(key);
        return key;
      }
    }
  };
  var DSEncoderV1 = class {
    constructor() {
      this.restEncoder = createEncoder();
    }
    toUint8Array() {
      return toUint8Array(this.restEncoder);
    }
    resetDsCurVal() {
    }
    /**
     * @param {number} clock
     */
    writeDsClock(clock) {
      writeVarUint(this.restEncoder, clock);
    }
    /**
     * @param {number} len
     */
    writeDsLen(len) {
      writeVarUint(this.restEncoder, len);
    }
  };
  var UpdateEncoderV1 = class extends DSEncoderV1 {
    /**
     * @param {ID} id
     */
    writeLeftID(id2) {
      writeVarUint(this.restEncoder, id2.client);
      writeVarUint(this.restEncoder, id2.clock);
    }
    /**
     * @param {ID} id
     */
    writeRightID(id2) {
      writeVarUint(this.restEncoder, id2.client);
      writeVarUint(this.restEncoder, id2.clock);
    }
    /**
     * Use writeClient and writeClock instead of writeID if possible.
     * @param {number} client
     */
    writeClient(client) {
      writeVarUint(this.restEncoder, client);
    }
    /**
     * @param {number} info An unsigned 8-bit integer
     */
    writeInfo(info) {
      writeUint8(this.restEncoder, info);
    }
    /**
     * @param {string} s
     */
    writeString(s) {
      writeVarString(this.restEncoder, s);
    }
    /**
     * @param {boolean} isYKey
     */
    writeParentInfo(isYKey) {
      writeVarUint(this.restEncoder, isYKey ? 1 : 0);
    }
    /**
     * @param {number} info An unsigned 8-bit integer
     */
    writeTypeRef(info) {
      writeVarUint(this.restEncoder, info);
    }
    /**
     * Write len of a struct - well suited for Opt RLE encoder.
     *
     * @param {number} len
     */
    writeLen(len) {
      writeVarUint(this.restEncoder, len);
    }
    /**
     * @param {any} any
     */
    writeAny(any2) {
      writeAny(this.restEncoder, any2);
    }
    /**
     * @param {Uint8Array} buf
     */
    writeBuf(buf) {
      writeVarUint8Array(this.restEncoder, buf);
    }
    /**
     * @param {any} embed
     */
    writeJSON(embed) {
      writeVarString(this.restEncoder, JSON.stringify(embed));
    }
    /**
     * @param {string} key
     */
    writeKey(key) {
      writeVarString(this.restEncoder, key);
    }
  };
  var DSEncoderV2 = class {
    constructor() {
      this.restEncoder = createEncoder();
      this.dsCurrVal = 0;
    }
    toUint8Array() {
      return toUint8Array(this.restEncoder);
    }
    resetDsCurVal() {
      this.dsCurrVal = 0;
    }
    /**
     * @param {number} clock
     */
    writeDsClock(clock) {
      const diff = clock - this.dsCurrVal;
      this.dsCurrVal = clock;
      writeVarUint(this.restEncoder, diff);
    }
    /**
     * @param {number} len
     */
    writeDsLen(len) {
      if (len === 0) {
        unexpectedCase();
      }
      writeVarUint(this.restEncoder, len - 1);
      this.dsCurrVal += len;
    }
  };
  var UpdateEncoderV2 = class extends DSEncoderV2 {
    constructor() {
      super();
      this.keyMap = /* @__PURE__ */ new Map();
      this.keyClock = 0;
      this.keyClockEncoder = new IntDiffOptRleEncoder();
      this.clientEncoder = new UintOptRleEncoder();
      this.leftClockEncoder = new IntDiffOptRleEncoder();
      this.rightClockEncoder = new IntDiffOptRleEncoder();
      this.infoEncoder = new RleEncoder(writeUint8);
      this.stringEncoder = new StringEncoder();
      this.parentInfoEncoder = new RleEncoder(writeUint8);
      this.typeRefEncoder = new UintOptRleEncoder();
      this.lenEncoder = new UintOptRleEncoder();
    }
    toUint8Array() {
      const encoder = createEncoder();
      writeVarUint(encoder, 0);
      writeVarUint8Array(encoder, this.keyClockEncoder.toUint8Array());
      writeVarUint8Array(encoder, this.clientEncoder.toUint8Array());
      writeVarUint8Array(encoder, this.leftClockEncoder.toUint8Array());
      writeVarUint8Array(encoder, this.rightClockEncoder.toUint8Array());
      writeVarUint8Array(encoder, toUint8Array(this.infoEncoder));
      writeVarUint8Array(encoder, this.stringEncoder.toUint8Array());
      writeVarUint8Array(encoder, toUint8Array(this.parentInfoEncoder));
      writeVarUint8Array(encoder, this.typeRefEncoder.toUint8Array());
      writeVarUint8Array(encoder, this.lenEncoder.toUint8Array());
      writeUint8Array(encoder, toUint8Array(this.restEncoder));
      return toUint8Array(encoder);
    }
    /**
     * @param {ID} id
     */
    writeLeftID(id2) {
      this.clientEncoder.write(id2.client);
      this.leftClockEncoder.write(id2.clock);
    }
    /**
     * @param {ID} id
     */
    writeRightID(id2) {
      this.clientEncoder.write(id2.client);
      this.rightClockEncoder.write(id2.clock);
    }
    /**
     * @param {number} client
     */
    writeClient(client) {
      this.clientEncoder.write(client);
    }
    /**
     * @param {number} info An unsigned 8-bit integer
     */
    writeInfo(info) {
      this.infoEncoder.write(info);
    }
    /**
     * @param {string} s
     */
    writeString(s) {
      this.stringEncoder.write(s);
    }
    /**
     * @param {boolean} isYKey
     */
    writeParentInfo(isYKey) {
      this.parentInfoEncoder.write(isYKey ? 1 : 0);
    }
    /**
     * @param {number} info An unsigned 8-bit integer
     */
    writeTypeRef(info) {
      this.typeRefEncoder.write(info);
    }
    /**
     * Write len of a struct - well suited for Opt RLE encoder.
     *
     * @param {number} len
     */
    writeLen(len) {
      this.lenEncoder.write(len);
    }
    /**
     * @param {any} any
     */
    writeAny(any2) {
      writeAny(this.restEncoder, any2);
    }
    /**
     * @param {Uint8Array} buf
     */
    writeBuf(buf) {
      writeVarUint8Array(this.restEncoder, buf);
    }
    /**
     * This is mainly here for legacy purposes.
     *
     * Initial we incoded objects using JSON. Now we use the much faster lib0/any-encoder. This method mainly exists for legacy purposes for the v1 encoder.
     *
     * @param {any} embed
     */
    writeJSON(embed) {
      writeAny(this.restEncoder, embed);
    }
    /**
     * Property keys are often reused. For example, in y-prosemirror the key `bold` might
     * occur very often. For a 3d application, the key `position` might occur very often.
     *
     * We cache these keys in a Map and refer to them via a unique number.
     *
     * @param {string} key
     */
    writeKey(key) {
      const clock = this.keyMap.get(key);
      if (clock === void 0) {
        this.keyClockEncoder.write(this.keyClock++);
        this.stringEncoder.write(key);
      } else {
        this.keyClockEncoder.write(clock);
      }
    }
  };
  var writeStructs = (encoder, structs, client, clock) => {
    clock = max(clock, structs[0].id.clock);
    const startNewStructs = findIndexSS(structs, clock);
    writeVarUint(encoder.restEncoder, structs.length - startNewStructs);
    encoder.writeClient(client);
    writeVarUint(encoder.restEncoder, clock);
    const firstStruct = structs[startNewStructs];
    firstStruct.write(encoder, clock - firstStruct.id.clock);
    for (let i = startNewStructs + 1; i < structs.length; i++) {
      structs[i].write(encoder, 0);
    }
  };
  var writeClientsStructs = (encoder, store2, _sm) => {
    const sm = /* @__PURE__ */ new Map();
    _sm.forEach((clock, client) => {
      if (getState(store2, client) > clock) {
        sm.set(client, clock);
      }
    });
    getStateVector(store2).forEach((_clock, client) => {
      if (!_sm.has(client)) {
        sm.set(client, 0);
      }
    });
    writeVarUint(encoder.restEncoder, sm.size);
    from(sm.entries()).sort((a, b) => b[0] - a[0]).forEach(([client, clock]) => {
      writeStructs(
        encoder,
        /** @type {Array<GC|Item>} */
        store2.clients.get(client),
        client,
        clock
      );
    });
  };
  var readClientsStructRefs = (decoder, doc2) => {
    const clientRefs = create();
    const numOfStateUpdates = readVarUint(decoder.restDecoder);
    for (let i = 0; i < numOfStateUpdates; i++) {
      const numberOfStructs = readVarUint(decoder.restDecoder);
      const refs = new Array(numberOfStructs);
      const client = decoder.readClient();
      let clock = readVarUint(decoder.restDecoder);
      clientRefs.set(client, { i: 0, refs });
      for (let i2 = 0; i2 < numberOfStructs; i2++) {
        const info = decoder.readInfo();
        switch (BITS5 & info) {
          case 0: {
            const len = decoder.readLen();
            refs[i2] = new GC(createID(client, clock), len);
            clock += len;
            break;
          }
          case 10: {
            const len = readVarUint(decoder.restDecoder);
            refs[i2] = new Skip(createID(client, clock), len);
            clock += len;
            break;
          }
          default: {
            const cantCopyParentInfo = (info & (BIT7 | BIT8)) === 0;
            const struct = new Item(
              createID(client, clock),
              null,
              // leftd
              (info & BIT8) === BIT8 ? decoder.readLeftID() : null,
              // origin
              null,
              // right
              (info & BIT7) === BIT7 ? decoder.readRightID() : null,
              // right origin
              cantCopyParentInfo ? decoder.readParentInfo() ? doc2.get(decoder.readString()) : decoder.readLeftID() : null,
              // parent
              cantCopyParentInfo && (info & BIT6) === BIT6 ? decoder.readString() : null,
              // parentSub
              readItemContent(decoder, info)
              // item content
            );
            refs[i2] = struct;
            clock += struct.length;
          }
        }
      }
    }
    return clientRefs;
  };
  var integrateStructs = (transaction, store2, clientsStructRefs) => {
    const stack = [];
    let clientsStructRefsIds = from(clientsStructRefs.keys()).sort((a, b) => a - b);
    if (clientsStructRefsIds.length === 0) {
      return null;
    }
    const getNextStructTarget = () => {
      if (clientsStructRefsIds.length === 0) {
        return null;
      }
      let nextStructsTarget = (
        /** @type {{i:number,refs:Array<GC|Item>}} */
        clientsStructRefs.get(clientsStructRefsIds[clientsStructRefsIds.length - 1])
      );
      while (nextStructsTarget.refs.length === nextStructsTarget.i) {
        clientsStructRefsIds.pop();
        if (clientsStructRefsIds.length > 0) {
          nextStructsTarget = /** @type {{i:number,refs:Array<GC|Item>}} */
          clientsStructRefs.get(clientsStructRefsIds[clientsStructRefsIds.length - 1]);
        } else {
          return null;
        }
      }
      return nextStructsTarget;
    };
    let curStructsTarget = getNextStructTarget();
    if (curStructsTarget === null && stack.length === 0) {
      return null;
    }
    const restStructs = new StructStore();
    const missingSV = /* @__PURE__ */ new Map();
    const updateMissingSv = (client, clock) => {
      const mclock = missingSV.get(client);
      if (mclock == null || mclock > clock) {
        missingSV.set(client, clock);
      }
    };
    let stackHead = (
      /** @type {any} */
      curStructsTarget.refs[
        /** @type {any} */
        curStructsTarget.i++
      ]
    );
    const state = /* @__PURE__ */ new Map();
    const addStackToRestSS = () => {
      for (const item of stack) {
        const client = item.id.client;
        const unapplicableItems = clientsStructRefs.get(client);
        if (unapplicableItems) {
          unapplicableItems.i--;
          restStructs.clients.set(client, unapplicableItems.refs.slice(unapplicableItems.i));
          clientsStructRefs.delete(client);
          unapplicableItems.i = 0;
          unapplicableItems.refs = [];
        } else {
          restStructs.clients.set(client, [item]);
        }
        clientsStructRefsIds = clientsStructRefsIds.filter((c) => c !== client);
      }
      stack.length = 0;
    };
    while (true) {
      if (stackHead.constructor !== Skip) {
        const localClock = setIfUndefined(state, stackHead.id.client, () => getState(store2, stackHead.id.client));
        const offset = localClock - stackHead.id.clock;
        if (offset < 0) {
          stack.push(stackHead);
          updateMissingSv(stackHead.id.client, stackHead.id.clock - 1);
          addStackToRestSS();
        } else {
          const missing = stackHead.getMissing(transaction, store2);
          if (missing !== null) {
            stack.push(stackHead);
            const structRefs = clientsStructRefs.get(
              /** @type {number} */
              missing
            ) || { refs: [], i: 0 };
            if (structRefs.refs.length === structRefs.i) {
              updateMissingSv(
                /** @type {number} */
                missing,
                getState(store2, missing)
              );
              addStackToRestSS();
            } else {
              stackHead = structRefs.refs[structRefs.i++];
              continue;
            }
          } else if (offset === 0 || offset < stackHead.length) {
            stackHead.integrate(transaction, offset);
            state.set(stackHead.id.client, stackHead.id.clock + stackHead.length);
          }
        }
      }
      if (stack.length > 0) {
        stackHead = /** @type {GC|Item} */
        stack.pop();
      } else if (curStructsTarget !== null && curStructsTarget.i < curStructsTarget.refs.length) {
        stackHead = /** @type {GC|Item} */
        curStructsTarget.refs[curStructsTarget.i++];
      } else {
        curStructsTarget = getNextStructTarget();
        if (curStructsTarget === null) {
          break;
        } else {
          stackHead = /** @type {GC|Item} */
          curStructsTarget.refs[curStructsTarget.i++];
        }
      }
    }
    if (restStructs.clients.size > 0) {
      const encoder = new UpdateEncoderV2();
      writeClientsStructs(encoder, restStructs, /* @__PURE__ */ new Map());
      writeVarUint(encoder.restEncoder, 0);
      return { missing: missingSV, update: encoder.toUint8Array() };
    }
    return null;
  };
  var writeStructsFromTransaction = (encoder, transaction) => writeClientsStructs(encoder, transaction.doc.store, transaction.beforeState);
  var readUpdateV2 = (decoder, ydoc, transactionOrigin, structDecoder = new UpdateDecoderV2(decoder)) => transact(ydoc, (transaction) => {
    transaction.local = false;
    let retry = false;
    const doc2 = transaction.doc;
    const store2 = doc2.store;
    const ss = readClientsStructRefs(structDecoder, doc2);
    const restStructs = integrateStructs(transaction, store2, ss);
    const pending = store2.pendingStructs;
    if (pending) {
      for (const [client, clock] of pending.missing) {
        if (clock < getState(store2, client)) {
          retry = true;
          break;
        }
      }
      if (restStructs) {
        for (const [client, clock] of restStructs.missing) {
          const mclock = pending.missing.get(client);
          if (mclock == null || mclock > clock) {
            pending.missing.set(client, clock);
          }
        }
        pending.update = mergeUpdatesV2([pending.update, restStructs.update]);
      }
    } else {
      store2.pendingStructs = restStructs;
    }
    const dsRest = readAndApplyDeleteSet(structDecoder, transaction, store2);
    if (store2.pendingDs) {
      const pendingDSUpdate = new UpdateDecoderV2(createDecoder(store2.pendingDs));
      readVarUint(pendingDSUpdate.restDecoder);
      const dsRest2 = readAndApplyDeleteSet(pendingDSUpdate, transaction, store2);
      if (dsRest && dsRest2) {
        store2.pendingDs = mergeUpdatesV2([dsRest, dsRest2]);
      } else {
        store2.pendingDs = dsRest || dsRest2;
      }
    } else {
      store2.pendingDs = dsRest;
    }
    if (retry) {
      const update = (
        /** @type {{update: Uint8Array}} */
        store2.pendingStructs.update
      );
      store2.pendingStructs = null;
      applyUpdateV2(transaction.doc, update);
    }
  }, transactionOrigin, false);
  var readUpdate = (decoder, ydoc, transactionOrigin) => readUpdateV2(decoder, ydoc, transactionOrigin, new UpdateDecoderV1(decoder));
  var applyUpdateV2 = (ydoc, update, transactionOrigin, YDecoder = UpdateDecoderV2) => {
    const decoder = createDecoder(update);
    readUpdateV2(decoder, ydoc, transactionOrigin, new YDecoder(decoder));
  };
  var applyUpdate = (ydoc, update, transactionOrigin) => applyUpdateV2(ydoc, update, transactionOrigin, UpdateDecoderV1);
  var writeStateAsUpdate = (encoder, doc2, targetStateVector = /* @__PURE__ */ new Map()) => {
    writeClientsStructs(encoder, doc2.store, targetStateVector);
    writeDeleteSet(encoder, createDeleteSetFromStructStore(doc2.store));
  };
  var encodeStateAsUpdateV2 = (doc2, encodedTargetStateVector = new Uint8Array([0]), encoder = new UpdateEncoderV2()) => {
    const targetStateVector = decodeStateVector(encodedTargetStateVector);
    writeStateAsUpdate(encoder, doc2, targetStateVector);
    const updates = [encoder.toUint8Array()];
    if (doc2.store.pendingDs) {
      updates.push(doc2.store.pendingDs);
    }
    if (doc2.store.pendingStructs) {
      updates.push(diffUpdateV2(doc2.store.pendingStructs.update, encodedTargetStateVector));
    }
    if (updates.length > 1) {
      if (encoder.constructor === UpdateEncoderV1) {
        return mergeUpdates(updates.map((update, i) => i === 0 ? update : convertUpdateFormatV2ToV1(update)));
      } else if (encoder.constructor === UpdateEncoderV2) {
        return mergeUpdatesV2(updates);
      }
    }
    return updates[0];
  };
  var encodeStateAsUpdate = (doc2, encodedTargetStateVector) => encodeStateAsUpdateV2(doc2, encodedTargetStateVector, new UpdateEncoderV1());
  var readStateVector = (decoder) => {
    const ss = /* @__PURE__ */ new Map();
    const ssLength = readVarUint(decoder.restDecoder);
    for (let i = 0; i < ssLength; i++) {
      const client = readVarUint(decoder.restDecoder);
      const clock = readVarUint(decoder.restDecoder);
      ss.set(client, clock);
    }
    return ss;
  };
  var decodeStateVector = (decodedState) => readStateVector(new DSDecoderV1(createDecoder(decodedState)));
  var writeStateVector = (encoder, sv) => {
    writeVarUint(encoder.restEncoder, sv.size);
    from(sv.entries()).sort((a, b) => b[0] - a[0]).forEach(([client, clock]) => {
      writeVarUint(encoder.restEncoder, client);
      writeVarUint(encoder.restEncoder, clock);
    });
    return encoder;
  };
  var writeDocumentStateVector = (encoder, doc2) => writeStateVector(encoder, getStateVector(doc2.store));
  var encodeStateVectorV2 = (doc2, encoder = new DSEncoderV2()) => {
    if (doc2 instanceof Map) {
      writeStateVector(encoder, doc2);
    } else {
      writeDocumentStateVector(encoder, doc2);
    }
    return encoder.toUint8Array();
  };
  var encodeStateVector = (doc2) => encodeStateVectorV2(doc2, new DSEncoderV1());
  var EventHandler = class {
    constructor() {
      this.l = [];
    }
  };
  var createEventHandler = () => new EventHandler();
  var addEventHandlerListener = (eventHandler, f) => eventHandler.l.push(f);
  var removeEventHandlerListener = (eventHandler, f) => {
    const l = eventHandler.l;
    const len = l.length;
    eventHandler.l = l.filter((g) => f !== g);
    if (len === eventHandler.l.length) {
      console.error("[yjs] Tried to remove event handler that doesn't exist.");
    }
  };
  var callEventHandlerListeners = (eventHandler, arg0, arg1) => callAll(eventHandler.l, [arg0, arg1]);
  var ID = class {
    /**
     * @param {number} client client id
     * @param {number} clock unique per client id, continuous number
     */
    constructor(client, clock) {
      this.client = client;
      this.clock = clock;
    }
  };
  var compareIDs = (a, b) => a === b || a !== null && b !== null && a.client === b.client && a.clock === b.clock;
  var createID = (client, clock) => new ID(client, clock);
  var writeID = (encoder, id2) => {
    writeVarUint(encoder, id2.client);
    writeVarUint(encoder, id2.clock);
  };
  var readID = (decoder) => createID(readVarUint(decoder), readVarUint(decoder));
  var findRootTypeKey = (type) => {
    for (const [key, value] of type.doc.share.entries()) {
      if (value === type) {
        return key;
      }
    }
    throw unexpectedCase();
  };
  var isParentOf = (parent, child) => {
    while (child !== null) {
      if (child.parent === parent) {
        return true;
      }
      child = /** @type {AbstractType<any>} */
      child.parent._item;
    }
    return false;
  };
  var logType = (type) => {
    const res = [];
    let n = type._start;
    while (n) {
      res.push(n);
      n = n.right;
    }
    console.log("Children: ", res);
    console.log("Children content: ", res.filter((m) => !m.deleted).map((m) => m.content));
  };
  var PermanentUserData = class {
    /**
     * @param {Doc} doc
     * @param {YMap<any>} [storeType]
     */
    constructor(doc2, storeType = doc2.getMap("users")) {
      const dss = /* @__PURE__ */ new Map();
      this.yusers = storeType;
      this.doc = doc2;
      this.clients = /* @__PURE__ */ new Map();
      this.dss = dss;
      const initUser = (user, userDescription) => {
        const ds = user.get("ds");
        const ids = user.get("ids");
        const addClientId = (
          /** @param {number} clientid */
          (clientid) => this.clients.set(clientid, userDescription)
        );
        ds.observe(
          /** @param {YArrayEvent<any>} event */
          (event) => {
            event.changes.added.forEach((item) => {
              item.content.getContent().forEach((encodedDs) => {
                if (encodedDs instanceof Uint8Array) {
                  this.dss.set(userDescription, mergeDeleteSets([this.dss.get(userDescription) || createDeleteSet(), readDeleteSet(new DSDecoderV1(createDecoder(encodedDs)))]));
                }
              });
            });
          }
        );
        this.dss.set(userDescription, mergeDeleteSets(ds.map((encodedDs) => readDeleteSet(new DSDecoderV1(createDecoder(encodedDs))))));
        ids.observe(
          /** @param {YArrayEvent<any>} event */
          (event) => event.changes.added.forEach((item) => item.content.getContent().forEach(addClientId))
        );
        ids.forEach(addClientId);
      };
      storeType.observe((event) => {
        event.keysChanged.forEach(
          (userDescription) => initUser(storeType.get(userDescription), userDescription)
        );
      });
      storeType.forEach(initUser);
    }
    /**
     * @param {Doc} doc
     * @param {number} clientid
     * @param {string} userDescription
     * @param {Object} conf
     * @param {function(Transaction, DeleteSet):boolean} [conf.filter]
     */
    setUserMapping(doc2, clientid, userDescription, { filter = () => true } = {}) {
      const users2 = this.yusers;
      let user = users2.get(userDescription);
      if (!user) {
        user = new YMap();
        user.set("ids", new YArray());
        user.set("ds", new YArray());
        users2.set(userDescription, user);
      }
      user.get("ids").push([clientid]);
      users2.observe((_event) => {
        setTimeout(() => {
          const userOverwrite = users2.get(userDescription);
          if (userOverwrite !== user) {
            user = userOverwrite;
            this.clients.forEach((_userDescription, clientid2) => {
              if (userDescription === _userDescription) {
                user.get("ids").push([clientid2]);
              }
            });
            const encoder = new DSEncoderV1();
            const ds = this.dss.get(userDescription);
            if (ds) {
              writeDeleteSet(encoder, ds);
              user.get("ds").push([encoder.toUint8Array()]);
            }
          }
        }, 0);
      });
      doc2.on(
        "afterTransaction",
        /** @param {Transaction} transaction */
        (transaction) => {
          setTimeout(() => {
            const yds = user.get("ds");
            const ds = transaction.deleteSet;
            if (transaction.local && ds.clients.size > 0 && filter(transaction, ds)) {
              const encoder = new DSEncoderV1();
              writeDeleteSet(encoder, ds);
              yds.push([encoder.toUint8Array()]);
            }
          });
        }
      );
    }
    /**
     * @param {number} clientid
     * @return {any}
     */
    getUserByClientId(clientid) {
      return this.clients.get(clientid) || null;
    }
    /**
     * @param {ID} id
     * @return {string | null}
     */
    getUserByDeletedId(id2) {
      for (const [userDescription, ds] of this.dss.entries()) {
        if (isDeleted(ds, id2)) {
          return userDescription;
        }
      }
      return null;
    }
  };
  var RelativePosition = class {
    /**
     * @param {ID|null} type
     * @param {string|null} tname
     * @param {ID|null} item
     * @param {number} assoc
     */
    constructor(type, tname, item, assoc = 0) {
      this.type = type;
      this.tname = tname;
      this.item = item;
      this.assoc = assoc;
    }
  };
  var relativePositionToJSON = (rpos) => {
    const json = {};
    if (rpos.type) {
      json.type = rpos.type;
    }
    if (rpos.tname) {
      json.tname = rpos.tname;
    }
    if (rpos.item) {
      json.item = rpos.item;
    }
    if (rpos.assoc != null) {
      json.assoc = rpos.assoc;
    }
    return json;
  };
  var createRelativePositionFromJSON = (json) => new RelativePosition(json.type == null ? null : createID(json.type.client, json.type.clock), json.tname || null, json.item == null ? null : createID(json.item.client, json.item.clock), json.assoc == null ? 0 : json.assoc);
  var AbsolutePosition = class {
    /**
     * @param {AbstractType<any>} type
     * @param {number} index
     * @param {number} [assoc]
     */
    constructor(type, index, assoc = 0) {
      this.type = type;
      this.index = index;
      this.assoc = assoc;
    }
  };
  var createAbsolutePosition = (type, index, assoc = 0) => new AbsolutePosition(type, index, assoc);
  var createRelativePosition = (type, item, assoc) => {
    let typeid = null;
    let tname = null;
    if (type._item === null) {
      tname = findRootTypeKey(type);
    } else {
      typeid = createID(type._item.id.client, type._item.id.clock);
    }
    return new RelativePosition(typeid, tname, item, assoc);
  };
  var createRelativePositionFromTypeIndex = (type, index, assoc = 0) => {
    let t = type._start;
    if (assoc < 0) {
      if (index === 0) {
        return createRelativePosition(type, null, assoc);
      }
      index--;
    }
    while (t !== null) {
      if (!t.deleted && t.countable) {
        if (t.length > index) {
          return createRelativePosition(type, createID(t.id.client, t.id.clock + index), assoc);
        }
        index -= t.length;
      }
      if (t.right === null && assoc < 0) {
        return createRelativePosition(type, t.lastId, assoc);
      }
      t = t.right;
    }
    return createRelativePosition(type, null, assoc);
  };
  var writeRelativePosition = (encoder, rpos) => {
    const { type, tname, item, assoc } = rpos;
    if (item !== null) {
      writeVarUint(encoder, 0);
      writeID(encoder, item);
    } else if (tname !== null) {
      writeUint8(encoder, 1);
      writeVarString(encoder, tname);
    } else if (type !== null) {
      writeUint8(encoder, 2);
      writeID(encoder, type);
    } else {
      throw unexpectedCase();
    }
    writeVarInt(encoder, assoc);
    return encoder;
  };
  var encodeRelativePosition = (rpos) => {
    const encoder = createEncoder();
    writeRelativePosition(encoder, rpos);
    return toUint8Array(encoder);
  };
  var readRelativePosition = (decoder) => {
    let type = null;
    let tname = null;
    let itemID = null;
    switch (readVarUint(decoder)) {
      case 0:
        itemID = readID(decoder);
        break;
      case 1:
        tname = readVarString(decoder);
        break;
      case 2: {
        type = readID(decoder);
      }
    }
    const assoc = hasContent(decoder) ? readVarInt(decoder) : 0;
    return new RelativePosition(type, tname, itemID, assoc);
  };
  var decodeRelativePosition = (uint8Array) => readRelativePosition(createDecoder(uint8Array));
  var createAbsolutePositionFromRelativePosition = (rpos, doc2) => {
    const store2 = doc2.store;
    const rightID = rpos.item;
    const typeID = rpos.type;
    const tname = rpos.tname;
    const assoc = rpos.assoc;
    let type = null;
    let index = 0;
    if (rightID !== null) {
      if (getState(store2, rightID.client) <= rightID.clock) {
        return null;
      }
      const res = followRedone(store2, rightID);
      const right = res.item;
      if (!(right instanceof Item)) {
        return null;
      }
      type = /** @type {AbstractType<any>} */
      right.parent;
      if (type._item === null || !type._item.deleted) {
        index = right.deleted || !right.countable ? 0 : res.diff + (assoc >= 0 ? 0 : 1);
        let n = right.left;
        while (n !== null) {
          if (!n.deleted && n.countable) {
            index += n.length;
          }
          n = n.left;
        }
      }
    } else {
      if (tname !== null) {
        type = doc2.get(tname);
      } else if (typeID !== null) {
        if (getState(store2, typeID.client) <= typeID.clock) {
          return null;
        }
        const { item } = followRedone(store2, typeID);
        if (item instanceof Item && item.content instanceof ContentType) {
          type = item.content.type;
        } else {
          return null;
        }
      } else {
        throw unexpectedCase();
      }
      if (assoc >= 0) {
        index = type._length;
      } else {
        index = 0;
      }
    }
    return createAbsolutePosition(type, index, rpos.assoc);
  };
  var compareRelativePositions = (a, b) => a === b || a !== null && b !== null && a.tname === b.tname && compareIDs(a.item, b.item) && compareIDs(a.type, b.type) && a.assoc === b.assoc;
  var Snapshot = class {
    /**
     * @param {DeleteSet} ds
     * @param {Map<number,number>} sv state map
     */
    constructor(ds, sv) {
      this.ds = ds;
      this.sv = sv;
    }
  };
  var equalSnapshots = (snap1, snap2) => {
    const ds1 = snap1.ds.clients;
    const ds2 = snap2.ds.clients;
    const sv1 = snap1.sv;
    const sv2 = snap2.sv;
    if (sv1.size !== sv2.size || ds1.size !== ds2.size) {
      return false;
    }
    for (const [key, value] of sv1.entries()) {
      if (sv2.get(key) !== value) {
        return false;
      }
    }
    for (const [client, dsitems1] of ds1.entries()) {
      const dsitems2 = ds2.get(client) || [];
      if (dsitems1.length !== dsitems2.length) {
        return false;
      }
      for (let i = 0; i < dsitems1.length; i++) {
        const dsitem1 = dsitems1[i];
        const dsitem2 = dsitems2[i];
        if (dsitem1.clock !== dsitem2.clock || dsitem1.len !== dsitem2.len) {
          return false;
        }
      }
    }
    return true;
  };
  var encodeSnapshotV2 = (snapshot2, encoder = new DSEncoderV2()) => {
    writeDeleteSet(encoder, snapshot2.ds);
    writeStateVector(encoder, snapshot2.sv);
    return encoder.toUint8Array();
  };
  var encodeSnapshot = (snapshot2) => encodeSnapshotV2(snapshot2, new DSEncoderV1());
  var decodeSnapshotV2 = (buf, decoder = new DSDecoderV2(createDecoder(buf))) => {
    return new Snapshot(readDeleteSet(decoder), readStateVector(decoder));
  };
  var decodeSnapshot = (buf) => decodeSnapshotV2(buf, new DSDecoderV1(createDecoder(buf)));
  var createSnapshot = (ds, sm) => new Snapshot(ds, sm);
  var emptySnapshot = createSnapshot(createDeleteSet(), /* @__PURE__ */ new Map());
  var snapshot = (doc2) => createSnapshot(createDeleteSetFromStructStore(doc2.store), getStateVector(doc2.store));
  var isVisible = (item, snapshot2) => snapshot2 === void 0 ? !item.deleted : snapshot2.sv.has(item.id.client) && (snapshot2.sv.get(item.id.client) || 0) > item.id.clock && !isDeleted(snapshot2.ds, item.id);
  var splitSnapshotAffectedStructs = (transaction, snapshot2) => {
    const meta = setIfUndefined(transaction.meta, splitSnapshotAffectedStructs, create2);
    const store2 = transaction.doc.store;
    if (!meta.has(snapshot2)) {
      snapshot2.sv.forEach((clock, client) => {
        if (clock < getState(store2, client)) {
          getItemCleanStart(transaction, createID(client, clock));
        }
      });
      iterateDeletedStructs(transaction, snapshot2.ds, (_item) => {
      });
      meta.add(snapshot2);
    }
  };
  var createDocFromSnapshot = (originDoc, snapshot2, newDoc = new Doc()) => {
    if (originDoc.gc) {
      throw new Error("Garbage-collection must be disabled in `originDoc`!");
    }
    const { sv, ds } = snapshot2;
    const encoder = new UpdateEncoderV2();
    originDoc.transact((transaction) => {
      let size = 0;
      sv.forEach((clock) => {
        if (clock > 0) {
          size++;
        }
      });
      writeVarUint(encoder.restEncoder, size);
      for (const [client, clock] of sv) {
        if (clock === 0) {
          continue;
        }
        if (clock < getState(originDoc.store, client)) {
          getItemCleanStart(transaction, createID(client, clock));
        }
        const structs = originDoc.store.clients.get(client) || [];
        const lastStructIndex = findIndexSS(structs, clock - 1);
        writeVarUint(encoder.restEncoder, lastStructIndex + 1);
        encoder.writeClient(client);
        writeVarUint(encoder.restEncoder, 0);
        for (let i = 0; i <= lastStructIndex; i++) {
          structs[i].write(encoder, 0);
        }
      }
      writeDeleteSet(encoder, ds);
    });
    applyUpdateV2(newDoc, encoder.toUint8Array(), "snapshot");
    return newDoc;
  };
  var snapshotContainsUpdateV2 = (snapshot2, update, YDecoder = UpdateDecoderV2) => {
    const updateDecoder = new YDecoder(createDecoder(update));
    const lazyDecoder = new LazyStructReader(updateDecoder, false);
    for (let curr = lazyDecoder.curr; curr !== null; curr = lazyDecoder.next()) {
      if ((snapshot2.sv.get(curr.id.client) || 0) < curr.id.clock + curr.length) {
        return false;
      }
    }
    const mergedDS = mergeDeleteSets([snapshot2.ds, readDeleteSet(updateDecoder)]);
    return equalDeleteSets(snapshot2.ds, mergedDS);
  };
  var snapshotContainsUpdate = (snapshot2, update) => snapshotContainsUpdateV2(snapshot2, update, UpdateDecoderV1);
  var StructStore = class {
    constructor() {
      this.clients = /* @__PURE__ */ new Map();
      this.pendingStructs = null;
      this.pendingDs = null;
    }
  };
  var getStateVector = (store2) => {
    const sm = /* @__PURE__ */ new Map();
    store2.clients.forEach((structs, client) => {
      const struct = structs[structs.length - 1];
      sm.set(client, struct.id.clock + struct.length);
    });
    return sm;
  };
  var getState = (store2, client) => {
    const structs = store2.clients.get(client);
    if (structs === void 0) {
      return 0;
    }
    const lastStruct = structs[structs.length - 1];
    return lastStruct.id.clock + lastStruct.length;
  };
  var addStruct = (store2, struct) => {
    let structs = store2.clients.get(struct.id.client);
    if (structs === void 0) {
      structs = [];
      store2.clients.set(struct.id.client, structs);
    } else {
      const lastStruct = structs[structs.length - 1];
      if (lastStruct.id.clock + lastStruct.length !== struct.id.clock) {
        throw unexpectedCase();
      }
    }
    structs.push(struct);
  };
  var findIndexSS = (structs, clock) => {
    let left = 0;
    let right = structs.length - 1;
    let mid = structs[right];
    let midclock = mid.id.clock;
    if (midclock === clock) {
      return right;
    }
    let midindex = floor(clock / (midclock + mid.length - 1) * right);
    while (left <= right) {
      mid = structs[midindex];
      midclock = mid.id.clock;
      if (midclock <= clock) {
        if (clock < midclock + mid.length) {
          return midindex;
        }
        left = midindex + 1;
      } else {
        right = midindex - 1;
      }
      midindex = floor((left + right) / 2);
    }
    throw unexpectedCase();
  };
  var find = (store2, id2) => {
    const structs = store2.clients.get(id2.client);
    return structs[findIndexSS(structs, id2.clock)];
  };
  var getItem = (
    /** @type {function(StructStore,ID):Item} */
    find
  );
  var findIndexCleanStart = (transaction, structs, clock) => {
    const index = findIndexSS(structs, clock);
    const struct = structs[index];
    if (struct.id.clock < clock && struct instanceof Item) {
      structs.splice(index + 1, 0, splitItem(transaction, struct, clock - struct.id.clock));
      return index + 1;
    }
    return index;
  };
  var getItemCleanStart = (transaction, id2) => {
    const structs = (
      /** @type {Array<Item>} */
      transaction.doc.store.clients.get(id2.client)
    );
    return structs[findIndexCleanStart(transaction, structs, id2.clock)];
  };
  var getItemCleanEnd = (transaction, store2, id2) => {
    const structs = store2.clients.get(id2.client);
    const index = findIndexSS(structs, id2.clock);
    const struct = structs[index];
    if (id2.clock !== struct.id.clock + struct.length - 1 && struct.constructor !== GC) {
      structs.splice(index + 1, 0, splitItem(transaction, struct, id2.clock - struct.id.clock + 1));
    }
    return struct;
  };
  var replaceStruct = (store2, struct, newStruct) => {
    const structs = (
      /** @type {Array<GC|Item>} */
      store2.clients.get(struct.id.client)
    );
    structs[findIndexSS(structs, struct.id.clock)] = newStruct;
  };
  var iterateStructs = (transaction, structs, clockStart, len, f) => {
    if (len === 0) {
      return;
    }
    const clockEnd = clockStart + len;
    let index = findIndexCleanStart(transaction, structs, clockStart);
    let struct;
    do {
      struct = structs[index++];
      if (clockEnd < struct.id.clock + struct.length) {
        findIndexCleanStart(transaction, structs, clockEnd);
      }
      f(struct);
    } while (index < structs.length && structs[index].id.clock < clockEnd);
  };
  var Transaction = class {
    /**
     * @param {Doc} doc
     * @param {any} origin
     * @param {boolean} local
     */
    constructor(doc2, origin, local) {
      this.doc = doc2;
      this.deleteSet = new DeleteSet();
      this.beforeState = getStateVector(doc2.store);
      this.afterState = /* @__PURE__ */ new Map();
      this.changed = /* @__PURE__ */ new Map();
      this.changedParentTypes = /* @__PURE__ */ new Map();
      this._mergeStructs = [];
      this.origin = origin;
      this.meta = /* @__PURE__ */ new Map();
      this.local = local;
      this.subdocsAdded = /* @__PURE__ */ new Set();
      this.subdocsRemoved = /* @__PURE__ */ new Set();
      this.subdocsLoaded = /* @__PURE__ */ new Set();
      this._needFormattingCleanup = false;
    }
  };
  var writeUpdateMessageFromTransaction = (encoder, transaction) => {
    if (transaction.deleteSet.clients.size === 0 && !any(transaction.afterState, (clock, client) => transaction.beforeState.get(client) !== clock)) {
      return false;
    }
    sortAndMergeDeleteSet(transaction.deleteSet);
    writeStructsFromTransaction(encoder, transaction);
    writeDeleteSet(encoder, transaction.deleteSet);
    return true;
  };
  var addChangedTypeToTransaction = (transaction, type, parentSub) => {
    const item = type._item;
    if (item === null || item.id.clock < (transaction.beforeState.get(item.id.client) || 0) && !item.deleted) {
      setIfUndefined(transaction.changed, type, create2).add(parentSub);
    }
  };
  var tryToMergeWithLefts = (structs, pos) => {
    let right = structs[pos];
    let left = structs[pos - 1];
    let i = pos;
    for (; i > 0; right = left, left = structs[--i - 1]) {
      if (left.deleted === right.deleted && left.constructor === right.constructor) {
        if (left.mergeWith(right)) {
          if (right instanceof Item && right.parentSub !== null && /** @type {AbstractType<any>} */
          right.parent._map.get(right.parentSub) === right) {
            right.parent._map.set(
              right.parentSub,
              /** @type {Item} */
              left
            );
          }
          continue;
        }
      }
      break;
    }
    const merged = pos - i;
    if (merged) {
      structs.splice(pos + 1 - merged, merged);
    }
    return merged;
  };
  var tryGcDeleteSet = (ds, store2, gcFilter) => {
    for (const [client, deleteItems] of ds.clients.entries()) {
      const structs = (
        /** @type {Array<GC|Item>} */
        store2.clients.get(client)
      );
      for (let di = deleteItems.length - 1; di >= 0; di--) {
        const deleteItem = deleteItems[di];
        const endDeleteItemClock = deleteItem.clock + deleteItem.len;
        for (let si = findIndexSS(structs, deleteItem.clock), struct = structs[si]; si < structs.length && struct.id.clock < endDeleteItemClock; struct = structs[++si]) {
          const struct2 = structs[si];
          if (deleteItem.clock + deleteItem.len <= struct2.id.clock) {
            break;
          }
          if (struct2 instanceof Item && struct2.deleted && !struct2.keep && gcFilter(struct2)) {
            struct2.gc(store2, false);
          }
        }
      }
    }
  };
  var tryMergeDeleteSet = (ds, store2) => {
    ds.clients.forEach((deleteItems, client) => {
      const structs = (
        /** @type {Array<GC|Item>} */
        store2.clients.get(client)
      );
      for (let di = deleteItems.length - 1; di >= 0; di--) {
        const deleteItem = deleteItems[di];
        const mostRightIndexToCheck = min(structs.length - 1, 1 + findIndexSS(structs, deleteItem.clock + deleteItem.len - 1));
        for (let si = mostRightIndexToCheck, struct = structs[si]; si > 0 && struct.id.clock >= deleteItem.clock; struct = structs[si]) {
          si -= 1 + tryToMergeWithLefts(structs, si);
        }
      }
    });
  };
  var tryGc = (ds, store2, gcFilter) => {
    tryGcDeleteSet(ds, store2, gcFilter);
    tryMergeDeleteSet(ds, store2);
  };
  var cleanupTransactions = (transactionCleanups, i) => {
    if (i < transactionCleanups.length) {
      const transaction = transactionCleanups[i];
      const doc2 = transaction.doc;
      const store2 = doc2.store;
      const ds = transaction.deleteSet;
      const mergeStructs = transaction._mergeStructs;
      try {
        sortAndMergeDeleteSet(ds);
        transaction.afterState = getStateVector(transaction.doc.store);
        doc2.emit("beforeObserverCalls", [transaction, doc2]);
        const fs = [];
        transaction.changed.forEach(
          (subs, itemtype) => fs.push(() => {
            if (itemtype._item === null || !itemtype._item.deleted) {
              itemtype._callObserver(transaction, subs);
            }
          })
        );
        fs.push(() => {
          transaction.changedParentTypes.forEach((events, type) => {
            if (type._dEH.l.length > 0 && (type._item === null || !type._item.deleted)) {
              events = events.filter(
                (event) => event.target._item === null || !event.target._item.deleted
              );
              events.forEach((event) => {
                event.currentTarget = type;
                event._path = null;
              });
              events.sort((event1, event2) => event1.path.length - event2.path.length);
              callEventHandlerListeners(type._dEH, events, transaction);
            }
          });
        });
        fs.push(() => doc2.emit("afterTransaction", [transaction, doc2]));
        callAll(fs, []);
        if (transaction._needFormattingCleanup) {
          cleanupYTextAfterTransaction(transaction);
        }
      } finally {
        if (doc2.gc) {
          tryGcDeleteSet(ds, store2, doc2.gcFilter);
        }
        tryMergeDeleteSet(ds, store2);
        transaction.afterState.forEach((clock, client) => {
          const beforeClock = transaction.beforeState.get(client) || 0;
          if (beforeClock !== clock) {
            const structs = (
              /** @type {Array<GC|Item>} */
              store2.clients.get(client)
            );
            const firstChangePos = max(findIndexSS(structs, beforeClock), 1);
            for (let i2 = structs.length - 1; i2 >= firstChangePos; ) {
              i2 -= 1 + tryToMergeWithLefts(structs, i2);
            }
          }
        });
        for (let i2 = mergeStructs.length - 1; i2 >= 0; i2--) {
          const { client, clock } = mergeStructs[i2].id;
          const structs = (
            /** @type {Array<GC|Item>} */
            store2.clients.get(client)
          );
          const replacedStructPos = findIndexSS(structs, clock);
          if (replacedStructPos + 1 < structs.length) {
            if (tryToMergeWithLefts(structs, replacedStructPos + 1) > 1) {
              continue;
            }
          }
          if (replacedStructPos > 0) {
            tryToMergeWithLefts(structs, replacedStructPos);
          }
        }
        if (!transaction.local && transaction.afterState.get(doc2.clientID) !== transaction.beforeState.get(doc2.clientID)) {
          print(ORANGE, BOLD, "[yjs] ", UNBOLD, RED, "Changed the client-id because another client seems to be using it.");
          doc2.clientID = generateNewClientId();
        }
        doc2.emit("afterTransactionCleanup", [transaction, doc2]);
        if (doc2._observers.has("update")) {
          const encoder = new UpdateEncoderV1();
          const hasContent2 = writeUpdateMessageFromTransaction(encoder, transaction);
          if (hasContent2) {
            doc2.emit("update", [encoder.toUint8Array(), transaction.origin, doc2, transaction]);
          }
        }
        if (doc2._observers.has("updateV2")) {
          const encoder = new UpdateEncoderV2();
          const hasContent2 = writeUpdateMessageFromTransaction(encoder, transaction);
          if (hasContent2) {
            doc2.emit("updateV2", [encoder.toUint8Array(), transaction.origin, doc2, transaction]);
          }
        }
        const { subdocsAdded, subdocsLoaded, subdocsRemoved } = transaction;
        if (subdocsAdded.size > 0 || subdocsRemoved.size > 0 || subdocsLoaded.size > 0) {
          subdocsAdded.forEach((subdoc) => {
            subdoc.clientID = doc2.clientID;
            if (subdoc.collectionid == null) {
              subdoc.collectionid = doc2.collectionid;
            }
            doc2.subdocs.add(subdoc);
          });
          subdocsRemoved.forEach((subdoc) => doc2.subdocs.delete(subdoc));
          doc2.emit("subdocs", [{ loaded: subdocsLoaded, added: subdocsAdded, removed: subdocsRemoved }, doc2, transaction]);
          subdocsRemoved.forEach((subdoc) => subdoc.destroy());
        }
        if (transactionCleanups.length <= i + 1) {
          doc2._transactionCleanups = [];
          doc2.emit("afterAllTransactions", [doc2, transactionCleanups]);
        } else {
          cleanupTransactions(transactionCleanups, i + 1);
        }
      }
    }
  };
  var transact = (doc2, f, origin = null, local = true) => {
    const transactionCleanups = doc2._transactionCleanups;
    let initialCall = false;
    let result = null;
    if (doc2._transaction === null) {
      initialCall = true;
      doc2._transaction = new Transaction(doc2, origin, local);
      transactionCleanups.push(doc2._transaction);
      if (transactionCleanups.length === 1) {
        doc2.emit("beforeAllTransactions", [doc2]);
      }
      doc2.emit("beforeTransaction", [doc2._transaction, doc2]);
    }
    try {
      result = f(doc2._transaction);
    } finally {
      if (initialCall) {
        const finishCleanup = doc2._transaction === transactionCleanups[0];
        doc2._transaction = null;
        if (finishCleanup) {
          cleanupTransactions(transactionCleanups, 0);
        }
      }
    }
    return result;
  };
  var StackItem = class {
    /**
     * @param {DeleteSet} deletions
     * @param {DeleteSet} insertions
     */
    constructor(deletions, insertions) {
      this.insertions = insertions;
      this.deletions = deletions;
      this.meta = /* @__PURE__ */ new Map();
    }
  };
  var clearUndoManagerStackItem = (tr, um, stackItem) => {
    iterateDeletedStructs(tr, stackItem.deletions, (item) => {
      if (item instanceof Item && um.scope.some((type) => isParentOf(type, item))) {
        keepItem(item, false);
      }
    });
  };
  var popStackItem = (undoManager2, stack, eventType) => {
    let result = null;
    let _tr = null;
    const doc2 = undoManager2.doc;
    const scope = undoManager2.scope;
    transact(doc2, (transaction) => {
      while (stack.length > 0 && result === null) {
        const store2 = doc2.store;
        const stackItem = (
          /** @type {StackItem} */
          stack.pop()
        );
        const itemsToRedo = /* @__PURE__ */ new Set();
        const itemsToDelete = [];
        let performedChange = false;
        iterateDeletedStructs(transaction, stackItem.insertions, (struct) => {
          if (struct instanceof Item) {
            if (struct.redone !== null) {
              let { item, diff } = followRedone(store2, struct.id);
              if (diff > 0) {
                item = getItemCleanStart(transaction, createID(item.id.client, item.id.clock + diff));
              }
              struct = item;
            }
            if (!struct.deleted && scope.some((type) => isParentOf(
              type,
              /** @type {Item} */
              struct
            ))) {
              itemsToDelete.push(struct);
            }
          }
        });
        iterateDeletedStructs(transaction, stackItem.deletions, (struct) => {
          if (struct instanceof Item && scope.some((type) => isParentOf(type, struct)) && // Never redo structs in stackItem.insertions because they were created and deleted in the same capture interval.
          !isDeleted(stackItem.insertions, struct.id)) {
            itemsToRedo.add(struct);
          }
        });
        itemsToRedo.forEach((struct) => {
          performedChange = redoItem(transaction, struct, itemsToRedo, stackItem.insertions, undoManager2.ignoreRemoteMapChanges, undoManager2) !== null || performedChange;
        });
        for (let i = itemsToDelete.length - 1; i >= 0; i--) {
          const item = itemsToDelete[i];
          if (undoManager2.deleteFilter(item)) {
            item.delete(transaction);
            performedChange = true;
          }
        }
        result = performedChange ? stackItem : null;
      }
      transaction.changed.forEach((subProps, type) => {
        if (subProps.has(null) && type._searchMarker) {
          type._searchMarker.length = 0;
        }
      });
      _tr = transaction;
    }, undoManager2);
    if (result != null) {
      const changedParentTypes = _tr.changedParentTypes;
      undoManager2.emit("stack-item-popped", [{ stackItem: result, type: eventType, changedParentTypes }, undoManager2]);
    }
    return result;
  };
  var UndoManager = class extends Observable {
    /**
     * @param {AbstractType<any>|Array<AbstractType<any>>} typeScope Accepts either a single type, or an array of types
     * @param {UndoManagerOptions} options
     */
    constructor(typeScope, {
      captureTimeout = 500,
      captureTransaction = (_tr) => true,
      deleteFilter = () => true,
      trackedOrigins = /* @__PURE__ */ new Set([null]),
      ignoreRemoteMapChanges = false,
      doc: doc2 = (
        /** @type {Doc} */
        isArray(typeScope) ? typeScope[0].doc : typeScope.doc
      )
    } = {}) {
      super();
      this.scope = [];
      this.addToScope(typeScope);
      this.deleteFilter = deleteFilter;
      trackedOrigins.add(this);
      this.trackedOrigins = trackedOrigins;
      this.captureTransaction = captureTransaction;
      this.undoStack = [];
      this.redoStack = [];
      this.undoing = false;
      this.redoing = false;
      this.doc = doc2;
      this.lastChange = 0;
      this.ignoreRemoteMapChanges = ignoreRemoteMapChanges;
      this.captureTimeout = captureTimeout;
      this.afterTransactionHandler = (transaction) => {
        if (!this.captureTransaction(transaction) || !this.scope.some((type) => transaction.changedParentTypes.has(type)) || !this.trackedOrigins.has(transaction.origin) && (!transaction.origin || !this.trackedOrigins.has(transaction.origin.constructor))) {
          return;
        }
        const undoing = this.undoing;
        const redoing = this.redoing;
        const stack = undoing ? this.redoStack : this.undoStack;
        if (undoing) {
          this.stopCapturing();
        } else if (!redoing) {
          this.clear(false, true);
        }
        const insertions = new DeleteSet();
        transaction.afterState.forEach((endClock, client) => {
          const startClock = transaction.beforeState.get(client) || 0;
          const len = endClock - startClock;
          if (len > 0) {
            addToDeleteSet(insertions, client, startClock, len);
          }
        });
        const now = getUnixTime();
        let didAdd = false;
        if (this.lastChange > 0 && now - this.lastChange < this.captureTimeout && stack.length > 0 && !undoing && !redoing) {
          const lastOp = stack[stack.length - 1];
          lastOp.deletions = mergeDeleteSets([lastOp.deletions, transaction.deleteSet]);
          lastOp.insertions = mergeDeleteSets([lastOp.insertions, insertions]);
        } else {
          stack.push(new StackItem(transaction.deleteSet, insertions));
          didAdd = true;
        }
        if (!undoing && !redoing) {
          this.lastChange = now;
        }
        iterateDeletedStructs(
          transaction,
          transaction.deleteSet,
          /** @param {Item|GC} item */
          (item) => {
            if (item instanceof Item && this.scope.some((type) => isParentOf(type, item))) {
              keepItem(item, true);
            }
          }
        );
        const changeEvent = [{ stackItem: stack[stack.length - 1], origin: transaction.origin, type: undoing ? "redo" : "undo", changedParentTypes: transaction.changedParentTypes }, this];
        if (didAdd) {
          this.emit("stack-item-added", changeEvent);
        } else {
          this.emit("stack-item-updated", changeEvent);
        }
      };
      this.doc.on("afterTransaction", this.afterTransactionHandler);
      this.doc.on("destroy", () => {
        this.destroy();
      });
    }
    /**
     * @param {Array<AbstractType<any>> | AbstractType<any>} ytypes
     */
    addToScope(ytypes) {
      ytypes = isArray(ytypes) ? ytypes : [ytypes];
      ytypes.forEach((ytype) => {
        if (this.scope.every((yt) => yt !== ytype)) {
          this.scope.push(ytype);
        }
      });
    }
    /**
     * @param {any} origin
     */
    addTrackedOrigin(origin) {
      this.trackedOrigins.add(origin);
    }
    /**
     * @param {any} origin
     */
    removeTrackedOrigin(origin) {
      this.trackedOrigins.delete(origin);
    }
    clear(clearUndoStack = true, clearRedoStack = true) {
      if (clearUndoStack && this.canUndo() || clearRedoStack && this.canRedo()) {
        this.doc.transact((tr) => {
          if (clearUndoStack) {
            this.undoStack.forEach((item) => clearUndoManagerStackItem(tr, this, item));
            this.undoStack = [];
          }
          if (clearRedoStack) {
            this.redoStack.forEach((item) => clearUndoManagerStackItem(tr, this, item));
            this.redoStack = [];
          }
          this.emit("stack-cleared", [{ undoStackCleared: clearUndoStack, redoStackCleared: clearRedoStack }]);
        });
      }
    }
    /**
     * UndoManager merges Undo-StackItem if they are created within time-gap
     * smaller than `options.captureTimeout`. Call `um.stopCapturing()` so that the next
     * StackItem won't be merged.
     *
     *
     * @example
     *     // without stopCapturing
     *     ytext.insert(0, 'a')
     *     ytext.insert(1, 'b')
     *     um.undo()
     *     ytext.toString() // => '' (note that 'ab' was removed)
     *     // with stopCapturing
     *     ytext.insert(0, 'a')
     *     um.stopCapturing()
     *     ytext.insert(0, 'b')
     *     um.undo()
     *     ytext.toString() // => 'a' (note that only 'b' was removed)
     *
     */
    stopCapturing() {
      this.lastChange = 0;
    }
    /**
     * Undo last changes on type.
     *
     * @return {StackItem?} Returns StackItem if a change was applied
     */
    undo() {
      this.undoing = true;
      let res;
      try {
        res = popStackItem(this, this.undoStack, "undo");
      } finally {
        this.undoing = false;
      }
      return res;
    }
    /**
     * Redo last undo operation.
     *
     * @return {StackItem?} Returns StackItem if a change was applied
     */
    redo() {
      this.redoing = true;
      let res;
      try {
        res = popStackItem(this, this.redoStack, "redo");
      } finally {
        this.redoing = false;
      }
      return res;
    }
    /**
     * Are undo steps available?
     *
     * @return {boolean} `true` if undo is possible
     */
    canUndo() {
      return this.undoStack.length > 0;
    }
    /**
     * Are redo steps available?
     *
     * @return {boolean} `true` if redo is possible
     */
    canRedo() {
      return this.redoStack.length > 0;
    }
    destroy() {
      this.trackedOrigins.delete(this);
      this.doc.off("afterTransaction", this.afterTransactionHandler);
      super.destroy();
    }
  };
  function* lazyStructReaderGenerator(decoder) {
    const numOfStateUpdates = readVarUint(decoder.restDecoder);
    for (let i = 0; i < numOfStateUpdates; i++) {
      const numberOfStructs = readVarUint(decoder.restDecoder);
      const client = decoder.readClient();
      let clock = readVarUint(decoder.restDecoder);
      for (let i2 = 0; i2 < numberOfStructs; i2++) {
        const info = decoder.readInfo();
        if (info === 10) {
          const len = readVarUint(decoder.restDecoder);
          yield new Skip(createID(client, clock), len);
          clock += len;
        } else if ((BITS5 & info) !== 0) {
          const cantCopyParentInfo = (info & (BIT7 | BIT8)) === 0;
          const struct = new Item(
            createID(client, clock),
            null,
            // left
            (info & BIT8) === BIT8 ? decoder.readLeftID() : null,
            // origin
            null,
            // right
            (info & BIT7) === BIT7 ? decoder.readRightID() : null,
            // right origin
            // @ts-ignore Force writing a string here.
            cantCopyParentInfo ? decoder.readParentInfo() ? decoder.readString() : decoder.readLeftID() : null,
            // parent
            cantCopyParentInfo && (info & BIT6) === BIT6 ? decoder.readString() : null,
            // parentSub
            readItemContent(decoder, info)
            // item content
          );
          yield struct;
          clock += struct.length;
        } else {
          const len = decoder.readLen();
          yield new GC(createID(client, clock), len);
          clock += len;
        }
      }
    }
  }
  var LazyStructReader = class {
    /**
     * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
     * @param {boolean} filterSkips
     */
    constructor(decoder, filterSkips) {
      this.gen = lazyStructReaderGenerator(decoder);
      this.curr = null;
      this.done = false;
      this.filterSkips = filterSkips;
      this.next();
    }
    /**
     * @return {Item | GC | Skip |null}
     */
    next() {
      do {
        this.curr = this.gen.next().value || null;
      } while (this.filterSkips && this.curr !== null && this.curr.constructor === Skip);
      return this.curr;
    }
  };
  var logUpdate = (update) => logUpdateV2(update, UpdateDecoderV1);
  var logUpdateV2 = (update, YDecoder = UpdateDecoderV2) => {
    const structs = [];
    const updateDecoder = new YDecoder(createDecoder(update));
    const lazyDecoder = new LazyStructReader(updateDecoder, false);
    for (let curr = lazyDecoder.curr; curr !== null; curr = lazyDecoder.next()) {
      structs.push(curr);
    }
    print("Structs: ", structs);
    const ds = readDeleteSet(updateDecoder);
    print("DeleteSet: ", ds);
  };
  var decodeUpdate = (update) => decodeUpdateV2(update, UpdateDecoderV1);
  var decodeUpdateV2 = (update, YDecoder = UpdateDecoderV2) => {
    const structs = [];
    const updateDecoder = new YDecoder(createDecoder(update));
    const lazyDecoder = new LazyStructReader(updateDecoder, false);
    for (let curr = lazyDecoder.curr; curr !== null; curr = lazyDecoder.next()) {
      structs.push(curr);
    }
    return {
      structs,
      ds: readDeleteSet(updateDecoder)
    };
  };
  var LazyStructWriter = class {
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     */
    constructor(encoder) {
      this.currClient = 0;
      this.startClock = 0;
      this.written = 0;
      this.encoder = encoder;
      this.clientStructs = [];
    }
  };
  var mergeUpdates = (updates) => mergeUpdatesV2(updates, UpdateDecoderV1, UpdateEncoderV1);
  var encodeStateVectorFromUpdateV2 = (update, YEncoder = DSEncoderV2, YDecoder = UpdateDecoderV2) => {
    const encoder = new YEncoder();
    const updateDecoder = new LazyStructReader(new YDecoder(createDecoder(update)), false);
    let curr = updateDecoder.curr;
    if (curr !== null) {
      let size = 0;
      let currClient = curr.id.client;
      let stopCounting = curr.id.clock !== 0;
      let currClock = stopCounting ? 0 : curr.id.clock + curr.length;
      for (; curr !== null; curr = updateDecoder.next()) {
        if (currClient !== curr.id.client) {
          if (currClock !== 0) {
            size++;
            writeVarUint(encoder.restEncoder, currClient);
            writeVarUint(encoder.restEncoder, currClock);
          }
          currClient = curr.id.client;
          currClock = 0;
          stopCounting = curr.id.clock !== 0;
        }
        if (curr.constructor === Skip) {
          stopCounting = true;
        }
        if (!stopCounting) {
          currClock = curr.id.clock + curr.length;
        }
      }
      if (currClock !== 0) {
        size++;
        writeVarUint(encoder.restEncoder, currClient);
        writeVarUint(encoder.restEncoder, currClock);
      }
      const enc = createEncoder();
      writeVarUint(enc, size);
      writeBinaryEncoder(enc, encoder.restEncoder);
      encoder.restEncoder = enc;
      return encoder.toUint8Array();
    } else {
      writeVarUint(encoder.restEncoder, 0);
      return encoder.toUint8Array();
    }
  };
  var encodeStateVectorFromUpdate = (update) => encodeStateVectorFromUpdateV2(update, DSEncoderV1, UpdateDecoderV1);
  var parseUpdateMetaV2 = (update, YDecoder = UpdateDecoderV2) => {
    const from2 = /* @__PURE__ */ new Map();
    const to = /* @__PURE__ */ new Map();
    const updateDecoder = new LazyStructReader(new YDecoder(createDecoder(update)), false);
    let curr = updateDecoder.curr;
    if (curr !== null) {
      let currClient = curr.id.client;
      let currClock = curr.id.clock;
      from2.set(currClient, currClock);
      for (; curr !== null; curr = updateDecoder.next()) {
        if (currClient !== curr.id.client) {
          to.set(currClient, currClock);
          from2.set(curr.id.client, curr.id.clock);
          currClient = curr.id.client;
        }
        currClock = curr.id.clock + curr.length;
      }
      to.set(currClient, currClock);
    }
    return { from: from2, to };
  };
  var parseUpdateMeta = (update) => parseUpdateMetaV2(update, UpdateDecoderV1);
  var sliceStruct = (left, diff) => {
    if (left.constructor === GC) {
      const { client, clock } = left.id;
      return new GC(createID(client, clock + diff), left.length - diff);
    } else if (left.constructor === Skip) {
      const { client, clock } = left.id;
      return new Skip(createID(client, clock + diff), left.length - diff);
    } else {
      const leftItem = (
        /** @type {Item} */
        left
      );
      const { client, clock } = leftItem.id;
      return new Item(
        createID(client, clock + diff),
        null,
        createID(client, clock + diff - 1),
        null,
        leftItem.rightOrigin,
        leftItem.parent,
        leftItem.parentSub,
        leftItem.content.splice(diff)
      );
    }
  };
  var mergeUpdatesV2 = (updates, YDecoder = UpdateDecoderV2, YEncoder = UpdateEncoderV2) => {
    if (updates.length === 1) {
      return updates[0];
    }
    const updateDecoders = updates.map((update) => new YDecoder(createDecoder(update)));
    let lazyStructDecoders = updateDecoders.map((decoder) => new LazyStructReader(decoder, true));
    let currWrite = null;
    const updateEncoder = new YEncoder();
    const lazyStructEncoder = new LazyStructWriter(updateEncoder);
    while (true) {
      lazyStructDecoders = lazyStructDecoders.filter((dec) => dec.curr !== null);
      lazyStructDecoders.sort(
        /** @type {function(any,any):number} */
        (dec1, dec2) => {
          if (dec1.curr.id.client === dec2.curr.id.client) {
            const clockDiff = dec1.curr.id.clock - dec2.curr.id.clock;
            if (clockDiff === 0) {
              return dec1.curr.constructor === dec2.curr.constructor ? 0 : dec1.curr.constructor === Skip ? 1 : -1;
            } else {
              return clockDiff;
            }
          } else {
            return dec2.curr.id.client - dec1.curr.id.client;
          }
        }
      );
      if (lazyStructDecoders.length === 0) {
        break;
      }
      const currDecoder = lazyStructDecoders[0];
      const firstClient = (
        /** @type {Item | GC} */
        currDecoder.curr.id.client
      );
      if (currWrite !== null) {
        let curr = (
          /** @type {Item | GC | null} */
          currDecoder.curr
        );
        let iterated = false;
        while (curr !== null && curr.id.clock + curr.length <= currWrite.struct.id.clock + currWrite.struct.length && curr.id.client >= currWrite.struct.id.client) {
          curr = currDecoder.next();
          iterated = true;
        }
        if (curr === null || // current decoder is empty
        curr.id.client !== firstClient || // check whether there is another decoder that has has updates from `firstClient`
        iterated && curr.id.clock > currWrite.struct.id.clock + currWrite.struct.length) {
          continue;
        }
        if (firstClient !== currWrite.struct.id.client) {
          writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
          currWrite = { struct: curr, offset: 0 };
          currDecoder.next();
        } else {
          if (currWrite.struct.id.clock + currWrite.struct.length < curr.id.clock) {
            if (currWrite.struct.constructor === Skip) {
              currWrite.struct.length = curr.id.clock + curr.length - currWrite.struct.id.clock;
            } else {
              writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
              const diff = curr.id.clock - currWrite.struct.id.clock - currWrite.struct.length;
              const struct = new Skip(createID(firstClient, currWrite.struct.id.clock + currWrite.struct.length), diff);
              currWrite = { struct, offset: 0 };
            }
          } else {
            const diff = currWrite.struct.id.clock + currWrite.struct.length - curr.id.clock;
            if (diff > 0) {
              if (currWrite.struct.constructor === Skip) {
                currWrite.struct.length -= diff;
              } else {
                curr = sliceStruct(curr, diff);
              }
            }
            if (!currWrite.struct.mergeWith(
              /** @type {any} */
              curr
            )) {
              writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
              currWrite = { struct: curr, offset: 0 };
              currDecoder.next();
            }
          }
        }
      } else {
        currWrite = { struct: (
          /** @type {Item | GC} */
          currDecoder.curr
        ), offset: 0 };
        currDecoder.next();
      }
      for (let next = currDecoder.curr; next !== null && next.id.client === firstClient && next.id.clock === currWrite.struct.id.clock + currWrite.struct.length && next.constructor !== Skip; next = currDecoder.next()) {
        writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
        currWrite = { struct: next, offset: 0 };
      }
    }
    if (currWrite !== null) {
      writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
      currWrite = null;
    }
    finishLazyStructWriting(lazyStructEncoder);
    const dss = updateDecoders.map((decoder) => readDeleteSet(decoder));
    const ds = mergeDeleteSets(dss);
    writeDeleteSet(updateEncoder, ds);
    return updateEncoder.toUint8Array();
  };
  var diffUpdateV2 = (update, sv, YDecoder = UpdateDecoderV2, YEncoder = UpdateEncoderV2) => {
    const state = decodeStateVector(sv);
    const encoder = new YEncoder();
    const lazyStructWriter = new LazyStructWriter(encoder);
    const decoder = new YDecoder(createDecoder(update));
    const reader = new LazyStructReader(decoder, false);
    while (reader.curr) {
      const curr = reader.curr;
      const currClient = curr.id.client;
      const svClock = state.get(currClient) || 0;
      if (reader.curr.constructor === Skip) {
        reader.next();
        continue;
      }
      if (curr.id.clock + curr.length > svClock) {
        writeStructToLazyStructWriter(lazyStructWriter, curr, max(svClock - curr.id.clock, 0));
        reader.next();
        while (reader.curr && reader.curr.id.client === currClient) {
          writeStructToLazyStructWriter(lazyStructWriter, reader.curr, 0);
          reader.next();
        }
      } else {
        while (reader.curr && reader.curr.id.client === currClient && reader.curr.id.clock + reader.curr.length <= svClock) {
          reader.next();
        }
      }
    }
    finishLazyStructWriting(lazyStructWriter);
    const ds = readDeleteSet(decoder);
    writeDeleteSet(encoder, ds);
    return encoder.toUint8Array();
  };
  var diffUpdate = (update, sv) => diffUpdateV2(update, sv, UpdateDecoderV1, UpdateEncoderV1);
  var flushLazyStructWriter = (lazyWriter) => {
    if (lazyWriter.written > 0) {
      lazyWriter.clientStructs.push({ written: lazyWriter.written, restEncoder: toUint8Array(lazyWriter.encoder.restEncoder) });
      lazyWriter.encoder.restEncoder = createEncoder();
      lazyWriter.written = 0;
    }
  };
  var writeStructToLazyStructWriter = (lazyWriter, struct, offset) => {
    if (lazyWriter.written > 0 && lazyWriter.currClient !== struct.id.client) {
      flushLazyStructWriter(lazyWriter);
    }
    if (lazyWriter.written === 0) {
      lazyWriter.currClient = struct.id.client;
      lazyWriter.encoder.writeClient(struct.id.client);
      writeVarUint(lazyWriter.encoder.restEncoder, struct.id.clock + offset);
    }
    struct.write(lazyWriter.encoder, offset);
    lazyWriter.written++;
  };
  var finishLazyStructWriting = (lazyWriter) => {
    flushLazyStructWriter(lazyWriter);
    const restEncoder = lazyWriter.encoder.restEncoder;
    writeVarUint(restEncoder, lazyWriter.clientStructs.length);
    for (let i = 0; i < lazyWriter.clientStructs.length; i++) {
      const partStructs = lazyWriter.clientStructs[i];
      writeVarUint(restEncoder, partStructs.written);
      writeUint8Array(restEncoder, partStructs.restEncoder);
    }
  };
  var convertUpdateFormat = (update, blockTransformer, YDecoder, YEncoder) => {
    const updateDecoder = new YDecoder(createDecoder(update));
    const lazyDecoder = new LazyStructReader(updateDecoder, false);
    const updateEncoder = new YEncoder();
    const lazyWriter = new LazyStructWriter(updateEncoder);
    for (let curr = lazyDecoder.curr; curr !== null; curr = lazyDecoder.next()) {
      writeStructToLazyStructWriter(lazyWriter, blockTransformer(curr), 0);
    }
    finishLazyStructWriting(lazyWriter);
    const ds = readDeleteSet(updateDecoder);
    writeDeleteSet(updateEncoder, ds);
    return updateEncoder.toUint8Array();
  };
  var createObfuscator = ({ formatting = true, subdocs = true, yxml = true } = {}) => {
    let i = 0;
    const mapKeyCache = create();
    const nodeNameCache = create();
    const formattingKeyCache = create();
    const formattingValueCache = create();
    formattingValueCache.set(null, null);
    return (block) => {
      switch (block.constructor) {
        case GC:
        case Skip:
          return block;
        case Item: {
          const item = (
            /** @type {Item} */
            block
          );
          const content = item.content;
          switch (content.constructor) {
            case ContentDeleted:
              break;
            case ContentType: {
              if (yxml) {
                const type = (
                  /** @type {ContentType} */
                  content.type
                );
                if (type instanceof YXmlElement) {
                  type.nodeName = setIfUndefined(nodeNameCache, type.nodeName, () => "node-" + i);
                }
                if (type instanceof YXmlHook) {
                  type.hookName = setIfUndefined(nodeNameCache, type.hookName, () => "hook-" + i);
                }
              }
              break;
            }
            case ContentAny: {
              const c = (
                /** @type {ContentAny} */
                content
              );
              c.arr = c.arr.map(() => i);
              break;
            }
            case ContentBinary: {
              const c = (
                /** @type {ContentBinary} */
                content
              );
              c.content = new Uint8Array([i]);
              break;
            }
            case ContentDoc: {
              const c = (
                /** @type {ContentDoc} */
                content
              );
              if (subdocs) {
                c.opts = {};
                c.doc.guid = i + "";
              }
              break;
            }
            case ContentEmbed: {
              const c = (
                /** @type {ContentEmbed} */
                content
              );
              c.embed = {};
              break;
            }
            case ContentFormat: {
              const c = (
                /** @type {ContentFormat} */
                content
              );
              if (formatting) {
                c.key = setIfUndefined(formattingKeyCache, c.key, () => i + "");
                c.value = setIfUndefined(formattingValueCache, c.value, () => ({ i }));
              }
              break;
            }
            case ContentJSON: {
              const c = (
                /** @type {ContentJSON} */
                content
              );
              c.arr = c.arr.map(() => i);
              break;
            }
            case ContentString: {
              const c = (
                /** @type {ContentString} */
                content
              );
              c.str = repeat(i % 10 + "", c.str.length);
              break;
            }
            default:
              unexpectedCase();
          }
          if (item.parentSub) {
            item.parentSub = setIfUndefined(mapKeyCache, item.parentSub, () => i + "");
          }
          i++;
          return block;
        }
        default:
          unexpectedCase();
      }
    };
  };
  var obfuscateUpdate = (update, opts) => convertUpdateFormat(update, createObfuscator(opts), UpdateDecoderV1, UpdateEncoderV1);
  var obfuscateUpdateV2 = (update, opts) => convertUpdateFormat(update, createObfuscator(opts), UpdateDecoderV2, UpdateEncoderV2);
  var convertUpdateFormatV1ToV2 = (update) => convertUpdateFormat(update, id, UpdateDecoderV1, UpdateEncoderV2);
  var convertUpdateFormatV2ToV1 = (update) => convertUpdateFormat(update, id, UpdateDecoderV2, UpdateEncoderV1);
  var errorComputeChanges = "You must not compute changes after the event-handler fired.";
  var YEvent = class {
    /**
     * @param {T} target The changed type.
     * @param {Transaction} transaction
     */
    constructor(target, transaction) {
      this.target = target;
      this.currentTarget = target;
      this.transaction = transaction;
      this._changes = null;
      this._keys = null;
      this._delta = null;
      this._path = null;
    }
    /**
     * Computes the path from `y` to the changed type.
     *
     * @todo v14 should standardize on path: Array<{parent, index}> because that is easier to work with.
     *
     * The following property holds:
     * @example
     *   let type = y
     *   event.path.forEach(dir => {
     *     type = type.get(dir)
     *   })
     *   type === event.target // => true
     */
    get path() {
      return this._path || (this._path = getPathTo(this.currentTarget, this.target));
    }
    /**
     * Check if a struct is deleted by this event.
     *
     * In contrast to change.deleted, this method also returns true if the struct was added and then deleted.
     *
     * @param {AbstractStruct} struct
     * @return {boolean}
     */
    deletes(struct) {
      return isDeleted(this.transaction.deleteSet, struct.id);
    }
    /**
     * @type {Map<string, { action: 'add' | 'update' | 'delete', oldValue: any, newValue: any }>}
     */
    get keys() {
      if (this._keys === null) {
        if (this.transaction.doc._transactionCleanups.length === 0) {
          throw create3(errorComputeChanges);
        }
        const keys2 = /* @__PURE__ */ new Map();
        const target = this.target;
        const changed = (
          /** @type Set<string|null> */
          this.transaction.changed.get(target)
        );
        changed.forEach((key) => {
          if (key !== null) {
            const item = (
              /** @type {Item} */
              target._map.get(key)
            );
            let action;
            let oldValue;
            if (this.adds(item)) {
              let prev = item.left;
              while (prev !== null && this.adds(prev)) {
                prev = prev.left;
              }
              if (this.deletes(item)) {
                if (prev !== null && this.deletes(prev)) {
                  action = "delete";
                  oldValue = last(prev.content.getContent());
                } else {
                  return;
                }
              } else {
                if (prev !== null && this.deletes(prev)) {
                  action = "update";
                  oldValue = last(prev.content.getContent());
                } else {
                  action = "add";
                  oldValue = void 0;
                }
              }
            } else {
              if (this.deletes(item)) {
                action = "delete";
                oldValue = last(
                  /** @type {Item} */
                  item.content.getContent()
                );
              } else {
                return;
              }
            }
            keys2.set(key, { action, oldValue });
          }
        });
        this._keys = keys2;
      }
      return this._keys;
    }
    /**
     * This is a computed property. Note that this can only be safely computed during the
     * event call. Computing this property after other changes happened might result in
     * unexpected behavior (incorrect computation of deltas). A safe way to collect changes
     * is to store the `changes` or the `delta` object. Avoid storing the `transaction` object.
     *
     * @type {Array<{insert?: string | Array<any> | object | AbstractType<any>, retain?: number, delete?: number, attributes?: Object<string, any>}>}
     */
    get delta() {
      return this.changes.delta;
    }
    /**
     * Check if a struct is added by this event.
     *
     * In contrast to change.deleted, this method also returns true if the struct was added and then deleted.
     *
     * @param {AbstractStruct} struct
     * @return {boolean}
     */
    adds(struct) {
      return struct.id.clock >= (this.transaction.beforeState.get(struct.id.client) || 0);
    }
    /**
     * This is a computed property. Note that this can only be safely computed during the
     * event call. Computing this property after other changes happened might result in
     * unexpected behavior (incorrect computation of deltas). A safe way to collect changes
     * is to store the `changes` or the `delta` object. Avoid storing the `transaction` object.
     *
     * @type {{added:Set<Item>,deleted:Set<Item>,keys:Map<string,{action:'add'|'update'|'delete',oldValue:any}>,delta:Array<{insert?:Array<any>|string, delete?:number, retain?:number}>}}
     */
    get changes() {
      let changes = this._changes;
      if (changes === null) {
        if (this.transaction.doc._transactionCleanups.length === 0) {
          throw create3(errorComputeChanges);
        }
        const target = this.target;
        const added = create2();
        const deleted = create2();
        const delta = [];
        changes = {
          added,
          deleted,
          delta,
          keys: this.keys
        };
        const changed = (
          /** @type Set<string|null> */
          this.transaction.changed.get(target)
        );
        if (changed.has(null)) {
          let lastOp = null;
          const packOp = () => {
            if (lastOp) {
              delta.push(lastOp);
            }
          };
          for (let item = target._start; item !== null; item = item.right) {
            if (item.deleted) {
              if (this.deletes(item) && !this.adds(item)) {
                if (lastOp === null || lastOp.delete === void 0) {
                  packOp();
                  lastOp = { delete: 0 };
                }
                lastOp.delete += item.length;
                deleted.add(item);
              }
            } else {
              if (this.adds(item)) {
                if (lastOp === null || lastOp.insert === void 0) {
                  packOp();
                  lastOp = { insert: [] };
                }
                lastOp.insert = lastOp.insert.concat(item.content.getContent());
                added.add(item);
              } else {
                if (lastOp === null || lastOp.retain === void 0) {
                  packOp();
                  lastOp = { retain: 0 };
                }
                lastOp.retain += item.length;
              }
            }
          }
          if (lastOp !== null && lastOp.retain === void 0) {
            packOp();
          }
        }
        this._changes = changes;
      }
      return (
        /** @type {any} */
        changes
      );
    }
  };
  var getPathTo = (parent, child) => {
    const path = [];
    while (child._item !== null && child !== parent) {
      if (child._item.parentSub !== null) {
        path.unshift(child._item.parentSub);
      } else {
        let i = 0;
        let c = (
          /** @type {AbstractType<any>} */
          child._item.parent._start
        );
        while (c !== child._item && c !== null) {
          if (!c.deleted) {
            i++;
          }
          c = c.right;
        }
        path.unshift(i);
      }
      child = /** @type {AbstractType<any>} */
      child._item.parent;
    }
    return path;
  };
  var maxSearchMarker = 80;
  var globalSearchMarkerTimestamp = 0;
  var ArraySearchMarker = class {
    /**
     * @param {Item} p
     * @param {number} index
     */
    constructor(p, index) {
      p.marker = true;
      this.p = p;
      this.index = index;
      this.timestamp = globalSearchMarkerTimestamp++;
    }
  };
  var refreshMarkerTimestamp = (marker) => {
    marker.timestamp = globalSearchMarkerTimestamp++;
  };
  var overwriteMarker = (marker, p, index) => {
    marker.p.marker = false;
    marker.p = p;
    p.marker = true;
    marker.index = index;
    marker.timestamp = globalSearchMarkerTimestamp++;
  };
  var markPosition = (searchMarker, p, index) => {
    if (searchMarker.length >= maxSearchMarker) {
      const marker = searchMarker.reduce((a, b) => a.timestamp < b.timestamp ? a : b);
      overwriteMarker(marker, p, index);
      return marker;
    } else {
      const pm = new ArraySearchMarker(p, index);
      searchMarker.push(pm);
      return pm;
    }
  };
  var findMarker = (yarray, index) => {
    if (yarray._start === null || index === 0 || yarray._searchMarker === null) {
      return null;
    }
    const marker = yarray._searchMarker.length === 0 ? null : yarray._searchMarker.reduce((a, b) => abs(index - a.index) < abs(index - b.index) ? a : b);
    let p = yarray._start;
    let pindex = 0;
    if (marker !== null) {
      p = marker.p;
      pindex = marker.index;
      refreshMarkerTimestamp(marker);
    }
    while (p.right !== null && pindex < index) {
      if (!p.deleted && p.countable) {
        if (index < pindex + p.length) {
          break;
        }
        pindex += p.length;
      }
      p = p.right;
    }
    while (p.left !== null && pindex > index) {
      p = p.left;
      if (!p.deleted && p.countable) {
        pindex -= p.length;
      }
    }
    while (p.left !== null && p.left.id.client === p.id.client && p.left.id.clock + p.left.length === p.id.clock) {
      p = p.left;
      if (!p.deleted && p.countable) {
        pindex -= p.length;
      }
    }
    if (marker !== null && abs(marker.index - pindex) < /** @type {YText|YArray<any>} */
    p.parent.length / maxSearchMarker) {
      overwriteMarker(marker, p, pindex);
      return marker;
    } else {
      return markPosition(yarray._searchMarker, p, pindex);
    }
  };
  var updateMarkerChanges = (searchMarker, index, len) => {
    for (let i = searchMarker.length - 1; i >= 0; i--) {
      const m = searchMarker[i];
      if (len > 0) {
        let p = m.p;
        p.marker = false;
        while (p && (p.deleted || !p.countable)) {
          p = p.left;
          if (p && !p.deleted && p.countable) {
            m.index -= p.length;
          }
        }
        if (p === null || p.marker === true) {
          searchMarker.splice(i, 1);
          continue;
        }
        m.p = p;
        p.marker = true;
      }
      if (index < m.index || len > 0 && index === m.index) {
        m.index = max(index, m.index + len);
      }
    }
  };
  var getTypeChildren = (t) => {
    let s = t._start;
    const arr = [];
    while (s) {
      arr.push(s);
      s = s.right;
    }
    return arr;
  };
  var callTypeObservers = (type, transaction, event) => {
    const changedType = type;
    const changedParentTypes = transaction.changedParentTypes;
    while (true) {
      setIfUndefined(changedParentTypes, type, () => []).push(event);
      if (type._item === null) {
        break;
      }
      type = /** @type {AbstractType<any>} */
      type._item.parent;
    }
    callEventHandlerListeners(changedType._eH, event, transaction);
  };
  var AbstractType = class {
    constructor() {
      this._item = null;
      this._map = /* @__PURE__ */ new Map();
      this._start = null;
      this.doc = null;
      this._length = 0;
      this._eH = createEventHandler();
      this._dEH = createEventHandler();
      this._searchMarker = null;
    }
    /**
     * @return {AbstractType<any>|null}
     */
    get parent() {
      return this._item ? (
        /** @type {AbstractType<any>} */
        this._item.parent
      ) : null;
    }
    /**
     * Integrate this type into the Yjs instance.
     *
     * * Save this struct in the os
     * * This type is sent to other client
     * * Observer functions are fired
     *
     * @param {Doc} y The Yjs instance
     * @param {Item|null} item
     */
    _integrate(y, item) {
      this.doc = y;
      this._item = item;
    }
    /**
     * @return {AbstractType<EventType>}
     */
    _copy() {
      throw methodUnimplemented();
    }
    /**
     * @return {AbstractType<EventType>}
     */
    clone() {
      throw methodUnimplemented();
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} _encoder
     */
    _write(_encoder) {
    }
    /**
     * The first non-deleted item
     */
    get _first() {
      let n = this._start;
      while (n !== null && n.deleted) {
        n = n.right;
      }
      return n;
    }
    /**
     * Creates YEvent and calls all type observers.
     * Must be implemented by each type.
     *
     * @param {Transaction} transaction
     * @param {Set<null|string>} _parentSubs Keys changed on this type. `null` if list was modified.
     */
    _callObserver(transaction, _parentSubs) {
      if (!transaction.local && this._searchMarker) {
        this._searchMarker.length = 0;
      }
    }
    /**
     * Observe all events that are created on this type.
     *
     * @param {function(EventType, Transaction):void} f Observer function
     */
    observe(f) {
      addEventHandlerListener(this._eH, f);
    }
    /**
     * Observe all events that are created by this type and its children.
     *
     * @param {function(Array<YEvent<any>>,Transaction):void} f Observer function
     */
    observeDeep(f) {
      addEventHandlerListener(this._dEH, f);
    }
    /**
     * Unregister an observer function.
     *
     * @param {function(EventType,Transaction):void} f Observer function
     */
    unobserve(f) {
      removeEventHandlerListener(this._eH, f);
    }
    /**
     * Unregister an observer function.
     *
     * @param {function(Array<YEvent<any>>,Transaction):void} f Observer function
     */
    unobserveDeep(f) {
      removeEventHandlerListener(this._dEH, f);
    }
    /**
     * @abstract
     * @return {any}
     */
    toJSON() {
    }
  };
  var typeListSlice = (type, start, end) => {
    if (start < 0) {
      start = type._length + start;
    }
    if (end < 0) {
      end = type._length + end;
    }
    let len = end - start;
    const cs = [];
    let n = type._start;
    while (n !== null && len > 0) {
      if (n.countable && !n.deleted) {
        const c = n.content.getContent();
        if (c.length <= start) {
          start -= c.length;
        } else {
          for (let i = start; i < c.length && len > 0; i++) {
            cs.push(c[i]);
            len--;
          }
          start = 0;
        }
      }
      n = n.right;
    }
    return cs;
  };
  var typeListToArray = (type) => {
    const cs = [];
    let n = type._start;
    while (n !== null) {
      if (n.countable && !n.deleted) {
        const c = n.content.getContent();
        for (let i = 0; i < c.length; i++) {
          cs.push(c[i]);
        }
      }
      n = n.right;
    }
    return cs;
  };
  var typeListToArraySnapshot = (type, snapshot2) => {
    const cs = [];
    let n = type._start;
    while (n !== null) {
      if (n.countable && isVisible(n, snapshot2)) {
        const c = n.content.getContent();
        for (let i = 0; i < c.length; i++) {
          cs.push(c[i]);
        }
      }
      n = n.right;
    }
    return cs;
  };
  var typeListForEach = (type, f) => {
    let index = 0;
    let n = type._start;
    while (n !== null) {
      if (n.countable && !n.deleted) {
        const c = n.content.getContent();
        for (let i = 0; i < c.length; i++) {
          f(c[i], index++, type);
        }
      }
      n = n.right;
    }
  };
  var typeListMap = (type, f) => {
    const result = [];
    typeListForEach(type, (c, i) => {
      result.push(f(c, i, type));
    });
    return result;
  };
  var typeListCreateIterator = (type) => {
    let n = type._start;
    let currentContent = null;
    let currentContentIndex = 0;
    return {
      [Symbol.iterator]() {
        return this;
      },
      next: () => {
        if (currentContent === null) {
          while (n !== null && n.deleted) {
            n = n.right;
          }
          if (n === null) {
            return {
              done: true,
              value: void 0
            };
          }
          currentContent = n.content.getContent();
          currentContentIndex = 0;
          n = n.right;
        }
        const value = currentContent[currentContentIndex++];
        if (currentContent.length <= currentContentIndex) {
          currentContent = null;
        }
        return {
          done: false,
          value
        };
      }
    };
  };
  var typeListGet = (type, index) => {
    const marker = findMarker(type, index);
    let n = type._start;
    if (marker !== null) {
      n = marker.p;
      index -= marker.index;
    }
    for (; n !== null; n = n.right) {
      if (!n.deleted && n.countable) {
        if (index < n.length) {
          return n.content.getContent()[index];
        }
        index -= n.length;
      }
    }
  };
  var typeListInsertGenericsAfter = (transaction, parent, referenceItem, content) => {
    let left = referenceItem;
    const doc2 = transaction.doc;
    const ownClientId = doc2.clientID;
    const store2 = doc2.store;
    const right = referenceItem === null ? parent._start : referenceItem.right;
    let jsonContent = [];
    const packJsonContent = () => {
      if (jsonContent.length > 0) {
        left = new Item(createID(ownClientId, getState(store2, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentAny(jsonContent));
        left.integrate(transaction, 0);
        jsonContent = [];
      }
    };
    content.forEach((c) => {
      if (c === null) {
        jsonContent.push(c);
      } else {
        switch (c.constructor) {
          case Number:
          case Object:
          case Boolean:
          case Array:
          case String:
            jsonContent.push(c);
            break;
          default:
            packJsonContent();
            switch (c.constructor) {
              case Uint8Array:
              case ArrayBuffer:
                left = new Item(createID(ownClientId, getState(store2, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentBinary(new Uint8Array(
                  /** @type {Uint8Array} */
                  c
                )));
                left.integrate(transaction, 0);
                break;
              case Doc:
                left = new Item(createID(ownClientId, getState(store2, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentDoc(
                  /** @type {Doc} */
                  c
                ));
                left.integrate(transaction, 0);
                break;
              default:
                if (c instanceof AbstractType) {
                  left = new Item(createID(ownClientId, getState(store2, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentType(c));
                  left.integrate(transaction, 0);
                } else {
                  throw new Error("Unexpected content type in insert operation");
                }
            }
        }
      }
    });
    packJsonContent();
  };
  var lengthExceeded = create3("Length exceeded!");
  var typeListInsertGenerics = (transaction, parent, index, content) => {
    if (index > parent._length) {
      throw lengthExceeded;
    }
    if (index === 0) {
      if (parent._searchMarker) {
        updateMarkerChanges(parent._searchMarker, index, content.length);
      }
      return typeListInsertGenericsAfter(transaction, parent, null, content);
    }
    const startIndex = index;
    const marker = findMarker(parent, index);
    let n = parent._start;
    if (marker !== null) {
      n = marker.p;
      index -= marker.index;
      if (index === 0) {
        n = n.prev;
        index += n && n.countable && !n.deleted ? n.length : 0;
      }
    }
    for (; n !== null; n = n.right) {
      if (!n.deleted && n.countable) {
        if (index <= n.length) {
          if (index < n.length) {
            getItemCleanStart(transaction, createID(n.id.client, n.id.clock + index));
          }
          break;
        }
        index -= n.length;
      }
    }
    if (parent._searchMarker) {
      updateMarkerChanges(parent._searchMarker, startIndex, content.length);
    }
    return typeListInsertGenericsAfter(transaction, parent, n, content);
  };
  var typeListPushGenerics = (transaction, parent, content) => {
    const marker = (parent._searchMarker || []).reduce((maxMarker, currMarker) => currMarker.index > maxMarker.index ? currMarker : maxMarker, { index: 0, p: parent._start });
    let n = marker.p;
    if (n) {
      while (n.right) {
        n = n.right;
      }
    }
    return typeListInsertGenericsAfter(transaction, parent, n, content);
  };
  var typeListDelete = (transaction, parent, index, length3) => {
    if (length3 === 0) {
      return;
    }
    const startIndex = index;
    const startLength = length3;
    const marker = findMarker(parent, index);
    let n = parent._start;
    if (marker !== null) {
      n = marker.p;
      index -= marker.index;
    }
    for (; n !== null && index > 0; n = n.right) {
      if (!n.deleted && n.countable) {
        if (index < n.length) {
          getItemCleanStart(transaction, createID(n.id.client, n.id.clock + index));
        }
        index -= n.length;
      }
    }
    while (length3 > 0 && n !== null) {
      if (!n.deleted) {
        if (length3 < n.length) {
          getItemCleanStart(transaction, createID(n.id.client, n.id.clock + length3));
        }
        n.delete(transaction);
        length3 -= n.length;
      }
      n = n.right;
    }
    if (length3 > 0) {
      throw lengthExceeded;
    }
    if (parent._searchMarker) {
      updateMarkerChanges(
        parent._searchMarker,
        startIndex,
        -startLength + length3
        /* in case we remove the above exception */
      );
    }
  };
  var typeMapDelete = (transaction, parent, key) => {
    const c = parent._map.get(key);
    if (c !== void 0) {
      c.delete(transaction);
    }
  };
  var typeMapSet = (transaction, parent, key, value) => {
    const left = parent._map.get(key) || null;
    const doc2 = transaction.doc;
    const ownClientId = doc2.clientID;
    let content;
    if (value == null) {
      content = new ContentAny([value]);
    } else {
      switch (value.constructor) {
        case Number:
        case Object:
        case Boolean:
        case Array:
        case String:
          content = new ContentAny([value]);
          break;
        case Uint8Array:
          content = new ContentBinary(
            /** @type {Uint8Array} */
            value
          );
          break;
        case Doc:
          content = new ContentDoc(
            /** @type {Doc} */
            value
          );
          break;
        default:
          if (value instanceof AbstractType) {
            content = new ContentType(value);
          } else {
            throw new Error("Unexpected content type");
          }
      }
    }
    new Item(createID(ownClientId, getState(doc2.store, ownClientId)), left, left && left.lastId, null, null, parent, key, content).integrate(transaction, 0);
  };
  var typeMapGet = (parent, key) => {
    const val = parent._map.get(key);
    return val !== void 0 && !val.deleted ? val.content.getContent()[val.length - 1] : void 0;
  };
  var typeMapGetAll = (parent) => {
    const res = {};
    parent._map.forEach((value, key) => {
      if (!value.deleted) {
        res[key] = value.content.getContent()[value.length - 1];
      }
    });
    return res;
  };
  var typeMapHas = (parent, key) => {
    const val = parent._map.get(key);
    return val !== void 0 && !val.deleted;
  };
  var typeMapGetSnapshot = (parent, key, snapshot2) => {
    let v = parent._map.get(key) || null;
    while (v !== null && (!snapshot2.sv.has(v.id.client) || v.id.clock >= (snapshot2.sv.get(v.id.client) || 0))) {
      v = v.left;
    }
    return v !== null && isVisible(v, snapshot2) ? v.content.getContent()[v.length - 1] : void 0;
  };
  var createMapIterator = (map2) => iteratorFilter(
    map2.entries(),
    /** @param {any} entry */
    (entry) => !entry[1].deleted
  );
  var YArrayEvent = class extends YEvent {
    /**
     * @param {YArray<T>} yarray The changed type
     * @param {Transaction} transaction The transaction object
     */
    constructor(yarray, transaction) {
      super(yarray, transaction);
      this._transaction = transaction;
    }
  };
  var YArray = class _YArray extends AbstractType {
    constructor() {
      super();
      this._prelimContent = [];
      this._searchMarker = [];
    }
    /**
     * Construct a new YArray containing the specified items.
     * @template {Object<string,any>|Array<any>|number|null|string|Uint8Array} T
     * @param {Array<T>} items
     * @return {YArray<T>}
     */
    static from(items2) {
      const a = new _YArray();
      a.push(items2);
      return a;
    }
    /**
     * Integrate this type into the Yjs instance.
     *
     * * Save this struct in the os
     * * This type is sent to other client
     * * Observer functions are fired
     *
     * @param {Doc} y The Yjs instance
     * @param {Item} item
     */
    _integrate(y, item) {
      super._integrate(y, item);
      this.insert(
        0,
        /** @type {Array<any>} */
        this._prelimContent
      );
      this._prelimContent = null;
    }
    /**
     * @return {YArray<T>}
     */
    _copy() {
      return new _YArray();
    }
    /**
     * @return {YArray<T>}
     */
    clone() {
      const arr = new _YArray();
      arr.insert(0, this.toArray().map(
        (el) => el instanceof AbstractType ? (
          /** @type {typeof el} */
          el.clone()
        ) : el
      ));
      return arr;
    }
    get length() {
      return this._prelimContent === null ? this._length : this._prelimContent.length;
    }
    /**
     * Creates YArrayEvent and calls observers.
     *
     * @param {Transaction} transaction
     * @param {Set<null|string>} parentSubs Keys changed on this type. `null` if list was modified.
     */
    _callObserver(transaction, parentSubs) {
      super._callObserver(transaction, parentSubs);
      callTypeObservers(this, transaction, new YArrayEvent(this, transaction));
    }
    /**
     * Inserts new content at an index.
     *
     * Important: This function expects an array of content. Not just a content
     * object. The reason for this "weirdness" is that inserting several elements
     * is very efficient when it is done as a single operation.
     *
     * @example
     *  // Insert character 'a' at position 0
     *  yarray.insert(0, ['a'])
     *  // Insert numbers 1, 2 at position 1
     *  yarray.insert(1, [1, 2])
     *
     * @param {number} index The index to insert content at.
     * @param {Array<T>} content The array of content
     */
    insert(index, content) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeListInsertGenerics(
            transaction,
            this,
            index,
            /** @type {any} */
            content
          );
        });
      } else {
        this._prelimContent.splice(index, 0, ...content);
      }
    }
    /**
     * Appends content to this YArray.
     *
     * @param {Array<T>} content Array of content to append.
     *
     * @todo Use the following implementation in all types.
     */
    push(content) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeListPushGenerics(
            transaction,
            this,
            /** @type {any} */
            content
          );
        });
      } else {
        this._prelimContent.push(...content);
      }
    }
    /**
     * Preppends content to this YArray.
     *
     * @param {Array<T>} content Array of content to preppend.
     */
    unshift(content) {
      this.insert(0, content);
    }
    /**
     * Deletes elements starting from an index.
     *
     * @param {number} index Index at which to start deleting elements
     * @param {number} length The number of elements to remove. Defaults to 1.
     */
    delete(index, length3 = 1) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeListDelete(transaction, this, index, length3);
        });
      } else {
        this._prelimContent.splice(index, length3);
      }
    }
    /**
     * Returns the i-th element from a YArray.
     *
     * @param {number} index The index of the element to return from the YArray
     * @return {T}
     */
    get(index) {
      return typeListGet(this, index);
    }
    /**
     * Transforms this YArray to a JavaScript Array.
     *
     * @return {Array<T>}
     */
    toArray() {
      return typeListToArray(this);
    }
    /**
     * Transforms this YArray to a JavaScript Array.
     *
     * @param {number} [start]
     * @param {number} [end]
     * @return {Array<T>}
     */
    slice(start = 0, end = this.length) {
      return typeListSlice(this, start, end);
    }
    /**
     * Transforms this Shared Type to a JSON object.
     *
     * @return {Array<any>}
     */
    toJSON() {
      return this.map((c) => c instanceof AbstractType ? c.toJSON() : c);
    }
    /**
     * Returns an Array with the result of calling a provided function on every
     * element of this YArray.
     *
     * @template M
     * @param {function(T,number,YArray<T>):M} f Function that produces an element of the new Array
     * @return {Array<M>} A new array with each element being the result of the
     *                 callback function
     */
    map(f) {
      return typeListMap(
        this,
        /** @type {any} */
        f
      );
    }
    /**
     * Executes a provided function once on overy element of this YArray.
     *
     * @param {function(T,number,YArray<T>):void} f A function to execute on every element of this YArray.
     */
    forEach(f) {
      typeListForEach(this, f);
    }
    /**
     * @return {IterableIterator<T>}
     */
    [Symbol.iterator]() {
      return typeListCreateIterator(this);
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     */
    _write(encoder) {
      encoder.writeTypeRef(YArrayRefID);
    }
  };
  var readYArray = (_decoder) => new YArray();
  var YMapEvent = class extends YEvent {
    /**
     * @param {YMap<T>} ymap The YArray that changed.
     * @param {Transaction} transaction
     * @param {Set<any>} subs The keys that changed.
     */
    constructor(ymap, transaction, subs) {
      super(ymap, transaction);
      this.keysChanged = subs;
    }
  };
  var YMap = class _YMap extends AbstractType {
    /**
     *
     * @param {Iterable<readonly [string, any]>=} entries - an optional iterable to initialize the YMap
     */
    constructor(entries) {
      super();
      this._prelimContent = null;
      if (entries === void 0) {
        this._prelimContent = /* @__PURE__ */ new Map();
      } else {
        this._prelimContent = new Map(entries);
      }
    }
    /**
     * Integrate this type into the Yjs instance.
     *
     * * Save this struct in the os
     * * This type is sent to other client
     * * Observer functions are fired
     *
     * @param {Doc} y The Yjs instance
     * @param {Item} item
     */
    _integrate(y, item) {
      super._integrate(y, item);
      this._prelimContent.forEach((value, key) => {
        this.set(key, value);
      });
      this._prelimContent = null;
    }
    /**
     * @return {YMap<MapType>}
     */
    _copy() {
      return new _YMap();
    }
    /**
     * @return {YMap<MapType>}
     */
    clone() {
      const map2 = new _YMap();
      this.forEach((value, key) => {
        map2.set(key, value instanceof AbstractType ? (
          /** @type {typeof value} */
          value.clone()
        ) : value);
      });
      return map2;
    }
    /**
     * Creates YMapEvent and calls observers.
     *
     * @param {Transaction} transaction
     * @param {Set<null|string>} parentSubs Keys changed on this type. `null` if list was modified.
     */
    _callObserver(transaction, parentSubs) {
      callTypeObservers(this, transaction, new YMapEvent(this, transaction, parentSubs));
    }
    /**
     * Transforms this Shared Type to a JSON object.
     *
     * @return {Object<string,any>}
     */
    toJSON() {
      const map2 = {};
      this._map.forEach((item, key) => {
        if (!item.deleted) {
          const v = item.content.getContent()[item.length - 1];
          map2[key] = v instanceof AbstractType ? v.toJSON() : v;
        }
      });
      return map2;
    }
    /**
     * Returns the size of the YMap (count of key/value pairs)
     *
     * @return {number}
     */
    get size() {
      return [...createMapIterator(this._map)].length;
    }
    /**
     * Returns the keys for each element in the YMap Type.
     *
     * @return {IterableIterator<string>}
     */
    keys() {
      return iteratorMap(
        createMapIterator(this._map),
        /** @param {any} v */
        (v) => v[0]
      );
    }
    /**
     * Returns the values for each element in the YMap Type.
     *
     * @return {IterableIterator<any>}
     */
    values() {
      return iteratorMap(
        createMapIterator(this._map),
        /** @param {any} v */
        (v) => v[1].content.getContent()[v[1].length - 1]
      );
    }
    /**
     * Returns an Iterator of [key, value] pairs
     *
     * @return {IterableIterator<any>}
     */
    entries() {
      return iteratorMap(
        createMapIterator(this._map),
        /** @param {any} v */
        (v) => [v[0], v[1].content.getContent()[v[1].length - 1]]
      );
    }
    /**
     * Executes a provided function on once on every key-value pair.
     *
     * @param {function(MapType,string,YMap<MapType>):void} f A function to execute on every element of this YArray.
     */
    forEach(f) {
      this._map.forEach((item, key) => {
        if (!item.deleted) {
          f(item.content.getContent()[item.length - 1], key, this);
        }
      });
    }
    /**
     * Returns an Iterator of [key, value] pairs
     *
     * @return {IterableIterator<any>}
     */
    [Symbol.iterator]() {
      return this.entries();
    }
    /**
     * Remove a specified element from this YMap.
     *
     * @param {string} key The key of the element to remove.
     */
    delete(key) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeMapDelete(transaction, this, key);
        });
      } else {
        this._prelimContent.delete(key);
      }
    }
    /**
     * Adds or updates an element with a specified key and value.
     * @template {MapType} VAL
     *
     * @param {string} key The key of the element to add to this YMap
     * @param {VAL} value The value of the element to add
     * @return {VAL}
     */
    set(key, value) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeMapSet(
            transaction,
            this,
            key,
            /** @type {any} */
            value
          );
        });
      } else {
        this._prelimContent.set(key, value);
      }
      return value;
    }
    /**
     * Returns a specified element from this YMap.
     *
     * @param {string} key
     * @return {MapType|undefined}
     */
    get(key) {
      return (
        /** @type {any} */
        typeMapGet(this, key)
      );
    }
    /**
     * Returns a boolean indicating whether the specified key exists or not.
     *
     * @param {string} key The key to test.
     * @return {boolean}
     */
    has(key) {
      return typeMapHas(this, key);
    }
    /**
     * Removes all elements from this YMap.
     */
    clear() {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          this.forEach(function(_value, key, map2) {
            typeMapDelete(transaction, map2, key);
          });
        });
      } else {
        this._prelimContent.clear();
      }
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     */
    _write(encoder) {
      encoder.writeTypeRef(YMapRefID);
    }
  };
  var readYMap = (_decoder) => new YMap();
  var equalAttrs = (a, b) => a === b || typeof a === "object" && typeof b === "object" && a && b && equalFlat(a, b);
  var ItemTextListPosition = class {
    /**
     * @param {Item|null} left
     * @param {Item|null} right
     * @param {number} index
     * @param {Map<string,any>} currentAttributes
     */
    constructor(left, right, index, currentAttributes) {
      this.left = left;
      this.right = right;
      this.index = index;
      this.currentAttributes = currentAttributes;
    }
    /**
     * Only call this if you know that this.right is defined
     */
    forward() {
      if (this.right === null) {
        unexpectedCase();
      }
      switch (this.right.content.constructor) {
        case ContentFormat:
          if (!this.right.deleted) {
            updateCurrentAttributes(
              this.currentAttributes,
              /** @type {ContentFormat} */
              this.right.content
            );
          }
          break;
        default:
          if (!this.right.deleted) {
            this.index += this.right.length;
          }
          break;
      }
      this.left = this.right;
      this.right = this.right.right;
    }
  };
  var findNextPosition = (transaction, pos, count2) => {
    while (pos.right !== null && count2 > 0) {
      switch (pos.right.content.constructor) {
        case ContentFormat:
          if (!pos.right.deleted) {
            updateCurrentAttributes(
              pos.currentAttributes,
              /** @type {ContentFormat} */
              pos.right.content
            );
          }
          break;
        default:
          if (!pos.right.deleted) {
            if (count2 < pos.right.length) {
              getItemCleanStart(transaction, createID(pos.right.id.client, pos.right.id.clock + count2));
            }
            pos.index += pos.right.length;
            count2 -= pos.right.length;
          }
          break;
      }
      pos.left = pos.right;
      pos.right = pos.right.right;
    }
    return pos;
  };
  var findPosition = (transaction, parent, index) => {
    const currentAttributes = /* @__PURE__ */ new Map();
    const marker = findMarker(parent, index);
    if (marker) {
      const pos = new ItemTextListPosition(marker.p.left, marker.p, marker.index, currentAttributes);
      return findNextPosition(transaction, pos, index - marker.index);
    } else {
      const pos = new ItemTextListPosition(null, parent._start, 0, currentAttributes);
      return findNextPosition(transaction, pos, index);
    }
  };
  var insertNegatedAttributes = (transaction, parent, currPos, negatedAttributes) => {
    while (currPos.right !== null && (currPos.right.deleted === true || currPos.right.content.constructor === ContentFormat && equalAttrs(
      negatedAttributes.get(
        /** @type {ContentFormat} */
        currPos.right.content.key
      ),
      /** @type {ContentFormat} */
      currPos.right.content.value
    ))) {
      if (!currPos.right.deleted) {
        negatedAttributes.delete(
          /** @type {ContentFormat} */
          currPos.right.content.key
        );
      }
      currPos.forward();
    }
    const doc2 = transaction.doc;
    const ownClientId = doc2.clientID;
    negatedAttributes.forEach((val, key) => {
      const left = currPos.left;
      const right = currPos.right;
      const nextFormat = new Item(createID(ownClientId, getState(doc2.store, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentFormat(key, val));
      nextFormat.integrate(transaction, 0);
      currPos.right = nextFormat;
      currPos.forward();
    });
  };
  var updateCurrentAttributes = (currentAttributes, format) => {
    const { key, value } = format;
    if (value === null) {
      currentAttributes.delete(key);
    } else {
      currentAttributes.set(key, value);
    }
  };
  var minimizeAttributeChanges = (currPos, attributes) => {
    while (true) {
      if (currPos.right === null) {
        break;
      } else if (currPos.right.deleted || currPos.right.content.constructor === ContentFormat && equalAttrs(
        attributes[
          /** @type {ContentFormat} */
          currPos.right.content.key
        ] || null,
        /** @type {ContentFormat} */
        currPos.right.content.value
      )) ;
      else {
        break;
      }
      currPos.forward();
    }
  };
  var insertAttributes = (transaction, parent, currPos, attributes) => {
    const doc2 = transaction.doc;
    const ownClientId = doc2.clientID;
    const negatedAttributes = /* @__PURE__ */ new Map();
    for (const key in attributes) {
      const val = attributes[key];
      const currentVal = currPos.currentAttributes.get(key) || null;
      if (!equalAttrs(currentVal, val)) {
        negatedAttributes.set(key, currentVal);
        const { left, right } = currPos;
        currPos.right = new Item(createID(ownClientId, getState(doc2.store, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentFormat(key, val));
        currPos.right.integrate(transaction, 0);
        currPos.forward();
      }
    }
    return negatedAttributes;
  };
  var insertText = (transaction, parent, currPos, text2, attributes) => {
    currPos.currentAttributes.forEach((_val, key) => {
      if (attributes[key] === void 0) {
        attributes[key] = null;
      }
    });
    const doc2 = transaction.doc;
    const ownClientId = doc2.clientID;
    minimizeAttributeChanges(currPos, attributes);
    const negatedAttributes = insertAttributes(transaction, parent, currPos, attributes);
    const content = text2.constructor === String ? new ContentString(
      /** @type {string} */
      text2
    ) : text2 instanceof AbstractType ? new ContentType(text2) : new ContentEmbed(text2);
    let { left, right, index } = currPos;
    if (parent._searchMarker) {
      updateMarkerChanges(parent._searchMarker, currPos.index, content.getLength());
    }
    right = new Item(createID(ownClientId, getState(doc2.store, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, content);
    right.integrate(transaction, 0);
    currPos.right = right;
    currPos.index = index;
    currPos.forward();
    insertNegatedAttributes(transaction, parent, currPos, negatedAttributes);
  };
  var formatText = (transaction, parent, currPos, length3, attributes) => {
    const doc2 = transaction.doc;
    const ownClientId = doc2.clientID;
    minimizeAttributeChanges(currPos, attributes);
    const negatedAttributes = insertAttributes(transaction, parent, currPos, attributes);
    iterationLoop: while (currPos.right !== null && (length3 > 0 || negatedAttributes.size > 0 && (currPos.right.deleted || currPos.right.content.constructor === ContentFormat))) {
      if (!currPos.right.deleted) {
        switch (currPos.right.content.constructor) {
          case ContentFormat: {
            const { key, value } = (
              /** @type {ContentFormat} */
              currPos.right.content
            );
            const attr = attributes[key];
            if (attr !== void 0) {
              if (equalAttrs(attr, value)) {
                negatedAttributes.delete(key);
              } else {
                if (length3 === 0) {
                  break iterationLoop;
                }
                negatedAttributes.set(key, value);
              }
              currPos.right.delete(transaction);
            } else {
              currPos.currentAttributes.set(key, value);
            }
            break;
          }
          default:
            if (length3 < currPos.right.length) {
              getItemCleanStart(transaction, createID(currPos.right.id.client, currPos.right.id.clock + length3));
            }
            length3 -= currPos.right.length;
            break;
        }
      }
      currPos.forward();
    }
    if (length3 > 0) {
      let newlines = "";
      for (; length3 > 0; length3--) {
        newlines += "\n";
      }
      currPos.right = new Item(createID(ownClientId, getState(doc2.store, ownClientId)), currPos.left, currPos.left && currPos.left.lastId, currPos.right, currPos.right && currPos.right.id, parent, null, new ContentString(newlines));
      currPos.right.integrate(transaction, 0);
      currPos.forward();
    }
    insertNegatedAttributes(transaction, parent, currPos, negatedAttributes);
  };
  var cleanupFormattingGap = (transaction, start, curr, startAttributes, currAttributes) => {
    let end = start;
    const endFormats = create();
    while (end && (!end.countable || end.deleted)) {
      if (!end.deleted && end.content.constructor === ContentFormat) {
        const cf = (
          /** @type {ContentFormat} */
          end.content
        );
        endFormats.set(cf.key, cf);
      }
      end = end.right;
    }
    let cleanups = 0;
    let reachedCurr = false;
    while (start !== end) {
      if (curr === start) {
        reachedCurr = true;
      }
      if (!start.deleted) {
        const content = start.content;
        switch (content.constructor) {
          case ContentFormat: {
            const { key, value } = (
              /** @type {ContentFormat} */
              content
            );
            const startAttrValue = startAttributes.get(key) || null;
            if (endFormats.get(key) !== content || startAttrValue === value) {
              start.delete(transaction);
              cleanups++;
              if (!reachedCurr && (currAttributes.get(key) || null) === value && startAttrValue !== value) {
                if (startAttrValue === null) {
                  currAttributes.delete(key);
                } else {
                  currAttributes.set(key, startAttrValue);
                }
              }
            }
            if (!reachedCurr && !start.deleted) {
              updateCurrentAttributes(
                currAttributes,
                /** @type {ContentFormat} */
                content
              );
            }
            break;
          }
        }
      }
      start = /** @type {Item} */
      start.right;
    }
    return cleanups;
  };
  var cleanupContextlessFormattingGap = (transaction, item) => {
    while (item && item.right && (item.right.deleted || !item.right.countable)) {
      item = item.right;
    }
    const attrs = /* @__PURE__ */ new Set();
    while (item && (item.deleted || !item.countable)) {
      if (!item.deleted && item.content.constructor === ContentFormat) {
        const key = (
          /** @type {ContentFormat} */
          item.content.key
        );
        if (attrs.has(key)) {
          item.delete(transaction);
        } else {
          attrs.add(key);
        }
      }
      item = item.left;
    }
  };
  var cleanupYTextFormatting = (type) => {
    let res = 0;
    transact(
      /** @type {Doc} */
      type.doc,
      (transaction) => {
        let start = (
          /** @type {Item} */
          type._start
        );
        let end = type._start;
        let startAttributes = create();
        const currentAttributes = copy(startAttributes);
        while (end) {
          if (end.deleted === false) {
            switch (end.content.constructor) {
              case ContentFormat:
                updateCurrentAttributes(
                  currentAttributes,
                  /** @type {ContentFormat} */
                  end.content
                );
                break;
              default:
                res += cleanupFormattingGap(transaction, start, end, startAttributes, currentAttributes);
                startAttributes = copy(currentAttributes);
                start = end;
                break;
            }
          }
          end = end.right;
        }
      }
    );
    return res;
  };
  var cleanupYTextAfterTransaction = (transaction) => {
    const needFullCleanup = /* @__PURE__ */ new Set();
    const doc2 = transaction.doc;
    for (const [client, afterClock] of transaction.afterState.entries()) {
      const clock = transaction.beforeState.get(client) || 0;
      if (afterClock === clock) {
        continue;
      }
      iterateStructs(
        transaction,
        /** @type {Array<Item|GC>} */
        doc2.store.clients.get(client),
        clock,
        afterClock,
        (item) => {
          if (!item.deleted && /** @type {Item} */
          item.content.constructor === ContentFormat && item.constructor !== GC) {
            needFullCleanup.add(
              /** @type {any} */
              item.parent
            );
          }
        }
      );
    }
    transact(doc2, (t) => {
      iterateDeletedStructs(transaction, transaction.deleteSet, (item) => {
        if (item instanceof GC || !/** @type {YText} */
        item.parent._hasFormatting || needFullCleanup.has(
          /** @type {YText} */
          item.parent
        )) {
          return;
        }
        const parent = (
          /** @type {YText} */
          item.parent
        );
        if (item.content.constructor === ContentFormat) {
          needFullCleanup.add(parent);
        } else {
          cleanupContextlessFormattingGap(t, item);
        }
      });
      for (const yText of needFullCleanup) {
        cleanupYTextFormatting(yText);
      }
    });
  };
  var deleteText = (transaction, currPos, length3) => {
    const startLength = length3;
    const startAttrs = copy(currPos.currentAttributes);
    const start = currPos.right;
    while (length3 > 0 && currPos.right !== null) {
      if (currPos.right.deleted === false) {
        switch (currPos.right.content.constructor) {
          case ContentType:
          case ContentEmbed:
          case ContentString:
            if (length3 < currPos.right.length) {
              getItemCleanStart(transaction, createID(currPos.right.id.client, currPos.right.id.clock + length3));
            }
            length3 -= currPos.right.length;
            currPos.right.delete(transaction);
            break;
        }
      }
      currPos.forward();
    }
    if (start) {
      cleanupFormattingGap(transaction, start, currPos.right, startAttrs, currPos.currentAttributes);
    }
    const parent = (
      /** @type {AbstractType<any>} */
      /** @type {Item} */
      (currPos.left || currPos.right).parent
    );
    if (parent._searchMarker) {
      updateMarkerChanges(parent._searchMarker, currPos.index, -startLength + length3);
    }
    return currPos;
  };
  var YTextEvent = class extends YEvent {
    /**
     * @param {YText} ytext
     * @param {Transaction} transaction
     * @param {Set<any>} subs The keys that changed
     */
    constructor(ytext, transaction, subs) {
      super(ytext, transaction);
      this.childListChanged = false;
      this.keysChanged = /* @__PURE__ */ new Set();
      subs.forEach((sub) => {
        if (sub === null) {
          this.childListChanged = true;
        } else {
          this.keysChanged.add(sub);
        }
      });
    }
    /**
     * @type {{added:Set<Item>,deleted:Set<Item>,keys:Map<string,{action:'add'|'update'|'delete',oldValue:any}>,delta:Array<{insert?:Array<any>|string, delete?:number, retain?:number}>}}
     */
    get changes() {
      if (this._changes === null) {
        const changes = {
          keys: this.keys,
          delta: this.delta,
          added: /* @__PURE__ */ new Set(),
          deleted: /* @__PURE__ */ new Set()
        };
        this._changes = changes;
      }
      return (
        /** @type {any} */
        this._changes
      );
    }
    /**
     * Compute the changes in the delta format.
     * A {@link https://quilljs.com/docs/delta/|Quill Delta}) that represents the changes on the document.
     *
     * @type {Array<{insert?:string|object|AbstractType<any>, delete?:number, retain?:number, attributes?: Object<string,any>}>}
     *
     * @public
     */
    get delta() {
      if (this._delta === null) {
        const y = (
          /** @type {Doc} */
          this.target.doc
        );
        const delta = [];
        transact(y, (transaction) => {
          const currentAttributes = /* @__PURE__ */ new Map();
          const oldAttributes = /* @__PURE__ */ new Map();
          let item = this.target._start;
          let action = null;
          const attributes = {};
          let insert = "";
          let retain = 0;
          let deleteLen = 0;
          const addOp = () => {
            if (action !== null) {
              let op = null;
              switch (action) {
                case "delete":
                  if (deleteLen > 0) {
                    op = { delete: deleteLen };
                  }
                  deleteLen = 0;
                  break;
                case "insert":
                  if (typeof insert === "object" || insert.length > 0) {
                    op = { insert };
                    if (currentAttributes.size > 0) {
                      op.attributes = {};
                      currentAttributes.forEach((value, key) => {
                        if (value !== null) {
                          op.attributes[key] = value;
                        }
                      });
                    }
                  }
                  insert = "";
                  break;
                case "retain":
                  if (retain > 0) {
                    op = { retain };
                    if (!isEmpty(attributes)) {
                      op.attributes = assign({}, attributes);
                    }
                  }
                  retain = 0;
                  break;
              }
              if (op) delta.push(op);
              action = null;
            }
          };
          while (item !== null) {
            switch (item.content.constructor) {
              case ContentType:
              case ContentEmbed:
                if (this.adds(item)) {
                  if (!this.deletes(item)) {
                    addOp();
                    action = "insert";
                    insert = item.content.getContent()[0];
                    addOp();
                  }
                } else if (this.deletes(item)) {
                  if (action !== "delete") {
                    addOp();
                    action = "delete";
                  }
                  deleteLen += 1;
                } else if (!item.deleted) {
                  if (action !== "retain") {
                    addOp();
                    action = "retain";
                  }
                  retain += 1;
                }
                break;
              case ContentString:
                if (this.adds(item)) {
                  if (!this.deletes(item)) {
                    if (action !== "insert") {
                      addOp();
                      action = "insert";
                    }
                    insert += /** @type {ContentString} */
                    item.content.str;
                  }
                } else if (this.deletes(item)) {
                  if (action !== "delete") {
                    addOp();
                    action = "delete";
                  }
                  deleteLen += item.length;
                } else if (!item.deleted) {
                  if (action !== "retain") {
                    addOp();
                    action = "retain";
                  }
                  retain += item.length;
                }
                break;
              case ContentFormat: {
                const { key, value } = (
                  /** @type {ContentFormat} */
                  item.content
                );
                if (this.adds(item)) {
                  if (!this.deletes(item)) {
                    const curVal = currentAttributes.get(key) || null;
                    if (!equalAttrs(curVal, value)) {
                      if (action === "retain") {
                        addOp();
                      }
                      if (equalAttrs(value, oldAttributes.get(key) || null)) {
                        delete attributes[key];
                      } else {
                        attributes[key] = value;
                      }
                    } else if (value !== null) {
                      item.delete(transaction);
                    }
                  }
                } else if (this.deletes(item)) {
                  oldAttributes.set(key, value);
                  const curVal = currentAttributes.get(key) || null;
                  if (!equalAttrs(curVal, value)) {
                    if (action === "retain") {
                      addOp();
                    }
                    attributes[key] = curVal;
                  }
                } else if (!item.deleted) {
                  oldAttributes.set(key, value);
                  const attr = attributes[key];
                  if (attr !== void 0) {
                    if (!equalAttrs(attr, value)) {
                      if (action === "retain") {
                        addOp();
                      }
                      if (value === null) {
                        delete attributes[key];
                      } else {
                        attributes[key] = value;
                      }
                    } else if (attr !== null) {
                      item.delete(transaction);
                    }
                  }
                }
                if (!item.deleted) {
                  if (action === "insert") {
                    addOp();
                  }
                  updateCurrentAttributes(
                    currentAttributes,
                    /** @type {ContentFormat} */
                    item.content
                  );
                }
                break;
              }
            }
            item = item.right;
          }
          addOp();
          while (delta.length > 0) {
            const lastOp = delta[delta.length - 1];
            if (lastOp.retain !== void 0 && lastOp.attributes === void 0) {
              delta.pop();
            } else {
              break;
            }
          }
        });
        this._delta = delta;
      }
      return (
        /** @type {any} */
        this._delta
      );
    }
  };
  var YText = class _YText extends AbstractType {
    /**
     * @param {String} [string] The initial value of the YText.
     */
    constructor(string) {
      super();
      this._pending = string !== void 0 ? [() => this.insert(0, string)] : [];
      this._searchMarker = [];
      this._hasFormatting = false;
    }
    /**
     * Number of characters of this text type.
     *
     * @type {number}
     */
    get length() {
      return this._length;
    }
    /**
     * @param {Doc} y
     * @param {Item} item
     */
    _integrate(y, item) {
      super._integrate(y, item);
      try {
        this._pending.forEach((f) => f());
      } catch (e) {
        console.error(e);
      }
      this._pending = null;
    }
    _copy() {
      return new _YText();
    }
    /**
     * @return {YText}
     */
    clone() {
      const text2 = new _YText();
      text2.applyDelta(this.toDelta());
      return text2;
    }
    /**
     * Creates YTextEvent and calls observers.
     *
     * @param {Transaction} transaction
     * @param {Set<null|string>} parentSubs Keys changed on this type. `null` if list was modified.
     */
    _callObserver(transaction, parentSubs) {
      super._callObserver(transaction, parentSubs);
      const event = new YTextEvent(this, transaction, parentSubs);
      callTypeObservers(this, transaction, event);
      if (!transaction.local && this._hasFormatting) {
        transaction._needFormattingCleanup = true;
      }
    }
    /**
     * Returns the unformatted string representation of this YText type.
     *
     * @public
     */
    toString() {
      let str = "";
      let n = this._start;
      while (n !== null) {
        if (!n.deleted && n.countable && n.content.constructor === ContentString) {
          str += /** @type {ContentString} */
          n.content.str;
        }
        n = n.right;
      }
      return str;
    }
    /**
     * Returns the unformatted string representation of this YText type.
     *
     * @return {string}
     * @public
     */
    toJSON() {
      return this.toString();
    }
    /**
     * Apply a {@link Delta} on this shared YText type.
     *
     * @param {any} delta The changes to apply on this element.
     * @param {object}  opts
     * @param {boolean} [opts.sanitize] Sanitize input delta. Removes ending newlines if set to true.
     *
     *
     * @public
     */
    applyDelta(delta, { sanitize = true } = {}) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          const currPos = new ItemTextListPosition(null, this._start, 0, /* @__PURE__ */ new Map());
          for (let i = 0; i < delta.length; i++) {
            const op = delta[i];
            if (op.insert !== void 0) {
              const ins = !sanitize && typeof op.insert === "string" && i === delta.length - 1 && currPos.right === null && op.insert.slice(-1) === "\n" ? op.insert.slice(0, -1) : op.insert;
              if (typeof ins !== "string" || ins.length > 0) {
                insertText(transaction, this, currPos, ins, op.attributes || {});
              }
            } else if (op.retain !== void 0) {
              formatText(transaction, this, currPos, op.retain, op.attributes || {});
            } else if (op.delete !== void 0) {
              deleteText(transaction, currPos, op.delete);
            }
          }
        });
      } else {
        this._pending.push(() => this.applyDelta(delta));
      }
    }
    /**
     * Returns the Delta representation of this YText type.
     *
     * @param {Snapshot} [snapshot]
     * @param {Snapshot} [prevSnapshot]
     * @param {function('removed' | 'added', ID):any} [computeYChange]
     * @return {any} The Delta representation of this type.
     *
     * @public
     */
    toDelta(snapshot2, prevSnapshot, computeYChange) {
      const ops = [];
      const currentAttributes = /* @__PURE__ */ new Map();
      const doc2 = (
        /** @type {Doc} */
        this.doc
      );
      let str = "";
      let n = this._start;
      function packStr() {
        if (str.length > 0) {
          const attributes = {};
          let addAttributes = false;
          currentAttributes.forEach((value, key) => {
            addAttributes = true;
            attributes[key] = value;
          });
          const op = { insert: str };
          if (addAttributes) {
            op.attributes = attributes;
          }
          ops.push(op);
          str = "";
        }
      }
      const computeDelta = () => {
        while (n !== null) {
          if (isVisible(n, snapshot2) || prevSnapshot !== void 0 && isVisible(n, prevSnapshot)) {
            switch (n.content.constructor) {
              case ContentString: {
                const cur = currentAttributes.get("ychange");
                if (snapshot2 !== void 0 && !isVisible(n, snapshot2)) {
                  if (cur === void 0 || cur.user !== n.id.client || cur.type !== "removed") {
                    packStr();
                    currentAttributes.set("ychange", computeYChange ? computeYChange("removed", n.id) : { type: "removed" });
                  }
                } else if (prevSnapshot !== void 0 && !isVisible(n, prevSnapshot)) {
                  if (cur === void 0 || cur.user !== n.id.client || cur.type !== "added") {
                    packStr();
                    currentAttributes.set("ychange", computeYChange ? computeYChange("added", n.id) : { type: "added" });
                  }
                } else if (cur !== void 0) {
                  packStr();
                  currentAttributes.delete("ychange");
                }
                str += /** @type {ContentString} */
                n.content.str;
                break;
              }
              case ContentType:
              case ContentEmbed: {
                packStr();
                const op = {
                  insert: n.content.getContent()[0]
                };
                if (currentAttributes.size > 0) {
                  const attrs = (
                    /** @type {Object<string,any>} */
                    {}
                  );
                  op.attributes = attrs;
                  currentAttributes.forEach((value, key) => {
                    attrs[key] = value;
                  });
                }
                ops.push(op);
                break;
              }
              case ContentFormat:
                if (isVisible(n, snapshot2)) {
                  packStr();
                  updateCurrentAttributes(
                    currentAttributes,
                    /** @type {ContentFormat} */
                    n.content
                  );
                }
                break;
            }
          }
          n = n.right;
        }
        packStr();
      };
      if (snapshot2 || prevSnapshot) {
        transact(doc2, (transaction) => {
          if (snapshot2) {
            splitSnapshotAffectedStructs(transaction, snapshot2);
          }
          if (prevSnapshot) {
            splitSnapshotAffectedStructs(transaction, prevSnapshot);
          }
          computeDelta();
        }, "cleanup");
      } else {
        computeDelta();
      }
      return ops;
    }
    /**
     * Insert text at a given index.
     *
     * @param {number} index The index at which to start inserting.
     * @param {String} text The text to insert at the specified position.
     * @param {TextAttributes} [attributes] Optionally define some formatting
     *                                    information to apply on the inserted
     *                                    Text.
     * @public
     */
    insert(index, text2, attributes) {
      if (text2.length <= 0) {
        return;
      }
      const y = this.doc;
      if (y !== null) {
        transact(y, (transaction) => {
          const pos = findPosition(transaction, this, index);
          if (!attributes) {
            attributes = {};
            pos.currentAttributes.forEach((v, k) => {
              attributes[k] = v;
            });
          }
          insertText(transaction, this, pos, text2, attributes);
        });
      } else {
        this._pending.push(() => this.insert(index, text2, attributes));
      }
    }
    /**
     * Inserts an embed at a index.
     *
     * @param {number} index The index to insert the embed at.
     * @param {Object | AbstractType<any>} embed The Object that represents the embed.
     * @param {TextAttributes} attributes Attribute information to apply on the
     *                                    embed
     *
     * @public
     */
    insertEmbed(index, embed, attributes = {}) {
      const y = this.doc;
      if (y !== null) {
        transact(y, (transaction) => {
          const pos = findPosition(transaction, this, index);
          insertText(transaction, this, pos, embed, attributes);
        });
      } else {
        this._pending.push(() => this.insertEmbed(index, embed, attributes));
      }
    }
    /**
     * Deletes text starting from an index.
     *
     * @param {number} index Index at which to start deleting.
     * @param {number} length The number of characters to remove. Defaults to 1.
     *
     * @public
     */
    delete(index, length3) {
      if (length3 === 0) {
        return;
      }
      const y = this.doc;
      if (y !== null) {
        transact(y, (transaction) => {
          deleteText(transaction, findPosition(transaction, this, index), length3);
        });
      } else {
        this._pending.push(() => this.delete(index, length3));
      }
    }
    /**
     * Assigns properties to a range of text.
     *
     * @param {number} index The position where to start formatting.
     * @param {number} length The amount of characters to assign properties to.
     * @param {TextAttributes} attributes Attribute information to apply on the
     *                                    text.
     *
     * @public
     */
    format(index, length3, attributes) {
      if (length3 === 0) {
        return;
      }
      const y = this.doc;
      if (y !== null) {
        transact(y, (transaction) => {
          const pos = findPosition(transaction, this, index);
          if (pos.right === null) {
            return;
          }
          formatText(transaction, this, pos, length3, attributes);
        });
      } else {
        this._pending.push(() => this.format(index, length3, attributes));
      }
    }
    /**
     * Removes an attribute.
     *
     * @note Xml-Text nodes don't have attributes. You can use this feature to assign properties to complete text-blocks.
     *
     * @param {String} attributeName The attribute name that is to be removed.
     *
     * @public
     */
    removeAttribute(attributeName) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeMapDelete(transaction, this, attributeName);
        });
      } else {
        this._pending.push(() => this.removeAttribute(attributeName));
      }
    }
    /**
     * Sets or updates an attribute.
     *
     * @note Xml-Text nodes don't have attributes. You can use this feature to assign properties to complete text-blocks.
     *
     * @param {String} attributeName The attribute name that is to be set.
     * @param {any} attributeValue The attribute value that is to be set.
     *
     * @public
     */
    setAttribute(attributeName, attributeValue) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeMapSet(transaction, this, attributeName, attributeValue);
        });
      } else {
        this._pending.push(() => this.setAttribute(attributeName, attributeValue));
      }
    }
    /**
     * Returns an attribute value that belongs to the attribute name.
     *
     * @note Xml-Text nodes don't have attributes. You can use this feature to assign properties to complete text-blocks.
     *
     * @param {String} attributeName The attribute name that identifies the
     *                               queried value.
     * @return {any} The queried attribute value.
     *
     * @public
     */
    getAttribute(attributeName) {
      return (
        /** @type {any} */
        typeMapGet(this, attributeName)
      );
    }
    /**
     * Returns all attribute name/value pairs in a JSON Object.
     *
     * @note Xml-Text nodes don't have attributes. You can use this feature to assign properties to complete text-blocks.
     *
     * @return {Object<string, any>} A JSON Object that describes the attributes.
     *
     * @public
     */
    getAttributes() {
      return typeMapGetAll(this);
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     */
    _write(encoder) {
      encoder.writeTypeRef(YTextRefID);
    }
  };
  var readYText = (_decoder) => new YText();
  var YXmlTreeWalker = class {
    /**
     * @param {YXmlFragment | YXmlElement} root
     * @param {function(AbstractType<any>):boolean} [f]
     */
    constructor(root, f = () => true) {
      this._filter = f;
      this._root = root;
      this._currentNode = /** @type {Item} */
      root._start;
      this._firstCall = true;
    }
    [Symbol.iterator]() {
      return this;
    }
    /**
     * Get the next node.
     *
     * @return {IteratorResult<YXmlElement|YXmlText|YXmlHook>} The next node.
     *
     * @public
     */
    next() {
      let n = this._currentNode;
      let type = n && n.content && /** @type {any} */
      n.content.type;
      if (n !== null && (!this._firstCall || n.deleted || !this._filter(type))) {
        do {
          type = /** @type {any} */
          n.content.type;
          if (!n.deleted && (type.constructor === YXmlElement || type.constructor === YXmlFragment) && type._start !== null) {
            n = type._start;
          } else {
            while (n !== null) {
              if (n.right !== null) {
                n = n.right;
                break;
              } else if (n.parent === this._root) {
                n = null;
              } else {
                n = /** @type {AbstractType<any>} */
                n.parent._item;
              }
            }
          }
        } while (n !== null && (n.deleted || !this._filter(
          /** @type {ContentType} */
          n.content.type
        )));
      }
      this._firstCall = false;
      if (n === null) {
        return { value: void 0, done: true };
      }
      this._currentNode = n;
      return { value: (
        /** @type {any} */
        n.content.type
      ), done: false };
    }
  };
  var YXmlFragment = class _YXmlFragment extends AbstractType {
    constructor() {
      super();
      this._prelimContent = [];
    }
    /**
     * @type {YXmlElement|YXmlText|null}
     */
    get firstChild() {
      const first = this._first;
      return first ? first.content.getContent()[0] : null;
    }
    /**
     * Integrate this type into the Yjs instance.
     *
     * * Save this struct in the os
     * * This type is sent to other client
     * * Observer functions are fired
     *
     * @param {Doc} y The Yjs instance
     * @param {Item} item
     */
    _integrate(y, item) {
      super._integrate(y, item);
      this.insert(
        0,
        /** @type {Array<any>} */
        this._prelimContent
      );
      this._prelimContent = null;
    }
    _copy() {
      return new _YXmlFragment();
    }
    /**
     * @return {YXmlFragment}
     */
    clone() {
      const el = new _YXmlFragment();
      el.insert(0, this.toArray().map((item) => item instanceof AbstractType ? item.clone() : item));
      return el;
    }
    get length() {
      return this._prelimContent === null ? this._length : this._prelimContent.length;
    }
    /**
     * Create a subtree of childNodes.
     *
     * @example
     * const walker = elem.createTreeWalker(dom => dom.nodeName === 'div')
     * for (let node in walker) {
     *   // `node` is a div node
     *   nop(node)
     * }
     *
     * @param {function(AbstractType<any>):boolean} filter Function that is called on each child element and
     *                          returns a Boolean indicating whether the child
     *                          is to be included in the subtree.
     * @return {YXmlTreeWalker} A subtree and a position within it.
     *
     * @public
     */
    createTreeWalker(filter) {
      return new YXmlTreeWalker(this, filter);
    }
    /**
     * Returns the first YXmlElement that matches the query.
     * Similar to DOM's {@link querySelector}.
     *
     * Query support:
     *   - tagname
     * TODO:
     *   - id
     *   - attribute
     *
     * @param {CSS_Selector} query The query on the children.
     * @return {YXmlElement|YXmlText|YXmlHook|null} The first element that matches the query or null.
     *
     * @public
     */
    querySelector(query) {
      query = query.toUpperCase();
      const iterator = new YXmlTreeWalker(this, (element2) => element2.nodeName && element2.nodeName.toUpperCase() === query);
      const next = iterator.next();
      if (next.done) {
        return null;
      } else {
        return next.value;
      }
    }
    /**
     * Returns all YXmlElements that match the query.
     * Similar to Dom's {@link querySelectorAll}.
     *
     * @todo Does not yet support all queries. Currently only query by tagName.
     *
     * @param {CSS_Selector} query The query on the children
     * @return {Array<YXmlElement|YXmlText|YXmlHook|null>} The elements that match this query.
     *
     * @public
     */
    querySelectorAll(query) {
      query = query.toUpperCase();
      return from(new YXmlTreeWalker(this, (element2) => element2.nodeName && element2.nodeName.toUpperCase() === query));
    }
    /**
     * Creates YXmlEvent and calls observers.
     *
     * @param {Transaction} transaction
     * @param {Set<null|string>} parentSubs Keys changed on this type. `null` if list was modified.
     */
    _callObserver(transaction, parentSubs) {
      callTypeObservers(this, transaction, new YXmlEvent(this, parentSubs, transaction));
    }
    /**
     * Get the string representation of all the children of this YXmlFragment.
     *
     * @return {string} The string representation of all children.
     */
    toString() {
      return typeListMap(this, (xml) => xml.toString()).join("");
    }
    /**
     * @return {string}
     */
    toJSON() {
      return this.toString();
    }
    /**
     * Creates a Dom Element that mirrors this YXmlElement.
     *
     * @param {Document} [_document=document] The document object (you must define
     *                                        this when calling this method in
     *                                        nodejs)
     * @param {Object<string, any>} [hooks={}] Optional property to customize how hooks
     *                                             are presented in the DOM
     * @param {any} [binding] You should not set this property. This is
     *                               used if DomBinding wants to create a
     *                               association to the created DOM type.
     * @return {Node} The {@link https://developer.mozilla.org/en-US/docs/Web/API/Element|Dom Element}
     *
     * @public
     */
    toDOM(_document = document, hooks = {}, binding) {
      const fragment = _document.createDocumentFragment();
      if (binding !== void 0) {
        binding._createAssociation(fragment, this);
      }
      typeListForEach(this, (xmlType) => {
        fragment.insertBefore(xmlType.toDOM(_document, hooks, binding), null);
      });
      return fragment;
    }
    /**
     * Inserts new content at an index.
     *
     * @example
     *  // Insert character 'a' at position 0
     *  xml.insert(0, [new Y.XmlText('text')])
     *
     * @param {number} index The index to insert content at
     * @param {Array<YXmlElement|YXmlText>} content The array of content
     */
    insert(index, content) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeListInsertGenerics(transaction, this, index, content);
        });
      } else {
        this._prelimContent.splice(index, 0, ...content);
      }
    }
    /**
     * Inserts new content at an index.
     *
     * @example
     *  // Insert character 'a' at position 0
     *  xml.insert(0, [new Y.XmlText('text')])
     *
     * @param {null|Item|YXmlElement|YXmlText} ref The index to insert content at
     * @param {Array<YXmlElement|YXmlText>} content The array of content
     */
    insertAfter(ref, content) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          const refItem = ref && ref instanceof AbstractType ? ref._item : ref;
          typeListInsertGenericsAfter(transaction, this, refItem, content);
        });
      } else {
        const pc = (
          /** @type {Array<any>} */
          this._prelimContent
        );
        const index = ref === null ? 0 : pc.findIndex((el) => el === ref) + 1;
        if (index === 0 && ref !== null) {
          throw create3("Reference item not found");
        }
        pc.splice(index, 0, ...content);
      }
    }
    /**
     * Deletes elements starting from an index.
     *
     * @param {number} index Index at which to start deleting elements
     * @param {number} [length=1] The number of elements to remove. Defaults to 1.
     */
    delete(index, length3 = 1) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeListDelete(transaction, this, index, length3);
        });
      } else {
        this._prelimContent.splice(index, length3);
      }
    }
    /**
     * Transforms this YArray to a JavaScript Array.
     *
     * @return {Array<YXmlElement|YXmlText|YXmlHook>}
     */
    toArray() {
      return typeListToArray(this);
    }
    /**
     * Appends content to this YArray.
     *
     * @param {Array<YXmlElement|YXmlText>} content Array of content to append.
     */
    push(content) {
      this.insert(this.length, content);
    }
    /**
     * Preppends content to this YArray.
     *
     * @param {Array<YXmlElement|YXmlText>} content Array of content to preppend.
     */
    unshift(content) {
      this.insert(0, content);
    }
    /**
     * Returns the i-th element from a YArray.
     *
     * @param {number} index The index of the element to return from the YArray
     * @return {YXmlElement|YXmlText}
     */
    get(index) {
      return typeListGet(this, index);
    }
    /**
     * Transforms this YArray to a JavaScript Array.
     *
     * @param {number} [start]
     * @param {number} [end]
     * @return {Array<YXmlElement|YXmlText>}
     */
    slice(start = 0, end = this.length) {
      return typeListSlice(this, start, end);
    }
    /**
     * Executes a provided function on once on overy child element.
     *
     * @param {function(YXmlElement|YXmlText,number, typeof self):void} f A function to execute on every element of this YArray.
     */
    forEach(f) {
      typeListForEach(this, f);
    }
    /**
     * Transform the properties of this type to binary and write it to an
     * BinaryEncoder.
     *
     * This is called when this Item is sent to a remote peer.
     *
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder The encoder to write data to.
     */
    _write(encoder) {
      encoder.writeTypeRef(YXmlFragmentRefID);
    }
  };
  var readYXmlFragment = (_decoder) => new YXmlFragment();
  var YXmlElement = class _YXmlElement extends YXmlFragment {
    constructor(nodeName = "UNDEFINED") {
      super();
      this.nodeName = nodeName;
      this._prelimAttrs = /* @__PURE__ */ new Map();
    }
    /**
     * @type {YXmlElement|YXmlText|null}
     */
    get nextSibling() {
      const n = this._item ? this._item.next : null;
      return n ? (
        /** @type {YXmlElement|YXmlText} */
        /** @type {ContentType} */
        n.content.type
      ) : null;
    }
    /**
     * @type {YXmlElement|YXmlText|null}
     */
    get prevSibling() {
      const n = this._item ? this._item.prev : null;
      return n ? (
        /** @type {YXmlElement|YXmlText} */
        /** @type {ContentType} */
        n.content.type
      ) : null;
    }
    /**
     * Integrate this type into the Yjs instance.
     *
     * * Save this struct in the os
     * * This type is sent to other client
     * * Observer functions are fired
     *
     * @param {Doc} y The Yjs instance
     * @param {Item} item
     */
    _integrate(y, item) {
      super._integrate(y, item);
      /** @type {Map<string, any>} */
      this._prelimAttrs.forEach((value, key) => {
        this.setAttribute(key, value);
      });
      this._prelimAttrs = null;
    }
    /**
     * Creates an Item with the same effect as this Item (without position effect)
     *
     * @return {YXmlElement}
     */
    _copy() {
      return new _YXmlElement(this.nodeName);
    }
    /**
     * @return {YXmlElement<KV>}
     */
    clone() {
      const el = new _YXmlElement(this.nodeName);
      const attrs = this.getAttributes();
      forEach(attrs, (value, key) => {
        if (typeof value === "string") {
          el.setAttribute(key, value);
        }
      });
      el.insert(0, this.toArray().map((item) => item instanceof AbstractType ? item.clone() : item));
      return el;
    }
    /**
     * Returns the XML serialization of this YXmlElement.
     * The attributes are ordered by attribute-name, so you can easily use this
     * method to compare YXmlElements
     *
     * @return {string} The string representation of this type.
     *
     * @public
     */
    toString() {
      const attrs = this.getAttributes();
      const stringBuilder = [];
      const keys2 = [];
      for (const key in attrs) {
        keys2.push(key);
      }
      keys2.sort();
      const keysLen = keys2.length;
      for (let i = 0; i < keysLen; i++) {
        const key = keys2[i];
        stringBuilder.push(key + '="' + attrs[key] + '"');
      }
      const nodeName = this.nodeName.toLocaleLowerCase();
      const attrsString = stringBuilder.length > 0 ? " " + stringBuilder.join(" ") : "";
      return `<${nodeName}${attrsString}>${super.toString()}</${nodeName}>`;
    }
    /**
     * Removes an attribute from this YXmlElement.
     *
     * @param {string} attributeName The attribute name that is to be removed.
     *
     * @public
     */
    removeAttribute(attributeName) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeMapDelete(transaction, this, attributeName);
        });
      } else {
        this._prelimAttrs.delete(attributeName);
      }
    }
    /**
     * Sets or updates an attribute.
     *
     * @template {keyof KV & string} KEY
     *
     * @param {KEY} attributeName The attribute name that is to be set.
     * @param {KV[KEY]} attributeValue The attribute value that is to be set.
     *
     * @public
     */
    setAttribute(attributeName, attributeValue) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeMapSet(transaction, this, attributeName, attributeValue);
        });
      } else {
        this._prelimAttrs.set(attributeName, attributeValue);
      }
    }
    /**
     * Returns an attribute value that belongs to the attribute name.
     *
     * @template {keyof KV & string} KEY
     *
     * @param {KEY} attributeName The attribute name that identifies the
     *                               queried value.
     * @return {KV[KEY]|undefined} The queried attribute value.
     *
     * @public
     */
    getAttribute(attributeName) {
      return (
        /** @type {any} */
        typeMapGet(this, attributeName)
      );
    }
    /**
     * Returns whether an attribute exists
     *
     * @param {string} attributeName The attribute name to check for existence.
     * @return {boolean} whether the attribute exists.
     *
     * @public
     */
    hasAttribute(attributeName) {
      return (
        /** @type {any} */
        typeMapHas(this, attributeName)
      );
    }
    /**
     * Returns all attribute name/value pairs in a JSON Object.
     *
     * @return {{ [Key in Extract<keyof KV,string>]?: KV[Key]}} A JSON Object that describes the attributes.
     *
     * @public
     */
    getAttributes() {
      return (
        /** @type {any} */
        typeMapGetAll(this)
      );
    }
    /**
     * Creates a Dom Element that mirrors this YXmlElement.
     *
     * @param {Document} [_document=document] The document object (you must define
     *                                        this when calling this method in
     *                                        nodejs)
     * @param {Object<string, any>} [hooks={}] Optional property to customize how hooks
     *                                             are presented in the DOM
     * @param {any} [binding] You should not set this property. This is
     *                               used if DomBinding wants to create a
     *                               association to the created DOM type.
     * @return {Node} The {@link https://developer.mozilla.org/en-US/docs/Web/API/Element|Dom Element}
     *
     * @public
     */
    toDOM(_document = document, hooks = {}, binding) {
      const dom = _document.createElement(this.nodeName);
      const attrs = this.getAttributes();
      for (const key in attrs) {
        const value = attrs[key];
        if (typeof value === "string") {
          dom.setAttribute(key, value);
        }
      }
      typeListForEach(this, (yxml) => {
        dom.appendChild(yxml.toDOM(_document, hooks, binding));
      });
      if (binding !== void 0) {
        binding._createAssociation(dom, this);
      }
      return dom;
    }
    /**
     * Transform the properties of this type to binary and write it to an
     * BinaryEncoder.
     *
     * This is called when this Item is sent to a remote peer.
     *
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder The encoder to write data to.
     */
    _write(encoder) {
      encoder.writeTypeRef(YXmlElementRefID);
      encoder.writeKey(this.nodeName);
    }
  };
  var readYXmlElement = (decoder) => new YXmlElement(decoder.readKey());
  var YXmlEvent = class extends YEvent {
    /**
     * @param {YXmlElement|YXmlText|YXmlFragment} target The target on which the event is created.
     * @param {Set<string|null>} subs The set of changed attributes. `null` is included if the
     *                   child list changed.
     * @param {Transaction} transaction The transaction instance with wich the
     *                                  change was created.
     */
    constructor(target, subs, transaction) {
      super(target, transaction);
      this.childListChanged = false;
      this.attributesChanged = /* @__PURE__ */ new Set();
      subs.forEach((sub) => {
        if (sub === null) {
          this.childListChanged = true;
        } else {
          this.attributesChanged.add(sub);
        }
      });
    }
  };
  var YXmlHook = class _YXmlHook extends YMap {
    /**
     * @param {string} hookName nodeName of the Dom Node.
     */
    constructor(hookName) {
      super();
      this.hookName = hookName;
    }
    /**
     * Creates an Item with the same effect as this Item (without position effect)
     */
    _copy() {
      return new _YXmlHook(this.hookName);
    }
    /**
     * @return {YXmlHook}
     */
    clone() {
      const el = new _YXmlHook(this.hookName);
      this.forEach((value, key) => {
        el.set(key, value);
      });
      return el;
    }
    /**
     * Creates a Dom Element that mirrors this YXmlElement.
     *
     * @param {Document} [_document=document] The document object (you must define
     *                                        this when calling this method in
     *                                        nodejs)
     * @param {Object.<string, any>} [hooks] Optional property to customize how hooks
     *                                             are presented in the DOM
     * @param {any} [binding] You should not set this property. This is
     *                               used if DomBinding wants to create a
     *                               association to the created DOM type
     * @return {Element} The {@link https://developer.mozilla.org/en-US/docs/Web/API/Element|Dom Element}
     *
     * @public
     */
    toDOM(_document = document, hooks = {}, binding) {
      const hook = hooks[this.hookName];
      let dom;
      if (hook !== void 0) {
        dom = hook.createDom(this);
      } else {
        dom = document.createElement(this.hookName);
      }
      dom.setAttribute("data-yjs-hook", this.hookName);
      if (binding !== void 0) {
        binding._createAssociation(dom, this);
      }
      return dom;
    }
    /**
     * Transform the properties of this type to binary and write it to an
     * BinaryEncoder.
     *
     * This is called when this Item is sent to a remote peer.
     *
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder The encoder to write data to.
     */
    _write(encoder) {
      encoder.writeTypeRef(YXmlHookRefID);
      encoder.writeKey(this.hookName);
    }
  };
  var readYXmlHook = (decoder) => new YXmlHook(decoder.readKey());
  var YXmlText = class _YXmlText extends YText {
    /**
     * @type {YXmlElement|YXmlText|null}
     */
    get nextSibling() {
      const n = this._item ? this._item.next : null;
      return n ? (
        /** @type {YXmlElement|YXmlText} */
        /** @type {ContentType} */
        n.content.type
      ) : null;
    }
    /**
     * @type {YXmlElement|YXmlText|null}
     */
    get prevSibling() {
      const n = this._item ? this._item.prev : null;
      return n ? (
        /** @type {YXmlElement|YXmlText} */
        /** @type {ContentType} */
        n.content.type
      ) : null;
    }
    _copy() {
      return new _YXmlText();
    }
    /**
     * @return {YXmlText}
     */
    clone() {
      const text2 = new _YXmlText();
      text2.applyDelta(this.toDelta());
      return text2;
    }
    /**
     * Creates a Dom Element that mirrors this YXmlText.
     *
     * @param {Document} [_document=document] The document object (you must define
     *                                        this when calling this method in
     *                                        nodejs)
     * @param {Object<string, any>} [hooks] Optional property to customize how hooks
     *                                             are presented in the DOM
     * @param {any} [binding] You should not set this property. This is
     *                               used if DomBinding wants to create a
     *                               association to the created DOM type.
     * @return {Text} The {@link https://developer.mozilla.org/en-US/docs/Web/API/Element|Dom Element}
     *
     * @public
     */
    toDOM(_document = document, hooks, binding) {
      const dom = _document.createTextNode(this.toString());
      if (binding !== void 0) {
        binding._createAssociation(dom, this);
      }
      return dom;
    }
    toString() {
      return this.toDelta().map((delta) => {
        const nestedNodes = [];
        for (const nodeName in delta.attributes) {
          const attrs = [];
          for (const key in delta.attributes[nodeName]) {
            attrs.push({ key, value: delta.attributes[nodeName][key] });
          }
          attrs.sort((a, b) => a.key < b.key ? -1 : 1);
          nestedNodes.push({ nodeName, attrs });
        }
        nestedNodes.sort((a, b) => a.nodeName < b.nodeName ? -1 : 1);
        let str = "";
        for (let i = 0; i < nestedNodes.length; i++) {
          const node = nestedNodes[i];
          str += `<${node.nodeName}`;
          for (let j = 0; j < node.attrs.length; j++) {
            const attr = node.attrs[j];
            str += ` ${attr.key}="${attr.value}"`;
          }
          str += ">";
        }
        str += delta.insert;
        for (let i = nestedNodes.length - 1; i >= 0; i--) {
          str += `</${nestedNodes[i].nodeName}>`;
        }
        return str;
      }).join("");
    }
    /**
     * @return {string}
     */
    toJSON() {
      return this.toString();
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     */
    _write(encoder) {
      encoder.writeTypeRef(YXmlTextRefID);
    }
  };
  var readYXmlText = (decoder) => new YXmlText();
  var AbstractStruct = class {
    /**
     * @param {ID} id
     * @param {number} length
     */
    constructor(id2, length3) {
      this.id = id2;
      this.length = length3;
    }
    /**
     * @type {boolean}
     */
    get deleted() {
      throw methodUnimplemented();
    }
    /**
     * Merge this struct with the item to the right.
     * This method is already assuming that `this.id.clock + this.length === this.id.clock`.
     * Also this method does *not* remove right from StructStore!
     * @param {AbstractStruct} right
     * @return {boolean} wether this merged with right
     */
    mergeWith(right) {
      return false;
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder The encoder to write data to.
     * @param {number} offset
     * @param {number} encodingRef
     */
    write(encoder, offset, encodingRef) {
      throw methodUnimplemented();
    }
    /**
     * @param {Transaction} transaction
     * @param {number} offset
     */
    integrate(transaction, offset) {
      throw methodUnimplemented();
    }
  };
  var structGCRefNumber = 0;
  var GC = class extends AbstractStruct {
    get deleted() {
      return true;
    }
    delete() {
    }
    /**
     * @param {GC} right
     * @return {boolean}
     */
    mergeWith(right) {
      if (this.constructor !== right.constructor) {
        return false;
      }
      this.length += right.length;
      return true;
    }
    /**
     * @param {Transaction} transaction
     * @param {number} offset
     */
    integrate(transaction, offset) {
      if (offset > 0) {
        this.id.clock += offset;
        this.length -= offset;
      }
      addStruct(transaction.doc.store, this);
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeInfo(structGCRefNumber);
      encoder.writeLen(this.length - offset);
    }
    /**
     * @param {Transaction} transaction
     * @param {StructStore} store
     * @return {null | number}
     */
    getMissing(transaction, store2) {
      return null;
    }
  };
  var ContentBinary = class _ContentBinary {
    /**
     * @param {Uint8Array} content
     */
    constructor(content) {
      this.content = content;
    }
    /**
     * @return {number}
     */
    getLength() {
      return 1;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return [this.content];
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentBinary}
     */
    copy() {
      return new _ContentBinary(this.content);
    }
    /**
     * @param {number} offset
     * @return {ContentBinary}
     */
    splice(offset) {
      throw methodUnimplemented();
    }
    /**
     * @param {ContentBinary} right
     * @return {boolean}
     */
    mergeWith(right) {
      return false;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeBuf(this.content);
    }
    /**
     * @return {number}
     */
    getRef() {
      return 3;
    }
  };
  var readContentBinary = (decoder) => new ContentBinary(decoder.readBuf());
  var ContentDeleted = class _ContentDeleted {
    /**
     * @param {number} len
     */
    constructor(len) {
      this.len = len;
    }
    /**
     * @return {number}
     */
    getLength() {
      return this.len;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return [];
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return false;
    }
    /**
     * @return {ContentDeleted}
     */
    copy() {
      return new _ContentDeleted(this.len);
    }
    /**
     * @param {number} offset
     * @return {ContentDeleted}
     */
    splice(offset) {
      const right = new _ContentDeleted(this.len - offset);
      this.len = offset;
      return right;
    }
    /**
     * @param {ContentDeleted} right
     * @return {boolean}
     */
    mergeWith(right) {
      this.len += right.len;
      return true;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
      addToDeleteSet(transaction.deleteSet, item.id.client, item.id.clock, this.len);
      item.markDeleted();
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeLen(this.len - offset);
    }
    /**
     * @return {number}
     */
    getRef() {
      return 1;
    }
  };
  var readContentDeleted = (decoder) => new ContentDeleted(decoder.readLen());
  var createDocFromOpts = (guid, opts) => new Doc({ guid, ...opts, shouldLoad: opts.shouldLoad || opts.autoLoad || false });
  var ContentDoc = class _ContentDoc {
    /**
     * @param {Doc} doc
     */
    constructor(doc2) {
      if (doc2._item) {
        console.error("This document was already integrated as a sub-document. You should create a second instance instead with the same guid.");
      }
      this.doc = doc2;
      const opts = {};
      this.opts = opts;
      if (!doc2.gc) {
        opts.gc = false;
      }
      if (doc2.autoLoad) {
        opts.autoLoad = true;
      }
      if (doc2.meta !== null) {
        opts.meta = doc2.meta;
      }
    }
    /**
     * @return {number}
     */
    getLength() {
      return 1;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return [this.doc];
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentDoc}
     */
    copy() {
      return new _ContentDoc(createDocFromOpts(this.doc.guid, this.opts));
    }
    /**
     * @param {number} offset
     * @return {ContentDoc}
     */
    splice(offset) {
      throw methodUnimplemented();
    }
    /**
     * @param {ContentDoc} right
     * @return {boolean}
     */
    mergeWith(right) {
      return false;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
      this.doc._item = item;
      transaction.subdocsAdded.add(this.doc);
      if (this.doc.shouldLoad) {
        transaction.subdocsLoaded.add(this.doc);
      }
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
      if (transaction.subdocsAdded.has(this.doc)) {
        transaction.subdocsAdded.delete(this.doc);
      } else {
        transaction.subdocsRemoved.add(this.doc);
      }
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeString(this.doc.guid);
      encoder.writeAny(this.opts);
    }
    /**
     * @return {number}
     */
    getRef() {
      return 9;
    }
  };
  var readContentDoc = (decoder) => new ContentDoc(createDocFromOpts(decoder.readString(), decoder.readAny()));
  var ContentEmbed = class _ContentEmbed {
    /**
     * @param {Object} embed
     */
    constructor(embed) {
      this.embed = embed;
    }
    /**
     * @return {number}
     */
    getLength() {
      return 1;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return [this.embed];
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentEmbed}
     */
    copy() {
      return new _ContentEmbed(this.embed);
    }
    /**
     * @param {number} offset
     * @return {ContentEmbed}
     */
    splice(offset) {
      throw methodUnimplemented();
    }
    /**
     * @param {ContentEmbed} right
     * @return {boolean}
     */
    mergeWith(right) {
      return false;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeJSON(this.embed);
    }
    /**
     * @return {number}
     */
    getRef() {
      return 5;
    }
  };
  var readContentEmbed = (decoder) => new ContentEmbed(decoder.readJSON());
  var ContentFormat = class _ContentFormat {
    /**
     * @param {string} key
     * @param {Object} value
     */
    constructor(key, value) {
      this.key = key;
      this.value = value;
    }
    /**
     * @return {number}
     */
    getLength() {
      return 1;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return [];
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return false;
    }
    /**
     * @return {ContentFormat}
     */
    copy() {
      return new _ContentFormat(this.key, this.value);
    }
    /**
     * @param {number} _offset
     * @return {ContentFormat}
     */
    splice(_offset) {
      throw methodUnimplemented();
    }
    /**
     * @param {ContentFormat} _right
     * @return {boolean}
     */
    mergeWith(_right) {
      return false;
    }
    /**
     * @param {Transaction} _transaction
     * @param {Item} item
     */
    integrate(_transaction, item) {
      const p = (
        /** @type {YText} */
        item.parent
      );
      p._searchMarker = null;
      p._hasFormatting = true;
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeKey(this.key);
      encoder.writeJSON(this.value);
    }
    /**
     * @return {number}
     */
    getRef() {
      return 6;
    }
  };
  var readContentFormat = (decoder) => new ContentFormat(decoder.readKey(), decoder.readJSON());
  var ContentJSON = class _ContentJSON {
    /**
     * @param {Array<any>} arr
     */
    constructor(arr) {
      this.arr = arr;
    }
    /**
     * @return {number}
     */
    getLength() {
      return this.arr.length;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return this.arr;
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentJSON}
     */
    copy() {
      return new _ContentJSON(this.arr);
    }
    /**
     * @param {number} offset
     * @return {ContentJSON}
     */
    splice(offset) {
      const right = new _ContentJSON(this.arr.slice(offset));
      this.arr = this.arr.slice(0, offset);
      return right;
    }
    /**
     * @param {ContentJSON} right
     * @return {boolean}
     */
    mergeWith(right) {
      this.arr = this.arr.concat(right.arr);
      return true;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      const len = this.arr.length;
      encoder.writeLen(len - offset);
      for (let i = offset; i < len; i++) {
        const c = this.arr[i];
        encoder.writeString(c === void 0 ? "undefined" : JSON.stringify(c));
      }
    }
    /**
     * @return {number}
     */
    getRef() {
      return 2;
    }
  };
  var readContentJSON = (decoder) => {
    const len = decoder.readLen();
    const cs = [];
    for (let i = 0; i < len; i++) {
      const c = decoder.readString();
      if (c === "undefined") {
        cs.push(void 0);
      } else {
        cs.push(JSON.parse(c));
      }
    }
    return new ContentJSON(cs);
  };
  var ContentAny = class _ContentAny {
    /**
     * @param {Array<any>} arr
     */
    constructor(arr) {
      this.arr = arr;
    }
    /**
     * @return {number}
     */
    getLength() {
      return this.arr.length;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return this.arr;
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentAny}
     */
    copy() {
      return new _ContentAny(this.arr);
    }
    /**
     * @param {number} offset
     * @return {ContentAny}
     */
    splice(offset) {
      const right = new _ContentAny(this.arr.slice(offset));
      this.arr = this.arr.slice(0, offset);
      return right;
    }
    /**
     * @param {ContentAny} right
     * @return {boolean}
     */
    mergeWith(right) {
      this.arr = this.arr.concat(right.arr);
      return true;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      const len = this.arr.length;
      encoder.writeLen(len - offset);
      for (let i = offset; i < len; i++) {
        const c = this.arr[i];
        encoder.writeAny(c);
      }
    }
    /**
     * @return {number}
     */
    getRef() {
      return 8;
    }
  };
  var readContentAny = (decoder) => {
    const len = decoder.readLen();
    const cs = [];
    for (let i = 0; i < len; i++) {
      cs.push(decoder.readAny());
    }
    return new ContentAny(cs);
  };
  var ContentString = class _ContentString {
    /**
     * @param {string} str
     */
    constructor(str) {
      this.str = str;
    }
    /**
     * @return {number}
     */
    getLength() {
      return this.str.length;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return this.str.split("");
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentString}
     */
    copy() {
      return new _ContentString(this.str);
    }
    /**
     * @param {number} offset
     * @return {ContentString}
     */
    splice(offset) {
      const right = new _ContentString(this.str.slice(offset));
      this.str = this.str.slice(0, offset);
      const firstCharCode = this.str.charCodeAt(offset - 1);
      if (firstCharCode >= 55296 && firstCharCode <= 56319) {
        this.str = this.str.slice(0, offset - 1) + "\uFFFD";
        right.str = "\uFFFD" + right.str.slice(1);
      }
      return right;
    }
    /**
     * @param {ContentString} right
     * @return {boolean}
     */
    mergeWith(right) {
      this.str += right.str;
      return true;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeString(offset === 0 ? this.str : this.str.slice(offset));
    }
    /**
     * @return {number}
     */
    getRef() {
      return 4;
    }
  };
  var readContentString = (decoder) => new ContentString(decoder.readString());
  var typeRefs = [
    readYArray,
    readYMap,
    readYText,
    readYXmlElement,
    readYXmlFragment,
    readYXmlHook,
    readYXmlText
  ];
  var YArrayRefID = 0;
  var YMapRefID = 1;
  var YTextRefID = 2;
  var YXmlElementRefID = 3;
  var YXmlFragmentRefID = 4;
  var YXmlHookRefID = 5;
  var YXmlTextRefID = 6;
  var ContentType = class _ContentType {
    /**
     * @param {AbstractType<any>} type
     */
    constructor(type) {
      this.type = type;
    }
    /**
     * @return {number}
     */
    getLength() {
      return 1;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return [this.type];
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentType}
     */
    copy() {
      return new _ContentType(this.type._copy());
    }
    /**
     * @param {number} offset
     * @return {ContentType}
     */
    splice(offset) {
      throw methodUnimplemented();
    }
    /**
     * @param {ContentType} right
     * @return {boolean}
     */
    mergeWith(right) {
      return false;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
      this.type._integrate(transaction.doc, item);
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
      let item = this.type._start;
      while (item !== null) {
        if (!item.deleted) {
          item.delete(transaction);
        } else if (item.id.clock < (transaction.beforeState.get(item.id.client) || 0)) {
          transaction._mergeStructs.push(item);
        }
        item = item.right;
      }
      this.type._map.forEach((item2) => {
        if (!item2.deleted) {
          item2.delete(transaction);
        } else if (item2.id.clock < (transaction.beforeState.get(item2.id.client) || 0)) {
          transaction._mergeStructs.push(item2);
        }
      });
      transaction.changed.delete(this.type);
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
      let item = this.type._start;
      while (item !== null) {
        item.gc(store2, true);
        item = item.right;
      }
      this.type._start = null;
      this.type._map.forEach(
        /** @param {Item | null} item */
        (item2) => {
          while (item2 !== null) {
            item2.gc(store2, true);
            item2 = item2.left;
          }
        }
      );
      this.type._map = /* @__PURE__ */ new Map();
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      this.type._write(encoder);
    }
    /**
     * @return {number}
     */
    getRef() {
      return 7;
    }
  };
  var readContentType = (decoder) => new ContentType(typeRefs[decoder.readTypeRef()](decoder));
  var followRedone = (store2, id2) => {
    let nextID = id2;
    let diff = 0;
    let item;
    do {
      if (diff > 0) {
        nextID = createID(nextID.client, nextID.clock + diff);
      }
      item = getItem(store2, nextID);
      diff = nextID.clock - item.id.clock;
      nextID = item.redone;
    } while (nextID !== null && item instanceof Item);
    return {
      item,
      diff
    };
  };
  var keepItem = (item, keep) => {
    while (item !== null && item.keep !== keep) {
      item.keep = keep;
      item = /** @type {AbstractType<any>} */
      item.parent._item;
    }
  };
  var splitItem = (transaction, leftItem, diff) => {
    const { client, clock } = leftItem.id;
    const rightItem = new Item(
      createID(client, clock + diff),
      leftItem,
      createID(client, clock + diff - 1),
      leftItem.right,
      leftItem.rightOrigin,
      leftItem.parent,
      leftItem.parentSub,
      leftItem.content.splice(diff)
    );
    if (leftItem.deleted) {
      rightItem.markDeleted();
    }
    if (leftItem.keep) {
      rightItem.keep = true;
    }
    if (leftItem.redone !== null) {
      rightItem.redone = createID(leftItem.redone.client, leftItem.redone.clock + diff);
    }
    leftItem.right = rightItem;
    if (rightItem.right !== null) {
      rightItem.right.left = rightItem;
    }
    transaction._mergeStructs.push(rightItem);
    if (rightItem.parentSub !== null && rightItem.right === null) {
      rightItem.parent._map.set(rightItem.parentSub, rightItem);
    }
    leftItem.length = diff;
    return rightItem;
  };
  var isDeletedByUndoStack = (stack, id2) => some(
    stack,
    /** @param {StackItem} s */
    (s) => isDeleted(s.deletions, id2)
  );
  var redoItem = (transaction, item, redoitems, itemsToDelete, ignoreRemoteMapChanges, um) => {
    const doc2 = transaction.doc;
    const store2 = doc2.store;
    const ownClientID = doc2.clientID;
    const redone = item.redone;
    if (redone !== null) {
      return getItemCleanStart(transaction, redone);
    }
    let parentItem = (
      /** @type {AbstractType<any>} */
      item.parent._item
    );
    let left = null;
    let right;
    if (parentItem !== null && parentItem.deleted === true) {
      if (parentItem.redone === null && (!redoitems.has(parentItem) || redoItem(transaction, parentItem, redoitems, itemsToDelete, ignoreRemoteMapChanges, um) === null)) {
        return null;
      }
      while (parentItem.redone !== null) {
        parentItem = getItemCleanStart(transaction, parentItem.redone);
      }
    }
    const parentType = parentItem === null ? (
      /** @type {AbstractType<any>} */
      item.parent
    ) : (
      /** @type {ContentType} */
      parentItem.content.type
    );
    if (item.parentSub === null) {
      left = item.left;
      right = item;
      while (left !== null) {
        let leftTrace = left;
        while (leftTrace !== null && /** @type {AbstractType<any>} */
        leftTrace.parent._item !== parentItem) {
          leftTrace = leftTrace.redone === null ? null : getItemCleanStart(transaction, leftTrace.redone);
        }
        if (leftTrace !== null && /** @type {AbstractType<any>} */
        leftTrace.parent._item === parentItem) {
          left = leftTrace;
          break;
        }
        left = left.left;
      }
      while (right !== null) {
        let rightTrace = right;
        while (rightTrace !== null && /** @type {AbstractType<any>} */
        rightTrace.parent._item !== parentItem) {
          rightTrace = rightTrace.redone === null ? null : getItemCleanStart(transaction, rightTrace.redone);
        }
        if (rightTrace !== null && /** @type {AbstractType<any>} */
        rightTrace.parent._item === parentItem) {
          right = rightTrace;
          break;
        }
        right = right.right;
      }
    } else {
      right = null;
      if (item.right && !ignoreRemoteMapChanges) {
        left = item;
        while (left !== null && left.right !== null && (left.right.redone || isDeleted(itemsToDelete, left.right.id) || isDeletedByUndoStack(um.undoStack, left.right.id) || isDeletedByUndoStack(um.redoStack, left.right.id))) {
          left = left.right;
          while (left.redone) left = getItemCleanStart(transaction, left.redone);
        }
        if (left && left.right !== null) {
          return null;
        }
      } else {
        left = parentType._map.get(item.parentSub) || null;
      }
    }
    const nextClock = getState(store2, ownClientID);
    const nextId = createID(ownClientID, nextClock);
    const redoneItem = new Item(
      nextId,
      left,
      left && left.lastId,
      right,
      right && right.id,
      parentType,
      item.parentSub,
      item.content.copy()
    );
    item.redone = nextId;
    keepItem(redoneItem, true);
    redoneItem.integrate(transaction, 0);
    return redoneItem;
  };
  var Item = class _Item extends AbstractStruct {
    /**
     * @param {ID} id
     * @param {Item | null} left
     * @param {ID | null} origin
     * @param {Item | null} right
     * @param {ID | null} rightOrigin
     * @param {AbstractType<any>|ID|null} parent Is a type if integrated, is null if it is possible to copy parent from left or right, is ID before integration to search for it.
     * @param {string | null} parentSub
     * @param {AbstractContent} content
     */
    constructor(id2, left, origin, right, rightOrigin, parent, parentSub, content) {
      super(id2, content.getLength());
      this.origin = origin;
      this.left = left;
      this.right = right;
      this.rightOrigin = rightOrigin;
      this.parent = parent;
      this.parentSub = parentSub;
      this.redone = null;
      this.content = content;
      this.info = this.content.isCountable() ? BIT2 : 0;
    }
    /**
     * This is used to mark the item as an indexed fast-search marker
     *
     * @type {boolean}
     */
    set marker(isMarked) {
      if ((this.info & BIT4) > 0 !== isMarked) {
        this.info ^= BIT4;
      }
    }
    get marker() {
      return (this.info & BIT4) > 0;
    }
    /**
     * If true, do not garbage collect this Item.
     */
    get keep() {
      return (this.info & BIT1) > 0;
    }
    set keep(doKeep) {
      if (this.keep !== doKeep) {
        this.info ^= BIT1;
      }
    }
    get countable() {
      return (this.info & BIT2) > 0;
    }
    /**
     * Whether this item was deleted or not.
     * @type {Boolean}
     */
    get deleted() {
      return (this.info & BIT3) > 0;
    }
    set deleted(doDelete) {
      if (this.deleted !== doDelete) {
        this.info ^= BIT3;
      }
    }
    markDeleted() {
      this.info |= BIT3;
    }
    /**
     * Return the creator clientID of the missing op or define missing items and return null.
     *
     * @param {Transaction} transaction
     * @param {StructStore} store
     * @return {null | number}
     */
    getMissing(transaction, store2) {
      if (this.origin && this.origin.client !== this.id.client && this.origin.clock >= getState(store2, this.origin.client)) {
        return this.origin.client;
      }
      if (this.rightOrigin && this.rightOrigin.client !== this.id.client && this.rightOrigin.clock >= getState(store2, this.rightOrigin.client)) {
        return this.rightOrigin.client;
      }
      if (this.parent && this.parent.constructor === ID && this.id.client !== this.parent.client && this.parent.clock >= getState(store2, this.parent.client)) {
        return this.parent.client;
      }
      if (this.origin) {
        this.left = getItemCleanEnd(transaction, store2, this.origin);
        this.origin = this.left.lastId;
      }
      if (this.rightOrigin) {
        this.right = getItemCleanStart(transaction, this.rightOrigin);
        this.rightOrigin = this.right.id;
      }
      if (this.left && this.left.constructor === GC || this.right && this.right.constructor === GC) {
        this.parent = null;
      }
      if (!this.parent) {
        if (this.left && this.left.constructor === _Item) {
          this.parent = this.left.parent;
          this.parentSub = this.left.parentSub;
        }
        if (this.right && this.right.constructor === _Item) {
          this.parent = this.right.parent;
          this.parentSub = this.right.parentSub;
        }
      } else if (this.parent.constructor === ID) {
        const parentItem = getItem(store2, this.parent);
        if (parentItem.constructor === GC) {
          this.parent = null;
        } else {
          this.parent = /** @type {ContentType} */
          parentItem.content.type;
        }
      }
      return null;
    }
    /**
     * @param {Transaction} transaction
     * @param {number} offset
     */
    integrate(transaction, offset) {
      if (offset > 0) {
        this.id.clock += offset;
        this.left = getItemCleanEnd(transaction, transaction.doc.store, createID(this.id.client, this.id.clock - 1));
        this.origin = this.left.lastId;
        this.content = this.content.splice(offset);
        this.length -= offset;
      }
      if (this.parent) {
        if (!this.left && (!this.right || this.right.left !== null) || this.left && this.left.right !== this.right) {
          let left = this.left;
          let o;
          if (left !== null) {
            o = left.right;
          } else if (this.parentSub !== null) {
            o = /** @type {AbstractType<any>} */
            this.parent._map.get(this.parentSub) || null;
            while (o !== null && o.left !== null) {
              o = o.left;
            }
          } else {
            o = /** @type {AbstractType<any>} */
            this.parent._start;
          }
          const conflictingItems = /* @__PURE__ */ new Set();
          const itemsBeforeOrigin = /* @__PURE__ */ new Set();
          while (o !== null && o !== this.right) {
            itemsBeforeOrigin.add(o);
            conflictingItems.add(o);
            if (compareIDs(this.origin, o.origin)) {
              if (o.id.client < this.id.client) {
                left = o;
                conflictingItems.clear();
              } else if (compareIDs(this.rightOrigin, o.rightOrigin)) {
                break;
              }
            } else if (o.origin !== null && itemsBeforeOrigin.has(getItem(transaction.doc.store, o.origin))) {
              if (!conflictingItems.has(getItem(transaction.doc.store, o.origin))) {
                left = o;
                conflictingItems.clear();
              }
            } else {
              break;
            }
            o = o.right;
          }
          this.left = left;
        }
        if (this.left !== null) {
          const right = this.left.right;
          this.right = right;
          this.left.right = this;
        } else {
          let r;
          if (this.parentSub !== null) {
            r = /** @type {AbstractType<any>} */
            this.parent._map.get(this.parentSub) || null;
            while (r !== null && r.left !== null) {
              r = r.left;
            }
          } else {
            r = /** @type {AbstractType<any>} */
            this.parent._start;
            this.parent._start = this;
          }
          this.right = r;
        }
        if (this.right !== null) {
          this.right.left = this;
        } else if (this.parentSub !== null) {
          this.parent._map.set(this.parentSub, this);
          if (this.left !== null) {
            this.left.delete(transaction);
          }
        }
        if (this.parentSub === null && this.countable && !this.deleted) {
          this.parent._length += this.length;
        }
        addStruct(transaction.doc.store, this);
        this.content.integrate(transaction, this);
        addChangedTypeToTransaction(
          transaction,
          /** @type {AbstractType<any>} */
          this.parent,
          this.parentSub
        );
        if (
          /** @type {AbstractType<any>} */
          this.parent._item !== null && /** @type {AbstractType<any>} */
          this.parent._item.deleted || this.parentSub !== null && this.right !== null
        ) {
          this.delete(transaction);
        }
      } else {
        new GC(this.id, this.length).integrate(transaction, 0);
      }
    }
    /**
     * Returns the next non-deleted item
     */
    get next() {
      let n = this.right;
      while (n !== null && n.deleted) {
        n = n.right;
      }
      return n;
    }
    /**
     * Returns the previous non-deleted item
     */
    get prev() {
      let n = this.left;
      while (n !== null && n.deleted) {
        n = n.left;
      }
      return n;
    }
    /**
     * Computes the last content address of this Item.
     */
    get lastId() {
      return this.length === 1 ? this.id : createID(this.id.client, this.id.clock + this.length - 1);
    }
    /**
     * Try to merge two items
     *
     * @param {Item} right
     * @return {boolean}
     */
    mergeWith(right) {
      if (this.constructor === right.constructor && compareIDs(right.origin, this.lastId) && this.right === right && compareIDs(this.rightOrigin, right.rightOrigin) && this.id.client === right.id.client && this.id.clock + this.length === right.id.clock && this.deleted === right.deleted && this.redone === null && right.redone === null && this.content.constructor === right.content.constructor && this.content.mergeWith(right.content)) {
        const searchMarker = (
          /** @type {AbstractType<any>} */
          this.parent._searchMarker
        );
        if (searchMarker) {
          searchMarker.forEach((marker) => {
            if (marker.p === right) {
              marker.p = this;
              if (!this.deleted && this.countable) {
                marker.index -= this.length;
              }
            }
          });
        }
        if (right.keep) {
          this.keep = true;
        }
        this.right = right.right;
        if (this.right !== null) {
          this.right.left = this;
        }
        this.length += right.length;
        return true;
      }
      return false;
    }
    /**
     * Mark this Item as deleted.
     *
     * @param {Transaction} transaction
     */
    delete(transaction) {
      if (!this.deleted) {
        const parent = (
          /** @type {AbstractType<any>} */
          this.parent
        );
        if (this.countable && this.parentSub === null) {
          parent._length -= this.length;
        }
        this.markDeleted();
        addToDeleteSet(transaction.deleteSet, this.id.client, this.id.clock, this.length);
        addChangedTypeToTransaction(transaction, parent, this.parentSub);
        this.content.delete(transaction);
      }
    }
    /**
     * @param {StructStore} store
     * @param {boolean} parentGCd
     */
    gc(store2, parentGCd) {
      if (!this.deleted) {
        throw unexpectedCase();
      }
      this.content.gc(store2);
      if (parentGCd) {
        replaceStruct(store2, this, new GC(this.id, this.length));
      } else {
        this.content = new ContentDeleted(this.length);
      }
    }
    /**
     * Transform the properties of this type to binary and write it to an
     * BinaryEncoder.
     *
     * This is called when this Item is sent to a remote peer.
     *
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder The encoder to write data to.
     * @param {number} offset
     */
    write(encoder, offset) {
      const origin = offset > 0 ? createID(this.id.client, this.id.clock + offset - 1) : this.origin;
      const rightOrigin = this.rightOrigin;
      const parentSub = this.parentSub;
      const info = this.content.getRef() & BITS5 | (origin === null ? 0 : BIT8) | // origin is defined
      (rightOrigin === null ? 0 : BIT7) | // right origin is defined
      (parentSub === null ? 0 : BIT6);
      encoder.writeInfo(info);
      if (origin !== null) {
        encoder.writeLeftID(origin);
      }
      if (rightOrigin !== null) {
        encoder.writeRightID(rightOrigin);
      }
      if (origin === null && rightOrigin === null) {
        const parent = (
          /** @type {AbstractType<any>} */
          this.parent
        );
        if (parent._item !== void 0) {
          const parentItem = parent._item;
          if (parentItem === null) {
            const ykey = findRootTypeKey(parent);
            encoder.writeParentInfo(true);
            encoder.writeString(ykey);
          } else {
            encoder.writeParentInfo(false);
            encoder.writeLeftID(parentItem.id);
          }
        } else if (parent.constructor === String) {
          encoder.writeParentInfo(true);
          encoder.writeString(parent);
        } else if (parent.constructor === ID) {
          encoder.writeParentInfo(false);
          encoder.writeLeftID(parent);
        } else {
          unexpectedCase();
        }
        if (parentSub !== null) {
          encoder.writeString(parentSub);
        }
      }
      this.content.write(encoder, offset);
    }
  };
  var readItemContent = (decoder, info) => contentRefs[info & BITS5](decoder);
  var contentRefs = [
    () => {
      unexpectedCase();
    },
    // GC is not ItemContent
    readContentDeleted,
    // 1
    readContentJSON,
    // 2
    readContentBinary,
    // 3
    readContentString,
    // 4
    readContentEmbed,
    // 5
    readContentFormat,
    // 6
    readContentType,
    // 7
    readContentAny,
    // 8
    readContentDoc,
    // 9
    () => {
      unexpectedCase();
    }
    // 10 - Skip is not ItemContent
  ];
  var structSkipRefNumber = 10;
  var Skip = class extends AbstractStruct {
    get deleted() {
      return true;
    }
    delete() {
    }
    /**
     * @param {Skip} right
     * @return {boolean}
     */
    mergeWith(right) {
      if (this.constructor !== right.constructor) {
        return false;
      }
      this.length += right.length;
      return true;
    }
    /**
     * @param {Transaction} transaction
     * @param {number} offset
     */
    integrate(transaction, offset) {
      unexpectedCase();
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeInfo(structSkipRefNumber);
      writeVarUint(encoder.restEncoder, this.length - offset);
    }
    /**
     * @param {Transaction} transaction
     * @param {StructStore} store
     * @return {null | number}
     */
    getMissing(transaction, store2) {
      return null;
    }
  };
  var glo = (
    /** @type {any} */
    typeof globalThis !== "undefined" ? globalThis : typeof window !== "undefined" ? window : typeof global !== "undefined" ? global : {}
  );
  var importIdentifier = "__ $YJS$ __";
  if (glo[importIdentifier] === true) {
    console.error("Yjs was already imported. This breaks constructor checks and will lead to issues! - https://github.com/yjs/yjs/issues/438");
  }
  glo[importIdentifier] = true;

  // packages/sync/build-module/config.js
  var CRDT_DOC_VERSION = 1;
  var CRDT_DOC_META_PERSISTENCE_KEY = "fromPersistence";
  var CRDT_RECORD_MAP_KEY = "document";
  var CRDT_STATE_MAP_KEY = "state";
  var CRDT_STATE_VERSION_KEY = "version";
  var LOCAL_EDITOR_ORIGIN = "gutenberg";
  var LOCAL_SYNC_MANAGER_ORIGIN = "syncManager";
  var WORDPRESS_META_KEY_FOR_CRDT_DOC_PERSISTENCE = "_crdt_document";

  // packages/sync/build-module/utils.js
  function createYjsDoc(documentMeta) {
    const metaMap = new Map(
      Object.entries(documentMeta)
    );
    const ydoc = new Doc({ meta: metaMap });
    const stateMap = ydoc.getMap(CRDT_STATE_MAP_KEY);
    stateMap.set(CRDT_STATE_VERSION_KEY, CRDT_DOC_VERSION);
    return ydoc;
  }
  function serializeCrdtDoc(crdtDoc) {
    return JSON.stringify({
      document: toBase64(encodeStateAsUpdateV2(crdtDoc))
    });
  }
  function deserializeCrdtDoc(serializedCrdtDoc) {
    try {
      const { document: document2 } = JSON.parse(serializedCrdtDoc);
      const docMetaMap = /* @__PURE__ */ new Map();
      docMetaMap.set(CRDT_DOC_META_PERSISTENCE_KEY, true);
      const ydoc = createYjsDoc({ meta: docMetaMap });
      const yupdate = fromBase64(document2);
      applyUpdateV2(ydoc, yupdate);
      ydoc.clientID = Math.floor(Math.random() * 1e9);
      return ydoc;
    } catch (e) {
      return null;
    }
  }

  // packages/sync/build-module/persistence.js
  function getPersistedCrdtDoc(record) {
    const serializedCrdtDoc = record.meta?.[WORDPRESS_META_KEY_FOR_CRDT_DOC_PERSISTENCE];
    if (serializedCrdtDoc) {
      return deserializeCrdtDoc(serializedCrdtDoc);
    }
    return null;
  }
  function createPersistedCRDTDoc(ydoc) {
    return {
      [WORDPRESS_META_KEY_FOR_CRDT_DOC_PERSISTENCE]: serializeCrdtDoc(ydoc)
    };
  }

  // packages/sync/build-module/providers/index.js
  var import_hooks = __toESM(require_hooks());

  // node_modules/lib0/indexeddb.js
  var rtop = (request) => create4((resolve2, reject2) => {
    request.onerror = (event) => reject2(new Error(event.target.error));
    request.onsuccess = (event) => resolve2(event.target.result);
  });
  var openDB = (name, initDB) => create4((resolve2, reject2) => {
    const request = indexedDB.open(name);
    request.onupgradeneeded = (event) => initDB(event.target.result);
    request.onerror = (event) => reject2(create3(event.target.error));
    request.onsuccess = (event) => {
      const db = event.target.result;
      db.onversionchange = () => {
        db.close();
      };
      if (typeof addEventListener !== "undefined") {
        addEventListener("unload", () => db.close());
      }
      resolve2(db);
    };
  });
  var deleteDB = (name) => rtop(indexedDB.deleteDatabase(name));
  var createStores = (db, definitions) => definitions.forEach(
    (d) => (
      // @ts-ignore
      db.createObjectStore.apply(db, d)
    )
  );
  var transact2 = (db, stores, access = "readwrite") => {
    const transaction = db.transaction(stores, access);
    return stores.map((store2) => getStore(transaction, store2));
  };
  var count = (store2, range) => rtop(store2.count(range));
  var get = (store2, key) => rtop(store2.get(key));
  var del = (store2, key) => rtop(store2.delete(key));
  var put = (store2, item, key) => rtop(store2.put(item, key));
  var addAutoKey = (store2, item) => rtop(store2.add(item));
  var getAll = (store2, range, limit) => rtop(store2.getAll(range, limit));
  var queryFirst = (store2, query, direction) => {
    let first = null;
    return iterateKeys(store2, query, (key) => {
      first = key;
      return false;
    }, direction).then(() => first);
  };
  var getLastKey = (store2, range = null) => queryFirst(store2, range, "prev");
  var iterateOnRequest = (request, f) => create4((resolve2, reject2) => {
    request.onerror = reject2;
    request.onsuccess = async (event) => {
      const cursor = event.target.result;
      if (cursor === null || await f(cursor) === false) {
        return resolve2();
      }
      cursor.continue();
    };
  });
  var iterateKeys = (store2, keyrange, f, direction = "next") => iterateOnRequest(store2.openKeyCursor(keyrange, direction), (cursor) => f(cursor.key));
  var getStore = (t, store2) => t.objectStore(store2);
  var createIDBKeyRangeUpperBound = (upper, upperOpen) => IDBKeyRange.upperBound(upper, upperOpen);
  var createIDBKeyRangeLowerBound = (lower, lowerOpen) => IDBKeyRange.lowerBound(lower, lowerOpen);

  // node_modules/y-indexeddb/src/y-indexeddb.js
  var customStoreName = "custom";
  var updatesStoreName = "updates";
  var PREFERRED_TRIM_SIZE = 500;
  var fetchUpdates = (idbPersistence, beforeApplyUpdatesCallback = () => {
  }, afterApplyUpdatesCallback = () => {
  }) => {
    const [updatesStore] = transact2(
      /** @type {IDBDatabase} */
      idbPersistence.db,
      [updatesStoreName]
    );
    return getAll(updatesStore, createIDBKeyRangeLowerBound(idbPersistence._dbref, false)).then((updates) => {
      if (!idbPersistence._destroyed) {
        beforeApplyUpdatesCallback(updatesStore);
        transact(idbPersistence.doc, () => {
          updates.forEach((val) => applyUpdate(idbPersistence.doc, val));
        }, idbPersistence, false);
        afterApplyUpdatesCallback(updatesStore);
      }
    }).then(() => getLastKey(updatesStore).then((lastKey) => {
      idbPersistence._dbref = lastKey + 1;
    })).then(() => count(updatesStore).then((cnt) => {
      idbPersistence._dbsize = cnt;
    })).then(() => updatesStore);
  };
  var storeState = (idbPersistence, forceStore = true) => fetchUpdates(idbPersistence).then((updatesStore) => {
    if (forceStore || idbPersistence._dbsize >= PREFERRED_TRIM_SIZE) {
      addAutoKey(updatesStore, encodeStateAsUpdate(idbPersistence.doc)).then(() => del(updatesStore, createIDBKeyRangeUpperBound(idbPersistence._dbref, true))).then(() => count(updatesStore).then((cnt) => {
        idbPersistence._dbsize = cnt;
      }));
    }
  });
  var IndexeddbPersistence = class extends Observable {
    /**
     * @param {string} name
     * @param {Y.Doc} doc
     */
    constructor(name, doc2) {
      super();
      this.doc = doc2;
      this.name = name;
      this._dbref = 0;
      this._dbsize = 0;
      this._destroyed = false;
      this.db = null;
      this.synced = false;
      this._db = openDB(
        name,
        (db) => createStores(db, [
          ["updates", { autoIncrement: true }],
          ["custom"]
        ])
      );
      this.whenSynced = create4((resolve2) => this.on("synced", () => resolve2(this)));
      this._db.then((db) => {
        this.db = db;
        const beforeApplyUpdatesCallback = (updatesStore) => addAutoKey(updatesStore, encodeStateAsUpdate(doc2));
        const afterApplyUpdatesCallback = () => {
          if (this._destroyed) return this;
          this.synced = true;
          this.emit("synced", [this]);
        };
        fetchUpdates(this, beforeApplyUpdatesCallback, afterApplyUpdatesCallback);
      });
      this._storeTimeout = 1e3;
      this._storeTimeoutId = null;
      this._storeUpdate = (update, origin) => {
        if (this.db && origin !== this) {
          const [updatesStore] = transact2(
            /** @type {IDBDatabase} */
            this.db,
            [updatesStoreName]
          );
          addAutoKey(updatesStore, update);
          if (++this._dbsize >= PREFERRED_TRIM_SIZE) {
            if (this._storeTimeoutId !== null) {
              clearTimeout(this._storeTimeoutId);
            }
            this._storeTimeoutId = setTimeout(() => {
              storeState(this, false);
              this._storeTimeoutId = null;
            }, this._storeTimeout);
          }
        }
      };
      doc2.on("update", this._storeUpdate);
      this.destroy = this.destroy.bind(this);
      doc2.on("destroy", this.destroy);
    }
    destroy() {
      if (this._storeTimeoutId) {
        clearTimeout(this._storeTimeoutId);
      }
      this.doc.off("update", this._storeUpdate);
      this.doc.off("destroy", this.destroy);
      this._destroyed = true;
      return this._db.then((db) => {
        db.close();
      });
    }
    /**
     * Destroys this instance and removes all data from indexeddb.
     *
     * @return {Promise<void>}
     */
    clearData() {
      return this.destroy().then(() => {
        deleteDB(this.name);
      });
    }
    /**
     * @param {String | number | ArrayBuffer | Date} key
     * @return {Promise<String | number | ArrayBuffer | Date | any>}
     */
    get(key) {
      return this._db.then((db) => {
        const [custom] = transact2(db, [customStoreName], "readonly");
        return get(custom, key);
      });
    }
    /**
     * @param {String | number | ArrayBuffer | Date} key
     * @param {String | number | ArrayBuffer | Date} value
     * @return {Promise<String | number | ArrayBuffer | Date>}
     */
    set(key, value) {
      return this._db.then((db) => {
        const [custom] = transact2(db, [customStoreName]);
        return put(custom, value, key);
      });
    }
    /**
     * @param {String | number | ArrayBuffer | Date} key
     * @return {Promise<undefined>}
     */
    del(key) {
      return this._db.then((db) => {
        const [custom] = transact2(db, [customStoreName]);
        return del(custom, key);
      });
    }
  };

  // packages/sync/build-module/providers/indexeddb-provider.js
  function createIndexedDbProvider(objectType, objectId, doc2) {
    const roomName = `${objectType}-${objectId}`;
    const provider = new IndexeddbPersistence(roomName, doc2);
    return Promise.resolve({
      destroy: () => provider.destroy()
    });
  }

  // node_modules/lib0/websocket.js
  var reconnectTimeoutBase = 1200;
  var maxReconnectTimeout = 2500;
  var messageReconnectTimeout = 3e4;
  var setupWS = (wsclient) => {
    if (wsclient.shouldConnect && wsclient.ws === null) {
      const websocket = new WebSocket(wsclient.url);
      const binaryType = wsclient.binaryType;
      let pingTimeout = null;
      if (binaryType) {
        websocket.binaryType = binaryType;
      }
      wsclient.ws = websocket;
      wsclient.connecting = true;
      wsclient.connected = false;
      websocket.onmessage = (event) => {
        wsclient.lastMessageReceived = getUnixTime();
        const data = event.data;
        const message = typeof data === "string" ? JSON.parse(data) : data;
        if (message && message.type === "pong") {
          clearTimeout(pingTimeout);
          pingTimeout = setTimeout(sendPing, messageReconnectTimeout / 2);
        }
        wsclient.emit("message", [message, wsclient]);
      };
      const onclose = (error) => {
        if (wsclient.ws !== null) {
          wsclient.ws = null;
          wsclient.connecting = false;
          if (wsclient.connected) {
            wsclient.connected = false;
            wsclient.emit("disconnect", [{ type: "disconnect", error }, wsclient]);
          } else {
            wsclient.unsuccessfulReconnects++;
          }
          setTimeout(setupWS, min(log10(wsclient.unsuccessfulReconnects + 1) * reconnectTimeoutBase, maxReconnectTimeout), wsclient);
        }
        clearTimeout(pingTimeout);
      };
      const sendPing = () => {
        if (wsclient.ws === websocket) {
          wsclient.send({
            type: "ping"
          });
        }
      };
      websocket.onclose = () => onclose(null);
      websocket.onerror = (error) => onclose(error);
      websocket.onopen = () => {
        wsclient.lastMessageReceived = getUnixTime();
        wsclient.connecting = false;
        wsclient.connected = true;
        wsclient.unsuccessfulReconnects = 0;
        wsclient.emit("connect", [{ type: "connect" }, wsclient]);
        pingTimeout = setTimeout(sendPing, messageReconnectTimeout / 2);
      };
    }
  };
  var WebsocketClient = class extends Observable {
    /**
     * @param {string} url
     * @param {object} opts
     * @param {'arraybuffer' | 'blob' | null} [opts.binaryType] Set `ws.binaryType`
     */
    constructor(url, { binaryType } = {}) {
      super();
      this.url = url;
      this.ws = null;
      this.binaryType = binaryType || null;
      this.connected = false;
      this.connecting = false;
      this.unsuccessfulReconnects = 0;
      this.lastMessageReceived = 0;
      this.shouldConnect = true;
      this._checkInterval = setInterval(() => {
        if (this.connected && messageReconnectTimeout < getUnixTime() - this.lastMessageReceived) {
          this.ws.close();
        }
      }, messageReconnectTimeout / 2);
      setupWS(this);
    }
    /**
     * @param {any} message
     */
    send(message) {
      if (this.ws) {
        this.ws.send(JSON.stringify(message));
      }
    }
    destroy() {
      clearInterval(this._checkInterval);
      this.disconnect();
      super.destroy();
    }
    disconnect() {
      this.shouldConnect = false;
      if (this.ws !== null) {
        this.ws.close();
      }
    }
    connect() {
      this.shouldConnect = true;
      if (!this.connected && this.ws === null) {
        setupWS(this);
      }
    }
  };

  // node_modules/lib0/broadcastchannel.js
  var channels = /* @__PURE__ */ new Map();
  var LocalStoragePolyfill = class {
    /**
     * @param {string} room
     */
    constructor(room) {
      this.room = room;
      this.onmessage = null;
      this._onChange = (e) => e.key === room && this.onmessage !== null && this.onmessage({ data: fromBase64(e.newValue || "") });
      onChange(this._onChange);
    }
    /**
     * @param {ArrayBuffer} buf
     */
    postMessage(buf) {
      varStorage.setItem(this.room, toBase64(createUint8ArrayFromArrayBuffer(buf)));
    }
    close() {
      offChange(this._onChange);
    }
  };
  var BC = typeof BroadcastChannel === "undefined" ? LocalStoragePolyfill : BroadcastChannel;
  var getChannel = (room) => setIfUndefined(channels, room, () => {
    const subs = create2();
    const bc = new BC(room);
    bc.onmessage = (e) => subs.forEach((sub) => sub(e.data, "broadcastchannel"));
    return {
      bc,
      subs
    };
  });
  var subscribe = (room, f) => {
    getChannel(room).subs.add(f);
    return f;
  };
  var unsubscribe = (room, f) => {
    const channel = getChannel(room);
    const unsubscribed = channel.subs.delete(f);
    if (unsubscribed && channel.subs.size === 0) {
      channel.bc.close();
      channels.delete(room);
    }
    return unsubscribed;
  };
  var publish = (room, data, origin = null) => {
    const c = getChannel(room);
    c.bc.postMessage(data);
    c.subs.forEach((sub) => sub(data, origin));
  };

  // node_modules/lib0/mutex.js
  var createMutex = () => {
    let token = true;
    return (f, g) => {
      if (token) {
        token = false;
        try {
          f();
        } finally {
          token = true;
        }
      } else if (g !== void 0) {
        g();
      }
    };
  };

  // packages/sync/build-module/providers/y-webrtc/y-webrtc.js
  var import_simplepeer_min = __toESM(require_simplepeer_min());

  // node_modules/y-protocols/sync.js
  var messageYjsSyncStep1 = 0;
  var messageYjsSyncStep2 = 1;
  var messageYjsUpdate = 2;
  var writeSyncStep1 = (encoder, doc2) => {
    writeVarUint(encoder, messageYjsSyncStep1);
    const sv = encodeStateVector(doc2);
    writeVarUint8Array(encoder, sv);
  };
  var writeSyncStep2 = (encoder, doc2, encodedStateVector) => {
    writeVarUint(encoder, messageYjsSyncStep2);
    writeVarUint8Array(encoder, encodeStateAsUpdate(doc2, encodedStateVector));
  };
  var readSyncStep1 = (decoder, encoder, doc2) => writeSyncStep2(encoder, doc2, readVarUint8Array(decoder));
  var readSyncStep2 = (decoder, doc2, transactionOrigin) => {
    try {
      applyUpdate(doc2, readVarUint8Array(decoder), transactionOrigin);
    } catch (error) {
      console.error("Caught error while handling a Yjs update", error);
    }
  };
  var writeUpdate = (encoder, update) => {
    writeVarUint(encoder, messageYjsUpdate);
    writeVarUint8Array(encoder, update);
  };
  var readUpdate2 = readSyncStep2;
  var readSyncMessage = (decoder, encoder, doc2, transactionOrigin) => {
    const messageType = readVarUint(decoder);
    switch (messageType) {
      case messageYjsSyncStep1:
        readSyncStep1(decoder, encoder, doc2);
        break;
      case messageYjsSyncStep2:
        readSyncStep2(decoder, doc2, transactionOrigin);
        break;
      case messageYjsUpdate:
        readUpdate2(decoder, doc2, transactionOrigin);
        break;
      default:
        throw new Error("Unknown message type");
    }
    return messageType;
  };

  // node_modules/y-protocols/awareness.js
  var outdatedTimeout = 3e4;
  var Awareness = class extends Observable {
    /**
     * @param {Y.Doc} doc
     */
    constructor(doc2) {
      super();
      this.doc = doc2;
      this.clientID = doc2.clientID;
      this.states = /* @__PURE__ */ new Map();
      this.meta = /* @__PURE__ */ new Map();
      this._checkInterval = /** @type {any} */
      setInterval(() => {
        const now = getUnixTime();
        if (this.getLocalState() !== null && outdatedTimeout / 2 <= now - /** @type {{lastUpdated:number}} */
        this.meta.get(this.clientID).lastUpdated) {
          this.setLocalState(this.getLocalState());
        }
        const remove = [];
        this.meta.forEach((meta, clientid) => {
          if (clientid !== this.clientID && outdatedTimeout <= now - meta.lastUpdated && this.states.has(clientid)) {
            remove.push(clientid);
          }
        });
        if (remove.length > 0) {
          removeAwarenessStates(this, remove, "timeout");
        }
      }, floor(outdatedTimeout / 10));
      doc2.on("destroy", () => {
        this.destroy();
      });
      this.setLocalState({});
    }
    destroy() {
      this.emit("destroy", [this]);
      this.setLocalState(null);
      super.destroy();
      clearInterval(this._checkInterval);
    }
    /**
     * @return {Object<string,any>|null}
     */
    getLocalState() {
      return this.states.get(this.clientID) || null;
    }
    /**
     * @param {Object<string,any>|null} state
     */
    setLocalState(state) {
      const clientID = this.clientID;
      const currLocalMeta = this.meta.get(clientID);
      const clock = currLocalMeta === void 0 ? 0 : currLocalMeta.clock + 1;
      const prevState = this.states.get(clientID);
      if (state === null) {
        this.states.delete(clientID);
      } else {
        this.states.set(clientID, state);
      }
      this.meta.set(clientID, {
        clock,
        lastUpdated: getUnixTime()
      });
      const added = [];
      const updated = [];
      const filteredUpdated = [];
      const removed = [];
      if (state === null) {
        removed.push(clientID);
      } else if (prevState == null) {
        if (state != null) {
          added.push(clientID);
        }
      } else {
        updated.push(clientID);
        if (!equalityDeep(prevState, state)) {
          filteredUpdated.push(clientID);
        }
      }
      if (added.length > 0 || filteredUpdated.length > 0 || removed.length > 0) {
        this.emit("change", [{ added, updated: filteredUpdated, removed }, "local"]);
      }
      this.emit("update", [{ added, updated, removed }, "local"]);
    }
    /**
     * @param {string} field
     * @param {any} value
     */
    setLocalStateField(field, value) {
      const state = this.getLocalState();
      if (state !== null) {
        this.setLocalState({
          ...state,
          [field]: value
        });
      }
    }
    /**
     * @return {Map<number,Object<string,any>>}
     */
    getStates() {
      return this.states;
    }
  };
  var removeAwarenessStates = (awareness, clients, origin) => {
    const removed = [];
    for (let i = 0; i < clients.length; i++) {
      const clientID = clients[i];
      if (awareness.states.has(clientID)) {
        awareness.states.delete(clientID);
        if (clientID === awareness.clientID) {
          const curMeta = (
            /** @type {MetaClientState} */
            awareness.meta.get(clientID)
          );
          awareness.meta.set(clientID, {
            clock: curMeta.clock + 1,
            lastUpdated: getUnixTime()
          });
        }
        removed.push(clientID);
      }
    }
    if (removed.length > 0) {
      awareness.emit("change", [{ added: [], updated: [], removed }, origin]);
      awareness.emit("update", [{ added: [], updated: [], removed }, origin]);
    }
  };
  var encodeAwarenessUpdate = (awareness, clients, states = awareness.states) => {
    const len = clients.length;
    const encoder = createEncoder();
    writeVarUint(encoder, len);
    for (let i = 0; i < len; i++) {
      const clientID = clients[i];
      const state = states.get(clientID) || null;
      const clock = (
        /** @type {MetaClientState} */
        awareness.meta.get(clientID).clock
      );
      writeVarUint(encoder, clientID);
      writeVarUint(encoder, clock);
      writeVarString(encoder, JSON.stringify(state));
    }
    return toUint8Array(encoder);
  };
  var applyAwarenessUpdate = (awareness, update, origin) => {
    const decoder = createDecoder(update);
    const timestamp = getUnixTime();
    const added = [];
    const updated = [];
    const filteredUpdated = [];
    const removed = [];
    const len = readVarUint(decoder);
    for (let i = 0; i < len; i++) {
      const clientID = readVarUint(decoder);
      let clock = readVarUint(decoder);
      const state = JSON.parse(readVarString(decoder));
      const clientMeta = awareness.meta.get(clientID);
      const prevState = awareness.states.get(clientID);
      const currClock = clientMeta === void 0 ? 0 : clientMeta.clock;
      if (currClock < clock || currClock === clock && state === null && awareness.states.has(clientID)) {
        if (state === null) {
          if (clientID === awareness.clientID && awareness.getLocalState() != null) {
            clock++;
          } else {
            awareness.states.delete(clientID);
          }
        } else {
          awareness.states.set(clientID, state);
        }
        awareness.meta.set(clientID, {
          clock,
          lastUpdated: timestamp
        });
        if (clientMeta === void 0 && state !== null) {
          added.push(clientID);
        } else if (clientMeta !== void 0 && state === null) {
          removed.push(clientID);
        } else if (state !== null) {
          if (!equalityDeep(state, prevState)) {
            filteredUpdated.push(clientID);
          }
          updated.push(clientID);
        }
      }
    }
    if (added.length > 0 || filteredUpdated.length > 0 || removed.length > 0) {
      awareness.emit("change", [{
        added,
        updated: filteredUpdated,
        removed
      }, origin]);
    }
    if (added.length > 0 || updated.length > 0 || removed.length > 0) {
      awareness.emit("update", [{
        added,
        updated,
        removed
      }, origin]);
    }
  };

  // packages/sync/build-module/providers/y-webrtc/crypto.js
  var deriveKey = (secret, roomName) => {
    const secretBuffer = encodeUtf8(secret).buffer;
    const salt = encodeUtf8(roomName).buffer;
    return crypto.subtle.importKey("raw", secretBuffer, "PBKDF2", false, ["deriveKey"]).then(
      (keyMaterial) => crypto.subtle.deriveKey(
        {
          name: "PBKDF2",
          salt,
          iterations: 1e5,
          hash: "SHA-256"
        },
        keyMaterial,
        {
          name: "AES-GCM",
          length: 256
        },
        true,
        ["encrypt", "decrypt"]
      )
    );
  };
  var encrypt = (data, key) => {
    if (!key) {
      return (
        /** @type {PromiseLike<Uint8Array>} */
        resolve(data)
      );
    }
    const iv = crypto.getRandomValues(new Uint8Array(12));
    return crypto.subtle.encrypt(
      {
        name: "AES-GCM",
        iv
      },
      key,
      data
    ).then((cipher) => {
      const encryptedDataEncoder = createEncoder();
      writeVarString(encryptedDataEncoder, "AES-GCM");
      writeVarUint8Array(encryptedDataEncoder, iv);
      writeVarUint8Array(
        encryptedDataEncoder,
        new Uint8Array(cipher)
      );
      return toUint8Array(encryptedDataEncoder);
    });
  };
  var encryptJson = (data, key) => {
    const dataEncoder = createEncoder();
    writeAny(dataEncoder, data);
    return encrypt(toUint8Array(dataEncoder), key);
  };
  var decrypt = (data, key) => {
    if (!key) {
      return (
        /** @type {PromiseLike<Uint8Array>} */
        resolve(data)
      );
    }
    const dataDecoder = createDecoder(data);
    const algorithm = readVarString(dataDecoder);
    if (algorithm !== "AES-GCM") {
      reject(create3("Unknown encryption algorithm"));
    }
    const iv = readVarUint8Array(dataDecoder);
    const cipher = readVarUint8Array(dataDecoder);
    return crypto.subtle.decrypt(
      {
        name: "AES-GCM",
        iv
      },
      key,
      cipher
    ).then((data2) => new Uint8Array(data2));
  };
  var decryptJson = (data, key) => decrypt(data, key).then(
    (decryptedValue) => readAny(
      createDecoder(new Uint8Array(decryptedValue))
    )
  );

  // packages/sync/build-module/providers/y-webrtc/y-webrtc.js
  var log = createModuleLogger2("y-webrtc");
  var messageSync = 0;
  var messageQueryAwareness = 3;
  var messageAwareness = 1;
  var messageBcPeerId = 4;
  var signalingConns = /* @__PURE__ */ new Map();
  var rooms = /* @__PURE__ */ new Map();
  var checkIsSynced = (room) => {
    let synced = true;
    room.webrtcConns.forEach((peer) => {
      if (!peer.synced) {
        synced = false;
      }
    });
    if (!synced && room.synced || synced && !room.synced) {
      room.synced = synced;
      room.provider.emit("synced", [{ synced }]);
      log(
        "synced ",
        BOLD,
        room.name,
        UNBOLD,
        " with all peers"
      );
    }
  };
  var readMessage = (room, buf, syncedCallback) => {
    const decoder = createDecoder(buf);
    const encoder = createEncoder();
    const messageType = readVarUint(decoder);
    if (room === void 0) {
      return null;
    }
    const awareness = room.awareness;
    const doc2 = room.doc;
    let sendReply = false;
    switch (messageType) {
      case messageSync: {
        writeVarUint(encoder, messageSync);
        const syncMessageType = readSyncMessage(
          decoder,
          encoder,
          doc2,
          room
        );
        if (syncMessageType === messageYjsSyncStep2 && !room.synced) {
          syncedCallback();
        }
        if (syncMessageType === messageYjsSyncStep1) {
          sendReply = true;
        }
        break;
      }
      case messageQueryAwareness:
        writeVarUint(encoder, messageAwareness);
        writeVarUint8Array(
          encoder,
          encodeAwarenessUpdate(
            awareness,
            Array.from(awareness.getStates().keys())
          )
        );
        sendReply = true;
        break;
      case messageAwareness:
        applyAwarenessUpdate(
          awareness,
          readVarUint8Array(decoder),
          room
        );
        break;
      case messageBcPeerId: {
        const add = readUint8(decoder) === 1;
        const peerName = readVarString(decoder);
        if (peerName !== room.peerId && (room.bcConns.has(peerName) && !add || !room.bcConns.has(peerName) && add)) {
          const removed = [];
          const added = [];
          if (add) {
            room.bcConns.add(peerName);
            added.push(peerName);
          } else {
            room.bcConns.delete(peerName);
            removed.push(peerName);
          }
          room.provider.emit("peers", [
            {
              added,
              removed,
              webrtcPeers: Array.from(room.webrtcConns.keys()),
              bcPeers: Array.from(room.bcConns)
            }
          ]);
          broadcastBcPeerId(room);
        }
        break;
      }
      default:
        console.error("Unable to compute message");
        return encoder;
    }
    if (!sendReply) {
      return null;
    }
    return encoder;
  };
  var readPeerMessage = (peerConn, buf) => {
    const room = peerConn.room;
    log(
      "received message from ",
      BOLD,
      peerConn.remotePeerId,
      GREY,
      " (",
      room.name,
      ")",
      UNBOLD,
      UNCOLOR
    );
    return readMessage(room, buf, () => {
      peerConn.synced = true;
      log(
        "synced ",
        BOLD,
        room.name,
        UNBOLD,
        " with ",
        BOLD,
        peerConn.remotePeerId
      );
      checkIsSynced(room);
    });
  };
  var sendWebrtcConn = (webrtcConn, encoder) => {
    log(
      "send message to ",
      BOLD,
      webrtcConn.remotePeerId,
      UNBOLD,
      GREY,
      " (",
      webrtcConn.room.name,
      ")",
      UNCOLOR
    );
    try {
      webrtcConn.peer.send(toUint8Array(encoder));
    } catch (e) {
    }
  };
  var broadcastWebrtcConn = (room, m) => {
    log("broadcast message in ", BOLD, room.name, UNBOLD);
    room.webrtcConns.forEach((conn) => {
      try {
        conn.peer.send(m);
      } catch (e) {
      }
    });
  };
  var WebrtcConn = class {
    /**
     * @param {SignalingConn} signalingConn
     * @param {boolean} initiator
     * @param {string} remotePeerId
     * @param {Room} room
     */
    constructor(signalingConn, initiator, remotePeerId, room) {
      log("establishing connection to ", BOLD, remotePeerId);
      this.room = room;
      this.remotePeerId = remotePeerId;
      this.glareToken = void 0;
      this.closed = false;
      this.connected = false;
      this.synced = false;
      this.peer = new import_simplepeer_min.default({ initiator, ...room.provider.peerOpts });
      this.peer.on("signal", (signal) => {
        if (this.glareToken === void 0) {
          this.glareToken = Date.now() + Math.random();
        }
        publishSignalingMessage(signalingConn, room, {
          to: remotePeerId,
          from: room.peerId,
          type: "signal",
          token: this.glareToken,
          signal
        });
      });
      this.peer.on("connect", () => {
        log("connected to ", BOLD, remotePeerId);
        this.connected = true;
        const provider = room.provider;
        const doc2 = provider.doc;
        const awareness = room.awareness;
        const encoder = createEncoder();
        writeVarUint(encoder, messageSync);
        writeSyncStep1(encoder, doc2);
        sendWebrtcConn(this, encoder);
        const awarenessStates = awareness.getStates();
        if (awarenessStates.size > 0) {
          const encoder2 = createEncoder();
          writeVarUint(encoder2, messageAwareness);
          writeVarUint8Array(
            encoder2,
            encodeAwarenessUpdate(
              awareness,
              Array.from(awarenessStates.keys())
            )
          );
          sendWebrtcConn(this, encoder2);
        }
      });
      this.peer.on("close", () => {
        this.connected = false;
        this.closed = true;
        if (room.webrtcConns.has(this.remotePeerId)) {
          room.webrtcConns.delete(this.remotePeerId);
          room.provider.emit("peers", [
            {
              removed: [this.remotePeerId],
              added: [],
              webrtcPeers: Array.from(room.webrtcConns.keys()),
              bcPeers: Array.from(room.bcConns)
            }
          ]);
        }
        checkIsSynced(room);
        this.peer.destroy();
        log("closed connection to ", BOLD, remotePeerId);
        announceSignalingInfo(room);
      });
      this.peer.on("error", (err) => {
        log(
          "Error in connection to ",
          BOLD,
          remotePeerId,
          ": ",
          err
        );
        announceSignalingInfo(room);
      });
      this.peer.on("data", (data) => {
        const answer = readPeerMessage(this, data);
        if (answer !== null) {
          sendWebrtcConn(this, answer);
        }
      });
    }
    destroy() {
      this.peer.destroy();
    }
  };
  var broadcastBcMessage = (room, m) => encrypt(m, room.key).then((data) => room.mux(() => publish(room.name, data)));
  var broadcastRoomMessage = (room, m) => {
    if (room.bcconnected) {
      broadcastBcMessage(room, m);
    }
    broadcastWebrtcConn(room, m);
  };
  var announceSignalingInfo = (room) => {
    signalingConns.forEach((conn) => {
      if (conn.connected) {
        conn.send({ type: "subscribe", topics: [room.name] });
        if (room.webrtcConns.size < room.provider.maxConns) {
          publishSignalingMessage(conn, room, {
            type: "announce",
            from: room.peerId
          });
        }
      }
    });
  };
  var broadcastBcPeerId = (room) => {
    if (room.provider.filterBcConns) {
      const encoderPeerIdBc = createEncoder();
      writeVarUint(encoderPeerIdBc, messageBcPeerId);
      writeUint8(encoderPeerIdBc, 1);
      writeVarString(encoderPeerIdBc, room.peerId);
      broadcastBcMessage(room, toUint8Array(encoderPeerIdBc));
    }
  };
  var Room = class {
    /**
     * @param {Y.Doc} doc
     * @param {WebrtcProvider} provider
     * @param {string} name
     * @param {CryptoKey|null} key
     */
    constructor(doc2, provider, name, key) {
      this.peerId = uuidv4();
      this.doc = doc2;
      this.awareness = provider.awareness;
      this.provider = provider;
      this.synced = false;
      this.name = name;
      this.key = key;
      this.webrtcConns = /* @__PURE__ */ new Map();
      this.bcConns = /* @__PURE__ */ new Set();
      this.mux = createMutex();
      this.bcconnected = false;
      this._bcSubscriber = (data) => decrypt(new Uint8Array(data), key).then(
        (m) => this.mux(() => {
          const reply = readMessage(this, m, () => {
          });
          if (reply) {
            broadcastBcMessage(
              this,
              toUint8Array(reply)
            );
          }
        })
      );
      this._docUpdateHandler = (update, origin) => {
        const encoder = createEncoder();
        writeVarUint(encoder, messageSync);
        writeUpdate(encoder, update);
        broadcastRoomMessage(this, toUint8Array(encoder));
      };
      this._awarenessUpdateHandler = ({ added, updated, removed }, origin) => {
        const changedClients = added.concat(updated).concat(removed);
        const encoderAwareness = createEncoder();
        writeVarUint(encoderAwareness, messageAwareness);
        writeVarUint8Array(
          encoderAwareness,
          encodeAwarenessUpdate(
            this.awareness,
            changedClients
          )
        );
        broadcastRoomMessage(
          this,
          toUint8Array(encoderAwareness)
        );
      };
      this._beforeUnloadHandler = () => {
        removeAwarenessStates(
          this.awareness,
          [doc2.clientID],
          "window unload"
        );
        rooms.forEach((room) => {
          room.disconnect();
        });
      };
      if (typeof window !== "undefined") {
        window.addEventListener(
          "beforeunload",
          this._beforeUnloadHandler
        );
      } else if (typeof process !== "undefined") {
        process.on("exit", this._beforeUnloadHandler);
      }
    }
    connect() {
      this.doc.on("update", this._docUpdateHandler);
      this.awareness.on("update", this._awarenessUpdateHandler);
      announceSignalingInfo(this);
      const roomName = this.name;
      subscribe(roomName, this._bcSubscriber);
      this.bcconnected = true;
      broadcastBcPeerId(this);
      const encoderSync = createEncoder();
      writeVarUint(encoderSync, messageSync);
      writeSyncStep1(encoderSync, this.doc);
      broadcastBcMessage(this, toUint8Array(encoderSync));
      const encoderState = createEncoder();
      writeVarUint(encoderState, messageSync);
      writeSyncStep2(encoderState, this.doc);
      broadcastBcMessage(this, toUint8Array(encoderState));
      const encoderAwarenessQuery = createEncoder();
      writeVarUint(encoderAwarenessQuery, messageQueryAwareness);
      broadcastBcMessage(
        this,
        toUint8Array(encoderAwarenessQuery)
      );
      const encoderAwarenessState = createEncoder();
      writeVarUint(encoderAwarenessState, messageAwareness);
      writeVarUint8Array(
        encoderAwarenessState,
        encodeAwarenessUpdate(this.awareness, [
          this.doc.clientID
        ])
      );
      broadcastBcMessage(
        this,
        toUint8Array(encoderAwarenessState)
      );
    }
    disconnect() {
      signalingConns.forEach((conn) => {
        if (conn.connected) {
          conn.send({ type: "unsubscribe", topics: [this.name] });
        }
      });
      removeAwarenessStates(
        this.awareness,
        [this.doc.clientID],
        "disconnect"
      );
      const encoderPeerIdBc = createEncoder();
      writeVarUint(encoderPeerIdBc, messageBcPeerId);
      writeUint8(encoderPeerIdBc, 0);
      writeVarString(encoderPeerIdBc, this.peerId);
      broadcastBcMessage(this, toUint8Array(encoderPeerIdBc));
      unsubscribe(this.name, this._bcSubscriber);
      this.bcconnected = false;
      this.doc.off("update", this._docUpdateHandler);
      this.awareness.off("update", this._awarenessUpdateHandler);
      this.webrtcConns.forEach((conn) => conn.destroy());
    }
    destroy() {
      this.disconnect();
      if (typeof window !== "undefined") {
        window.removeEventListener(
          "beforeunload",
          this._beforeUnloadHandler
        );
      } else if (typeof process !== "undefined") {
        process.off("exit", this._beforeUnloadHandler);
      }
    }
  };
  var openRoom = (doc2, provider, name, key) => {
    if (rooms.has(name)) {
      throw create3(
        `A Yjs Doc connected to room "${name}" already exists!`
      );
    }
    const room = new Room(doc2, provider, name, key);
    rooms.set(
      name,
      /** @type {Room} */
      room
    );
    return room;
  };
  var publishSignalingMessage = (conn, room, data) => {
    if (room.key) {
      encryptJson(data, room.key).then((data2) => {
        conn.send({
          type: "publish",
          topic: room.name,
          data: toBase64(data2)
        });
      });
    } else {
      conn.send({ type: "publish", topic: room.name, data });
    }
  };
  var SignalingConn = class extends WebsocketClient {
    constructor(url) {
      super(url);
      this.providers = /* @__PURE__ */ new Set();
      this.on("connect", () => {
        log(`connected (${url})`);
        const topics = Array.from(rooms.keys());
        this.send({ type: "subscribe", topics });
        rooms.forEach(
          (room) => publishSignalingMessage(this, room, {
            type: "announce",
            from: room.peerId
          })
        );
      });
      this.on("message", (m) => {
        switch (m.type) {
          case "publish": {
            const roomName = m.topic;
            const room = rooms.get(roomName);
            if (room == null || typeof roomName !== "string") {
              return;
            }
            const execMessage = (data) => {
              const webrtcConns = room.webrtcConns;
              const peerId = room.peerId;
              if (data == null || data.from === peerId || data.to !== void 0 && data.to !== peerId || room.bcConns.has(data.from)) {
                return;
              }
              const emitPeerChange = webrtcConns.has(data.from) ? () => {
              } : () => room.provider.emit("peers", [
                {
                  removed: [],
                  added: [data.from],
                  webrtcPeers: Array.from(
                    room.webrtcConns.keys()
                  ),
                  bcPeers: Array.from(room.bcConns)
                }
              ]);
              switch (data.type) {
                case "announce":
                  if (webrtcConns.size < room.provider.maxConns) {
                    setIfUndefined(
                      webrtcConns,
                      data.from,
                      () => new WebrtcConn(
                        this,
                        true,
                        data.from,
                        room
                      )
                    );
                    emitPeerChange();
                  }
                  break;
                case "signal":
                  if (data.signal.type === "offer") {
                    const existingConn = webrtcConns.get(
                      data.from
                    );
                    if (existingConn) {
                      const remoteToken = data.token;
                      const localToken = existingConn.glareToken;
                      if (localToken && localToken > remoteToken) {
                        log(
                          "offer rejected: ",
                          data.from
                        );
                        return;
                      }
                      existingConn.glareToken = void 0;
                    }
                  }
                  if (data.signal.type === "answer") {
                    log("offer answered by: ", data.from);
                    const existingConn = webrtcConns.get(
                      data.from
                    );
                    existingConn.glareToken = void 0;
                  }
                  if (data.to === peerId) {
                    setIfUndefined(
                      webrtcConns,
                      data.from,
                      () => new WebrtcConn(
                        this,
                        false,
                        data.from,
                        room
                      )
                    ).peer.signal(data.signal);
                    emitPeerChange();
                  }
                  break;
              }
            };
            if (room.key) {
              if (typeof m.data === "string") {
                decryptJson(
                  fromBase64(m.data),
                  room.key
                ).then(execMessage);
              }
            } else {
              execMessage(m.data);
            }
          }
        }
      });
      this.on("disconnect", () => log(`disconnect (${url})`));
    }
  };
  var WebrtcProvider = class extends Observable {
    /**
     * @param {string} roomName
     * @param {Y.Doc} doc
     * @param {ProviderOptions?} opts
     */
    constructor(roomName, doc2, {
      signaling = ["wss://y-webrtc-eu.fly.dev"],
      password = null,
      awareness = new Awareness(doc2),
      maxConns = 20 + floor(rand() * 15),
      // the random factor reduces the chance that n clients form a cluster
      filterBcConns = true,
      peerOpts = {}
      // simple-peer options. See https://github.com/feross/simple-peer#peer--new-peeropts
    } = {}) {
      super();
      this.roomName = roomName;
      this.doc = doc2;
      this.filterBcConns = filterBcConns;
      this.awareness = awareness;
      this.shouldConnect = false;
      this.signalingUrls = signaling;
      this.signalingConns = [];
      this.maxConns = maxConns;
      this.peerOpts = peerOpts;
      this.key = password ? deriveKey(password, roomName) : (
        /** @type {PromiseLike<null>} */
        resolve(null)
      );
      this.room = null;
      this.key.then((key) => {
        this.room = openRoom(doc2, this, roomName, key);
        if (this.shouldConnect) {
          this.room.connect();
        } else {
          this.room.disconnect();
        }
      });
      this.connect();
      this.destroy = this.destroy.bind(this);
      doc2.on("destroy", this.destroy);
    }
    /**
     * @type {boolean}
     */
    get connected() {
      return this.room !== null && this.shouldConnect;
    }
    connect() {
      this.shouldConnect = true;
      this.signalingUrls.forEach((url) => {
        const signalingConn = setIfUndefined(
          signalingConns,
          url,
          () => new SignalingConn(url)
        );
        this.signalingConns.push(signalingConn);
        signalingConn.providers.add(this);
      });
      if (this.room) {
        this.room.connect();
      }
    }
    disconnect() {
      this.shouldConnect = false;
      this.signalingConns.forEach((conn) => {
        conn.providers.delete(this);
        if (conn.providers.size === 0) {
          conn.destroy();
          signalingConns.delete(conn.url);
        }
      });
      if (this.room) {
        this.room.disconnect();
      }
    }
    destroy() {
      this.doc.off("destroy", this.destroy);
      this.key.then(() => {
        this.room.destroy();
        rooms.delete(this.roomName);
      });
      super.destroy();
    }
  };

  // packages/sync/build-module/providers/webrtc-http-stream-signaling.js
  var import_url2 = __toESM(require_url());
  function setupSignalEventHandlers(signalCon, url) {
    signalCon.on("connect", () => {
      log(`connected (${url})`);
      const topics = Array.from(rooms.keys());
      signalCon.send({ type: "subscribe", topics });
      rooms.forEach(
        (room) => publishSignalingMessage(signalCon, room, {
          type: "announce",
          from: room.peerId
        })
      );
    });
    signalCon.on(
      "message",
      (m) => {
        switch (m.type) {
          case "publish": {
            const roomName = m.topic;
            const room = rooms.get(roomName);
            if (room === null || typeof roomName !== "string" || room === void 0) {
              return;
            }
            const execMessage = (data) => {
              const webrtcConns = room.webrtcConns;
              const peerId = room.peerId;
              if (data === null || data.from === peerId || data.to !== void 0 && data.to !== peerId || room.bcConns.has(data.from)) {
                return;
              }
              const emitPeerChange = webrtcConns.has(data.from) ? () => {
              } : () => room.provider.emit("peers", [
                {
                  removed: [],
                  added: [data.from],
                  webrtcPeers: Array.from(
                    room.webrtcConns.keys()
                  ),
                  bcPeers: Array.from(room.bcConns)
                }
              ]);
              switch (data.type) {
                case "announce":
                  if (webrtcConns.size < room.provider.maxConns) {
                    setIfUndefined(
                      webrtcConns,
                      data.from,
                      () => new WebrtcConn(
                        signalCon,
                        true,
                        data.from,
                        room
                      )
                    );
                    emitPeerChange();
                  }
                  break;
                case "signal":
                  if (data.signal.type === "offer") {
                    const existingConn = webrtcConns.get(
                      data.from
                    );
                    if (existingConn) {
                      const remoteToken = data.token;
                      const localToken = existingConn.glareToken;
                      if (localToken && localToken > remoteToken) {
                        log(
                          "offer rejected: ",
                          data.from
                        );
                        return;
                      }
                      existingConn.glareToken = void 0;
                    }
                  }
                  if (data.signal.type === "answer") {
                    log("offer answered by: ", data.from);
                    const existingConn = webrtcConns.get(
                      data.from
                    );
                    if (existingConn) {
                      existingConn.glareToken = void 0;
                    }
                  }
                  if (data.to === peerId) {
                    setIfUndefined(
                      webrtcConns,
                      data.from,
                      () => new WebrtcConn(
                        signalCon,
                        false,
                        data.from,
                        room
                      )
                    ).peer.signal(data.signal);
                    emitPeerChange();
                  }
                  break;
              }
            };
            if (room.key) {
              if (typeof m.data === "string") {
                decryptJson(
                  fromBase64(m.data),
                  room.key
                ).then(execMessage);
              }
            } else {
              execMessage(m.data);
            }
          }
        }
      }
    );
    signalCon.on("disconnect", () => log(`disconnect (${url})`));
  }
  function setupHttpSignal(httpClient) {
    if (httpClient.shouldConnect && httpClient.ws === null) {
      const subscriberId = Math.floor(1e5 + Math.random() * 9e5);
      const url = httpClient.url;
      const eventSource = new window.EventSource(
        (0, import_url2.addQueryArgs)(url, {
          subscriber_id: subscriberId,
          action: "gutenberg_signaling_server"
        })
      );
      let pingTimeout = null;
      eventSource.onmessage = (event) => {
        httpClient.lastMessageReceived = Date.now();
        const data = event.data;
        if (data) {
          const messages = JSON.parse(data);
          if (Array.isArray(messages)) {
            messages.forEach(onSingleMessage);
          }
        }
      };
      httpClient.ws = eventSource;
      httpClient.connecting = true;
      httpClient.connected = false;
      const onSingleMessage = (message) => {
        if (message && message.type === "pong") {
          clearTimeout(pingTimeout);
          pingTimeout = setTimeout(
            sendPing,
            messageReconnectTimeout2 / 2
          );
        }
        httpClient.emit("message", [message, httpClient]);
      };
      const onclose = (error) => {
        if (httpClient.ws !== null) {
          httpClient.ws.close();
          httpClient.ws = null;
          httpClient.connecting = false;
          if (httpClient.connected) {
            httpClient.connected = false;
            httpClient.emit("disconnect", [
              { type: "disconnect", error },
              httpClient
            ]);
          } else {
            httpClient.unsuccessfulReconnects++;
          }
        }
        clearTimeout(pingTimeout);
      };
      const sendPing = () => {
        if (httpClient.ws && httpClient.ws.readyState === window.EventSource.OPEN) {
          httpClient.send({
            type: "ping"
          });
        }
      };
      if (httpClient.ws) {
        httpClient.ws.onclose = () => {
          onclose(null);
        };
        httpClient.ws.send = function send(message) {
          window.fetch(url, {
            body: new URLSearchParams({
              subscriber_id: subscriberId.toString(),
              action: "gutenberg_signaling_server",
              message
            }),
            method: "POST"
          }).catch(() => {
            log(
              "Error sending to server with message: " + message
            );
          });
        };
      }
      eventSource.onerror = () => {
      };
      eventSource.onopen = () => {
        if (httpClient.connected) {
          return;
        }
        if (eventSource.readyState === window.EventSource.OPEN) {
          httpClient.lastMessageReceived = Date.now();
          httpClient.connecting = false;
          httpClient.connected = true;
          httpClient.unsuccessfulReconnects = 0;
          httpClient.emit("connect", [
            { type: "connect" },
            httpClient
          ]);
          pingTimeout = setTimeout(
            sendPing,
            messageReconnectTimeout2 / 2
          );
        }
      };
    }
  }
  var messageReconnectTimeout2 = 3e4;
  var HttpSignalingConn = class extends Observable {
    /**
     * @param {string} url
     */
    constructor(url) {
      super();
      this.url = url;
      this.ws = null;
      this.binaryType = null;
      this.connected = false;
      this.connecting = false;
      this.unsuccessfulReconnects = 0;
      this.lastMessageReceived = 0;
      this.shouldConnect = true;
      this._checkInterval = setInterval(() => {
        if (this.connected && messageReconnectTimeout2 < Date.now() - this.lastMessageReceived && this.ws) {
          this.ws.close();
        }
      }, messageReconnectTimeout2 / 2);
      setupHttpSignal(this);
      this.providers = /* @__PURE__ */ new Set();
      setupSignalEventHandlers(this, url);
    }
    /**
     * @param {any} message
     */
    send(message) {
      if (this.ws) {
        this.ws.send(JSON.stringify(message));
      }
    }
    destroy() {
      clearInterval(this._checkInterval);
      this.disconnect();
      super.destroy();
    }
    disconnect() {
      this.shouldConnect = false;
      if (this.ws !== null) {
        this.ws.close();
      }
    }
    connect() {
      this.shouldConnect = true;
      if (!this.connected && this.ws === null) {
        setupHttpSignal(this);
      }
    }
  };
  var WebrtcProviderWithHttpSignaling = class extends WebrtcProvider {
    connect() {
      this.shouldConnect = true;
      this.signalingUrls.forEach((url) => {
        const signalingConn = setIfUndefined(
          signalingConns,
          url,
          // Only this conditional logic to create a normal websocket connection or
          // an http signaling connection was added to the constructor when compared
          // with the base class.
          url.startsWith("ws://") || url.startsWith("wss://") ? () => new SignalingConn(url) : () => new HttpSignalingConn(url)
        );
        this.signalingConns.push(signalingConn);
        signalingConn.providers.add(this);
      });
      if (this.room) {
        this.room.connect();
      }
    }
  };

  // packages/sync/build-module/providers/webrtc-provider.js
  function createWebRTCProvider({ signaling, password }) {
    return function(objectType, objectId, doc2) {
      const roomName = `${objectType}-${objectId}`;
      const provider = new WebrtcProviderWithHttpSignaling(roomName, doc2, {
        signaling,
        password
      });
      return Promise.resolve({
        destroy: () => provider.destroy()
      });
    };
  }

  // packages/sync/build-module/providers/index.js
  var providerCreators = null;
  function getDefaultProviderCreators() {
    const signalingUrl = window?.wp?.ajax?.settings?.url;
    if (!signalingUrl) {
      return [];
    }
    return [
      createIndexedDbProvider,
      createWebRTCProvider({
        password: window?.__experimentalCollaborativeEditingSecret,
        signaling: [signalingUrl]
      })
    ];
  }
  function isProviderCreator(creator) {
    return "function" === typeof creator;
  }
  function getProviderCreators() {
    if (providerCreators) {
      return providerCreators;
    }
    const filteredProviderCreators = (0, import_hooks.applyFilters)(
      "sync.providers",
      getDefaultProviderCreators()
    );
    if (!Array.isArray(filteredProviderCreators)) {
      providerCreators = [];
      return providerCreators;
    }
    providerCreators = filteredProviderCreators.filter(isProviderCreator);
    return providerCreators;
  }

  // packages/sync/build-module/manager.js
  function createSyncManager() {
    const entityStates = /* @__PURE__ */ new Map();
    async function loadEntity(syncConfig, objectType, objectId, record, handlers) {
      const providerCreators2 = getProviderCreators();
      if (0 === providerCreators2.length) {
        return;
      }
      const entityId = getEntityId(objectType, objectId);
      if (entityStates.has(entityId)) {
        return;
      }
      const ydoc = createYjsDoc({ objectType });
      const recordMap = ydoc.getMap(CRDT_RECORD_MAP_KEY);
      const unload = () => {
        providerResults.forEach((result) => result.destroy());
        recordMap.unobserveDeep(onRecordUpdate);
        ydoc.destroy();
        entityStates.delete(entityId);
      };
      const onRecordUpdate = (_events, transaction) => {
        if (transaction.local && !(transaction.origin instanceof UndoManager)) {
          return;
        }
        void updateEntityRecord(objectType, objectId);
      };
      const entityState = {
        handlers,
        objectId,
        objectType,
        syncConfig,
        unload,
        ydoc
      };
      entityStates.set(entityId, entityState);
      const providerResults = await Promise.all(
        providerCreators2.map(
          (create8) => create8(objectType, objectId, ydoc)
        )
      );
      recordMap.observeDeep(onRecordUpdate);
      const isInvalid = applyPersistedCrdtDoc(syncConfig, ydoc, record);
      if (isInvalid) {
        ydoc.transact(() => {
          syncConfig.applyChangesToCRDTDoc(ydoc, record);
        }, LOCAL_SYNC_MANAGER_ORIGIN);
        const meta = createEntityMeta(objectType, objectId);
        handlers.editRecord({ meta });
        handlers.saveRecord();
      }
    }
    function unloadEntity(objectType, objectId) {
      entityStates.get(getEntityId(objectType, objectId))?.unload();
    }
    function getEntityId(objectType, objectId) {
      return `${objectType}_${objectId}`;
    }
    function applyPersistedCrdtDoc(syncConfig, targetDoc, record) {
      if (!syncConfig.supports?.crdtPersistence) {
        return true;
      }
      const tempDoc = getPersistedCrdtDoc(record);
      if (!tempDoc) {
        return true;
      }
      const update = encodeStateAsUpdateV2(tempDoc);
      targetDoc.transact(() => {
        applyUpdateV2(targetDoc, update);
      }, LOCAL_SYNC_MANAGER_ORIGIN);
      const changes = syncConfig.getChangesFromCRDTDoc(tempDoc, record);
      tempDoc.destroy();
      return Object.keys(changes).length > 0;
    }
    function updateCRDTDoc(objectType, objectId, changes, origin) {
      const entityId = getEntityId(objectType, objectId);
      const entityState = entityStates.get(entityId);
      if (!entityState) {
        return;
      }
      const { syncConfig, ydoc } = entityState;
      ydoc.transact(() => {
        syncConfig.applyChangesToCRDTDoc(ydoc, changes);
      }, origin);
    }
    async function updateEntityRecord(objectType, objectId) {
      const entityId = getEntityId(objectType, objectId);
      const entityState = entityStates.get(entityId);
      if (!entityState) {
        return;
      }
      const { handlers, syncConfig, ydoc } = entityState;
      const changes = syncConfig.getChangesFromCRDTDoc(
        ydoc,
        await handlers.getEditedRecord()
      );
      if (0 === Object.keys(changes).length) {
        return;
      }
      handlers.editRecord(changes);
    }
    function createEntityMeta(objectType, objectId) {
      const entityId = getEntityId(objectType, objectId);
      const entityState = entityStates.get(entityId);
      if (!entityState?.syncConfig.supports?.crdtPersistence) {
        return {};
      }
      return createPersistedCRDTDoc(entityState.ydoc);
    }
    return {
      createMeta: createEntityMeta,
      load: loadEntity,
      unload: unloadEntity,
      update: updateCRDTDoc
    };
  }

  // packages/core-data/build-module/sync.js
  var syncManager;
  function getSyncManager() {
    if (syncManager) {
      return syncManager;
    }
    syncManager = createSyncManager();
    return syncManager;
  }

  // packages/core-data/build-module/utils/crdt.js
  var import_es63 = __toESM(require_es6());
  var import_blocks2 = __toESM(require_blocks());

  // node_modules/uuid/dist/esm-browser/rng.js
  var getRandomValues2;
  var rnds8 = new Uint8Array(16);
  function rng() {
    if (!getRandomValues2) {
      getRandomValues2 = typeof crypto !== "undefined" && crypto.getRandomValues && crypto.getRandomValues.bind(crypto);
      if (!getRandomValues2) {
        throw new Error("crypto.getRandomValues() not supported. See https://github.com/uuidjs/uuid#getrandomvalues-not-supported");
      }
    }
    return getRandomValues2(rnds8);
  }

  // node_modules/uuid/dist/esm-browser/stringify.js
  var byteToHex = [];
  for (let i = 0; i < 256; ++i) {
    byteToHex.push((i + 256).toString(16).slice(1));
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
      for (let i = 0; i < 16; ++i) {
        buf[offset + i] = rnds[i];
      }
      return buf;
    }
    return unsafeStringify(rnds);
  }
  var v4_default = v4;

  // packages/core-data/build-module/utils/crdt-blocks.js
  var import_es62 = __toESM(require_es6());
  var import_rich_text = __toESM(require_rich_text());
  var import_blocks = __toESM(require_blocks());
  var serializableBlocksCache = /* @__PURE__ */ new WeakMap();
  function makeBlockAttributesSerializable(attributes) {
    const newAttributes = { ...attributes };
    for (const [key, value] of Object.entries(attributes)) {
      if (value instanceof import_rich_text.RichTextData) {
        newAttributes[key] = value.valueOf();
      }
    }
    return newAttributes;
  }
  function makeBlocksSerializable(blocks) {
    return blocks.map((block) => {
      const blockAsJson = block instanceof yjs_exports.Map ? block.toJSON() : block;
      const { name, innerBlocks, attributes, ...rest } = blockAsJson;
      delete rest.validationIssues;
      delete rest.originalContent;
      return {
        ...rest,
        name,
        attributes: makeBlockAttributesSerializable(attributes),
        innerBlocks: makeBlocksSerializable(innerBlocks)
      };
    });
  }
  function areBlocksEqual(gblock, yblock) {
    const yblockAsJson = yblock.toJSON();
    const overwrites = {
      innerBlocks: null,
      clientId: null
    };
    const res = (0, import_es62.default)(
      Object.assign({}, gblock, overwrites),
      Object.assign({}, yblockAsJson, overwrites)
    );
    const inners = gblock.innerBlocks || [];
    const yinners = yblock.get("innerBlocks");
    return res && inners.length === yinners.length && inners.every(
      (block, i) => areBlocksEqual(block, yinners.get(i))
    );
  }
  function createNewYAttributeMap(blockName, attributes) {
    return new yjs_exports.Map(
      Object.entries(attributes).map(
        ([attributeName, attributeValue]) => {
          return [
            attributeName,
            createNewYAttributeValue(
              blockName,
              attributeName,
              attributeValue
            )
          ];
        }
      )
    );
  }
  function createNewYAttributeValue(blockName, attributeName, attributeValue) {
    const isRichText = isRichTextAttribute(blockName, attributeName);
    if (isRichText) {
      return new yjs_exports.Text(attributeValue?.toString() ?? "");
    }
    return attributeValue;
  }
  function createNewYBlock(block) {
    return new yjs_exports.Map(
      Object.entries(block).map(([key, value]) => {
        switch (key) {
          case "attributes": {
            return [key, createNewYAttributeMap(block.name, value)];
          }
          case "innerBlocks": {
            const innerBlocks = new yjs_exports.Array();
            if (!Array.isArray(value)) {
              return [key, innerBlocks];
            }
            innerBlocks.insert(
              0,
              value.map(
                (innerBlock) => createNewYBlock(innerBlock)
              )
            );
            return [key, innerBlocks];
          }
          default:
            return [key, value];
        }
      })
    );
  }
  function mergeCrdtBlocks(yblocks, incomingBlocks, lastSelection2) {
    if (!serializableBlocksCache.has(incomingBlocks)) {
      serializableBlocksCache.set(
        incomingBlocks,
        makeBlocksSerializable(incomingBlocks)
      );
    }
    const allBlocks = serializableBlocksCache.get(incomingBlocks) ?? [];
    const blocksToSync = allBlocks.filter(
      (block) => shouldBlockBeSynced(block)
    );
    const numOfCommonEntries = Math.min(
      blocksToSync.length ?? 0,
      yblocks.length
    );
    let left = 0;
    let right = 0;
    for (; left < numOfCommonEntries && areBlocksEqual(blocksToSync[left], yblocks.get(left)); left++) {
    }
    for (; right < numOfCommonEntries - left && areBlocksEqual(
      blocksToSync[blocksToSync.length - right - 1],
      yblocks.get(yblocks.length - right - 1)
    ); right++) {
    }
    const numOfUpdatesNeeded = numOfCommonEntries - left - right;
    const numOfInsertionsNeeded = Math.max(
      0,
      blocksToSync.length - yblocks.length
    );
    const numOfDeletionsNeeded = Math.max(
      0,
      yblocks.length - blocksToSync.length
    );
    for (let i = 0; i < numOfUpdatesNeeded; i++, left++) {
      const block = blocksToSync[left];
      const yblock = yblocks.get(left);
      Object.entries(block).forEach(([key, value]) => {
        switch (key) {
          case "attributes": {
            const currentAttributes = yblock.get(
              key
            );
            if (!currentAttributes) {
              yblock.set(
                key,
                createNewYAttributeMap(block.name, value)
              );
              break;
            }
            Object.entries(value).forEach(
              ([attributeName, attributeValue]) => {
                if ((0, import_es62.default)(
                  currentAttributes?.get(attributeName),
                  attributeValue
                )) {
                  return;
                }
                const isRichText = isRichTextAttribute(
                  block.name,
                  attributeName
                );
                if (isRichText && "string" === typeof attributeValue) {
                  const blockYText = currentAttributes.get(
                    attributeName
                  );
                  mergeRichTextUpdate(
                    blockYText,
                    attributeValue,
                    lastSelection2
                  );
                } else {
                  currentAttributes.set(
                    attributeName,
                    createNewYAttributeValue(
                      block.name,
                      attributeName,
                      attributeValue
                    )
                  );
                }
              }
            );
            currentAttributes.forEach(
              (_attrValue, attrName) => {
                if (!value.hasOwnProperty(attrName)) {
                  currentAttributes.delete(attrName);
                }
              }
            );
            break;
          }
          case "innerBlocks": {
            const yInnerBlocks = yblock.get(key);
            mergeCrdtBlocks(yInnerBlocks, value ?? [], lastSelection2);
            break;
          }
          default:
            if (!(0, import_es62.default)(block[key], yblock.get(key))) {
              yblock.set(key, value);
            }
        }
      });
      yblock.forEach((_v, k) => {
        if (!block.hasOwnProperty(k)) {
          yblock.delete(k);
        }
      });
    }
    yblocks.delete(left, numOfDeletionsNeeded);
    for (let i = 0; i < numOfInsertionsNeeded; i++, left++) {
      const newBlock = [createNewYBlock(blocksToSync[left])];
      yblocks.insert(left, newBlock);
    }
    const knownClientIds = /* @__PURE__ */ new Set();
    for (let j = 0; j < yblocks.length; j++) {
      const yblock = yblocks.get(j);
      let clientId = yblock.get("clientId");
      if (knownClientIds.has(clientId)) {
        clientId = v4_default();
        yblock.set("clientId", clientId);
      }
      knownClientIds.add(clientId);
    }
  }
  function shouldBlockBeSynced(block) {
    if ("core/gallery" === block.name) {
      return !block.innerBlocks.some(
        (innerBlock) => innerBlock.attributes && innerBlock.attributes.blob
      );
    }
    return true;
  }
  var cachedRichTextAttributes;
  function isRichTextAttribute(blockName, attributeName) {
    if (!cachedRichTextAttributes) {
      cachedRichTextAttributes = /* @__PURE__ */ new Map();
      for (const blockType of (0, import_blocks.getBlockTypes)()) {
        const richTextAttributeMap = /* @__PURE__ */ new Map();
        for (const [name, definition] of Object.entries(
          blockType.attributes ?? {}
        )) {
          if ("rich-text" === definition.type) {
            richTextAttributeMap.set(name, true);
          }
        }
        cachedRichTextAttributes.set(
          blockType.name,
          richTextAttributeMap
        );
      }
    }
    return cachedRichTextAttributes.get(blockName)?.has(attributeName) ?? false;
  }
  function mergeRichTextUpdate(blockYText, updatedValue, lastSelection2) {
    blockYText.delete(0, blockYText.toString().length);
    blockYText.insert(0, updatedValue);
  }

  // packages/core-data/build-module/utils/crdt.js
  var lastSelection = null;
  var allowedPostProperties = /* @__PURE__ */ new Set([
    "author",
    "blocks",
    "comment_status",
    "date",
    "excerpt",
    "featured_media",
    "format",
    "ping_status",
    "meta",
    "slug",
    "status",
    "sticky",
    "tags",
    "template",
    "title"
  ]);
  var disallowedPostMetaKeys = /* @__PURE__ */ new Set([
    WORDPRESS_META_KEY_FOR_CRDT_DOC_PERSISTENCE
  ]);
  function defaultApplyChangesToCRDTDoc(ydoc, changes) {
    const ymap = ydoc.getMap(CRDT_RECORD_MAP_KEY);
    Object.entries(changes).forEach(([key, newValue]) => {
      if ("function" === typeof newValue) {
        return;
      }
      function setValue(updatedValue) {
        ymap.set(key, updatedValue);
      }
      switch (key) {
        // Add support for additional data types here.
        default: {
          const currentValue = ymap.get(key);
          mergeValue(currentValue, newValue, setValue);
        }
      }
    });
  }
  function applyPostChangesToCRDTDoc(ydoc, changes, _postType) {
    const ymap = ydoc.getMap(CRDT_RECORD_MAP_KEY);
    Object.entries(changes).forEach(([key, newValue]) => {
      if (!allowedPostProperties.has(key)) {
        return;
      }
      if ("function" === typeof newValue) {
        return;
      }
      function setValue(updatedValue) {
        ymap.set(key, updatedValue);
      }
      switch (key) {
        case "blocks": {
          let currentBlocks = ymap.get("blocks");
          if (!(currentBlocks instanceof yjs_exports.Array)) {
            currentBlocks = new yjs_exports.Array();
            setValue(currentBlocks);
          }
          const newBlocks = newValue ?? [];
          mergeCrdtBlocks(currentBlocks, newBlocks, lastSelection);
          break;
        }
        case "excerpt": {
          const currentValue = ymap.get("excerpt");
          const rawNewValue = getRawValue(newValue);
          mergeValue(currentValue, rawNewValue, setValue);
          break;
        }
        // "Meta" is overloaded term; here, it refers to post meta.
        case "meta": {
          let metaMap = ymap.get("meta");
          if (!(metaMap instanceof yjs_exports.Map)) {
            metaMap = new yjs_exports.Map();
            setValue(metaMap);
          }
          Object.entries(newValue ?? {}).forEach(
            ([metaKey, metaValue]) => {
              if (disallowedPostMetaKeys.has(metaKey)) {
                return;
              }
              mergeValue(
                metaMap.get(metaKey),
                // current value in CRDT
                metaValue,
                // new value from changes
                (updatedMetaValue) => {
                  metaMap.set(metaKey, updatedMetaValue);
                }
              );
            }
          );
          break;
        }
        case "slug": {
          if (!newValue) {
            break;
          }
          const currentValue = ymap.get("slug");
          mergeValue(currentValue, newValue, setValue);
          break;
        }
        case "title": {
          const currentValue = ymap.get("title");
          let rawNewValue = getRawValue(newValue);
          if (!currentValue && "Auto Draft" === rawNewValue) {
            rawNewValue = "";
          }
          mergeValue(currentValue, rawNewValue, setValue);
          break;
        }
        // Add support for additional data types here.
        default: {
          const currentValue = ymap.get(key);
          mergeValue(currentValue, newValue, setValue);
        }
      }
    });
    if ("selection" in changes) {
      lastSelection = changes.selection?.selectionStart ?? null;
    }
  }
  function defaultGetChangesFromCRDTDoc(crdtDoc) {
    return crdtDoc.getMap(CRDT_RECORD_MAP_KEY).toJSON();
  }
  function getPostChangesFromCRDTDoc(ydoc, editedRecord, _postType) {
    const ymap = ydoc.getMap(CRDT_RECORD_MAP_KEY);
    let allowedMetaChanges = {};
    const changes = Object.fromEntries(
      Object.entries(ymap.toJSON()).filter(([key, newValue]) => {
        if (!allowedPostProperties.has(key)) {
          return false;
        }
        const currentValue = editedRecord[key];
        switch (key) {
          case "blocks": {
            if (ydoc.meta?.get(CRDT_DOC_META_PERSISTENCE_KEY) && editedRecord.content) {
              const blocks = ymap.get("blocks");
              return (0, import_blocks2.__unstableSerializeAndClean)(
                blocks.toJSON()
              ).trim() !== editedRecord.content.raw.trim();
            }
            return true;
          }
          case "date": {
            const currentDateIsFloating = ["draft", "auto-draft", "pending"].includes(
              ymap.get("status")
            ) && (null === currentValue || editedRecord.modified === currentValue);
            if (!newValue && currentDateIsFloating) {
              return false;
            }
            return haveValuesChanged(currentValue, newValue);
          }
          case "meta": {
            allowedMetaChanges = Object.fromEntries(
              Object.entries(newValue ?? {}).filter(
                ([metaKey]) => !disallowedPostMetaKeys.has(metaKey)
              )
            );
            const mergedValue = {
              ...currentValue,
              ...allowedMetaChanges
            };
            return haveValuesChanged(currentValue, mergedValue);
          }
          case "status": {
            if ("auto-draft" === newValue) {
              return false;
            }
            return haveValuesChanged(currentValue, newValue);
          }
          case "excerpt":
          case "title": {
            return haveValuesChanged(
              getRawValue(currentValue),
              newValue
            );
          }
          // Add support for additional data types here.
          default: {
            return haveValuesChanged(currentValue, newValue);
          }
        }
      })
    );
    if ("object" === typeof changes.meta) {
      changes.meta = {
        ...editedRecord.meta,
        ...allowedMetaChanges
      };
    }
    return changes;
  }
  function getRawValue(value) {
    if ("string" === typeof value) {
      return value;
    }
    if (value && "object" === typeof value && "raw" in value && "string" === typeof value.raw) {
      return value.raw;
    }
    return void 0;
  }
  function haveValuesChanged(currentValue, newValue) {
    return !(0, import_es63.default)(currentValue, newValue);
  }
  function mergeValue(currentValue, newValue, setValue) {
    if (haveValuesChanged(currentValue, newValue)) {
      setValue(newValue);
    }
  }

  // packages/core-data/build-module/entities.js
  var DEFAULT_ENTITY_KEY = "id";
  var POST_RAW_ATTRIBUTES = ["title", "excerpt", "content"];
  var blocksTransientEdits = {
    blocks: {
      read: (record) => (0, import_blocks3.parse)(record.content?.raw ?? ""),
      write: (record) => ({
        content: (0, import_blocks3.__unstableSerializeAndClean)(record.blocks)
      })
    }
  };
  var rootEntitiesConfig = [
    {
      label: (0, import_i18n.__)("Base"),
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
      label: (0, import_i18n.__)("Post Type"),
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
      label: (0, import_i18n.__)("Media"),
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
      label: (0, import_i18n.__)("Taxonomy")
    },
    {
      name: "sidebar",
      kind: "root",
      baseURL: "/wp/v2/sidebars",
      baseURLParams: { context: "edit" },
      plural: "sidebars",
      transientEdits: { blocks: true },
      label: (0, import_i18n.__)("Widget areas")
    },
    {
      name: "widget",
      kind: "root",
      baseURL: "/wp/v2/widgets",
      baseURLParams: { context: "edit" },
      plural: "widgets",
      transientEdits: { blocks: true },
      label: (0, import_i18n.__)("Widgets")
    },
    {
      name: "widgetType",
      kind: "root",
      baseURL: "/wp/v2/widget-types",
      baseURLParams: { context: "edit" },
      plural: "widgetTypes",
      label: (0, import_i18n.__)("Widget types")
    },
    {
      label: (0, import_i18n.__)("User"),
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
      label: (0, import_i18n.__)("Comment"),
      supportsPagination: true
    },
    {
      name: "menu",
      kind: "root",
      baseURL: "/wp/v2/menus",
      baseURLParams: { context: "edit" },
      plural: "menus",
      label: (0, import_i18n.__)("Menu"),
      supportsPagination: true
    },
    {
      name: "menuItem",
      kind: "root",
      baseURL: "/wp/v2/menu-items",
      baseURLParams: { context: "edit" },
      plural: "menuItems",
      label: (0, import_i18n.__)("Menu Item"),
      rawAttributes: ["title"],
      supportsPagination: true
    },
    {
      name: "menuLocation",
      kind: "root",
      baseURL: "/wp/v2/menu-locations",
      baseURLParams: { context: "edit" },
      plural: "menuLocations",
      label: (0, import_i18n.__)("Menu Location"),
      key: "name"
    },
    {
      label: (0, import_i18n.__)("Global Styles"),
      name: "globalStyles",
      kind: "root",
      baseURL: "/wp/v2/global-styles",
      baseURLParams: { context: "edit" },
      plural: "globalStylesVariations",
      // Should be different from name.
      getTitle: () => (0, import_i18n.__)("Custom Styles"),
      getRevisionsUrl: (parentId, revisionId) => `/wp/v2/global-styles/${parentId}/revisions${revisionId ? "/" + revisionId : ""}`,
      supportsPagination: true
    },
    {
      label: (0, import_i18n.__)("Themes"),
      name: "theme",
      kind: "root",
      baseURL: "/wp/v2/themes",
      baseURLParams: { context: "edit" },
      plural: "themes",
      key: "stylesheet"
    },
    {
      label: (0, import_i18n.__)("Plugins"),
      name: "plugin",
      kind: "root",
      baseURL: "/wp/v2/plugins",
      baseURLParams: { context: "edit" },
      plural: "plugins",
      key: "plugin"
    },
    {
      label: (0, import_i18n.__)("Status"),
      name: "status",
      kind: "root",
      baseURL: "/wp/v2/statuses",
      baseURLParams: { context: "edit" },
      plural: "statuses",
      key: "slug"
    },
    {
      label: (0, import_i18n.__)("Registered Templates"),
      name: "registeredTemplate",
      kind: "root",
      baseURL: "/wp/v2/registered-templates",
      key: "id"
    }
  ];
  var deprecatedEntities = {
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
  var additionalEntityConfigLoaders = [
    { kind: "postType", loadEntities: loadPostTypeEntities },
    { kind: "taxonomy", loadEntities: loadTaxonomyEntities },
    {
      kind: "root",
      name: "site",
      plural: "sites",
      loadEntities: loadSiteEntity
    }
  ];
  var prePersistPostType = (persistedRecord, edits, name, isTemplate) => {
    const newEdits = {};
    if (!isTemplate && persistedRecord?.status === "auto-draft") {
      if (!edits.status && !newEdits.status) {
        newEdits.status = "draft";
      }
      if ((!edits.title || edits.title === "Auto Draft") && !newEdits.title && (!persistedRecord?.title || persistedRecord?.title === "Auto Draft")) {
        newEdits.title = "";
      }
    }
    if (persistedRecord && window.__experimentalEnableSync) {
      if (true) {
        const objectType = `postType/${name}`;
        const objectId = persistedRecord.id;
        const meta = getSyncManager()?.createMeta(objectType, objectId);
        newEdits.meta = {
          ...edits.meta,
          ...meta
        };
      }
    }
    return newEdits;
  };
  async function loadPostTypeEntities() {
    const postTypes = await (0, import_api_fetch.default)({
      path: "/wp/v2/types?context=view"
    });
    return Object.entries(postTypes ?? {}).map(([name, postType]) => {
      const isTemplate = ["wp_template", "wp_template_part"].includes(
        name
      );
      const namespace = postType?.rest_namespace ?? "wp/v2";
      const entity2 = {
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
        __unstablePrePersist: (persistedRecord, edits) => prePersistPostType(persistedRecord, edits, name, isTemplate),
        __unstable_rest_base: postType.rest_base,
        supportsPagination: true,
        getRevisionsUrl: (parentId, revisionId) => `/${namespace}/${postType.rest_base}/${parentId}/revisions${revisionId ? "/" + revisionId : ""}`,
        revisionKey: isTemplate && !window?.__experimentalTemplateActivate ? "wp_id" : DEFAULT_ENTITY_KEY
      };
      if (window.__experimentalEnableSync) {
        if (true) {
          entity2.syncConfig = {
            /**
             * Apply changes from the local editor to the local CRDT document so
             * that those changes can be synced to other peers (via the provider).
             *
             * @param {import('@wordpress/sync').CRDTDoc}               crdtDoc
             * @param {Partial< import('@wordpress/sync').ObjectData >} changes
             * @return {void}
             */
            applyChangesToCRDTDoc: (crdtDoc, changes) => applyPostChangesToCRDTDoc(crdtDoc, changes, postType),
            /**
             * Extract changes from a CRDT document that can be used to update the
             * local editor state.
             *
             * @param {import('@wordpress/sync').CRDTDoc}    crdtDoc
             * @param {import('@wordpress/sync').ObjectData} editedRecord
             * @return {Partial< import('@wordpress/sync').ObjectData >} Changes to record
             */
            getChangesFromCRDTDoc: (crdtDoc, editedRecord) => getPostChangesFromCRDTDoc(
              crdtDoc,
              editedRecord,
              postType
            ),
            /**
             * Sync features supported by the entity.
             *
             * @type {Record< string, boolean >}
             */
            supports: {
              crdtPersistence: true
            }
          };
        }
      }
      return entity2;
    });
  }
  async function loadTaxonomyEntities() {
    const taxonomies = await (0, import_api_fetch.default)({
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
    const entity2 = {
      label: (0, import_i18n.__)("Site"),
      name: "site",
      kind: "root",
      key: false,
      baseURL: "/wp/v2/settings",
      meta: {}
    };
    if (window.__experimentalEnableSync) {
      if (true) {
        entity2.syncConfig = {
          applyChangesToCRDTDoc: defaultApplyChangesToCRDTDoc,
          getChangesFromCRDTDoc: defaultGetChangesFromCRDTDoc
        };
      }
    }
    const site = await (0, import_api_fetch.default)({
      path: entity2.baseURL,
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
    return [{ ...entity2, meta: { labels } }];
  }
  var getMethodName = (kind, name, prefix = "get") => {
    const kindPrefix = kind === "root" ? "" : pascalCase(kind);
    const suffix = pascalCase(name);
    return `${prefix}${kindPrefix}${suffix}`;
  };

  // packages/core-data/build-module/queried-data/reducer.js
  function getContextFromAction(action) {
    const { query } = action;
    if (!query) {
      return "default";
    }
    const queryParts = get_query_parts_default(query);
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
  function removeEntitiesById(entities2, ids) {
    return Object.fromEntries(
      Object.entries(entities2).filter(
        ([id2]) => !ids.some((itemId) => {
          if (Number.isInteger(itemId)) {
            return itemId === +id2;
          }
          return itemId === id2;
        })
      )
    );
  }
  function items(state = {}, action) {
    switch (action.type) {
      case "RECEIVE_ITEMS": {
        const context = getContextFromAction(action);
        const key = action.key || DEFAULT_ENTITY_KEY;
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
        const { query, key = DEFAULT_ENTITY_KEY } = action;
        const queryParts = query ? get_query_parts_default(query) : {};
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
  var receiveQueries = (0, import_compose.compose)([
    // Limit to matching action type so we don't attempt to replace action on
    // an unhandled action.
    if_matching_action_default((action) => "query" in action),
    // Inject query parts into action for use both in `onSubKey` and reducer.
    replace_action_default((action) => {
      if (action.query) {
        return {
          ...action,
          ...get_query_parts_default(action.query)
        };
      }
      return action;
    }),
    on_sub_key_default("context"),
    // Queries shape is shared, but keyed by query `stableKey` part. Original
    // reducer tracks only a single query object.
    on_sub_key_default("stableKey")
  ])((state = {}, action) => {
    const { type, page, perPage, key = DEFAULT_ENTITY_KEY } = action;
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
  var queries = (state = {}, action) => {
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
  var reducer_default = (0, import_data2.combineReducers)({
    items,
    itemIsComplete,
    queries
  });

  // packages/core-data/build-module/reducer.js
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
  var withMultiEntityRecordEdits = (reducer) => (state, action) => {
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
    return (0, import_compose2.compose)([
      withMultiEntityRecordEdits,
      // Limit to matching action type so we don't attempt to replace action on
      // an unhandled action.
      if_matching_action_default(
        (action) => action.name && action.kind && action.name === entityConfig.name && action.kind === entityConfig.kind
      ),
      // Inject the entity config into the action.
      replace_action_default((action) => {
        return {
          key: entityConfig.key || DEFAULT_ENTITY_KEY,
          ...action
        };
      })
    ])(
      (0, import_data3.combineReducers)({
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
                      !(0, import_es64.default)(
                        edits[key],
                        record[key]?.raw ?? record[key]
                      ) && // Sometimes the server alters the sent value which means
                      // we need to also remove the edits before the api request.
                      (!action.persistedEdits || !(0, import_es64.default)(
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
                ([id2]) => !action.itemIds.some((itemId) => {
                  if (Number.isInteger(itemId)) {
                    return itemId === +id2;
                  }
                  return itemId === id2;
                })
              )
            );
          }
          return state;
        }
      })
    );
  }
  function entitiesConfig(state = rootEntitiesConfig, action) {
    switch (action.type) {
      case "ADD_ENTITIES":
        return [...state, ...action.entities];
    }
    return state;
  }
  var entities = (state = {}, action) => {
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
      entitiesDataReducer = (0, import_data3.combineReducers)(
        Object.fromEntries(
          Object.entries(entitiesByKind).map(
            ([kind, subEntities]) => {
              const kindReducer = (0, import_data3.combineReducers)(
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
  function undoManager(state = (0, import_undo_manager.createUndoManager)()) {
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
  function editorSettings(state = null, action) {
    switch (action.type) {
      case "RECEIVE_EDITOR_SETTINGS":
        return action.settings;
    }
    return state;
  }
  function editorAssets(state = null, action) {
    switch (action.type) {
      case "RECEIVE_EDITOR_ASSETS":
        return action.assets;
    }
    return state;
  }
  var reducer_default2 = (0, import_data3.combineReducers)({
    users,
    currentTheme,
    currentGlobalStylesId,
    currentUser,
    themeGlobalStyleVariations,
    themeBaseGlobalStyles,
    themeGlobalStyleRevisions,
    entities,
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
    registeredPostMeta,
    editorSettings,
    editorAssets
  });

  // packages/core-data/build-module/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    __experimentalGetCurrentGlobalStylesId: () => __experimentalGetCurrentGlobalStylesId,
    __experimentalGetCurrentThemeBaseGlobalStyles: () => __experimentalGetCurrentThemeBaseGlobalStyles,
    __experimentalGetCurrentThemeGlobalStylesVariations: () => __experimentalGetCurrentThemeGlobalStylesVariations,
    __experimentalGetDirtyEntityRecords: () => __experimentalGetDirtyEntityRecords,
    __experimentalGetEntitiesBeingSaved: () => __experimentalGetEntitiesBeingSaved,
    __experimentalGetEntityRecordNoResolver: () => __experimentalGetEntityRecordNoResolver,
    canUser: () => canUser,
    canUserEditEntityRecord: () => canUserEditEntityRecord,
    getAuthors: () => getAuthors,
    getAutosave: () => getAutosave,
    getAutosaves: () => getAutosaves,
    getBlockPatternCategories: () => getBlockPatternCategories,
    getBlockPatterns: () => getBlockPatterns,
    getCurrentTheme: () => getCurrentTheme,
    getCurrentThemeGlobalStylesRevisions: () => getCurrentThemeGlobalStylesRevisions,
    getCurrentUser: () => getCurrentUser,
    getDefaultTemplateId: () => getDefaultTemplateId,
    getEditedEntityRecord: () => getEditedEntityRecord,
    getEmbedPreview: () => getEmbedPreview,
    getEntitiesByKind: () => getEntitiesByKind,
    getEntitiesConfig: () => getEntitiesConfig,
    getEntity: () => getEntity,
    getEntityConfig: () => getEntityConfig,
    getEntityRecord: () => getEntityRecord,
    getEntityRecordEdits: () => getEntityRecordEdits,
    getEntityRecordNonTransientEdits: () => getEntityRecordNonTransientEdits,
    getEntityRecords: () => getEntityRecords,
    getEntityRecordsTotalItems: () => getEntityRecordsTotalItems,
    getEntityRecordsTotalPages: () => getEntityRecordsTotalPages,
    getLastEntityDeleteError: () => getLastEntityDeleteError,
    getLastEntitySaveError: () => getLastEntitySaveError,
    getRawEntityRecord: () => getRawEntityRecord,
    getRedoEdit: () => getRedoEdit,
    getReferenceByDistinctEdits: () => getReferenceByDistinctEdits,
    getRevision: () => getRevision,
    getRevisions: () => getRevisions,
    getThemeSupports: () => getThemeSupports,
    getUndoEdit: () => getUndoEdit,
    getUserPatternCategories: () => getUserPatternCategories,
    getUserQueryResults: () => getUserQueryResults,
    hasEditsForEntityRecord: () => hasEditsForEntityRecord,
    hasEntityRecord: () => hasEntityRecord,
    hasEntityRecords: () => hasEntityRecords,
    hasFetchedAutosaves: () => hasFetchedAutosaves,
    hasRedo: () => hasRedo,
    hasUndo: () => hasUndo,
    isAutosavingEntityRecord: () => isAutosavingEntityRecord,
    isDeletingEntityRecord: () => isDeletingEntityRecord,
    isPreviewEmbedFallback: () => isPreviewEmbedFallback,
    isRequestingEmbedPreview: () => isRequestingEmbedPreview,
    isSavingEntityRecord: () => isSavingEntityRecord
  });
  var import_data4 = __toESM(require_data());
  var import_url3 = __toESM(require_url());
  var import_deprecated2 = __toESM(require_deprecated());

  // packages/core-data/build-module/name.js
  var STORE_NAME = "core";

  // packages/core-data/build-module/utils/log-entity-deprecation.js
  var import_deprecated = __toESM(require_deprecated());
  var loggedAlready = false;
  function logEntityDeprecation(kind, name, functionName, {
    alternativeFunctionName,
    isShorthandSelector = false
  } = {}) {
    const deprecation = deprecatedEntities[kind]?.[name];
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
      (0, import_deprecated.default)(message, {
        ...deprecation,
        alternative: alternativeMessage
      });
    }
    loggedAlready = true;
    setTimeout(() => {
      loggedAlready = false;
    }, 0);
  }

  // packages/core-data/build-module/selectors.js
  var EMPTY_OBJECT = {};
  var isRequestingEmbedPreview = (0, import_data4.createRegistrySelector)(
    (select) => (state, url) => {
      return select(STORE_NAME).isResolving("getEmbedPreview", [
        url
      ]);
    }
  );
  function getAuthors(state, query) {
    (0, import_deprecated2.default)("select( 'core' ).getAuthors()", {
      since: "5.9",
      alternative: "select( 'core' ).getUsers({ who: 'authors' })"
    });
    const path = (0, import_url3.addQueryArgs)(
      "/wp/v2/users/?who=authors&per_page=100",
      query
    );
    return getUserQueryResults(state, path);
  }
  function getCurrentUser(state) {
    return state.currentUser;
  }
  var getUserQueryResults = (0, import_data4.createSelector)(
    (state, queryID) => {
      const queryResults = state.users.queries[queryID] ?? [];
      return queryResults.map((id2) => state.users.byId[id2]);
    },
    (state, queryID) => [
      state.users.queries[queryID],
      state.users.byId
    ]
  );
  function getEntitiesByKind(state, kind) {
    (0, import_deprecated2.default)("wp.data.select( 'core' ).getEntitiesByKind()", {
      since: "6.0",
      alternative: "wp.data.select( 'core' ).getEntitiesConfig()"
    });
    return getEntitiesConfig(state, kind);
  }
  var getEntitiesConfig = (0, import_data4.createSelector)(
    (state, kind) => state.entities.config.filter((entity2) => entity2.kind === kind),
    /* eslint-disable @typescript-eslint/no-unused-vars */
    (state, kind) => state.entities.config
    /* eslint-enable @typescript-eslint/no-unused-vars */
  );
  function getEntity(state, kind, name) {
    (0, import_deprecated2.default)("wp.data.select( 'core' ).getEntity()", {
      since: "6.0",
      alternative: "wp.data.select( 'core' ).getEntityConfig()"
    });
    return getEntityConfig(state, kind, name);
  }
  function getEntityConfig(state, kind, name) {
    logEntityDeprecation(kind, name, "getEntityConfig");
    return state.entities.config?.find(
      (config) => config.kind === kind && config.name === name
    );
  }
  var getEntityRecord = (0, import_data4.createSelector)(
    ((state, kind, name, key, query) => {
      logEntityDeprecation(kind, name, "getEntityRecord");
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
      const fields = get_normalized_comma_separable_default(query._fields) ?? [];
      for (let f = 0; f < fields.length; f++) {
        const field = fields[f].split(".");
        let value = item;
        field.forEach((fieldName) => {
          value = value?.[fieldName];
        });
        setNestedValue(filteredItem, field, value);
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
  getEntityRecord.__unstableNormalizeArgs = (args2) => {
    const newArgs = [...args2];
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
    const fields = get_normalized_comma_separable_default(query._fields) ?? [];
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
  var getRawEntityRecord = (0, import_data4.createSelector)(
    (state, kind, name, key) => {
      logEntityDeprecation(kind, name, "getRawEntityRecord");
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
    logEntityDeprecation(kind, name, "hasEntityRecords");
    return Array.isArray(getEntityRecords(state, kind, name, query));
  }
  var getEntityRecords = ((state, kind, name, query) => {
    logEntityDeprecation(kind, name, "getEntityRecords");
    const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
    if (!queriedState) {
      return null;
    }
    return getQueriedItems(queriedState, query);
  });
  var getEntityRecordsTotalItems = (state, kind, name, query) => {
    logEntityDeprecation(kind, name, "getEntityRecordsTotalItems");
    const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
    if (!queriedState) {
      return null;
    }
    return getQueriedTotalItems(queriedState, query);
  };
  var getEntityRecordsTotalPages = (state, kind, name, query) => {
    logEntityDeprecation(kind, name, "getEntityRecordsTotalPages");
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
  var __experimentalGetDirtyEntityRecords = (0, import_data4.createSelector)(
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
                key: entityRecord ? entityRecord[entityConfig.key || DEFAULT_ENTITY_KEY] : void 0,
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
  var __experimentalGetEntitiesBeingSaved = (0, import_data4.createSelector)(
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
                key: entityRecord ? entityRecord[entityConfig.key || DEFAULT_ENTITY_KEY] : void 0,
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
    logEntityDeprecation(kind, name, "getEntityRecordEdits");
    return state.entities.records?.[kind]?.[name]?.edits?.[recordId];
  }
  var getEntityRecordNonTransientEdits = (0, import_data4.createSelector)(
    (state, kind, name, recordId) => {
      logEntityDeprecation(kind, name, "getEntityRecordNonTransientEdits");
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
    logEntityDeprecation(kind, name, "hasEditsForEntityRecord");
    return isSavingEntityRecord(state, kind, name, recordId) || Object.keys(
      getEntityRecordNonTransientEdits(state, kind, name, recordId)
    ).length > 0;
  }
  var getEditedEntityRecord = (0, import_data4.createSelector)(
    (state, kind, name, recordId) => {
      logEntityDeprecation(kind, name, "getEditedEntityRecord");
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
    logEntityDeprecation(kind, name, "isAutosavingEntityRecord");
    const { pending, isAutosave } = state.entities.records?.[kind]?.[name]?.saving?.[recordId] ?? {};
    return Boolean(pending && isAutosave);
  }
  function isSavingEntityRecord(state, kind, name, recordId) {
    logEntityDeprecation(kind, name, "isSavingEntityRecord");
    return state.entities.records?.[kind]?.[name]?.saving?.[recordId]?.pending ?? false;
  }
  function isDeletingEntityRecord(state, kind, name, recordId) {
    logEntityDeprecation(kind, name, "isDeletingEntityRecord");
    return state.entities.records?.[kind]?.[name]?.deleting?.[recordId]?.pending ?? false;
  }
  function getLastEntitySaveError(state, kind, name, recordId) {
    logEntityDeprecation(kind, name, "getLastEntitySaveError");
    return state.entities.records?.[kind]?.[name]?.saving?.[recordId]?.error;
  }
  function getLastEntityDeleteError(state, kind, name, recordId) {
    logEntityDeprecation(kind, name, "getLastEntityDeleteError");
    return state.entities.records?.[kind]?.[name]?.deleting?.[recordId]?.error;
  }
  function getUndoEdit(state) {
    (0, import_deprecated2.default)("select( 'core' ).getUndoEdit()", {
      since: "6.3"
    });
    return void 0;
  }
  function getRedoEdit(state) {
    (0, import_deprecated2.default)("select( 'core' ).getRedoEdit()", {
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
  function canUser(state, action, resource, id2) {
    const isEntity = typeof resource === "object";
    if (isEntity && (!resource.kind || !resource.name)) {
      return false;
    }
    if (isEntity) {
      logEntityDeprecation(resource.kind, resource.name, "canUser");
    }
    const key = getUserPermissionCacheKey(action, resource, id2);
    return state.userPermissions[key];
  }
  function canUserEditEntityRecord(state, kind, name, recordId) {
    (0, import_deprecated2.default)(`wp.data.select( 'core' ).canUserEditEntityRecord()`, {
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
    const autosaves2 = state.autosaves[postId];
    return autosaves2?.find(
      (autosave) => autosave.author === authorId
    );
  }
  var hasFetchedAutosaves = (0, import_data4.createRegistrySelector)(
    (select) => (state, postType, postId) => {
      return select(STORE_NAME).hasFinishedResolution("getAutosaves", [
        postType,
        postId
      ]);
    }
  );
  function getReferenceByDistinctEdits(state) {
    return state.editsReference;
  }
  function __experimentalGetCurrentThemeBaseGlobalStyles(state) {
    const currentTheme2 = getCurrentTheme(state);
    if (!currentTheme2) {
      return null;
    }
    return state.themeBaseGlobalStyles[currentTheme2.stylesheet];
  }
  function __experimentalGetCurrentThemeGlobalStylesVariations(state) {
    const currentTheme2 = getCurrentTheme(state);
    if (!currentTheme2) {
      return null;
    }
    return state.themeGlobalStyleVariations[currentTheme2.stylesheet];
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
    (0, import_deprecated2.default)("select( 'core' ).getCurrentThemeGlobalStylesRevisions()", {
      since: "6.5.0",
      alternative: "select( 'core' ).getRevisions( 'root', 'globalStyles', ${ recordKey } )"
    });
    const currentGlobalStylesId2 = __experimentalGetCurrentGlobalStylesId(state);
    if (!currentGlobalStylesId2) {
      return null;
    }
    return state.themeGlobalStyleRevisions[currentGlobalStylesId2];
  }
  function getDefaultTemplateId(state, query) {
    return state.defaultTemplates[JSON.stringify(query)];
  }
  var getRevisions = (state, kind, name, recordKey, query) => {
    logEntityDeprecation(kind, name, "getRevisions");
    const queriedStateRevisions = state.entities.records?.[kind]?.[name]?.revisions?.[recordKey];
    if (!queriedStateRevisions) {
      return null;
    }
    return getQueriedItems(queriedStateRevisions, query);
  };
  var getRevision = (0, import_data4.createSelector)(
    (state, kind, name, recordKey, revisionKey, query) => {
      logEntityDeprecation(kind, name, "getRevision");
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
      const fields = get_normalized_comma_separable_default(query._fields) ?? [];
      for (let f = 0; f < fields.length; f++) {
        const field = fields[f].split(".");
        let value = item;
        field.forEach((fieldName) => {
          value = value?.[fieldName];
        });
        setNestedValue(filteredItem, field, value);
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

  // packages/core-data/build-module/private-selectors.js
  var private_selectors_exports = {};
  __export(private_selectors_exports, {
    getBlockPatternsForPostType: () => getBlockPatternsForPostType,
    getEditorAssets: () => getEditorAssets,
    getEditorSettings: () => getEditorSettings,
    getEntityRecordPermissions: () => getEntityRecordPermissions,
    getEntityRecordsPermissions: () => getEntityRecordsPermissions,
    getHomePage: () => getHomePage,
    getNavigationFallbackId: () => getNavigationFallbackId,
    getPostsPageId: () => getPostsPageId,
    getRegisteredPostMeta: () => getRegisteredPostMeta,
    getTemplateId: () => getTemplateId,
    getUndoManager: () => getUndoManager
  });
  var import_data5 = __toESM(require_data());

  // packages/core-data/build-module/lock-unlock.js
  var import_private_apis = __toESM(require_private_apis());
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/core-data"
  );

  // packages/core-data/build-module/private-selectors.js
  function getUndoManager(state) {
    return state.undoManager;
  }
  function getNavigationFallbackId(state) {
    return state.navigationFallbackId;
  }
  var getBlockPatternsForPostType = (0, import_data5.createRegistrySelector)(
    (select) => (0, import_data5.createSelector)(
      (state, postType) => select(STORE_NAME).getBlockPatterns().filter(
        ({ postTypes }) => !postTypes || Array.isArray(postTypes) && postTypes.includes(postType)
      ),
      () => [select(STORE_NAME).getBlockPatterns()]
    )
  );
  var getEntityRecordsPermissions = (0, import_data5.createRegistrySelector)(
    (select) => (0, import_data5.createSelector)(
      (state, kind, name, ids) => {
        const normalizedIds = Array.isArray(ids) ? ids : [ids];
        return normalizedIds.map((id2) => ({
          delete: select(STORE_NAME).canUser("delete", {
            kind,
            name,
            id: id2
          }),
          update: select(STORE_NAME).canUser("update", {
            kind,
            name,
            id: id2
          })
        }));
      },
      (state) => [state.userPermissions]
    )
  );
  function getEntityRecordPermissions(state, kind, name, id2) {
    logEntityDeprecation(kind, name, "getEntityRecordPermissions");
    return getEntityRecordsPermissions(state, kind, name, id2)[0];
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
  var getHomePage = (0, import_data5.createRegistrySelector)(
    (select) => (0, import_data5.createSelector)(
      () => {
        const siteData = select(STORE_NAME).getEntityRecord(
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
          STORE_NAME
        ).getDefaultTemplateId({
          slug: "front-page"
        });
        if (!frontPageTemplateId) {
          return null;
        }
        return { postType: "wp_template", postId: frontPageTemplateId };
      },
      (state) => [
        // Even though getDefaultTemplateId.shouldInvalidate returns true when root/site changes,
        // it doesn't seem to invalidate this cache, I'm not sure why.
        getEntityRecord(state, "root", "site"),
        getEntityRecord(state, "root", "__unstableBase"),
        getDefaultTemplateId(state, {
          slug: "front-page"
        })
      ]
    )
  );
  var getPostsPageId = (0, import_data5.createRegistrySelector)((select) => () => {
    const siteData = select(STORE_NAME).getEntityRecord(
      "root",
      "__unstableBase"
    );
    return siteData?.show_on_front === "page" ? normalizePageId(siteData.page_for_posts) : null;
  });
  var getTemplateId = (0, import_data5.createRegistrySelector)(
    (select) => (state, postType, postId) => {
      const homepage = unlock(select(STORE_NAME)).getHomePage();
      if (!homepage) {
        return;
      }
      if (postType === "page" && postType === homepage?.postType && postId.toString() === homepage?.postId) {
        const templates = select(STORE_NAME).getEntityRecords(
          "postType",
          "wp_template",
          {
            per_page: -1
          }
        );
        if (!templates) {
          return;
        }
        const id2 = templates.find(({ slug }) => slug === "front-page")?.id;
        if (id2) {
          return id2;
        }
      }
      const editedEntity = select(STORE_NAME).getEditedEntityRecord(
        "postType",
        postType,
        postId
      );
      if (!editedEntity) {
        return;
      }
      const postsPageId = unlock(select(STORE_NAME)).getPostsPageId();
      if (postType === "page" && postsPageId === postId.toString()) {
        return select(STORE_NAME).getDefaultTemplateId({
          slug: "home"
        });
      }
      const currentTemplateSlug = editedEntity.template;
      if (currentTemplateSlug) {
        const currentTemplate = select(STORE_NAME).getEntityRecords("postType", "wp_template", {
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
      return select(STORE_NAME).getDefaultTemplateId({
        slug: slugToCheck
      });
    }
  );
  function getEditorSettings(state) {
    return state.editorSettings;
  }
  function getEditorAssets(state) {
    return state.editorAssets;
  }

  // packages/core-data/build-module/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    __experimentalBatch: () => __experimentalBatch,
    __experimentalReceiveCurrentGlobalStylesId: () => __experimentalReceiveCurrentGlobalStylesId,
    __experimentalReceiveThemeBaseGlobalStyles: () => __experimentalReceiveThemeBaseGlobalStyles,
    __experimentalReceiveThemeGlobalStyleVariations: () => __experimentalReceiveThemeGlobalStyleVariations,
    __experimentalSaveSpecifiedEntityEdits: () => __experimentalSaveSpecifiedEntityEdits,
    __unstableCreateUndoLevel: () => __unstableCreateUndoLevel,
    addEntities: () => addEntities,
    deleteEntityRecord: () => deleteEntityRecord,
    editEntityRecord: () => editEntityRecord,
    receiveAutosaves: () => receiveAutosaves,
    receiveCurrentTheme: () => receiveCurrentTheme,
    receiveCurrentUser: () => receiveCurrentUser,
    receiveDefaultTemplateId: () => receiveDefaultTemplateId,
    receiveEmbedPreview: () => receiveEmbedPreview,
    receiveEntityRecords: () => receiveEntityRecords,
    receiveNavigationFallbackId: () => receiveNavigationFallbackId,
    receiveRevisions: () => receiveRevisions,
    receiveThemeGlobalStyleRevisions: () => receiveThemeGlobalStyleRevisions,
    receiveThemeSupports: () => receiveThemeSupports,
    receiveUploadPermissions: () => receiveUploadPermissions,
    receiveUserPermission: () => receiveUserPermission,
    receiveUserPermissions: () => receiveUserPermissions,
    receiveUserQuery: () => receiveUserQuery,
    redo: () => redo,
    saveEditedEntityRecord: () => saveEditedEntityRecord,
    saveEntityRecord: () => saveEntityRecord,
    undo: () => undo
  });
  var import_es65 = __toESM(require_es6());
  var import_api_fetch3 = __toESM(require_api_fetch());
  var import_url4 = __toESM(require_url());
  var import_deprecated3 = __toESM(require_deprecated());

  // packages/core-data/build-module/batch/default-processor.js
  var import_api_fetch2 = __toESM(require_api_fetch());
  var maxItems = null;
  function chunk(arr, chunkSize) {
    const tmp = [...arr];
    const cache3 = [];
    while (tmp.length) {
      cache3.push(tmp.splice(0, chunkSize));
    }
    return cache3;
  }
  async function defaultProcessor(requests) {
    if (maxItems === null) {
      const preflightResponse = await (0, import_api_fetch2.default)({
        path: "/batch/v1",
        method: "OPTIONS"
      });
      maxItems = preflightResponse.endpoints[0].args.requests.maxItems;
    }
    const results = [];
    for (const batchRequests of chunk(requests, maxItems)) {
      const batchResponse = await (0, import_api_fetch2.default)({
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

  // packages/core-data/build-module/batch/create-batch.js
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
        const id2 = ++lastId;
        pending.add(id2);
        const add = (input) => new Promise((resolve2, reject2) => {
          queue.push({
            input,
            resolve: resolve2,
            reject: reject2
          });
          pending.delete(id2);
        });
        if (typeof inputOrThunk === "function") {
          return Promise.resolve(inputOrThunk(add)).finally(() => {
            pending.delete(id2);
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
          await new Promise((resolve2) => {
            const unsubscribe2 = pending.subscribe(() => {
              if (!pending.size) {
                unsubscribe2();
                resolve2(void 0);
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
          for (const { reject: reject2 } of queue) {
            reject2(error);
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
  var ObservableSet = class {
    constructor(...args2) {
      this.set = new Set(...args2);
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
  };

  // packages/core-data/build-module/actions.js
  function receiveUserQuery(queryID, users2) {
    return {
      type: "RECEIVE_USER_QUERY",
      users: Array.isArray(users2) ? users2 : [users2],
      queryID
    };
  }
  function receiveCurrentUser(currentUser2) {
    return {
      type: "RECEIVE_CURRENT_USER",
      currentUser: currentUser2
    };
  }
  function addEntities(entities2) {
    return {
      type: "ADD_ENTITIES",
      entities: entities2
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
  function receiveCurrentTheme(currentTheme2) {
    return {
      type: "RECEIVE_CURRENT_THEME",
      currentTheme: currentTheme2
    };
  }
  function __experimentalReceiveCurrentGlobalStylesId(currentGlobalStylesId2) {
    return {
      type: "RECEIVE_CURRENT_GLOBAL_STYLES_ID",
      id: currentGlobalStylesId2
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
    (0, import_deprecated3.default)("wp.data.dispatch( 'core' ).receiveThemeSupports", {
      since: "5.9"
    });
    return {
      type: "DO_NOTHING"
    };
  }
  function receiveThemeGlobalStyleRevisions(currentId, revisions) {
    (0, import_deprecated3.default)(
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
  var deleteEntityRecord = (kind, name, recordId, query, { __unstableFetch = import_api_fetch3.default, throwOnError = false } = {}) => async ({ dispatch, resolveSelect }) => {
    logEntityDeprecation(kind, name, "deleteEntityRecord");
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    let error;
    let deletedRecord = false;
    if (!entityConfig) {
      return;
    }
    const lock2 = await dispatch.__unstableAcquireStoreLock(
      STORE_NAME,
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
      let { baseURL } = entityConfig;
      if (kind === "postType" && name === "wp_template" && (recordId && typeof recordId === "string" && !/^\d+$/.test(recordId) || !window?.__experimentalTemplateActivate)) {
        baseURL = baseURL.slice(0, baseURL.lastIndexOf("/")) + "/templates";
      }
      try {
        let path = `${baseURL}/${recordId}`;
        if (query) {
          path = (0, import_url4.addQueryArgs)(path, query);
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
      dispatch.__unstableReleaseStoreLock(lock2);
    }
  };
  var editEntityRecord = (kind, name, recordId, edits, options = {}) => ({ select, dispatch }) => {
    logEntityDeprecation(kind, name, "editEntityRecord");
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
        acc[key] = (0, import_es65.default)(recordValue, value) ? void 0 : value;
        return acc;
      }, {})
    };
    if (window.__experimentalEnableSync && entityConfig.syncConfig) {
      if (true) {
        const objectType = `${kind}/${name}`;
        const objectId = recordId;
        getSyncManager()?.update(
          objectType,
          objectId,
          edit.edits,
          LOCAL_EDITOR_ORIGIN
        );
      }
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
  var undo = () => ({ select, dispatch }) => {
    const undoRecord = select.getUndoManager().undo();
    if (!undoRecord) {
      return;
    }
    dispatch({
      type: "UNDO",
      record: undoRecord
    });
  };
  var redo = () => ({ select, dispatch }) => {
    const redoRecord = select.getUndoManager().redo();
    if (!redoRecord) {
      return;
    }
    dispatch({
      type: "REDO",
      record: redoRecord
    });
  };
  var __unstableCreateUndoLevel = () => ({ select }) => {
    select.getUndoManager().addRecord();
  };
  var saveEntityRecord = (kind, name, record, {
    isAutosave = false,
    __unstableFetch = import_api_fetch3.default,
    throwOnError = false
  } = {}) => async ({ select, resolveSelect, dispatch }) => {
    logEntityDeprecation(kind, name, "saveEntityRecord");
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    if (!entityConfig) {
      return;
    }
    const entityIdKey = entityConfig.key ?? DEFAULT_ENTITY_KEY;
    const recordId = record[entityIdKey];
    const isNewRecord = !!entityIdKey && !recordId;
    const lock2 = await dispatch.__unstableAcquireStoreLock(
      STORE_NAME,
      ["entities", "records", kind, name, recordId || v4_default()],
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
      let { baseURL } = entityConfig;
      if (kind === "postType" && name === "wp_template" && (recordId && typeof recordId === "string" && !/^\d+$/.test(recordId) || !window?.__experimentalTemplateActivate)) {
        baseURL = baseURL.slice(0, baseURL.lastIndexOf("/")) + "/templates";
      }
      try {
        const path = `${baseURL}${recordId ? "/" + recordId : ""}`;
        const persistedRecord = !isNewRecord ? select.getRawEntityRecord(kind, name, recordId) : {};
        if (isAutosave) {
          const currentUser2 = select.getCurrentUser();
          const currentUserId = currentUser2 ? currentUser2.id : void 0;
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
      dispatch.__unstableReleaseStoreLock(lock2);
    }
  };
  var __experimentalBatch = (requests) => async ({ dispatch }) => {
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
  var saveEditedEntityRecord = (kind, name, recordId, options) => async ({ select, dispatch, resolveSelect }) => {
    logEntityDeprecation(kind, name, "saveEditedEntityRecord");
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
    const entityIdKey = entityConfig.key || DEFAULT_ENTITY_KEY;
    const edits = select.getEntityRecordNonTransientEdits(
      kind,
      name,
      recordId
    );
    const record = { [entityIdKey]: recordId, ...edits };
    return await dispatch.saveEntityRecord(kind, name, record, options);
  };
  var __experimentalSaveSpecifiedEntityEdits = (kind, name, recordId, itemsToSave, options) => async ({ select, dispatch, resolveSelect }) => {
    logEntityDeprecation(
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
      setNestedValue(editsToSave, item, getNestedValue(edits, item));
    }
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    const entityIdKey = entityConfig?.key || DEFAULT_ENTITY_KEY;
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
    (0, import_deprecated3.default)("wp.data.dispatch( 'core' ).receiveUploadPermissions", {
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
  function receiveAutosaves(postId, autosaves2) {
    return {
      type: "RECEIVE_AUTOSAVES",
      postId,
      autosaves: Array.isArray(autosaves2) ? autosaves2 : [autosaves2]
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
  var receiveRevisions = (kind, name, recordKey, records, query, invalidateCache = false, meta) => async ({ dispatch, resolveSelect }) => {
    logEntityDeprecation(kind, name, "receiveRevisions");
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    const key = entityConfig && entityConfig?.revisionKey ? entityConfig.revisionKey : DEFAULT_ENTITY_KEY;
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

  // packages/core-data/build-module/private-actions.js
  var private_actions_exports = {};
  __export(private_actions_exports, {
    editMediaEntity: () => editMediaEntity,
    receiveEditorAssets: () => receiveEditorAssets,
    receiveEditorSettings: () => receiveEditorSettings,
    receiveRegisteredPostMeta: () => receiveRegisteredPostMeta
  });
  var import_api_fetch4 = __toESM(require_api_fetch());
  function receiveRegisteredPostMeta(postType, registeredPostMeta2) {
    return {
      type: "RECEIVE_REGISTERED_POST_META",
      postType,
      registeredPostMeta: registeredPostMeta2
    };
  }
  var editMediaEntity = (recordId, edits = {}, { __unstableFetch = import_api_fetch4.default, throwOnError = false } = {}) => async ({ dispatch, resolveSelect }) => {
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
    const lock2 = await dispatch.__unstableAcquireStoreLock(
      STORE_NAME,
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
      dispatch.__unstableReleaseStoreLock(lock2);
    }
  };
  function receiveEditorSettings(settings) {
    return {
      type: "RECEIVE_EDITOR_SETTINGS",
      settings
    };
  }
  function receiveEditorAssets(assets) {
    return {
      type: "RECEIVE_EDITOR_ASSETS",
      assets
    };
  }

  // packages/core-data/build-module/resolvers.js
  var resolvers_exports = {};
  __export(resolvers_exports, {
    __experimentalGetCurrentGlobalStylesId: () => __experimentalGetCurrentGlobalStylesId2,
    __experimentalGetCurrentThemeBaseGlobalStyles: () => __experimentalGetCurrentThemeBaseGlobalStyles2,
    __experimentalGetCurrentThemeGlobalStylesVariations: () => __experimentalGetCurrentThemeGlobalStylesVariations2,
    canUser: () => canUser2,
    canUserEditEntityRecord: () => canUserEditEntityRecord2,
    getAuthors: () => getAuthors2,
    getAutosave: () => getAutosave2,
    getAutosaves: () => getAutosaves2,
    getBlockPatternCategories: () => getBlockPatternCategories2,
    getBlockPatterns: () => getBlockPatterns2,
    getCurrentTheme: () => getCurrentTheme2,
    getCurrentThemeGlobalStylesRevisions: () => getCurrentThemeGlobalStylesRevisions2,
    getCurrentUser: () => getCurrentUser2,
    getDefaultTemplateId: () => getDefaultTemplateId2,
    getEditedEntityRecord: () => getEditedEntityRecord2,
    getEditorAssets: () => getEditorAssets2,
    getEditorSettings: () => getEditorSettings2,
    getEmbedPreview: () => getEmbedPreview2,
    getEntitiesConfig: () => getEntitiesConfig2,
    getEntityRecord: () => getEntityRecord2,
    getEntityRecords: () => getEntityRecords2,
    getEntityRecordsTotalItems: () => getEntityRecordsTotalItems2,
    getEntityRecordsTotalPages: () => getEntityRecordsTotalPages2,
    getNavigationFallbackId: () => getNavigationFallbackId2,
    getRawEntityRecord: () => getRawEntityRecord2,
    getRegisteredPostMeta: () => getRegisteredPostMeta2,
    getRevision: () => getRevision2,
    getRevisions: () => getRevisions2,
    getThemeSupports: () => getThemeSupports2,
    getUserPatternCategories: () => getUserPatternCategories2
  });
  var import_url7 = __toESM(require_url());
  var import_html_entities2 = __toESM(require_html_entities());
  var import_api_fetch8 = __toESM(require_api_fetch());

  // packages/core-data/build-module/fetch/index.js
  var import_api_fetch7 = __toESM(require_api_fetch());

  // packages/core-data/build-module/fetch/__experimental-fetch-link-suggestions.js
  var import_api_fetch5 = __toESM(require_api_fetch());
  var import_url5 = __toESM(require_url());
  var import_html_entities = __toESM(require_html_entities());
  var import_i18n2 = __toESM(require_i18n());
  async function fetchLinkSuggestions(search, searchOptions = {}, editorSettings2 = {}) {
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
    const { disablePostFormats = false } = editorSettings2;
    const queries2 = [];
    if (!type || type === "post") {
      queries2.push(
        (0, import_api_fetch5.default)({
          path: (0, import_url5.addQueryArgs)("/wp/v2/search", {
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
              title: (0, import_html_entities.decodeEntities)(result.title || "") || (0, import_i18n2.__)("(no title)"),
              type: result.subtype || result.type,
              kind: "post-type"
            };
          });
        }).catch(() => [])
        // Fail by returning no results.
      );
    }
    if (!type || type === "term") {
      queries2.push(
        (0, import_api_fetch5.default)({
          path: (0, import_url5.addQueryArgs)("/wp/v2/search", {
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
              title: (0, import_html_entities.decodeEntities)(result.title || "") || (0, import_i18n2.__)("(no title)"),
              type: result.subtype || result.type,
              kind: "taxonomy"
            };
          });
        }).catch(() => [])
        // Fail by returning no results.
      );
    }
    if (!disablePostFormats && (!type || type === "post-format")) {
      queries2.push(
        (0, import_api_fetch5.default)({
          path: (0, import_url5.addQueryArgs)("/wp/v2/search", {
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
              title: (0, import_html_entities.decodeEntities)(result.title || "") || (0, import_i18n2.__)("(no title)"),
              type: result.subtype || result.type,
              kind: "taxonomy"
            };
          });
        }).catch(() => [])
        // Fail by returning no results.
      );
    }
    if (!type || type === "attachment") {
      queries2.push(
        (0, import_api_fetch5.default)({
          path: (0, import_url5.addQueryArgs)("/wp/v2/media", {
            search,
            page,
            per_page: perPage
          })
        }).then((results2) => {
          return results2.map((result) => {
            return {
              id: result.id,
              url: result.source_url,
              title: (0, import_html_entities.decodeEntities)(result.title.rendered || "") || (0, import_i18n2.__)("(no title)"),
              type: result.type,
              kind: "media"
            };
          });
        }).catch(() => [])
        // Fail by returning no results.
      );
    }
    const responses = await Promise.all(queries2);
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
  function tokenize(text2) {
    return text2.toLowerCase().match(/[\p{L}\p{N}]+/gu) || [];
  }

  // packages/core-data/build-module/fetch/__experimental-fetch-url-data.js
  var import_api_fetch6 = __toESM(require_api_fetch());
  var import_url6 = __toESM(require_url());
  var CACHE = /* @__PURE__ */ new Map();
  var fetchUrlData = async (url, options = {}) => {
    const endpoint = "/wp-block-editor/v1/url-details";
    const args2 = {
      url: (0, import_url6.prependHTTP)(url)
    };
    if (!(0, import_url6.isURL)(url)) {
      return Promise.reject(`${url} is not a valid URL.`);
    }
    const protocol = (0, import_url6.getProtocol)(url);
    if (!protocol || !(0, import_url6.isValidProtocol)(protocol) || !protocol.startsWith("http") || !/^https?:\/\/[^\/\s]/i.test(url)) {
      return Promise.reject(
        `${url} does not have a valid protocol. URLs must be "http" based`
      );
    }
    if (CACHE.has(url)) {
      return CACHE.get(url);
    }
    return (0, import_api_fetch6.default)({
      path: (0, import_url6.addQueryArgs)(endpoint, args2),
      ...options
    }).then((res) => {
      CACHE.set(url, res);
      return res;
    });
  };
  var experimental_fetch_url_data_default = fetchUrlData;

  // packages/core-data/build-module/fetch/index.js
  async function fetchBlockPatterns() {
    const restPatterns = await (0, import_api_fetch7.default)({
      path: "/wp/v2/block-patterns/patterns"
    });
    if (!restPatterns) {
      return [];
    }
    return restPatterns.map(
      (pattern) => Object.fromEntries(
        Object.entries(pattern).map(([key, value]) => [
          camelCase(key),
          value
        ])
      )
    );
  }

  // packages/core-data/build-module/resolvers.js
  var getAuthors2 = (query) => async ({ dispatch }) => {
    const path = (0, import_url7.addQueryArgs)(
      "/wp/v2/users/?who=authors&per_page=100",
      query
    );
    const users2 = await (0, import_api_fetch8.default)({ path });
    dispatch.receiveUserQuery(path, users2);
  };
  var getCurrentUser2 = () => async ({ dispatch }) => {
    const currentUser2 = await (0, import_api_fetch8.default)({ path: "/wp/v2/users/me" });
    dispatch.receiveCurrentUser(currentUser2);
  };
  var getEntityRecord2 = (kind, name, key = "", query) => async ({ select, dispatch, registry, resolveSelect }) => {
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.name === name && config.kind === kind
    );
    if (!entityConfig) {
      return;
    }
    const lock2 = await dispatch.__unstableAcquireStoreLock(
      STORE_NAME,
      ["entities", "records", kind, name, key],
      { exclusive: false }
    );
    try {
      if (query !== void 0 && query._fields) {
        query = {
          ...query,
          _fields: [
            .../* @__PURE__ */ new Set([
              ...get_normalized_comma_separable_default(query._fields) || [],
              entityConfig.key || DEFAULT_ENTITY_KEY
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
      let { baseURL } = entityConfig;
      if (kind === "postType" && name === "wp_template" && (key && typeof key === "string" && !/^\d+$/.test(key) || !window?.__experimentalTemplateActivate)) {
        baseURL = baseURL.slice(0, baseURL.lastIndexOf("/")) + "/templates";
      }
      const path = (0, import_url7.addQueryArgs)(baseURL + (key ? "/" + key : ""), {
        ...entityConfig.baseURLParams,
        ...query
      });
      const response = await (0, import_api_fetch8.default)({ path, parse: false });
      const record = await response.json();
      const permissions = getUserPermissionsFromAllowHeader(
        response.headers?.get("allow")
      );
      const canUserResolutionsArgs = [];
      const receiveUserPermissionArgs = {};
      for (const action of ALLOWED_RESOURCE_ACTIONS) {
        receiveUserPermissionArgs[getUserPermissionCacheKey(action, {
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
        if (true) {
          const objectType = `${kind}/${name}`;
          const objectId = key;
          const recordWithTransients = { ...record };
          Object.entries(entityConfig.transientEdits ?? {}).filter(
            ([propName, transientConfig]) => void 0 === recordWithTransients[propName] && transientConfig && "object" === typeof transientConfig && "read" in transientConfig && "function" === typeof transientConfig.read
          ).forEach(([propName, transientConfig]) => {
            recordWithTransients[propName] = transientConfig.read(recordWithTransients);
          });
          await getSyncManager()?.load(
            entityConfig.syncConfig,
            objectType,
            objectId,
            recordWithTransients,
            {
              // Handle edits sourced from the sync manager.
              editRecord: (edits) => {
                if (!Object.keys(edits).length) {
                  return;
                }
                dispatch({
                  type: "EDIT_ENTITY_RECORD",
                  kind,
                  name,
                  recordId: key,
                  edits,
                  meta: {
                    undo: void 0
                  }
                });
              },
              // Get the current entity record (with edits)
              getEditedRecord: async () => await resolveSelect.getEditedEntityRecord(
                kind,
                name,
                key
              ),
              // Save the current entity record's unsaved edits.
              saveRecord: () => {
                dispatch.saveEditedEntityRecord(
                  kind,
                  name,
                  key
                );
              }
            }
          );
        }
      }
      registry.batch(() => {
        dispatch.receiveEntityRecords(kind, name, record, query);
        dispatch.receiveUserPermissions(receiveUserPermissionArgs);
        dispatch.finishResolutions("canUser", canUserResolutionsArgs);
      });
    } finally {
      dispatch.__unstableReleaseStoreLock(lock2);
    }
  };
  getEntityRecord2.shouldInvalidate = (action, kind, name) => {
    return kind === "root" && name === "site" && (action.type === "RECEIVE_ITEMS" && // Making sure persistedEdits is set seems to be the only way of
    // knowing whether it's an update or fetch. Only an update would
    // have persistedEdits.
    action.persistedEdits && action.persistedEdits.status !== "auto-draft" || action.type === "REMOVE_ITEMS") && action.kind === "postType" && action.name === "wp_template";
  };
  var getRawEntityRecord2 = forward_resolver_default("getEntityRecord");
  var getEditedEntityRecord2 = forward_resolver_default("getEntityRecord");
  var getEntityRecords2 = (kind, name, query = {}) => async ({ dispatch, registry, resolveSelect }) => {
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.name === name && config.kind === kind
    );
    if (!entityConfig) {
      return;
    }
    const lock2 = await dispatch.__unstableAcquireStoreLock(
      STORE_NAME,
      ["entities", "records", kind, name],
      { exclusive: false }
    );
    const rawQuery = { ...query };
    const key = entityConfig.key || DEFAULT_ENTITY_KEY;
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
              ...get_normalized_comma_separable_default(query._fields) || [],
              key
            ])
          ].join()
        };
      }
      let { baseURL } = entityConfig;
      const { combinedTemplates = true } = query;
      if (kind === "postType" && name === "wp_template" && combinedTemplates) {
        baseURL = baseURL.slice(0, baseURL.lastIndexOf("/")) + "/templates";
      }
      const path = (0, import_url7.addQueryArgs)(baseURL, {
        ...entityConfig.baseURLParams,
        ...query
      });
      let records = [], meta;
      if (entityConfig.supportsPagination && query.per_page !== -1) {
        const response = await (0, import_api_fetch8.default)({ path, parse: false });
        records = Object.values(await response.json());
        meta = {
          totalItems: parseInt(
            response.headers.get("X-WP-Total")
          ),
          totalPages: parseInt(
            response.headers.get("X-WP-TotalPages")
          )
        };
      } else if (query.per_page === -1 && query[RECEIVE_INTERMEDIATE_RESULTS] === true) {
        let page = 1;
        let totalPages;
        do {
          const response = await (0, import_api_fetch8.default)({
            path: (0, import_url7.addQueryArgs)(path, { page, per_page: 100 }),
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
        records = Object.values(await (0, import_api_fetch8.default)({ path }));
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
          permissions: getUserPermissionsFromAllowHeader(
            record._links.self[0].targetHints.allow
          )
        }));
        const canUserResolutionsArgs = [];
        const receiveUserPermissionArgs = {};
        for (const targetHint of targetHints) {
          for (const action of ALLOWED_RESOURCE_ACTIONS) {
            canUserResolutionsArgs.push([
              action,
              { kind, name, id: targetHint.id }
            ]);
            receiveUserPermissionArgs[getUserPermissionCacheKey(action, {
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
        dispatch.__unstableReleaseStoreLock(lock2);
      });
    } catch (e) {
      dispatch.__unstableReleaseStoreLock(lock2);
    }
  };
  getEntityRecords2.shouldInvalidate = (action, kind, name) => {
    return (action.type === "RECEIVE_ITEMS" || action.type === "REMOVE_ITEMS") && action.invalidateCache && kind === action.kind && name === action.name;
  };
  var getEntityRecordsTotalItems2 = forward_resolver_default("getEntityRecords");
  var getEntityRecordsTotalPages2 = forward_resolver_default("getEntityRecords");
  var getCurrentTheme2 = () => async ({ dispatch, resolveSelect }) => {
    const activeThemes = await resolveSelect.getEntityRecords(
      "root",
      "theme",
      { status: "active" }
    );
    dispatch.receiveCurrentTheme(activeThemes[0]);
  };
  var getThemeSupports2 = forward_resolver_default("getCurrentTheme");
  var getEmbedPreview2 = (url) => async ({ dispatch }) => {
    try {
      const embedProxyResponse = await (0, import_api_fetch8.default)({
        path: (0, import_url7.addQueryArgs)("/oembed/1.0/proxy", { url })
      });
      dispatch.receiveEmbedPreview(url, embedProxyResponse);
    } catch (error) {
      dispatch.receiveEmbedPreview(url, false);
    }
  };
  var canUser2 = (requestedAction, resource, id2) => async ({ dispatch, registry, resolveSelect }) => {
    if (!ALLOWED_RESOURCE_ACTIONS.includes(requestedAction)) {
      throw new Error(`'${requestedAction}' is not a valid action.`);
    }
    const { hasStartedResolution } = registry.select(STORE_NAME);
    for (const relatedAction of ALLOWED_RESOURCE_ACTIONS) {
      if (relatedAction === requestedAction) {
        continue;
      }
      const isAlreadyResolving = hasStartedResolution("canUser", [
        relatedAction,
        resource,
        id2
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
      resourcePath = `/wp/v2/${resource}` + (id2 ? "/" + id2 : "");
    }
    let response;
    try {
      response = await (0, import_api_fetch8.default)({
        path: resourcePath,
        method: "OPTIONS",
        parse: false
      });
    } catch (error) {
      return;
    }
    const permissions = getUserPermissionsFromAllowHeader(
      response.headers?.get("allow")
    );
    registry.batch(() => {
      for (const action of ALLOWED_RESOURCE_ACTIONS) {
        const key = getUserPermissionCacheKey(action, resource, id2);
        dispatch.receiveUserPermission(key, permissions[action]);
        if (action !== requestedAction) {
          dispatch.finishResolution("canUser", [
            action,
            resource,
            id2
          ]);
        }
      }
    });
  };
  var canUserEditEntityRecord2 = (kind, name, recordId) => async ({ dispatch }) => {
    await dispatch(canUser2("update", { kind, name, id: recordId }));
  };
  var getAutosaves2 = (postType, postId) => async ({ dispatch, resolveSelect }) => {
    const {
      rest_base: restBase,
      rest_namespace: restNamespace = "wp/v2",
      supports
    } = await resolveSelect.getPostType(postType);
    if (!supports?.autosave) {
      return;
    }
    const autosaves2 = await (0, import_api_fetch8.default)({
      path: `/${restNamespace}/${restBase}/${postId}/autosaves?context=edit`
    });
    if (autosaves2 && autosaves2.length) {
      dispatch.receiveAutosaves(postId, autosaves2);
    }
  };
  var getAutosave2 = (postType, postId) => async ({ resolveSelect }) => {
    await resolveSelect.getAutosaves(postType, postId);
  };
  var __experimentalGetCurrentGlobalStylesId2 = () => async ({ dispatch, resolveSelect }) => {
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
    const id2 = matches ? Number(matches[1]) : null;
    if (id2) {
      dispatch.__experimentalReceiveCurrentGlobalStylesId(id2);
    }
  };
  var __experimentalGetCurrentThemeBaseGlobalStyles2 = () => async ({ resolveSelect, dispatch }) => {
    const currentTheme2 = await resolveSelect.getCurrentTheme();
    const themeGlobalStyles = await (0, import_api_fetch8.default)({
      path: `/wp/v2/global-styles/themes/${currentTheme2.stylesheet}?context=view`
    });
    dispatch.__experimentalReceiveThemeBaseGlobalStyles(
      currentTheme2.stylesheet,
      themeGlobalStyles
    );
  };
  var __experimentalGetCurrentThemeGlobalStylesVariations2 = () => async ({ resolveSelect, dispatch }) => {
    const currentTheme2 = await resolveSelect.getCurrentTheme();
    const variations = await (0, import_api_fetch8.default)({
      path: `/wp/v2/global-styles/themes/${currentTheme2.stylesheet}/variations?context=view`
    });
    dispatch.__experimentalReceiveThemeGlobalStyleVariations(
      currentTheme2.stylesheet,
      variations
    );
  };
  var getCurrentThemeGlobalStylesRevisions2 = () => async ({ resolveSelect, dispatch }) => {
    const globalStylesId = await resolveSelect.__experimentalGetCurrentGlobalStylesId();
    const record = globalStylesId ? await resolveSelect.getEntityRecord(
      "root",
      "globalStyles",
      globalStylesId
    ) : void 0;
    const revisionsURL = record?._links?.["version-history"]?.[0]?.href;
    if (revisionsURL) {
      const resetRevisions = await (0, import_api_fetch8.default)({
        url: revisionsURL
      });
      const revisions = resetRevisions?.map(
        (revision) => Object.fromEntries(
          Object.entries(revision).map(([key, value]) => [
            camelCase(key),
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
  getCurrentThemeGlobalStylesRevisions2.shouldInvalidate = (action) => {
    return action.type === "SAVE_ENTITY_RECORD_FINISH" && action.kind === "root" && !action.error && action.name === "globalStyles";
  };
  var getBlockPatterns2 = () => async ({ dispatch }) => {
    const patterns = await fetchBlockPatterns();
    dispatch({ type: "RECEIVE_BLOCK_PATTERNS", patterns });
  };
  var getBlockPatternCategories2 = () => async ({ dispatch }) => {
    const categories = await (0, import_api_fetch8.default)({
      path: "/wp/v2/block-patterns/categories"
    });
    dispatch({ type: "RECEIVE_BLOCK_PATTERN_CATEGORIES", categories });
  };
  var getUserPatternCategories2 = () => async ({ dispatch, resolveSelect }) => {
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
      label: (0, import_html_entities2.decodeEntities)(userCategory.name),
      name: userCategory.slug
    })) || [];
    dispatch({
      type: "RECEIVE_USER_PATTERN_CATEGORIES",
      patternCategories: mappedPatternCategories
    });
  };
  var getNavigationFallbackId2 = () => async ({ dispatch, select, registry }) => {
    const fallback = await (0, import_api_fetch8.default)({
      path: (0, import_url7.addQueryArgs)("/wp-block-editor/v1/navigation-fallback", {
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
  var getDefaultTemplateId2 = (query) => async ({ dispatch, registry, resolveSelect }) => {
    const template = await (0, import_api_fetch8.default)({
      path: (0, import_url7.addQueryArgs)("/wp/v2/templates/lookup", query)
    });
    await resolveSelect.getEntitiesConfig("postType");
    const id2 = template?.wp_id || template?.id;
    if (id2) {
      template.id = id2;
      registry.batch(() => {
        dispatch.receiveDefaultTemplateId(query, id2);
        dispatch.receiveEntityRecords("postType", template.type, [
          template
        ]);
        dispatch.finishResolution("getEntityRecord", [
          "postType",
          template.type,
          id2
        ]);
      });
    }
  };
  getDefaultTemplateId2.shouldInvalidate = (action) => {
    return action.type === "RECEIVE_ITEMS" && action.kind === "root" && action.name === "site";
  };
  var getRevisions2 = (kind, name, recordKey, query = {}) => async ({ dispatch, registry, resolveSelect }) => {
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
            ...get_normalized_comma_separable_default(query._fields) || [],
            entityConfig.revisionKey || DEFAULT_ENTITY_KEY
          ])
        ].join()
      };
    }
    const path = (0, import_url7.addQueryArgs)(
      entityConfig.getRevisionsUrl(recordKey),
      query
    );
    let records, response;
    const meta = {};
    const isPaginated = entityConfig.supportsPagination && query.per_page !== -1;
    try {
      response = await (0, import_api_fetch8.default)({ path, parse: !isPaginated });
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
          const key = entityConfig.key || DEFAULT_ENTITY_KEY;
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
  getRevisions2.shouldInvalidate = (action, kind, name, recordKey) => action.type === "SAVE_ENTITY_RECORD_FINISH" && name === action.name && kind === action.kind && !action.error && recordKey === action.recordId;
  var getRevision2 = (kind, name, recordKey, revisionKey, query) => async ({ dispatch, resolveSelect }) => {
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
            ...get_normalized_comma_separable_default(query._fields) || [],
            entityConfig.revisionKey || DEFAULT_ENTITY_KEY
          ])
        ].join()
      };
    }
    const path = (0, import_url7.addQueryArgs)(
      entityConfig.getRevisionsUrl(recordKey, revisionKey),
      query
    );
    let record;
    try {
      record = await (0, import_api_fetch8.default)({ path });
    } catch (error) {
      return;
    }
    if (record) {
      dispatch.receiveRevisions(kind, name, recordKey, record, query);
    }
  };
  var getRegisteredPostMeta2 = (postType) => async ({ dispatch, resolveSelect }) => {
    let options;
    try {
      const {
        rest_namespace: restNamespace = "wp/v2",
        rest_base: restBase
      } = await resolveSelect.getPostType(postType) || {};
      options = await (0, import_api_fetch8.default)({
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
  var getEntitiesConfig2 = (kind) => async ({ dispatch }) => {
    const loader = additionalEntityConfigLoaders.find(
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
  var getEditorSettings2 = () => async ({ dispatch }) => {
    const settings = await (0, import_api_fetch8.default)({
      path: "/wp-block-editor/v1/settings"
    });
    dispatch.receiveEditorSettings(settings);
  };
  var getEditorAssets2 = () => async ({ dispatch }) => {
    const assets = await (0, import_api_fetch8.default)({
      path: "/wp-block-editor/v1/assets"
    });
    dispatch.receiveEditorAssets(assets);
  };

  // packages/core-data/build-module/locks/utils.js
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
  function hasConflictingLock({ exclusive }, locks2) {
    if (exclusive && locks2.length) {
      return true;
    }
    if (!exclusive && locks2.filter((lock2) => lock2.exclusive).length) {
      return true;
    }
    return false;
  }

  // packages/core-data/build-module/locks/reducer.js
  var DEFAULT_STATE = {
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
        const { lock: lock2, request } = action;
        const { store: store2, path } = request;
        const storePath = [store2, ...path];
        const newTree = deepCopyLocksTreePath(state.tree, storePath);
        const node = getNode(newTree, storePath);
        node.locks = [...node.locks, lock2];
        return {
          ...state,
          requests: state.requests.filter((r) => r !== request),
          tree: newTree
        };
      }
      case "RELEASE_LOCK": {
        const { lock: lock2 } = action;
        const storePath = [lock2.store, ...lock2.path];
        const newTree = deepCopyLocksTreePath(state.tree, storePath);
        const node = getNode(newTree, storePath);
        node.locks = node.locks.filter((l) => l !== lock2);
        return {
          ...state,
          tree: newTree
        };
      }
    }
    return state;
  }

  // packages/core-data/build-module/locks/selectors.js
  function getPendingLockRequests(state) {
    return state.requests;
  }
  function isLockAvailable(state, store2, path, { exclusive }) {
    const storePath = [store2, ...path];
    const locks2 = state.tree;
    for (const node2 of iteratePath(locks2, storePath)) {
      if (hasConflictingLock({ exclusive }, node2.locks)) {
        return false;
      }
    }
    const node = getNode(locks2, storePath);
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

  // packages/core-data/build-module/locks/engine.js
  function createLocks() {
    let state = locks(void 0, { type: "@@INIT" });
    function processPendingLockRequests() {
      for (const request of getPendingLockRequests(state)) {
        const { store: store2, path, exclusive, notifyAcquired } = request;
        if (isLockAvailable(state, store2, path, { exclusive })) {
          const lock2 = { store: store2, path, exclusive };
          state = locks(state, {
            type: "GRANT_LOCK_REQUEST",
            lock: lock2,
            request
          });
          notifyAcquired(lock2);
        }
      }
    }
    function acquire(store2, path, exclusive) {
      return new Promise((resolve2) => {
        state = locks(state, {
          type: "ENQUEUE_LOCK_REQUEST",
          request: { store: store2, path, exclusive, notifyAcquired: resolve2 }
        });
        processPendingLockRequests();
      });
    }
    function release(lock2) {
      state = locks(state, {
        type: "RELEASE_LOCK",
        lock: lock2
      });
      processPendingLockRequests();
    }
    return { acquire, release };
  }

  // packages/core-data/build-module/locks/actions.js
  function createLocksActions() {
    const locks2 = createLocks();
    function __unstableAcquireStoreLock(store2, path, { exclusive }) {
      return () => locks2.acquire(store2, path, exclusive);
    }
    function __unstableReleaseStoreLock(lock2) {
      return () => locks2.release(lock2);
    }
    return { __unstableAcquireStoreLock, __unstableReleaseStoreLock };
  }

  // packages/core-data/build-module/dynamic-entities.js
  var dynamicActions;
  var dynamicSelectors;

  // packages/core-data/build-module/entity-provider.js
  var import_element2 = __toESM(require_element());

  // packages/core-data/build-module/entity-context.js
  var import_element = __toESM(require_element());
  var EntityContext = (0, import_element.createContext)({});
  EntityContext.displayName = "EntityContext";

  // packages/core-data/build-module/entity-provider.js
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  function EntityProvider({ kind, type: name, id: id2, children }) {
    const parent = (0, import_element2.useContext)(EntityContext);
    const childContext = (0, import_element2.useMemo)(
      () => ({
        ...parent,
        [kind]: {
          ...parent?.[kind],
          [name]: id2
        }
      }),
      [parent, kind, name, id2]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(EntityContext.Provider, { value: childContext, children });
  }

  // packages/core-data/build-module/hooks/use-entity-record.js
  var import_data7 = __toESM(require_data());
  var import_deprecated4 = __toESM(require_deprecated());
  var import_element3 = __toESM(require_element());

  // packages/core-data/build-module/hooks/use-query-select.js
  var import_data6 = __toESM(require_data());

  // node_modules/memize/dist/index.js
  function memize(fn, options) {
    var size = 0;
    var head;
    var tail;
    options = options || {};
    function memoized() {
      var node = head, len = arguments.length, args2, i;
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
      args2 = new Array(len);
      for (i = 0; i < len; i++) {
        args2[i] = arguments[i];
      }
      node = {
        args: args2,
        // Generate the result from original function
        val: fn.apply(null, args2)
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

  // packages/core-data/build-module/hooks/memoize.js
  var memoize_default = memize;

  // packages/core-data/build-module/hooks/constants.js
  var Status = /* @__PURE__ */ ((Status2) => {
    Status2["Idle"] = "IDLE";
    Status2["Resolving"] = "RESOLVING";
    Status2["Error"] = "ERROR";
    Status2["Success"] = "SUCCESS";
    return Status2;
  })(Status || {});

  // packages/core-data/build-module/hooks/use-query-select.js
  var META_SELECTORS = [
    "getIsResolving",
    "hasStartedResolution",
    "hasFinishedResolution",
    "isResolving",
    "getCachedResolvers"
  ];
  function useQuerySelect(mapQuerySelect, deps) {
    return (0, import_data6.useSelect)((select, registry) => {
      const resolve2 = (store2) => enrichSelectors(select(store2));
      return mapQuerySelect(resolve2, registry);
    }, deps);
  }
  var enrichSelectors = memoize_default(((selectors) => {
    const resolvers = {};
    for (const selectorName in selectors) {
      if (META_SELECTORS.includes(selectorName)) {
        continue;
      }
      Object.defineProperty(resolvers, selectorName, {
        get: () => (...args2) => {
          const data = selectors[selectorName](...args2);
          const resolutionStatus = selectors.getResolutionState(
            selectorName,
            args2
          )?.status;
          let status;
          switch (resolutionStatus) {
            case "resolving":
              status = Status.Resolving;
              break;
            case "finished":
              status = Status.Success;
              break;
            case "error":
              status = Status.Error;
              break;
            case void 0:
              status = Status.Idle;
              break;
          }
          return {
            data,
            status,
            isResolving: status === Status.Resolving,
            hasStarted: status !== Status.Idle,
            hasResolved: status === Status.Success || status === Status.Error
          };
        }
      });
    }
    return resolvers;
  }));

  // packages/core-data/build-module/hooks/use-entity-record.js
  var EMPTY_OBJECT2 = {};
  function useEntityRecord(kind, name, recordId, options = { enabled: true }) {
    const { editEntityRecord: editEntityRecord2, saveEditedEntityRecord: saveEditedEntityRecord2 } = (0, import_data7.useDispatch)(store);
    const mutations = (0, import_element3.useMemo)(
      () => ({
        edit: (record2, editOptions = {}) => editEntityRecord2(kind, name, recordId, record2, editOptions),
        save: (saveOptions = {}) => saveEditedEntityRecord2(kind, name, recordId, {
          throwOnError: true,
          ...saveOptions
        })
      }),
      [editEntityRecord2, kind, name, recordId, saveEditedEntityRecord2]
    );
    const { editedRecord, hasEdits, edits } = (0, import_data7.useSelect)(
      (select) => {
        if (!options.enabled) {
          return {
            editedRecord: EMPTY_OBJECT2,
            hasEdits: false,
            edits: EMPTY_OBJECT2
          };
        }
        return {
          editedRecord: select(store).getEditedEntityRecord(
            kind,
            name,
            recordId
          ),
          hasEdits: select(store).hasEditsForEntityRecord(
            kind,
            name,
            recordId
          ),
          edits: select(store).getEntityRecordNonTransientEdits(
            kind,
            name,
            recordId
          )
        };
      },
      [kind, name, recordId, options.enabled]
    );
    const { data: record, ...querySelectRest } = useQuerySelect(
      (query) => {
        if (!options.enabled) {
          return {
            data: null
          };
        }
        return query(store).getEntityRecord(kind, name, recordId);
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
    (0, import_deprecated4.default)(`wp.data.__experimentalUseEntityRecord`, {
      alternative: "wp.data.useEntityRecord",
      since: "6.1"
    });
    return useEntityRecord(kind, name, recordId, options);
  }

  // packages/core-data/build-module/hooks/use-entity-records.js
  var import_url8 = __toESM(require_url());
  var import_deprecated5 = __toESM(require_deprecated());
  var import_data8 = __toESM(require_data());
  var import_element4 = __toESM(require_element());
  var EMPTY_ARRAY = [];
  function useEntityRecords(kind, name, queryArgs = {}, options = { enabled: true }) {
    const queryAsString = (0, import_url8.addQueryArgs)("", queryArgs);
    const { data: records, ...rest } = useQuerySelect(
      (query) => {
        if (!options.enabled) {
          return {
            // Avoiding returning a new reference on every execution.
            data: EMPTY_ARRAY
          };
        }
        return query(store).getEntityRecords(kind, name, queryArgs);
      },
      [kind, name, queryAsString, options.enabled]
    );
    const { totalItems, totalPages } = (0, import_data8.useSelect)(
      (select) => {
        if (!options.enabled) {
          return {
            totalItems: null,
            totalPages: null
          };
        }
        return {
          totalItems: select(store).getEntityRecordsTotalItems(
            kind,
            name,
            queryArgs
          ),
          totalPages: select(store).getEntityRecordsTotalPages(
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
    (0, import_deprecated5.default)(`wp.data.__experimentalUseEntityRecords`, {
      alternative: "wp.data.useEntityRecords",
      since: "6.1"
    });
    return useEntityRecords(kind, name, queryArgs, options);
  }
  function useEntityRecordsWithPermissions(kind, name, queryArgs = {}, options = { enabled: true }) {
    const entityConfig = (0, import_data8.useSelect)(
      (select) => select(store).getEntityConfig(kind, name),
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
              ...get_normalized_comma_separable_default(
                queryArgs._fields
              ) || [],
              "_links"
            ])
          ].join()
        } : {}
      },
      options
    );
    const ids = (0, import_element4.useMemo)(
      () => data?.map(
        // @ts-ignore
        (record) => record[entityConfig?.key ?? "id"]
      ) ?? [],
      [data, entityConfig?.key]
    );
    const permissions = (0, import_data8.useSelect)(
      (select) => {
        const { getEntityRecordsPermissions: getEntityRecordsPermissions2 } = unlock(
          select(store)
        );
        return getEntityRecordsPermissions2(kind, name, ids);
      },
      [ids, kind, name]
    );
    const dataWithPermissions = (0, import_element4.useMemo)(
      () => data?.map((record, index) => ({
        // @ts-ignore
        ...record,
        permissions: permissions[index]
      })) ?? [],
      [data, permissions]
    );
    return { records: dataWithPermissions, ...ret };
  }

  // packages/core-data/build-module/hooks/use-resource-permissions.js
  var import_deprecated6 = __toESM(require_deprecated());
  var import_warning = __toESM(require_warning());
  function useResourcePermissions(resource, id2) {
    const isEntity = typeof resource === "object";
    const resourceAsString = isEntity ? JSON.stringify(resource) : resource;
    if (isEntity && typeof id2 !== "undefined") {
      (0, import_warning.default)(
        `When 'resource' is an entity object, passing 'id' as a separate argument isn't supported.`
      );
    }
    return useQuerySelect(
      (resolve2) => {
        const hasId = isEntity ? !!resource.id : !!id2;
        const { canUser: canUser3 } = resolve2(store);
        const create8 = canUser3(
          "create",
          isEntity ? { kind: resource.kind, name: resource.name } : resource
        );
        if (!hasId) {
          const read2 = canUser3("read", resource);
          const isResolving2 = create8.isResolving || read2.isResolving;
          const hasResolved2 = create8.hasResolved && read2.hasResolved;
          let status2 = Status.Idle;
          if (isResolving2) {
            status2 = Status.Resolving;
          } else if (hasResolved2) {
            status2 = Status.Success;
          }
          return {
            status: status2,
            isResolving: isResolving2,
            hasResolved: hasResolved2,
            canCreate: create8.hasResolved && create8.data,
            canRead: read2.hasResolved && read2.data
          };
        }
        const read = canUser3("read", resource, id2);
        const update = canUser3("update", resource, id2);
        const _delete = canUser3("delete", resource, id2);
        const isResolving = read.isResolving || create8.isResolving || update.isResolving || _delete.isResolving;
        const hasResolved = read.hasResolved && create8.hasResolved && update.hasResolved && _delete.hasResolved;
        let status = Status.Idle;
        if (isResolving) {
          status = Status.Resolving;
        } else if (hasResolved) {
          status = Status.Success;
        }
        return {
          status,
          isResolving,
          hasResolved,
          canRead: hasResolved && read.data,
          canCreate: hasResolved && create8.data,
          canUpdate: hasResolved && update.data,
          canDelete: hasResolved && _delete.data
        };
      },
      [resourceAsString, id2]
    );
  }
  var use_resource_permissions_default = useResourcePermissions;
  function __experimentalUseResourcePermissions(resource, id2) {
    (0, import_deprecated6.default)(`wp.data.__experimentalUseResourcePermissions`, {
      alternative: "wp.data.useResourcePermissions",
      since: "6.1"
    });
    return useResourcePermissions(resource, id2);
  }

  // packages/core-data/build-module/hooks/use-entity-block-editor.js
  var import_element6 = __toESM(require_element());
  var import_data9 = __toESM(require_data());
  var import_blocks4 = __toESM(require_blocks());

  // packages/core-data/build-module/hooks/use-entity-id.js
  var import_element5 = __toESM(require_element());
  function useEntityId(kind, name) {
    const context = (0, import_element5.useContext)(EntityContext);
    return context?.[kind]?.[name];
  }

  // packages/core-data/build-module/footnotes/index.js
  var import_rich_text2 = __toESM(require_rich_text());

  // packages/core-data/build-module/footnotes/get-rich-text-values-cached.js
  var import_block_editor = __toESM(require_block_editor());
  var unlockedApis;
  var cache = /* @__PURE__ */ new WeakMap();
  function getRichTextValuesCached(block) {
    if (!unlockedApis) {
      unlockedApis = unlock(import_block_editor.privateApis);
    }
    if (!cache.has(block)) {
      const values = unlockedApis.getRichTextValues([block]);
      cache.set(block, values);
    }
    return cache.get(block);
  }

  // packages/core-data/build-module/footnotes/get-footnotes-order.js
  var cache2 = /* @__PURE__ */ new WeakMap();
  function getBlockFootnotesOrder(block) {
    if (!cache2.has(block)) {
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
      cache2.set(block, order);
    }
    return cache2.get(block);
  }
  function getFootnotesOrder(blocks) {
    return blocks.flatMap(getBlockFootnotesOrder);
  }

  // packages/core-data/build-module/footnotes/index.js
  var oldFootnotes = {};
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
        if (typeof value !== "string" && !(value instanceof import_rich_text2.RichTextData)) {
          continue;
        }
        const richTextValue = typeof value === "string" ? import_rich_text2.RichTextData.fromHTMLString(value) : new import_rich_text2.RichTextData(value);
        let hasFootnotes = false;
        richTextValue.replacements.forEach((replacement) => {
          if (replacement.type === "core/footnote") {
            const id2 = replacement.attributes["data-fn"];
            const index = newOrder.indexOf(id2);
            const countValue = (0, import_rich_text2.create)({
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
            replacement.innerHTML = (0, import_rich_text2.toHTMLString)({
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

  // packages/core-data/build-module/hooks/use-entity-block-editor.js
  var EMPTY_ARRAY2 = [];
  var parsedBlocksCache = /* @__PURE__ */ new WeakMap();
  function useEntityBlockEditor(kind, name, { id: _id } = {}) {
    const providerId = useEntityId(kind, name);
    const id2 = _id ?? providerId;
    const { getEntityRecord: getEntityRecord3, getEntityRecordEdits: getEntityRecordEdits2 } = (0, import_data9.useSelect)(STORE_NAME);
    const { content, editedBlocks, meta } = (0, import_data9.useSelect)(
      (select) => {
        if (!id2) {
          return {};
        }
        const { getEditedEntityRecord: getEditedEntityRecord3 } = select(STORE_NAME);
        const editedRecord = getEditedEntityRecord3(kind, name, id2);
        return {
          editedBlocks: editedRecord.blocks,
          content: editedRecord.content,
          meta: editedRecord.meta
        };
      },
      [kind, name, id2]
    );
    const { __unstableCreateUndoLevel: __unstableCreateUndoLevel2, editEntityRecord: editEntityRecord2 } = (0, import_data9.useDispatch)(STORE_NAME);
    const blocks = (0, import_element6.useMemo)(() => {
      if (!id2) {
        return void 0;
      }
      if (editedBlocks) {
        return editedBlocks;
      }
      if (!content || typeof content !== "string") {
        return EMPTY_ARRAY2;
      }
      const edits = getEntityRecordEdits2(kind, name, id2);
      const isUnedited = !edits || !Object.keys(edits).length;
      const cackeKey = isUnedited ? getEntityRecord3(kind, name, id2) : edits;
      let _blocks = parsedBlocksCache.get(cackeKey);
      if (!_blocks) {
        _blocks = (0, import_blocks4.parse)(content);
        parsedBlocksCache.set(cackeKey, _blocks);
      }
      return _blocks;
    }, [
      kind,
      name,
      id2,
      editedBlocks,
      content,
      getEntityRecord3,
      getEntityRecordEdits2
    ]);
    const onChange2 = (0, import_element6.useCallback)(
      (newBlocks, options) => {
        const noChange = blocks === newBlocks;
        if (noChange) {
          return __unstableCreateUndoLevel2(kind, name, id2);
        }
        const { selection, ...rest } = options;
        const edits = {
          selection,
          content: ({ blocks: blocksForSerialization = [] }) => (0, import_blocks4.__unstableSerializeAndClean)(blocksForSerialization),
          ...updateFootnotesFromMeta(newBlocks, meta)
        };
        editEntityRecord2(kind, name, id2, edits, {
          isCached: false,
          ...rest
        });
      },
      [
        kind,
        name,
        id2,
        blocks,
        meta,
        __unstableCreateUndoLevel2,
        editEntityRecord2
      ]
    );
    const onInput = (0, import_element6.useCallback)(
      (newBlocks, options) => {
        const { selection, ...rest } = options;
        const footnotesChanges = updateFootnotesFromMeta(newBlocks, meta);
        const edits = { selection, ...footnotesChanges };
        editEntityRecord2(kind, name, id2, edits, {
          isCached: true,
          ...rest
        });
      },
      [kind, name, id2, meta, editEntityRecord2]
    );
    return [blocks, onInput, onChange2];
  }

  // packages/core-data/build-module/hooks/use-entity-prop.js
  var import_element7 = __toESM(require_element());
  var import_data10 = __toESM(require_data());
  function useEntityProp(kind, name, prop, _id) {
    const providerId = useEntityId(kind, name);
    const id2 = _id ?? providerId;
    const { value, fullValue } = (0, import_data10.useSelect)(
      (select) => {
        const { getEntityRecord: getEntityRecord3, getEditedEntityRecord: getEditedEntityRecord3 } = select(STORE_NAME);
        const record = getEntityRecord3(kind, name, id2);
        const editedRecord = getEditedEntityRecord3(kind, name, id2);
        return record && editedRecord ? {
          value: editedRecord[prop],
          fullValue: record[prop]
        } : {};
      },
      [kind, name, id2, prop]
    );
    const { editEntityRecord: editEntityRecord2 } = (0, import_data10.useDispatch)(STORE_NAME);
    const setValue = (0, import_element7.useCallback)(
      (newValue) => {
        editEntityRecord2(kind, name, id2, {
          [prop]: newValue
        });
      },
      [editEntityRecord2, kind, name, id2, prop]
    );
    return [value, setValue, fullValue];
  }

  // packages/core-data/build-module/private-apis.js
  var privateApis = {};
  lock(privateApis, {
    useEntityRecordsWithPermissions,
    RECEIVE_INTERMEDIATE_RESULTS
  });

  // packages/core-data/build-module/index.js
  var entitiesConfig2 = [
    ...rootEntitiesConfig,
    ...additionalEntityConfigLoaders.filter((config) => !!config.name)
  ];
  var entitySelectors = entitiesConfig2.reduce((result, entity2) => {
    const { kind, name, plural } = entity2;
    const getEntityRecordMethodName = getMethodName(kind, name);
    result[getEntityRecordMethodName] = (state, key, query) => {
      logEntityDeprecation(kind, name, getEntityRecordMethodName, {
        isShorthandSelector: true,
        alternativeFunctionName: "getEntityRecord"
      });
      return getEntityRecord(state, kind, name, key, query);
    };
    if (plural) {
      const getEntityRecordsMethodName = getMethodName(kind, plural, "get");
      result[getEntityRecordsMethodName] = (state, query) => {
        logEntityDeprecation(kind, name, getEntityRecordsMethodName, {
          isShorthandSelector: true,
          alternativeFunctionName: "getEntityRecords"
        });
        return getEntityRecords(state, kind, name, query);
      };
    }
    return result;
  }, {});
  var entityResolvers = entitiesConfig2.reduce((result, entity2) => {
    const { kind, name, plural } = entity2;
    const getEntityRecordMethodName = getMethodName(kind, name);
    result[getEntityRecordMethodName] = (key, query) => {
      logEntityDeprecation(kind, name, getEntityRecordMethodName, {
        isShorthandSelector: true,
        alternativeFunctionName: "getEntityRecord"
      });
      return getEntityRecord2(kind, name, key, query);
    };
    if (plural) {
      const getEntityRecordsMethodName = getMethodName(kind, plural, "get");
      result[getEntityRecordsMethodName] = (...args2) => {
        logEntityDeprecation(kind, plural, getEntityRecordsMethodName, {
          isShorthandSelector: true,
          alternativeFunctionName: "getEntityRecords"
        });
        return getEntityRecords2(kind, name, ...args2);
      };
      result[getEntityRecordsMethodName].shouldInvalidate = (action) => getEntityRecords2.shouldInvalidate(action, kind, name);
    }
    return result;
  }, {});
  var entityActions = entitiesConfig2.reduce((result, entity2) => {
    const { kind, name } = entity2;
    const saveEntityRecordMethodName = getMethodName(kind, name, "save");
    result[saveEntityRecordMethodName] = (record, options) => {
      logEntityDeprecation(kind, name, saveEntityRecordMethodName, {
        isShorthandSelector: true,
        alternativeFunctionName: "saveEntityRecord"
      });
      return saveEntityRecord(kind, name, record, options);
    };
    const deleteEntityRecordMethodName = getMethodName(kind, name, "delete");
    result[deleteEntityRecordMethodName] = (key, query, options) => {
      logEntityDeprecation(kind, name, deleteEntityRecordMethodName, {
        isShorthandSelector: true,
        alternativeFunctionName: "deleteEntityRecord"
      });
      return deleteEntityRecord(kind, name, key, query, options);
    };
    return result;
  }, {});
  var storeConfig = () => ({
    reducer: reducer_default2,
    actions: {
      ...dynamicActions,
      ...actions_exports,
      ...entityActions,
      ...createLocksActions()
    },
    selectors: {
      ...dynamicSelectors,
      ...selectors_exports,
      ...entitySelectors
    },
    resolvers: { ...resolvers_exports, ...entityResolvers }
  });
  var store = (0, import_data11.createReduxStore)(STORE_NAME, storeConfig());
  unlock(store).registerPrivateSelectors(private_selectors_exports);
  unlock(store).registerPrivateActions(private_actions_exports);
  (0, import_data11.register)(store);
  return __toCommonJS(index_exports);
})();
/*! Bundled license information:

simple-peer/simplepeer.min.js:
  (*!
  * The buffer module from node.js, for the browser.
  *
  * @author   Feross Aboukhadijeh <https://feross.org>
  * @license  MIT
  *)
  (*! ieee754. BSD-3-Clause License. Feross Aboukhadijeh <https://feross.org/opensource> *)
  (*! queue-microtask. MIT License. Feross Aboukhadijeh <https://feross.org/opensource> *)
  (*! safe-buffer. MIT License. Feross Aboukhadijeh <https://feross.org/opensource> *)
  (*! simple-peer. MIT License. Feross Aboukhadijeh <https://feross.org/opensource> *)
*/
//# sourceMappingURL=index.js.map
