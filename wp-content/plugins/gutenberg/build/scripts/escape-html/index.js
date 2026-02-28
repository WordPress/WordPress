"use strict";
var wp;
(wp ||= {}).escapeHtml = (() => {
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

  // packages/escape-html/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    escapeAmpersand: () => escapeAmpersand,
    escapeAttribute: () => escapeAttribute,
    escapeEditableHTML: () => escapeEditableHTML,
    escapeHTML: () => escapeHTML,
    escapeLessThan: () => escapeLessThan,
    escapeQuotationMark: () => escapeQuotationMark,
    isValidAttributeName: () => isValidAttributeName
  });

  // packages/escape-html/build-module/escape-greater.js
  function __unstableEscapeGreaterThan(value) {
    return value.replace(/>/g, "&gt;");
  }

  // packages/escape-html/build-module/index.js
  var REGEXP_INVALID_ATTRIBUTE_NAME = /[\u007F-\u009F "'>/="\uFDD0-\uFDEF]/;
  function escapeAmpersand(value) {
    return value.replace(/&(?!([a-z0-9]+|#[0-9]+|#x[a-f0-9]+);)/gi, "&amp;");
  }
  function escapeQuotationMark(value) {
    return value.replace(/"/g, "&quot;");
  }
  function escapeLessThan(value) {
    return value.replace(/</g, "&lt;");
  }
  function escapeAttribute(value) {
    return __unstableEscapeGreaterThan(
      escapeQuotationMark(escapeAmpersand(value))
    );
  }
  function escapeHTML(value) {
    return escapeLessThan(escapeAmpersand(value));
  }
  function escapeEditableHTML(value) {
    return escapeLessThan(value.replace(/&/g, "&amp;"));
  }
  function isValidAttributeName(name) {
    return !REGEXP_INVALID_ATTRIBUTE_NAME.test(name);
  }
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
