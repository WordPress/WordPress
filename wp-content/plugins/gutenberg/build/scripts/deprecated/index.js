"use strict";
var wp;
(wp ||= {}).deprecated = (() => {
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

  // package-external:@wordpress/hooks
  var require_hooks = __commonJS({
    "package-external:@wordpress/hooks"(exports, module) {
      module.exports = window.wp.hooks;
    }
  });

  // packages/deprecated/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    default: () => deprecated,
    logged: () => logged
  });
  var import_hooks = __toESM(require_hooks());
  var logged = /* @__PURE__ */ Object.create(null);
  function deprecated(feature, options = {}) {
    const { since, version, alternative, plugin, link, hint } = options;
    const pluginMessage = plugin ? ` from ${plugin}` : "";
    const sinceMessage = since ? ` since version ${since}` : "";
    const versionMessage = version ? ` and will be removed${pluginMessage} in version ${version}` : "";
    const useInsteadMessage = alternative ? ` Please use ${alternative} instead.` : "";
    const linkMessage = link ? ` See: ${link}` : "";
    const hintMessage = hint ? ` Note: ${hint}` : "";
    const message = `${feature} is deprecated${sinceMessage}${versionMessage}.${useInsteadMessage}${linkMessage}${hintMessage}`;
    if (message in logged) {
      return;
    }
    (0, import_hooks.doAction)("deprecated", feature, options, message);
    console.warn(message);
    logged[message] = true;
  }
  return __toCommonJS(index_exports);
})();
if (typeof wp.deprecated === 'object' && wp.deprecated.default) { wp.deprecated = wp.deprecated.default; }
//# sourceMappingURL=index.js.map
