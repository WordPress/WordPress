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

;// CONCATENATED MODULE: external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/reducer.js
/**
 * Reducer returning the registered shortcuts
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */
function reducer(state = {}, action) {
  switch (action.type) {
    case 'REGISTER_SHORTCUT':
      return {
        ...state,
        [action.name]: {
          category: action.category,
          keyCombination: action.keyCombination,
          aliases: action.aliases,
          description: action.description
        }
      };
    case 'UNREGISTER_SHORTCUT':
      const {
        [action.name]: actionName,
        ...remainingState
      } = state;
      return remainingState;
  }
  return state;
}
/* harmony default export */ const store_reducer = (reducer);

;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/actions.js
/** @typedef {import('@wordpress/keycodes').WPKeycodeModifier} WPKeycodeModifier */

/**
 * Keyboard key combination.
 *
 * @typedef {Object} WPShortcutKeyCombination
 *
 * @property {string}                      character Character.
 * @property {WPKeycodeModifier|undefined} modifier  Modifier.
 */

/**
 * Configuration of a registered keyboard shortcut.
 *
 * @typedef {Object} WPShortcutConfig
 *
 * @property {string}                     name           Shortcut name.
 * @property {string}                     category       Shortcut category.
 * @property {string}                     description    Shortcut description.
 * @property {WPShortcutKeyCombination}   keyCombination Shortcut key combination.
 * @property {WPShortcutKeyCombination[]} [aliases]      Shortcut aliases.
 */

/**
 * Returns an action object used to register a new keyboard shortcut.
 *
 * @param {WPShortcutConfig} config Shortcut config.
 *
 * @example
 *
 *```js
 * import { useEffect } from 'react';
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect, useDispatch } from '@wordpress/data';
 * import { __ } from '@wordpress/i18n';
 *
 * const ExampleComponent = () => {
 *     const { registerShortcut } = useDispatch( keyboardShortcutsStore );
 *
 *     useEffect( () => {
 *         registerShortcut( {
 *             name: 'custom/my-custom-shortcut',
 *             category: 'my-category',
 *             description: __( 'My custom shortcut' ),
 *             keyCombination: {
 *                 modifier: 'primary',
 *                 character: 'j',
 *             },
 *         } );
 *     }, [] );
 *
 *     const shortcut = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getShortcutKeyCombination(
 *                 'custom/my-custom-shortcut'
 *             ),
 *         []
 *     );
 *
 *     return shortcut ? (
 *         <p>{ __( 'Shortcut is registered.' ) }</p>
 *     ) : (
 *         <p>{ __( 'Shortcut is not registered.' ) }</p>
 *     );
 * };
 *```
 * @return {Object} action.
 */
function registerShortcut({
  name,
  category,
  description,
  keyCombination,
  aliases
}) {
  return {
    type: 'REGISTER_SHORTCUT',
    name,
    category,
    keyCombination,
    aliases,
    description
  };
}

/**
 * Returns an action object used to unregister a keyboard shortcut.
 *
 * @param {string} name Shortcut name.
 *
 * @example
 *
 *```js
 * import { useEffect } from 'react';
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect, useDispatch } from '@wordpress/data';
 * import { __ } from '@wordpress/i18n';
 *
 * const ExampleComponent = () => {
 *     const { unregisterShortcut } = useDispatch( keyboardShortcutsStore );
 *
 *     useEffect( () => {
 *         unregisterShortcut( 'core/editor/next-region' );
 *     }, [] );
 *
 *     const shortcut = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getShortcutKeyCombination(
 *                 'core/editor/next-region'
 *             ),
 *         []
 *     );
 *
 *     return shortcut ? (
 *         <p>{ __( 'Shortcut is not unregistered.' ) }</p>
 *     ) : (
 *         <p>{ __( 'Shortcut is unregistered.' ) }</p>
 *     );
 * };
 *```
 * @return {Object} action.
 */
function unregisterShortcut(name) {
  return {
    type: 'UNREGISTER_SHORTCUT',
    name
  };
}

;// CONCATENATED MODULE: external ["wp","keycodes"]
const external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/selectors.js
/**
 * WordPress dependencies
 */



/** @typedef {import('./actions').WPShortcutKeyCombination} WPShortcutKeyCombination */

/** @typedef {import('@wordpress/keycodes').WPKeycodeHandlerByModifier} WPKeycodeHandlerByModifier */

