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

// package-external:@wordpress/patterns
var require_patterns = __commonJS({
  "package-external:@wordpress/patterns"(exports, module) {
    module.exports = window.wp.patterns;
  }
});

// package-external:@wordpress/html-entities
var require_html_entities = __commonJS({
  "package-external:@wordpress/html-entities"(exports, module) {
    module.exports = window.wp.htmlEntities;
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

// package-external:@wordpress/data
var require_data = __commonJS({
  "package-external:@wordpress/data"(exports, module) {
    module.exports = window.wp.data;
  }
});

// package-external:@wordpress/private-apis
var require_private_apis = __commonJS({
  "package-external:@wordpress/private-apis"(exports, module) {
    module.exports = window.wp.privateApis;
  }
});

// package-external:@wordpress/blocks
var require_blocks = __commonJS({
  "package-external:@wordpress/blocks"(exports, module) {
    module.exports = window.wp.blocks;
  }
});

// package-external:@wordpress/url
var require_url = __commonJS({
  "package-external:@wordpress/url"(exports, module) {
    module.exports = window.wp.url;
  }
});

// package-external:@wordpress/api-fetch
var require_api_fetch = __commonJS({
  "package-external:@wordpress/api-fetch"(exports, module) {
    module.exports = window.wp.apiFetch;
  }
});

// packages/icons/build-module/library/backup.mjs
var import_primitives = __toESM(require_primitives(), 1);
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
var backup_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M7.59201 17.7565C8.8135 18.6933 10.3417 19.25 12 19.25C16.0041 19.25 19.25 16.0041 19.25 12C19.25 7.99594 16.0041 4.75 12 4.75C7.99594 4.75 4.75 7.99593 4.75 12L4.75 14M14.75 14.75L12.25 12.25L12.25 8M2 12L4.75 14L7.5 12" }) });

// packages/icons/build-module/library/external.mjs
var import_primitives2 = __toESM(require_primitives(), 1);
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
var external_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M9.5 6.25H6.5C5.80964 6.25 5.25 6.80964 5.25 7.5V17.5C5.25 18.1904 5.80964 18.75 6.5 18.75H16.5C17.1904 18.75 17.75 18.1904 17.75 17.5V14.5M11.5 12.5L18.75 5.25M12.5 5.25H18.75V11.5" }) });

// packages/icons/build-module/library/trash.mjs
var import_primitives3 = __toESM(require_primitives(), 1);
var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
var trash_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", style: { fill: "none" }, stroke: "currentColor", strokeWidth: "1.5", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M5 7.5H7M19 7.5H17M17 7.5L16.1588 17.1733C16.069 18.2068 15.2038 19 14.1664 19H9.83363C8.79622 19 7.93102 18.2068 7.84115 17.1733L7 7.5M17 7.5H14.9897M7 7.5H9.01027M14.9897 7.5C14.8627 5.96012 13.5727 4.75 12 4.75C10.4273 4.75 9.13731 5.96012 9.01027 7.5M14.9897 7.5H9.01027" }) });

// packages/core-actions/build-module/post_type_supports/delete-post/action.mjs
var import_i18n2 = __toESM(require_i18n(), 1);
var import_element = __toESM(require_element(), 1);
var import_components = __toESM(require_components(), 1);
var import_patterns = __toESM(require_patterns(), 1);
var import_html_entities2 = __toESM(require_html_entities(), 1);

// packages/core-actions/build-module/shared/utils.mjs
var import_html_entities = __toESM(require_html_entities(), 1);
var import_i18n = __toESM(require_i18n(), 1);
function isTemplate(post) {
  return post.type === "wp_template";
}
function isTemplatePart(post) {
  return post.type === "wp_template_part";
}
function isTemplateOrTemplatePart(p) {
  return p.type === "wp_template" || p.type === "wp_template_part";
}
function getItemTitle(item, fallback = (0, import_i18n.__)("(no title)")) {
  let title = "";
  if (typeof item.title === "string") {
    title = (0, import_html_entities.decodeEntities)(item.title);
  } else if (item.title && "rendered" in item.title) {
    title = (0, import_html_entities.decodeEntities)(item.title.rendered);
  } else if (item.title && "raw" in item.title) {
    title = (0, import_html_entities.decodeEntities)(item.title.raw);
  }
  return title || fallback;
}
function isTemplateRemovable(template) {
  if (!template) {
    return false;
  }
  return [template.source, template.source].includes("custom") && !Boolean(template.type === "wp_template" && template?.plugin) && !template.has_theme_file;
}

// packages/core-actions/build-module/post_type_supports/delete-post/delete-post-with-notices.mjs
var import_notices = __toESM(require_notices(), 1);
var import_core_data = __toESM(require_core_data(), 1);
var import_data = __toESM(require_data(), 1);
function getErrorMessagesFromPromises(allSettledResults) {
  const errorMessages = /* @__PURE__ */ new Set();
  if (allSettledResults.length === 1) {
    const typedError = allSettledResults[0];
    if (typedError.reason?.message) {
      errorMessages.add(typedError.reason.message);
    }
  } else {
    const failedPromises = allSettledResults.filter(
      ({ status }) => status === "rejected"
    );
    for (const failedPromise of failedPromises) {
      const typedError = failedPromise;
      if (typedError.reason?.message) {
        errorMessages.add(typedError.reason.message);
      }
    }
  }
  return errorMessages;
}
var deletePostWithNotices = async (posts, notice, callbacks) => {
  const { createSuccessNotice, createErrorNotice } = (0, import_data.dispatch)(import_notices.store);
  const { deleteEntityRecord } = (0, import_data.dispatch)(import_core_data.store);
  const allSettledResults = await Promise.allSettled(
    posts.map((post) => {
      return deleteEntityRecord(
        "postType",
        post.type,
        post.id,
        { force: true },
        { throwOnError: true }
      );
    })
  );
  if (allSettledResults.every(({ status }) => status === "fulfilled")) {
    let successMessage;
    if (allSettledResults.length === 1) {
      successMessage = notice.success.messages.getMessage(posts[0]);
    } else {
      successMessage = notice.success.messages.getBatchMessage(posts);
    }
    createSuccessNotice(successMessage, {
      type: notice.success.type ?? "snackbar",
      id: notice.success.id
    });
    callbacks.onActionPerformed?.(posts);
  } else {
    const errorMessages = getErrorMessagesFromPromises(allSettledResults);
    let errorMessage = "";
    if (allSettledResults.length === 1) {
      errorMessage = notice.error.messages.getMessage(errorMessages);
    } else {
      errorMessage = notice.error.messages.getBatchMessage(errorMessages);
    }
    createErrorNotice(errorMessage, {
      type: notice.error.type ?? "snackbar",
      id: notice.error.id
    });
    callbacks.onActionError?.();
  }
};

