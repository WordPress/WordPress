var __create = Object.create;
var __defProp = Object.defineProperty;
var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __getProtoOf = Object.getPrototypeOf;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __commonJS = (cb, mod) => function __require() {
  return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
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

// package-external:@wordpress/element
var require_element = __commonJS({
  "package-external:@wordpress/element"(exports, module) {
    module.exports = window.wp.element;
  }
});

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

// package-external:@wordpress/private-apis
var require_private_apis = __commonJS({
  "package-external:@wordpress/private-apis"(exports, module) {
    module.exports = window.wp.privateApis;
  }
});

// packages/workflow/build-module/index.js
var import_element3 = __toESM(require_element());

// node_modules/cmdk/dist/chunk-NZJY6EH4.mjs
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

// node_modules/@radix-ui/react-dialog/dist/index.mjs
var React37 = __toESM(require_react(), 1);

// node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/primitive/dist/index.mjs
var canUseDOM = !!(typeof window !== "undefined" && window.document && window.document.createElement);
function composeEventHandlers(originalEventHandler, ourEventHandler, { checkForDefaultPrevented = true } = {}) {
  return function handleEvent(event) {
    originalEventHandler?.(event);
    if (checkForDefaultPrevented === false || !event.defaultPrevented) {
      return ourEventHandler?.(event);
    }
  };
}

// node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-compose-refs/dist/index.mjs
var React = __toESM(require_react(), 1);
function setRef(ref, value) {
  if (typeof ref === "function") {
    return ref(value);
  } else if (ref !== null && ref !== void 0) {
    ref.current = value;
  }
}
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
function useComposedRefs(...refs) {
  return React.useCallback(composeRefs(...refs), refs);
}

// node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-context/dist/index.mjs
var React2 = __toESM(require_react(), 1);
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
function createContext2(rootComponentName, defaultContext) {
  const Context = React2.createContext(defaultContext);
  const Provider = (props) => {
    const { children, ...context } = props;
    const value = React2.useMemo(() => context, Object.values(context));
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(Context.Provider, { value, children });
  };
  Provider.displayName = rootComponentName + "Provider";
  function useContext22(consumerName) {
    const context = React2.useContext(Context);
    if (context) return context;
    if (defaultContext !== void 0) return defaultContext;
    throw new Error(`\`${consumerName}\` must be used within \`${rootComponentName}\``);
  }
  return [Provider, useContext22];
}
function createContextScope(scopeName, createContextScopeDeps = []) {
  let defaultContexts = [];
  function createContext32(rootComponentName, defaultContext) {
    const BaseContext = React2.createContext(defaultContext);
    const index = defaultContexts.length;
    defaultContexts = [...defaultContexts, defaultContext];
    const Provider = (props) => {
      const { scope, children, ...context } = props;
      const Context = scope?.[scopeName]?.[index] || BaseContext;
      const value = React2.useMemo(() => context, Object.values(context));
      return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(Context.Provider, { value, children });
    };
    Provider.displayName = rootComponentName + "Provider";
    function useContext22(consumerName, scope) {
      const Context = scope?.[scopeName]?.[index] || BaseContext;
      const context = React2.useContext(Context);
      if (context) return context;
      if (defaultContext !== void 0) return defaultContext;
      throw new Error(`\`${consumerName}\` must be used within \`${rootComponentName}\``);
    }
    return [Provider, useContext22];
  }
  const createScope = () => {
    const scopeContexts = defaultContexts.map((defaultContext) => {
      return React2.createContext(defaultContext);
    });
    return function useScope(scope) {
      const contexts = scope?.[scopeName] || scopeContexts;
      return React2.useMemo(
        () => ({ [`__scope${scopeName}`]: { ...scope, [scopeName]: contexts } }),
        [scope, contexts]
      );
    };
  };
  createScope.scopeName = scopeName;
  return [createContext32, composeContextScopes(createScope, ...createContextScopeDeps)];
}
function composeContextScopes(...scopes) {
  const baseScope = scopes[0];
  if (scopes.length === 1) return baseScope;
  const createScope = () => {
    const scopeHooks = scopes.map((createScope2) => ({
      useScope: createScope2(),
      scopeName: createScope2.scopeName
    }));
    return function useComposedScopes(overrideScopes) {
      const nextScopes = scopeHooks.reduce((nextScopes2, { useScope, scopeName }) => {
        const scopeProps = useScope(overrideScopes);
        const currentScope = scopeProps[`__scope${scopeName}`];
        return { ...nextScopes2, ...currentScope };
      }, {});
      return React2.useMemo(() => ({ [`__scope${baseScope.scopeName}`]: nextScopes }), [nextScopes]);
    };
  };
  createScope.scopeName = baseScope.scopeName;
  return createScope;
}

// node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-id/dist/index.mjs
var React4 = __toESM(require_react(), 1);

// node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-use-layout-effect/dist/index.mjs
var React3 = __toESM(require_react(), 1);
var useLayoutEffect2 = globalThis?.document ? React3.useLayoutEffect : () => {
};

// node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-id/dist/index.mjs
var useReactId = React4[" useId ".trim().toString()] || (() => void 0);
var count = 0;
function useId(deterministicId) {
  const [id, setId] = React4.useState(useReactId());
  useLayoutEffect2(() => {
    if (!deterministicId) setId((reactId) => reactId ?? String(count++));
  }, [deterministicId]);
  return deterministicId || (id ? `radix-${id}` : "");
}

// node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-use-controllable-state/dist/index.mjs
var React5 = __toESM(require_react(), 1);
var React22 = __toESM(require_react(), 1);
var useInsertionEffect = React5[" useInsertionEffect ".trim().toString()] || useLayoutEffect2;
function useControllableState({
  prop,
  defaultProp,
  onChange = () => {
  },
  caller
}) {
  const [uncontrolledProp, setUncontrolledProp, onChangeRef] = useUncontrolledState({
    defaultProp,
    onChange
  });
  const isControlled = prop !== void 0;
  const value = isControlled ? prop : uncontrolledProp;
  if (true) {
    const isControlledRef = React5.useRef(prop !== void 0);
    React5.useEffect(() => {
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
  const setValue = React5.useCallback(
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
function useUncontrolledState({
  defaultProp,
  onChange
}) {
  const [value, setValue] = React5.useState(defaultProp);
  const prevValueRef = React5.useRef(value);
  const onChangeRef = React5.useRef(onChange);
  useInsertionEffect(() => {
    onChangeRef.current = onChange;
  }, [onChange]);
  React5.useEffect(() => {
    if (prevValueRef.current !== value) {
      onChangeRef.current?.(value);
      prevValueRef.current = value;
    }
  }, [value, prevValueRef]);
  return [value, setValue, onChangeRef];
}
function isFunction(value) {
  return typeof value === "function";
}
var SYNC_STATE = Symbol("RADIX:SYNC_STATE");

// node_modules/@radix-ui/react-dismissable-layer/dist/index.mjs
var React11 = __toESM(require_react(), 1);

// node_modules/@radix-ui/react-dismissable-layer/node_modules/@radix-ui/primitive/dist/index.mjs
var canUseDOM2 = !!(typeof window !== "undefined" && window.document && window.document.createElement);
function composeEventHandlers2(originalEventHandler, ourEventHandler, { checkForDefaultPrevented = true } = {}) {
  return function handleEvent(event) {
    originalEventHandler?.(event);
    if (checkForDefaultPrevented === false || !event.defaultPrevented) {
      return ourEventHandler?.(event);
    }
  };
}

// node_modules/@radix-ui/react-dismissable-layer/node_modules/@radix-ui/react-primitive/dist/index.mjs
var React8 = __toESM(require_react(), 1);
var ReactDOM = __toESM(require_react_dom(), 1);

// node_modules/@radix-ui/react-dismissable-layer/node_modules/@radix-ui/react-slot/dist/index.mjs
var React7 = __toESM(require_react(), 1);

// node_modules/@radix-ui/react-dismissable-layer/node_modules/@radix-ui/react-compose-refs/dist/index.mjs
var React6 = __toESM(require_react(), 1);
function setRef2(ref, value) {
  if (typeof ref === "function") {
    return ref(value);
  } else if (ref !== null && ref !== void 0) {
    ref.current = value;
  }
}
function composeRefs2(...refs) {
  return (node) => {
    let hasCleanup = false;
    const cleanups = refs.map((ref) => {
      const cleanup = setRef2(ref, node);
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
            setRef2(refs[i], null);
          }
        }
      };
    }
  };
}
function useComposedRefs2(...refs) {
  return React6.useCallback(composeRefs2(...refs), refs);
}

// node_modules/@radix-ui/react-dismissable-layer/node_modules/@radix-ui/react-slot/dist/index.mjs
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
// @__NO_SIDE_EFFECTS__
function createSlot(ownerName) {
  const SlotClone = /* @__PURE__ */ createSlotClone(ownerName);
  const Slot2 = React7.forwardRef((props, forwardedRef) => {
    const { children, ...slotProps } = props;
    const childrenArray = React7.Children.toArray(children);
    const slottable = childrenArray.find(isSlottable);
    if (slottable) {
      const newElement = slottable.props.children;
      const newChildren = childrenArray.map((child) => {
        if (child === slottable) {
          if (React7.Children.count(newElement) > 1) return React7.Children.only(null);
          return React7.isValidElement(newElement) ? newElement.props.children : null;
        } else {
          return child;
        }
      });
      return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(SlotClone, { ...slotProps, ref: forwardedRef, children: React7.isValidElement(newElement) ? React7.cloneElement(newElement, void 0, newChildren) : null });
    }
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(SlotClone, { ...slotProps, ref: forwardedRef, children });
  });
  Slot2.displayName = `${ownerName}.Slot`;
  return Slot2;
}
// @__NO_SIDE_EFFECTS__
function createSlotClone(ownerName) {
  const SlotClone = React7.forwardRef((props, forwardedRef) => {
    const { children, ...slotProps } = props;
    if (React7.isValidElement(children)) {
      const childrenRef = getElementRef(children);
      const props2 = mergeProps(slotProps, children.props);
      if (children.type !== React7.Fragment) {
        props2.ref = forwardedRef ? composeRefs2(forwardedRef, childrenRef) : childrenRef;
      }
      return React7.cloneElement(children, props2);
    }
    return React7.Children.count(children) > 1 ? React7.Children.only(null) : null;
  });
  SlotClone.displayName = `${ownerName}.SlotClone`;
  return SlotClone;
}
var SLOTTABLE_IDENTIFIER = Symbol("radix.slottable");
function isSlottable(child) {
  return React7.isValidElement(child) && typeof child.type === "function" && "__radixId" in child.type && child.type.__radixId === SLOTTABLE_IDENTIFIER;
}
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

// node_modules/@radix-ui/react-dismissable-layer/node_modules/@radix-ui/react-primitive/dist/index.mjs
var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
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
      window[Symbol.for("radix-ui")] = true;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(Comp, { ...primitiveProps, ref: forwardedRef });
  });
  Node2.displayName = `Primitive.${node}`;
  return { ...primitive, [node]: Node2 };
}, {});
function dispatchDiscreteCustomEvent(target, event) {
  if (target) ReactDOM.flushSync(() => target.dispatchEvent(event));
}

// node_modules/@radix-ui/react-dismissable-layer/node_modules/@radix-ui/react-use-callback-ref/dist/index.mjs
var React9 = __toESM(require_react(), 1);
function useCallbackRef(callback) {
  const callbackRef = React9.useRef(callback);
  React9.useEffect(() => {
    callbackRef.current = callback;
  });
  return React9.useMemo(() => (...args) => callbackRef.current?.(...args), []);
}

// node_modules/@radix-ui/react-dismissable-layer/node_modules/@radix-ui/react-use-escape-keydown/dist/index.mjs
var React10 = __toESM(require_react(), 1);
function useEscapeKeydown(onEscapeKeyDownProp, ownerDocument = globalThis?.document) {
  const onEscapeKeyDown = useCallbackRef(onEscapeKeyDownProp);
  React10.useEffect(() => {
    const handleKeyDown = (event) => {
      if (event.key === "Escape") {
        onEscapeKeyDown(event);
      }
    };
    ownerDocument.addEventListener("keydown", handleKeyDown, { capture: true });
    return () => ownerDocument.removeEventListener("keydown", handleKeyDown, { capture: true });
  }, [onEscapeKeyDown, ownerDocument]);
}

// node_modules/@radix-ui/react-dismissable-layer/dist/index.mjs
var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
var DISMISSABLE_LAYER_NAME = "DismissableLayer";
var CONTEXT_UPDATE = "dismissableLayer.update";
var POINTER_DOWN_OUTSIDE = "dismissableLayer.pointerDownOutside";
var FOCUS_OUTSIDE = "dismissableLayer.focusOutside";
var originalBodyPointerEvents;
var DismissableLayerContext = React11.createContext({
  layers: /* @__PURE__ */ new Set(),
  layersWithOutsidePointerEventsDisabled: /* @__PURE__ */ new Set(),
  branches: /* @__PURE__ */ new Set()
});
var DismissableLayer = React11.forwardRef(
  (props, forwardedRef) => {
    const {
      disableOutsidePointerEvents = false,
      onEscapeKeyDown,
      onPointerDownOutside,
      onFocusOutside,
      onInteractOutside,
      onDismiss,
      ...layerProps
    } = props;
    const context = React11.useContext(DismissableLayerContext);
    const [node, setNode] = React11.useState(null);
    const ownerDocument = node?.ownerDocument ?? globalThis?.document;
    const [, force] = React11.useState({});
    const composedRefs = useComposedRefs2(forwardedRef, (node2) => setNode(node2));
    const layers = Array.from(context.layers);
    const [highestLayerWithOutsidePointerEventsDisabled] = [...context.layersWithOutsidePointerEventsDisabled].slice(-1);
    const highestLayerWithOutsidePointerEventsDisabledIndex = layers.indexOf(highestLayerWithOutsidePointerEventsDisabled);
    const index = node ? layers.indexOf(node) : -1;
    const isBodyPointerEventsDisabled = context.layersWithOutsidePointerEventsDisabled.size > 0;
    const isPointerEventsEnabled = index >= highestLayerWithOutsidePointerEventsDisabledIndex;
    const pointerDownOutside = usePointerDownOutside((event) => {
      const target = event.target;
      const isPointerDownOnBranch = [...context.branches].some((branch) => branch.contains(target));
      if (!isPointerEventsEnabled || isPointerDownOnBranch) return;
      onPointerDownOutside?.(event);
      onInteractOutside?.(event);
      if (!event.defaultPrevented) onDismiss?.();
    }, ownerDocument);
    const focusOutside = useFocusOutside((event) => {
      const target = event.target;
      const isFocusInBranch = [...context.branches].some((branch) => branch.contains(target));
      if (isFocusInBranch) return;
      onFocusOutside?.(event);
      onInteractOutside?.(event);
      if (!event.defaultPrevented) onDismiss?.();
    }, ownerDocument);
    useEscapeKeydown((event) => {
      const isHighestLayer = index === context.layers.size - 1;
      if (!isHighestLayer) return;
      onEscapeKeyDown?.(event);
      if (!event.defaultPrevented && onDismiss) {
        event.preventDefault();
        onDismiss();
      }
    }, ownerDocument);
    React11.useEffect(() => {
      if (!node) return;
      if (disableOutsidePointerEvents) {
        if (context.layersWithOutsidePointerEventsDisabled.size === 0) {
          originalBodyPointerEvents = ownerDocument.body.style.pointerEvents;
          ownerDocument.body.style.pointerEvents = "none";
        }
        context.layersWithOutsidePointerEventsDisabled.add(node);
      }
      context.layers.add(node);
      dispatchUpdate();
      return () => {
        if (disableOutsidePointerEvents && context.layersWithOutsidePointerEventsDisabled.size === 1) {
          ownerDocument.body.style.pointerEvents = originalBodyPointerEvents;
        }
      };
    }, [node, ownerDocument, disableOutsidePointerEvents, context]);
    React11.useEffect(() => {
      return () => {
        if (!node) return;
        context.layers.delete(node);
        context.layersWithOutsidePointerEventsDisabled.delete(node);
        dispatchUpdate();
      };
    }, [node, context]);
    React11.useEffect(() => {
      const handleUpdate = () => force({});
      document.addEventListener(CONTEXT_UPDATE, handleUpdate);
      return () => document.removeEventListener(CONTEXT_UPDATE, handleUpdate);
    }, []);
    return /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
      Primitive.div,
      {
        ...layerProps,
        ref: composedRefs,
        style: {
          pointerEvents: isBodyPointerEventsDisabled ? isPointerEventsEnabled ? "auto" : "none" : void 0,
          ...props.style
        },
        onFocusCapture: composeEventHandlers2(props.onFocusCapture, focusOutside.onFocusCapture),
        onBlurCapture: composeEventHandlers2(props.onBlurCapture, focusOutside.onBlurCapture),
        onPointerDownCapture: composeEventHandlers2(
          props.onPointerDownCapture,
          pointerDownOutside.onPointerDownCapture
        )
      }
    );
  }
);
DismissableLayer.displayName = DISMISSABLE_LAYER_NAME;
var BRANCH_NAME = "DismissableLayerBranch";
var DismissableLayerBranch = React11.forwardRef((props, forwardedRef) => {
  const context = React11.useContext(DismissableLayerContext);
  const ref = React11.useRef(null);
  const composedRefs = useComposedRefs2(forwardedRef, ref);
  React11.useEffect(() => {
    const node = ref.current;
    if (node) {
      context.branches.add(node);
      return () => {
        context.branches.delete(node);
      };
    }
  }, [context.branches]);
  return /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(Primitive.div, { ...props, ref: composedRefs });
});
DismissableLayerBranch.displayName = BRANCH_NAME;
function usePointerDownOutside(onPointerDownOutside, ownerDocument = globalThis?.document) {
  const handlePointerDownOutside = useCallbackRef(onPointerDownOutside);
  const isPointerInsideReactTreeRef = React11.useRef(false);
  const handleClickRef = React11.useRef(() => {
  });
  React11.useEffect(() => {
    const handlePointerDown = (event) => {
      if (event.target && !isPointerInsideReactTreeRef.current) {
        let handleAndDispatchPointerDownOutsideEvent2 = function() {
          handleAndDispatchCustomEvent(
            POINTER_DOWN_OUTSIDE,
            handlePointerDownOutside,
            eventDetail,
            { discrete: true }
          );
        };
        var handleAndDispatchPointerDownOutsideEvent = handleAndDispatchPointerDownOutsideEvent2;
        const eventDetail = { originalEvent: event };
        if (event.pointerType === "touch") {
          ownerDocument.removeEventListener("click", handleClickRef.current);
          handleClickRef.current = handleAndDispatchPointerDownOutsideEvent2;
          ownerDocument.addEventListener("click", handleClickRef.current, { once: true });
        } else {
          handleAndDispatchPointerDownOutsideEvent2();
        }
      } else {
        ownerDocument.removeEventListener("click", handleClickRef.current);
      }
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
  }, [ownerDocument, handlePointerDownOutside]);
  return {
    // ensures we check React component tree (not just DOM tree)
    onPointerDownCapture: () => isPointerInsideReactTreeRef.current = true
  };
}
function useFocusOutside(onFocusOutside, ownerDocument = globalThis?.document) {
  const handleFocusOutside = useCallbackRef(onFocusOutside);
  const isFocusInsideReactTreeRef = React11.useRef(false);
  React11.useEffect(() => {
    const handleFocus = (event) => {
      if (event.target && !isFocusInsideReactTreeRef.current) {
        const eventDetail = { originalEvent: event };
        handleAndDispatchCustomEvent(FOCUS_OUTSIDE, handleFocusOutside, eventDetail, {
          discrete: false
        });
      }
    };
    ownerDocument.addEventListener("focusin", handleFocus);
    return () => ownerDocument.removeEventListener("focusin", handleFocus);
  }, [ownerDocument, handleFocusOutside]);
  return {
    onFocusCapture: () => isFocusInsideReactTreeRef.current = true,
    onBlurCapture: () => isFocusInsideReactTreeRef.current = false
  };
}
function dispatchUpdate() {
  const event = new CustomEvent(CONTEXT_UPDATE);
  document.dispatchEvent(event);
}
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

// node_modules/@radix-ui/react-focus-scope/dist/index.mjs
var React16 = __toESM(require_react(), 1);

// node_modules/@radix-ui/react-focus-scope/node_modules/@radix-ui/react-compose-refs/dist/index.mjs
var React12 = __toESM(require_react(), 1);
function setRef3(ref, value) {
  if (typeof ref === "function") {
    return ref(value);
  } else if (ref !== null && ref !== void 0) {
    ref.current = value;
  }
}
function composeRefs3(...refs) {
  return (node) => {
    let hasCleanup = false;
    const cleanups = refs.map((ref) => {
      const cleanup = setRef3(ref, node);
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
            setRef3(refs[i], null);
          }
        }
      };
    }
  };
}
function useComposedRefs3(...refs) {
  return React12.useCallback(composeRefs3(...refs), refs);
}

// node_modules/@radix-ui/react-focus-scope/node_modules/@radix-ui/react-primitive/dist/index.mjs
var React14 = __toESM(require_react(), 1);
var ReactDOM2 = __toESM(require_react_dom(), 1);

