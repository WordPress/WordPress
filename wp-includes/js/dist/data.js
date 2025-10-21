/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 66:
/***/ ((module) => {



var isMergeableObject = function isMergeableObject(value) {
	return isNonNullObject(value)
		&& !isSpecial(value)
};

function isNonNullObject(value) {
	return !!value && typeof value === 'object'
}

function isSpecial(value) {
	var stringValue = Object.prototype.toString.call(value);

	return stringValue === '[object RegExp]'
		|| stringValue === '[object Date]'
		|| isReactElement(value)
}

// see https://github.com/facebook/react/blob/b5ac963fb791d1298e7f396236383bc955f916c1/src/isomorphic/classic/element/ReactElement.js#L21-L25
var canUseSymbol = typeof Symbol === 'function' && Symbol.for;
var REACT_ELEMENT_TYPE = canUseSymbol ? Symbol.for('react.element') : 0xeac7;

function isReactElement(value) {
	return value.$$typeof === REACT_ELEMENT_TYPE
}

function emptyTarget(val) {
	return Array.isArray(val) ? [] : {}
}

function cloneUnlessOtherwiseSpecified(value, options) {
	return (options.clone !== false && options.isMergeableObject(value))
		? deepmerge(emptyTarget(value), value, options)
		: value
}

function defaultArrayMerge(target, source, options) {
	return target.concat(source).map(function(element) {
		return cloneUnlessOtherwiseSpecified(element, options)
	})
}

function getMergeFunction(key, options) {
	if (!options.customMerge) {
		return deepmerge
	}
	var customMerge = options.customMerge(key);
	return typeof customMerge === 'function' ? customMerge : deepmerge
}

function getEnumerableOwnPropertySymbols(target) {
	return Object.getOwnPropertySymbols
		? Object.getOwnPropertySymbols(target).filter(function(symbol) {
			return Object.propertyIsEnumerable.call(target, symbol)
		})
		: []
}

function getKeys(target) {
	return Object.keys(target).concat(getEnumerableOwnPropertySymbols(target))
}

function propertyIsOnObject(object, property) {
	try {
		return property in object
	} catch(_) {
		return false
	}
}

// Protects from prototype poisoning and unexpected merging up the prototype chain.
function propertyIsUnsafe(target, key) {
	return propertyIsOnObject(target, key) // Properties are safe to merge if they don't exist in the target yet,
		&& !(Object.hasOwnProperty.call(target, key) // unsafe if they exist up the prototype chain,
			&& Object.propertyIsEnumerable.call(target, key)) // and also unsafe if they're nonenumerable.
}

function mergeObject(target, source, options) {
	var destination = {};
	if (options.isMergeableObject(target)) {
		getKeys(target).forEach(function(key) {
			destination[key] = cloneUnlessOtherwiseSpecified(target[key], options);
		});
	}
	getKeys(source).forEach(function(key) {
		if (propertyIsUnsafe(target, key)) {
			return
		}

		if (propertyIsOnObject(target, key) && options.isMergeableObject(source[key])) {
			destination[key] = getMergeFunction(key, options)(target[key], source[key], options);
		} else {
			destination[key] = cloneUnlessOtherwiseSpecified(source[key], options);
		}
	});
	return destination
}

function deepmerge(target, source, options) {
	options = options || {};
	options.arrayMerge = options.arrayMerge || defaultArrayMerge;
	options.isMergeableObject = options.isMergeableObject || isMergeableObject;
	// cloneUnlessOtherwiseSpecified is added to `options` so that custom arrayMerge()
	// implementations can use it. The caller may not replace it.
	options.cloneUnlessOtherwiseSpecified = cloneUnlessOtherwiseSpecified;

	var sourceIsArray = Array.isArray(source);
	var targetIsArray = Array.isArray(target);
	var sourceAndTargetTypesMatch = sourceIsArray === targetIsArray;

	if (!sourceAndTargetTypesMatch) {
		return cloneUnlessOtherwiseSpecified(source, options)
	} else if (sourceIsArray) {
		return options.arrayMerge(target, source, options)
	} else {
		return mergeObject(target, source, options)
	}
}

deepmerge.all = function deepmergeAll(array, options) {
	if (!Array.isArray(array)) {
		throw new Error('first argument should be an array')
	}

	return array.reduce(function(prev, next) {
		return deepmerge(prev, next, options)
	}, {})
};

var deepmerge_1 = deepmerge;

module.exports = deepmerge_1;


/***/ }),

