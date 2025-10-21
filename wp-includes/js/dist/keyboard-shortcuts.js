/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  ShortcutProvider: () => (/* reexport */ ShortcutProvider),
  __unstableUseShortcutEventMatch: () => (/* reexport */ useShortcutEventMatch),
  store: () => (/* reexport */ store),
  useShortcut: () => (/* reexport */ useShortcut)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  registerShortcut: () => (registerShortcut),
  unregisterShortcut: () => (unregisterShortcut)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  getAllShortcutKeyCombinations: () => (getAllShortcutKeyCombinations),
  getAllShortcutRawKeyCombinations: () => (getAllShortcutRawKeyCombinations),
  getCategoryShortcuts: () => (getCategoryShortcuts),
  getShortcutAliases: () => (getShortcutAliases),
  getShortcutDescription: () => (getShortcutDescription),
  getShortcutKeyCombination: () => (getShortcutKeyCombination),
  getShortcutRepresentation: () => (getShortcutRepresentation)
});

;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/reducer.js
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


;// ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/actions.js
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


;// external ["wp","keycodes"]
const external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/selectors.js


const EMPTY_ARRAY = [];
const FORMATTING_METHODS = {
  display: external_wp_keycodes_namespaceObject.displayShortcut,
  raw: external_wp_keycodes_namespaceObject.rawShortcut,
  ariaLabel: external_wp_keycodes_namespaceObject.shortcutAriaLabel
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
const getAllShortcutKeyCombinations = (0,external_wp_data_namespaceObject.createSelector)(
  (state, name) => {
    return [
      getShortcutKeyCombination(state, name),
      ...getShortcutAliases(state, name)
    ].filter(Boolean);
  },
  (state, name) => [state[name]]
);
const getAllShortcutRawKeyCombinations = (0,external_wp_data_namespaceObject.createSelector)(
  (state, name) => {
    return getAllShortcutKeyCombinations(state, name).map(
      (combination) => getKeyCombinationRepresentation(combination, "raw")
    );
  },
  (state, name) => [state[name]]
);
const getCategoryShortcuts = (0,external_wp_data_namespaceObject.createSelector)(
  (state, categoryName) => {
    return Object.entries(state).filter(([, shortcut]) => shortcut.category === categoryName).map(([name]) => name);
  },
  (state) => [state]
);


;// ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/index.js




const STORE_NAME = "core/keyboard-shortcuts";
const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer_default,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);


;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// ./node_modules/@wordpress/keyboard-shortcuts/build-module/hooks/use-shortcut-event-match.js



function useShortcutEventMatch() {
  const { getAllShortcutKeyCombinations } = (0,external_wp_data_namespaceObject.useSelect)(
    store
  );
  function isMatch(name, event) {
    return getAllShortcutKeyCombinations(name).some(
      ({ modifier, character }) => {
        return external_wp_keycodes_namespaceObject.isKeyboardEvent[modifier](event, character);
      }
    );
  }
  return isMatch;
}


;// ./node_modules/@wordpress/keyboard-shortcuts/build-module/context.js

const globalShortcuts = /* @__PURE__ */ new Set();
const globalListener = (event) => {
  for (const keyboardShortcut of globalShortcuts) {
    keyboardShortcut(event);
  }
};
const context = (0,external_wp_element_namespaceObject.createContext)({
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


;// ./node_modules/@wordpress/keyboard-shortcuts/build-module/hooks/use-shortcut.js



function useShortcut(name, callback, { isDisabled = false } = {}) {
  const shortcuts = (0,external_wp_element_namespaceObject.useContext)(context);
  const isMatch = useShortcutEventMatch();
  const callbackRef = (0,external_wp_element_namespaceObject.useRef)();
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    callbackRef.current = callback;
  }, [callback]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
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


;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// ./node_modules/@wordpress/keyboard-shortcuts/build-module/components/shortcut-provider.js



const { Provider } = context;
function ShortcutProvider(props) {
  const [keyboardShortcuts] = (0,external_wp_element_namespaceObject.useState)(() => /* @__PURE__ */ new Set());
  function onKeyDown(event) {
    if (props.onKeyDown) {
      props.onKeyDown(event);
    }
    for (const keyboardShortcut of keyboardShortcuts) {
      keyboardShortcut(event);
    }
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(Provider, { value: keyboardShortcuts, children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("div", { ...props, onKeyDown }) });
}


;// ./node_modules/@wordpress/keyboard-shortcuts/build-module/index.js






(window.wp = window.wp || {}).keyboardShortcuts = __webpack_exports__;
/******/ })()
;