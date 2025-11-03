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
  ReusableBlocksMenuItems: () => (/* reexport */ ReusableBlocksMenuItems),
  store: () => (/* reexport */ store)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/reusable-blocks/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  __experimentalConvertBlockToStatic: () => (__experimentalConvertBlockToStatic),
  __experimentalConvertBlocksToReusable: () => (__experimentalConvertBlocksToReusable),
  __experimentalDeleteReusableBlock: () => (__experimentalDeleteReusableBlock),
  __experimentalSetEditingReusableBlock: () => (__experimentalSetEditingReusableBlock)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/reusable-blocks/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  __experimentalIsEditingReusableBlock: () => (__experimentalIsEditingReusableBlock)
});

;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
;// external ["wp","blocks"]
const external_wp_blocks_namespaceObject = window["wp"]["blocks"];
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// ./node_modules/@wordpress/reusable-blocks/build-module/store/actions.js



const __experimentalConvertBlockToStatic = (clientId) => ({ registry }) => {
  const oldBlock = registry.select(external_wp_blockEditor_namespaceObject.store).getBlock(clientId);
  const reusableBlock = registry.select("core").getEditedEntityRecord(
    "postType",
    "wp_block",
    oldBlock.attributes.ref
  );
  const newBlocks = (0,external_wp_blocks_namespaceObject.parse)(
    typeof reusableBlock.content === "function" ? reusableBlock.content(reusableBlock) : reusableBlock.content
  );
  registry.dispatch(external_wp_blockEditor_namespaceObject.store).replaceBlocks(oldBlock.clientId, newBlocks);
};
const __experimentalConvertBlocksToReusable = (clientIds, title, syncType) => async ({ registry, dispatch }) => {
  const meta = syncType === "unsynced" ? {
    wp_pattern_sync_status: syncType
  } : void 0;
  const reusableBlock = {
    title: title || (0,external_wp_i18n_namespaceObject.__)("Untitled pattern block"),
    content: (0,external_wp_blocks_namespaceObject.serialize)(
      registry.select(external_wp_blockEditor_namespaceObject.store).getBlocksByClientId(clientIds)
    ),
    status: "publish",
    meta
  };
  const updatedRecord = await registry.dispatch("core").saveEntityRecord("postType", "wp_block", reusableBlock);
  if (syncType === "unsynced") {
    return;
  }
  const newBlock = (0,external_wp_blocks_namespaceObject.createBlock)("core/block", {
    ref: updatedRecord.id
  });
  registry.dispatch(external_wp_blockEditor_namespaceObject.store).replaceBlocks(clientIds, newBlock);
  dispatch.__experimentalSetEditingReusableBlock(
    newBlock.clientId,
    true
  );
};
const __experimentalDeleteReusableBlock = (id) => async ({ registry }) => {
  const reusableBlock = registry.select("core").getEditedEntityRecord("postType", "wp_block", id);
  if (!reusableBlock) {
    return;
  }
  const allBlocks = registry.select(external_wp_blockEditor_namespaceObject.store).getBlocks();
  const associatedBlocks = allBlocks.filter(
    (block) => (0,external_wp_blocks_namespaceObject.isReusableBlock)(block) && block.attributes.ref === id
  );
  const associatedBlockClientIds = associatedBlocks.map(
    (block) => block.clientId
  );
  if (associatedBlockClientIds.length) {
    registry.dispatch(external_wp_blockEditor_namespaceObject.store).removeBlocks(associatedBlockClientIds);
  }
  await registry.dispatch("core").deleteEntityRecord("postType", "wp_block", id);
};
function __experimentalSetEditingReusableBlock(clientId, isEditing) {
  return {
    type: "SET_EDITING_REUSABLE_BLOCK",
    clientId,
    isEditing
  };
}


;// ./node_modules/@wordpress/reusable-blocks/build-module/store/reducer.js

function isEditingReusableBlock(state = {}, action) {
  if (action?.type === "SET_EDITING_REUSABLE_BLOCK") {
    return {
      ...state,
      [action.clientId]: action.isEditing
    };
  }
  return state;
}
var reducer_default = (0,external_wp_data_namespaceObject.combineReducers)({
  isEditingReusableBlock
});