// packages/core-actions/build-module/lock-unlock.mjs
var import_private_apis = __toESM(require_private_apis(), 1);
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/core-actions"
);

// packages/core-actions/build-module/post_type_supports/delete-post/action.mjs
var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
var { PATTERN_TYPES } = unlock(import_patterns.privateApis);
var deletePost = {
  icon: trash_default,
  isEligible(post) {
    if (isTemplateOrTemplatePart(post)) {
      return isTemplateRemovable(post);
    }
    return post.type === PATTERN_TYPES.user;
  },
  RenderModal: ({ items, closeModal, onActionPerformed }) => {
    const [isBusy, setIsBusy] = (0, import_element.useState)(false);
    const isResetting = items.every(
      (item) => isTemplateOrTemplatePart(item) && item?.has_theme_file
    );
    return /* @__PURE__ */ (0, import_jsx_runtime4.jsxs)(import_components.__experimentalVStack, { spacing: "5", children: [
      /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_components.__experimentalText, { children: items.length > 1 ? (0, import_i18n2.sprintf)(
        // translators: %d: number of items to delete.
        (0, import_i18n2._n)(
          "Delete %d item?",
          "Delete %d items?",
          items.length
        ),
        items.length
      ) : (0, import_i18n2.sprintf)(
        // translators: %s: The template or template part's title
        (0, import_i18n2._x)('Delete "%s"?', "template part"),
        getItemTitle(items[0])
      ) }),
      /* @__PURE__ */ (0, import_jsx_runtime4.jsxs)(import_components.__experimentalHStack, { justify: "right", children: [
        /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
          import_components.Button,
          {
            variant: "tertiary",
            onClick: closeModal,
            disabled: isBusy,
            accessibleWhenDisabled: true,
            __next40pxDefaultSize: true,
            children: (0, import_i18n2.__)("Cancel")
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
          import_components.Button,
          {
            variant: "primary",
            onClick: async () => {
              setIsBusy(true);
              const notice = {
                success: {
                  messages: {
                    getMessage: (item) => {
                      return isResetting ? (0, import_i18n2.sprintf)(
                        /* translators: %s: The template/part's name. */
                        (0, import_i18n2.__)('"%s" reset.'),
                        (0, import_html_entities2.decodeEntities)(
                          getItemTitle(item)
                        )
                      ) : (0, import_i18n2.sprintf)(
                        /* translators: %s: The template/part's name. */
                        (0, import_i18n2._x)(
                          '"%s" deleted.',
                          "template part"
                        ),
                        (0, import_html_entities2.decodeEntities)(
                          getItemTitle(item)
                        )
                      );
                    },
                    getBatchMessage: () => {
                      return isResetting ? (0, import_i18n2.__)("Items reset.") : (0, import_i18n2.__)("Items deleted.");
                    }
                  }
                },
                error: {
                  messages: {
                    getMessage: (error) => {
                      if (error.size === 1) {
                        return [...error][0];
                      }
                      return isResetting ? (0, import_i18n2.__)(
                        "An error occurred while reverting the item."
                      ) : (0, import_i18n2.__)(
                        "An error occurred while deleting the item."
                      );
                    },
                    getBatchMessage: (errors) => {
                      if (errors.size === 0) {
                        return isResetting ? (0, import_i18n2.__)(
                          "An error occurred while reverting the items."
                        ) : (0, import_i18n2.__)(
                          "An error occurred while deleting the items."
                        );
                      }
                      if (errors.size === 1) {
                        return isResetting ? (0, import_i18n2.sprintf)(
                          /* translators: %s: an error message */
                          (0, import_i18n2.__)(
                            "An error occurred while reverting the items: %s"
                          ),
                          [...errors][0]
                        ) : (0, import_i18n2.sprintf)(
                          /* translators: %s: an error message */
                          (0, import_i18n2.__)(
                            "An error occurred while deleting the items: %s"
                          ),
                          [...errors][0]
                        );
                      }
                      return isResetting ? (0, import_i18n2.sprintf)(
                        /* translators: %s: a list of comma separated error messages */
                        (0, import_i18n2.__)(
                          "Some errors occurred while reverting the items: %s"
                        ),
                        [...errors].join(
                          ","
                        )
                      ) : (0, import_i18n2.sprintf)(
                        /* translators: %s: a list of comma separated error messages */
                        (0, import_i18n2.__)(
                          "Some errors occurred while deleting the items: %s"
                        ),
                        [...errors].join(
                          ","
                        )
                      );
                    }
                  }
                }
              };
              await deletePostWithNotices(items, notice, {
                onActionPerformed
              });
              setIsBusy(false);
              closeModal?.();
            },
            isBusy,
            disabled: isBusy,
            accessibleWhenDisabled: true,
            __next40pxDefaultSize: true,
            children: (0, import_i18n2.__)("Delete")
          }
        )
      ] })
    ] });
  }
};
var action_default = deletePost;

