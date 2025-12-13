"use strict";
var wp;
(wp ||= {}).blob = (() => {
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

  // packages/blob/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    createBlobURL: () => createBlobURL,
    downloadBlob: () => downloadBlob,
    getBlobByURL: () => getBlobByURL,
    getBlobTypeByURL: () => getBlobTypeByURL,
    isBlobURL: () => isBlobURL,
    revokeBlobURL: () => revokeBlobURL
  });
  var cache = {};
  function createBlobURL(file) {
    const url = window.URL.createObjectURL(file);
    cache[url] = file;
    return url;
  }
  function getBlobByURL(url) {
    return cache[url];
  }
  function getBlobTypeByURL(url) {
    return getBlobByURL(url)?.type.split("/")[0];
  }
  function revokeBlobURL(url) {
    if (cache[url]) {
      window.URL.revokeObjectURL(url);
    }
    delete cache[url];
  }
  function isBlobURL(url) {
    if (!url || !url.indexOf) {
      return false;
    }
    return url.indexOf("blob:") === 0;
  }
  function downloadBlob(filename, content, contentType = "") {
    if (!filename || !content) {
      return;
    }
    const file = new window.Blob([content], { type: contentType });
    const url = window.URL.createObjectURL(file);
    const anchorElement = document.createElement("a");
    anchorElement.href = url;
    anchorElement.download = filename;
    anchorElement.style.display = "none";
    document.body.appendChild(anchorElement);
    anchorElement.click();
    document.body.removeChild(anchorElement);
    window.URL.revokeObjectURL(url);
  }
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
