/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 7734:
/***/ ((module) => {



// do not edit .js files directly - edit src/index.jst


  var envHasBigInt64Array = typeof BigInt64Array !== 'undefined';


module.exports = function equal(a, b) {
  if (a === b) return true;

  if (a && b && typeof a == 'object' && typeof b == 'object') {
    if (a.constructor !== b.constructor) return false;

    var length, i, keys;
    if (Array.isArray(a)) {
      length = a.length;
      if (length != b.length) return false;
      for (i = length; i-- !== 0;)
        if (!equal(a[i], b[i])) return false;
      return true;
    }


    if ((a instanceof Map) && (b instanceof Map)) {
      if (a.size !== b.size) return false;
      for (i of a.entries())
        if (!b.has(i[0])) return false;
      for (i of a.entries())
        if (!equal(i[1], b.get(i[0]))) return false;
      return true;
    }

    if ((a instanceof Set) && (b instanceof Set)) {
      if (a.size !== b.size) return false;
      for (i of a.entries())
        if (!b.has(i[0])) return false;
      return true;
    }

    if (ArrayBuffer.isView(a) && ArrayBuffer.isView(b)) {
      length = a.length;
      if (length != b.length) return false;
      for (i = length; i-- !== 0;)
        if (a[i] !== b[i]) return false;
      return true;
    }


    if (a.constructor === RegExp) return a.source === b.source && a.flags === b.flags;
    if (a.valueOf !== Object.prototype.valueOf) return a.valueOf() === b.valueOf();
    if (a.toString !== Object.prototype.toString) return a.toString() === b.toString();

    keys = Object.keys(a);
    length = keys.length;
    if (length !== Object.keys(b).length) return false;

    for (i = length; i-- !== 0;)
      if (!Object.prototype.hasOwnProperty.call(b, keys[i])) return false;

    for (i = length; i-- !== 0;) {
      var key = keys[i];

      if (!equal(a[key], b[key])) return false;
    }

    return true;
  }

  // true if both NaN, false otherwise
  return a!==a && b!==b;
};


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
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
  initialize: () => (/* binding */ initialize),
  store: () => (/* reexport */ store)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/customize-widgets/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  __experimentalGetInsertionPoint: () => (__experimentalGetInsertionPoint),
  isInserterOpened: () => (isInserterOpened)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/customize-widgets/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  setIsInserterOpened: () => (setIsInserterOpened)
});

;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","blockLibrary"]
const external_wp_blockLibrary_namespaceObject = window["wp"]["blockLibrary"];
;// external ["wp","widgets"]
const external_wp_widgets_namespaceObject = window["wp"]["widgets"];
;// external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// external ["wp","preferences"]
const external_wp_preferences_namespaceObject = window["wp"]["preferences"];
;// external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// external ["wp","hooks"]
const external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// ./node_modules/@wordpress/customize-widgets/build-module/components/error-boundary/index.js
/**
 * WordPress dependencies
 */







function CopyButton({
  text,
  children
}) {
  const ref = (0,external_wp_compose_namespaceObject.useCopyToClipboard)(text);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Button, {
    size: "compact",
    variant: "secondary",
    ref: ref,
    children: children
  });
}
class ErrorBoundary extends external_wp_element_namespaceObject.Component {
  constructor() {
    super(...arguments);
    this.state = {
      error: null
    };
  }
  componentDidCatch(error) {
    this.setState({
      error
    });
    (0,external_wp_hooks_namespaceObject.doAction)('editor.ErrorBoundary.errorLogged', error);
  }
  render() {
    const {
      error
    } = this.state;
    if (!error) {
      return this.props.children;
    }
    return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.Warning, {
      className: "customize-widgets-error-boundary",
      actions: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(CopyButton, {
        text: error.stack,
        children: (0,external_wp_i18n_namespaceObject.__)('Copy Error')
      }, "copy-error")],
      children: (0,external_wp_i18n_namespaceObject.__)('The editor has encountered an unexpected error.')
    });
  }
}

;// external ["wp","coreData"]
const external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// external ["wp","mediaUtils"]
const external_wp_mediaUtils_namespaceObject = window["wp"]["mediaUtils"];
;// ./node_modules/@wordpress/customize-widgets/build-module/components/block-inspector-button/index.js
/**
 * WordPress dependencies
 */






function BlockInspectorButton({
  inspector,
  closeMenu,
  ...props
}) {
  const selectedBlockClientId = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_blockEditor_namespaceObject.store).getSelectedBlockClientId(), []);
  const selectedBlock = (0,external_wp_element_namespaceObject.useMemo)(() => document.getElementById(`block-${selectedBlockClientId}`), [selectedBlockClientId]);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuItem, {
    onClick: () => {
      // Open the inspector.
      inspector.open({
        returnFocusWhenClose: selectedBlock
      });
      // Then close the dropdown menu.
      closeMenu();
    },
    ...props,
    children: (0,external_wp_i18n_namespaceObject.__)('Show more settings')
  });
}
/* harmony default export */ const block_inspector_button = (BlockInspectorButton);

;// ./node_modules/clsx/dist/clsx.mjs
function r(e){var t,f,n="";if("string"==typeof e||"number"==typeof e)n+=e;else if("object"==typeof e)if(Array.isArray(e)){var o=e.length;for(t=0;t<o;t++)e[t]&&(f=r(e[t]))&&(n&&(n+=" "),n+=f)}else for(f in e)e[f]&&(n&&(n+=" "),n+=f);return n}function clsx(){for(var e,t,f=0,n="",o=arguments.length;f<o;f++)(e=arguments[f])&&(t=r(e))&&(n&&(n+=" "),n+=t);return n}/* harmony default export */ const dist_clsx = (clsx);
;// external ["wp","keycodes"]
const external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// ./node_modules/@wordpress/icons/build-module/library/undo.js
/**
 * WordPress dependencies
 */


const undo = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z"
  })
});
/* harmony default export */ const library_undo = (undo);

;// ./node_modules/@wordpress/icons/build-module/library/redo.js
/**
 * WordPress dependencies
 */


const redo = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z"
  })
});
/* harmony default export */ const library_redo = (redo);

;// ./node_modules/@wordpress/icons/build-module/library/plus.js
/**
 * WordPress dependencies
 */


const plus = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M11 12.5V17.5H12.5V12.5H17.5V11H12.5V6H11V11H6V12.5H11Z"
  })
});
/* harmony default export */ const library_plus = (plus);

;// ./node_modules/@wordpress/icons/build-module/library/close-small.js
/**
 * WordPress dependencies
 */


const closeSmall = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"
  })
});
/* harmony default export */ const close_small = (closeSmall);

;// ./node_modules/@wordpress/customize-widgets/build-module/store/reducer.js
/**
 * WordPress dependencies
 */


/**
 * Reducer tracking whether the inserter is open.
 *
 * @param {boolean|Object} state
 * @param {Object}         action
 */
function blockInserterPanel(state = false, action) {
  switch (action.type) {
    case 'SET_IS_INSERTER_OPENED':
      return action.value;
  }
  return state;
}
/* harmony default export */ const reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  blockInserterPanel
}));

;// ./node_modules/@wordpress/customize-widgets/build-module/store/selectors.js
const EMPTY_INSERTION_POINT = {
  rootClientId: undefined,
  insertionIndex: undefined
};

/**
 * Returns true if the inserter is opened.
 *
 * @param {Object} state Global application state.
 *
 * @example
 * ```js
 * import { store as customizeWidgetsStore } from '@wordpress/customize-widgets';
 * import { __ } from '@wordpress/i18n';
 * import { useSelect } from '@wordpress/data';
 *
 * const ExampleComponent = () => {
 *    const { isInserterOpened } = useSelect(
 *        ( select ) => select( customizeWidgetsStore ),
 *        []
 *    );
 *
 *    return isInserterOpened()
 *        ? __( 'Inserter is open' )
 *        : __( 'Inserter is closed.' );
 * };
 * ```
 *
 * @return {boolean} Whether the inserter is opened.
 */
function isInserterOpened(state) {
  return !!state.blockInserterPanel;
}

/**
 * Get the insertion point for the inserter.
 *
 * @param {Object} state Global application state.
 *
 * @return {Object} The root client ID and index to insert at.
 */
function __experimentalGetInsertionPoint(state) {
  if (typeof state.blockInserterPanel === 'boolean') {
    return EMPTY_INSERTION_POINT;
  }
  return state.blockInserterPanel;
}

