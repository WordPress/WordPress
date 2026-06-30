var wp;
(wp ||= {}).nux = (() => {
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

  // package-external:@wordpress/deprecated
  var require_deprecated = __commonJS({
    "package-external:@wordpress/deprecated"(exports, module) {
      module.exports = window.wp.deprecated;
    }
  });

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // packages/nux/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    DotTip: () => dot_tip_default,
    store: () => store
  });
  var import_deprecated = __toESM(require_deprecated(), 1);

  // packages/nux/build-module/store/index.mjs
  var import_data = __toESM(require_data(), 1);

  // packages/nux/build-module/store/reducer.mjs
  var DEFAULT_STATE = {
    guides: [],
    preferences: {
      areTipsEnabled: false,
      dismissedTips: {}
    }
  };
  function reducer(state = DEFAULT_STATE) {
    return state;
  }

  // packages/nux/build-module/store/actions.mjs
  var actions_exports = {};
  __export(actions_exports, {
    disableTips: () => disableTips,
    dismissTip: () => dismissTip,
    enableTips: () => enableTips,
    triggerGuide: () => triggerGuide
  });
  function triggerGuide(tipIds) {
    return {
      type: "TRIGGER_GUIDE",
      tipIds
    };
  }
  function dismissTip(id) {
    return {
      type: "DISMISS_TIP",
      id
    };
  }
  function disableTips() {
    return {
      type: "DISABLE_TIPS"
    };
  }
  function enableTips() {
    return {
      type: "ENABLE_TIPS"
    };
  }

  // packages/nux/build-module/store/selectors.mjs
  var selectors_exports = {};
  __export(selectors_exports, {
    areTipsEnabled: () => areTipsEnabled,
    getAssociatedGuide: () => getAssociatedGuide,
    isTipVisible: () => isTipVisible
  });
  function getAssociatedGuide() {
    return null;
  }
  function isTipVisible() {
    return false;
  }
  function areTipsEnabled() {
    return false;
  }

  // packages/nux/build-module/store/index.mjs
  var STORE_NAME = "core/nux";
  var store = (0, import_data.createReduxStore)(STORE_NAME, {
    reducer,
    actions: actions_exports,
    selectors: selectors_exports,
    persist: ["preferences"]
  });
  (0, import_data.registerStore)(STORE_NAME, {
    reducer,
    actions: actions_exports,
    selectors: selectors_exports,
    persist: ["preferences"]
  });

  // packages/nux/build-module/components/dot-tip/index.mjs
  function DotTip() {
    return null;
  }
  var dot_tip_default = DotTip;

  // packages/nux/build-module/index.mjs
  (0, import_deprecated.default)("wp.nux", {
    since: "5.4",
    hint: "wp.components.Guide can be used to show a user guide.",
    version: "6.2"
  });
  return __toCommonJS(index_exports);
})();