/**
 * Shared reference to an empty array for cases where it is important to avoid
 * returning a new array reference on every invocation.
 *
 * @type {Array<any>}
 */
const EMPTY_ARRAY = [];

/**
 * Shortcut formatting methods.
 *
 * @property {WPKeycodeHandlerByModifier} display     Display formatting.
 * @property {WPKeycodeHandlerByModifier} rawShortcut Raw shortcut formatting.
 * @property {WPKeycodeHandlerByModifier} ariaLabel   ARIA label formatting.
 */
const FORMATTING_METHODS = {
  display: external_wp_keycodes_namespaceObject.displayShortcut,
  raw: external_wp_keycodes_namespaceObject.rawShortcut,
  ariaLabel: external_wp_keycodes_namespaceObject.shortcutAriaLabel
};

/**
 * Returns a string representing the key combination.
 *
 * @param {?WPShortcutKeyCombination} shortcut       Key combination.
 * @param {keyof FORMATTING_METHODS}  representation Type of representation
 *                                                   (display, raw, ariaLabel).
 *
 * @return {string?} Shortcut representation.
 */
function getKeyCombinationRepresentation(shortcut, representation) {
  if (!shortcut) {
    return null;
  }
  return shortcut.modifier ? FORMATTING_METHODS[representation][shortcut.modifier](shortcut.character) : shortcut.character;
}

/**
 * Returns the main key combination for a given shortcut name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Shortcut name.
 *
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 * import { createInterpolateElement } from '@wordpress/element';
 * import { sprintf } from '@wordpress/i18n';
 * const ExampleComponent = () => {
 *     const {character, modifier} = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getShortcutKeyCombination(
 *                 'core/editor/next-region'
 *             ),
 *         []
 *     );
 *
 *     return (
 *         <div>
 *             { createInterpolateElement(
 *                 sprintf(
 *                     'Character: <code>%s</code> / Modifier: <code>%s</code>',
 *                     character,
 *                     modifier
 *                 ),
 *                 {
 *                     code: <code />,
 *                 }
 *             ) }
 *         </div>
 *     );
 * };
 *```
 *
 * @return {WPShortcutKeyCombination?} Key combination.
 */
function getShortcutKeyCombination(state, name) {
  return state[name] ? state[name].keyCombination : null;
}

/**
 * Returns a string representing the main key combination for a given shortcut name.
 *
 * @param {Object}                   state          Global state.
 * @param {string}                   name           Shortcut name.
 * @param {keyof FORMATTING_METHODS} representation Type of representation
 *                                                  (display, raw, ariaLabel).
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 * import { sprintf } from '@wordpress/i18n';
 *
 * const ExampleComponent = () => {
 *     const {display, raw, ariaLabel} = useSelect(
 *         ( select ) =>{
 *             return {
 *                 display: select( keyboardShortcutsStore ).getShortcutRepresentation('core/editor/next-region' ),
 *                 raw: select( keyboardShortcutsStore ).getShortcutRepresentation('core/editor/next-region','raw' ),
 *                 ariaLabel: select( keyboardShortcutsStore ).getShortcutRepresentation('core/editor/next-region', 'ariaLabel')
 *             }
 *         },
 *         []
 *     );
 *
 *     return (
 *         <ul>
 *             <li>{ sprintf( 'display string: %s', display ) }</li>
 *             <li>{ sprintf( 'raw string: %s', raw ) }</li>
 *             <li>{ sprintf( 'ariaLabel string: %s', ariaLabel ) }</li>
 *         </ul>
 *     );
 * };
 *```
 *
 * @return {string?} Shortcut representation.
 */
function getShortcutRepresentation(state, name, representation = 'display') {
  const shortcut = getShortcutKeyCombination(state, name);
  return getKeyCombinationRepresentation(shortcut, representation);
}

/**
 * Returns the shortcut description given its name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Shortcut name.
 *
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 * import { __ } from '@wordpress/i18n';
 * const ExampleComponent = () => {
 *     const shortcutDescription = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getShortcutDescription( 'core/editor/next-region' ),
 *         []
 *     );
 *
 *     return shortcutDescription ? (
 *         <div>{ shortcutDescription }</div>
 *     ) : (
 *         <div>{ __( 'No description.' ) }</div>
 *     );
 * };
 *```
 * @return {string?} Shortcut description.
 */
