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

// package-external:@wordpress/html-entities
var require_html_entities = __commonJS({
  "package-external:@wordpress/html-entities"(exports, module) {
    module.exports = window.wp.htmlEntities;
  }
});

// package-external:@wordpress/i18n
var require_i18n = __commonJS({
  "package-external:@wordpress/i18n"(exports, module) {
    module.exports = window.wp.i18n;
  }
});

// routes/media-editor/route.ts
var import_core_data = __toESM(require_core_data());
var import_data = __toESM(require_data());
var import_html_entities = __toESM(require_html_entities());
var import_i18n = __toESM(require_i18n());
import { notFound } from "@wordpress/route";
function getAttachmentId(id) {
  const attachmentId = parseInt(id, 10);
  if (Number.isNaN(attachmentId) || attachmentId <= 0) {
    throw notFound();
  }
  return attachmentId;
}
function getAttachmentTitle(attachment) {
  const title = typeof attachment.title === "string" ? attachment.title : attachment.title?.rendered || attachment.title?.raw;
  return title ? (0, import_html_entities.decodeEntities)(title) : (0, import_i18n.__)("Edit media");
}
var route = {
  beforeLoad: async ({ params }) => {
    if (!window?.__experimentalMediaEditor) {
      throw notFound();
    }
    const attachmentId = getAttachmentId(params.id);
    try {
      const attachment = await (0, import_data.resolveSelect)(import_core_data.store).getEntityRecord(
        "postType",
        "attachment",
        attachmentId
      );
      if (!attachment) {
        throw notFound();
      }
    } catch {
      throw notFound();
    }
  },
  title: async ({ params }) => {
    const attachmentId = getAttachmentId(params.id);
    const attachment = await (0, import_data.resolveSelect)(import_core_data.store).getEntityRecord(
      "postType",
      "attachment",
      attachmentId
    );
    return attachment ? getAttachmentTitle(attachment) : (0, import_i18n.__)("Edit media");
  }
};
export {
  route
};
