/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  privateApis: () => (/* reexport */ privateApis),
  store: () => (/* reexport */ store)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/patterns/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  convertSyncedPatternToStatic: () => (convertSyncedPatternToStatic),
  createPattern: () => (createPattern),
  createPatternFromFile: () => (createPatternFromFile),
  setEditingPattern: () => (setEditingPattern)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/patterns/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  isEditingPattern: () => (selectors_isEditingPattern)
});

;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// ./node_modules/@wordpress/patterns/build-module/store/reducer.js

function isEditingPattern(state = {}, action) {
  if (action?.type === "SET_EDITING_PATTERN") {
    return {
      ...state,
      [action.clientId]: action.isEditing
    };
  }
  return state;
}
var reducer_default = (0,external_wp_data_namespaceObject.combineReducers)({
  isEditingPattern
});


;// external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// external ["wp","coreData"]
const external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// ./node_modules/@wordpress/patterns/build-module/constants.js
const PATTERN_TYPES = {
  theme: "pattern",
  user: "wp_block"
};
const PATTERN_DEFAULT_CATEGORY = "all-patterns";
const PATTERN_USER_CATEGORY = "my-patterns";
const EXCLUDED_PATTERN_SOURCES = [
  "core",
  "pattern-directory/core",
  "pattern-directory/featured"
];
const PATTERN_SYNC_TYPES = {
  full: "fully",
  unsynced: "unsynced"
};
const PARTIAL_SYNCING_SUPPORTED_BLOCKS = {
  "core/paragraph": ["content"],
  "core/heading": ["content"],
  "core/button": ["text", "url", "linkTarget", "rel"],
  "core/image": ["id", "url", "title", "alt", "caption"]
};
const PATTERN_OVERRIDES_BINDING_SOURCE = "core/pattern-overrides";


;// ./node_modules/@wordpress/patterns/build-module/store/actions.js




const createPattern = (title, syncType, content, categories) => async ({ registry }) => {
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
  const updatedRecord = await registry.dispatch(external_wp_coreData_namespaceObject.store).saveEntityRecord("postType", "wp_block", reusableBlock);
  return updatedRecord;
};
const createPatternFromFile = (file, categories) => async ({ dispatch }) => {
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
const convertSyncedPatternToStatic = (clientId) => ({ registry }) => {
  const patternBlock = registry.select(external_wp_blockEditor_namespaceObject.store).getBlock(clientId);
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
            if (!(0,external_wp_blocks_namespaceObject.getBlockType)(block.name)?.attributes[attributeName]) {
              continue;
            }
            block.attributes[attributeName] = value;
          }
        }
      }
      return (0,external_wp_blocks_namespaceObject.cloneBlock)(
        block,
        {
          metadata: metadata && Object.keys(metadata).length > 0 ? metadata : void 0
        },
        cloneBlocksAndRemoveBindings(block.innerBlocks)
      );
    });
  }
  const patternInnerBlocks = registry.select(external_wp_blockEditor_namespaceObject.store).getBlocks(patternBlock.clientId);
  registry.dispatch(external_wp_blockEditor_namespaceObject.store).replaceBlocks(
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


;// ./node_modules/@wordpress/patterns/build-module/store/constants.js
const STORE_NAME = "core/patterns";


;// ./node_modules/@wordpress/patterns/build-module/store/selectors.js
function selectors_isEditingPattern(state, clientId) {
  return state.isEditingPattern[clientId];
}


;// external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// ./node_modules/@wordpress/patterns/build-module/lock-unlock.js

const { lock, unlock } = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/patterns"
);


;// ./node_modules/@wordpress/patterns/build-module/store/index.js






const storeConfig = {
  reducer: reducer_default
};
const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  ...storeConfig
});
(0,external_wp_data_namespaceObject.register)(store);
unlock(store).registerPrivateActions(actions_namespaceObject);
unlock(store).registerPrivateSelectors(selectors_namespaceObject);


;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// ./node_modules/@wordpress/patterns/build-module/api/index.js

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


;// ./node_modules/@wordpress/patterns/build-module/components/overrides-panel.js








const { BlockQuickNavigation } = unlock(external_wp_blockEditor_namespaceObject.privateApis);
function OverridesPanel() {
  const allClientIds = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => select(external_wp_blockEditor_namespaceObject.store).getClientIdsWithDescendants(),
    []
  );
  const { getBlock } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_blockEditor_namespaceObject.store);
  const clientIdsWithOverrides = (0,external_wp_element_namespaceObject.useMemo)(
    () => allClientIds.filter((clientId) => {
      const block = getBlock(clientId);
      return isOverridableBlock(block);
    }),
    [allClientIds, getBlock]
  );
  if (!clientIdsWithOverrides?.length) {
    return null;
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.PanelBody, { title: (0,external_wp_i18n_namespaceObject.__)("Overrides"), children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(BlockQuickNavigation, { clientIds: clientIdsWithOverrides }) });
}