;// ./node_modules/@wordpress/customize-widgets/build-module/store/actions.js
/**
 * Returns an action object used to open/close the inserter.
 *
 * @param {boolean|Object} value                Whether the inserter should be
 *                                              opened (true) or closed (false).
 *                                              To specify an insertion point,
 *                                              use an object.
 * @param {string}         value.rootClientId   The root client ID to insert at.
 * @param {number}         value.insertionIndex The index to insert at.
 *
 * @example
 * ```js
 * import { useState } from 'react';
 * import { store as customizeWidgetsStore } from '@wordpress/customize-widgets';
 * import { __ } from '@wordpress/i18n';
 * import { useDispatch } from '@wordpress/data';
 * import { Button } from '@wordpress/components';
 *
 * const ExampleComponent = () => {
 *   const { setIsInserterOpened } = useDispatch( customizeWidgetsStore );
 *   const [ isOpen, setIsOpen ] = useState( false );
 *
 *    return (
 *        <Button
 *            onClick={ () => {
 *                setIsInserterOpened( ! isOpen );
 *                setIsOpen( ! isOpen );
 *            } }
 *        >
 *            { __( 'Open/close inserter' ) }
 *        </Button>
 *    );
 * };
 * ```
 *
 * @return {Object} Action object.
 */
function setIsInserterOpened(value) {
  return {
    type: 'SET_IS_INSERTER_OPENED',
    value
  };
}

;// ./node_modules/@wordpress/customize-widgets/build-module/store/constants.js
/**
 * Module Constants
 */
const STORE_NAME = 'core/customize-widgets';

;// ./node_modules/@wordpress/customize-widgets/build-module/store/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





/**
 * Block editor data store configuration.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#registering-a-store
 *
 * @type {Object}
 */
const storeConfig = {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject
};

/**
 * Store definition for the edit widgets namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */
const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, storeConfig);
(0,external_wp_data_namespaceObject.register)(store);

;// ./node_modules/@wordpress/customize-widgets/build-module/components/inserter/index.js
/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */


function Inserter({
  setIsOpened
}) {
  const inserterTitleId = (0,external_wp_compose_namespaceObject.useInstanceId)(Inserter, 'customize-widget-layout__inserter-panel-title');
  const insertionPoint = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).__experimentalGetInsertionPoint(), []);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
    className: "customize-widgets-layout__inserter-panel",
    "aria-labelledby": inserterTitleId,
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
      className: "customize-widgets-layout__inserter-panel-header",
      children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h2", {
        id: inserterTitleId,
        className: "customize-widgets-layout__inserter-panel-header-title",
        children: (0,external_wp_i18n_namespaceObject.__)('Add a block')
      }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Button, {
        size: "small",
        icon: close_small,
        onClick: () => setIsOpened(false),
        "aria-label": (0,external_wp_i18n_namespaceObject.__)('Close inserter')
      })]
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
      className: "customize-widgets-layout__inserter-panel-content",
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.__experimentalLibrary, {
        rootClientId: insertionPoint.rootClientId,
        __experimentalInsertionIndex: insertionPoint.insertionIndex,
        showInserterHelpPanel: true,
        onSelect: () => setIsOpened(false)
      })
    })]
  });
}
/* harmony default export */ const components_inserter = (Inserter);

;// ./node_modules/@wordpress/icons/build-module/library/more-vertical.js
/**
 * WordPress dependencies
 */


const moreVertical = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z"
  })
});
/* harmony default export */ const more_vertical = (moreVertical);

;// ./node_modules/@wordpress/icons/build-module/library/external.js
/**
 * WordPress dependencies
 */


const external = /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24",
  children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, {
    d: "M19.5 4.5h-7V6h4.44l-5.97 5.97 1.06 1.06L18 7.06v4.44h1.5v-7Zm-13 1a2 2 0 0 0-2 2v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2v-3H17v3a.5.5 0 0 1-.5.5h-10a.5.5 0 0 1-.5-.5v-10a.5.5 0 0 1 .5-.5h3V5.5h-3Z"
  })
});
/* harmony default export */ const library_external = (external);

;// external ["wp","keyboardShortcuts"]
const external_wp_keyboardShortcuts_namespaceObject = window["wp"]["keyboardShortcuts"];
;// ./node_modules/@wordpress/customize-widgets/build-module/components/keyboard-shortcut-help-modal/config.js
/**
 * WordPress dependencies
 */

const textFormattingShortcuts = [{
  keyCombination: {
    modifier: 'primary',
    character: 'b'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Make the selected text bold.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'i'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Make the selected text italic.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'k'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Convert the selected text into a link.')
}, {
  keyCombination: {
    modifier: 'primaryShift',
    character: 'k'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Remove a link.')
}, {
  keyCombination: {
    character: '[['
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Insert a link to a post or page.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'u'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Underline the selected text.')
}, {
  keyCombination: {
    modifier: 'access',
    character: 'd'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Strikethrough the selected text.')
}, {
  keyCombination: {
    modifier: 'access',
    character: 'x'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Make the selected text inline code.')
}, {
  keyCombination: {
    modifier: 'access',
    character: '0'
  },
  aliases: [{
    modifier: 'access',
    character: '7'
  }],
  description: (0,external_wp_i18n_namespaceObject.__)('Convert the current heading to a paragraph.')
}, {
  keyCombination: {
    modifier: 'access',
    character: '1-6'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Convert the current paragraph or heading to a heading of level 1 to 6.')
}, {
  keyCombination: {
    modifier: 'primaryShift',
    character: 'SPACE'
  },
  description: (0,external_wp_i18n_namespaceObject.__)('Add non breaking space.')
}];

;// ./node_modules/@wordpress/customize-widgets/build-module/components/keyboard-shortcut-help-modal/shortcut.js
/**
 * WordPress dependencies
 */



function KeyCombination({
  keyCombination,
  forceAriaLabel
}) {
  const shortcut = keyCombination.modifier ? external_wp_keycodes_namespaceObject.displayShortcutList[keyCombination.modifier](keyCombination.character) : keyCombination.character;
  const ariaLabel = keyCombination.modifier ? external_wp_keycodes_namespaceObject.shortcutAriaLabel[keyCombination.modifier](keyCombination.character) : keyCombination.character;
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("kbd", {
    className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-key-combination",
    "aria-label": forceAriaLabel || ariaLabel,
    children: (Array.isArray(shortcut) ? shortcut : [shortcut]).map((character, index) => {
      if (character === '+') {
        return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_element_namespaceObject.Fragment, {
          children: character
        }, index);
      }
      return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("kbd", {
        className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-key",
        children: character
      }, index);
    })
  });
}
function Shortcut({
  description,
  keyCombination,
  aliases = [],
  ariaLabel
}) {
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
      className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-description",
      children: description
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
      className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-term",
      children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(KeyCombination, {
        keyCombination: keyCombination,
        forceAriaLabel: ariaLabel
      }), aliases.map((alias, index) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(KeyCombination, {
        keyCombination: alias,
        forceAriaLabel: ariaLabel
      }, index))]
    })]
  });
}
/* harmony default export */ const keyboard_shortcut_help_modal_shortcut = (Shortcut);

;// ./node_modules/@wordpress/customize-widgets/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


function DynamicShortcut({
  name
}) {
  const {
    keyCombination,
    description,
    aliases
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    const {
      getShortcutKeyCombination,
      getShortcutDescription,
      getShortcutAliases
    } = select(external_wp_keyboardShortcuts_namespaceObject.store);
    return {
      keyCombination: getShortcutKeyCombination(name),
      aliases: getShortcutAliases(name),
      description: getShortcutDescription(name)
    };
  }, [name]);
  if (!keyCombination) {
    return null;
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(keyboard_shortcut_help_modal_shortcut, {
    keyCombination: keyCombination,
    description: description,
    aliases: aliases
  });
}
/* harmony default export */ const dynamic_shortcut = (DynamicShortcut);

;// ./node_modules/@wordpress/customize-widgets/build-module/components/keyboard-shortcut-help-modal/index.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */




const ShortcutList = ({
  shortcuts
}) =>
/*#__PURE__*/
/*
 * Disable reason: The `list` ARIA role is redundant but
 * Safari+VoiceOver won't announce the list otherwise.
 */
