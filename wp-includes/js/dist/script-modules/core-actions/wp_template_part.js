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

// package-external:@wordpress/notices
var require_notices = __commonJS({
  "package-external:@wordpress/notices"(exports, module) {
    module.exports = window.wp.notices;
  }
});

// package-external:@wordpress/element
var require_element = __commonJS({
  "package-external:@wordpress/element"(exports, module) {
    module.exports = window.wp.element;
  }
});

// package-external:@wordpress/blocks
var require_blocks = __commonJS({
  "package-external:@wordpress/blocks"(exports, module) {
    module.exports = window.wp.blocks;
  }
});

// package-external:@wordpress/patterns
var require_patterns = __commonJS({
  "package-external:@wordpress/patterns"(exports, module) {
    module.exports = window.wp.patterns;
  }
});

// package-external:@wordpress/private-apis
var require_private_apis = __commonJS({
  "package-external:@wordpress/private-apis"(exports, module) {
    module.exports = window.wp.privateApis;
  }
});

// package-external:@wordpress/html-entities
var require_html_entities = __commonJS({
  "package-external:@wordpress/html-entities"(exports, module) {
    module.exports = window.wp.htmlEntities;
  }
});

// vendor-external:react/jsx-runtime
var require_jsx_runtime = __commonJS({
  "vendor-external:react/jsx-runtime"(exports, module) {
    module.exports = window.ReactJSXRuntime;
  }
});

// packages/core-actions/build-module/wp_template_part/duplicate-template-part/action.mjs
var import_data = __toESM(require_data(), 1);
var import_i18n2 = __toESM(require_i18n(), 1);
var import_notices = __toESM(require_notices(), 1);
var import_element = __toESM(require_element(), 1);
var import_blocks = __toESM(require_blocks(), 1);
var import_patterns = __toESM(require_patterns(), 1);

// packages/core-actions/build-module/lock-unlock.mjs
var import_private_apis = __toESM(require_private_apis(), 1);
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/core-actions"
);

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

// packages/core-actions/build-module/wp_template_part/duplicate-template-part/action.mjs
var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
var { CreateTemplatePartModalContents } = unlock(import_patterns.privateApis);
var duplicateTemplatePart = {
  isEligible: (item) => item.type === "wp_template_part",
  RenderModal: ({ items, closeModal }) => {
    const [item] = items;
    const blocks = (0, import_element.useMemo)(() => {
      return item.blocks ?? (0, import_blocks.parse)(
        typeof item.content === "string" ? item.content : item.content.raw,
        {
          __unstableSkipMigrationLogs: true
        }
      );
    }, [item.content, item.blocks]);
    const { createSuccessNotice } = (0, import_data.useDispatch)(import_notices.store);
    function onTemplatePartSuccess(templatePart) {
      createSuccessNotice(
        (0, import_i18n2.sprintf)(
          // translators: %s: The new template part's title e.g. 'Call to action (copy)'.
          (0, import_i18n2._x)('"%s" duplicated.', "template part"),
          getItemTitle(templatePart)
        ),
        { type: "snackbar", id: "edit-site-patterns-success" }
      );
      closeModal?.();
    }
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
      CreateTemplatePartModalContents,
      {
        blocks,
        defaultArea: item.area,
        defaultTitle: (0, import_i18n2.sprintf)(
          /* translators: %s: Existing template part title */
          (0, import_i18n2._x)("%s (Copy)", "template part"),
          getItemTitle(item)
        ),
        onCreate: onTemplatePartSuccess,
        onError: closeModal,
        confirmLabel: (0, import_i18n2._x)("Duplicate", "action label"),
        closeModal: closeModal ?? (() => {
        })
      }
    );
  }
};
var action_default = duplicateTemplatePart;

// packages/core-actions/build-module/wp_template_part/index.mjs
var actions = {
  "duplicate-template-part": action_default
};
var wp_template_part_default = actions;
export {
  wp_template_part_default as default
};
