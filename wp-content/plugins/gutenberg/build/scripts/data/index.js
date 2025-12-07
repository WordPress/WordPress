"use strict";
var wp;
(wp ||= {}).data = (() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __commonJS = (cb, mod) => function __require() {
    return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
  };
  var __export = (target, all) => {
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
  };
  var __copyProps = (to, from, except, desc) => {
    if (from && typeof from === "object" || typeof from === "function") {
      for (let key of __getOwnPropNames(from))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from[key], enumerable: !(desc = __getOwnPropDesc(from, key)) || desc.enumerable });
    }
    return to;
  };
  var __toESM = (mod, isNodeMode, target) => (target = mod != null ? __create(__getProtoOf(mod)) : {}, __copyProps(
    // If the importer is in node compatibility mode or this is not an ESM
    // file that has been converted to a CommonJS file using a Babel-
    // compatible transform (i.e. "__esModule" has not been set), then set
    // "default" to the CommonJS "module.exports" for node compatibility.
    isNodeMode || !mod || !mod.__esModule ? __defProp(target, "default", { value: mod, enumerable: true }) : target,
    mod
  ));
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // package-external:@wordpress/deprecated
  var require_deprecated = __commonJS({
    "package-external:@wordpress/deprecated"(exports, module) {
      module.exports = window.wp.deprecated;
    }
  });

  // node_modules/equivalent-key-map/equivalent-key-map.js
  var require_equivalent_key_map = __commonJS({
    "node_modules/equivalent-key-map/equivalent-key-map.js"(exports, module) {
      "use strict";
      function _typeof(obj) {
        if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
          _typeof = function(obj2) {
            return typeof obj2;
          };
        } else {
          _typeof = function(obj2) {
            return obj2 && typeof Symbol === "function" && obj2.constructor === Symbol && obj2 !== Symbol.prototype ? "symbol" : typeof obj2;
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
      function getValuePair(instance, key) {
        var _map = instance._map, _arrayTreeMap = instance._arrayTreeMap, _objectTreeMap = instance._objectTreeMap;
        if (_map.has(key)) {
          return _map.get(key);
        }
        var properties = Object.keys(key).sort();
        var map = Array.isArray(key) ? _arrayTreeMap : _objectTreeMap;
        for (var i = 0; i < properties.length; i++) {
          var property = properties[i];
          map = map.get(property);
          if (map === void 0) {
            return;
          }
          var propertyValue = key[property];
          map = map.get(propertyValue);
          if (map === void 0) {
            return;
          }
        }
        var valuePair = map.get("_ekm_value");
        if (!valuePair) {
          return;
        }
        _map.delete(valuePair[0]);
        valuePair[0] = key;
        map.set("_ekm_value", valuePair);
        _map.set(key, valuePair);
        return valuePair;
      }
      var EquivalentKeyMap3 = /* @__PURE__ */ (function() {
        function EquivalentKeyMap4(iterable) {
          _classCallCheck(this, EquivalentKeyMap4);
          this.clear();
          if (iterable instanceof EquivalentKeyMap4) {
            var iterablePairs = [];
            iterable.forEach(function(value, key) {
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
        _createClass(EquivalentKeyMap4, [{
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
            if (key === null || _typeof(key) !== "object") {
              this._map.set(key, value);
              return this;
            }
            var properties = Object.keys(key).sort();
            var valuePair = [key, value];
            var map = Array.isArray(key) ? this._arrayTreeMap : this._objectTreeMap;
            for (var i = 0; i < properties.length; i++) {
              var property = properties[i];
              if (!map.has(property)) {
                map.set(property, new EquivalentKeyMap4());
              }
              map = map.get(property);
              var propertyValue = key[property];
              if (!map.has(propertyValue)) {
                map.set(propertyValue, new EquivalentKeyMap4());
              }
              map = map.get(propertyValue);
            }
            var previousValuePair = map.get("_ekm_value");
            if (previousValuePair) {
              this._map.delete(previousValuePair[0]);
            }
            map.set("_ekm_value", valuePair);
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
            if (key === null || _typeof(key) !== "object") {
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
            if (key === null || _typeof(key) !== "object") {
              return this._map.has(key);
            }
            return getValuePair(this, key) !== void 0;
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
            }
            this.set(key, void 0);
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
            var thisArg = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this;
            this._map.forEach(function(value, key) {
              if (key !== null && _typeof(key) === "object") {
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
            this._map = /* @__PURE__ */ new Map();
            this._arrayTreeMap = /* @__PURE__ */ new Map();
            this._objectTreeMap = /* @__PURE__ */ new Map();
          }
        }, {
          key: "size",
          get: function get() {
            return this._map.size;
          }
        }]);
        return EquivalentKeyMap4;
      })();
      module.exports = EquivalentKeyMap3;
    }
  });

  // package-external:@wordpress/redux-routine
  var require_redux_routine = __commonJS({
    "package-external:@wordpress/redux-routine"(exports, module) {
      module.exports = window.wp.reduxRoutine;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // node_modules/deepmerge/dist/cjs.js
  var require_cjs = __commonJS({
    "node_modules/deepmerge/dist/cjs.js"(exports, module) {
      "use strict";
      var isMergeableObject = function isMergeableObject2(value) {
        return isNonNullObject(value) && !isSpecial(value);
      };
      function isNonNullObject(value) {
        return !!value && typeof value === "object";
      }
      function isSpecial(value) {
        var stringValue = Object.prototype.toString.call(value);
        return stringValue === "[object RegExp]" || stringValue === "[object Date]" || isReactElement(value);
      }
      var canUseSymbol = typeof Symbol === "function" && Symbol.for;
      var REACT_ELEMENT_TYPE = canUseSymbol ? Symbol.for("react.element") : 60103;
      function isReactElement(value) {
        return value.$$typeof === REACT_ELEMENT_TYPE;
      }
      function emptyTarget(val) {
        return Array.isArray(val) ? [] : {};
      }
      function cloneUnlessOtherwiseSpecified(value, options) {
        return options.clone !== false && options.isMergeableObject(value) ? deepmerge2(emptyTarget(value), value, options) : value;
      }
      function defaultArrayMerge(target, source, options) {
        return target.concat(source).map(function(element) {
          return cloneUnlessOtherwiseSpecified(element, options);
        });
      }
      function getMergeFunction(key, options) {
        if (!options.customMerge) {
          return deepmerge2;
        }
        var customMerge = options.customMerge(key);
        return typeof customMerge === "function" ? customMerge : deepmerge2;
      }
      function getEnumerableOwnPropertySymbols(target) {
        return Object.getOwnPropertySymbols ? Object.getOwnPropertySymbols(target).filter(function(symbol) {
          return Object.propertyIsEnumerable.call(target, symbol);
        }) : [];
      }
      function getKeys(target) {
        return Object.keys(target).concat(getEnumerableOwnPropertySymbols(target));
      }
      function propertyIsOnObject(object, property) {
        try {
          return property in object;
        } catch (_) {
          return false;
        }
      }
      function propertyIsUnsafe(target, key) {
        return propertyIsOnObject(target, key) && !(Object.hasOwnProperty.call(target, key) && Object.propertyIsEnumerable.call(target, key));
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
            return;
          }
          if (propertyIsOnObject(target, key) && options.isMergeableObject(source[key])) {
            destination[key] = getMergeFunction(key, options)(target[key], source[key], options);
          } else {
            destination[key] = cloneUnlessOtherwiseSpecified(source[key], options);
          }
        });
        return destination;
      }
      function deepmerge2(target, source, options) {
        options = options || {};
        options.arrayMerge = options.arrayMerge || defaultArrayMerge;
        options.isMergeableObject = options.isMergeableObject || isMergeableObject;
        options.cloneUnlessOtherwiseSpecified = cloneUnlessOtherwiseSpecified;
        var sourceIsArray = Array.isArray(source);
        var targetIsArray = Array.isArray(target);
        var sourceAndTargetTypesMatch = sourceIsArray === targetIsArray;
        if (!sourceAndTargetTypesMatch) {
          return cloneUnlessOtherwiseSpecified(source, options);
        } else if (sourceIsArray) {
          return options.arrayMerge(target, source, options);
        } else {
          return mergeObject(target, source, options);
        }
      }
      deepmerge2.all = function deepmergeAll(array, options) {
        if (!Array.isArray(array)) {
          throw new Error("first argument should be an array");
        }
        return array.reduce(function(prev, next) {
          return deepmerge2(prev, next, options);
        }, {});
      };
      var deepmerge_1 = deepmerge2;
      module.exports = deepmerge_1;
    }
  });

  // package-external:@wordpress/priority-queue
  var require_priority_queue = __commonJS({
    "package-external:@wordpress/priority-queue"(exports, module) {
      module.exports = window.wp.priorityQueue;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/is-shallow-equal
  var require_is_shallow_equal = __commonJS({
    "package-external:@wordpress/is-shallow-equal"(exports, module) {
      module.exports = window.wp.isShallowEqual;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // packages/data/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    AsyncModeProvider: () => context_default2,
    RegistryConsumer: () => RegistryConsumer,
    RegistryProvider: () => context_default,
    combineReducers: () => combineReducers2,
    controls: () => controls,
    createReduxStore: () => createReduxStore,
    createRegistry: () => createRegistry,
    createRegistryControl: () => createRegistryControl,
    createRegistrySelector: () => createRegistrySelector,
    createSelector: () => rememo_default,
    dispatch: () => dispatch2,
    plugins: () => plugins_exports,
    register: () => register,
    registerGenericStore: () => registerGenericStore,
    registerStore: () => registerStore,
    resolveSelect: () => resolveSelect2,
    select: () => select2,
    subscribe: () => subscribe,
    suspendSelect: () => suspendSelect,
    use: () => use,
    useDispatch: () => use_dispatch_default,
    useRegistry: () => useRegistry,
    useSelect: () => useSelect,
    useSuspenseSelect: () => useSuspenseSelect,
    withDispatch: () => with_dispatch_default,
    withRegistry: () => with_registry_default,
    withSelect: () => with_select_default
  });

  // packages/data/build-module/registry.js
  var import_deprecated2 = __toESM(require_deprecated());

  // node_modules/redux/dist/redux.mjs
  var $$observable = /* @__PURE__ */ (() => typeof Symbol === "function" && Symbol.observable || "@@observable")();
  var symbol_observable_default = $$observable;
  var randomString = () => Math.random().toString(36).substring(7).split("").join(".");
  var ActionTypes = {
    INIT: `@@redux/INIT${/* @__PURE__ */ randomString()}`,
    REPLACE: `@@redux/REPLACE${/* @__PURE__ */ randomString()}`,
    PROBE_UNKNOWN_ACTION: () => `@@redux/PROBE_UNKNOWN_ACTION${randomString()}`
  };
  var actionTypes_default = ActionTypes;
  function isPlainObject(obj) {
    if (typeof obj !== "object" || obj === null)
      return false;
    let proto = obj;
    while (Object.getPrototypeOf(proto) !== null) {
      proto = Object.getPrototypeOf(proto);
    }
    return Object.getPrototypeOf(obj) === proto || Object.getPrototypeOf(obj) === null;
  }
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
    if (true) {
      typeOfVal = miniKindOf(val);
    }
    return typeOfVal;
  }
  function createStore(reducer, preloadedState, enhancer) {
    if (typeof reducer !== "function") {
      throw new Error(false ? formatProdErrorMessage(2) : `Expected the root reducer to be a function. Instead, received: '${kindOf(reducer)}'`);
    }
    if (typeof preloadedState === "function" && typeof enhancer === "function" || typeof enhancer === "function" && typeof arguments[3] === "function") {
      throw new Error(false ? formatProdErrorMessage(0) : "It looks like you are passing several store enhancers to createStore(). This is not supported. Instead, compose them together to a single function. See https://redux.js.org/tutorials/fundamentals/part-4-store#creating-a-store-with-enhancers for an example.");
    }
    if (typeof preloadedState === "function" && typeof enhancer === "undefined") {
      enhancer = preloadedState;
      preloadedState = void 0;
    }
    if (typeof enhancer !== "undefined") {
      if (typeof enhancer !== "function") {
        throw new Error(false ? formatProdErrorMessage(1) : `Expected the enhancer to be a function. Instead, received: '${kindOf(enhancer)}'`);
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
        throw new Error(false ? formatProdErrorMessage(3) : "You may not call store.getState() while the reducer is executing. The reducer has already received the state as an argument. Pass it down from the top reducer instead of reading it from the store.");
      }
      return currentState;
    }
    function subscribe2(listener) {
      if (typeof listener !== "function") {
        throw new Error(false ? formatProdErrorMessage(4) : `Expected the listener to be a function. Instead, received: '${kindOf(listener)}'`);
      }
      if (isDispatching) {
        throw new Error(false ? formatProdErrorMessage(5) : "You may not call store.subscribe() while the reducer is executing. If you would like to be notified after the store has been updated, subscribe from a component and invoke store.getState() in the callback to access the latest state. See https://redux.js.org/api/store#subscribelistener for more details.");
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
          throw new Error(false ? formatProdErrorMessage(6) : "You may not unsubscribe from a store listener while the reducer is executing. See https://redux.js.org/api/store#subscribelistener for more details.");
        }
        isSubscribed = false;
        ensureCanMutateNextListeners();
        nextListeners.delete(listenerId);
        currentListeners = null;
      };
    }
    function dispatch3(action) {
      if (!isPlainObject(action)) {
        throw new Error(false ? formatProdErrorMessage(7) : `Actions must be plain objects. Instead, the actual type was: '${kindOf(action)}'. You may need to add middleware to your store setup to handle dispatching other values, such as 'redux-thunk' to handle dispatching functions. See https://redux.js.org/tutorials/fundamentals/part-4-store#middleware and https://redux.js.org/tutorials/fundamentals/part-6-async-logic#using-the-redux-thunk-middleware for examples.`);
      }
      if (typeof action.type === "undefined") {
        throw new Error(false ? formatProdErrorMessage(8) : 'Actions may not have an undefined "type" property. You may have misspelled an action type string constant.');
      }
      if (typeof action.type !== "string") {
        throw new Error(false ? formatProdErrorMessage(17) : `Action "type" property must be a string. Instead, the actual type was: '${kindOf(action.type)}'. Value was: '${action.type}' (stringified)`);
      }
      if (isDispatching) {
        throw new Error(false ? formatProdErrorMessage(9) : "Reducers may not dispatch actions.");
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
        throw new Error(false ? formatProdErrorMessage(10) : `Expected the nextReducer to be a function. Instead, received: '${kindOf(nextReducer)}`);
      }
      currentReducer = nextReducer;
      dispatch3({
        type: actionTypes_default.REPLACE
      });
    }
    function observable() {
      const outerSubscribe = subscribe2;
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
            throw new Error(false ? formatProdErrorMessage(11) : `Expected the observer to be an object. Instead, received: '${kindOf(observer)}'`);
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
    dispatch3({
      type: actionTypes_default.INIT
    });
    const store = {
      dispatch: dispatch3,
      subscribe: subscribe2,
      getState,
      replaceReducer,
      [symbol_observable_default]: observable
    };
    return store;
  }
  function compose(...funcs) {
    if (funcs.length === 0) {
      return (arg) => arg;
    }
    if (funcs.length === 1) {
      return funcs[0];
    }
    return funcs.reduce((a, b) => (...args) => a(b(...args)));
  }
  function applyMiddleware(...middlewares) {
    return (createStore2) => (reducer, preloadedState) => {
      const store = createStore2(reducer, preloadedState);
      let dispatch3 = () => {
        throw new Error(false ? formatProdErrorMessage(15) : "Dispatching while constructing your middleware is not allowed. Other middleware would not be applied to this dispatch.");
      };
      const middlewareAPI = {
        getState: store.getState,
        dispatch: (action, ...args) => dispatch3(action, ...args)
      };
      const chain = middlewares.map((middleware) => middleware(middlewareAPI));
      dispatch3 = compose(...chain)(store.dispatch);
      return {
        ...store,
        dispatch: dispatch3
      };
    };
  }

  // packages/data/build-module/redux-store/index.js
  var import_equivalent_key_map2 = __toESM(require_equivalent_key_map());
  var import_redux_routine = __toESM(require_redux_routine());
  var import_compose = __toESM(require_compose());

  // packages/data/build-module/redux-store/combine-reducers.js
  function combineReducers(reducers) {
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

  // packages/data/build-module/factory.js
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

  // packages/data/build-module/controls.js
  var SELECT = "@@data/SELECT";
  var RESOLVE_SELECT = "@@data/RESOLVE_SELECT";
  var DISPATCH = "@@data/DISPATCH";
  function isObject(object) {
    return object !== null && typeof object === "object";
  }
  function select(storeNameOrDescriptor, selectorName, ...args) {
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
  var controls = { select, resolveSelect, dispatch };
  var builtinControls = {
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

  // packages/data/build-module/lock-unlock.js
  var import_private_apis = __toESM(require_private_apis());
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/data"
  );

  // node_modules/is-promise/index.mjs
  function isPromise(obj) {
    return !!obj && (typeof obj === "object" || typeof obj === "function") && typeof obj.then === "function";
  }

  // packages/data/build-module/promise-middleware.js
  var promiseMiddleware = () => (next) => (action) => {
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

  // packages/data/build-module/resolvers-cache-middleware.js
  var createResolversCacheMiddleware = (registry, storeName) => () => (next) => (action) => {
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

  // packages/data/build-module/redux-store/thunk-middleware.js
  function createThunkMiddleware(args) {
    return () => (next) => (action) => {
      if (typeof action === "function") {
        return action(args);
      }
      return next(action);
    };
  }

  // packages/data/build-module/redux-store/metadata/reducer.js
  var import_equivalent_key_map = __toESM(require_equivalent_key_map());

  // packages/data/build-module/redux-store/metadata/utils.js
  var onSubKey = (actionProperty) => (reducer) => (state = {}, action) => {
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

  // packages/data/build-module/redux-store/metadata/reducer.js
  var subKeysIsResolved = onSubKey("selectorName")((state = new import_equivalent_key_map.default(), action) => {
    switch (action.type) {
      case "START_RESOLUTION": {
        const nextState = new import_equivalent_key_map.default(state);
        nextState.set(selectorArgsToStateKey(action.args), {
          status: "resolving"
        });
        return nextState;
      }
      case "FINISH_RESOLUTION": {
        const nextState = new import_equivalent_key_map.default(state);
        nextState.set(selectorArgsToStateKey(action.args), {
          status: "finished"
        });
        return nextState;
      }
      case "FAIL_RESOLUTION": {
        const nextState = new import_equivalent_key_map.default(state);
        nextState.set(selectorArgsToStateKey(action.args), {
          status: "error",
          error: action.error
        });
        return nextState;
      }
      case "START_RESOLUTIONS": {
        const nextState = new import_equivalent_key_map.default(state);
        for (const resolutionArgs of action.args) {
          nextState.set(selectorArgsToStateKey(resolutionArgs), {
            status: "resolving"
          });
        }
        return nextState;
      }
      case "FINISH_RESOLUTIONS": {
        const nextState = new import_equivalent_key_map.default(state);
        for (const resolutionArgs of action.args) {
          nextState.set(selectorArgsToStateKey(resolutionArgs), {
            status: "finished"
          });
        }
        return nextState;
      }
      case "FAIL_RESOLUTIONS": {
        const nextState = new import_equivalent_key_map.default(state);
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
        const nextState = new import_equivalent_key_map.default(state);
        nextState.delete(selectorArgsToStateKey(action.args));
        return nextState;
      }
    }
    return state;
  });
  var isResolved = (state = {}, action) => {
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

  // packages/data/build-module/redux-store/metadata/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    countSelectorsByStatus: () => countSelectorsByStatus,
    getCachedResolvers: () => getCachedResolvers,
    getIsResolving: () => getIsResolving,
    getResolutionError: () => getResolutionError,
    getResolutionState: () => getResolutionState,
    hasFinishedResolution: () => hasFinishedResolution,
    hasResolutionFailed: () => hasResolutionFailed,
    hasResolvingSelectors: () => hasResolvingSelectors,
    hasStartedResolution: () => hasStartedResolution,
    isResolving: () => isResolving
  });
  var import_deprecated = __toESM(require_deprecated());

  // node_modules/rememo/rememo.js
  var LEAF_KEY = {};
  function arrayOf(value) {
    return [value];
  }
  function isObjectLike(value) {
    return !!value && "object" === typeof value;
  }
  function createCache() {
    var cache = {
      clear: function() {
        cache.head = null;
      }
    };
    return cache;
  }
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
  function rememo_default(selector, getDependants) {
    var rootCache;
    var normalizedGetDependants = getDependants ? getDependants : arrayOf;
    function getCache(dependants) {
      var caches = rootCache, isUniqueByDependants = true, i, dependant, map, cache;
      for (i = 0; i < dependants.length; i++) {
        dependant = dependants[i];
        if (!isObjectLike(dependant)) {
          isUniqueByDependants = false;
          break;
        }
        if (caches.has(dependant)) {
          caches = caches.get(dependant);
        } else {
          map = /* @__PURE__ */ new WeakMap();
          caches.set(dependant, map);
          caches = map;
        }
      }
      if (!caches.has(LEAF_KEY)) {
        cache = createCache();
        cache.isUniqueByDependants = isUniqueByDependants;
        caches.set(LEAF_KEY, cache);
      }
      return caches.get(LEAF_KEY);
    }
    function clear() {
      rootCache = /* @__PURE__ */ new WeakMap();
    }
    function callSelector() {
      var len = arguments.length, cache, node, i, args, dependants;
      args = new Array(len);
      for (i = 0; i < len; i++) {
        args[i] = arguments[i];
      }
      dependants = normalizedGetDependants.apply(null, args);
      cache = getCache(dependants);
      if (!cache.isUniqueByDependants) {
        if (cache.lastDependants && !isShallowEqual(dependants, cache.lastDependants, 0)) {
          cache.clear();
        }
        cache.lastDependants = dependants;
      }
      node = cache.head;
      while (node) {
        if (!isShallowEqual(node.args, args, 1)) {
          node = node.next;
          continue;
        }
        if (node !== cache.head) {
          node.prev.next = node.next;
          if (node.next) {
            node.next.prev = node.prev;
          }
          node.next = cache.head;
          node.prev = null;
          cache.head.prev = node;
          cache.head = node;
        }
        return node.val;
      }
      node = /** @type {CacheNode} */
      {
        // Generate the result from original function
        val: selector.apply(null, args)
      };
      args[0] = null;
      node.args = args;
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
    return (
      /** @type {S & EnhancedSelector} */
      callSelector
    );
  }

  // packages/data/build-module/redux-store/metadata/selectors.js
  function getResolutionState(state, selectorName, args) {
    const map = state[selectorName];
    if (!map) {
      return;
    }
    return map.get(selectorArgsToStateKey(args));
  }
  function getIsResolving(state, selectorName, args) {
    (0, import_deprecated.default)("wp.data.select( store ).getIsResolving", {
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
  var countSelectorsByStatus = rememo_default(
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

  // packages/data/build-module/redux-store/metadata/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    failResolution: () => failResolution,
    failResolutions: () => failResolutions,
    finishResolution: () => finishResolution,
    finishResolutions: () => finishResolutions,
    invalidateResolution: () => invalidateResolution,
    invalidateResolutionForStore: () => invalidateResolutionForStore,
    invalidateResolutionForStoreSelector: () => invalidateResolutionForStoreSelector,
    startResolution: () => startResolution,
    startResolutions: () => startResolutions
  });
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

  // packages/data/build-module/redux-store/index.js
  var trimUndefinedValues = (array) => {
    const result = [...array];
    for (let i = result.length - 1; i >= 0; i--) {
      if (result[i] === void 0) {
        result.splice(i, 1);
      }
    }
    return result;
  };
  var mapValues = (obj, callback) => Object.fromEntries(
    Object.entries(obj ?? {}).map(([key, value]) => [
      key,
      callback(value, key)
    ])
  );
  var devToolsReplacer = (key, state) => {
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
          cache[selectorName] = new import_equivalent_key_map2.default();
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
          ...mapValues(actions_exports, bindAction),
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
          selectors_exports,
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
        const subscribe2 = store && ((listener) => {
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
          subscribe: subscribe2
        };
      }
    };
    lock(storeDescriptor, privateRegistrationFunctions);
    return storeDescriptor;
  }
  function instantiateReduxStore(key, options, registry, thunkArgs) {
    const controls2 = {
      ...options.controls,
      ...builtinControls
    };
    const normalizedControls = mapValues(
      controls2,
      (control) => control.isRegistryControl ? control(registry) : control
    );
    const middlewares = [
      resolvers_cache_middleware_default(registry, key),
      promise_middleware_default,
      (0, import_redux_routine.default)(normalizedControls),
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
    const enhancedReducer = combineReducers({
      metadata: reducer_default,
      root: reducer
    });
    return createStore(
      enhancedReducer,
      { root: initialState },
      (0, import_compose.compose)(enhancers)
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

  // packages/data/build-module/store/index.js
  var coreDataStore = {
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

  // packages/data/build-module/utils/emitter.js
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

  // packages/data/build-module/registry.js
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
    const subscribe2 = (listener, storeNameOrDescriptor) => {
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
    function select3(storeNameOrDescriptor) {
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
    function resolveSelect3(storeNameOrDescriptor) {
      const storeName = getStoreName(storeNameOrDescriptor);
      listeningStores?.add(storeName);
      const store = stores[storeName];
      if (store) {
        return store.getResolveSelectors();
      }
      return parent && parent.resolveSelect(storeName);
    }
    function suspendSelect2(storeNameOrDescriptor) {
      const storeName = getStoreName(storeNameOrDescriptor);
      listeningStores?.add(storeName);
      const store = stores[storeName];
      if (store) {
        return store.getSuspendSelectors();
      }
      return parent && parent.suspendSelect(storeName);
    }
    function dispatch3(storeNameOrDescriptor) {
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
    function registerStoreInstance(name, createStore2) {
      if (stores[name]) {
        console.error('Store "' + name + '" is already registered.');
        return stores[name];
      }
      const store = createStore2();
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
    function register2(store) {
      registerStoreInstance(
        store.name,
        () => store.instantiate(registry)
      );
    }
    function registerGenericStore2(name, store) {
      (0, import_deprecated2.default)("wp.data.registerGenericStore", {
        since: "5.9",
        alternative: "wp.data.register( storeDescriptor )"
      });
      registerStoreInstance(name, () => store);
    }
    function registerStore2(storeName, options) {
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
      subscribe: subscribe2,
      select: select3,
      resolveSelect: resolveSelect3,
      suspendSelect: suspendSelect2,
      dispatch: dispatch3,
      use: use2,
      register: register2,
      registerGenericStore: registerGenericStore2,
      registerStore: registerStore2,
      __unstableMarkListeningStores
    };
    function use2(plugin, options) {
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

  // packages/data/build-module/default-registry.js
  var default_registry_default = createRegistry();

  // packages/data/build-module/plugins/index.js
  var plugins_exports = {};
  __export(plugins_exports, {
    persistence: () => persistence_default
  });

  // node_modules/is-plain-object/dist/is-plain-object.mjs
  function isObject2(o) {
    return Object.prototype.toString.call(o) === "[object Object]";
  }
  function isPlainObject2(o) {
    var ctor, prot;
    if (isObject2(o) === false) return false;
    ctor = o.constructor;
    if (ctor === void 0) return true;
    prot = ctor.prototype;
    if (isObject2(prot) === false) return false;
    if (prot.hasOwnProperty("isPrototypeOf") === false) {
      return false;
    }
    return true;
  }

  // packages/data/build-module/plugins/persistence/index.js
  var import_deepmerge = __toESM(require_cjs());

  // packages/data/build-module/plugins/persistence/storage/object.js
  var objectStorage;
  var storage = {
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

  // packages/data/build-module/plugins/persistence/storage/default.js
  var storage2;
  try {
    storage2 = window.localStorage;
    storage2.setItem("__wpDataTestLocalStorage", "");
    storage2.removeItem("__wpDataTestLocalStorage");
  } catch (error) {
    storage2 = object_default;
  }
  var default_default = storage2;

  // packages/data/build-module/plugins/persistence/index.js
  var DEFAULT_STORAGE = default_default;
  var DEFAULT_STORAGE_KEY = "WP_DATA";
  var withLazySameState = (reducer) => (state, action) => {
    if (action.nextState === state) {
      return state;
    }
    return reducer(state, action);
  };
  function createPersistenceInterface(options) {
    const { storage: storage3 = DEFAULT_STORAGE, storageKey = DEFAULT_STORAGE_KEY } = options;
    let data;
    function getData() {
      if (data === void 0) {
        const persisted = storage3.getItem(storageKey);
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
      storage3.setItem(storageKey, JSON.stringify(data));
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
          combineReducers2(reducers)
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
          if (isPlainObject2(initialState) && isPlainObject2(persistedState)) {
            initialState = (0, import_deepmerge.default)(initialState, persistedState, {
              isMergeableObject: isPlainObject2
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

  // packages/data/build-module/components/with-select/index.js
  var import_compose2 = __toESM(require_compose());

  // packages/data/build-module/components/use-select/index.js
  var import_priority_queue = __toESM(require_priority_queue());
  var import_element5 = __toESM(require_element());
  var import_is_shallow_equal = __toESM(require_is_shallow_equal());

  // packages/data/build-module/components/registry-provider/use-registry.js
  var import_element2 = __toESM(require_element());

  // packages/data/build-module/components/registry-provider/context.js
  var import_element = __toESM(require_element());
  var Context = (0, import_element.createContext)(default_registry_default);
  Context.displayName = "RegistryProviderContext";
  var { Consumer, Provider } = Context;
  var RegistryConsumer = Consumer;
  var context_default = Provider;

  // packages/data/build-module/components/registry-provider/use-registry.js
  function useRegistry() {
    return (0, import_element2.useContext)(Context);
  }

  // packages/data/build-module/components/async-mode-provider/use-async-mode.js
  var import_element4 = __toESM(require_element());

  // packages/data/build-module/components/async-mode-provider/context.js
  var import_element3 = __toESM(require_element());
  var Context2 = (0, import_element3.createContext)(false);
  Context2.displayName = "AsyncModeContext";
  var { Consumer: Consumer2, Provider: Provider2 } = Context2;
  var context_default2 = Provider2;

  // packages/data/build-module/components/async-mode-provider/use-async-mode.js
  function useAsyncMode() {
    return (0, import_element4.useContext)(Context2);
  }

  // packages/data/build-module/components/use-select/index.js
  var renderQueue = (0, import_priority_queue.createQueue)();
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
    const select3 = suspense ? registry.suspendSelect : registry.select;
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
      function subscribe2(listener) {
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
      return { subscribe: subscribe2, updateStores };
    };
    return (mapSelect, isAsync) => {
      function updateValue() {
        if (lastMapResultValid && mapSelect === lastMapSelect) {
          return lastMapResult;
        }
        const listeningStores = { current: null };
        const mapResult = registry.__unstableMarkListeningStores(
          () => mapSelect(select3, registry),
          listeningStores
        );
        if (true) {
          if (!didWarnUnstableReference) {
            const secondMapResult = mapSelect(select3, registry);
            if (!(0, import_is_shallow_equal.default)(mapResult, secondMapResult)) {
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
        if (!(0, import_is_shallow_equal.default)(lastMapResult, mapResult)) {
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
    const store = (0, import_element5.useMemo)(
      () => Store(registry, suspense),
      [registry, suspense]
    );
    const selector = (0, import_element5.useCallback)(mapSelect, deps);
    const { subscribe: subscribe2, getValue } = store(selector, isAsync);
    const result = (0, import_element5.useSyncExternalStore)(subscribe2, getValue, getValue);
    (0, import_element5.useDebugValue)(result);
    return result;
  }
  function useSelect(mapSelect, deps) {
    const staticSelectMode = typeof mapSelect !== "function";
    const staticSelectModeRef = (0, import_element5.useRef)(staticSelectMode);
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

  // packages/data/build-module/components/with-select/index.js
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var withSelect = (mapSelectToProps) => (0, import_compose2.createHigherOrderComponent)(
    (WrappedComponent) => (0, import_compose2.pure)((ownProps) => {
      const mapSelect = (select3, registry) => mapSelectToProps(select3, ownProps, registry);
      const mergeProps = useSelect(mapSelect);
      return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(WrappedComponent, { ...ownProps, ...mergeProps });
    }),
    "withSelect"
  );
  var with_select_default = withSelect;

  // packages/data/build-module/components/with-dispatch/index.js
  var import_compose4 = __toESM(require_compose());

  // packages/data/build-module/components/use-dispatch/use-dispatch.js
  var useDispatch = (storeNameOrDescriptor) => {
    const { dispatch: dispatch3 } = useRegistry();
    return storeNameOrDescriptor === void 0 ? dispatch3 : dispatch3(storeNameOrDescriptor);
  };
  var use_dispatch_default = useDispatch;

  // packages/data/build-module/components/use-dispatch/use-dispatch-with-map.js
  var import_element6 = __toESM(require_element());
  var import_compose3 = __toESM(require_compose());
  var useDispatchWithMap = (dispatchMap, deps) => {
    const registry = useRegistry();
    const currentDispatchMapRef = (0, import_element6.useRef)(dispatchMap);
    (0, import_compose3.useIsomorphicLayoutEffect)(() => {
      currentDispatchMapRef.current = dispatchMap;
    });
    return (0, import_element6.useMemo)(() => {
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

  // packages/data/build-module/components/with-dispatch/index.js
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  var withDispatch = (mapDispatchToProps) => (0, import_compose4.createHigherOrderComponent)(
    (WrappedComponent) => (ownProps) => {
      const mapDispatch = (dispatch3, registry) => mapDispatchToProps(dispatch3, ownProps, registry);
      const dispatchProps = use_dispatch_with_map_default(mapDispatch, []);
      return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(WrappedComponent, { ...ownProps, ...dispatchProps });
    },
    "withDispatch"
  );
  var with_dispatch_default = withDispatch;

  // packages/data/build-module/components/with-registry/index.js
  var import_compose5 = __toESM(require_compose());
  var import_jsx_runtime3 = __toESM(require_jsx_runtime());
  var withRegistry = (0, import_compose5.createHigherOrderComponent)(
    (OriginalComponent) => (props) => /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(RegistryConsumer, { children: (registry) => /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(OriginalComponent, { ...props, registry }) }),
    "withRegistry"
  );
  var with_registry_default = withRegistry;

  // packages/data/build-module/dispatch.js
  function dispatch2(storeNameOrDescriptor) {
    return default_registry_default.dispatch(storeNameOrDescriptor);
  }

  // packages/data/build-module/select.js
  function select2(storeNameOrDescriptor) {
    return default_registry_default.select(storeNameOrDescriptor);
  }

  // packages/data/build-module/index.js
  var combineReducers2 = combineReducers;
  var resolveSelect2 = default_registry_default.resolveSelect;
  var suspendSelect = default_registry_default.suspendSelect;
  var subscribe = default_registry_default.subscribe;
  var registerGenericStore = default_registry_default.registerGenericStore;
  var registerStore = default_registry_default.registerStore;
  var use = default_registry_default.use;
  var register = default_registry_default.register;
  return __toCommonJS(index_exports);
})();
/*! Bundled license information:

is-plain-object/dist/is-plain-object.mjs:
  (*!
   * is-plain-object <https://github.com/jonschlinkert/is-plain-object>
   *
   * Copyright (c) 2014-2017, Jon Schlinkert.
   * Released under the MIT License.
   *)
*/
//# sourceMappingURL=index.js.map
