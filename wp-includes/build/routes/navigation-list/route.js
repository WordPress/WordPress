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

// routes/navigation-list/route.ts
var import_data = __toESM(require_data());
var import_core_data = __toESM(require_core_data());
var import_i18n = __toESM(require_i18n());
var NAVIGATION_POST_TYPE = "wp_navigation";
var PRELOADED_NAVIGATION_MENUS_QUERY = {
  per_page: -1,
  status: ["publish", "draft"],
  order: "desc",
  orderby: "date"
};
var route = {
  title: () => (0, import_i18n.__)("Navigation"),
  canvas: async ({
    search
  }) => {
    const [firstNavigation] = await (0, import_data.resolveSelect)(
      import_core_data.store
    ).getEntityRecords(
      "postType",
      NAVIGATION_POST_TYPE,
      PRELOADED_NAVIGATION_MENUS_QUERY
    );
    if (!firstNavigation) {
      return { postType: NAVIGATION_POST_TYPE, isPreview: true };
    }
    const postId = search.ids ? parseInt(search.ids[0]) : firstNavigation.id;
    return {
      postType: NAVIGATION_POST_TYPE,
      postId,
      isPreview: true,
      editLink: `/types/wp_navigation/edit/${postId}`
    };
  },
  loader: async () => {
    await Promise.all([
      // Preload navigation menus
      (0, import_data.resolveSelect)(import_core_data.store).getEntityRecords(
        "postType",
        NAVIGATION_POST_TYPE,
        PRELOADED_NAVIGATION_MENUS_QUERY
      ),
      (0, import_data.resolveSelect)(import_core_data.store).canUser("create", {
        kind: "postType",
        name: NAVIGATION_POST_TYPE
      }),
      // Preload post type object (what usePostFields needs)
      (0, import_data.resolveSelect)(import_core_data.store).getPostType(NAVIGATION_POST_TYPE),
      // Preload users data (what usePostFields needs for author field)
      (0, import_data.resolveSelect)(import_core_data.store).getEntityRecords("root", "user", {
        per_page: -1
      })
    ]);
  }
};
export {
  route
};
