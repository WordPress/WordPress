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

  // packages/plugins/node_modules/react-is/cjs/react-is.development.js
  var require_react_is_development = __commonJS({
    "packages/plugins/node_modules/react-is/cjs/react-is.development.js"(exports) {
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

  // packages/plugins/node_modules/react-is/index.js
  var require_react_is = __commonJS({
    "packages/plugins/node_modules/react-is/index.js"(exports, module) {
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
  var plugins_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives.Path, { d: "M10.5 4v4h3V4H15v4h1.5a1 1 0 011 1v4l-3 4v2a1 1 0 01-1 1h-3a1 1 0 01-1-1v-2l-3-4V9a1 1 0 011-1H9V4h1.5zm.5 12.5v2h2v-2l3-4v-3H8v3l3 4z" }) });

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
   * Copyright (c) Facebook, Inc. and its affiliates.
   *
   * This source code is licensed under the MIT license found in the
   * LICENSE file in the root directory of this source tree.
   *)
*/
