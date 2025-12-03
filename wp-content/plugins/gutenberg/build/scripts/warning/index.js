"use strict";
var wp;
(wp ||= {}).warning = (() => {
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

  // packages/warning/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    default: () => warning
  });

  // packages/warning/build-module/utils.js
  var logged = /* @__PURE__ */ new Set();

  // packages/warning/build-module/index.js
  function isDev() {
    return true;
  }
  function warning(message) {
    if (!isDev()) {
      return;
    }
    if (logged.has(message)) {
      return;
    }
    console.warn(message);
    try {
      throw Error(message);
    } catch (x) {
    }
    logged.add(message);
  }
  return __toCommonJS(index_exports);
})();
if (typeof wp.warning === 'object' && wp.warning.default) { wp.warning = wp.warning.default; }
//# sourceMappingURL=index.js.map
