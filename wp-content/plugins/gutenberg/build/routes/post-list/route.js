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

// routes/post-list/route.ts
var import_data4 = __toESM(require_data());
var import_core_data2 = __toESM(require_core_data());

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

// routes/post-list/view-utils.ts
var import_data3 = __toESM(require_data());
var import_core_data = __toESM(require_core_data());
var DEFAULT_VIEW = {
  type: "table",
  sort: {
    field: "date",
    direction: "desc"
  },
  fields: ["author", "status", "date"],
  titleField: "title",
  mediaField: "featured_media",
  descriptionField: "excerpt"
};
var DEFAULT_VIEWS = [
  {
    slug: "all",
    label: "All",
    view: {
      ...DEFAULT_VIEW
    }
  },
  {
    slug: "publish",
    label: "Published",
    view: {
      ...DEFAULT_VIEW,
      filters: [
        {
          field: "status",
          operator: "is",
          value: "publish"
        }
      ]
    }
  },
  {
    slug: "draft",
    label: "Draft",
    view: {
      ...DEFAULT_VIEW,
      filters: [
        {
          field: "status",
          operator: "is",
          value: "draft"
        }
      ]
    }
  },
  {
    slug: "pending",
    label: "Pending",
    view: {
      ...DEFAULT_VIEW,
      filters: [
        {
          field: "status",
          operator: "is",
          value: "pending"
        }
      ]
    }
  },
  {
    slug: "private",
    label: "Private",
    view: {
      ...DEFAULT_VIEW,
      filters: [
        {
          field: "status",
          operator: "is",
          value: "private"
        }
      ]
    }
  },
  {
    slug: "trash",
    label: "Trash",
    view: {
      ...DEFAULT_VIEW,
      filters: [
        {
          field: "status",
          operator: "is",
          value: "trash"
        }
      ]
    }
  }
];
function getDefaultView(postType, slug) {
  const viewConfig = DEFAULT_VIEWS.find((v) => v.slug === slug);
  const baseView = viewConfig?.view || DEFAULT_VIEW;
  return {
    ...baseView,
    showLevels: postType?.hierarchical
  };
}
async function ensureView(type, slug, search) {
  const postTypeObject = await (0, import_data3.resolveSelect)(import_core_data.store).getPostType(type);
  const defaultView = getDefaultView(postTypeObject, slug);
  return loadView({
    kind: "postType",
    name: type,
    slug: slug ?? "all",
    defaultView,
    queryParams: search
  });
}
function viewToQuery(view, postType) {
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
    let sortField = view.sort.field;
    if (sortField === "attached_to") {
      sortField = "parent";
    }
    result.orderby = sortField;
  }
  if (void 0 !== view.sort?.direction) {
    result.order = view.sort.direction;
  }
  if (view.showLevels) {
    result.orderby_hierarchy = true;
  }
  const status = view.filters?.find(
    (filter) => filter.field === "status"
  );
  if (status) {
    result.status = status.value;
  } else if (postType === "attachment") {
    result.status = "inherit";
  } else {
    result.status = "draft,future,pending,private,publish";
  }
  const author = view.filters?.find(
    (filter) => filter.field === "author"
  );
  if (author && author.operator === "is") {
    result.author = author.value;
  } else if (author && author.operator === "isNot") {
    result.author_exclude = author.value;
  }
  const commentStatus = view.filters?.find(
    (filter) => filter.field === "comment_status"
  );
  if (commentStatus && commentStatus.operator === "is") {
    result.comment_status = commentStatus.value;
  } else if (commentStatus && commentStatus.operator === "isNot") {
    result.comment_status_exclude = commentStatus.value;
  }
  const mediaType = view.filters?.find(
    (filter) => filter.field === "media_type"
  );
  if (mediaType) {
    result.media_type = mediaType.value;
  }
  if (postType === "attachment") {
    result._embed = "wp:attached-to";
  }
  return result;
}

// routes/post-list/route.ts
var route = {
  async canvas(context) {
    const { params, search } = context;
    const view = await ensureView(params.type, params.slug, {
      page: search.page,
      search: search.search
    });
    if (view.type !== "list") {
      return void 0;
    }
    if (search.postIds && search.postIds.length > 0) {
      return {
        postType: params.type,
        postId: search.postIds[0].toString(),
        isPreview: true
      };
    }
    const query = viewToQuery(view, params.type);
    const posts = await (0, import_data4.resolveSelect)(import_core_data2.store).getEntityRecords(
      "postType",
      params.type,
      { ...query, per_page: 1 }
    );
    if (posts && posts.length > 0) {
      return {
        postType: params.type,
        postId: posts[0].id.toString(),
        isPreview: true
      };
    }
    return void 0;
  }
};
export {
  route
};