;// external ["wp","notices"]
const external_wp_notices_namespaceObject = window["wp"]["notices"];
;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// external ["wp","htmlEntities"]
const external_wp_htmlEntities_namespaceObject = window["wp"]["htmlEntities"];
;// ./node_modules/@wordpress/patterns/build-module/components/category-selector.js






const unescapeString = (arg) => {
  return (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(arg);
};
const CATEGORY_SLUG = "wp_pattern_category";
function CategorySelector({
  categoryTerms,
  onChange,
  categoryMap
}) {
  const [search, setSearch] = (0,external_wp_element_namespaceObject.useState)("");
  const debouncedSearch = (0,external_wp_compose_namespaceObject.useDebounce)(setSearch, 500);
  const suggestions = (0,external_wp_element_namespaceObject.useMemo)(() => {
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
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.FormTokenField,
    {
      className: "patterns-menu-items__convert-modal-categories",
      value: categoryTerms,
      suggestions,
      onChange: handleChange,
      onInputChange: debouncedSearch,
      label: (0,external_wp_i18n_namespaceObject.__)("Categories"),
      tokenizeOnBlur: true,
      __experimentalExpandOnFocus: true,
      __next40pxDefaultSize: true,
      __nextHasNoMarginBottom: true
    }
  );
}


;// ./node_modules/@wordpress/patterns/build-module/private-hooks.js




function useAddPatternCategory() {
  const { saveEntityRecord, invalidateResolution } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const { corePatternCategories, userPatternCategories } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const { getUserPatternCategories, getBlockPatternCategories } = select(external_wp_coreData_namespaceObject.store);
      return {
        corePatternCategories: getBlockPatternCategories(),
        userPatternCategories: getUserPatternCategories()
      };
    },
    []
  );
  const categoryMap = (0,external_wp_element_namespaceObject.useMemo)(() => {
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


;// ./node_modules/@wordpress/patterns/build-module/components/create-pattern-modal.js












function CreatePatternModal({
  className = "patterns-menu-items__convert-modal",
  modalTitle,
  ...restProps
}) {
  const defaultModalTitle = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => select(external_wp_coreData_namespaceObject.store).getPostType(PATTERN_TYPES.user)?.labels?.add_new_item,
    []
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Modal,
    {
      title: modalTitle || defaultModalTitle,
      onRequestClose: restProps.onClose,
      overlayClassName: className,
      focusOnMount: "firstContentElement",
      size: "small",
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(CreatePatternModalContents, { ...restProps })
    }
  );
}
function CreatePatternModalContents({
  confirmLabel = (0,external_wp_i18n_namespaceObject.__)("Add"),
  defaultCategories = [],
  content,
  onClose,
  onError,
  onSuccess,
  defaultSyncType = PATTERN_SYNC_TYPES.full,
  defaultTitle = ""
}) {
  const [syncType, setSyncType] = (0,external_wp_element_namespaceObject.useState)(defaultSyncType);
  const [categoryTerms, setCategoryTerms] = (0,external_wp_element_namespaceObject.useState)(defaultCategories);
  const [title, setTitle] = (0,external_wp_element_namespaceObject.useState)(defaultTitle);
  const [isSaving, setIsSaving] = (0,external_wp_element_namespaceObject.useState)(false);
  const { createPattern } = unlock((0,external_wp_data_namespaceObject.useDispatch)(store));
  const { createErrorNotice } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
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
      const newPattern = await createPattern(
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
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    "form",
    {
      onSubmit: (event) => {
        event.preventDefault();
        onCreate(title, syncType);
      },
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalVStack, { spacing: "5", children: [
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          external_wp_components_namespaceObject.TextControl,
          {
            label: (0,external_wp_i18n_namespaceObject.__)("Name"),
            value: title,
            onChange: setTitle,
            placeholder: (0,external_wp_i18n_namespaceObject.__)("My pattern"),
            className: "patterns-create-modal__name-input",
            __nextHasNoMarginBottom: true,
            __next40pxDefaultSize: true
          }
        ),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          CategorySelector,
          {
            categoryTerms,
            onChange: setCategoryTerms,
            categoryMap
          }
        ),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          external_wp_components_namespaceObject.ToggleControl,
          {
            __nextHasNoMarginBottom: true,
            label: (0,external_wp_i18n_namespaceObject._x)("Synced", "pattern (singular)"),
            help: (0,external_wp_i18n_namespaceObject.__)(
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
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalHStack, { justify: "right", children: [
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            external_wp_components_namespaceObject.Button,
            {
              __next40pxDefaultSize: true,
              variant: "tertiary",
              onClick: () => {
                onClose();
                setTitle("");
              },
              children: (0,external_wp_i18n_namespaceObject.__)("Cancel")
            }
          ),
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            external_wp_components_namespaceObject.Button,
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


;// ./node_modules/@wordpress/patterns/build-module/components/duplicate-pattern-modal.js







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
  const { createSuccessNotice } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const categories = (0,external_wp_data_namespaceObject.useSelect)((select) => {
    const { getUserPatternCategories, getBlockPatternCategories } = select(external_wp_coreData_namespaceObject.store);
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
    defaultTitle: (0,external_wp_i18n_namespaceObject.sprintf)(
      /* translators: %s: Existing pattern title */
      (0,external_wp_i18n_namespaceObject._x)("%s (Copy)", "pattern"),
      typeof pattern.title === "string" ? pattern.title : pattern.title.raw
    ),
    onSuccess: ({ pattern: newPattern }) => {
      createSuccessNotice(
        (0,external_wp_i18n_namespaceObject.sprintf)(
          // translators: %s: The new pattern's title e.g. 'Call to action (copy)'.
          (0,external_wp_i18n_namespaceObject._x)('"%s" duplicated.', "pattern"),
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
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    CreatePatternModal,
    {
      modalTitle: (0,external_wp_i18n_namespaceObject.__)("Duplicate pattern"),
      confirmLabel: (0,external_wp_i18n_namespaceObject.__)("Duplicate"),
      onClose,
      onError: onClose,
      ...duplicatedProps
    }
  );
}


;// ./node_modules/@wordpress/patterns/build-module/components/rename-pattern-modal.js








function RenamePatternModal({
  onClose,
  onError,
  onSuccess,
  pattern,
  ...props
}) {
  const originalName = (0,external_wp_htmlEntities_namespaceObject.decodeEntities)(pattern.title);
  const [name, setName] = (0,external_wp_element_namespaceObject.useState)(originalName);
  const [isSaving, setIsSaving] = (0,external_wp_element_namespaceObject.useState)(false);
  const {
    editEntityRecord,
    __experimentalSaveSpecifiedEntityEdits: saveSpecifiedEntityEdits
  } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const { createSuccessNotice, createErrorNotice } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
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
      createSuccessNotice((0,external_wp_i18n_namespaceObject.__)("Pattern renamed"), {
        type: "snackbar",
        id: "pattern-update"
      });
    } catch (error) {
      onError?.();
      const errorMessage = error.message && error.code !== "unknown_error" ? error.message : (0,external_wp_i18n_namespaceObject.__)("An error occurred while renaming the pattern.");
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
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Modal,
    {
      title: (0,external_wp_i18n_namespaceObject.__)("Rename"),
      ...props,
      onRequestClose: onClose,
      focusOnMount: "firstContentElement",
      size: "small",
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("form", { onSubmit: onRename, children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalVStack, { spacing: "5", children: [
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          external_wp_components_namespaceObject.TextControl,
          {
            __nextHasNoMarginBottom: true,
            __next40pxDefaultSize: true,
            label: (0,external_wp_i18n_namespaceObject.__)("Name"),
            value: name,
            onChange: setName,
            required: true
          }
        ),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalHStack, { justify: "right", children: [
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            external_wp_components_namespaceObject.Button,
            {
              __next40pxDefaultSize: true,
              variant: "tertiary",
              onClick: onRequestClose,
              children: (0,external_wp_i18n_namespaceObject.__)("Cancel")
            }
          ),
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            external_wp_components_namespaceObject.Button,
            {
              __next40pxDefaultSize: true,
              variant: "primary",
              type: "submit",
              children: (0,external_wp_i18n_namespaceObject.__)("Save")
            }
          )
        ] })
      ] }) })
    }
  );
}


;// external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// ./node_modules/@wordpress/icons/build-module/library/symbol.js


var symbol_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-1 1.4l-5.6 5.6c-.1.1-.3.1-.4 0l-5.6-5.6c-.1-.1-.1-.3 0-.4l5.6-5.6s.1-.1.2-.1.1 0 .2.1l5.6 5.6c.1.1.1.3 0 .4zm-16.6-.4L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z" }) });


;// ./node_modules/@wordpress/patterns/build-module/components/pattern-convert-button.js














function PatternConvertButton({
  clientIds,
  rootClientId,
  closeBlockSettingsMenu
}) {
  const { createSuccessNotice } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const { replaceBlocks, updateBlockAttributes } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const { setEditingPattern } = unlock((0,external_wp_data_namespaceObject.useDispatch)(store));
  const [isModalOpen, setIsModalOpen] = (0,external_wp_element_namespaceObject.useState)(false);
  const { getBlockAttributes } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_blockEditor_namespaceObject.store);
  const canConvert = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const { canUser } = select(external_wp_coreData_namespaceObject.store);
      const {
        getBlocksByClientId: getBlocksByClientId2,
        canInsertBlockType,
        getBlockRootClientId
      } = select(external_wp_blockEditor_namespaceObject.store);
      const rootId = rootClientId || (clientIds.length > 0 ? getBlockRootClientId(clientIds[0]) : void 0);
      const blocks = getBlocksByClientId2(clientIds) ?? [];
      const hasReusableBlockSupport = (blockName) => {
        const blockType = (0,external_wp_blocks_namespaceObject.getBlockType)(blockName);
        const hasParent = blockType && "parent" in blockType;
        return (0,external_wp_blocks_namespaceObject.hasBlockSupport)(blockName, "reusable", !hasParent);
      };
      const isSyncedPattern = blocks.length === 1 && blocks[0] && (0,external_wp_blocks_namespaceObject.isReusableBlock)(blocks[0]) && !!select(external_wp_coreData_namespaceObject.store).getEntityRecord(
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
  const { getBlocksByClientId } = (0,external_wp_data_namespaceObject.useSelect)(external_wp_blockEditor_namespaceObject.store);
  const getContent = (0,external_wp_element_namespaceObject.useCallback)(
    () => (0,external_wp_blocks_namespaceObject.serialize)(getBlocksByClientId(clientIds)),
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
      const newBlock = (0,external_wp_blocks_namespaceObject.createBlock)("core/block", {
        ref: pattern.id
      });
      replaceBlocks(clientIds, newBlock);
      setEditingPattern(newBlock.clientId, true);
      closeBlockSettingsMenu();
    }
    createSuccessNotice(
      pattern.wp_pattern_sync_status === PATTERN_SYNC_TYPES.unsynced ? (0,external_wp_i18n_namespaceObject.sprintf)(
        // translators: %s: the name the user has given to the pattern.
        (0,external_wp_i18n_namespaceObject.__)("Unsynced pattern created: %s"),
        pattern.title.raw
      ) : (0,external_wp_i18n_namespaceObject.sprintf)(
        // translators: %s: the name the user has given to the pattern.
        (0,external_wp_i18n_namespaceObject.__)("Synced pattern created: %s"),
        pattern.title.raw
      ),
      {
        type: "snackbar",
        id: "convert-to-pattern-success"
      }
    );
    setIsModalOpen(false);
  };
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_components_namespaceObject.MenuItem,
      {
        icon: symbol_default,
        onClick: () => setIsModalOpen(true),
        "aria-expanded": isModalOpen,
        "aria-haspopup": "dialog",
        children: (0,external_wp_i18n_namespaceObject.__)("Create pattern")
      }
    ),
    isModalOpen && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
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


;// external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// ./node_modules/@wordpress/patterns/build-module/components/patterns-manage-button.js










function PatternsManageButton({ clientId }) {
  const {
    attributes,
    canDetach,
    isVisible,
    managePatternsUrl,
    isSyncedPattern,
    isUnsyncedPattern
  } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const { canRemoveBlock, getBlock } = select(external_wp_blockEditor_namespaceObject.store);
      const { canUser } = select(external_wp_coreData_namespaceObject.store);
      const block = getBlock(clientId);
      const _isUnsyncedPattern = window?.__experimentalContentOnlyPatternInsertion && !!block?.attributes?.metadata?.patternName;
      const _isSyncedPattern = !!block && (0,external_wp_blocks_namespaceObject.isReusableBlock)(block) && !!canUser("update", {
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
        }) ? (0,external_wp_url_namespaceObject.addQueryArgs)("site-editor.php", {
          p: "/pattern"
        }) : (0,external_wp_url_namespaceObject.addQueryArgs)("edit.php", {
          post_type: "wp_block"
        })
      };
    },
    [clientId]
  );
  const { updateBlockAttributes } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_blockEditor_namespaceObject.store);
  const { convertSyncedPatternToStatic } = unlock(
    (0,external_wp_data_namespaceObject.useDispatch)(store)
  );
  if (!isVisible) {
    return null;
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    canDetach && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_components_namespaceObject.MenuItem,
      {
        onClick: () => {
          if (isSyncedPattern) {
            convertSyncedPatternToStatic(clientId);
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
        children: (0,external_wp_i18n_namespaceObject.__)("Detach")
      }
    ),
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuItem, { href: managePatternsUrl, children: (0,external_wp_i18n_namespaceObject.__)("Manage patterns") })
  ] });
}
var patterns_manage_button_default = PatternsManageButton;


