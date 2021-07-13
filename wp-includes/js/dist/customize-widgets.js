this["wp"] = this["wp"] || {}; this["wp"]["customizeWidgets"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "9pbN");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1CF3":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["dom"]; }());

/***/ }),

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "6aBm":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["mediaUtils"]; }());

/***/ }),

/***/ "9pbN":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "initialize", function() { return /* binding */ initialize; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/customize-widgets/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "__unstableIsFeatureActive", function() { return __unstableIsFeatureActive; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/customize-widgets/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "__unstableToggleFeature", function() { return __unstableToggleFeature; });

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external ["wp","blockLibrary"]
var external_wp_blockLibrary_ = __webpack_require__("QyPg");

// EXTERNAL MODULE: external ["wp","widgets"]
var external_wp_widgets_ = __webpack_require__("GLVC");

// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__("HSyU");

// EXTERNAL MODULE: external ["wp","components"]
var external_wp_components_ = __webpack_require__("tI+e");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: external ["wp","coreData"]
var external_wp_coreData_ = __webpack_require__("jZUy");

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: external ["wp","blockEditor"]
var external_wp_blockEditor_ = __webpack_require__("axFQ");

// EXTERNAL MODULE: external ["wp","mediaUtils"]
var external_wp_mediaUtils_ = __webpack_require__("6aBm");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/block-inspector-button/index.js



/**
 * WordPress dependencies
 */






function BlockInspectorButton({
  inspector,
  closeMenu,
  ...props
}) {
  const selectedBlockClientId = Object(external_wp_data_["useSelect"])(select => select(external_wp_blockEditor_["store"]).getSelectedBlockClientId(), []);
  const selectedBlock = Object(external_wp_element_["useMemo"])(() => document.getElementById(`block-${selectedBlockClientId}`), [selectedBlockClientId]);
  return Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], Object(esm_extends["a" /* default */])({
    onClick: () => {
      // Open the inspector.
      inspector.open({
        returnFocusWhenClose: selectedBlock
      }); // Then close the dropdown menu.

      closeMenu();
    }
  }, props), Object(external_wp_i18n_["__"])('Show more settings'));
}

/* harmony default export */ var block_inspector_button = (BlockInspectorButton);

// EXTERNAL MODULE: ./node_modules/classnames/index.js
var classnames = __webpack_require__("TSYQ");
var classnames_default = /*#__PURE__*/__webpack_require__.n(classnames);

// EXTERNAL MODULE: external ["wp","keycodes"]
var external_wp_keycodes_ = __webpack_require__("RxS6");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/undo.js
var library_undo = __webpack_require__("Ntru");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/redo.js
var library_redo = __webpack_require__("K2cm");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/plus.js
var plus = __webpack_require__("Q4Sy");

// EXTERNAL MODULE: external ["wp","compose"]
var external_wp_compose_ = __webpack_require__("K9lf");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/close-small.js
var close_small = __webpack_require__("bWcr");

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/inserter/index.js


/**
 * WordPress dependencies
 */






function Inserter({
  setIsOpened
}) {
  const inserterTitleId = Object(external_wp_compose_["useInstanceId"])(Inserter, 'customize-widget-layout__inserter-panel-title');
  return Object(external_wp_element_["createElement"])("div", {
    className: "customize-widgets-layout__inserter-panel",
    "aria-labelledby": inserterTitleId
  }, Object(external_wp_element_["createElement"])("div", {
    className: "customize-widgets-layout__inserter-panel-header"
  }, Object(external_wp_element_["createElement"])("h2", {
    id: inserterTitleId,
    className: "customize-widgets-layout__inserter-panel-header-title"
  }, Object(external_wp_i18n_["__"])('Add a block')), Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    className: "customize-widgets-layout__inserter-panel-header-close-button",
    icon: close_small["a" /* default */],
    onClick: () => setIsOpened(false),
    "aria-label": Object(external_wp_i18n_["__"])('Close inserter')
  })), Object(external_wp_element_["createElement"])("div", {
    className: "customize-widgets-layout__inserter-panel-content"
  }, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["__experimentalLibrary"], {
    showInserterHelpPanel: true,
    onSelect: () => setIsOpened(false)
  })));
}

/* harmony default export */ var components_inserter = (Inserter);

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/more-vertical.js
var more_vertical = __webpack_require__("VKE3");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/external.js
var external = __webpack_require__("K+tz");

// EXTERNAL MODULE: external ["wp","keyboardShortcuts"]
var external_wp_keyboardShortcuts_ = __webpack_require__("hF7m");

// EXTERNAL MODULE: ./node_modules/@wordpress/icons/build-module/library/check.js
var check = __webpack_require__("RMJe");

// EXTERNAL MODULE: external ["wp","a11y"]
var external_wp_a11y_ = __webpack_require__("gdqT");

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/store/defaults.js
const PREFERENCES_DEFAULTS = {
  features: {
    fixedToolbar: false,
    welcomeGuide: true
  }
};

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/store/reducer.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Higher-order reducer creator which provides the given initial state for the
 * original reducer.
 *
 * @param {*} initialState Initial state to provide to reducer.
 *
 * @return {Function} Higher-order reducer.
 */

const createWithInitialState = initialState => reducer => {
  return (state = initialState, action) => reducer(state, action);
};
/**
 * Reducer returning the user preferences.
 *
 * @param {Object}  state                           Current state.
 * @param {Object}  action                          Dispatched action.
 *
 * @return {Object} Updated state.
 */


const preferences = Object(external_lodash_["flow"])([external_wp_data_["combineReducers"], createWithInitialState(PREFERENCES_DEFAULTS)])({
  features(state, action) {
    if (action.type === 'TOGGLE_FEATURE') {
      return { ...state,
        [action.feature]: !state[action.feature]
      };
    }

    return state;
  }

});
/* harmony default export */ var reducer = (Object(external_wp_data_["combineReducers"])({
  preferences
}));

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/store/selectors.js
/**
 * External dependencies
 */

/**
 * Returns whether the given feature is enabled or not.
 *
 * This function is unstable, as it is mostly copied from the edit-post
 * package. Editor features and preferences have a lot of scope for
 * being generalized and refactored.
 *
 * @param {Object} state   Global application state.
 * @param {string} feature Feature slug.
 *
 * @return {boolean} Is active.
 */

