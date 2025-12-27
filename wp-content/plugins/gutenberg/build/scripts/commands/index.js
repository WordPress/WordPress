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

  // package-external:@wordpress/primitives
  var require_primitives = __commonJS({
    "package-external:@wordpress/primitives"(exports, module) {
      module.exports = window.wp.primitives;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // packages/commands/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    CommandMenu: () => CommandMenu,
    privateApis: () => privateApis,
    store: () => store,
    useCommand: () => useCommand,
    useCommandLoader: () => useCommandLoader,
    useCommands: () => useCommands
  });

  // packages/commands/node_modules/cmdk/dist/chunk-NZJY6EH4.mjs
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
  function G(_, C, h, P, A, f, O) {
    if (f === C.length) return A === _.length ? U : k;
    var T2 = `${A},${f}`;
    if (O[T2] !== void 0) return O[T2];
    for (var L = P.charAt(f), c = h.indexOf(L, A), S = 0, E, N2, R, M2; c >= 0; ) E = G(_, C, h, P, c + 1, f + 1, O), E > S && (c === A ? E *= U : m.test(_.charAt(c - 1)) ? (E *= H, R = _.slice(A, c - 1).match(B), R && A > 0 && (E *= Math.pow(u, R.length))) : K.test(_.charAt(c - 1)) ? (E *= Y, M2 = _.slice(A, c - 1).match(X), M2 && A > 0 && (E *= Math.pow(u, M2.length))) : (E *= J, A > 0 && (E *= Math.pow(u, c - A))), _.charAt(c) !== C.charAt(f) && (E *= $)), (E < p && h.charAt(c - 1) === P.charAt(f + 1) || P.charAt(f + 1) === P.charAt(f) && h.charAt(c - 1) !== P.charAt(f)) && (N2 = G(_, C, h, P, c + 1, f + 2, O), N2 * p > E && (E = N2 * p)), E > S && (S = E), c = h.indexOf(L, c + 1);
    return O[T2] = S, S;
  }
  function D(_) {
    return _.toLowerCase().replace(X, " ");
  }
  function W(_, C, h) {
    return _ = h && h.length > 0 ? `${_ + " " + h.join(" ")}` : _, G(_, C, D(_), D(C), 0, 0, {});
  }

  // packages/commands/node_modules/@radix-ui/react-dialog/node_modules/@babel/runtime/helpers/esm/extends.js
  function _extends() {
    return _extends = Object.assign ? Object.assign.bind() : function(n) {
      for (var e = 1; e < arguments.length; e++) {
        var t2 = arguments[e];
        for (var r2 in t2) ({}).hasOwnProperty.call(t2, r2) && (n[r2] = t2[r2]);
      }
      return n;
    }, _extends.apply(null, arguments);
  }

  // packages/commands/node_modules/@radix-ui/react-dialog/dist/index.mjs
  var import_react15 = __toESM(require_react(), 1);

  // node_modules/@radix-ui/primitive/dist/index.mjs
  function $e42e1063c40fb3ef$export$b9ecd428b558ff10(originalEventHandler, ourEventHandler, { checkForDefaultPrevented = true } = {}) {
    return function handleEvent(event) {
      originalEventHandler === null || originalEventHandler === void 0 || originalEventHandler(event);
      if (checkForDefaultPrevented === false || !event.defaultPrevented) return ourEventHandler === null || ourEventHandler === void 0 ? void 0 : ourEventHandler(event);
    };
  }

  // node_modules/@radix-ui/react-compose-refs/dist/index.mjs
  var import_react = __toESM(require_react(), 1);
  function $6ed0406888f73fc4$var$setRef(ref, value) {
    if (typeof ref === "function") ref(value);
    else if (ref !== null && ref !== void 0) ref.current = value;
  }
  function $6ed0406888f73fc4$export$43e446d32b3d21af(...refs) {
    return (node) => refs.forEach(
      (ref) => $6ed0406888f73fc4$var$setRef(ref, node)
    );
  }
  function $6ed0406888f73fc4$export$c7b2cbe3552a0d05(...refs) {
    return (0, import_react.useCallback)($6ed0406888f73fc4$export$43e446d32b3d21af(...refs), refs);
  }

  // node_modules/@radix-ui/react-context/dist/index.mjs
  var import_react2 = __toESM(require_react(), 1);
  function $c512c27ab02ef895$export$fd42f52fd3ae1109(rootComponentName, defaultContext) {
    const Context = /* @__PURE__ */ (0, import_react2.createContext)(defaultContext);
    function Provider(props) {
      const { children, ...context2 } = props;
      const value = (0, import_react2.useMemo)(
        () => context2,
        Object.values(context2)
      );
      return /* @__PURE__ */ (0, import_react2.createElement)(Context.Provider, {
        value
      }, children);
    }
    function useContext2(consumerName) {
      const context2 = (0, import_react2.useContext)(Context);
      if (context2) return context2;
      if (defaultContext !== void 0) return defaultContext;
      throw new Error(`\`${consumerName}\` must be used within \`${rootComponentName}\``);
    }
    Provider.displayName = rootComponentName + "Provider";
    return [
      Provider,
      useContext2
    ];
  }
  function $c512c27ab02ef895$export$50c7b4e9d9f19c1(scopeName, createContextScopeDeps = []) {
    let defaultContexts = [];
    function $c512c27ab02ef895$export$fd42f52fd3ae11092(rootComponentName, defaultContext) {
      const BaseContext = /* @__PURE__ */ (0, import_react2.createContext)(defaultContext);
      const index = defaultContexts.length;
      defaultContexts = [
        ...defaultContexts,
        defaultContext
      ];
      function Provider(props) {
        const { scope, children, ...context2 } = props;
        const Context = (scope === null || scope === void 0 ? void 0 : scope[scopeName][index]) || BaseContext;
        const value = (0, import_react2.useMemo)(
          () => context2,
          Object.values(context2)
        );
        return /* @__PURE__ */ (0, import_react2.createElement)(Context.Provider, {
          value
        }, children);
      }
      function useContext2(consumerName, scope) {
        const Context = (scope === null || scope === void 0 ? void 0 : scope[scopeName][index]) || BaseContext;
        const context2 = (0, import_react2.useContext)(Context);
        if (context2) return context2;
        if (defaultContext !== void 0) return defaultContext;
        throw new Error(`\`${consumerName}\` must be used within \`${rootComponentName}\``);
      }
      Provider.displayName = rootComponentName + "Provider";
      return [
        Provider,
        useContext2
      ];
    }
    const createScope = () => {
      const scopeContexts = defaultContexts.map((defaultContext) => {
        return /* @__PURE__ */ (0, import_react2.createContext)(defaultContext);
      });
      return function useScope(scope) {
        const contexts = (scope === null || scope === void 0 ? void 0 : scope[scopeName]) || scopeContexts;
        return (0, import_react2.useMemo)(
          () => ({
            [`__scope${scopeName}`]: {
              ...scope,
              [scopeName]: contexts
            }
          }),
          [
            scope,
            contexts
          ]
        );
      };
    };
    createScope.scopeName = scopeName;
    return [
      $c512c27ab02ef895$export$fd42f52fd3ae11092,
      $c512c27ab02ef895$var$composeContextScopes(createScope, ...createContextScopeDeps)
    ];
  }
  function $c512c27ab02ef895$var$composeContextScopes(...scopes) {
    const baseScope = scopes[0];
    if (scopes.length === 1) return baseScope;
    const createScope1 = () => {
      const scopeHooks = scopes.map(
        (createScope) => ({
          useScope: createScope(),
          scopeName: createScope.scopeName
        })
      );
      return function useComposedScopes(overrideScopes) {
        const nextScopes1 = scopeHooks.reduce((nextScopes, { useScope, scopeName }) => {
          const scopeProps = useScope(overrideScopes);
          const currentScope = scopeProps[`__scope${scopeName}`];
          return {
            ...nextScopes,
            ...currentScope
          };
        }, {});
        return (0, import_react2.useMemo)(
          () => ({
            [`__scope${baseScope.scopeName}`]: nextScopes1
          }),
          [
            nextScopes1
          ]
        );
      };
    };
    createScope1.scopeName = baseScope.scopeName;
    return createScope1;
  }

  // node_modules/@radix-ui/react-id/dist/index.mjs
  var $2AODx$react = __toESM(require_react(), 1);

  // node_modules/@radix-ui/react-use-layout-effect/dist/index.mjs
  var import_react3 = __toESM(require_react(), 1);
  var $9f79659886946c16$export$e5c5a5f917a5871c = Boolean(globalThis === null || globalThis === void 0 ? void 0 : globalThis.document) ? import_react3.useLayoutEffect : () => {
  };

  // node_modules/@radix-ui/react-id/dist/index.mjs
  var $1746a345f3d73bb7$var$useReactId = $2AODx$react["useId".toString()] || (() => void 0);
  var $1746a345f3d73bb7$var$count = 0;
  function $1746a345f3d73bb7$export$f680877a34711e37(deterministicId) {
    const [id, setId] = $2AODx$react.useState($1746a345f3d73bb7$var$useReactId());
    $9f79659886946c16$export$e5c5a5f917a5871c(() => {
      if (!deterministicId) setId(
        (reactId) => reactId !== null && reactId !== void 0 ? reactId : String($1746a345f3d73bb7$var$count++)
      );
    }, [
      deterministicId
    ]);
    return deterministicId || (id ? `radix-${id}` : "");
  }

  // node_modules/@radix-ui/react-use-controllable-state/dist/index.mjs
  var import_react5 = __toESM(require_react(), 1);

  // node_modules/@radix-ui/react-use-callback-ref/dist/index.mjs
  var import_react4 = __toESM(require_react(), 1);
  function $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(callback) {
    const callbackRef = (0, import_react4.useRef)(callback);
    (0, import_react4.useEffect)(() => {
      callbackRef.current = callback;
    });
    return (0, import_react4.useMemo)(
      () => (...args) => {
        var _callbackRef$current;
        return (_callbackRef$current = callbackRef.current) === null || _callbackRef$current === void 0 ? void 0 : _callbackRef$current.call(callbackRef, ...args);
      },
      []
    );
  }

  // node_modules/@radix-ui/react-use-controllable-state/dist/index.mjs
  function $71cd76cc60e0454e$export$6f32135080cb4c3({ prop, defaultProp, onChange = () => {
  } }) {
    const [uncontrolledProp, setUncontrolledProp] = $71cd76cc60e0454e$var$useUncontrolledState({
      defaultProp,
      onChange
    });
    const isControlled = prop !== void 0;
    const value1 = isControlled ? prop : uncontrolledProp;
    const handleChange = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onChange);
    const setValue = (0, import_react5.useCallback)((nextValue) => {
      if (isControlled) {
        const setter = nextValue;
        const value = typeof nextValue === "function" ? setter(prop) : nextValue;
        if (value !== prop) handleChange(value);
      } else setUncontrolledProp(nextValue);
    }, [
      isControlled,
      prop,
      setUncontrolledProp,
      handleChange
    ]);
    return [
      value1,
      setValue
    ];
  }
  function $71cd76cc60e0454e$var$useUncontrolledState({ defaultProp, onChange }) {
    const uncontrolledState = (0, import_react5.useState)(defaultProp);
    const [value] = uncontrolledState;
    const prevValueRef = (0, import_react5.useRef)(value);
    const handleChange = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onChange);
    (0, import_react5.useEffect)(() => {
      if (prevValueRef.current !== value) {
        handleChange(value);
        prevValueRef.current = value;
      }
    }, [
      value,
      prevValueRef,
      handleChange
    ]);
    return uncontrolledState;
  }

  // packages/commands/node_modules/@radix-ui/react-dismissable-layer/node_modules/@babel/runtime/helpers/esm/extends.js
  function _extends2() {
    return _extends2 = Object.assign ? Object.assign.bind() : function(n) {
      for (var e = 1; e < arguments.length; e++) {
        var t2 = arguments[e];
        for (var r2 in t2) ({}).hasOwnProperty.call(t2, r2) && (n[r2] = t2[r2]);
      }
      return n;
    }, _extends2.apply(null, arguments);
  }

  // packages/commands/node_modules/@radix-ui/react-dismissable-layer/dist/index.mjs
  var import_react9 = __toESM(require_react(), 1);

  // node_modules/@babel/runtime/helpers/esm/extends.js
  function _extends3() {
    return _extends3 = Object.assign ? Object.assign.bind() : function(n) {
      for (var e = 1; e < arguments.length; e++) {
        var t2 = arguments[e];
        for (var r2 in t2) ({}).hasOwnProperty.call(t2, r2) && (n[r2] = t2[r2]);
      }
      return n;
    }, _extends3.apply(null, arguments);
  }

  // node_modules/@radix-ui/react-primitive/dist/index.mjs
  var import_react7 = __toESM(require_react(), 1);
  var import_react_dom = __toESM(require_react_dom(), 1);

  // node_modules/@radix-ui/react-slot/dist/index.mjs
  var import_react6 = __toESM(require_react(), 1);
  var $5e63c961fc1ce211$export$8c6ed5c666ac1360 = /* @__PURE__ */ (0, import_react6.forwardRef)((props, forwardedRef) => {
    const { children, ...slotProps } = props;
    const childrenArray = import_react6.Children.toArray(children);
    const slottable = childrenArray.find($5e63c961fc1ce211$var$isSlottable);
    if (slottable) {
      const newElement = slottable.props.children;
      const newChildren = childrenArray.map((child) => {
        if (child === slottable) {
          if (import_react6.Children.count(newElement) > 1) return import_react6.Children.only(null);
          return /* @__PURE__ */ (0, import_react6.isValidElement)(newElement) ? newElement.props.children : null;
        } else return child;
      });
      return /* @__PURE__ */ (0, import_react6.createElement)($5e63c961fc1ce211$var$SlotClone, _extends3({}, slotProps, {
        ref: forwardedRef
      }), /* @__PURE__ */ (0, import_react6.isValidElement)(newElement) ? /* @__PURE__ */ (0, import_react6.cloneElement)(newElement, void 0, newChildren) : null);
    }
    return /* @__PURE__ */ (0, import_react6.createElement)($5e63c961fc1ce211$var$SlotClone, _extends3({}, slotProps, {
      ref: forwardedRef
    }), children);
  });
  $5e63c961fc1ce211$export$8c6ed5c666ac1360.displayName = "Slot";
  var $5e63c961fc1ce211$var$SlotClone = /* @__PURE__ */ (0, import_react6.forwardRef)((props, forwardedRef) => {
    const { children, ...slotProps } = props;
    if (/* @__PURE__ */ (0, import_react6.isValidElement)(children)) return /* @__PURE__ */ (0, import_react6.cloneElement)(children, {
      ...$5e63c961fc1ce211$var$mergeProps(slotProps, children.props),
      ref: forwardedRef ? $6ed0406888f73fc4$export$43e446d32b3d21af(forwardedRef, children.ref) : children.ref
    });
    return import_react6.Children.count(children) > 1 ? import_react6.Children.only(null) : null;
  });
  $5e63c961fc1ce211$var$SlotClone.displayName = "SlotClone";
  var $5e63c961fc1ce211$export$d9f1ccf0bdb05d45 = ({ children }) => {
    return /* @__PURE__ */ (0, import_react6.createElement)(import_react6.Fragment, null, children);
  };
  function $5e63c961fc1ce211$var$isSlottable(child) {
    return /* @__PURE__ */ (0, import_react6.isValidElement)(child) && child.type === $5e63c961fc1ce211$export$d9f1ccf0bdb05d45;
  }
  function $5e63c961fc1ce211$var$mergeProps(slotProps, childProps) {
    const overrideProps = {
      ...childProps
    };
    for (const propName in childProps) {
      const slotPropValue = slotProps[propName];
      const childPropValue = childProps[propName];
      const isHandler = /^on[A-Z]/.test(propName);
      if (isHandler) {
        if (slotPropValue && childPropValue) overrideProps[propName] = (...args) => {
          childPropValue(...args);
          slotPropValue(...args);
        };
        else if (slotPropValue) overrideProps[propName] = slotPropValue;
      } else if (propName === "style") overrideProps[propName] = {
        ...slotPropValue,
        ...childPropValue
      };
      else if (propName === "className") overrideProps[propName] = [
        slotPropValue,
        childPropValue
      ].filter(Boolean).join(" ");
    }
    return {
      ...slotProps,
      ...overrideProps
    };
  }

  // node_modules/@radix-ui/react-primitive/dist/index.mjs
  var $8927f6f2acc4f386$var$NODES = [
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
    "span",
    "svg",
    "ul"
  ];
  var $8927f6f2acc4f386$export$250ffa63cdc0d034 = $8927f6f2acc4f386$var$NODES.reduce((primitive, node) => {
    const Node = /* @__PURE__ */ (0, import_react7.forwardRef)((props, forwardedRef) => {
      const { asChild, ...primitiveProps } = props;
      const Comp = asChild ? $5e63c961fc1ce211$export$8c6ed5c666ac1360 : node;
      (0, import_react7.useEffect)(() => {
        window[Symbol.for("radix-ui")] = true;
      }, []);
      return /* @__PURE__ */ (0, import_react7.createElement)(Comp, _extends3({}, primitiveProps, {
        ref: forwardedRef
      }));
    });
    Node.displayName = `Primitive.${node}`;
    return {
      ...primitive,
      [node]: Node
    };
  }, {});
  function $8927f6f2acc4f386$export$6d1a0317bde7de7f(target, event) {
    if (target) (0, import_react_dom.flushSync)(
      () => target.dispatchEvent(event)
    );
  }

  // node_modules/@radix-ui/react-use-escape-keydown/dist/index.mjs
  var import_react8 = __toESM(require_react(), 1);
  function $addc16e1bbe58fd0$export$3a72a57244d6e765(onEscapeKeyDownProp, ownerDocument = globalThis === null || globalThis === void 0 ? void 0 : globalThis.document) {
    const onEscapeKeyDown = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onEscapeKeyDownProp);
    (0, import_react8.useEffect)(() => {
      const handleKeyDown = (event) => {
        if (event.key === "Escape") onEscapeKeyDown(event);
      };
      ownerDocument.addEventListener("keydown", handleKeyDown);
      return () => ownerDocument.removeEventListener("keydown", handleKeyDown);
    }, [
      onEscapeKeyDown,
      ownerDocument
    ]);
  }

  // packages/commands/node_modules/@radix-ui/react-dismissable-layer/dist/index.mjs
  var $5cb92bef7577960e$var$CONTEXT_UPDATE = "dismissableLayer.update";
  var $5cb92bef7577960e$var$POINTER_DOWN_OUTSIDE = "dismissableLayer.pointerDownOutside";
  var $5cb92bef7577960e$var$FOCUS_OUTSIDE = "dismissableLayer.focusOutside";
  var $5cb92bef7577960e$var$originalBodyPointerEvents;
  var $5cb92bef7577960e$var$DismissableLayerContext = /* @__PURE__ */ (0, import_react9.createContext)({
    layers: /* @__PURE__ */ new Set(),
    layersWithOutsidePointerEventsDisabled: /* @__PURE__ */ new Set(),
    branches: /* @__PURE__ */ new Set()
  });
  var $5cb92bef7577960e$export$177fb62ff3ec1f22 = /* @__PURE__ */ (0, import_react9.forwardRef)((props, forwardedRef) => {
    var _node$ownerDocument;
    const { disableOutsidePointerEvents = false, onEscapeKeyDown, onPointerDownOutside, onFocusOutside, onInteractOutside, onDismiss, ...layerProps } = props;
    const context2 = (0, import_react9.useContext)($5cb92bef7577960e$var$DismissableLayerContext);
    const [node1, setNode] = (0, import_react9.useState)(null);
    const ownerDocument = (_node$ownerDocument = node1 === null || node1 === void 0 ? void 0 : node1.ownerDocument) !== null && _node$ownerDocument !== void 0 ? _node$ownerDocument : globalThis === null || globalThis === void 0 ? void 0 : globalThis.document;
    const [, force] = (0, import_react9.useState)({});
    const composedRefs = $6ed0406888f73fc4$export$c7b2cbe3552a0d05(
      forwardedRef,
      (node) => setNode(node)
    );
    const layers = Array.from(context2.layers);
    const [highestLayerWithOutsidePointerEventsDisabled] = [
      ...context2.layersWithOutsidePointerEventsDisabled
    ].slice(-1);
    const highestLayerWithOutsidePointerEventsDisabledIndex = layers.indexOf(highestLayerWithOutsidePointerEventsDisabled);
    const index = node1 ? layers.indexOf(node1) : -1;
    const isBodyPointerEventsDisabled = context2.layersWithOutsidePointerEventsDisabled.size > 0;
    const isPointerEventsEnabled = index >= highestLayerWithOutsidePointerEventsDisabledIndex;
    const pointerDownOutside = $5cb92bef7577960e$var$usePointerDownOutside((event) => {
      const target = event.target;
      const isPointerDownOnBranch = [
        ...context2.branches
      ].some(
        (branch) => branch.contains(target)
      );
      if (!isPointerEventsEnabled || isPointerDownOnBranch) return;
      onPointerDownOutside === null || onPointerDownOutside === void 0 || onPointerDownOutside(event);
      onInteractOutside === null || onInteractOutside === void 0 || onInteractOutside(event);
      if (!event.defaultPrevented) onDismiss === null || onDismiss === void 0 || onDismiss();
    }, ownerDocument);
    const focusOutside = $5cb92bef7577960e$var$useFocusOutside((event) => {
      const target = event.target;
      const isFocusInBranch = [
        ...context2.branches
      ].some(
        (branch) => branch.contains(target)
      );
      if (isFocusInBranch) return;
      onFocusOutside === null || onFocusOutside === void 0 || onFocusOutside(event);
      onInteractOutside === null || onInteractOutside === void 0 || onInteractOutside(event);
      if (!event.defaultPrevented) onDismiss === null || onDismiss === void 0 || onDismiss();
    }, ownerDocument);
    $addc16e1bbe58fd0$export$3a72a57244d6e765((event) => {
      const isHighestLayer = index === context2.layers.size - 1;
      if (!isHighestLayer) return;
      onEscapeKeyDown === null || onEscapeKeyDown === void 0 || onEscapeKeyDown(event);
      if (!event.defaultPrevented && onDismiss) {
        event.preventDefault();
        onDismiss();
      }
    }, ownerDocument);
    (0, import_react9.useEffect)(() => {
      if (!node1) return;
      if (disableOutsidePointerEvents) {
        if (context2.layersWithOutsidePointerEventsDisabled.size === 0) {
          $5cb92bef7577960e$var$originalBodyPointerEvents = ownerDocument.body.style.pointerEvents;
          ownerDocument.body.style.pointerEvents = "none";
        }
        context2.layersWithOutsidePointerEventsDisabled.add(node1);
      }
      context2.layers.add(node1);
      $5cb92bef7577960e$var$dispatchUpdate();
      return () => {
        if (disableOutsidePointerEvents && context2.layersWithOutsidePointerEventsDisabled.size === 1) ownerDocument.body.style.pointerEvents = $5cb92bef7577960e$var$originalBodyPointerEvents;
      };
    }, [
      node1,
      ownerDocument,
      disableOutsidePointerEvents,
      context2
    ]);
    (0, import_react9.useEffect)(() => {
      return () => {
        if (!node1) return;
        context2.layers.delete(node1);
        context2.layersWithOutsidePointerEventsDisabled.delete(node1);
        $5cb92bef7577960e$var$dispatchUpdate();
      };
    }, [
      node1,
      context2
    ]);
    (0, import_react9.useEffect)(() => {
      const handleUpdate = () => force({});
      document.addEventListener($5cb92bef7577960e$var$CONTEXT_UPDATE, handleUpdate);
      return () => document.removeEventListener($5cb92bef7577960e$var$CONTEXT_UPDATE, handleUpdate);
    }, []);
    return /* @__PURE__ */ (0, import_react9.createElement)($8927f6f2acc4f386$export$250ffa63cdc0d034.div, _extends2({}, layerProps, {
      ref: composedRefs,
      style: {
        pointerEvents: isBodyPointerEventsDisabled ? isPointerEventsEnabled ? "auto" : "none" : void 0,
        ...props.style
      },
      onFocusCapture: $e42e1063c40fb3ef$export$b9ecd428b558ff10(props.onFocusCapture, focusOutside.onFocusCapture),
      onBlurCapture: $e42e1063c40fb3ef$export$b9ecd428b558ff10(props.onBlurCapture, focusOutside.onBlurCapture),
      onPointerDownCapture: $e42e1063c40fb3ef$export$b9ecd428b558ff10(props.onPointerDownCapture, pointerDownOutside.onPointerDownCapture)
    }));
  });
  function $5cb92bef7577960e$var$usePointerDownOutside(onPointerDownOutside, ownerDocument = globalThis === null || globalThis === void 0 ? void 0 : globalThis.document) {
    const handlePointerDownOutside = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onPointerDownOutside);
    const isPointerInsideReactTreeRef = (0, import_react9.useRef)(false);
    const handleClickRef = (0, import_react9.useRef)(() => {
    });
    (0, import_react9.useEffect)(() => {
      const handlePointerDown = (event) => {
        if (event.target && !isPointerInsideReactTreeRef.current) {
          let handleAndDispatchPointerDownOutsideEvent = function() {
            $5cb92bef7577960e$var$handleAndDispatchCustomEvent($5cb92bef7577960e$var$POINTER_DOWN_OUTSIDE, handlePointerDownOutside, eventDetail, {
              discrete: true
            });
          };
          const eventDetail = {
            originalEvent: event
          };
          if (event.pointerType === "touch") {
            ownerDocument.removeEventListener("click", handleClickRef.current);
            handleClickRef.current = handleAndDispatchPointerDownOutsideEvent;
            ownerDocument.addEventListener("click", handleClickRef.current, {
              once: true
            });
          } else handleAndDispatchPointerDownOutsideEvent();
        } else
          ownerDocument.removeEventListener("click", handleClickRef.current);
        isPointerInsideReactTreeRef.current = false;
      };
      const timerId = window.setTimeout(() => {
        ownerDocument.addEventListener("pointerdown", handlePointerDown);
      }, 0);
      return () => {
        window.clearTimeout(timerId);
        ownerDocument.removeEventListener("pointerdown", handlePointerDown);
        ownerDocument.removeEventListener("click", handleClickRef.current);
      };
    }, [
      ownerDocument,
      handlePointerDownOutside
    ]);
    return {
      // ensures we check React component tree (not just DOM tree)
      onPointerDownCapture: () => isPointerInsideReactTreeRef.current = true
    };
  }
  function $5cb92bef7577960e$var$useFocusOutside(onFocusOutside, ownerDocument = globalThis === null || globalThis === void 0 ? void 0 : globalThis.document) {
    const handleFocusOutside = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onFocusOutside);
    const isFocusInsideReactTreeRef = (0, import_react9.useRef)(false);
    (0, import_react9.useEffect)(() => {
      const handleFocus = (event) => {
        if (event.target && !isFocusInsideReactTreeRef.current) {
          const eventDetail = {
            originalEvent: event
          };
          $5cb92bef7577960e$var$handleAndDispatchCustomEvent($5cb92bef7577960e$var$FOCUS_OUTSIDE, handleFocusOutside, eventDetail, {
            discrete: false
          });
        }
      };
      ownerDocument.addEventListener("focusin", handleFocus);
      return () => ownerDocument.removeEventListener("focusin", handleFocus);
    }, [
      ownerDocument,
      handleFocusOutside
    ]);
    return {
      onFocusCapture: () => isFocusInsideReactTreeRef.current = true,
      onBlurCapture: () => isFocusInsideReactTreeRef.current = false
    };
  }
  function $5cb92bef7577960e$var$dispatchUpdate() {
    const event = new CustomEvent($5cb92bef7577960e$var$CONTEXT_UPDATE);
    document.dispatchEvent(event);
  }
  function $5cb92bef7577960e$var$handleAndDispatchCustomEvent(name, handler, detail, { discrete }) {
    const target = detail.originalEvent.target;
    const event = new CustomEvent(name, {
      bubbles: false,
      cancelable: true,
      detail
    });
    if (handler) target.addEventListener(name, handler, {
      once: true
    });
    if (discrete) $8927f6f2acc4f386$export$6d1a0317bde7de7f(target, event);
    else target.dispatchEvent(event);
  }

  // packages/commands/node_modules/@radix-ui/react-focus-scope/node_modules/@babel/runtime/helpers/esm/extends.js
  function _extends4() {
    return _extends4 = Object.assign ? Object.assign.bind() : function(n) {
      for (var e = 1; e < arguments.length; e++) {
        var t2 = arguments[e];
        for (var r2 in t2) ({}).hasOwnProperty.call(t2, r2) && (n[r2] = t2[r2]);
      }
      return n;
    }, _extends4.apply(null, arguments);
  }

  // packages/commands/node_modules/@radix-ui/react-focus-scope/dist/index.mjs
  var import_react10 = __toESM(require_react(), 1);
  var $d3863c46a17e8a28$var$AUTOFOCUS_ON_MOUNT = "focusScope.autoFocusOnMount";
  var $d3863c46a17e8a28$var$AUTOFOCUS_ON_UNMOUNT = "focusScope.autoFocusOnUnmount";
  var $d3863c46a17e8a28$var$EVENT_OPTIONS = {
    bubbles: false,
    cancelable: true
  };
  var $d3863c46a17e8a28$export$20e40289641fbbb6 = /* @__PURE__ */ (0, import_react10.forwardRef)((props, forwardedRef) => {
    const { loop = false, trapped = false, onMountAutoFocus: onMountAutoFocusProp, onUnmountAutoFocus: onUnmountAutoFocusProp, ...scopeProps } = props;
    const [container1, setContainer] = (0, import_react10.useState)(null);
    const onMountAutoFocus = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onMountAutoFocusProp);
    const onUnmountAutoFocus = $b1b2314f5f9a1d84$export$25bec8c6f54ee79a(onUnmountAutoFocusProp);
    const lastFocusedElementRef = (0, import_react10.useRef)(null);
    const composedRefs = $6ed0406888f73fc4$export$c7b2cbe3552a0d05(
      forwardedRef,
      (node) => setContainer(node)
    );
    const focusScope = (0, import_react10.useRef)({
      paused: false,
      pause() {
        this.paused = true;
      },
      resume() {
        this.paused = false;
      }
    }).current;
    (0, import_react10.useEffect)(() => {
      if (trapped) {
        let handleFocusIn = function(event) {
          if (focusScope.paused || !container1) return;
          const target = event.target;
          if (container1.contains(target)) lastFocusedElementRef.current = target;
          else $d3863c46a17e8a28$var$focus(lastFocusedElementRef.current, {
            select: true
          });
        }, handleFocusOut = function(event) {
          if (focusScope.paused || !container1) return;
          const relatedTarget = event.relatedTarget;
          if (relatedTarget === null) return;
          if (!container1.contains(relatedTarget)) $d3863c46a17e8a28$var$focus(lastFocusedElementRef.current, {
            select: true
          });
        }, handleMutations = function(mutations) {
          const focusedElement = document.activeElement;
          if (focusedElement !== document.body) return;
          for (const mutation of mutations) if (mutation.removedNodes.length > 0) $d3863c46a17e8a28$var$focus(container1);
        };
        document.addEventListener("focusin", handleFocusIn);
        document.addEventListener("focusout", handleFocusOut);
        const mutationObserver = new MutationObserver(handleMutations);
        if (container1) mutationObserver.observe(container1, {
          childList: true,
          subtree: true
        });
        return () => {
          document.removeEventListener("focusin", handleFocusIn);
          document.removeEventListener("focusout", handleFocusOut);
          mutationObserver.disconnect();
        };
      }
    }, [
      trapped,
      container1,
      focusScope.paused
    ]);
    (0, import_react10.useEffect)(() => {
      if (container1) {
        $d3863c46a17e8a28$var$focusScopesStack.add(focusScope);
        const previouslyFocusedElement = document.activeElement;
        const hasFocusedCandidate = container1.contains(previouslyFocusedElement);
        if (!hasFocusedCandidate) {
          const mountEvent = new CustomEvent($d3863c46a17e8a28$var$AUTOFOCUS_ON_MOUNT, $d3863c46a17e8a28$var$EVENT_OPTIONS);
          container1.addEventListener($d3863c46a17e8a28$var$AUTOFOCUS_ON_MOUNT, onMountAutoFocus);
          container1.dispatchEvent(mountEvent);
          if (!mountEvent.defaultPrevented) {
            $d3863c46a17e8a28$var$focusFirst($d3863c46a17e8a28$var$removeLinks($d3863c46a17e8a28$var$getTabbableCandidates(container1)), {
              select: true
            });
            if (document.activeElement === previouslyFocusedElement) $d3863c46a17e8a28$var$focus(container1);
          }
        }
        return () => {
          container1.removeEventListener($d3863c46a17e8a28$var$AUTOFOCUS_ON_MOUNT, onMountAutoFocus);
          setTimeout(() => {
            const unmountEvent = new CustomEvent($d3863c46a17e8a28$var$AUTOFOCUS_ON_UNMOUNT, $d3863c46a17e8a28$var$EVENT_OPTIONS);
            container1.addEventListener($d3863c46a17e8a28$var$AUTOFOCUS_ON_UNMOUNT, onUnmountAutoFocus);
            container1.dispatchEvent(unmountEvent);
            if (!unmountEvent.defaultPrevented) $d3863c46a17e8a28$var$focus(previouslyFocusedElement !== null && previouslyFocusedElement !== void 0 ? previouslyFocusedElement : document.body, {
              select: true
            });
            container1.removeEventListener($d3863c46a17e8a28$var$AUTOFOCUS_ON_UNMOUNT, onUnmountAutoFocus);
            $d3863c46a17e8a28$var$focusScopesStack.remove(focusScope);
          }, 0);
        };
      }
    }, [
      container1,
      onMountAutoFocus,
      onUnmountAutoFocus,
      focusScope
    ]);
    const handleKeyDown = (0, import_react10.useCallback)((event) => {
      if (!loop && !trapped) return;
      if (focusScope.paused) return;
      const isTabKey = event.key === "Tab" && !event.altKey && !event.ctrlKey && !event.metaKey;
      const focusedElement = document.activeElement;
      if (isTabKey && focusedElement) {
        const container = event.currentTarget;
        const [first, last] = $d3863c46a17e8a28$var$getTabbableEdges(container);
        const hasTabbableElementsInside = first && last;
        if (!hasTabbableElementsInside) {
          if (focusedElement === container) event.preventDefault();
        } else {
          if (!event.shiftKey && focusedElement === last) {
            event.preventDefault();
            if (loop) $d3863c46a17e8a28$var$focus(first, {
              select: true
            });
          } else if (event.shiftKey && focusedElement === first) {
            event.preventDefault();
            if (loop) $d3863c46a17e8a28$var$focus(last, {
              select: true
            });
          }
        }
      }
    }, [
      loop,
      trapped,
      focusScope.paused
    ]);
    return /* @__PURE__ */ (0, import_react10.createElement)($8927f6f2acc4f386$export$250ffa63cdc0d034.div, _extends4({
      tabIndex: -1
    }, scopeProps, {
      ref: composedRefs,
      onKeyDown: handleKeyDown
    }));
  });
  function $d3863c46a17e8a28$var$focusFirst(candidates, { select = false } = {}) {
    const previouslyFocusedElement = document.activeElement;
    for (const candidate of candidates) {
      $d3863c46a17e8a28$var$focus(candidate, {
        select
      });
      if (document.activeElement !== previouslyFocusedElement) return;
    }
  }
  function $d3863c46a17e8a28$var$getTabbableEdges(container) {
    const candidates = $d3863c46a17e8a28$var$getTabbableCandidates(container);
    const first = $d3863c46a17e8a28$var$findVisible(candidates, container);
    const last = $d3863c46a17e8a28$var$findVisible(candidates.reverse(), container);
    return [
      first,
      last
    ];
  }
  function $d3863c46a17e8a28$var$getTabbableCandidates(container) {
    const nodes = [];
    const walker = document.createTreeWalker(container, NodeFilter.SHOW_ELEMENT, {
      acceptNode: (node) => {
        const isHiddenInput = node.tagName === "INPUT" && node.type === "hidden";
        if (node.disabled || node.hidden || isHiddenInput) return NodeFilter.FILTER_SKIP;
        return node.tabIndex >= 0 ? NodeFilter.FILTER_ACCEPT : NodeFilter.FILTER_SKIP;
      }
    });
    while (walker.nextNode()) nodes.push(walker.currentNode);
    return nodes;
  }
  function $d3863c46a17e8a28$var$findVisible(elements, container) {
    for (const element of elements) {
      if (!$d3863c46a17e8a28$var$isHidden(element, {
        upTo: container
      })) return element;
    }
  }
  function $d3863c46a17e8a28$var$isHidden(node, { upTo }) {
    if (getComputedStyle(node).visibility === "hidden") return true;
    while (node) {
      if (upTo !== void 0 && node === upTo) return false;
      if (getComputedStyle(node).display === "none") return true;
      node = node.parentElement;
    }
    return false;
  }
  function $d3863c46a17e8a28$var$isSelectableInput(element) {
    return element instanceof HTMLInputElement && "select" in element;
  }
  function $d3863c46a17e8a28$var$focus(element, { select = false } = {}) {
    if (element && element.focus) {
      const previouslyFocusedElement = document.activeElement;
      element.focus({
        preventScroll: true
      });
      if (element !== previouslyFocusedElement && $d3863c46a17e8a28$var$isSelectableInput(element) && select) element.select();
    }
  }
  var $d3863c46a17e8a28$var$focusScopesStack = $d3863c46a17e8a28$var$createFocusScopesStack();
  function $d3863c46a17e8a28$var$createFocusScopesStack() {
    let stack = [];
    return {
      add(focusScope) {
        const activeFocusScope = stack[0];
        if (focusScope !== activeFocusScope) activeFocusScope === null || activeFocusScope === void 0 || activeFocusScope.pause();
        stack = $d3863c46a17e8a28$var$arrayRemove(stack, focusScope);
        stack.unshift(focusScope);
      },
      remove(focusScope) {
        var _stack$;
        stack = $d3863c46a17e8a28$var$arrayRemove(stack, focusScope);
        (_stack$ = stack[0]) === null || _stack$ === void 0 || _stack$.resume();
      }
    };
  }
  function $d3863c46a17e8a28$var$arrayRemove(array, item) {
    const updatedArray = [
      ...array
    ];
    const index = updatedArray.indexOf(item);
    if (index !== -1) updatedArray.splice(index, 1);
    return updatedArray;
  }
  function $d3863c46a17e8a28$var$removeLinks(items) {
    return items.filter(
      (item) => item.tagName !== "A"
    );
  }

  // packages/commands/node_modules/@radix-ui/react-portal/node_modules/@babel/runtime/helpers/esm/extends.js
  function _extends5() {
    return _extends5 = Object.assign ? Object.assign.bind() : function(n) {
      for (var e = 1; e < arguments.length; e++) {
        var t2 = arguments[e];
        for (var r2 in t2) ({}).hasOwnProperty.call(t2, r2) && (n[r2] = t2[r2]);
      }
      return n;
    }, _extends5.apply(null, arguments);
  }

  // packages/commands/node_modules/@radix-ui/react-portal/dist/index.mjs
  var import_react11 = __toESM(require_react(), 1);
  var import_react_dom2 = __toESM(require_react_dom(), 1);
  var $f1701beae083dbae$export$602eac185826482c = /* @__PURE__ */ (0, import_react11.forwardRef)((props, forwardedRef) => {
    var _globalThis$document;
    const { container = globalThis === null || globalThis === void 0 ? void 0 : (_globalThis$document = globalThis.document) === null || _globalThis$document === void 0 ? void 0 : _globalThis$document.body, ...portalProps } = props;
    return container ? /* @__PURE__ */ import_react_dom2.default.createPortal(/* @__PURE__ */ (0, import_react11.createElement)($8927f6f2acc4f386$export$250ffa63cdc0d034.div, _extends5({}, portalProps, {
      ref: forwardedRef
    })), container) : null;
  });

  // packages/commands/node_modules/@radix-ui/react-presence/dist/index.mjs
  var import_react12 = __toESM(require_react(), 1);
  var import_react_dom3 = __toESM(require_react_dom(), 1);
  function $fe963b355347cc68$export$3e6543de14f8614f(initialState, machine) {
    return (0, import_react12.useReducer)((state, event) => {
      const nextState = machine[state][event];
      return nextState !== null && nextState !== void 0 ? nextState : state;
    }, initialState);
  }
  var $921a889cee6df7e8$export$99c2b779aa4e8b8b = (props) => {
    const { present, children } = props;
    const presence = $921a889cee6df7e8$var$usePresence(present);
    const child = typeof children === "function" ? children({
      present: presence.isPresent
    }) : import_react12.Children.only(children);
    const ref = $6ed0406888f73fc4$export$c7b2cbe3552a0d05(presence.ref, child.ref);
    const forceMount = typeof children === "function";
    return forceMount || presence.isPresent ? /* @__PURE__ */ (0, import_react12.cloneElement)(child, {
      ref
    }) : null;
  };
  $921a889cee6df7e8$export$99c2b779aa4e8b8b.displayName = "Presence";
  function $921a889cee6df7e8$var$usePresence(present) {
    const [node1, setNode] = (0, import_react12.useState)();
    const stylesRef = (0, import_react12.useRef)({});
    const prevPresentRef = (0, import_react12.useRef)(present);
    const prevAnimationNameRef = (0, import_react12.useRef)("none");
    const initialState = present ? "mounted" : "unmounted";
    const [state, send] = $fe963b355347cc68$export$3e6543de14f8614f(initialState, {
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
    (0, import_react12.useEffect)(() => {
      const currentAnimationName = $921a889cee6df7e8$var$getAnimationName(stylesRef.current);
      prevAnimationNameRef.current = state === "mounted" ? currentAnimationName : "none";
    }, [
      state
    ]);
    $9f79659886946c16$export$e5c5a5f917a5871c(() => {
      const styles = stylesRef.current;
      const wasPresent = prevPresentRef.current;
      const hasPresentChanged = wasPresent !== present;
      if (hasPresentChanged) {
        const prevAnimationName = prevAnimationNameRef.current;
        const currentAnimationName = $921a889cee6df7e8$var$getAnimationName(styles);
        if (present) send("MOUNT");
        else if (currentAnimationName === "none" || (styles === null || styles === void 0 ? void 0 : styles.display) === "none")
          send("UNMOUNT");
        else {
          const isAnimating = prevAnimationName !== currentAnimationName;
          if (wasPresent && isAnimating) send("ANIMATION_OUT");
          else send("UNMOUNT");
        }
        prevPresentRef.current = present;
      }
    }, [
      present,
      send
    ]);
    $9f79659886946c16$export$e5c5a5f917a5871c(() => {
      if (node1) {
        const handleAnimationEnd = (event) => {
          const currentAnimationName = $921a889cee6df7e8$var$getAnimationName(stylesRef.current);
          const isCurrentAnimation = currentAnimationName.includes(event.animationName);
          if (event.target === node1 && isCurrentAnimation)
            (0, import_react_dom3.flushSync)(
              () => send("ANIMATION_END")
            );
        };
        const handleAnimationStart = (event) => {
          if (event.target === node1)
            prevAnimationNameRef.current = $921a889cee6df7e8$var$getAnimationName(stylesRef.current);
        };
        node1.addEventListener("animationstart", handleAnimationStart);
        node1.addEventListener("animationcancel", handleAnimationEnd);
        node1.addEventListener("animationend", handleAnimationEnd);
        return () => {
          node1.removeEventListener("animationstart", handleAnimationStart);
          node1.removeEventListener("animationcancel", handleAnimationEnd);
          node1.removeEventListener("animationend", handleAnimationEnd);
        };
      } else
        send("ANIMATION_END");
    }, [
      node1,
      send
    ]);
    return {
      isPresent: [
        "mounted",
        "unmountSuspended"
      ].includes(state),
      ref: (0, import_react12.useCallback)((node) => {
        if (node) stylesRef.current = getComputedStyle(node);
        setNode(node);
      }, [])
    };
  }
  function $921a889cee6df7e8$var$getAnimationName(styles) {
    return (styles === null || styles === void 0 ? void 0 : styles.animationName) || "none";
  }

  // node_modules/@radix-ui/react-focus-guards/dist/index.mjs
  var import_react13 = __toESM(require_react(), 1);
  var $3db38b7d1fb3fe6a$var$count = 0;
  function $3db38b7d1fb3fe6a$export$b7ece24a22aeda8c() {
    (0, import_react13.useEffect)(() => {
      var _edgeGuards$, _edgeGuards$2;
      const edgeGuards = document.querySelectorAll("[data-radix-focus-guard]");
      document.body.insertAdjacentElement("afterbegin", (_edgeGuards$ = edgeGuards[0]) !== null && _edgeGuards$ !== void 0 ? _edgeGuards$ : $3db38b7d1fb3fe6a$var$createFocusGuard());
      document.body.insertAdjacentElement("beforeend", (_edgeGuards$2 = edgeGuards[1]) !== null && _edgeGuards$2 !== void 0 ? _edgeGuards$2 : $3db38b7d1fb3fe6a$var$createFocusGuard());
      $3db38b7d1fb3fe6a$var$count++;
      return () => {
        if ($3db38b7d1fb3fe6a$var$count === 1) document.querySelectorAll("[data-radix-focus-guard]").forEach(
          (node) => node.remove()
        );
        $3db38b7d1fb3fe6a$var$count--;
      };
    }, []);
  }
  function $3db38b7d1fb3fe6a$var$createFocusGuard() {
    const element = document.createElement("span");
    element.setAttribute("data-radix-focus-guard", "");
    element.tabIndex = 0;
    element.style.cssText = "outline: none; opacity: 0; position: fixed; pointer-events: none";
    return element;
  }

  // node_modules/tslib/tslib.es6.mjs
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

  // node_modules/react-remove-scroll/dist/es2015/Combination.js
  var React6 = __toESM(require_react());

  // node_modules/react-remove-scroll/dist/es2015/UI.js
  var React2 = __toESM(require_react());

  // node_modules/react-remove-scroll-bar/dist/es2015/constants.js
  var zeroRightClassName = "right-scroll-bar-position";
  var fullWidthClassName = "width-before-scroll-bar";
  var noScrollbarsClassName = "with-scroll-bars-hidden";
  var removedBarSizeVariable = "--removed-body-scroll-bar-size";

  // node_modules/use-callback-ref/dist/es2015/assignRef.js
  function assignRef(ref, value) {
    if (typeof ref === "function") {
      ref(value);
    } else if (ref) {
      ref.current = value;
    }
    return ref;
  }

  // node_modules/use-callback-ref/dist/es2015/useRef.js
  var import_react14 = __toESM(require_react());
  function useCallbackRef(initialValue, callback) {
    var ref = (0, import_react14.useState)(function() {
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

  // node_modules/use-callback-ref/dist/es2015/useMergeRef.js
  function useMergeRefs(refs, defaultValue) {
    return useCallbackRef(defaultValue || null, function(newValue) {
      return refs.forEach(function(ref) {
        return assignRef(ref, newValue);
      });
    });
  }

  // node_modules/use-sidecar/dist/es2015/medium.js
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

  // node_modules/use-sidecar/dist/es2015/exports.js
  var React = __toESM(require_react());
  var SideCar = function(_a) {
    var sideCar = _a.sideCar, rest = __rest(_a, ["sideCar"]);
    if (!sideCar) {
      throw new Error("Sidecar: please provide `sideCar` property to import the right car");
    }
    var Target = sideCar.read();
    if (!Target) {
      throw new Error("Sidecar medium not found");
    }
    return React.createElement(Target, __assign({}, rest));
  };
  SideCar.isSideCarExport = true;
  function exportSidecar(medium, exported) {
    medium.useMedium(exported);
    return SideCar;
  }

  // node_modules/react-remove-scroll/dist/es2015/medium.js
  var effectCar = createSidecarMedium();

  // node_modules/react-remove-scroll/dist/es2015/UI.js
  var nothing = function() {
    return;
  };
  var RemoveScroll = React2.forwardRef(function(props, parentRef) {
    var ref = React2.useRef(null);
    var _a = React2.useState({
      onScrollCapture: nothing,
      onWheelCapture: nothing,
      onTouchMoveCapture: nothing
    }), callbacks = _a[0], setCallbacks = _a[1];
    var forwardProps = props.forwardProps, children = props.children, className = props.className, removeScrollBar = props.removeScrollBar, enabled = props.enabled, shards = props.shards, sideCar = props.sideCar, noIsolation = props.noIsolation, inert = props.inert, allowPinchZoom = props.allowPinchZoom, _b = props.as, Container = _b === void 0 ? "div" : _b, rest = __rest(props, ["forwardProps", "children", "className", "removeScrollBar", "enabled", "shards", "sideCar", "noIsolation", "inert", "allowPinchZoom", "as"]);
    var SideCar2 = sideCar;
    var containerRef = useMergeRefs([ref, parentRef]);
    var containerProps = __assign(__assign({}, rest), callbacks);
    return React2.createElement(
      React2.Fragment,
      null,
      enabled && React2.createElement(SideCar2, { sideCar: effectCar, removeScrollBar, shards, noIsolation, inert, setCallbacks, allowPinchZoom: !!allowPinchZoom, lockRef: ref }),
      forwardProps ? React2.cloneElement(React2.Children.only(children), __assign(__assign({}, containerProps), { ref: containerRef })) : React2.createElement(Container, __assign({}, containerProps, { className, ref: containerRef }), children)
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

  // node_modules/react-remove-scroll/dist/es2015/SideEffect.js
  var React5 = __toESM(require_react());

  // node_modules/react-remove-scroll-bar/dist/es2015/component.js
  var React4 = __toESM(require_react());

  // node_modules/react-style-singleton/dist/es2015/hook.js
  var React3 = __toESM(require_react());

  // node_modules/get-nonce/dist/es2015/index.js
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

  // node_modules/react-style-singleton/dist/es2015/singleton.js
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

  // node_modules/react-style-singleton/dist/es2015/hook.js
  var styleHookSingleton = function() {
    var sheet = stylesheetSingleton();
    return function(styles, isDynamic) {
      React3.useEffect(function() {
        sheet.add(styles);
        return function() {
          sheet.remove();
        };
      }, [styles && isDynamic]);
    };
  };

  // node_modules/react-style-singleton/dist/es2015/component.js
  var styleSingleton = function() {
    var useStyle = styleHookSingleton();
    var Sheet = function(_a) {
      var styles = _a.styles, dynamic = _a.dynamic;
      useStyle(styles, dynamic);
      return null;
    };
    return Sheet;
  };

  // node_modules/react-remove-scroll-bar/dist/es2015/utils.js
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

  // node_modules/react-remove-scroll-bar/dist/es2015/component.js
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
    React4.useEffect(function() {
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
    var gap = React4.useMemo(function() {
      return getGapWidth(gapMode);
    }, [gapMode]);
    return React4.createElement(Style, { styles: getStyles(gap, !noRelative, gapMode, !noImportant ? "!important" : "") });
  };

  // node_modules/react-remove-scroll/dist/es2015/aggresiveCapture.js
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

  // node_modules/react-remove-scroll/dist/es2015/handleScroll.js
  var alwaysContainsScroll = function(node) {
    return node.tagName === "TEXTAREA";
  };
  var elementCanBeScrolled = function(node, overflow) {
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
    var current = node;
    do {
      if (typeof ShadowRoot !== "undefined" && current instanceof ShadowRoot) {
        current = current.host;
      }
      var isScrollable = elementCouldBeScrolled(axis, current);
      if (isScrollable) {
        var _a = getScrollVariables(axis, current), s = _a[1], d = _a[2];
        if (s > d) {
          return true;
        }
      }
      current = current.parentNode;
    } while (current && current !== document.body);
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
      var _a = getScrollVariables(axis, target), position = _a[0], scroll_1 = _a[1], capacity = _a[2];
      var elementScroll = scroll_1 - capacity - directionFactor * position;
      if (position || elementScroll) {
        if (elementCouldBeScrolled(axis, target)) {
          availableScroll += elementScroll;
          availableScrollTop += position;
        }
      }
      target = target.parentNode;
    } while (
      // portaled content
      !targetInLock && target !== document.body || // self content
      targetInLock && (endTarget.contains(target) || endTarget === target)
    );
    if (isDeltaPositive && (noOverscroll && availableScroll === 0 || !noOverscroll && delta > availableScroll)) {
      shouldCancelScroll = true;
    } else if (!isDeltaPositive && (noOverscroll && availableScrollTop === 0 || !noOverscroll && -delta > availableScrollTop)) {
      shouldCancelScroll = true;
    }
    return shouldCancelScroll;
  };

  // node_modules/react-remove-scroll/dist/es2015/SideEffect.js
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
    var shouldPreventQueue = React5.useRef([]);
    var touchStartRef = React5.useRef([0, 0]);
    var activeAxis = React5.useRef();
    var id = React5.useState(idCounter++)[0];
    var Style2 = React5.useState(function() {
      return styleSingleton();
    })[0];
    var lastProps = React5.useRef(props);
    React5.useEffect(function() {
      lastProps.current = props;
    }, [props]);
    React5.useEffect(function() {
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
    var shouldCancelEvent = React5.useCallback(function(event, parent) {
      if ("touches" in event && event.touches.length === 2) {
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
    var shouldPrevent = React5.useCallback(function(_event) {
      var event = _event;
      if (!lockStack.length || lockStack[lockStack.length - 1] !== Style2) {
        return;
      }
      var delta = "deltaY" in event ? getDeltaXY(event) : getTouchXY(event);
      var sourceEvent = shouldPreventQueue.current.filter(function(e) {
        return e.name === event.type && e.target === event.target && deltaCompare(e.delta, delta);
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
    var shouldCancel = React5.useCallback(function(name, delta, target, should) {
      var event = { name, delta, target, should };
      shouldPreventQueue.current.push(event);
      setTimeout(function() {
        shouldPreventQueue.current = shouldPreventQueue.current.filter(function(e) {
          return e !== event;
        });
      }, 1);
    }, []);
    var scrollTouchStart = React5.useCallback(function(event) {
      touchStartRef.current = getTouchXY(event);
      activeAxis.current = void 0;
    }, []);
    var scrollWheel = React5.useCallback(function(event) {
      shouldCancel(event.type, getDeltaXY(event), event.target, shouldCancelEvent(event, props.lockRef.current));
    }, []);
    var scrollTouchMove = React5.useCallback(function(event) {
      shouldCancel(event.type, getTouchXY(event), event.target, shouldCancelEvent(event, props.lockRef.current));
    }, []);
    React5.useEffect(function() {
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
    return React5.createElement(
      React5.Fragment,
      null,
      inert ? React5.createElement(Style2, { styles: generateStyle(id) }) : null,
      removeScrollBar ? React5.createElement(RemoveScrollBar, { gapMode: "margin" }) : null
    );
  }

  // node_modules/react-remove-scroll/dist/es2015/sidecar.js
  var sidecar_default = exportSidecar(effectCar, RemoveScrollSideCar);

  // node_modules/react-remove-scroll/dist/es2015/Combination.js
  var ReactRemoveScroll = React6.forwardRef(function(props, ref) {
    return React6.createElement(RemoveScroll, __assign({}, props, { ref, sideCar: sidecar_default }));
  });
  ReactRemoveScroll.classNames = RemoveScroll.classNames;
  var Combination_default = ReactRemoveScroll;

  // node_modules/aria-hidden/dist/es2015/index.js
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
    targets.push.apply(targets, Array.from(activeParentNode.querySelectorAll("[aria-live]")));
    return applyAttributeToOthers(targets, activeParentNode, markerName, "aria-hidden");
  };

  // packages/commands/node_modules/@radix-ui/react-dialog/dist/index.mjs
  var $5d3850c4d0b4e6c7$var$DIALOG_NAME = "Dialog";
  var [$5d3850c4d0b4e6c7$var$createDialogContext, $5d3850c4d0b4e6c7$export$cc702773b8ea3e41] = $c512c27ab02ef895$export$50c7b4e9d9f19c1($5d3850c4d0b4e6c7$var$DIALOG_NAME);
  var [$5d3850c4d0b4e6c7$var$DialogProvider, $5d3850c4d0b4e6c7$var$useDialogContext] = $5d3850c4d0b4e6c7$var$createDialogContext($5d3850c4d0b4e6c7$var$DIALOG_NAME);
  var $5d3850c4d0b4e6c7$export$3ddf2d174ce01153 = (props) => {
    const { __scopeDialog, children, open: openProp, defaultOpen, onOpenChange, modal = true } = props;
    const triggerRef = (0, import_react15.useRef)(null);
    const contentRef = (0, import_react15.useRef)(null);
    const [open2 = false, setOpen] = $71cd76cc60e0454e$export$6f32135080cb4c3({
      prop: openProp,
      defaultProp: defaultOpen,
      onChange: onOpenChange
    });
    return /* @__PURE__ */ (0, import_react15.createElement)($5d3850c4d0b4e6c7$var$DialogProvider, {
      scope: __scopeDialog,
      triggerRef,
      contentRef,
      contentId: $1746a345f3d73bb7$export$f680877a34711e37(),
      titleId: $1746a345f3d73bb7$export$f680877a34711e37(),
      descriptionId: $1746a345f3d73bb7$export$f680877a34711e37(),
      open: open2,
      onOpenChange: setOpen,
      onOpenToggle: (0, import_react15.useCallback)(
        () => setOpen(
          (prevOpen) => !prevOpen
        ),
        [
          setOpen
        ]
      ),
      modal
    }, children);
  };
  var $5d3850c4d0b4e6c7$var$PORTAL_NAME = "DialogPortal";
  var [$5d3850c4d0b4e6c7$var$PortalProvider, $5d3850c4d0b4e6c7$var$usePortalContext] = $5d3850c4d0b4e6c7$var$createDialogContext($5d3850c4d0b4e6c7$var$PORTAL_NAME, {
    forceMount: void 0
  });
  var $5d3850c4d0b4e6c7$export$dad7c95542bacce0 = (props) => {
    const { __scopeDialog, forceMount, children, container } = props;
    const context2 = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$PORTAL_NAME, __scopeDialog);
    return /* @__PURE__ */ (0, import_react15.createElement)($5d3850c4d0b4e6c7$var$PortalProvider, {
      scope: __scopeDialog,
      forceMount
    }, import_react15.Children.map(
      children,
      (child) => /* @__PURE__ */ (0, import_react15.createElement)($921a889cee6df7e8$export$99c2b779aa4e8b8b, {
        present: forceMount || context2.open
      }, /* @__PURE__ */ (0, import_react15.createElement)($f1701beae083dbae$export$602eac185826482c, {
        asChild: true,
        container
      }, child))
    ));
  };
  var $5d3850c4d0b4e6c7$var$OVERLAY_NAME = "DialogOverlay";
  var $5d3850c4d0b4e6c7$export$bd1d06c79be19e17 = /* @__PURE__ */ (0, import_react15.forwardRef)((props, forwardedRef) => {
    const portalContext = $5d3850c4d0b4e6c7$var$usePortalContext($5d3850c4d0b4e6c7$var$OVERLAY_NAME, props.__scopeDialog);
    const { forceMount = portalContext.forceMount, ...overlayProps } = props;
    const context2 = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$OVERLAY_NAME, props.__scopeDialog);
    return context2.modal ? /* @__PURE__ */ (0, import_react15.createElement)($921a889cee6df7e8$export$99c2b779aa4e8b8b, {
      present: forceMount || context2.open
    }, /* @__PURE__ */ (0, import_react15.createElement)($5d3850c4d0b4e6c7$var$DialogOverlayImpl, _extends({}, overlayProps, {
      ref: forwardedRef
    }))) : null;
  });
  var $5d3850c4d0b4e6c7$var$DialogOverlayImpl = /* @__PURE__ */ (0, import_react15.forwardRef)((props, forwardedRef) => {
    const { __scopeDialog, ...overlayProps } = props;
    const context2 = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$OVERLAY_NAME, __scopeDialog);
    return (
      // Make sure `Content` is scrollable even when it doesn't live inside `RemoveScroll`
      // ie. when `Overlay` and `Content` are siblings
      /* @__PURE__ */ (0, import_react15.createElement)(Combination_default, {
        as: $5e63c961fc1ce211$export$8c6ed5c666ac1360,
        allowPinchZoom: true,
        shards: [
          context2.contentRef
        ]
      }, /* @__PURE__ */ (0, import_react15.createElement)($8927f6f2acc4f386$export$250ffa63cdc0d034.div, _extends({
        "data-state": $5d3850c4d0b4e6c7$var$getState(context2.open)
      }, overlayProps, {
        ref: forwardedRef,
        style: {
          pointerEvents: "auto",
          ...overlayProps.style
        }
      })))
    );
  });
  var $5d3850c4d0b4e6c7$var$CONTENT_NAME = "DialogContent";
  var $5d3850c4d0b4e6c7$export$b6d9565de1e068cf = /* @__PURE__ */ (0, import_react15.forwardRef)((props, forwardedRef) => {
    const portalContext = $5d3850c4d0b4e6c7$var$usePortalContext($5d3850c4d0b4e6c7$var$CONTENT_NAME, props.__scopeDialog);
    const { forceMount = portalContext.forceMount, ...contentProps } = props;
    const context2 = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$CONTENT_NAME, props.__scopeDialog);
    return /* @__PURE__ */ (0, import_react15.createElement)($921a889cee6df7e8$export$99c2b779aa4e8b8b, {
      present: forceMount || context2.open
    }, context2.modal ? /* @__PURE__ */ (0, import_react15.createElement)($5d3850c4d0b4e6c7$var$DialogContentModal, _extends({}, contentProps, {
      ref: forwardedRef
    })) : /* @__PURE__ */ (0, import_react15.createElement)($5d3850c4d0b4e6c7$var$DialogContentNonModal, _extends({}, contentProps, {
      ref: forwardedRef
    })));
  });
  var $5d3850c4d0b4e6c7$var$DialogContentModal = /* @__PURE__ */ (0, import_react15.forwardRef)((props, forwardedRef) => {
    const context2 = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$CONTENT_NAME, props.__scopeDialog);
    const contentRef = (0, import_react15.useRef)(null);
    const composedRefs = $6ed0406888f73fc4$export$c7b2cbe3552a0d05(forwardedRef, context2.contentRef, contentRef);
    (0, import_react15.useEffect)(() => {
      const content = contentRef.current;
      if (content) return hideOthers(content);
    }, []);
    return /* @__PURE__ */ (0, import_react15.createElement)($5d3850c4d0b4e6c7$var$DialogContentImpl, _extends({}, props, {
      ref: composedRefs,
      trapFocus: context2.open,
      disableOutsidePointerEvents: true,
      onCloseAutoFocus: $e42e1063c40fb3ef$export$b9ecd428b558ff10(props.onCloseAutoFocus, (event) => {
        var _context$triggerRef$c;
        event.preventDefault();
        (_context$triggerRef$c = context2.triggerRef.current) === null || _context$triggerRef$c === void 0 || _context$triggerRef$c.focus();
      }),
      onPointerDownOutside: $e42e1063c40fb3ef$export$b9ecd428b558ff10(props.onPointerDownOutside, (event) => {
        const originalEvent = event.detail.originalEvent;
        const ctrlLeftClick = originalEvent.button === 0 && originalEvent.ctrlKey === true;
        const isRightClick = originalEvent.button === 2 || ctrlLeftClick;
        if (isRightClick) event.preventDefault();
      }),
      onFocusOutside: $e42e1063c40fb3ef$export$b9ecd428b558ff10(
        props.onFocusOutside,
        (event) => event.preventDefault()
      )
    }));
  });
  var $5d3850c4d0b4e6c7$var$DialogContentNonModal = /* @__PURE__ */ (0, import_react15.forwardRef)((props, forwardedRef) => {
    const context2 = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$CONTENT_NAME, props.__scopeDialog);
    const hasInteractedOutsideRef = (0, import_react15.useRef)(false);
    const hasPointerDownOutsideRef = (0, import_react15.useRef)(false);
    return /* @__PURE__ */ (0, import_react15.createElement)($5d3850c4d0b4e6c7$var$DialogContentImpl, _extends({}, props, {
      ref: forwardedRef,
      trapFocus: false,
      disableOutsidePointerEvents: false,
      onCloseAutoFocus: (event) => {
        var _props$onCloseAutoFoc;
        (_props$onCloseAutoFoc = props.onCloseAutoFocus) === null || _props$onCloseAutoFoc === void 0 || _props$onCloseAutoFoc.call(props, event);
        if (!event.defaultPrevented) {
          var _context$triggerRef$c2;
          if (!hasInteractedOutsideRef.current) (_context$triggerRef$c2 = context2.triggerRef.current) === null || _context$triggerRef$c2 === void 0 || _context$triggerRef$c2.focus();
          event.preventDefault();
        }
        hasInteractedOutsideRef.current = false;
        hasPointerDownOutsideRef.current = false;
      },
      onInteractOutside: (event) => {
        var _props$onInteractOuts, _context$triggerRef$c3;
        (_props$onInteractOuts = props.onInteractOutside) === null || _props$onInteractOuts === void 0 || _props$onInteractOuts.call(props, event);
        if (!event.defaultPrevented) {
          hasInteractedOutsideRef.current = true;
          if (event.detail.originalEvent.type === "pointerdown") hasPointerDownOutsideRef.current = true;
        }
        const target = event.target;
        const targetIsTrigger = (_context$triggerRef$c3 = context2.triggerRef.current) === null || _context$triggerRef$c3 === void 0 ? void 0 : _context$triggerRef$c3.contains(target);
        if (targetIsTrigger) event.preventDefault();
        if (event.detail.originalEvent.type === "focusin" && hasPointerDownOutsideRef.current) event.preventDefault();
      }
    }));
  });
  var $5d3850c4d0b4e6c7$var$DialogContentImpl = /* @__PURE__ */ (0, import_react15.forwardRef)((props, forwardedRef) => {
    const { __scopeDialog, trapFocus, onOpenAutoFocus, onCloseAutoFocus, ...contentProps } = props;
    const context2 = $5d3850c4d0b4e6c7$var$useDialogContext($5d3850c4d0b4e6c7$var$CONTENT_NAME, __scopeDialog);
    const contentRef = (0, import_react15.useRef)(null);
    const composedRefs = $6ed0406888f73fc4$export$c7b2cbe3552a0d05(forwardedRef, contentRef);
    $3db38b7d1fb3fe6a$export$b7ece24a22aeda8c();
    return /* @__PURE__ */ (0, import_react15.createElement)(import_react15.Fragment, null, /* @__PURE__ */ (0, import_react15.createElement)($d3863c46a17e8a28$export$20e40289641fbbb6, {
      asChild: true,
      loop: true,
      trapped: trapFocus,
      onMountAutoFocus: onOpenAutoFocus,
      onUnmountAutoFocus: onCloseAutoFocus
    }, /* @__PURE__ */ (0, import_react15.createElement)($5cb92bef7577960e$export$177fb62ff3ec1f22, _extends({
      role: "dialog",
      id: context2.contentId,
      "aria-describedby": context2.descriptionId,
      "aria-labelledby": context2.titleId,
      "data-state": $5d3850c4d0b4e6c7$var$getState(context2.open)
    }, contentProps, {
      ref: composedRefs,
      onDismiss: () => context2.onOpenChange(false)
    }))), false);
  });
  var $5d3850c4d0b4e6c7$var$TITLE_NAME = "DialogTitle";
  function $5d3850c4d0b4e6c7$var$getState(open2) {
    return open2 ? "open" : "closed";
  }
  var $5d3850c4d0b4e6c7$var$TITLE_WARNING_NAME = "DialogTitleWarning";
  var [$5d3850c4d0b4e6c7$export$69b62a49393917d6, $5d3850c4d0b4e6c7$var$useWarningContext] = $c512c27ab02ef895$export$fd42f52fd3ae1109($5d3850c4d0b4e6c7$var$TITLE_WARNING_NAME, {
    contentName: $5d3850c4d0b4e6c7$var$CONTENT_NAME,
    titleName: $5d3850c4d0b4e6c7$var$TITLE_NAME,
    docsSlug: "dialog"
  });
  var $5d3850c4d0b4e6c7$export$be92b6f5f03c0fe9 = $5d3850c4d0b4e6c7$export$3ddf2d174ce01153;
  var $5d3850c4d0b4e6c7$export$602eac185826482c = $5d3850c4d0b4e6c7$export$dad7c95542bacce0;
  var $5d3850c4d0b4e6c7$export$c6fdb837b070b4ff = $5d3850c4d0b4e6c7$export$bd1d06c79be19e17;
  var $5d3850c4d0b4e6c7$export$7c6e2c02157bb7d2 = $5d3850c4d0b4e6c7$export$b6d9565de1e068cf;

  // packages/commands/node_modules/cmdk/dist/index.mjs
  var t = __toESM(require_react(), 1);
  var V = '[cmdk-group=""]';
  var X2 = '[cmdk-group-items=""]';
  var ge = '[cmdk-group-heading=""]';
  var Y2 = '[cmdk-item=""]';
  var le = `${Y2}:not([aria-disabled="true"])`;
  var Q = "cmdk-item-select";
  var M = "data-value";
  var Re = (r2, o, n) => W(r2, o, n);
  var ue = t.createContext(void 0);
  var G2 = () => t.useContext(ue);
  var de = t.createContext(void 0);
  var Z = () => t.useContext(de);
  var fe = t.createContext(void 0);
  var me = t.forwardRef((r2, o) => {
    let n = k2(() => {
      var e, s;
      return { search: "", value: (s = (e = r2.value) != null ? e : r2.defaultValue) != null ? s : "", filtered: { count: 0, items: /* @__PURE__ */ new Map(), groups: /* @__PURE__ */ new Set() } };
    }), u2 = k2(() => /* @__PURE__ */ new Set()), c = k2(() => /* @__PURE__ */ new Map()), d = k2(() => /* @__PURE__ */ new Map()), f = k2(() => /* @__PURE__ */ new Set()), p2 = pe(r2), { label: v, children: b, value: l, onValueChange: y, filter: S, shouldFilter: C, loop: L, disablePointerSelection: ee = false, vimBindings: j = true, ...H2 } = r2, te = t.useId(), $2 = t.useId(), K2 = t.useId(), x = t.useRef(null), g = Me();
    T(() => {
      if (l !== void 0) {
        let e = l.trim();
        n.current.value = e, h.emit();
      }
    }, [l]), T(() => {
      g(6, re);
    }, []);
    let h = t.useMemo(() => ({ subscribe: (e) => (f.current.add(e), () => f.current.delete(e)), snapshot: () => n.current, setState: (e, s, i) => {
      var a, m2, R;
      if (!Object.is(n.current[e], s)) {
        if (n.current[e] = s, e === "search") z(), q(), g(1, U2);
        else if (e === "value" && (i || g(5, re), ((a = p2.current) == null ? void 0 : a.value) !== void 0)) {
          let E = s != null ? s : "";
          (R = (m2 = p2.current).onValueChange) == null || R.call(m2, E);
          return;
        }
        h.emit();
      }
    }, emit: () => {
      f.current.forEach((e) => e());
    } }), []), B2 = t.useMemo(() => ({ value: (e, s, i) => {
      var a;
      s !== ((a = d.current.get(e)) == null ? void 0 : a.value) && (d.current.set(e, { value: s, keywords: i }), n.current.filtered.items.set(e, ne(s, i)), g(2, () => {
        q(), h.emit();
      }));
    }, item: (e, s) => (u2.current.add(e), s && (c.current.has(s) ? c.current.get(s).add(e) : c.current.set(s, /* @__PURE__ */ new Set([e]))), g(3, () => {
      z(), q(), n.current.value || U2(), h.emit();
    }), () => {
      d.current.delete(e), u2.current.delete(e), n.current.filtered.items.delete(e);
      let i = O();
      g(4, () => {
        z(), (i == null ? void 0 : i.getAttribute("id")) === e && U2(), h.emit();
      });
    }), group: (e) => (c.current.has(e) || c.current.set(e, /* @__PURE__ */ new Set()), () => {
      d.current.delete(e), c.current.delete(e);
    }), filter: () => p2.current.shouldFilter, label: v || r2["aria-label"], disablePointerSelection: ee, listId: te, inputId: K2, labelId: $2, listInnerRef: x }), []);
    function ne(e, s) {
      var a, m2;
      let i = (m2 = (a = p2.current) == null ? void 0 : a.filter) != null ? m2 : Re;
      return e ? i(e, n.current.search, s) : 0;
    }
    function q() {
      if (!n.current.search || p2.current.shouldFilter === false) return;
      let e = n.current.filtered.items, s = [];
      n.current.filtered.groups.forEach((a) => {
        let m2 = c.current.get(a), R = 0;
        m2.forEach((E) => {
          let P = e.get(E);
          R = Math.max(P, R);
        }), s.push([a, R]);
      });
      let i = x.current;
      A().sort((a, m2) => {
        var P, _;
        let R = a.getAttribute("id"), E = m2.getAttribute("id");
        return ((P = e.get(E)) != null ? P : 0) - ((_ = e.get(R)) != null ? _ : 0);
      }).forEach((a) => {
        let m2 = a.closest(X2);
        m2 ? m2.appendChild(a.parentElement === m2 ? a : a.closest(`${X2} > *`)) : i.appendChild(a.parentElement === i ? a : a.closest(`${X2} > *`));
      }), s.sort((a, m2) => m2[1] - a[1]).forEach((a) => {
        let m2 = x.current.querySelector(`${V}[${M}="${encodeURIComponent(a[0])}"]`);
        m2 == null || m2.parentElement.appendChild(m2);
      });
    }
    function U2() {
      let e = A().find((i) => i.getAttribute("aria-disabled") !== "true"), s = e == null ? void 0 : e.getAttribute(M);
      h.setState("value", s || void 0);
    }
    function z() {
      var s, i, a, m2;
      if (!n.current.search || p2.current.shouldFilter === false) {
        n.current.filtered.count = u2.current.size;
        return;
      }
      n.current.filtered.groups = /* @__PURE__ */ new Set();
      let e = 0;
      for (let R of u2.current) {
        let E = (i = (s = d.current.get(R)) == null ? void 0 : s.value) != null ? i : "", P = (m2 = (a = d.current.get(R)) == null ? void 0 : a.keywords) != null ? m2 : [], _ = ne(E, P);
        n.current.filtered.items.set(R, _), _ > 0 && e++;
      }
      for (let [R, E] of c.current) for (let P of E) if (n.current.filtered.items.get(P) > 0) {
        n.current.filtered.groups.add(R);
        break;
      }
      n.current.filtered.count = e;
    }
    function re() {
      var s, i, a;
      let e = O();
      e && (((s = e.parentElement) == null ? void 0 : s.firstChild) === e && ((a = (i = e.closest(V)) == null ? void 0 : i.querySelector(ge)) == null || a.scrollIntoView({ block: "nearest" })), e.scrollIntoView({ block: "nearest" }));
    }
    function O() {
      var e;
      return (e = x.current) == null ? void 0 : e.querySelector(`${Y2}[aria-selected="true"]`);
    }
    function A() {
      var e;
      return Array.from((e = x.current) == null ? void 0 : e.querySelectorAll(le));
    }
    function W2(e) {
      let i = A()[e];
      i && h.setState("value", i.getAttribute(M));
    }
    function J2(e) {
      var R;
      let s = O(), i = A(), a = i.findIndex((E) => E === s), m2 = i[a + e];
      (R = p2.current) != null && R.loop && (m2 = a + e < 0 ? i[i.length - 1] : a + e === i.length ? i[0] : i[a + e]), m2 && h.setState("value", m2.getAttribute(M));
    }
    function oe(e) {
      let s = O(), i = s == null ? void 0 : s.closest(V), a;
      for (; i && !a; ) i = e > 0 ? we(i, V) : Ie(i, V), a = i == null ? void 0 : i.querySelector(le);
      a ? h.setState("value", a.getAttribute(M)) : J2(e);
    }
    let ie = () => W2(A().length - 1), ae = (e) => {
      e.preventDefault(), e.metaKey ? ie() : e.altKey ? oe(1) : J2(1);
    }, se = (e) => {
      e.preventDefault(), e.metaKey ? W2(0) : e.altKey ? oe(-1) : J2(-1);
    };
    return t.createElement($8927f6f2acc4f386$export$250ffa63cdc0d034.div, { ref: o, tabIndex: -1, ...H2, "cmdk-root": "", onKeyDown: (e) => {
      var s;
      if ((s = H2.onKeyDown) == null || s.call(H2, e), !e.defaultPrevented) switch (e.key) {
        case "n":
        case "j": {
          j && e.ctrlKey && ae(e);
          break;
        }
        case "ArrowDown": {
          ae(e);
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
          e.preventDefault(), W2(0);
          break;
        }
        case "End": {
          e.preventDefault(), ie();
          break;
        }
        case "Enter":
          if (!e.nativeEvent.isComposing && e.keyCode !== 229) {
            e.preventDefault();
            let i = O();
            if (i) {
              let a = new Event(Q);
              i.dispatchEvent(a);
            }
          }
      }
    } }, t.createElement("label", { "cmdk-label": "", htmlFor: B2.inputId, id: B2.labelId, style: De }, v), F(r2, (e) => t.createElement(de.Provider, { value: h }, t.createElement(ue.Provider, { value: B2 }, e))));
  });
  var be = t.forwardRef((r2, o) => {
    var K2, x;
    let n = t.useId(), u2 = t.useRef(null), c = t.useContext(fe), d = G2(), f = pe(r2), p2 = (x = (K2 = f.current) == null ? void 0 : K2.forceMount) != null ? x : c == null ? void 0 : c.forceMount;
    T(() => {
      if (!p2) return d.item(n, c == null ? void 0 : c.id);
    }, [p2]);
    let v = ve(n, u2, [r2.value, r2.children, u2], r2.keywords), b = Z(), l = D2((g) => g.value && g.value === v.current), y = D2((g) => p2 || d.filter() === false ? true : g.search ? g.filtered.items.get(n) > 0 : true);
    t.useEffect(() => {
      let g = u2.current;
      if (!(!g || r2.disabled)) return g.addEventListener(Q, S), () => g.removeEventListener(Q, S);
    }, [y, r2.onSelect, r2.disabled]);
    function S() {
      var g, h;
      C(), (h = (g = f.current).onSelect) == null || h.call(g, v.current);
    }
    function C() {
      b.setState("value", v.current, true);
    }
    if (!y) return null;
    let { disabled: L, value: ee, onSelect: j, forceMount: H2, keywords: te, ...$2 } = r2;
    return t.createElement($8927f6f2acc4f386$export$250ffa63cdc0d034.div, { ref: N([u2, o]), ...$2, id: n, "cmdk-item": "", role: "option", "aria-disabled": !!L, "aria-selected": !!l, "data-disabled": !!L, "data-selected": !!l, onPointerMove: L || d.disablePointerSelection ? void 0 : C, onClick: L ? void 0 : S }, r2.children);
  });
  var he = t.forwardRef((r2, o) => {
    let { heading: n, children: u2, forceMount: c, ...d } = r2, f = t.useId(), p2 = t.useRef(null), v = t.useRef(null), b = t.useId(), l = G2(), y = D2((C) => c || l.filter() === false ? true : C.search ? C.filtered.groups.has(f) : true);
    T(() => l.group(f), []), ve(f, p2, [r2.value, r2.heading, v]);
    let S = t.useMemo(() => ({ id: f, forceMount: c }), [c]);
    return t.createElement($8927f6f2acc4f386$export$250ffa63cdc0d034.div, { ref: N([p2, o]), ...d, "cmdk-group": "", role: "presentation", hidden: y ? void 0 : true }, n && t.createElement("div", { ref: v, "cmdk-group-heading": "", "aria-hidden": true, id: b }, n), F(r2, (C) => t.createElement("div", { "cmdk-group-items": "", role: "group", "aria-labelledby": n ? b : void 0 }, t.createElement(fe.Provider, { value: S }, C))));
  });
  var ye = t.forwardRef((r2, o) => {
    let { alwaysRender: n, ...u2 } = r2, c = t.useRef(null), d = D2((f) => !f.search);
    return !n && !d ? null : t.createElement($8927f6f2acc4f386$export$250ffa63cdc0d034.div, { ref: N([c, o]), ...u2, "cmdk-separator": "", role: "separator" });
  });
  var Ee = t.forwardRef((r2, o) => {
    let { onValueChange: n, ...u2 } = r2, c = r2.value != null, d = Z(), f = D2((l) => l.search), p2 = D2((l) => l.value), v = G2(), b = t.useMemo(() => {
      var y;
      let l = (y = v.listInnerRef.current) == null ? void 0 : y.querySelector(`${Y2}[${M}="${encodeURIComponent(p2)}"]`);
      return l == null ? void 0 : l.getAttribute("id");
    }, []);
    return t.useEffect(() => {
      r2.value != null && d.setState("search", r2.value);
    }, [r2.value]), t.createElement($8927f6f2acc4f386$export$250ffa63cdc0d034.input, { ref: o, ...u2, "cmdk-input": "", autoComplete: "off", autoCorrect: "off", spellCheck: false, "aria-autocomplete": "list", role: "combobox", "aria-expanded": true, "aria-controls": v.listId, "aria-labelledby": v.labelId, "aria-activedescendant": b, id: v.inputId, type: "text", value: c ? r2.value : f, onChange: (l) => {
      c || d.setState("search", l.target.value), n == null || n(l.target.value);
    } });
  });
  var Se = t.forwardRef((r2, o) => {
    let { children: n, label: u2 = "Suggestions", ...c } = r2, d = t.useRef(null), f = t.useRef(null), p2 = G2();
    return t.useEffect(() => {
      if (f.current && d.current) {
        let v = f.current, b = d.current, l, y = new ResizeObserver(() => {
          l = requestAnimationFrame(() => {
            let S = v.offsetHeight;
            b.style.setProperty("--cmdk-list-height", S.toFixed(1) + "px");
          });
        });
        return y.observe(v), () => {
          cancelAnimationFrame(l), y.unobserve(v);
        };
      }
    }, []), t.createElement($8927f6f2acc4f386$export$250ffa63cdc0d034.div, { ref: N([d, o]), ...c, "cmdk-list": "", role: "listbox", "aria-label": u2, id: p2.listId }, F(r2, (v) => t.createElement("div", { ref: N([f, p2.listInnerRef]), "cmdk-list-sizer": "" }, v)));
  });
  var Ce = t.forwardRef((r2, o) => {
    let { open: n, onOpenChange: u2, overlayClassName: c, contentClassName: d, container: f, ...p2 } = r2;
    return t.createElement($5d3850c4d0b4e6c7$export$be92b6f5f03c0fe9, { open: n, onOpenChange: u2 }, t.createElement($5d3850c4d0b4e6c7$export$602eac185826482c, { container: f }, t.createElement($5d3850c4d0b4e6c7$export$c6fdb837b070b4ff, { "cmdk-overlay": "", className: c }), t.createElement($5d3850c4d0b4e6c7$export$7c6e2c02157bb7d2, { "aria-label": r2.label, "cmdk-dialog": "", className: d }, t.createElement(me, { ref: o, ...p2 }))));
  });
  var xe = t.forwardRef((r2, o) => D2((u2) => u2.filtered.count === 0) ? t.createElement($8927f6f2acc4f386$export$250ffa63cdc0d034.div, { ref: o, ...r2, "cmdk-empty": "", role: "presentation" }) : null);
  var Pe = t.forwardRef((r2, o) => {
    let { progress: n, children: u2, label: c = "Loading...", ...d } = r2;
    return t.createElement($8927f6f2acc4f386$export$250ffa63cdc0d034.div, { ref: o, ...d, "cmdk-loading": "", role: "progressbar", "aria-valuenow": n, "aria-valuemin": 0, "aria-valuemax": 100, "aria-label": c }, F(r2, (f) => t.createElement("div", { "aria-hidden": true }, f)));
  });
  var He = Object.assign(me, { List: Se, Item: be, Input: Ee, Group: he, Separator: ye, Dialog: Ce, Empty: xe, Loading: Pe });
  function we(r2, o) {
    let n = r2.nextElementSibling;
    for (; n; ) {
      if (n.matches(o)) return n;
      n = n.nextElementSibling;
    }
  }
  function Ie(r2, o) {
    let n = r2.previousElementSibling;
    for (; n; ) {
      if (n.matches(o)) return n;
      n = n.previousElementSibling;
    }
  }
  function pe(r2) {
    let o = t.useRef(r2);
    return T(() => {
      o.current = r2;
    }), o;
  }
  var T = typeof window == "undefined" ? t.useEffect : t.useLayoutEffect;
  function k2(r2) {
    let o = t.useRef();
    return o.current === void 0 && (o.current = r2()), o;
  }
  function N(r2) {
    return (o) => {
      r2.forEach((n) => {
        typeof n == "function" ? n(o) : n != null && (n.current = o);
      });
    };
  }
  function D2(r2) {
    let o = Z(), n = () => r2(o.snapshot());
    return t.useSyncExternalStore(o.subscribe, n, n);
  }
  function ve(r2, o, n, u2 = []) {
    let c = t.useRef(), d = G2();
    return T(() => {
      var v;
      let f = (() => {
        var b;
        for (let l of n) {
          if (typeof l == "string") return l.trim();
          if (typeof l == "object" && "current" in l) return l.current ? (b = l.current.textContent) == null ? void 0 : b.trim() : c.current;
        }
      })(), p2 = u2.map((b) => b.trim());
      d.value(r2, f, p2), (v = o.current) == null || v.setAttribute(M, f), c.current = f;
    }), c;
  }
  var Me = () => {
    let [r2, o] = t.useState(), n = k2(() => /* @__PURE__ */ new Map());
    return T(() => {
      n.current.forEach((u2) => u2()), n.current = /* @__PURE__ */ new Map();
    }, [r2]), (u2, c) => {
      n.current.set(u2, c), o({});
    };
  };
  function Te(r2) {
    let o = r2.type;
    return typeof o == "function" ? o(r2.props) : "render" in o ? o.render(r2.props) : r2;
  }
  function F({ asChild: r2, children: o }, n) {
    return r2 && t.isValidElement(o) ? t.cloneElement(Te(o), { ref: o.ref }, n(o.props.children)) : n(o);
  }
  var De = { position: "absolute", width: "1px", height: "1px", padding: "0", margin: "-1px", overflow: "hidden", clip: "rect(0, 0, 0, 0)", whiteSpace: "nowrap", borderWidth: "0" };

  // node_modules/clsx/dist/clsx.mjs
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

  // packages/commands/build-module/components/command-menu.js
  var import_data4 = __toESM(require_data());
  var import_element2 = __toESM(require_element());
  var import_i18n = __toESM(require_i18n());
  var import_components = __toESM(require_components());
  var import_keyboard_shortcuts = __toESM(require_keyboard_shortcuts());

  // packages/icons/build-module/icon/index.js
  var import_element = __toESM(require_element());
  var icon_default = (0, import_element.forwardRef)(
    ({ icon, size = 24, ...props }, ref) => {
      return (0, import_element.cloneElement)(icon, {
        width: size,
        height: size,
        ...props,
        ref
      });
    }
  );

  // packages/icons/build-module/library/search.js
  var import_primitives = __toESM(require_primitives());
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var search_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M13 5c-3.3 0-6 2.7-6 6 0 1.4.5 2.7 1.3 3.7l-3.8 3.8 1.1 1.1 3.8-3.8c1 .8 2.3 1.3 3.7 1.3 3.3 0 6-2.7 6-6S16.3 5 13 5zm0 10.5c-2.5 0-4.5-2-4.5-4.5s2-4.5 4.5-4.5 4.5 2 4.5 4.5-2 4.5-4.5 4.5z" }) });

  // packages/commands/build-module/store/index.js
  var import_data3 = __toESM(require_data());

  // packages/commands/build-module/store/reducer.js
  var import_data = __toESM(require_data());
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
  var reducer = (0, import_data.combineReducers)({
    commands,
    commandLoaders,
    isOpen,
    context
  });
  var reducer_default = reducer;

  // packages/commands/build-module/store/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    close: () => close,
    open: () => open,
    registerCommand: () => registerCommand,
    registerCommandLoader: () => registerCommandLoader,
    unregisterCommand: () => unregisterCommand,
    unregisterCommandLoader: () => unregisterCommandLoader
  });
  function registerCommand(config) {
    return {
      type: "REGISTER_COMMAND",
      ...config
    };
  }
  function unregisterCommand(name) {
    return {
      type: "UNREGISTER_COMMAND",
      name
    };
  }
  function registerCommandLoader(config) {
    return {
      type: "REGISTER_COMMAND_LOADER",
      ...config
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

  // packages/commands/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    getCommandLoaders: () => getCommandLoaders,
    getCommands: () => getCommands,
    getContext: () => getContext,
    isOpen: () => isOpen2
  });
  var import_data2 = __toESM(require_data());
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

  // packages/commands/build-module/store/private-actions.js
  var private_actions_exports = {};
  __export(private_actions_exports, {
    setContext: () => setContext
  });
  function setContext(context2) {
    return {
      type: "SET_CONTEXT",
      context: context2
    };
  }

  // packages/commands/build-module/lock-unlock.js
  var import_private_apis = __toESM(require_private_apis());
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/commands"
  );

  // packages/commands/build-module/store/index.js
  var STORE_NAME = "core/commands";
  var store = (0, import_data3.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    actions: actions_exports,
    selectors: selectors_exports
  });
  (0, import_data3.register)(store);
  unlock(store).registerPrivateActions(private_actions_exports);

  // packages/commands/build-module/components/command-menu.js
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  var inputLabel = (0, import_i18n.__)("Search commands and settings");
  function CommandMenuLoader({ name, search, hook, setLoader, close: close2 }) {
    const { isLoading, commands: commands2 = [] } = hook({ search }) ?? {};
    (0, import_element2.useEffect)(() => {
      setLoader(name, isLoading);
    }, [setLoader, name, isLoading]);
    if (!commands2.length) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_jsx_runtime2.Fragment, { children: commands2.map((command) => /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
      He.Item,
      {
        value: command.searchLabel ?? command.label,
        keywords: command.keywords,
        onSelect: () => command.callback({ close: close2 }),
        id: command.name,
        children: /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(
          import_components.__experimentalHStack,
          {
            alignment: "left",
            className: clsx_default("commands-command-menu__item", {
              "has-icon": command.icon
            }),
            children: [
              command.icon && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(icon_default, { icon: command.icon }),
              /* @__PURE__ */ (0, import_jsx_runtime2.jsx)("span", { children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
                import_components.TextHighlight,
                {
                  text: command.label,
                  highlight: search
                }
              ) })
            ]
          }
        )
      },
      command.name
    )) });
  }
  function CommandMenuLoaderWrapper({ hook, search, setLoader, close: close2 }) {
    const currentLoaderRef = (0, import_element2.useRef)(hook);
    const [key, setKey] = (0, import_element2.useState)(0);
    (0, import_element2.useEffect)(() => {
      if (currentLoaderRef.current !== hook) {
        currentLoaderRef.current = hook;
        setKey((prevKey) => prevKey + 1);
      }
    }, [hook]);
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
      CommandMenuLoader,
      {
        hook: currentLoaderRef.current,
        search,
        setLoader,
        close: close2
      },
      key
    );
  }
  function CommandMenuGroup({ isContextual, search, setLoader, close: close2 }) {
    const { commands: commands2, loaders } = (0, import_data4.useSelect)(
      (select) => {
        const { getCommands: getCommands2, getCommandLoaders: getCommandLoaders2 } = select(store);
        return {
          commands: getCommands2(isContextual),
          loaders: getCommandLoaders2(isContextual)
        };
      },
      [isContextual]
    );
    if (!commands2.length && !loaders.length) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(He.Group, { children: [
      commands2.map((command) => /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
        He.Item,
        {
          value: command.searchLabel ?? command.label,
          keywords: command.keywords,
          onSelect: () => command.callback({ close: close2 }),
          id: command.name,
          children: /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(
            import_components.__experimentalHStack,
            {
              alignment: "left",
              className: clsx_default("commands-command-menu__item", {
                "has-icon": command.icon
              }),
              children: [
                command.icon && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(icon_default, { icon: command.icon }),
                /* @__PURE__ */ (0, import_jsx_runtime2.jsx)("span", { children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
                  import_components.TextHighlight,
                  {
                    text: command.label,
                    highlight: search
                  }
                ) })
              ]
            }
          )
        },
        command.name
      )),
      loaders.map((loader) => /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
        CommandMenuLoaderWrapper,
        {
          hook: loader.hook,
          search,
          setLoader,
          close: close2
        },
        loader.name
      ))
    ] });
  }
  function CommandInput({ isOpen: isOpen3, search, setSearch }) {
    const commandMenuInput = (0, import_element2.useRef)();
    const _value = D2((state) => state.value);
    const selectedItemId = (0, import_element2.useMemo)(() => {
      const item = document.querySelector(
        `[cmdk-item=""][data-value="${_value}"]`
      );
      return item?.getAttribute("id");
    }, [_value]);
    (0, import_element2.useEffect)(() => {
      if (isOpen3) {
        commandMenuInput.current.focus();
      }
    }, [isOpen3]);
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
      He.Input,
      {
        ref: commandMenuInput,
        value: search,
        onValueChange: setSearch,
        placeholder: inputLabel,
        "aria-activedescendant": selectedItemId,
        icon: search
      }
    );
  }
  function CommandMenu() {
    const { registerShortcut } = (0, import_data4.useDispatch)(import_keyboard_shortcuts.store);
    const [search, setSearch] = (0, import_element2.useState)("");
    const isOpen3 = (0, import_data4.useSelect)(
      (select) => select(store).isOpen(),
      []
    );
    const { open: open2, close: close2 } = (0, import_data4.useDispatch)(store);
    const [loaders, setLoaders] = (0, import_element2.useState)({});
    (0, import_element2.useEffect)(() => {
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
      /** @type {import('react').KeyboardEventHandler} */
      (event) => {
        if (event.defaultPrevented) {
          return;
        }
        event.preventDefault();
        if (isOpen3) {
          close2();
        } else {
          open2();
        }
      },
      {
        bindGlobal: true
      }
    );
    const setLoader = (0, import_element2.useCallback)(
      (name, value) => setLoaders((current) => ({
        ...current,
        [name]: value
      })),
      []
    );
    const closeAndReset = () => {
      setSearch("");
      close2();
    };
    if (!isOpen3) {
      return false;
    }
    const onKeyDown = (event) => {
      if (
        // Ignore keydowns from IMEs
        event.nativeEvent.isComposing || // Workaround for Mac Safari where the final Enter/Backspace of an IME composition
        // is `isComposing=false`, even though it's technically still part of the composition.
        // These can only be detected by keyCode.
        event.keyCode === 229
      ) {
        event.preventDefault();
      }
    };
    const isLoading = Object.values(loaders).some(Boolean);
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
      import_components.Modal,
      {
        className: "commands-command-menu",
        overlayClassName: "commands-command-menu__overlay",
        onRequestClose: closeAndReset,
        __experimentalHideHeader: true,
        contentLabel: (0, import_i18n.__)("Command palette"),
        children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)("div", { className: "commands-command-menu__container", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(He, { label: inputLabel, onKeyDown, children: [
          /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)("div", { className: "commands-command-menu__header", children: [
            /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
              icon_default,
              {
                className: "commands-command-menu__header-search-icon",
                icon: search_default
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
              CommandInput,
              {
                search,
                setSearch,
                isOpen: isOpen3
              }
            )
          ] }),
          /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(He.List, { label: (0, import_i18n.__)("Command suggestions"), children: [
            search && !isLoading && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(He.Empty, { children: (0, import_i18n.__)("No results found.") }),
            /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
              CommandMenuGroup,
              {
                search,
                setLoader,
                close: closeAndReset,
                isContextual: true
              }
            ),
            search && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
              CommandMenuGroup,
              {
                search,
                setLoader,
                close: closeAndReset
              }
            )
          ] })
        ] }) })
      }
    );
  }

  // packages/commands/build-module/hooks/use-command-context.js
  var import_element3 = __toESM(require_element());
  var import_data5 = __toESM(require_data());
  function useCommandContext(context2) {
    const { getContext: getContext2 } = (0, import_data5.useSelect)(store);
    const initialContext = (0, import_element3.useRef)(getContext2());
    const { setContext: setContext2 } = unlock((0, import_data5.useDispatch)(store));
    (0, import_element3.useEffect)(() => {
      setContext2(context2);
    }, [context2, setContext2]);
    (0, import_element3.useEffect)(() => {
      const initialContextRef = initialContext.current;
      return () => setContext2(initialContextRef);
    }, [setContext2]);
  }

  // packages/commands/build-module/private-apis.js
  var privateApis = {};
  lock(privateApis, {
    useCommandContext
  });

  // packages/commands/build-module/hooks/use-command.js
  var import_element4 = __toESM(require_element());
  var import_data6 = __toESM(require_data());
  function useCommand(command) {
    const { registerCommand: registerCommand2, unregisterCommand: unregisterCommand2 } = (0, import_data6.useDispatch)(store);
    const currentCallbackRef = (0, import_element4.useRef)(command.callback);
    (0, import_element4.useEffect)(() => {
      currentCallbackRef.current = command.callback;
    }, [command.callback]);
    (0, import_element4.useEffect)(() => {
      if (command.disabled) {
        return;
      }
      registerCommand2({
        name: command.name,
        context: command.context,
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
      command.keywords,
      command.disabled,
      registerCommand2,
      unregisterCommand2
    ]);
  }
  function useCommands(commands2) {
    const { registerCommand: registerCommand2, unregisterCommand: unregisterCommand2 } = (0, import_data6.useDispatch)(store);
    const currentCallbacksRef = (0, import_element4.useRef)({});
    (0, import_element4.useEffect)(() => {
      if (!commands2) {
        return;
      }
      commands2.forEach((command) => {
        if (command.callback) {
          currentCallbacksRef.current[command.name] = command.callback;
        }
      });
    }, [commands2]);
    (0, import_element4.useEffect)(() => {
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

  // packages/commands/build-module/hooks/use-command-loader.js
  var import_element5 = __toESM(require_element());
  var import_data7 = __toESM(require_data());
  function useCommandLoader(loader) {
    const { registerCommandLoader: registerCommandLoader2, unregisterCommandLoader: unregisterCommandLoader2 } = (0, import_data7.useDispatch)(store);
    (0, import_element5.useEffect)(() => {
      if (loader.disabled) {
        return;
      }
      registerCommandLoader2({
        name: loader.name,
        hook: loader.hook,
        context: loader.context
      });
      return () => {
        unregisterCommandLoader2(loader.name);
      };
    }, [
      loader.name,
      loader.hook,
      loader.context,
      loader.disabled,
      registerCommandLoader2,
      unregisterCommandLoader2
    ]);
  }
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
