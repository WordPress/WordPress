"use strict";
var wp;
(wp ||= {}).reactI18n = (() => {
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

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // packages/react-i18n/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    I18nProvider: () => I18nProvider,
    useI18n: () => useI18n,
    withI18n: () => withI18n
  });
  var import_element = __toESM(require_element());
  var import_i18n = __toESM(require_i18n());
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  function makeContextValue(i18n) {
    return {
      __: i18n.__.bind(i18n),
      _x: i18n._x.bind(i18n),
      _n: i18n._n.bind(i18n),
      _nx: i18n._nx.bind(i18n),
      isRTL: i18n.isRTL.bind(i18n),
      hasTranslation: i18n.hasTranslation.bind(i18n)
    };
  }
  var I18nContext = (0, import_element.createContext)(makeContextValue(import_i18n.defaultI18n));
  I18nContext.displayName = "I18nContext";
  function I18nProvider(props) {
    const { children, i18n = import_i18n.defaultI18n } = props;
    const [update, forceUpdate] = (0, import_element.useReducer)(() => [], []);
    (0, import_element.useEffect)(() => i18n.subscribe(forceUpdate), [i18n]);
    const value = (0, import_element.useMemo)(() => makeContextValue(i18n), [i18n, update]);
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(I18nContext.Provider, { value, children });
  }
  var useI18n = () => (0, import_element.useContext)(I18nContext);
  function withI18n(InnerComponent) {
    const EnhancedComponent = (props) => {
      const i18nProps = useI18n();
      return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(InnerComponent, { ...props, ...i18nProps });
    };
    const innerComponentName = InnerComponent.displayName || InnerComponent.name || "Component";
    EnhancedComponent.displayName = `WithI18n(${innerComponentName})`;
    return EnhancedComponent;
  }
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
