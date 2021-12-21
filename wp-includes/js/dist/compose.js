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
/******/ 	return __webpack_require__(__webpack_require__.s = "PD33");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1CF3":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["dom"]; }());

/***/ }),

/***/ "GRId":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["element"]; }());

/***/ }),

/***/ "NMb1":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["deprecated"]; }());

/***/ }),

/***/ "PD33":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "createHigherOrderComponent", function() { return /* reexport */ create_higher_order_component; });
__webpack_require__.d(__webpack_exports__, "compose", function() { return /* reexport */ compose; });
__webpack_require__.d(__webpack_exports__, "ifCondition", function() { return /* reexport */ if_condition; });
__webpack_require__.d(__webpack_exports__, "pure", function() { return /* reexport */ higher_order_pure; });
__webpack_require__.d(__webpack_exports__, "withGlobalEvents", function() { return /* reexport */ withGlobalEvents; });
__webpack_require__.d(__webpack_exports__, "withInstanceId", function() { return /* reexport */ with_instance_id; });
__webpack_require__.d(__webpack_exports__, "withSafeTimeout", function() { return /* reexport */ with_safe_timeout; });
__webpack_require__.d(__webpack_exports__, "withState", function() { return /* reexport */ withState; });
__webpack_require__.d(__webpack_exports__, "useConstrainedTabbing", function() { return /* reexport */ use_constrained_tabbing; });
__webpack_require__.d(__webpack_exports__, "useCopyOnClick", function() { return /* reexport */ useCopyOnClick; });
__webpack_require__.d(__webpack_exports__, "useCopyToClipboard", function() { return /* reexport */ useCopyToClipboard; });
__webpack_require__.d(__webpack_exports__, "__experimentalUseDialog", function() { return /* reexport */ use_dialog; });
__webpack_require__.d(__webpack_exports__, "__experimentalUseDisabled", function() { return /* reexport */ useDisabled; });
__webpack_require__.d(__webpack_exports__, "__experimentalUseDragging", function() { return /* reexport */ useDragging; });
__webpack_require__.d(__webpack_exports__, "useFocusOnMount", function() { return /* reexport */ useFocusOnMount; });
__webpack_require__.d(__webpack_exports__, "__experimentalUseFocusOutside", function() { return /* reexport */ useFocusOutside; });
__webpack_require__.d(__webpack_exports__, "useFocusReturn", function() { return /* reexport */ use_focus_return; });
__webpack_require__.d(__webpack_exports__, "useInstanceId", function() { return /* reexport */ useInstanceId; });
__webpack_require__.d(__webpack_exports__, "useIsomorphicLayoutEffect", function() { return /* reexport */ use_isomorphic_layout_effect; });
__webpack_require__.d(__webpack_exports__, "useKeyboardShortcut", function() { return /* reexport */ use_keyboard_shortcut; });
__webpack_require__.d(__webpack_exports__, "useMediaQuery", function() { return /* reexport */ useMediaQuery; });
__webpack_require__.d(__webpack_exports__, "usePrevious", function() { return /* reexport */ usePrevious; });
__webpack_require__.d(__webpack_exports__, "useReducedMotion", function() { return /* reexport */ use_reduced_motion; });
__webpack_require__.d(__webpack_exports__, "useViewportMatch", function() { return /* reexport */ use_viewport_match; });
__webpack_require__.d(__webpack_exports__, "useResizeObserver", function() { return /* reexport */ use_resize_observer; });
__webpack_require__.d(__webpack_exports__, "useAsyncList", function() { return /* reexport */ use_async_list; });
__webpack_require__.d(__webpack_exports__, "useWarnOnChange", function() { return /* reexport */ use_warn_on_change; });
__webpack_require__.d(__webpack_exports__, "useDebounce", function() { return /* reexport */ useDebounce; });
__webpack_require__.d(__webpack_exports__, "useThrottle", function() { return /* reexport */ useThrottle; });
__webpack_require__.d(__webpack_exports__, "useMergeRefs", function() { return /* reexport */ useMergeRefs; });
__webpack_require__.d(__webpack_exports__, "useRefEffect", function() { return /* reexport */ useRefEffect; });
__webpack_require__.d(__webpack_exports__, "__experimentalUseDropZone", function() { return /* reexport */ useDropZone; });
__webpack_require__.d(__webpack_exports__, "useFocusableIframe", function() { return /* reexport */ useFocusableIframe; });
__webpack_require__.d(__webpack_exports__, "__experimentalUseFixedWindowList", function() { return /* reexport */ useFixedWindowList; });

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/utils/create-higher-order-component/index.js
/**
 * External dependencies
 */
 // eslint-disable-next-line no-restricted-imports

/**
 * Given a function mapping a component to an enhanced component and modifier
 * name, returns the enhanced component augmented with a generated displayName.
 *
 * @param  mapComponentToEnhancedComponent Function mapping component to enhanced component.
 * @param  modifierName                    Seed name from which to generated display name.
 *
 * @return Component class with generated display name assigned.
 */
function createHigherOrderComponent(mapComponent, modifierName) {
  return Inner => {
    const Outer = mapComponent(Inner);
    const displayName = Inner.displayName || Inner.name || 'Component';
    Outer.displayName = `${Object(external_lodash_["upperFirst"])(Object(external_lodash_["camelCase"])(modifierName))}(${displayName})`;
    return Outer;
  };
}

/* harmony default export */ var create_higher_order_component = (createHigherOrderComponent);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/higher-order/compose.js
/**
 * External dependencies
 */

/**
 * Composes multiple higher-order components into a single higher-order component. Performs right-to-left function
 * composition, where each successive invocation is supplied the return value of the previous.
 *
 * This is just a re-export of `lodash`'s `flowRight` function.
 *
 * @see https://docs-lodash.com/v4/flow-right/
 */

/* harmony default export */ var compose = (external_lodash_["flowRight"]);

// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__("GRId");

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/higher-order/if-condition/index.js


/**
 * Internal dependencies
 */


/**
 * Higher-order component creator, creating a new component which renders if
 * the given condition is satisfied or with the given optional prop name.
 *
 * @example
 * ```ts
 * type Props = { foo: string };
 * const Component = ( props: Props ) => <div>{ props.foo }</div>;
 * const ConditionalComponent = ifCondition( ( props: Props ) => props.foo.length !== 0 )( Component );
 * <ConditionalComponent foo="" />; // => null
 * <ConditionalComponent foo="bar" />; // => <div>bar</div>;
 * ```
 *
 * @param  predicate Function to test condition.
 *
 * @return Higher-order component.
 */
const ifCondition = predicate => create_higher_order_component(WrappedComponent => props => {
  if (!predicate(props)) {
    return null;
  }

  return Object(external_wp_element_["createElement"])(WrappedComponent, props);
}, 'ifCondition');

/* harmony default export */ var if_condition = (ifCondition);

// EXTERNAL MODULE: external ["wp","isShallowEqual"]
var external_wp_isShallowEqual_ = __webpack_require__("rl8x");
var external_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_wp_isShallowEqual_);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/higher-order/pure/index.js


/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



/**
 * Given a component returns the enhanced component augmented with a component
 * only rerendering when its props/state change
 */
const pure = create_higher_order_component(Wrapped => {
  if (Wrapped.prototype instanceof external_wp_element_["Component"]) {
    return class extends Wrapped {
      shouldComponentUpdate(nextProps, nextState) {
        return !external_wp_isShallowEqual_default()(nextProps, this.props) || !external_wp_isShallowEqual_default()(nextState, this.state);
      }

    };
  }

  return class extends external_wp_element_["Component"] {
    shouldComponentUpdate(nextProps) {
      return !external_wp_isShallowEqual_default()(nextProps, this.props);
    }

    render() {
      return Object(external_wp_element_["createElement"])(Wrapped, this.props);
    }

  };
}, 'pure');
/* harmony default export */ var higher_order_pure = (pure);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/extends.js
var esm_extends = __webpack_require__("wx14");

// EXTERNAL MODULE: external ["wp","deprecated"]
var external_wp_deprecated_ = __webpack_require__("NMb1");
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/higher-order/with-global-events/listener.js
/**
 * External dependencies
 */

/**
 * Class responsible for orchestrating event handling on the global window,
 * binding a single event to be shared across all handling instances, and
 * removing the handler when no instances are listening for the event.
 */

class listener_Listener {
  constructor() {
    /** @type {any} */
    this.listeners = {};
    this.handleEvent = this.handleEvent.bind(this);
  }

  add(
  /** @type {any} */
  eventType,
  /** @type {any} */
  instance) {
    if (!this.listeners[eventType]) {
      // Adding first listener for this type, so bind event.
      window.addEventListener(eventType, this.handleEvent);
      this.listeners[eventType] = [];
    }

    this.listeners[eventType].push(instance);
  }

  remove(
  /** @type {any} */
  eventType,
  /** @type {any} */
  instance) {
    this.listeners[eventType] = Object(external_lodash_["without"])(this.listeners[eventType], instance);

    if (!this.listeners[eventType].length) {
      // Removing last listener for this type, so unbind event.
      window.removeEventListener(eventType, this.handleEvent);
      delete this.listeners[eventType];
    }
  }

  handleEvent(
  /** @type {any} */
  event) {
    Object(external_lodash_["forEach"])(this.listeners[event.type], instance => {
      instance.handleEvent(event);
    });
  }

}

/* harmony default export */ var listener = (listener_Listener);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/higher-order/with-global-events/index.js



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
 */

const with_global_events_listener = new listener();
/* eslint-disable jsdoc/no-undefined-types */

/**
 * Higher-order component creator which, given an object of DOM event types and
 * values corresponding to a callback function name on the component, will
 * create or update a window event handler to invoke the callback when an event
 * occurs. On behalf of the consuming developer, the higher-order component
 * manages unbinding when the component unmounts, and binding at most a single
 * event handler for the entire application.
 *
 * @deprecated
 *
 * @param {Record<keyof GlobalEventHandlersEventMap, string>} eventTypesToHandlers Object with keys of DOM
 *                                                                                 event type, the value a
 *                                                                                 name of the function on
 *                                                                                 the original component's
 *                                                                                 instance which handles
 *                                                                                 the event.
 *
 * @return {any} Higher-order component.
 */

