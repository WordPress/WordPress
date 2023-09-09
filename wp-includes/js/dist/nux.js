<<<<<<< HEAD
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
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
=======
this["wp"] = this["wp"] || {}; this["wp"]["nux"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "51Wn");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ "25BE":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
}

/***/ }),

/***/ "51Wn":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
<<<<<<< HEAD
__webpack_require__.d(__webpack_exports__, {
  DotTip: () => (/* reexport */ dot_tip),
  store: () => (/* reexport */ store)
});
=======
__webpack_require__.d(__webpack_exports__, "DotTip", function() { return /* reexport */ dot_tip; });
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

// NAMESPACE OBJECT: ./node_modules/@wordpress/nux/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
<<<<<<< HEAD
__webpack_require__.d(actions_namespaceObject, {
  disableTips: () => (disableTips),
  dismissTip: () => (dismissTip),
  enableTips: () => (enableTips),
  triggerGuide: () => (triggerGuide)
});
=======
__webpack_require__.d(actions_namespaceObject, "triggerGuide", function() { return triggerGuide; });
__webpack_require__.d(actions_namespaceObject, "dismissTip", function() { return dismissTip; });
__webpack_require__.d(actions_namespaceObject, "disableTips", function() { return disableTips; });
__webpack_require__.d(actions_namespaceObject, "enableTips", function() { return enableTips; });
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

// NAMESPACE OBJECT: ./node_modules/@wordpress/nux/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
<<<<<<< HEAD
__webpack_require__.d(selectors_namespaceObject, {
  areTipsEnabled: () => (selectors_areTipsEnabled),
  getAssociatedGuide: () => (getAssociatedGuide),
  isTipVisible: () => (isTipVisible)
});

;// CONCATENATED MODULE: external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// CONCATENATED MODULE: external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/store/reducer.js
=======
__webpack_require__.d(selectors_namespaceObject, "getAssociatedGuide", function() { return getAssociatedGuide; });
__webpack_require__.d(selectors_namespaceObject, "isTipVisible", function() { return isTipVisible; });
__webpack_require__.d(selectors_namespaceObject, "areTipsEnabled", function() { return selectors_areTipsEnabled; });

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__("rePB");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__("vpQ4");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__("KQm4");

// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/store/reducer.js




>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

/**
 * Reducer that tracks which tips are in a guide. Each guide is represented by
 * an array which contains the tip identifiers contained within that guide.
 *
<<<<<<< HEAD
 * @param {Array}  state  Current state.
=======
 * @param {Array} state  Current state.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 * @param {Object} action Dispatched action.
 *
 * @return {Array} Updated state.
 */

<<<<<<< HEAD
function guides(state = [], action) {
  switch (action.type) {
    case 'TRIGGER_GUIDE':
      return [...state, action.tipIds];
=======
function guides() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'TRIGGER_GUIDE':
      return Object(toConsumableArray["a" /* default */])(state).concat([action.tipIds]);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  }

  return state;
}
/**
 * Reducer that tracks whether or not tips are globally enabled.
 *
<<<<<<< HEAD
 * @param {boolean} state  Current state.
 * @param {Object}  action Dispatched action.
=======
 * @param {boolean} state Current state.
 * @param {Object} action Dispatched action.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {boolean} Updated state.
 */

<<<<<<< HEAD
function areTipsEnabled(state = true, action) {
=======
function areTipsEnabled() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : true;
  var action = arguments.length > 1 ? arguments[1] : undefined;

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  switch (action.type) {
    case 'DISABLE_TIPS':
      return false;

    case 'ENABLE_TIPS':
      return true;
  }

  return state;
}
/**
 * Reducer that tracks which tips have been dismissed. If the state object
 * contains a tip identifier, then that tip is dismissed.
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

<<<<<<< HEAD
function dismissedTips(state = {}, action) {
  switch (action.type) {
    case 'DISMISS_TIP':
      return { ...state,
        [action.id]: true
      };
=======
function dismissedTips() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'DISMISS_TIP':
      return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, action.id, true));
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

    case 'ENABLE_TIPS':
      return {};
  }

  return state;
}
<<<<<<< HEAD
const preferences = (0,external_wp_data_namespaceObject.combineReducers)({
  areTipsEnabled,
  dismissedTips
});
/* harmony default export */ const reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  guides,
  preferences
}));

;// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/store/actions.js
=======
var preferences = Object(external_this_wp_data_["combineReducers"])({
  areTipsEnabled: areTipsEnabled,
  dismissedTips: dismissedTips
});
/* harmony default export */ var reducer = (Object(external_this_wp_data_["combineReducers"])({
  guides: guides,
  preferences: preferences
}));

// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/store/actions.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Returns an action object that, when dispatched, presents a guide that takes
 * the user through a series of tips step by step.
 *
 * @param {string[]} tipIds Which tips to show in the guide.
 *
 * @return {Object} Action object.
 */
function triggerGuide(tipIds) {
  return {
    type: 'TRIGGER_GUIDE',
<<<<<<< HEAD
    tipIds
=======
    tipIds: tipIds
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  };
}
/**
 * Returns an action object that, when dispatched, dismisses the given tip. A
 * dismissed tip will not show again.
 *
 * @param {string} id The tip to dismiss.
 *
 * @return {Object} Action object.
 */

function dismissTip(id) {
  return {
    type: 'DISMISS_TIP',
<<<<<<< HEAD
    id
=======
    id: id
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  };
}
/**
 * Returns an action object that, when dispatched, prevents all tips from
 * showing again.
 *
 * @return {Object} Action object.
 */

function disableTips() {
  return {
    type: 'DISABLE_TIPS'
  };
}
/**
 * Returns an action object that, when dispatched, makes all tips show again.
 *
 * @return {Object} Action object.
 */

function enableTips() {
  return {
    type: 'ENABLE_TIPS'
  };
}

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/rememo/rememo.js


/** @typedef {(...args: any[]) => *[]} GetDependants */

/** @typedef {() => void} Clear */

/**
 * @typedef {{
 *   getDependants: GetDependants,
 *   clear: Clear
 * }} EnhancedSelector
 */

/**
 * Internal cache entry.
 *
 * @typedef CacheNode
 *
 * @property {?CacheNode|undefined} [prev] Previous node.
 * @property {?CacheNode|undefined} [next] Next node.
 * @property {*[]} args Function arguments for cache entry.
 * @property {*} val Function result.
 */

/**
 * @typedef Cache
 *
 * @property {Clear} clear Function to clear cache.
 * @property {boolean} [isUniqueByDependants] Whether dependants are valid in
 * considering cache uniqueness. A cache is unique if dependents are all arrays
 * or objects.
 * @property {CacheNode?} [head] Cache head.
 * @property {*[]} [lastDependants] Dependants from previous invocation.
 */

/**
 * Arbitrary value used as key for referencing cache object in WeakMap tree.
 *
 * @type {{}}
 */
var LEAF_KEY = {};

/**
 * Returns the first argument as the sole entry in an array.
 *
 * @template T
 *
 * @param {T} value Value to return.
 *
 * @return {[T]} Value returned as entry in array.
 */