/***/ 3249:
/***/ ((module) => {



function _typeof(obj) {
  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function (obj) {
      return typeof obj;
    };
  } else {
    _typeof = function (obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

function _defineProperties(target, props) {
  for (var i = 0; i < props.length; i++) {
    var descriptor = props[i];
    descriptor.enumerable = descriptor.enumerable || false;
    descriptor.configurable = true;
    if ("value" in descriptor) descriptor.writable = true;
    Object.defineProperty(target, descriptor.key, descriptor);
  }
}

function _createClass(Constructor, protoProps, staticProps) {
  if (protoProps) _defineProperties(Constructor.prototype, protoProps);
  if (staticProps) _defineProperties(Constructor, staticProps);
  return Constructor;
}

/**
 * Given an instance of EquivalentKeyMap, returns its internal value pair tuple
 * for a key, if one exists. The tuple members consist of the last reference
 * value for the key (used in efficient subsequent lookups) and the value
 * assigned for the key at the leaf node.
 *
 * @param {EquivalentKeyMap} instance EquivalentKeyMap instance.
 * @param {*} key                     The key for which to return value pair.
 *
 * @return {?Array} Value pair, if exists.
 */
function getValuePair(instance, key) {
  var _map = instance._map,
      _arrayTreeMap = instance._arrayTreeMap,
      _objectTreeMap = instance._objectTreeMap; // Map keeps a reference to the last object-like key used to set the
  // value, which can be used to shortcut immediately to the value.

  if (_map.has(key)) {
    return _map.get(key);
  } // Sort keys to ensure stable retrieval from tree.


  var properties = Object.keys(key).sort(); // Tree by type to avoid conflicts on numeric object keys, empty value.

  var map = Array.isArray(key) ? _arrayTreeMap : _objectTreeMap;

  for (var i = 0; i < properties.length; i++) {
    var property = properties[i];
    map = map.get(property);

    if (map === undefined) {
      return;
    }

    var propertyValue = key[property];
    map = map.get(propertyValue);

    if (map === undefined) {
      return;
    }
  }

  var valuePair = map.get('_ekm_value');

  if (!valuePair) {
    return;
  } // If reached, it implies that an object-like key was set with another
  // reference, so delete the reference and replace with the current.


  _map.delete(valuePair[0]);

  valuePair[0] = key;
  map.set('_ekm_value', valuePair);

  _map.set(key, valuePair);

  return valuePair;
}
/**
 * Variant of a Map object which enables lookup by equivalent (deeply equal)
 * object and array keys.
 */


var EquivalentKeyMap =
/*#__PURE__*/
function () {
  /**
   * Constructs a new instance of EquivalentKeyMap.
   *
   * @param {Iterable.<*>} iterable Initial pair of key, value for map.
   */
  function EquivalentKeyMap(iterable) {
    _classCallCheck(this, EquivalentKeyMap);

    this.clear();

    if (iterable instanceof EquivalentKeyMap) {
      // Map#forEach is only means of iterating with support for IE11.
      var iterablePairs = [];
      iterable.forEach(function (value, key) {
        iterablePairs.push([key, value]);
      });
      iterable = iterablePairs;
    }

    if (iterable != null) {
      for (var i = 0; i < iterable.length; i++) {
        this.set(iterable[i][0], iterable[i][1]);
      }
    }
  }
  /**
   * Accessor property returning the number of elements.
   *
   * @return {number} Number of elements.
   */


  _createClass(EquivalentKeyMap, [{
    key: "set",

    /**
     * Add or update an element with a specified key and value.
     *
     * @param {*} key   The key of the element to add.
     * @param {*} value The value of the element to add.
     *
     * @return {EquivalentKeyMap} Map instance.
     */
    value: function set(key, value) {
      // Shortcut non-object-like to set on internal Map.
      if (key === null || _typeof(key) !== 'object') {
        this._map.set(key, value);

        return this;
      } // Sort keys to ensure stable assignment into tree.


      var properties = Object.keys(key).sort();
      var valuePair = [key, value]; // Tree by type to avoid conflicts on numeric object keys, empty value.

      var map = Array.isArray(key) ? this._arrayTreeMap : this._objectTreeMap;

      for (var i = 0; i < properties.length; i++) {
        var property = properties[i];

        if (!map.has(property)) {
          map.set(property, new EquivalentKeyMap());
        }

        map = map.get(property);
        var propertyValue = key[property];

        if (!map.has(propertyValue)) {
          map.set(propertyValue, new EquivalentKeyMap());
        }

        map = map.get(propertyValue);
      } // If an _ekm_value exists, there was already an equivalent key. Before
      // overriding, ensure that the old key reference is removed from map to
      // avoid memory leak of accumulating equivalent keys. This is, in a
      // sense, a poor man's WeakMap, while still enabling iterability.


      var previousValuePair = map.get('_ekm_value');

      if (previousValuePair) {
        this._map.delete(previousValuePair[0]);
      }

      map.set('_ekm_value', valuePair);

      this._map.set(key, valuePair);

      return this;
    }
    /**
     * Returns a specified element.
     *
     * @param {*} key The key of the element to return.
     *
     * @return {?*} The element associated with the specified key or undefined
     *              if the key can't be found.
     */

  }, {
    key: "get",
    value: function get(key) {
      // Shortcut non-object-like to get from internal Map.
      if (key === null || _typeof(key) !== 'object') {
        return this._map.get(key);
      }

      var valuePair = getValuePair(this, key);

      if (valuePair) {
        return valuePair[1];
      }
    }
    /**
     * Returns a boolean indicating whether an element with the specified key
     * exists or not.
     *
     * @param {*} key The key of the element to test for presence.
     *
     * @return {boolean} Whether an element with the specified key exists.
     */

  }, {
    key: "has",
    value: function has(key) {
      if (key === null || _typeof(key) !== 'object') {
        return this._map.has(key);
      } // Test on the _presence_ of the pair, not its value, as even undefined
      // can be a valid member value for a key.


      return getValuePair(this, key) !== undefined;
    }
    /**
     * Removes the specified element.
     *
     * @param {*} key The key of the element to remove.
     *
     * @return {boolean} Returns true if an element existed and has been
     *                   removed, or false if the element does not exist.
     */

  }, {
    key: "delete",
    value: function _delete(key) {
      if (!this.has(key)) {
        return false;
      } // This naive implementation will leave orphaned child trees. A better
      // implementation should traverse and remove orphans.


      this.set(key, undefined);
      return true;
    }
    /**
     * Executes a provided function once per each key/value pair, in insertion
     * order.
     *
     * @param {Function} callback Function to execute for each element.
     * @param {*}        thisArg  Value to use as `this` when executing
     *                            `callback`.
     */

  }, {
    key: "forEach",
    value: function forEach(callback) {
      var _this = this;

      var thisArg = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this;

      this._map.forEach(function (value, key) {
        // Unwrap value from object-like value pair.
        if (key !== null && _typeof(key) === 'object') {
          value = value[1];
        }

        callback.call(thisArg, value, key, _this);
      });
    }
    /**
     * Removes all elements.
     */

  }, {
    key: "clear",
    value: function clear() {
      this._map = new Map();
      this._arrayTreeMap = new Map();
      this._objectTreeMap = new Map();
    }
  }, {
    key: "size",
    get: function get() {
      return this._map.size;
    }
  }]);

  return EquivalentKeyMap;
}();

module.exports = EquivalentKeyMap;


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
  AsyncModeProvider: () => (/* reexport */ context_context_default),
  RegistryConsumer: () => (/* reexport */ RegistryConsumer),
  RegistryProvider: () => (/* reexport */ context_default),
  combineReducers: () => (/* binding */ build_module_combineReducers),
  controls: () => (/* reexport */ controls),
  createReduxStore: () => (/* reexport */ createReduxStore),
  createRegistry: () => (/* reexport */ createRegistry),
  createRegistryControl: () => (/* reexport */ createRegistryControl),
  createRegistrySelector: () => (/* reexport */ createRegistrySelector),
  createSelector: () => (/* reexport */ rememo),
  dispatch: () => (/* reexport */ dispatch_dispatch),
  plugins: () => (/* reexport */ plugins_namespaceObject),
  register: () => (/* binding */ register),
  registerGenericStore: () => (/* binding */ registerGenericStore),
  registerStore: () => (/* binding */ registerStore),
  resolveSelect: () => (/* binding */ build_module_resolveSelect),
  select: () => (/* reexport */ select_select),
  subscribe: () => (/* binding */ subscribe),
  suspendSelect: () => (/* binding */ suspendSelect),
  use: () => (/* binding */ use),
  useDispatch: () => (/* reexport */ use_dispatch_default),
  useRegistry: () => (/* reexport */ useRegistry),
  useSelect: () => (/* reexport */ useSelect),
  useSuspenseSelect: () => (/* reexport */ useSuspenseSelect),
  withDispatch: () => (/* reexport */ with_dispatch_default),
  withRegistry: () => (/* reexport */ with_registry_default),
  withSelect: () => (/* reexport */ with_select_default)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/data/build-module/redux-store/metadata/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  countSelectorsByStatus: () => (countSelectorsByStatus),
  getCachedResolvers: () => (getCachedResolvers),
  getIsResolving: () => (getIsResolving),
  getResolutionError: () => (getResolutionError),
  getResolutionState: () => (getResolutionState),
  hasFinishedResolution: () => (hasFinishedResolution),
  hasResolutionFailed: () => (hasResolutionFailed),
  hasResolvingSelectors: () => (hasResolvingSelectors),
  hasStartedResolution: () => (hasStartedResolution),
  isResolving: () => (isResolving)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/data/build-module/redux-store/metadata/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  failResolution: () => (failResolution),
  failResolutions: () => (failResolutions),
  finishResolution: () => (finishResolution),
  finishResolutions: () => (finishResolutions),
  invalidateResolution: () => (invalidateResolution),
  invalidateResolutionForStore: () => (invalidateResolutionForStore),
  invalidateResolutionForStoreSelector: () => (invalidateResolutionForStoreSelector),
  startResolution: () => (startResolution),
  startResolutions: () => (startResolutions)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/data/build-module/plugins/index.js
var plugins_namespaceObject = {};
__webpack_require__.r(plugins_namespaceObject);
__webpack_require__.d(plugins_namespaceObject, {
  persistence: () => (persistence_default)
});

;// external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// ./node_modules/redux/dist/redux.mjs
// src/utils/formatProdErrorMessage.ts
function formatProdErrorMessage(code) {
  return `Minified Redux error #${code}; visit https://redux.js.org/Errors?code=${code} for the full message or use the non-minified dev environment for full errors. `;
}

// src/utils/symbol-observable.ts
var $$observable = /* @__PURE__ */ (() => typeof Symbol === "function" && Symbol.observable || "@@observable")();
var symbol_observable_default = $$observable;

// src/utils/actionTypes.ts
var randomString = () => Math.random().toString(36).substring(7).split("").join(".");
var ActionTypes = {
  INIT: `@@redux/INIT${/* @__PURE__ */ randomString()}`,
  REPLACE: `@@redux/REPLACE${/* @__PURE__ */ randomString()}`,
  PROBE_UNKNOWN_ACTION: () => `@@redux/PROBE_UNKNOWN_ACTION${randomString()}`
};
var actionTypes_default = ActionTypes;

// src/utils/isPlainObject.ts
function isPlainObject(obj) {
  if (typeof obj !== "object" || obj === null)
    return false;
  let proto = obj;
  while (Object.getPrototypeOf(proto) !== null) {
    proto = Object.getPrototypeOf(proto);
  }
  return Object.getPrototypeOf(obj) === proto || Object.getPrototypeOf(obj) === null;
}

// src/utils/kindOf.ts
function miniKindOf(val) {
  if (val === void 0)
    return "undefined";
  if (val === null)
    return "null";
  const type = typeof val;
  switch (type) {
    case "boolean":
    case "string":
    case "number":
    case "symbol":
    case "function": {
      return type;
    }
  }
  if (Array.isArray(val))
    return "array";
  if (isDate(val))
    return "date";
  if (isError(val))
    return "error";
  const constructorName = ctorName(val);
  switch (constructorName) {
    case "Symbol":
    case "Promise":
    case "WeakMap":
    case "WeakSet":
    case "Map":
    case "Set":
      return constructorName;
  }
  return Object.prototype.toString.call(val).slice(8, -1).toLowerCase().replace(/\s/g, "");
}
function ctorName(val) {
  return typeof val.constructor === "function" ? val.constructor.name : null;
}
function isError(val) {
  return val instanceof Error || typeof val.message === "string" && val.constructor && typeof val.constructor.stackTraceLimit === "number";
}
function isDate(val) {
  if (val instanceof Date)
    return true;
  return typeof val.toDateString === "function" && typeof val.getDate === "function" && typeof val.setDate === "function";
}
function kindOf(val) {
  let typeOfVal = typeof val;
  if (false) {}
  return typeOfVal;
}

// src/createStore.ts
function createStore(reducer, preloadedState, enhancer) {
  if (typeof reducer !== "function") {
    throw new Error( true ? formatProdErrorMessage(2) : 0);
  }
  if (typeof preloadedState === "function" && typeof enhancer === "function" || typeof enhancer === "function" && typeof arguments[3] === "function") {
    throw new Error( true ? formatProdErrorMessage(0) : 0);
  }
  if (typeof preloadedState === "function" && typeof enhancer === "undefined") {
    enhancer = preloadedState;
    preloadedState = void 0;
  }
  if (typeof enhancer !== "undefined") {
    if (typeof enhancer !== "function") {
      throw new Error( true ? formatProdErrorMessage(1) : 0);
    }
    return enhancer(createStore)(reducer, preloadedState);
  }
  let currentReducer = reducer;
  let currentState = preloadedState;
  let currentListeners = /* @__PURE__ */ new Map();
  let nextListeners = currentListeners;
  let listenerIdCounter = 0;
  let isDispatching = false;
  function ensureCanMutateNextListeners() {
    if (nextListeners === currentListeners) {
      nextListeners = /* @__PURE__ */ new Map();
      currentListeners.forEach((listener, key) => {
        nextListeners.set(key, listener);
      });
    }
  }
  function getState() {
    if (isDispatching) {
      throw new Error( true ? formatProdErrorMessage(3) : 0);
    }
    return currentState;
  }
  function subscribe(listener) {
    if (typeof listener !== "function") {
      throw new Error( true ? formatProdErrorMessage(4) : 0);
    }
    if (isDispatching) {
      throw new Error( true ? formatProdErrorMessage(5) : 0);
    }
    let isSubscribed = true;
    ensureCanMutateNextListeners();
    const listenerId = listenerIdCounter++;
    nextListeners.set(listenerId, listener);
    return function unsubscribe() {
      if (!isSubscribed) {
        return;
      }
      if (isDispatching) {
        throw new Error( true ? formatProdErrorMessage(6) : 0);
      }
      isSubscribed = false;
      ensureCanMutateNextListeners();
      nextListeners.delete(listenerId);
      currentListeners = null;
    };
  }
  function dispatch(action) {
    if (!isPlainObject(action)) {
      throw new Error( true ? formatProdErrorMessage(7) : 0);
    }
    if (typeof action.type === "undefined") {
      throw new Error( true ? formatProdErrorMessage(8) : 0);
    }
    if (typeof action.type !== "string") {
      throw new Error( true ? formatProdErrorMessage(17) : 0);
    }
    if (isDispatching) {
      throw new Error( true ? formatProdErrorMessage(9) : 0);
    }
    try {
      isDispatching = true;
      currentState = currentReducer(currentState, action);
    } finally {
      isDispatching = false;
    }
    const listeners = currentListeners = nextListeners;
    listeners.forEach((listener) => {
      listener();
    });
    return action;
  }
  function replaceReducer(nextReducer) {
    if (typeof nextReducer !== "function") {
      throw new Error( true ? formatProdErrorMessage(10) : 0);
    }
    currentReducer = nextReducer;
    dispatch({
      type: actionTypes_default.REPLACE
    });
  }
  function observable() {
    const outerSubscribe = subscribe;
    return {
      /**
       * The minimal observable subscription method.
       * @param observer Any object that can be used as an observer.
       * The observer object should have a `next` method.
       * @returns An object with an `unsubscribe` method that can
       * be used to unsubscribe the observable from the store, and prevent further
       * emission of values from the observable.
       */
      subscribe(observer) {
        if (typeof observer !== "object" || observer === null) {
          throw new Error( true ? formatProdErrorMessage(11) : 0);
        }
        function observeState() {
          const observerAsObserver = observer;
          if (observerAsObserver.next) {
            observerAsObserver.next(getState());
          }
        }
        observeState();
        const unsubscribe = outerSubscribe(observeState);
        return {
          unsubscribe
        };
      },
      [symbol_observable_default]() {
        return this;
      }
    };
  }
  dispatch({
    type: actionTypes_default.INIT
  });
  const store = {
    dispatch,
    subscribe,
    getState,
    replaceReducer,
    [symbol_observable_default]: observable
  };
  return store;
}
function legacy_createStore(reducer, preloadedState, enhancer) {
  return createStore(reducer, preloadedState, enhancer);
}

// src/utils/warning.ts
function warning(message) {
  if (typeof console !== "undefined" && typeof console.error === "function") {
    console.error(message);
  }
  try {
    throw new Error(message);
  } catch (e) {
  }
}

// src/combineReducers.ts
function getUnexpectedStateShapeWarningMessage(inputState, reducers, action, unexpectedKeyCache) {
  const reducerKeys = Object.keys(reducers);
  const argumentName = action && action.type === actionTypes_default.INIT ? "preloadedState argument passed to createStore" : "previous state received by the reducer";
  if (reducerKeys.length === 0) {
    return "Store does not have a valid reducer. Make sure the argument passed to combineReducers is an object whose values are reducers.";
  }
  if (!isPlainObject(inputState)) {
    return `The ${argumentName} has unexpected type of "${kindOf(inputState)}". Expected argument to be an object with the following keys: "${reducerKeys.join('", "')}"`;
  }
  const unexpectedKeys = Object.keys(inputState).filter((key) => !reducers.hasOwnProperty(key) && !unexpectedKeyCache[key]);
  unexpectedKeys.forEach((key) => {
    unexpectedKeyCache[key] = true;
  });
  if (action && action.type === actionTypes_default.REPLACE)
    return;
  if (unexpectedKeys.length > 0) {
    return `Unexpected ${unexpectedKeys.length > 1 ? "keys" : "key"} "${unexpectedKeys.join('", "')}" found in ${argumentName}. Expected to find one of the known reducer keys instead: "${reducerKeys.join('", "')}". Unexpected keys will be ignored.`;
  }
}
function assertReducerShape(reducers) {
  Object.keys(reducers).forEach((key) => {
    const reducer = reducers[key];
    const initialState = reducer(void 0, {
      type: actionTypes_default.INIT
    });
    if (typeof initialState === "undefined") {
      throw new Error( true ? formatProdErrorMessage(12) : 0);
    }
    if (typeof reducer(void 0, {
      type: actionTypes_default.PROBE_UNKNOWN_ACTION()
    }) === "undefined") {
      throw new Error( true ? formatProdErrorMessage(13) : 0);
    }
  });
}
function combineReducers(reducers) {
  const reducerKeys = Object.keys(reducers);
  const finalReducers = {};
  for (let i = 0; i < reducerKeys.length; i++) {
    const key = reducerKeys[i];
    if (false) {}
    if (typeof reducers[key] === "function") {
      finalReducers[key] = reducers[key];
    }
  }
  const finalReducerKeys = Object.keys(finalReducers);
  let unexpectedKeyCache;
  if (false) {}
  let shapeAssertionError;
  try {
    assertReducerShape(finalReducers);
  } catch (e) {
    shapeAssertionError = e;
  }
  return function combination(state = {}, action) {
    if (shapeAssertionError) {
      throw shapeAssertionError;
    }
    if (false) {}
    let hasChanged = false;
    const nextState = {};
    for (let i = 0; i < finalReducerKeys.length; i++) {
      const key = finalReducerKeys[i];
      const reducer = finalReducers[key];
      const previousStateForKey = state[key];
      const nextStateForKey = reducer(previousStateForKey, action);
      if (typeof nextStateForKey === "undefined") {
        const actionType = action && action.type;
        throw new Error( true ? formatProdErrorMessage(14) : 0);
      }
      nextState[key] = nextStateForKey;
      hasChanged = hasChanged || nextStateForKey !== previousStateForKey;
    }
    hasChanged = hasChanged || finalReducerKeys.length !== Object.keys(state).length;
    return hasChanged ? nextState : state;
  };
}

// src/bindActionCreators.ts
function bindActionCreator(actionCreator, dispatch) {
  return function(...args) {
    return dispatch(actionCreator.apply(this, args));
  };
}
function bindActionCreators(actionCreators, dispatch) {
  if (typeof actionCreators === "function") {
    return bindActionCreator(actionCreators, dispatch);
  }
  if (typeof actionCreators !== "object" || actionCreators === null) {
    throw new Error( true ? formatProdErrorMessage(16) : 0);
  }
  const boundActionCreators = {};
  for (const key in actionCreators) {
    const actionCreator = actionCreators[key];
    if (typeof actionCreator === "function") {
      boundActionCreators[key] = bindActionCreator(actionCreator, dispatch);
    }
  }
  return boundActionCreators;
}

// src/compose.ts
function compose(...funcs) {
  if (funcs.length === 0) {
    return (arg) => arg;
  }
  if (funcs.length === 1) {
    return funcs[0];
  }
  return funcs.reduce((a, b) => (...args) => a(b(...args)));
}

// src/applyMiddleware.ts
function applyMiddleware(...middlewares) {
  return (createStore2) => (reducer, preloadedState) => {
    const store = createStore2(reducer, preloadedState);
    let dispatch = () => {
      throw new Error( true ? formatProdErrorMessage(15) : 0);
    };
    const middlewareAPI = {
      getState: store.getState,
      dispatch: (action, ...args) => dispatch(action, ...args)
    };
    const chain = middlewares.map((middleware) => middleware(middlewareAPI));
    dispatch = compose(...chain)(store.dispatch);
    return {
      ...store,
      dispatch
    };
  };
}

// src/utils/isAction.ts
function isAction(action) {
  return isPlainObject(action) && "type" in action && typeof action.type === "string";
}

//# sourceMappingURL=redux.mjs.map
// EXTERNAL MODULE: ./node_modules/equivalent-key-map/equivalent-key-map.js
var equivalent_key_map = __webpack_require__(3249);
var equivalent_key_map_default = /*#__PURE__*/__webpack_require__.n(equivalent_key_map);
;// external ["wp","reduxRoutine"]
const external_wp_reduxRoutine_namespaceObject = window["wp"]["reduxRoutine"];
var external_wp_reduxRoutine_default = /*#__PURE__*/__webpack_require__.n(external_wp_reduxRoutine_namespaceObject);
;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// ./node_modules/@wordpress/data/build-module/redux-store/combine-reducers.js
function combine_reducers_combineReducers(reducers) {
  const keys = Object.keys(reducers);
  return function combinedReducer(state = {}, action) {
    const nextState = {};
    let hasChanged = false;
    for (const key of keys) {
      const reducer = reducers[key];
      const prevStateForKey = state[key];
      const nextStateForKey = reducer(prevStateForKey, action);
      nextState[key] = nextStateForKey;
      hasChanged = hasChanged || nextStateForKey !== prevStateForKey;
    }
    return hasChanged ? nextState : state;
  };
}


;// ./node_modules/@wordpress/data/build-module/factory.js
function createRegistrySelector(registrySelector) {
  const selectorsByRegistry = /* @__PURE__ */ new WeakMap();
  const wrappedSelector = (...args) => {
    let selector = selectorsByRegistry.get(wrappedSelector.registry);
    if (!selector) {
      selector = registrySelector(wrappedSelector.registry.select);
      selectorsByRegistry.set(wrappedSelector.registry, selector);
    }
    return selector(...args);
  };
  wrappedSelector.isRegistrySelector = true;
  return wrappedSelector;
}
function createRegistryControl(registryControl) {
  registryControl.isRegistryControl = true;
  return registryControl;
}


;// ./node_modules/@wordpress/data/build-module/controls.js

const SELECT = "@@data/SELECT";
const RESOLVE_SELECT = "@@data/RESOLVE_SELECT";
const DISPATCH = "@@data/DISPATCH";
function isObject(object) {
  return object !== null && typeof object === "object";
}
function controls_select(storeNameOrDescriptor, selectorName, ...args) {
  return {
    type: SELECT,
    storeKey: isObject(storeNameOrDescriptor) ? storeNameOrDescriptor.name : storeNameOrDescriptor,
    selectorName,
    args
  };
}
function resolveSelect(storeNameOrDescriptor, selectorName, ...args) {
  return {
    type: RESOLVE_SELECT,
    storeKey: isObject(storeNameOrDescriptor) ? storeNameOrDescriptor.name : storeNameOrDescriptor,
    selectorName,
    args
  };
}
function dispatch(storeNameOrDescriptor, actionName, ...args) {
  return {
    type: DISPATCH,
    storeKey: isObject(storeNameOrDescriptor) ? storeNameOrDescriptor.name : storeNameOrDescriptor,
    actionName,
    args
  };
}
const controls = { select: controls_select, resolveSelect, dispatch };
const builtinControls = {
  [SELECT]: createRegistryControl(
    (registry) => ({ storeKey, selectorName, args }) => registry.select(storeKey)[selectorName](...args)
  ),
  [RESOLVE_SELECT]: createRegistryControl(
    (registry) => ({ storeKey, selectorName, args }) => {
      const method = registry.select(storeKey)[selectorName].hasResolver ? "resolveSelect" : "select";
      return registry[method](storeKey)[selectorName](
        ...args
      );
    }
  ),
  [DISPATCH]: createRegistryControl(
    (registry) => ({ storeKey, actionName, args }) => registry.dispatch(storeKey)[actionName](...args)
  )
};


;// external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// ./node_modules/@wordpress/data/build-module/lock-unlock.js

const { lock, unlock } = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/data"
);


;// ./node_modules/is-promise/index.mjs
function isPromise(obj) {
  return !!obj && (typeof obj === 'object' || typeof obj === 'function') && typeof obj.then === 'function';
}

;// ./node_modules/@wordpress/data/build-module/promise-middleware.js

const promiseMiddleware = () => (next) => (action) => {
  if (isPromise(action)) {
    return action.then((resolvedAction) => {
      if (resolvedAction) {
        return next(resolvedAction);
      }
    });
  }
  return next(action);
};
var promise_middleware_default = promiseMiddleware;


;// ./node_modules/@wordpress/data/build-module/resolvers-cache-middleware.js
const createResolversCacheMiddleware = (registry, storeName) => () => (next) => (action) => {
  const resolvers = registry.select(storeName).getCachedResolvers();
  const resolverEntries = Object.entries(resolvers);
  resolverEntries.forEach(([selectorName, resolversByArgs]) => {
    const resolver = registry.stores[storeName]?.resolvers?.[selectorName];
    if (!resolver || !resolver.shouldInvalidate) {
      return;
    }
    resolversByArgs.forEach((value, args) => {
      if (value === void 0) {
        return;
      }
      if (value.status !== "finished" && value.status !== "error") {
        return;
      }
      if (!resolver.shouldInvalidate(action, ...args)) {
        return;
      }
      registry.dispatch(storeName).invalidateResolution(selectorName, args);
    });
  });
  return next(action);
};
var resolvers_cache_middleware_default = createResolversCacheMiddleware;


;// ./node_modules/@wordpress/data/build-module/redux-store/thunk-middleware.js
function createThunkMiddleware(args) {
  return () => (next) => (action) => {
    if (typeof action === "function") {
      return action(args);
    }
    return next(action);
  };
}


;// ./node_modules/@wordpress/data/build-module/redux-store/metadata/utils.js
const onSubKey = (actionProperty) => (reducer) => (state = {}, action) => {
  const key = action[actionProperty];
  if (key === void 0) {
    return state;
  }
  const nextKeyState = reducer(state[key], action);
  if (nextKeyState === state[key]) {
    return state;
  }
  return {
    ...state,
    [key]: nextKeyState
  };
};
function selectorArgsToStateKey(args) {
  if (args === void 0 || args === null) {
    return [];
  }
  const len = args.length;
  let idx = len;
  while (idx > 0 && args[idx - 1] === void 0) {
    idx--;
  }
  return idx === len ? args : args.slice(0, idx);
}


;// ./node_modules/@wordpress/data/build-module/redux-store/metadata/reducer.js


const subKeysIsResolved = onSubKey("selectorName")((state = new (equivalent_key_map_default())(), action) => {
  switch (action.type) {
    case "START_RESOLUTION": {
      const nextState = new (equivalent_key_map_default())(state);
      nextState.set(selectorArgsToStateKey(action.args), {
        status: "resolving"
      });
      return nextState;
    }
    case "FINISH_RESOLUTION": {
      const nextState = new (equivalent_key_map_default())(state);
      nextState.set(selectorArgsToStateKey(action.args), {
        status: "finished"
      });
      return nextState;
    }
    case "FAIL_RESOLUTION": {
      const nextState = new (equivalent_key_map_default())(state);
      nextState.set(selectorArgsToStateKey(action.args), {
        status: "error",
        error: action.error
      });
      return nextState;
    }
    case "START_RESOLUTIONS": {
      const nextState = new (equivalent_key_map_default())(state);
      for (const resolutionArgs of action.args) {
        nextState.set(selectorArgsToStateKey(resolutionArgs), {
          status: "resolving"
        });
      }
      return nextState;
    }
    case "FINISH_RESOLUTIONS": {
      const nextState = new (equivalent_key_map_default())(state);
      for (const resolutionArgs of action.args) {
        nextState.set(selectorArgsToStateKey(resolutionArgs), {
          status: "finished"
        });
      }
      return nextState;
    }
    case "FAIL_RESOLUTIONS": {
      const nextState = new (equivalent_key_map_default())(state);
      action.args.forEach((resolutionArgs, idx) => {
        const resolutionState = {
          status: "error",
          error: void 0
        };
        const error = action.errors[idx];
        if (error) {
          resolutionState.error = error;
        }
        nextState.set(
          selectorArgsToStateKey(resolutionArgs),
          resolutionState
        );
      });
      return nextState;
    }
    case "INVALIDATE_RESOLUTION": {
      const nextState = new (equivalent_key_map_default())(state);
      nextState.delete(selectorArgsToStateKey(action.args));
      return nextState;
    }
  }
  return state;
});
const isResolved = (state = {}, action) => {
  switch (action.type) {
    case "INVALIDATE_RESOLUTION_FOR_STORE":
      return {};
    case "INVALIDATE_RESOLUTION_FOR_STORE_SELECTOR": {
      if (action.selectorName in state) {
        const {
          [action.selectorName]: removedSelector,
          ...restState
        } = state;
        return restState;
      }
      return state;
    }
    case "START_RESOLUTION":
    case "FINISH_RESOLUTION":
    case "FAIL_RESOLUTION":
    case "START_RESOLUTIONS":
    case "FINISH_RESOLUTIONS":
    case "FAIL_RESOLUTIONS":
    case "INVALIDATE_RESOLUTION":
      return subKeysIsResolved(state, action);
  }
  return state;
};
var reducer_default = isResolved;


;// ./node_modules/rememo/rememo.js


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
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
function isObjectLike(value) {
	return !!value && 'object' === typeof value;
}

/**
 * Creates and returns a new cache object.
 *
 * @return {Cache} Cache object.
 */
function createCache() {
	/** @type {Cache} */
	var cache = {
		clear: function () {
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
 * @param {*[]} a First array.
 * @param {*[]} b Second array.
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
function isShallowEqual(a, b, fromIndex) {
	var i;

	if (a.length !== b.length) {
		return false;
	}

	for (i = fromIndex; i < a.length; i++) {
		if (a[i] !== b[i]) {
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
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
			if (caches.has(dependant)) {
				// Traverse into nested WeakMap.
				caches = caches.get(dependant);
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set(dependant, map);
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
		if (!caches.has(LEAF_KEY)) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set(LEAF_KEY, cache);
		}

		return caches.get(LEAF_KEY);
	}

	/**
	 * Resets root memoization cache.
	 */
	function clear() {
		rootCache = new WeakMap();
	}

	/* eslint-disable jsdoc/check-param-names */
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
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
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
		while (node) {
			// Check whether node arguments match arguments
			if (!isShallowEqual(node.args, args, 1)) {
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if (node !== cache.head) {
				// Adjust siblings to point to each other.
				/** @type {CacheNode} */ (node.prev).next = node.next;
				if (node.next) {
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
				/** @type {CacheNode} */ (cache.head).prev = node;
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		node = /** @type {CacheNode} */ ({
			// Generate the result from original function
			val: selector.apply(null, args),
		});

		// Avoid including the source object in the cache.
		args[0] = null;
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if (cache.head) {
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

	callSelector.getDependants = normalizedGetDependants;
	callSelector.clear = clear;
	clear();

	return /** @type {S & EnhancedSelector} */ (callSelector);
}

;// ./node_modules/@wordpress/data/build-module/redux-store/metadata/selectors.js



function getResolutionState(state, selectorName, args) {
  const map = state[selectorName];
  if (!map) {
    return;
  }
  return map.get(selectorArgsToStateKey(args));
}
function getIsResolving(state, selectorName, args) {
  external_wp_deprecated_default()("wp.data.select( store ).getIsResolving", {
    since: "6.6",
    version: "6.8",
    alternative: "wp.data.select( store ).getResolutionState"
  });
  const resolutionState = getResolutionState(state, selectorName, args);
  return resolutionState && resolutionState.status === "resolving";
}
function hasStartedResolution(state, selectorName, args) {
  return getResolutionState(state, selectorName, args) !== void 0;
}
function hasFinishedResolution(state, selectorName, args) {
  const status = getResolutionState(state, selectorName, args)?.status;
  return status === "finished" || status === "error";
}
function hasResolutionFailed(state, selectorName, args) {
  return getResolutionState(state, selectorName, args)?.status === "error";
}
function getResolutionError(state, selectorName, args) {
  const resolutionState = getResolutionState(state, selectorName, args);
  return resolutionState?.status === "error" ? resolutionState.error : null;
}
function isResolving(state, selectorName, args) {
  return getResolutionState(state, selectorName, args)?.status === "resolving";
}
function getCachedResolvers(state) {
  return state;
}
function hasResolvingSelectors(state) {
  return Object.values(state).some(
    (selectorState) => (
      /**
       * This uses the internal `_map` property of `EquivalentKeyMap` for
       * optimization purposes, since the `EquivalentKeyMap` implementation
       * does not support a `.values()` implementation.
       *
       * @see https://github.com/aduth/equivalent-key-map
       */
      Array.from(selectorState._map.values()).some(
        (resolution) => resolution[1]?.status === "resolving"
      )
    )
  );
}
const countSelectorsByStatus = rememo(
  (state) => {
    const selectorsByStatus = {};
    Object.values(state).forEach(
      (selectorState) => (
        /**
         * This uses the internal `_map` property of `EquivalentKeyMap` for
         * optimization purposes, since the `EquivalentKeyMap` implementation
         * does not support a `.values()` implementation.
         *
         * @see https://github.com/aduth/equivalent-key-map
         */
        Array.from(selectorState._map.values()).forEach(
          (resolution) => {
            const currentStatus = resolution[1]?.status ?? "error";
            if (!selectorsByStatus[currentStatus]) {
              selectorsByStatus[currentStatus] = 0;
            }
            selectorsByStatus[currentStatus]++;
          }
        )
      )
    );
    return selectorsByStatus;
  },
  (state) => [state]
);


;// ./node_modules/@wordpress/data/build-module/redux-store/metadata/actions.js
function startResolution(selectorName, args) {
  return {
    type: "START_RESOLUTION",
    selectorName,
    args
  };
}
function finishResolution(selectorName, args) {
  return {
    type: "FINISH_RESOLUTION",
    selectorName,
    args
  };
}
function failResolution(selectorName, args, error) {
  return {
    type: "FAIL_RESOLUTION",
    selectorName,
    args,
    error
  };
}
function startResolutions(selectorName, args) {
  return {
    type: "START_RESOLUTIONS",
    selectorName,
    args
  };
}
function finishResolutions(selectorName, args) {
  return {
    type: "FINISH_RESOLUTIONS",
    selectorName,
    args
  };
}
function failResolutions(selectorName, args, errors) {
  return {
    type: "FAIL_RESOLUTIONS",
    selectorName,
    args,
    errors
  };
}
function invalidateResolution(selectorName, args) {
  return {
    type: "INVALIDATE_RESOLUTION",
    selectorName,
    args
  };
}
function invalidateResolutionForStore() {
  return {
    type: "INVALIDATE_RESOLUTION_FOR_STORE"
  };
}
function invalidateResolutionForStoreSelector(selectorName) {
  return {
    type: "INVALIDATE_RESOLUTION_FOR_STORE_SELECTOR",
    selectorName
  };
}


;// ./node_modules/@wordpress/data/build-module/redux-store/index.js













const trimUndefinedValues = (array) => {
  const result = [...array];
  for (let i = result.length - 1; i >= 0; i--) {
    if (result[i] === void 0) {
      result.splice(i, 1);
    }
  }
  return result;
};
const mapValues = (obj, callback) => Object.fromEntries(
  Object.entries(obj ?? {}).map(([key, value]) => [
    key,
    callback(value, key)
  ])
);
const devToolsReplacer = (key, state) => {
  if (state instanceof Map) {
    return Object.fromEntries(state);
  }
  if (state instanceof window.HTMLElement) {
    return null;
  }
  return state;
};
function createResolversCache() {
  const cache = {};
  return {
    isRunning(selectorName, args) {
      return cache[selectorName] && cache[selectorName].get(trimUndefinedValues(args));
    },
    clear(selectorName, args) {
      if (cache[selectorName]) {
        cache[selectorName].delete(trimUndefinedValues(args));
      }
    },
    markAsRunning(selectorName, args) {
      if (!cache[selectorName]) {
        cache[selectorName] = new (equivalent_key_map_default())();
      }
      cache[selectorName].set(trimUndefinedValues(args), true);
    }
  };
}
function createBindingCache(getItem, bindItem) {
  const cache = /* @__PURE__ */ new WeakMap();
  return {
    get(itemName) {
      const item = getItem(itemName);
      if (!item) {
        return null;
      }
      let boundItem = cache.get(item);
      if (!boundItem) {
        boundItem = bindItem(item, itemName);
        cache.set(item, boundItem);
      }
      return boundItem;
    }
  };
}
function createPrivateProxy(publicItems, privateItems) {
  return new Proxy(publicItems, {
    get: (target, itemName) => privateItems.get(itemName) || Reflect.get(target, itemName)
  });
}
function createReduxStore(key, options) {
  const privateActions = {};
  const privateSelectors = {};
  const privateRegistrationFunctions = {
    privateActions,
    registerPrivateActions: (actions) => {
      Object.assign(privateActions, actions);
    },
    privateSelectors,
    registerPrivateSelectors: (selectors) => {
      Object.assign(privateSelectors, selectors);
    }
  };
  const storeDescriptor = {
    name: key,
    instantiate: (registry) => {
      const listeners = /* @__PURE__ */ new Set();
      const reducer = options.reducer;
      const thunkArgs = {
        registry,
        get dispatch() {
          return thunkDispatch;
        },
        get select() {
          return thunkSelect;
        },
        get resolveSelect() {
          return resolveSelectors;
        }
      };
      const store = instantiateReduxStore(
        key,
        options,
        registry,
        thunkArgs
      );
      lock(store, privateRegistrationFunctions);
      const resolversCache = createResolversCache();
      function bindAction(action) {
        return (...args) => Promise.resolve(store.dispatch(action(...args)));
      }
      const actions = {
        ...mapValues(actions_namespaceObject, bindAction),
        ...mapValues(options.actions, bindAction)
      };
      const allActions = createPrivateProxy(
        actions,
        createBindingCache(
          (name) => privateActions[name],
          bindAction
        )
      );
      const thunkDispatch = new Proxy(
        (action) => store.dispatch(action),
        { get: (target, name) => allActions[name] }
      );
      lock(actions, allActions);
      const resolvers = options.resolvers ? mapValues(options.resolvers, mapResolver) : {};
      function bindSelector(selector, selectorName) {
        if (selector.isRegistrySelector) {
          selector.registry = registry;
        }
        const boundSelector = (...args) => {
          args = normalize(selector, args);
          const state = store.__unstableOriginalGetState();
          if (selector.isRegistrySelector) {
            selector.registry = registry;
          }
          return selector(state.root, ...args);
        };
        boundSelector.__unstableNormalizeArgs = selector.__unstableNormalizeArgs;
        const resolver = resolvers[selectorName];
        if (!resolver) {
          boundSelector.hasResolver = false;
          return boundSelector;
        }
        return mapSelectorWithResolver(
          boundSelector,
          selectorName,
          resolver,
          store,
          resolversCache,
          boundMetadataSelectors
        );
      }
      function bindMetadataSelector(metaDataSelector) {
        const boundSelector = (selectorName, selectorArgs, ...args) => {
          if (selectorName) {
            const targetSelector = options.selectors?.[selectorName];
            if (targetSelector) {
              selectorArgs = normalize(
                targetSelector,
                selectorArgs
              );
            }
          }
          const state = store.__unstableOriginalGetState();
          return metaDataSelector(
            state.metadata,
            selectorName,
            selectorArgs,
            ...args
          );
        };
        boundSelector.hasResolver = false;
        return boundSelector;
      }
      const boundMetadataSelectors = mapValues(
        selectors_namespaceObject,
        bindMetadataSelector
      );
      const boundSelectors = mapValues(options.selectors, bindSelector);
      const selectors = {
        ...boundMetadataSelectors,
        ...boundSelectors
      };
      const boundPrivateSelectors = createBindingCache(
        (name) => privateSelectors[name],
        bindSelector
      );
      const allSelectors = createPrivateProxy(
        selectors,
        boundPrivateSelectors
      );
      for (const selectorName of Object.keys(privateSelectors)) {
        boundPrivateSelectors.get(selectorName);
      }
      const thunkSelect = new Proxy(
        (selector) => selector(store.__unstableOriginalGetState()),
        { get: (target, name) => allSelectors[name] }
      );
      lock(selectors, allSelectors);
      const bindResolveSelector = mapResolveSelector(
        store,
        boundMetadataSelectors
      );
      const resolveSelectors = mapValues(
        boundSelectors,
        bindResolveSelector
      );
      const allResolveSelectors = createPrivateProxy(
        resolveSelectors,
        createBindingCache(
          (name) => boundPrivateSelectors.get(name),
          bindResolveSelector
        )
      );
      lock(resolveSelectors, allResolveSelectors);
      const bindSuspendSelector = mapSuspendSelector(
        store,
        boundMetadataSelectors
      );
      const suspendSelectors = {
        ...boundMetadataSelectors,
        // no special suspense behavior
        ...mapValues(boundSelectors, bindSuspendSelector)
      };
      const allSuspendSelectors = createPrivateProxy(
        suspendSelectors,
        createBindingCache(
          (name) => boundPrivateSelectors.get(name),
          bindSuspendSelector
        )
      );
      lock(suspendSelectors, allSuspendSelectors);
      const getSelectors = () => selectors;
      const getActions = () => actions;
      const getResolveSelectors = () => resolveSelectors;
      const getSuspendSelectors = () => suspendSelectors;
      store.__unstableOriginalGetState = store.getState;
      store.getState = () => store.__unstableOriginalGetState().root;
      const subscribe = store && ((listener) => {
        listeners.add(listener);
        return () => listeners.delete(listener);
      });
      let lastState = store.__unstableOriginalGetState();
      store.subscribe(() => {
        const state = store.__unstableOriginalGetState();
        const hasChanged = state !== lastState;
        lastState = state;
        if (hasChanged) {
          for (const listener of listeners) {
            listener();
          }
        }
      });
      return {
        reducer,
        store,
        actions,
        selectors,
        resolvers,
        getSelectors,
        getResolveSelectors,
        getSuspendSelectors,
        getActions,
        subscribe
      };
    }
  };
  lock(storeDescriptor, privateRegistrationFunctions);
  return storeDescriptor;
}
function instantiateReduxStore(key, options, registry, thunkArgs) {
  const controls = {
    ...options.controls,
    ...builtinControls
  };
  const normalizedControls = mapValues(
    controls,
    (control) => control.isRegistryControl ? control(registry) : control
  );
  const middlewares = [
    resolvers_cache_middleware_default(registry, key),
    promise_middleware_default,
    external_wp_reduxRoutine_default()(normalizedControls),
    createThunkMiddleware(thunkArgs)
  ];
  const enhancers = [applyMiddleware(...middlewares)];
  if (typeof window !== "undefined" && window.__REDUX_DEVTOOLS_EXTENSION__) {
    enhancers.push(
      window.__REDUX_DEVTOOLS_EXTENSION__({
        name: key,
        instanceId: key,
        serialize: {
          replacer: devToolsReplacer
        }
      })
    );
  }
  const { reducer, initialState } = options;
  const enhancedReducer = combine_reducers_combineReducers({
    metadata: reducer_default,
    root: reducer
  });
  return createStore(
    enhancedReducer,
    { root: initialState },
    (0,external_wp_compose_namespaceObject.compose)(enhancers)
  );
}
function mapResolveSelector(store, boundMetadataSelectors) {
  return (selector, selectorName) => {
    if (!selector.hasResolver) {
      return async (...args) => selector.apply(null, args);
    }
    return (...args) => new Promise((resolve, reject) => {
      const hasFinished = () => {
        return boundMetadataSelectors.hasFinishedResolution(
          selectorName,
          args
        );
      };
      const finalize = (result2) => {
        const hasFailed = boundMetadataSelectors.hasResolutionFailed(
          selectorName,
          args
        );
        if (hasFailed) {
          const error = boundMetadataSelectors.getResolutionError(
            selectorName,
            args
          );
          reject(error);
        } else {
          resolve(result2);
        }
      };
      const getResult = () => selector.apply(null, args);
      const result = getResult();
      if (hasFinished()) {
        return finalize(result);
      }
      const unsubscribe = store.subscribe(() => {
        if (hasFinished()) {
          unsubscribe();
          finalize(getResult());
        }
      });
    });
  };
}
function mapSuspendSelector(store, boundMetadataSelectors) {
  return (selector, selectorName) => {
    if (!selector.hasResolver) {
      return selector;
    }
    return (...args) => {
      const result = selector.apply(null, args);
      if (boundMetadataSelectors.hasFinishedResolution(
        selectorName,
        args
      )) {
        if (boundMetadataSelectors.hasResolutionFailed(
          selectorName,
          args
        )) {
          throw boundMetadataSelectors.getResolutionError(
            selectorName,
            args
          );
        }
        return result;
      }
      throw new Promise((resolve) => {
        const unsubscribe = store.subscribe(() => {
          if (boundMetadataSelectors.hasFinishedResolution(
            selectorName,
            args
          )) {
            resolve();
            unsubscribe();
          }
        });
      });
    };
  };
}
function mapResolver(resolver) {
  if (resolver.fulfill) {
    return resolver;
  }
  return {
    ...resolver,
    // Copy the enumerable properties of the resolver function.
    fulfill: resolver
    // Add the fulfill method.
  };
}
function mapSelectorWithResolver(selector, selectorName, resolver, store, resolversCache, boundMetadataSelectors) {
  function fulfillSelector(args) {
    const state = store.getState();
    if (resolversCache.isRunning(selectorName, args) || typeof resolver.isFulfilled === "function" && resolver.isFulfilled(state, ...args)) {
      return;
    }
    if (boundMetadataSelectors.hasStartedResolution(selectorName, args)) {
      return;
    }
    resolversCache.markAsRunning(selectorName, args);
    setTimeout(async () => {
      resolversCache.clear(selectorName, args);
      store.dispatch(
        startResolution(selectorName, args)
      );
      try {
        const action = resolver.fulfill(...args);
        if (action) {
          await store.dispatch(action);
        }
        store.dispatch(
          finishResolution(selectorName, args)
        );
      } catch (error) {
        store.dispatch(
          failResolution(selectorName, args, error)
        );
      }
    }, 0);
  }
  const selectorResolver = (...args) => {
    args = normalize(selector, args);
    fulfillSelector(args);
    return selector(...args);
  };
  selectorResolver.hasResolver = true;
  return selectorResolver;
}
function normalize(selector, args) {
  if (selector.__unstableNormalizeArgs && typeof selector.__unstableNormalizeArgs === "function" && args?.length) {
    return selector.__unstableNormalizeArgs(args);
  }
  return args;
}


;// ./node_modules/@wordpress/data/build-module/store/index.js
const coreDataStore = {
  name: "core/data",
  instantiate(registry) {
    const getCoreDataSelector = (selectorName) => (key, ...args) => {
      return registry.select(key)[selectorName](...args);
    };
    const getCoreDataAction = (actionName) => (key, ...args) => {
      return registry.dispatch(key)[actionName](...args);
    };
    return {
      getSelectors() {
        return Object.fromEntries(
          [
            "getIsResolving",
            "hasStartedResolution",
            "hasFinishedResolution",
            "isResolving",
            "getCachedResolvers"
          ].map((selectorName) => [
            selectorName,
            getCoreDataSelector(selectorName)
          ])
        );
      },
      getActions() {
        return Object.fromEntries(
          [
            "startResolution",
            "finishResolution",
            "invalidateResolution",
            "invalidateResolutionForStore",
            "invalidateResolutionForStoreSelector"
          ].map((actionName) => [
            actionName,
            getCoreDataAction(actionName)
          ])
        );
      },
      subscribe() {
        return () => () => {
        };
      }
    };
  }
};
var store_default = coreDataStore;


;// ./node_modules/@wordpress/data/build-module/utils/emitter.js
function createEmitter() {
  let isPaused = false;
  let isPending = false;
  const listeners = /* @__PURE__ */ new Set();
  const notifyListeners = () => (
    // We use Array.from to clone the listeners Set
    // This ensures that we don't run a listener
    // that was added as a response to another listener.
    Array.from(listeners).forEach((listener) => listener())
  );
  return {
    get isPaused() {
      return isPaused;
    },
    subscribe(listener) {
      listeners.add(listener);
      return () => listeners.delete(listener);
    },
    pause() {
      isPaused = true;
    },
    resume() {
      isPaused = false;
      if (isPending) {
        isPending = false;
        notifyListeners();
      }
    },
    emit() {
      if (isPaused) {
        isPending = true;
        return;
      }
      notifyListeners();
    }
  };
}


;// ./node_modules/@wordpress/data/build-module/registry.js





function getStoreName(storeNameOrDescriptor) {
  return typeof storeNameOrDescriptor === "string" ? storeNameOrDescriptor : storeNameOrDescriptor.name;
}
function createRegistry(storeConfigs = {}, parent = null) {
  const stores = {};
  const emitter = createEmitter();
  let listeningStores = null;
  function globalListener() {
    emitter.emit();
  }
  const subscribe = (listener, storeNameOrDescriptor) => {
    if (!storeNameOrDescriptor) {
      return emitter.subscribe(listener);
    }
    const storeName = getStoreName(storeNameOrDescriptor);
    const store = stores[storeName];
    if (store) {
      return store.subscribe(listener);
    }
    if (!parent) {
      return emitter.subscribe(listener);
    }
    return parent.subscribe(listener, storeNameOrDescriptor);
  };
  function select(storeNameOrDescriptor) {
    const storeName = getStoreName(storeNameOrDescriptor);
    listeningStores?.add(storeName);
    const store = stores[storeName];
    if (store) {
      return store.getSelectors();
    }
    return parent?.select(storeName);
  }
  function __unstableMarkListeningStores(callback, ref) {
    listeningStores = /* @__PURE__ */ new Set();
    try {
      return callback.call(this);
    } finally {
      ref.current = Array.from(listeningStores);
      listeningStores = null;
    }
  }
  function resolveSelect(storeNameOrDescriptor) {
    const storeName = getStoreName(storeNameOrDescriptor);
    listeningStores?.add(storeName);
    const store = stores[storeName];
    if (store) {
      return store.getResolveSelectors();
    }
    return parent && parent.resolveSelect(storeName);
  }
  function suspendSelect(storeNameOrDescriptor) {
    const storeName = getStoreName(storeNameOrDescriptor);
    listeningStores?.add(storeName);
    const store = stores[storeName];
    if (store) {
      return store.getSuspendSelectors();
    }
    return parent && parent.suspendSelect(storeName);
  }
  function dispatch(storeNameOrDescriptor) {
    const storeName = getStoreName(storeNameOrDescriptor);
    const store = stores[storeName];
    if (store) {
      return store.getActions();
    }
    return parent && parent.dispatch(storeName);
  }
  function withPlugins(attributes) {
    return Object.fromEntries(
      Object.entries(attributes).map(([key, attribute]) => {
        if (typeof attribute !== "function") {
          return [key, attribute];
        }
        return [
          key,
          function() {
            return registry[key].apply(null, arguments);
          }
        ];
      })
    );
  }
  function registerStoreInstance(name, createStore) {
    if (stores[name]) {
      console.error('Store "' + name + '" is already registered.');
      return stores[name];
    }
    const store = createStore();
    if (typeof store.getSelectors !== "function") {
      throw new TypeError("store.getSelectors must be a function");
    }
    if (typeof store.getActions !== "function") {
      throw new TypeError("store.getActions must be a function");
    }
    if (typeof store.subscribe !== "function") {
      throw new TypeError("store.subscribe must be a function");
    }
    store.emitter = createEmitter();
    const currentSubscribe = store.subscribe;
    store.subscribe = (listener) => {
      const unsubscribeFromEmitter = store.emitter.subscribe(listener);
      const unsubscribeFromStore = currentSubscribe(() => {
        if (store.emitter.isPaused) {
          store.emitter.emit();
          return;
        }
        listener();
      });
      return () => {
        unsubscribeFromStore?.();
        unsubscribeFromEmitter?.();
      };
    };
    stores[name] = store;
    store.subscribe(globalListener);
    if (parent) {
      try {
        unlock(store.store).registerPrivateActions(
          unlock(parent).privateActionsOf(name)
        );
        unlock(store.store).registerPrivateSelectors(
          unlock(parent).privateSelectorsOf(name)
        );
      } catch (e) {
      }
    }
    return store;
  }
  function register(store) {
    registerStoreInstance(
      store.name,
      () => store.instantiate(registry)
    );
  }
  function registerGenericStore(name, store) {
    external_wp_deprecated_default()("wp.data.registerGenericStore", {
      since: "5.9",
      alternative: "wp.data.register( storeDescriptor )"
    });
    registerStoreInstance(name, () => store);
  }
  function registerStore(storeName, options) {
    if (!options.reducer) {
      throw new TypeError("Must specify store reducer");
    }
    const store = registerStoreInstance(
      storeName,
      () => createReduxStore(storeName, options).instantiate(registry)
    );
    return store.store;
  }
  function batch(callback) {
    if (emitter.isPaused) {
      callback();
      return;
    }
    emitter.pause();
    Object.values(stores).forEach((store) => store.emitter.pause());
    try {
      callback();
    } finally {
      emitter.resume();
      Object.values(stores).forEach(
        (store) => store.emitter.resume()
      );
    }
  }
  let registry = {
    batch,
    stores,
    namespaces: stores,
    // TODO: Deprecate/remove this.
    subscribe,
    select,
    resolveSelect,
    suspendSelect,
    dispatch,
    use,
    register,
    registerGenericStore,
    registerStore,
    __unstableMarkListeningStores
  };
  function use(plugin, options) {
    if (!plugin) {
      return;
    }
    registry = {
      ...registry,
      ...plugin(registry, options)
    };
    return registry;
  }
  registry.register(store_default);
  for (const [name, config] of Object.entries(storeConfigs)) {
    registry.register(createReduxStore(name, config));
  }
  if (parent) {
    parent.subscribe(globalListener);
  }
  const registryWithPlugins = withPlugins(registry);
  lock(registryWithPlugins, {
    privateActionsOf: (name) => {
      try {
        return unlock(stores[name].store).privateActions;
      } catch (e) {
        return {};
      }
    },
    privateSelectorsOf: (name) => {
      try {
        return unlock(stores[name].store).privateSelectors;
      } catch (e) {
        return {};
      }
    }
  });
  return registryWithPlugins;
}


;// ./node_modules/@wordpress/data/build-module/default-registry.js

var default_registry_default = createRegistry();


;// ./node_modules/is-plain-object/dist/is-plain-object.mjs
/*!
 * is-plain-object <https://github.com/jonschlinkert/is-plain-object>
 *
 * Copyright (c) 2014-2017, Jon Schlinkert.
 * Released under the MIT License.
 */

function is_plain_object_isObject(o) {
  return Object.prototype.toString.call(o) === '[object Object]';
}

function is_plain_object_isPlainObject(o) {
  var ctor,prot;

  if (is_plain_object_isObject(o) === false) return false;

  // If has modified constructor
  ctor = o.constructor;
  if (ctor === undefined) return true;

  // If has modified prototype
  prot = ctor.prototype;
  if (is_plain_object_isObject(prot) === false) return false;

  // If constructor does not have an Object-specific method
  if (prot.hasOwnProperty('isPrototypeOf') === false) {
    return false;
  }

  // Most likely a plain Object
  return true;
}



// EXTERNAL MODULE: ./node_modules/deepmerge/dist/cjs.js
var cjs = __webpack_require__(66);
var cjs_default = /*#__PURE__*/__webpack_require__.n(cjs);
;// ./node_modules/@wordpress/data/build-module/plugins/persistence/storage/object.js
let objectStorage;
const storage = {
  getItem(key) {
    if (!objectStorage || !objectStorage[key]) {
      return null;
    }
    return objectStorage[key];
  },
  setItem(key, value) {
    if (!objectStorage) {
      storage.clear();
    }
    objectStorage[key] = String(value);
  },
  clear() {
    objectStorage = /* @__PURE__ */ Object.create(null);
  }
};
var object_default = storage;


;// ./node_modules/@wordpress/data/build-module/plugins/persistence/storage/default.js

let default_storage;
try {
  default_storage = window.localStorage;
  default_storage.setItem("__wpDataTestLocalStorage", "");
  default_storage.removeItem("__wpDataTestLocalStorage");
} catch (error) {
  default_storage = object_default;
}
var default_default = default_storage;


;// ./node_modules/@wordpress/data/build-module/plugins/persistence/index.js




const DEFAULT_STORAGE = default_default;
const DEFAULT_STORAGE_KEY = "WP_DATA";
const withLazySameState = (reducer) => (state, action) => {
  if (action.nextState === state) {
    return state;
  }
  return reducer(state, action);
};
function createPersistenceInterface(options) {
  const { storage = DEFAULT_STORAGE, storageKey = DEFAULT_STORAGE_KEY } = options;
  let data;
  function getData() {
    if (data === void 0) {
      const persisted = storage.getItem(storageKey);
      if (persisted === null) {
        data = {};
      } else {
        try {
          data = JSON.parse(persisted);
        } catch (error) {
          data = {};
        }
      }
    }
    return data;
  }
  function setData(key, value) {
    data = { ...data, [key]: value };
    storage.setItem(storageKey, JSON.stringify(data));
  }
  return {
    get: getData,
    set: setData
  };
}
function persistencePlugin(registry, pluginOptions) {
  const persistence = createPersistenceInterface(pluginOptions);
  function createPersistOnChange(getState, storeName, keys) {
    let getPersistedState;
    if (Array.isArray(keys)) {
      const reducers = keys.reduce(
        (accumulator, key) => Object.assign(accumulator, {
          [key]: (state, action) => action.nextState[key]
        }),
        {}
      );
      getPersistedState = withLazySameState(
        build_module_combineReducers(reducers)
      );
    } else {
      getPersistedState = (state, action) => action.nextState;
    }
    let lastState = getPersistedState(void 0, {
      nextState: getState()
    });
    return () => {
      const state = getPersistedState(lastState, {
        nextState: getState()
      });
      if (state !== lastState) {
        persistence.set(storeName, state);
        lastState = state;
      }
    };
  }
  return {
    registerStore(storeName, options) {
      if (!options.persist) {
        return registry.registerStore(storeName, options);
      }
      const persistedState = persistence.get()[storeName];
      if (persistedState !== void 0) {
        let initialState = options.reducer(options.initialState, {
          type: "@@WP/PERSISTENCE_RESTORE"
        });
        if (is_plain_object_isPlainObject(initialState) && is_plain_object_isPlainObject(persistedState)) {
          initialState = cjs_default()(initialState, persistedState, {
            isMergeableObject: is_plain_object_isPlainObject
          });
        } else {
          initialState = persistedState;
        }
        options = {
          ...options,
          initialState
        };
      }
      const store = registry.registerStore(storeName, options);
      store.subscribe(
        createPersistOnChange(
          store.getState,
          storeName,
          options.persist
        )
      );
      return store;
    }
  };
}
persistencePlugin.__unstableMigrate = () => {
};
var persistence_default = persistencePlugin;


;// ./node_modules/@wordpress/data/build-module/plugins/index.js



;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// external ["wp","priorityQueue"]
const external_wp_priorityQueue_namespaceObject = window["wp"]["priorityQueue"];
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","isShallowEqual"]
const external_wp_isShallowEqual_namespaceObject = window["wp"]["isShallowEqual"];
var external_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_wp_isShallowEqual_namespaceObject);
;// ./node_modules/@wordpress/data/build-module/components/registry-provider/context.js


const Context = (0,external_wp_element_namespaceObject.createContext)(default_registry_default);
Context.displayName = "RegistryProviderContext";
const { Consumer, Provider } = Context;
const RegistryConsumer = Consumer;
var context_default = Provider;


;// ./node_modules/@wordpress/data/build-module/components/registry-provider/use-registry.js


function useRegistry() {
  return (0,external_wp_element_namespaceObject.useContext)(Context);
}


;// ./node_modules/@wordpress/data/build-module/components/async-mode-provider/context.js

const context_Context = (0,external_wp_element_namespaceObject.createContext)(false);
context_Context.displayName = "AsyncModeContext";
const { Consumer: context_Consumer, Provider: context_Provider } = context_Context;
const AsyncModeConsumer = (/* unused pure expression or super */ null && (context_Consumer));
var context_context_default = context_Provider;


;// ./node_modules/@wordpress/data/build-module/components/async-mode-provider/use-async-mode.js


function useAsyncMode() {
  return (0,external_wp_element_namespaceObject.useContext)(context_Context);
}


;// ./node_modules/@wordpress/data/build-module/components/use-select/index.js





const renderQueue = (0,external_wp_priorityQueue_namespaceObject.createQueue)();
function warnOnUnstableReference(a, b) {
  if (!a || !b) {
    return;
  }
  const keys = typeof a === "object" && typeof b === "object" ? Object.keys(a).filter((k) => a[k] !== b[k]) : [];
  console.warn(
    "The `useSelect` hook returns different values when called with the same state and parameters.\nThis can lead to unnecessary re-renders and performance issues if not fixed.\n\nNon-equal value keys: %s\n\n",
    keys.join(", ")
  );
}
function Store(registry, suspense) {
  const select = suspense ? registry.suspendSelect : registry.select;
  const queueContext = {};
  let lastMapSelect;
  let lastMapResult;
  let lastMapResultValid = false;
  let lastIsAsync;
  let subscriber;
  let didWarnUnstableReference;
  const storeStatesOnMount = /* @__PURE__ */ new Map();
  function getStoreState(name) {
    return registry.stores[name]?.store?.getState?.() ?? {};
  }
  const createSubscriber = (stores) => {
    const activeStores = [...stores];
    const activeSubscriptions = /* @__PURE__ */ new Set();
    function subscribe(listener) {
      if (lastMapResultValid) {
        for (const name of activeStores) {
          if (storeStatesOnMount.get(name) !== getStoreState(name)) {
            lastMapResultValid = false;
          }
        }
      }
      storeStatesOnMount.clear();
      const onStoreChange = () => {
        lastMapResultValid = false;
        listener();
      };
      const onChange = () => {
        if (lastIsAsync) {
          renderQueue.add(queueContext, onStoreChange);
        } else {
          onStoreChange();
        }
      };
      const unsubs = [];
      function subscribeStore(storeName) {
        unsubs.push(registry.subscribe(onChange, storeName));
      }
      for (const storeName of activeStores) {
        subscribeStore(storeName);
      }
      activeSubscriptions.add(subscribeStore);
      return () => {
        activeSubscriptions.delete(subscribeStore);
        for (const unsub of unsubs.values()) {
          unsub?.();
        }
        renderQueue.cancel(queueContext);
      };
    }
    function updateStores(newStores) {
      for (const newStore of newStores) {
        if (activeStores.includes(newStore)) {
          continue;
        }
        activeStores.push(newStore);
        for (const subscription of activeSubscriptions) {
          subscription(newStore);
        }
      }
    }
    return { subscribe, updateStores };
  };
  return (mapSelect, isAsync) => {
    function updateValue() {
      if (lastMapResultValid && mapSelect === lastMapSelect) {
        return lastMapResult;
      }
      const listeningStores = { current: null };
      const mapResult = registry.__unstableMarkListeningStores(
        () => mapSelect(select, registry),
        listeningStores
      );
      if (true) {
        if (!didWarnUnstableReference) {
          const secondMapResult = mapSelect(select, registry);
          if (!external_wp_isShallowEqual_default()(mapResult, secondMapResult)) {
            warnOnUnstableReference(mapResult, secondMapResult);
            didWarnUnstableReference = true;
          }
        }
      }
      if (!subscriber) {
        for (const name of listeningStores.current) {
          storeStatesOnMount.set(name, getStoreState(name));
        }
        subscriber = createSubscriber(listeningStores.current);
      } else {
        subscriber.updateStores(listeningStores.current);
      }
      if (!external_wp_isShallowEqual_default()(lastMapResult, mapResult)) {
        lastMapResult = mapResult;
      }
      lastMapSelect = mapSelect;
      lastMapResultValid = true;
    }
    function getValue() {
      updateValue();
      return lastMapResult;
    }
    if (lastIsAsync && !isAsync) {
      lastMapResultValid = false;
      renderQueue.cancel(queueContext);
    }
    updateValue();
    lastIsAsync = isAsync;
    return { subscribe: subscriber.subscribe, getValue };
  };
}
function _useStaticSelect(storeName) {
  return useRegistry().select(storeName);
}
function _useMappingSelect(suspense, mapSelect, deps) {
  const registry = useRegistry();
  const isAsync = useAsyncMode();
  const store = (0,external_wp_element_namespaceObject.useMemo)(
    () => Store(registry, suspense),
    [registry, suspense]
  );
  const selector = (0,external_wp_element_namespaceObject.useCallback)(mapSelect, deps);
  const { subscribe, getValue } = store(selector, isAsync);
  const result = (0,external_wp_element_namespaceObject.useSyncExternalStore)(subscribe, getValue, getValue);
  (0,external_wp_element_namespaceObject.useDebugValue)(result);
  return result;
}
function useSelect(mapSelect, deps) {
  const staticSelectMode = typeof mapSelect !== "function";
  const staticSelectModeRef = (0,external_wp_element_namespaceObject.useRef)(staticSelectMode);
  if (staticSelectMode !== staticSelectModeRef.current) {
    const prevMode = staticSelectModeRef.current ? "static" : "mapping";
    const nextMode = staticSelectMode ? "static" : "mapping";
    throw new Error(
      `Switching useSelect from ${prevMode} to ${nextMode} is not allowed`
    );
  }
  return staticSelectMode ? _useStaticSelect(mapSelect) : _useMappingSelect(false, mapSelect, deps);
}
function useSuspenseSelect(mapSelect, deps) {
  return _useMappingSelect(true, mapSelect, deps);
}


;// ./node_modules/@wordpress/data/build-module/components/with-select/index.js



const withSelect = (mapSelectToProps) => (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(
  (WrappedComponent) => (0,external_wp_compose_namespaceObject.pure)((ownProps) => {
    const mapSelect = (select, registry) => mapSelectToProps(select, ownProps, registry);
    const mergeProps = useSelect(mapSelect);
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(WrappedComponent, { ...ownProps, ...mergeProps });
  }),
  "withSelect"
);
var with_select_default = withSelect;


;// ./node_modules/@wordpress/data/build-module/components/use-dispatch/use-dispatch-with-map.js



const useDispatchWithMap = (dispatchMap, deps) => {
  const registry = useRegistry();
  const currentDispatchMapRef = (0,external_wp_element_namespaceObject.useRef)(dispatchMap);
  (0,external_wp_compose_namespaceObject.useIsomorphicLayoutEffect)(() => {
    currentDispatchMapRef.current = dispatchMap;
  });
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    const currentDispatchProps = currentDispatchMapRef.current(
      registry.dispatch,
      registry
    );
    return Object.fromEntries(
      Object.entries(currentDispatchProps).map(
        ([propName, dispatcher]) => {
          if (typeof dispatcher !== "function") {
            console.warn(
              `Property ${propName} returned from dispatchMap in useDispatchWithMap must be a function.`
            );
          }
          return [
            propName,
            (...args) => currentDispatchMapRef.current(registry.dispatch, registry)[propName](...args)
          ];
        }
      )
    );
  }, [registry, ...deps]);
};
var use_dispatch_with_map_default = useDispatchWithMap;


;// ./node_modules/@wordpress/data/build-module/components/with-dispatch/index.js



const withDispatch = (mapDispatchToProps) => (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(
  (WrappedComponent) => (ownProps) => {
    const mapDispatch = (dispatch, registry) => mapDispatchToProps(dispatch, ownProps, registry);
    const dispatchProps = use_dispatch_with_map_default(mapDispatch, []);
    return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(WrappedComponent, { ...ownProps, ...dispatchProps });
  },
  "withDispatch"
);
var with_dispatch_default = withDispatch;


;// ./node_modules/@wordpress/data/build-module/components/with-registry/index.js



const withRegistry = (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(
  (OriginalComponent) => (props) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(RegistryConsumer, { children: (registry) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(OriginalComponent, { ...props, registry }) }),
  "withRegistry"
);
var with_registry_default = withRegistry;


;// ./node_modules/@wordpress/data/build-module/components/use-dispatch/use-dispatch.js

const useDispatch = (storeNameOrDescriptor) => {
  const { dispatch } = useRegistry();
  return storeNameOrDescriptor === void 0 ? dispatch : dispatch(storeNameOrDescriptor);
};
var use_dispatch_default = useDispatch;


;// ./node_modules/@wordpress/data/build-module/dispatch.js

function dispatch_dispatch(storeNameOrDescriptor) {
  return default_registry_default.dispatch(storeNameOrDescriptor);
}


;// ./node_modules/@wordpress/data/build-module/select.js

function select_select(storeNameOrDescriptor) {
  return default_registry_default.select(storeNameOrDescriptor);
}


;// ./node_modules/@wordpress/data/build-module/index.js

















const build_module_combineReducers = combine_reducers_combineReducers;
const build_module_resolveSelect = default_registry_default.resolveSelect;
const suspendSelect = default_registry_default.suspendSelect;
const subscribe = default_registry_default.subscribe;
const registerGenericStore = default_registry_default.registerGenericStore;
const registerStore = default_registry_default.registerStore;
const use = default_registry_default.use;
const register = default_registry_default.register;


(window.wp = window.wp || {}).data = __webpack_exports__;
/******/ })()
;