;// ./node_modules/@wordpress/patterns/build-module/components/index.js




function PatternsMenuItems({ rootClientId }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockSettingsMenuControls, { children: ({ selectedClientIds, onClose }) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      PatternConvertButton,
      {
        clientIds: selectedClientIds,
        rootClientId,
        closeBlockSettingsMenu: onClose
      }
    ),
    selectedClientIds.length === 1 && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      patterns_manage_button_default,
      {
        clientId: selectedClientIds[0]
      }
    )
  ] }) });
}


;// external ["wp","a11y"]
const external_wp_a11y_namespaceObject = window["wp"]["a11y"];
;// ./node_modules/@wordpress/patterns/build-module/components/rename-pattern-category-modal.js










function RenamePatternCategoryModal({
  category,
  existingCategories,
  onClose,
  onError,
  onSuccess,
  ...props
}) {
  const id = (0,external_wp_element_namespaceObject.useId)();
  const textControlRef = (0,external_wp_element_namespaceObject.useRef)();
  const [name, setName] = (0,external_wp_element_namespaceObject.useState)((0,external_wp_htmlEntities_namespaceObject.decodeEntities)(category.name));
  const [isSaving, setIsSaving] = (0,external_wp_element_namespaceObject.useState)(false);
  const [validationMessage, setValidationMessage] = (0,external_wp_element_namespaceObject.useState)(false);
  const validationMessageId = validationMessage ? `patterns-rename-pattern-category-modal__validation-message-${id}` : void 0;
  const { saveEntityRecord, invalidateResolution } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_coreData_namespaceObject.store);
  const { createErrorNotice, createSuccessNotice } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
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
      const message = (0,external_wp_i18n_namespaceObject.__)("Please enter a new name for this category.");
      (0,external_wp_a11y_namespaceObject.speak)(message, "assertive");
      setValidationMessage(message);
      textControlRef.current?.focus();
      return;
    }
    if (existingCategories.patternCategories.find((existingCategory) => {
      return existingCategory.id !== category.id && existingCategory.label.toLowerCase() === name.toLowerCase();
    })) {
      const message = (0,external_wp_i18n_namespaceObject.__)(
        "This category already exists. Please use a different name."
      );
      (0,external_wp_a11y_namespaceObject.speak)(message, "assertive");
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
      createSuccessNotice((0,external_wp_i18n_namespaceObject.__)("Pattern category renamed."), {
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
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Modal,
    {
      title: (0,external_wp_i18n_namespaceObject.__)("Rename"),
      onRequestClose,
      ...props,
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)("form", { onSubmit: onSave, children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalVStack, { spacing: "5", children: [
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalVStack, { spacing: "2", children: [
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            external_wp_components_namespaceObject.TextControl,
            {
              ref: textControlRef,
              __nextHasNoMarginBottom: true,
              __next40pxDefaultSize: true,
              label: (0,external_wp_i18n_namespaceObject.__)("Name"),
              value: name,
              onChange,
              "aria-describedby": validationMessageId,
              required: true
            }
          ),
          validationMessage && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            "span",
            {
              className: "patterns-rename-pattern-category-modal__validation-message",
              id: validationMessageId,
              children: validationMessage
            }
          )
        ] }),
        /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalHStack, { justify: "right", children: [
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            external_wp_components_namespaceObject.Button,
            {
              __next40pxDefaultSize: true,
              variant: "tertiary",
              onClick: onRequestClose,
              children: (0,external_wp_i18n_namespaceObject.__)("Cancel")
            }
          ),
          /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
            external_wp_components_namespaceObject.Button,
            {
              __next40pxDefaultSize: true,
              variant: "primary",
              type: "submit",
              "aria-disabled": !name || name === category.name || isSaving,
              isBusy: isSaving,
              children: (0,external_wp_i18n_namespaceObject.__)("Save")
            }
          )
        ] })
      ] }) })
    }
  );
}


