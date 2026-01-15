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

// package-external:@wordpress/components
var require_components = __commonJS({
  "package-external:@wordpress/components"(exports, module) {
    module.exports = window.wp.components;
  }
});

// vendor-external:react/jsx-runtime
var require_jsx_runtime = __commonJS({
  "vendor-external:react/jsx-runtime"(exports, module) {
    module.exports = window.ReactJSXRuntime;
  }
});

// package-external:@wordpress/element
var require_element = __commonJS({
  "package-external:@wordpress/element"(exports, module) {
    module.exports = window.wp.element;
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

// package-external:@wordpress/html-entities
var require_html_entities = __commonJS({
  "package-external:@wordpress/html-entities"(exports, module) {
    module.exports = window.wp.htmlEntities;
  }
});

// package-external:@wordpress/block-editor
var require_block_editor = __commonJS({
  "package-external:@wordpress/block-editor"(exports, module) {
    module.exports = window.wp.blockEditor;
  }
});

// package-external:@wordpress/blocks
var require_blocks = __commonJS({
  "package-external:@wordpress/blocks"(exports, module) {
    module.exports = window.wp.blocks;
  }
});

// package-external:@wordpress/private-apis
var require_private_apis = __commonJS({
  "package-external:@wordpress/private-apis"(exports, module) {
    module.exports = window.wp.privateApis;
  }
});

// package-external:@wordpress/primitives
var require_primitives = __commonJS({
  "package-external:@wordpress/primitives"(exports, module) {
    module.exports = window.wp.primitives;
  }
});

// routes/navigation-edit/stage.tsx
import { useParams } from "@wordpress/route";

// packages/admin-ui/build-module/breadcrumbs/index.js
var import_i18n = __toESM(require_i18n());
var import_components = __toESM(require_components());
var import_jsx_runtime = __toESM(require_jsx_runtime());
import { Link } from "@wordpress/route";
var BreadcrumbItem = ({
  item: { label, to }
}) => {
  if (!to) {
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)("li", { children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.__experimentalHeading, { level: 1, truncate: true, children: label }) });
  }
  return /* @__PURE__ */ (0, import_jsx_runtime.jsx)("li", { children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(Link, { to, children: label }) });
};
var Breadcrumbs = ({ items }) => {
  if (!items.length) {
    return null;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime.jsx)("nav", { "aria-label": (0, import_i18n.__)("Breadcrumbs"), children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
    import_components.__experimentalHStack,
    {
      as: "ul",
      className: "admin-ui-breadcrumbs__list",
      spacing: 0,
      justify: "flex-start",
      alignment: "center",
      children: items.map((item, index) => /* @__PURE__ */ (0, import_jsx_runtime.jsx)(BreadcrumbItem, { item }, index))
    }
  ) });
};
var breadcrumbs_default = Breadcrumbs;

// node_modules/clsx/dist/clsx.mjs
function r(e) {
  var t, f, n = "";
  if ("string" == typeof e || "number" == typeof e) n += e;
  else if ("object" == typeof e) if (Array.isArray(e)) {
    var o = e.length;
    for (t = 0; t < o; t++) e[t] && (f = r(e[t])) && (n && (n += " "), n += f);
  } else for (f in e) e[f] && (n && (n += " "), n += f);
  return n;
}
function clsx() {
  for (var e, t, f = 0, n = "", o = arguments.length; f < o; f++) (e = arguments[f]) && (t = r(e)) && (n && (n += " "), n += t);
  return n;
}
var clsx_default = clsx;

// packages/admin-ui/build-module/navigable-region/index.js
var import_element = __toESM(require_element());
var import_jsx_runtime2 = __toESM(require_jsx_runtime());
var NavigableRegion = (0, import_element.forwardRef)(
  ({ children, className, ariaLabel, as: Tag = "div", ...props }, ref) => {
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
      Tag,
      {
        ref,
        className: clsx_default("admin-ui-navigable-region", className),
        "aria-label": ariaLabel,
        role: "region",
        tabIndex: "-1",
        ...props,
        children
      }
    );
  }
);
NavigableRegion.displayName = "NavigableRegion";
var navigable_region_default = NavigableRegion;

