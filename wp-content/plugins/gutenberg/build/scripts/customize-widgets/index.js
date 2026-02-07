var wp;
(wp ||= {}).customizeWidgets = (() => {
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

  // package-external:@wordpress/widgets
  var require_widgets = __commonJS({
    "package-external:@wordpress/widgets"(exports, module) {
      module.exports = window.wp.widgets;
    }
  });

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

  // package-external:@wordpress/preferences
  var require_preferences = __commonJS({
    "package-external:@wordpress/preferences"(exports, module) {
      module.exports = window.wp.preferences;
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

  // package-external:@wordpress/block-editor
  var require_block_editor = __commonJS({
    "package-external:@wordpress/block-editor"(exports, module) {
      module.exports = window.wp.blockEditor;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/hooks
  var require_hooks = __commonJS({
    "package-external:@wordpress/hooks"(exports, module) {
      module.exports = window.wp.hooks;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // package-external:@wordpress/core-data
  var require_core_data = __commonJS({
    "package-external:@wordpress/core-data"(exports, module) {
      module.exports = window.wp.coreData;
    }
  });

  // package-external:@wordpress/media-utils
  var require_media_utils = __commonJS({
    "package-external:@wordpress/media-utils"(exports, module) {
      module.exports = window.wp.mediaUtils;
    }
  });

  // package-external:@wordpress/keycodes
  var require_keycodes = __commonJS({
    "package-external:@wordpress/keycodes"(exports, module) {
      module.exports = window.wp.keycodes;
    }
  });

  // package-external:@wordpress/primitives
  var require_primitives = __commonJS({
    "package-external:@wordpress/primitives"(exports, module) {
      module.exports = window.wp.primitives;
    }
  });

  // package-external:@wordpress/keyboard-shortcuts
  var require_keyboard_shortcuts = __commonJS({
    "package-external:@wordpress/keyboard-shortcuts"(exports, module) {
      module.exports = window.wp.keyboardShortcuts;
    }
  });

  // node_modules/fast-deep-equal/es6/index.js
  var require_es6 = __commonJS({
    "node_modules/fast-deep-equal/es6/index.js"(exports, module) {
      "use strict";
      module.exports = function equal(a, b) {
        if (a === b) return true;
        if (a && b && typeof a == "object" && typeof b == "object") {
          if (a.constructor !== b.constructor) return false;
          var length, i, keys;
          if (Array.isArray(a)) {
            length = a.length;
            if (length != b.length) return false;
            for (i = length; i-- !== 0; )
              if (!equal(a[i], b[i])) return false;
            return true;
          }
          if (a instanceof Map && b instanceof Map) {
            if (a.size !== b.size) return false;
            for (i of a.entries())
              if (!b.has(i[0])) return false;
            for (i of a.entries())
              if (!equal(i[1], b.get(i[0]))) return false;
            return true;
          }
          if (a instanceof Set && b instanceof Set) {
            if (a.size !== b.size) return false;
            for (i of a.entries())
              if (!b.has(i[0])) return false;
            return true;
          }
          if (ArrayBuffer.isView(a) && ArrayBuffer.isView(b)) {
            length = a.length;
            if (length != b.length) return false;
            for (i = length; i-- !== 0; )
              if (a[i] !== b[i]) return false;
            return true;
          }
          if (a.constructor === RegExp) return a.source === b.source && a.flags === b.flags;
          if (a.valueOf !== Object.prototype.valueOf) return a.valueOf() === b.valueOf();
          if (a.toString !== Object.prototype.toString) return a.toString() === b.toString();
          keys = Object.keys(a);
          length = keys.length;
          if (length !== Object.keys(b).length) return false;
          for (i = length; i-- !== 0; )
            if (!Object.prototype.hasOwnProperty.call(b, keys[i])) return false;
          for (i = length; i-- !== 0; ) {
            var key = keys[i];
            if (!equal(a[key], b[key])) return false;
          }
          return true;
        }
        return a !== a && b !== b;
      };
    }
  });

  // package-external:@wordpress/is-shallow-equal
  var require_is_shallow_equal = __commonJS({
    "package-external:@wordpress/is-shallow-equal"(exports, module) {
      module.exports = window.wp.isShallowEqual;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // package-external:@wordpress/dom
  var require_dom = __commonJS({
    "package-external:@wordpress/dom"(exports, module) {
      module.exports = window.wp.dom;
    }
  });

  // packages/customize-widgets/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    initialize: () => initialize,
    store: () => store
  });
  var import_element17 = __toESM(require_element());
  var import_block_library2 = __toESM(require_block_library());
  var import_widgets5 = __toESM(require_widgets());
  var import_blocks2 = __toESM(require_blocks());
  var import_data17 = __toESM(require_data());
  var import_preferences4 = __toESM(require_preferences());

  // packages/customize-widgets/build-module/components/customize-widgets/index.js
  var import_element16 = __toESM(require_element());
  var import_components8 = __toESM(require_components());

  // packages/customize-widgets/build-module/components/error-boundary/index.js
  var import_element = __toESM(require_element());
  var import_i18n = __toESM(require_i18n());
  var import_components = __toESM(require_components());
  var import_block_editor = __toESM(require_block_editor());
  var import_compose = __toESM(require_compose());
  var import_hooks = __toESM(require_hooks());
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  function CopyButton({ text, children }) {
    const ref = (0, import_compose.useCopyToClipboard)(text);
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.Button, { size: "compact", variant: "secondary", ref, children });
  }
  var ErrorBoundary = class extends import_element.Component {
    constructor() {
      super(...arguments);
      this.state = {
        error: null
      };
    }
    componentDidCatch(error) {
      this.setState({ error });
      (0, import_hooks.doAction)("editor.ErrorBoundary.errorLogged", error);
    }
    render() {
      const { error } = this.state;
      if (!error) {
        return this.props.children;
      }
      return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
        import_block_editor.Warning,
        {
          className: "customize-widgets-error-boundary",
          actions: [
            /* @__PURE__ */ (0, import_jsx_runtime.jsx)(CopyButton, { text: error.stack, children: (0, import_i18n.__)("Copy Error") }, "copy-error")
          ],
          children: (0, import_i18n.__)("The editor has encountered an unexpected error.")
        }
      );
    }
  };

  // packages/customize-widgets/build-module/components/sidebar-block-editor/index.js
  var import_compose3 = __toESM(require_compose());
  var import_core_data = __toESM(require_core_data());
  var import_data12 = __toESM(require_data());
  var import_element13 = __toESM(require_element());
  var import_block_editor8 = __toESM(require_block_editor());
  var import_media_utils = __toESM(require_media_utils());
  var import_preferences3 = __toESM(require_preferences());
  var import_block_library = __toESM(require_block_library());

  // packages/customize-widgets/build-module/components/block-inspector-button/index.js
  var import_element2 = __toESM(require_element());
  var import_i18n2 = __toESM(require_i18n());
  var import_components2 = __toESM(require_components());
  var import_data = __toESM(require_data());
  var import_block_editor2 = __toESM(require_block_editor());
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  function BlockInspectorButton({ inspector, closeMenu, ...props }) {
    const selectedBlockClientId = (0, import_data.useSelect)(
      (select) => select(import_block_editor2.store).getSelectedBlockClientId(),
      []
    );
    const selectedBlock = (0, import_element2.useMemo)(
      () => document.getElementById(`block-${selectedBlockClientId}`),
      [selectedBlockClientId]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
      import_components2.MenuItem,
      {
        onClick: () => {
          inspector.open({
            returnFocusWhenClose: selectedBlock
          });
          closeMenu();
        },
        ...props,
        children: (0, import_i18n2.__)("Show more settings")
      }
    );
  }
  var block_inspector_button_default = BlockInspectorButton;

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

  // packages/customize-widgets/build-module/components/header/index.js
  var import_components6 = __toESM(require_components());
  var import_block_editor4 = __toESM(require_block_editor());
  var import_element6 = __toESM(require_element());
  var import_keycodes3 = __toESM(require_keycodes());
  var import_i18n7 = __toESM(require_i18n());

  // packages/icons/build-module/library/close-small.js
  var import_primitives = __toESM(require_primitives());
  var import_jsx_runtime3 = __toESM(require_jsx_runtime());
  var close_small_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives.Path, { d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z" }) });

  // packages/icons/build-module/library/external.js
  var import_primitives2 = __toESM(require_primitives());
  var import_jsx_runtime4 = __toESM(require_jsx_runtime());
  var external_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives2.Path, { d: "M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z" }) });

  // packages/icons/build-module/library/more-vertical.js
  var import_primitives3 = __toESM(require_primitives());
  var import_jsx_runtime5 = __toESM(require_jsx_runtime());
  var more_vertical_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives3.Path, { d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z" }) });

  // packages/icons/build-module/library/plus.js
  var import_primitives4 = __toESM(require_primitives());
  var import_jsx_runtime6 = __toESM(require_jsx_runtime());
  var plus_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives4.Path, { d: "M11 12.5V17.5H12.5V12.5H17.5V11H12.5V6H11V11H6V12.5H11Z" }) });

  // packages/icons/build-module/library/redo.js
  var import_primitives5 = __toESM(require_primitives());
  var import_jsx_runtime7 = __toESM(require_jsx_runtime());
  var redo_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives5.Path, { d: "M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z" }) });

  // packages/icons/build-module/library/undo.js
  var import_primitives6 = __toESM(require_primitives());
  var import_jsx_runtime8 = __toESM(require_jsx_runtime());
  var undo_default = /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives6.Path, { d: "M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z" }) });

  // packages/customize-widgets/build-module/components/inserter/index.js
  var import_i18n3 = __toESM(require_i18n());
  var import_block_editor3 = __toESM(require_block_editor());
  var import_components3 = __toESM(require_components());
  var import_compose2 = __toESM(require_compose());
  var import_data4 = __toESM(require_data());

  // packages/customize-widgets/build-module/store/index.js
  var import_data3 = __toESM(require_data());

  // packages/customize-widgets/build-module/store/reducer.js
  var import_data2 = __toESM(require_data());
  function blockInserterPanel(state = false, action) {
    switch (action.type) {
      case "SET_IS_INSERTER_OPENED":
        return action.value;
    }
    return state;
  }
  var reducer_default = (0, import_data2.combineReducers)({
    blockInserterPanel
  });

  // packages/customize-widgets/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    __experimentalGetInsertionPoint: () => __experimentalGetInsertionPoint,
    isInserterOpened: () => isInserterOpened
  });
  var EMPTY_INSERTION_POINT = {
    rootClientId: void 0,
    insertionIndex: void 0
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

  // packages/customize-widgets/build-module/store/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    setIsInserterOpened: () => setIsInserterOpened
  });
  function setIsInserterOpened(value) {
    return {
      type: "SET_IS_INSERTER_OPENED",
      value
    };
  }

  // packages/customize-widgets/build-module/store/constants.js
  var STORE_NAME = "core/customize-widgets";

  // packages/customize-widgets/build-module/store/index.js
  var storeConfig = {
    reducer: reducer_default,
    selectors: selectors_exports,
    actions: actions_exports
  };
  var store = (0, import_data3.createReduxStore)(STORE_NAME, storeConfig);
  (0, import_data3.register)(store);

  // packages/customize-widgets/build-module/components/inserter/index.js
  var import_jsx_runtime9 = __toESM(require_jsx_runtime());
  function Inserter({ setIsOpened }) {
    const inserterTitleId = (0, import_compose2.useInstanceId)(
      Inserter,
      "customize-widget-layout__inserter-panel-title"
    );
    const insertionPoint = (0, import_data4.useSelect)(
      (select) => select(store).__experimentalGetInsertionPoint(),
      []
    );
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(
      "div",
      {
        className: "customize-widgets-layout__inserter-panel",
        "aria-labelledby": inserterTitleId,
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)("div", { className: "customize-widgets-layout__inserter-panel-header", children: [
            /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
              "h2",
              {
                id: inserterTitleId,
                className: "customize-widgets-layout__inserter-panel-header-title",
                children: (0, import_i18n3.__)("Add a block")
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
              import_components3.Button,
              {
                size: "small",
                icon: close_small_default,
                onClick: () => setIsOpened(false),
                "aria-label": (0, import_i18n3.__)("Close inserter")
              }
            )
          ] }),
          /* @__PURE__ */ (0, import_jsx_runtime9.jsx)("div", { className: "customize-widgets-layout__inserter-panel-content", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
            import_block_editor3.__experimentalLibrary,
            {
              rootClientId: insertionPoint.rootClientId,
              __experimentalInsertionIndex: insertionPoint.insertionIndex,
              showInserterHelpPanel: true,
              onSelect: () => setIsOpened(false)
            }
          ) })
        ]
      }
    );
  }
  var inserter_default = Inserter;

  // packages/customize-widgets/build-module/components/more-menu/index.js
  var import_components5 = __toESM(require_components());
  var import_element5 = __toESM(require_element());
  var import_i18n6 = __toESM(require_i18n());
  var import_keycodes2 = __toESM(require_keycodes());
  var import_keyboard_shortcuts3 = __toESM(require_keyboard_shortcuts());
  var import_preferences = __toESM(require_preferences());

  // packages/customize-widgets/build-module/components/keyboard-shortcut-help-modal/index.js
  var import_components4 = __toESM(require_components());
  var import_i18n5 = __toESM(require_i18n());
  var import_keyboard_shortcuts2 = __toESM(require_keyboard_shortcuts());
  var import_data6 = __toESM(require_data());
  var import_element4 = __toESM(require_element());

  // packages/customize-widgets/build-module/components/keyboard-shortcut-help-modal/config.js
  var import_i18n4 = __toESM(require_i18n());
  var textFormattingShortcuts = [
    {
      keyCombination: { modifier: "primary", character: "b" },
      description: (0, import_i18n4.__)("Make the selected text bold.")
    },
    {
      keyCombination: { modifier: "primary", character: "i" },
      description: (0, import_i18n4.__)("Make the selected text italic.")
    },
    {
      keyCombination: { modifier: "primary", character: "k" },
      description: (0, import_i18n4.__)("Convert the selected text into a link.")
    },
    {
      keyCombination: { modifier: "primaryShift", character: "k" },
      description: (0, import_i18n4.__)("Remove a link.")
    },
    {
      keyCombination: { character: "[[" },
      description: (0, import_i18n4.__)("Insert a link to a post or page.")
    },
    {
      keyCombination: { modifier: "primary", character: "u" },
      description: (0, import_i18n4.__)("Underline the selected text.")
    },
    {
      keyCombination: { modifier: "access", character: "d" },
      description: (0, import_i18n4.__)("Strikethrough the selected text.")
    },
    {
      keyCombination: { modifier: "access", character: "x" },
      description: (0, import_i18n4.__)("Make the selected text inline code.")
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
      description: (0, import_i18n4.__)("Convert the current heading to a paragraph.")
    },
    {
      keyCombination: { modifier: "access", character: "1-6" },
      description: (0, import_i18n4.__)(
        "Convert the current paragraph or heading to a heading of level 1 to 6."
      )
    },
    {
      keyCombination: { modifier: "primaryShift", character: "SPACE" },
      description: (0, import_i18n4.__)("Add non breaking space.")
    }
  ];

  // packages/customize-widgets/build-module/components/keyboard-shortcut-help-modal/shortcut.js
  var import_element3 = __toESM(require_element());
  var import_keycodes = __toESM(require_keycodes());
  var import_jsx_runtime10 = __toESM(require_jsx_runtime());
  function KeyCombination({ keyCombination, forceAriaLabel }) {
    const shortcut = keyCombination.modifier ? import_keycodes.displayShortcutList[keyCombination.modifier](
      keyCombination.character
    ) : keyCombination.character;
    const ariaLabel = keyCombination.modifier ? import_keycodes.shortcutAriaLabel[keyCombination.modifier](
      keyCombination.character
    ) : keyCombination.character;
    return /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
      "kbd",
      {
        className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-key-combination",
        "aria-label": forceAriaLabel || ariaLabel,
        children: (Array.isArray(shortcut) ? shortcut : [shortcut]).map(
          (character, index) => {
            if (character === "+") {
              return /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_element3.Fragment, { children: character }, index);
            }
            return /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
              "kbd",
              {
                className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-key",
                children: character
              },
              index
            );
          }
        )
      }
    );
  }
  function Shortcut({ description, keyCombination, aliases = [], ariaLabel }) {
    return /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)(import_jsx_runtime10.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime10.jsx)("div", { className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-description", children: description }),
      /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)("div", { className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-term", children: [
        /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
          KeyCombination,
          {
            keyCombination,
            forceAriaLabel: ariaLabel
          }
        ),
        aliases.map((alias, index) => /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
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

  // packages/customize-widgets/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.js
  var import_data5 = __toESM(require_data());
  var import_keyboard_shortcuts = __toESM(require_keyboard_shortcuts());
  var import_jsx_runtime11 = __toESM(require_jsx_runtime());
  function DynamicShortcut({ name }) {
    const { keyCombination, description, aliases } = (0, import_data5.useSelect)(
      (select) => {
        const {
          getShortcutKeyCombination,
          getShortcutDescription,
          getShortcutAliases
        } = select(import_keyboard_shortcuts.store);
        return {
          keyCombination: getShortcutKeyCombination(name),
          aliases: getShortcutAliases(name),
          description: getShortcutDescription(name)
        };
      },
      [name]
    );
    if (!keyCombination) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
      shortcut_default,
      {
        keyCombination,
        description,
        aliases
      }
    );
  }
  var dynamic_shortcut_default = DynamicShortcut;

  // packages/customize-widgets/build-module/components/keyboard-shortcut-help-modal/index.js
  var import_jsx_runtime12 = __toESM(require_jsx_runtime());
  var ShortcutList = ({ shortcuts }) => (
    /*
     * Disable reason: The `list` ARIA role is redundant but
     * Safari+VoiceOver won't announce the list otherwise.
     */
    /* eslint-disable jsx-a11y/no-redundant-roles */
    /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
      "ul",
      {
        className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-list",
        role: "list",
        children: shortcuts.map((shortcut, index) => /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
          "li",
          {
            className: "customize-widgets-keyboard-shortcut-help-modal__shortcut",
            children: typeof shortcut === "string" ? /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(dynamic_shortcut_default, { name: shortcut }) : /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(shortcut_default, { ...shortcut })
          },
          index
        ))
      }
    )
  );
  var ShortcutSection = ({ title, shortcuts, className }) => /* @__PURE__ */ (0, import_jsx_runtime12.jsxs)(
    "section",
    {
      className: clsx_default(
        "customize-widgets-keyboard-shortcut-help-modal__section",
        className
      ),
      children: [
        !!title && /* @__PURE__ */ (0, import_jsx_runtime12.jsx)("h2", { className: "customize-widgets-keyboard-shortcut-help-modal__section-title", children: title }),
        /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(ShortcutList, { shortcuts })
      ]
    }
  );
  var ShortcutCategorySection = ({
    title,
    categoryName,
    additionalShortcuts = []
  }) => {
    const categoryShortcuts = (0, import_data6.useSelect)(
      (select) => {
        return select(import_keyboard_shortcuts2.store).getCategoryShortcuts(
          categoryName
        );
      },
      [categoryName]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
      ShortcutSection,
      {
        title,
        shortcuts: categoryShortcuts.concat(additionalShortcuts)
      }
    );
  };
  function KeyboardShortcutHelpModal({
    isModalActive,
    toggleModal
  }) {
    const { registerShortcut } = (0, import_data6.useDispatch)(import_keyboard_shortcuts2.store);
    (0, import_element4.useEffect)(() => {
      registerShortcut({
        name: "core/customize-widgets/keyboard-shortcuts",
        category: "main",
        description: (0, import_i18n5.__)("Display these keyboard shortcuts."),
        keyCombination: {
          modifier: "access",
          character: "h"
        }
      });
    }, [registerShortcut]);
    (0, import_keyboard_shortcuts2.useShortcut)("core/customize-widgets/keyboard-shortcuts", toggleModal);
    if (!isModalActive) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime12.jsxs)(
      import_components4.Modal,
      {
        className: "customize-widgets-keyboard-shortcut-help-modal",
        title: (0, import_i18n5.__)("Keyboard shortcuts"),
        onRequestClose: toggleModal,
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
            ShortcutSection,
            {
              className: "customize-widgets-keyboard-shortcut-help-modal__main-shortcuts",
              shortcuts: ["core/customize-widgets/keyboard-shortcuts"]
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
            ShortcutCategorySection,
            {
              title: (0, import_i18n5.__)("Global shortcuts"),
              categoryName: "global"
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
            ShortcutCategorySection,
            {
              title: (0, import_i18n5.__)("Selection shortcuts"),
              categoryName: "selection"
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
            ShortcutCategorySection,
            {
              title: (0, import_i18n5.__)("Block shortcuts"),
              categoryName: "block",
              additionalShortcuts: [
                {
                  keyCombination: { character: "/" },
                  description: (0, import_i18n5.__)(
                    "Change the block type after adding a new paragraph."
                  ),
                  /* translators: The forward-slash character. e.g. '/'. */
                  ariaLabel: (0, import_i18n5.__)("Forward-slash")
                }
              ]
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
            ShortcutSection,
            {
              title: (0, import_i18n5.__)("Text formatting"),
              shortcuts: textFormattingShortcuts
            }
          )
        ]
      }
    );
  }

  // packages/customize-widgets/build-module/components/more-menu/index.js
  var import_jsx_runtime13 = __toESM(require_jsx_runtime());
  function MoreMenu() {
    const [
      isKeyboardShortcutsModalActive,
      setIsKeyboardShortcutsModalVisible
    ] = (0, import_element5.useState)(false);
    const toggleKeyboardShortcutsModal = () => setIsKeyboardShortcutsModalVisible(!isKeyboardShortcutsModalActive);
    (0, import_keyboard_shortcuts3.useShortcut)(
      "core/customize-widgets/keyboard-shortcuts",
      toggleKeyboardShortcutsModal
    );
    return /* @__PURE__ */ (0, import_jsx_runtime13.jsxs)(import_jsx_runtime13.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
        import_components5.ToolbarDropdownMenu,
        {
          icon: more_vertical_default,
          label: (0, import_i18n6.__)("Options"),
          popoverProps: {
            placement: "bottom-end",
            className: "more-menu-dropdown__content"
          },
          toggleProps: {
            tooltipPosition: "bottom",
            size: "compact"
          },
          children: () => /* @__PURE__ */ (0, import_jsx_runtime13.jsxs)(import_jsx_runtime13.Fragment, { children: [
            /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_components5.MenuGroup, { label: (0, import_i18n6._x)("View", "noun"), children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
              import_preferences.PreferenceToggleMenuItem,
              {
                scope: "core/customize-widgets",
                name: "fixedToolbar",
                label: (0, import_i18n6.__)("Top toolbar"),
                info: (0, import_i18n6.__)(
                  "Access all block and document tools in a single place"
                ),
                messageActivated: (0, import_i18n6.__)(
                  "Top toolbar activated"
                ),
                messageDeactivated: (0, import_i18n6.__)(
                  "Top toolbar deactivated"
                )
              }
            ) }),
            /* @__PURE__ */ (0, import_jsx_runtime13.jsxs)(import_components5.MenuGroup, { label: (0, import_i18n6.__)("Tools"), children: [
              /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
                import_components5.MenuItem,
                {
                  onClick: () => {
                    setIsKeyboardShortcutsModalVisible(true);
                  },
                  shortcut: import_keycodes2.displayShortcut.access("h"),
                  children: (0, import_i18n6.__)("Keyboard shortcuts")
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
                import_preferences.PreferenceToggleMenuItem,
                {
                  scope: "core/customize-widgets",
                  name: "welcomeGuide",
                  label: (0, import_i18n6.__)("Welcome Guide")
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime13.jsxs)(
                import_components5.MenuItem,
                {
                  role: "menuitem",
                  icon: external_default,
                  href: (0, import_i18n6.__)(
                    "https://wordpress.org/documentation/article/block-based-widgets-editor/"
                  ),
                  target: "_blank",
                  rel: "noopener noreferrer",
                  children: [
                    (0, import_i18n6.__)("Help"),
                    /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_components5.VisuallyHidden, {
                      as: "span",
                      /* translators: accessibility text */
                      children: (0, import_i18n6.__)("(opens in a new tab)")
                    })
                  ]
                }
              )
            ] }),
            /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_components5.MenuGroup, { label: (0, import_i18n6.__)("Preferences"), children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
              import_preferences.PreferenceToggleMenuItem,
              {
                scope: "core/customize-widgets",
                name: "keepCaretInsideBlock",
                label: (0, import_i18n6.__)(
                  "Contain text cursor inside block"
                ),
                info: (0, import_i18n6.__)(
                  "Aids screen readers by stopping text caret from leaving blocks."
                ),
                messageActivated: (0, import_i18n6.__)(
                  "Contain text cursor inside block activated"
                ),
                messageDeactivated: (0, import_i18n6.__)(
                  "Contain text cursor inside block deactivated"
                )
              }
            ) })
          ] })
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
        KeyboardShortcutHelpModal,
        {
          isModalActive: isKeyboardShortcutsModalActive,
          toggleModal: toggleKeyboardShortcutsModal
        }
      )
    ] });
  }

  // packages/customize-widgets/build-module/components/header/index.js
  var import_jsx_runtime14 = __toESM(require_jsx_runtime());
  function Header({
    sidebar,
    inserter,
    isInserterOpened: isInserterOpened2,
    setIsInserterOpened: setIsInserterOpened2,
    isFixedToolbarActive
  }) {
    const [[hasUndo, hasRedo], setUndoRedo] = (0, import_element6.useState)([
      sidebar.hasUndo(),
      sidebar.hasRedo()
    ]);
    const shortcut = (0, import_keycodes3.isAppleOS)() ? import_keycodes3.displayShortcut.primaryShift("z") : import_keycodes3.displayShortcut.primary("y");
    (0, import_element6.useEffect)(() => {
      return sidebar.subscribeHistory(() => {
        setUndoRedo([sidebar.hasUndo(), sidebar.hasRedo()]);
      });
    }, [sidebar]);
    return /* @__PURE__ */ (0, import_jsx_runtime14.jsxs)(import_jsx_runtime14.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
        "div",
        {
          className: clsx_default("customize-widgets-header", {
            "is-fixed-toolbar-active": isFixedToolbarActive
          }),
          children: /* @__PURE__ */ (0, import_jsx_runtime14.jsxs)(
            import_block_editor4.NavigableToolbar,
            {
              className: "customize-widgets-header-toolbar",
              "aria-label": (0, import_i18n7.__)("Document tools"),
              children: [
                /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
                  import_components6.ToolbarButton,
                  {
                    icon: !(0, import_i18n7.isRTL)() ? undo_default : redo_default,
                    label: (0, import_i18n7.__)("Undo"),
                    shortcut: import_keycodes3.displayShortcut.primary("z"),
                    disabled: !hasUndo,
                    onClick: sidebar.undo,
                    className: "customize-widgets-editor-history-button undo-button"
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
                  import_components6.ToolbarButton,
                  {
                    icon: !(0, import_i18n7.isRTL)() ? redo_default : undo_default,
                    label: (0, import_i18n7.__)("Redo"),
                    shortcut,
                    disabled: !hasRedo,
                    onClick: sidebar.redo,
                    className: "customize-widgets-editor-history-button redo-button"
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
                  import_components6.ToolbarButton,
                  {
                    className: "customize-widgets-header-toolbar__inserter-toggle",
                    isPressed: isInserterOpened2,
                    variant: "primary",
                    icon: plus_default,
                    label: (0, import_i18n7._x)(
                      "Add block",
                      "Generic label for block inserter button"
                    ),
                    onClick: () => {
                      setIsInserterOpened2((isOpen) => !isOpen);
                    }
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(MoreMenu, {})
              ]
            }
          )
        }
      ),
      (0, import_element6.createPortal)(
        /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(inserter_default, { setIsOpened: setIsInserterOpened2 }),
        inserter.contentContainer[0]
      )
    ] });
  }
  var header_default = Header;

  // packages/customize-widgets/build-module/components/inserter/use-inserter.js
  var import_element7 = __toESM(require_element());
  var import_data7 = __toESM(require_data());
  function useInserter(inserter) {
    const isInserterOpened2 = (0, import_data7.useSelect)(
      (select) => select(store).isInserterOpened(),
      []
    );
    const { setIsInserterOpened: setIsInserterOpened2 } = (0, import_data7.useDispatch)(store);
    (0, import_element7.useEffect)(() => {
      if (isInserterOpened2) {
        inserter.open();
      } else {
        inserter.close();
      }
    }, [inserter, isInserterOpened2]);
    return [
      isInserterOpened2,
      (0, import_element7.useCallback)(
        (updater) => {
          let isOpen = updater;
          if (typeof updater === "function") {
            isOpen = updater(
              (0, import_data7.select)(store).isInserterOpened()
            );
          }
          setIsInserterOpened2(isOpen);
        },
        [setIsInserterOpened2]
      )
    ];
  }

  // packages/customize-widgets/build-module/components/sidebar-block-editor/sidebar-editor-provider.js
  var import_block_editor6 = __toESM(require_block_editor());

  // packages/customize-widgets/build-module/components/sidebar-block-editor/use-sidebar-block-editor.js
  var import_es6 = __toESM(require_es6());
  var import_element8 = __toESM(require_element());
  var import_is_shallow_equal = __toESM(require_is_shallow_equal());
  var import_widgets2 = __toESM(require_widgets());

  // packages/customize-widgets/build-module/utils.js
  var import_blocks = __toESM(require_blocks());
  var import_widgets = __toESM(require_widgets());
  function settingIdToWidgetId(settingId) {
    const matches = settingId.match(/^widget_(.+)(?:\[(\d+)\])$/);
    if (matches) {
      const idBase = matches[1];
      const number = parseInt(matches[2], 10);
      return `${idBase}-${number}`;
    }
    return settingId;
  }
  function blockToWidget(block, existingWidget = null) {
    let widget;
    const isValidLegacyWidgetBlock = block.name === "core/legacy-widget" && (block.attributes.id || block.attributes.instance);
    if (isValidLegacyWidgetBlock) {
      if (block.attributes.id) {
        widget = {
          id: block.attributes.id
        };
      } else {
        const { encoded, hash, raw, ...rest } = block.attributes.instance;
        widget = {
          idBase: block.attributes.idBase,
          instance: {
            ...existingWidget?.instance,
            // Required only for the customizer.
            is_widget_customizer_js_value: true,
            encoded_serialized_instance: encoded,
            instance_hash_key: hash,
            raw_instance: raw,
            ...rest
          }
        };
      }
    } else {
      const instance = {
        content: (0, import_blocks.serialize)(block)
      };
      widget = {
        idBase: "block",
        widgetClass: "WP_Widget_Block",
        instance: {
          raw_instance: instance
        }
      };
    }
    const { form, rendered, ...restExistingWidget } = existingWidget || {};
    return {
      ...restExistingWidget,
      ...widget
    };
  }
  function widgetToBlock({ id, idBase, number, instance }) {
    let block;
    const {
      encoded_serialized_instance: encoded,
      instance_hash_key: hash,
      raw_instance: raw,
      ...rest
    } = instance;
    if (idBase === "block") {
      const parsedBlocks = (0, import_blocks.parse)(raw.content ?? "", {
        __unstableSkipAutop: true
      });
      block = parsedBlocks.length ? parsedBlocks[0] : (0, import_blocks.createBlock)("core/paragraph", {});
    } else if (number) {
      block = (0, import_blocks.createBlock)("core/legacy-widget", {
        idBase,
        instance: {
          encoded,
          hash,
          raw,
          ...rest
        }
      });
    } else {
      block = (0, import_blocks.createBlock)("core/legacy-widget", {
        id
      });
    }
    return (0, import_widgets.addWidgetIdToBlock)(block, id);
  }

  // packages/customize-widgets/build-module/components/sidebar-block-editor/use-sidebar-block-editor.js
  function widgetsToBlocks(widgets) {
    return widgets.map((widget) => widgetToBlock(widget));
  }
  function useSidebarBlockEditor(sidebar) {
    const [blocks, setBlocks] = (0, import_element8.useState)(
      () => widgetsToBlocks(sidebar.getWidgets())
    );
    (0, import_element8.useEffect)(() => {
      return sidebar.subscribe((prevWidgets, nextWidgets) => {
        setBlocks((prevBlocks) => {
          const prevWidgetsMap = new Map(
            prevWidgets.map((widget) => [widget.id, widget])
          );
          const prevBlocksMap = new Map(
            prevBlocks.map((block) => [
              (0, import_widgets2.getWidgetIdFromBlock)(block),
              block
            ])
          );
          const nextBlocks = nextWidgets.map((nextWidget) => {
            const prevWidget = prevWidgetsMap.get(nextWidget.id);
            if (prevWidget && prevWidget === nextWidget) {
              return prevBlocksMap.get(nextWidget.id);
            }
            return widgetToBlock(nextWidget);
          });
          if ((0, import_is_shallow_equal.default)(prevBlocks, nextBlocks)) {
            return prevBlocks;
          }
          return nextBlocks;
        });
      });
    }, [sidebar]);
    const onChangeBlocks = (0, import_element8.useCallback)(
      (nextBlocks) => {
        setBlocks((prevBlocks) => {
          if ((0, import_is_shallow_equal.default)(prevBlocks, nextBlocks)) {
            return prevBlocks;
          }
          const prevBlocksMap = new Map(
            prevBlocks.map((block) => [
              (0, import_widgets2.getWidgetIdFromBlock)(block),
              block
            ])
          );
          const nextWidgets = nextBlocks.map((nextBlock) => {
            const widgetId = (0, import_widgets2.getWidgetIdFromBlock)(nextBlock);
            if (widgetId && prevBlocksMap.has(widgetId)) {
              const prevBlock = prevBlocksMap.get(widgetId);
              const prevWidget = sidebar.getWidget(widgetId);
              if ((0, import_es6.default)(nextBlock, prevBlock) && prevWidget) {
                return prevWidget;
              }
              return blockToWidget(nextBlock, prevWidget);
            }
            return blockToWidget(nextBlock);
          });
          if ((0, import_is_shallow_equal.default)(sidebar.getWidgets(), nextWidgets)) {
            return prevBlocks;
          }
          const addedWidgetIds = sidebar.setWidgets(nextWidgets);
          return nextBlocks.reduce(
            (updatedNextBlocks, nextBlock, index) => {
              const addedWidgetId = addedWidgetIds[index];
              if (addedWidgetId !== null) {
                if (updatedNextBlocks === nextBlocks) {
                  updatedNextBlocks = nextBlocks.slice();
                }
                updatedNextBlocks[index] = (0, import_widgets2.addWidgetIdToBlock)(
                  nextBlock,
                  addedWidgetId
                );
              }
              return updatedNextBlocks;
            },
            nextBlocks
          );
        });
      },
      [sidebar]
    );
    return [blocks, onChangeBlocks, onChangeBlocks];
  }

  // packages/customize-widgets/build-module/components/focus-control/use-blocks-focus-control.js
  var import_element10 = __toESM(require_element());
  var import_data8 = __toESM(require_data());
  var import_block_editor5 = __toESM(require_block_editor());
  var import_widgets3 = __toESM(require_widgets());

  // packages/customize-widgets/build-module/components/focus-control/index.js
  var import_element9 = __toESM(require_element());
  var import_jsx_runtime15 = __toESM(require_jsx_runtime());
  var FocusControlContext = (0, import_element9.createContext)();
  FocusControlContext.displayName = "FocusControlContext";
  function FocusControl({ api, sidebarControls, children }) {
    const [focusedWidgetIdRef, setFocusedWidgetIdRef] = (0, import_element9.useState)({
      current: null
    });
    const focusWidget = (0, import_element9.useCallback)(
      (widgetId) => {
        for (const sidebarControl of sidebarControls) {
          const widgets = sidebarControl.setting.get();
          if (widgets.includes(widgetId)) {
            sidebarControl.sectionInstance.expand({
              // Schedule it after the complete callback so that
              // it won't be overridden by the "Back" button focus.
              completeCallback() {
                setFocusedWidgetIdRef({ current: widgetId });
              }
            });
            break;
          }
        }
      },
      [sidebarControls]
    );
    (0, import_element9.useEffect)(() => {
      function handleFocus(settingId) {
        const widgetId = settingIdToWidgetId(settingId);
        focusWidget(widgetId);
      }
      let previewBound = false;
      function handleReady() {
        api.previewer.preview.bind(
          "focus-control-for-setting",
          handleFocus
        );
        previewBound = true;
      }
      api.previewer.bind("ready", handleReady);
      return () => {
        api.previewer.unbind("ready", handleReady);
        if (previewBound) {
          api.previewer.preview.unbind(
            "focus-control-for-setting",
            handleFocus
          );
        }
      };
    }, [api, focusWidget]);
    const context = (0, import_element9.useMemo)(
      () => [focusedWidgetIdRef, focusWidget],
      [focusedWidgetIdRef, focusWidget]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(FocusControlContext.Provider, { value: context, children });
  }
  var useFocusControl = () => (0, import_element9.useContext)(FocusControlContext);

  // packages/customize-widgets/build-module/components/focus-control/use-blocks-focus-control.js
  function useBlocksFocusControl(blocks) {
    const { selectBlock } = (0, import_data8.useDispatch)(import_block_editor5.store);
    const [focusedWidgetIdRef] = useFocusControl();
    const blocksRef = (0, import_element10.useRef)(blocks);
    (0, import_element10.useEffect)(() => {
      blocksRef.current = blocks;
    }, [blocks]);
    (0, import_element10.useEffect)(() => {
      if (focusedWidgetIdRef.current) {
        const focusedBlock = blocksRef.current.find(
          (block) => (0, import_widgets3.getWidgetIdFromBlock)(block) === focusedWidgetIdRef.current
        );
        if (focusedBlock) {
          selectBlock(focusedBlock.clientId);
          const blockNode = document.querySelector(
            `[data-block="${focusedBlock.clientId}"]`
          );
          blockNode?.focus();
        }
      }
    }, [focusedWidgetIdRef, selectBlock]);
  }

  // packages/customize-widgets/build-module/lock-unlock.js
  var import_private_apis = __toESM(require_private_apis());
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/customize-widgets"
  );

  // packages/customize-widgets/build-module/components/sidebar-block-editor/sidebar-editor-provider.js
  var import_jsx_runtime16 = __toESM(require_jsx_runtime());
  var { ExperimentalBlockEditorProvider } = unlock(import_block_editor6.privateApis);
  function SidebarEditorProvider({
    sidebar,
    settings,
    children
  }) {
    const [blocks, onInput, onChange] = useSidebarBlockEditor(sidebar);
    useBlocksFocusControl(blocks);
    return /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
      ExperimentalBlockEditorProvider,
      {
        value: blocks,
        onInput,
        onChange,
        settings,
        useSubRegistry: false,
        children
      }
    );
  }

  // packages/customize-widgets/build-module/components/welcome-guide/index.js
  var import_i18n8 = __toESM(require_i18n());
  var import_components7 = __toESM(require_components());
  var import_data9 = __toESM(require_data());
  var import_preferences2 = __toESM(require_preferences());
  var import_jsx_runtime17 = __toESM(require_jsx_runtime());
  function WelcomeGuide({ sidebar }) {
    const { toggle } = (0, import_data9.useDispatch)(import_preferences2.store);
    const isEntirelyBlockWidgets = sidebar.getWidgets().every((widget) => widget.id.startsWith("block-"));
    return /* @__PURE__ */ (0, import_jsx_runtime17.jsxs)("div", { className: "customize-widgets-welcome-guide", children: [
      /* @__PURE__ */ (0, import_jsx_runtime17.jsx)("div", { className: "customize-widgets-welcome-guide__image__wrapper", children: /* @__PURE__ */ (0, import_jsx_runtime17.jsxs)("picture", { children: [
        /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
          "source",
          {
            srcSet: "https://s.w.org/images/block-editor/welcome-editor.svg",
            media: "(prefers-reduced-motion: reduce)"
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
          "img",
          {
            className: "customize-widgets-welcome-guide__image",
            src: "https://s.w.org/images/block-editor/welcome-editor.gif",
            width: "312",
            height: "240",
            alt: ""
          }
        )
      ] }) }),
      /* @__PURE__ */ (0, import_jsx_runtime17.jsx)("h1", { className: "customize-widgets-welcome-guide__heading", children: (0, import_i18n8.__)("Welcome to block Widgets") }),
      /* @__PURE__ */ (0, import_jsx_runtime17.jsx)("p", { className: "customize-widgets-welcome-guide__text", children: isEntirelyBlockWidgets ? (0, import_i18n8.__)(
        "Your theme provides different \u201Cblock\u201D areas for you to add and edit content.\xA0Try adding a search bar, social icons, or other types of blocks here and see how they\u2019ll look on your site."
      ) : (0, import_i18n8.__)(
        "You can now add any block to your site\u2019s widget areas. Don\u2019t worry, all of your favorite widgets still work flawlessly."
      ) }),
      /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
        import_components7.Button,
        {
          size: "compact",
          variant: "primary",
          onClick: () => toggle("core/customize-widgets", "welcomeGuide"),
          children: (0, import_i18n8.__)("Got it")
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime17.jsx)("hr", { className: "customize-widgets-welcome-guide__separator" }),
      !isEntirelyBlockWidgets && /* @__PURE__ */ (0, import_jsx_runtime17.jsxs)("p", { className: "customize-widgets-welcome-guide__more-info", children: [
        (0, import_i18n8.__)("Want to stick with the old widgets?"),
        /* @__PURE__ */ (0, import_jsx_runtime17.jsx)("br", {}),
        /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
          import_components7.ExternalLink,
          {
            href: (0, import_i18n8.__)(
              "https://wordpress.org/plugins/classic-widgets/"
            ),
            children: (0, import_i18n8.__)("Get the Classic Widgets plugin.")
          }
        )
      ] }),
      /* @__PURE__ */ (0, import_jsx_runtime17.jsxs)("p", { className: "customize-widgets-welcome-guide__more-info", children: [
        (0, import_i18n8.__)("New to the block editor?"),
        /* @__PURE__ */ (0, import_jsx_runtime17.jsx)("br", {}),
        /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
          import_components7.ExternalLink,
          {
            href: (0, import_i18n8.__)(
              "https://wordpress.org/documentation/article/wordpress-block-editor/"
            ),
            children: (0, import_i18n8.__)("Here's a detailed guide.")
          }
        )
      ] })
    ] });
  }

  // packages/customize-widgets/build-module/components/keyboard-shortcuts/index.js
  var import_element11 = __toESM(require_element());
  var import_keyboard_shortcuts4 = __toESM(require_keyboard_shortcuts());
  var import_keycodes4 = __toESM(require_keycodes());
  var import_data10 = __toESM(require_data());
  var import_i18n9 = __toESM(require_i18n());
  function KeyboardShortcuts({ undo, redo, save }) {
    (0, import_keyboard_shortcuts4.useShortcut)("core/customize-widgets/undo", (event) => {
      undo();
      event.preventDefault();
    });
    (0, import_keyboard_shortcuts4.useShortcut)("core/customize-widgets/redo", (event) => {
      redo();
      event.preventDefault();
    });
    (0, import_keyboard_shortcuts4.useShortcut)("core/customize-widgets/save", (event) => {
      event.preventDefault();
      save();
    });
    return null;
  }
  function KeyboardShortcutsRegister() {
    const { registerShortcut, unregisterShortcut } = (0, import_data10.useDispatch)(
      import_keyboard_shortcuts4.store
    );
    (0, import_element11.useEffect)(() => {
      registerShortcut({
        name: "core/customize-widgets/undo",
        category: "global",
        description: (0, import_i18n9.__)("Undo your last changes."),
        keyCombination: {
          modifier: "primary",
          character: "z"
        }
      });
      registerShortcut({
        name: "core/customize-widgets/redo",
        category: "global",
        description: (0, import_i18n9.__)("Redo your last undo."),
        keyCombination: {
          modifier: "primaryShift",
          character: "z"
        },
        // Disable on Apple OS because it conflicts with the browser's
        // history shortcut. It's a fine alias for both Windows and Linux.
        // Since there's no conflict for Ctrl+Shift+Z on both Windows and
        // Linux, we keep it as the default for consistency.
        aliases: (0, import_keycodes4.isAppleOS)() ? [] : [
          {
            modifier: "primary",
            character: "y"
          }
        ]
      });
      registerShortcut({
        name: "core/customize-widgets/save",
        category: "global",
        description: (0, import_i18n9.__)("Save your changes."),
        keyCombination: {
          modifier: "primary",
          character: "s"
        }
      });
      return () => {
        unregisterShortcut("core/customize-widgets/undo");
        unregisterShortcut("core/customize-widgets/redo");
        unregisterShortcut("core/customize-widgets/save");
      };
    }, [registerShortcut]);
    return null;
  }
  KeyboardShortcuts.Register = KeyboardShortcutsRegister;
  var keyboard_shortcuts_default = KeyboardShortcuts;

  // packages/customize-widgets/build-module/components/block-appender/index.js
  var import_element12 = __toESM(require_element());
  var import_block_editor7 = __toESM(require_block_editor());
  var import_data11 = __toESM(require_data());
  var import_jsx_runtime18 = __toESM(require_jsx_runtime());
  function BlockAppender(props) {
    const ref = (0, import_element12.useRef)();
    const isBlocksListEmpty = (0, import_data11.useSelect)(
      (select) => select(import_block_editor7.store).getBlockCount() === 0
    );
    (0, import_element12.useEffect)(() => {
      if (isBlocksListEmpty && ref.current) {
        const { ownerDocument } = ref.current;
        if (!ownerDocument.activeElement || ownerDocument.activeElement === ownerDocument.body) {
          ref.current.focus();
        }
      }
    }, [isBlocksListEmpty]);
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(import_block_editor7.ButtonBlockAppender, { ...props, ref });
  }

  // packages/customize-widgets/build-module/components/sidebar-block-editor/index.js
  var import_jsx_runtime19 = __toESM(require_jsx_runtime());
  var { ExperimentalBlockCanvas: BlockCanvas } = unlock(
    import_block_editor8.privateApis
  );
  var { BlockKeyboardShortcuts } = unlock(import_block_library.privateApis);
  function SidebarBlockEditor({
    blockEditorSettings,
    sidebar,
    inserter,
    inspector
  }) {
    const [isInserterOpened2, setIsInserterOpened2] = useInserter(inserter);
    const isMediumViewport = (0, import_compose3.useViewportMatch)("small");
    const {
      hasUploadPermissions,
      isFixedToolbarActive,
      keepCaretInsideBlock,
      isWelcomeGuideActive
    } = (0, import_data12.useSelect)((select) => {
      const { get } = select(import_preferences3.store);
      return {
        hasUploadPermissions: select(import_core_data.store).canUser("create", {
          kind: "postType",
          name: "attachment"
        }) ?? true,
        isFixedToolbarActive: !!get(
          "core/customize-widgets",
          "fixedToolbar"
        ),
        keepCaretInsideBlock: !!get(
          "core/customize-widgets",
          "keepCaretInsideBlock"
        ),
        isWelcomeGuideActive: !!get(
          "core/customize-widgets",
          "welcomeGuide"
        )
      };
    }, []);
    const settings = (0, import_element13.useMemo)(() => {
      let mediaUploadBlockEditor;
      if (hasUploadPermissions) {
        mediaUploadBlockEditor = ({ onError, ...argumentsObject }) => {
          (0, import_media_utils.uploadMedia)({
            wpAllowedMimeTypes: blockEditorSettings.allowedMimeTypes,
            onError: ({ message }) => onError(message),
            ...argumentsObject
          });
        };
      }
      return {
        ...blockEditorSettings,
        __experimentalSetIsInserterOpened: setIsInserterOpened2,
        mediaUpload: mediaUploadBlockEditor,
        hasFixedToolbar: isFixedToolbarActive || !isMediumViewport,
        keepCaretInsideBlock,
        editorTool: "edit",
        __unstableHasCustomAppender: true
      };
    }, [
      hasUploadPermissions,
      blockEditorSettings,
      isFixedToolbarActive,
      isMediumViewport,
      keepCaretInsideBlock,
      setIsInserterOpened2
    ]);
    if (isWelcomeGuideActive) {
      return /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(WelcomeGuide, { sidebar });
    }
    return /* @__PURE__ */ (0, import_jsx_runtime19.jsxs)(import_jsx_runtime19.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(keyboard_shortcuts_default.Register, {}),
      /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(BlockKeyboardShortcuts, {}),
      /* @__PURE__ */ (0, import_jsx_runtime19.jsxs)(SidebarEditorProvider, { sidebar, settings, children: [
        /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
          keyboard_shortcuts_default,
          {
            undo: sidebar.undo,
            redo: sidebar.redo,
            save: sidebar.save
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
          header_default,
          {
            sidebar,
            inserter,
            isInserterOpened: isInserterOpened2,
            setIsInserterOpened: setIsInserterOpened2,
            isFixedToolbarActive: isFixedToolbarActive || !isMediumViewport
          }
        ),
        (isFixedToolbarActive || !isMediumViewport) && /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(import_block_editor8.BlockToolbar, { hideDragHandle: true }),
        /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
          BlockCanvas,
          {
            shouldIframe: false,
            styles: settings.defaultEditorStyles,
            height: "100%",
            children: /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(import_block_editor8.BlockList, { renderAppender: BlockAppender })
          }
        ),
        (0, import_element13.createPortal)(
          // This is a temporary hack to prevent button component inside <BlockInspector>
          // from submitting form when type="button" is not specified.
          /* @__PURE__ */ (0, import_jsx_runtime19.jsx)("form", { onSubmit: (event) => event.preventDefault(), children: /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(import_block_editor8.BlockInspector, {}) }),
          inspector.contentContainer[0]
        )
      ] }),
      /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(import_block_editor8.__unstableBlockSettingsMenuFirstItem, { children: ({ onClose }) => /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
        block_inspector_button_default,
        {
          inspector,
          closeMenu: onClose
        }
      ) })
    ] });
  }

  // packages/customize-widgets/build-module/components/sidebar-controls/index.js
  var import_element14 = __toESM(require_element());
  var import_jsx_runtime20 = __toESM(require_jsx_runtime());
  var SidebarControlsContext = (0, import_element14.createContext)();
  SidebarControlsContext.displayName = "SidebarControlsContext";
  function SidebarControls({
    sidebarControls,
    activeSidebarControl,
    children
  }) {
    const context = (0, import_element14.useMemo)(
      () => ({
        sidebarControls,
        activeSidebarControl
      }),
      [sidebarControls, activeSidebarControl]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(SidebarControlsContext.Provider, { value: context, children });
  }
  function useSidebarControls() {
    const { sidebarControls } = (0, import_element14.useContext)(SidebarControlsContext);
    return sidebarControls;
  }
  function useActiveSidebarControl() {
    const { activeSidebarControl } = (0, import_element14.useContext)(SidebarControlsContext);
    return activeSidebarControl;
  }

  // packages/customize-widgets/build-module/components/customize-widgets/use-clear-selected-block.js
  var import_element15 = __toESM(require_element());
  var import_data13 = __toESM(require_data());
  var import_block_editor9 = __toESM(require_block_editor());
  function useClearSelectedBlock(sidebarControl, popoverRef) {
    const { hasSelectedBlock, hasMultiSelection } = (0, import_data13.useSelect)(import_block_editor9.store);
    const { clearSelectedBlock } = (0, import_data13.useDispatch)(import_block_editor9.store);
    (0, import_element15.useEffect)(() => {
      if (popoverRef.current && sidebarControl) {
        let handleClearSelectedBlock = function(element) {
          if (
            // 1. Make sure there are blocks being selected.
            (hasSelectedBlock() || hasMultiSelection()) && // 2. The element should exist in the DOM (not deleted).
            element && ownerDocument.contains(element) && // 3. It should also not exist in the container, the popover, nor the dialog.
            !container.contains(element) && !popoverRef.current.contains(element) && !element.closest('[role="dialog"]') && // 4. The inspector should not be opened.
            !inspector.expanded()
          ) {
            clearSelectedBlock();
          }
        }, handleMouseDown = function(event) {
          handleClearSelectedBlock(event.target);
        }, handleBlur = function() {
          handleClearSelectedBlock(ownerDocument.activeElement);
        };
        const inspector = sidebarControl.inspector;
        const container = sidebarControl.container[0];
        const ownerDocument = container.ownerDocument;
        const ownerWindow = ownerDocument.defaultView;
        ownerDocument.addEventListener("mousedown", handleMouseDown);
        ownerWindow.addEventListener("blur", handleBlur);
        return () => {
          ownerDocument.removeEventListener(
            "mousedown",
            handleMouseDown
          );
          ownerWindow.removeEventListener("blur", handleBlur);
        };
      }
    }, [
      popoverRef,
      sidebarControl,
      hasSelectedBlock,
      hasMultiSelection,
      clearSelectedBlock
    ]);
  }

  // packages/customize-widgets/build-module/components/customize-widgets/index.js
  var import_jsx_runtime21 = __toESM(require_jsx_runtime());
  function CustomizeWidgets({
    api,
    sidebarControls,
    blockEditorSettings
  }) {
    const [activeSidebarControl, setActiveSidebarControl] = (0, import_element16.useState)(null);
    const parentContainer = document.getElementById(
      "customize-theme-controls"
    );
    const popoverRef = (0, import_element16.useRef)();
    useClearSelectedBlock(activeSidebarControl, popoverRef);
    (0, import_element16.useEffect)(() => {
      const unsubscribers = sidebarControls.map(
        (sidebarControl) => sidebarControl.subscribe((expanded) => {
          if (expanded) {
            setActiveSidebarControl(sidebarControl);
          }
        })
      );
      return () => {
        unsubscribers.forEach((unsubscriber) => unsubscriber());
      };
    }, [sidebarControls]);
    const activeSidebar = activeSidebarControl && (0, import_element16.createPortal)(
      /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(ErrorBoundary, { children: /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
        SidebarBlockEditor,
        {
          blockEditorSettings,
          sidebar: activeSidebarControl.sidebarAdapter,
          inserter: activeSidebarControl.inserter,
          inspector: activeSidebarControl.inspector
        },
        activeSidebarControl.id
      ) }),
      activeSidebarControl.container[0]
    );
    const popover = parentContainer && (0, import_element16.createPortal)(
      /* @__PURE__ */ (0, import_jsx_runtime21.jsx)("div", { className: "customize-widgets-popover", ref: popoverRef, children: /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(import_components8.Popover.Slot, {}) }),
      parentContainer
    );
    return /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(import_components8.SlotFillProvider, { children: /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
      SidebarControls,
      {
        sidebarControls,
        activeSidebarControl,
        children: /* @__PURE__ */ (0, import_jsx_runtime21.jsxs)(FocusControl, { api, sidebarControls, children: [
          activeSidebar,
          popover
        ] })
      }
    ) });
  }

  // packages/customize-widgets/build-module/controls/sidebar-section.js
  var import_i18n10 = __toESM(require_i18n());

  // packages/customize-widgets/build-module/controls/inspector-section.js
  function getInspectorSection() {
    const {
      wp: { customize }
    } = window;
    return class InspectorSection extends customize.Section {
      constructor(id, options) {
        super(id, options);
        this.parentSection = options.parentSection;
        this.returnFocusWhenClose = null;
        this._isOpen = false;
      }
      get isOpen() {
        return this._isOpen;
      }
      set isOpen(value) {
        this._isOpen = value;
        this.triggerActiveCallbacks();
      }
      ready() {
        this.contentContainer[0].classList.add(
          "customize-widgets-layout__inspector"
        );
      }
      isContextuallyActive() {
        return this.isOpen;
      }
      onChangeExpanded(expanded, args) {
        super.onChangeExpanded(expanded, args);
        if (this.parentSection && !args.unchanged) {
          if (expanded) {
            this.parentSection.collapse({
              manualTransition: true
            });
          } else {
            this.parentSection.expand({
              manualTransition: true,
              completeCallback: () => {
                if (this.returnFocusWhenClose && !this.contentContainer[0].contains(
                  this.returnFocusWhenClose
                )) {
                  this.returnFocusWhenClose.focus();
                }
              }
            });
          }
        }
      }
      open({ returnFocusWhenClose } = {}) {
        this.isOpen = true;
        this.returnFocusWhenClose = returnFocusWhenClose;
        this.expand({
          allowMultiple: true
        });
      }
      close() {
        this.collapse({
          allowMultiple: true
        });
      }
      collapse(options) {
        this.isOpen = false;
        super.collapse(options);
      }
      triggerActiveCallbacks() {
        this.active.callbacks.fireWith(this.active, [false, true]);
      }
    };
  }

  // packages/customize-widgets/build-module/controls/sidebar-section.js
  var getInspectorSectionId = (sidebarId) => `widgets-inspector-${sidebarId}`;
  function getSidebarSection() {
    const {
      wp: { customize }
    } = window;
    const reduceMotionMediaQuery = window.matchMedia(
      "(prefers-reduced-motion: reduce)"
    );
    let isReducedMotion = reduceMotionMediaQuery.matches;
    reduceMotionMediaQuery.addEventListener("change", (event) => {
      isReducedMotion = event.matches;
    });
    return class SidebarSection extends customize.Section {
      ready() {
        const InspectorSection = getInspectorSection();
        this.inspector = new InspectorSection(
          getInspectorSectionId(this.id),
          {
            title: (0, import_i18n10.__)("Block Settings"),
            parentSection: this,
            customizeAction: [
              (0, import_i18n10.__)("Customizing"),
              (0, import_i18n10.__)("Widgets"),
              this.params.title
            ].join(" \u25B8 ")
          }
        );
        customize.section.add(this.inspector);
        this.contentContainer[0].classList.add(
          "customize-widgets__sidebar-section"
        );
      }
      hasSubSectionOpened() {
        return this.inspector.expanded();
      }
      onChangeExpanded(expanded, _args) {
        const controls = this.controls();
        const args = {
          ..._args,
          completeCallback() {
            controls.forEach((control) => {
              control.onChangeSectionExpanded?.(expanded, args);
            });
            _args.completeCallback?.();
          }
        };
        if (args.manualTransition) {
          if (expanded) {
            this.contentContainer.addClass(["busy", "open"]);
            this.contentContainer.removeClass("is-sub-section-open");
            this.contentContainer.closest(".wp-full-overlay").addClass("section-open");
          } else {
            this.contentContainer.addClass([
              "busy",
              "is-sub-section-open"
            ]);
            this.contentContainer.closest(".wp-full-overlay").addClass("section-open");
            this.contentContainer.removeClass("open");
          }
          const handleTransitionEnd = () => {
            this.contentContainer.removeClass("busy");
            args.completeCallback();
          };
          if (isReducedMotion) {
            handleTransitionEnd();
          } else {
            this.contentContainer.one(
              "transitionend",
              handleTransitionEnd
            );
          }
        } else {
          super.onChangeExpanded(expanded, args);
        }
      }
    };
  }

  // packages/customize-widgets/build-module/controls/sidebar-control.js
  var import_data15 = __toESM(require_data());

  // packages/customize-widgets/build-module/components/sidebar-block-editor/sidebar-adapter.js
  var { wp } = window;
  function parseWidgetId(widgetId) {
    const matches = widgetId.match(/^(.+)-(\d+)$/);
    if (matches) {
      return {
        idBase: matches[1],
        number: parseInt(matches[2], 10)
      };
    }
    return { idBase: widgetId };
  }
  function widgetIdToSettingId(widgetId) {
    const { idBase, number } = parseWidgetId(widgetId);
    if (number) {
      return `widget_${idBase}[${number}]`;
    }
    return `widget_${idBase}`;
  }
  function debounce(leading, callback, timeout) {
    let isLeading = false;
    let timerID;
    function debounced(...args) {
      const result = (isLeading ? callback : leading).apply(this, args);
      isLeading = true;
      clearTimeout(timerID);
      timerID = setTimeout(() => {
        isLeading = false;
      }, timeout);
      return result;
    }
    debounced.cancel = () => {
      isLeading = false;
      clearTimeout(timerID);
    };
    return debounced;
  }
  var SidebarAdapter = class {
    constructor(setting, api) {
      this.setting = setting;
      this.api = api;
      this.locked = false;
      this.widgetsCache = /* @__PURE__ */ new WeakMap();
      this.subscribers = /* @__PURE__ */ new Set();
      this.history = [
        this._getWidgetIds().map(
          (widgetId) => this.getWidget(widgetId)
        )
      ];
      this.historyIndex = 0;
      this.historySubscribers = /* @__PURE__ */ new Set();
      this._debounceSetHistory = debounce(
        this._pushHistory,
        this._replaceHistory,
        1e3
      );
      this.setting.bind(this._handleSettingChange.bind(this));
      this.api.bind("change", this._handleAllSettingsChange.bind(this));
      this.undo = this.undo.bind(this);
      this.redo = this.redo.bind(this);
      this.save = this.save.bind(this);
    }
    subscribe(callback) {
      this.subscribers.add(callback);
      return () => {
        this.subscribers.delete(callback);
      };
    }
    getWidgets() {
      return this.history[this.historyIndex];
    }
    _emit(...args) {
      for (const callback of this.subscribers) {
        callback(...args);
      }
    }
    _getWidgetIds() {
      return this.setting.get();
    }
    _pushHistory() {
      this.history = [
        ...this.history.slice(0, this.historyIndex + 1),
        this._getWidgetIds().map(
          (widgetId) => this.getWidget(widgetId)
        )
      ];
      this.historyIndex += 1;
      this.historySubscribers.forEach((listener) => listener());
    }
    _replaceHistory() {
      this.history[this.historyIndex] = this._getWidgetIds().map(
        (widgetId) => this.getWidget(widgetId)
      );
    }
    _handleSettingChange() {
      if (this.locked) {
        return;
      }
      const prevWidgets = this.getWidgets();
      this._pushHistory();
      this._emit(prevWidgets, this.getWidgets());
    }
    _handleAllSettingsChange(setting) {
      if (this.locked) {
        return;
      }
      if (!setting.id.startsWith("widget_")) {
        return;
      }
      const widgetId = settingIdToWidgetId(setting.id);
      if (!this.setting.get().includes(widgetId)) {
        return;
      }
      const prevWidgets = this.getWidgets();
      this._pushHistory();
      this._emit(prevWidgets, this.getWidgets());
    }
    _createWidget(widget) {
      const widgetModel = wp.customize.Widgets.availableWidgets.findWhere({
        id_base: widget.idBase
      });
      let number = widget.number;
      if (widgetModel.get("is_multi") && !number) {
        widgetModel.set(
          "multi_number",
          widgetModel.get("multi_number") + 1
        );
        number = widgetModel.get("multi_number");
      }
      const settingId = number ? `widget_${widget.idBase}[${number}]` : `widget_${widget.idBase}`;
      const settingArgs = {
        transport: wp.customize.Widgets.data.selectiveRefreshableWidgets[widgetModel.get("id_base")] ? "postMessage" : "refresh",
        previewer: this.setting.previewer
      };
      const setting = this.api.create(
        settingId,
        settingId,
        "",
        settingArgs
      );
      setting.set(widget.instance);
      const widgetId = settingIdToWidgetId(settingId);
      return widgetId;
    }
    _removeWidget(widget) {
      const settingId = widgetIdToSettingId(widget.id);
      const setting = this.api(settingId);
      if (setting) {
        const instance = setting.get();
        this.widgetsCache.delete(instance);
      }
      this.api.remove(settingId);
    }
    _updateWidget(widget) {
      const prevWidget = this.getWidget(widget.id);
      if (prevWidget === widget) {
        return widget.id;
      }
      if (prevWidget.idBase && widget.idBase && prevWidget.idBase === widget.idBase) {
        const settingId = widgetIdToSettingId(widget.id);
        this.api(settingId).set(widget.instance);
        return widget.id;
      }
      this._removeWidget(widget);
      return this._createWidget(widget);
    }
    getWidget(widgetId) {
      if (!widgetId) {
        return null;
      }
      const { idBase, number } = parseWidgetId(widgetId);
      const settingId = widgetIdToSettingId(widgetId);
      const setting = this.api(settingId);
      if (!setting) {
        return null;
      }
      const instance = setting.get();
      if (this.widgetsCache.has(instance)) {
        return this.widgetsCache.get(instance);
      }
      const widget = {
        id: widgetId,
        idBase,
        number,
        instance
      };
      this.widgetsCache.set(instance, widget);
      return widget;
    }
    _updateWidgets(nextWidgets) {
      this.locked = true;
      const addedWidgetIds = [];
      const nextWidgetIds = nextWidgets.map((nextWidget) => {
        if (nextWidget.id && this.getWidget(nextWidget.id)) {
          addedWidgetIds.push(null);
          return this._updateWidget(nextWidget);
        }
        const widgetId = this._createWidget(nextWidget);
        addedWidgetIds.push(widgetId);
        return widgetId;
      });
      const deletedWidgets = this.getWidgets().filter(
        (widget) => !nextWidgetIds.includes(widget.id)
      );
      deletedWidgets.forEach((widget) => this._removeWidget(widget));
      this.setting.set(nextWidgetIds);
      this.locked = false;
      return addedWidgetIds;
    }
    setWidgets(nextWidgets) {
      const addedWidgetIds = this._updateWidgets(nextWidgets);
      this._debounceSetHistory();
      return addedWidgetIds;
    }
    /**
     * Undo/Redo related features
     */
    hasUndo() {
      return this.historyIndex > 0;
    }
    hasRedo() {
      return this.historyIndex < this.history.length - 1;
    }
    _seek(historyIndex) {
      const currentWidgets = this.getWidgets();
      this.historyIndex = historyIndex;
      const widgets = this.history[this.historyIndex];
      this._updateWidgets(widgets);
      this._emit(currentWidgets, this.getWidgets());
      this.historySubscribers.forEach((listener) => listener());
      this._debounceSetHistory.cancel();
    }
    undo() {
      if (!this.hasUndo()) {
        return;
      }
      this._seek(this.historyIndex - 1);
    }
    redo() {
      if (!this.hasRedo()) {
        return;
      }
      this._seek(this.historyIndex + 1);
    }
    subscribeHistory(listener) {
      this.historySubscribers.add(listener);
      return () => {
        this.historySubscribers.delete(listener);
      };
    }
    save() {
      this.api.previewer.save();
    }
  };

  // packages/customize-widgets/build-module/controls/inserter-outer-section.js
  var import_keycodes5 = __toESM(require_keycodes());
  var import_dom = __toESM(require_dom());
  var import_data14 = __toESM(require_data());
  function getInserterOuterSection() {
    const {
      wp: { customize }
    } = window;
    const OuterSection = customize.OuterSection;
    customize.OuterSection = class extends OuterSection {
      onChangeExpanded(expanded, args) {
        if (expanded) {
          customize.section.each((section) => {
            if (section.params.type === "outer" && section.id !== this.id) {
              if (section.expanded()) {
                section.collapse();
              }
            }
          });
        }
        return super.onChangeExpanded(expanded, args);
      }
    };
    customize.sectionConstructor.outer = customize.OuterSection;
    return class InserterOuterSection extends customize.OuterSection {
      constructor(...args) {
        super(...args);
        this.params.type = "outer";
        this.activeElementBeforeExpanded = null;
        const ownerWindow = this.contentContainer[0].ownerDocument.defaultView;
        ownerWindow.addEventListener(
          "keydown",
          (event) => {
            if (this.expanded() && (event.keyCode === import_keycodes5.ESCAPE || event.code === "Escape") && !event.defaultPrevented) {
              event.preventDefault();
              event.stopPropagation();
              (0, import_data14.dispatch)(store).setIsInserterOpened(
                false
              );
            }
          },
          // Use capture mode to make this run before other event listeners.
          true
        );
        this.contentContainer.addClass("widgets-inserter");
        this.isFromInternalAction = false;
        this.expanded.bind(() => {
          if (!this.isFromInternalAction) {
            (0, import_data14.dispatch)(store).setIsInserterOpened(
              this.expanded()
            );
          }
          this.isFromInternalAction = false;
        });
      }
      open() {
        if (!this.expanded()) {
          const contentContainer = this.contentContainer[0];
          this.activeElementBeforeExpanded = contentContainer.ownerDocument.activeElement;
          this.isFromInternalAction = true;
          this.expand({
            completeCallback() {
              const searchBox = import_dom.focus.tabbable.find(contentContainer)[1];
              if (searchBox) {
                searchBox.focus();
              }
            }
          });
        }
      }
      close() {
        if (this.expanded()) {
          const contentContainer = this.contentContainer[0];
          const activeElement = contentContainer.ownerDocument.activeElement;
          this.isFromInternalAction = true;
          this.collapse({
            completeCallback() {
              if (contentContainer.contains(activeElement)) {
                if (this.activeElementBeforeExpanded) {
                  this.activeElementBeforeExpanded.focus();
                }
              }
            }
          });
        }
      }
    };
  }

  // packages/customize-widgets/build-module/controls/sidebar-control.js
  var getInserterId = (controlId) => `widgets-inserter-${controlId}`;
  function getSidebarControl() {
    const {
      wp: { customize }
    } = window;
    return class SidebarControl extends customize.Control {
      constructor(...args) {
        super(...args);
        this.subscribers = /* @__PURE__ */ new Set();
      }
      ready() {
        const InserterOuterSection = getInserterOuterSection();
        this.inserter = new InserterOuterSection(
          getInserterId(this.id),
          {}
        );
        customize.section.add(this.inserter);
        this.sectionInstance = customize.section(this.section());
        this.inspector = this.sectionInstance.inspector;
        this.sidebarAdapter = new SidebarAdapter(this.setting, customize);
      }
      subscribe(callback) {
        this.subscribers.add(callback);
        return () => {
          this.subscribers.delete(callback);
        };
      }
      onChangeSectionExpanded(expanded, args) {
        if (!args.unchanged) {
          if (!expanded) {
            (0, import_data15.dispatch)(store).setIsInserterOpened(
              false
            );
          }
          this.subscribers.forEach(
            (subscriber) => subscriber(expanded, args)
          );
        }
      }
    };
  }

  // packages/customize-widgets/build-module/filters/move-to-sidebar.js
  var import_block_editor10 = __toESM(require_block_editor());
  var import_compose4 = __toESM(require_compose());
  var import_data16 = __toESM(require_data());
  var import_hooks2 = __toESM(require_hooks());
  var import_widgets4 = __toESM(require_widgets());
  var import_jsx_runtime22 = __toESM(require_jsx_runtime());
  var withMoveToSidebarToolbarItem = (0, import_compose4.createHigherOrderComponent)(
    (BlockEdit) => (props) => {
      let widgetId = (0, import_widgets4.getWidgetIdFromBlock)(props);
      const sidebarControls = useSidebarControls();
      const activeSidebarControl = useActiveSidebarControl();
      const hasMultipleSidebars = sidebarControls?.length > 1;
      const blockName = props.name;
      const clientId = props.clientId;
      const canInsertBlockInSidebar = (0, import_data16.useSelect)(
        (select) => {
          return select(import_block_editor10.store).canInsertBlockType(
            blockName,
            ""
          );
        },
        [blockName]
      );
      const block = (0, import_data16.useSelect)(
        (select) => select(import_block_editor10.store).getBlock(clientId),
        [clientId]
      );
      const { removeBlock } = (0, import_data16.useDispatch)(import_block_editor10.store);
      const [, focusWidget] = useFocusControl();
      function moveToSidebar(sidebarControlId) {
        const newSidebarControl = sidebarControls.find(
          (sidebarControl) => sidebarControl.id === sidebarControlId
        );
        if (widgetId) {
          const oldSetting = activeSidebarControl.setting;
          const newSetting = newSidebarControl.setting;
          oldSetting(oldSetting().filter((id) => id !== widgetId));
          newSetting([...newSetting(), widgetId]);
        } else {
          const sidebarAdapter = newSidebarControl.sidebarAdapter;
          removeBlock(clientId);
          const addedWidgetIds = sidebarAdapter.setWidgets([
            ...sidebarAdapter.getWidgets(),
            blockToWidget(block)
          ]);
          widgetId = addedWidgetIds.reverse().find((id) => !!id);
        }
        focusWidget(widgetId);
      }
      return /* @__PURE__ */ (0, import_jsx_runtime22.jsxs)(import_jsx_runtime22.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(BlockEdit, { ...props }, "edit"),
        hasMultipleSidebars && canInsertBlockInSidebar && /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(import_block_editor10.BlockControls, { children: /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
          import_widgets4.MoveToWidgetArea,
          {
            widgetAreas: sidebarControls.map(
              (sidebarControl) => ({
                id: sidebarControl.id,
                name: sidebarControl.params.label,
                description: sidebarControl.params.description
              })
            ),
            currentWidgetAreaId: activeSidebarControl?.id,
            onSelect: moveToSidebar
          }
        ) })
      ] });
    },
    "withMoveToSidebarToolbarItem"
  );
  (0, import_hooks2.addFilter)(
    "editor.BlockEdit",
    "core/customize-widgets/block-edit",
    withMoveToSidebarToolbarItem
  );

  // packages/customize-widgets/build-module/filters/replace-media-upload.js
  var import_hooks3 = __toESM(require_hooks());
  var import_media_utils2 = __toESM(require_media_utils());
  var { MediaUploadModal: MediaUploadModalComponent } = unlock(
    import_media_utils2.privateApis
  );
  if (window.__experimentalDataViewsMediaModal) {
    (0, import_hooks3.addFilter)(
      "editor.MediaUploadModal",
      "core/customize-widgets/replace-media-upload-modal",
      () => {
        return MediaUploadModalComponent;
      }
    );
  }
  var replaceMediaUpload = () => import_media_utils2.MediaUpload;
  (0, import_hooks3.addFilter)(
    "editor.MediaUpload",
    "core/edit-widgets/replace-media-upload",
    replaceMediaUpload
  );

  // packages/customize-widgets/build-module/filters/wide-widget-display.js
  var import_compose5 = __toESM(require_compose());
  var import_hooks4 = __toESM(require_hooks());
  var import_jsx_runtime23 = __toESM(require_jsx_runtime());
  var { wp: wp2 } = window;
  var withWideWidgetDisplay = (0, import_compose5.createHigherOrderComponent)(
    (BlockEdit) => (props) => {
      const { idBase } = props.attributes;
      const isWide = wp2.customize.Widgets.data.availableWidgets.find(
        (widget) => widget.id_base === idBase
      )?.is_wide ?? false;
      return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(BlockEdit, { ...props, isWide }, "edit");
    },
    "withWideWidgetDisplay"
  );
  (0, import_hooks4.addFilter)(
    "editor.BlockEdit",
    "core/customize-widgets/wide-widget-display",
    withWideWidgetDisplay
  );

  // packages/customize-widgets/build-module/index.js
  var import_jsx_runtime24 = __toESM(require_jsx_runtime());
  var { wp: wp3 } = window;
  var DISABLED_BLOCKS = [
    "core/more",
    "core/block",
    "core/freeform",
    "core/template-part"
  ];
  var ENABLE_EXPERIMENTAL_FSE_BLOCKS = false;
  function initialize(editorName, blockEditorSettings) {
    (0, import_data17.dispatch)(import_preferences4.store).setDefaults("core/customize-widgets", {
      fixedToolbar: false,
      welcomeGuide: true
    });
    (0, import_data17.dispatch)(import_blocks2.store).reapplyBlockTypeFilters();
    const coreBlocks = (0, import_block_library2.__experimentalGetCoreBlocks)().filter((block) => {
      return !(DISABLED_BLOCKS.includes(block.name) || block.name.startsWith("core/post") || block.name.startsWith("core/query") || block.name.startsWith("core/site") || block.name.startsWith("core/navigation"));
    });
    (0, import_block_library2.registerCoreBlocks)(coreBlocks);
    (0, import_widgets5.registerLegacyWidgetBlock)();
    if (true) {
      (0, import_block_library2.__experimentalRegisterExperimentalCoreBlocks)({
        enableFSEBlocks: ENABLE_EXPERIMENTAL_FSE_BLOCKS
      });
    }
    (0, import_widgets5.registerLegacyWidgetVariations)(blockEditorSettings);
    (0, import_widgets5.registerWidgetGroupBlock)();
    (0, import_blocks2.setFreeformContentHandlerName)("core/html");
    const SidebarControl = getSidebarControl(blockEditorSettings);
    wp3.customize.sectionConstructor.sidebar = getSidebarSection();
    wp3.customize.controlConstructor.sidebar_block_editor = SidebarControl;
    const container = document.createElement("div");
    document.body.appendChild(container);
    wp3.customize.bind("ready", () => {
      const sidebarControls = [];
      wp3.customize.control.each((control) => {
        if (control instanceof SidebarControl) {
          sidebarControls.push(control);
        }
      });
      (0, import_element17.createRoot)(container).render(
        /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(import_element17.StrictMode, { children: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
          CustomizeWidgets,
          {
            api: wp3.customize,
            sidebarControls,
            blockEditorSettings
          }
        ) })
      );
    });
  }
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