function getShortcutDescription(state, name) {
  return state[name] ? state[name].description : null;
}

/**
 * Returns the aliases for a given shortcut name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Shortcut name.
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 * import { createInterpolateElement } from '@wordpress/element';
 * import { sprintf } from '@wordpress/i18n';
 * const ExampleComponent = () => {
 *     const shortcutAliases = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getShortcutAliases(
 *                 'core/editor/next-region'
 *             ),
 *         []
 *     );
 *
 *     return (
 *         shortcutAliases.length > 0 && (
 *             <ul>
 *                 { shortcutAliases.map( ( { character, modifier }, index ) => (
 *                     <li key={ index }>
 *                         { createInterpolateElement(
 *                             sprintf(
 *                                 'Character: <code>%s</code> / Modifier: <code>%s</code>',
 *                                 character,
 *                                 modifier
 *                             ),
 *                             {
 *                                 code: <code />,
 *                             }
 *                         ) }
 *                     </li>
 *                 ) ) }
 *             </ul>
 *         )
 *     );
 * };
 *```
 *
 * @return {WPShortcutKeyCombination[]} Key combinations.
 */
function getShortcutAliases(state, name) {
  return state[name] && state[name].aliases ? state[name].aliases : EMPTY_ARRAY;
}

/**
 * Returns the shortcuts that include aliases for a given shortcut name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Shortcut name.
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 * import { createInterpolateElement } from '@wordpress/element';
 * import { sprintf } from '@wordpress/i18n';
 *
 * const ExampleComponent = () => {
 *     const allShortcutKeyCombinations = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getAllShortcutKeyCombinations(
 *                 'core/editor/next-region'
 *             ),
 *         []
 *     );
 *
 *     return (
 *         allShortcutKeyCombinations.length > 0 && (
 *             <ul>
 *                 { allShortcutKeyCombinations.map(
 *                     ( { character, modifier }, index ) => (
 *                         <li key={ index }>
 *                             { createInterpolateElement(
 *                                 sprintf(
 *                                     'Character: <code>%s</code> / Modifier: <code>%s</code>',
 *                                     character,
 *                                     modifier
 *                                 ),
 *                                 {
 *                                     code: <code />,
 *                                 }
 *                             ) }
 *                         </li>
 *                     )
 *                 ) }
 *             </ul>
 *         )
 *     );
 * };
 *```
 *
 * @return {WPShortcutKeyCombination[]} Key combinations.
 */
const getAllShortcutKeyCombinations = (0,external_wp_data_namespaceObject.createSelector)((state, name) => {
  return [getShortcutKeyCombination(state, name), ...getShortcutAliases(state, name)].filter(Boolean);
}, (state, name) => [state[name]]);

/**
 * Returns the raw representation of all the keyboard combinations of a given shortcut name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Shortcut name.
 *
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 * import { createInterpolateElement } from '@wordpress/element';
 * import { sprintf } from '@wordpress/i18n';
 *
 * const ExampleComponent = () => {
 *     const allShortcutRawKeyCombinations = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getAllShortcutRawKeyCombinations(
 *                 'core/editor/next-region'
 *             ),
 *         []
 *     );
 *
 *     return (
 *         allShortcutRawKeyCombinations.length > 0 && (
 *             <ul>
 *                 { allShortcutRawKeyCombinations.map(
 *                     ( shortcutRawKeyCombination, index ) => (
 *                         <li key={ index }>
 *                             { createInterpolateElement(
 *                                 sprintf(
 *                                     ' <code>%s</code>',
 *                                     shortcutRawKeyCombination
 *                                 ),
 *                                 {
 *                                     code: <code />,
 *                                 }
 *                             ) }
 *                         </li>
 *                     )
 *                 ) }
 *             </ul>
 *         )
 *     );
 * };
 *```
 *
 * @return {string[]} Shortcuts.
 */
const getAllShortcutRawKeyCombinations = (0,external_wp_data_namespaceObject.createSelector)((state, name) => {
  return getAllShortcutKeyCombinations(state, name).map(combination => getKeyCombinationRepresentation(combination, 'raw'));
}, (state, name) => [state[name]]);

