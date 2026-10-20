(function() {
"use strict";
var wp;
(wp ||= {}).commands = (() => {
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

  // vendor-external:react
  var require_react = __commonJS({
    "vendor-external:react"(exports, module) {
      module.exports = window.React;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // vendor-external:react-dom
  var require_react_dom = __commonJS({
    "vendor-external:react-dom"(exports, module) {
      module.exports = window.ReactDOM;
    }
  });

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
    }
  });

  // package-external:@wordpress/keyboard-shortcuts
  var require_keyboard_shortcuts = __commonJS({
    "package-external:@wordpress/keyboard-shortcuts"(exports, module) {
      module.exports = window.wp.keyboardShortcuts;
    }
  });

  // package-external:@wordpress/keycodes
  var require_keycodes = __commonJS({
    "package-external:@wordpress/keycodes"(exports, module) {
      module.exports = window.wp.keycodes;
    }
  });

  // package-external:@wordpress/primitives
  var require_primitives = __commonJS({
    "package-external:@wordpress/primitives"(exports, module) {
      module.exports = window.wp.primitives;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // package-external:@wordpress/preferences
  var require_preferences = __commonJS({
    "package-external:@wordpress/preferences"(exports, module) {
      module.exports = window.wp.preferences;
    }
  });

  // packages/commands/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    CommandMenu: () => CommandMenu,
    privateApis: () => privateApis,
    store: () => store,
    useCommand: () => useCommand,
    useCommandLoader: () => useCommandLoader,
    useCommands: () => useCommands
  });

  // node_modules/.store/cmdk@1.1.1-2SvSjHrFhiqsPDore7Lelw/node_modules/cmdk/dist/chunk-NZJY6EH4.mjs
  var U = 1;
  var Y = 0.9;
  var H = 0.8;
  var J = 0.17;
  var p = 0.1;
  var u = 0.999;
  var $ = 0.9999;
  var k = 0.99;
  var m = /[\\\/_+.#"@\[\(\{&]/;
  var B = /[\\\/_+.#"@\[\(\{&]/g;
  var K = /[\s-]/;
  var X = /[\s-]/g;
  function G(_, C, h, P2, A, f, O) {
    if (f === C.length) return A === _.length ? U : k;
    var T2 = `${A},${f}`;
    if (O[T2] !== void 0) return O[T2];
    for (var L2 = P2.charAt(f), c = h.indexOf(L2, A), S = 0, E, N2, R, M; c >= 0; ) E = G(_, C, h, P2, c + 1, f + 1, O), E > S && (c === A ? E *= U : m.test(_.charAt(c - 1)) ? (E *= H, R = _.slice(A, c - 1).match(B), R && A > 0 && (E *= Math.pow(u, R.length))) : K.test(_.charAt(c - 1)) ? (E *= Y, M = _.slice(A, c - 1).match(X), M && A > 0 && (E *= Math.pow(u, M.length))) : (E *= J, A > 0 && (E *= Math.pow(u, c - A))), _.charAt(c) !== C.charAt(f) && (E *= $)), (E < p && h.charAt(c - 1) === P2.charAt(f + 1) || P2.charAt(f + 1) === P2.charAt(f) && h.charAt(c - 1) !== P2.charAt(f)) && (N2 = G(_, C, h, P2, c + 1, f + 2, O), N2 * p > E && (E = N2 * p)), E > S && (S = E), c = h.indexOf(L2, c + 1);
    return O[T2] = S, S;
  }
  function D(_) {
    return _.toLowerCase().replace(X, " ");
  }
  function W(_, C, h) {
    return _ = h && h.length > 0 ? `${_ + " " + h.join(" ")}` : _, G(_, C, D(_), D(C), 0, 0, {});
  }

  // node_modules/.store/@radix-ui/react-dialog@1.1.23-ytDSaPyJAw3b9aD4KkIiOA/node_modules/@radix-ui/react-dialog/dist/index.mjs
  var React24 = __toESM(require_react(), 1);

  // node_modules/.store/@radix-ui/primitive@1.1.7-U6Tg5_5zgTQvcXJNNg7HcQ/node_modules/@radix-ui/primitive/dist/index.mjs
  var __defProp2 = Object.defineProperty;
  var __name = (target, value) => __defProp2(target, "name", { value, configurable: true });
  var canUseDOM = !!(typeof window !== "undefined" && window.document && window.document.createElement);
  function composeEventHandlers(originalEventHandler, ourEventHandler, { checkForDefaultPrevented = true } = {}) {
    return /* @__PURE__ */ __name(function handleEvent(event) {
      originalEventHandler?.(event);
      if (checkForDefaultPrevented === false || !event || !event.defaultPrevented) {
        return ourEventHandler?.(event);
      }
    }, "handleEvent");
  }
  __name(composeEventHandlers, "composeEventHandlers");
  function getOwnerWindow(element) {
    if (!canUseDOM) {
      throw new Error("Cannot access window outside of the DOM");
    }
    return element?.ownerDocument?.defaultView ?? window;
  }
  __name(getOwnerWindow, "getOwnerWindow");
  function getOwnerDocument(element) {
    if (!canUseDOM) {
      throw new Error("Cannot access document outside of the DOM");
    }
    return element?.ownerDocument ?? document;
  }
  __name(getOwnerDocument, "getOwnerDocument");
  function getActiveElement(node, activeDescendant = false) {
    const { activeElement } = getOwnerDocument(node);
    if (!activeElement?.nodeName) {
      return null;
    }
    if (isFrame(activeElement) && activeElement.contentDocument) {
      return getActiveElement(activeElement.contentDocument.body, activeDescendant);
    }
    if (activeDescendant) {
      const id = activeElement.getAttribute("aria-activedescendant");
      if (id) {
        const element = getOwnerDocument(activeElement).getElementById(id);
        if (element) {
          return element;
        }
      }
    }
    return activeElement;
  }
  __name(getActiveElement, "getActiveElement");
  function isFrame(element) {
    return element.tagName === "IFRAME";
  }
  __name(isFrame, "isFrame");

  // node_modules/.store/@radix-ui/react-compose-refs@1.1.5-Yxi_9Vcwu9BZ5W9uATcsBQ/node_modules/@radix-ui/react-compose-refs/dist/index.mjs
  var React = __toESM(require_react(), 1);
  var __defProp3 = Object.defineProperty;
  var __name2 = (target, value) => __defProp3(target, "name", { value, configurable: true });
  function setRef(ref, value) {
    if (typeof ref === "function") {
      return ref(value);
    } else if (ref !== null && ref !== void 0) {
      ref.current = value;
    }
  }
  __name2(setRef, "setRef");
  function composeRefs(...refs) {
    return (node) => {
      let hasCleanup = false;
      const cleanups = refs.map((ref) => {
        const cleanup = setRef(ref, node);
        if (!hasCleanup && typeof cleanup == "function") {
          hasCleanup = true;
        }
        return cleanup;
      });
      if (hasCleanup) {
        return () => {
          for (let i = 0; i < cleanups.length; i++) {
            const cleanup = cleanups[i];
            if (typeof cleanup == "function") {
              cleanup();
            } else {
              setRef(refs[i], null);
            }
          }
        };
      }
    };
  }
  __name2(composeRefs, "composeRefs");
  function useComposedRefs(...refs) {
    return React.useCallback(composeRefs(...refs), refs);
  }
  __name2(useComposedRefs, "useComposedRefs");

  // node_modules/.store/@radix-ui/react-context@1.2.2-j7-25QEapK1ImCGTa8GJpg/node_modules/@radix-ui/react-context/dist/index.mjs
  var React2 = __toESM(require_react(), 1);
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
  var __defProp4 = Object.defineProperty;
  var __name3 = (target, value) => __defProp4(target, "name", { value, configurable: true });
  // @__NO_SIDE_EFFECTS__
  function createContext2(rootComponentName, defaultContext) {
    const Context = React2.createContext(defaultContext);
    Context.displayName = rootComponentName + "Context";
    const Provider = /* @__PURE__ */ __name3((props) => {
      const { children, ...context2 } = props;
      const value = React2.useMemo(() => context2, Object.values(context2));
      return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(Context.Provider, { value, children });
    }, "Provider");
    Provider.displayName = rootComponentName + "Provider";
    function useContext22(consumerName, options = {}) {
      const { optional = false } = options;
      const context2 = React2.useContext(Context);
      if (context2) return context2;
      if (defaultContext !== void 0) return defaultContext;
      if (optional) return void 0;
      throw new Error(`\`${consumerName}\` must be used within \`${rootComponentName}\``);
    }
    __name3(useContext22, "useContext");
    return [Provider, useContext22];
  }
  __name3(createContext2, "createContext");
  // @__NO_SIDE_EFFECTS__
  function createContextScope(scopeName, createContextScopeDeps = []) {
    let defaultContexts = [];
    function createContext32(rootComponentName, defaultContext) {
      const BaseContext = React2.createContext(defaultContext);
      BaseContext.displayName = rootComponentName + "Context";
      const index = defaultContexts.length;
      defaultContexts = [...defaultContexts, defaultContext];
      const Provider = /* @__PURE__ */ __name3((props) => {
        const { scope, children, ...context2 } = props;
        const Context = scope?.[scopeName]?.[index] || BaseContext;
        const value = React2.useMemo(() => context2, Object.values(context2));
        return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(Context.Provider, { value, children });
      }, "Provider");
      Provider.displayName = rootComponentName + "Provider";
      function useContext22(consumerName, scope, options = {}) {
        const { optional = false } = options;
        const Context = scope?.[scopeName]?.[index] || BaseContext;
        const context2 = React2.useContext(Context);
        if (context2) return context2;
        if (defaultContext !== void 0) return defaultContext;
        if (optional) return void 0;
        throw new Error(`\`${consumerName}\` must be used within \`${rootComponentName}\``);
      }
      __name3(useContext22, "useContext");
      return [Provider, useContext22];
    }
    __name3(createContext32, "createContext");
    const createScope = /* @__PURE__ */ __name3(() => {
      const scopeContexts = defaultContexts.map((defaultContext) => {
        return React2.createContext(defaultContext);
      });
      return /* @__PURE__ */ __name3(function useScope(scope) {
        const contexts = scope?.[scopeName] || scopeContexts;
        return React2.useMemo(
          () => ({ [`__scope${scopeName}`]: { ...scope, [scopeName]: contexts } }),
          [scope, contexts]
        );
      }, "useScope");
    }, "createScope");
    createScope.scopeName = scopeName;
    return [createContext32, composeContextScopes(createScope, ...createContextScopeDeps)];
  }
  __name3(createContextScope, "createContextScope");
  function composeContextScopes(...scopes) {
    const baseScope = scopes[0];
    if (scopes.length === 1) return baseScope;
    const createScope = /* @__PURE__ */ __name3(() => {
      const scopeHooks = scopes.map((createScope2) => ({
        useScope: createScope2(),
        scopeName: createScope2.scopeName
      }));
      return /* @__PURE__ */ __name3(function useComposedScopes(overrideScopes) {
        const nextScopes = scopeHooks.reduce((nextScopes2, { useScope, scopeName }) => {
          const scopeProps = useScope(overrideScopes);
          const currentScope = scopeProps[`__scope${scopeName}`];
          return { ...nextScopes2, ...currentScope };
        }, {});
        return React2.useMemo(() => ({ [`__scope${baseScope.scopeName}`]: nextScopes }), [nextScopes]);
      }, "useComposedScopes");
    }, "createScope");
    createScope.scopeName = baseScope.scopeName;
    return createScope;
  }
  __name3(composeContextScopes, "composeContextScopes");

  // node_modules/.store/@radix-ui/react-id@1.1.4-lJYQ8uCc9ZjR3dGsRybCAQ/node_modules/@radix-ui/react-id/dist/index.mjs
  var React4 = __toESM(require_react(), 1);

  // node_modules/.store/@radix-ui/react-use-layout-effect@1.1.4-xgr8ZdGzRbbpiAVfalj0KQ/node_modules/@radix-ui/react-use-layout-effect/dist/index.mjs
  var React3 = __toESM(require_react(), 1);
  var useLayoutEffect2 = globalThis?.document ? React3.useLayoutEffect : () => {
  };

  // node_modules/.store/@radix-ui/react-id@1.1.4-lJYQ8uCc9ZjR3dGsRybCAQ/node_modules/@radix-ui/react-id/dist/index.mjs
  var __defProp5 = Object.defineProperty;
  var __name4 = (target, value) => __defProp5(target, "name", { value, configurable: true });
  var useReactId = React4[" useId ".trim().toString()] || (() => void 0);
  var count = 0;
  function useId(deterministicId) {
    const [id, setId] = React4.useState(useReactId());
    useLayoutEffect2(() => {
      if (!deterministicId) setId((reactId) => reactId ?? String(count++));
    }, [deterministicId]);
    return deterministicId || (id ? `radix-${id}` : "");
  }
  __name4(useId, "useId");

  // node_modules/.store/@radix-ui/react-use-controllable-state@1.2.6-E1gehUSActoGL_ABO78qWQ/node_modules/@radix-ui/react-use-controllable-state/dist/index.mjs
  var React6 = __toESM(require_react(), 1);

  // node_modules/.store/@radix-ui/primitive@1.1.7-U6Tg5_5zgTQvcXJNNg7HcQ/node_modules/@radix-ui/primitive/dist/internal/is-development.true.mjs
  var IS_DEVELOPMENT = true;

  // node_modules/.store/@radix-ui/react-use-controllable-state@1.2.6-E1gehUSActoGL_ABO78qWQ/node_modules/@radix-ui/react-use-controllable-state/dist/index.mjs
  var React22 = __toESM(require_react(), 1);

  // node_modules/.store/@radix-ui/react-use-effect-event@0.0.5-Yi5oSZAZmYwW6OVrdtieoA/node_modules/@radix-ui/react-use-effect-event/dist/index.mjs
  var React5 = __toESM(require_react(), 1);
  var __defProp6 = Object.defineProperty;
  var __name5 = (target, value) => __defProp6(target, "name", { value, configurable: true });
  var useReactEffectEvent = React5[" useEffectEvent ".trim().toString()];
  var useReactInsertionEffect = React5[" useInsertionEffect ".trim().toString()];
  function useEffectEvent(callback) {
    if (typeof useReactEffectEvent === "function") {
      return useReactEffectEvent(callback);
    }
    const ref = React5.useRef(() => {
      throw new Error("Cannot call an event handler while rendering.");
    });
    if (typeof useReactInsertionEffect === "function") {
      useReactInsertionEffect(() => {
        ref.current = callback;
      });
    } else {
      useLayoutEffect2(() => {
        ref.current = callback;
      });
    }
    return React5.useMemo(() => ((...args) => ref.current?.(...args)), []);
  }
  __name5(useEffectEvent, "useEffectEvent");

  // node_modules/.store/@radix-ui/react-use-controllable-state@1.2.6-E1gehUSActoGL_ABO78qWQ/node_modules/@radix-ui/react-use-controllable-state/dist/index.mjs
  var __defProp7 = Object.defineProperty;
  var __name6 = (target, value) => __defProp7(target, "name", { value, configurable: true });
  var useInsertionEffect = React6[" useInsertionEffect ".trim().toString()] || useLayoutEffect2;
  function useControllableState({
    prop,
    defaultProp,
    onChange = /* @__PURE__ */ __name6(() => {
    }, "onChange"),
    caller
  }) {
    const [uncontrolledProp, setUncontrolledProp, onChangeRef] = useUncontrolledState({
      defaultProp,
      onChange
    });
    const isControlled = prop !== void 0;
    const value = isControlled ? prop : uncontrolledProp;
    if (IS_DEVELOPMENT) {
      const isControlledRef = React6.useRef(prop !== void 0);
      React6.useEffect(() => {
        const wasControlled = isControlledRef.current;
        if (wasControlled !== isControlled) {
          const from = wasControlled ? "controlled" : "uncontrolled";
          const to = isControlled ? "controlled" : "uncontrolled";
          console.warn(
            `${caller} is changing from ${from} to ${to}. Components should not switch from controlled to uncontrolled (or vice versa). Decide between using a controlled or uncontrolled value for the lifetime of the component.`
          );
        }
        isControlledRef.current = isControlled;
      }, [isControlled, caller]);
    }
    const setValue = React6.useCallback(
      (nextValue) => {
        if (isControlled) {
          const value2 = isFunction(nextValue) ? nextValue(prop) : nextValue;
          if (value2 !== prop) {
            onChangeRef.current?.(value2);
          }
        } else {
          setUncontrolledProp(nextValue);
        }
      },
      [isControlled, prop, setUncontrolledProp, onChangeRef]
    );
    return [value, setValue];
  }
  __name6(useControllableState, "useControllableState");
  function useUncontrolledState({
    defaultProp,
    onChange
  }) {
    const [value, setValue] = React6.useState(defaultProp);
    const prevValueRef = React6.useRef(value);
    const onChangeRef = React6.useRef(onChange);
    useInsertionEffect(() => {
      onChangeRef.current = onChange;
    }, [onChange]);
    React6.useEffect(() => {
      if (prevValueRef.current !== value) {
        onChangeRef.current?.(value);
        prevValueRef.current = value;
      }
    }, [value, prevValueRef]);
    return [value, setValue, onChangeRef];
  }
  __name6(useUncontrolledState, "useUncontrolledState");
  function isFunction(value) {
    return typeof value === "function";
  }
  __name6(isFunction, "isFunction");
  var SYNC_STATE = /* @__PURE__ */ Symbol("RADIX:SYNC_STATE");
  function useControllableStateReducer(reducer2, userArgs, initialArg, init) {
    const { prop: controlledState, defaultProp, onChange: onChangeProp, caller } = userArgs;
    const isControlled = controlledState !== void 0;
    const onChange = useEffectEvent(onChangeProp);
    if (IS_DEVELOPMENT) {
      const isControlledRef = React22.useRef(controlledState !== void 0);
      React22.useEffect(() => {
        const wasControlled = isControlledRef.current;
        if (wasControlled !== isControlled) {
          const from = wasControlled ? "controlled" : "uncontrolled";
          const to = isControlled ? "controlled" : "uncontrolled";
          console.warn(
            `${caller} is changing from ${from} to ${to}. Components should not switch from controlled to uncontrolled (or vice versa). Decide between using a controlled or uncontrolled value for the lifetime of the component.`
          );
        }
        isControlledRef.current = isControlled;
      }, [isControlled, caller]);
    }
    const args = [{ ...initialArg, state: defaultProp }];
    if (init) {
      args.push(init);
    }
    const [internalState, dispatch2] = React22.useReducer(
      (state2, action) => {
        if (action.type === SYNC_STATE) {
          return { ...state2, state: action.state };
        }
        const next = reducer2(state2, action);
        if (isControlled && !Object.is(next.state, state2.state)) {
          onChange(next.state);
        }
        return next;
      },
      ...args
    );
    const uncontrolledState = internalState.state;
    const prevValueRef = React22.useRef(uncontrolledState);
    React22.useEffect(() => {
      if (prevValueRef.current !== uncontrolledState) {
        prevValueRef.current = uncontrolledState;
        if (!isControlled) {
          onChange(uncontrolledState);
        }
      }
    }, [uncontrolledState, prevValueRef, isControlled]);
    const state = React22.useMemo(() => {
      const isControlled2 = controlledState !== void 0;
      if (isControlled2) {
        return { ...internalState, state: controlledState };
      }
      return internalState;
    }, [internalState, controlledState]);
    React22.useEffect(() => {
      if (isControlled && !Object.is(controlledState, internalState.state)) {
        dispatch2({ type: SYNC_STATE, state: controlledState });
      }
    }, [controlledState, internalState.state, isControlled]);
    return [state, dispatch2];
  }
  __name6(useControllableStateReducer, "useControllableStateReducer");

  // node_modules/.store/@radix-ui/react-dismissable-layer@1.1.19-TH-TBgk6yZ9haPsxC6i4zw/node_modules/@radix-ui/react-dismissable-layer/dist/index.mjs
  var React10 = __toESM(require_react(), 1);

  // node_modules/.store/@radix-ui/react-primitive@2.1.10-zeSsoG72bCPsw9Cm9y8JEw/node_modules/@radix-ui/react-primitive/dist/index.mjs
  var React8 = __toESM(require_react(), 1);
  var ReactDOM = __toESM(require_react_dom(), 1);

  // node_modules/.store/@radix-ui/react-slot@1.3.3-gXjH-EJQB-ydh9AEtBi9Xg/node_modules/@radix-ui/react-slot/dist/index.mjs
  var React7 = __toESM(require_react(), 1);
  var __defProp8 = Object.defineProperty;
  var __name7 = (target, value) => __defProp8(target, "name", { value, configurable: true });
  // @__NO_SIDE_EFFECTS__
  function createSlot(ownerName) {
    const Slot2 = React7.forwardRef((props, forwardedRef) => {
      let { children, ...slotProps } = props;
      let slottableElement = null;
      let hasSlottable = false;
      const newChildren = [];
      if (isLazyComponent(children) && typeof use === "function") {
        children = use(children._payload);
      }
      React7.Children.forEach(children, (maybeSlottable) => {
        if (isSlottable(maybeSlottable)) {
          hasSlottable = true;
          const slottable = maybeSlottable;
          let child = "child" in slottable.props ? slottable.props.child : slottable.props.children;
          if (isLazyComponent(child) && typeof use === "function") {
            child = use(child._payload);
          }
          slottableElement = getSlottableElementFromSlottable(slottable, child);
          newChildren.push(slottableElement?.props?.children);
        } else {
          newChildren.push(maybeSlottable);
        }
      });
      if (slottableElement) {
        slottableElement = React7.cloneElement(slottableElement, void 0, newChildren);
      } else if (
        // A `Slottable` was found but it didn't resolve to a single element (e.g.
        // it wrapped multiple elements, text, or a render-prop `child` that
        // wasn't an element). Don't fall back to treating the `Slottable` wrapper
        // itself as the slot target — throw a descriptive error below instead.
        !hasSlottable && React7.Children.count(children) === 1 && React7.isValidElement(children)
      ) {
        slottableElement = children;
      }
      const slottableElementRef = slottableElement ? getElementRef(slottableElement) : void 0;
      const composedRef = useComposedRefs(forwardedRef, slottableElementRef);
      if (!slottableElement) {
        if (children || children === 0) {
          throw new Error(
            hasSlottable ? createSlottableError(ownerName) : createSlotError(ownerName)
          );
        }
        return children;
      }
      const mergedProps = mergeProps(slotProps, slottableElement.props ?? {});
      if (slottableElement.type !== React7.Fragment) {
        mergedProps.ref = forwardedRef ? composedRef : slottableElementRef;
      }
      return React7.cloneElement(slottableElement, mergedProps);
    });
    Slot2.displayName = `${ownerName}.Slot`;
    return Slot2;
  }
  __name7(createSlot, "createSlot");
  var SLOTTABLE_IDENTIFIER = /* @__PURE__ */ Symbol.for("radix.slottable");
  // @__NO_SIDE_EFFECTS__
  function createSlottable(ownerName) {
    const Slottable2 = /* @__PURE__ */ __name7((props) => "child" in props ? props.children(props.child) : props.children, "Slottable");
    Slottable2.displayName = `${ownerName}.Slottable`;
    Slottable2.__radixId = SLOTTABLE_IDENTIFIER;
    return Slottable2;
  }
  __name7(createSlottable, "createSlottable");
  var getSlottableElementFromSlottable = /* @__PURE__ */ __name7((slottable, child) => {
    if ("child" in slottable.props) {
      const child2 = slottable.props.child;
      if (!React7.isValidElement(child2)) return null;
      return React7.cloneElement(child2, void 0, slottable.props.children(child2.props.children));
    }
    return React7.isValidElement(child) ? child : null;
  }, "getSlottableElementFromSlottable");
  function mergeProps(slotProps, childProps) {
    const overrideProps = { ...childProps };
    for (const propName in childProps) {
      const slotPropValue = slotProps[propName];
      const childPropValue = childProps[propName];
      const isHandler = /^on[A-Z]/.test(propName);
      if (isHandler) {
        if (slotPropValue && childPropValue) {
          overrideProps[propName] = (...args) => {
            const result = childPropValue(...args);
            slotPropValue(...args);
            return result;
          };
        } else if (slotPropValue) {
          overrideProps[propName] = slotPropValue;
        }
      } else if (propName === "style") {
        overrideProps[propName] = { ...slotPropValue, ...childPropValue };
      } else if (propName === "className") {
        overrideProps[propName] = [slotPropValue, childPropValue].filter(Boolean).join(" ");
      }
    }
    return { ...slotProps, ...overrideProps };
  }
  __name7(mergeProps, "mergeProps");
  function getElementRef(element) {
    let getter = Object.getOwnPropertyDescriptor(element.props, "ref")?.get;
    let mayWarn = getter && "isReactWarning" in getter && getter.isReactWarning;
    if (mayWarn) {
      return element.ref;
    }
    getter = Object.getOwnPropertyDescriptor(element, "ref")?.get;
    mayWarn = getter && "isReactWarning" in getter && getter.isReactWarning;
    if (mayWarn) {
      return element.props.ref;
    }
    return element.props.ref || element.ref;
  }
  __name7(getElementRef, "getElementRef");
  function isSlottable(child) {
    return React7.isValidElement(child) && typeof child.type === "function" && "__radixId" in child.type && child.type.__radixId === SLOTTABLE_IDENTIFIER;
  }
  __name7(isSlottable, "isSlottable");
  var REACT_LAZY_TYPE = /* @__PURE__ */ Symbol.for("react.lazy");
  function isLazyComponent(element) {
    return element != null && typeof element === "object" && "$$typeof" in element && element.$$typeof === REACT_LAZY_TYPE && "_payload" in element && isPromiseLike(element._payload);
  }
  __name7(isLazyComponent, "isLazyComponent");
  function isPromiseLike(value) {
    return typeof value === "object" && value !== null && "then" in value;
  }
  __name7(isPromiseLike, "isPromiseLike");
  var createSlotError = /* @__PURE__ */ __name7((ownerName) => {
    return `${ownerName} failed to slot onto its children. Expected a single React element child or \`Slottable\`.`;
  }, "createSlotError");
  var createSlottableError = /* @__PURE__ */ __name7((ownerName) => {
    return `${ownerName} failed to slot onto its \`Slottable\`. Expected \`Slottable\` to receive a single React element child.`;
  }, "createSlottableError");
  var use = React7[" use ".trim().toString()];

  // node_modules/.store/@radix-ui/react-primitive@2.1.10-zeSsoG72bCPsw9Cm9y8JEw/node_modules/@radix-ui/react-primitive/dist/index.mjs
  var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
  var __defProp9 = Object.defineProperty;
  var __name8 = (target, value) => __defProp9(target, "name", { value, configurable: true });
  var NODES = [
    "a",
    "button",
    "div",
    "form",
    "h2",
    "h3",
    "img",
    "input",
    "label",
    "li",
    "nav",
    "ol",
    "p",
    "select",
    "span",
    "svg",
    "ul"
  ];
  var Primitive = NODES.reduce((primitive, node) => {
    const Slot2 = createSlot(`Primitive.${node}`);
    const Node2 = React8.forwardRef((props, forwardedRef) => {
      const { asChild, ...primitiveProps } = props;
      const Comp = asChild ? Slot2 : node;
      if (typeof window !== "undefined") {
        window[/* @__PURE__ */ Symbol.for("radix-ui")] = true;
      }
      return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(Comp, { ...primitiveProps, ref: forwardedRef });
    });
    Node2.displayName = `Primitive.${node}`;
    return { ...primitive, [node]: Node2 };
  }, {});
  function dispatchDiscreteCustomEvent(target, event) {
    if (target) ReactDOM.flushSync(() => target.dispatchEvent(event));
  }
  __name8(dispatchDiscreteCustomEvent, "dispatchDiscreteCustomEvent");

  // node_modules/.store/@radix-ui/react-use-callback-ref@1.1.4-e0xqDXwla-RxBDlP4otgfA/node_modules/@radix-ui/react-use-callback-ref/dist/index.mjs
  var React9 = __toESM(require_react(), 1);
  var __defProp10 = Object.defineProperty;
  var __name9 = (target, value) => __defProp10(target, "name", { value, configurable: true });
  function useCallbackRef(callback) {
    const callbackRef = React9.useRef(callback);
    React9.useEffect(() => {
      callbackRef.current = callback;
    });
    return React9.useMemo(() => ((...args) => callbackRef.current?.(...args)), []);
  }
  __name9(useCallbackRef, "useCallbackRef");

  // node_modules/.store/@radix-ui/react-dismissable-layer@1.1.19-TH-TBgk6yZ9haPsxC6i4zw/node_modules/@radix-ui/react-dismissable-layer/dist/index.mjs
  var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
  var __defProp11 = Object.defineProperty;
  var __name10 = (target, value) => __defProp11(target, "name", { value, configurable: true });
  var CONTEXT_UPDATE = "dismissableLayer.update";
  var POINTER_DOWN_OUTSIDE = "dismissableLayer.pointerDownOutside";
  var FOCUS_OUTSIDE = "dismissableLayer.focusOutside";
  var originalBodyPointerEvents;
  var DismissableLayerContext = React10.createContext({
    layers: /* @__PURE__ */ new Set(),
    layersWithOutsidePointerEventsDisabled: /* @__PURE__ */ new Set(),
    branches: /* @__PURE__ */ new Set(),
    // Outside elements that belong to a layer's own dismiss affordance (eg, a
    // dialog overlay). Pressing them should dismiss the layer regardless of
    // whether or not they stop propagation.
    //
    // See https://github.com/radix-ui/primitives/issues/3346
    dismissableSurfaces: /* @__PURE__ */ new Set()
  });
  var DismissableLayer = /* @__PURE__ */ React10.forwardRef(
    // blank line to reduce diff noise
    /* @__PURE__ */ __name10(function DismissableLayer2(props, forwardedRef) {
      const {
        disableOutsidePointerEvents = false,
        deferPointerDownOutside = false,
        onEscapeKeyDown,
        onPointerDownOutside,
        onFocusOutside,
        onInteractOutside,
        onDismiss,
        ...layerProps
      } = props;
      const context2 = React10.useContext(DismissableLayerContext);
      const [node, setNode] = React10.useState(null);
      const ownerDocument = node?.ownerDocument ?? globalThis?.document;
      const [, force] = React10.useState({});
      const composedRefs = useComposedRefs(forwardedRef, setNode);
      const layers = Array.from(context2.layers);
      const [highestLayerWithOutsidePointerEventsDisabled] = [
        ...context2.layersWithOutsidePointerEventsDisabled
      ].slice(-1);
      const highestLayerWithOutsidePointerEventsDisabledIndex = highestLayerWithOutsidePointerEventsDisabled ? layers.indexOf(highestLayerWithOutsidePointerEventsDisabled) : -1;
      const index = node ? layers.indexOf(node) : -1;
      const isBodyPointerEventsDisabled = context2.layersWithOutsidePointerEventsDisabled.size > 0;
      const isPointerEventsEnabled = index >= highestLayerWithOutsidePointerEventsDisabledIndex;
      const isDeferredPointerDownOutsideRef = React10.useRef(false);
      const pointerDownOutside = usePointerDownOutside(
        (event) => {
          onPointerDownOutside?.(event);
          onInteractOutside?.(event);
          if (!event.defaultPrevented) onDismiss?.();
        },
        {
          ownerDocument,
          deferPointerDownOutside,
          isDeferredPointerDownOutsideRef,
          dismissableSurfaces: context2.dismissableSurfaces,
          shouldHandlePointerDownOutside: React10.useCallback(
            (target) => {
              if (!(target instanceof Node)) {
                return false;
              }
              const isPointerDownOnBranch = [...context2.branches].some(
                (branch) => branch.contains(target)
              );
              return isPointerEventsEnabled && !isPointerDownOnBranch;
            },
            [context2.branches, isPointerEventsEnabled]
          )
        }
      );
      const focusOutside = useFocusOutside((event) => {
        if (deferPointerDownOutside && isDeferredPointerDownOutsideRef.current) {
          return;
        }
        const target = event.target;
        const isFocusInBranch = [...context2.branches].some((branch) => branch.contains(target));
        if (isFocusInBranch) return;
        onFocusOutside?.(event);
        onInteractOutside?.(event);
        if (!event.defaultPrevented) onDismiss?.();
      }, ownerDocument);
      const isHighestLayer = node ? index === layers.length - 1 : false;
      const handleKeyDown = useCallbackRef((event) => {
        if (event.key !== "Escape") {
          return;
        }
        onEscapeKeyDown?.(event);
        if (!event.defaultPrevented && onDismiss) {
          event.preventDefault();
          onDismiss();
        }
      });
      React10.useEffect(() => {
        if (!isHighestLayer) {
          return;
        }
        ownerDocument.addEventListener("keydown", handleKeyDown, { capture: true });
        return () => ownerDocument.removeEventListener("keydown", handleKeyDown, { capture: true });
      }, [ownerDocument, isHighestLayer, handleKeyDown]);
      React10.useEffect(() => {
        if (!node) return;
        if (disableOutsidePointerEvents) {
          if (context2.layersWithOutsidePointerEventsDisabled.size === 0) {
            originalBodyPointerEvents = ownerDocument.body.style.pointerEvents;
            ownerDocument.body.style.pointerEvents = "none";
          }
          context2.layersWithOutsidePointerEventsDisabled.add(node);
        }
        context2.layers.add(node);
        dispatchUpdate();
        return () => {
          if (disableOutsidePointerEvents) {
            context2.layersWithOutsidePointerEventsDisabled.delete(node);
            if (context2.layersWithOutsidePointerEventsDisabled.size === 0) {
              ownerDocument.body.style.pointerEvents = originalBodyPointerEvents;
            }
          }
        };
      }, [node, ownerDocument, disableOutsidePointerEvents, context2]);
      React10.useEffect(() => {
        return () => {
          if (!node) return;
          context2.layers.delete(node);
          context2.layersWithOutsidePointerEventsDisabled.delete(node);
          dispatchUpdate();
        };
      }, [node, context2]);
      React10.useEffect(() => {
        const handleUpdate = /* @__PURE__ */ __name10(() => force({}), "handleUpdate");
        document.addEventListener(CONTEXT_UPDATE, handleUpdate);
        return () => document.removeEventListener(CONTEXT_UPDATE, handleUpdate);
      }, []);
      return /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
        Primitive.div,
        {
          ...layerProps,
          ref: composedRefs,
          style: {
            pointerEvents: isBodyPointerEventsDisabled ? isPointerEventsEnabled ? "auto" : "none" : void 0,
            ...props.style
          },
          onFocusCapture: composeEventHandlers(props.onFocusCapture, focusOutside.onFocusCapture),
          onBlurCapture: composeEventHandlers(props.onBlurCapture, focusOutside.onBlurCapture),
          onPointerDownCapture: composeEventHandlers(
            props.onPointerDownCapture,
            pointerDownOutside.onPointerDownCapture
          )
        }
      );
    }, "DismissableLayer")
  );
  function useDismissableLayerSurface() {
    const context2 = React10.useContext(DismissableLayerContext);
    const [node, setNode] = React10.useState(null);
    React10.useEffect(() => {
      if (!node) {
        return;
      }
      context2.dismissableSurfaces.add(node);
      return () => {
        context2.dismissableSurfaces.delete(node);
      };
    }, [node, context2.dismissableSurfaces]);
    return setNode;
  }
  __name10(useDismissableLayerSurface, "useDismissableLayerSurface");
  var IS_TRUE = /* @__PURE__ */ __name10(() => true, "IS_TRUE");
  function usePointerDownOutside(onPointerDownOutside, args) {
    const {
      ownerDocument = globalThis?.document,
      deferPointerDownOutside = false,
      isDeferredPointerDownOutsideRef,
      dismissableSurfaces,
      shouldHandlePointerDownOutside = IS_TRUE
    } = args;
    const handlePointerDownOutside = useCallbackRef(onPointerDownOutside);
    const isPointerInsideReactTreeRef = React10.useRef(false);
    const isPointerDownOutsideRef = React10.useRef(false);
    const interceptedOutsideInteractionEventsRef = React10.useRef(/* @__PURE__ */ new Map());
    const handleClickRef = React10.useRef(() => {
    });
    React10.useEffect(() => {
      function resetOutsideInteraction() {
        isPointerDownOutsideRef.current = false;
        isDeferredPointerDownOutsideRef.current = false;
        interceptedOutsideInteractionEventsRef.current.clear();
      }
      __name10(resetOutsideInteraction, "resetOutsideInteraction");
      function isOutsideInteractionIntercepted() {
        return Array.from(interceptedOutsideInteractionEventsRef.current.values()).some(Boolean);
      }
      __name10(isOutsideInteractionIntercepted, "isOutsideInteractionIntercepted");
      function handleInteractionCapture(event) {
        if (!isPointerDownOutsideRef.current) {
          return;
        }
        const target = event.target;
        const isDismissableSurface = target instanceof Node && [...dismissableSurfaces].some((surface) => surface.contains(target));
        if (!isDismissableSurface) {
          interceptedOutsideInteractionEventsRef.current.set(event.type, true);
        }
        if (event.type === "click") {
          window.setTimeout(() => {
            if (isPointerDownOutsideRef.current) {
              handleClickRef.current();
            }
          }, 0);
        }
      }
      __name10(handleInteractionCapture, "handleInteractionCapture");
      function handleInteractionBubble(event) {
        if (isPointerDownOutsideRef.current) {
          interceptedOutsideInteractionEventsRef.current.set(event.type, false);
        }
      }
      __name10(handleInteractionBubble, "handleInteractionBubble");
      const handlePointerDown = /* @__PURE__ */ __name10((event) => {
        if (event.target && !isPointerInsideReactTreeRef.current) {
          let handleAndDispatchPointerDownOutsideEvent2 = function() {
            ownerDocument.removeEventListener("click", handleClickRef.current);
            const wasOutsideInteractionIntercepted = isOutsideInteractionIntercepted();
            resetOutsideInteraction();
            if (!wasOutsideInteractionIntercepted) {
              handleAndDispatchCustomEvent(
                POINTER_DOWN_OUTSIDE,
                handlePointerDownOutside,
                eventDetail,
                { discrete: true }
              );
            }
          };
          var handleAndDispatchPointerDownOutsideEvent = handleAndDispatchPointerDownOutsideEvent2;
          __name10(handleAndDispatchPointerDownOutsideEvent2, "handleAndDispatchPointerDownOutsideEvent");
          if (!shouldHandlePointerDownOutside(event.target)) {
            ownerDocument.removeEventListener("click", handleClickRef.current);
            resetOutsideInteraction();
            isPointerInsideReactTreeRef.current = false;
            return;
          }
          const eventDetail = { originalEvent: event };
          isPointerDownOutsideRef.current = true;
          isDeferredPointerDownOutsideRef.current = deferPointerDownOutside && event.button === 0;
          interceptedOutsideInteractionEventsRef.current.clear();
          if (!deferPointerDownOutside || event.button !== 0) {
            handleAndDispatchPointerDownOutsideEvent2();
          } else {
            ownerDocument.removeEventListener("click", handleClickRef.current);
            handleClickRef.current = handleAndDispatchPointerDownOutsideEvent2;
            ownerDocument.addEventListener("click", handleClickRef.current, { once: true });
          }
        } else {
          ownerDocument.removeEventListener("click", handleClickRef.current);
          resetOutsideInteraction();
        }
        isPointerInsideReactTreeRef.current = false;
      }, "handlePointerDown");
      const outsideInteractionEvents = [
        "pointerup",
        "mousedown",
        "mouseup",
        "touchstart",
        "touchend",
        "click"
      ];
      for (const eventName of outsideInteractionEvents) {
        ownerDocument.addEventListener(eventName, handleInteractionCapture, true);
        ownerDocument.addEventListener(eventName, handleInteractionBubble);
      }
      const timerId = window.setTimeout(() => {
        ownerDocument.addEventListener("pointerdown", handlePointerDown);
      }, 0);
      return () => {
        window.clearTimeout(timerId);
        ownerDocument.removeEventListener("pointerdown", handlePointerDown);
        ownerDocument.removeEventListener("click", handleClickRef.current);
        for (const eventName of outsideInteractionEvents) {
          ownerDocument.removeEventListener(eventName, handleInteractionCapture, true);
          ownerDocument.removeEventListener(eventName, handleInteractionBubble);
        }
      };
    }, [
      ownerDocument,
      handlePointerDownOutside,
      deferPointerDownOutside,
      isDeferredPointerDownOutsideRef,
      dismissableSurfaces,
      shouldHandlePointerDownOutside
    ]);
    return {
      // ensures we check React component tree (not just DOM tree)
      onPointerDownCapture: /* @__PURE__ */ __name10(() => isPointerInsideReactTreeRef.current = true, "onPointerDownCapture")
    };
  }
  __name10(usePointerDownOutside, "usePointerDownOutside");
  function useFocusOutside(onFocusOutside, ownerDocument = globalThis?.document) {
    const handleFocusOutside = useCallbackRef(onFocusOutside);
    const isFocusInsideReactTreeRef = React10.useRef(false);
    React10.useEffect(() => {
      const handleFocus = /* @__PURE__ */ __name10((event) => {
        if (event.target && !isFocusInsideReactTreeRef.current) {
          const eventDetail = { originalEvent: event };
          handleAndDispatchCustomEvent(FOCUS_OUTSIDE, handleFocusOutside, eventDetail, {
            discrete: false
          });
        }
      }, "handleFocus");
      ownerDocument.addEventListener("focusin", handleFocus);
      return () => ownerDocument.removeEventListener("focusin", handleFocus);
    }, [ownerDocument, handleFocusOutside]);
    return {
      onFocusCapture: /* @__PURE__ */ __name10(() => isFocusInsideReactTreeRef.current = true, "onFocusCapture"),
      onBlurCapture: /* @__PURE__ */ __name10(() => isFocusInsideReactTreeRef.current = false, "onBlurCapture")
    };
  }
  __name10(useFocusOutside, "useFocusOutside");
  function dispatchUpdate() {
    const event = new CustomEvent(CONTEXT_UPDATE);
    document.dispatchEvent(event);
  }
  __name10(dispatchUpdate, "dispatchUpdate");
  function handleAndDispatchCustomEvent(name, handler, detail, { discrete }) {
    const target = detail.originalEvent.target;
    const event = new CustomEvent(name, { bubbles: false, cancelable: true, detail });
    if (handler) target.addEventListener(name, handler, { once: true });
    if (discrete) {
      dispatchDiscreteCustomEvent(target, event);
    } else {
      target.dispatchEvent(event);
    }
  }
  __name10(handleAndDispatchCustomEvent, "handleAndDispatchCustomEvent");

  // node_modules/.store/@radix-ui/react-focus-scope@1.1.16-YPQp7Tv7wxmVoy-hNvj09g/node_modules/@radix-ui/react-focus-scope/dist/index.mjs
  var React11 = __toESM(require_react(), 1);
  var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
  var __defProp12 = Object.defineProperty;
  var __name11 = (target, value) => __defProp12(target, "name", { value, configurable: true });
  var AUTOFOCUS_ON_MOUNT = "focusScope.autoFocusOnMount";
  var AUTOFOCUS_ON_UNMOUNT = "focusScope.autoFocusOnUnmount";
  var EVENT_OPTIONS = { bubbles: false, cancelable: true };
  var FocusScope = /* @__PURE__ */ React11.forwardRef(
    /* @__PURE__ */ __name11(function FocusScope2(props, forwardedRef) {
      const {
        loop = false,
        trapped = false,
        onMountAutoFocus: onMountAutoFocusProp,
        onUnmountAutoFocus: onUnmountAutoFocusProp,
        ...scopeProps
      } = props;
      const [container, setContainer] = React11.useState(null);
      const onMountAutoFocus = useCallbackRef(onMountAutoFocusProp);
      const onUnmountAutoFocus = useCallbackRef(onUnmountAutoFocusProp);
      const lastFocusedElementRef = React11.useRef(null);
      const composedRefs = useComposedRefs(forwardedRef, setContainer);
      const focusScope = React11.useRef({
        paused: false,
        pause() {
          this.paused = true;
        },
        resume() {
          this.paused = false;
        }
      }).current;
      React11.useEffect(() => {
        if (trapped) {
          let handleFocusIn2 = function(event) {
            if (focusScope.paused || !container) return;
            const target = event.target;
            if (container.contains(target)) {
              lastFocusedElementRef.current = target;
            } else {
              focus(lastFocusedElementRef.current, { select: true });
            }
          }, handleFocusOut2 = function(event) {
            if (focusScope.paused || !container) return;
            const relatedTarget = event.relatedTarget;
            if (relatedTarget === null) return;
            if (!container.contains(relatedTarget)) {
              focus(lastFocusedElementRef.current, { select: true });
            }
          }, handleMutations2 = function(mutations) {
            const focusedElement = document.activeElement;
            if (focusedElement !== document.body) return;
            for (const mutation of mutations) {
              if (mutation.removedNodes.length > 0) focus(container);
            }
          };
          var handleFocusIn = handleFocusIn2, handleFocusOut = handleFocusOut2, handleMutations = handleMutations2;
          __name11(handleFocusIn2, "handleFocusIn");
          __name11(handleFocusOut2, "handleFocusOut");
          __name11(handleMutations2, "handleMutations");
          document.addEventListener("focusin", handleFocusIn2);
          document.addEventListener("focusout", handleFocusOut2);
          const mutationObserver = new MutationObserver(handleMutations2);
          if (container) mutationObserver.observe(container, { childList: true, subtree: true });
          return () => {
            document.removeEventListener("focusin", handleFocusIn2);
            document.removeEventListener("focusout", handleFocusOut2);
            mutationObserver.disconnect();
          };
        }
      }, [trapped, container, focusScope.paused]);
      React11.useEffect(() => {
        if (container) {
          focusScopesStack.add(focusScope);
          const previouslyFocusedElement = document.activeElement;
          const hasFocusedCandidate = container.contains(previouslyFocusedElement);
          if (!hasFocusedCandidate) {
            const mountEvent = new CustomEvent(AUTOFOCUS_ON_MOUNT, EVENT_OPTIONS);
            container.addEventListener(AUTOFOCUS_ON_MOUNT, onMountAutoFocus);
            container.dispatchEvent(mountEvent);
            if (!mountEvent.defaultPrevented) {
              focusFirst(removeLinks(getTabbableCandidates(container)), { select: true });
              if (document.activeElement === previouslyFocusedElement) {
                focus(container);
              }
            }
          }
          return () => {
            container.removeEventListener(AUTOFOCUS_ON_MOUNT, onMountAutoFocus);
            setTimeout(() => {
              const unmountEvent = new CustomEvent(AUTOFOCUS_ON_UNMOUNT, EVENT_OPTIONS);
              container.addEventListener(AUTOFOCUS_ON_UNMOUNT, onUnmountAutoFocus);
              container.dispatchEvent(unmountEvent);
              if (!unmountEvent.defaultPrevented) {
                focus(previouslyFocusedElement ?? document.body, { select: true });
              }
              container.removeEventListener(AUTOFOCUS_ON_UNMOUNT, onUnmountAutoFocus);
              focusScopesStack.remove(focusScope);
            }, 0);
          };
        }
      }, [container, onMountAutoFocus, onUnmountAutoFocus, focusScope]);
      const handleKeyDown = React11.useCallback(
        (event) => {
          if (!loop && !trapped) return;
          if (focusScope.paused) return;
          const isTabKey = event.key === "Tab" && !event.altKey && !event.ctrlKey && !event.metaKey;
          const focusedElement = document.activeElement;
          if (isTabKey && focusedElement) {
            const container2 = event.currentTarget;
            const [first, last] = getTabbableEdges(container2);
            const hasTabbableElementsInside = first && last;
            if (!hasTabbableElementsInside) {
              if (focusedElement === container2) event.preventDefault();
            } else {
              if (!event.shiftKey && focusedElement === last) {
                event.preventDefault();
                if (loop) focus(first, { select: true });
              } else if (event.shiftKey && focusedElement === first) {
                event.preventDefault();
                if (loop) focus(last, { select: true });
              }
            }
          }
        },
        [loop, trapped, focusScope.paused]
      );
      return /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(Primitive.div, { tabIndex: -1, ...scopeProps, ref: composedRefs, onKeyDown: handleKeyDown });
    }, "FocusScope")
  );
  function focusFirst(candidates, { select = false } = {}) {
    const previouslyFocusedElement = document.activeElement;
    for (const candidate of candidates) {
      focus(candidate, { select });
      if (document.activeElement !== previouslyFocusedElement) return;
    }
  }
  __name11(focusFirst, "focusFirst");
  function getTabbableEdges(container) {
    const candidates = getTabbableCandidates(container);
    const first = findVisible(candidates, container);
    const last = findVisible(candidates.reverse(), container);
    return [first, last];
  }
  __name11(getTabbableEdges, "getTabbableEdges");
  function getTabbableCandidates(container) {
    const nodes = [];
    const walker = document.createTreeWalker(container, NodeFilter.SHOW_ELEMENT, {
      acceptNode: /* @__PURE__ */ __name11((node) => {
        const isHiddenInput = node.tagName === "INPUT" && node.type === "hidden";
        if (node.disabled || node.hidden || isHiddenInput) return NodeFilter.FILTER_SKIP;
        return node.tabIndex >= 0 ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_SKIP;
      }, "acceptNode")
    });
    while (walker.nextNode()) nodes.push(walker.currentNode);
    return nodes;
  }
  __name11(getTabbableCandidates, "getTabbableCandidates");
  function findVisible(elements, container) {
    const canUseCheckVisibility = typeof container.checkVisibility === "function" && container.checkVisibility({ checkVisibilityCSS: true });
    for (const element of elements) {
      const hidden = canUseCheckVisibility ? !element.checkVisibility({ checkVisibilityCSS: true }) : isHidden(element, { upTo: container });
      if (!hidden) {
        return element;
      }
    }
  }
  __name11(findVisible, "findVisible");
  function isHidden(node, { upTo }) {
    if (getComputedStyle(node).visibility === "hidden") return true;
    while (node) {
      if (upTo !== void 0 && node === upTo) return false;
      if (getComputedStyle(node).display === "none") return true;
      node = node.parentElement;
    }
    return false;
  }
  __name11(isHidden, "isHidden");
  function isSelectableInput(element) {
    return element instanceof HTMLInputElement && "select" in element;
  }
  __name11(isSelectableInput, "isSelectableInput");
  function focus(element, { select = false } = {}) {
    if (element && element.focus) {
      const previouslyFocusedElement = document.activeElement;
      element.focus({ preventScroll: true });
      if (element !== previouslyFocusedElement && isSelectableInput(element) && select)
        element.select();
    }
  }
  __name11(focus, "focus");
  var focusScopesStack = createFocusScopesStack();
  function createFocusScopesStack() {
    let stack = [];
    return {
      add(focusScope) {
        const activeFocusScope = stack[0];
        if (focusScope !== activeFocusScope) {
          activeFocusScope?.pause();
        }
        stack = arrayRemove(stack, focusScope);
        stack.unshift(focusScope);
      },
      remove(focusScope) {
        stack = arrayRemove(stack, focusScope);
        stack[0]?.resume();
      }
    };
  }
  __name11(createFocusScopesStack, "createFocusScopesStack");
  function arrayRemove(array, item) {
    const updatedArray = [...array];
    const index = updatedArray.indexOf(item);
    if (index !== -1) {
      updatedArray.splice(index, 1);
    }
    return updatedArray;
  }
  __name11(arrayRemove, "arrayRemove");
  function removeLinks(items) {
    return items.filter((item) => item.tagName !== "A");
  }
  __name11(removeLinks, "removeLinks");

  // node_modules/.store/@radix-ui/react-portal@1.1.17-c107g9qJPrY_oXR9dwaImg/node_modules/@radix-ui/react-portal/dist/index.mjs
  var React12 = __toESM(require_react(), 1);
  var ReactDOM2 = __toESM(require_react_dom(), 1);
  var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
  var __defProp13 = Object.defineProperty;
  var __name12 = (target, value) => __defProp13(target, "name", { value, configurable: true });
  var Portal = /* @__PURE__ */ React12.forwardRef(
    /* @__PURE__ */ __name12(function Portal2(props, forwardedRef) {
      const { container: containerProp, ...portalProps } = props;
      const [mounted, setMounted] = React12.useState(false);
      useLayoutEffect2(() => setMounted(true), []);
      const container = containerProp || mounted && globalThis?.document?.body;
      return container ? ReactDOM2.createPortal(/* @__PURE__ */ (0, import_jsx_runtime5.jsx)(Primitive.div, { ...portalProps, ref: forwardedRef }), container) : null;
    }, "Portal")
  );

  // node_modules/.store/@radix-ui/react-presence@1.1.10-HIhysypzaG_K5sVxhG5Wig/node_modules/@radix-ui/react-presence/dist/index.mjs
  var React23 = __toESM(require_react(), 1);
  var React13 = __toESM(require_react(), 1);
  var __defProp14 = Object.defineProperty;
  var __name13 = (target, value) => __defProp14(target, "name", { value, configurable: true });
  function useStateMachine(initialState, machine) {
    return React13.useReducer((state, event) => {
      const nextState = machine[state][event];
      return nextState ?? state;
    }, initialState);
  }
  __name13(useStateMachine, "useStateMachine");
  var Presence = /* @__PURE__ */ __name13((props) => {
    const { present, children } = props;
    const presence = usePresence(present);
    const child = typeof children === "function" ? children({ present: presence.isPresent }) : React23.Children.only(children);
    const ref = useStableComposedRefs(presence.ref, getElementRef2(child));
    const forceMount = typeof children === "function";
    return forceMount || presence.isPresent ? React23.cloneElement(child, { ref }) : null;
  }, "Presence");
  function usePresence(present) {
    const [node, setNode] = React23.useState();
    const stylesRef = React23.useRef(null);
    const prevPresentRef = React23.useRef(present);
    const prevAnimationNameRef = React23.useRef("none");
    const mountAnimationNameRef = React23.useRef(void 0);
    const initialState = present ? "mounted" : "unmounted";
    const [state, send] = useStateMachine(initialState, {
      mounted: {
        UNMOUNT: "unmounted",
        ANIMATION_OUT: "unmountSuspended"
      },
      unmountSuspended: {
        MOUNT: "mounted",
        ANIMATION_END: "unmounted"
      },
      unmounted: {
        MOUNT: "mounted"
      }
    });
    React23.useEffect(() => {
      if (state === "mounted") {
        prevAnimationNameRef.current = mountAnimationNameRef.current ?? getAnimationName(stylesRef.current);
        mountAnimationNameRef.current = void 0;
      } else {
        prevAnimationNameRef.current = "none";
      }
    }, [state]);
    useLayoutEffect2(() => {
      const styles = stylesRef.current;
      const wasPresent = prevPresentRef.current;
      const hasPresentChanged = wasPresent !== present;
      if (hasPresentChanged) {
        const prevAnimationName = prevAnimationNameRef.current;
        const currentAnimationName = getAnimationName(styles);
        if (present) {
          mountAnimationNameRef.current = currentAnimationName;
          send("MOUNT");
        } else if (currentAnimationName === "none" || styles?.display === "none") {
          send("UNMOUNT");
        } else {
          const isAnimating = prevAnimationName !== currentAnimationName;
          if (wasPresent && isAnimating) {
            send("ANIMATION_OUT");
          } else {
            send("UNMOUNT");
          }
        }
        prevPresentRef.current = present;
      }
    }, [present, send]);
    useLayoutEffect2(() => {
      if (node) {
        let timeoutId;
        const ownerWindow = node.ownerDocument.defaultView ?? window;
        const handleAnimationEnd = /* @__PURE__ */ __name13((event) => {
          const currentAnimationName = getAnimationName(stylesRef.current);
          const isCurrentAnimation = currentAnimationName.includes(CSS.escape(event.animationName));
          if (event.target === node && isCurrentAnimation) {
            send("ANIMATION_END");
            if (!prevPresentRef.current) {
              const currentFillMode = node.style.animationFillMode;
              node.style.animationFillMode = "forwards";
              timeoutId = ownerWindow.setTimeout(() => {
                if (node.style.animationFillMode === "forwards") {
                  node.style.animationFillMode = currentFillMode;
                }
              });
            }
          }
        }, "handleAnimationEnd");
        const handleAnimationStart = /* @__PURE__ */ __name13((event) => {
          if (event.target === node) {
            prevAnimationNameRef.current = getAnimationName(stylesRef.current);
          }
        }, "handleAnimationStart");
        node.addEventListener("animationstart", handleAnimationStart);
        node.addEventListener("animationcancel", handleAnimationEnd);
        node.addEventListener("animationend", handleAnimationEnd);
        return () => {
          ownerWindow.clearTimeout(timeoutId);
          node.removeEventListener("animationstart", handleAnimationStart);
          node.removeEventListener("animationcancel", handleAnimationEnd);
          node.removeEventListener("animationend", handleAnimationEnd);
        };
      } else {
        send("ANIMATION_END");
      }
    }, [node, send]);
    return {
      isPresent: ["mounted", "unmountSuspended"].includes(state),
      ref: React23.useCallback((node2) => {
        if (node2) {
          const styles = getComputedStyle(node2);
          stylesRef.current = styles;
          mountAnimationNameRef.current = getAnimationName(styles);
        } else {
          stylesRef.current = null;
        }
        setNode(node2);
      }, [])
    };
  }
  __name13(usePresence, "usePresence");
  function setRef2(ref, value) {
    if (typeof ref === "function") {
      return ref(value);
    } else if (ref !== null && ref !== void 0) {
      ref.current = value;
    }
  }
  __name13(setRef2, "setRef");
  function useStableComposedRefs(...refs) {
    const refsRef = React23.useRef(refs);
    refsRef.current = refs;
    return React23.useCallback((node) => {
      const currentRefs = refsRef.current;
      let hasCleanup = false;
      const cleanups = currentRefs.map((ref) => {
        const cleanup = setRef2(ref, node);
        if (!hasCleanup && typeof cleanup === "function") {
          hasCleanup = true;
        }
        return cleanup;
      });
      if (hasCleanup) {
        return () => {
          for (let i = 0; i < cleanups.length; i++) {
            const cleanup = cleanups[i];
            if (typeof cleanup === "function") {
              cleanup();
            } else {
              setRef2(currentRefs[i], null);
            }
          }
        };
      }
    }, []);
  }
  __name13(useStableComposedRefs, "useStableComposedRefs");
  function getAnimationName(styles) {
    return styles?.animationName || "none";
  }
  __name13(getAnimationName, "getAnimationName");
  function getElementRef2(element) {
    let getter = Object.getOwnPropertyDescriptor(element.props, "ref")?.get;
    let mayWarn = getter && "isReactWarning" in getter && getter.isReactWarning;
    if (mayWarn) {
      return element.ref;
    }
    getter = Object.getOwnPropertyDescriptor(element, "ref")?.get;
    mayWarn = getter && "isReactWarning" in getter && getter.isReactWarning;
    if (mayWarn) {
      return element.props.ref;
    }
    return element.props.ref || element.ref;
  }
  __name13(getElementRef2, "getElementRef");

  // node_modules/.store/@radix-ui/react-focus-guards@1.1.6-J8NRBkK6-zVq-z3xjKlU6Q/node_modules/@radix-ui/react-focus-guards/dist/index.mjs
  var React14 = __toESM(require_react(), 1);
  var __defProp15 = Object.defineProperty;
  var __name14 = (target, value) => __defProp15(target, "name", { value, configurable: true });
  var count2 = 0;
  var guards = null;
  function FocusGuards(props) {
    useFocusGuards();
    return props.children;
  }
  __name14(FocusGuards, "FocusGuards");
  function useFocusGuards() {
    React14.useEffect(() => {
      if (!guards) {
        guards = { start: createFocusGuard(), end: createFocusGuard() };
      }
      const { start, end } = guards;
      if (document.body.firstElementChild !== start) {
        document.body.insertAdjacentElement("afterbegin", start);
      }
      if (document.body.lastElementChild !== end) {
        document.body.insertAdjacentElement("beforeend", end);
      }
      count2++;
      return () => {
        if (count2 === 1) {
          guards?.start.remove();
          guards?.end.remove();
          guards = null;
        }
        count2 = Math.max(0, count2 - 1);
      };
    }, []);
  }
  __name14(useFocusGuards, "useFocusGuards");
  function createFocusGuard() {
    const element = document.createElement("span");
    element.setAttribute("data-radix-focus-guard", "");
    element.tabIndex = 0;
    element.style.outline = "none";
    element.style.opacity = "0";
    element.style.position = "fixed";
    element.style.pointerEvents = "none";
    return element;
  }
  __name14(createFocusGuard, "createFocusGuard");

  // node_modules/.store/tslib@2.8.1-LT-dxu1EkVy7OUDyVf5glQ/node_modules/tslib/tslib.es6.mjs
  var __assign = function() {
    __assign = Object.assign || function __assign2(t2) {
      for (var s, i = 1, n = arguments.length; i < n; i++) {
        s = arguments[i];
        for (var p2 in s) if (Object.prototype.hasOwnProperty.call(s, p2)) t2[p2] = s[p2];
      }
      return t2;
    };
    return __assign.apply(this, arguments);
  };
  function __rest(s, e) {
    var t2 = {};
    for (var p2 in s) if (Object.prototype.hasOwnProperty.call(s, p2) && e.indexOf(p2) < 0)
      t2[p2] = s[p2];
    if (s != null && typeof Object.getOwnPropertySymbols === "function")
      for (var i = 0, p2 = Object.getOwnPropertySymbols(s); i < p2.length; i++) {
        if (e.indexOf(p2[i]) < 0 && Object.prototype.propertyIsEnumerable.call(s, p2[i]))
          t2[p2[i]] = s[p2[i]];
      }
    return t2;
  }
  function __spreadArray(to, from, pack) {
    if (pack || arguments.length === 2) for (var i = 0, l = from.length, ar; i < l; i++) {
      if (ar || !(i in from)) {
        if (!ar) ar = Array.prototype.slice.call(from, 0, i);
        ar[i] = from[i];
      }
    }
    return to.concat(ar || Array.prototype.slice.call(from));
  }

  // node_modules/.store/react-remove-scroll@2.7.2-g5JjNH6M9u1FDMe3NHg_3Q/node_modules/react-remove-scroll/dist/es2015/Combination.js
  var React21 = __toESM(require_react());

  // node_modules/.store/react-remove-scroll@2.7.2-g5JjNH6M9u1FDMe3NHg_3Q/node_modules/react-remove-scroll/dist/es2015/UI.js
  var React17 = __toESM(require_react());

  // node_modules/.store/react-remove-scroll-bar@2.3.8-4TFmpYw1N_ya2Y7i0xBtxQ/node_modules/react-remove-scroll-bar/dist/es2015/constants.js
  var zeroRightClassName = "right-scroll-bar-position";
  var fullWidthClassName = "width-before-scroll-bar";
  var noScrollbarsClassName = "with-scroll-bars-hidden";
  var removedBarSizeVariable = "--removed-body-scroll-bar-size";

  // node_modules/.store/use-callback-ref@1.3.3-CY49Gx_8xVP_WAvrqWfBkg/node_modules/use-callback-ref/dist/es2015/assignRef.js
  function assignRef(ref, value) {
    if (typeof ref === "function") {
      ref(value);
    } else if (ref) {
      ref.current = value;
    }
    return ref;
  }

  // node_modules/.store/use-callback-ref@1.3.3-CY49Gx_8xVP_WAvrqWfBkg/node_modules/use-callback-ref/dist/es2015/useRef.js
  var import_react = __toESM(require_react());
  function useCallbackRef2(initialValue, callback) {
    var ref = (0, import_react.useState)(function() {
      return {
        // value
        value: initialValue,
        // last callback
        callback,
        // "memoized" public interface
        facade: {
          get current() {
            return ref.value;
          },
          set current(value) {
            var last = ref.value;
            if (last !== value) {
              ref.value = value;
              ref.callback(value, last);
            }
          }
        }
      };
    })[0];
    ref.callback = callback;
    return ref.facade;
  }

  // node_modules/.store/use-callback-ref@1.3.3-CY49Gx_8xVP_WAvrqWfBkg/node_modules/use-callback-ref/dist/es2015/useMergeRef.js
  var React15 = __toESM(require_react());
  var useIsomorphicLayoutEffect = typeof window !== "undefined" ? React15.useLayoutEffect : React15.useEffect;
  var currentValues = /* @__PURE__ */ new WeakMap();
  function useMergeRefs(refs, defaultValue) {
    var callbackRef = useCallbackRef2(defaultValue || null, function(newValue) {
      return refs.forEach(function(ref) {
        return assignRef(ref, newValue);
      });
    });
    useIsomorphicLayoutEffect(function() {
      var oldValue = currentValues.get(callbackRef);
      if (oldValue) {
        var prevRefs_1 = new Set(oldValue);
        var nextRefs_1 = new Set(refs);
        var current_1 = callbackRef.current;
        prevRefs_1.forEach(function(ref) {
          if (!nextRefs_1.has(ref)) {
            assignRef(ref, null);
          }
        });
        nextRefs_1.forEach(function(ref) {
          if (!prevRefs_1.has(ref)) {
            assignRef(ref, current_1);
          }
        });
      }
      currentValues.set(callbackRef, refs);
    }, [refs]);
    return callbackRef;
  }

  // node_modules/.store/use-sidecar@1.1.3-6pQBe5oGIq-z4KtBgLaB5w/node_modules/use-sidecar/dist/es2015/medium.js
  function ItoI(a) {
    return a;
  }
  function innerCreateMedium(defaults, middleware) {
    if (middleware === void 0) {
      middleware = ItoI;
    }
    var buffer = [];
    var assigned = false;
    var medium = {
      read: function() {
        if (assigned) {
          throw new Error("Sidecar: could not `read` from an `assigned` medium. `read` could be used only with `useMedium`.");
        }
        if (buffer.length) {
          return buffer[buffer.length - 1];
        }
        return defaults;
      },
      useMedium: function(data) {
        var item = middleware(data, assigned);
        buffer.push(item);
        return function() {
          buffer = buffer.filter(function(x) {
            return x !== item;
          });
        };
      },
      assignSyncMedium: function(cb) {
        assigned = true;
        while (buffer.length) {
          var cbs = buffer;
          buffer = [];
          cbs.forEach(cb);
        }
        buffer = {
          push: function(x) {
            return cb(x);
          },
          filter: function() {
            return buffer;
          }
        };
      },
      assignMedium: function(cb) {
        assigned = true;
        var pendingQueue = [];
        if (buffer.length) {
          var cbs = buffer;
          buffer = [];
          cbs.forEach(cb);
          pendingQueue = buffer;
        }
        var executeQueue = function() {
          var cbs2 = pendingQueue;
          pendingQueue = [];
          cbs2.forEach(cb);
        };
        var cycle = function() {
          return Promise.resolve().then(executeQueue);
        };
        cycle();
        buffer = {
          push: function(x) {
            pendingQueue.push(x);
            cycle();
          },
          filter: function(filter) {
            pendingQueue = pendingQueue.filter(filter);
            return buffer;
          }
        };
      }
    };
    return medium;
  }
  function createSidecarMedium(options) {
    if (options === void 0) {
      options = {};
    }
    var medium = innerCreateMedium(null);
    medium.options = __assign({ async: true, ssr: false }, options);
    return medium;
  }

  // node_modules/.store/use-sidecar@1.1.3-6pQBe5oGIq-z4KtBgLaB5w/node_modules/use-sidecar/dist/es2015/exports.js
  var React16 = __toESM(require_react());
  var SideCar = function(_a) {
    var sideCar = _a.sideCar, rest = __rest(_a, ["sideCar"]);
    if (!sideCar) {
      throw new Error("Sidecar: please provide `sideCar` property to import the right car");
    }
    var Target = sideCar.read();
    if (!Target) {
      throw new Error("Sidecar medium not found");
    }
    return React16.createElement(Target, __assign({}, rest));
  };
  SideCar.isSideCarExport = true;
  function exportSidecar(medium, exported) {
    medium.useMedium(exported);
    return SideCar;
  }

  // node_modules/.store/react-remove-scroll@2.7.2-g5JjNH6M9u1FDMe3NHg_3Q/node_modules/react-remove-scroll/dist/es2015/medium.js
  var effectCar = createSidecarMedium();

  // node_modules/.store/react-remove-scroll@2.7.2-g5JjNH6M9u1FDMe3NHg_3Q/node_modules/react-remove-scroll/dist/es2015/UI.js
  var nothing = function() {
    return;
  };
  var RemoveScroll = React17.forwardRef(function(props, parentRef) {
    var ref = React17.useRef(null);
    var _a = React17.useState({
      onScrollCapture: nothing,
      onWheelCapture: nothing,
      onTouchMoveCapture: nothing
    }), callbacks = _a[0], setCallbacks = _a[1];
    var forwardProps = props.forwardProps, children = props.children, className = props.className, removeScrollBar = props.removeScrollBar, enabled = props.enabled, shards = props.shards, sideCar = props.sideCar, noRelative = props.noRelative, noIsolation = props.noIsolation, inert = props.inert, allowPinchZoom = props.allowPinchZoom, _b = props.as, Container = _b === void 0 ? "div" : _b, gapMode = props.gapMode, rest = __rest(props, ["forwardProps", "children", "className", "removeScrollBar", "enabled", "shards", "sideCar", "noRelative", "noIsolation", "inert", "allowPinchZoom", "as", "gapMode"]);
    var SideCar2 = sideCar;
    var containerRef = useMergeRefs([ref, parentRef]);
    var containerProps = __assign(__assign({}, rest), callbacks);
    return React17.createElement(
      React17.Fragment,
      null,
      enabled && React17.createElement(SideCar2, { sideCar: effectCar, removeScrollBar, shards, noRelative, noIsolation, inert, setCallbacks, allowPinchZoom: !!allowPinchZoom, lockRef: ref, gapMode }),
      forwardProps ? React17.cloneElement(React17.Children.only(children), __assign(__assign({}, containerProps), { ref: containerRef })) : React17.createElement(Container, __assign({}, containerProps, { className, ref: containerRef }), children)
    );
  });
  RemoveScroll.defaultProps = {
    enabled: true,
    removeScrollBar: true,
    inert: false
  };
  RemoveScroll.classNames = {
    fullWidth: fullWidthClassName,
    zeroRight: zeroRightClassName
  };

  // node_modules/.store/react-remove-scroll@2.7.2-g5JjNH6M9u1FDMe3NHg_3Q/node_modules/react-remove-scroll/dist/es2015/SideEffect.js
  var React20 = __toESM(require_react());

  // node_modules/.store/react-remove-scroll-bar@2.3.8-4TFmpYw1N_ya2Y7i0xBtxQ/node_modules/react-remove-scroll-bar/dist/es2015/component.js
  var React19 = __toESM(require_react());

  // node_modules/.store/react-style-singleton@2.2.3-HhrrM8n2z-BDpx6IHTz-qw/node_modules/react-style-singleton/dist/es2015/hook.js
  var React18 = __toESM(require_react());

  // node_modules/.store/get-nonce@1.0.1-T9SDetHAbMT8POt0PFKPhQ/node_modules/get-nonce/dist/es2015/index.js
  var currentNonce;
  var getNonce = function() {
    if (currentNonce) {
      return currentNonce;
    }
    if (typeof __webpack_nonce__ !== "undefined") {
      return __webpack_nonce__;
    }
    return void 0;
  };

  // node_modules/.store/react-style-singleton@2.2.3-HhrrM8n2z-BDpx6IHTz-qw/node_modules/react-style-singleton/dist/es2015/singleton.js
  function makeStyleTag() {
    if (!document)
      return null;
    var tag = document.createElement("style");
    tag.type = "text/css";
    var nonce = getNonce();
    if (nonce) {
      tag.setAttribute("nonce", nonce);
    }
    return tag;
  }
  function injectStyles(tag, css) {
    if (tag.styleSheet) {
      tag.styleSheet.cssText = css;
    } else {
      tag.appendChild(document.createTextNode(css));
    }
  }
  function insertStyleTag(tag) {
    var head = document.head || document.getElementsByTagName("head")[0];
    head.appendChild(tag);
  }
  var stylesheetSingleton = function() {
    var counter = 0;
    var stylesheet = null;
    return {
      add: function(style) {
        if (counter == 0) {
          if (stylesheet = makeStyleTag()) {
            injectStyles(stylesheet, style);
            insertStyleTag(stylesheet);
          }
        }
        counter++;
      },
      remove: function() {
        counter--;
        if (!counter && stylesheet) {
          stylesheet.parentNode && stylesheet.parentNode.removeChild(stylesheet);
          stylesheet = null;
        }
      }
    };
  };

  // node_modules/.store/react-style-singleton@2.2.3-HhrrM8n2z-BDpx6IHTz-qw/node_modules/react-style-singleton/dist/es2015/hook.js
  var styleHookSingleton = function() {
    var sheet = stylesheetSingleton();
    return function(styles, isDynamic) {
      React18.useEffect(function() {
        sheet.add(styles);
        return function() {
          sheet.remove();
        };
      }, [styles && isDynamic]);
    };
  };

  // node_modules/.store/react-style-singleton@2.2.3-HhrrM8n2z-BDpx6IHTz-qw/node_modules/react-style-singleton/dist/es2015/component.js
  var styleSingleton = function() {
    var useStyle = styleHookSingleton();
    var Sheet = function(_a) {
      var styles = _a.styles, dynamic = _a.dynamic;
      useStyle(styles, dynamic);
      return null;
    };
    return Sheet;
  };

  // node_modules/.store/react-remove-scroll-bar@2.3.8-4TFmpYw1N_ya2Y7i0xBtxQ/node_modules/react-remove-scroll-bar/dist/es2015/utils.js
  var zeroGap = {
    left: 0,
    top: 0,
    right: 0,
    gap: 0
  };
  var parse = function(x) {
    return parseInt(x || "", 10) || 0;
  };
  var getOffset = function(gapMode) {
    var cs = window.getComputedStyle(document.body);
    var left = cs[gapMode === "padding" ? "paddingLeft" : "marginLeft"];
    var top = cs[gapMode === "padding" ? "paddingTop" : "marginTop"];
    var right = cs[gapMode === "padding" ? "paddingRight" : "marginRight"];
    return [parse(left), parse(top), parse(right)];
  };
  var getGapWidth = function(gapMode) {
    if (gapMode === void 0) {
      gapMode = "margin";
    }
    if (typeof window === "undefined") {
      return zeroGap;
    }
    var offsets = getOffset(gapMode);
    var documentWidth = document.documentElement.clientWidth;
    var windowWidth = window.innerWidth;
    return {
      left: offsets[0],
      top: offsets[1],
      right: offsets[2],
      gap: Math.max(0, windowWidth - documentWidth + offsets[2] - offsets[0])
    };
  };

  // node_modules/.store/react-remove-scroll-bar@2.3.8-4TFmpYw1N_ya2Y7i0xBtxQ/node_modules/react-remove-scroll-bar/dist/es2015/component.js
  var Style = styleSingleton();
  var lockAttribute = "data-scroll-locked";
  var getStyles = function(_a, allowRelative, gapMode, important) {
    var left = _a.left, top = _a.top, right = _a.right, gap = _a.gap;
    if (gapMode === void 0) {
      gapMode = "margin";
    }
    return "\n  .".concat(noScrollbarsClassName, " {\n   overflow: hidden ").concat(important, ";\n   padding-right: ").concat(gap, "px ").concat(important, ";\n  }\n  body[").concat(lockAttribute, "] {\n    overflow: hidden ").concat(important, ";\n    overscroll-behavior: contain;\n    ").concat([
      allowRelative && "position: relative ".concat(important, ";"),
      gapMode === "margin" && "\n    padding-left: ".concat(left, "px;\n    padding-top: ").concat(top, "px;\n    padding-right: ").concat(right, "px;\n    margin-left:0;\n    margin-top:0;\n    margin-right: ").concat(gap, "px ").concat(important, ";\n    "),
      gapMode === "padding" && "padding-right: ".concat(gap, "px ").concat(important, ";")
    ].filter(Boolean).join(""), "\n  }\n  \n  .").concat(zeroRightClassName, " {\n    right: ").concat(gap, "px ").concat(important, ";\n  }\n  \n  .").concat(fullWidthClassName, " {\n    margin-right: ").concat(gap, "px ").concat(important, ";\n  }\n  \n  .").concat(zeroRightClassName, " .").concat(zeroRightClassName, " {\n    right: 0 ").concat(important, ";\n  }\n  \n  .").concat(fullWidthClassName, " .").concat(fullWidthClassName, " {\n    margin-right: 0 ").concat(important, ";\n  }\n  \n  body[").concat(lockAttribute, "] {\n    ").concat(removedBarSizeVariable, ": ").concat(gap, "px;\n  }\n");
  };
  var getCurrentUseCounter = function() {
    var counter = parseInt(document.body.getAttribute(lockAttribute) || "0", 10);
    return isFinite(counter) ? counter : 0;
  };
  var useLockAttribute = function() {
    React19.useEffect(function() {
      document.body.setAttribute(lockAttribute, (getCurrentUseCounter() + 1).toString());
      return function() {
        var newCounter = getCurrentUseCounter() - 1;
        if (newCounter <= 0) {
          document.body.removeAttribute(lockAttribute);
        } else {
          document.body.setAttribute(lockAttribute, newCounter.toString());
        }
      };
    }, []);
  };
  var RemoveScrollBar = function(_a) {
    var noRelative = _a.noRelative, noImportant = _a.noImportant, _b = _a.gapMode, gapMode = _b === void 0 ? "margin" : _b;
    useLockAttribute();
    var gap = React19.useMemo(function() {
      return getGapWidth(gapMode);
    }, [gapMode]);
    return React19.createElement(Style, { styles: getStyles(gap, !noRelative, gapMode, !noImportant ? "!important" : "") });
  };

  // node_modules/.store/react-remove-scroll@2.7.2-g5JjNH6M9u1FDMe3NHg_3Q/node_modules/react-remove-scroll/dist/es2015/aggresiveCapture.js
  var passiveSupported = false;
  if (typeof window !== "undefined") {
    try {
      options = Object.defineProperty({}, "passive", {
        get: function() {
          passiveSupported = true;
          return true;
        }
      });
      window.addEventListener("test", options, options);
      window.removeEventListener("test", options, options);
    } catch (err) {
      passiveSupported = false;
    }
  }
  var options;
  var nonPassive = passiveSupported ? { passive: false } : false;

  // node_modules/.store/react-remove-scroll@2.7.2-g5JjNH6M9u1FDMe3NHg_3Q/node_modules/react-remove-scroll/dist/es2015/handleScroll.js
  var alwaysContainsScroll = function(node) {
    return node.tagName === "TEXTAREA";
  };
  var elementCanBeScrolled = function(node, overflow) {
    if (!(node instanceof Element)) {
      return false;
    }
    var styles = window.getComputedStyle(node);
    return (
      // not-not-scrollable
      styles[overflow] !== "hidden" && // contains scroll inside self
      !(styles.overflowY === styles.overflowX && !alwaysContainsScroll(node) && styles[overflow] === "visible")
    );
  };
  var elementCouldBeVScrolled = function(node) {
    return elementCanBeScrolled(node, "overflowY");
  };
  var elementCouldBeHScrolled = function(node) {
    return elementCanBeScrolled(node, "overflowX");
  };
  var locationCouldBeScrolled = function(axis, node) {
    var ownerDocument = node.ownerDocument;
    var current = node;
    do {
      if (typeof ShadowRoot !== "undefined" && current instanceof ShadowRoot) {
        current = current.host;
      }
      var isScrollable = elementCouldBeScrolled(axis, current);
      if (isScrollable) {
        var _a = getScrollVariables(axis, current), scrollHeight = _a[1], clientHeight = _a[2];
        if (scrollHeight > clientHeight) {
          return true;
        }
      }
      current = current.parentNode;
    } while (current && current !== ownerDocument.body);
    return false;
  };
  var getVScrollVariables = function(_a) {
    var scrollTop = _a.scrollTop, scrollHeight = _a.scrollHeight, clientHeight = _a.clientHeight;
    return [
      scrollTop,
      scrollHeight,
      clientHeight
    ];
  };
  var getHScrollVariables = function(_a) {
    var scrollLeft = _a.scrollLeft, scrollWidth = _a.scrollWidth, clientWidth = _a.clientWidth;
    return [
      scrollLeft,
      scrollWidth,
      clientWidth
    ];
  };
  var elementCouldBeScrolled = function(axis, node) {
    return axis === "v" ? elementCouldBeVScrolled(node) : elementCouldBeHScrolled(node);
  };
  var getScrollVariables = function(axis, node) {
    return axis === "v" ? getVScrollVariables(node) : getHScrollVariables(node);
  };
  var getDirectionFactor = function(axis, direction) {
    return axis === "h" && direction === "rtl" ? -1 : 1;
  };
  var handleScroll = function(axis, endTarget, event, sourceDelta, noOverscroll) {
    var directionFactor = getDirectionFactor(axis, window.getComputedStyle(endTarget).direction);
    var delta = directionFactor * sourceDelta;
    var target = event.target;
    var targetInLock = endTarget.contains(target);
    var shouldCancelScroll = false;
    var isDeltaPositive = delta > 0;
    var availableScroll = 0;
    var availableScrollTop = 0;
    do {
      if (!target) {
        break;
      }
      var _a = getScrollVariables(axis, target), position = _a[0], scroll_1 = _a[1], capacity = _a[2];
      var elementScroll = scroll_1 - capacity - directionFactor * position;
      if (position || elementScroll) {
        if (elementCouldBeScrolled(axis, target)) {
          availableScroll += elementScroll;
          availableScrollTop += position;
        }
      }
      var parent_1 = target.parentNode;
      target = parent_1 && parent_1.nodeType === Node.DOCUMENT_FRAGMENT_NODE ? parent_1.host : parent_1;
    } while (
      // portaled content
      !targetInLock && target !== document.body || // self content
      targetInLock && (endTarget.contains(target) || endTarget === target)
    );
    if (isDeltaPositive && (noOverscroll && Math.abs(availableScroll) < 1 || !noOverscroll && delta > availableScroll)) {
      shouldCancelScroll = true;
    } else if (!isDeltaPositive && (noOverscroll && Math.abs(availableScrollTop) < 1 || !noOverscroll && -delta > availableScrollTop)) {
      shouldCancelScroll = true;
    }
    return shouldCancelScroll;
  };

  // node_modules/.store/react-remove-scroll@2.7.2-g5JjNH6M9u1FDMe3NHg_3Q/node_modules/react-remove-scroll/dist/es2015/SideEffect.js
  var getTouchXY = function(event) {
    return "changedTouches" in event ? [event.changedTouches[0].clientX, event.changedTouches[0].clientY] : [0, 0];
  };
  var getDeltaXY = function(event) {
    return [event.deltaX, event.deltaY];
  };
  var extractRef = function(ref) {
    return ref && "current" in ref ? ref.current : ref;
  };
  var deltaCompare = function(x, y) {
    return x[0] === y[0] && x[1] === y[1];
  };
  var generateStyle = function(id) {
    return "\n  .block-interactivity-".concat(id, " {pointer-events: none;}\n  .allow-interactivity-").concat(id, " {pointer-events: all;}\n");
  };
  var idCounter = 0;
  var lockStack = [];
  function RemoveScrollSideCar(props) {
    var shouldPreventQueue = React20.useRef([]);
    var touchStartRef = React20.useRef([0, 0]);
    var activeAxis = React20.useRef();
    var id = React20.useState(idCounter++)[0];
    var Style2 = React20.useState(styleSingleton)[0];
    var lastProps = React20.useRef(props);
    React20.useEffect(function() {
      lastProps.current = props;
    }, [props]);
    React20.useEffect(function() {
      if (props.inert) {
        document.body.classList.add("block-interactivity-".concat(id));
        var allow_1 = __spreadArray([props.lockRef.current], (props.shards || []).map(extractRef), true).filter(Boolean);
        allow_1.forEach(function(el) {
          return el.classList.add("allow-interactivity-".concat(id));
        });
        return function() {
          document.body.classList.remove("block-interactivity-".concat(id));
          allow_1.forEach(function(el) {
            return el.classList.remove("allow-interactivity-".concat(id));
          });
        };
      }
      return;
    }, [props.inert, props.lockRef.current, props.shards]);
    var shouldCancelEvent = React20.useCallback(function(event, parent) {
      if ("touches" in event && event.touches.length === 2 || event.type === "wheel" && event.ctrlKey) {
        return !lastProps.current.allowPinchZoom;
      }
      var touch = getTouchXY(event);
      var touchStart = touchStartRef.current;
      var deltaX = "deltaX" in event ? event.deltaX : touchStart[0] - touch[0];
      var deltaY = "deltaY" in event ? event.deltaY : touchStart[1] - touch[1];
      var currentAxis;
      var target = event.target;
      var moveDirection = Math.abs(deltaX) > Math.abs(deltaY) ? "h" : "v";
      if ("touches" in event && moveDirection === "h" && target.type === "range") {
        return false;
      }
      var selection = window.getSelection();
      var anchorNode = selection && selection.anchorNode;
      var isTouchingSelection = anchorNode ? anchorNode === target || anchorNode.contains(target) : false;
      if (isTouchingSelection) {
        return false;
      }
      var canBeScrolledInMainDirection = locationCouldBeScrolled(moveDirection, target);
      if (!canBeScrolledInMainDirection) {
        return true;
      }
      if (canBeScrolledInMainDirection) {
        currentAxis = moveDirection;
      } else {
        currentAxis = moveDirection === "v" ? "h" : "v";
        canBeScrolledInMainDirection = locationCouldBeScrolled(moveDirection, target);
      }
      if (!canBeScrolledInMainDirection) {
        return false;
      }
      if (!activeAxis.current && "changedTouches" in event && (deltaX || deltaY)) {
        activeAxis.current = currentAxis;
      }
      if (!currentAxis) {
        return true;
      }
      var cancelingAxis = activeAxis.current || currentAxis;
      return handleScroll(cancelingAxis, parent, event, cancelingAxis === "h" ? deltaX : deltaY, true);
    }, []);
    var shouldPrevent = React20.useCallback(function(_event) {
      var event = _event;
      if (!lockStack.length || lockStack[lockStack.length - 1] !== Style2) {
        return;
      }
      var delta = "deltaY" in event ? getDeltaXY(event) : getTouchXY(event);
      var sourceEvent = shouldPreventQueue.current.filter(function(e) {
        return e.name === event.type && (e.target === event.target || event.target === e.shadowParent) && deltaCompare(e.delta, delta);
      })[0];
      if (sourceEvent && sourceEvent.should) {
        if (event.cancelable) {
          event.preventDefault();
        }
        return;
      }
      if (!sourceEvent) {
        var shardNodes = (lastProps.current.shards || []).map(extractRef).filter(Boolean).filter(function(node) {
          return node.contains(event.target);
        });
        var shouldStop = shardNodes.length > 0 ? shouldCancelEvent(event, shardNodes[0]) : !lastProps.current.noIsolation;
        if (shouldStop) {
          if (event.cancelable) {
            event.preventDefault();
          }
        }
      }
    }, []);
    var shouldCancel = React20.useCallback(function(name, delta, target, should) {
      var event = { name, delta, target, should, shadowParent: getOutermostShadowParent(target) };
      shouldPreventQueue.current.push(event);
      setTimeout(function() {
        shouldPreventQueue.current = shouldPreventQueue.current.filter(function(e) {
          return e !== event;
        });
      }, 1);
    }, []);
    var scrollTouchStart = React20.useCallback(function(event) {
      touchStartRef.current = getTouchXY(event);
      activeAxis.current = void 0;
    }, []);
    var scrollWheel = React20.useCallback(function(event) {
      shouldCancel(event.type, getDeltaXY(event), event.target, shouldCancelEvent(event, props.lockRef.current));
    }, []);
    var scrollTouchMove = React20.useCallback(function(event) {
      shouldCancel(event.type, getTouchXY(event), event.target, shouldCancelEvent(event, props.lockRef.current));
    }, []);
    React20.useEffect(function() {
      lockStack.push(Style2);
      props.setCallbacks({
        onScrollCapture: scrollWheel,
        onWheelCapture: scrollWheel,
        onTouchMoveCapture: scrollTouchMove
      });
      document.addEventListener("wheel", shouldPrevent, nonPassive);
      document.addEventListener("touchmove", shouldPrevent, nonPassive);
      document.addEventListener("touchstart", scrollTouchStart, nonPassive);
      return function() {
        lockStack = lockStack.filter(function(inst) {
          return inst !== Style2;
        });
        document.removeEventListener("wheel", shouldPrevent, nonPassive);
        document.removeEventListener("touchmove", shouldPrevent, nonPassive);
        document.removeEventListener("touchstart", scrollTouchStart, nonPassive);
      };
    }, []);
    var removeScrollBar = props.removeScrollBar, inert = props.inert;
    return React20.createElement(
      React20.Fragment,
      null,
      inert ? React20.createElement(Style2, { styles: generateStyle(id) }) : null,
      removeScrollBar ? React20.createElement(RemoveScrollBar, { noRelative: props.noRelative, gapMode: props.gapMode }) : null
    );
  }
  function getOutermostShadowParent(node) {
    var shadowParent = null;
    while (node !== null) {
      if (node instanceof ShadowRoot) {
        shadowParent = node.host;
        node = node.host;
      }
      node = node.parentNode;
    }
    return shadowParent;
  }

  // node_modules/.store/react-remove-scroll@2.7.2-g5JjNH6M9u1FDMe3NHg_3Q/node_modules/react-remove-scroll/dist/es2015/sidecar.js
  var sidecar_default = exportSidecar(effectCar, RemoveScrollSideCar);

  // node_modules/.store/react-remove-scroll@2.7.2-g5JjNH6M9u1FDMe3NHg_3Q/node_modules/react-remove-scroll/dist/es2015/Combination.js
  var ReactRemoveScroll = React21.forwardRef(function(props, ref) {
    return React21.createElement(RemoveScroll, __assign({}, props, { ref, sideCar: sidecar_default }));
  });
  ReactRemoveScroll.classNames = RemoveScroll.classNames;
  var Combination_default = ReactRemoveScroll;

  // node_modules/.store/aria-hidden@1.2.6--GO2Ve_nUkubj48l2T3YZg/node_modules/aria-hidden/dist/es2015/index.js
  var getDefaultParent = function(originalTarget) {
    if (typeof document === "undefined") {
      return null;
    }
    var sampleTarget = Array.isArray(originalTarget) ? originalTarget[0] : originalTarget;
    return sampleTarget.ownerDocument.body;
  };
  var counterMap = /* @__PURE__ */ new WeakMap();
  var uncontrolledNodes = /* @__PURE__ */ new WeakMap();
  var markerMap = {};
  var lockCount = 0;
  var unwrapHost = function(node) {
    return node && (node.host || unwrapHost(node.parentNode));
  };
  var correctTargets = function(parent, targets) {
    return targets.map(function(target) {
      if (parent.contains(target)) {
        return target;
      }
      var correctedTarget = unwrapHost(target);
      if (correctedTarget && parent.contains(correctedTarget)) {
        return correctedTarget;
      }
      console.error("aria-hidden", target, "in not contained inside", parent, ". Doing nothing");
      return null;
    }).filter(function(x) {
      return Boolean(x);
    });
  };
  var applyAttributeToOthers = function(originalTarget, parentNode, markerName, controlAttribute) {
    var targets = correctTargets(parentNode, Array.isArray(originalTarget) ? originalTarget : [originalTarget]);
    if (!markerMap[markerName]) {
      markerMap[markerName] = /* @__PURE__ */ new WeakMap();
    }
    var markerCounter = markerMap[markerName];
    var hiddenNodes = [];
    var elementsToKeep = /* @__PURE__ */ new Set();
    var elementsToStop = new Set(targets);
    var keep = function(el) {
      if (!el || elementsToKeep.has(el)) {
        return;
      }
      elementsToKeep.add(el);
      keep(el.parentNode);
    };
    targets.forEach(keep);
    var deep = function(parent) {
      if (!parent || elementsToStop.has(parent)) {
        return;
      }
      Array.prototype.forEach.call(parent.children, function(node) {
        if (elementsToKeep.has(node)) {
          deep(node);
        } else {
          try {
            var attr = node.getAttribute(controlAttribute);
            var alreadyHidden = attr !== null && attr !== "false";
            var counterValue = (counterMap.get(node) || 0) + 1;
            var markerValue = (markerCounter.get(node) || 0) + 1;
            counterMap.set(node, counterValue);
            markerCounter.set(node, markerValue);
            hiddenNodes.push(node);
            if (counterValue === 1 && alreadyHidden) {
              uncontrolledNodes.set(node, true);
            }
            if (markerValue === 1) {
              node.setAttribute(markerName, "true");
            }
            if (!alreadyHidden) {
              node.setAttribute(controlAttribute, "true");
            }
          } catch (e) {
            console.error("aria-hidden: cannot operate on ", node, e);
          }
        }
      });
    };
    deep(parentNode);
    elementsToKeep.clear();
    lockCount++;
    return function() {
      hiddenNodes.forEach(function(node) {
        var counterValue = counterMap.get(node) - 1;
        var markerValue = markerCounter.get(node) - 1;
        counterMap.set(node, counterValue);
        markerCounter.set(node, markerValue);
        if (!counterValue) {
          if (!uncontrolledNodes.has(node)) {
            node.removeAttribute(controlAttribute);
          }
          uncontrolledNodes.delete(node);
        }
        if (!markerValue) {
          node.removeAttribute(markerName);
        }
      });
      lockCount--;
      if (!lockCount) {
        counterMap = /* @__PURE__ */ new WeakMap();
        counterMap = /* @__PURE__ */ new WeakMap();
        uncontrolledNodes = /* @__PURE__ */ new WeakMap();
        markerMap = {};
      }
    };
  };
  var hideOthers = function(originalTarget, parentNode, markerName) {
    if (markerName === void 0) {
      markerName = "data-aria-hidden";
    }
    var targets = Array.from(Array.isArray(originalTarget) ? originalTarget : [originalTarget]);
    var activeParentNode = parentNode || getDefaultParent(originalTarget);
    if (!activeParentNode) {
      return function() {
        return null;
      };
    }
    targets.push.apply(targets, Array.from(activeParentNode.querySelectorAll("[aria-live], script")));
    return applyAttributeToOthers(targets, activeParentNode, markerName, "aria-hidden");
  };

  // node_modules/.store/@radix-ui/react-dialog@1.1.23-ytDSaPyJAw3b9aD4KkIiOA/node_modules/@radix-ui/react-dialog/dist/index.mjs
  var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
  var __defProp16 = Object.defineProperty;
  var __name15 = (target, value) => __defProp16(target, "name", { value, configurable: true });
  var DIALOG_NAME = "Dialog";
  var [createDialogContext, createDialogScope] = createContextScope(DIALOG_NAME);
  var [DialogProvider, useDialogContext] = createDialogContext(DIALOG_NAME);
  var Dialog = /* @__PURE__ */ __name15((props) => {
    const {
      __scopeDialog,
      children,
      open: openProp,
      defaultOpen,
      onOpenChange,
      modal = true
    } = props;
    const triggerRef = React24.useRef(null);
    const contentRef = React24.useRef(null);
    const [open2, setOpen] = useControllableState({
      prop: openProp,
      defaultProp: defaultOpen ?? false,
      onChange: onOpenChange,
      caller: DIALOG_NAME
    });
    const [titleCount, setTitleCount] = React24.useState(0);
    const [descriptionCount, setDescriptionCount] = React24.useState(0);
    return /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
      DialogProvider,
      {
        scope: __scopeDialog,
        triggerRef,
        contentRef,
        contentId: useId(),
        titleId: useId(),
        descriptionId: useId(),
        titlePresent: titleCount > 0,
        descriptionPresent: descriptionCount > 0,
        setTitleCount,
        setDescriptionCount,
        open: open2,
        onOpenChange: setOpen,
        onOpenToggle: React24.useCallback(() => setOpen((prevOpen) => !prevOpen), [setOpen]),
        modal,
        children
      }
    );
  }, "Dialog");
  var PORTAL_NAME = "DialogPortal";
  var [PortalProvider, usePortalContext] = createDialogContext(PORTAL_NAME, {
    forceMount: void 0
  });
  var DialogPortal = /* @__PURE__ */ __name15((props) => {
    const { __scopeDialog, forceMount, children, container } = props;
    const context2 = useDialogContext(PORTAL_NAME, __scopeDialog);
    return /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(PortalProvider, { scope: __scopeDialog, forceMount, children: React24.Children.map(children, (child) => /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(Presence, { present: forceMount || context2.open, children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(Portal, { asChild: true, container, children: child }) })) });
  }, "DialogPortal");
  var OVERLAY_NAME = "DialogOverlay";
  var DialogOverlay = /* @__PURE__ */ React24.forwardRef(
    /* @__PURE__ */ __name15(function DialogOverlay2(props, forwardedRef) {
      const portalContext = usePortalContext(OVERLAY_NAME, props.__scopeDialog);
      const { forceMount = portalContext.forceMount, ...overlayProps } = props;
      const context2 = useDialogContext(OVERLAY_NAME, props.__scopeDialog);
      return context2.modal ? /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(Presence, { present: forceMount || context2.open, children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(DialogOverlayImpl, { ...overlayProps, ref: forwardedRef }) }) : null;
    }, "DialogOverlay")
  );
  var Slot = createSlot("DialogOverlay.RemoveScroll");
  var DialogOverlayImpl = /* @__PURE__ */ React24.forwardRef(
    // blank line to reduce diff noise
    /* @__PURE__ */ __name15(function DialogOverlayImpl2(props, forwardedRef) {
      const { __scopeDialog, ...overlayProps } = props;
      const context2 = useDialogContext(OVERLAY_NAME, __scopeDialog);
      const registerDismissableSurface = useDismissableLayerSurface();
      const composedRefs = useComposedRefs(forwardedRef, registerDismissableSurface);
      return (
        // Make sure `Content` is scrollable even when it doesn't live inside `RemoveScroll`
        // ie. when `Overlay` and `Content` are siblings
        /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(Combination_default, { as: Slot, allowPinchZoom: true, shards: [context2.contentRef], children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
          Primitive.div,
          {
            "data-state": getState(context2.open),
            ...overlayProps,
            ref: composedRefs,
            style: { pointerEvents: "auto", ...overlayProps.style }
          }
        ) })
      );
    }, "DialogOverlayImpl")
  );
  var CONTENT_NAME = "DialogContent";
  var DialogContent = /* @__PURE__ */ React24.forwardRef(
    /* @__PURE__ */ __name15(function DialogContent2(props, forwardedRef) {
      const portalContext = usePortalContext(CONTENT_NAME, props.__scopeDialog);
      const { forceMount = portalContext.forceMount, ...contentProps } = props;
      const context2 = useDialogContext(CONTENT_NAME, props.__scopeDialog);
      return /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(Presence, { present: forceMount || context2.open, children: context2.modal ? /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(DialogContentModal, { ...contentProps, ref: forwardedRef }) : /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(DialogContentNonModal, { ...contentProps, ref: forwardedRef }) });
    }, "DialogContent")
  );
  var DialogContentModal = /* @__PURE__ */ React24.forwardRef(
    // blank line to reduce diff noise
    /* @__PURE__ */ __name15(function DialogContentModal2(props, forwardedRef) {
      const context2 = useDialogContext(CONTENT_NAME, props.__scopeDialog);
      const contentRef = React24.useRef(null);
      const composedRefs = useComposedRefs(forwardedRef, context2.contentRef, contentRef);
      React24.useEffect(() => {
        const content = contentRef.current;
        if (content) return hideOthers(content);
      }, []);
      return /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
        DialogContentImpl,
        {
          ...props,
          ref: composedRefs,
          trapFocus: context2.open,
          disableOutsidePointerEvents: context2.open,
          onCloseAutoFocus: composeEventHandlers(props.onCloseAutoFocus, (event) => {
            event.preventDefault();
            context2.triggerRef.current?.focus();
          }),
          onPointerDownOutside: composeEventHandlers(props.onPointerDownOutside, (event) => {
            const originalEvent = event.detail.originalEvent;
            const ctrlLeftClick = originalEvent.button === 0 && originalEvent.ctrlKey === true;
            const isRightClick = originalEvent.button === 2 || ctrlLeftClick;
            if (isRightClick) event.preventDefault();
          }),
          onFocusOutside: composeEventHandlers(
            props.onFocusOutside,
            (event) => event.preventDefault()
          )
        }
      );
    }, "DialogContentModal")
  );
  var DialogContentNonModal = /* @__PURE__ */ React24.forwardRef(
    // blank line to reduce diff noise
    /* @__PURE__ */ __name15(function DialogContentNonModal2(props, forwardedRef) {
      const context2 = useDialogContext(CONTENT_NAME, props.__scopeDialog);
      const hasInteractedOutsideRef = React24.useRef(false);
      const hasPointerDownOutsideRef = React24.useRef(false);
      return /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
        DialogContentImpl,
        {
          ...props,
          ref: forwardedRef,
          trapFocus: false,
          disableOutsidePointerEvents: false,
          onCloseAutoFocus: (event) => {
            props.onCloseAutoFocus?.(event);
            if (!event.defaultPrevented) {
              if (!hasInteractedOutsideRef.current) context2.triggerRef.current?.focus();
              event.preventDefault();
            }
            hasInteractedOutsideRef.current = false;
            hasPointerDownOutsideRef.current = false;
          },
          onInteractOutside: (event) => {
            props.onInteractOutside?.(event);
            if (!event.defaultPrevented) {
              hasInteractedOutsideRef.current = true;
              if (event.detail.originalEvent.type === "pointerdown") {
                hasPointerDownOutsideRef.current = true;
              }
            }
            const target = event.target;
            const targetIsTrigger = context2.triggerRef.current?.contains(target);
            if (targetIsTrigger) event.preventDefault();
            if (event.detail.originalEvent.type === "focusin" && hasPointerDownOutsideRef.current) {
              event.preventDefault();
            }
          }
        }
      );
    }, "DialogContentNonModal")
  );
  var DialogContentImpl = /* @__PURE__ */ React24.forwardRef(
    // blank line to reduce diff noise
    /* @__PURE__ */ __name15(function DialogContentImpl2(props, forwardedRef) {
      const { __scopeDialog, trapFocus, onOpenAutoFocus, onCloseAutoFocus, ...contentProps } = props;
      const context2 = useDialogContext(CONTENT_NAME, __scopeDialog);
      useFocusGuards();
      return /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_jsx_runtime6.Fragment, { children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
        FocusScope,
        {
          asChild: true,
          loop: true,
          trapped: trapFocus,
          onMountAutoFocus: onOpenAutoFocus,
          onUnmountAutoFocus: onCloseAutoFocus,
          children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
            DismissableLayer,
            {
              role: "dialog",
              id: context2.contentId,
              "aria-describedby": context2.descriptionPresent ? context2.descriptionId : void 0,
              "aria-labelledby": context2.titlePresent ? context2.titleId : void 0,
              "data-state": getState(context2.open),
              ...contentProps,
              ref: forwardedRef,
              deferPointerDownOutside: true,
              onDismiss: () => context2.onOpenChange(false)
            }
          )
        }
      ) });
    }, "DialogContentImpl")
  );
  function getState(open2) {
    return open2 ? "open" : "closed";
  }
  __name15(getState, "getState");

  // node_modules/.store/cmdk@1.1.1-2SvSjHrFhiqsPDore7Lelw/node_modules/cmdk/dist/index.mjs
  var t = __toESM(require_react(), 1);
  var N = '[cmdk-group=""]';
  var Y2 = '[cmdk-group-items=""]';
  var be = '[cmdk-group-heading=""]';
  var le = '[cmdk-item=""]';
  var ce = `${le}:not([aria-disabled="true"])`;
  var Z = "cmdk-item-select";
  var T = "data-value";
  var Re = (r2, o, n) => W(r2, o, n);
  var ue = t.createContext(void 0);
  var K2 = () => t.useContext(ue);
  var de = t.createContext(void 0);
  var ee = () => t.useContext(de);
  var fe = t.createContext(void 0);
  var me = t.forwardRef((r2, o) => {
    let n = L(() => {
      var e, a;
      return { search: "", value: (a = (e = r2.value) != null ? e : r2.defaultValue) != null ? a : "", selectedItemId: void 0, filtered: { count: 0, items: /* @__PURE__ */ new Map(), groups: /* @__PURE__ */ new Set() } };
    }), u2 = L(() => /* @__PURE__ */ new Set()), c = L(() => /* @__PURE__ */ new Map()), d = L(() => /* @__PURE__ */ new Map()), f = L(() => /* @__PURE__ */ new Set()), p2 = pe(r2), { label: b, children: m2, value: R, onValueChange: x, filter: C, shouldFilter: S, loop: A, disablePointerSelection: ge = false, vimBindings: j = true, ...O } = r2, $2 = useId(), q = useId(), _ = useId(), I = t.useRef(null), v = ke();
    k2(() => {
      if (R !== void 0) {
        let e = R.trim();
        n.current.value = e, E.emit();
      }
    }, [R]), k2(() => {
      v(6, ne);
    }, []);
    let E = t.useMemo(() => ({ subscribe: (e) => (f.current.add(e), () => f.current.delete(e)), snapshot: () => n.current, setState: (e, a, s) => {
      var i, l, g, y;
      if (!Object.is(n.current[e], a)) {
        if (n.current[e] = a, e === "search") J2(), z(), v(1, W2);
        else if (e === "value") {
          if (document.activeElement.hasAttribute("cmdk-input") || document.activeElement.hasAttribute("cmdk-root")) {
            let h = document.getElementById(_);
            h ? h.focus() : (i = document.getElementById($2)) == null || i.focus();
          }
          if (v(7, () => {
            var h;
            n.current.selectedItemId = (h = M()) == null ? void 0 : h.id, E.emit();
          }), s || v(5, ne), ((l = p2.current) == null ? void 0 : l.value) !== void 0) {
            let h = a != null ? a : "";
            (y = (g = p2.current).onValueChange) == null || y.call(g, h);
            return;
          }
        }
        E.emit();
      }
    }, emit: () => {
      f.current.forEach((e) => e());
    } }), []), U2 = t.useMemo(() => ({ value: (e, a, s) => {
      var i;
      a !== ((i = d.current.get(e)) == null ? void 0 : i.value) && (d.current.set(e, { value: a, keywords: s }), n.current.filtered.items.set(e, te(a, s)), v(2, () => {
        z(), E.emit();
      }));
    }, item: (e, a) => (u2.current.add(e), a && (c.current.has(a) ? c.current.get(a).add(e) : c.current.set(a, /* @__PURE__ */ new Set([e]))), v(3, () => {
      J2(), z(), n.current.value || W2(), E.emit();
    }), () => {
      d.current.delete(e), u2.current.delete(e), n.current.filtered.items.delete(e);
      let s = M();
      v(4, () => {
        J2(), (s == null ? void 0 : s.getAttribute("id")) === e && W2(), E.emit();
      });
    }), group: (e) => (c.current.has(e) || c.current.set(e, /* @__PURE__ */ new Set()), () => {
      d.current.delete(e), c.current.delete(e);
    }), filter: () => p2.current.shouldFilter, label: b || r2["aria-label"], getDisablePointerSelection: () => p2.current.disablePointerSelection, listId: $2, inputId: _, labelId: q, listInnerRef: I }), []);
    function te(e, a) {
      var i, l;
      let s = (l = (i = p2.current) == null ? void 0 : i.filter) != null ? l : Re;
      return e ? s(e, n.current.search, a) : 0;
    }
    function z() {
      if (!n.current.search || p2.current.shouldFilter === false) return;
      let e = n.current.filtered.items, a = [];
      n.current.filtered.groups.forEach((i) => {
        let l = c.current.get(i), g = 0;
        l.forEach((y) => {
          let h = e.get(y);
          g = Math.max(h, g);
        }), a.push([i, g]);
      });
      let s = I.current;
      V().sort((i, l) => {
        var h, F;
        let g = i.getAttribute("id"), y = l.getAttribute("id");
        return ((h = e.get(y)) != null ? h : 0) - ((F = e.get(g)) != null ? F : 0);
      }).forEach((i) => {
        let l = i.closest(Y2);
        l ? l.appendChild(i.parentElement === l ? i : i.closest(`${Y2} > *`)) : s.appendChild(i.parentElement === s ? i : i.closest(`${Y2} > *`));
      }), a.sort((i, l) => l[1] - i[1]).forEach((i) => {
        var g;
        let l = (g = I.current) == null ? void 0 : g.querySelector(`${N}[${T}="${encodeURIComponent(i[0])}"]`);
        l == null || l.parentElement.appendChild(l);
      });
    }
    function W2() {
      let e = V().find((s) => s.getAttribute("aria-disabled") !== "true"), a = e == null ? void 0 : e.getAttribute(T);
      E.setState("value", a || void 0);
    }
    function J2() {
      var a, s, i, l;
      if (!n.current.search || p2.current.shouldFilter === false) {
        n.current.filtered.count = u2.current.size;
        return;
      }
      n.current.filtered.groups = /* @__PURE__ */ new Set();
      let e = 0;
      for (let g of u2.current) {
        let y = (s = (a = d.current.get(g)) == null ? void 0 : a.value) != null ? s : "", h = (l = (i = d.current.get(g)) == null ? void 0 : i.keywords) != null ? l : [], F = te(y, h);
        n.current.filtered.items.set(g, F), F > 0 && e++;
      }
      for (let [g, y] of c.current) for (let h of y) if (n.current.filtered.items.get(h) > 0) {
        n.current.filtered.groups.add(g);
        break;
      }
      n.current.filtered.count = e;
    }
    function ne() {
      var a, s, i;
      let e = M();
      e && (((a = e.parentElement) == null ? void 0 : a.firstChild) === e && ((i = (s = e.closest(N)) == null ? void 0 : s.querySelector(be)) == null || i.scrollIntoView({ block: "nearest" })), e.scrollIntoView({ block: "nearest" }));
    }
    function M() {
      var e;
      return (e = I.current) == null ? void 0 : e.querySelector(`${le}[aria-selected="true"]`);
    }
    function V() {
      var e;
      return Array.from(((e = I.current) == null ? void 0 : e.querySelectorAll(ce)) || []);
    }
    function X2(e) {
      let s = V()[e];
      s && E.setState("value", s.getAttribute(T));
    }
    function Q(e) {
      var g;
      let a = M(), s = V(), i = s.findIndex((y) => y === a), l = s[i + e];
      (g = p2.current) != null && g.loop && (l = i + e < 0 ? s[s.length - 1] : i + e === s.length ? s[0] : s[i + e]), l && E.setState("value", l.getAttribute(T));
    }
    function re(e) {
      let a = M(), s = a == null ? void 0 : a.closest(N), i;
      for (; s && !i; ) s = e > 0 ? we(s, N) : De(s, N), i = s == null ? void 0 : s.querySelector(ce);
      i ? E.setState("value", i.getAttribute(T)) : Q(e);
    }
    let oe = () => X2(V().length - 1), ie = (e) => {
      e.preventDefault(), e.metaKey ? oe() : e.altKey ? re(1) : Q(1);
    }, se = (e) => {
      e.preventDefault(), e.metaKey ? X2(0) : e.altKey ? re(-1) : Q(-1);
    };
    return t.createElement(Primitive.div, { ref: o, tabIndex: -1, ...O, "cmdk-root": "", onKeyDown: (e) => {
      var s;
      (s = O.onKeyDown) == null || s.call(O, e);
      let a = e.nativeEvent.isComposing || e.keyCode === 229;
      if (!(e.defaultPrevented || a)) switch (e.key) {
        case "n":
        case "j": {
          j && e.ctrlKey && ie(e);
          break;
        }
        case "ArrowDown": {
          ie(e);
          break;
        }
        case "p":
        case "k": {
          j && e.ctrlKey && se(e);
          break;
        }
        case "ArrowUp": {
          se(e);
          break;
        }
        case "Home": {
          e.preventDefault(), X2(0);
          break;
        }
        case "End": {
          e.preventDefault(), oe();
          break;
        }
        case "Enter": {
          e.preventDefault();
          let i = M();
          if (i) {
            let l = new Event(Z);
            i.dispatchEvent(l);
          }
        }
      }
    } }, t.createElement("label", { "cmdk-label": "", htmlFor: U2.inputId, id: U2.labelId, style: Te }, b), B2(r2, (e) => t.createElement(de.Provider, { value: E }, t.createElement(ue.Provider, { value: U2 }, e))));
  });
  var he = t.forwardRef((r2, o) => {
    var _, I;
    let n = useId(), u2 = t.useRef(null), c = t.useContext(fe), d = K2(), f = pe(r2), p2 = (I = (_ = f.current) == null ? void 0 : _.forceMount) != null ? I : c == null ? void 0 : c.forceMount;
    k2(() => {
      if (!p2) return d.item(n, c == null ? void 0 : c.id);
    }, [p2]);
    let b = ve(n, u2, [r2.value, r2.children, u2], r2.keywords), m2 = ee(), R = P((v) => v.value && v.value === b.current), x = P((v) => p2 || d.filter() === false ? true : v.search ? v.filtered.items.get(n) > 0 : true);
    t.useEffect(() => {
      let v = u2.current;
      if (!(!v || r2.disabled)) return v.addEventListener(Z, C), () => v.removeEventListener(Z, C);
    }, [x, r2.onSelect, r2.disabled]);
    function C() {
      var v, E;
      S(), (E = (v = f.current).onSelect) == null || E.call(v, b.current);
    }
    function S() {
      m2.setState("value", b.current, true);
    }
    if (!x) return null;
    let { disabled: A, value: ge, onSelect: j, forceMount: O, keywords: $2, ...q } = r2;
    return t.createElement(Primitive.div, { ref: composeRefs(u2, o), ...q, id: n, "cmdk-item": "", role: "option", "aria-disabled": !!A, "aria-selected": !!R, "data-disabled": !!A, "data-selected": !!R, onPointerMove: A || d.getDisablePointerSelection() ? void 0 : S, onClick: A ? void 0 : C }, r2.children);
  });
  var Ee = t.forwardRef((r2, o) => {
    let { heading: n, children: u2, forceMount: c, ...d } = r2, f = useId(), p2 = t.useRef(null), b = t.useRef(null), m2 = useId(), R = K2(), x = P((S) => c || R.filter() === false ? true : S.search ? S.filtered.groups.has(f) : true);
    k2(() => R.group(f), []), ve(f, p2, [r2.value, r2.heading, b]);
    let C = t.useMemo(() => ({ id: f, forceMount: c }), [c]);
    return t.createElement(Primitive.div, { ref: composeRefs(p2, o), ...d, "cmdk-group": "", role: "presentation", hidden: x ? void 0 : true }, n && t.createElement("div", { ref: b, "cmdk-group-heading": "", "aria-hidden": true, id: m2 }, n), B2(r2, (S) => t.createElement("div", { "cmdk-group-items": "", role: "group", "aria-labelledby": n ? m2 : void 0 }, t.createElement(fe.Provider, { value: C }, S))));
  });
  var ye = t.forwardRef((r2, o) => {
    let { alwaysRender: n, ...u2 } = r2, c = t.useRef(null), d = P((f) => !f.search);
    return !n && !d ? null : t.createElement(Primitive.div, { ref: composeRefs(c, o), ...u2, "cmdk-separator": "", role: "separator" });
  });
  var Se = t.forwardRef((r2, o) => {
    let { onValueChange: n, ...u2 } = r2, c = r2.value != null, d = ee(), f = P((m2) => m2.search), p2 = P((m2) => m2.selectedItemId), b = K2();
    return t.useEffect(() => {
      r2.value != null && d.setState("search", r2.value);
    }, [r2.value]), t.createElement(Primitive.input, { ref: o, ...u2, "cmdk-input": "", autoComplete: "off", autoCorrect: "off", spellCheck: false, "aria-autocomplete": "list", role: "combobox", "aria-expanded": true, "aria-controls": b.listId, "aria-labelledby": b.labelId, "aria-activedescendant": p2, id: b.inputId, type: "text", value: c ? r2.value : f, onChange: (m2) => {
      c || d.setState("search", m2.target.value), n == null || n(m2.target.value);
    } });
  });
  var Ce = t.forwardRef((r2, o) => {
    let { children: n, label: u2 = "Suggestions", ...c } = r2, d = t.useRef(null), f = t.useRef(null), p2 = P((m2) => m2.selectedItemId), b = K2();
    return t.useEffect(() => {
      if (f.current && d.current) {
        let m2 = f.current, R = d.current, x, C = new ResizeObserver(() => {
          x = requestAnimationFrame(() => {
            let S = m2.offsetHeight;
            R.style.setProperty("--cmdk-list-height", S.toFixed(1) + "px");
          });
        });
        return C.observe(m2), () => {
          cancelAnimationFrame(x), C.unobserve(m2);
        };
      }
    }, []), t.createElement(Primitive.div, { ref: composeRefs(d, o), ...c, "cmdk-list": "", role: "listbox", tabIndex: -1, "aria-activedescendant": p2, "aria-label": u2, id: b.listId }, B2(r2, (m2) => t.createElement("div", { ref: composeRefs(f, b.listInnerRef), "cmdk-list-sizer": "" }, m2)));
  });
  var xe = t.forwardRef((r2, o) => {
    let { open: n, onOpenChange: u2, overlayClassName: c, contentClassName: d, container: f, ...p2 } = r2;
    return t.createElement(Dialog, { open: n, onOpenChange: u2 }, t.createElement(DialogPortal, { container: f }, t.createElement(DialogOverlay, { "cmdk-overlay": "", className: c }), t.createElement(DialogContent, { "aria-label": r2.label, "cmdk-dialog": "", className: d }, t.createElement(me, { ref: o, ...p2 }))));
  });
  var Ie = t.forwardRef((r2, o) => P((u2) => u2.filtered.count === 0) ? t.createElement(Primitive.div, { ref: o, ...r2, "cmdk-empty": "", role: "presentation" }) : null);
  var Pe = t.forwardRef((r2, o) => {
    let { progress: n, children: u2, label: c = "Loading...", ...d } = r2;
    return t.createElement(Primitive.div, { ref: o, ...d, "cmdk-loading": "", role: "progressbar", "aria-valuenow": n, "aria-valuemin": 0, "aria-valuemax": 100, "aria-label": c }, B2(r2, (f) => t.createElement("div", { "aria-hidden": true }, f)));
  });
  var _e = Object.assign(me, { List: Ce, Item: he, Input: Se, Group: Ee, Separator: ye, Dialog: xe, Empty: Ie, Loading: Pe });
  function we(r2, o) {
    let n = r2.nextElementSibling;
    for (; n; ) {
      if (n.matches(o)) return n;
      n = n.nextElementSibling;
    }
  }
  function De(r2, o) {
    let n = r2.previousElementSibling;
    for (; n; ) {
      if (n.matches(o)) return n;
      n = n.previousElementSibling;
    }
  }
  function pe(r2) {
    let o = t.useRef(r2);
    return k2(() => {
      o.current = r2;
    }), o;
  }
  var k2 = typeof window == "undefined" ? t.useEffect : t.useLayoutEffect;
  function L(r2) {
    let o = t.useRef();
    return o.current === void 0 && (o.current = r2()), o;
  }
  function P(r2) {
    let o = ee(), n = () => r2(o.snapshot());
    return t.useSyncExternalStore(o.subscribe, n, n);
  }
  function ve(r2, o, n, u2 = []) {
    let c = t.useRef(), d = K2();
    return k2(() => {
      var b;
      let f = (() => {
        var m2;
        for (let R of n) {
          if (typeof R == "string") return R.trim();
          if (typeof R == "object" && "current" in R) return R.current ? (m2 = R.current.textContent) == null ? void 0 : m2.trim() : c.current;
        }
      })(), p2 = u2.map((m2) => m2.trim());
      d.value(r2, f, p2), (b = o.current) == null || b.setAttribute(T, f), c.current = f;
    }), c;
  }
  var ke = () => {
    let [r2, o] = t.useState(), n = L(() => /* @__PURE__ */ new Map());
    return k2(() => {
      n.current.forEach((u2) => u2()), n.current = /* @__PURE__ */ new Map();
    }, [r2]), (u2, c) => {
      n.current.set(u2, c), o({});
    };
  };
  function Me(r2) {
    let o = r2.type;
    return typeof o == "function" ? o(r2.props) : "render" in o ? o.render(r2.props) : r2;
  }
  function B2({ asChild: r2, children: o }, n) {
    return r2 && t.isValidElement(o) ? t.cloneElement(Me(o), { ref: o.ref }, n(o.props.children)) : n(o);
  }
  var Te = { position: "absolute", width: "1px", height: "1px", padding: "0", margin: "-1px", overflow: "hidden", clip: "rect(0, 0, 0, 0)", whiteSpace: "nowrap", borderWidth: "0" };

  // node_modules/.store/clsx@2.1.1-XOLP1vUL9vEGfpiXm37fAw/node_modules/clsx/dist/clsx.mjs
  function r(e) {
    var t2, f, n = "";
    if ("string" == typeof e || "number" == typeof e) n += e;
    else if ("object" == typeof e) if (Array.isArray(e)) {
      var o = e.length;
      for (t2 = 0; t2 < o; t2++) e[t2] && (f = r(e[t2])) && (n && (n += " "), n += f);
    } else for (f in e) e[f] && (n && (n += " "), n += f);
    return n;
  }
  function clsx() {
    for (var e, t2, f = 0, n = "", o = arguments.length; f < o; f++) (e = arguments[f]) && (t2 = r(e)) && (n && (n += " "), n += t2);
    return n;
  }
  var clsx_default = clsx;

  // packages/commands/build-module/components/command-menu.mjs
  var import_data5 = __toESM(require_data(), 1);
  var import_element3 = __toESM(require_element(), 1);
  var import_i18n = __toESM(require_i18n(), 1);
  var import_components = __toESM(require_components(), 1);
  var import_keyboard_shortcuts = __toESM(require_keyboard_shortcuts(), 1);
  var import_keycodes = __toESM(require_keycodes(), 1);

  // packages/icons/build-module/icon/index.mjs
  var import_element = __toESM(require_element(), 1);
  var icon_default = (0, import_element.forwardRef)(
    ({ icon, size = 24, style, ...props }, ref) => {
      const intrinsicStyle = icon.props.style;
      const mergedStyle = intrinsicStyle || style ? { ...intrinsicStyle, ...style } : void 0;
      return (0, import_element.cloneElement)(icon, {
        width: size,
        height: size,
        ...props,
        // Merge styles so the icon's intrinsic style (e.g. `fill: none` on
        // stroke-based icons) is preserved unless the consumer overrides
        // the same property explicitly.
        ...mergedStyle ? { style: mergedStyle } : {},
        ref
      });
    }
  );

  // packages/icons/build-module/library/arrow-right.mjs
  var import_primitives = __toESM(require_primitives(), 1);
  var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
  var arrow_right_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives.Path, { d: "M4 12L19 12M14 17L19 12L14 7" }) });

  // packages/icons/build-module/library/search.mjs
  var import_primitives2 = __toESM(require_primitives(), 1);
  var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
  var search_default = /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives2.Path, { d: "M5 19L9.28769 14.7123M18.25 11C18.25 13.8995 15.8995 16.25 13 16.25C10.1005 16.25 7.75 13.8995 7.75 11C7.75 8.10051 10.1005 5.75 13 5.75C15.8995 5.75 18.25 8.10051 18.25 11Z" }) });

  // packages/commands/build-module/store/index.mjs
  var import_data3 = __toESM(require_data(), 1);

  // packages/commands/build-module/store/reducer.mjs
  var import_data = __toESM(require_data(), 1);
  function commands(state = {}, action) {
    switch (action.type) {
      case "REGISTER_COMMAND":
        return {
          ...state,
          [action.name]: {
            name: action.name,
            label: action.label,
            searchLabel: action.searchLabel,
            context: action.context,
            category: action.category,
            callback: action.callback,
            icon: action.icon,
            keywords: action.keywords
          }
        };
      case "UNREGISTER_COMMAND": {
        const { [action.name]: _, ...remainingState } = state;
        return remainingState;
      }
    }
    return state;
  }
  function commandLoaders(state = {}, action) {
    switch (action.type) {
      case "REGISTER_COMMAND_LOADER":
        return {
          ...state,
          [action.name]: {
            name: action.name,
            context: action.context,
            category: action.category,
            hook: action.hook
          }
        };
      case "UNREGISTER_COMMAND_LOADER": {
        const { [action.name]: _, ...remainingState } = state;
        return remainingState;
      }
    }
    return state;
  }
  function isOpen(state = false, action) {
    switch (action.type) {
      case "OPEN":
        return true;
      case "CLOSE":
        return false;
    }
    return state;
  }
  function context(state = "root", action) {
    switch (action.type) {
      case "SET_CONTEXT":
        return action.context;
    }
    return state;
  }
  function loaderStates(state = {}, action) {
    switch (action.type) {
      case "SET_LOADER_LOADING": {
        if (action.isLoading) {
          return { ...state, [action.name]: true };
        }
        if (!(action.name in state)) {
          return state;
        }
        const { [action.name]: _, ...remainingState } = state;
        return remainingState;
      }
    }
    return state;
  }
  var reducer = (0, import_data.combineReducers)({
    commands,
    commandLoaders,
    isOpen,
    context,
    loaderStates
  });
  var reducer_default = reducer;

  // packages/commands/build-module/store/actions.mjs
  var actions_exports = {};
  __export(actions_exports, {
    close: () => close,
    open: () => open,
    registerCommand: () => registerCommand,
    registerCommandLoader: () => registerCommandLoader,
    unregisterCommand: () => unregisterCommand,
    unregisterCommandLoader: () => unregisterCommandLoader
  });
  var REGISTERABLE_CATEGORIES = /* @__PURE__ */ new Set([
    "command",
    "view",
    "edit",
    "action"
  ]);
  function registerCommand(config) {
    let { category } = config;
    if (!category || !REGISTERABLE_CATEGORIES.has(category)) {
      category = "action";
    }
    return {
      type: "REGISTER_COMMAND",
      ...config,
      category
    };
  }
  function unregisterCommand(name) {
    return {
      type: "UNREGISTER_COMMAND",
      name
    };
  }
  function registerCommandLoader(config) {
    let { category } = config;
    if (!category || !REGISTERABLE_CATEGORIES.has(category)) {
      category = "action";
    }
    return {
      type: "REGISTER_COMMAND_LOADER",
      ...config,
      category
    };
  }
  function unregisterCommandLoader(name) {
    return {
      type: "UNREGISTER_COMMAND_LOADER",
      name
    };
  }
  function open() {
    return {
      type: "OPEN"
    };
  }
  function close() {
    return {
      type: "CLOSE"
    };
  }

  // packages/commands/build-module/store/selectors.mjs
  var selectors_exports = {};
  __export(selectors_exports, {
    getCommandLoaders: () => getCommandLoaders,
    getCommands: () => getCommands,
    getContext: () => getContext,
    isOpen: () => isOpen2
  });
  var import_data2 = __toESM(require_data(), 1);
  var getCommands = (0, import_data2.createSelector)(
    (state, contextual = false) => Object.values(state.commands).filter((command) => {
      const isContextual = command.context && command.context === state.context;
      return contextual ? isContextual : !isContextual;
    }),
    (state) => [state.commands, state.context]
  );
  var getCommandLoaders = (0, import_data2.createSelector)(
    (state, contextual = false) => Object.values(state.commandLoaders).filter((loader) => {
      const isContextual = loader.context && loader.context === state.context;
      return contextual ? isContextual : !isContextual;
    }),
    (state) => [state.commandLoaders, state.context]
  );
  function isOpen2(state) {
    return state.isOpen;
  }
  function getContext(state) {
    return state.context;
  }

  // packages/commands/build-module/store/private-actions.mjs
  var private_actions_exports = {};
  __export(private_actions_exports, {
    setContext: () => setContext,
    setLoaderLoading: () => setLoaderLoading
  });
  function setContext(context2) {
    return {
      type: "SET_CONTEXT",
      context: context2
    };
  }
  function setLoaderLoading(name, isLoading2) {
    return {
      type: "SET_LOADER_LOADING",
      name,
      isLoading: isLoading2
    };
  }

  // packages/commands/build-module/store/private-selectors.mjs
  var private_selectors_exports = {};
  __export(private_selectors_exports, {
    isLoading: () => isLoading
  });
  function isLoading(state) {
    return Object.entries(state.loaderStates).some(
      ([name, loading]) => loading && !!state.commandLoaders[name]
    );
  }

  // packages/commands/build-module/lock-unlock.mjs
  var import_private_apis = __toESM(require_private_apis(), 1);
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/commands"
  );

  // packages/commands/build-module/store/index.mjs
  var STORE_NAME = "core/commands";
  var store = (0, import_data3.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    actions: actions_exports,
    selectors: selectors_exports
  });
  (0, import_data3.register)(store);
  unlock(store).registerPrivateActions(private_actions_exports);
  unlock(store).registerPrivateSelectors(private_selectors_exports);

  // packages/commands/build-module/components/use-recent-commands.mjs
  var import_data4 = __toESM(require_data(), 1);
  var import_preferences = __toESM(require_preferences(), 1);
  var import_element2 = __toESM(require_element(), 1);
  var MAX_RECENTLY_SAVED = 30;
  var MAX_RECENTLY_DISPLAYED = 5;
  var EMPTY_ARRAY = [];
  var EMPTY_COMMANDS = [];
  var EMPTY_SET = /* @__PURE__ */ new Set();
  function recordUsage(name) {
    const current = (0, import_data4.select)(import_preferences.store).get(
      "core/commands",
      "recentlyUsed"
    ) ?? [];
    const next = [name, ...current.filter((n) => n !== name)].slice(
      0,
      MAX_RECENTLY_SAVED
    );
    (0, import_data4.dispatch)(import_preferences.store).set("core/commands", "recentlyUsed", next);
  }
  function useLoaderLoadingState(name, loading) {
    const { setLoaderLoading: setLoaderLoading2 } = unlock((0, import_data4.useDispatch)(store));
    (0, import_element2.useEffect)(() => {
      setLoaderLoading2(name, loading);
    }, [setLoaderLoading2, name, loading]);
    (0, import_element2.useEffect)(() => {
      return () => setLoaderLoading2(name, false);
    }, [setLoaderLoading2, name]);
  }
  function useLoaderCollector(hook, name, filterNames, onResolved) {
    const { isLoading: loading, commands: commands2 = EMPTY_COMMANDS } = hook({ search: "" }) ?? {};
    useLoaderLoadingState(name, loading);
    const filtered = (0, import_element2.useMemo)(
      () => filterNames ? commands2.filter((c) => filterNames.has(c.name)) : commands2,
      [commands2, filterNames]
    );
    (0, import_element2.useEffect)(() => {
      onResolved(name, filtered);
    }, [onResolved, name, filtered]);
    (0, import_element2.useEffect)(() => {
      return () => onResolved(name, []);
    }, [onResolved, name]);
  }
  function useRecentCommands() {
    const {
      contextualCommands,
      staticCommands,
      contextualLoaders,
      staticLoaders,
      recentlyUsedNames = EMPTY_ARRAY
    } = (0, import_data4.useSelect)((select) => {
      const { getCommands: getCommands2, getCommandLoaders: getCommandLoaders2 } = select(store);
      return {
        contextualCommands: getCommands2(true),
        staticCommands: getCommands2(false),
        contextualLoaders: getCommandLoaders2(true),
        staticLoaders: getCommandLoaders2(false),
        recentlyUsedNames: select(import_preferences.store).get(
          "core/commands",
          "recentlyUsed"
        )
      };
    }, []);
    const [resolvedMap, setResolvedMap] = (0, import_element2.useState)(
      () => /* @__PURE__ */ new Map()
    );
    const onResolved = (0, import_element2.useCallback)((loaderName, cmds) => {
      setResolvedMap((prev) => {
        const prevCmds = prev.get(loaderName);
        if (prevCmds && prevCmds.length === cmds.length && prevCmds.every((c, i) => c.name === cmds[i].name)) {
          return prev;
        }
        const next = new Map(prev);
        next.set(loaderName, cmds);
        return next;
      });
    }, []);
    const { recentNames, recentSet } = (0, import_element2.useMemo)(() => {
      const names = recentlyUsedNames.slice(0, MAX_RECENTLY_DISPLAYED);
      return { recentNames: names, recentSet: new Set(names) };
    }, [recentlyUsedNames]);
    const loaders = (0, import_element2.useMemo)(
      () => [...contextualLoaders, ...staticLoaders],
      [contextualLoaders, staticLoaders]
    );
    const commands2 = (0, import_element2.useMemo)(() => {
      const allByName = /* @__PURE__ */ new Map();
      [...contextualCommands, ...staticCommands].forEach(
        (c) => allByName.set(c.name, c)
      );
      for (const cmds of resolvedMap.values()) {
        cmds.forEach((c) => {
          if (!allByName.has(c.name)) {
            allByName.set(c.name, c);
          }
        });
      }
      return recentNames.map((n) => allByName.get(n)).filter((c) => !!c);
    }, [contextualCommands, staticCommands, resolvedMap, recentNames]);
    if (!recentlyUsedNames.length) {
      return {
        commands: EMPTY_COMMANDS,
        loaders: [],
        recentSet: EMPTY_SET,
        onResolved
      };
    }
    return { commands: commands2, loaders, recentSet, onResolved };
  }

  // packages/commands/build-module/components/command-menu.mjs
  var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
  var ITEM_ID_PREFIX = "command-palette-item-";
  var inputLabel = (0, import_i18n.__)("Search commands and settings");
  var CATEGORY_FALLBACK_ICONS = {
    view: arrow_right_default
  };
  var CATEGORY_LABELS = {
    command: (0, import_i18n.__)("Command"),
    view: (0, import_i18n.__)("View"),
    edit: (0, import_i18n.__)("Edit"),
    action: (0, import_i18n.__)("Action"),
    workflow: (0, import_i18n.__)("Workflow")
  };
  function isValidIcon(icon) {
    return typeof icon === "string" && icon !== "" || typeof icon === "function" || (0, import_element3.isValidElement)(icon);
  }
  function CommandItem({
    command,
    search,
    category,
    valuePrefix
  }) {
    const { close: close2 } = (0, import_data5.useDispatch)(store);
    const commandCategory = category ?? command.category;
    const categoryLabel = commandCategory ? CATEGORY_LABELS[commandCategory] : void 0;
    const fallbackIcon = commandCategory ? CATEGORY_FALLBACK_ICONS[commandCategory] : void 0;
    const icon = isValidIcon(command.icon) ? command.icon : fallbackIcon;
    const label = command.searchLabel ?? command.label;
    const value = valuePrefix ? `${valuePrefix}${command.name}` : label;
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
      _e.Item,
      {
        id: `${ITEM_ID_PREFIX}${value.toLowerCase()}`,
        value,
        keywords: valuePrefix ? [...command.keywords ?? [], label] : command.keywords,
        onSelect: () => {
          recordUsage(command.name);
          command.callback({ close: close2 });
        },
        children: /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(
          import_components.__experimentalHStack,
          {
            alignment: "left",
            className: clsx_default("commands-command-menu__item", {
              "has-icon": !!icon
            }),
            children: [
              icon && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_components.Icon, { icon, size: 24 }),
              /* @__PURE__ */ (0, import_jsx_runtime9.jsx)("span", { className: "commands-command-menu__item-label", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
                import_components.TextHighlight,
                {
                  text: command.label,
                  highlight: search
                }
              ) }),
              categoryLabel && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)("span", { className: "commands-command-menu__item-category", children: categoryLabel })
            ]
          }
        )
      },
      command.name
    );
  }
  function CommandMenuLoader({
    name,
    search,
    hook,
    category,
    valuePrefix
  }) {
    const { isLoading: loading, commands: commands2 = [] } = hook({ search }) ?? {};
    useLoaderLoadingState(name, loading);
    if (!commands2.length) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_jsx_runtime9.Fragment, { children: commands2.map((command) => /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
      CommandItem,
      {
        command,
        search,
        category: command.category ?? category,
        valuePrefix
      },
      command.name
    )) });
  }
  function CommandMenuLoaderWrapper({
    hook,
    ...props
  }) {
    const currentLoaderRef = (0, import_element3.useRef)(hook);
    const [key, setKey] = (0, import_element3.useState)(0);
    (0, import_element3.useEffect)(() => {
      if (currentLoaderRef.current !== hook) {
        currentLoaderRef.current = hook;
        setKey((prevKey) => prevKey + 1);
      }
    }, [hook]);
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
      CommandMenuLoader,
      {
        hook: currentLoaderRef.current,
        ...props
      },
      key
    );
  }
  function CommandList({
    search,
    commands: commands2,
    loaders,
    valuePrefix
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(import_jsx_runtime9.Fragment, { children: [
      commands2.map((command) => /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
        CommandItem,
        {
          command,
          search,
          valuePrefix
        },
        command.name
      )),
      loaders.map((loader) => /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
        CommandMenuLoaderWrapper,
        {
          name: loader.name,
          search,
          hook: loader.hook,
          category: loader.category,
          valuePrefix
        },
        loader.name
      ))
    ] });
  }
  function RecentLoaderRunner({
    hook,
    name,
    filterNames,
    onResolved
  }) {
    useLoaderCollector(hook, name, filterNames, onResolved);
    return null;
  }
  function RecentGroup() {
    const { commands: commands2, loaders, recentSet, onResolved } = useRecentCommands();
    if (!commands2.length && !loaders.length) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(_e.Group, { heading: (0, import_i18n.__)("Recent"), children: [
      loaders.map((loader) => /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
        RecentLoaderRunner,
        {
          name: loader.name,
          hook: loader.hook,
          filterNames: recentSet,
          onResolved
        },
        loader.name
      )),
      commands2.map((command) => /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
        CommandItem,
        {
          command,
          search: "",
          valuePrefix: "recent-"
        },
        command.name
      ))
    ] });
  }
  function SuggestionsGroup() {
    const { commands: commands2, loaders } = (0, import_data5.useSelect)((select) => {
      const { getCommands: getCommands2, getCommandLoaders: getCommandLoaders2 } = select(store);
      return {
        commands: getCommands2(true),
        loaders: getCommandLoaders2(true)
      };
    }, []);
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(_e.Group, { heading: (0, import_i18n.__)("Suggestions"), children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(CommandList, { search: "", commands: commands2, loaders }) });
  }
  function ResultsGroup({ search }) {
    const { commands: commands2, contextualCommands, loaders, contextualLoaders } = (0, import_data5.useSelect)((select) => {
      const { getCommands: getCommands2, getCommandLoaders: getCommandLoaders2 } = select(store);
      return {
        commands: getCommands2(false),
        contextualCommands: getCommands2(true),
        loaders: getCommandLoaders2(false),
        contextualLoaders: getCommandLoaders2(true)
      };
    }, []);
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(_e.Group, { heading: (0, import_i18n.__)("Results"), children: [
      /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
        CommandList,
        {
          search,
          commands: commands2,
          loaders
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
        CommandList,
        {
          search,
          commands: contextualCommands,
          loaders: contextualLoaders
        }
      )
    ] });
  }
  function CommandInput({ search, setSearch }) {
    const commandMenuInput = (0, import_element3.useRef)(null);
    const _value = P((state) => state.value);
    const selectedItemId = _value ? `${ITEM_ID_PREFIX}${_value}` : null;
    (0, import_element3.useEffect)(() => {
      commandMenuInput.current.focus();
    }, []);
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
      _e.Input,
      {
        ref: commandMenuInput,
        value: search,
        onValueChange: setSearch,
        placeholder: inputLabel,
        "aria-activedescendant": selectedItemId ?? void 0
      }
    );
  }
  function CommandMenu() {
    const { registerShortcut } = (0, import_data5.useDispatch)(import_keyboard_shortcuts.store);
    const [search, setSearch] = (0, import_element3.useState)("");
    const { isOpen: paletteIsOpen, loadersLoading } = (0, import_data5.useSelect)(
      (select) => ({
        isOpen: select(store).isOpen(),
        loadersLoading: unlock(select(store)).isLoading()
      }),
      []
    );
    const { open: open2, close: close2 } = (0, import_data5.useDispatch)(store);
    (0, import_element3.useEffect)(() => {
      registerShortcut({
        name: "core/commands",
        category: "global",
        description: (0, import_i18n.__)("Open the command palette."),
        keyCombination: {
          modifier: "primary",
          character: "k"
        }
      });
    }, [registerShortcut]);
    (0, import_keyboard_shortcuts.useShortcut)(
      "core/commands",
      (0, import_keycodes.withIgnoreIMEEvents)((event) => {
        if (event.defaultPrevented) {
          return;
        }
        event.preventDefault();
        if (paletteIsOpen) {
          close2();
        } else {
          open2();
        }
      })
    );
    const closeAndReset = () => {
      setSearch("");
      close2();
    };
    if (!paletteIsOpen) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
      import_components.Modal,
      {
        className: "commands-command-menu",
        overlayClassName: "commands-command-menu__overlay",
        onRequestClose: closeAndReset,
        __experimentalHideHeader: true,
        size: "medium",
        contentLabel: (0, import_i18n.__)("Command palette"),
        children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)("div", { className: "commands-command-menu__container", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(_e, { label: inputLabel, loop: true, children: [
          /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)("div", { className: "commands-command-menu__header", children: [
            /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
              icon_default,
              {
                className: "commands-command-menu__header-search-icon",
                icon: search_default
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
              CommandInput,
              {
                search,
                setSearch
              }
            )
          ] }),
          /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(_e.List, { label: (0, import_i18n.__)("Command suggestions"), children: [
            search && !loadersLoading && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(_e.Empty, { children: (0, import_i18n.__)("No results found.") }),
            !search && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(RecentGroup, {}),
            !search && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(SuggestionsGroup, {}),
            search && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(ResultsGroup, { search })
          ] })
        ] }) })
      }
    );
  }

  // packages/commands/build-module/hooks/use-command-context.mjs
  var import_element4 = __toESM(require_element(), 1);
  var import_data6 = __toESM(require_data(), 1);
  function useCommandContext(context2) {
    const { getContext: getContext2 } = (0, import_data6.useSelect)(store);
    const initialContext = (0, import_element4.useRef)(getContext2());
    const { setContext: setContext2 } = unlock((0, import_data6.useDispatch)(store));
    (0, import_element4.useEffect)(() => {
      setContext2(context2);
    }, [context2, setContext2]);
    (0, import_element4.useEffect)(() => {
      const initialContextRef = initialContext.current;
      return () => setContext2(initialContextRef);
    }, [setContext2]);
  }

  // packages/commands/build-module/private-apis.mjs
  var privateApis = {};
  lock(privateApis, {
    useCommandContext
  });

  // packages/commands/build-module/hooks/use-command.mjs
  var import_element5 = __toESM(require_element(), 1);
  var import_data7 = __toESM(require_data(), 1);
  function useCommand(command) {
    const { registerCommand: registerCommand2, unregisterCommand: unregisterCommand2 } = (0, import_data7.useDispatch)(store);
    const currentCallbackRef = (0, import_element5.useRef)(command.callback);
    (0, import_element5.useEffect)(() => {
      currentCallbackRef.current = command.callback;
    }, [command.callback]);
    (0, import_element5.useEffect)(() => {
      if (command.disabled) {
        return;
      }
      registerCommand2({
        name: command.name,
        context: command.context,
        category: command.category,
        label: command.label,
        searchLabel: command.searchLabel,
        icon: command.icon,
        keywords: command.keywords,
        callback: (...args) => currentCallbackRef.current(...args)
      });
      return () => {
        unregisterCommand2(command.name);
      };
    }, [
      command.name,
      command.label,
      command.searchLabel,
      command.icon,
      command.context,
      command.category,
      command.keywords,
      command.disabled,
      registerCommand2,
      unregisterCommand2
    ]);
  }
  function useCommands(commands2) {
    const { registerCommand: registerCommand2, unregisterCommand: unregisterCommand2 } = (0, import_data7.useDispatch)(store);
    const currentCallbacksRef = (0, import_element5.useRef)({});
    (0, import_element5.useEffect)(() => {
      if (!commands2) {
        return;
      }
      commands2.forEach((command) => {
        if (command.callback) {
          currentCallbacksRef.current[command.name] = command.callback;
        }
      });
    }, [commands2]);
    (0, import_element5.useEffect)(() => {
      if (!commands2) {
        return;
      }
      commands2.forEach((command) => {
        if (command.disabled) {
          return;
        }
        registerCommand2({
          name: command.name,
          context: command.context,
          category: command.category,
          label: command.label,
          searchLabel: command.searchLabel,
          icon: command.icon,
          keywords: command.keywords,
          callback: (...args) => {
            const callback = currentCallbacksRef.current[command.name];
            if (callback) {
              callback(...args);
            }
          }
        });
      });
      return () => {
        commands2.forEach((command) => {
          unregisterCommand2(command.name);
        });
      };
    }, [commands2, registerCommand2, unregisterCommand2]);
  }

  // packages/commands/build-module/hooks/use-command-loader.mjs
  var import_element6 = __toESM(require_element(), 1);
  var import_data8 = __toESM(require_data(), 1);
  function useCommandLoader(loader) {
    const { registerCommandLoader: registerCommandLoader2, unregisterCommandLoader: unregisterCommandLoader2 } = (0, import_data8.useDispatch)(store);
    const currentHookRef = (0, import_element6.useRef)(loader.hook);
    (0, import_element6.useEffect)(() => {
      currentHookRef.current = loader.hook;
    }, [loader.hook]);
    const hook = (0, import_element6.useCallback)(
      (...args) => currentHookRef.current(...args),
      []
    );
    (0, import_element6.useEffect)(() => {
      if (loader.disabled) {
        return;
      }
      registerCommandLoader2({
        name: loader.name,
        hook,
        context: loader.context,
        category: loader.category
      });
      return () => {
        unregisterCommandLoader2(loader.name);
      };
    }, [
      loader.name,
      hook,
      loader.context,
      loader.category,
      loader.disabled,
      registerCommandLoader2,
      unregisterCommandLoader2
    ]);
  }
  return __toCommonJS(index_exports);
})();
(window.wp ||= {}).commands = wp.commands;
})();