function __unstableIsFeatureActive(state, feature) {
  return Object(external_lodash_["get"])(state.preferences.features, [feature], false);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/store/actions.js
/**
 * Returns an action object used to toggle a feature flag.
 *
 * This function is unstable, as it is mostly copied from the edit-post
 * package. Editor features and preferences have a lot of scope for
 * being generalized and refactored.
 *
 * @param {string} feature Feature name.
 *
 * @return {Object} Action object.
 */
function __unstableToggleFeature(feature) {
  return {
    type: 'TOGGLE_FEATURE',
    feature
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/store/constants.js
/**
 * Module Constants
 */
const STORE_NAME = 'core/customize-widgets';

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





/**
 * Block editor data store configuration.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#registerStore
 *
 * @type {Object}
 */

const storeConfig = {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject,
  persist: ['preferences']
};
/**
 * Store definition for the edit widgets namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = Object(external_wp_data_["createReduxStore"])(STORE_NAME, storeConfig); // Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.

Object(external_wp_data_["registerStore"])(STORE_NAME, storeConfig);

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/more-menu/feature-toggle.js


/**
 * WordPress dependencies
 */





/**
 * Internal dependencies
 */


function FeatureToggle({
  label,
  info,
  messageActivated,
  messageDeactivated,
  shortcut,
  feature
}) {
  const isActive = Object(external_wp_data_["useSelect"])(select => select(store).__unstableIsFeatureActive(feature), [feature]);
  const {
    __unstableToggleFeature: toggleFeature
  } = Object(external_wp_data_["useDispatch"])(store);

  const speakMessage = () => {
    if (isActive) {
      Object(external_wp_a11y_["speak"])(messageDeactivated || Object(external_wp_i18n_["__"])('Feature deactivated'));
    } else {
      Object(external_wp_a11y_["speak"])(messageActivated || Object(external_wp_i18n_["__"])('Feature activated'));
    }
  };

  return Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
    icon: isActive && check["a" /* default */],
    isSelected: isActive,
    onClick: () => {
      toggleFeature(feature);
      speakMessage();
    },
    role: "menuitemcheckbox",
    info: info,
    shortcut: shortcut
  }, label);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/keyboard-shortcut-help-modal/config.js
/**
 * WordPress dependencies
 */

const textFormattingShortcuts = [{
  keyCombination: {
    modifier: 'primary',
    character: 'b'
  },
  description: Object(external_wp_i18n_["__"])('Make the selected text bold.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'i'
  },
  description: Object(external_wp_i18n_["__"])('Make the selected text italic.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'k'
  },
  description: Object(external_wp_i18n_["__"])('Convert the selected text into a link.')
}, {
  keyCombination: {
    modifier: 'primaryShift',
    character: 'k'
  },
  description: Object(external_wp_i18n_["__"])('Remove a link.')
}, {
  keyCombination: {
    modifier: 'primary',
    character: 'u'
  },
  description: Object(external_wp_i18n_["__"])('Underline the selected text.')
}];

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/keyboard-shortcut-help-modal/shortcut.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




function KeyCombination({
  keyCombination,
  forceAriaLabel
}) {
  const shortcut = keyCombination.modifier ? external_wp_keycodes_["displayShortcutList"][keyCombination.modifier](keyCombination.character) : keyCombination.character;
  const ariaLabel = keyCombination.modifier ? external_wp_keycodes_["shortcutAriaLabel"][keyCombination.modifier](keyCombination.character) : keyCombination.character;
  return Object(external_wp_element_["createElement"])("kbd", {
    className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-key-combination",
    "aria-label": forceAriaLabel || ariaLabel
  }, Object(external_lodash_["castArray"])(shortcut).map((character, index) => {
    if (character === '+') {
      return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], {
        key: index
      }, character);
    }

    return Object(external_wp_element_["createElement"])("kbd", {
      key: index,
      className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-key"
    }, character);
  }));
}

function Shortcut({
  description,
  keyCombination,
  aliases = [],
  ariaLabel
}) {
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("div", {
    className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-description"
  }, description), Object(external_wp_element_["createElement"])("div", {
    className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-term"
  }, Object(external_wp_element_["createElement"])(KeyCombination, {
    keyCombination: keyCombination,
    forceAriaLabel: ariaLabel
  }), aliases.map((alias, index) => Object(external_wp_element_["createElement"])(KeyCombination, {
    keyCombination: alias,
    forceAriaLabel: ariaLabel,
    key: index
  }))));
}

/* harmony default export */ var keyboard_shortcut_help_modal_shortcut = (Shortcut);

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/keyboard-shortcut-help-modal/dynamic-shortcut.js


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
  } = Object(external_wp_data_["useSelect"])(select => {
    const {
      getShortcutKeyCombination,
      getShortcutDescription,
      getShortcutAliases
    } = select(external_wp_keyboardShortcuts_["store"]);
    return {
      keyCombination: getShortcutKeyCombination(name),
      aliases: getShortcutAliases(name),
      description: getShortcutDescription(name)
    };
  });

  if (!keyCombination) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(keyboard_shortcut_help_modal_shortcut, {
    keyCombination: keyCombination,
    description: description,
    aliases: aliases
  });
}

/* harmony default export */ var dynamic_shortcut = (DynamicShortcut);

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/keyboard-shortcut-help-modal/index.js


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
/*
 * Disable reason: The `list` ARIA role is redundant but
 * Safari+VoiceOver won't announce the list otherwise.
 */

/* eslint-disable jsx-a11y/no-redundant-roles */
Object(external_wp_element_["createElement"])("ul", {
  className: "customize-widgets-keyboard-shortcut-help-modal__shortcut-list",
  role: "list"
}, shortcuts.map((shortcut, index) => Object(external_wp_element_["createElement"])("li", {
  className: "customize-widgets-keyboard-shortcut-help-modal__shortcut",
  key: index
}, Object(external_lodash_["isString"])(shortcut) ? Object(external_wp_element_["createElement"])(dynamic_shortcut, {
  name: shortcut
}) : Object(external_wp_element_["createElement"])(keyboard_shortcut_help_modal_shortcut, shortcut))))
/* eslint-enable jsx-a11y/no-redundant-roles */
;

const ShortcutSection = ({
  title,
  shortcuts,
  className
}) => Object(external_wp_element_["createElement"])("section", {
  className: classnames_default()('customize-widgets-keyboard-shortcut-help-modal__section', className)
}, !!title && Object(external_wp_element_["createElement"])("h2", {
  className: "customize-widgets-keyboard-shortcut-help-modal__section-title"
}, title), Object(external_wp_element_["createElement"])(ShortcutList, {
  shortcuts: shortcuts
}));

