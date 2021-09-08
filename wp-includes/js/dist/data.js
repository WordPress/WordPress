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

function isPromise(obj) {
  return !!obj && (typeof obj === 'object' || typeof obj === 'function') && typeof obj.then === 'function';
}


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



/**
 * External dependencies
 */


/**
 * Internal dependencies
 */



/**
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
 */

/**
 * Internal dependencies
 */



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

/**
 * Creates a new store registry, given an optional object of initial store
 * configurations.
 *
 * @param {Object} storeConfigs Initial store configurations.
 *
 * @return {WPDataRegistry} Data registry.
 */

function createRegistry() {
  var storeConfigs = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var stores = {};
  var listeners = [];
  /**
   * Global listener called for each store's update.
   */

  function globalListener() {
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
  };
  /**
   * Calls a selector given the current state and extra arguments.
   *
   * @param {string} reducerKey Part of the state shape to register the
   *                            selectors for.
   *
   * @return {*} The selector's returned value.
   */


  function select(reducerKey) {
    var store = stores[reducerKey];
    return store && store.getSelectors();
  }
  /**
   * Returns the available actions for a part of the state.
   *
   * @param {string} reducerKey Part of the state shape to dispatch the
   *                            action for.
   *
   * @return {*} The action's returned value.
   */


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
   *
   * @return {Object} Registered store object.
   */

  registry.registerStore = function (reducerKey, options) {
    if (!options.reducer) {
      throw new TypeError('Must specify store reducer');
    }

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
    if (!objectStorage || !objectStorage[key]) {
      return null;
    }

    return objectStorage[key];
  },
  setItem: function setItem(key, value) {
    if (!objectStorage) {
      object_storage.clear();
    }

    objectStorage[key] = String(value);
  },
  clear: function clear() {
    objectStorage = Object.create(null);
  }
};
/* harmony default export */ var object = (object_storage);

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/persistence/storage/default.js
/**
 * Internal dependencies
 */

var default_storage;

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

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/plugins/persistence/index.js



/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/**
 * Persistence plugin options.
 *
 * @property {Storage} storage    Persistent storage implementation. This must
 *                                at least implement `getItem` and `setItem` of
 *                                the Web Storage API.
 * @property {string}  storageKey Key on which to set in persistent storage.
 *
 * @typedef {WPDataPersistencePluginOptions}
 */

/**
 * Default plugin storage.
 *
 * @type {Storage}
 */

var DEFAULT_STORAGE = storage_default;
/**
 * Default plugin storage key.
 *
 * @type {string}
 */

var DEFAULT_STORAGE_KEY = 'WP_DATA';
/**
 * Higher-order reducer to provides an initial value when state is undefined.
 *
 * @param {Function} reducer      Original reducer.
 * @param {*}         initialState Value to use as initial state.
 *
 * @return {Function} Enhanced reducer.
 */

function withInitialState(reducer, initialState) {
  return function () {
    var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : initialState;
    var action = arguments.length > 1 ? arguments[1] : undefined;
    return reducer(state, action);
  };
}
/**
 * Creates a persistence interface, exposing getter and setter methods (`get`
 * and `set` respectively).
 *
 * @param {WPDataPersistencePluginOptions} options Plugin options.
 *
 * @return {Object} Persistence interface.
 */

function createPersistenceInterface(options) {
  var _options$storage = options.storage,
      storage = _options$storage === void 0 ? DEFAULT_STORAGE : _options$storage,
      _options$storageKey = options.storageKey,
      storageKey = _options$storageKey === void 0 ? DEFAULT_STORAGE_KEY : _options$storageKey;
  var data;
  /**
   * Returns the persisted data as an object, defaulting to an empty object.
   *
   * @return {Object} Persisted data.
   */

  function get() {
    if (data === undefined) {
      // If unset, getItem is expected to return null. Fall back to
      // empty object.
      var persisted = storage.getItem(storageKey);

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


  function set(key, value) {
    data = Object(objectSpread["a" /* default */])({}, data, Object(defineProperty["a" /* default */])({}, key, value));
    storage.setItem(storageKey, JSON.stringify(data));
  }

  return {
    get: get,
    set: set
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

/* harmony default export */ var plugins_persistence = (function (registry, pluginOptions) {
  var persistence = createPersistenceInterface(pluginOptions);
  /**
   * Creates an enhanced store dispatch function, triggering the state of the
   * given reducer key to be persisted when changed.
   *
   * @param {Function}       getState   Function which returns current state.
   * @param {string}         reducerKey Reducer key.
   * @param {?Array<string>} keys       Optional subset of keys to save.
   *
   * @return {Function} Enhanced dispatch function.
   */

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
    };
  }

  return {
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
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



var _createContext = Object(external_this_wp_element_["createContext"])(default_registry),
    Consumer = _createContext.Consumer,
    Provider = _createContext.Provider;

var RegistryConsumer = Consumer;
/* harmony default export */ var registry_provider = (Provider);

// CONCATENATED MODULE: ./node_modules/@wordpress/data/build-module/components/with-select/index.js









/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


/**
 * Higher-order component used to inject state-derived props using registered
 * selectors.
 *
 * @param {Function} mapSelectToProps Function called on every state change,
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



/**
 * Internal dependencies
 */


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








/**
 * The combineReducers helper function turns an object whose values are different
 * reducing functions into a single reducing function you can pass to registerReducer.
 *
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