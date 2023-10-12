/******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 1919:
/***/ (function(module) {

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
/***/ (function(module) {

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
/***/ (function(module) {

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
/******/ 	!function() {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = function(module) {
/******/ 			var getter = module && module.__esModule ?
/******/ 				function() { return module['default']; } :
/******/ 				function() { return module; };
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
!function() {
"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "AsyncModeProvider": function() { return /* reexport */ async_mode_provider_context; },
  "RegistryConsumer": function() { return /* reexport */ RegistryConsumer; },
  "RegistryProvider": function() { return /* reexport */ context; },
  "combineReducers": function() { return /* binding */ build_module_combineReducers; },
  "controls": function() { return /* reexport */ controls; },
  "createReduxStore": function() { return /* reexport */ createReduxStore; },
  "createRegistry": function() { return /* reexport */ createRegistry; },
  "createRegistryControl": function() { return /* reexport */ createRegistryControl; },
  "createRegistrySelector": function() { return /* reexport */ createRegistrySelector; },
  "dispatch": function() { return /* reexport */ dispatch_dispatch; },
  "plugins": function() { return /* reexport */ plugins_namespaceObject; },
  "register": function() { return /* binding */ register; },
  "registerGenericStore": function() { return /* binding */ registerGenericStore; },
  "registerStore": function() { return /* binding */ registerStore; },
  "resolveSelect": function() { return /* binding */ build_module_resolveSelect; },
  "select": function() { return /* reexport */ select_select; },
  "subscribe": function() { return /* binding */ subscribe; },
  "suspendSelect": function() { return /* binding */ suspendSelect; },
  "use": function() { return /* binding */ use; },
  "useDispatch": function() { return /* reexport */ use_dispatch; },
  "useRegistry": function() { return /* reexport */ useRegistry; },
  "useSelect": function() { return /* reexport */ useSelect; },
  "useSuspenseSelect": function() { return /* reexport */ useSuspenseSelect; },
  "withDispatch": function() { return /* reexport */ with_dispatch; },
  "withRegistry": function() { return /* reexport */ with_registry; },
  "withSelect": function() { return /* reexport */ with_select; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/data/build-module/redux-store/metadata/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  "getCachedResolvers": function() { return getCachedResolvers; },
  "getIsResolving": function() { return getIsResolving; },
  "getResolutionError": function() { return getResolutionError; },
  "getResolutionState": function() { return getResolutionState; },
  "hasFinishedResolution": function() { return hasFinishedResolution; },
  "hasResolutionFailed": function() { return hasResolutionFailed; },
  "hasResolvingSelectors": function() { return hasResolvingSelectors; },
  "hasStartedResolution": function() { return hasStartedResolution; },
  "isResolving": function() { return isResolving; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/data/build-module/redux-store/metadata/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  "failResolution": function() { return failResolution; },
  "failResolutions": function() { return failResolutions; },
  "finishResolution": function() { return finishResolution; },
  "finishResolutions": function() { return finishResolutions; },
  "invalidateResolution": function() { return invalidateResolution; },
  "invalidateResolutionForStore": function() { return invalidateResolutionForStore; },
  "invalidateResolutionForStoreSelector": function() { return invalidateResolutionForStoreSelector; },
  "startResolution": function() { return startResolution; },
  "startResolutions": function() { return startResolutions; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/data/build-module/plugins/index.js
var plugins_namespaceObject = {};
__webpack_require__.r(plugins_namespaceObject);
__webpack_require__.d(plugins_namespaceObject, {
  "persistence": function() { return persistence; }
});

// EXTERNAL MODULE: ./node_modules/turbo-combine-reducers/index.js
var turbo_combine_reducers = __webpack_require__(9125);
var turbo_combine_reducers_default = /*#__PURE__*/__webpack_require__.n(turbo_combine_reducers);
;// CONCATENATED MODULE: external ["wp","deprecated"]
var external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/typeof.js
function _typeof(o) {
  "@babel/helpers - typeof";

  return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (o) {
    return typeof o;
  } : function (o) {
    return o && "function" == typeof Symbol && o.constructor === Symbol && o !== Symbol.prototype ? "symbol" : typeof o;
  }, _typeof(o);
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

function ownKeys(e, r) {
  var t = Object.keys(e);
  if (Object.getOwnPropertySymbols) {
    var o = Object.getOwnPropertySymbols(e);
    r && (o = o.filter(function (r) {
      return Object.getOwnPropertyDescriptor(e, r).enumerable;
    })), t.push.apply(t, o);
  }
  return t;
}
function _objectSpread2(e) {
  for (var r = 1; r < arguments.length; r++) {
    var t = null != arguments[r] ? arguments[r] : {};
    r % 2 ? ownKeys(Object(t), !0).forEach(function (r) {
      _defineProperty(e, r, t[r]);
    }) : Object.getOwnPropertyDescriptors ? Object.defineProperties(e, Object.getOwnPropertyDescriptors(t)) : ownKeys(Object(t)).forEach(function (r) {
      Object.defineProperty(e, r, Object.getOwnPropertyDescriptor(t, r));
    });
  }
  return e;
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
var external_wp_reduxRoutine_namespaceObject = window["wp"]["reduxRoutine"];
var external_wp_reduxRoutine_default = /*#__PURE__*/__webpack_require__.n(external_wp_reduxRoutine_namespaceObject);
;// CONCATENATED MODULE: external ["wp","compose"]
var external_wp_compose_namespaceObject = window["wp"]["compose"];
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
var external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/lock-unlock.js
/**
 * WordPress dependencies
 */

const {
  lock,
  unlock
} = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)('I know using unstable features means my plugin or theme will inevitably break on the next WordPress release.', '@wordpress/data');

;// CONCATENATED MODULE: ./node_modules/is-promise/index.mjs
function isPromise(obj) {
  return !!obj && (typeof obj === 'object' || typeof obj === 'function') && typeof obj.then === 'function';
}

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

/* harmony default export */ var promise_middleware = (promiseMiddleware);

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
/* harmony default export */ var store = (coreDataStore);

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

/* harmony default export */ var resolvers_cache_middleware = (createResolversCacheMiddleware);

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

/* harmony default export */ var metadata_reducer = (isResolved);

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
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */



/**
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
 */

/**
 * Internal dependencies
 */





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
/**
 * Creates a new store registry, given an optional object of initial store
 * configurations.
 *
 * @param {Object}  storeConfigs Initial store configurations.
 * @param {Object?} parent       Parent registry.
 *
 * @return {WPDataRegistry} Data registry.
 */


function createRegistry(storeConfigs = {}, parent = null) {
  const stores = {};
  const emitter = createEmitter();
  let listeningStores = null;
  /**
   * Global listener called for each store's update.
   */

  function globalListener() {
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
  };
  /**
   * Calls a selector given the current state and extra arguments.
   *
   * @param {string|StoreDescriptor} storeNameOrDescriptor Unique namespace identifier for the store
   *                                                       or the store descriptor.
   *
   * @return {*} The selector's returned value.
   */


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
  }
  /**
   * Returns the available actions for a part of the state.
   *
   * @param {string|StoreDescriptor} storeNameOrDescriptor Unique namespace identifier for the store
   *                                                       or the store descriptor.
   *
   * @return {*} The action's returned value.
   */


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
   *
   * @return {Object} Registered store object.
   */


  function registerStore(storeName, options) {
    if (!options.reducer) {
      throw new TypeError('Must specify store reducer');
    }

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

/* harmony default export */ var default_registry = (createRegistry());

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
    objectStorage = Object.create(null);
  }

};
/* harmony default export */ var object = (storage);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/persistence/storage/default.js
/**
 * Internal dependencies
 */

let default_storage;

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

/* harmony default export */ var storage_default = (default_storage);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/persistence/index.js
/**
 * External dependencies
 */


/**
 * Internal dependencies
 */



/** @typedef {import('../../registry').WPDataRegistry} WPDataRegistry */

/** @typedef {import('../../registry').WPDataPlugin} WPDataPlugin */

/**
 * @typedef {Object} WPDataPersistencePluginOptions Persistence plugin options.
 *
 * @property {Storage} storage    Persistent storage implementation. This must
 *                                at least implement `getItem` and `setItem` of
 *                                the Web Storage API.
 * @property {string}  storageKey Key on which to set in persistent storage.
 *
 */

/**
 * Default plugin storage.
 *
 * @type {Storage}
 */

const DEFAULT_STORAGE = storage_default;
/**
 * Default plugin storage key.
 *
 * @type {string}
 */

const DEFAULT_STORAGE_KEY = 'WP_DATA';
/**
 * Higher-order reducer which invokes the original reducer only if state is
 * inequal from that of the action's `nextState` property, otherwise returning
 * the original state reference.
 *
 * @param {Function} reducer Original reducer.
 *
 * @return {Function} Enhanced reducer.
 */

const withLazySameState = reducer => (state, action) => {
  if (action.nextState === state) {
    return state;
  }

  return reducer(state, action);
};
/**
 * Creates a persistence interface, exposing getter and setter methods (`get`
 * and `set` respectively).
 *
 * @param {WPDataPersistencePluginOptions} options Plugin options.
 *
 * @return {Object} Persistence interface.
 */

function createPersistenceInterface(options) {
  const {
    storage = DEFAULT_STORAGE,
    storageKey = DEFAULT_STORAGE_KEY
  } = options;
  let data;
  /**
   * Returns the persisted data as an object, defaulting to an empty object.
   *
   * @return {Object} Persisted data.
   */

  function getData() {
    if (data === undefined) {
      // If unset, getItem is expected to return null. Fall back to
      // empty object.
      const persisted = storage.getItem(storageKey);

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


  function setData(key, value) {
    data = { ...data,
      [key]: value
    };
    storage.setItem(storageKey, JSON.stringify(data));
  }

  return {
    get: getData,
    set: setData
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

function persistencePlugin(registry, pluginOptions) {
  const persistence = createPersistenceInterface(pluginOptions);
  /**
   * Creates an enhanced store dispatch function, triggering the state of the
   * given store name to be persisted when changed.
   *
   * @param {Function}       getState  Function which returns current state.
   * @param {string}         storeName Store name.
   * @param {?Array<string>} keys      Optional subset of keys to save.
   *
   * @return {Function} Enhanced dispatch function.
   */

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
    };
  }

  return {
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

/* harmony default export */ var persistence = (persistencePlugin);

;// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/index.js


;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: external ["wp","priorityQueue"]
var external_wp_priorityQueue_namespaceObject = window["wp"]["priorityQueue"];
;// CONCATENATED MODULE: external ["wp","isShallowEqual"]
var external_wp_isShallowEqual_namespaceObject = window["wp"]["isShallowEqual"];
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

/* harmony default export */ var context = (Provider);

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

/* harmony default export */ var async_mode_provider_context = (context_Provider);

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
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */



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


/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/** @typedef {import('@wordpress/element').WPComponent} WPComponent */

/**
 * Higher-order component used to inject state-derived props using registered
 * selectors.
 *
 * @param {Function} mapSelectToProps Function called on every state change,
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

/* harmony default export */ var with_select = (withSelect);

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

/* harmony default export */ var use_dispatch_with_map = (useDispatchWithMap);

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

/* harmony default export */ var with_dispatch = (withDispatch);

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
/* harmony default export */ var with_registry = (withRegistry);

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

/* harmony default export */ var use_dispatch = (useDispatch);

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
/**
 * Internal dependencies
 */

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














/**
 * Object of available plugins to use with a registry.
 *
 * @see [use](#use)
 *
 * @type {Object}
 */


/**
 * The combineReducers helper function turns an object whose values are different
 * reducing functions into a single reducing function you can pass to registerReducer.
 *
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

}();
(window.wp = window.wp || {}).data = __webpack_exports__;
/******/ })()
;