function withGlobalEvents(eventTypesToHandlers) {
  external_wp_deprecated_default()('wp.compose.withGlobalEvents', {
    since: '5.7',
    alternative: 'useEffect'
  });
  return create_higher_order_component(WrappedComponent => {
    class Wrapper extends external_wp_element_["Component"] {
      constructor(
      /** @type {any} */
      props) {
        super(props);
        this.handleEvent = this.handleEvent.bind(this);
        this.handleRef = this.handleRef.bind(this);
      }

      componentDidMount() {
        Object(external_lodash_["forEach"])(eventTypesToHandlers, (_, eventType) => {
          with_global_events_listener.add(eventType, this);
        });
      }

      componentWillUnmount() {
        Object(external_lodash_["forEach"])(eventTypesToHandlers, (_, eventType) => {
          with_global_events_listener.remove(eventType, this);
        });
      }

      handleEvent(
      /** @type {any} */
      event) {
        const handler = eventTypesToHandlers[
        /** @type {keyof GlobalEventHandlersEventMap} */
        event.type
        /* eslint-enable jsdoc/no-undefined-types */
        ];

        if (typeof this.wrappedRef[handler] === 'function') {
          this.wrappedRef[handler](event);
        }
      }

      handleRef(
      /** @type {any} */
      el) {
        this.wrappedRef = el; // Any component using `withGlobalEvents` that is not setting a `ref`
        // will cause `this.props.forwardedRef` to be `null`, so we need this
        // check.

        if (this.props.forwardedRef) {
          this.props.forwardedRef(el);
        }
      }

      render() {
        return Object(external_wp_element_["createElement"])(WrappedComponent, Object(esm_extends["a" /* default */])({}, this.props.ownProps, {
          ref: this.handleRef
        }));
      }

    }

    return Object(external_wp_element_["forwardRef"])((props, ref) => {
      return Object(external_wp_element_["createElement"])(Wrapper, {
        ownProps: props,
        forwardedRef: ref
      });
    });
  }, 'withGlobalEvents');
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-instance-id/index.js
// Disable reason: Object and object are distinctly different types in TypeScript and we mean the lowercase object in thise case
// but eslint wants to force us to use `Object`. See https://stackoverflow.com/questions/49464634/difference-between-object-and-object-in-typescript

/* eslint-disable jsdoc/check-types */

/**
 * WordPress dependencies
 */

/**
 * @type {WeakMap<object, number>}
 */

const instanceMap = new WeakMap();
/**
 * Creates a new id for a given object.
 *
 * @param {object} object Object reference to create an id for.
 * @return {number} The instance id (index).
 */

function createId(object) {
  const instances = instanceMap.get(object) || 0;
  instanceMap.set(object, instances + 1);
  return instances;
}
/**
 * Provides a unique instance ID.
 *
 * @param {object}          object           Object reference to create an id for.
 * @param {string}          [prefix]         Prefix for the unique id.
 * @param {string | number} [preferredId=''] Default ID to use.
 * @return {string | number} The unique instance id.
 */


function useInstanceId(object, prefix) {
  let preferredId = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : '';
  return Object(external_wp_element_["useMemo"])(() => {
    if (preferredId) return preferredId;
    const id = createId(object);
    return prefix ? `${prefix}-${id}` : id;
  }, [object]);
}
/* eslint-enable jsdoc/check-types */

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/higher-order/with-instance-id/index.js



/**
 * External dependencies
 */
// eslint-disable-next-line no-restricted-imports

/**
 * Internal dependencies
 */


/**
 * A Higher Order Component used to be provide a unique instance ID by
 * component.
 */

const withInstanceId = create_higher_order_component(WrappedComponent => {
  return props => {
    const instanceId = useInstanceId(WrappedComponent);
    return Object(external_wp_element_["createElement"])(WrappedComponent, Object(esm_extends["a" /* default */])({}, props, {
      instanceId: instanceId
    }));
  };
}, 'withInstanceId');
/* harmony default export */ var with_instance_id = (withInstanceId);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/higher-order/with-safe-timeout/index.js


/**
 * External dependencies
 */
 // eslint-disable-next-line no-restricted-imports

/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */



/**
 * A higher-order component used to provide and manage delayed function calls
 * that ought to be bound to a component's lifecycle.
 */
const withSafeTimeout = create_higher_order_component(OriginalComponent => {
  return class WrappedComponent extends external_wp_element_["Component"] {
    constructor(props) {
      super(props);
      this.timeouts = [];
      this.setTimeout = this.setTimeout.bind(this);
      this.clearTimeout = this.clearTimeout.bind(this);
    }

    componentWillUnmount() {
      this.timeouts.forEach(clearTimeout);
    }

    setTimeout(fn, delay) {
      const id = setTimeout(() => {
        fn();
        this.clearTimeout(id);
      }, delay);
      this.timeouts.push(id);
      return id;
    }

    clearTimeout(id) {
      clearTimeout(id);
      this.timeouts = Object(external_lodash_["without"])(this.timeouts, id);
    }

    render() {
      const props = { ...this.props,
        setTimeout: this.setTimeout,
        clearTimeout: this.clearTimeout
      };
      return Object(external_wp_element_["createElement"])(OriginalComponent, props);
    }

  };
}, 'withSafeTimeout');
/* harmony default export */ var with_safe_timeout = (withSafeTimeout);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/higher-order/with-state/index.js



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
 * @deprecated Use `useState` instead.
 *
 * @param {any} initialState Optional initial state of the component.
 *
 * @return {any} A higher order component wrapper accepting a component that takes the state props + its own props + `setState` and returning a component that only accepts the own props.
 */

function withState() {
  let initialState = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  external_wp_deprecated_default()('wp.compose.withState', {
    alternative: 'wp.element.useState'
  });
  return create_higher_order_component(OriginalComponent => {
    return class WrappedComponent extends external_wp_element_["Component"] {
      constructor(
      /** @type {any} */
      props) {
        super(props);
        this.setState = this.setState.bind(this);
        this.state = initialState;
      }

      render() {
        return Object(external_wp_element_["createElement"])(OriginalComponent, Object(esm_extends["a" /* default */])({}, this.props, this.state, {
          setState: this.setState
        }));
      }

    };
  }, 'withState');
}

// EXTERNAL MODULE: external ["wp","keycodes"]
var external_wp_keycodes_ = __webpack_require__("RxS6");

// EXTERNAL MODULE: external ["wp","dom"]
var external_wp_dom_ = __webpack_require__("1CF3");

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-ref-effect/index.js
/**
 * External dependencies
 */
// eslint-disable-next-line no-restricted-imports

/**
 * WordPress dependencies
 */

/**
 * Effect-like ref callback. Just like with `useEffect`, this allows you to
 * return a cleanup function to be run if the ref changes or one of the
 * dependencies changes. The ref is provided as an argument to the callback
 * functions. The main difference between this and `useEffect` is that
 * the `useEffect` callback is not called when the ref changes, but this is.
 * Pass the returned ref callback as the component's ref and merge multiple refs
 * with `useMergeRefs`.
 *
 * It's worth noting that if the dependencies array is empty, there's not
 * strictly a need to clean up event handlers for example, because the node is
 * to be removed. It *is* necessary if you add dependencies because the ref
 * callback will be called multiple times for the same node.
 *
 * @param  callback     Callback with ref as argument.
 * @param  dependencies Dependencies of the callback.
 *
 * @return Ref callback.
 */

function useRefEffect(callback, dependencies) {
  const cleanup = Object(external_wp_element_["useRef"])();
  return Object(external_wp_element_["useCallback"])(node => {
    if (node) {
      cleanup.current = callback(node);
    } else if (cleanup.current) {
      cleanup.current();
    }
  }, dependencies);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-constrained-tabbing/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * In Dialogs/modals, the tabbing must be constrained to the content of
 * the wrapper element. This hook adds the behavior to the returned ref.
 *
 * @return {import('react').RefCallback<Element>} Element Ref.
 *
 * @example
 * ```js
 * import { useConstrainedTabbing } from '@wordpress/compose';
 *
 * const ConstrainedTabbingExample = () => {
 *     const constrainedTabbingRef = useConstrainedTabbing()
 *     return (
 *         <div ref={ constrainedTabbingRef }>
 *             <Button />
 *             <Button />
 *         </div>
 *     );
 * }
 * ```
 */

function useConstrainedTabbing() {
  return useRefEffect((
  /** @type {HTMLElement} */
  node) => {
    /** @type {number|undefined} */
    let timeoutId;

    function onKeyDown(
    /** @type {KeyboardEvent} */
    event) {
      const {
        keyCode,
        shiftKey,
        target
      } = event;

      if (keyCode !== external_wp_keycodes_["TAB"]) {
        return;
      }

      const action = shiftKey ? 'findPrevious' : 'findNext';
      const nextElement = external_wp_dom_["focus"].tabbable[action](
      /** @type {HTMLElement} */
      target) || null; // If the element that is about to receive focus is outside the
      // area, move focus to a div and insert it at the start or end of
      // the area, depending on the direction. Without preventing default
      // behaviour, the browser will then move focus to the next element.

      if (node.contains(nextElement)) {
        return;
      }

      const domAction = shiftKey ? 'append' : 'prepend';
      const {
        ownerDocument
      } = node;
      const trap = ownerDocument.createElement('div');
      trap.tabIndex = -1;
      node[domAction](trap);
      trap.focus(); // Remove after the browser moves focus to the next element.

      timeoutId = setTimeout(() => node.removeChild(trap));
    }

    node.addEventListener('keydown', onKeyDown);
    return () => {
      node.removeEventListener('keydown', onKeyDown);
      clearTimeout(timeoutId);
    };
  }, []);
}

/* harmony default export */ var use_constrained_tabbing = (useConstrainedTabbing);

// EXTERNAL MODULE: ./node_modules/clipboard/dist/clipboard.js
var dist_clipboard = __webpack_require__("sxGJ");
var clipboard_default = /*#__PURE__*/__webpack_require__.n(dist_clipboard);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-copy-on-click/index.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/* eslint-disable jsdoc/no-undefined-types */

/**
 * Copies the text to the clipboard when the element is clicked.
 *
 * @deprecated
 *
 * @param {import('react').RefObject<string | Element | NodeListOf<Element>>} ref       Reference with the element.
 * @param {string|Function}                                                   text      The text to copy.
 * @param {number}                                                            [timeout] Optional timeout to reset the returned
 *                                                                                      state. 4 seconds by default.
 *
 * @return {boolean} Whether or not the text has been copied. Resets after the
 *                   timeout.
 */

function useCopyOnClick(ref, text) {
  let timeout = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : 4000;

  /* eslint-enable jsdoc/no-undefined-types */
  external_wp_deprecated_default()('wp.compose.useCopyOnClick', {
    since: '10.3',
    plugin: 'Gutenberg',
    alternative: 'wp.compose.useCopyToClipboard'
  });
  /** @type {import('react').MutableRefObject<Clipboard | undefined>} */

  const clipboard = Object(external_wp_element_["useRef"])();
  const [hasCopied, setHasCopied] = Object(external_wp_element_["useState"])(false);
  Object(external_wp_element_["useEffect"])(() => {
    /** @type {number | undefined} */
    let timeoutId;

    if (!ref.current) {
      return;
    } // Clipboard listens to click events.


    clipboard.current = new clipboard_default.a(ref.current, {
      text: () => typeof text === 'function' ? text() : text
    });
    clipboard.current.on('success', _ref => {
      let {
        clearSelection,
        trigger
      } = _ref;
      // Clearing selection will move focus back to the triggering button,
      // ensuring that it is not reset to the body, and further that it is
      // kept within the rendered node.
      clearSelection(); // Handle ClipboardJS focus bug, see https://github.com/zenorocha/clipboard.js/issues/680

      if (trigger) {
        /** @type {HTMLElement} */
        trigger.focus();
      }

      if (timeout) {
        setHasCopied(true);
        clearTimeout(timeoutId);
        timeoutId = setTimeout(() => setHasCopied(false), timeout);
      }
    });
    return () => {
      if (clipboard.current) {
        clipboard.current.destroy();
      }

      clearTimeout(timeoutId);
    };
  }, [text, timeout, setHasCopied]);
  return hasCopied;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-copy-to-clipboard/index.js
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
 * @template T
 * @param {T} value
 * @return {import('react').RefObject<T>} The updated ref
 */

function useUpdatedRef(value) {
  const ref = Object(external_wp_element_["useRef"])(value);
  ref.current = value;
  return ref;
}
/**
 * Copies the given text to the clipboard when the element is clicked.
 *
 * @template {HTMLElement} TElementType
 * @param {string | (() => string)} text      The text to copy. Use a function if not
 *                                            already available and expensive to compute.
 * @param {Function}                onSuccess Called when to text is copied.
 *
 * @return {import('react').Ref<TElementType>} A ref to assign to the target element.
 */


function useCopyToClipboard(text, onSuccess) {
  // Store the dependencies as refs and continuesly update them so they're
  // fresh when the callback is called.
  const textRef = useUpdatedRef(text);
  const onSuccessRef = useUpdatedRef(onSuccess);
  return useRefEffect(node => {
    // Clipboard listens to click events.
    const clipboard = new clipboard_default.a(node, {
      text() {
        return typeof textRef.current === 'function' ? textRef.current() : textRef.current || '';
      }

    });
    clipboard.on('success', _ref => {
      let {
        clearSelection
      } = _ref;
      // Clearing selection will move focus back to the triggering
      // button, ensuring that it is not reset to the body, and
      // further that it is kept within the rendered node.
      clearSelection(); // Handle ClipboardJS focus bug, see
      // https://github.com/zenorocha/clipboard.js/issues/680

      node.focus();

      if (onSuccessRef.current) {
        onSuccessRef.current();
      }
    });
    return () => {
      clipboard.destroy();
    };
  }, []);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-focus-on-mount/index.js
/**
 * WordPress dependencies
 */


/**
 * Hook used to focus the first tabbable element on mount.
 *
 * @param {boolean | 'firstElement'} focusOnMount Focus on mount mode.
 * @return {import('react').RefCallback<HTMLElement>} Ref callback.
 *
 * @example
 * ```js
 * import { useFocusOnMount } from '@wordpress/compose';
 *
 * const WithFocusOnMount = () => {
 *     const ref = useFocusOnMount()
 *     return (
 *         <div ref={ ref }>
 *             <Button />
 *             <Button />
 *         </div>
 *     );
 * }
 * ```
 */

function useFocusOnMount() {
  let focusOnMount = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'firstElement';
  const focusOnMountRef = Object(external_wp_element_["useRef"])(focusOnMount);
  Object(external_wp_element_["useEffect"])(() => {
    focusOnMountRef.current = focusOnMount;
  }, [focusOnMount]);
  return Object(external_wp_element_["useCallback"])(node => {
    var _node$ownerDocument$a, _node$ownerDocument;

    if (!node || focusOnMountRef.current === false) {
      return;
    }

    if (node.contains((_node$ownerDocument$a = (_node$ownerDocument = node.ownerDocument) === null || _node$ownerDocument === void 0 ? void 0 : _node$ownerDocument.activeElement) !== null && _node$ownerDocument$a !== void 0 ? _node$ownerDocument$a : null)) {
      return;
    }

    let target = node;

    if (focusOnMountRef.current === 'firstElement') {
      const firstTabbable = external_wp_dom_["focus"].tabbable.find(node)[0];

      if (firstTabbable) {
        target =
        /** @type {HTMLElement} */
        firstTabbable;
      }
    }

    target.focus();
  }, []);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-focus-return/index.js
/**
 * WordPress dependencies
 */

/**
 * When opening modals/sidebars/dialogs, the focus
 * must move to the opened area and return to the
 * previously focused element when closed.
 * The current hook implements the returning behavior.
 *
 * @param {() => void} [onFocusReturn] Overrides the default return behavior.
 * @return {import('react').RefCallback<HTMLElement>} Element Ref.
 *
 * @example
 * ```js
 * import { useFocusReturn } from '@wordpress/compose';
 *
 * const WithFocusReturn = () => {
 *     const ref = useFocusReturn()
 *     return (
 *         <div ref={ ref }>
 *             <Button />
 *             <Button />
 *         </div>
 *     );
 * }
 * ```
 */

function useFocusReturn(onFocusReturn) {
  /** @type {import('react').MutableRefObject<null | HTMLElement>} */
  const ref = Object(external_wp_element_["useRef"])(null);
  /** @type {import('react').MutableRefObject<null | Element>} */

  const focusedBeforeMount = Object(external_wp_element_["useRef"])(null);
  const onFocusReturnRef = Object(external_wp_element_["useRef"])(onFocusReturn);
  Object(external_wp_element_["useEffect"])(() => {
    onFocusReturnRef.current = onFocusReturn;
  }, [onFocusReturn]);
  return Object(external_wp_element_["useCallback"])(node => {
    if (node) {
      // Set ref to be used when unmounting.
      ref.current = node; // Only set when the node mounts.

      if (focusedBeforeMount.current) {
        return;
      }

      focusedBeforeMount.current = node.ownerDocument.activeElement;
    } else if (focusedBeforeMount.current) {
      var _ref$current, _ref$current2, _ref$current3;

      const isFocused = (_ref$current = ref.current) === null || _ref$current === void 0 ? void 0 : _ref$current.contains((_ref$current2 = ref.current) === null || _ref$current2 === void 0 ? void 0 : _ref$current2.ownerDocument.activeElement);

      if ((_ref$current3 = ref.current) !== null && _ref$current3 !== void 0 && _ref$current3.isConnected && !isFocused) {
        return;
      } // Defer to the component's own explicit focus return behavior, if
      // specified. This allows for support that the `onFocusReturn`
      // decides to allow the default behavior to occur under some
      // conditions.


      if (onFocusReturnRef.current) {
        onFocusReturnRef.current();
      } else {
        var _focusedBeforeMount$c;

        /** @type {null | HTMLElement} */
        (_focusedBeforeMount$c = focusedBeforeMount.current) === null || _focusedBeforeMount$c === void 0 ? void 0 : _focusedBeforeMount$c.focus();
      }
    }
  }, []);
}

/* harmony default export */ var use_focus_return = (useFocusReturn);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-focus-outside/index.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Input types which are classified as button types, for use in considering
 * whether element is a (focus-normalized) button.
 *
 * @type {string[]}
 */

const INPUT_BUTTON_TYPES = ['button', 'submit'];
/**
 * @typedef {HTMLButtonElement | HTMLLinkElement | HTMLInputElement} FocusNormalizedButton
 */
// Disable reason: Rule doesn't support predicate return types

/* eslint-disable jsdoc/valid-types */

/**
 * Returns true if the given element is a button element subject to focus
 * normalization, or false otherwise.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/button#Clicking_and_focus
 *
 * @param {EventTarget} eventTarget The target from a mouse or touch event.
 *
 * @return {eventTarget is FocusNormalizedButton} Whether element is a button.
 */

function isFocusNormalizedButton(eventTarget) {
  if (!(eventTarget instanceof window.HTMLElement)) {
    return false;
  }

  switch (eventTarget.nodeName) {
    case 'A':
    case 'BUTTON':
      return true;

    case 'INPUT':
      return Object(external_lodash_["includes"])(INPUT_BUTTON_TYPES,
      /** @type {HTMLInputElement} */
      eventTarget.type);
  }

  return false;
}
/* eslint-enable jsdoc/valid-types */

/**
 * @typedef {import('react').SyntheticEvent} SyntheticEvent
 */

/**
 * @callback EventCallback
 * @param {SyntheticEvent} event input related event.
 */

/**
 * @typedef FocusOutsideReactElement
 * @property {EventCallback} handleFocusOutside callback for a focus outside event.
 */

/**
 * @typedef {import('react').MutableRefObject<FocusOutsideReactElement | undefined>} FocusOutsideRef
 */

/**
 * @typedef {Object} FocusOutsideReturnValue
 * @property {EventCallback} onFocus      An event handler for focus events.
 * @property {EventCallback} onBlur       An event handler for blur events.
 * @property {EventCallback} onMouseDown  An event handler for mouse down events.
 * @property {EventCallback} onMouseUp    An event handler for mouse up events.
 * @property {EventCallback} onTouchStart An event handler for touch start events.
 * @property {EventCallback} onTouchEnd   An event handler for touch end events.
 */

/**
 * A react hook that can be used to check whether focus has moved outside the
 * element the event handlers are bound to.
 *
 * @param {EventCallback} onFocusOutside A callback triggered when focus moves outside
 *                                       the element the event handlers are bound to.
 *
 * @return {FocusOutsideReturnValue} An object containing event handlers. Bind the event handlers
 *                                   to a wrapping element element to capture when focus moves
 *                                   outside that element.
 */


function useFocusOutside(onFocusOutside) {
  const currentOnFocusOutside = Object(external_wp_element_["useRef"])(onFocusOutside);
  Object(external_wp_element_["useEffect"])(() => {
    currentOnFocusOutside.current = onFocusOutside;
  }, [onFocusOutside]);
  const preventBlurCheck = Object(external_wp_element_["useRef"])(false);
  /**
   * @type {import('react').MutableRefObject<number | undefined>}
   */

  const blurCheckTimeoutId = Object(external_wp_element_["useRef"])();
  /**
   * Cancel a blur check timeout.
   */

  const cancelBlurCheck = Object(external_wp_element_["useCallback"])(() => {
    clearTimeout(blurCheckTimeoutId.current);
  }, []); // Cancel blur checks on unmount.

  Object(external_wp_element_["useEffect"])(() => {
    return () => cancelBlurCheck();
  }, []); // Cancel a blur check if the callback or ref is no longer provided.

  Object(external_wp_element_["useEffect"])(() => {
    if (!onFocusOutside) {
      cancelBlurCheck();
    }
  }, [onFocusOutside, cancelBlurCheck]);
  /**
   * Handles a mousedown or mouseup event to respectively assign and
   * unassign a flag for preventing blur check on button elements. Some
   * browsers, namely Firefox and Safari, do not emit a focus event on
   * button elements when clicked, while others do. The logic here
   * intends to normalize this as treating click on buttons as focus.
   *
   * @see https://developer.mozilla.org/en-US/docs/Web/HTML/Element/button#Clicking_and_focus
   *
   * @param {SyntheticEvent} event Event for mousedown or mouseup.
   */

  const normalizeButtonFocus = Object(external_wp_element_["useCallback"])(event => {
    const {
      type,
      target
    } = event;
    const isInteractionEnd = Object(external_lodash_["includes"])(['mouseup', 'touchend'], type);

    if (isInteractionEnd) {
      preventBlurCheck.current = false;
    } else if (isFocusNormalizedButton(target)) {
      preventBlurCheck.current = true;
    }
  }, []);
  /**
   * A callback triggered when a blur event occurs on the element the handler
   * is bound to.
   *
   * Calls the `onFocusOutside` callback in an immediate timeout if focus has
   * move outside the bound element and is still within the document.
   *
   * @param {SyntheticEvent} event Blur event.
   */

  const queueBlurCheck = Object(external_wp_element_["useCallback"])(event => {
    // React does not allow using an event reference asynchronously
    // due to recycling behavior, except when explicitly persisted.
    event.persist(); // Skip blur check if clicking button. See `normalizeButtonFocus`.

    if (preventBlurCheck.current) {
      return;
    }

    blurCheckTimeoutId.current = setTimeout(() => {
      // If document is not focused then focus should remain
      // inside the wrapped component and therefore we cancel
      // this blur event thereby leaving focus in place.
      // https://developer.mozilla.org/en-US/docs/Web/API/Document/hasFocus.
      if (!document.hasFocus()) {
        event.preventDefault();
        return;
      }

      if ('function' === typeof currentOnFocusOutside.current) {
        currentOnFocusOutside.current(event);
      }
    }, 0);
  }, []);
  return {
    onFocus: cancelBlurCheck,
    onMouseDown: normalizeButtonFocus,
    onMouseUp: normalizeButtonFocus,
    onTouchStart: normalizeButtonFocus,
    onTouchEnd: normalizeButtonFocus,
    onBlur: queueBlurCheck
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-merge-refs/index.js
/**
 * WordPress dependencies
 */

/* eslint-disable jsdoc/valid-types */

/**
 * @template T
 * @typedef {T extends import('react').Ref<infer R> ? R : never} TypeFromRef
 */

/* eslint-enable jsdoc/valid-types */

/**
 * @template T
 * @param {import('react').Ref<T>} ref
 * @param {T}                      value
 */

function assignRef(ref, value) {
  if (typeof ref === 'function') {
    ref(value);
  } else if (ref && ref.hasOwnProperty('current')) {
    /* eslint-disable jsdoc/no-undefined-types */

    /** @type {import('react').MutableRefObject<T>} */
    ref.current = value;
    /* eslint-enable jsdoc/no-undefined-types */
  }
}
/**
 * Merges refs into one ref callback.
 *
 * It also ensures that the merged ref callbacks are only called when they
 * change (as a result of a `useCallback` dependency update) OR when the ref
 * value changes, just as React does when passing a single ref callback to the
 * component.
 *
 * As expected, if you pass a new function on every render, the ref callback
 * will be called after every render.
 *
 * If you don't wish a ref callback to be called after every render, wrap it
 * with `useCallback( callback, dependencies )`. When a dependency changes, the
 * old ref callback will be called with `null` and the new ref callback will be
 * called with the same value.
 *
 * To make ref callbacks easier to use, you can also pass the result of
 * `useRefEffect`, which makes cleanup easier by allowing you to return a
 * cleanup function instead of handling `null`.
 *
 * It's also possible to _disable_ a ref (and its behaviour) by simply not
 * passing the ref.
 *
 * ```jsx
 * const ref = useRefEffect( ( node ) => {
 *   node.addEventListener( ... );
 *   return () => {
 *     node.removeEventListener( ... );
 *   };
 * }, [ ...dependencies ] );
 * const otherRef = useRef();
 * const mergedRefs useMergeRefs( [
 *   enabled && ref,
 *   otherRef,
 * ] );
 * return <div ref={ mergedRefs } />;
 * ```
 *
 * @template {import('react').Ref<any>} TRef
 * @param {Array<TRef>} refs The refs to be merged.
 *
 * @return {import('react').RefCallback<TypeFromRef<TRef>>} The merged ref callback.
 */


function useMergeRefs(refs) {
  const element = Object(external_wp_element_["useRef"])();
  const didElementChange = Object(external_wp_element_["useRef"])(false);
  /* eslint-disable jsdoc/no-undefined-types */

  /** @type {import('react').MutableRefObject<TRef[]>} */

  /* eslint-enable jsdoc/no-undefined-types */

  const previousRefs = Object(external_wp_element_["useRef"])([]);
  const currentRefs = Object(external_wp_element_["useRef"])(refs); // Update on render before the ref callback is called, so the ref callback
  // always has access to the current refs.

  currentRefs.current = refs; // If any of the refs change, call the previous ref with `null` and the new
  // ref with the node, except when the element changes in the same cycle, in
  // which case the ref callbacks will already have been called.

  Object(external_wp_element_["useLayoutEffect"])(() => {
    if (didElementChange.current === false) {
      refs.forEach((ref, index) => {
        const previousRef = previousRefs.current[index];

        if (ref !== previousRef) {
          assignRef(previousRef, null);
          assignRef(ref, element.current);
        }
      });
    }

    previousRefs.current = refs;
  }, refs); // No dependencies, must be reset after every render so ref callbacks are
  // correctly called after a ref change.

  Object(external_wp_element_["useLayoutEffect"])(() => {
    didElementChange.current = false;
  }); // There should be no dependencies so that `callback` is only called when
  // the node changes.

  return Object(external_wp_element_["useCallback"])(value => {
    // Update the element so it can be used when calling ref callbacks on a
    // dependency change.
    assignRef(element, value);
    didElementChange.current = true; // When an element changes, the current ref callback should be called
    // with the new element and the previous one with `null`.

    const refsToAssign = value ? currentRefs.current : previousRefs.current; // Update the latest refs.

    for (const ref of refsToAssign) {
      assignRef(ref, value);
    }
  }, []);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-dialog/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */






/* eslint-disable jsdoc/valid-types */

/**
 * @typedef DialogOptions
 * @property {Parameters<useFocusOnMount>[0]} focusOnMount Focus on mount arguments.
 * @property {() => void}                     onClose      Function to call when the dialog is closed.
 */

/* eslint-enable jsdoc/valid-types */

/**
 * Returns a ref and props to apply to a dialog wrapper to enable the following behaviors:
 *  - constrained tabbing.
 *  - focus on mount.
 *  - return focus on unmount.
 *  - focus outside.
 *
 * @param {DialogOptions} options Dialog Options.
 */

function useDialog(options) {
  /**
   * @type {import('react').MutableRefObject<DialogOptions | undefined>}
   */
  const currentOptions = Object(external_wp_element_["useRef"])();
  Object(external_wp_element_["useEffect"])(() => {
    currentOptions.current = options;
  }, Object.values(options));
  const constrainedTabbingRef = use_constrained_tabbing();
  const focusOnMountRef = useFocusOnMount(options.focusOnMount);
  const focusReturnRef = use_focus_return();
  const focusOutsideProps = useFocusOutside(event => {
    var _currentOptions$curre, _currentOptions$curre2;

    // This unstable prop  is here only to manage backward compatibility
    // for the Popover component otherwise, the onClose should be enough.
    // @ts-ignore unstable property
    if ((_currentOptions$curre = currentOptions.current) !== null && _currentOptions$curre !== void 0 && _currentOptions$curre.__unstableOnClose) {
      // @ts-ignore unstable property
      currentOptions.current.__unstableOnClose('focus-outside', event);
    } else if ((_currentOptions$curre2 = currentOptions.current) !== null && _currentOptions$curre2 !== void 0 && _currentOptions$curre2.onClose) {
      currentOptions.current.onClose();
    }
  });
  const closeOnEscapeRef = Object(external_wp_element_["useCallback"])(node => {
    if (!node) {
      return;
    }

    node.addEventListener('keydown', (
    /** @type {KeyboardEvent} */
    event) => {
      var _currentOptions$curre3;

      // Close on escape
      if (event.keyCode === external_wp_keycodes_["ESCAPE"] && !event.defaultPrevented && (_currentOptions$curre3 = currentOptions.current) !== null && _currentOptions$curre3 !== void 0 && _currentOptions$curre3.onClose) {
        event.preventDefault();
        currentOptions.current.onClose();
      }
    });
  }, []);
  return [useMergeRefs([options.focusOnMount !== false ? constrainedTabbingRef : null, options.focusOnMount !== false ? focusReturnRef : null, options.focusOnMount !== false ? focusOnMountRef : null, closeOnEscapeRef]), { ...focusOutsideProps,
    tabIndex: '-1'
  }];
}

/* harmony default export */ var use_dialog = (useDialog);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-disabled/index.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



/**
 * Names of control nodes which qualify for disabled behavior.
 *
 * See WHATWG HTML Standard: 4.10.18.5: "Enabling and disabling form controls: the disabled attribute".
 *
 * @see https://html.spec.whatwg.org/multipage/form-control-infrastructure.html#enabling-and-disabling-form-controls:-the-disabled-attribute
 *
 * @type {string[]}
 */

const DISABLED_ELIGIBLE_NODE_NAMES = ['BUTTON', 'FIELDSET', 'INPUT', 'OPTGROUP', 'OPTION', 'SELECT', 'TEXTAREA'];
/**
 * In some circumstances, such as block previews, all focusable DOM elements
 * (input fields, links, buttons, etc.) need to be disabled. This hook adds the
 * behavior to disable nested DOM elements to the returned ref.
 *
 * @return {import('react').RefObject<HTMLElement>} Element Ref.
 *
 * @example
 * ```js
 * import { __experimentalUseDisabled as useDisabled } from '@wordpress/compose';
 * const DisabledExample = () => {
 * 	const disabledRef = useDisabled();
 *	return (
 *		<div ref={ disabledRef }>
 *			<a href="#">This link will have tabindex set to -1</a>
 *			<input placeholder="This input will have the disabled attribute added to it." type="text" />
 *		</div>
 *	);
 * };
 * ```
 */

function useDisabled() {
  /** @type {import('react').RefObject<HTMLElement>} */
  const node = Object(external_wp_element_["useRef"])(null);

  const disable = () => {
    if (!node.current) {
      return;
    }

    external_wp_dom_["focus"].focusable.find(node.current).forEach(focusable => {
      if (Object(external_lodash_["includes"])(DISABLED_ELIGIBLE_NODE_NAMES, focusable.nodeName)) {
        focusable.setAttribute('disabled', '');
      }

      if (focusable.nodeName === 'A') {
        focusable.setAttribute('tabindex', '-1');
      }

      const tabIndex = focusable.getAttribute('tabindex');

      if (tabIndex !== null && tabIndex !== '-1') {
        focusable.removeAttribute('tabindex');
      }

      if (focusable.hasAttribute('contenteditable')) {
        focusable.setAttribute('contenteditable', 'false');
      }
    });
  }; // Debounce re-disable since disabling process itself will incur
  // additional mutations which should be ignored.


  const debouncedDisable = Object(external_wp_element_["useCallback"])(Object(external_lodash_["debounce"])(disable, undefined, {
    leading: true
  }), []);
  Object(external_wp_element_["useLayoutEffect"])(() => {
    disable();
    /** @type {MutationObserver | undefined} */

    let observer;

    if (node.current) {
      observer = new window.MutationObserver(debouncedDisable);
      observer.observe(node.current, {
        childList: true,
        attributes: true,
        subtree: true
      });
    }

    return () => {
      if (observer) {
        observer.disconnect();
      }

      debouncedDisable.cancel();
    };
  }, []);
  return node;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-isomorphic-layout-effect/index.js
/**
 * WordPress dependencies
 */

/**
 * Preferred over direct usage of `useLayoutEffect` when supporting
 * server rendered components (SSR) because currently React
 * throws a warning when using useLayoutEffect in that environment.
 */

const useIsomorphicLayoutEffect = typeof window !== 'undefined' ? external_wp_element_["useLayoutEffect"] : external_wp_element_["useEffect"];
/* harmony default export */ var use_isomorphic_layout_effect = (useIsomorphicLayoutEffect);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-dragging/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * @param {Object}                  props
 * @param {(e: MouseEvent) => void} props.onDragStart
 * @param {(e: MouseEvent) => void} props.onDragMove
 * @param {(e: MouseEvent) => void} props.onDragEnd
 */

function useDragging(_ref) {
  let {
    onDragStart,
    onDragMove,
    onDragEnd
  } = _ref;
  const [isDragging, setIsDragging] = Object(external_wp_element_["useState"])(false);
  const eventsRef = Object(external_wp_element_["useRef"])({
    onDragStart,
    onDragMove,
    onDragEnd
  });
  use_isomorphic_layout_effect(() => {
    eventsRef.current.onDragStart = onDragStart;
    eventsRef.current.onDragMove = onDragMove;
    eventsRef.current.onDragEnd = onDragEnd;
  }, [onDragStart, onDragMove, onDragEnd]);
  const onMouseMove = Object(external_wp_element_["useCallback"])((
  /** @type {MouseEvent} */
  event) => eventsRef.current.onDragMove && eventsRef.current.onDragMove(event), []);
  const endDrag = Object(external_wp_element_["useCallback"])((
  /** @type {MouseEvent} */
  event) => {
    if (eventsRef.current.onDragEnd) {
      eventsRef.current.onDragEnd(event);
    }

    document.removeEventListener('mousemove', onMouseMove);
    document.removeEventListener('mouseup', endDrag);
    setIsDragging(false);
  }, []);
  const startDrag = Object(external_wp_element_["useCallback"])((
  /** @type {MouseEvent} */
  event) => {
    if (eventsRef.current.onDragStart) {
      eventsRef.current.onDragStart(event);
    }

    document.addEventListener('mousemove', onMouseMove);
    document.addEventListener('mouseup', endDrag);
    setIsDragging(true);
  }, []); // Remove the global events when unmounting if needed.

  Object(external_wp_element_["useEffect"])(() => {
    return () => {
      if (isDragging) {
        document.removeEventListener('mousemove', onMouseMove);
        document.removeEventListener('mouseup', endDrag);
      }
    };
  }, [isDragging]);
  return {
    startDrag,
    endDrag,
    isDragging
  };
}

// EXTERNAL MODULE: ./node_modules/mousetrap/mousetrap.js
var mousetrap_mousetrap = __webpack_require__("imBb");
var mousetrap_default = /*#__PURE__*/__webpack_require__.n(mousetrap_mousetrap);

// EXTERNAL MODULE: ./node_modules/mousetrap/plugins/global-bind/mousetrap-global-bind.js
var mousetrap_global_bind = __webpack_require__("VcSt");

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-keyboard-shortcut/index.js
/**
 * External dependencies
 */



/**
 * WordPress dependencies
 */


/**
 * A block selection object.
 *
 * @typedef {Object} WPKeyboardShortcutConfig
 *
 * @property {boolean}                                [bindGlobal] Handle keyboard events anywhere including inside textarea/input fields.
 * @property {string}                                 [eventName]  Event name used to trigger the handler, defaults to keydown.
 * @property {boolean}                                [isDisabled] Disables the keyboard handler if the value is true.
 * @property {import('react').RefObject<HTMLElement>} [target]     React reference to the DOM element used to catch the keyboard event.
 */

/**
 * Return true if platform is MacOS.
 *
 * @param {Window} [_window] window object by default; used for DI testing.
 *
 * @return {boolean} True if MacOS; false otherwise.
 */

function isAppleOS() {
  let _window = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : window;

  const {
    platform
  } = _window.navigator;
  return platform.indexOf('Mac') !== -1 || Object(external_lodash_["includes"])(['iPad', 'iPhone'], platform);
}
/* eslint-disable jsdoc/valid-types */

/**
 * Attach a keyboard shortcut handler.
 *
 * @see https://craig.is/killing/mice#api.bind for information about the `callback` parameter.
 *
 * @param {string[]|string}                                                       shortcuts Keyboard Shortcuts.
 * @param {(e: import('mousetrap').ExtendedKeyboardEvent, combo: string) => void} callback  Shortcut callback.
 * @param {WPKeyboardShortcutConfig}                                              options   Shortcut options.
 */


function useKeyboardShortcut(
/* eslint-enable jsdoc/valid-types */
shortcuts, callback) {
  let {
    bindGlobal = false,
    eventName = 'keydown',
    isDisabled = false,
    // This is important for performance considerations.
    target
  } = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  const currentCallback = Object(external_wp_element_["useRef"])(callback);
  Object(external_wp_element_["useEffect"])(() => {
    currentCallback.current = callback;
  }, [callback]);
  Object(external_wp_element_["useEffect"])(() => {
    if (isDisabled) {
      return;
    }

    const mousetrap = new mousetrap_default.a(target && target.current ? target.current : // We were passing `document` here previously, so to successfully cast it to Element we must cast it first to `unknown`.
    // Not sure if this is a mistake but it was the behavior previous to the addition of types so we're just doing what's
    // necessary to maintain the existing behavior

    /** @type {Element} */

    /** @type {unknown} */
    document);
    Object(external_lodash_["castArray"])(shortcuts).forEach(shortcut => {
      const keys = shortcut.split('+'); // Determines whether a key is a modifier by the length of the string.
      // E.g. if I add a pass a shortcut Shift+Cmd+M, it'll determine that
      // the modifiers are Shift and Cmd because they're not a single character.

      const modifiers = new Set(keys.filter(value => value.length > 1));
      const hasAlt = modifiers.has('alt');
      const hasShift = modifiers.has('shift'); // This should be better moved to the shortcut registration instead.

      if (isAppleOS() && (modifiers.size === 1 && hasAlt || modifiers.size === 2 && hasAlt && hasShift)) {
        throw new Error(`Cannot bind ${shortcut}. Alt and Shift+Alt modifiers are reserved for character input.`);
      }

      const bindFn = bindGlobal ? 'bindGlobal' : 'bind'; // @ts-ignore `bindGlobal` is an undocumented property

      mousetrap[bindFn](shortcut, function () {
        return (
          /* eslint-enable jsdoc/valid-types */
          currentCallback.current(...arguments)
        );
      }, eventName);
    });
    return () => {
      mousetrap.reset();
    };
  }, [shortcuts, bindGlobal, eventName, target, isDisabled]);
}

/* harmony default export */ var use_keyboard_shortcut = (useKeyboardShortcut);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-media-query/index.js
/**
 * WordPress dependencies
 */

/**
 * Runs a media query and returns its value when it changes.
 *
 * @param {string} [query] Media Query.
 * @return {boolean} return value of the media query.
 */

function useMediaQuery(query) {
  const [match, setMatch] = Object(external_wp_element_["useState"])(() => !!(query && typeof window !== 'undefined' && window.matchMedia(query).matches));
  Object(external_wp_element_["useEffect"])(() => {
    if (!query) {
      return;
    }

    const updateMatch = () => setMatch(window.matchMedia(query).matches);

    updateMatch();
    const list = window.matchMedia(query);
    list.addListener(updateMatch);
    return () => {
      list.removeListener(updateMatch);
    };
  }, [query]);
  return !!query && match;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-previous/index.js
/**
 * WordPress dependencies
 */

/**
 * Use something's value from the previous render.
 * Based on https://usehooks.com/usePrevious/.
 *
 * @param  value The value to track.
 *
 * @return The value from the previous render.
 */

function usePrevious(value) {
  const ref = Object(external_wp_element_["useRef"])(); // Store current value in ref.

  Object(external_wp_element_["useEffect"])(() => {
    ref.current = value;
  }, [value]); // Re-run when value changes.
  // Return previous value (happens before update in useEffect above).

  return ref.current;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-reduced-motion/index.js
/**
 * Internal dependencies
 */

/**
 * Hook returning whether the user has a preference for reduced motion.
 *
 * @return {boolean} Reduced motion preference value.
 */

const useReducedMotion = () => useMediaQuery('(prefers-reduced-motion: reduce)');

/* harmony default export */ var use_reduced_motion = (useReducedMotion);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-viewport-match/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * @typedef {"huge" | "wide" | "large" | "medium" | "small" | "mobile"} WPBreakpoint
 */

/**
 * Hash of breakpoint names with pixel width at which it becomes effective.
 *
 * @see _breakpoints.scss
 *
 * @type {Record<WPBreakpoint, number>}
 */

const BREAKPOINTS = {
  huge: 1440,
  wide: 1280,
  large: 960,
  medium: 782,
  small: 600,
  mobile: 480
};
/**
 * @typedef {">=" | "<"} WPViewportOperator
 */

/**
 * Object mapping media query operators to the condition to be used.
 *
 * @type {Record<WPViewportOperator, string>}
 */

const CONDITIONS = {
  '>=': 'min-width',
  '<': 'max-width'
};
/**
 * Object mapping media query operators to a function that given a breakpointValue and a width evaluates if the operator matches the values.
 *
 * @type {Record<WPViewportOperator, (breakpointValue: number, width: number) => boolean>}
 */

const OPERATOR_EVALUATORS = {
  '>=': (breakpointValue, width) => width >= breakpointValue,
  '<': (breakpointValue, width) => width < breakpointValue
};
const ViewportMatchWidthContext = Object(external_wp_element_["createContext"])(
/** @type {null | number} */
null);
/**
 * Returns true if the viewport matches the given query, or false otherwise.
 *
 * @param {WPBreakpoint}       breakpoint      Breakpoint size name.
 * @param {WPViewportOperator} [operator=">="] Viewport operator.
 *
 * @example
 *
 * ```js
 * useViewportMatch( 'huge', '<' );
 * useViewportMatch( 'medium' );
 * ```
 *
 * @return {boolean} Whether viewport matches query.
 */

const useViewportMatch = function (breakpoint) {
  let operator = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '>=';
  const simulatedWidth = Object(external_wp_element_["useContext"])(ViewportMatchWidthContext);
  const mediaQuery = !simulatedWidth && `(${CONDITIONS[operator]}: ${BREAKPOINTS[breakpoint]}px)`;
  const mediaQueryResult = useMediaQuery(mediaQuery || undefined);

  if (simulatedWidth) {
    return OPERATOR_EVALUATORS[operator](BREAKPOINTS[breakpoint], simulatedWidth);
  }

  return mediaQueryResult;
};

useViewportMatch.__experimentalWidthProvider = ViewportMatchWidthContext.Provider;
/* harmony default export */ var use_viewport_match = (useViewportMatch);

// EXTERNAL MODULE: ./node_modules/react-resize-aware/dist/index.js
var dist = __webpack_require__("SSiF");
var dist_default = /*#__PURE__*/__webpack_require__.n(dist);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-resize-observer/index.js
/**
 * External dependencies
 */

/**
 * Hook which allows to listen the resize event of any target element when it changes sizes.
 * _Note: `useResizeObserver` will report `null` until after first render_
 *
 * Simply a re-export of `react-resize-aware` so refer to its documentation <https://github.com/FezVrasta/react-resize-aware>
 * for more details.
 *
 * @see https://github.com/FezVrasta/react-resize-aware
 *
 * @example
 *
 * ```js
 * const App = () => {
 * 	const [ resizeListener, sizes ] = useResizeObserver();
 *
 * 	return (
 * 		<div>
 * 			{ resizeListener }
 * 			Your content here
 * 		</div>
 * 	);
 * };
 * ```
 *
 */

/* harmony default export */ var use_resize_observer = (dist_default.a);

// EXTERNAL MODULE: external ["wp","priorityQueue"]
var external_wp_priorityQueue_ = __webpack_require__("XI5e");

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-async-list/index.js
/**
 * WordPress dependencies
 */



/**
 * Returns the first items from list that are present on state.
 *
 * @param  list  New array.
 * @param  state Current state.
 * @return First items present iin state.
 */
function getFirstItemsPresentInState(list, state) {
  const firstItems = [];

  for (let i = 0; i < list.length; i++) {
    const item = list[i];

    if (!state.includes(item)) {
      break;
    }

    firstItems.push(item);
  }

  return firstItems;
}
/**
 * React hook returns an array which items get asynchronously appended from a source array.
 * This behavior is useful if we want to render a list of items asynchronously for performance reasons.
 *
 * @param  list   Source array.
 * @param  config Configuration object.
 *
 * @return Async array.
 */


function useAsyncList(list) {
  let config = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {
    step: 1
  };
  const {
    step = 1
  } = config;
  const [current, setCurrent] = Object(external_wp_element_["useState"])([]);
  Object(external_wp_element_["useEffect"])(() => {
    // On reset, we keep the first items that were previously rendered.
    let firstItems = getFirstItemsPresentInState(list, current);

    if (firstItems.length < step) {
      firstItems = firstItems.concat(list.slice(firstItems.length, step));
    }

    setCurrent(firstItems);
    let nextIndex = firstItems.length;
    const asyncQueue = Object(external_wp_priorityQueue_["createQueue"])();

    const append = () => {
      if (list.length <= nextIndex) {
        return;
      }

      setCurrent(state => [...state, ...list.slice(nextIndex, nextIndex + step)]);
      nextIndex += step;
      asyncQueue.add({}, append);
    };

    asyncQueue.add({}, append);
    return () => asyncQueue.reset();
  }, [list]);
  return current;
}

/* harmony default export */ var use_async_list = (useAsyncList);

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-warn-on-change/index.js
/**
 * Internal dependencies
 */
 // Disable reason: Object and object are distinctly different types in TypeScript and we mean the lowercase object in thise case
// but eslint wants to force us to use `Object`. See https://stackoverflow.com/questions/49464634/difference-between-object-and-object-in-typescript

/* eslint-disable jsdoc/check-types */

/**
 * Hook that performs a shallow comparison between the preview value of an object
 * and the new one, if there's a difference, it prints it to the console.
 * this is useful in performance related work, to check why a component re-renders.
 *
 *  @example
 *
 * ```jsx
 * function MyComponent(props) {
 *    useWarnOnChange(props);
 *
 *    return "Something";
 * }
 * ```
 *
 * @param {object} object Object which changes to compare.
 * @param {string} prefix Just a prefix to show when console logging.
 */

function useWarnOnChange(object) {
  let prefix = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 'Change detection';
  const previousValues = usePrevious(object);
  Object.entries(previousValues !== null && previousValues !== void 0 ? previousValues : []).forEach(_ref => {
    let [key, value] = _ref;

    if (value !== object[
    /** @type {keyof typeof object} */
    key]) {
      // eslint-disable-next-line no-console
      console.warn(`${prefix}: ${key} key changed:`, value, object[
      /** @type {keyof typeof object} */
      key]
      /* eslint-enable jsdoc/check-types */
      );
    }
  });
}

/* harmony default export */ var use_warn_on_change = (useWarnOnChange);

// EXTERNAL MODULE: ./node_modules/use-memo-one/dist/use-memo-one.esm.js
var use_memo_one_esm = __webpack_require__("mHlH");

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-debounce/index.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/* eslint-disable jsdoc/valid-types */

/**
 * Debounces a function with Lodash's `debounce`. A new debounced function will
 * be returned and any scheduled calls cancelled if any of the arguments change,
 * including the function to debounce, so please wrap functions created on
 * render in components in `useCallback`.
 *
 * @see https://docs-lodash.com/v4/debounce/
 *
 * @template {(...args: any[]) => void} TFunc
 *
 * @param {TFunc}                             fn        The function to debounce.
 * @param {number}                            [wait]    The number of milliseconds to delay.
 * @param {import('lodash').DebounceSettings} [options] The options object.
 * @return {import('lodash').DebouncedFunc<TFunc>} Debounced function.
 */

function useDebounce(fn, wait, options) {
  /* eslint-enable jsdoc/valid-types */
  const debounced = Object(use_memo_one_esm["a" /* useMemoOne */])(() => Object(external_lodash_["debounce"])(fn, wait, options), [fn, wait, options]);
  Object(external_wp_element_["useEffect"])(() => () => debounced.cancel(), [debounced]);
  return debounced;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-throttle/index.js
/**
 * External dependencies
 */


/**
 * WordPress dependencies
 */


/**
 * Throttles a function with Lodash's `throttle`. A new throttled function will
 * be returned and any scheduled calls cancelled if any of the arguments change,
 * including the function to throttle, so please wrap functions created on
 * render in components in `useCallback`.
 *
 * @see https://docs-lodash.com/v4/throttle/
 *
 * @template {(...args: any[]) => void} TFunc
 *
 * @param {TFunc}                             fn        The function to throttle.
 * @param {number}                            [wait]    The number of milliseconds to throttle invocations to.
 * @param {import('lodash').ThrottleSettings} [options] The options object. See linked documentation for details.
 * @return {import('lodash').DebouncedFunc<TFunc>} Throttled function.
 */

function useThrottle(fn, wait, options) {
  const throttled = Object(use_memo_one_esm["a" /* useMemoOne */])(() => Object(external_lodash_["throttle"])(fn, wait, options), [fn, wait, options]);
  Object(external_wp_element_["useEffect"])(() => () => throttled.cancel(), [throttled]);
  return throttled;
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-drop-zone/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/* eslint-disable jsdoc/valid-types */

/**
 * @template T
 * @param {T} value
 * @return {import('react').MutableRefObject<T>} A ref with the value.
 */

function useFreshRef(value) {
  /* eslint-enable jsdoc/valid-types */

  /* eslint-disable jsdoc/no-undefined-types */

  /** @type {import('react').MutableRefObject<T>} */

  /* eslint-enable jsdoc/no-undefined-types */
  // Disable reason: We're doing something pretty JavaScript-y here where the
  // ref will always have a current value that is not null or undefined but it
  // needs to start as undefined. We don't want to change the return type so
  // it's easier to just ts-ignore this specific line that's complaining about
  // undefined not being part of T.
  // @ts-ignore
  const ref = Object(external_wp_element_["useRef"])();
  ref.current = value;
  return ref;
}
/**
 * A hook to facilitate drag and drop handling.
 *
 * @param {Object}                  props             Named parameters.
 * @param {boolean}                 props.isDisabled  Whether or not to disable the drop zone.
 * @param {(e: DragEvent) => void}  props.onDragStart Called when dragging has started.
 * @param {(e: DragEvent) => void}  props.onDragEnter Called when the zone is entered.
 * @param {(e: DragEvent) => void}  props.onDragOver  Called when the zone is moved within.
 * @param {(e: DragEvent) => void}  props.onDragLeave Called when the zone is left.
 * @param {(e: MouseEvent) => void} props.onDragEnd   Called when dragging has ended.
 * @param {(e: DragEvent) => void}  props.onDrop      Called when dropping in the zone.
 *
 * @return {import('react').RefCallback<HTMLElement>} Ref callback to be passed to the drop zone element.
 */


function useDropZone(_ref) {
  let {
    isDisabled,
    onDrop: _onDrop,
    onDragStart: _onDragStart,
    onDragEnter: _onDragEnter,
    onDragLeave: _onDragLeave,
    onDragEnd: _onDragEnd,
    onDragOver: _onDragOver
  } = _ref;
  const onDropRef = useFreshRef(_onDrop);
  const onDragStartRef = useFreshRef(_onDragStart);
  const onDragEnterRef = useFreshRef(_onDragEnter);
  const onDragLeaveRef = useFreshRef(_onDragLeave);
  const onDragEndRef = useFreshRef(_onDragEnd);
  const onDragOverRef = useFreshRef(_onDragOver);
  return useRefEffect(element => {
    if (isDisabled) {
      return;
    }

    let isDragging = false;
    const {
      ownerDocument
    } = element;
    /**
     * Checks if an element is in the drop zone.
     *
     * @param {EventTarget|null} targetToCheck
     *
     * @return {boolean} True if in drop zone, false if not.
     */

    function isElementInZone(targetToCheck) {
      const {
        defaultView
      } = ownerDocument;

      if (!targetToCheck || !defaultView || !(targetToCheck instanceof defaultView.HTMLElement) || !element.contains(targetToCheck)) {
        return false;
      }
      /** @type {HTMLElement|null} */


      let elementToCheck = targetToCheck;

      do {
        if (elementToCheck.dataset.isDropZone) {
          return elementToCheck === element;
        }
      } while (elementToCheck = elementToCheck.parentElement);

      return false;
    }

    function maybeDragStart(
    /** @type {DragEvent} */
    event) {
      if (isDragging) {
        return;
      }

      isDragging = true;
      ownerDocument.removeEventListener('dragenter', maybeDragStart); // Note that `dragend` doesn't fire consistently for file and
      // HTML drag events where the drag origin is outside the browser
      // window. In Firefox it may also not fire if the originating
      // node is removed.

      ownerDocument.addEventListener('dragend', maybeDragEnd);
      ownerDocument.addEventListener('mousemove', maybeDragEnd);

      if (onDragStartRef.current) {
        onDragStartRef.current(event);
      }
    }

    function onDragEnter(
    /** @type {DragEvent} */
    event) {
      event.preventDefault(); // The `dragenter` event will also fire when entering child
      // elements, but we only want to call `onDragEnter` when
      // entering the drop zone, which means the `relatedTarget`
      // (element that has been left) should be outside the drop zone.

      if (element.contains(
      /** @type {Node} */
      event.relatedTarget)) {
        return;
      }

      if (onDragEnterRef.current) {
        onDragEnterRef.current(event);
      }
    }

    function onDragOver(
    /** @type {DragEvent} */
    event) {
      // Only call onDragOver for the innermost hovered drop zones.
      if (!event.defaultPrevented && onDragOverRef.current) {
        onDragOverRef.current(event);
      } // Prevent the browser default while also signalling to parent
      // drop zones that `onDragOver` is already handled.


      event.preventDefault();
    }

    function onDragLeave(
    /** @type {DragEvent} */
    event) {
      // The `dragleave` event will also fire when leaving child
      // elements, but we only want to call `onDragLeave` when
      // leaving the drop zone, which means the `relatedTarget`
      // (element that has been entered) should be outside the drop
      // zone.
      if (isElementInZone(event.relatedTarget)) {
        return;
      }

      if (onDragLeaveRef.current) {
        onDragLeaveRef.current(event);
      }
    }

    function onDrop(
    /** @type {DragEvent} */
    event) {
      // Don't handle drop if an inner drop zone already handled it.
      if (event.defaultPrevented) {
        return;
      } // Prevent the browser default while also signalling to parent
      // drop zones that `onDrop` is already handled.


      event.preventDefault(); // This seemingly useless line has been shown to resolve a
      // Safari issue where files dragged directly from the dock are
      // not recognized.
      // eslint-disable-next-line no-unused-expressions

      event.dataTransfer && event.dataTransfer.files.length;

      if (onDropRef.current) {
        onDropRef.current(event);
      }

      maybeDragEnd(event);
    }

    function maybeDragEnd(
    /** @type {MouseEvent} */
    event) {
      if (!isDragging) {
        return;
      }

      isDragging = false;
      ownerDocument.addEventListener('dragenter', maybeDragStart);
      ownerDocument.removeEventListener('dragend', maybeDragEnd);
      ownerDocument.removeEventListener('mousemove', maybeDragEnd);

      if (onDragEndRef.current) {
        onDragEndRef.current(event);
      }
    }

    element.dataset.isDropZone = 'true';
    element.addEventListener('drop', onDrop);
    element.addEventListener('dragenter', onDragEnter);
    element.addEventListener('dragover', onDragOver);
    element.addEventListener('dragleave', onDragLeave); // The `dragstart` event doesn't fire if the drag started outside
    // the document.

    ownerDocument.addEventListener('dragenter', maybeDragStart);
    return () => {
      delete element.dataset.isDropZone;
      element.removeEventListener('drop', onDrop);
      element.removeEventListener('dragenter', onDragEnter);
      element.removeEventListener('dragover', onDragOver);
      element.removeEventListener('dragleave', onDragLeave);
      ownerDocument.removeEventListener('dragend', maybeDragEnd);
      ownerDocument.removeEventListener('mousemove', maybeDragEnd);
      ownerDocument.addEventListener('dragenter', maybeDragStart);
    };
  }, [isDisabled]);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-focusable-iframe/index.js
/**
 * Internal dependencies
 */

/**
 * Dispatches a bubbling focus event when the iframe receives focus. Use
 * `onFocus` as usual on the iframe or a parent element.
 *
 * @return {Object} Ref to pass to the iframe.
 */

function useFocusableIframe() {
  return useRefEffect(element => {
    const {
      ownerDocument
    } = element;
    if (!ownerDocument) return;
    const {
      defaultView
    } = ownerDocument;
    if (!defaultView) return;
    /**
     * Checks whether the iframe is the activeElement, inferring that it has
     * then received focus, and dispatches a focus event.
     */

    function checkFocus() {
      if (ownerDocument && ownerDocument.activeElement === element) {
        /** @type {HTMLElement} */
        element.focus();
      }
    }

    defaultView.addEventListener('blur', checkFocus);
    return () => {
      defaultView.removeEventListener('blur', checkFocus);
    };
  }, []);
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/hooks/use-fixed-window-list/index.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */




const DEFAULT_INIT_WINDOW_SIZE = 30;
/**
 * @typedef {Object} WPFixedWindowList
 *
 * @property {number}                  visibleItems Items visible in the current viewport
 * @property {number}                  start        Start index of the window
 * @property {number}                  end          End index of the window
 * @property {(index:number)=>boolean} itemInView   Returns true if item is in the window
 */

/**
 * @typedef {Object} WPFixedWindowListOptions
 *
 * @property {number}  [windowOverscan] Renders windowOverscan number of items before and after the calculated visible window.
 * @property {boolean} [useWindowing]   When false avoids calculating the window size
 * @property {number}  [initWindowSize] Initial window size to use on first render before we can calculate the window size.
 */

/**
 *
 * @param {import('react').RefObject<HTMLElement>} elementRef Used to find the closest scroll container that contains element.
 * @param { number }                               itemHeight Fixed item height in pixels
 * @param { number }                               totalItems Total items in list
 * @param { WPFixedWindowListOptions }             [options]  Options object
 * @return {[ WPFixedWindowList, setFixedListWindow:(nextWindow:WPFixedWindowList)=>void]} Array with the fixed window list and setter
 */

function useFixedWindowList(elementRef, itemHeight, totalItems, options) {
  var _options$initWindowSi, _options$useWindowing;

  const initWindowSize = (_options$initWindowSi = options === null || options === void 0 ? void 0 : options.initWindowSize) !== null && _options$initWindowSi !== void 0 ? _options$initWindowSi : DEFAULT_INIT_WINDOW_SIZE;
  const useWindowing = (_options$useWindowing = options === null || options === void 0 ? void 0 : options.useWindowing) !== null && _options$useWindowing !== void 0 ? _options$useWindowing : true;
  const [fixedListWindow, setFixedListWindow] = Object(external_wp_element_["useState"])({
    visibleItems: initWindowSize,
    start: 0,
    end: initWindowSize,
    itemInView: (
    /** @type {number} */
    index) => {
      return index >= 0 && index <= initWindowSize;
    }
  });
  Object(external_wp_element_["useLayoutEffect"])(() => {
    var _scrollContainer$owne, _scrollContainer$owne2, _scrollContainer$owne3, _scrollContainer$owne4;

    if (!useWindowing) {
      return;
    }

    const scrollContainer = Object(external_wp_dom_["getScrollContainer"])(elementRef.current);

    const measureWindow = (
    /** @type {boolean | undefined} */
    initRender) => {
      var _options$windowOversc;

      if (!scrollContainer) {
        return;
      }

      const visibleItems = Math.ceil(scrollContainer.clientHeight / itemHeight); // Aim to keep opening list view fast, afterward we can optimize for scrolling

      const windowOverscan = initRender ? visibleItems : (_options$windowOversc = options === null || options === void 0 ? void 0 : options.windowOverscan) !== null && _options$windowOversc !== void 0 ? _options$windowOversc : visibleItems;
      const firstViewableIndex = Math.floor(scrollContainer.scrollTop / itemHeight);
      const start = Math.max(0, firstViewableIndex - windowOverscan);
      const end = Math.min(totalItems - 1, firstViewableIndex + visibleItems + windowOverscan);
      setFixedListWindow(lastWindow => {
        const nextWindow = {
          visibleItems,
          start,
          end,
          itemInView: (
          /** @type {number} */
          index) => {
            return start <= index && index <= end;
          }
        };

        if (lastWindow.start !== nextWindow.start || lastWindow.end !== nextWindow.end || lastWindow.visibleItems !== nextWindow.visibleItems) {
          return nextWindow;
        }

        return lastWindow;
      });
    };

    measureWindow(true);
    const debounceMeasureList = Object(external_lodash_["debounce"])(() => {
      measureWindow();
    }, 16);
    scrollContainer === null || scrollContainer === void 0 ? void 0 : scrollContainer.addEventListener('scroll', debounceMeasureList);
    scrollContainer === null || scrollContainer === void 0 ? void 0 : (_scrollContainer$owne = scrollContainer.ownerDocument) === null || _scrollContainer$owne === void 0 ? void 0 : (_scrollContainer$owne2 = _scrollContainer$owne.defaultView) === null || _scrollContainer$owne2 === void 0 ? void 0 : _scrollContainer$owne2.addEventListener('resize', debounceMeasureList);
    scrollContainer === null || scrollContainer === void 0 ? void 0 : (_scrollContainer$owne3 = scrollContainer.ownerDocument) === null || _scrollContainer$owne3 === void 0 ? void 0 : (_scrollContainer$owne4 = _scrollContainer$owne3.defaultView) === null || _scrollContainer$owne4 === void 0 ? void 0 : _scrollContainer$owne4.addEventListener('resize', debounceMeasureList);
    return () => {
      var _scrollContainer$owne5, _scrollContainer$owne6;

      scrollContainer === null || scrollContainer === void 0 ? void 0 : scrollContainer.removeEventListener('scroll', debounceMeasureList);
      scrollContainer === null || scrollContainer === void 0 ? void 0 : (_scrollContainer$owne5 = scrollContainer.ownerDocument) === null || _scrollContainer$owne5 === void 0 ? void 0 : (_scrollContainer$owne6 = _scrollContainer$owne5.defaultView) === null || _scrollContainer$owne6 === void 0 ? void 0 : _scrollContainer$owne6.removeEventListener('resize', debounceMeasureList);
    };
  }, [itemHeight, elementRef, totalItems]);
  Object(external_wp_element_["useLayoutEffect"])(() => {
    var _scrollContainer$owne7, _scrollContainer$owne8;

    if (!useWindowing) {
      return;
    }

    const scrollContainer = Object(external_wp_dom_["getScrollContainer"])(elementRef.current);

    const handleKeyDown = (
    /** @type {KeyboardEvent} */
    event) => {
      switch (event.keyCode) {
        case external_wp_keycodes_["HOME"]:
          {
            return scrollContainer === null || scrollContainer === void 0 ? void 0 : scrollContainer.scrollTo({
              top: 0
            });
          }

        case external_wp_keycodes_["END"]:
          {
            return scrollContainer === null || scrollContainer === void 0 ? void 0 : scrollContainer.scrollTo({
              top: totalItems * itemHeight
            });
          }

        case external_wp_keycodes_["PAGEUP"]:
          {
            return scrollContainer === null || scrollContainer === void 0 ? void 0 : scrollContainer.scrollTo({
              top: scrollContainer.scrollTop - fixedListWindow.visibleItems * itemHeight
            });
          }

        case external_wp_keycodes_["PAGEDOWN"]:
          {
            return scrollContainer === null || scrollContainer === void 0 ? void 0 : scrollContainer.scrollTo({
              top: scrollContainer.scrollTop + fixedListWindow.visibleItems * itemHeight
            });
          }
      }
    };

    scrollContainer === null || scrollContainer === void 0 ? void 0 : (_scrollContainer$owne7 = scrollContainer.ownerDocument) === null || _scrollContainer$owne7 === void 0 ? void 0 : (_scrollContainer$owne8 = _scrollContainer$owne7.defaultView) === null || _scrollContainer$owne8 === void 0 ? void 0 : _scrollContainer$owne8.addEventListener('keydown', handleKeyDown);
    return () => {
      var _scrollContainer$owne9, _scrollContainer$owne10;

      scrollContainer === null || scrollContainer === void 0 ? void 0 : (_scrollContainer$owne9 = scrollContainer.ownerDocument) === null || _scrollContainer$owne9 === void 0 ? void 0 : (_scrollContainer$owne10 = _scrollContainer$owne9.defaultView) === null || _scrollContainer$owne10 === void 0 ? void 0 : _scrollContainer$owne10.removeEventListener('keydown', handleKeyDown);
    };
  }, [totalItems, itemHeight, elementRef, fixedListWindow.visibleItems]);
  return [fixedListWindow, setFixedListWindow];
}

// CONCATENATED MODULE: ./node_modules/@wordpress/compose/build-module/index.js
// Utils
 // Compose helper (aliased flowRight from Lodash)

 // Higher-order components






 // Hooks





























/***/ }),

/***/ "RxS6":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["keycodes"]; }());

/***/ }),

/***/ "SSiF":
/***/ (function(module, exports, __webpack_require__) {

var e=__webpack_require__("cDcd"),n={display:"block",opacity:0,position:"absolute",top:0,left:0,height:"100%",width:"100%",overflow:"hidden",pointerEvents:"none",zIndex:-1},t=function(t){var r=t.onResize,u=e.useRef();return function(n,t){var r=function(){return n.current&&n.current.contentDocument&&n.current.contentDocument.defaultView};function u(){t();var e=r();e&&e.addEventListener("resize",t)}e.useEffect((function(){return r()?u():n.current&&n.current.addEventListener&&n.current.addEventListener("load",u),function(){var e=r();e&&"function"==typeof e.removeEventListener&&e.removeEventListener("resize",t)}}),[])}(u,(function(){return r(u)})),e.createElement("iframe",{style:n,src:"about:blank",ref:u,"aria-hidden":!0,tabIndex:-1,frameBorder:0})},r=function(e){return{width:null!=e?e.offsetWidth:null,height:null!=e?e.offsetHeight:null}};module.exports=function(n){void 0===n&&(n=r);var u=e.useState(n(null)),o=u[0],i=u[1],c=e.useCallback((function(e){return i(n(e.current))}),[n]);return[e.useMemo((function(){return e.createElement(t,{onResize:c})}),[c]),o]};


/***/ }),

/***/ "VcSt":
/***/ (function(module, exports) {

/**
 * adds a bindGlobal method to Mousetrap that allows you to
 * bind specific keyboard shortcuts that will still work
 * inside a text input field
 *
 * usage:
 * Mousetrap.bindGlobal('ctrl+s', _saveChanges);
 */
/* global Mousetrap:true */
(function(Mousetrap) {
    if (! Mousetrap) {
        return;
    }
    var _globalCallbacks = {};
    var _originalStopCallback = Mousetrap.prototype.stopCallback;

    Mousetrap.prototype.stopCallback = function(e, element, combo, sequence) {
        var self = this;

        if (self.paused) {
            return true;
        }

        if (_globalCallbacks[combo] || _globalCallbacks[sequence]) {
            return false;
        }

        return _originalStopCallback.call(self, e, element, combo);
    };

    Mousetrap.prototype.bindGlobal = function(keys, callback, action) {
        var self = this;
        self.bind(keys, callback, action);

        if (keys instanceof Array) {
            for (var i = 0; i < keys.length; i++) {
                _globalCallbacks[keys[i]] = true;
            }
            return;
        }

        _globalCallbacks[keys] = true;
    };

    Mousetrap.init();
}) (typeof Mousetrap !== "undefined" ? Mousetrap : undefined);


/***/ }),

/***/ "XI5e":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["priorityQueue"]; }());

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "cDcd":
/***/ (function(module, exports) {

(function() { module.exports = window["React"]; }());

/***/ }),

/***/ "imBb":
/***/ (function(module, exports, __webpack_require__) {

var __WEBPACK_AMD_DEFINE_RESULT__;/*global define:false */
/**
 * Copyright 2012-2017 Craig Campbell
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * Mousetrap is a simple keyboard shortcut library for Javascript with
 * no external dependencies
 *
 * @version 1.6.5
 * @url craig.is/killing/mice
 */
(function(window, document, undefined) {

    // Check if mousetrap is used inside browser, if not, return
    if (!window) {
        return;
    }

    /**
     * mapping of special keycodes to their corresponding keys
     *
     * everything in this dictionary cannot use keypress events
     * so it has to be here to map to the correct keycodes for
     * keyup/keydown events
     *
     * @type {Object}
     */
    var _MAP = {
        8: 'backspace',
        9: 'tab',
        13: 'enter',
        16: 'shift',
        17: 'ctrl',
        18: 'alt',
        20: 'capslock',
        27: 'esc',
        32: 'space',
        33: 'pageup',
        34: 'pagedown',
        35: 'end',
        36: 'home',
        37: 'left',
        38: 'up',
        39: 'right',
        40: 'down',
        45: 'ins',
        46: 'del',
        91: 'meta',
        93: 'meta',
        224: 'meta'
    };

    /**
     * mapping for special characters so they can support
     *
     * this dictionary is only used incase you want to bind a
     * keyup or keydown event to one of these keys
     *
     * @type {Object}
     */
    var _KEYCODE_MAP = {
        106: '*',
        107: '+',
        109: '-',
        110: '.',
        111 : '/',
        186: ';',
        187: '=',
        188: ',',
        189: '-',
        190: '.',
        191: '/',
        192: '`',
        219: '[',
        220: '\\',
        221: ']',
        222: '\''
    };

    /**
     * this is a mapping of keys that require shift on a US keypad
     * back to the non shift equivelents
     *
     * this is so you can use keyup events with these keys
     *
     * note that this will only work reliably on US keyboards
     *
     * @type {Object}
     */
    var _SHIFT_MAP = {
        '~': '`',
        '!': '1',
        '@': '2',
        '#': '3',
        '$': '4',
        '%': '5',
        '^': '6',
        '&': '7',
        '*': '8',
        '(': '9',
        ')': '0',
        '_': '-',
        '+': '=',
        ':': ';',
        '\"': '\'',
        '<': ',',
        '>': '.',
        '?': '/',
        '|': '\\'
    };

    /**
     * this is a list of special strings you can use to map
     * to modifier keys when you specify your keyboard shortcuts
     *
     * @type {Object}
     */
    var _SPECIAL_ALIASES = {
        'option': 'alt',
        'command': 'meta',
        'return': 'enter',
        'escape': 'esc',
        'plus': '+',
        'mod': /Mac|iPod|iPhone|iPad/.test(navigator.platform) ? 'meta' : 'ctrl'
    };

    /**
     * variable to store the flipped version of _MAP from above
     * needed to check if we should use keypress or not when no action
     * is specified
     *
     * @type {Object|undefined}
     */
    var _REVERSE_MAP;

    /**
     * loop through the f keys, f1 to f19 and add them to the map
     * programatically
     */
    for (var i = 1; i < 20; ++i) {
        _MAP[111 + i] = 'f' + i;
    }

    /**
     * loop through to map numbers on the numeric keypad
     */
    for (i = 0; i <= 9; ++i) {

        // This needs to use a string cause otherwise since 0 is falsey
        // mousetrap will never fire for numpad 0 pressed as part of a keydown
        // event.
        //
        // @see https://github.com/ccampbell/mousetrap/pull/258
        _MAP[i + 96] = i.toString();
    }

    /**
     * cross browser add event method
     *
     * @param {Element|HTMLDocument} object
     * @param {string} type
     * @param {Function} callback
     * @returns void
     */
    function _addEvent(object, type, callback) {
        if (object.addEventListener) {
            object.addEventListener(type, callback, false);
            return;
        }

        object.attachEvent('on' + type, callback);
    }

    /**
     * takes the event and returns the key character
     *
     * @param {Event} e
     * @return {string}
     */
    function _characterFromEvent(e) {

        // for keypress events we should return the character as is
        if (e.type == 'keypress') {
            var character = String.fromCharCode(e.which);

            // if the shift key is not pressed then it is safe to assume
            // that we want the character to be lowercase.  this means if
            // you accidentally have caps lock on then your key bindings
            // will continue to work
            //
            // the only side effect that might not be desired is if you
            // bind something like 'A' cause you want to trigger an
            // event when capital A is pressed caps lock will no longer
            // trigger the event.  shift+a will though.
            if (!e.shiftKey) {
                character = character.toLowerCase();
            }

            return character;
        }

        // for non keypress events the special maps are needed
        if (_MAP[e.which]) {
            return _MAP[e.which];
        }

        if (_KEYCODE_MAP[e.which]) {
            return _KEYCODE_MAP[e.which];
        }

        // if it is not in the special map

        // with keydown and keyup events the character seems to always
        // come in as an uppercase character whether you are pressing shift
        // or not.  we should make sure it is always lowercase for comparisons
        return String.fromCharCode(e.which).toLowerCase();
    }

    /**
     * checks if two arrays are equal
     *
     * @param {Array} modifiers1
     * @param {Array} modifiers2
     * @returns {boolean}
     */
    function _modifiersMatch(modifiers1, modifiers2) {
        return modifiers1.sort().join(',') === modifiers2.sort().join(',');
    }

    /**
     * takes a key event and figures out what the modifiers are
     *
     * @param {Event} e
     * @returns {Array}
     */
    function _eventModifiers(e) {
        var modifiers = [];

        if (e.shiftKey) {
            modifiers.push('shift');
        }

        if (e.altKey) {
            modifiers.push('alt');
        }

        if (e.ctrlKey) {
            modifiers.push('ctrl');
        }

        if (e.metaKey) {
            modifiers.push('meta');
        }

        return modifiers;
    }

    /**
     * prevents default for this event
     *
     * @param {Event} e
     * @returns void
     */
    function _preventDefault(e) {
        if (e.preventDefault) {
            e.preventDefault();
            return;
        }

        e.returnValue = false;
    }

    /**
     * stops propogation for this event
     *
     * @param {Event} e
     * @returns void
     */
    function _stopPropagation(e) {
        if (e.stopPropagation) {
            e.stopPropagation();
            return;
        }

        e.cancelBubble = true;
    }

    /**
     * determines if the keycode specified is a modifier key or not
     *
     * @param {string} key
     * @returns {boolean}
     */
    function _isModifier(key) {
        return key == 'shift' || key == 'ctrl' || key == 'alt' || key == 'meta';
    }

    /**
     * reverses the map lookup so that we can look for specific keys
     * to see what can and can't use keypress
     *
     * @return {Object}
     */
    function _getReverseMap() {
        if (!_REVERSE_MAP) {
            _REVERSE_MAP = {};
            for (var key in _MAP) {

                // pull out the numeric keypad from here cause keypress should
                // be able to detect the keys from the character
                if (key > 95 && key < 112) {
                    continue;
                }

                if (_MAP.hasOwnProperty(key)) {
                    _REVERSE_MAP[_MAP[key]] = key;
                }
            }
        }
        return _REVERSE_MAP;
    }

    /**
     * picks the best action based on the key combination
     *
     * @param {string} key - character for key
     * @param {Array} modifiers
     * @param {string=} action passed in
     */
    function _pickBestAction(key, modifiers, action) {

        // if no action was picked in we should try to pick the one
        // that we think would work best for this key
        if (!action) {
            action = _getReverseMap()[key] ? 'keydown' : 'keypress';
        }

        // modifier keys don't work as expected with keypress,
        // switch to keydown
        if (action == 'keypress' && modifiers.length) {
            action = 'keydown';
        }

        return action;
    }

    /**
     * Converts from a string key combination to an array
     *
     * @param  {string} combination like "command+shift+l"
     * @return {Array}
     */
    function _keysFromString(combination) {
        if (combination === '+') {
            return ['+'];
        }

        combination = combination.replace(/\+{2}/g, '+plus');
        return combination.split('+');
    }

    /**
     * Gets info for a specific key combination
     *
     * @param  {string} combination key combination ("command+s" or "a" or "*")
     * @param  {string=} action
     * @returns {Object}
     */
    function _getKeyInfo(combination, action) {
        var keys;
        var key;
        var i;
        var modifiers = [];

        // take the keys from this pattern and figure out what the actual
        // pattern is all about
        keys = _keysFromString(combination);

        for (i = 0; i < keys.length; ++i) {
            key = keys[i];

            // normalize key names
            if (_SPECIAL_ALIASES[key]) {
                key = _SPECIAL_ALIASES[key];
            }

            // if this is not a keypress event then we should
            // be smart about using shift keys
            // this will only work for US keyboards however
            if (action && action != 'keypress' && _SHIFT_MAP[key]) {
                key = _SHIFT_MAP[key];
                modifiers.push('shift');
            }

            // if this key is a modifier then add it to the list of modifiers
            if (_isModifier(key)) {
                modifiers.push(key);
            }
        }

        // depending on what the key combination is
        // we will try to pick the best event for it
        action = _pickBestAction(key, modifiers, action);

        return {
            key: key,
            modifiers: modifiers,
            action: action
        };
    }

    function _belongsTo(element, ancestor) {
        if (element === null || element === document) {
            return false;
        }

        if (element === ancestor) {
            return true;
        }

        return _belongsTo(element.parentNode, ancestor);
    }

    function Mousetrap(targetElement) {
        var self = this;

        targetElement = targetElement || document;

        if (!(self instanceof Mousetrap)) {
            return new Mousetrap(targetElement);
        }

        /**
         * element to attach key events to
         *
         * @type {Element}
         */
        self.target = targetElement;

        /**
         * a list of all the callbacks setup via Mousetrap.bind()
         *
         * @type {Object}
         */
        self._callbacks = {};

        /**
         * direct map of string combinations to callbacks used for trigger()
         *
         * @type {Object}
         */
        self._directMap = {};

        /**
         * keeps track of what level each sequence is at since multiple
         * sequences can start out with the same sequence
         *
         * @type {Object}
         */
        var _sequenceLevels = {};

        /**
         * variable to store the setTimeout call
         *
         * @type {null|number}
         */
        var _resetTimer;

        /**
         * temporary state where we will ignore the next keyup
         *
         * @type {boolean|string}
         */
        var _ignoreNextKeyup = false;

        /**
         * temporary state where we will ignore the next keypress
         *
         * @type {boolean}
         */
        var _ignoreNextKeypress = false;

        /**
         * are we currently inside of a sequence?
         * type of action ("keyup" or "keydown" or "keypress") or false
         *
         * @type {boolean|string}
         */
        var _nextExpectedAction = false;

        /**
         * resets all sequence counters except for the ones passed in
         *
         * @param {Object} doNotReset
         * @returns void
         */
        function _resetSequences(doNotReset) {
            doNotReset = doNotReset || {};

            var activeSequences = false,
                key;

            for (key in _sequenceLevels) {
                if (doNotReset[key]) {
                    activeSequences = true;
                    continue;
                }
                _sequenceLevels[key] = 0;
            }

            if (!activeSequences) {
                _nextExpectedAction = false;
            }
        }

        /**
         * finds all callbacks that match based on the keycode, modifiers,
         * and action
         *
         * @param {string} character
         * @param {Array} modifiers
         * @param {Event|Object} e
         * @param {string=} sequenceName - name of the sequence we are looking for
         * @param {string=} combination
         * @param {number=} level
         * @returns {Array}
         */
        function _getMatches(character, modifiers, e, sequenceName, combination, level) {
            var i;
            var callback;
            var matches = [];
            var action = e.type;

            // if there are no events related to this keycode
            if (!self._callbacks[character]) {
                return [];
            }

            // if a modifier key is coming up on its own we should allow it
            if (action == 'keyup' && _isModifier(character)) {
                modifiers = [character];
            }

            // loop through all callbacks for the key that was pressed
            // and see if any of them match
            for (i = 0; i < self._callbacks[character].length; ++i) {
                callback = self._callbacks[character][i];

                // if a sequence name is not specified, but this is a sequence at
                // the wrong level then move onto the next match
                if (!sequenceName && callback.seq && _sequenceLevels[callback.seq] != callback.level) {
                    continue;
                }

                // if the action we are looking for doesn't match the action we got
                // then we should keep going
                if (action != callback.action) {
                    continue;
                }

                // if this is a keypress event and the meta key and control key
                // are not pressed that means that we need to only look at the
                // character, otherwise check the modifiers as well
                //
                // chrome will not fire a keypress if meta or control is down
                // safari will fire a keypress if meta or meta+shift is down
                // firefox will fire a keypress if meta or control is down
                if ((action == 'keypress' && !e.metaKey && !e.ctrlKey) || _modifiersMatch(modifiers, callback.modifiers)) {

                    // when you bind a combination or sequence a second time it
                    // should overwrite the first one.  if a sequenceName or
                    // combination is specified in this call it does just that
                    //
                    // @todo make deleting its own method?
                    var deleteCombo = !sequenceName && callback.combo == combination;
                    var deleteSequence = sequenceName && callback.seq == sequenceName && callback.level == level;
                    if (deleteCombo || deleteSequence) {
                        self._callbacks[character].splice(i, 1);
                    }

                    matches.push(callback);
                }
            }

            return matches;
        }

        /**
         * actually calls the callback function
         *
         * if your callback function returns false this will use the jquery
         * convention - prevent default and stop propogation on the event
         *
         * @param {Function} callback
         * @param {Event} e
         * @returns void
         */
        function _fireCallback(callback, e, combo, sequence) {

            // if this event should not happen stop here
            if (self.stopCallback(e, e.target || e.srcElement, combo, sequence)) {
                return;
            }

            if (callback(e, combo) === false) {
                _preventDefault(e);
                _stopPropagation(e);
            }
        }

        /**
         * handles a character key event
         *
         * @param {string} character
         * @param {Array} modifiers
         * @param {Event} e
         * @returns void
         */
        self._handleKey = function(character, modifiers, e) {
            var callbacks = _getMatches(character, modifiers, e);
            var i;
            var doNotReset = {};
            var maxLevel = 0;
            var processedSequenceCallback = false;

            // Calculate the maxLevel for sequences so we can only execute the longest callback sequence
            for (i = 0; i < callbacks.length; ++i) {
                if (callbacks[i].seq) {
                    maxLevel = Math.max(maxLevel, callbacks[i].level);
                }
            }

            // loop through matching callbacks for this key event
            for (i = 0; i < callbacks.length; ++i) {

                // fire for all sequence callbacks
                // this is because if for example you have multiple sequences
                // bound such as "g i" and "g t" they both need to fire the
                // callback for matching g cause otherwise you can only ever
                // match the first one
                if (callbacks[i].seq) {

                    // only fire callbacks for the maxLevel to prevent
                    // subsequences from also firing
                    //
                    // for example 'a option b' should not cause 'option b' to fire
                    // even though 'option b' is part of the other sequence
                    //
                    // any sequences that do not match here will be discarded
                    // below by the _resetSequences call
                    if (callbacks[i].level != maxLevel) {
                        continue;
                    }

                    processedSequenceCallback = true;

                    // keep a list of which sequences were matches for later
                    doNotReset[callbacks[i].seq] = 1;
                    _fireCallback(callbacks[i].callback, e, callbacks[i].combo, callbacks[i].seq);
                    continue;
                }

                // if there were no sequence matches but we are still here
                // that means this is a regular match so we should fire that
                if (!processedSequenceCallback) {
                    _fireCallback(callbacks[i].callback, e, callbacks[i].combo);
                }
            }

            // if the key you pressed matches the type of sequence without
            // being a modifier (ie "keyup" or "keypress") then we should
            // reset all sequences that were not matched by this event
            //
            // this is so, for example, if you have the sequence "h a t" and you
            // type "h e a r t" it does not match.  in this case the "e" will
            // cause the sequence to reset
            //
            // modifier keys are ignored because you can have a sequence
            // that contains modifiers such as "enter ctrl+space" and in most
            // cases the modifier key will be pressed before the next key
            //
            // also if you have a sequence such as "ctrl+b a" then pressing the
            // "b" key will trigger a "keypress" and a "keydown"
            //
            // the "keydown" is expected when there is a modifier, but the
            // "keypress" ends up matching the _nextExpectedAction since it occurs
            // after and that causes the sequence to reset
            //
            // we ignore keypresses in a sequence that directly follow a keydown
            // for the same character
            var ignoreThisKeypress = e.type == 'keypress' && _ignoreNextKeypress;
            if (e.type == _nextExpectedAction && !_isModifier(character) && !ignoreThisKeypress) {
                _resetSequences(doNotReset);
            }

            _ignoreNextKeypress = processedSequenceCallback && e.type == 'keydown';
        };

        /**
         * handles a keydown event
         *
         * @param {Event} e
         * @returns void
         */
        function _handleKeyEvent(e) {

            // normalize e.which for key events
            // @see http://stackoverflow.com/questions/4285627/javascript-keycode-vs-charcode-utter-confusion
            if (typeof e.which !== 'number') {
                e.which = e.keyCode;
            }

            var character = _characterFromEvent(e);

            // no character found then stop
            if (!character) {
                return;
            }

            // need to use === for the character check because the character can be 0
            if (e.type == 'keyup' && _ignoreNextKeyup === character) {
                _ignoreNextKeyup = false;
                return;
            }

            self.handleKey(character, _eventModifiers(e), e);
        }

        /**
         * called to set a 1 second timeout on the specified sequence
         *
         * this is so after each key press in the sequence you have 1 second
         * to press the next key before you have to start over
         *
         * @returns void
         */
        function _resetSequenceTimer() {
            clearTimeout(_resetTimer);
            _resetTimer = setTimeout(_resetSequences, 1000);
        }

        /**
         * binds a key sequence to an event
         *
         * @param {string} combo - combo specified in bind call
         * @param {Array} keys
         * @param {Function} callback
         * @param {string=} action
         * @returns void
         */
        function _bindSequence(combo, keys, callback, action) {

            // start off by adding a sequence level record for this combination
            // and setting the level to 0
            _sequenceLevels[combo] = 0;

            /**
             * callback to increase the sequence level for this sequence and reset
             * all other sequences that were active
             *
             * @param {string} nextAction
             * @returns {Function}
             */
            function _increaseSequence(nextAction) {
                return function() {
                    _nextExpectedAction = nextAction;
                    ++_sequenceLevels[combo];
                    _resetSequenceTimer();
                };
            }

            /**
             * wraps the specified callback inside of another function in order
             * to reset all sequence counters as soon as this sequence is done
             *
             * @param {Event} e
             * @returns void
             */
            function _callbackAndReset(e) {
                _fireCallback(callback, e, combo);

                // we should ignore the next key up if the action is key down
                // or keypress.  this is so if you finish a sequence and
                // release the key the final key will not trigger a keyup
                if (action !== 'keyup') {
                    _ignoreNextKeyup = _characterFromEvent(e);
                }

                // weird race condition if a sequence ends with the key
                // another sequence begins with
                setTimeout(_resetSequences, 10);
            }

            // loop through keys one at a time and bind the appropriate callback
            // function.  for any key leading up to the final one it should
            // increase the sequence. after the final, it should reset all sequences
            //
            // if an action is specified in the original bind call then that will
            // be used throughout.  otherwise we will pass the action that the
            // next key in the sequence should match.  this allows a sequence
            // to mix and match keypress and keydown events depending on which
            // ones are better suited to the key provided
            for (var i = 0; i < keys.length; ++i) {
                var isFinal = i + 1 === keys.length;
                var wrappedCallback = isFinal ? _callbackAndReset : _increaseSequence(action || _getKeyInfo(keys[i + 1]).action);
                _bindSingle(keys[i], wrappedCallback, action, combo, i);
            }
        }

        /**
         * binds a single keyboard combination
         *
         * @param {string} combination
         * @param {Function} callback
         * @param {string=} action
         * @param {string=} sequenceName - name of sequence if part of sequence
         * @param {number=} level - what part of the sequence the command is
         * @returns void
         */
        function _bindSingle(combination, callback, action, sequenceName, level) {

            // store a direct mapped reference for use with Mousetrap.trigger
            self._directMap[combination + ':' + action] = callback;

            // make sure multiple spaces in a row become a single space
            combination = combination.replace(/\s+/g, ' ');

            var sequence = combination.split(' ');
            var info;

            // if this pattern is a sequence of keys then run through this method
            // to reprocess each pattern one key at a time
            if (sequence.length > 1) {
                _bindSequence(combination, sequence, callback, action);
                return;
            }

            info = _getKeyInfo(combination, action);

            // make sure to initialize array if this is the first time
            // a callback is added for this key
            self._callbacks[info.key] = self._callbacks[info.key] || [];

            // remove an existing match if there is one
            _getMatches(info.key, info.modifiers, {type: info.action}, sequenceName, combination, level);

            // add this call back to the array
            // if it is a sequence put it at the beginning
            // if not put it at the end
            //
            // this is important because the way these are processed expects
            // the sequence ones to come first
            self._callbacks[info.key][sequenceName ? 'unshift' : 'push']({
                callback: callback,
                modifiers: info.modifiers,
                action: info.action,
                seq: sequenceName,
                level: level,
                combo: combination
            });
        }

        /**
         * binds multiple combinations to the same callback
         *
         * @param {Array} combinations
         * @param {Function} callback
         * @param {string|undefined} action
         * @returns void
         */
        self._bindMultiple = function(combinations, callback, action) {
            for (var i = 0; i < combinations.length; ++i) {
                _bindSingle(combinations[i], callback, action);
            }
        };

        // start!
        _addEvent(targetElement, 'keypress', _handleKeyEvent);
        _addEvent(targetElement, 'keydown', _handleKeyEvent);
        _addEvent(targetElement, 'keyup', _handleKeyEvent);
    }

    /**
     * binds an event to mousetrap
     *
     * can be a single key, a combination of keys separated with +,
     * an array of keys, or a sequence of keys separated by spaces
     *
     * be sure to list the modifier keys first to make sure that the
     * correct key ends up getting bound (the last key in the pattern)
     *
     * @param {string|Array} keys
     * @param {Function} callback
     * @param {string=} action - 'keypress', 'keydown', or 'keyup'
     * @returns void
     */
    Mousetrap.prototype.bind = function(keys, callback, action) {
        var self = this;
        keys = keys instanceof Array ? keys : [keys];
        self._bindMultiple.call(self, keys, callback, action);
        return self;
    };

    /**
     * unbinds an event to mousetrap
     *
     * the unbinding sets the callback function of the specified key combo
     * to an empty function and deletes the corresponding key in the
     * _directMap dict.
     *
     * TODO: actually remove this from the _callbacks dictionary instead
     * of binding an empty function
     *
     * the keycombo+action has to be exactly the same as
     * it was defined in the bind method
     *
     * @param {string|Array} keys
     * @param {string} action
     * @returns void
     */
    Mousetrap.prototype.unbind = function(keys, action) {
        var self = this;
        return self.bind.call(self, keys, function() {}, action);
    };

    /**
     * triggers an event that has already been bound
     *
     * @param {string} keys
     * @param {string=} action
     * @returns void
     */
    Mousetrap.prototype.trigger = function(keys, action) {
        var self = this;
        if (self._directMap[keys + ':' + action]) {
            self._directMap[keys + ':' + action]({}, keys);
        }
        return self;
    };

    /**
     * resets the library back to its initial state.  this is useful
     * if you want to clear out the current keyboard shortcuts and bind
     * new ones - for example if you switch to another page
     *
     * @returns void
     */
    Mousetrap.prototype.reset = function() {
        var self = this;
        self._callbacks = {};
        self._directMap = {};
        return self;
    };

    /**
     * should we stop this event before firing off callbacks
     *
     * @param {Event} e
     * @param {Element} element
     * @return {boolean}
     */
    Mousetrap.prototype.stopCallback = function(e, element) {
        var self = this;

        // if the element has the class "mousetrap" then no need to stop
        if ((' ' + element.className + ' ').indexOf(' mousetrap ') > -1) {
            return false;
        }

        if (_belongsTo(element, self.target)) {
            return false;
        }

        // Events originating from a shadow DOM are re-targetted and `e.target` is the shadow host,
        // not the initial event target in the shadow tree. Note that not all events cross the
        // shadow boundary.
        // For shadow trees with `mode: 'open'`, the initial event target is the first element in
        // the event’s composed path. For shadow trees with `mode: 'closed'`, the initial event
        // target cannot be obtained.
        if ('composedPath' in e && typeof e.composedPath === 'function') {
            // For open shadow trees, update `element` so that the following check works.
            var initialEventTarget = e.composedPath()[0];
            if (initialEventTarget !== e.target) {
                element = initialEventTarget;
            }
        }

        // stop for input, select, and textarea
        return element.tagName == 'INPUT' || element.tagName == 'SELECT' || element.tagName == 'TEXTAREA' || element.isContentEditable;
    };

    /**
     * exposes _handleKey publicly so it can be overwritten by extensions
     */
    Mousetrap.prototype.handleKey = function() {
        var self = this;
        return self._handleKey.apply(self, arguments);
    };

    /**
     * allow custom key mappings
     */
    Mousetrap.addKeycodes = function(object) {
        for (var key in object) {
            if (object.hasOwnProperty(key)) {
                _MAP[key] = object[key];
            }
        }
        _REVERSE_MAP = null;
    };

    /**
     * Init the global mousetrap functions
     *
     * This method is needed to allow the global mousetrap functions to work
     * now that mousetrap is a constructor function.
     */
    Mousetrap.init = function() {
        var documentMousetrap = Mousetrap(document);
        for (var method in documentMousetrap) {
            if (method.charAt(0) !== '_') {
                Mousetrap[method] = (function(method) {
                    return function() {
                        return documentMousetrap[method].apply(documentMousetrap, arguments);
                    };
                } (method));
            }
        }
    };

    Mousetrap.init();

    // expose mousetrap to the global object
    window.Mousetrap = Mousetrap;

    // expose as a common js module
    if ( true && module.exports) {
        module.exports = Mousetrap;
    }

    // expose mousetrap as an AMD module
    if (true) {
        !(__WEBPACK_AMD_DEFINE_RESULT__ = (function() {
            return Mousetrap;
        }).call(exports, __webpack_require__, exports, module),
				__WEBPACK_AMD_DEFINE_RESULT__ !== undefined && (module.exports = __WEBPACK_AMD_DEFINE_RESULT__));
    }
}) (typeof window !== 'undefined' ? window : null, typeof  window !== 'undefined' ? document : null);


/***/ }),

/***/ "mHlH":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* unused harmony export useCallback */
/* unused harmony export useCallbackOne */
/* unused harmony export useMemo */
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return useMemoOne; });
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("cDcd");
/* harmony import */ var react__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(react__WEBPACK_IMPORTED_MODULE_0__);


function areInputsEqual(newInputs, lastInputs) {
  if (newInputs.length !== lastInputs.length) {
    return false;
  }

  for (var i = 0; i < newInputs.length; i++) {
    if (newInputs[i] !== lastInputs[i]) {
      return false;
    }
  }

  return true;
}

function useMemoOne(getResult, inputs) {
  var initial = Object(react__WEBPACK_IMPORTED_MODULE_0__["useState"])(function () {
    return {
      inputs: inputs,
      result: getResult()
    };
  })[0];
  var isFirstRun = Object(react__WEBPACK_IMPORTED_MODULE_0__["useRef"])(true);
  var committed = Object(react__WEBPACK_IMPORTED_MODULE_0__["useRef"])(initial);
  var useCache = isFirstRun.current || Boolean(inputs && committed.current.inputs && areInputsEqual(inputs, committed.current.inputs));
  var cache = useCache ? committed.current : {
    inputs: inputs,
    result: getResult()
  };
  Object(react__WEBPACK_IMPORTED_MODULE_0__["useEffect"])(function () {
    isFirstRun.current = false;
    committed.current = cache;
  }, [cache]);
  return cache.result;
}
function useCallbackOne(callback, inputs) {
  return useMemoOne(function () {
    return callback;
  }, inputs);
}
var useMemo = useMemoOne;
var useCallback = useCallbackOne;




/***/ }),

/***/ "rl8x":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["isShallowEqual"]; }());

/***/ }),

