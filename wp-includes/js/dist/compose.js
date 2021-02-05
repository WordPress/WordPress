this["wp"] = this["wp"] || {}; this["wp"]["compose"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = 314);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["element"]; }());

/***/ }),

/***/ 10:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _classCallCheck; });
function _classCallCheck(instance, Constructor) {
  if (!(instance instanceof Constructor)) {
    throw new TypeError("Cannot call a class as a function");
  }
}

/***/ }),

/***/ 12:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _possibleConstructorReturn; });
/* harmony import */ var _helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(28);
/* harmony import */ var _assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(3);


function _possibleConstructorReturn(self, call) {
  if (call && (Object(_helpers_esm_typeof__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(call) === "object" || typeof call === "function")) {
    return call;
  }

  return Object(_assertThisInitialized__WEBPACK_IMPORTED_MODULE_1__[/* default */ "a"])(self);
}

/***/ }),

/***/ 13:
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

/***/ 14:
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

/***/ 18:
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

/***/ }),

/***/ 2:
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ 28:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _typeof; });
function _typeof2(obj) { if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof2 = function _typeof2(obj) { return typeof obj; }; } else { _typeof2 = function _typeof2(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof2(obj); }

function _typeof(obj) {
  if (typeof Symbol === "function" && _typeof2(Symbol.iterator) === "symbol") {
    _typeof = function _typeof(obj) {
      return _typeof2(obj);
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : _typeof2(obj);
    };
  }

  return _typeof(obj);
}

/***/ }),

/***/ 3:
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

/***/ 314:
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "createHigherOrderComponent", function() { return /* reexport */ create_higher_order_component; });
__webpack_require__.d(__webpack_exports__, "ifCondition", function() { return /* reexport */ if_condition; });
__webpack_require__.d(__webpack_exports__, "pure", function() { return /* reexport */ build_module_pure; });
__webpack_require__.d(__webpack_exports__, "withGlobalEvents", function() { return /* reexport */ with_global_events; });
__webpack_require__.d(__webpack_exports__, "withInstanceId", function() { return /* reexport */ with_instance_id; });
__webpack_require__.d(__webpack_exports__, "withSafeTimeout", function() { return /* reexport */ with_safe_timeout; });
__webpack_require__.d(__webpack_exports__, "withState", function() { return /* reexport */ withState; });
__webpack_require__.d(__webpack_exports__, "compose", function() { return /* reexport */ external_lodash_["flowRight"]; });

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__(2);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/create-higher-order-component/index.js
/**
 * External dependencies
 */

/**
 * Given a function mapping a component to an enhanced component and modifier
 * name, returns the enhanced component augmented with a generated displayName.
 *
 * @param {Function} mapComponentToEnhancedComponent Function mapping component
 *                                                   to enhanced component.
 * @param {string}   modifierName                    Seed name from which to
 *                                                   generated display name.
 *
 * @return {WPComponent} Component class with generated display name assigned.
 */

function createHigherOrderComponent(mapComponentToEnhancedComponent, modifierName) {
  return function (OriginalComponent) {
    var EnhancedComponent = mapComponentToEnhancedComponent(OriginalComponent);
    var _OriginalComponent$di = OriginalComponent.displayName,
        displayName = _OriginalComponent$di === void 0 ? OriginalComponent.name || 'Component' : _OriginalComponent$di;
    EnhancedComponent.displayName = "".concat(Object(external_lodash_["upperFirst"])(Object(external_lodash_["camelCase"])(modifierName)), "(").concat(displayName, ")");
    return EnhancedComponent;
  };
}

/* harmony default export */ var create_higher_order_component = (createHigherOrderComponent);

// EXTERNAL MODULE: external {"this":["wp","element"]}
var external_this_wp_element_ = __webpack_require__(0);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/if-condition/index.js


/**
 * Internal dependencies
 */

/**
 * Higher-order component creator, creating a new component which renders if
 * the given condition is satisfied or with the given optional prop name.
 *
 * @param {Function} predicate Function to test condition.
 *
 * @return {Function} Higher-order component.
 */