const ShortcutCategorySection = ({
  title,
  categoryName,
  additionalShortcuts = []
}) => {
  const categoryShortcuts = Object(external_wp_data_["useSelect"])(select => {
    return select(external_wp_keyboardShortcuts_["store"]).getCategoryShortcuts(categoryName);
  }, [categoryName]);
  return Object(external_wp_element_["createElement"])(ShortcutSection, {
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
  } = Object(external_wp_data_["useDispatch"])(external_wp_keyboardShortcuts_["store"]);
  registerShortcut({
    name: 'core/customize-widgets/keyboard-shortcuts',
    category: 'main',
    description: Object(external_wp_i18n_["__"])('Display these keyboard shortcuts.'),
    keyCombination: {
      modifier: 'access',
      character: 'h'
    }
  });
  Object(external_wp_keyboardShortcuts_["useShortcut"])('core/customize-widgets/keyboard-shortcuts', toggleModal, {
    bindGlobal: true
  });

  if (!isModalActive) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(external_wp_components_["Modal"], {
    className: "customize-widgets-keyboard-shortcut-help-modal",
    title: Object(external_wp_i18n_["__"])('Keyboard shortcuts'),
    closeLabel: Object(external_wp_i18n_["__"])('Close'),
    onRequestClose: toggleModal
  }, Object(external_wp_element_["createElement"])(ShortcutSection, {
    className: "customize-widgets-keyboard-shortcut-help-modal__main-shortcuts",
    shortcuts: ['core/customize-widgets/keyboard-shortcuts']
  }), Object(external_wp_element_["createElement"])(ShortcutCategorySection, {
    title: Object(external_wp_i18n_["__"])('Global shortcuts'),
    categoryName: "global"
  }), Object(external_wp_element_["createElement"])(ShortcutCategorySection, {
    title: Object(external_wp_i18n_["__"])('Selection shortcuts'),
    categoryName: "selection"
  }), Object(external_wp_element_["createElement"])(ShortcutCategorySection, {
    title: Object(external_wp_i18n_["__"])('Block shortcuts'),
    categoryName: "block",
    additionalShortcuts: [{
      keyCombination: {
        character: '/'
      },
      description: Object(external_wp_i18n_["__"])('Change the block type after adding a new paragraph.'),

      /* translators: The forward-slash character. e.g. '/'. */
      ariaLabel: Object(external_wp_i18n_["__"])('Forward-slash')
    }]
  }), Object(external_wp_element_["createElement"])(ShortcutSection, {
    title: Object(external_wp_i18n_["__"])('Text formatting'),
    shortcuts: textFormattingShortcuts
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/more-menu/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



const POPOVER_PROPS = {
  className: 'customize-widgets-more-menu__content',
  position: 'bottom left'
};
const TOGGLE_PROPS = {
  tooltipPosition: 'bottom'
};
function MoreMenu() {
  const [isKeyboardShortcutsModalActive, setIsKeyboardShortcutsModalVisible] = Object(external_wp_element_["useState"])(false);

  const toggleKeyboardShortcutsModal = () => setIsKeyboardShortcutsModalVisible(!isKeyboardShortcutsModalActive);

  Object(external_wp_keyboardShortcuts_["useShortcut"])('core/customize-widgets/keyboard-shortcuts', toggleKeyboardShortcutsModal, {
    bindGlobal: true
  });
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["ToolbarDropdownMenu"], {
    className: "customize-widgets-more-menu",
    icon: more_vertical["a" /* default */]
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    label: Object(external_wp_i18n_["__"])('Options'),
    popoverProps: POPOVER_PROPS,
    toggleProps: TOGGLE_PROPS
  }, () => Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_components_["MenuGroup"], {
    label: Object(external_wp_i18n_["_x"])('View', 'noun')
  }, Object(external_wp_element_["createElement"])(FeatureToggle, {
    feature: "fixedToolbar",
    label: Object(external_wp_i18n_["__"])('Top toolbar'),
    info: Object(external_wp_i18n_["__"])('Access all block and document tools in a single place'),
    messageActivated: Object(external_wp_i18n_["__"])('Top toolbar activated'),
    messageDeactivated: Object(external_wp_i18n_["__"])('Top toolbar deactivated')
  })), Object(external_wp_element_["createElement"])(external_wp_components_["MenuGroup"], {
    label: Object(external_wp_i18n_["__"])('Tools')
  }, Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
    onClick: () => {
      setIsKeyboardShortcutsModalVisible(true);
    },
    shortcut: external_wp_keycodes_["displayShortcut"].access('h')
  }, Object(external_wp_i18n_["__"])('Keyboard shortcuts')), Object(external_wp_element_["createElement"])(FeatureToggle, {
    feature: "welcomeGuide",
    label: Object(external_wp_i18n_["__"])('Welcome Guide')
  }), Object(external_wp_element_["createElement"])(external_wp_components_["MenuItem"], {
    role: "menuitem",
    icon: external["a" /* default */],
    href: Object(external_wp_i18n_["__"])('https://wordpress.org/support/article/wordpress-editor/'),
    target: "_blank",
    rel: "noopener noreferrer"
  }, Object(external_wp_i18n_["__"])('Help'), Object(external_wp_element_["createElement"])(external_wp_components_["VisuallyHidden"], {
    as: "span"
  },
  /* translators: accessibility text */
  Object(external_wp_i18n_["__"])('(opens in a new tab)')))), Object(external_wp_element_["createElement"])(external_wp_components_["MenuGroup"], {
    label: Object(external_wp_i18n_["__"])('Preferences')
  }, Object(external_wp_element_["createElement"])(FeatureToggle, {
    feature: "keepCaretInsideBlock",
    label: Object(external_wp_i18n_["__"])('Contain text cursor inside block'),
    info: Object(external_wp_i18n_["__"])('Aids screen readers by stopping text caret from leaving blocks.'),
    messageActivated: Object(external_wp_i18n_["__"])('Contain text cursor inside block activated'),
    messageDeactivated: Object(external_wp_i18n_["__"])('Contain text cursor inside block deactivated')
  })))), Object(external_wp_element_["createElement"])(KeyboardShortcutHelpModal, {
    isModalActive: isKeyboardShortcutsModalActive,
    toggleModal: toggleKeyboardShortcutsModal
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/header/index.js


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
  const [[hasUndo, hasRedo], setUndoRedo] = Object(external_wp_element_["useState"])([sidebar.hasUndo(), sidebar.hasRedo()]);
  Object(external_wp_element_["useEffect"])(() => {
    return sidebar.subscribeHistory(() => {
      setUndoRedo([sidebar.hasUndo(), sidebar.hasRedo()]);
    });
  }, [sidebar]);
  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("div", {
    className: classnames_default()('customize-widgets-header', {
      'is-fixed-toolbar-active': isFixedToolbarActive
    })
  }, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["NavigableToolbar"], {
    className: "customize-widgets-header-toolbar",
    "aria-label": Object(external_wp_i18n_["__"])('Document tools')
  }, Object(external_wp_element_["createElement"])(external_wp_components_["ToolbarButton"], {
    icon: !Object(external_wp_i18n_["isRTL"])() ? library_undo["a" /* default */] : library_redo["a" /* default */]
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    label: Object(external_wp_i18n_["__"])('Undo'),
    shortcut: external_wp_keycodes_["displayShortcut"].primary('z') // If there are no undo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/WordPress/gutenberg/issues/3486
    ,
    "aria-disabled": !hasUndo,
    onClick: sidebar.undo,
    className: "customize-widgets-editor-history-button undo-button"
  }), Object(external_wp_element_["createElement"])(external_wp_components_["ToolbarButton"], {
    icon: !Object(external_wp_i18n_["isRTL"])() ? library_redo["a" /* default */] : library_undo["a" /* default */]
    /* translators: button label text should, if possible, be under 16 characters. */
    ,
    label: Object(external_wp_i18n_["__"])('Redo'),
    shortcut: external_wp_keycodes_["displayShortcut"].primaryShift('z') // If there are no undo levels we don't want to actually disable this
    // button, because it will remove focus for keyboard users.
    // See: https://github.com/WordPress/gutenberg/issues/3486
    ,
    "aria-disabled": !hasRedo,
    onClick: sidebar.redo,
    className: "customize-widgets-editor-history-button redo-button"
  }), Object(external_wp_element_["createElement"])(external_wp_components_["ToolbarButton"], {
    className: "customize-widgets-header-toolbar__inserter-toggle",
    isPressed: isInserterOpened,
    isPrimary: true,
    icon: plus["a" /* default */],
    label: Object(external_wp_i18n_["_x"])('Add block', 'Generic label for block inserter button'),
    onClick: () => {
      setIsInserterOpened(isOpen => !isOpen);
    }
  }), Object(external_wp_element_["createElement"])(MoreMenu, null))), Object(external_wp_element_["createPortal"])(Object(external_wp_element_["createElement"])(components_inserter, {
    setIsOpened: setIsInserterOpened
  }), inserter.contentContainer[0]));
}

/* harmony default export */ var header = (Header);

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/inserter/use-inserter.js
/**
 * WordPress dependencies
 */

function useInserter(inserter) {
  const [isInserterOpened, setIsInserterOpened] = Object(external_wp_element_["useState"])(() => inserter.isOpen);
  Object(external_wp_element_["useEffect"])(() => {
    return inserter.subscribe(setIsInserterOpened);
  }, [inserter]);
  return [isInserterOpened, Object(external_wp_element_["useCallback"])(updater => {
    let isOpen = updater;

    if (typeof updater === 'function') {
      isOpen = updater(inserter.isOpen);
    }

    if (isOpen) {
      inserter.open();
    } else {
      inserter.close();
    }
  }, [inserter])];
}

// EXTERNAL MODULE: external ["wp","isShallowEqual"]
var external_wp_isShallowEqual_ = __webpack_require__("rl8x");
var external_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_wp_isShallowEqual_);

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/utils.js
// @ts-check

