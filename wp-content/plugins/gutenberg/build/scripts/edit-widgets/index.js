var wp;
(wp ||= {}).editWidgets = (() => {
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
    for (var name2 in all)
      __defProp(target, name2, { get: all[name2], enumerable: true });
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

  // package-external:@wordpress/blocks
  var require_blocks = __commonJS({
    "package-external:@wordpress/blocks"(exports, module) {
      module.exports = window.wp.blocks;
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

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/block-library
  var require_block_library = __commonJS({
    "package-external:@wordpress/block-library"(exports, module) {
      module.exports = window.wp.blockLibrary;
    }
  });

  // package-external:@wordpress/core-data
  var require_core_data = __commonJS({
    "package-external:@wordpress/core-data"(exports, module) {
      module.exports = window.wp.coreData;
    }
  });

  // package-external:@wordpress/widgets
  var require_widgets = __commonJS({
    "package-external:@wordpress/widgets"(exports, module) {
      module.exports = window.wp.widgets;
    }
  });

  // package-external:@wordpress/preferences
  var require_preferences = __commonJS({
    "package-external:@wordpress/preferences"(exports, module) {
      module.exports = window.wp.preferences;
    }
  });

  // package-external:@wordpress/api-fetch
  var require_api_fetch = __commonJS({
    "package-external:@wordpress/api-fetch"(exports, module) {
      module.exports = window.wp.apiFetch;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/notices
  var require_notices = __commonJS({
    "package-external:@wordpress/notices"(exports, module) {
      module.exports = window.wp.notices;
    }
  });

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
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

  // package-external:@wordpress/viewport
  var require_viewport = __commonJS({
    "package-external:@wordpress/viewport"(exports, module) {
      module.exports = window.wp.viewport;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/plugins
  var require_plugins = __commonJS({
    "package-external:@wordpress/plugins"(exports, module) {
      module.exports = window.wp.plugins;
    }
  });

  // package-external:@wordpress/block-editor
  var require_block_editor = __commonJS({
    "package-external:@wordpress/block-editor"(exports, module) {
      module.exports = window.wp.blockEditor;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // package-external:@wordpress/hooks
  var require_hooks = __commonJS({
    "package-external:@wordpress/hooks"(exports, module) {
      module.exports = window.wp.hooks;
    }
  });

  // package-external:@wordpress/media-utils
  var require_media_utils = __commonJS({
    "package-external:@wordpress/media-utils"(exports, module) {
      module.exports = window.wp.mediaUtils;
    }
  });

  // package-external:@wordpress/patterns
  var require_patterns = __commonJS({
    "package-external:@wordpress/patterns"(exports, module) {
      module.exports = window.wp.patterns;
    }
  });

  // package-external:@wordpress/keyboard-shortcuts
  var require_keyboard_shortcuts = __commonJS({
    "package-external:@wordpress/keyboard-shortcuts"(exports, module) {
      module.exports = window.wp.keyboardShortcuts;
    }
  });

  // package-external:@wordpress/keycodes
  var require_keycodes = __commonJS({
    "package-external:@wordpress/keycodes"(exports, module) {
      module.exports = window.wp.keycodes;
    }
  });

  // package-external:@wordpress/url
  var require_url = __commonJS({
    "package-external:@wordpress/url"(exports, module) {
      module.exports = window.wp.url;
    }
  });

  // package-external:@wordpress/dom
  var require_dom = __commonJS({
    "package-external:@wordpress/dom"(exports, module) {
      module.exports = window.wp.dom;
    }
  });

  // packages/edit-widgets/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    initialize: () => initialize,
    initializeEditor: () => initializeEditor,
    reinitializeEditor: () => reinitializeEditor,
    store: () => store2
  });
  var import_blocks3 = __toESM(require_blocks());
  var import_data33 = __toESM(require_data());
  var import_deprecated6 = __toESM(require_deprecated());
  var import_element25 = __toESM(require_element());
  var import_block_library2 = __toESM(require_block_library());
  var import_core_data12 = __toESM(require_core_data());
  var import_widgets5 = __toESM(require_widgets());
  var import_preferences10 = __toESM(require_preferences());

  // packages/edit-widgets/build-module/store/index.js
  var import_api_fetch = __toESM(require_api_fetch());
  var import_data8 = __toESM(require_data());

  // packages/edit-widgets/build-module/store/reducer.js
  var import_data = __toESM(require_data());
  function widgetAreasOpenState(state = {}, action) {
    const { type } = action;
    switch (type) {
      case "SET_WIDGET_AREAS_OPEN_STATE": {
        return action.widgetAreasOpenState;
      }
      case "SET_IS_WIDGET_AREA_OPEN": {
        const { clientId, isOpen } = action;
        return {
          ...state,
          [clientId]: isOpen
        };
      }
      default: {
        return state;
      }
    }
  }
  function blockInserterPanel(state = false, action) {
    switch (action.type) {
      case "SET_IS_LIST_VIEW_OPENED":
        return action.isOpen ? false : state;
      case "SET_IS_INSERTER_OPENED":
        return action.value;
    }
    return state;
  }
  function listViewPanel(state = false, action) {
    switch (action.type) {
      case "SET_IS_INSERTER_OPENED":
        return action.value ? false : state;
      case "SET_IS_LIST_VIEW_OPENED":
        return action.isOpen;
    }
    return state;
  }
  function listViewToggleRef(state = { current: null }) {
    return state;
  }
  function inserterSidebarToggleRef(state = { current: null }) {
    return state;
  }
  function widgetSavingLock(state = {}, action) {
    switch (action.type) {
      case "LOCK_WIDGET_SAVING":
        return { ...state, [action.lockName]: true };
      case "UNLOCK_WIDGET_SAVING": {
        const { [action.lockName]: removedLockName, ...restState } = state;
        return restState;
      }
    }
    return state;
  }
  var reducer_default = (0, import_data.combineReducers)({
    blockInserterPanel,
    inserterSidebarToggleRef,
    listViewPanel,
    listViewToggleRef,
    widgetAreasOpenState,
    widgetSavingLock
  });

  // packages/edit-widgets/build-module/store/resolvers.js
  var resolvers_exports = {};
  __export(resolvers_exports, {
    getWidgetAreas: () => getWidgetAreas,
    getWidgets: () => getWidgets
  });
  var import_blocks2 = __toESM(require_blocks());
  var import_core_data2 = __toESM(require_core_data());

  // packages/edit-widgets/build-module/store/actions.js
  var actions_exports2 = {};
  __export(actions_exports2, {
    closeGeneralSidebar: () => closeGeneralSidebar,
    lockWidgetSaving: () => lockWidgetSaving,
    moveBlockToWidgetArea: () => moveBlockToWidgetArea,
    persistStubPost: () => persistStubPost,
    saveEditedWidgetAreas: () => saveEditedWidgetAreas,
    saveWidgetArea: () => saveWidgetArea,
    saveWidgetAreas: () => saveWidgetAreas,
    setIsInserterOpened: () => setIsInserterOpened,
    setIsListViewOpened: () => setIsListViewOpened,
    setIsWidgetAreaOpen: () => setIsWidgetAreaOpen,
    setWidgetAreasOpenState: () => setWidgetAreasOpenState,
    setWidgetIdForClientId: () => setWidgetIdForClientId,
    unlockWidgetSaving: () => unlockWidgetSaving
  });
  var import_i18n3 = __toESM(require_i18n());
  var import_notices = __toESM(require_notices());

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

  // packages/interface/build-module/components/complementary-area/index.js
  var import_components5 = __toESM(require_components());
  var import_data6 = __toESM(require_data());
  var import_i18n = __toESM(require_i18n());

  // packages/icons/build-module/library/block-default.js
  var import_primitives = __toESM(require_primitives());
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var block_default_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M19 8h-1V6h-5v2h-2V6H6v2H5c-1.1 0-2 .9-2 2v8c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2v-8c0-1.1-.9-2-2-2zm.5 10c0 .3-.2.5-.5.5H5c-.3 0-.5-.2-.5-.5v-8c0-.3.2-.5.5-.5h14c.3 0 .5.2.5.5v8z" }) });

  // packages/icons/build-module/library/check.js
  var import_primitives2 = __toESM(require_primitives());
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  var check_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M16.5 7.5 10 13.9l-2.5-2.4-1 1 3.5 3.6 7.5-7.6z" }) });

  // packages/icons/build-module/library/close-small.js
  var import_primitives3 = __toESM(require_primitives());
  var import_jsx_runtime3 = __toESM(require_jsx_runtime());
  var close_small_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z" }) });

  // packages/icons/build-module/library/drawer-left.js
  var import_primitives4 = __toESM(require_primitives());
  var import_jsx_runtime4 = __toESM(require_jsx_runtime());
  var drawer_left_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
    import_primitives4.Path,
    {
      fillRule: "evenodd",
      clipRule: "evenodd",
      d: "M18 4H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zM8.5 18.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h2.5v13zm10-.5c0 .3-.2.5-.5.5h-8v-13h8c.3 0 .5.2.5.5v12z"
    }
  ) });

  // packages/icons/build-module/library/drawer-right.js
  var import_primitives5 = __toESM(require_primitives());
  var import_jsx_runtime5 = __toESM(require_jsx_runtime());
  var drawer_right_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
    import_primitives5.Path,
    {
      fillRule: "evenodd",
      clipRule: "evenodd",
      d: "M18 4H6c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm-4 14.5H6c-.3 0-.5-.2-.5-.5V6c0-.3.2-.5.5-.5h8v13zm4.5-.5c0 .3-.2.5-.5.5h-2.5v-13H18c.3 0 .5.2.5.5v12z"
    }
  ) });

  // packages/icons/build-module/library/external.js
  var import_primitives6 = __toESM(require_primitives());
  var import_jsx_runtime6 = __toESM(require_jsx_runtime());
  var external_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.Path, { d: "M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z" }) });

  // packages/icons/build-module/library/list-view.js
  var import_primitives7 = __toESM(require_primitives());
  var import_jsx_runtime7 = __toESM(require_jsx_runtime());
  var list_view_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.Path, { d: "M3 6h11v1.5H3V6Zm3.5 5.5h11V13h-11v-1.5ZM21 17H10v1.5h11V17Z" }) });

  // packages/icons/build-module/library/more-vertical.js
  var import_primitives8 = __toESM(require_primitives());
  var import_jsx_runtime8 = __toESM(require_jsx_runtime());
  var more_vertical_default = /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.Path, { d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z" }) });

  // packages/icons/build-module/library/plus.js
  var import_primitives9 = __toESM(require_primitives());
  var import_jsx_runtime9 = __toESM(require_jsx_runtime());
  var plus_default = /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives9.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives9.Path, { d: "M11 12.5V17.5H12.5V12.5H17.5V11H12.5V6H11V11H6V12.5H11Z" }) });

  // packages/icons/build-module/library/redo.js
  var import_primitives10 = __toESM(require_primitives());
  var import_jsx_runtime10 = __toESM(require_jsx_runtime());
  var redo_default = /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives10.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives10.Path, { d: "M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z" }) });

  // packages/icons/build-module/library/star-empty.js
  var import_primitives11 = __toESM(require_primitives());
  var import_jsx_runtime11 = __toESM(require_jsx_runtime());
  var star_empty_default = /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives11.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
    import_primitives11.Path,
    {
      fillRule: "evenodd",
      d: "M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z",
      clipRule: "evenodd"
    }
  ) });

  // packages/icons/build-module/library/star-filled.js
  var import_primitives12 = __toESM(require_primitives());
  var import_jsx_runtime12 = __toESM(require_jsx_runtime());
  var star_filled_default = /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives12.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives12.Path, { d: "M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.071-.222L4.754 9.881a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z" }) });

  // packages/icons/build-module/library/undo.js
  var import_primitives13 = __toESM(require_primitives());
  var import_jsx_runtime13 = __toESM(require_jsx_runtime());
  var undo_default = /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives13.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives13.Path, { d: "M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z" }) });

  // packages/interface/build-module/components/complementary-area/index.js
  var import_element2 = __toESM(require_element());
  var import_viewport = __toESM(require_viewport());
  var import_preferences3 = __toESM(require_preferences());
  var import_compose = __toESM(require_compose());
  var import_plugins2 = __toESM(require_plugins());

  // packages/interface/build-module/components/complementary-area-toggle/index.js
  var import_components = __toESM(require_components());
  var import_data5 = __toESM(require_data());
  var import_plugins = __toESM(require_plugins());

  // packages/interface/build-module/store/index.js
  var import_data4 = __toESM(require_data());

  // packages/interface/build-module/store/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    closeModal: () => closeModal,
    disableComplementaryArea: () => disableComplementaryArea,
    enableComplementaryArea: () => enableComplementaryArea,
    openModal: () => openModal,
    pinItem: () => pinItem,
    setDefaultComplementaryArea: () => setDefaultComplementaryArea,
    setFeatureDefaults: () => setFeatureDefaults,
    setFeatureValue: () => setFeatureValue,
    toggleFeature: () => toggleFeature,
    unpinItem: () => unpinItem
  });
  var import_deprecated2 = __toESM(require_deprecated());
  var import_preferences = __toESM(require_preferences());

  // packages/interface/build-module/store/deprecated.js
  var import_deprecated = __toESM(require_deprecated());
  function normalizeComplementaryAreaScope(scope) {
    if (["core/edit-post", "core/edit-site"].includes(scope)) {
      (0, import_deprecated.default)(`${scope} interface scope`, {
        alternative: "core interface scope",
        hint: "core/edit-post and core/edit-site are merging.",
        version: "6.6"
      });
      return "core";
    }
    return scope;
  }
  function normalizeComplementaryAreaName(scope, name2) {
    if (scope === "core" && name2 === "edit-site/template") {
      (0, import_deprecated.default)(`edit-site/template sidebar`, {
        alternative: "edit-post/document",
        version: "6.6"
      });
      return "edit-post/document";
    }
    if (scope === "core" && name2 === "edit-site/block-inspector") {
      (0, import_deprecated.default)(`edit-site/block-inspector sidebar`, {
        alternative: "edit-post/block",
        version: "6.6"
      });
      return "edit-post/block";
    }
    return name2;
  }

  // packages/interface/build-module/store/actions.js
  var setDefaultComplementaryArea = (scope, area) => {
    scope = normalizeComplementaryAreaScope(scope);
    area = normalizeComplementaryAreaName(scope, area);
    return {
      type: "SET_DEFAULT_COMPLEMENTARY_AREA",
      scope,
      area
    };
  };
  var enableComplementaryArea = (scope, area) => ({ registry, dispatch: dispatch2 }) => {
    if (!area) {
      return;
    }
    scope = normalizeComplementaryAreaScope(scope);
    area = normalizeComplementaryAreaName(scope, area);
    const isComplementaryAreaVisible = registry.select(import_preferences.store).get(scope, "isComplementaryAreaVisible");
    if (!isComplementaryAreaVisible) {
      registry.dispatch(import_preferences.store).set(scope, "isComplementaryAreaVisible", true);
    }
    dispatch2({
      type: "ENABLE_COMPLEMENTARY_AREA",
      scope,
      area
    });
  };
  var disableComplementaryArea = (scope) => ({ registry }) => {
    scope = normalizeComplementaryAreaScope(scope);
    const isComplementaryAreaVisible = registry.select(import_preferences.store).get(scope, "isComplementaryAreaVisible");
    if (isComplementaryAreaVisible) {
      registry.dispatch(import_preferences.store).set(scope, "isComplementaryAreaVisible", false);
    }
  };
  var pinItem = (scope, item) => ({ registry }) => {
    if (!item) {
      return;
    }
    scope = normalizeComplementaryAreaScope(scope);
    item = normalizeComplementaryAreaName(scope, item);
    const pinnedItems = registry.select(import_preferences.store).get(scope, "pinnedItems");
    if (pinnedItems?.[item] === true) {
      return;
    }
    registry.dispatch(import_preferences.store).set(scope, "pinnedItems", {
      ...pinnedItems,
      [item]: true
    });
  };
  var unpinItem = (scope, item) => ({ registry }) => {
    if (!item) {
      return;
    }
    scope = normalizeComplementaryAreaScope(scope);
    item = normalizeComplementaryAreaName(scope, item);
    const pinnedItems = registry.select(import_preferences.store).get(scope, "pinnedItems");
    registry.dispatch(import_preferences.store).set(scope, "pinnedItems", {
      ...pinnedItems,
      [item]: false
    });
  };
  function toggleFeature(scope, featureName) {
    return function({ registry }) {
      (0, import_deprecated2.default)(`dispatch( 'core/interface' ).toggleFeature`, {
        since: "6.0",
        alternative: `dispatch( 'core/preferences' ).toggle`
      });
      registry.dispatch(import_preferences.store).toggle(scope, featureName);
    };
  }
  function setFeatureValue(scope, featureName, value) {
    return function({ registry }) {
      (0, import_deprecated2.default)(`dispatch( 'core/interface' ).setFeatureValue`, {
        since: "6.0",
        alternative: `dispatch( 'core/preferences' ).set`
      });
      registry.dispatch(import_preferences.store).set(scope, featureName, !!value);
    };
  }
  function setFeatureDefaults(scope, defaults) {
    return function({ registry }) {
      (0, import_deprecated2.default)(`dispatch( 'core/interface' ).setFeatureDefaults`, {
        since: "6.0",
        alternative: `dispatch( 'core/preferences' ).setDefaults`
      });
      registry.dispatch(import_preferences.store).setDefaults(scope, defaults);
    };
  }
  function openModal(name2) {
    return {
      type: "OPEN_MODAL",
      name: name2
    };
  }
  function closeModal() {
    return {
      type: "CLOSE_MODAL"
    };
  }

  // packages/interface/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    getActiveComplementaryArea: () => getActiveComplementaryArea,
    isComplementaryAreaLoading: () => isComplementaryAreaLoading,
    isFeatureActive: () => isFeatureActive,
    isItemPinned: () => isItemPinned,
    isModalActive: () => isModalActive
  });
  var import_data2 = __toESM(require_data());
  var import_deprecated4 = __toESM(require_deprecated());
  var import_preferences2 = __toESM(require_preferences());
  var getActiveComplementaryArea = (0, import_data2.createRegistrySelector)(
    (select) => (state, scope) => {
      scope = normalizeComplementaryAreaScope(scope);
      const isComplementaryAreaVisible = select(import_preferences2.store).get(
        scope,
        "isComplementaryAreaVisible"
      );
      if (isComplementaryAreaVisible === void 0) {
        return void 0;
      }
      if (isComplementaryAreaVisible === false) {
        return null;
      }
      return state?.complementaryAreas?.[scope];
    }
  );
  var isComplementaryAreaLoading = (0, import_data2.createRegistrySelector)(
    (select) => (state, scope) => {
      scope = normalizeComplementaryAreaScope(scope);
      const isVisible = select(import_preferences2.store).get(
        scope,
        "isComplementaryAreaVisible"
      );
      const identifier = state?.complementaryAreas?.[scope];
      return isVisible && identifier === void 0;
    }
  );
  var isItemPinned = (0, import_data2.createRegistrySelector)(
    (select) => (state, scope, item) => {
      scope = normalizeComplementaryAreaScope(scope);
      item = normalizeComplementaryAreaName(scope, item);
      const pinnedItems = select(import_preferences2.store).get(
        scope,
        "pinnedItems"
      );
      return pinnedItems?.[item] ?? true;
    }
  );
  var isFeatureActive = (0, import_data2.createRegistrySelector)(
    (select) => (state, scope, featureName) => {
      (0, import_deprecated4.default)(
        `select( 'core/interface' ).isFeatureActive( scope, featureName )`,
        {
          since: "6.0",
          alternative: `select( 'core/preferences' ).get( scope, featureName )`
        }
      );
      return !!select(import_preferences2.store).get(scope, featureName);
    }
  );
  function isModalActive(state, modalName) {
    return state.activeModal === modalName;
  }

  // packages/interface/build-module/store/reducer.js
  var import_data3 = __toESM(require_data());
  function complementaryAreas(state = {}, action) {
    switch (action.type) {
      case "SET_DEFAULT_COMPLEMENTARY_AREA": {
        const { scope, area } = action;
        if (state[scope]) {
          return state;
        }
        return {
          ...state,
          [scope]: area
        };
      }
      case "ENABLE_COMPLEMENTARY_AREA": {
        const { scope, area } = action;
        return {
          ...state,
          [scope]: area
        };
      }
    }
    return state;
  }
  function activeModal(state = null, action) {
    switch (action.type) {
      case "OPEN_MODAL":
        return action.name;
      case "CLOSE_MODAL":
        return null;
    }
    return state;
  }
  var reducer_default2 = (0, import_data3.combineReducers)({
    complementaryAreas,
    activeModal
  });

  // packages/interface/build-module/store/constants.js
  var STORE_NAME = "core/interface";

  // packages/interface/build-module/store/index.js
  var store = (0, import_data4.createReduxStore)(STORE_NAME, {
    reducer: reducer_default2,
    actions: actions_exports,
    selectors: selectors_exports
  });
  (0, import_data4.register)(store);

  // packages/interface/build-module/components/complementary-area-toggle/index.js
  var import_jsx_runtime14 = __toESM(require_jsx_runtime());
  function roleSupportsCheckedState(role) {
    return [
      "checkbox",
      "option",
      "radio",
      "switch",
      "menuitemcheckbox",
      "menuitemradio",
      "treeitem"
    ].includes(role);
  }
  function ComplementaryAreaToggle({
    as = import_components.Button,
    scope,
    identifier: identifierProp,
    icon: iconProp,
    selectedIcon,
    name: name2,
    shortcut,
    ...props
  }) {
    const ComponentToUse = as;
    const context = (0, import_plugins.usePluginContext)();
    const icon = iconProp || context.icon;
    const identifier = identifierProp || `${context.name}/${name2}`;
    const isSelected = (0, import_data5.useSelect)(
      (select) => select(store).getActiveComplementaryArea(scope) === identifier,
      [identifier, scope]
    );
    const { enableComplementaryArea: enableComplementaryArea2, disableComplementaryArea: disableComplementaryArea2 } = (0, import_data5.useDispatch)(store);
    return /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
      ComponentToUse,
      {
        icon: selectedIcon && isSelected ? selectedIcon : icon,
        "aria-controls": identifier.replace("/", ":"),
        "aria-checked": roleSupportsCheckedState(props.role) ? isSelected : void 0,
        onClick: () => {
          if (isSelected) {
            disableComplementaryArea2(scope);
          } else {
            enableComplementaryArea2(scope, identifier);
          }
        },
        shortcut,
        ...props
      }
    );
  }

  // packages/interface/build-module/components/complementary-area-header/index.js
  var import_jsx_runtime15 = __toESM(require_jsx_runtime());
  var ComplementaryAreaHeader = ({
    children,
    className,
    toggleButtonProps
  }) => {
    const toggleButton = /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(ComplementaryAreaToggle, { icon: close_small_default, ...toggleButtonProps });
    return /* @__PURE__ */ (0, import_jsx_runtime15.jsxs)(
      "div",
      {
        className: clsx_default(
          "components-panel__header",
          "interface-complementary-area-header",
          className
        ),
        tabIndex: -1,
        children: [
          children,
          toggleButton
        ]
      }
    );
  };
  var complementary_area_header_default = ComplementaryAreaHeader;

  // packages/interface/build-module/components/complementary-area-more-menu-item/index.js
  var import_components3 = __toESM(require_components());

  // packages/interface/build-module/components/action-item/index.js
  var import_components2 = __toESM(require_components());
  var import_element = __toESM(require_element());
  var import_jsx_runtime16 = __toESM(require_jsx_runtime());
  var noop = () => {
  };
  function ActionItemSlot({
    name: name2,
    as: Component2 = import_components2.MenuGroup,
    fillProps = {},
    bubblesVirtually,
    ...props
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
      import_components2.Slot,
      {
        name: name2,
        bubblesVirtually,
        fillProps,
        children: (fills) => {
          if (!import_element.Children.toArray(fills).length) {
            return null;
          }
          const initializedByPlugins = [];
          import_element.Children.forEach(
            fills,
            ({
              props: { __unstableExplicitMenuItem, __unstableTarget }
            }) => {
              if (__unstableTarget && __unstableExplicitMenuItem) {
                initializedByPlugins.push(__unstableTarget);
              }
            }
          );
          const children = import_element.Children.map(fills, (child) => {
            if (!child.props.__unstableExplicitMenuItem && initializedByPlugins.includes(
              child.props.__unstableTarget
            )) {
              return null;
            }
            return child;
          });
          return /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(Component2, { ...props, children });
        }
      }
    );
  }
  function ActionItem({ name: name2, as: Component2 = import_components2.Button, onClick, ...props }) {
    return /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_components2.Fill, { name: name2, children: ({ onClick: fpOnClick }) => {
      return /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
        Component2,
        {
          onClick: onClick || fpOnClick ? (...args) => {
            (onClick || noop)(...args);
            (fpOnClick || noop)(...args);
          } : void 0,
          ...props
        }
      );
    } });
  }
  ActionItem.Slot = ActionItemSlot;
  var action_item_default = ActionItem;

  // packages/interface/build-module/components/complementary-area-more-menu-item/index.js
  var import_jsx_runtime17 = __toESM(require_jsx_runtime());
  var PluginsMenuItem = ({
    // Menu item is marked with unstable prop for backward compatibility.
    // They are removed so they don't leak to DOM elements.
    // @see https://github.com/WordPress/gutenberg/issues/14457
    __unstableExplicitMenuItem,
    __unstableTarget,
    ...restProps
  }) => /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_components3.MenuItem, { ...restProps });
  function ComplementaryAreaMoreMenuItem({
    scope,
    target,
    __unstableExplicitMenuItem,
    ...props
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
      ComplementaryAreaToggle,
      {
        as: (toggleProps) => {
          return /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
            action_item_default,
            {
              __unstableExplicitMenuItem,
              __unstableTarget: `${scope}/${target}`,
              as: PluginsMenuItem,
              name: `${scope}/plugin-more-menu`,
              ...toggleProps
            }
          );
        },
        role: "menuitemcheckbox",
        selectedIcon: check_default,
        name: target,
        scope,
        ...props
      }
    );
  }

  // packages/interface/build-module/components/pinned-items/index.js
  var import_components4 = __toESM(require_components());
  var import_jsx_runtime18 = __toESM(require_jsx_runtime());
  function PinnedItems({ scope, ...props }) {
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(import_components4.Fill, { name: `PinnedItems/${scope}`, ...props });
  }
  function PinnedItemsSlot({ scope, className, ...props }) {
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(import_components4.Slot, { name: `PinnedItems/${scope}`, ...props, children: (fills) => fills?.length > 0 && /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
      "div",
      {
        className: clsx_default(
          className,
          "interface-pinned-items"
        ),
        children: fills
      }
    ) });
  }
  PinnedItems.Slot = PinnedItemsSlot;
  var pinned_items_default = PinnedItems;

  // packages/interface/build-module/components/complementary-area/index.js
  var import_jsx_runtime19 = __toESM(require_jsx_runtime());
  var ANIMATION_DURATION = 0.3;
  function ComplementaryAreaSlot({ scope, ...props }) {
    return /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(import_components5.Slot, { name: `ComplementaryArea/${scope}`, ...props });
  }
  var SIDEBAR_WIDTH = 280;
  var variants = {
    open: { width: SIDEBAR_WIDTH },
    closed: { width: 0 },
    mobileOpen: { width: "100vw" }
  };
  function ComplementaryAreaFill({
    activeArea,
    isActive,
    scope,
    children,
    className,
    id
  }) {
    const disableMotion = (0, import_compose.useReducedMotion)();
    const isMobileViewport = (0, import_compose.useViewportMatch)("medium", "<");
    const previousActiveArea = (0, import_compose.usePrevious)(activeArea);
    const previousIsActive = (0, import_compose.usePrevious)(isActive);
    const [, setState] = (0, import_element2.useState)({});
    (0, import_element2.useEffect)(() => {
      setState({});
    }, [isActive]);
    const transition = {
      type: "tween",
      duration: disableMotion || isMobileViewport || !!previousActiveArea && !!activeArea && activeArea !== previousActiveArea ? 0 : ANIMATION_DURATION,
      ease: [0.6, 0, 0.4, 1]
    };
    return /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(import_components5.Fill, { name: `ComplementaryArea/${scope}`, children: /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(import_components5.__unstableAnimatePresence, { initial: false, children: (previousIsActive || isActive) && /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
      import_components5.__unstableMotion.div,
      {
        variants,
        initial: "closed",
        animate: isMobileViewport ? "mobileOpen" : "open",
        exit: "closed",
        transition,
        className: "interface-complementary-area__fill",
        children: /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
          "div",
          {
            id,
            className,
            style: {
              width: isMobileViewport ? "100vw" : SIDEBAR_WIDTH
            },
            children
          }
        )
      }
    ) }) });
  }
  function useAdjustComplementaryListener(scope, identifier, activeArea, isActive, isSmall) {
    const previousIsSmallRef = (0, import_element2.useRef)(false);
    const shouldOpenWhenNotSmallRef = (0, import_element2.useRef)(false);
    const { enableComplementaryArea: enableComplementaryArea2, disableComplementaryArea: disableComplementaryArea2 } = (0, import_data6.useDispatch)(store);
    (0, import_element2.useEffect)(() => {
      if (isActive && isSmall && !previousIsSmallRef.current) {
        disableComplementaryArea2(scope);
        shouldOpenWhenNotSmallRef.current = true;
      } else if (
        // If there is a flag indicating the complementary area should be
        // enabled when we go from small to big window size and we are going
        // from a small to big window size.
        shouldOpenWhenNotSmallRef.current && !isSmall && previousIsSmallRef.current
      ) {
        shouldOpenWhenNotSmallRef.current = false;
        enableComplementaryArea2(scope, identifier);
      } else if (
        // If the flag is indicating the current complementary should be
        // reopened but another complementary area becomes active, remove
        // the flag.
        shouldOpenWhenNotSmallRef.current && activeArea && activeArea !== identifier
      ) {
        shouldOpenWhenNotSmallRef.current = false;
      }
      if (isSmall !== previousIsSmallRef.current) {
        previousIsSmallRef.current = isSmall;
      }
    }, [
      isActive,
      isSmall,
      scope,
      identifier,
      activeArea,
      disableComplementaryArea2,
      enableComplementaryArea2
    ]);
  }
  function ComplementaryArea({
    children,
    className,
    closeLabel = (0, import_i18n.__)("Close plugin"),
    identifier: identifierProp,
    header,
    headerClassName,
    icon: iconProp,
    isPinnable = true,
    panelClassName,
    scope,
    name: name2,
    title,
    toggleShortcut,
    isActiveByDefault
  }) {
    const context = (0, import_plugins2.usePluginContext)();
    const icon = iconProp || context.icon;
    const identifier = identifierProp || `${context.name}/${name2}`;
    const [isReady, setIsReady] = (0, import_element2.useState)(false);
    const {
      isLoading,
      isActive,
      isPinned,
      activeArea,
      isSmall,
      isLarge,
      showIconLabels
    } = (0, import_data6.useSelect)(
      (select) => {
        const {
          getActiveComplementaryArea: getActiveComplementaryArea2,
          isComplementaryAreaLoading: isComplementaryAreaLoading2,
          isItemPinned: isItemPinned2
        } = select(store);
        const { get } = select(import_preferences3.store);
        const _activeArea = getActiveComplementaryArea2(scope);
        return {
          isLoading: isComplementaryAreaLoading2(scope),
          isActive: _activeArea === identifier,
          isPinned: isItemPinned2(scope, identifier),
          activeArea: _activeArea,
          isSmall: select(import_viewport.store).isViewportMatch("< medium"),
          isLarge: select(import_viewport.store).isViewportMatch("large"),
          showIconLabels: get("core", "showIconLabels")
        };
      },
      [identifier, scope]
    );
    const isMobileViewport = (0, import_compose.useViewportMatch)("medium", "<");
    useAdjustComplementaryListener(
      scope,
      identifier,
      activeArea,
      isActive,
      isSmall
    );
    const {
      enableComplementaryArea: enableComplementaryArea2,
      disableComplementaryArea: disableComplementaryArea2,
      pinItem: pinItem2,
      unpinItem: unpinItem2
    } = (0, import_data6.useDispatch)(store);
    (0, import_element2.useEffect)(() => {
      if (isActiveByDefault && activeArea === void 0 && !isSmall) {
        enableComplementaryArea2(scope, identifier);
      } else if (activeArea === void 0 && isSmall) {
        disableComplementaryArea2(scope, identifier);
      }
      setIsReady(true);
    }, [
      activeArea,
      isActiveByDefault,
      scope,
      identifier,
      isSmall,
      enableComplementaryArea2,
      disableComplementaryArea2
    ]);
    if (!isReady) {
      return;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime19.jsxs)(import_jsx_runtime19.Fragment, { children: [
      isPinnable && /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(pinned_items_default, { scope, children: isPinned && /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
        ComplementaryAreaToggle,
        {
          scope,
          identifier,
          isPressed: isActive && (!showIconLabels || isLarge),
          "aria-expanded": isActive,
          "aria-disabled": isLoading,
          label: title,
          icon: showIconLabels ? check_default : icon,
          showTooltip: !showIconLabels,
          variant: showIconLabels ? "tertiary" : void 0,
          size: "compact",
          shortcut: toggleShortcut
        }
      ) }),
      name2 && isPinnable && /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
        ComplementaryAreaMoreMenuItem,
        {
          target: name2,
          scope,
          icon,
          identifier,
          children: title
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime19.jsxs)(
        ComplementaryAreaFill,
        {
          activeArea,
          isActive,
          className: clsx_default("interface-complementary-area", className),
          scope,
          id: identifier.replace("/", ":"),
          children: [
            /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
              complementary_area_header_default,
              {
                className: headerClassName,
                closeLabel,
                onClose: () => disableComplementaryArea2(scope),
                toggleButtonProps: {
                  label: closeLabel,
                  size: "compact",
                  shortcut: toggleShortcut,
                  scope,
                  identifier
                },
                children: header || /* @__PURE__ */ (0, import_jsx_runtime19.jsxs)(import_jsx_runtime19.Fragment, { children: [
                  /* @__PURE__ */ (0, import_jsx_runtime19.jsx)("h2", { className: "interface-complementary-area-header__title", children: title }),
                  isPinnable && !isMobileViewport && /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
                    import_components5.Button,
                    {
                      className: "interface-complementary-area__pin-unpin-item",
                      icon: isPinned ? star_filled_default : star_empty_default,
                      label: isPinned ? (0, import_i18n.__)("Unpin from toolbar") : (0, import_i18n.__)("Pin to toolbar"),
                      onClick: () => (isPinned ? unpinItem2 : pinItem2)(
                        scope,
                        identifier
                      ),
                      isPressed: isPinned,
                      "aria-expanded": isPinned,
                      size: "compact"
                    }
                  )
                ] })
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(import_components5.Panel, { className: panelClassName, children })
          ]
        }
      )
    ] });
  }
  ComplementaryArea.Slot = ComplementaryAreaSlot;
  var complementary_area_default = ComplementaryArea;

  // packages/admin-ui/build-module/navigable-region/index.js
  var import_element3 = __toESM(require_element());
  var import_jsx_runtime20 = __toESM(require_jsx_runtime());
  var NavigableRegion = (0, import_element3.forwardRef)(
    ({ children, className, ariaLabel, as: Tag = "div", ...props }, ref) => {
      return /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
        Tag,
        {
          ref,
          className: clsx_default("admin-ui-navigable-region", className),
          "aria-label": ariaLabel,
          role: "region",
          tabIndex: "-1",
          ...props,
          children
        }
      );
    }
  );
  NavigableRegion.displayName = "NavigableRegion";
  var navigable_region_default = NavigableRegion;

  // packages/interface/build-module/components/interface-skeleton/index.js
  var import_element4 = __toESM(require_element());
  var import_components6 = __toESM(require_components());
  var import_i18n2 = __toESM(require_i18n());
  var import_compose2 = __toESM(require_compose());
  var import_jsx_runtime21 = __toESM(require_jsx_runtime());
  var ANIMATION_DURATION2 = 0.25;
  var commonTransition = {
    type: "tween",
    duration: ANIMATION_DURATION2,
    ease: [0.6, 0, 0.4, 1]
  };
  function useHTMLClass(className) {
    (0, import_element4.useEffect)(() => {
      const element = document && document.querySelector(`html:not(.${className})`);
      if (!element) {
        return;
      }
      element.classList.toggle(className);
      return () => {
        element.classList.toggle(className);
      };
    }, [className]);
  }
  var headerVariants = {
    hidden: { opacity: 1, marginTop: -60 },
    visible: { opacity: 1, marginTop: 0 },
    distractionFreeHover: {
      opacity: 1,
      marginTop: 0,
      transition: {
        ...commonTransition,
        delay: 0.2,
        delayChildren: 0.2
      }
    },
    distractionFreeHidden: {
      opacity: 0,
      marginTop: -60
    },
    distractionFreeDisabled: {
      opacity: 0,
      marginTop: 0,
      transition: {
        ...commonTransition,
        delay: 0.8,
        delayChildren: 0.8
      }
    }
  };
  function InterfaceSkeleton({
    isDistractionFree,
    footer,
    header,
    editorNotices,
    sidebar,
    secondarySidebar,
    content,
    actions,
    labels,
    className
  }, ref) {
    const [secondarySidebarResizeListener, secondarySidebarSize] = (0, import_compose2.useResizeObserver)();
    const isMobileViewport = (0, import_compose2.useViewportMatch)("medium", "<");
    const disableMotion = (0, import_compose2.useReducedMotion)();
    const defaultTransition = {
      type: "tween",
      duration: disableMotion ? 0 : ANIMATION_DURATION2,
      ease: [0.6, 0, 0.4, 1]
    };
    useHTMLClass("interface-interface-skeleton__html-container");
    const defaultLabels = {
      /* translators: accessibility text for the top bar landmark region. */
      header: (0, import_i18n2._x)("Header", "header landmark area"),
      /* translators: accessibility text for the content landmark region. */
      body: (0, import_i18n2.__)("Content"),
      /* translators: accessibility text for the secondary sidebar landmark region. */
      secondarySidebar: (0, import_i18n2.__)("Block Library"),
      /* translators: accessibility text for the settings landmark region. */
      sidebar: (0, import_i18n2._x)("Settings", "settings landmark area"),
      /* translators: accessibility text for the publish landmark region. */
      actions: (0, import_i18n2.__)("Publish"),
      /* translators: accessibility text for the footer landmark region. */
      footer: (0, import_i18n2.__)("Footer")
    };
    const mergedLabels = { ...defaultLabels, ...labels };
    return /* @__PURE__ */ (0, import_jsx_runtime21.jsxs)(
      "div",
      {
        ref,
        className: clsx_default(
          className,
          "interface-interface-skeleton",
          !!footer && "has-footer"
        ),
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime21.jsxs)("div", { className: "interface-interface-skeleton__editor", children: [
            /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(import_components6.__unstableAnimatePresence, { initial: false, children: !!header && /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
              navigable_region_default,
              {
                as: import_components6.__unstableMotion.div,
                className: "interface-interface-skeleton__header",
                "aria-label": mergedLabels.header,
                initial: isDistractionFree && !isMobileViewport ? "distractionFreeHidden" : "hidden",
                whileHover: isDistractionFree && !isMobileViewport ? "distractionFreeHover" : "visible",
                animate: isDistractionFree && !isMobileViewport ? "distractionFreeDisabled" : "visible",
                exit: isDistractionFree && !isMobileViewport ? "distractionFreeHidden" : "hidden",
                variants: headerVariants,
                transition: defaultTransition,
                children: header
              }
            ) }),
            isDistractionFree && /* @__PURE__ */ (0, import_jsx_runtime21.jsx)("div", { className: "interface-interface-skeleton__header", children: editorNotices }),
            /* @__PURE__ */ (0, import_jsx_runtime21.jsxs)("div", { className: "interface-interface-skeleton__body", children: [
              /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(import_components6.__unstableAnimatePresence, { initial: false, children: !!secondarySidebar && /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
                navigable_region_default,
                {
                  className: "interface-interface-skeleton__secondary-sidebar",
                  ariaLabel: mergedLabels.secondarySidebar,
                  as: import_components6.__unstableMotion.div,
                  initial: "closed",
                  animate: "open",
                  exit: "closed",
                  variants: {
                    open: { width: secondarySidebarSize.width },
                    closed: { width: 0 }
                  },
                  transition: defaultTransition,
                  children: /* @__PURE__ */ (0, import_jsx_runtime21.jsxs)(
                    import_components6.__unstableMotion.div,
                    {
                      style: {
                        position: "absolute",
                        width: isMobileViewport ? "100vw" : "fit-content",
                        height: "100%",
                        left: 0
                      },
                      variants: {
                        open: { x: 0 },
                        closed: { x: "-100%" }
                      },
                      transition: defaultTransition,
                      children: [
                        secondarySidebarResizeListener,
                        secondarySidebar
                      ]
                    }
                  )
                }
              ) }),
              /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
                navigable_region_default,
                {
                  className: "interface-interface-skeleton__content",
                  ariaLabel: mergedLabels.body,
                  children: content
                }
              ),
              !!sidebar && /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
                navigable_region_default,
                {
                  className: "interface-interface-skeleton__sidebar",
                  ariaLabel: mergedLabels.sidebar,
                  children: sidebar
                }
              ),
              !!actions && /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
                navigable_region_default,
                {
                  className: "interface-interface-skeleton__actions",
                  ariaLabel: mergedLabels.actions,
                  children: actions
                }
              )
            ] })
          ] }),
          !!footer && /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
            navigable_region_default,
            {
              className: "interface-interface-skeleton__footer",
              ariaLabel: mergedLabels.footer,
              children: footer
            }
          )
        ]
      }
    );
  }
  var interface_skeleton_default = (0, import_element4.forwardRef)(InterfaceSkeleton);

  // packages/edit-widgets/build-module/store/actions.js
  var import_widgets2 = __toESM(require_widgets());
  var import_core_data = __toESM(require_core_data());
  var import_block_editor = __toESM(require_block_editor());

  // packages/edit-widgets/build-module/store/transformers.js
  var import_blocks = __toESM(require_blocks());
  var import_widgets = __toESM(require_widgets());
  function transformWidgetToBlock(widget) {
    if (widget.id_base === "block") {
      const parsedBlocks = (0, import_blocks.parse)(widget.instance.raw.content, {
        __unstableSkipAutop: true
      });
      if (!parsedBlocks.length) {
        return (0, import_widgets.addWidgetIdToBlock)(
          (0, import_blocks.createBlock)("core/paragraph", {}, []),
          widget.id
        );
      }
      return (0, import_widgets.addWidgetIdToBlock)(parsedBlocks[0], widget.id);
    }
    let attributes;
    if (widget._embedded.about[0].is_multi) {
      attributes = {
        idBase: widget.id_base,
        instance: widget.instance
      };
    } else {
      attributes = {
        id: widget.id
      };
    }
    return (0, import_widgets.addWidgetIdToBlock)(
      (0, import_blocks.createBlock)("core/legacy-widget", attributes, []),
      widget.id
    );
  }
  function transformBlockToWidget(block, relatedWidget = {}) {
    let widget;
    const isValidLegacyWidgetBlock = block.name === "core/legacy-widget" && (block.attributes.id || block.attributes.instance);
    if (isValidLegacyWidgetBlock) {
      widget = {
        ...relatedWidget,
        id: block.attributes.id ?? relatedWidget.id,
        id_base: block.attributes.idBase ?? relatedWidget.id_base,
        instance: block.attributes.instance ?? relatedWidget.instance
      };
    } else {
      widget = {
        ...relatedWidget,
        id_base: "block",
        instance: {
          raw: {
            content: (0, import_blocks.serialize)(block)
          }
        }
      };
    }
    delete widget.rendered;
    delete widget.rendered_form;
    return widget;
  }

  // packages/edit-widgets/build-module/store/utils.js
  var KIND = "root";
  var WIDGET_AREA_ENTITY_TYPE = "sidebar";
  var POST_TYPE = "postType";
  var buildWidgetAreaPostId = (widgetAreaId) => `widget-area-${widgetAreaId}`;
  var buildWidgetAreasPostId = () => `widget-areas`;
  function buildWidgetAreasQuery() {
    return {
      per_page: -1
    };
  }
  function buildWidgetsQuery() {
    return {
      per_page: -1,
      _embed: "about"
    };
  }
  var createStubPost = (id, blocks) => ({
    id,
    slug: id,
    status: "draft",
    type: "page",
    blocks,
    meta: {
      widgetAreaId: id
    }
  });

  // packages/edit-widgets/build-module/store/constants.js
  var STORE_NAME2 = "core/edit-widgets";

  // packages/edit-widgets/build-module/store/actions.js
  var persistStubPost = (id, blocks) => ({ registry }) => {
    const stubPost = createStubPost(id, blocks);
    registry.dispatch(import_core_data.store).receiveEntityRecords(
      KIND,
      POST_TYPE,
      stubPost,
      { id: stubPost.id },
      false
    );
    return stubPost;
  };
  var saveEditedWidgetAreas = () => async ({ select, dispatch: dispatch2, registry }) => {
    const editedWidgetAreas = select.getEditedWidgetAreas();
    if (!editedWidgetAreas?.length) {
      return;
    }
    try {
      await dispatch2.saveWidgetAreas(editedWidgetAreas);
      registry.dispatch(import_notices.store).createSuccessNotice((0, import_i18n3.__)("Widgets saved."), {
        type: "snackbar"
      });
    } catch (e) {
      registry.dispatch(import_notices.store).createErrorNotice(
        /* translators: %s: The error message. */
        (0, import_i18n3.sprintf)((0, import_i18n3.__)("There was an error. %s"), e.message),
        {
          type: "snackbar"
        }
      );
    }
  };
  var saveWidgetAreas = (widgetAreas) => async ({ dispatch: dispatch2, registry }) => {
    try {
      for (const widgetArea of widgetAreas) {
        await dispatch2.saveWidgetArea(widgetArea.id);
      }
    } finally {
      await registry.dispatch(import_core_data.store).finishResolution(
        "getEntityRecord",
        KIND,
        WIDGET_AREA_ENTITY_TYPE,
        buildWidgetAreasQuery()
      );
    }
  };
  var saveWidgetArea = (widgetAreaId) => async ({ dispatch: dispatch2, select, registry }) => {
    const widgets = select.getWidgets();
    const post = registry.select(import_core_data.store).getEditedEntityRecord(
      KIND,
      POST_TYPE,
      buildWidgetAreaPostId(widgetAreaId)
    );
    const areaWidgets = Object.values(widgets).filter(
      ({ sidebar }) => sidebar === widgetAreaId
    );
    const usedReferenceWidgets = [];
    const widgetsBlocks = post.blocks.filter((block) => {
      const { id } = block.attributes;
      if (block.name === "core/legacy-widget" && id) {
        if (usedReferenceWidgets.includes(id)) {
          return false;
        }
        usedReferenceWidgets.push(id);
      }
      return true;
    });
    const deletedWidgets = [];
    for (const widget of areaWidgets) {
      const widgetsNewArea = select.getWidgetAreaForWidgetId(widget.id);
      if (!widgetsNewArea) {
        deletedWidgets.push(widget);
      }
    }
    const batchMeta = [];
    const batchTasks = [];
    const sidebarWidgetsIds = [];
    for (let i = 0; i < widgetsBlocks.length; i++) {
      const block = widgetsBlocks[i];
      const widgetId = (0, import_widgets2.getWidgetIdFromBlock)(block);
      const oldWidget = widgets[widgetId];
      const widget = transformBlockToWidget(block, oldWidget);
      sidebarWidgetsIds.push(widgetId);
      if (oldWidget) {
        registry.dispatch(import_core_data.store).editEntityRecord(
          "root",
          "widget",
          widgetId,
          {
            ...widget,
            sidebar: widgetAreaId
          },
          { undoIgnore: true }
        );
        const hasEdits = registry.select(import_core_data.store).hasEditsForEntityRecord("root", "widget", widgetId);
        if (!hasEdits) {
          continue;
        }
        batchTasks.push(
          ({ saveEditedEntityRecord }) => saveEditedEntityRecord("root", "widget", widgetId)
        );
      } else {
        batchTasks.push(
          ({ saveEntityRecord }) => saveEntityRecord("root", "widget", {
            ...widget,
            sidebar: widgetAreaId
          })
        );
      }
      batchMeta.push({
        block,
        position: i,
        clientId: block.clientId
      });
    }
    for (const widget of deletedWidgets) {
      batchTasks.push(
        ({ deleteEntityRecord }) => deleteEntityRecord("root", "widget", widget.id, {
          force: true
        })
      );
    }
    const records = await registry.dispatch(import_core_data.store).__experimentalBatch(batchTasks);
    const preservedRecords = records.filter(
      (record) => !record.hasOwnProperty("deleted")
    );
    const failedWidgetNames = [];
    for (let i = 0; i < preservedRecords.length; i++) {
      const widget = preservedRecords[i];
      const { block, position } = batchMeta[i];
      post.blocks[position].attributes.__internalWidgetId = widget.id;
      const error = registry.select(import_core_data.store).getLastEntitySaveError("root", "widget", widget.id);
      if (error) {
        failedWidgetNames.push(block.attributes?.name || block?.name);
      }
      if (!sidebarWidgetsIds[position]) {
        sidebarWidgetsIds[position] = widget.id;
      }
    }
    if (failedWidgetNames.length) {
      throw new Error(
        (0, import_i18n3.sprintf)(
          /* translators: %s: List of widget names */
          (0, import_i18n3.__)("Could not save the following widgets: %s."),
          failedWidgetNames.join(", ")
        )
      );
    }
    registry.dispatch(import_core_data.store).editEntityRecord(
      KIND,
      WIDGET_AREA_ENTITY_TYPE,
      widgetAreaId,
      {
        widgets: sidebarWidgetsIds
      },
      { undoIgnore: true }
    );
    dispatch2(trySaveWidgetArea(widgetAreaId));
    registry.dispatch(import_core_data.store).receiveEntityRecords(KIND, POST_TYPE, post, void 0);
  };
  var trySaveWidgetArea = (widgetAreaId) => ({ registry }) => {
    registry.dispatch(import_core_data.store).saveEditedEntityRecord(
      KIND,
      WIDGET_AREA_ENTITY_TYPE,
      widgetAreaId,
      {
        throwOnError: true
      }
    );
  };
  function setWidgetIdForClientId(clientId, widgetId) {
    return {
      type: "SET_WIDGET_ID_FOR_CLIENT_ID",
      clientId,
      widgetId
    };
  }
  function setWidgetAreasOpenState(widgetAreasOpenState2) {
    return {
      type: "SET_WIDGET_AREAS_OPEN_STATE",
      widgetAreasOpenState: widgetAreasOpenState2
    };
  }
  function setIsWidgetAreaOpen(clientId, isOpen) {
    return {
      type: "SET_IS_WIDGET_AREA_OPEN",
      clientId,
      isOpen
    };
  }
  function setIsInserterOpened(value) {
    return {
      type: "SET_IS_INSERTER_OPENED",
      value
    };
  }
  function setIsListViewOpened(isOpen) {
    return {
      type: "SET_IS_LIST_VIEW_OPENED",
      isOpen
    };
  }
  var closeGeneralSidebar = () => ({ registry }) => {
    registry.dispatch(store).disableComplementaryArea(STORE_NAME2);
  };
  var moveBlockToWidgetArea = (clientId, widgetAreaId) => async ({ dispatch: dispatch2, select, registry }) => {
    const sourceRootClientId = registry.select(import_block_editor.store).getBlockRootClientId(clientId);
    const widgetAreas = registry.select(import_block_editor.store).getBlocks();
    const destinationWidgetAreaBlock = widgetAreas.find(
      ({ attributes }) => attributes.id === widgetAreaId
    );
    const destinationRootClientId = destinationWidgetAreaBlock.clientId;
    const destinationInnerBlocksClientIds = registry.select(import_block_editor.store).getBlockOrder(destinationRootClientId);
    const destinationIndex = destinationInnerBlocksClientIds.length;
    const isDestinationWidgetAreaOpen = select.getIsWidgetAreaOpen(
      destinationRootClientId
    );
    if (!isDestinationWidgetAreaOpen) {
      dispatch2.setIsWidgetAreaOpen(destinationRootClientId, true);
    }
    registry.dispatch(import_block_editor.store).moveBlocksToPosition(
      [clientId],
      sourceRootClientId,
      destinationRootClientId,
      destinationIndex
    );
  };
  function unlockWidgetSaving(lockName) {
    return {
      type: "UNLOCK_WIDGET_SAVING",
      lockName
    };
  }
  function lockWidgetSaving(lockName) {
    return {
      type: "LOCK_WIDGET_SAVING",
      lockName
    };
  }

  // packages/edit-widgets/build-module/store/resolvers.js
  var getWidgetAreas = () => async ({ dispatch: dispatch2, registry }) => {
    const query = buildWidgetAreasQuery();
    const widgetAreas = await registry.resolveSelect(import_core_data2.store).getEntityRecords(KIND, WIDGET_AREA_ENTITY_TYPE, query);
    const widgetAreaBlocks = [];
    const sortedWidgetAreas = widgetAreas.sort((a, b) => {
      if (a.id === "wp_inactive_widgets") {
        return 1;
      }
      if (b.id === "wp_inactive_widgets") {
        return -1;
      }
      return 0;
    });
    for (const widgetArea of sortedWidgetAreas) {
      widgetAreaBlocks.push(
        (0, import_blocks2.createBlock)("core/widget-area", {
          id: widgetArea.id,
          name: widgetArea.name
        })
      );
      if (!widgetArea.widgets.length) {
        dispatch2(
          persistStubPost(
            buildWidgetAreaPostId(widgetArea.id),
            []
          )
        );
      }
    }
    const widgetAreasOpenState2 = {};
    widgetAreaBlocks.forEach((widgetAreaBlock, index) => {
      widgetAreasOpenState2[widgetAreaBlock.clientId] = index === 0;
    });
    dispatch2(setWidgetAreasOpenState(widgetAreasOpenState2));
    dispatch2(
      persistStubPost(buildWidgetAreasPostId(), widgetAreaBlocks)
    );
  };
  var getWidgets = () => async ({ dispatch: dispatch2, registry }) => {
    const query = buildWidgetsQuery();
    const widgets = await registry.resolveSelect(import_core_data2.store).getEntityRecords("root", "widget", query);
    const groupedBySidebar = {};
    for (const widget of widgets) {
      const block = transformWidgetToBlock(widget);
      groupedBySidebar[widget.sidebar] = groupedBySidebar[widget.sidebar] || [];
      groupedBySidebar[widget.sidebar].push(block);
    }
    for (const sidebarId in groupedBySidebar) {
      if (groupedBySidebar.hasOwnProperty(sidebarId)) {
        dispatch2(
          persistStubPost(
            buildWidgetAreaPostId(sidebarId),
            groupedBySidebar[sidebarId]
          )
        );
      }
    }
  };

  // packages/edit-widgets/build-module/store/selectors.js
  var selectors_exports2 = {};
  __export(selectors_exports2, {
    __experimentalGetInsertionPoint: () => __experimentalGetInsertionPoint,
    canInsertBlockInWidgetArea: () => canInsertBlockInWidgetArea,
    getEditedWidgetAreas: () => getEditedWidgetAreas,
    getIsWidgetAreaOpen: () => getIsWidgetAreaOpen,
    getParentWidgetAreaBlock: () => getParentWidgetAreaBlock,
    getReferenceWidgetBlocks: () => getReferenceWidgetBlocks,
    getWidget: () => getWidget,
    getWidgetAreaForWidgetId: () => getWidgetAreaForWidgetId,
    getWidgetAreas: () => getWidgetAreas2,
    getWidgets: () => getWidgets2,
    isInserterOpened: () => isInserterOpened,
    isListViewOpened: () => isListViewOpened,
    isSavingWidgetAreas: () => isSavingWidgetAreas,
    isWidgetSavingLocked: () => isWidgetSavingLocked
  });
  var import_data7 = __toESM(require_data());
  var import_widgets3 = __toESM(require_widgets());
  var import_core_data3 = __toESM(require_core_data());
  var import_block_editor2 = __toESM(require_block_editor());
  var EMPTY_INSERTION_POINT = {
    rootClientId: void 0,
    insertionIndex: void 0
  };
  var getWidgets2 = (0, import_data7.createRegistrySelector)(
    (select) => (0, import_data7.createSelector)(
      () => {
        const widgets = select(import_core_data3.store).getEntityRecords(
          "root",
          "widget",
          buildWidgetsQuery()
        );
        return (
          // Key widgets by their ID.
          widgets?.reduce(
            (allWidgets, widget) => ({
              ...allWidgets,
              [widget.id]: widget
            }),
            {}
          ) ?? {}
        );
      },
      () => [
        select(import_core_data3.store).getEntityRecords(
          "root",
          "widget",
          buildWidgetsQuery()
        )
      ]
    )
  );
  var getWidget = (0, import_data7.createRegistrySelector)(
    (select) => (state, id) => {
      const widgets = select(STORE_NAME2).getWidgets();
      return widgets[id];
    }
  );
  var getWidgetAreas2 = (0, import_data7.createRegistrySelector)((select) => () => {
    const query = buildWidgetAreasQuery();
    return select(import_core_data3.store).getEntityRecords(
      KIND,
      WIDGET_AREA_ENTITY_TYPE,
      query
    );
  });
  var getWidgetAreaForWidgetId = (0, import_data7.createRegistrySelector)(
    (select) => (state, widgetId) => {
      const widgetAreas = select(STORE_NAME2).getWidgetAreas();
      return widgetAreas.find((widgetArea) => {
        const post = select(import_core_data3.store).getEditedEntityRecord(
          KIND,
          POST_TYPE,
          buildWidgetAreaPostId(widgetArea.id)
        );
        const blockWidgetIds = post.blocks.map(
          (block) => (0, import_widgets3.getWidgetIdFromBlock)(block)
        );
        return blockWidgetIds.includes(widgetId);
      });
    }
  );
  var getParentWidgetAreaBlock = (0, import_data7.createRegistrySelector)(
    (select) => (state, clientId) => {
      const { getBlock, getBlockName, getBlockParents } = select(import_block_editor2.store);
      const blockParents = getBlockParents(clientId);
      const widgetAreaClientId = blockParents.find(
        (parentClientId) => getBlockName(parentClientId) === "core/widget-area"
      );
      return getBlock(widgetAreaClientId);
    }
  );
  var getEditedWidgetAreas = (0, import_data7.createRegistrySelector)(
    (select) => (state, ids) => {
      let widgetAreas = select(STORE_NAME2).getWidgetAreas();
      if (!widgetAreas) {
        return [];
      }
      if (ids) {
        widgetAreas = widgetAreas.filter(
          ({ id }) => ids.includes(id)
        );
      }
      return widgetAreas.filter(
        ({ id }) => select(import_core_data3.store).hasEditsForEntityRecord(
          KIND,
          POST_TYPE,
          buildWidgetAreaPostId(id)
        )
      ).map(
        ({ id }) => select(import_core_data3.store).getEditedEntityRecord(
          KIND,
          WIDGET_AREA_ENTITY_TYPE,
          id
        )
      );
    }
  );
  var getReferenceWidgetBlocks = (0, import_data7.createRegistrySelector)(
    (select) => (state, referenceWidgetName = null) => {
      const results = [];
      const widgetAreas = select(STORE_NAME2).getWidgetAreas();
      for (const _widgetArea of widgetAreas) {
        const post = select(import_core_data3.store).getEditedEntityRecord(
          KIND,
          POST_TYPE,
          buildWidgetAreaPostId(_widgetArea.id)
        );
        for (const block of post.blocks) {
          if (block.name === "core/legacy-widget" && (!referenceWidgetName || block.attributes?.referenceWidgetName === referenceWidgetName)) {
            results.push(block);
          }
        }
      }
      return results;
    }
  );
  var isSavingWidgetAreas = (0, import_data7.createRegistrySelector)((select) => () => {
    const widgetAreasIds = select(STORE_NAME2).getWidgetAreas()?.map(({ id }) => id);
    if (!widgetAreasIds) {
      return false;
    }
    for (const id of widgetAreasIds) {
      const isSaving = select(import_core_data3.store).isSavingEntityRecord(
        KIND,
        WIDGET_AREA_ENTITY_TYPE,
        id
      );
      if (isSaving) {
        return true;
      }
    }
    const widgetIds = [
      ...Object.keys(select(STORE_NAME2).getWidgets()),
      void 0
      // account for new widgets without an ID
    ];
    for (const id of widgetIds) {
      const isSaving = select(import_core_data3.store).isSavingEntityRecord(
        "root",
        "widget",
        id
      );
      if (isSaving) {
        return true;
      }
    }
    return false;
  });
  var getIsWidgetAreaOpen = (state, clientId) => {
    const { widgetAreasOpenState: widgetAreasOpenState2 } = state;
    return !!widgetAreasOpenState2[clientId];
  };
  function isInserterOpened(state) {
    return !!state.blockInserterPanel;
  }
  function __experimentalGetInsertionPoint(state) {
    if (typeof state.blockInserterPanel === "boolean") {
      return EMPTY_INSERTION_POINT;
    }
    return state.blockInserterPanel;
  }
  var canInsertBlockInWidgetArea = (0, import_data7.createRegistrySelector)(
    (select) => (state, blockName) => {
      const widgetAreas = select(import_block_editor2.store).getBlocks();
      const [firstWidgetArea] = widgetAreas;
      return select(import_block_editor2.store).canInsertBlockType(
        blockName,
        firstWidgetArea.clientId
      );
    }
  );
  function isListViewOpened(state) {
    return state.listViewPanel;
  }
  function isWidgetSavingLocked(state) {
    return Object.keys(state.widgetSavingLock).length > 0;
  }

  // packages/edit-widgets/build-module/store/private-selectors.js
  var private_selectors_exports = {};
  __export(private_selectors_exports, {
    getInserterSidebarToggleRef: () => getInserterSidebarToggleRef,
    getListViewToggleRef: () => getListViewToggleRef
  });
  function getListViewToggleRef(state) {
    return state.listViewToggleRef;
  }
  function getInserterSidebarToggleRef(state) {
    return state.inserterSidebarToggleRef;
  }

  // packages/edit-widgets/build-module/lock-unlock.js
  var import_private_apis = __toESM(require_private_apis());
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/edit-widgets"
  );

  // packages/edit-widgets/build-module/store/index.js
  var storeConfig = {
    reducer: reducer_default,
    selectors: selectors_exports2,
    resolvers: resolvers_exports,
    actions: actions_exports2
  };
  var store2 = (0, import_data8.createReduxStore)(STORE_NAME2, storeConfig);
  (0, import_data8.register)(store2);
  import_api_fetch.default.use(function(options, next) {
    if (options.path?.indexOf("/wp/v2/types/widget-area") === 0) {
      return Promise.resolve({});
    }
    return next(options);
  });
  unlock(store2).registerPrivateSelectors(private_selectors_exports);

  // packages/edit-widgets/build-module/filters/move-to-widget-area.js
  var import_block_editor3 = __toESM(require_block_editor());
  var import_compose3 = __toESM(require_compose());
  var import_data9 = __toESM(require_data());
  var import_hooks = __toESM(require_hooks());
  var import_widgets4 = __toESM(require_widgets());
  var import_jsx_runtime22 = __toESM(require_jsx_runtime());
  var withMoveToWidgetAreaToolbarItem = (0, import_compose3.createHigherOrderComponent)(
    (BlockEdit) => (props) => {
      const { clientId, name: blockName } = props;
      const { widgetAreas, currentWidgetAreaId, canInsertBlockInWidgetArea: canInsertBlockInWidgetArea2 } = (0, import_data9.useSelect)(
        (select) => {
          if (blockName === "core/widget-area") {
            return {};
          }
          const selectors = select(store2);
          const widgetAreaBlock = selectors.getParentWidgetAreaBlock(clientId);
          return {
            widgetAreas: selectors.getWidgetAreas(),
            currentWidgetAreaId: widgetAreaBlock?.attributes?.id,
            canInsertBlockInWidgetArea: selectors.canInsertBlockInWidgetArea(blockName)
          };
        },
        [clientId, blockName]
      );
      const { moveBlockToWidgetArea: moveBlockToWidgetArea2 } = (0, import_data9.useDispatch)(store2);
      const hasMultipleWidgetAreas = widgetAreas?.length > 1;
      const isMoveToWidgetAreaVisible = blockName !== "core/widget-area" && hasMultipleWidgetAreas && canInsertBlockInWidgetArea2;
      return /* @__PURE__ */ (0, import_jsx_runtime22.jsxs)(import_jsx_runtime22.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(BlockEdit, { ...props }, "edit"),
        isMoveToWidgetAreaVisible && /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(import_block_editor3.BlockControls, { children: /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
          import_widgets4.MoveToWidgetArea,
          {
            widgetAreas,
            currentWidgetAreaId,
            onSelect: (widgetAreaId) => {
              moveBlockToWidgetArea2(
                props.clientId,
                widgetAreaId
              );
            }
          }
        ) })
      ] });
    },
    "withMoveToWidgetAreaToolbarItem"
  );
  (0, import_hooks.addFilter)(
    "editor.BlockEdit",
    "core/edit-widgets/block-edit",
    withMoveToWidgetAreaToolbarItem
  );

  // packages/edit-widgets/build-module/filters/replace-media-upload.js
  var import_hooks2 = __toESM(require_hooks());
  var import_media_utils = __toESM(require_media_utils());
  var { MediaUploadModal: MediaUploadModalComponent } = unlock(
    import_media_utils.privateApis
  );
  if (window.__experimentalDataViewsMediaModal) {
    (0, import_hooks2.addFilter)(
      "editor.MediaUploadModal",
      "core/edit-widgets/replace-media-upload-modal",
      () => {
        return MediaUploadModalComponent;
      }
    );
  }
  var replaceMediaUpload = () => import_media_utils.MediaUpload;
  (0, import_hooks2.addFilter)(
    "editor.MediaUpload",
    "core/edit-widgets/replace-media-upload",
    replaceMediaUpload
  );

  // packages/edit-widgets/build-module/blocks/widget-area/index.js
  var widget_area_exports = {};
  __export(widget_area_exports, {
    metadata: () => block_default,
    name: () => name,
    settings: () => settings
  });
  var import_i18n4 = __toESM(require_i18n());

  // packages/edit-widgets/build-module/blocks/widget-area/block.json
  var block_default = {
    $schema: "https://schemas.wp.org/trunk/block.json",
    apiVersion: 3,
    name: "core/widget-area",
    title: "Widget Area",
    category: "widgets",
    attributes: {
      id: {
        type: "string"
      },
      name: {
        type: "string"
      }
    },
    supports: {
      html: false,
      inserter: false,
      customClassName: false,
      reusable: false,
      __experimentalToolbar: false,
      __experimentalParentSelector: false,
      __experimentalDisableBlockOverlay: true
    },
    editorStyle: "wp-block-widget-area-editor",
    style: "wp-block-widget-area"
  };

  // packages/edit-widgets/build-module/blocks/widget-area/edit/index.js
  var import_element7 = __toESM(require_element());
  var import_data10 = __toESM(require_data());
  var import_core_data5 = __toESM(require_core_data());
  var import_components7 = __toESM(require_components());
  var import_block_editor5 = __toESM(require_block_editor());

  // packages/edit-widgets/build-module/blocks/widget-area/edit/inner-blocks.js
  var import_core_data4 = __toESM(require_core_data());
  var import_block_editor4 = __toESM(require_block_editor());
  var import_element6 = __toESM(require_element());

  // packages/edit-widgets/build-module/blocks/widget-area/edit/use-is-dragging-within.js
  var import_element5 = __toESM(require_element());
  var useIsDraggingWithin = (elementRef) => {
    const [isDraggingWithin, setIsDraggingWithin] = (0, import_element5.useState)(false);
    (0, import_element5.useEffect)(() => {
      const { ownerDocument } = elementRef.current;
      function handleDragStart(event) {
        handleDragEnter(event);
      }
      function handleDragEnd() {
        setIsDraggingWithin(false);
      }
      function handleDragEnter(event) {
        if (elementRef.current.contains(event.target)) {
          setIsDraggingWithin(true);
        } else {
          setIsDraggingWithin(false);
        }
      }
      ownerDocument.addEventListener("dragstart", handleDragStart);
      ownerDocument.addEventListener("dragend", handleDragEnd);
      ownerDocument.addEventListener("dragenter", handleDragEnter);
      return () => {
        ownerDocument.removeEventListener("dragstart", handleDragStart);
        ownerDocument.removeEventListener("dragend", handleDragEnd);
        ownerDocument.removeEventListener("dragenter", handleDragEnter);
      };
    }, []);
    return isDraggingWithin;
  };
  var use_is_dragging_within_default = useIsDraggingWithin;

  // packages/edit-widgets/build-module/blocks/widget-area/edit/inner-blocks.js
  var import_jsx_runtime23 = __toESM(require_jsx_runtime());
  function WidgetAreaInnerBlocks({ id }) {
    const [blocks, onInput, onChange] = (0, import_core_data4.useEntityBlockEditor)(
      "root",
      "postType"
    );
    const innerBlocksRef = (0, import_element6.useRef)();
    const isDraggingWithinInnerBlocks = use_is_dragging_within_default(innerBlocksRef);
    const shouldHighlightDropZone = isDraggingWithinInnerBlocks;
    const innerBlocksProps = (0, import_block_editor4.useInnerBlocksProps)(
      { ref: innerBlocksRef },
      {
        value: blocks,
        onInput,
        onChange,
        templateLock: false,
        renderAppender: import_block_editor4.InnerBlocks.ButtonBlockAppender
      }
    );
    return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
      "div",
      {
        "data-widget-area-id": id,
        className: clsx_default(
          "wp-block-widget-area__inner-blocks block-editor-inner-blocks editor-styles-wrapper",
          {
            "wp-block-widget-area__highlight-drop-zone": shouldHighlightDropZone
          }
        ),
        children: /* @__PURE__ */ (0, import_jsx_runtime23.jsx)("div", { ...innerBlocksProps })
      }
    );
  }

  // packages/edit-widgets/build-module/blocks/widget-area/edit/index.js
  var import_jsx_runtime24 = __toESM(require_jsx_runtime());
  function WidgetAreaEdit({
    clientId,
    attributes: { id, name: name2 }
  }) {
    const isOpen = (0, import_data10.useSelect)(
      (select) => select(store2).getIsWidgetAreaOpen(clientId),
      [clientId]
    );
    const { setIsWidgetAreaOpen: setIsWidgetAreaOpen2 } = (0, import_data10.useDispatch)(store2);
    const wrapper = (0, import_element7.useRef)();
    const setOpen = (0, import_element7.useCallback)(
      (openState) => setIsWidgetAreaOpen2(clientId, openState),
      [clientId]
    );
    const isDragging = useIsDragging(wrapper);
    const isDraggingWithin = use_is_dragging_within_default(wrapper);
    const [openedWhileDragging, setOpenedWhileDragging] = (0, import_element7.useState)(false);
    (0, import_element7.useEffect)(() => {
      if (!isDragging) {
        setOpenedWhileDragging(false);
        return;
      }
      if (isDraggingWithin && !isOpen) {
        setOpen(true);
        setOpenedWhileDragging(true);
      } else if (!isDraggingWithin && isOpen && openedWhileDragging) {
        setOpen(false);
      }
    }, [isOpen, isDragging, isDraggingWithin, openedWhileDragging]);
    const blockProps = (0, import_block_editor5.useBlockProps)();
    return /* @__PURE__ */ (0, import_jsx_runtime24.jsx)("div", { ...blockProps, children: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(import_components7.Panel, { ref: wrapper, children: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
      import_components7.PanelBody,
      {
        title: name2,
        opened: isOpen,
        onToggle: () => {
          setIsWidgetAreaOpen2(clientId, !isOpen);
        },
        scrollAfterOpen: !isDragging,
        children: ({ opened }) => (
          // This is required to ensure LegacyWidget blocks are not
          // unmounted when the panel is collapsed. Unmounting legacy
          // widgets may have unintended consequences (e.g.  TinyMCE
          // not being properly reinitialized)
          /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
            import_components7.__unstableDisclosureContent,
            {
              className: "wp-block-widget-area__panel-body-content",
              visible: opened,
              children: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
                import_core_data5.EntityProvider,
                {
                  kind: "root",
                  type: "postType",
                  id: `widget-area-${id}`,
                  children: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(WidgetAreaInnerBlocks, { id })
                }
              )
            }
          )
        )
      }
    ) }) });
  }
  var useIsDragging = (elementRef) => {
    const [isDragging, setIsDragging] = (0, import_element7.useState)(false);
    (0, import_element7.useEffect)(() => {
      const { ownerDocument } = elementRef.current;
      function handleDragStart() {
        setIsDragging(true);
      }
      function handleDragEnd() {
        setIsDragging(false);
      }
      ownerDocument.addEventListener("dragstart", handleDragStart);
      ownerDocument.addEventListener("dragend", handleDragEnd);
      return () => {
        ownerDocument.removeEventListener("dragstart", handleDragStart);
        ownerDocument.removeEventListener("dragend", handleDragEnd);
      };
    }, []);
    return isDragging;
  };

  // packages/edit-widgets/build-module/blocks/widget-area/index.js
  var { name } = block_default;
  var settings = {
    title: (0, import_i18n4.__)("Widget Area"),
    description: (0, import_i18n4.__)("A widget area container."),
    __experimentalLabel: ({ name: label }) => label,
    edit: WidgetAreaEdit
  };

  // packages/edit-widgets/build-module/components/layout/index.js
  var import_i18n21 = __toESM(require_i18n());
  var import_data32 = __toESM(require_data());
  var import_plugins3 = __toESM(require_plugins());
  var import_notices4 = __toESM(require_notices());
  var import_components23 = __toESM(require_components());

  // packages/edit-widgets/build-module/components/error-boundary/index.js
  var import_element8 = __toESM(require_element());
  var import_i18n5 = __toESM(require_i18n());
  var import_components8 = __toESM(require_components());
  var import_block_editor6 = __toESM(require_block_editor());
  var import_compose4 = __toESM(require_compose());
  var import_hooks3 = __toESM(require_hooks());
  var import_jsx_runtime25 = __toESM(require_jsx_runtime());
  function CopyButton({ text, children }) {
    const ref = (0, import_compose4.useCopyToClipboard)(text);
    return /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(import_components8.Button, { __next40pxDefaultSize: true, variant: "secondary", ref, children });
  }
  function ErrorBoundaryWarning({ message, error }) {
    const actions = [
      /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(CopyButton, { text: error.stack, children: (0, import_i18n5.__)("Copy Error") }, "copy-error")
    ];
    return /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(import_block_editor6.Warning, { className: "edit-widgets-error-boundary", actions, children: message });
  }
  var ErrorBoundary = class extends import_element8.Component {
    constructor() {
      super(...arguments);
      this.state = {
        error: null
      };
    }
    componentDidCatch(error) {
      (0, import_hooks3.doAction)("editor.ErrorBoundary.errorLogged", error);
    }
    static getDerivedStateFromError(error) {
      return { error };
    }
    render() {
      if (!this.state.error) {
        return this.props.children;
      }
      return /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(
        ErrorBoundaryWarning,
        {
          message: (0, import_i18n5.__)(
            "The editor has encountered an unexpected error."
          ),
          error: this.state.error
        }
      );
    }
  };

  // packages/edit-widgets/build-module/components/widget-areas-block-editor-provider/index.js
  var import_components9 = __toESM(require_components());
  var import_compose5 = __toESM(require_compose());
  var import_media_utils2 = __toESM(require_media_utils());
  var import_data13 = __toESM(require_data());
  var import_core_data8 = __toESM(require_core_data());
  var import_element10 = __toESM(require_element());
  var import_block_editor8 = __toESM(require_block_editor());
  var import_patterns = __toESM(require_patterns());
  var import_preferences4 = __toESM(require_preferences());
  var import_block_library = __toESM(require_block_library());

  // packages/edit-widgets/build-module/components/keyboard-shortcuts/index.js
  var import_element9 = __toESM(require_element());
  var import_keyboard_shortcuts = __toESM(require_keyboard_shortcuts());
  var import_keycodes = __toESM(require_keycodes());
  var import_data11 = __toESM(require_data());
  var import_i18n6 = __toESM(require_i18n());
  var import_core_data6 = __toESM(require_core_data());
  function KeyboardShortcuts() {
    const { redo, undo } = (0, import_data11.useDispatch)(import_core_data6.store);
    const { saveEditedWidgetAreas: saveEditedWidgetAreas2 } = (0, import_data11.useDispatch)(store2);
    (0, import_keyboard_shortcuts.useShortcut)("core/edit-widgets/undo", (event) => {
      undo();
      event.preventDefault();
    });
    (0, import_keyboard_shortcuts.useShortcut)("core/edit-widgets/redo", (event) => {
      redo();
      event.preventDefault();
    });
    (0, import_keyboard_shortcuts.useShortcut)("core/edit-widgets/save", (event) => {
      event.preventDefault();
      saveEditedWidgetAreas2();
    });
    return null;
  }
  function KeyboardShortcutsRegister() {
    const { registerShortcut } = (0, import_data11.useDispatch)(import_keyboard_shortcuts.store);
    (0, import_element9.useEffect)(() => {
      registerShortcut({
        name: "core/edit-widgets/undo",
        category: "global",
        description: (0, import_i18n6.__)("Undo your last changes."),
        keyCombination: {
          modifier: "primary",
          character: "z"
        }
      });
      registerShortcut({
        name: "core/edit-widgets/redo",
        category: "global",
        description: (0, import_i18n6.__)("Redo your last undo."),
        keyCombination: {
          modifier: "primaryShift",
          character: "z"
        },
        // Disable on Apple OS because it conflicts with the browser's
        // history shortcut. It's a fine alias for both Windows and Linux.
        // Since there's no conflict for Ctrl+Shift+Z on both Windows and
        // Linux, we keep it as the default for consistency.
        aliases: (0, import_keycodes.isAppleOS)() ? [] : [
          {
            modifier: "primary",
            character: "y"
          }
        ]
      });
      registerShortcut({
        name: "core/edit-widgets/save",
        category: "global",
        description: (0, import_i18n6.__)("Save your changes."),
        keyCombination: {
          modifier: "primary",
          character: "s"
        }
      });
      registerShortcut({
        name: "core/edit-widgets/keyboard-shortcuts",
        category: "main",
        description: (0, import_i18n6.__)("Display these keyboard shortcuts."),
        keyCombination: {
          modifier: "access",
          character: "h"
        }
      });
      registerShortcut({
        name: "core/edit-widgets/next-region",
        category: "global",
        description: (0, import_i18n6.__)("Navigate to the next part of the editor."),
        keyCombination: {
          modifier: "ctrl",
          character: "`"
        },
        aliases: [
          {
            modifier: "access",
            character: "n"
          }
        ]
      });
      registerShortcut({
        name: "core/edit-widgets/previous-region",
        category: "global",
        description: (0, import_i18n6.__)("Navigate to the previous part of the editor."),
        keyCombination: {
          modifier: "ctrlShift",
          character: "`"
        },
        aliases: [
          {
            modifier: "access",
            character: "p"
          },
          {
            modifier: "ctrlShift",
            character: "~"
          }
        ]
      });
    }, [registerShortcut]);
    return null;
  }
  KeyboardShortcuts.Register = KeyboardShortcutsRegister;
  var keyboard_shortcuts_default = KeyboardShortcuts;

  // packages/edit-widgets/build-module/hooks/use-last-selected-widget-area.js
  var import_data12 = __toESM(require_data());
  var import_block_editor7 = __toESM(require_block_editor());
  var import_core_data7 = __toESM(require_core_data());
  var useLastSelectedWidgetArea = () => (0, import_data12.useSelect)((select) => {
    const { getBlockSelectionEnd, getBlockName } = select(import_block_editor7.store);
    const selectionEndClientId = getBlockSelectionEnd();
    if (getBlockName(selectionEndClientId) === "core/widget-area") {
      return selectionEndClientId;
    }
    const { getParentWidgetAreaBlock: getParentWidgetAreaBlock2 } = select(store2);
    const widgetAreaBlock = getParentWidgetAreaBlock2(selectionEndClientId);
    const widgetAreaBlockClientId = widgetAreaBlock?.clientId;
    if (widgetAreaBlockClientId) {
      return widgetAreaBlockClientId;
    }
    const { getEntityRecord } = select(import_core_data7.store);
    const widgetAreasPost = getEntityRecord(
      KIND,
      POST_TYPE,
      buildWidgetAreasPostId()
    );
    return widgetAreasPost?.blocks[0]?.clientId;
  }, []);
  var use_last_selected_widget_area_default = useLastSelectedWidgetArea;

  // packages/edit-widgets/build-module/constants.js
  var ALLOW_REUSABLE_BLOCKS = false;
  var ENABLE_EXPERIMENTAL_FSE_BLOCKS = false;

  // packages/edit-widgets/build-module/components/widget-areas-block-editor-provider/index.js
  var import_jsx_runtime26 = __toESM(require_jsx_runtime());
  var { ExperimentalBlockEditorProvider } = unlock(import_block_editor8.privateApis);
  var { PatternsMenuItems } = unlock(import_patterns.privateApis);
  var { BlockKeyboardShortcuts } = unlock(import_block_library.privateApis);
  var EMPTY_ARRAY = [];
  function WidgetAreasBlockEditorProvider({
    blockEditorSettings,
    children,
    ...props
  }) {
    const isLargeViewport = (0, import_compose5.useViewportMatch)("medium");
    const {
      hasUploadPermissions,
      reusableBlocks,
      isFixedToolbarActive,
      keepCaretInsideBlock,
      pageOnFront,
      pageForPosts
    } = (0, import_data13.useSelect)((select) => {
      const { canUser, getEntityRecord, getEntityRecords } = select(import_core_data8.store);
      const siteSettings = canUser("read", {
        kind: "root",
        name: "site"
      }) ? getEntityRecord("root", "site") : void 0;
      return {
        hasUploadPermissions: canUser("create", {
          kind: "postType",
          name: "attachment"
        }) ?? true,
        reusableBlocks: ALLOW_REUSABLE_BLOCKS ? getEntityRecords("postType", "wp_block") : EMPTY_ARRAY,
        isFixedToolbarActive: !!select(import_preferences4.store).get(
          "core/edit-widgets",
          "fixedToolbar"
        ),
        keepCaretInsideBlock: !!select(import_preferences4.store).get(
          "core/edit-widgets",
          "keepCaretInsideBlock"
        ),
        pageOnFront: siteSettings?.page_on_front,
        pageForPosts: siteSettings?.page_for_posts
      };
    }, []);
    const { setIsInserterOpened: setIsInserterOpened2 } = (0, import_data13.useDispatch)(store2);
    const settings2 = (0, import_element10.useMemo)(() => {
      let mediaUploadBlockEditor;
      if (hasUploadPermissions) {
        mediaUploadBlockEditor = ({ onError, ...argumentsObject }) => {
          (0, import_media_utils2.uploadMedia)({
            wpAllowedMimeTypes: blockEditorSettings.allowedMimeTypes,
            onError: ({ message }) => onError(message),
            ...argumentsObject
          });
        };
      }
      return {
        ...blockEditorSettings,
        __experimentalReusableBlocks: reusableBlocks,
        hasFixedToolbar: isFixedToolbarActive || !isLargeViewport,
        keepCaretInsideBlock,
        mediaUpload: mediaUploadBlockEditor,
        templateLock: "all",
        __experimentalSetIsInserterOpened: setIsInserterOpened2,
        pageOnFront,
        pageForPosts,
        editorTool: "edit"
      };
    }, [
      hasUploadPermissions,
      blockEditorSettings,
      isFixedToolbarActive,
      isLargeViewport,
      keepCaretInsideBlock,
      reusableBlocks,
      setIsInserterOpened2,
      pageOnFront,
      pageForPosts
    ]);
    const widgetAreaId = use_last_selected_widget_area_default();
    const [blocks, onInput, onChange] = (0, import_core_data8.useEntityBlockEditor)(
      KIND,
      POST_TYPE,
      { id: buildWidgetAreasPostId() }
    );
    return /* @__PURE__ */ (0, import_jsx_runtime26.jsxs)(import_components9.SlotFillProvider, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(keyboard_shortcuts_default.Register, {}),
      /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(BlockKeyboardShortcuts, {}),
      /* @__PURE__ */ (0, import_jsx_runtime26.jsxs)(
        ExperimentalBlockEditorProvider,
        {
          value: blocks,
          onInput,
          onChange,
          settings: settings2,
          useSubRegistry: false,
          ...props,
          children: [
            children,
            /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(PatternsMenuItems, { rootClientId: widgetAreaId })
          ]
        }
      )
    ] });
  }

  // packages/edit-widgets/build-module/components/sidebar/index.js
  var import_element12 = __toESM(require_element());
  var import_i18n8 = __toESM(require_i18n());
  var import_block_editor10 = __toESM(require_block_editor());
  var import_components11 = __toESM(require_components());
  var import_data15 = __toESM(require_data());

  // packages/edit-widgets/build-module/components/sidebar/widget-areas.js
  var import_data14 = __toESM(require_data());
  var import_element11 = __toESM(require_element());
  var import_block_editor9 = __toESM(require_block_editor());
  var import_components10 = __toESM(require_components());
  var import_i18n7 = __toESM(require_i18n());
  var import_url = __toESM(require_url());
  var import_dom = __toESM(require_dom());
  var import_jsx_runtime27 = __toESM(require_jsx_runtime());
  function WidgetAreas({ selectedWidgetAreaId }) {
    const widgetAreas = (0, import_data14.useSelect)(
      (select) => select(store2).getWidgetAreas(),
      []
    );
    const selectedWidgetArea = (0, import_element11.useMemo)(
      () => selectedWidgetAreaId && widgetAreas?.find(
        (widgetArea) => widgetArea.id === selectedWidgetAreaId
      ),
      [selectedWidgetAreaId, widgetAreas]
    );
    let description;
    if (!selectedWidgetArea) {
      description = (0, import_i18n7.__)(
        // eslint-disable-next-line no-restricted-syntax -- 'sidebar' is a common web design term for layouts
        "Widget Areas are global parts in your site\u2019s layout that can accept blocks. These vary by theme, but are typically parts like your Sidebar or Footer."
      );
    } else if (selectedWidgetAreaId === "wp_inactive_widgets") {
      description = (0, import_i18n7.__)(
        "Blocks in this Widget Area will not be displayed in your site."
      );
    } else {
      description = selectedWidgetArea.description;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime27.jsx)("div", { className: "edit-widgets-widget-areas", children: /* @__PURE__ */ (0, import_jsx_runtime27.jsxs)("div", { className: "edit-widgets-widget-areas__top-container", children: [
      /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(import_block_editor9.BlockIcon, { icon: block_default_default }),
      /* @__PURE__ */ (0, import_jsx_runtime27.jsxs)("div", { children: [
        /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(
          "p",
          {
            dangerouslySetInnerHTML: {
              __html: (0, import_dom.safeHTML)(description)
            }
          }
        ),
        widgetAreas?.length === 0 && /* @__PURE__ */ (0, import_jsx_runtime27.jsx)("p", { children: (0, import_i18n7.__)(
          "Your theme does not contain any Widget Areas."
        ) }),
        !selectedWidgetArea && /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(
          import_components10.Button,
          {
            __next40pxDefaultSize: true,
            href: (0, import_url.addQueryArgs)("customize.php", {
              "autofocus[panel]": "widgets",
              return: window.location.pathname
            }),
            variant: "tertiary",
            children: (0, import_i18n7.__)("Manage with live preview")
          }
        )
      ] })
    ] }) });
  }

  // packages/edit-widgets/build-module/components/sidebar/index.js
  var import_jsx_runtime28 = __toESM(require_jsx_runtime());
  var SIDEBAR_ACTIVE_BY_DEFAULT = import_element12.Platform.select({
    web: true,
    native: false
  });
  var BLOCK_INSPECTOR_IDENTIFIER = "edit-widgets/block-inspector";
  var WIDGET_AREAS_IDENTIFIER = "edit-widgets/block-areas";
  var { Tabs } = unlock(import_components11.privateApis);
  function SidebarHeader({ selectedWidgetAreaBlock }) {
    return /* @__PURE__ */ (0, import_jsx_runtime28.jsxs)(Tabs.TabList, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(Tabs.Tab, { tabId: WIDGET_AREAS_IDENTIFIER, children: selectedWidgetAreaBlock ? selectedWidgetAreaBlock.attributes.name : (0, import_i18n8.__)("Widget Areas") }),
      /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(Tabs.Tab, { tabId: BLOCK_INSPECTOR_IDENTIFIER, children: (0, import_i18n8.__)("Block") })
    ] });
  }
  function SidebarContent({
    hasSelectedNonAreaBlock,
    currentArea,
    isGeneralSidebarOpen,
    selectedWidgetAreaBlock
  }) {
    const { enableComplementaryArea: enableComplementaryArea2 } = (0, import_data15.useDispatch)(store);
    (0, import_element12.useEffect)(() => {
      if (hasSelectedNonAreaBlock && currentArea === WIDGET_AREAS_IDENTIFIER && isGeneralSidebarOpen) {
        enableComplementaryArea2(
          "core/edit-widgets",
          BLOCK_INSPECTOR_IDENTIFIER
        );
      }
      if (!hasSelectedNonAreaBlock && currentArea === BLOCK_INSPECTOR_IDENTIFIER && isGeneralSidebarOpen) {
        enableComplementaryArea2(
          "core/edit-widgets",
          WIDGET_AREAS_IDENTIFIER
        );
      }
    }, [hasSelectedNonAreaBlock, enableComplementaryArea2]);
    const tabsContextValue = (0, import_element12.useContext)(Tabs.Context);
    return /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
      complementary_area_default,
      {
        className: "edit-widgets-sidebar",
        header: /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(Tabs.Context.Provider, { value: tabsContextValue, children: /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
          SidebarHeader,
          {
            selectedWidgetAreaBlock
          }
        ) }),
        headerClassName: "edit-widgets-sidebar__panel-tabs",
        title: (0, import_i18n8.__)("Settings"),
        closeLabel: (0, import_i18n8.__)("Close Settings"),
        scope: "core/edit-widgets",
        identifier: currentArea,
        icon: (0, import_i18n8.isRTL)() ? drawer_left_default : drawer_right_default,
        isActiveByDefault: SIDEBAR_ACTIVE_BY_DEFAULT,
        children: /* @__PURE__ */ (0, import_jsx_runtime28.jsxs)(Tabs.Context.Provider, { value: tabsContextValue, children: [
          /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
            Tabs.TabPanel,
            {
              tabId: WIDGET_AREAS_IDENTIFIER,
              focusable: false,
              children: /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
                WidgetAreas,
                {
                  selectedWidgetAreaId: selectedWidgetAreaBlock?.attributes.id
                }
              )
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
            Tabs.TabPanel,
            {
              tabId: BLOCK_INSPECTOR_IDENTIFIER,
              focusable: false,
              children: hasSelectedNonAreaBlock ? /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(import_block_editor10.BlockInspector, {}) : (
                // Pretend that Widget Areas are part of the UI by not
                // showing the Block Inspector when one is selected.
                /* @__PURE__ */ (0, import_jsx_runtime28.jsx)("span", { className: "block-editor-block-inspector__no-blocks", children: (0, import_i18n8.__)("No block selected.") })
              )
            }
          )
        ] })
      }
    );
  }
  function Sidebar() {
    const {
      currentArea,
      hasSelectedNonAreaBlock,
      isGeneralSidebarOpen,
      selectedWidgetAreaBlock
    } = (0, import_data15.useSelect)((select) => {
      const { getSelectedBlock, getBlock, getBlockParentsByBlockName } = select(import_block_editor10.store);
      const { getActiveComplementaryArea: getActiveComplementaryArea2 } = select(store);
      const selectedBlock = getSelectedBlock();
      const activeArea = getActiveComplementaryArea2(store2.name);
      let currentSelection = activeArea;
      if (!currentSelection) {
        if (selectedBlock) {
          currentSelection = BLOCK_INSPECTOR_IDENTIFIER;
        } else {
          currentSelection = WIDGET_AREAS_IDENTIFIER;
        }
      }
      let widgetAreaBlock;
      if (selectedBlock) {
        if (selectedBlock.name === "core/widget-area") {
          widgetAreaBlock = selectedBlock;
        } else {
          widgetAreaBlock = getBlock(
            getBlockParentsByBlockName(
              selectedBlock.clientId,
              "core/widget-area"
            )[0]
          );
        }
      }
      return {
        currentArea: currentSelection,
        hasSelectedNonAreaBlock: !!(selectedBlock && selectedBlock.name !== "core/widget-area"),
        isGeneralSidebarOpen: !!activeArea,
        selectedWidgetAreaBlock: widgetAreaBlock
      };
    }, []);
    const { enableComplementaryArea: enableComplementaryArea2 } = (0, import_data15.useDispatch)(store);
    const onTabSelect = (0, import_element12.useCallback)(
      (newSelectedTabId) => {
        if (!!newSelectedTabId) {
          enableComplementaryArea2(
            store2.name,
            newSelectedTabId
          );
        }
      },
      [enableComplementaryArea2]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
      Tabs,
      {
        selectedTabId: isGeneralSidebarOpen ? currentArea : null,
        onSelect: onTabSelect,
        selectOnMove: false,
        children: /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
          SidebarContent,
          {
            hasSelectedNonAreaBlock,
            currentArea,
            isGeneralSidebarOpen,
            selectedWidgetAreaBlock
          }
        )
      }
    );
  }

  // packages/edit-widgets/build-module/components/layout/interface.js
  var import_compose12 = __toESM(require_compose());
  var import_block_editor17 = __toESM(require_block_editor());
  var import_element22 = __toESM(require_element());
  var import_data29 = __toESM(require_data());
  var import_i18n18 = __toESM(require_i18n());
  var import_preferences8 = __toESM(require_preferences());

  // packages/edit-widgets/build-module/components/header/index.js
  var import_block_editor12 = __toESM(require_block_editor());
  var import_data22 = __toESM(require_data());
  var import_element18 = __toESM(require_element());
  var import_i18n16 = __toESM(require_i18n());
  var import_components19 = __toESM(require_components());
  var import_compose8 = __toESM(require_compose());
  var import_preferences6 = __toESM(require_preferences());

  // packages/edit-widgets/build-module/components/header/document-tools/index.js
  var import_data18 = __toESM(require_data());
  var import_i18n11 = __toESM(require_i18n());
  var import_components14 = __toESM(require_components());
  var import_block_editor11 = __toESM(require_block_editor());
  var import_element15 = __toESM(require_element());
  var import_compose6 = __toESM(require_compose());

  // packages/edit-widgets/build-module/components/header/undo-redo/undo.js
  var import_i18n9 = __toESM(require_i18n());
  var import_components12 = __toESM(require_components());
  var import_data16 = __toESM(require_data());
  var import_keycodes2 = __toESM(require_keycodes());
  var import_core_data9 = __toESM(require_core_data());
  var import_element13 = __toESM(require_element());
  var import_jsx_runtime29 = __toESM(require_jsx_runtime());
  function UndoButton(props, ref) {
    const hasUndo = (0, import_data16.useSelect)(
      (select) => select(import_core_data9.store).hasUndo(),
      []
    );
    const { undo } = (0, import_data16.useDispatch)(import_core_data9.store);
    return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
      import_components12.Button,
      {
        ...props,
        ref,
        icon: !(0, import_i18n9.isRTL)() ? undo_default : redo_default,
        label: (0, import_i18n9.__)("Undo"),
        shortcut: import_keycodes2.displayShortcut.primary("z"),
        "aria-disabled": !hasUndo,
        onClick: hasUndo ? undo : void 0,
        size: "compact"
      }
    );
  }
  var undo_default2 = (0, import_element13.forwardRef)(UndoButton);

  // packages/edit-widgets/build-module/components/header/undo-redo/redo.js
  var import_i18n10 = __toESM(require_i18n());
  var import_components13 = __toESM(require_components());
  var import_data17 = __toESM(require_data());
  var import_keycodes3 = __toESM(require_keycodes());
  var import_core_data10 = __toESM(require_core_data());
  var import_element14 = __toESM(require_element());
  var import_jsx_runtime30 = __toESM(require_jsx_runtime());
  function RedoButton(props, ref) {
    const shortcut = (0, import_keycodes3.isAppleOS)() ? import_keycodes3.displayShortcut.primaryShift("z") : import_keycodes3.displayShortcut.primary("y");
    const hasRedo = (0, import_data17.useSelect)(
      (select) => select(import_core_data10.store).hasRedo(),
      []
    );
    const { redo } = (0, import_data17.useDispatch)(import_core_data10.store);
    return /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
      import_components13.Button,
      {
        ...props,
        ref,
        icon: !(0, import_i18n10.isRTL)() ? redo_default : undo_default,
        label: (0, import_i18n10.__)("Redo"),
        shortcut,
        "aria-disabled": !hasRedo,
        onClick: hasRedo ? redo : void 0,
        size: "compact"
      }
    );
  }
  var redo_default2 = (0, import_element14.forwardRef)(RedoButton);

  // packages/edit-widgets/build-module/components/header/document-tools/index.js
  var import_jsx_runtime31 = __toESM(require_jsx_runtime());
  function DocumentTools() {
    const isMediumViewport = (0, import_compose6.useViewportMatch)("medium");
    const {
      isInserterOpen,
      isListViewOpen,
      inserterSidebarToggleRef: inserterSidebarToggleRef2,
      listViewToggleRef: listViewToggleRef2
    } = (0, import_data18.useSelect)((select) => {
      const {
        isInserterOpened: isInserterOpened2,
        getInserterSidebarToggleRef: getInserterSidebarToggleRef2,
        isListViewOpened: isListViewOpened2,
        getListViewToggleRef: getListViewToggleRef2
      } = unlock(select(store2));
      return {
        isInserterOpen: isInserterOpened2(),
        isListViewOpen: isListViewOpened2(),
        inserterSidebarToggleRef: getInserterSidebarToggleRef2(),
        listViewToggleRef: getListViewToggleRef2()
      };
    }, []);
    const { setIsInserterOpened: setIsInserterOpened2, setIsListViewOpened: setIsListViewOpened2 } = (0, import_data18.useDispatch)(store2);
    const toggleListView = (0, import_element15.useCallback)(
      () => setIsListViewOpened2(!isListViewOpen),
      [setIsListViewOpened2, isListViewOpen]
    );
    const toggleInserterSidebar = (0, import_element15.useCallback)(
      () => setIsInserterOpened2(!isInserterOpen),
      [setIsInserterOpened2, isInserterOpen]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(
      import_block_editor11.NavigableToolbar,
      {
        className: "edit-widgets-header-toolbar",
        "aria-label": (0, import_i18n11.__)("Document tools"),
        variant: "unstyled",
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
            import_components14.ToolbarItem,
            {
              ref: inserterSidebarToggleRef2,
              as: import_components14.Button,
              className: "edit-widgets-header-toolbar__inserter-toggle",
              variant: "primary",
              isPressed: isInserterOpen,
              onMouseDown: (event) => {
                event.preventDefault();
              },
              onClick: toggleInserterSidebar,
              icon: plus_default,
              label: (0, import_i18n11._x)(
                "Block Inserter",
                "Generic label for block inserter button"
              ),
              size: "compact"
            }
          ),
          isMediumViewport && /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(import_jsx_runtime31.Fragment, { children: [
            /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_components14.ToolbarItem, { as: undo_default2 }),
            /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_components14.ToolbarItem, { as: redo_default2 }),
            /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
              import_components14.ToolbarItem,
              {
                as: import_components14.Button,
                className: "edit-widgets-header-toolbar__list-view-toggle",
                icon: list_view_default,
                isPressed: isListViewOpen,
                label: (0, import_i18n11.__)("List View"),
                onClick: toggleListView,
                ref: listViewToggleRef2,
                size: "compact"
              }
            )
          ] })
        ]
      }
    );
  }
  var document_tools_default = DocumentTools;

  // packages/edit-widgets/build-module/components/save-button/index.js
  var import_components15 = __toESM(require_components());
  var import_i18n12 = __toESM(require_i18n());
  var import_data19 = __toESM(require_data());
  var import_jsx_runtime32 = __toESM(require_jsx_runtime());
  function SaveButton() {
    const { hasEditedWidgetAreaIds, isSaving, isWidgetSaveLocked } = (0, import_data19.useSelect)(
      (select) => {
        const {
          getEditedWidgetAreas: getEditedWidgetAreas2,
          isSavingWidgetAreas: isSavingWidgetAreas2,
          isWidgetSavingLocked: isWidgetSavingLocked2
        } = select(store2);
        return {
          hasEditedWidgetAreaIds: getEditedWidgetAreas2()?.length > 0,
          isSaving: isSavingWidgetAreas2(),
          isWidgetSaveLocked: isWidgetSavingLocked2()
        };
      },
      []
    );
    const { saveEditedWidgetAreas: saveEditedWidgetAreas2 } = (0, import_data19.useDispatch)(store2);
    const isDisabled = isWidgetSaveLocked || isSaving || !hasEditedWidgetAreaIds;
    return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(
      import_components15.Button,
      {
        variant: "primary",
        isBusy: isSaving,
        "aria-disabled": isDisabled,
        onClick: isDisabled ? void 0 : saveEditedWidgetAreas2,
        size: "compact",
        children: isSaving ? (0, import_i18n12.__)("Saving\u2026") : (0, import_i18n12.__)("Update")
      }
    );
  }
  var save_button_default = SaveButton;

  // packages/edit-widgets/build-module/components/more-menu/index.js
  var import_components18 = __toESM(require_components());
  var import_element17 = __toESM(require_element());
  var import_i18n15 = __toESM(require_i18n());
  var import_preferences5 = __toESM(require_preferences());
  var import_keycodes5 = __toESM(require_keycodes());
  var import_keyboard_shortcuts5 = __toESM(require_keyboard_shortcuts());
  var import_compose7 = __toESM(require_compose());

  // packages/edit-widgets/build-module/components/keyboard-shortcut-help-modal/index.js
  var import_components16 = __toESM(require_components());
  var import_i18n14 = __toESM(require_i18n());
  var import_keyboard_shortcuts4 = __toESM(require_keyboard_shortcuts());
  var import_data21 = __toESM(require_data());

  // packages/edit-widgets/build-module/components/keyboard-shortcut-help-modal/config.js
  var import_i18n13 = __toESM(require_i18n());
  var textFormattingShortcuts = [
    {
      keyCombination: { modifier: "primary", character: "b" },
      description: (0, import_i18n13.__)("Make the selected text bold.")
    },
    {
      keyCombination: { modifier: "primary", character: "i" },
      description: (0, import_i18n13.__)("Make the selected text italic.")
    },
    {
      keyCombination: { modifier: "primary", character: "k" },
      description: (0, import_i18n13.__)("Convert the selected text into a link.")
    },
    {
      keyCombination: { modifier: "primaryShift", character: "k" },
      description: (0, import_i18n13.__)("Remove a link.")
    },
    {
      keyCombination: { character: "[[" },
      description: (0, import_i18n13.__)("Insert a link to a post or page.")
    },
    {
      keyCombination: { modifier: "primary", character: "u" },
      description: (0, import_i18n13.__)("Underline the selected text.")
    },
    {
      keyCombination: { modifier: "access", character: "d" },
      description: (0, import_i18n13.__)("Strikethrough the selected text.")
    },
    {
      keyCombination: { modifier: "access", character: "x" },
      description: (0, import_i18n13.__)("Make the selected text inline code.")
    },
    {
      keyCombination: {
        modifier: "access",
        character: "0"
      },
      aliases: [
        {
          modifier: "access",
          character: "7"
        }
      ],
      description: (0, import_i18n13.__)("Convert the current heading to a paragraph.")
    },
    {
      keyCombination: { modifier: "access", character: "1-6" },
      description: (0, import_i18n13.__)(
        "Convert the current paragraph or heading to a heading of level 1 to 6."
      )
    },
    {
      keyCombination: { modifier: "primaryShift", character: "SPACE" },
      description: (0, import_i18n13.__)("Add non breaking space.")
    }
  ];

  // packages/edit-widgets/build-module/components/keyboard-shortcut-help-modal/shortcut.js
  var import_element16 = __toESM(require_element());
  var import_keycodes4 = __toESM(require_keycodes());
  var import_jsx_runtime33 = __toESM(require_jsx_runtime());
  function KeyCombination({ keyCombination, forceAriaLabel }) {
    const shortcut = keyCombination.modifier ? import_keycodes4.displayShortcutList[keyCombination.modifier](
      keyCombination.character
    ) : keyCombination.character;
    const ariaLabel = keyCombination.modifier ? import_keycodes4.shortcutAriaLabel[keyCombination.modifier](
      keyCombination.character
    ) : keyCombination.character;
    const shortcuts = Array.isArray(shortcut) ? shortcut : [shortcut];
    return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
      "kbd",
      {
        className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-key-combination",
        "aria-label": forceAriaLabel || ariaLabel,
        children: shortcuts.map((character, index) => {
          if (character === "+") {
            return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_element16.Fragment, { children: character }, index);
          }
          return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
            "kbd",
            {
              className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-key",
              children: character
            },
            index
          );
        })
      }
    );
  }
  function Shortcut({ description, keyCombination, aliases = [], ariaLabel }) {
    return /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(import_jsx_runtime33.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime33.jsx)("div", { className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-description", children: description }),
      /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)("div", { className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-term", children: [
        /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
          KeyCombination,
          {
            keyCombination,
            forceAriaLabel: ariaLabel
          }
        ),
        aliases.map((alias, index) => /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
          KeyCombination,
          {
            keyCombination: alias,
            forceAriaLabel: ariaLabel
          },
          index
        ))
      ] })
    ] });
  }
  var shortcut_default = Shortcut;

  // packages/edit-widgets/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.js
  var import_data20 = __toESM(require_data());
  var import_keyboard_shortcuts3 = __toESM(require_keyboard_shortcuts());
  var import_jsx_runtime34 = __toESM(require_jsx_runtime());
  function DynamicShortcut({ name: name2 }) {
    const { keyCombination, description, aliases } = (0, import_data20.useSelect)(
      (select) => {
        const {
          getShortcutKeyCombination,
          getShortcutDescription,
          getShortcutAliases
        } = select(import_keyboard_shortcuts3.store);
        return {
          keyCombination: getShortcutKeyCombination(name2),
          aliases: getShortcutAliases(name2),
          description: getShortcutDescription(name2)
        };
      },
      [name2]
    );
    if (!keyCombination) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(
      shortcut_default,
      {
        keyCombination,
        description,
        aliases
      }
    );
  }
  var dynamic_shortcut_default = DynamicShortcut;

  // packages/edit-widgets/build-module/components/keyboard-shortcut-help-modal/index.js
  var import_jsx_runtime35 = __toESM(require_jsx_runtime());
  var ShortcutList = ({ shortcuts }) => (
    /*
     * Disable reason: The `list` ARIA role is redundant but
     * Safari+VoiceOver won't announce the list otherwise.
     */
    /* eslint-disable jsx-a11y/no-redundant-roles */
    /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
      "ul",
      {
        className: "edit-widgets-keyboard-shortcut-help-modal__shortcut-list",
        role: "list",
        children: shortcuts.map((shortcut, index) => /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
          "li",
          {
            className: "edit-widgets-keyboard-shortcut-help-modal__shortcut",
            children: typeof shortcut === "string" ? /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(dynamic_shortcut_default, { name: shortcut }) : /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(shortcut_default, { ...shortcut })
          },
          index
        ))
      }
    )
  );
  var ShortcutSection = ({ title, shortcuts, className }) => /* @__PURE__ */ (0, import_jsx_runtime35.jsxs)(
    "section",
    {
      className: clsx_default(
        "edit-widgets-keyboard-shortcut-help-modal__section",
        className
      ),
      children: [
        !!title && /* @__PURE__ */ (0, import_jsx_runtime35.jsx)("h2", { className: "edit-widgets-keyboard-shortcut-help-modal__section-title", children: title }),
        /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(ShortcutList, { shortcuts })
      ]
    }
  );
  var ShortcutCategorySection = ({
    title,
    categoryName,
    additionalShortcuts = []
  }) => {
    const categoryShortcuts = (0, import_data21.useSelect)(
      (select) => {
        return select(import_keyboard_shortcuts4.store).getCategoryShortcuts(
          categoryName
        );
      },
      [categoryName]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
      ShortcutSection,
      {
        title,
        shortcuts: categoryShortcuts.concat(additionalShortcuts)
      }
    );
  };
  function KeyboardShortcutHelpModal({
    isModalActive: isModalActive2,
    toggleModal
  }) {
    (0, import_keyboard_shortcuts4.useShortcut)("core/edit-widgets/keyboard-shortcuts", toggleModal, {
      bindGlobal: true
    });
    if (!isModalActive2) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime35.jsxs)(
      import_components16.Modal,
      {
        className: "edit-widgets-keyboard-shortcut-help-modal",
        title: (0, import_i18n14.__)("Keyboard shortcuts"),
        onRequestClose: toggleModal,
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
            ShortcutSection,
            {
              className: "edit-widgets-keyboard-shortcut-help-modal__main-shortcuts",
              shortcuts: ["core/edit-widgets/keyboard-shortcuts"]
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
            ShortcutCategorySection,
            {
              title: (0, import_i18n14.__)("Global shortcuts"),
              categoryName: "global"
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
            ShortcutCategorySection,
            {
              title: (0, import_i18n14.__)("Selection shortcuts"),
              categoryName: "selection"
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
            ShortcutCategorySection,
            {
              title: (0, import_i18n14.__)("Block shortcuts"),
              categoryName: "block",
              additionalShortcuts: [
                {
                  keyCombination: { character: "/" },
                  description: (0, import_i18n14.__)(
                    "Change the block type after adding a new paragraph."
                  ),
                  /* translators: The forward-slash character. e.g. '/'. */
                  ariaLabel: (0, import_i18n14.__)("Forward-slash")
                }
              ]
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
            ShortcutSection,
            {
              title: (0, import_i18n14.__)("Text formatting"),
              shortcuts: textFormattingShortcuts
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime35.jsx)(
            ShortcutCategorySection,
            {
              title: (0, import_i18n14.__)("List View shortcuts"),
              categoryName: "list-view"
            }
          )
        ]
      }
    );
  }

  // packages/edit-widgets/build-module/components/more-menu/tools-more-menu-group.js
  var import_components17 = __toESM(require_components());
  var import_jsx_runtime36 = __toESM(require_jsx_runtime());
  var { Fill: ToolsMoreMenuGroup, Slot: Slot4 } = (0, import_components17.createSlotFill)(
    "EditWidgetsToolsMoreMenuGroup"
  );
  ToolsMoreMenuGroup.Slot = ({ fillProps }) => /* @__PURE__ */ (0, import_jsx_runtime36.jsx)(Slot4, { fillProps, children: (fills) => fills.length > 0 && fills });
  var tools_more_menu_group_default = ToolsMoreMenuGroup;

  // packages/edit-widgets/build-module/components/more-menu/index.js
  var import_jsx_runtime37 = __toESM(require_jsx_runtime());
  function MoreMenu() {
    const [
      isKeyboardShortcutsModalActive,
      setIsKeyboardShortcutsModalVisible
    ] = (0, import_element17.useState)(false);
    const toggleKeyboardShortcutsModal = () => setIsKeyboardShortcutsModalVisible(!isKeyboardShortcutsModalActive);
    (0, import_keyboard_shortcuts5.useShortcut)(
      "core/edit-widgets/keyboard-shortcuts",
      toggleKeyboardShortcutsModal
    );
    const isLargeViewport = (0, import_compose7.useViewportMatch)("medium");
    return /* @__PURE__ */ (0, import_jsx_runtime37.jsxs)(import_jsx_runtime37.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
        import_components18.DropdownMenu,
        {
          icon: more_vertical_default,
          label: (0, import_i18n15.__)("Options"),
          popoverProps: {
            placement: "bottom-end",
            className: "more-menu-dropdown__content"
          },
          toggleProps: {
            tooltipPosition: "bottom",
            size: "compact"
          },
          children: (onClose) => /* @__PURE__ */ (0, import_jsx_runtime37.jsxs)(import_jsx_runtime37.Fragment, { children: [
            isLargeViewport && /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(import_components18.MenuGroup, { label: (0, import_i18n15._x)("View", "noun"), children: /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
              import_preferences5.PreferenceToggleMenuItem,
              {
                scope: "core/edit-widgets",
                name: "fixedToolbar",
                label: (0, import_i18n15.__)("Top toolbar"),
                info: (0, import_i18n15.__)(
                  "Access all block and document tools in a single place"
                ),
                messageActivated: (0, import_i18n15.__)(
                  "Top toolbar activated"
                ),
                messageDeactivated: (0, import_i18n15.__)(
                  "Top toolbar deactivated"
                )
              }
            ) }),
            /* @__PURE__ */ (0, import_jsx_runtime37.jsxs)(import_components18.MenuGroup, { label: (0, import_i18n15.__)("Tools"), children: [
              /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
                import_components18.MenuItem,
                {
                  onClick: () => {
                    setIsKeyboardShortcutsModalVisible(true);
                  },
                  shortcut: import_keycodes5.displayShortcut.access("h"),
                  children: (0, import_i18n15.__)("Keyboard shortcuts")
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
                import_preferences5.PreferenceToggleMenuItem,
                {
                  scope: "core/edit-widgets",
                  name: "welcomeGuide",
                  label: (0, import_i18n15.__)("Welcome Guide")
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime37.jsxs)(
                import_components18.MenuItem,
                {
                  role: "menuitem",
                  icon: external_default,
                  href: (0, import_i18n15.__)(
                    "https://wordpress.org/documentation/article/block-based-widgets-editor/"
                  ),
                  target: "_blank",
                  rel: "noopener noreferrer",
                  children: [
                    (0, import_i18n15.__)("Help"),
                    /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(import_components18.VisuallyHidden, {
                      as: "span",
                      /* translators: accessibility text */
                      children: (0, import_i18n15.__)("(opens in a new tab)")
                    })
                  ]
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
                tools_more_menu_group_default.Slot,
                {
                  fillProps: { onClose }
                }
              )
            ] }),
            /* @__PURE__ */ (0, import_jsx_runtime37.jsxs)(import_components18.MenuGroup, { label: (0, import_i18n15.__)("Preferences"), children: [
              /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
                import_preferences5.PreferenceToggleMenuItem,
                {
                  scope: "core/edit-widgets",
                  name: "keepCaretInsideBlock",
                  label: (0, import_i18n15.__)(
                    "Contain text cursor inside block"
                  ),
                  info: (0, import_i18n15.__)(
                    "Aids screen readers by stopping text caret from leaving blocks."
                  ),
                  messageActivated: (0, import_i18n15.__)(
                    "Contain text cursor inside block activated"
                  ),
                  messageDeactivated: (0, import_i18n15.__)(
                    "Contain text cursor inside block deactivated"
                  )
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
                import_preferences5.PreferenceToggleMenuItem,
                {
                  scope: "core/edit-widgets",
                  name: "themeStyles",
                  info: (0, import_i18n15.__)(
                    "Make the editor look like your theme."
                  ),
                  label: (0, import_i18n15.__)("Use theme styles")
                }
              ),
              isLargeViewport && /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
                import_preferences5.PreferenceToggleMenuItem,
                {
                  scope: "core/edit-widgets",
                  name: "showBlockBreadcrumbs",
                  label: (0, import_i18n15.__)("Display block breadcrumbs"),
                  info: (0, import_i18n15.__)(
                    "Shows block breadcrumbs at the bottom of the editor."
                  ),
                  messageActivated: (0, import_i18n15.__)(
                    "Display block breadcrumbs activated"
                  ),
                  messageDeactivated: (0, import_i18n15.__)(
                    "Display block breadcrumbs deactivated"
                  )
                }
              )
            ] })
          ] })
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime37.jsx)(
        KeyboardShortcutHelpModal,
        {
          isModalActive: isKeyboardShortcutsModalActive,
          toggleModal: toggleKeyboardShortcutsModal
        }
      )
    ] });
  }

  // packages/edit-widgets/build-module/components/header/index.js
  var import_jsx_runtime38 = __toESM(require_jsx_runtime());
  function Header() {
    const isLargeViewport = (0, import_compose8.useViewportMatch)("medium");
    const blockToolbarRef = (0, import_element18.useRef)();
    const { hasFixedToolbar } = (0, import_data22.useSelect)(
      (select) => ({
        hasFixedToolbar: !!select(import_preferences6.store).get(
          "core/edit-widgets",
          "fixedToolbar"
        )
      }),
      []
    );
    return /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(import_jsx_runtime38.Fragment, { children: /* @__PURE__ */ (0, import_jsx_runtime38.jsxs)("div", { className: "edit-widgets-header", children: [
      /* @__PURE__ */ (0, import_jsx_runtime38.jsxs)("div", { className: "edit-widgets-header__navigable-toolbar-wrapper", children: [
        isLargeViewport && /* @__PURE__ */ (0, import_jsx_runtime38.jsx)("h1", { className: "edit-widgets-header__title", children: (0, import_i18n16.__)("Widgets") }),
        !isLargeViewport && /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
          import_components19.VisuallyHidden,
          {
            as: "h1",
            className: "edit-widgets-header__title",
            children: (0, import_i18n16.__)("Widgets")
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(document_tools_default, {}),
        hasFixedToolbar && isLargeViewport && /* @__PURE__ */ (0, import_jsx_runtime38.jsxs)(import_jsx_runtime38.Fragment, { children: [
          /* @__PURE__ */ (0, import_jsx_runtime38.jsx)("div", { className: "selected-block-tools-wrapper", children: /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(import_block_editor12.BlockToolbar, { hideDragHandle: true }) }),
          /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(
            import_components19.Popover.Slot,
            {
              ref: blockToolbarRef,
              name: "block-toolbar"
            }
          )
        ] })
      ] }),
      /* @__PURE__ */ (0, import_jsx_runtime38.jsxs)("div", { className: "edit-widgets-header__actions", children: [
        /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(pinned_items_default.Slot, { scope: "core/edit-widgets" }),
        /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(save_button_default, {}),
        /* @__PURE__ */ (0, import_jsx_runtime38.jsx)(MoreMenu, {})
      ] })
    ] }) });
  }
  var header_default = Header;

  // packages/edit-widgets/build-module/components/widget-areas-block-editor-content/index.js
  var import_block_editor13 = __toESM(require_block_editor());
  var import_compose9 = __toESM(require_compose());
  var import_data24 = __toESM(require_data());
  var import_element19 = __toESM(require_element());
  var import_preferences7 = __toESM(require_preferences());

  // packages/edit-widgets/build-module/components/notices/index.js
  var import_components20 = __toESM(require_components());
  var import_data23 = __toESM(require_data());
  var import_notices2 = __toESM(require_notices());
  var import_jsx_runtime39 = __toESM(require_jsx_runtime());
  var MAX_VISIBLE_NOTICES = -3;
  function Notices() {
    const { removeNotice } = (0, import_data23.useDispatch)(import_notices2.store);
    const { notices } = (0, import_data23.useSelect)((select) => {
      return {
        notices: select(import_notices2.store).getNotices()
      };
    }, []);
    const dismissibleNotices = notices.filter(
      ({ isDismissible, type }) => isDismissible && type === "default"
    );
    const nonDismissibleNotices = notices.filter(
      ({ isDismissible, type }) => !isDismissible && type === "default"
    );
    const snackbarNotices = notices.filter(({ type }) => type === "snackbar").slice(MAX_VISIBLE_NOTICES);
    return /* @__PURE__ */ (0, import_jsx_runtime39.jsxs)(import_jsx_runtime39.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime39.jsx)(
        import_components20.NoticeList,
        {
          notices: nonDismissibleNotices,
          className: "edit-widgets-notices__pinned"
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime39.jsx)(
        import_components20.NoticeList,
        {
          notices: dismissibleNotices,
          className: "edit-widgets-notices__dismissible",
          onRemove: removeNotice
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime39.jsx)(
        import_components20.SnackbarList,
        {
          notices: snackbarNotices,
          className: "edit-widgets-notices__snackbar",
          onRemove: removeNotice
        }
      )
    ] });
  }
  var notices_default = Notices;

  // packages/edit-widgets/build-module/components/widget-areas-block-editor-content/index.js
  var import_jsx_runtime40 = __toESM(require_jsx_runtime());
  function WidgetAreasBlockEditorContent({
    blockEditorSettings
  }) {
    const hasThemeStyles = (0, import_data24.useSelect)(
      (select) => !!select(import_preferences7.store).get(
        "core/edit-widgets",
        "themeStyles"
      ),
      []
    );
    const isLargeViewport = (0, import_compose9.useViewportMatch)("medium");
    const styles = (0, import_element19.useMemo)(() => {
      return hasThemeStyles ? blockEditorSettings.styles : [];
    }, [blockEditorSettings, hasThemeStyles]);
    return /* @__PURE__ */ (0, import_jsx_runtime40.jsxs)("div", { className: "edit-widgets-block-editor", children: [
      /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(notices_default, {}),
      !isLargeViewport && /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(import_block_editor13.BlockToolbar, { hideDragHandle: true }),
      /* @__PURE__ */ (0, import_jsx_runtime40.jsxs)(import_block_editor13.BlockTools, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(keyboard_shortcuts_default, {}),
        /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(
          import_block_editor13.__unstableEditorStyles,
          {
            styles,
            scope: ":where(.editor-styles-wrapper)"
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(import_block_editor13.BlockSelectionClearer, { children: /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(import_block_editor13.WritingFlow, { children: /* @__PURE__ */ (0, import_jsx_runtime40.jsx)(import_block_editor13.BlockList, { className: "edit-widgets-main-block-list" }) }) })
      ] })
    ] });
  }

  // packages/edit-widgets/build-module/components/secondary-sidebar/index.js
  var import_data28 = __toESM(require_data());

  // packages/edit-widgets/build-module/components/secondary-sidebar/inserter-sidebar.js
  var import_block_editor15 = __toESM(require_block_editor());
  var import_compose10 = __toESM(require_compose());
  var import_element20 = __toESM(require_element());
  var import_data26 = __toESM(require_data());

  // packages/edit-widgets/build-module/hooks/use-widget-library-insertion-point.js
  var import_data25 = __toESM(require_data());
  var import_block_editor14 = __toESM(require_block_editor());
  var import_core_data11 = __toESM(require_core_data());
  var useWidgetLibraryInsertionPoint = () => {
    const firstRootId = (0, import_data25.useSelect)((select) => {
      const { getEntityRecord } = select(import_core_data11.store);
      const widgetAreasPost = getEntityRecord(
        KIND,
        POST_TYPE,
        buildWidgetAreasPostId()
      );
      return widgetAreasPost?.blocks[0]?.clientId;
    }, []);
    return (0, import_data25.useSelect)(
      (select) => {
        const {
          getBlockRootClientId,
          getBlockSelectionEnd,
          getBlockOrder,
          getBlockIndex
        } = select(import_block_editor14.store);
        const insertionPoint = select(store2).__experimentalGetInsertionPoint();
        if (insertionPoint.rootClientId) {
          return insertionPoint;
        }
        const clientId = getBlockSelectionEnd() || firstRootId;
        const rootClientId = getBlockRootClientId(clientId);
        if (clientId && rootClientId === "") {
          return {
            rootClientId: clientId,
            insertionIndex: getBlockOrder(clientId).length
          };
        }
        return {
          rootClientId,
          insertionIndex: getBlockIndex(clientId) + 1
        };
      },
      [firstRootId]
    );
  };
  var use_widget_library_insertion_point_default = useWidgetLibraryInsertionPoint;

  // packages/edit-widgets/build-module/components/secondary-sidebar/inserter-sidebar.js
  var import_jsx_runtime41 = __toESM(require_jsx_runtime());
  function InserterSidebar() {
    const isMobileViewport = (0, import_compose10.useViewportMatch)("medium", "<");
    const { rootClientId, insertionIndex } = use_widget_library_insertion_point_default();
    const { setIsInserterOpened: setIsInserterOpened2 } = (0, import_data26.useDispatch)(store2);
    const closeInserter = (0, import_element20.useCallback)(() => {
      return setIsInserterOpened2(false);
    }, [setIsInserterOpened2]);
    const libraryRef = (0, import_element20.useRef)();
    return /* @__PURE__ */ (0, import_jsx_runtime41.jsx)("div", { className: "edit-widgets-layout__inserter-panel", children: /* @__PURE__ */ (0, import_jsx_runtime41.jsx)("div", { className: "edit-widgets-layout__inserter-panel-content", children: /* @__PURE__ */ (0, import_jsx_runtime41.jsx)(
      import_block_editor15.__experimentalLibrary,
      {
        showInserterHelpPanel: true,
        shouldFocusBlock: isMobileViewport,
        rootClientId,
        __experimentalInsertionIndex: insertionIndex,
        ref: libraryRef,
        onClose: closeInserter
      }
    ) }) });
  }

  // packages/edit-widgets/build-module/components/secondary-sidebar/list-view-sidebar.js
  var import_block_editor16 = __toESM(require_block_editor());
  var import_components21 = __toESM(require_components());
  var import_compose11 = __toESM(require_compose());
  var import_data27 = __toESM(require_data());
  var import_element21 = __toESM(require_element());
  var import_i18n17 = __toESM(require_i18n());
  var import_keycodes6 = __toESM(require_keycodes());
  var import_jsx_runtime42 = __toESM(require_jsx_runtime());
  function ListViewSidebar() {
    const { setIsListViewOpened: setIsListViewOpened2 } = (0, import_data27.useDispatch)(store2);
    const { getListViewToggleRef: getListViewToggleRef2 } = unlock((0, import_data27.useSelect)(store2));
    const [dropZoneElement, setDropZoneElement] = (0, import_element21.useState)(null);
    const focusOnMountRef = (0, import_compose11.useFocusOnMount)("firstElement");
    const closeListView = (0, import_element21.useCallback)(() => {
      setIsListViewOpened2(false);
      getListViewToggleRef2().current?.focus();
    }, [getListViewToggleRef2, setIsListViewOpened2]);
    const closeOnEscape = (0, import_element21.useCallback)(
      (event) => {
        if (event.keyCode === import_keycodes6.ESCAPE && !event.defaultPrevented) {
          event.preventDefault();
          closeListView();
        }
      },
      [closeListView]
    );
    return (
      // eslint-disable-next-line jsx-a11y/no-static-element-interactions
      /* @__PURE__ */ (0, import_jsx_runtime42.jsxs)(
        "div",
        {
          className: "edit-widgets-editor__list-view-panel",
          onKeyDown: closeOnEscape,
          children: [
            /* @__PURE__ */ (0, import_jsx_runtime42.jsxs)("div", { className: "edit-widgets-editor__list-view-panel-header", children: [
              /* @__PURE__ */ (0, import_jsx_runtime42.jsx)("strong", { children: (0, import_i18n17.__)("List View") }),
              /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(
                import_components21.Button,
                {
                  icon: close_small_default,
                  label: (0, import_i18n17.__)("Close"),
                  onClick: closeListView,
                  size: "compact"
                }
              )
            ] }),
            /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(
              "div",
              {
                className: "edit-widgets-editor__list-view-panel-content",
                ref: (0, import_compose11.useMergeRefs)([focusOnMountRef, setDropZoneElement]),
                children: /* @__PURE__ */ (0, import_jsx_runtime42.jsx)(import_block_editor16.__experimentalListView, { dropZoneElement })
              }
            )
          ]
        }
      )
    );
  }

  // packages/edit-widgets/build-module/components/secondary-sidebar/index.js
  var import_jsx_runtime43 = __toESM(require_jsx_runtime());
  function SecondarySidebar() {
    const { isInserterOpen, isListViewOpen } = (0, import_data28.useSelect)((select) => {
      const { isInserterOpened: isInserterOpened2, isListViewOpened: isListViewOpened2 } = select(store2);
      return {
        isInserterOpen: isInserterOpened2(),
        isListViewOpen: isListViewOpened2()
      };
    }, []);
    if (isInserterOpen) {
      return /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(InserterSidebar, {});
    }
    if (isListViewOpen) {
      return /* @__PURE__ */ (0, import_jsx_runtime43.jsx)(ListViewSidebar, {});
    }
    return null;
  }

  // packages/edit-widgets/build-module/components/layout/interface.js
  var import_jsx_runtime44 = __toESM(require_jsx_runtime());
  var interfaceLabels = {
    /* translators: accessibility text for the widgets screen top bar landmark region. */
    header: (0, import_i18n18.__)("Widgets top bar"),
    /* translators: accessibility text for the widgets screen content landmark region. */
    body: (0, import_i18n18.__)("Widgets and blocks"),
    /* translators: accessibility text for the widgets screen settings landmark region. */
    sidebar: (0, import_i18n18.__)("Widgets settings"),
    /* translators: accessibility text for the widgets screen footer landmark region. */
    footer: (0, import_i18n18.__)("Widgets footer")
  };
  function Interface({ blockEditorSettings }) {
    const isMobileViewport = (0, import_compose12.useViewportMatch)("medium", "<");
    const isHugeViewport = (0, import_compose12.useViewportMatch)("huge", ">=");
    const { setIsInserterOpened: setIsInserterOpened2, setIsListViewOpened: setIsListViewOpened2, closeGeneralSidebar: closeGeneralSidebar2 } = (0, import_data29.useDispatch)(store2);
    const {
      hasBlockBreadCrumbsEnabled,
      hasSidebarEnabled,
      isInserterOpened: isInserterOpened2,
      isListViewOpened: isListViewOpened2
    } = (0, import_data29.useSelect)(
      (select) => ({
        hasSidebarEnabled: !!select(
          store
        ).getActiveComplementaryArea(store2.name),
        isInserterOpened: !!select(store2).isInserterOpened(),
        isListViewOpened: !!select(store2).isListViewOpened(),
        hasBlockBreadCrumbsEnabled: !!select(import_preferences8.store).get(
          "core/edit-widgets",
          "showBlockBreadcrumbs"
        )
      }),
      []
    );
    (0, import_element22.useEffect)(() => {
      if (hasSidebarEnabled && !isHugeViewport) {
        setIsInserterOpened2(false);
        setIsListViewOpened2(false);
      }
    }, [hasSidebarEnabled, isHugeViewport]);
    (0, import_element22.useEffect)(() => {
      if ((isInserterOpened2 || isListViewOpened2) && !isHugeViewport) {
        closeGeneralSidebar2();
      }
    }, [isInserterOpened2, isListViewOpened2, isHugeViewport]);
    const secondarySidebarLabel = isListViewOpened2 ? (0, import_i18n18.__)("List View") : (0, import_i18n18.__)("Block Library");
    const hasSecondarySidebar = isListViewOpened2 || isInserterOpened2;
    return /* @__PURE__ */ (0, import_jsx_runtime44.jsx)(
      interface_skeleton_default,
      {
        labels: {
          ...interfaceLabels,
          secondarySidebar: secondarySidebarLabel
        },
        header: /* @__PURE__ */ (0, import_jsx_runtime44.jsx)(header_default, {}),
        secondarySidebar: hasSecondarySidebar && /* @__PURE__ */ (0, import_jsx_runtime44.jsx)(SecondarySidebar, {}),
        sidebar: /* @__PURE__ */ (0, import_jsx_runtime44.jsx)(complementary_area_default.Slot, { scope: "core/edit-widgets" }),
        content: /* @__PURE__ */ (0, import_jsx_runtime44.jsx)(import_jsx_runtime44.Fragment, { children: /* @__PURE__ */ (0, import_jsx_runtime44.jsx)(
          WidgetAreasBlockEditorContent,
          {
            blockEditorSettings
          }
        ) }),
        footer: hasBlockBreadCrumbsEnabled && !isMobileViewport && /* @__PURE__ */ (0, import_jsx_runtime44.jsx)("div", { className: "edit-widgets-layout__footer", children: /* @__PURE__ */ (0, import_jsx_runtime44.jsx)(import_block_editor17.BlockBreadcrumb, { rootLabelText: (0, import_i18n18.__)("Widgets") }) })
      }
    );
  }
  var interface_default = Interface;

  // packages/edit-widgets/build-module/components/layout/unsaved-changes-warning.js
  var import_i18n19 = __toESM(require_i18n());
  var import_element23 = __toESM(require_element());
  var import_data30 = __toESM(require_data());
  function UnsavedChangesWarning() {
    const isDirty = (0, import_data30.useSelect)((select) => {
      const { getEditedWidgetAreas: getEditedWidgetAreas2 } = select(store2);
      const editedWidgetAreas = getEditedWidgetAreas2();
      return editedWidgetAreas?.length > 0;
    }, []);
    (0, import_element23.useEffect)(() => {
      const warnIfUnsavedChanges = (event) => {
        if (isDirty) {
          event.returnValue = (0, import_i18n19.__)(
            "You have unsaved changes. If you proceed, they will be lost."
          );
          return event.returnValue;
        }
      };
      window.addEventListener("beforeunload", warnIfUnsavedChanges);
      return () => {
        window.removeEventListener("beforeunload", warnIfUnsavedChanges);
      };
    }, [isDirty]);
    return null;
  }

  // packages/edit-widgets/build-module/components/welcome-guide/index.js
  var import_data31 = __toESM(require_data());
  var import_components22 = __toESM(require_components());
  var import_i18n20 = __toESM(require_i18n());
  var import_element24 = __toESM(require_element());
  var import_preferences9 = __toESM(require_preferences());
  var import_jsx_runtime45 = __toESM(require_jsx_runtime());
  function WelcomeGuide() {
    const isActive = (0, import_data31.useSelect)(
      (select) => !!select(import_preferences9.store).get(
        "core/edit-widgets",
        "welcomeGuide"
      ),
      []
    );
    const { toggle } = (0, import_data31.useDispatch)(import_preferences9.store);
    const widgetAreas = (0, import_data31.useSelect)(
      (select) => select(store2).getWidgetAreas({ per_page: -1 }),
      []
    );
    if (!isActive) {
      return null;
    }
    const isEntirelyBlockWidgets = widgetAreas?.every(
      (widgetArea) => widgetArea.id === "wp_inactive_widgets" || widgetArea.widgets.every(
        (widgetId) => widgetId.startsWith("block-")
      )
    );
    const numWidgetAreas = widgetAreas?.filter(
      (widgetArea) => widgetArea.id !== "wp_inactive_widgets"
    ).length ?? 0;
    return /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(
      import_components22.Guide,
      {
        className: "edit-widgets-welcome-guide",
        contentLabel: (0, import_i18n20.__)("Welcome to block Widgets"),
        finishButtonText: (0, import_i18n20.__)("Get started"),
        onFinish: () => toggle("core/edit-widgets", "welcomeGuide"),
        pages: [
          {
            image: /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(
              WelcomeGuideImage,
              {
                nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.svg",
                animatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.gif"
              }
            ),
            content: /* @__PURE__ */ (0, import_jsx_runtime45.jsxs)(import_jsx_runtime45.Fragment, { children: [
              /* @__PURE__ */ (0, import_jsx_runtime45.jsx)("h1", { className: "edit-widgets-welcome-guide__heading", children: (0, import_i18n20.__)("Welcome to block Widgets") }),
              isEntirelyBlockWidgets ? /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(import_jsx_runtime45.Fragment, { children: /* @__PURE__ */ (0, import_jsx_runtime45.jsx)("p", { className: "edit-widgets-welcome-guide__text", children: (0, import_i18n20.sprintf)(
                // Translators: %s: Number of block areas in the current theme.
                (0, import_i18n20._n)(
                  "Your theme provides %s \u201Cblock\u201D area for you to add and edit content.\xA0Try adding a search bar, social icons, or other types of blocks here and see how they\u2019ll look on your site.",
                  "Your theme provides %s different \u201Cblock\u201D areas for you to add and edit content.\xA0Try adding a search bar, social icons, or other types of blocks here and see how they\u2019ll look on your site.",
                  numWidgetAreas
                ),
                numWidgetAreas
              ) }) }) : /* @__PURE__ */ (0, import_jsx_runtime45.jsxs)(import_jsx_runtime45.Fragment, { children: [
                /* @__PURE__ */ (0, import_jsx_runtime45.jsx)("p", { className: "edit-widgets-welcome-guide__text", children: (0, import_i18n20.__)(
                  "You can now add any block to your site\u2019s widget areas. Don\u2019t worry, all of your favorite widgets still work flawlessly."
                ) }),
                /* @__PURE__ */ (0, import_jsx_runtime45.jsxs)("p", { className: "edit-widgets-welcome-guide__text", children: [
                  /* @__PURE__ */ (0, import_jsx_runtime45.jsx)("strong", { children: (0, import_i18n20.__)(
                    "Want to stick with the old widgets?"
                  ) }),
                  " ",
                  /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(
                    import_components22.ExternalLink,
                    {
                      href: (0, import_i18n20.__)(
                        "https://wordpress.org/plugins/classic-widgets/"
                      ),
                      children: (0, import_i18n20.__)(
                        "Get the Classic Widgets plugin."
                      )
                    }
                  )
                ] })
              ] })
            ] })
          },
          {
            image: /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(
              WelcomeGuideImage,
              {
                nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-editor.svg",
                animatedSrc: "https://s.w.org/images/block-editor/welcome-editor.gif"
              }
            ),
            content: /* @__PURE__ */ (0, import_jsx_runtime45.jsxs)(import_jsx_runtime45.Fragment, { children: [
              /* @__PURE__ */ (0, import_jsx_runtime45.jsx)("h1", { className: "edit-widgets-welcome-guide__heading", children: (0, import_i18n20.__)("Customize each block") }),
              /* @__PURE__ */ (0, import_jsx_runtime45.jsx)("p", { className: "edit-widgets-welcome-guide__text", children: (0, import_i18n20.__)(
                "Each block comes with its own set of controls for changing things like color, width, and alignment. These will show and hide automatically when you have a block selected."
              ) })
            ] })
          },
          {
            image: /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(
              WelcomeGuideImage,
              {
                nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-library.svg",
                animatedSrc: "https://s.w.org/images/block-editor/welcome-library.gif"
              }
            ),
            content: /* @__PURE__ */ (0, import_jsx_runtime45.jsxs)(import_jsx_runtime45.Fragment, { children: [
              /* @__PURE__ */ (0, import_jsx_runtime45.jsx)("h1", { className: "edit-widgets-welcome-guide__heading", children: (0, import_i18n20.__)("Explore all blocks") }),
              /* @__PURE__ */ (0, import_jsx_runtime45.jsx)("p", { className: "edit-widgets-welcome-guide__text", children: (0, import_element24.createInterpolateElement)(
                (0, import_i18n20.__)(
                  "All of the blocks available to you live in the block library. You\u2019ll find it wherever you see the <InserterIconImage /> icon."
                ),
                {
                  InserterIconImage: /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(
                    "img",
                    {
                      className: "edit-widgets-welcome-guide__inserter-icon",
                      alt: (0, import_i18n20.__)("inserter"),
                      src: "data:image/svg+xml,%3Csvg width='18' height='18' viewBox='0 0 18 18' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='18' height='18' rx='2' fill='%231E1E1E'/%3E%3Cpath d='M9.22727 4V14M4 8.77273H14' stroke='white' stroke-width='1.5'/%3E%3C/svg%3E%0A"
                    }
                  )
                }
              ) })
            ] })
          },
          {
            image: /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(
              WelcomeGuideImage,
              {
                nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.svg",
                animatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.gif"
              }
            ),
            content: /* @__PURE__ */ (0, import_jsx_runtime45.jsxs)(import_jsx_runtime45.Fragment, { children: [
              /* @__PURE__ */ (0, import_jsx_runtime45.jsx)("h1", { className: "edit-widgets-welcome-guide__heading", children: (0, import_i18n20.__)("Learn more") }),
              /* @__PURE__ */ (0, import_jsx_runtime45.jsx)("p", { className: "edit-widgets-welcome-guide__text", children: (0, import_element24.createInterpolateElement)(
                (0, import_i18n20.__)(
                  "New to the block editor? Want to learn more about using it? <a>Here's a detailed guide.</a>"
                ),
                {
                  a: /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(
                    import_components22.ExternalLink,
                    {
                      href: (0, import_i18n20.__)(
                        "https://wordpress.org/documentation/article/wordpress-block-editor/"
                      )
                    }
                  )
                }
              ) })
            ] })
          }
        ]
      }
    );
  }
  function WelcomeGuideImage({ nonAnimatedSrc, animatedSrc }) {
    return /* @__PURE__ */ (0, import_jsx_runtime45.jsxs)("picture", { className: "edit-widgets-welcome-guide__image", children: [
      /* @__PURE__ */ (0, import_jsx_runtime45.jsx)(
        "source",
        {
          srcSet: nonAnimatedSrc,
          media: "(prefers-reduced-motion: reduce)"
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime45.jsx)("img", { src: animatedSrc, width: "312", height: "240", alt: "" })
    ] });
  }

  // packages/edit-widgets/build-module/components/layout/index.js
  var import_jsx_runtime46 = __toESM(require_jsx_runtime());
  function Layout({ blockEditorSettings }) {
    const { createErrorNotice } = (0, import_data32.useDispatch)(import_notices4.store);
    function onPluginAreaError(name2) {
      createErrorNotice(
        (0, import_i18n21.sprintf)(
          /* translators: %s: plugin name */
          (0, import_i18n21.__)(
            'The "%s" plugin has encountered an error and cannot be rendered.'
          ),
          name2
        )
      );
    }
    const navigateRegionsProps = (0, import_components23.__unstableUseNavigateRegions)();
    return /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(ErrorBoundary, { children: /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(
      "div",
      {
        className: navigateRegionsProps.className,
        ...navigateRegionsProps,
        ref: navigateRegionsProps.ref,
        children: /* @__PURE__ */ (0, import_jsx_runtime46.jsxs)(
          WidgetAreasBlockEditorProvider,
          {
            blockEditorSettings,
            children: [
              /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(interface_default, { blockEditorSettings }),
              /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(Sidebar, {}),
              /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(import_plugins3.PluginArea, { onError: onPluginAreaError }),
              /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(UnsavedChangesWarning, {}),
              /* @__PURE__ */ (0, import_jsx_runtime46.jsx)(WelcomeGuide, {})
            ]
          }
        )
      }
    ) });
  }
  var layout_default = Layout;

  // packages/edit-widgets/build-module/index.js
  var import_jsx_runtime47 = __toESM(require_jsx_runtime());
  var disabledBlocks = [
    "core/more",
    "core/freeform",
    "core/template-part",
    ...ALLOW_REUSABLE_BLOCKS ? [] : ["core/block"]
  ];
  function initializeEditor(id, settings2) {
    const target = document.getElementById(id);
    const root = (0, import_element25.createRoot)(target);
    const coreBlocks = (0, import_block_library2.__experimentalGetCoreBlocks)().filter((block) => {
      return !(disabledBlocks.includes(block.name) || block.name.startsWith("core/post") || block.name.startsWith("core/query") || block.name.startsWith("core/site") || block.name.startsWith("core/navigation"));
    });
    (0, import_data33.dispatch)(import_preferences10.store).setDefaults("core/edit-widgets", {
      fixedToolbar: false,
      welcomeGuide: true,
      showBlockBreadcrumbs: true,
      themeStyles: true
    });
    (0, import_data33.dispatch)(import_blocks3.store).reapplyBlockTypeFilters();
    (0, import_block_library2.registerCoreBlocks)(coreBlocks);
    (0, import_widgets5.registerLegacyWidgetBlock)();
    if (true) {
      (0, import_block_library2.__experimentalRegisterExperimentalCoreBlocks)({
        enableFSEBlocks: ENABLE_EXPERIMENTAL_FSE_BLOCKS
      });
    }
    (0, import_widgets5.registerLegacyWidgetVariations)(settings2);
    registerBlock(widget_area_exports);
    (0, import_widgets5.registerWidgetGroupBlock)();
    settings2.__experimentalFetchLinkSuggestions = (search, searchOptions) => (0, import_core_data12.__experimentalFetchLinkSuggestions)(search, searchOptions, settings2);
    (0, import_blocks3.setFreeformContentHandlerName)("core/html");
    root.render(
      /* @__PURE__ */ (0, import_jsx_runtime47.jsx)(import_element25.StrictMode, { children: /* @__PURE__ */ (0, import_jsx_runtime47.jsx)(layout_default, { blockEditorSettings: settings2 }) })
    );
    return root;
  }
  var initialize = initializeEditor;
  function reinitializeEditor() {
    (0, import_deprecated6.default)("wp.editWidgets.reinitializeEditor", {
      since: "6.2",
      version: "6.3"
    });
  }
  var registerBlock = (block) => {
    if (!block) {
      return;
    }
    const { metadata, settings: settings2, name: name2 } = block;
    if (metadata) {
      (0, import_blocks3.unstable__bootstrapServerSideBlockDefinitions)({ [name2]: metadata });
    }
    (0, import_blocks3.registerBlockType)(name2, settings2);
  };
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