// node_modules/@radix-ui/react-focus-scope/node_modules/@radix-ui/react-slot/dist/index.mjs
var React13 = __toESM(require_react(), 1);
var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
// @__NO_SIDE_EFFECTS__
function createSlot2(ownerName) {
  const SlotClone = /* @__PURE__ */ createSlotClone2(ownerName);
  const Slot2 = React13.forwardRef((props, forwardedRef) => {
    const { children, ...slotProps } = props;
    const childrenArray = React13.Children.toArray(children);
    const slottable = childrenArray.find(isSlottable2);
    if (slottable) {
      const newElement = slottable.props.children;
      const newChildren = childrenArray.map((child) => {
        if (child === slottable) {
          if (React13.Children.count(newElement) > 1) return React13.Children.only(null);
          return React13.isValidElement(newElement) ? newElement.props.children : null;
        } else {
          return child;
        }
      });
      return /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(SlotClone, { ...slotProps, ref: forwardedRef, children: React13.isValidElement(newElement) ? React13.cloneElement(newElement, void 0, newChildren) : null });
    }
    return /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(SlotClone, { ...slotProps, ref: forwardedRef, children });
  });
  Slot2.displayName = `${ownerName}.Slot`;
  return Slot2;
}
// @__NO_SIDE_EFFECTS__
function createSlotClone2(ownerName) {
  const SlotClone = React13.forwardRef((props, forwardedRef) => {
    const { children, ...slotProps } = props;
    if (React13.isValidElement(children)) {
      const childrenRef = getElementRef2(children);
      const props2 = mergeProps2(slotProps, children.props);
      if (children.type !== React13.Fragment) {
        props2.ref = forwardedRef ? composeRefs3(forwardedRef, childrenRef) : childrenRef;
      }
      return React13.cloneElement(children, props2);
    }
    return React13.Children.count(children) > 1 ? React13.Children.only(null) : null;
  });
  SlotClone.displayName = `${ownerName}.SlotClone`;
  return SlotClone;
}
var SLOTTABLE_IDENTIFIER2 = Symbol("radix.slottable");
function isSlottable2(child) {
  return React13.isValidElement(child) && typeof child.type === "function" && "__radixId" in child.type && child.type.__radixId === SLOTTABLE_IDENTIFIER2;
}
function mergeProps2(slotProps, childProps) {
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

// node_modules/@radix-ui/react-focus-scope/node_modules/@radix-ui/react-primitive/dist/index.mjs
var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
var NODES2 = [
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
var Primitive2 = NODES2.reduce((primitive, node) => {
  const Slot2 = createSlot2(`Primitive.${node}`);
  const Node2 = React14.forwardRef((props, forwardedRef) => {
    const { asChild, ...primitiveProps } = props;
    const Comp = asChild ? Slot2 : node;
    if (typeof window !== "undefined") {
      window[Symbol.for("radix-ui")] = true;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(Comp, { ...primitiveProps, ref: forwardedRef });
  });
  Node2.displayName = `Primitive.${node}`;
  return { ...primitive, [node]: Node2 };
}, {});

// node_modules/@radix-ui/react-focus-scope/node_modules/@radix-ui/react-use-callback-ref/dist/index.mjs
var React15 = __toESM(require_react(), 1);
function useCallbackRef2(callback) {
  const callbackRef = React15.useRef(callback);
  React15.useEffect(() => {
    callbackRef.current = callback;
  });
  return React15.useMemo(() => (...args) => callbackRef.current?.(...args), []);
}

// node_modules/@radix-ui/react-focus-scope/dist/index.mjs
var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
var AUTOFOCUS_ON_MOUNT = "focusScope.autoFocusOnMount";
var AUTOFOCUS_ON_UNMOUNT = "focusScope.autoFocusOnUnmount";
var EVENT_OPTIONS = { bubbles: false, cancelable: true };
var FOCUS_SCOPE_NAME = "FocusScope";
var FocusScope = React16.forwardRef((props, forwardedRef) => {
  const {
    loop = false,
    trapped = false,
    onMountAutoFocus: onMountAutoFocusProp,
    onUnmountAutoFocus: onUnmountAutoFocusProp,
    ...scopeProps
  } = props;
  const [container, setContainer] = React16.useState(null);
  const onMountAutoFocus = useCallbackRef2(onMountAutoFocusProp);
  const onUnmountAutoFocus = useCallbackRef2(onUnmountAutoFocusProp);
  const lastFocusedElementRef = React16.useRef(null);
  const composedRefs = useComposedRefs3(forwardedRef, (node) => setContainer(node));
  const focusScope = React16.useRef({
    paused: false,
    pause() {
      this.paused = true;
    },
    resume() {
      this.paused = false;
    }
  }).current;
  React16.useEffect(() => {
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
  React16.useEffect(() => {
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
  const handleKeyDown = React16.useCallback(
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
  return /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(Primitive2.div, { tabIndex: -1, ...scopeProps, ref: composedRefs, onKeyDown: handleKeyDown });
});
FocusScope.displayName = FOCUS_SCOPE_NAME;
function focusFirst(candidates, { select = false } = {}) {
  const previouslyFocusedElement = document.activeElement;
  for (const candidate of candidates) {
    focus(candidate, { select });
    if (document.activeElement !== previouslyFocusedElement) return;
  }
}
function getTabbableEdges(container) {
  const candidates = getTabbableCandidates(container);
  const first = findVisible(candidates, container);
  const last = findVisible(candidates.reverse(), container);
  return [first, last];
}
function getTabbableCandidates(container) {
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
function findVisible(elements, container) {
  for (const element of elements) {
    if (!isHidden(element, { upTo: container })) return element;
  }
}
function isHidden(node, { upTo }) {
  if (getComputedStyle(node).visibility === "hidden") return true;
  while (node) {
    if (upTo !== void 0 && node === upTo) return false;
    if (getComputedStyle(node).display === "none") return true;
    node = node.parentElement;
  }
  return false;
}
function isSelectableInput(element) {
  return element instanceof HTMLInputElement && "select" in element;
}
function focus(element, { select = false } = {}) {
  if (element && element.focus) {
    const previouslyFocusedElement = document.activeElement;
    element.focus({ preventScroll: true });
    if (element !== previouslyFocusedElement && isSelectableInput(element) && select)
      element.select();
  }
}
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
function arrayRemove(array, item) {
  const updatedArray = [...array];
  const index = updatedArray.indexOf(item);
  if (index !== -1) {
    updatedArray.splice(index, 1);
  }
  return updatedArray;
}
function removeLinks(items) {
  return items.filter((item) => item.tagName !== "A");
}

// node_modules/@radix-ui/react-portal/dist/index.mjs
var React21 = __toESM(require_react(), 1);
var import_react_dom = __toESM(require_react_dom(), 1);

// node_modules/@radix-ui/react-portal/node_modules/@radix-ui/react-primitive/dist/index.mjs
var React19 = __toESM(require_react(), 1);
var ReactDOM3 = __toESM(require_react_dom(), 1);

// node_modules/@radix-ui/react-portal/node_modules/@radix-ui/react-slot/dist/index.mjs
var React18 = __toESM(require_react(), 1);

// node_modules/@radix-ui/react-portal/node_modules/@radix-ui/react-compose-refs/dist/index.mjs
var React17 = __toESM(require_react(), 1);
function setRef4(ref, value) {
  if (typeof ref === "function") {
    return ref(value);
  } else if (ref !== null && ref !== void 0) {
    ref.current = value;
  }
}
function composeRefs4(...refs) {
  return (node) => {
    let hasCleanup = false;
    const cleanups = refs.map((ref) => {
      const cleanup = setRef4(ref, node);
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
            setRef4(refs[i], null);
          }
        }
      };
    }
  };
}

// node_modules/@radix-ui/react-portal/node_modules/@radix-ui/react-slot/dist/index.mjs
var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
// @__NO_SIDE_EFFECTS__
function createSlot3(ownerName) {
  const SlotClone = /* @__PURE__ */ createSlotClone3(ownerName);
  const Slot2 = React18.forwardRef((props, forwardedRef) => {
    const { children, ...slotProps } = props;
    const childrenArray = React18.Children.toArray(children);
    const slottable = childrenArray.find(isSlottable3);
    if (slottable) {
      const newElement = slottable.props.children;
      const newChildren = childrenArray.map((child) => {
        if (child === slottable) {
          if (React18.Children.count(newElement) > 1) return React18.Children.only(null);
          return React18.isValidElement(newElement) ? newElement.props.children : null;
        } else {
          return child;
        }
      });
      return /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(SlotClone, { ...slotProps, ref: forwardedRef, children: React18.isValidElement(newElement) ? React18.cloneElement(newElement, void 0, newChildren) : null });
    }
    return /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(SlotClone, { ...slotProps, ref: forwardedRef, children });
  });
  Slot2.displayName = `${ownerName}.Slot`;
  return Slot2;
}
// @__NO_SIDE_EFFECTS__
function createSlotClone3(ownerName) {
  const SlotClone = React18.forwardRef((props, forwardedRef) => {
    const { children, ...slotProps } = props;
    if (React18.isValidElement(children)) {
      const childrenRef = getElementRef3(children);
      const props2 = mergeProps3(slotProps, children.props);
      if (children.type !== React18.Fragment) {
        props2.ref = forwardedRef ? composeRefs4(forwardedRef, childrenRef) : childrenRef;
      }
      return React18.cloneElement(children, props2);
    }
    return React18.Children.count(children) > 1 ? React18.Children.only(null) : null;
  });
  SlotClone.displayName = `${ownerName}.SlotClone`;
  return SlotClone;
}
var SLOTTABLE_IDENTIFIER3 = Symbol("radix.slottable");
function isSlottable3(child) {
  return React18.isValidElement(child) && typeof child.type === "function" && "__radixId" in child.type && child.type.__radixId === SLOTTABLE_IDENTIFIER3;
}
function mergeProps3(slotProps, childProps) {
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
function getElementRef3(element) {
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

// node_modules/@radix-ui/react-portal/node_modules/@radix-ui/react-primitive/dist/index.mjs
var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
var NODES3 = [
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
var Primitive3 = NODES3.reduce((primitive, node) => {
  const Slot2 = createSlot3(`Primitive.${node}`);
  const Node2 = React19.forwardRef((props, forwardedRef) => {
    const { asChild, ...primitiveProps } = props;
    const Comp = asChild ? Slot2 : node;
    if (typeof window !== "undefined") {
      window[Symbol.for("radix-ui")] = true;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(Comp, { ...primitiveProps, ref: forwardedRef });
  });
  Node2.displayName = `Primitive.${node}`;
  return { ...primitive, [node]: Node2 };
}, {});

// node_modules/@radix-ui/react-portal/node_modules/@radix-ui/react-use-layout-effect/dist/index.mjs
var React20 = __toESM(require_react(), 1);
var useLayoutEffect22 = globalThis?.document ? React20.useLayoutEffect : () => {
};

// node_modules/@radix-ui/react-portal/dist/index.mjs
var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
var PORTAL_NAME = "Portal";
var Portal = React21.forwardRef((props, forwardedRef) => {
  const { container: containerProp, ...portalProps } = props;
  const [mounted, setMounted] = React21.useState(false);
  useLayoutEffect22(() => setMounted(true), []);
  const container = containerProp || mounted && globalThis?.document?.body;
  return container ? import_react_dom.default.createPortal(/* @__PURE__ */ (0, import_jsx_runtime10.jsx)(Primitive3.div, { ...portalProps, ref: forwardedRef }), container) : null;
});
Portal.displayName = PORTAL_NAME;

// node_modules/@radix-ui/react-presence/dist/index.mjs
var React25 = __toESM(require_react(), 1);

// node_modules/@radix-ui/react-presence/node_modules/@radix-ui/react-compose-refs/dist/index.mjs
var React23 = __toESM(require_react(), 1);
function setRef5(ref, value) {
  if (typeof ref === "function") {
    return ref(value);
  } else if (ref !== null && ref !== void 0) {
    ref.current = value;
  }
}
function composeRefs5(...refs) {
  return (node) => {
    let hasCleanup = false;
    const cleanups = refs.map((ref) => {
      const cleanup = setRef5(ref, node);
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
            setRef5(refs[i], null);
          }
        }
      };
    }
  };
}
function useComposedRefs4(...refs) {
  return React23.useCallback(composeRefs5(...refs), refs);
}

// node_modules/@radix-ui/react-presence/node_modules/@radix-ui/react-use-layout-effect/dist/index.mjs
var React24 = __toESM(require_react(), 1);
var useLayoutEffect23 = globalThis?.document ? React24.useLayoutEffect : () => {
};

// node_modules/@radix-ui/react-presence/dist/index.mjs
var React26 = __toESM(require_react(), 1);
function useStateMachine(initialState, machine) {
  return React26.useReducer((state, event) => {
    const nextState = machine[state][event];
    return nextState ?? state;
  }, initialState);
}
var Presence = (props) => {
  const { present, children } = props;
  const presence = usePresence(present);
  const child = typeof children === "function" ? children({ present: presence.isPresent }) : React25.Children.only(children);
  const ref = useComposedRefs4(presence.ref, getElementRef4(child));
  const forceMount = typeof children === "function";
  return forceMount || presence.isPresent ? React25.cloneElement(child, { ref }) : null;
};
Presence.displayName = "Presence";
function usePresence(present) {
  const [node, setNode] = React25.useState();
  const stylesRef = React25.useRef(null);
  const prevPresentRef = React25.useRef(present);
  const prevAnimationNameRef = React25.useRef("none");
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
  React25.useEffect(() => {
    const currentAnimationName = getAnimationName(stylesRef.current);
    prevAnimationNameRef.current = state === "mounted" ? currentAnimationName : "none";
  }, [state]);
  useLayoutEffect23(() => {
    const styles = stylesRef.current;
    const wasPresent = prevPresentRef.current;
    const hasPresentChanged = wasPresent !== present;
    if (hasPresentChanged) {
      const prevAnimationName = prevAnimationNameRef.current;
      const currentAnimationName = getAnimationName(styles);
      if (present) {
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
  useLayoutEffect23(() => {
    if (node) {
      let timeoutId;
      const ownerWindow = node.ownerDocument.defaultView ?? window;
      const handleAnimationEnd = (event) => {
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
      };
      const handleAnimationStart = (event) => {
        if (event.target === node) {
          prevAnimationNameRef.current = getAnimationName(stylesRef.current);
        }
      };
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
    ref: React25.useCallback((node2) => {
      stylesRef.current = node2 ? getComputedStyle(node2) : null;
      setNode(node2);
    }, [])
  };
}
function getAnimationName(styles) {
  return styles?.animationName || "none";
}
function getElementRef4(element) {
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

// node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-primitive/dist/index.mjs
var React28 = __toESM(require_react(), 1);
var ReactDOM5 = __toESM(require_react_dom(), 1);

// node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-slot/dist/index.mjs
var React27 = __toESM(require_react(), 1);
var import_jsx_runtime11 = __toESM(require_jsx_runtime(), 1);
// @__NO_SIDE_EFFECTS__
function createSlot4(ownerName) {
  const SlotClone = /* @__PURE__ */ createSlotClone4(ownerName);
  const Slot2 = React27.forwardRef((props, forwardedRef) => {
    const { children, ...slotProps } = props;
    const childrenArray = React27.Children.toArray(children);
    const slottable = childrenArray.find(isSlottable4);
    if (slottable) {
      const newElement = slottable.props.children;
      const newChildren = childrenArray.map((child) => {
        if (child === slottable) {
          if (React27.Children.count(newElement) > 1) return React27.Children.only(null);
          return React27.isValidElement(newElement) ? newElement.props.children : null;
        } else {
          return child;
        }
      });
      return /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(SlotClone, { ...slotProps, ref: forwardedRef, children: React27.isValidElement(newElement) ? React27.cloneElement(newElement, void 0, newChildren) : null });
    }
    return /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(SlotClone, { ...slotProps, ref: forwardedRef, children });
  });
  Slot2.displayName = `${ownerName}.Slot`;
  return Slot2;
}
// @__NO_SIDE_EFFECTS__
function createSlotClone4(ownerName) {
  const SlotClone = React27.forwardRef((props, forwardedRef) => {
    const { children, ...slotProps } = props;
    if (React27.isValidElement(children)) {
      const childrenRef = getElementRef5(children);
      const props2 = mergeProps4(slotProps, children.props);
      if (children.type !== React27.Fragment) {
        props2.ref = forwardedRef ? composeRefs(forwardedRef, childrenRef) : childrenRef;
      }
      return React27.cloneElement(children, props2);
    }
    return React27.Children.count(children) > 1 ? React27.Children.only(null) : null;
  });
  SlotClone.displayName = `${ownerName}.SlotClone`;
  return SlotClone;
}
var SLOTTABLE_IDENTIFIER4 = Symbol("radix.slottable");
function isSlottable4(child) {
  return React27.isValidElement(child) && typeof child.type === "function" && "__radixId" in child.type && child.type.__radixId === SLOTTABLE_IDENTIFIER4;
}
function mergeProps4(slotProps, childProps) {
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
function getElementRef5(element) {
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

// node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-primitive/dist/index.mjs
var import_jsx_runtime12 = __toESM(require_jsx_runtime(), 1);
var NODES4 = [
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
var Primitive4 = NODES4.reduce((primitive, node) => {
  const Slot2 = createSlot4(`Primitive.${node}`);
  const Node2 = React28.forwardRef((props, forwardedRef) => {
    const { asChild, ...primitiveProps } = props;
    const Comp = asChild ? Slot2 : node;
    if (typeof window !== "undefined") {
      window[Symbol.for("radix-ui")] = true;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(Comp, { ...primitiveProps, ref: forwardedRef });
  });
  Node2.displayName = `Primitive.${node}`;
  return { ...primitive, [node]: Node2 };
}, {});

// node_modules/@radix-ui/react-dialog/node_modules/@radix-ui/react-focus-guards/dist/index.mjs
var React29 = __toESM(require_react(), 1);
var count2 = 0;
function useFocusGuards() {
  React29.useEffect(() => {
    const edgeGuards = document.querySelectorAll("[data-radix-focus-guard]");
    document.body.insertAdjacentElement("afterbegin", edgeGuards[0] ?? createFocusGuard());
    document.body.insertAdjacentElement("beforeend", edgeGuards[1] ?? createFocusGuard());
    count2++;
    return () => {
      if (count2 === 1) {
        document.querySelectorAll("[data-radix-focus-guard]").forEach((node) => node.remove());
      }
      count2--;
    };
  }, []);
}
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

// node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/Combination.js
var React36 = __toESM(require_react());

// node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/UI.js
var React32 = __toESM(require_react());

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
var import_react = __toESM(require_react());
function useCallbackRef3(initialValue, callback) {
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

// node_modules/use-callback-ref/dist/es2015/useMergeRef.js
var React30 = __toESM(require_react());
var useIsomorphicLayoutEffect = typeof window !== "undefined" ? React30.useLayoutEffect : React30.useEffect;
var currentValues = /* @__PURE__ */ new WeakMap();
function useMergeRefs(refs, defaultValue) {
  var callbackRef = useCallbackRef3(defaultValue || null, function(newValue) {
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
var React31 = __toESM(require_react());
var SideCar = function(_a) {
  var sideCar = _a.sideCar, rest = __rest(_a, ["sideCar"]);
  if (!sideCar) {
    throw new Error("Sidecar: please provide `sideCar` property to import the right car");
  }
  var Target = sideCar.read();
  if (!Target) {
    throw new Error("Sidecar medium not found");
  }
  return React31.createElement(Target, __assign({}, rest));
};
SideCar.isSideCarExport = true;
function exportSidecar(medium, exported) {
  medium.useMedium(exported);
  return SideCar;
}

// node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/medium.js
var effectCar = createSidecarMedium();

// node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/UI.js
var nothing = function() {
  return;
};
var RemoveScroll = React32.forwardRef(function(props, parentRef) {
  var ref = React32.useRef(null);
  var _a = React32.useState({
    onScrollCapture: nothing,
    onWheelCapture: nothing,
    onTouchMoveCapture: nothing
  }), callbacks = _a[0], setCallbacks = _a[1];
  var forwardProps = props.forwardProps, children = props.children, className = props.className, removeScrollBar = props.removeScrollBar, enabled = props.enabled, shards = props.shards, sideCar = props.sideCar, noRelative = props.noRelative, noIsolation = props.noIsolation, inert = props.inert, allowPinchZoom = props.allowPinchZoom, _b = props.as, Container = _b === void 0 ? "div" : _b, gapMode = props.gapMode, rest = __rest(props, ["forwardProps", "children", "className", "removeScrollBar", "enabled", "shards", "sideCar", "noRelative", "noIsolation", "inert", "allowPinchZoom", "as", "gapMode"]);
  var SideCar2 = sideCar;
  var containerRef = useMergeRefs([ref, parentRef]);
  var containerProps = __assign(__assign({}, rest), callbacks);
  return React32.createElement(
    React32.Fragment,
    null,
    enabled && React32.createElement(SideCar2, { sideCar: effectCar, removeScrollBar, shards, noRelative, noIsolation, inert, setCallbacks, allowPinchZoom: !!allowPinchZoom, lockRef: ref, gapMode }),
    forwardProps ? React32.cloneElement(React32.Children.only(children), __assign(__assign({}, containerProps), { ref: containerRef })) : React32.createElement(Container, __assign({}, containerProps, { className, ref: containerRef }), children)
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

// node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/SideEffect.js
var React35 = __toESM(require_react());

// node_modules/react-remove-scroll-bar/dist/es2015/component.js
var React34 = __toESM(require_react());

// node_modules/react-style-singleton/dist/es2015/hook.js
var React33 = __toESM(require_react());

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
function injectStyles(tag, css2) {
  if (tag.styleSheet) {
    tag.styleSheet.cssText = css2;
  } else {
    tag.appendChild(document.createTextNode(css2));
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
    React33.useEffect(function() {
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
  React34.useEffect(function() {
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
  var gap = React34.useMemo(function() {
    return getGapWidth(gapMode);
  }, [gapMode]);
  return React34.createElement(Style, { styles: getStyles(gap, !noRelative, gapMode, !noImportant ? "!important" : "") });
};

// node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/aggresiveCapture.js
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

// node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/handleScroll.js
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

// node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/SideEffect.js
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
  var shouldPreventQueue = React35.useRef([]);
  var touchStartRef = React35.useRef([0, 0]);
  var activeAxis = React35.useRef();
  var id = React35.useState(idCounter++)[0];
  var Style2 = React35.useState(styleSingleton)[0];
  var lastProps = React35.useRef(props);
  React35.useEffect(function() {
    lastProps.current = props;
  }, [props]);
  React35.useEffect(function() {
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
  var shouldCancelEvent = React35.useCallback(function(event, parent) {
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
  var shouldPrevent = React35.useCallback(function(_event) {
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
  var shouldCancel = React35.useCallback(function(name, delta, target, should) {
    var event = { name, delta, target, should, shadowParent: getOutermostShadowParent(target) };
    shouldPreventQueue.current.push(event);
    setTimeout(function() {
      shouldPreventQueue.current = shouldPreventQueue.current.filter(function(e) {
        return e !== event;
      });
    }, 1);
  }, []);
  var scrollTouchStart = React35.useCallback(function(event) {
    touchStartRef.current = getTouchXY(event);
    activeAxis.current = void 0;
  }, []);
  var scrollWheel = React35.useCallback(function(event) {
    shouldCancel(event.type, getDeltaXY(event), event.target, shouldCancelEvent(event, props.lockRef.current));
  }, []);
  var scrollTouchMove = React35.useCallback(function(event) {
    shouldCancel(event.type, getTouchXY(event), event.target, shouldCancelEvent(event, props.lockRef.current));
  }, []);
  React35.useEffect(function() {
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
  return React35.createElement(
    React35.Fragment,
    null,
    inert ? React35.createElement(Style2, { styles: generateStyle(id) }) : null,
    removeScrollBar ? React35.createElement(RemoveScrollBar, { noRelative: props.noRelative, gapMode: props.gapMode }) : null
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

// node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/sidecar.js
var sidecar_default = exportSidecar(effectCar, RemoveScrollSideCar);

// node_modules/@radix-ui/react-dialog/node_modules/react-remove-scroll/dist/es2015/Combination.js
var ReactRemoveScroll = React36.forwardRef(function(props, ref) {
  return React36.createElement(RemoveScroll, __assign({}, props, { ref, sideCar: sidecar_default }));
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

// node_modules/@radix-ui/react-dialog/dist/index.mjs
var import_jsx_runtime13 = __toESM(require_jsx_runtime(), 1);
var DIALOG_NAME = "Dialog";
var [createDialogContext, createDialogScope] = createContextScope(DIALOG_NAME);
var [DialogProvider, useDialogContext] = createDialogContext(DIALOG_NAME);
var Dialog = (props) => {
  const {
    __scopeDialog,
    children,
    open: openProp,
    defaultOpen,
    onOpenChange,
    modal = true
  } = props;
  const triggerRef = React37.useRef(null);
  const contentRef = React37.useRef(null);
  const [open, setOpen] = useControllableState({
    prop: openProp,
    defaultProp: defaultOpen ?? false,
    onChange: onOpenChange,
    caller: DIALOG_NAME
  });
  return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
    DialogProvider,
    {
      scope: __scopeDialog,
      triggerRef,
      contentRef,
      contentId: useId(),
      titleId: useId(),
      descriptionId: useId(),
      open,
      onOpenChange: setOpen,
      onOpenToggle: React37.useCallback(() => setOpen((prevOpen) => !prevOpen), [setOpen]),
      modal,
      children
    }
  );
};
Dialog.displayName = DIALOG_NAME;
var TRIGGER_NAME = "DialogTrigger";
var DialogTrigger = React37.forwardRef(
  (props, forwardedRef) => {
    const { __scopeDialog, ...triggerProps } = props;
    const context = useDialogContext(TRIGGER_NAME, __scopeDialog);
    const composedTriggerRef = useComposedRefs(forwardedRef, context.triggerRef);
    return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
      Primitive4.button,
      {
        type: "button",
        "aria-haspopup": "dialog",
        "aria-expanded": context.open,
        "aria-controls": context.contentId,
        "data-state": getState(context.open),
        ...triggerProps,
        ref: composedTriggerRef,
        onClick: composeEventHandlers(props.onClick, context.onOpenToggle)
      }
    );
  }
);
DialogTrigger.displayName = TRIGGER_NAME;
var PORTAL_NAME2 = "DialogPortal";
var [PortalProvider, usePortalContext] = createDialogContext(PORTAL_NAME2, {
  forceMount: void 0
});
var DialogPortal = (props) => {
  const { __scopeDialog, forceMount, children, container } = props;
  const context = useDialogContext(PORTAL_NAME2, __scopeDialog);
  return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(PortalProvider, { scope: __scopeDialog, forceMount, children: React37.Children.map(children, (child) => /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(Presence, { present: forceMount || context.open, children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(Portal, { asChild: true, container, children: child }) })) });
};
DialogPortal.displayName = PORTAL_NAME2;
var OVERLAY_NAME = "DialogOverlay";
var DialogOverlay = React37.forwardRef(
  (props, forwardedRef) => {
    const portalContext = usePortalContext(OVERLAY_NAME, props.__scopeDialog);
    const { forceMount = portalContext.forceMount, ...overlayProps } = props;
    const context = useDialogContext(OVERLAY_NAME, props.__scopeDialog);
    return context.modal ? /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(Presence, { present: forceMount || context.open, children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(DialogOverlayImpl, { ...overlayProps, ref: forwardedRef }) }) : null;
  }
);
DialogOverlay.displayName = OVERLAY_NAME;
var Slot = createSlot4("DialogOverlay.RemoveScroll");
var DialogOverlayImpl = React37.forwardRef(
  (props, forwardedRef) => {
    const { __scopeDialog, ...overlayProps } = props;
    const context = useDialogContext(OVERLAY_NAME, __scopeDialog);
    return (
      // Make sure `Content` is scrollable even when it doesn't live inside `RemoveScroll`
      // ie. when `Overlay` and `Content` are siblings
      /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(Combination_default, { as: Slot, allowPinchZoom: true, shards: [context.contentRef], children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
        Primitive4.div,
        {
          "data-state": getState(context.open),
          ...overlayProps,
          ref: forwardedRef,
          style: { pointerEvents: "auto", ...overlayProps.style }
        }
      ) })
    );
  }
);
var CONTENT_NAME = "DialogContent";
var DialogContent = React37.forwardRef(
  (props, forwardedRef) => {
    const portalContext = usePortalContext(CONTENT_NAME, props.__scopeDialog);
    const { forceMount = portalContext.forceMount, ...contentProps } = props;
    const context = useDialogContext(CONTENT_NAME, props.__scopeDialog);
    return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(Presence, { present: forceMount || context.open, children: context.modal ? /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(DialogContentModal, { ...contentProps, ref: forwardedRef }) : /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(DialogContentNonModal, { ...contentProps, ref: forwardedRef }) });
  }
);
DialogContent.displayName = CONTENT_NAME;
var DialogContentModal = React37.forwardRef(
  (props, forwardedRef) => {
    const context = useDialogContext(CONTENT_NAME, props.__scopeDialog);
    const contentRef = React37.useRef(null);
    const composedRefs = useComposedRefs(forwardedRef, context.contentRef, contentRef);
    React37.useEffect(() => {
      const content = contentRef.current;
      if (content) return hideOthers(content);
    }, []);
    return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
      DialogContentImpl,
      {
        ...props,
        ref: composedRefs,
        trapFocus: context.open,
        disableOutsidePointerEvents: true,
        onCloseAutoFocus: composeEventHandlers(props.onCloseAutoFocus, (event) => {
          event.preventDefault();
          context.triggerRef.current?.focus();
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
  }
);
var DialogContentNonModal = React37.forwardRef(
  (props, forwardedRef) => {
    const context = useDialogContext(CONTENT_NAME, props.__scopeDialog);
    const hasInteractedOutsideRef = React37.useRef(false);
    const hasPointerDownOutsideRef = React37.useRef(false);
    return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
      DialogContentImpl,
      {
        ...props,
        ref: forwardedRef,
        trapFocus: false,
        disableOutsidePointerEvents: false,
        onCloseAutoFocus: (event) => {
          props.onCloseAutoFocus?.(event);
          if (!event.defaultPrevented) {
            if (!hasInteractedOutsideRef.current) context.triggerRef.current?.focus();
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
          const targetIsTrigger = context.triggerRef.current?.contains(target);
          if (targetIsTrigger) event.preventDefault();
          if (event.detail.originalEvent.type === "focusin" && hasPointerDownOutsideRef.current) {
            event.preventDefault();
          }
        }
      }
    );
  }
);
var DialogContentImpl = React37.forwardRef(
  (props, forwardedRef) => {
    const { __scopeDialog, trapFocus, onOpenAutoFocus, onCloseAutoFocus, ...contentProps } = props;
    const context = useDialogContext(CONTENT_NAME, __scopeDialog);
    const contentRef = React37.useRef(null);
    const composedRefs = useComposedRefs(forwardedRef, contentRef);
    useFocusGuards();
    return /* @__PURE__ */ (0, import_jsx_runtime13.jsxs)(import_jsx_runtime13.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
        FocusScope,
        {
          asChild: true,
          loop: true,
          trapped: trapFocus,
          onMountAutoFocus: onOpenAutoFocus,
          onUnmountAutoFocus: onCloseAutoFocus,
          children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
            DismissableLayer,
            {
              role: "dialog",
              id: context.contentId,
              "aria-describedby": context.descriptionId,
              "aria-labelledby": context.titleId,
              "data-state": getState(context.open),
              ...contentProps,
              ref: composedRefs,
              onDismiss: () => context.onOpenChange(false)
            }
          )
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime13.jsxs)(import_jsx_runtime13.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(TitleWarning, { titleId: context.titleId }),
        /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(DescriptionWarning, { contentRef, descriptionId: context.descriptionId })
      ] })
    ] });
  }
);
var TITLE_NAME = "DialogTitle";
var DialogTitle = React37.forwardRef(
  (props, forwardedRef) => {
    const { __scopeDialog, ...titleProps } = props;
    const context = useDialogContext(TITLE_NAME, __scopeDialog);
    return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(Primitive4.h2, { id: context.titleId, ...titleProps, ref: forwardedRef });
  }
);
DialogTitle.displayName = TITLE_NAME;
var DESCRIPTION_NAME = "DialogDescription";
var DialogDescription = React37.forwardRef(
  (props, forwardedRef) => {
    const { __scopeDialog, ...descriptionProps } = props;
    const context = useDialogContext(DESCRIPTION_NAME, __scopeDialog);
    return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(Primitive4.p, { id: context.descriptionId, ...descriptionProps, ref: forwardedRef });
  }
);
DialogDescription.displayName = DESCRIPTION_NAME;
var CLOSE_NAME = "DialogClose";
var DialogClose = React37.forwardRef(
  (props, forwardedRef) => {
    const { __scopeDialog, ...closeProps } = props;
    const context = useDialogContext(CLOSE_NAME, __scopeDialog);
    return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
      Primitive4.button,
      {
        type: "button",
        ...closeProps,
        ref: forwardedRef,
        onClick: composeEventHandlers(props.onClick, () => context.onOpenChange(false))
      }
    );
  }
);
DialogClose.displayName = CLOSE_NAME;
function getState(open) {
  return open ? "open" : "closed";
}
var TITLE_WARNING_NAME = "DialogTitleWarning";
var [WarningProvider, useWarningContext] = createContext2(TITLE_WARNING_NAME, {
  contentName: CONTENT_NAME,
  titleName: TITLE_NAME,
  docsSlug: "dialog"
});
var TitleWarning = ({ titleId }) => {
  const titleWarningContext = useWarningContext(TITLE_WARNING_NAME);
  const MESSAGE = `\`${titleWarningContext.contentName}\` requires a \`${titleWarningContext.titleName}\` for the component to be accessible for screen reader users.

If you want to hide the \`${titleWarningContext.titleName}\`, you can wrap it with our VisuallyHidden component.

For more information, see https://radix-ui.com/primitives/docs/components/${titleWarningContext.docsSlug}`;
  React37.useEffect(() => {
    if (titleId) {
      const hasTitle = document.getElementById(titleId);
      if (!hasTitle) console.error(MESSAGE);
    }
  }, [MESSAGE, titleId]);
  return null;
};
var DESCRIPTION_WARNING_NAME = "DialogDescriptionWarning";
var DescriptionWarning = ({ contentRef, descriptionId }) => {
  const descriptionWarningContext = useWarningContext(DESCRIPTION_WARNING_NAME);
  const MESSAGE = `Warning: Missing \`Description\` or \`aria-describedby={undefined}\` for {${descriptionWarningContext.contentName}}.`;
  React37.useEffect(() => {
    const describedById = contentRef.current?.getAttribute("aria-describedby");
    if (descriptionId && describedById) {
      const hasDescription = document.getElementById(descriptionId);
      if (!hasDescription) console.warn(MESSAGE);
    }
  }, [MESSAGE, contentRef, descriptionId]);
  return null;
};
var Root = Dialog;
var Portal2 = DialogPortal;
var Overlay = DialogOverlay;
var Content = DialogContent;

// node_modules/cmdk/dist/index.mjs
var t = __toESM(require_react(), 1);

// node_modules/cmdk/node_modules/@radix-ui/react-primitive/dist/index.mjs
var React40 = __toESM(require_react(), 1);
var ReactDOM6 = __toESM(require_react_dom(), 1);

// node_modules/cmdk/node_modules/@radix-ui/react-slot/dist/index.mjs
var React39 = __toESM(require_react(), 1);

// node_modules/cmdk/node_modules/@radix-ui/react-compose-refs/dist/index.mjs
var React38 = __toESM(require_react(), 1);
function setRef6(ref, value) {
  if (typeof ref === "function") {
    return ref(value);
  } else if (ref !== null && ref !== void 0) {
    ref.current = value;
  }
}
function composeRefs6(...refs) {
  return (node) => {
    let hasCleanup = false;
    const cleanups = refs.map((ref) => {
      const cleanup = setRef6(ref, node);
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
            setRef6(refs[i], null);
          }
        }
      };
    }
  };
}

// node_modules/cmdk/node_modules/@radix-ui/react-slot/dist/index.mjs
var import_jsx_runtime14 = __toESM(require_jsx_runtime(), 1);
var REACT_LAZY_TYPE = Symbol.for("react.lazy");
var use = React39[" use ".trim().toString()];
function isPromiseLike(value) {
  return typeof value === "object" && value !== null && "then" in value;
}
function isLazyComponent(element) {
  return element != null && typeof element === "object" && "$$typeof" in element && element.$$typeof === REACT_LAZY_TYPE && "_payload" in element && isPromiseLike(element._payload);
}
// @__NO_SIDE_EFFECTS__
function createSlot5(ownerName) {
  const SlotClone = /* @__PURE__ */ createSlotClone5(ownerName);
  const Slot2 = React39.forwardRef((props, forwardedRef) => {
    let { children, ...slotProps } = props;
    if (isLazyComponent(children) && typeof use === "function") {
      children = use(children._payload);
    }
    const childrenArray = React39.Children.toArray(children);
    const slottable = childrenArray.find(isSlottable5);
    if (slottable) {
      const newElement = slottable.props.children;
      const newChildren = childrenArray.map((child) => {
        if (child === slottable) {
          if (React39.Children.count(newElement) > 1) return React39.Children.only(null);
          return React39.isValidElement(newElement) ? newElement.props.children : null;
        } else {
          return child;
        }
      });
      return /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(SlotClone, { ...slotProps, ref: forwardedRef, children: React39.isValidElement(newElement) ? React39.cloneElement(newElement, void 0, newChildren) : null });
    }
    return /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(SlotClone, { ...slotProps, ref: forwardedRef, children });
  });
  Slot2.displayName = `${ownerName}.Slot`;
  return Slot2;
}
// @__NO_SIDE_EFFECTS__
function createSlotClone5(ownerName) {
  const SlotClone = React39.forwardRef((props, forwardedRef) => {
    let { children, ...slotProps } = props;
    if (isLazyComponent(children) && typeof use === "function") {
      children = use(children._payload);
    }
    if (React39.isValidElement(children)) {
      const childrenRef = getElementRef6(children);
      const props2 = mergeProps5(slotProps, children.props);
      if (children.type !== React39.Fragment) {
        props2.ref = forwardedRef ? composeRefs6(forwardedRef, childrenRef) : childrenRef;
      }
      return React39.cloneElement(children, props2);
    }
    return React39.Children.count(children) > 1 ? React39.Children.only(null) : null;
  });
  SlotClone.displayName = `${ownerName}.SlotClone`;
  return SlotClone;
}
var SLOTTABLE_IDENTIFIER5 = Symbol("radix.slottable");
function isSlottable5(child) {
  return React39.isValidElement(child) && typeof child.type === "function" && "__radixId" in child.type && child.type.__radixId === SLOTTABLE_IDENTIFIER5;
}
function mergeProps5(slotProps, childProps) {
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
function getElementRef6(element) {
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

// node_modules/cmdk/node_modules/@radix-ui/react-primitive/dist/index.mjs
var import_jsx_runtime15 = __toESM(require_jsx_runtime(), 1);
var NODES5 = [
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
var Primitive5 = NODES5.reduce((primitive, node) => {
  const Slot2 = createSlot5(`Primitive.${node}`);
  const Node2 = React40.forwardRef((props, forwardedRef) => {
    const { asChild, ...primitiveProps } = props;
    const Comp = asChild ? Slot2 : node;
    if (typeof window !== "undefined") {
      window[Symbol.for("radix-ui")] = true;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(Comp, { ...primitiveProps, ref: forwardedRef });
  });
  Node2.displayName = `Primitive.${node}`;
  return { ...primitive, [node]: Node2 };
}, {});

// node_modules/cmdk/node_modules/@radix-ui/react-id/dist/index.mjs
var React42 = __toESM(require_react(), 1);

// node_modules/cmdk/node_modules/@radix-ui/react-use-layout-effect/dist/index.mjs
var React41 = __toESM(require_react(), 1);
var useLayoutEffect24 = globalThis?.document ? React41.useLayoutEffect : () => {
};

// node_modules/cmdk/node_modules/@radix-ui/react-id/dist/index.mjs
var useReactId2 = React42[" useId ".trim().toString()] || (() => void 0);
var count3 = 0;
function useId2(deterministicId) {
  const [id, setId] = React42.useState(useReactId2());
  useLayoutEffect24(() => {
    if (!deterministicId) setId((reactId) => reactId ?? String(count3++));
  }, [deterministicId]);
  return deterministicId || (id ? `radix-${id}` : "");
}

// node_modules/cmdk/dist/index.mjs
var N = '[cmdk-group=""]';
var Y2 = '[cmdk-group-items=""]';
var be = '[cmdk-group-heading=""]';
var le = '[cmdk-item=""]';
var ce = `${le}:not([aria-disabled="true"])`;
var Z = "cmdk-item-select";
var T = "data-value";
var Re = (r, o, n) => W(r, o, n);
var ue = t.createContext(void 0);
var K2 = () => t.useContext(ue);
var de = t.createContext(void 0);
var ee = () => t.useContext(de);
var fe = t.createContext(void 0);
var me = t.forwardRef((r, o) => {
  let n = L(() => {
    var e, a;
    return { search: "", value: (a = (e = r.value) != null ? e : r.defaultValue) != null ? a : "", selectedItemId: void 0, filtered: { count: 0, items: /* @__PURE__ */ new Map(), groups: /* @__PURE__ */ new Set() } };
  }), u2 = L(() => /* @__PURE__ */ new Set()), c = L(() => /* @__PURE__ */ new Map()), d = L(() => /* @__PURE__ */ new Map()), f = L(() => /* @__PURE__ */ new Set()), p2 = pe(r), { label: b, children: m2, value: R, onValueChange: x, filter: C, shouldFilter: S, loop: A, disablePointerSelection: ge = false, vimBindings: j = true, ...O } = r, $2 = useId2(), q = useId2(), _ = useId2(), I = t.useRef(null), v = ke();
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
  }), filter: () => p2.current.shouldFilter, label: b || r["aria-label"], getDisablePointerSelection: () => p2.current.disablePointerSelection, listId: $2, inputId: _, labelId: q, listInnerRef: I }), []);
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
  return t.createElement(Primitive5.div, { ref: o, tabIndex: -1, ...O, "cmdk-root": "", onKeyDown: (e) => {
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
  } }, t.createElement("label", { "cmdk-label": "", htmlFor: U2.inputId, id: U2.labelId, style: Te }, b), B2(r, (e) => t.createElement(de.Provider, { value: E }, t.createElement(ue.Provider, { value: U2 }, e))));
});
var he = t.forwardRef((r, o) => {
  var _, I;
  let n = useId2(), u2 = t.useRef(null), c = t.useContext(fe), d = K2(), f = pe(r), p2 = (I = (_ = f.current) == null ? void 0 : _.forceMount) != null ? I : c == null ? void 0 : c.forceMount;
  k2(() => {
    if (!p2) return d.item(n, c == null ? void 0 : c.id);
  }, [p2]);
  let b = ve(n, u2, [r.value, r.children, u2], r.keywords), m2 = ee(), R = P((v) => v.value && v.value === b.current), x = P((v) => p2 || d.filter() === false ? true : v.search ? v.filtered.items.get(n) > 0 : true);
  t.useEffect(() => {
    let v = u2.current;
    if (!(!v || r.disabled)) return v.addEventListener(Z, C), () => v.removeEventListener(Z, C);
  }, [x, r.onSelect, r.disabled]);
  function C() {
    var v, E;
    S(), (E = (v = f.current).onSelect) == null || E.call(v, b.current);
  }
  function S() {
    m2.setState("value", b.current, true);
  }
  if (!x) return null;
  let { disabled: A, value: ge, onSelect: j, forceMount: O, keywords: $2, ...q } = r;
  return t.createElement(Primitive5.div, { ref: composeRefs6(u2, o), ...q, id: n, "cmdk-item": "", role: "option", "aria-disabled": !!A, "aria-selected": !!R, "data-disabled": !!A, "data-selected": !!R, onPointerMove: A || d.getDisablePointerSelection() ? void 0 : S, onClick: A ? void 0 : C }, r.children);
});
var Ee = t.forwardRef((r, o) => {
  let { heading: n, children: u2, forceMount: c, ...d } = r, f = useId2(), p2 = t.useRef(null), b = t.useRef(null), m2 = useId2(), R = K2(), x = P((S) => c || R.filter() === false ? true : S.search ? S.filtered.groups.has(f) : true);
  k2(() => R.group(f), []), ve(f, p2, [r.value, r.heading, b]);
  let C = t.useMemo(() => ({ id: f, forceMount: c }), [c]);
  return t.createElement(Primitive5.div, { ref: composeRefs6(p2, o), ...d, "cmdk-group": "", role: "presentation", hidden: x ? void 0 : true }, n && t.createElement("div", { ref: b, "cmdk-group-heading": "", "aria-hidden": true, id: m2 }, n), B2(r, (S) => t.createElement("div", { "cmdk-group-items": "", role: "group", "aria-labelledby": n ? m2 : void 0 }, t.createElement(fe.Provider, { value: C }, S))));
});
var ye = t.forwardRef((r, o) => {
  let { alwaysRender: n, ...u2 } = r, c = t.useRef(null), d = P((f) => !f.search);
  return !n && !d ? null : t.createElement(Primitive5.div, { ref: composeRefs6(c, o), ...u2, "cmdk-separator": "", role: "separator" });
});
var Se = t.forwardRef((r, o) => {
  let { onValueChange: n, ...u2 } = r, c = r.value != null, d = ee(), f = P((m2) => m2.search), p2 = P((m2) => m2.selectedItemId), b = K2();
  return t.useEffect(() => {
    r.value != null && d.setState("search", r.value);
  }, [r.value]), t.createElement(Primitive5.input, { ref: o, ...u2, "cmdk-input": "", autoComplete: "off", autoCorrect: "off", spellCheck: false, "aria-autocomplete": "list", role: "combobox", "aria-expanded": true, "aria-controls": b.listId, "aria-labelledby": b.labelId, "aria-activedescendant": p2, id: b.inputId, type: "text", value: c ? r.value : f, onChange: (m2) => {
    c || d.setState("search", m2.target.value), n == null || n(m2.target.value);
  } });
});
var Ce = t.forwardRef((r, o) => {
  let { children: n, label: u2 = "Suggestions", ...c } = r, d = t.useRef(null), f = t.useRef(null), p2 = P((m2) => m2.selectedItemId), b = K2();
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
  }, []), t.createElement(Primitive5.div, { ref: composeRefs6(d, o), ...c, "cmdk-list": "", role: "listbox", tabIndex: -1, "aria-activedescendant": p2, "aria-label": u2, id: b.listId }, B2(r, (m2) => t.createElement("div", { ref: composeRefs6(f, b.listInnerRef), "cmdk-list-sizer": "" }, m2)));
});
var xe = t.forwardRef((r, o) => {
  let { open: n, onOpenChange: u2, overlayClassName: c, contentClassName: d, container: f, ...p2 } = r;
  return t.createElement(Root, { open: n, onOpenChange: u2 }, t.createElement(Portal2, { container: f }, t.createElement(Overlay, { "cmdk-overlay": "", className: c }), t.createElement(Content, { "aria-label": r.label, "cmdk-dialog": "", className: d }, t.createElement(me, { ref: o, ...p2 }))));
});
var Ie = t.forwardRef((r, o) => P((u2) => u2.filtered.count === 0) ? t.createElement(Primitive5.div, { ref: o, ...r, "cmdk-empty": "", role: "presentation" }) : null);
var Pe = t.forwardRef((r, o) => {
  let { progress: n, children: u2, label: c = "Loading...", ...d } = r;
  return t.createElement(Primitive5.div, { ref: o, ...d, "cmdk-loading": "", role: "progressbar", "aria-valuenow": n, "aria-valuemin": 0, "aria-valuemax": 100, "aria-label": c }, B2(r, (f) => t.createElement("div", { "aria-hidden": true }, f)));
});
var _e = Object.assign(me, { List: Ce, Item: he, Input: Se, Group: Ee, Separator: ye, Dialog: xe, Empty: Ie, Loading: Pe });
function we(r, o) {
  let n = r.nextElementSibling;
  for (; n; ) {
    if (n.matches(o)) return n;
    n = n.nextElementSibling;
  }
}
function De(r, o) {
  let n = r.previousElementSibling;
  for (; n; ) {
    if (n.matches(o)) return n;
    n = n.previousElementSibling;
  }
}
function pe(r) {
  let o = t.useRef(r);
  return k2(() => {
    o.current = r;
  }), o;
}
var k2 = typeof window == "undefined" ? t.useEffect : t.useLayoutEffect;
function L(r) {
  let o = t.useRef();
  return o.current === void 0 && (o.current = r()), o;
}
function P(r) {
  let o = ee(), n = () => r(o.snapshot());
  return t.useSyncExternalStore(o.subscribe, n, n);
}
function ve(r, o, n, u2 = []) {
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
    d.value(r, f, p2), (b = o.current) == null || b.setAttribute(T, f), c.current = f;
  }), c;
}
var ke = () => {
  let [r, o] = t.useState(), n = L(() => /* @__PURE__ */ new Map());
  return k2(() => {
    n.current.forEach((u2) => u2()), n.current = /* @__PURE__ */ new Map();
  }, [r]), (u2, c) => {
    n.current.set(u2, c), o({});
  };
};
function Me(r) {
  let o = r.type;
  return typeof o == "function" ? o(r.props) : "render" in o ? o.render(r.props) : r;
}
function B2({ asChild: r, children: o }, n) {
  return r && t.isValidElement(o) ? t.cloneElement(Me(o), { ref: o.ref }, n(o.props.children)) : n(o);
}
var Te = { position: "absolute", width: "1px", height: "1px", padding: "0", margin: "-1px", overflow: "hidden", clip: "rect(0, 0, 0, 0)", whiteSpace: "nowrap", borderWidth: "0" };

// packages/workflow/build-module/components/workflow-menu.js
var import_data = __toESM(require_data());
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
var import_jsx_runtime16 = __toESM(require_jsx_runtime());
var search_default = /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_primitives.Path, { d: "M13 5c-3.3 0-6 2.7-6 6 0 1.4.5 2.7 1.3 3.7l-3.8 3.8 1.1 1.1 3.8-3.8c1 .8 2.3 1.3 3.7 1.3 3.3 0 6-2.7 6-6S16.3 5 13 5zm0 10.5c-2.5 0-4.5-2-4.5-4.5s2-4.5 4.5-4.5 4.5 2 4.5 4.5-2 4.5-4.5 4.5z" }) });

// packages/workflow/build-module/components/workflow-menu.js
import { executeAbility, store as abilitiesStore } from "@wordpress/abilities";

// packages/workflow/build-module/lock-unlock.js
var import_private_apis = __toESM(require_private_apis());
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/workflows"
);

// packages/workflow/build-module/components/workflow-menu.js
var import_jsx_runtime17 = __toESM(require_jsx_runtime());
var css = `/**
 * Typography
 */
/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
/**
 * Breakpoints & Media Queries
 */
/**
*  Converts a hex value into the rgb equivalent.
*
* @param {string} hex - the hexadecimal value to convert
* @return {string} comma separated rgb values
*/
/**
 * Long content fade mixin
 *
 * Creates a fading overlay to signify that the content is longer
 * than the space allows.
 */
/**
 * Breakpoint mixins
 */
/**
 * Focus styles.
 */
/**
 * Applies editor left position to the selector passed as argument
 */
/**
 * Styles that are reused verbatim in a few places
 */
/**
 * Allows users to opt-out of animations via OS-level preferences.
 */
/**
 * Reset default styles for JavaScript UI based pages.
 * This is a WP-admin agnostic reset
 */
/**
 * Reset the WP Admin page styles for Gutenberg-like pages.
 */
:root {
  --wp-block-synced-color: #7a00df;
  --wp-block-synced-color--rgb: 122, 0, 223;
  --wp-bound-block-color: var(--wp-block-synced-color);
  --wp-editor-canvas-background: #ddd;
  --wp-admin-theme-color: #007cba;
  --wp-admin-theme-color--rgb: 0, 124, 186;
  --wp-admin-theme-color-darker-10: rgb(0, 107, 160.5);
  --wp-admin-theme-color-darker-10--rgb: 0, 107, 160.5;
  --wp-admin-theme-color-darker-20: #005a87;
  --wp-admin-theme-color-darker-20--rgb: 0, 90, 135;
  --wp-admin-border-width-focus: 2px;
}
@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  :root {
    --wp-admin-border-width-focus: 1.5px;
  }
}

.workflows-workflow-menu {
  border-radius: 4px;
  width: calc(100% - 32px);
  margin: auto;
  max-width: 400px;
  position: relative;
  top: calc(5% + 64px);
}
@media (min-width: 600px) {
  .workflows-workflow-menu {
    top: calc(10% + 64px);
  }
}
.workflows-workflow-menu .components-modal__content {
  margin: 0;
  padding: 0;
}

.workflows-workflow-menu__overlay {
  display: block;
  align-items: start;
}

.workflows-workflow-menu__header {
  padding: 0 16px;
}

.workflows-workflow-menu__header-search-icon:dir(ltr) {
  transform: scaleX(-1);
}

.workflows-workflow-menu__container {
  will-change: transform;
}
.workflows-workflow-menu__container:focus {
  outline: none;
}
.workflows-workflow-menu__container [cmdk-input] {
  border: none;
  width: 100%;
  padding: 16px 4px;
  outline: none;
  color: #1e1e1e;
  margin: 0;
  font-size: 15px;
  line-height: 28px;
  border-radius: 0;
}
.workflows-workflow-menu__container [cmdk-input]::placeholder {
  color: #757575;
}
.workflows-workflow-menu__container [cmdk-input]:focus {
  box-shadow: none;
  outline: none;
}
.workflows-workflow-menu__container [cmdk-item] {
  border-radius: 2px;
  cursor: pointer;
  display: flex;
  align-items: center;
  color: #1e1e1e;
  font-size: 13px;
}
.workflows-workflow-menu__container [cmdk-item][aria-selected=true], .workflows-workflow-menu__container [cmdk-item]:active {
  background: var(--wp-admin-theme-color);
  color: #fff;
}
.workflows-workflow-menu__container [cmdk-item][aria-disabled=true] {
  color: #949494;
  cursor: not-allowed;
}
.workflows-workflow-menu__container [cmdk-item] > div {
  min-height: 40px;
  padding: 4px;
  padding-left: 16px;
}
.workflows-workflow-menu__container [cmdk-root] > [cmdk-list] {
  max-height: 368px;
  overflow: auto;
}
.workflows-workflow-menu__container [cmdk-root] > [cmdk-list] [cmdk-list-sizer] > [cmdk-group]:last-child [cmdk-group-items]:not(:empty) {
  padding-bottom: 8px;
}
.workflows-workflow-menu__container [cmdk-root] > [cmdk-list] [cmdk-list-sizer] > [cmdk-group] > [cmdk-group-items]:not(:empty) {
  padding: 0 8px;
}
.workflows-workflow-menu__container [cmdk-empty] {
  display: flex;
  align-items: center;
  justify-content: center;
  white-space: pre-wrap;
  color: #1e1e1e;
  padding: 8px 0 32px;
}
.workflows-workflow-menu__container [cmdk-loading] {
  padding: 16px;
}
.workflows-workflow-menu__container [cmdk-list-sizer] {
  position: relative;
}

.workflows-workflow-menu__item span {
  display: inline-block;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.workflows-workflow-menu__item mark {
  color: inherit;
  background: unset;
  font-weight: 600;
}

.workflows-workflow-menu__output {
  padding: 16px;
}

.workflows-workflow-menu__output-header {
  margin-bottom: 16px;
  border-bottom: 1px solid #ddd;
  padding-bottom: 8px;
}
.workflows-workflow-menu__output-header h3 {
  margin: 0 0 4px;
  font-size: 16px;
  font-weight: 600;
  color: #1e1e1e;
}

.workflows-workflow-menu__output-hint {
  margin: 0;
  font-size: 12px;
  color: #757575;
}

.workflows-workflow-menu__output-content {
  max-height: 400px;
  overflow: auto;
}
.workflows-workflow-menu__output-content pre {
  margin: 0;
  padding: 12px;
  background: #f0f0f0;
  border-radius: 2px;
  font-size: 12px;
  line-height: 1.5;
  white-space: pre-wrap;
  word-break: break-word;
  color: #1e1e1e;
}

.workflows-workflow-menu__output-error {
  padding: 12px;
  background: #e0e0e0;
  border: 1px solid rgb(158.3684210526, 18.6315789474, 18.6315789474);
  border-radius: 2px;
  color: #cc1818;
}
.workflows-workflow-menu__output-error p {
  margin: 0;
  font-size: 13px;
}

.workflows-workflow-menu__executing {
  padding: 24px 16px;
  color: #757575;
  font-size: 14px;
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy93b3JrZmxvdy9zcmMvY29tcG9uZW50cyIsInNvdXJjZXMiOlsiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX21peGlucy5zY3NzIiwiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX3ZhcmlhYmxlcy5zY3NzIiwiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX2NvbG9ycy5zY3NzIiwiLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX2JyZWFrcG9pbnRzLnNjc3MiLCIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9iYXNlLXN0eWxlcy9fZnVuY3Rpb25zLnNjc3MiLCIuLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9iYXNlLXN0eWxlcy9fbG9uZy1jb250ZW50LWZhZGUuc2NzcyIsIi4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19kZWZhdWx0LWN1c3RvbS1wcm9wZXJ0aWVzLnNjc3MiLCJ3b3JrZmxvdy1tZW51LnNjc3MiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQTtBQUFBO0FDQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUNBQTtBQUFBO0FBQUE7QURVQTtBQUFBO0FBQUE7QUFPQTtBQUFBO0FBQUE7QUE2QkE7QUFBQTtBQUFBO0FBQUE7QUFpQkE7QUFBQTtBQUFBO0FBV0E7QUFBQTtBQUFBO0FBZ0JBO0FBQUE7QUFBQTtBQXlCQTtBQUFBO0FBQUE7QUFLQTtBQUFBO0FBQUE7QUFlQTtBQUFBO0FBQUE7QUFtQkE7QUFBQTtBQUFBO0FBU0E7QUFBQTtBQUFBO0FBQUE7QUVuS0E7QUFBQTtBQUFBO0FDQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FDQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FMNEVBO0FBQUE7QUFBQTtBQTBEQTtBQUFBO0FBQUE7QUFnREE7QUFBQTtBQUFBO0FBcUNBO0FBQUE7QUFBQTtBQW9CQTtBQUFBO0FBQUE7QUF3TEE7QUFBQTtBQUFBO0FBQUE7QUFnREE7QUFBQTtBQUFBO0FNaGRBO0VBQ0M7RUFDQTtFQUdBO0VBQ0E7RU5zZkE7RUFDQTtFQUVBO0VBQ0E7RUFDQTtFQUNBO0VBSUE7O0FBQ0E7RU12Z0JEO0lOd2dCRTs7OztBT3hnQkY7RUFDQyxlTjRDYztFTTNDZDtFQUNBO0VBQ0E7RUFDQTtFQUNBOztBUHdHQTtFTzlHRDtJQVNFOzs7QUFHRDtFQUNDO0VBQ0E7OztBQUlGO0VBQ0M7RUFDQTs7O0FBR0Q7RUFDQzs7O0FBSUE7RUFDQzs7O0FBSUY7RUFFQzs7QUFFQTtFQUNDOztBQUdEO0VBQ0M7RUFDQTtFQUNBO0VBQ0E7RUFDQSxPTC9DUztFS2dEVDtFQUNBO0VBQ0E7RUFDQTs7QUFFQTtFQUNDLE9McERROztBS3VEVDtFQUNDO0VBQ0E7O0FBSUY7RUFDQyxlTkZhO0VNR2I7RUFDQTtFQUNBO0VBQ0EsT0xwRVM7RUtxRVQsV05uRGlCOztBTXFEakI7RUFFQztFQUNBLE9MbEVLOztBS3FFTjtFQUNDLE9MM0VRO0VLNEVSOztBQUdEO0VBQ0MsWU5PNkI7RU1ON0IsU050Q1k7RU11Q1osY05wQ1k7O0FNd0NkO0VBQ0MsWU5pQm1CO0VNaEJuQjs7QUFHQTtFQUdDLGdCTmxEWTs7QU1xRGI7RUFDQzs7QUFJRjtFQUNDO0VBQ0E7RUFDQTtFQUNBO0VBQ0EsT0w5R1M7RUsrR1Q7O0FBR0Q7RUFDQyxTTmxFYTs7QU1xRWQ7RUFDQzs7O0FBSUY7RUFFQztFQUNBO0VBQ0E7RUFDQTs7O0FBR0Q7RUFDQztFQUNBO0VBQ0E7OztBQUdEO0VBQ0MsU056RmM7OztBTTRGZjtFQUNDLGVON0ZjO0VNOEZkO0VBQ0EsZ0JOakdjOztBTW1HZDtFQUNDO0VBQ0E7RUFDQTtFQUNBLE9MdEpTOzs7QUswSlg7RUFDQztFQUNBO0VBQ0EsT0wzSlU7OztBSzhKWDtFQUNDO0VBQ0E7O0FBRUE7RUFDQztFQUNBLFNOdEhhO0VNdUhiLFlMaEtTO0VLaUtULGVOMUdhO0VNMkdiO0VBQ0E7RUFDQTtFQUNBO0VBQ0EsT0w3S1M7OztBS2lMWDtFQUNDLFNObEljO0VNbUlkLFlMN0tVO0VLOEtWO0VBQ0EsZU52SGM7RU13SGQsT0xyS1c7O0FLdUtYO0VBQ0M7RUFDQTs7O0FBSUY7RUFDQztFQUNBLE9MOUxVO0VLK0xWIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBUeXBvZ3JhcGh5XG4gKi9cblxuQHVzZSBcInNhc3M6Y29sb3JcIjtcbkB1c2UgXCJzYXNzOm1hdGhcIjtcbkB1c2UgXCIuL3ZhcmlhYmxlc1wiO1xuQHVzZSBcIi4vY29sb3JzXCI7XG5AdXNlIFwiLi9icmVha3BvaW50c1wiO1xuQHVzZSBcIi4vZnVuY3Rpb25zXCI7XG5AdXNlIFwiLi9sb25nLWNvbnRlbnQtZmFkZVwiO1xuXG5AbWl4aW4gX3RleHQtaGVhZGluZygpIHtcblx0Zm9udC1mYW1pbHk6IHZhcmlhYmxlcy4kZm9udC1mYW1pbHktaGVhZGluZ3M7XG5cdGZvbnQtd2VpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtd2VpZ2h0LW1lZGl1bTtcbn1cblxuQG1peGluIF90ZXh0LWJvZHkoKSB7XG5cdGZvbnQtZmFtaWx5OiB2YXJpYWJsZXMuJGZvbnQtZmFtaWx5LWJvZHk7XG5cdGZvbnQtd2VpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtd2VpZ2h0LXJlZ3VsYXI7XG59XG5cbkBtaXhpbiBoZWFkaW5nLXNtYWxsKCkge1xuXHRAaW5jbHVkZSBfdGV4dC1oZWFkaW5nKCk7XG5cdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRmb250LXNpemUteC1zbWFsbDtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC14LXNtYWxsO1xufVxuXG5AbWl4aW4gaGVhZGluZy1tZWRpdW0oKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWhlYWRpbmcoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS1tZWRpdW07XG5cdGxpbmUtaGVpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtbGluZS1oZWlnaHQtc21hbGw7XG59XG5cbkBtaXhpbiBoZWFkaW5nLWxhcmdlKCkge1xuXHRAaW5jbHVkZSBfdGV4dC1oZWFkaW5nKCk7XG5cdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRmb250LXNpemUtbGFyZ2U7XG5cdGxpbmUtaGVpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtbGluZS1oZWlnaHQtc21hbGw7XG59XG5cbkBtaXhpbiBoZWFkaW5nLXgtbGFyZ2UoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWhlYWRpbmcoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS14LWxhcmdlO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LW1lZGl1bTtcbn1cblxuQG1peGluIGhlYWRpbmctMngtbGFyZ2UoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWhlYWRpbmcoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS0yeC1sYXJnZTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC0yeC1sYXJnZTtcbn1cblxuQG1peGluIGJvZHktc21hbGwoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWJvZHkoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS1zbWFsbDtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC14LXNtYWxsO1xufVxuXG5AbWl4aW4gYm9keS1tZWRpdW0oKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWJvZHkoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS1tZWRpdW07XG5cdGxpbmUtaGVpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtbGluZS1oZWlnaHQtc21hbGw7XG59XG5cbkBtaXhpbiBib2R5LWxhcmdlKCkge1xuXHRAaW5jbHVkZSBfdGV4dC1ib2R5KCk7XG5cdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRmb250LXNpemUtbGFyZ2U7XG5cdGxpbmUtaGVpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtbGluZS1oZWlnaHQtbWVkaXVtO1xufVxuXG5AbWl4aW4gYm9keS14LWxhcmdlKCkge1xuXHRAaW5jbHVkZSBfdGV4dC1ib2R5KCk7XG5cdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRmb250LXNpemUteC1sYXJnZTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC14LWxhcmdlO1xufVxuXG4vKipcbiAqIEJyZWFrcG9pbnQgbWl4aW5zXG4gKi9cblxuQG1peGluIGJyZWFrLXhodWdlKCkge1xuXHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay14aHVnZSkgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbkBtaXhpbiBicmVhay1odWdlKCkge1xuXHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay1odWdlKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLXdpZGUoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLXdpZGUpIH0pIHtcblx0XHRAY29udGVudDtcblx0fVxufVxuXG5AbWl4aW4gYnJlYWsteGxhcmdlKCkge1xuXHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay14bGFyZ2UpIH0pIHtcblx0XHRAY29udGVudDtcblx0fVxufVxuXG5AbWl4aW4gYnJlYWstbGFyZ2UoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLWxhcmdlKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLW1lZGl1bSgpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbWVkaXVtKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLXNtYWxsKCkge1xuXHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay1zbWFsbCkgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbkBtaXhpbiBicmVhay1tb2JpbGUoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLW1vYmlsZSkgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbkBtaXhpbiBicmVhay16b29tZWQtaW4oKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLXpvb21lZC1pbikgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbi8qKlxuICogRm9jdXMgc3R5bGVzLlxuICovXG5cbkBtaXhpbiBibG9jay10b29sYmFyLWJ1dHRvbi1zdHlsZV9fZm9jdXMoKSB7XG5cdGJveC1zaGFkb3c6IGluc2V0IDAgMCAwIHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoIGNvbG9ycy4kd2hpdGUsIDAgMCAwIHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXG5cdC8vIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlIHdpbGwgc2hvdyB0aGlzIG91dGxpbmUsIGJ1dCBub3QgdGhlIGJveC1zaGFkb3cuXG5cdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcbn1cblxuLy8gVGFicywgSW5wdXRzLCBTcXVhcmUgYnV0dG9ucy5cbkBtaXhpbiBpbnB1dC1zdHlsZV9fbmV1dHJhbCgpIHtcblx0Ym94LXNoYWRvdzogMCAwIDAgdHJhbnNwYXJlbnQ7XG5cdGJvcmRlci1yYWRpdXM6IHZhcmlhYmxlcy4kcmFkaXVzLXNtYWxsO1xuXHRib3JkZXI6IHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoIHNvbGlkIGNvbG9ycy4kZ3JheS02MDA7XG5cblx0QG1lZGlhIG5vdCAocHJlZmVycy1yZWR1Y2VkLW1vdGlvbikge1xuXHRcdHRyYW5zaXRpb246IGJveC1zaGFkb3cgMC4xcyBsaW5lYXI7XG5cdH1cbn1cblxuXG5AbWl4aW4gaW5wdXQtc3R5bGVfX2ZvY3VzKCRhY2NlbnQtY29sb3I6IHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKSkge1xuXHRib3JkZXItY29sb3I6ICRhY2NlbnQtY29sb3I7XG5cdC8vIEV4cGFuZCB0aGUgZGVmYXVsdCBib3JkZXIgZm9jdXMgc3R5bGUgYnkgLjVweCB0byBiZSBhIHRvdGFsIG9mIDEuNXB4LlxuXHRib3gtc2hhZG93OiAwIDAgMCAwLjVweCAkYWNjZW50LWNvbG9yO1xuXHQvLyBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZSB3aWxsIHNob3cgdGhpcyBvdXRsaW5lLCBidXQgbm90IHRoZSBib3gtc2hhZG93LlxuXHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQ7XG59XG5cbkBtaXhpbiBidXR0b24tc3R5bGVfX2ZvY3VzKCkge1xuXHRib3gtc2hhZG93OiAwIDAgMCB2YXIoLS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXMpIHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblxuXHQvLyBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZSB3aWxsIHNob3cgdGhpcyBvdXRsaW5lLCBidXQgbm90IHRoZSBib3gtc2hhZG93LlxuXHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQ7XG59XG5cblxuQG1peGluIGJ1dHRvbi1zdHlsZS1vdXRzZXRfX2ZvY3VzKCRmb2N1cy1jb2xvcikge1xuXHRib3gtc2hhZG93OiAwIDAgMCB2YXIoLS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXMpIGNvbG9ycy4kd2hpdGUsIDAgMCAwIGNhbGMoMiAqIHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykpICRmb2N1cy1jb2xvcjtcblxuXHQvLyBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZSB3aWxsIHNob3cgdGhpcyBvdXRsaW5lLCBidXQgbm90IHRoZSBib3gtc2hhZG93LlxuXHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQ7XG5cdG91dGxpbmUtb2Zmc2V0OiAycHg7XG59XG5cblxuLyoqXG4gKiBBcHBsaWVzIGVkaXRvciBsZWZ0IHBvc2l0aW9uIHRvIHRoZSBzZWxlY3RvciBwYXNzZWQgYXMgYXJndW1lbnRcbiAqL1xuXG5AbWl4aW4gZWRpdG9yLWxlZnQoJHNlbGVjdG9yKSB7XG5cdCN7JHNlbGVjdG9yfSB7IC8qIFNldCBsZWZ0IHBvc2l0aW9uIHdoZW4gYXV0by1mb2xkIGlzIG5vdCBvbiB0aGUgYm9keSBlbGVtZW50LiAqL1xuXHRcdGxlZnQ6IDA7XG5cblx0XHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay1tZWRpdW0gKyAxKSB9KSB7XG5cdFx0XHRsZWZ0OiB2YXJpYWJsZXMuJGFkbWluLXNpZGViYXItd2lkdGg7XG5cdFx0fVxuXHR9XG5cblx0LmF1dG8tZm9sZCAjeyRzZWxlY3Rvcn0geyAvKiBBdXRvIGZvbGQgaXMgd2hlbiBvbiBzbWFsbGVyIGJyZWFrcG9pbnRzLCBuYXYgbWVudSBhdXRvIGNvbGxhcHNlcy4gKi9cblx0XHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay1tZWRpdW0gKyAxKSB9KSB7XG5cdFx0XHRsZWZ0OiB2YXJpYWJsZXMuJGFkbWluLXNpZGViYXItd2lkdGgtY29sbGFwc2VkO1xuXHRcdH1cblxuXHRcdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLWxhcmdlICsgMSkgfSkge1xuXHRcdFx0bGVmdDogdmFyaWFibGVzLiRhZG1pbi1zaWRlYmFyLXdpZHRoO1xuXHRcdH1cblx0fVxuXG5cdC8qIFNpZGViYXIgbWFudWFsbHkgY29sbGFwc2VkLiAqL1xuXHQuZm9sZGVkICN7JHNlbGVjdG9yfSB7XG5cdFx0bGVmdDogMDtcblxuXHRcdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLW1lZGl1bSArIDEpIH0pIHtcblx0XHRcdGxlZnQ6IHZhcmlhYmxlcy4kYWRtaW4tc2lkZWJhci13aWR0aC1jb2xsYXBzZWQ7XG5cdFx0fVxuXHR9XG5cblx0Ym9keS5pcy1mdWxsc2NyZWVuLW1vZGUgI3skc2VsZWN0b3J9IHtcblx0XHRsZWZ0OiAwICFpbXBvcnRhbnQ7XG5cdH1cbn1cblxuLyoqXG4gKiBTdHlsZXMgdGhhdCBhcmUgcmV1c2VkIHZlcmJhdGltIGluIGEgZmV3IHBsYWNlc1xuICovXG5cbi8vIFRoZXNlIGFyZSBhZGRpdGlvbmFsIHN0eWxlcyBmb3IgYWxsIGNhcHRpb25zLCB3aGVuIHRoZSB0aGVtZSBvcHRzIGluIHRvIGJsb2NrIHN0eWxlcy5cbkBtaXhpbiBjYXB0aW9uLXN0eWxlKCkge1xuXHRtYXJnaW4tdG9wOiAwLjVlbTtcblx0bWFyZ2luLWJvdHRvbTogMWVtO1xufVxuXG5AbWl4aW4gY2FwdGlvbi1zdHlsZS10aGVtZSgpIHtcblx0Y29sb3I6ICM1NTU7XG5cdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRkZWZhdWx0LWZvbnQtc2l6ZTtcblx0dGV4dC1hbGlnbjogY2VudGVyO1xuXG5cdC5pcy1kYXJrLXRoZW1lICYge1xuXHRcdGNvbG9yOiBjb2xvcnMuJGxpZ2h0LWdyYXktcGxhY2Vob2xkZXI7XG5cdH1cbn1cblxuLyoqXG4gKiBBbGxvd3MgdXNlcnMgdG8gb3B0LW91dCBvZiBhbmltYXRpb25zIHZpYSBPUy1sZXZlbCBwcmVmZXJlbmNlcy5cbiAqL1xuXG5AbWl4aW4gcmVkdWNlLW1vdGlvbigkcHJvcGVydHk6IFwiXCIpIHtcblxuXHRAaWYgJHByb3BlcnR5ID09IFwidHJhbnNpdGlvblwiIHtcblx0XHRAbWVkaWEgKHByZWZlcnMtcmVkdWNlZC1tb3Rpb246IHJlZHVjZSkge1xuXHRcdFx0dHJhbnNpdGlvbi1kdXJhdGlvbjogMHM7XG5cdFx0XHR0cmFuc2l0aW9uLWRlbGF5OiAwcztcblx0XHR9XG5cdH0gQGVsc2UgaWYgJHByb3BlcnR5ID09IFwiYW5pbWF0aW9uXCIge1xuXHRcdEBtZWRpYSAocHJlZmVycy1yZWR1Y2VkLW1vdGlvbjogcmVkdWNlKSB7XG5cdFx0XHRhbmltYXRpb24tZHVyYXRpb246IDFtcztcblx0XHRcdGFuaW1hdGlvbi1kZWxheTogMHM7XG5cdFx0fVxuXHR9IEBlbHNlIHtcblx0XHRAbWVkaWEgKHByZWZlcnMtcmVkdWNlZC1tb3Rpb246IHJlZHVjZSkge1xuXHRcdFx0dHJhbnNpdGlvbi1kdXJhdGlvbjogMHM7XG5cdFx0XHR0cmFuc2l0aW9uLWRlbGF5OiAwcztcblx0XHRcdGFuaW1hdGlvbi1kdXJhdGlvbjogMW1zO1xuXHRcdFx0YW5pbWF0aW9uLWRlbGF5OiAwcztcblx0XHR9XG5cdH1cbn1cblxuQG1peGluIGlucHV0LWNvbnRyb2woJGFjY2VudC1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpKSB7XG5cdGZvbnQtZmFtaWx5OiB2YXJpYWJsZXMuJGRlZmF1bHQtZm9udDtcblx0cGFkZGluZzogNnB4IDhweDtcblx0LyogRm9udHMgc21hbGxlciB0aGFuIDE2cHggY2F1c2VzIG1vYmlsZSBzYWZhcmkgdG8gem9vbS4gKi9cblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJG1vYmlsZS10ZXh0LW1pbi1mb250LXNpemU7XG5cdC8qIE92ZXJyaWRlIGNvcmUgbGluZS1oZWlnaHQuIFRvIGJlIHJldmlld2VkLiAqL1xuXHRsaW5lLWhlaWdodDogbm9ybWFsO1xuXHRAaW5jbHVkZSBpbnB1dC1zdHlsZV9fbmV1dHJhbCgpO1xuXG5cdEBpbmNsdWRlIGJyZWFrLXNtYWxsIHtcblx0XHRmb250LXNpemU6IHZhcmlhYmxlcy4kZGVmYXVsdC1mb250LXNpemU7XG5cdFx0LyogT3ZlcnJpZGUgY29yZSBsaW5lLWhlaWdodC4gVG8gYmUgcmV2aWV3ZWQuICovXG5cdFx0bGluZS1oZWlnaHQ6IG5vcm1hbDtcblx0fVxuXG5cdCY6Zm9jdXMge1xuXHRcdEBpbmNsdWRlIGlucHV0LXN0eWxlX19mb2N1cygkYWNjZW50LWNvbG9yKTtcblx0fVxuXG5cdC8vIFVzZSBvcGFjaXR5IHRvIHdvcmsgaW4gdmFyaW91cyBlZGl0b3Igc3R5bGVzLlxuXHQmOjotd2Via2l0LWlucHV0LXBsYWNlaG9sZGVyIHtcblx0XHRjb2xvcjogY29sb3JzLiRkYXJrLWdyYXktcGxhY2Vob2xkZXI7XG5cdH1cblxuXHQmOjotbW96LXBsYWNlaG9sZGVyIHtcblx0XHRjb2xvcjogY29sb3JzLiRkYXJrLWdyYXktcGxhY2Vob2xkZXI7XG5cdH1cblxuXHQmOi1tcy1pbnB1dC1wbGFjZWhvbGRlciB7XG5cdFx0Y29sb3I6IGNvbG9ycy4kZGFyay1ncmF5LXBsYWNlaG9sZGVyO1xuXHR9XG59XG5cbkBtaXhpbiBjaGVja2JveC1jb250cm9sIHtcblx0Ym9yZGVyOiB2YXJpYWJsZXMuJGJvcmRlci13aWR0aCBzb2xpZCBjb2xvcnMuJGdyYXktOTAwO1xuXHRtYXJnaW4tcmlnaHQ6IHZhcmlhYmxlcy4kZ3JpZC11bml0LTE1O1xuXHR0cmFuc2l0aW9uOiBub25lO1xuXHRib3JkZXItcmFkaXVzOiB2YXJpYWJsZXMuJHJhZGl1cy1zbWFsbDtcblx0QGluY2x1ZGUgaW5wdXQtY29udHJvbDtcblxuXHQmOmZvY3VzIHtcblx0XHRib3gtc2hhZG93OiAwIDAgMCAodmFyaWFibGVzLiRib3JkZXItd2lkdGggKiAyKSBjb2xvcnMuJHdoaXRlLCAwIDAgMCAodmFyaWFibGVzLiRib3JkZXItd2lkdGggKiAyICsgdmFyaWFibGVzLiRib3JkZXItd2lkdGgtZm9jdXMtZmFsbGJhY2spIHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblxuXHRcdC8vIE9ubHkgdmlzaWJsZSBpbiBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZS5cblx0XHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQ7XG5cdH1cblxuXHQmOmNoZWNrZWQge1xuXHRcdGJhY2tncm91bmQ6IHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblx0XHRib3JkZXItY29sb3I6IHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblxuXHRcdC8vIEhpZGUgZGVmYXVsdCBjaGVja2JveCBzdHlsZXMgaW4gSUUuXG5cdFx0Jjo6LW1zLWNoZWNrIHtcblx0XHRcdG9wYWNpdHk6IDA7XG5cdFx0fVxuXHR9XG5cblx0JjpjaGVja2VkOjpiZWZvcmUsXG5cdCZbYXJpYS1jaGVja2VkPVwibWl4ZWRcIl06OmJlZm9yZSB7XG5cdFx0bWFyZ2luOiAtM3B4IC01cHg7XG5cdFx0Y29sb3I6IGNvbG9ycy4kd2hpdGU7XG5cblx0XHRAaW5jbHVkZSBicmVhay1tZWRpdW0oKSB7XG5cdFx0XHRtYXJnaW46IC00cHggMCAwIC01cHg7XG5cdFx0fVxuXHR9XG5cblx0JlthcmlhLWNoZWNrZWQ9XCJtaXhlZFwiXSB7XG5cdFx0YmFja2dyb3VuZDogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXHRcdGJvcmRlci1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXG5cdFx0Jjo6YmVmb3JlIHtcblx0XHRcdC8vIEluaGVyaXRlZCBmcm9tIGBmb3Jtcy5jc3NgLlxuXHRcdFx0Ly8gU2VlOiBodHRwczovL2dpdGh1Yi5jb20vV29yZFByZXNzL3dvcmRwcmVzcy1kZXZlbG9wL3RyZWUvNS4xLjEvc3JjL3dwLWFkbWluL2Nzcy9mb3Jtcy5jc3MjTDEyMi1MMTMyXG5cdFx0XHRjb250ZW50OiBcIlxcZjQ2MFwiO1xuXHRcdFx0ZmxvYXQ6IGxlZnQ7XG5cdFx0XHRkaXNwbGF5OiBpbmxpbmUtYmxvY2s7XG5cdFx0XHR2ZXJ0aWNhbC1hbGlnbjogbWlkZGxlO1xuXHRcdFx0d2lkdGg6IDE2cHg7XG5cdFx0XHQvKiBzdHlsZWxpbnQtZGlzYWJsZS1uZXh0LWxpbmUgZm9udC1mYW1pbHktbm8tbWlzc2luZy1nZW5lcmljLWZhbWlseS1rZXl3b3JkIC0tIGRhc2hpY29ucyBkb24ndCBuZWVkIGEgZ2VuZXJpYyBmYW1pbHkga2V5d29yZC4gKi9cblx0XHRcdGZvbnQ6IG5vcm1hbCAzMHB4LzEgZGFzaGljb25zO1xuXHRcdFx0c3BlYWs6IG5vbmU7XG5cdFx0XHQtd2Via2l0LWZvbnQtc21vb3RoaW5nOiBhbnRpYWxpYXNlZDtcblx0XHRcdC1tb3otb3N4LWZvbnQtc21vb3RoaW5nOiBncmF5c2NhbGU7XG5cblx0XHRcdEBpbmNsdWRlIGJyZWFrLW1lZGl1bSgpIHtcblx0XHRcdFx0ZmxvYXQ6IG5vbmU7XG5cdFx0XHRcdGZvbnQtc2l6ZTogMjFweDtcblx0XHRcdH1cblx0XHR9XG5cdH1cblxuXHQmW2FyaWEtZGlzYWJsZWQ9XCJ0cnVlXCJdLFxuXHQmOmRpc2FibGVkIHtcblx0XHRiYWNrZ3JvdW5kOiBjb2xvcnMuJGdyYXktMTAwO1xuXHRcdGJvcmRlci1jb2xvcjogY29sb3JzLiRncmF5LTMwMDtcblx0XHRjdXJzb3I6IGRlZmF1bHQ7XG5cblx0XHQvLyBPdmVycmlkZSBzdHlsZSBpbmhlcml0ZWQgZnJvbSB3cC1hZG1pbi4gUmVxdWlyZWQgdG8gYXZvaWQgZGVncmFkZWQgYXBwZWFyYW5jZSBvbiBkaWZmZXJlbnQgYmFja2dyb3VuZHMuXG5cdFx0b3BhY2l0eTogMTtcblx0fVxufVxuXG5AbWl4aW4gcmFkaW8tY29udHJvbCB7XG5cdGJvcmRlcjogdmFyaWFibGVzLiRib3JkZXItd2lkdGggc29saWQgY29sb3JzLiRncmF5LTkwMDtcblx0bWFyZ2luLXJpZ2h0OiB2YXJpYWJsZXMuJGdyaWQtdW5pdC0xNTtcblx0dHJhbnNpdGlvbjogbm9uZTtcblx0Ym9yZGVyLXJhZGl1czogdmFyaWFibGVzLiRyYWRpdXMtcm91bmQ7XG5cdHdpZHRoOiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemUtc207XG5cdGhlaWdodDogdmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLXNtO1xuXHRtaW4td2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZS1zbTtcblx0bWF4LXdpZHRoOiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemUtc207XG5cdHBvc2l0aW9uOiByZWxhdGl2ZTtcblxuXHRAbWVkaWEgbm90IChwcmVmZXJzLXJlZHVjZWQtbW90aW9uKSB7XG5cdFx0dHJhbnNpdGlvbjogYm94LXNoYWRvdyAwLjFzIGxpbmVhcjtcblx0fVxuXG5cdEBpbmNsdWRlIGJyZWFrLXNtYWxsKCkge1xuXHRcdGhlaWdodDogdmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplO1xuXHRcdHdpZHRoOiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemU7XG5cdFx0bWluLXdpZHRoOiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemU7XG5cdFx0bWF4LXdpZHRoOiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemU7XG5cdH1cblxuXHQmOmNoZWNrZWQ6OmJlZm9yZSB7XG5cdFx0Ym94LXNpemluZzogaW5oZXJpdDtcblx0XHR3aWR0aDogbWF0aC5kaXYodmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLXNtLCAyKTtcblx0XHRoZWlnaHQ6IG1hdGguZGl2KHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZS1zbSwgMik7XG5cdFx0cG9zaXRpb246IGFic29sdXRlO1xuXHRcdHRvcDogNTAlO1xuXHRcdGxlZnQ6IDUwJTtcblx0XHR0cmFuc2Zvcm06IHRyYW5zbGF0ZSgtNTAlLCAtNTAlKTtcblx0XHRtYXJnaW46IDA7XG5cdFx0YmFja2dyb3VuZC1jb2xvcjogY29sb3JzLiR3aGl0ZTtcblxuXHRcdC8vIFRoaXMgYm9yZGVyIHNlcnZlcyBhcyBhIGJhY2tncm91bmQgY29sb3IgaW4gV2luZG93cyBIaWdoIENvbnRyYXN0IG1vZGUuXG5cdFx0Ym9yZGVyOiA0cHggc29saWQgY29sb3JzLiR3aGl0ZTtcblxuXHRcdEBpbmNsdWRlIGJyZWFrLXNtYWxsKCkge1xuXHRcdFx0d2lkdGg6IG1hdGguZGl2KHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZSwgMik7XG5cdFx0XHRoZWlnaHQ6IG1hdGguZGl2KHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZSwgMik7XG5cdFx0fVxuXHR9XG5cblx0Jjpmb2N1cyB7XG5cdFx0Ym94LXNoYWRvdzogMCAwIDAgKHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoICogMikgY29sb3JzLiR3aGl0ZSwgMCAwIDAgKHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoICogMiArIHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoLWZvY3VzLWZhbGxiYWNrKSB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcik7XG5cblx0XHQvLyBPbmx5IHZpc2libGUgaW4gV2luZG93cyBIaWdoIENvbnRyYXN0IG1vZGUuXG5cdFx0b3V0bGluZTogMnB4IHNvbGlkIHRyYW5zcGFyZW50O1xuXHR9XG5cblx0JjpjaGVja2VkIHtcblx0XHRiYWNrZ3JvdW5kOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcik7XG5cdFx0Ym9yZGVyOiBub25lO1xuXHR9XG59XG5cbi8qKlxuICogUmVzZXQgZGVmYXVsdCBzdHlsZXMgZm9yIEphdmFTY3JpcHQgVUkgYmFzZWQgcGFnZXMuXG4gKiBUaGlzIGlzIGEgV1AtYWRtaW4gYWdub3N0aWMgcmVzZXRcbiAqL1xuXG5AbWl4aW4gcmVzZXQge1xuXHRib3gtc2l6aW5nOiBib3JkZXItYm94O1xuXG5cdCosXG5cdCo6OmJlZm9yZSxcblx0Kjo6YWZ0ZXIge1xuXHRcdGJveC1zaXppbmc6IGluaGVyaXQ7XG5cdH1cbn1cblxuQG1peGluIGxpbmstcmVzZXQge1xuXHQmOmZvY3VzIHtcblx0XHRjb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3ItLXJnYik7XG5cdFx0Ym94LXNoYWRvdzogMCAwIDAgdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvciwgIzAwN2NiYSk7XG5cdFx0Ym9yZGVyLXJhZGl1czogdmFyaWFibGVzLiRyYWRpdXMtc21hbGw7XG5cdH1cbn1cblxuLy8gVGhlIGVkaXRvciBpbnB1dCByZXNldCB3aXRoIGluY3JlYXNlZCBzcGVjaWZpY2l0eSB0byBhdm9pZCB0aGVtZSBzdHlsZXMgYmxlZWRpbmcgaW4uXG5AbWl4aW4gZWRpdG9yLWlucHV0LXJlc2V0KCkge1xuXHRmb250LWZhbWlseTogdmFyaWFibGVzLiRlZGl0b3ItaHRtbC1mb250ICFpbXBvcnRhbnQ7XG5cdGNvbG9yOiBjb2xvcnMuJGdyYXktOTAwICFpbXBvcnRhbnQ7XG5cdGJhY2tncm91bmQ6IGNvbG9ycy4kd2hpdGUgIWltcG9ydGFudDtcblx0cGFkZGluZzogdmFyaWFibGVzLiRncmlkLXVuaXQtMTUgIWltcG9ydGFudDtcblx0Ym9yZGVyOiB2YXJpYWJsZXMuJGJvcmRlci13aWR0aCBzb2xpZCBjb2xvcnMuJGdyYXktOTAwICFpbXBvcnRhbnQ7XG5cdGJveC1zaGFkb3c6IG5vbmUgIWltcG9ydGFudDtcblx0Ym9yZGVyLXJhZGl1czogdmFyaWFibGVzLiRyYWRpdXMtc21hbGwgIWltcG9ydGFudDtcblxuXHQvLyBGb250cyBzbWFsbGVyIHRoYW4gMTZweCBjYXVzZXMgbW9iaWxlIHNhZmFyaSB0byB6b29tLlxuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kbW9iaWxlLXRleHQtbWluLWZvbnQtc2l6ZSAhaW1wb3J0YW50O1xuXHRAaW5jbHVkZSBicmVhay1zbWFsbCB7XG5cdFx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGRlZmF1bHQtZm9udC1zaXplICFpbXBvcnRhbnQ7XG5cdH1cblxuXHQmOmZvY3VzIHtcblx0XHRib3JkZXItY29sb3I6IHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKSAhaW1wb3J0YW50O1xuXHRcdGJveC1zaGFkb3c6IDAgMCAwICh2YXJpYWJsZXMuJGJvcmRlci13aWR0aC1mb2N1cy1mYWxsYmFjayAtIHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoKSB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcikgIWltcG9ydGFudDtcblxuXHRcdC8vIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlIHdpbGwgc2hvdyB0aGlzIG91dGxpbmUsIGJ1dCBub3QgdGhlIGJveC1zaGFkb3cuXG5cdFx0b3V0bGluZTogMnB4IHNvbGlkIHRyYW5zcGFyZW50ICFpbXBvcnRhbnQ7XG5cdH1cbn1cblxuLyoqXG4gKiBSZXNldCB0aGUgV1AgQWRtaW4gcGFnZSBzdHlsZXMgZm9yIEd1dGVuYmVyZy1saWtlIHBhZ2VzLlxuICovXG5cbkBtaXhpbiB3cC1hZG1pbi1yZXNldCggJGNvbnRlbnQtY29udGFpbmVyICkge1xuXHRiYWNrZ3JvdW5kOiBjb2xvcnMuJHdoaXRlO1xuXG5cdCN3cGNvbnRlbnQge1xuXHRcdHBhZGRpbmctbGVmdDogMDtcblx0fVxuXG5cdCN3cGJvZHktY29udGVudCB7XG5cdFx0cGFkZGluZy1ib3R0b206IDA7XG5cdH1cblxuXHQvKiBXZSBoaWRlIGxlZ2FjeSBub3RpY2VzIGluIEd1dGVuYmVyZyBCYXNlZCBQYWdlcywgYmVjYXVzZSB0aGV5IHdlcmUgbm90IGRlc2lnbmVkIGluIGEgd2F5IHRoYXQgc2NhbGVkIHdlbGwuXG5cdCAgIFBsdWdpbnMgY2FuIHVzZSBHdXRlbmJlcmcgbm90aWNlcyBpZiB0aGV5IG5lZWQgdG8gcGFzcyBvbiBpbmZvcm1hdGlvbiB0byB0aGUgdXNlciB3aGVuIHRoZXkgYXJlIGVkaXRpbmcuICovXG5cdCN3cGJvZHktY29udGVudCA+IGRpdjpub3QoI3sgJGNvbnRlbnQtY29udGFpbmVyIH0pOm5vdCgjc2NyZWVuLW1ldGEpIHtcblx0XHRkaXNwbGF5OiBub25lO1xuXHR9XG5cblx0I3dwZm9vdGVyIHtcblx0XHRkaXNwbGF5OiBub25lO1xuXHR9XG5cblx0LmExMXktc3BlYWstcmVnaW9uIHtcblx0XHRsZWZ0OiAtMXB4O1xuXHRcdHRvcDogLTFweDtcblx0fVxuXG5cdHVsI2FkbWlubWVudSBhLndwLWhhcy1jdXJyZW50LXN1Ym1lbnU6OmFmdGVyLFxuXHR1bCNhZG1pbm1lbnUgPiBsaS5jdXJyZW50ID4gYS5jdXJyZW50OjphZnRlciB7XG5cdFx0Ym9yZGVyLXJpZ2h0LWNvbG9yOiBjb2xvcnMuJHdoaXRlO1xuXHR9XG5cblx0Lm1lZGlhLWZyYW1lIHNlbGVjdC5hdHRhY2htZW50LWZpbHRlcnM6bGFzdC1vZi10eXBlIHtcblx0XHR3aWR0aDogYXV0bztcblx0XHRtYXgtd2lkdGg6IDEwMCU7XG5cdH1cbn1cblxuQG1peGluIGFkbWluLXNjaGVtZSgkY29sb3ItcHJpbWFyeSkge1xuXHQvLyBEZWZpbmUgUkdCIGVxdWl2YWxlbnRzIGZvciB1c2UgaW4gcmdiYSBmdW5jdGlvbi5cblx0Ly8gSGV4YWRlY2ltYWwgY3NzIHZhcnMgZG8gbm90IHdvcmsgaW4gdGhlIHJnYmEgZnVuY3Rpb24uXG5cdC0td3AtYWRtaW4tdGhlbWUtY29sb3I6ICN7JGNvbG9yLXByaW1hcnl9O1xuXHQtLXdwLWFkbWluLXRoZW1lLWNvbG9yLS1yZ2I6ICN7ZnVuY3Rpb25zLmhleC10by1yZ2IoJGNvbG9yLXByaW1hcnkpfTtcblx0Ly8gRGFya2VyIHNoYWRlcy5cblx0LS13cC1hZG1pbi10aGVtZS1jb2xvci1kYXJrZXItMTA6ICN7Y29sb3IuYWRqdXN0KCRjb2xvci1wcmltYXJ5LCAkbGlnaHRuZXNzOiAtNSUpfTtcblx0LS13cC1hZG1pbi10aGVtZS1jb2xvci1kYXJrZXItMTAtLXJnYjogI3tmdW5jdGlvbnMuaGV4LXRvLXJnYihjb2xvci5hZGp1c3QoJGNvbG9yLXByaW1hcnksICRsaWdodG5lc3M6IC01JSkpfTtcblx0LS13cC1hZG1pbi10aGVtZS1jb2xvci1kYXJrZXItMjA6ICN7Y29sb3IuYWRqdXN0KCRjb2xvci1wcmltYXJ5LCAkbGlnaHRuZXNzOiAtMTAlKX07XG5cdC0td3AtYWRtaW4tdGhlbWUtY29sb3ItZGFya2VyLTIwLS1yZ2I6ICN7ZnVuY3Rpb25zLmhleC10by1yZ2IoY29sb3IuYWRqdXN0KCRjb2xvci1wcmltYXJ5LCAkbGlnaHRuZXNzOiAtMTAlKSl9O1xuXG5cdC8vIEZvY3VzIHN0eWxlIHdpZHRoLlxuXHQvLyBBdm9pZCByb3VuZGluZyBpc3N1ZXMgYnkgc2hvd2luZyBhIHdob2xlIDJweCBmb3IgMXggc2NyZWVucywgYW5kIDEuNXB4IG9uIGhpZ2ggcmVzb2x1dGlvbiBzY3JlZW5zLlxuXHQtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1czogMnB4O1xuXHRAbWVkaWEgKCAtd2Via2l0LW1pbi1kZXZpY2UtcGl4ZWwtcmF0aW86IDIpLCAobWluLXJlc29sdXRpb246IDE5MmRwaSkge1xuXHRcdC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzOiAxLjVweDtcblx0fVxufVxuXG5AbWl4aW4gd29yZHByZXNzLWFkbWluLXNjaGVtZXMoKSB7XG5cdGJvZHkuYWRtaW4tY29sb3ItbGlnaHQge1xuXHRcdEBpbmNsdWRlIGFkbWluLXNjaGVtZSgjMDA4NWJhKTtcblx0fVxuXG5cdGJvZHkuYWRtaW4tY29sb3ItbW9kZXJuIHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoIzM4NThlOSk7XG5cdH1cblxuXHRib2R5LmFkbWluLWNvbG9yLWJsdWUge1xuXHRcdEBpbmNsdWRlIGFkbWluLXNjaGVtZSgjMDk2NDg0KTtcblx0fVxuXG5cdGJvZHkuYWRtaW4tY29sb3ItY29mZmVlIHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoIzQ2NDAzYyk7XG5cdH1cblxuXHRib2R5LmFkbWluLWNvbG9yLWVjdG9wbGFzbSB7XG5cdFx0QGluY2x1ZGUgYWRtaW4tc2NoZW1lKCM1MjNmNmQpO1xuXHR9XG5cblx0Ym9keS5hZG1pbi1jb2xvci1taWRuaWdodCB7XG5cdFx0QGluY2x1ZGUgYWRtaW4tc2NoZW1lKCNlMTRkNDMpO1xuXHR9XG5cblx0Ym9keS5hZG1pbi1jb2xvci1vY2VhbiB7XG5cdFx0QGluY2x1ZGUgYWRtaW4tc2NoZW1lKCM2MjdjODMpO1xuXHR9XG5cblx0Ym9keS5hZG1pbi1jb2xvci1zdW5yaXNlIHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoI2RkODIzYik7XG5cdH1cbn1cblxuLy8gRGVwcmVjYXRlZCBmcm9tIFVJLCBrZXB0IGZvciBiYWNrLWNvbXBhdC5cbkBtaXhpbiBiYWNrZ3JvdW5kLWNvbG9ycy1kZXByZWNhdGVkKCkge1xuXHQuaGFzLXZlcnktbGlnaHQtZ3JheS1iYWNrZ3JvdW5kLWNvbG9yIHtcblx0XHRiYWNrZ3JvdW5kLWNvbG9yOiAjZWVlO1xuXHR9XG5cblx0Lmhhcy12ZXJ5LWRhcmstZ3JheS1iYWNrZ3JvdW5kLWNvbG9yIHtcblx0XHRiYWNrZ3JvdW5kLWNvbG9yOiAjMzEzMTMxO1xuXHR9XG59XG5cbi8vIERlcHJlY2F0ZWQgZnJvbSBVSSwga2VwdCBmb3IgYmFjay1jb21wYXQuXG5AbWl4aW4gZm9yZWdyb3VuZC1jb2xvcnMtZGVwcmVjYXRlZCgpIHtcblx0Lmhhcy12ZXJ5LWxpZ2h0LWdyYXktY29sb3Ige1xuXHRcdGNvbG9yOiAjZWVlO1xuXHR9XG5cblx0Lmhhcy12ZXJ5LWRhcmstZ3JheS1jb2xvciB7XG5cdFx0Y29sb3I6ICMzMTMxMzE7XG5cdH1cbn1cblxuLy8gRGVwcmVjYXRlZCBmcm9tIFVJLCBrZXB0IGZvciBiYWNrLWNvbXBhdC5cbkBtaXhpbiBncmFkaWVudC1jb2xvcnMtZGVwcmVjYXRlZCgpIHtcblx0Ly8gT3VyIGNsYXNzZXMgdXNlcyB0aGUgc2FtZSB2YWx1ZXMgd2Ugc2V0IGZvciBncmFkaWVudCB2YWx1ZSBhdHRyaWJ1dGVzLlxuXG5cdC8qIHN0eWxlbGludC1kaXNhYmxlIEBzdHlsaXN0aWMvZnVuY3Rpb24tY29tbWEtc3BhY2UtYWZ0ZXIgLS0gV2UgY2FuIG5vdCB1c2Ugc3BhY2luZyBiZWNhdXNlIG9mIFdQIG11bHRpIHNpdGUga3NlcyBydWxlLiAqL1xuXHQuaGFzLXZpdmlkLWdyZWVuLWN5YW4tdG8tdml2aWQtY3lhbi1ibHVlLWdyYWRpZW50LWJhY2tncm91bmQge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCgxMzVkZWcscmdiYSgwLDIwOCwxMzIsMSkgMCUscmdiYSg2LDE0NywyMjcsMSkgMTAwJSk7XG5cdH1cblxuXHQuaGFzLXB1cnBsZS1jcnVzaC1ncmFkaWVudC1iYWNrZ3JvdW5kIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQoMTM1ZGVnLHJnYig1MiwyMjYsMjI4KSAwJSxyZ2IoNzEsMzMsMjUxKSA1MCUscmdiKDE3MSwyOSwyNTQpIDEwMCUpO1xuXHR9XG5cblx0Lmhhcy1oYXp5LWRhd24tZ3JhZGllbnQtYmFja2dyb3VuZCB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KDEzNWRlZyxyZ2IoMjUwLDE3MiwxNjgpIDAlLHJnYigyMTgsMjA4LDIzNikgMTAwJSk7XG5cdH1cblxuXHQuaGFzLXN1YmR1ZWQtb2xpdmUtZ3JhZGllbnQtYmFja2dyb3VuZCB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KDEzNWRlZyxyZ2IoMjUwLDI1MCwyMjUpIDAlLHJnYigxMDMsMTY2LDExMykgMTAwJSk7XG5cdH1cblxuXHQuaGFzLWF0b21pYy1jcmVhbS1ncmFkaWVudC1iYWNrZ3JvdW5kIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQoMTM1ZGVnLHJnYigyNTMsMjE1LDE1NCkgMCUscmdiKDAsNzQsODkpIDEwMCUpO1xuXHR9XG5cblx0Lmhhcy1uaWdodHNoYWRlLWdyYWRpZW50LWJhY2tncm91bmQge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCgxMzVkZWcscmdiKDUxLDksMTA0KSAwJSxyZ2IoNDksMjA1LDIwNykgMTAwJSk7XG5cdH1cblxuXHQuaGFzLW1pZG5pZ2h0LWdyYWRpZW50LWJhY2tncm91bmQge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCgxMzVkZWcscmdiKDIsMywxMjkpIDAlLHJnYig0MCwxMTYsMjUyKSAxMDAlKTtcblx0fVxuXHQvKiBzdHlsZWxpbnQtZW5hYmxlIEBzdHlsaXN0aWMvZnVuY3Rpb24tY29tbWEtc3BhY2UtYWZ0ZXIgKi9cbn1cblxuQG1peGluIGN1c3RvbS1zY3JvbGxiYXJzLW9uLWhvdmVyKCRoYW5kbGUtY29sb3IsICRoYW5kbGUtY29sb3ItaG92ZXIpIHtcblxuXHQvLyBXZWJLaXRcblx0Jjo6LXdlYmtpdC1zY3JvbGxiYXIge1xuXHRcdHdpZHRoOiAxMnB4O1xuXHRcdGhlaWdodDogMTJweDtcblx0fVxuXHQmOjotd2Via2l0LXNjcm9sbGJhci10cmFjayB7XG5cdFx0YmFja2dyb3VuZC1jb2xvcjogdHJhbnNwYXJlbnQ7XG5cdH1cblx0Jjo6LXdlYmtpdC1zY3JvbGxiYXItdGh1bWIge1xuXHRcdGJhY2tncm91bmQtY29sb3I6ICRoYW5kbGUtY29sb3I7XG5cdFx0Ym9yZGVyLXJhZGl1czogOHB4O1xuXHRcdGJvcmRlcjogM3B4IHNvbGlkIHRyYW5zcGFyZW50O1xuXHRcdGJhY2tncm91bmQtY2xpcDogcGFkZGluZy1ib3g7XG5cdH1cblx0Jjpob3Zlcjo6LXdlYmtpdC1zY3JvbGxiYXItdGh1bWIsIC8vIFRoaXMgbmVlZHMgc3BlY2lmaWNpdHkuXG5cdCY6Zm9jdXM6Oi13ZWJraXQtc2Nyb2xsYmFyLXRodW1iLFxuXHQmOmZvY3VzLXdpdGhpbjo6LXdlYmtpdC1zY3JvbGxiYXItdGh1bWIge1xuXHRcdGJhY2tncm91bmQtY29sb3I6ICRoYW5kbGUtY29sb3ItaG92ZXI7XG5cdH1cblxuXHQvLyBGaXJlZm94IDEwOSsgYW5kIENocm9tZSAxMTErXG5cdHNjcm9sbGJhci13aWR0aDogdGhpbjtcblx0c2Nyb2xsYmFyLWd1dHRlcjogc3RhYmxlIGJvdGgtZWRnZXM7XG5cdHNjcm9sbGJhci1jb2xvcjogJGhhbmRsZS1jb2xvciB0cmFuc3BhcmVudDsgLy8gU3ludGF4LCBcImRhcmtcIiwgXCJsaWdodFwiLCBvciBcIiNoYW5kbGUtY29sb3IgI3RyYWNrLWNvbG9yXCJcblxuXHQmOmhvdmVyLFxuXHQmOmZvY3VzLFxuXHQmOmZvY3VzLXdpdGhpbiB7XG5cdFx0c2Nyb2xsYmFyLWNvbG9yOiAkaGFuZGxlLWNvbG9yLWhvdmVyIHRyYW5zcGFyZW50O1xuXHR9XG5cblx0Ly8gTmVlZGVkIHRvIGZpeCBhIFNhZmFyaSByZW5kZXJpbmcgaXNzdWUuXG5cdHdpbGwtY2hhbmdlOiB0cmFuc2Zvcm07XG5cblx0Ly8gQWx3YXlzIHNob3cgc2Nyb2xsYmFyIG9uIE1vYmlsZSBkZXZpY2VzLlxuXHRAbWVkaWEgKGhvdmVyOiBub25lKSB7XG5cdFx0JiB7XG5cdFx0XHRzY3JvbGxiYXItY29sb3I6ICRoYW5kbGUtY29sb3ItaG92ZXIgdHJhbnNwYXJlbnQ7XG5cdFx0fVxuXHR9XG59XG5cbkBtaXhpbiBzZWxlY3RlZC1ibG9jay1vdXRsaW5lKCR3aWR0aFJhdGlvOiAxKSB7XG5cdG91dGxpbmUtY29sb3I6IHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblx0b3V0bGluZS1zdHlsZTogc29saWQ7XG5cdG91dGxpbmUtd2lkdGg6IGNhbGMoI3skd2lkdGhSYXRpb30gKiAodmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSAvIHZhcigtLXdwLWJsb2NrLWVkaXRvci1pZnJhbWUtem9vbS1vdXQtc2NhbGUsIDEpKSk7XG5cdG91dGxpbmUtb2Zmc2V0OiBjYWxjKCN7JHdpZHRoUmF0aW99ICogKCgtMSAqIHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgKSAvIHZhcigtLXdwLWJsb2NrLWVkaXRvci1pZnJhbWUtem9vbS1vdXQtc2NhbGUsIDEpKSk7XG59XG5cbkBtaXhpbiBzZWxlY3RlZC1ibG9jay1mb2N1cygkd2lkdGhSYXRpbzogMSkge1xuXHRjb250ZW50OiBcIlwiO1xuXHRwb3NpdGlvbjogYWJzb2x1dGU7XG5cdHBvaW50ZXItZXZlbnRzOiBub25lO1xuXHR0b3A6IDA7XG5cdHJpZ2h0OiAwO1xuXHRib3R0b206IDA7XG5cdGxlZnQ6IDA7XG5cdEBpbmNsdWRlIHNlbGVjdGVkLWJsb2NrLW91dGxpbmUoJHdpZHRoUmF0aW8pO1xufVxuIiwiLyoqXG4gKiBTQ1NTIFZhcmlhYmxlcy5cbiAqXG4gKiBQbGVhc2UgdXNlIHZhcmlhYmxlcyBmcm9tIHRoaXMgc2hlZXQgdG8gZW5zdXJlIGNvbnNpc3RlbmN5IGFjcm9zcyB0aGUgVUkuXG4gKiBEb24ndCBhZGQgdG8gdGhpcyBzaGVldCB1bmxlc3MgeW91J3JlIHByZXR0eSBzdXJlIHRoZSB2YWx1ZSB3aWxsIGJlIHJldXNlZCBpbiBtYW55IHBsYWNlcy5cbiAqIEZvciBleGFtcGxlLCBkb24ndCBhZGQgcnVsZXMgdG8gdGhpcyBzaGVldCB0aGF0IGFmZmVjdCBibG9jayB2aXN1YWxzLiBJdCdzIHB1cmVseSBmb3IgVUkuXG4gKi9cblxuQHVzZSBcIi4vY29sb3JzXCI7XG5cbi8qKlxuICogRm9udHMgJiBiYXNpYyB2YXJpYWJsZXMuXG4gKi9cblxuJGRlZmF1bHQtZm9udDogLWFwcGxlLXN5c3RlbSwgQmxpbmtNYWNTeXN0ZW1Gb250LFwiU2Vnb2UgVUlcIiwgUm9ib3RvLCBPeHlnZW4tU2FucywgVWJ1bnR1LCBDYW50YXJlbGwsXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmOyAvLyBUb2RvOiBkZXByZWNhdGUgaW4gZmF2b3Igb2YgJGZhbWlseSB2YXJpYWJsZXNcbiRkZWZhdWx0LWxpbmUtaGVpZ2h0OiAxLjQ7IC8vIFRvZG86IGRlcHJlY2F0ZSBpbiBmYXZvciBvZiAkbGluZS1oZWlnaHQgdG9rZW5zXG5cbi8qKlxuICogVHlwb2dyYXBoeVxuICovXG5cbi8vIFNpemVzXG4kZm9udC1zaXplLXgtc21hbGw6IDExcHg7XG4kZm9udC1zaXplLXNtYWxsOiAxMnB4O1xuJGZvbnQtc2l6ZS1tZWRpdW06IDEzcHg7XG4kZm9udC1zaXplLWxhcmdlOiAxNXB4O1xuJGZvbnQtc2l6ZS14LWxhcmdlOiAyMHB4O1xuJGZvbnQtc2l6ZS0yeC1sYXJnZTogMzJweDtcblxuLy8gTGluZSBoZWlnaHRzXG4kZm9udC1saW5lLWhlaWdodC14LXNtYWxsOiAxNnB4O1xuJGZvbnQtbGluZS1oZWlnaHQtc21hbGw6IDIwcHg7XG4kZm9udC1saW5lLWhlaWdodC1tZWRpdW06IDI0cHg7XG4kZm9udC1saW5lLWhlaWdodC1sYXJnZTogMjhweDtcbiRmb250LWxpbmUtaGVpZ2h0LXgtbGFyZ2U6IDMycHg7XG4kZm9udC1saW5lLWhlaWdodC0yeC1sYXJnZTogNDBweDtcblxuLy8gV2VpZ2h0c1xuJGZvbnQtd2VpZ2h0LXJlZ3VsYXI6IDQwMDtcbiRmb250LXdlaWdodC1tZWRpdW06IDQ5OTsgLy8gZW5zdXJlcyBmYWxsYmFjayB0byA0MDAgKGluc3RlYWQgb2YgNjAwKVxuXG4vLyBGYW1pbGllc1xuJGZvbnQtZmFtaWx5LWhlYWRpbmdzOiAtYXBwbGUtc3lzdGVtLCBcInN5c3RlbS11aVwiLCBcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLCBcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7XG4kZm9udC1mYW1pbHktYm9keTogLWFwcGxlLXN5c3RlbSwgXCJzeXN0ZW0tdWlcIiwgXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCwgXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmO1xuJGZvbnQtZmFtaWx5LW1vbm86IE1lbmxvLCBDb25zb2xhcywgbW9uYWNvLCBtb25vc3BhY2U7XG5cbi8qKlxuICogR3JpZCBTeXN0ZW0uXG4gKiBodHRwczovL21ha2Uud29yZHByZXNzLm9yZy9kZXNpZ24vMjAxOS8xMC8zMS9wcm9wb3NhbC1hLWNvbnNpc3RlbnQtc3BhY2luZy1zeXN0ZW0tZm9yLXdvcmRwcmVzcy9cbiAqL1xuXG4kZ3JpZC11bml0OiA4cHg7XG4kZ3JpZC11bml0LTA1OiAwLjUgKiAkZ3JpZC11bml0O1x0Ly8gNHB4XG4kZ3JpZC11bml0LTEwOiAxICogJGdyaWQtdW5pdDtcdFx0Ly8gOHB4XG4kZ3JpZC11bml0LTE1OiAxLjUgKiAkZ3JpZC11bml0O1x0Ly8gMTJweFxuJGdyaWQtdW5pdC0yMDogMiAqICRncmlkLXVuaXQ7XHRcdC8vIDE2cHhcbiRncmlkLXVuaXQtMzA6IDMgKiAkZ3JpZC11bml0O1x0XHQvLyAyNHB4XG4kZ3JpZC11bml0LTQwOiA0ICogJGdyaWQtdW5pdDtcdFx0Ly8gMzJweFxuJGdyaWQtdW5pdC01MDogNSAqICRncmlkLXVuaXQ7XHRcdC8vIDQwcHhcbiRncmlkLXVuaXQtNjA6IDYgKiAkZ3JpZC11bml0O1x0XHQvLyA0OHB4XG4kZ3JpZC11bml0LTcwOiA3ICogJGdyaWQtdW5pdDtcdFx0Ly8gNTZweFxuJGdyaWQtdW5pdC04MDogOCAqICRncmlkLXVuaXQ7XHRcdC8vIDY0cHhcblxuLyoqXG4gKiBSYWRpdXMgc2NhbGUuXG4gKi9cblxuJHJhZGl1cy14LXNtYWxsOiAxcHg7ICAgLy8gQXBwbGllZCB0byBlbGVtZW50cyBsaWtlIGJ1dHRvbnMgbmVzdGVkIHdpdGhpbiBwcmltaXRpdmVzIGxpa2UgaW5wdXRzLlxuJHJhZGl1cy1zbWFsbDogMnB4OyAgICAgLy8gQXBwbGllZCB0byBtb3N0IHByaW1pdGl2ZXMuXG4kcmFkaXVzLW1lZGl1bTogNHB4OyAgICAvLyBBcHBsaWVkIHRvIGNvbnRhaW5lcnMgd2l0aCBzbWFsbGVyIHBhZGRpbmcuXG4kcmFkaXVzLWxhcmdlOiA4cHg7ICAgICAvLyBBcHBsaWVkIHRvIGNvbnRhaW5lcnMgd2l0aCBsYXJnZXIgcGFkZGluZy5cbiRyYWRpdXMtZnVsbDogOTk5OXB4OyAgIC8vIEZvciBwaWxscy5cbiRyYWRpdXMtcm91bmQ6IDUwJTsgICAgIC8vIEZvciBjaXJjbGVzIGFuZCBvdmFscy5cblxuLyoqXG4gKiBFbGV2YXRpb24gc2NhbGUuXG4gKi9cblxuLy8gRm9yIHNlY3Rpb25zIGFuZCBjb250YWluZXJzIHRoYXQgZ3JvdXAgcmVsYXRlZCBjb250ZW50IGFuZCBjb250cm9scywgd2hpY2ggbWF5IG92ZXJsYXAgb3RoZXIgY29udGVudC4gRXhhbXBsZTogUHJldmlldyBGcmFtZS5cbiRlbGV2YXRpb24teC1zbWFsbDogMCAxcHggMXB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMyksIDAgMXB4IDJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpLCAwIDNweCAzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKSwgMCA0cHggNHB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMSk7XG5cbi8vIEZvciBjb21wb25lbnRzIHRoYXQgcHJvdmlkZSBjb250ZXh0dWFsIGZlZWRiYWNrIHdpdGhvdXQgYmVpbmcgaW50cnVzaXZlLiBHZW5lcmFsbHkgbm9uLWludGVycnVwdGl2ZS4gRXhhbXBsZTogVG9vbHRpcHMsIFNuYWNrYmFyLlxuJGVsZXZhdGlvbi1zbWFsbDogMCAxcHggMnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNSksIDAgMnB4IDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDZweCA2cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAzKSwgMCA4cHggOHB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMik7XG5cbi8vIEZvciBjb21wb25lbnRzIHRoYXQgb2ZmZXIgYWRkaXRpb25hbCBhY3Rpb25zLiBFeGFtcGxlOiBNZW51cywgQ29tbWFuZCBQYWxldHRlXG4kZWxldmF0aW9uLW1lZGl1bTogMCAycHggM3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNSksIDAgNHB4IDVweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDEycHggMTJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDMpLCAwIDE2cHggMTZweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vLyBGb3IgY29tcG9uZW50cyB0aGF0IGNvbmZpcm0gZGVjaXNpb25zIG9yIGhhbmRsZSBuZWNlc3NhcnkgaW50ZXJydXB0aW9ucy4gRXhhbXBsZTogTW9kYWxzLlxuJGVsZXZhdGlvbi1sYXJnZTogMCA1cHggMTVweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDgpLCAwIDE1cHggMjdweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDcpLCAwIDMwcHggMzZweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDUwcHggNDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vKipcbiAqIERpbWVuc2lvbnMuXG4gKi9cblxuJGljb24tc2l6ZTogMjRweDtcbiRidXR0b24tc2l6ZTogMzZweDtcbiRidXR0b24tc2l6ZS1uZXh0LWRlZmF1bHQtNDBweDogNDBweDsgLy8gdHJhbnNpdGlvbmFyeSB2YXJpYWJsZSBmb3IgbmV4dCBkZWZhdWx0IGJ1dHRvbiBzaXplXG4kYnV0dG9uLXNpemUtc21hbGw6IDI0cHg7XG4kYnV0dG9uLXNpemUtY29tcGFjdDogMzJweDtcbiRoZWFkZXItaGVpZ2h0OiA2NHB4O1xuJHBhbmVsLWhlYWRlci1oZWlnaHQ6ICRncmlkLXVuaXQtNjA7XG4kbmF2LXNpZGViYXItd2lkdGg6IDMwMHB4O1xuJGFkbWluLWJhci1oZWlnaHQ6IDMycHg7XG4kYWRtaW4tYmFyLWhlaWdodC1iaWc6IDQ2cHg7XG4kYWRtaW4tc2lkZWJhci13aWR0aDogMTYwcHg7XG4kYWRtaW4tc2lkZWJhci13aWR0aC1iaWc6IDE5MHB4O1xuJGFkbWluLXNpZGViYXItd2lkdGgtY29sbGFwc2VkOiAzNnB4O1xuJG1vZGFsLW1pbi13aWR0aDogMzUwcHg7XG4kbW9kYWwtd2lkdGgtc21hbGw6IDM4NHB4O1xuJG1vZGFsLXdpZHRoLW1lZGl1bTogNTEycHg7XG4kbW9kYWwtd2lkdGgtbGFyZ2U6IDg0MHB4O1xuJHNwaW5uZXItc2l6ZTogMTZweDtcbiRjYW52YXMtcGFkZGluZzogJGdyaWQtdW5pdC0yMDtcbiRwYWxldHRlLW1heC1oZWlnaHQ6IDM2OHB4O1xuXG4vKipcbiAqIE1vYmlsZSBzcGVjaWZpYyBzdHlsZXNcbiAqL1xuJG1vYmlsZS10ZXh0LW1pbi1mb250LXNpemU6IDE2cHg7IC8vIEFueSBmb250IHNpemUgYmVsb3cgMTZweCB3aWxsIGNhdXNlIE1vYmlsZSBTYWZhcmkgdG8gXCJ6b29tIGluXCIuXG5cbi8qKlxuICogRWRpdG9yIHN0eWxlcy5cbiAqL1xuXG4kc2lkZWJhci13aWR0aDogMjgwcHg7XG4kY29udGVudC13aWR0aDogODQwcHg7XG4kd2lkZS1jb250ZW50LXdpZHRoOiAxMTAwcHg7XG4kd2lkZ2V0LWFyZWEtd2lkdGg6IDcwMHB4O1xuJHNlY29uZGFyeS1zaWRlYmFyLXdpZHRoOiAzNTBweDtcbiRlZGl0b3ItZm9udC1zaXplOiAxNnB4O1xuJGRlZmF1bHQtYmxvY2stbWFyZ2luOiAyOHB4OyAvLyBUaGlzIHZhbHVlIHByb3ZpZGVzIGEgY29uc2lzdGVudCwgY29udGlndW91cyBzcGFjaW5nIGJldHdlZW4gYmxvY2tzLlxuJHRleHQtZWRpdG9yLWZvbnQtc2l6ZTogMTVweDtcbiRlZGl0b3ItbGluZS1oZWlnaHQ6IDEuODtcbiRlZGl0b3ItaHRtbC1mb250OiAkZm9udC1mYW1pbHktbW9ubztcblxuLyoqXG4gKiBCbG9jayAmIEVkaXRvciBVSS5cbiAqL1xuXG4kYmxvY2stdG9vbGJhci1oZWlnaHQ6ICRncmlkLXVuaXQtNjA7XG4kYm9yZGVyLXdpZHRoOiAxcHg7XG4kYm9yZGVyLXdpZHRoLWZvY3VzLWZhbGxiYWNrOiAycHg7IC8vIFRoaXMgZXhpc3RzIGFzIGEgZmFsbGJhY2ssIGFuZCBpcyBpZGVhbGx5IG92ZXJyaWRkZW4gYnkgdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSB1bmxlc3MgaW4gc29tZSBTQVNTIG1hdGggY2FzZXMuXG4kYm9yZGVyLXdpZHRoLXRhYjogMS41cHg7XG4kaGVscHRleHQtZm9udC1zaXplOiAxMnB4O1xuJHJhZGlvLWlucHV0LXNpemU6IDE2cHg7XG4kcmFkaW8taW5wdXQtc2l6ZS1zbTogMjRweDsgLy8gV2lkdGggJiBoZWlnaHQgZm9yIHNtYWxsIHZpZXdwb3J0cy5cblxuLy8gRGVwcmVjYXRlZCwgcGxlYXNlIGF2b2lkIHVzaW5nIHRoZXNlLlxuJGJsb2NrLXBhZGRpbmc6IDE0cHg7IC8vIFVzZWQgdG8gZGVmaW5lIHNwYWNlIGJldHdlZW4gYmxvY2sgZm9vdHByaW50IGFuZCBzdXJyb3VuZGluZyBib3JkZXJzLlxuJHJhZGl1cy1ibG9jay11aTogJHJhZGl1cy1zbWFsbDtcbiRzaGFkb3ctcG9wb3ZlcjogJGVsZXZhdGlvbi14LXNtYWxsO1xuJHNoYWRvdy1tb2RhbDogJGVsZXZhdGlvbi1sYXJnZTtcbiRkZWZhdWx0LWZvbnQtc2l6ZTogJGZvbnQtc2l6ZS1tZWRpdW07XG5cbi8qKlxuICogQmxvY2sgcGFkZGluZ3MuXG4gKi9cblxuLy8gUGFkZGluZyBmb3IgYmxvY2tzIHdpdGggYSBiYWNrZ3JvdW5kIGNvbG9yIChlLmcuIHBhcmFncmFwaCBvciBncm91cCkuXG4kYmxvY2stYmctcGFkZGluZy0tdjogMS4yNWVtO1xuJGJsb2NrLWJnLXBhZGRpbmctLWg6IDIuMzc1ZW07XG5cblxuLyoqXG4gKiBSZWFjdCBOYXRpdmUgc3BlY2lmaWMuXG4gKiBUaGVzZSB2YXJpYWJsZXMgZG8gbm90IGFwcGVhciB0byBiZSB1c2VkIGFueXdoZXJlIGVsc2UuXG4gKi9cblxuLy8gRGltZW5zaW9ucy5cbiRtb2JpbGUtaGVhZGVyLXRvb2xiYXItaGVpZ2h0OiA0NHB4O1xuJG1vYmlsZS1oZWFkZXItdG9vbGJhci1leHBhbmRlZC1oZWlnaHQ6IDUycHg7XG4kbW9iaWxlLWZsb2F0aW5nLXRvb2xiYXItaGVpZ2h0OiA0NHB4O1xuJG1vYmlsZS1mbG9hdGluZy10b29sYmFyLW1hcmdpbjogOHB4O1xuJG1vYmlsZS1jb2xvci1zd2F0Y2g6IDQ4cHg7XG5cbi8vIEJsb2NrIFVJLlxuJG1vYmlsZS1ibG9jay10b29sYmFyLWhlaWdodDogNDRweDtcbiRkaW1tZWQtb3BhY2l0eTogMTtcbiRibG9jay1lZGdlLXRvLWNvbnRlbnQ6IDE2cHg7XG4kc29saWQtYm9yZGVyLXNwYWNlOiAxMnB4O1xuJGRhc2hlZC1ib3JkZXItc3BhY2U6IDZweDtcbiRibG9jay1zZWxlY3RlZC1tYXJnaW46IDNweDtcbiRibG9jay1zZWxlY3RlZC1ib3JkZXItd2lkdGg6IDFweDtcbiRibG9jay1zZWxlY3RlZC1wYWRkaW5nOiAwO1xuJGJsb2NrLXNlbGVjdGVkLWNoaWxkLW1hcmdpbjogNXB4O1xuJGJsb2NrLXNlbGVjdGVkLXRvLWNvbnRlbnQ6ICRibG9jay1lZGdlLXRvLWNvbnRlbnQgLSAkYmxvY2stc2VsZWN0ZWQtbWFyZ2luIC0gJGJsb2NrLXNlbGVjdGVkLWJvcmRlci13aWR0aDtcbiIsIi8qKlxuICogQ29sb3JzXG4gKi9cblxuLy8gV29yZFByZXNzIGdyYXlzLlxuJGJsYWNrOiAjMDAwO1x0XHRcdC8vIFVzZSBvbmx5IHdoZW4geW91IHRydWx5IG5lZWQgcHVyZSBibGFjay4gRm9yIFVJLCB1c2UgJGdyYXktOTAwLlxuJGdyYXktOTAwOiAjMWUxZTFlO1xuJGdyYXktODAwOiAjMmYyZjJmO1xuJGdyYXktNzAwOiAjNzU3NTc1O1x0XHQvLyBNZWV0cyA0LjY6MSAoNC41OjEgaXMgbWluaW11bSkgdGV4dCBjb250cmFzdCBhZ2FpbnN0IHdoaXRlLlxuJGdyYXktNjAwOiAjOTQ5NDk0O1x0XHQvLyBNZWV0cyAzOjEgVUkgb3IgbGFyZ2UgdGV4dCBjb250cmFzdCBhZ2FpbnN0IHdoaXRlLlxuJGdyYXktNDAwOiAjY2NjO1xuJGdyYXktMzAwOiAjZGRkO1x0XHQvLyBVc2VkIGZvciBtb3N0IGJvcmRlcnMuXG4kZ3JheS0yMDA6ICNlMGUwZTA7XHRcdC8vIFVzZWQgc3BhcmluZ2x5IGZvciBsaWdodCBib3JkZXJzLlxuJGdyYXktMTAwOiAjZjBmMGYwO1x0XHQvLyBVc2VkIGZvciBsaWdodCBncmF5IGJhY2tncm91bmRzLlxuJHdoaXRlOiAjZmZmO1xuXG4vLyBPcGFjaXRpZXMgJiBhZGRpdGlvbmFsIGNvbG9ycy5cbiRkYXJrLWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJGdyYXktOTAwLCAwLjYyKTtcbiRtZWRpdW0tZ3JheS1wbGFjZWhvbGRlcjogcmdiYSgkZ3JheS05MDAsIDAuNTUpO1xuJGxpZ2h0LWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJHdoaXRlLCAwLjY1KTtcblxuLy8gQWxlcnQgY29sb3JzLlxuJGFsZXJ0LXllbGxvdzogI2YwYjg0OTtcbiRhbGVydC1yZWQ6ICNjYzE4MTg7XG4kYWxlcnQtZ3JlZW46ICM0YWI4NjY7XG5cbi8vIERlcHJlY2F0ZWQsIHBsZWFzZSBhdm9pZCB1c2luZyB0aGVzZS5cbiRkYXJrLXRoZW1lLWZvY3VzOiAkd2hpdGU7XHQvLyBGb2N1cyBjb2xvciB3aGVuIHRoZSB0aGVtZSBpcyBkYXJrLlxuIiwiLyoqXG4gKiBCcmVha3BvaW50cyAmIE1lZGlhIFF1ZXJpZXNcbiAqL1xuXG4vLyBNb3N0IHVzZWQgYnJlYWtwb2ludHNcbiRicmVhay14aHVnZTogMTkyMHB4O1xuJGJyZWFrLWh1Z2U6IDE0NDBweDtcbiRicmVhay13aWRlOiAxMjgwcHg7XG4kYnJlYWsteGxhcmdlOiAxMDgwcHg7XG4kYnJlYWstbGFyZ2U6IDk2MHB4O1x0Ly8gYWRtaW4gc2lkZWJhciBhdXRvIGZvbGRzXG4kYnJlYWstbWVkaXVtOiA3ODJweDtcdC8vIGFkbWluYmFyIGdvZXMgYmlnXG4kYnJlYWstc21hbGw6IDYwMHB4O1xuJGJyZWFrLW1vYmlsZTogNDgwcHg7XG4kYnJlYWstem9vbWVkLWluOiAyODBweDtcblxuLy8gQWxsIG1lZGlhIHF1ZXJpZXMgY3VycmVudGx5IGluIFdvcmRQcmVzczpcbi8vXG4vLyBtaW4td2lkdGg6IDIwMDBweFxuLy8gbWluLXdpZHRoOiAxNjgwcHhcbi8vIG1pbi13aWR0aDogMTI1MHB4XG4vLyBtYXgtd2lkdGg6IDExMjBweCAqXG4vLyBtYXgtd2lkdGg6IDEwMDBweFxuLy8gbWluLXdpZHRoOiA3NjlweCBhbmQgbWF4LXdpZHRoOiAxMDAwcHhcbi8vIG1heC13aWR0aDogOTYwcHggKlxuLy8gbWF4LXdpZHRoOiA5MDBweFxuLy8gbWF4LXdpZHRoOiA4NTBweFxuLy8gbWluLXdpZHRoOiA4MDBweCBhbmQgbWF4LXdpZHRoOiAxNDk5cHhcbi8vIG1heC13aWR0aDogODAwcHhcbi8vIG1heC13aWR0aDogNzk5cHhcbi8vIG1heC13aWR0aDogNzgycHggKlxuLy8gbWF4LXdpZHRoOiA3NjhweFxuLy8gbWF4LXdpZHRoOiA2NDBweCAqXG4vLyBtYXgtd2lkdGg6IDYwMHB4ICpcbi8vIG1heC13aWR0aDogNTIwcHhcbi8vIG1heC13aWR0aDogNTAwcHhcbi8vIG1heC13aWR0aDogNDgwcHggKlxuLy8gbWF4LXdpZHRoOiA0MDBweCAqXG4vLyBtYXgtd2lkdGg6IDM4MHB4XG4vLyBtYXgtd2lkdGg6IDMyMHB4ICpcbi8vXG4vLyBUaG9zZSBtYXJrZWQgKiBzZWVtIHRvIGJlIG1vcmUgY29tbW9ubHkgdXNlZCB0aGFuIHRoZSBvdGhlcnMuXG4vLyBMZXQncyB0cnkgYW5kIHVzZSBhcyBmZXcgb2YgdGhlc2UgYXMgcG9zc2libGUsIGFuZCBiZSBtaW5kZnVsIGFib3V0IGFkZGluZyBuZXcgb25lcywgc28gd2UgZG9uJ3QgbWFrZSB0aGUgc2l0dWF0aW9uIHdvcnNlXG4iLCIvKipcbiogIENvbnZlcnRzIGEgaGV4IHZhbHVlIGludG8gdGhlIHJnYiBlcXVpdmFsZW50LlxuKlxuKiBAcGFyYW0ge3N0cmluZ30gaGV4IC0gdGhlIGhleGFkZWNpbWFsIHZhbHVlIHRvIGNvbnZlcnRcbiogQHJldHVybiB7c3RyaW5nfSBjb21tYSBzZXBhcmF0ZWQgcmdiIHZhbHVlc1xuKi9cblxuQHVzZSBcInNhc3M6Y29sb3JcIjtcbkB1c2UgXCJzYXNzOm1ldGFcIjtcblxuQGZ1bmN0aW9uIGhleC10by1yZ2IoJGhleCkge1xuXHQvKlxuXHQgKiBUT0RPOiBgY29sb3Iue3JlZHxncmVlbnxibHVlfWAgd2lsbCB0cmlnZ2VyIGEgZGVwcmVjYXRpb24gd2FybmluZyBpbiBEYXJ0IFNhc3MsXG5cdCAqIGJ1dCB0aGUgU2FzcyB1c2VkIGJ5IHRoZSBHdXRlbmJlcmcgcHJvamVjdCBkb2Vzbid0IHN1cHBvcnQgYGNvbG9yLmNoYW5uZWwoKWAgeWV0LFxuXHQgKiBzbyB3ZSBjYW4ndCBtaWdyYXRlIHRvIGl0IGF0IHRoaXMgdGltZS5cblx0ICogSW4gdGhlIGZ1dHVyZSwgYWZ0ZXIgdGhlIEd1dGVuYmVyZyBwcm9qZWN0IGhhcyBiZWVuIGZ1bGx5IG1pZ3JhdGVkIHRvIERhcnQgU2Fzcyxcblx0ICogUmVtb3ZlIHRoaXMgY29uZGl0aW9uYWwgc3RhdGVtZW50IGFuZCB1c2Ugb25seSBgY29sb3IuY2hhbm5lbCgpYC5cblx0ICovXG5cdEBpZiBtZXRhLmZ1bmN0aW9uLWV4aXN0cyhcImNoYW5uZWxcIiwgXCJjb2xvclwiKSB7XG5cdFx0QHJldHVybiBjb2xvci5jaGFubmVsKCRoZXgsIFwicmVkXCIpLCBjb2xvci5jaGFubmVsKCRoZXgsIFwiZ3JlZW5cIiksIGNvbG9yLmNoYW5uZWwoJGhleCwgXCJibHVlXCIpO1xuXHR9IEBlbHNlIHtcblx0XHRAcmV0dXJuIGNvbG9yLnJlZCgkaGV4KSwgY29sb3IuZ3JlZW4oJGhleCksIGNvbG9yLmJsdWUoJGhleCk7XG5cdH1cbn1cbiIsIi8qKlxuICogTG9uZyBjb250ZW50IGZhZGUgbWl4aW5cbiAqXG4gKiBDcmVhdGVzIGEgZmFkaW5nIG92ZXJsYXkgdG8gc2lnbmlmeSB0aGF0IHRoZSBjb250ZW50IGlzIGxvbmdlclxuICogdGhhbiB0aGUgc3BhY2UgYWxsb3dzLlxuICovXG5cbkBtaXhpbiBsb25nLWNvbnRlbnQtZmFkZSgkZGlyZWN0aW9uOiByaWdodCwgJHNpemU6IDIwJSwgJGNvbG9yOiAjZmZmLCAkZWRnZTogMCwgJHotaW5kZXg6IGZhbHNlKSB7XG5cdGNvbnRlbnQ6IFwiXCI7XG5cdGRpc3BsYXk6IGJsb2NrO1xuXHRwb3NpdGlvbjogYWJzb2x1dGU7XG5cdC13ZWJraXQtdG91Y2gtY2FsbG91dDogbm9uZTtcblx0LXdlYmtpdC11c2VyLXNlbGVjdDogbm9uZTtcblx0LWtodG1sLXVzZXItc2VsZWN0OiBub25lO1xuXHQtbW96LXVzZXItc2VsZWN0OiBub25lO1xuXHQtbXMtdXNlci1zZWxlY3Q6IG5vbmU7XG5cdHVzZXItc2VsZWN0OiBub25lO1xuXHRwb2ludGVyLWV2ZW50czogbm9uZTtcblxuXHRAaWYgJHotaW5kZXgge1xuXHRcdHotaW5kZXg6ICR6LWluZGV4O1xuXHR9XG5cblx0QGlmICRkaXJlY3Rpb24gPT0gXCJib3R0b21cIiB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KHRvIHRvcCwgdHJhbnNwYXJlbnQsICRjb2xvciA5MCUpO1xuXHRcdGxlZnQ6ICRlZGdlO1xuXHRcdHJpZ2h0OiAkZWRnZTtcblx0XHR0b3A6ICRlZGdlO1xuXHRcdGJvdHRvbTogY2FsYygxMDAlIC0gJHNpemUpO1xuXHRcdHdpZHRoOiBhdXRvO1xuXHR9XG5cblx0QGlmICRkaXJlY3Rpb24gPT0gXCJ0b3BcIiB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KHRvIGJvdHRvbSwgdHJhbnNwYXJlbnQsICRjb2xvciA5MCUpO1xuXHRcdHRvcDogY2FsYygxMDAlIC0gJHNpemUpO1xuXHRcdGxlZnQ6ICRlZGdlO1xuXHRcdHJpZ2h0OiAkZWRnZTtcblx0XHRib3R0b206ICRlZGdlO1xuXHRcdHdpZHRoOiBhdXRvO1xuXHR9XG5cblx0QGlmICRkaXJlY3Rpb24gPT0gXCJsZWZ0XCIge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCh0byBsZWZ0LCB0cmFuc3BhcmVudCwgJGNvbG9yIDkwJSk7XG5cdFx0dG9wOiAkZWRnZTtcblx0XHRsZWZ0OiAkZWRnZTtcblx0XHRib3R0b206ICRlZGdlO1xuXHRcdHJpZ2h0OiBhdXRvO1xuXHRcdHdpZHRoOiAkc2l6ZTtcblx0XHRoZWlnaHQ6IGF1dG87XG5cdH1cblxuXHRAaWYgJGRpcmVjdGlvbiA9PSBcInJpZ2h0XCIge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCh0byByaWdodCwgdHJhbnNwYXJlbnQsICRjb2xvciA5MCUpO1xuXHRcdHRvcDogJGVkZ2U7XG5cdFx0Ym90dG9tOiAkZWRnZTtcblx0XHRyaWdodDogJGVkZ2U7XG5cdFx0bGVmdDogYXV0bztcblx0XHR3aWR0aDogJHNpemU7XG5cdFx0aGVpZ2h0OiBhdXRvO1xuXHR9XG59XG4iLCJAdXNlIFwiLi9taXhpbnNcIjtcbkB1c2UgXCIuL2Z1bmN0aW9uc1wiO1xuQHVzZSBcIi4vY29sb3JzXCI7XG5cbi8vIEl0IGlzIGltcG9ydGFudCB0byBpbmNsdWRlIHRoZXNlIHN0eWxlcyBpbiBhbGwgYnVpbHQgc3R5bGVzaGVldHMuXG4vLyBUaGlzIGFsbG93cyB0byBDU1MgdmFyaWFibGVzIHBvc3QgQ1NTIHBsdWdpbiB0byBnZW5lcmF0ZSBmYWxsYmFja3MuXG4vLyBJdCBhbHNvIHByb3ZpZGVzIGRlZmF1bHQgQ1NTIHZhcmlhYmxlcyBmb3IgbnBtIHBhY2thZ2UgY29uc3VtZXJzLlxuOnJvb3Qge1xuXHQtLXdwLWJsb2NrLXN5bmNlZC1jb2xvcjogIzdhMDBkZjtcblx0LS13cC1ibG9jay1zeW5jZWQtY29sb3ItLXJnYjogI3tmdW5jdGlvbnMuaGV4LXRvLXJnYigjN2EwMGRmKX07XG5cdC8vIFRoaXMgQ1NTIHZhcmlhYmxlIGlzIG5vdCB1c2VkIGluIEd1dGVuYmVyZyBwcm9qZWN0LFxuXHQvLyBidXQgaXMgbWFpbnRhaW5lZCBmb3IgYmFja3dhcmRzIGNvbXBhdGliaWxpdHkuXG5cdC0td3AtYm91bmQtYmxvY2stY29sb3I6IHZhcigtLXdwLWJsb2NrLXN5bmNlZC1jb2xvcik7XG5cdC0td3AtZWRpdG9yLWNhbnZhcy1iYWNrZ3JvdW5kOiAje2NvbG9ycy4kZ3JheS0zMDB9O1xuXHRAaW5jbHVkZSBtaXhpbnMuYWRtaW4tc2NoZW1lKCMwMDdjYmEpO1xufVxuIiwiQHVzZSBcInNhc3M6Y29sb3JcIjtcbkB1c2UgXCJAd29yZHByZXNzL2Jhc2Utc3R5bGVzL21peGluc1wiIGFzICo7XG5AdXNlIFwiQHdvcmRwcmVzcy9iYXNlLXN0eWxlcy92YXJpYWJsZXNcIiBhcyAqO1xuQHVzZSBcIkB3b3JkcHJlc3MvYmFzZS1zdHlsZXMvY29sb3JzXCIgYXMgKjtcbkB1c2UgXCJAd29yZHByZXNzL2Jhc2Utc3R5bGVzL2RlZmF1bHQtY3VzdG9tLXByb3BlcnRpZXNcIiBhcyAqO1xuXG4vLyBIZXJlIHdlIGV4dGVuZCB0aGUgbW9kYWwgc3R5bGVzIHRvIGJlIHRpZ2h0ZXIsIGFuZCB0byB0aGUgY2VudGVyLiBCZWNhdXNlIHRoZSBwYWxldHRlIHVzZXMgdGhlIG1vZGFsIGFzIGEgY29udGFpbmVyLlxuLndvcmtmbG93cy13b3JrZmxvdy1tZW51IHtcblx0Ym9yZGVyLXJhZGl1czogJGdyaWQtdW5pdC0wNTtcblx0d2lkdGg6IGNhbGMoMTAwJSAtICN7JGdyaWQtdW5pdC00MH0pO1xuXHRtYXJnaW46IGF1dG87XG5cdG1heC13aWR0aDogNDAwcHg7XG5cdHBvc2l0aW9uOiByZWxhdGl2ZTtcblx0dG9wOiBjYWxjKDUlICsgI3skaGVhZGVyLWhlaWdodH0pO1xuXG5cdEBpbmNsdWRlIGJyZWFrLXNtYWxsKCkge1xuXHRcdHRvcDogY2FsYygxMCUgKyAjeyRoZWFkZXItaGVpZ2h0fSk7XG5cdH1cblxuXHQuY29tcG9uZW50cy1tb2RhbF9fY29udGVudCB7XG5cdFx0bWFyZ2luOiAwO1xuXHRcdHBhZGRpbmc6IDA7XG5cdH1cbn1cblxuLndvcmtmbG93cy13b3JrZmxvdy1tZW51X19vdmVybGF5IHtcblx0ZGlzcGxheTogYmxvY2s7XG5cdGFsaWduLWl0ZW1zOiBzdGFydDtcbn1cblxuLndvcmtmbG93cy13b3JrZmxvdy1tZW51X19oZWFkZXIge1xuXHRwYWRkaW5nOiAwICRncmlkLXVuaXQtMjA7XG59XG5cbi53b3JrZmxvd3Mtd29ya2Zsb3ctbWVudV9faGVhZGVyLXNlYXJjaC1pY29uIHtcblx0JjpkaXIobHRyKSB7XG5cdFx0dHJhbnNmb3JtOiBzY2FsZVgoLTEpO1xuXHR9XG59XG5cbi53b3JrZmxvd3Mtd29ya2Zsb3ctbWVudV9fY29udGFpbmVyIHtcblx0Ly8gdGhlIHN0eWxlIGhlcmUgaXMgYSBoYWNrIHRvIGZvcmNlIHNhZmFyaSB0byByZXBhaW50IHRvIGF2b2lkIGEgc3R5bGUgZ2xpdGNoXG5cdHdpbGwtY2hhbmdlOiB0cmFuc2Zvcm07XG5cblx0Jjpmb2N1cyB7XG5cdFx0b3V0bGluZTogbm9uZTtcblx0fVxuXG5cdFtjbWRrLWlucHV0XSB7XG5cdFx0Ym9yZGVyOiBub25lO1xuXHRcdHdpZHRoOiAxMDAlO1xuXHRcdHBhZGRpbmc6ICRncmlkLXVuaXQtMjAgJGdyaWQtdW5pdC0wNTtcblx0XHRvdXRsaW5lOiBub25lO1xuXHRcdGNvbG9yOiAkZ3JheS05MDA7XG5cdFx0bWFyZ2luOiAwO1xuXHRcdGZvbnQtc2l6ZTogMTVweDtcblx0XHRsaW5lLWhlaWdodDogMjhweDtcblx0XHRib3JkZXItcmFkaXVzOiAwO1xuXG5cdFx0Jjo6cGxhY2Vob2xkZXIge1xuXHRcdFx0Y29sb3I6ICRncmF5LTcwMDtcblx0XHR9XG5cblx0XHQmOmZvY3VzIHtcblx0XHRcdGJveC1zaGFkb3c6IG5vbmU7XG5cdFx0XHRvdXRsaW5lOiBub25lO1xuXHRcdH1cblx0fVxuXG5cdFtjbWRrLWl0ZW1dIHtcblx0XHRib3JkZXItcmFkaXVzOiAkcmFkaXVzLXNtYWxsO1xuXHRcdGN1cnNvcjogcG9pbnRlcjtcblx0XHRkaXNwbGF5OiBmbGV4O1xuXHRcdGFsaWduLWl0ZW1zOiBjZW50ZXI7XG5cdFx0Y29sb3I6ICRncmF5LTkwMDtcblx0XHRmb250LXNpemU6ICRkZWZhdWx0LWZvbnQtc2l6ZTtcblxuXHRcdCZbYXJpYS1zZWxlY3RlZD1cInRydWVcIl0sXG5cdFx0JjphY3RpdmUge1xuXHRcdFx0YmFja2dyb3VuZDogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXHRcdFx0Y29sb3I6ICR3aGl0ZTtcblx0XHR9XG5cblx0XHQmW2FyaWEtZGlzYWJsZWQ9XCJ0cnVlXCJdIHtcblx0XHRcdGNvbG9yOiAkZ3JheS02MDA7XG5cdFx0XHRjdXJzb3I6IG5vdC1hbGxvd2VkO1xuXHRcdH1cblxuXHRcdD4gZGl2IHtcblx0XHRcdG1pbi1oZWlnaHQ6ICRidXR0b24tc2l6ZS1uZXh0LWRlZmF1bHQtNDBweDtcblx0XHRcdHBhZGRpbmc6ICRncmlkLXVuaXQtMDU7XG5cdFx0XHRwYWRkaW5nLWxlZnQ6ICRncmlkLXVuaXQtMjA7XG5cdFx0fVxuXHR9XG5cblx0W2NtZGstcm9vdF0gPiBbY21kay1saXN0XSB7XG5cdFx0bWF4LWhlaWdodDogJHBhbGV0dGUtbWF4LWhlaWdodDsgLy8gU3BlY2lmaWMgdG8gbm90IGhhdmUgd29ya2Zsb3dzIG92ZXJmbG93IG9kZGx5LlxuXHRcdG92ZXJmbG93OiBhdXRvO1xuXG5cdFx0Ly8gRW5zdXJlcyB0aGVyZSBpcyBhbHdheXMgcGFkZGluZyBib3R0b20gb24gdGhlIGxhc3QgZ3JvdXAsIHdoZW4gdGhlcmUgYXJlIHdvcmtmbG93cy5cblx0XHQmXG5cdFx0W2NtZGstbGlzdC1zaXplcl0gPiBbY21kay1ncm91cF06bGFzdC1jaGlsZFxuXHRcdFtjbWRrLWdyb3VwLWl0ZW1zXTpub3QoOmVtcHR5KSB7XG5cdFx0XHRwYWRkaW5nLWJvdHRvbTogJGdyaWQtdW5pdC0xMDtcblx0XHR9XG5cblx0XHQmIFtjbWRrLWxpc3Qtc2l6ZXJdID4gW2NtZGstZ3JvdXBdID4gW2NtZGstZ3JvdXAtaXRlbXNdOm5vdCg6ZW1wdHkpIHtcblx0XHRcdHBhZGRpbmc6IDAgJGdyaWQtdW5pdC0xMDtcblx0XHR9XG5cdH1cblxuXHRbY21kay1lbXB0eV0ge1xuXHRcdGRpc3BsYXk6IGZsZXg7XG5cdFx0YWxpZ24taXRlbXM6IGNlbnRlcjtcblx0XHRqdXN0aWZ5LWNvbnRlbnQ6IGNlbnRlcjtcblx0XHR3aGl0ZS1zcGFjZTogcHJlLXdyYXA7XG5cdFx0Y29sb3I6ICRncmF5LTkwMDtcblx0XHRwYWRkaW5nOiAkZ3JpZC11bml0LTEwIDAgJGdyaWQtdW5pdC00MDtcblx0fVxuXG5cdFtjbWRrLWxvYWRpbmddIHtcblx0XHRwYWRkaW5nOiAkZ3JpZC11bml0LTIwO1xuXHR9XG5cblx0W2NtZGstbGlzdC1zaXplcl0ge1xuXHRcdHBvc2l0aW9uOiByZWxhdGl2ZTtcblx0fVxufVxuXG4ud29ya2Zsb3dzLXdvcmtmbG93LW1lbnVfX2l0ZW0gc3BhbiB7XG5cdC8vIEVuc3VyZSB3b3JrZmxvd3MgZG8gbm90IHJ1biBvZmYgdGhlIGVkZ2UgKGdyZWF0IGZvciBwb3N0IHRpdGxlcykuXG5cdGRpc3BsYXk6IGlubGluZS1ibG9jaztcblx0b3ZlcmZsb3c6IGhpZGRlbjtcblx0dGV4dC1vdmVyZmxvdzogZWxsaXBzaXM7XG5cdHdoaXRlLXNwYWNlOiBub3dyYXA7XG59XG5cbi53b3JrZmxvd3Mtd29ya2Zsb3ctbWVudV9faXRlbSBtYXJrIHtcblx0Y29sb3I6IGluaGVyaXQ7XG5cdGJhY2tncm91bmQ6IHVuc2V0O1xuXHRmb250LXdlaWdodDogNjAwO1xufVxuXG4ud29ya2Zsb3dzLXdvcmtmbG93LW1lbnVfX291dHB1dCB7XG5cdHBhZGRpbmc6ICRncmlkLXVuaXQtMjA7XG59XG5cbi53b3JrZmxvd3Mtd29ya2Zsb3ctbWVudV9fb3V0cHV0LWhlYWRlciB7XG5cdG1hcmdpbi1ib3R0b206ICRncmlkLXVuaXQtMjA7XG5cdGJvcmRlci1ib3R0b206IDFweCBzb2xpZCAkZ3JheS0zMDA7XG5cdHBhZGRpbmctYm90dG9tOiAkZ3JpZC11bml0LTEwO1xuXG5cdGgzIHtcblx0XHRtYXJnaW46IDAgMCAkZ3JpZC11bml0LTA1O1xuXHRcdGZvbnQtc2l6ZTogMTZweDtcblx0XHRmb250LXdlaWdodDogNjAwO1xuXHRcdGNvbG9yOiAkZ3JheS05MDA7XG5cdH1cbn1cblxuLndvcmtmbG93cy13b3JrZmxvdy1tZW51X19vdXRwdXQtaGludCB7XG5cdG1hcmdpbjogMDtcblx0Zm9udC1zaXplOiAxMnB4O1xuXHRjb2xvcjogJGdyYXktNzAwO1xufVxuXG4ud29ya2Zsb3dzLXdvcmtmbG93LW1lbnVfX291dHB1dC1jb250ZW50IHtcblx0bWF4LWhlaWdodDogNDAwcHg7XG5cdG92ZXJmbG93OiBhdXRvO1xuXG5cdHByZSB7XG5cdFx0bWFyZ2luOiAwO1xuXHRcdHBhZGRpbmc6ICRncmlkLXVuaXQtMTU7XG5cdFx0YmFja2dyb3VuZDogJGdyYXktMTAwO1xuXHRcdGJvcmRlci1yYWRpdXM6ICRyYWRpdXMtc21hbGw7XG5cdFx0Zm9udC1zaXplOiAxMnB4O1xuXHRcdGxpbmUtaGVpZ2h0OiAxLjU7XG5cdFx0d2hpdGUtc3BhY2U6IHByZS13cmFwO1xuXHRcdHdvcmQtYnJlYWs6IGJyZWFrLXdvcmQ7XG5cdFx0Y29sb3I6ICRncmF5LTkwMDtcblx0fVxufVxuXG4ud29ya2Zsb3dzLXdvcmtmbG93LW1lbnVfX291dHB1dC1lcnJvciB7XG5cdHBhZGRpbmc6ICRncmlkLXVuaXQtMTU7XG5cdGJhY2tncm91bmQ6ICRncmF5LTIwMDtcblx0Ym9yZGVyOiAxcHggc29saWQgI3tjb2xvci5hZGp1c3QoICRhbGVydC1yZWQsICRsaWdodG5lc3M6IC0xMCUgKX07XG5cdGJvcmRlci1yYWRpdXM6ICRyYWRpdXMtc21hbGw7XG5cdGNvbG9yOiAkYWxlcnQtcmVkO1xuXG5cdHAge1xuXHRcdG1hcmdpbjogMDtcblx0XHRmb250LXNpemU6IDEzcHg7XG5cdH1cbn1cblxuLndvcmtmbG93cy13b3JrZmxvdy1tZW51X19leGVjdXRpbmcge1xuXHRwYWRkaW5nOiAkZ3JpZC11bml0LTMwICRncmlkLXVuaXQtMjA7XG5cdGNvbG9yOiAkZ3JheS03MDA7XG5cdGZvbnQtc2l6ZTogMTRweDtcbn1cbiJdfQ== */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css));
var { withIgnoreIMEEvents } = unlock(import_components.privateApis);
var EMPTY_ARRAY = [];
var inputLabel = (0, import_i18n.__)("Run abilities and workflows");
function WorkflowInput({ isOpen, search, setSearch, abilities }) {
  const workflowMenuInput = (0, import_element2.useRef)();
  const _value = P((state) => state.value);
  const selectedItemId = (0, import_element2.useMemo)(() => {
    const ability = abilities.find((a) => a.label === _value);
    return ability?.name;
  }, [_value, abilities]);
  (0, import_element2.useEffect)(() => {
    if (isOpen) {
      workflowMenuInput.current.focus();
    }
  }, [isOpen]);
  return /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
    _e.Input,
    {
      ref: workflowMenuInput,
      value: search,
      onValueChange: setSearch,
      placeholder: inputLabel,
      "aria-activedescendant": selectedItemId
    }
  );
}
function WorkflowMenu() {
  const { registerShortcut } = (0, import_data.useDispatch)(import_keyboard_shortcuts.store);
  const [search, setSearch] = (0, import_element2.useState)("");
  const [isOpen, setIsOpen] = (0, import_element2.useState)(false);
  const [abilityOutput, setAbilityOutput] = (0, import_element2.useState)(null);
  const [isExecuting, setIsExecuting] = (0, import_element2.useState)(false);
  const containerRef = (0, import_element2.useRef)();
  const abilities = (0, import_data.useSelect)((select) => {
    const allAbilities = select(abilitiesStore).getAbilities();
    return allAbilities || EMPTY_ARRAY;
  }, []);
  const filteredAbilities = (0, import_element2.useMemo)(() => {
    if (!search) {
      return abilities;
    }
    const searchLower = search.toLowerCase();
    return abilities.filter(
      (ability) => ability.label?.toLowerCase().includes(searchLower) || ability.name?.toLowerCase().includes(searchLower)
    );
  }, [abilities, search]);
  (0, import_element2.useEffect)(() => {
    if (abilityOutput && containerRef.current) {
      containerRef.current.focus();
    }
  }, [abilityOutput]);
  (0, import_element2.useEffect)(() => {
    registerShortcut({
      name: "core/workflows",
      category: "global",
      description: (0, import_i18n.__)("Open the workflow palette."),
      keyCombination: {
        modifier: "primary",
        character: "j"
      }
    });
  }, [registerShortcut]);
  (0, import_keyboard_shortcuts.useShortcut)(
    "core/workflows",
    /** @type {import('react').KeyboardEventHandler} */
    withIgnoreIMEEvents((event) => {
      if (event.defaultPrevented) {
        return;
      }
      event.preventDefault();
      setIsOpen(!isOpen);
    }),
    {
      bindGlobal: true
    }
  );
  const closeAndReset = () => {
    setSearch("");
    setIsOpen(false);
    setAbilityOutput(null);
    setIsExecuting(false);
  };
  const goBack = () => {
    setAbilityOutput(null);
    setIsExecuting(false);
    setSearch("");
  };
  const handleExecuteAbility = async (ability) => {
    setIsExecuting(true);
    try {
      const result = await executeAbility(ability.name);
      setAbilityOutput({
        name: ability.name,
        label: ability?.label || ability.name,
        description: ability?.description || "",
        success: true,
        data: result
      });
    } catch (error) {
      setAbilityOutput({
        name: ability.name,
        label: ability?.label || ability.name,
        description: ability?.description || "",
        success: false,
        error: error.message || String(error)
      });
    } finally {
      setIsExecuting(false);
    }
  };
  const onContainerKeyDown = (event) => {
    if (abilityOutput && (event.key === "Escape" || event.key === "Backspace" || event.key === "Delete")) {
      event.preventDefault();
      event.stopPropagation();
      goBack();
    }
  };
  if (!isOpen) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
    import_components.Modal,
    {
      className: "workflows-workflow-menu",
      overlayClassName: "workflows-workflow-menu__overlay",
      onRequestClose: abilityOutput ? goBack : closeAndReset,
      __experimentalHideHeader: true,
      contentLabel: (0, import_i18n.__)("Workflow palette"),
      children: /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
        "div",
        {
          className: "workflows-workflow-menu__container",
          onKeyDown: withIgnoreIMEEvents(onContainerKeyDown),
          ref: containerRef,
          tabIndex: -1,
          role: "presentation",
          children: abilityOutput ? /* @__PURE__ */ (0, import_jsx_runtime17.jsxs)("div", { className: "workflows-workflow-menu__output", children: [
            /* @__PURE__ */ (0, import_jsx_runtime17.jsxs)("div", { className: "workflows-workflow-menu__output-header", children: [
              /* @__PURE__ */ (0, import_jsx_runtime17.jsx)("h3", { children: abilityOutput.label }),
              abilityOutput.description && /* @__PURE__ */ (0, import_jsx_runtime17.jsx)("p", { className: "workflows-workflow-menu__output-hint", children: abilityOutput.description })
            ] }),
            /* @__PURE__ */ (0, import_jsx_runtime17.jsx)("div", { className: "workflows-workflow-menu__output-content", children: abilityOutput.success ? /* @__PURE__ */ (0, import_jsx_runtime17.jsx)("pre", { children: JSON.stringify(
              abilityOutput.data,
              null,
              2
            ) }) : /* @__PURE__ */ (0, import_jsx_runtime17.jsx)("div", { className: "workflows-workflow-menu__output-error", children: /* @__PURE__ */ (0, import_jsx_runtime17.jsx)("p", { children: abilityOutput.error }) }) })
          ] }) : /* @__PURE__ */ (0, import_jsx_runtime17.jsxs)(_e, { label: inputLabel, shouldFilter: false, children: [
            /* @__PURE__ */ (0, import_jsx_runtime17.jsxs)(import_components.__experimentalHStack, { className: "workflows-workflow-menu__header", children: [
              /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
                icon_default,
                {
                  className: "workflows-workflow-menu__header-search-icon",
                  icon: search_default
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
                WorkflowInput,
                {
                  search,
                  setSearch,
                  isOpen,
                  abilities
                }
              )
            ] }),
            /* @__PURE__ */ (0, import_jsx_runtime17.jsxs)(_e.List, { label: (0, import_i18n.__)("Workflow suggestions"), children: [
              isExecuting && /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
                import_components.__experimentalHStack,
                {
                  className: "workflows-workflow-menu__executing",
                  align: "center",
                  children: (0, import_i18n.__)("Executing ability\u2026")
                }
              ),
              !isExecuting && search && filteredAbilities.length === 0 && /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(_e.Empty, { children: (0, import_i18n.__)("No results found.") }),
              !isExecuting && filteredAbilities.length > 0 && /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(_e.Group, { children: filteredAbilities.map((ability) => /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
                _e.Item,
                {
                  value: ability.label,
                  className: "workflows-workflow-menu__item",
                  onSelect: () => handleExecuteAbility(ability),
                  id: ability.name,
                  children: /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_components.__experimentalHStack, { alignment: "left", children: /* @__PURE__ */ (0, import_jsx_runtime17.jsx)("span", { children: /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
                    import_components.TextHighlight,
                    {
                      text: ability.label,
                      highlight: search
                    }
                  ) }) })
                },
                ability.name
              )) })
            ] })
          ] })
        }
      )
    }
  );
}

// packages/workflow/build-module/index.js
var root = document.createElement("div");
document.body.appendChild(root);
(0, import_element3.createRoot)(root).render((0, import_element3.createElement)(WorkflowMenu));