var if_condition_ifCondition = function ifCondition(predicate) {
  return create_higher_order_component(function (WrappedComponent) {
    return function (props) {
      if (!predicate(props)) {
        return null;
      }

      return Object(external_this_wp_element_["createElement"])(WrappedComponent, props);
    };
  }, 'ifCondition');
};

/* harmony default export */ var if_condition = (if_condition_ifCondition);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/classCallCheck.js
var classCallCheck = __webpack_require__(10);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/createClass.js
var createClass = __webpack_require__(9);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/possibleConstructorReturn.js
var possibleConstructorReturn = __webpack_require__(12);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/getPrototypeOf.js
var getPrototypeOf = __webpack_require__(13);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/inherits.js + 1 modules
var inherits = __webpack_require__(14);

// EXTERNAL MODULE: external {"this":["wp","isShallowEqual"]}
var external_this_wp_isShallowEqual_ = __webpack_require__(40);
var external_this_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_this_wp_isShallowEqual_);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/pure/index.js







/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Given a component returns the enhanced component augmented with a component
 * only rerendering when its props/state change
 *
 * @param {Function} mapComponentToEnhancedComponent Function mapping component
 *                                                   to enhanced component.
 * @param {string}   modifierName                    Seed name from which to
 *                                                   generated display name.
 *
 * @return {WPComponent} Component class with generated display name assigned.
 */

var pure = create_higher_order_component(function (Wrapped) {
  if (Wrapped.prototype instanceof external_this_wp_element_["Component"]) {
    return (
      /*#__PURE__*/
      function (_Wrapped) {
        Object(inherits["a" /* default */])(_class, _Wrapped);

        function _class() {
          Object(classCallCheck["a" /* default */])(this, _class);

          return Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(_class).apply(this, arguments));
        }

        Object(createClass["a" /* default */])(_class, [{
          key: "shouldComponentUpdate",
          value: function shouldComponentUpdate(nextProps, nextState) {
            return !external_this_wp_isShallowEqual_default()(nextProps, this.props) || !external_this_wp_isShallowEqual_default()(nextState, this.state);
          }
        }]);

        return _class;
      }(Wrapped)
    );
  }

  return (
    /*#__PURE__*/
    function (_Component) {
      Object(inherits["a" /* default */])(_class2, _Component);

      function _class2() {
        Object(classCallCheck["a" /* default */])(this, _class2);

        return Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(_class2).apply(this, arguments));
      }

      Object(createClass["a" /* default */])(_class2, [{
        key: "shouldComponentUpdate",
        value: function shouldComponentUpdate(nextProps) {
          return !external_this_wp_isShallowEqual_default()(nextProps, this.props);
        }
      }, {
        key: "render",
        value: function render() {
          return Object(external_this_wp_element_["createElement"])(Wrapped, this.props);
        }
      }]);

      return _class2;
    }(external_this_wp_element_["Component"])
  );
}, 'pure');
/* harmony default export */ var build_module_pure = (pure);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__(18);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/assertThisInitialized.js
var assertThisInitialized = __webpack_require__(3);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/with-global-events/listener.js



/**
 * External dependencies
 */

/**
 * Class responsible for orchestrating event handling on the global window,
 * binding a single event to be shared across all handling instances, and
 * removing the handler when no instances are listening for the event.
 */

var listener_Listener =
/*#__PURE__*/
function () {
  function Listener() {
    Object(classCallCheck["a" /* default */])(this, Listener);

    this.listeners = {};
    this.handleEvent = this.handleEvent.bind(this);
  }

  Object(createClass["a" /* default */])(Listener, [{
    key: "add",
    value: function add(eventType, instance) {
      if (!this.listeners[eventType]) {
        // Adding first listener for this type, so bind event.
        window.addEventListener(eventType, this.handleEvent);
        this.listeners[eventType] = [];
      }

      this.listeners[eventType].push(instance);
    }
  }, {
    key: "remove",
    value: function remove(eventType, instance) {
      this.listeners[eventType] = Object(external_lodash_["without"])(this.listeners[eventType], instance);

      if (!this.listeners[eventType].length) {
        // Removing last listener for this type, so unbind event.
        window.removeEventListener(eventType, this.handleEvent);
        delete this.listeners[eventType];
      }
    }
  }, {
    key: "handleEvent",
    value: function handleEvent(event) {
      Object(external_lodash_["forEach"])(this.listeners[event.type], function (instance) {
        instance.handleEvent(event);
      });
    }
  }]);

  return Listener;
}();