;// ./node_modules/@wordpress/patterns/build-module/components/allow-overrides-modal.js





function AllowOverridesModal({
  placeholder,
  initialName = "",
  onClose,
  onSave
}) {
  const [editedBlockName, setEditedBlockName] = (0,external_wp_element_namespaceObject.useState)(initialName);
  const descriptionId = (0,external_wp_element_namespaceObject.useId)();
  const isNameValid = !!editedBlockName.trim();
  const handleSubmit = () => {
    if (editedBlockName !== initialName) {
      const message = (0,external_wp_i18n_namespaceObject.sprintf)(
        /* translators: %s: new name/label for the block */
        (0,external_wp_i18n_namespaceObject.__)('Block name changed to: "%s".'),
        editedBlockName
      );
      (0,external_wp_a11y_namespaceObject.speak)(message, "assertive");
    }
    onSave(editedBlockName);
    onClose();
  };
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Modal,
    {
      title: (0,external_wp_i18n_namespaceObject.__)("Enable overrides"),
      onRequestClose: onClose,
      focusOnMount: "firstContentElement",
      aria: { describedby: descriptionId },
      size: "small",
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        "form",
        {
          onSubmit: (event) => {
            event.preventDefault();
            if (!isNameValid) {
              return;
            }
            handleSubmit();
          },
          children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalVStack, { spacing: "6", children: [
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalText, { id: descriptionId, children: (0,external_wp_i18n_namespaceObject.__)(
              "Overrides are changes you make to a block within a synced pattern instance. Use overrides to customize a synced pattern instance to suit its new context. Name this block to specify an override."
            ) }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
              external_wp_components_namespaceObject.TextControl,
              {
                __nextHasNoMarginBottom: true,
                __next40pxDefaultSize: true,
                value: editedBlockName,
                label: (0,external_wp_i18n_namespaceObject.__)("Name"),
                help: (0,external_wp_i18n_namespaceObject.__)(
                  'For example, if you are creating a recipe pattern, you use "Recipe Title", "Recipe Description", etc.'
                ),
                placeholder,
                onChange: setEditedBlockName
              }
            ),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalHStack, { justify: "right", children: [
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
                external_wp_components_namespaceObject.Button,
                {
                  __next40pxDefaultSize: true,
                  variant: "tertiary",
                  onClick: onClose,
                  children: (0,external_wp_i18n_namespaceObject.__)("Cancel")
                }
              ),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
                external_wp_components_namespaceObject.Button,
                {
                  __next40pxDefaultSize: true,
                  "aria-disabled": !isNameValid,
                  variant: "primary",
                  type: "submit",
                  children: (0,external_wp_i18n_namespaceObject.__)("Enable")
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
  const descriptionId = (0,external_wp_element_namespaceObject.useId)();
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.Modal,
    {
      title: (0,external_wp_i18n_namespaceObject.__)("Disable overrides"),
      onRequestClose: onClose,
      aria: { describedby: descriptionId },
      size: "small",
      children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        "form",
        {
          onSubmit: (event) => {
            event.preventDefault();
            onSave();
            onClose();
          },
          children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalVStack, { spacing: "6", children: [
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalText, { id: descriptionId, children: (0,external_wp_i18n_namespaceObject.__)(
              "Are you sure you want to disable overrides? Disabling overrides will revert all applied overrides for this block throughout instances of this pattern."
            ) }),
            /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalHStack, { justify: "right", children: [
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
                external_wp_components_namespaceObject.Button,
                {
                  __next40pxDefaultSize: true,
                  variant: "tertiary",
                  onClick: onClose,
                  children: (0,external_wp_i18n_namespaceObject.__)("Cancel")
                }
              ),
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
                external_wp_components_namespaceObject.Button,
                {
                  __next40pxDefaultSize: true,
                  variant: "primary",
                  type: "submit",
                  children: (0,external_wp_i18n_namespaceObject.__)("Disable")
                }
              )
            ] })
          ] })
        }
      )
    }
  );
}


