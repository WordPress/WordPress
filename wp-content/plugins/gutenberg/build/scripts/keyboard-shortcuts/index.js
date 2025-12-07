var wp;
(wp ||= {}).keyboardShortcuts = (() => {
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

  // package-external:@wordpress/keycodes
  var require_keycodes = __commonJS({
    "package-external:@wordpress/keycodes"(exports, module) {
      module.exports = window.wp.keycodes;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // packages/keyboard-shortcuts/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    ShortcutProvider: () => ShortcutProvider,
    __unstableUseShortcutEventMatch: () => useShortcutEventMatch,
    store: () => store,
    useShortcut: () => useShortcut
  });

  // packages/keyboard-shortcuts/build-module/store/index.js
  var import_data2 = __toESM(require_data());

  // packages/keyboard-shortcuts/build-module/store/reducer.js
  function reducer(state = {}, action) {
    switch (action.type) {
      case "REGISTER_SHORTCUT":
        return {
          ...state,
          [action.name]: {
            category: action.category,
            keyCombination: action.keyCombination,
            aliases: action.aliases,
            description: action.description
          }
        };
      case "UNREGISTER_SHORTCUT":
        const { [action.name]: actionName, ...remainingState } = state;
        return remainingState;
    }
    return state;
  }
  var reducer_default = reducer;

  // packages/keyboard-shortcuts/build-module/store/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    registerShortcut: () => registerShortcut,
    unregisterShortcut: () => unregisterShortcut
  });
  function registerShortcut({
    name,
    category,
    description,
    keyCombination,
    aliases
  }) {
    return {
      type: "REGISTER_SHORTCUT",
      name,
      category,
      keyCombination,
      aliases,
      description
    };
  }
  function unregisterShortcut(name) {
    return {
      type: "UNREGISTER_SHORTCUT",
      name
    };
  }

  // packages/keyboard-shortcuts/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    getAllShortcutKeyCombinations: () => getAllShortcutKeyCombinations,
    getAllShortcutRawKeyCombinations: () => getAllShortcutRawKeyCombinations,
    getCategoryShortcuts: () => getCategoryShortcuts,
    getShortcutAliases: () => getShortcutAliases,
    getShortcutDescription: () => getShortcutDescription,
    getShortcutKeyCombination: () => getShortcutKeyCombination,
    getShortcutRepresentation: () => getShortcutRepresentation
  });
  var import_data = __toESM(require_data());
  var import_keycodes = __toESM(require_keycodes());
  var EMPTY_ARRAY = [];
  var FORMATTING_METHODS = {
    display: import_keycodes.displayShortcut,
    raw: import_keycodes.rawShortcut,
    ariaLabel: import_keycodes.shortcutAriaLabel
  };
  function getKeyCombinationRepresentation(shortcut, representation) {
    if (!shortcut) {
      return null;
    }
    return shortcut.modifier ? FORMATTING_METHODS[representation][shortcut.modifier](
      shortcut.character
    ) : shortcut.character;
  }
  function getShortcutKeyCombination(state, name) {
    return state[name] ? state[name].keyCombination : null;
  }
  function getShortcutRepresentation(state, name, representation = "display") {
    const shortcut = getShortcutKeyCombination(state, name);
    return getKeyCombinationRepresentation(shortcut, representation);
  }
  function getShortcutDescription(state, name) {
    return state[name] ? state[name].description : null;
  }
  function getShortcutAliases(state, name) {
    return state[name] && state[name].aliases ? state[name].aliases : EMPTY_ARRAY;
  }
  var getAllShortcutKeyCombinations = (0, import_data.createSelector)(
    (state, name) => {
      return [
        getShortcutKeyCombination(state, name),
        ...getShortcutAliases(state, name)
      ].filter(Boolean);
    },
    (state, name) => [state[name]]
  );
  var getAllShortcutRawKeyCombinations = (0, import_data.createSelector)(
    (state, name) => {
      return getAllShortcutKeyCombinations(state, name).map(
        (combination) => getKeyCombinationRepresentation(combination, "raw")
      );
    },
    (state, name) => [state[name]]
  );
  var getCategoryShortcuts = (0, import_data.createSelector)(
    (state, categoryName) => {
      return Object.entries(state).filter(([, shortcut]) => shortcut.category === categoryName).map(([name]) => name);
    },
    (state) => [state]
  );

  // packages/keyboard-shortcuts/build-module/store/index.js
  var STORE_NAME = "core/keyboard-shortcuts";
  var store = (0, import_data2.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    actions: actions_exports,
    selectors: selectors_exports
  });
  (0, import_data2.register)(store);

  // packages/keyboard-shortcuts/build-module/hooks/use-shortcut.js
  var import_element2 = __toESM(require_element());

  // packages/keyboard-shortcuts/build-module/hooks/use-shortcut-event-match.js
  var import_data3 = __toESM(require_data());
  var import_keycodes2 = __toESM(require_keycodes());
  function useShortcutEventMatch() {
    const { getAllShortcutKeyCombinations: getAllShortcutKeyCombinations2 } = (0, import_data3.useSelect)(
      store
    );
    function isMatch(name, event) {
      return getAllShortcutKeyCombinations2(name).some(
        ({ modifier, character }) => {
          return import_keycodes2.isKeyboardEvent[modifier](event, character);
        }
      );
    }
    return isMatch;
  }

  // packages/keyboard-shortcuts/build-module/context.js
  var import_element = __toESM(require_element());
  var globalShortcuts = /* @__PURE__ */ new Set();
  var globalListener = (event) => {
    for (const keyboardShortcut of globalShortcuts) {
      keyboardShortcut(event);
    }
  };
  var context = (0, import_element.createContext)({
    add: (shortcut) => {
      if (globalShortcuts.size === 0) {
        document.addEventListener("keydown", globalListener);
      }
      globalShortcuts.add(shortcut);
    },
    delete: (shortcut) => {
      globalShortcuts.delete(shortcut);
      if (globalShortcuts.size === 0) {
        document.removeEventListener("keydown", globalListener);
      }
    }
  });
  context.displayName = "KeyboardShortcutsContext";

  // packages/keyboard-shortcuts/build-module/hooks/use-shortcut.js
  function useShortcut(name, callback, { isDisabled = false } = {}) {
    const shortcuts = (0, import_element2.useContext)(context);
    const isMatch = useShortcutEventMatch();
    const callbackRef = (0, import_element2.useRef)();
    (0, import_element2.useEffect)(() => {
      callbackRef.current = callback;
    }, [callback]);
    (0, import_element2.useEffect)(() => {
      if (isDisabled) {
        return;
      }
      function _callback(event) {
        if (isMatch(name, event)) {
          callbackRef.current(event);
        }
      }
      shortcuts.add(_callback);
      return () => {
        shortcuts.delete(_callback);
      };
    }, [name, isDisabled, shortcuts]);
  }

  // packages/keyboard-shortcuts/build-module/components/shortcut-provider.js
  var import_element3 = __toESM(require_element());
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var { Provider } = context;
  function ShortcutProvider(props) {
    const [keyboardShortcuts] = (0, import_element3.useState)(() => /* @__PURE__ */ new Set());
    function onKeyDown(event) {
      if (props.onKeyDown) {
        props.onKeyDown(event);
      }
      for (const keyboardShortcut of keyboardShortcuts) {
        keyboardShortcut(event);
      }
    }
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(Provider, { value: keyboardShortcuts, children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)("div", { ...props, onKeyDown }) });
  }
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
