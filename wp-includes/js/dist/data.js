<<<<<<< HEAD
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 1919:
/***/ ((module) => {

"use strict";


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

/***/ 2167:
/***/ ((module) => {

"use strict";


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


/***/ }),

/***/ 9125:
/***/ ((module) => {

function combineReducers( reducers ) {
	var keys = Object.keys( reducers ),
		getNextState;

	getNextState = ( function() {
		var fn, i, key;

		fn = 'return {';
		for ( i = 0; i < keys.length; i++ ) {
			// Rely on Quoted escaping of JSON.stringify with guarantee that
			// each member of Object.keys is a string.
			//
			// "If Type(value) is String, then return the result of calling the
			// abstract operation Quote with argument value. [...] The abstract
			// operation Quote(value) wraps a String value in double quotes and
			// escapes characters within it."
			//
			// https://www.ecma-international.org/ecma-262/5.1/#sec-15.12.3
			key = JSON.stringify( keys[ i ] );

			fn += key + ':r[' + key + '](s[' + key + '],a),';
		}
		fn += '}';

		return new Function( 'r,s,a', fn );
	} )();

	return function combinedReducer( state, action ) {
		var nextState, i, key;

		// Assumed changed if initial state.
		if ( state === undefined ) {
			return getNextState( reducers, {}, action );
		}

		nextState = getNextState( reducers, state, action );

		// Determine whether state has changed.
		i = keys.length;
		while ( i-- ) {
			key = keys[ i ];
			if ( state[ key ] !== nextState[ key ] ) {
				// Return immediately if a changed value is encountered.
				return nextState;
			}
		}

		return state;
	};
}

module.exports = combineReducers;


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
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  AsyncModeProvider: () => (/* reexport */ async_mode_provider_context),
  RegistryConsumer: () => (/* reexport */ RegistryConsumer),
  RegistryProvider: () => (/* reexport */ context),
  combineReducers: () => (/* binding */ build_module_combineReducers),
  controls: () => (/* reexport */ controls),
  createReduxStore: () => (/* reexport */ createReduxStore),
  createRegistry: () => (/* reexport */ createRegistry),
  createRegistryControl: () => (/* reexport */ createRegistryControl),
  createRegistrySelector: () => (/* reexport */ createRegistrySelector),
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
  useDispatch: () => (/* reexport */ use_dispatch),
  useRegistry: () => (/* reexport */ useRegistry),
  useSelect: () => (/* reexport */ useSelect),
  useSuspenseSelect: () => (/* reexport */ useSuspenseSelect),
  withDispatch: () => (/* reexport */ with_dispatch),
  withRegistry: () => (/* reexport */ with_registry),
  withSelect: () => (/* reexport */ with_select)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/data/build-module/redux-store/metadata/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
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
  persistence: () => (persistence)
});

// EXTERNAL MODULE: ./node_modules/turbo-combine-reducers/index.js
var turbo_combine_reducers = __webpack_require__(9125);
var turbo_combine_reducers_default = /*#__PURE__*/__webpack_require__.n(turbo_combine_reducers);
;// CONCATENATED MODULE: external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/typeof.js
function _typeof(obj) {
  "@babel/helpers - typeof";

  return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) {
    return typeof obj;
  } : function (obj) {
    return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
  }, _typeof(obj);
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toPrimitive.js

function _toPrimitive(input, hint) {
  if (_typeof(input) !== "object" || input === null) return input;
  var prim = input[Symbol.toPrimitive];
  if (prim !== undefined) {
    var res = prim.call(input, hint || "default");
    if (_typeof(res) !== "object") return res;
    throw new TypeError("@@toPrimitive must return a primitive value.");
  }
  return (hint === "string" ? String : Number)(input);
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toPropertyKey.js


function _toPropertyKey(arg) {
  var key = _toPrimitive(arg, "string");
  return _typeof(key) === "symbol" ? key : String(key);
}
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js

function _defineProperty(obj, key, value) {
  key = _toPropertyKey(key);
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
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread2.js

function ownKeys(object, enumerableOnly) {
  var keys = Object.keys(object);
  if (Object.getOwnPropertySymbols) {
    var symbols = Object.getOwnPropertySymbols(object);
    enumerableOnly && (symbols = symbols.filter(function (sym) {
      return Object.getOwnPropertyDescriptor(object, sym).enumerable;
    })), keys.push.apply(keys, symbols);
  }
  return keys;
}
function _objectSpread2(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = null != arguments[i] ? arguments[i] : {};
    i % 2 ? ownKeys(Object(source), !0).forEach(function (key) {
      _defineProperty(target, key, source[key]);
    }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(target, Object.getOwnPropertyDescriptors(source)) : ownKeys(Object(source)).forEach(function (key) {
      Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
    });
  }
  return target;
}
;// CONCATENATED MODULE: ./node_modules/redux/es/redux.js


/**
 * Adapted from React: https://github.com/facebook/react/blob/master/packages/shared/formatProdErrorMessage.js
 *
 * Do not require this module directly! Use normal throw error calls. These messages will be replaced with error codes
 * during build.
 * @param {number} code
 */
function formatProdErrorMessage(code) {
  return "Minified Redux error #" + code + "; visit https://redux.js.org/Errors?code=" + code + " for the full message or " + 'use the non-minified dev environment for full errors. ';
}

// Inlined version of the `symbol-observable` polyfill
var $$observable = (function () {
  return typeof Symbol === 'function' && Symbol.observable || '@@observable';
})();

/**
 * These are private action types reserved by Redux.
 * For any unknown actions, you must return the current state.
 * If the current state is undefined, you must return the initial state.
 * Do not reference these action types directly in your code.
 */
var randomString = function randomString() {
  return Math.random().toString(36).substring(7).split('').join('.');
};

var ActionTypes = {
  INIT: "@@redux/INIT" + randomString(),
  REPLACE: "@@redux/REPLACE" + randomString(),
  PROBE_UNKNOWN_ACTION: function PROBE_UNKNOWN_ACTION() {
    return "@@redux/PROBE_UNKNOWN_ACTION" + randomString();
  }
};

/**
 * @param {any} obj The object to inspect.
 * @returns {boolean} True if the argument appears to be a plain object.
 */
function isPlainObject(obj) {
  if (typeof obj !== 'object' || obj === null) return false;
  var proto = obj;

  while (Object.getPrototypeOf(proto) !== null) {
    proto = Object.getPrototypeOf(proto);
  }

  return Object.getPrototypeOf(obj) === proto;
}

// Inlined / shortened version of `kindOf` from https://github.com/jonschlinkert/kind-of
function miniKindOf(val) {
  if (val === void 0) return 'undefined';
  if (val === null) return 'null';
  var type = typeof val;

  switch (type) {
    case 'boolean':
    case 'string':
    case 'number':
    case 'symbol':
    case 'function':
      {
        return type;
      }
  }

  if (Array.isArray(val)) return 'array';
  if (isDate(val)) return 'date';
  if (isError(val)) return 'error';
  var constructorName = ctorName(val);

  switch (constructorName) {
    case 'Symbol':
    case 'Promise':
    case 'WeakMap':
    case 'WeakSet':
    case 'Map':
    case 'Set':
      return constructorName;
  } // other


  return type.slice(8, -1).toLowerCase().replace(/\s/g, '');
}

function ctorName(val) {
  return typeof val.constructor === 'function' ? val.constructor.name : null;
}

function isError(val) {
  return val instanceof Error || typeof val.message === 'string' && val.constructor && typeof val.constructor.stackTraceLimit === 'number';
}

function isDate(val) {
  if (val instanceof Date) return true;
  return typeof val.toDateString === 'function' && typeof val.getDate === 'function' && typeof val.setDate === 'function';
}

function kindOf(val) {
  var typeOfVal = typeof val;

  if (false) {}

  return typeOfVal;
}

/**
 * @deprecated
 *
 * **We recommend using the `configureStore` method
 * of the `@reduxjs/toolkit` package**, which replaces `createStore`.
 *
 * Redux Toolkit is our recommended approach for writing Redux logic today,
 * including store setup, reducers, data fetching, and more.
 *
 * **For more details, please read this Redux docs page:**
 * **https://redux.js.org/introduction/why-rtk-is-redux-today**
 *
 * `configureStore` from Redux Toolkit is an improved version of `createStore` that
 * simplifies setup and helps avoid common bugs.
 *
 * You should not be using the `redux` core package by itself today, except for learning purposes.
 * The `createStore` method from the core `redux` package will not be removed, but we encourage
 * all users to migrate to using Redux Toolkit for all Redux code.
 *
 * If you want to use `createStore` without this visual deprecation warning, use
 * the `legacy_createStore` import instead:
 *
 * `import { legacy_createStore as createStore} from 'redux'`
 *
 */

function createStore(reducer, preloadedState, enhancer) {
  var _ref2;

  if (typeof preloadedState === 'function' && typeof enhancer === 'function' || typeof enhancer === 'function' && typeof arguments[3] === 'function') {
    throw new Error( true ? formatProdErrorMessage(0) : 0);
  }

  if (typeof preloadedState === 'function' && typeof enhancer === 'undefined') {
    enhancer = preloadedState;
    preloadedState = undefined;
  }

  if (typeof enhancer !== 'undefined') {
    if (typeof enhancer !== 'function') {
      throw new Error( true ? formatProdErrorMessage(1) : 0);
    }

    return enhancer(createStore)(reducer, preloadedState);
  }

  if (typeof reducer !== 'function') {
    throw new Error( true ? formatProdErrorMessage(2) : 0);
  }

  var currentReducer = reducer;
  var currentState = preloadedState;
  var currentListeners = [];
  var nextListeners = currentListeners;
  var isDispatching = false;
  /**
   * This makes a shallow copy of currentListeners so we can use
   * nextListeners as a temporary list while dispatching.
   *
   * This prevents any bugs around consumers calling
   * subscribe/unsubscribe in the middle of a dispatch.
   */

  function ensureCanMutateNextListeners() {
    if (nextListeners === currentListeners) {
      nextListeners = currentListeners.slice();
    }
  }
  /**
   * Reads the state tree managed by the store.
   *
   * @returns {any} The current state tree of your application.
   */


  function getState() {
    if (isDispatching) {
      throw new Error( true ? formatProdErrorMessage(3) : 0);
    }

    return currentState;
  }
  /**
   * Adds a change listener. It will be called any time an action is dispatched,
   * and some part of the state tree may potentially have changed. You may then
   * call `getState()` to read the current state tree inside the callback.
   *
   * You may call `dispatch()` from a change listener, with the following
   * caveats:
   *
   * 1. The subscriptions are snapshotted just before every `dispatch()` call.
   * If you subscribe or unsubscribe while the listeners are being invoked, this
   * will not have any effect on the `dispatch()` that is currently in progress.
   * However, the next `dispatch()` call, whether nested or not, will use a more
   * recent snapshot of the subscription list.
   *
   * 2. The listener should not expect to see all state changes, as the state
   * might have been updated multiple times during a nested `dispatch()` before
   * the listener is called. It is, however, guaranteed that all subscribers
   * registered before the `dispatch()` started will be called with the latest
   * state by the time it exits.
   *
   * @param {Function} listener A callback to be invoked on every dispatch.
   * @returns {Function} A function to remove this change listener.
   */


  function subscribe(listener) {
    if (typeof listener !== 'function') {
      throw new Error( true ? formatProdErrorMessage(4) : 0);
    }

    if (isDispatching) {
      throw new Error( true ? formatProdErrorMessage(5) : 0);
    }

    var isSubscribed = true;
    ensureCanMutateNextListeners();
    nextListeners.push(listener);
    return function unsubscribe() {
      if (!isSubscribed) {
        return;
      }

      if (isDispatching) {
        throw new Error( true ? formatProdErrorMessage(6) : 0);
      }

      isSubscribed = false;
      ensureCanMutateNextListeners();
      var index = nextListeners.indexOf(listener);
      nextListeners.splice(index, 1);
      currentListeners = null;
    };
  }
  /**
   * Dispatches an action. It is the only way to trigger a state change.
   *
   * The `reducer` function, used to create the store, will be called with the
   * current state tree and the given `action`. Its return value will
   * be considered the **next** state of the tree, and the change listeners
   * will be notified.
   *
   * The base implementation only supports plain object actions. If you want to
   * dispatch a Promise, an Observable, a thunk, or something else, you need to
   * wrap your store creating function into the corresponding middleware. For
   * example, see the documentation for the `redux-thunk` package. Even the
   * middleware will eventually dispatch plain object actions using this method.
   *
   * @param {Object} action A plain object representing “what changed”. It is
   * a good idea to keep actions serializable so you can record and replay user
   * sessions, or use the time travelling `redux-devtools`. An action must have
   * a `type` property which may not be `undefined`. It is a good idea to use
   * string constants for action types.
   *
   * @returns {Object} For convenience, the same action object you dispatched.
   *
   * Note that, if you use a custom middleware, it may wrap `dispatch()` to
   * return something else (for example, a Promise you can await).
   */


  function dispatch(action) {
    if (!isPlainObject(action)) {
      throw new Error( true ? formatProdErrorMessage(7) : 0);
    }

    if (typeof action.type === 'undefined') {
      throw new Error( true ? formatProdErrorMessage(8) : 0);
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

    var listeners = currentListeners = nextListeners;

    for (var i = 0; i < listeners.length; i++) {
      var listener = listeners[i];
      listener();
    }

    return action;
  }
  /**
   * Replaces the reducer currently used by the store to calculate the state.
   *
   * You might need this if your app implements code splitting and you want to
   * load some of the reducers dynamically. You might also need this if you
   * implement a hot reloading mechanism for Redux.
   *
   * @param {Function} nextReducer The reducer for the store to use instead.
   * @returns {void}
   */


  function replaceReducer(nextReducer) {
    if (typeof nextReducer !== 'function') {
      throw new Error( true ? formatProdErrorMessage(10) : 0);
    }

    currentReducer = nextReducer; // This action has a similiar effect to ActionTypes.INIT.
    // Any reducers that existed in both the new and old rootReducer
    // will receive the previous state. This effectively populates
    // the new state tree with any relevant data from the old one.

    dispatch({
      type: ActionTypes.REPLACE
    });
  }
  /**
   * Interoperability point for observable/reactive libraries.
   * @returns {observable} A minimal observable of state changes.
   * For more information, see the observable proposal:
   * https://github.com/tc39/proposal-observable
   */


  function observable() {
    var _ref;

    var outerSubscribe = subscribe;
    return _ref = {
      /**
       * The minimal observable subscription method.
       * @param {Object} observer Any object that can be used as an observer.
       * The observer object should have a `next` method.
       * @returns {subscription} An object with an `unsubscribe` method that can
       * be used to unsubscribe the observable from the store, and prevent further
       * emission of values from the observable.
       */
      subscribe: function subscribe(observer) {
        if (typeof observer !== 'object' || observer === null) {
          throw new Error( true ? formatProdErrorMessage(11) : 0);
        }

        function observeState() {
          if (observer.next) {
            observer.next(getState());
          }
        }

        observeState();
        var unsubscribe = outerSubscribe(observeState);
        return {
          unsubscribe: unsubscribe
        };
      }
    }, _ref[$$observable] = function () {
      return this;
    }, _ref;
  } // When a store is created, an "INIT" action is dispatched so that every
  // reducer returns their initial state. This effectively populates
  // the initial state tree.


  dispatch({
    type: ActionTypes.INIT
  });
  return _ref2 = {
    dispatch: dispatch,
    subscribe: subscribe,
    getState: getState,
    replaceReducer: replaceReducer
  }, _ref2[$$observable] = observable, _ref2;
}
/**
 * Creates a Redux store that holds the state tree.
 *
 * **We recommend using `configureStore` from the
 * `@reduxjs/toolkit` package**, which replaces `createStore`:
 * **https://redux.js.org/introduction/why-rtk-is-redux-today**
 *
 * The only way to change the data in the store is to call `dispatch()` on it.
 *
 * There should only be a single store in your app. To specify how different
 * parts of the state tree respond to actions, you may combine several reducers
 * into a single reducer function by using `combineReducers`.
 *
 * @param {Function} reducer A function that returns the next state tree, given
 * the current state tree and the action to handle.
 *
 * @param {any} [preloadedState] The initial state. You may optionally specify it
 * to hydrate the state from the server in universal apps, or to restore a
 * previously serialized user session.
 * If you use `combineReducers` to produce the root reducer function, this must be
 * an object with the same shape as `combineReducers` keys.
 *
 * @param {Function} [enhancer] The store enhancer. You may optionally specify it
 * to enhance the store with third-party capabilities such as middleware,
 * time travel, persistence, etc. The only store enhancer that ships with Redux
 * is `applyMiddleware()`.
 *
 * @returns {Store} A Redux store that lets you read the state, dispatch actions
 * and subscribe to changes.
 */

var legacy_createStore = (/* unused pure expression or super */ null && (createStore));

/**
 * Prints a warning in the console if it exists.
 *
 * @param {String} message The warning message.
 * @returns {void}
 */
function warning(message) {
  /* eslint-disable no-console */
  if (typeof console !== 'undefined' && typeof console.error === 'function') {
    console.error(message);
  }
  /* eslint-enable no-console */


  try {
    // This error was thrown as a convenience so that if you enable
    // "break on all exceptions" in your console,
    // it would pause the execution at this line.
    throw new Error(message);
  } catch (e) {} // eslint-disable-line no-empty

}

function getUnexpectedStateShapeWarningMessage(inputState, reducers, action, unexpectedKeyCache) {
  var reducerKeys = Object.keys(reducers);
  var argumentName = action && action.type === ActionTypes.INIT ? 'preloadedState argument passed to createStore' : 'previous state received by the reducer';

  if (reducerKeys.length === 0) {
    return 'Store does not have a valid reducer. Make sure the argument passed ' + 'to combineReducers is an object whose values are reducers.';
  }

  if (!isPlainObject(inputState)) {
    return "The " + argumentName + " has unexpected type of \"" + kindOf(inputState) + "\". Expected argument to be an object with the following " + ("keys: \"" + reducerKeys.join('", "') + "\"");
  }

  var unexpectedKeys = Object.keys(inputState).filter(function (key) {
    return !reducers.hasOwnProperty(key) && !unexpectedKeyCache[key];
  });
  unexpectedKeys.forEach(function (key) {
    unexpectedKeyCache[key] = true;
  });
  if (action && action.type === ActionTypes.REPLACE) return;

  if (unexpectedKeys.length > 0) {
    return "Unexpected " + (unexpectedKeys.length > 1 ? 'keys' : 'key') + " " + ("\"" + unexpectedKeys.join('", "') + "\" found in " + argumentName + ". ") + "Expected to find one of the known reducer keys instead: " + ("\"" + reducerKeys.join('", "') + "\". Unexpected keys will be ignored.");
  }
}

function assertReducerShape(reducers) {
  Object.keys(reducers).forEach(function (key) {
    var reducer = reducers[key];
    var initialState = reducer(undefined, {
      type: ActionTypes.INIT
    });

    if (typeof initialState === 'undefined') {
      throw new Error( true ? formatProdErrorMessage(12) : 0);
    }

    if (typeof reducer(undefined, {
      type: ActionTypes.PROBE_UNKNOWN_ACTION()
    }) === 'undefined') {
      throw new Error( true ? formatProdErrorMessage(13) : 0);
    }
  });
}
/**
 * Turns an object whose values are different reducer functions, into a single
 * reducer function. It will call every child reducer, and gather their results
 * into a single state object, whose keys correspond to the keys of the passed
 * reducer functions.
 *
 * @param {Object} reducers An object whose values correspond to different
 * reducer functions that need to be combined into one. One handy way to obtain
 * it is to use ES6 `import * as reducers` syntax. The reducers may never return
 * undefined for any action. Instead, they should return their initial state
 * if the state passed to them was undefined, and the current state for any
 * unrecognized action.
 *
 * @returns {Function} A reducer function that invokes every reducer inside the
 * passed object, and builds a state object with the same shape.
 */


function combineReducers(reducers) {
  var reducerKeys = Object.keys(reducers);
  var finalReducers = {};

  for (var i = 0; i < reducerKeys.length; i++) {
    var key = reducerKeys[i];

    if (false) {}

    if (typeof reducers[key] === 'function') {
      finalReducers[key] = reducers[key];
    }
  }

  var finalReducerKeys = Object.keys(finalReducers); // This is used to make sure we don't warn about the same
  // keys multiple times.

  var unexpectedKeyCache;

  if (false) {}

  var shapeAssertionError;

  try {
    assertReducerShape(finalReducers);
  } catch (e) {
    shapeAssertionError = e;
  }

  return function combination(state, action) {
    if (state === void 0) {
      state = {};
    }

    if (shapeAssertionError) {
      throw shapeAssertionError;
    }

    if (false) { var warningMessage; }

    var hasChanged = false;
    var nextState = {};

    for (var _i = 0; _i < finalReducerKeys.length; _i++) {
      var _key = finalReducerKeys[_i];
      var reducer = finalReducers[_key];
      var previousStateForKey = state[_key];
      var nextStateForKey = reducer(previousStateForKey, action);

      if (typeof nextStateForKey === 'undefined') {
        var actionType = action && action.type;
        throw new Error( true ? formatProdErrorMessage(14) : 0);
      }

      nextState[_key] = nextStateForKey;
      hasChanged = hasChanged || nextStateForKey !== previousStateForKey;
    }

    hasChanged = hasChanged || finalReducerKeys.length !== Object.keys(state).length;
    return hasChanged ? nextState : state;
  };
}

function bindActionCreator(actionCreator, dispatch) {
  return function () {
    return dispatch(actionCreator.apply(this, arguments));
  };
}
/**
 * Turns an object whose values are action creators, into an object with the
 * same keys, but with every function wrapped into a `dispatch` call so they
 * may be invoked directly. This is just a convenience method, as you can call
 * `store.dispatch(MyActionCreators.doSomething())` yourself just fine.
 *
 * For convenience, you can also pass an action creator as the first argument,
 * and get a dispatch wrapped function in return.
 *
 * @param {Function|Object} actionCreators An object whose values are action
 * creator functions. One handy way to obtain it is to use ES6 `import * as`
 * syntax. You may also pass a single function.
 *
 * @param {Function} dispatch The `dispatch` function available on your Redux
 * store.
 *
 * @returns {Function|Object} The object mimicking the original object, but with
 * every action creator wrapped into the `dispatch` call. If you passed a
 * function as `actionCreators`, the return value will also be a single
 * function.
 */


function bindActionCreators(actionCreators, dispatch) {
  if (typeof actionCreators === 'function') {
    return bindActionCreator(actionCreators, dispatch);
  }

  if (typeof actionCreators !== 'object' || actionCreators === null) {
    throw new Error( true ? formatProdErrorMessage(16) : 0);
  }

  var boundActionCreators = {};

  for (var key in actionCreators) {
    var actionCreator = actionCreators[key];

    if (typeof actionCreator === 'function') {
      boundActionCreators[key] = bindActionCreator(actionCreator, dispatch);
    }
  }

  return boundActionCreators;
}

/**
 * Composes single-argument functions from right to left. The rightmost
 * function can take multiple arguments as it provides the signature for
 * the resulting composite function.
 *
 * @param {...Function} funcs The functions to compose.
 * @returns {Function} A function obtained by composing the argument functions
 * from right to left. For example, compose(f, g, h) is identical to doing
 * (...args) => f(g(h(...args))).
 */
function compose() {
  for (var _len = arguments.length, funcs = new Array(_len), _key = 0; _key < _len; _key++) {
    funcs[_key] = arguments[_key];
  }

  if (funcs.length === 0) {
    return function (arg) {
      return arg;
    };
  }

  if (funcs.length === 1) {
    return funcs[0];
  }

  return funcs.reduce(function (a, b) {
    return function () {
      return a(b.apply(void 0, arguments));
    };
  });
}

/**
 * Creates a store enhancer that applies middleware to the dispatch method
 * of the Redux store. This is handy for a variety of tasks, such as expressing
 * asynchronous actions in a concise manner, or logging every action payload.
 *
 * See `redux-thunk` package as an example of the Redux middleware.
 *
 * Because middleware is potentially asynchronous, this should be the first
 * store enhancer in the composition chain.
 *
 * Note that each middleware will be given the `dispatch` and `getState` functions
 * as named arguments.
 *
 * @param {...Function} middlewares The middleware chain to be applied.
 * @returns {Function} A store enhancer applying the middleware.
 */

function applyMiddleware() {
  for (var _len = arguments.length, middlewares = new Array(_len), _key = 0; _key < _len; _key++) {
    middlewares[_key] = arguments[_key];
  }

  return function (createStore) {
    return function () {
      var store = createStore.apply(void 0, arguments);

      var _dispatch = function dispatch() {
        throw new Error( true ? formatProdErrorMessage(15) : 0);
      };

      var middlewareAPI = {
        getState: store.getState,
        dispatch: function dispatch() {
          return _dispatch.apply(void 0, arguments);
        }
      };
      var chain = middlewares.map(function (middleware) {
        return middleware(middlewareAPI);
      });
      _dispatch = compose.apply(void 0, chain)(store.dispatch);
      return _objectSpread2(_objectSpread2({}, store), {}, {
        dispatch: _dispatch
      });
    };
  };
}



// EXTERNAL MODULE: ./node_modules/equivalent-key-map/equivalent-key-map.js
var equivalent_key_map = __webpack_require__(2167);
var equivalent_key_map_default = /*#__PURE__*/__webpack_require__.n(equivalent_key_map);
;// CONCATENATED MODULE: external ["wp","reduxRoutine"]
const external_wp_reduxRoutine_namespaceObject = window["wp"]["reduxRoutine"];
var external_wp_reduxRoutine_default = /*#__PURE__*/__webpack_require__.n(external_wp_reduxRoutine_namespaceObject);
;// CONCATENATED MODULE: external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/factory.js
/**
 * Creates a selector function that takes additional curried argument with the
 * registry `select` function. While a regular selector has signature
 * ```js
 * ( state, ...selectorArgs ) => ( result )
 * ```
 * that allows to select data from the store's `state`, a registry selector
 * has signature:
 * ```js
 * ( select ) => ( state, ...selectorArgs ) => ( result )
 * ```
 * that supports also selecting from other registered stores.
 *
 * @example
 * ```js
 * import { store as coreStore } from '@wordpress/core-data';
 * import { store as editorStore } from '@wordpress/editor';
 *
 * const getCurrentPostId = createRegistrySelector( ( select ) => ( state ) => {
 *   return select( editorStore ).getCurrentPostId();
 * } );
 *
 * const getPostEdits = createRegistrySelector( ( select ) => ( state ) => {
 *   // calling another registry selector just like any other function
 *   const postType = getCurrentPostType( state );
 *   const postId = getCurrentPostId( state );
 *	 return select( coreStore ).getEntityRecordEdits( 'postType', postType, postId );
 * } );
 * ```
 *
 * Note how the `getCurrentPostId` selector can be called just like any other function,
 * (it works even inside a regular non-registry selector) and we don't need to pass the
 * registry as argument. The registry binding happens automatically when registering the selector
 * with a store.
 *
 * @param {Function} registrySelector Function receiving a registry `select`
 *                                    function and returning a state selector.
 *
 * @return {Function} Registry selector that can be registered with a store.
 */
function createRegistrySelector(registrySelector) {
  // Create a selector function that is bound to the registry referenced by `selector.registry`
  // and that has the same API as a regular selector. Binding it in such a way makes it
  // possible to call the selector directly from another selector.
  const selector = (...args) => registrySelector(selector.registry.select)(...args);
  /**
   * Flag indicating that the selector is a registry selector that needs the correct registry
   * reference to be assigned to `selector.registry` to make it work correctly.
   * be mapped as a registry selector.
   *
   * @type {boolean}
   */


  selector.isRegistrySelector = true;
  return selector;
}
/**
 * Creates a control function that takes additional curried argument with the `registry` object.
 * While a regular control has signature
 * ```js
 * ( action ) => ( iteratorOrPromise )
 * ```
 * where the control works with the `action` that it's bound to, a registry control has signature:
 * ```js
 * ( registry ) => ( action ) => ( iteratorOrPromise )
 * ```
 * A registry control is typically used to select data or dispatch an action to a registered
 * store.
 *
 * When registering a control created with `createRegistryControl` with a store, the store
 * knows which calling convention to use when executing the control.
 *
 * @param {Function} registryControl Function receiving a registry object and returning a control.
 *
 * @return {Function} Registry control that can be registered with a store.
 */

function createRegistryControl(registryControl) {
  registryControl.isRegistryControl = true;
  return registryControl;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/controls.js
/**
 * Internal dependencies
 */

/** @typedef {import('./types').StoreDescriptor} StoreDescriptor */

const SELECT = '@@data/SELECT';
const RESOLVE_SELECT = '@@data/RESOLVE_SELECT';
const DISPATCH = '@@data/DISPATCH';

function isObject(object) {
  return object !== null && typeof object === 'object';
}
/**
 * Dispatches a control action for triggering a synchronous registry select.
 *
 * Note: This control synchronously returns the current selector value, triggering the
 * resolution, but not waiting for it.
 *
 * @param {string|StoreDescriptor} storeNameOrDescriptor Unique namespace identifier for the store
 * @param {string}                 selectorName          The name of the selector.
 * @param {Array}                  args                  Arguments for the selector.
 *
 * @example
 * ```js
 * import { controls } from '@wordpress/data';
 *
 * // Action generator using `select`.
 * export function* myAction() {
 *   const isEditorSideBarOpened = yield controls.select( 'core/edit-post', 'isEditorSideBarOpened' );
 *   // Do stuff with the result from the `select`.
 * }
 * ```
 *
 * @return {Object} The control descriptor.
 */


function controls_select(storeNameOrDescriptor, selectorName, ...args) {
  return {
    type: SELECT,
    storeKey: isObject(storeNameOrDescriptor) ? storeNameOrDescriptor.name : storeNameOrDescriptor,
    selectorName,
    args
  };
}
/**
 * Dispatches a control action for triggering and resolving a registry select.
 *
 * Note: when this control action is handled, it automatically considers
 * selectors that may have a resolver. In such case, it will return a `Promise` that resolves
 * after the selector finishes resolving, with the final result value.
 *
 * @param {string|StoreDescriptor} storeNameOrDescriptor Unique namespace identifier for the store
 * @param {string}                 selectorName          The name of the selector
 * @param {Array}                  args                  Arguments for the selector.
 *
 * @example
 * ```js
 * import { controls } from '@wordpress/data';
 *
 * // Action generator using resolveSelect
 * export function* myAction() {
 * 	const isSidebarOpened = yield controls.resolveSelect( 'core/edit-post', 'isEditorSideBarOpened' );
 * 	// do stuff with the result from the select.
 * }
 * ```
 *
 * @return {Object} The control descriptor.
 */


function resolveSelect(storeNameOrDescriptor, selectorName, ...args) {
  return {
    type: RESOLVE_SELECT,
    storeKey: isObject(storeNameOrDescriptor) ? storeNameOrDescriptor.name : storeNameOrDescriptor,
    selectorName,
    args
  };
}
/**
 * Dispatches a control action for triggering a registry dispatch.
 *
 * @param {string|StoreDescriptor} storeNameOrDescriptor Unique namespace identifier for the store
 * @param {string}                 actionName            The name of the action to dispatch
 * @param {Array}                  args                  Arguments for the dispatch action.
 *
 * @example
 * ```js
 * import { controls } from '@wordpress/data-controls';
 *
 * // Action generator using dispatch
 * export function* myAction() {
 *   yield controls.dispatch( 'core/edit-post', 'togglePublishSidebar' );
 *   // do some other things.
 * }
 * ```
 *
 * @return {Object}  The control descriptor.
 */


function dispatch(storeNameOrDescriptor, actionName, ...args) {
  return {
    type: DISPATCH,
    storeKey: isObject(storeNameOrDescriptor) ? storeNameOrDescriptor.name : storeNameOrDescriptor,
    actionName,
    args
  };
}

const controls = {
  select: controls_select,
  resolveSelect,
  dispatch
};
const builtinControls = {
  [SELECT]: createRegistryControl(registry => ({
    storeKey,
    selectorName,
    args
  }) => registry.select(storeKey)[selectorName](...args)),
  [RESOLVE_SELECT]: createRegistryControl(registry => ({
    storeKey,
    selectorName,
    args
  }) => {
    const method = registry.select(storeKey)[selectorName].hasResolver ? 'resolveSelect' : 'select';
    return registry[method](storeKey)[selectorName](...args);
  }),
  [DISPATCH]: createRegistryControl(registry => ({
    storeKey,
    actionName,
    args
  }) => registry.dispatch(storeKey)[actionName](...args))
};

;// CONCATENATED MODULE: external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/lock-unlock.js
/**
 * WordPress dependencies
 */

const {
  lock,
  unlock
} = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I know using unstable features means my plugin or theme will inevitably break on the next WordPress release.', '@wordpress/data');

;// CONCATENATED MODULE: ./node_modules/is-promise/index.mjs
=======
this["wp"] = this["wp"] || {}; this["wp"]["data"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "pfJ3");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1OyB":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ "25BE":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
}

/***/ }),

/***/ "8mpt":
/***/ (function(module, exports) {

function combineReducers( reducers ) {
	var keys = Object.keys( reducers ),
		getNextState;

	getNextState = ( function() {
		var fn, i, key;

		fn = 'return {';
		for ( i = 0; i < keys.length; i++ ) {
			// Rely on Quoted escaping of JSON.stringify with guarantee that
			// each member of Object.keys is a string.
			//
			// "If Type(value) is String, then return the result of calling the
			// abstract operation Quote with argument value. [...] The abstract
			// operation Quote(value) wraps a String value in double quotes and
			// escapes characters within it."
			//
			// https://www.ecma-international.org/ecma-262/5.1/#sec-15.12.3
			key = JSON.stringify( keys[ i ] );

			fn += key + ':r[' + key + '](s[' + key + '],a),';
		}
		fn += '}';

		return new Function( 'r,s,a', fn );
	} )();

	return function combinedReducer( state, action ) {
		var nextState, i, key;

		// Assumed changed if initial state.
		if ( state === undefined ) {
			return getNextState( reducers, {}, action );
		}

		nextState = getNextState( reducers, state, action );

		// Determine whether state has changed.
		i = keys.length;
		while ( i-- ) {
			key = keys[ i ];
			if ( state[ key ] !== nextState[ key ] ) {
				// Return immediately if a changed value is encountered.
				return nextState;
			}
		}

		return state;
	};
}

module.exports = combineReducers;


/***/ }),

/***/ "ANjH":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ applyMiddleware; });
__webpack_require__.d(__webpack_exports__, "b", function() { return /* binding */ combineReducers; });
__webpack_require__.d(__webpack_exports__, "c", function() { return /* binding */ redux_createStore; });

// UNUSED EXPORTS: __DO_NOT_USE__ActionTypes, bindActionCreators, compose

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__("rePB");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread2.js


function ownKeys(object, enumerableOnly) {
  var keys = Object.keys(object);

  if (Object.getOwnPropertySymbols) {
    var symbols = Object.getOwnPropertySymbols(object);
    if (enumerableOnly) symbols = symbols.filter(function (sym) {
      return Object.getOwnPropertyDescriptor(object, sym).enumerable;
    });
    keys.push.apply(keys, symbols);
  }

  return keys;
}

function _objectSpread2(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? arguments[i] : {};

    if (i % 2) {
      ownKeys(Object(source), true).forEach(function (key) {
        Object(defineProperty["a" /* default */])(target, key, source[key]);
      });
    } else if (Object.getOwnPropertyDescriptors) {
      Object.defineProperties(target, Object.getOwnPropertyDescriptors(source));
    } else {
      ownKeys(Object(source)).forEach(function (key) {
        Object.defineProperty(target, key, Object.getOwnPropertyDescriptor(source, key));
      });
    }
  }

  return target;
}
// CONCATENATED MODULE: ./node_modules/redux/es/redux.js


