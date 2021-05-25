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
  Object(external_wp_i18n_["__"])('(opens in a new tab)')))), Object(external_wp_element_["createElement"])(external_wp_components_["MenuGroup"], null, Object(external_wp_element_["createElement"])(FeatureToggle, {
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

// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__("HSyU");

// EXTERNAL MODULE: external ["wp","isShallowEqual"]
var external_wp_isShallowEqual_ = __webpack_require__("rl8x");
var external_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_wp_isShallowEqual_);

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/sidebar-block-editor/use-sidebar-block-editor.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
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

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/utils.js
// @ts-check

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

// CONCATENATED MODULE: ./node_modules/@wordpress/customize-widgets/build-module/components/welcome-guide/images.js


const EditorImage = props => Object(external_wp_element_["createElement"])(external_wp_element_["Fragment"], null, Object(external_wp_element_["createElement"])("img", Object(esm_extends["a" /* default */])({
  className: "customize-widgets-welcome-guide__image customize-widgets-welcome-guide__image__prm-np",
  alt: "",
  src: "data:image/gif;base64,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Y8ueTbu27du4c+vezbu379/AgwsfTry48ePIkytfzry58+fQo0ufTr269evYs2vfzr279+/gw4sfT768+fPo06tfz769+/fw48ufT7++/fv48+vfz7+///8ABijggAQWaOCBCCao4IIMNujggxBGKOGEFFZo4YUYZqjhhhx26OGHIIYo4ogklmjiiSimqOKKLLbo4oswxijjjDTWaOONOOao44489ujjj0AGKeSQRBZp5JFIJqnk/5JMNunkk1BGKeWUVFZp5ZVYZqnlllx26eWXYIYp5phklmnmmWimqeaabLbp5ptwxinnnHTWaeedeOap55589unnn4AGKuighBZq6KGIJqrooow26uijkEYq6aSUVmrppZhmqummnHbq6aeghirqqKSWauqpqKaq6qqsturqq7DGKuustNZq66245qrrrrz26uuvwAYr7LDEFmvsscgmq+yyzDbr7LPQRivttNRWa+212Gar7bbcduvtt+CGK+645JZr7rnopqvuuuy26+678MYr77z01mvvvfjmq+++/Pbr778AByzwwAQXbPDBCCes8MIMN+zwwxBHLPHEFFds8Y3FGGes8cYcd+zxxyCHLPLIJJds8skop6zyyiy37PLLMMcs88w012zzzTjnrPPOPPfs889ABy300EQXbfTRSCet9NJMN+3001BHLfXUVFdt9dVYZ6311lx37fXXYIct9thkl2322WinrfbabLft9ttwxy333HTXbffdeOet99589+3334AHLvjghBceXyAAIfkEBQQAAAAsMAEIARAAEAAAB0aAAIIACAiDhIaHg4WHjIqPkJGSjgyPlYiOhBObE4mIihMVohUTkgChFBSkpqiqpZITEqISr5GFm5mWuqa8vYK5toWekwiBACH5BAUEAAEALDABCAEQABAAAAdWgACCg4SFhoeIiYqLhgsLixI1Hx81EgiHCAhqbpxuahOXhQgTVlBeXlBWoIUXo1ZLp0uqoYMHozV0c3N0n7SECBIuNWouloiOghcHAI+JyIzMzdDTi4EAIfkEBQQAAQAsMAELAQ8ACwAAB2GAAIIACIWGhYODCBOMjRITCIoIflaVlpV+kYsTVkowMElJn0pWkIsSayt8HCMFfHwra5CEnCt9fH23fbGQDIudoKGipLOEFSxIyUgYSCwVkYMMF9PU0wyJggza29rY3t+BACH5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uRXq3s0rubPnz6BDix5NurRpwJQtY9aM97Tr17Bjy55NW3Lqy3Tttq7Nu7fv38CDF769Wjdn4ciTK1/OvDHx3JubS59OvTrw55mNW9/Ovbv3yS0q484e/bv58+jRY2d9PL379/CRr9euGKZf+3vx4ye9X0B/wfpphVp79QlYWn9XIfbfYAs61uBkBP5GwBXhATFDceUltiCCnD3oGYeGBZjVgA4ayF97CR7m4V8rFihai7PBVOGF0O0W34045ujafBnq6OOPQDpX4XjsBWnkkUiiNiT/hjYm6eSTOvLYJJRUVnmelBFaqeWW0mHJ5ZdgLudlmGSWyduYZqappmlorunmm7YtWWOWcNZp53BykjflnXz2eV+eRfop6KD+AUofoYja2WaijKq5aKOQhvlopJRqOWmlmD55aaacGrlpp6BGaWiPoZbq46empqreqHuq6uqq4jFJ56u0codqrbg2d2uuvMrH6qy9BivcrsIWSxuxxib7GrLKNsvfr85GWxuz0lYLXqxzWqvtadRu662C0H4r7ofhjmvuY92eq25+5a7rbojtvitvYOnOK2699nqLb77a7stvtf7+G23AAjdLcMHJHoxwsQovHGzDDvMKccS4Tkwx/60WX+xqxhqnynHHpX4MMqgij8xpySZjinLKlK7MMqQuv8xozDIjSnOlBWzgwM489+yzAxsAO3C83xKgwgMaDKD00kw3PYAD594MKQpJO2310gZAba7UjFpggNURpLCXAwNogMK7XCNaQARXhz122We7mzahG1TttNt6kW022kRrS3bbYucNN9/Y6il0pmig4ZcNLBX2wNUD4C2A3nGvO3eViS/eOGGPA/723nL3bW3nYAc++eChFx6ouH+X/nnl6l4uaAZfuy446JaLXm0AbNt+Ou6x614t1b5TTrhq2Z6bgd1LS2586sgbrm4AG/TevOnP5676oeZ2kQIKD0Dwtf/zqGsf/erujsA2+cBHLfy36keOffnBb0+quvGzD7v79rdqbv7za9/W3oc4xfWFcYJhnAI3pxcAvu54ROJeqjJ3QAZqboF9ceDt9jfA/h1uWxr8HQfHJbtG5Y8UIoSgrOYVwuzV73wSxF8EDPAA0wnBABAY4b0IWDNhlbCHb/ohENckxCGmqYhGLBMSkygpHjIRY0584saiKEWPUbGKIbsiFkmmxS2erIteVBkYw9iyMZIRZmY848zSqEabsbGNg1oiHI8kxzkGqY52/BEe8ygqD/JRVXv8440CKUj4ELKQ7jkkImEFw/stsox+fOQXIylJMVKykpBspP8w6cZLcrL/UYr8pHVCKUrqkLKUXXojKrl0ylWKSZWutFIrY+krT9KST7O85XVgqUsn5bKXvfklMKfFy2F6qpjGBJIwkxmbZTJzWch8Zo6cKU02RbOa8aEmNkdDHAZIb5tHvCY406PNcX6mnObsDDrTeS1NfpCdSFonPB0kznnaqp72HCU+82nKffIzlbb8Z5LkKVAV+bOgyiEoQgmj0IUCaEneZM0VHOrLUcGIotm06Dsxmsg8sYAqCeDoQOHCggp48wNciAJLyJKAlibALTBFS0th4lKXfmWmLH0pAXCaFp7GVCs6XUtQbzpUmhaVLT4laliSOpalxIALH5jLFyqwhiVQwQZX/8iqVrfK1axy4Ksc6KpYx0rWrbrErC0oq1rXeoWvehWsblVrXNmq1bjOlUIyoetb9UrWs1KorGHlK1796lW93tUlcSWsYLv6VcUqdqt3zepj28oBx6ZVrQSwARWWsIYKfGEQE1ADE4oQgygkIQlRSK1qV8va1qYWta6NrWthK1vTnva2uH2tbVVL29qalrWnfW1vfWvb4BL3uMJFrnBzC1zYGne1qH2ub287294aV7rBjS5upatc2V53u9wF7m55O1zeKhe7zo1BEZighlEMQgI1YIEJFIABDCjgvvjNb37ZwF823DcEIdCvgAdM4ALjt74ITrB9FQDg+t7XwQYuMP+EIRzhCls4vwC+rwlCQOEL0xfBBu7wfwOs3wwPOMEC7vCEMWBiFYPYwxdesYIXTGAUH5jGMNavix8cghnU4LOkAMABLkDkBhS5AUhOspKXzOQmO/nJTD4ykolM5SpTWclEBgEIpAzlJh+Zy1i+wJTF3OUyQxnMZq7ymKNMZjM/Wc1L5vKX22xkOsPZzW6es5WvnGQ925nOeK7zmvtM5iozIMiCOAADFs3oRjv60ZCOtKQnTelKM5rIls60pA+gaAZwWtOgDnWlPy3qUpv61KhOtapFzWlEu/rVsI61rGdN61rDutW2zrWr3eteXfv616/uNbCHTexiG/vYyC72AhZykOxmO/vZQV72sl09bWhbe9cIuLa2X11tW3e71t+mdbhjLW1m/3rc2063uklRbmqbe93PFja8oY1ucr8b3PcWd77tXe95+/vfAA+4wAdO8IIb/OAIT7jCF87whjv84b7mdbYhTvGKW/ziDpc4xjdu7UAAACH5BAUEAAAALHEApwCPAcoAAAf/gACCggcMhoeIiYqLjI2Oj5CRkpOUlZaXmJmOB5ydnIaCoACapJOhpaiVB4OsgwwXsLGys7S1tre4ubq7vL2+v8DBwsPDhrDGxMm8yMrNvwytgggVFR8s19jZ2tvc3d7f4OHi4+Tl5ufo6erfM+3u7evx8vP05h8VEgut1VZcRP8AAwocSLCgwYMIEypcyLChw4cQI0qcSLGixYsYCT7hYqWGBFYHXFhJo2SFEyooU6pcybKly5cwY8qcSbOmzZs4c+rcybOnz59Ag6p04iRGGisuVo26kONJkiFGokqdSrWq1atYs2rdyrWr169gw4odS7as2bNo06q9OiTJkw8X/w4geGXCixEbAvLq3cu3r9+/gAMLHky4sOHDiBMrXsx4b4IEjSNLnky5sgDINryYiDv3QogYBAjgtUy6tOnTqFOrXs26NePQMUJcAND5MwEBo13r3s27t+/fwHsTaBF7dm3QuIMrX868ufPnqocXp/3KdnLo2LNr3879t3TZ1D0jz929vPnz6NP7/W68+nj18OPLn++dOPjjt8nT38+/v//D7IVnnX7/FWjggfMFiN91CDbo4IPaKehefhBWaOGFwtnXnngUYujhhyBSJiGHDIZo4oko/jXigCm26OKJK7734ow0QhhjhzXmqGN/N5a4449AntcjgUEWaaRzQx6p5P+SyyXJ5JNQuuZklFRWSdqUgIXWl5Z6ccllal8KEGZpYyIW2plnrhYmmgDeZliZjcE5mZzAEXDFbliq6OZea7pJZ2V9grmnYmiyGd2ghhb255aDSrboa402GWl0Ggooo5WYZrpYnpp26qmKlS5I5KekdhpaqBP6WOqqmnLK6qtU9gjrrJi6SuutRdqK66466srrry/6CuywJp46najEJjujsMo2WyGzzkaLILTSVusftdZmmyCqJI6q7bf7YQvuuOWJS+652ZmL7rrNqcvuu3VyyyK89HLnbr34snZvvvyetm+/AAMq76UBF4znwDgarLC+CKu68MNkNuwtxBRH9m//xRhvKXHGHDu6cccgE/pxyCQrOnLJKGd5csos87lyyy1fDHPBMs8McM0284tzzvju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CBMqXMiwocOHECNKnEixosWLGDNq3Mixo8ePIEOKHEkymSBWJVOqBHnS1cqXMGPKnEmzps2bOHPq3Mmzp8+fQIMKHUq0qNGjt3QhXcp0mdKmUKNKnUq1qtWrWLNq3cq1q9evYMOKHUu2rNmzaNOqXcu2rdu3cP/jyp1Lt67du3jz6t3Lt6/fv4ADCx5MuLDhw4gTK17MuLHjx5AjS55MubLly5gza97MubPnz6BDix5NurTp06hTq17NurXr17Bjy55Nu7bt27hz697Nu7dvhS1/C38UfLjx48iTK1/OvLnz59CjS//3dHru6taza9/Ovbv37+DDix9Pvrz58+jTq1/Pvr379/Djy59Pv779VAwYfCx+X9+ECi64UMEEHvHXHz4VYIDHDW+4cKBlDNQwhwce3FDDg5ORcgELO5Swgxs16IchZKQ0oEAJBtBgxwcijvgYAyaWoIESK7boYmMwnjhjjTe+GOOOLI6GnXg5okhjkKINGV7/kUDa2KNiTB7p5JOIRckjlYtZiSSWiWk5JZeEeQlmlz9KOWaVZV55ZmFFGmDmmmymuSWcgrX5Jp11xuimmngCZiefffolZqB+yvkloXkNimhfii66V6OOJmpopHxBSqldll5KV6aaysVpp3B9Cqpboo7KVqmmqoVqqmityqpZrr5KVqyyikVrrWDdiqtXbao4566qXjBDhzsACmxaEU5Y4QcHoHRsWgni8YKFzbr0LFr/uVCDCwRe2xYADFxwwaHennXAAeWmq+667LbrbmwAvJtKvEGBS4i48nKCbwAM0BsUCwFggEG+mwgcAMBAxYswwaYA7C9QVzC8ScRUSFxW/8UWOwrAwxcMnHEpGFxw08aIdPwxyCLzZGIAHCRwsiYJcADUyi2/nEnMNues88489+zzz0AHLfTQRBdt9NFIJ6300rgZyPRPTj8t9dRUyySAAIpcjYjWAXCtitddY50K2JZcbbbZsIB9NiVkP9K2Jm93Encvc4PWttpi1+0J3l+LjcnZa7+Ct96JEL6133IjDorhtTBe9eOQRy755JRXbvnlmGeu+eacd+7556CHLvropJdu+umop6766qy37vrrsMcu++y012777bjnrvvuvPfu++/ABy/88MQXb/zxyCev/PLMN+/889BHL/301Fdv/fXYZ6/99tx3b5ji3ocv/v/45KNOACN+n1++JHeDv7417b8vf1nuz2///fjnn7HYBujv//8AnE79AjiNB0xHSYOJ340QKBgFEvAfA3ygBCdIwdMALoIVJMYFM8jBDnrwgyAMoQhHSMISvgOD3ImaCX2hwhW68IUwjKEMZ0jDGtrwhjjMoQ53yMMe+vCHQAyiEIdIxCIa8YhITKISl8jEJjrxiVCMohSnSMUqWvGKWMyiFrfIxS568YtgDKMYx0jGMprxjGhMoxrXyMY2uvGNcIyjHOdIxzra8Y54zKMe98jHPlpEFIRYWdLapoAG5ASQARAk0ghpSJs4y2RcdFbApuayQI7sYZcjGUw0mQiM+SzVAZVEBCghwUmakEtnHJBZIlJJiAR48pROGcX/ZnANBqYDYAZL2oXqlZ8AgAAEYCTZvgjRSy2S4lyIKKUfl8nM+CjTi89spjSnSc1qWvOaqGthFrWJzW5685vgDGfybIlFcorznOhMpzrXyc52uvOd8IynPOdJz3ra8574zKc+98nPfvrznwANqEAHStCCGvSgCE2oQhfK0IY69KEQjahEJ0rRilr0ohjNqEY3ytGOevSjIA2pSEdKD25esSUbS6lKV8rSlrr0pTCNqUxnStOa2vSmKg0EACH5BAUEAAEALF8AZwCxARUBAAf/gAGCDISFgoeIiYqLjI2Oj5CRkpOUlZaXmJmam5ydnp+gmQwXDaUNFwyhqqusra6vsLGys7SPNSwKuSYfFbW+v8DBwsPExZcKczslyyU7MwjG0dLT1NXW1Bok2gYlOhPQ1+Hi4+Tl5jMBGhEWIh0dMXcVE+b09fb3+KokFjYCBC0w4s3LR7CgwYP3SIgQ4K9FkjsuBiKcSLGixVokRjD89zAiuIsgQ4ocOUnhRoAQJZJcybKlRZMNA0Z0SbOmzXow/8lUebOnz5/BcqKcCbSo0aOsMp7cibSp06eVhDKFSrWqVakprWrdehQrUa5gw7r0ylOs2bMUyaJdy9ag2rZw/+OWeyu3rl1pdO/q3esrL9+/gJNqjJk1sOHDnvwiXsw4kuLGkCMfeiy58mLKljMDxqy5813OnkPDBS269FnSplNzRa26NVWlhL+6ng2WNe3bQG3j3m1TN+/fLH0DHx5SOPHjaRfGLou8OUnjzqMnXJggAQemBw5I337RZPXrWbNzHz9x31KB5NMjVGchQQvwstXLv5dNG4kSLzwKEj+//7hkzJTgwQvyHMKffwhW84EJuSiAwQwuSHBgghROg4B2plxwwQEXalfhh9MUYgiIJJZo4okopqjiiiy2KAwCH7koYysTzmgjKBreqKMrNezooyfo/CjkkEQWaeSRSCap5P+STDbp5JNQRinllFRWaeWVWGap5ZZcdunll2CGKeaYZJZp5plopqnmmmy26eabcMYp55x01mnnnXjmqeeefPbp55+ABirooIQWauihiCaq6KKMNuroo5BGKumklFZq6aWYZqrpppx26umnoIYq6qiklmrqqaimquqqrLbq6quwxirrrLTWauutuOaq66689urrr8AGK+ywxBZr7LHIJqvsssw26+yz0EYr7bTUVmvttdhmq+223Hbr7bfghivuuOSWa+656Kar7rrstuvuu/DGK++89NZr77345qvvvvz26++/AAcs8MAEF2zwwQgnrPDCDDfs8MMQRyzxxBRXbPFUxRhnrPHGHHfs8ccghyzyyCSXbPLJKKes8sost+zyyzDHLPPMNNds880456zzzjz37PPPQAct9NBEF2300UgnrfTSTDft9NNQR53yBSSnMjIAIQcCACH5BAUEAAEALGAAZwAYADIAAAf/gAGCg4IMhoeGhIqDFxcBDZCRkBcMi4oYGAqam5ssFRMIl4slpKWkHj+flQEHB4sksLGxJS81EoOtiiK7I7u+Ij1AMxWWAQI2NgLKy8swPiHExcbM1M4KFQy5C4rUzATWxNrc3QIELSs+16yu24Tk5efp4ezj3ebo6uLu5Pfy6wHtRAh61y8fvX324hkEKGjEQH4K5zFESK2gxHaDCEb8h/FhQnwX61XcqC8jRJAcRXojedDkR3/6BE57ubCdw5kjUZb0mNOfNI0of570WQwoUUtG1QmlGa3oUKVOmVqSmbQpoZtVpeFcGTRqT6hIn1pdlHXp17Eql1k0a88ZBrQBSRy+a+bjbTECW6vBdKWIAIEEgAMH1snXkixZJXaADaBglKlSHnZ8qBCKUA0WgzhpZuFCQuV1AFYJkkS6keh/lhAhOgCgtWsAgQAAIfkEBQQAAAAsYgBnAB8AOQAAB/+ADIKDhIWGhwCJiouMAA0NII+Skw0XFwyNmYkKCkidnKCgIR8VmpklqKmqqjsspo0ksbKzsgYlPwsIjSK8vb6/vh0dSkcVE6+KAsrLyzY2AgQtScXHyADM2MvRMNTW2d/b1CPf5OXR08bI5djn3a/rzO3p7/DK8tWm9fbS7vn69yL0wbs3TqA5fvP81bun7h9CfJoEMqS38GHDeOQsUmQWIIXHFAGgaVTITEWGkxlUiEQ3IaBEAio2oFRBgKBBAgJMzqw5MmI2nSdVTiS5rOPHkEN9QluXNJPEnk4dsrw4EGqjp1M3Mn3o0iC4hwW9Zmt6VWo/pdpqsrPKCBzOeGxpF2E9G3Xs2qxaa77VFrdtvL378Gr9yi3hqwSIE/NIjLgFh8IQTdGaLKvEDcOmVmlO5UEHZk2hQmPAwAlDjgoIdFmrZOnCJEuPQFyyxuhAokODaC9KnVq379/AgwsfTpy2peKZRodezikQACH5BAUEAAEALGQAZwCoARUBAAf/gAGCg4SFhoUMiYqLjI2Kh5CRkpOUlZaXmJmam5ydnp+goaKjpIINkg0NIKmsra6vDRcXDKW1tre4ubq7vL2+lAoBwYcKxcbHyMnGIR8VCL/Q0dLT1NXW1wEl2tvc3d7bHiYTz9jl5ufo6eq1JO3u7/Dx7gYlLxUT6/n6+/z90iIAAwocCBCCARQjAnboEOOECwn+IkqcSLEiIQECAmDcyLHjRoMqOBJoAYPCQ4soU6pcOc2jS44gRZI0CZGlzZs4c2p6yTPmxpElT+ocSrToTZ4vfWIEStOo06dQ+SF1qVQAU6FRs2rdGm2qx6pXa3IdS7asKK8dwc7Earat27eR/9DCNBDy51qxcPPqNSv3I12ZQfHuHUz4qUcCSNUGLsy48dC+GBU3dUy5MkrIAiSztcy5cz7MmgV7Hk3aGui/dheXXs0a14YMsGPD3gA5dOvbuEGliMC7t+8UfW3nHk7ckgXfyCNYEJkY9dK7xaNLN3Q8ee/lcoVP3168unXlwZ1bhc69fG7v1rGj1W6+fWn0ydV7Ze++Pmf4yOVPpW+/P2MBu30XAXDZiReWfwhS9ppssdEWXl3PqZbghJydBuF4ElKoYWMWAjbZhiAS1mFqH4ZoIlwjRljiiSyWlSKGK7Yoo1YvHjjjjTTWZiB5OPZ4FEaF1Mijj0SqBORFOl5oY/+RTP74oIebNSmlPxkh+SSJUU6pZURCZrjll/10GSOYZKojZpZlpllOAmyy2UILbcbZJkhxtsDBCmOqqec18vTpZz1o7imoNN8UaqgHL7iAz6CMQlOMIMpEKikGLLiAADmNZrpLKqbA4umns2gqajSOlGrqqKimquqqrLbq6quwxirrrLTWyhmntuZay6O69urrr8AGK+ywxBZr7LHIJqvsssw26+yz0EYr7bTUVmvttdhmq+223Hbr7bfghivuuOSWa+656Kar7rrstuvuu/DGK++89NZr77345qvvvvz26++/AAcs8MAEF2zwwQgnrPDCDDfs8MMQRyzxxBRXbPH/xRhnrPHGHHfs8ccghyzyyCSXbPLJKKes8sost+zyyzDHLPPMNNds880456zzzjz37PPPQAct9NBEF2300UgnrfTSTDft9NNQRy311FRXbfXVWGet9dZcd+3112CHLfbYZJdt9tlop6322my37fbbcMct99x012333XjnrffefPft99+ABy744IQXbvjhiCeu+OKMN+7445BHLvnklFdu+eWYZ6755px37vnnoIcu+uikl2766ainrvrqrLfu+uuwxy777LTXbvvtuOeu++689+7778AHL/zwxBdv/PHIJ6/88sw37zyRsnSeSOcAVG/99dhnr/323Hfv/ffghy/+E/jkl2/++einr/767Lfv/vvlBwIAIfkEBQQAAAAsYgBnAG4AMwAAB/+ADIKDhIWGh4iJiouMjYsHBwCSk5SVlpeYmZoADZ2en6ChoqOkoRenkZuqq6yUCq+wsbKztLW2CiGxJjk1Eq2/wJglw8TFxsfIycrHHjcuDMHRwCTU1dbX2Nna29gGJTsf0NLjmyIdHSIjIusj5+nq6/Hy8/T19SPqOD1ELBcMqeQCTrIhgAABAQgFEDSYsKHDhxAjRrQxpEi/fwIzShKQIMHDjhJDihRJscgMfxo1cvToEOTIlzAVVjwpLmXAmDhzNix50aZAnUBj8kTpk1zQoyQr9iw6DqlTiENrMg32tGrCqMEITAVg1SrWrVS7Pv0K9pfYsUqJlm111inZtav/2iJ9u2rDgLt4897NoDWa3KN0VUHQS3gAhKZ/gQbeNLhw3sPSEitOK3VVY8d3IfuVnHOxpsuYNYflLJQyMNCORQMjjZOsiJeoC6s2yxom2RGwMeOdzbb2S8+ZYhPmzcr3b9O/hOslHtd4UotqWSl/jNi5ROCYMui+u6G6dajI4Tb/Dh565YB9vZPfGV78pvXll7p/D9+ha5jpi9a3H37EfEz7sWfef/QFKNOABGZiIELYEbjggfJlpd+CDf7XkEEYflcSTQkqeGGG1m0YXYeUPCgiNJCQSElHLCbAQ4sttiDjjDDWaKONL7LYgg1GmIRSiipSws2QRBZpjQEaeGDCRI8ABQnAMlBGKeUx4DDppCS1hKBlLrd06WUsWprQS5NOnnJKKCCkCUIpbLYJSpqnnBckJJA4Yuedh1xZCQJ8IqAnJoEAACH5BAUEAAEALGIAYACrADoAAAf/gAGCg4SFhoeIiYqLjI2Oj4gIkgiQlZaXmJmam4uTlJygoaKjpKWmp6ipqqusra6vsLGys7S1tre4tQy7vL2+v8DBwsPDB7nHgw3Ky8zNzs/Q0dLRF9UMyLUKhArc3d7f4OHi4+TjGBg5FZ/YtyXu7/Dx8vP09fb2OyYT6+yzJP8AAwocSLCgwYMHDZTQUWGCrRGCRIjo0CEARIkjKIoYAbEUR4kBJIocSbKkyZMoN6pMSZJijBMuJNwSEMCGAAIEBNG0idOVgJ9AgwodSrSo0aNGCbSAQaGGTFw/EyQgFHVqK6RYs2rVqpRpzFxVqQqQ6nOr2bNbu1KIKaIfLbRw/+MSVfvV7Sy5eOXSfWo3Vt6/Z/f2lQW4cFbBjXIODmW4cdKla/kyormYk+PLQhFXvoq58962nBRvVtTZM+S6iUdrKo1ZsyIVEAzInk1bNgQVqhGxvuw6UYQBjAZEyH1ot+PeiCAAXzQAAnFDxhsjP6Q8uPPnYqMDnm6oOvPr2HVq335aciLvipqHHzSevFfzyZenBx++/V/uhdAnUr8+gP28nzGiHyL8rfcfXvgRMuAhBdZ3YFwJDrKgIQ1i9yCE5TUyYSEVPnchXBEK8ltww/X3IVp7dfRabLW1eFt//p1oVoiNUAZjjDJyVZ6KmohmYo46vnfjKEAGGdkjNg6pW8ORWAXIiI9KFsckUjRGOdmUR1VpJWlYPibklqt1WZSW7IHJnphzZYgMTUnCgmaaX67Z5itvDkWmmYXUmZma2CTAwyFkicKDVYO0MKhUUrVgKKKJLsqooociCimjCShK6aWYUtoCByscieclCIUq6qikklDCC059msk9rLbqqqsevOCCQ3ZxEws4IYSAq67f5Lrrr+UEiwELDfEzizKIIAuLMyCAwKyzzTT77LTTVNvABdfgsgs7xHTr7bffHgMAAKq2EggAIfkEBQQAAAAsYgBnAKgBCwEAB/+AAIKDhIWGh4iJiouMjY6PkJGODJSVlpeYmZUHkp2en6ChoqOkpaanqKmqjw2trq+wsa0XF7K0nAAICKu8vb6/wMHCw8SKCsfIycrLxxgYzDMfLhO5u8XX2Nna29zdjiXg4eLj5OXhO20u3uvs7e7v8Isk8/T19vf49BoeNzXx/wADChw4SoTBgwgTKjTYAYUBCAl7KDnzgQHBixgzaoQnoKPHjyBDdiSg4iHIIUkoWtzIsqXLl6lEypRJ0uTHIVFUwtzJs6fPQzODfqwJ4WTOij+TKl16UahTASUhELh5dCXTq1izansqNKpRnVrDih2rimtQr1TBkl3Ltu0jszP/0XrEqdatXVMj7maEK1NuR7pI9QoWlXfwQL4i/QoAbNWw48c+EYdUzBiy5csuJYOkXBWzZ08qhIgeTZq0is+lNH/kXBe160QWBsieTbv2AAuvQ6n2yDpw7t+FMgxwNCADcE+7O/ZufPy3cOLGm0dKDtXm3M7SgT9vVDw7JOrLvTsfzj26eEbgrf/Ffh71dkbd2y9KXzStb/mY3y+Kjx8R/a/39QeZforwJ2Ah/92UUoAHGkZgIgY2KEiCcy3InISCPYhIhBJS+JeFGD6m4SEcNujhYiCGKFZhiYxoSIkHnohSayra5WIhMAooY4o16hWbbUDKhluNO9LYY1uhlaak/2in9Vgkg0cmJcIoAkQJwJMXWqklZFhu6eVlXX7JVpViGhJmmWjadWaabJK1ZptwZvVmnHQqNWedePJUZA559vnTnn4GupOM7AlqaEYJJKrooow2qmhUjBrB46GUXpTPpZjSY0AJO+SQZaWgwmPOqKSK48ELNXwa6qreMOPqq8yYkEMNErBqKzyy5KprLLQwkMWtwLajybDEWhLsscgmq+yyzDbr7LPQRivttNRWa+212Gar7bbcduvtt+CGK+645JZr7rnopqvuuuy26+678MYr77z01mvvvfjmq+++/Pbr778AByzwwAQXbPDBCCes8MIMN+zwwxBHLPHEFFds8f/FGGes8cYcd+zxxyCHLPLIJJds8skop6zyyiy37PLLMMcs88w012zzzTjnrPPOPPfs889ABy300EQXbfTRSCet9NJMN+3001BHLfXUVFdt9dVYZ6311lx37fXXYIct9thkl2322WinrfbabLft9ttwxy333HTXbffdeOet99589+3334AHLvjghBdu+OGIJ6744ow37vjjkEcu+eSU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qqy26uqrsMYq66y01mrrrbjmquuuvPbq66/ABivssMQWa+yxyCar7LLMNuvss9BGK+201FZr7bXYZqvtttx26+234IYr7rjklmvuueimq+667Lbr7rvwxivvvPTWa++9+Oar77789uvvvwAHLPDABBds8MEIJ6zwwgw37PDDEEcs8cQUV2zxUcUYZ6zxxhx37PHHIIcs8sgkl2zyySinrPLKLLfs8sswxyzzzDTXbPPNOOes88489+zzz0AHLfTQRBdt9NFIJ6300kw37fTTnzhRMgsiu1BBIAAh+QQFBAAAACxYAGcA9wAZAQAH/4AAgoOEhYaHiImKi4yNjo+QkZKTlJWKDJaZmpucnZ6foIoNDQCYoaeoqaqrrIQKCiw1FROttba3uLglO3MYFbnAwcLDkCQkGh5zNabEzc7PtiIAERo7MxfMiQIC0N3e34vbFiQlCg3Z4Onq64XbPOTm6Ozz9M/bCfDn9fv8w/f58voJHHjqXzl9BBMq9GQw3sKHECk1RBixokVtAvAdDHixY8SJHD2KTAhypEmIJU+qJJhypct9LV/KXBdzpk1vNW/qbJZzp09gPX8KhUSgCwFBXboIKqpUUdChUBdtEIICgAghQqShELIhXEaAUcM+mlr1alYAW7s6/bpRrNtFI/9SjADQJUUKpXHnrtXo8K1fR0cbPf1LONLgwogZHU7M+NDixpAFPY7ceDLlxJYvF86s+e82AmA7iwbwOfRozaXbnkYtALTq1ZQ5w4Yqe7bQ2rZ94s6tU5xp3mJbtDhEzRo24JCRKQuJ3K8cX80hx5oVvbr169iza9/Ovbv37+AVXbgQPiwGDOVtIUAgaH16n+4BxH8v9AD9+/jz69/Pv7///wAGKOCABBZo4IEIJqjgggw26OCDEEYo4YQUVmjhhRhmqOGGHHbo4YcghijiiCSWaOKJKKao4oostujiizDGKOOMNNZo44045qjjjjz26OOPQAYp5JBEFmnkkUgmqeSokkw26eSTUEYp5ZRUVmnllVhmqeWWXHbp5ZdghinmmGSWaeaZaKap5ppstunmm3DGKeecdNZp55145qnnnnz26eefgAYq6KCEFmrooYgmquiijDbq6KOQRirppJRWaumlmGaq6aacdurpp6CGKuqopJZq6qmopqrqqqy26uqrsMYq66y01mrrrbjmquuuvPbq66/ABivssMQWa+yxyCarLJXzMcsek4EAACH5BAUEAAAALC4BcAAcACAAAAdngACCg4SFhoY8h4qLjI2Oj5CRkpOUlZaXmJmam5ydhygZGRYCACqhKgACFqEoBI0OEREbpBmxGQAEG7EOjrCytLapuhG8jRtCQiqkKMgoqSrIG5ikrp7W19jZ2tuWCZ3e3OGN1ZsEgQAh+QQFBAAAACwtAXAAHQAgAAAHY4AAgoOEhYaHCYeKi4yNjo+QkZKTlJWWl5iZmpucnZYPEREOAoKgEQ+CAg6hqIwQAwMQpACvsQACArUQjbqzuqm/jA8QEA+zw8WpyK2Ys57P0NHS04MEntad2Jza1N2QiZ0JgQAh+QQFBAABACxkAGgA5gASAQAH/4ANDQGEhYaHiImKi4yNjo+QkZKTlJWWlAoKl5ucnZ6foKGio6SlpqeoqaqrrK2ur7CxsrO0tba3uLm6u7y9vr/AwcLDxMXGx8jJysvMzc7P0NHS09TV1tfY2drb3N3e3+Dh4uPk5ebn6Onq3Bka7huFEO4QhRvuGhnr+ocZA4T5hAwELARwAMB9+/r9KyQwQMMABQ8iXJfBgEV4hCBYpEdog0UDEieKHEmypMmTKFOqXMmypctWCV6SjCmzps2bOHPq3Mmzp8+fQIMKHUq0qNGjSJMqDSBoaTYGDJxiAwBAqtWrWLNq3cq1q9evYMOKHUu2rNmzaNOqXcu2rdu3cOvjyp1Lt67du3jz6t3Lt6/fv4ADCx5MuLDhw4gTK17MuLHjx5AjS55MubLly5gza97MubPnz6BDix5NurTp06hTq17NurXr17Bjy55Nu7bt27hz697Nu7fv38CDCx9OvLjx48iTK1/OvLnz59CjS59Ovbr169iza9/Ovbv37+DDix9Pvrz58+jTq1/Pvr379/Djy59Pv779+/jz69/Pv7///wAGKOCABBZo4IEIJqjgggw26OCDEEYo4YQUVmjhhRhmqOGGHHbo4YcghiiiLZmcJsiJKKao4oostujiizDGKOOMNNZo44045hgIACH5BAUEAAAALHQAfQDOAB0AAAesgACCg4SFhoeIiYqLjI2Oj5CRkpONBJSXmJmam5ybEQOgFoMGoAaDFqADEZ2sra6vsIkQqaKCqQOnqRCxvL2+v4uzoYO3uaC7wMnKy52fw4KkA6aCqKCrzNjZ2q6W297f4OHi4+Tl5ufo6erp3evu7/Dx8vP09fb3+Pn6+/z9/v8AAwocSLCgwYMIEypcyLChw4cQI0qcSLEiNgwYLlywaFHjgQMcOSJAENJiIAAh+QQFBAAAACwiAX0AHgAQAAAHRIACgoOEhYaHiIQGA4wFg4yMgwGQBokCkAOOgpiSnImYAY+QnaOJi4yhm6UCk4yVlrCxsrO0tba3uLm6u7y9vrkEv4KBACH5BAUEAAEALHAAfgDYACIAAAeqgAGCg4SFhoeIiYqLjI2Oj5CRkpEDlQMCigKWA5Odnp+goaKjnZiJpqSpqqusraKoh7Cus7S1tp6bsoWalre+v8DBwsPExcbHyMnKy8zNzs/Q0dLT1NXW19jZ2tvc3d7f4OHi4+TGuuXo6err7O3u7/Dx8vP09fb33wn6+/r4/v8AAwq01qBgg4EIEzFgkLDhIQAAFixwSLGixYsYM2rc+K8gR4EKFHz8FwgAOw=="
}, props)), Object(external_wp_element_["createElement"])("img", Object(esm_extends["a" /* default */])({
  className: "customize-widgets-welcome-guide__image customize-widgets-welcome-guide__image__prm-r",
  alt: "",
  src: "data:image/svg+xml,%3Csvg fill='none' height='240' viewBox='0 0 312 240' width='312' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='m0 0h312v240h-312z' fill='%2300a0d2'/%3E%3Crect fill='%23fff' height='108' rx='2' width='216' x='48' y='80'/%3E%3Cg stroke='%23000' stroke-width='1.5'%3E%3Cpath d='m158.917 142v-15.111'/%3E%3Cpath d='m154.472 142v-15.111'/%3E%3Cpath d='m162.333 126.75h-8.889'/%3E%3Cpath d='m153.139 130.889v4.071c-1.928-.353-3.389-2.041-3.389-4.071s1.461-3.718 3.389-4.071z' fill='%23000'/%3E%3C/g%3E%3Crect fill='%23fff' height='21' rx='1.5' stroke='%231e1e1e' width='117' x='48.5' y='53.5'/%3E%3Cpath d='m70.592 53v22' stroke='%231e1e1e'/%3E%3Cpath d='m144.432 53v22' stroke='%231e1e1e'/%3E%3Crect fill='%23333' height='8' rx='1' width='9' x='55' y='60'/%3E%3Cpath d='m150 63h2v2h-2z' fill='%23333'/%3E%3Cpath d='m154 63h2v2h-2z' fill='%23333'/%3E%3Cpath d='m158 63h2v2h-2z' fill='%23333'/%3E%3C/svg%3E"
}, props)));

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
  }, Object(external_wp_element_["createElement"])(EditorImage, null), Object(external_wp_element_["createElement"])("h1", {
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
      keepCaretInsideBlock
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
  }), Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockTools"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockSelectionClearer"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["WritingFlow"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["ObserveTyping"], null, Object(external_wp_element_["createElement"])(external_wp_blockEditor_["BlockList"], null))))), Object(external_wp_element_["createPortal"])( // This is a temporary hack to prevent button component inside <BlockInspector>
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
      const inspectorContainer = sidebarControl.inspector.contentContainer[0];
      const container = sidebarControl.container[0];
      const ownerDocument = container.ownerDocument;
      const ownerWindow = ownerDocument.defaultView;

      function handleClearSelectedBlock(element) {
        if ( // 1. Make sure there are blocks being selected.
        (hasSelectedBlock() || hasMultiSelection()) && // 2. The element should exist in the DOM (not deleted).
        element && ownerDocument.contains(element) && // 3. It should also not exist in the container, inspector, nor the popover.
        !container.contains(element) && !popoverRef.current.contains(element) && !inspectorContainer.contains(element)) {
          clearSelectedBlock();
        }
      } // Handle focusing in the same document.


      function handleFocus(event) {
        handleClearSelectedBlock(event.target);
      } // Handle focusing outside the current document, like to iframes.


      function handleBlur() {
        handleClearSelectedBlock(ownerDocument.activeElement);
      }

      ownerDocument.addEventListener('focusin', handleFocus);
      ownerWindow.addEventListener('blur', handleBlur);
      return () => {
        ownerDocument.removeEventListener('focusin', handleFocus);
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
    }); // TODO: We should in theory also handle delete widgets here too.

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
  const widgetId = Object(external_wp_widgets_["getWidgetIdFromBlock"])(props);
  const sidebarControls = useSidebarControls();
  const activeSidebarControl = useActiveSidebarControl();
  const hasMultipleSidebars = (sidebarControls === null || sidebarControls === void 0 ? void 0 : sidebarControls.length) > 1;
  const blockName = props.name;
  const canInsertBlockInSidebar = Object(external_wp_data_["useSelect"])(select => {
    // Use an empty string to represent the root block list, which
    // in the customizer editor represents a sidebar/widget area.
    return select(external_wp_blockEditor_["store"]).canInsertBlockType(blockName, '');
  }, [blockName]);

  function moveToSidebar(sidebarControlId) {
    const newSidebarControl = sidebarControls.find(sidebarControl => sidebarControl.id === sidebarControlId);
    const oldSetting = activeSidebarControl.setting;
    const newSetting = newSidebarControl.setting;
    oldSetting(Object(external_lodash_["without"])(oldSetting(), widgetId));
    newSetting([...newSetting(), widgetId]);
    newSidebarControl.expand();
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
/**
 * Initializes the widgets block editor in the customizer.
 *
 * @param {string} editorName          The editor name.
 * @param {Object} blockEditorSettings Block editor settings.
 */

function initialize(editorName, blockEditorSettings) {
  const coreBlocks = Object(external_wp_blockLibrary_["__experimentalGetCoreBlocks"])().filter(block => !['core/more'].includes(block.name));

  Object(external_wp_blockLibrary_["registerCoreBlocks"])(coreBlocks);

  if (false) {}

  Object(external_wp_widgets_["registerLegacyWidgetVariations"])(blockEditorSettings);
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