/* eslint-disable jsx-a11y/no-redundant-roles */
(0,external_ReactJSXRuntime_namespaceObject.jsx)("ul", {
  className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-list",
  role: "list",
  children: shortcuts.map((shortcut, index) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("li", {
    className: "customize-widgets-keyboard-shortcut-help-modal__shortcut",
    children: typeof shortcut === 'string' ? /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(dynamic_shortcut, {
      name: shortcut
    }) : /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(keyboard_shortcut_help_modal_shortcut, {
      ...shortcut
    })
  }, index))
})
/* eslint-enable jsx-a11y/no-redundant-roles */;
const ShortcutSection = ({
  title,
  shortcuts,
  className
}) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("section", {
  className: dist_clsx('customize-widgets-keyboard-shortcut-help-modal__section', className),
  children: [!!title && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h2", {
    className: "customize-widgets-keyboard-shortcut-help-modal__section-title",
    children: title
  }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ShortcutList, {
    shortcuts: shortcuts
  })]
});
const ShortcutCategorySection = ({
  title,
  categoryName,
  additionalShortcuts = []
}) => {
  const categoryShortcuts = (0,external_wp_data_namespaceObject.useSelect)(select => {
    return select(external_wp_keyboardShortcuts_namespaceObject.store).getCategoryShortcuts(categoryName);
  }, [categoryName]);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ShortcutSection, {
    title: title,
    shortcuts: categoryShortcuts.concat(additionalShortcuts)
  });
};
function KeyboardShortcutHelpModal({
  isModalActive,
  toggleModal
}) {
  const {
    registerShortcut
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_keyboardShortcuts_namespaceObject.store);
  registerShortcut({
    name: 'core/customize-widgets/keyboard-shortcuts',
    category: 'main',
    description: (0,external_wp_i18n_namespaceObject.__)('Display these keyboard shortcuts.'),
    keyCombination: {
      modifier: 'access',
      character: 'h'
    }
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/customize-widgets/keyboard-shortcuts', toggleModal);
  if (!isModalActive) {
    return null;
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.Modal, {
    className: "customize-widgets-keyboard-shortcut-help-modal",
    title: (0,external_wp_i18n_namespaceObject.__)('Keyboard shortcuts'),
    onRequestClose: toggleModal,
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ShortcutSection, {
      className: "customize-widgets-keyboard-shortcut-help-modal__main-shortcuts",
      shortcuts: ['core/customize-widgets/keyboard-shortcuts']
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ShortcutCategorySection, {
      title: (0,external_wp_i18n_namespaceObject.__)('Global shortcuts'),
      categoryName: "global"
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ShortcutCategorySection, {
      title: (0,external_wp_i18n_namespaceObject.__)('Selection shortcuts'),
      categoryName: "selection"
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ShortcutCategorySection, {
      title: (0,external_wp_i18n_namespaceObject.__)('Block shortcuts'),
      categoryName: "block",
      additionalShortcuts: [{
        keyCombination: {
          character: '/'
        },
        description: (0,external_wp_i18n_namespaceObject.__)('Change the block type after adding a new paragraph.'),
        /* translators: The forward-slash character. e.g. '/'. */
        ariaLabel: (0,external_wp_i18n_namespaceObject.__)('Forward-slash')
      }]
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ShortcutSection, {
      title: (0,external_wp_i18n_namespaceObject.__)('Text formatting'),
      shortcuts: textFormattingShortcuts
    })]
  });
}

;// ./node_modules/@wordpress/customize-widgets/build-module/components/more-menu/index.js
/**
 * WordPress dependencies
 */








/**
 * Internal dependencies
 */


function MoreMenu() {
  const [isKeyboardShortcutsModalActive, setIsKeyboardShortcutsModalVisible] = (0,external_wp_element_namespaceObject.useState)(false);
  const toggleKeyboardShortcutsModal = () => setIsKeyboardShortcutsModalVisible(!isKeyboardShortcutsModalActive);
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/customize-widgets/keyboard-shortcuts', toggleKeyboardShortcutsModal);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarDropdownMenu, {
      icon: more_vertical,
      label: (0,external_wp_i18n_namespaceObject.__)('Options'),
      popoverProps: {
        placement: 'bottom-end',
        className: 'more-menu-dropdown__content'
      },
      toggleProps: {
        tooltipPosition: 'bottom',
        size: 'compact'
      },
      children: () => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuGroup, {
          label: (0,external_wp_i18n_namespaceObject._x)('View', 'noun'),
          children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
            scope: "core/customize-widgets",
            name: "fixedToolbar",
            label: (0,external_wp_i18n_namespaceObject.__)('Top toolbar'),
            info: (0,external_wp_i18n_namespaceObject.__)('Access all block and document tools in a single place'),
            messageActivated: (0,external_wp_i18n_namespaceObject.__)('Top toolbar activated'),
            messageDeactivated: (0,external_wp_i18n_namespaceObject.__)('Top toolbar deactivated')
          })
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.MenuGroup, {
          label: (0,external_wp_i18n_namespaceObject.__)('Tools'),
          children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuItem, {
            onClick: () => {
              setIsKeyboardShortcutsModalVisible(true);
            },
            shortcut: external_wp_keycodes_namespaceObject.displayShortcut.access('h'),
            children: (0,external_wp_i18n_namespaceObject.__)('Keyboard shortcuts')
          }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
            scope: "core/customize-widgets",
            name: "welcomeGuide",
            label: (0,external_wp_i18n_namespaceObject.__)('Welcome Guide')
          }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.MenuItem, {
            role: "menuitem",
            icon: library_external,
            href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/documentation/article/block-based-widgets-editor/'),
            target: "_blank",
            rel: "noopener noreferrer",
            children: [(0,external_wp_i18n_namespaceObject.__)('Help'), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.VisuallyHidden, {
              as: "span",
              children: /* translators: accessibility text */
              (0,external_wp_i18n_namespaceObject.__)('(opens in a new tab)')
            })]
          })]
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuGroup, {
          label: (0,external_wp_i18n_namespaceObject.__)('Preferences'),
          children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_preferences_namespaceObject.PreferenceToggleMenuItem, {
            scope: "core/customize-widgets",
            name: "keepCaretInsideBlock",
            label: (0,external_wp_i18n_namespaceObject.__)('Contain text cursor inside block'),
            info: (0,external_wp_i18n_namespaceObject.__)('Aids screen readers by stopping text caret from leaving blocks.'),
            messageActivated: (0,external_wp_i18n_namespaceObject.__)('Contain text cursor inside block activated'),
            messageDeactivated: (0,external_wp_i18n_namespaceObject.__)('Contain text cursor inside block deactivated')
          })
        })]
      })
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(KeyboardShortcutHelpModal, {
      isModalActive: isKeyboardShortcutsModalActive,
      toggleModal: toggleKeyboardShortcutsModal
    })]
  });
}

;// ./node_modules/@wordpress/customize-widgets/build-module/components/header/index.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */



function Header({
  sidebar,
  inserter,
  isInserterOpened,
  setIsInserterOpened,
  isFixedToolbarActive
}) {
  const [[hasUndo, hasRedo], setUndoRedo] = (0,external_wp_element_namespaceObject.useState)([sidebar.hasUndo(), sidebar.hasRedo()]);
  const shortcut = (0,external_wp_keycodes_namespaceObject.isAppleOS)() ? external_wp_keycodes_namespaceObject.displayShortcut.primaryShift('z') : external_wp_keycodes_namespaceObject.displayShortcut.primary('y');
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    return sidebar.subscribeHistory(() => {
      setUndoRedo([sidebar.hasUndo(), sidebar.hasRedo()]);
    });
  }, [sidebar]);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
      className: dist_clsx('customize-widgets-header', {
        'is-fixed-toolbar-active': isFixedToolbarActive
      }),
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_blockEditor_namespaceObject.NavigableToolbar, {
        className: "customize-widgets-header-toolbar",
        "aria-label": (0,external_wp_i18n_namespaceObject.__)('Document tools'),
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarButton, {
          icon: !(0,external_wp_i18n_namespaceObject.isRTL)() ? library_undo : library_redo
          /* translators: button label text should, if possible, be under 16 characters. */,
          label: (0,external_wp_i18n_namespaceObject.__)('Undo'),
          shortcut: external_wp_keycodes_namespaceObject.displayShortcut.primary('z'),
          disabled: !hasUndo,
          onClick: sidebar.undo,
          className: "customize-widgets-editor-history-button undo-button"
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarButton, {
          icon: !(0,external_wp_i18n_namespaceObject.isRTL)() ? library_redo : library_undo
          /* translators: button label text should, if possible, be under 16 characters. */,
          label: (0,external_wp_i18n_namespaceObject.__)('Redo'),
          shortcut: shortcut,
          disabled: !hasRedo,
          onClick: sidebar.redo,
          className: "customize-widgets-editor-history-button redo-button"
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarButton, {
          className: "customize-widgets-header-toolbar__inserter-toggle",
          isPressed: isInserterOpened,
          variant: "primary",
          icon: library_plus,
          label: (0,external_wp_i18n_namespaceObject._x)('Add block', 'Generic label for block inserter button'),
          onClick: () => {
            setIsInserterOpened(isOpen => !isOpen);
          }
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(MoreMenu, {})]
      })
    }), (0,external_wp_element_namespaceObject.createPortal)(/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(components_inserter, {
      setIsOpened: setIsInserterOpened
    }), inserter.contentContainer[0])]
  });
}
/* harmony default export */ const header = (Header);

;// ./node_modules/@wordpress/customize-widgets/build-module/components/inserter/use-inserter.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */

function useInserter(inserter) {
  const isInserterOpened = (0,external_wp_data_namespaceObject.useSelect)(select => select(store).isInserterOpened(), []);
  const {
    setIsInserterOpened
  } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isInserterOpened) {
      inserter.open();
    } else {
      inserter.close();
    }
  }, [inserter, isInserterOpened]);
  return [isInserterOpened, (0,external_wp_element_namespaceObject.useCallback)(updater => {
    let isOpen = updater;
    if (typeof updater === 'function') {
      isOpen = updater((0,external_wp_data_namespaceObject.select)(store).isInserterOpened());
    }
    setIsInserterOpened(isOpen);
  }, [setIsInserterOpened])];
}