function arrayOf(value) {
	return [value];
=======
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__("ODXe");

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__("pPDe");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/store/selectors.js


/**
 * External dependencies
 */


/**
 * An object containing information about a guide.
 *
 * @typedef {Object} NUX.GuideInfo
 * @property {string[]} tipIds       Which tips the guide contains.
 * @property {?string}  currentTipId The guide's currently showing tip.
 * @property {?string}  nextTipId    The guide's next tip to show.
 */

/**
 * Returns an object describing the guide, if any, that the given tip is a part
 * of.
 *
 * @param {Object} state Global application state.
 * @param {string} tipId The tip to query.
 *
 * @return {?NUX.GuideInfo} Information about the associated guide.
 */

var getAssociatedGuide = Object(rememo["a" /* default */])(function (state, tipId) {
  var _iteratorNormalCompletion = true;
  var _didIteratorError = false;
  var _iteratorError = undefined;

  try {
    for (var _iterator = state.guides[Symbol.iterator](), _step; !(_iteratorNormalCompletion = (_step = _iterator.next()).done); _iteratorNormalCompletion = true) {
      var tipIds = _step.value;

      if (Object(external_lodash_["includes"])(tipIds, tipId)) {
        var nonDismissedTips = Object(external_lodash_["difference"])(tipIds, Object(external_lodash_["keys"])(state.preferences.dismissedTips));

        var _nonDismissedTips = Object(slicedToArray["a" /* default */])(nonDismissedTips, 2),
            _nonDismissedTips$ = _nonDismissedTips[0],
            currentTipId = _nonDismissedTips$ === void 0 ? null : _nonDismissedTips$,
            _nonDismissedTips$2 = _nonDismissedTips[1],
            nextTipId = _nonDismissedTips$2 === void 0 ? null : _nonDismissedTips$2;

        return {
          tipIds: tipIds,
          currentTipId: currentTipId,
          nextTipId: nextTipId
        };
      }
    }
  } catch (err) {
    _didIteratorError = true;
    _iteratorError = err;
  } finally {
    try {
      if (!_iteratorNormalCompletion && _iterator.return != null) {
        _iterator.return();
      }
    } finally {
      if (_didIteratorError) {
        throw _iteratorError;
      }
    }
  }

  return null;
}, function (state) {
  return [state.guides, state.preferences.dismissedTips];
});
/**
 * Determines whether or not the given tip is showing. Tips are hidden if they
 * are disabled, have been dismissed, or are not the current tip in any
 * guide that they have been added to.
 *
 * @param {Object} state Global application state.
 * @param {string} tipId The tip to query.
 *
 * @return {boolean} Whether or not the given tip is showing.
 */

function isTipVisible(state, tipId) {
  if (!state.preferences.areTipsEnabled) {
    return false;
  }

  if (state.preferences.dismissedTips[tipId]) {
    return false;
  }

  var associatedGuide = getAssociatedGuide(state, tipId);

  if (associatedGuide && associatedGuide.currentTipId !== tipId) {
    return false;
  }

  return true;
}
/**
 * Returns whether or not tips are globally enabled.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether tips are globally enabled.
 */

function selectors_areTipsEnabled(state) {
  return state.preferences.areTipsEnabled;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




var store = Object(external_this_wp_data_["registerStore"])('core/nux', {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['preferences']
});
/* harmony default export */ var build_module_store = (store);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__("K9lf");

// EXTERNAL MODULE: external {"this":["wp","components"]}
var external_this_wp_components_ = __webpack_require__("tI+e");

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__("l3Sj");

// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/components/dot-tip/index.js


/**
 * WordPress dependencies
 */





function getAnchorRect(anchor) {
  // The default getAnchorRect() excludes an element's top and bottom padding
  // from its calculation. We want tips to point to the outer margin of an
  // element, so we override getAnchorRect() to include all padding.
  return anchor.parentNode.getBoundingClientRect();
}

function onClick(event) {
  // Tips are often nested within buttons. We stop propagation so that clicking
  // on a tip doesn't result in the button being clicked.
  event.stopPropagation();
}

function DotTip(_ref) {
  var children = _ref.children,
      isVisible = _ref.isVisible,
      hasNextTip = _ref.hasNextTip,
      onDismiss = _ref.onDismiss,
      onDisable = _ref.onDisable;

  if (!isVisible) {
    return null;
  }

  return Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Popover"], {
    className: "nux-dot-tip",
    position: "middle right",
    noArrow: true,
    focusOnMount: "container",
    getAnchorRect: getAnchorRect,
    role: "dialog",
    "aria-label": Object(external_this_wp_i18n_["__"])('Editor tips'),
    onClick: onClick
  }, Object(external_this_wp_element_["createElement"])("p", null, children), Object(external_this_wp_element_["createElement"])("p", null, Object(external_this_wp_element_["createElement"])(external_this_wp_components_["Button"], {
    isLink: true,
    onClick: onDismiss
  }, hasNextTip ? Object(external_this_wp_i18n_["__"])('See next tip') : Object(external_this_wp_i18n_["__"])('Got it'))), Object(external_this_wp_element_["createElement"])(external_this_wp_components_["IconButton"], {
    className: "nux-dot-tip__disable",
    icon: "no-alt",
    label: Object(external_this_wp_i18n_["__"])('Disable tips'),
    onClick: onDisable
  }));
}
/* harmony default export */ var dot_tip = (Object(external_this_wp_compose_["compose"])(Object(external_this_wp_data_["withSelect"])(function (select, _ref2) {
  var tipId = _ref2.tipId;

  var _select = select('core/nux'),
      isTipVisible = _select.isTipVisible,
      getAssociatedGuide = _select.getAssociatedGuide;

  var associatedGuide = getAssociatedGuide(tipId);
  return {
    isVisible: isTipVisible(tipId),
    hasNextTip: !!(associatedGuide && associatedGuide.nextTipId)
  };
}), Object(external_this_wp_data_["withDispatch"])(function (dispatch, _ref3) {
  var tipId = _ref3.tipId;

  var _dispatch = dispatch('core/nux'),
      dismissTip = _dispatch.dismissTip,
      disableTips = _dispatch.disableTips;

  return {
    onDismiss: function onDismiss() {
      dismissTip(tipId);
    },
    onDisable: function onDisable() {
      disableTips();
    }
  };
}))(DotTip));

// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/index.js
/**
 * Internal dependencies
 */




/***/ }),

/***/ "BsWD":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _unsupportedIterableToArray; });
/* harmony import */ var _babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("a3WO");

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return Object(_babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return Object(_babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
}

/***/ }),

/***/ "DSFK":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayWithHoles; });
function _arrayWithHoles(arr) {
  if (Array.isArray(arr)) return arr;
}

/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "K9lf":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["compose"]; }());

/***/ }),

/***/ "KQm4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _toConsumableArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js
var arrayLikeToArray = __webpack_require__("a3WO");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return Object(arrayLikeToArray["a" /* default */])(arr);
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
var iterableToArray = __webpack_require__("25BE");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
var unsupportedIterableToArray = __webpack_require__("BsWD");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js




function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || Object(iterableToArray["a" /* default */])(arr) || Object(unsupportedIterableToArray["a" /* default */])(arr) || _nonIterableSpread();
}

/***/ }),

/***/ "ODXe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _slicedToArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithHoles.js
var arrayWithHoles = __webpack_require__("DSFK");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArrayLimit.js
function _iterableToArrayLimit(arr, i) {
  if (typeof Symbol === "undefined" || !(Symbol.iterator in Object(arr))) return;
  var _arr = [];
  var _n = true;
  var _d = false;
  var _e = undefined;

  try {
    for (var _i = arr[Symbol.iterator](), _s; !(_n = (_s = _i.next()).done); _n = true) {
      _arr.push(_s.value);

      if (i && _arr.length === i) break;
    }
  } catch (err) {
    _d = true;
    _e = err;
  } finally {
    try {
      if (!_n && _i["return"] != null) _i["return"]();
    } finally {
      if (_d) throw _e;
    }
  }

  return _arr;
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
var unsupportedIterableToArray = __webpack_require__("BsWD");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableRest.js
var nonIterableRest = __webpack_require__("PYwp");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js




function _slicedToArray(arr, i) {
  return Object(arrayWithHoles["a" /* default */])(arr) || _iterableToArrayLimit(arr, i) || Object(unsupportedIterableToArray["a" /* default */])(arr, i) || Object(nonIterableRest["a" /* default */])();
}

/***/ }),