/**
 * WordPress dependencies
 */


/**
 * External dependencies
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
      } = block.attributes.instance; // Widget that extends WP_Widget.

      widget = {
        idBase: block.attributes.idBase,
        instance: { ...(existingWidget === null || existingWidget === void 0 ? void 0 : existingWidget.instance),
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
      content: Object(external_wp_blocks_["serialize"])(block)
    };
    widget = {
      idBase: 'block',
      widgetClass: 'WP_Widget_Block',
      instance: {
        raw_instance: instance
      }
    };
  }

  return { ...Object(external_lodash_["omit"])(existingWidget, ['form', 'rendered']),
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
    const parsedBlocks = Object(external_wp_blocks_["parse"])(raw.content);
    block = parsedBlocks.length ? parsedBlocks[0] : Object(external_wp_blocks_["createBlock"])('core/paragraph', {});
  } else if (number) {
    // Widget that extends WP_Widget.
    block = Object(external_wp_blocks_["createBlock"])('core/legacy-widget', {
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
    block = Object(external_wp_blocks_["createBlock"])('core/legacy-widget', {
      id
    });
  }

  return Object(external_wp_widgets_["addWidgetIdToBlock"])(block, id);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/sidebar-block-editor/use-sidebar-block-editor.js
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
  const [blocks, setBlocks] = Object(external_wp_element_["useState"])(() => widgetsToBlocks(sidebar.getWidgets()));
  Object(external_wp_element_["useEffect"])(() => {
    return sidebar.subscribe((prevWidgets, nextWidgets) => {
      setBlocks(prevBlocks => {
        const prevWidgetsMap = new Map(prevWidgets.map(widget => [widget.id, widget]));
        const prevBlocksMap = new Map(prevBlocks.map(block => [Object(external_wp_widgets_["getWidgetIdFromBlock"])(block), block]));
        const nextBlocks = nextWidgets.map(nextWidget => {
          const prevWidget = prevWidgetsMap.get(nextWidget.id); // Bail out updates.

          if (prevWidget && prevWidget === nextWidget) {
            return prevBlocksMap.get(nextWidget.id);
          }

          return widgetToBlock(nextWidget);
        }); // Bail out updates.

        if (external_wp_isShallowEqual_default()(prevBlocks, nextBlocks)) {
          return prevBlocks;
        }

        return nextBlocks;
      });
    });
  }, [sidebar]);
  const onChangeBlocks = Object(external_wp_element_["useCallback"])(nextBlocks => {
    setBlocks(prevBlocks => {
      if (external_wp_isShallowEqual_default()(prevBlocks, nextBlocks)) {
        return prevBlocks;
      }

      const prevBlocksMap = new Map(prevBlocks.map(block => [Object(external_wp_widgets_["getWidgetIdFromBlock"])(block), block]));
      const nextWidgets = nextBlocks.map(nextBlock => {
        const widgetId = Object(external_wp_widgets_["getWidgetIdFromBlock"])(nextBlock); // Update existing widgets.

        if (widgetId && prevBlocksMap.has(widgetId)) {
          const prevBlock = prevBlocksMap.get(widgetId);
          const prevWidget = sidebar.getWidget(widgetId); // Bail out updates by returning the previous widgets.
          // Deep equality is necessary until the block editor's internals changes.

          if (Object(external_lodash_["isEqual"])(nextBlock, prevBlock) && prevWidget) {
            return prevWidget;
          }

          return blockToWidget(nextBlock, prevWidget);
        } // Add a new widget.


        return blockToWidget(nextBlock);
      }); // Bail out updates if the updated widgets are the same.

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

          updatedNextBlocks[index] = Object(external_wp_widgets_["addWidgetIdToBlock"])(nextBlock, addedWidgetId);
        }

        return updatedNextBlocks;
      }, nextBlocks);
    });
  }, [sidebar]);
  return [blocks, onChangeBlocks, onChangeBlocks];
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/focus-control/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


const FocusControlContext = Object(external_wp_element_["createContext"])();
function FocusControl({
  api,
  sidebarControls,
  children
}) {
  const [focusedWidgetIdRef, setFocusedWidgetIdRef] = Object(external_wp_element_["useState"])({
    current: null
  });
  const focusWidget = Object(external_wp_element_["useCallback"])(widgetId => {
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
  Object(external_wp_element_["useEffect"])(() => {
    function handleFocus(settingId) {
      const widgetId = settingIdToWidgetId(settingId);
      focusWidget(widgetId);
    }

    function handleReady() {
      api.previewer.preview.bind('focus-control-for-setting', handleFocus);
    }

    api.previewer.bind('ready', handleReady);
    return () => {
      api.previewer.unbind('ready', handleReady);
      api.previewer.preview.unbind('focus-control-for-setting', handleFocus);
    };
  }, [api, focusWidget]);
  const context = Object(external_wp_element_["useMemo"])(() => [focusedWidgetIdRef, focusWidget], [focusedWidgetIdRef, focusWidget]);
  return Object(external_wp_element_["createElement"])(FocusControlContext.Provider, {
    value: context
  }, children);
}
const useFocusControl = () => Object(external_wp_element_["useContext"])(FocusControlContext);

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/focus-control/use-blocks-focus-control.js
/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */


function useBlocksFocusControl(blocks) {
  const {
    selectBlock
  } = Object(external_wp_data_["useDispatch"])(external_wp_blockEditor_["store"]);
  const [focusedWidgetIdRef] = useFocusControl();
  const blocksRef = Object(external_wp_element_["useRef"])(blocks);
  Object(external_wp_element_["useEffect"])(() => {
    blocksRef.current = blocks;
  }, [blocks]);
  Object(external_wp_element_["useEffect"])(() => {
    if (focusedWidgetIdRef.current) {
      const focusedBlock = blocksRef.current.find(block => Object(external_wp_widgets_["getWidgetIdFromBlock"])(block) === focusedWidgetIdRef.current);

      if (focusedBlock) {
        selectBlock(focusedBlock.clientId); // If the block is already being selected, the DOM node won't
        // get focused again automatically.
        // We select the DOM and focus it manually here.

        const blockNode = document.querySelector(`[data-block="${focusedBlock.clientId}"]`);
        blockNode === null || blockNode === void 0 ? void 0 : blockNode.focus();
      }
    }
  }, [focusedWidgetIdRef, selectBlock]);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/sidebar-block-editor/sidebar-editor-provider.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



function SidebarEditorProvider({
  sidebar,
  settings,
  children
}) {
  const [blocks, onInput, onChange] = useSidebarBlockEditor(sidebar);
  useBlocksFocusControl(blocks);
  return Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockEditorProvider"], {
    value: blocks,
    onInput: onInput,
    onChange: onChange,
    settings: settings,
    useSubRegistry: false
  }, children);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/welcome-guide/index.js


/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