// EXTERNAL MODULE: ./node_modules/fast-deep-equal/es6/index.js
var es6 = __webpack_require__(7734);
var es6_default = /*#__PURE__*/__webpack_require__.n(es6);
;// external ["wp","isShallowEqual"]
const external_wp_isShallowEqual_namespaceObject = window["wp"]["isShallowEqual"];
var external_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_wp_isShallowEqual_namespaceObject);
;// ./node_modules/@wordpress/customize-widgets/build-module/utils.js
// @ts-check
/**
 * WordPress dependencies
 */



/**
 * Convert settingId to widgetId.
 *
 * @param {string} settingId The setting id.
 * @return {string} The widget id.
 */
function settingIdToWidgetId(settingId) {
  const matches = settingId.match(/^widget_(.+)(?:\[(\d+)\])$/);
  if (matches) {
    const idBase = matches[1];
    const number = parseInt(matches[2], 10);
    return `${idBase}-${number}`;
  }
  return settingId;
}

/**
 * Transform a block to a customizable widget.
 *
 * @param {WPBlock} block          The block to be transformed from.
 * @param {Object}  existingWidget The widget to be extended from.
 * @return {Object} The transformed widget.
 */
function blockToWidget(block, existingWidget = null) {
  let widget;
  const isValidLegacyWidgetBlock = block.name === 'core/legacy-widget' && (block.attributes.id || block.attributes.instance);
  if (isValidLegacyWidgetBlock) {
    if (block.attributes.id) {
      // Widget that does not extend WP_Widget.
      widget = {
        id: block.attributes.id
      };
    } else {
      const {
        encoded,
        hash,
        raw,
        ...rest
      } = block.attributes.instance;

      // Widget that extends WP_Widget.
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
      content: (0,external_wp_blocks_namespaceObject.serialize)(block)
    };
    widget = {
      idBase: 'block',
      widgetClass: 'WP_Widget_Block',
      instance: {
        raw_instance: instance
      }
    };
  }
  const {
    form,
    rendered,
    ...restExistingWidget
  } = existingWidget || {};
  return {
    ...restExistingWidget,
    ...widget
  };
}

/**
 * Transform a widget to a block.
 *
 * @param {Object} widget          The widget to be transformed from.
 * @param {string} widget.id       The widget id.
 * @param {string} widget.idBase   The id base of the widget.
 * @param {number} widget.number   The number/index of the widget.
 * @param {Object} widget.instance The instance of the widget.
 * @return {WPBlock} The transformed block.
 */
function widgetToBlock({
  id,
  idBase,
  number,
  instance
}) {
  let block;
  const {
    encoded_serialized_instance: encoded,
    instance_hash_key: hash,
    raw_instance: raw,
    ...rest
  } = instance;
  if (idBase === 'block') {
    var _raw$content;
    const parsedBlocks = (0,external_wp_blocks_namespaceObject.parse)((_raw$content = raw.content) !== null && _raw$content !== void 0 ? _raw$content : '', {
      __unstableSkipAutop: true
    });
    block = parsedBlocks.length ? parsedBlocks[0] : (0,external_wp_blocks_namespaceObject.createBlock)('core/paragraph', {});
  } else if (number) {
    // Widget that extends WP_Widget.
    block = (0,external_wp_blocks_namespaceObject.createBlock)('core/legacy-widget', {
      idBase,
      instance: {
        encoded,
        hash,
        raw,
        ...rest
      }
    });
  } else {
    // Widget that does not extend WP_Widget.
    block = (0,external_wp_blocks_namespaceObject.createBlock)('core/legacy-widget', {
      id
    });
  }
  return (0,external_wp_widgets_namespaceObject.addWidgetIdToBlock)(block, id);
}

;// ./node_modules/@wordpress/customize-widgets/build-module/components/sidebar-block-editor/use-sidebar-block-editor.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */

function widgetsToBlocks(widgets) {
  return widgets.map(widget => widgetToBlock(widget));
}
function useSidebarBlockEditor(sidebar) {
  const [blocks, setBlocks] = (0,external_wp_element_namespaceObject.useState)(() => widgetsToBlocks(sidebar.getWidgets()));
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    return sidebar.subscribe((prevWidgets, nextWidgets) => {
      setBlocks(prevBlocks => {
        const prevWidgetsMap = new Map(prevWidgets.map(widget => [widget.id, widget]));
        const prevBlocksMap = new Map(prevBlocks.map(block => [(0,external_wp_widgets_namespaceObject.getWidgetIdFromBlock)(block), block]));
        const nextBlocks = nextWidgets.map(nextWidget => {
          const prevWidget = prevWidgetsMap.get(nextWidget.id);

          // Bail out updates.
          if (prevWidget && prevWidget === nextWidget) {
            return prevBlocksMap.get(nextWidget.id);
          }
          return widgetToBlock(nextWidget);
        });

        // Bail out updates.
        if (external_wp_isShallowEqual_default()(prevBlocks, nextBlocks)) {
          return prevBlocks;
        }
        return nextBlocks;
      });
    });
  }, [sidebar]);
  const onChangeBlocks = (0,external_wp_element_namespaceObject.useCallback)(nextBlocks => {
    setBlocks(prevBlocks => {
      if (external_wp_isShallowEqual_default()(prevBlocks, nextBlocks)) {
        return prevBlocks;
      }
      const prevBlocksMap = new Map(prevBlocks.map(block => [(0,external_wp_widgets_namespaceObject.getWidgetIdFromBlock)(block), block]));
      const nextWidgets = nextBlocks.map(nextBlock => {
        const widgetId = (0,external_wp_widgets_namespaceObject.getWidgetIdFromBlock)(nextBlock);

        // Update existing widgets.
        if (widgetId && prevBlocksMap.has(widgetId)) {
          const prevBlock = prevBlocksMap.get(widgetId);
          const prevWidget = sidebar.getWidget(widgetId);

          // Bail out updates by returning the previous widgets.
          // Deep equality is necessary until the block editor's internals changes.
          if (es6_default()(nextBlock, prevBlock) && prevWidget) {
            return prevWidget;
          }
          return blockToWidget(nextBlock, prevWidget);
        }

        // Add a new widget.
        return blockToWidget(nextBlock);
      });

      // Bail out updates if the updated widgets are the same.
      if (external_wp_isShallowEqual_default()(sidebar.getWidgets(), nextWidgets)) {
        return prevBlocks;
      }
      const addedWidgetIds = sidebar.setWidgets(nextWidgets);
      return nextBlocks.reduce((updatedNextBlocks, nextBlock, index) => {
        const addedWidgetId = addedWidgetIds[index];
        if (addedWidgetId !== null) {
          // Only create a new instance if necessary to prevent
          // the whole editor from re-rendering on every edit.
          if (updatedNextBlocks === nextBlocks) {
            updatedNextBlocks = nextBlocks.slice();
          }
          updatedNextBlocks[index] = (0,external_wp_widgets_namespaceObject.addWidgetIdToBlock)(nextBlock, addedWidgetId);
        }
        return updatedNextBlocks;
      }, nextBlocks);
    });
  }, [sidebar]);
  return [blocks, onChangeBlocks, onChangeBlocks];
}

;// ./node_modules/@wordpress/customize-widgets/build-module/components/focus-control/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


const FocusControlContext = (0,external_wp_element_namespaceObject.createContext)();
function FocusControl({
  api,
  sidebarControls,
  children
}) {
  const [focusedWidgetIdRef, setFocusedWidgetIdRef] = (0,external_wp_element_namespaceObject.useState)({
    current: null
  });
  const focusWidget = (0,external_wp_element_namespaceObject.useCallback)(widgetId => {
    for (const sidebarControl of sidebarControls) {
      const widgets = sidebarControl.setting.get();
      if (widgets.includes(widgetId)) {
        sidebarControl.sectionInstance.expand({
          // Schedule it after the complete callback so that
          // it won't be overridden by the "Back" button focus.
          completeCallback() {
            // Create a "ref-like" object every time to ensure
            // the same widget id can also triggers the focus control.
            setFocusedWidgetIdRef({
              current: widgetId
            });
          }
        });
        break;
      }
    }
  }, [sidebarControls]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    function handleFocus(settingId) {
      const widgetId = settingIdToWidgetId(settingId);
      focusWidget(widgetId);
    }
    let previewBound = false;
    function handleReady() {
      api.previewer.preview.bind('focus-control-for-setting', handleFocus);
      previewBound = true;
    }
    api.previewer.bind('ready', handleReady);
    return () => {
      api.previewer.unbind('ready', handleReady);
      if (previewBound) {
        api.previewer.preview.unbind('focus-control-for-setting', handleFocus);
      }
    };
  }, [api, focusWidget]);
  const context = (0,external_wp_element_namespaceObject.useMemo)(() => [focusedWidgetIdRef, focusWidget], [focusedWidgetIdRef, focusWidget]);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(FocusControlContext.Provider, {
    value: context,
    children: children
  });
}
const useFocusControl = () => (0,external_wp_element_namespaceObject.useContext)(FocusControlContext);

;// ./node_modules/@wordpress/customize-widgets/build-module/components/focus-control/use-blocks-focus-control.js
/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */

function useBlocksFocusControl(blocks) {
  const {
    selectBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const [focusedWidgetIdRef] = useFocusControl();
  const blocksRef = (0,external_wp_element_namespaceObject.useRef)(blocks);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    blocksRef.current = blocks;
  }, [blocks]);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (focusedWidgetIdRef.current) {
      const focusedBlock = blocksRef.current.find(block => (0,external_wp_widgets_namespaceObject.getWidgetIdFromBlock)(block) === focusedWidgetIdRef.current);
      if (focusedBlock) {
        selectBlock(focusedBlock.clientId);
        // If the block is already being selected, the DOM node won't
        // get focused again automatically.
        // We select the DOM and focus it manually here.
        const blockNode = document.querySelector(`[data-block="${focusedBlock.clientId}"]`);
        blockNode?.focus();
      }
    }
  }, [focusedWidgetIdRef, selectBlock]);
}

;// external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// ./node_modules/@wordpress/customize-widgets/build-module/lock-unlock.js
/**
 * WordPress dependencies
 */

const {
  lock,
  unlock
} = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.', '@wordpress/customize-widgets');

;// ./node_modules/@wordpress/customize-widgets/build-module/components/sidebar-block-editor/sidebar-editor-provider.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




const {
  ExperimentalBlockEditorProvider
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
function SidebarEditorProvider({
  sidebar,
  settings,
  children
}) {
  const [blocks, onInput, onChange] = useSidebarBlockEditor(sidebar);
  useBlocksFocusControl(blocks);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ExperimentalBlockEditorProvider, {
    value: blocks,
    onInput: onInput,
    onChange: onChange,
    settings: settings,
    useSubRegistry: false,
    children: children
  });
}

;// ./node_modules/@wordpress/customize-widgets/build-module/components/welcome-guide/index.js
/**
 * WordPress dependencies
 */





function WelcomeGuide({
  sidebar
}) {
  const {
    toggle
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_preferences_namespaceObject.store);
  const isEntirelyBlockWidgets = sidebar.getWidgets().every(widget => widget.id.startsWith('block-'));
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("div", {
    className: "customize-widgets-welcome-guide",
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
      className: "customize-widgets-welcome-guide__image__wrapper",
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("picture", {
        children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("source", {
          srcSet: "https://s.w.org/images/block-editor/welcome-editor.svg",
          media: "(prefers-reduced-motion: reduce)"
        }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("img", {
          className: "customize-widgets-welcome-guide__image",
          src: "https://s.w.org/images/block-editor/welcome-editor.gif",
          width: "312",
          height: "240",
          alt: ""
        })]
      })
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("h1", {
      className: "customize-widgets-welcome-guide__heading",
      children: (0,external_wp_i18n_namespaceObject.__)('Welcome to block Widgets')
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("p", {
      className: "customize-widgets-welcome-guide__text",
      children: isEntirelyBlockWidgets ? (0,external_wp_i18n_namespaceObject.__)('Your theme provides different “block” areas for you to add and edit content. Try adding a search bar, social icons, or other types of blocks here and see how they’ll look on your site.') : (0,external_wp_i18n_namespaceObject.__)('You can now add any block to your site’s widget areas. Don’t worry, all of your favorite widgets still work flawlessly.')
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Button, {
      size: "compact",
      variant: "primary",
      onClick: () => toggle('core/customize-widgets', 'welcomeGuide'),
      children: (0,external_wp_i18n_namespaceObject.__)('Got it')
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("hr", {
      className: "customize-widgets-welcome-guide__separator"
    }), !isEntirelyBlockWidgets && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("p", {
      className: "customize-widgets-welcome-guide__more-info",
      children: [(0,external_wp_i18n_namespaceObject.__)('Want to stick with the old widgets?'), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("br", {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ExternalLink, {
        href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/plugins/classic-widgets/'),
        children: (0,external_wp_i18n_namespaceObject.__)('Get the Classic Widgets plugin.')
      })]
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)("p", {
      className: "customize-widgets-welcome-guide__more-info",
      children: [(0,external_wp_i18n_namespaceObject.__)('New to the block editor?'), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("br", {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ExternalLink, {
        href: (0,external_wp_i18n_namespaceObject.__)('https://wordpress.org/documentation/article/wordpress-block-editor/'),
        children: (0,external_wp_i18n_namespaceObject.__)("Here's a detailed guide.")
      })]
    })]
  });
}

;// ./node_modules/@wordpress/customize-widgets/build-module/components/keyboard-shortcuts/index.js
/**
 * WordPress dependencies
 */





function KeyboardShortcuts({
  undo,
  redo,
  save
}) {
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/customize-widgets/undo', event => {
    undo();
    event.preventDefault();
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/customize-widgets/redo', event => {
    redo();
    event.preventDefault();
  });
  (0,external_wp_keyboardShortcuts_namespaceObject.useShortcut)('core/customize-widgets/save', event => {
    event.preventDefault();
    save();
  });
  return null;
}
function KeyboardShortcutsRegister() {
  const {
    registerShortcut,
    unregisterShortcut
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_keyboardShortcuts_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    registerShortcut({
      name: 'core/customize-widgets/undo',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Undo your last changes.'),
      keyCombination: {
        modifier: 'primary',
        character: 'z'
      }
    });
    registerShortcut({
      name: 'core/customize-widgets/redo',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Redo your last undo.'),
      keyCombination: {
        modifier: 'primaryShift',
        character: 'z'
      },
      // Disable on Apple OS because it conflicts with the browser's
      // history shortcut. It's a fine alias for both Windows and Linux.
      // Since there's no conflict for Ctrl+Shift+Z on both Windows and
      // Linux, we keep it as the default for consistency.
      aliases: (0,external_wp_keycodes_namespaceObject.isAppleOS)() ? [] : [{
        modifier: 'primary',
        character: 'y'
      }]
    });
    registerShortcut({
      name: 'core/customize-widgets/save',
      category: 'global',
      description: (0,external_wp_i18n_namespaceObject.__)('Save your changes.'),
      keyCombination: {
        modifier: 'primary',
        character: 's'
      }
    });
    return () => {
      unregisterShortcut('core/customize-widgets/undo');
      unregisterShortcut('core/customize-widgets/redo');
      unregisterShortcut('core/customize-widgets/save');
    };
  }, [registerShortcut]);
  return null;
}
KeyboardShortcuts.Register = KeyboardShortcutsRegister;
/* harmony default export */ const keyboard_shortcuts = (KeyboardShortcuts);

;// ./node_modules/@wordpress/customize-widgets/build-module/components/block-appender/index.js
/**
 * WordPress dependencies
 */




function BlockAppender(props) {
  const ref = (0,external_wp_element_namespaceObject.useRef)();
  const isBlocksListEmpty = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_blockEditor_namespaceObject.store).getBlockCount() === 0);

  // Move the focus to the block appender to prevent focus from
  // being lost when emptying the widget area.
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (isBlocksListEmpty && ref.current) {
      const {
        ownerDocument
      } = ref.current;
      if (!ownerDocument.activeElement || ownerDocument.activeElement === ownerDocument.body) {
        ref.current.focus();
      }
    }
  }, [isBlocksListEmpty]);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.ButtonBlockAppender, {
    ...props,
    ref: ref
  });
}

;// ./node_modules/@wordpress/customize-widgets/build-module/components/sidebar-block-editor/index.js
/**
 * WordPress dependencies
 */









/**
 * Internal dependencies
 */