;// ./node_modules/@wordpress/patterns/build-module/components/pattern-overrides-controls.js







function PatternOverridesControls({
  attributes,
  setAttributes,
  name: blockName
}) {
  const controlId = (0,external_wp_element_namespaceObject.useId)();
  const [showAllowOverridesModal, setShowAllowOverridesModal] = (0,external_wp_element_namespaceObject.useState)(false);
  const [showDisallowOverridesModal, setShowDisallowOverridesModal] = (0,external_wp_element_namespaceObject.useState)(false);
  const hasName = !!attributes.metadata?.name;
  const defaultBindings = attributes.metadata?.bindings?.__default;
  const hasOverrides = hasName && defaultBindings?.source === PATTERN_OVERRIDES_BINDING_SOURCE;
  const isConnectedToOtherSources = defaultBindings?.source && defaultBindings.source !== PATTERN_OVERRIDES_BINDING_SOURCE;
  const { updateBlockBindings } = (0,external_wp_blockEditor_namespaceObject.useBlockBindingsUtils)();
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
  const helpText = !hasOverrides && hasUnsupportedImageAttributes ? (0,external_wp_i18n_namespaceObject.__)(
    `Overrides currently don't support image links. Remove the link first before enabling overrides.`
  ) : (0,external_wp_i18n_namespaceObject.__)(
    "Allow changes to this block throughout instances of this pattern."
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.InspectorControls, { group: "advanced", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_components_namespaceObject.BaseControl,
      {
        __nextHasNoMarginBottom: true,
        id: controlId,
        label: (0,external_wp_i18n_namespaceObject.__)("Overrides"),
        help: helpText,
        children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          external_wp_components_namespaceObject.Button,
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
            children: hasOverrides ? (0,external_wp_i18n_namespaceObject.__)("Disable overrides") : (0,external_wp_i18n_namespaceObject.__)("Enable overrides")
          }
        )
      }
    ) }),
    showAllowOverridesModal && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      AllowOverridesModal,
      {
        initialName: attributes.metadata?.name,
        onClose: () => setShowAllowOverridesModal(false),
        onSave: (newName) => {
          updateBindings(true, newName);
        }
      }
    ),
    showDisallowOverridesModal && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      DisallowOverridesModal,
      {
        onClose: () => setShowDisallowOverridesModal(false),
        onSave: () => updateBindings(false)
      }
    )
  ] });
}
var pattern_overrides_controls_default = PatternOverridesControls;