// packages/admin-ui/build-module/page/header.js
var import_components3 = __toESM(require_components());

// packages/admin-ui/build-module/page/sidebar-toggle-slot.js
var import_components2 = __toESM(require_components());
var { Fill: SidebarToggleFill, Slot: SidebarToggleSlot } = (0, import_components2.createSlotFill)("SidebarToggle");

// packages/admin-ui/build-module/page/header.js
var import_jsx_runtime3 = __toESM(require_jsx_runtime());
function Header({
  breadcrumbs,
  badges,
  title,
  subTitle,
  actions,
  showSidebarToggle = true
}) {
  return /* @__PURE__ */ (0, import_jsx_runtime3.jsxs)(import_components3.__experimentalVStack, { className: "admin-ui-page__header", as: "header", children: [
    /* @__PURE__ */ (0, import_jsx_runtime3.jsxs)(import_components3.__experimentalHStack, { justify: "space-between", spacing: 2, children: [
      /* @__PURE__ */ (0, import_jsx_runtime3.jsxs)(import_components3.__experimentalHStack, { spacing: 2, justify: "left", children: [
        showSidebarToggle && /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
          SidebarToggleSlot,
          {
            bubblesVirtually: true,
            className: "admin-ui-page__sidebar-toggle-slot"
          }
        ),
        title && /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_components3.__experimentalHeading, { as: "h2", level: 3, weight: 500, truncate: true, children: title }),
        breadcrumbs,
        badges
      ] }),
      /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
        import_components3.__experimentalHStack,
        {
          style: { width: "auto", flexShrink: 0 },
          spacing: 2,
          className: "admin-ui-page__header-actions",
          children: actions
        }
      )
    ] }),
    subTitle && /* @__PURE__ */ (0, import_jsx_runtime3.jsx)("p", { className: "admin-ui-page__header-subtitle", children: subTitle })
  ] });
}

// packages/admin-ui/build-module/page/index.js
var import_jsx_runtime4 = __toESM(require_jsx_runtime());
function Page({
  breadcrumbs,
  badges,
  title,
  subTitle,
  children,
  className,
  actions,
  hasPadding = false,
  showSidebarToggle = true
}) {
  const classes = clsx_default("admin-ui-page", className);
  return /* @__PURE__ */ (0, import_jsx_runtime4.jsxs)(navigable_region_default, { className: classes, ariaLabel: title, children: [
    (title || breadcrumbs || badges) && /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
      Header,
      {
        breadcrumbs,
        badges,
        title,
        subTitle,
        actions,
        showSidebarToggle
      }
    ),
    hasPadding ? /* @__PURE__ */ (0, import_jsx_runtime4.jsx)("div", { className: "admin-ui-page__content has-padding", children }) : children
  ] });
}
Page.SidebarToggleFill = SidebarToggleFill;
var page_default = Page;

// routes/navigation-edit/stage.tsx
var import_data3 = __toESM(require_data());
var import_core_data2 = __toESM(require_core_data());
var import_i18n3 = __toESM(require_i18n());
var import_html_entities = __toESM(require_html_entities());

// routes/navigation-edit/editor/index.tsx
var import_element3 = __toESM(require_element());
var import_block_editor3 = __toESM(require_block_editor());
var import_blocks2 = __toESM(require_blocks());
var import_components5 = __toESM(require_components());
import { useEditorAssets } from "@wordpress/lazy-editor";

// routes/navigation-edit/editor/style.scss
var css = `.navigation-edit-editor__hidden-blocks {
  display: none;
}`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css));

// routes/navigation-edit/editor/content.tsx
var import_block_editor2 = __toESM(require_block_editor());
var import_data2 = __toESM(require_data());
var import_blocks = __toESM(require_blocks());
var import_element2 = __toESM(require_element());
var import_core_data = __toESM(require_core_data());

// routes/lock-unlock.ts
var import_private_apis = __toESM(require_private_apis());
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/routes"
);

// packages/icons/build-module/library/chevron-down.js
var import_primitives = __toESM(require_primitives());
var import_jsx_runtime5 = __toESM(require_jsx_runtime());
var chevron_down_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives.Path, { d: "M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z" }) });

