"use strict";
var wp;
(wp ||= {}).dataControls = (() => {
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

  // package-external:@wordpress/api-fetch
  var require_api_fetch = __commonJS({
    "package-external:@wordpress/api-fetch"(exports, module) {
      module.exports = window.wp.apiFetch;
    }
  });

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // package-external:@wordpress/deprecated
  var require_deprecated = __commonJS({
    "package-external:@wordpress/deprecated"(exports, module) {
      module.exports = window.wp.deprecated;
    }
  });

  // packages/data-controls/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    __unstableAwaitPromise: () => __unstableAwaitPromise,
    apiFetch: () => apiFetch,
    controls: () => controls,
    dispatch: () => dispatch,
    select: () => select,
    syncSelect: () => syncSelect
  });
  var import_api_fetch = __toESM(require_api_fetch());
  var import_data = __toESM(require_data());
  var import_deprecated = __toESM(require_deprecated());
  function apiFetch(request) {
    return {
      type: "API_FETCH",
      request
    };
  }
  function select(storeNameOrDescriptor, selectorName, ...args) {
    (0, import_deprecated.default)("`select` control in `@wordpress/data-controls`", {
      since: "5.7",
      alternative: "built-in `resolveSelect` control in `@wordpress/data`"
    });
    return import_data.controls.resolveSelect(
      storeNameOrDescriptor,
      selectorName,
      ...args
    );
  }
  function syncSelect(storeNameOrDescriptor, selectorName, ...args) {
    (0, import_deprecated.default)("`syncSelect` control in `@wordpress/data-controls`", {
      since: "5.7",
      alternative: "built-in `select` control in `@wordpress/data`"
    });
    return import_data.controls.select(storeNameOrDescriptor, selectorName, ...args);
  }
  function dispatch(storeNameOrDescriptor, actionName, ...args) {
    (0, import_deprecated.default)("`dispatch` control in `@wordpress/data-controls`", {
      since: "5.7",
      alternative: "built-in `dispatch` control in `@wordpress/data`"
    });
    return import_data.controls.dispatch(storeNameOrDescriptor, actionName, ...args);
  }
  var __unstableAwaitPromise = function(promise) {
    return {
      type: "AWAIT_PROMISE",
      promise
    };
  };
  var controls = {
    AWAIT_PROMISE({ promise }) {
      return promise;
    },
    API_FETCH({ request }) {
      return (0, import_api_fetch.default)(request);
    }
  };
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