// packages/core-actions/build-module/post_type_supports/duplicate-post/action.mjs
var import_data2 = __toESM(require_data(), 1);
var import_core_data2 = __toESM(require_core_data(), 1);
var import_i18n3 = __toESM(require_i18n(), 1);
var import_notices2 = __toESM(require_notices(), 1);
var import_element2 = __toESM(require_element(), 1);
var import_components2 = __toESM(require_components(), 1);
var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
var duplicatePost = {
  isEligible({ status }) {
    return status !== "trash";
  },
  RenderModal: ({ items, closeModal, onActionPerformed }) => {
    const [item, setItem] = (0, import_element2.useState)({
      ...items[0],
      title: (0, import_i18n3.sprintf)(
        /* translators: %s: Existing post title */
        (0, import_i18n3._x)("%s (Copy)", "post"),
        getItemTitle(items[0])
      )
    });
    const [isCreatingPage, setIsCreatingPage] = (0, import_element2.useState)(false);
    const { saveEntityRecord } = (0, import_data2.useDispatch)(import_core_data2.store);
    const { createSuccessNotice, createErrorNotice } = (0, import_data2.useDispatch)(import_notices2.store);
    async function createPage(event) {
      event.preventDefault();
      if (isCreatingPage) {
        return;
      }
      const newItemObject = {
        status: "draft",
        title: item.title,
        slug: item.title || (0, import_i18n3.__)("No title"),
        comment_status: item.comment_status,
        content: typeof item.content === "string" ? item.content : item.content.raw,
        excerpt: typeof item.excerpt === "string" ? item.excerpt : item.excerpt?.raw,
        meta: item.meta,
        parent: item.parent,
        password: item.password,
        template: item.template,
        format: item.format,
        featured_media: item.featured_media,
        menu_order: item.menu_order,
        ping_status: item.ping_status
      };
      const assignablePropertiesPrefix = "wp:action-assign-";
      const assignableProperties = Object.keys(item?._links || {}).filter(
        (property) => property.startsWith(assignablePropertiesPrefix)
      ).map(
        (property) => property.slice(assignablePropertiesPrefix.length)
      );
      assignableProperties.forEach((property) => {
        if (item.hasOwnProperty(property)) {
          newItemObject[property] = item[property];
        }
      });
      setIsCreatingPage(true);
      try {
        const newItem = await saveEntityRecord(
          "postType",
          item.type,
          newItemObject,
          { throwOnError: true }
        );
        createSuccessNotice(
          (0, import_i18n3.sprintf)(
            // translators: %s: Title of the created post, e.g: "Hello world".
            (0, import_i18n3.__)('"%s" successfully created.'),
            getItemTitle(newItem)
          ),
          {
            id: "duplicate-post-action",
            type: "snackbar"
          }
        );
        if (onActionPerformed) {
          onActionPerformed([newItem]);
        }
      } catch (error) {
        const typedError = error;
        const errorMessage = typedError.message && typedError.code !== "unknown_error" ? typedError.message : (0, import_i18n3.__)("An error occurred while duplicating the page.");
        createErrorNotice(errorMessage, {
          type: "snackbar"
        });
      } finally {
        setIsCreatingPage(false);
        closeModal?.();
      }
    }
    return /* @__PURE__ */ (0, import_jsx_runtime5.jsx)("form", { onSubmit: createPage, children: /* @__PURE__ */ (0, import_jsx_runtime5.jsxs)(import_components2.__experimentalVStack, { spacing: 3, children: [
      /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
        import_components2.__experimentalInputControl,
        {
          label: (0, import_i18n3.__)("Title"),
          placeholder: (0, import_i18n3.__)("No title"),
          value: getItemTitle(item),
          onChange: (value) => setItem((prev) => ({
            ...prev,
            title: value || (0, import_i18n3.__)("No title")
          }))
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime5.jsxs)(import_components2.__experimentalHStack, { spacing: 2, justify: "end", children: [
        /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
          import_components2.Button,
          {
            variant: "tertiary",
            onClick: closeModal,
            __next40pxDefaultSize: true,
            children: (0, import_i18n3.__)("Cancel")
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(
          import_components2.Button,
          {
            variant: "primary",
            type: "submit",
            isBusy: isCreatingPage,
            "aria-disabled": isCreatingPage,
            __next40pxDefaultSize: true,
            children: (0, import_i18n3._x)("Duplicate", "action label")
          }
        )
      ] })
    ] }) });
  }
};
var action_default2 = duplicatePost;

// packages/core-actions/build-module/post_type_supports/move-to-trash/action.mjs
var import_data3 = __toESM(require_data(), 1);
var import_core_data3 = __toESM(require_core_data(), 1);
var import_i18n4 = __toESM(require_i18n(), 1);
var import_notices3 = __toESM(require_notices(), 1);
var import_element3 = __toESM(require_element(), 1);
var import_components3 = __toESM(require_components(), 1);

// packages/core-actions/build-module/shared/has-action-link.mjs
function hasActionLink(item, action) {
  if (!item._links) {
    return true;
  }
  return !!item._links[action];
}

// packages/core-actions/build-module/post_type_supports/move-to-trash/action.mjs
var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
var trashPost = {
  icon: trash_default,
  isEligible(item) {
    if (item.type === "wp_template_part" || item.type === "wp_block") {
      return false;
    }
    if (item.type === "wp_template" && typeof item.id === "string") {
      return false;
    }
    return !!item.status && !["auto-draft", "trash"].includes(item.status) && item.permissions?.delete && hasActionLink(item, "wp:action-trash");
  },
  RenderModal: ({ items, closeModal, onActionPerformed }) => {
    const [isBusy, setIsBusy] = (0, import_element3.useState)(false);
    const { createSuccessNotice, createErrorNotice } = (0, import_data3.useDispatch)(import_notices3.store);
    const { deleteEntityRecord } = (0, import_data3.useDispatch)(import_core_data3.store);
    return /* @__PURE__ */ (0, import_jsx_runtime6.jsxs)(import_components3.__experimentalVStack, { spacing: "5", children: [
      /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_components3.__experimentalText, { children: items.length === 1 ? (0, import_i18n4.sprintf)(
        // translators: %s: The item's title.
        (0, import_i18n4.__)(
          'Are you sure you want to move "%s" to the trash?'
        ),
        getItemTitle(items[0])
      ) : (0, import_i18n4.sprintf)(
        // translators: %d: The number of items (2 or more).
        (0, import_i18n4._n)(
          "Are you sure you want to move %d item to the trash ?",
          "Are you sure you want to move %d items to the trash ?",
          items.length
        ),
        items.length
      ) }),
      /* @__PURE__ */ (0, import_jsx_runtime6.jsxs)(import_components3.__experimentalHStack, { justify: "right", children: [
        /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
          import_components3.Button,
          {
            __next40pxDefaultSize: true,
            variant: "tertiary",
            onClick: closeModal,
            disabled: isBusy,
            accessibleWhenDisabled: true,
            children: (0, import_i18n4.__)("Cancel")
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(
          import_components3.Button,
          {
            __next40pxDefaultSize: true,
            variant: "primary",
            onClick: async () => {
              setIsBusy(true);
              const promiseResult = await Promise.allSettled(
                items.map(
                  (item) => deleteEntityRecord(
                    "postType",
                    item.type,
                    item.id.toString(),
                    {},
                    { throwOnError: true }
                  )
                )
              );
              if (promiseResult.every(
                ({ status }) => status === "fulfilled"
              )) {
                let successMessage;
                if (promiseResult.length === 1) {
                  successMessage = (0, import_i18n4.sprintf)(
                    /* translators: %s: The item's title. */
                    (0, import_i18n4.__)('"%s" moved to the trash.'),
                    getItemTitle(items[0])
                  );
                } else {
                  successMessage = (0, import_i18n4.sprintf)(
                    /* translators: %d: The number of items. */
                    (0, import_i18n4._n)(
                      "%d item moved to the trash.",
                      "%d items moved to the trash.",
                      items.length
                    ),
                    items.length
                  );
                }
                createSuccessNotice(successMessage, {
                  type: "snackbar",
                  id: "move-to-trash-action"
                });
              } else {
                let errorMessage;
                if (promiseResult.length === 1) {
                  const typedError = promiseResult[0];
                  if (typedError.reason?.message) {
                    errorMessage = typedError.reason.message;
                  } else {
                    errorMessage = (0, import_i18n4.__)(
                      "An error occurred while moving the item to the trash."
                    );
                  }
                } else {
                  const errorMessages = /* @__PURE__ */ new Set();
                  const failedPromises = promiseResult.filter(
                    ({ status }) => status === "rejected"
                  );
                  for (const failedPromise of failedPromises) {
                    const typedError = failedPromise;
                    if (typedError.reason?.message) {
                      errorMessages.add(
                        typedError.reason.message
                      );
                    }
                  }
                  if (errorMessages.size === 0) {
                    errorMessage = (0, import_i18n4.__)(
                      "An error occurred while moving the items to the trash."
                    );
                  } else if (errorMessages.size === 1) {
                    errorMessage = (0, import_i18n4.sprintf)(
                      /* translators: %s: an error message */
                      (0, import_i18n4.__)(
                        "An error occurred while moving the item to the trash: %s"
                      ),
                      [...errorMessages][0]
                    );
                  } else {
                    errorMessage = (0, import_i18n4.sprintf)(
                      /* translators: %s: a list of comma separated error messages */
                      (0, import_i18n4.__)(
                        "Some errors occurred while moving the items to the trash: %s"
                      ),
                      [...errorMessages].join(",")
                    );
                  }
                }
                createErrorNotice(errorMessage, {
                  type: "snackbar"
                });
              }
              if (onActionPerformed) {
                onActionPerformed(items);
              }
              setIsBusy(false);
              closeModal?.();
            },
            isBusy,
            disabled: isBusy,
            accessibleWhenDisabled: true,
            children: (0, import_i18n4._x)("Trash", "verb")
          }
        )
      ] })
    ] });
  }
};
var action_default3 = trashPost;

// packages/core-actions/build-module/post_type_supports/order-pages/action.mjs
var import_data4 = __toESM(require_data(), 1);
var import_core_data4 = __toESM(require_core_data(), 1);
var import_i18n5 = __toESM(require_i18n(), 1);
var import_notices4 = __toESM(require_notices(), 1);
var import_element4 = __toESM(require_element(), 1);
var import_components4 = __toESM(require_components(), 1);
var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
function isItemValid(item) {
  return typeof item.menu_order === "number" && Number.isInteger(item.menu_order);
}
function ReorderModal({
  items,
  closeModal,
  onActionPerformed
}) {
  const [item, setItem] = (0, import_element4.useState)(items[0]);
  const { editEntityRecord, saveEditedEntityRecord } = (0, import_data4.useDispatch)(import_core_data4.store);
  const { createSuccessNotice, createErrorNotice } = (0, import_data4.useDispatch)(import_notices4.store);
  const isValid = isItemValid(item);
  async function onOrder(event) {
    event.preventDefault();
    if (!isValid) {
      return;
    }
    try {
      await editEntityRecord("postType", item.type, item.id, {
        menu_order: item.menu_order
      });
      closeModal?.();
      await saveEditedEntityRecord("postType", item.type, item.id, {
        throwOnError: true
      });
      createSuccessNotice((0, import_i18n5.__)("Order updated."), {
        type: "snackbar"
      });
      onActionPerformed?.(items);
    } catch (error) {
      const typedError = error;
      const errorMessage = typedError.message && typedError.code !== "unknown_error" ? typedError.message : (0, import_i18n5.__)("An error occurred while updating the order");
      createErrorNotice(errorMessage, {
        type: "snackbar"
      });
    }
  }
  return /* @__PURE__ */ (0, import_jsx_runtime7.jsx)("form", { onSubmit: onOrder, children: /* @__PURE__ */ (0, import_jsx_runtime7.jsxs)(import_components4.__experimentalVStack, { spacing: "5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime7.jsx)("div", { children: (0, import_i18n5.__)(
      "Determines the order of pages. Pages with the same order value are sorted alphabetically. Negative order values are supported."
    ) }),
    /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
      import_components4.__experimentalInputControl,
      {
        label: (0, import_i18n5.__)("Order"),
        type: "number",
        value: typeof item.menu_order === "number" && Number.isInteger(item.menu_order) ? String(item.menu_order) : "",
        onChange: (value) => {
          const parsed = parseInt(value, 10);
          setItem({
            ...item,
            menu_order: isNaN(parsed) ? void 0 : parsed
          });
        }
      }
    ),
    /* @__PURE__ */ (0, import_jsx_runtime7.jsxs)(import_components4.__experimentalHStack, { justify: "right", children: [
      /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
        import_components4.Button,
        {
          __next40pxDefaultSize: true,
          variant: "tertiary",
          onClick: () => {
            closeModal?.();
          },
          children: (0, import_i18n5.__)("Cancel")
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
        import_components4.Button,
        {
          __next40pxDefaultSize: true,
          variant: "primary",
          type: "submit",
          accessibleWhenDisabled: true,
          disabled: !isValid,
          children: (0, import_i18n5.__)("Save")
        }
      )
    ] })
  ] }) });
}
var reorderPage = {
  isEligible({ status }) {
    return status !== "trash";
  },
  RenderModal: ReorderModal
};
var action_default4 = reorderPage;