/***/ "sxGJ":
/***/ (function(module, exports, __webpack_require__) {

/*!
 * clipboard.js v2.0.8
 * https://clipboardjs.com/
 *
 * Licensed MIT © Zeno Rocha
 */
(function webpackUniversalModuleDefinition(root, factory) {
	if(true)
		module.exports = factory();
	else {}
})(this, function() {
return /******/ (function() { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 134:
/***/ (function(__unused_webpack_module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "default": function() { return /* binding */ clipboard; }
});

// EXTERNAL MODULE: ./node_modules/tiny-emitter/index.js
var tiny_emitter = __webpack_require__(279);
var tiny_emitter_default = /*#__PURE__*/__webpack_require__.n(tiny_emitter);
// EXTERNAL MODULE: ./node_modules/good-listener/src/listen.js
var listen = __webpack_require__(370);
var listen_default = /*#__PURE__*/__webpack_require__.n(listen);
// EXTERNAL MODULE: ./node_modules/select/src/select.js
var src_select = __webpack_require__(817);
var select_default = /*#__PURE__*/__webpack_require__.n(src_select);
;// CONCATENATED MODULE: ./src/clipboard-action.js
function _typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return _typeof(obj); }

function _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }


/**
 * Inner class which performs selection from either `text` or `target`
 * properties and then executes copy or cut operations.
 */

var ClipboardAction = /*#__PURE__*/function () {
  /**
   * @param {Object} options
   */
  function ClipboardAction(options) {
    _classCallCheck(this, ClipboardAction);

    this.resolveOptions(options);
    this.initSelection();
  }
  /**
   * Defines base properties passed from constructor.
   * @param {Object} options
   */


  _createClass(ClipboardAction, [{
    key: "resolveOptions",
    value: function resolveOptions() {
      var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      this.action = options.action;
      this.container = options.container;
      this.emitter = options.emitter;
      this.target = options.target;
      this.text = options.text;
      this.trigger = options.trigger;
      this.selectedText = '';
    }
    /**
     * Decides which selection strategy is going to be applied based
     * on the existence of `text` and `target` properties.
     */

  }, {
    key: "initSelection",
    value: function initSelection() {
      if (this.text) {
        this.selectFake();
      } else if (this.target) {
        this.selectTarget();
      }
    }
    /**
     * Creates a fake textarea element, sets its value from `text` property,
     */

  }, {
    key: "createFakeElement",
    value: function createFakeElement() {
      var isRTL = document.documentElement.getAttribute('dir') === 'rtl';
      this.fakeElem = document.createElement('textarea'); // Prevent zooming on iOS

      this.fakeElem.style.fontSize = '12pt'; // Reset box model

      this.fakeElem.style.border = '0';
      this.fakeElem.style.padding = '0';
      this.fakeElem.style.margin = '0'; // Move element out of screen horizontally

      this.fakeElem.style.position = 'absolute';
      this.fakeElem.style[isRTL ? 'right' : 'left'] = '-9999px'; // Move element to the same position vertically

      var yPosition = window.pageYOffset || document.documentElement.scrollTop;
      this.fakeElem.style.top = "".concat(yPosition, "px");
      this.fakeElem.setAttribute('readonly', '');
      this.fakeElem.value = this.text;
      return this.fakeElem;
    }
    /**
     * Get's the value of fakeElem,
     * and makes a selection on it.
     */

  }, {
    key: "selectFake",
    value: function selectFake() {
      var _this = this;

      var fakeElem = this.createFakeElement();

      this.fakeHandlerCallback = function () {
        return _this.removeFake();
      };

      this.fakeHandler = this.container.addEventListener('click', this.fakeHandlerCallback) || true;
      this.container.appendChild(fakeElem);
      this.selectedText = select_default()(fakeElem);
      this.copyText();
      this.removeFake();
    }
    /**
     * Only removes the fake element after another click event, that way
     * a user can hit `Ctrl+C` to copy because selection still exists.
     */

  }, {
    key: "removeFake",
    value: function removeFake() {
      if (this.fakeHandler) {
        this.container.removeEventListener('click', this.fakeHandlerCallback);
        this.fakeHandler = null;
        this.fakeHandlerCallback = null;
      }

      if (this.fakeElem) {
        this.container.removeChild(this.fakeElem);
        this.fakeElem = null;
      }
    }
    /**
     * Selects the content from element passed on `target` property.
     */

  }, {
    key: "selectTarget",
    value: function selectTarget() {
      this.selectedText = select_default()(this.target);
      this.copyText();
    }
    /**
     * Executes the copy operation based on the current selection.
     */

  }, {
    key: "copyText",
    value: function copyText() {
      var succeeded;

      try {
        succeeded = document.execCommand(this.action);
      } catch (err) {
        succeeded = false;
      }

      this.handleResult(succeeded);
    }
    /**
     * Fires an event based on the copy operation result.
     * @param {Boolean} succeeded
     */

  }, {
    key: "handleResult",
    value: function handleResult(succeeded) {
      this.emitter.emit(succeeded ? 'success' : 'error', {
        action: this.action,
        text: this.selectedText,
        trigger: this.trigger,
        clearSelection: this.clearSelection.bind(this)
      });
    }
    /**
     * Moves focus away from `target` and back to the trigger, removes current selection.
     */

  }, {
    key: "clearSelection",
    value: function clearSelection() {
      if (this.trigger) {
        this.trigger.focus();
      }

      document.activeElement.blur();
      window.getSelection().removeAllRanges();
    }
    /**
     * Sets the `action` to be performed which can be either 'copy' or 'cut'.
     * @param {String} action
     */

  }, {
    key: "destroy",

    /**
     * Destroy lifecycle.
     */
    value: function destroy() {
      this.removeFake();
    }
  }, {
    key: "action",
    set: function set() {
      var action = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : 'copy';
      this._action = action;

      if (this._action !== 'copy' && this._action !== 'cut') {
        throw new Error('Invalid "action" value, use either "copy" or "cut"');
      }
    }
    /**
     * Gets the `action` property.
     * @return {String}
     */
    ,
    get: function get() {
      return this._action;
    }
    /**
     * Sets the `target` property using an element
     * that will be have its content copied.
     * @param {Element} target
     */

  }, {
    key: "target",
    set: function set(target) {
      if (target !== undefined) {
        if (target && _typeof(target) === 'object' && target.nodeType === 1) {
          if (this.action === 'copy' && target.hasAttribute('disabled')) {
            throw new Error('Invalid "target" attribute. Please use "readonly" instead of "disabled" attribute');
          }

          if (this.action === 'cut' && (target.hasAttribute('readonly') || target.hasAttribute('disabled'))) {
            throw new Error('Invalid "target" attribute. You can\'t cut text from elements with "readonly" or "disabled" attributes');
          }

          this._target = target;
        } else {
          throw new Error('Invalid "target" value, use a valid Element');
        }
      }
    }
    /**
     * Gets the `target` property.
     * @return {String|HTMLElement}
     */
    ,
    get: function get() {
      return this._target;
    }
  }]);

  return ClipboardAction;
}();

/* harmony default export */ var clipboard_action = (ClipboardAction);
;// CONCATENATED MODULE: ./src/clipboard.js
function clipboard_typeof(obj) { "@babel/helpers - typeof"; if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") { clipboard_typeof = function _typeof(obj) { return typeof obj; }; } else { clipboard_typeof = function _typeof(obj) { return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }; } return clipboard_typeof(obj); }

function clipboard_classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError("Cannot call a class as a function"); } }

function clipboard_defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if ("value" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }

function clipboard_createClass(Constructor, protoProps, staticProps) { if (protoProps) clipboard_defineProperties(Constructor.prototype, protoProps); if (staticProps) clipboard_defineProperties(Constructor, staticProps); return Constructor; }

function _inherits(subClass, superClass) { if (typeof superClass !== "function" && superClass !== null) { throw new TypeError("Super expression must either be null or a function"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }

function _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }

function _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }

function _possibleConstructorReturn(self, call) { if (call && (clipboard_typeof(call) === "object" || typeof call === "function")) { return call; } return _assertThisInitialized(self); }

function _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError("this hasn't been initialised - super() hasn't been called"); } return self; }

function _isNativeReflectConstruct() { if (typeof Reflect === "undefined" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === "function") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }

function _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }




/**
 * Helper function to retrieve attribute value.
 * @param {String} suffix
 * @param {Element} element
 */

function getAttributeValue(suffix, element) {
  var attribute = "data-clipboard-".concat(suffix);

  if (!element.hasAttribute(attribute)) {
    return;
  }

  return element.getAttribute(attribute);
}
/**
 * Base class which takes one or more elements, adds event listeners to them,
 * and instantiates a new `ClipboardAction` on each click.
 */


var Clipboard = /*#__PURE__*/function (_Emitter) {
  _inherits(Clipboard, _Emitter);

  var _super = _createSuper(Clipboard);

  /**
   * @param {String|HTMLElement|HTMLCollection|NodeList} trigger
   * @param {Object} options
   */
  function Clipboard(trigger, options) {
    var _this;

    clipboard_classCallCheck(this, Clipboard);

    _this = _super.call(this);

    _this.resolveOptions(options);

    _this.listenClick(trigger);

    return _this;
  }
  /**
   * Defines if attributes would be resolved using internal setter functions
   * or custom functions that were passed in the constructor.
   * @param {Object} options
   */


  clipboard_createClass(Clipboard, [{
    key: "resolveOptions",
    value: function resolveOptions() {
      var options = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
      this.action = typeof options.action === 'function' ? options.action : this.defaultAction;
      this.target = typeof options.target === 'function' ? options.target : this.defaultTarget;
      this.text = typeof options.text === 'function' ? options.text : this.defaultText;
      this.container = clipboard_typeof(options.container) === 'object' ? options.container : document.body;
    }
    /**
     * Adds a click event listener to the passed trigger.
     * @param {String|HTMLElement|HTMLCollection|NodeList} trigger
     */

  }, {
    key: "listenClick",
    value: function listenClick(trigger) {
      var _this2 = this;

      this.listener = listen_default()(trigger, 'click', function (e) {
        return _this2.onClick(e);
      });
    }
    /**
     * Defines a new `ClipboardAction` on each click event.
     * @param {Event} e
     */

  }, {
    key: "onClick",
    value: function onClick(e) {
      var trigger = e.delegateTarget || e.currentTarget;

      if (this.clipboardAction) {
        this.clipboardAction = null;
      }

      this.clipboardAction = new clipboard_action({
        action: this.action(trigger),
        target: this.target(trigger),
        text: this.text(trigger),
        container: this.container,
        trigger: trigger,
        emitter: this
      });
    }
    /**
     * Default `action` lookup function.
     * @param {Element} trigger
     */

  }, {
    key: "defaultAction",
    value: function defaultAction(trigger) {
      return getAttributeValue('action', trigger);
    }
    /**
     * Default `target` lookup function.
     * @param {Element} trigger
     */

  }, {
    key: "defaultTarget",
    value: function defaultTarget(trigger) {
      var selector = getAttributeValue('target', trigger);

      if (selector) {
        return document.querySelector(selector);
      }
    }
    /**
     * Returns the support of the given action, or all actions if no action is
     * given.
     * @param {String} [action]
     */

  }, {
    key: "defaultText",

    /**
     * Default `text` lookup function.
     * @param {Element} trigger
     */
    value: function defaultText(trigger) {
      return getAttributeValue('text', trigger);
    }
    /**
     * Destroy lifecycle.
     */

  }, {
    key: "destroy",
    value: function destroy() {
      this.listener.destroy();

      if (this.clipboardAction) {
        this.clipboardAction.destroy();
        this.clipboardAction = null;
      }
    }
  }], [{
    key: "isSupported",
    value: function isSupported() {
      var action = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : ['copy', 'cut'];
      var actions = typeof action === 'string' ? [action] : action;
      var support = !!document.queryCommandSupported;
      actions.forEach(function (action) {
        support = support && !!document.queryCommandSupported(action);
      });
      return support;
    }
  }]);

  return Clipboard;
}((tiny_emitter_default()));