;// ./node_modules/@wordpress/patterns/build-module/components/reset-overrides-control.js





const CONTENT = "content";
function ResetOverridesControl(props) {
  const name = props.attributes.metadata?.name;
  const registry = (0,external_wp_data_namespaceObject.useRegistry)();
  const isOverridden = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      if (!name) {
        return;
      }
      const { getBlockAttributes, getBlockParentsByBlockName } = select(external_wp_blockEditor_namespaceObject.store);
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
    const { getBlockAttributes, getBlockParentsByBlockName } = registry.select(external_wp_blockEditor_namespaceObject.store);
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
    const { updateBlockAttributes, __unstableMarkLastChangeAsPersistent } = registry.dispatch(external_wp_blockEditor_namespaceObject.store);
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
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.__unstableBlockToolbarLastItem, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarGroup, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarButton, { onClick, disabled: !isOverridden, children: (0,external_wp_i18n_namespaceObject.__)("Reset") }) }) });
}


;// ./node_modules/@wordpress/icons/build-module/library/copy.js


var copy_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
  external_wp_primitives_namespaceObject.Path,
  {
    fillRule: "evenodd",
    clipRule: "evenodd",
    d: "M5 4.5h11a.5.5 0 0 1 .5.5v11a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V5a.5.5 0 0 1 .5-.5ZM3 5a2 2 0 0 1 2-2h11a2 2 0 0 1 2 2v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5Zm17 3v10.75c0 .69-.56 1.25-1.25 1.25H6v1.5h12.75a2.75 2.75 0 0 0 2.75-2.75V8H20Z"
  }
) });