function WelcomeGuide({
  sidebar
}) {
  const {
    __unstableToggleFeature: toggleFeature
  } = Object(external_wp_data_["useDispatch"])(store);
  const isEntirelyBlockWidgets = sidebar.getWidgets().every(widget => widget.id.startsWith('block-'));
  return Object(external_wp_element_["createElement"])("div", {
    className: "customize-widgets-welcome-guide"
  }, Object(external_wp_element_["createElement"])("div", {
    className: "customize-widgets-welcome-guide__image__wrapper"
  }, Object(external_wp_element_["createElement"])("picture", null, Object(external_wp_element_["createElement"])("source", {
    srcSet: "https://s.w.org/images/block-editor/welcome-editor.svg",
    media: "(prefers-reduced-motion: reduce)"
  }), Object(external_wp_element_["createElement"])("img", {
    className: "customize-widgets-welcome-guide__image",
    src: "https://s.w.org/images/block-editor/welcome-editor.gif",
    width: "312",
    height: "240",
    alt: ""
  }))), Object(external_wp_element_["createElement"])("h1", {
    className: "customize-widgets-welcome-guide__heading"
  }, Object(external_wp_i18n_["__"])('Welcome to block Widgets')), Object(external_wp_element_["createElement"])("p", {
    className: "customize-widgets-welcome-guide__text"
  }, isEntirelyBlockWidgets ? Object(external_wp_i18n_["__"])('Your theme provides different “block” areas for you to add and edit content. Try adding a search bar, social icons, or other types of blocks here and see how they’ll look on your site.') : Object(external_wp_i18n_["__"])('You can now add any block to your site’s widget areas. Don’t worry, all of your favorite widgets still work flawlessly.')), Object(external_wp_element_["createElement"])(external_wp_components_["Button"], {
    className: "customize-widgets-welcome-guide__button",
    isPrimary: true,
    onClick: () => toggleFeature('welcomeGuide')
  }, Object(external_wp_i18n_["__"])('Got it')), Object(external_wp_element_["createElement"])("hr", {
    className: "customize-widgets-welcome-guide__separator"
  }), !isEntirelyBlockWidgets && Object(external_wp_element_["createElement"])("p", {
    className: "customize-widgets-welcome-guide__more-info"
  }, Object(external_wp_i18n_["__"])('Want to stick with the old widgets?'), Object(external_wp_element_["createElement"])("br", null), Object(external_wp_element_["createElement"])(external_wp_components_["ExternalLink"], {
    href: Object(external_wp_i18n_["__"])('https://wordpress.org/plugins/classic-widgets/')
  }, Object(external_wp_i18n_["__"])('Get the Classic Widgets plugin.'))), Object(external_wp_element_["createElement"])("p", {
    className: "customize-widgets-welcome-guide__more-info"
  }, Object(external_wp_i18n_["__"])('New to the block editor?'), Object(external_wp_element_["createElement"])("br", null), Object(external_wp_element_["createElement"])(external_wp_components_["ExternalLink"], {
    href: Object(external_wp_i18n_["__"])('https://wordpress.org/support/article/wordpress-editor/')
  }, Object(external_wp_i18n_["__"])("Here's a detailed guide."))));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/keyboard-shortcuts/index.js
/**
 * WordPress dependencies
 */





function KeyboardShortcuts({
  undo,
  redo,
  save
}) {
  Object(external_wp_keyboardShortcuts_["useShortcut"])('core/customize-widgets/undo', event => {
    undo();
    event.preventDefault();
  }, {
    bindGlobal: true
  });
  Object(external_wp_keyboardShortcuts_["useShortcut"])('core/customize-widgets/redo', event => {
    redo();
    event.preventDefault();
  }, {
    bindGlobal: true
  });
  Object(external_wp_keyboardShortcuts_["useShortcut"])('core/customize-widgets/save', event => {
    event.preventDefault();
    save();
  }, {
    bindGlobal: true
  });
  return null;
}

function KeyboardShortcutsRegister() {
  const {
    registerShortcut,
    unregisterShortcut
  } = Object(external_wp_data_["useDispatch"])(external_wp_keyboardShortcuts_["store"]);
  Object(external_wp_element_["useEffect"])(() => {
    registerShortcut({
      name: 'core/customize-widgets/undo',
      category: 'global',
      description: Object(external_wp_i18n_["__"])('Undo your last changes.'),
      keyCombination: {
        modifier: 'primary',
        character: 'z'
      }
    });
    registerShortcut({
      name: 'core/customize-widgets/redo',
      category: 'global',
      description: Object(external_wp_i18n_["__"])('Redo your last undo.'),
      keyCombination: {
        modifier: 'primaryShift',
        character: 'z'
      }
    });
    registerShortcut({
      name: 'core/customize-widgets/save',
      category: 'global',
      description: Object(external_wp_i18n_["__"])('Save your changes.'),
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
/* harmony default export */ var keyboard_shortcuts = (KeyboardShortcuts);

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/block-appender/index.js



/**
 * WordPress dependencies
 */



function BlockAppender(props) {
  const ref = Object(external_wp_element_["useRef"])();
  const isBlocksListEmpty = Object(external_wp_data_["useSelect"])(select => select(external_wp_blockEditor_["store"]).getBlockCount() === 0); // Move the focus to the block appender to prevent focus from
  // being lost when emptying the widget area.

  Object(external_wp_element_["useEffect"])(() => {
    if (isBlocksListEmpty && ref.current) {
      const {
        ownerDocument
      } = ref.current;

      if (!ownerDocument.activeElement || ownerDocument.activeElement === ownerDocument.body) {
        ref.current.focus();
      }
    }
  }, [isBlocksListEmpty]);
  return Object(external_wp_element_["createElement"])(external_wp_blockEditor_["ButtonBlockAppender"], Object(esm_extends["a" /* default */])({}, props, {
    ref: ref
  }));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/sidebar-block-editor/index.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */









function SidebarBlockEditor({
  blockEditorSettings,
  sidebar,
  inserter,
  inspector
}) {
  const [isInserterOpened, setIsInserterOpened] = useInserter(inserter);
  const {
    hasUploadPermissions,
    isFixedToolbarActive,
    keepCaretInsideBlock,
    isWelcomeGuideActive
  } = Object(external_wp_data_["useSelect"])(select => {
    return {
      hasUploadPermissions: Object(external_lodash_["defaultTo"])(select(external_wp_coreData_["store"]).canUser('create', 'media'), true),
      isFixedToolbarActive: select(store).__unstableIsFeatureActive('fixedToolbar'),
      keepCaretInsideBlock: select(store).__unstableIsFeatureActive('keepCaretInsideBlock'),
      isWelcomeGuideActive: select(store).__unstableIsFeatureActive('welcomeGuide')
    };
  }, []);
  const settings = Object(external_wp_element_["useMemo"])(() => {
    let mediaUploadBlockEditor;

    if (hasUploadPermissions) {
      mediaUploadBlockEditor = ({
        onError,
        ...argumentsObject
      }) => {
        Object(external_wp_mediaUtils_["uploadMedia"])({
          wpAllowedMimeTypes: blockEditorSettings.allowedMimeTypes,
          onError: ({
            message
          }) => onError(message),
          ...argumentsObject
        });
      };
    }

    return { ...blockEditorSettings,
      __experimentalSetIsInserterOpened: setIsInserterOpened,
      mediaUpload: mediaUploadBlockEditor,
      hasFixedToolbar: isFixedToolbarActive,
      keepCaretInsideBlock,
      __unstableHasCustomAppender: true
    };
  }, [hasUploadPermissions, blockEditorSettings, isFixedToolbarActive, keepCaretInsideBlock]);

  if (isWelcomeGuideActive) {
    return Object(external_wp_element_["createElement"])(WelcomeGuide, {
      sidebar: sidebar
    });
  }

  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockEditorKeyboardShortcuts"].Register, null), Object(external_wp_element_["createElement"])(keyboard_shortcuts.Register, null), Object(external_wp_element_["createElement"])(SidebarEditorProvider, {
    sidebar: sidebar,
    settings: settings
  }, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockEditorKeyboardShortcuts"], null), Object(external_wp_element_["createElement"])(keyboard_shortcuts, {
    undo: sidebar.undo,
    redo: sidebar.redo,
    save: sidebar.save
  }), Object(external_wp_element_["createElement"])(header, {
    sidebar: sidebar,
    inserter: inserter,
    isInserterOpened: isInserterOpened,
    setIsInserterOpened: setIsInserterOpened,
    isFixedToolbarActive: isFixedToolbarActive
  }), Object(external_wp_element_["createElement"])(external_wp_blockEditor_["CopyHandler"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockTools"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockSelectionClearer"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["WritingFlow"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["ObserveTyping"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockList"], {
    renderAppender: BlockAppender
  })))))), Object(external_wp_element_["createPortal"])( // This is a temporary hack to prevent button component inside <BlockInspector>
  // from submitting form when type="button" is not specified.
  Object(external_wp_element_["createElement"])("form", {
    onSubmit: event => event.preventDefault()
  }, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockInspector"], null)), inspector.contentContainer[0])), Object(external_wp_element_["createElement"])(external_wp_blockEditor_["__unstableBlockSettingsMenuFirstItem"], null, ({
    onClose
  }) => Object(external_wp_element_["createElement"])(block_inspector_button, {
    inspector: inspector,
    closeMenu: onClose
  })));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/sidebar-controls/index.js