/* harmony default export */ var clipboard = (Clipboard);

/***/ }),

/***/ 828:
/***/ (function(module) {

var DOCUMENT_NODE_TYPE = 9;

/**
 * A polyfill for Element.matches()
 */
if (typeof Element !== 'undefined' && !Element.prototype.matches) {
    var proto = Element.prototype;

    proto.matches = proto.matchesSelector ||
                    proto.mozMatchesSelector ||
                    proto.msMatchesSelector ||
                    proto.oMatchesSelector ||
                    proto.webkitMatchesSelector;
}

/**
 * Finds the closest parent that matches a selector.
 *
 * @param {Element} element
 * @param {String} selector
 * @return {Function}
 */
function closest (element, selector) {
    while (element && element.nodeType !== DOCUMENT_NODE_TYPE) {
        if (typeof element.matches === 'function' &&
            element.matches(selector)) {
          return element;
        }
        element = element.parentNode;
    }
}

module.exports = closest;


/***/ }),

/***/ 438:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var closest = __webpack_require__(828);

/**
 * Delegates event to a selector.
 *
 * @param {Element} element
 * @param {String} selector
 * @param {String} type
 * @param {Function} callback
 * @param {Boolean} useCapture
 * @return {Object}
 */
function _delegate(element, selector, type, callback, useCapture) {
    var listenerFn = listener.apply(this, arguments);

    element.addEventListener(type, listenerFn, useCapture);

    return {
        destroy: function() {
            element.removeEventListener(type, listenerFn, useCapture);
        }
    }
}