// packages/core-actions/build-module/post_type_supports/permanently-delete/action.mjs
var import_core_data5 = __toESM(require_core_data(), 1);
var import_i18n6 = __toESM(require_i18n(), 1);
var import_notices5 = __toESM(require_notices(), 1);
var import_element5 = __toESM(require_element(), 1);
var import_data5 = __toESM(require_data(), 1);
var import_components5 = __toESM(require_components(), 1);
var import_html_entities3 = __toESM(require_html_entities(), 1);
var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
var permanentlyDeletePost = {
  icon: trash_default,
  isEligible(item) {
    if (isTemplateOrTemplatePart(item) || item.type === "wp_block") {
      return false;
    }
    const { status, permissions } = item;
    if (!status || status === "auto-draft" || !permissions?.delete) {
      return false;
    }
    return status === "trash" || !hasActionLink(item, "wp:action-trash");
  },
  RenderModal: ({ items, closeModal, onActionPerformed }) => {
    const [isBusy, setIsBusy] = (0, import_element5.useState)(false);
    const { createSuccessNotice, createErrorNotice } = (0, import_data5.useDispatch)(import_notices5.store);
    const { deleteEntityRecord } = (0, import_data5.useDispatch)(import_core_data5.store);
    return /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)(import_components5.__experimentalVStack, { spacing: "5", children: [
      /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_components5.__experimentalText, { children: items.length > 1 ? (0, import_i18n6.sprintf)(
        // translators: %d: number of items to delete.
        (0, import_i18n6._n)(
          "Are you sure you want to permanently delete %d item?",
          "Are you sure you want to permanently delete %d items?",
          items.length
        ),
        items.length
      ) : (0, import_i18n6.sprintf)(
        // translators: %s: The post's title
        (0, import_i18n6.__)(
          'Are you sure you want to permanently delete "%s"?'
        ),
        (0, import_html_entities3.decodeEntities)(getItemTitle(items[0]))
      ) }),
      /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)(import_components5.__experimentalHStack, { justify: "right", children: [
        /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(
          import_components5.Button,
          {
            variant: "tertiary",
            onClick: closeModal,
            disabled: isBusy,
            accessibleWhenDisabled: true,
            __next40pxDefaultSize: true,
            children: (0, import_i18n6.__)("Cancel")
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(
          import_components5.Button,
          {
            variant: "primary",
            onClick: async () => {
              setIsBusy(true);
              const promiseResult = await Promise.allSettled(
                items.map(
                  (post) => deleteEntityRecord(
                    "postType",
                    post.type,
                    post.id,
                    { force: true },
                    { throwOnError: true }
                  )
                )
              );
              if (promiseResult.every(
                ({ status }) => status === "fulfilled"
              )) {
                let successMessage;
                if (promiseResult.length === 1) {
                  successMessage = (0, import_i18n6.sprintf)(
                    /* translators: %s: The posts's title. */
                    (0, import_i18n6.__)('"%s" permanently deleted.'),
                    getItemTitle(items[0])
                  );
                } else {
                  successMessage = (0, import_i18n6.__)(
                    "The items were permanently deleted."
                  );
                }
                createSuccessNotice(successMessage, {
                  type: "snackbar",
                  id: "permanently-delete-post-action"
                });
                onActionPerformed?.(items);
              } else {
                let errorMessage;
                if (promiseResult.length === 1) {
                  const typedError = promiseResult[0];
                  if (typedError.reason?.message) {
                    errorMessage = typedError.reason.message;
                  } else {
                    errorMessage = (0, import_i18n6.__)(
                      "An error occurred while permanently deleting the item."
                    );
                  }
                } else {
                  const errorMessages = /* @__PURE__ */ new Set();
                  const failedPromises = promiseResult.filter(
                    ({ status }) => status === "rejected"
                  );
                  for (const failedPromise of failedPromises) {
                    const typedError = failedPromise;
                    if (typedError.reason?.message) {
                      errorMessages.add(
                        typedError.reason.message
                      );
                    }
                  }
                  if (errorMessages.size === 0) {
                    errorMessage = (0, import_i18n6.__)(
                      "An error occurred while permanently deleting the items."
                    );
                  } else if (errorMessages.size === 1) {
                    errorMessage = (0, import_i18n6.sprintf)(
                      /* translators: %s: an error message */
                      (0, import_i18n6.__)(
                        "An error occurred while permanently deleting the items: %s"
                      ),
                      [...errorMessages][0]
                    );
                  } else {
                    errorMessage = (0, import_i18n6.sprintf)(
                      /* translators: %s: a list of comma separated error messages */
                      (0, import_i18n6.__)(
                        "Some errors occurred while permanently deleting the items: %s"
                      ),
                      [...errorMessages].join(",")
                    );
                  }
                }
                createErrorNotice(errorMessage, {
                  type: "snackbar"
                });
              }
              setIsBusy(false);
              closeModal?.();
            },
            isBusy,
            disabled: isBusy,
            accessibleWhenDisabled: true,
            __next40pxDefaultSize: true,
            children: (0, import_i18n6.__)("Delete permanently")
          }
        )
      ] })
    ] });
  }
};
var action_default5 = permanentlyDeletePost;