;// ./node_modules/@wordpress/patterns/build-module/components/pattern-overrides-block-controls.js










const { useBlockDisplayTitle } = unlock(external_wp_blockEditor_namespaceObject.privateApis);
function PatternOverridesToolbarIndicator({ clientIds }) {
  const isSingleBlockSelected = clientIds.length === 1;
  const { icon, firstBlockName } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const { getBlockAttributes, getBlockNamesByClientId } = select(external_wp_blockEditor_namespaceObject.store);
      const { getBlockType, getActiveBlockVariation } = select(external_wp_blocks_namespaceObject.store);
      const blockTypeNames = getBlockNamesByClientId(clientIds);
      const _firstBlockTypeName = blockTypeNames[0];
      const firstBlockType = getBlockType(_firstBlockTypeName);
      let _icon;
      if (isSingleBlockSelected) {
        const match = getActiveBlockVariation(
          _firstBlockTypeName,
          getBlockAttributes(clientIds[0])
        );
        _icon = match?.icon || firstBlockType.icon;
      } else {
        const isSelectionOfSameType = new Set(blockTypeNames).size === 1;
        _icon = isSelectionOfSameType ? firstBlockType.icon : copy_default;
      }
      return {
        icon: _icon,
        firstBlockName: getBlockAttributes(clientIds[0]).metadata.name
      };
    },
    [clientIds, isSingleBlockSelected]
  );
  const firstBlockTitle = useBlockDisplayTitle({
    clientId: clientIds[0],
    maximumLength: 35
  });
  const blockDescription = isSingleBlockSelected ? (0,external_wp_i18n_namespaceObject.sprintf)(
    /* translators: 1: The block type's name. 2: The block's user-provided name (the same as the override name). */
    (0,external_wp_i18n_namespaceObject.__)('This %1$s is editable using the "%2$s" override.'),
    firstBlockTitle.toLowerCase(),
    firstBlockName
  ) : (0,external_wp_i18n_namespaceObject.__)("These blocks are editable using overrides.");
  const descriptionId = (0,external_wp_element_namespaceObject.useId)();
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.ToolbarItem, { children: (toggleProps) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
    external_wp_components_namespaceObject.DropdownMenu,
    {
      className: "patterns-pattern-overrides-toolbar-indicator",
      label: firstBlockTitle,
      popoverProps: {
        placement: "bottom-start",
        className: "patterns-pattern-overrides-toolbar-indicator__popover"
      },
      icon: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
        external_wp_blockEditor_namespaceObject.BlockIcon,
        {
          icon,
          className: "patterns-pattern-overrides-toolbar-indicator-icon",
          showColors: true
        }
      ) }),
      toggleProps: {
        description: blockDescription,
        ...toggleProps
      },
      menuProps: {
        orientation: "both",
        "aria-describedby": descriptionId
      },
      children: () => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.__experimentalText, { id: descriptionId, children: blockDescription })
    }
  ) });
}
function PatternOverridesBlockControls() {
  const { clientIds, hasPatternOverrides, hasParentPattern } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const {
        getBlockAttributes,
        getSelectedBlockClientIds,
        getBlockParentsByBlockName
      } = select(external_wp_blockEditor_namespaceObject.store);
      const selectedClientIds = getSelectedBlockClientIds();
      const _hasPatternOverrides = selectedClientIds.every(
        (clientId) => Object.values(
          getBlockAttributes(clientId)?.metadata?.bindings ?? {}
        ).some(
          (binding) => binding?.source === PATTERN_OVERRIDES_BINDING_SOURCE
        )
      );
      const _hasParentPattern = selectedClientIds.every(
        (clientId) => getBlockParentsByBlockName(clientId, "core/block", true).length > 0
      );
      return {
        clientIds: selectedClientIds,
        hasPatternOverrides: _hasPatternOverrides,
        hasParentPattern: _hasParentPattern
      };
    },
    []
  );
  return hasPatternOverrides && hasParentPattern ? /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockControls, { group: "parent", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(PatternOverridesToolbarIndicator, { clientIds }) }) : null;
}