/**
 * Delegates event to a selector.
 *
 * @param {Element|String|Array} [elements]
 * @param {String} selector
 * @param {String} type
 * @param {Function} callback
 * @param {Boolean} useCapture
 * @return {Object}
 */
function delegate(elements, selector, type, callback, useCapture) {
    // Handle the regular Element usage
    if (typeof elements.addEventListener === 'function') {
        return _delegate.apply(null, arguments);
    }

    // Handle Element-less usage, it defaults to global delegation
    if (typeof type === 'function') {
        // Use `document` as the first parameter, then apply arguments
        // This is a short way to .unshift `arguments` without running into deoptimizations
        return _delegate.bind(null, document).apply(null, arguments);
    }

    // Handle Selector-based usage
    if (typeof elements === 'string') {
        elements = document.querySelectorAll(elements);
    }

    // Handle Array-like based usage
    return Array.prototype.map.call(elements, function (element) {
        return _delegate(element, selector, type, callback, useCapture);
    });
}

/**
 * Finds closest match and invokes callback.
 *
 * @param {Element} element
 * @param {String} selector
 * @param {String} type
 * @param {Function} callback
 * @return {Function}
 */
function listener(element, selector, type, callback) {
    return function(e) {
        e.delegateTarget = closest(e.target, selector);

        if (e.delegateTarget) {
            callback.call(element, e);
        }
    }
}