/**
 * WordPress dependencies
 */

const SidebarControlsContext = Object(external_wp_element_["createContext"])();
function SidebarControls({
  sidebarControls,
  activeSidebarControl,
  children
}) {
  const context = Object(external_wp_element_["useMemo"])(() => ({
    sidebarControls,
    activeSidebarControl
  }), [sidebarControls, activeSidebarControl]);
  return Object(external_wp_element_["createElement"])(SidebarControlsContext.Provider, {
    value: context
  }, children);
}
function useSidebarControls() {
  const {
    sidebarControls
  } = Object(external_wp_element_["useContext"])(SidebarControlsContext);
  return sidebarControls;
}
function useActiveSidebarControl() {
  const {
    activeSidebarControl
  } = Object(external_wp_element_["useContext"])(SidebarControlsContext);
  return activeSidebarControl;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/customize-widgets/use-clear-selected-block.js
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
 * @param {Object} popoverRef The ref object of the popover node container.
 */

function useClearSelectedBlock(sidebarControl, popoverRef) {
  const {
    hasSelectedBlock,
    hasMultiSelection
  } = Object(external_wp_data_["useSelect"])(external_wp_blockEditor_["store"]);
  const {
    clearSelectedBlock
  } = Object(external_wp_data_["useDispatch"])(external_wp_blockEditor_["store"]);
  Object(external_wp_element_["useEffect"])(() => {
    if (popoverRef.current && sidebarControl) {
      const inspector = sidebarControl.inspector;
      const container = sidebarControl.container[0];
      const ownerDocument = container.ownerDocument;
      const ownerWindow = ownerDocument.defaultView;

      function handleClearSelectedBlock(element) {
        if ( // 1. Make sure there are blocks being selected.
        (hasSelectedBlock() || hasMultiSelection()) && // 2. The element should exist in the DOM (not deleted).
        element && ownerDocument.contains(element) && // 3. It should also not exist in the container, the popover, nor the dialog.
        !container.contains(element) && !popoverRef.current.contains(element) && !element.closest('[role="dialog"]') && // 4. The inspector should not be opened.
        !inspector.expanded()) {
          clearSelectedBlock();
        }
      } // Handle mouse down in the same document.


      function handleMouseDown(event) {
        handleClearSelectedBlock(event.target);
      } // Handle focusing outside the current document, like to iframes.


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

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/customize-widgets/index.js


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
  const [activeSidebarControl, setActiveSidebarControl] = Object(external_wp_element_["useState"])(null);
  const parentContainer = document.getElementById('customize-theme-controls');
  const popoverRef = Object(external_wp_element_["useRef"])();
  useClearSelectedBlock(activeSidebarControl, popoverRef);
  Object(external_wp_element_["useEffect"])(() => {
    const unsubscribers = sidebarControls.map(sidebarControl => sidebarControl.subscribe(expanded => {
      if (expanded) {
        setActiveSidebarControl(sidebarControl);
      }
    }));
    return () => {
      unsubscribers.forEach(unsubscriber => unsubscriber());
    };
  }, [sidebarControls]);
  const activeSidebar = activeSidebarControl && Object(external_wp_element_["createPortal"])(Object(external_wp_element_["createElement"])(SidebarBlockEditor, {
    key: activeSidebarControl.id,
    blockEditorSettings: blockEditorSettings,
    sidebar: activeSidebarControl.sidebarAdapter,
    inserter: activeSidebarControl.inserter,
    inspector: activeSidebarControl.inspector
  }), activeSidebarControl.container[0]); // We have to portal this to the parent of both the editor and the inspector,
  // so that the popovers will appear above both of them.

  const popover = parentContainer && Object(external_wp_element_["createPortal"])(Object(external_wp_element_["createElement"])("div", {
    className: "customize-widgets-popover",
    ref: popoverRef
  }, Object(external_wp_element_["createElement"])(external_wp_components_["Popover"].Slot, null)), parentContainer);
  return Object(external_wp_element_["createElement"])(external_wp_components_["SlotFillProvider"], null, Object(external_wp_element_["createElement"])(SidebarControls, {
    sidebarControls: sidebarControls,
    activeSidebarControl: activeSidebarControl
  }, Object(external_wp_element_["createElement"])(FocusControl, {
    api: api,
    sidebarControls: sidebarControls
  }, activeSidebar, popover)));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/controls/inspector-section.js
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
    }

    ready() {
      this.contentContainer[0].classList.add('customize-widgets-layout__inspector');
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

  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/controls/sidebar-section.js
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
  return class SidebarSection extends customize.Section {
    ready() {
      const InspectorSection = getInspectorSection();
      this.inspector = new InspectorSection(getInspectorSectionId(this.id), {
        title: Object(external_wp_i18n_["__"])('Block Settings'),
        parentSection: this,
        customizeAction: [Object(external_wp_i18n_["__"])('Customizing'), Object(external_wp_i18n_["__"])('Widgets'), this.params.title].join(' ▸ ')
      });
      customize.section.add(this.inspector);
      this.contentContainer[0].classList.add('customize-widgets__sidebar-section');
    }

    hasSubSectionOpened() {
      return this.inspector.expanded();
    }

    onChangeExpanded(expanded, _args) {
      const controls = this.controls();
      const args = { ..._args,

        completeCallback() {
          var _args$completeCallbac;

          controls.forEach(control => {
            control.onChangeSectionExpanded(expanded, args);
          });
          (_args$completeCallbac = _args.completeCallback) === null || _args$completeCallbac === void 0 ? void 0 : _args$completeCallbac.call(_args);
        }

      };

      if (args.manualTransition) {
        if (expanded) {
          this.contentContainer.addClass(['busy', 'open']);
          this.contentContainer.removeClass('is-sub-section-open');
          this.contentContainer.closest('.wp-full-overlay').addClass('section-open');
          this.contentContainer.one('transitionend', () => {
            this.contentContainer.removeClass('busy');
            args.completeCallback();
          });
        } else {
          this.contentContainer.addClass(['busy', 'is-sub-section-open']);
          this.contentContainer.closest('.wp-full-overlay').addClass('section-open');
          this.contentContainer.removeClass('open');
          this.contentContainer.one('transitionend', () => {
            this.contentContainer.removeClass('busy');
            args.completeCallback();
          });
        }
      } else {
        super.onChangeExpanded(expanded, args);
      }
    }

  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/sidebar-block-editor/sidebar-adapter.js
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
  } // Likely an old single widget.


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

class sidebar_adapter_SidebarAdapter {
  constructor(setting, api) {
    this.setting = setting;
    this.api = api;
    this.locked = false;
    this.widgetsCache = new WeakMap();
    this.subscribers = new Set();
    this.history = [this._getWidgetIds().map(widgetId => this.getWidget(widgetId))];
    this.historyIndex = 0;
    this.historySubscribers = new Set(); // Debounce the input for 1 second.

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
    const prevWidget = this.getWidget(widget.id); // Bail out update if nothing changed.

    if (prevWidget === widget) {
      return widget.id;
    } // Update existing setting if only the widget's instance changed.


    if (prevWidget.idBase && widget.idBase && prevWidget.idBase === widget.idBase) {
      const settingId = widgetIdToSettingId(widget.id);
      this.api(settingId).set(widget.instance);
      return widget.id;
    } // Otherwise delete and re-create.


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

// EXTERNAL MODULE: external ["wp","dom"]
var external_wp_dom_ = __webpack_require__("1CF3");

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/controls/inserter-outer-section.js
/**
 * WordPress dependencies
 */


function getInserterOuterSection() {
  const {
    wp: {
      customize
    }
  } = window;
  const OuterSection = customize.OuterSection; // Override the OuterSection class to handle multiple outer sections.
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

  }; // Handle constructor so that "params.type" can be correctly pointed to "outer".

  customize.sectionConstructor.outer = customize.OuterSection;
  return class InserterOuterSection extends customize.OuterSection {
    constructor(...args) {
      super(...args); // This is necessary since we're creating a new class which is not identical to the original OuterSection.
      // @See https://github.com/WordPress/wordpress-develop/blob/42b05c397c50d9dc244083eff52991413909d4bd/src/js/_enqueues/wp/customize/controls.js#L1427-L1436

      this.params.type = 'outer';
      this.activeElementBeforeExpanded = null;
      const ownerWindow = this.contentContainer[0].ownerDocument.defaultView; // Handle closing the inserter when pressing the Escape key.

      ownerWindow.addEventListener('keydown', event => {
        if (this.isOpen && (event.keyCode === external_wp_keycodes_["ESCAPE"] || event.code === 'Escape')) {
          event.stopPropagation();
          this.close();
        }
      }, // Use capture mode to make this run before other event listeners.
      true);
      this.contentContainer.addClass('widgets-inserter');
    }

    get isOpen() {
      return this.expanded();
    }

    subscribe(handler) {
      this.expanded.bind(handler);
      return () => this.expanded.unbind(handler);
    }

    open() {
      if (!this.isOpen) {
        const contentContainer = this.contentContainer[0];
        this.activeElementBeforeExpanded = contentContainer.ownerDocument.activeElement;
        this.expand({
          completeCallback() {
            // We have to do this in a "completeCallback" or else the elements will not yet be visible/tabbable.
            // The first one should be the close button,
            // we want to skip it and choose the second one instead, which is the search box.
            const searchBox = external_wp_dom_["focus"].tabbable.find(contentContainer)[1];

            if (searchBox) {
              searchBox.focus();
            }
          }

        });
      }
    }

    close() {
      if (this.isOpen) {
        const contentContainer = this.contentContainer[0];
        const activeElement = contentContainer.ownerDocument.activeElement;
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

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/controls/sidebar-control.js
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
      this.sidebarAdapter = new sidebar_adapter_SidebarAdapter(this.setting, customize);
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
          this.inserter.close();
        }

        this.subscribers.forEach(subscriber => subscriber(expanded, args));
      }
    }

  };
}

// EXTERNAL MODULE: external ["wp","hooks"]
var external_wp_hooks_ = __webpack_require__("g56x");

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/filters/move-to-sidebar.js


/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */




const withMoveToSidebarToolbarItem = Object(external_wp_compose_["createHigherOrderComponent"])(BlockEdit => props => {
  let widgetId = Object(external_wp_widgets_["getWidgetIdFromBlock"])(props);
  const sidebarControls = useSidebarControls();
  const activeSidebarControl = useActiveSidebarControl();
  const hasMultipleSidebars = (sidebarControls === null || sidebarControls === void 0 ? void 0 : sidebarControls.length) > 1;
  const blockName = props.name;
  const clientId = props.clientId;
  const canInsertBlockInSidebar = Object(external_wp_data_["useSelect"])(select => {
    // Use an empty string to represent the root block list, which
    // in the customizer editor represents a sidebar/widget area.
    return select(external_wp_blockEditor_["store"]).canInsertBlockType(blockName, '');
  }, [blockName]);
  const block = Object(external_wp_data_["useSelect"])(select => select(external_wp_blockEditor_["store"]).getBlock(clientId), [clientId]);
  const {
    removeBlock
  } = Object(external_wp_data_["useDispatch"])(external_wp_blockEditor_["store"]);
  const [, focusWidget] = useFocusControl();

  function moveToSidebar(sidebarControlId) {
    const newSidebarControl = sidebarControls.find(sidebarControl => sidebarControl.id === sidebarControlId);

    if (widgetId) {
      /**
       * If there's a widgetId, move it to the other sidebar.
       */
      const oldSetting = activeSidebarControl.setting;
      const newSetting = newSidebarControl.setting;
      oldSetting(Object(external_lodash_["without"])(oldSetting(), widgetId));
      newSetting([...newSetting(), widgetId]);
    } else {
      /**
       * If there isn't a widgetId, it's most likely a inner block.
       * First, remove the block in the original sidebar,
       * then, create a new widget in the new sidebar and get back its widgetId.
       */
      const sidebarAdapter = newSidebarControl.sidebarAdapter;
      removeBlock(clientId);
      const addedWidgetIds = sidebarAdapter.setWidgets([...sidebarAdapter.getWidgets(), blockToWidget(block)]); // The last non-null id is the added widget's id.

      widgetId = addedWidgetIds.reverse().find(id => !!id);
    } // Move focus to the moved widget and expand the sidebar.


    focusWidget(widgetId);
  }

  return Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])(BlockEdit, props), hasMultipleSidebars && canInsertBlockInSidebar && Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockControls"], null, Object(external_wp_element_["createElement"])(external_wp_widgets_["MoveToWidgetArea"], {
    widgetAreas: sidebarControls.map(sidebarControl => ({
      id: sidebarControl.id,
      name: sidebarControl.params.label,
      description: sidebarControl.params.description
    })),
    currentWidgetAreaId: activeSidebarControl === null || activeSidebarControl === void 0 ? void 0 : activeSidebarControl.id,
    onSelect: moveToSidebar
  })));
}, 'withMoveToSidebarToolbarItem');
Object(external_wp_hooks_["addFilter"])('editor.BlockEdit', 'core/customize-widgets/block-edit', withMoveToSidebarToolbarItem);

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/filters/replace-media-upload.js
/**
 * WordPress dependencies
 */



