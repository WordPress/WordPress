var __create = Object.create;
var __defProp = Object.defineProperty;
var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
var __getOwnPropNames = Object.getOwnPropertyNames;
var __getProtoOf = Object.getPrototypeOf;
var __hasOwnProp = Object.prototype.hasOwnProperty;
var __commonJS = (cb, mod) => function __require() {
  return mod || (0, cb[__getOwnPropNames(cb)[0]])((mod = { exports: {} }).exports, mod), mod.exports;
};
var __export = (target, all) => {
  for (var name in all)
    __defProp(target, name, { get: all[name], enumerable: true });
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

// package-external:@wordpress/core-data
var require_core_data = __commonJS({
  "package-external:@wordpress/core-data"(exports, module) {
    module.exports = window.wp.coreData;
  }
});

// package-external:@wordpress/commands
var require_commands = __commonJS({
  "package-external:@wordpress/commands"(exports, module) {
    module.exports = window.wp.commands;
  }
});

// package-external:@wordpress/editor
var require_editor = __commonJS({
  "package-external:@wordpress/editor"(exports, module) {
    module.exports = window.wp.editor;
  }
});

// package-external:@wordpress/compose
var require_compose = __commonJS({
  "package-external:@wordpress/compose"(exports, module) {
    module.exports = window.wp.compose;
  }
});

// package-external:@wordpress/primitives
var require_primitives = __commonJS({
  "package-external:@wordpress/primitives"(exports, module) {
    module.exports = window.wp.primitives;
  }
});

// package-external:@wordpress/html-entities
var require_html_entities = __commonJS({
  "package-external:@wordpress/html-entities"(exports, module) {
    module.exports = window.wp.htmlEntities;
  }
});

// package-external:@wordpress/keycodes
var require_keycodes = __commonJS({
  "package-external:@wordpress/keycodes"(exports, module) {
    module.exports = window.wp.keycodes;
  }
});

// package-external:@wordpress/url
var require_url = __commonJS({
  "package-external:@wordpress/url"(exports, module) {
    module.exports = window.wp.url;
  }
});

// package-external:@wordpress/private-apis
var require_private_apis = __commonJS({
  "package-external:@wordpress/private-apis"(exports, module) {
    module.exports = window.wp.privateApis;
  }
});

// package-external:@wordpress/keyboard-shortcuts
var require_keyboard_shortcuts = __commonJS({
  "package-external:@wordpress/keyboard-shortcuts"(exports, module) {
    module.exports = window.wp.keyboardShortcuts;
  }
});

// package-external:@wordpress/theme
var require_theme = __commonJS({
  "package-external:@wordpress/theme"(exports, module) {
    module.exports = window.wp.theme;
  }
});

// packages/boot/build-module/components/app/index.js
var import_element15 = __toESM(require_element());
var import_data11 = __toESM(require_data());

// packages/boot/build-module/components/app/router.js
var import_i18n11 = __toESM(require_i18n());
var import_element14 = __toESM(require_element());

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
var import_jsx_runtime = __toESM(require_jsx_runtime());
var NavigableRegion = (0, import_element.forwardRef)(
  ({ children, className, ariaLabel, as: Tag = "div", ...props }, ref) => {
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
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
var import_components2 = __toESM(require_components());

// packages/admin-ui/build-module/page/sidebar-toggle-slot.js
var import_components = __toESM(require_components());
var { Fill: SidebarToggleFill, Slot: SidebarToggleSlot } = (0, import_components.createSlotFill)("SidebarToggle");

// packages/admin-ui/build-module/page/header.js
var import_jsx_runtime2 = __toESM(require_jsx_runtime());
function Header({
  breadcrumbs,
  badges,
  title,
  subTitle,
  actions,
  showSidebarToggle = true
}) {
  return /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(import_components2.__experimentalVStack, { className: "admin-ui-page__header", as: "header", children: [
    /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(import_components2.__experimentalHStack, { justify: "space-between", spacing: 2, children: [
      /* @__PURE__ */ (0, import_jsx_runtime2.jsxs)(import_components2.__experimentalHStack, { spacing: 2, justify: "left", children: [
        showSidebarToggle && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
          SidebarToggleSlot,
          {
            bubblesVirtually: true,
            className: "admin-ui-page__sidebar-toggle-slot"
          }
        ),
        title && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_components2.__experimentalHeading, { as: "h2", level: 3, weight: 500, truncate: true, children: title }),
        breadcrumbs,
        badges
      ] }),
      /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
        import_components2.__experimentalHStack,
        {
          style: { width: "auto", flexShrink: 0 },
          spacing: 2,
          className: "admin-ui-page__header-actions",
          children: actions
        }
      )
    ] }),
    subTitle && /* @__PURE__ */ (0, import_jsx_runtime2.jsx)("p", { className: "admin-ui-page__header-subtitle", children: subTitle })
  ] });
}

// packages/admin-ui/build-module/page/index.js
var import_jsx_runtime3 = __toESM(require_jsx_runtime());
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
  return /* @__PURE__ */ (0, import_jsx_runtime3.jsxs)(navigable_region_default, { className: classes, ariaLabel: title, children: [
    (title || breadcrumbs || badges) && /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(
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
    hasPadding ? /* @__PURE__ */ (0, import_jsx_runtime3.jsx)("div", { className: "admin-ui-page__content has-padding", children }) : children
  ] });
}
Page.SidebarToggleFill = SidebarToggleFill;
var page_default = Page;

// packages/boot/build-module/components/app/router.js
var import_data10 = __toESM(require_data());
var import_core_data6 = __toESM(require_core_data());
import {
  privateApis as routePrivateApis6
} from "@wordpress/route";

// packages/boot/build-module/components/root/index.js
var import_commands2 = __toESM(require_commands());
var import_editor4 = __toESM(require_editor());
var import_compose4 = __toESM(require_compose());
var import_components14 = __toESM(require_components());
import { privateApis as routePrivateApis5 } from "@wordpress/route";

// packages/icons/build-module/icon/index.js
var import_element2 = __toESM(require_element());
var icon_default = (0, import_element2.forwardRef)(
  ({ icon, size = 24, ...props }, ref) => {
    return (0, import_element2.cloneElement)(icon, {
      width: size,
      height: size,
      ...props,
      ref
    });
  }
);

// packages/icons/build-module/library/arrow-up-left.js
var import_primitives = __toESM(require_primitives());
var import_jsx_runtime4 = __toESM(require_jsx_runtime());
var arrow_up_left_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives.Path, { d: "M14 6H6v8h1.5V8.5L17 18l1-1-9.5-9.5H14V6Z" }) });

// packages/icons/build-module/library/check.js
var import_primitives2 = __toESM(require_primitives());
var import_jsx_runtime5 = __toESM(require_jsx_runtime());
var check_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives2.Path, { d: "M16.5 7.5 10 13.9l-2.5-2.4-1 1 3.5 3.6 7.5-7.6z" }) });

// packages/icons/build-module/library/chevron-down-small.js
var import_primitives3 = __toESM(require_primitives());
var import_jsx_runtime6 = __toESM(require_jsx_runtime());
var chevron_down_small_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives3.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives3.Path, { d: "m15.99 10.889-3.988 3.418-3.988-3.418.976-1.14 3.012 2.582 3.012-2.581.976 1.139Z" }) });

// packages/icons/build-module/library/chevron-left-small.js
var import_primitives4 = __toESM(require_primitives());
var import_jsx_runtime7 = __toESM(require_jsx_runtime());
var chevron_left_small_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives4.Path, { d: "m13.1 16-3.4-4 3.4-4 1.1 1-2.6 3 2.6 3-1.1 1z" }) });

// packages/icons/build-module/library/chevron-left.js
var import_primitives5 = __toESM(require_primitives());
var import_jsx_runtime8 = __toESM(require_jsx_runtime());
var chevron_left_default = /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives5.Path, { d: "M14.6 7l-1.2-1L8 12l5.4 6 1.2-1-4.6-5z" }) });

// packages/icons/build-module/library/chevron-right-small.js
var import_primitives6 = __toESM(require_primitives());
var import_jsx_runtime9 = __toESM(require_jsx_runtime());
var chevron_right_small_default = /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives6.Path, { d: "M10.8622 8.04053L14.2805 12.0286L10.8622 16.0167L9.72327 15.0405L12.3049 12.0286L9.72327 9.01672L10.8622 8.04053Z" }) });

// packages/icons/build-module/library/chevron-right.js
var import_primitives7 = __toESM(require_primitives());
var import_jsx_runtime10 = __toESM(require_jsx_runtime());
var chevron_right_default = /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives7.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives7.Path, { d: "M10.6 6L9.4 7l4.6 5-4.6 5 1.2 1 5.4-6z" }) });

// packages/icons/build-module/library/menu.js
var import_primitives8 = __toESM(require_primitives());
var import_jsx_runtime11 = __toESM(require_jsx_runtime());
var menu_default = /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives8.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives8.Path, { d: "M5 5v1.5h14V5H5zm0 7.8h14v-1.5H5v1.5zM5 19h14v-1.5H5V19z" }) });

// packages/icons/build-module/library/search.js
var import_primitives9 = __toESM(require_primitives());
var import_jsx_runtime12 = __toESM(require_jsx_runtime());
var search_default = /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives9.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives9.Path, { d: "M13 5c-3.3 0-6 2.7-6 6 0 1.4.5 2.7 1.3 3.7l-3.8 3.8 1.1 1.1 3.8-3.8c1 .8 2.3 1.3 3.7 1.3 3.3 0 6-2.7 6-6S16.3 5 13 5zm0 10.5c-2.5 0-4.5-2-4.5-4.5s2-4.5 4.5-4.5 4.5 2 4.5 4.5-2 4.5-4.5 4.5z" }) });

// packages/icons/build-module/library/wordpress.js
var import_primitives10 = __toESM(require_primitives());
var import_jsx_runtime13 = __toESM(require_jsx_runtime());
var wordpress_default = /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives10.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "-2 -2 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives10.Path, { d: "M20 10c0-5.51-4.49-10-10-10C4.48 0 0 4.49 0 10c0 5.52 4.48 10 10 10 5.51 0 10-4.48 10-10zM7.78 15.37L4.37 6.22c.55-.02 1.17-.08 1.17-.08.5-.06.44-1.13-.06-1.11 0 0-1.45.11-2.37.11-.18 0-.37 0-.58-.01C4.12 2.69 6.87 1.11 10 1.11c2.33 0 4.45.87 6.05 2.34-.68-.11-1.65.39-1.65 1.58 0 .74.45 1.36.9 2.1.35.61.55 1.36.55 2.46 0 1.49-1.4 5-1.4 5l-3.03-8.37c.54-.02.82-.17.82-.17.5-.05.44-1.25-.06-1.22 0 0-1.44.12-2.38.12-.87 0-2.33-.12-2.33-.12-.5-.03-.56 1.2-.06 1.22l.92.08 1.26 3.41zM17.41 10c.24-.64.74-1.87.43-4.25.7 1.29 1.05 2.71 1.05 4.25 0 3.29-1.73 6.24-4.4 7.78.97-2.59 1.94-5.2 2.92-7.78zM6.1 18.09C3.12 16.65 1.11 13.53 1.11 10c0-1.3.23-2.48.72-3.59C3.25 10.3 4.67 14.2 6.1 18.09zm4.03-6.63l2.58 6.98c-.86.29-1.76.45-2.71.45-.79 0-1.57-.11-2.29-.33.81-2.38 1.62-4.74 2.42-7.1z" }) });

// packages/boot/build-module/components/root/index.js
var import_element13 = __toESM(require_element());
var import_i18n10 = __toESM(require_i18n());

// packages/boot/build-module/components/site-hub/index.js
var import_data2 = __toESM(require_data());
var import_components3 = __toESM(require_components());
var import_i18n2 = __toESM(require_i18n());
var import_core_data2 = __toESM(require_core_data());
var import_html_entities = __toESM(require_html_entities());
var import_keycodes = __toESM(require_keycodes());
var import_commands = __toESM(require_commands());
var import_url = __toESM(require_url());

// packages/boot/build-module/components/site-icon-link/index.js
import { Link, privateApis as routePrivateApis } from "@wordpress/route";

// packages/boot/build-module/lock-unlock.js
var import_private_apis = __toESM(require_private_apis());
var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/boot"
);

// packages/boot/build-module/components/site-icon/index.js
var import_data = __toESM(require_data());
var import_i18n = __toESM(require_i18n());
var import_core_data = __toESM(require_core_data());
var import_jsx_runtime14 = __toESM(require_jsx_runtime());
var css = `/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
.boot-site-icon {
  display: flex;
}

.boot-site-icon__icon {
  width: 32px;
  height: 32px;
  color: var(--wpds-color-fg-content-neutral, #1e1e1e);
}

.boot-site-icon__image {
  width: 32px;
  height: 32px;
  object-fit: cover;
  aspect-ratio: 1/1;
  border-radius: var(--wpds-border-radius-surface-md, 4px);
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy9ib290L3NyYy9jb21wb25lbnRzL3NpdGUtaWNvbiIsInNvdXJjZXMiOlsiLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX3ZhcmlhYmxlcy5zY3NzIiwiLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX2NvbG9ycy5zY3NzIiwic3R5bGUuc2NzcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQ0FBO0FBQUE7QUFBQTtBRFVBO0FBQUE7QUFBQTtBQU9BO0FBQUE7QUFBQTtBQTZCQTtBQUFBO0FBQUE7QUFBQTtBQWlCQTtBQUFBO0FBQUE7QUFXQTtBQUFBO0FBQUE7QUFnQkE7QUFBQTtBQUFBO0FBeUJBO0FBQUE7QUFBQTtBQUtBO0FBQUE7QUFBQTtBQWVBO0FBQUE7QUFBQTtBQW1CQTtBQUFBO0FBQUE7QUFTQTtBQUFBO0FBQUE7QUFBQTtBRWpLQTtFQUNDOzs7QUFHRDtFQUNDLE9Ga0RjO0VFakRkLFFGaURjO0VFaERkOzs7QUFHRDtFQUNDLE9GNENjO0VFM0NkLFFGMkNjO0VFMUNkO0VBQ0E7RUFDQSIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogU0NTUyBWYXJpYWJsZXMuXG4gKlxuICogUGxlYXNlIHVzZSB2YXJpYWJsZXMgZnJvbSB0aGlzIHNoZWV0IHRvIGVuc3VyZSBjb25zaXN0ZW5jeSBhY3Jvc3MgdGhlIFVJLlxuICogRG9uJ3QgYWRkIHRvIHRoaXMgc2hlZXQgdW5sZXNzIHlvdSdyZSBwcmV0dHkgc3VyZSB0aGUgdmFsdWUgd2lsbCBiZSByZXVzZWQgaW4gbWFueSBwbGFjZXMuXG4gKiBGb3IgZXhhbXBsZSwgZG9uJ3QgYWRkIHJ1bGVzIHRvIHRoaXMgc2hlZXQgdGhhdCBhZmZlY3QgYmxvY2sgdmlzdWFscy4gSXQncyBwdXJlbHkgZm9yIFVJLlxuICovXG5cbkB1c2UgXCIuL2NvbG9yc1wiO1xuXG4vKipcbiAqIEZvbnRzICYgYmFzaWMgdmFyaWFibGVzLlxuICovXG5cbiRkZWZhdWx0LWZvbnQ6IC1hcHBsZS1zeXN0ZW0sIEJsaW5rTWFjU3lzdGVtRm9udCxcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLFwiSGVsdmV0aWNhIE5ldWVcIiwgc2Fucy1zZXJpZjsgLy8gVG9kbzogZGVwcmVjYXRlIGluIGZhdm9yIG9mICRmYW1pbHkgdmFyaWFibGVzXG4kZGVmYXVsdC1saW5lLWhlaWdodDogMS40OyAvLyBUb2RvOiBkZXByZWNhdGUgaW4gZmF2b3Igb2YgJGxpbmUtaGVpZ2h0IHRva2Vuc1xuXG4vKipcbiAqIFR5cG9ncmFwaHlcbiAqL1xuXG4vLyBTaXplc1xuJGZvbnQtc2l6ZS14LXNtYWxsOiAxMXB4O1xuJGZvbnQtc2l6ZS1zbWFsbDogMTJweDtcbiRmb250LXNpemUtbWVkaXVtOiAxM3B4O1xuJGZvbnQtc2l6ZS1sYXJnZTogMTVweDtcbiRmb250LXNpemUteC1sYXJnZTogMjBweDtcbiRmb250LXNpemUtMngtbGFyZ2U6IDMycHg7XG5cbi8vIExpbmUgaGVpZ2h0c1xuJGZvbnQtbGluZS1oZWlnaHQteC1zbWFsbDogMTZweDtcbiRmb250LWxpbmUtaGVpZ2h0LXNtYWxsOiAyMHB4O1xuJGZvbnQtbGluZS1oZWlnaHQtbWVkaXVtOiAyNHB4O1xuJGZvbnQtbGluZS1oZWlnaHQtbGFyZ2U6IDI4cHg7XG4kZm9udC1saW5lLWhlaWdodC14LWxhcmdlOiAzMnB4O1xuJGZvbnQtbGluZS1oZWlnaHQtMngtbGFyZ2U6IDQwcHg7XG5cbi8vIFdlaWdodHNcbiRmb250LXdlaWdodC1yZWd1bGFyOiA0MDA7XG4kZm9udC13ZWlnaHQtbWVkaXVtOiA0OTk7IC8vIGVuc3VyZXMgZmFsbGJhY2sgdG8gNDAwIChpbnN0ZWFkIG9mIDYwMClcblxuLy8gRmFtaWxpZXNcbiRmb250LWZhbWlseS1oZWFkaW5nczogLWFwcGxlLXN5c3RlbSwgXCJzeXN0ZW0tdWlcIiwgXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCwgXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmO1xuJGZvbnQtZmFtaWx5LWJvZHk6IC1hcHBsZS1zeXN0ZW0sIFwic3lzdGVtLXVpXCIsIFwiU2Vnb2UgVUlcIiwgUm9ib3RvLCBPeHlnZW4tU2FucywgVWJ1bnR1LCBDYW50YXJlbGwsIFwiSGVsdmV0aWNhIE5ldWVcIiwgc2Fucy1zZXJpZjtcbiRmb250LWZhbWlseS1tb25vOiBNZW5sbywgQ29uc29sYXMsIG1vbmFjbywgbW9ub3NwYWNlO1xuXG4vKipcbiAqIEdyaWQgU3lzdGVtLlxuICogaHR0cHM6Ly9tYWtlLndvcmRwcmVzcy5vcmcvZGVzaWduLzIwMTkvMTAvMzEvcHJvcG9zYWwtYS1jb25zaXN0ZW50LXNwYWNpbmctc3lzdGVtLWZvci13b3JkcHJlc3MvXG4gKi9cblxuJGdyaWQtdW5pdDogOHB4O1xuJGdyaWQtdW5pdC0wNTogMC41ICogJGdyaWQtdW5pdDtcdC8vIDRweFxuJGdyaWQtdW5pdC0xMDogMSAqICRncmlkLXVuaXQ7XHRcdC8vIDhweFxuJGdyaWQtdW5pdC0xNTogMS41ICogJGdyaWQtdW5pdDtcdC8vIDEycHhcbiRncmlkLXVuaXQtMjA6IDIgKiAkZ3JpZC11bml0O1x0XHQvLyAxNnB4XG4kZ3JpZC11bml0LTMwOiAzICogJGdyaWQtdW5pdDtcdFx0Ly8gMjRweFxuJGdyaWQtdW5pdC00MDogNCAqICRncmlkLXVuaXQ7XHRcdC8vIDMycHhcbiRncmlkLXVuaXQtNTA6IDUgKiAkZ3JpZC11bml0O1x0XHQvLyA0MHB4XG4kZ3JpZC11bml0LTYwOiA2ICogJGdyaWQtdW5pdDtcdFx0Ly8gNDhweFxuJGdyaWQtdW5pdC03MDogNyAqICRncmlkLXVuaXQ7XHRcdC8vIDU2cHhcbiRncmlkLXVuaXQtODA6IDggKiAkZ3JpZC11bml0O1x0XHQvLyA2NHB4XG5cbi8qKlxuICogUmFkaXVzIHNjYWxlLlxuICovXG5cbiRyYWRpdXMteC1zbWFsbDogMXB4OyAgIC8vIEFwcGxpZWQgdG8gZWxlbWVudHMgbGlrZSBidXR0b25zIG5lc3RlZCB3aXRoaW4gcHJpbWl0aXZlcyBsaWtlIGlucHV0cy5cbiRyYWRpdXMtc21hbGw6IDJweDsgICAgIC8vIEFwcGxpZWQgdG8gbW9zdCBwcmltaXRpdmVzLlxuJHJhZGl1cy1tZWRpdW06IDRweDsgICAgLy8gQXBwbGllZCB0byBjb250YWluZXJzIHdpdGggc21hbGxlciBwYWRkaW5nLlxuJHJhZGl1cy1sYXJnZTogOHB4OyAgICAgLy8gQXBwbGllZCB0byBjb250YWluZXJzIHdpdGggbGFyZ2VyIHBhZGRpbmcuXG4kcmFkaXVzLWZ1bGw6IDk5OTlweDsgICAvLyBGb3IgcGlsbHMuXG4kcmFkaXVzLXJvdW5kOiA1MCU7ICAgICAvLyBGb3IgY2lyY2xlcyBhbmQgb3ZhbHMuXG5cbi8qKlxuICogRWxldmF0aW9uIHNjYWxlLlxuICovXG5cbi8vIEZvciBzZWN0aW9ucyBhbmQgY29udGFpbmVycyB0aGF0IGdyb3VwIHJlbGF0ZWQgY29udGVudCBhbmQgY29udHJvbHMsIHdoaWNoIG1heSBvdmVybGFwIG90aGVyIGNvbnRlbnQuIEV4YW1wbGU6IFByZXZpZXcgRnJhbWUuXG4kZWxldmF0aW9uLXgtc21hbGw6IDAgMXB4IDFweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDMpLCAwIDFweCAycHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKSwgMCAzcHggM3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMiksIDAgNHB4IDRweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDEpO1xuXG4vLyBGb3IgY29tcG9uZW50cyB0aGF0IHByb3ZpZGUgY29udGV4dHVhbCBmZWVkYmFjayB3aXRob3V0IGJlaW5nIGludHJ1c2l2ZS4gR2VuZXJhbGx5IG5vbi1pbnRlcnJ1cHRpdmUuIEV4YW1wbGU6IFRvb2x0aXBzLCBTbmFja2Jhci5cbiRlbGV2YXRpb24tc21hbGw6IDAgMXB4IDJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDUpLCAwIDJweCAzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA0KSwgMCA2cHggNnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMyksIDAgOHB4IDhweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vLyBGb3IgY29tcG9uZW50cyB0aGF0IG9mZmVyIGFkZGl0aW9uYWwgYWN0aW9ucy4gRXhhbXBsZTogTWVudXMsIENvbW1hbmQgUGFsZXR0ZVxuJGVsZXZhdGlvbi1tZWRpdW06IDAgMnB4IDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDUpLCAwIDRweCA1cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA0KSwgMCAxMnB4IDEycHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAzKSwgMCAxNnB4IDE2cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKTtcblxuLy8gRm9yIGNvbXBvbmVudHMgdGhhdCBjb25maXJtIGRlY2lzaW9ucyBvciBoYW5kbGUgbmVjZXNzYXJ5IGludGVycnVwdGlvbnMuIEV4YW1wbGU6IE1vZGFscy5cbiRlbGV2YXRpb24tbGFyZ2U6IDAgNXB4IDE1cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA4KSwgMCAxNXB4IDI3cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA3KSwgMCAzMHB4IDM2cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA0KSwgMCA1MHB4IDQzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKTtcblxuLyoqXG4gKiBEaW1lbnNpb25zLlxuICovXG5cbiRpY29uLXNpemU6IDI0cHg7XG4kYnV0dG9uLXNpemU6IDM2cHg7XG4kYnV0dG9uLXNpemUtbmV4dC1kZWZhdWx0LTQwcHg6IDQwcHg7IC8vIHRyYW5zaXRpb25hcnkgdmFyaWFibGUgZm9yIG5leHQgZGVmYXVsdCBidXR0b24gc2l6ZVxuJGJ1dHRvbi1zaXplLXNtYWxsOiAyNHB4O1xuJGJ1dHRvbi1zaXplLWNvbXBhY3Q6IDMycHg7XG4kaGVhZGVyLWhlaWdodDogNjRweDtcbiRwYW5lbC1oZWFkZXItaGVpZ2h0OiAkZ3JpZC11bml0LTYwO1xuJG5hdi1zaWRlYmFyLXdpZHRoOiAzMDBweDtcbiRhZG1pbi1iYXItaGVpZ2h0OiAzMnB4O1xuJGFkbWluLWJhci1oZWlnaHQtYmlnOiA0NnB4O1xuJGFkbWluLXNpZGViYXItd2lkdGg6IDE2MHB4O1xuJGFkbWluLXNpZGViYXItd2lkdGgtYmlnOiAxOTBweDtcbiRhZG1pbi1zaWRlYmFyLXdpZHRoLWNvbGxhcHNlZDogMzZweDtcbiRtb2RhbC1taW4td2lkdGg6IDM1MHB4O1xuJG1vZGFsLXdpZHRoLXNtYWxsOiAzODRweDtcbiRtb2RhbC13aWR0aC1tZWRpdW06IDUxMnB4O1xuJG1vZGFsLXdpZHRoLWxhcmdlOiA4NDBweDtcbiRzcGlubmVyLXNpemU6IDE2cHg7XG4kY2FudmFzLXBhZGRpbmc6ICRncmlkLXVuaXQtMjA7XG4kcGFsZXR0ZS1tYXgtaGVpZ2h0OiAzNjhweDtcblxuLyoqXG4gKiBNb2JpbGUgc3BlY2lmaWMgc3R5bGVzXG4gKi9cbiRtb2JpbGUtdGV4dC1taW4tZm9udC1zaXplOiAxNnB4OyAvLyBBbnkgZm9udCBzaXplIGJlbG93IDE2cHggd2lsbCBjYXVzZSBNb2JpbGUgU2FmYXJpIHRvIFwiem9vbSBpblwiLlxuXG4vKipcbiAqIEVkaXRvciBzdHlsZXMuXG4gKi9cblxuJHNpZGViYXItd2lkdGg6IDI4MHB4O1xuJGNvbnRlbnQtd2lkdGg6IDg0MHB4O1xuJHdpZGUtY29udGVudC13aWR0aDogMTEwMHB4O1xuJHdpZGdldC1hcmVhLXdpZHRoOiA3MDBweDtcbiRzZWNvbmRhcnktc2lkZWJhci13aWR0aDogMzUwcHg7XG4kZWRpdG9yLWZvbnQtc2l6ZTogMTZweDtcbiRkZWZhdWx0LWJsb2NrLW1hcmdpbjogMjhweDsgLy8gVGhpcyB2YWx1ZSBwcm92aWRlcyBhIGNvbnNpc3RlbnQsIGNvbnRpZ3VvdXMgc3BhY2luZyBiZXR3ZWVuIGJsb2Nrcy5cbiR0ZXh0LWVkaXRvci1mb250LXNpemU6IDE1cHg7XG4kZWRpdG9yLWxpbmUtaGVpZ2h0OiAxLjg7XG4kZWRpdG9yLWh0bWwtZm9udDogJGZvbnQtZmFtaWx5LW1vbm87XG5cbi8qKlxuICogQmxvY2sgJiBFZGl0b3IgVUkuXG4gKi9cblxuJGJsb2NrLXRvb2xiYXItaGVpZ2h0OiAkZ3JpZC11bml0LTYwO1xuJGJvcmRlci13aWR0aDogMXB4O1xuJGJvcmRlci13aWR0aC1mb2N1cy1mYWxsYmFjazogMnB4OyAvLyBUaGlzIGV4aXN0cyBhcyBhIGZhbGxiYWNrLCBhbmQgaXMgaWRlYWxseSBvdmVycmlkZGVuIGJ5IHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgdW5sZXNzIGluIHNvbWUgU0FTUyBtYXRoIGNhc2VzLlxuJGJvcmRlci13aWR0aC10YWI6IDEuNXB4O1xuJGhlbHB0ZXh0LWZvbnQtc2l6ZTogMTJweDtcbiRyYWRpby1pbnB1dC1zaXplOiAxNnB4O1xuJHJhZGlvLWlucHV0LXNpemUtc206IDI0cHg7IC8vIFdpZHRoICYgaGVpZ2h0IGZvciBzbWFsbCB2aWV3cG9ydHMuXG5cbi8vIERlcHJlY2F0ZWQsIHBsZWFzZSBhdm9pZCB1c2luZyB0aGVzZS5cbiRibG9jay1wYWRkaW5nOiAxNHB4OyAvLyBVc2VkIHRvIGRlZmluZSBzcGFjZSBiZXR3ZWVuIGJsb2NrIGZvb3RwcmludCBhbmQgc3Vycm91bmRpbmcgYm9yZGVycy5cbiRyYWRpdXMtYmxvY2stdWk6ICRyYWRpdXMtc21hbGw7XG4kc2hhZG93LXBvcG92ZXI6ICRlbGV2YXRpb24teC1zbWFsbDtcbiRzaGFkb3ctbW9kYWw6ICRlbGV2YXRpb24tbGFyZ2U7XG4kZGVmYXVsdC1mb250LXNpemU6ICRmb250LXNpemUtbWVkaXVtO1xuXG4vKipcbiAqIEJsb2NrIHBhZGRpbmdzLlxuICovXG5cbi8vIFBhZGRpbmcgZm9yIGJsb2NrcyB3aXRoIGEgYmFja2dyb3VuZCBjb2xvciAoZS5nLiBwYXJhZ3JhcGggb3IgZ3JvdXApLlxuJGJsb2NrLWJnLXBhZGRpbmctLXY6IDEuMjVlbTtcbiRibG9jay1iZy1wYWRkaW5nLS1oOiAyLjM3NWVtO1xuXG5cbi8qKlxuICogUmVhY3QgTmF0aXZlIHNwZWNpZmljLlxuICogVGhlc2UgdmFyaWFibGVzIGRvIG5vdCBhcHBlYXIgdG8gYmUgdXNlZCBhbnl3aGVyZSBlbHNlLlxuICovXG5cbi8vIERpbWVuc2lvbnMuXG4kbW9iaWxlLWhlYWRlci10b29sYmFyLWhlaWdodDogNDRweDtcbiRtb2JpbGUtaGVhZGVyLXRvb2xiYXItZXhwYW5kZWQtaGVpZ2h0OiA1MnB4O1xuJG1vYmlsZS1mbG9hdGluZy10b29sYmFyLWhlaWdodDogNDRweDtcbiRtb2JpbGUtZmxvYXRpbmctdG9vbGJhci1tYXJnaW46IDhweDtcbiRtb2JpbGUtY29sb3Itc3dhdGNoOiA0OHB4O1xuXG4vLyBCbG9jayBVSS5cbiRtb2JpbGUtYmxvY2stdG9vbGJhci1oZWlnaHQ6IDQ0cHg7XG4kZGltbWVkLW9wYWNpdHk6IDE7XG4kYmxvY2stZWRnZS10by1jb250ZW50OiAxNnB4O1xuJHNvbGlkLWJvcmRlci1zcGFjZTogMTJweDtcbiRkYXNoZWQtYm9yZGVyLXNwYWNlOiA2cHg7XG4kYmxvY2stc2VsZWN0ZWQtbWFyZ2luOiAzcHg7XG4kYmxvY2stc2VsZWN0ZWQtYm9yZGVyLXdpZHRoOiAxcHg7XG4kYmxvY2stc2VsZWN0ZWQtcGFkZGluZzogMDtcbiRibG9jay1zZWxlY3RlZC1jaGlsZC1tYXJnaW46IDVweDtcbiRibG9jay1zZWxlY3RlZC10by1jb250ZW50OiAkYmxvY2stZWRnZS10by1jb250ZW50IC0gJGJsb2NrLXNlbGVjdGVkLW1hcmdpbiAtICRibG9jay1zZWxlY3RlZC1ib3JkZXItd2lkdGg7XG4iLCIvKipcbiAqIENvbG9yc1xuICovXG5cbi8vIFdvcmRQcmVzcyBncmF5cy5cbiRibGFjazogIzAwMDtcdFx0XHQvLyBVc2Ugb25seSB3aGVuIHlvdSB0cnVseSBuZWVkIHB1cmUgYmxhY2suIEZvciBVSSwgdXNlICRncmF5LTkwMC5cbiRncmF5LTkwMDogIzFlMWUxZTtcbiRncmF5LTgwMDogIzJmMmYyZjtcbiRncmF5LTcwMDogIzc1NzU3NTtcdFx0Ly8gTWVldHMgNC42OjEgKDQuNToxIGlzIG1pbmltdW0pIHRleHQgY29udHJhc3QgYWdhaW5zdCB3aGl0ZS5cbiRncmF5LTYwMDogIzk0OTQ5NDtcdFx0Ly8gTWVldHMgMzoxIFVJIG9yIGxhcmdlIHRleHQgY29udHJhc3QgYWdhaW5zdCB3aGl0ZS5cbiRncmF5LTQwMDogI2NjYztcbiRncmF5LTMwMDogI2RkZDtcdFx0Ly8gVXNlZCBmb3IgbW9zdCBib3JkZXJzLlxuJGdyYXktMjAwOiAjZTBlMGUwO1x0XHQvLyBVc2VkIHNwYXJpbmdseSBmb3IgbGlnaHQgYm9yZGVycy5cbiRncmF5LTEwMDogI2YwZjBmMDtcdFx0Ly8gVXNlZCBmb3IgbGlnaHQgZ3JheSBiYWNrZ3JvdW5kcy5cbiR3aGl0ZTogI2ZmZjtcblxuLy8gT3BhY2l0aWVzICYgYWRkaXRpb25hbCBjb2xvcnMuXG4kZGFyay1ncmF5LXBsYWNlaG9sZGVyOiByZ2JhKCRncmF5LTkwMCwgMC42Mik7XG4kbWVkaXVtLWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJGdyYXktOTAwLCAwLjU1KTtcbiRsaWdodC1ncmF5LXBsYWNlaG9sZGVyOiByZ2JhKCR3aGl0ZSwgMC42NSk7XG5cbi8vIEFsZXJ0IGNvbG9ycy5cbiRhbGVydC15ZWxsb3c6ICNmMGI4NDk7XG4kYWxlcnQtcmVkOiAjY2MxODE4O1xuJGFsZXJ0LWdyZWVuOiAjNGFiODY2O1xuXG4vLyBEZXByZWNhdGVkLCBwbGVhc2UgYXZvaWQgdXNpbmcgdGhlc2UuXG4kZGFyay10aGVtZS1mb2N1czogJHdoaXRlO1x0Ly8gRm9jdXMgY29sb3Igd2hlbiB0aGUgdGhlbWUgaXMgZGFyay5cbiIsIkB1c2UgXCJAd29yZHByZXNzL2Jhc2Utc3R5bGVzL3ZhcmlhYmxlc1wiO1xuXG4uYm9vdC1zaXRlLWljb24ge1xuXHRkaXNwbGF5OiBmbGV4O1xufVxuXG4uYm9vdC1zaXRlLWljb25fX2ljb24ge1xuXHR3aWR0aDogdmFyaWFibGVzLiRncmlkLXVuaXQtNDA7XG5cdGhlaWdodDogdmFyaWFibGVzLiRncmlkLXVuaXQtNDA7XG5cdGNvbG9yOiB2YXIoLS13cGRzLWNvbG9yLWZnLWNvbnRlbnQtbmV1dHJhbCwgIzFlMWUxZSk7XG59XG5cbi5ib290LXNpdGUtaWNvbl9faW1hZ2Uge1xuXHR3aWR0aDogdmFyaWFibGVzLiRncmlkLXVuaXQtNDA7XG5cdGhlaWdodDogdmFyaWFibGVzLiRncmlkLXVuaXQtNDA7XG5cdG9iamVjdC1maXQ6IGNvdmVyO1xuXHRhc3BlY3QtcmF0aW86IDEgLyAxO1xuXHRib3JkZXItcmFkaXVzOiB2YXIoLS13cGRzLWJvcmRlci1yYWRpdXMtc3VyZmFjZS1tZCwgNHB4KTtcbn1cbiJdfQ== */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css));
function SiteIcon({ className }) {
  const { isRequestingSite, siteIconUrl } = (0, import_data.useSelect)((select) => {
    const { getEntityRecord } = select(import_core_data.store);
    const siteData = getEntityRecord(
      "root",
      "__unstableBase",
      void 0
    );
    return {
      isRequestingSite: !siteData,
      siteIconUrl: siteData?.site_icon_url
    };
  }, []);
  let icon = null;
  if (isRequestingSite && !siteIconUrl) {
    icon = /* @__PURE__ */ (0, import_jsx_runtime14.jsx)("div", { className: "boot-site-icon__image" });
  } else {
    icon = siteIconUrl ? /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
      "img",
      {
        className: "boot-site-icon__image",
        alt: (0, import_i18n.__)("Site Icon"),
        src: siteIconUrl
      }
    ) : /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
      icon_default,
      {
        className: "boot-site-icon__icon",
        icon: wordpress_default,
        size: 48
      }
    );
  }
  return /* @__PURE__ */ (0, import_jsx_runtime14.jsx)("div", { className: clsx_default(className, "boot-site-icon"), children: icon });
}
var site_icon_default = SiteIcon;

// packages/boot/build-module/components/site-icon-link/index.js
var import_jsx_runtime15 = __toESM(require_jsx_runtime());
var css2 = `/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
.boot-site-icon-link {
  width: 64px;
  height: 64px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--wpds-color-bg-surface-neutral-weak, #f0f0f0);
  text-decoration: none;
}
@media not (prefers-reduced-motion) {
  .boot-site-icon-link {
    transition: outline 0.1s ease-out;
  }
}
.boot-site-icon-link:focus:not(:active) {
  outline: var(--wpds-border-width-interactive-focus, 2px) solid var(--wpds-color-stroke-focus-brand, #0073aa);
  outline-offset: calc(-1 * var(--wpds-border-width-interactive-focus, 2px));
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy9ib290L3NyYy9jb21wb25lbnRzL3NpdGUtaWNvbi1saW5rIiwic291cmNlcyI6WyIuLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9iYXNlLXN0eWxlcy9fdmFyaWFibGVzLnNjc3MiLCIuLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9iYXNlLXN0eWxlcy9fY29sb3JzLnNjc3MiLCJzdHlsZS5zY3NzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FDQUE7QUFBQTtBQUFBO0FEVUE7QUFBQTtBQUFBO0FBT0E7QUFBQTtBQUFBO0FBNkJBO0FBQUE7QUFBQTtBQUFBO0FBaUJBO0FBQUE7QUFBQTtBQVdBO0FBQUE7QUFBQTtBQWdCQTtBQUFBO0FBQUE7QUF5QkE7QUFBQTtBQUFBO0FBS0E7QUFBQTtBQUFBO0FBZUE7QUFBQTtBQUFBO0FBbUJBO0FBQUE7QUFBQTtBQVNBO0FBQUE7QUFBQTtBQUFBO0FFL0pBO0VBQ0MsT0Y4RmU7RUU3RmYsUUY2RmU7RUU1RmY7RUFDQTtFQUNBO0VBQ0E7RUFDQTs7QUFFQTtFQVREO0lBVUU7OztBQUdEO0VBQ0MsU0FDQztFQUVEIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBTQ1NTIFZhcmlhYmxlcy5cbiAqXG4gKiBQbGVhc2UgdXNlIHZhcmlhYmxlcyBmcm9tIHRoaXMgc2hlZXQgdG8gZW5zdXJlIGNvbnNpc3RlbmN5IGFjcm9zcyB0aGUgVUkuXG4gKiBEb24ndCBhZGQgdG8gdGhpcyBzaGVldCB1bmxlc3MgeW91J3JlIHByZXR0eSBzdXJlIHRoZSB2YWx1ZSB3aWxsIGJlIHJldXNlZCBpbiBtYW55IHBsYWNlcy5cbiAqIEZvciBleGFtcGxlLCBkb24ndCBhZGQgcnVsZXMgdG8gdGhpcyBzaGVldCB0aGF0IGFmZmVjdCBibG9jayB2aXN1YWxzLiBJdCdzIHB1cmVseSBmb3IgVUkuXG4gKi9cblxuQHVzZSBcIi4vY29sb3JzXCI7XG5cbi8qKlxuICogRm9udHMgJiBiYXNpYyB2YXJpYWJsZXMuXG4gKi9cblxuJGRlZmF1bHQtZm9udDogLWFwcGxlLXN5c3RlbSwgQmxpbmtNYWNTeXN0ZW1Gb250LFwiU2Vnb2UgVUlcIiwgUm9ib3RvLCBPeHlnZW4tU2FucywgVWJ1bnR1LCBDYW50YXJlbGwsXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmOyAvLyBUb2RvOiBkZXByZWNhdGUgaW4gZmF2b3Igb2YgJGZhbWlseSB2YXJpYWJsZXNcbiRkZWZhdWx0LWxpbmUtaGVpZ2h0OiAxLjQ7IC8vIFRvZG86IGRlcHJlY2F0ZSBpbiBmYXZvciBvZiAkbGluZS1oZWlnaHQgdG9rZW5zXG5cbi8qKlxuICogVHlwb2dyYXBoeVxuICovXG5cbi8vIFNpemVzXG4kZm9udC1zaXplLXgtc21hbGw6IDExcHg7XG4kZm9udC1zaXplLXNtYWxsOiAxMnB4O1xuJGZvbnQtc2l6ZS1tZWRpdW06IDEzcHg7XG4kZm9udC1zaXplLWxhcmdlOiAxNXB4O1xuJGZvbnQtc2l6ZS14LWxhcmdlOiAyMHB4O1xuJGZvbnQtc2l6ZS0yeC1sYXJnZTogMzJweDtcblxuLy8gTGluZSBoZWlnaHRzXG4kZm9udC1saW5lLWhlaWdodC14LXNtYWxsOiAxNnB4O1xuJGZvbnQtbGluZS1oZWlnaHQtc21hbGw6IDIwcHg7XG4kZm9udC1saW5lLWhlaWdodC1tZWRpdW06IDI0cHg7XG4kZm9udC1saW5lLWhlaWdodC1sYXJnZTogMjhweDtcbiRmb250LWxpbmUtaGVpZ2h0LXgtbGFyZ2U6IDMycHg7XG4kZm9udC1saW5lLWhlaWdodC0yeC1sYXJnZTogNDBweDtcblxuLy8gV2VpZ2h0c1xuJGZvbnQtd2VpZ2h0LXJlZ3VsYXI6IDQwMDtcbiRmb250LXdlaWdodC1tZWRpdW06IDQ5OTsgLy8gZW5zdXJlcyBmYWxsYmFjayB0byA0MDAgKGluc3RlYWQgb2YgNjAwKVxuXG4vLyBGYW1pbGllc1xuJGZvbnQtZmFtaWx5LWhlYWRpbmdzOiAtYXBwbGUtc3lzdGVtLCBcInN5c3RlbS11aVwiLCBcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLCBcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7XG4kZm9udC1mYW1pbHktYm9keTogLWFwcGxlLXN5c3RlbSwgXCJzeXN0ZW0tdWlcIiwgXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCwgXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmO1xuJGZvbnQtZmFtaWx5LW1vbm86IE1lbmxvLCBDb25zb2xhcywgbW9uYWNvLCBtb25vc3BhY2U7XG5cbi8qKlxuICogR3JpZCBTeXN0ZW0uXG4gKiBodHRwczovL21ha2Uud29yZHByZXNzLm9yZy9kZXNpZ24vMjAxOS8xMC8zMS9wcm9wb3NhbC1hLWNvbnNpc3RlbnQtc3BhY2luZy1zeXN0ZW0tZm9yLXdvcmRwcmVzcy9cbiAqL1xuXG4kZ3JpZC11bml0OiA4cHg7XG4kZ3JpZC11bml0LTA1OiAwLjUgKiAkZ3JpZC11bml0O1x0Ly8gNHB4XG4kZ3JpZC11bml0LTEwOiAxICogJGdyaWQtdW5pdDtcdFx0Ly8gOHB4XG4kZ3JpZC11bml0LTE1OiAxLjUgKiAkZ3JpZC11bml0O1x0Ly8gMTJweFxuJGdyaWQtdW5pdC0yMDogMiAqICRncmlkLXVuaXQ7XHRcdC8vIDE2cHhcbiRncmlkLXVuaXQtMzA6IDMgKiAkZ3JpZC11bml0O1x0XHQvLyAyNHB4XG4kZ3JpZC11bml0LTQwOiA0ICogJGdyaWQtdW5pdDtcdFx0Ly8gMzJweFxuJGdyaWQtdW5pdC01MDogNSAqICRncmlkLXVuaXQ7XHRcdC8vIDQwcHhcbiRncmlkLXVuaXQtNjA6IDYgKiAkZ3JpZC11bml0O1x0XHQvLyA0OHB4XG4kZ3JpZC11bml0LTcwOiA3ICogJGdyaWQtdW5pdDtcdFx0Ly8gNTZweFxuJGdyaWQtdW5pdC04MDogOCAqICRncmlkLXVuaXQ7XHRcdC8vIDY0cHhcblxuLyoqXG4gKiBSYWRpdXMgc2NhbGUuXG4gKi9cblxuJHJhZGl1cy14LXNtYWxsOiAxcHg7ICAgLy8gQXBwbGllZCB0byBlbGVtZW50cyBsaWtlIGJ1dHRvbnMgbmVzdGVkIHdpdGhpbiBwcmltaXRpdmVzIGxpa2UgaW5wdXRzLlxuJHJhZGl1cy1zbWFsbDogMnB4OyAgICAgLy8gQXBwbGllZCB0byBtb3N0IHByaW1pdGl2ZXMuXG4kcmFkaXVzLW1lZGl1bTogNHB4OyAgICAvLyBBcHBsaWVkIHRvIGNvbnRhaW5lcnMgd2l0aCBzbWFsbGVyIHBhZGRpbmcuXG4kcmFkaXVzLWxhcmdlOiA4cHg7ICAgICAvLyBBcHBsaWVkIHRvIGNvbnRhaW5lcnMgd2l0aCBsYXJnZXIgcGFkZGluZy5cbiRyYWRpdXMtZnVsbDogOTk5OXB4OyAgIC8vIEZvciBwaWxscy5cbiRyYWRpdXMtcm91bmQ6IDUwJTsgICAgIC8vIEZvciBjaXJjbGVzIGFuZCBvdmFscy5cblxuLyoqXG4gKiBFbGV2YXRpb24gc2NhbGUuXG4gKi9cblxuLy8gRm9yIHNlY3Rpb25zIGFuZCBjb250YWluZXJzIHRoYXQgZ3JvdXAgcmVsYXRlZCBjb250ZW50IGFuZCBjb250cm9scywgd2hpY2ggbWF5IG92ZXJsYXAgb3RoZXIgY29udGVudC4gRXhhbXBsZTogUHJldmlldyBGcmFtZS5cbiRlbGV2YXRpb24teC1zbWFsbDogMCAxcHggMXB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMyksIDAgMXB4IDJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpLCAwIDNweCAzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKSwgMCA0cHggNHB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMSk7XG5cbi8vIEZvciBjb21wb25lbnRzIHRoYXQgcHJvdmlkZSBjb250ZXh0dWFsIGZlZWRiYWNrIHdpdGhvdXQgYmVpbmcgaW50cnVzaXZlLiBHZW5lcmFsbHkgbm9uLWludGVycnVwdGl2ZS4gRXhhbXBsZTogVG9vbHRpcHMsIFNuYWNrYmFyLlxuJGVsZXZhdGlvbi1zbWFsbDogMCAxcHggMnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNSksIDAgMnB4IDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDZweCA2cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAzKSwgMCA4cHggOHB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMik7XG5cbi8vIEZvciBjb21wb25lbnRzIHRoYXQgb2ZmZXIgYWRkaXRpb25hbCBhY3Rpb25zLiBFeGFtcGxlOiBNZW51cywgQ29tbWFuZCBQYWxldHRlXG4kZWxldmF0aW9uLW1lZGl1bTogMCAycHggM3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNSksIDAgNHB4IDVweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDEycHggMTJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDMpLCAwIDE2cHggMTZweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vLyBGb3IgY29tcG9uZW50cyB0aGF0IGNvbmZpcm0gZGVjaXNpb25zIG9yIGhhbmRsZSBuZWNlc3NhcnkgaW50ZXJydXB0aW9ucy4gRXhhbXBsZTogTW9kYWxzLlxuJGVsZXZhdGlvbi1sYXJnZTogMCA1cHggMTVweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDgpLCAwIDE1cHggMjdweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDcpLCAwIDMwcHggMzZweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDUwcHggNDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vKipcbiAqIERpbWVuc2lvbnMuXG4gKi9cblxuJGljb24tc2l6ZTogMjRweDtcbiRidXR0b24tc2l6ZTogMzZweDtcbiRidXR0b24tc2l6ZS1uZXh0LWRlZmF1bHQtNDBweDogNDBweDsgLy8gdHJhbnNpdGlvbmFyeSB2YXJpYWJsZSBmb3IgbmV4dCBkZWZhdWx0IGJ1dHRvbiBzaXplXG4kYnV0dG9uLXNpemUtc21hbGw6IDI0cHg7XG4kYnV0dG9uLXNpemUtY29tcGFjdDogMzJweDtcbiRoZWFkZXItaGVpZ2h0OiA2NHB4O1xuJHBhbmVsLWhlYWRlci1oZWlnaHQ6ICRncmlkLXVuaXQtNjA7XG4kbmF2LXNpZGViYXItd2lkdGg6IDMwMHB4O1xuJGFkbWluLWJhci1oZWlnaHQ6IDMycHg7XG4kYWRtaW4tYmFyLWhlaWdodC1iaWc6IDQ2cHg7XG4kYWRtaW4tc2lkZWJhci13aWR0aDogMTYwcHg7XG4kYWRtaW4tc2lkZWJhci13aWR0aC1iaWc6IDE5MHB4O1xuJGFkbWluLXNpZGViYXItd2lkdGgtY29sbGFwc2VkOiAzNnB4O1xuJG1vZGFsLW1pbi13aWR0aDogMzUwcHg7XG4kbW9kYWwtd2lkdGgtc21hbGw6IDM4NHB4O1xuJG1vZGFsLXdpZHRoLW1lZGl1bTogNTEycHg7XG4kbW9kYWwtd2lkdGgtbGFyZ2U6IDg0MHB4O1xuJHNwaW5uZXItc2l6ZTogMTZweDtcbiRjYW52YXMtcGFkZGluZzogJGdyaWQtdW5pdC0yMDtcbiRwYWxldHRlLW1heC1oZWlnaHQ6IDM2OHB4O1xuXG4vKipcbiAqIE1vYmlsZSBzcGVjaWZpYyBzdHlsZXNcbiAqL1xuJG1vYmlsZS10ZXh0LW1pbi1mb250LXNpemU6IDE2cHg7IC8vIEFueSBmb250IHNpemUgYmVsb3cgMTZweCB3aWxsIGNhdXNlIE1vYmlsZSBTYWZhcmkgdG8gXCJ6b29tIGluXCIuXG5cbi8qKlxuICogRWRpdG9yIHN0eWxlcy5cbiAqL1xuXG4kc2lkZWJhci13aWR0aDogMjgwcHg7XG4kY29udGVudC13aWR0aDogODQwcHg7XG4kd2lkZS1jb250ZW50LXdpZHRoOiAxMTAwcHg7XG4kd2lkZ2V0LWFyZWEtd2lkdGg6IDcwMHB4O1xuJHNlY29uZGFyeS1zaWRlYmFyLXdpZHRoOiAzNTBweDtcbiRlZGl0b3ItZm9udC1zaXplOiAxNnB4O1xuJGRlZmF1bHQtYmxvY2stbWFyZ2luOiAyOHB4OyAvLyBUaGlzIHZhbHVlIHByb3ZpZGVzIGEgY29uc2lzdGVudCwgY29udGlndW91cyBzcGFjaW5nIGJldHdlZW4gYmxvY2tzLlxuJHRleHQtZWRpdG9yLWZvbnQtc2l6ZTogMTVweDtcbiRlZGl0b3ItbGluZS1oZWlnaHQ6IDEuODtcbiRlZGl0b3ItaHRtbC1mb250OiAkZm9udC1mYW1pbHktbW9ubztcblxuLyoqXG4gKiBCbG9jayAmIEVkaXRvciBVSS5cbiAqL1xuXG4kYmxvY2stdG9vbGJhci1oZWlnaHQ6ICRncmlkLXVuaXQtNjA7XG4kYm9yZGVyLXdpZHRoOiAxcHg7XG4kYm9yZGVyLXdpZHRoLWZvY3VzLWZhbGxiYWNrOiAycHg7IC8vIFRoaXMgZXhpc3RzIGFzIGEgZmFsbGJhY2ssIGFuZCBpcyBpZGVhbGx5IG92ZXJyaWRkZW4gYnkgdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSB1bmxlc3MgaW4gc29tZSBTQVNTIG1hdGggY2FzZXMuXG4kYm9yZGVyLXdpZHRoLXRhYjogMS41cHg7XG4kaGVscHRleHQtZm9udC1zaXplOiAxMnB4O1xuJHJhZGlvLWlucHV0LXNpemU6IDE2cHg7XG4kcmFkaW8taW5wdXQtc2l6ZS1zbTogMjRweDsgLy8gV2lkdGggJiBoZWlnaHQgZm9yIHNtYWxsIHZpZXdwb3J0cy5cblxuLy8gRGVwcmVjYXRlZCwgcGxlYXNlIGF2b2lkIHVzaW5nIHRoZXNlLlxuJGJsb2NrLXBhZGRpbmc6IDE0cHg7IC8vIFVzZWQgdG8gZGVmaW5lIHNwYWNlIGJldHdlZW4gYmxvY2sgZm9vdHByaW50IGFuZCBzdXJyb3VuZGluZyBib3JkZXJzLlxuJHJhZGl1cy1ibG9jay11aTogJHJhZGl1cy1zbWFsbDtcbiRzaGFkb3ctcG9wb3ZlcjogJGVsZXZhdGlvbi14LXNtYWxsO1xuJHNoYWRvdy1tb2RhbDogJGVsZXZhdGlvbi1sYXJnZTtcbiRkZWZhdWx0LWZvbnQtc2l6ZTogJGZvbnQtc2l6ZS1tZWRpdW07XG5cbi8qKlxuICogQmxvY2sgcGFkZGluZ3MuXG4gKi9cblxuLy8gUGFkZGluZyBmb3IgYmxvY2tzIHdpdGggYSBiYWNrZ3JvdW5kIGNvbG9yIChlLmcuIHBhcmFncmFwaCBvciBncm91cCkuXG4kYmxvY2stYmctcGFkZGluZy0tdjogMS4yNWVtO1xuJGJsb2NrLWJnLXBhZGRpbmctLWg6IDIuMzc1ZW07XG5cblxuLyoqXG4gKiBSZWFjdCBOYXRpdmUgc3BlY2lmaWMuXG4gKiBUaGVzZSB2YXJpYWJsZXMgZG8gbm90IGFwcGVhciB0byBiZSB1c2VkIGFueXdoZXJlIGVsc2UuXG4gKi9cblxuLy8gRGltZW5zaW9ucy5cbiRtb2JpbGUtaGVhZGVyLXRvb2xiYXItaGVpZ2h0OiA0NHB4O1xuJG1vYmlsZS1oZWFkZXItdG9vbGJhci1leHBhbmRlZC1oZWlnaHQ6IDUycHg7XG4kbW9iaWxlLWZsb2F0aW5nLXRvb2xiYXItaGVpZ2h0OiA0NHB4O1xuJG1vYmlsZS1mbG9hdGluZy10b29sYmFyLW1hcmdpbjogOHB4O1xuJG1vYmlsZS1jb2xvci1zd2F0Y2g6IDQ4cHg7XG5cbi8vIEJsb2NrIFVJLlxuJG1vYmlsZS1ibG9jay10b29sYmFyLWhlaWdodDogNDRweDtcbiRkaW1tZWQtb3BhY2l0eTogMTtcbiRibG9jay1lZGdlLXRvLWNvbnRlbnQ6IDE2cHg7XG4kc29saWQtYm9yZGVyLXNwYWNlOiAxMnB4O1xuJGRhc2hlZC1ib3JkZXItc3BhY2U6IDZweDtcbiRibG9jay1zZWxlY3RlZC1tYXJnaW46IDNweDtcbiRibG9jay1zZWxlY3RlZC1ib3JkZXItd2lkdGg6IDFweDtcbiRibG9jay1zZWxlY3RlZC1wYWRkaW5nOiAwO1xuJGJsb2NrLXNlbGVjdGVkLWNoaWxkLW1hcmdpbjogNXB4O1xuJGJsb2NrLXNlbGVjdGVkLXRvLWNvbnRlbnQ6ICRibG9jay1lZGdlLXRvLWNvbnRlbnQgLSAkYmxvY2stc2VsZWN0ZWQtbWFyZ2luIC0gJGJsb2NrLXNlbGVjdGVkLWJvcmRlci13aWR0aDtcbiIsIi8qKlxuICogQ29sb3JzXG4gKi9cblxuLy8gV29yZFByZXNzIGdyYXlzLlxuJGJsYWNrOiAjMDAwO1x0XHRcdC8vIFVzZSBvbmx5IHdoZW4geW91IHRydWx5IG5lZWQgcHVyZSBibGFjay4gRm9yIFVJLCB1c2UgJGdyYXktOTAwLlxuJGdyYXktOTAwOiAjMWUxZTFlO1xuJGdyYXktODAwOiAjMmYyZjJmO1xuJGdyYXktNzAwOiAjNzU3NTc1O1x0XHQvLyBNZWV0cyA0LjY6MSAoNC41OjEgaXMgbWluaW11bSkgdGV4dCBjb250cmFzdCBhZ2FpbnN0IHdoaXRlLlxuJGdyYXktNjAwOiAjOTQ5NDk0O1x0XHQvLyBNZWV0cyAzOjEgVUkgb3IgbGFyZ2UgdGV4dCBjb250cmFzdCBhZ2FpbnN0IHdoaXRlLlxuJGdyYXktNDAwOiAjY2NjO1xuJGdyYXktMzAwOiAjZGRkO1x0XHQvLyBVc2VkIGZvciBtb3N0IGJvcmRlcnMuXG4kZ3JheS0yMDA6ICNlMGUwZTA7XHRcdC8vIFVzZWQgc3BhcmluZ2x5IGZvciBsaWdodCBib3JkZXJzLlxuJGdyYXktMTAwOiAjZjBmMGYwO1x0XHQvLyBVc2VkIGZvciBsaWdodCBncmF5IGJhY2tncm91bmRzLlxuJHdoaXRlOiAjZmZmO1xuXG4vLyBPcGFjaXRpZXMgJiBhZGRpdGlvbmFsIGNvbG9ycy5cbiRkYXJrLWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJGdyYXktOTAwLCAwLjYyKTtcbiRtZWRpdW0tZ3JheS1wbGFjZWhvbGRlcjogcmdiYSgkZ3JheS05MDAsIDAuNTUpO1xuJGxpZ2h0LWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJHdoaXRlLCAwLjY1KTtcblxuLy8gQWxlcnQgY29sb3JzLlxuJGFsZXJ0LXllbGxvdzogI2YwYjg0OTtcbiRhbGVydC1yZWQ6ICNjYzE4MTg7XG4kYWxlcnQtZ3JlZW46ICM0YWI4NjY7XG5cbi8vIERlcHJlY2F0ZWQsIHBsZWFzZSBhdm9pZCB1c2luZyB0aGVzZS5cbiRkYXJrLXRoZW1lLWZvY3VzOiAkd2hpdGU7XHQvLyBGb2N1cyBjb2xvciB3aGVuIHRoZSB0aGVtZSBpcyBkYXJrLlxuIiwiQHVzZSBcIkB3b3JkcHJlc3MvYmFzZS1zdHlsZXMvdmFyaWFibGVzXCI7XG5cbiRoZWFkZXItaGVpZ2h0OiB2YXJpYWJsZXMuJGhlYWRlci1oZWlnaHQ7XG5cbi5ib290LXNpdGUtaWNvbi1saW5rIHtcblx0d2lkdGg6ICRoZWFkZXItaGVpZ2h0O1xuXHRoZWlnaHQ6ICRoZWFkZXItaGVpZ2h0O1xuXHRkaXNwbGF5OiBpbmxpbmUtZmxleDtcblx0YWxpZ24taXRlbXM6IGNlbnRlcjtcblx0anVzdGlmeS1jb250ZW50OiBjZW50ZXI7XG5cdGJhY2tncm91bmQ6IHZhcigtLXdwZHMtY29sb3ItYmctc3VyZmFjZS1uZXV0cmFsLXdlYWssICNmMGYwZjApO1xuXHR0ZXh0LWRlY29yYXRpb246IG5vbmU7XG5cblx0QG1lZGlhIG5vdCAocHJlZmVycy1yZWR1Y2VkLW1vdGlvbikge1xuXHRcdHRyYW5zaXRpb246IG91dGxpbmUgMC4xcyBlYXNlLW91dDtcblx0fVxuXG5cdCY6Zm9jdXM6bm90KDphY3RpdmUpIHtcblx0XHRvdXRsaW5lOlxuXHRcdFx0dmFyKC0td3Bkcy1ib3JkZXItd2lkdGgtaW50ZXJhY3RpdmUtZm9jdXMsIDJweCkgc29saWRcblx0XHRcdHZhcigtLXdwZHMtY29sb3Itc3Ryb2tlLWZvY3VzLWJyYW5kLCAjMDA3M2FhKTtcblx0XHRvdXRsaW5lLW9mZnNldDogY2FsYygtMSAqIHZhcigtLXdwZHMtYm9yZGVyLXdpZHRoLWludGVyYWN0aXZlLWZvY3VzLCAycHgpKTtcblx0fVxufVxuIl19 */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css2));
var { useCanGoBack, useRouter } = unlock(routePrivateApis);
function SiteIconLink({
  to,
  isBackButton,
  ...props
}) {
  const router = useRouter();
  const canGoBack = useCanGoBack();
  return /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(
    Link,
    {
      to,
      "aria-label": props["aria-label"],
      className: "boot-site-icon-link",
      onClick: (event) => {
        if (canGoBack && isBackButton) {
          event.preventDefault();
          router.history.back();
        }
      },
      children: /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(site_icon_default, {})
    }
  );
}
var site_icon_link_default = SiteIconLink;

// packages/boot/build-module/components/site-hub/index.js
var import_jsx_runtime16 = __toESM(require_jsx_runtime());
var css3 = `/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
.boot-site-hub {
  position: sticky;
  top: 0;
  background-color: var(--wpds-color-bg-surface-neutral-weak, #f0f0f0);
  z-index: 1;
  display: grid;
  grid-template-columns: 60px 1fr auto;
  align-items: center;
  padding-right: 16px;
  flex-shrink: 0;
}

.boot-site-hub__actions {
  flex-shrink: 0;
}

.boot-site-hub__title {
  display: flex;
  align-items: center;
  text-decoration: none;
}
.boot-site-hub__title .components-external-link__contents {
  text-decoration: none;
  margin-inline-start: 4px;
  overflow: hidden;
  max-width: 140px;
}
.boot-site-hub__title .components-external-link__icon {
  opacity: 0;
  transition: opacity 0.1s ease-out;
}
.boot-site-hub__title:hover .components-external-link__icon {
  opacity: 1;
}
@media not (prefers-reduced-motion) {
  .boot-site-hub__title {
    transition: outline 0.1s ease-out;
  }
}
.boot-site-hub__title:focus:not(:active) {
  outline: var(--wpds-border-width-interactive-focus, 2px) solid var(--wpds-color-stroke-focus-brand, #0073aa);
  outline-offset: calc(-1 * var(--wpds-border-width-interactive-focus, 2px));
}

.boot-site-hub__title-text {
  color: var(--wpds-color-fg-content-neutral, #1e1e1e);
  font-size: 13px;
  font-weight: 499;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}

.boot-site-hub__url {
  color: var(--wpds-color-fg-content-neutral-weak, #757575);
  font-size: 12px;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy9ib290L3NyYy9jb21wb25lbnRzL3NpdGUtaHViIiwic291cmNlcyI6WyIuLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9iYXNlLXN0eWxlcy9fdmFyaWFibGVzLnNjc3MiLCIuLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9iYXNlLXN0eWxlcy9fY29sb3JzLnNjc3MiLCJzdHlsZS5zY3NzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FDQUE7QUFBQTtBQUFBO0FEVUE7QUFBQTtBQUFBO0FBT0E7QUFBQTtBQUFBO0FBNkJBO0FBQUE7QUFBQTtBQUFBO0FBaUJBO0FBQUE7QUFBQTtBQVdBO0FBQUE7QUFBQTtBQWdCQTtBQUFBO0FBQUE7QUF5QkE7QUFBQTtBQUFBO0FBS0E7QUFBQTtBQUFBO0FBZUE7QUFBQTtBQUFBO0FBbUJBO0FBQUE7QUFBQTtBQVNBO0FBQUE7QUFBQTtBQUFBO0FFaktBO0VBQ0M7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQSxlRjZDYztFRTVDZDs7O0FBR0Q7RUFDQzs7O0FBR0Q7RUFDQztFQUNBO0VBQ0E7O0FBRUE7RUFDQztFQUNBLHFCRjJCYTtFRTFCYjtFQUNBOztBQUlEO0VBQ0M7RUFDQTs7QUFHRDtFQUNDOztBQUlEO0VBdkJEO0lBd0JFOzs7QUFHRDtFQUNDLFNBQ0M7RUFFRDs7O0FBSUY7RUFDQztFQUNBLFdGL0JrQjtFRWdDbEIsYUZqQm9CO0VFa0JwQjtFQUNBO0VBQ0E7OztBQUdEO0VBQ0M7RUFDQSxXRnpDaUI7RUUwQ2pCO0VBQ0E7RUFDQSIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogU0NTUyBWYXJpYWJsZXMuXG4gKlxuICogUGxlYXNlIHVzZSB2YXJpYWJsZXMgZnJvbSB0aGlzIHNoZWV0IHRvIGVuc3VyZSBjb25zaXN0ZW5jeSBhY3Jvc3MgdGhlIFVJLlxuICogRG9uJ3QgYWRkIHRvIHRoaXMgc2hlZXQgdW5sZXNzIHlvdSdyZSBwcmV0dHkgc3VyZSB0aGUgdmFsdWUgd2lsbCBiZSByZXVzZWQgaW4gbWFueSBwbGFjZXMuXG4gKiBGb3IgZXhhbXBsZSwgZG9uJ3QgYWRkIHJ1bGVzIHRvIHRoaXMgc2hlZXQgdGhhdCBhZmZlY3QgYmxvY2sgdmlzdWFscy4gSXQncyBwdXJlbHkgZm9yIFVJLlxuICovXG5cbkB1c2UgXCIuL2NvbG9yc1wiO1xuXG4vKipcbiAqIEZvbnRzICYgYmFzaWMgdmFyaWFibGVzLlxuICovXG5cbiRkZWZhdWx0LWZvbnQ6IC1hcHBsZS1zeXN0ZW0sIEJsaW5rTWFjU3lzdGVtRm9udCxcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLFwiSGVsdmV0aWNhIE5ldWVcIiwgc2Fucy1zZXJpZjsgLy8gVG9kbzogZGVwcmVjYXRlIGluIGZhdm9yIG9mICRmYW1pbHkgdmFyaWFibGVzXG4kZGVmYXVsdC1saW5lLWhlaWdodDogMS40OyAvLyBUb2RvOiBkZXByZWNhdGUgaW4gZmF2b3Igb2YgJGxpbmUtaGVpZ2h0IHRva2Vuc1xuXG4vKipcbiAqIFR5cG9ncmFwaHlcbiAqL1xuXG4vLyBTaXplc1xuJGZvbnQtc2l6ZS14LXNtYWxsOiAxMXB4O1xuJGZvbnQtc2l6ZS1zbWFsbDogMTJweDtcbiRmb250LXNpemUtbWVkaXVtOiAxM3B4O1xuJGZvbnQtc2l6ZS1sYXJnZTogMTVweDtcbiRmb250LXNpemUteC1sYXJnZTogMjBweDtcbiRmb250LXNpemUtMngtbGFyZ2U6IDMycHg7XG5cbi8vIExpbmUgaGVpZ2h0c1xuJGZvbnQtbGluZS1oZWlnaHQteC1zbWFsbDogMTZweDtcbiRmb250LWxpbmUtaGVpZ2h0LXNtYWxsOiAyMHB4O1xuJGZvbnQtbGluZS1oZWlnaHQtbWVkaXVtOiAyNHB4O1xuJGZvbnQtbGluZS1oZWlnaHQtbGFyZ2U6IDI4cHg7XG4kZm9udC1saW5lLWhlaWdodC14LWxhcmdlOiAzMnB4O1xuJGZvbnQtbGluZS1oZWlnaHQtMngtbGFyZ2U6IDQwcHg7XG5cbi8vIFdlaWdodHNcbiRmb250LXdlaWdodC1yZWd1bGFyOiA0MDA7XG4kZm9udC13ZWlnaHQtbWVkaXVtOiA0OTk7IC8vIGVuc3VyZXMgZmFsbGJhY2sgdG8gNDAwIChpbnN0ZWFkIG9mIDYwMClcblxuLy8gRmFtaWxpZXNcbiRmb250LWZhbWlseS1oZWFkaW5nczogLWFwcGxlLXN5c3RlbSwgXCJzeXN0ZW0tdWlcIiwgXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCwgXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmO1xuJGZvbnQtZmFtaWx5LWJvZHk6IC1hcHBsZS1zeXN0ZW0sIFwic3lzdGVtLXVpXCIsIFwiU2Vnb2UgVUlcIiwgUm9ib3RvLCBPeHlnZW4tU2FucywgVWJ1bnR1LCBDYW50YXJlbGwsIFwiSGVsdmV0aWNhIE5ldWVcIiwgc2Fucy1zZXJpZjtcbiRmb250LWZhbWlseS1tb25vOiBNZW5sbywgQ29uc29sYXMsIG1vbmFjbywgbW9ub3NwYWNlO1xuXG4vKipcbiAqIEdyaWQgU3lzdGVtLlxuICogaHR0cHM6Ly9tYWtlLndvcmRwcmVzcy5vcmcvZGVzaWduLzIwMTkvMTAvMzEvcHJvcG9zYWwtYS1jb25zaXN0ZW50LXNwYWNpbmctc3lzdGVtLWZvci13b3JkcHJlc3MvXG4gKi9cblxuJGdyaWQtdW5pdDogOHB4O1xuJGdyaWQtdW5pdC0wNTogMC41ICogJGdyaWQtdW5pdDtcdC8vIDRweFxuJGdyaWQtdW5pdC0xMDogMSAqICRncmlkLXVuaXQ7XHRcdC8vIDhweFxuJGdyaWQtdW5pdC0xNTogMS41ICogJGdyaWQtdW5pdDtcdC8vIDEycHhcbiRncmlkLXVuaXQtMjA6IDIgKiAkZ3JpZC11bml0O1x0XHQvLyAxNnB4XG4kZ3JpZC11bml0LTMwOiAzICogJGdyaWQtdW5pdDtcdFx0Ly8gMjRweFxuJGdyaWQtdW5pdC00MDogNCAqICRncmlkLXVuaXQ7XHRcdC8vIDMycHhcbiRncmlkLXVuaXQtNTA6IDUgKiAkZ3JpZC11bml0O1x0XHQvLyA0MHB4XG4kZ3JpZC11bml0LTYwOiA2ICogJGdyaWQtdW5pdDtcdFx0Ly8gNDhweFxuJGdyaWQtdW5pdC03MDogNyAqICRncmlkLXVuaXQ7XHRcdC8vIDU2cHhcbiRncmlkLXVuaXQtODA6IDggKiAkZ3JpZC11bml0O1x0XHQvLyA2NHB4XG5cbi8qKlxuICogUmFkaXVzIHNjYWxlLlxuICovXG5cbiRyYWRpdXMteC1zbWFsbDogMXB4OyAgIC8vIEFwcGxpZWQgdG8gZWxlbWVudHMgbGlrZSBidXR0b25zIG5lc3RlZCB3aXRoaW4gcHJpbWl0aXZlcyBsaWtlIGlucHV0cy5cbiRyYWRpdXMtc21hbGw6IDJweDsgICAgIC8vIEFwcGxpZWQgdG8gbW9zdCBwcmltaXRpdmVzLlxuJHJhZGl1cy1tZWRpdW06IDRweDsgICAgLy8gQXBwbGllZCB0byBjb250YWluZXJzIHdpdGggc21hbGxlciBwYWRkaW5nLlxuJHJhZGl1cy1sYXJnZTogOHB4OyAgICAgLy8gQXBwbGllZCB0byBjb250YWluZXJzIHdpdGggbGFyZ2VyIHBhZGRpbmcuXG4kcmFkaXVzLWZ1bGw6IDk5OTlweDsgICAvLyBGb3IgcGlsbHMuXG4kcmFkaXVzLXJvdW5kOiA1MCU7ICAgICAvLyBGb3IgY2lyY2xlcyBhbmQgb3ZhbHMuXG5cbi8qKlxuICogRWxldmF0aW9uIHNjYWxlLlxuICovXG5cbi8vIEZvciBzZWN0aW9ucyBhbmQgY29udGFpbmVycyB0aGF0IGdyb3VwIHJlbGF0ZWQgY29udGVudCBhbmQgY29udHJvbHMsIHdoaWNoIG1heSBvdmVybGFwIG90aGVyIGNvbnRlbnQuIEV4YW1wbGU6IFByZXZpZXcgRnJhbWUuXG4kZWxldmF0aW9uLXgtc21hbGw6IDAgMXB4IDFweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDMpLCAwIDFweCAycHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKSwgMCAzcHggM3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMiksIDAgNHB4IDRweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDEpO1xuXG4vLyBGb3IgY29tcG9uZW50cyB0aGF0IHByb3ZpZGUgY29udGV4dHVhbCBmZWVkYmFjayB3aXRob3V0IGJlaW5nIGludHJ1c2l2ZS4gR2VuZXJhbGx5IG5vbi1pbnRlcnJ1cHRpdmUuIEV4YW1wbGU6IFRvb2x0aXBzLCBTbmFja2Jhci5cbiRlbGV2YXRpb24tc21hbGw6IDAgMXB4IDJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDUpLCAwIDJweCAzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA0KSwgMCA2cHggNnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMyksIDAgOHB4IDhweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vLyBGb3IgY29tcG9uZW50cyB0aGF0IG9mZmVyIGFkZGl0aW9uYWwgYWN0aW9ucy4gRXhhbXBsZTogTWVudXMsIENvbW1hbmQgUGFsZXR0ZVxuJGVsZXZhdGlvbi1tZWRpdW06IDAgMnB4IDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDUpLCAwIDRweCA1cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA0KSwgMCAxMnB4IDEycHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAzKSwgMCAxNnB4IDE2cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKTtcblxuLy8gRm9yIGNvbXBvbmVudHMgdGhhdCBjb25maXJtIGRlY2lzaW9ucyBvciBoYW5kbGUgbmVjZXNzYXJ5IGludGVycnVwdGlvbnMuIEV4YW1wbGU6IE1vZGFscy5cbiRlbGV2YXRpb24tbGFyZ2U6IDAgNXB4IDE1cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA4KSwgMCAxNXB4IDI3cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA3KSwgMCAzMHB4IDM2cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA0KSwgMCA1MHB4IDQzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKTtcblxuLyoqXG4gKiBEaW1lbnNpb25zLlxuICovXG5cbiRpY29uLXNpemU6IDI0cHg7XG4kYnV0dG9uLXNpemU6IDM2cHg7XG4kYnV0dG9uLXNpemUtbmV4dC1kZWZhdWx0LTQwcHg6IDQwcHg7IC8vIHRyYW5zaXRpb25hcnkgdmFyaWFibGUgZm9yIG5leHQgZGVmYXVsdCBidXR0b24gc2l6ZVxuJGJ1dHRvbi1zaXplLXNtYWxsOiAyNHB4O1xuJGJ1dHRvbi1zaXplLWNvbXBhY3Q6IDMycHg7XG4kaGVhZGVyLWhlaWdodDogNjRweDtcbiRwYW5lbC1oZWFkZXItaGVpZ2h0OiAkZ3JpZC11bml0LTYwO1xuJG5hdi1zaWRlYmFyLXdpZHRoOiAzMDBweDtcbiRhZG1pbi1iYXItaGVpZ2h0OiAzMnB4O1xuJGFkbWluLWJhci1oZWlnaHQtYmlnOiA0NnB4O1xuJGFkbWluLXNpZGViYXItd2lkdGg6IDE2MHB4O1xuJGFkbWluLXNpZGViYXItd2lkdGgtYmlnOiAxOTBweDtcbiRhZG1pbi1zaWRlYmFyLXdpZHRoLWNvbGxhcHNlZDogMzZweDtcbiRtb2RhbC1taW4td2lkdGg6IDM1MHB4O1xuJG1vZGFsLXdpZHRoLXNtYWxsOiAzODRweDtcbiRtb2RhbC13aWR0aC1tZWRpdW06IDUxMnB4O1xuJG1vZGFsLXdpZHRoLWxhcmdlOiA4NDBweDtcbiRzcGlubmVyLXNpemU6IDE2cHg7XG4kY2FudmFzLXBhZGRpbmc6ICRncmlkLXVuaXQtMjA7XG4kcGFsZXR0ZS1tYXgtaGVpZ2h0OiAzNjhweDtcblxuLyoqXG4gKiBNb2JpbGUgc3BlY2lmaWMgc3R5bGVzXG4gKi9cbiRtb2JpbGUtdGV4dC1taW4tZm9udC1zaXplOiAxNnB4OyAvLyBBbnkgZm9udCBzaXplIGJlbG93IDE2cHggd2lsbCBjYXVzZSBNb2JpbGUgU2FmYXJpIHRvIFwiem9vbSBpblwiLlxuXG4vKipcbiAqIEVkaXRvciBzdHlsZXMuXG4gKi9cblxuJHNpZGViYXItd2lkdGg6IDI4MHB4O1xuJGNvbnRlbnQtd2lkdGg6IDg0MHB4O1xuJHdpZGUtY29udGVudC13aWR0aDogMTEwMHB4O1xuJHdpZGdldC1hcmVhLXdpZHRoOiA3MDBweDtcbiRzZWNvbmRhcnktc2lkZWJhci13aWR0aDogMzUwcHg7XG4kZWRpdG9yLWZvbnQtc2l6ZTogMTZweDtcbiRkZWZhdWx0LWJsb2NrLW1hcmdpbjogMjhweDsgLy8gVGhpcyB2YWx1ZSBwcm92aWRlcyBhIGNvbnNpc3RlbnQsIGNvbnRpZ3VvdXMgc3BhY2luZyBiZXR3ZWVuIGJsb2Nrcy5cbiR0ZXh0LWVkaXRvci1mb250LXNpemU6IDE1cHg7XG4kZWRpdG9yLWxpbmUtaGVpZ2h0OiAxLjg7XG4kZWRpdG9yLWh0bWwtZm9udDogJGZvbnQtZmFtaWx5LW1vbm87XG5cbi8qKlxuICogQmxvY2sgJiBFZGl0b3IgVUkuXG4gKi9cblxuJGJsb2NrLXRvb2xiYXItaGVpZ2h0OiAkZ3JpZC11bml0LTYwO1xuJGJvcmRlci13aWR0aDogMXB4O1xuJGJvcmRlci13aWR0aC1mb2N1cy1mYWxsYmFjazogMnB4OyAvLyBUaGlzIGV4aXN0cyBhcyBhIGZhbGxiYWNrLCBhbmQgaXMgaWRlYWxseSBvdmVycmlkZGVuIGJ5IHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgdW5sZXNzIGluIHNvbWUgU0FTUyBtYXRoIGNhc2VzLlxuJGJvcmRlci13aWR0aC10YWI6IDEuNXB4O1xuJGhlbHB0ZXh0LWZvbnQtc2l6ZTogMTJweDtcbiRyYWRpby1pbnB1dC1zaXplOiAxNnB4O1xuJHJhZGlvLWlucHV0LXNpemUtc206IDI0cHg7IC8vIFdpZHRoICYgaGVpZ2h0IGZvciBzbWFsbCB2aWV3cG9ydHMuXG5cbi8vIERlcHJlY2F0ZWQsIHBsZWFzZSBhdm9pZCB1c2luZyB0aGVzZS5cbiRibG9jay1wYWRkaW5nOiAxNHB4OyAvLyBVc2VkIHRvIGRlZmluZSBzcGFjZSBiZXR3ZWVuIGJsb2NrIGZvb3RwcmludCBhbmQgc3Vycm91bmRpbmcgYm9yZGVycy5cbiRyYWRpdXMtYmxvY2stdWk6ICRyYWRpdXMtc21hbGw7XG4kc2hhZG93LXBvcG92ZXI6ICRlbGV2YXRpb24teC1zbWFsbDtcbiRzaGFkb3ctbW9kYWw6ICRlbGV2YXRpb24tbGFyZ2U7XG4kZGVmYXVsdC1mb250LXNpemU6ICRmb250LXNpemUtbWVkaXVtO1xuXG4vKipcbiAqIEJsb2NrIHBhZGRpbmdzLlxuICovXG5cbi8vIFBhZGRpbmcgZm9yIGJsb2NrcyB3aXRoIGEgYmFja2dyb3VuZCBjb2xvciAoZS5nLiBwYXJhZ3JhcGggb3IgZ3JvdXApLlxuJGJsb2NrLWJnLXBhZGRpbmctLXY6IDEuMjVlbTtcbiRibG9jay1iZy1wYWRkaW5nLS1oOiAyLjM3NWVtO1xuXG5cbi8qKlxuICogUmVhY3QgTmF0aXZlIHNwZWNpZmljLlxuICogVGhlc2UgdmFyaWFibGVzIGRvIG5vdCBhcHBlYXIgdG8gYmUgdXNlZCBhbnl3aGVyZSBlbHNlLlxuICovXG5cbi8vIERpbWVuc2lvbnMuXG4kbW9iaWxlLWhlYWRlci10b29sYmFyLWhlaWdodDogNDRweDtcbiRtb2JpbGUtaGVhZGVyLXRvb2xiYXItZXhwYW5kZWQtaGVpZ2h0OiA1MnB4O1xuJG1vYmlsZS1mbG9hdGluZy10b29sYmFyLWhlaWdodDogNDRweDtcbiRtb2JpbGUtZmxvYXRpbmctdG9vbGJhci1tYXJnaW46IDhweDtcbiRtb2JpbGUtY29sb3Itc3dhdGNoOiA0OHB4O1xuXG4vLyBCbG9jayBVSS5cbiRtb2JpbGUtYmxvY2stdG9vbGJhci1oZWlnaHQ6IDQ0cHg7XG4kZGltbWVkLW9wYWNpdHk6IDE7XG4kYmxvY2stZWRnZS10by1jb250ZW50OiAxNnB4O1xuJHNvbGlkLWJvcmRlci1zcGFjZTogMTJweDtcbiRkYXNoZWQtYm9yZGVyLXNwYWNlOiA2cHg7XG4kYmxvY2stc2VsZWN0ZWQtbWFyZ2luOiAzcHg7XG4kYmxvY2stc2VsZWN0ZWQtYm9yZGVyLXdpZHRoOiAxcHg7XG4kYmxvY2stc2VsZWN0ZWQtcGFkZGluZzogMDtcbiRibG9jay1zZWxlY3RlZC1jaGlsZC1tYXJnaW46IDVweDtcbiRibG9jay1zZWxlY3RlZC10by1jb250ZW50OiAkYmxvY2stZWRnZS10by1jb250ZW50IC0gJGJsb2NrLXNlbGVjdGVkLW1hcmdpbiAtICRibG9jay1zZWxlY3RlZC1ib3JkZXItd2lkdGg7XG4iLCIvKipcbiAqIENvbG9yc1xuICovXG5cbi8vIFdvcmRQcmVzcyBncmF5cy5cbiRibGFjazogIzAwMDtcdFx0XHQvLyBVc2Ugb25seSB3aGVuIHlvdSB0cnVseSBuZWVkIHB1cmUgYmxhY2suIEZvciBVSSwgdXNlICRncmF5LTkwMC5cbiRncmF5LTkwMDogIzFlMWUxZTtcbiRncmF5LTgwMDogIzJmMmYyZjtcbiRncmF5LTcwMDogIzc1NzU3NTtcdFx0Ly8gTWVldHMgNC42OjEgKDQuNToxIGlzIG1pbmltdW0pIHRleHQgY29udHJhc3QgYWdhaW5zdCB3aGl0ZS5cbiRncmF5LTYwMDogIzk0OTQ5NDtcdFx0Ly8gTWVldHMgMzoxIFVJIG9yIGxhcmdlIHRleHQgY29udHJhc3QgYWdhaW5zdCB3aGl0ZS5cbiRncmF5LTQwMDogI2NjYztcbiRncmF5LTMwMDogI2RkZDtcdFx0Ly8gVXNlZCBmb3IgbW9zdCBib3JkZXJzLlxuJGdyYXktMjAwOiAjZTBlMGUwO1x0XHQvLyBVc2VkIHNwYXJpbmdseSBmb3IgbGlnaHQgYm9yZGVycy5cbiRncmF5LTEwMDogI2YwZjBmMDtcdFx0Ly8gVXNlZCBmb3IgbGlnaHQgZ3JheSBiYWNrZ3JvdW5kcy5cbiR3aGl0ZTogI2ZmZjtcblxuLy8gT3BhY2l0aWVzICYgYWRkaXRpb25hbCBjb2xvcnMuXG4kZGFyay1ncmF5LXBsYWNlaG9sZGVyOiByZ2JhKCRncmF5LTkwMCwgMC42Mik7XG4kbWVkaXVtLWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJGdyYXktOTAwLCAwLjU1KTtcbiRsaWdodC1ncmF5LXBsYWNlaG9sZGVyOiByZ2JhKCR3aGl0ZSwgMC42NSk7XG5cbi8vIEFsZXJ0IGNvbG9ycy5cbiRhbGVydC15ZWxsb3c6ICNmMGI4NDk7XG4kYWxlcnQtcmVkOiAjY2MxODE4O1xuJGFsZXJ0LWdyZWVuOiAjNGFiODY2O1xuXG4vLyBEZXByZWNhdGVkLCBwbGVhc2UgYXZvaWQgdXNpbmcgdGhlc2UuXG4kZGFyay10aGVtZS1mb2N1czogJHdoaXRlO1x0Ly8gRm9jdXMgY29sb3Igd2hlbiB0aGUgdGhlbWUgaXMgZGFyay5cbiIsIkB1c2UgXCJAd29yZHByZXNzL2Jhc2Utc3R5bGVzL3ZhcmlhYmxlc1wiO1xuXG4uYm9vdC1zaXRlLWh1YiB7XG5cdHBvc2l0aW9uOiBzdGlja3k7XG5cdHRvcDogMDtcblx0YmFja2dyb3VuZC1jb2xvcjogdmFyKC0td3Bkcy1jb2xvci1iZy1zdXJmYWNlLW5ldXRyYWwtd2VhaywgI2YwZjBmMCk7XG5cdHotaW5kZXg6IDE7XG5cdGRpc3BsYXk6IGdyaWQ7XG5cdGdyaWQtdGVtcGxhdGUtY29sdW1uczogNjBweCAxZnIgYXV0bztcblx0YWxpZ24taXRlbXM6IGNlbnRlcjtcblx0cGFkZGluZy1yaWdodDogdmFyaWFibGVzLiRncmlkLXVuaXQtMjA7XG5cdGZsZXgtc2hyaW5rOiAwOyAvLyBQcmV2ZW50IGZsZXggcGFyZW50IGZyb20gc2hyaW5raW5nIHRoaXMgZWxlbWVudC5cbn1cblxuLmJvb3Qtc2l0ZS1odWJfX2FjdGlvbnMge1xuXHRmbGV4LXNocmluazogMDtcbn1cblxuLmJvb3Qtc2l0ZS1odWJfX3RpdGxlIHtcblx0ZGlzcGxheTogZmxleDtcblx0YWxpZ24taXRlbXM6IGNlbnRlcjtcblx0dGV4dC1kZWNvcmF0aW9uOiBub25lO1xuXG5cdC5jb21wb25lbnRzLWV4dGVybmFsLWxpbmtfX2NvbnRlbnRzIHtcblx0XHR0ZXh0LWRlY29yYXRpb246IG5vbmU7XG5cdFx0bWFyZ2luLWlubGluZS1zdGFydDogdmFyaWFibGVzLiRncmlkLXVuaXQtMDU7XG5cdFx0b3ZlcmZsb3c6IGhpZGRlbjtcblx0XHRtYXgtd2lkdGg6IDE0MHB4O1xuXHR9XG5cblx0Ly8gU2hvdyBpY29uIG9uIGhvdmVyXG5cdC5jb21wb25lbnRzLWV4dGVybmFsLWxpbmtfX2ljb24ge1xuXHRcdG9wYWNpdHk6IDA7XG5cdFx0dHJhbnNpdGlvbjogb3BhY2l0eSAwLjFzIGVhc2Utb3V0O1xuXHR9XG5cblx0Jjpob3ZlciAuY29tcG9uZW50cy1leHRlcm5hbC1saW5rX19pY29uIHtcblx0XHRvcGFjaXR5OiAxO1xuXHR9XG5cblx0Ly8gRm9jdXMgc3R5bGVzXG5cdEBtZWRpYSBub3QgKHByZWZlcnMtcmVkdWNlZC1tb3Rpb24pIHtcblx0XHR0cmFuc2l0aW9uOiBvdXRsaW5lIDAuMXMgZWFzZS1vdXQ7XG5cdH1cblxuXHQmOmZvY3VzOm5vdCg6YWN0aXZlKSB7XG5cdFx0b3V0bGluZTpcblx0XHRcdHZhcigtLXdwZHMtYm9yZGVyLXdpZHRoLWludGVyYWN0aXZlLWZvY3VzLCAycHgpIHNvbGlkXG5cdFx0XHR2YXIoLS13cGRzLWNvbG9yLXN0cm9rZS1mb2N1cy1icmFuZCwgIzAwNzNhYSk7XG5cdFx0b3V0bGluZS1vZmZzZXQ6IGNhbGMoLTEgKiB2YXIoLS13cGRzLWJvcmRlci13aWR0aC1pbnRlcmFjdGl2ZS1mb2N1cywgMnB4KSk7XG5cdH1cbn1cblxuLmJvb3Qtc2l0ZS1odWJfX3RpdGxlLXRleHQge1xuXHRjb2xvcjogdmFyKC0td3Bkcy1jb2xvci1mZy1jb250ZW50LW5ldXRyYWwsICMxZTFlMWUpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLW1lZGl1bTtcblx0Zm9udC13ZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC13ZWlnaHQtbWVkaXVtO1xuXHRvdmVyZmxvdzogaGlkZGVuO1xuXHR0ZXh0LW92ZXJmbG93OiBlbGxpcHNpcztcblx0d2hpdGUtc3BhY2U6IG5vd3JhcDtcbn1cblxuLmJvb3Qtc2l0ZS1odWJfX3VybCB7XG5cdGNvbG9yOiB2YXIoLS13cGRzLWNvbG9yLWZnLWNvbnRlbnQtbmV1dHJhbC13ZWFrLCAjNzU3NTc1KTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS1zbWFsbDtcblx0b3ZlcmZsb3c6IGhpZGRlbjtcblx0dGV4dC1vdmVyZmxvdzogZWxsaXBzaXM7XG5cdHdoaXRlLXNwYWNlOiBub3dyYXA7XG59XG4iXX0= */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css3));
function SiteHub() {
  const { homeUrl, siteTitle } = (0, import_data2.useSelect)((select) => {
    const { getEntityRecord } = select(import_core_data2.store);
    const _base = getEntityRecord(
      "root",
      "__unstableBase"
    );
    return {
      homeUrl: _base?.home,
      siteTitle: !_base?.name && !!_base?.url ? (0, import_url.filterURLForDisplay)(_base?.url) : _base?.name
    };
  }, []);
  const { open: openCommandCenter } = (0, import_data2.useDispatch)(import_commands.store);
  return /* @__PURE__ */ (0, import_jsx_runtime16.jsxs)("div", { className: "boot-site-hub", children: [
    /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(site_icon_link_default, { to: "/", "aria-label": (0, import_i18n2.__)("Go to the Dashboard") }),
    /* @__PURE__ */ (0, import_jsx_runtime16.jsxs)(
      import_components3.ExternalLink,
      {
        href: homeUrl ?? "/",
        className: "boot-site-hub__title",
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime16.jsx)("div", { className: "boot-site-hub__title-text", children: siteTitle && (0, import_html_entities.decodeEntities)(siteTitle) }),
          /* @__PURE__ */ (0, import_jsx_runtime16.jsx)("div", { className: "boot-site-hub__url", children: (0, import_url.filterURLForDisplay)(homeUrl ?? "") })
        ]
      }
    ),
    /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_components3.__experimentalHStack, { className: "boot-site-hub__actions", children: /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
      import_components3.Button,
      {
        variant: "tertiary",
        icon: search_default,
        onClick: () => openCommandCenter(),
        size: "compact",
        label: (0, import_i18n2.__)("Open command palette"),
        shortcut: import_keycodes.displayShortcut.primary("k")
      }
    ) })
  ] });
}
var site_hub_default = SiteHub;

// packages/boot/build-module/components/navigation/index.js
var import_element6 = __toESM(require_element());
var import_data6 = __toESM(require_data());

// packages/boot/build-module/store/index.js
var import_data3 = __toESM(require_data());

// packages/boot/build-module/store/reducer.js
var initialState = {
  menuItems: {},
  routes: []
};
function reducer(state = initialState, action) {
  switch (action.type) {
    case "REGISTER_MENU_ITEM":
      return {
        ...state,
        menuItems: {
          ...state.menuItems,
          [action.id]: action.menuItem
        }
      };
    case "UPDATE_MENU_ITEM":
      return {
        ...state,
        menuItems: {
          ...state.menuItems,
          [action.id]: {
            ...state.menuItems[action.id],
            ...action.updates
          }
        }
      };
    case "REGISTER_ROUTE":
      return {
        ...state,
        routes: [...state.routes, action.route]
      };
  }
  return state;
}

// packages/boot/build-module/store/actions.js
var actions_exports = {};
__export(actions_exports, {
  registerMenuItem: () => registerMenuItem,
  registerRoute: () => registerRoute,
  updateMenuItem: () => updateMenuItem
});
function registerMenuItem(id, menuItem) {
  return {
    type: "REGISTER_MENU_ITEM",
    id,
    menuItem
  };
}
function updateMenuItem(id, updates) {
  return {
    type: "UPDATE_MENU_ITEM",
    id,
    updates
  };
}
function registerRoute(route) {
  return {
    type: "REGISTER_ROUTE",
    route
  };
}

// packages/boot/build-module/store/selectors.js
var selectors_exports = {};
__export(selectors_exports, {
  getMenuItems: () => getMenuItems,
  getRoutes: () => getRoutes
});
function getMenuItems(state) {
  return Object.values(state.menuItems);
}
function getRoutes(state) {
  return state.routes;
}

// packages/boot/build-module/store/index.js
var STORE_NAME = "wordpress/boot";
var store = (0, import_data3.createReduxStore)(STORE_NAME, {
  reducer,
  actions: actions_exports,
  selectors: selectors_exports
});
(0, import_data3.register)(store);

// packages/boot/build-module/components/navigation/navigation-item/index.js
var import_components6 = __toESM(require_components());

// packages/boot/build-module/components/navigation/router-link-item.js
var import_element3 = __toESM(require_element());
var import_components4 = __toESM(require_components());
import { privateApis as routePrivateApis2 } from "@wordpress/route";
var import_jsx_runtime17 = __toESM(require_jsx_runtime());
var { createLink } = unlock(routePrivateApis2);
function AnchorOnlyItem(props, forwardedRef) {
  return /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_components4.__experimentalItem, { as: "a", ref: forwardedRef, ...props });
}
var RouterLinkItem = createLink((0, import_element3.forwardRef)(AnchorOnlyItem));
var router_link_item_default = RouterLinkItem;

// packages/boot/build-module/components/navigation/items.js
var import_element4 = __toESM(require_element());
var import_components5 = __toESM(require_components());
var import_primitives11 = __toESM(require_primitives());
var import_jsx_runtime18 = __toESM(require_jsx_runtime());
function isSvg(element) {
  return (0, import_element4.isValidElement)(element) && (element.type === import_primitives11.SVG || element.type === "svg");
}
function wrapIcon(icon, shouldShowPlaceholder = true) {
  if (isSvg(icon)) {
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(import_components5.Icon, { icon });
  }
  if (typeof icon === "string" && icon.startsWith("dashicons-")) {
    const iconKey = icon.replace(
      /^dashicons-/,
      ""
    );
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
      import_components5.Dashicon,
      {
        style: { padding: "2px" },
        icon: iconKey,
        "aria-hidden": "true"
      }
    );
  }
  if (typeof icon === "string" && icon.startsWith("data:")) {
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
      "img",
      {
        src: icon,
        alt: "",
        "aria-hidden": "true",
        style: {
          width: "20px",
          height: "20px",
          display: "block",
          padding: "2px"
        }
      }
    );
  }
  if (icon) {
    return icon;
  }
  if (shouldShowPlaceholder) {
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
      "div",
      {
        style: { width: "24px", height: "24px" },
        "aria-hidden": "true"
      }
    );
  }
  return null;
}

// packages/boot/build-module/components/navigation/navigation-item/index.js
var import_jsx_runtime19 = __toESM(require_jsx_runtime());
var css4 = `/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
/**
 * Typography
 */
/**
 * Breakpoints & Media Queries
 */
/**
*  Converts a hex value into the rgb equivalent.
*
* @param {string} hex - the hexadecimal value to convert
* @return {string} comma separated rgb values
*/
/**
 * Long content fade mixin
 *
 * Creates a fading overlay to signify that the content is longer
 * than the space allows.
 */
/**
 * Breakpoint mixins
 */
/**
 * Focus styles.
 */
/**
 * Applies editor left position to the selector passed as argument
 */
/**
 * Styles that are reused verbatim in a few places
 */
/**
 * Allows users to opt-out of animations via OS-level preferences.
 */
/**
 * Reset default styles for JavaScript UI based pages.
 * This is a WP-admin agnostic reset
 */
/**
 * Reset the WP Admin page styles for Gutenberg-like pages.
 */
.boot-navigation-item.components-item {
  color: var(--wpds-color-fg-interactive-neutral, #1e1e1e);
  padding-inline: 4px;
  padding-block: 0;
  margin-inline: 12px;
  margin-block-end: 4px;
  width: calc(100% - 24px);
  border: none;
  min-height: 32px;
  display: flex;
  align-items: center;
  font-family: -apple-system, "system-ui", "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
  font-weight: 400;
  font-size: 13px;
  line-height: 20px;
}
.boot-dropdown-item__children .boot-navigation-item.components-item {
  min-height: 24px;
}
.boot-navigation-item.components-item {
  border-radius: var(--wpds-border-radius-surface-sm, 2px);
}
.boot-navigation-item.components-item.active, .boot-navigation-item.components-item:hover, .boot-navigation-item.components-item:focus, .boot-navigation-item.components-item[aria-current=true] {
  color: var(--wpds-color-fg-interactive-brand-active, #0073aa);
}
.boot-navigation-item.components-item.active {
  font-weight: 499;
}
.boot-navigation-item.components-item svg:last-child {
  padding: 4px;
}
.boot-navigation-item.components-item[aria-current=true] {
  color: var(--wpds-color-fg-interactive-brand-active, #0073aa);
  font-weight: 499;
}
.boot-navigation-item.components-item:focus-visible {
  transform: translateZ(0);
}
.boot-navigation-item.components-item.with-suffix {
  padding-right: 16px;
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy9ib290L3NyYy9jb21wb25lbnRzL25hdmlnYXRpb24vbmF2aWdhdGlvbi1pdGVtIiwic291cmNlcyI6WyIuLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9iYXNlLXN0eWxlcy9fdmFyaWFibGVzLnNjc3MiLCIuLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9iYXNlLXN0eWxlcy9fY29sb3JzLnNjc3MiLCIuLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9iYXNlLXN0eWxlcy9fbWl4aW5zLnNjc3MiLCIuLi8uLi8uLi8uLi8uLi8uLi9ub2RlX21vZHVsZXMvQHdvcmRwcmVzcy9iYXNlLXN0eWxlcy9fYnJlYWtwb2ludHMuc2NzcyIsIi4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19mdW5jdGlvbnMuc2NzcyIsIi4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19sb25nLWNvbnRlbnQtZmFkZS5zY3NzIiwic3R5bGUuc2NzcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQ0FBO0FBQUE7QUFBQTtBRFVBO0FBQUE7QUFBQTtBQU9BO0FBQUE7QUFBQTtBQTZCQTtBQUFBO0FBQUE7QUFBQTtBQWlCQTtBQUFBO0FBQUE7QUFXQTtBQUFBO0FBQUE7QUFnQkE7QUFBQTtBQUFBO0FBeUJBO0FBQUE7QUFBQTtBQUtBO0FBQUE7QUFBQTtBQWVBO0FBQUE7QUFBQTtBQW1CQTtBQUFBO0FBQUE7QUFTQTtBQUFBO0FBQUE7QUFBQTtBRW5LQTtBQUFBO0FBQUE7QUNBQTtBQUFBO0FBQUE7QUNBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUNBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUg0RUE7QUFBQTtBQUFBO0FBMERBO0FBQUE7QUFBQTtBQWdEQTtBQUFBO0FBQUE7QUFxQ0E7QUFBQTtBQUFBO0FBb0JBO0FBQUE7QUFBQTtBQXdMQTtBQUFBO0FBQUE7QUFBQTtBQWdEQTtBQUFBO0FBQUE7QUlwZEE7RUFDQztFQUNBLGdCTitDYztFTTlDZDtFQUNBLGVOK0NjO0VNOUNkLGtCTjRDYztFTTNDZDtFQUNBO0VBQ0EsWU44Q2M7RU03Q2Q7RUFDQTtFSktBLGFGeUJrQjtFRXhCbEIsYUZtQnFCO0VFc0JyQixXRnBDa0I7RUVxQ2xCLGFGOUJ3Qjs7QU1meEI7RUFDQyxZTnVDYTs7QU1yRGY7RUFrQkM7O0FBRUE7RUFJQzs7QUFHRDtFQUNDLGFOUW1COztBTUxwQjtFQUNDLFNOaUJhOztBTWRkO0VBQ0M7RUFDQSxhTkRtQjs7QU1LcEI7RUFDQzs7QUFHRDtFQUNDLGVOTWEiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIFNDU1MgVmFyaWFibGVzLlxuICpcbiAqIFBsZWFzZSB1c2UgdmFyaWFibGVzIGZyb20gdGhpcyBzaGVldCB0byBlbnN1cmUgY29uc2lzdGVuY3kgYWNyb3NzIHRoZSBVSS5cbiAqIERvbid0IGFkZCB0byB0aGlzIHNoZWV0IHVubGVzcyB5b3UncmUgcHJldHR5IHN1cmUgdGhlIHZhbHVlIHdpbGwgYmUgcmV1c2VkIGluIG1hbnkgcGxhY2VzLlxuICogRm9yIGV4YW1wbGUsIGRvbid0IGFkZCBydWxlcyB0byB0aGlzIHNoZWV0IHRoYXQgYWZmZWN0IGJsb2NrIHZpc3VhbHMuIEl0J3MgcHVyZWx5IGZvciBVSS5cbiAqL1xuXG5AdXNlIFwiLi9jb2xvcnNcIjtcblxuLyoqXG4gKiBGb250cyAmIGJhc2ljIHZhcmlhYmxlcy5cbiAqL1xuXG4kZGVmYXVsdC1mb250OiAtYXBwbGUtc3lzdGVtLCBCbGlua01hY1N5c3RlbUZvbnQsXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCxcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7IC8vIFRvZG86IGRlcHJlY2F0ZSBpbiBmYXZvciBvZiAkZmFtaWx5IHZhcmlhYmxlc1xuJGRlZmF1bHQtbGluZS1oZWlnaHQ6IDEuNDsgLy8gVG9kbzogZGVwcmVjYXRlIGluIGZhdm9yIG9mICRsaW5lLWhlaWdodCB0b2tlbnNcblxuLyoqXG4gKiBUeXBvZ3JhcGh5XG4gKi9cblxuLy8gU2l6ZXNcbiRmb250LXNpemUteC1zbWFsbDogMTFweDtcbiRmb250LXNpemUtc21hbGw6IDEycHg7XG4kZm9udC1zaXplLW1lZGl1bTogMTNweDtcbiRmb250LXNpemUtbGFyZ2U6IDE1cHg7XG4kZm9udC1zaXplLXgtbGFyZ2U6IDIwcHg7XG4kZm9udC1zaXplLTJ4LWxhcmdlOiAzMnB4O1xuXG4vLyBMaW5lIGhlaWdodHNcbiRmb250LWxpbmUtaGVpZ2h0LXgtc21hbGw6IDE2cHg7XG4kZm9udC1saW5lLWhlaWdodC1zbWFsbDogMjBweDtcbiRmb250LWxpbmUtaGVpZ2h0LW1lZGl1bTogMjRweDtcbiRmb250LWxpbmUtaGVpZ2h0LWxhcmdlOiAyOHB4O1xuJGZvbnQtbGluZS1oZWlnaHQteC1sYXJnZTogMzJweDtcbiRmb250LWxpbmUtaGVpZ2h0LTJ4LWxhcmdlOiA0MHB4O1xuXG4vLyBXZWlnaHRzXG4kZm9udC13ZWlnaHQtcmVndWxhcjogNDAwO1xuJGZvbnQtd2VpZ2h0LW1lZGl1bTogNDk5OyAvLyBlbnN1cmVzIGZhbGxiYWNrIHRvIDQwMCAoaW5zdGVhZCBvZiA2MDApXG5cbi8vIEZhbWlsaWVzXG4kZm9udC1mYW1pbHktaGVhZGluZ3M6IC1hcHBsZS1zeXN0ZW0sIFwic3lzdGVtLXVpXCIsIFwiU2Vnb2UgVUlcIiwgUm9ib3RvLCBPeHlnZW4tU2FucywgVWJ1bnR1LCBDYW50YXJlbGwsIFwiSGVsdmV0aWNhIE5ldWVcIiwgc2Fucy1zZXJpZjtcbiRmb250LWZhbWlseS1ib2R5OiAtYXBwbGUtc3lzdGVtLCBcInN5c3RlbS11aVwiLCBcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLCBcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7XG4kZm9udC1mYW1pbHktbW9ubzogTWVubG8sIENvbnNvbGFzLCBtb25hY28sIG1vbm9zcGFjZTtcblxuLyoqXG4gKiBHcmlkIFN5c3RlbS5cbiAqIGh0dHBzOi8vbWFrZS53b3JkcHJlc3Mub3JnL2Rlc2lnbi8yMDE5LzEwLzMxL3Byb3Bvc2FsLWEtY29uc2lzdGVudC1zcGFjaW5nLXN5c3RlbS1mb3Itd29yZHByZXNzL1xuICovXG5cbiRncmlkLXVuaXQ6IDhweDtcbiRncmlkLXVuaXQtMDU6IDAuNSAqICRncmlkLXVuaXQ7XHQvLyA0cHhcbiRncmlkLXVuaXQtMTA6IDEgKiAkZ3JpZC11bml0O1x0XHQvLyA4cHhcbiRncmlkLXVuaXQtMTU6IDEuNSAqICRncmlkLXVuaXQ7XHQvLyAxMnB4XG4kZ3JpZC11bml0LTIwOiAyICogJGdyaWQtdW5pdDtcdFx0Ly8gMTZweFxuJGdyaWQtdW5pdC0zMDogMyAqICRncmlkLXVuaXQ7XHRcdC8vIDI0cHhcbiRncmlkLXVuaXQtNDA6IDQgKiAkZ3JpZC11bml0O1x0XHQvLyAzMnB4XG4kZ3JpZC11bml0LTUwOiA1ICogJGdyaWQtdW5pdDtcdFx0Ly8gNDBweFxuJGdyaWQtdW5pdC02MDogNiAqICRncmlkLXVuaXQ7XHRcdC8vIDQ4cHhcbiRncmlkLXVuaXQtNzA6IDcgKiAkZ3JpZC11bml0O1x0XHQvLyA1NnB4XG4kZ3JpZC11bml0LTgwOiA4ICogJGdyaWQtdW5pdDtcdFx0Ly8gNjRweFxuXG4vKipcbiAqIFJhZGl1cyBzY2FsZS5cbiAqL1xuXG4kcmFkaXVzLXgtc21hbGw6IDFweDsgICAvLyBBcHBsaWVkIHRvIGVsZW1lbnRzIGxpa2UgYnV0dG9ucyBuZXN0ZWQgd2l0aGluIHByaW1pdGl2ZXMgbGlrZSBpbnB1dHMuXG4kcmFkaXVzLXNtYWxsOiAycHg7ICAgICAvLyBBcHBsaWVkIHRvIG1vc3QgcHJpbWl0aXZlcy5cbiRyYWRpdXMtbWVkaXVtOiA0cHg7ICAgIC8vIEFwcGxpZWQgdG8gY29udGFpbmVycyB3aXRoIHNtYWxsZXIgcGFkZGluZy5cbiRyYWRpdXMtbGFyZ2U6IDhweDsgICAgIC8vIEFwcGxpZWQgdG8gY29udGFpbmVycyB3aXRoIGxhcmdlciBwYWRkaW5nLlxuJHJhZGl1cy1mdWxsOiA5OTk5cHg7ICAgLy8gRm9yIHBpbGxzLlxuJHJhZGl1cy1yb3VuZDogNTAlOyAgICAgLy8gRm9yIGNpcmNsZXMgYW5kIG92YWxzLlxuXG4vKipcbiAqIEVsZXZhdGlvbiBzY2FsZS5cbiAqL1xuXG4vLyBGb3Igc2VjdGlvbnMgYW5kIGNvbnRhaW5lcnMgdGhhdCBncm91cCByZWxhdGVkIGNvbnRlbnQgYW5kIGNvbnRyb2xzLCB3aGljaCBtYXkgb3ZlcmxhcCBvdGhlciBjb250ZW50LiBFeGFtcGxlOiBQcmV2aWV3IEZyYW1lLlxuJGVsZXZhdGlvbi14LXNtYWxsOiAwIDFweCAxcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAzKSwgMCAxcHggMnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMiksIDAgM3B4IDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpLCAwIDRweCA0cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAxKTtcblxuLy8gRm9yIGNvbXBvbmVudHMgdGhhdCBwcm92aWRlIGNvbnRleHR1YWwgZmVlZGJhY2sgd2l0aG91dCBiZWluZyBpbnRydXNpdmUuIEdlbmVyYWxseSBub24taW50ZXJydXB0aXZlLiBFeGFtcGxlOiBUb29sdGlwcywgU25hY2tiYXIuXG4kZWxldmF0aW9uLXNtYWxsOiAwIDFweCAycHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA1KSwgMCAycHggM3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNCksIDAgNnB4IDZweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDMpLCAwIDhweCA4cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKTtcblxuLy8gRm9yIGNvbXBvbmVudHMgdGhhdCBvZmZlciBhZGRpdGlvbmFsIGFjdGlvbnMuIEV4YW1wbGU6IE1lbnVzLCBDb21tYW5kIFBhbGV0dGVcbiRlbGV2YXRpb24tbWVkaXVtOiAwIDJweCAzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA1KSwgMCA0cHggNXB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNCksIDAgMTJweCAxMnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMyksIDAgMTZweCAxNnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMik7XG5cbi8vIEZvciBjb21wb25lbnRzIHRoYXQgY29uZmlybSBkZWNpc2lvbnMgb3IgaGFuZGxlIG5lY2Vzc2FyeSBpbnRlcnJ1cHRpb25zLiBFeGFtcGxlOiBNb2RhbHMuXG4kZWxldmF0aW9uLWxhcmdlOiAwIDVweCAxNXB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wOCksIDAgMTVweCAyN3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNyksIDAgMzBweCAzNnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNCksIDAgNTBweCA0M3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMik7XG5cbi8qKlxuICogRGltZW5zaW9ucy5cbiAqL1xuXG4kaWNvbi1zaXplOiAyNHB4O1xuJGJ1dHRvbi1zaXplOiAzNnB4O1xuJGJ1dHRvbi1zaXplLW5leHQtZGVmYXVsdC00MHB4OiA0MHB4OyAvLyB0cmFuc2l0aW9uYXJ5IHZhcmlhYmxlIGZvciBuZXh0IGRlZmF1bHQgYnV0dG9uIHNpemVcbiRidXR0b24tc2l6ZS1zbWFsbDogMjRweDtcbiRidXR0b24tc2l6ZS1jb21wYWN0OiAzMnB4O1xuJGhlYWRlci1oZWlnaHQ6IDY0cHg7XG4kcGFuZWwtaGVhZGVyLWhlaWdodDogJGdyaWQtdW5pdC02MDtcbiRuYXYtc2lkZWJhci13aWR0aDogMzAwcHg7XG4kYWRtaW4tYmFyLWhlaWdodDogMzJweDtcbiRhZG1pbi1iYXItaGVpZ2h0LWJpZzogNDZweDtcbiRhZG1pbi1zaWRlYmFyLXdpZHRoOiAxNjBweDtcbiRhZG1pbi1zaWRlYmFyLXdpZHRoLWJpZzogMTkwcHg7XG4kYWRtaW4tc2lkZWJhci13aWR0aC1jb2xsYXBzZWQ6IDM2cHg7XG4kbW9kYWwtbWluLXdpZHRoOiAzNTBweDtcbiRtb2RhbC13aWR0aC1zbWFsbDogMzg0cHg7XG4kbW9kYWwtd2lkdGgtbWVkaXVtOiA1MTJweDtcbiRtb2RhbC13aWR0aC1sYXJnZTogODQwcHg7XG4kc3Bpbm5lci1zaXplOiAxNnB4O1xuJGNhbnZhcy1wYWRkaW5nOiAkZ3JpZC11bml0LTIwO1xuJHBhbGV0dGUtbWF4LWhlaWdodDogMzY4cHg7XG5cbi8qKlxuICogTW9iaWxlIHNwZWNpZmljIHN0eWxlc1xuICovXG4kbW9iaWxlLXRleHQtbWluLWZvbnQtc2l6ZTogMTZweDsgLy8gQW55IGZvbnQgc2l6ZSBiZWxvdyAxNnB4IHdpbGwgY2F1c2UgTW9iaWxlIFNhZmFyaSB0byBcInpvb20gaW5cIi5cblxuLyoqXG4gKiBFZGl0b3Igc3R5bGVzLlxuICovXG5cbiRzaWRlYmFyLXdpZHRoOiAyODBweDtcbiRjb250ZW50LXdpZHRoOiA4NDBweDtcbiR3aWRlLWNvbnRlbnQtd2lkdGg6IDExMDBweDtcbiR3aWRnZXQtYXJlYS13aWR0aDogNzAwcHg7XG4kc2Vjb25kYXJ5LXNpZGViYXItd2lkdGg6IDM1MHB4O1xuJGVkaXRvci1mb250LXNpemU6IDE2cHg7XG4kZGVmYXVsdC1ibG9jay1tYXJnaW46IDI4cHg7IC8vIFRoaXMgdmFsdWUgcHJvdmlkZXMgYSBjb25zaXN0ZW50LCBjb250aWd1b3VzIHNwYWNpbmcgYmV0d2VlbiBibG9ja3MuXG4kdGV4dC1lZGl0b3ItZm9udC1zaXplOiAxNXB4O1xuJGVkaXRvci1saW5lLWhlaWdodDogMS44O1xuJGVkaXRvci1odG1sLWZvbnQ6ICRmb250LWZhbWlseS1tb25vO1xuXG4vKipcbiAqIEJsb2NrICYgRWRpdG9yIFVJLlxuICovXG5cbiRibG9jay10b29sYmFyLWhlaWdodDogJGdyaWQtdW5pdC02MDtcbiRib3JkZXItd2lkdGg6IDFweDtcbiRib3JkZXItd2lkdGgtZm9jdXMtZmFsbGJhY2s6IDJweDsgLy8gVGhpcyBleGlzdHMgYXMgYSBmYWxsYmFjaywgYW5kIGlzIGlkZWFsbHkgb3ZlcnJpZGRlbiBieSB2YXIoLS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXMpIHVubGVzcyBpbiBzb21lIFNBU1MgbWF0aCBjYXNlcy5cbiRib3JkZXItd2lkdGgtdGFiOiAxLjVweDtcbiRoZWxwdGV4dC1mb250LXNpemU6IDEycHg7XG4kcmFkaW8taW5wdXQtc2l6ZTogMTZweDtcbiRyYWRpby1pbnB1dC1zaXplLXNtOiAyNHB4OyAvLyBXaWR0aCAmIGhlaWdodCBmb3Igc21hbGwgdmlld3BvcnRzLlxuXG4vLyBEZXByZWNhdGVkLCBwbGVhc2UgYXZvaWQgdXNpbmcgdGhlc2UuXG4kYmxvY2stcGFkZGluZzogMTRweDsgLy8gVXNlZCB0byBkZWZpbmUgc3BhY2UgYmV0d2VlbiBibG9jayBmb290cHJpbnQgYW5kIHN1cnJvdW5kaW5nIGJvcmRlcnMuXG4kcmFkaXVzLWJsb2NrLXVpOiAkcmFkaXVzLXNtYWxsO1xuJHNoYWRvdy1wb3BvdmVyOiAkZWxldmF0aW9uLXgtc21hbGw7XG4kc2hhZG93LW1vZGFsOiAkZWxldmF0aW9uLWxhcmdlO1xuJGRlZmF1bHQtZm9udC1zaXplOiAkZm9udC1zaXplLW1lZGl1bTtcblxuLyoqXG4gKiBCbG9jayBwYWRkaW5ncy5cbiAqL1xuXG4vLyBQYWRkaW5nIGZvciBibG9ja3Mgd2l0aCBhIGJhY2tncm91bmQgY29sb3IgKGUuZy4gcGFyYWdyYXBoIG9yIGdyb3VwKS5cbiRibG9jay1iZy1wYWRkaW5nLS12OiAxLjI1ZW07XG4kYmxvY2stYmctcGFkZGluZy0taDogMi4zNzVlbTtcblxuXG4vKipcbiAqIFJlYWN0IE5hdGl2ZSBzcGVjaWZpYy5cbiAqIFRoZXNlIHZhcmlhYmxlcyBkbyBub3QgYXBwZWFyIHRvIGJlIHVzZWQgYW55d2hlcmUgZWxzZS5cbiAqL1xuXG4vLyBEaW1lbnNpb25zLlxuJG1vYmlsZS1oZWFkZXItdG9vbGJhci1oZWlnaHQ6IDQ0cHg7XG4kbW9iaWxlLWhlYWRlci10b29sYmFyLWV4cGFuZGVkLWhlaWdodDogNTJweDtcbiRtb2JpbGUtZmxvYXRpbmctdG9vbGJhci1oZWlnaHQ6IDQ0cHg7XG4kbW9iaWxlLWZsb2F0aW5nLXRvb2xiYXItbWFyZ2luOiA4cHg7XG4kbW9iaWxlLWNvbG9yLXN3YXRjaDogNDhweDtcblxuLy8gQmxvY2sgVUkuXG4kbW9iaWxlLWJsb2NrLXRvb2xiYXItaGVpZ2h0OiA0NHB4O1xuJGRpbW1lZC1vcGFjaXR5OiAxO1xuJGJsb2NrLWVkZ2UtdG8tY29udGVudDogMTZweDtcbiRzb2xpZC1ib3JkZXItc3BhY2U6IDEycHg7XG4kZGFzaGVkLWJvcmRlci1zcGFjZTogNnB4O1xuJGJsb2NrLXNlbGVjdGVkLW1hcmdpbjogM3B4O1xuJGJsb2NrLXNlbGVjdGVkLWJvcmRlci13aWR0aDogMXB4O1xuJGJsb2NrLXNlbGVjdGVkLXBhZGRpbmc6IDA7XG4kYmxvY2stc2VsZWN0ZWQtY2hpbGQtbWFyZ2luOiA1cHg7XG4kYmxvY2stc2VsZWN0ZWQtdG8tY29udGVudDogJGJsb2NrLWVkZ2UtdG8tY29udGVudCAtICRibG9jay1zZWxlY3RlZC1tYXJnaW4gLSAkYmxvY2stc2VsZWN0ZWQtYm9yZGVyLXdpZHRoO1xuIiwiLyoqXG4gKiBDb2xvcnNcbiAqL1xuXG4vLyBXb3JkUHJlc3MgZ3JheXMuXG4kYmxhY2s6ICMwMDA7XHRcdFx0Ly8gVXNlIG9ubHkgd2hlbiB5b3UgdHJ1bHkgbmVlZCBwdXJlIGJsYWNrLiBGb3IgVUksIHVzZSAkZ3JheS05MDAuXG4kZ3JheS05MDA6ICMxZTFlMWU7XG4kZ3JheS04MDA6ICMyZjJmMmY7XG4kZ3JheS03MDA6ICM3NTc1NzU7XHRcdC8vIE1lZXRzIDQuNjoxICg0LjU6MSBpcyBtaW5pbXVtKSB0ZXh0IGNvbnRyYXN0IGFnYWluc3Qgd2hpdGUuXG4kZ3JheS02MDA6ICM5NDk0OTQ7XHRcdC8vIE1lZXRzIDM6MSBVSSBvciBsYXJnZSB0ZXh0IGNvbnRyYXN0IGFnYWluc3Qgd2hpdGUuXG4kZ3JheS00MDA6ICNjY2M7XG4kZ3JheS0zMDA6ICNkZGQ7XHRcdC8vIFVzZWQgZm9yIG1vc3QgYm9yZGVycy5cbiRncmF5LTIwMDogI2UwZTBlMDtcdFx0Ly8gVXNlZCBzcGFyaW5nbHkgZm9yIGxpZ2h0IGJvcmRlcnMuXG4kZ3JheS0xMDA6ICNmMGYwZjA7XHRcdC8vIFVzZWQgZm9yIGxpZ2h0IGdyYXkgYmFja2dyb3VuZHMuXG4kd2hpdGU6ICNmZmY7XG5cbi8vIE9wYWNpdGllcyAmIGFkZGl0aW9uYWwgY29sb3JzLlxuJGRhcmstZ3JheS1wbGFjZWhvbGRlcjogcmdiYSgkZ3JheS05MDAsIDAuNjIpO1xuJG1lZGl1bS1ncmF5LXBsYWNlaG9sZGVyOiByZ2JhKCRncmF5LTkwMCwgMC41NSk7XG4kbGlnaHQtZ3JheS1wbGFjZWhvbGRlcjogcmdiYSgkd2hpdGUsIDAuNjUpO1xuXG4vLyBBbGVydCBjb2xvcnMuXG4kYWxlcnQteWVsbG93OiAjZjBiODQ5O1xuJGFsZXJ0LXJlZDogI2NjMTgxODtcbiRhbGVydC1ncmVlbjogIzRhYjg2NjtcblxuLy8gRGVwcmVjYXRlZCwgcGxlYXNlIGF2b2lkIHVzaW5nIHRoZXNlLlxuJGRhcmstdGhlbWUtZm9jdXM6ICR3aGl0ZTtcdC8vIEZvY3VzIGNvbG9yIHdoZW4gdGhlIHRoZW1lIGlzIGRhcmsuXG4iLCIvKipcbiAqIFR5cG9ncmFwaHlcbiAqL1xuXG5AdXNlIFwic2Fzczpjb2xvclwiO1xuQHVzZSBcInNhc3M6bWF0aFwiO1xuQHVzZSBcIi4vdmFyaWFibGVzXCI7XG5AdXNlIFwiLi9jb2xvcnNcIjtcbkB1c2UgXCIuL2JyZWFrcG9pbnRzXCI7XG5AdXNlIFwiLi9mdW5jdGlvbnNcIjtcbkB1c2UgXCIuL2xvbmctY29udGVudC1mYWRlXCI7XG5cbkBtaXhpbiBfdGV4dC1oZWFkaW5nKCkge1xuXHRmb250LWZhbWlseTogdmFyaWFibGVzLiRmb250LWZhbWlseS1oZWFkaW5ncztcblx0Zm9udC13ZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC13ZWlnaHQtbWVkaXVtO1xufVxuXG5AbWl4aW4gX3RleHQtYm9keSgpIHtcblx0Zm9udC1mYW1pbHk6IHZhcmlhYmxlcy4kZm9udC1mYW1pbHktYm9keTtcblx0Zm9udC13ZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC13ZWlnaHQtcmVndWxhcjtcbn1cblxuQG1peGluIGhlYWRpbmctc21hbGwoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWhlYWRpbmcoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS14LXNtYWxsO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LXgtc21hbGw7XG59XG5cbkBtaXhpbiBoZWFkaW5nLW1lZGl1bSgpIHtcblx0QGluY2x1ZGUgX3RleHQtaGVhZGluZygpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLW1lZGl1bTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC1zbWFsbDtcbn1cblxuQG1peGluIGhlYWRpbmctbGFyZ2UoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWhlYWRpbmcoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS1sYXJnZTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC1zbWFsbDtcbn1cblxuQG1peGluIGhlYWRpbmcteC1sYXJnZSgpIHtcblx0QGluY2x1ZGUgX3RleHQtaGVhZGluZygpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLXgtbGFyZ2U7XG5cdGxpbmUtaGVpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtbGluZS1oZWlnaHQtbWVkaXVtO1xufVxuXG5AbWl4aW4gaGVhZGluZy0yeC1sYXJnZSgpIHtcblx0QGluY2x1ZGUgX3RleHQtaGVhZGluZygpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLTJ4LWxhcmdlO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LTJ4LWxhcmdlO1xufVxuXG5AbWl4aW4gYm9keS1zbWFsbCgpIHtcblx0QGluY2x1ZGUgX3RleHQtYm9keSgpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLXNtYWxsO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LXgtc21hbGw7XG59XG5cbkBtaXhpbiBib2R5LW1lZGl1bSgpIHtcblx0QGluY2x1ZGUgX3RleHQtYm9keSgpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLW1lZGl1bTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC1zbWFsbDtcbn1cblxuQG1peGluIGJvZHktbGFyZ2UoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWJvZHkoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS1sYXJnZTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC1tZWRpdW07XG59XG5cbkBtaXhpbiBib2R5LXgtbGFyZ2UoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWJvZHkoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS14LWxhcmdlO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LXgtbGFyZ2U7XG59XG5cbi8qKlxuICogQnJlYWtwb2ludCBtaXhpbnNcbiAqL1xuXG5AbWl4aW4gYnJlYWsteGh1Z2UoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLXhodWdlKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLWh1Z2UoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLWh1Z2UpIH0pIHtcblx0XHRAY29udGVudDtcblx0fVxufVxuXG5AbWl4aW4gYnJlYWstd2lkZSgpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstd2lkZSkgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbkBtaXhpbiBicmVhay14bGFyZ2UoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLXhsYXJnZSkgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbkBtaXhpbiBicmVhay1sYXJnZSgpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbGFyZ2UpIH0pIHtcblx0XHRAY29udGVudDtcblx0fVxufVxuXG5AbWl4aW4gYnJlYWstbWVkaXVtKCkge1xuXHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay1tZWRpdW0pIH0pIHtcblx0XHRAY29udGVudDtcblx0fVxufVxuXG5AbWl4aW4gYnJlYWstc21hbGwoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLXNtYWxsKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLW1vYmlsZSgpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbW9iaWxlKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLXpvb21lZC1pbigpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstem9vbWVkLWluKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuLyoqXG4gKiBGb2N1cyBzdHlsZXMuXG4gKi9cblxuQG1peGluIGJsb2NrLXRvb2xiYXItYnV0dG9uLXN0eWxlX19mb2N1cygpIHtcblx0Ym94LXNoYWRvdzogaW5zZXQgMCAwIDAgdmFyaWFibGVzLiRib3JkZXItd2lkdGggY29sb3JzLiR3aGl0ZSwgMCAwIDAgdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcik7XG5cblx0Ly8gV2luZG93cyBIaWdoIENvbnRyYXN0IG1vZGUgd2lsbCBzaG93IHRoaXMgb3V0bGluZSwgYnV0IG5vdCB0aGUgYm94LXNoYWRvdy5cblx0b3V0bGluZTogMnB4IHNvbGlkIHRyYW5zcGFyZW50O1xufVxuXG4vLyBUYWJzLCBJbnB1dHMsIFNxdWFyZSBidXR0b25zLlxuQG1peGluIGlucHV0LXN0eWxlX19uZXV0cmFsKCkge1xuXHRib3gtc2hhZG93OiAwIDAgMCB0cmFuc3BhcmVudDtcblx0Ym9yZGVyLXJhZGl1czogdmFyaWFibGVzLiRyYWRpdXMtc21hbGw7XG5cdGJvcmRlcjogdmFyaWFibGVzLiRib3JkZXItd2lkdGggc29saWQgY29sb3JzLiRncmF5LTYwMDtcblxuXHRAbWVkaWEgbm90IChwcmVmZXJzLXJlZHVjZWQtbW90aW9uKSB7XG5cdFx0dHJhbnNpdGlvbjogYm94LXNoYWRvdyAwLjFzIGxpbmVhcjtcblx0fVxufVxuXG5cbkBtaXhpbiBpbnB1dC1zdHlsZV9fZm9jdXMoJGFjY2VudC1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpKSB7XG5cdGJvcmRlci1jb2xvcjogJGFjY2VudC1jb2xvcjtcblx0Ly8gRXhwYW5kIHRoZSBkZWZhdWx0IGJvcmRlciBmb2N1cyBzdHlsZSBieSAuNXB4IHRvIGJlIGEgdG90YWwgb2YgMS41cHguXG5cdGJveC1zaGFkb3c6IDAgMCAwIDAuNXB4ICRhY2NlbnQtY29sb3I7XG5cdC8vIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlIHdpbGwgc2hvdyB0aGlzIG91dGxpbmUsIGJ1dCBub3QgdGhlIGJveC1zaGFkb3cuXG5cdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcbn1cblxuQG1peGluIGJ1dHRvbi1zdHlsZV9fZm9jdXMoKSB7XG5cdGJveC1zaGFkb3c6IDAgMCAwIHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXG5cdC8vIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlIHdpbGwgc2hvdyB0aGlzIG91dGxpbmUsIGJ1dCBub3QgdGhlIGJveC1zaGFkb3cuXG5cdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcbn1cblxuXG5AbWl4aW4gYnV0dG9uLXN0eWxlLW91dHNldF9fZm9jdXMoJGZvY3VzLWNvbG9yKSB7XG5cdGJveC1zaGFkb3c6IDAgMCAwIHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgY29sb3JzLiR3aGl0ZSwgMCAwIDAgY2FsYygyICogdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSkgJGZvY3VzLWNvbG9yO1xuXG5cdC8vIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlIHdpbGwgc2hvdyB0aGlzIG91dGxpbmUsIGJ1dCBub3QgdGhlIGJveC1zaGFkb3cuXG5cdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcblx0b3V0bGluZS1vZmZzZXQ6IDJweDtcbn1cblxuXG4vKipcbiAqIEFwcGxpZXMgZWRpdG9yIGxlZnQgcG9zaXRpb24gdG8gdGhlIHNlbGVjdG9yIHBhc3NlZCBhcyBhcmd1bWVudFxuICovXG5cbkBtaXhpbiBlZGl0b3ItbGVmdCgkc2VsZWN0b3IpIHtcblx0I3skc2VsZWN0b3J9IHsgLyogU2V0IGxlZnQgcG9zaXRpb24gd2hlbiBhdXRvLWZvbGQgaXMgbm90IG9uIHRoZSBib2R5IGVsZW1lbnQuICovXG5cdFx0bGVmdDogMDtcblxuXHRcdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLW1lZGl1bSArIDEpIH0pIHtcblx0XHRcdGxlZnQ6IHZhcmlhYmxlcy4kYWRtaW4tc2lkZWJhci13aWR0aDtcblx0XHR9XG5cdH1cblxuXHQuYXV0by1mb2xkICN7JHNlbGVjdG9yfSB7IC8qIEF1dG8gZm9sZCBpcyB3aGVuIG9uIHNtYWxsZXIgYnJlYWtwb2ludHMsIG5hdiBtZW51IGF1dG8gY29sbGFwc2VzLiAqL1xuXHRcdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLW1lZGl1bSArIDEpIH0pIHtcblx0XHRcdGxlZnQ6IHZhcmlhYmxlcy4kYWRtaW4tc2lkZWJhci13aWR0aC1jb2xsYXBzZWQ7XG5cdFx0fVxuXG5cdFx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbGFyZ2UgKyAxKSB9KSB7XG5cdFx0XHRsZWZ0OiB2YXJpYWJsZXMuJGFkbWluLXNpZGViYXItd2lkdGg7XG5cdFx0fVxuXHR9XG5cblx0LyogU2lkZWJhciBtYW51YWxseSBjb2xsYXBzZWQuICovXG5cdC5mb2xkZWQgI3skc2VsZWN0b3J9IHtcblx0XHRsZWZ0OiAwO1xuXG5cdFx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbWVkaXVtICsgMSkgfSkge1xuXHRcdFx0bGVmdDogdmFyaWFibGVzLiRhZG1pbi1zaWRlYmFyLXdpZHRoLWNvbGxhcHNlZDtcblx0XHR9XG5cdH1cblxuXHRib2R5LmlzLWZ1bGxzY3JlZW4tbW9kZSAjeyRzZWxlY3Rvcn0ge1xuXHRcdGxlZnQ6IDAgIWltcG9ydGFudDtcblx0fVxufVxuXG4vKipcbiAqIFN0eWxlcyB0aGF0IGFyZSByZXVzZWQgdmVyYmF0aW0gaW4gYSBmZXcgcGxhY2VzXG4gKi9cblxuLy8gVGhlc2UgYXJlIGFkZGl0aW9uYWwgc3R5bGVzIGZvciBhbGwgY2FwdGlvbnMsIHdoZW4gdGhlIHRoZW1lIG9wdHMgaW4gdG8gYmxvY2sgc3R5bGVzLlxuQG1peGluIGNhcHRpb24tc3R5bGUoKSB7XG5cdG1hcmdpbi10b3A6IDAuNWVtO1xuXHRtYXJnaW4tYm90dG9tOiAxZW07XG59XG5cbkBtaXhpbiBjYXB0aW9uLXN0eWxlLXRoZW1lKCkge1xuXHRjb2xvcjogIzU1NTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGRlZmF1bHQtZm9udC1zaXplO1xuXHR0ZXh0LWFsaWduOiBjZW50ZXI7XG5cblx0LmlzLWRhcmstdGhlbWUgJiB7XG5cdFx0Y29sb3I6IGNvbG9ycy4kbGlnaHQtZ3JheS1wbGFjZWhvbGRlcjtcblx0fVxufVxuXG4vKipcbiAqIEFsbG93cyB1c2VycyB0byBvcHQtb3V0IG9mIGFuaW1hdGlvbnMgdmlhIE9TLWxldmVsIHByZWZlcmVuY2VzLlxuICovXG5cbkBtaXhpbiByZWR1Y2UtbW90aW9uKCRwcm9wZXJ0eTogXCJcIikge1xuXG5cdEBpZiAkcHJvcGVydHkgPT0gXCJ0cmFuc2l0aW9uXCIge1xuXHRcdEBtZWRpYSAocHJlZmVycy1yZWR1Y2VkLW1vdGlvbjogcmVkdWNlKSB7XG5cdFx0XHR0cmFuc2l0aW9uLWR1cmF0aW9uOiAwcztcblx0XHRcdHRyYW5zaXRpb24tZGVsYXk6IDBzO1xuXHRcdH1cblx0fSBAZWxzZSBpZiAkcHJvcGVydHkgPT0gXCJhbmltYXRpb25cIiB7XG5cdFx0QG1lZGlhIChwcmVmZXJzLXJlZHVjZWQtbW90aW9uOiByZWR1Y2UpIHtcblx0XHRcdGFuaW1hdGlvbi1kdXJhdGlvbjogMW1zO1xuXHRcdFx0YW5pbWF0aW9uLWRlbGF5OiAwcztcblx0XHR9XG5cdH0gQGVsc2Uge1xuXHRcdEBtZWRpYSAocHJlZmVycy1yZWR1Y2VkLW1vdGlvbjogcmVkdWNlKSB7XG5cdFx0XHR0cmFuc2l0aW9uLWR1cmF0aW9uOiAwcztcblx0XHRcdHRyYW5zaXRpb24tZGVsYXk6IDBzO1xuXHRcdFx0YW5pbWF0aW9uLWR1cmF0aW9uOiAxbXM7XG5cdFx0XHRhbmltYXRpb24tZGVsYXk6IDBzO1xuXHRcdH1cblx0fVxufVxuXG5AbWl4aW4gaW5wdXQtY29udHJvbCgkYWNjZW50LWNvbG9yOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcikpIHtcblx0Zm9udC1mYW1pbHk6IHZhcmlhYmxlcy4kZGVmYXVsdC1mb250O1xuXHRwYWRkaW5nOiA2cHggOHB4O1xuXHQvKiBGb250cyBzbWFsbGVyIHRoYW4gMTZweCBjYXVzZXMgbW9iaWxlIHNhZmFyaSB0byB6b29tLiAqL1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kbW9iaWxlLXRleHQtbWluLWZvbnQtc2l6ZTtcblx0LyogT3ZlcnJpZGUgY29yZSBsaW5lLWhlaWdodC4gVG8gYmUgcmV2aWV3ZWQuICovXG5cdGxpbmUtaGVpZ2h0OiBub3JtYWw7XG5cdEBpbmNsdWRlIGlucHV0LXN0eWxlX19uZXV0cmFsKCk7XG5cblx0QGluY2x1ZGUgYnJlYWstc21hbGwge1xuXHRcdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRkZWZhdWx0LWZvbnQtc2l6ZTtcblx0XHQvKiBPdmVycmlkZSBjb3JlIGxpbmUtaGVpZ2h0LiBUbyBiZSByZXZpZXdlZC4gKi9cblx0XHRsaW5lLWhlaWdodDogbm9ybWFsO1xuXHR9XG5cblx0Jjpmb2N1cyB7XG5cdFx0QGluY2x1ZGUgaW5wdXQtc3R5bGVfX2ZvY3VzKCRhY2NlbnQtY29sb3IpO1xuXHR9XG5cblx0Ly8gVXNlIG9wYWNpdHkgdG8gd29yayBpbiB2YXJpb3VzIGVkaXRvciBzdHlsZXMuXG5cdCY6Oi13ZWJraXQtaW5wdXQtcGxhY2Vob2xkZXIge1xuXHRcdGNvbG9yOiBjb2xvcnMuJGRhcmstZ3JheS1wbGFjZWhvbGRlcjtcblx0fVxuXG5cdCY6Oi1tb3otcGxhY2Vob2xkZXIge1xuXHRcdGNvbG9yOiBjb2xvcnMuJGRhcmstZ3JheS1wbGFjZWhvbGRlcjtcblx0fVxuXG5cdCY6LW1zLWlucHV0LXBsYWNlaG9sZGVyIHtcblx0XHRjb2xvcjogY29sb3JzLiRkYXJrLWdyYXktcGxhY2Vob2xkZXI7XG5cdH1cbn1cblxuQG1peGluIGNoZWNrYm94LWNvbnRyb2wge1xuXHRib3JkZXI6IHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoIHNvbGlkIGNvbG9ycy4kZ3JheS05MDA7XG5cdG1hcmdpbi1yaWdodDogdmFyaWFibGVzLiRncmlkLXVuaXQtMTU7XG5cdHRyYW5zaXRpb246IG5vbmU7XG5cdGJvcmRlci1yYWRpdXM6IHZhcmlhYmxlcy4kcmFkaXVzLXNtYWxsO1xuXHRAaW5jbHVkZSBpbnB1dC1jb250cm9sO1xuXG5cdCY6Zm9jdXMge1xuXHRcdGJveC1zaGFkb3c6IDAgMCAwICh2YXJpYWJsZXMuJGJvcmRlci13aWR0aCAqIDIpIGNvbG9ycy4kd2hpdGUsIDAgMCAwICh2YXJpYWJsZXMuJGJvcmRlci13aWR0aCAqIDIgKyB2YXJpYWJsZXMuJGJvcmRlci13aWR0aC1mb2N1cy1mYWxsYmFjaykgdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXG5cdFx0Ly8gT25seSB2aXNpYmxlIGluIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlLlxuXHRcdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcblx0fVxuXG5cdCY6Y2hlY2tlZCB7XG5cdFx0YmFja2dyb3VuZDogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXHRcdGJvcmRlci1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXG5cdFx0Ly8gSGlkZSBkZWZhdWx0IGNoZWNrYm94IHN0eWxlcyBpbiBJRS5cblx0XHQmOjotbXMtY2hlY2sge1xuXHRcdFx0b3BhY2l0eTogMDtcblx0XHR9XG5cdH1cblxuXHQmOmNoZWNrZWQ6OmJlZm9yZSxcblx0JlthcmlhLWNoZWNrZWQ9XCJtaXhlZFwiXTo6YmVmb3JlIHtcblx0XHRtYXJnaW46IC0zcHggLTVweDtcblx0XHRjb2xvcjogY29sb3JzLiR3aGl0ZTtcblxuXHRcdEBpbmNsdWRlIGJyZWFrLW1lZGl1bSgpIHtcblx0XHRcdG1hcmdpbjogLTRweCAwIDAgLTVweDtcblx0XHR9XG5cdH1cblxuXHQmW2FyaWEtY2hlY2tlZD1cIm1peGVkXCJdIHtcblx0XHRiYWNrZ3JvdW5kOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcik7XG5cdFx0Ym9yZGVyLWNvbG9yOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcik7XG5cblx0XHQmOjpiZWZvcmUge1xuXHRcdFx0Ly8gSW5oZXJpdGVkIGZyb20gYGZvcm1zLmNzc2AuXG5cdFx0XHQvLyBTZWU6IGh0dHBzOi8vZ2l0aHViLmNvbS9Xb3JkUHJlc3Mvd29yZHByZXNzLWRldmVsb3AvdHJlZS81LjEuMS9zcmMvd3AtYWRtaW4vY3NzL2Zvcm1zLmNzcyNMMTIyLUwxMzJcblx0XHRcdGNvbnRlbnQ6IFwiXFxmNDYwXCI7XG5cdFx0XHRmbG9hdDogbGVmdDtcblx0XHRcdGRpc3BsYXk6IGlubGluZS1ibG9jaztcblx0XHRcdHZlcnRpY2FsLWFsaWduOiBtaWRkbGU7XG5cdFx0XHR3aWR0aDogMTZweDtcblx0XHRcdC8qIHN0eWxlbGludC1kaXNhYmxlLW5leHQtbGluZSBmb250LWZhbWlseS1uby1taXNzaW5nLWdlbmVyaWMtZmFtaWx5LWtleXdvcmQgLS0gZGFzaGljb25zIGRvbid0IG5lZWQgYSBnZW5lcmljIGZhbWlseSBrZXl3b3JkLiAqL1xuXHRcdFx0Zm9udDogbm9ybWFsIDMwcHgvMSBkYXNoaWNvbnM7XG5cdFx0XHRzcGVhazogbm9uZTtcblx0XHRcdC13ZWJraXQtZm9udC1zbW9vdGhpbmc6IGFudGlhbGlhc2VkO1xuXHRcdFx0LW1vei1vc3gtZm9udC1zbW9vdGhpbmc6IGdyYXlzY2FsZTtcblxuXHRcdFx0QGluY2x1ZGUgYnJlYWstbWVkaXVtKCkge1xuXHRcdFx0XHRmbG9hdDogbm9uZTtcblx0XHRcdFx0Zm9udC1zaXplOiAyMXB4O1xuXHRcdFx0fVxuXHRcdH1cblx0fVxuXG5cdCZbYXJpYS1kaXNhYmxlZD1cInRydWVcIl0sXG5cdCY6ZGlzYWJsZWQge1xuXHRcdGJhY2tncm91bmQ6IGNvbG9ycy4kZ3JheS0xMDA7XG5cdFx0Ym9yZGVyLWNvbG9yOiBjb2xvcnMuJGdyYXktMzAwO1xuXHRcdGN1cnNvcjogZGVmYXVsdDtcblxuXHRcdC8vIE92ZXJyaWRlIHN0eWxlIGluaGVyaXRlZCBmcm9tIHdwLWFkbWluLiBSZXF1aXJlZCB0byBhdm9pZCBkZWdyYWRlZCBhcHBlYXJhbmNlIG9uIGRpZmZlcmVudCBiYWNrZ3JvdW5kcy5cblx0XHRvcGFjaXR5OiAxO1xuXHR9XG59XG5cbkBtaXhpbiByYWRpby1jb250cm9sIHtcblx0Ym9yZGVyOiB2YXJpYWJsZXMuJGJvcmRlci13aWR0aCBzb2xpZCBjb2xvcnMuJGdyYXktOTAwO1xuXHRtYXJnaW4tcmlnaHQ6IHZhcmlhYmxlcy4kZ3JpZC11bml0LTE1O1xuXHR0cmFuc2l0aW9uOiBub25lO1xuXHRib3JkZXItcmFkaXVzOiB2YXJpYWJsZXMuJHJhZGl1cy1yb3VuZDtcblx0d2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZS1zbTtcblx0aGVpZ2h0OiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemUtc207XG5cdG1pbi13aWR0aDogdmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLXNtO1xuXHRtYXgtd2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZS1zbTtcblx0cG9zaXRpb246IHJlbGF0aXZlO1xuXG5cdEBtZWRpYSBub3QgKHByZWZlcnMtcmVkdWNlZC1tb3Rpb24pIHtcblx0XHR0cmFuc2l0aW9uOiBib3gtc2hhZG93IDAuMXMgbGluZWFyO1xuXHR9XG5cblx0QGluY2x1ZGUgYnJlYWstc21hbGwoKSB7XG5cdFx0aGVpZ2h0OiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemU7XG5cdFx0d2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZTtcblx0XHRtaW4td2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZTtcblx0XHRtYXgtd2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZTtcblx0fVxuXG5cdCY6Y2hlY2tlZDo6YmVmb3JlIHtcblx0XHRib3gtc2l6aW5nOiBpbmhlcml0O1xuXHRcdHdpZHRoOiBtYXRoLmRpdih2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemUtc20sIDIpO1xuXHRcdGhlaWdodDogbWF0aC5kaXYodmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLXNtLCAyKTtcblx0XHRwb3NpdGlvbjogYWJzb2x1dGU7XG5cdFx0dG9wOiA1MCU7XG5cdFx0bGVmdDogNTAlO1xuXHRcdHRyYW5zZm9ybTogdHJhbnNsYXRlKC01MCUsIC01MCUpO1xuXHRcdG1hcmdpbjogMDtcblx0XHRiYWNrZ3JvdW5kLWNvbG9yOiBjb2xvcnMuJHdoaXRlO1xuXG5cdFx0Ly8gVGhpcyBib3JkZXIgc2VydmVzIGFzIGEgYmFja2dyb3VuZCBjb2xvciBpbiBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZS5cblx0XHRib3JkZXI6IDRweCBzb2xpZCBjb2xvcnMuJHdoaXRlO1xuXG5cdFx0QGluY2x1ZGUgYnJlYWstc21hbGwoKSB7XG5cdFx0XHR3aWR0aDogbWF0aC5kaXYodmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLCAyKTtcblx0XHRcdGhlaWdodDogbWF0aC5kaXYodmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLCAyKTtcblx0XHR9XG5cdH1cblxuXHQmOmZvY3VzIHtcblx0XHRib3gtc2hhZG93OiAwIDAgMCAodmFyaWFibGVzLiRib3JkZXItd2lkdGggKiAyKSBjb2xvcnMuJHdoaXRlLCAwIDAgMCAodmFyaWFibGVzLiRib3JkZXItd2lkdGggKiAyICsgdmFyaWFibGVzLiRib3JkZXItd2lkdGgtZm9jdXMtZmFsbGJhY2spIHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblxuXHRcdC8vIE9ubHkgdmlzaWJsZSBpbiBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZS5cblx0XHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQ7XG5cdH1cblxuXHQmOmNoZWNrZWQge1xuXHRcdGJhY2tncm91bmQ6IHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblx0XHRib3JkZXI6IG5vbmU7XG5cdH1cbn1cblxuLyoqXG4gKiBSZXNldCBkZWZhdWx0IHN0eWxlcyBmb3IgSmF2YVNjcmlwdCBVSSBiYXNlZCBwYWdlcy5cbiAqIFRoaXMgaXMgYSBXUC1hZG1pbiBhZ25vc3RpYyByZXNldFxuICovXG5cbkBtaXhpbiByZXNldCB7XG5cdGJveC1zaXppbmc6IGJvcmRlci1ib3g7XG5cblx0Kixcblx0Kjo6YmVmb3JlLFxuXHQqOjphZnRlciB7XG5cdFx0Ym94LXNpemluZzogaW5oZXJpdDtcblx0fVxufVxuXG5AbWl4aW4gbGluay1yZXNldCB7XG5cdCY6Zm9jdXMge1xuXHRcdGNvbG9yOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvci0tcmdiKTtcblx0XHRib3gtc2hhZG93OiAwIDAgMCB2YXIoLS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXMpIHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yLCAjMDA3Y2JhKTtcblx0XHRib3JkZXItcmFkaXVzOiB2YXJpYWJsZXMuJHJhZGl1cy1zbWFsbDtcblx0fVxufVxuXG4vLyBUaGUgZWRpdG9yIGlucHV0IHJlc2V0IHdpdGggaW5jcmVhc2VkIHNwZWNpZmljaXR5IHRvIGF2b2lkIHRoZW1lIHN0eWxlcyBibGVlZGluZyBpbi5cbkBtaXhpbiBlZGl0b3ItaW5wdXQtcmVzZXQoKSB7XG5cdGZvbnQtZmFtaWx5OiB2YXJpYWJsZXMuJGVkaXRvci1odG1sLWZvbnQgIWltcG9ydGFudDtcblx0Y29sb3I6IGNvbG9ycy4kZ3JheS05MDAgIWltcG9ydGFudDtcblx0YmFja2dyb3VuZDogY29sb3JzLiR3aGl0ZSAhaW1wb3J0YW50O1xuXHRwYWRkaW5nOiB2YXJpYWJsZXMuJGdyaWQtdW5pdC0xNSAhaW1wb3J0YW50O1xuXHRib3JkZXI6IHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoIHNvbGlkIGNvbG9ycy4kZ3JheS05MDAgIWltcG9ydGFudDtcblx0Ym94LXNoYWRvdzogbm9uZSAhaW1wb3J0YW50O1xuXHRib3JkZXItcmFkaXVzOiB2YXJpYWJsZXMuJHJhZGl1cy1zbWFsbCAhaW1wb3J0YW50O1xuXG5cdC8vIEZvbnRzIHNtYWxsZXIgdGhhbiAxNnB4IGNhdXNlcyBtb2JpbGUgc2FmYXJpIHRvIHpvb20uXG5cdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRtb2JpbGUtdGV4dC1taW4tZm9udC1zaXplICFpbXBvcnRhbnQ7XG5cdEBpbmNsdWRlIGJyZWFrLXNtYWxsIHtcblx0XHRmb250LXNpemU6IHZhcmlhYmxlcy4kZGVmYXVsdC1mb250LXNpemUgIWltcG9ydGFudDtcblx0fVxuXG5cdCY6Zm9jdXMge1xuXHRcdGJvcmRlci1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpICFpbXBvcnRhbnQ7XG5cdFx0Ym94LXNoYWRvdzogMCAwIDAgKHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoLWZvY3VzLWZhbGxiYWNrIC0gdmFyaWFibGVzLiRib3JkZXItd2lkdGgpIHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKSAhaW1wb3J0YW50O1xuXG5cdFx0Ly8gV2luZG93cyBIaWdoIENvbnRyYXN0IG1vZGUgd2lsbCBzaG93IHRoaXMgb3V0bGluZSwgYnV0IG5vdCB0aGUgYm94LXNoYWRvdy5cblx0XHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQgIWltcG9ydGFudDtcblx0fVxufVxuXG4vKipcbiAqIFJlc2V0IHRoZSBXUCBBZG1pbiBwYWdlIHN0eWxlcyBmb3IgR3V0ZW5iZXJnLWxpa2UgcGFnZXMuXG4gKi9cblxuQG1peGluIHdwLWFkbWluLXJlc2V0KCAkY29udGVudC1jb250YWluZXIgKSB7XG5cdGJhY2tncm91bmQ6IGNvbG9ycy4kd2hpdGU7XG5cblx0I3dwY29udGVudCB7XG5cdFx0cGFkZGluZy1sZWZ0OiAwO1xuXHR9XG5cblx0I3dwYm9keS1jb250ZW50IHtcblx0XHRwYWRkaW5nLWJvdHRvbTogMDtcblx0fVxuXG5cdC8qIFdlIGhpZGUgbGVnYWN5IG5vdGljZXMgaW4gR3V0ZW5iZXJnIEJhc2VkIFBhZ2VzLCBiZWNhdXNlIHRoZXkgd2VyZSBub3QgZGVzaWduZWQgaW4gYSB3YXkgdGhhdCBzY2FsZWQgd2VsbC5cblx0ICAgUGx1Z2lucyBjYW4gdXNlIEd1dGVuYmVyZyBub3RpY2VzIGlmIHRoZXkgbmVlZCB0byBwYXNzIG9uIGluZm9ybWF0aW9uIHRvIHRoZSB1c2VyIHdoZW4gdGhleSBhcmUgZWRpdGluZy4gKi9cblx0I3dwYm9keS1jb250ZW50ID4gZGl2Om5vdCgjeyAkY29udGVudC1jb250YWluZXIgfSk6bm90KCNzY3JlZW4tbWV0YSkge1xuXHRcdGRpc3BsYXk6IG5vbmU7XG5cdH1cblxuXHQjd3Bmb290ZXIge1xuXHRcdGRpc3BsYXk6IG5vbmU7XG5cdH1cblxuXHQuYTExeS1zcGVhay1yZWdpb24ge1xuXHRcdGxlZnQ6IC0xcHg7XG5cdFx0dG9wOiAtMXB4O1xuXHR9XG5cblx0dWwjYWRtaW5tZW51IGEud3AtaGFzLWN1cnJlbnQtc3VibWVudTo6YWZ0ZXIsXG5cdHVsI2FkbWlubWVudSA+IGxpLmN1cnJlbnQgPiBhLmN1cnJlbnQ6OmFmdGVyIHtcblx0XHRib3JkZXItcmlnaHQtY29sb3I6IGNvbG9ycy4kd2hpdGU7XG5cdH1cblxuXHQubWVkaWEtZnJhbWUgc2VsZWN0LmF0dGFjaG1lbnQtZmlsdGVyczpsYXN0LW9mLXR5cGUge1xuXHRcdHdpZHRoOiBhdXRvO1xuXHRcdG1heC13aWR0aDogMTAwJTtcblx0fVxufVxuXG5AbWl4aW4gYWRtaW4tc2NoZW1lKCRjb2xvci1wcmltYXJ5KSB7XG5cdC8vIERlZmluZSBSR0IgZXF1aXZhbGVudHMgZm9yIHVzZSBpbiByZ2JhIGZ1bmN0aW9uLlxuXHQvLyBIZXhhZGVjaW1hbCBjc3MgdmFycyBkbyBub3Qgd29yayBpbiB0aGUgcmdiYSBmdW5jdGlvbi5cblx0LS13cC1hZG1pbi10aGVtZS1jb2xvcjogI3skY29sb3ItcHJpbWFyeX07XG5cdC0td3AtYWRtaW4tdGhlbWUtY29sb3ItLXJnYjogI3tmdW5jdGlvbnMuaGV4LXRvLXJnYigkY29sb3ItcHJpbWFyeSl9O1xuXHQvLyBEYXJrZXIgc2hhZGVzLlxuXHQtLXdwLWFkbWluLXRoZW1lLWNvbG9yLWRhcmtlci0xMDogI3tjb2xvci5hZGp1c3QoJGNvbG9yLXByaW1hcnksICRsaWdodG5lc3M6IC01JSl9O1xuXHQtLXdwLWFkbWluLXRoZW1lLWNvbG9yLWRhcmtlci0xMC0tcmdiOiAje2Z1bmN0aW9ucy5oZXgtdG8tcmdiKGNvbG9yLmFkanVzdCgkY29sb3ItcHJpbWFyeSwgJGxpZ2h0bmVzczogLTUlKSl9O1xuXHQtLXdwLWFkbWluLXRoZW1lLWNvbG9yLWRhcmtlci0yMDogI3tjb2xvci5hZGp1c3QoJGNvbG9yLXByaW1hcnksICRsaWdodG5lc3M6IC0xMCUpfTtcblx0LS13cC1hZG1pbi10aGVtZS1jb2xvci1kYXJrZXItMjAtLXJnYjogI3tmdW5jdGlvbnMuaGV4LXRvLXJnYihjb2xvci5hZGp1c3QoJGNvbG9yLXByaW1hcnksICRsaWdodG5lc3M6IC0xMCUpKX07XG5cblx0Ly8gRm9jdXMgc3R5bGUgd2lkdGguXG5cdC8vIEF2b2lkIHJvdW5kaW5nIGlzc3VlcyBieSBzaG93aW5nIGEgd2hvbGUgMnB4IGZvciAxeCBzY3JlZW5zLCBhbmQgMS41cHggb24gaGlnaCByZXNvbHV0aW9uIHNjcmVlbnMuXG5cdC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzOiAycHg7XG5cdEBtZWRpYSAoIC13ZWJraXQtbWluLWRldmljZS1waXhlbC1yYXRpbzogMiksIChtaW4tcmVzb2x1dGlvbjogMTkyZHBpKSB7XG5cdFx0LS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXM6IDEuNXB4O1xuXHR9XG59XG5cbkBtaXhpbiB3b3JkcHJlc3MtYWRtaW4tc2NoZW1lcygpIHtcblx0Ym9keS5hZG1pbi1jb2xvci1saWdodCB7XG5cdFx0QGluY2x1ZGUgYWRtaW4tc2NoZW1lKCMwMDg1YmEpO1xuXHR9XG5cblx0Ym9keS5hZG1pbi1jb2xvci1tb2Rlcm4ge1xuXHRcdEBpbmNsdWRlIGFkbWluLXNjaGVtZSgjMzg1OGU5KTtcblx0fVxuXG5cdGJvZHkuYWRtaW4tY29sb3ItYmx1ZSB7XG5cdFx0QGluY2x1ZGUgYWRtaW4tc2NoZW1lKCMwOTY0ODQpO1xuXHR9XG5cblx0Ym9keS5hZG1pbi1jb2xvci1jb2ZmZWUge1xuXHRcdEBpbmNsdWRlIGFkbWluLXNjaGVtZSgjNDY0MDNjKTtcblx0fVxuXG5cdGJvZHkuYWRtaW4tY29sb3ItZWN0b3BsYXNtIHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoIzUyM2Y2ZCk7XG5cdH1cblxuXHRib2R5LmFkbWluLWNvbG9yLW1pZG5pZ2h0IHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoI2UxNGQ0Myk7XG5cdH1cblxuXHRib2R5LmFkbWluLWNvbG9yLW9jZWFuIHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoIzYyN2M4Myk7XG5cdH1cblxuXHRib2R5LmFkbWluLWNvbG9yLXN1bnJpc2Uge1xuXHRcdEBpbmNsdWRlIGFkbWluLXNjaGVtZSgjZGQ4MjNiKTtcblx0fVxufVxuXG4vLyBEZXByZWNhdGVkIGZyb20gVUksIGtlcHQgZm9yIGJhY2stY29tcGF0LlxuQG1peGluIGJhY2tncm91bmQtY29sb3JzLWRlcHJlY2F0ZWQoKSB7XG5cdC5oYXMtdmVyeS1saWdodC1ncmF5LWJhY2tncm91bmQtY29sb3Ige1xuXHRcdGJhY2tncm91bmQtY29sb3I6ICNlZWU7XG5cdH1cblxuXHQuaGFzLXZlcnktZGFyay1ncmF5LWJhY2tncm91bmQtY29sb3Ige1xuXHRcdGJhY2tncm91bmQtY29sb3I6ICMzMTMxMzE7XG5cdH1cbn1cblxuLy8gRGVwcmVjYXRlZCBmcm9tIFVJLCBrZXB0IGZvciBiYWNrLWNvbXBhdC5cbkBtaXhpbiBmb3JlZ3JvdW5kLWNvbG9ycy1kZXByZWNhdGVkKCkge1xuXHQuaGFzLXZlcnktbGlnaHQtZ3JheS1jb2xvciB7XG5cdFx0Y29sb3I6ICNlZWU7XG5cdH1cblxuXHQuaGFzLXZlcnktZGFyay1ncmF5LWNvbG9yIHtcblx0XHRjb2xvcjogIzMxMzEzMTtcblx0fVxufVxuXG4vLyBEZXByZWNhdGVkIGZyb20gVUksIGtlcHQgZm9yIGJhY2stY29tcGF0LlxuQG1peGluIGdyYWRpZW50LWNvbG9ycy1kZXByZWNhdGVkKCkge1xuXHQvLyBPdXIgY2xhc3NlcyB1c2VzIHRoZSBzYW1lIHZhbHVlcyB3ZSBzZXQgZm9yIGdyYWRpZW50IHZhbHVlIGF0dHJpYnV0ZXMuXG5cblx0Lyogc3R5bGVsaW50LWRpc2FibGUgQHN0eWxpc3RpYy9mdW5jdGlvbi1jb21tYS1zcGFjZS1hZnRlciAtLSBXZSBjYW4gbm90IHVzZSBzcGFjaW5nIGJlY2F1c2Ugb2YgV1AgbXVsdGkgc2l0ZSBrc2VzIHJ1bGUuICovXG5cdC5oYXMtdml2aWQtZ3JlZW4tY3lhbi10by12aXZpZC1jeWFuLWJsdWUtZ3JhZGllbnQtYmFja2dyb3VuZCB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KDEzNWRlZyxyZ2JhKDAsMjA4LDEzMiwxKSAwJSxyZ2JhKDYsMTQ3LDIyNywxKSAxMDAlKTtcblx0fVxuXG5cdC5oYXMtcHVycGxlLWNydXNoLWdyYWRpZW50LWJhY2tncm91bmQge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCgxMzVkZWcscmdiKDUyLDIyNiwyMjgpIDAlLHJnYig3MSwzMywyNTEpIDUwJSxyZ2IoMTcxLDI5LDI1NCkgMTAwJSk7XG5cdH1cblxuXHQuaGFzLWhhenktZGF3bi1ncmFkaWVudC1iYWNrZ3JvdW5kIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQoMTM1ZGVnLHJnYigyNTAsMTcyLDE2OCkgMCUscmdiKDIxOCwyMDgsMjM2KSAxMDAlKTtcblx0fVxuXG5cdC5oYXMtc3ViZHVlZC1vbGl2ZS1ncmFkaWVudC1iYWNrZ3JvdW5kIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQoMTM1ZGVnLHJnYigyNTAsMjUwLDIyNSkgMCUscmdiKDEwMywxNjYsMTEzKSAxMDAlKTtcblx0fVxuXG5cdC5oYXMtYXRvbWljLWNyZWFtLWdyYWRpZW50LWJhY2tncm91bmQge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCgxMzVkZWcscmdiKDI1MywyMTUsMTU0KSAwJSxyZ2IoMCw3NCw4OSkgMTAwJSk7XG5cdH1cblxuXHQuaGFzLW5pZ2h0c2hhZGUtZ3JhZGllbnQtYmFja2dyb3VuZCB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KDEzNWRlZyxyZ2IoNTEsOSwxMDQpIDAlLHJnYig0OSwyMDUsMjA3KSAxMDAlKTtcblx0fVxuXG5cdC5oYXMtbWlkbmlnaHQtZ3JhZGllbnQtYmFja2dyb3VuZCB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KDEzNWRlZyxyZ2IoMiwzLDEyOSkgMCUscmdiKDQwLDExNiwyNTIpIDEwMCUpO1xuXHR9XG5cdC8qIHN0eWxlbGludC1lbmFibGUgQHN0eWxpc3RpYy9mdW5jdGlvbi1jb21tYS1zcGFjZS1hZnRlciAqL1xufVxuXG5AbWl4aW4gY3VzdG9tLXNjcm9sbGJhcnMtb24taG92ZXIoJGhhbmRsZS1jb2xvciwgJGhhbmRsZS1jb2xvci1ob3Zlcikge1xuXG5cdC8vIFdlYktpdFxuXHQmOjotd2Via2l0LXNjcm9sbGJhciB7XG5cdFx0d2lkdGg6IDEycHg7XG5cdFx0aGVpZ2h0OiAxMnB4O1xuXHR9XG5cdCY6Oi13ZWJraXQtc2Nyb2xsYmFyLXRyYWNrIHtcblx0XHRiYWNrZ3JvdW5kLWNvbG9yOiB0cmFuc3BhcmVudDtcblx0fVxuXHQmOjotd2Via2l0LXNjcm9sbGJhci10aHVtYiB7XG5cdFx0YmFja2dyb3VuZC1jb2xvcjogJGhhbmRsZS1jb2xvcjtcblx0XHRib3JkZXItcmFkaXVzOiA4cHg7XG5cdFx0Ym9yZGVyOiAzcHggc29saWQgdHJhbnNwYXJlbnQ7XG5cdFx0YmFja2dyb3VuZC1jbGlwOiBwYWRkaW5nLWJveDtcblx0fVxuXHQmOmhvdmVyOjotd2Via2l0LXNjcm9sbGJhci10aHVtYiwgLy8gVGhpcyBuZWVkcyBzcGVjaWZpY2l0eS5cblx0Jjpmb2N1czo6LXdlYmtpdC1zY3JvbGxiYXItdGh1bWIsXG5cdCY6Zm9jdXMtd2l0aGluOjotd2Via2l0LXNjcm9sbGJhci10aHVtYiB7XG5cdFx0YmFja2dyb3VuZC1jb2xvcjogJGhhbmRsZS1jb2xvci1ob3Zlcjtcblx0fVxuXG5cdC8vIEZpcmVmb3ggMTA5KyBhbmQgQ2hyb21lIDExMStcblx0c2Nyb2xsYmFyLXdpZHRoOiB0aGluO1xuXHRzY3JvbGxiYXItZ3V0dGVyOiBzdGFibGUgYm90aC1lZGdlcztcblx0c2Nyb2xsYmFyLWNvbG9yOiAkaGFuZGxlLWNvbG9yIHRyYW5zcGFyZW50OyAvLyBTeW50YXgsIFwiZGFya1wiLCBcImxpZ2h0XCIsIG9yIFwiI2hhbmRsZS1jb2xvciAjdHJhY2stY29sb3JcIlxuXG5cdCY6aG92ZXIsXG5cdCY6Zm9jdXMsXG5cdCY6Zm9jdXMtd2l0aGluIHtcblx0XHRzY3JvbGxiYXItY29sb3I6ICRoYW5kbGUtY29sb3ItaG92ZXIgdHJhbnNwYXJlbnQ7XG5cdH1cblxuXHQvLyBOZWVkZWQgdG8gZml4IGEgU2FmYXJpIHJlbmRlcmluZyBpc3N1ZS5cblx0d2lsbC1jaGFuZ2U6IHRyYW5zZm9ybTtcblxuXHQvLyBBbHdheXMgc2hvdyBzY3JvbGxiYXIgb24gTW9iaWxlIGRldmljZXMuXG5cdEBtZWRpYSAoaG92ZXI6IG5vbmUpIHtcblx0XHQmIHtcblx0XHRcdHNjcm9sbGJhci1jb2xvcjogJGhhbmRsZS1jb2xvci1ob3ZlciB0cmFuc3BhcmVudDtcblx0XHR9XG5cdH1cbn1cblxuQG1peGluIHNlbGVjdGVkLWJsb2NrLW91dGxpbmUoJHdpZHRoUmF0aW86IDEpIHtcblx0b3V0bGluZS1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXHRvdXRsaW5lLXN0eWxlOiBzb2xpZDtcblx0b3V0bGluZS13aWR0aDogY2FsYygjeyR3aWR0aFJhdGlvfSAqICh2YXIoLS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXMpIC8gdmFyKC0td3AtYmxvY2stZWRpdG9yLWlmcmFtZS16b29tLW91dC1zY2FsZSwgMSkpKTtcblx0b3V0bGluZS1vZmZzZXQ6IGNhbGMoI3skd2lkdGhSYXRpb30gKiAoKC0xICogdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSApIC8gdmFyKC0td3AtYmxvY2stZWRpdG9yLWlmcmFtZS16b29tLW91dC1zY2FsZSwgMSkpKTtcbn1cblxuQG1peGluIHNlbGVjdGVkLWJsb2NrLWZvY3VzKCR3aWR0aFJhdGlvOiAxKSB7XG5cdGNvbnRlbnQ6IFwiXCI7XG5cdHBvc2l0aW9uOiBhYnNvbHV0ZTtcblx0cG9pbnRlci1ldmVudHM6IG5vbmU7XG5cdHRvcDogMDtcblx0cmlnaHQ6IDA7XG5cdGJvdHRvbTogMDtcblx0bGVmdDogMDtcblx0QGluY2x1ZGUgc2VsZWN0ZWQtYmxvY2stb3V0bGluZSgkd2lkdGhSYXRpbyk7XG59XG4iLCIvKipcbiAqIEJyZWFrcG9pbnRzICYgTWVkaWEgUXVlcmllc1xuICovXG5cbi8vIE1vc3QgdXNlZCBicmVha3BvaW50c1xuJGJyZWFrLXhodWdlOiAxOTIwcHg7XG4kYnJlYWstaHVnZTogMTQ0MHB4O1xuJGJyZWFrLXdpZGU6IDEyODBweDtcbiRicmVhay14bGFyZ2U6IDEwODBweDtcbiRicmVhay1sYXJnZTogOTYwcHg7XHQvLyBhZG1pbiBzaWRlYmFyIGF1dG8gZm9sZHNcbiRicmVhay1tZWRpdW06IDc4MnB4O1x0Ly8gYWRtaW5iYXIgZ29lcyBiaWdcbiRicmVhay1zbWFsbDogNjAwcHg7XG4kYnJlYWstbW9iaWxlOiA0ODBweDtcbiRicmVhay16b29tZWQtaW46IDI4MHB4O1xuXG4vLyBBbGwgbWVkaWEgcXVlcmllcyBjdXJyZW50bHkgaW4gV29yZFByZXNzOlxuLy9cbi8vIG1pbi13aWR0aDogMjAwMHB4XG4vLyBtaW4td2lkdGg6IDE2ODBweFxuLy8gbWluLXdpZHRoOiAxMjUwcHhcbi8vIG1heC13aWR0aDogMTEyMHB4ICpcbi8vIG1heC13aWR0aDogMTAwMHB4XG4vLyBtaW4td2lkdGg6IDc2OXB4IGFuZCBtYXgtd2lkdGg6IDEwMDBweFxuLy8gbWF4LXdpZHRoOiA5NjBweCAqXG4vLyBtYXgtd2lkdGg6IDkwMHB4XG4vLyBtYXgtd2lkdGg6IDg1MHB4XG4vLyBtaW4td2lkdGg6IDgwMHB4IGFuZCBtYXgtd2lkdGg6IDE0OTlweFxuLy8gbWF4LXdpZHRoOiA4MDBweFxuLy8gbWF4LXdpZHRoOiA3OTlweFxuLy8gbWF4LXdpZHRoOiA3ODJweCAqXG4vLyBtYXgtd2lkdGg6IDc2OHB4XG4vLyBtYXgtd2lkdGg6IDY0MHB4ICpcbi8vIG1heC13aWR0aDogNjAwcHggKlxuLy8gbWF4LXdpZHRoOiA1MjBweFxuLy8gbWF4LXdpZHRoOiA1MDBweFxuLy8gbWF4LXdpZHRoOiA0ODBweCAqXG4vLyBtYXgtd2lkdGg6IDQwMHB4ICpcbi8vIG1heC13aWR0aDogMzgwcHhcbi8vIG1heC13aWR0aDogMzIwcHggKlxuLy9cbi8vIFRob3NlIG1hcmtlZCAqIHNlZW0gdG8gYmUgbW9yZSBjb21tb25seSB1c2VkIHRoYW4gdGhlIG90aGVycy5cbi8vIExldCdzIHRyeSBhbmQgdXNlIGFzIGZldyBvZiB0aGVzZSBhcyBwb3NzaWJsZSwgYW5kIGJlIG1pbmRmdWwgYWJvdXQgYWRkaW5nIG5ldyBvbmVzLCBzbyB3ZSBkb24ndCBtYWtlIHRoZSBzaXR1YXRpb24gd29yc2VcbiIsIi8qKlxuKiAgQ29udmVydHMgYSBoZXggdmFsdWUgaW50byB0aGUgcmdiIGVxdWl2YWxlbnQuXG4qXG4qIEBwYXJhbSB7c3RyaW5nfSBoZXggLSB0aGUgaGV4YWRlY2ltYWwgdmFsdWUgdG8gY29udmVydFxuKiBAcmV0dXJuIHtzdHJpbmd9IGNvbW1hIHNlcGFyYXRlZCByZ2IgdmFsdWVzXG4qL1xuXG5AdXNlIFwic2Fzczpjb2xvclwiO1xuQHVzZSBcInNhc3M6bWV0YVwiO1xuXG5AZnVuY3Rpb24gaGV4LXRvLXJnYigkaGV4KSB7XG5cdC8qXG5cdCAqIFRPRE86IGBjb2xvci57cmVkfGdyZWVufGJsdWV9YCB3aWxsIHRyaWdnZXIgYSBkZXByZWNhdGlvbiB3YXJuaW5nIGluIERhcnQgU2Fzcyxcblx0ICogYnV0IHRoZSBTYXNzIHVzZWQgYnkgdGhlIEd1dGVuYmVyZyBwcm9qZWN0IGRvZXNuJ3Qgc3VwcG9ydCBgY29sb3IuY2hhbm5lbCgpYCB5ZXQsXG5cdCAqIHNvIHdlIGNhbid0IG1pZ3JhdGUgdG8gaXQgYXQgdGhpcyB0aW1lLlxuXHQgKiBJbiB0aGUgZnV0dXJlLCBhZnRlciB0aGUgR3V0ZW5iZXJnIHByb2plY3QgaGFzIGJlZW4gZnVsbHkgbWlncmF0ZWQgdG8gRGFydCBTYXNzLFxuXHQgKiBSZW1vdmUgdGhpcyBjb25kaXRpb25hbCBzdGF0ZW1lbnQgYW5kIHVzZSBvbmx5IGBjb2xvci5jaGFubmVsKClgLlxuXHQgKi9cblx0QGlmIG1ldGEuZnVuY3Rpb24tZXhpc3RzKFwiY2hhbm5lbFwiLCBcImNvbG9yXCIpIHtcblx0XHRAcmV0dXJuIGNvbG9yLmNoYW5uZWwoJGhleCwgXCJyZWRcIiksIGNvbG9yLmNoYW5uZWwoJGhleCwgXCJncmVlblwiKSwgY29sb3IuY2hhbm5lbCgkaGV4LCBcImJsdWVcIik7XG5cdH0gQGVsc2Uge1xuXHRcdEByZXR1cm4gY29sb3IucmVkKCRoZXgpLCBjb2xvci5ncmVlbigkaGV4KSwgY29sb3IuYmx1ZSgkaGV4KTtcblx0fVxufVxuIiwiLyoqXG4gKiBMb25nIGNvbnRlbnQgZmFkZSBtaXhpblxuICpcbiAqIENyZWF0ZXMgYSBmYWRpbmcgb3ZlcmxheSB0byBzaWduaWZ5IHRoYXQgdGhlIGNvbnRlbnQgaXMgbG9uZ2VyXG4gKiB0aGFuIHRoZSBzcGFjZSBhbGxvd3MuXG4gKi9cblxuQG1peGluIGxvbmctY29udGVudC1mYWRlKCRkaXJlY3Rpb246IHJpZ2h0LCAkc2l6ZTogMjAlLCAkY29sb3I6ICNmZmYsICRlZGdlOiAwLCAkei1pbmRleDogZmFsc2UpIHtcblx0Y29udGVudDogXCJcIjtcblx0ZGlzcGxheTogYmxvY2s7XG5cdHBvc2l0aW9uOiBhYnNvbHV0ZTtcblx0LXdlYmtpdC10b3VjaC1jYWxsb3V0OiBub25lO1xuXHQtd2Via2l0LXVzZXItc2VsZWN0OiBub25lO1xuXHQta2h0bWwtdXNlci1zZWxlY3Q6IG5vbmU7XG5cdC1tb3otdXNlci1zZWxlY3Q6IG5vbmU7XG5cdC1tcy11c2VyLXNlbGVjdDogbm9uZTtcblx0dXNlci1zZWxlY3Q6IG5vbmU7XG5cdHBvaW50ZXItZXZlbnRzOiBub25lO1xuXG5cdEBpZiAkei1pbmRleCB7XG5cdFx0ei1pbmRleDogJHotaW5kZXg7XG5cdH1cblxuXHRAaWYgJGRpcmVjdGlvbiA9PSBcImJvdHRvbVwiIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQodG8gdG9wLCB0cmFuc3BhcmVudCwgJGNvbG9yIDkwJSk7XG5cdFx0bGVmdDogJGVkZ2U7XG5cdFx0cmlnaHQ6ICRlZGdlO1xuXHRcdHRvcDogJGVkZ2U7XG5cdFx0Ym90dG9tOiBjYWxjKDEwMCUgLSAkc2l6ZSk7XG5cdFx0d2lkdGg6IGF1dG87XG5cdH1cblxuXHRAaWYgJGRpcmVjdGlvbiA9PSBcInRvcFwiIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQodG8gYm90dG9tLCB0cmFuc3BhcmVudCwgJGNvbG9yIDkwJSk7XG5cdFx0dG9wOiBjYWxjKDEwMCUgLSAkc2l6ZSk7XG5cdFx0bGVmdDogJGVkZ2U7XG5cdFx0cmlnaHQ6ICRlZGdlO1xuXHRcdGJvdHRvbTogJGVkZ2U7XG5cdFx0d2lkdGg6IGF1dG87XG5cdH1cblxuXHRAaWYgJGRpcmVjdGlvbiA9PSBcImxlZnRcIiB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KHRvIGxlZnQsIHRyYW5zcGFyZW50LCAkY29sb3IgOTAlKTtcblx0XHR0b3A6ICRlZGdlO1xuXHRcdGxlZnQ6ICRlZGdlO1xuXHRcdGJvdHRvbTogJGVkZ2U7XG5cdFx0cmlnaHQ6IGF1dG87XG5cdFx0d2lkdGg6ICRzaXplO1xuXHRcdGhlaWdodDogYXV0bztcblx0fVxuXG5cdEBpZiAkZGlyZWN0aW9uID09IFwicmlnaHRcIiB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KHRvIHJpZ2h0LCB0cmFuc3BhcmVudCwgJGNvbG9yIDkwJSk7XG5cdFx0dG9wOiAkZWRnZTtcblx0XHRib3R0b206ICRlZGdlO1xuXHRcdHJpZ2h0OiAkZWRnZTtcblx0XHRsZWZ0OiBhdXRvO1xuXHRcdHdpZHRoOiAkc2l6ZTtcblx0XHRoZWlnaHQ6IGF1dG87XG5cdH1cbn1cbiIsIkB1c2UgXCJAd29yZHByZXNzL2Jhc2Utc3R5bGVzL3ZhcmlhYmxlc1wiO1xuQHVzZSBcIkB3b3JkcHJlc3MvYmFzZS1zdHlsZXMvbWl4aW5zXCI7XG5cbi5ib290LW5hdmlnYXRpb24taXRlbS5jb21wb25lbnRzLWl0ZW0ge1xuXHRjb2xvcjogdmFyKC0td3Bkcy1jb2xvci1mZy1pbnRlcmFjdGl2ZS1uZXV0cmFsLCAjMWUxZTFlKTtcblx0cGFkZGluZy1pbmxpbmU6IHZhcmlhYmxlcy4kZ3JpZC11bml0LTA1O1xuXHRwYWRkaW5nLWJsb2NrOiAwO1xuXHRtYXJnaW4taW5saW5lOiB2YXJpYWJsZXMuJGdyaWQtdW5pdC0xNTtcblx0bWFyZ2luLWJsb2NrLWVuZDogdmFyaWFibGVzLiRncmlkLXVuaXQtMDU7XG5cdHdpZHRoOiBjYWxjKDEwMCUgLSB2YXJpYWJsZXMuJGdyaWQtdW5pdC0xNSAqIDIpO1xuXHRib3JkZXI6IG5vbmU7XG5cdG1pbi1oZWlnaHQ6IHZhcmlhYmxlcy4kZ3JpZC11bml0LTQwO1xuXHRkaXNwbGF5OiBmbGV4O1xuXHRhbGlnbi1pdGVtczogY2VudGVyO1xuXHRAaW5jbHVkZSBtaXhpbnMuYm9keS1tZWRpdW0oKTtcblxuXHQuYm9vdC1kcm9wZG93bi1pdGVtX19jaGlsZHJlbiAmIHtcblx0XHRtaW4taGVpZ2h0OiB2YXJpYWJsZXMuJGdyaWQtdW5pdC0zMDtcblx0fVxuXG5cdC8vIFJvdW5kZWQgZm9jdXMgcmluZ1xuXHRib3JkZXItcmFkaXVzOiB2YXIoLS13cGRzLWJvcmRlci1yYWRpdXMtc3VyZmFjZS1zbSwgMnB4KTtcblxuXHQmLmFjdGl2ZSxcblx0Jjpob3Zlcixcblx0Jjpmb2N1cyxcblx0JlthcmlhLWN1cnJlbnQ9XCJ0cnVlXCJdIHtcblx0XHRjb2xvcjogdmFyKC0td3Bkcy1jb2xvci1mZy1pbnRlcmFjdGl2ZS1icmFuZC1hY3RpdmUsICMwMDczYWEpO1xuXHR9XG5cblx0Ji5hY3RpdmUge1xuXHRcdGZvbnQtd2VpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtd2VpZ2h0LW1lZGl1bTtcblx0fVxuXG5cdHN2ZzpsYXN0LWNoaWxkIHtcblx0XHRwYWRkaW5nOiB2YXJpYWJsZXMuJGdyaWQtdW5pdC0wNTtcblx0fVxuXG5cdCZbYXJpYS1jdXJyZW50PVwidHJ1ZVwiXSB7XG5cdFx0Y29sb3I6IHZhcigtLXdwZHMtY29sb3ItZmctaW50ZXJhY3RpdmUtYnJhbmQtYWN0aXZlLCAjMDA3M2FhKTtcblx0XHRmb250LXdlaWdodDogdmFyaWFibGVzLiRmb250LXdlaWdodC1tZWRpdW07XG5cdH1cblxuXHQvLyBNYWtlIHN1cmUgdGhlIGZvY3VzIHN0eWxlIGlzIGRyYXduIG9uIHRvcCBvZiB0aGUgY3VycmVudCBpdGVtIGJhY2tncm91bmQuXG5cdCY6Zm9jdXMtdmlzaWJsZSB7XG5cdFx0dHJhbnNmb3JtOiB0cmFuc2xhdGVaKDApO1xuXHR9XG5cblx0Ji53aXRoLXN1ZmZpeCB7XG5cdFx0cGFkZGluZy1yaWdodDogdmFyaWFibGVzLiRncmlkLXVuaXQtMjA7XG5cdH1cbn1cbiJdfQ== */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css4));
function NavigationItem({
  className,
  icon,
  shouldShowPlaceholder = true,
  children,
  to
}) {
  const isExternal = !String(
    new URL(to, window.location.origin)
  ).startsWith(window.location.origin);
  const content = /* @__PURE__ */ (0, import_jsx_runtime19.jsxs)(import_components6.__experimentalHStack, { justify: "flex-start", spacing: 2, style: { flexGrow: "1" }, children: [
    wrapIcon(icon, shouldShowPlaceholder),
    /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(import_components6.FlexBlock, { children })
  ] });
  if (isExternal) {
    return /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
      import_components6.__experimentalItem,
      {
        as: "a",
        href: to,
        className: clsx_default("boot-navigation-item", className),
        children: content
      }
    );
  }
  return /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
    router_link_item_default,
    {
      to,
      className: clsx_default("boot-navigation-item", className),
      children: content
    }
  );
}

// packages/boot/build-module/components/navigation/drilldown-item/index.js
var import_components7 = __toESM(require_components());
var import_i18n3 = __toESM(require_i18n());
var import_jsx_runtime20 = __toESM(require_jsx_runtime());
function DrilldownItem({
  className,
  id,
  icon,
  shouldShowPlaceholder = true,
  children,
  onNavigate
}) {
  const handleClick = (e) => {
    e.preventDefault();
    onNavigate({ id, direction: "forward" });
  };
  return /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
    import_components7.__experimentalItem,
    {
      className: clsx_default("boot-navigation-item", className),
      onClick: handleClick,
      children: /* @__PURE__ */ (0, import_jsx_runtime20.jsxs)(
        import_components7.__experimentalHStack,
        {
          justify: "flex-start",
          spacing: 2,
          style: { flexGrow: "1" },
          children: [
            wrapIcon(icon, shouldShowPlaceholder),
            /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(import_components7.FlexBlock, { children }),
            /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(import_components7.Icon, { icon: (0, import_i18n3.isRTL)() ? chevron_left_small_default : chevron_right_small_default })
          ]
        }
      )
    }
  );
}

// packages/boot/build-module/components/navigation/dropdown-item/index.js
var import_components8 = __toESM(require_components());
var import_compose = __toESM(require_compose());
var import_data4 = __toESM(require_data());
var import_jsx_runtime21 = __toESM(require_jsx_runtime());
var css5 = `/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
.boot-dropdown-item__children {
  display: flex;
  flex-direction: column;
  padding: 2px;
  margin-block-start: -2px;
  margin-block-end: 2px;
  margin-inline-start: 30px;
  overflow: hidden;
}

.boot-dropdown-item__chevron.is-up {
  transform: rotate(180deg);
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy9ib290L3NyYy9jb21wb25lbnRzL25hdmlnYXRpb24vZHJvcGRvd24taXRlbSIsInNvdXJjZXMiOlsiLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX3ZhcmlhYmxlcy5zY3NzIiwiLi4vLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX2NvbG9ycy5zY3NzIiwic3R5bGUuc2NzcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQ0FBO0FBQUE7QUFBQTtBRFVBO0FBQUE7QUFBQTtBQU9BO0FBQUE7QUFBQTtBQTZCQTtBQUFBO0FBQUE7QUFBQTtBQWlCQTtBQUFBO0FBQUE7QUFXQTtBQUFBO0FBQUE7QUFnQkE7QUFBQTtBQUFBO0FBeUJBO0FBQUE7QUFBQTtBQUtBO0FBQUE7QUFBQTtBQWVBO0FBQUE7QUFBQTtBQW1CQTtBQUFBO0FBQUE7QUFTQTtBQUFBO0FBQUE7QUFBQTtBRWpLQTtFQUNDO0VBQ0E7RUFPQSxTQURzQztFQUV0QztFQUNBLGtCQUhzQztFQUl0QyxxQkFDQztFQUVEOzs7QUFHRDtFQUNDIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBTQ1NTIFZhcmlhYmxlcy5cbiAqXG4gKiBQbGVhc2UgdXNlIHZhcmlhYmxlcyBmcm9tIHRoaXMgc2hlZXQgdG8gZW5zdXJlIGNvbnNpc3RlbmN5IGFjcm9zcyB0aGUgVUkuXG4gKiBEb24ndCBhZGQgdG8gdGhpcyBzaGVldCB1bmxlc3MgeW91J3JlIHByZXR0eSBzdXJlIHRoZSB2YWx1ZSB3aWxsIGJlIHJldXNlZCBpbiBtYW55IHBsYWNlcy5cbiAqIEZvciBleGFtcGxlLCBkb24ndCBhZGQgcnVsZXMgdG8gdGhpcyBzaGVldCB0aGF0IGFmZmVjdCBibG9jayB2aXN1YWxzLiBJdCdzIHB1cmVseSBmb3IgVUkuXG4gKi9cblxuQHVzZSBcIi4vY29sb3JzXCI7XG5cbi8qKlxuICogRm9udHMgJiBiYXNpYyB2YXJpYWJsZXMuXG4gKi9cblxuJGRlZmF1bHQtZm9udDogLWFwcGxlLXN5c3RlbSwgQmxpbmtNYWNTeXN0ZW1Gb250LFwiU2Vnb2UgVUlcIiwgUm9ib3RvLCBPeHlnZW4tU2FucywgVWJ1bnR1LCBDYW50YXJlbGwsXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmOyAvLyBUb2RvOiBkZXByZWNhdGUgaW4gZmF2b3Igb2YgJGZhbWlseSB2YXJpYWJsZXNcbiRkZWZhdWx0LWxpbmUtaGVpZ2h0OiAxLjQ7IC8vIFRvZG86IGRlcHJlY2F0ZSBpbiBmYXZvciBvZiAkbGluZS1oZWlnaHQgdG9rZW5zXG5cbi8qKlxuICogVHlwb2dyYXBoeVxuICovXG5cbi8vIFNpemVzXG4kZm9udC1zaXplLXgtc21hbGw6IDExcHg7XG4kZm9udC1zaXplLXNtYWxsOiAxMnB4O1xuJGZvbnQtc2l6ZS1tZWRpdW06IDEzcHg7XG4kZm9udC1zaXplLWxhcmdlOiAxNXB4O1xuJGZvbnQtc2l6ZS14LWxhcmdlOiAyMHB4O1xuJGZvbnQtc2l6ZS0yeC1sYXJnZTogMzJweDtcblxuLy8gTGluZSBoZWlnaHRzXG4kZm9udC1saW5lLWhlaWdodC14LXNtYWxsOiAxNnB4O1xuJGZvbnQtbGluZS1oZWlnaHQtc21hbGw6IDIwcHg7XG4kZm9udC1saW5lLWhlaWdodC1tZWRpdW06IDI0cHg7XG4kZm9udC1saW5lLWhlaWdodC1sYXJnZTogMjhweDtcbiRmb250LWxpbmUtaGVpZ2h0LXgtbGFyZ2U6IDMycHg7XG4kZm9udC1saW5lLWhlaWdodC0yeC1sYXJnZTogNDBweDtcblxuLy8gV2VpZ2h0c1xuJGZvbnQtd2VpZ2h0LXJlZ3VsYXI6IDQwMDtcbiRmb250LXdlaWdodC1tZWRpdW06IDQ5OTsgLy8gZW5zdXJlcyBmYWxsYmFjayB0byA0MDAgKGluc3RlYWQgb2YgNjAwKVxuXG4vLyBGYW1pbGllc1xuJGZvbnQtZmFtaWx5LWhlYWRpbmdzOiAtYXBwbGUtc3lzdGVtLCBcInN5c3RlbS11aVwiLCBcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLCBcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7XG4kZm9udC1mYW1pbHktYm9keTogLWFwcGxlLXN5c3RlbSwgXCJzeXN0ZW0tdWlcIiwgXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCwgXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmO1xuJGZvbnQtZmFtaWx5LW1vbm86IE1lbmxvLCBDb25zb2xhcywgbW9uYWNvLCBtb25vc3BhY2U7XG5cbi8qKlxuICogR3JpZCBTeXN0ZW0uXG4gKiBodHRwczovL21ha2Uud29yZHByZXNzLm9yZy9kZXNpZ24vMjAxOS8xMC8zMS9wcm9wb3NhbC1hLWNvbnNpc3RlbnQtc3BhY2luZy1zeXN0ZW0tZm9yLXdvcmRwcmVzcy9cbiAqL1xuXG4kZ3JpZC11bml0OiA4cHg7XG4kZ3JpZC11bml0LTA1OiAwLjUgKiAkZ3JpZC11bml0O1x0Ly8gNHB4XG4kZ3JpZC11bml0LTEwOiAxICogJGdyaWQtdW5pdDtcdFx0Ly8gOHB4XG4kZ3JpZC11bml0LTE1OiAxLjUgKiAkZ3JpZC11bml0O1x0Ly8gMTJweFxuJGdyaWQtdW5pdC0yMDogMiAqICRncmlkLXVuaXQ7XHRcdC8vIDE2cHhcbiRncmlkLXVuaXQtMzA6IDMgKiAkZ3JpZC11bml0O1x0XHQvLyAyNHB4XG4kZ3JpZC11bml0LTQwOiA0ICogJGdyaWQtdW5pdDtcdFx0Ly8gMzJweFxuJGdyaWQtdW5pdC01MDogNSAqICRncmlkLXVuaXQ7XHRcdC8vIDQwcHhcbiRncmlkLXVuaXQtNjA6IDYgKiAkZ3JpZC11bml0O1x0XHQvLyA0OHB4XG4kZ3JpZC11bml0LTcwOiA3ICogJGdyaWQtdW5pdDtcdFx0Ly8gNTZweFxuJGdyaWQtdW5pdC04MDogOCAqICRncmlkLXVuaXQ7XHRcdC8vIDY0cHhcblxuLyoqXG4gKiBSYWRpdXMgc2NhbGUuXG4gKi9cblxuJHJhZGl1cy14LXNtYWxsOiAxcHg7ICAgLy8gQXBwbGllZCB0byBlbGVtZW50cyBsaWtlIGJ1dHRvbnMgbmVzdGVkIHdpdGhpbiBwcmltaXRpdmVzIGxpa2UgaW5wdXRzLlxuJHJhZGl1cy1zbWFsbDogMnB4OyAgICAgLy8gQXBwbGllZCB0byBtb3N0IHByaW1pdGl2ZXMuXG4kcmFkaXVzLW1lZGl1bTogNHB4OyAgICAvLyBBcHBsaWVkIHRvIGNvbnRhaW5lcnMgd2l0aCBzbWFsbGVyIHBhZGRpbmcuXG4kcmFkaXVzLWxhcmdlOiA4cHg7ICAgICAvLyBBcHBsaWVkIHRvIGNvbnRhaW5lcnMgd2l0aCBsYXJnZXIgcGFkZGluZy5cbiRyYWRpdXMtZnVsbDogOTk5OXB4OyAgIC8vIEZvciBwaWxscy5cbiRyYWRpdXMtcm91bmQ6IDUwJTsgICAgIC8vIEZvciBjaXJjbGVzIGFuZCBvdmFscy5cblxuLyoqXG4gKiBFbGV2YXRpb24gc2NhbGUuXG4gKi9cblxuLy8gRm9yIHNlY3Rpb25zIGFuZCBjb250YWluZXJzIHRoYXQgZ3JvdXAgcmVsYXRlZCBjb250ZW50IGFuZCBjb250cm9scywgd2hpY2ggbWF5IG92ZXJsYXAgb3RoZXIgY29udGVudC4gRXhhbXBsZTogUHJldmlldyBGcmFtZS5cbiRlbGV2YXRpb24teC1zbWFsbDogMCAxcHggMXB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMyksIDAgMXB4IDJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpLCAwIDNweCAzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKSwgMCA0cHggNHB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMSk7XG5cbi8vIEZvciBjb21wb25lbnRzIHRoYXQgcHJvdmlkZSBjb250ZXh0dWFsIGZlZWRiYWNrIHdpdGhvdXQgYmVpbmcgaW50cnVzaXZlLiBHZW5lcmFsbHkgbm9uLWludGVycnVwdGl2ZS4gRXhhbXBsZTogVG9vbHRpcHMsIFNuYWNrYmFyLlxuJGVsZXZhdGlvbi1zbWFsbDogMCAxcHggMnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNSksIDAgMnB4IDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDZweCA2cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAzKSwgMCA4cHggOHB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMik7XG5cbi8vIEZvciBjb21wb25lbnRzIHRoYXQgb2ZmZXIgYWRkaXRpb25hbCBhY3Rpb25zLiBFeGFtcGxlOiBNZW51cywgQ29tbWFuZCBQYWxldHRlXG4kZWxldmF0aW9uLW1lZGl1bTogMCAycHggM3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNSksIDAgNHB4IDVweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDEycHggMTJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDMpLCAwIDE2cHggMTZweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vLyBGb3IgY29tcG9uZW50cyB0aGF0IGNvbmZpcm0gZGVjaXNpb25zIG9yIGhhbmRsZSBuZWNlc3NhcnkgaW50ZXJydXB0aW9ucy4gRXhhbXBsZTogTW9kYWxzLlxuJGVsZXZhdGlvbi1sYXJnZTogMCA1cHggMTVweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDgpLCAwIDE1cHggMjdweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDcpLCAwIDMwcHggMzZweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDUwcHggNDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vKipcbiAqIERpbWVuc2lvbnMuXG4gKi9cblxuJGljb24tc2l6ZTogMjRweDtcbiRidXR0b24tc2l6ZTogMzZweDtcbiRidXR0b24tc2l6ZS1uZXh0LWRlZmF1bHQtNDBweDogNDBweDsgLy8gdHJhbnNpdGlvbmFyeSB2YXJpYWJsZSBmb3IgbmV4dCBkZWZhdWx0IGJ1dHRvbiBzaXplXG4kYnV0dG9uLXNpemUtc21hbGw6IDI0cHg7XG4kYnV0dG9uLXNpemUtY29tcGFjdDogMzJweDtcbiRoZWFkZXItaGVpZ2h0OiA2NHB4O1xuJHBhbmVsLWhlYWRlci1oZWlnaHQ6ICRncmlkLXVuaXQtNjA7XG4kbmF2LXNpZGViYXItd2lkdGg6IDMwMHB4O1xuJGFkbWluLWJhci1oZWlnaHQ6IDMycHg7XG4kYWRtaW4tYmFyLWhlaWdodC1iaWc6IDQ2cHg7XG4kYWRtaW4tc2lkZWJhci13aWR0aDogMTYwcHg7XG4kYWRtaW4tc2lkZWJhci13aWR0aC1iaWc6IDE5MHB4O1xuJGFkbWluLXNpZGViYXItd2lkdGgtY29sbGFwc2VkOiAzNnB4O1xuJG1vZGFsLW1pbi13aWR0aDogMzUwcHg7XG4kbW9kYWwtd2lkdGgtc21hbGw6IDM4NHB4O1xuJG1vZGFsLXdpZHRoLW1lZGl1bTogNTEycHg7XG4kbW9kYWwtd2lkdGgtbGFyZ2U6IDg0MHB4O1xuJHNwaW5uZXItc2l6ZTogMTZweDtcbiRjYW52YXMtcGFkZGluZzogJGdyaWQtdW5pdC0yMDtcbiRwYWxldHRlLW1heC1oZWlnaHQ6IDM2OHB4O1xuXG4vKipcbiAqIE1vYmlsZSBzcGVjaWZpYyBzdHlsZXNcbiAqL1xuJG1vYmlsZS10ZXh0LW1pbi1mb250LXNpemU6IDE2cHg7IC8vIEFueSBmb250IHNpemUgYmVsb3cgMTZweCB3aWxsIGNhdXNlIE1vYmlsZSBTYWZhcmkgdG8gXCJ6b29tIGluXCIuXG5cbi8qKlxuICogRWRpdG9yIHN0eWxlcy5cbiAqL1xuXG4kc2lkZWJhci13aWR0aDogMjgwcHg7XG4kY29udGVudC13aWR0aDogODQwcHg7XG4kd2lkZS1jb250ZW50LXdpZHRoOiAxMTAwcHg7XG4kd2lkZ2V0LWFyZWEtd2lkdGg6IDcwMHB4O1xuJHNlY29uZGFyeS1zaWRlYmFyLXdpZHRoOiAzNTBweDtcbiRlZGl0b3ItZm9udC1zaXplOiAxNnB4O1xuJGRlZmF1bHQtYmxvY2stbWFyZ2luOiAyOHB4OyAvLyBUaGlzIHZhbHVlIHByb3ZpZGVzIGEgY29uc2lzdGVudCwgY29udGlndW91cyBzcGFjaW5nIGJldHdlZW4gYmxvY2tzLlxuJHRleHQtZWRpdG9yLWZvbnQtc2l6ZTogMTVweDtcbiRlZGl0b3ItbGluZS1oZWlnaHQ6IDEuODtcbiRlZGl0b3ItaHRtbC1mb250OiAkZm9udC1mYW1pbHktbW9ubztcblxuLyoqXG4gKiBCbG9jayAmIEVkaXRvciBVSS5cbiAqL1xuXG4kYmxvY2stdG9vbGJhci1oZWlnaHQ6ICRncmlkLXVuaXQtNjA7XG4kYm9yZGVyLXdpZHRoOiAxcHg7XG4kYm9yZGVyLXdpZHRoLWZvY3VzLWZhbGxiYWNrOiAycHg7IC8vIFRoaXMgZXhpc3RzIGFzIGEgZmFsbGJhY2ssIGFuZCBpcyBpZGVhbGx5IG92ZXJyaWRkZW4gYnkgdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSB1bmxlc3MgaW4gc29tZSBTQVNTIG1hdGggY2FzZXMuXG4kYm9yZGVyLXdpZHRoLXRhYjogMS41cHg7XG4kaGVscHRleHQtZm9udC1zaXplOiAxMnB4O1xuJHJhZGlvLWlucHV0LXNpemU6IDE2cHg7XG4kcmFkaW8taW5wdXQtc2l6ZS1zbTogMjRweDsgLy8gV2lkdGggJiBoZWlnaHQgZm9yIHNtYWxsIHZpZXdwb3J0cy5cblxuLy8gRGVwcmVjYXRlZCwgcGxlYXNlIGF2b2lkIHVzaW5nIHRoZXNlLlxuJGJsb2NrLXBhZGRpbmc6IDE0cHg7IC8vIFVzZWQgdG8gZGVmaW5lIHNwYWNlIGJldHdlZW4gYmxvY2sgZm9vdHByaW50IGFuZCBzdXJyb3VuZGluZyBib3JkZXJzLlxuJHJhZGl1cy1ibG9jay11aTogJHJhZGl1cy1zbWFsbDtcbiRzaGFkb3ctcG9wb3ZlcjogJGVsZXZhdGlvbi14LXNtYWxsO1xuJHNoYWRvdy1tb2RhbDogJGVsZXZhdGlvbi1sYXJnZTtcbiRkZWZhdWx0LWZvbnQtc2l6ZTogJGZvbnQtc2l6ZS1tZWRpdW07XG5cbi8qKlxuICogQmxvY2sgcGFkZGluZ3MuXG4gKi9cblxuLy8gUGFkZGluZyBmb3IgYmxvY2tzIHdpdGggYSBiYWNrZ3JvdW5kIGNvbG9yIChlLmcuIHBhcmFncmFwaCBvciBncm91cCkuXG4kYmxvY2stYmctcGFkZGluZy0tdjogMS4yNWVtO1xuJGJsb2NrLWJnLXBhZGRpbmctLWg6IDIuMzc1ZW07XG5cblxuLyoqXG4gKiBSZWFjdCBOYXRpdmUgc3BlY2lmaWMuXG4gKiBUaGVzZSB2YXJpYWJsZXMgZG8gbm90IGFwcGVhciB0byBiZSB1c2VkIGFueXdoZXJlIGVsc2UuXG4gKi9cblxuLy8gRGltZW5zaW9ucy5cbiRtb2JpbGUtaGVhZGVyLXRvb2xiYXItaGVpZ2h0OiA0NHB4O1xuJG1vYmlsZS1oZWFkZXItdG9vbGJhci1leHBhbmRlZC1oZWlnaHQ6IDUycHg7XG4kbW9iaWxlLWZsb2F0aW5nLXRvb2xiYXItaGVpZ2h0OiA0NHB4O1xuJG1vYmlsZS1mbG9hdGluZy10b29sYmFyLW1hcmdpbjogOHB4O1xuJG1vYmlsZS1jb2xvci1zd2F0Y2g6IDQ4cHg7XG5cbi8vIEJsb2NrIFVJLlxuJG1vYmlsZS1ibG9jay10b29sYmFyLWhlaWdodDogNDRweDtcbiRkaW1tZWQtb3BhY2l0eTogMTtcbiRibG9jay1lZGdlLXRvLWNvbnRlbnQ6IDE2cHg7XG4kc29saWQtYm9yZGVyLXNwYWNlOiAxMnB4O1xuJGRhc2hlZC1ib3JkZXItc3BhY2U6IDZweDtcbiRibG9jay1zZWxlY3RlZC1tYXJnaW46IDNweDtcbiRibG9jay1zZWxlY3RlZC1ib3JkZXItd2lkdGg6IDFweDtcbiRibG9jay1zZWxlY3RlZC1wYWRkaW5nOiAwO1xuJGJsb2NrLXNlbGVjdGVkLWNoaWxkLW1hcmdpbjogNXB4O1xuJGJsb2NrLXNlbGVjdGVkLXRvLWNvbnRlbnQ6ICRibG9jay1lZGdlLXRvLWNvbnRlbnQgLSAkYmxvY2stc2VsZWN0ZWQtbWFyZ2luIC0gJGJsb2NrLXNlbGVjdGVkLWJvcmRlci13aWR0aDtcbiIsIi8qKlxuICogQ29sb3JzXG4gKi9cblxuLy8gV29yZFByZXNzIGdyYXlzLlxuJGJsYWNrOiAjMDAwO1x0XHRcdC8vIFVzZSBvbmx5IHdoZW4geW91IHRydWx5IG5lZWQgcHVyZSBibGFjay4gRm9yIFVJLCB1c2UgJGdyYXktOTAwLlxuJGdyYXktOTAwOiAjMWUxZTFlO1xuJGdyYXktODAwOiAjMmYyZjJmO1xuJGdyYXktNzAwOiAjNzU3NTc1O1x0XHQvLyBNZWV0cyA0LjY6MSAoNC41OjEgaXMgbWluaW11bSkgdGV4dCBjb250cmFzdCBhZ2FpbnN0IHdoaXRlLlxuJGdyYXktNjAwOiAjOTQ5NDk0O1x0XHQvLyBNZWV0cyAzOjEgVUkgb3IgbGFyZ2UgdGV4dCBjb250cmFzdCBhZ2FpbnN0IHdoaXRlLlxuJGdyYXktNDAwOiAjY2NjO1xuJGdyYXktMzAwOiAjZGRkO1x0XHQvLyBVc2VkIGZvciBtb3N0IGJvcmRlcnMuXG4kZ3JheS0yMDA6ICNlMGUwZTA7XHRcdC8vIFVzZWQgc3BhcmluZ2x5IGZvciBsaWdodCBib3JkZXJzLlxuJGdyYXktMTAwOiAjZjBmMGYwO1x0XHQvLyBVc2VkIGZvciBsaWdodCBncmF5IGJhY2tncm91bmRzLlxuJHdoaXRlOiAjZmZmO1xuXG4vLyBPcGFjaXRpZXMgJiBhZGRpdGlvbmFsIGNvbG9ycy5cbiRkYXJrLWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJGdyYXktOTAwLCAwLjYyKTtcbiRtZWRpdW0tZ3JheS1wbGFjZWhvbGRlcjogcmdiYSgkZ3JheS05MDAsIDAuNTUpO1xuJGxpZ2h0LWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJHdoaXRlLCAwLjY1KTtcblxuLy8gQWxlcnQgY29sb3JzLlxuJGFsZXJ0LXllbGxvdzogI2YwYjg0OTtcbiRhbGVydC1yZWQ6ICNjYzE4MTg7XG4kYWxlcnQtZ3JlZW46ICM0YWI4NjY7XG5cbi8vIERlcHJlY2F0ZWQsIHBsZWFzZSBhdm9pZCB1c2luZyB0aGVzZS5cbiRkYXJrLXRoZW1lLWZvY3VzOiAkd2hpdGU7XHQvLyBGb2N1cyBjb2xvciB3aGVuIHRoZSB0aGVtZSBpcyBkYXJrLlxuIiwiQHVzZSBcIkB3b3JkcHJlc3MvYmFzZS1zdHlsZXMvdmFyaWFibGVzXCI7XG5cbi5ib290LWRyb3Bkb3duLWl0ZW1fX2NoaWxkcmVuIHtcblx0ZGlzcGxheTogZmxleDtcblx0ZmxleC1kaXJlY3Rpb246IGNvbHVtbjtcblxuXHQvLyBJbiBvcmRlciB0byBhdm9pZCB0aGUgZm9jdXMgcmluZyBvZiBlYWNoIGxpc3QgaXRlbSBmcm9tIGJlaW5nIGN1dCBvZmYsXG5cdC8vIHdlIGFkZCBwYWRkaW5nIGFyb3VuZCB0aGUgbWVudSBpdGVtcy5cblx0Ly8gQXQgdGhlIHNhbWUgdGltZSwgd2UgdXNlIHRoZSBzYW1lIHZhbHVlIHRvIHR3ZWFrIG1hcmdpbnMgc28gdGhhdFxuXHQvLyB0aGUgaXRlbXMgc3RpbGwgcmV0YWluIHRoZSBzYW1lIHBvc2l0aW9uIGFuZCBmb290cHJpbnQuXG5cdCRwYWRkaW5nLXRvLWF2b2lkLWN1dHRpbmctZm9jdXMtcmluZzogMnB4O1xuXHRwYWRkaW5nOiAkcGFkZGluZy10by1hdm9pZC1jdXR0aW5nLWZvY3VzLXJpbmc7XG5cdG1hcmdpbi1ibG9jay1zdGFydDogLSRwYWRkaW5nLXRvLWF2b2lkLWN1dHRpbmctZm9jdXMtcmluZztcblx0bWFyZ2luLWJsb2NrLWVuZDogJHBhZGRpbmctdG8tYXZvaWQtY3V0dGluZy1mb2N1cy1yaW5nO1xuXHRtYXJnaW4taW5saW5lLXN0YXJ0OlxuXHRcdHZhcmlhYmxlcy4kZ3JpZC11bml0LTQwIC1cblx0XHQkcGFkZGluZy10by1hdm9pZC1jdXR0aW5nLWZvY3VzLXJpbmc7XG5cdG92ZXJmbG93OiBoaWRkZW47XG59XG5cbi5ib290LWRyb3Bkb3duLWl0ZW1fX2NoZXZyb24uaXMtdXAge1xuXHR0cmFuc2Zvcm06IHJvdGF0ZSgxODBkZWcpO1xufVxuIl19 */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css5));
var ANIMATION_DURATION = 0.2;
function DropdownItem({
  className,
  id,
  icon,
  children,
  isExpanded,
  onToggle
}) {
  const menuItems = (0, import_data4.useSelect)(
    (select) => (
      // @ts-ignore
      select(STORE_NAME).getMenuItems()
    ),
    []
  );
  const items = menuItems.filter((item) => item.parent === id);
  const disableMotion = (0, import_compose.useReducedMotion)();
  return /* @__PURE__ */ (0, import_jsx_runtime21.jsxs)("div", { className: "boot-dropdown-item", children: [
    /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
      import_components8.__experimentalItem,
      {
        className: clsx_default("boot-navigation-item", className),
        onClick: (e) => {
          e.preventDefault();
          e.stopPropagation();
          onToggle();
        },
        onMouseDown: (e) => e.preventDefault(),
        children: /* @__PURE__ */ (0, import_jsx_runtime21.jsxs)(
          import_components8.__experimentalHStack,
          {
            justify: "flex-start",
            spacing: 2,
            style: { flexGrow: "1" },
            children: [
              wrapIcon(icon, false),
              /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(import_components8.FlexBlock, { children }),
              /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
                import_components8.Icon,
                {
                  icon: chevron_down_small_default,
                  className: clsx_default("boot-dropdown-item__chevron", {
                    "is-up": isExpanded
                  })
                }
              )
            ]
          }
        )
      }
    ),
    /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(import_components8.__unstableAnimatePresence, { initial: false, children: isExpanded && /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
      import_components8.__unstableMotion.div,
      {
        initial: { height: 0 },
        animate: { height: "auto" },
        exit: { height: 0 },
        transition: {
          type: "tween",
          duration: disableMotion ? 0 : ANIMATION_DURATION,
          ease: "easeOut"
        },
        className: "boot-dropdown-item__children",
        children: items.map((item, index) => /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
          NavigationItem,
          {
            to: item.to,
            shouldShowPlaceholder: false,
            children: item.label
          },
          index
        ))
      }
    ) })
  ] });
}

// packages/boot/build-module/components/navigation/navigation-screen/index.js
var import_components9 = __toESM(require_components());
var import_i18n4 = __toESM(require_i18n());
var import_compose2 = __toESM(require_compose());
var import_jsx_runtime22 = __toESM(require_jsx_runtime());
var css6 = `/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
.boot-navigation-screen {
  padding-block-end: 4px;
}

.boot-navigation-screen .components-text {
  color: var(--wpds-color-fg-content-neutral, #1e1e1e);
}

.boot-navigation-screen__title-icon {
  position: sticky;
  top: 0;
  padding: 12px 16px 8px 16px;
}

.boot-navigation-screen__title {
  flex-grow: 1;
  overflow-wrap: break-word;
}
.boot-navigation-screen__title.boot-navigation-screen__title, .boot-navigation-screen__title.boot-navigation-screen__title .boot-navigation-screen__title {
  line-height: 32px;
  color: var(--wpds-color-fg-content-neutral, #1e1e1e);
}

.boot-navigation-screen__actions {
  display: flex;
  flex-shrink: 0;
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy9ib290L3NyYy9jb21wb25lbnRzL25hdmlnYXRpb24vbmF2aWdhdGlvbi1zY3JlZW4iLCJzb3VyY2VzIjpbIi4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL192YXJpYWJsZXMuc2NzcyIsIi4uLy4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19jb2xvcnMuc2NzcyIsInN0eWxlLnNjc3MiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUNBQTtBQUFBO0FBQUE7QURVQTtBQUFBO0FBQUE7QUFPQTtBQUFBO0FBQUE7QUE2QkE7QUFBQTtBQUFBO0FBQUE7QUFpQkE7QUFBQTtBQUFBO0FBV0E7QUFBQTtBQUFBO0FBZ0JBO0FBQUE7QUFBQTtBQXlCQTtBQUFBO0FBQUE7QUFLQTtBQUFBO0FBQUE7QUFlQTtBQUFBO0FBQUE7QUFtQkE7QUFBQTtBQUFBO0FBU0E7QUFBQTtBQUFBO0FBQUE7QUVqS0E7RUFFQyxtQkZnRGM7OztBRTdDZjtFQUNDOzs7QUFHRDtFQUNDO0VBQ0E7RUFDQSxTQUNDOzs7QUFJRjtFQUNDO0VBQ0E7O0FBRUE7RUFFQyxhRlN5QjtFRVJ6Qjs7O0FBSUY7RUFDQztFQUNBIiwic291cmNlc0NvbnRlbnQiOlsiLyoqXG4gKiBTQ1NTIFZhcmlhYmxlcy5cbiAqXG4gKiBQbGVhc2UgdXNlIHZhcmlhYmxlcyBmcm9tIHRoaXMgc2hlZXQgdG8gZW5zdXJlIGNvbnNpc3RlbmN5IGFjcm9zcyB0aGUgVUkuXG4gKiBEb24ndCBhZGQgdG8gdGhpcyBzaGVldCB1bmxlc3MgeW91J3JlIHByZXR0eSBzdXJlIHRoZSB2YWx1ZSB3aWxsIGJlIHJldXNlZCBpbiBtYW55IHBsYWNlcy5cbiAqIEZvciBleGFtcGxlLCBkb24ndCBhZGQgcnVsZXMgdG8gdGhpcyBzaGVldCB0aGF0IGFmZmVjdCBibG9jayB2aXN1YWxzLiBJdCdzIHB1cmVseSBmb3IgVUkuXG4gKi9cblxuQHVzZSBcIi4vY29sb3JzXCI7XG5cbi8qKlxuICogRm9udHMgJiBiYXNpYyB2YXJpYWJsZXMuXG4gKi9cblxuJGRlZmF1bHQtZm9udDogLWFwcGxlLXN5c3RlbSwgQmxpbmtNYWNTeXN0ZW1Gb250LFwiU2Vnb2UgVUlcIiwgUm9ib3RvLCBPeHlnZW4tU2FucywgVWJ1bnR1LCBDYW50YXJlbGwsXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmOyAvLyBUb2RvOiBkZXByZWNhdGUgaW4gZmF2b3Igb2YgJGZhbWlseSB2YXJpYWJsZXNcbiRkZWZhdWx0LWxpbmUtaGVpZ2h0OiAxLjQ7IC8vIFRvZG86IGRlcHJlY2F0ZSBpbiBmYXZvciBvZiAkbGluZS1oZWlnaHQgdG9rZW5zXG5cbi8qKlxuICogVHlwb2dyYXBoeVxuICovXG5cbi8vIFNpemVzXG4kZm9udC1zaXplLXgtc21hbGw6IDExcHg7XG4kZm9udC1zaXplLXNtYWxsOiAxMnB4O1xuJGZvbnQtc2l6ZS1tZWRpdW06IDEzcHg7XG4kZm9udC1zaXplLWxhcmdlOiAxNXB4O1xuJGZvbnQtc2l6ZS14LWxhcmdlOiAyMHB4O1xuJGZvbnQtc2l6ZS0yeC1sYXJnZTogMzJweDtcblxuLy8gTGluZSBoZWlnaHRzXG4kZm9udC1saW5lLWhlaWdodC14LXNtYWxsOiAxNnB4O1xuJGZvbnQtbGluZS1oZWlnaHQtc21hbGw6IDIwcHg7XG4kZm9udC1saW5lLWhlaWdodC1tZWRpdW06IDI0cHg7XG4kZm9udC1saW5lLWhlaWdodC1sYXJnZTogMjhweDtcbiRmb250LWxpbmUtaGVpZ2h0LXgtbGFyZ2U6IDMycHg7XG4kZm9udC1saW5lLWhlaWdodC0yeC1sYXJnZTogNDBweDtcblxuLy8gV2VpZ2h0c1xuJGZvbnQtd2VpZ2h0LXJlZ3VsYXI6IDQwMDtcbiRmb250LXdlaWdodC1tZWRpdW06IDQ5OTsgLy8gZW5zdXJlcyBmYWxsYmFjayB0byA0MDAgKGluc3RlYWQgb2YgNjAwKVxuXG4vLyBGYW1pbGllc1xuJGZvbnQtZmFtaWx5LWhlYWRpbmdzOiAtYXBwbGUtc3lzdGVtLCBcInN5c3RlbS11aVwiLCBcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLCBcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7XG4kZm9udC1mYW1pbHktYm9keTogLWFwcGxlLXN5c3RlbSwgXCJzeXN0ZW0tdWlcIiwgXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCwgXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmO1xuJGZvbnQtZmFtaWx5LW1vbm86IE1lbmxvLCBDb25zb2xhcywgbW9uYWNvLCBtb25vc3BhY2U7XG5cbi8qKlxuICogR3JpZCBTeXN0ZW0uXG4gKiBodHRwczovL21ha2Uud29yZHByZXNzLm9yZy9kZXNpZ24vMjAxOS8xMC8zMS9wcm9wb3NhbC1hLWNvbnNpc3RlbnQtc3BhY2luZy1zeXN0ZW0tZm9yLXdvcmRwcmVzcy9cbiAqL1xuXG4kZ3JpZC11bml0OiA4cHg7XG4kZ3JpZC11bml0LTA1OiAwLjUgKiAkZ3JpZC11bml0O1x0Ly8gNHB4XG4kZ3JpZC11bml0LTEwOiAxICogJGdyaWQtdW5pdDtcdFx0Ly8gOHB4XG4kZ3JpZC11bml0LTE1OiAxLjUgKiAkZ3JpZC11bml0O1x0Ly8gMTJweFxuJGdyaWQtdW5pdC0yMDogMiAqICRncmlkLXVuaXQ7XHRcdC8vIDE2cHhcbiRncmlkLXVuaXQtMzA6IDMgKiAkZ3JpZC11bml0O1x0XHQvLyAyNHB4XG4kZ3JpZC11bml0LTQwOiA0ICogJGdyaWQtdW5pdDtcdFx0Ly8gMzJweFxuJGdyaWQtdW5pdC01MDogNSAqICRncmlkLXVuaXQ7XHRcdC8vIDQwcHhcbiRncmlkLXVuaXQtNjA6IDYgKiAkZ3JpZC11bml0O1x0XHQvLyA0OHB4XG4kZ3JpZC11bml0LTcwOiA3ICogJGdyaWQtdW5pdDtcdFx0Ly8gNTZweFxuJGdyaWQtdW5pdC04MDogOCAqICRncmlkLXVuaXQ7XHRcdC8vIDY0cHhcblxuLyoqXG4gKiBSYWRpdXMgc2NhbGUuXG4gKi9cblxuJHJhZGl1cy14LXNtYWxsOiAxcHg7ICAgLy8gQXBwbGllZCB0byBlbGVtZW50cyBsaWtlIGJ1dHRvbnMgbmVzdGVkIHdpdGhpbiBwcmltaXRpdmVzIGxpa2UgaW5wdXRzLlxuJHJhZGl1cy1zbWFsbDogMnB4OyAgICAgLy8gQXBwbGllZCB0byBtb3N0IHByaW1pdGl2ZXMuXG4kcmFkaXVzLW1lZGl1bTogNHB4OyAgICAvLyBBcHBsaWVkIHRvIGNvbnRhaW5lcnMgd2l0aCBzbWFsbGVyIHBhZGRpbmcuXG4kcmFkaXVzLWxhcmdlOiA4cHg7ICAgICAvLyBBcHBsaWVkIHRvIGNvbnRhaW5lcnMgd2l0aCBsYXJnZXIgcGFkZGluZy5cbiRyYWRpdXMtZnVsbDogOTk5OXB4OyAgIC8vIEZvciBwaWxscy5cbiRyYWRpdXMtcm91bmQ6IDUwJTsgICAgIC8vIEZvciBjaXJjbGVzIGFuZCBvdmFscy5cblxuLyoqXG4gKiBFbGV2YXRpb24gc2NhbGUuXG4gKi9cblxuLy8gRm9yIHNlY3Rpb25zIGFuZCBjb250YWluZXJzIHRoYXQgZ3JvdXAgcmVsYXRlZCBjb250ZW50IGFuZCBjb250cm9scywgd2hpY2ggbWF5IG92ZXJsYXAgb3RoZXIgY29udGVudC4gRXhhbXBsZTogUHJldmlldyBGcmFtZS5cbiRlbGV2YXRpb24teC1zbWFsbDogMCAxcHggMXB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMyksIDAgMXB4IDJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpLCAwIDNweCAzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKSwgMCA0cHggNHB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMSk7XG5cbi8vIEZvciBjb21wb25lbnRzIHRoYXQgcHJvdmlkZSBjb250ZXh0dWFsIGZlZWRiYWNrIHdpdGhvdXQgYmVpbmcgaW50cnVzaXZlLiBHZW5lcmFsbHkgbm9uLWludGVycnVwdGl2ZS4gRXhhbXBsZTogVG9vbHRpcHMsIFNuYWNrYmFyLlxuJGVsZXZhdGlvbi1zbWFsbDogMCAxcHggMnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNSksIDAgMnB4IDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDZweCA2cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAzKSwgMCA4cHggOHB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMik7XG5cbi8vIEZvciBjb21wb25lbnRzIHRoYXQgb2ZmZXIgYWRkaXRpb25hbCBhY3Rpb25zLiBFeGFtcGxlOiBNZW51cywgQ29tbWFuZCBQYWxldHRlXG4kZWxldmF0aW9uLW1lZGl1bTogMCAycHggM3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNSksIDAgNHB4IDVweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDEycHggMTJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDMpLCAwIDE2cHggMTZweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vLyBGb3IgY29tcG9uZW50cyB0aGF0IGNvbmZpcm0gZGVjaXNpb25zIG9yIGhhbmRsZSBuZWNlc3NhcnkgaW50ZXJydXB0aW9ucy4gRXhhbXBsZTogTW9kYWxzLlxuJGVsZXZhdGlvbi1sYXJnZTogMCA1cHggMTVweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDgpLCAwIDE1cHggMjdweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDcpLCAwIDMwcHggMzZweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDUwcHggNDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vKipcbiAqIERpbWVuc2lvbnMuXG4gKi9cblxuJGljb24tc2l6ZTogMjRweDtcbiRidXR0b24tc2l6ZTogMzZweDtcbiRidXR0b24tc2l6ZS1uZXh0LWRlZmF1bHQtNDBweDogNDBweDsgLy8gdHJhbnNpdGlvbmFyeSB2YXJpYWJsZSBmb3IgbmV4dCBkZWZhdWx0IGJ1dHRvbiBzaXplXG4kYnV0dG9uLXNpemUtc21hbGw6IDI0cHg7XG4kYnV0dG9uLXNpemUtY29tcGFjdDogMzJweDtcbiRoZWFkZXItaGVpZ2h0OiA2NHB4O1xuJHBhbmVsLWhlYWRlci1oZWlnaHQ6ICRncmlkLXVuaXQtNjA7XG4kbmF2LXNpZGViYXItd2lkdGg6IDMwMHB4O1xuJGFkbWluLWJhci1oZWlnaHQ6IDMycHg7XG4kYWRtaW4tYmFyLWhlaWdodC1iaWc6IDQ2cHg7XG4kYWRtaW4tc2lkZWJhci13aWR0aDogMTYwcHg7XG4kYWRtaW4tc2lkZWJhci13aWR0aC1iaWc6IDE5MHB4O1xuJGFkbWluLXNpZGViYXItd2lkdGgtY29sbGFwc2VkOiAzNnB4O1xuJG1vZGFsLW1pbi13aWR0aDogMzUwcHg7XG4kbW9kYWwtd2lkdGgtc21hbGw6IDM4NHB4O1xuJG1vZGFsLXdpZHRoLW1lZGl1bTogNTEycHg7XG4kbW9kYWwtd2lkdGgtbGFyZ2U6IDg0MHB4O1xuJHNwaW5uZXItc2l6ZTogMTZweDtcbiRjYW52YXMtcGFkZGluZzogJGdyaWQtdW5pdC0yMDtcbiRwYWxldHRlLW1heC1oZWlnaHQ6IDM2OHB4O1xuXG4vKipcbiAqIE1vYmlsZSBzcGVjaWZpYyBzdHlsZXNcbiAqL1xuJG1vYmlsZS10ZXh0LW1pbi1mb250LXNpemU6IDE2cHg7IC8vIEFueSBmb250IHNpemUgYmVsb3cgMTZweCB3aWxsIGNhdXNlIE1vYmlsZSBTYWZhcmkgdG8gXCJ6b29tIGluXCIuXG5cbi8qKlxuICogRWRpdG9yIHN0eWxlcy5cbiAqL1xuXG4kc2lkZWJhci13aWR0aDogMjgwcHg7XG4kY29udGVudC13aWR0aDogODQwcHg7XG4kd2lkZS1jb250ZW50LXdpZHRoOiAxMTAwcHg7XG4kd2lkZ2V0LWFyZWEtd2lkdGg6IDcwMHB4O1xuJHNlY29uZGFyeS1zaWRlYmFyLXdpZHRoOiAzNTBweDtcbiRlZGl0b3ItZm9udC1zaXplOiAxNnB4O1xuJGRlZmF1bHQtYmxvY2stbWFyZ2luOiAyOHB4OyAvLyBUaGlzIHZhbHVlIHByb3ZpZGVzIGEgY29uc2lzdGVudCwgY29udGlndW91cyBzcGFjaW5nIGJldHdlZW4gYmxvY2tzLlxuJHRleHQtZWRpdG9yLWZvbnQtc2l6ZTogMTVweDtcbiRlZGl0b3ItbGluZS1oZWlnaHQ6IDEuODtcbiRlZGl0b3ItaHRtbC1mb250OiAkZm9udC1mYW1pbHktbW9ubztcblxuLyoqXG4gKiBCbG9jayAmIEVkaXRvciBVSS5cbiAqL1xuXG4kYmxvY2stdG9vbGJhci1oZWlnaHQ6ICRncmlkLXVuaXQtNjA7XG4kYm9yZGVyLXdpZHRoOiAxcHg7XG4kYm9yZGVyLXdpZHRoLWZvY3VzLWZhbGxiYWNrOiAycHg7IC8vIFRoaXMgZXhpc3RzIGFzIGEgZmFsbGJhY2ssIGFuZCBpcyBpZGVhbGx5IG92ZXJyaWRkZW4gYnkgdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSB1bmxlc3MgaW4gc29tZSBTQVNTIG1hdGggY2FzZXMuXG4kYm9yZGVyLXdpZHRoLXRhYjogMS41cHg7XG4kaGVscHRleHQtZm9udC1zaXplOiAxMnB4O1xuJHJhZGlvLWlucHV0LXNpemU6IDE2cHg7XG4kcmFkaW8taW5wdXQtc2l6ZS1zbTogMjRweDsgLy8gV2lkdGggJiBoZWlnaHQgZm9yIHNtYWxsIHZpZXdwb3J0cy5cblxuLy8gRGVwcmVjYXRlZCwgcGxlYXNlIGF2b2lkIHVzaW5nIHRoZXNlLlxuJGJsb2NrLXBhZGRpbmc6IDE0cHg7IC8vIFVzZWQgdG8gZGVmaW5lIHNwYWNlIGJldHdlZW4gYmxvY2sgZm9vdHByaW50IGFuZCBzdXJyb3VuZGluZyBib3JkZXJzLlxuJHJhZGl1cy1ibG9jay11aTogJHJhZGl1cy1zbWFsbDtcbiRzaGFkb3ctcG9wb3ZlcjogJGVsZXZhdGlvbi14LXNtYWxsO1xuJHNoYWRvdy1tb2RhbDogJGVsZXZhdGlvbi1sYXJnZTtcbiRkZWZhdWx0LWZvbnQtc2l6ZTogJGZvbnQtc2l6ZS1tZWRpdW07XG5cbi8qKlxuICogQmxvY2sgcGFkZGluZ3MuXG4gKi9cblxuLy8gUGFkZGluZyBmb3IgYmxvY2tzIHdpdGggYSBiYWNrZ3JvdW5kIGNvbG9yIChlLmcuIHBhcmFncmFwaCBvciBncm91cCkuXG4kYmxvY2stYmctcGFkZGluZy0tdjogMS4yNWVtO1xuJGJsb2NrLWJnLXBhZGRpbmctLWg6IDIuMzc1ZW07XG5cblxuLyoqXG4gKiBSZWFjdCBOYXRpdmUgc3BlY2lmaWMuXG4gKiBUaGVzZSB2YXJpYWJsZXMgZG8gbm90IGFwcGVhciB0byBiZSB1c2VkIGFueXdoZXJlIGVsc2UuXG4gKi9cblxuLy8gRGltZW5zaW9ucy5cbiRtb2JpbGUtaGVhZGVyLXRvb2xiYXItaGVpZ2h0OiA0NHB4O1xuJG1vYmlsZS1oZWFkZXItdG9vbGJhci1leHBhbmRlZC1oZWlnaHQ6IDUycHg7XG4kbW9iaWxlLWZsb2F0aW5nLXRvb2xiYXItaGVpZ2h0OiA0NHB4O1xuJG1vYmlsZS1mbG9hdGluZy10b29sYmFyLW1hcmdpbjogOHB4O1xuJG1vYmlsZS1jb2xvci1zd2F0Y2g6IDQ4cHg7XG5cbi8vIEJsb2NrIFVJLlxuJG1vYmlsZS1ibG9jay10b29sYmFyLWhlaWdodDogNDRweDtcbiRkaW1tZWQtb3BhY2l0eTogMTtcbiRibG9jay1lZGdlLXRvLWNvbnRlbnQ6IDE2cHg7XG4kc29saWQtYm9yZGVyLXNwYWNlOiAxMnB4O1xuJGRhc2hlZC1ib3JkZXItc3BhY2U6IDZweDtcbiRibG9jay1zZWxlY3RlZC1tYXJnaW46IDNweDtcbiRibG9jay1zZWxlY3RlZC1ib3JkZXItd2lkdGg6IDFweDtcbiRibG9jay1zZWxlY3RlZC1wYWRkaW5nOiAwO1xuJGJsb2NrLXNlbGVjdGVkLWNoaWxkLW1hcmdpbjogNXB4O1xuJGJsb2NrLXNlbGVjdGVkLXRvLWNvbnRlbnQ6ICRibG9jay1lZGdlLXRvLWNvbnRlbnQgLSAkYmxvY2stc2VsZWN0ZWQtbWFyZ2luIC0gJGJsb2NrLXNlbGVjdGVkLWJvcmRlci13aWR0aDtcbiIsIi8qKlxuICogQ29sb3JzXG4gKi9cblxuLy8gV29yZFByZXNzIGdyYXlzLlxuJGJsYWNrOiAjMDAwO1x0XHRcdC8vIFVzZSBvbmx5IHdoZW4geW91IHRydWx5IG5lZWQgcHVyZSBibGFjay4gRm9yIFVJLCB1c2UgJGdyYXktOTAwLlxuJGdyYXktOTAwOiAjMWUxZTFlO1xuJGdyYXktODAwOiAjMmYyZjJmO1xuJGdyYXktNzAwOiAjNzU3NTc1O1x0XHQvLyBNZWV0cyA0LjY6MSAoNC41OjEgaXMgbWluaW11bSkgdGV4dCBjb250cmFzdCBhZ2FpbnN0IHdoaXRlLlxuJGdyYXktNjAwOiAjOTQ5NDk0O1x0XHQvLyBNZWV0cyAzOjEgVUkgb3IgbGFyZ2UgdGV4dCBjb250cmFzdCBhZ2FpbnN0IHdoaXRlLlxuJGdyYXktNDAwOiAjY2NjO1xuJGdyYXktMzAwOiAjZGRkO1x0XHQvLyBVc2VkIGZvciBtb3N0IGJvcmRlcnMuXG4kZ3JheS0yMDA6ICNlMGUwZTA7XHRcdC8vIFVzZWQgc3BhcmluZ2x5IGZvciBsaWdodCBib3JkZXJzLlxuJGdyYXktMTAwOiAjZjBmMGYwO1x0XHQvLyBVc2VkIGZvciBsaWdodCBncmF5IGJhY2tncm91bmRzLlxuJHdoaXRlOiAjZmZmO1xuXG4vLyBPcGFjaXRpZXMgJiBhZGRpdGlvbmFsIGNvbG9ycy5cbiRkYXJrLWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJGdyYXktOTAwLCAwLjYyKTtcbiRtZWRpdW0tZ3JheS1wbGFjZWhvbGRlcjogcmdiYSgkZ3JheS05MDAsIDAuNTUpO1xuJGxpZ2h0LWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJHdoaXRlLCAwLjY1KTtcblxuLy8gQWxlcnQgY29sb3JzLlxuJGFsZXJ0LXllbGxvdzogI2YwYjg0OTtcbiRhbGVydC1yZWQ6ICNjYzE4MTg7XG4kYWxlcnQtZ3JlZW46ICM0YWI4NjY7XG5cbi8vIERlcHJlY2F0ZWQsIHBsZWFzZSBhdm9pZCB1c2luZyB0aGVzZS5cbiRkYXJrLXRoZW1lLWZvY3VzOiAkd2hpdGU7XHQvLyBGb2N1cyBjb2xvciB3aGVuIHRoZSB0aGVtZSBpcyBkYXJrLlxuIiwiQHVzZSBcIkB3b3JkcHJlc3MvYmFzZS1zdHlsZXMvdmFyaWFibGVzXCI7XG5cbi5ib290LW5hdmlnYXRpb24tc2NyZWVuIHtcblx0Ly8gQXZvaWQgY3V0dGluZyBvZmYgZm9jdXMgcmluZyBvZiB0aGUgbGFzdCBtZW51IGl0ZW1cblx0cGFkZGluZy1ibG9jay1lbmQ6IHZhcmlhYmxlcy4kZ3JpZC11bml0LTA1O1xufVxuXG4uYm9vdC1uYXZpZ2F0aW9uLXNjcmVlbiAuY29tcG9uZW50cy10ZXh0IHtcblx0Y29sb3I6IHZhcigtLXdwZHMtY29sb3ItZmctY29udGVudC1uZXV0cmFsLCAjMWUxZTFlKTtcbn1cblxuLmJvb3QtbmF2aWdhdGlvbi1zY3JlZW5fX3RpdGxlLWljb24ge1xuXHRwb3NpdGlvbjogc3RpY2t5O1xuXHR0b3A6IDA7XG5cdHBhZGRpbmc6XG5cdFx0dmFyaWFibGVzLiRncmlkLXVuaXQtMTUgdmFyaWFibGVzLiRncmlkLXVuaXQtMjBcblx0XHR2YXJpYWJsZXMuJGdyaWQtdW5pdC0xMCB2YXJpYWJsZXMuJGdyaWQtdW5pdC0yMDtcbn1cblxuLmJvb3QtbmF2aWdhdGlvbi1zY3JlZW5fX3RpdGxlIHtcblx0ZmxleC1ncm93OiAxO1xuXHRvdmVyZmxvdy13cmFwOiBicmVhay13b3JkO1xuXG5cdCYjeyZ9LFxuXHQmI3smfSAuYm9vdC1uYXZpZ2F0aW9uLXNjcmVlbl9fdGl0bGUge1xuXHRcdGxpbmUtaGVpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtbGluZS1oZWlnaHQteC1sYXJnZTtcblx0XHRjb2xvcjogdmFyKC0td3Bkcy1jb2xvci1mZy1jb250ZW50LW5ldXRyYWwsICMxZTFlMWUpO1xuXHR9XG59XG5cbi5ib290LW5hdmlnYXRpb24tc2NyZWVuX19hY3Rpb25zIHtcblx0ZGlzcGxheTogZmxleDtcblx0ZmxleC1zaHJpbms6IDA7XG59XG4iXX0= */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css6));
var ANIMATION_DURATION2 = 0.3;
var slideVariants = {
  initial: (direction) => ({
    x: direction === "forward" ? 100 : -100,
    opacity: 0
  }),
  animate: {
    x: 0,
    opacity: 1
  },
  exit: (direction) => ({
    x: direction === "forward" ? 100 : -100,
    opacity: 0
  })
};
function NavigationScreen({
  isRoot,
  title,
  actions,
  content,
  description,
  animationDirection,
  backMenuItem,
  backButtonRef,
  navigationKey,
  onNavigate
}) {
  const icon = (0, import_i18n4.isRTL)() ? chevron_right_default : chevron_left_default;
  const disableMotion = (0, import_compose2.useReducedMotion)();
  const handleBackClick = (e) => {
    e.preventDefault();
    onNavigate({ id: backMenuItem, direction: "backward" });
  };
  return /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
    "div",
    {
      className: "boot-navigation-screen",
      style: {
        overflow: "hidden",
        position: "relative",
        display: "grid",
        gridTemplateColumns: "1fr",
        gridTemplateRows: "1fr"
      },
      children: /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(import_components9.__unstableAnimatePresence, { initial: false, children: /* @__PURE__ */ (0, import_jsx_runtime22.jsxs)(
        import_components9.__unstableMotion.div,
        {
          custom: animationDirection,
          variants: slideVariants,
          initial: "initial",
          animate: "animate",
          exit: "exit",
          transition: {
            type: "tween",
            duration: disableMotion ? 0 : ANIMATION_DURATION2,
            ease: [0.33, 0, 0, 1]
          },
          style: {
            width: "100%",
            gridColumn: "1",
            gridRow: "1"
          },
          children: [
            /* @__PURE__ */ (0, import_jsx_runtime22.jsxs)(
              import_components9.__experimentalHStack,
              {
                spacing: 2,
                className: "boot-navigation-screen__title-icon",
                children: [
                  !isRoot && /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
                    import_components9.Button,
                    {
                      ref: backButtonRef,
                      icon,
                      onClick: handleBackClick,
                      label: (0, import_i18n4.__)("Back"),
                      size: "small",
                      variant: "tertiary"
                    }
                  ),
                  /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
                    import_components9.__experimentalHeading,
                    {
                      className: "boot-navigation-screen__title",
                      level: 1,
                      size: "15px",
                      children: title
                    }
                  ),
                  actions && /* @__PURE__ */ (0, import_jsx_runtime22.jsx)("div", { className: "boot-navigation-screen__actions", children: actions })
                ]
              }
            ),
            description && /* @__PURE__ */ (0, import_jsx_runtime22.jsx)("div", { className: "boot-navigation-screen__description", children: description }),
            content
          ]
        },
        navigationKey
      ) })
    }
  );
}

// packages/boot/build-module/components/navigation/use-sidebar-parent.js
var import_element5 = __toESM(require_element());
var import_data5 = __toESM(require_data());
import { privateApis as routePrivateApis3 } from "@wordpress/route";

// packages/boot/build-module/components/navigation/path-matching.js
var isValidParentPath = (currentPath, menuPath) => {
  if (!menuPath || menuPath === currentPath) {
    return false;
  }
  const normalizePath = (path) => {
    const normalized = path.startsWith("/") ? path : "/" + path;
    return normalized.endsWith("/") && normalized.length > 1 ? normalized.slice(0, -1) : normalized;
  };
  const normalizedCurrent = normalizePath(currentPath);
  const normalizedMenu = normalizePath(menuPath);
  return normalizedCurrent.startsWith(normalizedMenu) && (normalizedCurrent[normalizedMenu.length] === "/" || normalizedMenu === "/");
};
var findClosestMenuItem = (currentPath, menuItems) => {
  const exactMatch = menuItems.find((item) => item.to === currentPath);
  if (exactMatch) {
    return exactMatch;
  }
  let bestMatch = null;
  let bestPathLength = 0;
  for (const item of menuItems) {
    if (!item.to) {
      continue;
    }
    if (isValidParentPath(currentPath, item.to)) {
      if (item.to.length > bestPathLength) {
        bestMatch = item;
        bestPathLength = item.to.length;
      }
    }
  }
  return bestMatch;
};
var findDrilldownParent = (id, menuItems) => {
  if (!id) {
    return void 0;
  }
  const currentItem = menuItems.find((item) => item.id === id);
  if (!currentItem) {
    return void 0;
  }
  if (currentItem.parent) {
    const parentItem = menuItems.find(
      (item) => item.id === currentItem.parent
    );
    if (parentItem?.parent_type === "drilldown") {
      return parentItem.id;
    }
    if (parentItem) {
      return findDrilldownParent(parentItem.id, menuItems);
    }
  }
  return void 0;
};
var findDropdownParent = (id, menuItems) => {
  if (!id) {
    return void 0;
  }
  const currentItem = menuItems.find((item) => item.id === id);
  if (!currentItem) {
    return void 0;
  }
  if (currentItem.parent) {
    const parentItem = menuItems.find(
      (item) => item.id === currentItem.parent
    );
    if (parentItem?.parent_type === "dropdown") {
      return parentItem.id;
    }
  }
  return void 0;
};

// packages/boot/build-module/components/navigation/use-sidebar-parent.js
var { useRouter: useRouter2, useMatches } = unlock(routePrivateApis3);
function useSidebarParent() {
  const matches = useMatches();
  const router = useRouter2();
  const menuItems = (0, import_data5.useSelect)(
    (select) => (
      // @ts-ignore
      select(STORE_NAME).getMenuItems()
    ),
    []
  );
  const currentPath = matches[matches.length - 1].pathname.slice(
    router.options.basepath?.length ?? 0
  );
  const currentMenuItem = findClosestMenuItem(currentPath, menuItems);
  const [parentId, setParentId] = (0, import_element5.useState)(
    findDrilldownParent(currentMenuItem?.id, menuItems)
  );
  const [parentDropdownId, setParentDropdownId] = (0, import_element5.useState)(findDropdownParent(currentMenuItem?.id, menuItems));
  (0, import_element5.useEffect)(() => {
    const matchedMenuItem = findClosestMenuItem(currentPath, menuItems);
    const updatedParentId = findDrilldownParent(
      matchedMenuItem?.id,
      menuItems
    );
    const updatedDropdownParent = findDropdownParent(
      matchedMenuItem?.id,
      menuItems
    );
    setParentId(updatedParentId);
    setParentDropdownId(updatedDropdownParent);
  }, [currentPath, menuItems]);
  return [
    parentId,
    setParentId,
    parentDropdownId,
    setParentDropdownId
  ];
}

// packages/boot/build-module/components/navigation/index.js
var import_jsx_runtime23 = __toESM(require_jsx_runtime());
function Navigation() {
  const backButtonRef = (0, import_element6.useRef)(null);
  const [animationDirection, setAnimationDirection] = (0, import_element6.useState)(null);
  const [parentId, setParentId, parentDropdownId, setParentDropdownId] = useSidebarParent();
  const menuItems = (0, import_data6.useSelect)(
    (select) => (
      // @ts-ignore
      select(STORE_NAME).getMenuItems()
    ),
    []
  );
  const parent = (0, import_element6.useMemo)(
    () => menuItems.find((item) => item.id === parentId),
    [menuItems, parentId]
  );
  const navigationKey = parent ? `drilldown-${parent.id}` : "root";
  const handleNavigate = ({
    id,
    direction
  }) => {
    setAnimationDirection(direction);
    setParentId(id);
  };
  const handleDropdownToggle = (dropdownId) => {
    setParentDropdownId(
      parentDropdownId === dropdownId ? void 0 : dropdownId
    );
  };
  const items = (0, import_element6.useMemo)(
    () => menuItems.filter((item) => item.parent === parentId),
    [menuItems, parentId]
  );
  const hasRealIcons = items.some((item) => !!item.icon);
  return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
    NavigationScreen,
    {
      isRoot: !parent,
      title: parent ? parent.label : "",
      backMenuItem: parent?.parent,
      backButtonRef,
      animationDirection: animationDirection || void 0,
      navigationKey,
      onNavigate: handleNavigate,
      content: /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_jsx_runtime23.Fragment, { children: items.map((item) => {
        if (item.parent_type === "dropdown") {
          return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
            DropdownItem,
            {
              id: item.id,
              className: "boot-navigation-item",
              icon: item.icon,
              shouldShowPlaceholder: hasRealIcons,
              isExpanded: parentDropdownId === item.id,
              onToggle: () => handleDropdownToggle(item.id),
              children: item.label
            },
            item.id
          );
        }
        if (item.parent_type === "drilldown") {
          return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
            DrilldownItem,
            {
              id: item.id,
              icon: item.icon,
              shouldShowPlaceholder: hasRealIcons,
              onNavigate: handleNavigate,
              children: item.label
            },
            item.id
          );
        }
        return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
          NavigationItem,
          {
            to: item.to,
            icon: item.icon,
            shouldShowPlaceholder: hasRealIcons,
            children: item.label
          },
          item.id
        );
      }) })
    }
  );
}
var navigation_default = Navigation;

// packages/boot/build-module/components/save-button/index.js
var import_element8 = __toESM(require_element());
var import_data8 = __toESM(require_data());
var import_i18n6 = __toESM(require_i18n());
var import_core_data4 = __toESM(require_core_data());
var import_keycodes2 = __toESM(require_keycodes());
var import_editor2 = __toESM(require_editor());
var import_components10 = __toESM(require_components());

// packages/boot/build-module/components/save-panel/use-save-shortcut.js
var import_element7 = __toESM(require_element());
var import_keyboard_shortcuts = __toESM(require_keyboard_shortcuts());
var import_i18n5 = __toESM(require_i18n());
var import_data7 = __toESM(require_data());
var import_core_data3 = __toESM(require_core_data());
var import_editor = __toESM(require_editor());
var shortcutName = "core/boot/save";
function useSaveShortcut({
  openSavePanel
}) {
  const { __experimentalGetDirtyEntityRecords, isSavingEntityRecord } = (0, import_data7.useSelect)(import_core_data3.store);
  const { hasNonPostEntityChanges, isPostSavingLocked } = (0, import_data7.useSelect)(import_editor.store);
  const { savePost } = (0, import_data7.useDispatch)(import_editor.store);
  const { registerShortcut, unregisterShortcut } = (0, import_data7.useDispatch)(
    import_keyboard_shortcuts.store
  );
  (0, import_element7.useEffect)(() => {
    registerShortcut({
      name: shortcutName,
      category: "global",
      description: (0, import_i18n5.__)("Save your changes."),
      keyCombination: {
        modifier: "primary",
        character: "s"
      }
    });
    return () => {
      unregisterShortcut(shortcutName);
    };
  }, [registerShortcut, unregisterShortcut]);
  (0, import_keyboard_shortcuts.useShortcut)(shortcutName, (event) => {
    event.preventDefault();
    const dirtyEntityRecords = __experimentalGetDirtyEntityRecords();
    const hasDirtyEntities = !!dirtyEntityRecords.length;
    const isSaving = dirtyEntityRecords.some(
      (record) => isSavingEntityRecord(record.kind, record.name, record.key)
    );
    if (!hasDirtyEntities || isSaving) {
      return;
    }
    if (hasNonPostEntityChanges()) {
      openSavePanel();
    } else if (!isPostSavingLocked()) {
      savePost();
    }
  });
}

// packages/boot/build-module/components/save-button/index.js
var import_jsx_runtime24 = __toESM(require_jsx_runtime());
var css7 = `.boot-save-button {
  width: 100%;
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy9ib290L3NyYy9jb21wb25lbnRzL3NhdmUtYnV0dG9uIiwic291cmNlcyI6WyJzdHlsZS5zY3NzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0VBQ0MiLCJzb3VyY2VzQ29udGVudCI6WyIuYm9vdC1zYXZlLWJ1dHRvbiB7XG5cdHdpZHRoOiAxMDAlO1xufVxuIl19 */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css7));
function SaveButton() {
  const [isSaveViewOpen, setIsSaveViewOpened] = (0, import_element8.useState)(false);
  const { isSaving, dirtyEntityRecordsCount } = (0, import_data8.useSelect)((select) => {
    const { isSavingEntityRecord, __experimentalGetDirtyEntityRecords } = select(import_core_data4.store);
    const dirtyEntityRecords = __experimentalGetDirtyEntityRecords();
    return {
      isSaving: dirtyEntityRecords.some(
        (record) => isSavingEntityRecord(record.kind, record.name, record.key)
      ),
      dirtyEntityRecordsCount: dirtyEntityRecords.length
    };
  }, []);
  const [showSavedState, setShowSavedState] = (0, import_element8.useState)(false);
  (0, import_element8.useEffect)(() => {
    if (isSaving) {
      setShowSavedState(true);
    }
  }, [isSaving]);
  const hasChanges = dirtyEntityRecordsCount > 0;
  (0, import_element8.useEffect)(() => {
    if (!isSaving && hasChanges) {
      setShowSavedState(false);
    }
  }, [isSaving, hasChanges]);
  function hideSavedState() {
    if (showSavedState) {
      setShowSavedState(false);
    }
  }
  const shouldShowButton = hasChanges || showSavedState;
  useSaveShortcut({ openSavePanel: () => setIsSaveViewOpened(true) });
  if (!shouldShowButton) {
    return null;
  }
  const isInSavedState = showSavedState && !hasChanges;
  const disabled = isSaving || isInSavedState;
  const getLabel = () => {
    if (isInSavedState) {
      return (0, import_i18n6.__)("Saved");
    }
    return (0, import_i18n6.sprintf)(
      // translators: %d: number of unsaved changes (number).
      (0, import_i18n6._n)(
        "Review %d change\u2026",
        "Review %d changes\u2026",
        dirtyEntityRecordsCount
      ),
      dirtyEntityRecordsCount
    );
  };
  const label = getLabel();
  return /* @__PURE__ */ (0, import_jsx_runtime24.jsxs)(import_jsx_runtime24.Fragment, { children: [
    /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
      import_components10.Tooltip,
      {
        text: hasChanges ? label : void 0,
        shortcut: import_keycodes2.displayShortcut.primary("s"),
        children: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
          import_components10.Button,
          {
            variant: "primary",
            size: "compact",
            onClick: () => setIsSaveViewOpened(true),
            onBlur: hideSavedState,
            disabled,
            accessibleWhenDisabled: true,
            isBusy: isSaving,
            "aria-keyshortcuts": import_keycodes2.rawShortcut.primary("s"),
            className: "boot-save-button",
            icon: isInSavedState ? check_default : void 0,
            children: label
          }
        )
      }
    ),
    isSaveViewOpen && /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
      import_components10.Modal,
      {
        title: (0, import_i18n6.__)("Review changes"),
        onRequestClose: () => setIsSaveViewOpened(false),
        size: "small",
        children: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
          import_editor2.EntitiesSavedStates,
          {
            close: () => setIsSaveViewOpened(false),
            variant: "inline"
          }
        )
      }
    )
  ] });
}

// packages/boot/build-module/components/sidebar/index.js
var import_jsx_runtime25 = __toESM(require_jsx_runtime());
var css8 = `/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
.boot-sidebar__scrollable {
  overflow: auto;
  height: 100%;
  position: relative;
  display: flex;
  flex-direction: column;
}

.boot-sidebar__content {
  flex-grow: 1;
  contain: content;
  position: relative;
}

.boot-sidebar__footer {
  padding: 16px 8px 8px 16px;
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy9ib290L3NyYy9jb21wb25lbnRzL3NpZGViYXIiLCJzb3VyY2VzIjpbIi4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL192YXJpYWJsZXMuc2NzcyIsIi4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19jb2xvcnMuc2NzcyIsInN0eWxlLnNjc3MiXSwibmFtZXMiOltdLCJtYXBwaW5ncyI6IkFBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUNBQTtBQUFBO0FBQUE7QURVQTtBQUFBO0FBQUE7QUFPQTtBQUFBO0FBQUE7QUE2QkE7QUFBQTtBQUFBO0FBQUE7QUFpQkE7QUFBQTtBQUFBO0FBV0E7QUFBQTtBQUFBO0FBZ0JBO0FBQUE7QUFBQTtBQXlCQTtBQUFBO0FBQUE7QUFLQTtBQUFBO0FBQUE7QUFlQTtBQUFBO0FBQUE7QUFtQkE7QUFBQTtBQUFBO0FBU0E7QUFBQTtBQUFBO0FBQUE7QUVqS0E7RUFDQztFQUNBO0VBQ0E7RUFDQTtFQUNBOzs7QUFHRDtFQUNDO0VBQ0E7RUFDQTs7O0FBR0Q7RUFDQyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogU0NTUyBWYXJpYWJsZXMuXG4gKlxuICogUGxlYXNlIHVzZSB2YXJpYWJsZXMgZnJvbSB0aGlzIHNoZWV0IHRvIGVuc3VyZSBjb25zaXN0ZW5jeSBhY3Jvc3MgdGhlIFVJLlxuICogRG9uJ3QgYWRkIHRvIHRoaXMgc2hlZXQgdW5sZXNzIHlvdSdyZSBwcmV0dHkgc3VyZSB0aGUgdmFsdWUgd2lsbCBiZSByZXVzZWQgaW4gbWFueSBwbGFjZXMuXG4gKiBGb3IgZXhhbXBsZSwgZG9uJ3QgYWRkIHJ1bGVzIHRvIHRoaXMgc2hlZXQgdGhhdCBhZmZlY3QgYmxvY2sgdmlzdWFscy4gSXQncyBwdXJlbHkgZm9yIFVJLlxuICovXG5cbkB1c2UgXCIuL2NvbG9yc1wiO1xuXG4vKipcbiAqIEZvbnRzICYgYmFzaWMgdmFyaWFibGVzLlxuICovXG5cbiRkZWZhdWx0LWZvbnQ6IC1hcHBsZS1zeXN0ZW0sIEJsaW5rTWFjU3lzdGVtRm9udCxcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLFwiSGVsdmV0aWNhIE5ldWVcIiwgc2Fucy1zZXJpZjsgLy8gVG9kbzogZGVwcmVjYXRlIGluIGZhdm9yIG9mICRmYW1pbHkgdmFyaWFibGVzXG4kZGVmYXVsdC1saW5lLWhlaWdodDogMS40OyAvLyBUb2RvOiBkZXByZWNhdGUgaW4gZmF2b3Igb2YgJGxpbmUtaGVpZ2h0IHRva2Vuc1xuXG4vKipcbiAqIFR5cG9ncmFwaHlcbiAqL1xuXG4vLyBTaXplc1xuJGZvbnQtc2l6ZS14LXNtYWxsOiAxMXB4O1xuJGZvbnQtc2l6ZS1zbWFsbDogMTJweDtcbiRmb250LXNpemUtbWVkaXVtOiAxM3B4O1xuJGZvbnQtc2l6ZS1sYXJnZTogMTVweDtcbiRmb250LXNpemUteC1sYXJnZTogMjBweDtcbiRmb250LXNpemUtMngtbGFyZ2U6IDMycHg7XG5cbi8vIExpbmUgaGVpZ2h0c1xuJGZvbnQtbGluZS1oZWlnaHQteC1zbWFsbDogMTZweDtcbiRmb250LWxpbmUtaGVpZ2h0LXNtYWxsOiAyMHB4O1xuJGZvbnQtbGluZS1oZWlnaHQtbWVkaXVtOiAyNHB4O1xuJGZvbnQtbGluZS1oZWlnaHQtbGFyZ2U6IDI4cHg7XG4kZm9udC1saW5lLWhlaWdodC14LWxhcmdlOiAzMnB4O1xuJGZvbnQtbGluZS1oZWlnaHQtMngtbGFyZ2U6IDQwcHg7XG5cbi8vIFdlaWdodHNcbiRmb250LXdlaWdodC1yZWd1bGFyOiA0MDA7XG4kZm9udC13ZWlnaHQtbWVkaXVtOiA0OTk7IC8vIGVuc3VyZXMgZmFsbGJhY2sgdG8gNDAwIChpbnN0ZWFkIG9mIDYwMClcblxuLy8gRmFtaWxpZXNcbiRmb250LWZhbWlseS1oZWFkaW5nczogLWFwcGxlLXN5c3RlbSwgXCJzeXN0ZW0tdWlcIiwgXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCwgXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmO1xuJGZvbnQtZmFtaWx5LWJvZHk6IC1hcHBsZS1zeXN0ZW0sIFwic3lzdGVtLXVpXCIsIFwiU2Vnb2UgVUlcIiwgUm9ib3RvLCBPeHlnZW4tU2FucywgVWJ1bnR1LCBDYW50YXJlbGwsIFwiSGVsdmV0aWNhIE5ldWVcIiwgc2Fucy1zZXJpZjtcbiRmb250LWZhbWlseS1tb25vOiBNZW5sbywgQ29uc29sYXMsIG1vbmFjbywgbW9ub3NwYWNlO1xuXG4vKipcbiAqIEdyaWQgU3lzdGVtLlxuICogaHR0cHM6Ly9tYWtlLndvcmRwcmVzcy5vcmcvZGVzaWduLzIwMTkvMTAvMzEvcHJvcG9zYWwtYS1jb25zaXN0ZW50LXNwYWNpbmctc3lzdGVtLWZvci13b3JkcHJlc3MvXG4gKi9cblxuJGdyaWQtdW5pdDogOHB4O1xuJGdyaWQtdW5pdC0wNTogMC41ICogJGdyaWQtdW5pdDtcdC8vIDRweFxuJGdyaWQtdW5pdC0xMDogMSAqICRncmlkLXVuaXQ7XHRcdC8vIDhweFxuJGdyaWQtdW5pdC0xNTogMS41ICogJGdyaWQtdW5pdDtcdC8vIDEycHhcbiRncmlkLXVuaXQtMjA6IDIgKiAkZ3JpZC11bml0O1x0XHQvLyAxNnB4XG4kZ3JpZC11bml0LTMwOiAzICogJGdyaWQtdW5pdDtcdFx0Ly8gMjRweFxuJGdyaWQtdW5pdC00MDogNCAqICRncmlkLXVuaXQ7XHRcdC8vIDMycHhcbiRncmlkLXVuaXQtNTA6IDUgKiAkZ3JpZC11bml0O1x0XHQvLyA0MHB4XG4kZ3JpZC11bml0LTYwOiA2ICogJGdyaWQtdW5pdDtcdFx0Ly8gNDhweFxuJGdyaWQtdW5pdC03MDogNyAqICRncmlkLXVuaXQ7XHRcdC8vIDU2cHhcbiRncmlkLXVuaXQtODA6IDggKiAkZ3JpZC11bml0O1x0XHQvLyA2NHB4XG5cbi8qKlxuICogUmFkaXVzIHNjYWxlLlxuICovXG5cbiRyYWRpdXMteC1zbWFsbDogMXB4OyAgIC8vIEFwcGxpZWQgdG8gZWxlbWVudHMgbGlrZSBidXR0b25zIG5lc3RlZCB3aXRoaW4gcHJpbWl0aXZlcyBsaWtlIGlucHV0cy5cbiRyYWRpdXMtc21hbGw6IDJweDsgICAgIC8vIEFwcGxpZWQgdG8gbW9zdCBwcmltaXRpdmVzLlxuJHJhZGl1cy1tZWRpdW06IDRweDsgICAgLy8gQXBwbGllZCB0byBjb250YWluZXJzIHdpdGggc21hbGxlciBwYWRkaW5nLlxuJHJhZGl1cy1sYXJnZTogOHB4OyAgICAgLy8gQXBwbGllZCB0byBjb250YWluZXJzIHdpdGggbGFyZ2VyIHBhZGRpbmcuXG4kcmFkaXVzLWZ1bGw6IDk5OTlweDsgICAvLyBGb3IgcGlsbHMuXG4kcmFkaXVzLXJvdW5kOiA1MCU7ICAgICAvLyBGb3IgY2lyY2xlcyBhbmQgb3ZhbHMuXG5cbi8qKlxuICogRWxldmF0aW9uIHNjYWxlLlxuICovXG5cbi8vIEZvciBzZWN0aW9ucyBhbmQgY29udGFpbmVycyB0aGF0IGdyb3VwIHJlbGF0ZWQgY29udGVudCBhbmQgY29udHJvbHMsIHdoaWNoIG1heSBvdmVybGFwIG90aGVyIGNvbnRlbnQuIEV4YW1wbGU6IFByZXZpZXcgRnJhbWUuXG4kZWxldmF0aW9uLXgtc21hbGw6IDAgMXB4IDFweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDMpLCAwIDFweCAycHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKSwgMCAzcHggM3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMiksIDAgNHB4IDRweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDEpO1xuXG4vLyBGb3IgY29tcG9uZW50cyB0aGF0IHByb3ZpZGUgY29udGV4dHVhbCBmZWVkYmFjayB3aXRob3V0IGJlaW5nIGludHJ1c2l2ZS4gR2VuZXJhbGx5IG5vbi1pbnRlcnJ1cHRpdmUuIEV4YW1wbGU6IFRvb2x0aXBzLCBTbmFja2Jhci5cbiRlbGV2YXRpb24tc21hbGw6IDAgMXB4IDJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDUpLCAwIDJweCAzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA0KSwgMCA2cHggNnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMyksIDAgOHB4IDhweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vLyBGb3IgY29tcG9uZW50cyB0aGF0IG9mZmVyIGFkZGl0aW9uYWwgYWN0aW9ucy4gRXhhbXBsZTogTWVudXMsIENvbW1hbmQgUGFsZXR0ZVxuJGVsZXZhdGlvbi1tZWRpdW06IDAgMnB4IDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDUpLCAwIDRweCA1cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA0KSwgMCAxMnB4IDEycHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAzKSwgMCAxNnB4IDE2cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKTtcblxuLy8gRm9yIGNvbXBvbmVudHMgdGhhdCBjb25maXJtIGRlY2lzaW9ucyBvciBoYW5kbGUgbmVjZXNzYXJ5IGludGVycnVwdGlvbnMuIEV4YW1wbGU6IE1vZGFscy5cbiRlbGV2YXRpb24tbGFyZ2U6IDAgNXB4IDE1cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA4KSwgMCAxNXB4IDI3cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA3KSwgMCAzMHB4IDM2cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA0KSwgMCA1MHB4IDQzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKTtcblxuLyoqXG4gKiBEaW1lbnNpb25zLlxuICovXG5cbiRpY29uLXNpemU6IDI0cHg7XG4kYnV0dG9uLXNpemU6IDM2cHg7XG4kYnV0dG9uLXNpemUtbmV4dC1kZWZhdWx0LTQwcHg6IDQwcHg7IC8vIHRyYW5zaXRpb25hcnkgdmFyaWFibGUgZm9yIG5leHQgZGVmYXVsdCBidXR0b24gc2l6ZVxuJGJ1dHRvbi1zaXplLXNtYWxsOiAyNHB4O1xuJGJ1dHRvbi1zaXplLWNvbXBhY3Q6IDMycHg7XG4kaGVhZGVyLWhlaWdodDogNjRweDtcbiRwYW5lbC1oZWFkZXItaGVpZ2h0OiAkZ3JpZC11bml0LTYwO1xuJG5hdi1zaWRlYmFyLXdpZHRoOiAzMDBweDtcbiRhZG1pbi1iYXItaGVpZ2h0OiAzMnB4O1xuJGFkbWluLWJhci1oZWlnaHQtYmlnOiA0NnB4O1xuJGFkbWluLXNpZGViYXItd2lkdGg6IDE2MHB4O1xuJGFkbWluLXNpZGViYXItd2lkdGgtYmlnOiAxOTBweDtcbiRhZG1pbi1zaWRlYmFyLXdpZHRoLWNvbGxhcHNlZDogMzZweDtcbiRtb2RhbC1taW4td2lkdGg6IDM1MHB4O1xuJG1vZGFsLXdpZHRoLXNtYWxsOiAzODRweDtcbiRtb2RhbC13aWR0aC1tZWRpdW06IDUxMnB4O1xuJG1vZGFsLXdpZHRoLWxhcmdlOiA4NDBweDtcbiRzcGlubmVyLXNpemU6IDE2cHg7XG4kY2FudmFzLXBhZGRpbmc6ICRncmlkLXVuaXQtMjA7XG4kcGFsZXR0ZS1tYXgtaGVpZ2h0OiAzNjhweDtcblxuLyoqXG4gKiBNb2JpbGUgc3BlY2lmaWMgc3R5bGVzXG4gKi9cbiRtb2JpbGUtdGV4dC1taW4tZm9udC1zaXplOiAxNnB4OyAvLyBBbnkgZm9udCBzaXplIGJlbG93IDE2cHggd2lsbCBjYXVzZSBNb2JpbGUgU2FmYXJpIHRvIFwiem9vbSBpblwiLlxuXG4vKipcbiAqIEVkaXRvciBzdHlsZXMuXG4gKi9cblxuJHNpZGViYXItd2lkdGg6IDI4MHB4O1xuJGNvbnRlbnQtd2lkdGg6IDg0MHB4O1xuJHdpZGUtY29udGVudC13aWR0aDogMTEwMHB4O1xuJHdpZGdldC1hcmVhLXdpZHRoOiA3MDBweDtcbiRzZWNvbmRhcnktc2lkZWJhci13aWR0aDogMzUwcHg7XG4kZWRpdG9yLWZvbnQtc2l6ZTogMTZweDtcbiRkZWZhdWx0LWJsb2NrLW1hcmdpbjogMjhweDsgLy8gVGhpcyB2YWx1ZSBwcm92aWRlcyBhIGNvbnNpc3RlbnQsIGNvbnRpZ3VvdXMgc3BhY2luZyBiZXR3ZWVuIGJsb2Nrcy5cbiR0ZXh0LWVkaXRvci1mb250LXNpemU6IDE1cHg7XG4kZWRpdG9yLWxpbmUtaGVpZ2h0OiAxLjg7XG4kZWRpdG9yLWh0bWwtZm9udDogJGZvbnQtZmFtaWx5LW1vbm87XG5cbi8qKlxuICogQmxvY2sgJiBFZGl0b3IgVUkuXG4gKi9cblxuJGJsb2NrLXRvb2xiYXItaGVpZ2h0OiAkZ3JpZC11bml0LTYwO1xuJGJvcmRlci13aWR0aDogMXB4O1xuJGJvcmRlci13aWR0aC1mb2N1cy1mYWxsYmFjazogMnB4OyAvLyBUaGlzIGV4aXN0cyBhcyBhIGZhbGxiYWNrLCBhbmQgaXMgaWRlYWxseSBvdmVycmlkZGVuIGJ5IHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgdW5sZXNzIGluIHNvbWUgU0FTUyBtYXRoIGNhc2VzLlxuJGJvcmRlci13aWR0aC10YWI6IDEuNXB4O1xuJGhlbHB0ZXh0LWZvbnQtc2l6ZTogMTJweDtcbiRyYWRpby1pbnB1dC1zaXplOiAxNnB4O1xuJHJhZGlvLWlucHV0LXNpemUtc206IDI0cHg7IC8vIFdpZHRoICYgaGVpZ2h0IGZvciBzbWFsbCB2aWV3cG9ydHMuXG5cbi8vIERlcHJlY2F0ZWQsIHBsZWFzZSBhdm9pZCB1c2luZyB0aGVzZS5cbiRibG9jay1wYWRkaW5nOiAxNHB4OyAvLyBVc2VkIHRvIGRlZmluZSBzcGFjZSBiZXR3ZWVuIGJsb2NrIGZvb3RwcmludCBhbmQgc3Vycm91bmRpbmcgYm9yZGVycy5cbiRyYWRpdXMtYmxvY2stdWk6ICRyYWRpdXMtc21hbGw7XG4kc2hhZG93LXBvcG92ZXI6ICRlbGV2YXRpb24teC1zbWFsbDtcbiRzaGFkb3ctbW9kYWw6ICRlbGV2YXRpb24tbGFyZ2U7XG4kZGVmYXVsdC1mb250LXNpemU6ICRmb250LXNpemUtbWVkaXVtO1xuXG4vKipcbiAqIEJsb2NrIHBhZGRpbmdzLlxuICovXG5cbi8vIFBhZGRpbmcgZm9yIGJsb2NrcyB3aXRoIGEgYmFja2dyb3VuZCBjb2xvciAoZS5nLiBwYXJhZ3JhcGggb3IgZ3JvdXApLlxuJGJsb2NrLWJnLXBhZGRpbmctLXY6IDEuMjVlbTtcbiRibG9jay1iZy1wYWRkaW5nLS1oOiAyLjM3NWVtO1xuXG5cbi8qKlxuICogUmVhY3QgTmF0aXZlIHNwZWNpZmljLlxuICogVGhlc2UgdmFyaWFibGVzIGRvIG5vdCBhcHBlYXIgdG8gYmUgdXNlZCBhbnl3aGVyZSBlbHNlLlxuICovXG5cbi8vIERpbWVuc2lvbnMuXG4kbW9iaWxlLWhlYWRlci10b29sYmFyLWhlaWdodDogNDRweDtcbiRtb2JpbGUtaGVhZGVyLXRvb2xiYXItZXhwYW5kZWQtaGVpZ2h0OiA1MnB4O1xuJG1vYmlsZS1mbG9hdGluZy10b29sYmFyLWhlaWdodDogNDRweDtcbiRtb2JpbGUtZmxvYXRpbmctdG9vbGJhci1tYXJnaW46IDhweDtcbiRtb2JpbGUtY29sb3Itc3dhdGNoOiA0OHB4O1xuXG4vLyBCbG9jayBVSS5cbiRtb2JpbGUtYmxvY2stdG9vbGJhci1oZWlnaHQ6IDQ0cHg7XG4kZGltbWVkLW9wYWNpdHk6IDE7XG4kYmxvY2stZWRnZS10by1jb250ZW50OiAxNnB4O1xuJHNvbGlkLWJvcmRlci1zcGFjZTogMTJweDtcbiRkYXNoZWQtYm9yZGVyLXNwYWNlOiA2cHg7XG4kYmxvY2stc2VsZWN0ZWQtbWFyZ2luOiAzcHg7XG4kYmxvY2stc2VsZWN0ZWQtYm9yZGVyLXdpZHRoOiAxcHg7XG4kYmxvY2stc2VsZWN0ZWQtcGFkZGluZzogMDtcbiRibG9jay1zZWxlY3RlZC1jaGlsZC1tYXJnaW46IDVweDtcbiRibG9jay1zZWxlY3RlZC10by1jb250ZW50OiAkYmxvY2stZWRnZS10by1jb250ZW50IC0gJGJsb2NrLXNlbGVjdGVkLW1hcmdpbiAtICRibG9jay1zZWxlY3RlZC1ib3JkZXItd2lkdGg7XG4iLCIvKipcbiAqIENvbG9yc1xuICovXG5cbi8vIFdvcmRQcmVzcyBncmF5cy5cbiRibGFjazogIzAwMDtcdFx0XHQvLyBVc2Ugb25seSB3aGVuIHlvdSB0cnVseSBuZWVkIHB1cmUgYmxhY2suIEZvciBVSSwgdXNlICRncmF5LTkwMC5cbiRncmF5LTkwMDogIzFlMWUxZTtcbiRncmF5LTgwMDogIzJmMmYyZjtcbiRncmF5LTcwMDogIzc1NzU3NTtcdFx0Ly8gTWVldHMgNC42OjEgKDQuNToxIGlzIG1pbmltdW0pIHRleHQgY29udHJhc3QgYWdhaW5zdCB3aGl0ZS5cbiRncmF5LTYwMDogIzk0OTQ5NDtcdFx0Ly8gTWVldHMgMzoxIFVJIG9yIGxhcmdlIHRleHQgY29udHJhc3QgYWdhaW5zdCB3aGl0ZS5cbiRncmF5LTQwMDogI2NjYztcbiRncmF5LTMwMDogI2RkZDtcdFx0Ly8gVXNlZCBmb3IgbW9zdCBib3JkZXJzLlxuJGdyYXktMjAwOiAjZTBlMGUwO1x0XHQvLyBVc2VkIHNwYXJpbmdseSBmb3IgbGlnaHQgYm9yZGVycy5cbiRncmF5LTEwMDogI2YwZjBmMDtcdFx0Ly8gVXNlZCBmb3IgbGlnaHQgZ3JheSBiYWNrZ3JvdW5kcy5cbiR3aGl0ZTogI2ZmZjtcblxuLy8gT3BhY2l0aWVzICYgYWRkaXRpb25hbCBjb2xvcnMuXG4kZGFyay1ncmF5LXBsYWNlaG9sZGVyOiByZ2JhKCRncmF5LTkwMCwgMC42Mik7XG4kbWVkaXVtLWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJGdyYXktOTAwLCAwLjU1KTtcbiRsaWdodC1ncmF5LXBsYWNlaG9sZGVyOiByZ2JhKCR3aGl0ZSwgMC42NSk7XG5cbi8vIEFsZXJ0IGNvbG9ycy5cbiRhbGVydC15ZWxsb3c6ICNmMGI4NDk7XG4kYWxlcnQtcmVkOiAjY2MxODE4O1xuJGFsZXJ0LWdyZWVuOiAjNGFiODY2O1xuXG4vLyBEZXByZWNhdGVkLCBwbGVhc2UgYXZvaWQgdXNpbmcgdGhlc2UuXG4kZGFyay10aGVtZS1mb2N1czogJHdoaXRlO1x0Ly8gRm9jdXMgY29sb3Igd2hlbiB0aGUgdGhlbWUgaXMgZGFyay5cbiIsIkB1c2UgXCJAd29yZHByZXNzL2Jhc2Utc3R5bGVzL3ZhcmlhYmxlc1wiO1xuXG4uYm9vdC1zaWRlYmFyX19zY3JvbGxhYmxlIHtcblx0b3ZlcmZsb3c6IGF1dG87XG5cdGhlaWdodDogMTAwJTtcblx0cG9zaXRpb246IHJlbGF0aXZlO1xuXHRkaXNwbGF5OiBmbGV4O1xuXHRmbGV4LWRpcmVjdGlvbjogY29sdW1uO1xufVxuXG4uYm9vdC1zaWRlYmFyX19jb250ZW50IHtcblx0ZmxleC1ncm93OiAxO1xuXHRjb250YWluOiBjb250ZW50O1xuXHRwb3NpdGlvbjogcmVsYXRpdmU7XG59XG5cbi5ib290LXNpZGViYXJfX2Zvb3RlciB7XG5cdHBhZGRpbmc6IHZhcmlhYmxlcy4kZ3JpZC11bml0LTIwIHZhcmlhYmxlcy4kZ3JpZC11bml0LTEwIHZhcmlhYmxlcy4kZ3JpZC11bml0LTEwIHZhcmlhYmxlcy4kZ3JpZC11bml0LTIwO1xufVxuIl19 */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css8));
function Sidebar() {
  return /* @__PURE__ */ (0, import_jsx_runtime25.jsxs)("div", { className: "boot-sidebar__scrollable", children: [
    /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(site_hub_default, {}),
    /* @__PURE__ */ (0, import_jsx_runtime25.jsx)("div", { className: "boot-sidebar__content", children: /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(navigation_default, {}) }),
    /* @__PURE__ */ (0, import_jsx_runtime25.jsx)("div", { className: "boot-sidebar__footer", children: /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(SaveButton, {}) })
  ] });
}

// packages/boot/build-module/components/save-panel/index.js
var import_element9 = __toESM(require_element());
var import_components11 = __toESM(require_components());
var import_editor3 = __toESM(require_editor());
var import_i18n7 = __toESM(require_i18n());
var import_jsx_runtime26 = __toESM(require_jsx_runtime());
function SavePanel() {
  const [isOpen, setIsOpen] = (0, import_element9.useState)(false);
  useSaveShortcut({
    openSavePanel: () => setIsOpen(true)
  });
  if (!isOpen) {
    return false;
  }
  return /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
    import_components11.Modal,
    {
      className: "edit-site-save-panel__modal",
      onRequestClose: () => setIsOpen(false),
      title: (0, import_i18n7.__)("Review changes"),
      size: "small",
      children: /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
        import_editor3.EntitiesSavedStates,
        {
          close: () => setIsOpen(false),
          variant: "inline"
        }
      )
    }
  );
}

// packages/boot/build-module/components/canvas-renderer/index.js
var import_element11 = __toESM(require_element());

// packages/boot/build-module/components/canvas/index.js
var import_element10 = __toESM(require_element());
var import_components13 = __toESM(require_components());
import { useNavigate } from "@wordpress/route";

// packages/boot/build-module/components/canvas/back-button.js
var import_components12 = __toESM(require_components());
var import_compose3 = __toESM(require_compose());
var import_i18n8 = __toESM(require_i18n());
var import_jsx_runtime27 = __toESM(require_jsx_runtime());
var css9 = `/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
.boot-canvas-back-button {
  position: absolute;
  top: 0;
  left: 0;
  height: 64px;
  width: 64px;
  z-index: 100;
}

.boot-canvas-back-button__container {
  position: relative;
  width: 100%;
  height: 100%;
}

.boot-canvas-back-button__link.components-button {
  width: 64px;
  height: 64px;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  background: var(--wpds-color-bg-surface-neutral-weak);
  text-decoration: none;
  padding: 0;
  border-radius: 0;
}
@media not (prefers-reduced-motion) {
  .boot-canvas-back-button__link.components-button {
    transition: outline 0.1s ease-out;
  }
}
.boot-canvas-back-button__link.components-button:focus:not(:active) {
  outline: var(--wpds-border-width-interactive-focus) solid var(--wpds-color-stroke-focus-brand);
  outline-offset: calc(-1 * var(--wpds-border-width-interactive-focus));
}

.boot-canvas-back-button__icon {
  position: absolute;
  top: 0;
  left: 0;
  width: 64px;
  height: 64px;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: hsl(0, 0%, 80%);
  pointer-events: none;
}
.boot-canvas-back-button__icon svg {
  fill: currentColor;
}
.boot-canvas-back-button__icon.has-site-icon {
  background-color: hsla(0, 0%, 100%, 0.6);
  -webkit-backdrop-filter: saturate(180%) blur(15px);
  backdrop-filter: saturate(180%) blur(15px);
}

.interface-interface-skeleton__header {
  margin-top: 0 !important;
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy9ib290L3NyYy9jb21wb25lbnRzL2NhbnZhcyIsInNvdXJjZXMiOlsiLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX3ZhcmlhYmxlcy5zY3NzIiwiLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX2NvbG9ycy5zY3NzIiwiYmFjay1idXR0b24uc2NzcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQ0FBO0FBQUE7QUFBQTtBRFVBO0FBQUE7QUFBQTtBQU9BO0FBQUE7QUFBQTtBQTZCQTtBQUFBO0FBQUE7QUFBQTtBQWlCQTtBQUFBO0FBQUE7QUFXQTtBQUFBO0FBQUE7QUFnQkE7QUFBQTtBQUFBO0FBeUJBO0FBQUE7QUFBQTtBQUtBO0FBQUE7QUFBQTtBQWVBO0FBQUE7QUFBQTtBQW1CQTtBQUFBO0FBQUE7QUFTQTtBQUFBO0FBQUE7QUFBQTtBRWpLQTtFQUNDO0VBQ0E7RUFDQTtFQUNBLFFGNkZlO0VFNUZmLE9GNEZlO0VFM0ZmOzs7QUFHRDtFQUNDO0VBQ0E7RUFDQTs7O0FBR0Q7RUFDQyxPRmlGZTtFRWhGZixRRmdGZTtFRS9FZjtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTs7QUFFQTtFQVhEO0lBWUU7OztBQUdEO0VBQ0MsU0FDQztFQUVEOzs7QUFJRjtFQUNDO0VBQ0E7RUFDQTtFQUNBLE9GdURlO0VFdERmLFFGc0RlO0VFckRmO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7O0FBRUE7RUFDQzs7QUFHRDtFQUNDO0VBQ0E7RUFDQTs7O0FBS0Y7RUFDQyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogU0NTUyBWYXJpYWJsZXMuXG4gKlxuICogUGxlYXNlIHVzZSB2YXJpYWJsZXMgZnJvbSB0aGlzIHNoZWV0IHRvIGVuc3VyZSBjb25zaXN0ZW5jeSBhY3Jvc3MgdGhlIFVJLlxuICogRG9uJ3QgYWRkIHRvIHRoaXMgc2hlZXQgdW5sZXNzIHlvdSdyZSBwcmV0dHkgc3VyZSB0aGUgdmFsdWUgd2lsbCBiZSByZXVzZWQgaW4gbWFueSBwbGFjZXMuXG4gKiBGb3IgZXhhbXBsZSwgZG9uJ3QgYWRkIHJ1bGVzIHRvIHRoaXMgc2hlZXQgdGhhdCBhZmZlY3QgYmxvY2sgdmlzdWFscy4gSXQncyBwdXJlbHkgZm9yIFVJLlxuICovXG5cbkB1c2UgXCIuL2NvbG9yc1wiO1xuXG4vKipcbiAqIEZvbnRzICYgYmFzaWMgdmFyaWFibGVzLlxuICovXG5cbiRkZWZhdWx0LWZvbnQ6IC1hcHBsZS1zeXN0ZW0sIEJsaW5rTWFjU3lzdGVtRm9udCxcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLFwiSGVsdmV0aWNhIE5ldWVcIiwgc2Fucy1zZXJpZjsgLy8gVG9kbzogZGVwcmVjYXRlIGluIGZhdm9yIG9mICRmYW1pbHkgdmFyaWFibGVzXG4kZGVmYXVsdC1saW5lLWhlaWdodDogMS40OyAvLyBUb2RvOiBkZXByZWNhdGUgaW4gZmF2b3Igb2YgJGxpbmUtaGVpZ2h0IHRva2Vuc1xuXG4vKipcbiAqIFR5cG9ncmFwaHlcbiAqL1xuXG4vLyBTaXplc1xuJGZvbnQtc2l6ZS14LXNtYWxsOiAxMXB4O1xuJGZvbnQtc2l6ZS1zbWFsbDogMTJweDtcbiRmb250LXNpemUtbWVkaXVtOiAxM3B4O1xuJGZvbnQtc2l6ZS1sYXJnZTogMTVweDtcbiRmb250LXNpemUteC1sYXJnZTogMjBweDtcbiRmb250LXNpemUtMngtbGFyZ2U6IDMycHg7XG5cbi8vIExpbmUgaGVpZ2h0c1xuJGZvbnQtbGluZS1oZWlnaHQteC1zbWFsbDogMTZweDtcbiRmb250LWxpbmUtaGVpZ2h0LXNtYWxsOiAyMHB4O1xuJGZvbnQtbGluZS1oZWlnaHQtbWVkaXVtOiAyNHB4O1xuJGZvbnQtbGluZS1oZWlnaHQtbGFyZ2U6IDI4cHg7XG4kZm9udC1saW5lLWhlaWdodC14LWxhcmdlOiAzMnB4O1xuJGZvbnQtbGluZS1oZWlnaHQtMngtbGFyZ2U6IDQwcHg7XG5cbi8vIFdlaWdodHNcbiRmb250LXdlaWdodC1yZWd1bGFyOiA0MDA7XG4kZm9udC13ZWlnaHQtbWVkaXVtOiA0OTk7IC8vIGVuc3VyZXMgZmFsbGJhY2sgdG8gNDAwIChpbnN0ZWFkIG9mIDYwMClcblxuLy8gRmFtaWxpZXNcbiRmb250LWZhbWlseS1oZWFkaW5nczogLWFwcGxlLXN5c3RlbSwgXCJzeXN0ZW0tdWlcIiwgXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCwgXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmO1xuJGZvbnQtZmFtaWx5LWJvZHk6IC1hcHBsZS1zeXN0ZW0sIFwic3lzdGVtLXVpXCIsIFwiU2Vnb2UgVUlcIiwgUm9ib3RvLCBPeHlnZW4tU2FucywgVWJ1bnR1LCBDYW50YXJlbGwsIFwiSGVsdmV0aWNhIE5ldWVcIiwgc2Fucy1zZXJpZjtcbiRmb250LWZhbWlseS1tb25vOiBNZW5sbywgQ29uc29sYXMsIG1vbmFjbywgbW9ub3NwYWNlO1xuXG4vKipcbiAqIEdyaWQgU3lzdGVtLlxuICogaHR0cHM6Ly9tYWtlLndvcmRwcmVzcy5vcmcvZGVzaWduLzIwMTkvMTAvMzEvcHJvcG9zYWwtYS1jb25zaXN0ZW50LXNwYWNpbmctc3lzdGVtLWZvci13b3JkcHJlc3MvXG4gKi9cblxuJGdyaWQtdW5pdDogOHB4O1xuJGdyaWQtdW5pdC0wNTogMC41ICogJGdyaWQtdW5pdDtcdC8vIDRweFxuJGdyaWQtdW5pdC0xMDogMSAqICRncmlkLXVuaXQ7XHRcdC8vIDhweFxuJGdyaWQtdW5pdC0xNTogMS41ICogJGdyaWQtdW5pdDtcdC8vIDEycHhcbiRncmlkLXVuaXQtMjA6IDIgKiAkZ3JpZC11bml0O1x0XHQvLyAxNnB4XG4kZ3JpZC11bml0LTMwOiAzICogJGdyaWQtdW5pdDtcdFx0Ly8gMjRweFxuJGdyaWQtdW5pdC00MDogNCAqICRncmlkLXVuaXQ7XHRcdC8vIDMycHhcbiRncmlkLXVuaXQtNTA6IDUgKiAkZ3JpZC11bml0O1x0XHQvLyA0MHB4XG4kZ3JpZC11bml0LTYwOiA2ICogJGdyaWQtdW5pdDtcdFx0Ly8gNDhweFxuJGdyaWQtdW5pdC03MDogNyAqICRncmlkLXVuaXQ7XHRcdC8vIDU2cHhcbiRncmlkLXVuaXQtODA6IDggKiAkZ3JpZC11bml0O1x0XHQvLyA2NHB4XG5cbi8qKlxuICogUmFkaXVzIHNjYWxlLlxuICovXG5cbiRyYWRpdXMteC1zbWFsbDogMXB4OyAgIC8vIEFwcGxpZWQgdG8gZWxlbWVudHMgbGlrZSBidXR0b25zIG5lc3RlZCB3aXRoaW4gcHJpbWl0aXZlcyBsaWtlIGlucHV0cy5cbiRyYWRpdXMtc21hbGw6IDJweDsgICAgIC8vIEFwcGxpZWQgdG8gbW9zdCBwcmltaXRpdmVzLlxuJHJhZGl1cy1tZWRpdW06IDRweDsgICAgLy8gQXBwbGllZCB0byBjb250YWluZXJzIHdpdGggc21hbGxlciBwYWRkaW5nLlxuJHJhZGl1cy1sYXJnZTogOHB4OyAgICAgLy8gQXBwbGllZCB0byBjb250YWluZXJzIHdpdGggbGFyZ2VyIHBhZGRpbmcuXG4kcmFkaXVzLWZ1bGw6IDk5OTlweDsgICAvLyBGb3IgcGlsbHMuXG4kcmFkaXVzLXJvdW5kOiA1MCU7ICAgICAvLyBGb3IgY2lyY2xlcyBhbmQgb3ZhbHMuXG5cbi8qKlxuICogRWxldmF0aW9uIHNjYWxlLlxuICovXG5cbi8vIEZvciBzZWN0aW9ucyBhbmQgY29udGFpbmVycyB0aGF0IGdyb3VwIHJlbGF0ZWQgY29udGVudCBhbmQgY29udHJvbHMsIHdoaWNoIG1heSBvdmVybGFwIG90aGVyIGNvbnRlbnQuIEV4YW1wbGU6IFByZXZpZXcgRnJhbWUuXG4kZWxldmF0aW9uLXgtc21hbGw6IDAgMXB4IDFweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDMpLCAwIDFweCAycHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKSwgMCAzcHggM3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMiksIDAgNHB4IDRweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDEpO1xuXG4vLyBGb3IgY29tcG9uZW50cyB0aGF0IHByb3ZpZGUgY29udGV4dHVhbCBmZWVkYmFjayB3aXRob3V0IGJlaW5nIGludHJ1c2l2ZS4gR2VuZXJhbGx5IG5vbi1pbnRlcnJ1cHRpdmUuIEV4YW1wbGU6IFRvb2x0aXBzLCBTbmFja2Jhci5cbiRlbGV2YXRpb24tc21hbGw6IDAgMXB4IDJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDUpLCAwIDJweCAzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA0KSwgMCA2cHggNnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMyksIDAgOHB4IDhweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vLyBGb3IgY29tcG9uZW50cyB0aGF0IG9mZmVyIGFkZGl0aW9uYWwgYWN0aW9ucy4gRXhhbXBsZTogTWVudXMsIENvbW1hbmQgUGFsZXR0ZVxuJGVsZXZhdGlvbi1tZWRpdW06IDAgMnB4IDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDUpLCAwIDRweCA1cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA0KSwgMCAxMnB4IDEycHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAzKSwgMCAxNnB4IDE2cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKTtcblxuLy8gRm9yIGNvbXBvbmVudHMgdGhhdCBjb25maXJtIGRlY2lzaW9ucyBvciBoYW5kbGUgbmVjZXNzYXJ5IGludGVycnVwdGlvbnMuIEV4YW1wbGU6IE1vZGFscy5cbiRlbGV2YXRpb24tbGFyZ2U6IDAgNXB4IDE1cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA4KSwgMCAxNXB4IDI3cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA3KSwgMCAzMHB4IDM2cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA0KSwgMCA1MHB4IDQzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKTtcblxuLyoqXG4gKiBEaW1lbnNpb25zLlxuICovXG5cbiRpY29uLXNpemU6IDI0cHg7XG4kYnV0dG9uLXNpemU6IDM2cHg7XG4kYnV0dG9uLXNpemUtbmV4dC1kZWZhdWx0LTQwcHg6IDQwcHg7IC8vIHRyYW5zaXRpb25hcnkgdmFyaWFibGUgZm9yIG5leHQgZGVmYXVsdCBidXR0b24gc2l6ZVxuJGJ1dHRvbi1zaXplLXNtYWxsOiAyNHB4O1xuJGJ1dHRvbi1zaXplLWNvbXBhY3Q6IDMycHg7XG4kaGVhZGVyLWhlaWdodDogNjRweDtcbiRwYW5lbC1oZWFkZXItaGVpZ2h0OiAkZ3JpZC11bml0LTYwO1xuJG5hdi1zaWRlYmFyLXdpZHRoOiAzMDBweDtcbiRhZG1pbi1iYXItaGVpZ2h0OiAzMnB4O1xuJGFkbWluLWJhci1oZWlnaHQtYmlnOiA0NnB4O1xuJGFkbWluLXNpZGViYXItd2lkdGg6IDE2MHB4O1xuJGFkbWluLXNpZGViYXItd2lkdGgtYmlnOiAxOTBweDtcbiRhZG1pbi1zaWRlYmFyLXdpZHRoLWNvbGxhcHNlZDogMzZweDtcbiRtb2RhbC1taW4td2lkdGg6IDM1MHB4O1xuJG1vZGFsLXdpZHRoLXNtYWxsOiAzODRweDtcbiRtb2RhbC13aWR0aC1tZWRpdW06IDUxMnB4O1xuJG1vZGFsLXdpZHRoLWxhcmdlOiA4NDBweDtcbiRzcGlubmVyLXNpemU6IDE2cHg7XG4kY2FudmFzLXBhZGRpbmc6ICRncmlkLXVuaXQtMjA7XG4kcGFsZXR0ZS1tYXgtaGVpZ2h0OiAzNjhweDtcblxuLyoqXG4gKiBNb2JpbGUgc3BlY2lmaWMgc3R5bGVzXG4gKi9cbiRtb2JpbGUtdGV4dC1taW4tZm9udC1zaXplOiAxNnB4OyAvLyBBbnkgZm9udCBzaXplIGJlbG93IDE2cHggd2lsbCBjYXVzZSBNb2JpbGUgU2FmYXJpIHRvIFwiem9vbSBpblwiLlxuXG4vKipcbiAqIEVkaXRvciBzdHlsZXMuXG4gKi9cblxuJHNpZGViYXItd2lkdGg6IDI4MHB4O1xuJGNvbnRlbnQtd2lkdGg6IDg0MHB4O1xuJHdpZGUtY29udGVudC13aWR0aDogMTEwMHB4O1xuJHdpZGdldC1hcmVhLXdpZHRoOiA3MDBweDtcbiRzZWNvbmRhcnktc2lkZWJhci13aWR0aDogMzUwcHg7XG4kZWRpdG9yLWZvbnQtc2l6ZTogMTZweDtcbiRkZWZhdWx0LWJsb2NrLW1hcmdpbjogMjhweDsgLy8gVGhpcyB2YWx1ZSBwcm92aWRlcyBhIGNvbnNpc3RlbnQsIGNvbnRpZ3VvdXMgc3BhY2luZyBiZXR3ZWVuIGJsb2Nrcy5cbiR0ZXh0LWVkaXRvci1mb250LXNpemU6IDE1cHg7XG4kZWRpdG9yLWxpbmUtaGVpZ2h0OiAxLjg7XG4kZWRpdG9yLWh0bWwtZm9udDogJGZvbnQtZmFtaWx5LW1vbm87XG5cbi8qKlxuICogQmxvY2sgJiBFZGl0b3IgVUkuXG4gKi9cblxuJGJsb2NrLXRvb2xiYXItaGVpZ2h0OiAkZ3JpZC11bml0LTYwO1xuJGJvcmRlci13aWR0aDogMXB4O1xuJGJvcmRlci13aWR0aC1mb2N1cy1mYWxsYmFjazogMnB4OyAvLyBUaGlzIGV4aXN0cyBhcyBhIGZhbGxiYWNrLCBhbmQgaXMgaWRlYWxseSBvdmVycmlkZGVuIGJ5IHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgdW5sZXNzIGluIHNvbWUgU0FTUyBtYXRoIGNhc2VzLlxuJGJvcmRlci13aWR0aC10YWI6IDEuNXB4O1xuJGhlbHB0ZXh0LWZvbnQtc2l6ZTogMTJweDtcbiRyYWRpby1pbnB1dC1zaXplOiAxNnB4O1xuJHJhZGlvLWlucHV0LXNpemUtc206IDI0cHg7IC8vIFdpZHRoICYgaGVpZ2h0IGZvciBzbWFsbCB2aWV3cG9ydHMuXG5cbi8vIERlcHJlY2F0ZWQsIHBsZWFzZSBhdm9pZCB1c2luZyB0aGVzZS5cbiRibG9jay1wYWRkaW5nOiAxNHB4OyAvLyBVc2VkIHRvIGRlZmluZSBzcGFjZSBiZXR3ZWVuIGJsb2NrIGZvb3RwcmludCBhbmQgc3Vycm91bmRpbmcgYm9yZGVycy5cbiRyYWRpdXMtYmxvY2stdWk6ICRyYWRpdXMtc21hbGw7XG4kc2hhZG93LXBvcG92ZXI6ICRlbGV2YXRpb24teC1zbWFsbDtcbiRzaGFkb3ctbW9kYWw6ICRlbGV2YXRpb24tbGFyZ2U7XG4kZGVmYXVsdC1mb250LXNpemU6ICRmb250LXNpemUtbWVkaXVtO1xuXG4vKipcbiAqIEJsb2NrIHBhZGRpbmdzLlxuICovXG5cbi8vIFBhZGRpbmcgZm9yIGJsb2NrcyB3aXRoIGEgYmFja2dyb3VuZCBjb2xvciAoZS5nLiBwYXJhZ3JhcGggb3IgZ3JvdXApLlxuJGJsb2NrLWJnLXBhZGRpbmctLXY6IDEuMjVlbTtcbiRibG9jay1iZy1wYWRkaW5nLS1oOiAyLjM3NWVtO1xuXG5cbi8qKlxuICogUmVhY3QgTmF0aXZlIHNwZWNpZmljLlxuICogVGhlc2UgdmFyaWFibGVzIGRvIG5vdCBhcHBlYXIgdG8gYmUgdXNlZCBhbnl3aGVyZSBlbHNlLlxuICovXG5cbi8vIERpbWVuc2lvbnMuXG4kbW9iaWxlLWhlYWRlci10b29sYmFyLWhlaWdodDogNDRweDtcbiRtb2JpbGUtaGVhZGVyLXRvb2xiYXItZXhwYW5kZWQtaGVpZ2h0OiA1MnB4O1xuJG1vYmlsZS1mbG9hdGluZy10b29sYmFyLWhlaWdodDogNDRweDtcbiRtb2JpbGUtZmxvYXRpbmctdG9vbGJhci1tYXJnaW46IDhweDtcbiRtb2JpbGUtY29sb3Itc3dhdGNoOiA0OHB4O1xuXG4vLyBCbG9jayBVSS5cbiRtb2JpbGUtYmxvY2stdG9vbGJhci1oZWlnaHQ6IDQ0cHg7XG4kZGltbWVkLW9wYWNpdHk6IDE7XG4kYmxvY2stZWRnZS10by1jb250ZW50OiAxNnB4O1xuJHNvbGlkLWJvcmRlci1zcGFjZTogMTJweDtcbiRkYXNoZWQtYm9yZGVyLXNwYWNlOiA2cHg7XG4kYmxvY2stc2VsZWN0ZWQtbWFyZ2luOiAzcHg7XG4kYmxvY2stc2VsZWN0ZWQtYm9yZGVyLXdpZHRoOiAxcHg7XG4kYmxvY2stc2VsZWN0ZWQtcGFkZGluZzogMDtcbiRibG9jay1zZWxlY3RlZC1jaGlsZC1tYXJnaW46IDVweDtcbiRibG9jay1zZWxlY3RlZC10by1jb250ZW50OiAkYmxvY2stZWRnZS10by1jb250ZW50IC0gJGJsb2NrLXNlbGVjdGVkLW1hcmdpbiAtICRibG9jay1zZWxlY3RlZC1ib3JkZXItd2lkdGg7XG4iLCIvKipcbiAqIENvbG9yc1xuICovXG5cbi8vIFdvcmRQcmVzcyBncmF5cy5cbiRibGFjazogIzAwMDtcdFx0XHQvLyBVc2Ugb25seSB3aGVuIHlvdSB0cnVseSBuZWVkIHB1cmUgYmxhY2suIEZvciBVSSwgdXNlICRncmF5LTkwMC5cbiRncmF5LTkwMDogIzFlMWUxZTtcbiRncmF5LTgwMDogIzJmMmYyZjtcbiRncmF5LTcwMDogIzc1NzU3NTtcdFx0Ly8gTWVldHMgNC42OjEgKDQuNToxIGlzIG1pbmltdW0pIHRleHQgY29udHJhc3QgYWdhaW5zdCB3aGl0ZS5cbiRncmF5LTYwMDogIzk0OTQ5NDtcdFx0Ly8gTWVldHMgMzoxIFVJIG9yIGxhcmdlIHRleHQgY29udHJhc3QgYWdhaW5zdCB3aGl0ZS5cbiRncmF5LTQwMDogI2NjYztcbiRncmF5LTMwMDogI2RkZDtcdFx0Ly8gVXNlZCBmb3IgbW9zdCBib3JkZXJzLlxuJGdyYXktMjAwOiAjZTBlMGUwO1x0XHQvLyBVc2VkIHNwYXJpbmdseSBmb3IgbGlnaHQgYm9yZGVycy5cbiRncmF5LTEwMDogI2YwZjBmMDtcdFx0Ly8gVXNlZCBmb3IgbGlnaHQgZ3JheSBiYWNrZ3JvdW5kcy5cbiR3aGl0ZTogI2ZmZjtcblxuLy8gT3BhY2l0aWVzICYgYWRkaXRpb25hbCBjb2xvcnMuXG4kZGFyay1ncmF5LXBsYWNlaG9sZGVyOiByZ2JhKCRncmF5LTkwMCwgMC42Mik7XG4kbWVkaXVtLWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJGdyYXktOTAwLCAwLjU1KTtcbiRsaWdodC1ncmF5LXBsYWNlaG9sZGVyOiByZ2JhKCR3aGl0ZSwgMC42NSk7XG5cbi8vIEFsZXJ0IGNvbG9ycy5cbiRhbGVydC15ZWxsb3c6ICNmMGI4NDk7XG4kYWxlcnQtcmVkOiAjY2MxODE4O1xuJGFsZXJ0LWdyZWVuOiAjNGFiODY2O1xuXG4vLyBEZXByZWNhdGVkLCBwbGVhc2UgYXZvaWQgdXNpbmcgdGhlc2UuXG4kZGFyay10aGVtZS1mb2N1czogJHdoaXRlO1x0Ly8gRm9jdXMgY29sb3Igd2hlbiB0aGUgdGhlbWUgaXMgZGFyay5cbiIsIkB1c2UgXCJAd29yZHByZXNzL2Jhc2Utc3R5bGVzL3ZhcmlhYmxlc1wiO1xuXG4uYm9vdC1jYW52YXMtYmFjay1idXR0b24ge1xuXHRwb3NpdGlvbjogYWJzb2x1dGU7XG5cdHRvcDogMDtcblx0bGVmdDogMDtcblx0aGVpZ2h0OiB2YXJpYWJsZXMuJGhlYWRlci1oZWlnaHQ7XG5cdHdpZHRoOiB2YXJpYWJsZXMuJGhlYWRlci1oZWlnaHQ7XG5cdHotaW5kZXg6IDEwMDtcbn1cblxuLmJvb3QtY2FudmFzLWJhY2stYnV0dG9uX19jb250YWluZXIge1xuXHRwb3NpdGlvbjogcmVsYXRpdmU7XG5cdHdpZHRoOiAxMDAlO1xuXHRoZWlnaHQ6IDEwMCU7XG59XG5cbi5ib290LWNhbnZhcy1iYWNrLWJ1dHRvbl9fbGluay5jb21wb25lbnRzLWJ1dHRvbiB7XG5cdHdpZHRoOiB2YXJpYWJsZXMuJGhlYWRlci1oZWlnaHQ7XG5cdGhlaWdodDogdmFyaWFibGVzLiRoZWFkZXItaGVpZ2h0O1xuXHRkaXNwbGF5OiBpbmxpbmUtZmxleDtcblx0YWxpZ24taXRlbXM6IGNlbnRlcjtcblx0anVzdGlmeS1jb250ZW50OiBjZW50ZXI7XG5cdGJhY2tncm91bmQ6IHZhcigtLXdwZHMtY29sb3ItYmctc3VyZmFjZS1uZXV0cmFsLXdlYWspO1xuXHR0ZXh0LWRlY29yYXRpb246IG5vbmU7XG5cdHBhZGRpbmc6IDA7XG5cdGJvcmRlci1yYWRpdXM6IDA7XG5cblx0QG1lZGlhIG5vdCAocHJlZmVycy1yZWR1Y2VkLW1vdGlvbikge1xuXHRcdHRyYW5zaXRpb246IG91dGxpbmUgMC4xcyBlYXNlLW91dDtcblx0fVxuXG5cdCY6Zm9jdXM6bm90KDphY3RpdmUpIHtcblx0XHRvdXRsaW5lOlxuXHRcdFx0dmFyKC0td3Bkcy1ib3JkZXItd2lkdGgtaW50ZXJhY3RpdmUtZm9jdXMpIHNvbGlkXG5cdFx0XHR2YXIoLS13cGRzLWNvbG9yLXN0cm9rZS1mb2N1cy1icmFuZCk7XG5cdFx0b3V0bGluZS1vZmZzZXQ6IGNhbGMoLTEgKiB2YXIoLS13cGRzLWJvcmRlci13aWR0aC1pbnRlcmFjdGl2ZS1mb2N1cykpO1xuXHR9XG59XG5cbi5ib290LWNhbnZhcy1iYWNrLWJ1dHRvbl9faWNvbiB7XG5cdHBvc2l0aW9uOiBhYnNvbHV0ZTtcblx0dG9wOiAwO1xuXHRsZWZ0OiAwO1xuXHR3aWR0aDogdmFyaWFibGVzLiRoZWFkZXItaGVpZ2h0O1xuXHRoZWlnaHQ6IHZhcmlhYmxlcy4kaGVhZGVyLWhlaWdodDtcblx0ZGlzcGxheTogZmxleDtcblx0YWxpZ24taXRlbXM6IGNlbnRlcjtcblx0anVzdGlmeS1jb250ZW50OiBjZW50ZXI7XG5cdGJhY2tncm91bmQtY29sb3I6IGhzbGEoMCwgMCUsIDgwJSk7XG5cdHBvaW50ZXItZXZlbnRzOiBub25lO1xuXG5cdHN2ZyB7XG5cdFx0ZmlsbDogY3VycmVudENvbG9yO1xuXHR9XG5cblx0Ji5oYXMtc2l0ZS1pY29uIHtcblx0XHRiYWNrZ3JvdW5kLWNvbG9yOiBoc2xhKDAsIDAlLCAxMDAlLCAwLjYpO1xuXHRcdC13ZWJraXQtYmFja2Ryb3AtZmlsdGVyOiBzYXR1cmF0ZSgxODAlKSBibHVyKDE1cHgpO1xuXHRcdGJhY2tkcm9wLWZpbHRlcjogc2F0dXJhdGUoMTgwJSkgYmx1cigxNXB4KTtcblx0fVxufVxuXG4vLyBSZW1vdmUgdGhlIGhlYWRlciBzbGlkZS1pbiBhbmltYXRpb24gc28gdGhlIGJhY2sgbG9nbyBkb2VzIG5vdCBtb3ZlLlxuLmludGVyZmFjZS1pbnRlcmZhY2Utc2tlbGV0b25fX2hlYWRlciB7XG5cdG1hcmdpbi10b3A6IDAgIWltcG9ydGFudDtcbn1cbiJdfQ== */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css9));
var toggleHomeIconVariants = {
  edit: {
    opacity: 0,
    scale: 0.2
  },
  hover: {
    opacity: 1,
    scale: 1,
    clipPath: "inset( 22% round 2px )"
  }
};
function BootBackButton({ length }) {
  const disableMotion = (0, import_compose3.useReducedMotion)();
  const handleBack = () => {
    window.history.back();
  };
  if (length > 1) {
    return null;
  }
  const transition = {
    duration: disableMotion ? 0 : 0.3
  };
  return /* @__PURE__ */ (0, import_jsx_runtime27.jsxs)(
    import_components12.__unstableMotion.div,
    {
      className: "boot-canvas-back-button",
      animate: "edit",
      initial: "edit",
      whileHover: "hover",
      whileTap: "tap",
      transition,
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(
          import_components12.Button,
          {
            className: "boot-canvas-back-button__link",
            onClick: handleBack,
            "aria-label": (0, import_i18n8.__)("Go back"),
            __next40pxDefaultSize: true,
            children: /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(site_icon_default, {})
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(
          import_components12.__unstableMotion.div,
          {
            className: "boot-canvas-back-button__icon",
            variants: toggleHomeIconVariants,
            children: /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(import_components12.Icon, { icon: arrow_up_left_default })
          }
        )
      ]
    }
  );
}

// packages/boot/build-module/components/canvas/index.js
var import_jsx_runtime28 = __toESM(require_jsx_runtime());
function Canvas({ canvas }) {
  const [Editor, setEditor] = (0, import_element10.useState)(null);
  const navigate = useNavigate();
  (0, import_element10.useEffect)(() => {
    import("@wordpress/lazy-editor").then((module) => {
      setEditor(() => module.Editor);
    }).catch((error) => {
      console.error("Failed to load lazy editor:", error);
    });
  }, []);
  if (!Editor) {
    return /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
      "div",
      {
        style: {
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
          height: "100%",
          padding: "2rem"
        },
        children: /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(import_components13.Spinner, {})
      }
    );
  }
  const backButton = !canvas.isPreview ? ({ length }) => /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(BootBackButton, { length }) : void 0;
  return /* @__PURE__ */ (0, import_jsx_runtime28.jsxs)("div", { style: { height: "100%", position: "relative" }, children: [
    /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
      "div",
      {
        style: { height: "100%" },
        inert: canvas.isPreview ? "true" : void 0,
        children: /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
          Editor,
          {
            postType: canvas.postType,
            postId: canvas.postId,
            settings: { isPreviewMode: canvas.isPreview },
            backButton
          }
        )
      }
    ),
    canvas.isPreview && canvas.editLink && /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
      "div",
      {
        onClick: () => navigate({ to: canvas.editLink }),
        onKeyDown: (e) => {
          if (e.key === "Enter" || e.key === " ") {
            e.preventDefault();
            navigate({ to: canvas.editLink });
          }
        },
        style: {
          position: "absolute",
          inset: 0,
          cursor: "pointer",
          zIndex: 1
        },
        role: "button",
        tabIndex: 0,
        "aria-label": "Click to edit"
      }
    )
  ] });
}

// packages/boot/build-module/components/canvas-renderer/index.js
var import_jsx_runtime29 = __toESM(require_jsx_runtime());
function CanvasRenderer({
  canvas,
  routeContentModule
}) {
  const [CustomCanvas, setCustomCanvas] = (0, import_element11.useState)(null);
  (0, import_element11.useEffect)(() => {
    if (canvas === null && routeContentModule) {
      import(routeContentModule).then((module) => {
        setCustomCanvas(() => module.canvas);
      }).catch((error) => {
        console.error("Failed to load custom canvas:", error);
      });
    } else {
      setCustomCanvas(null);
    }
  }, [canvas, routeContentModule]);
  if (canvas === void 0) {
    return null;
  }
  if (canvas === null) {
    if (!CustomCanvas) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(CustomCanvas, {});
  }
  return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(Canvas, { canvas });
}

// packages/boot/build-module/components/app/use-route-title.js
var import_element12 = __toESM(require_element());
var import_data9 = __toESM(require_data());
var import_core_data5 = __toESM(require_core_data());
var import_i18n9 = __toESM(require_i18n());
var import_html_entities2 = __toESM(require_html_entities());
import { speak } from "@wordpress/a11y";
import { privateApis as routePrivateApis4 } from "@wordpress/route";
var { useLocation, useMatches: useMatches2 } = unlock(routePrivateApis4);
function useRouteTitle() {
  const location = useLocation();
  const matches = useMatches2();
  const currentMatch = matches[matches.length - 1];
  const routeTitle = currentMatch?.loaderData?.title;
  const siteTitle = (0, import_data9.useSelect)(
    (select) => select(import_core_data5.store).getEntityRecord(
      "root",
      "__unstableBase"
    )?.name,
    []
  );
  const isInitialLocationRef = (0, import_element12.useRef)(true);
  (0, import_element12.useEffect)(() => {
    isInitialLocationRef.current = false;
  }, [location]);
  (0, import_element12.useEffect)(() => {
    if (isInitialLocationRef.current) {
      return;
    }
    if (routeTitle && typeof routeTitle === "string" && siteTitle && typeof siteTitle === "string") {
      const decodedRouteTitle = (0, import_html_entities2.decodeEntities)(routeTitle);
      const decodedSiteTitle = (0, import_html_entities2.decodeEntities)(siteTitle);
      const formattedTitle = (0, import_i18n9.sprintf)(
        /* translators: Admin document title. 1: Admin screen name, 2: Site name. */
        (0, import_i18n9.__)("%1$s \u2039 %2$s \u2014 WordPress"),
        decodedRouteTitle,
        decodedSiteTitle
      );
      document.title = formattedTitle;
      if (decodedRouteTitle) {
        speak(decodedRouteTitle, "assertive");
      }
    }
  }, [routeTitle, siteTitle, location]);
}

// packages/boot/build-module/components/user-theme-provider/index.js
var import_theme = __toESM(require_theme());
var import_jsx_runtime30 = __toESM(require_jsx_runtime());
var ThemeProvider = unlock(import_theme.privateApis).ThemeProvider;
var THEME_PRIMARY_COLORS = /* @__PURE__ */ new Map([
  ["light", "#0085ba"],
  ["modern", "#3858e9"],
  ["blue", "#096484"],
  ["coffee", "#46403c"],
  ["ectoplasm", "#523f6d"],
  ["midnight", "#e14d43"],
  ["ocean", "#627c83"],
  ["sunrise", "#dd823b"]
]);
function getAdminThemePrimaryColor() {
  const theme = document.body.className.match(/admin-color-([a-z]+)/)?.[1];
  return theme && THEME_PRIMARY_COLORS.get(theme);
}
function UserThemeProvider({
  color,
  ...restProps
}) {
  const primary = getAdminThemePrimaryColor();
  return /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(ThemeProvider, { ...restProps, color: { primary, ...color } });
}

// packages/boot/build-module/components/root/index.js
var import_jsx_runtime31 = __toESM(require_jsx_runtime());
var css10 = `/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
/**
 * Typography
 */
/**
 * Breakpoints & Media Queries
 */
/**
*  Converts a hex value into the rgb equivalent.
*
* @param {string} hex - the hexadecimal value to convert
* @return {string} comma separated rgb values
*/
/**
 * Long content fade mixin
 *
 * Creates a fading overlay to signify that the content is longer
 * than the space allows.
 */
/**
 * Breakpoint mixins
 */
/**
 * Focus styles.
 */
/**
 * Applies editor left position to the selector passed as argument
 */
/**
 * Styles that are reused verbatim in a few places
 */
/**
 * Allows users to opt-out of animations via OS-level preferences.
 */
/**
 * Reset default styles for JavaScript UI based pages.
 * This is a WP-admin agnostic reset
 */
/**
 * Reset the WP Admin page styles for Gutenberg-like pages.
 */
.boot-layout {
  height: 100%;
  width: 100%;
  display: flex;
  flex-direction: row;
  color: var(--wpds-color-fg-content-neutral, #1e1e1e);
  isolation: isolate;
  background: var(--wpds-color-bg-surface-neutral-weak, #f0f0f0);
}

.boot-layout__sidebar-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 100002;
  cursor: pointer;
}

.boot-layout__sidebar {
  height: 100%;
  flex-shrink: 0;
  width: 240px;
  position: relative;
  overflow: hidden;
}
.boot-layout__sidebar.is-mobile {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  width: 300px;
  max-width: 85vw;
  background: var(--wpds-color-bg-surface-neutral-weak, #f0f0f0);
  z-index: 100003;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.2);
}

.boot-layout__mobile-sidebar-drawer {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 3;
  background: var(--wpds-color-bg-surface-neutral, #fff);
  padding: 16px;
  border-bottom: 1px solid var(--wpds-color-stroke-surface-neutral-weak, #ddd);
  display: flex;
  justify-content: flex-start;
  align-items: center;
}

.boot-layout__canvas.has-mobile-drawer {
  position: relative;
  padding-top: 65px;
}

.boot-layout__surfaces {
  display: flex;
  flex-grow: 1;
  margin: 0;
  gap: 8px;
}
@media (min-width: 782px) {
  .boot-layout__surfaces {
    margin: 8px;
  }
  .boot-layout--single-page .boot-layout__surfaces {
    margin-top: 0;
    margin-left: 0;
  }
}

.boot-layout__stage,
.boot-layout__inspector {
  flex: 1;
  overflow-y: auto;
  background: var(--wpds-color-bg-surface-neutral, #fff);
  color: var(--wpds-color-fg-content-neutral, #1e1e1e);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid var(--wpds-color-stroke-surface-neutral-weak, #ddd);
  position: relative;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100vw;
  height: 100vh;
  border-radius: 0;
  margin: 0;
}
@media (min-width: 782px) {
  .boot-layout__stage,
  .boot-layout__inspector {
    position: static;
    width: auto;
    height: auto;
    border-radius: 8px;
    margin: 0;
  }
}

.boot-layout__stage {
  z-index: 2;
}
@media (min-width: 782px) {
  .boot-layout__stage {
    z-index: auto;
  }
}

.boot-layout__inspector {
  z-index: 3;
}
@media (min-width: 782px) {
  .boot-layout__inspector {
    z-index: auto;
  }
}

.boot-layout__canvas {
  flex: 1;
  overflow-y: auto;
  background: var(--wpds-color-bg-surface-neutral, #fff);
  color: var(--wpds-color-fg-content-neutral, #1e1e1e);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid var(--wpds-color-stroke-surface-neutral-weak, #ddd);
  position: relative;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100vw;
  height: 100vh;
  z-index: 1;
  border-radius: 0;
  margin: 0;
}
@media (min-width: 782px) {
  .boot-layout__canvas {
    position: static;
    width: auto;
    height: auto;
    border-radius: 8px;
    z-index: auto;
  }
}

@media (min-width: 782px) {
  .boot-layout.has-canvas .boot-layout__stage,
  .boot-layout__inspector {
    max-width: 400px;
  }
}

.boot-layout__canvas .interface-interface-skeleton {
  position: relative;
  height: 100%;
  top: 0 !important;
  left: 0 !important;
}

.boot-layout.has-full-canvas .boot-layout__surfaces {
  margin: 0;
  gap: 0;
}

.boot-layout.has-full-canvas .boot-layout__stage,
.boot-layout.has-full-canvas .boot-layout__inspector {
  display: none;
}

.boot-layout.has-full-canvas .boot-layout__canvas {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  max-width: none;
  margin: 0;
  border-radius: 0;
  border: none;
  box-shadow: none;
  overflow: hidden;
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy9ib290L3NyYy9jb21wb25lbnRzL3Jvb3QiLCJzb3VyY2VzIjpbIi4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL192YXJpYWJsZXMuc2NzcyIsIi4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19jb2xvcnMuc2NzcyIsIi4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19taXhpbnMuc2NzcyIsIi4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19icmVha3BvaW50cy5zY3NzIiwiLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX2Z1bmN0aW9ucy5zY3NzIiwiLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX2xvbmctY29udGVudC1mYWRlLnNjc3MiLCJzdHlsZS5zY3NzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FDQUE7QUFBQTtBQUFBO0FEVUE7QUFBQTtBQUFBO0FBT0E7QUFBQTtBQUFBO0FBNkJBO0FBQUE7QUFBQTtBQUFBO0FBaUJBO0FBQUE7QUFBQTtBQVdBO0FBQUE7QUFBQTtBQWdCQTtBQUFBO0FBQUE7QUF5QkE7QUFBQTtBQUFBO0FBS0E7QUFBQTtBQUFBO0FBZUE7QUFBQTtBQUFBO0FBbUJBO0FBQUE7QUFBQTtBQVNBO0FBQUE7QUFBQTtBQUFBO0FFbktBO0FBQUE7QUFBQTtBQ0FBO0FBQUE7QUFBQTtBQ0FBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQ0FBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBSDRFQTtBQUFBO0FBQUE7QUEwREE7QUFBQTtBQUFBO0FBZ0RBO0FBQUE7QUFBQTtBQXFDQTtBQUFBO0FBQUE7QUFvQkE7QUFBQTtBQUFBO0FBd0xBO0FBQUE7QUFBQTtBQUFBO0FBZ0RBO0FBQUE7QUFBQTtBSXBkQTtFQUNDO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBOzs7QUFHRDtFQUNDO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7OztBQUdEO0VBQ0M7RUFDQTtFQUNBO0VBQ0E7RUFDQTs7QUFFQTtFQUNDO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTs7O0FBSUY7RUFDQztFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQSxTTkljO0VNSGQ7RUFDQTtFQUNBO0VBQ0E7OztBQUdEO0VBQ0M7RUFDQTs7O0FBR0Q7RUFDQztFQUNBO0VBQ0E7RUFDQSxLTmRjOztBRTBEZDtFSWhERDtJQU9FLFFOakJhOztFTW1CYjtJQUNDO0lBQ0E7Ozs7QUFLSDtBQUFBO0VBRUM7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFHQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7O0FKYUE7RUloQ0Q7QUFBQTtJQXVCRTtJQUNBO0lBQ0E7SUFDQTtJQUNBOzs7O0FBSUY7RUFDQzs7QUpBQTtFSUREO0lBSUU7Ozs7QUFJRjtFQUNDOztBSlJBO0VJT0Q7SUFJRTs7OztBQUlGO0VBQ0M7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFHQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTs7QUpsQ0E7RUllRDtJQXVCRTtJQUNBO0lBQ0E7SUFDQTtJQUNBOzs7O0FKMUNEO0VJOENEO0FBQUE7SUFHRTs7OztBQUlGO0VBQ0M7RUFDQTtFQUNBO0VBQ0E7OztBQUlEO0VBQ0M7RUFDQTs7O0FBR0Q7QUFBQTtFQUVDOzs7QUFHRDtFQUNDO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0EiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIFNDU1MgVmFyaWFibGVzLlxuICpcbiAqIFBsZWFzZSB1c2UgdmFyaWFibGVzIGZyb20gdGhpcyBzaGVldCB0byBlbnN1cmUgY29uc2lzdGVuY3kgYWNyb3NzIHRoZSBVSS5cbiAqIERvbid0IGFkZCB0byB0aGlzIHNoZWV0IHVubGVzcyB5b3UncmUgcHJldHR5IHN1cmUgdGhlIHZhbHVlIHdpbGwgYmUgcmV1c2VkIGluIG1hbnkgcGxhY2VzLlxuICogRm9yIGV4YW1wbGUsIGRvbid0IGFkZCBydWxlcyB0byB0aGlzIHNoZWV0IHRoYXQgYWZmZWN0IGJsb2NrIHZpc3VhbHMuIEl0J3MgcHVyZWx5IGZvciBVSS5cbiAqL1xuXG5AdXNlIFwiLi9jb2xvcnNcIjtcblxuLyoqXG4gKiBGb250cyAmIGJhc2ljIHZhcmlhYmxlcy5cbiAqL1xuXG4kZGVmYXVsdC1mb250OiAtYXBwbGUtc3lzdGVtLCBCbGlua01hY1N5c3RlbUZvbnQsXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCxcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7IC8vIFRvZG86IGRlcHJlY2F0ZSBpbiBmYXZvciBvZiAkZmFtaWx5IHZhcmlhYmxlc1xuJGRlZmF1bHQtbGluZS1oZWlnaHQ6IDEuNDsgLy8gVG9kbzogZGVwcmVjYXRlIGluIGZhdm9yIG9mICRsaW5lLWhlaWdodCB0b2tlbnNcblxuLyoqXG4gKiBUeXBvZ3JhcGh5XG4gKi9cblxuLy8gU2l6ZXNcbiRmb250LXNpemUteC1zbWFsbDogMTFweDtcbiRmb250LXNpemUtc21hbGw6IDEycHg7XG4kZm9udC1zaXplLW1lZGl1bTogMTNweDtcbiRmb250LXNpemUtbGFyZ2U6IDE1cHg7XG4kZm9udC1zaXplLXgtbGFyZ2U6IDIwcHg7XG4kZm9udC1zaXplLTJ4LWxhcmdlOiAzMnB4O1xuXG4vLyBMaW5lIGhlaWdodHNcbiRmb250LWxpbmUtaGVpZ2h0LXgtc21hbGw6IDE2cHg7XG4kZm9udC1saW5lLWhlaWdodC1zbWFsbDogMjBweDtcbiRmb250LWxpbmUtaGVpZ2h0LW1lZGl1bTogMjRweDtcbiRmb250LWxpbmUtaGVpZ2h0LWxhcmdlOiAyOHB4O1xuJGZvbnQtbGluZS1oZWlnaHQteC1sYXJnZTogMzJweDtcbiRmb250LWxpbmUtaGVpZ2h0LTJ4LWxhcmdlOiA0MHB4O1xuXG4vLyBXZWlnaHRzXG4kZm9udC13ZWlnaHQtcmVndWxhcjogNDAwO1xuJGZvbnQtd2VpZ2h0LW1lZGl1bTogNDk5OyAvLyBlbnN1cmVzIGZhbGxiYWNrIHRvIDQwMCAoaW5zdGVhZCBvZiA2MDApXG5cbi8vIEZhbWlsaWVzXG4kZm9udC1mYW1pbHktaGVhZGluZ3M6IC1hcHBsZS1zeXN0ZW0sIFwic3lzdGVtLXVpXCIsIFwiU2Vnb2UgVUlcIiwgUm9ib3RvLCBPeHlnZW4tU2FucywgVWJ1bnR1LCBDYW50YXJlbGwsIFwiSGVsdmV0aWNhIE5ldWVcIiwgc2Fucy1zZXJpZjtcbiRmb250LWZhbWlseS1ib2R5OiAtYXBwbGUtc3lzdGVtLCBcInN5c3RlbS11aVwiLCBcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLCBcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7XG4kZm9udC1mYW1pbHktbW9ubzogTWVubG8sIENvbnNvbGFzLCBtb25hY28sIG1vbm9zcGFjZTtcblxuLyoqXG4gKiBHcmlkIFN5c3RlbS5cbiAqIGh0dHBzOi8vbWFrZS53b3JkcHJlc3Mub3JnL2Rlc2lnbi8yMDE5LzEwLzMxL3Byb3Bvc2FsLWEtY29uc2lzdGVudC1zcGFjaW5nLXN5c3RlbS1mb3Itd29yZHByZXNzL1xuICovXG5cbiRncmlkLXVuaXQ6IDhweDtcbiRncmlkLXVuaXQtMDU6IDAuNSAqICRncmlkLXVuaXQ7XHQvLyA0cHhcbiRncmlkLXVuaXQtMTA6IDEgKiAkZ3JpZC11bml0O1x0XHQvLyA4cHhcbiRncmlkLXVuaXQtMTU6IDEuNSAqICRncmlkLXVuaXQ7XHQvLyAxMnB4XG4kZ3JpZC11bml0LTIwOiAyICogJGdyaWQtdW5pdDtcdFx0Ly8gMTZweFxuJGdyaWQtdW5pdC0zMDogMyAqICRncmlkLXVuaXQ7XHRcdC8vIDI0cHhcbiRncmlkLXVuaXQtNDA6IDQgKiAkZ3JpZC11bml0O1x0XHQvLyAzMnB4XG4kZ3JpZC11bml0LTUwOiA1ICogJGdyaWQtdW5pdDtcdFx0Ly8gNDBweFxuJGdyaWQtdW5pdC02MDogNiAqICRncmlkLXVuaXQ7XHRcdC8vIDQ4cHhcbiRncmlkLXVuaXQtNzA6IDcgKiAkZ3JpZC11bml0O1x0XHQvLyA1NnB4XG4kZ3JpZC11bml0LTgwOiA4ICogJGdyaWQtdW5pdDtcdFx0Ly8gNjRweFxuXG4vKipcbiAqIFJhZGl1cyBzY2FsZS5cbiAqL1xuXG4kcmFkaXVzLXgtc21hbGw6IDFweDsgICAvLyBBcHBsaWVkIHRvIGVsZW1lbnRzIGxpa2UgYnV0dG9ucyBuZXN0ZWQgd2l0aGluIHByaW1pdGl2ZXMgbGlrZSBpbnB1dHMuXG4kcmFkaXVzLXNtYWxsOiAycHg7ICAgICAvLyBBcHBsaWVkIHRvIG1vc3QgcHJpbWl0aXZlcy5cbiRyYWRpdXMtbWVkaXVtOiA0cHg7ICAgIC8vIEFwcGxpZWQgdG8gY29udGFpbmVycyB3aXRoIHNtYWxsZXIgcGFkZGluZy5cbiRyYWRpdXMtbGFyZ2U6IDhweDsgICAgIC8vIEFwcGxpZWQgdG8gY29udGFpbmVycyB3aXRoIGxhcmdlciBwYWRkaW5nLlxuJHJhZGl1cy1mdWxsOiA5OTk5cHg7ICAgLy8gRm9yIHBpbGxzLlxuJHJhZGl1cy1yb3VuZDogNTAlOyAgICAgLy8gRm9yIGNpcmNsZXMgYW5kIG92YWxzLlxuXG4vKipcbiAqIEVsZXZhdGlvbiBzY2FsZS5cbiAqL1xuXG4vLyBGb3Igc2VjdGlvbnMgYW5kIGNvbnRhaW5lcnMgdGhhdCBncm91cCByZWxhdGVkIGNvbnRlbnQgYW5kIGNvbnRyb2xzLCB3aGljaCBtYXkgb3ZlcmxhcCBvdGhlciBjb250ZW50LiBFeGFtcGxlOiBQcmV2aWV3IEZyYW1lLlxuJGVsZXZhdGlvbi14LXNtYWxsOiAwIDFweCAxcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAzKSwgMCAxcHggMnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMiksIDAgM3B4IDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpLCAwIDRweCA0cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAxKTtcblxuLy8gRm9yIGNvbXBvbmVudHMgdGhhdCBwcm92aWRlIGNvbnRleHR1YWwgZmVlZGJhY2sgd2l0aG91dCBiZWluZyBpbnRydXNpdmUuIEdlbmVyYWxseSBub24taW50ZXJydXB0aXZlLiBFeGFtcGxlOiBUb29sdGlwcywgU25hY2tiYXIuXG4kZWxldmF0aW9uLXNtYWxsOiAwIDFweCAycHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA1KSwgMCAycHggM3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNCksIDAgNnB4IDZweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDMpLCAwIDhweCA4cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKTtcblxuLy8gRm9yIGNvbXBvbmVudHMgdGhhdCBvZmZlciBhZGRpdGlvbmFsIGFjdGlvbnMuIEV4YW1wbGU6IE1lbnVzLCBDb21tYW5kIFBhbGV0dGVcbiRlbGV2YXRpb24tbWVkaXVtOiAwIDJweCAzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA1KSwgMCA0cHggNXB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNCksIDAgMTJweCAxMnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMyksIDAgMTZweCAxNnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMik7XG5cbi8vIEZvciBjb21wb25lbnRzIHRoYXQgY29uZmlybSBkZWNpc2lvbnMgb3IgaGFuZGxlIG5lY2Vzc2FyeSBpbnRlcnJ1cHRpb25zLiBFeGFtcGxlOiBNb2RhbHMuXG4kZWxldmF0aW9uLWxhcmdlOiAwIDVweCAxNXB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wOCksIDAgMTVweCAyN3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNyksIDAgMzBweCAzNnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNCksIDAgNTBweCA0M3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMik7XG5cbi8qKlxuICogRGltZW5zaW9ucy5cbiAqL1xuXG4kaWNvbi1zaXplOiAyNHB4O1xuJGJ1dHRvbi1zaXplOiAzNnB4O1xuJGJ1dHRvbi1zaXplLW5leHQtZGVmYXVsdC00MHB4OiA0MHB4OyAvLyB0cmFuc2l0aW9uYXJ5IHZhcmlhYmxlIGZvciBuZXh0IGRlZmF1bHQgYnV0dG9uIHNpemVcbiRidXR0b24tc2l6ZS1zbWFsbDogMjRweDtcbiRidXR0b24tc2l6ZS1jb21wYWN0OiAzMnB4O1xuJGhlYWRlci1oZWlnaHQ6IDY0cHg7XG4kcGFuZWwtaGVhZGVyLWhlaWdodDogJGdyaWQtdW5pdC02MDtcbiRuYXYtc2lkZWJhci13aWR0aDogMzAwcHg7XG4kYWRtaW4tYmFyLWhlaWdodDogMzJweDtcbiRhZG1pbi1iYXItaGVpZ2h0LWJpZzogNDZweDtcbiRhZG1pbi1zaWRlYmFyLXdpZHRoOiAxNjBweDtcbiRhZG1pbi1zaWRlYmFyLXdpZHRoLWJpZzogMTkwcHg7XG4kYWRtaW4tc2lkZWJhci13aWR0aC1jb2xsYXBzZWQ6IDM2cHg7XG4kbW9kYWwtbWluLXdpZHRoOiAzNTBweDtcbiRtb2RhbC13aWR0aC1zbWFsbDogMzg0cHg7XG4kbW9kYWwtd2lkdGgtbWVkaXVtOiA1MTJweDtcbiRtb2RhbC13aWR0aC1sYXJnZTogODQwcHg7XG4kc3Bpbm5lci1zaXplOiAxNnB4O1xuJGNhbnZhcy1wYWRkaW5nOiAkZ3JpZC11bml0LTIwO1xuJHBhbGV0dGUtbWF4LWhlaWdodDogMzY4cHg7XG5cbi8qKlxuICogTW9iaWxlIHNwZWNpZmljIHN0eWxlc1xuICovXG4kbW9iaWxlLXRleHQtbWluLWZvbnQtc2l6ZTogMTZweDsgLy8gQW55IGZvbnQgc2l6ZSBiZWxvdyAxNnB4IHdpbGwgY2F1c2UgTW9iaWxlIFNhZmFyaSB0byBcInpvb20gaW5cIi5cblxuLyoqXG4gKiBFZGl0b3Igc3R5bGVzLlxuICovXG5cbiRzaWRlYmFyLXdpZHRoOiAyODBweDtcbiRjb250ZW50LXdpZHRoOiA4NDBweDtcbiR3aWRlLWNvbnRlbnQtd2lkdGg6IDExMDBweDtcbiR3aWRnZXQtYXJlYS13aWR0aDogNzAwcHg7XG4kc2Vjb25kYXJ5LXNpZGViYXItd2lkdGg6IDM1MHB4O1xuJGVkaXRvci1mb250LXNpemU6IDE2cHg7XG4kZGVmYXVsdC1ibG9jay1tYXJnaW46IDI4cHg7IC8vIFRoaXMgdmFsdWUgcHJvdmlkZXMgYSBjb25zaXN0ZW50LCBjb250aWd1b3VzIHNwYWNpbmcgYmV0d2VlbiBibG9ja3MuXG4kdGV4dC1lZGl0b3ItZm9udC1zaXplOiAxNXB4O1xuJGVkaXRvci1saW5lLWhlaWdodDogMS44O1xuJGVkaXRvci1odG1sLWZvbnQ6ICRmb250LWZhbWlseS1tb25vO1xuXG4vKipcbiAqIEJsb2NrICYgRWRpdG9yIFVJLlxuICovXG5cbiRibG9jay10b29sYmFyLWhlaWdodDogJGdyaWQtdW5pdC02MDtcbiRib3JkZXItd2lkdGg6IDFweDtcbiRib3JkZXItd2lkdGgtZm9jdXMtZmFsbGJhY2s6IDJweDsgLy8gVGhpcyBleGlzdHMgYXMgYSBmYWxsYmFjaywgYW5kIGlzIGlkZWFsbHkgb3ZlcnJpZGRlbiBieSB2YXIoLS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXMpIHVubGVzcyBpbiBzb21lIFNBU1MgbWF0aCBjYXNlcy5cbiRib3JkZXItd2lkdGgtdGFiOiAxLjVweDtcbiRoZWxwdGV4dC1mb250LXNpemU6IDEycHg7XG4kcmFkaW8taW5wdXQtc2l6ZTogMTZweDtcbiRyYWRpby1pbnB1dC1zaXplLXNtOiAyNHB4OyAvLyBXaWR0aCAmIGhlaWdodCBmb3Igc21hbGwgdmlld3BvcnRzLlxuXG4vLyBEZXByZWNhdGVkLCBwbGVhc2UgYXZvaWQgdXNpbmcgdGhlc2UuXG4kYmxvY2stcGFkZGluZzogMTRweDsgLy8gVXNlZCB0byBkZWZpbmUgc3BhY2UgYmV0d2VlbiBibG9jayBmb290cHJpbnQgYW5kIHN1cnJvdW5kaW5nIGJvcmRlcnMuXG4kcmFkaXVzLWJsb2NrLXVpOiAkcmFkaXVzLXNtYWxsO1xuJHNoYWRvdy1wb3BvdmVyOiAkZWxldmF0aW9uLXgtc21hbGw7XG4kc2hhZG93LW1vZGFsOiAkZWxldmF0aW9uLWxhcmdlO1xuJGRlZmF1bHQtZm9udC1zaXplOiAkZm9udC1zaXplLW1lZGl1bTtcblxuLyoqXG4gKiBCbG9jayBwYWRkaW5ncy5cbiAqL1xuXG4vLyBQYWRkaW5nIGZvciBibG9ja3Mgd2l0aCBhIGJhY2tncm91bmQgY29sb3IgKGUuZy4gcGFyYWdyYXBoIG9yIGdyb3VwKS5cbiRibG9jay1iZy1wYWRkaW5nLS12OiAxLjI1ZW07XG4kYmxvY2stYmctcGFkZGluZy0taDogMi4zNzVlbTtcblxuXG4vKipcbiAqIFJlYWN0IE5hdGl2ZSBzcGVjaWZpYy5cbiAqIFRoZXNlIHZhcmlhYmxlcyBkbyBub3QgYXBwZWFyIHRvIGJlIHVzZWQgYW55d2hlcmUgZWxzZS5cbiAqL1xuXG4vLyBEaW1lbnNpb25zLlxuJG1vYmlsZS1oZWFkZXItdG9vbGJhci1oZWlnaHQ6IDQ0cHg7XG4kbW9iaWxlLWhlYWRlci10b29sYmFyLWV4cGFuZGVkLWhlaWdodDogNTJweDtcbiRtb2JpbGUtZmxvYXRpbmctdG9vbGJhci1oZWlnaHQ6IDQ0cHg7XG4kbW9iaWxlLWZsb2F0aW5nLXRvb2xiYXItbWFyZ2luOiA4cHg7XG4kbW9iaWxlLWNvbG9yLXN3YXRjaDogNDhweDtcblxuLy8gQmxvY2sgVUkuXG4kbW9iaWxlLWJsb2NrLXRvb2xiYXItaGVpZ2h0OiA0NHB4O1xuJGRpbW1lZC1vcGFjaXR5OiAxO1xuJGJsb2NrLWVkZ2UtdG8tY29udGVudDogMTZweDtcbiRzb2xpZC1ib3JkZXItc3BhY2U6IDEycHg7XG4kZGFzaGVkLWJvcmRlci1zcGFjZTogNnB4O1xuJGJsb2NrLXNlbGVjdGVkLW1hcmdpbjogM3B4O1xuJGJsb2NrLXNlbGVjdGVkLWJvcmRlci13aWR0aDogMXB4O1xuJGJsb2NrLXNlbGVjdGVkLXBhZGRpbmc6IDA7XG4kYmxvY2stc2VsZWN0ZWQtY2hpbGQtbWFyZ2luOiA1cHg7XG4kYmxvY2stc2VsZWN0ZWQtdG8tY29udGVudDogJGJsb2NrLWVkZ2UtdG8tY29udGVudCAtICRibG9jay1zZWxlY3RlZC1tYXJnaW4gLSAkYmxvY2stc2VsZWN0ZWQtYm9yZGVyLXdpZHRoO1xuIiwiLyoqXG4gKiBDb2xvcnNcbiAqL1xuXG4vLyBXb3JkUHJlc3MgZ3JheXMuXG4kYmxhY2s6ICMwMDA7XHRcdFx0Ly8gVXNlIG9ubHkgd2hlbiB5b3UgdHJ1bHkgbmVlZCBwdXJlIGJsYWNrLiBGb3IgVUksIHVzZSAkZ3JheS05MDAuXG4kZ3JheS05MDA6ICMxZTFlMWU7XG4kZ3JheS04MDA6ICMyZjJmMmY7XG4kZ3JheS03MDA6ICM3NTc1NzU7XHRcdC8vIE1lZXRzIDQuNjoxICg0LjU6MSBpcyBtaW5pbXVtKSB0ZXh0IGNvbnRyYXN0IGFnYWluc3Qgd2hpdGUuXG4kZ3JheS02MDA6ICM5NDk0OTQ7XHRcdC8vIE1lZXRzIDM6MSBVSSBvciBsYXJnZSB0ZXh0IGNvbnRyYXN0IGFnYWluc3Qgd2hpdGUuXG4kZ3JheS00MDA6ICNjY2M7XG4kZ3JheS0zMDA6ICNkZGQ7XHRcdC8vIFVzZWQgZm9yIG1vc3QgYm9yZGVycy5cbiRncmF5LTIwMDogI2UwZTBlMDtcdFx0Ly8gVXNlZCBzcGFyaW5nbHkgZm9yIGxpZ2h0IGJvcmRlcnMuXG4kZ3JheS0xMDA6ICNmMGYwZjA7XHRcdC8vIFVzZWQgZm9yIGxpZ2h0IGdyYXkgYmFja2dyb3VuZHMuXG4kd2hpdGU6ICNmZmY7XG5cbi8vIE9wYWNpdGllcyAmIGFkZGl0aW9uYWwgY29sb3JzLlxuJGRhcmstZ3JheS1wbGFjZWhvbGRlcjogcmdiYSgkZ3JheS05MDAsIDAuNjIpO1xuJG1lZGl1bS1ncmF5LXBsYWNlaG9sZGVyOiByZ2JhKCRncmF5LTkwMCwgMC41NSk7XG4kbGlnaHQtZ3JheS1wbGFjZWhvbGRlcjogcmdiYSgkd2hpdGUsIDAuNjUpO1xuXG4vLyBBbGVydCBjb2xvcnMuXG4kYWxlcnQteWVsbG93OiAjZjBiODQ5O1xuJGFsZXJ0LXJlZDogI2NjMTgxODtcbiRhbGVydC1ncmVlbjogIzRhYjg2NjtcblxuLy8gRGVwcmVjYXRlZCwgcGxlYXNlIGF2b2lkIHVzaW5nIHRoZXNlLlxuJGRhcmstdGhlbWUtZm9jdXM6ICR3aGl0ZTtcdC8vIEZvY3VzIGNvbG9yIHdoZW4gdGhlIHRoZW1lIGlzIGRhcmsuXG4iLCIvKipcbiAqIFR5cG9ncmFwaHlcbiAqL1xuXG5AdXNlIFwic2Fzczpjb2xvclwiO1xuQHVzZSBcInNhc3M6bWF0aFwiO1xuQHVzZSBcIi4vdmFyaWFibGVzXCI7XG5AdXNlIFwiLi9jb2xvcnNcIjtcbkB1c2UgXCIuL2JyZWFrcG9pbnRzXCI7XG5AdXNlIFwiLi9mdW5jdGlvbnNcIjtcbkB1c2UgXCIuL2xvbmctY29udGVudC1mYWRlXCI7XG5cbkBtaXhpbiBfdGV4dC1oZWFkaW5nKCkge1xuXHRmb250LWZhbWlseTogdmFyaWFibGVzLiRmb250LWZhbWlseS1oZWFkaW5ncztcblx0Zm9udC13ZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC13ZWlnaHQtbWVkaXVtO1xufVxuXG5AbWl4aW4gX3RleHQtYm9keSgpIHtcblx0Zm9udC1mYW1pbHk6IHZhcmlhYmxlcy4kZm9udC1mYW1pbHktYm9keTtcblx0Zm9udC13ZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC13ZWlnaHQtcmVndWxhcjtcbn1cblxuQG1peGluIGhlYWRpbmctc21hbGwoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWhlYWRpbmcoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS14LXNtYWxsO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LXgtc21hbGw7XG59XG5cbkBtaXhpbiBoZWFkaW5nLW1lZGl1bSgpIHtcblx0QGluY2x1ZGUgX3RleHQtaGVhZGluZygpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLW1lZGl1bTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC1zbWFsbDtcbn1cblxuQG1peGluIGhlYWRpbmctbGFyZ2UoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWhlYWRpbmcoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS1sYXJnZTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC1zbWFsbDtcbn1cblxuQG1peGluIGhlYWRpbmcteC1sYXJnZSgpIHtcblx0QGluY2x1ZGUgX3RleHQtaGVhZGluZygpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLXgtbGFyZ2U7XG5cdGxpbmUtaGVpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtbGluZS1oZWlnaHQtbWVkaXVtO1xufVxuXG5AbWl4aW4gaGVhZGluZy0yeC1sYXJnZSgpIHtcblx0QGluY2x1ZGUgX3RleHQtaGVhZGluZygpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLTJ4LWxhcmdlO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LTJ4LWxhcmdlO1xufVxuXG5AbWl4aW4gYm9keS1zbWFsbCgpIHtcblx0QGluY2x1ZGUgX3RleHQtYm9keSgpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLXNtYWxsO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LXgtc21hbGw7XG59XG5cbkBtaXhpbiBib2R5LW1lZGl1bSgpIHtcblx0QGluY2x1ZGUgX3RleHQtYm9keSgpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLW1lZGl1bTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC1zbWFsbDtcbn1cblxuQG1peGluIGJvZHktbGFyZ2UoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWJvZHkoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS1sYXJnZTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC1tZWRpdW07XG59XG5cbkBtaXhpbiBib2R5LXgtbGFyZ2UoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWJvZHkoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS14LWxhcmdlO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LXgtbGFyZ2U7XG59XG5cbi8qKlxuICogQnJlYWtwb2ludCBtaXhpbnNcbiAqL1xuXG5AbWl4aW4gYnJlYWsteGh1Z2UoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLXhodWdlKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLWh1Z2UoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLWh1Z2UpIH0pIHtcblx0XHRAY29udGVudDtcblx0fVxufVxuXG5AbWl4aW4gYnJlYWstd2lkZSgpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstd2lkZSkgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbkBtaXhpbiBicmVhay14bGFyZ2UoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLXhsYXJnZSkgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbkBtaXhpbiBicmVhay1sYXJnZSgpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbGFyZ2UpIH0pIHtcblx0XHRAY29udGVudDtcblx0fVxufVxuXG5AbWl4aW4gYnJlYWstbWVkaXVtKCkge1xuXHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay1tZWRpdW0pIH0pIHtcblx0XHRAY29udGVudDtcblx0fVxufVxuXG5AbWl4aW4gYnJlYWstc21hbGwoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLXNtYWxsKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLW1vYmlsZSgpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbW9iaWxlKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLXpvb21lZC1pbigpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstem9vbWVkLWluKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuLyoqXG4gKiBGb2N1cyBzdHlsZXMuXG4gKi9cblxuQG1peGluIGJsb2NrLXRvb2xiYXItYnV0dG9uLXN0eWxlX19mb2N1cygpIHtcblx0Ym94LXNoYWRvdzogaW5zZXQgMCAwIDAgdmFyaWFibGVzLiRib3JkZXItd2lkdGggY29sb3JzLiR3aGl0ZSwgMCAwIDAgdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcik7XG5cblx0Ly8gV2luZG93cyBIaWdoIENvbnRyYXN0IG1vZGUgd2lsbCBzaG93IHRoaXMgb3V0bGluZSwgYnV0IG5vdCB0aGUgYm94LXNoYWRvdy5cblx0b3V0bGluZTogMnB4IHNvbGlkIHRyYW5zcGFyZW50O1xufVxuXG4vLyBUYWJzLCBJbnB1dHMsIFNxdWFyZSBidXR0b25zLlxuQG1peGluIGlucHV0LXN0eWxlX19uZXV0cmFsKCkge1xuXHRib3gtc2hhZG93OiAwIDAgMCB0cmFuc3BhcmVudDtcblx0Ym9yZGVyLXJhZGl1czogdmFyaWFibGVzLiRyYWRpdXMtc21hbGw7XG5cdGJvcmRlcjogdmFyaWFibGVzLiRib3JkZXItd2lkdGggc29saWQgY29sb3JzLiRncmF5LTYwMDtcblxuXHRAbWVkaWEgbm90IChwcmVmZXJzLXJlZHVjZWQtbW90aW9uKSB7XG5cdFx0dHJhbnNpdGlvbjogYm94LXNoYWRvdyAwLjFzIGxpbmVhcjtcblx0fVxufVxuXG5cbkBtaXhpbiBpbnB1dC1zdHlsZV9fZm9jdXMoJGFjY2VudC1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpKSB7XG5cdGJvcmRlci1jb2xvcjogJGFjY2VudC1jb2xvcjtcblx0Ly8gRXhwYW5kIHRoZSBkZWZhdWx0IGJvcmRlciBmb2N1cyBzdHlsZSBieSAuNXB4IHRvIGJlIGEgdG90YWwgb2YgMS41cHguXG5cdGJveC1zaGFkb3c6IDAgMCAwIDAuNXB4ICRhY2NlbnQtY29sb3I7XG5cdC8vIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlIHdpbGwgc2hvdyB0aGlzIG91dGxpbmUsIGJ1dCBub3QgdGhlIGJveC1zaGFkb3cuXG5cdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcbn1cblxuQG1peGluIGJ1dHRvbi1zdHlsZV9fZm9jdXMoKSB7XG5cdGJveC1zaGFkb3c6IDAgMCAwIHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXG5cdC8vIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlIHdpbGwgc2hvdyB0aGlzIG91dGxpbmUsIGJ1dCBub3QgdGhlIGJveC1zaGFkb3cuXG5cdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcbn1cblxuXG5AbWl4aW4gYnV0dG9uLXN0eWxlLW91dHNldF9fZm9jdXMoJGZvY3VzLWNvbG9yKSB7XG5cdGJveC1zaGFkb3c6IDAgMCAwIHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgY29sb3JzLiR3aGl0ZSwgMCAwIDAgY2FsYygyICogdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSkgJGZvY3VzLWNvbG9yO1xuXG5cdC8vIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlIHdpbGwgc2hvdyB0aGlzIG91dGxpbmUsIGJ1dCBub3QgdGhlIGJveC1zaGFkb3cuXG5cdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcblx0b3V0bGluZS1vZmZzZXQ6IDJweDtcbn1cblxuXG4vKipcbiAqIEFwcGxpZXMgZWRpdG9yIGxlZnQgcG9zaXRpb24gdG8gdGhlIHNlbGVjdG9yIHBhc3NlZCBhcyBhcmd1bWVudFxuICovXG5cbkBtaXhpbiBlZGl0b3ItbGVmdCgkc2VsZWN0b3IpIHtcblx0I3skc2VsZWN0b3J9IHsgLyogU2V0IGxlZnQgcG9zaXRpb24gd2hlbiBhdXRvLWZvbGQgaXMgbm90IG9uIHRoZSBib2R5IGVsZW1lbnQuICovXG5cdFx0bGVmdDogMDtcblxuXHRcdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLW1lZGl1bSArIDEpIH0pIHtcblx0XHRcdGxlZnQ6IHZhcmlhYmxlcy4kYWRtaW4tc2lkZWJhci13aWR0aDtcblx0XHR9XG5cdH1cblxuXHQuYXV0by1mb2xkICN7JHNlbGVjdG9yfSB7IC8qIEF1dG8gZm9sZCBpcyB3aGVuIG9uIHNtYWxsZXIgYnJlYWtwb2ludHMsIG5hdiBtZW51IGF1dG8gY29sbGFwc2VzLiAqL1xuXHRcdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLW1lZGl1bSArIDEpIH0pIHtcblx0XHRcdGxlZnQ6IHZhcmlhYmxlcy4kYWRtaW4tc2lkZWJhci13aWR0aC1jb2xsYXBzZWQ7XG5cdFx0fVxuXG5cdFx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbGFyZ2UgKyAxKSB9KSB7XG5cdFx0XHRsZWZ0OiB2YXJpYWJsZXMuJGFkbWluLXNpZGViYXItd2lkdGg7XG5cdFx0fVxuXHR9XG5cblx0LyogU2lkZWJhciBtYW51YWxseSBjb2xsYXBzZWQuICovXG5cdC5mb2xkZWQgI3skc2VsZWN0b3J9IHtcblx0XHRsZWZ0OiAwO1xuXG5cdFx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbWVkaXVtICsgMSkgfSkge1xuXHRcdFx0bGVmdDogdmFyaWFibGVzLiRhZG1pbi1zaWRlYmFyLXdpZHRoLWNvbGxhcHNlZDtcblx0XHR9XG5cdH1cblxuXHRib2R5LmlzLWZ1bGxzY3JlZW4tbW9kZSAjeyRzZWxlY3Rvcn0ge1xuXHRcdGxlZnQ6IDAgIWltcG9ydGFudDtcblx0fVxufVxuXG4vKipcbiAqIFN0eWxlcyB0aGF0IGFyZSByZXVzZWQgdmVyYmF0aW0gaW4gYSBmZXcgcGxhY2VzXG4gKi9cblxuLy8gVGhlc2UgYXJlIGFkZGl0aW9uYWwgc3R5bGVzIGZvciBhbGwgY2FwdGlvbnMsIHdoZW4gdGhlIHRoZW1lIG9wdHMgaW4gdG8gYmxvY2sgc3R5bGVzLlxuQG1peGluIGNhcHRpb24tc3R5bGUoKSB7XG5cdG1hcmdpbi10b3A6IDAuNWVtO1xuXHRtYXJnaW4tYm90dG9tOiAxZW07XG59XG5cbkBtaXhpbiBjYXB0aW9uLXN0eWxlLXRoZW1lKCkge1xuXHRjb2xvcjogIzU1NTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGRlZmF1bHQtZm9udC1zaXplO1xuXHR0ZXh0LWFsaWduOiBjZW50ZXI7XG5cblx0LmlzLWRhcmstdGhlbWUgJiB7XG5cdFx0Y29sb3I6IGNvbG9ycy4kbGlnaHQtZ3JheS1wbGFjZWhvbGRlcjtcblx0fVxufVxuXG4vKipcbiAqIEFsbG93cyB1c2VycyB0byBvcHQtb3V0IG9mIGFuaW1hdGlvbnMgdmlhIE9TLWxldmVsIHByZWZlcmVuY2VzLlxuICovXG5cbkBtaXhpbiByZWR1Y2UtbW90aW9uKCRwcm9wZXJ0eTogXCJcIikge1xuXG5cdEBpZiAkcHJvcGVydHkgPT0gXCJ0cmFuc2l0aW9uXCIge1xuXHRcdEBtZWRpYSAocHJlZmVycy1yZWR1Y2VkLW1vdGlvbjogcmVkdWNlKSB7XG5cdFx0XHR0cmFuc2l0aW9uLWR1cmF0aW9uOiAwcztcblx0XHRcdHRyYW5zaXRpb24tZGVsYXk6IDBzO1xuXHRcdH1cblx0fSBAZWxzZSBpZiAkcHJvcGVydHkgPT0gXCJhbmltYXRpb25cIiB7XG5cdFx0QG1lZGlhIChwcmVmZXJzLXJlZHVjZWQtbW90aW9uOiByZWR1Y2UpIHtcblx0XHRcdGFuaW1hdGlvbi1kdXJhdGlvbjogMW1zO1xuXHRcdFx0YW5pbWF0aW9uLWRlbGF5OiAwcztcblx0XHR9XG5cdH0gQGVsc2Uge1xuXHRcdEBtZWRpYSAocHJlZmVycy1yZWR1Y2VkLW1vdGlvbjogcmVkdWNlKSB7XG5cdFx0XHR0cmFuc2l0aW9uLWR1cmF0aW9uOiAwcztcblx0XHRcdHRyYW5zaXRpb24tZGVsYXk6IDBzO1xuXHRcdFx0YW5pbWF0aW9uLWR1cmF0aW9uOiAxbXM7XG5cdFx0XHRhbmltYXRpb24tZGVsYXk6IDBzO1xuXHRcdH1cblx0fVxufVxuXG5AbWl4aW4gaW5wdXQtY29udHJvbCgkYWNjZW50LWNvbG9yOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcikpIHtcblx0Zm9udC1mYW1pbHk6IHZhcmlhYmxlcy4kZGVmYXVsdC1mb250O1xuXHRwYWRkaW5nOiA2cHggOHB4O1xuXHQvKiBGb250cyBzbWFsbGVyIHRoYW4gMTZweCBjYXVzZXMgbW9iaWxlIHNhZmFyaSB0byB6b29tLiAqL1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kbW9iaWxlLXRleHQtbWluLWZvbnQtc2l6ZTtcblx0LyogT3ZlcnJpZGUgY29yZSBsaW5lLWhlaWdodC4gVG8gYmUgcmV2aWV3ZWQuICovXG5cdGxpbmUtaGVpZ2h0OiBub3JtYWw7XG5cdEBpbmNsdWRlIGlucHV0LXN0eWxlX19uZXV0cmFsKCk7XG5cblx0QGluY2x1ZGUgYnJlYWstc21hbGwge1xuXHRcdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRkZWZhdWx0LWZvbnQtc2l6ZTtcblx0XHQvKiBPdmVycmlkZSBjb3JlIGxpbmUtaGVpZ2h0LiBUbyBiZSByZXZpZXdlZC4gKi9cblx0XHRsaW5lLWhlaWdodDogbm9ybWFsO1xuXHR9XG5cblx0Jjpmb2N1cyB7XG5cdFx0QGluY2x1ZGUgaW5wdXQtc3R5bGVfX2ZvY3VzKCRhY2NlbnQtY29sb3IpO1xuXHR9XG5cblx0Ly8gVXNlIG9wYWNpdHkgdG8gd29yayBpbiB2YXJpb3VzIGVkaXRvciBzdHlsZXMuXG5cdCY6Oi13ZWJraXQtaW5wdXQtcGxhY2Vob2xkZXIge1xuXHRcdGNvbG9yOiBjb2xvcnMuJGRhcmstZ3JheS1wbGFjZWhvbGRlcjtcblx0fVxuXG5cdCY6Oi1tb3otcGxhY2Vob2xkZXIge1xuXHRcdGNvbG9yOiBjb2xvcnMuJGRhcmstZ3JheS1wbGFjZWhvbGRlcjtcblx0fVxuXG5cdCY6LW1zLWlucHV0LXBsYWNlaG9sZGVyIHtcblx0XHRjb2xvcjogY29sb3JzLiRkYXJrLWdyYXktcGxhY2Vob2xkZXI7XG5cdH1cbn1cblxuQG1peGluIGNoZWNrYm94LWNvbnRyb2wge1xuXHRib3JkZXI6IHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoIHNvbGlkIGNvbG9ycy4kZ3JheS05MDA7XG5cdG1hcmdpbi1yaWdodDogdmFyaWFibGVzLiRncmlkLXVuaXQtMTU7XG5cdHRyYW5zaXRpb246IG5vbmU7XG5cdGJvcmRlci1yYWRpdXM6IHZhcmlhYmxlcy4kcmFkaXVzLXNtYWxsO1xuXHRAaW5jbHVkZSBpbnB1dC1jb250cm9sO1xuXG5cdCY6Zm9jdXMge1xuXHRcdGJveC1zaGFkb3c6IDAgMCAwICh2YXJpYWJsZXMuJGJvcmRlci13aWR0aCAqIDIpIGNvbG9ycy4kd2hpdGUsIDAgMCAwICh2YXJpYWJsZXMuJGJvcmRlci13aWR0aCAqIDIgKyB2YXJpYWJsZXMuJGJvcmRlci13aWR0aC1mb2N1cy1mYWxsYmFjaykgdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXG5cdFx0Ly8gT25seSB2aXNpYmxlIGluIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlLlxuXHRcdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcblx0fVxuXG5cdCY6Y2hlY2tlZCB7XG5cdFx0YmFja2dyb3VuZDogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXHRcdGJvcmRlci1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXG5cdFx0Ly8gSGlkZSBkZWZhdWx0IGNoZWNrYm94IHN0eWxlcyBpbiBJRS5cblx0XHQmOjotbXMtY2hlY2sge1xuXHRcdFx0b3BhY2l0eTogMDtcblx0XHR9XG5cdH1cblxuXHQmOmNoZWNrZWQ6OmJlZm9yZSxcblx0JlthcmlhLWNoZWNrZWQ9XCJtaXhlZFwiXTo6YmVmb3JlIHtcblx0XHRtYXJnaW46IC0zcHggLTVweDtcblx0XHRjb2xvcjogY29sb3JzLiR3aGl0ZTtcblxuXHRcdEBpbmNsdWRlIGJyZWFrLW1lZGl1bSgpIHtcblx0XHRcdG1hcmdpbjogLTRweCAwIDAgLTVweDtcblx0XHR9XG5cdH1cblxuXHQmW2FyaWEtY2hlY2tlZD1cIm1peGVkXCJdIHtcblx0XHRiYWNrZ3JvdW5kOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcik7XG5cdFx0Ym9yZGVyLWNvbG9yOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcik7XG5cblx0XHQmOjpiZWZvcmUge1xuXHRcdFx0Ly8gSW5oZXJpdGVkIGZyb20gYGZvcm1zLmNzc2AuXG5cdFx0XHQvLyBTZWU6IGh0dHBzOi8vZ2l0aHViLmNvbS9Xb3JkUHJlc3Mvd29yZHByZXNzLWRldmVsb3AvdHJlZS81LjEuMS9zcmMvd3AtYWRtaW4vY3NzL2Zvcm1zLmNzcyNMMTIyLUwxMzJcblx0XHRcdGNvbnRlbnQ6IFwiXFxmNDYwXCI7XG5cdFx0XHRmbG9hdDogbGVmdDtcblx0XHRcdGRpc3BsYXk6IGlubGluZS1ibG9jaztcblx0XHRcdHZlcnRpY2FsLWFsaWduOiBtaWRkbGU7XG5cdFx0XHR3aWR0aDogMTZweDtcblx0XHRcdC8qIHN0eWxlbGludC1kaXNhYmxlLW5leHQtbGluZSBmb250LWZhbWlseS1uby1taXNzaW5nLWdlbmVyaWMtZmFtaWx5LWtleXdvcmQgLS0gZGFzaGljb25zIGRvbid0IG5lZWQgYSBnZW5lcmljIGZhbWlseSBrZXl3b3JkLiAqL1xuXHRcdFx0Zm9udDogbm9ybWFsIDMwcHgvMSBkYXNoaWNvbnM7XG5cdFx0XHRzcGVhazogbm9uZTtcblx0XHRcdC13ZWJraXQtZm9udC1zbW9vdGhpbmc6IGFudGlhbGlhc2VkO1xuXHRcdFx0LW1vei1vc3gtZm9udC1zbW9vdGhpbmc6IGdyYXlzY2FsZTtcblxuXHRcdFx0QGluY2x1ZGUgYnJlYWstbWVkaXVtKCkge1xuXHRcdFx0XHRmbG9hdDogbm9uZTtcblx0XHRcdFx0Zm9udC1zaXplOiAyMXB4O1xuXHRcdFx0fVxuXHRcdH1cblx0fVxuXG5cdCZbYXJpYS1kaXNhYmxlZD1cInRydWVcIl0sXG5cdCY6ZGlzYWJsZWQge1xuXHRcdGJhY2tncm91bmQ6IGNvbG9ycy4kZ3JheS0xMDA7XG5cdFx0Ym9yZGVyLWNvbG9yOiBjb2xvcnMuJGdyYXktMzAwO1xuXHRcdGN1cnNvcjogZGVmYXVsdDtcblxuXHRcdC8vIE92ZXJyaWRlIHN0eWxlIGluaGVyaXRlZCBmcm9tIHdwLWFkbWluLiBSZXF1aXJlZCB0byBhdm9pZCBkZWdyYWRlZCBhcHBlYXJhbmNlIG9uIGRpZmZlcmVudCBiYWNrZ3JvdW5kcy5cblx0XHRvcGFjaXR5OiAxO1xuXHR9XG59XG5cbkBtaXhpbiByYWRpby1jb250cm9sIHtcblx0Ym9yZGVyOiB2YXJpYWJsZXMuJGJvcmRlci13aWR0aCBzb2xpZCBjb2xvcnMuJGdyYXktOTAwO1xuXHRtYXJnaW4tcmlnaHQ6IHZhcmlhYmxlcy4kZ3JpZC11bml0LTE1O1xuXHR0cmFuc2l0aW9uOiBub25lO1xuXHRib3JkZXItcmFkaXVzOiB2YXJpYWJsZXMuJHJhZGl1cy1yb3VuZDtcblx0d2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZS1zbTtcblx0aGVpZ2h0OiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemUtc207XG5cdG1pbi13aWR0aDogdmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLXNtO1xuXHRtYXgtd2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZS1zbTtcblx0cG9zaXRpb246IHJlbGF0aXZlO1xuXG5cdEBtZWRpYSBub3QgKHByZWZlcnMtcmVkdWNlZC1tb3Rpb24pIHtcblx0XHR0cmFuc2l0aW9uOiBib3gtc2hhZG93IDAuMXMgbGluZWFyO1xuXHR9XG5cblx0QGluY2x1ZGUgYnJlYWstc21hbGwoKSB7XG5cdFx0aGVpZ2h0OiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemU7XG5cdFx0d2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZTtcblx0XHRtaW4td2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZTtcblx0XHRtYXgtd2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZTtcblx0fVxuXG5cdCY6Y2hlY2tlZDo6YmVmb3JlIHtcblx0XHRib3gtc2l6aW5nOiBpbmhlcml0O1xuXHRcdHdpZHRoOiBtYXRoLmRpdih2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemUtc20sIDIpO1xuXHRcdGhlaWdodDogbWF0aC5kaXYodmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLXNtLCAyKTtcblx0XHRwb3NpdGlvbjogYWJzb2x1dGU7XG5cdFx0dG9wOiA1MCU7XG5cdFx0bGVmdDogNTAlO1xuXHRcdHRyYW5zZm9ybTogdHJhbnNsYXRlKC01MCUsIC01MCUpO1xuXHRcdG1hcmdpbjogMDtcblx0XHRiYWNrZ3JvdW5kLWNvbG9yOiBjb2xvcnMuJHdoaXRlO1xuXG5cdFx0Ly8gVGhpcyBib3JkZXIgc2VydmVzIGFzIGEgYmFja2dyb3VuZCBjb2xvciBpbiBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZS5cblx0XHRib3JkZXI6IDRweCBzb2xpZCBjb2xvcnMuJHdoaXRlO1xuXG5cdFx0QGluY2x1ZGUgYnJlYWstc21hbGwoKSB7XG5cdFx0XHR3aWR0aDogbWF0aC5kaXYodmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLCAyKTtcblx0XHRcdGhlaWdodDogbWF0aC5kaXYodmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLCAyKTtcblx0XHR9XG5cdH1cblxuXHQmOmZvY3VzIHtcblx0XHRib3gtc2hhZG93OiAwIDAgMCAodmFyaWFibGVzLiRib3JkZXItd2lkdGggKiAyKSBjb2xvcnMuJHdoaXRlLCAwIDAgMCAodmFyaWFibGVzLiRib3JkZXItd2lkdGggKiAyICsgdmFyaWFibGVzLiRib3JkZXItd2lkdGgtZm9jdXMtZmFsbGJhY2spIHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblxuXHRcdC8vIE9ubHkgdmlzaWJsZSBpbiBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZS5cblx0XHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQ7XG5cdH1cblxuXHQmOmNoZWNrZWQge1xuXHRcdGJhY2tncm91bmQ6IHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblx0XHRib3JkZXI6IG5vbmU7XG5cdH1cbn1cblxuLyoqXG4gKiBSZXNldCBkZWZhdWx0IHN0eWxlcyBmb3IgSmF2YVNjcmlwdCBVSSBiYXNlZCBwYWdlcy5cbiAqIFRoaXMgaXMgYSBXUC1hZG1pbiBhZ25vc3RpYyByZXNldFxuICovXG5cbkBtaXhpbiByZXNldCB7XG5cdGJveC1zaXppbmc6IGJvcmRlci1ib3g7XG5cblx0Kixcblx0Kjo6YmVmb3JlLFxuXHQqOjphZnRlciB7XG5cdFx0Ym94LXNpemluZzogaW5oZXJpdDtcblx0fVxufVxuXG5AbWl4aW4gbGluay1yZXNldCB7XG5cdCY6Zm9jdXMge1xuXHRcdGNvbG9yOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvci0tcmdiKTtcblx0XHRib3gtc2hhZG93OiAwIDAgMCB2YXIoLS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXMpIHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yLCAjMDA3Y2JhKTtcblx0XHRib3JkZXItcmFkaXVzOiB2YXJpYWJsZXMuJHJhZGl1cy1zbWFsbDtcblx0fVxufVxuXG4vLyBUaGUgZWRpdG9yIGlucHV0IHJlc2V0IHdpdGggaW5jcmVhc2VkIHNwZWNpZmljaXR5IHRvIGF2b2lkIHRoZW1lIHN0eWxlcyBibGVlZGluZyBpbi5cbkBtaXhpbiBlZGl0b3ItaW5wdXQtcmVzZXQoKSB7XG5cdGZvbnQtZmFtaWx5OiB2YXJpYWJsZXMuJGVkaXRvci1odG1sLWZvbnQgIWltcG9ydGFudDtcblx0Y29sb3I6IGNvbG9ycy4kZ3JheS05MDAgIWltcG9ydGFudDtcblx0YmFja2dyb3VuZDogY29sb3JzLiR3aGl0ZSAhaW1wb3J0YW50O1xuXHRwYWRkaW5nOiB2YXJpYWJsZXMuJGdyaWQtdW5pdC0xNSAhaW1wb3J0YW50O1xuXHRib3JkZXI6IHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoIHNvbGlkIGNvbG9ycy4kZ3JheS05MDAgIWltcG9ydGFudDtcblx0Ym94LXNoYWRvdzogbm9uZSAhaW1wb3J0YW50O1xuXHRib3JkZXItcmFkaXVzOiB2YXJpYWJsZXMuJHJhZGl1cy1zbWFsbCAhaW1wb3J0YW50O1xuXG5cdC8vIEZvbnRzIHNtYWxsZXIgdGhhbiAxNnB4IGNhdXNlcyBtb2JpbGUgc2FmYXJpIHRvIHpvb20uXG5cdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRtb2JpbGUtdGV4dC1taW4tZm9udC1zaXplICFpbXBvcnRhbnQ7XG5cdEBpbmNsdWRlIGJyZWFrLXNtYWxsIHtcblx0XHRmb250LXNpemU6IHZhcmlhYmxlcy4kZGVmYXVsdC1mb250LXNpemUgIWltcG9ydGFudDtcblx0fVxuXG5cdCY6Zm9jdXMge1xuXHRcdGJvcmRlci1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpICFpbXBvcnRhbnQ7XG5cdFx0Ym94LXNoYWRvdzogMCAwIDAgKHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoLWZvY3VzLWZhbGxiYWNrIC0gdmFyaWFibGVzLiRib3JkZXItd2lkdGgpIHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKSAhaW1wb3J0YW50O1xuXG5cdFx0Ly8gV2luZG93cyBIaWdoIENvbnRyYXN0IG1vZGUgd2lsbCBzaG93IHRoaXMgb3V0bGluZSwgYnV0IG5vdCB0aGUgYm94LXNoYWRvdy5cblx0XHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQgIWltcG9ydGFudDtcblx0fVxufVxuXG4vKipcbiAqIFJlc2V0IHRoZSBXUCBBZG1pbiBwYWdlIHN0eWxlcyBmb3IgR3V0ZW5iZXJnLWxpa2UgcGFnZXMuXG4gKi9cblxuQG1peGluIHdwLWFkbWluLXJlc2V0KCAkY29udGVudC1jb250YWluZXIgKSB7XG5cdGJhY2tncm91bmQ6IGNvbG9ycy4kd2hpdGU7XG5cblx0I3dwY29udGVudCB7XG5cdFx0cGFkZGluZy1sZWZ0OiAwO1xuXHR9XG5cblx0I3dwYm9keS1jb250ZW50IHtcblx0XHRwYWRkaW5nLWJvdHRvbTogMDtcblx0fVxuXG5cdC8qIFdlIGhpZGUgbGVnYWN5IG5vdGljZXMgaW4gR3V0ZW5iZXJnIEJhc2VkIFBhZ2VzLCBiZWNhdXNlIHRoZXkgd2VyZSBub3QgZGVzaWduZWQgaW4gYSB3YXkgdGhhdCBzY2FsZWQgd2VsbC5cblx0ICAgUGx1Z2lucyBjYW4gdXNlIEd1dGVuYmVyZyBub3RpY2VzIGlmIHRoZXkgbmVlZCB0byBwYXNzIG9uIGluZm9ybWF0aW9uIHRvIHRoZSB1c2VyIHdoZW4gdGhleSBhcmUgZWRpdGluZy4gKi9cblx0I3dwYm9keS1jb250ZW50ID4gZGl2Om5vdCgjeyAkY29udGVudC1jb250YWluZXIgfSk6bm90KCNzY3JlZW4tbWV0YSkge1xuXHRcdGRpc3BsYXk6IG5vbmU7XG5cdH1cblxuXHQjd3Bmb290ZXIge1xuXHRcdGRpc3BsYXk6IG5vbmU7XG5cdH1cblxuXHQuYTExeS1zcGVhay1yZWdpb24ge1xuXHRcdGxlZnQ6IC0xcHg7XG5cdFx0dG9wOiAtMXB4O1xuXHR9XG5cblx0dWwjYWRtaW5tZW51IGEud3AtaGFzLWN1cnJlbnQtc3VibWVudTo6YWZ0ZXIsXG5cdHVsI2FkbWlubWVudSA+IGxpLmN1cnJlbnQgPiBhLmN1cnJlbnQ6OmFmdGVyIHtcblx0XHRib3JkZXItcmlnaHQtY29sb3I6IGNvbG9ycy4kd2hpdGU7XG5cdH1cblxuXHQubWVkaWEtZnJhbWUgc2VsZWN0LmF0dGFjaG1lbnQtZmlsdGVyczpsYXN0LW9mLXR5cGUge1xuXHRcdHdpZHRoOiBhdXRvO1xuXHRcdG1heC13aWR0aDogMTAwJTtcblx0fVxufVxuXG5AbWl4aW4gYWRtaW4tc2NoZW1lKCRjb2xvci1wcmltYXJ5KSB7XG5cdC8vIERlZmluZSBSR0IgZXF1aXZhbGVudHMgZm9yIHVzZSBpbiByZ2JhIGZ1bmN0aW9uLlxuXHQvLyBIZXhhZGVjaW1hbCBjc3MgdmFycyBkbyBub3Qgd29yayBpbiB0aGUgcmdiYSBmdW5jdGlvbi5cblx0LS13cC1hZG1pbi10aGVtZS1jb2xvcjogI3skY29sb3ItcHJpbWFyeX07XG5cdC0td3AtYWRtaW4tdGhlbWUtY29sb3ItLXJnYjogI3tmdW5jdGlvbnMuaGV4LXRvLXJnYigkY29sb3ItcHJpbWFyeSl9O1xuXHQvLyBEYXJrZXIgc2hhZGVzLlxuXHQtLXdwLWFkbWluLXRoZW1lLWNvbG9yLWRhcmtlci0xMDogI3tjb2xvci5hZGp1c3QoJGNvbG9yLXByaW1hcnksICRsaWdodG5lc3M6IC01JSl9O1xuXHQtLXdwLWFkbWluLXRoZW1lLWNvbG9yLWRhcmtlci0xMC0tcmdiOiAje2Z1bmN0aW9ucy5oZXgtdG8tcmdiKGNvbG9yLmFkanVzdCgkY29sb3ItcHJpbWFyeSwgJGxpZ2h0bmVzczogLTUlKSl9O1xuXHQtLXdwLWFkbWluLXRoZW1lLWNvbG9yLWRhcmtlci0yMDogI3tjb2xvci5hZGp1c3QoJGNvbG9yLXByaW1hcnksICRsaWdodG5lc3M6IC0xMCUpfTtcblx0LS13cC1hZG1pbi10aGVtZS1jb2xvci1kYXJrZXItMjAtLXJnYjogI3tmdW5jdGlvbnMuaGV4LXRvLXJnYihjb2xvci5hZGp1c3QoJGNvbG9yLXByaW1hcnksICRsaWdodG5lc3M6IC0xMCUpKX07XG5cblx0Ly8gRm9jdXMgc3R5bGUgd2lkdGguXG5cdC8vIEF2b2lkIHJvdW5kaW5nIGlzc3VlcyBieSBzaG93aW5nIGEgd2hvbGUgMnB4IGZvciAxeCBzY3JlZW5zLCBhbmQgMS41cHggb24gaGlnaCByZXNvbHV0aW9uIHNjcmVlbnMuXG5cdC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzOiAycHg7XG5cdEBtZWRpYSAoIC13ZWJraXQtbWluLWRldmljZS1waXhlbC1yYXRpbzogMiksIChtaW4tcmVzb2x1dGlvbjogMTkyZHBpKSB7XG5cdFx0LS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXM6IDEuNXB4O1xuXHR9XG59XG5cbkBtaXhpbiB3b3JkcHJlc3MtYWRtaW4tc2NoZW1lcygpIHtcblx0Ym9keS5hZG1pbi1jb2xvci1saWdodCB7XG5cdFx0QGluY2x1ZGUgYWRtaW4tc2NoZW1lKCMwMDg1YmEpO1xuXHR9XG5cblx0Ym9keS5hZG1pbi1jb2xvci1tb2Rlcm4ge1xuXHRcdEBpbmNsdWRlIGFkbWluLXNjaGVtZSgjMzg1OGU5KTtcblx0fVxuXG5cdGJvZHkuYWRtaW4tY29sb3ItYmx1ZSB7XG5cdFx0QGluY2x1ZGUgYWRtaW4tc2NoZW1lKCMwOTY0ODQpO1xuXHR9XG5cblx0Ym9keS5hZG1pbi1jb2xvci1jb2ZmZWUge1xuXHRcdEBpbmNsdWRlIGFkbWluLXNjaGVtZSgjNDY0MDNjKTtcblx0fVxuXG5cdGJvZHkuYWRtaW4tY29sb3ItZWN0b3BsYXNtIHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoIzUyM2Y2ZCk7XG5cdH1cblxuXHRib2R5LmFkbWluLWNvbG9yLW1pZG5pZ2h0IHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoI2UxNGQ0Myk7XG5cdH1cblxuXHRib2R5LmFkbWluLWNvbG9yLW9jZWFuIHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoIzYyN2M4Myk7XG5cdH1cblxuXHRib2R5LmFkbWluLWNvbG9yLXN1bnJpc2Uge1xuXHRcdEBpbmNsdWRlIGFkbWluLXNjaGVtZSgjZGQ4MjNiKTtcblx0fVxufVxuXG4vLyBEZXByZWNhdGVkIGZyb20gVUksIGtlcHQgZm9yIGJhY2stY29tcGF0LlxuQG1peGluIGJhY2tncm91bmQtY29sb3JzLWRlcHJlY2F0ZWQoKSB7XG5cdC5oYXMtdmVyeS1saWdodC1ncmF5LWJhY2tncm91bmQtY29sb3Ige1xuXHRcdGJhY2tncm91bmQtY29sb3I6ICNlZWU7XG5cdH1cblxuXHQuaGFzLXZlcnktZGFyay1ncmF5LWJhY2tncm91bmQtY29sb3Ige1xuXHRcdGJhY2tncm91bmQtY29sb3I6ICMzMTMxMzE7XG5cdH1cbn1cblxuLy8gRGVwcmVjYXRlZCBmcm9tIFVJLCBrZXB0IGZvciBiYWNrLWNvbXBhdC5cbkBtaXhpbiBmb3JlZ3JvdW5kLWNvbG9ycy1kZXByZWNhdGVkKCkge1xuXHQuaGFzLXZlcnktbGlnaHQtZ3JheS1jb2xvciB7XG5cdFx0Y29sb3I6ICNlZWU7XG5cdH1cblxuXHQuaGFzLXZlcnktZGFyay1ncmF5LWNvbG9yIHtcblx0XHRjb2xvcjogIzMxMzEzMTtcblx0fVxufVxuXG4vLyBEZXByZWNhdGVkIGZyb20gVUksIGtlcHQgZm9yIGJhY2stY29tcGF0LlxuQG1peGluIGdyYWRpZW50LWNvbG9ycy1kZXByZWNhdGVkKCkge1xuXHQvLyBPdXIgY2xhc3NlcyB1c2VzIHRoZSBzYW1lIHZhbHVlcyB3ZSBzZXQgZm9yIGdyYWRpZW50IHZhbHVlIGF0dHJpYnV0ZXMuXG5cblx0Lyogc3R5bGVsaW50LWRpc2FibGUgQHN0eWxpc3RpYy9mdW5jdGlvbi1jb21tYS1zcGFjZS1hZnRlciAtLSBXZSBjYW4gbm90IHVzZSBzcGFjaW5nIGJlY2F1c2Ugb2YgV1AgbXVsdGkgc2l0ZSBrc2VzIHJ1bGUuICovXG5cdC5oYXMtdml2aWQtZ3JlZW4tY3lhbi10by12aXZpZC1jeWFuLWJsdWUtZ3JhZGllbnQtYmFja2dyb3VuZCB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KDEzNWRlZyxyZ2JhKDAsMjA4LDEzMiwxKSAwJSxyZ2JhKDYsMTQ3LDIyNywxKSAxMDAlKTtcblx0fVxuXG5cdC5oYXMtcHVycGxlLWNydXNoLWdyYWRpZW50LWJhY2tncm91bmQge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCgxMzVkZWcscmdiKDUyLDIyNiwyMjgpIDAlLHJnYig3MSwzMywyNTEpIDUwJSxyZ2IoMTcxLDI5LDI1NCkgMTAwJSk7XG5cdH1cblxuXHQuaGFzLWhhenktZGF3bi1ncmFkaWVudC1iYWNrZ3JvdW5kIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQoMTM1ZGVnLHJnYigyNTAsMTcyLDE2OCkgMCUscmdiKDIxOCwyMDgsMjM2KSAxMDAlKTtcblx0fVxuXG5cdC5oYXMtc3ViZHVlZC1vbGl2ZS1ncmFkaWVudC1iYWNrZ3JvdW5kIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQoMTM1ZGVnLHJnYigyNTAsMjUwLDIyNSkgMCUscmdiKDEwMywxNjYsMTEzKSAxMDAlKTtcblx0fVxuXG5cdC5oYXMtYXRvbWljLWNyZWFtLWdyYWRpZW50LWJhY2tncm91bmQge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCgxMzVkZWcscmdiKDI1MywyMTUsMTU0KSAwJSxyZ2IoMCw3NCw4OSkgMTAwJSk7XG5cdH1cblxuXHQuaGFzLW5pZ2h0c2hhZGUtZ3JhZGllbnQtYmFja2dyb3VuZCB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KDEzNWRlZyxyZ2IoNTEsOSwxMDQpIDAlLHJnYig0OSwyMDUsMjA3KSAxMDAlKTtcblx0fVxuXG5cdC5oYXMtbWlkbmlnaHQtZ3JhZGllbnQtYmFja2dyb3VuZCB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KDEzNWRlZyxyZ2IoMiwzLDEyOSkgMCUscmdiKDQwLDExNiwyNTIpIDEwMCUpO1xuXHR9XG5cdC8qIHN0eWxlbGludC1lbmFibGUgQHN0eWxpc3RpYy9mdW5jdGlvbi1jb21tYS1zcGFjZS1hZnRlciAqL1xufVxuXG5AbWl4aW4gY3VzdG9tLXNjcm9sbGJhcnMtb24taG92ZXIoJGhhbmRsZS1jb2xvciwgJGhhbmRsZS1jb2xvci1ob3Zlcikge1xuXG5cdC8vIFdlYktpdFxuXHQmOjotd2Via2l0LXNjcm9sbGJhciB7XG5cdFx0d2lkdGg6IDEycHg7XG5cdFx0aGVpZ2h0OiAxMnB4O1xuXHR9XG5cdCY6Oi13ZWJraXQtc2Nyb2xsYmFyLXRyYWNrIHtcblx0XHRiYWNrZ3JvdW5kLWNvbG9yOiB0cmFuc3BhcmVudDtcblx0fVxuXHQmOjotd2Via2l0LXNjcm9sbGJhci10aHVtYiB7XG5cdFx0YmFja2dyb3VuZC1jb2xvcjogJGhhbmRsZS1jb2xvcjtcblx0XHRib3JkZXItcmFkaXVzOiA4cHg7XG5cdFx0Ym9yZGVyOiAzcHggc29saWQgdHJhbnNwYXJlbnQ7XG5cdFx0YmFja2dyb3VuZC1jbGlwOiBwYWRkaW5nLWJveDtcblx0fVxuXHQmOmhvdmVyOjotd2Via2l0LXNjcm9sbGJhci10aHVtYiwgLy8gVGhpcyBuZWVkcyBzcGVjaWZpY2l0eS5cblx0Jjpmb2N1czo6LXdlYmtpdC1zY3JvbGxiYXItdGh1bWIsXG5cdCY6Zm9jdXMtd2l0aGluOjotd2Via2l0LXNjcm9sbGJhci10aHVtYiB7XG5cdFx0YmFja2dyb3VuZC1jb2xvcjogJGhhbmRsZS1jb2xvci1ob3Zlcjtcblx0fVxuXG5cdC8vIEZpcmVmb3ggMTA5KyBhbmQgQ2hyb21lIDExMStcblx0c2Nyb2xsYmFyLXdpZHRoOiB0aGluO1xuXHRzY3JvbGxiYXItZ3V0dGVyOiBzdGFibGUgYm90aC1lZGdlcztcblx0c2Nyb2xsYmFyLWNvbG9yOiAkaGFuZGxlLWNvbG9yIHRyYW5zcGFyZW50OyAvLyBTeW50YXgsIFwiZGFya1wiLCBcImxpZ2h0XCIsIG9yIFwiI2hhbmRsZS1jb2xvciAjdHJhY2stY29sb3JcIlxuXG5cdCY6aG92ZXIsXG5cdCY6Zm9jdXMsXG5cdCY6Zm9jdXMtd2l0aGluIHtcblx0XHRzY3JvbGxiYXItY29sb3I6ICRoYW5kbGUtY29sb3ItaG92ZXIgdHJhbnNwYXJlbnQ7XG5cdH1cblxuXHQvLyBOZWVkZWQgdG8gZml4IGEgU2FmYXJpIHJlbmRlcmluZyBpc3N1ZS5cblx0d2lsbC1jaGFuZ2U6IHRyYW5zZm9ybTtcblxuXHQvLyBBbHdheXMgc2hvdyBzY3JvbGxiYXIgb24gTW9iaWxlIGRldmljZXMuXG5cdEBtZWRpYSAoaG92ZXI6IG5vbmUpIHtcblx0XHQmIHtcblx0XHRcdHNjcm9sbGJhci1jb2xvcjogJGhhbmRsZS1jb2xvci1ob3ZlciB0cmFuc3BhcmVudDtcblx0XHR9XG5cdH1cbn1cblxuQG1peGluIHNlbGVjdGVkLWJsb2NrLW91dGxpbmUoJHdpZHRoUmF0aW86IDEpIHtcblx0b3V0bGluZS1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXHRvdXRsaW5lLXN0eWxlOiBzb2xpZDtcblx0b3V0bGluZS13aWR0aDogY2FsYygjeyR3aWR0aFJhdGlvfSAqICh2YXIoLS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXMpIC8gdmFyKC0td3AtYmxvY2stZWRpdG9yLWlmcmFtZS16b29tLW91dC1zY2FsZSwgMSkpKTtcblx0b3V0bGluZS1vZmZzZXQ6IGNhbGMoI3skd2lkdGhSYXRpb30gKiAoKC0xICogdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSApIC8gdmFyKC0td3AtYmxvY2stZWRpdG9yLWlmcmFtZS16b29tLW91dC1zY2FsZSwgMSkpKTtcbn1cblxuQG1peGluIHNlbGVjdGVkLWJsb2NrLWZvY3VzKCR3aWR0aFJhdGlvOiAxKSB7XG5cdGNvbnRlbnQ6IFwiXCI7XG5cdHBvc2l0aW9uOiBhYnNvbHV0ZTtcblx0cG9pbnRlci1ldmVudHM6IG5vbmU7XG5cdHRvcDogMDtcblx0cmlnaHQ6IDA7XG5cdGJvdHRvbTogMDtcblx0bGVmdDogMDtcblx0QGluY2x1ZGUgc2VsZWN0ZWQtYmxvY2stb3V0bGluZSgkd2lkdGhSYXRpbyk7XG59XG4iLCIvKipcbiAqIEJyZWFrcG9pbnRzICYgTWVkaWEgUXVlcmllc1xuICovXG5cbi8vIE1vc3QgdXNlZCBicmVha3BvaW50c1xuJGJyZWFrLXhodWdlOiAxOTIwcHg7XG4kYnJlYWstaHVnZTogMTQ0MHB4O1xuJGJyZWFrLXdpZGU6IDEyODBweDtcbiRicmVhay14bGFyZ2U6IDEwODBweDtcbiRicmVhay1sYXJnZTogOTYwcHg7XHQvLyBhZG1pbiBzaWRlYmFyIGF1dG8gZm9sZHNcbiRicmVhay1tZWRpdW06IDc4MnB4O1x0Ly8gYWRtaW5iYXIgZ29lcyBiaWdcbiRicmVhay1zbWFsbDogNjAwcHg7XG4kYnJlYWstbW9iaWxlOiA0ODBweDtcbiRicmVhay16b29tZWQtaW46IDI4MHB4O1xuXG4vLyBBbGwgbWVkaWEgcXVlcmllcyBjdXJyZW50bHkgaW4gV29yZFByZXNzOlxuLy9cbi8vIG1pbi13aWR0aDogMjAwMHB4XG4vLyBtaW4td2lkdGg6IDE2ODBweFxuLy8gbWluLXdpZHRoOiAxMjUwcHhcbi8vIG1heC13aWR0aDogMTEyMHB4ICpcbi8vIG1heC13aWR0aDogMTAwMHB4XG4vLyBtaW4td2lkdGg6IDc2OXB4IGFuZCBtYXgtd2lkdGg6IDEwMDBweFxuLy8gbWF4LXdpZHRoOiA5NjBweCAqXG4vLyBtYXgtd2lkdGg6IDkwMHB4XG4vLyBtYXgtd2lkdGg6IDg1MHB4XG4vLyBtaW4td2lkdGg6IDgwMHB4IGFuZCBtYXgtd2lkdGg6IDE0OTlweFxuLy8gbWF4LXdpZHRoOiA4MDBweFxuLy8gbWF4LXdpZHRoOiA3OTlweFxuLy8gbWF4LXdpZHRoOiA3ODJweCAqXG4vLyBtYXgtd2lkdGg6IDc2OHB4XG4vLyBtYXgtd2lkdGg6IDY0MHB4ICpcbi8vIG1heC13aWR0aDogNjAwcHggKlxuLy8gbWF4LXdpZHRoOiA1MjBweFxuLy8gbWF4LXdpZHRoOiA1MDBweFxuLy8gbWF4LXdpZHRoOiA0ODBweCAqXG4vLyBtYXgtd2lkdGg6IDQwMHB4ICpcbi8vIG1heC13aWR0aDogMzgwcHhcbi8vIG1heC13aWR0aDogMzIwcHggKlxuLy9cbi8vIFRob3NlIG1hcmtlZCAqIHNlZW0gdG8gYmUgbW9yZSBjb21tb25seSB1c2VkIHRoYW4gdGhlIG90aGVycy5cbi8vIExldCdzIHRyeSBhbmQgdXNlIGFzIGZldyBvZiB0aGVzZSBhcyBwb3NzaWJsZSwgYW5kIGJlIG1pbmRmdWwgYWJvdXQgYWRkaW5nIG5ldyBvbmVzLCBzbyB3ZSBkb24ndCBtYWtlIHRoZSBzaXR1YXRpb24gd29yc2VcbiIsIi8qKlxuKiAgQ29udmVydHMgYSBoZXggdmFsdWUgaW50byB0aGUgcmdiIGVxdWl2YWxlbnQuXG4qXG4qIEBwYXJhbSB7c3RyaW5nfSBoZXggLSB0aGUgaGV4YWRlY2ltYWwgdmFsdWUgdG8gY29udmVydFxuKiBAcmV0dXJuIHtzdHJpbmd9IGNvbW1hIHNlcGFyYXRlZCByZ2IgdmFsdWVzXG4qL1xuXG5AdXNlIFwic2Fzczpjb2xvclwiO1xuQHVzZSBcInNhc3M6bWV0YVwiO1xuXG5AZnVuY3Rpb24gaGV4LXRvLXJnYigkaGV4KSB7XG5cdC8qXG5cdCAqIFRPRE86IGBjb2xvci57cmVkfGdyZWVufGJsdWV9YCB3aWxsIHRyaWdnZXIgYSBkZXByZWNhdGlvbiB3YXJuaW5nIGluIERhcnQgU2Fzcyxcblx0ICogYnV0IHRoZSBTYXNzIHVzZWQgYnkgdGhlIEd1dGVuYmVyZyBwcm9qZWN0IGRvZXNuJ3Qgc3VwcG9ydCBgY29sb3IuY2hhbm5lbCgpYCB5ZXQsXG5cdCAqIHNvIHdlIGNhbid0IG1pZ3JhdGUgdG8gaXQgYXQgdGhpcyB0aW1lLlxuXHQgKiBJbiB0aGUgZnV0dXJlLCBhZnRlciB0aGUgR3V0ZW5iZXJnIHByb2plY3QgaGFzIGJlZW4gZnVsbHkgbWlncmF0ZWQgdG8gRGFydCBTYXNzLFxuXHQgKiBSZW1vdmUgdGhpcyBjb25kaXRpb25hbCBzdGF0ZW1lbnQgYW5kIHVzZSBvbmx5IGBjb2xvci5jaGFubmVsKClgLlxuXHQgKi9cblx0QGlmIG1ldGEuZnVuY3Rpb24tZXhpc3RzKFwiY2hhbm5lbFwiLCBcImNvbG9yXCIpIHtcblx0XHRAcmV0dXJuIGNvbG9yLmNoYW5uZWwoJGhleCwgXCJyZWRcIiksIGNvbG9yLmNoYW5uZWwoJGhleCwgXCJncmVlblwiKSwgY29sb3IuY2hhbm5lbCgkaGV4LCBcImJsdWVcIik7XG5cdH0gQGVsc2Uge1xuXHRcdEByZXR1cm4gY29sb3IucmVkKCRoZXgpLCBjb2xvci5ncmVlbigkaGV4KSwgY29sb3IuYmx1ZSgkaGV4KTtcblx0fVxufVxuIiwiLyoqXG4gKiBMb25nIGNvbnRlbnQgZmFkZSBtaXhpblxuICpcbiAqIENyZWF0ZXMgYSBmYWRpbmcgb3ZlcmxheSB0byBzaWduaWZ5IHRoYXQgdGhlIGNvbnRlbnQgaXMgbG9uZ2VyXG4gKiB0aGFuIHRoZSBzcGFjZSBhbGxvd3MuXG4gKi9cblxuQG1peGluIGxvbmctY29udGVudC1mYWRlKCRkaXJlY3Rpb246IHJpZ2h0LCAkc2l6ZTogMjAlLCAkY29sb3I6ICNmZmYsICRlZGdlOiAwLCAkei1pbmRleDogZmFsc2UpIHtcblx0Y29udGVudDogXCJcIjtcblx0ZGlzcGxheTogYmxvY2s7XG5cdHBvc2l0aW9uOiBhYnNvbHV0ZTtcblx0LXdlYmtpdC10b3VjaC1jYWxsb3V0OiBub25lO1xuXHQtd2Via2l0LXVzZXItc2VsZWN0OiBub25lO1xuXHQta2h0bWwtdXNlci1zZWxlY3Q6IG5vbmU7XG5cdC1tb3otdXNlci1zZWxlY3Q6IG5vbmU7XG5cdC1tcy11c2VyLXNlbGVjdDogbm9uZTtcblx0dXNlci1zZWxlY3Q6IG5vbmU7XG5cdHBvaW50ZXItZXZlbnRzOiBub25lO1xuXG5cdEBpZiAkei1pbmRleCB7XG5cdFx0ei1pbmRleDogJHotaW5kZXg7XG5cdH1cblxuXHRAaWYgJGRpcmVjdGlvbiA9PSBcImJvdHRvbVwiIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQodG8gdG9wLCB0cmFuc3BhcmVudCwgJGNvbG9yIDkwJSk7XG5cdFx0bGVmdDogJGVkZ2U7XG5cdFx0cmlnaHQ6ICRlZGdlO1xuXHRcdHRvcDogJGVkZ2U7XG5cdFx0Ym90dG9tOiBjYWxjKDEwMCUgLSAkc2l6ZSk7XG5cdFx0d2lkdGg6IGF1dG87XG5cdH1cblxuXHRAaWYgJGRpcmVjdGlvbiA9PSBcInRvcFwiIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQodG8gYm90dG9tLCB0cmFuc3BhcmVudCwgJGNvbG9yIDkwJSk7XG5cdFx0dG9wOiBjYWxjKDEwMCUgLSAkc2l6ZSk7XG5cdFx0bGVmdDogJGVkZ2U7XG5cdFx0cmlnaHQ6ICRlZGdlO1xuXHRcdGJvdHRvbTogJGVkZ2U7XG5cdFx0d2lkdGg6IGF1dG87XG5cdH1cblxuXHRAaWYgJGRpcmVjdGlvbiA9PSBcImxlZnRcIiB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KHRvIGxlZnQsIHRyYW5zcGFyZW50LCAkY29sb3IgOTAlKTtcblx0XHR0b3A6ICRlZGdlO1xuXHRcdGxlZnQ6ICRlZGdlO1xuXHRcdGJvdHRvbTogJGVkZ2U7XG5cdFx0cmlnaHQ6IGF1dG87XG5cdFx0d2lkdGg6ICRzaXplO1xuXHRcdGhlaWdodDogYXV0bztcblx0fVxuXG5cdEBpZiAkZGlyZWN0aW9uID09IFwicmlnaHRcIiB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KHRvIHJpZ2h0LCB0cmFuc3BhcmVudCwgJGNvbG9yIDkwJSk7XG5cdFx0dG9wOiAkZWRnZTtcblx0XHRib3R0b206ICRlZGdlO1xuXHRcdHJpZ2h0OiAkZWRnZTtcblx0XHRsZWZ0OiBhdXRvO1xuXHRcdHdpZHRoOiAkc2l6ZTtcblx0XHRoZWlnaHQ6IGF1dG87XG5cdH1cbn1cbiIsIkB1c2UgXCJAd29yZHByZXNzL2Jhc2Utc3R5bGVzL3ZhcmlhYmxlc1wiO1xuQHVzZSBcIkB3b3JkcHJlc3MvYmFzZS1zdHlsZXMvbWl4aW5zXCI7XG5cbi5ib290LWxheW91dCB7XG5cdGhlaWdodDogMTAwJTtcblx0d2lkdGg6IDEwMCU7XG5cdGRpc3BsYXk6IGZsZXg7XG5cdGZsZXgtZGlyZWN0aW9uOiByb3c7XG5cdGNvbG9yOiB2YXIoLS13cGRzLWNvbG9yLWZnLWNvbnRlbnQtbmV1dHJhbCwgIzFlMWUxZSk7XG5cdGlzb2xhdGlvbjogaXNvbGF0ZTtcblx0YmFja2dyb3VuZDogdmFyKC0td3Bkcy1jb2xvci1iZy1zdXJmYWNlLW5ldXRyYWwtd2VhaywgI2YwZjBmMCk7XG59XG5cbi5ib290LWxheW91dF9fc2lkZWJhci1iYWNrZHJvcCB7XG5cdHBvc2l0aW9uOiBmaXhlZDtcblx0dG9wOiAwO1xuXHRsZWZ0OiAwO1xuXHRyaWdodDogMDtcblx0Ym90dG9tOiAwO1xuXHRiYWNrZ3JvdW5kLWNvbG9yOiByZ2JhKDAsIDAsIDAsIDAuNSk7XG5cdHotaW5kZXg6IDEwMDAwMjtcblx0Y3Vyc29yOiBwb2ludGVyO1xufVxuXG4uYm9vdC1sYXlvdXRfX3NpZGViYXIge1xuXHRoZWlnaHQ6IDEwMCU7XG5cdGZsZXgtc2hyaW5rOiAwO1xuXHR3aWR0aDogMjQwcHg7XG5cdHBvc2l0aW9uOiByZWxhdGl2ZTtcblx0b3ZlcmZsb3c6IGhpZGRlbjtcblxuXHQmLmlzLW1vYmlsZSB7XG5cdFx0cG9zaXRpb246IGZpeGVkO1xuXHRcdHRvcDogMDtcblx0XHRib3R0b206IDA7XG5cdFx0bGVmdDogMDtcblx0XHR3aWR0aDogMzAwcHg7XG5cdFx0bWF4LXdpZHRoOiA4NXZ3O1xuXHRcdGJhY2tncm91bmQ6IHZhcigtLXdwZHMtY29sb3ItYmctc3VyZmFjZS1uZXV0cmFsLXdlYWssICNmMGYwZjApO1xuXHRcdHotaW5kZXg6IDEwMDAwMztcblx0XHRib3gtc2hhZG93OiAycHggMCA4cHggcmdiYSgwLCAwLCAwLCAwLjIpO1xuXHR9XG59XG5cbi5ib290LWxheW91dF9fbW9iaWxlLXNpZGViYXItZHJhd2VyIHtcblx0cG9zaXRpb246IGZpeGVkO1xuXHR0b3A6IDA7XG5cdGxlZnQ6IDA7XG5cdHJpZ2h0OiAwO1xuXHR6LWluZGV4OiAzO1xuXHRiYWNrZ3JvdW5kOiB2YXIoLS13cGRzLWNvbG9yLWJnLXN1cmZhY2UtbmV1dHJhbCwgI2ZmZik7XG5cdHBhZGRpbmc6IHZhcmlhYmxlcy4kZ3JpZC11bml0LTIwO1xuXHRib3JkZXItYm90dG9tOiAxcHggc29saWQgdmFyKC0td3Bkcy1jb2xvci1zdHJva2Utc3VyZmFjZS1uZXV0cmFsLXdlYWssICNkZGQpO1xuXHRkaXNwbGF5OiBmbGV4O1xuXHRqdXN0aWZ5LWNvbnRlbnQ6IGZsZXgtc3RhcnQ7XG5cdGFsaWduLWl0ZW1zOiBjZW50ZXI7XG59XG5cbi5ib290LWxheW91dF9fY2FudmFzLmhhcy1tb2JpbGUtZHJhd2VyIHtcblx0cG9zaXRpb246IHJlbGF0aXZlO1xuXHRwYWRkaW5nLXRvcDogNjVweDtcbn1cblxuLmJvb3QtbGF5b3V0X19zdXJmYWNlcyB7XG5cdGRpc3BsYXk6IGZsZXg7XG5cdGZsZXgtZ3JvdzogMTtcblx0bWFyZ2luOiAwO1xuXHRnYXA6IHZhcmlhYmxlcy4kZ3JpZC11bml0LTEwO1xuXG5cdEBpbmNsdWRlIG1peGlucy5icmVhay1tZWRpdW0ge1xuXHRcdG1hcmdpbjogdmFyaWFibGVzLiRncmlkLXVuaXQtMTA7XG5cblx0XHQuYm9vdC1sYXlvdXQtLXNpbmdsZS1wYWdlICYge1xuXHRcdFx0bWFyZ2luLXRvcDogMDtcblx0XHRcdG1hcmdpbi1sZWZ0OiAwO1xuXHRcdH1cblx0fVxufVxuXG4uYm9vdC1sYXlvdXRfX3N0YWdlLFxuLmJvb3QtbGF5b3V0X19pbnNwZWN0b3Ige1xuXHRmbGV4OiAxO1xuXHRvdmVyZmxvdy15OiBhdXRvO1xuXHRiYWNrZ3JvdW5kOiB2YXIoLS13cGRzLWNvbG9yLWJnLXN1cmZhY2UtbmV1dHJhbCwgI2ZmZik7XG5cdGNvbG9yOiB2YXIoLS13cGRzLWNvbG9yLWZnLWNvbnRlbnQtbmV1dHJhbCwgIzFlMWUxZSk7XG5cdGJveC1zaGFkb3c6IDAgMXB4IDNweCByZ2JhKDAsIDAsIDAsIDAuMSk7XG5cdGJvcmRlcjogMXB4IHNvbGlkIHZhcigtLXdwZHMtY29sb3Itc3Ryb2tlLXN1cmZhY2UtbmV1dHJhbC13ZWFrLCAjZGRkKTtcblx0cG9zaXRpb246IHJlbGF0aXZlO1xuXG5cdC8vIE1vYmlsZS1maXJzdDogc3VyZmFjZXMgdGFrZSBmdWxsIHNjcmVlbiB3aXRoIGZpeGVkIHBvc2l0aW9uaW5nXG5cdHBvc2l0aW9uOiBmaXhlZDtcblx0dG9wOiAwO1xuXHRsZWZ0OiAwO1xuXHRyaWdodDogMDtcblx0Ym90dG9tOiAwO1xuXHR3aWR0aDogMTAwdnc7XG5cdGhlaWdodDogMTAwdmg7XG5cdGJvcmRlci1yYWRpdXM6IDA7XG5cdG1hcmdpbjogMDtcblxuXHQvLyBEZXNrdG9wOiByZXN0b3JlIG9yaWdpbmFsIHN0eWxlc1xuXHRAaW5jbHVkZSBtaXhpbnMuYnJlYWstbWVkaXVtIHtcblx0XHRwb3NpdGlvbjogc3RhdGljO1xuXHRcdHdpZHRoOiBhdXRvO1xuXHRcdGhlaWdodDogYXV0bztcblx0XHRib3JkZXItcmFkaXVzOiA4cHg7XG5cdFx0bWFyZ2luOiAwO1xuXHR9XG59XG5cbi5ib290LWxheW91dF9fc3RhZ2Uge1xuXHR6LWluZGV4OiAyOyAvLyBIaWdoZXN0IHN1cmZhY2UgcHJpb3JpdHkgb24gbW9iaWxlXG5cblx0QGluY2x1ZGUgbWl4aW5zLmJyZWFrLW1lZGl1bSB7XG5cdFx0ei1pbmRleDogYXV0bztcblx0fVxufVxuXG4uYm9vdC1sYXlvdXRfX2luc3BlY3RvciB7XG5cdHotaW5kZXg6IDM7IC8vIE1lZGl1bSBzdXJmYWNlIHByaW9yaXR5IG9uIG1vYmlsZVxuXG5cdEBpbmNsdWRlIG1peGlucy5icmVhay1tZWRpdW0ge1xuXHRcdHotaW5kZXg6IGF1dG87XG5cdH1cbn1cblxuLmJvb3QtbGF5b3V0X19jYW52YXMge1xuXHRmbGV4OiAxO1xuXHRvdmVyZmxvdy15OiBhdXRvO1xuXHRiYWNrZ3JvdW5kOiB2YXIoLS13cGRzLWNvbG9yLWJnLXN1cmZhY2UtbmV1dHJhbCwgI2ZmZik7XG5cdGNvbG9yOiB2YXIoLS13cGRzLWNvbG9yLWZnLWNvbnRlbnQtbmV1dHJhbCwgIzFlMWUxZSk7XG5cdGJveC1zaGFkb3c6IDAgMXB4IDNweCByZ2JhKDAsIDAsIDAsIDAuMSk7XG5cdGJvcmRlcjogMXB4IHNvbGlkIHZhcigtLXdwZHMtY29sb3Itc3Ryb2tlLXN1cmZhY2UtbmV1dHJhbC13ZWFrLCAjZGRkKTtcblx0cG9zaXRpb246IHJlbGF0aXZlO1xuXG5cdC8vIE1vYmlsZS1maXJzdDogZnVsbCBzY3JlZW4gd2l0aCBsb3dlc3QgcHJpb3JpdHlcblx0cG9zaXRpb246IGZpeGVkO1xuXHR0b3A6IDA7XG5cdGxlZnQ6IDA7XG5cdHJpZ2h0OiAwO1xuXHRib3R0b206IDA7XG5cdHdpZHRoOiAxMDB2dztcblx0aGVpZ2h0OiAxMDB2aDtcblx0ei1pbmRleDogMTsgLy8gTG93ZXN0IHN1cmZhY2UgcHJpb3JpdHlcblx0Ym9yZGVyLXJhZGl1czogMDtcblx0bWFyZ2luOiAwO1xuXG5cdC8vIERlc2t0b3A6IHJlc3RvcmUgb3JpZ2luYWwgc3R5bGVzXG5cdEBpbmNsdWRlIG1peGlucy5icmVhay1tZWRpdW0ge1xuXHRcdHBvc2l0aW9uOiBzdGF0aWM7XG5cdFx0d2lkdGg6IGF1dG87XG5cdFx0aGVpZ2h0OiBhdXRvO1xuXHRcdGJvcmRlci1yYWRpdXM6IDhweDtcblx0XHR6LWluZGV4OiBhdXRvO1xuXHR9XG59XG5cbi5ib290LWxheW91dC5oYXMtY2FudmFzIC5ib290LWxheW91dF9fc3RhZ2UsXG4uYm9vdC1sYXlvdXRfX2luc3BlY3RvciB7XG5cdEBpbmNsdWRlIG1peGlucy5icmVhay1tZWRpdW0ge1xuXHRcdG1heC13aWR0aDogNDAwcHg7XG5cdH1cbn1cblxuLmJvb3QtbGF5b3V0X19jYW52YXMgLmludGVyZmFjZS1pbnRlcmZhY2Utc2tlbGV0b24ge1xuXHRwb3NpdGlvbjogcmVsYXRpdmU7XG5cdGhlaWdodDogMTAwJTtcblx0dG9wOiAwICFpbXBvcnRhbnQ7XG5cdGxlZnQ6IDAgIWltcG9ydGFudDtcbn1cblxuLy8gRnVsbC1zY3JlZW4gY2FudmFzIG1vZGVcbi5ib290LWxheW91dC5oYXMtZnVsbC1jYW52YXMgLmJvb3QtbGF5b3V0X19zdXJmYWNlcyB7XG5cdG1hcmdpbjogMDtcblx0Z2FwOiAwO1xufVxuXG4uYm9vdC1sYXlvdXQuaGFzLWZ1bGwtY2FudmFzIC5ib290LWxheW91dF9fc3RhZ2UsXG4uYm9vdC1sYXlvdXQuaGFzLWZ1bGwtY2FudmFzIC5ib290LWxheW91dF9faW5zcGVjdG9yIHtcblx0ZGlzcGxheTogbm9uZTtcbn1cblxuLmJvb3QtbGF5b3V0Lmhhcy1mdWxsLWNhbnZhcyAuYm9vdC1sYXlvdXRfX2NhbnZhcyB7XG5cdHBvc2l0aW9uOiBmaXhlZDtcblx0dG9wOiAwO1xuXHRsZWZ0OiAwO1xuXHRyaWdodDogMDtcblx0Ym90dG9tOiAwO1xuXHRtYXgtd2lkdGg6IG5vbmU7XG5cdG1hcmdpbjogMDtcblx0Ym9yZGVyLXJhZGl1czogMDtcblx0Ym9yZGVyOiBub25lO1xuXHRib3gtc2hhZG93OiBub25lO1xuXHRvdmVyZmxvdzogaGlkZGVuO1xufVxuIl19 */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css10));
var { useLocation: useLocation2, useMatches: useMatches3, Outlet } = unlock(routePrivateApis5);
function Root() {
  const matches = useMatches3();
  const location = useLocation2();
  const currentMatch = matches[matches.length - 1];
  const canvas = currentMatch?.loaderData?.canvas;
  const routeContentModule = currentMatch?.loaderData?.routeContentModule;
  const isFullScreen = canvas && !canvas.isPreview;
  useRouteTitle();
  const isMobileViewport = (0, import_compose4.useViewportMatch)("medium", "<");
  const [isMobileSidebarOpen, setIsMobileSidebarOpen] = (0, import_element13.useState)(false);
  const disableMotion = (0, import_compose4.useReducedMotion)();
  (0, import_element13.useEffect)(() => {
    setIsMobileSidebarOpen(false);
  }, [location.pathname, isMobileViewport]);
  return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_components14.SlotFillProvider, { children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(UserThemeProvider, { isRoot: true, color: { bg: "#f8f8f8" }, children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(UserThemeProvider, { color: { bg: "#1d2327" }, children: /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(
    "div",
    {
      className: clsx_default("boot-layout", {
        "has-canvas": !!canvas || canvas === null,
        "has-full-canvas": isFullScreen
      }),
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_commands2.CommandMenu, {}),
        /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(SavePanel, {}),
        /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_editor4.EditorSnackbars, {}),
        isMobileViewport && /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(page_default.SidebarToggleFill, { children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
          import_components14.Button,
          {
            icon: menu_default,
            onClick: () => setIsMobileSidebarOpen(true),
            label: (0, import_i18n10.__)("Open navigation panel"),
            size: "compact"
          }
        ) }),
        /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_components14.__unstableAnimatePresence, { children: isMobileViewport && isMobileSidebarOpen && !isFullScreen && /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
          import_components14.__unstableMotion.div,
          {
            initial: { opacity: 0 },
            animate: { opacity: 1 },
            exit: { opacity: 0 },
            transition: {
              type: "tween",
              duration: disableMotion ? 0 : 0.2,
              ease: "easeOut"
            },
            className: "boot-layout__sidebar-backdrop",
            onClick: () => setIsMobileSidebarOpen(false),
            onKeyDown: (event) => {
              if (event.key === "Escape") {
                setIsMobileSidebarOpen(false);
              }
            },
            role: "button",
            tabIndex: -1,
            "aria-label": (0, import_i18n10.__)(
              "Close navigation panel"
            )
          }
        ) }),
        /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(import_components14.__unstableAnimatePresence, { children: isMobileViewport && isMobileSidebarOpen && !isFullScreen && /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
          import_components14.__unstableMotion.div,
          {
            initial: { x: "-100%" },
            animate: { x: 0 },
            exit: { x: "-100%" },
            transition: {
              type: "tween",
              duration: disableMotion ? 0 : 0.2,
              ease: "easeOut"
            },
            className: "boot-layout__sidebar is-mobile",
            children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(Sidebar, {})
          }
        ) }),
        !isMobileViewport && !isFullScreen && /* @__PURE__ */ (0, import_jsx_runtime31.jsx)("div", { className: "boot-layout__sidebar", children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(Sidebar, {}) }),
        /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)("div", { className: "boot-layout__surfaces", children: [
          /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(UserThemeProvider, { color: { bg: "#ffffff" }, children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(Outlet, {}) }),
          (canvas || canvas === null) && /* @__PURE__ */ (0, import_jsx_runtime31.jsxs)(
            "div",
            {
              className: clsx_default("boot-layout__canvas", {
                "has-mobile-drawer": canvas?.isPreview && isMobileViewport
              }),
              children: [
                canvas?.isPreview && isMobileViewport && /* @__PURE__ */ (0, import_jsx_runtime31.jsx)("div", { className: "boot-layout__mobile-sidebar-drawer", children: /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
                  import_components14.Button,
                  {
                    icon: menu_default,
                    onClick: () => setIsMobileSidebarOpen(
                      true
                    ),
                    label: (0, import_i18n10.__)(
                      "Open navigation panel"
                    ),
                    size: "compact"
                  }
                ) }),
                /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
                  CanvasRenderer,
                  {
                    canvas,
                    routeContentModule
                  }
                )
              ]
            }
          )
        ] })
      ]
    }
  ) }) }) });
}

// packages/boot/build-module/components/app/router.js
var import_jsx_runtime32 = __toESM(require_jsx_runtime());
var {
  createLazyRoute,
  createRouter,
  createRootRoute,
  createRoute,
  RouterProvider,
  createBrowserHistory,
  parseHref,
  useLoaderData
} = unlock(routePrivateApis6);
function NotFoundComponent() {
  return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("div", { className: "boot-layout__stage", children: /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(page_default, { title: (0, import_i18n11.__)("Route not found"), hasPadding: true, children: (0, import_i18n11.__)("The page you're looking for does not exist") }) });
}
function createRouteFromDefinition(route, parentRoute) {
  let tanstackRoute = createRoute({
    getParentRoute: () => parentRoute,
    path: route.path,
    beforeLoad: async (opts) => {
      if (route.route_module) {
        const module = await import(route.route_module);
        const routeConfig = module.route || {};
        if (routeConfig.beforeLoad) {
          return routeConfig.beforeLoad({
            params: opts.params || {},
            search: opts.search || {}
          });
        }
      }
    },
    loader: async (opts) => {
      let routeConfig = {};
      if (route.route_module) {
        const module = await import(route.route_module);
        routeConfig = module.route || {};
      }
      const context = {
        params: opts.params || {},
        search: opts.deps || {}
      };
      const [, loaderData, canvasData, titleData] = await Promise.all([
        (0, import_data10.resolveSelect)(import_core_data6.store).getEntityRecord(
          "root",
          "__unstableBase"
        ),
        routeConfig.loader ? routeConfig.loader(context) : Promise.resolve(void 0),
        routeConfig.canvas ? routeConfig.canvas(context) : Promise.resolve(void 0),
        routeConfig.title ? routeConfig.title(context) : Promise.resolve(void 0)
      ]);
      let inspector = true;
      if (routeConfig.inspector) {
        inspector = await routeConfig.inspector(context);
      }
      return {
        ...loaderData,
        canvas: canvasData,
        inspector,
        title: titleData,
        routeContentModule: route.content_module
      };
    },
    loaderDeps: (opts) => opts.search
  });
  tanstackRoute = tanstackRoute.lazy(async () => {
    const module = route.content_module ? await import(route.content_module) : {};
    const Stage = module.stage;
    const Inspector = module.inspector;
    return createLazyRoute(route.path)({
      component: function RouteComponent() {
        const { inspector: showInspector } = useLoaderData({ from: route.path }) ?? {};
        return /* @__PURE__ */ (0, import_jsx_runtime32.jsxs)(import_jsx_runtime32.Fragment, { children: [
          Stage && /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("div", { className: "boot-layout__stage", children: /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(Stage, {}) }),
          Inspector && showInspector && /* @__PURE__ */ (0, import_jsx_runtime32.jsx)("div", { className: "boot-layout__inspector", children: /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(Inspector, {}) })
        ] });
      }
    });
  });
  return tanstackRoute;
}
function createRouteTree(routes, rootComponent = Root) {
  const rootRoute = createRootRoute({
    component: rootComponent,
    context: () => ({})
  });
  const dynamicRoutes = routes.map(
    (route) => createRouteFromDefinition(route, rootRoute)
  );
  return rootRoute.addChildren(dynamicRoutes);
}
function createPathHistory() {
  return createBrowserHistory({
    parseLocation: () => {
      const url = new URL(window.location.href);
      const path = url.searchParams.get("p") || "/";
      const pathHref = `${path}${url.hash}`;
      return parseHref(pathHref, window.history.state);
    },
    createHref: (href) => {
      const searchParams = new URLSearchParams(window.location.search);
      searchParams.set("p", href);
      return `${window.location.pathname}?${searchParams}`;
    }
  });
}
function Router({
  routes,
  rootComponent = Root
}) {
  const router = (0, import_element14.useMemo)(() => {
    const history = createPathHistory();
    const routeTree = createRouteTree(routes, rootComponent);
    return createRouter({
      history,
      routeTree,
      defaultPreload: "intent",
      defaultNotFoundComponent: NotFoundComponent,
      defaultViewTransition: {
        types: ({
          fromLocation
        }) => {
          if (!fromLocation) {
            return false;
          }
          return ["navigate"];
        }
      }
    });
  }, [routes, rootComponent]);
  return /* @__PURE__ */ (0, import_jsx_runtime32.jsx)(RouterProvider, { router });
}

// packages/boot/build-module/components/root/single-page.js
var import_commands3 = __toESM(require_commands());
var import_editor5 = __toESM(require_editor());
import { privateApis as routePrivateApis7 } from "@wordpress/route";
var import_jsx_runtime33 = __toESM(require_jsx_runtime());
var css11 = `/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
/**
 * Typography
 */
/**
 * Breakpoints & Media Queries
 */
/**
*  Converts a hex value into the rgb equivalent.
*
* @param {string} hex - the hexadecimal value to convert
* @return {string} comma separated rgb values
*/
/**
 * Long content fade mixin
 *
 * Creates a fading overlay to signify that the content is longer
 * than the space allows.
 */
/**
 * Breakpoint mixins
 */
/**
 * Focus styles.
 */
/**
 * Applies editor left position to the selector passed as argument
 */
/**
 * Styles that are reused verbatim in a few places
 */
/**
 * Allows users to opt-out of animations via OS-level preferences.
 */
/**
 * Reset default styles for JavaScript UI based pages.
 * This is a WP-admin agnostic reset
 */
/**
 * Reset the WP Admin page styles for Gutenberg-like pages.
 */
.boot-layout {
  height: 100%;
  width: 100%;
  display: flex;
  flex-direction: row;
  color: var(--wpds-color-fg-content-neutral, #1e1e1e);
  isolation: isolate;
  background: var(--wpds-color-bg-surface-neutral-weak, #f0f0f0);
}

.boot-layout__sidebar-backdrop {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  z-index: 100002;
  cursor: pointer;
}

.boot-layout__sidebar {
  height: 100%;
  flex-shrink: 0;
  width: 240px;
  position: relative;
  overflow: hidden;
}
.boot-layout__sidebar.is-mobile {
  position: fixed;
  top: 0;
  bottom: 0;
  left: 0;
  width: 300px;
  max-width: 85vw;
  background: var(--wpds-color-bg-surface-neutral-weak, #f0f0f0);
  z-index: 100003;
  box-shadow: 2px 0 8px rgba(0, 0, 0, 0.2);
}

.boot-layout__mobile-sidebar-drawer {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  z-index: 3;
  background: var(--wpds-color-bg-surface-neutral, #fff);
  padding: 16px;
  border-bottom: 1px solid var(--wpds-color-stroke-surface-neutral-weak, #ddd);
  display: flex;
  justify-content: flex-start;
  align-items: center;
}

.boot-layout__canvas.has-mobile-drawer {
  position: relative;
  padding-top: 65px;
}

.boot-layout__surfaces {
  display: flex;
  flex-grow: 1;
  margin: 0;
  gap: 8px;
}
@media (min-width: 782px) {
  .boot-layout__surfaces {
    margin: 8px;
  }
  .boot-layout--single-page .boot-layout__surfaces {
    margin-top: 0;
    margin-left: 0;
  }
}

.boot-layout__stage,
.boot-layout__inspector {
  flex: 1;
  overflow-y: auto;
  background: var(--wpds-color-bg-surface-neutral, #fff);
  color: var(--wpds-color-fg-content-neutral, #1e1e1e);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid var(--wpds-color-stroke-surface-neutral-weak, #ddd);
  position: relative;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100vw;
  height: 100vh;
  border-radius: 0;
  margin: 0;
}
@media (min-width: 782px) {
  .boot-layout__stage,
  .boot-layout__inspector {
    position: static;
    width: auto;
    height: auto;
    border-radius: 8px;
    margin: 0;
  }
}

.boot-layout__stage {
  z-index: 2;
}
@media (min-width: 782px) {
  .boot-layout__stage {
    z-index: auto;
  }
}

.boot-layout__inspector {
  z-index: 3;
}
@media (min-width: 782px) {
  .boot-layout__inspector {
    z-index: auto;
  }
}

.boot-layout__canvas {
  flex: 1;
  overflow-y: auto;
  background: var(--wpds-color-bg-surface-neutral, #fff);
  color: var(--wpds-color-fg-content-neutral, #1e1e1e);
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
  border: 1px solid var(--wpds-color-stroke-surface-neutral-weak, #ddd);
  position: relative;
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  width: 100vw;
  height: 100vh;
  z-index: 1;
  border-radius: 0;
  margin: 0;
}
@media (min-width: 782px) {
  .boot-layout__canvas {
    position: static;
    width: auto;
    height: auto;
    border-radius: 8px;
    z-index: auto;
  }
}

@media (min-width: 782px) {
  .boot-layout.has-canvas .boot-layout__stage,
  .boot-layout__inspector {
    max-width: 400px;
  }
}

.boot-layout__canvas .interface-interface-skeleton {
  position: relative;
  height: 100%;
  top: 0 !important;
  left: 0 !important;
}

.boot-layout.has-full-canvas .boot-layout__surfaces {
  margin: 0;
  gap: 0;
}

.boot-layout.has-full-canvas .boot-layout__stage,
.boot-layout.has-full-canvas .boot-layout__inspector {
  display: none;
}

.boot-layout.has-full-canvas .boot-layout__canvas {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  max-width: none;
  margin: 0;
  border-radius: 0;
  border: none;
  box-shadow: none;
  overflow: hidden;
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy9ib290L3NyYy9jb21wb25lbnRzL3Jvb3QiLCJzb3VyY2VzIjpbIi4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL192YXJpYWJsZXMuc2NzcyIsIi4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19jb2xvcnMuc2NzcyIsIi4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19taXhpbnMuc2NzcyIsIi4uLy4uLy4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19icmVha3BvaW50cy5zY3NzIiwiLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX2Z1bmN0aW9ucy5zY3NzIiwiLi4vLi4vLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX2xvbmctY29udGVudC1mYWRlLnNjc3MiLCJzdHlsZS5zY3NzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FDQUE7QUFBQTtBQUFBO0FEVUE7QUFBQTtBQUFBO0FBT0E7QUFBQTtBQUFBO0FBNkJBO0FBQUE7QUFBQTtBQUFBO0FBaUJBO0FBQUE7QUFBQTtBQVdBO0FBQUE7QUFBQTtBQWdCQTtBQUFBO0FBQUE7QUF5QkE7QUFBQTtBQUFBO0FBS0E7QUFBQTtBQUFBO0FBZUE7QUFBQTtBQUFBO0FBbUJBO0FBQUE7QUFBQTtBQVNBO0FBQUE7QUFBQTtBQUFBO0FFbktBO0FBQUE7QUFBQTtBQ0FBO0FBQUE7QUFBQTtBQ0FBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQ0FBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBSDRFQTtBQUFBO0FBQUE7QUEwREE7QUFBQTtBQUFBO0FBZ0RBO0FBQUE7QUFBQTtBQXFDQTtBQUFBO0FBQUE7QUFvQkE7QUFBQTtBQUFBO0FBd0xBO0FBQUE7QUFBQTtBQUFBO0FBZ0RBO0FBQUE7QUFBQTtBSXBkQTtFQUNDO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBOzs7QUFHRDtFQUNDO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7OztBQUdEO0VBQ0M7RUFDQTtFQUNBO0VBQ0E7RUFDQTs7QUFFQTtFQUNDO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTs7O0FBSUY7RUFDQztFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQSxTTkljO0VNSGQ7RUFDQTtFQUNBO0VBQ0E7OztBQUdEO0VBQ0M7RUFDQTs7O0FBR0Q7RUFDQztFQUNBO0VBQ0E7RUFDQSxLTmRjOztBRTBEZDtFSWhERDtJQU9FLFFOakJhOztFTW1CYjtJQUNDO0lBQ0E7Ozs7QUFLSDtBQUFBO0VBRUM7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFHQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7O0FKYUE7RUloQ0Q7QUFBQTtJQXVCRTtJQUNBO0lBQ0E7SUFDQTtJQUNBOzs7O0FBSUY7RUFDQzs7QUpBQTtFSUREO0lBSUU7Ozs7QUFJRjtFQUNDOztBSlJBO0VJT0Q7SUFJRTs7OztBQUlGO0VBQ0M7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFHQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTs7QUpsQ0E7RUllRDtJQXVCRTtJQUNBO0lBQ0E7SUFDQTtJQUNBOzs7O0FKMUNEO0VJOENEO0FBQUE7SUFHRTs7OztBQUlGO0VBQ0M7RUFDQTtFQUNBO0VBQ0E7OztBQUlEO0VBQ0M7RUFDQTs7O0FBR0Q7QUFBQTtFQUVDOzs7QUFHRDtFQUNDO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0EiLCJzb3VyY2VzQ29udGVudCI6WyIvKipcbiAqIFNDU1MgVmFyaWFibGVzLlxuICpcbiAqIFBsZWFzZSB1c2UgdmFyaWFibGVzIGZyb20gdGhpcyBzaGVldCB0byBlbnN1cmUgY29uc2lzdGVuY3kgYWNyb3NzIHRoZSBVSS5cbiAqIERvbid0IGFkZCB0byB0aGlzIHNoZWV0IHVubGVzcyB5b3UncmUgcHJldHR5IHN1cmUgdGhlIHZhbHVlIHdpbGwgYmUgcmV1c2VkIGluIG1hbnkgcGxhY2VzLlxuICogRm9yIGV4YW1wbGUsIGRvbid0IGFkZCBydWxlcyB0byB0aGlzIHNoZWV0IHRoYXQgYWZmZWN0IGJsb2NrIHZpc3VhbHMuIEl0J3MgcHVyZWx5IGZvciBVSS5cbiAqL1xuXG5AdXNlIFwiLi9jb2xvcnNcIjtcblxuLyoqXG4gKiBGb250cyAmIGJhc2ljIHZhcmlhYmxlcy5cbiAqL1xuXG4kZGVmYXVsdC1mb250OiAtYXBwbGUtc3lzdGVtLCBCbGlua01hY1N5c3RlbUZvbnQsXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCxcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7IC8vIFRvZG86IGRlcHJlY2F0ZSBpbiBmYXZvciBvZiAkZmFtaWx5IHZhcmlhYmxlc1xuJGRlZmF1bHQtbGluZS1oZWlnaHQ6IDEuNDsgLy8gVG9kbzogZGVwcmVjYXRlIGluIGZhdm9yIG9mICRsaW5lLWhlaWdodCB0b2tlbnNcblxuLyoqXG4gKiBUeXBvZ3JhcGh5XG4gKi9cblxuLy8gU2l6ZXNcbiRmb250LXNpemUteC1zbWFsbDogMTFweDtcbiRmb250LXNpemUtc21hbGw6IDEycHg7XG4kZm9udC1zaXplLW1lZGl1bTogMTNweDtcbiRmb250LXNpemUtbGFyZ2U6IDE1cHg7XG4kZm9udC1zaXplLXgtbGFyZ2U6IDIwcHg7XG4kZm9udC1zaXplLTJ4LWxhcmdlOiAzMnB4O1xuXG4vLyBMaW5lIGhlaWdodHNcbiRmb250LWxpbmUtaGVpZ2h0LXgtc21hbGw6IDE2cHg7XG4kZm9udC1saW5lLWhlaWdodC1zbWFsbDogMjBweDtcbiRmb250LWxpbmUtaGVpZ2h0LW1lZGl1bTogMjRweDtcbiRmb250LWxpbmUtaGVpZ2h0LWxhcmdlOiAyOHB4O1xuJGZvbnQtbGluZS1oZWlnaHQteC1sYXJnZTogMzJweDtcbiRmb250LWxpbmUtaGVpZ2h0LTJ4LWxhcmdlOiA0MHB4O1xuXG4vLyBXZWlnaHRzXG4kZm9udC13ZWlnaHQtcmVndWxhcjogNDAwO1xuJGZvbnQtd2VpZ2h0LW1lZGl1bTogNDk5OyAvLyBlbnN1cmVzIGZhbGxiYWNrIHRvIDQwMCAoaW5zdGVhZCBvZiA2MDApXG5cbi8vIEZhbWlsaWVzXG4kZm9udC1mYW1pbHktaGVhZGluZ3M6IC1hcHBsZS1zeXN0ZW0sIFwic3lzdGVtLXVpXCIsIFwiU2Vnb2UgVUlcIiwgUm9ib3RvLCBPeHlnZW4tU2FucywgVWJ1bnR1LCBDYW50YXJlbGwsIFwiSGVsdmV0aWNhIE5ldWVcIiwgc2Fucy1zZXJpZjtcbiRmb250LWZhbWlseS1ib2R5OiAtYXBwbGUtc3lzdGVtLCBcInN5c3RlbS11aVwiLCBcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLCBcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7XG4kZm9udC1mYW1pbHktbW9ubzogTWVubG8sIENvbnNvbGFzLCBtb25hY28sIG1vbm9zcGFjZTtcblxuLyoqXG4gKiBHcmlkIFN5c3RlbS5cbiAqIGh0dHBzOi8vbWFrZS53b3JkcHJlc3Mub3JnL2Rlc2lnbi8yMDE5LzEwLzMxL3Byb3Bvc2FsLWEtY29uc2lzdGVudC1zcGFjaW5nLXN5c3RlbS1mb3Itd29yZHByZXNzL1xuICovXG5cbiRncmlkLXVuaXQ6IDhweDtcbiRncmlkLXVuaXQtMDU6IDAuNSAqICRncmlkLXVuaXQ7XHQvLyA0cHhcbiRncmlkLXVuaXQtMTA6IDEgKiAkZ3JpZC11bml0O1x0XHQvLyA4cHhcbiRncmlkLXVuaXQtMTU6IDEuNSAqICRncmlkLXVuaXQ7XHQvLyAxMnB4XG4kZ3JpZC11bml0LTIwOiAyICogJGdyaWQtdW5pdDtcdFx0Ly8gMTZweFxuJGdyaWQtdW5pdC0zMDogMyAqICRncmlkLXVuaXQ7XHRcdC8vIDI0cHhcbiRncmlkLXVuaXQtNDA6IDQgKiAkZ3JpZC11bml0O1x0XHQvLyAzMnB4XG4kZ3JpZC11bml0LTUwOiA1ICogJGdyaWQtdW5pdDtcdFx0Ly8gNDBweFxuJGdyaWQtdW5pdC02MDogNiAqICRncmlkLXVuaXQ7XHRcdC8vIDQ4cHhcbiRncmlkLXVuaXQtNzA6IDcgKiAkZ3JpZC11bml0O1x0XHQvLyA1NnB4XG4kZ3JpZC11bml0LTgwOiA4ICogJGdyaWQtdW5pdDtcdFx0Ly8gNjRweFxuXG4vKipcbiAqIFJhZGl1cyBzY2FsZS5cbiAqL1xuXG4kcmFkaXVzLXgtc21hbGw6IDFweDsgICAvLyBBcHBsaWVkIHRvIGVsZW1lbnRzIGxpa2UgYnV0dG9ucyBuZXN0ZWQgd2l0aGluIHByaW1pdGl2ZXMgbGlrZSBpbnB1dHMuXG4kcmFkaXVzLXNtYWxsOiAycHg7ICAgICAvLyBBcHBsaWVkIHRvIG1vc3QgcHJpbWl0aXZlcy5cbiRyYWRpdXMtbWVkaXVtOiA0cHg7ICAgIC8vIEFwcGxpZWQgdG8gY29udGFpbmVycyB3aXRoIHNtYWxsZXIgcGFkZGluZy5cbiRyYWRpdXMtbGFyZ2U6IDhweDsgICAgIC8vIEFwcGxpZWQgdG8gY29udGFpbmVycyB3aXRoIGxhcmdlciBwYWRkaW5nLlxuJHJhZGl1cy1mdWxsOiA5OTk5cHg7ICAgLy8gRm9yIHBpbGxzLlxuJHJhZGl1cy1yb3VuZDogNTAlOyAgICAgLy8gRm9yIGNpcmNsZXMgYW5kIG92YWxzLlxuXG4vKipcbiAqIEVsZXZhdGlvbiBzY2FsZS5cbiAqL1xuXG4vLyBGb3Igc2VjdGlvbnMgYW5kIGNvbnRhaW5lcnMgdGhhdCBncm91cCByZWxhdGVkIGNvbnRlbnQgYW5kIGNvbnRyb2xzLCB3aGljaCBtYXkgb3ZlcmxhcCBvdGhlciBjb250ZW50LiBFeGFtcGxlOiBQcmV2aWV3IEZyYW1lLlxuJGVsZXZhdGlvbi14LXNtYWxsOiAwIDFweCAxcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAzKSwgMCAxcHggMnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMiksIDAgM3B4IDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpLCAwIDRweCA0cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAxKTtcblxuLy8gRm9yIGNvbXBvbmVudHMgdGhhdCBwcm92aWRlIGNvbnRleHR1YWwgZmVlZGJhY2sgd2l0aG91dCBiZWluZyBpbnRydXNpdmUuIEdlbmVyYWxseSBub24taW50ZXJydXB0aXZlLiBFeGFtcGxlOiBUb29sdGlwcywgU25hY2tiYXIuXG4kZWxldmF0aW9uLXNtYWxsOiAwIDFweCAycHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA1KSwgMCAycHggM3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNCksIDAgNnB4IDZweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDMpLCAwIDhweCA4cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKTtcblxuLy8gRm9yIGNvbXBvbmVudHMgdGhhdCBvZmZlciBhZGRpdGlvbmFsIGFjdGlvbnMuIEV4YW1wbGU6IE1lbnVzLCBDb21tYW5kIFBhbGV0dGVcbiRlbGV2YXRpb24tbWVkaXVtOiAwIDJweCAzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjA1KSwgMCA0cHggNXB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNCksIDAgMTJweCAxMnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMyksIDAgMTZweCAxNnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMik7XG5cbi8vIEZvciBjb21wb25lbnRzIHRoYXQgY29uZmlybSBkZWNpc2lvbnMgb3IgaGFuZGxlIG5lY2Vzc2FyeSBpbnRlcnJ1cHRpb25zLiBFeGFtcGxlOiBNb2RhbHMuXG4kZWxldmF0aW9uLWxhcmdlOiAwIDVweCAxNXB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wOCksIDAgMTVweCAyN3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNyksIDAgMzBweCAzNnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNCksIDAgNTBweCA0M3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMik7XG5cbi8qKlxuICogRGltZW5zaW9ucy5cbiAqL1xuXG4kaWNvbi1zaXplOiAyNHB4O1xuJGJ1dHRvbi1zaXplOiAzNnB4O1xuJGJ1dHRvbi1zaXplLW5leHQtZGVmYXVsdC00MHB4OiA0MHB4OyAvLyB0cmFuc2l0aW9uYXJ5IHZhcmlhYmxlIGZvciBuZXh0IGRlZmF1bHQgYnV0dG9uIHNpemVcbiRidXR0b24tc2l6ZS1zbWFsbDogMjRweDtcbiRidXR0b24tc2l6ZS1jb21wYWN0OiAzMnB4O1xuJGhlYWRlci1oZWlnaHQ6IDY0cHg7XG4kcGFuZWwtaGVhZGVyLWhlaWdodDogJGdyaWQtdW5pdC02MDtcbiRuYXYtc2lkZWJhci13aWR0aDogMzAwcHg7XG4kYWRtaW4tYmFyLWhlaWdodDogMzJweDtcbiRhZG1pbi1iYXItaGVpZ2h0LWJpZzogNDZweDtcbiRhZG1pbi1zaWRlYmFyLXdpZHRoOiAxNjBweDtcbiRhZG1pbi1zaWRlYmFyLXdpZHRoLWJpZzogMTkwcHg7XG4kYWRtaW4tc2lkZWJhci13aWR0aC1jb2xsYXBzZWQ6IDM2cHg7XG4kbW9kYWwtbWluLXdpZHRoOiAzNTBweDtcbiRtb2RhbC13aWR0aC1zbWFsbDogMzg0cHg7XG4kbW9kYWwtd2lkdGgtbWVkaXVtOiA1MTJweDtcbiRtb2RhbC13aWR0aC1sYXJnZTogODQwcHg7XG4kc3Bpbm5lci1zaXplOiAxNnB4O1xuJGNhbnZhcy1wYWRkaW5nOiAkZ3JpZC11bml0LTIwO1xuJHBhbGV0dGUtbWF4LWhlaWdodDogMzY4cHg7XG5cbi8qKlxuICogTW9iaWxlIHNwZWNpZmljIHN0eWxlc1xuICovXG4kbW9iaWxlLXRleHQtbWluLWZvbnQtc2l6ZTogMTZweDsgLy8gQW55IGZvbnQgc2l6ZSBiZWxvdyAxNnB4IHdpbGwgY2F1c2UgTW9iaWxlIFNhZmFyaSB0byBcInpvb20gaW5cIi5cblxuLyoqXG4gKiBFZGl0b3Igc3R5bGVzLlxuICovXG5cbiRzaWRlYmFyLXdpZHRoOiAyODBweDtcbiRjb250ZW50LXdpZHRoOiA4NDBweDtcbiR3aWRlLWNvbnRlbnQtd2lkdGg6IDExMDBweDtcbiR3aWRnZXQtYXJlYS13aWR0aDogNzAwcHg7XG4kc2Vjb25kYXJ5LXNpZGViYXItd2lkdGg6IDM1MHB4O1xuJGVkaXRvci1mb250LXNpemU6IDE2cHg7XG4kZGVmYXVsdC1ibG9jay1tYXJnaW46IDI4cHg7IC8vIFRoaXMgdmFsdWUgcHJvdmlkZXMgYSBjb25zaXN0ZW50LCBjb250aWd1b3VzIHNwYWNpbmcgYmV0d2VlbiBibG9ja3MuXG4kdGV4dC1lZGl0b3ItZm9udC1zaXplOiAxNXB4O1xuJGVkaXRvci1saW5lLWhlaWdodDogMS44O1xuJGVkaXRvci1odG1sLWZvbnQ6ICRmb250LWZhbWlseS1tb25vO1xuXG4vKipcbiAqIEJsb2NrICYgRWRpdG9yIFVJLlxuICovXG5cbiRibG9jay10b29sYmFyLWhlaWdodDogJGdyaWQtdW5pdC02MDtcbiRib3JkZXItd2lkdGg6IDFweDtcbiRib3JkZXItd2lkdGgtZm9jdXMtZmFsbGJhY2s6IDJweDsgLy8gVGhpcyBleGlzdHMgYXMgYSBmYWxsYmFjaywgYW5kIGlzIGlkZWFsbHkgb3ZlcnJpZGRlbiBieSB2YXIoLS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXMpIHVubGVzcyBpbiBzb21lIFNBU1MgbWF0aCBjYXNlcy5cbiRib3JkZXItd2lkdGgtdGFiOiAxLjVweDtcbiRoZWxwdGV4dC1mb250LXNpemU6IDEycHg7XG4kcmFkaW8taW5wdXQtc2l6ZTogMTZweDtcbiRyYWRpby1pbnB1dC1zaXplLXNtOiAyNHB4OyAvLyBXaWR0aCAmIGhlaWdodCBmb3Igc21hbGwgdmlld3BvcnRzLlxuXG4vLyBEZXByZWNhdGVkLCBwbGVhc2UgYXZvaWQgdXNpbmcgdGhlc2UuXG4kYmxvY2stcGFkZGluZzogMTRweDsgLy8gVXNlZCB0byBkZWZpbmUgc3BhY2UgYmV0d2VlbiBibG9jayBmb290cHJpbnQgYW5kIHN1cnJvdW5kaW5nIGJvcmRlcnMuXG4kcmFkaXVzLWJsb2NrLXVpOiAkcmFkaXVzLXNtYWxsO1xuJHNoYWRvdy1wb3BvdmVyOiAkZWxldmF0aW9uLXgtc21hbGw7XG4kc2hhZG93LW1vZGFsOiAkZWxldmF0aW9uLWxhcmdlO1xuJGRlZmF1bHQtZm9udC1zaXplOiAkZm9udC1zaXplLW1lZGl1bTtcblxuLyoqXG4gKiBCbG9jayBwYWRkaW5ncy5cbiAqL1xuXG4vLyBQYWRkaW5nIGZvciBibG9ja3Mgd2l0aCBhIGJhY2tncm91bmQgY29sb3IgKGUuZy4gcGFyYWdyYXBoIG9yIGdyb3VwKS5cbiRibG9jay1iZy1wYWRkaW5nLS12OiAxLjI1ZW07XG4kYmxvY2stYmctcGFkZGluZy0taDogMi4zNzVlbTtcblxuXG4vKipcbiAqIFJlYWN0IE5hdGl2ZSBzcGVjaWZpYy5cbiAqIFRoZXNlIHZhcmlhYmxlcyBkbyBub3QgYXBwZWFyIHRvIGJlIHVzZWQgYW55d2hlcmUgZWxzZS5cbiAqL1xuXG4vLyBEaW1lbnNpb25zLlxuJG1vYmlsZS1oZWFkZXItdG9vbGJhci1oZWlnaHQ6IDQ0cHg7XG4kbW9iaWxlLWhlYWRlci10b29sYmFyLWV4cGFuZGVkLWhlaWdodDogNTJweDtcbiRtb2JpbGUtZmxvYXRpbmctdG9vbGJhci1oZWlnaHQ6IDQ0cHg7XG4kbW9iaWxlLWZsb2F0aW5nLXRvb2xiYXItbWFyZ2luOiA4cHg7XG4kbW9iaWxlLWNvbG9yLXN3YXRjaDogNDhweDtcblxuLy8gQmxvY2sgVUkuXG4kbW9iaWxlLWJsb2NrLXRvb2xiYXItaGVpZ2h0OiA0NHB4O1xuJGRpbW1lZC1vcGFjaXR5OiAxO1xuJGJsb2NrLWVkZ2UtdG8tY29udGVudDogMTZweDtcbiRzb2xpZC1ib3JkZXItc3BhY2U6IDEycHg7XG4kZGFzaGVkLWJvcmRlci1zcGFjZTogNnB4O1xuJGJsb2NrLXNlbGVjdGVkLW1hcmdpbjogM3B4O1xuJGJsb2NrLXNlbGVjdGVkLWJvcmRlci13aWR0aDogMXB4O1xuJGJsb2NrLXNlbGVjdGVkLXBhZGRpbmc6IDA7XG4kYmxvY2stc2VsZWN0ZWQtY2hpbGQtbWFyZ2luOiA1cHg7XG4kYmxvY2stc2VsZWN0ZWQtdG8tY29udGVudDogJGJsb2NrLWVkZ2UtdG8tY29udGVudCAtICRibG9jay1zZWxlY3RlZC1tYXJnaW4gLSAkYmxvY2stc2VsZWN0ZWQtYm9yZGVyLXdpZHRoO1xuIiwiLyoqXG4gKiBDb2xvcnNcbiAqL1xuXG4vLyBXb3JkUHJlc3MgZ3JheXMuXG4kYmxhY2s6ICMwMDA7XHRcdFx0Ly8gVXNlIG9ubHkgd2hlbiB5b3UgdHJ1bHkgbmVlZCBwdXJlIGJsYWNrLiBGb3IgVUksIHVzZSAkZ3JheS05MDAuXG4kZ3JheS05MDA6ICMxZTFlMWU7XG4kZ3JheS04MDA6ICMyZjJmMmY7XG4kZ3JheS03MDA6ICM3NTc1NzU7XHRcdC8vIE1lZXRzIDQuNjoxICg0LjU6MSBpcyBtaW5pbXVtKSB0ZXh0IGNvbnRyYXN0IGFnYWluc3Qgd2hpdGUuXG4kZ3JheS02MDA6ICM5NDk0OTQ7XHRcdC8vIE1lZXRzIDM6MSBVSSBvciBsYXJnZSB0ZXh0IGNvbnRyYXN0IGFnYWluc3Qgd2hpdGUuXG4kZ3JheS00MDA6ICNjY2M7XG4kZ3JheS0zMDA6ICNkZGQ7XHRcdC8vIFVzZWQgZm9yIG1vc3QgYm9yZGVycy5cbiRncmF5LTIwMDogI2UwZTBlMDtcdFx0Ly8gVXNlZCBzcGFyaW5nbHkgZm9yIGxpZ2h0IGJvcmRlcnMuXG4kZ3JheS0xMDA6ICNmMGYwZjA7XHRcdC8vIFVzZWQgZm9yIGxpZ2h0IGdyYXkgYmFja2dyb3VuZHMuXG4kd2hpdGU6ICNmZmY7XG5cbi8vIE9wYWNpdGllcyAmIGFkZGl0aW9uYWwgY29sb3JzLlxuJGRhcmstZ3JheS1wbGFjZWhvbGRlcjogcmdiYSgkZ3JheS05MDAsIDAuNjIpO1xuJG1lZGl1bS1ncmF5LXBsYWNlaG9sZGVyOiByZ2JhKCRncmF5LTkwMCwgMC41NSk7XG4kbGlnaHQtZ3JheS1wbGFjZWhvbGRlcjogcmdiYSgkd2hpdGUsIDAuNjUpO1xuXG4vLyBBbGVydCBjb2xvcnMuXG4kYWxlcnQteWVsbG93OiAjZjBiODQ5O1xuJGFsZXJ0LXJlZDogI2NjMTgxODtcbiRhbGVydC1ncmVlbjogIzRhYjg2NjtcblxuLy8gRGVwcmVjYXRlZCwgcGxlYXNlIGF2b2lkIHVzaW5nIHRoZXNlLlxuJGRhcmstdGhlbWUtZm9jdXM6ICR3aGl0ZTtcdC8vIEZvY3VzIGNvbG9yIHdoZW4gdGhlIHRoZW1lIGlzIGRhcmsuXG4iLCIvKipcbiAqIFR5cG9ncmFwaHlcbiAqL1xuXG5AdXNlIFwic2Fzczpjb2xvclwiO1xuQHVzZSBcInNhc3M6bWF0aFwiO1xuQHVzZSBcIi4vdmFyaWFibGVzXCI7XG5AdXNlIFwiLi9jb2xvcnNcIjtcbkB1c2UgXCIuL2JyZWFrcG9pbnRzXCI7XG5AdXNlIFwiLi9mdW5jdGlvbnNcIjtcbkB1c2UgXCIuL2xvbmctY29udGVudC1mYWRlXCI7XG5cbkBtaXhpbiBfdGV4dC1oZWFkaW5nKCkge1xuXHRmb250LWZhbWlseTogdmFyaWFibGVzLiRmb250LWZhbWlseS1oZWFkaW5ncztcblx0Zm9udC13ZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC13ZWlnaHQtbWVkaXVtO1xufVxuXG5AbWl4aW4gX3RleHQtYm9keSgpIHtcblx0Zm9udC1mYW1pbHk6IHZhcmlhYmxlcy4kZm9udC1mYW1pbHktYm9keTtcblx0Zm9udC13ZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC13ZWlnaHQtcmVndWxhcjtcbn1cblxuQG1peGluIGhlYWRpbmctc21hbGwoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWhlYWRpbmcoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS14LXNtYWxsO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LXgtc21hbGw7XG59XG5cbkBtaXhpbiBoZWFkaW5nLW1lZGl1bSgpIHtcblx0QGluY2x1ZGUgX3RleHQtaGVhZGluZygpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLW1lZGl1bTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC1zbWFsbDtcbn1cblxuQG1peGluIGhlYWRpbmctbGFyZ2UoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWhlYWRpbmcoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS1sYXJnZTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC1zbWFsbDtcbn1cblxuQG1peGluIGhlYWRpbmcteC1sYXJnZSgpIHtcblx0QGluY2x1ZGUgX3RleHQtaGVhZGluZygpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLXgtbGFyZ2U7XG5cdGxpbmUtaGVpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtbGluZS1oZWlnaHQtbWVkaXVtO1xufVxuXG5AbWl4aW4gaGVhZGluZy0yeC1sYXJnZSgpIHtcblx0QGluY2x1ZGUgX3RleHQtaGVhZGluZygpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLTJ4LWxhcmdlO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LTJ4LWxhcmdlO1xufVxuXG5AbWl4aW4gYm9keS1zbWFsbCgpIHtcblx0QGluY2x1ZGUgX3RleHQtYm9keSgpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLXNtYWxsO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LXgtc21hbGw7XG59XG5cbkBtaXhpbiBib2R5LW1lZGl1bSgpIHtcblx0QGluY2x1ZGUgX3RleHQtYm9keSgpO1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kZm9udC1zaXplLW1lZGl1bTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC1zbWFsbDtcbn1cblxuQG1peGluIGJvZHktbGFyZ2UoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWJvZHkoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS1sYXJnZTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC1tZWRpdW07XG59XG5cbkBtaXhpbiBib2R5LXgtbGFyZ2UoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWJvZHkoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS14LWxhcmdlO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LXgtbGFyZ2U7XG59XG5cbi8qKlxuICogQnJlYWtwb2ludCBtaXhpbnNcbiAqL1xuXG5AbWl4aW4gYnJlYWsteGh1Z2UoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLXhodWdlKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLWh1Z2UoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLWh1Z2UpIH0pIHtcblx0XHRAY29udGVudDtcblx0fVxufVxuXG5AbWl4aW4gYnJlYWstd2lkZSgpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstd2lkZSkgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbkBtaXhpbiBicmVhay14bGFyZ2UoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLXhsYXJnZSkgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbkBtaXhpbiBicmVhay1sYXJnZSgpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbGFyZ2UpIH0pIHtcblx0XHRAY29udGVudDtcblx0fVxufVxuXG5AbWl4aW4gYnJlYWstbWVkaXVtKCkge1xuXHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay1tZWRpdW0pIH0pIHtcblx0XHRAY29udGVudDtcblx0fVxufVxuXG5AbWl4aW4gYnJlYWstc21hbGwoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLXNtYWxsKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLW1vYmlsZSgpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbW9iaWxlKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLXpvb21lZC1pbigpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstem9vbWVkLWluKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuLyoqXG4gKiBGb2N1cyBzdHlsZXMuXG4gKi9cblxuQG1peGluIGJsb2NrLXRvb2xiYXItYnV0dG9uLXN0eWxlX19mb2N1cygpIHtcblx0Ym94LXNoYWRvdzogaW5zZXQgMCAwIDAgdmFyaWFibGVzLiRib3JkZXItd2lkdGggY29sb3JzLiR3aGl0ZSwgMCAwIDAgdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcik7XG5cblx0Ly8gV2luZG93cyBIaWdoIENvbnRyYXN0IG1vZGUgd2lsbCBzaG93IHRoaXMgb3V0bGluZSwgYnV0IG5vdCB0aGUgYm94LXNoYWRvdy5cblx0b3V0bGluZTogMnB4IHNvbGlkIHRyYW5zcGFyZW50O1xufVxuXG4vLyBUYWJzLCBJbnB1dHMsIFNxdWFyZSBidXR0b25zLlxuQG1peGluIGlucHV0LXN0eWxlX19uZXV0cmFsKCkge1xuXHRib3gtc2hhZG93OiAwIDAgMCB0cmFuc3BhcmVudDtcblx0Ym9yZGVyLXJhZGl1czogdmFyaWFibGVzLiRyYWRpdXMtc21hbGw7XG5cdGJvcmRlcjogdmFyaWFibGVzLiRib3JkZXItd2lkdGggc29saWQgY29sb3JzLiRncmF5LTYwMDtcblxuXHRAbWVkaWEgbm90IChwcmVmZXJzLXJlZHVjZWQtbW90aW9uKSB7XG5cdFx0dHJhbnNpdGlvbjogYm94LXNoYWRvdyAwLjFzIGxpbmVhcjtcblx0fVxufVxuXG5cbkBtaXhpbiBpbnB1dC1zdHlsZV9fZm9jdXMoJGFjY2VudC1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpKSB7XG5cdGJvcmRlci1jb2xvcjogJGFjY2VudC1jb2xvcjtcblx0Ly8gRXhwYW5kIHRoZSBkZWZhdWx0IGJvcmRlciBmb2N1cyBzdHlsZSBieSAuNXB4IHRvIGJlIGEgdG90YWwgb2YgMS41cHguXG5cdGJveC1zaGFkb3c6IDAgMCAwIDAuNXB4ICRhY2NlbnQtY29sb3I7XG5cdC8vIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlIHdpbGwgc2hvdyB0aGlzIG91dGxpbmUsIGJ1dCBub3QgdGhlIGJveC1zaGFkb3cuXG5cdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcbn1cblxuQG1peGluIGJ1dHRvbi1zdHlsZV9fZm9jdXMoKSB7XG5cdGJveC1zaGFkb3c6IDAgMCAwIHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXG5cdC8vIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlIHdpbGwgc2hvdyB0aGlzIG91dGxpbmUsIGJ1dCBub3QgdGhlIGJveC1zaGFkb3cuXG5cdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcbn1cblxuXG5AbWl4aW4gYnV0dG9uLXN0eWxlLW91dHNldF9fZm9jdXMoJGZvY3VzLWNvbG9yKSB7XG5cdGJveC1zaGFkb3c6IDAgMCAwIHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgY29sb3JzLiR3aGl0ZSwgMCAwIDAgY2FsYygyICogdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSkgJGZvY3VzLWNvbG9yO1xuXG5cdC8vIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlIHdpbGwgc2hvdyB0aGlzIG91dGxpbmUsIGJ1dCBub3QgdGhlIGJveC1zaGFkb3cuXG5cdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcblx0b3V0bGluZS1vZmZzZXQ6IDJweDtcbn1cblxuXG4vKipcbiAqIEFwcGxpZXMgZWRpdG9yIGxlZnQgcG9zaXRpb24gdG8gdGhlIHNlbGVjdG9yIHBhc3NlZCBhcyBhcmd1bWVudFxuICovXG5cbkBtaXhpbiBlZGl0b3ItbGVmdCgkc2VsZWN0b3IpIHtcblx0I3skc2VsZWN0b3J9IHsgLyogU2V0IGxlZnQgcG9zaXRpb24gd2hlbiBhdXRvLWZvbGQgaXMgbm90IG9uIHRoZSBib2R5IGVsZW1lbnQuICovXG5cdFx0bGVmdDogMDtcblxuXHRcdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLW1lZGl1bSArIDEpIH0pIHtcblx0XHRcdGxlZnQ6IHZhcmlhYmxlcy4kYWRtaW4tc2lkZWJhci13aWR0aDtcblx0XHR9XG5cdH1cblxuXHQuYXV0by1mb2xkICN7JHNlbGVjdG9yfSB7IC8qIEF1dG8gZm9sZCBpcyB3aGVuIG9uIHNtYWxsZXIgYnJlYWtwb2ludHMsIG5hdiBtZW51IGF1dG8gY29sbGFwc2VzLiAqL1xuXHRcdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLW1lZGl1bSArIDEpIH0pIHtcblx0XHRcdGxlZnQ6IHZhcmlhYmxlcy4kYWRtaW4tc2lkZWJhci13aWR0aC1jb2xsYXBzZWQ7XG5cdFx0fVxuXG5cdFx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbGFyZ2UgKyAxKSB9KSB7XG5cdFx0XHRsZWZ0OiB2YXJpYWJsZXMuJGFkbWluLXNpZGViYXItd2lkdGg7XG5cdFx0fVxuXHR9XG5cblx0LyogU2lkZWJhciBtYW51YWxseSBjb2xsYXBzZWQuICovXG5cdC5mb2xkZWQgI3skc2VsZWN0b3J9IHtcblx0XHRsZWZ0OiAwO1xuXG5cdFx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbWVkaXVtICsgMSkgfSkge1xuXHRcdFx0bGVmdDogdmFyaWFibGVzLiRhZG1pbi1zaWRlYmFyLXdpZHRoLWNvbGxhcHNlZDtcblx0XHR9XG5cdH1cblxuXHRib2R5LmlzLWZ1bGxzY3JlZW4tbW9kZSAjeyRzZWxlY3Rvcn0ge1xuXHRcdGxlZnQ6IDAgIWltcG9ydGFudDtcblx0fVxufVxuXG4vKipcbiAqIFN0eWxlcyB0aGF0IGFyZSByZXVzZWQgdmVyYmF0aW0gaW4gYSBmZXcgcGxhY2VzXG4gKi9cblxuLy8gVGhlc2UgYXJlIGFkZGl0aW9uYWwgc3R5bGVzIGZvciBhbGwgY2FwdGlvbnMsIHdoZW4gdGhlIHRoZW1lIG9wdHMgaW4gdG8gYmxvY2sgc3R5bGVzLlxuQG1peGluIGNhcHRpb24tc3R5bGUoKSB7XG5cdG1hcmdpbi10b3A6IDAuNWVtO1xuXHRtYXJnaW4tYm90dG9tOiAxZW07XG59XG5cbkBtaXhpbiBjYXB0aW9uLXN0eWxlLXRoZW1lKCkge1xuXHRjb2xvcjogIzU1NTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGRlZmF1bHQtZm9udC1zaXplO1xuXHR0ZXh0LWFsaWduOiBjZW50ZXI7XG5cblx0LmlzLWRhcmstdGhlbWUgJiB7XG5cdFx0Y29sb3I6IGNvbG9ycy4kbGlnaHQtZ3JheS1wbGFjZWhvbGRlcjtcblx0fVxufVxuXG4vKipcbiAqIEFsbG93cyB1c2VycyB0byBvcHQtb3V0IG9mIGFuaW1hdGlvbnMgdmlhIE9TLWxldmVsIHByZWZlcmVuY2VzLlxuICovXG5cbkBtaXhpbiByZWR1Y2UtbW90aW9uKCRwcm9wZXJ0eTogXCJcIikge1xuXG5cdEBpZiAkcHJvcGVydHkgPT0gXCJ0cmFuc2l0aW9uXCIge1xuXHRcdEBtZWRpYSAocHJlZmVycy1yZWR1Y2VkLW1vdGlvbjogcmVkdWNlKSB7XG5cdFx0XHR0cmFuc2l0aW9uLWR1cmF0aW9uOiAwcztcblx0XHRcdHRyYW5zaXRpb24tZGVsYXk6IDBzO1xuXHRcdH1cblx0fSBAZWxzZSBpZiAkcHJvcGVydHkgPT0gXCJhbmltYXRpb25cIiB7XG5cdFx0QG1lZGlhIChwcmVmZXJzLXJlZHVjZWQtbW90aW9uOiByZWR1Y2UpIHtcblx0XHRcdGFuaW1hdGlvbi1kdXJhdGlvbjogMW1zO1xuXHRcdFx0YW5pbWF0aW9uLWRlbGF5OiAwcztcblx0XHR9XG5cdH0gQGVsc2Uge1xuXHRcdEBtZWRpYSAocHJlZmVycy1yZWR1Y2VkLW1vdGlvbjogcmVkdWNlKSB7XG5cdFx0XHR0cmFuc2l0aW9uLWR1cmF0aW9uOiAwcztcblx0XHRcdHRyYW5zaXRpb24tZGVsYXk6IDBzO1xuXHRcdFx0YW5pbWF0aW9uLWR1cmF0aW9uOiAxbXM7XG5cdFx0XHRhbmltYXRpb24tZGVsYXk6IDBzO1xuXHRcdH1cblx0fVxufVxuXG5AbWl4aW4gaW5wdXQtY29udHJvbCgkYWNjZW50LWNvbG9yOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcikpIHtcblx0Zm9udC1mYW1pbHk6IHZhcmlhYmxlcy4kZGVmYXVsdC1mb250O1xuXHRwYWRkaW5nOiA2cHggOHB4O1xuXHQvKiBGb250cyBzbWFsbGVyIHRoYW4gMTZweCBjYXVzZXMgbW9iaWxlIHNhZmFyaSB0byB6b29tLiAqL1xuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kbW9iaWxlLXRleHQtbWluLWZvbnQtc2l6ZTtcblx0LyogT3ZlcnJpZGUgY29yZSBsaW5lLWhlaWdodC4gVG8gYmUgcmV2aWV3ZWQuICovXG5cdGxpbmUtaGVpZ2h0OiBub3JtYWw7XG5cdEBpbmNsdWRlIGlucHV0LXN0eWxlX19uZXV0cmFsKCk7XG5cblx0QGluY2x1ZGUgYnJlYWstc21hbGwge1xuXHRcdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRkZWZhdWx0LWZvbnQtc2l6ZTtcblx0XHQvKiBPdmVycmlkZSBjb3JlIGxpbmUtaGVpZ2h0LiBUbyBiZSByZXZpZXdlZC4gKi9cblx0XHRsaW5lLWhlaWdodDogbm9ybWFsO1xuXHR9XG5cblx0Jjpmb2N1cyB7XG5cdFx0QGluY2x1ZGUgaW5wdXQtc3R5bGVfX2ZvY3VzKCRhY2NlbnQtY29sb3IpO1xuXHR9XG5cblx0Ly8gVXNlIG9wYWNpdHkgdG8gd29yayBpbiB2YXJpb3VzIGVkaXRvciBzdHlsZXMuXG5cdCY6Oi13ZWJraXQtaW5wdXQtcGxhY2Vob2xkZXIge1xuXHRcdGNvbG9yOiBjb2xvcnMuJGRhcmstZ3JheS1wbGFjZWhvbGRlcjtcblx0fVxuXG5cdCY6Oi1tb3otcGxhY2Vob2xkZXIge1xuXHRcdGNvbG9yOiBjb2xvcnMuJGRhcmstZ3JheS1wbGFjZWhvbGRlcjtcblx0fVxuXG5cdCY6LW1zLWlucHV0LXBsYWNlaG9sZGVyIHtcblx0XHRjb2xvcjogY29sb3JzLiRkYXJrLWdyYXktcGxhY2Vob2xkZXI7XG5cdH1cbn1cblxuQG1peGluIGNoZWNrYm94LWNvbnRyb2wge1xuXHRib3JkZXI6IHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoIHNvbGlkIGNvbG9ycy4kZ3JheS05MDA7XG5cdG1hcmdpbi1yaWdodDogdmFyaWFibGVzLiRncmlkLXVuaXQtMTU7XG5cdHRyYW5zaXRpb246IG5vbmU7XG5cdGJvcmRlci1yYWRpdXM6IHZhcmlhYmxlcy4kcmFkaXVzLXNtYWxsO1xuXHRAaW5jbHVkZSBpbnB1dC1jb250cm9sO1xuXG5cdCY6Zm9jdXMge1xuXHRcdGJveC1zaGFkb3c6IDAgMCAwICh2YXJpYWJsZXMuJGJvcmRlci13aWR0aCAqIDIpIGNvbG9ycy4kd2hpdGUsIDAgMCAwICh2YXJpYWJsZXMuJGJvcmRlci13aWR0aCAqIDIgKyB2YXJpYWJsZXMuJGJvcmRlci13aWR0aC1mb2N1cy1mYWxsYmFjaykgdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXG5cdFx0Ly8gT25seSB2aXNpYmxlIGluIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlLlxuXHRcdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcblx0fVxuXG5cdCY6Y2hlY2tlZCB7XG5cdFx0YmFja2dyb3VuZDogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXHRcdGJvcmRlci1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXG5cdFx0Ly8gSGlkZSBkZWZhdWx0IGNoZWNrYm94IHN0eWxlcyBpbiBJRS5cblx0XHQmOjotbXMtY2hlY2sge1xuXHRcdFx0b3BhY2l0eTogMDtcblx0XHR9XG5cdH1cblxuXHQmOmNoZWNrZWQ6OmJlZm9yZSxcblx0JlthcmlhLWNoZWNrZWQ9XCJtaXhlZFwiXTo6YmVmb3JlIHtcblx0XHRtYXJnaW46IC0zcHggLTVweDtcblx0XHRjb2xvcjogY29sb3JzLiR3aGl0ZTtcblxuXHRcdEBpbmNsdWRlIGJyZWFrLW1lZGl1bSgpIHtcblx0XHRcdG1hcmdpbjogLTRweCAwIDAgLTVweDtcblx0XHR9XG5cdH1cblxuXHQmW2FyaWEtY2hlY2tlZD1cIm1peGVkXCJdIHtcblx0XHRiYWNrZ3JvdW5kOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcik7XG5cdFx0Ym9yZGVyLWNvbG9yOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcik7XG5cblx0XHQmOjpiZWZvcmUge1xuXHRcdFx0Ly8gSW5oZXJpdGVkIGZyb20gYGZvcm1zLmNzc2AuXG5cdFx0XHQvLyBTZWU6IGh0dHBzOi8vZ2l0aHViLmNvbS9Xb3JkUHJlc3Mvd29yZHByZXNzLWRldmVsb3AvdHJlZS81LjEuMS9zcmMvd3AtYWRtaW4vY3NzL2Zvcm1zLmNzcyNMMTIyLUwxMzJcblx0XHRcdGNvbnRlbnQ6IFwiXFxmNDYwXCI7XG5cdFx0XHRmbG9hdDogbGVmdDtcblx0XHRcdGRpc3BsYXk6IGlubGluZS1ibG9jaztcblx0XHRcdHZlcnRpY2FsLWFsaWduOiBtaWRkbGU7XG5cdFx0XHR3aWR0aDogMTZweDtcblx0XHRcdC8qIHN0eWxlbGludC1kaXNhYmxlLW5leHQtbGluZSBmb250LWZhbWlseS1uby1taXNzaW5nLWdlbmVyaWMtZmFtaWx5LWtleXdvcmQgLS0gZGFzaGljb25zIGRvbid0IG5lZWQgYSBnZW5lcmljIGZhbWlseSBrZXl3b3JkLiAqL1xuXHRcdFx0Zm9udDogbm9ybWFsIDMwcHgvMSBkYXNoaWNvbnM7XG5cdFx0XHRzcGVhazogbm9uZTtcblx0XHRcdC13ZWJraXQtZm9udC1zbW9vdGhpbmc6IGFudGlhbGlhc2VkO1xuXHRcdFx0LW1vei1vc3gtZm9udC1zbW9vdGhpbmc6IGdyYXlzY2FsZTtcblxuXHRcdFx0QGluY2x1ZGUgYnJlYWstbWVkaXVtKCkge1xuXHRcdFx0XHRmbG9hdDogbm9uZTtcblx0XHRcdFx0Zm9udC1zaXplOiAyMXB4O1xuXHRcdFx0fVxuXHRcdH1cblx0fVxuXG5cdCZbYXJpYS1kaXNhYmxlZD1cInRydWVcIl0sXG5cdCY6ZGlzYWJsZWQge1xuXHRcdGJhY2tncm91bmQ6IGNvbG9ycy4kZ3JheS0xMDA7XG5cdFx0Ym9yZGVyLWNvbG9yOiBjb2xvcnMuJGdyYXktMzAwO1xuXHRcdGN1cnNvcjogZGVmYXVsdDtcblxuXHRcdC8vIE92ZXJyaWRlIHN0eWxlIGluaGVyaXRlZCBmcm9tIHdwLWFkbWluLiBSZXF1aXJlZCB0byBhdm9pZCBkZWdyYWRlZCBhcHBlYXJhbmNlIG9uIGRpZmZlcmVudCBiYWNrZ3JvdW5kcy5cblx0XHRvcGFjaXR5OiAxO1xuXHR9XG59XG5cbkBtaXhpbiByYWRpby1jb250cm9sIHtcblx0Ym9yZGVyOiB2YXJpYWJsZXMuJGJvcmRlci13aWR0aCBzb2xpZCBjb2xvcnMuJGdyYXktOTAwO1xuXHRtYXJnaW4tcmlnaHQ6IHZhcmlhYmxlcy4kZ3JpZC11bml0LTE1O1xuXHR0cmFuc2l0aW9uOiBub25lO1xuXHRib3JkZXItcmFkaXVzOiB2YXJpYWJsZXMuJHJhZGl1cy1yb3VuZDtcblx0d2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZS1zbTtcblx0aGVpZ2h0OiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemUtc207XG5cdG1pbi13aWR0aDogdmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLXNtO1xuXHRtYXgtd2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZS1zbTtcblx0cG9zaXRpb246IHJlbGF0aXZlO1xuXG5cdEBtZWRpYSBub3QgKHByZWZlcnMtcmVkdWNlZC1tb3Rpb24pIHtcblx0XHR0cmFuc2l0aW9uOiBib3gtc2hhZG93IDAuMXMgbGluZWFyO1xuXHR9XG5cblx0QGluY2x1ZGUgYnJlYWstc21hbGwoKSB7XG5cdFx0aGVpZ2h0OiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemU7XG5cdFx0d2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZTtcblx0XHRtaW4td2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZTtcblx0XHRtYXgtd2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZTtcblx0fVxuXG5cdCY6Y2hlY2tlZDo6YmVmb3JlIHtcblx0XHRib3gtc2l6aW5nOiBpbmhlcml0O1xuXHRcdHdpZHRoOiBtYXRoLmRpdih2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemUtc20sIDIpO1xuXHRcdGhlaWdodDogbWF0aC5kaXYodmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLXNtLCAyKTtcblx0XHRwb3NpdGlvbjogYWJzb2x1dGU7XG5cdFx0dG9wOiA1MCU7XG5cdFx0bGVmdDogNTAlO1xuXHRcdHRyYW5zZm9ybTogdHJhbnNsYXRlKC01MCUsIC01MCUpO1xuXHRcdG1hcmdpbjogMDtcblx0XHRiYWNrZ3JvdW5kLWNvbG9yOiBjb2xvcnMuJHdoaXRlO1xuXG5cdFx0Ly8gVGhpcyBib3JkZXIgc2VydmVzIGFzIGEgYmFja2dyb3VuZCBjb2xvciBpbiBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZS5cblx0XHRib3JkZXI6IDRweCBzb2xpZCBjb2xvcnMuJHdoaXRlO1xuXG5cdFx0QGluY2x1ZGUgYnJlYWstc21hbGwoKSB7XG5cdFx0XHR3aWR0aDogbWF0aC5kaXYodmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLCAyKTtcblx0XHRcdGhlaWdodDogbWF0aC5kaXYodmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLCAyKTtcblx0XHR9XG5cdH1cblxuXHQmOmZvY3VzIHtcblx0XHRib3gtc2hhZG93OiAwIDAgMCAodmFyaWFibGVzLiRib3JkZXItd2lkdGggKiAyKSBjb2xvcnMuJHdoaXRlLCAwIDAgMCAodmFyaWFibGVzLiRib3JkZXItd2lkdGggKiAyICsgdmFyaWFibGVzLiRib3JkZXItd2lkdGgtZm9jdXMtZmFsbGJhY2spIHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblxuXHRcdC8vIE9ubHkgdmlzaWJsZSBpbiBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZS5cblx0XHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQ7XG5cdH1cblxuXHQmOmNoZWNrZWQge1xuXHRcdGJhY2tncm91bmQ6IHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblx0XHRib3JkZXI6IG5vbmU7XG5cdH1cbn1cblxuLyoqXG4gKiBSZXNldCBkZWZhdWx0IHN0eWxlcyBmb3IgSmF2YVNjcmlwdCBVSSBiYXNlZCBwYWdlcy5cbiAqIFRoaXMgaXMgYSBXUC1hZG1pbiBhZ25vc3RpYyByZXNldFxuICovXG5cbkBtaXhpbiByZXNldCB7XG5cdGJveC1zaXppbmc6IGJvcmRlci1ib3g7XG5cblx0Kixcblx0Kjo6YmVmb3JlLFxuXHQqOjphZnRlciB7XG5cdFx0Ym94LXNpemluZzogaW5oZXJpdDtcblx0fVxufVxuXG5AbWl4aW4gbGluay1yZXNldCB7XG5cdCY6Zm9jdXMge1xuXHRcdGNvbG9yOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvci0tcmdiKTtcblx0XHRib3gtc2hhZG93OiAwIDAgMCB2YXIoLS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXMpIHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yLCAjMDA3Y2JhKTtcblx0XHRib3JkZXItcmFkaXVzOiB2YXJpYWJsZXMuJHJhZGl1cy1zbWFsbDtcblx0fVxufVxuXG4vLyBUaGUgZWRpdG9yIGlucHV0IHJlc2V0IHdpdGggaW5jcmVhc2VkIHNwZWNpZmljaXR5IHRvIGF2b2lkIHRoZW1lIHN0eWxlcyBibGVlZGluZyBpbi5cbkBtaXhpbiBlZGl0b3ItaW5wdXQtcmVzZXQoKSB7XG5cdGZvbnQtZmFtaWx5OiB2YXJpYWJsZXMuJGVkaXRvci1odG1sLWZvbnQgIWltcG9ydGFudDtcblx0Y29sb3I6IGNvbG9ycy4kZ3JheS05MDAgIWltcG9ydGFudDtcblx0YmFja2dyb3VuZDogY29sb3JzLiR3aGl0ZSAhaW1wb3J0YW50O1xuXHRwYWRkaW5nOiB2YXJpYWJsZXMuJGdyaWQtdW5pdC0xNSAhaW1wb3J0YW50O1xuXHRib3JkZXI6IHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoIHNvbGlkIGNvbG9ycy4kZ3JheS05MDAgIWltcG9ydGFudDtcblx0Ym94LXNoYWRvdzogbm9uZSAhaW1wb3J0YW50O1xuXHRib3JkZXItcmFkaXVzOiB2YXJpYWJsZXMuJHJhZGl1cy1zbWFsbCAhaW1wb3J0YW50O1xuXG5cdC8vIEZvbnRzIHNtYWxsZXIgdGhhbiAxNnB4IGNhdXNlcyBtb2JpbGUgc2FmYXJpIHRvIHpvb20uXG5cdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRtb2JpbGUtdGV4dC1taW4tZm9udC1zaXplICFpbXBvcnRhbnQ7XG5cdEBpbmNsdWRlIGJyZWFrLXNtYWxsIHtcblx0XHRmb250LXNpemU6IHZhcmlhYmxlcy4kZGVmYXVsdC1mb250LXNpemUgIWltcG9ydGFudDtcblx0fVxuXG5cdCY6Zm9jdXMge1xuXHRcdGJvcmRlci1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpICFpbXBvcnRhbnQ7XG5cdFx0Ym94LXNoYWRvdzogMCAwIDAgKHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoLWZvY3VzLWZhbGxiYWNrIC0gdmFyaWFibGVzLiRib3JkZXItd2lkdGgpIHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKSAhaW1wb3J0YW50O1xuXG5cdFx0Ly8gV2luZG93cyBIaWdoIENvbnRyYXN0IG1vZGUgd2lsbCBzaG93IHRoaXMgb3V0bGluZSwgYnV0IG5vdCB0aGUgYm94LXNoYWRvdy5cblx0XHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQgIWltcG9ydGFudDtcblx0fVxufVxuXG4vKipcbiAqIFJlc2V0IHRoZSBXUCBBZG1pbiBwYWdlIHN0eWxlcyBmb3IgR3V0ZW5iZXJnLWxpa2UgcGFnZXMuXG4gKi9cblxuQG1peGluIHdwLWFkbWluLXJlc2V0KCAkY29udGVudC1jb250YWluZXIgKSB7XG5cdGJhY2tncm91bmQ6IGNvbG9ycy4kd2hpdGU7XG5cblx0I3dwY29udGVudCB7XG5cdFx0cGFkZGluZy1sZWZ0OiAwO1xuXHR9XG5cblx0I3dwYm9keS1jb250ZW50IHtcblx0XHRwYWRkaW5nLWJvdHRvbTogMDtcblx0fVxuXG5cdC8qIFdlIGhpZGUgbGVnYWN5IG5vdGljZXMgaW4gR3V0ZW5iZXJnIEJhc2VkIFBhZ2VzLCBiZWNhdXNlIHRoZXkgd2VyZSBub3QgZGVzaWduZWQgaW4gYSB3YXkgdGhhdCBzY2FsZWQgd2VsbC5cblx0ICAgUGx1Z2lucyBjYW4gdXNlIEd1dGVuYmVyZyBub3RpY2VzIGlmIHRoZXkgbmVlZCB0byBwYXNzIG9uIGluZm9ybWF0aW9uIHRvIHRoZSB1c2VyIHdoZW4gdGhleSBhcmUgZWRpdGluZy4gKi9cblx0I3dwYm9keS1jb250ZW50ID4gZGl2Om5vdCgjeyAkY29udGVudC1jb250YWluZXIgfSk6bm90KCNzY3JlZW4tbWV0YSkge1xuXHRcdGRpc3BsYXk6IG5vbmU7XG5cdH1cblxuXHQjd3Bmb290ZXIge1xuXHRcdGRpc3BsYXk6IG5vbmU7XG5cdH1cblxuXHQuYTExeS1zcGVhay1yZWdpb24ge1xuXHRcdGxlZnQ6IC0xcHg7XG5cdFx0dG9wOiAtMXB4O1xuXHR9XG5cblx0dWwjYWRtaW5tZW51IGEud3AtaGFzLWN1cnJlbnQtc3VibWVudTo6YWZ0ZXIsXG5cdHVsI2FkbWlubWVudSA+IGxpLmN1cnJlbnQgPiBhLmN1cnJlbnQ6OmFmdGVyIHtcblx0XHRib3JkZXItcmlnaHQtY29sb3I6IGNvbG9ycy4kd2hpdGU7XG5cdH1cblxuXHQubWVkaWEtZnJhbWUgc2VsZWN0LmF0dGFjaG1lbnQtZmlsdGVyczpsYXN0LW9mLXR5cGUge1xuXHRcdHdpZHRoOiBhdXRvO1xuXHRcdG1heC13aWR0aDogMTAwJTtcblx0fVxufVxuXG5AbWl4aW4gYWRtaW4tc2NoZW1lKCRjb2xvci1wcmltYXJ5KSB7XG5cdC8vIERlZmluZSBSR0IgZXF1aXZhbGVudHMgZm9yIHVzZSBpbiByZ2JhIGZ1bmN0aW9uLlxuXHQvLyBIZXhhZGVjaW1hbCBjc3MgdmFycyBkbyBub3Qgd29yayBpbiB0aGUgcmdiYSBmdW5jdGlvbi5cblx0LS13cC1hZG1pbi10aGVtZS1jb2xvcjogI3skY29sb3ItcHJpbWFyeX07XG5cdC0td3AtYWRtaW4tdGhlbWUtY29sb3ItLXJnYjogI3tmdW5jdGlvbnMuaGV4LXRvLXJnYigkY29sb3ItcHJpbWFyeSl9O1xuXHQvLyBEYXJrZXIgc2hhZGVzLlxuXHQtLXdwLWFkbWluLXRoZW1lLWNvbG9yLWRhcmtlci0xMDogI3tjb2xvci5hZGp1c3QoJGNvbG9yLXByaW1hcnksICRsaWdodG5lc3M6IC01JSl9O1xuXHQtLXdwLWFkbWluLXRoZW1lLWNvbG9yLWRhcmtlci0xMC0tcmdiOiAje2Z1bmN0aW9ucy5oZXgtdG8tcmdiKGNvbG9yLmFkanVzdCgkY29sb3ItcHJpbWFyeSwgJGxpZ2h0bmVzczogLTUlKSl9O1xuXHQtLXdwLWFkbWluLXRoZW1lLWNvbG9yLWRhcmtlci0yMDogI3tjb2xvci5hZGp1c3QoJGNvbG9yLXByaW1hcnksICRsaWdodG5lc3M6IC0xMCUpfTtcblx0LS13cC1hZG1pbi10aGVtZS1jb2xvci1kYXJrZXItMjAtLXJnYjogI3tmdW5jdGlvbnMuaGV4LXRvLXJnYihjb2xvci5hZGp1c3QoJGNvbG9yLXByaW1hcnksICRsaWdodG5lc3M6IC0xMCUpKX07XG5cblx0Ly8gRm9jdXMgc3R5bGUgd2lkdGguXG5cdC8vIEF2b2lkIHJvdW5kaW5nIGlzc3VlcyBieSBzaG93aW5nIGEgd2hvbGUgMnB4IGZvciAxeCBzY3JlZW5zLCBhbmQgMS41cHggb24gaGlnaCByZXNvbHV0aW9uIHNjcmVlbnMuXG5cdC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzOiAycHg7XG5cdEBtZWRpYSAoIC13ZWJraXQtbWluLWRldmljZS1waXhlbC1yYXRpbzogMiksIChtaW4tcmVzb2x1dGlvbjogMTkyZHBpKSB7XG5cdFx0LS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXM6IDEuNXB4O1xuXHR9XG59XG5cbkBtaXhpbiB3b3JkcHJlc3MtYWRtaW4tc2NoZW1lcygpIHtcblx0Ym9keS5hZG1pbi1jb2xvci1saWdodCB7XG5cdFx0QGluY2x1ZGUgYWRtaW4tc2NoZW1lKCMwMDg1YmEpO1xuXHR9XG5cblx0Ym9keS5hZG1pbi1jb2xvci1tb2Rlcm4ge1xuXHRcdEBpbmNsdWRlIGFkbWluLXNjaGVtZSgjMzg1OGU5KTtcblx0fVxuXG5cdGJvZHkuYWRtaW4tY29sb3ItYmx1ZSB7XG5cdFx0QGluY2x1ZGUgYWRtaW4tc2NoZW1lKCMwOTY0ODQpO1xuXHR9XG5cblx0Ym9keS5hZG1pbi1jb2xvci1jb2ZmZWUge1xuXHRcdEBpbmNsdWRlIGFkbWluLXNjaGVtZSgjNDY0MDNjKTtcblx0fVxuXG5cdGJvZHkuYWRtaW4tY29sb3ItZWN0b3BsYXNtIHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoIzUyM2Y2ZCk7XG5cdH1cblxuXHRib2R5LmFkbWluLWNvbG9yLW1pZG5pZ2h0IHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoI2UxNGQ0Myk7XG5cdH1cblxuXHRib2R5LmFkbWluLWNvbG9yLW9jZWFuIHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoIzYyN2M4Myk7XG5cdH1cblxuXHRib2R5LmFkbWluLWNvbG9yLXN1bnJpc2Uge1xuXHRcdEBpbmNsdWRlIGFkbWluLXNjaGVtZSgjZGQ4MjNiKTtcblx0fVxufVxuXG4vLyBEZXByZWNhdGVkIGZyb20gVUksIGtlcHQgZm9yIGJhY2stY29tcGF0LlxuQG1peGluIGJhY2tncm91bmQtY29sb3JzLWRlcHJlY2F0ZWQoKSB7XG5cdC5oYXMtdmVyeS1saWdodC1ncmF5LWJhY2tncm91bmQtY29sb3Ige1xuXHRcdGJhY2tncm91bmQtY29sb3I6ICNlZWU7XG5cdH1cblxuXHQuaGFzLXZlcnktZGFyay1ncmF5LWJhY2tncm91bmQtY29sb3Ige1xuXHRcdGJhY2tncm91bmQtY29sb3I6ICMzMTMxMzE7XG5cdH1cbn1cblxuLy8gRGVwcmVjYXRlZCBmcm9tIFVJLCBrZXB0IGZvciBiYWNrLWNvbXBhdC5cbkBtaXhpbiBmb3JlZ3JvdW5kLWNvbG9ycy1kZXByZWNhdGVkKCkge1xuXHQuaGFzLXZlcnktbGlnaHQtZ3JheS1jb2xvciB7XG5cdFx0Y29sb3I6ICNlZWU7XG5cdH1cblxuXHQuaGFzLXZlcnktZGFyay1ncmF5LWNvbG9yIHtcblx0XHRjb2xvcjogIzMxMzEzMTtcblx0fVxufVxuXG4vLyBEZXByZWNhdGVkIGZyb20gVUksIGtlcHQgZm9yIGJhY2stY29tcGF0LlxuQG1peGluIGdyYWRpZW50LWNvbG9ycy1kZXByZWNhdGVkKCkge1xuXHQvLyBPdXIgY2xhc3NlcyB1c2VzIHRoZSBzYW1lIHZhbHVlcyB3ZSBzZXQgZm9yIGdyYWRpZW50IHZhbHVlIGF0dHJpYnV0ZXMuXG5cblx0Lyogc3R5bGVsaW50LWRpc2FibGUgQHN0eWxpc3RpYy9mdW5jdGlvbi1jb21tYS1zcGFjZS1hZnRlciAtLSBXZSBjYW4gbm90IHVzZSBzcGFjaW5nIGJlY2F1c2Ugb2YgV1AgbXVsdGkgc2l0ZSBrc2VzIHJ1bGUuICovXG5cdC5oYXMtdml2aWQtZ3JlZW4tY3lhbi10by12aXZpZC1jeWFuLWJsdWUtZ3JhZGllbnQtYmFja2dyb3VuZCB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KDEzNWRlZyxyZ2JhKDAsMjA4LDEzMiwxKSAwJSxyZ2JhKDYsMTQ3LDIyNywxKSAxMDAlKTtcblx0fVxuXG5cdC5oYXMtcHVycGxlLWNydXNoLWdyYWRpZW50LWJhY2tncm91bmQge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCgxMzVkZWcscmdiKDUyLDIyNiwyMjgpIDAlLHJnYig3MSwzMywyNTEpIDUwJSxyZ2IoMTcxLDI5LDI1NCkgMTAwJSk7XG5cdH1cblxuXHQuaGFzLWhhenktZGF3bi1ncmFkaWVudC1iYWNrZ3JvdW5kIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQoMTM1ZGVnLHJnYigyNTAsMTcyLDE2OCkgMCUscmdiKDIxOCwyMDgsMjM2KSAxMDAlKTtcblx0fVxuXG5cdC5oYXMtc3ViZHVlZC1vbGl2ZS1ncmFkaWVudC1iYWNrZ3JvdW5kIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQoMTM1ZGVnLHJnYigyNTAsMjUwLDIyNSkgMCUscmdiKDEwMywxNjYsMTEzKSAxMDAlKTtcblx0fVxuXG5cdC5oYXMtYXRvbWljLWNyZWFtLWdyYWRpZW50LWJhY2tncm91bmQge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCgxMzVkZWcscmdiKDI1MywyMTUsMTU0KSAwJSxyZ2IoMCw3NCw4OSkgMTAwJSk7XG5cdH1cblxuXHQuaGFzLW5pZ2h0c2hhZGUtZ3JhZGllbnQtYmFja2dyb3VuZCB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KDEzNWRlZyxyZ2IoNTEsOSwxMDQpIDAlLHJnYig0OSwyMDUsMjA3KSAxMDAlKTtcblx0fVxuXG5cdC5oYXMtbWlkbmlnaHQtZ3JhZGllbnQtYmFja2dyb3VuZCB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KDEzNWRlZyxyZ2IoMiwzLDEyOSkgMCUscmdiKDQwLDExNiwyNTIpIDEwMCUpO1xuXHR9XG5cdC8qIHN0eWxlbGludC1lbmFibGUgQHN0eWxpc3RpYy9mdW5jdGlvbi1jb21tYS1zcGFjZS1hZnRlciAqL1xufVxuXG5AbWl4aW4gY3VzdG9tLXNjcm9sbGJhcnMtb24taG92ZXIoJGhhbmRsZS1jb2xvciwgJGhhbmRsZS1jb2xvci1ob3Zlcikge1xuXG5cdC8vIFdlYktpdFxuXHQmOjotd2Via2l0LXNjcm9sbGJhciB7XG5cdFx0d2lkdGg6IDEycHg7XG5cdFx0aGVpZ2h0OiAxMnB4O1xuXHR9XG5cdCY6Oi13ZWJraXQtc2Nyb2xsYmFyLXRyYWNrIHtcblx0XHRiYWNrZ3JvdW5kLWNvbG9yOiB0cmFuc3BhcmVudDtcblx0fVxuXHQmOjotd2Via2l0LXNjcm9sbGJhci10aHVtYiB7XG5cdFx0YmFja2dyb3VuZC1jb2xvcjogJGhhbmRsZS1jb2xvcjtcblx0XHRib3JkZXItcmFkaXVzOiA4cHg7XG5cdFx0Ym9yZGVyOiAzcHggc29saWQgdHJhbnNwYXJlbnQ7XG5cdFx0YmFja2dyb3VuZC1jbGlwOiBwYWRkaW5nLWJveDtcblx0fVxuXHQmOmhvdmVyOjotd2Via2l0LXNjcm9sbGJhci10aHVtYiwgLy8gVGhpcyBuZWVkcyBzcGVjaWZpY2l0eS5cblx0Jjpmb2N1czo6LXdlYmtpdC1zY3JvbGxiYXItdGh1bWIsXG5cdCY6Zm9jdXMtd2l0aGluOjotd2Via2l0LXNjcm9sbGJhci10aHVtYiB7XG5cdFx0YmFja2dyb3VuZC1jb2xvcjogJGhhbmRsZS1jb2xvci1ob3Zlcjtcblx0fVxuXG5cdC8vIEZpcmVmb3ggMTA5KyBhbmQgQ2hyb21lIDExMStcblx0c2Nyb2xsYmFyLXdpZHRoOiB0aGluO1xuXHRzY3JvbGxiYXItZ3V0dGVyOiBzdGFibGUgYm90aC1lZGdlcztcblx0c2Nyb2xsYmFyLWNvbG9yOiAkaGFuZGxlLWNvbG9yIHRyYW5zcGFyZW50OyAvLyBTeW50YXgsIFwiZGFya1wiLCBcImxpZ2h0XCIsIG9yIFwiI2hhbmRsZS1jb2xvciAjdHJhY2stY29sb3JcIlxuXG5cdCY6aG92ZXIsXG5cdCY6Zm9jdXMsXG5cdCY6Zm9jdXMtd2l0aGluIHtcblx0XHRzY3JvbGxiYXItY29sb3I6ICRoYW5kbGUtY29sb3ItaG92ZXIgdHJhbnNwYXJlbnQ7XG5cdH1cblxuXHQvLyBOZWVkZWQgdG8gZml4IGEgU2FmYXJpIHJlbmRlcmluZyBpc3N1ZS5cblx0d2lsbC1jaGFuZ2U6IHRyYW5zZm9ybTtcblxuXHQvLyBBbHdheXMgc2hvdyBzY3JvbGxiYXIgb24gTW9iaWxlIGRldmljZXMuXG5cdEBtZWRpYSAoaG92ZXI6IG5vbmUpIHtcblx0XHQmIHtcblx0XHRcdHNjcm9sbGJhci1jb2xvcjogJGhhbmRsZS1jb2xvci1ob3ZlciB0cmFuc3BhcmVudDtcblx0XHR9XG5cdH1cbn1cblxuQG1peGluIHNlbGVjdGVkLWJsb2NrLW91dGxpbmUoJHdpZHRoUmF0aW86IDEpIHtcblx0b3V0bGluZS1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXHRvdXRsaW5lLXN0eWxlOiBzb2xpZDtcblx0b3V0bGluZS13aWR0aDogY2FsYygjeyR3aWR0aFJhdGlvfSAqICh2YXIoLS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXMpIC8gdmFyKC0td3AtYmxvY2stZWRpdG9yLWlmcmFtZS16b29tLW91dC1zY2FsZSwgMSkpKTtcblx0b3V0bGluZS1vZmZzZXQ6IGNhbGMoI3skd2lkdGhSYXRpb30gKiAoKC0xICogdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSApIC8gdmFyKC0td3AtYmxvY2stZWRpdG9yLWlmcmFtZS16b29tLW91dC1zY2FsZSwgMSkpKTtcbn1cblxuQG1peGluIHNlbGVjdGVkLWJsb2NrLWZvY3VzKCR3aWR0aFJhdGlvOiAxKSB7XG5cdGNvbnRlbnQ6IFwiXCI7XG5cdHBvc2l0aW9uOiBhYnNvbHV0ZTtcblx0cG9pbnRlci1ldmVudHM6IG5vbmU7XG5cdHRvcDogMDtcblx0cmlnaHQ6IDA7XG5cdGJvdHRvbTogMDtcblx0bGVmdDogMDtcblx0QGluY2x1ZGUgc2VsZWN0ZWQtYmxvY2stb3V0bGluZSgkd2lkdGhSYXRpbyk7XG59XG4iLCIvKipcbiAqIEJyZWFrcG9pbnRzICYgTWVkaWEgUXVlcmllc1xuICovXG5cbi8vIE1vc3QgdXNlZCBicmVha3BvaW50c1xuJGJyZWFrLXhodWdlOiAxOTIwcHg7XG4kYnJlYWstaHVnZTogMTQ0MHB4O1xuJGJyZWFrLXdpZGU6IDEyODBweDtcbiRicmVhay14bGFyZ2U6IDEwODBweDtcbiRicmVhay1sYXJnZTogOTYwcHg7XHQvLyBhZG1pbiBzaWRlYmFyIGF1dG8gZm9sZHNcbiRicmVhay1tZWRpdW06IDc4MnB4O1x0Ly8gYWRtaW5iYXIgZ29lcyBiaWdcbiRicmVhay1zbWFsbDogNjAwcHg7XG4kYnJlYWstbW9iaWxlOiA0ODBweDtcbiRicmVhay16b29tZWQtaW46IDI4MHB4O1xuXG4vLyBBbGwgbWVkaWEgcXVlcmllcyBjdXJyZW50bHkgaW4gV29yZFByZXNzOlxuLy9cbi8vIG1pbi13aWR0aDogMjAwMHB4XG4vLyBtaW4td2lkdGg6IDE2ODBweFxuLy8gbWluLXdpZHRoOiAxMjUwcHhcbi8vIG1heC13aWR0aDogMTEyMHB4ICpcbi8vIG1heC13aWR0aDogMTAwMHB4XG4vLyBtaW4td2lkdGg6IDc2OXB4IGFuZCBtYXgtd2lkdGg6IDEwMDBweFxuLy8gbWF4LXdpZHRoOiA5NjBweCAqXG4vLyBtYXgtd2lkdGg6IDkwMHB4XG4vLyBtYXgtd2lkdGg6IDg1MHB4XG4vLyBtaW4td2lkdGg6IDgwMHB4IGFuZCBtYXgtd2lkdGg6IDE0OTlweFxuLy8gbWF4LXdpZHRoOiA4MDBweFxuLy8gbWF4LXdpZHRoOiA3OTlweFxuLy8gbWF4LXdpZHRoOiA3ODJweCAqXG4vLyBtYXgtd2lkdGg6IDc2OHB4XG4vLyBtYXgtd2lkdGg6IDY0MHB4ICpcbi8vIG1heC13aWR0aDogNjAwcHggKlxuLy8gbWF4LXdpZHRoOiA1MjBweFxuLy8gbWF4LXdpZHRoOiA1MDBweFxuLy8gbWF4LXdpZHRoOiA0ODBweCAqXG4vLyBtYXgtd2lkdGg6IDQwMHB4ICpcbi8vIG1heC13aWR0aDogMzgwcHhcbi8vIG1heC13aWR0aDogMzIwcHggKlxuLy9cbi8vIFRob3NlIG1hcmtlZCAqIHNlZW0gdG8gYmUgbW9yZSBjb21tb25seSB1c2VkIHRoYW4gdGhlIG90aGVycy5cbi8vIExldCdzIHRyeSBhbmQgdXNlIGFzIGZldyBvZiB0aGVzZSBhcyBwb3NzaWJsZSwgYW5kIGJlIG1pbmRmdWwgYWJvdXQgYWRkaW5nIG5ldyBvbmVzLCBzbyB3ZSBkb24ndCBtYWtlIHRoZSBzaXR1YXRpb24gd29yc2VcbiIsIi8qKlxuKiAgQ29udmVydHMgYSBoZXggdmFsdWUgaW50byB0aGUgcmdiIGVxdWl2YWxlbnQuXG4qXG4qIEBwYXJhbSB7c3RyaW5nfSBoZXggLSB0aGUgaGV4YWRlY2ltYWwgdmFsdWUgdG8gY29udmVydFxuKiBAcmV0dXJuIHtzdHJpbmd9IGNvbW1hIHNlcGFyYXRlZCByZ2IgdmFsdWVzXG4qL1xuXG5AdXNlIFwic2Fzczpjb2xvclwiO1xuQHVzZSBcInNhc3M6bWV0YVwiO1xuXG5AZnVuY3Rpb24gaGV4LXRvLXJnYigkaGV4KSB7XG5cdC8qXG5cdCAqIFRPRE86IGBjb2xvci57cmVkfGdyZWVufGJsdWV9YCB3aWxsIHRyaWdnZXIgYSBkZXByZWNhdGlvbiB3YXJuaW5nIGluIERhcnQgU2Fzcyxcblx0ICogYnV0IHRoZSBTYXNzIHVzZWQgYnkgdGhlIEd1dGVuYmVyZyBwcm9qZWN0IGRvZXNuJ3Qgc3VwcG9ydCBgY29sb3IuY2hhbm5lbCgpYCB5ZXQsXG5cdCAqIHNvIHdlIGNhbid0IG1pZ3JhdGUgdG8gaXQgYXQgdGhpcyB0aW1lLlxuXHQgKiBJbiB0aGUgZnV0dXJlLCBhZnRlciB0aGUgR3V0ZW5iZXJnIHByb2plY3QgaGFzIGJlZW4gZnVsbHkgbWlncmF0ZWQgdG8gRGFydCBTYXNzLFxuXHQgKiBSZW1vdmUgdGhpcyBjb25kaXRpb25hbCBzdGF0ZW1lbnQgYW5kIHVzZSBvbmx5IGBjb2xvci5jaGFubmVsKClgLlxuXHQgKi9cblx0QGlmIG1ldGEuZnVuY3Rpb24tZXhpc3RzKFwiY2hhbm5lbFwiLCBcImNvbG9yXCIpIHtcblx0XHRAcmV0dXJuIGNvbG9yLmNoYW5uZWwoJGhleCwgXCJyZWRcIiksIGNvbG9yLmNoYW5uZWwoJGhleCwgXCJncmVlblwiKSwgY29sb3IuY2hhbm5lbCgkaGV4LCBcImJsdWVcIik7XG5cdH0gQGVsc2Uge1xuXHRcdEByZXR1cm4gY29sb3IucmVkKCRoZXgpLCBjb2xvci5ncmVlbigkaGV4KSwgY29sb3IuYmx1ZSgkaGV4KTtcblx0fVxufVxuIiwiLyoqXG4gKiBMb25nIGNvbnRlbnQgZmFkZSBtaXhpblxuICpcbiAqIENyZWF0ZXMgYSBmYWRpbmcgb3ZlcmxheSB0byBzaWduaWZ5IHRoYXQgdGhlIGNvbnRlbnQgaXMgbG9uZ2VyXG4gKiB0aGFuIHRoZSBzcGFjZSBhbGxvd3MuXG4gKi9cblxuQG1peGluIGxvbmctY29udGVudC1mYWRlKCRkaXJlY3Rpb246IHJpZ2h0LCAkc2l6ZTogMjAlLCAkY29sb3I6ICNmZmYsICRlZGdlOiAwLCAkei1pbmRleDogZmFsc2UpIHtcblx0Y29udGVudDogXCJcIjtcblx0ZGlzcGxheTogYmxvY2s7XG5cdHBvc2l0aW9uOiBhYnNvbHV0ZTtcblx0LXdlYmtpdC10b3VjaC1jYWxsb3V0OiBub25lO1xuXHQtd2Via2l0LXVzZXItc2VsZWN0OiBub25lO1xuXHQta2h0bWwtdXNlci1zZWxlY3Q6IG5vbmU7XG5cdC1tb3otdXNlci1zZWxlY3Q6IG5vbmU7XG5cdC1tcy11c2VyLXNlbGVjdDogbm9uZTtcblx0dXNlci1zZWxlY3Q6IG5vbmU7XG5cdHBvaW50ZXItZXZlbnRzOiBub25lO1xuXG5cdEBpZiAkei1pbmRleCB7XG5cdFx0ei1pbmRleDogJHotaW5kZXg7XG5cdH1cblxuXHRAaWYgJGRpcmVjdGlvbiA9PSBcImJvdHRvbVwiIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQodG8gdG9wLCB0cmFuc3BhcmVudCwgJGNvbG9yIDkwJSk7XG5cdFx0bGVmdDogJGVkZ2U7XG5cdFx0cmlnaHQ6ICRlZGdlO1xuXHRcdHRvcDogJGVkZ2U7XG5cdFx0Ym90dG9tOiBjYWxjKDEwMCUgLSAkc2l6ZSk7XG5cdFx0d2lkdGg6IGF1dG87XG5cdH1cblxuXHRAaWYgJGRpcmVjdGlvbiA9PSBcInRvcFwiIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQodG8gYm90dG9tLCB0cmFuc3BhcmVudCwgJGNvbG9yIDkwJSk7XG5cdFx0dG9wOiBjYWxjKDEwMCUgLSAkc2l6ZSk7XG5cdFx0bGVmdDogJGVkZ2U7XG5cdFx0cmlnaHQ6ICRlZGdlO1xuXHRcdGJvdHRvbTogJGVkZ2U7XG5cdFx0d2lkdGg6IGF1dG87XG5cdH1cblxuXHRAaWYgJGRpcmVjdGlvbiA9PSBcImxlZnRcIiB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KHRvIGxlZnQsIHRyYW5zcGFyZW50LCAkY29sb3IgOTAlKTtcblx0XHR0b3A6ICRlZGdlO1xuXHRcdGxlZnQ6ICRlZGdlO1xuXHRcdGJvdHRvbTogJGVkZ2U7XG5cdFx0cmlnaHQ6IGF1dG87XG5cdFx0d2lkdGg6ICRzaXplO1xuXHRcdGhlaWdodDogYXV0bztcblx0fVxuXG5cdEBpZiAkZGlyZWN0aW9uID09IFwicmlnaHRcIiB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KHRvIHJpZ2h0LCB0cmFuc3BhcmVudCwgJGNvbG9yIDkwJSk7XG5cdFx0dG9wOiAkZWRnZTtcblx0XHRib3R0b206ICRlZGdlO1xuXHRcdHJpZ2h0OiAkZWRnZTtcblx0XHRsZWZ0OiBhdXRvO1xuXHRcdHdpZHRoOiAkc2l6ZTtcblx0XHRoZWlnaHQ6IGF1dG87XG5cdH1cbn1cbiIsIkB1c2UgXCJAd29yZHByZXNzL2Jhc2Utc3R5bGVzL3ZhcmlhYmxlc1wiO1xuQHVzZSBcIkB3b3JkcHJlc3MvYmFzZS1zdHlsZXMvbWl4aW5zXCI7XG5cbi5ib290LWxheW91dCB7XG5cdGhlaWdodDogMTAwJTtcblx0d2lkdGg6IDEwMCU7XG5cdGRpc3BsYXk6IGZsZXg7XG5cdGZsZXgtZGlyZWN0aW9uOiByb3c7XG5cdGNvbG9yOiB2YXIoLS13cGRzLWNvbG9yLWZnLWNvbnRlbnQtbmV1dHJhbCwgIzFlMWUxZSk7XG5cdGlzb2xhdGlvbjogaXNvbGF0ZTtcblx0YmFja2dyb3VuZDogdmFyKC0td3Bkcy1jb2xvci1iZy1zdXJmYWNlLW5ldXRyYWwtd2VhaywgI2YwZjBmMCk7XG59XG5cbi5ib290LWxheW91dF9fc2lkZWJhci1iYWNrZHJvcCB7XG5cdHBvc2l0aW9uOiBmaXhlZDtcblx0dG9wOiAwO1xuXHRsZWZ0OiAwO1xuXHRyaWdodDogMDtcblx0Ym90dG9tOiAwO1xuXHRiYWNrZ3JvdW5kLWNvbG9yOiByZ2JhKDAsIDAsIDAsIDAuNSk7XG5cdHotaW5kZXg6IDEwMDAwMjtcblx0Y3Vyc29yOiBwb2ludGVyO1xufVxuXG4uYm9vdC1sYXlvdXRfX3NpZGViYXIge1xuXHRoZWlnaHQ6IDEwMCU7XG5cdGZsZXgtc2hyaW5rOiAwO1xuXHR3aWR0aDogMjQwcHg7XG5cdHBvc2l0aW9uOiByZWxhdGl2ZTtcblx0b3ZlcmZsb3c6IGhpZGRlbjtcblxuXHQmLmlzLW1vYmlsZSB7XG5cdFx0cG9zaXRpb246IGZpeGVkO1xuXHRcdHRvcDogMDtcblx0XHRib3R0b206IDA7XG5cdFx0bGVmdDogMDtcblx0XHR3aWR0aDogMzAwcHg7XG5cdFx0bWF4LXdpZHRoOiA4NXZ3O1xuXHRcdGJhY2tncm91bmQ6IHZhcigtLXdwZHMtY29sb3ItYmctc3VyZmFjZS1uZXV0cmFsLXdlYWssICNmMGYwZjApO1xuXHRcdHotaW5kZXg6IDEwMDAwMztcblx0XHRib3gtc2hhZG93OiAycHggMCA4cHggcmdiYSgwLCAwLCAwLCAwLjIpO1xuXHR9XG59XG5cbi5ib290LWxheW91dF9fbW9iaWxlLXNpZGViYXItZHJhd2VyIHtcblx0cG9zaXRpb246IGZpeGVkO1xuXHR0b3A6IDA7XG5cdGxlZnQ6IDA7XG5cdHJpZ2h0OiAwO1xuXHR6LWluZGV4OiAzO1xuXHRiYWNrZ3JvdW5kOiB2YXIoLS13cGRzLWNvbG9yLWJnLXN1cmZhY2UtbmV1dHJhbCwgI2ZmZik7XG5cdHBhZGRpbmc6IHZhcmlhYmxlcy4kZ3JpZC11bml0LTIwO1xuXHRib3JkZXItYm90dG9tOiAxcHggc29saWQgdmFyKC0td3Bkcy1jb2xvci1zdHJva2Utc3VyZmFjZS1uZXV0cmFsLXdlYWssICNkZGQpO1xuXHRkaXNwbGF5OiBmbGV4O1xuXHRqdXN0aWZ5LWNvbnRlbnQ6IGZsZXgtc3RhcnQ7XG5cdGFsaWduLWl0ZW1zOiBjZW50ZXI7XG59XG5cbi5ib290LWxheW91dF9fY2FudmFzLmhhcy1tb2JpbGUtZHJhd2VyIHtcblx0cG9zaXRpb246IHJlbGF0aXZlO1xuXHRwYWRkaW5nLXRvcDogNjVweDtcbn1cblxuLmJvb3QtbGF5b3V0X19zdXJmYWNlcyB7XG5cdGRpc3BsYXk6IGZsZXg7XG5cdGZsZXgtZ3JvdzogMTtcblx0bWFyZ2luOiAwO1xuXHRnYXA6IHZhcmlhYmxlcy4kZ3JpZC11bml0LTEwO1xuXG5cdEBpbmNsdWRlIG1peGlucy5icmVhay1tZWRpdW0ge1xuXHRcdG1hcmdpbjogdmFyaWFibGVzLiRncmlkLXVuaXQtMTA7XG5cblx0XHQuYm9vdC1sYXlvdXQtLXNpbmdsZS1wYWdlICYge1xuXHRcdFx0bWFyZ2luLXRvcDogMDtcblx0XHRcdG1hcmdpbi1sZWZ0OiAwO1xuXHRcdH1cblx0fVxufVxuXG4uYm9vdC1sYXlvdXRfX3N0YWdlLFxuLmJvb3QtbGF5b3V0X19pbnNwZWN0b3Ige1xuXHRmbGV4OiAxO1xuXHRvdmVyZmxvdy15OiBhdXRvO1xuXHRiYWNrZ3JvdW5kOiB2YXIoLS13cGRzLWNvbG9yLWJnLXN1cmZhY2UtbmV1dHJhbCwgI2ZmZik7XG5cdGNvbG9yOiB2YXIoLS13cGRzLWNvbG9yLWZnLWNvbnRlbnQtbmV1dHJhbCwgIzFlMWUxZSk7XG5cdGJveC1zaGFkb3c6IDAgMXB4IDNweCByZ2JhKDAsIDAsIDAsIDAuMSk7XG5cdGJvcmRlcjogMXB4IHNvbGlkIHZhcigtLXdwZHMtY29sb3Itc3Ryb2tlLXN1cmZhY2UtbmV1dHJhbC13ZWFrLCAjZGRkKTtcblx0cG9zaXRpb246IHJlbGF0aXZlO1xuXG5cdC8vIE1vYmlsZS1maXJzdDogc3VyZmFjZXMgdGFrZSBmdWxsIHNjcmVlbiB3aXRoIGZpeGVkIHBvc2l0aW9uaW5nXG5cdHBvc2l0aW9uOiBmaXhlZDtcblx0dG9wOiAwO1xuXHRsZWZ0OiAwO1xuXHRyaWdodDogMDtcblx0Ym90dG9tOiAwO1xuXHR3aWR0aDogMTAwdnc7XG5cdGhlaWdodDogMTAwdmg7XG5cdGJvcmRlci1yYWRpdXM6IDA7XG5cdG1hcmdpbjogMDtcblxuXHQvLyBEZXNrdG9wOiByZXN0b3JlIG9yaWdpbmFsIHN0eWxlc1xuXHRAaW5jbHVkZSBtaXhpbnMuYnJlYWstbWVkaXVtIHtcblx0XHRwb3NpdGlvbjogc3RhdGljO1xuXHRcdHdpZHRoOiBhdXRvO1xuXHRcdGhlaWdodDogYXV0bztcblx0XHRib3JkZXItcmFkaXVzOiA4cHg7XG5cdFx0bWFyZ2luOiAwO1xuXHR9XG59XG5cbi5ib290LWxheW91dF9fc3RhZ2Uge1xuXHR6LWluZGV4OiAyOyAvLyBIaWdoZXN0IHN1cmZhY2UgcHJpb3JpdHkgb24gbW9iaWxlXG5cblx0QGluY2x1ZGUgbWl4aW5zLmJyZWFrLW1lZGl1bSB7XG5cdFx0ei1pbmRleDogYXV0bztcblx0fVxufVxuXG4uYm9vdC1sYXlvdXRfX2luc3BlY3RvciB7XG5cdHotaW5kZXg6IDM7IC8vIE1lZGl1bSBzdXJmYWNlIHByaW9yaXR5IG9uIG1vYmlsZVxuXG5cdEBpbmNsdWRlIG1peGlucy5icmVhay1tZWRpdW0ge1xuXHRcdHotaW5kZXg6IGF1dG87XG5cdH1cbn1cblxuLmJvb3QtbGF5b3V0X19jYW52YXMge1xuXHRmbGV4OiAxO1xuXHRvdmVyZmxvdy15OiBhdXRvO1xuXHRiYWNrZ3JvdW5kOiB2YXIoLS13cGRzLWNvbG9yLWJnLXN1cmZhY2UtbmV1dHJhbCwgI2ZmZik7XG5cdGNvbG9yOiB2YXIoLS13cGRzLWNvbG9yLWZnLWNvbnRlbnQtbmV1dHJhbCwgIzFlMWUxZSk7XG5cdGJveC1zaGFkb3c6IDAgMXB4IDNweCByZ2JhKDAsIDAsIDAsIDAuMSk7XG5cdGJvcmRlcjogMXB4IHNvbGlkIHZhcigtLXdwZHMtY29sb3Itc3Ryb2tlLXN1cmZhY2UtbmV1dHJhbC13ZWFrLCAjZGRkKTtcblx0cG9zaXRpb246IHJlbGF0aXZlO1xuXG5cdC8vIE1vYmlsZS1maXJzdDogZnVsbCBzY3JlZW4gd2l0aCBsb3dlc3QgcHJpb3JpdHlcblx0cG9zaXRpb246IGZpeGVkO1xuXHR0b3A6IDA7XG5cdGxlZnQ6IDA7XG5cdHJpZ2h0OiAwO1xuXHRib3R0b206IDA7XG5cdHdpZHRoOiAxMDB2dztcblx0aGVpZ2h0OiAxMDB2aDtcblx0ei1pbmRleDogMTsgLy8gTG93ZXN0IHN1cmZhY2UgcHJpb3JpdHlcblx0Ym9yZGVyLXJhZGl1czogMDtcblx0bWFyZ2luOiAwO1xuXG5cdC8vIERlc2t0b3A6IHJlc3RvcmUgb3JpZ2luYWwgc3R5bGVzXG5cdEBpbmNsdWRlIG1peGlucy5icmVhay1tZWRpdW0ge1xuXHRcdHBvc2l0aW9uOiBzdGF0aWM7XG5cdFx0d2lkdGg6IGF1dG87XG5cdFx0aGVpZ2h0OiBhdXRvO1xuXHRcdGJvcmRlci1yYWRpdXM6IDhweDtcblx0XHR6LWluZGV4OiBhdXRvO1xuXHR9XG59XG5cbi5ib290LWxheW91dC5oYXMtY2FudmFzIC5ib290LWxheW91dF9fc3RhZ2UsXG4uYm9vdC1sYXlvdXRfX2luc3BlY3RvciB7XG5cdEBpbmNsdWRlIG1peGlucy5icmVhay1tZWRpdW0ge1xuXHRcdG1heC13aWR0aDogNDAwcHg7XG5cdH1cbn1cblxuLmJvb3QtbGF5b3V0X19jYW52YXMgLmludGVyZmFjZS1pbnRlcmZhY2Utc2tlbGV0b24ge1xuXHRwb3NpdGlvbjogcmVsYXRpdmU7XG5cdGhlaWdodDogMTAwJTtcblx0dG9wOiAwICFpbXBvcnRhbnQ7XG5cdGxlZnQ6IDAgIWltcG9ydGFudDtcbn1cblxuLy8gRnVsbC1zY3JlZW4gY2FudmFzIG1vZGVcbi5ib290LWxheW91dC5oYXMtZnVsbC1jYW52YXMgLmJvb3QtbGF5b3V0X19zdXJmYWNlcyB7XG5cdG1hcmdpbjogMDtcblx0Z2FwOiAwO1xufVxuXG4uYm9vdC1sYXlvdXQuaGFzLWZ1bGwtY2FudmFzIC5ib290LWxheW91dF9fc3RhZ2UsXG4uYm9vdC1sYXlvdXQuaGFzLWZ1bGwtY2FudmFzIC5ib290LWxheW91dF9faW5zcGVjdG9yIHtcblx0ZGlzcGxheTogbm9uZTtcbn1cblxuLmJvb3QtbGF5b3V0Lmhhcy1mdWxsLWNhbnZhcyAuYm9vdC1sYXlvdXRfX2NhbnZhcyB7XG5cdHBvc2l0aW9uOiBmaXhlZDtcblx0dG9wOiAwO1xuXHRsZWZ0OiAwO1xuXHRyaWdodDogMDtcblx0Ym90dG9tOiAwO1xuXHRtYXgtd2lkdGg6IG5vbmU7XG5cdG1hcmdpbjogMDtcblx0Ym9yZGVyLXJhZGl1czogMDtcblx0Ym9yZGVyOiBub25lO1xuXHRib3gtc2hhZG93OiBub25lO1xuXHRvdmVyZmxvdzogaGlkZGVuO1xufVxuIl19 */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css11));
var { useMatches: useMatches4, Outlet: Outlet2 } = unlock(routePrivateApis7);
function RootSinglePage() {
  const matches = useMatches4();
  const currentMatch = matches[matches.length - 1];
  const canvas = currentMatch?.loaderData?.canvas;
  const routeContentModule = currentMatch?.loaderData?.routeContentModule;
  const isFullScreen = canvas && !canvas.isPreview;
  useRouteTitle();
  return /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(UserThemeProvider, { isRoot: true, color: { bg: "#f8f8f8" }, children: /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(UserThemeProvider, { color: { bg: "#1d2327" }, children: /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)(
    "div",
    {
      className: clsx_default("boot-layout boot-layout--single-page", {
        "has-canvas": !!canvas || canvas === null,
        "has-full-canvas": isFullScreen
      }),
      children: [
        /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_commands3.CommandMenu, {}),
        /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(SavePanel, {}),
        /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(import_editor5.EditorSnackbars, {}),
        /* @__PURE__ */ (0, import_jsx_runtime33.jsxs)("div", { className: "boot-layout__surfaces", children: [
          /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(UserThemeProvider, { color: { bg: "#ffffff" }, children: /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(Outlet2, {}) }),
          (canvas || canvas === null) && /* @__PURE__ */ (0, import_jsx_runtime33.jsx)("div", { className: "boot-layout__canvas", children: /* @__PURE__ */ (0, import_jsx_runtime33.jsx)(
            CanvasRenderer,
            {
              canvas,
              routeContentModule
            }
          ) })
        ] })
      ]
    }
  ) }) });
}

// packages/boot/build-module/components/app/index.js
var import_jsx_runtime34 = __toESM(require_jsx_runtime());
function App({ rootComponent }) {
  const routes = (0, import_data11.useSelect)((select) => select(store).getRoutes(), []);
  return /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(Router, { routes, rootComponent });
}
async function init({
  mountId,
  menuItems,
  routes,
  initModules
}) {
  (menuItems ?? []).forEach((menuItem) => {
    (0, import_data11.dispatch)(store).registerMenuItem(menuItem.id, menuItem);
  });
  (routes ?? []).forEach((route) => {
    (0, import_data11.dispatch)(store).registerRoute(route);
  });
  for (const moduleId of initModules ?? []) {
    const module = await import(moduleId);
    await module.init();
  }
  const rootElement = document.getElementById(mountId);
  if (rootElement) {
    const root = (0, import_element15.createRoot)(rootElement);
    root.render(
      /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(import_element15.StrictMode, { children: /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(App, {}) })
    );
  }
}
async function initSinglePage({
  mountId,
  routes
}) {
  (routes ?? []).forEach((route) => {
    (0, import_data11.dispatch)(store).registerRoute(route);
  });
  const rootElement = document.getElementById(mountId);
  if (rootElement) {
    const root = (0, import_element15.createRoot)(rootElement);
    root.render(
      /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(import_element15.StrictMode, { children: /* @__PURE__ */ (0, import_jsx_runtime34.jsx)(App, { rootComponent: RootSinglePage }) })
    );
  }
}

// packages/boot/build-module/index.js
var css12 = `@charset "UTF-8";
/* -------------------------------------------
 *  Autogenerated by \u26CB Terrazzo. DO NOT EDIT!
 * ------------------------------------------- */
:root {
  --wpds-border-radius-surface-lg: 8px; /* Large radius for surfaces */
  --wpds-border-radius-surface-md: 4px; /* Medium radius for surfaces */
  --wpds-border-radius-surface-sm: 2px; /* Small radius for surfaces */
  --wpds-border-radius-surface-xs: 1px; /* Extra small radius for surfaces */
  --wpds-border-width-interactive-focus: 2px; /* Border width for focus ring */
  --wpds-border-width-surface-lg: 8px; /* Large width for surfaces */
  --wpds-border-width-surface-md: 4px; /* Medium width for surfaces */
  --wpds-border-width-surface-sm: 2px; /* Small width for surfaces */
  --wpds-border-width-surface-xs: 1px; /* Extra small width for surfaces */
  --wpds-color-bg-interactive-brand: #00000000; /* Background color for interactive elements with brand tone and normal emphasis. */
  --wpds-color-bg-interactive-brand-active: #f6f8fd; /* Background color for interactive elements with brand tone and normal emphasis that are hovered, focused, or active. */
  --wpds-color-bg-interactive-brand-disabled: #e2e2e2; /* Background color for interactive elements with brand tone and normal emphasis, in their disabled state. */
  --wpds-color-bg-interactive-brand-strong: #3858e9; /* Background color for interactive elements with brand tone and strong emphasis. */
  --wpds-color-bg-interactive-brand-strong-active: #2e49d9; /* Background color for interactive elements with brand tone and strong emphasis that are hovered, focused, or active. */
  --wpds-color-bg-interactive-brand-strong-disabled: #d2d2d2; /* Background color for interactive elements with brand tone and strong emphasis, in their disabled state. */
  --wpds-color-bg-interactive-brand-weak: #00000000; /* Background color for interactive elements with brand tone and weak emphasis. */
  --wpds-color-bg-interactive-brand-weak-active: #e6eaf4; /* Background color for interactive elements with brand tone and weak emphasis that are hovered, focused, or active. */
  --wpds-color-bg-interactive-brand-weak-disabled: #e2e2e2; /* Background color for interactive elements with brand tone and weak emphasis, in their disabled state. */
  --wpds-color-bg-interactive-error: #00000000; /* Background color for interactive elements with error tone and normal emphasis. */
  --wpds-color-bg-interactive-error-active: #fff6f4; /* Background color for interactive elements with error tone and normal emphasis that are hovered, focused, or active. */
  --wpds-color-bg-interactive-error-disabled: #e2e2e2; /* Background color for interactive elements with error tone and normal emphasis, in their disabled state. */
  --wpds-color-bg-interactive-error-strong: #cc1818; /* Background color for interactive elements with error tone and strong emphasis. */
  --wpds-color-bg-interactive-error-strong-active: #b90000; /* Background color for interactive elements with error tone and strong emphasis that are hovered, focused, or active. */
  --wpds-color-bg-interactive-error-strong-disabled: #d2d2d2; /* Background color for interactive elements with error tone and strong emphasis, in their disabled state. */
  --wpds-color-bg-interactive-error-weak: #00000000; /* Background color for interactive elements with error tone and weak emphasis. */
  --wpds-color-bg-interactive-error-weak-active: #f6e6e3; /* Background color for interactive elements with error tone and weak emphasis that are hovered, focused, or active. */
  --wpds-color-bg-interactive-error-weak-disabled: #e2e2e2; /* Background color for interactive elements with error tone and weak emphasis, in their disabled state. */
  --wpds-color-bg-interactive-neutral: #00000000; /* Background color for interactive elements with neutral tone and normal emphasis. */
  --wpds-color-bg-interactive-neutral-active: #eaeaea; /* Background color for interactive elements with neutral tone and normal emphasis that are hovered, focused, or active. */
  --wpds-color-bg-interactive-neutral-disabled: #e2e2e2; /* Background color for interactive elements with neutral tone and normal emphasis, in their disabled state. */
  --wpds-color-bg-interactive-neutral-strong: #2d2d2d; /* Background color for interactive elements with neutral tone and strong emphasis. */
  --wpds-color-bg-interactive-neutral-strong-active: #1e1e1e; /* Background color for interactive elements with neutral tone and strong emphasis that are hovered, focused, or active. */
  --wpds-color-bg-interactive-neutral-strong-disabled: #d2d2d2; /* Background color for interactive elements with neutral tone and strong emphasis, in their disabled state. */
  --wpds-color-bg-interactive-neutral-weak: #00000000; /* Background color for interactive elements with neutral tone and weak emphasis. */
  --wpds-color-bg-interactive-neutral-weak-active: #eaeaea; /* Background color for interactive elements with neutral tone and weak emphasis that are hovered, focused, or active. */
  --wpds-color-bg-interactive-neutral-weak-disabled: #e2e2e2; /* Background color for interactive elements with neutral tone and weak emphasis, in their disabled state. */
  --wpds-color-bg-surface-brand: #ecf0f9; /* Background color for surfaces with brand tone and normal emphasis. */
  --wpds-color-bg-surface-caution: #fee994; /* Background color for surfaces with caution tone and normal emphasis. */
  --wpds-color-bg-surface-caution-weak: #fff9c9; /* Background color for surfaces with caution tone and weak emphasis. */
  --wpds-color-bg-surface-error: #f6e6e3; /* Background color for surfaces with error tone and normal emphasis. */
  --wpds-color-bg-surface-error-weak: #fff6f4; /* Background color for surfaces with error tone and weak emphasis. */
  --wpds-color-bg-surface-info: #deebfa; /* Background color for surfaces with info tone and normal emphasis. */
  --wpds-color-bg-surface-info-weak: #f3f9ff; /* Background color for surfaces with info tone and weak emphasis. */
  --wpds-color-bg-surface-neutral: #f8f8f8; /* Background color for surfaces with normal emphasis. */
  --wpds-color-bg-surface-neutral-strong: #ffffff; /* Background color for surfaces with strong emphasis. */
  --wpds-color-bg-surface-neutral-weak: #f0f0f0; /* Background color for surfaces with weak emphasis. */
  --wpds-color-bg-surface-success: #c5f7cc; /* Background color for surfaces with success tone and normal emphasis. */
  --wpds-color-bg-surface-success-weak: #eaffed; /* Background color for surfaces with success tone and weak emphasis. */
  --wpds-color-bg-surface-warning: #fde6bd; /* Background color for surfaces with warning tone and normal emphasis. */
  --wpds-color-bg-surface-warning-weak: #fff7e0; /* Background color for surfaces with warning tone and weak emphasis. */
  --wpds-color-bg-thumb-brand: #3858e9; /* Background color for thumbs with a brand tone and normal emphasis (eg. slider thumb and filled track). */
  --wpds-color-bg-thumb-brand-active: #3858e9; /* Background color for thumbs with a brand tone and normal emphasis (eg. slider thumb and filled track) that are hovered, focused, or active. */
  --wpds-color-bg-thumb-brand-disabled: #d8d8d8; /* Background color for thumbs with a brand tone and normal emphasis (eg. slider thumb and filled track), in their disabled state. */
  --wpds-color-bg-thumb-neutral-weak: #8a8a8a; /* Background color for thumbs with a neutral tone and weak emphasis (eg. scrollbar thumb). */
  --wpds-color-bg-thumb-neutral-weak-active: #6c6c6c; /* Background color for thumbs with a neutral tone and weak emphasis (eg. scrollbar thumb) that are hovered, focused, or active. */
  --wpds-color-bg-track-neutral: #d8d8d8; /* Background color for tracks with a neutral tone and normal emphasis (eg. slider or progressbar track). */
  --wpds-color-bg-track-neutral-weak: #e0e0e0; /* Background color for tracks with a neutral tone and weak emphasis (eg. scrollbar track). */
  --wpds-color-fg-content-caution: #281d00; /* Foreground color for content like text with caution tone and normal emphasis. */
  --wpds-color-fg-content-caution-weak: #826a00; /* Foreground color for content like text with caution tone and weak emphasis. */
  --wpds-color-fg-content-error: #470000; /* Foreground color for content like text with error tone and normal emphasis. */
  --wpds-color-fg-content-error-weak: #cc1818; /* Foreground color for content like text with error tone and weak emphasis. */
  --wpds-color-fg-content-info: #001b4f; /* Foreground color for content like text with info tone and normal emphasis. */
  --wpds-color-fg-content-info-weak: #006bd7; /* Foreground color for content like text with info tone and weak emphasis. */
  --wpds-color-fg-content-neutral: #1e1e1e; /* Foreground color for content like text with normal emphasis. */
  --wpds-color-fg-content-neutral-weak: #6d6d6d; /* Foreground color for content like text with weak emphasis. */
  --wpds-color-fg-content-success: #002900; /* Foreground color for content like text with success tone and normal emphasis. */
  --wpds-color-fg-content-success-weak: #007f30; /* Foreground color for content like text with success tone and weak emphasis. */
  --wpds-color-fg-content-warning: #2e1900; /* Foreground color for content like text with warning tone and normal emphasis. */
  --wpds-color-fg-content-warning-weak: #926300; /* Foreground color for content like text with warning tone and weak emphasis. */
  --wpds-color-fg-interactive-brand: #3858e9; /* Foreground color for interactive elements with brand tone and normal emphasis. */
  --wpds-color-fg-interactive-brand-active: #3858e9; /* Foreground color for interactive elements with brand tone and normal emphasis that are hovered, focused, or active. */
  --wpds-color-fg-interactive-brand-disabled: #8a8a8a; /* Foreground color for interactive elements with brand tone and normal emphasis, in their disabled state. */
  --wpds-color-fg-interactive-brand-strong: #eff0f2; /* Foreground color for interactive elements with brand tone and strong emphasis. */
  --wpds-color-fg-interactive-brand-strong-active: #eff0f2; /* Foreground color for interactive elements with brand tone and strong emphasis that are hovered, focused, or active. */
  --wpds-color-fg-interactive-brand-strong-disabled: #6d6d6d; /* Foreground color for interactive elements with brand tone and strong emphasis, in their disabled state. */
  --wpds-color-fg-interactive-error: #cc1818; /* Foreground color for interactive elements with error tone and normal emphasis. */
  --wpds-color-fg-interactive-error-active: #cc1818; /* Foreground color for interactive elements with error tone and normal emphasis that are hovered, focused, or active. */
  --wpds-color-fg-interactive-error-disabled: #8a8a8a; /* Foreground color for interactive elements with error tone and normal emphasis, in their disabled state. */
  --wpds-color-fg-interactive-error-strong: #f2efef; /* Foreground color for interactive elements with error tone and strong emphasis. */
  --wpds-color-fg-interactive-error-strong-active: #f2efef; /* Foreground color for interactive elements with error tone and strong emphasis that are hovered, focused, or active. */
  --wpds-color-fg-interactive-error-strong-disabled: #6d6d6d; /* Foreground color for interactive elements with error tone and strong emphasis, in their disabled state. */
  --wpds-color-fg-interactive-neutral: #1e1e1e; /* Foreground color for interactive elements with neutral tone and normal emphasis. */
  --wpds-color-fg-interactive-neutral-active: #1e1e1e; /* Foreground color for interactive elements with neutral tone and normal emphasis that are hovered, focused, or active. */
  --wpds-color-fg-interactive-neutral-disabled: #8a8a8a; /* Foreground color for interactive elements with neutral tone and normal emphasis, in their disabled state. */
  --wpds-color-fg-interactive-neutral-strong: #f0f0f0; /* Foreground color for interactive elements with neutral tone and strong emphasis. */
  --wpds-color-fg-interactive-neutral-strong-active: #f0f0f0; /* Foreground color for interactive elements with neutral tone and strong emphasis that are hovered, focused, or active. */
  --wpds-color-fg-interactive-neutral-strong-disabled: #6d6d6d; /* Foreground color for interactive elements with neutral tone and strong emphasis, in their disabled state. */
  --wpds-color-fg-interactive-neutral-weak: #6d6d6d; /* Foreground color for interactive elements with neutral tone and weak emphasis. */
  --wpds-color-fg-interactive-neutral-weak-disabled: #8a8a8a; /* Foreground color for interactive elements with neutral tone and weak emphasis, in their disabled state. */
  --wpds-color-stroke-focus-brand: #3858e9; /* Accessible stroke color applied to focus rings. */
  --wpds-color-stroke-interactive-brand: #3858e9; /* Accessible stroke color used for interactive brand-toned elements with normal emphasis. */
  --wpds-color-stroke-interactive-brand-active: #2337c8; /* Accessible stroke color used for interactive brand-toned elements with normal emphasis that are hovered, focused, or active. */
  --wpds-color-stroke-interactive-error: #cc1818; /* Accessible stroke color used for interactive error-toned elements with normal emphasis. */
  --wpds-color-stroke-interactive-error-active: #9d0000; /* Accessible stroke color used for interactive error-toned elements with normal emphasis that are hovered, focused, or active. */
  --wpds-color-stroke-interactive-error-strong: #cc1818; /* Accessible stroke color used for interactive error-toned elements with strong emphasis. */
  --wpds-color-stroke-interactive-neutral: #8a8a8a; /* Accessible stroke color used for interactive neutrally-toned elements with normal emphasis. */
  --wpds-color-stroke-interactive-neutral-active: #6c6c6c; /* Accessible stroke color used for interactive neutrally-toned elements with normal emphasis that are hovered, focused, or active. */
  --wpds-color-stroke-interactive-neutral-disabled: #d8d8d8; /* Accessible stroke color used for interactive neutrally-toned elements with normal emphasis, in their disabled state. */
  --wpds-color-stroke-interactive-neutral-strong: #6c6c6c; /* Accessible stroke color used for interactive neutrally-toned elements with strong emphasis. */
  --wpds-color-stroke-surface-brand: #a3b1d4; /* Decorative stroke color used to define brand-toned surface boundaries with normal emphasis. */
  --wpds-color-stroke-surface-brand-strong: #3858e9; /* Decorative stroke color used to define brand-toned surface boundaries with strong emphasis. */
  --wpds-color-stroke-surface-error: #daa39b; /* Decorative stroke color used to define error-toned surface boundaries with normal emphasis. */
  --wpds-color-stroke-surface-error-strong: #cc1818; /* Decorative stroke color used to define error-toned surface boundaries with strong emphasis. */
  --wpds-color-stroke-surface-info: #9fbcdc; /* Decorative stroke color used to define info-toned surface boundaries with normal emphasis. */
  --wpds-color-stroke-surface-info-strong: #006bd7; /* Decorative stroke color used to define info-toned surface boundaries with strong emphasis. */
  --wpds-color-stroke-surface-neutral: #d8d8d8; /* Decorative stroke color used to define neutrally-toned surface boundaries with normal emphasis. */
  --wpds-color-stroke-surface-neutral-strong: #8a8a8a; /* Decorative stroke color used to define neutrally-toned surface boundaries with strong emphasis. */
  --wpds-color-stroke-surface-neutral-weak: #e0e0e0; /* Decorative stroke color used to define neutrally-toned surface boundaries with weak emphasis. */
  --wpds-color-stroke-surface-success: #8ac894; /* Decorative stroke color used to define success-toned surface boundaries with normal emphasis. */
  --wpds-color-stroke-surface-success-strong: #007f30; /* Decorative stroke color used to define success-toned surface boundaries with strong emphasis. */
  --wpds-color-stroke-surface-warning: #d0b381; /* Decorative stroke color used to define warning-toned surface boundaries with normal emphasis. */
  --wpds-color-stroke-surface-warning-strong: #926300; /* Decorative stroke color used to define warning-toned surface boundaries with strong emphasis. */
  --wpds-dimension-base: 4px; /* Base dimension unit */
  --wpds-dimension-gap-2xs: 4px; /* 2x extra small gap */
  --wpds-dimension-gap-lg: 24px; /* Large gap */
  --wpds-dimension-gap-md: 16px; /* Medium gap */
  --wpds-dimension-gap-sm: 12px; /* Small gap */
  --wpds-dimension-gap-xl: 40px; /* Extra large gap */
  --wpds-dimension-gap-xs: 8px; /* Extra small gap */
  --wpds-dimension-padding-surface-2xs: 4px; /* 2x extra small spacing for surfaces */
  --wpds-dimension-padding-surface-lg: 32px; /* Large spacing for surfaces */
  --wpds-dimension-padding-surface-md: 24px; /* Medium spacing for surfaces */
  --wpds-dimension-padding-surface-sm: 16px; /* Small spacing for surfaces */
  --wpds-dimension-padding-surface-xs: 8px; /* Extra small spacing for surfaces */
  --wpds-elevation-large: 0 5px 15px 0 #00000014, 0 15px 27px 0 #00000012,
  	0 30px 36px 0 #0000000a, 0 50px 43px 0 #00000005; /* For components that confirm decisions or handle necessary interruptions. Example: Modals. */
  --wpds-elevation-medium: 0 2px 3px 0 #0000000d, 0 4px 5px 0 #0000000a,
  	0 12px 12px 0 #00000008, 0 16px 16px 0 #00000005; /* For components that offer additional actions. Example: Menus, Command Palette */
  --wpds-elevation-small: 0 1px 2px 0 #0000000d, 0 2px 3px 0 #0000000a,
  	0 6px 6px 0 #00000008, 0 8px 8px 0 #00000005; /* For components that provide contextual feedback without being intrusive. Generally non-interruptive. Example: Tooltips, Snackbar. */
  --wpds-elevation-x-small: 0 1px 1px 0 #00000008, 0 1px 2px 0 #00000005,
  	0 3px 3px 0 #00000005, 0 4px 4px 0 #00000003; /* For sections and containers that group related content and controls, which may overlap other content. Example: Preview Frame. */
  --wpds-font-family-body: -apple-system, system-ui, "Segoe UI", "Roboto",
  	"Oxygen-Sans", "Ubuntu", "Cantarell", "Helvetica Neue", sans-serif; /* Body font family */
  --wpds-font-family-heading: -apple-system, system-ui, "Segoe UI", "Roboto",
  	"Oxygen-Sans", "Ubuntu", "Cantarell", "Helvetica Neue", sans-serif; /* Headings font family */
  --wpds-font-family-mono: "Menlo", "Consolas", monaco, monospace; /* Monospace font family */
  --wpds-font-line-height-2x-large: 40px; /* 2X large line height */
  --wpds-font-line-height-large: 28px; /* Large line height */
  --wpds-font-line-height-medium: 24px; /* Medium line height */
  --wpds-font-line-height-small: 20px; /* Small line height */
  --wpds-font-line-height-x-large: 32px; /* Extra large line height */
  --wpds-font-line-height-x-small: 16px; /* Extra small line height */
  --wpds-font-size-2x-large: 32px; /* 2X large font size */
  --wpds-font-size-large: 15px; /* Large font size */
  --wpds-font-size-medium: 13px; /* Medium font size */
  --wpds-font-size-small: 12px; /* Small font size */
  --wpds-font-size-x-large: 20px; /* Extra large font size */
  --wpds-font-size-x-small: 11px; /* Extra small font size */
}

[data-wpds-theme-provider-id][data-wpds-density=default] {
  --wpds-dimension-base: 4px; /* Base dimension unit */
  --wpds-dimension-gap-2xs: 4px; /* 2x extra small gap */
  --wpds-dimension-gap-lg: 24px; /* Large gap */
  --wpds-dimension-gap-md: 16px; /* Medium gap */
  --wpds-dimension-gap-sm: 12px; /* Small gap */
  --wpds-dimension-gap-xl: 40px; /* Extra large gap */
  --wpds-dimension-gap-xs: 8px; /* Extra small gap */
  --wpds-dimension-padding-surface-2xs: 4px; /* 2x extra small spacing for surfaces */
  --wpds-dimension-padding-surface-lg: 32px; /* Large spacing for surfaces */
  --wpds-dimension-padding-surface-md: 24px; /* Medium spacing for surfaces */
  --wpds-dimension-padding-surface-sm: 16px; /* Small spacing for surfaces */
  --wpds-dimension-padding-surface-xs: 8px; /* Extra small spacing for surfaces */
}

[data-wpds-theme-provider-id][data-wpds-density=compact] {
  --wpds-dimension-gap-2xs: 4px; /* 2x extra small gap */
  --wpds-dimension-gap-lg: 20px; /* Large gap */
  --wpds-dimension-gap-md: 12px; /* Medium gap */
  --wpds-dimension-gap-sm: 8px; /* Small gap */
  --wpds-dimension-gap-xl: 32px; /* Extra large gap */
  --wpds-dimension-gap-xs: 4px; /* Extra small gap */
  --wpds-dimension-padding-surface-2xs: 4px; /* 2x extra small spacing for surfaces */
  --wpds-dimension-padding-surface-lg: 24px; /* Large spacing for surfaces */
  --wpds-dimension-padding-surface-md: 20px; /* Medium spacing for surfaces */
  --wpds-dimension-padding-surface-sm: 12px; /* Small spacing for surfaces */
  --wpds-dimension-padding-surface-xs: 4px; /* Extra small spacing for surfaces */
}

[data-wpds-theme-provider-id][data-wpds-density=comfortable] {
  --wpds-dimension-gap-2xs: 8px; /* 2x extra small gap */
  --wpds-dimension-gap-lg: 32px; /* Large gap */
  --wpds-dimension-gap-md: 20px; /* Medium gap */
  --wpds-dimension-gap-sm: 16px; /* Small gap */
  --wpds-dimension-gap-xl: 48px; /* Extra large gap */
  --wpds-dimension-gap-xs: 12px; /* Extra small gap */
  --wpds-dimension-padding-surface-2xs: 8px; /* 2x extra small spacing for surfaces */
  --wpds-dimension-padding-surface-lg: 40px; /* Large spacing for surfaces */
  --wpds-dimension-padding-surface-md: 32px; /* Medium spacing for surfaces */
  --wpds-dimension-padding-surface-sm: 20px; /* Small spacing for surfaces */
  --wpds-dimension-padding-surface-xs: 12px; /* Extra small spacing for surfaces */
}

@media (-webkit-min-device-pixel-ratio: 2), (min-resolution: 192dpi) {
  :root {
    --wpds-border-width-interactive-focus: 1.5px; /* Border width for focus ring */
  }
}
/**
 * Colors
 */
/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
/**
 * Typography
 */
/**
 * Breakpoints & Media Queries
 */
/**
*  Converts a hex value into the rgb equivalent.
*
* @param {string} hex - the hexadecimal value to convert
* @return {string} comma separated rgb values
*/
/**
 * Long content fade mixin
 *
 * Creates a fading overlay to signify that the content is longer
 * than the space allows.
 */
/**
 * Breakpoint mixins
 */
/**
 * Focus styles.
 */
/**
 * Applies editor left position to the selector passed as argument
 */
/**
 * Styles that are reused verbatim in a few places
 */
/**
 * Allows users to opt-out of animations via OS-level preferences.
 */
/**
 * Reset default styles for JavaScript UI based pages.
 * This is a WP-admin agnostic reset
 */
/**
 * Reset the WP Admin page styles for Gutenberg-like pages.
 */
.admin-ui-page {
  display: flex;
  height: 100%;
  background-color: #fff;
  color: #2f2f2f;
  position: relative;
  z-index: 1;
  flex-flow: column;
}

.admin-ui-page__header {
  padding: 16px 24px;
  border-bottom: 1px solid #f0f0f0;
  background: #fff;
  position: sticky;
  top: 0;
  z-index: 1;
}

.admin-ui-page__sidebar-toggle-slot:empty {
  display: none;
}

.admin-ui-page__header-subtitle {
  padding-block-end: 8px;
  color: #757575;
  font-family: -apple-system, "system-ui", "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
  font-weight: 400;
  font-size: 13px;
  line-height: 20px;
  margin: 0;
}

.admin-ui-page__content {
  flex-grow: 1;
  overflow: auto;
  display: flex;
  flex-direction: column;
}

.admin-ui-page__content.has-padding {
  padding: 16px 24px;
}

.show-icon-labels .admin-ui-page__header-actions .components-button.has-icon {
  width: auto;
  padding: 0 8px;
}

.show-icon-labels .admin-ui-page__header-actions .components-button.has-icon svg {
  display: none;
}

.show-icon-labels .admin-ui-page__header-actions .components-button.has-icon::after {
  content: attr(aria-label);
  font-size: 12px;
}

.admin-ui-breadcrumbs__list {
  list-style: none;
  padding: 0;
  margin: 0;
  gap: 0;
  font-size: 15px;
  min-height: 32px;
  font-weight: 500;
}

.admin-ui-breadcrumbs__list li:not(:last-child)::after {
  content: "/";
  margin: 0 8px;
}

.admin-ui-breadcrumbs__list h1 {
  font-size: inherit;
  line-height: inherit;
}

/**
 * Typography
 */
/**
 * SCSS Variables.
 *
 * Please use variables from this sheet to ensure consistency across the UI.
 * Don't add to this sheet unless you're pretty sure the value will be reused in many places.
 * For example, don't add rules to this sheet that affect block visuals. It's purely for UI.
 */
/**
 * Colors
 */
/**
 * Fonts & basic variables.
 */
/**
 * Typography
 */
/**
 * Grid System.
 * https://make.wordpress.org/design/2019/10/31/proposal-a-consistent-spacing-system-for-wordpress/
 */
/**
 * Radius scale.
 */
/**
 * Elevation scale.
 */
/**
 * Dimensions.
 */
/**
 * Mobile specific styles
 */
/**
 * Editor styles.
 */
/**
 * Block & Editor UI.
 */
/**
 * Block paddings.
 */
/**
 * React Native specific.
 * These variables do not appear to be used anywhere else.
 */
/**
 * Breakpoints & Media Queries
 */
/**
*  Converts a hex value into the rgb equivalent.
*
* @param {string} hex - the hexadecimal value to convert
* @return {string} comma separated rgb values
*/
/**
 * Long content fade mixin
 *
 * Creates a fading overlay to signify that the content is longer
 * than the space allows.
 */
/**
 * Breakpoint mixins
 */
/**
 * Focus styles.
 */
/**
 * Applies editor left position to the selector passed as argument
 */
/**
 * Styles that are reused verbatim in a few places
 */
/**
 * Allows users to opt-out of animations via OS-level preferences.
 */
/**
 * Reset default styles for JavaScript UI based pages.
 * This is a WP-admin agnostic reset
 */
/**
 * Reset the WP Admin page styles for Gutenberg-like pages.
 */
@media (min-width: 600px) {
  .boot-layout-container .boot-layout {
    position: absolute;
    top: 0;
    right: 0;
    bottom: 0;
    left: 0;
    min-height: calc(100vh - 46px);
  }
}
@media (min-width: 782px) {
  .boot-layout-container .boot-layout {
    min-height: calc(100vh - 32px);
  }
  body:has(.boot-layout.has-full-canvas) .boot-layout-container .boot-layout {
    min-height: 100vh;
  }
}
.boot-layout-container .boot-layout img {
  max-width: 100%;
  height: auto;
}

.boot-layout .components-editor-notices__snackbar {
  position: fixed;
  right: 0;
  bottom: 16px;
  padding-left: 16px;
  padding-right: 16px;
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy9ib290L3NyYyIsInNvdXJjZXMiOlsiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvdGhlbWUvc3JjL3ByZWJ1aWx0L2Nzcy9kZXNpZ24tdG9rZW5zLmNzcyIsIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2FkbWluLXVpL2J1aWxkLXN0eWxlL3N0eWxlLmNzcyIsIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19taXhpbnMuc2NzcyIsIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL192YXJpYWJsZXMuc2NzcyIsIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19jb2xvcnMuc2NzcyIsIi4uLy4uLy4uL25vZGVfbW9kdWxlcy9Ad29yZHByZXNzL2Jhc2Utc3R5bGVzL19icmVha3BvaW50cy5zY3NzIiwiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX2Z1bmN0aW9ucy5zY3NzIiwiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX2xvbmctY29udGVudC1mYWRlLnNjc3MiLCJzdHlsZS5zY3NzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiI7QUFBQTtBQUFBO0FBQUE7QUFJQTtFQUNDO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7QUFBQSxxREFDbUQ7RUFDbkQ7QUFBQSxxREFDbUQ7RUFDbkQ7QUFBQSxpREFDK0M7RUFDL0M7QUFBQSxpREFDK0M7RUFDL0M7QUFBQSx1RUFDcUU7RUFDckU7QUFBQSx1RUFDcUU7RUFDckU7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7OztBQUdEO0VBQ0M7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBOzs7QUFHRDtFQUNDO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7OztBQUdEO0VBQ0M7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTs7O0FBR0Q7RUFDQztJQUNDOzs7QUN6TUY7QUFBQTtBQUFBO0FBR0E7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFPQTtBQUFBO0FBQUE7QUFHQTtBQUFBO0FBQUE7QUFHQTtBQUFBO0FBQUE7QUFBQTtBQUlBO0FBQUE7QUFBQTtBQUdBO0FBQUE7QUFBQTtBQUdBO0FBQUE7QUFBQTtBQUdBO0FBQUE7QUFBQTtBQUdBO0FBQUE7QUFBQTtBQUdBO0FBQUE7QUFBQTtBQUdBO0FBQUE7QUFBQTtBQUdBO0FBQUE7QUFBQTtBQUFBO0FBSUE7QUFBQTtBQUFBO0FBR0E7QUFBQTtBQUFBO0FBR0E7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBTUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBTUE7QUFBQTtBQUFBO0FBR0E7QUFBQTtBQUFBO0FBR0E7QUFBQTtBQUFBO0FBR0E7QUFBQTtBQUFBO0FBR0E7QUFBQTtBQUFBO0FBR0E7QUFBQTtBQUFBO0FBQUE7QUFJQTtBQUFBO0FBQUE7QUFHQTtFQUNFO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBOzs7QUFHRjtFQUNFO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTs7O0FBR0Y7RUFDRTs7O0FBR0Y7RUFDRTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTs7O0FBR0Y7RUFDRTtFQUNBO0VBQ0E7RUFDQTs7O0FBRUY7RUFDRTs7O0FBR0Y7RUFDRTtFQUNBOzs7QUFFRjtFQUNFOzs7QUFFRjtFQUNFO0VBQ0E7OztBQUdGO0VBQ0U7RUFDQTtFQUNBO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7OztBQUVGO0VBQ0U7RUFDQTs7O0FBRUY7RUFDRTtFQUNBOzs7QUMzSkY7QUFBQTtBQUFBO0FDQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUNBQTtBQUFBO0FBQUE7QURVQTtBQUFBO0FBQUE7QUFPQTtBQUFBO0FBQUE7QUE2QkE7QUFBQTtBQUFBO0FBQUE7QUFpQkE7QUFBQTtBQUFBO0FBV0E7QUFBQTtBQUFBO0FBZ0JBO0FBQUE7QUFBQTtBQXlCQTtBQUFBO0FBQUE7QUFLQTtBQUFBO0FBQUE7QUFlQTtBQUFBO0FBQUE7QUFtQkE7QUFBQTtBQUFBO0FBU0E7QUFBQTtBQUFBO0FBQUE7QUVuS0E7QUFBQTtBQUFBO0FDQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FDQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0FMNEVBO0FBQUE7QUFBQTtBQTBEQTtBQUFBO0FBQUE7QUFnREE7QUFBQTtBQUFBO0FBcUNBO0FBQUE7QUFBQTtBQW9CQTtBQUFBO0FBQUE7QUF3TEE7QUFBQTtBQUFBO0FBQUE7QUFnREE7QUFBQTtBQUFBO0FBbFdDO0VNaEhEO0lBSUU7SUFDQTtJQUNBO0lBQ0E7SUFDQTtJQUNBOzs7QU5pR0Q7RU0xR0Q7SUFjRTs7RUFFQTtJQUNDOzs7QUFJRjtFQUNDO0VBQ0E7OztBQUlGO0VBQ0M7RUFDQTtFQUNBO0VBQ0E7RUFDQSIsInNvdXJjZXNDb250ZW50IjpbIi8qIC0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS1cbiAqICBBdXRvZ2VuZXJhdGVkIGJ5IOKbiyBUZXJyYXp6by4gRE8gTk9UIEVESVQhXG4gKiAtLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tLS0tICovXG5cbjpyb290IHtcblx0LS13cGRzLWJvcmRlci1yYWRpdXMtc3VyZmFjZS1sZzogOHB4OyAvKiBMYXJnZSByYWRpdXMgZm9yIHN1cmZhY2VzICovXG5cdC0td3Bkcy1ib3JkZXItcmFkaXVzLXN1cmZhY2UtbWQ6IDRweDsgLyogTWVkaXVtIHJhZGl1cyBmb3Igc3VyZmFjZXMgKi9cblx0LS13cGRzLWJvcmRlci1yYWRpdXMtc3VyZmFjZS1zbTogMnB4OyAvKiBTbWFsbCByYWRpdXMgZm9yIHN1cmZhY2VzICovXG5cdC0td3Bkcy1ib3JkZXItcmFkaXVzLXN1cmZhY2UteHM6IDFweDsgLyogRXh0cmEgc21hbGwgcmFkaXVzIGZvciBzdXJmYWNlcyAqL1xuXHQtLXdwZHMtYm9yZGVyLXdpZHRoLWludGVyYWN0aXZlLWZvY3VzOiAycHg7IC8qIEJvcmRlciB3aWR0aCBmb3IgZm9jdXMgcmluZyAqL1xuXHQtLXdwZHMtYm9yZGVyLXdpZHRoLXN1cmZhY2UtbGc6IDhweDsgLyogTGFyZ2Ugd2lkdGggZm9yIHN1cmZhY2VzICovXG5cdC0td3Bkcy1ib3JkZXItd2lkdGgtc3VyZmFjZS1tZDogNHB4OyAvKiBNZWRpdW0gd2lkdGggZm9yIHN1cmZhY2VzICovXG5cdC0td3Bkcy1ib3JkZXItd2lkdGgtc3VyZmFjZS1zbTogMnB4OyAvKiBTbWFsbCB3aWR0aCBmb3Igc3VyZmFjZXMgKi9cblx0LS13cGRzLWJvcmRlci13aWR0aC1zdXJmYWNlLXhzOiAxcHg7IC8qIEV4dHJhIHNtYWxsIHdpZHRoIGZvciBzdXJmYWNlcyAqL1xuXHQtLXdwZHMtY29sb3ItYmctaW50ZXJhY3RpdmUtYnJhbmQ6ICMwMDAwMDAwMDsgLyogQmFja2dyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBicmFuZCB0b25lIGFuZCBub3JtYWwgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1iZy1pbnRlcmFjdGl2ZS1icmFuZC1hY3RpdmU6ICNmNmY4ZmQ7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggYnJhbmQgdG9uZSBhbmQgbm9ybWFsIGVtcGhhc2lzIHRoYXQgYXJlIGhvdmVyZWQsIGZvY3VzZWQsIG9yIGFjdGl2ZS4gKi9cblx0LS13cGRzLWNvbG9yLWJnLWludGVyYWN0aXZlLWJyYW5kLWRpc2FibGVkOiAjZTJlMmUyOyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIGJyYW5kIHRvbmUgYW5kIG5vcm1hbCBlbXBoYXNpcywgaW4gdGhlaXIgZGlzYWJsZWQgc3RhdGUuICovXG5cdC0td3Bkcy1jb2xvci1iZy1pbnRlcmFjdGl2ZS1icmFuZC1zdHJvbmc6ICMzODU4ZTk7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggYnJhbmQgdG9uZSBhbmQgc3Ryb25nIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctaW50ZXJhY3RpdmUtYnJhbmQtc3Ryb25nLWFjdGl2ZTogIzJlNDlkOTsgLyogQmFja2dyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBicmFuZCB0b25lIGFuZCBzdHJvbmcgZW1waGFzaXMgdGhhdCBhcmUgaG92ZXJlZCwgZm9jdXNlZCwgb3IgYWN0aXZlLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctaW50ZXJhY3RpdmUtYnJhbmQtc3Ryb25nLWRpc2FibGVkOiAjZDJkMmQyOyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIGJyYW5kIHRvbmUgYW5kIHN0cm9uZyBlbXBoYXNpcywgaW4gdGhlaXIgZGlzYWJsZWQgc3RhdGUuICovXG5cdC0td3Bkcy1jb2xvci1iZy1pbnRlcmFjdGl2ZS1icmFuZC13ZWFrOiAjMDAwMDAwMDA7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggYnJhbmQgdG9uZSBhbmQgd2VhayBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLWJnLWludGVyYWN0aXZlLWJyYW5kLXdlYWstYWN0aXZlOiAjZTZlYWY0OyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIGJyYW5kIHRvbmUgYW5kIHdlYWsgZW1waGFzaXMgdGhhdCBhcmUgaG92ZXJlZCwgZm9jdXNlZCwgb3IgYWN0aXZlLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctaW50ZXJhY3RpdmUtYnJhbmQtd2Vhay1kaXNhYmxlZDogI2UyZTJlMjsgLyogQmFja2dyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBicmFuZCB0b25lIGFuZCB3ZWFrIGVtcGhhc2lzLCBpbiB0aGVpciBkaXNhYmxlZCBzdGF0ZS4gKi9cblx0LS13cGRzLWNvbG9yLWJnLWludGVyYWN0aXZlLWVycm9yOiAjMDAwMDAwMDA7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggZXJyb3IgdG9uZSBhbmQgbm9ybWFsIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctaW50ZXJhY3RpdmUtZXJyb3ItYWN0aXZlOiAjZmZmNmY0OyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIGVycm9yIHRvbmUgYW5kIG5vcm1hbCBlbXBoYXNpcyB0aGF0IGFyZSBob3ZlcmVkLCBmb2N1c2VkLCBvciBhY3RpdmUuICovXG5cdC0td3Bkcy1jb2xvci1iZy1pbnRlcmFjdGl2ZS1lcnJvci1kaXNhYmxlZDogI2UyZTJlMjsgLyogQmFja2dyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBlcnJvciB0b25lIGFuZCBub3JtYWwgZW1waGFzaXMsIGluIHRoZWlyIGRpc2FibGVkIHN0YXRlLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctaW50ZXJhY3RpdmUtZXJyb3Itc3Ryb25nOiAjY2MxODE4OyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIGVycm9yIHRvbmUgYW5kIHN0cm9uZyBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLWJnLWludGVyYWN0aXZlLWVycm9yLXN0cm9uZy1hY3RpdmU6ICNiOTAwMDA7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggZXJyb3IgdG9uZSBhbmQgc3Ryb25nIGVtcGhhc2lzIHRoYXQgYXJlIGhvdmVyZWQsIGZvY3VzZWQsIG9yIGFjdGl2ZS4gKi9cblx0LS13cGRzLWNvbG9yLWJnLWludGVyYWN0aXZlLWVycm9yLXN0cm9uZy1kaXNhYmxlZDogI2QyZDJkMjsgLyogQmFja2dyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBlcnJvciB0b25lIGFuZCBzdHJvbmcgZW1waGFzaXMsIGluIHRoZWlyIGRpc2FibGVkIHN0YXRlLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctaW50ZXJhY3RpdmUtZXJyb3Itd2VhazogIzAwMDAwMDAwOyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIGVycm9yIHRvbmUgYW5kIHdlYWsgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1iZy1pbnRlcmFjdGl2ZS1lcnJvci13ZWFrLWFjdGl2ZTogI2Y2ZTZlMzsgLyogQmFja2dyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBlcnJvciB0b25lIGFuZCB3ZWFrIGVtcGhhc2lzIHRoYXQgYXJlIGhvdmVyZWQsIGZvY3VzZWQsIG9yIGFjdGl2ZS4gKi9cblx0LS13cGRzLWNvbG9yLWJnLWludGVyYWN0aXZlLWVycm9yLXdlYWstZGlzYWJsZWQ6ICNlMmUyZTI7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggZXJyb3IgdG9uZSBhbmQgd2VhayBlbXBoYXNpcywgaW4gdGhlaXIgZGlzYWJsZWQgc3RhdGUuICovXG5cdC0td3Bkcy1jb2xvci1iZy1pbnRlcmFjdGl2ZS1uZXV0cmFsOiAjMDAwMDAwMDA7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggbmV1dHJhbCB0b25lIGFuZCBub3JtYWwgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1iZy1pbnRlcmFjdGl2ZS1uZXV0cmFsLWFjdGl2ZTogI2VhZWFlYTsgLyogQmFja2dyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBuZXV0cmFsIHRvbmUgYW5kIG5vcm1hbCBlbXBoYXNpcyB0aGF0IGFyZSBob3ZlcmVkLCBmb2N1c2VkLCBvciBhY3RpdmUuICovXG5cdC0td3Bkcy1jb2xvci1iZy1pbnRlcmFjdGl2ZS1uZXV0cmFsLWRpc2FibGVkOiAjZTJlMmUyOyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIG5ldXRyYWwgdG9uZSBhbmQgbm9ybWFsIGVtcGhhc2lzLCBpbiB0aGVpciBkaXNhYmxlZCBzdGF0ZS4gKi9cblx0LS13cGRzLWNvbG9yLWJnLWludGVyYWN0aXZlLW5ldXRyYWwtc3Ryb25nOiAjMmQyZDJkOyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIG5ldXRyYWwgdG9uZSBhbmQgc3Ryb25nIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctaW50ZXJhY3RpdmUtbmV1dHJhbC1zdHJvbmctYWN0aXZlOiAjMWUxZTFlOyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIG5ldXRyYWwgdG9uZSBhbmQgc3Ryb25nIGVtcGhhc2lzIHRoYXQgYXJlIGhvdmVyZWQsIGZvY3VzZWQsIG9yIGFjdGl2ZS4gKi9cblx0LS13cGRzLWNvbG9yLWJnLWludGVyYWN0aXZlLW5ldXRyYWwtc3Ryb25nLWRpc2FibGVkOiAjZDJkMmQyOyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIG5ldXRyYWwgdG9uZSBhbmQgc3Ryb25nIGVtcGhhc2lzLCBpbiB0aGVpciBkaXNhYmxlZCBzdGF0ZS4gKi9cblx0LS13cGRzLWNvbG9yLWJnLWludGVyYWN0aXZlLW5ldXRyYWwtd2VhazogIzAwMDAwMDAwOyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIG5ldXRyYWwgdG9uZSBhbmQgd2VhayBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLWJnLWludGVyYWN0aXZlLW5ldXRyYWwtd2Vhay1hY3RpdmU6ICNlYWVhZWE7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggbmV1dHJhbCB0b25lIGFuZCB3ZWFrIGVtcGhhc2lzIHRoYXQgYXJlIGhvdmVyZWQsIGZvY3VzZWQsIG9yIGFjdGl2ZS4gKi9cblx0LS13cGRzLWNvbG9yLWJnLWludGVyYWN0aXZlLW5ldXRyYWwtd2Vhay1kaXNhYmxlZDogI2UyZTJlMjsgLyogQmFja2dyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBuZXV0cmFsIHRvbmUgYW5kIHdlYWsgZW1waGFzaXMsIGluIHRoZWlyIGRpc2FibGVkIHN0YXRlLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctc3VyZmFjZS1icmFuZDogI2VjZjBmOTsgLyogQmFja2dyb3VuZCBjb2xvciBmb3Igc3VyZmFjZXMgd2l0aCBicmFuZCB0b25lIGFuZCBub3JtYWwgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1iZy1zdXJmYWNlLWNhdXRpb246ICNmZWU5OTQ7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIHN1cmZhY2VzIHdpdGggY2F1dGlvbiB0b25lIGFuZCBub3JtYWwgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1iZy1zdXJmYWNlLWNhdXRpb24td2VhazogI2ZmZjljOTsgLyogQmFja2dyb3VuZCBjb2xvciBmb3Igc3VyZmFjZXMgd2l0aCBjYXV0aW9uIHRvbmUgYW5kIHdlYWsgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1iZy1zdXJmYWNlLWVycm9yOiAjZjZlNmUzOyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBzdXJmYWNlcyB3aXRoIGVycm9yIHRvbmUgYW5kIG5vcm1hbCBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLWJnLXN1cmZhY2UtZXJyb3Itd2VhazogI2ZmZjZmNDsgLyogQmFja2dyb3VuZCBjb2xvciBmb3Igc3VyZmFjZXMgd2l0aCBlcnJvciB0b25lIGFuZCB3ZWFrIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctc3VyZmFjZS1pbmZvOiAjZGVlYmZhOyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBzdXJmYWNlcyB3aXRoIGluZm8gdG9uZSBhbmQgbm9ybWFsIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctc3VyZmFjZS1pbmZvLXdlYWs6ICNmM2Y5ZmY7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIHN1cmZhY2VzIHdpdGggaW5mbyB0b25lIGFuZCB3ZWFrIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctc3VyZmFjZS1uZXV0cmFsOiAjZjhmOGY4OyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBzdXJmYWNlcyB3aXRoIG5vcm1hbCBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLWJnLXN1cmZhY2UtbmV1dHJhbC1zdHJvbmc6ICNmZmZmZmY7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIHN1cmZhY2VzIHdpdGggc3Ryb25nIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctc3VyZmFjZS1uZXV0cmFsLXdlYWs6ICNmMGYwZjA7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIHN1cmZhY2VzIHdpdGggd2VhayBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLWJnLXN1cmZhY2Utc3VjY2VzczogI2M1ZjdjYzsgLyogQmFja2dyb3VuZCBjb2xvciBmb3Igc3VyZmFjZXMgd2l0aCBzdWNjZXNzIHRvbmUgYW5kIG5vcm1hbCBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLWJnLXN1cmZhY2Utc3VjY2Vzcy13ZWFrOiAjZWFmZmVkOyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBzdXJmYWNlcyB3aXRoIHN1Y2Nlc3MgdG9uZSBhbmQgd2VhayBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLWJnLXN1cmZhY2Utd2FybmluZzogI2ZkZTZiZDsgLyogQmFja2dyb3VuZCBjb2xvciBmb3Igc3VyZmFjZXMgd2l0aCB3YXJuaW5nIHRvbmUgYW5kIG5vcm1hbCBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLWJnLXN1cmZhY2Utd2FybmluZy13ZWFrOiAjZmZmN2UwOyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciBzdXJmYWNlcyB3aXRoIHdhcm5pbmcgdG9uZSBhbmQgd2VhayBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLWJnLXRodW1iLWJyYW5kOiAjMzg1OGU5OyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciB0aHVtYnMgd2l0aCBhIGJyYW5kIHRvbmUgYW5kIG5vcm1hbCBlbXBoYXNpcyAoZWcuIHNsaWRlciB0aHVtYiBhbmQgZmlsbGVkIHRyYWNrKS4gKi9cblx0LS13cGRzLWNvbG9yLWJnLXRodW1iLWJyYW5kLWFjdGl2ZTogIzM4NThlOTsgLyogQmFja2dyb3VuZCBjb2xvciBmb3IgdGh1bWJzIHdpdGggYSBicmFuZCB0b25lIGFuZCBub3JtYWwgZW1waGFzaXMgKGVnLiBzbGlkZXIgdGh1bWIgYW5kIGZpbGxlZCB0cmFjaykgdGhhdCBhcmUgaG92ZXJlZCwgZm9jdXNlZCwgb3IgYWN0aXZlLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctdGh1bWItYnJhbmQtZGlzYWJsZWQ6ICNkOGQ4ZDg7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIHRodW1icyB3aXRoIGEgYnJhbmQgdG9uZSBhbmQgbm9ybWFsIGVtcGhhc2lzIChlZy4gc2xpZGVyIHRodW1iIGFuZCBmaWxsZWQgdHJhY2spLCBpbiB0aGVpciBkaXNhYmxlZCBzdGF0ZS4gKi9cblx0LS13cGRzLWNvbG9yLWJnLXRodW1iLW5ldXRyYWwtd2VhazogIzhhOGE4YTsgLyogQmFja2dyb3VuZCBjb2xvciBmb3IgdGh1bWJzIHdpdGggYSBuZXV0cmFsIHRvbmUgYW5kIHdlYWsgZW1waGFzaXMgKGVnLiBzY3JvbGxiYXIgdGh1bWIpLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctdGh1bWItbmV1dHJhbC13ZWFrLWFjdGl2ZTogIzZjNmM2YzsgLyogQmFja2dyb3VuZCBjb2xvciBmb3IgdGh1bWJzIHdpdGggYSBuZXV0cmFsIHRvbmUgYW5kIHdlYWsgZW1waGFzaXMgKGVnLiBzY3JvbGxiYXIgdGh1bWIpIHRoYXQgYXJlIGhvdmVyZWQsIGZvY3VzZWQsIG9yIGFjdGl2ZS4gKi9cblx0LS13cGRzLWNvbG9yLWJnLXRyYWNrLW5ldXRyYWw6ICNkOGQ4ZDg7IC8qIEJhY2tncm91bmQgY29sb3IgZm9yIHRyYWNrcyB3aXRoIGEgbmV1dHJhbCB0b25lIGFuZCBub3JtYWwgZW1waGFzaXMgKGVnLiBzbGlkZXIgb3IgcHJvZ3Jlc3NiYXIgdHJhY2spLiAqL1xuXHQtLXdwZHMtY29sb3ItYmctdHJhY2stbmV1dHJhbC13ZWFrOiAjZTBlMGUwOyAvKiBCYWNrZ3JvdW5kIGNvbG9yIGZvciB0cmFja3Mgd2l0aCBhIG5ldXRyYWwgdG9uZSBhbmQgd2VhayBlbXBoYXNpcyAoZWcuIHNjcm9sbGJhciB0cmFjaykuICovXG5cdC0td3Bkcy1jb2xvci1mZy1jb250ZW50LWNhdXRpb246ICMyODFkMDA7IC8qIEZvcmVncm91bmQgY29sb3IgZm9yIGNvbnRlbnQgbGlrZSB0ZXh0IHdpdGggY2F1dGlvbiB0b25lIGFuZCBub3JtYWwgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1mZy1jb250ZW50LWNhdXRpb24td2VhazogIzgyNmEwMDsgLyogRm9yZWdyb3VuZCBjb2xvciBmb3IgY29udGVudCBsaWtlIHRleHQgd2l0aCBjYXV0aW9uIHRvbmUgYW5kIHdlYWsgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1mZy1jb250ZW50LWVycm9yOiAjNDcwMDAwOyAvKiBGb3JlZ3JvdW5kIGNvbG9yIGZvciBjb250ZW50IGxpa2UgdGV4dCB3aXRoIGVycm9yIHRvbmUgYW5kIG5vcm1hbCBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLWZnLWNvbnRlbnQtZXJyb3Itd2VhazogI2NjMTgxODsgLyogRm9yZWdyb3VuZCBjb2xvciBmb3IgY29udGVudCBsaWtlIHRleHQgd2l0aCBlcnJvciB0b25lIGFuZCB3ZWFrIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItZmctY29udGVudC1pbmZvOiAjMDAxYjRmOyAvKiBGb3JlZ3JvdW5kIGNvbG9yIGZvciBjb250ZW50IGxpa2UgdGV4dCB3aXRoIGluZm8gdG9uZSBhbmQgbm9ybWFsIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItZmctY29udGVudC1pbmZvLXdlYWs6ICMwMDZiZDc7IC8qIEZvcmVncm91bmQgY29sb3IgZm9yIGNvbnRlbnQgbGlrZSB0ZXh0IHdpdGggaW5mbyB0b25lIGFuZCB3ZWFrIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItZmctY29udGVudC1uZXV0cmFsOiAjMWUxZTFlOyAvKiBGb3JlZ3JvdW5kIGNvbG9yIGZvciBjb250ZW50IGxpa2UgdGV4dCB3aXRoIG5vcm1hbCBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLWZnLWNvbnRlbnQtbmV1dHJhbC13ZWFrOiAjNmQ2ZDZkOyAvKiBGb3JlZ3JvdW5kIGNvbG9yIGZvciBjb250ZW50IGxpa2UgdGV4dCB3aXRoIHdlYWsgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1mZy1jb250ZW50LXN1Y2Nlc3M6ICMwMDI5MDA7IC8qIEZvcmVncm91bmQgY29sb3IgZm9yIGNvbnRlbnQgbGlrZSB0ZXh0IHdpdGggc3VjY2VzcyB0b25lIGFuZCBub3JtYWwgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1mZy1jb250ZW50LXN1Y2Nlc3Mtd2VhazogIzAwN2YzMDsgLyogRm9yZWdyb3VuZCBjb2xvciBmb3IgY29udGVudCBsaWtlIHRleHQgd2l0aCBzdWNjZXNzIHRvbmUgYW5kIHdlYWsgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1mZy1jb250ZW50LXdhcm5pbmc6ICMyZTE5MDA7IC8qIEZvcmVncm91bmQgY29sb3IgZm9yIGNvbnRlbnQgbGlrZSB0ZXh0IHdpdGggd2FybmluZyB0b25lIGFuZCBub3JtYWwgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1mZy1jb250ZW50LXdhcm5pbmctd2VhazogIzkyNjMwMDsgLyogRm9yZWdyb3VuZCBjb2xvciBmb3IgY29udGVudCBsaWtlIHRleHQgd2l0aCB3YXJuaW5nIHRvbmUgYW5kIHdlYWsgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1mZy1pbnRlcmFjdGl2ZS1icmFuZDogIzM4NThlOTsgLyogRm9yZWdyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBicmFuZCB0b25lIGFuZCBub3JtYWwgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1mZy1pbnRlcmFjdGl2ZS1icmFuZC1hY3RpdmU6ICMzODU4ZTk7IC8qIEZvcmVncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggYnJhbmQgdG9uZSBhbmQgbm9ybWFsIGVtcGhhc2lzIHRoYXQgYXJlIGhvdmVyZWQsIGZvY3VzZWQsIG9yIGFjdGl2ZS4gKi9cblx0LS13cGRzLWNvbG9yLWZnLWludGVyYWN0aXZlLWJyYW5kLWRpc2FibGVkOiAjOGE4YThhOyAvKiBGb3JlZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIGJyYW5kIHRvbmUgYW5kIG5vcm1hbCBlbXBoYXNpcywgaW4gdGhlaXIgZGlzYWJsZWQgc3RhdGUuICovXG5cdC0td3Bkcy1jb2xvci1mZy1pbnRlcmFjdGl2ZS1icmFuZC1zdHJvbmc6ICNlZmYwZjI7IC8qIEZvcmVncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggYnJhbmQgdG9uZSBhbmQgc3Ryb25nIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItZmctaW50ZXJhY3RpdmUtYnJhbmQtc3Ryb25nLWFjdGl2ZTogI2VmZjBmMjsgLyogRm9yZWdyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBicmFuZCB0b25lIGFuZCBzdHJvbmcgZW1waGFzaXMgdGhhdCBhcmUgaG92ZXJlZCwgZm9jdXNlZCwgb3IgYWN0aXZlLiAqL1xuXHQtLXdwZHMtY29sb3ItZmctaW50ZXJhY3RpdmUtYnJhbmQtc3Ryb25nLWRpc2FibGVkOiAjNmQ2ZDZkOyAvKiBGb3JlZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIGJyYW5kIHRvbmUgYW5kIHN0cm9uZyBlbXBoYXNpcywgaW4gdGhlaXIgZGlzYWJsZWQgc3RhdGUuICovXG5cdC0td3Bkcy1jb2xvci1mZy1pbnRlcmFjdGl2ZS1lcnJvcjogI2NjMTgxODsgLyogRm9yZWdyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBlcnJvciB0b25lIGFuZCBub3JtYWwgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1mZy1pbnRlcmFjdGl2ZS1lcnJvci1hY3RpdmU6ICNjYzE4MTg7IC8qIEZvcmVncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggZXJyb3IgdG9uZSBhbmQgbm9ybWFsIGVtcGhhc2lzIHRoYXQgYXJlIGhvdmVyZWQsIGZvY3VzZWQsIG9yIGFjdGl2ZS4gKi9cblx0LS13cGRzLWNvbG9yLWZnLWludGVyYWN0aXZlLWVycm9yLWRpc2FibGVkOiAjOGE4YThhOyAvKiBGb3JlZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIGVycm9yIHRvbmUgYW5kIG5vcm1hbCBlbXBoYXNpcywgaW4gdGhlaXIgZGlzYWJsZWQgc3RhdGUuICovXG5cdC0td3Bkcy1jb2xvci1mZy1pbnRlcmFjdGl2ZS1lcnJvci1zdHJvbmc6ICNmMmVmZWY7IC8qIEZvcmVncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggZXJyb3IgdG9uZSBhbmQgc3Ryb25nIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItZmctaW50ZXJhY3RpdmUtZXJyb3Itc3Ryb25nLWFjdGl2ZTogI2YyZWZlZjsgLyogRm9yZWdyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBlcnJvciB0b25lIGFuZCBzdHJvbmcgZW1waGFzaXMgdGhhdCBhcmUgaG92ZXJlZCwgZm9jdXNlZCwgb3IgYWN0aXZlLiAqL1xuXHQtLXdwZHMtY29sb3ItZmctaW50ZXJhY3RpdmUtZXJyb3Itc3Ryb25nLWRpc2FibGVkOiAjNmQ2ZDZkOyAvKiBGb3JlZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIGVycm9yIHRvbmUgYW5kIHN0cm9uZyBlbXBoYXNpcywgaW4gdGhlaXIgZGlzYWJsZWQgc3RhdGUuICovXG5cdC0td3Bkcy1jb2xvci1mZy1pbnRlcmFjdGl2ZS1uZXV0cmFsOiAjMWUxZTFlOyAvKiBGb3JlZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIG5ldXRyYWwgdG9uZSBhbmQgbm9ybWFsIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItZmctaW50ZXJhY3RpdmUtbmV1dHJhbC1hY3RpdmU6ICMxZTFlMWU7IC8qIEZvcmVncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggbmV1dHJhbCB0b25lIGFuZCBub3JtYWwgZW1waGFzaXMgdGhhdCBhcmUgaG92ZXJlZCwgZm9jdXNlZCwgb3IgYWN0aXZlLiAqL1xuXHQtLXdwZHMtY29sb3ItZmctaW50ZXJhY3RpdmUtbmV1dHJhbC1kaXNhYmxlZDogIzhhOGE4YTsgLyogRm9yZWdyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBuZXV0cmFsIHRvbmUgYW5kIG5vcm1hbCBlbXBoYXNpcywgaW4gdGhlaXIgZGlzYWJsZWQgc3RhdGUuICovXG5cdC0td3Bkcy1jb2xvci1mZy1pbnRlcmFjdGl2ZS1uZXV0cmFsLXN0cm9uZzogI2YwZjBmMDsgLyogRm9yZWdyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBuZXV0cmFsIHRvbmUgYW5kIHN0cm9uZyBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLWZnLWludGVyYWN0aXZlLW5ldXRyYWwtc3Ryb25nLWFjdGl2ZTogI2YwZjBmMDsgLyogRm9yZWdyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBuZXV0cmFsIHRvbmUgYW5kIHN0cm9uZyBlbXBoYXNpcyB0aGF0IGFyZSBob3ZlcmVkLCBmb2N1c2VkLCBvciBhY3RpdmUuICovXG5cdC0td3Bkcy1jb2xvci1mZy1pbnRlcmFjdGl2ZS1uZXV0cmFsLXN0cm9uZy1kaXNhYmxlZDogIzZkNmQ2ZDsgLyogRm9yZWdyb3VuZCBjb2xvciBmb3IgaW50ZXJhY3RpdmUgZWxlbWVudHMgd2l0aCBuZXV0cmFsIHRvbmUgYW5kIHN0cm9uZyBlbXBoYXNpcywgaW4gdGhlaXIgZGlzYWJsZWQgc3RhdGUuICovXG5cdC0td3Bkcy1jb2xvci1mZy1pbnRlcmFjdGl2ZS1uZXV0cmFsLXdlYWs6ICM2ZDZkNmQ7IC8qIEZvcmVncm91bmQgY29sb3IgZm9yIGludGVyYWN0aXZlIGVsZW1lbnRzIHdpdGggbmV1dHJhbCB0b25lIGFuZCB3ZWFrIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3ItZmctaW50ZXJhY3RpdmUtbmV1dHJhbC13ZWFrLWRpc2FibGVkOiAjOGE4YThhOyAvKiBGb3JlZ3JvdW5kIGNvbG9yIGZvciBpbnRlcmFjdGl2ZSBlbGVtZW50cyB3aXRoIG5ldXRyYWwgdG9uZSBhbmQgd2VhayBlbXBoYXNpcywgaW4gdGhlaXIgZGlzYWJsZWQgc3RhdGUuICovXG5cdC0td3Bkcy1jb2xvci1zdHJva2UtZm9jdXMtYnJhbmQ6ICMzODU4ZTk7IC8qIEFjY2Vzc2libGUgc3Ryb2tlIGNvbG9yIGFwcGxpZWQgdG8gZm9jdXMgcmluZ3MuICovXG5cdC0td3Bkcy1jb2xvci1zdHJva2UtaW50ZXJhY3RpdmUtYnJhbmQ6ICMzODU4ZTk7IC8qIEFjY2Vzc2libGUgc3Ryb2tlIGNvbG9yIHVzZWQgZm9yIGludGVyYWN0aXZlIGJyYW5kLXRvbmVkIGVsZW1lbnRzIHdpdGggbm9ybWFsIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3Itc3Ryb2tlLWludGVyYWN0aXZlLWJyYW5kLWFjdGl2ZTogIzIzMzdjODsgLyogQWNjZXNzaWJsZSBzdHJva2UgY29sb3IgdXNlZCBmb3IgaW50ZXJhY3RpdmUgYnJhbmQtdG9uZWQgZWxlbWVudHMgd2l0aCBub3JtYWwgZW1waGFzaXMgdGhhdCBhcmUgaG92ZXJlZCwgZm9jdXNlZCwgb3IgYWN0aXZlLiAqL1xuXHQtLXdwZHMtY29sb3Itc3Ryb2tlLWludGVyYWN0aXZlLWVycm9yOiAjY2MxODE4OyAvKiBBY2Nlc3NpYmxlIHN0cm9rZSBjb2xvciB1c2VkIGZvciBpbnRlcmFjdGl2ZSBlcnJvci10b25lZCBlbGVtZW50cyB3aXRoIG5vcm1hbCBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLXN0cm9rZS1pbnRlcmFjdGl2ZS1lcnJvci1hY3RpdmU6ICM5ZDAwMDA7IC8qIEFjY2Vzc2libGUgc3Ryb2tlIGNvbG9yIHVzZWQgZm9yIGludGVyYWN0aXZlIGVycm9yLXRvbmVkIGVsZW1lbnRzIHdpdGggbm9ybWFsIGVtcGhhc2lzIHRoYXQgYXJlIGhvdmVyZWQsIGZvY3VzZWQsIG9yIGFjdGl2ZS4gKi9cblx0LS13cGRzLWNvbG9yLXN0cm9rZS1pbnRlcmFjdGl2ZS1lcnJvci1zdHJvbmc6ICNjYzE4MTg7IC8qIEFjY2Vzc2libGUgc3Ryb2tlIGNvbG9yIHVzZWQgZm9yIGludGVyYWN0aXZlIGVycm9yLXRvbmVkIGVsZW1lbnRzIHdpdGggc3Ryb25nIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3Itc3Ryb2tlLWludGVyYWN0aXZlLW5ldXRyYWw6ICM4YThhOGE7IC8qIEFjY2Vzc2libGUgc3Ryb2tlIGNvbG9yIHVzZWQgZm9yIGludGVyYWN0aXZlIG5ldXRyYWxseS10b25lZCBlbGVtZW50cyB3aXRoIG5vcm1hbCBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLXN0cm9rZS1pbnRlcmFjdGl2ZS1uZXV0cmFsLWFjdGl2ZTogIzZjNmM2YzsgLyogQWNjZXNzaWJsZSBzdHJva2UgY29sb3IgdXNlZCBmb3IgaW50ZXJhY3RpdmUgbmV1dHJhbGx5LXRvbmVkIGVsZW1lbnRzIHdpdGggbm9ybWFsIGVtcGhhc2lzIHRoYXQgYXJlIGhvdmVyZWQsIGZvY3VzZWQsIG9yIGFjdGl2ZS4gKi9cblx0LS13cGRzLWNvbG9yLXN0cm9rZS1pbnRlcmFjdGl2ZS1uZXV0cmFsLWRpc2FibGVkOiAjZDhkOGQ4OyAvKiBBY2Nlc3NpYmxlIHN0cm9rZSBjb2xvciB1c2VkIGZvciBpbnRlcmFjdGl2ZSBuZXV0cmFsbHktdG9uZWQgZWxlbWVudHMgd2l0aCBub3JtYWwgZW1waGFzaXMsIGluIHRoZWlyIGRpc2FibGVkIHN0YXRlLiAqL1xuXHQtLXdwZHMtY29sb3Itc3Ryb2tlLWludGVyYWN0aXZlLW5ldXRyYWwtc3Ryb25nOiAjNmM2YzZjOyAvKiBBY2Nlc3NpYmxlIHN0cm9rZSBjb2xvciB1c2VkIGZvciBpbnRlcmFjdGl2ZSBuZXV0cmFsbHktdG9uZWQgZWxlbWVudHMgd2l0aCBzdHJvbmcgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1zdHJva2Utc3VyZmFjZS1icmFuZDogI2EzYjFkNDsgLyogRGVjb3JhdGl2ZSBzdHJva2UgY29sb3IgdXNlZCB0byBkZWZpbmUgYnJhbmQtdG9uZWQgc3VyZmFjZSBib3VuZGFyaWVzIHdpdGggbm9ybWFsIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3Itc3Ryb2tlLXN1cmZhY2UtYnJhbmQtc3Ryb25nOiAjMzg1OGU5OyAvKiBEZWNvcmF0aXZlIHN0cm9rZSBjb2xvciB1c2VkIHRvIGRlZmluZSBicmFuZC10b25lZCBzdXJmYWNlIGJvdW5kYXJpZXMgd2l0aCBzdHJvbmcgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1zdHJva2Utc3VyZmFjZS1lcnJvcjogI2RhYTM5YjsgLyogRGVjb3JhdGl2ZSBzdHJva2UgY29sb3IgdXNlZCB0byBkZWZpbmUgZXJyb3ItdG9uZWQgc3VyZmFjZSBib3VuZGFyaWVzIHdpdGggbm9ybWFsIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3Itc3Ryb2tlLXN1cmZhY2UtZXJyb3Itc3Ryb25nOiAjY2MxODE4OyAvKiBEZWNvcmF0aXZlIHN0cm9rZSBjb2xvciB1c2VkIHRvIGRlZmluZSBlcnJvci10b25lZCBzdXJmYWNlIGJvdW5kYXJpZXMgd2l0aCBzdHJvbmcgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1zdHJva2Utc3VyZmFjZS1pbmZvOiAjOWZiY2RjOyAvKiBEZWNvcmF0aXZlIHN0cm9rZSBjb2xvciB1c2VkIHRvIGRlZmluZSBpbmZvLXRvbmVkIHN1cmZhY2UgYm91bmRhcmllcyB3aXRoIG5vcm1hbCBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLXN0cm9rZS1zdXJmYWNlLWluZm8tc3Ryb25nOiAjMDA2YmQ3OyAvKiBEZWNvcmF0aXZlIHN0cm9rZSBjb2xvciB1c2VkIHRvIGRlZmluZSBpbmZvLXRvbmVkIHN1cmZhY2UgYm91bmRhcmllcyB3aXRoIHN0cm9uZyBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLXN0cm9rZS1zdXJmYWNlLW5ldXRyYWw6ICNkOGQ4ZDg7IC8qIERlY29yYXRpdmUgc3Ryb2tlIGNvbG9yIHVzZWQgdG8gZGVmaW5lIG5ldXRyYWxseS10b25lZCBzdXJmYWNlIGJvdW5kYXJpZXMgd2l0aCBub3JtYWwgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1zdHJva2Utc3VyZmFjZS1uZXV0cmFsLXN0cm9uZzogIzhhOGE4YTsgLyogRGVjb3JhdGl2ZSBzdHJva2UgY29sb3IgdXNlZCB0byBkZWZpbmUgbmV1dHJhbGx5LXRvbmVkIHN1cmZhY2UgYm91bmRhcmllcyB3aXRoIHN0cm9uZyBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLXN0cm9rZS1zdXJmYWNlLW5ldXRyYWwtd2VhazogI2UwZTBlMDsgLyogRGVjb3JhdGl2ZSBzdHJva2UgY29sb3IgdXNlZCB0byBkZWZpbmUgbmV1dHJhbGx5LXRvbmVkIHN1cmZhY2UgYm91bmRhcmllcyB3aXRoIHdlYWsgZW1waGFzaXMuICovXG5cdC0td3Bkcy1jb2xvci1zdHJva2Utc3VyZmFjZS1zdWNjZXNzOiAjOGFjODk0OyAvKiBEZWNvcmF0aXZlIHN0cm9rZSBjb2xvciB1c2VkIHRvIGRlZmluZSBzdWNjZXNzLXRvbmVkIHN1cmZhY2UgYm91bmRhcmllcyB3aXRoIG5vcm1hbCBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLXN0cm9rZS1zdXJmYWNlLXN1Y2Nlc3Mtc3Ryb25nOiAjMDA3ZjMwOyAvKiBEZWNvcmF0aXZlIHN0cm9rZSBjb2xvciB1c2VkIHRvIGRlZmluZSBzdWNjZXNzLXRvbmVkIHN1cmZhY2UgYm91bmRhcmllcyB3aXRoIHN0cm9uZyBlbXBoYXNpcy4gKi9cblx0LS13cGRzLWNvbG9yLXN0cm9rZS1zdXJmYWNlLXdhcm5pbmc6ICNkMGIzODE7IC8qIERlY29yYXRpdmUgc3Ryb2tlIGNvbG9yIHVzZWQgdG8gZGVmaW5lIHdhcm5pbmctdG9uZWQgc3VyZmFjZSBib3VuZGFyaWVzIHdpdGggbm9ybWFsIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtY29sb3Itc3Ryb2tlLXN1cmZhY2Utd2FybmluZy1zdHJvbmc6ICM5MjYzMDA7IC8qIERlY29yYXRpdmUgc3Ryb2tlIGNvbG9yIHVzZWQgdG8gZGVmaW5lIHdhcm5pbmctdG9uZWQgc3VyZmFjZSBib3VuZGFyaWVzIHdpdGggc3Ryb25nIGVtcGhhc2lzLiAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLWJhc2U6IDRweDsgLyogQmFzZSBkaW1lbnNpb24gdW5pdCAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLWdhcC0yeHM6IDRweDsgLyogMnggZXh0cmEgc21hbGwgZ2FwICovXG5cdC0td3Bkcy1kaW1lbnNpb24tZ2FwLWxnOiAyNHB4OyAvKiBMYXJnZSBnYXAgKi9cblx0LS13cGRzLWRpbWVuc2lvbi1nYXAtbWQ6IDE2cHg7IC8qIE1lZGl1bSBnYXAgKi9cblx0LS13cGRzLWRpbWVuc2lvbi1nYXAtc206IDEycHg7IC8qIFNtYWxsIGdhcCAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLWdhcC14bDogNDBweDsgLyogRXh0cmEgbGFyZ2UgZ2FwICovXG5cdC0td3Bkcy1kaW1lbnNpb24tZ2FwLXhzOiA4cHg7IC8qIEV4dHJhIHNtYWxsIGdhcCAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLXBhZGRpbmctc3VyZmFjZS0yeHM6IDRweDsgLyogMnggZXh0cmEgc21hbGwgc3BhY2luZyBmb3Igc3VyZmFjZXMgKi9cblx0LS13cGRzLWRpbWVuc2lvbi1wYWRkaW5nLXN1cmZhY2UtbGc6IDMycHg7IC8qIExhcmdlIHNwYWNpbmcgZm9yIHN1cmZhY2VzICovXG5cdC0td3Bkcy1kaW1lbnNpb24tcGFkZGluZy1zdXJmYWNlLW1kOiAyNHB4OyAvKiBNZWRpdW0gc3BhY2luZyBmb3Igc3VyZmFjZXMgKi9cblx0LS13cGRzLWRpbWVuc2lvbi1wYWRkaW5nLXN1cmZhY2Utc206IDE2cHg7IC8qIFNtYWxsIHNwYWNpbmcgZm9yIHN1cmZhY2VzICovXG5cdC0td3Bkcy1kaW1lbnNpb24tcGFkZGluZy1zdXJmYWNlLXhzOiA4cHg7IC8qIEV4dHJhIHNtYWxsIHNwYWNpbmcgZm9yIHN1cmZhY2VzICovXG5cdC0td3Bkcy1lbGV2YXRpb24tbGFyZ2U6IDAgNXB4IDE1cHggMCAjMDAwMDAwMTQsIDAgMTVweCAyN3B4IDAgIzAwMDAwMDEyLFxuXHRcdDAgMzBweCAzNnB4IDAgIzAwMDAwMDBhLCAwIDUwcHggNDNweCAwICMwMDAwMDAwNTsgLyogRm9yIGNvbXBvbmVudHMgdGhhdCBjb25maXJtIGRlY2lzaW9ucyBvciBoYW5kbGUgbmVjZXNzYXJ5IGludGVycnVwdGlvbnMuIEV4YW1wbGU6IE1vZGFscy4gKi9cblx0LS13cGRzLWVsZXZhdGlvbi1tZWRpdW06IDAgMnB4IDNweCAwICMwMDAwMDAwZCwgMCA0cHggNXB4IDAgIzAwMDAwMDBhLFxuXHRcdDAgMTJweCAxMnB4IDAgIzAwMDAwMDA4LCAwIDE2cHggMTZweCAwICMwMDAwMDAwNTsgLyogRm9yIGNvbXBvbmVudHMgdGhhdCBvZmZlciBhZGRpdGlvbmFsIGFjdGlvbnMuIEV4YW1wbGU6IE1lbnVzLCBDb21tYW5kIFBhbGV0dGUgKi9cblx0LS13cGRzLWVsZXZhdGlvbi1zbWFsbDogMCAxcHggMnB4IDAgIzAwMDAwMDBkLCAwIDJweCAzcHggMCAjMDAwMDAwMGEsXG5cdFx0MCA2cHggNnB4IDAgIzAwMDAwMDA4LCAwIDhweCA4cHggMCAjMDAwMDAwMDU7IC8qIEZvciBjb21wb25lbnRzIHRoYXQgcHJvdmlkZSBjb250ZXh0dWFsIGZlZWRiYWNrIHdpdGhvdXQgYmVpbmcgaW50cnVzaXZlLiBHZW5lcmFsbHkgbm9uLWludGVycnVwdGl2ZS4gRXhhbXBsZTogVG9vbHRpcHMsIFNuYWNrYmFyLiAqL1xuXHQtLXdwZHMtZWxldmF0aW9uLXgtc21hbGw6IDAgMXB4IDFweCAwICMwMDAwMDAwOCwgMCAxcHggMnB4IDAgIzAwMDAwMDA1LFxuXHRcdDAgM3B4IDNweCAwICMwMDAwMDAwNSwgMCA0cHggNHB4IDAgIzAwMDAwMDAzOyAvKiBGb3Igc2VjdGlvbnMgYW5kIGNvbnRhaW5lcnMgdGhhdCBncm91cCByZWxhdGVkIGNvbnRlbnQgYW5kIGNvbnRyb2xzLCB3aGljaCBtYXkgb3ZlcmxhcCBvdGhlciBjb250ZW50LiBFeGFtcGxlOiBQcmV2aWV3IEZyYW1lLiAqL1xuXHQtLXdwZHMtZm9udC1mYW1pbHktYm9keTogLWFwcGxlLXN5c3RlbSwgc3lzdGVtLXVpLCAnU2Vnb2UgVUknLCAnUm9ib3RvJyxcblx0XHQnT3h5Z2VuLVNhbnMnLCAnVWJ1bnR1JywgJ0NhbnRhcmVsbCcsICdIZWx2ZXRpY2EgTmV1ZScsIHNhbnMtc2VyaWY7IC8qIEJvZHkgZm9udCBmYW1pbHkgKi9cblx0LS13cGRzLWZvbnQtZmFtaWx5LWhlYWRpbmc6IC1hcHBsZS1zeXN0ZW0sIHN5c3RlbS11aSwgJ1NlZ29lIFVJJywgJ1JvYm90bycsXG5cdFx0J094eWdlbi1TYW5zJywgJ1VidW50dScsICdDYW50YXJlbGwnLCAnSGVsdmV0aWNhIE5ldWUnLCBzYW5zLXNlcmlmOyAvKiBIZWFkaW5ncyBmb250IGZhbWlseSAqL1xuXHQtLXdwZHMtZm9udC1mYW1pbHktbW9ubzogJ01lbmxvJywgJ0NvbnNvbGFzJywgbW9uYWNvLCBtb25vc3BhY2U7IC8qIE1vbm9zcGFjZSBmb250IGZhbWlseSAqL1xuXHQtLXdwZHMtZm9udC1saW5lLWhlaWdodC0yeC1sYXJnZTogNDBweDsgLyogMlggbGFyZ2UgbGluZSBoZWlnaHQgKi9cblx0LS13cGRzLWZvbnQtbGluZS1oZWlnaHQtbGFyZ2U6IDI4cHg7IC8qIExhcmdlIGxpbmUgaGVpZ2h0ICovXG5cdC0td3Bkcy1mb250LWxpbmUtaGVpZ2h0LW1lZGl1bTogMjRweDsgLyogTWVkaXVtIGxpbmUgaGVpZ2h0ICovXG5cdC0td3Bkcy1mb250LWxpbmUtaGVpZ2h0LXNtYWxsOiAyMHB4OyAvKiBTbWFsbCBsaW5lIGhlaWdodCAqL1xuXHQtLXdwZHMtZm9udC1saW5lLWhlaWdodC14LWxhcmdlOiAzMnB4OyAvKiBFeHRyYSBsYXJnZSBsaW5lIGhlaWdodCAqL1xuXHQtLXdwZHMtZm9udC1saW5lLWhlaWdodC14LXNtYWxsOiAxNnB4OyAvKiBFeHRyYSBzbWFsbCBsaW5lIGhlaWdodCAqL1xuXHQtLXdwZHMtZm9udC1zaXplLTJ4LWxhcmdlOiAzMnB4OyAvKiAyWCBsYXJnZSBmb250IHNpemUgKi9cblx0LS13cGRzLWZvbnQtc2l6ZS1sYXJnZTogMTVweDsgLyogTGFyZ2UgZm9udCBzaXplICovXG5cdC0td3Bkcy1mb250LXNpemUtbWVkaXVtOiAxM3B4OyAvKiBNZWRpdW0gZm9udCBzaXplICovXG5cdC0td3Bkcy1mb250LXNpemUtc21hbGw6IDEycHg7IC8qIFNtYWxsIGZvbnQgc2l6ZSAqL1xuXHQtLXdwZHMtZm9udC1zaXplLXgtbGFyZ2U6IDIwcHg7IC8qIEV4dHJhIGxhcmdlIGZvbnQgc2l6ZSAqL1xuXHQtLXdwZHMtZm9udC1zaXplLXgtc21hbGw6IDExcHg7IC8qIEV4dHJhIHNtYWxsIGZvbnQgc2l6ZSAqL1xufVxuXG5bZGF0YS13cGRzLXRoZW1lLXByb3ZpZGVyLWlkXVtkYXRhLXdwZHMtZGVuc2l0eT0nZGVmYXVsdCddIHtcblx0LS13cGRzLWRpbWVuc2lvbi1iYXNlOiA0cHg7IC8qIEJhc2UgZGltZW5zaW9uIHVuaXQgKi9cblx0LS13cGRzLWRpbWVuc2lvbi1nYXAtMnhzOiA0cHg7IC8qIDJ4IGV4dHJhIHNtYWxsIGdhcCAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLWdhcC1sZzogMjRweDsgLyogTGFyZ2UgZ2FwICovXG5cdC0td3Bkcy1kaW1lbnNpb24tZ2FwLW1kOiAxNnB4OyAvKiBNZWRpdW0gZ2FwICovXG5cdC0td3Bkcy1kaW1lbnNpb24tZ2FwLXNtOiAxMnB4OyAvKiBTbWFsbCBnYXAgKi9cblx0LS13cGRzLWRpbWVuc2lvbi1nYXAteGw6IDQwcHg7IC8qIEV4dHJhIGxhcmdlIGdhcCAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLWdhcC14czogOHB4OyAvKiBFeHRyYSBzbWFsbCBnYXAgKi9cblx0LS13cGRzLWRpbWVuc2lvbi1wYWRkaW5nLXN1cmZhY2UtMnhzOiA0cHg7IC8qIDJ4IGV4dHJhIHNtYWxsIHNwYWNpbmcgZm9yIHN1cmZhY2VzICovXG5cdC0td3Bkcy1kaW1lbnNpb24tcGFkZGluZy1zdXJmYWNlLWxnOiAzMnB4OyAvKiBMYXJnZSBzcGFjaW5nIGZvciBzdXJmYWNlcyAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLXBhZGRpbmctc3VyZmFjZS1tZDogMjRweDsgLyogTWVkaXVtIHNwYWNpbmcgZm9yIHN1cmZhY2VzICovXG5cdC0td3Bkcy1kaW1lbnNpb24tcGFkZGluZy1zdXJmYWNlLXNtOiAxNnB4OyAvKiBTbWFsbCBzcGFjaW5nIGZvciBzdXJmYWNlcyAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLXBhZGRpbmctc3VyZmFjZS14czogOHB4OyAvKiBFeHRyYSBzbWFsbCBzcGFjaW5nIGZvciBzdXJmYWNlcyAqL1xufVxuXG5bZGF0YS13cGRzLXRoZW1lLXByb3ZpZGVyLWlkXVtkYXRhLXdwZHMtZGVuc2l0eT0nY29tcGFjdCddIHtcblx0LS13cGRzLWRpbWVuc2lvbi1nYXAtMnhzOiA0cHg7IC8qIDJ4IGV4dHJhIHNtYWxsIGdhcCAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLWdhcC1sZzogMjBweDsgLyogTGFyZ2UgZ2FwICovXG5cdC0td3Bkcy1kaW1lbnNpb24tZ2FwLW1kOiAxMnB4OyAvKiBNZWRpdW0gZ2FwICovXG5cdC0td3Bkcy1kaW1lbnNpb24tZ2FwLXNtOiA4cHg7IC8qIFNtYWxsIGdhcCAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLWdhcC14bDogMzJweDsgLyogRXh0cmEgbGFyZ2UgZ2FwICovXG5cdC0td3Bkcy1kaW1lbnNpb24tZ2FwLXhzOiA0cHg7IC8qIEV4dHJhIHNtYWxsIGdhcCAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLXBhZGRpbmctc3VyZmFjZS0yeHM6IDRweDsgLyogMnggZXh0cmEgc21hbGwgc3BhY2luZyBmb3Igc3VyZmFjZXMgKi9cblx0LS13cGRzLWRpbWVuc2lvbi1wYWRkaW5nLXN1cmZhY2UtbGc6IDI0cHg7IC8qIExhcmdlIHNwYWNpbmcgZm9yIHN1cmZhY2VzICovXG5cdC0td3Bkcy1kaW1lbnNpb24tcGFkZGluZy1zdXJmYWNlLW1kOiAyMHB4OyAvKiBNZWRpdW0gc3BhY2luZyBmb3Igc3VyZmFjZXMgKi9cblx0LS13cGRzLWRpbWVuc2lvbi1wYWRkaW5nLXN1cmZhY2Utc206IDEycHg7IC8qIFNtYWxsIHNwYWNpbmcgZm9yIHN1cmZhY2VzICovXG5cdC0td3Bkcy1kaW1lbnNpb24tcGFkZGluZy1zdXJmYWNlLXhzOiA0cHg7IC8qIEV4dHJhIHNtYWxsIHNwYWNpbmcgZm9yIHN1cmZhY2VzICovXG59XG5cbltkYXRhLXdwZHMtdGhlbWUtcHJvdmlkZXItaWRdW2RhdGEtd3Bkcy1kZW5zaXR5PSdjb21mb3J0YWJsZSddIHtcblx0LS13cGRzLWRpbWVuc2lvbi1nYXAtMnhzOiA4cHg7IC8qIDJ4IGV4dHJhIHNtYWxsIGdhcCAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLWdhcC1sZzogMzJweDsgLyogTGFyZ2UgZ2FwICovXG5cdC0td3Bkcy1kaW1lbnNpb24tZ2FwLW1kOiAyMHB4OyAvKiBNZWRpdW0gZ2FwICovXG5cdC0td3Bkcy1kaW1lbnNpb24tZ2FwLXNtOiAxNnB4OyAvKiBTbWFsbCBnYXAgKi9cblx0LS13cGRzLWRpbWVuc2lvbi1nYXAteGw6IDQ4cHg7IC8qIEV4dHJhIGxhcmdlIGdhcCAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLWdhcC14czogMTJweDsgLyogRXh0cmEgc21hbGwgZ2FwICovXG5cdC0td3Bkcy1kaW1lbnNpb24tcGFkZGluZy1zdXJmYWNlLTJ4czogOHB4OyAvKiAyeCBleHRyYSBzbWFsbCBzcGFjaW5nIGZvciBzdXJmYWNlcyAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLXBhZGRpbmctc3VyZmFjZS1sZzogNDBweDsgLyogTGFyZ2Ugc3BhY2luZyBmb3Igc3VyZmFjZXMgKi9cblx0LS13cGRzLWRpbWVuc2lvbi1wYWRkaW5nLXN1cmZhY2UtbWQ6IDMycHg7IC8qIE1lZGl1bSBzcGFjaW5nIGZvciBzdXJmYWNlcyAqL1xuXHQtLXdwZHMtZGltZW5zaW9uLXBhZGRpbmctc3VyZmFjZS1zbTogMjBweDsgLyogU21hbGwgc3BhY2luZyBmb3Igc3VyZmFjZXMgKi9cblx0LS13cGRzLWRpbWVuc2lvbi1wYWRkaW5nLXN1cmZhY2UteHM6IDEycHg7IC8qIEV4dHJhIHNtYWxsIHNwYWNpbmcgZm9yIHN1cmZhY2VzICovXG59XG5cbkBtZWRpYSAoIC13ZWJraXQtbWluLWRldmljZS1waXhlbC1yYXRpbzogMiApLCAoIG1pbi1yZXNvbHV0aW9uOiAxOTJkcGkgKSB7XG5cdDpyb290IHtcblx0XHQtLXdwZHMtYm9yZGVyLXdpZHRoLWludGVyYWN0aXZlLWZvY3VzOiAxLjVweDsgLyogQm9yZGVyIHdpZHRoIGZvciBmb2N1cyByaW5nICovXG5cdH1cbn1cbiIsIi8qKlxuICogQ29sb3JzXG4gKi9cbi8qKlxuICogU0NTUyBWYXJpYWJsZXMuXG4gKlxuICogUGxlYXNlIHVzZSB2YXJpYWJsZXMgZnJvbSB0aGlzIHNoZWV0IHRvIGVuc3VyZSBjb25zaXN0ZW5jeSBhY3Jvc3MgdGhlIFVJLlxuICogRG9uJ3QgYWRkIHRvIHRoaXMgc2hlZXQgdW5sZXNzIHlvdSdyZSBwcmV0dHkgc3VyZSB0aGUgdmFsdWUgd2lsbCBiZSByZXVzZWQgaW4gbWFueSBwbGFjZXMuXG4gKiBGb3IgZXhhbXBsZSwgZG9uJ3QgYWRkIHJ1bGVzIHRvIHRoaXMgc2hlZXQgdGhhdCBhZmZlY3QgYmxvY2sgdmlzdWFscy4gSXQncyBwdXJlbHkgZm9yIFVJLlxuICovXG4vKipcbiAqIEZvbnRzICYgYmFzaWMgdmFyaWFibGVzLlxuICovXG4vKipcbiAqIFR5cG9ncmFwaHlcbiAqL1xuLyoqXG4gKiBHcmlkIFN5c3RlbS5cbiAqIGh0dHBzOi8vbWFrZS53b3JkcHJlc3Mub3JnL2Rlc2lnbi8yMDE5LzEwLzMxL3Byb3Bvc2FsLWEtY29uc2lzdGVudC1zcGFjaW5nLXN5c3RlbS1mb3Itd29yZHByZXNzL1xuICovXG4vKipcbiAqIFJhZGl1cyBzY2FsZS5cbiAqL1xuLyoqXG4gKiBFbGV2YXRpb24gc2NhbGUuXG4gKi9cbi8qKlxuICogRGltZW5zaW9ucy5cbiAqL1xuLyoqXG4gKiBNb2JpbGUgc3BlY2lmaWMgc3R5bGVzXG4gKi9cbi8qKlxuICogRWRpdG9yIHN0eWxlcy5cbiAqL1xuLyoqXG4gKiBCbG9jayAmIEVkaXRvciBVSS5cbiAqL1xuLyoqXG4gKiBCbG9jayBwYWRkaW5ncy5cbiAqL1xuLyoqXG4gKiBSZWFjdCBOYXRpdmUgc3BlY2lmaWMuXG4gKiBUaGVzZSB2YXJpYWJsZXMgZG8gbm90IGFwcGVhciB0byBiZSB1c2VkIGFueXdoZXJlIGVsc2UuXG4gKi9cbi8qKlxuICogVHlwb2dyYXBoeVxuICovXG4vKipcbiAqIEJyZWFrcG9pbnRzICYgTWVkaWEgUXVlcmllc1xuICovXG4vKipcbiogIENvbnZlcnRzIGEgaGV4IHZhbHVlIGludG8gdGhlIHJnYiBlcXVpdmFsZW50LlxuKlxuKiBAcGFyYW0ge3N0cmluZ30gaGV4IC0gdGhlIGhleGFkZWNpbWFsIHZhbHVlIHRvIGNvbnZlcnRcbiogQHJldHVybiB7c3RyaW5nfSBjb21tYSBzZXBhcmF0ZWQgcmdiIHZhbHVlc1xuKi9cbi8qKlxuICogTG9uZyBjb250ZW50IGZhZGUgbWl4aW5cbiAqXG4gKiBDcmVhdGVzIGEgZmFkaW5nIG92ZXJsYXkgdG8gc2lnbmlmeSB0aGF0IHRoZSBjb250ZW50IGlzIGxvbmdlclxuICogdGhhbiB0aGUgc3BhY2UgYWxsb3dzLlxuICovXG4vKipcbiAqIEJyZWFrcG9pbnQgbWl4aW5zXG4gKi9cbi8qKlxuICogRm9jdXMgc3R5bGVzLlxuICovXG4vKipcbiAqIEFwcGxpZXMgZWRpdG9yIGxlZnQgcG9zaXRpb24gdG8gdGhlIHNlbGVjdG9yIHBhc3NlZCBhcyBhcmd1bWVudFxuICovXG4vKipcbiAqIFN0eWxlcyB0aGF0IGFyZSByZXVzZWQgdmVyYmF0aW0gaW4gYSBmZXcgcGxhY2VzXG4gKi9cbi8qKlxuICogQWxsb3dzIHVzZXJzIHRvIG9wdC1vdXQgb2YgYW5pbWF0aW9ucyB2aWEgT1MtbGV2ZWwgcHJlZmVyZW5jZXMuXG4gKi9cbi8qKlxuICogUmVzZXQgZGVmYXVsdCBzdHlsZXMgZm9yIEphdmFTY3JpcHQgVUkgYmFzZWQgcGFnZXMuXG4gKiBUaGlzIGlzIGEgV1AtYWRtaW4gYWdub3N0aWMgcmVzZXRcbiAqL1xuLyoqXG4gKiBSZXNldCB0aGUgV1AgQWRtaW4gcGFnZSBzdHlsZXMgZm9yIEd1dGVuYmVyZy1saWtlIHBhZ2VzLlxuICovXG4uYWRtaW4tdWktcGFnZSB7XG4gIGRpc3BsYXk6IGZsZXg7XG4gIGhlaWdodDogMTAwJTtcbiAgYmFja2dyb3VuZC1jb2xvcjogI2ZmZjtcbiAgY29sb3I6ICMyZjJmMmY7XG4gIHBvc2l0aW9uOiByZWxhdGl2ZTtcbiAgei1pbmRleDogMTtcbiAgZmxleC1mbG93OiBjb2x1bW47XG59XG5cbi5hZG1pbi11aS1wYWdlX19oZWFkZXIge1xuICBwYWRkaW5nOiAxNnB4IDI0cHg7XG4gIGJvcmRlci1ib3R0b206IDFweCBzb2xpZCAjZjBmMGYwO1xuICBiYWNrZ3JvdW5kOiAjZmZmO1xuICBwb3NpdGlvbjogc3RpY2t5O1xuICB0b3A6IDA7XG4gIHotaW5kZXg6IDE7XG59XG5cbi5hZG1pbi11aS1wYWdlX19zaWRlYmFyLXRvZ2dsZS1zbG90OmVtcHR5IHtcbiAgZGlzcGxheTogbm9uZTtcbn1cblxuLmFkbWluLXVpLXBhZ2VfX2hlYWRlci1zdWJ0aXRsZSB7XG4gIHBhZGRpbmctYmxvY2stZW5kOiA4cHg7XG4gIGNvbG9yOiAjNzU3NTc1O1xuICBmb250LWZhbWlseTogLWFwcGxlLXN5c3RlbSwgXCJzeXN0ZW0tdWlcIiwgXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCwgXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmO1xuICBmb250LXdlaWdodDogNDAwO1xuICBmb250LXNpemU6IDEzcHg7XG4gIGxpbmUtaGVpZ2h0OiAyMHB4O1xuICBtYXJnaW46IDA7XG59XG5cbi5hZG1pbi11aS1wYWdlX19jb250ZW50IHtcbiAgZmxleC1ncm93OiAxO1xuICBvdmVyZmxvdzogYXV0bztcbiAgZGlzcGxheTogZmxleDtcbiAgZmxleC1kaXJlY3Rpb246IGNvbHVtbjtcbn1cbi5hZG1pbi11aS1wYWdlX19jb250ZW50Lmhhcy1wYWRkaW5nIHtcbiAgcGFkZGluZzogMTZweCAyNHB4O1xufVxuXG4uc2hvdy1pY29uLWxhYmVscyAuYWRtaW4tdWktcGFnZV9faGVhZGVyLWFjdGlvbnMgLmNvbXBvbmVudHMtYnV0dG9uLmhhcy1pY29uIHtcbiAgd2lkdGg6IGF1dG87XG4gIHBhZGRpbmc6IDAgOHB4O1xufVxuLnNob3ctaWNvbi1sYWJlbHMgLmFkbWluLXVpLXBhZ2VfX2hlYWRlci1hY3Rpb25zIC5jb21wb25lbnRzLWJ1dHRvbi5oYXMtaWNvbiBzdmcge1xuICBkaXNwbGF5OiBub25lO1xufVxuLnNob3ctaWNvbi1sYWJlbHMgLmFkbWluLXVpLXBhZ2VfX2hlYWRlci1hY3Rpb25zIC5jb21wb25lbnRzLWJ1dHRvbi5oYXMtaWNvbjo6YWZ0ZXIge1xuICBjb250ZW50OiBhdHRyKGFyaWEtbGFiZWwpO1xuICBmb250LXNpemU6IDEycHg7XG59XG5cbi5hZG1pbi11aS1icmVhZGNydW1ic19fbGlzdCB7XG4gIGxpc3Qtc3R5bGU6IG5vbmU7XG4gIHBhZGRpbmc6IDA7XG4gIG1hcmdpbjogMDtcbiAgZ2FwOiAwO1xuICBmb250LXNpemU6IDE1cHg7XG4gIG1pbi1oZWlnaHQ6IDMycHg7XG4gIGZvbnQtd2VpZ2h0OiA1MDA7XG59XG4uYWRtaW4tdWktYnJlYWRjcnVtYnNfX2xpc3QgbGk6bm90KDpsYXN0LWNoaWxkKTo6YWZ0ZXIge1xuICBjb250ZW50OiBcIi9cIjtcbiAgbWFyZ2luOiAwIDhweDtcbn1cbi5hZG1pbi11aS1icmVhZGNydW1ic19fbGlzdCBoMSB7XG4gIGZvbnQtc2l6ZTogaW5oZXJpdDtcbiAgbGluZS1oZWlnaHQ6IGluaGVyaXQ7XG59IiwiLyoqXG4gKiBUeXBvZ3JhcGh5XG4gKi9cblxuQHVzZSBcInNhc3M6Y29sb3JcIjtcbkB1c2UgXCJzYXNzOm1hdGhcIjtcbkB1c2UgXCIuL3ZhcmlhYmxlc1wiO1xuQHVzZSBcIi4vY29sb3JzXCI7XG5AdXNlIFwiLi9icmVha3BvaW50c1wiO1xuQHVzZSBcIi4vZnVuY3Rpb25zXCI7XG5AdXNlIFwiLi9sb25nLWNvbnRlbnQtZmFkZVwiO1xuXG5AbWl4aW4gX3RleHQtaGVhZGluZygpIHtcblx0Zm9udC1mYW1pbHk6IHZhcmlhYmxlcy4kZm9udC1mYW1pbHktaGVhZGluZ3M7XG5cdGZvbnQtd2VpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtd2VpZ2h0LW1lZGl1bTtcbn1cblxuQG1peGluIF90ZXh0LWJvZHkoKSB7XG5cdGZvbnQtZmFtaWx5OiB2YXJpYWJsZXMuJGZvbnQtZmFtaWx5LWJvZHk7XG5cdGZvbnQtd2VpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtd2VpZ2h0LXJlZ3VsYXI7XG59XG5cbkBtaXhpbiBoZWFkaW5nLXNtYWxsKCkge1xuXHRAaW5jbHVkZSBfdGV4dC1oZWFkaW5nKCk7XG5cdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRmb250LXNpemUteC1zbWFsbDtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC14LXNtYWxsO1xufVxuXG5AbWl4aW4gaGVhZGluZy1tZWRpdW0oKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWhlYWRpbmcoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS1tZWRpdW07XG5cdGxpbmUtaGVpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtbGluZS1oZWlnaHQtc21hbGw7XG59XG5cbkBtaXhpbiBoZWFkaW5nLWxhcmdlKCkge1xuXHRAaW5jbHVkZSBfdGV4dC1oZWFkaW5nKCk7XG5cdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRmb250LXNpemUtbGFyZ2U7XG5cdGxpbmUtaGVpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtbGluZS1oZWlnaHQtc21hbGw7XG59XG5cbkBtaXhpbiBoZWFkaW5nLXgtbGFyZ2UoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWhlYWRpbmcoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS14LWxhcmdlO1xuXHRsaW5lLWhlaWdodDogdmFyaWFibGVzLiRmb250LWxpbmUtaGVpZ2h0LW1lZGl1bTtcbn1cblxuQG1peGluIGhlYWRpbmctMngtbGFyZ2UoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWhlYWRpbmcoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS0yeC1sYXJnZTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC0yeC1sYXJnZTtcbn1cblxuQG1peGluIGJvZHktc21hbGwoKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWJvZHkoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS1zbWFsbDtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC14LXNtYWxsO1xufVxuXG5AbWl4aW4gYm9keS1tZWRpdW0oKSB7XG5cdEBpbmNsdWRlIF90ZXh0LWJvZHkoKTtcblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGZvbnQtc2l6ZS1tZWRpdW07XG5cdGxpbmUtaGVpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtbGluZS1oZWlnaHQtc21hbGw7XG59XG5cbkBtaXhpbiBib2R5LWxhcmdlKCkge1xuXHRAaW5jbHVkZSBfdGV4dC1ib2R5KCk7XG5cdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRmb250LXNpemUtbGFyZ2U7XG5cdGxpbmUtaGVpZ2h0OiB2YXJpYWJsZXMuJGZvbnQtbGluZS1oZWlnaHQtbWVkaXVtO1xufVxuXG5AbWl4aW4gYm9keS14LWxhcmdlKCkge1xuXHRAaW5jbHVkZSBfdGV4dC1ib2R5KCk7XG5cdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRmb250LXNpemUteC1sYXJnZTtcblx0bGluZS1oZWlnaHQ6IHZhcmlhYmxlcy4kZm9udC1saW5lLWhlaWdodC14LWxhcmdlO1xufVxuXG4vKipcbiAqIEJyZWFrcG9pbnQgbWl4aW5zXG4gKi9cblxuQG1peGluIGJyZWFrLXhodWdlKCkge1xuXHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay14aHVnZSkgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbkBtaXhpbiBicmVhay1odWdlKCkge1xuXHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay1odWdlKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLXdpZGUoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLXdpZGUpIH0pIHtcblx0XHRAY29udGVudDtcblx0fVxufVxuXG5AbWl4aW4gYnJlYWsteGxhcmdlKCkge1xuXHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay14bGFyZ2UpIH0pIHtcblx0XHRAY29udGVudDtcblx0fVxufVxuXG5AbWl4aW4gYnJlYWstbGFyZ2UoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLWxhcmdlKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLW1lZGl1bSgpIHtcblx0QG1lZGlhIChtaW4td2lkdGg6ICN7IChicmVha3BvaW50cy4kYnJlYWstbWVkaXVtKSB9KSB7XG5cdFx0QGNvbnRlbnQ7XG5cdH1cbn1cblxuQG1peGluIGJyZWFrLXNtYWxsKCkge1xuXHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay1zbWFsbCkgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbkBtaXhpbiBicmVhay1tb2JpbGUoKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLW1vYmlsZSkgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbkBtaXhpbiBicmVhay16b29tZWQtaW4oKSB7XG5cdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLXpvb21lZC1pbikgfSkge1xuXHRcdEBjb250ZW50O1xuXHR9XG59XG5cbi8qKlxuICogRm9jdXMgc3R5bGVzLlxuICovXG5cbkBtaXhpbiBibG9jay10b29sYmFyLWJ1dHRvbi1zdHlsZV9fZm9jdXMoKSB7XG5cdGJveC1zaGFkb3c6IGluc2V0IDAgMCAwIHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoIGNvbG9ycy4kd2hpdGUsIDAgMCAwIHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXG5cdC8vIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlIHdpbGwgc2hvdyB0aGlzIG91dGxpbmUsIGJ1dCBub3QgdGhlIGJveC1zaGFkb3cuXG5cdG91dGxpbmU6IDJweCBzb2xpZCB0cmFuc3BhcmVudDtcbn1cblxuLy8gVGFicywgSW5wdXRzLCBTcXVhcmUgYnV0dG9ucy5cbkBtaXhpbiBpbnB1dC1zdHlsZV9fbmV1dHJhbCgpIHtcblx0Ym94LXNoYWRvdzogMCAwIDAgdHJhbnNwYXJlbnQ7XG5cdGJvcmRlci1yYWRpdXM6IHZhcmlhYmxlcy4kcmFkaXVzLXNtYWxsO1xuXHRib3JkZXI6IHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoIHNvbGlkIGNvbG9ycy4kZ3JheS02MDA7XG5cblx0QG1lZGlhIG5vdCAocHJlZmVycy1yZWR1Y2VkLW1vdGlvbikge1xuXHRcdHRyYW5zaXRpb246IGJveC1zaGFkb3cgMC4xcyBsaW5lYXI7XG5cdH1cbn1cblxuXG5AbWl4aW4gaW5wdXQtc3R5bGVfX2ZvY3VzKCRhY2NlbnQtY29sb3I6IHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKSkge1xuXHRib3JkZXItY29sb3I6ICRhY2NlbnQtY29sb3I7XG5cdC8vIEV4cGFuZCB0aGUgZGVmYXVsdCBib3JkZXIgZm9jdXMgc3R5bGUgYnkgLjVweCB0byBiZSBhIHRvdGFsIG9mIDEuNXB4LlxuXHRib3gtc2hhZG93OiAwIDAgMCAwLjVweCAkYWNjZW50LWNvbG9yO1xuXHQvLyBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZSB3aWxsIHNob3cgdGhpcyBvdXRsaW5lLCBidXQgbm90IHRoZSBib3gtc2hhZG93LlxuXHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQ7XG59XG5cbkBtaXhpbiBidXR0b24tc3R5bGVfX2ZvY3VzKCkge1xuXHRib3gtc2hhZG93OiAwIDAgMCB2YXIoLS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXMpIHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblxuXHQvLyBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZSB3aWxsIHNob3cgdGhpcyBvdXRsaW5lLCBidXQgbm90IHRoZSBib3gtc2hhZG93LlxuXHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQ7XG59XG5cblxuQG1peGluIGJ1dHRvbi1zdHlsZS1vdXRzZXRfX2ZvY3VzKCRmb2N1cy1jb2xvcikge1xuXHRib3gtc2hhZG93OiAwIDAgMCB2YXIoLS13cC1hZG1pbi1ib3JkZXItd2lkdGgtZm9jdXMpIGNvbG9ycy4kd2hpdGUsIDAgMCAwIGNhbGMoMiAqIHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykpICRmb2N1cy1jb2xvcjtcblxuXHQvLyBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZSB3aWxsIHNob3cgdGhpcyBvdXRsaW5lLCBidXQgbm90IHRoZSBib3gtc2hhZG93LlxuXHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQ7XG5cdG91dGxpbmUtb2Zmc2V0OiAycHg7XG59XG5cblxuLyoqXG4gKiBBcHBsaWVzIGVkaXRvciBsZWZ0IHBvc2l0aW9uIHRvIHRoZSBzZWxlY3RvciBwYXNzZWQgYXMgYXJndW1lbnRcbiAqL1xuXG5AbWl4aW4gZWRpdG9yLWxlZnQoJHNlbGVjdG9yKSB7XG5cdCN7JHNlbGVjdG9yfSB7IC8qIFNldCBsZWZ0IHBvc2l0aW9uIHdoZW4gYXV0by1mb2xkIGlzIG5vdCBvbiB0aGUgYm9keSBlbGVtZW50LiAqL1xuXHRcdGxlZnQ6IDA7XG5cblx0XHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay1tZWRpdW0gKyAxKSB9KSB7XG5cdFx0XHRsZWZ0OiB2YXJpYWJsZXMuJGFkbWluLXNpZGViYXItd2lkdGg7XG5cdFx0fVxuXHR9XG5cblx0LmF1dG8tZm9sZCAjeyRzZWxlY3Rvcn0geyAvKiBBdXRvIGZvbGQgaXMgd2hlbiBvbiBzbWFsbGVyIGJyZWFrcG9pbnRzLCBuYXYgbWVudSBhdXRvIGNvbGxhcHNlcy4gKi9cblx0XHRAbWVkaWEgKG1pbi13aWR0aDogI3sgKGJyZWFrcG9pbnRzLiRicmVhay1tZWRpdW0gKyAxKSB9KSB7XG5cdFx0XHRsZWZ0OiB2YXJpYWJsZXMuJGFkbWluLXNpZGViYXItd2lkdGgtY29sbGFwc2VkO1xuXHRcdH1cblxuXHRcdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLWxhcmdlICsgMSkgfSkge1xuXHRcdFx0bGVmdDogdmFyaWFibGVzLiRhZG1pbi1zaWRlYmFyLXdpZHRoO1xuXHRcdH1cblx0fVxuXG5cdC8qIFNpZGViYXIgbWFudWFsbHkgY29sbGFwc2VkLiAqL1xuXHQuZm9sZGVkICN7JHNlbGVjdG9yfSB7XG5cdFx0bGVmdDogMDtcblxuXHRcdEBtZWRpYSAobWluLXdpZHRoOiAjeyAoYnJlYWtwb2ludHMuJGJyZWFrLW1lZGl1bSArIDEpIH0pIHtcblx0XHRcdGxlZnQ6IHZhcmlhYmxlcy4kYWRtaW4tc2lkZWJhci13aWR0aC1jb2xsYXBzZWQ7XG5cdFx0fVxuXHR9XG5cblx0Ym9keS5pcy1mdWxsc2NyZWVuLW1vZGUgI3skc2VsZWN0b3J9IHtcblx0XHRsZWZ0OiAwICFpbXBvcnRhbnQ7XG5cdH1cbn1cblxuLyoqXG4gKiBTdHlsZXMgdGhhdCBhcmUgcmV1c2VkIHZlcmJhdGltIGluIGEgZmV3IHBsYWNlc1xuICovXG5cbi8vIFRoZXNlIGFyZSBhZGRpdGlvbmFsIHN0eWxlcyBmb3IgYWxsIGNhcHRpb25zLCB3aGVuIHRoZSB0aGVtZSBvcHRzIGluIHRvIGJsb2NrIHN0eWxlcy5cbkBtaXhpbiBjYXB0aW9uLXN0eWxlKCkge1xuXHRtYXJnaW4tdG9wOiAwLjVlbTtcblx0bWFyZ2luLWJvdHRvbTogMWVtO1xufVxuXG5AbWl4aW4gY2FwdGlvbi1zdHlsZS10aGVtZSgpIHtcblx0Y29sb3I6ICM1NTU7XG5cdGZvbnQtc2l6ZTogdmFyaWFibGVzLiRkZWZhdWx0LWZvbnQtc2l6ZTtcblx0dGV4dC1hbGlnbjogY2VudGVyO1xuXG5cdC5pcy1kYXJrLXRoZW1lICYge1xuXHRcdGNvbG9yOiBjb2xvcnMuJGxpZ2h0LWdyYXktcGxhY2Vob2xkZXI7XG5cdH1cbn1cblxuLyoqXG4gKiBBbGxvd3MgdXNlcnMgdG8gb3B0LW91dCBvZiBhbmltYXRpb25zIHZpYSBPUy1sZXZlbCBwcmVmZXJlbmNlcy5cbiAqL1xuXG5AbWl4aW4gcmVkdWNlLW1vdGlvbigkcHJvcGVydHk6IFwiXCIpIHtcblxuXHRAaWYgJHByb3BlcnR5ID09IFwidHJhbnNpdGlvblwiIHtcblx0XHRAbWVkaWEgKHByZWZlcnMtcmVkdWNlZC1tb3Rpb246IHJlZHVjZSkge1xuXHRcdFx0dHJhbnNpdGlvbi1kdXJhdGlvbjogMHM7XG5cdFx0XHR0cmFuc2l0aW9uLWRlbGF5OiAwcztcblx0XHR9XG5cdH0gQGVsc2UgaWYgJHByb3BlcnR5ID09IFwiYW5pbWF0aW9uXCIge1xuXHRcdEBtZWRpYSAocHJlZmVycy1yZWR1Y2VkLW1vdGlvbjogcmVkdWNlKSB7XG5cdFx0XHRhbmltYXRpb24tZHVyYXRpb246IDFtcztcblx0XHRcdGFuaW1hdGlvbi1kZWxheTogMHM7XG5cdFx0fVxuXHR9IEBlbHNlIHtcblx0XHRAbWVkaWEgKHByZWZlcnMtcmVkdWNlZC1tb3Rpb246IHJlZHVjZSkge1xuXHRcdFx0dHJhbnNpdGlvbi1kdXJhdGlvbjogMHM7XG5cdFx0XHR0cmFuc2l0aW9uLWRlbGF5OiAwcztcblx0XHRcdGFuaW1hdGlvbi1kdXJhdGlvbjogMW1zO1xuXHRcdFx0YW5pbWF0aW9uLWRlbGF5OiAwcztcblx0XHR9XG5cdH1cbn1cblxuQG1peGluIGlucHV0LWNvbnRyb2woJGFjY2VudC1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpKSB7XG5cdGZvbnQtZmFtaWx5OiB2YXJpYWJsZXMuJGRlZmF1bHQtZm9udDtcblx0cGFkZGluZzogNnB4IDhweDtcblx0LyogRm9udHMgc21hbGxlciB0aGFuIDE2cHggY2F1c2VzIG1vYmlsZSBzYWZhcmkgdG8gem9vbS4gKi9cblx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJG1vYmlsZS10ZXh0LW1pbi1mb250LXNpemU7XG5cdC8qIE92ZXJyaWRlIGNvcmUgbGluZS1oZWlnaHQuIFRvIGJlIHJldmlld2VkLiAqL1xuXHRsaW5lLWhlaWdodDogbm9ybWFsO1xuXHRAaW5jbHVkZSBpbnB1dC1zdHlsZV9fbmV1dHJhbCgpO1xuXG5cdEBpbmNsdWRlIGJyZWFrLXNtYWxsIHtcblx0XHRmb250LXNpemU6IHZhcmlhYmxlcy4kZGVmYXVsdC1mb250LXNpemU7XG5cdFx0LyogT3ZlcnJpZGUgY29yZSBsaW5lLWhlaWdodC4gVG8gYmUgcmV2aWV3ZWQuICovXG5cdFx0bGluZS1oZWlnaHQ6IG5vcm1hbDtcblx0fVxuXG5cdCY6Zm9jdXMge1xuXHRcdEBpbmNsdWRlIGlucHV0LXN0eWxlX19mb2N1cygkYWNjZW50LWNvbG9yKTtcblx0fVxuXG5cdC8vIFVzZSBvcGFjaXR5IHRvIHdvcmsgaW4gdmFyaW91cyBlZGl0b3Igc3R5bGVzLlxuXHQmOjotd2Via2l0LWlucHV0LXBsYWNlaG9sZGVyIHtcblx0XHRjb2xvcjogY29sb3JzLiRkYXJrLWdyYXktcGxhY2Vob2xkZXI7XG5cdH1cblxuXHQmOjotbW96LXBsYWNlaG9sZGVyIHtcblx0XHRjb2xvcjogY29sb3JzLiRkYXJrLWdyYXktcGxhY2Vob2xkZXI7XG5cdH1cblxuXHQmOi1tcy1pbnB1dC1wbGFjZWhvbGRlciB7XG5cdFx0Y29sb3I6IGNvbG9ycy4kZGFyay1ncmF5LXBsYWNlaG9sZGVyO1xuXHR9XG59XG5cbkBtaXhpbiBjaGVja2JveC1jb250cm9sIHtcblx0Ym9yZGVyOiB2YXJpYWJsZXMuJGJvcmRlci13aWR0aCBzb2xpZCBjb2xvcnMuJGdyYXktOTAwO1xuXHRtYXJnaW4tcmlnaHQ6IHZhcmlhYmxlcy4kZ3JpZC11bml0LTE1O1xuXHR0cmFuc2l0aW9uOiBub25lO1xuXHRib3JkZXItcmFkaXVzOiB2YXJpYWJsZXMuJHJhZGl1cy1zbWFsbDtcblx0QGluY2x1ZGUgaW5wdXQtY29udHJvbDtcblxuXHQmOmZvY3VzIHtcblx0XHRib3gtc2hhZG93OiAwIDAgMCAodmFyaWFibGVzLiRib3JkZXItd2lkdGggKiAyKSBjb2xvcnMuJHdoaXRlLCAwIDAgMCAodmFyaWFibGVzLiRib3JkZXItd2lkdGggKiAyICsgdmFyaWFibGVzLiRib3JkZXItd2lkdGgtZm9jdXMtZmFsbGJhY2spIHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblxuXHRcdC8vIE9ubHkgdmlzaWJsZSBpbiBXaW5kb3dzIEhpZ2ggQ29udHJhc3QgbW9kZS5cblx0XHRvdXRsaW5lOiAycHggc29saWQgdHJhbnNwYXJlbnQ7XG5cdH1cblxuXHQmOmNoZWNrZWQge1xuXHRcdGJhY2tncm91bmQ6IHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblx0XHRib3JkZXItY29sb3I6IHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblxuXHRcdC8vIEhpZGUgZGVmYXVsdCBjaGVja2JveCBzdHlsZXMgaW4gSUUuXG5cdFx0Jjo6LW1zLWNoZWNrIHtcblx0XHRcdG9wYWNpdHk6IDA7XG5cdFx0fVxuXHR9XG5cblx0JjpjaGVja2VkOjpiZWZvcmUsXG5cdCZbYXJpYS1jaGVja2VkPVwibWl4ZWRcIl06OmJlZm9yZSB7XG5cdFx0bWFyZ2luOiAtM3B4IC01cHg7XG5cdFx0Y29sb3I6IGNvbG9ycy4kd2hpdGU7XG5cblx0XHRAaW5jbHVkZSBicmVhay1tZWRpdW0oKSB7XG5cdFx0XHRtYXJnaW46IC00cHggMCAwIC01cHg7XG5cdFx0fVxuXHR9XG5cblx0JlthcmlhLWNoZWNrZWQ9XCJtaXhlZFwiXSB7XG5cdFx0YmFja2dyb3VuZDogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXHRcdGJvcmRlci1jb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3IpO1xuXG5cdFx0Jjo6YmVmb3JlIHtcblx0XHRcdC8vIEluaGVyaXRlZCBmcm9tIGBmb3Jtcy5jc3NgLlxuXHRcdFx0Ly8gU2VlOiBodHRwczovL2dpdGh1Yi5jb20vV29yZFByZXNzL3dvcmRwcmVzcy1kZXZlbG9wL3RyZWUvNS4xLjEvc3JjL3dwLWFkbWluL2Nzcy9mb3Jtcy5jc3MjTDEyMi1MMTMyXG5cdFx0XHRjb250ZW50OiBcIlxcZjQ2MFwiO1xuXHRcdFx0ZmxvYXQ6IGxlZnQ7XG5cdFx0XHRkaXNwbGF5OiBpbmxpbmUtYmxvY2s7XG5cdFx0XHR2ZXJ0aWNhbC1hbGlnbjogbWlkZGxlO1xuXHRcdFx0d2lkdGg6IDE2cHg7XG5cdFx0XHQvKiBzdHlsZWxpbnQtZGlzYWJsZS1uZXh0LWxpbmUgZm9udC1mYW1pbHktbm8tbWlzc2luZy1nZW5lcmljLWZhbWlseS1rZXl3b3JkIC0tIGRhc2hpY29ucyBkb24ndCBuZWVkIGEgZ2VuZXJpYyBmYW1pbHkga2V5d29yZC4gKi9cblx0XHRcdGZvbnQ6IG5vcm1hbCAzMHB4LzEgZGFzaGljb25zO1xuXHRcdFx0c3BlYWs6IG5vbmU7XG5cdFx0XHQtd2Via2l0LWZvbnQtc21vb3RoaW5nOiBhbnRpYWxpYXNlZDtcblx0XHRcdC1tb3otb3N4LWZvbnQtc21vb3RoaW5nOiBncmF5c2NhbGU7XG5cblx0XHRcdEBpbmNsdWRlIGJyZWFrLW1lZGl1bSgpIHtcblx0XHRcdFx0ZmxvYXQ6IG5vbmU7XG5cdFx0XHRcdGZvbnQtc2l6ZTogMjFweDtcblx0XHRcdH1cblx0XHR9XG5cdH1cblxuXHQmW2FyaWEtZGlzYWJsZWQ9XCJ0cnVlXCJdLFxuXHQmOmRpc2FibGVkIHtcblx0XHRiYWNrZ3JvdW5kOiBjb2xvcnMuJGdyYXktMTAwO1xuXHRcdGJvcmRlci1jb2xvcjogY29sb3JzLiRncmF5LTMwMDtcblx0XHRjdXJzb3I6IGRlZmF1bHQ7XG5cblx0XHQvLyBPdmVycmlkZSBzdHlsZSBpbmhlcml0ZWQgZnJvbSB3cC1hZG1pbi4gUmVxdWlyZWQgdG8gYXZvaWQgZGVncmFkZWQgYXBwZWFyYW5jZSBvbiBkaWZmZXJlbnQgYmFja2dyb3VuZHMuXG5cdFx0b3BhY2l0eTogMTtcblx0fVxufVxuXG5AbWl4aW4gcmFkaW8tY29udHJvbCB7XG5cdGJvcmRlcjogdmFyaWFibGVzLiRib3JkZXItd2lkdGggc29saWQgY29sb3JzLiRncmF5LTkwMDtcblx0bWFyZ2luLXJpZ2h0OiB2YXJpYWJsZXMuJGdyaWQtdW5pdC0xNTtcblx0dHJhbnNpdGlvbjogbm9uZTtcblx0Ym9yZGVyLXJhZGl1czogdmFyaWFibGVzLiRyYWRpdXMtcm91bmQ7XG5cdHdpZHRoOiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemUtc207XG5cdGhlaWdodDogdmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLXNtO1xuXHRtaW4td2lkdGg6IHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZS1zbTtcblx0bWF4LXdpZHRoOiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemUtc207XG5cdHBvc2l0aW9uOiByZWxhdGl2ZTtcblxuXHRAbWVkaWEgbm90IChwcmVmZXJzLXJlZHVjZWQtbW90aW9uKSB7XG5cdFx0dHJhbnNpdGlvbjogYm94LXNoYWRvdyAwLjFzIGxpbmVhcjtcblx0fVxuXG5cdEBpbmNsdWRlIGJyZWFrLXNtYWxsKCkge1xuXHRcdGhlaWdodDogdmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplO1xuXHRcdHdpZHRoOiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemU7XG5cdFx0bWluLXdpZHRoOiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemU7XG5cdFx0bWF4LXdpZHRoOiB2YXJpYWJsZXMuJHJhZGlvLWlucHV0LXNpemU7XG5cdH1cblxuXHQmOmNoZWNrZWQ6OmJlZm9yZSB7XG5cdFx0Ym94LXNpemluZzogaW5oZXJpdDtcblx0XHR3aWR0aDogbWF0aC5kaXYodmFyaWFibGVzLiRyYWRpby1pbnB1dC1zaXplLXNtLCAyKTtcblx0XHRoZWlnaHQ6IG1hdGguZGl2KHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZS1zbSwgMik7XG5cdFx0cG9zaXRpb246IGFic29sdXRlO1xuXHRcdHRvcDogNTAlO1xuXHRcdGxlZnQ6IDUwJTtcblx0XHR0cmFuc2Zvcm06IHRyYW5zbGF0ZSgtNTAlLCAtNTAlKTtcblx0XHRtYXJnaW46IDA7XG5cdFx0YmFja2dyb3VuZC1jb2xvcjogY29sb3JzLiR3aGl0ZTtcblxuXHRcdC8vIFRoaXMgYm9yZGVyIHNlcnZlcyBhcyBhIGJhY2tncm91bmQgY29sb3IgaW4gV2luZG93cyBIaWdoIENvbnRyYXN0IG1vZGUuXG5cdFx0Ym9yZGVyOiA0cHggc29saWQgY29sb3JzLiR3aGl0ZTtcblxuXHRcdEBpbmNsdWRlIGJyZWFrLXNtYWxsKCkge1xuXHRcdFx0d2lkdGg6IG1hdGguZGl2KHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZSwgMik7XG5cdFx0XHRoZWlnaHQ6IG1hdGguZGl2KHZhcmlhYmxlcy4kcmFkaW8taW5wdXQtc2l6ZSwgMik7XG5cdFx0fVxuXHR9XG5cblx0Jjpmb2N1cyB7XG5cdFx0Ym94LXNoYWRvdzogMCAwIDAgKHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoICogMikgY29sb3JzLiR3aGl0ZSwgMCAwIDAgKHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoICogMiArIHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoLWZvY3VzLWZhbGxiYWNrKSB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcik7XG5cblx0XHQvLyBPbmx5IHZpc2libGUgaW4gV2luZG93cyBIaWdoIENvbnRyYXN0IG1vZGUuXG5cdFx0b3V0bGluZTogMnB4IHNvbGlkIHRyYW5zcGFyZW50O1xuXHR9XG5cblx0JjpjaGVja2VkIHtcblx0XHRiYWNrZ3JvdW5kOiB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcik7XG5cdFx0Ym9yZGVyOiBub25lO1xuXHR9XG59XG5cbi8qKlxuICogUmVzZXQgZGVmYXVsdCBzdHlsZXMgZm9yIEphdmFTY3JpcHQgVUkgYmFzZWQgcGFnZXMuXG4gKiBUaGlzIGlzIGEgV1AtYWRtaW4gYWdub3N0aWMgcmVzZXRcbiAqL1xuXG5AbWl4aW4gcmVzZXQge1xuXHRib3gtc2l6aW5nOiBib3JkZXItYm94O1xuXG5cdCosXG5cdCo6OmJlZm9yZSxcblx0Kjo6YWZ0ZXIge1xuXHRcdGJveC1zaXppbmc6IGluaGVyaXQ7XG5cdH1cbn1cblxuQG1peGluIGxpbmstcmVzZXQge1xuXHQmOmZvY3VzIHtcblx0XHRjb2xvcjogdmFyKC0td3AtYWRtaW4tdGhlbWUtY29sb3ItLXJnYik7XG5cdFx0Ym94LXNoYWRvdzogMCAwIDAgdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvciwgIzAwN2NiYSk7XG5cdFx0Ym9yZGVyLXJhZGl1czogdmFyaWFibGVzLiRyYWRpdXMtc21hbGw7XG5cdH1cbn1cblxuLy8gVGhlIGVkaXRvciBpbnB1dCByZXNldCB3aXRoIGluY3JlYXNlZCBzcGVjaWZpY2l0eSB0byBhdm9pZCB0aGVtZSBzdHlsZXMgYmxlZWRpbmcgaW4uXG5AbWl4aW4gZWRpdG9yLWlucHV0LXJlc2V0KCkge1xuXHRmb250LWZhbWlseTogdmFyaWFibGVzLiRlZGl0b3ItaHRtbC1mb250ICFpbXBvcnRhbnQ7XG5cdGNvbG9yOiBjb2xvcnMuJGdyYXktOTAwICFpbXBvcnRhbnQ7XG5cdGJhY2tncm91bmQ6IGNvbG9ycy4kd2hpdGUgIWltcG9ydGFudDtcblx0cGFkZGluZzogdmFyaWFibGVzLiRncmlkLXVuaXQtMTUgIWltcG9ydGFudDtcblx0Ym9yZGVyOiB2YXJpYWJsZXMuJGJvcmRlci13aWR0aCBzb2xpZCBjb2xvcnMuJGdyYXktOTAwICFpbXBvcnRhbnQ7XG5cdGJveC1zaGFkb3c6IG5vbmUgIWltcG9ydGFudDtcblx0Ym9yZGVyLXJhZGl1czogdmFyaWFibGVzLiRyYWRpdXMtc21hbGwgIWltcG9ydGFudDtcblxuXHQvLyBGb250cyBzbWFsbGVyIHRoYW4gMTZweCBjYXVzZXMgbW9iaWxlIHNhZmFyaSB0byB6b29tLlxuXHRmb250LXNpemU6IHZhcmlhYmxlcy4kbW9iaWxlLXRleHQtbWluLWZvbnQtc2l6ZSAhaW1wb3J0YW50O1xuXHRAaW5jbHVkZSBicmVhay1zbWFsbCB7XG5cdFx0Zm9udC1zaXplOiB2YXJpYWJsZXMuJGRlZmF1bHQtZm9udC1zaXplICFpbXBvcnRhbnQ7XG5cdH1cblxuXHQmOmZvY3VzIHtcblx0XHRib3JkZXItY29sb3I6IHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKSAhaW1wb3J0YW50O1xuXHRcdGJveC1zaGFkb3c6IDAgMCAwICh2YXJpYWJsZXMuJGJvcmRlci13aWR0aC1mb2N1cy1mYWxsYmFjayAtIHZhcmlhYmxlcy4kYm9yZGVyLXdpZHRoKSB2YXIoLS13cC1hZG1pbi10aGVtZS1jb2xvcikgIWltcG9ydGFudDtcblxuXHRcdC8vIFdpbmRvd3MgSGlnaCBDb250cmFzdCBtb2RlIHdpbGwgc2hvdyB0aGlzIG91dGxpbmUsIGJ1dCBub3QgdGhlIGJveC1zaGFkb3cuXG5cdFx0b3V0bGluZTogMnB4IHNvbGlkIHRyYW5zcGFyZW50ICFpbXBvcnRhbnQ7XG5cdH1cbn1cblxuLyoqXG4gKiBSZXNldCB0aGUgV1AgQWRtaW4gcGFnZSBzdHlsZXMgZm9yIEd1dGVuYmVyZy1saWtlIHBhZ2VzLlxuICovXG5cbkBtaXhpbiB3cC1hZG1pbi1yZXNldCggJGNvbnRlbnQtY29udGFpbmVyICkge1xuXHRiYWNrZ3JvdW5kOiBjb2xvcnMuJHdoaXRlO1xuXG5cdCN3cGNvbnRlbnQge1xuXHRcdHBhZGRpbmctbGVmdDogMDtcblx0fVxuXG5cdCN3cGJvZHktY29udGVudCB7XG5cdFx0cGFkZGluZy1ib3R0b206IDA7XG5cdH1cblxuXHQvKiBXZSBoaWRlIGxlZ2FjeSBub3RpY2VzIGluIEd1dGVuYmVyZyBCYXNlZCBQYWdlcywgYmVjYXVzZSB0aGV5IHdlcmUgbm90IGRlc2lnbmVkIGluIGEgd2F5IHRoYXQgc2NhbGVkIHdlbGwuXG5cdCAgIFBsdWdpbnMgY2FuIHVzZSBHdXRlbmJlcmcgbm90aWNlcyBpZiB0aGV5IG5lZWQgdG8gcGFzcyBvbiBpbmZvcm1hdGlvbiB0byB0aGUgdXNlciB3aGVuIHRoZXkgYXJlIGVkaXRpbmcuICovXG5cdCN3cGJvZHktY29udGVudCA+IGRpdjpub3QoI3sgJGNvbnRlbnQtY29udGFpbmVyIH0pOm5vdCgjc2NyZWVuLW1ldGEpIHtcblx0XHRkaXNwbGF5OiBub25lO1xuXHR9XG5cblx0I3dwZm9vdGVyIHtcblx0XHRkaXNwbGF5OiBub25lO1xuXHR9XG5cblx0LmExMXktc3BlYWstcmVnaW9uIHtcblx0XHRsZWZ0OiAtMXB4O1xuXHRcdHRvcDogLTFweDtcblx0fVxuXG5cdHVsI2FkbWlubWVudSBhLndwLWhhcy1jdXJyZW50LXN1Ym1lbnU6OmFmdGVyLFxuXHR1bCNhZG1pbm1lbnUgPiBsaS5jdXJyZW50ID4gYS5jdXJyZW50OjphZnRlciB7XG5cdFx0Ym9yZGVyLXJpZ2h0LWNvbG9yOiBjb2xvcnMuJHdoaXRlO1xuXHR9XG5cblx0Lm1lZGlhLWZyYW1lIHNlbGVjdC5hdHRhY2htZW50LWZpbHRlcnM6bGFzdC1vZi10eXBlIHtcblx0XHR3aWR0aDogYXV0bztcblx0XHRtYXgtd2lkdGg6IDEwMCU7XG5cdH1cbn1cblxuQG1peGluIGFkbWluLXNjaGVtZSgkY29sb3ItcHJpbWFyeSkge1xuXHQvLyBEZWZpbmUgUkdCIGVxdWl2YWxlbnRzIGZvciB1c2UgaW4gcmdiYSBmdW5jdGlvbi5cblx0Ly8gSGV4YWRlY2ltYWwgY3NzIHZhcnMgZG8gbm90IHdvcmsgaW4gdGhlIHJnYmEgZnVuY3Rpb24uXG5cdC0td3AtYWRtaW4tdGhlbWUtY29sb3I6ICN7JGNvbG9yLXByaW1hcnl9O1xuXHQtLXdwLWFkbWluLXRoZW1lLWNvbG9yLS1yZ2I6ICN7ZnVuY3Rpb25zLmhleC10by1yZ2IoJGNvbG9yLXByaW1hcnkpfTtcblx0Ly8gRGFya2VyIHNoYWRlcy5cblx0LS13cC1hZG1pbi10aGVtZS1jb2xvci1kYXJrZXItMTA6ICN7Y29sb3IuYWRqdXN0KCRjb2xvci1wcmltYXJ5LCAkbGlnaHRuZXNzOiAtNSUpfTtcblx0LS13cC1hZG1pbi10aGVtZS1jb2xvci1kYXJrZXItMTAtLXJnYjogI3tmdW5jdGlvbnMuaGV4LXRvLXJnYihjb2xvci5hZGp1c3QoJGNvbG9yLXByaW1hcnksICRsaWdodG5lc3M6IC01JSkpfTtcblx0LS13cC1hZG1pbi10aGVtZS1jb2xvci1kYXJrZXItMjA6ICN7Y29sb3IuYWRqdXN0KCRjb2xvci1wcmltYXJ5LCAkbGlnaHRuZXNzOiAtMTAlKX07XG5cdC0td3AtYWRtaW4tdGhlbWUtY29sb3ItZGFya2VyLTIwLS1yZ2I6ICN7ZnVuY3Rpb25zLmhleC10by1yZ2IoY29sb3IuYWRqdXN0KCRjb2xvci1wcmltYXJ5LCAkbGlnaHRuZXNzOiAtMTAlKSl9O1xuXG5cdC8vIEZvY3VzIHN0eWxlIHdpZHRoLlxuXHQvLyBBdm9pZCByb3VuZGluZyBpc3N1ZXMgYnkgc2hvd2luZyBhIHdob2xlIDJweCBmb3IgMXggc2NyZWVucywgYW5kIDEuNXB4IG9uIGhpZ2ggcmVzb2x1dGlvbiBzY3JlZW5zLlxuXHQtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1czogMnB4O1xuXHRAbWVkaWEgKCAtd2Via2l0LW1pbi1kZXZpY2UtcGl4ZWwtcmF0aW86IDIpLCAobWluLXJlc29sdXRpb246IDE5MmRwaSkge1xuXHRcdC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzOiAxLjVweDtcblx0fVxufVxuXG5AbWl4aW4gd29yZHByZXNzLWFkbWluLXNjaGVtZXMoKSB7XG5cdGJvZHkuYWRtaW4tY29sb3ItbGlnaHQge1xuXHRcdEBpbmNsdWRlIGFkbWluLXNjaGVtZSgjMDA4NWJhKTtcblx0fVxuXG5cdGJvZHkuYWRtaW4tY29sb3ItbW9kZXJuIHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoIzM4NThlOSk7XG5cdH1cblxuXHRib2R5LmFkbWluLWNvbG9yLWJsdWUge1xuXHRcdEBpbmNsdWRlIGFkbWluLXNjaGVtZSgjMDk2NDg0KTtcblx0fVxuXG5cdGJvZHkuYWRtaW4tY29sb3ItY29mZmVlIHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoIzQ2NDAzYyk7XG5cdH1cblxuXHRib2R5LmFkbWluLWNvbG9yLWVjdG9wbGFzbSB7XG5cdFx0QGluY2x1ZGUgYWRtaW4tc2NoZW1lKCM1MjNmNmQpO1xuXHR9XG5cblx0Ym9keS5hZG1pbi1jb2xvci1taWRuaWdodCB7XG5cdFx0QGluY2x1ZGUgYWRtaW4tc2NoZW1lKCNlMTRkNDMpO1xuXHR9XG5cblx0Ym9keS5hZG1pbi1jb2xvci1vY2VhbiB7XG5cdFx0QGluY2x1ZGUgYWRtaW4tc2NoZW1lKCM2MjdjODMpO1xuXHR9XG5cblx0Ym9keS5hZG1pbi1jb2xvci1zdW5yaXNlIHtcblx0XHRAaW5jbHVkZSBhZG1pbi1zY2hlbWUoI2RkODIzYik7XG5cdH1cbn1cblxuLy8gRGVwcmVjYXRlZCBmcm9tIFVJLCBrZXB0IGZvciBiYWNrLWNvbXBhdC5cbkBtaXhpbiBiYWNrZ3JvdW5kLWNvbG9ycy1kZXByZWNhdGVkKCkge1xuXHQuaGFzLXZlcnktbGlnaHQtZ3JheS1iYWNrZ3JvdW5kLWNvbG9yIHtcblx0XHRiYWNrZ3JvdW5kLWNvbG9yOiAjZWVlO1xuXHR9XG5cblx0Lmhhcy12ZXJ5LWRhcmstZ3JheS1iYWNrZ3JvdW5kLWNvbG9yIHtcblx0XHRiYWNrZ3JvdW5kLWNvbG9yOiAjMzEzMTMxO1xuXHR9XG59XG5cbi8vIERlcHJlY2F0ZWQgZnJvbSBVSSwga2VwdCBmb3IgYmFjay1jb21wYXQuXG5AbWl4aW4gZm9yZWdyb3VuZC1jb2xvcnMtZGVwcmVjYXRlZCgpIHtcblx0Lmhhcy12ZXJ5LWxpZ2h0LWdyYXktY29sb3Ige1xuXHRcdGNvbG9yOiAjZWVlO1xuXHR9XG5cblx0Lmhhcy12ZXJ5LWRhcmstZ3JheS1jb2xvciB7XG5cdFx0Y29sb3I6ICMzMTMxMzE7XG5cdH1cbn1cblxuLy8gRGVwcmVjYXRlZCBmcm9tIFVJLCBrZXB0IGZvciBiYWNrLWNvbXBhdC5cbkBtaXhpbiBncmFkaWVudC1jb2xvcnMtZGVwcmVjYXRlZCgpIHtcblx0Ly8gT3VyIGNsYXNzZXMgdXNlcyB0aGUgc2FtZSB2YWx1ZXMgd2Ugc2V0IGZvciBncmFkaWVudCB2YWx1ZSBhdHRyaWJ1dGVzLlxuXG5cdC8qIHN0eWxlbGludC1kaXNhYmxlIEBzdHlsaXN0aWMvZnVuY3Rpb24tY29tbWEtc3BhY2UtYWZ0ZXIgLS0gV2UgY2FuIG5vdCB1c2Ugc3BhY2luZyBiZWNhdXNlIG9mIFdQIG11bHRpIHNpdGUga3NlcyBydWxlLiAqL1xuXHQuaGFzLXZpdmlkLWdyZWVuLWN5YW4tdG8tdml2aWQtY3lhbi1ibHVlLWdyYWRpZW50LWJhY2tncm91bmQge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCgxMzVkZWcscmdiYSgwLDIwOCwxMzIsMSkgMCUscmdiYSg2LDE0NywyMjcsMSkgMTAwJSk7XG5cdH1cblxuXHQuaGFzLXB1cnBsZS1jcnVzaC1ncmFkaWVudC1iYWNrZ3JvdW5kIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQoMTM1ZGVnLHJnYig1MiwyMjYsMjI4KSAwJSxyZ2IoNzEsMzMsMjUxKSA1MCUscmdiKDE3MSwyOSwyNTQpIDEwMCUpO1xuXHR9XG5cblx0Lmhhcy1oYXp5LWRhd24tZ3JhZGllbnQtYmFja2dyb3VuZCB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KDEzNWRlZyxyZ2IoMjUwLDE3MiwxNjgpIDAlLHJnYigyMTgsMjA4LDIzNikgMTAwJSk7XG5cdH1cblxuXHQuaGFzLXN1YmR1ZWQtb2xpdmUtZ3JhZGllbnQtYmFja2dyb3VuZCB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KDEzNWRlZyxyZ2IoMjUwLDI1MCwyMjUpIDAlLHJnYigxMDMsMTY2LDExMykgMTAwJSk7XG5cdH1cblxuXHQuaGFzLWF0b21pYy1jcmVhbS1ncmFkaWVudC1iYWNrZ3JvdW5kIHtcblx0XHRiYWNrZ3JvdW5kOiBsaW5lYXItZ3JhZGllbnQoMTM1ZGVnLHJnYigyNTMsMjE1LDE1NCkgMCUscmdiKDAsNzQsODkpIDEwMCUpO1xuXHR9XG5cblx0Lmhhcy1uaWdodHNoYWRlLWdyYWRpZW50LWJhY2tncm91bmQge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCgxMzVkZWcscmdiKDUxLDksMTA0KSAwJSxyZ2IoNDksMjA1LDIwNykgMTAwJSk7XG5cdH1cblxuXHQuaGFzLW1pZG5pZ2h0LWdyYWRpZW50LWJhY2tncm91bmQge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCgxMzVkZWcscmdiKDIsMywxMjkpIDAlLHJnYig0MCwxMTYsMjUyKSAxMDAlKTtcblx0fVxuXHQvKiBzdHlsZWxpbnQtZW5hYmxlIEBzdHlsaXN0aWMvZnVuY3Rpb24tY29tbWEtc3BhY2UtYWZ0ZXIgKi9cbn1cblxuQG1peGluIGN1c3RvbS1zY3JvbGxiYXJzLW9uLWhvdmVyKCRoYW5kbGUtY29sb3IsICRoYW5kbGUtY29sb3ItaG92ZXIpIHtcblxuXHQvLyBXZWJLaXRcblx0Jjo6LXdlYmtpdC1zY3JvbGxiYXIge1xuXHRcdHdpZHRoOiAxMnB4O1xuXHRcdGhlaWdodDogMTJweDtcblx0fVxuXHQmOjotd2Via2l0LXNjcm9sbGJhci10cmFjayB7XG5cdFx0YmFja2dyb3VuZC1jb2xvcjogdHJhbnNwYXJlbnQ7XG5cdH1cblx0Jjo6LXdlYmtpdC1zY3JvbGxiYXItdGh1bWIge1xuXHRcdGJhY2tncm91bmQtY29sb3I6ICRoYW5kbGUtY29sb3I7XG5cdFx0Ym9yZGVyLXJhZGl1czogOHB4O1xuXHRcdGJvcmRlcjogM3B4IHNvbGlkIHRyYW5zcGFyZW50O1xuXHRcdGJhY2tncm91bmQtY2xpcDogcGFkZGluZy1ib3g7XG5cdH1cblx0Jjpob3Zlcjo6LXdlYmtpdC1zY3JvbGxiYXItdGh1bWIsIC8vIFRoaXMgbmVlZHMgc3BlY2lmaWNpdHkuXG5cdCY6Zm9jdXM6Oi13ZWJraXQtc2Nyb2xsYmFyLXRodW1iLFxuXHQmOmZvY3VzLXdpdGhpbjo6LXdlYmtpdC1zY3JvbGxiYXItdGh1bWIge1xuXHRcdGJhY2tncm91bmQtY29sb3I6ICRoYW5kbGUtY29sb3ItaG92ZXI7XG5cdH1cblxuXHQvLyBGaXJlZm94IDEwOSsgYW5kIENocm9tZSAxMTErXG5cdHNjcm9sbGJhci13aWR0aDogdGhpbjtcblx0c2Nyb2xsYmFyLWd1dHRlcjogc3RhYmxlIGJvdGgtZWRnZXM7XG5cdHNjcm9sbGJhci1jb2xvcjogJGhhbmRsZS1jb2xvciB0cmFuc3BhcmVudDsgLy8gU3ludGF4LCBcImRhcmtcIiwgXCJsaWdodFwiLCBvciBcIiNoYW5kbGUtY29sb3IgI3RyYWNrLWNvbG9yXCJcblxuXHQmOmhvdmVyLFxuXHQmOmZvY3VzLFxuXHQmOmZvY3VzLXdpdGhpbiB7XG5cdFx0c2Nyb2xsYmFyLWNvbG9yOiAkaGFuZGxlLWNvbG9yLWhvdmVyIHRyYW5zcGFyZW50O1xuXHR9XG5cblx0Ly8gTmVlZGVkIHRvIGZpeCBhIFNhZmFyaSByZW5kZXJpbmcgaXNzdWUuXG5cdHdpbGwtY2hhbmdlOiB0cmFuc2Zvcm07XG5cblx0Ly8gQWx3YXlzIHNob3cgc2Nyb2xsYmFyIG9uIE1vYmlsZSBkZXZpY2VzLlxuXHRAbWVkaWEgKGhvdmVyOiBub25lKSB7XG5cdFx0JiB7XG5cdFx0XHRzY3JvbGxiYXItY29sb3I6ICRoYW5kbGUtY29sb3ItaG92ZXIgdHJhbnNwYXJlbnQ7XG5cdFx0fVxuXHR9XG59XG5cbkBtaXhpbiBzZWxlY3RlZC1ibG9jay1vdXRsaW5lKCR3aWR0aFJhdGlvOiAxKSB7XG5cdG91dGxpbmUtY29sb3I6IHZhcigtLXdwLWFkbWluLXRoZW1lLWNvbG9yKTtcblx0b3V0bGluZS1zdHlsZTogc29saWQ7XG5cdG91dGxpbmUtd2lkdGg6IGNhbGMoI3skd2lkdGhSYXRpb30gKiAodmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSAvIHZhcigtLXdwLWJsb2NrLWVkaXRvci1pZnJhbWUtem9vbS1vdXQtc2NhbGUsIDEpKSk7XG5cdG91dGxpbmUtb2Zmc2V0OiBjYWxjKCN7JHdpZHRoUmF0aW99ICogKCgtMSAqIHZhcigtLXdwLWFkbWluLWJvcmRlci13aWR0aC1mb2N1cykgKSAvIHZhcigtLXdwLWJsb2NrLWVkaXRvci1pZnJhbWUtem9vbS1vdXQtc2NhbGUsIDEpKSk7XG59XG5cbkBtaXhpbiBzZWxlY3RlZC1ibG9jay1mb2N1cygkd2lkdGhSYXRpbzogMSkge1xuXHRjb250ZW50OiBcIlwiO1xuXHRwb3NpdGlvbjogYWJzb2x1dGU7XG5cdHBvaW50ZXItZXZlbnRzOiBub25lO1xuXHR0b3A6IDA7XG5cdHJpZ2h0OiAwO1xuXHRib3R0b206IDA7XG5cdGxlZnQ6IDA7XG5cdEBpbmNsdWRlIHNlbGVjdGVkLWJsb2NrLW91dGxpbmUoJHdpZHRoUmF0aW8pO1xufVxuIiwiLyoqXG4gKiBTQ1NTIFZhcmlhYmxlcy5cbiAqXG4gKiBQbGVhc2UgdXNlIHZhcmlhYmxlcyBmcm9tIHRoaXMgc2hlZXQgdG8gZW5zdXJlIGNvbnNpc3RlbmN5IGFjcm9zcyB0aGUgVUkuXG4gKiBEb24ndCBhZGQgdG8gdGhpcyBzaGVldCB1bmxlc3MgeW91J3JlIHByZXR0eSBzdXJlIHRoZSB2YWx1ZSB3aWxsIGJlIHJldXNlZCBpbiBtYW55IHBsYWNlcy5cbiAqIEZvciBleGFtcGxlLCBkb24ndCBhZGQgcnVsZXMgdG8gdGhpcyBzaGVldCB0aGF0IGFmZmVjdCBibG9jayB2aXN1YWxzLiBJdCdzIHB1cmVseSBmb3IgVUkuXG4gKi9cblxuQHVzZSBcIi4vY29sb3JzXCI7XG5cbi8qKlxuICogRm9udHMgJiBiYXNpYyB2YXJpYWJsZXMuXG4gKi9cblxuJGRlZmF1bHQtZm9udDogLWFwcGxlLXN5c3RlbSwgQmxpbmtNYWNTeXN0ZW1Gb250LFwiU2Vnb2UgVUlcIiwgUm9ib3RvLCBPeHlnZW4tU2FucywgVWJ1bnR1LCBDYW50YXJlbGwsXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmOyAvLyBUb2RvOiBkZXByZWNhdGUgaW4gZmF2b3Igb2YgJGZhbWlseSB2YXJpYWJsZXNcbiRkZWZhdWx0LWxpbmUtaGVpZ2h0OiAxLjQ7IC8vIFRvZG86IGRlcHJlY2F0ZSBpbiBmYXZvciBvZiAkbGluZS1oZWlnaHQgdG9rZW5zXG5cbi8qKlxuICogVHlwb2dyYXBoeVxuICovXG5cbi8vIFNpemVzXG4kZm9udC1zaXplLXgtc21hbGw6IDExcHg7XG4kZm9udC1zaXplLXNtYWxsOiAxMnB4O1xuJGZvbnQtc2l6ZS1tZWRpdW06IDEzcHg7XG4kZm9udC1zaXplLWxhcmdlOiAxNXB4O1xuJGZvbnQtc2l6ZS14LWxhcmdlOiAyMHB4O1xuJGZvbnQtc2l6ZS0yeC1sYXJnZTogMzJweDtcblxuLy8gTGluZSBoZWlnaHRzXG4kZm9udC1saW5lLWhlaWdodC14LXNtYWxsOiAxNnB4O1xuJGZvbnQtbGluZS1oZWlnaHQtc21hbGw6IDIwcHg7XG4kZm9udC1saW5lLWhlaWdodC1tZWRpdW06IDI0cHg7XG4kZm9udC1saW5lLWhlaWdodC1sYXJnZTogMjhweDtcbiRmb250LWxpbmUtaGVpZ2h0LXgtbGFyZ2U6IDMycHg7XG4kZm9udC1saW5lLWhlaWdodC0yeC1sYXJnZTogNDBweDtcblxuLy8gV2VpZ2h0c1xuJGZvbnQtd2VpZ2h0LXJlZ3VsYXI6IDQwMDtcbiRmb250LXdlaWdodC1tZWRpdW06IDQ5OTsgLy8gZW5zdXJlcyBmYWxsYmFjayB0byA0MDAgKGluc3RlYWQgb2YgNjAwKVxuXG4vLyBGYW1pbGllc1xuJGZvbnQtZmFtaWx5LWhlYWRpbmdzOiAtYXBwbGUtc3lzdGVtLCBcInN5c3RlbS11aVwiLCBcIlNlZ29lIFVJXCIsIFJvYm90bywgT3h5Z2VuLVNhbnMsIFVidW50dSwgQ2FudGFyZWxsLCBcIkhlbHZldGljYSBOZXVlXCIsIHNhbnMtc2VyaWY7XG4kZm9udC1mYW1pbHktYm9keTogLWFwcGxlLXN5c3RlbSwgXCJzeXN0ZW0tdWlcIiwgXCJTZWdvZSBVSVwiLCBSb2JvdG8sIE94eWdlbi1TYW5zLCBVYnVudHUsIENhbnRhcmVsbCwgXCJIZWx2ZXRpY2EgTmV1ZVwiLCBzYW5zLXNlcmlmO1xuJGZvbnQtZmFtaWx5LW1vbm86IE1lbmxvLCBDb25zb2xhcywgbW9uYWNvLCBtb25vc3BhY2U7XG5cbi8qKlxuICogR3JpZCBTeXN0ZW0uXG4gKiBodHRwczovL21ha2Uud29yZHByZXNzLm9yZy9kZXNpZ24vMjAxOS8xMC8zMS9wcm9wb3NhbC1hLWNvbnNpc3RlbnQtc3BhY2luZy1zeXN0ZW0tZm9yLXdvcmRwcmVzcy9cbiAqL1xuXG4kZ3JpZC11bml0OiA4cHg7XG4kZ3JpZC11bml0LTA1OiAwLjUgKiAkZ3JpZC11bml0O1x0Ly8gNHB4XG4kZ3JpZC11bml0LTEwOiAxICogJGdyaWQtdW5pdDtcdFx0Ly8gOHB4XG4kZ3JpZC11bml0LTE1OiAxLjUgKiAkZ3JpZC11bml0O1x0Ly8gMTJweFxuJGdyaWQtdW5pdC0yMDogMiAqICRncmlkLXVuaXQ7XHRcdC8vIDE2cHhcbiRncmlkLXVuaXQtMzA6IDMgKiAkZ3JpZC11bml0O1x0XHQvLyAyNHB4XG4kZ3JpZC11bml0LTQwOiA0ICogJGdyaWQtdW5pdDtcdFx0Ly8gMzJweFxuJGdyaWQtdW5pdC01MDogNSAqICRncmlkLXVuaXQ7XHRcdC8vIDQwcHhcbiRncmlkLXVuaXQtNjA6IDYgKiAkZ3JpZC11bml0O1x0XHQvLyA0OHB4XG4kZ3JpZC11bml0LTcwOiA3ICogJGdyaWQtdW5pdDtcdFx0Ly8gNTZweFxuJGdyaWQtdW5pdC04MDogOCAqICRncmlkLXVuaXQ7XHRcdC8vIDY0cHhcblxuLyoqXG4gKiBSYWRpdXMgc2NhbGUuXG4gKi9cblxuJHJhZGl1cy14LXNtYWxsOiAxcHg7ICAgLy8gQXBwbGllZCB0byBlbGVtZW50cyBsaWtlIGJ1dHRvbnMgbmVzdGVkIHdpdGhpbiBwcmltaXRpdmVzIGxpa2UgaW5wdXRzLlxuJHJhZGl1cy1zbWFsbDogMnB4OyAgICAgLy8gQXBwbGllZCB0byBtb3N0IHByaW1pdGl2ZXMuXG4kcmFkaXVzLW1lZGl1bTogNHB4OyAgICAvLyBBcHBsaWVkIHRvIGNvbnRhaW5lcnMgd2l0aCBzbWFsbGVyIHBhZGRpbmcuXG4kcmFkaXVzLWxhcmdlOiA4cHg7ICAgICAvLyBBcHBsaWVkIHRvIGNvbnRhaW5lcnMgd2l0aCBsYXJnZXIgcGFkZGluZy5cbiRyYWRpdXMtZnVsbDogOTk5OXB4OyAgIC8vIEZvciBwaWxscy5cbiRyYWRpdXMtcm91bmQ6IDUwJTsgICAgIC8vIEZvciBjaXJjbGVzIGFuZCBvdmFscy5cblxuLyoqXG4gKiBFbGV2YXRpb24gc2NhbGUuXG4gKi9cblxuLy8gRm9yIHNlY3Rpb25zIGFuZCBjb250YWluZXJzIHRoYXQgZ3JvdXAgcmVsYXRlZCBjb250ZW50IGFuZCBjb250cm9scywgd2hpY2ggbWF5IG92ZXJsYXAgb3RoZXIgY29udGVudC4gRXhhbXBsZTogUHJldmlldyBGcmFtZS5cbiRlbGV2YXRpb24teC1zbWFsbDogMCAxcHggMXB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMyksIDAgMXB4IDJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpLCAwIDNweCAzcHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAyKSwgMCA0cHggNHB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMSk7XG5cbi8vIEZvciBjb21wb25lbnRzIHRoYXQgcHJvdmlkZSBjb250ZXh0dWFsIGZlZWRiYWNrIHdpdGhvdXQgYmVpbmcgaW50cnVzaXZlLiBHZW5lcmFsbHkgbm9uLWludGVycnVwdGl2ZS4gRXhhbXBsZTogVG9vbHRpcHMsIFNuYWNrYmFyLlxuJGVsZXZhdGlvbi1zbWFsbDogMCAxcHggMnB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNSksIDAgMnB4IDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDZweCA2cHggcmdiYShjb2xvcnMuJGJsYWNrLCAwLjAzKSwgMCA4cHggOHB4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wMik7XG5cbi8vIEZvciBjb21wb25lbnRzIHRoYXQgb2ZmZXIgYWRkaXRpb25hbCBhY3Rpb25zLiBFeGFtcGxlOiBNZW51cywgQ29tbWFuZCBQYWxldHRlXG4kZWxldmF0aW9uLW1lZGl1bTogMCAycHggM3B4IHJnYmEoY29sb3JzLiRibGFjaywgMC4wNSksIDAgNHB4IDVweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDEycHggMTJweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDMpLCAwIDE2cHggMTZweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vLyBGb3IgY29tcG9uZW50cyB0aGF0IGNvbmZpcm0gZGVjaXNpb25zIG9yIGhhbmRsZSBuZWNlc3NhcnkgaW50ZXJydXB0aW9ucy4gRXhhbXBsZTogTW9kYWxzLlxuJGVsZXZhdGlvbi1sYXJnZTogMCA1cHggMTVweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDgpLCAwIDE1cHggMjdweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDcpLCAwIDMwcHggMzZweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDQpLCAwIDUwcHggNDNweCByZ2JhKGNvbG9ycy4kYmxhY2ssIDAuMDIpO1xuXG4vKipcbiAqIERpbWVuc2lvbnMuXG4gKi9cblxuJGljb24tc2l6ZTogMjRweDtcbiRidXR0b24tc2l6ZTogMzZweDtcbiRidXR0b24tc2l6ZS1uZXh0LWRlZmF1bHQtNDBweDogNDBweDsgLy8gdHJhbnNpdGlvbmFyeSB2YXJpYWJsZSBmb3IgbmV4dCBkZWZhdWx0IGJ1dHRvbiBzaXplXG4kYnV0dG9uLXNpemUtc21hbGw6IDI0cHg7XG4kYnV0dG9uLXNpemUtY29tcGFjdDogMzJweDtcbiRoZWFkZXItaGVpZ2h0OiA2NHB4O1xuJHBhbmVsLWhlYWRlci1oZWlnaHQ6ICRncmlkLXVuaXQtNjA7XG4kbmF2LXNpZGViYXItd2lkdGg6IDMwMHB4O1xuJGFkbWluLWJhci1oZWlnaHQ6IDMycHg7XG4kYWRtaW4tYmFyLWhlaWdodC1iaWc6IDQ2cHg7XG4kYWRtaW4tc2lkZWJhci13aWR0aDogMTYwcHg7XG4kYWRtaW4tc2lkZWJhci13aWR0aC1iaWc6IDE5MHB4O1xuJGFkbWluLXNpZGViYXItd2lkdGgtY29sbGFwc2VkOiAzNnB4O1xuJG1vZGFsLW1pbi13aWR0aDogMzUwcHg7XG4kbW9kYWwtd2lkdGgtc21hbGw6IDM4NHB4O1xuJG1vZGFsLXdpZHRoLW1lZGl1bTogNTEycHg7XG4kbW9kYWwtd2lkdGgtbGFyZ2U6IDg0MHB4O1xuJHNwaW5uZXItc2l6ZTogMTZweDtcbiRjYW52YXMtcGFkZGluZzogJGdyaWQtdW5pdC0yMDtcbiRwYWxldHRlLW1heC1oZWlnaHQ6IDM2OHB4O1xuXG4vKipcbiAqIE1vYmlsZSBzcGVjaWZpYyBzdHlsZXNcbiAqL1xuJG1vYmlsZS10ZXh0LW1pbi1mb250LXNpemU6IDE2cHg7IC8vIEFueSBmb250IHNpemUgYmVsb3cgMTZweCB3aWxsIGNhdXNlIE1vYmlsZSBTYWZhcmkgdG8gXCJ6b29tIGluXCIuXG5cbi8qKlxuICogRWRpdG9yIHN0eWxlcy5cbiAqL1xuXG4kc2lkZWJhci13aWR0aDogMjgwcHg7XG4kY29udGVudC13aWR0aDogODQwcHg7XG4kd2lkZS1jb250ZW50LXdpZHRoOiAxMTAwcHg7XG4kd2lkZ2V0LWFyZWEtd2lkdGg6IDcwMHB4O1xuJHNlY29uZGFyeS1zaWRlYmFyLXdpZHRoOiAzNTBweDtcbiRlZGl0b3ItZm9udC1zaXplOiAxNnB4O1xuJGRlZmF1bHQtYmxvY2stbWFyZ2luOiAyOHB4OyAvLyBUaGlzIHZhbHVlIHByb3ZpZGVzIGEgY29uc2lzdGVudCwgY29udGlndW91cyBzcGFjaW5nIGJldHdlZW4gYmxvY2tzLlxuJHRleHQtZWRpdG9yLWZvbnQtc2l6ZTogMTVweDtcbiRlZGl0b3ItbGluZS1oZWlnaHQ6IDEuODtcbiRlZGl0b3ItaHRtbC1mb250OiAkZm9udC1mYW1pbHktbW9ubztcblxuLyoqXG4gKiBCbG9jayAmIEVkaXRvciBVSS5cbiAqL1xuXG4kYmxvY2stdG9vbGJhci1oZWlnaHQ6ICRncmlkLXVuaXQtNjA7XG4kYm9yZGVyLXdpZHRoOiAxcHg7XG4kYm9yZGVyLXdpZHRoLWZvY3VzLWZhbGxiYWNrOiAycHg7IC8vIFRoaXMgZXhpc3RzIGFzIGEgZmFsbGJhY2ssIGFuZCBpcyBpZGVhbGx5IG92ZXJyaWRkZW4gYnkgdmFyKC0td3AtYWRtaW4tYm9yZGVyLXdpZHRoLWZvY3VzKSB1bmxlc3MgaW4gc29tZSBTQVNTIG1hdGggY2FzZXMuXG4kYm9yZGVyLXdpZHRoLXRhYjogMS41cHg7XG4kaGVscHRleHQtZm9udC1zaXplOiAxMnB4O1xuJHJhZGlvLWlucHV0LXNpemU6IDE2cHg7XG4kcmFkaW8taW5wdXQtc2l6ZS1zbTogMjRweDsgLy8gV2lkdGggJiBoZWlnaHQgZm9yIHNtYWxsIHZpZXdwb3J0cy5cblxuLy8gRGVwcmVjYXRlZCwgcGxlYXNlIGF2b2lkIHVzaW5nIHRoZXNlLlxuJGJsb2NrLXBhZGRpbmc6IDE0cHg7IC8vIFVzZWQgdG8gZGVmaW5lIHNwYWNlIGJldHdlZW4gYmxvY2sgZm9vdHByaW50IGFuZCBzdXJyb3VuZGluZyBib3JkZXJzLlxuJHJhZGl1cy1ibG9jay11aTogJHJhZGl1cy1zbWFsbDtcbiRzaGFkb3ctcG9wb3ZlcjogJGVsZXZhdGlvbi14LXNtYWxsO1xuJHNoYWRvdy1tb2RhbDogJGVsZXZhdGlvbi1sYXJnZTtcbiRkZWZhdWx0LWZvbnQtc2l6ZTogJGZvbnQtc2l6ZS1tZWRpdW07XG5cbi8qKlxuICogQmxvY2sgcGFkZGluZ3MuXG4gKi9cblxuLy8gUGFkZGluZyBmb3IgYmxvY2tzIHdpdGggYSBiYWNrZ3JvdW5kIGNvbG9yIChlLmcuIHBhcmFncmFwaCBvciBncm91cCkuXG4kYmxvY2stYmctcGFkZGluZy0tdjogMS4yNWVtO1xuJGJsb2NrLWJnLXBhZGRpbmctLWg6IDIuMzc1ZW07XG5cblxuLyoqXG4gKiBSZWFjdCBOYXRpdmUgc3BlY2lmaWMuXG4gKiBUaGVzZSB2YXJpYWJsZXMgZG8gbm90IGFwcGVhciB0byBiZSB1c2VkIGFueXdoZXJlIGVsc2UuXG4gKi9cblxuLy8gRGltZW5zaW9ucy5cbiRtb2JpbGUtaGVhZGVyLXRvb2xiYXItaGVpZ2h0OiA0NHB4O1xuJG1vYmlsZS1oZWFkZXItdG9vbGJhci1leHBhbmRlZC1oZWlnaHQ6IDUycHg7XG4kbW9iaWxlLWZsb2F0aW5nLXRvb2xiYXItaGVpZ2h0OiA0NHB4O1xuJG1vYmlsZS1mbG9hdGluZy10b29sYmFyLW1hcmdpbjogOHB4O1xuJG1vYmlsZS1jb2xvci1zd2F0Y2g6IDQ4cHg7XG5cbi8vIEJsb2NrIFVJLlxuJG1vYmlsZS1ibG9jay10b29sYmFyLWhlaWdodDogNDRweDtcbiRkaW1tZWQtb3BhY2l0eTogMTtcbiRibG9jay1lZGdlLXRvLWNvbnRlbnQ6IDE2cHg7XG4kc29saWQtYm9yZGVyLXNwYWNlOiAxMnB4O1xuJGRhc2hlZC1ib3JkZXItc3BhY2U6IDZweDtcbiRibG9jay1zZWxlY3RlZC1tYXJnaW46IDNweDtcbiRibG9jay1zZWxlY3RlZC1ib3JkZXItd2lkdGg6IDFweDtcbiRibG9jay1zZWxlY3RlZC1wYWRkaW5nOiAwO1xuJGJsb2NrLXNlbGVjdGVkLWNoaWxkLW1hcmdpbjogNXB4O1xuJGJsb2NrLXNlbGVjdGVkLXRvLWNvbnRlbnQ6ICRibG9jay1lZGdlLXRvLWNvbnRlbnQgLSAkYmxvY2stc2VsZWN0ZWQtbWFyZ2luIC0gJGJsb2NrLXNlbGVjdGVkLWJvcmRlci13aWR0aDtcbiIsIi8qKlxuICogQ29sb3JzXG4gKi9cblxuLy8gV29yZFByZXNzIGdyYXlzLlxuJGJsYWNrOiAjMDAwO1x0XHRcdC8vIFVzZSBvbmx5IHdoZW4geW91IHRydWx5IG5lZWQgcHVyZSBibGFjay4gRm9yIFVJLCB1c2UgJGdyYXktOTAwLlxuJGdyYXktOTAwOiAjMWUxZTFlO1xuJGdyYXktODAwOiAjMmYyZjJmO1xuJGdyYXktNzAwOiAjNzU3NTc1O1x0XHQvLyBNZWV0cyA0LjY6MSAoNC41OjEgaXMgbWluaW11bSkgdGV4dCBjb250cmFzdCBhZ2FpbnN0IHdoaXRlLlxuJGdyYXktNjAwOiAjOTQ5NDk0O1x0XHQvLyBNZWV0cyAzOjEgVUkgb3IgbGFyZ2UgdGV4dCBjb250cmFzdCBhZ2FpbnN0IHdoaXRlLlxuJGdyYXktNDAwOiAjY2NjO1xuJGdyYXktMzAwOiAjZGRkO1x0XHQvLyBVc2VkIGZvciBtb3N0IGJvcmRlcnMuXG4kZ3JheS0yMDA6ICNlMGUwZTA7XHRcdC8vIFVzZWQgc3BhcmluZ2x5IGZvciBsaWdodCBib3JkZXJzLlxuJGdyYXktMTAwOiAjZjBmMGYwO1x0XHQvLyBVc2VkIGZvciBsaWdodCBncmF5IGJhY2tncm91bmRzLlxuJHdoaXRlOiAjZmZmO1xuXG4vLyBPcGFjaXRpZXMgJiBhZGRpdGlvbmFsIGNvbG9ycy5cbiRkYXJrLWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJGdyYXktOTAwLCAwLjYyKTtcbiRtZWRpdW0tZ3JheS1wbGFjZWhvbGRlcjogcmdiYSgkZ3JheS05MDAsIDAuNTUpO1xuJGxpZ2h0LWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJHdoaXRlLCAwLjY1KTtcblxuLy8gQWxlcnQgY29sb3JzLlxuJGFsZXJ0LXllbGxvdzogI2YwYjg0OTtcbiRhbGVydC1yZWQ6ICNjYzE4MTg7XG4kYWxlcnQtZ3JlZW46ICM0YWI4NjY7XG5cbi8vIERlcHJlY2F0ZWQsIHBsZWFzZSBhdm9pZCB1c2luZyB0aGVzZS5cbiRkYXJrLXRoZW1lLWZvY3VzOiAkd2hpdGU7XHQvLyBGb2N1cyBjb2xvciB3aGVuIHRoZSB0aGVtZSBpcyBkYXJrLlxuIiwiLyoqXG4gKiBCcmVha3BvaW50cyAmIE1lZGlhIFF1ZXJpZXNcbiAqL1xuXG4vLyBNb3N0IHVzZWQgYnJlYWtwb2ludHNcbiRicmVhay14aHVnZTogMTkyMHB4O1xuJGJyZWFrLWh1Z2U6IDE0NDBweDtcbiRicmVhay13aWRlOiAxMjgwcHg7XG4kYnJlYWsteGxhcmdlOiAxMDgwcHg7XG4kYnJlYWstbGFyZ2U6IDk2MHB4O1x0Ly8gYWRtaW4gc2lkZWJhciBhdXRvIGZvbGRzXG4kYnJlYWstbWVkaXVtOiA3ODJweDtcdC8vIGFkbWluYmFyIGdvZXMgYmlnXG4kYnJlYWstc21hbGw6IDYwMHB4O1xuJGJyZWFrLW1vYmlsZTogNDgwcHg7XG4kYnJlYWstem9vbWVkLWluOiAyODBweDtcblxuLy8gQWxsIG1lZGlhIHF1ZXJpZXMgY3VycmVudGx5IGluIFdvcmRQcmVzczpcbi8vXG4vLyBtaW4td2lkdGg6IDIwMDBweFxuLy8gbWluLXdpZHRoOiAxNjgwcHhcbi8vIG1pbi13aWR0aDogMTI1MHB4XG4vLyBtYXgtd2lkdGg6IDExMjBweCAqXG4vLyBtYXgtd2lkdGg6IDEwMDBweFxuLy8gbWluLXdpZHRoOiA3NjlweCBhbmQgbWF4LXdpZHRoOiAxMDAwcHhcbi8vIG1heC13aWR0aDogOTYwcHggKlxuLy8gbWF4LXdpZHRoOiA5MDBweFxuLy8gbWF4LXdpZHRoOiA4NTBweFxuLy8gbWluLXdpZHRoOiA4MDBweCBhbmQgbWF4LXdpZHRoOiAxNDk5cHhcbi8vIG1heC13aWR0aDogODAwcHhcbi8vIG1heC13aWR0aDogNzk5cHhcbi8vIG1heC13aWR0aDogNzgycHggKlxuLy8gbWF4LXdpZHRoOiA3NjhweFxuLy8gbWF4LXdpZHRoOiA2NDBweCAqXG4vLyBtYXgtd2lkdGg6IDYwMHB4ICpcbi8vIG1heC13aWR0aDogNTIwcHhcbi8vIG1heC13aWR0aDogNTAwcHhcbi8vIG1heC13aWR0aDogNDgwcHggKlxuLy8gbWF4LXdpZHRoOiA0MDBweCAqXG4vLyBtYXgtd2lkdGg6IDM4MHB4XG4vLyBtYXgtd2lkdGg6IDMyMHB4ICpcbi8vXG4vLyBUaG9zZSBtYXJrZWQgKiBzZWVtIHRvIGJlIG1vcmUgY29tbW9ubHkgdXNlZCB0aGFuIHRoZSBvdGhlcnMuXG4vLyBMZXQncyB0cnkgYW5kIHVzZSBhcyBmZXcgb2YgdGhlc2UgYXMgcG9zc2libGUsIGFuZCBiZSBtaW5kZnVsIGFib3V0IGFkZGluZyBuZXcgb25lcywgc28gd2UgZG9uJ3QgbWFrZSB0aGUgc2l0dWF0aW9uIHdvcnNlXG4iLCIvKipcbiogIENvbnZlcnRzIGEgaGV4IHZhbHVlIGludG8gdGhlIHJnYiBlcXVpdmFsZW50LlxuKlxuKiBAcGFyYW0ge3N0cmluZ30gaGV4IC0gdGhlIGhleGFkZWNpbWFsIHZhbHVlIHRvIGNvbnZlcnRcbiogQHJldHVybiB7c3RyaW5nfSBjb21tYSBzZXBhcmF0ZWQgcmdiIHZhbHVlc1xuKi9cblxuQHVzZSBcInNhc3M6Y29sb3JcIjtcbkB1c2UgXCJzYXNzOm1ldGFcIjtcblxuQGZ1bmN0aW9uIGhleC10by1yZ2IoJGhleCkge1xuXHQvKlxuXHQgKiBUT0RPOiBgY29sb3Iue3JlZHxncmVlbnxibHVlfWAgd2lsbCB0cmlnZ2VyIGEgZGVwcmVjYXRpb24gd2FybmluZyBpbiBEYXJ0IFNhc3MsXG5cdCAqIGJ1dCB0aGUgU2FzcyB1c2VkIGJ5IHRoZSBHdXRlbmJlcmcgcHJvamVjdCBkb2Vzbid0IHN1cHBvcnQgYGNvbG9yLmNoYW5uZWwoKWAgeWV0LFxuXHQgKiBzbyB3ZSBjYW4ndCBtaWdyYXRlIHRvIGl0IGF0IHRoaXMgdGltZS5cblx0ICogSW4gdGhlIGZ1dHVyZSwgYWZ0ZXIgdGhlIEd1dGVuYmVyZyBwcm9qZWN0IGhhcyBiZWVuIGZ1bGx5IG1pZ3JhdGVkIHRvIERhcnQgU2Fzcyxcblx0ICogUmVtb3ZlIHRoaXMgY29uZGl0aW9uYWwgc3RhdGVtZW50IGFuZCB1c2Ugb25seSBgY29sb3IuY2hhbm5lbCgpYC5cblx0ICovXG5cdEBpZiBtZXRhLmZ1bmN0aW9uLWV4aXN0cyhcImNoYW5uZWxcIiwgXCJjb2xvclwiKSB7XG5cdFx0QHJldHVybiBjb2xvci5jaGFubmVsKCRoZXgsIFwicmVkXCIpLCBjb2xvci5jaGFubmVsKCRoZXgsIFwiZ3JlZW5cIiksIGNvbG9yLmNoYW5uZWwoJGhleCwgXCJibHVlXCIpO1xuXHR9IEBlbHNlIHtcblx0XHRAcmV0dXJuIGNvbG9yLnJlZCgkaGV4KSwgY29sb3IuZ3JlZW4oJGhleCksIGNvbG9yLmJsdWUoJGhleCk7XG5cdH1cbn1cbiIsIi8qKlxuICogTG9uZyBjb250ZW50IGZhZGUgbWl4aW5cbiAqXG4gKiBDcmVhdGVzIGEgZmFkaW5nIG92ZXJsYXkgdG8gc2lnbmlmeSB0aGF0IHRoZSBjb250ZW50IGlzIGxvbmdlclxuICogdGhhbiB0aGUgc3BhY2UgYWxsb3dzLlxuICovXG5cbkBtaXhpbiBsb25nLWNvbnRlbnQtZmFkZSgkZGlyZWN0aW9uOiByaWdodCwgJHNpemU6IDIwJSwgJGNvbG9yOiAjZmZmLCAkZWRnZTogMCwgJHotaW5kZXg6IGZhbHNlKSB7XG5cdGNvbnRlbnQ6IFwiXCI7XG5cdGRpc3BsYXk6IGJsb2NrO1xuXHRwb3NpdGlvbjogYWJzb2x1dGU7XG5cdC13ZWJraXQtdG91Y2gtY2FsbG91dDogbm9uZTtcblx0LXdlYmtpdC11c2VyLXNlbGVjdDogbm9uZTtcblx0LWtodG1sLXVzZXItc2VsZWN0OiBub25lO1xuXHQtbW96LXVzZXItc2VsZWN0OiBub25lO1xuXHQtbXMtdXNlci1zZWxlY3Q6IG5vbmU7XG5cdHVzZXItc2VsZWN0OiBub25lO1xuXHRwb2ludGVyLWV2ZW50czogbm9uZTtcblxuXHRAaWYgJHotaW5kZXgge1xuXHRcdHotaW5kZXg6ICR6LWluZGV4O1xuXHR9XG5cblx0QGlmICRkaXJlY3Rpb24gPT0gXCJib3R0b21cIiB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KHRvIHRvcCwgdHJhbnNwYXJlbnQsICRjb2xvciA5MCUpO1xuXHRcdGxlZnQ6ICRlZGdlO1xuXHRcdHJpZ2h0OiAkZWRnZTtcblx0XHR0b3A6ICRlZGdlO1xuXHRcdGJvdHRvbTogY2FsYygxMDAlIC0gJHNpemUpO1xuXHRcdHdpZHRoOiBhdXRvO1xuXHR9XG5cblx0QGlmICRkaXJlY3Rpb24gPT0gXCJ0b3BcIiB7XG5cdFx0YmFja2dyb3VuZDogbGluZWFyLWdyYWRpZW50KHRvIGJvdHRvbSwgdHJhbnNwYXJlbnQsICRjb2xvciA5MCUpO1xuXHRcdHRvcDogY2FsYygxMDAlIC0gJHNpemUpO1xuXHRcdGxlZnQ6ICRlZGdlO1xuXHRcdHJpZ2h0OiAkZWRnZTtcblx0XHRib3R0b206ICRlZGdlO1xuXHRcdHdpZHRoOiBhdXRvO1xuXHR9XG5cblx0QGlmICRkaXJlY3Rpb24gPT0gXCJsZWZ0XCIge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCh0byBsZWZ0LCB0cmFuc3BhcmVudCwgJGNvbG9yIDkwJSk7XG5cdFx0dG9wOiAkZWRnZTtcblx0XHRsZWZ0OiAkZWRnZTtcblx0XHRib3R0b206ICRlZGdlO1xuXHRcdHJpZ2h0OiBhdXRvO1xuXHRcdHdpZHRoOiAkc2l6ZTtcblx0XHRoZWlnaHQ6IGF1dG87XG5cdH1cblxuXHRAaWYgJGRpcmVjdGlvbiA9PSBcInJpZ2h0XCIge1xuXHRcdGJhY2tncm91bmQ6IGxpbmVhci1ncmFkaWVudCh0byByaWdodCwgdHJhbnNwYXJlbnQsICRjb2xvciA5MCUpO1xuXHRcdHRvcDogJGVkZ2U7XG5cdFx0Ym90dG9tOiAkZWRnZTtcblx0XHRyaWdodDogJGVkZ2U7XG5cdFx0bGVmdDogYXV0bztcblx0XHR3aWR0aDogJHNpemU7XG5cdFx0aGVpZ2h0OiBhdXRvO1xuXHR9XG59XG4iLCJAdXNlIFwiQHdvcmRwcmVzcy90aGVtZS9zcmMvcHJlYnVpbHQvY3NzL2Rlc2lnbi10b2tlbnMuY3NzXCIgYXMgKjtcbkB1c2UgXCJAd29yZHByZXNzL2FkbWluLXVpL2J1aWxkLXN0eWxlL3N0eWxlLmNzc1wiIGFzICo7XG5AdXNlIFwiQHdvcmRwcmVzcy9iYXNlLXN0eWxlcy9taXhpbnNcIiBhcyAqO1xuQHVzZSBcIkB3b3JkcHJlc3MvYmFzZS1zdHlsZXMvdmFyaWFibGVzXCIgYXMgKjtcblxuLmJvb3QtbGF5b3V0LWNvbnRhaW5lciAuYm9vdC1sYXlvdXQge1xuXHQvLyBPbiBtb2JpbGUgdGhlIG1haW4gY29udGVudCBhcmVhIGhhcyB0byBzY3JvbGwsIG90aGVyd2lzZSB5b3UgY2FuIGludm9rZVxuXHQvLyB0aGUgb3ZlcnNjcm9sbCBib3VuY2Ugb24gdGhlIG5vbi1zY3JvbGxpbmcgY29udGFpbmVyLCBmb3IgYSBiYWQgZXhwZXJpZW5jZS5cblx0QGluY2x1ZGUgYnJlYWstc21hbGwge1xuXHRcdHBvc2l0aW9uOiBhYnNvbHV0ZTtcblx0XHR0b3A6IDA7XG5cdFx0cmlnaHQ6IDA7XG5cdFx0Ym90dG9tOiAwO1xuXHRcdGxlZnQ6IDA7XG5cdFx0bWluLWhlaWdodDogY2FsYygxMDB2aCAtICN7JGFkbWluLWJhci1oZWlnaHQtYmlnfSk7XG5cdH1cblxuXHQvLyBUaGUgV1AgaGVhZGVyIGhlaWdodCBjaGFuZ2VzIGF0IHRoaXMgYnJlYWtwb2ludC5cblx0QGluY2x1ZGUgYnJlYWstbWVkaXVtIHtcblx0XHRtaW4taGVpZ2h0OiBjYWxjKDEwMHZoIC0gI3skYWRtaW4tYmFyLWhlaWdodH0pO1xuXG5cdFx0Ym9keTpoYXMoLmJvb3QtbGF5b3V0Lmhhcy1mdWxsLWNhbnZhcykgJiB7XG5cdFx0XHRtaW4taGVpZ2h0OiAxMDB2aDtcblx0XHR9XG5cdH1cblxuXHRpbWcge1xuXHRcdG1heC13aWR0aDogMTAwJTtcblx0XHRoZWlnaHQ6IGF1dG87XG5cdH1cbn1cblxuLmJvb3QtbGF5b3V0IC5jb21wb25lbnRzLWVkaXRvci1ub3RpY2VzX19zbmFja2JhciB7XG5cdHBvc2l0aW9uOiBmaXhlZDtcblx0cmlnaHQ6IDA7XG5cdGJvdHRvbTogMTZweDtcblx0cGFkZGluZy1sZWZ0OiAxNnB4O1xuXHRwYWRkaW5nLXJpZ2h0OiAxNnB4O1xufVxuIl19 */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css12));
var css22 = `/**
 * Colors
 */
@media (max-width: 782px) {
  * {
    view-transition-name: none !important;
  }
}
::view-transition-old(root),
::view-transition-new(root) {
  animation-duration: 250ms;
}

@media not (prefers-reduced-motion: reduce) {
  .boot-layout__canvas .interface-interface-skeleton__header {
    view-transition-name: boot--canvas-header;
  }
  .boot-layout__canvas .interface-interface-skeleton__sidebar {
    view-transition-name: boot--canvas-sidebar;
  }
  .boot-layout.has-full-canvas .boot-layout__canvas .boot-site-icon-link,
  .boot-layout:not(.has-full-canvas) .boot-site-hub .boot-site-icon-link {
    view-transition-name: boot--site-icon-link;
  }
  .boot-layout__stage {
    view-transition-name: boot--stage;
  }
  .boot-layout__inspector {
    view-transition-name: boot--inspector;
  }
  .boot-layout__canvas:not(.is-full-canvas),
  .boot-layout__canvas.is-full-canvas .interface-interface-skeleton__content {
    view-transition-name: boot--canvas;
  }
  @supports (-webkit-hyphens: none) and (not (-moz-appearance: none)) {
    .boot-layout__stage {
      view-transition-name: boot-safari--stage;
    }
    .boot-layout__inspector {
      view-transition-name: boot-safari--inspector;
    }
    .boot-layout__canvas:not(.is-full-canvas),
    .boot-layout__canvas.is-full-canvas .interface-interface-skeleton__content {
      view-transition-name: boot-safari--canvas;
    }
  }
  .components-popover:first-of-type {
    view-transition-name: boot--components-popover;
  }
}
::view-transition-group(boot--canvas-header),
::view-transition-group(boot--canvas-sidebar),
::view-transition-group(boot-safari--canvas),
::view-transition-group(boot--canvas) {
  z-index: 1;
}

::view-transition-group(boot--site-icon-link) {
  z-index: 2;
}

::view-transition-new(boot--site-icon-link),
::view-transition-old(boot--site-icon-link) {
  animation: none;
}

::view-transition-new(boot-safari--canvas),
::view-transition-old(boot-safari--canvas),
::view-transition-new(boot-safari--stage),
::view-transition-old(boot-safari--stage),
::view-transition-old(boot-safari--inspector),
::view-transition-new(boot-safari--inspector) {
  width: auto;
}

::view-transition-new(boot--canvas),
::view-transition-old(boot--canvas),
::view-transition-new(boot--stage),
::view-transition-old(boot--stage),
::view-transition-new(boot--inspector),
::view-transition-old(boot--inspector) {
  background: #fff;
  border-radius: 8px;
  width: 100%;
  height: 100%;
  object-fit: none;
  object-position: left top;
  overflow: hidden;
}

::view-transition-new(boot--canvas),
::view-transition-old(boot--canvas) {
  object-position: center top;
}

::view-transition-old(boot-safari--inspector):only-child,
::view-transition-old(boot--inspector):only-child,
::view-transition-old(boot-safari--stage):only-child,
::view-transition-old(boot--stage):only-child {
  animation-name: zoomOut;
  will-change: transform, opacity;
}

::view-transition-new(boot-safari--inspector):only-child,
::view-transition-new(boot--inspector):only-child,
::view-transition-new(boot-safari--stage):only-child,
::view-transition-new(boot--stage):only-child {
  animation-name: zoomIn;
  will-change: transform, opacity;
}

@keyframes zoomOut {
  from {
    transform: scale(1);
    opacity: 1;
  }
  to {
    transform: scale(0.9);
    opacity: 0;
  }
}
@keyframes zoomIn {
  from {
    transform: scale(0.95);
    opacity: 0;
  }
  to {
    transform: scale(1);
    opacity: 1;
  }
}
::view-transition-new(boot-safari--canvas):only-child,
::view-transition-new(boot--canvas):only-child {
  animation-name: slideFromRight;
  will-change: transform;
}

::view-transition-old(boot-safari--canvas):only-child,
::view-transition-old(boot--canvas):only-child {
  animation-name: slideToRight;
  will-change: transform;
}

@keyframes slideFromRight {
  from {
    transform: translateX(100vw);
  }
  to {
    transform: translateX(0);
  }
}
@keyframes slideToRight {
  from {
    transform: translateX(0);
  }
  to {
    transform: translateX(100vw);
  }
}
::view-transition-new(boot--canvas-header):only-child {
  animation-name: slideHeaderFromTop;
  will-change: transform;
}

::view-transition-old(boot--canvas-header):only-child {
  animation-name: slideHeaderToTop;
  will-change: transform;
}

@keyframes slideHeaderFromTop {
  from {
    transform: translateY(-100%);
  }
}
@keyframes slideHeaderToTop {
  to {
    transform: translateY(-100%);
  }
}
::view-transition-new(boot--canvas-sidebar):only-child {
  animation-name: slideSidebarFromRight;
  will-change: transform;
}

::view-transition-old(boot--canvas-sidebar):only-child {
  animation-name: slideSidebarToRight;
  will-change: transform;
}

@keyframes slideSidebarFromRight {
  from {
    transform: translateX(100%);
  }
}
@keyframes slideSidebarToRight {
  to {
    transform: translateX(100%);
  }
}
/*# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VSb290IjoiL2hvbWUvc3ZuL2NoZWNrb3V0cy9kZXZlbG9wLnN2bi53b3JkcHJlc3Mub3JnL3RydW5rL2d1dGVuYmVyZy9wYWNrYWdlcy9ib290L3NyYyIsInNvdXJjZXMiOlsiLi4vLi4vLi4vbm9kZV9tb2R1bGVzL0B3b3JkcHJlc3MvYmFzZS1zdHlsZXMvX2NvbG9ycy5zY3NzIiwidmlldy10cmFuc2l0aW9ucy5zY3NzIl0sIm5hbWVzIjpbXSwibWFwcGluZ3MiOiJBQUFBO0FBQUE7QUFBQTtBQ0lBO0VBQ0M7SUFDQzs7O0FBSUY7QUFBQTtFQUVDOzs7QUFHRDtFQUNDO0lBQ0M7O0VBR0Q7SUFDQzs7RUFHRDtBQUFBO0lBTUM7O0VBSUQ7SUFDQzs7RUFHRDtJQUNDOztFQUdEO0FBQUE7SUFHQzs7RUFLRDtJQUNDO01BQ0M7O0lBR0Q7TUFDQzs7SUFHRDtBQUFBO01BR0M7OztFQU1GO0lBQ0M7OztBQUlGO0FBQUE7QUFBQTtBQUFBO0VBSUM7OztBQUdEO0VBQ0M7OztBQUdEO0FBQUE7RUFFQzs7O0FBSUQ7QUFBQTtBQUFBO0FBQUE7QUFBQTtBQUFBO0VBTUM7OztBQUtEO0FBQUE7QUFBQTtBQUFBO0FBQUE7QUFBQTtFQU1DLFlEN0ZPO0VDOEZQO0VBQ0E7RUFDQTtFQUNBO0VBQ0E7RUFDQTs7O0FBR0Q7QUFBQTtFQUVDOzs7QUFHRDtBQUFBO0FBQUE7QUFBQTtFQUlDO0VBQ0E7OztBQUdEO0FBQUE7QUFBQTtBQUFBO0VBSUM7RUFDQTs7O0FBR0Q7RUFDQztJQUNDO0lBQ0E7O0VBR0Q7SUFDQztJQUNBOzs7QUFJRjtFQUNDO0lBQ0M7SUFDQTs7RUFHRDtJQUNDO0lBQ0E7OztBQUlGO0FBQUE7RUFFQztFQUNBOzs7QUFHRDtBQUFBO0VBRUM7RUFDQTs7O0FBR0Q7RUFDQztJQUlDOztFQUdEO0lBQ0M7OztBQUlGO0VBQ0M7SUFDQzs7RUFHRDtJQUNDOzs7QUFJRjtFQUNDO0VBQ0E7OztBQUdEO0VBQ0M7RUFDQTs7O0FBR0Q7RUFDQztJQUNDOzs7QUFJRjtFQUNDO0lBQ0M7OztBQUlGO0VBQ0M7RUFDQTs7O0FBR0Q7RUFDQztFQUNBOzs7QUFHRDtFQUNDO0lBQ0M7OztBQUlGO0VBQ0M7SUFDQyIsInNvdXJjZXNDb250ZW50IjpbIi8qKlxuICogQ29sb3JzXG4gKi9cblxuLy8gV29yZFByZXNzIGdyYXlzLlxuJGJsYWNrOiAjMDAwO1x0XHRcdC8vIFVzZSBvbmx5IHdoZW4geW91IHRydWx5IG5lZWQgcHVyZSBibGFjay4gRm9yIFVJLCB1c2UgJGdyYXktOTAwLlxuJGdyYXktOTAwOiAjMWUxZTFlO1xuJGdyYXktODAwOiAjMmYyZjJmO1xuJGdyYXktNzAwOiAjNzU3NTc1O1x0XHQvLyBNZWV0cyA0LjY6MSAoNC41OjEgaXMgbWluaW11bSkgdGV4dCBjb250cmFzdCBhZ2FpbnN0IHdoaXRlLlxuJGdyYXktNjAwOiAjOTQ5NDk0O1x0XHQvLyBNZWV0cyAzOjEgVUkgb3IgbGFyZ2UgdGV4dCBjb250cmFzdCBhZ2FpbnN0IHdoaXRlLlxuJGdyYXktNDAwOiAjY2NjO1xuJGdyYXktMzAwOiAjZGRkO1x0XHQvLyBVc2VkIGZvciBtb3N0IGJvcmRlcnMuXG4kZ3JheS0yMDA6ICNlMGUwZTA7XHRcdC8vIFVzZWQgc3BhcmluZ2x5IGZvciBsaWdodCBib3JkZXJzLlxuJGdyYXktMTAwOiAjZjBmMGYwO1x0XHQvLyBVc2VkIGZvciBsaWdodCBncmF5IGJhY2tncm91bmRzLlxuJHdoaXRlOiAjZmZmO1xuXG4vLyBPcGFjaXRpZXMgJiBhZGRpdGlvbmFsIGNvbG9ycy5cbiRkYXJrLWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJGdyYXktOTAwLCAwLjYyKTtcbiRtZWRpdW0tZ3JheS1wbGFjZWhvbGRlcjogcmdiYSgkZ3JheS05MDAsIDAuNTUpO1xuJGxpZ2h0LWdyYXktcGxhY2Vob2xkZXI6IHJnYmEoJHdoaXRlLCAwLjY1KTtcblxuLy8gQWxlcnQgY29sb3JzLlxuJGFsZXJ0LXllbGxvdzogI2YwYjg0OTtcbiRhbGVydC1yZWQ6ICNjYzE4MTg7XG4kYWxlcnQtZ3JlZW46ICM0YWI4NjY7XG5cbi8vIERlcHJlY2F0ZWQsIHBsZWFzZSBhdm9pZCB1c2luZyB0aGVzZS5cbiRkYXJrLXRoZW1lLWZvY3VzOiAkd2hpdGU7XHQvLyBGb2N1cyBjb2xvciB3aGVuIHRoZSB0aGVtZSBpcyBkYXJrLlxuIiwiQHVzZSBcIkB3b3JkcHJlc3MvYmFzZS1zdHlsZXMvY29sb3JzXCI7XG5cbi8vIERpc2FibGUgdmlldyB0cmFuc2l0aW9ucyBvbiBtb2JpbGUgZGV2aWNlc1xuLy8gdG8gYXZvaWQgY29uZmxpY3RzIHdpdGggc2lkZWJhciBuYXZpZ2F0aW9uLlxuQG1lZGlhIChtYXgtd2lkdGg6IDc4MnB4KSB7XG5cdCoge1xuXHRcdHZpZXctdHJhbnNpdGlvbi1uYW1lOiBub25lICFpbXBvcnRhbnQ7XG5cdH1cbn1cblxuOjp2aWV3LXRyYW5zaXRpb24tb2xkKHJvb3QpLFxuOjp2aWV3LXRyYW5zaXRpb24tbmV3KHJvb3QpIHtcblx0YW5pbWF0aW9uLWR1cmF0aW9uOiAyNTBtcztcbn1cblxuQG1lZGlhIG5vdCAocHJlZmVycy1yZWR1Y2VkLW1vdGlvbjogcmVkdWNlKSB7XG5cdC5ib290LWxheW91dF9fY2FudmFzIC5pbnRlcmZhY2UtaW50ZXJmYWNlLXNrZWxldG9uX19oZWFkZXIge1xuXHRcdHZpZXctdHJhbnNpdGlvbi1uYW1lOiBib290LS1jYW52YXMtaGVhZGVyO1xuXHR9XG5cblx0LmJvb3QtbGF5b3V0X19jYW52YXMgLmludGVyZmFjZS1pbnRlcmZhY2Utc2tlbGV0b25fX3NpZGViYXIge1xuXHRcdHZpZXctdHJhbnNpdGlvbi1uYW1lOiBib290LS1jYW52YXMtc2lkZWJhcjtcblx0fVxuXG5cdC5ib290LWxheW91dC5oYXMtZnVsbC1jYW52YXNcblx0LmJvb3QtbGF5b3V0X19jYW52YXNcblx0LmJvb3Qtc2l0ZS1pY29uLWxpbmssXG5cdC5ib290LWxheW91dDpub3QoLmhhcy1mdWxsLWNhbnZhcylcblx0LmJvb3Qtc2l0ZS1odWJcblx0LmJvb3Qtc2l0ZS1pY29uLWxpbmsge1xuXHRcdHZpZXctdHJhbnNpdGlvbi1uYW1lOiBib290LS1zaXRlLWljb24tbGluaztcblx0fVxuXG5cdC8vIERlZmF1bHQgKG5vbi1TYWZhcmkpIHZpZXcgdHJhbnNpdGlvbiBuYW1lc1xuXHQuYm9vdC1sYXlvdXRfX3N0YWdlIHtcblx0XHR2aWV3LXRyYW5zaXRpb24tbmFtZTogYm9vdC0tc3RhZ2U7XG5cdH1cblxuXHQuYm9vdC1sYXlvdXRfX2luc3BlY3RvciB7XG5cdFx0dmlldy10cmFuc2l0aW9uLW5hbWU6IGJvb3QtLWluc3BlY3Rvcjtcblx0fVxuXG5cdC5ib290LWxheW91dF9fY2FudmFzOm5vdCguaXMtZnVsbC1jYW52YXMpLFxuXHQuYm9vdC1sYXlvdXRfX2NhbnZhcy5pcy1mdWxsLWNhbnZhc1xuXHQuaW50ZXJmYWNlLWludGVyZmFjZS1za2VsZXRvbl9fY29udGVudCB7XG5cdFx0dmlldy10cmFuc2l0aW9uLW5hbWU6IGJvb3QtLWNhbnZhcztcblx0fVxuXG5cdC8vIFNhZmFyaS1zcGVjaWZpYyB2aWV3IHRyYW5zaXRpb24gbmFtZXNcblx0Ly8gVXNlcyBDU1MgZmVhdHVyZSBkZXRlY3Rpb24gaW5zdGVhZCBvZiAuaXMtc2FmYXJpIGNsYXNzXG5cdEBzdXBwb3J0cyAoLXdlYmtpdC1oeXBoZW5zOm5vbmUpIGFuZCAobm90ICgtbW96LWFwcGVhcmFuY2U6bm9uZSkpIHtcblx0XHQuYm9vdC1sYXlvdXRfX3N0YWdlIHtcblx0XHRcdHZpZXctdHJhbnNpdGlvbi1uYW1lOiBib290LXNhZmFyaS0tc3RhZ2U7XG5cdFx0fVxuXG5cdFx0LmJvb3QtbGF5b3V0X19pbnNwZWN0b3Ige1xuXHRcdFx0dmlldy10cmFuc2l0aW9uLW5hbWU6IGJvb3Qtc2FmYXJpLS1pbnNwZWN0b3I7XG5cdFx0fVxuXG5cdFx0LmJvb3QtbGF5b3V0X19jYW52YXM6bm90KC5pcy1mdWxsLWNhbnZhcyksXG5cdFx0LmJvb3QtbGF5b3V0X19jYW52YXMuaXMtZnVsbC1jYW52YXNcblx0XHQuaW50ZXJmYWNlLWludGVyZmFjZS1za2VsZXRvbl9fY29udGVudCB7XG5cdFx0XHR2aWV3LXRyYW5zaXRpb24tbmFtZTogYm9vdC1zYWZhcmktLWNhbnZhcztcblx0XHR9XG5cdH1cblxuXHQvLyBGb3IgYW55IHBvcG92ZXIgdGhhdCBzdGF5cyBvcGVuIGFjcm9zcyBhIHF1ZXJ5IGNoYW5nZS5cblx0Ly8gTmFtaW5nIGl0IGF2b2lkcyB0aGUgc3RhZ2Ugb3ZlcmxheWluZyBpdC5cblx0LmNvbXBvbmVudHMtcG9wb3ZlcjpmaXJzdC1vZi10eXBlIHtcblx0XHR2aWV3LXRyYW5zaXRpb24tbmFtZTogYm9vdC0tY29tcG9uZW50cy1wb3BvdmVyO1xuXHR9XG59XG5cbjo6dmlldy10cmFuc2l0aW9uLWdyb3VwKGJvb3QtLWNhbnZhcy1oZWFkZXIpLFxuOjp2aWV3LXRyYW5zaXRpb24tZ3JvdXAoYm9vdC0tY2FudmFzLXNpZGViYXIpLFxuOjp2aWV3LXRyYW5zaXRpb24tZ3JvdXAoYm9vdC1zYWZhcmktLWNhbnZhcyksXG46OnZpZXctdHJhbnNpdGlvbi1ncm91cChib290LS1jYW52YXMpIHtcblx0ei1pbmRleDogMTtcbn1cblxuOjp2aWV3LXRyYW5zaXRpb24tZ3JvdXAoYm9vdC0tc2l0ZS1pY29uLWxpbmspIHtcblx0ei1pbmRleDogMjtcbn1cblxuOjp2aWV3LXRyYW5zaXRpb24tbmV3KGJvb3QtLXNpdGUtaWNvbi1saW5rKSxcbjo6dmlldy10cmFuc2l0aW9uLW9sZChib290LS1zaXRlLWljb24tbGluaykge1xuXHRhbmltYXRpb246IG5vbmU7XG59XG5cbi8vIFNhZmFyaS1zcGVjaWZpYyBwc2V1ZG8tZWxlbWVudCBzdHlsZXMgd2l0aCB3aWR0aDogYXV0byBmaXhcbjo6dmlldy10cmFuc2l0aW9uLW5ldyhib290LXNhZmFyaS0tY2FudmFzKSxcbjo6dmlldy10cmFuc2l0aW9uLW9sZChib290LXNhZmFyaS0tY2FudmFzKSxcbjo6dmlldy10cmFuc2l0aW9uLW5ldyhib290LXNhZmFyaS0tc3RhZ2UpLFxuOjp2aWV3LXRyYW5zaXRpb24tb2xkKGJvb3Qtc2FmYXJpLS1zdGFnZSksXG46OnZpZXctdHJhbnNpdGlvbi1vbGQoYm9vdC1zYWZhcmktLWluc3BlY3RvciksXG46OnZpZXctdHJhbnNpdGlvbi1uZXcoYm9vdC1zYWZhcmktLWluc3BlY3Rvcikge1xuXHR3aWR0aDogYXV0bztcbn1cblxuLy8gU2FmYXJpIHRyaXBzIHVwIHdpdGggdXNpbmcgb2JqZWN0IGZpdCBvbiB0aGUgcHNldWRvIGltYWdlcyBhbmQgZmlsbGluZyBvdXRcbi8vIGJhY2tncm91bmQuXG46OnZpZXctdHJhbnNpdGlvbi1uZXcoYm9vdC0tY2FudmFzKSxcbjo6dmlldy10cmFuc2l0aW9uLW9sZChib290LS1jYW52YXMpLFxuOjp2aWV3LXRyYW5zaXRpb24tbmV3KGJvb3QtLXN0YWdlKSxcbjo6dmlldy10cmFuc2l0aW9uLW9sZChib290LS1zdGFnZSksXG46OnZpZXctdHJhbnNpdGlvbi1uZXcoYm9vdC0taW5zcGVjdG9yKSxcbjo6dmlldy10cmFuc2l0aW9uLW9sZChib290LS1pbnNwZWN0b3IpIHtcblx0YmFja2dyb3VuZDogY29sb3JzLiR3aGl0ZTtcblx0Ym9yZGVyLXJhZGl1czogOHB4O1xuXHR3aWR0aDogMTAwJTtcblx0aGVpZ2h0OiAxMDAlO1xuXHRvYmplY3QtZml0OiBub25lO1xuXHRvYmplY3QtcG9zaXRpb246IGxlZnQgdG9wO1xuXHRvdmVyZmxvdzogaGlkZGVuO1xufVxuXG46OnZpZXctdHJhbnNpdGlvbi1uZXcoYm9vdC0tY2FudmFzKSxcbjo6dmlldy10cmFuc2l0aW9uLW9sZChib290LS1jYW52YXMpIHtcblx0b2JqZWN0LXBvc2l0aW9uOiBjZW50ZXIgdG9wO1xufVxuXG46OnZpZXctdHJhbnNpdGlvbi1vbGQoYm9vdC1zYWZhcmktLWluc3BlY3Rvcik6b25seS1jaGlsZCxcbjo6dmlldy10cmFuc2l0aW9uLW9sZChib290LS1pbnNwZWN0b3IpOm9ubHktY2hpbGQsXG46OnZpZXctdHJhbnNpdGlvbi1vbGQoYm9vdC1zYWZhcmktLXN0YWdlKTpvbmx5LWNoaWxkLFxuOjp2aWV3LXRyYW5zaXRpb24tb2xkKGJvb3QtLXN0YWdlKTpvbmx5LWNoaWxkIHtcblx0YW5pbWF0aW9uLW5hbWU6IHpvb21PdXQ7XG5cdHdpbGwtY2hhbmdlOiB0cmFuc2Zvcm0sIG9wYWNpdHk7XG59XG5cbjo6dmlldy10cmFuc2l0aW9uLW5ldyhib290LXNhZmFyaS0taW5zcGVjdG9yKTpvbmx5LWNoaWxkLFxuOjp2aWV3LXRyYW5zaXRpb24tbmV3KGJvb3QtLWluc3BlY3Rvcik6b25seS1jaGlsZCxcbjo6dmlldy10cmFuc2l0aW9uLW5ldyhib290LXNhZmFyaS0tc3RhZ2UpOm9ubHktY2hpbGQsXG46OnZpZXctdHJhbnNpdGlvbi1uZXcoYm9vdC0tc3RhZ2UpOm9ubHktY2hpbGQge1xuXHRhbmltYXRpb24tbmFtZTogem9vbUluO1xuXHR3aWxsLWNoYW5nZTogdHJhbnNmb3JtLCBvcGFjaXR5O1xufVxuXG5Aa2V5ZnJhbWVzIHpvb21PdXQge1xuXHRmcm9tIHtcblx0XHR0cmFuc2Zvcm06IHNjYWxlKDEpO1xuXHRcdG9wYWNpdHk6IDE7XG5cdH1cblxuXHR0byB7XG5cdFx0dHJhbnNmb3JtOiBzY2FsZSgwLjkpO1xuXHRcdG9wYWNpdHk6IDA7XG5cdH1cbn1cblxuQGtleWZyYW1lcyB6b29tSW4ge1xuXHRmcm9tIHtcblx0XHR0cmFuc2Zvcm06IHNjYWxlKDAuOTUpO1xuXHRcdG9wYWNpdHk6IDA7XG5cdH1cblxuXHR0byB7XG5cdFx0dHJhbnNmb3JtOiBzY2FsZSgxKTtcblx0XHRvcGFjaXR5OiAxO1xuXHR9XG59XG5cbjo6dmlldy10cmFuc2l0aW9uLW5ldyhib290LXNhZmFyaS0tY2FudmFzKTpvbmx5LWNoaWxkLFxuOjp2aWV3LXRyYW5zaXRpb24tbmV3KGJvb3QtLWNhbnZhcyk6b25seS1jaGlsZCB7XG5cdGFuaW1hdGlvbi1uYW1lOiBzbGlkZUZyb21SaWdodDtcblx0d2lsbC1jaGFuZ2U6IHRyYW5zZm9ybTtcbn1cblxuOjp2aWV3LXRyYW5zaXRpb24tb2xkKGJvb3Qtc2FmYXJpLS1jYW52YXMpOm9ubHktY2hpbGQsXG46OnZpZXctdHJhbnNpdGlvbi1vbGQoYm9vdC0tY2FudmFzKTpvbmx5LWNoaWxkIHtcblx0YW5pbWF0aW9uLW5hbWU6IHNsaWRlVG9SaWdodDtcblx0d2lsbC1jaGFuZ2U6IHRyYW5zZm9ybTtcbn1cblxuQGtleWZyYW1lcyBzbGlkZUZyb21SaWdodCB7XG5cdGZyb20ge1xuXHRcdC8vIFNob3VsZCBpZGVhbGx5IGJlIDEwMCUgKyAxNnB4LCBidXQgd2UgYWxzbyBuZWVkIHRvIHRha2UgaW50byBhY2NvdW50XG5cdFx0Ly8gdGhhdCB0aGUgY2FudmFzIGNhbiBiZSB0aGUgZWRpdG9yLWludGVyZmFjZS1za2VsZXRvbl9fY29udGVudCwgd2hpY2hcblx0XHQvLyBub3QgcGxhY2VkIG9uIHRoZSByaWdodCBzaWRlLlxuXHRcdHRyYW5zZm9ybTogdHJhbnNsYXRlWCgxMDB2dyk7XG5cdH1cblxuXHR0byB7XG5cdFx0dHJhbnNmb3JtOiB0cmFuc2xhdGVYKDApO1xuXHR9XG59XG5cbkBrZXlmcmFtZXMgc2xpZGVUb1JpZ2h0IHtcblx0ZnJvbSB7XG5cdFx0dHJhbnNmb3JtOiB0cmFuc2xhdGVYKDApO1xuXHR9XG5cblx0dG8ge1xuXHRcdHRyYW5zZm9ybTogdHJhbnNsYXRlWCgxMDB2dyk7XG5cdH1cbn1cblxuOjp2aWV3LXRyYW5zaXRpb24tbmV3KGJvb3QtLWNhbnZhcy1oZWFkZXIpOm9ubHktY2hpbGQge1xuXHRhbmltYXRpb24tbmFtZTogc2xpZGVIZWFkZXJGcm9tVG9wO1xuXHR3aWxsLWNoYW5nZTogdHJhbnNmb3JtO1xufVxuXG46OnZpZXctdHJhbnNpdGlvbi1vbGQoYm9vdC0tY2FudmFzLWhlYWRlcik6b25seS1jaGlsZCB7XG5cdGFuaW1hdGlvbi1uYW1lOiBzbGlkZUhlYWRlclRvVG9wO1xuXHR3aWxsLWNoYW5nZTogdHJhbnNmb3JtO1xufVxuXG5Aa2V5ZnJhbWVzIHNsaWRlSGVhZGVyRnJvbVRvcCB7XG5cdGZyb20ge1xuXHRcdHRyYW5zZm9ybTogdHJhbnNsYXRlWSgtMTAwJSk7XG5cdH1cbn1cblxuQGtleWZyYW1lcyBzbGlkZUhlYWRlclRvVG9wIHtcblx0dG8ge1xuXHRcdHRyYW5zZm9ybTogdHJhbnNsYXRlWSgtMTAwJSk7XG5cdH1cbn1cblxuOjp2aWV3LXRyYW5zaXRpb24tbmV3KGJvb3QtLWNhbnZhcy1zaWRlYmFyKTpvbmx5LWNoaWxkIHtcblx0YW5pbWF0aW9uLW5hbWU6IHNsaWRlU2lkZWJhckZyb21SaWdodDtcblx0d2lsbC1jaGFuZ2U6IHRyYW5zZm9ybTtcbn1cblxuOjp2aWV3LXRyYW5zaXRpb24tb2xkKGJvb3QtLWNhbnZhcy1zaWRlYmFyKTpvbmx5LWNoaWxkIHtcblx0YW5pbWF0aW9uLW5hbWU6IHNsaWRlU2lkZWJhclRvUmlnaHQ7XG5cdHdpbGwtY2hhbmdlOiB0cmFuc2Zvcm07XG59XG5cbkBrZXlmcmFtZXMgc2xpZGVTaWRlYmFyRnJvbVJpZ2h0IHtcblx0ZnJvbSB7XG5cdFx0dHJhbnNmb3JtOiB0cmFuc2xhdGVYKDEwMCUpO1xuXHR9XG59XG5cbkBrZXlmcmFtZXMgc2xpZGVTaWRlYmFyVG9SaWdodCB7XG5cdHRvIHtcblx0XHR0cmFuc2Zvcm06IHRyYW5zbGF0ZVgoMTAwJSk7XG5cdH1cbn1cbiJdfQ== */`;
document.head.appendChild(document.createElement("style")).appendChild(document.createTextNode(css22));
export {
  init,
  initSinglePage,
  store
};