module.exports = delegate;


/***/ }),

/***/ 879:
/***/ (function(__unused_webpack_module, exports) {

/**
 * Check if argument is a HTML element.
 *
 * @param {Object} value
 * @return {Boolean}
 */
exports.node = function(value) {
    return value !== undefined
        && value instanceof HTMLElement
        && value.nodeType === 1;
};

/**
 * Check if argument is a list of HTML elements.
 *
 * @param {Object} value
 * @return {Boolean}
 */
exports.nodeList = function(value) {
    var type = Object.prototype.toString.call(value);

    return value !== undefined
        && (type === '[object NodeList]' || type === '[object HTMLCollection]')
        && ('length' in value)
        && (value.length === 0 || exports.node(value[0]));
};

/**
 * Check if argument is a string.
 *
 * @param {Object} value
 * @return {Boolean}
 */
exports.string = function(value) {
    return typeof value === 'string'
        || value instanceof String;
};

/**
 * Check if argument is a function.
 *
 * @param {Object} value
 * @return {Boolean}
 */
exports.fn = function(value) {
    var type = Object.prototype.toString.call(value);

    return type === '[object Function]';
};


/***/ }),

/***/ 370:
/***/ (function(module, __unused_webpack_exports, __webpack_require__) {

var is = __webpack_require__(879);
var delegate = __webpack_require__(438);

/**
 * Validates all params and calls the right
 * listener function based on its target type.
 *
 * @param {String|HTMLElement|HTMLCollection|NodeList} target
 * @param {String} type
 * @param {Function} callback
 * @return {Object}
 */
function listen(target, type, callback) {
    if (!target && !type && !callback) {
        throw new Error('Missing required arguments');
    }

    if (!is.string(type)) {
        throw new TypeError('Second argument must be a String');
    }

    if (!is.fn(callback)) {
        throw new TypeError('Third argument must be a Function');
    }

    if (is.node(target)) {
        return listenNode(target, type, callback);
    }
    else if (is.nodeList(target)) {
        return listenNodeList(target, type, callback);
    }
    else if (is.string(target)) {
        return listenSelector(target, type, callback);
    }
    else {
        throw new TypeError('First argument must be a String, HTMLElement, HTMLCollection, or NodeList');
    }
}

/**
 * Adds an event listener to a HTML element
 * and returns a remove listener function.
 *
 * @param {HTMLElement} node
 * @param {String} type
 * @param {Function} callback
 * @return {Object}
 */
function listenNode(node, type, callback) {
    node.addEventListener(type, callback);

    return {
        destroy: function() {
            node.removeEventListener(type, callback);
        }
    }
}

/**
 * Add an event listener to a list of HTML elements
 * and returns a remove listener function.
 *
 * @param {NodeList|HTMLCollection} nodeList
 * @param {String} type
 * @param {Function} callback
 * @return {Object}
 */
function listenNodeList(nodeList, type, callback) {
    Array.prototype.forEach.call(nodeList, function(node) {
        node.addEventListener(type, callback);
    });

    return {
        destroy: function() {
            Array.prototype.forEach.call(nodeList, function(node) {
                node.removeEventListener(type, callback);
            });
        }
    }
}

/**
 * Add an event listener to a selector
 * and returns a remove listener function.
 *
 * @param {String} selector
 * @param {String} type
 * @param {Function} callback
 * @return {Object}
 */
function listenSelector(selector, type, callback) {
    return delegate(document.body, selector, type, callback);
}

module.exports = listen;


/***/ }),