const replaceMediaUpload = () => external_wp_mediaUtils_["MediaUpload"];

Object(external_wp_hooks_["addFilter"])('editor.MediaUpload', 'core/edit-widgets/replace-media-upload', replaceMediaUpload);

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/filters/wide-widget-display.js



/**
 * WordPress dependencies
 */


const {
  wp: wide_widget_display_wp
} = window;
const withWideWidgetDisplay = Object(external_wp_compose_["createHigherOrderComponent"])(BlockEdit => props => {
  var _wp$customize$Widgets, _wp$customize$Widgets2;

  const {
    idBase
  } = props.attributes;
  const isWide = (_wp$customize$Widgets = (_wp$customize$Widgets2 = wide_widget_display_wp.customize.Widgets.data.availableWidgets.find(widget => widget.id_base === idBase)) === null || _wp$customize$Widgets2 === void 0 ? void 0 : _wp$customize$Widgets2.is_wide) !== null && _wp$customize$Widgets !== void 0 ? _wp$customize$Widgets : false;
  return Object(external_wp_element_["createElement"])(BlockEdit, Object(esm_extends["a" /* default */])({}, props, {
    isWide: isWide
  }));
}, 'withWideWidgetDisplay');
Object(external_wp_hooks_["addFilter"])('editor.BlockEdit', 'core/customize-widgets/wide-widget-display', withWideWidgetDisplay);

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/filters/index.js
/**
 * Internal dependencies
 */




// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/index.js


/**
 * WordPress dependencies
 */




/**
 * Internal dependencies
 */





const {
  wp: build_module_wp
} = window;
const DISABLED_BLOCKS = ['core/more', 'core/block', 'core/freeform'];
const ENABLE_EXPERIMENTAL_FSE_BLOCKS = false;
/**
 * Initializes the widgets block editor in the customizer.
 *
 * @param {string} editorName          The editor name.
 * @param {Object} blockEditorSettings Block editor settings.
 */

function initialize(editorName, blockEditorSettings) {
  const coreBlocks = Object(external_wp_blockLibrary_["__experimentalGetCoreBlocks"])().filter(block => {
    return !(DISABLED_BLOCKS.includes(block.name) || block.name.startsWith('core/post') || block.name.startsWith('core/query') || block.name.startsWith('core/site'));
  });

  Object(external_wp_blockLibrary_["registerCoreBlocks"])(coreBlocks);
  Object(external_wp_widgets_["registerLegacyWidgetBlock"])();

  if (false) {}

  Object(external_wp_widgets_["registerLegacyWidgetVariations"])(blockEditorSettings); // As we are unregistering `core/freeform` to avoid the Classic block, we must
  // replace it with something as the default freeform content handler. Failure to
  // do this will result in errors in the default block parser.
  // see: https://github.com/WordPress/gutenberg/issues/33097

  Object(external_wp_blocks_["setFreeformContentHandlerName"])('core/html');
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
    Object(external_wp_element_["render"])(Object(external_wp_element_["createElement"])(CustomizeWidgets, {
      api: build_module_wp.customize,
      sidebarControls: sidebarControls,
      blockEditorSettings: blockEditorSettings
    }), container);
  });
}


/***/ }),

/***/ "GLVC":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["widgets"]; }());

/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "HSyU":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blocks"]; }());

/***/ }),

/***/ "K+tz":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const external = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"
}));
/* harmony default export */ __webpack_exports__["a"] = (external);


/***/ }),

/***/ "K2cm":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const redo = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M15.6 6.5l-1.1 1 2.9 3.3H8c-.9 0-1.7.3-2.3.9-1.4 1.5-1.4 4.2-1.4 5.6v.2h1.5v-.3c0-1.1 0-3.5 1-4.5.3-.3.7-.5 1.3-.5h9.2L14.5 15l1.1 1.1 4.6-4.6-4.6-5z"
}));
/* harmony default export */ __webpack_exports__["a"] = (redo);


/***/ }),

/***/ "K9lf":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["compose"]; }());

/***/ }),

/***/ "Ntru":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const undo = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.3 11.7c-.6-.6-1.4-.9-2.3-.9H6.7l2.9-3.3-1.1-1-4.5 5L8.5 16l1-1-2.7-2.7H16c.5 0 .9.2 1.3.5 1 1 1 3.4 1 4.5v.3h1.5v-.2c0-1.5 0-4.3-1.5-5.7z"
}));
/* harmony default export */ __webpack_exports__["a"] = (undo);


/***/ }),

/***/ "Q4Sy":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const plus = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18 11.2h-5.2V6h-1.6v5.2H6v1.6h5.2V18h1.6v-5.2H18z"
}));
/* harmony default export */ __webpack_exports__["a"] = (plus);


/***/ }),

/***/ "QyPg":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blockLibrary"]; }());

/***/ }),

/***/ "RMJe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const check = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M18.3 5.6L9.9 16.9l-4.6-3.4-.9 1.2 5.8 4.3 9.3-12.6z"
}));
/* harmony default export */ __webpack_exports__["a"] = (check);


/***/ }),

/***/ "RxS6":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["keycodes"]; }());

/***/ }),

/***/ "TSYQ":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_ARRAY__, __WEBPACK_AMD_DEFINE_RESULT__;/*!
  Copyright (c) 2018 Jed Watson.
  Licensed under the MIT License (MIT), see
  http://jedwatson.github.io/classnames
*/
/* global define */

(function () {
	'use strict';

	var hasOwn = {}.hasOwnProperty;

	function classNames() {
		var classes = [];

		for (var i = 0; i < arguments.length; i++) {
			var arg = arguments[i];
			if (!arg) continue;

			var argType = typeof arg;

			if (argType === 'string' || argType === 'number') {
				classes.push(arg);
			} else if (Array.isArray(arg)) {
				if (arg.length) {
					var inner = classNames.apply(null, arg);
					if (inner) {
						classes.push(inner);
					}
				}
			} else if (argType === 'object') {
				if (arg.toString === Object.prototype.toString) {
					for (var key in arg) {
						if (hasOwn.call(arg, key) && arg[key]) {
							classes.push(key);
						}
					}
				} else {
					classes.push(arg.toString());
				}
			}
		}

		return classes.join(' ');
	}

	if ( true && module.exports) {
		classNames.default = classNames;
		module.exports = classNames;
	} else if (true) {
		// register as 'classnames', consistent with npm package name
		!(__WEBPACK_AMD_DEFINE_ARRAY__ = [], __WEBPACK_AMD_DEFINE_RESULT__ = (function () {
			return classNames;
		}).apply(exports, __WEBPACK_AMD_DEFINE_ARRAY__),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
	} else {}
}());


/***/ }),

/***/ "Tqx9":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["primitives"]; }());

/***/ }),

/***/ "VKE3":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const moreVertical = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z"
}));
/* harmony default export */ __webpack_exports__["a"] = (moreVertical);


/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "axFQ":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["blockEditor"]; }());

/***/ }),

/***/ "bWcr":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("GRId");
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("Tqx9");
/* harmony import */ var _wordpress_primitives__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__);


/**
 * WordPress dependencies
 */

const closeSmall = Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["SVG"], {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, Object(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__["createElement"])(_wordpress_primitives__WEBPACK_IMPORTED_MODULE_1__["Path"], {
  d: "M12 13.06l3.712 3.713 1.061-1.06L13.061 12l3.712-3.712-1.06-1.06L12 10.938 8.288 7.227l-1.061 1.06L10.939 12l-3.712 3.712 1.06 1.061L12 13.061z"
}));
/* harmony default export */ __webpack_exports__["a"] = (closeSmall);


/***/ }),

/***/ "g56x":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["hooks"]; }());

/***/ }),

/***/ "gdqT":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["a11y"]; }());

/***/ }),

/***/ "hF7m":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["keyboardShortcuts"]; }());

/***/ }),

/***/ "jZUy":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["coreData"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ "rl8x":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["isShallowEqual"]; }());

/***/ }),

/***/ "tI+e":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["components"]; }());

/***/ }),

/***/ "wx14":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _extends; });
function _extends() {
  _extends = Object.assign || function (target) {
    for (var i = 1; i < arguments.length; i++) {
      var source = arguments[i];

      for (var key in source) {
        if (Object.prototype.hasOwnProperty.call(source, key)) {
          target[key] = source[key];
        }
      }
    }

    return target;
  };

  return _extends.apply(this, arguments);
}

/***/ })

/******/ });