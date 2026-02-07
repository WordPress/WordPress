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

  // package-external:@wordpress/notices
  var require_notices = __commonJS({
    "package-external:@wordpress/notices"(exports, module) {
      module.exports = window.wp.notices;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/html-entities
  var require_html_entities = __commonJS({
    "package-external:@wordpress/html-entities"(exports, module) {
      module.exports = window.wp.htmlEntities;
    }
  });

  // package-external:@wordpress/primitives
  var require_primitives = __commonJS({
    "package-external:@wordpress/primitives"(exports, module) {
      module.exports = window.wp.primitives;
    }
  });

  // package-external:@wordpress/url
  var require_url = __commonJS({
    "package-external:@wordpress/url"(exports, module) {
      module.exports = window.wp.url;
    }
  });

  // package-external:@wordpress/a11y
  var require_a11y = __commonJS({
    "package-external:@wordpress/a11y"(exports, module) {
      module.exports = window.wp.a11y;
    }
  });

  // packages/patterns/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    privateApis: () => privateApis,
    store: () => store
  });

  // packages/patterns/build-module/store/index.js
  var import_data2 = __toESM(require_data());

  // packages/patterns/build-module/store/reducer.js
  var import_data = __toESM(require_data());
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

  // packages/patterns/build-module/store/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    convertSyncedPatternToStatic: () => convertSyncedPatternToStatic,
    createPattern: () => createPattern,
    createPatternFromFile: () => createPatternFromFile,
    setEditingPattern: () => setEditingPattern
  });
  var import_blocks = __toESM(require_blocks());
  var import_core_data = __toESM(require_core_data());
  var import_block_editor = __toESM(require_block_editor());

  // packages/patterns/build-module/constants.js
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
  var PARTIAL_SYNCING_SUPPORTED_BLOCKS = {
    "core/paragraph": ["content"],
    "core/heading": ["content"],
    "core/button": ["text", "url", "linkTarget", "rel"],
    "core/image": ["id", "url", "title", "alt", "caption"]
  };
  var PATTERN_OVERRIDES_BINDING_SOURCE = "core/pattern-overrides";

  // packages/patterns/build-module/store/actions.js
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
    } catch (e) {
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

  // packages/patterns/build-module/store/constants.js
  var STORE_NAME = "core/patterns";

  // packages/patterns/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    isEditingPattern: () => isEditingPattern2
  });
  function isEditingPattern2(state, clientId) {
    return state.isEditingPattern[clientId];
  }

  // packages/patterns/build-module/lock-unlock.js
  var import_private_apis = __toESM(require_private_apis());
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/patterns"
  );

  // packages/patterns/build-module/store/index.js
  var storeConfig = {
    reducer: reducer_default
  };
  var store = (0, import_data2.createReduxStore)(STORE_NAME, {
    ...storeConfig
  });
  (0, import_data2.register)(store);
  unlock(store).registerPrivateActions(actions_exports);
  unlock(store).registerPrivateSelectors(selectors_exports);

  // packages/patterns/build-module/components/overrides-panel.js
  var import_block_editor2 = __toESM(require_block_editor());
  var import_components = __toESM(require_components());
  var import_data3 = __toESM(require_data());
  var import_element = __toESM(require_element());
  var import_i18n = __toESM(require_i18n());

  // packages/patterns/build-module/api/index.js
  function isOverridableBlock(block) {
    return Object.keys(PARTIAL_SYNCING_SUPPORTED_BLOCKS).includes(
      block.name
    ) && !!block.attributes.metadata?.name && !!block.attributes.metadata?.bindings && Object.values(block.attributes.metadata.bindings).some(
      (binding) => binding.source === "core/pattern-overrides"
    );
  }
  function hasOverridableBlocks(blocks) {
    return blocks.some((block) => {
      if (isOverridableBlock(block)) {
        return true;
      }
      return hasOverridableBlocks(block.innerBlocks);
    });
  }

  // packages/patterns/build-module/components/overrides-panel.js
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var { BlockQuickNavigation } = unlock(import_block_editor2.privateApis);
  function OverridesPanel() {
    const allClientIds = (0, import_data3.useSelect)(
      (select) => select(import_block_editor2.store).getClientIdsWithDescendants(),
      []
    );
    const { getBlock } = (0, import_data3.useSelect)(import_block_editor2.store);
    const clientIdsWithOverrides = (0, import_element.useMemo)(
      () => allClientIds.filter((clientId) => {
        const block = getBlock(clientId);
        return isOverridableBlock(block);
      }),
      [allClientIds, getBlock]
    );
    if (!clientIdsWithOverrides?.length) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.PanelBody, { title: (0, import_i18n.__)("Overrides"), children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(BlockQuickNavigation, { clientIds: clientIdsWithOverrides }) });
  }

  // packages/patterns/build-module/components/create-pattern-modal.js
  var import_components3 = __toESM(require_components());
  var import_i18n3 = __toESM(require_i18n());
  var import_element4 = __toESM(require_element());
  var import_data5 = __toESM(require_data());
  var import_notices = __toESM(require_notices());
  var import_core_data3 = __toESM(require_core_data());

  // packages/patterns/build-module/components/category-selector.js
  var import_i18n2 = __toESM(require_i18n());
  var import_element2 = __toESM(require_element());
  var import_components2 = __toESM(require_components());
  var import_compose = __toESM(require_compose());
  var import_html_entities = __toESM(require_html_entities());
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  var unescapeString = (arg) => {
    return (0, import_html_entities.decodeEntities)(arg);
  };
  var CATEGORY_SLUG = "wp_pattern_category";
  function CategorySelector({
    categoryTerms,
    onChange,
    categoryMap
  }) {
    const [search, setSearch] = (0, import_element2.useState)("");
    const debouncedSearch = (0, import_compose.useDebounce)(setSearch, 500);
    const suggestions = (0, import_element2.useMemo)(() => {
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
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
      import_components2.FormTokenField,
      {
        className: "patterns-menu-items__convert-modal-categories",
        value: categoryTerms,
        suggestions,
        onChange: handleChange,
        onInputChange: debouncedSearch,
        label: (0, import_i18n2.__)("Categories"),
        tokenizeOnBlur: true,
        __experimentalExpandOnFocus: true,
        __next40pxDefaultSize: true,
        __nextHasNoMarginBottom: true
      }
    );
  }

  // packages/patterns/build-module/private-hooks.js
  var import_data4 = __toESM(require_data());
  var import_core_data2 = __toESM(require_core_data());
  var import_element3 = __toESM(require_element());
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
    const categoryMap = (0, import_element3.useMemo)(() => {
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

  // packages/patterns/build-module/components/create-pattern-modal.js
  var import_jsx_runtime3 = __toESM(require_jsx_runtime());
  function CreatePatternModal({
    className = "patterns-menu-items__convert-modal",
    modalTitle,
    ...restProps
  }) {
    const defaultModalTitle = (0, import_data5.useSelect)(
      (select) => select(import_core_data3.store).getPostType(PATTERN_TYPES.user)?.labels?.add_new_item,
      []
    );
    return /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
      import_components3.Modal,
      {
        title: modalTitle || defaultModalTitle,
        onRequestClose: restProps.onClose,
        overlayClassName: className,
        focusOnMount: "firstContentElement",
        size: "small",
        children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(CreatePatternModalContents, { ...restProps })
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
    const [syncType, setSyncType] = (0, import_element4.useState)(defaultSyncType);
    const [categoryTerms, setCategoryTerms] = (0, import_element4.useState)(defaultCategories);
    const [title, setTitle] = (0, import_element4.useState)(defaultTitle);
    const [isSaving, setIsSaving] = (0, import_element4.useState)(false);
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
    return /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
      "form",
      {
        onSubmit: (event) => {
          event.preventDefault();
          onCreate(title, syncType);
        },
        children: /* @__PURE__ */ (0, import_jsx_runtime3.jsxs)(import_components3.__experimentalVStack, { spacing: "5", children: [
          /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
            import_components3.TextControl,
            {
              label: (0, import_i18n3.__)("Name"),
              value: title,
              onChange: setTitle,
              placeholder: (0, import_i18n3.__)("My pattern"),
              className: "patterns-create-modal__name-input",
              __nextHasNoMarginBottom: true,
              __next40pxDefaultSize: true
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
            CategorySelector,
            {
              categoryTerms,
              onChange: setCategoryTerms,
              categoryMap
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
            import_components3.ToggleControl,
            {
              __nextHasNoMarginBottom: true,
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
          /* @__PURE__ */ (0, import_jsx_runtime3.jsxs)(import_components3.__experimentalHStack, { justify: "right", children: [
            /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
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
            /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
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

  // packages/patterns/build-module/components/duplicate-pattern-modal.js
  var import_core_data4 = __toESM(require_core_data());
  var import_data6 = __toESM(require_data());
  var import_i18n4 = __toESM(require_i18n());
  var import_notices2 = __toESM(require_notices());
  var import_jsx_runtime4 = __toESM(require_jsx_runtime());
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
    return /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
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

  // packages/patterns/build-module/components/rename-pattern-modal.js
  var import_components4 = __toESM(require_components());
  var import_core_data5 = __toESM(require_core_data());
  var import_data7 = __toESM(require_data());
  var import_element5 = __toESM(require_element());
  var import_html_entities2 = __toESM(require_html_entities());
  var import_i18n5 = __toESM(require_i18n());
  var import_notices3 = __toESM(require_notices());
  var import_jsx_runtime5 = __toESM(require_jsx_runtime());
  function RenamePatternModal({
    onClose,
    onError,
    onSuccess,
    pattern,
    ...props
  }) {
    const originalName = (0, import_html_entities2.decodeEntities)(pattern.title);
    const [name, setName] = (0, import_element5.useState)(originalName);
    const [isSaving, setIsSaving] = (0, import_element5.useState)(false);
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
    return /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
      import_components4.Modal,
      {
        title: (0, import_i18n5.__)("Rename"),
        ...props,
        onRequestClose: onClose,
        focusOnMount: "firstContentElement",
        size: "small",
        children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)("form", { onSubmit: onRename, children: /* @__PURE__ */ (0, import_jsx_runtime5.jsxs)(import_components4.__experimentalVStack, { spacing: "5", children: [
          /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
            import_components4.TextControl,
            {
              __nextHasNoMarginBottom: true,
              __next40pxDefaultSize: true,
              label: (0, import_i18n5.__)("Name"),
              value: name,
              onChange: setName,
              required: true
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime5.jsxs)(import_components4.__experimentalHStack, { justify: "right", children: [
            /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
              import_components4.Button,
              {
                __next40pxDefaultSize: true,
                variant: "tertiary",
                onClick: onRequestClose,
                children: (0, import_i18n5.__)("Cancel")
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
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

  // packages/patterns/build-module/components/index.js
  var import_block_editor5 = __toESM(require_block_editor());

  // packages/patterns/build-module/components/pattern-convert-button.js
  var import_blocks2 = __toESM(require_blocks());
  var import_block_editor3 = __toESM(require_block_editor());
  var import_element6 = __toESM(require_element());
  var import_components5 = __toESM(require_components());

  // packages/icons/build-module/library/symbol.js
  var import_primitives = __toESM(require_primitives());
  var import_jsx_runtime6 = __toESM(require_jsx_runtime());
  var symbol_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives.Path, { d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-1 1.4l-5.6 5.6c-.1.1-.3.1-.4 0l-5.6-5.6c-.1-.1-.1-.3 0-.4l5.6-5.6s.1-.1.2-.1.1 0 .2.1l5.6 5.6c.1.1.1.3 0 .4zm-16.6-.4L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z" }) });

  // packages/patterns/build-module/components/pattern-convert-button.js
  var import_data8 = __toESM(require_data());
  var import_core_data6 = __toESM(require_core_data());
  var import_i18n6 = __toESM(require_i18n());
  var import_notices4 = __toESM(require_notices());
  var import_jsx_runtime7 = __toESM(require_jsx_runtime());
  function PatternConvertButton({
    clientIds,
    rootClientId,
    closeBlockSettingsMenu
  }) {
    const { createSuccessNotice } = (0, import_data8.useDispatch)(import_notices4.store);
    const { replaceBlocks, updateBlockAttributes } = (0, import_data8.useDispatch)(import_block_editor3.store);
    const { setEditingPattern: setEditingPattern2 } = unlock((0, import_data8.useDispatch)(store));
    const [isModalOpen, setIsModalOpen] = (0, import_element6.useState)(false);
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
        const isUnsyncedPattern = window?.__experimentalContentOnlyPatternInsertion && blocks.length === 1 && blocks?.[0]?.attributes?.metadata?.patternName;
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
    const getContent = (0, import_element6.useCallback)(
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
        closeBlockSettingsMenu();
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

  // packages/patterns/build-module/components/patterns-manage-button.js
  var import_components6 = __toESM(require_components());
  var import_i18n7 = __toESM(require_i18n());
  var import_blocks3 = __toESM(require_blocks());
  var import_data9 = __toESM(require_data());
  var import_block_editor4 = __toESM(require_block_editor());
  var import_url = __toESM(require_url());
  var import_core_data7 = __toESM(require_core_data());
  var import_jsx_runtime8 = __toESM(require_jsx_runtime());
  function PatternsManageButton({ clientId }) {
    const {
      attributes,
      canDetach,
      isVisible,
      managePatternsUrl,
      isSyncedPattern,
      isUnsyncedPattern
    } = (0, import_data9.useSelect)(
      (select) => {
        const { canRemoveBlock, getBlock } = select(import_block_editor4.store);
        const { canUser } = select(import_core_data7.store);
        const block = getBlock(clientId);
        const _isUnsyncedPattern = window?.__experimentalContentOnlyPatternInsertion && !!block?.attributes?.metadata?.patternName;
        const _isSyncedPattern = !!block && (0, import_blocks3.isReusableBlock)(block) && !!canUser("update", {
          kind: "postType",
          name: "wp_block",
          id: block.attributes.ref
        });
        return {
          attributes: block.attributes,
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
    if (!isVisible) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)(import_jsx_runtime8.Fragment, { children: [
      canDetach && /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(
        import_components6.MenuItem,
        {
          onClick: () => {
            if (isSyncedPattern) {
              convertSyncedPatternToStatic2(clientId);
            }
            if (isUnsyncedPattern) {
              const {
                patternName,
                ...attributesWithoutPatternName
              } = attributes?.metadata ?? {};
              updateBlockAttributes(clientId, {
                metadata: attributesWithoutPatternName
              });
            }
          },
          children: (0, import_i18n7.__)("Disconnect pattern")
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_components6.MenuItem, { href: managePatternsUrl, children: (0, import_i18n7.__)("Manage patterns") })
    ] });
  }
  var patterns_manage_button_default = PatternsManageButton;

  // packages/patterns/build-module/components/index.js
  var import_jsx_runtime9 = __toESM(require_jsx_runtime());
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
          clientId: selectedClientIds[0]
        }
      )
    ] }) });
  }

  // packages/patterns/build-module/components/rename-pattern-category-modal.js
  var import_components7 = __toESM(require_components());
  var import_core_data8 = __toESM(require_core_data());
  var import_data10 = __toESM(require_data());
  var import_element7 = __toESM(require_element());
  var import_html_entities3 = __toESM(require_html_entities());
  var import_i18n8 = __toESM(require_i18n());
  var import_notices5 = __toESM(require_notices());
  var import_a11y = __toESM(require_a11y());
  var import_jsx_runtime10 = __toESM(require_jsx_runtime());
  function RenamePatternCategoryModal({
    category,
    existingCategories,
    onClose,
    onError,
    onSuccess,
    ...props
  }) {
    const id = (0, import_element7.useId)();
    const textControlRef = (0, import_element7.useRef)();
    const [name, setName] = (0, import_element7.useState)((0, import_html_entities3.decodeEntities)(category.name));
    const [isSaving, setIsSaving] = (0, import_element7.useState)(false);
    const [validationMessage, setValidationMessage] = (0, import_element7.useState)(false);
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
        children: /* @__PURE__ */ (0, import_jsx_runtime10.jsx)("form", { onSubmit: onSave, children: /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)(import_components7.__experimentalVStack, { spacing: "5", children: [
          /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)(import_components7.__experimentalVStack, { spacing: "2", children: [
            /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
              import_components7.TextControl,
              {
                ref: textControlRef,
                __nextHasNoMarginBottom: true,
                __next40pxDefaultSize: true,
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
          /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)(import_components7.__experimentalHStack, { justify: "right", children: [
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

  // packages/patterns/build-module/components/pattern-overrides-controls.js
  var import_element9 = __toESM(require_element());
  var import_block_editor6 = __toESM(require_block_editor());
  var import_components9 = __toESM(require_components());
  var import_i18n10 = __toESM(require_i18n());

  // packages/patterns/build-module/components/allow-overrides-modal.js
  var import_components8 = __toESM(require_components());
  var import_i18n9 = __toESM(require_i18n());
  var import_element8 = __toESM(require_element());
  var import_a11y2 = __toESM(require_a11y());
  var import_jsx_runtime11 = __toESM(require_jsx_runtime());
  function AllowOverridesModal({
    placeholder,
    initialName = "",
    onClose,
    onSave
  }) {
    const [editedBlockName, setEditedBlockName] = (0, import_element8.useState)(initialName);
    const descriptionId = (0, import_element8.useId)();
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
            children: /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(import_components8.__experimentalVStack, { spacing: "6", children: [
              /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_components8.__experimentalText, { id: descriptionId, children: (0, import_i18n9.__)(
                "Overrides are changes you make to a block within a synced pattern instance. Use overrides to customize a synced pattern instance to suit its new context. Name this block to specify an override."
              ) }),
              /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(
                import_components8.TextControl,
                {
                  __nextHasNoMarginBottom: true,
                  __next40pxDefaultSize: true,
                  value: editedBlockName,
                  label: (0, import_i18n9.__)("Name"),
                  help: (0, import_i18n9.__)(
                    'For example, if you are creating a recipe pattern, you use "Recipe Title", "Recipe Description", etc.'
                  ),
                  placeholder,
                  onChange: setEditedBlockName
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(import_components8.__experimentalHStack, { justify: "right", children: [
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
    const descriptionId = (0, import_element8.useId)();
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
            children: /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(import_components8.__experimentalVStack, { spacing: "6", children: [
              /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_components8.__experimentalText, { id: descriptionId, children: (0, import_i18n9.__)(
                "Are you sure you want to disable overrides? Disabling overrides will revert all applied overrides for this block throughout instances of this pattern."
              ) }),
              /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(import_components8.__experimentalHStack, { justify: "right", children: [
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

  // packages/patterns/build-module/components/pattern-overrides-controls.js
  var import_jsx_runtime12 = __toESM(require_jsx_runtime());
  function PatternOverridesControls({
    attributes,
    setAttributes,
    name: blockName
  }) {
    const controlId = (0, import_element9.useId)();
    const [showAllowOverridesModal, setShowAllowOverridesModal] = (0, import_element9.useState)(false);
    const [showDisallowOverridesModal, setShowDisallowOverridesModal] = (0, import_element9.useState)(false);
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
          __nextHasNoMarginBottom: true,
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

  // packages/patterns/build-module/components/reset-overrides-control.js
  var import_block_editor7 = __toESM(require_block_editor());
  var import_components10 = __toESM(require_components());
  var import_data11 = __toESM(require_data());
  var import_i18n11 = __toESM(require_i18n());
  var import_jsx_runtime13 = __toESM(require_jsx_runtime());
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

  // packages/patterns/build-module/private-apis.js
  var privateApis = {};
  lock(privateApis, {
    OverridesPanel,
    CreatePatternModal,
    CreatePatternModalContents,
    DuplicatePatternModal,
    isOverridableBlock,
    hasOverridableBlocks,
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
    PATTERN_SYNC_TYPES,
    PARTIAL_SYNCING_SUPPORTED_BLOCKS
  });
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
