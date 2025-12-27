"use strict";
var wp;
(wp ||= {}).privateApis = (() => {
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

  // packages/private-apis/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    __dangerousOptInToUnstableAPIsOnlyForCoreModules: () => __dangerousOptInToUnstableAPIsOnlyForCoreModules
  });

  // packages/private-apis/build-module/implementation.js
  var CORE_MODULES_USING_PRIVATE_APIS = [
    "@wordpress/block-directory",
    "@wordpress/block-editor",
    "@wordpress/block-library",
    "@wordpress/blocks",
    "@wordpress/boot",
    "@wordpress/commands",
    "@wordpress/components",
    "@wordpress/core-commands",
    "@wordpress/core-data",
    "@wordpress/customize-widgets",
    "@wordpress/data",
    "@wordpress/edit-post",
    "@wordpress/edit-site",
    "@wordpress/edit-widgets",
    "@wordpress/editor",
    "@wordpress/format-library",
    "@wordpress/patterns",
    "@wordpress/preferences",
    "@wordpress/reusable-blocks",
    "@wordpress/router",
    "@wordpress/routes",
    "@wordpress/sync",
    "@wordpress/theme",
    "@wordpress/dataviews",
    "@wordpress/fields",
    "@wordpress/lazy-editor",
    "@wordpress/media-utils",
    "@wordpress/upload-media",
    "@wordpress/global-styles-ui"
  ];
  var registeredPrivateApis = [];
  var requiredConsent = "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.";
  var allowReRegistration = false ? false : true;
  var __dangerousOptInToUnstableAPIsOnlyForCoreModules = (consent, moduleName) => {
    if (!CORE_MODULES_USING_PRIVATE_APIS.includes(moduleName)) {
      throw new Error(
        `You tried to opt-in to unstable APIs as module "${moduleName}". This feature is only for JavaScript modules shipped with WordPress core. Please do not use it in plugins and themes as the unstable APIs will be removed without a warning. If you ignore this error and depend on unstable features, your product will inevitably break on one of the next WordPress releases.`
      );
    }
    if (!allowReRegistration && registeredPrivateApis.includes(moduleName)) {
      throw new Error(
        `You tried to opt-in to unstable APIs as module "${moduleName}" which is already registered. This feature is only for JavaScript modules shipped with WordPress core. Please do not use it in plugins and themes as the unstable APIs will be removed without a warning. If you ignore this error and depend on unstable features, your product will inevitably break on one of the next WordPress releases.`
      );
    }
    if (consent !== requiredConsent) {
      throw new Error(
        `You tried to opt-in to unstable APIs without confirming you know the consequences. This feature is only for JavaScript modules shipped with WordPress core. Please do not use it in plugins and themes as the unstable APIs will removed without a warning. If you ignore this error and depend on unstable features, your product will inevitably break on the next WordPress release.`
      );
    }
    registeredPrivateApis.push(moduleName);
    return {
      lock,
      unlock
    };
  };
  function lock(object, privateData) {
    if (!object) {
      throw new Error("Cannot lock an undefined object.");
    }
    const _object = object;
    if (!(__private in _object)) {
      _object[__private] = {};
    }
    lockedData.set(_object[__private], privateData);
  }
  function unlock(object) {
    if (!object) {
      throw new Error("Cannot unlock an undefined object.");
    }
    const _object = object;
    if (!(__private in _object)) {
      throw new Error(
        "Cannot unlock an object that was not locked before. "
      );
    }
    return lockedData.get(_object[__private]);
  }
  var lockedData = /* @__PURE__ */ new WeakMap();
  var __private = Symbol("Private API ID");
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
