"use strict";
var wp;
(wp ||= {}).domReady = (() => {
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

  // packages/dom-ready/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    default: () => domReady
  });
  function domReady(callback) {
    if (typeof document === "undefined") {
      return;
    }
    if (document.readyState === "complete" || // DOMContentLoaded + Images/Styles/etc loaded, so we call directly.
    document.readyState === "interactive") {
      return void callback();
    }
    document.addEventListener("DOMContentLoaded", callback);
  }
  return __toCommonJS(index_exports);
})();
if (typeof wp.domReady === 'object' && wp.domReady.default) { wp.domReady = wp.domReady.default; }
//# sourceMappingURL=index.js.map