/* harmony default export */ var listener = (listener_Listener);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/with-global-events/index.js









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
 * Listener instance responsible for managing document event handling.
 *
 * @type {Listener}
 */

var with_global_events_listener = new listener();

function withGlobalEvents(eventTypesToHandlers) {
  return create_higher_order_component(function (WrappedComponent) {
    var Wrapper =
    /*#__PURE__*/
    function (_Component) {
      Object(inherits["a" /* default */])(Wrapper, _Component);

      function Wrapper() {
        var _this;

        Object(classCallCheck["a" /* default */])(this, Wrapper);

        _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(Wrapper).apply(this, arguments));
        _this.handleEvent = _this.handleEvent.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
        _this.handleRef = _this.handleRef.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
        return _this;
      }

      Object(createClass["a" /* default */])(Wrapper, [{
        key: "componentDidMount",
        value: function componentDidMount() {
          var _this2 = this;

          Object(external_lodash_["forEach"])(eventTypesToHandlers, function (handler, eventType) {
            with_global_events_listener.add(eventType, _this2);
          });
        }
      }, {
        key: "componentWillUnmount",
        value: function componentWillUnmount() {
          var _this3 = this;

          Object(external_lodash_["forEach"])(eventTypesToHandlers, function (handler, eventType) {
            with_global_events_listener.remove(eventType, _this3);
          });
        }
      }, {
        key: "handleEvent",
        value: function handleEvent(event) {
          var handler = eventTypesToHandlers[event.type];

          if (typeof this.wrappedRef[handler] === 'function') {
            this.wrappedRef[handler](event);
          }
        }
      }, {
        key: "handleRef",
        value: function handleRef(el) {
          this.wrappedRef = el; // Any component using `withGlobalEvents` that is not setting a `ref`
          // will cause `this.props.forwardedRef` to be `null`, so we need this
          // check.

          if (this.props.forwardedRef) {
            this.props.forwardedRef(el);
          }
        }
      }, {
        key: "render",
        value: function render() {
          return Object(external_this_wp_element_["createElement"])(WrappedComponent, Object(esm_extends["a" /* default */])({}, this.props.ownProps, {
            ref: this.handleRef
          }));
        }
      }]);

      return Wrapper;
    }(external_this_wp_element_["Component"]);

    return Object(external_this_wp_element_["forwardRef"])(function (props, ref) {
      return Object(external_this_wp_element_["createElement"])(Wrapper, {
        ownProps: props,
        forwardedRef: ref
      });
    });
  }, 'withGlobalEvents');
}

/* harmony default export */ var with_global_events = (withGlobalEvents);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/with-instance-id/index.js








/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * A Higher Order Component used to be provide a unique instance ID by
 * component.
 *
 * @param {WPElement} WrappedComponent The wrapped component.
 *
 * @return {Component} Component with an instanceId prop.
 */

/* harmony default export */ var with_instance_id = (create_higher_order_component(function (WrappedComponent) {
  var instances = 0;
  return (
    /*#__PURE__*/
    function (_Component) {
      Object(inherits["a" /* default */])(_class, _Component);

      function _class() {
        var _this;

        Object(classCallCheck["a" /* default */])(this, _class);

        _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(_class).apply(this, arguments));
        _this.instanceId = instances++;
        return _this;
      }

      Object(createClass["a" /* default */])(_class, [{
        key: "render",
        value: function render() {
          return Object(external_this_wp_element_["createElement"])(WrappedComponent, Object(esm_extends["a" /* default */])({}, this.props, {
            instanceId: this.instanceId
          }));
        }
      }]);

      return _class;
    }(external_this_wp_element_["Component"])
  );
}, 'withInstanceId'));

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/with-safe-timeout/index.js









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
 * A higher-order component used to provide and manage delayed function calls
 * that ought to be bound to a component's lifecycle.
 *
 * @param {Component} OriginalComponent Component requiring setTimeout
 *
 * @return {Component}                  Wrapped component.
 */

