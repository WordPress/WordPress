"use strict";
var wp;
(wp ||= {}).notices = (() => {
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

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // packages/notices/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    store: () => store
  });

  // packages/notices/build-module/store/index.js
  var import_data = __toESM(require_data());

  // packages/notices/build-module/store/utils/on-sub-key.js
  var onSubKey = (actionProperty) => (reducer) => (state = {}, action) => {
    const key = action[actionProperty];
    if (key === void 0) {
      return state;
    }
    const nextKeyState = reducer(state[key], action);
    if (nextKeyState === state[key]) {
      return state;
    }
    return {
      ...state,
      [key]: nextKeyState
    };
  };
  var on_sub_key_default = onSubKey;

  // packages/notices/build-module/store/reducer.js
  var notices = on_sub_key_default("context")((state = [], action) => {
    switch (action.type) {
      case "CREATE_NOTICE":
        return [
          ...state.filter(({ id }) => id !== action.notice.id),
          action.notice
        ];
      case "REMOVE_NOTICE":
        return state.filter(({ id }) => id !== action.id);
      case "REMOVE_NOTICES":
        return state.filter(({ id }) => !action.ids.includes(id));
      case "REMOVE_ALL_NOTICES":
        return state.filter(({ type }) => type !== action.noticeType);
    }
    return state;
  });
  var reducer_default = notices;

  // packages/notices/build-module/store/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    createErrorNotice: () => createErrorNotice,
    createInfoNotice: () => createInfoNotice,
    createNotice: () => createNotice,
    createSuccessNotice: () => createSuccessNotice,
    createWarningNotice: () => createWarningNotice,
    removeAllNotices: () => removeAllNotices,
    removeNotice: () => removeNotice,
    removeNotices: () => removeNotices
  });

  // packages/notices/build-module/store/constants.js
  var DEFAULT_CONTEXT = "global";
  var DEFAULT_STATUS = "info";

  // packages/notices/build-module/store/actions.js
  var uniqueId = 0;
  function createNotice(status = DEFAULT_STATUS, content, options = {}) {
    const {
      speak = true,
      isDismissible = true,
      context = DEFAULT_CONTEXT,
      id = `${context}${++uniqueId}`,
      actions = [],
      type = "default",
      __unstableHTML,
      icon = null,
      explicitDismiss = false,
      onDismiss
    } = options;
    content = String(content);
    return {
      type: "CREATE_NOTICE",
      context,
      notice: {
        id,
        status,
        content,
        spokenMessage: speak ? content : null,
        __unstableHTML,
        isDismissible,
        actions,
        type,
        icon,
        explicitDismiss,
        onDismiss
      }
    };
  }
  function createSuccessNotice(content, options) {
    return createNotice("success", content, options);
  }
  function createInfoNotice(content, options) {
    return createNotice("info", content, options);
  }
  function createErrorNotice(content, options) {
    return createNotice("error", content, options);
  }
  function createWarningNotice(content, options) {
    return createNotice("warning", content, options);
  }
  function removeNotice(id, context = DEFAULT_CONTEXT) {
    return {
      type: "REMOVE_NOTICE",
      id,
      context
    };
  }
  function removeAllNotices(noticeType = "default", context = DEFAULT_CONTEXT) {
    return {
      type: "REMOVE_ALL_NOTICES",
      noticeType,
      context
    };
  }
  function removeNotices(ids, context = DEFAULT_CONTEXT) {
    return {
      type: "REMOVE_NOTICES",
      ids,
      context
    };
  }

  // packages/notices/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    getNotices: () => getNotices
  });
  var DEFAULT_NOTICES = [];
  function getNotices(state, context = DEFAULT_CONTEXT) {
    return state[context] || DEFAULT_NOTICES;
  }

  // packages/notices/build-module/store/index.js
  var store = (0, import_data.createReduxStore)("core/notices", {
    reducer: reducer_default,
    actions: actions_exports,
    selectors: selectors_exports
  });
  (0, import_data.register)(store);
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
