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

// vendor-external:react/jsx-runtime
var require_jsx_runtime = __commonJS({
  "vendor-external:react/jsx-runtime"(exports, module) {
    module.exports = window.ReactJSXRuntime;
  }
});

// packages/widget-primitives/build-module/tools/get-lazy-widget-component/get-lazy-widget-component.mjs
var import_element = __toESM(require_element(), 1);
function isValidWidgetModule(module) {
  return typeof module === "object" && module !== null && "default" in module && typeof module.default === "function";
}
var componentCache = /* @__PURE__ */ new Map();
function getLazyWidgetComponent(renderModule, resolveWidgetModule) {
  const cached = componentCache.get(renderModule);
  if (cached) {
    return cached;
  }
  const lazyComponent = (0, import_element.lazy)(async () => {
    const module = await resolveWidgetModule(renderModule);
    if (!isValidWidgetModule(module)) {
      throw new Error(`Invalid widget module: ${renderModule}`);
    }
    return module;
  });
  componentCache.set(renderModule, lazyComponent);
  return lazyComponent;
}

// packages/widget-primitives/build-module/widget-host/widget-actions-collector.mjs
var import_element3 = __toESM(require_element(), 1);

// packages/widget-primitives/build-module/widget-host/widget-host.mjs
var import_element2 = __toESM(require_element(), 1);
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
var WidgetHostContext = (0, import_element2.createContext)({});
function WidgetHostProvider({
  value,
  children
}) {
  const inherited = (0, import_element2.useContext)(WidgetHostContext);
  const merged = (0, import_element2.useMemo)(
    () => ({ ...inherited, ...value }),
    [inherited, value]
  );
  return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(WidgetHostContext.Provider, { value: merged, children });
}
function useWidgetHost() {
  return (0, import_element2.useContext)(WidgetHostContext);
}

// packages/widget-primitives/build-module/widget-host/widget-actions-collector.mjs
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
var WidgetActionsCollectorContext = (0, import_element3.createContext)(null);
function useWidgetActionsCollector() {
  return (0, import_element3.useContext)(WidgetActionsCollectorContext);
}
function joinDeclarations(declarations) {
  const byId = /* @__PURE__ */ new Map();
  for (const actions of declarations.values()) {
    for (const action of actions) {
      byId.set(action.id, action);
    }
  }
  return [...byId.values()];
}
function WidgetActionsCollector({
  children
}) {
  const hostDeclare = useWidgetHost().actions?.declare;
  const [declarations] = (0, import_element3.useState)(
    () => /* @__PURE__ */ new Map()
  );
  const value = (0, import_element3.useMemo)(
    () => ({
      hosted: !!hostDeclare,
      declare: (callId, actions) => {
        if (actions.length > 0) {
          declarations.set(callId, actions);
        } else {
          declarations.delete(callId);
        }
        hostDeclare?.(joinDeclarations(declarations));
      }
    }),
    [hostDeclare, declarations]
  );
  return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(WidgetActionsCollectorContext.Provider, { value, children });
}

// packages/widget-primitives/build-module/components/widget-render/widget-render.mjs
var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
function WidgetRender({
  widgetType,
  attributes,
  setAttributes,
  resolveWidgetModule
}) {
  const WidgetComponent = getLazyWidgetComponent(
    widgetType.renderModule,
    resolveWidgetModule
  );
  return /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(WidgetActionsCollector, { children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
    WidgetComponent,
    {
      attributes,
      setAttributes
    }
  ) });
}

// packages/widget-primitives/build-module/hooks/use-widget-actions.mjs
var import_element5 = __toESM(require_element(), 1);

// packages/widget-primitives/build-module/widget-host/host-link.mjs
var import_element4 = __toESM(require_element(), 1);
var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
var HostLink = (0, import_element4.forwardRef)(
  function UnforwardedHostLink({ href, children, ...props }, ref) {
    const { links } = useWidgetHost();
    const { download, target } = props;
    const opensNewDocument = download !== void 0 && download !== false || /^_blank$/i.test(target ?? "");
    const path = links && !opensNewDocument ? links.match(href) : null;
    if (links && path !== null) {
      return /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(links.Link, { ref, path, ...props, children });
    }
    return /* @__PURE__ */ (0, import_jsx_runtime4.jsx)("a", { ref, href, ...props, children });
  }
);