var withSafeTimeout = create_higher_order_component(function (OriginalComponent) {
  return (
    /*#__PURE__*/
    function (_Component) {
      Object(inherits["a" /* default */])(WrappedComponent, _Component);

      function WrappedComponent() {
        var _this;

        Object(classCallCheck["a" /* default */])(this, WrappedComponent);

        _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(WrappedComponent).apply(this, arguments));
        _this.timeouts = [];
        _this.setTimeout = _this.setTimeout.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
        _this.clearTimeout = _this.clearTimeout.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
        return _this;
      }

      Object(createClass["a" /* default */])(WrappedComponent, [{
        key: "componentWillUnmount",
        value: function componentWillUnmount() {
          this.timeouts.forEach(clearTimeout);
        }
      }, {
        key: "setTimeout",
        value: function (_setTimeout) {
          function setTimeout(_x, _x2) {
            return _setTimeout.apply(this, arguments);
          }

          setTimeout.toString = function () {
            return _setTimeout.toString();
          };

          return setTimeout;
        }(function (fn, delay) {
          var _this2 = this;

          var id = setTimeout(function () {
            fn();

            _this2.clearTimeout(id);
          }, delay);
          this.timeouts.push(id);
          return id;
        })
      }, {
        key: "clearTimeout",
        value: function (_clearTimeout) {
          function clearTimeout(_x3) {
            return _clearTimeout.apply(this, arguments);
          }

          clearTimeout.toString = function () {
            return _clearTimeout.toString();
          };

          return clearTimeout;
        }(function (id) {
          clearTimeout(id);
          this.timeouts = Object(external_lodash_["without"])(this.timeouts, id);
        })
      }, {
        key: "render",
        value: function render() {
          return Object(external_this_wp_element_["createElement"])(OriginalComponent, Object(esm_extends["a" /* default */])({}, this.props, {
            setTimeout: this.setTimeout,
            clearTimeout: this.clearTimeout
          }));
        }
      }]);

      return WrappedComponent;
    }(external_this_wp_element_["Component"])
  );
}, 'withSafeTimeout');
/* harmony default export */ var with_safe_timeout = (withSafeTimeout);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/with-state/index.js









/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * A Higher Order Component used to provide and manage internal component state
 * via props.
 *
 * @param {?Object} initialState Optional initial state of the component.
 *
 * @return {Component} Wrapped component.
 */

function withState() {
  var initialState = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  return create_higher_order_component(function (OriginalComponent) {
    return (
      /*#__PURE__*/
      function (_Component) {
        Object(inherits["a" /* default */])(WrappedComponent, _Component);

        function WrappedComponent() {
          var _this;

          Object(classCallCheck["a" /* default */])(this, WrappedComponent);

          _this = Object(possibleConstructorReturn["a" /* default */])(this, Object(getPrototypeOf["a" /* default */])(WrappedComponent).apply(this, arguments));
          _this.setState = _this.setState.bind(Object(assertThisInitialized["a" /* default */])(Object(assertThisInitialized["a" /* default */])(_this)));
          _this.state = initialState;
          return _this;
        }

        Object(createClass["a" /* default */])(WrappedComponent, [{
          key: "render",
          value: function render() {
            return Object(external_this_wp_element_["createElement"])(OriginalComponent, Object(esm_extends["a" /* default */])({}, this.props, this.state, {
              setState: this.setState
            }));
          }
        }]);

        return WrappedComponent;
      }(external_this_wp_element_["Component"])
    );
  }, 'withState');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/index.js
/**
 * External dependencies
 */








/**
 * Composes multiple higher-order components into a single higher-order component. Performs right-to-left function
 * composition, where each successive invocation is supplied the return value of the previous.
 *
 * @param {...Function} hocs The HOC functions to invoke.
 *
 * @return {Function} Returns the new composite function.
 */




/***/ }),

/***/ 40:
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["isShallowEqual"]; }());

/***/ }),

/***/ 9:
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

/***/ })

/******/ });