const {
  ExperimentalBlockCanvas: BlockCanvas
} = unlock(external_wp_blockEditor_namespaceObject.privateApis);
const {
  BlockKeyboardShortcuts
} = unlock(external_wp_blockLibrary_namespaceObject.privateApis);
function SidebarBlockEditor({
  blockEditorSettings,
  sidebar,
  inserter,
  inspector
}) {
  const [isInserterOpened, setIsInserterOpened] = useInserter(inserter);
  const isMediumViewport = (0,external_wp_compose_namespaceObject.useViewportMatch)('small');
  const {
    hasUploadPermissions,
    isFixedToolbarActive,
    keepCaretInsideBlock,
    isWelcomeGuideActive
  } = (0,external_wp_data_namespaceObject.useSelect)(select => {
    var _select$canUser;
    const {
      get
    } = select(external_wp_preferences_namespaceObject.store);
    return {
      hasUploadPermissions: (_select$canUser = select(external_wp_coreData_namespaceObject.store).canUser('create', {
        kind: 'root',
        name: 'media'
      })) !== null && _select$canUser !== void 0 ? _select$canUser : true,
      isFixedToolbarActive: !!get('core/customize-widgets', 'fixedToolbar'),
      keepCaretInsideBlock: !!get('core/customize-widgets', 'keepCaretInsideBlock'),
      isWelcomeGuideActive: !!get('core/customize-widgets', 'welcomeGuide')
    };
  }, []);
  const settings = (0,external_wp_element_namespaceObject.useMemo)(() => {
    let mediaUploadBlockEditor;
    if (hasUploadPermissions) {
      mediaUploadBlockEditor = ({
        onError,
        ...argumentsObject
      }) => {
        (0,external_wp_mediaUtils_namespaceObject.uploadMedia)({
          wpAllowedMimeTypes: blockEditorSettings.allowedMimeTypes,
          onError: ({
            message
          }) => onError(message),
          ...argumentsObject
        });
      };
    }
    return {
      ...blockEditorSettings,
      __experimentalSetIsInserterOpened: setIsInserterOpened,
      mediaUpload: mediaUploadBlockEditor,
      hasFixedToolbar: isFixedToolbarActive || !isMediumViewport,
      keepCaretInsideBlock,
      editorTool: 'edit',
      __unstableHasCustomAppender: true
    };
  }, [hasUploadPermissions, blockEditorSettings, isFixedToolbarActive, isMediumViewport, keepCaretInsideBlock, setIsInserterOpened]);
  if (isWelcomeGuideActive) {
    return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(WelcomeGuide, {
      sidebar: sidebar
    });
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(keyboard_shortcuts.Register, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(BlockKeyboardShortcuts, {}), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(SidebarEditorProvider, {
      sidebar: sidebar,
      settings: settings,
      children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(keyboard_shortcuts, {
        undo: sidebar.undo,
        redo: sidebar.redo,
        save: sidebar.save
      }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(header, {
        sidebar: sidebar,
        inserter: inserter,
        isInserterOpened: isInserterOpened,
        setIsInserterOpened: setIsInserterOpened,
        isFixedToolbarActive: isFixedToolbarActive || !isMediumViewport
      }), (isFixedToolbarActive || !isMediumViewport) && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockToolbar, {
        hideDragHandle: true
      }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(BlockCanvas, {
        shouldIframe: false,
        styles: settings.defaultEditorStyles,
        height: "100%",
        children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockList, {
          renderAppender: BlockAppender
        })
      }), (0,external_wp_element_namespaceObject.createPortal)(
      /*#__PURE__*/
      // This is a temporary hack to prevent button component inside <BlockInspector>
      // from submitting form when type="button" is not specified.
      (0,external_ReactJSXRuntime_namespaceObject.jsx)("form", {
        onSubmit: event => event.preventDefault(),
        children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockInspector, {})
      }), inspector.contentContainer[0])]
    }), /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.__unstableBlockSettingsMenuFirstItem, {
      children: ({
        onClose
      }) => /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(block_inspector_button, {
        inspector: inspector,
        closeMenu: onClose
      })
    })]
  });
}

;// ./node_modules/@wordpress/customize-widgets/build-module/components/sidebar-controls/index.js
/**
 * WordPress dependencies
 */


const SidebarControlsContext = (0,external_wp_element_namespaceObject.createContext)();
function SidebarControls({
  sidebarControls,
  activeSidebarControl,
  children
}) {
  const context = (0,external_wp_element_namespaceObject.useMemo)(() => ({
    sidebarControls,
    activeSidebarControl
  }), [sidebarControls, activeSidebarControl]);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(SidebarControlsContext.Provider, {
    value: context,
    children: children
  });
}
function useSidebarControls() {
  const {
    sidebarControls
  } = (0,external_wp_element_namespaceObject.useContext)(SidebarControlsContext);
  return sidebarControls;
}
function useActiveSidebarControl() {
  const {
    activeSidebarControl
  } = (0,external_wp_element_namespaceObject.useContext)(SidebarControlsContext);
  return activeSidebarControl;
}

;// ./node_modules/@wordpress/customize-widgets/build-module/components/customize-widgets/use-clear-selected-block.js
/**
 * WordPress dependencies
 */




/**
 * We can't just use <BlockSelectionClearer> because the customizer has
 * many root nodes rather than just one in the post editor.
 * We need to listen to the focus events in all those roots, and also in
 * the preview iframe.
 * This hook will clear the selected block when focusing outside the editor,
 * with a few exceptions:
 * 1. Focusing on popovers.
 * 2. Focusing on the inspector.
 * 3. Focusing on any modals/dialogs.
 * These cases are normally triggered by user interactions from the editor,
 * not by explicitly focusing outside the editor, hence no need for clearing.
 *
 * @param {Object} sidebarControl The sidebar control instance.
 * @param {Object} popoverRef     The ref object of the popover node container.
 */
function useClearSelectedBlock(sidebarControl, popoverRef) {
  const {
    hasSelectedBlock,
    hasMultiSelection
  } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_blockEditor_namespaceObject.store);
  const {
    clearSelectedBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if (popoverRef.current && sidebarControl) {
      const inspector = sidebarControl.inspector;
      const container = sidebarControl.container[0];
      const ownerDocument = container.ownerDocument;
      const ownerWindow = ownerDocument.defaultView;
      function handleClearSelectedBlock(element) {
        if (
        // 1. Make sure there are blocks being selected.
        (hasSelectedBlock() || hasMultiSelection()) &&
        // 2. The element should exist in the DOM (not deleted).
        element && ownerDocument.contains(element) &&
        // 3. It should also not exist in the container, the popover, nor the dialog.
        !container.contains(element) && !popoverRef.current.contains(element) && !element.closest('[role="dialog"]') &&
        // 4. The inspector should not be opened.
        !inspector.expanded()) {
          clearSelectedBlock();
        }
      }

      // Handle mouse down in the same document.
      function handleMouseDown(event) {
        handleClearSelectedBlock(event.target);
      }
      // Handle focusing outside the current document, like to iframes.
      function handleBlur() {
        handleClearSelectedBlock(ownerDocument.activeElement);
      }
      ownerDocument.addEventListener('mousedown', handleMouseDown);
      ownerWindow.addEventListener('blur', handleBlur);
      return () => {
        ownerDocument.removeEventListener('mousedown', handleMouseDown);
        ownerWindow.removeEventListener('blur', handleBlur);
      };
    }
  }, [popoverRef, sidebarControl, hasSelectedBlock, hasMultiSelection, clearSelectedBlock]);
}

;// ./node_modules/@wordpress/customize-widgets/build-module/components/customize-widgets/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */






function CustomizeWidgets({
  api,
  sidebarControls,
  blockEditorSettings
}) {
  const [activeSidebarControl, setActiveSidebarControl] = (0,external_wp_element_namespaceObject.useState)(null);
  const parentContainer = document.getElementById('customize-theme-controls');
  const popoverRef = (0,external_wp_element_namespaceObject.useRef)();
  useClearSelectedBlock(activeSidebarControl, popoverRef);
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    const unsubscribers = sidebarControls.map(sidebarControl => sidebarControl.subscribe(expanded => {
      if (expanded) {
        setActiveSidebarControl(sidebarControl);
      }
    }));
    return () => {
      unsubscribers.forEach(unsubscriber => unsubscriber());
    };
  }, [sidebarControls]);
  const activeSidebar = activeSidebarControl && (0,external_wp_element_namespaceObject.createPortal)(/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(ErrorBoundary, {
    children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(SidebarBlockEditor, {
      blockEditorSettings: blockEditorSettings,
      sidebar: activeSidebarControl.sidebarAdapter,
      inserter: activeSidebarControl.inserter,
      inspector: activeSidebarControl.inspector
    }, activeSidebarControl.id)
  }), activeSidebarControl.container[0]);

  // We have to portal this to the parent of both the editor and the inspector,
  // so that the popovers will appear above both of them.
  const popover = parentContainer && (0,external_wp_element_namespaceObject.createPortal)(/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)("div", {
    className: "customize-widgets-popover",
    ref: popoverRef,
    children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.Popover.Slot, {})
  }), parentContainer);
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.SlotFillProvider, {
    children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(SidebarControls, {
      sidebarControls: sidebarControls,
      activeSidebarControl: activeSidebarControl,
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(FocusControl, {
        api: api,
        sidebarControls: sidebarControls,
        children: [activeSidebar, popover]
      })
    })
  });
}

;// ./node_modules/@wordpress/customize-widgets/build-module/controls/inspector-section.js
function getInspectorSection() {
  const {
    wp: {
      customize
    }
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
      this.contentContainer[0].classList.add('customize-widgets-layout__inspector');
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
              // Return focus after finishing the transition.
              if (this.returnFocusWhenClose && !this.contentContainer[0].contains(this.returnFocusWhenClose)) {
                this.returnFocusWhenClose.focus();
              }
            }
          });
        }
      }
    }
    open({
      returnFocusWhenClose
    } = {}) {
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
      // Overridden collapse() function. Mostly call the parent collapse(), but also
      // move our .isOpen to false.
      // Initially, I tried tracking this with onChangeExpanded(), but it doesn't work
      // because the block settings sidebar is a layer "on top of" the G editor sidebar.
      //
      // For example, when closing the block settings sidebar, the G
      // editor sidebar would display, and onChangeExpanded in
      // inspector-section would run with expanded=true, but I want
      // isOpen to be false when the block settings is closed.
      this.isOpen = false;
      super.collapse(options);
    }
    triggerActiveCallbacks() {
      // Manually fire the callbacks associated with moving this.active
      // from false to true.  "active" is always true for this section,
      // and "isContextuallyActive" reflects if the block settings
      // sidebar is currently visible, that is, it has replaced the main
      // Gutenberg view.
      // The WP customizer only checks ".isContextuallyActive()" when
      // ".active" changes values. But our ".active" never changes value.
      // The WP customizer never foresaw a section being used a way we
      // fit the block settings sidebar into a section. By manually
      // triggering the "this.active" callbacks, we force the WP
      // customizer to query our .isContextuallyActive() function and
      // update its view of our status.
      this.active.callbacks.fireWith(this.active, [false, true]);
    }
  };
}

