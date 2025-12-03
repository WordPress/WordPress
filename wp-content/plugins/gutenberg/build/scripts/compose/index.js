"use strict";
var wp;
(wp ||= {}).compose = (() => {
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

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // package-external:@wordpress/is-shallow-equal
  var require_is_shallow_equal = __commonJS({
    "package-external:@wordpress/is-shallow-equal"(exports, module) {
      module.exports = window.wp.isShallowEqual;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/deprecated
  var require_deprecated = __commonJS({
    "package-external:@wordpress/deprecated"(exports, module) {
      module.exports = window.wp.deprecated;
    }
  });

  // package-external:@wordpress/dom
  var require_dom = __commonJS({
    "package-external:@wordpress/dom"(exports, module) {
      module.exports = window.wp.dom;
    }
  });

  // packages/compose/node_modules/clipboard/dist/clipboard.js
  var require_clipboard = __commonJS({
    "packages/compose/node_modules/clipboard/dist/clipboard.js"(exports, module) {
      (function webpackUniversalModuleDefinition(root, factory) {
        if (typeof exports === "object" && typeof module === "object")
          module.exports = factory();
        else if (typeof define === "function" && define.amd)
          define([], factory);
        else if (typeof exports === "object")
          exports["ClipboardJS"] = factory();
        else
          root["ClipboardJS"] = factory();
      })(exports, function() {
        return (
          /******/
          (function() {
            var __webpack_modules__ = {
              /***/
              686: (
                /***/
                (function(__unused_webpack_module, __webpack_exports__, __webpack_require__2) {
                  "use strict";
                  __webpack_require__2.d(__webpack_exports__, {
                    "default": function() {
                      return (
                        /* binding */
                        clipboard
                      );
                    }
                  });
                  var tiny_emitter = __webpack_require__2(279);
                  var tiny_emitter_default = /* @__PURE__ */ __webpack_require__2.n(tiny_emitter);
                  var listen = __webpack_require__2(370);
                  var listen_default = /* @__PURE__ */ __webpack_require__2.n(listen);
                  var src_select = __webpack_require__2(817);
                  var select_default = /* @__PURE__ */ __webpack_require__2.n(src_select);
                  ;
                  function command(type) {
                    try {
                      return document.execCommand(type);
                    } catch (err) {
                      return false;
                    }
                  }
                  ;
                  var ClipboardActionCut = function ClipboardActionCut2(target) {
                    var selectedText = select_default()(target);
                    command("cut");
                    return selectedText;
                  };
                  var actions_cut = ClipboardActionCut;
                  ;
                  function createFakeElement(value) {
                    var isRTL = document.documentElement.getAttribute("dir") === "rtl";
                    var fakeElement = document.createElement("textarea");
                    fakeElement.style.fontSize = "12pt";
                    fakeElement.style.border = "0";
                    fakeElement.style.padding = "0";
                    fakeElement.style.margin = "0";
                    fakeElement.style.position = "absolute";
                    fakeElement.style[isRTL ? "right" : "left"] = "-9999px";
                    var yPosition = window.pageYOffset || document.documentElement.scrollTop;
                    fakeElement.style.top = "".concat(yPosition, "px");
                    fakeElement.setAttribute("readonly", "");
                    fakeElement.value = value;
                    return fakeElement;
                  }
                  ;
                  var fakeCopyAction = function fakeCopyAction2(value, options) {
                    var fakeElement = createFakeElement(value);
                    options.container.appendChild(fakeElement);
                    var selectedText = select_default()(fakeElement);
                    command("copy");
                    fakeElement.remove();
                    return selectedText;
                  };
                  var ClipboardActionCopy = function ClipboardActionCopy2(target) {
                    var options = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {
                      container: document.body
                    };
                    var selectedText = "";
                    if (typeof target === "string") {
                      selectedText = fakeCopyAction(target, options);
                    } else if (target instanceof HTMLInputElement && !["text", "search", "url", "tel", "password"].includes(target === null || target === void 0 ? void 0 : target.type)) {
                      selectedText = fakeCopyAction(target.value, options);
                    } else {
                      selectedText = select_default()(target);
                      command("copy");
                    }
                    return selectedText;
                  };
                  var actions_copy = ClipboardActionCopy;
                  ;
                  function _typeof(obj) {
                    "@babel/helpers - typeof";
                    if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
                      _typeof = function _typeof2(obj2) {
                        return typeof obj2;
                      };
                    } else {
                      _typeof = function _typeof2(obj2) {
                        return obj2 && typeof Symbol === "function" && obj2.constructor === Symbol && obj2 !== Symbol.prototype ? "symbol" : typeof obj2;
                      };
                    }
                    return _typeof(obj);
                  }
                  var ClipboardActionDefault = function ClipboardActionDefault2() {
                    var options = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {};
                    var _options$action = options.action, action = _options$action === void 0 ? "copy" : _options$action, container = options.container, target = options.target, text = options.text;
                    if (action !== "copy" && action !== "cut") {
                      throw new Error('Invalid "action" value, use either "copy" or "cut"');
                    }
                    if (target !== void 0) {
                      if (target && _typeof(target) === "object" && target.nodeType === 1) {
                        if (action === "copy" && target.hasAttribute("disabled")) {
                          throw new Error('Invalid "target" attribute. Please use "readonly" instead of "disabled" attribute');
                        }
                        if (action === "cut" && (target.hasAttribute("readonly") || target.hasAttribute("disabled"))) {
                          throw new Error(`Invalid "target" attribute. You can't cut text from elements with "readonly" or "disabled" attributes`);
                        }
                      } else {
                        throw new Error('Invalid "target" value, use a valid Element');
                      }
                    }
                    if (text) {
                      return actions_copy(text, {
                        container
                      });
                    }
                    if (target) {
                      return action === "cut" ? actions_cut(target) : actions_copy(target, {
                        container
                      });
                    }
                  };
                  var actions_default = ClipboardActionDefault;
                  ;
                  function clipboard_typeof(obj) {
                    "@babel/helpers - typeof";
                    if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
                      clipboard_typeof = function _typeof2(obj2) {
                        return typeof obj2;
                      };
                    } else {
                      clipboard_typeof = function _typeof2(obj2) {
                        return obj2 && typeof Symbol === "function" && obj2.constructor === Symbol && obj2 !== Symbol.prototype ? "symbol" : typeof obj2;
                      };
                    }
                    return clipboard_typeof(obj);
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
                  function _inherits(subClass, superClass) {
                    if (typeof superClass !== "function" && superClass !== null) {
                      throw new TypeError("Super expression must either be null or a function");
                    }
                    subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } });
                    if (superClass) _setPrototypeOf(subClass, superClass);
                  }
                  function _setPrototypeOf(o, p) {
                    _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf2(o2, p2) {
                      o2.__proto__ = p2;
                      return o2;
                    };
                    return _setPrototypeOf(o, p);
                  }
                  function _createSuper(Derived) {
                    var hasNativeReflectConstruct = _isNativeReflectConstruct();
                    return function _createSuperInternal() {
                      var Super = _getPrototypeOf(Derived), result;
                      if (hasNativeReflectConstruct) {
                        var NewTarget = _getPrototypeOf(this).constructor;
                        result = Reflect.construct(Super, arguments, NewTarget);
                      } else {
                        result = Super.apply(this, arguments);
                      }
                      return _possibleConstructorReturn(this, result);
                    };
                  }
                  function _possibleConstructorReturn(self, call) {
                    if (call && (clipboard_typeof(call) === "object" || typeof call === "function")) {
                      return call;
                    }
                    return _assertThisInitialized(self);
                  }
                  function _assertThisInitialized(self) {
                    if (self === void 0) {
                      throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
                    }
                    return self;
                  }
                  function _isNativeReflectConstruct() {
                    if (typeof Reflect === "undefined" || !Reflect.construct) return false;
                    if (Reflect.construct.sham) return false;
                    if (typeof Proxy === "function") return true;
                    try {
                      Date.prototype.toString.call(Reflect.construct(Date, [], function() {
                      }));
                      return true;
                    } catch (e) {
                      return false;
                    }
                  }
                  function _getPrototypeOf(o) {
                    _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf2(o2) {
                      return o2.__proto__ || Object.getPrototypeOf(o2);
                    };
                    return _getPrototypeOf(o);
                  }
                  function getAttributeValue(suffix, element) {
                    var attribute = "data-clipboard-".concat(suffix);
                    if (!element.hasAttribute(attribute)) {
                      return;
                    }
                    return element.getAttribute(attribute);
                  }
                  var Clipboard3 = /* @__PURE__ */ (function(_Emitter) {
                    _inherits(Clipboard4, _Emitter);
                    var _super = _createSuper(Clipboard4);
                    function Clipboard4(trigger, options) {
                      var _this;
                      _classCallCheck(this, Clipboard4);
                      _this = _super.call(this);
                      _this.resolveOptions(options);
                      _this.listenClick(trigger);
                      return _this;
                    }
                    _createClass(Clipboard4, [{
                      key: "resolveOptions",
                      value: function resolveOptions() {
                        var options = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : {};
                        this.action = typeof options.action === "function" ? options.action : this.defaultAction;
                        this.target = typeof options.target === "function" ? options.target : this.defaultTarget;
                        this.text = typeof options.text === "function" ? options.text : this.defaultText;
                        this.container = clipboard_typeof(options.container) === "object" ? options.container : document.body;
                      }
                      /**
                       * Adds a click event listener to the passed trigger.
                       * @param {String|HTMLElement|HTMLCollection|NodeList} trigger
                       */
                    }, {
                      key: "listenClick",
                      value: function listenClick(trigger) {
                        var _this2 = this;
                        this.listener = listen_default()(trigger, "click", function(e) {
                          return _this2.onClick(e);
                        });
                      }
                      /**
                       * Defines a new `ClipboardAction` on each click event.
                       * @param {Event} e
                       */
                    }, {
                      key: "onClick",
                      value: function onClick(e) {
                        var trigger = e.delegateTarget || e.currentTarget;
                        var action = this.action(trigger) || "copy";
                        var text = actions_default({
                          action,
                          container: this.container,
                          target: this.target(trigger),
                          text: this.text(trigger)
                        });
                        this.emit(text ? "success" : "error", {
                          action,
                          text,
                          trigger,
                          clearSelection: function clearSelection() {
                            if (trigger) {
                              trigger.focus();
                            }
                            window.getSelection().removeAllRanges();
                          }
                        });
                      }
                      /**
                       * Default `action` lookup function.
                       * @param {Element} trigger
                       */
                    }, {
                      key: "defaultAction",
                      value: function defaultAction(trigger) {
                        return getAttributeValue("action", trigger);
                      }
                      /**
                       * Default `target` lookup function.
                       * @param {Element} trigger
                       */
                    }, {
                      key: "defaultTarget",
                      value: function defaultTarget(trigger) {
                        var selector = getAttributeValue("target", trigger);
                        if (selector) {
                          return document.querySelector(selector);
                        }
                      }
                      /**
                       * Allow fire programmatically a copy action
                       * @param {String|HTMLElement} target
                       * @param {Object} options
                       * @returns Text copied.
                       */
                    }, {
                      key: "defaultText",
                      /**
                       * Default `text` lookup function.
                       * @param {Element} trigger
                       */
                      value: function defaultText(trigger) {
                        return getAttributeValue("text", trigger);
                      }
                      /**
                       * Destroy lifecycle.
                       */
                    }, {
                      key: "destroy",
                      value: function destroy() {
                        this.listener.destroy();
                      }
                    }], [{
                      key: "copy",
                      value: function copy(target) {
                        var options = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : {
                          container: document.body
                        };
                        return actions_copy(target, options);
                      }
                      /**
                       * Allow fire programmatically a cut action
                       * @param {String|HTMLElement} target
                       * @returns Text cutted.
                       */
                    }, {
                      key: "cut",
                      value: function cut(target) {
                        return actions_cut(target);
                      }
                      /**
                       * Returns the support of the given action, or all actions if no action is
                       * given.
                       * @param {String} [action]
                       */
                    }, {
                      key: "isSupported",
                      value: function isSupported() {
                        var action = arguments.length > 0 && arguments[0] !== void 0 ? arguments[0] : ["copy", "cut"];
                        var actions = typeof action === "string" ? [action] : action;
                        var support = !!document.queryCommandSupported;
                        actions.forEach(function(action2) {
                          support = support && !!document.queryCommandSupported(action2);
                        });
                        return support;
                      }
                    }]);
                    return Clipboard4;
                  })(tiny_emitter_default());
                  var clipboard = Clipboard3;
                })
              ),
              /***/
              828: (
                /***/
                (function(module2) {
                  var DOCUMENT_NODE_TYPE = 9;
                  if (typeof Element !== "undefined" && !Element.prototype.matches) {
                    var proto = Element.prototype;
                    proto.matches = proto.matchesSelector || proto.mozMatchesSelector || proto.msMatchesSelector || proto.oMatchesSelector || proto.webkitMatchesSelector;
                  }
                  function closest(element, selector) {
                    while (element && element.nodeType !== DOCUMENT_NODE_TYPE) {
                      if (typeof element.matches === "function" && element.matches(selector)) {
                        return element;
                      }
                      element = element.parentNode;
                    }
                  }
                  module2.exports = closest;
                })
              ),
              /***/
              438: (
                /***/
                (function(module2, __unused_webpack_exports, __webpack_require__2) {
                  var closest = __webpack_require__2(828);
                  function _delegate(element, selector, type, callback, useCapture) {
                    var listenerFn = listener2.apply(this, arguments);
                    element.addEventListener(type, listenerFn, useCapture);
                    return {
                      destroy: function() {
                        element.removeEventListener(type, listenerFn, useCapture);
                      }
                    };
                  }
                  function delegate(elements, selector, type, callback, useCapture) {
                    if (typeof elements.addEventListener === "function") {
                      return _delegate.apply(null, arguments);
                    }
                    if (typeof type === "function") {
                      return _delegate.bind(null, document).apply(null, arguments);
                    }
                    if (typeof elements === "string") {
                      elements = document.querySelectorAll(elements);
                    }
                    return Array.prototype.map.call(elements, function(element) {
                      return _delegate(element, selector, type, callback, useCapture);
                    });
                  }
                  function listener2(element, selector, type, callback) {
                    return function(e) {
                      e.delegateTarget = closest(e.target, selector);
                      if (e.delegateTarget) {
                        callback.call(element, e);
                      }
                    };
                  }
                  module2.exports = delegate;
                })
              ),
              /***/
              879: (
                /***/
                (function(__unused_webpack_module, exports2) {
                  exports2.node = function(value) {
                    return value !== void 0 && value instanceof HTMLElement && value.nodeType === 1;
                  };
                  exports2.nodeList = function(value) {
                    var type = Object.prototype.toString.call(value);
                    return value !== void 0 && (type === "[object NodeList]" || type === "[object HTMLCollection]") && "length" in value && (value.length === 0 || exports2.node(value[0]));
                  };
                  exports2.string = function(value) {
                    return typeof value === "string" || value instanceof String;
                  };
                  exports2.fn = function(value) {
                    var type = Object.prototype.toString.call(value);
                    return type === "[object Function]";
                  };
                })
              ),
              /***/
              370: (
                /***/
                (function(module2, __unused_webpack_exports, __webpack_require__2) {
                  var is = __webpack_require__2(879);
                  var delegate = __webpack_require__2(438);
                  function listen(target, type, callback) {
                    if (!target && !type && !callback) {
                      throw new Error("Missing required arguments");
                    }
                    if (!is.string(type)) {
                      throw new TypeError("Second argument must be a String");
                    }
                    if (!is.fn(callback)) {
                      throw new TypeError("Third argument must be a Function");
                    }
                    if (is.node(target)) {
                      return listenNode(target, type, callback);
                    } else if (is.nodeList(target)) {
                      return listenNodeList(target, type, callback);
                    } else if (is.string(target)) {
                      return listenSelector(target, type, callback);
                    } else {
                      throw new TypeError("First argument must be a String, HTMLElement, HTMLCollection, or NodeList");
                    }
                  }
                  function listenNode(node, type, callback) {
                    node.addEventListener(type, callback);
                    return {
                      destroy: function() {
                        node.removeEventListener(type, callback);
                      }
                    };
                  }
                  function listenNodeList(nodeList, type, callback) {
                    Array.prototype.forEach.call(nodeList, function(node) {
                      node.addEventListener(type, callback);
                    });
                    return {
                      destroy: function() {
                        Array.prototype.forEach.call(nodeList, function(node) {
                          node.removeEventListener(type, callback);
                        });
                      }
                    };
                  }
                  function listenSelector(selector, type, callback) {
                    return delegate(document.body, selector, type, callback);
                  }
                  module2.exports = listen;
                })
              ),
              /***/
              817: (
                /***/
                (function(module2) {
                  function select(element) {
                    var selectedText;
                    if (element.nodeName === "SELECT") {
                      element.focus();
                      selectedText = element.value;
                    } else if (element.nodeName === "INPUT" || element.nodeName === "TEXTAREA") {
                      var isReadOnly = element.hasAttribute("readonly");
                      if (!isReadOnly) {
                        element.setAttribute("readonly", "");
                      }
                      element.select();
                      element.setSelectionRange(0, element.value.length);
                      if (!isReadOnly) {
                        element.removeAttribute("readonly");
                      }
                      selectedText = element.value;
                    } else {
                      if (element.hasAttribute("contenteditable")) {
                        element.focus();
                      }
                      var selection = window.getSelection();
                      var range = document.createRange();
                      range.selectNodeContents(element);
                      selection.removeAllRanges();
                      selection.addRange(range);
                      selectedText = selection.toString();
                    }
                    return selectedText;
                  }
                  module2.exports = select;
                })
              ),
              /***/
              279: (
                /***/
                (function(module2) {
                  function E() {
                  }
                  E.prototype = {
                    on: function(name, callback, ctx) {
                      var e = this.e || (this.e = {});
                      (e[name] || (e[name] = [])).push({
                        fn: callback,
                        ctx
                      });
                      return this;
                    },
                    once: function(name, callback, ctx) {
                      var self = this;
                      function listener2() {
                        self.off(name, listener2);
                        callback.apply(ctx, arguments);
                      }
                      ;
                      listener2._ = callback;
                      return this.on(name, listener2, ctx);
                    },
                    emit: function(name) {
                      var data = [].slice.call(arguments, 1);
                      var evtArr = ((this.e || (this.e = {}))[name] || []).slice();
                      var i = 0;
                      var len = evtArr.length;
                      for (i; i < len; i++) {
                        evtArr[i].fn.apply(evtArr[i].ctx, data);
                      }
                      return this;
                    },
                    off: function(name, callback) {
                      var e = this.e || (this.e = {});
                      var evts = e[name];
                      var liveEvents = [];
                      if (evts && callback) {
                        for (var i = 0, len = evts.length; i < len; i++) {
                          if (evts[i].fn !== callback && evts[i].fn._ !== callback)
                            liveEvents.push(evts[i]);
                        }
                      }
                      liveEvents.length ? e[name] = liveEvents : delete e[name];
                      return this;
                    }
                  };
                  module2.exports = E;
                  module2.exports.TinyEmitter = E;
                })
              )
              /******/
            };
            var __webpack_module_cache__ = {};
            function __webpack_require__(moduleId) {
              if (__webpack_module_cache__[moduleId]) {
                return __webpack_module_cache__[moduleId].exports;
              }
              var module2 = __webpack_module_cache__[moduleId] = {
                /******/
                // no module.id needed
                /******/
                // no module.loaded needed
                /******/
                exports: {}
                /******/
              };
              __webpack_modules__[moduleId](module2, module2.exports, __webpack_require__);
              return module2.exports;
            }
            !(function() {
              __webpack_require__.n = function(module2) {
                var getter = module2 && module2.__esModule ? (
                  /******/
                  function() {
                    return module2["default"];
                  }
                ) : (
                  /******/
                  function() {
                    return module2;
                  }
                );
                __webpack_require__.d(getter, { a: getter });
                return getter;
              };
            })();
            !(function() {
              __webpack_require__.d = function(exports2, definition) {
                for (var key in definition) {
                  if (__webpack_require__.o(definition, key) && !__webpack_require__.o(exports2, key)) {
                    Object.defineProperty(exports2, key, { enumerable: true, get: definition[key] });
                  }
                }
              };
            })();
            !(function() {
              __webpack_require__.o = function(obj, prop) {
                return Object.prototype.hasOwnProperty.call(obj, prop);
              };
            })();
            return __webpack_require__(686);
          })().default
        );
      });
    }
  });

  // package-external:@wordpress/keycodes
  var require_keycodes = __commonJS({
    "package-external:@wordpress/keycodes"(exports, module) {
      module.exports = window.wp.keycodes;
    }
  });

  // node_modules/mousetrap/mousetrap.js
  var require_mousetrap = __commonJS({
    "node_modules/mousetrap/mousetrap.js"(exports, module) {
      (function(window2, document2, undefined2) {
        if (!window2) {
          return;
        }
        var _MAP = {
          8: "backspace",
          9: "tab",
          13: "enter",
          16: "shift",
          17: "ctrl",
          18: "alt",
          20: "capslock",
          27: "esc",
          32: "space",
          33: "pageup",
          34: "pagedown",
          35: "end",
          36: "home",
          37: "left",
          38: "up",
          39: "right",
          40: "down",
          45: "ins",
          46: "del",
          91: "meta",
          93: "meta",
          224: "meta"
        };
        var _KEYCODE_MAP = {
          106: "*",
          107: "+",
          109: "-",
          110: ".",
          111: "/",
          186: ";",
          187: "=",
          188: ",",
          189: "-",
          190: ".",
          191: "/",
          192: "`",
          219: "[",
          220: "\\",
          221: "]",
          222: "'"
        };
        var _SHIFT_MAP = {
          "~": "`",
          "!": "1",
          "@": "2",
          "#": "3",
          "$": "4",
          "%": "5",
          "^": "6",
          "&": "7",
          "*": "8",
          "(": "9",
          ")": "0",
          "_": "-",
          "+": "=",
          ":": ";",
          '"': "'",
          "<": ",",
          ">": ".",
          "?": "/",
          "|": "\\"
        };
        var _SPECIAL_ALIASES = {
          "option": "alt",
          "command": "meta",
          "return": "enter",
          "escape": "esc",
          "plus": "+",
          "mod": /Mac|iPod|iPhone|iPad/.test(navigator.platform) ? "meta" : "ctrl"
        };
        var _REVERSE_MAP;
        for (var i = 1; i < 20; ++i) {
          _MAP[111 + i] = "f" + i;
        }
        for (i = 0; i <= 9; ++i) {
          _MAP[i + 96] = i.toString();
        }
        function _addEvent(object, type, callback) {
          if (object.addEventListener) {
            object.addEventListener(type, callback, false);
            return;
          }
          object.attachEvent("on" + type, callback);
        }
        function _characterFromEvent(e) {
          if (e.type == "keypress") {
            var character = String.fromCharCode(e.which);
            if (!e.shiftKey) {
              character = character.toLowerCase();
            }
            return character;
          }
          if (_MAP[e.which]) {
            return _MAP[e.which];
          }
          if (_KEYCODE_MAP[e.which]) {
            return _KEYCODE_MAP[e.which];
          }
          return String.fromCharCode(e.which).toLowerCase();
        }
        function _modifiersMatch(modifiers1, modifiers2) {
          return modifiers1.sort().join(",") === modifiers2.sort().join(",");
        }
        function _eventModifiers(e) {
          var modifiers = [];
          if (e.shiftKey) {
            modifiers.push("shift");
          }
          if (e.altKey) {
            modifiers.push("alt");
          }
          if (e.ctrlKey) {
            modifiers.push("ctrl");
          }
          if (e.metaKey) {
            modifiers.push("meta");
          }
          return modifiers;
        }
        function _preventDefault(e) {
          if (e.preventDefault) {
            e.preventDefault();
            return;
          }
          e.returnValue = false;
        }
        function _stopPropagation(e) {
          if (e.stopPropagation) {
            e.stopPropagation();
            return;
          }
          e.cancelBubble = true;
        }
        function _isModifier(key) {
          return key == "shift" || key == "ctrl" || key == "alt" || key == "meta";
        }
        function _getReverseMap() {
          if (!_REVERSE_MAP) {
            _REVERSE_MAP = {};
            for (var key in _MAP) {
              if (key > 95 && key < 112) {
                continue;
              }
              if (_MAP.hasOwnProperty(key)) {
                _REVERSE_MAP[_MAP[key]] = key;
              }
            }
          }
          return _REVERSE_MAP;
        }
        function _pickBestAction(key, modifiers, action) {
          if (!action) {
            action = _getReverseMap()[key] ? "keydown" : "keypress";
          }
          if (action == "keypress" && modifiers.length) {
            action = "keydown";
          }
          return action;
        }
        function _keysFromString(combination) {
          if (combination === "+") {
            return ["+"];
          }
          combination = combination.replace(/\+{2}/g, "+plus");
          return combination.split("+");
        }
        function _getKeyInfo(combination, action) {
          var keys;
          var key;
          var i2;
          var modifiers = [];
          keys = _keysFromString(combination);
          for (i2 = 0; i2 < keys.length; ++i2) {
            key = keys[i2];
            if (_SPECIAL_ALIASES[key]) {
              key = _SPECIAL_ALIASES[key];
            }
            if (action && action != "keypress" && _SHIFT_MAP[key]) {
              key = _SHIFT_MAP[key];
              modifiers.push("shift");
            }
            if (_isModifier(key)) {
              modifiers.push(key);
            }
          }
          action = _pickBestAction(key, modifiers, action);
          return {
            key,
            modifiers,
            action
          };
        }
        function _belongsTo(element, ancestor) {
          if (element === null || element === document2) {
            return false;
          }
          if (element === ancestor) {
            return true;
          }
          return _belongsTo(element.parentNode, ancestor);
        }
        function Mousetrap3(targetElement) {
          var self = this;
          targetElement = targetElement || document2;
          if (!(self instanceof Mousetrap3)) {
            return new Mousetrap3(targetElement);
          }
          self.target = targetElement;
          self._callbacks = {};
          self._directMap = {};
          var _sequenceLevels = {};
          var _resetTimer;
          var _ignoreNextKeyup = false;
          var _ignoreNextKeypress = false;
          var _nextExpectedAction = false;
          function _resetSequences(doNotReset) {
            doNotReset = doNotReset || {};
            var activeSequences = false, key;
            for (key in _sequenceLevels) {
              if (doNotReset[key]) {
                activeSequences = true;
                continue;
              }
              _sequenceLevels[key] = 0;
            }
            if (!activeSequences) {
              _nextExpectedAction = false;
            }
          }
          function _getMatches(character, modifiers, e, sequenceName, combination, level) {
            var i2;
            var callback;
            var matches = [];
            var action = e.type;
            if (!self._callbacks[character]) {
              return [];
            }
            if (action == "keyup" && _isModifier(character)) {
              modifiers = [character];
            }
            for (i2 = 0; i2 < self._callbacks[character].length; ++i2) {
              callback = self._callbacks[character][i2];
              if (!sequenceName && callback.seq && _sequenceLevels[callback.seq] != callback.level) {
                continue;
              }
              if (action != callback.action) {
                continue;
              }
              if (action == "keypress" && !e.metaKey && !e.ctrlKey || _modifiersMatch(modifiers, callback.modifiers)) {
                var deleteCombo = !sequenceName && callback.combo == combination;
                var deleteSequence = sequenceName && callback.seq == sequenceName && callback.level == level;
                if (deleteCombo || deleteSequence) {
                  self._callbacks[character].splice(i2, 1);
                }
                matches.push(callback);
              }
            }
            return matches;
          }
          function _fireCallback(callback, e, combo, sequence) {
            if (self.stopCallback(e, e.target || e.srcElement, combo, sequence)) {
              return;
            }
            if (callback(e, combo) === false) {
              _preventDefault(e);
              _stopPropagation(e);
            }
          }
          self._handleKey = function(character, modifiers, e) {
            var callbacks = _getMatches(character, modifiers, e);
            var i2;
            var doNotReset = {};
            var maxLevel = 0;
            var processedSequenceCallback = false;
            for (i2 = 0; i2 < callbacks.length; ++i2) {
              if (callbacks[i2].seq) {
                maxLevel = Math.max(maxLevel, callbacks[i2].level);
              }
            }
            for (i2 = 0; i2 < callbacks.length; ++i2) {
              if (callbacks[i2].seq) {
                if (callbacks[i2].level != maxLevel) {
                  continue;
                }
                processedSequenceCallback = true;
                doNotReset[callbacks[i2].seq] = 1;
                _fireCallback(callbacks[i2].callback, e, callbacks[i2].combo, callbacks[i2].seq);
                continue;
              }
              if (!processedSequenceCallback) {
                _fireCallback(callbacks[i2].callback, e, callbacks[i2].combo);
              }
            }
            var ignoreThisKeypress = e.type == "keypress" && _ignoreNextKeypress;
            if (e.type == _nextExpectedAction && !_isModifier(character) && !ignoreThisKeypress) {
              _resetSequences(doNotReset);
            }
            _ignoreNextKeypress = processedSequenceCallback && e.type == "keydown";
          };
          function _handleKeyEvent(e) {
            if (typeof e.which !== "number") {
              e.which = e.keyCode;
            }
            var character = _characterFromEvent(e);
            if (!character) {
              return;
            }
            if (e.type == "keyup" && _ignoreNextKeyup === character) {
              _ignoreNextKeyup = false;
              return;
            }
            self.handleKey(character, _eventModifiers(e), e);
          }
          function _resetSequenceTimer() {
            clearTimeout(_resetTimer);
            _resetTimer = setTimeout(_resetSequences, 1e3);
          }
          function _bindSequence(combo, keys, callback, action) {
            _sequenceLevels[combo] = 0;
            function _increaseSequence(nextAction) {
              return function() {
                _nextExpectedAction = nextAction;
                ++_sequenceLevels[combo];
                _resetSequenceTimer();
              };
            }
            function _callbackAndReset(e) {
              _fireCallback(callback, e, combo);
              if (action !== "keyup") {
                _ignoreNextKeyup = _characterFromEvent(e);
              }
              setTimeout(_resetSequences, 10);
            }
            for (var i2 = 0; i2 < keys.length; ++i2) {
              var isFinal = i2 + 1 === keys.length;
              var wrappedCallback = isFinal ? _callbackAndReset : _increaseSequence(action || _getKeyInfo(keys[i2 + 1]).action);
              _bindSingle(keys[i2], wrappedCallback, action, combo, i2);
            }
          }
          function _bindSingle(combination, callback, action, sequenceName, level) {
            self._directMap[combination + ":" + action] = callback;
            combination = combination.replace(/\s+/g, " ");
            var sequence = combination.split(" ");
            var info;
            if (sequence.length > 1) {
              _bindSequence(combination, sequence, callback, action);
              return;
            }
            info = _getKeyInfo(combination, action);
            self._callbacks[info.key] = self._callbacks[info.key] || [];
            _getMatches(info.key, info.modifiers, { type: info.action }, sequenceName, combination, level);
            self._callbacks[info.key][sequenceName ? "unshift" : "push"]({
              callback,
              modifiers: info.modifiers,
              action: info.action,
              seq: sequenceName,
              level,
              combo: combination
            });
          }
          self._bindMultiple = function(combinations, callback, action) {
            for (var i2 = 0; i2 < combinations.length; ++i2) {
              _bindSingle(combinations[i2], callback, action);
            }
          };
          _addEvent(targetElement, "keypress", _handleKeyEvent);
          _addEvent(targetElement, "keydown", _handleKeyEvent);
          _addEvent(targetElement, "keyup", _handleKeyEvent);
        }
        Mousetrap3.prototype.bind = function(keys, callback, action) {
          var self = this;
          keys = keys instanceof Array ? keys : [keys];
          self._bindMultiple.call(self, keys, callback, action);
          return self;
        };
        Mousetrap3.prototype.unbind = function(keys, action) {
          var self = this;
          return self.bind.call(self, keys, function() {
          }, action);
        };
        Mousetrap3.prototype.trigger = function(keys, action) {
          var self = this;
          if (self._directMap[keys + ":" + action]) {
            self._directMap[keys + ":" + action]({}, keys);
          }
          return self;
        };
        Mousetrap3.prototype.reset = function() {
          var self = this;
          self._callbacks = {};
          self._directMap = {};
          return self;
        };
        Mousetrap3.prototype.stopCallback = function(e, element) {
          var self = this;
          if ((" " + element.className + " ").indexOf(" mousetrap ") > -1) {
            return false;
          }
          if (_belongsTo(element, self.target)) {
            return false;
          }
          if ("composedPath" in e && typeof e.composedPath === "function") {
            var initialEventTarget = e.composedPath()[0];
            if (initialEventTarget !== e.target) {
              element = initialEventTarget;
            }
          }
          return element.tagName == "INPUT" || element.tagName == "SELECT" || element.tagName == "TEXTAREA" || element.isContentEditable;
        };
        Mousetrap3.prototype.handleKey = function() {
          var self = this;
          return self._handleKey.apply(self, arguments);
        };
        Mousetrap3.addKeycodes = function(object) {
          for (var key in object) {
            if (object.hasOwnProperty(key)) {
              _MAP[key] = object[key];
            }
          }
          _REVERSE_MAP = null;
        };
        Mousetrap3.init = function() {
          var documentMousetrap = Mousetrap3(document2);
          for (var method in documentMousetrap) {
            if (method.charAt(0) !== "_") {
              Mousetrap3[method] = /* @__PURE__ */ (function(method2) {
                return function() {
                  return documentMousetrap[method2].apply(documentMousetrap, arguments);
                };
              })(method);
            }
          }
        };
        Mousetrap3.init();
        window2.Mousetrap = Mousetrap3;
        if (typeof module !== "undefined" && module.exports) {
          module.exports = Mousetrap3;
        }
        if (typeof define === "function" && define.amd) {
          define(function() {
            return Mousetrap3;
          });
        }
      })(typeof window !== "undefined" ? window : null, typeof window !== "undefined" ? document : null);
    }
  });

  // package-external:@wordpress/undo-manager
  var require_undo_manager = __commonJS({
    "package-external:@wordpress/undo-manager"(exports, module) {
      module.exports = window.wp.undoManager;
    }
  });

  // package-external:@wordpress/priority-queue
  var require_priority_queue = __commonJS({
    "package-external:@wordpress/priority-queue"(exports, module) {
      module.exports = window.wp.priorityQueue;
    }
  });

  // vendor-external:react
  var require_react = __commonJS({
    "vendor-external:react"(exports, module) {
      module.exports = window.React;
    }
  });

  // packages/compose/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    __experimentalUseDialog: () => use_dialog_default,
    __experimentalUseDragging: () => useDragging,
    __experimentalUseDropZone: () => useDropZone,
    __experimentalUseFixedWindowList: () => useFixedWindowList,
    __experimentalUseFocusOutside: () => useFocusOutside,
    compose: () => compose_default,
    createHigherOrderComponent: () => createHigherOrderComponent,
    debounce: () => debounce,
    ifCondition: () => if_condition_default,
    observableMap: () => observableMap,
    pipe: () => pipe_default,
    pure: () => pure_default,
    throttle: () => throttle,
    useAsyncList: () => use_async_list_default,
    useConstrainedTabbing: () => use_constrained_tabbing_default,
    useCopyOnClick: () => useCopyOnClick,
    useCopyToClipboard: () => useCopyToClipboard,
    useDebounce: () => useDebounce,
    useDebouncedInput: () => useDebouncedInput,
    useDisabled: () => useDisabled,
    useEvent: () => useEvent,
    useFocusOnMount: () => useFocusOnMount,
    useFocusReturn: () => use_focus_return_default,
    useFocusableIframe: () => useFocusableIframe,
    useInstanceId: () => use_instance_id_default,
    useIsomorphicLayoutEffect: () => use_isomorphic_layout_effect_default,
    useKeyboardShortcut: () => use_keyboard_shortcut_default,
    useMediaQuery: () => useMediaQuery,
    useMergeRefs: () => useMergeRefs,
    useObservableValue: () => useObservableValue,
    usePrevious: () => usePrevious,
    useReducedMotion: () => use_reduced_motion_default,
    useRefEffect: () => useRefEffect,
    useResizeObserver: () => useResizeObserver2,
    useStateWithHistory: () => useStateWithHistory,
    useThrottle: () => useThrottle,
    useViewportMatch: () => use_viewport_match_default,
    useWarnOnChange: () => use_warn_on_change_default,
    withGlobalEvents: () => withGlobalEvents,
    withInstanceId: () => with_instance_id_default,
    withSafeTimeout: () => with_safe_timeout_default,
    withState: () => withState
  });

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

  // packages/compose/build-module/utils/create-higher-order-component/index.js
  function createHigherOrderComponent(mapComponent, modifierName) {
    return (Inner) => {
      const Outer = mapComponent(Inner);
      Outer.displayName = hocName(modifierName, Inner);
      return Outer;
    };
  }
  var hocName = (name, Inner) => {
    const inner = Inner.displayName || Inner.name || "Component";
    const outer = pascalCase(name ?? "");
    return `${outer}(${inner})`;
  };

  // packages/compose/build-module/utils/debounce/index.js
  var debounce = (func, wait, options) => {
    let lastArgs;
    let lastThis;
    let maxWait = 0;
    let result;
    let timerId;
    let lastCallTime;
    let lastInvokeTime = 0;
    let leading = false;
    let maxing = false;
    let trailing = true;
    if (options) {
      leading = !!options.leading;
      maxing = "maxWait" in options;
      if (options.maxWait !== void 0) {
        maxWait = Math.max(options.maxWait, wait);
      }
      trailing = "trailing" in options ? !!options.trailing : trailing;
    }
    function invokeFunc(time) {
      const args = lastArgs;
      const thisArg = lastThis;
      lastArgs = void 0;
      lastThis = void 0;
      lastInvokeTime = time;
      result = func.apply(thisArg, args);
      return result;
    }
    function startTimer(pendingFunc, waitTime) {
      timerId = setTimeout(pendingFunc, waitTime);
    }
    function cancelTimer() {
      if (timerId !== void 0) {
        clearTimeout(timerId);
      }
    }
    function leadingEdge(time) {
      lastInvokeTime = time;
      startTimer(timerExpired, wait);
      return leading ? invokeFunc(time) : result;
    }
    function getTimeSinceLastCall(time) {
      return time - (lastCallTime || 0);
    }
    function remainingWait(time) {
      const timeSinceLastCall = getTimeSinceLastCall(time);
      const timeSinceLastInvoke = time - lastInvokeTime;
      const timeWaiting = wait - timeSinceLastCall;
      return maxing ? Math.min(timeWaiting, maxWait - timeSinceLastInvoke) : timeWaiting;
    }
    function shouldInvoke(time) {
      const timeSinceLastCall = getTimeSinceLastCall(time);
      const timeSinceLastInvoke = time - lastInvokeTime;
      return lastCallTime === void 0 || timeSinceLastCall >= wait || timeSinceLastCall < 0 || maxing && timeSinceLastInvoke >= maxWait;
    }
    function timerExpired() {
      const time = Date.now();
      if (shouldInvoke(time)) {
        return trailingEdge(time);
      }
      startTimer(timerExpired, remainingWait(time));
      return void 0;
    }
    function clearTimer() {
      timerId = void 0;
    }
    function trailingEdge(time) {
      clearTimer();
      if (trailing && lastArgs) {
        return invokeFunc(time);
      }
      lastArgs = lastThis = void 0;
      return result;
    }
    function cancel() {
      cancelTimer();
      lastInvokeTime = 0;
      clearTimer();
      lastArgs = lastCallTime = lastThis = void 0;
    }
    function flush() {
      return pending() ? trailingEdge(Date.now()) : result;
    }
    function pending() {
      return timerId !== void 0;
    }
    function debounced(...args) {
      const time = Date.now();
      const isInvoking = shouldInvoke(time);
      lastArgs = args;
      lastThis = this;
      lastCallTime = time;
      if (isInvoking) {
        if (!pending()) {
          return leadingEdge(lastCallTime);
        }
        if (maxing) {
          startTimer(timerExpired, wait);
          return invokeFunc(lastCallTime);
        }
      }
      if (!pending()) {
        startTimer(timerExpired, wait);
      }
      return result;
    }
    debounced.cancel = cancel;
    debounced.flush = flush;
    debounced.pending = pending;
    return debounced;
  };

  // packages/compose/build-module/utils/throttle/index.js
  var throttle = (func, wait, options) => {
    let leading = true;
    let trailing = true;
    if (options) {
      leading = "leading" in options ? !!options.leading : leading;
      trailing = "trailing" in options ? !!options.trailing : trailing;
    }
    return debounce(func, wait, {
      leading,
      trailing,
      maxWait: wait
    });
  };

  // packages/compose/build-module/utils/observable-map/index.js
  function observableMap() {
    const map = /* @__PURE__ */ new Map();
    const listeners = /* @__PURE__ */ new Map();
    function callListeners(name) {
      const list = listeners.get(name);
      if (!list) {
        return;
      }
      for (const listener2 of list) {
        listener2();
      }
    }
    return {
      get(name) {
        return map.get(name);
      },
      set(name, value) {
        map.set(name, value);
        callListeners(name);
      },
      delete(name) {
        map.delete(name);
        callListeners(name);
      },
      subscribe(name, listener2) {
        let list = listeners.get(name);
        if (!list) {
          list = /* @__PURE__ */ new Set();
          listeners.set(name, list);
        }
        list.add(listener2);
        return () => {
          list.delete(listener2);
          if (list.size === 0) {
            listeners.delete(name);
          }
        };
      }
    };
  }

  // packages/compose/build-module/higher-order/pipe.js
  var basePipe = (reverse = false) => (...funcs) => (...args) => {
    const functions = funcs.flat();
    if (reverse) {
      functions.reverse();
    }
    return functions.reduce(
      (prev, func) => [func(...prev)],
      args
    )[0];
  };
  var pipe = basePipe();
  var pipe_default = pipe;

  // packages/compose/build-module/higher-order/compose.js
  var compose = basePipe(true);
  var compose_default = compose;

  // packages/compose/build-module/higher-order/if-condition/index.js
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  function ifCondition(predicate) {
    return createHigherOrderComponent(
      (WrappedComponent) => (props) => {
        if (!predicate(props)) {
          return null;
        }
        return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(WrappedComponent, { ...props });
      },
      "ifCondition"
    );
  }
  var if_condition_default = ifCondition;

  // packages/compose/build-module/higher-order/pure/index.js
  var import_is_shallow_equal = __toESM(require_is_shallow_equal());
  var import_element = __toESM(require_element());
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  var pure = createHigherOrderComponent(function(WrappedComponent) {
    if (WrappedComponent.prototype instanceof import_element.Component) {
      return class extends WrappedComponent {
        shouldComponentUpdate(nextProps, nextState) {
          return !(0, import_is_shallow_equal.default)(nextProps, this.props) || !(0, import_is_shallow_equal.default)(nextState, this.state);
        }
      };
    }
    return class extends import_element.Component {
      shouldComponentUpdate(nextProps) {
        return !(0, import_is_shallow_equal.default)(nextProps, this.props);
      }
      render() {
        return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(WrappedComponent, { ...this.props });
      }
    };
  }, "pure");
  var pure_default = pure;

  // packages/compose/build-module/higher-order/with-global-events/index.js
  var import_element2 = __toESM(require_element());
  var import_deprecated = __toESM(require_deprecated());

  // packages/compose/build-module/higher-order/with-global-events/listener.js
  var Listener = class {
    constructor() {
      this.listeners = {};
      this.handleEvent = this.handleEvent.bind(this);
    }
    add(eventType, instance) {
      if (!this.listeners[eventType]) {
        window.addEventListener(eventType, this.handleEvent);
        this.listeners[eventType] = [];
      }
      this.listeners[eventType].push(instance);
    }
    remove(eventType, instance) {
      if (!this.listeners[eventType]) {
        return;
      }
      this.listeners[eventType] = this.listeners[eventType].filter(
        (listener2) => listener2 !== instance
      );
      if (!this.listeners[eventType].length) {
        window.removeEventListener(eventType, this.handleEvent);
        delete this.listeners[eventType];
      }
    }
    handleEvent(event) {
      this.listeners[event.type]?.forEach(
        (instance) => {
          instance.handleEvent(event);
        }
      );
    }
  };
  var listener_default = Listener;

  // packages/compose/build-module/higher-order/with-global-events/index.js
  var import_jsx_runtime3 = __toESM(require_jsx_runtime());
  var listener = new listener_default();
  function withGlobalEvents(eventTypesToHandlers) {
    (0, import_deprecated.default)("wp.compose.withGlobalEvents", {
      since: "5.7",
      alternative: "useEffect"
    });
    return createHigherOrderComponent((WrappedComponent) => {
      class Wrapper extends import_element2.Component {
        constructor(props) {
          super(props);
          this.handleEvent = this.handleEvent.bind(this);
          this.handleRef = this.handleRef.bind(this);
        }
        componentDidMount() {
          Object.keys(eventTypesToHandlers).forEach((eventType) => {
            listener.add(eventType, this);
          });
        }
        componentWillUnmount() {
          Object.keys(eventTypesToHandlers).forEach((eventType) => {
            listener.remove(eventType, this);
          });
        }
        handleEvent(event) {
          const handler = eventTypesToHandlers[
            /** @type {keyof GlobalEventHandlersEventMap} */
            event.type
          ];
          if (typeof this.wrappedRef[handler] === "function") {
            this.wrappedRef[handler](event);
          }
        }
        handleRef(el) {
          this.wrappedRef = el;
          if (this.props.forwardedRef) {
            this.props.forwardedRef(el);
          }
        }
        render() {
          return /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
            WrappedComponent,
            {
              ...this.props.ownProps,
              ref: this.handleRef
            }
          );
        }
      }
      return (0, import_element2.forwardRef)((props, ref) => {
        return /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(Wrapper, { ownProps: props, forwardedRef: ref });
      });
    }, "withGlobalEvents");
  }

  // packages/compose/build-module/hooks/use-instance-id/index.js
  var import_element3 = __toESM(require_element());
  var instanceMap = /* @__PURE__ */ new WeakMap();
  function createId(object) {
    const instances = instanceMap.get(object) || 0;
    instanceMap.set(object, instances + 1);
    return instances;
  }
  function useInstanceId(object, prefix, preferredId) {
    return (0, import_element3.useMemo)(() => {
      if (preferredId) {
        return preferredId;
      }
      const id = createId(object);
      return prefix ? `${prefix}-${id}` : id;
    }, [object, preferredId, prefix]);
  }
  var use_instance_id_default = useInstanceId;

  // packages/compose/build-module/higher-order/with-instance-id/index.js
  var import_jsx_runtime4 = __toESM(require_jsx_runtime());
  var withInstanceId = createHigherOrderComponent(
    (WrappedComponent) => {
      return (props) => {
        const instanceId = use_instance_id_default(WrappedComponent);
        return /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(WrappedComponent, { ...props, instanceId });
      };
    },
    "instanceId"
  );
  var with_instance_id_default = withInstanceId;

  // packages/compose/build-module/higher-order/with-safe-timeout/index.js
  var import_element4 = __toESM(require_element());
  var import_jsx_runtime5 = __toESM(require_jsx_runtime());
  var withSafeTimeout = createHigherOrderComponent(
    (OriginalComponent) => {
      return class WrappedComponent extends import_element4.Component {
        timeouts;
        constructor(props) {
          super(props);
          this.timeouts = [];
          this.setTimeout = this.setTimeout.bind(this);
          this.clearTimeout = this.clearTimeout.bind(this);
        }
        componentWillUnmount() {
          this.timeouts.forEach(clearTimeout);
        }
        setTimeout(fn, delay) {
          const id = setTimeout(() => {
            fn();
            this.clearTimeout(id);
          }, delay);
          this.timeouts.push(id);
          return id;
        }
        clearTimeout(id) {
          clearTimeout(id);
          this.timeouts = this.timeouts.filter(
            (timeoutId) => timeoutId !== id
          );
        }
        render() {
          return (
            // @ts-ignore
            /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
              OriginalComponent,
              {
                ...this.props,
                setTimeout: this.setTimeout,
                clearTimeout: this.clearTimeout
              }
            )
          );
        }
      };
    },
    "withSafeTimeout"
  );
  var with_safe_timeout_default = withSafeTimeout;

  // packages/compose/build-module/higher-order/with-state/index.js
  var import_element5 = __toESM(require_element());
  var import_deprecated2 = __toESM(require_deprecated());
  var import_jsx_runtime6 = __toESM(require_jsx_runtime());
  function withState(initialState = {}) {
    (0, import_deprecated2.default)("wp.compose.withState", {
      since: "5.8",
      alternative: "wp.element.useState"
    });
    return createHigherOrderComponent((OriginalComponent) => {
      return class WrappedComponent extends import_element5.Component {
        constructor(props) {
          super(props);
          this.setState = this.setState.bind(this);
          this.state = initialState;
        }
        render() {
          return /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
            OriginalComponent,
            {
              ...this.props,
              ...this.state,
              setState: this.setState
            }
          );
        }
      };
    }, "withState");
  }

  // packages/compose/build-module/hooks/use-constrained-tabbing/index.js
  var import_dom = __toESM(require_dom());

  // packages/compose/build-module/hooks/use-ref-effect/index.js
  var import_element6 = __toESM(require_element());
  function useRefEffect(callback, dependencies) {
    const cleanupRef = (0, import_element6.useRef)();
    return (0, import_element6.useCallback)((node) => {
      if (node) {
        cleanupRef.current = callback(node);
      } else if (cleanupRef.current) {
        cleanupRef.current();
      }
    }, dependencies);
  }

  // packages/compose/build-module/hooks/use-constrained-tabbing/index.js
  function useConstrainedTabbing() {
    return useRefEffect((node) => {
      function onKeyDown(event) {
        const { key, shiftKey, target } = event;
        if (key !== "Tab") {
          return;
        }
        const action = shiftKey ? "findPrevious" : "findNext";
        const nextElement = import_dom.focus.tabbable[action](
          /** @type {HTMLElement} */
          target
        ) || null;
        if (
          /** @type {HTMLElement} */
          target.contains(nextElement)
        ) {
          event.preventDefault();
          nextElement?.focus();
          return;
        }
        if (node.contains(nextElement)) {
          return;
        }
        const domAction = shiftKey ? "append" : "prepend";
        const { ownerDocument } = node;
        const trap = ownerDocument.createElement("div");
        trap.tabIndex = -1;
        node[domAction](trap);
        trap.addEventListener("blur", () => node.removeChild(trap));
        trap.focus();
      }
      node.addEventListener("keydown", onKeyDown);
      return () => {
        node.removeEventListener("keydown", onKeyDown);
      };
    }, []);
  }
  var use_constrained_tabbing_default = useConstrainedTabbing;

  // packages/compose/build-module/hooks/use-copy-on-click/index.js
  var import_clipboard = __toESM(require_clipboard());
  var import_element7 = __toESM(require_element());
  var import_deprecated3 = __toESM(require_deprecated());
  function useCopyOnClick(ref, text, timeout = 4e3) {
    (0, import_deprecated3.default)("wp.compose.useCopyOnClick", {
      since: "5.8",
      alternative: "wp.compose.useCopyToClipboard"
    });
    const clipboardRef = (0, import_element7.useRef)();
    const [hasCopied, setHasCopied] = (0, import_element7.useState)(false);
    (0, import_element7.useEffect)(() => {
      let timeoutId;
      if (!ref.current) {
        return;
      }
      clipboardRef.current = new import_clipboard.default(ref.current, {
        text: () => typeof text === "function" ? text() : text
      });
      clipboardRef.current.on("success", ({ clearSelection, trigger }) => {
        clearSelection();
        if (trigger) {
          trigger.focus();
        }
        if (timeout) {
          setHasCopied(true);
          clearTimeout(timeoutId);
          timeoutId = setTimeout(() => setHasCopied(false), timeout);
        }
      });
      return () => {
        if (clipboardRef.current) {
          clipboardRef.current.destroy();
        }
        clearTimeout(timeoutId);
      };
    }, [text, timeout, setHasCopied]);
    return hasCopied;
  }

  // packages/compose/build-module/hooks/use-copy-to-clipboard/index.js
  var import_clipboard2 = __toESM(require_clipboard());
  var import_element8 = __toESM(require_element());
  function useUpdatedRef(value) {
    const ref = (0, import_element8.useRef)(value);
    (0, import_element8.useLayoutEffect)(() => {
      ref.current = value;
    }, [value]);
    return ref;
  }
  function useCopyToClipboard(text, onSuccess) {
    const textRef = useUpdatedRef(text);
    const onSuccessRef = useUpdatedRef(onSuccess);
    return useRefEffect((node) => {
      const clipboard = new import_clipboard2.default(node, {
        text() {
          return typeof textRef.current === "function" ? textRef.current() : textRef.current || "";
        }
      });
      clipboard.on("success", ({ clearSelection }) => {
        clearSelection();
        if (onSuccessRef.current) {
          onSuccessRef.current();
        }
      });
      return () => {
        clipboard.destroy();
      };
    }, []);
  }

  // packages/compose/build-module/hooks/use-dialog/index.js
  var import_element13 = __toESM(require_element());
  var import_keycodes = __toESM(require_keycodes());

  // packages/compose/build-module/hooks/use-focus-on-mount/index.js
  var import_element9 = __toESM(require_element());
  var import_dom2 = __toESM(require_dom());
  function useFocusOnMount(focusOnMount = "firstElement") {
    const focusOnMountRef = (0, import_element9.useRef)(focusOnMount);
    const setFocus = (target) => {
      target.focus({
        // When focusing newly mounted dialogs,
        // the position of the popover is often not right on the first render
        // This prevents the layout shifts when focusing the dialogs.
        preventScroll: true
      });
    };
    const timerIdRef = (0, import_element9.useRef)();
    (0, import_element9.useEffect)(() => {
      focusOnMountRef.current = focusOnMount;
    }, [focusOnMount]);
    return useRefEffect((node) => {
      if (!node || focusOnMountRef.current === false) {
        return;
      }
      if (node.contains(node.ownerDocument?.activeElement ?? null)) {
        return;
      }
      if (focusOnMountRef.current !== "firstElement") {
        setFocus(node);
        return;
      }
      timerIdRef.current = setTimeout(() => {
        const firstTabbable = import_dom2.focus.tabbable.find(node)[0];
        if (firstTabbable) {
          setFocus(firstTabbable);
        }
      }, 0);
      return () => {
        if (timerIdRef.current) {
          clearTimeout(timerIdRef.current);
        }
      };
    }, []);
  }

  // packages/compose/build-module/hooks/use-focus-return/index.js
  var import_element10 = __toESM(require_element());
  var origin = null;
  function useFocusReturn(onFocusReturn) {
    const ref = (0, import_element10.useRef)(null);
    const focusedBeforeMount = (0, import_element10.useRef)(null);
    const onFocusReturnRef = (0, import_element10.useRef)(onFocusReturn);
    (0, import_element10.useEffect)(() => {
      onFocusReturnRef.current = onFocusReturn;
    }, [onFocusReturn]);
    return (0, import_element10.useCallback)((node) => {
      if (node) {
        ref.current = node;
        if (focusedBeforeMount.current) {
          return;
        }
        const activeDocument = node.ownerDocument.activeElement instanceof window.HTMLIFrameElement ? node.ownerDocument.activeElement.contentDocument : node.ownerDocument;
        focusedBeforeMount.current = activeDocument?.activeElement ?? null;
      } else if (focusedBeforeMount.current) {
        const isFocused = ref.current?.contains(
          ref.current?.ownerDocument.activeElement
        );
        if (ref.current?.isConnected && !isFocused) {
          origin ??= focusedBeforeMount.current;
          return;
        }
        if (onFocusReturnRef.current) {
          onFocusReturnRef.current();
        } else {
          (!focusedBeforeMount.current.isConnected ? origin : focusedBeforeMount.current)?.focus();
        }
        origin = null;
      }
    }, []);
  }
  var use_focus_return_default = useFocusReturn;

  // packages/compose/build-module/hooks/use-focus-outside/index.js
  var import_element11 = __toESM(require_element());
  var INPUT_BUTTON_TYPES = ["button", "submit"];
  function isFocusNormalizedButton(eventTarget) {
    if (!(eventTarget instanceof window.HTMLElement)) {
      return false;
    }
    switch (eventTarget.nodeName) {
      case "A":
      case "BUTTON":
        return true;
      case "INPUT":
        return INPUT_BUTTON_TYPES.includes(
          eventTarget.type
        );
    }
    return false;
  }
  function useFocusOutside(onFocusOutside) {
    const currentOnFocusOutsideRef = (0, import_element11.useRef)(onFocusOutside);
    (0, import_element11.useEffect)(() => {
      currentOnFocusOutsideRef.current = onFocusOutside;
    }, [onFocusOutside]);
    const preventBlurCheckRef = (0, import_element11.useRef)(false);
    const blurCheckTimeoutIdRef = (0, import_element11.useRef)();
    const cancelBlurCheck = (0, import_element11.useCallback)(() => {
      clearTimeout(blurCheckTimeoutIdRef.current);
    }, []);
    (0, import_element11.useEffect)(() => {
      if (!onFocusOutside) {
        cancelBlurCheck();
      }
    }, [onFocusOutside, cancelBlurCheck]);
    const normalizeButtonFocus = (0, import_element11.useCallback)((event) => {
      const { type, target } = event;
      const isInteractionEnd = ["mouseup", "touchend"].includes(type);
      if (isInteractionEnd) {
        preventBlurCheckRef.current = false;
      } else if (isFocusNormalizedButton(target)) {
        preventBlurCheckRef.current = true;
      }
    }, []);
    const queueBlurCheck = (0, import_element11.useCallback)((event) => {
      event.persist();
      if (preventBlurCheckRef.current) {
        return;
      }
      const ignoreForRelatedTarget = event.target.getAttribute(
        "data-unstable-ignore-focus-outside-for-relatedtarget"
      );
      if (ignoreForRelatedTarget && event.relatedTarget?.closest(ignoreForRelatedTarget)) {
        return;
      }
      blurCheckTimeoutIdRef.current = setTimeout(() => {
        if (!document.hasFocus()) {
          event.preventDefault();
          return;
        }
        if ("function" === typeof currentOnFocusOutsideRef.current) {
          currentOnFocusOutsideRef.current(event);
        }
      }, 0);
    }, []);
    return {
      onFocus: cancelBlurCheck,
      onMouseDown: normalizeButtonFocus,
      onMouseUp: normalizeButtonFocus,
      onTouchStart: normalizeButtonFocus,
      onTouchEnd: normalizeButtonFocus,
      onBlur: queueBlurCheck
    };
  }

  // packages/compose/build-module/hooks/use-merge-refs/index.js
  var import_element12 = __toESM(require_element());
  function assignRef(ref, value) {
    if (typeof ref === "function") {
      ref(value);
    } else if (ref && ref.hasOwnProperty("current")) {
      ref.current = value;
    }
  }
  function useMergeRefs(refs) {
    const element = (0, import_element12.useRef)();
    const isAttachedRef = (0, import_element12.useRef)(false);
    const didElementChangeRef = (0, import_element12.useRef)(false);
    const previousRefsRef = (0, import_element12.useRef)([]);
    const currentRefsRef = (0, import_element12.useRef)(refs);
    currentRefsRef.current = refs;
    (0, import_element12.useLayoutEffect)(() => {
      if (didElementChangeRef.current === false && isAttachedRef.current === true) {
        refs.forEach((ref, index) => {
          const previousRef = previousRefsRef.current[index];
          if (ref !== previousRef) {
            assignRef(previousRef, null);
            assignRef(ref, element.current);
          }
        });
      }
      previousRefsRef.current = refs;
    }, refs);
    (0, import_element12.useLayoutEffect)(() => {
      didElementChangeRef.current = false;
    });
    return (0, import_element12.useCallback)((value) => {
      assignRef(element, value);
      didElementChangeRef.current = true;
      isAttachedRef.current = value !== null;
      const refsToAssign = value ? currentRefsRef.current : previousRefsRef.current;
      for (const ref of refsToAssign) {
        assignRef(ref, value);
      }
    }, []);
  }

  // packages/compose/build-module/hooks/use-dialog/index.js
  function useDialog(options) {
    const currentOptions = (0, import_element13.useRef)();
    const { constrainTabbing = options.focusOnMount !== false } = options;
    (0, import_element13.useEffect)(() => {
      currentOptions.current = options;
    }, Object.values(options));
    const constrainedTabbingRef = use_constrained_tabbing_default();
    const focusOnMountRef = useFocusOnMount(options.focusOnMount);
    const focusReturnRef = use_focus_return_default();
    const focusOutsideProps = useFocusOutside((event) => {
      if (currentOptions.current?.__unstableOnClose) {
        currentOptions.current.__unstableOnClose("focus-outside", event);
      } else if (currentOptions.current?.onClose) {
        currentOptions.current.onClose();
      }
    });
    const closeOnEscapeRef = (0, import_element13.useCallback)((node) => {
      if (!node) {
        return;
      }
      node.addEventListener("keydown", (event) => {
        if (event.keyCode === import_keycodes.ESCAPE && !event.defaultPrevented && currentOptions.current?.onClose) {
          event.preventDefault();
          currentOptions.current.onClose();
        }
      });
    }, []);
    return [
      useMergeRefs([
        constrainTabbing ? constrainedTabbingRef : null,
        options.focusOnMount !== false ? focusReturnRef : null,
        options.focusOnMount !== false ? focusOnMountRef : null,
        closeOnEscapeRef
      ]),
      {
        ...focusOutsideProps,
        tabIndex: -1
      }
    ];
  }
  var use_dialog_default = useDialog;

  // packages/compose/build-module/hooks/use-disabled/index.js
  function useDisabled({
    isDisabled: isDisabledProp = false
  } = {}) {
    return useRefEffect(
      (node) => {
        if (isDisabledProp) {
          return;
        }
        const defaultView = node?.ownerDocument?.defaultView;
        if (!defaultView) {
          return;
        }
        const updates = [];
        const disable = () => {
          node.childNodes.forEach((child) => {
            if (!(child instanceof defaultView.HTMLElement)) {
              return;
            }
            if (!child.getAttribute("inert")) {
              child.setAttribute("inert", "true");
              updates.push(() => {
                child.removeAttribute("inert");
              });
            }
          });
        };
        const debouncedDisable = debounce(disable, 0, {
          leading: true
        });
        disable();
        const observer = new window.MutationObserver(debouncedDisable);
        observer.observe(node, {
          childList: true
        });
        return () => {
          if (observer) {
            observer.disconnect();
          }
          debouncedDisable.cancel();
          updates.forEach((update) => update());
        };
      },
      [isDisabledProp]
    );
  }

  // packages/compose/build-module/hooks/use-event/index.js
  var import_element14 = __toESM(require_element());
  function useEvent(callback) {
    const ref = (0, import_element14.useRef)(() => {
      throw new Error(
        "Callbacks created with `useEvent` cannot be called during rendering."
      );
    });
    (0, import_element14.useInsertionEffect)(() => {
      ref.current = callback;
    });
    return (0, import_element14.useCallback)(
      (...args) => ref.current?.(...args),
      []
    );
  }

  // packages/compose/build-module/hooks/use-dragging/index.js
  var import_element16 = __toESM(require_element());

  // packages/compose/build-module/hooks/use-isomorphic-layout-effect/index.js
  var import_element15 = __toESM(require_element());
  var useIsomorphicLayoutEffect = typeof window !== "undefined" ? import_element15.useLayoutEffect : import_element15.useEffect;
  var use_isomorphic_layout_effect_default = useIsomorphicLayoutEffect;

  // packages/compose/build-module/hooks/use-dragging/index.js
  function useDragging({ onDragStart, onDragMove, onDragEnd }) {
    const [isDragging, setIsDragging] = (0, import_element16.useState)(false);
    const eventsRef = (0, import_element16.useRef)({
      onDragStart,
      onDragMove,
      onDragEnd
    });
    use_isomorphic_layout_effect_default(() => {
      eventsRef.current.onDragStart = onDragStart;
      eventsRef.current.onDragMove = onDragMove;
      eventsRef.current.onDragEnd = onDragEnd;
    }, [onDragStart, onDragMove, onDragEnd]);
    const onMouseMove = (0, import_element16.useCallback)(
      (event) => eventsRef.current.onDragMove && eventsRef.current.onDragMove(event),
      []
    );
    const endDrag = (0, import_element16.useCallback)((event) => {
      if (eventsRef.current.onDragEnd) {
        eventsRef.current.onDragEnd(event);
      }
      document.removeEventListener("mousemove", onMouseMove);
      document.removeEventListener("mouseup", endDrag);
      setIsDragging(false);
    }, []);
    const startDrag = (0, import_element16.useCallback)((event) => {
      if (eventsRef.current.onDragStart) {
        eventsRef.current.onDragStart(event);
      }
      document.addEventListener("mousemove", onMouseMove);
      document.addEventListener("mouseup", endDrag);
      setIsDragging(true);
    }, []);
    (0, import_element16.useEffect)(() => {
      return () => {
        if (isDragging) {
          document.removeEventListener("mousemove", onMouseMove);
          document.removeEventListener("mouseup", endDrag);
        }
      };
    }, [isDragging]);
    return {
      startDrag,
      endDrag,
      isDragging
    };
  }

  // packages/compose/build-module/hooks/use-keyboard-shortcut/index.js
  var import_mousetrap = __toESM(require_mousetrap());

  // node_modules/mousetrap/plugins/global-bind/mousetrap-global-bind.js
  (function(Mousetrap3) {
    if (!Mousetrap3) {
      return;
    }
    var _globalCallbacks = {};
    var _originalStopCallback = Mousetrap3.prototype.stopCallback;
    Mousetrap3.prototype.stopCallback = function(e, element, combo, sequence) {
      var self = this;
      if (self.paused) {
        return true;
      }
      if (_globalCallbacks[combo] || _globalCallbacks[sequence]) {
        return false;
      }
      return _originalStopCallback.call(self, e, element, combo);
    };
    Mousetrap3.prototype.bindGlobal = function(keys, callback, action) {
      var self = this;
      self.bind(keys, callback, action);
      if (keys instanceof Array) {
        for (var i = 0; i < keys.length; i++) {
          _globalCallbacks[keys[i]] = true;
        }
        return;
      }
      _globalCallbacks[keys] = true;
    };
    Mousetrap3.init();
  })(typeof Mousetrap !== "undefined" ? Mousetrap : void 0);

  // packages/compose/build-module/hooks/use-keyboard-shortcut/index.js
  var import_element17 = __toESM(require_element());
  var import_keycodes2 = __toESM(require_keycodes());
  function useKeyboardShortcut(shortcuts, callback, {
    bindGlobal = false,
    eventName = "keydown",
    isDisabled = false,
    // This is important for performance considerations.
    target
  } = {}) {
    const currentCallbackRef = (0, import_element17.useRef)(callback);
    (0, import_element17.useEffect)(() => {
      currentCallbackRef.current = callback;
    }, [callback]);
    (0, import_element17.useEffect)(() => {
      if (isDisabled) {
        return;
      }
      const mousetrap = new import_mousetrap.default(
        target && target.current ? target.current : (
          // We were passing `document` here previously, so to successfully cast it to Element we must cast it first to `unknown`.
          // Not sure if this is a mistake but it was the behavior previous to the addition of types so we're just doing what's
          // necessary to maintain the existing behavior.
          /** @type {Element} */
          /** @type {unknown} */
          document
        )
      );
      const shortcutsArray = Array.isArray(shortcuts) ? shortcuts : [shortcuts];
      shortcutsArray.forEach((shortcut) => {
        const keys = shortcut.split("+");
        const modifiers = new Set(
          keys.filter((value) => value.length > 1)
        );
        const hasAlt = modifiers.has("alt");
        const hasShift = modifiers.has("shift");
        if ((0, import_keycodes2.isAppleOS)() && (modifiers.size === 1 && hasAlt || modifiers.size === 2 && hasAlt && hasShift)) {
          throw new Error(
            `Cannot bind ${shortcut}. Alt and Shift+Alt modifiers are reserved for character input.`
          );
        }
        const bindFn = bindGlobal ? "bindGlobal" : "bind";
        mousetrap[bindFn](
          shortcut,
          (...args) => currentCallbackRef.current(...args),
          eventName
        );
      });
      return () => {
        mousetrap.reset();
      };
    }, [shortcuts, bindGlobal, eventName, target, isDisabled]);
  }
  var use_keyboard_shortcut_default = useKeyboardShortcut;

  // packages/compose/build-module/hooks/use-media-query/index.js
  var import_element18 = __toESM(require_element());
  var matchMediaCache = /* @__PURE__ */ new Map();
  function getMediaQueryList(query) {
    if (!query) {
      return null;
    }
    let match = matchMediaCache.get(query);
    if (match) {
      return match;
    }
    if (typeof window !== "undefined" && typeof window.matchMedia === "function") {
      match = window.matchMedia(query);
      matchMediaCache.set(query, match);
      return match;
    }
    return null;
  }
  function useMediaQuery(query) {
    const source = (0, import_element18.useMemo)(() => {
      const mediaQueryList = getMediaQueryList(query);
      return {
        /** @type {(onStoreChange: () => void) => () => void} */
        subscribe(onStoreChange) {
          if (!mediaQueryList) {
            return () => {
            };
          }
          mediaQueryList.addEventListener?.("change", onStoreChange);
          return () => {
            mediaQueryList.removeEventListener?.(
              "change",
              onStoreChange
            );
          };
        },
        getValue() {
          return mediaQueryList?.matches ?? false;
        }
      };
    }, [query]);
    return (0, import_element18.useSyncExternalStore)(
      source.subscribe,
      source.getValue,
      () => false
    );
  }

  // packages/compose/build-module/hooks/use-previous/index.js
  var import_element19 = __toESM(require_element());
  function usePrevious(value) {
    const ref = (0, import_element19.useRef)();
    (0, import_element19.useEffect)(() => {
      ref.current = value;
    }, [value]);
    return ref.current;
  }

  // packages/compose/build-module/hooks/use-reduced-motion/index.js
  var useReducedMotion = () => useMediaQuery("(prefers-reduced-motion: reduce)");
  var use_reduced_motion_default = useReducedMotion;

  // packages/compose/build-module/hooks/use-state-with-history/index.js
  var import_undo_manager = __toESM(require_undo_manager());
  var import_element20 = __toESM(require_element());
  function undoRedoReducer(state, action) {
    switch (action.type) {
      case "UNDO": {
        const undoRecord = state.manager.undo();
        if (undoRecord) {
          return {
            ...state,
            value: undoRecord[0].changes.prop.from
          };
        }
        return state;
      }
      case "REDO": {
        const redoRecord = state.manager.redo();
        if (redoRecord) {
          return {
            ...state,
            value: redoRecord[0].changes.prop.to
          };
        }
        return state;
      }
      case "RECORD": {
        state.manager.addRecord(
          [
            {
              id: "object",
              changes: {
                prop: { from: state.value, to: action.value }
              }
            }
          ],
          action.isStaged
        );
        return {
          ...state,
          value: action.value
        };
      }
    }
    return state;
  }
  function initReducer(value) {
    return {
      manager: (0, import_undo_manager.createUndoManager)(),
      value
    };
  }
  function useStateWithHistory(initialValue) {
    const [state, dispatch] = (0, import_element20.useReducer)(
      undoRedoReducer,
      initialValue,
      initReducer
    );
    return {
      value: state.value,
      setValue: (0, import_element20.useCallback)((newValue, isStaged) => {
        dispatch({
          type: "RECORD",
          value: newValue,
          isStaged
        });
      }, []),
      hasUndo: state.manager.hasUndo(),
      hasRedo: state.manager.hasRedo(),
      undo: (0, import_element20.useCallback)(() => {
        dispatch({ type: "UNDO" });
      }, []),
      redo: (0, import_element20.useCallback)(() => {
        dispatch({ type: "REDO" });
      }, [])
    };
  }

  // packages/compose/build-module/hooks/use-viewport-match/index.js
  var import_element21 = __toESM(require_element());
  var BREAKPOINTS = {
    xhuge: 1920,
    huge: 1440,
    wide: 1280,
    xlarge: 1080,
    large: 960,
    medium: 782,
    small: 600,
    mobile: 480
  };
  var CONDITIONS = {
    ">=": "min-width",
    "<": "max-width"
  };
  var OPERATOR_EVALUATORS = {
    ">=": (breakpointValue, width) => width >= breakpointValue,
    "<": (breakpointValue, width) => width < breakpointValue
  };
  var ViewportMatchWidthContext = (0, import_element21.createContext)(
    /** @type {null | number} */
    null
  );
  ViewportMatchWidthContext.displayName = "ViewportMatchWidthContext";
  var useViewportMatch = (breakpoint, operator = ">=") => {
    const simulatedWidth = (0, import_element21.useContext)(ViewportMatchWidthContext);
    const mediaQuery = !simulatedWidth && `(${CONDITIONS[operator]}: ${BREAKPOINTS[breakpoint]}px)`;
    const mediaQueryResult = useMediaQuery(mediaQuery || void 0);
    if (simulatedWidth) {
      return OPERATOR_EVALUATORS[operator](
        BREAKPOINTS[breakpoint],
        simulatedWidth
      );
    }
    return mediaQueryResult;
  };
  useViewportMatch.__experimentalWidthProvider = ViewportMatchWidthContext.Provider;
  var use_viewport_match_default = useViewportMatch;

  // packages/compose/build-module/hooks/use-resize-observer/use-resize-observer.js
  var import_element22 = __toESM(require_element());
  function useResizeObserver(callback, resizeObserverOptions = {}) {
    const callbackEvent = useEvent(callback);
    const observedElementRef = (0, import_element22.useRef)();
    const resizeObserverRef = (0, import_element22.useRef)();
    return useEvent((element) => {
      if (element === observedElementRef.current) {
        return;
      }
      resizeObserverRef.current ??= new ResizeObserver(callbackEvent);
      const { current: resizeObserver } = resizeObserverRef;
      if (observedElementRef.current) {
        resizeObserver.unobserve(observedElementRef.current);
      }
      observedElementRef.current = element;
      if (element) {
        resizeObserver.observe(element, resizeObserverOptions);
      }
    });
  }

  // packages/compose/build-module/hooks/use-resize-observer/legacy/index.js
  var import_element23 = __toESM(require_element());
  var import_jsx_runtime7 = __toESM(require_jsx_runtime());
  var extractSize = (entry) => {
    let entrySize;
    if (!entry.contentBoxSize) {
      entrySize = [entry.contentRect.width, entry.contentRect.height];
    } else if (entry.contentBoxSize[0]) {
      const contentBoxSize = entry.contentBoxSize[0];
      entrySize = [contentBoxSize.inlineSize, contentBoxSize.blockSize];
    } else {
      const contentBoxSize = entry.contentBoxSize;
      entrySize = [contentBoxSize.inlineSize, contentBoxSize.blockSize];
    }
    const [width, height] = entrySize.map((d) => Math.round(d));
    return { width, height };
  };
  var RESIZE_ELEMENT_STYLES = {
    position: "absolute",
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    pointerEvents: "none",
    opacity: 0,
    overflow: "hidden",
    zIndex: -1
  };
  function ResizeElement({ onResize }) {
    const resizeElementRef = useResizeObserver((entries) => {
      const newSize = extractSize(entries.at(-1));
      onResize(newSize);
    });
    return /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
      "div",
      {
        ref: resizeElementRef,
        style: RESIZE_ELEMENT_STYLES,
        "aria-hidden": "true"
      }
    );
  }
  function sizeEquals(a, b) {
    return a.width === b.width && a.height === b.height;
  }
  var NULL_SIZE = { width: null, height: null };
  function useLegacyResizeObserver() {
    const [size, setSize] = (0, import_element23.useState)(NULL_SIZE);
    const previousSizeRef = (0, import_element23.useRef)(NULL_SIZE);
    const handleResize = (0, import_element23.useCallback)((newSize) => {
      if (!sizeEquals(previousSizeRef.current, newSize)) {
        previousSizeRef.current = newSize;
        setSize(newSize);
      }
    }, []);
    const resizeElement = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(ResizeElement, { onResize: handleResize });
    return [resizeElement, size];
  }

  // packages/compose/build-module/hooks/use-resize-observer/index.js
  function useResizeObserver2(callback, options = {}) {
    return callback ? useResizeObserver(callback, options) : useLegacyResizeObserver();
  }

  // packages/compose/build-module/hooks/use-async-list/index.js
  var import_element24 = __toESM(require_element());
  var import_priority_queue = __toESM(require_priority_queue());
  function getFirstItemsPresentInState(list, state) {
    const firstItems = [];
    for (let i = 0; i < list.length; i++) {
      const item = list[i];
      if (!state.includes(item)) {
        break;
      }
      firstItems.push(item);
    }
    return firstItems;
  }
  function useAsyncList(list, config = { step: 1 }) {
    const { step = 1 } = config;
    const [current, setCurrent] = (0, import_element24.useState)([]);
    (0, import_element24.useEffect)(() => {
      let firstItems = getFirstItemsPresentInState(list, current);
      if (firstItems.length < step) {
        firstItems = firstItems.concat(
          list.slice(firstItems.length, step)
        );
      }
      setCurrent(firstItems);
      const asyncQueue = (0, import_priority_queue.createQueue)();
      for (let i = firstItems.length; i < list.length; i += step) {
        asyncQueue.add({}, () => {
          (0, import_element24.flushSync)(() => {
            setCurrent((state) => [
              ...state,
              ...list.slice(i, i + step)
            ]);
          });
        });
      }
      return () => asyncQueue.reset();
    }, [list]);
    return current;
  }
  var use_async_list_default = useAsyncList;

  // packages/compose/build-module/hooks/use-warn-on-change/index.js
  function useWarnOnChange(object, prefix = "Change detection") {
    const previousValues = usePrevious(object);
    Object.entries(previousValues ?? []).forEach(([key, value]) => {
      if (value !== object[
        /** @type {keyof typeof object} */
        key
      ]) {
        console.warn(
          `${prefix}: ${key} key changed:`,
          value,
          object[
            /** @type {keyof typeof object} */
            key
          ]
          /* eslint-enable jsdoc/check-types */
        );
      }
    });
  }
  var use_warn_on_change_default = useWarnOnChange;

  // node_modules/use-memo-one/dist/use-memo-one.esm.js
  var import_react = __toESM(require_react());
  function areInputsEqual(newInputs, lastInputs) {
    if (newInputs.length !== lastInputs.length) {
      return false;
    }
    for (var i = 0; i < newInputs.length; i++) {
      if (newInputs[i] !== lastInputs[i]) {
        return false;
      }
    }
    return true;
  }
  function useMemoOne(getResult, inputs) {
    var initial = (0, import_react.useState)(function() {
      return {
        inputs,
        result: getResult()
      };
    })[0];
    var committed = (0, import_react.useRef)(initial);
    var isInputMatch = Boolean(inputs && committed.current.inputs && areInputsEqual(inputs, committed.current.inputs));
    var cache = isInputMatch ? committed.current : {
      inputs,
      result: getResult()
    };
    (0, import_react.useEffect)(function() {
      committed.current = cache;
    }, [cache]);
    return cache.result;
  }

  // packages/compose/build-module/hooks/use-debounce/index.js
  var import_element25 = __toESM(require_element());
  function useDebounce(fn, wait, options) {
    const debounced = useMemoOne(
      () => debounce(fn, wait ?? 0, options),
      [fn, wait, options?.leading, options?.trailing, options?.maxWait]
    );
    (0, import_element25.useEffect)(() => () => debounced.cancel(), [debounced]);
    return debounced;
  }

  // packages/compose/build-module/hooks/use-debounced-input/index.js
  var import_element26 = __toESM(require_element());
  function useDebouncedInput(defaultValue = "") {
    const [input, setInput] = (0, import_element26.useState)(defaultValue);
    const [debouncedInput, setDebouncedState] = (0, import_element26.useState)(defaultValue);
    const setDebouncedInput = useDebounce(setDebouncedState, 250);
    (0, import_element26.useEffect)(() => {
      setDebouncedInput(input);
    }, [input, setDebouncedInput]);
    return [input, setInput, debouncedInput];
  }

  // packages/compose/build-module/hooks/use-throttle/index.js
  var import_element27 = __toESM(require_element());
  function useThrottle(fn, wait, options) {
    const throttled = useMemoOne(
      () => throttle(fn, wait ?? 0, options),
      [fn, wait, options]
    );
    (0, import_element27.useEffect)(() => () => throttled.cancel(), [throttled]);
    return throttled;
  }

  // packages/compose/build-module/hooks/use-drop-zone/index.js
  function useDropZone({
    dropZoneElement,
    isDisabled,
    onDrop: _onDrop,
    onDragStart: _onDragStart,
    onDragEnter: _onDragEnter,
    onDragLeave: _onDragLeave,
    onDragEnd: _onDragEnd,
    onDragOver: _onDragOver
  }) {
    const onDropEvent = useEvent(_onDrop);
    const onDragStartEvent = useEvent(_onDragStart);
    const onDragEnterEvent = useEvent(_onDragEnter);
    const onDragLeaveEvent = useEvent(_onDragLeave);
    const onDragEndEvent = useEvent(_onDragEnd);
    const onDragOverEvent = useEvent(_onDragOver);
    return useRefEffect(
      (elem) => {
        if (isDisabled) {
          return;
        }
        const element = dropZoneElement ?? elem;
        let isDragging = false;
        const { ownerDocument } = element;
        function isElementInZone(targetToCheck) {
          const { defaultView } = ownerDocument;
          if (!targetToCheck || !defaultView || !(targetToCheck instanceof defaultView.HTMLElement) || !element.contains(targetToCheck)) {
            return false;
          }
          let elementToCheck = targetToCheck;
          do {
            if (elementToCheck.dataset.isDropZone) {
              return elementToCheck === element;
            }
          } while (elementToCheck = elementToCheck.parentElement);
          return false;
        }
        function maybeDragStart(event) {
          if (isDragging) {
            return;
          }
          isDragging = true;
          ownerDocument.addEventListener("dragend", maybeDragEnd);
          ownerDocument.addEventListener("mousemove", maybeDragEnd);
          if (_onDragStart) {
            onDragStartEvent(event);
          }
        }
        function onDragEnter(event) {
          event.preventDefault();
          if (element.contains(
            /** @type {Node} */
            event.relatedTarget
          )) {
            return;
          }
          if (_onDragEnter) {
            onDragEnterEvent(event);
          }
        }
        function onDragOver(event) {
          if (!event.defaultPrevented && _onDragOver) {
            onDragOverEvent(event);
          }
          event.preventDefault();
        }
        function onDragLeave(event) {
          if (isElementInZone(event.relatedTarget)) {
            return;
          }
          if (_onDragLeave) {
            onDragLeaveEvent(event);
          }
        }
        function onDrop(event) {
          if (event.defaultPrevented) {
            return;
          }
          event.preventDefault();
          event.dataTransfer && event.dataTransfer.files.length;
          if (_onDrop) {
            onDropEvent(event);
          }
          maybeDragEnd(event);
        }
        function maybeDragEnd(event) {
          if (!isDragging) {
            return;
          }
          isDragging = false;
          ownerDocument.removeEventListener("dragend", maybeDragEnd);
          ownerDocument.removeEventListener("mousemove", maybeDragEnd);
          if (_onDragEnd) {
            onDragEndEvent(event);
          }
        }
        element.setAttribute("data-is-drop-zone", "true");
        element.addEventListener("drop", onDrop);
        element.addEventListener("dragenter", onDragEnter);
        element.addEventListener("dragover", onDragOver);
        element.addEventListener("dragleave", onDragLeave);
        ownerDocument.addEventListener("dragenter", maybeDragStart);
        return () => {
          element.removeAttribute("data-is-drop-zone");
          element.removeEventListener("drop", onDrop);
          element.removeEventListener("dragenter", onDragEnter);
          element.removeEventListener("dragover", onDragOver);
          element.removeEventListener("dragleave", onDragLeave);
          ownerDocument.removeEventListener("dragend", maybeDragEnd);
          ownerDocument.removeEventListener("mousemove", maybeDragEnd);
          ownerDocument.removeEventListener(
            "dragenter",
            maybeDragStart
          );
        };
      },
      [isDisabled, dropZoneElement]
      // Refresh when the passed in dropZoneElement changes.
    );
  }

  // packages/compose/build-module/hooks/use-focusable-iframe/index.js
  function useFocusableIframe() {
    return useRefEffect((element) => {
      const { ownerDocument } = element;
      if (!ownerDocument) {
        return;
      }
      const { defaultView } = ownerDocument;
      if (!defaultView) {
        return;
      }
      function checkFocus() {
        if (ownerDocument && ownerDocument.activeElement === element) {
          element.focus();
        }
      }
      defaultView.addEventListener("blur", checkFocus);
      return () => {
        defaultView.removeEventListener("blur", checkFocus);
      };
    }, []);
  }

  // packages/compose/build-module/hooks/use-fixed-window-list/index.js
  var import_element28 = __toESM(require_element());
  var import_dom3 = __toESM(require_dom());
  var import_keycodes3 = __toESM(require_keycodes());
  var DEFAULT_INIT_WINDOW_SIZE = 30;
  function useFixedWindowList(elementRef, itemHeight, totalItems, options) {
    const initWindowSize = options?.initWindowSize ?? DEFAULT_INIT_WINDOW_SIZE;
    const useWindowing = options?.useWindowing ?? true;
    const [fixedListWindow, setFixedListWindow] = (0, import_element28.useState)({
      visibleItems: initWindowSize,
      start: 0,
      end: initWindowSize,
      itemInView: (index) => {
        return index >= 0 && index <= initWindowSize;
      }
    });
    (0, import_element28.useLayoutEffect)(() => {
      if (!useWindowing) {
        return;
      }
      const scrollContainer = (0, import_dom3.getScrollContainer)(elementRef.current);
      const measureWindow = (initRender) => {
        if (!scrollContainer) {
          return;
        }
        const visibleItems = Math.ceil(
          scrollContainer.clientHeight / itemHeight
        );
        const windowOverscan = initRender ? visibleItems : options?.windowOverscan ?? visibleItems;
        const firstViewableIndex = Math.floor(
          scrollContainer.scrollTop / itemHeight
        );
        const start = Math.max(0, firstViewableIndex - windowOverscan);
        const end = Math.min(
          totalItems - 1,
          firstViewableIndex + visibleItems + windowOverscan
        );
        setFixedListWindow((lastWindow) => {
          const nextWindow = {
            visibleItems,
            start,
            end,
            itemInView: (index) => {
              return start <= index && index <= end;
            }
          };
          if (lastWindow.start !== nextWindow.start || lastWindow.end !== nextWindow.end || lastWindow.visibleItems !== nextWindow.visibleItems) {
            return nextWindow;
          }
          return lastWindow;
        });
      };
      measureWindow(true);
      const debounceMeasureList = debounce(() => {
        measureWindow();
      }, 16);
      scrollContainer?.addEventListener("scroll", debounceMeasureList);
      scrollContainer?.ownerDocument?.defaultView?.addEventListener(
        "resize",
        debounceMeasureList
      );
      scrollContainer?.ownerDocument?.defaultView?.addEventListener(
        "resize",
        debounceMeasureList
      );
      return () => {
        scrollContainer?.removeEventListener(
          "scroll",
          debounceMeasureList
        );
        scrollContainer?.ownerDocument?.defaultView?.removeEventListener(
          "resize",
          debounceMeasureList
        );
      };
    }, [
      itemHeight,
      elementRef,
      totalItems,
      options?.expandedState,
      options?.windowOverscan,
      useWindowing
    ]);
    (0, import_element28.useLayoutEffect)(() => {
      if (!useWindowing) {
        return;
      }
      const scrollContainer = (0, import_dom3.getScrollContainer)(elementRef.current);
      const handleKeyDown = (event) => {
        switch (event.keyCode) {
          case import_keycodes3.HOME: {
            return scrollContainer?.scrollTo({ top: 0 });
          }
          case import_keycodes3.END: {
            return scrollContainer?.scrollTo({
              top: totalItems * itemHeight
            });
          }
          case import_keycodes3.PAGEUP: {
            return scrollContainer?.scrollTo({
              top: scrollContainer.scrollTop - fixedListWindow.visibleItems * itemHeight
            });
          }
          case import_keycodes3.PAGEDOWN: {
            return scrollContainer?.scrollTo({
              top: scrollContainer.scrollTop + fixedListWindow.visibleItems * itemHeight
            });
          }
        }
      };
      scrollContainer?.ownerDocument?.defaultView?.addEventListener(
        "keydown",
        handleKeyDown
      );
      return () => {
        scrollContainer?.ownerDocument?.defaultView?.removeEventListener(
          "keydown",
          handleKeyDown
        );
      };
    }, [
      totalItems,
      itemHeight,
      elementRef,
      fixedListWindow.visibleItems,
      useWindowing,
      options?.expandedState
    ]);
    return [fixedListWindow, setFixedListWindow];
  }

  // packages/compose/build-module/hooks/use-observable-value/index.js
  var import_element29 = __toESM(require_element());
  function useObservableValue(map, name) {
    const [subscribe, getValue] = (0, import_element29.useMemo)(
      () => [
        (listener2) => map.subscribe(name, listener2),
        () => map.get(name)
      ],
      [map, name]
    );
    return (0, import_element29.useSyncExternalStore)(subscribe, getValue, getValue);
  }
  return __toCommonJS(index_exports);
})();
/*! Bundled license information:

clipboard/dist/clipboard.js:
  (*!
   * clipboard.js v2.0.11
   * https://clipboardjs.com/
   *
   * Licensed MIT © Zeno Rocha
   *)
*/
//# sourceMappingURL=index.js.map