;// ./node_modules/@wordpress/patterns/build-module/private-apis.js













const privateApis = {};
lock(privateApis, {
  OverridesPanel: OverridesPanel,
  CreatePatternModal: CreatePatternModal,
  CreatePatternModalContents: CreatePatternModalContents,
  DuplicatePatternModal: DuplicatePatternModal,
  isOverridableBlock: isOverridableBlock,
  hasOverridableBlocks: hasOverridableBlocks,
  useDuplicatePatternProps: useDuplicatePatternProps,
  RenamePatternModal: RenamePatternModal,
  PatternsMenuItems: PatternsMenuItems,
  RenamePatternCategoryModal: RenamePatternCategoryModal,
  PatternOverridesControls: pattern_overrides_controls_default,
  ResetOverridesControl: ResetOverridesControl,
  PatternOverridesBlockControls: PatternOverridesBlockControls,
  useAddPatternCategory: useAddPatternCategory,
  PATTERN_TYPES: PATTERN_TYPES,
  PATTERN_DEFAULT_CATEGORY: PATTERN_DEFAULT_CATEGORY,
  PATTERN_USER_CATEGORY: PATTERN_USER_CATEGORY,
  EXCLUDED_PATTERN_SOURCES: EXCLUDED_PATTERN_SOURCES,
  PATTERN_SYNC_TYPES: PATTERN_SYNC_TYPES,
  PARTIAL_SYNCING_SUPPORTED_BLOCKS: PARTIAL_SYNCING_SUPPORTED_BLOCKS
});


;// ./node_modules/@wordpress/patterns/build-module/index.js




(window.wp = window.wp || {}).patterns = __webpack_exports__;
/******/ })()
;