;// ./node_modules/@wordpress/customize-widgets/build-module/controls/sidebar-section.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */

const getInspectorSectionId = sidebarId => `widgets-inspector-${sidebarId}`;
function getSidebarSection() {
  const {
    wp: {
      customize
    }
  } = window;
  const reduceMotionMediaQuery = window.matchMedia('(prefers-reduced-motion: reduce)');
  let isReducedMotion = reduceMotionMediaQuery.matches;
  reduceMotionMediaQuery.addEventListener('change', event => {
    isReducedMotion = event.matches;
  });
  return class SidebarSection extends customize.Section {
    ready() {
      const InspectorSection = getInspectorSection();
      this.inspector = new InspectorSection(getInspectorSectionId(this.id), {
        title: (0,external_wp_i18n_namespaceObject.__)('Block Settings'),
        parentSection: this,
        customizeAction: [(0,external_wp_i18n_namespaceObject.__)('Customizing'), (0,external_wp_i18n_namespaceObject.__)('Widgets'), this.params.title].join(' ▸ ')
      });
      customize.section.add(this.inspector);
      this.contentContainer[0].classList.add('customize-widgets__sidebar-section');
    }
    hasSubSectionOpened() {
      return this.inspector.expanded();
    }
    onChangeExpanded(expanded, _args) {
      const controls = this.controls();
      const args = {
        ..._args,
        completeCallback() {
          controls.forEach(control => {
            control.onChangeSectionExpanded?.(expanded, args);
          });
          _args.completeCallback?.();
        }
      };
      if (args.manualTransition) {
        if (expanded) {
          this.contentContainer.addClass(['busy', 'open']);
          this.contentContainer.removeClass('is-sub-section-open');
          this.contentContainer.closest('.wp-full-overlay').addClass('section-open');
        } else {
          this.contentContainer.addClass(['busy', 'is-sub-section-open']);
          this.contentContainer.closest('.wp-full-overlay').addClass('section-open');
          this.contentContainer.removeClass('open');
        }
        const handleTransitionEnd = () => {
          this.contentContainer.removeClass('busy');
          args.completeCallback();
        };
        if (isReducedMotion) {
          handleTransitionEnd();
        } else {
          this.contentContainer.one('transitionend', handleTransitionEnd);
        }
      } else {
        super.onChangeExpanded(expanded, args);
      }
    }
  };
}

;// ./node_modules/@wordpress/customize-widgets/build-module/components/sidebar-block-editor/sidebar-adapter.js
/**
 * Internal dependencies
 */

const {
  wp
} = window;
function parseWidgetId(widgetId) {
  const matches = widgetId.match(/^(.+)-(\d+)$/);
  if (matches) {
    return {
      idBase: matches[1],
      number: parseInt(matches[2], 10)
    };
  }

  // Likely an old single widget.
  return {
    idBase: widgetId
  };
}
function widgetIdToSettingId(widgetId) {
  const {
    idBase,
    number
  } = parseWidgetId(widgetId);
  if (number) {
    return `widget_${idBase}[${number}]`;
  }
  return `widget_${idBase}`;
}