/**
 * Adapted from React: https://github.com/facebook/react/blob/master/packages/shared/formatProdErrorMessage.js
 *
 * Do not require this module directly! Use normal throw error calls. These messages will be replaced with error codes
 * during build.
 * @param {number} code
 */
function formatProdErrorMessage(code) {
  return "Minified Redux error #" + code + "; visit https://redux.js.org/Errors?code=" + code + " for the full message or " + 'use the non-minified dev environment for full errors. ';
}

// Inlined version of the `symbol-observable` polyfill
var $$observable = (function () {
  return typeof Symbol === 'function' && Symbol.observable || '@@observable';
})();

/**
 * These are private action types reserved by Redux.
 * For any unknown actions, you must return the current state.
 * If the current state is undefined, you must return the initial state.
 * Do not reference these action types directly in your code.
 */
var randomString = function randomString() {
  return Math.random().toString(36).substring(7).split('').join('.');
};

var ActionTypes = {
  INIT: "@@redux/INIT" + randomString(),
  REPLACE: "@@redux/REPLACE" + randomString(),
  PROBE_UNKNOWN_ACTION: function PROBE_UNKNOWN_ACTION() {
    return "@@redux/PROBE_UNKNOWN_ACTION" + randomString();
  }
};

/**
 * @param {any} obj The object to inspect.
 * @returns {boolean} True if the argument appears to be a plain object.
 */
function isPlainObject(obj) {
  if (typeof obj !== 'object' || obj === null) return false;
  var proto = obj;

  while (Object.getPrototypeOf(proto) !== null) {
    proto = Object.getPrototypeOf(proto);
  }

  return Object.getPrototypeOf(obj) === proto;
}

// Inlined / shortened version of `kindOf` from https://github.com/jonschlinkert/kind-of
function miniKindOf(val) {
  if (val === void 0) return 'undefined';
  if (val === null) return 'null';
  var type = typeof val;

  switch (type) {
    case 'boolean':
    case 'string':
    case 'number':
    case 'symbol':
    case 'function':
      {
        return type;
      }
  }

  if (Array.isArray(val)) return 'array';
  if (isDate(val)) return 'date';
  if (isError(val)) return 'error';
  var constructorName = ctorName(val);

  switch (constructorName) {
    case 'Symbol':
    case 'Promise':
    case 'WeakMap':
    case 'WeakSet':
    case 'Map':
    case 'Set':
      return constructorName;
  } // other


  return type.slice(8, -1).toLowerCase().replace(/\s/g, '');
}

function ctorName(val) {
  return typeof val.constructor === 'function' ? val.constructor.name : null;
}

function isError(val) {
  return val instanceof Error || typeof val.message === 'string' && val.constructor && typeof val.constructor.stackTraceLimit === 'number';
}

function isDate(val) {
  if (val instanceof Date) return true;
  return typeof val.toDateString === 'function' && typeof val.getDate === 'function' && typeof val.setDate === 'function';
}

function kindOf(val) {
  var typeOfVal = typeof val;

  if (false) {}

  return typeOfVal;
}

/**
 * Creates a Redux store that holds the state tree.
 * The only way to change the data in the store is to call `dispatch()` on it.
 *
 * There should only be a single store in your app. To specify how different
 * parts of the state tree respond to actions, you may combine several reducers
 * into a single reducer function by using `combineReducers`.
 *
 * @param {Function} reducer A function that returns the next state tree, given
 * the current state tree and the action to handle.
 *
 * @param {any} [preloadedState] The initial state. You may optionally specify it
 * to hydrate the state from the server in universal apps, or to restore a
 * previously serialized user session.
 * If you use `combineReducers` to produce the root reducer function, this must be
 * an object with the same shape as `combineReducers` keys.
 *
 * @param {Function} [enhancer] The store enhancer. You may optionally specify it
 * to enhance the store with third-party capabilities such as middleware,
 * time travel, persistence, etc. The only store enhancer that ships with Redux
 * is `applyMiddleware()`.
 *
 * @returns {Store} A Redux store that lets you read the state, dispatch actions
 * and subscribe to changes.
 */