// packages/core-actions/build-module/post_type_supports/rename-post/action.mjs
var import_data6 = __toESM(require_data(), 1);
var import_core_data6 = __toESM(require_core_data(), 1);
var import_i18n7 = __toESM(require_i18n(), 1);
var import_element6 = __toESM(require_element(), 1);
var import_patterns2 = __toESM(require_patterns(), 1);
var import_components6 = __toESM(require_components(), 1);
var import_notices6 = __toESM(require_notices(), 1);
var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
var { PATTERN_TYPES: PATTERN_TYPES2 } = unlock(import_patterns2.privateApis);
var renamePost = {
  isEligible(post) {
    if (post.status === "trash") {
      return false;
    }
    if (!["wp_template", "wp_template_part"].includes(post.type)) {
      return post.permissions?.update;
    }
    if (isTemplate(post)) {
      return isTemplateRemovable(post) && post.is_custom && post.permissions?.update;
    }
    if (isTemplatePart(post)) {
      return post.source === "custom" && !post?.has_theme_file && post.permissions?.update;
    }
    return post.type === PATTERN_TYPES2.user && post.permissions?.update;
  },
  RenderModal: ({ items, closeModal, onActionPerformed }) => {
    const [item] = items;
    const [title, setTitle] = (0, import_element6.useState)(() => getItemTitle(item, ""));
    const { editEntityRecord, saveEditedEntityRecord } = (0, import_data6.useDispatch)(import_core_data6.store);
    const { createSuccessNotice, createErrorNotice } = (0, import_data6.useDispatch)(import_notices6.store);
    async function onRename(event) {
      event.preventDefault();
      try {
        await editEntityRecord("postType", item.type, item.id, {
          title
        });
        setTitle("");
        closeModal?.();
        await saveEditedEntityRecord("postType", item.type, item.id, {
          throwOnError: true
        });
        createSuccessNotice((0, import_i18n7.__)("Name updated"), {
          type: "snackbar"
        });
        onActionPerformed?.(items);
      } catch (error) {
        const typedError = error;
        const errorMessage = typedError.message && typedError.code !== "unknown_error" ? typedError.message : (0, import_i18n7.__)("An error occurred while updating the name");
        createErrorNotice(errorMessage, { type: "snackbar" });
      }
    }
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)("form", { onSubmit: onRename, children: /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(import_components6.__experimentalVStack, { spacing: "5", children: [
      /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
        import_components6.TextControl,
        {
          label: (0, import_i18n7.__)("Name"),
          value: title,
          onChange: setTitle,
          required: true
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(import_components6.__experimentalHStack, { justify: "right", children: [
        /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
          import_components6.Button,
          {
            __next40pxDefaultSize: true,
            variant: "tertiary",
            onClick: () => {
              closeModal?.();
            },
            children: (0, import_i18n7.__)("Cancel")
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
          import_components6.Button,
          {
            __next40pxDefaultSize: true,
            variant: "primary",
            type: "submit",
            children: (0, import_i18n7.__)("Save")
          }
        )
      ] })
    ] }) });
  }
};
var action_default6 = renamePost;