/**
 * Returns the shortcut names list for a given category name.
 *
 * @param {Object} state Global state.
 * @param {string} name  Category name.
 * @example
 *
 *```js
 * import { store as keyboardShortcutsStore } from '@wordpress/keyboard-shortcuts';
 * import { useSelect } from '@wordpress/data';
 *
 * const ExampleComponent = () => {
 *     const categoryShortcuts = useSelect(
 *         ( select ) =>
 *             select( keyboardShortcutsStore ).getCategoryShortcuts(
 *                 'block'
 *             ),
 *         []
 *     );
 *
 *     return (
 *         categoryShortcuts.length > 0 && (
 *             <ul>
 *                 { categoryShortcuts.map( ( categoryShortcut ) => (
 *                     <li key={ categoryShortcut }>{ categoryShortcut }</li>
 *                 ) ) }
 *             </ul>
 *         )
 *     );
 * };
 *```
 * @return {string[]} Shortcut names.
 */
const getCategoryShortcuts = (0,external_wp_data_namespaceObject.createSelector)((state, categoryName) => {
  return Object.entries(state).filter(([, shortcut]) => shortcut.category === categoryName).map(([name]) => name);
}, state => [state]);

;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/store/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



const STORE_NAME = 'core/keyboard-shortcuts';

/**
 * Store definition for the keyboard shortcuts namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */
const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: store_reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/hooks/use-shortcut-event-match.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Returns a function to check if a keyboard event matches a shortcut name.
 *
 * @return {Function} A function to check if a keyboard event matches a
 *                    predefined shortcut combination.
 */
function useShortcutEventMatch() {
  const {
    getAllShortcutKeyCombinations
  } = (0,external_wp_data_namespaceObject.useSelect)(store);

  /**
   * A function to check if a keyboard event matches a predefined shortcut
   * combination.
   *
   * @param {string}        name  Shortcut name.
   * @param {KeyboardEvent} event Event to check.
   *
   * @return {boolean} True if the event matches any shortcuts, false if not.
   */
  function isMatch(name, event) {
    return getAllShortcutKeyCombinations(name).some(({
      modifier,
      character
    }) => {
      return external_wp_keycodes_namespaceObject.isKeyboardEvent[modifier](event, character);
    });
  }
  return isMatch;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/context.js
/* wp:polyfill */
/**
 * WordPress dependencies
 */

const globalShortcuts = new Set();
const globalListener = event => {
  for (const keyboardShortcut of globalShortcuts) {
    keyboardShortcut(event);
  }
};
const context = (0,external_wp_element_namespaceObject.createContext)({
  add: shortcut => {
    if (globalShortcuts.size === 0) {
      document.addEventListener('keydown', globalListener);
    }
    globalShortcuts.add(shortcut);
  },
  delete: shortcut => {
    globalShortcuts.delete(shortcut);
    if (globalShortcuts.size === 0) {
      document.removeEventListener('keydown', globalListener);
    }
  }
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/hooks/use-shortcut.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



/**
 * Attach a keyboard shortcut handler.
 *
 * @param {string}   name               Shortcut name.
 * @param {Function} callback           Shortcut callback.
 * @param {Object}   options            Shortcut options.
 * @param {boolean}  options.isDisabled Whether to disable to shortut.
 */
function useShortcut(name, callback, {
  isDisabled = false
} = {}) {
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

;// CONCATENATED MODULE: external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/components/shortcut-provider.js
/* wp:polyfill */
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


const {
  Provider
} = context;

/**
 * Handles callbacks added to context by `useShortcut`.
 * Adding a provider allows to register contextual shortcuts
 * that are only active when a certain part of the UI is focused.
 *
 * @param {Object} props Props to pass to `div`.
 *
 * @return {Element} Component.
 */
function ShortcutProvider(props) {
  const [keyboardShortcuts] = (0,external_wp_element_namespaceObject.useState)(() => new Set());
  function onKeyDown(event) {
    if (props.onKeyDown) {
      props.onKeyDown(event);
    }
    for (const keyboardShortcut of keyboardShortcuts) {
      keyboardShortcut(event);
    }
  }

  /* eslint-disable jsx-a11y/no-static-element-interactions */
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(Provider, {
    value: keyboardShortcuts,
    children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
      ...props,
      onKeyDown: onKeyDown
    })
  });
  /* eslint-enable jsx-a11y/no-static-element-interactions */
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/keyboard-shortcuts/build-module/index.js





(window.wp = window.wp || {}).keyboardShortcuts = __webpack_exports__;
/******/ })()
;