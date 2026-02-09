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

// routes/template-list/route.ts
var import_data3 = __toESM(require_data());
var import_core_data = __toESM(require_core_data());
var import_i18n = __toESM(require_i18n());

// packages/views/build-module/use-view.mjs
var import_element = __toESM(require_element(), 1);
var import_data = __toESM(require_data(), 1);
var import_preferences = __toESM(require_preferences(), 1);

// packages/views/build-module/preference-keys.mjs
function generatePreferenceKey(kind, name, slug) {
  return `dataviews-${kind}-${name}-${slug}`;
}

// packages/views/build-module/filter-utils.mjs
function mergeActiveViewOverrides(view, activeViewOverrides, defaultView) {
  if (!activeViewOverrides) {
    return view;
  }
  let result = view;
  if (activeViewOverrides.filters && activeViewOverrides.filters.length > 0) {
    const activeFields = new Set(
      activeViewOverrides.filters.map((f) => f.field)
    );
    const preserved = (view.filters ?? []).filter(
      (f) => !activeFields.has(f.field)
    );
    result = {
      ...result,
      filters: [...preserved, ...activeViewOverrides.filters]
    };
  }
  if (activeViewOverrides.sort) {
    const isDefaultSort = defaultView && view.sort?.field === defaultView.sort?.field && view.sort?.direction === defaultView.sort?.direction;
    if (isDefaultSort) {
      result = {
        ...result,
        sort: activeViewOverrides.sort
      };
    }
  }
  return result;
}

// packages/views/build-module/load-view.mjs
var import_data2 = __toESM(require_data(), 1);
var import_preferences2 = __toESM(require_preferences(), 1);
async function loadView(config) {
  const { kind, name, slug, defaultView, activeViewOverrides, queryParams } = config;
  const preferenceKey = generatePreferenceKey(kind, name, slug);
  const persistedView = (0, import_data2.select)(import_preferences2.store).get(
    "core/views",
    preferenceKey
  );
  const baseView = persistedView ?? defaultView;
  const page = queryParams?.page ?? 1;
  const search = queryParams?.search ?? "";
  return mergeActiveViewOverrides(
    {
      ...baseView,
      page,
      search
    },
    activeViewOverrides,
    defaultView
  );
}

// routes/template-list/view-utils.ts
var DEFAULT_VIEW = {
  type: "grid",
  perPage: 20,
  sort: {
    field: "title",
    direction: "asc"
  },
  fields: ["author", "active", "slug"],
  titleField: "title",
  descriptionField: "description",
  mediaField: "preview",
  filters: []
};
var DEFAULT_VIEW_LEGACY = {
  ...DEFAULT_VIEW,
  fields: ["author"]
};
function getActiveViewOverridesForTab(activeView) {
  if (activeView === "user") {
    return {
      sort: { field: "date", direction: "desc" }
    };
  }
  if (activeView === "active") {
    return {};
  }
  return {
    filters: [
      {
        field: "author",
        operator: "isAny",
        value: [activeView]
      }
    ]
  };
}
async function ensureView(activeView, search) {
  return loadView({
    kind: "postType",
    name: "wp_template",
    slug: "default-new",
    defaultView: DEFAULT_VIEW,
    activeViewOverrides: getActiveViewOverridesForTab(
      activeView ?? "active"
    ),
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
  return result;
}

// routes/template-list/route.ts
var route = {
  title: () => (0, import_i18n.__)("Templates"),
  async canvas(context) {
    const { params, search } = context;
    const view = await ensureView(params.activeView, {
      page: search.page,
      search: search.search
    });
    if (view.type !== "list") {
      return void 0;
    }
    if (search.postIds && search.postIds.length > 0) {
      const postId = search.postIds[0].toString();
      return {
        postType: "wp_template",
        postId,
        isPreview: true,
        editLink: `/types/wp_template/edit/${encodeURIComponent(
          postId
        )}`
      };
    }
    const query = viewToQuery(view);
    const posts = await (0, import_data3.resolveSelect)(import_core_data.store).getEntityRecords(
      "postType",
      "wp_template",
      { ...query, per_page: 1 }
    );
    if (posts && posts.length > 0) {
      const postId = posts[0].id.toString();
      return {
        postType: "wp_template",
        postId,
        isPreview: true,
        editLink: `/types/wp_template/edit/${encodeURIComponent(
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