;// ./node_modules/@wordpress/reusable-blocks/build-module/store/selectors.js
function __experimentalIsEditingReusableBlock(state, clientId) {
  return state.isEditingReusableBlock[clientId];
}


;// ./node_modules/@wordpress/reusable-blocks/build-module/store/index.js




const STORE_NAME = "core/reusable-blocks";
const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  actions: actions_namespaceObject,
  reducer: reducer_default,
  selectors: selectors_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);


;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","components"]
const external_wp_components_namespaceObject = window["wp"]["components"];
;// external ["wp","primitives"]
const external_wp_primitives_namespaceObject = window["wp"]["primitives"];
;// ./node_modules/@wordpress/icons/build-module/library/symbol.js


var symbol_default = /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_primitives_namespaceObject.Path, { d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-1 1.4l-5.6 5.6c-.1.1-.3.1-.4 0l-5.6-5.6c-.1-.1-.1-.3 0-.4l5.6-5.6s.1-.1.2-.1.1 0 .2.1l5.6 5.6c.1.1.1.3 0 .4zm-16.6-.4L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z" }) });


;// external ["wp","notices"]
const external_wp_notices_namespaceObject = window["wp"]["notices"];
;// external ["wp","coreData"]
const external_wp_coreData_namespaceObject = window["wp"]["coreData"];
;// ./node_modules/@wordpress/reusable-blocks/build-module/components/reusable-blocks-menu-items/reusable-block-convert-button.js











function ReusableBlockConvertButton({
  clientIds,
  rootClientId,
  onClose
}) {
  const [syncType, setSyncType] = (0,external_wp_element_namespaceObject.useState)(void 0);
  const [isModalOpen, setIsModalOpen] = (0,external_wp_element_namespaceObject.useState)(false);
  const [title, setTitle] = (0,external_wp_element_namespaceObject.useState)("");
  const canConvert = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const { canUser } = select(external_wp_coreData_namespaceObject.store);
      const {
        getBlocksByClientId,
        canInsertBlockType,
        getBlockRootClientId
      } = select(external_wp_blockEditor_namespaceObject.store);
      const rootId = rootClientId || (clientIds.length > 0 ? getBlockRootClientId(clientIds[0]) : void 0);
      const blocks = getBlocksByClientId(clientIds) ?? [];
      const isReusable = blocks.length === 1 && blocks[0] && (0,external_wp_blocks_namespaceObject.isReusableBlock)(blocks[0]) && !!select(external_wp_coreData_namespaceObject.store).getEntityRecord(
        "postType",
        "wp_block",
        blocks[0].attributes.ref
      );
      const _canConvert = (
        // Hide when this is already a reusable block.
        !isReusable && // Hide when reusable blocks are disabled.
        canInsertBlockType("core/block", rootId) && blocks.every(
          (block) => (
            // Guard against the case where a regular block has *just* been converted.
            !!block && // Hide on invalid blocks.
            block.isValid && // Hide when block doesn't support being made reusable.
            (0,external_wp_blocks_namespaceObject.hasBlockSupport)(block.name, "reusable", true)
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
  const { __experimentalConvertBlocksToReusable: convertBlocksToReusable } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  const { createSuccessNotice, createErrorNotice } = (0,external_wp_data_namespaceObject.useDispatch)(external_wp_notices_namespaceObject.store);
  const onConvert = (0,external_wp_element_namespaceObject.useCallback)(
    async function(reusableBlockTitle) {
      try {
        await convertBlocksToReusable(
          clientIds,
          reusableBlockTitle,
          syncType
        );
        createSuccessNotice(
          !syncType ? (0,external_wp_i18n_namespaceObject.sprintf)(
            // translators: %s: the name the user has given to the pattern.
            (0,external_wp_i18n_namespaceObject.__)("Synced pattern created: %s"),
            reusableBlockTitle
          ) : (0,external_wp_i18n_namespaceObject.sprintf)(
            // translators: %s: the name the user has given to the pattern.
            (0,external_wp_i18n_namespaceObject.__)("Unsynced pattern created: %s"),
            reusableBlockTitle
          ),
          {
            type: "snackbar",
            id: "convert-to-reusable-block-success"
          }
        );
      } catch (error) {
        createErrorNotice(error.message, {
          type: "snackbar",
          id: "convert-to-reusable-block-error"
        });
      }
    },
    [
      convertBlocksToReusable,
      clientIds,
      syncType,
      createSuccessNotice,
      createErrorNotice
    ]
  );
  if (!canConvert) {
    return null;
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuItem, { icon: symbol_default, onClick: () => setIsModalOpen(true), children: (0,external_wp_i18n_namespaceObject.__)("Create pattern") }),
    isModalOpen && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      external_wp_components_namespaceObject.Modal,
      {
        title: (0,external_wp_i18n_namespaceObject.__)("Create pattern"),
        onRequestClose: () => {
          setIsModalOpen(false);
          setTitle("");
        },
        overlayClassName: "reusable-blocks-menu-items__convert-modal",
        children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
          "form",
          {
            onSubmit: (event) => {
              event.preventDefault();
              onConvert(title);
              setIsModalOpen(false);
              setTitle("");
              onClose();
            },
            children: /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_wp_components_namespaceObject.__experimentalVStack, { spacing: "5", children: [
              /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
                external_wp_components_namespaceObject.TextControl,
                {
                  __next40pxDefaultSize: true,
                  __nextHasNoMarginBottom: true,
                  label: (0,external_wp_i18n_namespaceObject.__)("Name"),
                  value: title,
                  onChange: setTitle,
                  placeholder: (0,external_wp_i18n_namespaceObject.__)("My pattern")
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
                  checked: !syncType,
                  onChange: () => {
                    setSyncType(
                      !syncType ? "unsynced" : void 0
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
                      setIsModalOpen(false);
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
                    children: (0,external_wp_i18n_namespaceObject.__)("Create")
                  }
                )
              ] })
            ] })
          }
        )
      }
    )
  ] });
}


