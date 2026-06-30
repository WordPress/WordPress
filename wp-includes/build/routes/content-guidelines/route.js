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

// package-external:@wordpress/block-library
var require_block_library = __commonJS({
  "package-external:@wordpress/block-library"(exports, module) {
    module.exports = window.wp.blockLibrary;
  }
});

// routes/content-guidelines/route.ts
var import_i18n = __toESM(require_i18n());

// routes/content-guidelines/bootstrap-block-registry.ts
var import_data = __toESM(require_data());
var import_blocks = __toESM(require_blocks());
var import_block_library = __toESM(require_block_library());
var bootstrapped = false;
function bootstrapBlockRegistry() {
  if (bootstrapped) {
    return;
  }
  bootstrapped = true;
  (0, import_data.dispatch)(import_blocks.store).reapplyBlockTypeFilters();
  (0, import_block_library.registerCoreBlocks)();
}

// routes/content-guidelines/route.ts
var route = {
  beforeLoad: bootstrapBlockRegistry,
  title: () => (0, import_i18n.__)("Guidelines")
};
export {
  route
};