function redux_createStore(reducer, preloadedState, enhancer) {
  var _ref2;

  if (typeof preloadedState === 'function' && typeof enhancer === 'function' || typeof enhancer === 'function' && typeof arguments[3] === 'function') {
    throw new Error( true ? formatProdErrorMessage(0) : undefined);
  }

  if (typeof preloadedState === 'function' && typeof enhancer === 'undefined') {
    enhancer = preloadedState;
    preloadedState = undefined;
  }

  if (typeof enhancer !== 'undefined') {
    if (typeof enhancer !== 'function') {
      throw new Error( true ? formatProdErrorMessage(1) : undefined);
    }

    return enhancer(redux_createStore)(reducer, preloadedState);
  }

  if (typeof reducer !== 'function') {
    throw new Error( true ? formatProdErrorMessage(2) : undefined);
  }

  var currentReducer = reducer;
  var currentState = preloadedState;
  var currentListeners = [];
  var nextListeners = currentListeners;
  var isDispatching = false;
  /**
   * This makes a shallow copy of currentListeners so we can use
   * nextListeners as a temporary list while dispatching.
   *
   * This prevents any bugs around consumers calling
   * subscribe/unsubscribe in the middle of a dispatch.
   */

  function ensureCanMutateNextListeners() {
    if (nextListeners === currentListeners) {
      nextListeners = currentListeners.slice();
    }
  }
  /**
   * Reads the state tree managed by the store.
   *
   * @returns {any} The current state tree of your application.
   */


  function getState() {
    if (isDispatching) {
      throw new Error( true ? formatProdErrorMessage(3) : undefined);
    }

    return currentState;
  }
  /**
   * Adds a change listener. It will be called any time an action is dispatched,
   * and some part of the state tree may potentially have changed. You may then
   * call `getState()` to read the current state tree inside the callback.
   *
   * You may call `dispatch()` from a change listener, with the following
   * caveats:
   *
   * 1. The subscriptions are snapshotted just before every `dispatch()` call.
   * If you subscribe or unsubscribe while the listeners are being invoked, this
   * will not have any effect on the `dispatch()` that is currently in progress.
   * However, the next `dispatch()` call, whether nested or not, will use a more
   * recent snapshot of the subscription list.
   *
   * 2. The listener should not expect to see all state changes, as the state
   * might have been updated multiple times during a nested `dispatch()` before
   * the listener is called. It is, however, guaranteed that all subscribers
   * registered before the `dispatch()` started will be called with the latest
   * state by the time it exits.
   *
   * @param {Function} listener A callback to be invoked on every dispatch.
   * @returns {Function} A function to remove this change listener.
   */


  function subscribe(listener) {
    if (typeof listener !== 'function') {
      throw new Error( true ? formatProdErrorMessage(4) : undefined);
    }

    if (isDispatching) {
      throw new Error( true ? formatProdErrorMessage(5) : undefined);
    }

    var isSubscribed = true;
    ensureCanMutateNextListeners();
    nextListeners.push(listener);
    return function unsubscribe() {
      if (!isSubscribed) {
        return;
      }

      if (isDispatching) {
        throw new Error( true ? formatProdErrorMessage(6) : undefined);
      }

      isSubscribed = false;
      ensureCanMutateNextListeners();
      var index = nextListeners.indexOf(listener);
      nextListeners.splice(index, 1);
      currentListeners = null;
    };
  }
  /**
   * Dispatches an action. It is the only way to trigger a state change.
   *
   * The `reducer` function, used to create the store, will be called with the
   * current state tree and the given `action`. Its return value will
   * be considered the **next** state of the tree, and the change listeners
   * will be notified.
   *
   * The base implementation only supports plain object actions. If you want to
   * dispatch a Promise, an Observable, a thunk, or something else, you need to
   * wrap your store creating function into the corresponding middleware. For
   * example, see the documentation for the `redux-thunk` package. Even the
   * middleware will eventually dispatch plain object actions using this method.
   *
   * @param {Object} action A plain object representing “what changed”. It is
   * a good idea to keep actions serializable so you can record and replay user
   * sessions, or use the time travelling `redux-devtools`. An action must have
   * a `type` property which may not be `undefined`. It is a good idea to use
   * string constants for action types.
   *
   * @returns {Object} For convenience, the same action object you dispatched.
   *
   * Note that, if you use a custom middleware, it may wrap `dispatch()` to
   * return something else (for example, a Promise you can await).
   */


  function dispatch(action) {
    if (!isPlainObject(action)) {
      throw new Error( true ? formatProdErrorMessage(7) : undefined);
    }

    if (typeof action.type === 'undefined') {
      throw new Error( true ? formatProdErrorMessage(8) : undefined);
    }

    if (isDispatching) {
      throw new Error( true ? formatProdErrorMessage(9) : undefined);
    }

    try {
      isDispatching = true;
      currentState = currentReducer(currentState, action);
    } finally {
      isDispatching = false;
    }

    var listeners = currentListeners = nextListeners;

    for (var i = 0; i < listeners.length; i++) {
      var listener = listeners[i];
      listener();
    }

    return action;
  }
  /**
   * Replaces the reducer currently used by the store to calculate the state.
   *
   * You might need this if your app implements code splitting and you want to
   * load some of the reducers dynamically. You might also need this if you
   * implement a hot reloading mechanism for Redux.
   *
   * @param {Function} nextReducer The reducer for the store to use instead.
   * @returns {void}
   */


  function replaceReducer(nextReducer) {
    if (typeof nextReducer !== 'function') {
      throw new Error( true ? formatProdErrorMessage(10) : undefined);
    }

    currentReducer = nextReducer; // This action has a similiar effect to ActionTypes.INIT.
    // Any reducers that existed in both the new and old rootReducer
    // will receive the previous state. This effectively populates
    // the new state tree with any relevant data from the old one.

    dispatch({
      type: ActionTypes.REPLACE
    });
  }
  /**
   * Interoperability point for observable/reactive libraries.
   * @returns {observable} A minimal observable of state changes.
   * For more information, see the observable proposal:
   * https://github.com/tc39/proposal-observable
   */


  function observable() {
    var _ref;

    var outerSubscribe = subscribe;
    return _ref = {
      /**
       * The minimal observable subscription method.
       * @param {Object} observer Any object that can be used as an observer.
       * The observer object should have a `next` method.
       * @returns {subscription} An object with an `unsubscribe` method that can
       * be used to unsubscribe the observable from the store, and prevent further
       * emission of values from the observable.
       */
      subscribe: function subscribe(observer) {
        if (typeof observer !== 'object' || observer === null) {
          throw new Error( true ? formatProdErrorMessage(11) : undefined);
        }

        function observeState() {
          if (observer.next) {
            observer.next(getState());
          }
        }

        observeState();
        var unsubscribe = outerSubscribe(observeState);
        return {
          unsubscribe: unsubscribe
        };
      }
    }, _ref[$$observable] = function () {
      return this;
    }, _ref;
  } // When a store is created, an "INIT" action is dispatched so that every
  // reducer returns their initial state. This effectively populates
  // the initial state tree.


  dispatch({
    type: ActionTypes.INIT
  });
  return _ref2 = {
    dispatch: dispatch,
    subscribe: subscribe,
    getState: getState,
    replaceReducer: replaceReducer
  }, _ref2[$$observable] = observable, _ref2;
}

/**
 * Prints a warning in the console if it exists.
 *
 * @param {String} message The warning message.
 * @returns {void}
 */
function warning(message) {
  /* eslint-disable no-console */
  if (typeof console !== 'undefined' && typeof console.error === 'function') {
    console.error(message);
  }
  /* eslint-enable no-console */


  try {
    // This error was thrown as a convenience so that if you enable
    // "break on all exceptions" in your console,
    // it would pause the execution at this line.
    throw new Error(message);
  } catch (e) {} // eslint-disable-line no-empty

}

function getUnexpectedStateShapeWarningMessage(inputState, reducers, action, unexpectedKeyCache) {
  var reducerKeys = Object.keys(reducers);
  var argumentName = action && action.type === ActionTypes.INIT ? 'preloadedState argument passed to createStore' : 'previous state received by the reducer';

  if (reducerKeys.length === 0) {
    return 'Store does not have a valid reducer. Make sure the argument passed ' + 'to combineReducers is an object whose values are reducers.';
  }

  if (!isPlainObject(inputState)) {
    return "The " + argumentName + " has unexpected type of \"" + kindOf(inputState) + "\". Expected argument to be an object with the following " + ("keys: \"" + reducerKeys.join('", "') + "\"");
  }

  var unexpectedKeys = Object.keys(inputState).filter(function (key) {
    return !reducers.hasOwnProperty(key) && !unexpectedKeyCache[key];
  });
  unexpectedKeys.forEach(function (key) {
    unexpectedKeyCache[key] = true;
  });
  if (action && action.type === ActionTypes.REPLACE) return;

  if (unexpectedKeys.length > 0) {
    return "Unexpected " + (unexpectedKeys.length > 1 ? 'keys' : 'key') + " " + ("\"" + unexpectedKeys.join('", "') + "\" found in " + argumentName + ". ") + "Expected to find one of the known reducer keys instead: " + ("\"" + reducerKeys.join('", "') + "\". Unexpected keys will be ignored.");
  }
}

function assertReducerShape(reducers) {
  Object.keys(reducers).forEach(function (key) {
    var reducer = reducers[key];
    var initialState = reducer(undefined, {
      type: ActionTypes.INIT
    });

    if (typeof initialState === 'undefined') {
      throw new Error( true ? formatProdErrorMessage(12) : undefined);
    }

    if (typeof reducer(undefined, {
      type: ActionTypes.PROBE_UNKNOWN_ACTION()
    }) === 'undefined') {
      throw new Error( true ? formatProdErrorMessage(13) : undefined);
    }
  });
}
/**
 * Turns an object whose values are different reducer functions, into a single
 * reducer function. It will call every child reducer, and gather their results
 * into a single state object, whose keys correspond to the keys of the passed
 * reducer functions.
 *
 * @param {Object} reducers An object whose values correspond to different
 * reducer functions that need to be combined into one. One handy way to obtain
 * it is to use ES6 `import * as reducers` syntax. The reducers may never return
 * undefined for any action. Instead, they should return their initial state
 * if the state passed to them was undefined, and the current state for any
 * unrecognized action.
 *
 * @returns {Function} A reducer function that invokes every reducer inside the
 * passed object, and builds a state object with the same shape.
 */


function combineReducers(reducers) {
  var reducerKeys = Object.keys(reducers);
  var finalReducers = {};

  for (var i = 0; i < reducerKeys.length; i++) {
    var key = reducerKeys[i];

    if (false) {}

    if (typeof reducers[key] === 'function') {
      finalReducers[key] = reducers[key];
    }
  }

  var finalReducerKeys = Object.keys(finalReducers); // This is used to make sure we don't warn about the same
  // keys multiple times.

  var unexpectedKeyCache;

  if (false) {}

  var shapeAssertionError;

  try {
    assertReducerShape(finalReducers);
  } catch (e) {
    shapeAssertionError = e;
  }

  return function combination(state, action) {
    if (state === void 0) {
      state = {};
    }

    if (shapeAssertionError) {
      throw shapeAssertionError;
    }

    if (false) { var warningMessage; }

    var hasChanged = false;
    var nextState = {};

    for (var _i = 0; _i < finalReducerKeys.length; _i++) {
      var _key = finalReducerKeys[_i];
      var reducer = finalReducers[_key];
      var previousStateForKey = state[_key];
      var nextStateForKey = reducer(previousStateForKey, action);

      if (typeof nextStateForKey === 'undefined') {
        var actionType = action && action.type;
        throw new Error( true ? formatProdErrorMessage(14) : undefined);
      }

      nextState[_key] = nextStateForKey;
      hasChanged = hasChanged || nextStateForKey !== previousStateForKey;
    }

    hasChanged = hasChanged || finalReducerKeys.length !== Object.keys(state).length;
    return hasChanged ? nextState : state;
  };
}

function bindActionCreator(actionCreator, dispatch) {
  return function () {
    return dispatch(actionCreator.apply(this, arguments));
  };
}
/**
 * Turns an object whose values are action creators, into an object with the
 * same keys, but with every function wrapped into a `dispatch` call so they
 * may be invoked directly. This is just a convenience method, as you can call
 * `store.dispatch(MyActionCreators.doSomething())` yourself just fine.
 *
 * For convenience, you can also pass an action creator as the first argument,
 * and get a dispatch wrapped function in return.
 *
 * @param {Function|Object} actionCreators An object whose values are action
 * creator functions. One handy way to obtain it is to use ES6 `import * as`
 * syntax. You may also pass a single function.
 *
 * @param {Function} dispatch The `dispatch` function available on your Redux
 * store.
 *
 * @returns {Function|Object} The object mimicking the original object, but with
 * every action creator wrapped into the `dispatch` call. If you passed a
 * function as `actionCreators`, the return value will also be a single
 * function.
 */


function bindActionCreators(actionCreators, dispatch) {
  if (typeof actionCreators === 'function') {
    return bindActionCreator(actionCreators, dispatch);
  }

  if (typeof actionCreators !== 'object' || actionCreators === null) {
    throw new Error( true ? formatProdErrorMessage(16) : undefined);
  }

  var boundActionCreators = {};

  for (var key in actionCreators) {
    var actionCreator = actionCreators[key];

    if (typeof actionCreator === 'function') {
      boundActionCreators[key] = bindActionCreator(actionCreator, dispatch);
    }
  }

  return boundActionCreators;
}

/**
 * Composes single-argument functions from right to left. The rightmost
 * function can take multiple arguments as it provides the signature for
 * the resulting composite function.
 *
 * @param {...Function} funcs The functions to compose.
 * @returns {Function} A function obtained by composing the argument functions
 * from right to left. For example, compose(f, g, h) is identical to doing
 * (...args) => f(g(h(...args))).
 */
function compose() {
  for (var _len = arguments.length, funcs = new Array(_len), _key = 0; _key < _len; _key++) {
    funcs[_key] = arguments[_key];
  }

  if (funcs.length === 0) {
    return function (arg) {
      return arg;
    };
  }

  if (funcs.length === 1) {
    return funcs[0];
  }

  return funcs.reduce(function (a, b) {
    return function () {
      return a(b.apply(void 0, arguments));
    };
  });
}

/**
 * Creates a store enhancer that applies middleware to the dispatch method
 * of the Redux store. This is handy for a variety of tasks, such as expressing
 * asynchronous actions in a concise manner, or logging every action payload.
 *
 * See `redux-thunk` package as an example of the Redux middleware.
 *
 * Because middleware is potentially asynchronous, this should be the first
 * store enhancer in the composition chain.
 *
 * Note that each middleware will be given the `dispatch` and `getState` functions
 * as named arguments.
 *
 * @param {...Function} middlewares The middleware chain to be applied.
 * @returns {Function} A store enhancer applying the middleware.
 */

function applyMiddleware() {
  for (var _len = arguments.length, middlewares = new Array(_len), _key = 0; _key < _len; _key++) {
    middlewares[_key] = arguments[_key];
  }

  return function (createStore) {
    return function () {
      var store = createStore.apply(void 0, arguments);

      var _dispatch = function dispatch() {
        throw new Error( true ? formatProdErrorMessage(15) : undefined);
      };

      var middlewareAPI = {
        getState: store.getState,
        dispatch: function dispatch() {
          return _dispatch.apply(void 0, arguments);
        }
      };
      var chain = middlewares.map(function (middleware) {
        return middleware(middlewareAPI);
      });
      _dispatch = compose.apply(void 0, chain)(store.dispatch);
      return _objectSpread2(_objectSpread2({}, store), {}, {
        dispatch: _dispatch
      });
    };
  };
}

/*
 * This is a dummy function to check if the function name has been altered by minification.
 * If the function has been minified and NODE_ENV !== 'production', warn the user.
 */

function isCrushed() {}

if (false) {}




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

/***/ "FtRg":
/***/ (function(module, exports, __webpack_require__) {

"use strict";


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


/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ "HaE+":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _asyncToGenerator; });
function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) {
  try {
    var info = gen[key](arg);
    var value = info.value;
  } catch (error) {
    reject(error);
    return;
  }

  if (info.done) {
    resolve(value);
  } else {
    Promise.resolve(value).then(_next, _throw);
  }
}

function _asyncToGenerator(fn) {
  return function () {
    var self = this,
        args = arguments;
    return new Promise(function (resolve, reject) {
      var gen = fn.apply(self, args);

      function _next(value) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value);
      }

      function _throw(err) {
        asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err);
      }

      _next(undefined);
    });
  };
}

/***/ }),

/***/ "JX7q":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _assertThisInitialized; });
function _assertThisInitialized(self) {
  if (self === void 0) {
    throw new ReferenceError("this hasn't been initialised - super() hasn't been called");
  }

  return self;
}

/***/ }),

/***/ "Ji7U":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _inherits; });

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/setPrototypeOf.js
function _setPrototypeOf(o, p) {
  _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) {
    o.__proto__ = p;
    return o;
  };

  return _setPrototypeOf(o, p);
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js

function _inherits(subClass, superClass) {
  if (typeof superClass !== "function" && superClass !== null) {
    throw new TypeError("Super expression must either be null or a function");
  }

  subClass.prototype = Object.create(superClass && superClass.prototype, {
    constructor: {
      value: subClass,
      writable: true,
      configurable: true
    }
  });
  if (superClass) _setPrototypeOf(subClass, superClass);
}

/***/ }),

