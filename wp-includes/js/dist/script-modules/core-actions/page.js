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

// package-external:@wordpress/i18n
var require_i18n = __commonJS({
  "package-external:@wordpress/i18n"(exports, module) {
    module.exports = window.wp.i18n;
  }
});

// package-external:@wordpress/html-entities
var require_html_entities = __commonJS({
  "package-external:@wordpress/html-entities"(exports, module) {
    module.exports = window.wp.htmlEntities;
  }
});

// package-external:@wordpress/components
var require_components = __commonJS({
  "package-external:@wordpress/components"(exports, module) {
    module.exports = window.wp.components;
  }
});

// package-external:@wordpress/data
var require_data = __commonJS({
  "package-external:@wordpress/data"(exports, module) {
    module.exports = window.wp.data;
  }
});

// package-external:@wordpress/core-data
var require_core_data = __commonJS({
  "package-external:@wordpress/core-data"(exports, module) {
    module.exports = window.wp.coreData;
  }
});

// package-external:@wordpress/notices
var require_notices = __commonJS({
  "package-external:@wordpress/notices"(exports, module) {
    module.exports = window.wp.notices;
  }
});

// vendor-external:react/jsx-runtime
var require_jsx_runtime = __commonJS({
  "vendor-external:react/jsx-runtime"(exports, module) {
    module.exports = window.ReactJSXRuntime;
  }
});

// packages/core-actions/build-module/page/set-as-homepage/action.mjs
var import_i18n3 = __toESM(require_i18n(), 1);

// packages/core-actions/build-module/shared/utils.mjs
var import_html_entities = __toESM(require_html_entities(), 1);
var import_i18n = __toESM(require_i18n(), 1);
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

// packages/core-actions/build-module/shared/has-action-link.mjs
function hasActionLink(item, action) {
  if (!item._links) {
    return true;
  }
  return !!item._links[action];
}

// packages/core-actions/build-module/page/set-site-page-modal.mjs
var import_components = __toESM(require_components(), 1);
var import_data = __toESM(require_data(), 1);
var import_core_data2 = __toESM(require_core_data(), 1);
var import_i18n2 = __toESM(require_i18n(), 1);
var import_notices = __toESM(require_notices(), 1);

// packages/core-actions/build-module/page/refetch-pages.mjs
var import_core_data = __toESM(require_core_data(), 1);
async function refetchPages(registry, ids) {
  const include = [...new Set(ids.filter(Boolean))];
  if (!include.length) {
    return;
  }
  const query = { include };
  registry.dispatch(import_core_data.store).invalidateResolution("getEntityRecords", [
    "postType",
    "page",
    query
  ]);
  await registry.resolveSelect(import_core_data.store).getEntityRecords("postType", "page", query);
}

// packages/core-actions/build-module/page/set-site-page-modal.mjs
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
function SetSitePageModal({
  setting,
  items,
  closeModal,
  getText: getText3,
  submitLabel,
  successNotice,
  errorNotice
}) {
  const [item] = items;
  const { siteSettings, currentPage, isSaving } = (0, import_data.useSelect)(
    (select) => {
      const { getEntityRecord, isSavingEntityRecord } = select(import_core_data2.store);
      const settings = getEntityRecord("root", "site");
      const currentId = settings?.[setting];
      return {
        siteSettings: settings,
        // A setting holding no page is `0`, and `getEntityRecord()`
        // without a key would request the whole collection.
        currentPage: currentId ? getEntityRecord("postType", "page", currentId) : void 0,
        isSaving: isSavingEntityRecord("root", "site")
      };
    },
    [setting]
  );
  const registry = (0, import_data.useRegistry)();
  const { saveEntityRecord } = (0, import_data.useDispatch)(import_core_data2.store);
  const { createSuccessNotice, createErrorNotice } = (0, import_data.useDispatch)(import_notices.store);
  async function onSubmit(event) {
    event.preventDefault();
    const previousId = siteSettings?.[setting];
    try {
      await saveEntityRecord(
        "root",
        "site",
        { [setting]: item.id, show_on_front: "page" },
        { throwOnError: true }
      );
    } catch (error) {
      const typedError = error;
      const errorMessage = typedError.message && typedError.code !== "unknown_error" ? typedError.message : errorNotice;
      createErrorNotice(errorMessage, { type: "snackbar" });
      closeModal?.();
      return;
    }
    createSuccessNotice(successNotice, { type: "snackbar" });
    closeModal?.();
    try {
      await refetchPages(registry, [item.id, previousId]);
    } catch {
    }
  }
  return /* @__PURE__ */ (0, import_jsx_runtime.jsx)("form", { onSubmit, children: /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(import_components.__experimentalVStack, { spacing: "5", children: [
    /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.__experimentalText, { children: getText3({
      pageTitle: getItemTitle(item, ""),
      currentPage,
      siteSettings
    }) }),
    /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(import_components.__experimentalHStack, { justify: "right", children: [
      /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
        import_components.Button,
        {
          __next40pxDefaultSize: true,
          variant: "tertiary",
          onClick: () => {
            closeModal?.();
          },
          disabled: isSaving,
          accessibleWhenDisabled: true,
          children: (0, import_i18n2.__)("Cancel")
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
        import_components.Button,
        {
          __next40pxDefaultSize: true,
          variant: "primary",
          type: "submit",
          disabled: isSaving,
          accessibleWhenDisabled: true,
          children: submitLabel
        }
      )
    ] })
  ] }) });
}