/**
 * This is a custom debounce function to call different callbacks depending on
 * whether it's the _leading_ call or not.
 *
 * @param {Function} leading  The callback that gets called first.
 * @param {Function} callback The callback that gets called after the first time.
 * @param {number}   timeout  The debounced time in milliseconds.
 * @return {Function} The debounced function.
 */
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
class SidebarAdapter {
  constructor(setting, api) {
    this.setting = setting;
    this.api = api;
    this.locked = false;
    this.widgetsCache = new WeakMap();
    this.subscribers = new Set();
    this.history = [this._getWidgetIds().map(widgetId => this.getWidget(widgetId))];
    this.historyIndex = 0;
    this.historySubscribers = new Set();
    // Debounce the input for 1 second.
    this._debounceSetHistory = debounce(this._pushHistory, this._replaceHistory, 1000);
    this.setting.bind(this._handleSettingChange.bind(this));
    this.api.bind('change', this._handleAllSettingsChange.bind(this));
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
    this.history = [...this.history.slice(0, this.historyIndex + 1), this._getWidgetIds().map(widgetId => this.getWidget(widgetId))];
    this.historyIndex += 1;
    this.historySubscribers.forEach(listener => listener());
  }
  _replaceHistory() {
    this.history[this.historyIndex] = this._getWidgetIds().map(widgetId => this.getWidget(widgetId));
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
    if (!setting.id.startsWith('widget_')) {
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
    if (widgetModel.get('is_multi') && !number) {
      widgetModel.set('multi_number', widgetModel.get('multi_number') + 1);
      number = widgetModel.get('multi_number');
    }
    const settingId = number ? `widget_${widget.idBase}[${number}]` : `widget_${widget.idBase}`;
    const settingArgs = {
      transport: wp.customize.Widgets.data.selectiveRefreshableWidgets[widgetModel.get('id_base')] ? 'postMessage' : 'refresh',
      previewer: this.setting.previewer
    };
    const setting = this.api.create(settingId, settingId, '', settingArgs);
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

    // Bail out update if nothing changed.
    if (prevWidget === widget) {
      return widget.id;
    }

    // Update existing setting if only the widget's instance changed.
    if (prevWidget.idBase && widget.idBase && prevWidget.idBase === widget.idBase) {
      const settingId = widgetIdToSettingId(widget.id);
      this.api(settingId).set(widget.instance);
      return widget.id;
    }

    // Otherwise delete and re-create.
    this._removeWidget(widget);
    return this._createWidget(widget);
  }
  getWidget(widgetId) {
    if (!widgetId) {
      return null;
    }
    const {
      idBase,
      number
    } = parseWidgetId(widgetId);
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
    const nextWidgetIds = nextWidgets.map(nextWidget => {
      if (nextWidget.id && this.getWidget(nextWidget.id)) {
        addedWidgetIds.push(null);
        return this._updateWidget(nextWidget);
      }
      const widgetId = this._createWidget(nextWidget);
      addedWidgetIds.push(widgetId);
      return widgetId;
    });
    const deletedWidgets = this.getWidgets().filter(widget => !nextWidgetIds.includes(widget.id));
    deletedWidgets.forEach(widget => this._removeWidget(widget));
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
    this.historySubscribers.forEach(listener => listener());
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
}

;// external ["wp","dom"]
const external_wp_dom_namespaceObject = window["wp"]["dom"];
;// ./node_modules/@wordpress/customize-widgets/build-module/controls/inserter-outer-section.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */

function getInserterOuterSection() {
  const {
    wp: {
      customize
    }
  } = window;
  const OuterSection = customize.OuterSection;
  // Override the OuterSection class to handle multiple outer sections.
  // It closes all the other outer sections whenever one is opened.
  // The result is that at most one outer section can be opened at the same time.
  customize.OuterSection = class extends OuterSection {
    onChangeExpanded(expanded, args) {
      if (expanded) {
        customize.section.each(section => {
          if (section.params.type === 'outer' && section.id !== this.id) {
            if (section.expanded()) {
              section.collapse();
            }
          }
        });
      }
      return super.onChangeExpanded(expanded, args);
    }
  };
  // Handle constructor so that "params.type" can be correctly pointed to "outer".
  customize.sectionConstructor.outer = customize.OuterSection;
  return class InserterOuterSection extends customize.OuterSection {
    constructor(...args) {
      super(...args);

      // This is necessary since we're creating a new class which is not identical to the original OuterSection.
      // @See https://github.com/WordPress/wordpress-develop/blob/42b05c397c50d9dc244083eff52991413909d4bd/src/js/_enqueues/wp/customize/controls.js#L1427-L1436
      this.params.type = 'outer';
      this.activeElementBeforeExpanded = null;
      const ownerWindow = this.contentContainer[0].ownerDocument.defaultView;

      // Handle closing the inserter when pressing the Escape key.
      ownerWindow.addEventListener('keydown', event => {
        if (this.expanded() && (event.keyCode === external_wp_keycodes_namespaceObject.ESCAPE || event.code === 'Escape') && !event.defaultPrevented) {
          event.preventDefault();
          event.stopPropagation();
          (0,external_wp_data_namespaceObject.dispatch)(store).setIsInserterOpened(false);
        }
      },
      // Use capture mode to make this run before other event listeners.
      true);
      this.contentContainer.addClass('widgets-inserter');

      // Set a flag if the state is being changed from open() or close().
      // Don't propagate the event if it's an internal action to prevent infinite loop.
      this.isFromInternalAction = false;
      this.expanded.bind(() => {
        if (!this.isFromInternalAction) {
          // Propagate the event to React to sync the state.
          (0,external_wp_data_namespaceObject.dispatch)(store).setIsInserterOpened(this.expanded());
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
            // We have to do this in a "completeCallback" or else the elements will not yet be visible/tabbable.
            // The first one should be the close button,
            // we want to skip it and choose the second one instead, which is the search box.
            const searchBox = external_wp_dom_namespaceObject.focus.tabbable.find(contentContainer)[1];
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
            // Return back the focus when closing the inserter.
            // Only do this if the active element which triggers the action is inside the inserter,
            // (the close button for instance). In that case the focus will be lost.
            // Otherwise, we don't hijack the focus when the user is focusing on other elements
            // (like the quick inserter).
            if (contentContainer.contains(activeElement)) {
              // Return back the focus when closing the inserter.
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

;// ./node_modules/@wordpress/customize-widgets/build-module/controls/sidebar-control.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



const getInserterId = controlId => `widgets-inserter-${controlId}`;
function getSidebarControl() {
  const {
    wp: {
      customize
    }
  } = window;
  return class SidebarControl extends customize.Control {
    constructor(...args) {
      super(...args);
      this.subscribers = new Set();
    }
    ready() {
      const InserterOuterSection = getInserterOuterSection();
      this.inserter = new InserterOuterSection(getInserterId(this.id), {});
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
        // Close the inserter when the section collapses.
        if (!expanded) {
          (0,external_wp_data_namespaceObject.dispatch)(store).setIsInserterOpened(false);
        }
        this.subscribers.forEach(subscriber => subscriber(expanded, args));
      }
    }
  };
}

;// ./node_modules/@wordpress/customize-widgets/build-module/filters/move-to-sidebar.js
/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */




const withMoveToSidebarToolbarItem = (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(BlockEdit => props => {
  let widgetId = (0,external_wp_widgets_namespaceObject.getWidgetIdFromBlock)(props);
  const sidebarControls = useSidebarControls();
  const activeSidebarControl = useActiveSidebarControl();
  const hasMultipleSidebars = sidebarControls?.length > 1;
  const blockName = props.name;
  const clientId = props.clientId;
  const canInsertBlockInSidebar = (0,external_wp_data_namespaceObject.useSelect)(select => {
    // Use an empty string to represent the root block list, which
    // in the customizer editor represents a sidebar/widget area.
    return select(external_wp_blockEditor_namespaceObject.store).canInsertBlockType(blockName, '');
  }, [blockName]);
  const block = (0,external_wp_data_namespaceObject.useSelect)(select => select(external_wp_blockEditor_namespaceObject.store).getBlock(clientId), [clientId]);
  const {
    removeBlock
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const [, focusWidget] = useFocusControl();
  function moveToSidebar(sidebarControlId) {
    const newSidebarControl = sidebarControls.find(sidebarControl => sidebarControl.id === sidebarControlId);
    if (widgetId) {
      /**
       * If there's a widgetId, move it to the other sidebar.
       */
      const oldSetting = activeSidebarControl.setting;
      const newSetting = newSidebarControl.setting;
      oldSetting(oldSetting().filter(id => id !== widgetId));
      newSetting([...newSetting(), widgetId]);
    } else {
      /**
       * If there isn't a widgetId, it's most likely a inner block.
       * First, remove the block in the original sidebar,
       * then, create a new widget in the new sidebar and get back its widgetId.
       */
      const sidebarAdapter = newSidebarControl.sidebarAdapter;
      removeBlock(clientId);
      const addedWidgetIds = sidebarAdapter.setWidgets([...sidebarAdapter.getWidgets(), blockToWidget(block)]);
      // The last non-null id is the added widget's id.
      widgetId = addedWidgetIds.reverse().find(id => !!id);
    }

    // Move focus to the moved widget and expand the sidebar.
    focusWidget(widgetId);
  }
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, {
    children: [/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(BlockEdit, {
      ...props
    }, "edit"), hasMultipleSidebars && canInsertBlockInSidebar && /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockControls, {
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_widgets_namespaceObject.MoveToWidgetArea, {
        widgetAreas: sidebarControls.map(sidebarControl => ({
          id: sidebarControl.id,
          name: sidebarControl.params.label,
          description: sidebarControl.params.description
        })),
        currentWidgetAreaId: activeSidebarControl?.id,
        onSelect: moveToSidebar
      })
    })]
  });
}, 'withMoveToSidebarToolbarItem');
(0,external_wp_hooks_namespaceObject.addFilter)('editor.BlockEdit', 'core/customize-widgets/block-edit', withMoveToSidebarToolbarItem);

;// ./node_modules/@wordpress/customize-widgets/build-module/filters/replace-media-upload.js
/**
 * WordPress dependencies
 */


const replaceMediaUpload = () => external_wp_mediaUtils_namespaceObject.MediaUpload;
(0,external_wp_hooks_namespaceObject.addFilter)('editor.MediaUpload', 'core/edit-widgets/replace-media-upload', replaceMediaUpload);

;// ./node_modules/@wordpress/customize-widgets/build-module/filters/wide-widget-display.js
/**
 * WordPress dependencies
 */



const {
  wp: wide_widget_display_wp
} = window;
const withWideWidgetDisplay = (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(BlockEdit => props => {
  var _wp$customize$Widgets;
  const {
    idBase
  } = props.attributes;
  const isWide = (_wp$customize$Widgets = wide_widget_display_wp.customize.Widgets.data.availableWidgets.find(widget => widget.id_base === idBase)?.is_wide) !== null && _wp$customize$Widgets !== void 0 ? _wp$customize$Widgets : false;
  return /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(BlockEdit, {
    ...props,
    isWide: isWide
  }, "edit");
}, 'withWideWidgetDisplay');
(0,external_wp_hooks_namespaceObject.addFilter)('editor.BlockEdit', 'core/customize-widgets/wide-widget-display', withWideWidgetDisplay);

;// ./node_modules/@wordpress/customize-widgets/build-module/filters/index.js
/**
 * Internal dependencies
 */




;// ./node_modules/@wordpress/customize-widgets/build-module/index.js
/**
 * WordPress dependencies
 */







/**
 * Internal dependencies
 */





const {
  wp: build_module_wp
} = window;
const DISABLED_BLOCKS = ['core/more', 'core/block', 'core/freeform', 'core/template-part'];
const ENABLE_EXPERIMENTAL_FSE_BLOCKS = false;

/**
 * Initializes the widgets block editor in the customizer.
 *
 * @param {string} editorName          The editor name.
 * @param {Object} blockEditorSettings Block editor settings.
 */
function initialize(editorName, blockEditorSettings) {
  (0,external_wp_data_namespaceObject.dispatch)(external_wp_preferences_namespaceObject.store).setDefaults('core/customize-widgets', {
    fixedToolbar: false,
    welcomeGuide: true
  });
  (0,external_wp_data_namespaceObject.dispatch)(external_wp_blocks_namespaceObject.store).reapplyBlockTypeFilters();
  const coreBlocks = (0,external_wp_blockLibrary_namespaceObject.__experimentalGetCoreBlocks)().filter(block => {
    return !(DISABLED_BLOCKS.includes(block.name) || block.name.startsWith('core/post') || block.name.startsWith('core/query') || block.name.startsWith('core/site') || block.name.startsWith('core/navigation'));
  });
  (0,external_wp_blockLibrary_namespaceObject.registerCoreBlocks)(coreBlocks);
  (0,external_wp_widgets_namespaceObject.registerLegacyWidgetBlock)();
  if (false) {}
  (0,external_wp_widgets_namespaceObject.registerLegacyWidgetVariations)(blockEditorSettings);
  (0,external_wp_widgets_namespaceObject.registerWidgetGroupBlock)();

  // As we are unregistering `core/freeform` to avoid the Classic block, we must
  // replace it with something as the default freeform content handler. Failure to
  // do this will result in errors in the default block parser.
  // see: https://github.com/WordPress/gutenberg/issues/33097
  (0,external_wp_blocks_namespaceObject.setFreeformContentHandlerName)('core/html');
  const SidebarControl = getSidebarControl(blockEditorSettings);
  build_module_wp.customize.sectionConstructor.sidebar = getSidebarSection();
  build_module_wp.customize.controlConstructor.sidebar_block_editor = SidebarControl;
  const container = document.createElement('div');
  document.body.appendChild(container);
  build_module_wp.customize.bind('ready', () => {
    const sidebarControls = [];
    build_module_wp.customize.control.each(control => {
      if (control instanceof SidebarControl) {
        sidebarControls.push(control);
      }
    });
    (0,external_wp_element_namespaceObject.createRoot)(container).render(/*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_element_namespaceObject.StrictMode, {
      children: /*#__PURE__*/(0,external_ReactJSXRuntime_namespaceObject.jsx)(CustomizeWidgets, {
        api: build_module_wp.customize,
        sidebarControls: sidebarControls,
        blockEditorSettings: blockEditorSettings
      })
    }));
  });
}


(window.wp = window.wp || {}).customizeWidgets = __webpack_exports__;
/******/ })()
;