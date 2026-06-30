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

// package-external:@wordpress/core-data
var require_core_data = __commonJS({
  "package-external:@wordpress/core-data"(exports, module) {
    module.exports = window.wp.coreData;
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

// routes/taxonomy-edit/route.ts
var import_data = __toESM(require_data());
var import_core_data = __toESM(require_core_data());
var import_i18n = __toESM(require_i18n());
var import_notices = __toESM(require_notices());
import { redirect } from "@wordpress/route";
var USER_TAXONOMY_POST_TYPE = "wp_user_taxonomy";
var NEW_ID = "new";
var route = {
  beforeLoad: async ({ params }) => {
    if (params.id === NEW_ID) {
      return;
    }
    const id = parseInt(params.id, 10);
    let record;
    if (!Number.isNaN(id)) {
      try {
        record = await (0, import_data.resolveSelect)(import_core_data.store).getEntityRecord(
          "postType",
          USER_TAXONOMY_POST_TYPE,
          id
        );
      } catch {
      }
    }
    if (!record) {
      (0, import_data.dispatch)(import_notices.store).createErrorNotice(
        (0, import_i18n.__)("Taxonomy not found."),
        { type: "snackbar" }
      );
      throw redirect({ throw: true, to: "/" });
    }
  },
  title: async ({ params }) => {
    if (params.id === NEW_ID) {
      return (0, import_i18n.__)("Add taxonomy");
    }
    const id = parseInt(params.id, 10);
    const record = await (0, import_data.resolveSelect)(import_core_data.store).getEntityRecord(
      "postType",
      USER_TAXONOMY_POST_TYPE,
      id
    );
    return record?.title?.raw ?? record?.title?.rendered ?? (0, import_i18n.__)("Taxonomy");
  }
};
export {
  route
};