/***/ "JlUD":
/***/ (function(module, exports) {

module.exports = isPromise;
module.exports.default = isPromise;

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
function isPromise(obj) {
  return !!obj && (typeof obj === 'object' || typeof obj === 'function') && typeof obj.then === 'function';
}

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/promise-middleware.js
/**
 * External dependencies
 */

/**
 * Simplest possible promise redux middleware.
 *
 * @type {import('redux').Middleware}
 */

const promiseMiddleware = () => next => action => {
  if (isPromise(action)) {
    return action.then(resolvedAction => {
      if (resolvedAction) {
        return next(resolvedAction);
      }
    });
  }

  return next(action);
};

/* harmony default export */ const promise_middleware = (promiseMiddleware);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/store/index.js
const coreDataStore = {
  name: 'core/data',

  instantiate(registry) {
    const getCoreDataSelector = selectorName => (key, ...args) => {
      return registry.select(key)[selectorName](...args);
    };

    const getCoreDataAction = actionName => (key, ...args) => {
      return registry.dispatch(key)[actionName](...args);
    };

    return {
      getSelectors() {
        return Object.fromEntries(['getIsResolving', 'hasStartedResolution', 'hasFinishedResolution', 'isResolving', 'getCachedResolvers'].map(selectorName => [selectorName, getCoreDataSelector(selectorName)]));
      },

      getActions() {
        return Object.fromEntries(['startResolution', 'finishResolution', 'invalidateResolution', 'invalidateResolutionForStore', 'invalidateResolutionForStoreSelector'].map(actionName => [actionName, getCoreDataAction(actionName)]));
      },

      subscribe() {
        // There's no reasons to trigger any listener when we subscribe to this store
        // because there's no state stored in this store that need to retrigger selectors
        // if a change happens, the corresponding store where the tracking stated live
        // would have already triggered a "subscribe" call.
        return () => () => {};
      }

    };
  }

};
/* harmony default export */ const store = (coreDataStore);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/resolvers-cache-middleware.js
/**
 * Internal dependencies
 */

/** @typedef {import('./registry').WPDataRegistry} WPDataRegistry */

/**
 * Creates a middleware handling resolvers cache invalidation.
 *
 * @param {WPDataRegistry} registry   The registry reference for which to create
 *                                    the middleware.
 * @param {string}         reducerKey The namespace for which to create the
 *                                    middleware.
 *
 * @return {Function} Middleware function.
 */

const createResolversCacheMiddleware = (registry, reducerKey) => () => next => action => {
  const resolvers = registry.select(store).getCachedResolvers(reducerKey);
  Object.entries(resolvers).forEach(([selectorName, resolversByArgs]) => {
    const resolver = registry.stores?.[reducerKey]?.resolvers?.[selectorName];

    if (!resolver || !resolver.shouldInvalidate) {
      return;
    }

    resolversByArgs.forEach((value, args) => {
      // resolversByArgs is the map Map([ args ] => boolean) storing the cache resolution status for a given selector.
      // If the value is "finished" or "error" it means this resolver has finished its resolution which means we need
      // to invalidate it, if it's true it means it's inflight and the invalidation is not necessary.
      if (value?.status !== 'finished' && value?.status !== 'error' || !resolver.shouldInvalidate(action, ...args)) {
        return;
      } // Trigger cache invalidation


      registry.dispatch(store).invalidateResolution(reducerKey, selectorName, args);
    });
  });
  return next(action);
};

/* harmony default export */ const resolvers_cache_middleware = (createResolversCacheMiddleware);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/redux-store/thunk-middleware.js
function createThunkMiddleware(args) {
  return () => next => action => {
    if (typeof action === 'function') {
      return action(args);
    }

    return next(action);
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/redux-store/metadata/utils.js
/**
 * External dependencies
 */

/**
 * Higher-order reducer creator which creates a combined reducer object, keyed
 * by a property on the action object.
 *
 * @param actionProperty Action property by which to key object.
 * @return Higher-order reducer.
 */
const onSubKey = actionProperty => reducer => (state = {}, action) => {
  // Retrieve subkey from action. Do not track if undefined; useful for cases
  // where reducer is scoped by action shape.
  const key = action[actionProperty];

  if (key === undefined) {
    return state;
  } // Avoid updating state if unchanged. Note that this also accounts for a
  // reducer which returns undefined on a key which is not yet tracked.


  const nextKeyState = reducer(state[key], action);

  if (nextKeyState === state[key]) {
    return state;
  }

  return { ...state,
    [key]: nextKeyState
  };
};
/**
 * Normalize selector argument array by defaulting `undefined` value to an empty array
 * and removing trailing `undefined` values.
 *
 * @param args Selector argument array
 * @return Normalized state key array
 */

function selectorArgsToStateKey(args) {
  if (args === undefined || args === null) {
    return [];
  }

  const len = args.length;
  let idx = len;

  while (idx > 0 && args[idx - 1] === undefined) {
    idx--;
  }

  return idx === len ? args : args.slice(0, idx);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/redux-store/metadata/reducer.js
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Reducer function returning next state for selector resolution of
 * subkeys, object form:
 *
 *  selectorName -> EquivalentKeyMap<Array,boolean>
 */
const subKeysIsResolved = onSubKey('selectorName')((state = new (equivalent_key_map_default())(), action) => {
  switch (action.type) {
    case 'START_RESOLUTION':
      {
        const nextState = new (equivalent_key_map_default())(state);
        nextState.set(selectorArgsToStateKey(action.args), {
          status: 'resolving'
        });
        return nextState;
      }

    case 'FINISH_RESOLUTION':
      {
        const nextState = new (equivalent_key_map_default())(state);
        nextState.set(selectorArgsToStateKey(action.args), {
          status: 'finished'
        });
        return nextState;
      }

    case 'FAIL_RESOLUTION':
      {
        const nextState = new (equivalent_key_map_default())(state);
        nextState.set(selectorArgsToStateKey(action.args), {
          status: 'error',
          error: action.error
        });
        return nextState;
      }

    case 'START_RESOLUTIONS':
      {
        const nextState = new (equivalent_key_map_default())(state);

        for (const resolutionArgs of action.args) {
          nextState.set(selectorArgsToStateKey(resolutionArgs), {
            status: 'resolving'
          });
        }

        return nextState;
      }

    case 'FINISH_RESOLUTIONS':
      {
        const nextState = new (equivalent_key_map_default())(state);

        for (const resolutionArgs of action.args) {
          nextState.set(selectorArgsToStateKey(resolutionArgs), {
            status: 'finished'
          });
        }

        return nextState;
      }

    case 'FAIL_RESOLUTIONS':
      {
        const nextState = new (equivalent_key_map_default())(state);
        action.args.forEach((resolutionArgs, idx) => {
          const resolutionState = {
            status: 'error',
            error: undefined
          };
          const error = action.errors[idx];

          if (error) {
            resolutionState.error = error;
          }

          nextState.set(selectorArgsToStateKey(resolutionArgs), resolutionState);
        });
        return nextState;
      }

    case 'INVALIDATE_RESOLUTION':
      {
        const nextState = new (equivalent_key_map_default())(state);
        nextState.delete(selectorArgsToStateKey(action.args));
        return nextState;
      }
  }

  return state;
});
/**
 * Reducer function returning next state for selector resolution, object form:
 *
 *   selectorName -> EquivalentKeyMap<Array, boolean>
 *
 * @param state  Current state.
 * @param action Dispatched action.
 *
 * @return Next state.
 */

const isResolved = (state = {}, action) => {
  switch (action.type) {
    case 'INVALIDATE_RESOLUTION_FOR_STORE':
      return {};

    case 'INVALIDATE_RESOLUTION_FOR_STORE_SELECTOR':
      {
        if (action.selectorName in state) {
          const {
            [action.selectorName]: removedSelector,
            ...restState
          } = state;
          return restState;
        }

        return state;
      }

    case 'START_RESOLUTION':
    case 'FINISH_RESOLUTION':
    case 'FAIL_RESOLUTION':
    case 'START_RESOLUTIONS':
    case 'FINISH_RESOLUTIONS':
    case 'FAIL_RESOLUTIONS':
    case 'INVALIDATE_RESOLUTION':
      return subKeysIsResolved(state, action);
  }

  return state;
};

/* harmony default export */ const metadata_reducer = (isResolved);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/redux-store/metadata/selectors.js
/**
 * Internal dependencies
 */

/** @typedef {Record<string, import('./reducer').State>} State */

/** @typedef {import('./reducer').StateValue} StateValue */

/** @typedef {import('./reducer').Status} Status */

/**
 * Returns the raw resolution state value for a given selector name,
 * and arguments set. May be undefined if the selector has never been resolved
 * or not resolved for the given set of arguments, otherwise true or false for
 * resolution started and completed respectively.
 *
 * @param {State}      state        Data state.
 * @param {string}     selectorName Selector name.
 * @param {unknown[]?} args         Arguments passed to selector.
 *
 * @return {StateValue|undefined} isResolving value.
 */

function getResolutionState(state, selectorName, args) {
  const map = state[selectorName];

  if (!map) {
    return;
  }

  return map.get(selectorArgsToStateKey(args));
}
/**
 * Returns the raw `isResolving` value for a given selector name,
 * and arguments set. May be undefined if the selector has never been resolved
 * or not resolved for the given set of arguments, otherwise true or false for
 * resolution started and completed respectively.
 *
 * @param {State}      state        Data state.
 * @param {string}     selectorName Selector name.
 * @param {unknown[]?} args         Arguments passed to selector.
 *
 * @return {boolean | undefined} isResolving value.
 */

function getIsResolving(state, selectorName, args) {
  const resolutionState = getResolutionState(state, selectorName, args);
  return resolutionState && resolutionState.status === 'resolving';
}
/**
 * Returns true if resolution has already been triggered for a given
 * selector name, and arguments set.
 *
 * @param {State}      state        Data state.
 * @param {string}     selectorName Selector name.
 * @param {unknown[]?} args         Arguments passed to selector.
 *
 * @return {boolean} Whether resolution has been triggered.
 */

function hasStartedResolution(state, selectorName, args) {
  return getResolutionState(state, selectorName, args) !== undefined;
}
/**
 * Returns true if resolution has completed for a given selector
 * name, and arguments set.
 *
 * @param {State}      state        Data state.
 * @param {string}     selectorName Selector name.
 * @param {unknown[]?} args         Arguments passed to selector.
 *
 * @return {boolean} Whether resolution has completed.
 */

function hasFinishedResolution(state, selectorName, args) {
  const status = getResolutionState(state, selectorName, args)?.status;
  return status === 'finished' || status === 'error';
}
/**
 * Returns true if resolution has failed for a given selector
 * name, and arguments set.
 *
 * @param {State}      state        Data state.
 * @param {string}     selectorName Selector name.
 * @param {unknown[]?} args         Arguments passed to selector.
 *
 * @return {boolean} Has resolution failed
 */

function hasResolutionFailed(state, selectorName, args) {
  return getResolutionState(state, selectorName, args)?.status === 'error';
}
/**
 * Returns the resolution error for a given selector name, and arguments set.
 * Note it may be of an Error type, but may also be null, undefined, or anything else
 * that can be `throw`-n.
 *
 * @param {State}      state        Data state.
 * @param {string}     selectorName Selector name.
 * @param {unknown[]?} args         Arguments passed to selector.
 *
 * @return {Error|unknown} Last resolution error
 */

function getResolutionError(state, selectorName, args) {
  const resolutionState = getResolutionState(state, selectorName, args);
  return resolutionState?.status === 'error' ? resolutionState.error : null;
}
/**
 * Returns true if resolution has been triggered but has not yet completed for
 * a given selector name, and arguments set.
 *
 * @param {State}      state        Data state.
 * @param {string}     selectorName Selector name.
 * @param {unknown[]?} args         Arguments passed to selector.
 *
 * @return {boolean} Whether resolution is in progress.
 */

function isResolving(state, selectorName, args) {
  return getResolutionState(state, selectorName, args)?.status === 'resolving';
}
/**
 * Returns the list of the cached resolvers.
 *
 * @param {State} state Data state.
 *
 * @return {State} Resolvers mapped by args and selectorName.
 */

function getCachedResolvers(state) {
  return state;
}
/**
 * Whether the store has any currently resolving selectors.
 *
 * @param {State} state Data state.
 *
 * @return {boolean} True if one or more selectors are resolving, false otherwise.
 */

function hasResolvingSelectors(state) {
  return Object.values(state).some(selectorState =>
  /**
   * This uses the internal `_map` property of `EquivalentKeyMap` for
   * optimization purposes, since the `EquivalentKeyMap` implementation
   * does not support a `.values()` implementation.
   *
   * @see https://github.com/aduth/equivalent-key-map
   */
  Array.from(selectorState._map.values()).some(resolution => resolution[1]?.status === 'resolving'));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/redux-store/metadata/actions.js
/**
 * Returns an action object used in signalling that selector resolution has
 * started.
 *
 * @param {string}    selectorName Name of selector for which resolver triggered.
 * @param {unknown[]} args         Arguments to associate for uniqueness.
 *
 * @return {{ type: 'START_RESOLUTION', selectorName: string, args: unknown[] }} Action object.
 */
function startResolution(selectorName, args) {
  return {
    type: 'START_RESOLUTION',
    selectorName,
    args
  };
}
/**
 * Returns an action object used in signalling that selector resolution has
 * completed.
 *
 * @param {string}    selectorName Name of selector for which resolver triggered.
 * @param {unknown[]} args         Arguments to associate for uniqueness.
 *
 * @return {{ type: 'FINISH_RESOLUTION', selectorName: string, args: unknown[] }} Action object.
 */

function finishResolution(selectorName, args) {
  return {
    type: 'FINISH_RESOLUTION',
    selectorName,
    args
  };
}
/**
 * Returns an action object used in signalling that selector resolution has
 * failed.
 *
 * @param {string}        selectorName Name of selector for which resolver triggered.
 * @param {unknown[]}     args         Arguments to associate for uniqueness.
 * @param {Error|unknown} error        The error that caused the failure.
 *
 * @return {{ type: 'FAIL_RESOLUTION', selectorName: string, args: unknown[], error: Error|unknown }} Action object.
 */

function failResolution(selectorName, args, error) {
  return {
    type: 'FAIL_RESOLUTION',
    selectorName,
    args,
    error
  };
}
/**
 * Returns an action object used in signalling that a batch of selector resolutions has
 * started.
 *
 * @param {string}      selectorName Name of selector for which resolver triggered.
 * @param {unknown[][]} args         Array of arguments to associate for uniqueness, each item
 *                                   is associated to a resolution.
 *
 * @return {{ type: 'START_RESOLUTIONS', selectorName: string, args: unknown[][] }} Action object.
 */

function startResolutions(selectorName, args) {
  return {
    type: 'START_RESOLUTIONS',
    selectorName,
    args
  };
}
/**
 * Returns an action object used in signalling that a batch of selector resolutions has
 * completed.
 *
 * @param {string}      selectorName Name of selector for which resolver triggered.
 * @param {unknown[][]} args         Array of arguments to associate for uniqueness, each item
 *                                   is associated to a resolution.
 *
 * @return {{ type: 'FINISH_RESOLUTIONS', selectorName: string, args: unknown[][] }} Action object.
 */

function finishResolutions(selectorName, args) {
  return {
    type: 'FINISH_RESOLUTIONS',
    selectorName,
    args
  };
}
/**
 * Returns an action object used in signalling that a batch of selector resolutions has
 * completed and at least one of them has failed.
 *
 * @param {string}            selectorName Name of selector for which resolver triggered.
 * @param {unknown[]}         args         Array of arguments to associate for uniqueness, each item
 *                                         is associated to a resolution.
 * @param {(Error|unknown)[]} errors       Array of errors to associate for uniqueness, each item
 *                                         is associated to a resolution.
 * @return {{ type: 'FAIL_RESOLUTIONS', selectorName: string, args: unknown[], errors: Array<Error|unknown> }} Action object.
 */

function failResolutions(selectorName, args, errors) {
  return {
    type: 'FAIL_RESOLUTIONS',
    selectorName,
    args,
    errors
  };
}
/**
 * Returns an action object used in signalling that we should invalidate the resolution cache.
 *
 * @param {string}    selectorName Name of selector for which resolver should be invalidated.
 * @param {unknown[]} args         Arguments to associate for uniqueness.
 *
 * @return {{ type: 'INVALIDATE_RESOLUTION', selectorName: string, args: any[] }} Action object.
 */

function invalidateResolution(selectorName, args) {
  return {
    type: 'INVALIDATE_RESOLUTION',
    selectorName,
    args
  };
}
/**
 * Returns an action object used in signalling that the resolution
 * should be invalidated.
 *
 * @return {{ type: 'INVALIDATE_RESOLUTION_FOR_STORE' }} Action object.
 */

function invalidateResolutionForStore() {
  return {
    type: 'INVALIDATE_RESOLUTION_FOR_STORE'
  };
}
/**
 * Returns an action object used in signalling that the resolution cache for a
 * given selectorName should be invalidated.
 *
 * @param {string} selectorName Name of selector for which all resolvers should
 *                              be invalidated.
 *
 * @return  {{ type: 'INVALIDATE_RESOLUTION_FOR_STORE_SELECTOR', selectorName: string }} Action object.
 */

function invalidateResolutionForStoreSelector(selectorName) {
  return {
    type: 'INVALIDATE_RESOLUTION_FOR_STORE_SELECTOR',
    selectorName
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/redux-store/index.js
=======

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

/***/ "U8pU":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _typeof; });
function _typeof(obj) {
  "@babel/helpers - typeof";

  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function _typeof(obj) {
      return typeof obj;
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

/***/ }),

/***/ "XIDh":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["reduxRoutine"]; }());

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

/***/ "foSv":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _getPrototypeOf; });
function _getPrototypeOf(o) {
  _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) {
    return o.__proto__ || Object.getPrototypeOf(o);
  };
  return _getPrototypeOf(o);
}

/***/ }),

/***/ "md7G":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _possibleConstructorReturn; });
/* harmony import */ var _babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("U8pU");
/* harmony import */ var _babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__("JX7q");


function _possibleConstructorReturn(self, call) {
  if (call && (Object(_babel_runtime_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(call) === "object" || typeof call === "function")) {
    return call;
  }

  return Object(_babel_runtime_helpers_esm_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(self);
}

/***/ }),

