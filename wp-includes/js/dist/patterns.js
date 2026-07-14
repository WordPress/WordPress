var wp;
(wp ||= {}).patterns = (() => {
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

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // package-external:@wordpress/blocks
  var require_blocks = __commonJS({
    "package-external:@wordpress/blocks"(exports, module) {
      module.exports = window.wp.blocks;
    }
  });

  // package-external:@wordpress/core-data
  var require_core_data = __commonJS({
    "package-external:@wordpress/core-data"(exports, module) {
      module.exports = window.wp.coreData;
    }
  });

  // package-external:@wordpress/block-editor
  var require_block_editor = __commonJS({
    "package-external:@wordpress/block-editor"(exports, module) {
      module.exports = window.wp.blockEditor;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
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

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // vendor-external:react
  var require_react = __commonJS({
    "vendor-external:react"(exports, module) {
      module.exports = window.React;
    }
  });

  // package-external:@wordpress/a11y
  var require_a11y = __commonJS({
    "package-external:@wordpress/a11y"(exports, module) {
      module.exports = window.wp.a11y;
    }
  });

  // package-external:@wordpress/primitives
  var require_primitives = __commonJS({
    "package-external:@wordpress/primitives"(exports, module) {
      module.exports = window.wp.primitives;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/notices
  var require_notices = __commonJS({
    "package-external:@wordpress/notices"(exports, module) {
      module.exports = window.wp.notices;
    }
  });

  // package-external:@wordpress/html-entities
  var require_html_entities = __commonJS({
    "package-external:@wordpress/html-entities"(exports, module) {
      module.exports = window.wp.htmlEntities;
    }
  });

  // package-external:@wordpress/url
  var require_url = __commonJS({
    "package-external:@wordpress/url"(exports, module) {
      module.exports = window.wp.url;
    }
  });

  // packages/patterns/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    privateApis: () => privateApis,
    store: () => store
  });

  // packages/patterns/build-module/store/index.mjs
  var import_data2 = __toESM(require_data(), 1);

  // packages/patterns/build-module/store/reducer.mjs
  var import_data = __toESM(require_data(), 1);
  function isEditingPattern(state = {}, action) {
    if (action?.type === "SET_EDITING_PATTERN") {
      return {
        ...state,
        [action.clientId]: action.isEditing
      };
    }
    return state;
  }
  var reducer_default = (0, import_data.combineReducers)({
    isEditingPattern
  });

  // packages/patterns/build-module/store/actions.mjs
  var actions_exports = {};
  __export(actions_exports, {
    convertSyncedPatternToStatic: () => convertSyncedPatternToStatic,
    createPattern: () => createPattern,
    createPatternFromFile: () => createPatternFromFile,
    setEditingPattern: () => setEditingPattern
  });
  var import_blocks = __toESM(require_blocks(), 1);
  var import_core_data = __toESM(require_core_data(), 1);
  var import_block_editor = __toESM(require_block_editor(), 1);

  // packages/patterns/build-module/constants.mjs
  var PATTERN_TYPES = {
    theme: "pattern",
    user: "wp_block"
  };
  var PATTERN_DEFAULT_CATEGORY = "all-patterns";
  var PATTERN_USER_CATEGORY = "my-patterns";
  var EXCLUDED_PATTERN_SOURCES = [
    "core",
    "pattern-directory/core",
    "pattern-directory/featured"
  ];
  var PATTERN_SYNC_TYPES = {
    full: "fully",
    unsynced: "unsynced"
  };
  var PATTERN_OVERRIDES_BINDING_SOURCE = "core/pattern-overrides";

  // packages/patterns/build-module/store/actions.mjs
  var createPattern = (title, syncType, content, categories) => async ({ registry }) => {
    const meta = syncType === PATTERN_SYNC_TYPES.unsynced ? {
      wp_pattern_sync_status: syncType
    } : void 0;
    const reusableBlock = {
      title,
      content,
      status: "publish",
      meta,
      wp_pattern_category: categories
    };
    const updatedRecord = await registry.dispatch(import_core_data.store).saveEntityRecord("postType", "wp_block", reusableBlock);
    return updatedRecord;
  };
  var createPatternFromFile = (file, categories) => async ({ dispatch }) => {
    const fileContent = await file.text();
    let parsedContent;
    try {
      parsedContent = JSON.parse(fileContent);
    } catch {
      throw new Error("Invalid JSON file");
    }
    if (parsedContent.__file !== "wp_block" || !parsedContent.title || !parsedContent.content || typeof parsedContent.title !== "string" || typeof parsedContent.content !== "string" || parsedContent.syncStatus && typeof parsedContent.syncStatus !== "string") {
      throw new Error("Invalid pattern JSON file");
    }
    const pattern = await dispatch.createPattern(
      parsedContent.title,
      parsedContent.syncStatus,
      parsedContent.content,
      categories
    );
    return pattern;
  };
  var convertSyncedPatternToStatic = (clientId) => ({ registry }) => {
    const patternBlock = registry.select(import_block_editor.store).getBlock(clientId);
    const existingOverrides = patternBlock.attributes?.content;
    function cloneBlocksAndRemoveBindings(blocks) {
      return blocks.map((block) => {
        let metadata = block.attributes.metadata;
        if (metadata) {
          metadata = { ...metadata };
          delete metadata.id;
          delete metadata.bindings;
          if (existingOverrides?.[metadata.name]) {
            for (const [attributeName, value] of Object.entries(
              existingOverrides[metadata.name]
            )) {
              if (!(0, import_blocks.getBlockType)(block.name)?.attributes[attributeName]) {
                continue;
              }
              block.attributes[attributeName] = value;
            }
          }
        }
        return (0, import_blocks.cloneBlock)(
          block,
          {
            metadata: metadata && Object.keys(metadata).length > 0 ? metadata : void 0
          },
          cloneBlocksAndRemoveBindings(block.innerBlocks)
        );
      });
    }
    const patternInnerBlocks = registry.select(import_block_editor.store).getBlocks(patternBlock.clientId);
    registry.dispatch(import_block_editor.store).replaceBlocks(
      patternBlock.clientId,
      cloneBlocksAndRemoveBindings(patternInnerBlocks)
    );
  };
  function setEditingPattern(clientId, isEditing) {
    return {
      type: "SET_EDITING_PATTERN",
      clientId,
      isEditing
    };
  }

  // packages/patterns/build-module/store/constants.mjs
  var STORE_NAME = "core/patterns";

  // packages/patterns/build-module/store/selectors.mjs
  var selectors_exports = {};
  __export(selectors_exports, {
    isEditingPattern: () => isEditingPattern2
  });
  function isEditingPattern2(state, clientId) {
    return state.isEditingPattern[clientId];
  }

  // packages/patterns/build-module/lock-unlock.mjs
  var import_private_apis = __toESM(require_private_apis(), 1);
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/patterns"
  );

  // packages/patterns/build-module/store/index.mjs
  var storeConfig = {
    reducer: reducer_default
  };
  var store = (0, import_data2.createReduxStore)(STORE_NAME, {
    ...storeConfig
  });
  (0, import_data2.register)(store);
  unlock(store).registerPrivateActions(actions_exports);
  unlock(store).registerPrivateSelectors(selectors_exports);

  // packages/patterns/build-module/components/overrides-panel.mjs
  var import_block_editor2 = __toESM(require_block_editor(), 1);
  var import_components = __toESM(require_components(), 1);
  var import_data3 = __toESM(require_data(), 1);
  var import_element = __toESM(require_element(), 1);
  var import_i18n = __toESM(require_i18n(), 1);

  // packages/patterns/build-module/api/index.mjs
  function isOverridableBlock(block) {
    return !!block.attributes.metadata?.name && !!block.attributes.metadata?.bindings && Object.values(block.attributes.metadata.bindings).some(
      (binding) => binding.source === "core/pattern-overrides"
    );
  }

  // packages/patterns/build-module/components/overrides-panel.mjs
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
  var { BlockQuickNavigation } = unlock(import_block_editor2.privateApis);
  function OverridesPanel() {
    const { allClientIds, supportedBlockTypesRaw } = (0, import_data3.useSelect)(
      (select) => ({
        allClientIds: select(import_block_editor2.store).getClientIdsWithDescendants(),
        supportedBlockTypesRaw: select(import_block_editor2.store).getSettings()?.__experimentalBlockBindingsSupportedAttributes
      }),
      []
    );
    const { getBlock } = (0, import_data3.useSelect)(import_block_editor2.store);
    const clientIdsWithOverrides = (0, import_element.useMemo)(() => {
      const supportedBlockTypes = Object.keys(supportedBlockTypesRaw ?? {});
      return allClientIds.filter((clientId) => {
        const block = getBlock(clientId);
        return supportedBlockTypes.includes(block.name) && isOverridableBlock(block);
      });
    }, [allClientIds, getBlock, supportedBlockTypesRaw]);
    if (!clientIdsWithOverrides?.length) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.PanelBody, { title: (0, import_i18n.__)("Overrides"), children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(BlockQuickNavigation, { clientIds: clientIdsWithOverrides }) });
  }

  // packages/patterns/build-module/components/create-pattern-modal.mjs
  var import_components3 = __toESM(require_components(), 1);

  // node_modules/clsx/dist/clsx.mjs
  function r(e) {
    var t, f, n = "";
    if ("string" == typeof e || "number" == typeof e) n += e;
    else if ("object" == typeof e) if (Array.isArray(e)) {
      var o = e.length;
      for (t = 0; t < o; t++) e[t] && (f = r(e[t])) && (n && (n += " "), n += f);
    } else for (f in e) e[f] && (n && (n += " "), n += f);
    return n;
  }
  function clsx() {
    for (var e, t, f = 0, n = "", o = arguments.length; f < o; f++) (e = arguments[f]) && (t = r(e)) && (n && (n += " "), n += t);
    return n;
  }
  var clsx_default = clsx;

  // node_modules/@base-ui/utils/useRefWithInit.mjs
  var React = __toESM(require_react(), 1);
  var UNINITIALIZED = {};
  function useRefWithInit(init, initArg) {
    const ref = React.useRef(UNINITIALIZED);
    if (ref.current === UNINITIALIZED) {
      ref.current = init(initArg);
    }
    return ref;
  }

  // node_modules/@base-ui/utils/warn.mjs
  var set;
  if (true) {
    set = /* @__PURE__ */ new Set();
  }
  function warn(...messages) {
    if (true) {
      const messageKey = messages.join(" ");
      if (!set.has(messageKey)) {
        set.add(messageKey);
        console.warn(`Base UI: ${messageKey}`);
      }
    }
  }

  // node_modules/@base-ui/react/internals/useRenderElement.mjs
  var React4 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/useMergedRefs.mjs
  function useMergedRefs(a, b, c, d) {
    const forkRef = useRefWithInit(createForkRef).current;
    if (didChange(forkRef, a, b, c, d)) {
      update(forkRef, [a, b, c, d]);
    }
    return forkRef.callback;
  }
  function useMergedRefsN(refs) {
    const forkRef = useRefWithInit(createForkRef).current;
    if (didChangeN(forkRef, refs)) {
      update(forkRef, refs);
    }
    return forkRef.callback;
  }
  function createForkRef() {
    return {
      callback: null,
      cleanup: null,
      refs: []
    };
  }
  function didChange(forkRef, a, b, c, d) {
    return forkRef.refs[0] !== a || forkRef.refs[1] !== b || forkRef.refs[2] !== c || forkRef.refs[3] !== d;
  }
  function didChangeN(forkRef, newRefs) {
    return forkRef.refs.length !== newRefs.length || forkRef.refs.some((ref, index) => ref !== newRefs[index]);
  }
  function update(forkRef, refs) {
    forkRef.refs = refs;
    if (refs.every((ref) => ref == null)) {
      forkRef.callback = null;
      return;
    }
    forkRef.callback = (instance) => {
      if (forkRef.cleanup) {
        forkRef.cleanup();
        forkRef.cleanup = null;
      }
      if (instance != null) {
        const cleanupCallbacks = Array(refs.length).fill(null);
        for (let i = 0; i < refs.length; i += 1) {
          const ref = refs[i];
          if (ref == null) {
            continue;
          }
          switch (typeof ref) {
            case "function": {
              const refCleanup = ref(instance);
              if (typeof refCleanup === "function") {
                cleanupCallbacks[i] = refCleanup;
              }
              break;
            }
            case "object": {
              ref.current = instance;
              break;
            }
            default:
          }
        }
        forkRef.cleanup = () => {
          for (let i = 0; i < refs.length; i += 1) {
            const ref = refs[i];
            if (ref == null) {
              continue;
            }
            switch (typeof ref) {
              case "function": {
                const cleanupCallback = cleanupCallbacks[i];
                if (typeof cleanupCallback === "function") {
                  cleanupCallback();
                } else {
                  ref(null);
                }
                break;
              }
              case "object": {
                ref.current = null;
                break;
              }
              default:
            }
          }
        };
      }
    };
  }

  // node_modules/@base-ui/utils/getReactElementRef.mjs
  var React3 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/reactVersion.mjs
  var React2 = __toESM(require_react(), 1);
  var majorVersion = parseInt(React2.version, 10);
  function isReactVersionAtLeast(reactVersionToCheck) {
    return majorVersion >= reactVersionToCheck;
  }

  // node_modules/@base-ui/utils/getReactElementRef.mjs
  function getReactElementRef(element) {
    if (!/* @__PURE__ */ React3.isValidElement(element)) {
      return null;
    }
    const reactElement = element;
    const propsWithRef = reactElement.props;
    return (isReactVersionAtLeast(19) ? propsWithRef?.ref : reactElement.ref) ?? null;
  }

  // node_modules/@base-ui/utils/mergeObjects.mjs
  function mergeObjects(a, b) {
    if (a && !b) {
      return a;
    }
    if (!a && b) {
      return b;
    }
    if (a || b) {
      return {
        ...a,
        ...b
      };
    }
    return void 0;
  }

  // node_modules/@base-ui/utils/empty.mjs
  var EMPTY_ARRAY = Object.freeze([]);
  var EMPTY_OBJECT = Object.freeze({});

  // node_modules/@base-ui/react/internals/getStateAttributesProps.mjs
  function getStateAttributesProps(state, customMapping) {
    const props = {};
    for (const key in state) {
      const value = state[key];
      if (customMapping?.hasOwnProperty(key)) {
        const customProps = customMapping[key](value);
        if (customProps != null) {
          Object.assign(props, customProps);
        }
        continue;
      }
      if (value === true) {
        props[`data-${key.toLowerCase()}`] = "";
      } else if (value) {
        props[`data-${key.toLowerCase()}`] = value.toString();
      }
    }
    return props;
  }

  // node_modules/@base-ui/react/utils/resolveClassName.mjs
  function resolveClassName(className, state) {
    return typeof className === "function" ? className(state) : className;
  }

  // node_modules/@base-ui/react/utils/resolveStyle.mjs
  function resolveStyle(style, state) {
    return typeof style === "function" ? style(state) : style;
  }

  // node_modules/@base-ui/react/merge-props/mergeProps.mjs
  var EMPTY_PROPS = {};
  function mergeProps(a, b, c, d, e) {
    if (!c && !d && !e && !a) {
      return createInitialMergedProps(b);
    }
    let merged = createInitialMergedProps(a);
    if (b) {
      merged = mergeInto(merged, b);
    }
    if (c) {
      merged = mergeInto(merged, c);
    }
    if (d) {
      merged = mergeInto(merged, d);
    }
    if (e) {
      merged = mergeInto(merged, e);
    }
    return merged;
  }
  function mergePropsN(props) {
    if (props.length === 0) {
      return EMPTY_PROPS;
    }
    if (props.length === 1) {
      return createInitialMergedProps(props[0]);
    }
    let merged = createInitialMergedProps(props[0]);
    for (let i = 1; i < props.length; i += 1) {
      merged = mergeInto(merged, props[i]);
    }
    return merged;
  }
  function createInitialMergedProps(inputProps) {
    if (isPropsGetter(inputProps)) {
      return {
        ...resolvePropsGetter(inputProps, EMPTY_PROPS)
      };
    }
    return copyInitialProps(inputProps);
  }
  function mergeInto(merged, inputProps) {
    if (isPropsGetter(inputProps)) {
      return resolvePropsGetter(inputProps, merged);
    }
    return mutablyMergeInto(merged, inputProps);
  }
  function copyInitialProps(inputProps) {
    const copiedProps = {
      ...inputProps
    };
    for (const propName in copiedProps) {
      const propValue = copiedProps[propName];
      if (isEventHandler(propName, propValue)) {
        copiedProps[propName] = wrapEventHandler(propValue);
      }
    }
    return copiedProps;
  }
  function mutablyMergeInto(mergedProps, externalProps) {
    if (!externalProps) {
      return mergedProps;
    }
    for (const propName in externalProps) {
      const externalPropValue = externalProps[propName];
      switch (propName) {
        case "style": {
          mergedProps[propName] = mergeObjects(mergedProps.style, externalPropValue);
          break;
        }
        case "className": {
          mergedProps[propName] = mergeClassNames(mergedProps.className, externalPropValue);
          break;
        }
        default: {
          if (isEventHandler(propName, externalPropValue)) {
            mergedProps[propName] = mergeEventHandlers(mergedProps[propName], externalPropValue);
          } else {
            mergedProps[propName] = externalPropValue;
          }
        }
      }
    }
    return mergedProps;
  }
  function isEventHandler(key, value) {
    const code0 = key.charCodeAt(0);
    const code1 = key.charCodeAt(1);
    const code2 = key.charCodeAt(2);
    return code0 === 111 && code1 === 110 && code2 >= 65 && code2 <= 90 && (typeof value === "function" || typeof value === "undefined");
  }
  function isPropsGetter(inputProps) {
    return typeof inputProps === "function";
  }
  function resolvePropsGetter(inputProps, previousProps) {
    if (isPropsGetter(inputProps)) {
      return inputProps(previousProps);
    }
    return inputProps ?? EMPTY_PROPS;
  }
  function mergeEventHandlers(ourHandler, theirHandler) {
    if (!theirHandler) {
      return ourHandler;
    }
    if (!ourHandler) {
      return wrapEventHandler(theirHandler);
    }
    return (...args) => {
      const event = args[0];
      if (isSyntheticEvent(event)) {
        const baseUIEvent = event;
        makeEventPreventable(baseUIEvent);
        const result2 = theirHandler(...args);
        if (!baseUIEvent.baseUIHandlerPrevented) {
          ourHandler?.(...args);
        }
        return result2;
      }
      const result = theirHandler(...args);
      ourHandler?.(...args);
      return result;
    };
  }
  function wrapEventHandler(handler) {
    if (!handler) {
      return handler;
    }
    return (...args) => {
      const event = args[0];
      if (isSyntheticEvent(event)) {
        makeEventPreventable(event);
      }
      return handler(...args);
    };
  }
  function makeEventPreventable(event) {
    event.preventBaseUIHandler = () => {
      event.baseUIHandlerPrevented = true;
    };
    return event;
  }
  function mergeClassNames(ourClassName, theirClassName) {
    if (theirClassName) {
      if (ourClassName) {
        return theirClassName + " " + ourClassName;
      }
      return theirClassName;
    }
    return ourClassName;
  }
  function isSyntheticEvent(event) {
    return event != null && typeof event === "object" && "nativeEvent" in event;
  }

  // node_modules/@base-ui/react/internals/useRenderElement.mjs
  var import_react = __toESM(require_react(), 1);
  function useRenderElement(element, componentProps, params = {}) {
    const renderProp = componentProps.render;
    const outProps = useRenderElementProps(componentProps, params);
    if (params.enabled === false) {
      return null;
    }
    const state = params.state ?? EMPTY_OBJECT;
    return evaluateRenderProp(element, renderProp, outProps, state);
  }
  function useRenderElementProps(componentProps, params = {}) {
    const {
      className: classNameProp,
      style: styleProp,
      render: renderProp
    } = componentProps;
    const {
      state = EMPTY_OBJECT,
      ref,
      props,
      stateAttributesMapping,
      enabled = true
    } = params;
    const className = enabled ? resolveClassName(classNameProp, state) : void 0;
    const style = enabled ? resolveStyle(styleProp, state) : void 0;
    const stateProps = enabled ? getStateAttributesProps(state, stateAttributesMapping) : EMPTY_OBJECT;
    const resolvedProps = enabled && props ? resolveRenderFunctionProps(props) : void 0;
    const outProps = enabled ? mergeObjects(stateProps, resolvedProps) ?? {} : EMPTY_OBJECT;
    if (typeof document !== "undefined") {
      if (!enabled) {
        useMergedRefs(null, null);
      } else if (Array.isArray(ref)) {
        outProps.ref = useMergedRefsN([outProps.ref, getReactElementRef(renderProp), ...ref]);
      } else {
        outProps.ref = useMergedRefs(outProps.ref, getReactElementRef(renderProp), ref);
      }
    }
    if (!enabled) {
      return EMPTY_OBJECT;
    }
    if (className !== void 0) {
      outProps.className = mergeClassNames(outProps.className, className);
    }
    if (style !== void 0) {
      outProps.style = mergeObjects(outProps.style, style);
    }
    return outProps;
  }
  function resolveRenderFunctionProps(props) {
    if (Array.isArray(props)) {
      return mergePropsN(props);
    }
    return mergeProps(void 0, props);
  }
  var REACT_LAZY_TYPE = /* @__PURE__ */ Symbol.for("react.lazy");
  var COMPONENT_IDENTIFIER_PATTERN = /^[A-Z][A-Za-z0-9$]*$/;
  var LOWERCASE_CHARACTER_PATTERN = /[a-z]/;
  function evaluateRenderProp(element, render, props, state) {
    if (render) {
      if (typeof render === "function") {
        if (true) {
          warnIfRenderPropLooksLikeComponent(render);
        }
        return render(props, state);
      }
      const mergedProps = mergeProps(props, render.props);
      mergedProps.ref = props.ref;
      let newElement = render;
      if (newElement?.$$typeof === REACT_LAZY_TYPE) {
        const children = React4.Children.toArray(render);
        newElement = children[0];
      }
      if (true) {
        if (!/* @__PURE__ */ React4.isValidElement(newElement)) {
          throw new Error(["Base UI: The `render` prop was provided an invalid React element as `React.isValidElement(render)` is `false`.", "A valid React element must be provided to the `render` prop because it is cloned with props to replace the default element.", "https://base-ui.com/r/invalid-render-prop"].join("\n"));
        }
      }
      return /* @__PURE__ */ React4.cloneElement(newElement, mergedProps);
    }
    if (element) {
      if (typeof element === "string") {
        return renderTag(element, props);
      }
    }
    throw new Error(true ? "Base UI: Render element or function are not defined." : formatErrorMessage_default(8));
  }
  function warnIfRenderPropLooksLikeComponent(renderFn) {
    const functionName = renderFn.name;
    if (functionName.length === 0) {
      return;
    }
    if (!COMPONENT_IDENTIFIER_PATTERN.test(functionName)) {
      return;
    }
    if (!LOWERCASE_CHARACTER_PATTERN.test(functionName)) {
      return;
    }
    warn(`The \`render\` prop received a function named \`${functionName}\` that starts with an uppercase letter.`, "This usually means a React component was passed directly as `render={Component}`.", "Base UI calls `render` as a plain function, which can break the Rules of Hooks during reconciliation.", "If this is an intentional render callback, rename it to start with a lowercase letter.", "Use `render={<Component />}` or `render={(props) => <Component {...props} />}` instead.", "https://base-ui.com/r/invalid-render-prop");
  }
  function renderTag(Tag, props) {
    if (Tag === "button") {
      return /* @__PURE__ */ (0, import_react.createElement)("button", {
        type: "button",
        ...props,
        key: props.key
      });
    }
    if (Tag === "img") {
      return /* @__PURE__ */ (0, import_react.createElement)("img", {
        alt: "",
        ...props,
        key: props.key
      });
    }
    return /* @__PURE__ */ React4.createElement(Tag, props);
  }

  // node_modules/@base-ui/react/use-render/useRender.mjs
  function useRender(params) {
    return useRenderElement(params.defaultTagName ?? "div", params, params);
  }

  // packages/ui/build-module/text/text.mjs
  var import_element2 = __toESM(require_element(), 1);
  var STYLE_HASH_ATTRIBUTE = "data-wp-hash";
  function getRuntime() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument(targetDocument) {
    const runtime = getRuntime();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle(hash, css) {
    const runtime = getRuntime();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle("a495f9d138", '@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._83ed8a8da5dd50ea__text{margin:0}._14437cfb77831647__heading-2xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-2xl,32px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-2xl,32px);--_gcd-p-line-height:var(--wpds-typography-line-height-2xl,40px);font-size:var(--wpds-typography-font-size-2xl,32px);line-height:var(--wpds-typography-line-height-2xl,40px)}._14437cfb77831647__heading-2xl,._3c78b7fa9b4072dd__heading-xl{font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-emphasis,600)}._3c78b7fa9b4072dd__heading-xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-p-line-height:var(--wpds-typography-line-height-md,24px);font-size:var(--wpds-typography-font-size-xl,20px);line-height:var(--wpds-typography-line-height-md,24px)}.aa58f227716bcde2__heading-lg{--_gcd-heading-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-p-line-height:var(--wpds-typography-line-height-sm,20px);font-size:var(--wpds-typography-font-size-lg,15px)}.aa58f227716bcde2__heading-lg,.fc4da56d8dfe52c4__heading-md{font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-emphasis,600);line-height:var(--wpds-typography-line-height-sm,20px)}.fc4da56d8dfe52c4__heading-md{--_gcd-heading-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-p-line-height:var(--wpds-typography-line-height-sm,20px);font-size:var(--wpds-typography-font-size-md,13px)}.a9b78c7c82e8dff7__heading-sm{--_gcd-heading-font-size:var(--wpds-typography-font-size-xs,11px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-emphasis,600);--_gcd-p-font-size:var(--wpds-typography-font-size-xs,11px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);font-family:var(--wpds-typography-font-family-heading,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-size:var(--wpds-typography-font-size-xs,11px);font-weight:var(--wpds-typography-font-weight-emphasis,600);line-height:var(--wpds-typography-line-height-xs,16px);text-transform:uppercase}._305ff559e52180d5__body-xl{--_gcd-heading-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-default,400);--_gcd-p-font-size:var(--wpds-typography-font-size-xl,20px);--_gcd-p-line-height:var(--wpds-typography-line-height-xl,32px);font-size:var(--wpds-typography-font-size-xl,20px);line-height:var(--wpds-typography-line-height-xl,32px)}._305ff559e52180d5__body-xl,.ca1aa3fc2029e958__body-lg{font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-default,400)}.ca1aa3fc2029e958__body-lg{--_gcd-heading-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-default,400);--_gcd-p-font-size:var(--wpds-typography-font-size-lg,15px);--_gcd-p-line-height:var(--wpds-typography-line-height-md,24px);font-size:var(--wpds-typography-font-size-lg,15px);line-height:var(--wpds-typography-line-height-md,24px)}._131101940be12424__body-md{--_gcd-heading-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-default,400);--_gcd-p-font-size:var(--wpds-typography-font-size-md,13px);--_gcd-p-line-height:var(--wpds-typography-line-height-sm,20px);font-size:var(--wpds-typography-font-size-md,13px);line-height:var(--wpds-typography-line-height-sm,20px)}._0e8d87a42c1f75fa__body-sm,._131101940be12424__body-md{font-family:var(--wpds-typography-font-family-body,-apple-system,system-ui,"Segoe UI","Roboto","Oxygen-Sans","Ubuntu","Cantarell","Helvetica Neue",sans-serif);font-weight:var(--wpds-typography-font-weight-default,400)}._0e8d87a42c1f75fa__body-sm{--_gcd-heading-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-heading-font-weight:var(--wpds-typography-font-weight-default,400);--_gcd-p-font-size:var(--wpds-typography-font-size-sm,12px);--_gcd-p-line-height:var(--wpds-typography-line-height-xs,16px);font-size:var(--wpds-typography-font-size-sm,12px);line-height:var(--wpds-typography-line-height-xs,16px)}}}');
  }
  var style_default = { "text": "_83ed8a8da5dd50ea__text", "heading-2xl": "_14437cfb77831647__heading-2xl", "heading-xl": "_3c78b7fa9b4072dd__heading-xl", "heading-lg": "aa58f227716bcde2__heading-lg", "heading-md": "fc4da56d8dfe52c4__heading-md", "heading-sm": "a9b78c7c82e8dff7__heading-sm", "body-xl": "_305ff559e52180d5__body-xl", "body-lg": "ca1aa3fc2029e958__body-lg", "body-md": "_131101940be12424__body-md", "body-sm": "_0e8d87a42c1f75fa__body-sm" };
  if (typeof process === "undefined" || true) {
    registerStyle("af6d9984a6", "._6defc79820e382c6__button{box-sizing:var(--_gcd-button-box-sizing,border-box);font-family:var(--_gcd-button-font-family,inherit);font-size:var(--_gcd-button-font-size,inherit);font-weight:var(--_gcd-button-font-weight,inherit)}.d2cff2e5dea83bd1__input{box-sizing:var(--_gcd-input-box-sizing,border-box);font-family:var(--_gcd-input-font-family,inherit);font-size:var(--_gcd-input-font-size,inherit);font-weight:var(--_gcd-input-font-weight,inherit);margin:var(--_gcd-input-margin,0);&:is(textarea,[type=text],[type=password],[type=color],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){background-color:var(--_gcd-input-background-color,transparent);border:var(--_gcd-input-border,none);border-radius:var(--_gcd-input-border-radius,0);box-shadow:var(--_gcd-input-box-shadow,0 0 0 transparent);color:var(--_gcd-input-color,var(--wpds-color-foreground-interactive-neutral,#1e1e1e));&:focus{border-color:var(--_gcd-input-border-color-focus,var(--wp-admin-theme-color));box-shadow:var(--_gcd-input-box-shadow-focus,none);outline:var(--_gcd-input-outline-focus,none)}&:disabled{background:var(--_gcd-input-background-disabled,transparent);border-color:var(--_gcd-input-border-color-disabled,transparent);box-shadow:var(--_gcd-input-box-shadow-disabled,none);color:var(--_gcd-input-color-disabled,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}&::placeholder{color:var(--_gcd-input-placeholder-color,var(--wpds-color-foreground-interactive-neutral-disabled,#8d8d8d))}}&:is(textarea,[type=text],[type=password],[type=date],[type=datetime],[type=datetime-local],[type=email],[type=month],[type=number],[type=search],[type=tel],[type=time],[type=url],[type=week]){line-height:var(--_gcd-input-line-height,inherit);min-height:var(--_gcd-input-min-height,auto);padding:var(--_gcd-input-padding,0)}}._547d86373d02e108__textarea{box-sizing:var(--_gcd-textarea-box-sizing,border-box);overflow:var(--_gcd-textarea-overflow,auto);resize:var(--_gcd-textarea-resize,block)}._8c15fd0ed9f28ba4__div{outline:var(--_gcd-div-outline,0 solid transparent)}p._43cec3e1eec1066d__p{font-size:var(--_gcd-p-font-size,13px);line-height:var(--_gcd-p-line-height,1.5);margin:var(--_gcd-p-margin,0)}:is(h1,h2,h3,h4,h5,h6).e97669c6d9a38497__heading{color:var(--_gcd-heading-color,var(--wpds-color-foreground-content-neutral,#1e1e1e));font-size:var(--_gcd-heading-font-size,inherit);font-weight:var(--_gcd-heading-font-weight,var(--wpds-typography-font-weight-emphasis,600));margin:var(--_gcd-heading-margin,0)}._2c0831b0499dbd6e__a,._2c0831b0499dbd6e__a:is(:hover,:focus,:active){border-radius:var(--_gcd-a-border-radius,0);box-shadow:var(--_gcd-a-box-shadow,none);color:var(--_gcd-a-color,inherit);outline:var(--_gcd-a-outline,0 solid transparent);transition:var(--_gcd-a-transition,none)}");
  }
  var global_css_defense_default = { "button": "_6defc79820e382c6__button", "input": "d2cff2e5dea83bd1__input", "textarea": "_547d86373d02e108__textarea", "div": "_8c15fd0ed9f28ba4__div", "p": "_43cec3e1eec1066d__p", "heading": "e97669c6d9a38497__heading", "a": "_2c0831b0499dbd6e__a" };
  var Text = (0, import_element2.forwardRef)(function Text2({ variant = "body-md", render, className, ...props }, ref) {
    const element = useRender({
      render,
      defaultTagName: "span",
      ref,
      props: mergeProps(props, {
        className: clsx_default(
          style_default.text,
          global_css_defense_default.heading,
          global_css_defense_default.p,
          style_default[variant],
          className
        )
      })
    });
    return element;
  });

  // packages/icons/build-module/library/symbol.mjs
  var import_primitives = __toESM(require_primitives(), 1);
  var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
  var symbol_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", fill: "currentColor", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives.Path, { d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-1 1.4l-5.6 5.6c-.1.1-.3.1-.4 0l-5.6-5.6c-.1-.1-.1-.3 0-.4l5.6-5.6s.1-.1.2-.1.1 0 .2.1l5.6 5.6c.1.1.1.3 0 .4zm-16.6-.4L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z" }) });

  // packages/ui/build-module/stack/stack.mjs
  var import_element3 = __toESM(require_element(), 1);
  var STYLE_HASH_ATTRIBUTE2 = "data-wp-hash";
  function getRuntime2() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument2(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash2(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE2}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE2) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle2(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime2();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash2(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE2, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument2(targetDocument) {
    const runtime = getRuntime2();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle2(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle2(hash, css) {
    const runtime = getRuntime2();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle2(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle2("32aba35fe1", "@layer wp-ui{@layer utilities, components, compositions, overrides;@layer components{._19ce0419607e1896__stack{display:flex}}}");
  }
  var style_default2 = { "stack": "_19ce0419607e1896__stack" };
  var gapTokens = {
    xs: "var(--wpds-dimension-gap-xs, 4px)",
    sm: "var(--wpds-dimension-gap-sm, 8px)",
    md: "var(--wpds-dimension-gap-md, 12px)",
    lg: "var(--wpds-dimension-gap-lg, 16px)",
    xl: "var(--wpds-dimension-gap-xl, 24px)",
    "2xl": "var(--wpds-dimension-gap-2xl, 32px)",
    "3xl": "var(--wpds-dimension-gap-3xl, 40px)"
  };
  var Stack = (0, import_element3.forwardRef)(function Stack2({ direction, gap, align, justify, wrap, render, ...props }, ref) {
    const style = {
      gap: gap && gapTokens[gap],
      alignItems: align,
      justifyContent: justify,
      flexDirection: direction,
      flexWrap: wrap
    };
    const element = useRender({
      render,
      ref,
      props: mergeProps(props, { style, className: style_default2.stack })
    });
    return element;
  });

  // packages/patterns/build-module/components/create-pattern-modal.mjs
  var import_i18n3 = __toESM(require_i18n(), 1);
  var import_element6 = __toESM(require_element(), 1);
  var import_data5 = __toESM(require_data(), 1);
  var import_notices = __toESM(require_notices(), 1);
  var import_core_data3 = __toESM(require_core_data(), 1);

  // packages/patterns/build-module/components/category-selector.mjs
  var import_i18n2 = __toESM(require_i18n(), 1);
  var import_element4 = __toESM(require_element(), 1);
  var import_components2 = __toESM(require_components(), 1);
  var import_compose = __toESM(require_compose(), 1);
  var import_html_entities = __toESM(require_html_entities(), 1);
  var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
  var unescapeString = (arg) => {
    return (0, import_html_entities.decodeEntities)(arg);
  };
  var CATEGORY_SLUG = "wp_pattern_category";
  function CategorySelector({
    categoryTerms,
    onChange,
    categoryMap
  }) {
    const [search, setSearch] = (0, import_element4.useState)("");
    const debouncedSearch = (0, import_compose.useDebounce)(setSearch, 500);
    const suggestions = (0, import_element4.useMemo)(() => {
      return Array.from(categoryMap.values()).map((category) => unescapeString(category.label)).filter((category) => {
        if (search !== "") {
          return category.toLowerCase().includes(search.toLowerCase());
        }
        return true;
      }).sort((a, b) => a.localeCompare(b));
    }, [search, categoryMap]);
    function handleChange(termNames) {
      const uniqueTerms = termNames.reduce((terms, newTerm) => {
        if (!terms.some(
          (term) => term.toLowerCase() === newTerm.toLowerCase()
        )) {
          terms.push(newTerm);
        }
        return terms;
      }, []);
      onChange(uniqueTerms);
    }
    return /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
      import_components2.FormTokenField,
      {
        className: "patterns-menu-items__convert-modal-categories",
        value: categoryTerms,
        suggestions,
        onChange: handleChange,
        onInputChange: debouncedSearch,
        label: (0, import_i18n2.__)("Categories"),
        tokenizeOnBlur: true,
        __experimentalExpandOnFocus: true
      }
    );
  }

  // packages/patterns/build-module/private-hooks.mjs
  var import_data4 = __toESM(require_data(), 1);
  var import_core_data2 = __toESM(require_core_data(), 1);
  var import_element5 = __toESM(require_element(), 1);
  function useAddPatternCategory() {
    const { saveEntityRecord, invalidateResolution } = (0, import_data4.useDispatch)(import_core_data2.store);
    const { corePatternCategories, userPatternCategories } = (0, import_data4.useSelect)(
      (select) => {
        const { getUserPatternCategories, getBlockPatternCategories } = select(import_core_data2.store);
        return {
          corePatternCategories: getBlockPatternCategories(),
          userPatternCategories: getUserPatternCategories()
        };
      },
      []
    );
    const categoryMap = (0, import_element5.useMemo)(() => {
      const uniqueCategories = /* @__PURE__ */ new Map();
      userPatternCategories.forEach((category) => {
        uniqueCategories.set(category.label.toLowerCase(), {
          label: category.label,
          name: category.name,
          id: category.id
        });
      });
      corePatternCategories.forEach((category) => {
        if (!uniqueCategories.has(category.label.toLowerCase()) && // There are two core categories with `Post` label so explicitly remove the one with
        // the `query` slug to avoid any confusion.
        category.name !== "query") {
          uniqueCategories.set(category.label.toLowerCase(), {
            label: category.label,
            name: category.name
          });
        }
      });
      return uniqueCategories;
    }, [userPatternCategories, corePatternCategories]);
    async function findOrCreateTerm(term) {
      try {
        const existingTerm = categoryMap.get(term.toLowerCase());
        if (existingTerm?.id) {
          return existingTerm.id;
        }
        const termData = existingTerm ? { name: existingTerm.label, slug: existingTerm.name } : { name: term };
        const newTerm = await saveEntityRecord(
          "taxonomy",
          CATEGORY_SLUG,
          termData,
          { throwOnError: true }
        );
        invalidateResolution("getUserPatternCategories");
        return newTerm.id;
      } catch (error) {
        if (error.code !== "term_exists") {
          throw error;
        }
        return error.data.term_id;
      }
    }
    return { categoryMap, findOrCreateTerm };
  }

  // packages/patterns/build-module/components/create-pattern-modal.mjs
  var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
  function CreatePatternModal({
    className = "patterns-menu-items__convert-modal",
    modalTitle,
    ...restProps
  }) {
    const defaultModalTitle = (0, import_data5.useSelect)(
      (select) => select(import_core_data3.store).getPostType(PATTERN_TYPES.user)?.labels?.add_new_item,
      []
    );
    return /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
      import_components3.Modal,
      {
        title: modalTitle || defaultModalTitle,
        onRequestClose: restProps.onClose,
        overlayClassName: className,
        focusOnMount: "firstContentElement",
        size: "small",
        children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(CreatePatternModalContents, { ...restProps })
      }
    );
  }
  function CreatePatternModalContents({
    confirmLabel = (0, import_i18n3.__)("Add"),
    defaultCategories = [],
    content,
    onClose,
    onError,
    onSuccess,
    defaultSyncType = PATTERN_SYNC_TYPES.full,
    defaultTitle = ""
  }) {
    const [syncType, setSyncType] = (0, import_element6.useState)(defaultSyncType);
    const [categoryTerms, setCategoryTerms] = (0, import_element6.useState)(defaultCategories);
    const [title, setTitle] = (0, import_element6.useState)(defaultTitle);
    const [isSaving, setIsSaving] = (0, import_element6.useState)(false);
    const { createPattern: createPattern2 } = unlock((0, import_data5.useDispatch)(store));
    const { createErrorNotice } = (0, import_data5.useDispatch)(import_notices.store);
    const { categoryMap, findOrCreateTerm } = useAddPatternCategory();
    async function onCreate(patternTitle, sync) {
      if (!title || isSaving) {
        return;
      }
      try {
        setIsSaving(true);
        const categories = await Promise.all(
          categoryTerms.map(
            (termName) => findOrCreateTerm(termName)
          )
        );
        const newPattern = await createPattern2(
          patternTitle,
          sync,
          typeof content === "function" ? content() : content,
          categories
        );
        onSuccess({
          pattern: newPattern,
          categoryId: PATTERN_DEFAULT_CATEGORY
        });
      } catch (error) {
        createErrorNotice(error.message, {
          type: "snackbar",
          id: "pattern-create"
        });
        onError?.();
      } finally {
        setIsSaving(false);
        setCategoryTerms([]);
        setTitle("");
      }
    }
    return /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
      "form",
      {
        onSubmit: (event) => {
          event.preventDefault();
          onCreate(title, syncType);
        },
        children: /* @__PURE__ */ (0, import_jsx_runtime4.jsxs)(Stack, { direction: "column", gap: "lg", children: [
          /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
            import_components3.TextControl,
            {
              label: (0, import_i18n3.__)("Name"),
              value: title,
              onChange: setTitle,
              placeholder: (0, import_i18n3.__)("My pattern"),
              className: "patterns-create-modal__name-input"
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
            CategorySelector,
            {
              categoryTerms,
              onChange: setCategoryTerms,
              categoryMap
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
            import_components3.ToggleControl,
            {
              label: (0, import_i18n3._x)("Synced", "pattern (singular)"),
              help: (0, import_i18n3.__)(
                "Sync this pattern across multiple locations."
              ),
              checked: syncType === PATTERN_SYNC_TYPES.full,
              onChange: () => {
                setSyncType(
                  syncType === PATTERN_SYNC_TYPES.full ? PATTERN_SYNC_TYPES.unsynced : PATTERN_SYNC_TYPES.full
                );
              }
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime4.jsxs)(Stack, { gap: "sm", justify: "end", children: [
            /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
              import_components3.Button,
              {
                __next40pxDefaultSize: true,
                variant: "tertiary",
                onClick: () => {
                  onClose();
                  setTitle("");
                },
                children: (0, import_i18n3.__)("Cancel")
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
              import_components3.Button,
              {
                __next40pxDefaultSize: true,
                variant: "primary",
                type: "submit",
                "aria-disabled": !title || isSaving,
                isBusy: isSaving,
                children: confirmLabel
              }
            )
          ] })
        ] })
      }
    );
  }

  // packages/patterns/build-module/components/duplicate-pattern-modal.mjs
  var import_core_data4 = __toESM(require_core_data(), 1);
  var import_data6 = __toESM(require_data(), 1);
  var import_i18n4 = __toESM(require_i18n(), 1);
  var import_notices2 = __toESM(require_notices(), 1);
  var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
  function getTermLabels(pattern, categories) {
    if (pattern.type !== PATTERN_TYPES.user) {
      return categories.core?.filter(
        (category) => pattern.categories?.includes(category.name)
      ).map((category) => category.label);
    }
    return categories.user?.filter(
      (category) => pattern.wp_pattern_category?.includes(category.id)
    ).map((category) => category.label);
  }
  function useDuplicatePatternProps({ pattern, onSuccess }) {
    const { createSuccessNotice } = (0, import_data6.useDispatch)(import_notices2.store);
    const categories = (0, import_data6.useSelect)((select) => {
      const { getUserPatternCategories, getBlockPatternCategories } = select(import_core_data4.store);
      return {
        core: getBlockPatternCategories(),
        user: getUserPatternCategories()
      };
    });
    if (!pattern) {
      return null;
    }
    return {
      content: pattern.content,
      defaultCategories: getTermLabels(pattern, categories),
      defaultSyncType: pattern.type !== PATTERN_TYPES.user ? PATTERN_SYNC_TYPES.unsynced : pattern.wp_pattern_sync_status || PATTERN_SYNC_TYPES.full,
      defaultTitle: (0, import_i18n4.sprintf)(
        /* translators: %s: Existing pattern title */
        (0, import_i18n4._x)("%s (Copy)", "pattern"),
        typeof pattern.title === "string" ? pattern.title : pattern.title.raw
      ),
      onSuccess: ({ pattern: newPattern }) => {
        createSuccessNotice(
          (0, import_i18n4.sprintf)(
            // translators: %s: The new pattern's title e.g. 'Call to action (copy)'.
            (0, import_i18n4._x)('"%s" duplicated.', "pattern"),
            newPattern.title.raw
          ),
          {
            type: "snackbar",
            id: "patterns-create"
          }
        );
        onSuccess?.({ pattern: newPattern });
      }
    };
  }
  function DuplicatePatternModal({
    pattern,
    onClose,
    onSuccess
  }) {
    const duplicatedProps = useDuplicatePatternProps({ pattern, onSuccess });
    if (!pattern) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
      CreatePatternModal,
      {
        modalTitle: (0, import_i18n4.__)("Duplicate pattern"),
        confirmLabel: (0, import_i18n4.__)("Duplicate"),
        onClose,
        onError: onClose,
        ...duplicatedProps
      }
    );
  }

  // packages/patterns/build-module/components/rename-pattern-modal.mjs
  var import_components4 = __toESM(require_components(), 1);
  var import_core_data5 = __toESM(require_core_data(), 1);
  var import_data7 = __toESM(require_data(), 1);
  var import_element7 = __toESM(require_element(), 1);
  var import_html_entities2 = __toESM(require_html_entities(), 1);
  var import_i18n5 = __toESM(require_i18n(), 1);
  var import_notices3 = __toESM(require_notices(), 1);
  var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
  function RenamePatternModal({
    onClose,
    onError,
    onSuccess,
    pattern,
    ...props
  }) {
    const originalName = (0, import_html_entities2.decodeEntities)(pattern.title);
    const [name, setName] = (0, import_element7.useState)(originalName);
    const [isSaving, setIsSaving] = (0, import_element7.useState)(false);
    const {
      editEntityRecord,
      __experimentalSaveSpecifiedEntityEdits: saveSpecifiedEntityEdits
    } = (0, import_data7.useDispatch)(import_core_data5.store);
    const { createSuccessNotice, createErrorNotice } = (0, import_data7.useDispatch)(import_notices3.store);
    const onRename = async (event) => {
      event.preventDefault();
      if (!name || name === pattern.title || isSaving) {
        return;
      }
      try {
        await editEntityRecord("postType", pattern.type, pattern.id, {
          title: name
        });
        setIsSaving(true);
        setName("");
        onClose?.();
        const savedRecord = await saveSpecifiedEntityEdits(
          "postType",
          pattern.type,
          pattern.id,
          ["title"],
          { throwOnError: true }
        );
        onSuccess?.(savedRecord);
        createSuccessNotice((0, import_i18n5.__)("Pattern renamed"), {
          type: "snackbar",
          id: "pattern-update"
        });
      } catch (error) {
        onError?.();
        const errorMessage = error.message && error.code !== "unknown_error" ? error.message : (0, import_i18n5.__)("An error occurred while renaming the pattern.");
        createErrorNotice(errorMessage, {
          type: "snackbar",
          id: "pattern-update"
        });
      } finally {
        setIsSaving(false);
        setName("");
      }
    };
    const onRequestClose = () => {
      onClose?.();
      setName("");
    };
    return /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
      import_components4.Modal,
      {
        title: (0, import_i18n5.__)("Rename"),
        ...props,
        onRequestClose: onClose,
        focusOnMount: "firstContentElement",
        size: "small",
        children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)("form", { onSubmit: onRename, children: /* @__PURE__ */ (0, import_jsx_runtime6.jsxs)(Stack, { direction: "column", gap: "lg", children: [
          /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
            import_components4.TextControl,
            {
              label: (0, import_i18n5.__)("Name"),
              value: name,
              onChange: setName,
              required: true
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime6.jsxs)(Stack, { gap: "sm", justify: "end", children: [
            /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
              import_components4.Button,
              {
                __next40pxDefaultSize: true,
                variant: "tertiary",
                onClick: onRequestClose,
                children: (0, import_i18n5.__)("Cancel")
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
              import_components4.Button,
              {
                __next40pxDefaultSize: true,
                variant: "primary",
                type: "submit",
                children: (0, import_i18n5.__)("Save")
              }
            )
          ] })
        ] }) })
      }
    );
  }

  // packages/patterns/build-module/components/index.mjs
  var import_block_editor5 = __toESM(require_block_editor(), 1);

  // packages/patterns/build-module/components/pattern-convert-button.mjs
  var import_blocks2 = __toESM(require_blocks(), 1);
  var import_block_editor3 = __toESM(require_block_editor(), 1);
  var import_element8 = __toESM(require_element(), 1);
  var import_components5 = __toESM(require_components(), 1);
  var import_data8 = __toESM(require_data(), 1);
  var import_core_data6 = __toESM(require_core_data(), 1);
  var import_i18n6 = __toESM(require_i18n(), 1);
  var import_notices4 = __toESM(require_notices(), 1);
  var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
  function PatternConvertButton({
    clientIds,
    rootClientId,
    closeBlockSettingsMenu
  }) {
    const { createSuccessNotice } = (0, import_data8.useDispatch)(import_notices4.store);
    const { replaceBlocks, updateBlockAttributes } = (0, import_data8.useDispatch)(import_block_editor3.store);
    const { setEditingPattern: setEditingPattern2 } = unlock((0, import_data8.useDispatch)(store));
    const [isModalOpen, setIsModalOpen] = (0, import_element8.useState)(false);
    const { getBlockAttributes } = (0, import_data8.useSelect)(import_block_editor3.store);
    const canConvert = (0, import_data8.useSelect)(
      (select) => {
        const { canUser } = select(import_core_data6.store);
        const {
          getBlocksByClientId: getBlocksByClientId2,
          canInsertBlockType,
          getBlockRootClientId
        } = select(import_block_editor3.store);
        const rootId = rootClientId || (clientIds.length > 0 ? getBlockRootClientId(clientIds[0]) : void 0);
        const blocks = getBlocksByClientId2(clientIds) ?? [];
        const hasReusableBlockSupport = (blockName) => {
          const blockType = (0, import_blocks2.getBlockType)(blockName);
          const hasParent = blockType && "parent" in blockType;
          return (0, import_blocks2.hasBlockSupport)(blockName, "reusable", !hasParent);
        };
        const isSyncedPattern = blocks.length === 1 && blocks[0] && (0, import_blocks2.isReusableBlock)(blocks[0]) && !!select(import_core_data6.store).getEntityRecord(
          "postType",
          "wp_block",
          blocks[0].attributes.ref
        );
        const isUnsyncedPattern = blocks.length === 1 && blocks?.[0]?.attributes?.metadata?.patternName;
        const _canConvert = (
          // Hide when this is already a pattern.
          !isUnsyncedPattern && !isSyncedPattern && // Hide when patterns are disabled.
          canInsertBlockType("core/block", rootId) && blocks.every(
            (block) => (
              // Guard against the case where a regular block has *just* been converted.
              !!block && // Hide on invalid blocks.
              block.isValid && // Hide when block doesn't support being made into a pattern.
              hasReusableBlockSupport(block.name)
            )
          ) && // Hide when current doesn't have permission to do that.
          // Blocks refers to the wp_block post type, this checks the ability to create a post of that type.
          !!canUser("create", {
            kind: "postType",
            name: "wp_block"
          })
        );
        return _canConvert;
      },
      [clientIds, rootClientId]
    );
    const { getBlocksByClientId } = (0, import_data8.useSelect)(import_block_editor3.store);
    const getContent = (0, import_element8.useCallback)(
      () => (0, import_blocks2.serialize)(getBlocksByClientId(clientIds)),
      [getBlocksByClientId, clientIds]
    );
    if (!canConvert) {
      return null;
    }
    const handleSuccess = ({ pattern }) => {
      if (pattern.wp_pattern_sync_status === PATTERN_SYNC_TYPES.unsynced) {
        if (clientIds?.length === 1) {
          const existingAttributes = getBlockAttributes(clientIds[0]);
          updateBlockAttributes(clientIds[0], {
            metadata: {
              ...existingAttributes?.metadata ? existingAttributes.metadata : {},
              patternName: `core/block/${pattern.id}`,
              name: pattern.title.raw
            }
          });
        }
      } else {
        const newBlock = (0, import_blocks2.createBlock)("core/block", {
          ref: pattern.id
        });
        replaceBlocks(clientIds, newBlock);
        setEditingPattern2(newBlock.clientId, true);
      }
      createSuccessNotice(
        pattern.wp_pattern_sync_status === PATTERN_SYNC_TYPES.unsynced ? (0, import_i18n6.sprintf)(
          // translators: %s: the name the user has given to the pattern.
          (0, import_i18n6.__)("Unsynced pattern created: %s"),
          pattern.title.raw
        ) : (0, import_i18n6.sprintf)(
          // translators: %s: the name the user has given to the pattern.
          (0, import_i18n6.__)("Synced pattern created: %s"),
          pattern.title.raw
        ),
        {
          type: "snackbar",
          id: "convert-to-pattern-success"
        }
      );
      setIsModalOpen(false);
      closeBlockSettingsMenu();
    };
    return /* @__PURE__ */ (0, import_jsx_runtime7.jsxs)(import_jsx_runtime7.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
        import_components5.MenuItem,
        {
          icon: symbol_default,
          onClick: () => setIsModalOpen(true),
          "aria-expanded": isModalOpen,
          "aria-haspopup": "dialog",
          children: (0, import_i18n6.__)("Create pattern")
        }
      ),
      isModalOpen && /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
        CreatePatternModal,
        {
          content: getContent,
          onSuccess: (pattern) => {
            handleSuccess(pattern);
          },
          onError: () => {
            setIsModalOpen(false);
          },
          onClose: () => {
            setIsModalOpen(false);
          }
        }
      )
    ] });
  }

  // packages/patterns/build-module/components/patterns-manage-button.mjs
  var import_components6 = __toESM(require_components(), 1);
  var import_i18n7 = __toESM(require_i18n(), 1);
  var import_blocks3 = __toESM(require_blocks(), 1);
  var import_data9 = __toESM(require_data(), 1);
  var import_element9 = __toESM(require_element(), 1);
  var import_block_editor4 = __toESM(require_block_editor(), 1);
  var import_url = __toESM(require_url(), 1);
  var import_core_data7 = __toESM(require_core_data(), 1);
  var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
  function PatternsManageButton({ clientId, onClose }) {
    const [showConfirmDialog, setShowConfirmDialog] = (0, import_element9.useState)(false);
    const {
      attributes,
      canDetach,
      isVisible,
      managePatternsUrl,
      isSyncedPattern,
      isUnsyncedPattern,
      canEdit
    } = (0, import_data9.useSelect)(
      (select) => {
        const { canRemoveBlock, getBlock, canEditBlock } = select(import_block_editor4.store);
        const { canUser } = select(import_core_data7.store);
        const block = getBlock(clientId);
        const _isUnsyncedPattern = !!block?.attributes?.metadata?.patternName;
        const _isSyncedPattern = !!block && (0, import_blocks3.isReusableBlock)(block) && !!canUser("update", {
          kind: "postType",
          name: "wp_block",
          id: block.attributes.ref
        });
        return {
          attributes: block.attributes,
          canEdit: canEditBlock(clientId),
          // For unsynced patterns, detaching is simply removing the `patternName` attribute.
          // For synced patterns, the `core:block` block is replaced with its inner blocks,
          // so checking whether `canRemoveBlock` is possible is required.
          canDetach: _isUnsyncedPattern || _isSyncedPattern && canRemoveBlock(clientId),
          isUnsyncedPattern: _isUnsyncedPattern,
          isSyncedPattern: _isSyncedPattern,
          isVisible: _isUnsyncedPattern || _isSyncedPattern,
          // The site editor and templates both check whether the user
          // has edit_theme_options capabilities. We can leverage that here
          // and omit the manage patterns link if the user can't access it.
          managePatternsUrl: canUser("create", {
            kind: "postType",
            name: "wp_template"
          }) ? (0, import_url.addQueryArgs)("site-editor.php", {
            p: "/pattern"
          }) : (0, import_url.addQueryArgs)("edit.php", {
            post_type: "wp_block"
          })
        };
      },
      [clientId]
    );
    const { updateBlockAttributes } = (0, import_data9.useDispatch)(import_block_editor4.store);
    const { convertSyncedPatternToStatic: convertSyncedPatternToStatic2 } = unlock(
      (0, import_data9.useDispatch)(store)
    );
    if (!isVisible || !canEdit) {
      return null;
    }
    const handleDetach = () => {
      if (isSyncedPattern) {
        convertSyncedPatternToStatic2(clientId);
      }
      if (isUnsyncedPattern) {
        const { patternName, ...attributesWithoutPatternName } = attributes?.metadata ?? {};
        updateBlockAttributes(clientId, {
          metadata: attributesWithoutPatternName
        });
      }
      onClose?.();
      setShowConfirmDialog(false);
    };
    return /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)(import_jsx_runtime8.Fragment, { children: [
      canDetach && /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)(import_jsx_runtime8.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_components6.MenuItem, { onClick: () => setShowConfirmDialog(true), children: (0, import_i18n7.__)("Detach") }),
        /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(
          import_components6.__experimentalConfirmDialog,
          {
            isOpen: showConfirmDialog,
            onConfirm: handleDetach,
            onCancel: () => setShowConfirmDialog(false),
            confirmButtonText: (0, import_i18n7.__)("Detach"),
            size: "medium",
            title: (0, import_i18n7.__)("Detach pattern?"),
            __experimentalHideHeader: false,
            children: isSyncedPattern ? (0, import_i18n7.__)(
              "The blocks will be separated from the original pattern and will be fully editable. Future changes to the pattern will not apply here."
            ) : (0, import_i18n7.__)(
              "Blocks will no longer be associated with this pattern and will be fully editable."
            )
          }
        )
      ] }),
      /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_components6.MenuItem, { href: managePatternsUrl, children: (0, import_i18n7.__)("Manage patterns") })
    ] });
  }
  var patterns_manage_button_default = PatternsManageButton;

  // packages/patterns/build-module/components/index.mjs
  var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
  function PatternsMenuItems({ rootClientId }) {
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_block_editor5.BlockSettingsMenuControls, { children: ({ selectedClientIds, onClose }) => /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(import_jsx_runtime9.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
        PatternConvertButton,
        {
          clientIds: selectedClientIds,
          rootClientId,
          closeBlockSettingsMenu: onClose
        }
      ),
      selectedClientIds.length === 1 && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
        patterns_manage_button_default,
        {
          clientId: selectedClientIds[0],
          onClose
        }
      )
    ] }) });
  }

  // packages/patterns/build-module/components/rename-pattern-category-modal.mjs
  var import_components7 = __toESM(require_components(), 1);
  var import_core_data8 = __toESM(require_core_data(), 1);
  var import_data10 = __toESM(require_data(), 1);
  var import_element10 = __toESM(require_element(), 1);
  var import_html_entities3 = __toESM(require_html_entities(), 1);
  var import_i18n8 = __toESM(require_i18n(), 1);
  var import_notices5 = __toESM(require_notices(), 1);
  var import_a11y = __toESM(require_a11y(), 1);
  var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
  function RenamePatternCategoryModal({
    category,
    existingCategories,
    onClose,
    onError,
    onSuccess,
    ...props
  }) {
    const id = (0, import_element10.useId)();
    const textControlRef = (0, import_element10.useRef)();
    const [name, setName] = (0, import_element10.useState)((0, import_html_entities3.decodeEntities)(category.name));
    const [isSaving, setIsSaving] = (0, import_element10.useState)(false);
    const [validationMessage, setValidationMessage] = (0, import_element10.useState)(false);
    const validationMessageId = validationMessage ? `patterns-rename-pattern-category-modal__validation-message-${id}` : void 0;
    const { saveEntityRecord, invalidateResolution } = (0, import_data10.useDispatch)(import_core_data8.store);
    const { createErrorNotice, createSuccessNotice } = (0, import_data10.useDispatch)(import_notices5.store);
    const onChange = (newName) => {
      if (validationMessage) {
        setValidationMessage(void 0);
      }
      setName(newName);
    };
    const onSave = async (event) => {
      event.preventDefault();
      if (isSaving) {
        return;
      }
      if (!name || name === category.name) {
        const message = (0, import_i18n8.__)("Please enter a new name for this category.");
        (0, import_a11y.speak)(message, "assertive");
        setValidationMessage(message);
        textControlRef.current?.focus();
        return;
      }
      if (existingCategories.patternCategories.find((existingCategory) => {
        return existingCategory.id !== category.id && existingCategory.label.toLowerCase() === name.toLowerCase();
      })) {
        const message = (0, import_i18n8.__)(
          "This category already exists. Please use a different name."
        );
        (0, import_a11y.speak)(message, "assertive");
        setValidationMessage(message);
        textControlRef.current?.focus();
        return;
      }
      try {
        setIsSaving(true);
        const savedRecord = await saveEntityRecord(
          "taxonomy",
          CATEGORY_SLUG,
          {
            id: category.id,
            slug: category.slug,
            name
          }
        );
        invalidateResolution("getUserPatternCategories");
        onSuccess?.(savedRecord);
        onClose();
        createSuccessNotice((0, import_i18n8.__)("Pattern category renamed."), {
          type: "snackbar",
          id: "pattern-category-update"
        });
      } catch (error) {
        onError?.();
        createErrorNotice(error.message, {
          type: "snackbar",
          id: "pattern-category-update"
        });
      } finally {
        setIsSaving(false);
        setName("");
      }
    };
    const onRequestClose = () => {
      onClose();
      setName("");
    };
    return /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
      import_components7.Modal,
      {
        title: (0, import_i18n8.__)("Rename"),
        onRequestClose,
        ...props,
        children: /* @__PURE__ */ (0, import_jsx_runtime10.jsx)("form", { onSubmit: onSave, children: /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)(Stack, { direction: "column", gap: "lg", children: [
          /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)(Stack, { direction: "column", gap: "sm", children: [
            /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
              import_components7.TextControl,
              {
                ref: textControlRef,
                label: (0, import_i18n8.__)("Name"),
                value: name,
                onChange,
                "aria-describedby": validationMessageId,
                required: true
              }
            ),
            validationMessage && /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
              "span",
              {
                className: "patterns-rename-pattern-category-modal__validation-message",
                id: validationMessageId,
                children: validationMessage
              }
            )
          ] }),
          /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)(Stack, { gap: "sm", justify: "end", children: [
            /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
              import_components7.Button,
              {
                __next40pxDefaultSize: true,
                variant: "tertiary",
                onClick: onRequestClose,
                children: (0, import_i18n8.__)("Cancel")
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
              import_components7.Button,
              {
                __next40pxDefaultSize: true,
                variant: "primary",
                type: "submit",
                "aria-disabled": !name || name === category.name || isSaving,
                isBusy: isSaving,
                children: (0, import_i18n8.__)("Save")
              }
            )
          ] })
        ] }) })
      }
    );
  }

  // packages/patterns/build-module/components/pattern-overrides-controls.mjs
  var import_element12 = __toESM(require_element(), 1);
  var import_block_editor6 = __toESM(require_block_editor(), 1);
  var import_components9 = __toESM(require_components(), 1);
  var import_i18n10 = __toESM(require_i18n(), 1);

  // packages/patterns/build-module/components/allow-overrides-modal.mjs
  var import_components8 = __toESM(require_components(), 1);
  var import_i18n9 = __toESM(require_i18n(), 1);
  var import_element11 = __toESM(require_element(), 1);
  var import_a11y2 = __toESM(require_a11y(), 1);
  var import_jsx_runtime11 = __toESM(require_jsx_runtime(), 1);
  function AllowOverridesModal({
    placeholder,
    initialName = "",
    onClose,
    onSave
  }) {
    const [editedBlockName, setEditedBlockName] = (0, import_element11.useState)(initialName);
    const descriptionId = (0, import_element11.useId)();
    const isNameValid = !!editedBlockName.trim();
    const handleSubmit = () => {
      if (editedBlockName !== initialName) {
        const message = (0, import_i18n9.sprintf)(
          /* translators: %s: new name/label for the block */
          (0, import_i18n9.__)('Block name changed to: "%s".'),
          editedBlockName
        );
        (0, import_a11y2.speak)(message, "assertive");
      }
      onSave(editedBlockName);
      onClose();
    };
    return /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
      import_components8.Modal,
      {
        title: (0, import_i18n9.__)("Enable overrides"),
        onRequestClose: onClose,
        focusOnMount: "firstContentElement",
        aria: { describedby: descriptionId },
        size: "small",
        children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
          "form",
          {
            onSubmit: (event) => {
              event.preventDefault();
              if (!isNameValid) {
                return;
              }
              handleSubmit();
            },
            children: /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(Stack, { direction: "column", gap: "xl", children: [
              /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(Text, { id: descriptionId, children: (0, import_i18n9.__)(
                "Overrides are changes you make to a block within a synced pattern instance. Use overrides to customize a synced pattern instance to suit its new context. Name this block to specify an override."
              ) }),
              /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
                import_components8.TextControl,
                {
                  value: editedBlockName,
                  label: (0, import_i18n9.__)("Name"),
                  help: (0, import_i18n9.__)(
                    'For example, if you are creating a recipe pattern, you use "Recipe Title", "Recipe Description", etc.'
                  ),
                  placeholder,
                  onChange: setEditedBlockName
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(Stack, { gap: "sm", justify: "end", children: [
                /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
                  import_components8.Button,
                  {
                    __next40pxDefaultSize: true,
                    variant: "tertiary",
                    onClick: onClose,
                    children: (0, import_i18n9.__)("Cancel")
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
                  import_components8.Button,
                  {
                    __next40pxDefaultSize: true,
                    "aria-disabled": !isNameValid,
                    variant: "primary",
                    type: "submit",
                    children: (0, import_i18n9.__)("Enable")
                  }
                )
              ] })
            ] })
          }
        )
      }
    );
  }
  function DisallowOverridesModal({ onClose, onSave }) {
    const descriptionId = (0, import_element11.useId)();
    return /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
      import_components8.Modal,
      {
        title: (0, import_i18n9.__)("Disable overrides"),
        onRequestClose: onClose,
        aria: { describedby: descriptionId },
        size: "small",
        children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
          "form",
          {
            onSubmit: (event) => {
              event.preventDefault();
              onSave();
              onClose();
            },
            children: /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(Stack, { direction: "column", gap: "xl", children: [
              /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(Text, { id: descriptionId, children: (0, import_i18n9.__)(
                "Are you sure you want to disable overrides? Disabling overrides will revert all applied overrides for this block throughout instances of this pattern."
              ) }),
              /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(Stack, { gap: "sm", justify: "end", children: [
                /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
                  import_components8.Button,
                  {
                    __next40pxDefaultSize: true,
                    variant: "tertiary",
                    onClick: onClose,
                    children: (0, import_i18n9.__)("Cancel")
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
                  import_components8.Button,
                  {
                    __next40pxDefaultSize: true,
                    variant: "primary",
                    type: "submit",
                    children: (0, import_i18n9.__)("Disable")
                  }
                )
              ] })
            ] })
          }
        )
      }
    );
  }

  // packages/patterns/build-module/components/pattern-overrides-controls.mjs
  var import_jsx_runtime12 = __toESM(require_jsx_runtime(), 1);
  function PatternOverridesControls({
    attributes,
    setAttributes,
    name: blockName
  }) {
    const controlId = (0, import_element12.useId)();
    const [showAllowOverridesModal, setShowAllowOverridesModal] = (0, import_element12.useState)(false);
    const [showDisallowOverridesModal, setShowDisallowOverridesModal] = (0, import_element12.useState)(false);
    const hasName = !!attributes.metadata?.name;
    const defaultBindings = attributes.metadata?.bindings?.__default;
    const hasOverrides = hasName && defaultBindings?.source === PATTERN_OVERRIDES_BINDING_SOURCE;
    const isConnectedToOtherSources = defaultBindings?.source && defaultBindings.source !== PATTERN_OVERRIDES_BINDING_SOURCE;
    const { updateBlockBindings } = (0, import_block_editor6.useBlockBindingsUtils)();
    function updateBindings(isChecked, customName) {
      if (customName) {
        setAttributes({
          metadata: {
            ...attributes.metadata,
            name: customName
          }
        });
      }
      updateBlockBindings({
        __default: isChecked ? { source: PATTERN_OVERRIDES_BINDING_SOURCE } : void 0
      });
    }
    if (isConnectedToOtherSources) {
      return null;
    }
    const hasUnsupportedImageAttributes = blockName === "core/image" && !!attributes.href?.length;
    const helpText = !hasOverrides && hasUnsupportedImageAttributes ? (0, import_i18n10.__)(
      `Overrides currently don't support image links. Remove the link first before enabling overrides.`
    ) : (0, import_i18n10.__)(
      "Allow changes to this block throughout instances of this pattern."
    );
    return /* @__PURE__ */ (0, import_jsx_runtime12.jsxs)(import_jsx_runtime12.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_block_editor6.InspectorControls, { group: "advanced", children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
        import_components9.BaseControl,
        {
          id: controlId,
          label: (0, import_i18n10.__)("Overrides"),
          help: helpText,
          children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
            import_components9.Button,
            {
              __next40pxDefaultSize: true,
              className: "pattern-overrides-control__allow-overrides-button",
              variant: "secondary",
              "aria-haspopup": "dialog",
              onClick: () => {
                if (hasOverrides) {
                  setShowDisallowOverridesModal(true);
                } else {
                  setShowAllowOverridesModal(true);
                }
              },
              disabled: !hasOverrides && hasUnsupportedImageAttributes,
              accessibleWhenDisabled: true,
              children: hasOverrides ? (0, import_i18n10.__)("Disable overrides") : (0, import_i18n10.__)("Enable overrides")
            }
          )
        }
      ) }),
      showAllowOverridesModal && /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
        AllowOverridesModal,
        {
          initialName: attributes.metadata?.name,
          onClose: () => setShowAllowOverridesModal(false),
          onSave: (newName) => {
            updateBindings(true, newName);
          }
        }
      ),
      showDisallowOverridesModal && /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
        DisallowOverridesModal,
        {
          onClose: () => setShowDisallowOverridesModal(false),
          onSave: () => updateBindings(false)
        }
      )
    ] });
  }
  var pattern_overrides_controls_default = PatternOverridesControls;

  // packages/patterns/build-module/components/reset-overrides-control.mjs
  var import_block_editor7 = __toESM(require_block_editor(), 1);
  var import_components10 = __toESM(require_components(), 1);
  var import_data11 = __toESM(require_data(), 1);
  var import_i18n11 = __toESM(require_i18n(), 1);
  var import_jsx_runtime13 = __toESM(require_jsx_runtime(), 1);
  var CONTENT = "content";
  function ResetOverridesControl(props) {
    const name = props.attributes.metadata?.name;
    const registry = (0, import_data11.useRegistry)();
    const isOverridden = (0, import_data11.useSelect)(
      (select) => {
        if (!name) {
          return;
        }
        const { getBlockAttributes, getBlockParentsByBlockName } = select(import_block_editor7.store);
        const [patternClientId] = getBlockParentsByBlockName(
          props.clientId,
          "core/block",
          true
        );
        if (!patternClientId) {
          return;
        }
        const overrides = getBlockAttributes(patternClientId)[CONTENT];
        if (!overrides) {
          return;
        }
        return overrides.hasOwnProperty(name);
      },
      [props.clientId, name]
    );
    function onClick() {
      const { getBlockAttributes, getBlockParentsByBlockName } = registry.select(import_block_editor7.store);
      const [patternClientId] = getBlockParentsByBlockName(
        props.clientId,
        "core/block",
        true
      );
      if (!patternClientId) {
        return;
      }
      const overrides = getBlockAttributes(patternClientId)[CONTENT];
      if (!overrides.hasOwnProperty(name)) {
        return;
      }
      const { updateBlockAttributes, __unstableMarkLastChangeAsPersistent } = registry.dispatch(import_block_editor7.store);
      __unstableMarkLastChangeAsPersistent();
      let newOverrides = { ...overrides };
      delete newOverrides[name];
      if (!Object.keys(newOverrides).length) {
        newOverrides = void 0;
      }
      updateBlockAttributes(patternClientId, {
        [CONTENT]: newOverrides
      });
    }
    return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_block_editor7.__unstableBlockToolbarLastItem, { children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_components10.ToolbarGroup, { children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_components10.ToolbarButton, { onClick, disabled: !isOverridden, children: (0, import_i18n11.__)("Reset") }) }) });
  }

  // packages/patterns/build-module/private-apis.mjs
  var privateApis = {};
  lock(privateApis, {
    OverridesPanel,
    CreatePatternModal,
    CreatePatternModalContents,
    DuplicatePatternModal,
    isOverridableBlock,
    useDuplicatePatternProps,
    RenamePatternModal,
    PatternsMenuItems,
    RenamePatternCategoryModal,
    PatternOverridesControls: pattern_overrides_controls_default,
    ResetOverridesControl,
    useAddPatternCategory,
    PATTERN_TYPES,
    PATTERN_DEFAULT_CATEGORY,
    PATTERN_USER_CATEGORY,
    EXCLUDED_PATTERN_SOURCES,
    PATTERN_SYNC_TYPES
  });
  return __toCommonJS(index_exports);
})();