// packages/core-actions/build-module/post_type_supports/reset-post/action.mjs
var import_data7 = __toESM(require_data(), 1);
var import_core_data7 = __toESM(require_core_data(), 1);
var import_i18n8 = __toESM(require_i18n(), 1);
var import_notices7 = __toESM(require_notices(), 1);
var import_element7 = __toESM(require_element(), 1);
var import_blocks = __toESM(require_blocks(), 1);
var import_components7 = __toESM(require_components(), 1);
var import_url = __toESM(require_url(), 1);
var import_api_fetch = __toESM(require_api_fetch(), 1);
var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
var isTemplateRevertable = (templateOrTemplatePart) => {
  if (!templateOrTemplatePart) {
    return false;
  }
  return templateOrTemplatePart.source === "custom" && (Boolean(templateOrTemplatePart?.plugin) || templateOrTemplatePart?.has_theme_file);
};
var revertTemplate = async (template, { allowUndo = true } = {}) => {
  const noticeId = "edit-site-template-reverted";
  (0, import_data7.dispatch)(import_notices7.store).removeNotice(noticeId);
  if (!isTemplateRevertable(template)) {
    (0, import_data7.dispatch)(import_notices7.store).createErrorNotice(
      (0, import_i18n8.__)("This template is not revertable."),
      {
        type: "snackbar"
      }
    );
    return;
  }
  try {
    const templateEntityConfig = (0, import_data7.select)(import_core_data7.store).getEntityConfig(
      "postType",
      template.type
    );
    if (!templateEntityConfig) {
      (0, import_data7.dispatch)(import_notices7.store).createErrorNotice(
        (0, import_i18n8.__)(
          "The editor has encountered an unexpected error. Please reload."
        ),
        { type: "snackbar" }
      );
      return;
    }
    const fileTemplatePath = (0, import_url.addQueryArgs)(
      `${templateEntityConfig.baseURL}/${template.id}`,
      { context: "edit", source: template.origin }
    );
    const fileTemplate = await (0, import_api_fetch.default)({
      path: fileTemplatePath
    });
    if (!fileTemplate) {
      (0, import_data7.dispatch)(import_notices7.store).createErrorNotice(
        (0, import_i18n8.__)(
          "The editor has encountered an unexpected error. Please reload."
        ),
        { type: "snackbar" }
      );
      return;
    }
    const serializeBlocks = ({ blocks: blocksForSerialization = [] }) => (0, import_blocks.__unstableSerializeAndClean)(blocksForSerialization);
    const edited = (0, import_data7.select)(import_core_data7.store).getEditedEntityRecord(
      "postType",
      template.type,
      template.id
    );
    (0, import_data7.dispatch)(import_core_data7.store).editEntityRecord(
      "postType",
      template.type,
      template.id,
      {
        content: serializeBlocks,
        // Required to make the `undo` behave correctly.
        blocks: edited.blocks,
        // Required to revert the blocks in the editor.
        source: "custom"
        // required to avoid turning the editor into a dirty state
      },
      {
        undoIgnore: true
        // Required to merge this edit with the last undo level.
      }
    );
    const blocks = (0, import_blocks.parse)(fileTemplate?.content?.raw);
    (0, import_data7.dispatch)(import_core_data7.store).editEntityRecord(
      "postType",
      template.type,
      fileTemplate.id,
      {
        content: serializeBlocks,
        blocks,
        source: "theme"
      }
    );
    if (allowUndo) {
      const undoRevert = () => {
        (0, import_data7.dispatch)(import_core_data7.store).editEntityRecord(
          "postType",
          template.type,
          edited.id,
          {
            content: serializeBlocks,
            blocks: edited.blocks,
            source: "custom"
          }
        );
      };
      (0, import_data7.dispatch)(import_notices7.store).createSuccessNotice(
        (0, import_i18n8.__)("Template reset."),
        {
          type: "snackbar",
          id: noticeId,
          actions: [
            {
              label: (0, import_i18n8.__)("Undo"),
              onClick: undoRevert
            }
          ]
        }
      );
    }
  } catch (error) {
    const errorMessage = error.message && error.code !== "unknown_error" ? error.message : (0, import_i18n8.__)("Template revert failed. Please reload.");
    (0, import_data7.dispatch)(import_notices7.store).createErrorNotice(errorMessage, {
      type: "snackbar"
    });
  }
};
var resetPostAction = {
  isEligible: (item) => {
    return isTemplateOrTemplatePart(item) && item?.source === "custom" && (Boolean(item.type === "wp_template" && item?.plugin) || item?.has_theme_file);
  },
  icon: backup_default,
  RenderModal: ({ items, closeModal, onActionPerformed }) => {
    const [isBusy, setIsBusy] = (0, import_element7.useState)(false);
    const { saveEditedEntityRecord } = (0, import_data7.useDispatch)(import_core_data7.store);
    const { createSuccessNotice, createErrorNotice } = (0, import_data7.useDispatch)(import_notices7.store);
    const onConfirm = async () => {
      try {
        for (const template of items) {
          await revertTemplate(template, {
            allowUndo: false
          });
          await saveEditedEntityRecord(
            "postType",
            template.type,
            template.id
          );
        }
        createSuccessNotice(
          items.length > 1 ? (0, import_i18n8.sprintf)(
            /* translators: %d: The number of items. */
            (0, import_i18n8.__)("%d items reset."),
            items.length
          ) : (0, import_i18n8.sprintf)(
            /* translators: %s: The template/part's name. */
            (0, import_i18n8.__)('"%s" reset.'),
            getItemTitle(items[0])
          ),
          {
            type: "snackbar",
            id: "revert-template-action"
          }
        );
      } catch (error) {
        let fallbackErrorMessage;
        if (items[0].type === "wp_template") {
          fallbackErrorMessage = items.length === 1 ? (0, import_i18n8.__)(
            "An error occurred while reverting the template."
          ) : (0, import_i18n8.__)(
            "An error occurred while reverting the templates."
          );
        } else {
          fallbackErrorMessage = items.length === 1 ? (0, import_i18n8.__)(
            "An error occurred while reverting the template part."
          ) : (0, import_i18n8.__)(
            "An error occurred while reverting the template parts."
          );
        }
        const typedError = error;
        const errorMessage = typedError.message && typedError.code !== "unknown_error" ? typedError.message : fallbackErrorMessage;
        createErrorNotice(errorMessage, { type: "snackbar" });
      }
    };
    return /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)(import_components7.__experimentalVStack, { spacing: "5", children: [
      /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_components7.__experimentalText, { children: (0, import_i18n8.__)("Reset to default and clear all customizations?") }),
      /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)(import_components7.__experimentalHStack, { justify: "right", children: [
        /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
          import_components7.Button,
          {
            __next40pxDefaultSize: true,
            variant: "tertiary",
            onClick: closeModal,
            disabled: isBusy,
            accessibleWhenDisabled: true,
            children: (0, import_i18n8.__)("Cancel")
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
          import_components7.Button,
          {
            __next40pxDefaultSize: true,
            variant: "primary",
            onClick: async () => {
              setIsBusy(true);
              await onConfirm();
              onActionPerformed?.(items);
              setIsBusy(false);
              closeModal?.();
            },
            isBusy,
            disabled: isBusy,
            accessibleWhenDisabled: true,
            children: (0, import_i18n8.__)("Reset")
          }
        )
      ] })
    ] });
  }
};
var action_default7 = resetPostAction;

