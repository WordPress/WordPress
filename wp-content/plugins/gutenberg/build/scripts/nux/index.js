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

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/primitives
  var require_primitives = __commonJS({
    "package-external:@wordpress/primitives"(exports, module) {
      module.exports = window.wp.primitives;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // packages/nux/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    DotTip: () => dot_tip_default,
    store: () => store
  });
  var import_deprecated = __toESM(require_deprecated());

  // packages/nux/build-module/store/index.js
  var import_data3 = __toESM(require_data());

  // packages/nux/build-module/store/reducer.js
  var import_data = __toESM(require_data());
  function guides(state = [], action) {
    switch (action.type) {
      case "TRIGGER_GUIDE":
        return [...state, action.tipIds];
    }
    return state;
  }
  function areTipsEnabled(state = true, action) {
    switch (action.type) {
      case "DISABLE_TIPS":
        return false;
      case "ENABLE_TIPS":
        return true;
    }
    return state;
  }
  function dismissedTips(state = {}, action) {
    switch (action.type) {
      case "DISMISS_TIP":
        return {
          ...state,
          [action.id]: true
        };
      case "ENABLE_TIPS":
        return {};
    }
    return state;
  }
  var preferences = (0, import_data.combineReducers)({ areTipsEnabled, dismissedTips });
  var reducer_default = (0, import_data.combineReducers)({ guides, preferences });

  // packages/nux/build-module/store/actions.js
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

  // packages/nux/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    areTipsEnabled: () => areTipsEnabled2,
    getAssociatedGuide: () => getAssociatedGuide,
    isTipVisible: () => isTipVisible
  });
  var import_data2 = __toESM(require_data());
  var getAssociatedGuide = (0, import_data2.createSelector)(
    (state, tipId) => {
      for (const tipIds of state.guides) {
        if (tipIds.includes(tipId)) {
          const nonDismissedTips = tipIds.filter(
            (tId) => !Object.keys(
              state.preferences.dismissedTips
            ).includes(tId)
          );
          const [currentTipId = null, nextTipId = null] = nonDismissedTips;
          return { tipIds, currentTipId, nextTipId };
        }
      }
      return null;
    },
    (state) => [state.guides, state.preferences.dismissedTips]
  );
  function isTipVisible(state, tipId) {
    if (!state.preferences.areTipsEnabled) {
      return false;
    }
    if (state.preferences.dismissedTips?.hasOwnProperty(tipId)) {
      return false;
    }
    const associatedGuide = getAssociatedGuide(state, tipId);
    if (associatedGuide && associatedGuide.currentTipId !== tipId) {
      return false;
    }
    return true;
  }
  function areTipsEnabled2(state) {
    return state.preferences.areTipsEnabled;
  }

  // packages/nux/build-module/store/index.js
  var STORE_NAME = "core/nux";
  var store = (0, import_data3.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    actions: actions_exports,
    selectors: selectors_exports,
    persist: ["preferences"]
  });
  (0, import_data3.registerStore)(STORE_NAME, {
    reducer: reducer_default,
    actions: actions_exports,
    selectors: selectors_exports,
    persist: ["preferences"]
  });

  // packages/nux/build-module/components/dot-tip/index.js
  var import_compose = __toESM(require_compose());
  var import_components = __toESM(require_components());
  var import_i18n = __toESM(require_i18n());
  var import_data4 = __toESM(require_data());
  var import_element = __toESM(require_element());

  // packages/icons/build-module/library/close.js
  var import_primitives = __toESM(require_primitives());
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var close_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "m13.06 12 6.47-6.47-1.06-1.06L12 10.94 5.53 4.47 4.47 5.53 10.94 12l-6.47 6.47 1.06 1.06L12 13.06l6.47 6.47 1.06-1.06L13.06 12Z" }) });

  // packages/nux/build-module/components/dot-tip/index.js
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  function onClick(event) {
    event.stopPropagation();
  }
  function DotTip({
    position = "middle right",
    children,
    isVisible,
    hasNextTip,
    onDismiss,
    onDisable
  }) {
    const anchorParent = (0, import_element.useRef)(null);
    const onFocusOutsideCallback = (0, import_element.useCallback)(
      (event) => {
        if (!anchorParent.current) {
          return;
        }
        if (anchorParent.current.contains(event.relatedTarget)) {
          return;
        }
        onDisable();
      },
      [onDisable, anchorParent]
    );
    if (!isVisible) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(
      import_components.Popover,
      {
        className: "nux-dot-tip",
        position,
        focusOnMount: true,
        role: "dialog",
        "aria-label": (0, import_i18n.__)("Editor tips"),
        onClick,
        onFocusOutside: onFocusOutsideCallback,
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime2.jsx)("p", { children }),
          /* @__PURE__ */ (0, import_jsx_runtime2.jsx)("p", { children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
            import_components.Button,
            {
              __next40pxDefaultSize: true,
              variant: "link",
              onClick: onDismiss,
              children: hasNextTip ? (0, import_i18n.__)("See next tip") : (0, import_i18n.__)("Got it")
            }
          ) }),
          /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
            import_components.Button,
            {
              size: "small",
              className: "nux-dot-tip__disable",
              icon: close_default,
              label: (0, import_i18n.__)("Disable tips"),
              onClick: onDisable
            }
          )
        ]
      }
    );
  }
  var dot_tip_default = (0, import_compose.compose)(
    (0, import_data4.withSelect)((select, { tipId }) => {
      const { isTipVisible: isTipVisible2, getAssociatedGuide: getAssociatedGuide2 } = select(store);
      const associatedGuide = getAssociatedGuide2(tipId);
      return {
        isVisible: isTipVisible2(tipId),
        hasNextTip: !!(associatedGuide && associatedGuide.nextTipId)
      };
    }),
    (0, import_data4.withDispatch)((dispatch, { tipId }) => {
      const { dismissTip: dismissTip2, disableTips: disableTips2 } = dispatch(store);
      return {
        onDismiss() {
          dismissTip2(tipId);
        },
        onDisable() {
          disableTips2();
        }
      };
    })
  )(DotTip);

  // packages/nux/build-module/index.js
  (0, import_deprecated.default)("wp.nux", {
    since: "5.4",
    hint: "wp.components.Guide can be used to show a user guide.",
    version: "6.2"
  });
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