// packages/core-actions/build-module/page/set-as-homepage/action.mjs
var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
var getText = ({
  pageTitle,
  currentPage,
  siteSettings
}) => {
  let warning = "";
  if ("posts" === siteSettings?.show_on_front) {
    warning = (0, import_i18n3.__)(
      "This will replace the current homepage which is set to display latest posts."
    );
  } else if (currentPage) {
    warning = (0, import_i18n3.sprintf)(
      // translators: %s: title of the current home page.
      (0, import_i18n3.__)('This will replace the current homepage: "%s"'),
      getItemTitle(currentPage, "")
    );
  }
  return (0, import_i18n3.sprintf)(
    // translators: %1$s: title of the page to be set as the homepage, %2$s: homepage replacement warning message.
    (0, import_i18n3.__)('Set "%1$s" as the site homepage? %2$s'),
    pageTitle,
    warning
  ).trim();
};
var setAsHomepage = {
  isEligible(post) {
    return post.status === "publish" && hasActionLink(post, "wp:action-set-homepage");
  },
  RenderModal: ({ items, closeModal }) => {
    const submitLabel = (0, import_i18n3.__)("Set homepage");
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
      SetSitePageModal,
      {
        setting: "page_on_front",
        items,
        closeModal,
        getText,
        submitLabel,
        successNotice: (0, import_i18n3.__)("Homepage updated."),
        errorNotice: (0, import_i18n3.__)(
          "An error occurred while setting the homepage."
        )
      }
    );
  }
};
var action_default = setAsHomepage;

// packages/core-actions/build-module/page/set-as-posts-page/action.mjs
var import_i18n4 = __toESM(require_i18n(), 1);
var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
var getText2 = ({
  pageTitle,
  currentPage
}) => (0, import_i18n4.sprintf)(
  // translators: %1$s: title of the page to be set as the posts page, %2$s: posts page replacement warning message.
  (0, import_i18n4.__)('Set "%1$s" as the posts page? %2$s'),
  pageTitle,
  currentPage ? (0, import_i18n4.sprintf)(
    // translators: %s: title of the current posts page.
    (0, import_i18n4.__)('This will replace the current posts page: "%s"'),
    getItemTitle(currentPage, "")
  ) : (0, import_i18n4.__)("This page will show the latest posts.")
);
var setAsPostsPage = {
  isEligible(post) {
    return post.status === "publish" && hasActionLink(post, "wp:action-set-posts-page");
  },
  RenderModal: ({ items, closeModal }) => {
    const submitLabel = (0, import_i18n4.__)("Set posts page");
    return /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
      SetSitePageModal,
      {
        setting: "page_for_posts",
        items,
        closeModal,
        getText: getText2,
        submitLabel,
        successNotice: (0, import_i18n4.__)("Posts page updated."),
        errorNotice: (0, import_i18n4.__)(
          "An error occurred while setting the posts page."
        )
      }
    );
  }
};
var action_default2 = setAsPostsPage;

// packages/core-actions/build-module/page/index.mjs
var actions = {
  "set-as-homepage": action_default,
  "set-as-posts-page": action_default2
};
var page_default = actions;
export {
  page_default as default
};
