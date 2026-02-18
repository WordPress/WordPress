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

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // packages/notices/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    InlineNotices: () => InlineNotices,
    SnackbarNotices: () => SnackbarNotices,
    store: () => store
  });

  // packages/notices/build-module/store/index.mjs
  var import_data = __toESM(require_data(), 1);

  // packages/notices/build-module/store/utils/on-sub-key.mjs
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

  // packages/notices/build-module/store/reducer.mjs
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
      default:
        return state;
    }
  });
  var reducer_default = notices;

  // packages/notices/build-module/store/actions.mjs
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

  // packages/notices/build-module/store/constants.mjs
  var DEFAULT_CONTEXT = "global";
  var DEFAULT_STATUS = "info";

  // packages/notices/build-module/store/actions.mjs
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

  // packages/notices/build-module/store/selectors.mjs
  var selectors_exports = {};
  __export(selectors_exports, {
    getNotices: () => getNotices
  });
  var DEFAULT_NOTICES = [];
  function getNotices(state, context = DEFAULT_CONTEXT) {
    return state[context] || DEFAULT_NOTICES;
  }

  // packages/notices/build-module/store/index.mjs
  var store = (0, import_data.createReduxStore)("core/notices", {
    reducer: reducer_default,
    actions: actions_exports,
    selectors: selectors_exports
  });
  (0, import_data.register)(store);

  // node_modules/clsx/dist/clsx.mjs
  function r(e) {
    var t, f, n = "";
    if ("string" == typeof e || "number" == typeof e) n += e;
    else if ("object" == typeof e) if (Array.isArray(e)) {
      var o = e.length;
      for (t = 0; t < o; t++) e[t] && (f = r(e[t])) && (n && (n += " "), n += f);
    } else for (f in e) e[f] && (n && (n += " "), n += f);
    return n;
  }
  function clsx() {
    for (var e, t, f = 0, n = "", o = arguments.length; f < o; f++) (e = arguments[f]) && (t = r(e)) && (n && (n += " "), n += t);
    return n;
  }
  var clsx_default = clsx;

  // packages/notices/build-module/components/inline-notices/index.mjs
  var import_components = __toESM(require_components(), 1);
  var import_data2 = __toESM(require_data(), 1);
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
  if (typeof document !== "undefined" && !document.head.querySelector("style[data-wp-hash='fa538c333d']")) {
    const style = document.createElement("style");
    style.setAttribute("data-wp-hash", "fa538c333d");
    style.appendChild(document.createTextNode(".components-notices__dismissible,.components-notices__pinned{color:#1e1e1e}.components-notices__dismissible .components-notice,.components-notices__pinned .components-notice{border-bottom:1px solid #0003;box-sizing:border-box;min-height:64px;padding:0 12px}.components-notices__dismissible .components-notice .components-notice__dismiss,.components-notices__pinned .components-notice .components-notice__dismiss{margin-top:12px}"));
    document.head.appendChild(style);
  }
  function InlineNotices({
    children,
    pinnedNoticesClassName,
    dismissibleNoticesClassName,
    context
  }) {
    const notices2 = (0, import_data2.useSelect)(
      (select) => select(store).getNotices(context),
      [context]
    );
    const { removeNotice: removeNotice2 } = (0, import_data2.useDispatch)(store);
    const dismissibleNotices = notices2.filter(
      ({ isDismissible, type }) => isDismissible && type === "default"
    );
    const nonDismissibleNotices = notices2.filter(
      ({ isDismissible, type }) => !isDismissible && type === "default"
    );
    return /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(import_jsx_runtime.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
        import_components.NoticeList,
        {
          notices: nonDismissibleNotices,
          className: clsx_default(
            "components-notices__pinned",
            pinnedNoticesClassName
          )
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
        import_components.NoticeList,
        {
          notices: dismissibleNotices,
          className: clsx_default(
            "components-notices__dismissible",
            dismissibleNoticesClassName
          ),
          onRemove: (id) => removeNotice2(id, context),
          children
        }
      )
    ] });
  }

  // packages/notices/build-module/components/snackbar-notices/index.mjs
  var import_components2 = __toESM(require_components(), 1);
  var import_data3 = __toESM(require_data(), 1);
  var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
  var MAX_VISIBLE_NOTICES = -3;
  function SnackbarNotices({
    className,
    context
  }) {
    const notices2 = (0, import_data3.useSelect)(
      (select) => select(store).getNotices(context),
      [context]
    );
    const { removeNotice: removeNotice2 } = (0, import_data3.useDispatch)(store);
    const snackbarNotices = notices2.filter(({ type }) => type === "snackbar").slice(MAX_VISIBLE_NOTICES);
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
      import_components2.SnackbarList,
      {
        notices: snackbarNotices,
        className: clsx_default("components-notices__snackbar", className),
        onRemove: (id) => removeNotice2(id, context)
      }
    );
  }
  return __toCommonJS(index_exports);
})();