/***/ 817:
/***/ (function(module) {

function select(element) {
    var selectedText;

    if (element.nodeName === 'SELECT') {
        element.focus();

        selectedText = element.value;
    }
    else if (element.nodeName === 'INPUT' || element.nodeName === 'TEXTAREA') {
        var isReadOnly = element.hasAttribute('readonly');

        if (!isReadOnly) {
            element.setAttribute('readonly', '');
        }

        element.select();
        element.setSelectionRange(0, element.value.length);

        if (!isReadOnly) {
            element.removeAttribute('readonly');
        }

        selectedText = element.value;
    }
    else {
        if (element.hasAttribute('contenteditable')) {
            element.focus();
        }

        var selection = window.getSelection();
        var range = document.createRange();

        range.selectNodeContents(element);
        selection.removeAllRanges();
        selection.addRange(range);

        selectedText = selection.toString();
    }

    return selectedText;
}

module.exports = select;


/***/ }),

/***/ 279:
/***/ (function(module) {

function E () {
  // Keep this empty so it's easier to inherit from
  // (via https://github.com/lipsmack from https://github.com/scottcorgan/tiny-emitter/issues/3)
}

E.prototype = {
  on: function (name, callback, ctx) {
    var e = this.e || (this.e = {});

    (e[name] || (e[name] = [])).push({
      fn: callback,
      ctx: ctx
    });

    return this;
  },

  once: function (name, callback, ctx) {
    var self = this;
    function listener () {
      self.off(name, listener);
      callback.apply(ctx, arguments);
    };

    listener._ = callback
    return this.on(name, listener, ctx);
  },

  emit: function (name) {
    var data = [].slice.call(arguments, 1);
    var evtArr = ((this.e || (this.e = {}))[name] || []).slice();
    var i = 0;
    var len = evtArr.length;

    for (i; i < len; i++) {
      evtArr[i].fn.apply(evtArr[i].ctx, data);
    }

    return this;
  },

  off: function (name, callback) {
    var e = this.e || (this.e = {});
    var evts = e[name];
    var liveEvents = [];

    if (evts && callback) {
      for (var i = 0, len = evts.length; i < len; i++) {
        if (evts[i].fn !== callback && evts[i].fn._ !== callback)
          liveEvents.push(evts[i]);
      }
    }

    // Remove event from queue to prevent memory leak
    // Suggested by https://github.com/lazd
    // Ref: https://github.com/scottcorgan/tiny-emitter/commit/c6ebfaa9bc973b33d110a84a307742b7cf94c953#commitcomment-5024910

    (liveEvents.length)
      ? e[name] = liveEvents
      : delete e[name];

    return this;
  }
};

module.exports = E;
module.exports.TinyEmitter = E;


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		if(__webpack_module_cache__[moduleId]) {
/******/ 			return __webpack_module_cache__[moduleId].exports;
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
/************************************************************************/
/******/ 	// module exports must be returned from runtime so entry inlining is disabled
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(134);
/******/ })()
.default;
});

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