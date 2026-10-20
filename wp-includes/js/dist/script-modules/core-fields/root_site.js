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

// package-external:@wordpress/html-entities
var require_html_entities = __commonJS({
  "package-external:@wordpress/html-entities"(exports, module) {
    module.exports = window.wp.htmlEntities;
  }
});

// package-external:@wordpress/media-utils
var require_media_utils = __commonJS({
  "package-external:@wordpress/media-utils"(exports, module) {
    module.exports = window.wp.mediaUtils;
  }
});

// package-external:@wordpress/private-apis
var require_private_apis = __commonJS({
  "package-external:@wordpress/private-apis"(exports, module) {
    module.exports = window.wp.privateApis;
  }
});

// packages/core-fields/build-module/root_site/description/field.mjs
var import_html_entities = __toESM(require_html_entities(), 1);
var fieldExtensions = {
  getValue: ({ item }) => (0, import_html_entities.decodeEntities)(item.description ?? "")
};

// packages/core-fields/build-module/root_site/site_icon/field.mjs
var import_media_utils = __toESM(require_media_utils(), 1);

// packages/core-fields/build-module/lock-unlock.mjs
var import_private_apis = __toESM(require_private_apis(), 1);
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/core-fields"
);

// packages/core-fields/build-module/root_site/site_icon/field.mjs
var { MediaEdit } = unlock(import_media_utils.privateApis);
var fieldExtensions2 = {
  Edit: MediaEdit,
  // A site without an icon stores `0`, not an empty value.
  setValue: ({ value }) => ({
    site_icon: value ?? 0
  })
};

// packages/core-fields/build-module/root_site/site_logo/field.mjs
var import_media_utils2 = __toESM(require_media_utils(), 1);
var { MediaEdit: MediaEdit2 } = unlock(import_media_utils2.privateApis);
var fieldExtensions3 = {
  Edit: MediaEdit2,
  // A site without a logo stores `0`, not an empty value.
  setValue: ({ value }) => ({
    site_logo: value ?? 0
  })
};

// packages/core-fields/build-module/root_site/title/field.mjs
var import_html_entities2 = __toESM(require_html_entities(), 1);
var fieldExtensions4 = {
  getValue: ({ item }) => (0, import_html_entities2.decodeEntities)(item.title ?? "")
};

// packages/core-fields/build-module/root_site/index.mjs
var fields = {
  description: fieldExtensions,
  site_icon: fieldExtensions2,
  site_logo: fieldExtensions3,
  title: fieldExtensions4
};
var root_site_default = fields;
export {
  root_site_default as default
};
