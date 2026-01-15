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

// package-external:@wordpress/element
var require_element = __commonJS({
  "package-external:@wordpress/element"(exports, module) {
    module.exports = window.wp.element;
  }
});

// package-external:@wordpress/preferences
var require_preferences = __commonJS({
  "package-external:@wordpress/preferences"(exports, module) {
    module.exports = window.wp.preferences;
  }
});

// routes/template-part-list/route.ts
var import_data4 = __toESM(require_data());
var import_core_data2 = __toESM(require_core_data());
var import_i18n = __toESM(require_i18n());

// packages/views/build-module/preference-keys.js
function generatePreferenceKey(kind, name, slug) {
  return `dataviews-${kind}-${name}-${slug}`;
}

// packages/views/build-module/use-view.js
var import_element = __toESM(require_element());
var import_data = __toESM(require_data());
var import_preferences = __toESM(require_preferences());

// packages/views/build-module/load-view.js
var import_data2 = __toESM(require_data());
var import_preferences2 = __toESM(require_preferences());
async function loadView(config) {
  const { kind, name, slug, defaultView, queryParams } = config;
  const preferenceKey = generatePreferenceKey(kind, name, slug);
  const persistedView = (0, import_data2.select)(import_preferences2.store).get(
    "core/views",
    preferenceKey
  );
  const baseView = persistedView ?? defaultView;
  const page = queryParams?.page ?? 1;
  const search = queryParams?.search ?? "";
  return {
    ...baseView,
    page,
    search
  };
}

// routes/template-part-list/view-utils.ts
var import_data3 = __toESM(require_data());
var import_core_data = __toESM(require_core_data());
var DEFAULT_VIEW = {
  type: "grid",
  sort: {
    field: "date",
    direction: "desc"
  },
  fields: [],
  titleField: "title",
  mediaField: "preview"
};
var DEFAULT_VIEWS = [
  {
    slug: "all",
    label: "All Template Parts",
    view: {
      ...DEFAULT_VIEW
    }
  },
  {
    slug: "header",
    label: "Headers",
    view: {
      ...DEFAULT_VIEW,
      filters: [
        {
          field: "area",
          operator: "is",
          value: "header"
        }
      ]
    }
  },
  {
    slug: "footer",
    label: "Footers",
    view: {
      ...DEFAULT_VIEW,
      filters: [
        {
          field: "area",
          operator: "is",
          value: "footer"
        }
      ]
    }
  },
  {
    slug: "sidebar",
    label: "Sidebars",
    view: {
      ...DEFAULT_VIEW,
      filters: [
        {
          field: "area",
          operator: "is",
          value: "sidebar"
        }
      ]
    }
  },
  {
    slug: "overlay",
    label: "Overlays",
    view: {
      ...DEFAULT_VIEW,
      filters: [
        {
          field: "area",
          operator: "is",
          value: "overlay"
        }
      ]
    }
  },
  {
    slug: "uncategorized",
    label: "General",
    view: {
      ...DEFAULT_VIEW,
      filters: [
        {
          field: "area",
          operator: "is",
          value: "uncategorized"
        }
      ]
    }
  }
];
function getDefaultView(postType, area) {
  const viewConfig = DEFAULT_VIEWS.find((v) => v.slug === area);
  return viewConfig?.view || DEFAULT_VIEW;
}
async function ensureView(area, search) {
  const postTypeObject = await (0, import_data3.resolveSelect)(import_core_data.store).getPostType("wp_template_part");
  const defaultView = getDefaultView(postTypeObject, area);
  return loadView({
    kind: "postType",
    name: "wp_template_part",
    slug: area ?? "all",
    defaultView,
    queryParams: search
  });
}
function viewToQuery(view) {
  const result = {};
  if (void 0 !== view.perPage) {
    result.per_page = view.perPage;
  }
  if (void 0 !== view.page) {
    result.page = view.page;
  }
  if (![void 0, ""].includes(view.search)) {
    result.search = view.search;
  }
  if (void 0 !== view.sort?.field) {
    result.orderby = view.sort.field;
  }
  if (void 0 !== view.sort?.direction) {
    result.order = view.sort.direction;
  }
  const areaFilter = view.filters?.find(
    (filter) => filter.field === "area"
  );
  if (areaFilter) {
    result.area = areaFilter.value;
  }
  return result;
}

// routes/template-part-list/route.ts
var route = {
  title: () => (0, import_i18n.__)("Template Parts"),
  async canvas(context) {
    const { params, search } = context;
    const view = await ensureView(params.area, {
      page: search.page,
      search: search.search
    });
    if (view.type !== "list") {
      return void 0;
    }
    if (search.postIds && search.postIds.length > 0) {
      const postId = search.postIds[0].toString();
      return {
        postType: "wp_template_part",
        postId,
        isPreview: true,
        editLink: `/types/wp_template_part/edit/${encodeURIComponent(
          postId
        )}`
      };
    }
    const query = viewToQuery(view);
    const posts = await (0, import_data4.resolveSelect)(import_core_data2.store).getEntityRecords(
      "postType",
      "wp_template_part",
      { ...query, per_page: 1 }
    );
    if (posts && posts.length > 0) {
      const postId = posts[0].id.toString();
      return {
        postType: "wp_template_part",
        postId,
        isPreview: true,
        editLink: `/types/wp_template_part/edit/${encodeURIComponent(
          postId
        )}`
      };
    }
    return void 0;
  }
};
export {
  route
};