// packages/core-actions/build-module/post_type_supports/restore/action.mjs
var import_core_data8 = __toESM(require_core_data(), 1);
var import_i18n9 = __toESM(require_i18n(), 1);
var import_notices8 = __toESM(require_notices(), 1);
var restorePost = {
  icon: backup_default,
  isEligible(item) {
    return !isTemplateOrTemplatePart(item) && item.type !== "wp_block" && item.status === "trash" && item.permissions?.update;
  },
  async callback(posts, { registry, onActionPerformed }) {
    const { createSuccessNotice, createErrorNotice } = registry.dispatch(import_notices8.store);
    const { editEntityRecord, saveEditedEntityRecord } = registry.dispatch(import_core_data8.store);
    await Promise.allSettled(
      posts.map((post) => {
        return editEntityRecord("postType", post.type, post.id, {
          status: "draft"
        });
      })
    );
    const promiseResult = await Promise.allSettled(
      posts.map((post) => {
        return saveEditedEntityRecord("postType", post.type, post.id, {
          throwOnError: true
        });
      })
    );
    if (promiseResult.every(({ status }) => status === "fulfilled")) {
      let successMessage;
      if (posts.length === 1) {
        successMessage = (0, import_i18n9.sprintf)(
          /* translators: %s: The number of posts. */
          (0, import_i18n9.__)('"%s" has been restored.'),
          getItemTitle(posts[0])
        );
      } else if (posts[0].type === "page") {
        successMessage = (0, import_i18n9.sprintf)(
          /* translators: %d: The number of posts. */
          (0, import_i18n9.__)("%d pages have been restored."),
          posts.length
        );
      } else {
        successMessage = (0, import_i18n9.sprintf)(
          /* translators: %d: The number of posts. */
          (0, import_i18n9.__)("%d posts have been restored."),
          posts.length
        );
      }
      createSuccessNotice(successMessage, {
        type: "snackbar",
        id: "restore-post-action"
      });
      if (onActionPerformed) {
        onActionPerformed(posts);
      }
    } else {
      let errorMessage;
      if (promiseResult.length === 1) {
        const typedError = promiseResult[0];
        if (typedError.reason?.message) {
          errorMessage = typedError.reason.message;
        } else {
          errorMessage = (0, import_i18n9.__)(
            "An error occurred while restoring the post."
          );
        }
      } else {
        const errorMessages = /* @__PURE__ */ new Set();
        const failedPromises = promiseResult.filter(
          ({ status }) => status === "rejected"
        );
        for (const failedPromise of failedPromises) {
          const typedError = failedPromise;
          if (typedError.reason?.message) {
            errorMessages.add(typedError.reason.message);
          }
        }
        if (errorMessages.size === 0) {
          errorMessage = (0, import_i18n9.__)(
            "An error occurred while restoring the posts."
          );
        } else if (errorMessages.size === 1) {
          errorMessage = (0, import_i18n9.sprintf)(
            /* translators: %s: an error message */
            (0, import_i18n9.__)("An error occurred while restoring the posts: %s"),
            [...errorMessages][0]
          );
        } else {
          errorMessage = (0, import_i18n9.sprintf)(
            /* translators: %s: a list of comma separated error messages */
            (0, import_i18n9.__)(
              "Some errors occurred while restoring the posts: %s"
            ),
            [...errorMessages].join(",")
          );
        }
      }
      createErrorNotice(errorMessage, {
        type: "snackbar"
      });
    }
  }
};
var action_default8 = restorePost;