;// external ["wp","url"]
const external_wp_url_namespaceObject = window["wp"]["url"];
;// ./node_modules/@wordpress/reusable-blocks/build-module/components/reusable-blocks-menu-items/reusable-blocks-manage-button.js









function ReusableBlocksManageButton({ clientId }) {
  const { canRemove, isVisible, managePatternsUrl } = (0,external_wp_data_namespaceObject.useSelect)(
    (select) => {
      const { getBlock, canRemoveBlock } = select(external_wp_blockEditor_namespaceObject.store);
      const { canUser } = select(external_wp_coreData_namespaceObject.store);
      const reusableBlock = getBlock(clientId);
      return {
        canRemove: canRemoveBlock(clientId),
        isVisible: !!reusableBlock && (0,external_wp_blocks_namespaceObject.isReusableBlock)(reusableBlock) && !!canUser("update", {
          kind: "postType",
          name: "wp_block",
          id: reusableBlock.attributes.ref
        }),
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
  const { __experimentalConvertBlockToStatic: convertBlockToStatic } = (0,external_wp_data_namespaceObject.useDispatch)(store);
  if (!isVisible) {
    return null;
  }
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuItem, { href: managePatternsUrl, children: (0,external_wp_i18n_namespaceObject.__)("Manage patterns") }),
    canRemove && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_components_namespaceObject.MenuItem, { onClick: () => convertBlockToStatic(clientId), children: (0,external_wp_i18n_namespaceObject.__)("Detach") })
  ] });
}
var reusable_blocks_manage_button_default = ReusableBlocksManageButton;


;// ./node_modules/@wordpress/reusable-blocks/build-module/components/reusable-blocks-menu-items/index.js




function ReusableBlocksMenuItems({ rootClientId }) {
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(external_wp_blockEditor_namespaceObject.BlockSettingsMenuControls, { children: ({ onClose, selectedClientIds }) => /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsxs)(external_ReactJSXRuntime_namespaceObject.Fragment, { children: [
    /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      ReusableBlockConvertButton,
      {
        clientIds: selectedClientIds,
        rootClientId,
        onClose
      }
    ),
    selectedClientIds.length === 1 && /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(
      reusable_blocks_manage_button_default,
      {
        clientId: selectedClientIds[0]
      }
    )
  ] }) });
}


;// ./node_modules/@wordpress/reusable-blocks/build-module/components/index.js



;// ./node_modules/@wordpress/reusable-blocks/build-module/index.js




(window.wp = window.wp || {}).reusableBlocks = __webpack_exports__;
/******/ })()
;