/***/ "pfJ3":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "withSelect", function() { return /* reexport */ with_select; });
__webpack_require__.d(__webpack_exports__, "withDispatch", function() { return /* reexport */ with_dispatch; });
__webpack_require__.d(__webpack_exports__, "RegistryProvider", function() { return /* reexport */ registry_provider; });
__webpack_require__.d(__webpack_exports__, "RegistryConsumer", function() { return /* reexport */ RegistryConsumer; });
__webpack_require__.d(__webpack_exports__, "createRegistry", function() { return /* reexport */ createRegistry; });
__webpack_require__.d(__webpack_exports__, "plugins", function() { return /* reexport */ plugins_namespaceObject; });
__webpack_require__.d(__webpack_exports__, "combineReducers", function() { return /* reexport */ turbo_combine_reducers_default.a; });
__webpack_require__.d(__webpack_exports__, "select", function() { return /* binding */ build_module_select; });
__webpack_require__.d(__webpack_exports__, "dispatch", function() { return /* binding */ build_module_dispatch; });
__webpack_require__.d(__webpack_exports__, "subscribe", function() { return /* binding */ build_module_subscribe; });
__webpack_require__.d(__webpack_exports__, "registerGenericStore", function() { return /* binding */ build_module_registerGenericStore; });
__webpack_require__.d(__webpack_exports__, "registerStore", function() { return /* binding */ build_module_registerStore; });
__webpack_require__.d(__webpack_exports__, "use", function() { return /* binding */ build_module_use; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/data/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "getIsResolving", function() { return getIsResolving; });
__webpack_require__.d(selectors_namespaceObject, "hasStartedResolution", function() { return hasStartedResolution; });
__webpack_require__.d(selectors_namespaceObject, "hasFinishedResolution", function() { return hasFinishedResolution; });
__webpack_require__.d(selectors_namespaceObject, "isResolving", function() { return isResolving; });
__webpack_require__.d(selectors_namespaceObject, "getCachedResolvers", function() { return getCachedResolvers; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/data/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "startResolution", function() { return startResolution; });
__webpack_require__.d(actions_namespaceObject, "finishResolution", function() { return finishResolution; });
__webpack_require__.d(actions_namespaceObject, "invalidateResolution", function() { return invalidateResolution; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/data/build-module/plugins/index.js
var plugins_namespaceObject = {};
__webpack_require__.r(plugins_namespaceObject);
__webpack_require__.d(plugins_namespaceObject, "controls", function() { return controls; });
__webpack_require__.d(plugins_namespaceObject, "persistence", function() { return plugins_persistence; });

// EXTERNAL MODULE: ./node_modules/turbo-combine-reducers/index.js
var turbo_combine_reducers = __webpack_require__("8mpt");
var turbo_combine_reducers_default = /*#__PURE__*/__webpack_require__.n(turbo_combine_reducers);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/slicedToArray.js + 1 modules
var slicedToArray = __webpack_require__("ODXe");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__("vpQ4");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/asyncToGenerator.js
var asyncToGenerator = __webpack_require__("HaE+");

// EXTERNAL MODULE: ./node_modules/redux/es/redux.js + 1 modules
var redux = __webpack_require__("ANjH");

// EXTERNAL MODULE: ./node_modules/is-promise/index.js
var is_promise = __webpack_require__("JlUD");
var is_promise_default = /*#__PURE__*/__webpack_require__.n(is_promise);

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/promise-middleware.js
/**
 * External dependencies
 */

/**
 * Simplest possible promise redux middleware.
 *
 * @return {function} middleware.
 */

var promise_middleware_promiseMiddleware = function promiseMiddleware() {
  return function (next) {
    return function (action) {
      if (is_promise_default()(action)) {
        return action.then(function (resolvedAction) {
          if (resolvedAction) {
            return next(resolvedAction);
          }
        });
      }

      return next(action);
    };
  };
};

/* harmony default export */ var promise_middleware = (promise_middleware_promiseMiddleware);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__("KQm4");

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/resolvers-cache-middleware.js



/**
 * External dependencies
 */

/**
 * creates a middleware handling resolvers cache invalidation.
 *
 * @param {Object} registry
 * @param {string} reducerKey
 *
 * @return {function} middleware
 */

var resolvers_cache_middleware_createResolversCacheMiddleware = function createResolversCacheMiddleware(registry, reducerKey) {
  return function () {
    return function (next) {
      return function (action) {
        var resolvers = registry.select('core/data').getCachedResolvers(reducerKey);
        Object.entries(resolvers).forEach(function (_ref) {
          var _ref2 = Object(slicedToArray["a" /* default */])(_ref, 2),
              selectorName = _ref2[0],
              resolversByArgs = _ref2[1];

          var resolver = Object(external_lodash_["get"])(registry.namespaces, [reducerKey, 'resolvers', selectorName]);

          if (!resolver || !resolver.shouldInvalidate) {
            return;
          }

          resolversByArgs.forEach(function (value, args) {
            // resolversByArgs is the map Map([ args ] => boolean) storing the cache resolution status for a given selector.
            // If the value is false it means this resolver has finished its resolution which means we need to invalidate it,
            // if it's true it means it's inflight and the invalidation is not necessary.
            if (value !== false || !resolver.shouldInvalidate.apply(resolver, [action].concat(Object(toConsumableArray["a" /* default */])(args)))) {
              return;
            } // Trigger cache invalidation


            registry.dispatch('core/data').invalidateResolution(reducerKey, selectorName, args);
          });
        });
        next(action);
      };
    };
  };
};

/* harmony default export */ var resolvers_cache_middleware = (resolvers_cache_middleware_createResolversCacheMiddleware);

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/namespace-store.js



>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * External dependencies
 */


<<<<<<< HEAD

/**
 * WordPress dependencies
=======
/**
 * Internal dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */



/**
<<<<<<< HEAD
 * Internal dependencies
 */









/** @typedef {import('../types').DataRegistry} DataRegistry */

/**
 * @typedef {import('../types').StoreDescriptor<C>} StoreDescriptor
 * @template {import('../types').AnyConfig} C
 */

/**
 * @typedef {import('../types').ReduxStoreConfig<State,Actions,Selectors>} ReduxStoreConfig
 * @template State
 * @template {Record<string,import('../../types').ActionCreator>} Actions
 * @template Selectors
 */

const trimUndefinedValues = array => {
  const result = [...array];

  for (let i = result.length - 1; i >= 0; i--) {
    if (result[i] === undefined) {
      result.splice(i, 1);
    }
  }

  return result;
};
/**
 * Creates a new object with the same keys, but with `callback()` called as
 * a transformer function on each of the values.
 *
 * @param {Object}   obj      The object to transform.
 * @param {Function} callback The function to transform each object value.
 * @return {Array} Transformed object.
 */


const mapValues = (obj, callback) => Object.fromEntries(Object.entries(obj !== null && obj !== void 0 ? obj : {}).map(([key, value]) => [key, callback(value, key)])); // Convert Map objects to plain objects


const mapToObject = (key, state) => {
  if (state instanceof Map) {
    return Object.fromEntries(state);
  }

  return state;
};
/**
 * Create a cache to track whether resolvers started running or not.
 *
 * @return {Object} Resolvers Cache.
 */


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

function createBindingCache(bind) {
  const cache = new WeakMap();
  return {
    get(item, itemName) {
      let boundItem = cache.get(item);

      if (!boundItem) {
        boundItem = bind(item, itemName);
        cache.set(item, boundItem);
      }

      return boundItem;
    }

  };
}
/**
 * Creates a data store descriptor for the provided Redux store configuration containing
 * properties describing reducer, actions, selectors, controls and resolvers.
 *
 * @example
 * ```js
 * import { createReduxStore } from '@wordpress/data';
 *
 * const store = createReduxStore( 'demo', {
 *     reducer: ( state = 'OK' ) => state,
 *     selectors: {
 *         getValue: ( state ) => state,
 *     },
 * } );
 * ```
 *
 * @template State
 * @template {Record<string,import('../../types').ActionCreator>} Actions
 * @template Selectors
 * @param {string}                                    key     Unique namespace identifier.
 * @param {ReduxStoreConfig<State,Actions,Selectors>} options Registered store options, with properties
 *                                                            describing reducer, actions, selectors,
 *                                                            and resolvers.
 *
 * @return   {StoreDescriptor<ReduxStoreConfig<State,Actions,Selectors>>} Store Object.
 */


function createReduxStore(key, options) {
  const privateActions = {};
  const privateSelectors = {};
  const privateRegistrationFunctions = {
    privateActions,
    registerPrivateActions: actions => {
      Object.assign(privateActions, actions);
    },
    privateSelectors,
    registerPrivateSelectors: selectors => {
      Object.assign(privateSelectors, selectors);
    }
  };
  const storeDescriptor = {
    name: key,
    instantiate: registry => {
      const reducer = options.reducer;
      const thunkArgs = {
        registry,

        get dispatch() {
          return thunkActions;
        },

        get select() {
          return thunkSelectors;
        },

        get resolveSelect() {
          return getResolveSelectors();
        }

      };
      const store = instantiateReduxStore(key, options, registry, thunkArgs); // Expose the private registration functions on the store
      // so they can be copied to a sub registry in registry.js.

      lock(store, privateRegistrationFunctions);
      const resolversCache = createResolversCache();

      function bindAction(action) {
        return (...args) => Promise.resolve(store.dispatch(action(...args)));
      }

      const actions = { ...mapValues(actions_namespaceObject, bindAction),
        ...mapValues(options.actions, bindAction)
      };
      const boundPrivateActions = createBindingCache(bindAction);
      const allActions = new Proxy(() => {}, {
        get: (target, prop) => {
          const privateAction = privateActions[prop];
          return privateAction ? boundPrivateActions.get(privateAction, prop) : actions[prop];
        }
      });
      const thunkActions = new Proxy(allActions, {
        apply: (target, thisArg, [action]) => store.dispatch(action)
      });
      lock(actions, allActions);
      const resolvers = options.resolvers ? mapResolvers(options.resolvers) : {};

      function bindSelector(selector, selectorName) {
        if (selector.isRegistrySelector) {
          selector.registry = registry;
        }

        const boundSelector = (...args) => {
          const state = store.__unstableOriginalGetState();

          return selector(state.root, ...args);
        };

        const resolver = resolvers[selectorName];

        if (!resolver) {
          boundSelector.hasResolver = false;
          return boundSelector;
        }

        return mapSelectorWithResolver(boundSelector, selectorName, resolver, store, resolversCache);
      }

      function bindMetadataSelector(selector) {
        const boundSelector = (...args) => {
          const state = store.__unstableOriginalGetState();

          return selector(state.metadata, ...args);
        };

        boundSelector.hasResolver = false;
        return boundSelector;
      }

      const selectors = { ...mapValues(selectors_namespaceObject, bindMetadataSelector),
        ...mapValues(options.selectors, bindSelector)
      };
      const boundPrivateSelectors = createBindingCache(bindSelector); // Pre-bind the private selectors that have been registered by the time of
      // instantiation, so that registry selectors are bound to the registry.

      for (const [selectorName, selector] of Object.entries(privateSelectors)) {
        boundPrivateSelectors.get(selector, selectorName);
      }

      const allSelectors = new Proxy(() => {}, {
        get: (target, prop) => {
          const privateSelector = privateSelectors[prop];
          return privateSelector ? boundPrivateSelectors.get(privateSelector, prop) : selectors[prop];
        }
      });
      const thunkSelectors = new Proxy(allSelectors, {
        apply: (target, thisArg, [selector]) => selector(store.__unstableOriginalGetState())
      });
      lock(selectors, allSelectors);
      const resolveSelectors = mapResolveSelectors(selectors, store);
      const suspendSelectors = mapSuspendSelectors(selectors, store);

      const getSelectors = () => selectors;

      const getActions = () => actions;

      const getResolveSelectors = () => resolveSelectors;

      const getSuspendSelectors = () => suspendSelectors; // We have some modules monkey-patching the store object
      // It's wrong to do so but until we refactor all of our effects to controls
      // We need to keep the same "store" instance here.


      store.__unstableOriginalGetState = store.getState;

      store.getState = () => store.__unstableOriginalGetState().root; // Customize subscribe behavior to call listeners only on effective change,
      // not on every dispatch.


      const subscribe = store && (listener => {
        let lastState = store.__unstableOriginalGetState();

        return store.subscribe(() => {
          const state = store.__unstableOriginalGetState();

          const hasChanged = state !== lastState;
          lastState = state;

          if (hasChanged) {
            listener();
          }
        });
      }); // This can be simplified to just { subscribe, getSelectors, getActions }
      // Once we remove the use function.


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
  }; // Expose the private registration functions on the store
  // descriptor. That's a natural choice since that's where the
  // public actions and selectors are stored .

  lock(storeDescriptor, privateRegistrationFunctions);
  return storeDescriptor;
}
/**
 * Creates a redux store for a namespace.
 *
 * @param {string}       key       Unique namespace identifier.
 * @param {Object}       options   Registered store options, with properties
 *                                 describing reducer, actions, selectors,
 *                                 and resolvers.
 * @param {DataRegistry} registry  Registry reference.
 * @param {Object}       thunkArgs Argument object for the thunk middleware.
 * @return {Object} Newly created redux store.
 */

function instantiateReduxStore(key, options, registry, thunkArgs) {
  const controls = { ...options.controls,
    ...builtinControls
  };
  const normalizedControls = mapValues(controls, control => control.isRegistryControl ? control(registry) : control);
  const middlewares = [resolvers_cache_middleware(registry, key), promise_middleware, external_wp_reduxRoutine_default()(normalizedControls), createThunkMiddleware(thunkArgs)];
  const enhancers = [applyMiddleware(...middlewares)];

  if (typeof window !== 'undefined' && window.__REDUX_DEVTOOLS_EXTENSION__) {
    enhancers.push(window.__REDUX_DEVTOOLS_EXTENSION__({
      name: key,
      instanceId: key,
      serialize: {
        replacer: mapToObject
      }
    }));
  }

  const {
    reducer,
    initialState
  } = options;
  const enhancedReducer = turbo_combine_reducers_default()({
    metadata: metadata_reducer,
    root: reducer
  });
  return createStore(enhancedReducer, {
    root: initialState
  }, (0,external_wp_compose_namespaceObject.compose)(enhancers));
}
/**
 * Maps selectors to functions that return a resolution promise for them
 *
 * @param {Object} selectors Selectors to map.
 * @param {Object} store     The redux store the selectors select from.
 *
 * @return {Object} Selectors mapped to their resolution functions.
 */


function mapResolveSelectors(selectors, store) {
  const {
    getIsResolving,
    hasStartedResolution,
    hasFinishedResolution,
    hasResolutionFailed,
    isResolving,
    getCachedResolvers,
    getResolutionState,
    getResolutionError,
    ...storeSelectors
  } = selectors;
  return mapValues(storeSelectors, (selector, selectorName) => {
    // If the selector doesn't have a resolver, just convert the return value
    // (including exceptions) to a Promise, no additional extra behavior is needed.
    if (!selector.hasResolver) {
      return async (...args) => selector.apply(null, args);
    }

    return (...args) => {
      return new Promise((resolve, reject) => {
        const hasFinished = () => selectors.hasFinishedResolution(selectorName, args);

        const finalize = result => {
          const hasFailed = selectors.hasResolutionFailed(selectorName, args);

          if (hasFailed) {
            const error = selectors.getResolutionError(selectorName, args);
            reject(error);
          } else {
            resolve(result);
          }
        };

        const getResult = () => selector.apply(null, args); // Trigger the selector (to trigger the resolver)


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
  });
}
/**
 * Maps selectors to functions that throw a suspense promise if not yet resolved.
 *
 * @param {Object} selectors Selectors to map.
 * @param {Object} store     The redux store the selectors select from.
 *
 * @return {Object} Selectors mapped to their suspense functions.
 */


function mapSuspendSelectors(selectors, store) {
  return mapValues(selectors, (selector, selectorName) => {
    // Selector without a resolver doesn't have any extra suspense behavior.
    if (!selector.hasResolver) {
      return selector;
    }

    return (...args) => {
      const result = selector.apply(null, args);

      if (selectors.hasFinishedResolution(selectorName, args)) {
        if (selectors.hasResolutionFailed(selectorName, args)) {
          throw selectors.getResolutionError(selectorName, args);
        }

        return result;
      }

      throw new Promise(resolve => {
        const unsubscribe = store.subscribe(() => {
          if (selectors.hasFinishedResolution(selectorName, args)) {
            resolve();
            unsubscribe();
          }
        });
      });
    };
  });
}
/**
 * Convert resolvers to a normalized form, an object with `fulfill` method and
 * optional methods like `isFulfilled`.
 *
 * @param {Object} resolvers Resolver to convert
 */


function mapResolvers(resolvers) {
  return mapValues(resolvers, resolver => {
    if (resolver.fulfill) {
      return resolver;
    }

    return { ...resolver,
      // Copy the enumerable properties of the resolver function.
      fulfill: resolver // Add the fulfill method.

    };
  });
}
/**
 * Returns a selector with a matched resolver.
 * Resolvers are side effects invoked once per argument set of a given selector call,
 * used in ensuring that the data needs for the selector are satisfied.
 *
 * @param {Object} selector       The selector function to be bound.
 * @param {string} selectorName   The selector name.
 * @param {Object} resolver       Resolver to call.
 * @param {Object} store          The redux store to which the resolvers should be mapped.
 * @param {Object} resolversCache Resolvers Cache.
 */


function mapSelectorWithResolver(selector, selectorName, resolver, store, resolversCache) {
  function fulfillSelector(args) {
    const state = store.getState();

    if (resolversCache.isRunning(selectorName, args) || typeof resolver.isFulfilled === 'function' && resolver.isFulfilled(state, ...args)) {
      return;
    }

    const {
      metadata
    } = store.__unstableOriginalGetState();

    if (hasStartedResolution(metadata, selectorName, args)) {
      return;
    }

    resolversCache.markAsRunning(selectorName, args);
    setTimeout(async () => {
      resolversCache.clear(selectorName, args);
      store.dispatch(startResolution(selectorName, args));

      try {
        const action = resolver.fulfill(...args);

        if (action) {
          await store.dispatch(action);
        }

        store.dispatch(finishResolution(selectorName, args));
      } catch (error) {
        store.dispatch(failResolution(selectorName, args, error));
      }
    }, 0);
  }

  const selectorResolver = (...args) => {
    fulfillSelector(args);
    return selector(...args);
  };

  selectorResolver.hasResolver = true;
  return selectorResolver;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/utils/emitter.js
/**
 * Create an event emitter.
 *
 * @return {import("../types").DataEmitter} Emitter.
 */
function createEmitter() {
  let isPaused = false;
  let isPending = false;
  const listeners = new Set();

  const notifyListeners = () => // We use Array.from to clone the listeners Set
  // This ensures that we don't run a listener
  // that was added as a response to another listener.
  Array.from(listeners).forEach(listener => listener());

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

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/registry.js
/**
 * WordPress dependencies
=======
 * Creates a namespace object with a store derived from the reducer given.
 *
 * @param {string} key              Identifying string used for namespace and redex dev tools.
 * @param {Object} options          Contains reducer, actions, selectors, and resolvers.
 * @param {Object} registry         Temporary registry reference, required for namespace updates.
 *
 * @return {Object} Store Object.
 */

function createNamespace(key, options, registry) {
  var reducer = options.reducer;
  var store = createReduxStore(reducer, key, registry);
  var selectors, actions, resolvers;

  if (options.actions) {
    actions = mapActions(options.actions, store);
  }

  if (options.selectors) {
    selectors = mapSelectors(options.selectors, store);
  }

  if (options.resolvers) {
    var fulfillment = getCoreDataFulfillment(registry, key);
    var result = mapResolvers(options.resolvers, selectors, fulfillment, store);
    resolvers = result.resolvers;
    selectors = result.selectors;
  }

  var getSelectors = function getSelectors() {
    return selectors;
  };

  var getActions = function getActions() {
    return actions;
  }; // Customize subscribe behavior to call listeners only on effective change,
  // not on every dispatch.


  var subscribe = store && function (listener) {
    var lastState = store.getState();
    store.subscribe(function () {
      var state = store.getState();
      var hasChanged = state !== lastState;
      lastState = state;

      if (hasChanged) {
        listener();
      }
    });
  }; // This can be simplified to just { subscribe, getSelectors, getActions }
  // Once we remove the use function.


  return {
    reducer: reducer,
    store: store,
    actions: actions,
    selectors: selectors,
    resolvers: resolvers,
    getSelectors: getSelectors,
    getActions: getActions,
    subscribe: subscribe
  };
}
/**
 * Creates a redux store for a namespace.
 *
 * @param {Function} reducer    Root reducer for redux store.
 * @param {string} key          Part of the state shape to register the
 *                              selectors for.
 * @param {Object} registry     Registry reference, for resolver enhancer support.
 * @return {Object}             Newly created redux store.
 */

function createReduxStore(reducer, key, registry) {
  var enhancers = [Object(redux["a" /* applyMiddleware */])(resolvers_cache_middleware(registry, key), promise_middleware)];

  if (typeof window !== 'undefined' && window.__REDUX_DEVTOOLS_EXTENSION__) {
    enhancers.push(window.__REDUX_DEVTOOLS_EXTENSION__({
      name: key,
      instanceId: key
    }));
  }

  return Object(redux["c" /* createStore */])(reducer, Object(external_lodash_["flowRight"])(enhancers));
}
/**
 * Maps selectors to a redux store.
 *
 * @param {Object} selectors  Selectors to register. Keys will be used as the
 *                            public facing API. Selectors will get passed the
 *                            state as first argument.
 * @param {Object} store      The redux store to which the selectors should be mapped.
 * @return {Object}           Selectors mapped to the redux store provided.
 */


function mapSelectors(selectors, store) {
  var createStateSelector = function createStateSelector(selector) {
    return function runSelector() {
      // This function is an optimized implementation of:
      //
      //   selector( store.getState(), ...arguments )
      //
      // Where the above would incur an `Array#concat` in its application,
      // the logic here instead efficiently constructs an arguments array via
      // direct assignment.
      var argsLength = arguments.length;
      var args = new Array(argsLength + 1);
      args[0] = store.getState();

      for (var i = 0; i < argsLength; i++) {
        args[i + 1] = arguments[i];
      }

      return selector.apply(void 0, args);
    };
  };

  return Object(external_lodash_["mapValues"])(selectors, createStateSelector);
}
/**
 * Maps actions to dispatch from a given store.
 *
 * @param {Object} actions    Actions to register.
 * @param {Object} store      The redux store to which the actions should be mapped.
 * @return {Object}           Actions mapped to the redux store provided.
 */


function mapActions(actions, store) {
  var createBoundAction = function createBoundAction(action) {
    return function () {
      return store.dispatch(action.apply(void 0, arguments));
    };
  };

  return Object(external_lodash_["mapValues"])(actions, createBoundAction);
}
/**
 * Returns resolvers with matched selectors for a given namespace.
 * Resolvers are side effects invoked once per argument set of a given selector call,
 * used in ensuring that the data needs for the selector are satisfied.
 *
 * @param {Object} resolvers   Resolvers to register.
 * @param {Object} selectors   The current selectors to be modified.
 * @param {Object} fulfillment Fulfillment implementation functions.
 * @param {Object} store       The redux store to which the resolvers should be mapped.
 * @return {Object}            An object containing updated selectors and resolvers.
 */


function mapResolvers(resolvers, selectors, fulfillment, store) {
  var mapSelector = function mapSelector(selector, selectorName) {
    var resolver = resolvers[selectorName];

    if (!resolver) {
      return selector;
    }

    return function () {
      for (var _len = arguments.length, args = new Array(_len), _key = 0; _key < _len; _key++) {
        args[_key] = arguments[_key];
      }

      function fulfillSelector() {
        return _fulfillSelector.apply(this, arguments);
      }

      function _fulfillSelector() {
        _fulfillSelector = Object(asyncToGenerator["a" /* default */])(
        /*#__PURE__*/
        regeneratorRuntime.mark(function _callee() {
          var state;
          return regeneratorRuntime.wrap(function _callee$(_context) {
            while (1) {
              switch (_context.prev = _context.next) {
                case 0:
                  state = store.getState();

                  if (!(typeof resolver.isFulfilled === 'function' && resolver.isFulfilled.apply(resolver, [state].concat(args)))) {
                    _context.next = 3;
                    break;
                  }

                  return _context.abrupt("return");

                case 3:
                  if (!fulfillment.hasStarted(selectorName, args)) {
                    _context.next = 5;
                    break;
                  }

                  return _context.abrupt("return");

                case 5:
                  fulfillment.start(selectorName, args);
                  _context.next = 8;
                  return fulfillment.fulfill.apply(fulfillment, [selectorName].concat(args));

                case 8:
                  fulfillment.finish(selectorName, args);

                case 9:
                case "end":
                  return _context.stop();
              }
            }
          }, _callee, this);
        }));
        return _fulfillSelector.apply(this, arguments);
      }

      fulfillSelector.apply(void 0, args);
      return selector.apply(void 0, args);
    };
  };

  var mappedResolvers = Object(external_lodash_["mapValues"])(resolvers, function (resolver) {
    var _resolver$fulfill = resolver.fulfill,
        resolverFulfill = _resolver$fulfill === void 0 ? resolver : _resolver$fulfill;
    return Object(objectSpread["a" /* default */])({}, resolver, {
      fulfill: resolverFulfill
    });
  });
  return {
    resolvers: mappedResolvers,
    selectors: Object(external_lodash_["mapValues"])(selectors, mapSelector)
  };
}
/**
 * Bundles up fulfillment functions for resolvers.
 * @param {Object} registry     Registry reference, for fulfilling via resolvers
 * @param {string} key          Part of the state shape to register the
 *                              selectors for.
 * @return {Object}             An object providing fulfillment functions.
 */


function getCoreDataFulfillment(registry, key) {
  var _registry$select = registry.select('core/data'),
      hasStartedResolution = _registry$select.hasStartedResolution;

  var _registry$dispatch = registry.dispatch('core/data'),
      startResolution = _registry$dispatch.startResolution,
      finishResolution = _registry$dispatch.finishResolution;

  return {
    hasStarted: function hasStarted() {
      for (var _len2 = arguments.length, args = new Array(_len2), _key2 = 0; _key2 < _len2; _key2++) {
        args[_key2] = arguments[_key2];
      }

      return hasStartedResolution.apply(void 0, [key].concat(args));
    },
    start: function start() {
      for (var _len3 = arguments.length, args = new Array(_len3), _key3 = 0; _key3 < _len3; _key3++) {
        args[_key3] = arguments[_key3];
      }

      return startResolution.apply(void 0, [key].concat(args));
    },
    finish: function finish() {
      for (var _len4 = arguments.length, args = new Array(_len4), _key4 = 0; _key4 < _len4; _key4++) {
        args[_key4] = arguments[_key4];
      }

      return finishResolution.apply(void 0, [key].concat(args));
    },
    fulfill: function fulfill() {
      for (var _len5 = arguments.length, args = new Array(_len5), _key5 = 0; _key5 < _len5; _key5++) {
        args[_key5] = arguments[_key5];
      }

      return fulfillWithRegistry.apply(void 0, [registry, key].concat(args));
    }
  };
}
/**
 * Calls a resolver given arguments
 *
 * @param {Object} registry     Registry reference, for fulfilling via resolvers
 * @param {string} key          Part of the state shape to register the
 *                              selectors for.
 * @param {string} selectorName Selector name to fulfill.
 * @param {Array} args         Selector Arguments.
 */


function fulfillWithRegistry(_x, _x2, _x3) {
  return _fulfillWithRegistry.apply(this, arguments);
}

function _fulfillWithRegistry() {
  _fulfillWithRegistry = Object(asyncToGenerator["a" /* default */])(
  /*#__PURE__*/
  regeneratorRuntime.mark(function _callee2(registry, key, selectorName) {
    var namespace,
        resolver,
        _len6,
        args,
        _key6,
        action,
        _args2 = arguments;

    return regeneratorRuntime.wrap(function _callee2$(_context2) {
      while (1) {
        switch (_context2.prev = _context2.next) {
          case 0:
            namespace = registry.stores[key];
            resolver = Object(external_lodash_["get"])(namespace, ['resolvers', selectorName]);

            if (resolver) {
              _context2.next = 4;
              break;
            }

            return _context2.abrupt("return");

          case 4:
            for (_len6 = _args2.length, args = new Array(_len6 > 3 ? _len6 - 3 : 0), _key6 = 3; _key6 < _len6; _key6++) {
              args[_key6 - 3] = _args2[_key6];
            }

            action = resolver.fulfill.apply(resolver, args);

            if (!action) {
              _context2.next = 9;
              break;
            }

            _context2.next = 9;
            return namespace.store.dispatch(action);

          case 9:
          case "end":
            return _context2.stop();
        }
      }
    }, _callee2, this);
  }));
  return _fulfillWithRegistry.apply(this, arguments);
}

// EXTERNAL MODULE: ./node_modules/equivalent-key-map/equivalent-key-map.js
var equivalent_key_map = __webpack_require__("FtRg");
var equivalent_key_map_default = /*#__PURE__*/__webpack_require__.n(equivalent_key_map);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__("rePB");

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/store/utils.js



/**
 * Higher-order reducer creator which creates a combined reducer object, keyed
 * by a property on the action object.
 *
 * @param {string} actionProperty Action property by which to key object.
 *
 * @return {Function} Higher-order reducer.
 */
var utils_onSubKey = function onSubKey(actionProperty) {
  return function (reducer) {
    return function () {
      var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      var action = arguments.length > 1 ? arguments[1] : undefined;
      // Retrieve subkey from action. Do not track if undefined; useful for cases
      // where reducer is scoped by action shape.
      var key = action[actionProperty];

      if (key === undefined) {
        return state;
      } // Avoid updating state if unchanged. Note that this also accounts for a
      // reducer which returns undefined on a key which is not yet tracked.


      var nextKeyState = reducer(state[key], action);

      if (nextKeyState === state[key]) {
        return state;
      }

      return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, key, nextKeyState));
    };
  };
};

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/store/reducer.js
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Reducer function returning next state for selector resolution, object form:
 *
 *  reducerKey -> selectorName -> EquivalentKeyMap<Array,boolean>
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @returns {Object} Next state.
 */

var isResolved = Object(external_lodash_["flowRight"])([utils_onSubKey('reducerKey'), utils_onSubKey('selectorName')])(function () {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : new equivalent_key_map_default.a();
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'START_RESOLUTION':
    case 'FINISH_RESOLUTION':
      {
        var isStarting = action.type === 'START_RESOLUTION';
        var nextState = new equivalent_key_map_default.a(state);
        nextState.set(action.args, isStarting);
        return nextState;
      }

    case 'INVALIDATE_RESOLUTION':
      {
        var _nextState = new equivalent_key_map_default.a(state);

        _nextState.delete(action.args);

        return _nextState;
      }
  }

  return state;
});
/* harmony default export */ var store_reducer = (isResolved);

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/store/selectors.js
/**
 * External dependencies
 */

/**
 * Returns the raw `isResolving` value for a given reducer key, selector name,
 * and arguments set. May be undefined if the selector has never been resolved
 * or not resolved for the given set of arguments, otherwise true or false for
 * resolution started and completed respectively.
 *
 * @param {Object} state        Data state.
 * @param {string} reducerKey   Registered store reducer key.
 * @param {string} selectorName Selector name.
 * @param {Array}  args         Arguments passed to selector.
 *
 * @return {?boolean} isResolving value.
 */

function getIsResolving(state, reducerKey, selectorName, args) {
  var map = Object(external_lodash_["get"])(state, [reducerKey, selectorName]);

  if (!map) {
    return;
  }

  return map.get(args);
}
/**
 * Returns true if resolution has already been triggered for a given reducer
 * key, selector name, and arguments set.
 *
 * @param {Object} state        Data state.
 * @param {string} reducerKey   Registered store reducer key.
 * @param {string} selectorName Selector name.
 * @param {?Array} args         Arguments passed to selector (default `[]`).
 *
 * @return {boolean} Whether resolution has been triggered.
 */

function hasStartedResolution(state, reducerKey, selectorName) {
  var args = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : [];
  return getIsResolving(state, reducerKey, selectorName, args) !== undefined;
}
/**
 * Returns true if resolution has completed for a given reducer key, selector
 * name, and arguments set.
 *
 * @param {Object} state        Data state.
 * @param {string} reducerKey   Registered store reducer key.
 * @param {string} selectorName Selector name.
 * @param {?Array} args         Arguments passed to selector.
 *
 * @return {boolean} Whether resolution has completed.
 */

function hasFinishedResolution(state, reducerKey, selectorName) {
  var args = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : [];
  return getIsResolving(state, reducerKey, selectorName, args) === false;
}
/**
 * Returns true if resolution has been triggered but has not yet completed for
 * a given reducer key, selector name, and arguments set.
 *
 * @param {Object} state        Data state.
 * @param {string} reducerKey   Registered store reducer key.
 * @param {string} selectorName Selector name.
 * @param {?Array} args         Arguments passed to selector.
 *
 * @return {boolean} Whether resolution is in progress.
 */

function isResolving(state, reducerKey, selectorName) {
  var args = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : [];
  return getIsResolving(state, reducerKey, selectorName, args) === true;
}
/**
 * Returns the list of the cached resolvers.
 *
 * @param {Object} state      Data state.
 * @param {string} reducerKey Registered store reducer key.
 *
 * @return {Object} Resolvers mapped by args and selectorName.
 */

function getCachedResolvers(state, reducerKey) {
  return state.hasOwnProperty(reducerKey) ? state[reducerKey] : {};
}

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/store/actions.js
/**
 * Returns an action object used in signalling that selector resolution has
 * started.
 *
 * @param {string} reducerKey   Registered store reducer key.
 * @param {string} selectorName Name of selector for which resolver triggered.
 * @param {...*}   args         Arguments to associate for uniqueness.
 *
 * @return {Object} Action object.
 */
function startResolution(reducerKey, selectorName, args) {
  return {
    type: 'START_RESOLUTION',
    reducerKey: reducerKey,
    selectorName: selectorName,
    args: args
  };
}
/**
 * Returns an action object used in signalling that selector resolution has
 * completed.
 *
 * @param {string} reducerKey   Registered store reducer key.
 * @param {string} selectorName Name of selector for which resolver triggered.
 * @param {...*}   args         Arguments to associate for uniqueness.
 *
 * @return {Object} Action object.
 */

function finishResolution(reducerKey, selectorName, args) {
  return {
    type: 'FINISH_RESOLUTION',
    reducerKey: reducerKey,
    selectorName: selectorName,
    args: args
  };
}
/**
 * Returns an action object used in signalling that we should invalidate the resolution cache.
 *
 * @param {string} reducerKey   Registered store reducer key.
 * @param {string} selectorName Name of selector for which resolver should be invalidated.
 * @param {Array}  args         Arguments to associate for uniqueness.
 *
 * @return {Object} Action object.
 */

function invalidateResolution(reducerKey, selectorName, args) {
  return {
    type: 'INVALIDATE_RESOLUTION',
    reducerKey: reducerKey,
    selectorName: selectorName,
    args: args
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/store/index.js
/**
 * Internal dependencies
 */



/* harmony default export */ var build_module_store = ({
  reducer: store_reducer,
  actions: actions_namespaceObject,
  selectors: selectors_namespaceObject
});

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/registry.js



/**
 * External dependencies
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */

/**
 * Internal dependencies
 */



<<<<<<< HEAD


/** @typedef {import('./types').StoreDescriptor} StoreDescriptor */

/**
 * @typedef {Object} WPDataRegistry An isolated orchestrator of store registrations.
 *
 * @property {Function} registerGenericStore Given a namespace key and settings
 *                                           object, registers a new generic
 *                                           store.
 * @property {Function} registerStore        Given a namespace key and settings
 *                                           object, registers a new namespace
 *                                           store.
 * @property {Function} subscribe            Given a function callback, invokes
 *                                           the callback on any change to state
 *                                           within any registered store.
 * @property {Function} select               Given a namespace key, returns an
 *                                           object of the  store's registered
 *                                           selectors.
 * @property {Function} dispatch             Given a namespace key, returns an
 *                                           object of the store's registered
 *                                           action dispatchers.
 */

/**
 * @typedef {Object} WPDataPlugin An object of registry function overrides.
 *
 * @property {Function} registerStore registers store.
 */

function getStoreName(storeNameOrDescriptor) {
  return typeof storeNameOrDescriptor === 'string' ? storeNameOrDescriptor : storeNameOrDescriptor.name;
}
=======
/**
 * An isolated orchestrator of store registrations.
 *
 * @typedef {WPDataRegistry}
 *
 * @property {Function} registerGenericStore
 * @property {Function} registerStore
 * @property {Function} subscribe
 * @property {Function} select
 * @property {Function} dispatch
 */

/**
 * An object of registry function overrides.
 *
 * @typedef {WPDataPlugin}
 */

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Creates a new store registry, given an optional object of initial store
 * configurations.
 *
<<<<<<< HEAD
 * @param {Object}  storeConfigs Initial store configurations.
 * @param {Object?} parent       Parent registry.
=======
 * @param {Object} storeConfigs Initial store configurations.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {WPDataRegistry} Data registry.
 */

<<<<<<< HEAD

function createRegistry(storeConfigs = {}, parent = null) {
  const stores = {};
  const emitter = createEmitter();
  let listeningStores = null;
=======
function createRegistry() {
  var storeConfigs = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var stores = {};
  var listeners = [];
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  /**
   * Global listener called for each store's update.
   */

  function globalListener() {
<<<<<<< HEAD
    emitter.emit();
  }
  /**
   * Subscribe to changes to any data, either in all stores in registry, or
   * in one specific store.
   *
   * @param {Function}                listener              Listener function.
   * @param {string|StoreDescriptor?} storeNameOrDescriptor Optional store name.
   *
   * @return {Function} Unsubscribe function.
   */


  const subscribe = (listener, storeNameOrDescriptor) => {
    // subscribe to all stores
    if (!storeNameOrDescriptor) {
      return emitter.subscribe(listener);
    } // subscribe to one store


    const storeName = getStoreName(storeNameOrDescriptor);
    const store = stores[storeName];

    if (store) {
      return store.subscribe(listener);
    } // Trying to access a store that hasn't been registered,
    // this is a pattern rarely used but seen in some places.
    // We fallback to global `subscribe` here for backward-compatibility for now.
    // See https://github.com/WordPress/gutenberg/pull/27466 for more info.


    if (!parent) {
      return emitter.subscribe(listener);
    }

    return parent.subscribe(listener, storeNameOrDescriptor);
=======
    listeners.forEach(function (listener) {
      return listener();
    });
  }
  /**
   * Subscribe to changes to any data.
   *
   * @param {Function}   listener Listener function.
   *
   * @return {Function}           Unsubscribe function.
   */


  var subscribe = function subscribe(listener) {
    listeners.push(listener);
    return function () {
      listeners = Object(external_lodash_["without"])(listeners, listener);
    };
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  };
  /**
   * Calls a selector given the current state and extra arguments.
   *
<<<<<<< HEAD
   * @param {string|StoreDescriptor} storeNameOrDescriptor Unique namespace identifier for the store
   *                                                       or the store descriptor.
=======
   * @param {string} reducerKey Part of the state shape to register the
   *                            selectors for.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
   *
   * @return {*} The selector's returned value.
   */


<<<<<<< HEAD
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
    listeningStores = new Set();

    try {
      return callback.call(this);
    } finally {
      ref.current = Array.from(listeningStores);
      listeningStores = null;
    }
  }
  /**
   * Given a store descriptor, returns an object containing the store's selectors pre-bound to
   * state so that you only need to supply additional arguments, and modified so that they return
   * promises that resolve to their eventual values, after any resolvers have ran.
   *
   * @param {StoreDescriptor|string} storeNameOrDescriptor The store descriptor. The legacy calling
   *                                                       convention of passing the store name is
   *                                                       also supported.
   *
   * @return {Object} Each key of the object matches the name of a selector.
   */


  function resolveSelect(storeNameOrDescriptor) {
    const storeName = getStoreName(storeNameOrDescriptor);
    listeningStores?.add(storeName);
    const store = stores[storeName];

    if (store) {
      return store.getResolveSelectors();
    }

    return parent && parent.resolveSelect(storeName);
  }
  /**
   * Given a store descriptor, returns an object containing the store's selectors pre-bound to
   * state so that you only need to supply additional arguments, and modified so that they throw
   * promises in case the selector is not resolved yet.
   *
   * @param {StoreDescriptor|string} storeNameOrDescriptor The store descriptor. The legacy calling
   *                                                       convention of passing the store name is
   *                                                       also supported.
   *
   * @return {Object} Object containing the store's suspense-wrapped selectors.
   */


  function suspendSelect(storeNameOrDescriptor) {
    const storeName = getStoreName(storeNameOrDescriptor);
    listeningStores?.add(storeName);
    const store = stores[storeName];

    if (store) {
      return store.getSuspendSelectors();
    }

    return parent && parent.suspendSelect(storeName);
=======
  function select(reducerKey) {
    var store = stores[reducerKey];
    return store && store.getSelectors();
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  }
  /**
   * Returns the available actions for a part of the state.
   *
<<<<<<< HEAD
   * @param {string|StoreDescriptor} storeNameOrDescriptor Unique namespace identifier for the store
   *                                                       or the store descriptor.
=======
   * @param {string} reducerKey Part of the state shape to dispatch the
   *                            action for.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
   *
   * @return {*} The action's returned value.
   */


<<<<<<< HEAD
  function dispatch(storeNameOrDescriptor) {
    const storeName = getStoreName(storeNameOrDescriptor);
    const store = stores[storeName];

    if (store) {
      return store.getActions();
    }

    return parent && parent.dispatch(storeName);
  } //
  // Deprecated
  // TODO: Remove this after `use()` is removed.


  function withPlugins(attributes) {
    return Object.fromEntries(Object.entries(attributes).map(([key, attribute]) => {
      if (typeof attribute !== 'function') {
        return [key, attribute];
      }

      return [key, function () {
        return registry[key].apply(null, arguments);
      }];
    }));
  }
  /**
   * Registers a store instance.
   *
   * @param {string}   name        Store registry name.
   * @param {Function} createStore Function that creates a store object (getSelectors, getActions, subscribe).
   */


  function registerStoreInstance(name, createStore) {
    if (stores[name]) {
      // eslint-disable-next-line no-console
      console.error('Store "' + name + '" is already registered.');
      return stores[name];
    }

    const store = createStore();

    if (typeof store.getSelectors !== 'function') {
      throw new TypeError('store.getSelectors must be a function');
    }

    if (typeof store.getActions !== 'function') {
      throw new TypeError('store.getActions must be a function');
    }

    if (typeof store.subscribe !== 'function') {
      throw new TypeError('store.subscribe must be a function');
    } // The emitter is used to keep track of active listeners when the registry
    // get paused, that way, when resumed we should be able to call all these
    // pending listeners.


    store.emitter = createEmitter();
    const currentSubscribe = store.subscribe;

    store.subscribe = listener => {
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
    store.subscribe(globalListener); // Copy private actions and selectors from the parent store.

    if (parent) {
      try {
        unlock(store.store).registerPrivateActions(unlock(parent).privateActionsOf(name));
        unlock(store.store).registerPrivateSelectors(unlock(parent).privateSelectorsOf(name));
      } catch (e) {// unlock() throws if store.store was not locked.
        // The error indicates there's nothing to do here so let's
        // ignore it.
      }
    }

    return store;
  }
  /**
   * Registers a new store given a store descriptor.
   *
   * @param {StoreDescriptor} store Store descriptor.
   */


  function register(store) {
    registerStoreInstance(store.name, () => store.instantiate(registry));
  }

  function registerGenericStore(name, store) {
    external_wp_deprecated_default()('wp.data.registerGenericStore', {
      since: '5.9',
      alternative: 'wp.data.register( storeDescriptor )'
    });
    registerStoreInstance(name, () => store);
  }
  /**
   * Registers a standard `@wordpress/data` store.
   *
   * @param {string} storeName Unique namespace identifier.
   * @param {Object} options   Store description (reducer, actions, selectors, resolvers).
=======
  function dispatch(reducerKey) {
    var store = stores[reducerKey];
    return store && store.getActions();
  } //
  // Deprecated
  // TODO: Remove this after `use()` is removed.
  //


  function withPlugins(attributes) {
    return Object(external_lodash_["mapValues"])(attributes, function (attribute, key) {
      if (typeof attribute !== 'function') {
        return attribute;
      }

      return function () {
        return registry[key].apply(null, arguments);
      };
    });
  }
  /**
   * Registers a generic store.
   *
   * @param {string} key    Store registry key.
   * @param {Object} config Configuration (getSelectors, getActions, subscribe).
   */


  function registerGenericStore(key, config) {
    if (typeof config.getSelectors !== 'function') {
      throw new TypeError('config.getSelectors must be a function');
    }

    if (typeof config.getActions !== 'function') {
      throw new TypeError('config.getActions must be a function');
    }

    if (typeof config.subscribe !== 'function') {
      throw new TypeError('config.subscribe must be a function');
    }

    stores[key] = config;
    config.subscribe(globalListener);
  }

  var registry = {
    registerGenericStore: registerGenericStore,
    stores: stores,
    namespaces: stores,
    // TODO: Deprecate/remove this.
    subscribe: subscribe,
    select: select,
    dispatch: dispatch,
    use: use
  };
  /**
   * Registers a standard `@wordpress/data` store.
   *
   * @param {string} reducerKey Reducer key.
   * @param {Object} options    Store description (reducer, actions, selectors, resolvers).
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
   *
   * @return {Object} Registered store object.
   */

<<<<<<< HEAD

  function registerStore(storeName, options) {
=======
  registry.registerStore = function (reducerKey, options) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    if (!options.reducer) {
      throw new TypeError('Must specify store reducer');
    }

<<<<<<< HEAD
    const store = registerStoreInstance(storeName, () => createReduxStore(storeName, options).instantiate(registry));
    return store.store;
  }

  function batch(callback) {
    // If we're already batching, just call the callback.
    if (emitter.isPaused) {
      callback();
      return;
    }

    emitter.pause();
    Object.values(stores).forEach(store => store.emitter.pause());
    callback();
    emitter.resume();
    Object.values(stores).forEach(store => store.emitter.resume());
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
  }; //
  // TODO:
  // This function will be deprecated as soon as it is no longer internally referenced.

  function use(plugin, options) {
    if (!plugin) {
      return;
    }

    registry = { ...registry,
      ...plugin(registry, options)
    };
    return registry;
  }

  registry.register(store);

  for (const [name, config] of Object.entries(storeConfigs)) {
    registry.register(createReduxStore(name, config));
  }

  if (parent) {
    parent.subscribe(globalListener);
  }

  const registryWithPlugins = withPlugins(registry);
  lock(registryWithPlugins, {
    privateActionsOf: name => {
      try {
        return unlock(stores[name].store).privateActions;
      } catch (e) {
        // unlock() throws an error the store was not locked – this means
        // there no private actions are available
        return {};
      }
    },
    privateSelectorsOf: name => {
      try {
        return unlock(stores[name].store).privateSelectors;
      } catch (e) {
        return {};
      }
    }
  });
  return registryWithPlugins;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/default-registry.js
/**
 * Internal dependencies
 */

/* harmony default export */ const default_registry = (createRegistry());

;// CONCATENATED MODULE: ./node_modules/is-plain-object/dist/is-plain-object.mjs
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
var cjs = __webpack_require__(1919);
var cjs_default = /*#__PURE__*/__webpack_require__.n(cjs);
;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/persistence/storage/object.js
let objectStorage;
const storage = {
  getItem(key) {
=======
    var namespace = createNamespace(reducerKey, options, registry);
    registerGenericStore(reducerKey, namespace);
    return namespace.store;
  }; //
  // TODO:
  // This function will be deprecated as soon as it is no longer internally referenced.
  //


  function use(plugin, options) {
    registry = Object(objectSpread["a" /* default */])({}, registry, plugin(registry, options));
    return registry;
  }

  Object.entries(Object(objectSpread["a" /* default */])({
    'core/data': build_module_store
  }, storeConfigs)).map(function (_ref) {
    var _ref2 = Object(slicedToArray["a" /* default */])(_ref, 2),
        name = _ref2[0],
        config = _ref2[1];

    return registry.registerStore(name, config);
  });
  return withPlugins(registry);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/default-registry.js

/* harmony default export */ var default_registry = (createRegistry());

// EXTERNAL MODULE: external {"this":["wp","reduxRoutine"]}
var external_this_wp_reduxRoutine_ = __webpack_require__("XIDh");
var external_this_wp_reduxRoutine_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_reduxRoutine_);

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/controls/index.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/* harmony default export */ var controls = (function (registry) {
  return {
    registerStore: function registerStore(reducerKey, options) {
      var store = registry.registerStore(reducerKey, options);

      if (options.controls) {
        var middleware = external_this_wp_reduxRoutine_default()(options.controls);
        var enhancer = Object(redux["a" /* applyMiddleware */])(middleware);

        var createStore = function createStore() {
          return store;
        };

        Object.assign(store, enhancer(createStore)(options.reducer));
        registry.namespaces[reducerKey].supportControls = true;
      }

      return store;
    }
  };
});

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/persistence/storage/object.js
var objectStorage;
var object_storage = {
  getItem: function getItem(key) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    if (!objectStorage || !objectStorage[key]) {
      return null;
    }

    return objectStorage[key];
  },
<<<<<<< HEAD

  setItem(key, value) {
    if (!objectStorage) {
      storage.clear();
=======
  setItem: function setItem(key, value) {
    if (!objectStorage) {
      object_storage.clear();
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    }

    objectStorage[key] = String(value);
  },
<<<<<<< HEAD

  clear() {
    objectStorage = Object.create(null);
  }

};
/* harmony default export */ const object = (storage);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/persistence/storage/default.js
=======
  clear: function clear() {
    objectStorage = Object.create(null);
  }
};
/* harmony default export */ var object = (object_storage);

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/persistence/storage/default.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */

<<<<<<< HEAD
let default_storage;
=======
var default_storage;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

try {
  // Private Browsing in Safari 10 and earlier will throw an error when
  // attempting to set into localStorage. The test here is intentional in
  // causing a thrown error as condition for using fallback object storage.
  default_storage = window.localStorage;
  default_storage.setItem('__wpDataTestLocalStorage', '');
  default_storage.removeItem('__wpDataTestLocalStorage');
} catch (error) {
  default_storage = object;
}

<<<<<<< HEAD
/* harmony default export */ const storage_default = (default_storage);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/persistence/index.js
=======
/* harmony default export */ var storage_default = (default_storage);

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/persistence/index.js



>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * External dependencies
 */

<<<<<<< HEAD

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD

/** @typedef {import('../../registry').WPDataRegistry} WPDataRegistry */

/** @typedef {import('../../registry').WPDataPlugin} WPDataPlugin */

/**
 * @typedef {Object} WPDataPersistencePluginOptions Persistence plugin options.
=======
/**
 * Persistence plugin options.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @property {Storage} storage    Persistent storage implementation. This must
 *                                at least implement `getItem` and `setItem` of
 *                                the Web Storage API.
 * @property {string}  storageKey Key on which to set in persistent storage.
 *
<<<<<<< HEAD
=======
 * @typedef {WPDataPersistencePluginOptions}
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */

/**
 * Default plugin storage.
 *
 * @type {Storage}
 */

<<<<<<< HEAD
const DEFAULT_STORAGE = storage_default;
=======
var DEFAULT_STORAGE = storage_default;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Default plugin storage key.
 *
 * @type {string}
 */

<<<<<<< HEAD
const DEFAULT_STORAGE_KEY = 'WP_DATA';
/**
 * Higher-order reducer which invokes the original reducer only if state is
 * inequal from that of the action's `nextState` property, otherwise returning
 * the original state reference.
 *
 * @param {Function} reducer Original reducer.
=======
var DEFAULT_STORAGE_KEY = 'WP_DATA';
/**
 * Higher-order reducer to provides an initial value when state is undefined.
 *
 * @param {Function} reducer      Original reducer.
 * @param {*}         initialState Value to use as initial state.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {Function} Enhanced reducer.
 */

<<<<<<< HEAD
const withLazySameState = reducer => (state, action) => {
  if (action.nextState === state) {
    return state;
  }

  return reducer(state, action);
};
=======
function withInitialState(reducer, initialState) {
  return function () {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : initialState;
    var action = arguments.length > 1 ? arguments[1] : undefined;
    return reducer(state, action);
  };
}
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Creates a persistence interface, exposing getter and setter methods (`get`
 * and `set` respectively).
 *
 * @param {WPDataPersistencePluginOptions} options Plugin options.
 *
 * @return {Object} Persistence interface.
 */

function createPersistenceInterface(options) {
<<<<<<< HEAD
  const {
    storage = DEFAULT_STORAGE,
    storageKey = DEFAULT_STORAGE_KEY
  } = options;
  let data;
=======
  var _options$storage = options.storage,
      storage = _options$storage === void 0 ? DEFAULT_STORAGE : _options$storage,
      _options$storageKey = options.storageKey,
      storageKey = _options$storageKey === void 0 ? DEFAULT_STORAGE_KEY : _options$storageKey;
  var data;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  /**
   * Returns the persisted data as an object, defaulting to an empty object.
   *
   * @return {Object} Persisted data.
   */

<<<<<<< HEAD
  function getData() {
    if (data === undefined) {
      // If unset, getItem is expected to return null. Fall back to
      // empty object.
      const persisted = storage.getItem(storageKey);
=======
  function get() {
    if (data === undefined) {
      // If unset, getItem is expected to return null. Fall back to
      // empty object.
      var persisted = storage.getItem(storageKey);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

      if (persisted === null) {
        data = {};
      } else {
        try {
          data = JSON.parse(persisted);
        } catch (error) {
          // Similarly, should any error be thrown during parse of
          // the string (malformed JSON), fall back to empty object.
          data = {};
        }
      }
    }

    return data;
  }
  /**
   * Merges an updated reducer state into the persisted data.
   *
   * @param {string} key   Key to update.
   * @param {*}      value Updated value.
   */


<<<<<<< HEAD
  function setData(key, value) {
    data = { ...data,
      [key]: value
    };
=======
  function set(key, value) {
    data = Object(objectSpread["a" /* default */])({}, data, Object(defineProperty["a" /* default */])({}, key, value));
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    storage.setItem(storageKey, JSON.stringify(data));
  }

  return {
<<<<<<< HEAD
    get: getData,
    set: setData
=======
    get: get,
    set: set
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  };
}
/**
 * Data plugin to persist store state into a single storage key.
 *
 * @param {WPDataRegistry}                  registry      Data registry.
 * @param {?WPDataPersistencePluginOptions} pluginOptions Plugin options.
 *
 * @return {WPDataPlugin} Data plugin.
 */

<<<<<<< HEAD
function persistencePlugin(registry, pluginOptions) {
  const persistence = createPersistenceInterface(pluginOptions);
  /**
   * Creates an enhanced store dispatch function, triggering the state of the
   * given store name to be persisted when changed.
   *
   * @param {Function}       getState  Function which returns current state.
   * @param {string}         storeName Store name.
   * @param {?Array<string>} keys      Optional subset of keys to save.
=======
/* harmony default export */ var plugins_persistence = (function (registry, pluginOptions) {
  var persistence = createPersistenceInterface(pluginOptions);
  /**
   * Creates an enhanced store dispatch function, triggering the state of the
   * given reducer key to be persisted when changed.
   *
   * @param {Function}       getState   Function which returns current state.
   * @param {string}         reducerKey Reducer key.
   * @param {?Array<string>} keys       Optional subset of keys to save.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
   *
   * @return {Function} Enhanced dispatch function.
   */

<<<<<<< HEAD
  function createPersistOnChange(getState, storeName, keys) {
    let getPersistedState;

    if (Array.isArray(keys)) {
      // Given keys, the persisted state should by produced as an object
      // of the subset of keys. This implementation uses combineReducers
      // to leverage its behavior of returning the same object when none
      // of the property values changes. This allows a strict reference
      // equality to bypass a persistence set on an unchanging state.
      const reducers = keys.reduce((accumulator, key) => Object.assign(accumulator, {
        [key]: (state, action) => action.nextState[key]
      }), {});
      getPersistedState = withLazySameState(build_module_combineReducers(reducers));
    } else {
      getPersistedState = (state, action) => action.nextState;
    }

    let lastState = getPersistedState(undefined, {
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
=======
  function createPersistOnChange(getState, reducerKey, keys) {
    var lastState = getState();
    return function (result) {
      var state = getState();

      if (state !== lastState) {
        if (Array.isArray(keys)) {
          state = Object(external_lodash_["pick"])(state, keys);
        }

        persistence.set(reducerKey, state);
        lastState = state;
      }

      return result;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    };
  }

  return {
<<<<<<< HEAD
    registerStore(storeName, options) {
      if (!options.persist) {
        return registry.registerStore(storeName, options);
      } // Load from persistence to use as initial state.


      const persistedState = persistence.get()[storeName];

      if (persistedState !== undefined) {
        let initialState = options.reducer(options.initialState, {
          type: '@@WP/PERSISTENCE_RESTORE'
        });

        if (is_plain_object_isPlainObject(initialState) && is_plain_object_isPlainObject(persistedState)) {
          // If state is an object, ensure that:
          // - Other keys are left intact when persisting only a
          //   subset of keys.
          // - New keys in what would otherwise be used as initial
          //   state are deeply merged as base for persisted value.
          initialState = cjs_default()(initialState, persistedState, {
            isMergeableObject: is_plain_object_isPlainObject
          });
        } else {
          // If there is a mismatch in object-likeness of default
          // initial or persisted state, defer to persisted value.
          initialState = persistedState;
        }

        options = { ...options,
          initialState
        };
      }

      const store = registry.registerStore(storeName, options);
      store.subscribe(createPersistOnChange(store.getState, storeName, options.persist));
      return store;
    }

  };
}

persistencePlugin.__unstableMigrate = () => {};

/* harmony default export */ const persistence = (persistencePlugin);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/index.js


;// CONCATENATED MODULE: external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","priorityQueue"]
const external_wp_priorityQueue_namespaceObject = window["wp"]["priorityQueue"];
;// CONCATENATED MODULE: external ["wp","isShallowEqual"]
const external_wp_isShallowEqual_namespaceObject = window["wp"]["isShallowEqual"];
var external_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_wp_isShallowEqual_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/registry-provider/context.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


const Context = (0,external_wp_element_namespaceObject.createContext)(default_registry);
const {
  Consumer,
  Provider
} = Context;
/**
 * A custom react Context consumer exposing the provided `registry` to
 * children components. Used along with the RegistryProvider.
 *
 * You can read more about the react context api here:
 * https://reactjs.org/docs/context.html#contextprovider
 *
 * @example
 * ```js
 * import {
 *   RegistryProvider,
 *   RegistryConsumer,
 *   createRegistry
 * } from '@wordpress/data';
 *
 * const registry = createRegistry( {} );
 *
 * const App = ( { props } ) => {
 *   return <RegistryProvider value={ registry }>
 *     <div>Hello There</div>
 *     <RegistryConsumer>
 *       { ( registry ) => (
 *         <ComponentUsingRegistry
 *         		{ ...props }
 *         	  registry={ registry }
 *       ) }
 *     </RegistryConsumer>
 *   </RegistryProvider>
 * }
 * ```
 */

const RegistryConsumer = Consumer;
/**
 * A custom Context provider for exposing the provided `registry` to children
 * components via a consumer.
 *
 * See <a name="#RegistryConsumer">RegistryConsumer</a> documentation for
 * example.
 */

/* harmony default export */ const context = (Provider);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/registry-provider/use-registry.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * A custom react hook exposing the registry context for use.
 *
 * This exposes the `registry` value provided via the
 * <a href="#RegistryProvider">Registry Provider</a> to a component implementing
 * this hook.
 *
 * It acts similarly to the `useContext` react hook.
 *
 * Note: Generally speaking, `useRegistry` is a low level hook that in most cases
 * won't be needed for implementation. Most interactions with the `@wordpress/data`
 * API can be performed via the `useSelect` hook,  or the `withSelect` and
 * `withDispatch` higher order components.
 *
 * @example
 * ```js
 * import {
 *   RegistryProvider,
 *   createRegistry,
 *   useRegistry,
 * } from '@wordpress/data';
 *
 * const registry = createRegistry( {} );
 *
 * const SomeChildUsingRegistry = ( props ) => {
 *   const registry = useRegistry();
 *   // ...logic implementing the registry in other react hooks.
 * };
 *
 *
 * const ParentProvidingRegistry = ( props ) => {
 *   return <RegistryProvider value={ registry }>
 *     <SomeChildUsingRegistry { ...props } />
 *   </RegistryProvider>
 * };
 * ```
 *
 * @return {Function}  A custom react hook exposing the registry context value.
 */

function useRegistry() {
  return (0,external_wp_element_namespaceObject.useContext)(Context);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/async-mode-provider/context.js
/**
 * WordPress dependencies
 */

const context_Context = (0,external_wp_element_namespaceObject.createContext)(false);
const {
  Consumer: context_Consumer,
  Provider: context_Provider
} = context_Context;
const AsyncModeConsumer = (/* unused pure expression or super */ null && (context_Consumer));
/**
 * Context Provider Component used to switch the data module component rerendering
 * between Sync and Async modes.
 *
 * @example
 *
 * ```js
 * import { useSelect, AsyncModeProvider } from '@wordpress/data';
 * import { store as blockEditorStore } from '@wordpress/block-editor';
 *
 * function BlockCount() {
 *   const count = useSelect( ( select ) => {
 *     return select( blockEditorStore ).getBlockCount()
 *   }, [] );
 *
 *   return count;
 * }
 *
 * function App() {
 *   return (
 *     <AsyncModeProvider value={ true }>
 *       <BlockCount />
 *     </AsyncModeProvider>
 *   );
 * }
 * ```
 *
 * In this example, the BlockCount component is rerendered asynchronously.
 * It means if a more critical task is being performed (like typing in an input),
 * the rerendering is delayed until the browser becomes IDLE.
 * It is possible to nest multiple levels of AsyncModeProvider to fine-tune the rendering behavior.
 *
 * @param {boolean} props.value Enable Async Mode.
 * @return {WPComponent} The component to be rendered.
 */

/* harmony default export */ const async_mode_provider_context = (context_Provider);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/async-mode-provider/use-async-mode.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


function useAsyncMode() {
  return (0,external_wp_element_namespaceObject.useContext)(context_Context);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/use-select/index.js
=======
    registerStore: function registerStore(reducerKey, options) {
      if (!options.persist) {
        return registry.registerStore(reducerKey, options);
      }

      var initialState = persistence.get()[reducerKey];
      options = Object(objectSpread["a" /* default */])({}, options, {
        reducer: withInitialState(options.reducer, initialState)
      });
      var store = registry.registerStore(reducerKey, options);
      store.dispatch = Object(external_lodash_["flow"])([store.dispatch, createPersistOnChange(store.getState, reducerKey, options.persist)]);
      return store;
    }
  };
});

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/index.js



// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__("1OyB");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__("vuIU");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__("md7G");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__("foSv");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__("Ji7U");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__("JX7q");

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__("GRId");

// EXTERNAL MODULE: external {"this":["wp","isShallowEqual"]}
var external_this_wp_isShallowEqual_ = __webpack_require__("rl8x");
var external_this_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_isShallowEqual_);

// EXTERNAL MODULE: external {"this":["wp","compose"]}
var external_this_wp_compose_ = __webpack_require__("K9lf");

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/registry-provider/index.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */

<<<<<<< HEAD


=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */



<<<<<<< HEAD
const renderQueue = (0,external_wp_priorityQueue_namespaceObject.createQueue)();
/**
 * @typedef {import('../../types').StoreDescriptor<C>} StoreDescriptor
 * @template {import('../../types').AnyConfig} C
 */

/**
 * @typedef {import('../../types').ReduxStoreConfig<State,Actions,Selectors>} ReduxStoreConfig
 * @template State
 * @template {Record<string,import('../../types').ActionCreator>} Actions
 * @template Selectors
 */

/** @typedef {import('../../types').MapSelect} MapSelect */

/**
 * @typedef {import('../../types').UseSelectReturn<T>} UseSelectReturn
 * @template {MapSelect|StoreDescriptor<any>} T
 */

function Store(registry, suspense) {
  const select = suspense ? registry.suspendSelect : registry.select;
  const queueContext = {};
  let lastMapSelect;
  let lastMapResult;
  let lastMapResultValid = false;
  let lastIsAsync;
  let subscriber;

  const createSubscriber = stores => {
    // The set of stores the `subscribe` function is supposed to subscribe to. Here it is
    // initialized, and then the `updateStores` function can add new stores to it.
    const activeStores = [...stores]; // The `subscribe` function, which is passed to the `useSyncExternalStore` hook, could
    // be called multiple times to establish multiple subscriptions. That's why we need to
    // keep a set of active subscriptions;

    const activeSubscriptions = new Set();

    function subscribe(listener) {
      // Invalidate the value right after subscription was created. React will
      // call `getValue` after subscribing, to detect store updates that happened
      // in the interval between the `getValue` call during render and creating
      // the subscription, which is slightly delayed. We need to ensure that this
      // second `getValue` call will compute a fresh value.
      lastMapResultValid = false;

      const onStoreChange = () => {
        // Invalidate the value on store update, so that a fresh value is computed.
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
          // The return value of the subscribe function could be undefined if the store is a custom generic store.
          unsub?.();
        } // Cancel existing store updates that were already scheduled.


        renderQueue.cancel(queueContext);
      };
    } // Check if `newStores` contains some stores we're not subscribed to yet, and add them.


    function updateStores(newStores) {
      for (const newStore of newStores) {
        if (activeStores.includes(newStore)) {
          continue;
        } // New `subscribe` calls will subscribe to `newStore`, too.


        activeStores.push(newStore); // Add `newStore` to existing subscriptions.

        for (const subscription of activeSubscriptions) {
          subscription(newStore);
        }
      }
    }

    return {
      subscribe,
      updateStores
    };
  };

  return (mapSelect, isAsync) => {
    function updateValue() {
      // If the last value is valid, and the `mapSelect` callback hasn't changed,
      // then we can safely return the cached value. The value can change only on
      // store update, and in that case value will be invalidated by the listener.
      if (lastMapResultValid && mapSelect === lastMapSelect) {
        return lastMapResult;
      }

      const listeningStores = {
        current: null
      };

      const mapResult = registry.__unstableMarkListeningStores(() => mapSelect(select, registry), listeningStores);

      if (!subscriber) {
        subscriber = createSubscriber(listeningStores.current);
      } else {
        subscriber.updateStores(listeningStores.current);
      } // If the new value is shallow-equal to the old one, keep the old one so
      // that we don't trigger unwanted updates that do a `===` check.


      if (!external_wp_isShallowEqual_default()(lastMapResult, mapResult)) {
        lastMapResult = mapResult;
      }

      lastMapSelect = mapSelect;
      lastMapResultValid = true;
    }

    function getValue() {
      // Update the value in case it's been invalidated or `mapSelect` has changed.
      updateValue();
      return lastMapResult;
    } // When transitioning from async to sync mode, cancel existing store updates
    // that have been scheduled, and invalidate the value so that it's freshly
    // computed. It might have been changed by the update we just cancelled.


    if (lastIsAsync && !isAsync) {
      lastMapResultValid = false;
      renderQueue.cancel(queueContext);
    }

    updateValue();
    lastIsAsync = isAsync; // Return a pair of functions that can be passed to `useSyncExternalStore`.

    return {
      subscribe: subscriber.subscribe,
      getValue
    };
  };
}

function useStaticSelect(storeName) {
  return useRegistry().select(storeName);
}

function useMappingSelect(suspense, mapSelect, deps) {
  const registry = useRegistry();
  const isAsync = useAsyncMode();
  const store = (0,external_wp_element_namespaceObject.useMemo)(() => Store(registry, suspense), [registry]);
  const selector = (0,external_wp_element_namespaceObject.useCallback)(mapSelect, deps);
  const {
    subscribe,
    getValue
  } = store(selector, isAsync);
  const result = (0,external_wp_element_namespaceObject.useSyncExternalStore)(subscribe, getValue, getValue);
  (0,external_wp_element_namespaceObject.useDebugValue)(result);
  return result;
}
/**
 * Custom react hook for retrieving props from registered selectors.
 *
 * In general, this custom React hook follows the
 * [rules of hooks](https://reactjs.org/docs/hooks-rules.html).
 *
 * @template {MapSelect | StoreDescriptor<any>} T
 * @param {T}         mapSelect Function called on every state change. The returned value is
 *                              exposed to the component implementing this hook. The function
 *                              receives the `registry.select` method on the first argument
 *                              and the `registry` on the second argument.
 *                              When a store key is passed, all selectors for the store will be
 *                              returned. This is only meant for usage of these selectors in event
 *                              callbacks, not for data needed to create the element tree.
 * @param {unknown[]} deps      If provided, this memoizes the mapSelect so the same `mapSelect` is
 *                              invoked on every state change unless the dependencies change.
 *
 * @example
 * ```js
 * import { useSelect } from '@wordpress/data';
 * import { store as myCustomStore } from 'my-custom-store';
 *
 * function HammerPriceDisplay( { currency } ) {
 *   const price = useSelect( ( select ) => {
 *     return select( myCustomStore ).getPrice( 'hammer', currency );
 *   }, [ currency ] );
 *   return new Intl.NumberFormat( 'en-US', {
 *     style: 'currency',
 *     currency,
 *   } ).format( price );
 * }
 *
 * // Rendered in the application:
 * // <HammerPriceDisplay currency="USD" />
 * ```
 *
 * In the above example, when `HammerPriceDisplay` is rendered into an
 * application, the price will be retrieved from the store state using the
 * `mapSelect` callback on `useSelect`. If the currency prop changes then
 * any price in the state for that currency is retrieved. If the currency prop
 * doesn't change and other props are passed in that do change, the price will
 * not change because the dependency is just the currency.
 *
 * When data is only used in an event callback, the data should not be retrieved
 * on render, so it may be useful to get the selectors function instead.
 *
 * **Don't use `useSelect` this way when calling the selectors in the render
 * function because your component won't re-render on a data change.**
 *
 * ```js
 * import { useSelect } from '@wordpress/data';
 * import { store as myCustomStore } from 'my-custom-store';
 *
 * function Paste( { children } ) {
 *   const { getSettings } = useSelect( myCustomStore );
 *   function onPaste() {
 *     // Do something with the settings.
 *     const settings = getSettings();
 *   }
 *   return <div onPaste={ onPaste }>{ children }</div>;
 * }
 * ```
 * @return {UseSelectReturn<T>} A custom react hook.
 */


function useSelect(mapSelect, deps) {
  // On initial call, on mount, determine the mode of this `useSelect` call
  // and then never allow it to change on subsequent updates.
  const staticSelectMode = typeof mapSelect !== 'function';
  const staticSelectModeRef = (0,external_wp_element_namespaceObject.useRef)(staticSelectMode);

  if (staticSelectMode !== staticSelectModeRef.current) {
    const prevMode = staticSelectModeRef.current ? 'static' : 'mapping';
    const nextMode = staticSelectMode ? 'static' : 'mapping';
    throw new Error(`Switching useSelect from ${prevMode} to ${nextMode} is not allowed`);
  }
  /* eslint-disable react-hooks/rules-of-hooks */
  // `staticSelectMode` is not allowed to change during the hook instance's,
  // lifetime, so the rules of hooks are not really violated.


  return staticSelectMode ? useStaticSelect(mapSelect) : useMappingSelect(false, mapSelect, deps);
  /* eslint-enable react-hooks/rules-of-hooks */
}
/**
 * A variant of the `useSelect` hook that has the same API, but will throw a
 * suspense Promise if any of the called selectors is in an unresolved state.
 *
 * @param {Function} mapSelect Function called on every state change. The
 *                             returned value is exposed to the component
 *                             using this hook. The function receives the
 *                             `registry.suspendSelect` method as the first
 *                             argument and the `registry` as the second one.
 * @param {Array}    deps      A dependency array used to memoize the `mapSelect`
 *                             so that the same `mapSelect` is invoked on every
 *                             state change unless the dependencies change.
 *
 * @return {Object} Data object returned by the `mapSelect` function.
 */

function useSuspenseSelect(mapSelect, deps) {
  return useMappingSelect(true, mapSelect, deps);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/with-select/index.js
=======
var _createContext = Object(external_this_wp_element_["createContext"])(default_registry),
    Consumer = _createContext.Consumer,
    Provider = _createContext.Provider;

var RegistryConsumer = Consumer;
/* harmony default export */ var registry_provider = (Provider);

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/with-select/index.js







>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * WordPress dependencies
 */

<<<<<<< HEAD
=======


>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */


<<<<<<< HEAD
/** @typedef {import('@wordpress/element').WPComponent} WPComponent */

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Higher-order component used to inject state-derived props using registered
 * selectors.
 *
 * @param {Function} mapSelectToProps Function called on every state change,
<<<<<<< HEAD
 *                                    expected to return object of props to
 *                                    merge with the component's own props.
 *
 * @example
 * ```js
 * import { withSelect } from '@wordpress/data';
 * import { store as myCustomStore } from 'my-custom-store';
 *
 * function PriceDisplay( { price, currency } ) {
 * 	return new Intl.NumberFormat( 'en-US', {
 * 		style: 'currency',
 * 		currency,
 * 	} ).format( price );
 * }
 *
 * const HammerPriceDisplay = withSelect( ( select, ownProps ) => {
 * 	const { getPrice } = select( myCustomStore );
 * 	const { currency } = ownProps;
 *
 * 	return {
 * 		price: getPrice( 'hammer', currency ),
 * 	};
 * } )( PriceDisplay );
 *
 * // Rendered in the application:
 * //
 * //  <HammerPriceDisplay currency="USD" />
 * ```
 * In the above example, when `HammerPriceDisplay` is rendered into an
 * application, it will pass the price into the underlying `PriceDisplay`
 * component and update automatically if the price of a hammer ever changes in
 * the store.
 *
 * @return {WPComponent} Enhanced component with merged state data props.
 */

const withSelect = mapSelectToProps => (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(WrappedComponent => (0,external_wp_compose_namespaceObject.pure)(ownProps => {
  const mapSelect = (select, registry) => mapSelectToProps(select, ownProps, registry);

  const mergeProps = useSelect(mapSelect);
  return (0,external_wp_element_namespaceObject.createElement)(WrappedComponent, { ...ownProps,
    ...mergeProps
  });
}), 'withSelect');

/* harmony default export */ const with_select = (withSelect);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/use-dispatch/use-dispatch-with-map.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Custom react hook for returning aggregate dispatch actions using the provided
 * dispatchMap.
 *
 * Currently this is an internal api only and is implemented by `withDispatch`
 *
 * @param {Function} dispatchMap Receives the `registry.dispatch` function as
 *                               the first argument and the `registry` object
 *                               as the second argument.  Should return an
 *                               object mapping props to functions.
 * @param {Array}    deps        An array of dependencies for the hook.
 * @return {Object}  An object mapping props to functions created by the passed
 *                   in dispatchMap.
 */

const useDispatchWithMap = (dispatchMap, deps) => {
  const registry = useRegistry();
  const currentDispatchMap = (0,external_wp_element_namespaceObject.useRef)(dispatchMap);
  (0,external_wp_compose_namespaceObject.useIsomorphicLayoutEffect)(() => {
    currentDispatchMap.current = dispatchMap;
  });
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    const currentDispatchProps = currentDispatchMap.current(registry.dispatch, registry);
    return Object.fromEntries(Object.entries(currentDispatchProps).map(([propName, dispatcher]) => {
      if (typeof dispatcher !== 'function') {
        // eslint-disable-next-line no-console
        console.warn(`Property ${propName} returned from dispatchMap in useDispatchWithMap must be a function.`);
      }

      return [propName, (...args) => currentDispatchMap.current(registry.dispatch, registry)[propName](...args)];
    }));
  }, [registry, ...deps]);
};

/* harmony default export */ const use_dispatch_with_map = (useDispatchWithMap);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/with-dispatch/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/** @typedef {import('@wordpress/element').WPComponent} WPComponent */

/**
 * Higher-order component used to add dispatch props using registered action
 * creators.
 *
 * @param {Function} mapDispatchToProps A function of returning an object of
 *                                      prop names where value is a
 *                                      dispatch-bound action creator, or a
 *                                      function to be called with the
 *                                      component's props and returning an
 *                                      action creator.
 *
 * @example
 * ```jsx
 * function Button( { onClick, children } ) {
 *     return <button type="button" onClick={ onClick }>{ children }</button>;
 * }
 *
 * import { withDispatch } from '@wordpress/data';
 * import { store as myCustomStore } from 'my-custom-store';
 *
 * const SaleButton = withDispatch( ( dispatch, ownProps ) => {
 *     const { startSale } = dispatch( myCustomStore );
 *     const { discountPercent } = ownProps;
 *
 *     return {
 *         onClick() {
 *             startSale( discountPercent );
 *         },
 *     };
 * } )( Button );
 *
 * // Rendered in the application:
 * //
 * // <SaleButton discountPercent="20">Start Sale!</SaleButton>
 * ```
 *
 * @example
 * In the majority of cases, it will be sufficient to use only two first params
 * passed to `mapDispatchToProps` as illustrated in the previous example.
 * However, there might be some very advanced use cases where using the
 * `registry` object might be used as a tool to optimize the performance of
 * your component. Using `select` function from the registry might be useful
 * when you need to fetch some dynamic data from the store at the time when the
 * event is fired, but at the same time, you never use it to render your
 * component. In such scenario, you can avoid using the `withSelect` higher
 * order component to compute such prop, which might lead to unnecessary
 * re-renders of your component caused by its frequent value change.
 * Keep in mind, that `mapDispatchToProps` must return an object with functions
 * only.
 *
 * ```jsx
 * function Button( { onClick, children } ) {
 *     return <button type="button" onClick={ onClick }>{ children }</button>;
 * }
 *
 * import { withDispatch } from '@wordpress/data';
 * import { store as myCustomStore } from 'my-custom-store';
 *
 * const SaleButton = withDispatch( ( dispatch, ownProps, { select } ) => {
 *    // Stock number changes frequently.
 *    const { getStockNumber } = select( myCustomStore );
 *    const { startSale } = dispatch( myCustomStore );
 *    return {
 *        onClick() {
 *            const discountPercent = getStockNumber() > 50 ? 10 : 20;
 *            startSale( discountPercent );
 *        },
 *    };
 * } )( Button );
 *
 * // Rendered in the application:
 * //
 * //  <SaleButton>Start Sale!</SaleButton>
 * ```
 *
 * _Note:_ It is important that the `mapDispatchToProps` function always
 * returns an object with the same keys. For example, it should not contain
 * conditions under which a different value would be returned.
 *
 * @return {WPComponent} Enhanced component with merged dispatcher props.
 */

const withDispatch = mapDispatchToProps => (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(WrappedComponent => ownProps => {
  const mapDispatch = (dispatch, registry) => mapDispatchToProps(dispatch, ownProps, registry);

  const dispatchProps = use_dispatch_with_map(mapDispatch, []);
  return (0,external_wp_element_namespaceObject.createElement)(WrappedComponent, { ...ownProps,
    ...dispatchProps
  });
}, 'withDispatch');

/* harmony default export */ const with_dispatch = (withDispatch);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/with-registry/index.js


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Higher-order component which renders the original component with the current
 * registry context passed as its `registry` prop.
 *
 * @param {WPComponent} OriginalComponent Original component.
 *
 * @return {WPComponent} Enhanced component.
 */

const withRegistry = (0,external_wp_compose_namespaceObject.createHigherOrderComponent)(OriginalComponent => props => (0,external_wp_element_namespaceObject.createElement)(RegistryConsumer, null, registry => (0,external_wp_element_namespaceObject.createElement)(OriginalComponent, { ...props,
  registry: registry
})), 'withRegistry');
/* harmony default export */ const with_registry = (withRegistry);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/use-dispatch/use-dispatch.js
/**
 * Internal dependencies
 */

/**
 * @typedef {import('../../types').StoreDescriptor<StoreConfig>} StoreDescriptor
 * @template {import('../../types').AnyConfig} StoreConfig
 */

/**
 * @typedef {import('../../types').UseDispatchReturn<StoreNameOrDescriptor>} UseDispatchReturn
 * @template StoreNameOrDescriptor
 */

/**
 * A custom react hook returning the current registry dispatch actions creators.
 *
 * Note: The component using this hook must be within the context of a
 * RegistryProvider.
 *
 * @template {undefined | string | StoreDescriptor<any>} StoreNameOrDescriptor
 * @param {StoreNameOrDescriptor} [storeNameOrDescriptor] Optionally provide the name of the
 *                                                        store or its descriptor from which to
 *                                                        retrieve action creators. If not
 *                                                        provided, the registry.dispatch
 *                                                        function is returned instead.
 *
 * @example
 * This illustrates a pattern where you may need to retrieve dynamic data from
 * the server via the `useSelect` hook to use in combination with the dispatch
 * action.
 *
 * ```jsx
 * import { useDispatch, useSelect } from '@wordpress/data';
 * import { useCallback } from '@wordpress/element';
 * import { store as myCustomStore } from 'my-custom-store';
 *
 * function Button( { onClick, children } ) {
 *   return <button type="button" onClick={ onClick }>{ children }</button>
 * }
 *
 * const SaleButton = ( { children } ) => {
 *   const { stockNumber } = useSelect(
 *     ( select ) => select( myCustomStore ).getStockNumber(),
 *     []
 *   );
 *   const { startSale } = useDispatch( myCustomStore );
 *   const onClick = useCallback( () => {
 *     const discountPercent = stockNumber > 50 ? 10: 20;
 *     startSale( discountPercent );
 *   }, [ stockNumber ] );
 *   return <Button onClick={ onClick }>{ children }</Button>
 * }
 *
 * // Rendered somewhere in the application:
 * //
 * // <SaleButton>Start Sale!</SaleButton>
 * ```
 * @return {UseDispatchReturn<StoreNameOrDescriptor>} A custom react hook.
 */

const useDispatch = storeNameOrDescriptor => {
  const {
    dispatch
  } = useRegistry();
  return storeNameOrDescriptor === void 0 ? dispatch : dispatch(storeNameOrDescriptor);
};

/* harmony default export */ const use_dispatch = (useDispatch);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/dispatch.js
/**
 * Internal dependencies
 */

/**
 * Given a store descriptor, returns an object of the store's action creators.
 * Calling an action creator will cause it to be dispatched, updating the state value accordingly.
 *
 * Note: Action creators returned by the dispatch will return a promise when
 * they are called.
 *
 * @param storeNameOrDescriptor The store descriptor. The legacy calling convention of passing
 *                              the store name is also supported.
 *
 * @example
 * ```js
 * import { dispatch } from '@wordpress/data';
 * import { store as myCustomStore } from 'my-custom-store';
 *
 * dispatch( myCustomStore ).setPrice( 'hammer', 9.75 );
 * ```
 * @return Object containing the action creators.
 */

function dispatch_dispatch(storeNameOrDescriptor) {
  return default_registry.dispatch(storeNameOrDescriptor);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/select.js
=======
 *                                   expected to return object of props to
 *                                   merge with the component's own props.
 *
 * @return {Component} Enhanced component with merged state data props.
 */

var with_select_withSelect = function withSelect(mapSelectToProps) {
  return Object(external_this_wp_compose_["createHigherOrderComponent"])(function (WrappedComponent) {
    /**
     * Default merge props. A constant value is used as the fallback since it
     * can be more efficiently shallow compared in case component is repeatedly
    	 * rendered without its own merge props.
     *
     * @type {Object}
     */
    var DEFAULT_MERGE_PROPS = {};
    /**
     * Given a props object, returns the next merge props by mapSelectToProps.
     *
     * @param {Object} props Props to pass as argument to mapSelectToProps.
     *
     * @return {Object} Props to merge into rendered wrapped element.
     */

    function getNextMergeProps(props) {
      return mapSelectToProps(props.registry.select, props.ownProps, props.registry) || DEFAULT_MERGE_PROPS;
    }

    var ComponentWithSelect =
    /*#__PURE__*/
    function (_Component) {
      Object(inherits["a" /* default */])(ComponentWithSelect, _Component);

      function ComponentWithSelect(props) {
        var _this;

        Object(classCallCheck["a" /* default */])(this, ComponentWithSelect);

        _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ComponentWithSelect).call(this, props));
        _this.onStoreChange = _this.onStoreChange.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));

        _this.subscribe(props.registry);

        _this.mergeProps = getNextMergeProps(props);
        return _this;
      }

      Object(createClass["a" /* default */])(ComponentWithSelect, [{
        key: "componentDidMount",
        value: function componentDidMount() {
          this.canRunSelection = true; // A state change may have occurred between the constructor and
          // mount of the component (e.g. during the wrapped component's own
          // constructor), in which case selection should be rerun.

          if (this.hasQueuedSelection) {
            this.hasQueuedSelection = false;
            this.onStoreChange();
          }
        }
      }, {
        key: "componentWillUnmount",
        value: function componentWillUnmount() {
          this.canRunSelection = false;
          this.unsubscribe();
        }
      }, {
        key: "shouldComponentUpdate",
        value: function shouldComponentUpdate(nextProps, nextState) {
          // Cycle subscription if registry changes.
          var hasRegistryChanged = nextProps.registry !== this.props.registry;

          if (hasRegistryChanged) {
            this.unsubscribe();
            this.subscribe(nextProps.registry);
          } // Treat a registry change as equivalent to `ownProps`, to reflect
          // `mergeProps` to rendered component if and only if updated.


          var hasPropsChanged = hasRegistryChanged || !external_this_wp_isShallowEqual_default()(this.props.ownProps, nextProps.ownProps); // Only render if props have changed or merge props have been updated
          // from the store subscriber.

          if (this.state === nextState && !hasPropsChanged) {
            return false;
          }

          if (hasPropsChanged) {
            var nextMergeProps = getNextMergeProps(nextProps);

            if (!external_this_wp_isShallowEqual_default()(this.mergeProps, nextMergeProps)) {
              // If merge props change as a result of the incoming props,
              // they should be reflected as such in the upcoming render.
              // While side effects are discouraged in lifecycle methods,
              // this component is used heavily, and prior efforts to use
              // `getDerivedStateFromProps` had demonstrated miserable
              // performance.
              this.mergeProps = nextMergeProps;
            } // Regardless whether merge props are changing, fall through to
            // incur the render since the component will need to receive
            // the changed `ownProps`.

          }

          return true;
        }
      }, {
        key: "onStoreChange",
        value: function onStoreChange() {
          if (!this.canRunSelection) {
            this.hasQueuedSelection = true;
            return;
          }

          var nextMergeProps = getNextMergeProps(this.props);

          if (external_this_wp_isShallowEqual_default()(this.mergeProps, nextMergeProps)) {
            return;
          }

          this.mergeProps = nextMergeProps; // Schedule an update. Merge props are not assigned to state since
          // derivation of merge props from incoming props occurs within
          // shouldComponentUpdate, where setState is not allowed. setState
          // is used here instead of forceUpdate because forceUpdate bypasses
          // shouldComponentUpdate altogether, which isn't desireable if both
          // state and props change within the same render. Unfortunately,
          // this requires that next merge props are generated twice.

          this.setState({});
        }
      }, {
        key: "subscribe",
        value: function subscribe(registry) {
          this.unsubscribe = registry.subscribe(this.onStoreChange);
        }
      }, {
        key: "render",
        value: function render() {
          return Object(external_this_wp_element_["createElement"])(WrappedComponent, Object(esm_extends["a" /* default */])({}, this.props.ownProps, this.mergeProps));
        }
      }]);

      return ComponentWithSelect;
    }(external_this_wp_element_["Component"]);

    return function (ownProps) {
      return Object(external_this_wp_element_["createElement"])(RegistryConsumer, null, function (registry) {
        return Object(external_this_wp_element_["createElement"])(ComponentWithSelect, {
          ownProps: ownProps,
          registry: registry
        });
      });
    };
  }, 'withSelect');
};

/* harmony default export */ var with_select = (with_select_withSelect);

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/with-dispatch/index.js








/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Internal dependencies
 */

<<<<<<< HEAD
/**
 * Given a store descriptor, returns an object of the store's selectors.
 * The selector functions are been pre-bound to pass the current state automatically.
 * As a consumer, you need only pass arguments of the selector, if applicable.
 *
 *
 * @param storeNameOrDescriptor The store descriptor. The legacy calling convention
 *                              of passing the store name is also supported.
 *
 * @example
 * ```js
 * import { select } from '@wordpress/data';
 * import { store as myCustomStore } from 'my-custom-store';
 *
 * select( myCustomStore ).getPrice( 'hammer' );
 * ```
 *
 * @return Object containing the store's selectors.
 */

function select_select(storeNameOrDescriptor) {
  return default_registry.select(storeNameOrDescriptor);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/index.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */



/** @typedef {import('./types').StoreDescriptor} StoreDescriptor */








=======

/**
 * Higher-order component used to add dispatch props using registered action
 * creators.
 *
 * @param {Object} mapDispatchToProps Object of prop names where value is a
 *                                    dispatch-bound action creator, or a
 *                                    function to be called with with the
 *                                    component's props and returning an
 *                                    action creator.
 *
 * @return {Component} Enhanced component with merged dispatcher props.
 */

var with_dispatch_withDispatch = function withDispatch(mapDispatchToProps) {
  return Object(external_this_wp_compose_["createHigherOrderComponent"])(function (WrappedComponent) {
    var ComponentWithDispatch =
    /*#__PURE__*/
    function (_Component) {
      Object(inherits["a" /* default */])(ComponentWithDispatch, _Component);

      function ComponentWithDispatch(props) {
        var _this;

        Object(classCallCheck["a" /* default */])(this, ComponentWithDispatch);

        _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(ComponentWithDispatch).apply(this, arguments));
        _this.proxyProps = {};

        _this.setProxyProps(props);

        return _this;
      }

      Object(createClass["a" /* default */])(ComponentWithDispatch, [{
        key: "proxyDispatch",
        value: function proxyDispatch(propName) {
          var _mapDispatchToProps;

          for (var _len = arguments.length, args = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
            args[_key - 1] = arguments[_key];
          }

          // Original dispatcher is a pre-bound (dispatching) action creator.
          (_mapDispatchToProps = mapDispatchToProps(this.props.registry.dispatch, this.props.ownProps, this.props.registry))[propName].apply(_mapDispatchToProps, args);
        }
      }, {
        key: "setProxyProps",
        value: function setProxyProps(props) {
          var _this2 = this;

          // Assign as instance property so that in subsequent render
          // reconciliation, the prop values are referentially equal.
          // Importantly, note that while `mapDispatchToProps` is
          // called, it is done only to determine the keys for which
          // proxy functions should be created. The actual registry
          // dispatch does not occur until the function is called.
          var propsToDispatchers = mapDispatchToProps(this.props.registry.dispatch, props.ownProps, this.props.registry);
          this.proxyProps = Object(external_lodash_["mapValues"])(propsToDispatchers, function (dispatcher, propName) {
            if (typeof dispatcher !== 'function') {
              // eslint-disable-next-line no-console
              console.warn("Property ".concat(propName, " returned from mapDispatchToProps in withDispatch must be a function."));
            } // Prebind with prop name so we have reference to the original
            // dispatcher to invoke. Track between re-renders to avoid
            // creating new function references every render.


            if (_this2.proxyProps.hasOwnProperty(propName)) {
              return _this2.proxyProps[propName];
            }

            return _this2.proxyDispatch.bind(_this2, propName);
          });
        }
      }, {
        key: "render",
        value: function render() {
          return Object(external_this_wp_element_["createElement"])(WrappedComponent, Object(esm_extends["a" /* default */])({}, this.props.ownProps, this.proxyProps));
        }
      }]);

      return ComponentWithDispatch;
    }(external_this_wp_element_["Component"]);

    return function (ownProps) {
      return Object(external_this_wp_element_["createElement"])(RegistryConsumer, null, function (registry) {
        return Object(external_this_wp_element_["createElement"])(ComponentWithDispatch, {
          ownProps: ownProps,
          registry: registry
        });
      });
    };
  }, 'withDispatch');
};

/* harmony default export */ var with_dispatch = (with_dispatch_withDispatch);

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/index.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9






<<<<<<< HEAD
/**
 * Object of available plugins to use with a registry.
 *
 * @see [use](#use)
 *
 * @type {Object}
 */
=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * The combineReducers helper function turns an object whose values are different
 * reducing functions into a single reducing function you can pass to registerReducer.
 *
<<<<<<< HEAD
 * @type  {import('./types').combineReducers}
 * @param {Object} reducers An object whose values correspond to different reducing
 *                          functions that need to be combined into one.
 *
 * @example
 * ```js
 * import { combineReducers, createReduxStore, register } from '@wordpress/data';
 *
 * const prices = ( state = {}, action ) => {
 * 	return action.type === 'SET_PRICE' ?
 * 		{
 * 			...state,
 * 			[ action.item ]: action.price,
 * 		} :
 * 		state;
 * };
 *
 * const discountPercent = ( state = 0, action ) => {
 * 	return action.type === 'START_SALE' ?
 * 		action.discountPercent :
 * 		state;
 * };
 *
 * const store = createReduxStore( 'my-shop', {
 * 	reducer: combineReducers( {
 * 		prices,
 * 		discountPercent,
 * 	} ),
 * } );
 * register( store );
 * ```
 *
 * @return {Function} A reducer that invokes every reducer inside the reducers
 *                    object, and constructs a state object with the same shape.
 */

const build_module_combineReducers = (turbo_combine_reducers_default());
/**
 * Given a store descriptor, returns an object containing the store's selectors pre-bound to state
 * so that you only need to supply additional arguments, and modified so that they return promises
 * that resolve to their eventual values, after any resolvers have ran.
 *
 * @param {StoreDescriptor|string} storeNameOrDescriptor The store descriptor. The legacy calling
 *                                                       convention of passing the store name is
 *                                                       also supported.
 *
 * @example
 * ```js
 * import { resolveSelect } from '@wordpress/data';
 * import { store as myCustomStore } from 'my-custom-store';
 *
 * resolveSelect( myCustomStore ).getPrice( 'hammer' ).then(console.log)
 * ```
 *
 * @return {Object} Object containing the store's promise-wrapped selectors.
 */

const build_module_resolveSelect = default_registry.resolveSelect;
/**
 * Given a store descriptor, returns an object containing the store's selectors pre-bound to state
 * so that you only need to supply additional arguments, and modified so that they throw promises
 * in case the selector is not resolved yet.
 *
 * @param {StoreDescriptor|string} storeNameOrDescriptor The store descriptor. The legacy calling
 *                                                       convention of passing the store name is
 *                                                       also supported.
 *
 * @return {Object} Object containing the store's suspense-wrapped selectors.
 */

const suspendSelect = default_registry.suspendSelect;
/**
 * Given a listener function, the function will be called any time the state value
 * of one of the registered stores has changed. If you specify the optional
 * `storeNameOrDescriptor` parameter, the listener function will be called only
 * on updates on that one specific registered store.
 *
 * This function returns an `unsubscribe` function used to stop the subscription.
 *
 * @param {Function}                listener              Callback function.
 * @param {string|StoreDescriptor?} storeNameOrDescriptor Optional store name.
 *
 * @example
 * ```js
 * import { subscribe } from '@wordpress/data';
 *
 * const unsubscribe = subscribe( () => {
 * 	// You could use this opportunity to test whether the derived result of a
 * 	// selector has subsequently changed as the result of a state update.
 * } );
 *
 * // Later, if necessary...
 * unsubscribe();
 * ```
 */

const subscribe = default_registry.subscribe;
/**
 * Registers a generic store instance.
 *
 * @deprecated Use `register( storeDescriptor )` instead.
 *
 * @param {string} name  Store registry name.
 * @param {Object} store Store instance (`{ getSelectors, getActions, subscribe }`).
 */

const registerGenericStore = default_registry.registerGenericStore;
/**
 * Registers a standard `@wordpress/data` store.
 *
 * @deprecated Use `register` instead.
 *
 * @param {string} storeName Unique namespace identifier for the store.
 * @param {Object} options   Store description (reducer, actions, selectors, resolvers).
 *
 * @return {Object} Registered store object.
 */

const registerStore = default_registry.registerStore;
/**
 * Extends a registry to inherit functionality provided by a given plugin. A
 * plugin is an object with properties aligning to that of a registry, merged
 * to extend the default registry behavior.
 *
 * @param {Object} plugin Plugin object.
 */

const use = default_registry.use;
/**
 * Registers a standard `@wordpress/data` store descriptor.
 *
 * @example
 * ```js
 * import { createReduxStore, register } from '@wordpress/data';
 *
 * const store = createReduxStore( 'demo', {
 *     reducer: ( state = 'OK' ) => state,
 *     selectors: {
 *         getValue: ( state ) => state,
 *     },
 * } );
 * register( store );
 * ```
 *
 * @param {StoreDescriptor} store Store descriptor.
 */

const register = default_registry.register;

})();

(window.wp = window.wp || {}).data = __webpack_exports__;
/******/ })()
;
=======
 * @param {Object} reducers An object whose values correspond to different reducing
 *                          functions that need to be combined into one.
 *
 * @return {Function}       A reducer that invokes every reducer inside the reducers
 *                          object, and constructs a state object with the same shape.
 */


var build_module_select = default_registry.select;
var build_module_dispatch = default_registry.dispatch;
var build_module_subscribe = default_registry.subscribe;
var build_module_registerGenericStore = default_registry.registerGenericStore;
var build_module_registerStore = default_registry.registerStore;
var build_module_use = default_registry.use;


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

/***/ "rl8x":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["isShallowEqual"]; }());

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

/***/ }),

/***/ "vuIU":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _createClass; });
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
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