// packages/widget-primitives/build-module/hooks/use-widget-actions.mjs
var NO_ACTIONS = [];
function isSameAction(a, b) {
  const left = a;
  const right = b;
  const keys = Object.keys(left);
  return keys.length === Object.keys(right).length && keys.every(
    (key) => Object.is(left[key], right[key]) || typeof left[key] === "function" && typeof right[key] === "function"
  );
}
function isSameList(a, b) {
  return a.length === b.length && a.every((action, index) => isSameAction(action, b[index]));
}
function useWidgetActions(actions) {
  const hostDeclare = useWidgetHost().actions?.declare;
  const collector = useWidgetActionsCollector();
  const callId = (0, import_element5.useId)();
  const declare = (0, import_element5.useMemo)(
    () => collector ? (next) => collector.declare(callId, next) : hostDeclare,
    [collector, hostDeclare, callId]
  );
  const latestRef = (0, import_element5.useRef)(actions);
  const declaredRef = (0, import_element5.useRef)(null);
  (0, import_element5.useLayoutEffect)(() => {
    latestRef.current = actions;
    if (!declare || declaredRef.current && isSameList(declaredRef.current, actions)) {
      return;
    }
    declaredRef.current = actions;
    declare(
      actions.map((action) => {
        if (!("callback" in action)) {
          return action;
        }
        return {
          ...action,
          callback: () => {
            const current = latestRef.current.find(
              ({ id }) => id === action.id
            );
            return current && "callback" in current ? current.callback() : void 0;
          }
        };
      })
    );
  });
  (0, import_element5.useLayoutEffect)(
    () => () => {
      declaredRef.current = null;
      declare?.(NO_ACTIONS);
    },
    [declare]
  );
  return collector ? collector.hosted : !!hostDeclare;
}

// packages/widget-primitives/build-module/hooks/use-widget-types.mjs
var import_element6 = __toESM(require_element(), 1);

// packages/widget-primitives/build-module/field-types/field-types.mjs
var FIELD_TYPE_NAME_PATTERN = /^[a-z][a-z0-9-]*(\/[a-z][a-z0-9-]*)?$/;
var fieldTypes = /* @__PURE__ */ new Map();
function registerFieldType(fieldType) {
  if (!FIELD_TYPE_NAME_PATTERN.test(fieldType.name) || fieldTypes.has(fieldType.name)) {
    return void 0;
  }
  fieldTypes.set(fieldType.name, fieldType);
  return fieldType;
}
function resolveFields(fields) {
  return fields.map((field) => {
    const fieldType = field.type ? fieldTypes.get(field.type) : void 0;
    if (!fieldType) {
      return field;
    }
    const { name, baseType, isValid, ...fieldDefaults } = fieldType;
    const { type, isValid: fieldIsValid, ...rest } = field;
    return {
      ...fieldDefaults,
      ...rest,
      type: baseType,
      ...isValid || fieldIsValid ? { isValid: { ...isValid, ...fieldIsValid } } : {}
    };
  });
}

// packages/widget-primitives/build-module/icon-resolver/icon-resolver.mjs
var iconResolver;
function registerIconResolver(resolver) {
  if (iconResolver) {
    return void 0;
  }
  iconResolver = resolver;
  return resolver;
}
async function resolveIcon(reference) {
  if (!iconResolver) {
    return null;
  }
  try {
    return await iconResolver(reference);
  } catch {
    return null;
  }
}