// packages/core-actions/build-module/post_type_supports/view-post/action.mjs
var viewPost = {
  icon: external_default,
  isEligible(post) {
    return post.status !== "trash";
  },
  callback(posts, { onActionPerformed }) {
    const post = posts[0];
    window.open(post?.link, "_blank");
    if (onActionPerformed) {
      onActionPerformed(posts);
    }
  }
};
var action_default9 = viewPost;

// packages/core-actions/build-module/post_type_supports/view-post-revisions/action.mjs
var import_url2 = __toESM(require_url(), 1);
var import_i18n10 = __toESM(require_i18n(), 1);
var viewPostRevisions = {
  label(items) {
    const revisionsCount = items[0]._links?.["version-history"]?.[0]?.count ?? 0;
    return (0, import_i18n10.sprintf)(
      /* translators: %d: number of revisions. */
      (0, import_i18n10.__)("View revisions (%d)"),
      revisionsCount
    );
  },
  isEligible(post) {
    if (post.status === "trash") {
      return false;
    }
    const lastRevisionId = post?._links?.["predecessor-version"]?.[0]?.id ?? null;
    const revisionsCount = post?._links?.["version-history"]?.[0]?.count ?? 0;
    return !!lastRevisionId && revisionsCount > 1;
  },
  callback(posts, { onActionPerformed }) {
    const post = posts[0];
    const href = (0, import_url2.addQueryArgs)("revision.php", {
      revision: post?._links?.["predecessor-version"]?.[0]?.id
    });
    document.location.href = href;
    if (onActionPerformed) {
      onActionPerformed(posts);
    }
  }
};
var action_default10 = viewPostRevisions;

// packages/core-actions/build-module/post_type_supports/index.mjs
var actions = {
  "delete-post": action_default,
  "duplicate-post": action_default2,
  "move-to-trash": action_default3,
  "order-pages": action_default4,
  "permanently-delete": action_default5,
  "rename-post": action_default6,
  "reset-post": action_default7,
  restore: action_default8,
  "view-post": action_default9,
  "view-post-revisions": action_default10
};
var post_type_supports_default = actions;
export {
  post_type_supports_default as default
};