/***/ "PYwp":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _nonIterableRest; });
function _nonIterableRest() {
  throw new TypeError("Invalid attempt to destructure non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ "a3WO":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayLikeToArray; });
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

/***/ }),

/***/ "pPDe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";


var LEAF_KEY, hasWeakMap;

/**
 * Arbitrary value used as key for referencing cache object in WeakMap tree.
 *
 * @type {Object}
 */
LEAF_KEY = {};

/**
 * Whether environment supports WeakMap.
 *
 * @type {boolean}
 */
hasWeakMap = typeof WeakMap !== 'undefined';

/**
 * Returns the first argument as the sole entry in an array.
 *
 * @param {*} value Value to return.
 *
 * @return {Array} Value returned as entry in array.
 */
function arrayOf( value ) {
	return [ value ];
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
<<<<<<< HEAD
function isObjectLike(value) {
	return !!value && 'object' === typeof value;
=======
function isObjectLike( value ) {
	return !! value && 'object' === typeof value;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
}

/**
 * Creates and returns a new cache object.
 *
<<<<<<< HEAD
 * @return {Cache} Cache object.
 */
function createCache() {
	/** @type {Cache} */
	var cache = {
		clear: function () {
=======
 * @return {Object} Cache object.
 */
function createCache() {
	var cache = {
		clear: function() {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
<<<<<<< HEAD
 * @param {*[]} a First array.
 * @param {*[]} b Second array.
=======
 * @param {Array}  a         First array.
 * @param {Array}  b         Second array.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
<<<<<<< HEAD
function isShallowEqual(a, b, fromIndex) {
	var i;

	if (a.length !== b.length) {
		return false;
	}

	for (i = fromIndex; i < a.length; i++) {
		if (a[i] !== b[i]) {
=======
function isShallowEqual( a, b, fromIndex ) {
	var i;

	if ( a.length !== b.length ) {
		return false;
	}

	for ( i = fromIndex; i < a.length; i++ ) {
		if ( a[ i ] !== b[ i ] ) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			return false;
		}
	}

	return true;
}

/**
 * Returns a memoized selector function. The getDependants function argument is
 * called before the memoized selector and is expected to return an immutable
 * reference or array of references on which the selector depends for computing
 * its own return value. The memoize cache is preserved only as long as those
 * dependant references remain the same. If getDependants returns a different
 * reference(s), the cache is cleared and the selector value regenerated.
 *
<<<<<<< HEAD
 * @template {(...args: *[]) => *} S
 *
 * @param {S} selector Selector function.
 * @param {GetDependants=} getDependants Dependant getter returning an array of
 * references used in cache bust consideration.
 */
/* harmony default export */ function rememo(selector, getDependants) {
	/** @type {WeakMap<*,*>} */
	var rootCache;

	/** @type {GetDependants} */
	var normalizedGetDependants = getDependants ? getDependants : arrayOf;
=======
 * @param {Function} selector      Selector function.
 * @param {Function} getDependants Dependant getter returning an immutable
 *                                 reference or array of reference used in
 *                                 cache bust consideration.
 *
 * @return {Function} Memoized selector.
 */
/* harmony default export */ __webpack_exports__["a"] = (function( selector, getDependants ) {
	var rootCache, getCache;

	// Use object source as dependant if getter not provided
	if ( ! getDependants ) {
		getDependants = arrayOf;
	}

	/**
	 * Returns the root cache. If WeakMap is supported, this is assigned to the
	 * root WeakMap cache set, otherwise it is a shared instance of the default
	 * cache object.
	 *
	 * @return {(WeakMap|Object)} Root cache object.
	 */
	function getRootCache() {
		return rootCache;
	}
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

	/**
	 * Returns the cache for a given dependants array. When possible, a WeakMap
	 * will be used to create a unique cache for each set of dependants. This
	 * is feasible due to the nature of WeakMap in allowing garbage collection
	 * to occur on entries where the key object is no longer referenced. Since
	 * WeakMap requires the key to be an object, this is only possible when the
	 * dependant is object-like. The root cache is created as a hierarchy where
	 * each top-level key is the first entry in a dependants set, the value a
	 * WeakMap where each key is the next dependant, and so on. This continues
	 * so long as the dependants are object-like. If no dependants are object-
	 * like, then the cache is shared across all invocations.
	 *
	 * @see isObjectLike
	 *
<<<<<<< HEAD
	 * @param {*[]} dependants Selector dependants.
	 *
	 * @return {Cache} Cache object.
	 */
	function getCache(dependants) {
		var caches = rootCache,
			isUniqueByDependants = true,
			i,
			dependant,
			map,
			cache;

		for (i = 0; i < dependants.length; i++) {
			dependant = dependants[i];

			// Can only compose WeakMap from object-like key.
			if (!isObjectLike(dependant)) {
=======
	 * @param {Array} dependants Selector dependants.
	 *
	 * @return {Object} Cache object.
	 */
	function getWeakMapCache( dependants ) {
		var caches = rootCache,
			isUniqueByDependants = true,
			i, dependant, map, cache;

		for ( i = 0; i < dependants.length; i++ ) {
			dependant = dependants[ i ];

			// Can only compose WeakMap from object-like key.
			if ( ! isObjectLike( dependant ) ) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
<<<<<<< HEAD
			if (caches.has(dependant)) {
				// Traverse into nested WeakMap.
				caches = caches.get(dependant);
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set(dependant, map);
=======
			if ( caches.has( dependant ) ) {
				// Traverse into nested WeakMap.
				caches = caches.get( dependant );
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set( dependant, map );
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
<<<<<<< HEAD
		if (!caches.has(LEAF_KEY)) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set(LEAF_KEY, cache);
		}

		return caches.get(LEAF_KEY);
	}

=======
		if ( ! caches.has( LEAF_KEY ) ) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set( LEAF_KEY, cache );
		}

		return caches.get( LEAF_KEY );
	}

	// Assign cache handler by availability of WeakMap
	getCache = hasWeakMap ? getWeakMapCache : getRootCache;

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	/**
	 * Resets root memoization cache.
	 */
	function clear() {
<<<<<<< HEAD
		rootCache = new WeakMap();
	}

	/* eslint-disable jsdoc/check-param-names */
=======
		rootCache = hasWeakMap ? new WeakMap() : createCache();
	}

	// eslint-disable-next-line jsdoc/check-param-names
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
<<<<<<< HEAD
	 * @param {*}    source    Source object for derivation.
	 * @param {...*} extraArgs Additional arguments to pass to selector.
	 *
	 * @return {*} Selector result.
	 */
	/* eslint-enable jsdoc/check-param-names */
	function callSelector(/* source, ...extraArgs */) {
		var len = arguments.length,
			cache,
			node,
			i,
			args,
			dependants;

		// Create copy of arguments (avoid leaking deoptimization).
		args = new Array(len);
		for (i = 0; i < len; i++) {
			args[i] = arguments[i];
		}

		dependants = normalizedGetDependants.apply(null, args);
		cache = getCache(dependants);

		// If not guaranteed uniqueness by dependants (primitive type), shallow
		// compare against last dependants and, if references have changed,
		// destroy cache to recalculate result.
		if (!cache.isUniqueByDependants) {
			if (
				cache.lastDependants &&
				!isShallowEqual(dependants, cache.lastDependants, 0)
			) {
=======
	 * @param {Object} source    Source object for derivation.
	 * @param {...*}   extraArgs Additional arguments to pass to selector.
	 *
	 * @return {*} Selector result.
	 */
	function callSelector( /* source, ...extraArgs */ ) {
		var len = arguments.length,
			cache, node, i, args, dependants;

		// Create copy of arguments (avoid leaking deoptimization).
		args = new Array( len );
		for ( i = 0; i < len; i++ ) {
			args[ i ] = arguments[ i ];
		}

		dependants = getDependants.apply( null, args );
		cache = getCache( dependants );

		// If not guaranteed uniqueness by dependants (primitive type or lack
		// of WeakMap support), shallow compare against last dependants and, if
		// references have changed, destroy cache to recalculate result.
		if ( ! cache.isUniqueByDependants ) {
			if ( cache.lastDependants && ! isShallowEqual( dependants, cache.lastDependants, 0 ) ) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
<<<<<<< HEAD
		while (node) {
			// Check whether node arguments match arguments
			if (!isShallowEqual(node.args, args, 1)) {
=======
		while ( node ) {
			// Check whether node arguments match arguments
			if ( ! isShallowEqual( node.args, args, 1 ) ) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
<<<<<<< HEAD
			if (node !== cache.head) {
				// Adjust siblings to point to each other.
				/** @type {CacheNode} */ (node.prev).next = node.next;
				if (node.next) {
=======
			if ( node !== cache.head ) {
				// Adjust siblings to point to each other.
				node.prev.next = node.next;
				if ( node.next ) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
<<<<<<< HEAD
				/** @type {CacheNode} */ (cache.head).prev = node;
=======
				cache.head.prev = node;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

<<<<<<< HEAD
		node = /** @type {CacheNode} */ ({
			// Generate the result from original function
			val: selector.apply(null, args),
		});

		// Avoid including the source object in the cache.
		args[0] = null;
=======
		node = {
			// Generate the result from original function
			val: selector.apply( null, args ),
		};

		// Avoid including the source object in the cache.
		args[ 0 ] = null;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
<<<<<<< HEAD
		if (cache.head) {
=======
		if ( cache.head ) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

<<<<<<< HEAD
	callSelector.getDependants = normalizedGetDependants;
	callSelector.clear = clear;
	clear();

	return /** @type {S & EnhancedSelector} */ (callSelector);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/store/selectors.js
/**
 * External dependencies
 */

/**
 * An object containing information about a guide.
 *
 * @typedef {Object} NUXGuideInfo
 * @property {string[]} tipIds       Which tips the guide contains.
 * @property {?string}  currentTipId The guide's currently showing tip.
 * @property {?string}  nextTipId    The guide's next tip to show.
 */

/**
 * Returns an object describing the guide, if any, that the given tip is a part
 * of.
 *
 * @param {Object} state Global application state.
 * @param {string} tipId The tip to query.
 *
 * @return {?NUXGuideInfo} Information about the associated guide.
 */

const getAssociatedGuide = rememo((state, tipId) => {
  for (const tipIds of state.guides) {
    if (tipIds.includes(tipId)) {
      const nonDismissedTips = tipIds.filter(tId => !Object.keys(state.preferences.dismissedTips).includes(tId));
      const [currentTipId = null, nextTipId = null] = nonDismissedTips;
      return {
        tipIds,
        currentTipId,
        nextTipId
      };
    }
  }

  return null;
}, state => [state.guides, state.preferences.dismissedTips]);
/**
 * Determines whether or not the given tip is showing. Tips are hidden if they
 * are disabled, have been dismissed, or are not the current tip in any
 * guide that they have been added to.
 *
 * @param {Object} state Global application state.
 * @param {string} tipId The tip to query.
 *
 * @return {boolean} Whether or not the given tip is showing.
 */

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
/**
 * Returns whether or not tips are globally enabled.
 *
 * @param {Object} state Global application state.
 *
 * @return {boolean} Whether tips are globally enabled.
 */

function selectors_areTipsEnabled(state) {
  return state.preferences.areTipsEnabled;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




const STORE_NAME = 'core/nux';
/**
 * Store definition for the nux namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['preferences']
}); // Once we build a more generic persistence plugin that works across types of stores
// we'd be able to replace this with a register call.

(0,external_wp_data_namespaceObject.registerStore)(STORE_NAME, {
  reducer: reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject,
  persist: ['preferences']
});

;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// CONCATENATED MODULE: external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/icons/build-module/library/close.js


/**
 * WordPress dependencies
 */

const close_close = (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.SVG, {
  xmlns: "http://www.w3.org/2000/svg",
  viewBox: "0 0 24 24"
}, (0,external_wp_element_namespaceObject.createElement)(external_wp_primitives_namespaceObject.Path, {
  d: "M13 11.8l6.1-6.3-1-1-6.1 6.2-6.1-6.2-1 1 6.1 6.3-6.5 6.7 1 1 6.5-6.6 6.5 6.6 1-1z"
}));
/* harmony default export */ const library_close = (close_close);

;// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/components/dot-tip/index.js


/**
 * WordPress dependencies
 */






/**
 * Internal dependencies
 */



function onClick(event) {
  // Tips are often nested within buttons. We stop propagation so that clicking
  // on a tip doesn't result in the button being clicked.
  event.stopPropagation();
}

function DotTip({
  position = 'middle right',
  children,
  isVisible,
  hasNextTip,
  onDismiss,
  onDisable
}) {
  const anchorParent = (0,external_wp_element_namespaceObject.useRef)(null);
  const onFocusOutsideCallback = (0,external_wp_element_namespaceObject.useCallback)(event => {
    if (!anchorParent.current) {
      return;
    }

    if (anchorParent.current.contains(event.relatedTarget)) {
      return;
    }

    onDisable();
  }, [onDisable, anchorParent]);

  if (!isVisible) {
    return null;
  }

  return (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Popover, {
    className: "nux-dot-tip",
    position: position,
    focusOnMount: true,
    role: "dialog",
    "aria-label": (0,external_wp_i18n_namespaceObject.__)('Editor tips'),
    onClick: onClick,
    onFocusOutside: onFocusOutsideCallback
  }, (0,external_wp_element_namespaceObject.createElement)("p", null, children), (0,external_wp_element_namespaceObject.createElement)("p", null, (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    variant: "link",
    onClick: onDismiss
  }, hasNextTip ? (0,external_wp_i18n_namespaceObject.__)('See next tip') : (0,external_wp_i18n_namespaceObject.__)('Got it'))), (0,external_wp_element_namespaceObject.createElement)(external_wp_components_namespaceObject.Button, {
    className: "nux-dot-tip__disable",
    icon: library_close,
    label: (0,external_wp_i18n_namespaceObject.__)('Disable tips'),
    onClick: onDisable
  }));
}
/* harmony default export */ const dot_tip = ((0,external_wp_compose_namespaceObject.compose)((0,external_wp_data_namespaceObject.withSelect)((select, {
  tipId
}) => {
  const {
    isTipVisible,
    getAssociatedGuide
  } = select(store);
  const associatedGuide = getAssociatedGuide(tipId);
  return {
    isVisible: isTipVisible(tipId),
    hasNextTip: !!(associatedGuide && associatedGuide.nextTipId)
  };
}), (0,external_wp_data_namespaceObject.withDispatch)((dispatch, {
  tipId
}) => {
  const {
    dismissTip,
    disableTips
  } = dispatch(store);
  return {
    onDismiss() {
      dismissTip(tipId);
    },

    onDisable() {
      disableTips();
    }

  };
}))(DotTip));

;// CONCATENATED MODULE: ./node_modules/@wordpress/nux/build-module/index.js
/**
 * WordPress dependencies
 */



external_wp_deprecated_default()('wp.nux', {
  since: '5.4',
  hint: 'wp.components.Guide can be used to show a user guide.',
  version: '6.2'
});

(window.wp = window.wp || {}).nux = __webpack_exports__;
/******/ })()
;
=======
	callSelector.getDependants = getDependants;
	callSelector.clear = clear;
	clear();

	return callSelector;
});


/***/ }),

/***/ "rePB":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _defineProperty; });
function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

/***/ }),

/***/ "tI+e":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["components"]; }());

/***/ }),

/***/ "vpQ4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectSpread; });
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("rePB");

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? Object(arguments[i]) : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(target, key, source[key]);
    });
  }

  return target;
}

/***/ })

/******/ });
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