// packages/icons/build-module/library/chevron-up.js
var import_primitives2 = __toESM(require_primitives());
var import_jsx_runtime6 = __toESM(require_jsx_runtime());
var chevron_up_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives2.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives2.Path, { d: "M6.5 12.4L12 8l5.5 4.4-.9 1.2L12 10l-4.5 3.6-1-1.2z" }) });

// packages/icons/build-module/library/more-vertical.js
var import_primitives3 = __toESM(require_primitives());
var import_jsx_runtime7 = __toESM(require_jsx_runtime());
var more_vertical_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives3.Path, { d: "M13 19h-2v-2h2v2zm0-6h-2v-2h2v2zm0-6h-2V5h2v2z" }) });

// routes/navigation-edit/editor/leaf-more-menu.tsx
var import_components4 = __toESM(require_components());
var import_data = __toESM(require_data());
var import_i18n2 = __toESM(require_i18n());
var import_block_editor = __toESM(require_block_editor());
var POPOVER_PROPS = {
  className: "block-editor-block-settings-menu__popover",
  placement: "bottom-start"
};
function LeafMoreMenu({
  block,
  ...props
}) {
  const { clientId } = block;
  const { moveBlocksDown, moveBlocksUp, removeBlocks } = (0, import_data.useDispatch)(import_block_editor.store);
  const removeLabel = (0, import_i18n2.sprintf)(
    /* translators: %s: block name */
    (0, import_i18n2.__)("Remove %s"),
    (0, import_block_editor.BlockTitle)({ clientId, maximumLength: 25 })
  );
  const rootClientId = (0, import_data.useSelect)(
    (select) => {
      const { getBlockRootClientId } = select(import_block_editor.store);
      return getBlockRootClientId(clientId);
    },
    [clientId]
  );
  return /* @__PURE__ */ React.createElement(
    import_components4.DropdownMenu,
    {
      icon: more_vertical_default,
      label: (0, import_i18n2.__)("Options"),
      className: "block-editor-block-settings-menu",
      popoverProps: POPOVER_PROPS,
      noIcons: true,
      ...props
    },
    ({ onClose }) => /* @__PURE__ */ React.createElement(React.Fragment, null, /* @__PURE__ */ React.createElement(import_components4.MenuGroup, null, /* @__PURE__ */ React.createElement(
      import_components4.MenuItem,
      {
        icon: chevron_up_default,
        onClick: () => {
          moveBlocksUp([clientId], rootClientId);
          onClose();
        }
      },
      (0, import_i18n2.__)("Move up")
    ), /* @__PURE__ */ React.createElement(
      import_components4.MenuItem,
      {
        icon: chevron_down_default,
        onClick: () => {
          moveBlocksDown([clientId], rootClientId);
          onClose();
        }
      },
      (0, import_i18n2.__)("Move down")
    )), /* @__PURE__ */ React.createElement(import_components4.MenuGroup, null, /* @__PURE__ */ React.createElement(
      import_components4.MenuItem,
      {
        onClick: () => {
          removeBlocks([clientId], false);
          onClose();
        }
      },
      removeLabel
    )))
  );
}

