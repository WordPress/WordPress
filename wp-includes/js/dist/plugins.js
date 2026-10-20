(function() {
"use strict";
var wp;
(wp ||= {}).plugins = (() => {
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

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/hooks
  var require_hooks = __commonJS({
    "package-external:@wordpress/hooks"(exports, module) {
      module.exports = window.wp.hooks;
    }
  });

  // package-external:@wordpress/is-shallow-equal
  var require_is_shallow_equal = __commonJS({
    "package-external:@wordpress/is-shallow-equal"(exports, module) {
      module.exports = window.wp.isShallowEqual;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/deprecated
  var require_deprecated = __commonJS({
    "package-external:@wordpress/deprecated"(exports, module) {
      module.exports = window.wp.deprecated;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // node_modules/.store/react-is@19.3.0-h9DBeE4BCfrMSfxZZXEDFw/node_modules/react-is/cjs/react-is.development.js
  var require_react_is_development = __commonJS({
    "node_modules/.store/react-is@19.3.0-h9DBeE4BCfrMSfxZZXEDFw/node_modules/react-is/cjs/react-is.development.js"(exports) {
      "use strict";
      (function() {
        function typeOf(object) {
          if ("object" === typeof object && null !== object) {
            var $$typeof = object.$$typeof;
            switch ($$typeof) {
              case REACT_ELEMENT_TYPE:
                switch (object = object.type, object) {
                  case REACT_FRAGMENT_TYPE:
                  case REACT_PROFILER_TYPE:
                  case REACT_STRICT_MODE_TYPE:
                  case REACT_SUSPENSE_TYPE:
                  case REACT_SUSPENSE_LIST_TYPE:
                  case REACT_VIEW_TRANSITION_TYPE:
                    return object;
                  default:
                    switch (object = object && object.$$typeof, object) {
                      case REACT_CONTEXT_TYPE:
                      case REACT_FORWARD_REF_TYPE:
                      case REACT_LAZY_TYPE:
                      case REACT_MEMO_TYPE:
                        return object;
                      case REACT_CONSUMER_TYPE:
                        return object;
                      default:
                        return $$typeof;
                    }
                }
              case REACT_PORTAL_TYPE:
                return $$typeof;
            }
          }
        }
        var REACT_ELEMENT_TYPE = /* @__PURE__ */ Symbol.for("react.transitional.element"), REACT_PORTAL_TYPE = /* @__PURE__ */ Symbol.for("react.portal"), REACT_FRAGMENT_TYPE = /* @__PURE__ */ Symbol.for("react.fragment"), REACT_STRICT_MODE_TYPE = /* @__PURE__ */ Symbol.for("react.strict_mode"), REACT_PROFILER_TYPE = /* @__PURE__ */ Symbol.for("react.profiler"), REACT_CONSUMER_TYPE = /* @__PURE__ */ Symbol.for("react.consumer"), REACT_CONTEXT_TYPE = /* @__PURE__ */ Symbol.for("react.context"), REACT_FORWARD_REF_TYPE = /* @__PURE__ */ Symbol.for("react.forward_ref"), REACT_SUSPENSE_TYPE = /* @__PURE__ */ Symbol.for("react.suspense"), REACT_SUSPENSE_LIST_TYPE = /* @__PURE__ */ Symbol.for("react.suspense_list"), REACT_MEMO_TYPE = /* @__PURE__ */ Symbol.for("react.memo"), REACT_LAZY_TYPE = /* @__PURE__ */ Symbol.for("react.lazy"), REACT_VIEW_TRANSITION_TYPE = /* @__PURE__ */ Symbol.for("react.view_transition"), REACT_CLIENT_REFERENCE = /* @__PURE__ */ Symbol.for("react.client.reference");
        exports.ContextConsumer = REACT_CONSUMER_TYPE;
        exports.ContextProvider = REACT_CONTEXT_TYPE;
        exports.Element = REACT_ELEMENT_TYPE;
        exports.ForwardRef = REACT_FORWARD_REF_TYPE;
        exports.Fragment = REACT_FRAGMENT_TYPE;
        exports.Lazy = REACT_LAZY_TYPE;
        exports.Memo = REACT_MEMO_TYPE;
        exports.Portal = REACT_PORTAL_TYPE;
        exports.Profiler = REACT_PROFILER_TYPE;
        exports.StrictMode = REACT_STRICT_MODE_TYPE;
        exports.Suspense = REACT_SUSPENSE_TYPE;
        exports.SuspenseList = REACT_SUSPENSE_LIST_TYPE;
        exports.isContextConsumer = function(object) {
          return typeOf(object) === REACT_CONSUMER_TYPE;
        };
        exports.isContextProvider = function(object) {
          return typeOf(object) === REACT_CONTEXT_TYPE;
        };
        exports.isElement = function(object) {
          return "object" === typeof object && null !== object && object.$$typeof === REACT_ELEMENT_TYPE;
        };
        exports.isForwardRef = function(object) {
          return typeOf(object) === REACT_FORWARD_REF_TYPE;
        };
        exports.isFragment = function(object) {
          return typeOf(object) === REACT_FRAGMENT_TYPE;
        };
        exports.isLazy = function(object) {
          return typeOf(object) === REACT_LAZY_TYPE;
        };
        exports.isMemo = function(object) {
          return typeOf(object) === REACT_MEMO_TYPE;
        };
        exports.isPortal = function(object) {
          return typeOf(object) === REACT_PORTAL_TYPE;
        };
        exports.isProfiler = function(object) {
          return typeOf(object) === REACT_PROFILER_TYPE;
        };
        exports.isStrictMode = function(object) {
          return typeOf(object) === REACT_STRICT_MODE_TYPE;
        };
        exports.isSuspense = function(object) {
          return typeOf(object) === REACT_SUSPENSE_TYPE;
        };
        exports.isSuspenseList = function(object) {
          return typeOf(object) === REACT_SUSPENSE_LIST_TYPE;
        };
        exports.isValidElementType = function(type) {
          return "string" === typeof type || "function" === typeof type || type === REACT_FRAGMENT_TYPE || type === REACT_PROFILER_TYPE || type === REACT_STRICT_MODE_TYPE || type === REACT_SUSPENSE_TYPE || type === REACT_SUSPENSE_LIST_TYPE || type === REACT_VIEW_TRANSITION_TYPE || "object" === typeof type && null !== type && (type.$$typeof === REACT_LAZY_TYPE || type.$$typeof === REACT_MEMO_TYPE || type.$$typeof === REACT_CONTEXT_TYPE || type.$$typeof === REACT_CONSUMER_TYPE || type.$$typeof === REACT_FORWARD_REF_TYPE || type.$$typeof === REACT_CLIENT_REFERENCE || void 0 !== type.getModuleId) ? true : false;
        };
        exports.typeOf = typeOf;
      })();
    }
  });

  // node_modules/.store/react-is@19.3.0-h9DBeE4BCfrMSfxZZXEDFw/node_modules/react-is/index.js
  var require_react_is = __commonJS({
    "node_modules/.store/react-is@19.3.0-h9DBeE4BCfrMSfxZZXEDFw/node_modules/react-is/index.js"(exports, module) {
      "use strict";
      if (false) {
        module.exports = null;
      } else {
        module.exports = require_react_is_development();
      }
    }
  });

  // package-external:@wordpress/primitives
  var require_primitives = __commonJS({
    "package-external:@wordpress/primitives"(exports, module) {
      module.exports = window.wp.primitives;
    }
  });

  // packages/plugins/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    PluginArea: () => plugin_area_default,
    getPlugin: () => getPlugin,
    getPlugins: () => getPlugins,
    registerPlugin: () => registerPlugin,
    unregisterPlugin: () => unregisterPlugin,
    usePluginContext: () => usePluginContext,
    withPluginContext: () => withPluginContext
  });

  // node_modules/.store/memize@2.1.1-roXbtpG2bis3Lggp7SQpAA/node_modules/memize/dist/index.js
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

  // packages/plugins/build-module/components/plugin-area/index.mjs
  var import_element3 = __toESM(require_element(), 1);
  var import_hooks2 = __toESM(require_hooks(), 1);
  var import_is_shallow_equal = __toESM(require_is_shallow_equal(), 1);

  // packages/plugins/build-module/components/plugin-context/index.mjs
  var import_element = __toESM(require_element(), 1);
  var import_compose = __toESM(require_compose(), 1);
  var import_deprecated = __toESM(require_deprecated(), 1);
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
  var Context = (0, import_element.createContext)({
    name: null,
    icon: null
  });
  Context.displayName = "PluginContext";
  var PluginContextProvider = Context.Provider;
  function usePluginContext() {
    return (0, import_element.useContext)(Context);
  }
  var withPluginContext = (mapContextToProps) => (0, import_compose.createHigherOrderComponent)((OriginalComponent) => {
    (0, import_deprecated.default)("wp.plugins.withPluginContext", {
      since: "6.8.0",
      alternative: "wp.plugins.usePluginContext"
    });
    return (props) => /* @__PURE__ */ (0, import_jsx_runtime.jsx)(Context.Consumer, { children: (context) => /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
      OriginalComponent,
      {
        ...props,
        ...mapContextToProps(context, props)
      }
    ) });
  }, "withPluginContext");

  // packages/plugins/build-module/components/plugin-error-boundary/index.mjs
  var import_element2 = __toESM(require_element(), 1);
  var PluginErrorBoundary = class extends import_element2.Component {
    constructor(props) {
      super(props);
      this.state = {
        hasError: false
      };
    }
    static getDerivedStateFromError() {
      return { hasError: true };
    }
    componentDidCatch(error) {
      const { name, onError } = this.props;
      if (onError) {
        onError(name, error);
      }
    }
    render() {
      if (!this.state.hasError) {
        return this.props.children;
      }
      return null;
    }
  };

  // packages/plugins/build-module/api/index.mjs
  var import_react_is = __toESM(require_react_is(), 1);
  var import_hooks = __toESM(require_hooks(), 1);

  // packages/icons/build-module/library/plugins.mjs
  var import_primitives = __toESM(require_primitives(), 1);
  var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
  var plugins_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives.Path, { d: "M15.3995 11.5711L12.2175 8.38909L10.273 6.44454L7.67678 9.04074C7.3017 9.41581 7.09099 9.92452 7.09099 10.455L7.09099 13.455C7.09099 13.7202 6.98563 13.9745 6.7981 14.1621L5.14645 15.8137C4.95118 16.009 4.95118 16.3256 5.14645 16.5208L7.26777 18.6421C7.46303 18.8374 7.77961 18.8374 7.97487 18.6421L9.62652 16.9905C9.81406 16.8029 10.0684 16.6976 10.3336 16.6976H13.3336C13.8641 16.6976 14.3728 16.4869 14.7478 16.1118L17.344 13.5156L15.3995 11.5711ZM12.2175 8.38909L16.1066 4.5M15.3995 11.5711L19.2886 7.68198" }) });

  // packages/plugins/build-module/api/index.mjs
  var plugins = {};
  function registerPlugin(name, settings) {
    if (typeof settings !== "object") {
      console.error("No settings object provided!");
      return null;
    }
    if (typeof name !== "string") {
      console.error("Plugin name must be string.");
      return null;
    }
    if (!/^[a-z][a-z0-9-]*$/.test(name)) {
      console.error(
        'Plugin name must include only lowercase alphanumeric characters or dashes, and start with a letter. Example: "my-plugin".'
      );
      return null;
    }
    if (plugins[name]) {
      console.error(`Plugin "${name}" is already registered.`);
    }
    settings = (0, import_hooks.applyFilters)(
      "plugins.registerPlugin",
      settings,
      name
    );
    const { render, scope } = settings;
    if (!(0, import_react_is.isValidElementType)(render)) {
      console.error(
        'The "render" property must be specified and must be a valid component.'
      );
      return null;
    }
    if (scope) {
      if (typeof scope !== "string") {
        console.error("Plugin scope must be string.");
        return null;
      }
      if (!/^[a-z][a-z0-9-]*$/.test(scope)) {
        console.error(
          'Plugin scope must include only lowercase alphanumeric characters or dashes, and start with a letter. Example: "my-page".'
        );
        return null;
      }
    }
    plugins[name] = {
      name,
      icon: plugins_default,
      ...settings
    };
    (0, import_hooks.doAction)("plugins.pluginRegistered", settings, name);
    return settings;
  }
  function unregisterPlugin(name) {
    if (!plugins[name]) {
      console.error('Plugin "' + name + '" is not registered.');
      return;
    }
    const oldPlugin = plugins[name];
    delete plugins[name];
    (0, import_hooks.doAction)("plugins.pluginUnregistered", oldPlugin, name);
    return oldPlugin;
  }
  function getPlugin(name) {
    return plugins[name];
  }
  function getPlugins(scope) {
    return Object.values(plugins).filter(
      (plugin) => plugin.scope === scope
    );
  }

  // packages/plugins/build-module/components/plugin-area/index.mjs
  var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
  var getPluginContext = memize(
    (icon, name) => ({
      icon,
      name
    })
  );
  function PluginArea({
    scope,
    onError
  }) {
    const store = (0, import_element3.useMemo)(() => {
      let lastValue = [];
      return {
        subscribe(listener) {
          (0, import_hooks2.addAction)(
            "plugins.pluginRegistered",
            "core/plugins/plugin-area/plugins-registered",
            listener
          );
          (0, import_hooks2.addAction)(
            "plugins.pluginUnregistered",
            "core/plugins/plugin-area/plugins-unregistered",
            listener
          );
          return () => {
            (0, import_hooks2.removeAction)(
              "plugins.pluginRegistered",
              "core/plugins/plugin-area/plugins-registered"
            );
            (0, import_hooks2.removeAction)(
              "plugins.pluginUnregistered",
              "core/plugins/plugin-area/plugins-unregistered"
            );
          };
        },
        getValue() {
          const nextValue = getPlugins(scope);
          if (!(0, import_is_shallow_equal.isShallowEqual)(lastValue, nextValue)) {
            lastValue = nextValue;
          }
          return lastValue;
        }
      };
    }, [scope]);
    const plugins2 = (0, import_element3.useSyncExternalStore)(
      store.subscribe,
      store.getValue,
      store.getValue
    );
    return /* @__PURE__ */ (0, import_jsx_runtime3.jsx)("div", { style: { display: "none" }, children: plugins2.map(({ icon, name, render: Plugin }) => /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
      PluginContextProvider,
      {
        value: getPluginContext(icon, name),
        children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(PluginErrorBoundary, { name, onError, children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(Plugin, {}) })
      },
      name
    )) });
  }
  var plugin_area_default = PluginArea;
  return __toCommonJS(index_exports);
})();
/*! Bundled license information:

react-is/cjs/react-is.development.js:
  (**
   * @license React
   * react-is.development.js
   *
   * Copyright (c) Meta Platforms, Inc. and affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   *)
*/
(window.wp ||= {}).plugins = wp.plugins;
})();
