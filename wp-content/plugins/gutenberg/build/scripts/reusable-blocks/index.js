var wp;
(wp ||= {}).reusableBlocks = (() => {
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

  // package-external:@wordpress/block-editor
  var require_block_editor = __commonJS({
    "package-external:@wordpress/block-editor"(exports, module) {
      module.exports = window.wp.blockEditor;
    }
  });

  // package-external:@wordpress/blocks
  var require_blocks = __commonJS({
    "package-external:@wordpress/blocks"(exports, module) {
      module.exports = window.wp.blocks;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
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

  // package-external:@wordpress/notices
  var require_notices = __commonJS({
    "package-external:@wordpress/notices"(exports, module) {
      module.exports = window.wp.notices;
    }
  });

  // package-external:@wordpress/core-data
  var require_core_data = __commonJS({
    "package-external:@wordpress/core-data"(exports, module) {
      module.exports = window.wp.coreData;
    }
  });

  // package-external:@wordpress/url
  var require_url = __commonJS({
    "package-external:@wordpress/url"(exports, module) {
      module.exports = window.wp.url;
    }
  });

  // packages/reusable-blocks/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    ReusableBlocksMenuItems: () => ReusableBlocksMenuItems,
    store: () => store
  });

  // packages/reusable-blocks/build-module/store/index.js
  var import_data2 = __toESM(require_data());

  // packages/reusable-blocks/build-module/store/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    __experimentalConvertBlockToStatic: () => __experimentalConvertBlockToStatic,
    __experimentalConvertBlocksToReusable: () => __experimentalConvertBlocksToReusable,
    __experimentalDeleteReusableBlock: () => __experimentalDeleteReusableBlock,
    __experimentalSetEditingReusableBlock: () => __experimentalSetEditingReusableBlock
  });
  var import_block_editor = __toESM(require_block_editor());
  var import_blocks = __toESM(require_blocks());
  var import_i18n = __toESM(require_i18n());
  var __experimentalConvertBlockToStatic = (clientId) => ({ registry }) => {
    const oldBlock = registry.select(import_block_editor.store).getBlock(clientId);
    const reusableBlock = registry.select("core").getEditedEntityRecord(
      "postType",
      "wp_block",
      oldBlock.attributes.ref
    );
    const newBlocks = (0, import_blocks.parse)(
      typeof reusableBlock.content === "function" ? reusableBlock.content(reusableBlock) : reusableBlock.content
    );
    registry.dispatch(import_block_editor.store).replaceBlocks(oldBlock.clientId, newBlocks);
  };
  var __experimentalConvertBlocksToReusable = (clientIds, title, syncType) => async ({ registry, dispatch }) => {
    const meta = syncType === "unsynced" ? {
      wp_pattern_sync_status: syncType
    } : void 0;
    const reusableBlock = {
      title: title || (0, import_i18n.__)("Untitled pattern block"),
      content: (0, import_blocks.serialize)(
        registry.select(import_block_editor.store).getBlocksByClientId(clientIds)
      ),
      status: "publish",
      meta
    };
    const updatedRecord = await registry.dispatch("core").saveEntityRecord("postType", "wp_block", reusableBlock);
    if (syncType === "unsynced") {
      return;
    }
    const newBlock = (0, import_blocks.createBlock)("core/block", {
      ref: updatedRecord.id
    });
    registry.dispatch(import_block_editor.store).replaceBlocks(clientIds, newBlock);
    dispatch.__experimentalSetEditingReusableBlock(
      newBlock.clientId,
      true
    );
  };
  var __experimentalDeleteReusableBlock = (id) => async ({ registry }) => {
    const reusableBlock = registry.select("core").getEditedEntityRecord("postType", "wp_block", id);
    if (!reusableBlock) {
      return;
    }
    const allBlocks = registry.select(import_block_editor.store).getBlocks();
    const associatedBlocks = allBlocks.filter(
      (block) => (0, import_blocks.isReusableBlock)(block) && block.attributes.ref === id
    );
    const associatedBlockClientIds = associatedBlocks.map(
      (block) => block.clientId
    );
    if (associatedBlockClientIds.length) {
      registry.dispatch(import_block_editor.store).removeBlocks(associatedBlockClientIds);
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

  // packages/reusable-blocks/build-module/store/reducer.js
  var import_data = __toESM(require_data());
  function isEditingReusableBlock(state = {}, action) {
    if (action?.type === "SET_EDITING_REUSABLE_BLOCK") {
      return {
        ...state,
        [action.clientId]: action.isEditing
      };
    }
    return state;
  }
  var reducer_default = (0, import_data.combineReducers)({
    isEditingReusableBlock
  });

  // packages/reusable-blocks/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    __experimentalIsEditingReusableBlock: () => __experimentalIsEditingReusableBlock
  });
  function __experimentalIsEditingReusableBlock(state, clientId) {
    return state.isEditingReusableBlock[clientId];
  }

  // packages/reusable-blocks/build-module/store/index.js
  var STORE_NAME = "core/reusable-blocks";
  var store = (0, import_data2.createReduxStore)(STORE_NAME, {
    actions: actions_exports,
    reducer: reducer_default,
    selectors: selectors_exports
  });
  (0, import_data2.register)(store);

  // packages/reusable-blocks/build-module/components/reusable-blocks-menu-items/index.js
  var import_block_editor4 = __toESM(require_block_editor());

  // packages/reusable-blocks/build-module/components/reusable-blocks-menu-items/reusable-block-convert-button.js
  var import_blocks2 = __toESM(require_blocks());
  var import_block_editor2 = __toESM(require_block_editor());
  var import_element = __toESM(require_element());
  var import_components = __toESM(require_components());

  // packages/icons/build-module/library/symbol.js
  var import_primitives = __toESM(require_primitives());
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var symbol_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M21.3 10.8l-5.6-5.6c-.7-.7-1.8-.7-2.5 0l-5.6 5.6c-.7.7-.7 1.8 0 2.5l5.6 5.6c.3.3.8.5 1.2.5s.9-.2 1.2-.5l5.6-5.6c.8-.7.8-1.9.1-2.5zm-1 1.4l-5.6 5.6c-.1.1-.3.1-.4 0l-5.6-5.6c-.1-.1-.1-.3 0-.4l5.6-5.6s.1-.1.2-.1.1 0 .2.1l5.6 5.6c.1.1.1.3 0 .4zm-16.6-.4L10 5.5l-1-1-6.3 6.3c-.7.7-.7 1.8 0 2.5L9 19.5l1.1-1.1-6.3-6.3c-.2 0-.2-.2-.1-.3z" }) });

  // packages/reusable-blocks/build-module/components/reusable-blocks-menu-items/reusable-block-convert-button.js
  var import_data3 = __toESM(require_data());
  var import_i18n2 = __toESM(require_i18n());
  var import_notices = __toESM(require_notices());
  var import_core_data = __toESM(require_core_data());
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  function ReusableBlockConvertButton({
    clientIds,
    rootClientId,
    onClose
  }) {
    const [syncType, setSyncType] = (0, import_element.useState)(void 0);
    const [isModalOpen, setIsModalOpen] = (0, import_element.useState)(false);
    const [title, setTitle] = (0, import_element.useState)("");
    const canConvert = (0, import_data3.useSelect)(
      (select) => {
        const { canUser } = select(import_core_data.store);
        const {
          getBlocksByClientId,
          canInsertBlockType,
          getBlockRootClientId
        } = select(import_block_editor2.store);
        const rootId = rootClientId || (clientIds.length > 0 ? getBlockRootClientId(clientIds[0]) : void 0);
        const blocks = getBlocksByClientId(clientIds) ?? [];
        const isReusable = blocks.length === 1 && blocks[0] && (0, import_blocks2.isReusableBlock)(blocks[0]) && !!select(import_core_data.store).getEntityRecord(
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
              (0, import_blocks2.hasBlockSupport)(block.name, "reusable", true)
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
    const { __experimentalConvertBlocksToReusable: convertBlocksToReusable } = (0, import_data3.useDispatch)(store);
    const { createSuccessNotice, createErrorNotice } = (0, import_data3.useDispatch)(import_notices.store);
    const onConvert = (0, import_element.useCallback)(
      async function(reusableBlockTitle) {
        try {
          await convertBlocksToReusable(
            clientIds,
            reusableBlockTitle,
            syncType
          );
          createSuccessNotice(
            !syncType ? (0, import_i18n2.sprintf)(
              // translators: %s: the name the user has given to the pattern.
              (0, import_i18n2.__)("Synced pattern created: %s"),
              reusableBlockTitle
            ) : (0, import_i18n2.sprintf)(
              // translators: %s: the name the user has given to the pattern.
              (0, import_i18n2.__)("Unsynced pattern created: %s"),
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
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(import_jsx_runtime2.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_components.MenuItem, { icon: symbol_default, onClick: () => setIsModalOpen(true), children: (0, import_i18n2.__)("Create pattern") }),
      isModalOpen && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
        import_components.Modal,
        {
          title: (0, import_i18n2.__)("Create pattern"),
          onRequestClose: () => {
            setIsModalOpen(false);
            setTitle("");
          },
          overlayClassName: "reusable-blocks-menu-items__convert-modal",
          children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
            "form",
            {
              onSubmit: (event) => {
                event.preventDefault();
                onConvert(title);
                setIsModalOpen(false);
                setTitle("");
                onClose();
              },
              children: /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(import_components.__experimentalVStack, { spacing: "5", children: [
                /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
                  import_components.TextControl,
                  {
                    __next40pxDefaultSize: true,
                    __nextHasNoMarginBottom: true,
                    label: (0, import_i18n2.__)("Name"),
                    value: title,
                    onChange: setTitle,
                    placeholder: (0, import_i18n2.__)("My pattern")
                  }
                ),
                /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
                  import_components.ToggleControl,
                  {
                    __nextHasNoMarginBottom: true,
                    label: (0, import_i18n2._x)("Synced", "pattern (singular)"),
                    help: (0, import_i18n2.__)(
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
                /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(import_components.__experimentalHStack, { justify: "right", children: [
                  /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
                    import_components.Button,
                    {
                      __next40pxDefaultSize: true,
                      variant: "tertiary",
                      onClick: () => {
                        setIsModalOpen(false);
                        setTitle("");
                      },
                      children: (0, import_i18n2.__)("Cancel")
                    }
                  ),
                  /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
                    import_components.Button,
                    {
                      __next40pxDefaultSize: true,
                      variant: "primary",
                      type: "submit",
                      children: (0, import_i18n2.__)("Create")
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

  // packages/reusable-blocks/build-module/components/reusable-blocks-menu-items/reusable-blocks-manage-button.js
  var import_components2 = __toESM(require_components());
  var import_i18n3 = __toESM(require_i18n());
  var import_blocks3 = __toESM(require_blocks());
  var import_data4 = __toESM(require_data());
  var import_block_editor3 = __toESM(require_block_editor());
  var import_url = __toESM(require_url());
  var import_core_data2 = __toESM(require_core_data());
  var import_jsx_runtime3 = __toESM(require_jsx_runtime());
  function ReusableBlocksManageButton({ clientId }) {
    const { canRemove, isVisible, managePatternsUrl } = (0, import_data4.useSelect)(
      (select) => {
        const { getBlock, canRemoveBlock } = select(import_block_editor3.store);
        const { canUser } = select(import_core_data2.store);
        const reusableBlock = getBlock(clientId);
        return {
          canRemove: canRemoveBlock(clientId),
          isVisible: !!reusableBlock && (0, import_blocks3.isReusableBlock)(reusableBlock) && !!canUser("update", {
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
          }) ? (0, import_url.addQueryArgs)("site-editor.php", {
            p: "/pattern"
          }) : (0, import_url.addQueryArgs)("edit.php", {
            post_type: "wp_block"
          })
        };
      },
      [clientId]
    );
    const { __experimentalConvertBlockToStatic: convertBlockToStatic } = (0, import_data4.useDispatch)(store);
    if (!isVisible) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime3.jsxs)(import_jsx_runtime3.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_components2.MenuItem, { href: managePatternsUrl, children: (0, import_i18n3.__)("Manage patterns") }),
      canRemove && /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_components2.MenuItem, { onClick: () => convertBlockToStatic(clientId), children: (0, import_i18n3.__)("Disconnect pattern") })
    ] });
  }
  var reusable_blocks_manage_button_default = ReusableBlocksManageButton;

  // packages/reusable-blocks/build-module/components/reusable-blocks-menu-items/index.js
  var import_jsx_runtime4 = __toESM(require_jsx_runtime());
  function ReusableBlocksMenuItems({ rootClientId }) {
    return /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_block_editor4.BlockSettingsMenuControls, { children: ({ onClose, selectedClientIds }) => /* @__PURE__ */ (0, import_jsx_runtime4.jsxs)(import_jsx_runtime4.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
        ReusableBlockConvertButton,
        {
          clientIds: selectedClientIds,
          rootClientId,
          onClose
        }
      ),
      selectedClientIds.length === 1 && /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
        reusable_blocks_manage_button_default,
        {
          clientId: selectedClientIds[0]
        }
      )
    ] }) });
  }
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