// routes/navigation-edit/editor/content.tsx
var { PrivateListView } = unlock(import_block_editor2.privateApis);
var MAX_PAGE_COUNT = 100;
var PAGES_QUERY = [
  "postType",
  "page",
  {
    per_page: MAX_PAGE_COUNT,
    _fields: ["id", "link", "menu_order", "parent", "title", "type"],
    // TODO: When https://core.trac.wordpress.org/ticket/39037 REST API support for multiple orderby
    // values is resolved, update 'orderby' to [ 'menu_order', 'post_title' ] to provide a consistent
    // sort.
    orderby: "menu_order",
    order: "asc"
  }
];
function NavigationMenuContent({
  rootClientId
}) {
  const { listViewRootClientId, isLoading } = (0, import_data2.useSelect)(
    (select) => {
      const {
        areInnerBlocksControlled,
        getBlockName,
        getBlockCount,
        getBlockOrder
      } = select(import_block_editor2.store);
      const { isResolving } = select(import_core_data.store);
      const blockClientIds = getBlockOrder(rootClientId);
      const hasOnlyPageListBlock = blockClientIds.length === 1 && getBlockName(blockClientIds[0]) === "core/page-list";
      const pageListHasBlocks = hasOnlyPageListBlock && getBlockCount(blockClientIds[0]) > 0;
      const isLoadingPages = isResolving(
        "getEntityRecords",
        PAGES_QUERY
      );
      return {
        listViewRootClientId: pageListHasBlocks ? blockClientIds[0] : rootClientId,
        // This is a small hack to wait for the navigation block
        // to actually load its inner blocks.
        isLoading: !areInnerBlocksControlled(rootClientId) || isLoadingPages
      };
    },
    [rootClientId]
  );
  const { replaceBlock, __unstableMarkNextChangeAsNotPersistent } = (0, import_data2.useDispatch)(import_block_editor2.store);
  const offCanvasOnselect = (0, import_element2.useCallback)(
    (block) => {
      if (block.name === "core/navigation-link" && !block.attributes.url) {
        __unstableMarkNextChangeAsNotPersistent();
        replaceBlock(
          block.clientId,
          (0, import_blocks.createBlock)("core/navigation-link", block.attributes)
        );
      }
    },
    [__unstableMarkNextChangeAsNotPersistent, replaceBlock]
  );
  return /* @__PURE__ */ React.createElement(React.Fragment, null, !isLoading && /* @__PURE__ */ React.createElement(
    PrivateListView,
    {
      rootClientId: listViewRootClientId,
      onSelect: offCanvasOnselect,
      blockSettingsMenu: LeafMoreMenu,
      showAppender: false,
      isExpanded: true
    }
  ), /* @__PURE__ */ React.createElement("div", { className: "navigation-edit-editor__hidden-blocks" }, /* @__PURE__ */ React.createElement(import_block_editor2.BlockList, null)));
}

// routes/navigation-edit/editor/index.tsx
var noop = () => {
};
function NavigationMenuEditor({ id }) {
  const { isReady: assetsReady } = useEditorAssets();
  const blocks = (0, import_element3.useMemo)(() => {
    if (!assetsReady || !id) {
      return [];
    }
    return [(0, import_blocks2.createBlock)("core/navigation", { ref: id })];
  }, [assetsReady, id]);
  if (!assetsReady || !blocks.length) {
    return /* @__PURE__ */ React.createElement(
      "div",
      {
        style: {
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
          height: "100vh"
        }
      },
      /* @__PURE__ */ React.createElement(import_components5.Spinner, null)
    );
  }
  return /* @__PURE__ */ React.createElement(
    import_block_editor3.BlockEditorProvider,
    {
      settings: {},
      value: blocks,
      onChange: noop,
      onInput: noop
    },
    /* @__PURE__ */ React.createElement(NavigationMenuContent, { rootClientId: blocks[0].clientId })
  );
}

// routes/navigation-edit/stage.tsx
var NAVIGATION_POST_TYPE = "wp_navigation";
function NavigationEditStage() {
  const { id } = useParams({ from: "/navigation/edit/$id" });
  const navigationId = parseInt(id);
  const { navigationMenu } = (0, import_data3.useSelect)(
    (select) => {
      const { getEntityRecord } = select(import_core_data2.store);
      return {
        navigationMenu: getEntityRecord(
          "postType",
          NAVIGATION_POST_TYPE,
          navigationId
        )
      };
    },
    [navigationId]
  );
  if (!navigationMenu) {
    return;
  }
  const menuTitle = navigationMenu.title?.rendered || navigationMenu.title?.raw || "";
  return /* @__PURE__ */ React.createElement(
    page_default,
    {
      breadcrumbs: /* @__PURE__ */ React.createElement(
        breadcrumbs_default,
        {
          items: [
            {
              label: (0, import_i18n3.__)("Navigation"),
              to: "/navigation/list"
            },
            {
              label: (0, import_html_entities.decodeEntities)(menuTitle)
            }
          ]
        }
      ),
      hasPadding: true
    },
    /* @__PURE__ */ React.createElement(NavigationMenuEditor, { id: navigationId })
  );
}
var stage = NavigationEditStage;
export {
  stage
};