// packages/widget-primitives/build-module/hooks/use-widget-types.mjs
var pendingIcon = (0, import_element6.createElement)("svg", {
  viewBox: "0 0 24 24"
});
function withRenderableIcons(actions, holdPending) {
  return actions.map(({ icon, ...action }) => {
    if ((0, import_element6.isValidElement)(icon)) {
      return { ...action, icon };
    }
    if (holdPending && typeof icon === "string") {
      return { ...action, icon: pendingIcon };
    }
    return action;
  });
}
function mergeAttributes(recordAttributes, moduleAttributes) {
  if (!recordAttributes) {
    return moduleAttributes;
  }
  if (!moduleAttributes) {
    return recordAttributes;
  }
  const moduleById = new Map(
    moduleAttributes.map(
      (attribute) => [attribute.id, attribute]
    )
  );
  const recordIds = new Set(
    recordAttributes.map((attribute) => attribute.id)
  );
  return [
    ...recordAttributes.map((attribute) => {
      const moduleAttribute = moduleById.get(attribute.id);
      if (!moduleAttribute?.isValid || !attribute.isValid) {
        return { ...moduleAttribute, ...attribute };
      }
      return {
        ...moduleAttribute,
        ...attribute,
        isValid: { ...moduleAttribute.isValid, ...attribute.isValid }
      };
    }),
    ...moduleAttributes.filter(
      (attribute) => !recordIds.has(attribute.id)
    )
  ];
}
var DEFAULT_API_VERSION = 1;
function recordOverlay(record) {
  return {
    name: record.name,
    renderModule: record.render_module ?? "",
    ...record.presentation ? { presentation: record.presentation } : {},
    ...record.category ? { category: record.category } : {},
    ...record.description ? { description: record.description } : {},
    ...record.help ? { help: record.help } : {},
    ...record.keywords ? { keywords: record.keywords } : {}
  };
}
function useWidgetTypes(records) {
  const [widgetTypes, setWidgetTypes] = (0, import_element6.useState)([]);
  const [isResolvingWidgetTypes, setIsResolvingWidgetTypes] = (0, import_element6.useState)(true);
  (0, import_element6.useEffect)(() => {
    if (records === null || records === void 0) {
      setIsResolvingWidgetTypes(true);
      return;
    }
    if (records.length === 0) {
      setWidgetTypes([]);
      setIsResolvingWidgetTypes(false);
      return;
    }
    let cancelled = false;
    setIsResolvingWidgetTypes(true);
    Promise.all(
      records.map(async (record) => {
        if (!record.widget_module) {
          if (!record.render_module) {
            return null;
          }
          return {
            apiVersion: DEFAULT_API_VERSION,
            title: record.title ?? record.name,
            ...record.attributes ? {
              attributes: resolveFields(
                record.attributes
              )
            } : {},
            ...record.icon ? { icon: pendingIcon } : {},
            ...record.actions ? {
              actions: withRenderableIcons(
                record.actions,
                true
              )
            } : {},
            ...recordOverlay(record)
          };
        }
        try {
          const module = await import(
            /* webpackIgnore: true */
            /* @vite-ignore */
            record.widget_module
          );
          if (!module?.default) {
            return null;
          }
          const metadata = module.default;
          const moduleIcon = (0, import_element6.isValidElement)(metadata.icon) ? metadata.icon : void 0;
          const icon = moduleIcon ?? (record.icon ? pendingIcon : void 0);
          const actions = record.actions ?? metadata.actions;
          const attributes = mergeAttributes(
            record.attributes,
            metadata.attributes
          );
          return {
            apiVersion: DEFAULT_API_VERSION,
            ...metadata,
            ...attributes ? { attributes: resolveFields(attributes) } : {},
            icon,
            /*
             * `title` is required:
             * - Server-side title wins
             * - Then the module's title
             * - Then the record's name as fallback
             */
            title: record.title ?? metadata.title ?? record.name,
            ...actions ? {
              actions: withRenderableIcons(
                actions,
                actions === record.actions
              )
            } : {},
            ...recordOverlay(record)
          };
        } catch {
          return null;
        }
      })
    ).then((results) => {
      if (cancelled) {
        return;
      }
      setWidgetTypes(
        results.filter((t) => t !== null)
      );
      setIsResolvingWidgetTypes(false);
      for (const record of records) {
        if (!record.icon) {
          continue;
        }
        void resolveIcon(record.icon).then((resolved) => {
          if (cancelled) {
            return;
          }
          setWidgetTypes(
            (prev) => prev.map((widgetType) => {
              if (widgetType.name !== record.name) {
                return widgetType;
              }
              if (resolved) {
                return { ...widgetType, icon: resolved };
              }
              return widgetType.icon === pendingIcon ? { ...widgetType, icon: void 0 } : widgetType;
            })
          );
        });
      }
      for (const record of records) {
        for (const action of record.actions ?? []) {
          if (typeof action.icon !== "string") {
            continue;
          }
          void resolveIcon(action.icon).then((resolved) => {
            if (cancelled) {
              return;
            }
            setWidgetTypes(
              (prev) => prev.map((type) => {
                if (type.name !== record.name) {
                  return type;
                }
                return {
                  ...type,
                  actions: type.actions?.map((entry) => {
                    if (entry.id !== action.id) {
                      return entry;
                    }
                    if (resolved) {
                      return {
                        ...entry,
                        icon: resolved
                      };
                    }
                    return entry.icon === pendingIcon ? { ...entry, icon: void 0 } : entry;
                  })
                };
              })
            );
          });
        }
      }
    });
    return () => {
      cancelled = true;
    };
  }, [records]);
  return [widgetTypes, isResolvingWidgetTypes];
}
export {
  HostLink,
  WidgetHostProvider,
  WidgetRender,
  registerFieldType,
  registerIconResolver,
  useWidgetActions,
  useWidgetHost,
  useWidgetTypes
};
