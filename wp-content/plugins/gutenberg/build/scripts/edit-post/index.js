var wp;
(wp ||= {}).editPost = (() => {
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
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

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

  // package-external:@wordpress/deprecated
  var require_deprecated = __commonJS({
    "package-external:@wordpress/deprecated"(exports, module) {
      module.exports = window.wp.deprecated;
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

  // package-external:@wordpress/preferences
  var require_preferences = __commonJS({
    "package-external:@wordpress/preferences"(exports, module) {
      module.exports = window.wp.preferences;
    }
  });

  // package-external:@wordpress/widgets
  var require_widgets = __commonJS({
    "package-external:@wordpress/widgets"(exports, module) {
      module.exports = window.wp.widgets;
    }
  });

  // package-external:@wordpress/editor
  var require_editor = __commonJS({
    "package-external:@wordpress/editor"(exports, module) {
      module.exports = window.wp.editor;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
    }
  });

  // package-external:@wordpress/block-editor
  var require_block_editor = __commonJS({
    "package-external:@wordpress/block-editor"(exports, module) {
      module.exports = window.wp.blockEditor;
    }
  });

  // package-external:@wordpress/style-engine
  var require_style_engine = __commonJS({
    "package-external:@wordpress/style-engine"(exports, module) {
      module.exports = window.wp.styleEngine;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/plugins
  var require_plugins = __commonJS({
    "package-external:@wordpress/plugins"(exports, module) {
      module.exports = window.wp.plugins;
    }
  });

  // package-external:@wordpress/primitives
  var require_primitives = __commonJS({
    "package-external:@wordpress/primitives"(exports, module) {
      module.exports = window.wp.primitives;
    }
  });

  // package-external:@wordpress/notices
  var require_notices = __commonJS({
    "package-external:@wordpress/notices"(exports, module) {
      module.exports = window.wp.notices;
    }
  });

  // package-external:@wordpress/commands
  var require_commands = __commonJS({
    "package-external:@wordpress/commands"(exports, module) {
      module.exports = window.wp.commands;
    }
  });

  // package-external:@wordpress/url
  var require_url = __commonJS({
    "package-external:@wordpress/url"(exports, module) {
      module.exports = window.wp.url;
    }
  });

  // package-external:@wordpress/html-entities
  var require_html_entities = __commonJS({
    "package-external:@wordpress/html-entities"(exports, module) {
      module.exports = window.wp.htmlEntities;
    }
  });

  // package-external:@wordpress/core-data
  var require_core_data = __commonJS({
    "package-external:@wordpress/core-data"(exports, module) {
      module.exports = window.wp.coreData;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
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

  // package-external:@wordpress/api-fetch
  var require_api_fetch = __commonJS({
    "package-external:@wordpress/api-fetch"(exports, module) {
      module.exports = window.wp.apiFetch;
    }
  });

  // package-external:@wordpress/hooks
  var require_hooks = __commonJS({
    "package-external:@wordpress/hooks"(exports, module) {
      module.exports = window.wp.hooks;
    }
  });

  // package-external:@wordpress/keycodes
  var require_keycodes = __commonJS({
    "package-external:@wordpress/keycodes"(exports, module) {
      module.exports = window.wp.keycodes;
    }
  });

  // packages/edit-post/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    PluginBlockSettingsMenuItem: () => PluginBlockSettingsMenuItem,
    PluginDocumentSettingPanel: () => PluginDocumentSettingPanel,
    PluginMoreMenuItem: () => PluginMoreMenuItem,
    PluginPostPublishPanel: () => PluginPostPublishPanel,
    PluginPostStatusInfo: () => PluginPostStatusInfo,
    PluginPrePublishPanel: () => PluginPrePublishPanel,
    PluginSidebar: () => PluginSidebar,
    PluginSidebarMoreMenuItem: () => PluginSidebarMoreMenuItem,
    __experimentalFullscreenModeClose: () => fullscreen_mode_close_default,
    __experimentalMainDashboardButton: () => __experimentalMainDashboardButton,
    __experimentalPluginPostExcerpt: () => __experimentalPluginPostExcerpt,
    initializeEditor: () => initializeEditor,
    reinitializeEditor: () => reinitializeEditor,
    store: () => store
  });
  var import_blocks3 = __toESM(require_blocks());
  var import_block_library2 = __toESM(require_block_library());
  var import_deprecated4 = __toESM(require_deprecated());
  var import_element13 = __toESM(require_element());
  var import_data26 = __toESM(require_data());
  var import_preferences11 = __toESM(require_preferences());
  var import_widgets = __toESM(require_widgets());
  var import_editor20 = __toESM(require_editor());

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

  // packages/edit-post/build-module/components/layout/index.js
  var import_editor18 = __toESM(require_editor());
  var import_data25 = __toESM(require_data());
  var import_block_editor2 = __toESM(require_block_editor());

  // packages/global-styles-engine/build-module/utils/common.js
  var import_style_engine = __toESM(require_style_engine());
  var ROOT_BLOCK_SELECTOR = "body";
  var ROOT_CSS_PROPERTIES_SELECTOR = ":root";

  // packages/global-styles-engine/build-module/core/render.js
  var import_blocks = __toESM(require_blocks());
  var import_style_engine2 = __toESM(require_style_engine());
  var import_data = __toESM(require_data());

  // packages/global-styles-engine/build-module/utils/spacing.js
  function getSpacingPresetCssVar(value) {
    if (!value) {
      return;
    }
    const slug = value.match(/var:preset\|spacing\|(.+)/);
    if (!slug) {
      return value;
    }
    return `var(--wp--preset--spacing--${slug[1]})`;
  }

  // packages/global-styles-engine/build-module/utils/gap.js
  function getGapBoxControlValueFromStyle(blockGapValue) {
    if (!blockGapValue) {
      return null;
    }
    const isValueString = typeof blockGapValue === "string";
    return {
      top: isValueString ? blockGapValue : blockGapValue?.top,
      left: isValueString ? blockGapValue : blockGapValue?.left
    };
  }
  function getGapCSSValue(blockGapValue, defaultValue = "0") {
    const blockGapBoxControlValue = getGapBoxControlValueFromStyle(blockGapValue);
    if (!blockGapBoxControlValue) {
      return null;
    }
    const row = getSpacingPresetCssVar(blockGapBoxControlValue?.top) || defaultValue;
    const column = getSpacingPresetCssVar(blockGapBoxControlValue?.left) || defaultValue;
    return row === column ? row : `${row} ${column}`;
  }

  // packages/global-styles-engine/build-module/utils/layout.js
  var LAYOUT_DEFINITIONS = {
    default: {
      name: "default",
      slug: "flow",
      className: "is-layout-flow",
      baseStyles: [
        {
          selector: " > .alignleft",
          rules: {
            float: "left",
            "margin-inline-start": "0",
            "margin-inline-end": "2em"
          }
        },
        {
          selector: " > .alignright",
          rules: {
            float: "right",
            "margin-inline-start": "2em",
            "margin-inline-end": "0"
          }
        },
        {
          selector: " > .aligncenter",
          rules: {
            "margin-left": "auto !important",
            "margin-right": "auto !important"
          }
        }
      ],
      spacingStyles: [
        {
          selector: " > :first-child",
          rules: {
            "margin-block-start": "0"
          }
        },
        {
          selector: " > :last-child",
          rules: {
            "margin-block-end": "0"
          }
        },
        {
          selector: " > *",
          rules: {
            "margin-block-start": null,
            "margin-block-end": "0"
          }
        }
      ]
    },
    constrained: {
      name: "constrained",
      slug: "constrained",
      className: "is-layout-constrained",
      baseStyles: [
        {
          selector: " > .alignleft",
          rules: {
            float: "left",
            "margin-inline-start": "0",
            "margin-inline-end": "2em"
          }
        },
        {
          selector: " > .alignright",
          rules: {
            float: "right",
            "margin-inline-start": "2em",
            "margin-inline-end": "0"
          }
        },
        {
          selector: " > .aligncenter",
          rules: {
            "margin-left": "auto !important",
            "margin-right": "auto !important"
          }
        },
        {
          selector: " > :where(:not(.alignleft):not(.alignright):not(.alignfull))",
          rules: {
            "max-width": "var(--wp--style--global--content-size)",
            "margin-left": "auto !important",
            "margin-right": "auto !important"
          }
        },
        {
          selector: " > .alignwide",
          rules: {
            "max-width": "var(--wp--style--global--wide-size)"
          }
        }
      ],
      spacingStyles: [
        {
          selector: " > :first-child",
          rules: {
            "margin-block-start": "0"
          }
        },
        {
          selector: " > :last-child",
          rules: {
            "margin-block-end": "0"
          }
        },
        {
          selector: " > *",
          rules: {
            "margin-block-start": null,
            "margin-block-end": "0"
          }
        }
      ]
    },
    flex: {
      name: "flex",
      slug: "flex",
      className: "is-layout-flex",
      displayMode: "flex",
      baseStyles: [
        {
          selector: "",
          rules: {
            "flex-wrap": "wrap",
            "align-items": "center"
          }
        },
        {
          selector: " > :is(*, div)",
          // :is(*, div) instead of just * increases the specificity by 001.
          rules: {
            margin: "0"
          }
        }
      ],
      spacingStyles: [
        {
          selector: "",
          rules: {
            gap: null
          }
        }
      ]
    },
    grid: {
      name: "grid",
      slug: "grid",
      className: "is-layout-grid",
      displayMode: "grid",
      baseStyles: [
        {
          selector: " > :is(*, div)",
          // :is(*, div) instead of just * increases the specificity by 001.
          rules: {
            margin: "0"
          }
        }
      ],
      spacingStyles: [
        {
          selector: "",
          rules: {
            gap: null
          }
        }
      ]
    }
  };

  // packages/global-styles-engine/build-module/core/render.js
  function getLayoutStyles({
    layoutDefinitions = LAYOUT_DEFINITIONS,
    style,
    selector,
    hasBlockGapSupport,
    hasFallbackGapSupport,
    fallbackGapValue
  }) {
    let ruleset = "";
    let gapValue = hasBlockGapSupport ? getGapCSSValue(style?.spacing?.blockGap) : "";
    if (hasFallbackGapSupport) {
      if (selector === ROOT_BLOCK_SELECTOR) {
        gapValue = !gapValue ? "0.5em" : gapValue;
      } else if (!hasBlockGapSupport && fallbackGapValue) {
        gapValue = fallbackGapValue;
      }
    }
    if (gapValue && layoutDefinitions) {
      Object.values(layoutDefinitions).forEach(
        ({ className, name, spacingStyles }) => {
          if (!hasBlockGapSupport && "flex" !== name && "grid" !== name) {
            return;
          }
          if (spacingStyles?.length) {
            spacingStyles.forEach((spacingStyle) => {
              const declarations = [];
              if (spacingStyle.rules) {
                Object.entries(spacingStyle.rules).forEach(
                  ([cssProperty, cssValue]) => {
                    declarations.push(
                      `${cssProperty}: ${cssValue ? cssValue : gapValue}`
                    );
                  }
                );
              }
              if (declarations.length) {
                let combinedSelector = "";
                if (!hasBlockGapSupport) {
                  combinedSelector = selector === ROOT_BLOCK_SELECTOR ? `:where(.${className}${spacingStyle?.selector || ""})` : `:where(${selector}.${className}${spacingStyle?.selector || ""})`;
                } else {
                  combinedSelector = selector === ROOT_BLOCK_SELECTOR ? `:root :where(.${className})${spacingStyle?.selector || ""}` : `:root :where(${selector}-${className})${spacingStyle?.selector || ""}`;
                }
                ruleset += `${combinedSelector} { ${declarations.join(
                  "; "
                )}; }`;
              }
            });
          }
        }
      );
      if (selector === ROOT_BLOCK_SELECTOR && hasBlockGapSupport) {
        ruleset += `${ROOT_CSS_PROPERTIES_SELECTOR} { --wp--style--block-gap: ${gapValue}; }`;
      }
    }
    if (selector === ROOT_BLOCK_SELECTOR && layoutDefinitions) {
      const validDisplayModes = ["block", "flex", "grid"];
      Object.values(layoutDefinitions).forEach(
        ({ className, displayMode, baseStyles }) => {
          if (displayMode && validDisplayModes.includes(displayMode)) {
            ruleset += `${selector} .${className} { display:${displayMode}; }`;
          }
          if (baseStyles?.length) {
            baseStyles.forEach((baseStyle) => {
              const declarations = [];
              if (baseStyle.rules) {
                Object.entries(baseStyle.rules).forEach(
                  ([cssProperty, cssValue]) => {
                    declarations.push(
                      `${cssProperty}: ${cssValue}`
                    );
                  }
                );
              }
              if (declarations.length) {
                const combinedSelector = `.${className}${baseStyle?.selector || ""}`;
                ruleset += `${combinedSelector} { ${declarations.join(
                  "; "
                )}; }`;
              }
            });
          }
        }
      );
    }
    return ruleset;
  }

  // packages/edit-post/build-module/components/layout/index.js
  var import_plugins = __toESM(require_plugins());
  var import_i18n14 = __toESM(require_i18n());
  var import_element12 = __toESM(require_element());

  // packages/icons/build-module/library/arrow-up-left.js
  var import_primitives = __toESM(require_primitives());
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  var arrow_up_left_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives.Path, { d: "M14 6H6v8h1.5V8.5L17 18l1-1-9.5-9.5H14V6Z" }) });

  // packages/icons/build-module/library/chevron-down.js
  var import_primitives2 = __toESM(require_primitives());
  var import_jsx_runtime3 = __toESM(require_jsx_runtime());
  var chevron_down_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives2.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives2.Path, { d: "M17.5 11.6L12 16l-5.5-4.4.9-1.2L12 14l4.5-3.6 1 1.2z" }) });

  // packages/icons/build-module/library/chevron-up.js
  var import_primitives3 = __toESM(require_primitives());
  var import_jsx_runtime4 = __toESM(require_jsx_runtime());
  var chevron_up_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives3.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives3.Path, { d: "M6.5 12.4L12 8l5.5 4.4-.9 1.2L12 10l-4.5 3.6-1-1.2z" }) });

  // packages/icons/build-module/library/fullscreen.js
  var import_primitives4 = __toESM(require_primitives());
  var import_jsx_runtime5 = __toESM(require_jsx_runtime());
  var fullscreen_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives4.Path, { d: "M6 4a2 2 0 0 0-2 2v3h1.5V6a.5.5 0 0 1 .5-.5h3V4H6Zm3 14.5H6a.5.5 0 0 1-.5-.5v-3H4v3a2 2 0 0 0 2 2h3v-1.5Zm6 1.5v-1.5h3a.5.5 0 0 0 .5-.5v-3H20v3a2 2 0 0 1-2 2h-3Zm3-16a2 2 0 0 1 2 2v3h-1.5V6a.5.5 0 0 0-.5-.5h-3V4h3Z" }) });

  // packages/icons/build-module/library/wordpress.js
  var import_primitives5 = __toESM(require_primitives());
  var import_jsx_runtime6 = __toESM(require_jsx_runtime());
  var wordpress_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "-2 -2 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives5.Path, { d: "M20 10c0-5.51-4.49-10-10-10C4.48 0 0 4.49 0 10c0 5.52 4.48 10 10 10 5.51 0 10-4.48 10-10zM7.78 15.37L4.37 6.22c.55-.02 1.17-.08 1.17-.08.5-.06.44-1.13-.06-1.11 0 0-1.45.11-2.37.11-.18 0-.37 0-.58-.01C4.12 2.69 6.87 1.11 10 1.11c2.33 0 4.45.87 6.05 2.34-.68-.11-1.65.39-1.65 1.58 0 .74.45 1.36.9 2.1.35.61.55 1.36.55 2.46 0 1.49-1.4 5-1.4 5l-3.03-8.37c.54-.02.82-.17.82-.17.5-.05.44-1.25-.06-1.22 0 0-1.44.12-2.38.12-.87 0-2.33-.12-2.33-.12-.5-.03-.56 1.2-.06 1.22l.92.08 1.26 3.41zM17.41 10c.24-.64.74-1.87.43-4.25.7 1.29 1.05 2.71 1.05 4.25 0 3.29-1.73 6.24-4.4 7.78.97-2.59 1.94-5.2 2.92-7.78zM6.1 18.09C3.12 16.65 1.11 13.53 1.11 10c0-1.3.23-2.48.72-3.59C3.25 10.3 4.67 14.2 6.1 18.09zm4.03-6.63l2.58 6.98c-.86.29-1.76.45-2.71.45-.79 0-1.57-.11-2.29-.33.81-2.38 1.62-4.74 2.42-7.1z" }) });

  // packages/edit-post/build-module/components/layout/index.js
  var import_notices3 = __toESM(require_notices());
  var import_preferences10 = __toESM(require_preferences());
  var import_commands2 = __toESM(require_commands());
  var import_block_library = __toESM(require_block_library());
  var import_url5 = __toESM(require_url());
  var import_html_entities = __toESM(require_html_entities());
  var import_core_data6 = __toESM(require_core_data());
  var import_components9 = __toESM(require_components());
  var import_compose3 = __toESM(require_compose());

  // packages/edit-post/build-module/components/back-button/index.js
  var import_editor2 = __toESM(require_editor());
  var import_components2 = __toESM(require_components());

  // packages/edit-post/build-module/components/back-button/fullscreen-mode-close.js
  var import_data2 = __toESM(require_data());
  var import_components = __toESM(require_components());
  var import_i18n = __toESM(require_i18n());
  var import_url = __toESM(require_url());
  var import_editor = __toESM(require_editor());
  var import_core_data = __toESM(require_core_data());
  var import_compose = __toESM(require_compose());
  var import_jsx_runtime7 = __toESM(require_jsx_runtime());
  var siteIconVariants = {
    edit: {
      clipPath: "inset(0% round 0px)"
    },
    hover: {
      clipPath: "inset( 22% round 2px )"
    },
    tap: {
      clipPath: "inset(0% round 0px)"
    }
  };
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
  function FullscreenModeClose({ showTooltip, icon, href, initialPost }) {
    const { isRequestingSiteIcon, postType, siteIconUrl } = (0, import_data2.useSelect)(
      (select3) => {
        const { getCurrentPostType } = select3(import_editor.store);
        const { getEntityRecord, getPostType, isResolving } = select3(import_core_data.store);
        const siteData = getEntityRecord("root", "__unstableBase", void 0) || {};
        const _postType = initialPost?.type || getCurrentPostType();
        return {
          isRequestingSiteIcon: isResolving("getEntityRecord", [
            "root",
            "__unstableBase",
            void 0
          ]),
          postType: getPostType(_postType),
          siteIconUrl: siteData.site_icon_url
        };
      },
      [initialPost?.type]
    );
    const disableMotion = (0, import_compose.useReducedMotion)();
    const transition = {
      duration: disableMotion ? 0 : 0.2
    };
    if (!postType) {
      return null;
    }
    let siteIconContent;
    if (isRequestingSiteIcon && !siteIconUrl) {
      siteIconContent = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)("div", { className: "edit-post-fullscreen-mode-close-site-icon__image" });
    } else if (siteIconUrl) {
      siteIconContent = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
        "img",
        {
          className: "edit-post-fullscreen-mode-close-site-icon__image",
          alt: (0, import_i18n.__)("Site Icon"),
          src: siteIconUrl
        }
      );
    } else {
      siteIconContent = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
        import_components.Icon,
        {
          className: "edit-post-fullscreen-mode-close-site-icon__icon",
          icon: wordpress_default,
          size: 48
        }
      );
    }
    const buttonIcon = icon ? /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_components.Icon, { size: "36px", icon }) : /* @__PURE__ */ (0, import_jsx_runtime7.jsx)("div", { className: "edit-post-fullscreen-mode-close-site-icon", children: siteIconContent });
    const classes = clsx_default("edit-post-fullscreen-mode-close", {
      "has-icon": siteIconUrl
    });
    const buttonHref = href ?? (0, import_url.addQueryArgs)("edit.php", {
      post_type: postType.slug
    });
    const buttonLabel = postType?.labels?.view_items ?? (0, import_i18n.__)("Back");
    return /* @__PURE__ */ (0, import_jsx_runtime7.jsxs)(
      import_components.__unstableMotion.div,
      {
        className: "edit-post-fullscreen-mode-close__view-mode-toggle",
        animate: "edit",
        initial: "edit",
        whileHover: "hover",
        whileTap: "tap",
        transition,
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
            import_components.Button,
            {
              __next40pxDefaultSize: true,
              className: classes,
              href: buttonHref,
              label: buttonLabel,
              showTooltip,
              tooltipPosition: "middle right",
              children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_components.__unstableMotion.div, { variants: !disableMotion && siteIconVariants, children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)("div", { className: "edit-post-fullscreen-mode-close__view-mode-toggle-icon", children: buttonIcon }) })
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(
            import_components.__unstableMotion.div,
            {
              className: clsx_default(
                "edit-post-fullscreen-mode-close__back-icon",
                {
                  "has-site-icon": siteIconUrl
                }
              ),
              variants: !disableMotion && toggleHomeIconVariants,
              children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_components.Icon, { icon: arrow_up_left_default })
            }
          )
        ]
      }
    );
  }
  var fullscreen_mode_close_default = FullscreenModeClose;

  // packages/edit-post/build-module/lock-unlock.js
  var import_private_apis = __toESM(require_private_apis());
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/edit-post"
  );

  // packages/edit-post/build-module/components/back-button/index.js
  var import_jsx_runtime8 = __toESM(require_jsx_runtime());
  var { BackButton: BackButtonFill } = unlock(import_editor2.privateApis);
  var slideX = {
    hidden: { x: "-100%" },
    distractionFreeInactive: { x: 0 },
    hover: { x: 0, transition: { type: "tween", delay: 0.2 } }
  };
  function BackButton({ initialPost }) {
    return /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(BackButtonFill, { children: ({ length }) => length <= 1 && /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(
      import_components2.__unstableMotion.div,
      {
        variants: slideX,
        transition: { type: "tween", delay: 0.8 },
        children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(
          fullscreen_mode_close_default,
          {
            showTooltip: true,
            initialPost
          }
        )
      }
    ) });
  }
  var back_button_default = BackButton;

  // packages/edit-post/build-module/components/editor-initialization/listener-hooks.js
  var import_data3 = __toESM(require_data());
  var import_element2 = __toESM(require_element());
  var import_editor3 = __toESM(require_editor());
  var import_core_data2 = __toESM(require_core_data());

  // packages/edit-post/build-module/store/constants.js
  var STORE_NAME = "core/edit-post";
  var VIEW_AS_LINK_SELECTOR = "#wp-admin-bar-view a";
  var VIEW_AS_PREVIEW_LINK_SELECTOR = "#wp-admin-bar-preview a";

  // packages/edit-post/build-module/components/editor-initialization/listener-hooks.js
  var useUpdatePostLinkListener = () => {
    const { isViewable, newPermalink } = (0, import_data3.useSelect)((select3) => {
      const { getPostType } = select3(import_core_data2.store);
      const { getCurrentPost, getEditedPostAttribute } = select3(import_editor3.store);
      const postType = getPostType(getEditedPostAttribute("type"));
      return {
        isViewable: postType?.viewable,
        newPermalink: getCurrentPost().link
      };
    }, []);
    const nodeToUpdateRef = (0, import_element2.useRef)();
    (0, import_element2.useEffect)(() => {
      nodeToUpdateRef.current = document.querySelector(VIEW_AS_PREVIEW_LINK_SELECTOR) || document.querySelector(VIEW_AS_LINK_SELECTOR);
    }, []);
    (0, import_element2.useEffect)(() => {
      if (!newPermalink || !nodeToUpdateRef.current) {
        return;
      }
      if (!isViewable) {
        nodeToUpdateRef.current.style.display = "none";
        return;
      }
      nodeToUpdateRef.current.style.display = "";
      nodeToUpdateRef.current.setAttribute("href", newPermalink);
    }, [newPermalink, isViewable]);
  };

  // packages/edit-post/build-module/components/editor-initialization/index.js
  function EditorInitialization() {
    useUpdatePostLinkListener();
    return null;
  }

  // packages/edit-post/build-module/components/keyboard-shortcuts/index.js
  var import_element3 = __toESM(require_element());
  var import_data7 = __toESM(require_data());
  var import_keyboard_shortcuts = __toESM(require_keyboard_shortcuts());
  var import_i18n3 = __toESM(require_i18n());

  // packages/edit-post/build-module/store/index.js
  var import_data6 = __toESM(require_data());

  // packages/edit-post/build-module/store/reducer.js
  var import_data4 = __toESM(require_data());
  function isSavingMetaBoxes(state = false, action) {
    switch (action.type) {
      case "REQUEST_META_BOX_UPDATES":
        return true;
      case "META_BOX_UPDATES_SUCCESS":
      case "META_BOX_UPDATES_FAILURE":
        return false;
      default:
        return state;
    }
  }
  function mergeMetaboxes(metaboxes = [], newMetaboxes) {
    const mergedMetaboxes = [...metaboxes];
    for (const metabox of newMetaboxes) {
      const existing = mergedMetaboxes.findIndex(
        (box) => box.id === metabox.id
      );
      if (existing !== -1) {
        mergedMetaboxes[existing] = metabox;
      } else {
        mergedMetaboxes.push(metabox);
      }
    }
    return mergedMetaboxes;
  }
  function metaBoxLocations(state = {}, action) {
    switch (action.type) {
      case "SET_META_BOXES_PER_LOCATIONS": {
        const newState = { ...state };
        for (const [location, metaboxes] of Object.entries(
          action.metaBoxesPerLocation
        )) {
          newState[location] = mergeMetaboxes(
            newState[location],
            metaboxes
          );
        }
        return newState;
      }
    }
    return state;
  }
  function metaBoxesInitialized(state = false, action) {
    switch (action.type) {
      case "META_BOXES_INITIALIZED":
        return true;
    }
    return state;
  }
  var metaBoxes = (0, import_data4.combineReducers)({
    isSaving: isSavingMetaBoxes,
    locations: metaBoxLocations,
    initialized: metaBoxesInitialized
  });
  var reducer_default = (0, import_data4.combineReducers)({
    metaBoxes
  });

  // packages/edit-post/build-module/store/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    __experimentalSetPreviewDeviceType: () => __experimentalSetPreviewDeviceType,
    __unstableCreateTemplate: () => __unstableCreateTemplate,
    closeGeneralSidebar: () => closeGeneralSidebar,
    closeModal: () => closeModal,
    closePublishSidebar: () => closePublishSidebar,
    hideBlockTypes: () => hideBlockTypes,
    initializeMetaBoxes: () => initializeMetaBoxes,
    metaBoxUpdatesFailure: () => metaBoxUpdatesFailure,
    metaBoxUpdatesSuccess: () => metaBoxUpdatesSuccess,
    openGeneralSidebar: () => openGeneralSidebar,
    openModal: () => openModal,
    openPublishSidebar: () => openPublishSidebar,
    removeEditorPanel: () => removeEditorPanel,
    requestMetaBoxUpdates: () => requestMetaBoxUpdates,
    setAvailableMetaBoxesPerLocation: () => setAvailableMetaBoxesPerLocation,
    setIsEditingTemplate: () => setIsEditingTemplate,
    setIsInserterOpened: () => setIsInserterOpened,
    setIsListViewOpened: () => setIsListViewOpened,
    showBlockTypes: () => showBlockTypes,
    switchEditorMode: () => switchEditorMode,
    toggleDistractionFree: () => toggleDistractionFree,
    toggleEditorPanelEnabled: () => toggleEditorPanelEnabled,
    toggleEditorPanelOpened: () => toggleEditorPanelOpened,
    toggleFeature: () => toggleFeature,
    toggleFullscreenMode: () => toggleFullscreenMode,
    togglePinnedPluginItem: () => togglePinnedPluginItem,
    togglePublishSidebar: () => togglePublishSidebar,
    updatePreferredStyleVariations: () => updatePreferredStyleVariations
  });
  var import_api_fetch = __toESM(require_api_fetch());
  var import_preferences = __toESM(require_preferences());
  var import_editor4 = __toESM(require_editor());
  var import_deprecated = __toESM(require_deprecated());
  var import_hooks = __toESM(require_hooks());
  var import_core_data3 = __toESM(require_core_data());
  var import_notices = __toESM(require_notices());
  var import_i18n2 = __toESM(require_i18n());

  // packages/edit-post/build-module/utils/meta-boxes.js
  var getMetaBoxContainer = (location) => {
    const area = document.querySelector(
      `.edit-post-meta-boxes-area.is-${location} .metabox-location-${location}`
    );
    if (area) {
      return area;
    }
    return document.querySelector("#metaboxes .metabox-location-" + location);
  };

  // packages/edit-post/build-module/store/actions.js
  var { interfaceStore } = unlock(import_editor4.privateApis);
  var openGeneralSidebar = (name) => ({ registry }) => {
    registry.dispatch(interfaceStore).enableComplementaryArea("core", name);
  };
  var closeGeneralSidebar = () => ({ registry }) => registry.dispatch(interfaceStore).disableComplementaryArea("core");
  var openModal = (name) => ({ registry }) => {
    (0, import_deprecated.default)("select( 'core/edit-post' ).openModal( name )", {
      since: "6.3",
      alternative: "select( 'core/interface').openModal( name )"
    });
    return registry.dispatch(interfaceStore).openModal(name);
  };
  var closeModal = () => ({ registry }) => {
    (0, import_deprecated.default)("select( 'core/edit-post' ).closeModal()", {
      since: "6.3",
      alternative: "select( 'core/interface').closeModal()"
    });
    return registry.dispatch(interfaceStore).closeModal();
  };
  var openPublishSidebar = () => ({ registry }) => {
    (0, import_deprecated.default)("dispatch( 'core/edit-post' ).openPublishSidebar", {
      since: "6.6",
      alternative: "dispatch( 'core/editor').openPublishSidebar"
    });
    registry.dispatch(import_editor4.store).openPublishSidebar();
  };
  var closePublishSidebar = () => ({ registry }) => {
    (0, import_deprecated.default)("dispatch( 'core/edit-post' ).closePublishSidebar", {
      since: "6.6",
      alternative: "dispatch( 'core/editor').closePublishSidebar"
    });
    registry.dispatch(import_editor4.store).closePublishSidebar();
  };
  var togglePublishSidebar = () => ({ registry }) => {
    (0, import_deprecated.default)("dispatch( 'core/edit-post' ).togglePublishSidebar", {
      since: "6.6",
      alternative: "dispatch( 'core/editor').togglePublishSidebar"
    });
    registry.dispatch(import_editor4.store).togglePublishSidebar();
  };
  var toggleEditorPanelEnabled = (panelName) => ({ registry }) => {
    (0, import_deprecated.default)("dispatch( 'core/edit-post' ).toggleEditorPanelEnabled", {
      since: "6.5",
      alternative: "dispatch( 'core/editor').toggleEditorPanelEnabled"
    });
    registry.dispatch(import_editor4.store).toggleEditorPanelEnabled(panelName);
  };
  var toggleEditorPanelOpened = (panelName) => ({ registry }) => {
    (0, import_deprecated.default)("dispatch( 'core/edit-post' ).toggleEditorPanelOpened", {
      since: "6.5",
      alternative: "dispatch( 'core/editor').toggleEditorPanelOpened"
    });
    registry.dispatch(import_editor4.store).toggleEditorPanelOpened(panelName);
  };
  var removeEditorPanel = (panelName) => ({ registry }) => {
    (0, import_deprecated.default)("dispatch( 'core/edit-post' ).removeEditorPanel", {
      since: "6.5",
      alternative: "dispatch( 'core/editor').removeEditorPanel"
    });
    registry.dispatch(import_editor4.store).removeEditorPanel(panelName);
  };
  var toggleFeature = (feature) => ({ registry }) => registry.dispatch(import_preferences.store).toggle("core/edit-post", feature);
  var switchEditorMode = (mode) => ({ registry }) => {
    (0, import_deprecated.default)("dispatch( 'core/edit-post' ).switchEditorMode", {
      since: "6.6",
      alternative: "dispatch( 'core/editor').switchEditorMode"
    });
    registry.dispatch(import_editor4.store).switchEditorMode(mode);
  };
  var togglePinnedPluginItem = (pluginName) => ({ registry }) => {
    const isPinned = registry.select(interfaceStore).isItemPinned("core", pluginName);
    registry.dispatch(interfaceStore)[isPinned ? "unpinItem" : "pinItem"]("core", pluginName);
  };
  function updatePreferredStyleVariations() {
    (0, import_deprecated.default)("dispatch( 'core/edit-post' ).updatePreferredStyleVariations", {
      since: "6.6",
      hint: "Preferred Style Variations are not supported anymore."
    });
    return { type: "NOTHING" };
  }
  var showBlockTypes = (blockNames) => ({ registry }) => {
    unlock(registry.dispatch(import_editor4.store)).showBlockTypes(blockNames);
  };
  var hideBlockTypes = (blockNames) => ({ registry }) => {
    unlock(registry.dispatch(import_editor4.store)).hideBlockTypes(blockNames);
  };
  function setAvailableMetaBoxesPerLocation(metaBoxesPerLocation) {
    return {
      type: "SET_META_BOXES_PER_LOCATIONS",
      metaBoxesPerLocation
    };
  }
  var requestMetaBoxUpdates = () => async ({ registry, select: select3, dispatch: dispatch2 }) => {
    dispatch2({
      type: "REQUEST_META_BOX_UPDATES"
    });
    if (window.tinyMCE) {
      window.tinyMCE.triggerSave();
    }
    const baseFormData = new window.FormData(
      document.querySelector(".metabox-base-form")
    );
    const postId = baseFormData.get("post_ID");
    const postType = baseFormData.get("post_type");
    const post = registry.select(import_core_data3.store).getEditedEntityRecord("postType", postType, postId);
    const additionalData = [
      post.comment_status ? ["comment_status", post.comment_status] : false,
      post.ping_status ? ["ping_status", post.ping_status] : false,
      post.sticky ? ["sticky", post.sticky] : false,
      post.author ? ["post_author", post.author] : false
    ].filter(Boolean);
    const activeMetaBoxLocations = select3.getActiveMetaBoxLocations();
    const formDataToMerge = [
      baseFormData,
      ...activeMetaBoxLocations.map(
        (location) => new window.FormData(getMetaBoxContainer(location))
      )
    ];
    const formData = formDataToMerge.reduce((memo, currentFormData) => {
      for (const [key, value] of currentFormData) {
        memo.append(key, value);
      }
      return memo;
    }, new window.FormData());
    additionalData.forEach(
      ([key, value]) => formData.append(key, value)
    );
    try {
      await (0, import_api_fetch.default)({
        url: window._wpMetaBoxUrl,
        method: "POST",
        body: formData,
        parse: false
      });
      dispatch2.metaBoxUpdatesSuccess();
    } catch {
      dispatch2.metaBoxUpdatesFailure();
    }
  };
  function metaBoxUpdatesSuccess() {
    return {
      type: "META_BOX_UPDATES_SUCCESS"
    };
  }
  function metaBoxUpdatesFailure() {
    return {
      type: "META_BOX_UPDATES_FAILURE"
    };
  }
  var __experimentalSetPreviewDeviceType = (deviceType) => ({ registry }) => {
    (0, import_deprecated.default)(
      "dispatch( 'core/edit-post' ).__experimentalSetPreviewDeviceType",
      {
        since: "6.5",
        version: "6.7",
        hint: "registry.dispatch( editorStore ).setDeviceType"
      }
    );
    registry.dispatch(import_editor4.store).setDeviceType(deviceType);
  };
  var setIsInserterOpened = (value) => ({ registry }) => {
    (0, import_deprecated.default)("dispatch( 'core/edit-post' ).setIsInserterOpened", {
      since: "6.5",
      alternative: "dispatch( 'core/editor').setIsInserterOpened"
    });
    registry.dispatch(import_editor4.store).setIsInserterOpened(value);
  };
  var setIsListViewOpened = (isOpen) => ({ registry }) => {
    (0, import_deprecated.default)("dispatch( 'core/edit-post' ).setIsListViewOpened", {
      since: "6.5",
      alternative: "dispatch( 'core/editor').setIsListViewOpened"
    });
    registry.dispatch(import_editor4.store).setIsListViewOpened(isOpen);
  };
  function setIsEditingTemplate() {
    (0, import_deprecated.default)("dispatch( 'core/edit-post' ).setIsEditingTemplate", {
      since: "6.5",
      alternative: "dispatch( 'core/editor').setRenderingMode"
    });
    return { type: "NOTHING" };
  }
  function __unstableCreateTemplate() {
    (0, import_deprecated.default)("dispatch( 'core/edit-post' ).__unstableCreateTemplate", {
      since: "6.5"
    });
    return { type: "NOTHING" };
  }
  var metaBoxesInitialized2 = false;
  var initializeMetaBoxes = () => ({ registry, select: select3, dispatch: dispatch2 }) => {
    const isEditorReady = registry.select(import_editor4.store).__unstableIsEditorReady();
    if (!isEditorReady) {
      return;
    }
    if (metaBoxesInitialized2) {
      return;
    }
    const postType = registry.select(import_editor4.store).getCurrentPostType();
    if (window.postboxes.page !== postType) {
      window.postboxes.add_postbox_toggles(postType);
    }
    metaBoxesInitialized2 = true;
    (0, import_hooks.addAction)(
      "editor.savePost",
      "core/edit-post/save-metaboxes",
      async (post, options) => {
        if (!options.isAutosave && select3.hasMetaBoxes()) {
          await dispatch2.requestMetaBoxUpdates();
        }
      }
    );
    dispatch2({
      type: "META_BOXES_INITIALIZED"
    });
  };
  var toggleDistractionFree = () => ({ registry }) => {
    (0, import_deprecated.default)("dispatch( 'core/edit-post' ).toggleDistractionFree", {
      since: "6.6",
      alternative: "dispatch( 'core/editor').toggleDistractionFree"
    });
    registry.dispatch(import_editor4.store).toggleDistractionFree();
  };
  var toggleFullscreenMode = () => ({ registry }) => {
    const isFullscreen = registry.select(import_preferences.store).get("core/edit-post", "fullscreenMode");
    registry.dispatch(import_preferences.store).toggle("core/edit-post", "fullscreenMode");
    registry.dispatch(import_notices.store).createInfoNotice(
      isFullscreen ? (0, import_i18n2.__)("Fullscreen mode deactivated.") : (0, import_i18n2.__)("Fullscreen mode activated."),
      {
        id: "core/edit-post/toggle-fullscreen-mode/notice",
        type: "snackbar",
        actions: [
          {
            label: (0, import_i18n2.__)("Undo"),
            onClick: () => {
              registry.dispatch(import_preferences.store).toggle(
                "core/edit-post",
                "fullscreenMode"
              );
            }
          }
        ]
      }
    );
  };

  // packages/edit-post/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    __experimentalGetInsertionPoint: () => __experimentalGetInsertionPoint,
    __experimentalGetPreviewDeviceType: () => __experimentalGetPreviewDeviceType,
    areMetaBoxesInitialized: () => areMetaBoxesInitialized,
    getActiveGeneralSidebarName: () => getActiveGeneralSidebarName,
    getActiveMetaBoxLocations: () => getActiveMetaBoxLocations,
    getAllMetaBoxes: () => getAllMetaBoxes,
    getEditedPostTemplate: () => getEditedPostTemplate,
    getEditorMode: () => getEditorMode,
    getHiddenBlockTypes: () => getHiddenBlockTypes,
    getMetaBoxesPerLocation: () => getMetaBoxesPerLocation,
    getPreference: () => getPreference,
    getPreferences: () => getPreferences,
    hasMetaBoxes: () => hasMetaBoxes,
    isEditingTemplate: () => isEditingTemplate,
    isEditorPanelEnabled: () => isEditorPanelEnabled,
    isEditorPanelOpened: () => isEditorPanelOpened,
    isEditorPanelRemoved: () => isEditorPanelRemoved,
    isEditorSidebarOpened: () => isEditorSidebarOpened,
    isFeatureActive: () => isFeatureActive,
    isInserterOpened: () => isInserterOpened,
    isListViewOpened: () => isListViewOpened,
    isMetaBoxLocationActive: () => isMetaBoxLocationActive,
    isMetaBoxLocationVisible: () => isMetaBoxLocationVisible,
    isModalActive: () => isModalActive,
    isPluginItemPinned: () => isPluginItemPinned,
    isPluginSidebarOpened: () => isPluginSidebarOpened,
    isPublishSidebarOpened: () => isPublishSidebarOpened,
    isSavingMetaBoxes: () => isSavingMetaBoxes2
  });
  var import_data5 = __toESM(require_data());
  var import_preferences2 = __toESM(require_preferences());
  var import_core_data4 = __toESM(require_core_data());
  var import_editor5 = __toESM(require_editor());
  var import_deprecated2 = __toESM(require_deprecated());
  var { interfaceStore: interfaceStore2 } = unlock(import_editor5.privateApis);
  var EMPTY_ARRAY = [];
  var EMPTY_OBJECT = {};
  var getEditorMode = (0, import_data5.createRegistrySelector)(
    (select3) => () => select3(import_preferences2.store).get("core", "editorMode") ?? "visual"
  );
  var isEditorSidebarOpened = (0, import_data5.createRegistrySelector)(
    (select3) => () => {
      const activeGeneralSidebar = select3(interfaceStore2).getActiveComplementaryArea("core");
      return ["edit-post/document", "edit-post/block"].includes(
        activeGeneralSidebar
      );
    }
  );
  var isPluginSidebarOpened = (0, import_data5.createRegistrySelector)(
    (select3) => () => {
      const activeGeneralSidebar = select3(interfaceStore2).getActiveComplementaryArea("core");
      return !!activeGeneralSidebar && !["edit-post/document", "edit-post/block"].includes(
        activeGeneralSidebar
      );
    }
  );
  var getActiveGeneralSidebarName = (0, import_data5.createRegistrySelector)(
    (select3) => () => {
      return select3(interfaceStore2).getActiveComplementaryArea("core");
    }
  );
  function convertPanelsToOldFormat(inactivePanels, openPanels) {
    const panelsWithEnabledState = inactivePanels?.reduce(
      (accumulatedPanels, panelName) => ({
        ...accumulatedPanels,
        [panelName]: {
          enabled: false
        }
      }),
      {}
    );
    const panels = openPanels?.reduce((accumulatedPanels, panelName) => {
      const currentPanelState = accumulatedPanels?.[panelName];
      return {
        ...accumulatedPanels,
        [panelName]: {
          ...currentPanelState,
          opened: true
        }
      };
    }, panelsWithEnabledState ?? {});
    return panels ?? panelsWithEnabledState ?? EMPTY_OBJECT;
  }
  var getPreferences = (0, import_data5.createRegistrySelector)((select3) => () => {
    (0, import_deprecated2.default)(`select( 'core/edit-post' ).getPreferences`, {
      since: "6.0",
      alternative: `select( 'core/preferences' ).get`
    });
    const corePreferences = ["editorMode", "hiddenBlockTypes"].reduce(
      (accumulatedPrefs, preferenceKey) => {
        const value = select3(import_preferences2.store).get(
          "core",
          preferenceKey
        );
        return {
          ...accumulatedPrefs,
          [preferenceKey]: value
        };
      },
      {}
    );
    const inactivePanels = select3(import_preferences2.store).get(
      "core",
      "inactivePanels"
    );
    const openPanels = select3(import_preferences2.store).get("core", "openPanels");
    const panels = convertPanelsToOldFormat(inactivePanels, openPanels);
    return {
      ...corePreferences,
      panels
    };
  });
  function getPreference(state, preferenceKey, defaultValue) {
    (0, import_deprecated2.default)(`select( 'core/edit-post' ).getPreference`, {
      since: "6.0",
      alternative: `select( 'core/preferences' ).get`
    });
    const preferences = getPreferences(state);
    const value = preferences[preferenceKey];
    return value === void 0 ? defaultValue : value;
  }
  var getHiddenBlockTypes = (0, import_data5.createRegistrySelector)((select3) => () => {
    return select3(import_preferences2.store).get("core", "hiddenBlockTypes") ?? EMPTY_ARRAY;
  });
  var isPublishSidebarOpened = (0, import_data5.createRegistrySelector)(
    (select3) => () => {
      (0, import_deprecated2.default)(`select( 'core/edit-post' ).isPublishSidebarOpened`, {
        since: "6.6",
        alternative: `select( 'core/editor' ).isPublishSidebarOpened`
      });
      return select3(import_editor5.store).isPublishSidebarOpened();
    }
  );
  var isEditorPanelRemoved = (0, import_data5.createRegistrySelector)(
    (select3) => (state, panelName) => {
      (0, import_deprecated2.default)(`select( 'core/edit-post' ).isEditorPanelRemoved`, {
        since: "6.5",
        alternative: `select( 'core/editor' ).isEditorPanelRemoved`
      });
      return select3(import_editor5.store).isEditorPanelRemoved(panelName);
    }
  );
  var isEditorPanelEnabled = (0, import_data5.createRegistrySelector)(
    (select3) => (state, panelName) => {
      (0, import_deprecated2.default)(`select( 'core/edit-post' ).isEditorPanelEnabled`, {
        since: "6.5",
        alternative: `select( 'core/editor' ).isEditorPanelEnabled`
      });
      return select3(import_editor5.store).isEditorPanelEnabled(panelName);
    }
  );
  var isEditorPanelOpened = (0, import_data5.createRegistrySelector)(
    (select3) => (state, panelName) => {
      (0, import_deprecated2.default)(`select( 'core/edit-post' ).isEditorPanelOpened`, {
        since: "6.5",
        alternative: `select( 'core/editor' ).isEditorPanelOpened`
      });
      return select3(import_editor5.store).isEditorPanelOpened(panelName);
    }
  );
  var isModalActive = (0, import_data5.createRegistrySelector)(
    (select3) => (state, modalName) => {
      (0, import_deprecated2.default)(`select( 'core/edit-post' ).isModalActive`, {
        since: "6.3",
        alternative: `select( 'core/interface' ).isModalActive`
      });
      return !!select3(interfaceStore2).isModalActive(modalName);
    }
  );
  var isFeatureActive = (0, import_data5.createRegistrySelector)(
    (select3) => (state, feature) => {
      return !!select3(import_preferences2.store).get("core/edit-post", feature);
    }
  );
  var isPluginItemPinned = (0, import_data5.createRegistrySelector)(
    (select3) => (state, pluginName) => {
      return select3(interfaceStore2).isItemPinned("core", pluginName);
    }
  );
  var getActiveMetaBoxLocations = (0, import_data5.createSelector)(
    (state) => {
      return Object.keys(state.metaBoxes.locations).filter(
        (location) => isMetaBoxLocationActive(state, location)
      );
    },
    (state) => [state.metaBoxes.locations]
  );
  var isMetaBoxLocationVisible = (0, import_data5.createRegistrySelector)(
    (select3) => (state, location) => {
      return isMetaBoxLocationActive(state, location) && getMetaBoxesPerLocation(state, location)?.some(({ id }) => {
        return select3(import_editor5.store).isEditorPanelEnabled(
          `meta-box-${id}`
        );
      });
    }
  );
  function isMetaBoxLocationActive(state, location) {
    const metaBoxes2 = getMetaBoxesPerLocation(state, location);
    return !!metaBoxes2 && metaBoxes2.length !== 0;
  }
  function getMetaBoxesPerLocation(state, location) {
    return state.metaBoxes.locations[location];
  }
  var getAllMetaBoxes = (0, import_data5.createSelector)(
    (state) => {
      return Object.values(state.metaBoxes.locations).flat();
    },
    (state) => [state.metaBoxes.locations]
  );
  function hasMetaBoxes(state) {
    return getActiveMetaBoxLocations(state).length > 0;
  }
  function isSavingMetaBoxes2(state) {
    return state.metaBoxes.isSaving;
  }
  var __experimentalGetPreviewDeviceType = (0, import_data5.createRegistrySelector)(
    (select3) => () => {
      (0, import_deprecated2.default)(
        `select( 'core/edit-site' ).__experimentalGetPreviewDeviceType`,
        {
          since: "6.5",
          version: "6.7",
          alternative: `select( 'core/editor' ).getDeviceType`
        }
      );
      return select3(import_editor5.store).getDeviceType();
    }
  );
  var isInserterOpened = (0, import_data5.createRegistrySelector)((select3) => () => {
    (0, import_deprecated2.default)(`select( 'core/edit-post' ).isInserterOpened`, {
      since: "6.5",
      alternative: `select( 'core/editor' ).isInserterOpened`
    });
    return select3(import_editor5.store).isInserterOpened();
  });
  var __experimentalGetInsertionPoint = (0, import_data5.createRegistrySelector)(
    (select3) => () => {
      (0, import_deprecated2.default)(
        `select( 'core/edit-post' ).__experimentalGetInsertionPoint`,
        {
          since: "6.5",
          version: "6.7"
        }
      );
      return unlock(select3(import_editor5.store)).getInserter();
    }
  );
  var isListViewOpened = (0, import_data5.createRegistrySelector)((select3) => () => {
    (0, import_deprecated2.default)(`select( 'core/edit-post' ).isListViewOpened`, {
      since: "6.5",
      alternative: `select( 'core/editor' ).isListViewOpened`
    });
    return select3(import_editor5.store).isListViewOpened();
  });
  var isEditingTemplate = (0, import_data5.createRegistrySelector)((select3) => () => {
    (0, import_deprecated2.default)(`select( 'core/edit-post' ).isEditingTemplate`, {
      since: "6.5",
      alternative: `select( 'core/editor' ).getRenderingMode`
    });
    return select3(import_editor5.store).getCurrentPostType() === "wp_template";
  });
  function areMetaBoxesInitialized(state) {
    return state.metaBoxes.initialized;
  }
  var getEditedPostTemplate = (0, import_data5.createRegistrySelector)(
    (select3) => () => {
      const { id: postId, type: postType } = select3(import_editor5.store).getCurrentPost();
      const templateId = unlock(select3(import_core_data4.store)).getTemplateId(
        postType,
        postId
      );
      if (!templateId) {
        return void 0;
      }
      return select3(import_core_data4.store).getEditedEntityRecord(
        "postType",
        "wp_template",
        templateId
      );
    }
  );

  // packages/edit-post/build-module/store/index.js
  var store = (0, import_data6.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    actions: actions_exports,
    selectors: selectors_exports
  });
  (0, import_data6.register)(store);

  // packages/edit-post/build-module/components/keyboard-shortcuts/index.js
  function KeyboardShortcuts() {
    const { toggleFullscreenMode: toggleFullscreenMode2 } = (0, import_data7.useDispatch)(store);
    const { registerShortcut } = (0, import_data7.useDispatch)(import_keyboard_shortcuts.store);
    (0, import_element3.useEffect)(() => {
      registerShortcut({
        name: "core/edit-post/toggle-fullscreen",
        category: "global",
        description: (0, import_i18n3.__)("Enable or disable fullscreen mode."),
        keyCombination: {
          modifier: "secondary",
          character: "f"
        }
      });
    }, []);
    (0, import_keyboard_shortcuts.useShortcut)("core/edit-post/toggle-fullscreen", () => {
      toggleFullscreenMode2();
    });
    return null;
  }
  var keyboard_shortcuts_default = KeyboardShortcuts;

  // packages/edit-post/build-module/components/init-pattern-modal/index.js
  var import_data8 = __toESM(require_data());
  var import_i18n4 = __toESM(require_i18n());
  var import_components3 = __toESM(require_components());
  var import_element4 = __toESM(require_element());
  var import_editor6 = __toESM(require_editor());
  var import_jsx_runtime9 = __toESM(require_jsx_runtime());
  function InitPatternModal() {
    const { editPost } = (0, import_data8.useDispatch)(import_editor6.store);
    const [syncType, setSyncType] = (0, import_element4.useState)(void 0);
    const [title, setTitle] = (0, import_element4.useState)("");
    const { postType, isNewPost } = (0, import_data8.useSelect)((select3) => {
      const { getEditedPostAttribute, isCleanNewPost } = select3(import_editor6.store);
      return {
        postType: getEditedPostAttribute("type"),
        isNewPost: isCleanNewPost()
      };
    }, []);
    const [isModalOpen, setIsModalOpen] = (0, import_element4.useState)(
      () => isNewPost && postType === "wp_block"
    );
    if (postType !== "wp_block" || !isNewPost) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_jsx_runtime9.Fragment, { children: isModalOpen && /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
      import_components3.Modal,
      {
        title: (0, import_i18n4.__)("Create pattern"),
        onRequestClose: () => {
          setIsModalOpen(false);
        },
        overlayClassName: "reusable-blocks-menu-items__convert-modal",
        children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
          "form",
          {
            onSubmit: (event) => {
              event.preventDefault();
              setIsModalOpen(false);
              editPost({
                title,
                meta: {
                  wp_pattern_sync_status: syncType
                }
              });
            },
            children: /* @__PURE__ */ (0, import_jsx_runtime9.jsxs)(import_components3.__experimentalVStack, { spacing: "5", children: [
              /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
                import_components3.TextControl,
                {
                  label: (0, import_i18n4.__)("Name"),
                  value: title,
                  onChange: setTitle,
                  placeholder: (0, import_i18n4.__)("My pattern"),
                  className: "patterns-create-modal__name-input",
                  __nextHasNoMarginBottom: true,
                  __next40pxDefaultSize: true
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
                import_components3.ToggleControl,
                {
                  __nextHasNoMarginBottom: true,
                  label: (0, import_i18n4._x)("Synced", "pattern (singular)"),
                  help: (0, import_i18n4.__)(
                    "Sync this pattern across multiple locations."
                  ),
                  checked: !syncType,
                  onChange: () => {
                    setSyncType(
                      !syncType ? "unsynced" : void 0
                    );
                  }
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_components3.__experimentalHStack, { justify: "right", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
                import_components3.Button,
                {
                  __next40pxDefaultSize: true,
                  variant: "primary",
                  type: "submit",
                  disabled: !title,
                  accessibleWhenDisabled: true,
                  children: (0, import_i18n4.__)("Create")
                }
              ) })
            ] })
          }
        )
      }
    ) });
  }

  // packages/edit-post/build-module/components/browser-url/index.js
  var import_element5 = __toESM(require_element());
  var import_data9 = __toESM(require_data());
  var import_url2 = __toESM(require_url());
  var import_editor7 = __toESM(require_editor());
  function getPostEditURL(postId) {
    return (0, import_url2.addQueryArgs)("post.php", { post: postId, action: "edit" });
  }
  function BrowserURL() {
    const [historyId, setHistoryId] = (0, import_element5.useState)(null);
    const { postId, postStatus } = (0, import_data9.useSelect)((select3) => {
      const { getCurrentPost } = select3(import_editor7.store);
      const post = getCurrentPost();
      let { id, status, type } = post;
      const isTemplate = ["wp_template", "wp_template_part"].includes(
        type
      );
      if (isTemplate) {
        id = post.wp_id;
      }
      return {
        postId: id,
        postStatus: status
      };
    }, []);
    (0, import_element5.useEffect)(() => {
      if (postId && postId !== historyId && postStatus !== "auto-draft") {
        window.history.replaceState(
          { id: postId },
          "Post " + postId,
          getPostEditURL(postId)
        );
        setHistoryId(postId);
      }
    }, [postId, postStatus, historyId]);
    return null;
  }

  // packages/edit-post/build-module/components/meta-boxes/index.js
  var import_data12 = __toESM(require_data());

  // packages/edit-post/build-module/components/meta-boxes/meta-boxes-area/index.js
  var import_element6 = __toESM(require_element());
  var import_components4 = __toESM(require_components());
  var import_data10 = __toESM(require_data());
  var import_jsx_runtime10 = __toESM(require_jsx_runtime());
  function MetaBoxesArea({ location }) {
    const container = (0, import_element6.useRef)(null);
    const formRef = (0, import_element6.useRef)(null);
    (0, import_element6.useEffect)(() => {
      formRef.current = document.querySelector(
        ".metabox-location-" + location
      );
      if (formRef.current) {
        container.current.appendChild(formRef.current);
      }
      return () => {
        if (formRef.current) {
          document.querySelector("#metaboxes").appendChild(formRef.current);
        }
      };
    }, [location]);
    const isSaving = (0, import_data10.useSelect)((select3) => {
      return select3(store).isSavingMetaBoxes();
    }, []);
    const classes = clsx_default("edit-post-meta-boxes-area", `is-${location}`, {
      "is-loading": isSaving
    });
    return /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)("div", { className: classes, children: [
      isSaving && /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_components4.Spinner, {}),
      /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(
        "div",
        {
          className: "edit-post-meta-boxes-area__container",
          ref: container
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime10.jsx)("div", { className: "edit-post-meta-boxes-area__clear" })
    ] });
  }
  var meta_boxes_area_default = MetaBoxesArea;

  // packages/edit-post/build-module/components/meta-boxes/meta-box-visibility.js
  var import_element7 = __toESM(require_element());
  var import_data11 = __toESM(require_data());
  var import_editor8 = __toESM(require_editor());
  function MetaBoxVisibility({ id }) {
    const isVisible = (0, import_data11.useSelect)(
      (select3) => {
        return select3(import_editor8.store).isEditorPanelEnabled(
          `meta-box-${id}`
        );
      },
      [id]
    );
    (0, import_element7.useEffect)(() => {
      const element = document.getElementById(id);
      if (!element) {
        return;
      }
      if (isVisible) {
        element.classList.remove("is-hidden");
      } else {
        element.classList.add("is-hidden");
      }
    }, [id, isVisible]);
    return null;
  }

  // packages/edit-post/build-module/components/meta-boxes/index.js
  var import_jsx_runtime11 = __toESM(require_jsx_runtime());
  function MetaBoxes({ location }) {
    const metaBoxes2 = (0, import_data12.useSelect)(
      (select3) => select3(store).getMetaBoxesPerLocation(location),
      [location]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(import_jsx_runtime11.Fragment, { children: [
      (metaBoxes2 ?? []).map(({ id }) => /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(MetaBoxVisibility, { id }, id)),
      /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(meta_boxes_area_default, { location })
    ] });
  }

  // packages/edit-post/build-module/components/more-menu/index.js
  var import_i18n10 = __toESM(require_i18n());
  var import_compose2 = __toESM(require_compose());
  var import_editor14 = __toESM(require_editor());
  var import_keycodes = __toESM(require_keycodes());
  var import_preferences8 = __toESM(require_preferences());

  // packages/edit-post/build-module/components/more-menu/manage-patterns-menu-item.js
  var import_components5 = __toESM(require_components());
  var import_core_data5 = __toESM(require_core_data());
  var import_data13 = __toESM(require_data());
  var import_i18n5 = __toESM(require_i18n());
  var import_url3 = __toESM(require_url());
  var import_jsx_runtime12 = __toESM(require_jsx_runtime());
  function ManagePatternsMenuItem() {
    const url = (0, import_data13.useSelect)((select3) => {
      const { canUser } = select3(import_core_data5.store);
      const defaultUrl = (0, import_url3.addQueryArgs)("edit.php", {
        post_type: "wp_block"
      });
      const patternsUrl = (0, import_url3.addQueryArgs)("site-editor.php", {
        p: "/pattern"
      });
      return canUser("create", {
        kind: "postType",
        name: "wp_template"
      }) ? patternsUrl : defaultUrl;
    }, []);
    return /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_components5.MenuItem, { role: "menuitem", href: url, children: (0, import_i18n5.__)("Manage patterns") });
  }
  var manage_patterns_menu_item_default = ManagePatternsMenuItem;

  // packages/edit-post/build-module/components/more-menu/welcome-guide-menu-item.js
  var import_data14 = __toESM(require_data());
  var import_preferences3 = __toESM(require_preferences());
  var import_i18n6 = __toESM(require_i18n());
  var import_editor9 = __toESM(require_editor());
  var import_jsx_runtime13 = __toESM(require_jsx_runtime());
  function WelcomeGuideMenuItem() {
    const isEditingTemplate2 = (0, import_data14.useSelect)(
      (select3) => select3(import_editor9.store).getCurrentPostType() === "wp_template",
      []
    );
    return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
      import_preferences3.PreferenceToggleMenuItem,
      {
        scope: "core/edit-post",
        name: isEditingTemplate2 ? "welcomeGuideTemplate" : "welcomeGuide",
        label: (0, import_i18n6.__)("Welcome Guide")
      }
    );
  }

  // packages/edit-post/build-module/components/preferences-modal/index.js
  var import_i18n9 = __toESM(require_i18n());
  var import_preferences7 = __toESM(require_preferences());
  var import_editor13 = __toESM(require_editor());

  // packages/edit-post/build-module/components/preferences-modal/meta-boxes-section.js
  var import_i18n8 = __toESM(require_i18n());
  var import_data17 = __toESM(require_data());
  var import_editor12 = __toESM(require_editor());
  var import_preferences6 = __toESM(require_preferences());

  // packages/edit-post/build-module/components/preferences-modal/enable-custom-fields.js
  var import_element8 = __toESM(require_element());
  var import_i18n7 = __toESM(require_i18n());
  var import_components6 = __toESM(require_components());
  var import_data15 = __toESM(require_data());
  var import_editor10 = __toESM(require_editor());
  var import_preferences4 = __toESM(require_preferences());
  var import_url4 = __toESM(require_url());
  var import_jsx_runtime14 = __toESM(require_jsx_runtime());
  var { PreferenceBaseOption } = unlock(import_preferences4.privateApis);
  function submitCustomFieldsForm() {
    const customFieldsForm = document.getElementById(
      "toggle-custom-fields-form"
    );
    customFieldsForm.querySelector('[name="_wp_http_referer"]').setAttribute("value", (0, import_url4.getPathAndQueryString)(window.location.href));
    customFieldsForm.submit();
  }
  function CustomFieldsConfirmation({ willEnable }) {
    const [isReloading, setIsReloading] = (0, import_element8.useState)(false);
    return /* @__PURE__ */ (0, import_jsx_runtime14.jsxs)(import_jsx_runtime14.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime14.jsx)("p", { className: "edit-post-preferences-modal__custom-fields-confirmation-message", children: (0, import_i18n7.__)(
        "A page reload is required for this change. Make sure your content is saved before reloading."
      ) }),
      /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
        import_components6.Button,
        {
          __next40pxDefaultSize: true,
          variant: "secondary",
          isBusy: isReloading,
          accessibleWhenDisabled: true,
          disabled: isReloading,
          onClick: () => {
            setIsReloading(true);
            submitCustomFieldsForm();
          },
          children: willEnable ? (0, import_i18n7.__)("Show & Reload Page") : (0, import_i18n7.__)("Hide & Reload Page")
        }
      )
    ] });
  }
  function EnableCustomFieldsOption({ label }) {
    const areCustomFieldsEnabled = (0, import_data15.useSelect)((select3) => {
      return !!select3(import_editor10.store).getEditorSettings().enableCustomFields;
    }, []);
    const [isChecked, setIsChecked] = (0, import_element8.useState)(areCustomFieldsEnabled);
    return /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
      PreferenceBaseOption,
      {
        label,
        isChecked,
        onChange: setIsChecked,
        children: isChecked !== areCustomFieldsEnabled && /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(CustomFieldsConfirmation, { willEnable: isChecked })
      }
    );
  }

  // packages/edit-post/build-module/components/preferences-modal/enable-panel.js
  var import_data16 = __toESM(require_data());
  var import_editor11 = __toESM(require_editor());
  var import_preferences5 = __toESM(require_preferences());
  var import_jsx_runtime15 = __toESM(require_jsx_runtime());
  var { PreferenceBaseOption: PreferenceBaseOption2 } = unlock(import_preferences5.privateApis);
  function EnablePanelOption(props) {
    const { toggleEditorPanelEnabled: toggleEditorPanelEnabled2 } = (0, import_data16.useDispatch)(import_editor11.store);
    const { isChecked, isRemoved } = (0, import_data16.useSelect)(
      (select3) => {
        const { isEditorPanelEnabled: isEditorPanelEnabled2, isEditorPanelRemoved: isEditorPanelRemoved2 } = select3(import_editor11.store);
        return {
          isChecked: isEditorPanelEnabled2(props.panelName),
          isRemoved: isEditorPanelRemoved2(props.panelName)
        };
      },
      [props.panelName]
    );
    if (isRemoved) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(
      PreferenceBaseOption2,
      {
        isChecked,
        onChange: () => toggleEditorPanelEnabled2(props.panelName),
        ...props
      }
    );
  }

  // packages/edit-post/build-module/components/preferences-modal/meta-boxes-section.js
  var import_jsx_runtime16 = __toESM(require_jsx_runtime());
  var { PreferencesModalSection } = unlock(import_preferences6.privateApis);
  function MetaBoxesSection({
    areCustomFieldsRegistered,
    metaBoxes: metaBoxes2,
    ...sectionProps
  }) {
    const thirdPartyMetaBoxes = metaBoxes2.filter(
      ({ id }) => id !== "postcustom"
    );
    if (!areCustomFieldsRegistered && thirdPartyMetaBoxes.length === 0) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime16.jsxs)(PreferencesModalSection, { ...sectionProps, children: [
      areCustomFieldsRegistered && /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(EnableCustomFieldsOption, { label: (0, import_i18n8.__)("Custom fields") }),
      thirdPartyMetaBoxes.map(({ id, title }) => /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
        EnablePanelOption,
        {
          label: title,
          panelName: `meta-box-${id}`
        },
        id
      ))
    ] });
  }
  var meta_boxes_section_default = (0, import_data17.withSelect)((select3) => {
    const { getEditorSettings } = select3(import_editor12.store);
    const { getAllMetaBoxes: getAllMetaBoxes2 } = select3(store);
    return {
      // This setting should not live in the block editor's store.
      areCustomFieldsRegistered: getEditorSettings().enableCustomFields !== void 0,
      metaBoxes: getAllMetaBoxes2()
    };
  })(MetaBoxesSection);

  // packages/edit-post/build-module/components/preferences-modal/index.js
  var import_jsx_runtime17 = __toESM(require_jsx_runtime());
  var { PreferenceToggleControl } = unlock(import_preferences7.privateApis);
  var { PreferencesModal } = unlock(import_editor13.privateApis);
  function EditPostPreferencesModal() {
    const extraSections = {
      general: /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(meta_boxes_section_default, { title: (0, import_i18n9.__)("Advanced") }),
      appearance: /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
        PreferenceToggleControl,
        {
          scope: "core/edit-post",
          featureName: "themeStyles",
          help: (0, import_i18n9.__)("Make the editor look like your theme."),
          label: (0, import_i18n9.__)("Use theme styles")
        }
      )
    };
    return /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(PreferencesModal, { extraSections });
  }

  // packages/edit-post/build-module/components/more-menu/index.js
  var import_jsx_runtime18 = __toESM(require_jsx_runtime());
  var { ToolsMoreMenuGroup, ViewMoreMenuGroup } = unlock(import_editor14.privateApis);
  var MoreMenu = () => {
    const isLargeViewport = (0, import_compose2.useViewportMatch)("large");
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsxs)(import_jsx_runtime18.Fragment, { children: [
      isLargeViewport && /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(ViewMoreMenuGroup, { children: /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
        import_preferences8.PreferenceToggleMenuItem,
        {
          scope: "core/edit-post",
          name: "fullscreenMode",
          label: (0, import_i18n10.__)("Fullscreen mode"),
          info: (0, import_i18n10.__)("Show and hide the admin user interface"),
          messageActivated: (0, import_i18n10.__)("Fullscreen mode activated."),
          messageDeactivated: (0, import_i18n10.__)(
            "Fullscreen mode deactivated."
          ),
          shortcut: import_keycodes.displayShortcut.secondary("f")
        }
      ) }),
      /* @__PURE__ */ (0, import_jsx_runtime18.jsxs)(ToolsMoreMenuGroup, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(manage_patterns_menu_item_default, {}),
        /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(WelcomeGuideMenuItem, {})
      ] }),
      /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(EditPostPreferencesModal, {})
    ] });
  };
  var more_menu_default = MoreMenu;

  // packages/edit-post/build-module/components/welcome-guide/index.js
  var import_data20 = __toESM(require_data());

  // packages/edit-post/build-module/components/welcome-guide/default.js
  var import_data18 = __toESM(require_data());
  var import_components7 = __toESM(require_components());
  var import_i18n11 = __toESM(require_i18n());
  var import_element9 = __toESM(require_element());

  // packages/edit-post/build-module/components/welcome-guide/image.js
  var import_jsx_runtime19 = __toESM(require_jsx_runtime());
  function WelcomeGuideImage({ nonAnimatedSrc, animatedSrc }) {
    return /* @__PURE__ */ (0, import_jsx_runtime19.jsxs)("picture", { className: "edit-post-welcome-guide__image", children: [
      /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
        "source",
        {
          srcSet: nonAnimatedSrc,
          media: "(prefers-reduced-motion: reduce)"
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime19.jsx)("img", { src: animatedSrc, width: "312", height: "240", alt: "" })
    ] });
  }

  // packages/edit-post/build-module/components/welcome-guide/default.js
  var import_jsx_runtime20 = __toESM(require_jsx_runtime());
  function WelcomeGuideDefault() {
    const { toggleFeature: toggleFeature2 } = (0, import_data18.useDispatch)(store);
    return /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
      import_components7.Guide,
      {
        className: "edit-post-welcome-guide",
        contentLabel: (0, import_i18n11.__)("Welcome to the editor"),
        finishButtonText: (0, import_i18n11.__)("Get started"),
        onFinish: () => toggleFeature2("welcomeGuide"),
        pages: [
          {
            image: /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
              WelcomeGuideImage,
              {
                nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.svg",
                animatedSrc: "https://s.w.org/images/block-editor/welcome-canvas.gif"
              }
            ),
            content: /* @__PURE__ */ (0, import_jsx_runtime20.jsxs)(import_jsx_runtime20.Fragment, { children: [
              /* @__PURE__ */ (0, import_jsx_runtime20.jsx)("h1", { className: "edit-post-welcome-guide__heading", children: (0, import_i18n11.__)("Welcome to the editor") }),
              /* @__PURE__ */ (0, import_jsx_runtime20.jsx)("p", { className: "edit-post-welcome-guide__text", children: (0, import_i18n11.__)(
                "In the WordPress editor, each paragraph, image, or video is presented as a distinct \u201Cblock\u201D of content."
              ) })
            ] })
          },
          {
            image: /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
              WelcomeGuideImage,
              {
                nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-editor.svg",
                animatedSrc: "https://s.w.org/images/block-editor/welcome-editor.gif"
              }
            ),
            content: /* @__PURE__ */ (0, import_jsx_runtime20.jsxs)(import_jsx_runtime20.Fragment, { children: [
              /* @__PURE__ */ (0, import_jsx_runtime20.jsx)("h1", { className: "edit-post-welcome-guide__heading", children: (0, import_i18n11.__)("Customize each block") }),
              /* @__PURE__ */ (0, import_jsx_runtime20.jsx)("p", { className: "edit-post-welcome-guide__text", children: (0, import_i18n11.__)(
                "Each block comes with its own set of controls for changing things like color, width, and alignment. These will show and hide automatically when you have a block selected."
              ) })
            ] })
          },
          {
            image: /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
              WelcomeGuideImage,
              {
                nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-library.svg",
                animatedSrc: "https://s.w.org/images/block-editor/welcome-library.gif"
              }
            ),
            content: /* @__PURE__ */ (0, import_jsx_runtime20.jsxs)(import_jsx_runtime20.Fragment, { children: [
              /* @__PURE__ */ (0, import_jsx_runtime20.jsx)("h1", { className: "edit-post-welcome-guide__heading", children: (0, import_i18n11.__)("Explore all blocks") }),
              /* @__PURE__ */ (0, import_jsx_runtime20.jsx)("p", { className: "edit-post-welcome-guide__text", children: (0, import_element9.createInterpolateElement)(
                (0, import_i18n11.__)(
                  "All of the blocks available to you live in the block library. You\u2019ll find it wherever you see the <InserterIconImage /> icon."
                ),
                {
                  InserterIconImage: /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
                    "img",
                    {
                      alt: (0, import_i18n11.__)("inserter"),
                      src: "data:image/svg+xml,%3Csvg width='18' height='18' viewBox='0 0 18 18' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Crect width='18' height='18' rx='2' fill='%231E1E1E'/%3E%3Cpath d='M9.22727 4V14M4 8.77273H14' stroke='white' stroke-width='1.5'/%3E%3C/svg%3E%0A"
                    }
                  )
                }
              ) })
            ] })
          },
          {
            image: /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
              WelcomeGuideImage,
              {
                nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.svg",
                animatedSrc: "https://s.w.org/images/block-editor/welcome-documentation.gif"
              }
            ),
            content: /* @__PURE__ */ (0, import_jsx_runtime20.jsxs)(import_jsx_runtime20.Fragment, { children: [
              /* @__PURE__ */ (0, import_jsx_runtime20.jsx)("h1", { className: "edit-post-welcome-guide__heading", children: (0, import_i18n11.__)("Learn more") }),
              /* @__PURE__ */ (0, import_jsx_runtime20.jsx)("p", { className: "edit-post-welcome-guide__text", children: (0, import_element9.createInterpolateElement)(
                (0, import_i18n11.__)(
                  "New to the block editor? Want to learn more about using it? <a>Here's a detailed guide.</a>"
                ),
                {
                  a: /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
                    import_components7.ExternalLink,
                    {
                      href: (0, import_i18n11.__)(
                        "https://wordpress.org/documentation/article/wordpress-block-editor/"
                      )
                    }
                  )
                }
              ) })
            ] })
          }
        ]
      }
    );
  }

  // packages/edit-post/build-module/components/welcome-guide/template.js
  var import_data19 = __toESM(require_data());
  var import_components8 = __toESM(require_components());
  var import_i18n12 = __toESM(require_i18n());
  var import_jsx_runtime21 = __toESM(require_jsx_runtime());
  function WelcomeGuideTemplate() {
    const { toggleFeature: toggleFeature2 } = (0, import_data19.useDispatch)(store);
    return /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
      import_components8.Guide,
      {
        className: "edit-template-welcome-guide",
        contentLabel: (0, import_i18n12.__)("Welcome to the template editor"),
        finishButtonText: (0, import_i18n12.__)("Get started"),
        onFinish: () => toggleFeature2("welcomeGuideTemplate"),
        pages: [
          {
            image: /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
              WelcomeGuideImage,
              {
                nonAnimatedSrc: "https://s.w.org/images/block-editor/welcome-template-editor.svg",
                animatedSrc: "https://s.w.org/images/block-editor/welcome-template-editor.gif"
              }
            ),
            content: /* @__PURE__ */ (0, import_jsx_runtime21.jsxs)(import_jsx_runtime21.Fragment, { children: [
              /* @__PURE__ */ (0, import_jsx_runtime21.jsx)("h1", { className: "edit-post-welcome-guide__heading", children: (0, import_i18n12.__)("Welcome to the template editor") }),
              /* @__PURE__ */ (0, import_jsx_runtime21.jsx)("p", { className: "edit-post-welcome-guide__text", children: (0, import_i18n12.__)(
                "Templates help define the layout of the site. You can customize all aspects of your posts and pages using blocks and patterns in this editor."
              ) })
            ] })
          }
        ]
      }
    );
  }

  // packages/edit-post/build-module/components/welcome-guide/index.js
  var import_jsx_runtime22 = __toESM(require_jsx_runtime());
  function WelcomeGuide({ postType }) {
    const { isActive, isEditingTemplate: isEditingTemplate2 } = (0, import_data20.useSelect)(
      (select3) => {
        const { isFeatureActive: isFeatureActive2 } = select3(store);
        const _isEditingTemplate = postType === "wp_template";
        const feature = _isEditingTemplate ? "welcomeGuideTemplate" : "welcomeGuide";
        return {
          isActive: isFeatureActive2(feature),
          isEditingTemplate: _isEditingTemplate
        };
      },
      [postType]
    );
    if (!isActive) {
      return null;
    }
    return isEditingTemplate2 ? /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(WelcomeGuideTemplate, {}) : /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(WelcomeGuideDefault, {});
  }

  // packages/edit-post/build-module/commands/use-commands.js
  var import_data21 = __toESM(require_data());
  var import_i18n13 = __toESM(require_i18n());
  var import_commands = __toESM(require_commands());
  var import_preferences9 = __toESM(require_preferences());
  var import_notices2 = __toESM(require_notices());
  function useCommands() {
    const { isFullscreen } = (0, import_data21.useSelect)((select3) => {
      const { get } = select3(import_preferences9.store);
      return {
        isFullscreen: get("core/edit-post", "fullscreenMode")
      };
    }, []);
    const { toggle } = (0, import_data21.useDispatch)(import_preferences9.store);
    const { createInfoNotice } = (0, import_data21.useDispatch)(import_notices2.store);
    (0, import_commands.useCommand)({
      name: "core/toggle-fullscreen-mode",
      label: isFullscreen ? (0, import_i18n13.__)("Exit fullscreen") : (0, import_i18n13.__)("Enter fullscreen"),
      icon: fullscreen_default,
      callback: ({ close }) => {
        toggle("core/edit-post", "fullscreenMode");
        close();
        createInfoNotice(
          isFullscreen ? (0, import_i18n13.__)("Fullscreen off.") : (0, import_i18n13.__)("Fullscreen on."),
          {
            id: "core/edit-post/toggle-fullscreen-mode/notice",
            type: "snackbar",
            actions: [
              {
                label: (0, import_i18n13.__)("Undo"),
                onClick: () => {
                  toggle("core/edit-post", "fullscreenMode");
                }
              }
            ]
          }
        );
      }
    });
  }

  // packages/edit-post/build-module/components/layout/use-should-iframe.js
  var import_editor15 = __toESM(require_editor());
  var import_data22 = __toESM(require_data());
  var import_blocks2 = __toESM(require_blocks());
  var import_block_editor = __toESM(require_block_editor());
  var isGutenbergPlugin = true ? true : false;
  function useShouldIframe() {
    return (0, import_data22.useSelect)((select3) => {
      const { getEditorSettings, getCurrentPostType, getDeviceType } = select3(import_editor15.store);
      return (
        // If the theme is block based and the Gutenberg plugin is active,
        // we ALWAYS use the iframe for consistency across the post and site
        // editor.
        isGutenbergPlugin && getEditorSettings().__unstableIsBlockBasedTheme || // We also still want to iframe all the special
        // editor features and modes such as device previews, zoom out, and
        // template/pattern editing.
        getDeviceType() !== "Desktop" || ["wp_template", "wp_block"].includes(getCurrentPostType()) || unlock(select3(import_block_editor.store)).isZoomOut() || // Finally, still iframe the editor if all blocks are v3 (which means
        // they are marked as iframe-compatible).
        select3(import_blocks2.store).getBlockTypes().every((type) => type.apiVersion >= 3)
      );
    }, []);
  }

  // packages/edit-post/build-module/hooks/use-navigate-to-entity-record.js
  var import_element10 = __toESM(require_element());
  var import_data23 = __toESM(require_data());
  var import_editor16 = __toESM(require_editor());
  function useNavigateToEntityRecord(initialPostId, initialPostType, defaultRenderingMode) {
    const [postHistory, dispatch2] = (0, import_element10.useReducer)(
      (historyState, { type, post: post2, previousRenderingMode: previousRenderingMode2 }) => {
        if (type === "push") {
          return [...historyState, { post: post2, previousRenderingMode: previousRenderingMode2 }];
        }
        if (type === "pop") {
          if (historyState.length > 1) {
            return historyState.slice(0, -1);
          }
        }
        return historyState;
      },
      [
        {
          post: { postId: initialPostId, postType: initialPostType }
        }
      ]
    );
    const { post, previousRenderingMode } = postHistory[postHistory.length - 1];
    const { getRenderingMode } = (0, import_data23.useSelect)(import_editor16.store);
    const { setRenderingMode } = (0, import_data23.useDispatch)(import_editor16.store);
    const onNavigateToEntityRecord = (0, import_element10.useCallback)(
      (params) => {
        dispatch2({
          type: "push",
          post: { postId: params.postId, postType: params.postType },
          // Save the current rendering mode so we can restore it when navigating back.
          previousRenderingMode: getRenderingMode()
        });
        setRenderingMode(defaultRenderingMode);
      },
      [getRenderingMode, setRenderingMode, defaultRenderingMode]
    );
    const onNavigateToPreviousEntityRecord = (0, import_element10.useCallback)(() => {
      dispatch2({ type: "pop" });
      if (previousRenderingMode) {
        setRenderingMode(previousRenderingMode);
      }
    }, [setRenderingMode, previousRenderingMode]);
    return {
      currentPost: post,
      onNavigateToEntityRecord,
      onNavigateToPreviousEntityRecord: postHistory.length > 1 ? onNavigateToPreviousEntityRecord : void 0
    };
  }

  // packages/edit-post/build-module/components/meta-boxes/use-meta-box-initialization.js
  var import_data24 = __toESM(require_data());
  var import_editor17 = __toESM(require_editor());
  var import_element11 = __toESM(require_element());
  var useMetaBoxInitialization = (enabled) => {
    const isEnabledAndEditorReady = (0, import_data24.useSelect)(
      (select3) => enabled && select3(import_editor17.store).__unstableIsEditorReady(),
      [enabled]
    );
    const { initializeMetaBoxes: initializeMetaBoxes2 } = (0, import_data24.useDispatch)(store);
    (0, import_element11.useEffect)(() => {
      if (isEnabledAndEditorReady) {
        initializeMetaBoxes2();
      }
    }, [isEnabledAndEditorReady, initializeMetaBoxes2]);
  };

  // packages/edit-post/build-module/components/layout/index.js
  var import_jsx_runtime23 = __toESM(require_jsx_runtime());
  var { useCommandContext } = unlock(import_commands2.privateApis);
  var { Editor, FullscreenMode } = unlock(import_editor18.privateApis);
  var { BlockKeyboardShortcuts } = unlock(import_block_library.privateApis);
  var DESIGN_POST_TYPES = [
    "wp_template",
    "wp_template_part",
    "wp_block",
    "wp_navigation"
  ];
  function useEditorStyles(settings) {
    const { hasThemeStyleSupport } = (0, import_data25.useSelect)((select3) => {
      return {
        hasThemeStyleSupport: select3(store).isFeatureActive("themeStyles")
      };
    }, []);
    return (0, import_element12.useMemo)(() => {
      const presetStyles = settings.styles?.filter(
        (style) => style.__unstableType && style.__unstableType !== "theme"
      ) ?? [];
      const defaultEditorStyles = [
        ...settings?.defaultEditorStyles ?? [],
        ...presetStyles
      ];
      const hasThemeStyles = hasThemeStyleSupport && presetStyles.length !== (settings.styles?.length ?? 0);
      if (!settings.disableLayoutStyles && !hasThemeStyles) {
        defaultEditorStyles.push({
          css: getLayoutStyles({
            style: {},
            selector: "body",
            hasBlockGapSupport: false,
            hasFallbackGapSupport: true,
            fallbackGapValue: "0.5em"
          })
        });
      }
      return hasThemeStyles ? settings.styles ?? [] : defaultEditorStyles;
    }, [
      settings.defaultEditorStyles,
      settings.disableLayoutStyles,
      settings.styles,
      hasThemeStyleSupport
    ]);
  }
  function MetaBoxesMain({ isLegacy }) {
    const [isOpen, openHeight, hasAnyVisible] = (0, import_data25.useSelect)((select3) => {
      const { get } = select3(import_preferences10.store);
      const { isMetaBoxLocationVisible: isMetaBoxLocationVisible2 } = select3(store);
      return [
        !!get("core/edit-post", "metaBoxesMainIsOpen"),
        get("core/edit-post", "metaBoxesMainOpenHeight"),
        isMetaBoxLocationVisible2("normal") || isMetaBoxLocationVisible2("advanced") || isMetaBoxLocationVisible2("side")
      ];
    }, []);
    const { set: setPreference } = (0, import_data25.useDispatch)(import_preferences10.store);
    const metaBoxesMainRef = (0, import_element12.useRef)();
    const isShort = (0, import_compose3.useMediaQuery)("(max-height: 549px)");
    const [{ min, max }, setHeightConstraints] = (0, import_element12.useState)(() => ({}));
    const effectSizeConstraints = (0, import_compose3.useRefEffect)((node) => {
      const container = node.closest(
        ".interface-interface-skeleton__content"
      );
      if (!container) {
        return;
      }
      const noticeLists = container.querySelectorAll(
        ":scope > .components-notice-list"
      );
      const resizeHandle = container.querySelector(
        ".edit-post-meta-boxes-main__presenter"
      );
      const deriveConstraints = () => {
        const fullHeight = container.offsetHeight;
        let nextMax = fullHeight;
        for (const element of noticeLists) {
          nextMax -= element.offsetHeight;
        }
        const nextMin = resizeHandle.offsetHeight;
        setHeightConstraints({ min: nextMin, max: nextMax });
      };
      const observer = new window.ResizeObserver(deriveConstraints);
      observer.observe(container);
      for (const element of noticeLists) {
        observer.observe(element);
      }
      return () => observer.disconnect();
    }, []);
    const resizeDataRef = (0, import_element12.useRef)({});
    const separatorRef = (0, import_element12.useRef)();
    const separatorHelpId = (0, import_element12.useId)();
    const applyHeight = (candidateHeight = "auto", isPersistent, isInstant) => {
      if (candidateHeight === "auto") {
        isPersistent = false;
      } else {
        candidateHeight = Math.min(max, Math.max(min, candidateHeight));
      }
      if (isPersistent) {
        setPreference(
          "core/edit-post",
          "metaBoxesMainOpenHeight",
          candidateHeight
        );
      } else if (!isShort) {
        separatorRef.current.ariaValueNow = getAriaValueNow(candidateHeight);
      }
      if (isInstant) {
        metaBoxesMainRef.current.updateSize({
          height: candidateHeight,
          // Oddly, when the event that triggered this was not from the mouse (e.g. keydown),
          // if `width` is left unspecified a subsequent drag gesture applies a fixed
          // width and the pane fails to widen/narrow with parent width changes from
          // sidebars opening/closing or window resizes.
          width: "auto"
        });
      }
    };
    const getRenderValues = (0, import_compose3.useEvent)(() => ({ isOpen, openHeight, min }));
    (0, import_element12.useEffect)(() => {
      const fresh = getRenderValues();
      if (fresh.min !== void 0 && metaBoxesMainRef.current) {
        const usedOpenHeight = isShort ? "auto" : fresh.openHeight;
        const usedHeight = fresh.isOpen ? usedOpenHeight : fresh.min;
        applyHeight(usedHeight, false, true);
      }
    }, [isShort]);
    if (!hasAnyVisible) {
      return;
    }
    const contents = /* @__PURE__ */ (0, import_jsx_runtime23.jsxs)(
      "div",
      {
        className: "edit-post-layout__metaboxes edit-post-meta-boxes-main__liner",
        hidden: !isLegacy && !isOpen,
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(MetaBoxes, { location: "normal" }),
          /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(MetaBoxes, { location: "advanced" })
        ]
      }
    );
    if (isLegacy) {
      return contents;
    }
    const isAutoHeight = openHeight === void 0;
    const getAriaValueNow = (height) => Math.round((height - min) / (max - min) * 100);
    const usedAriaValueNow = max === void 0 || isAutoHeight ? 50 : getAriaValueNow(openHeight);
    const persistIsOpen = (to = !isOpen) => setPreference("core/edit-post", "metaBoxesMainIsOpen", to);
    const onSeparatorKeyDown = (event) => {
      const delta = { ArrowUp: 20, ArrowDown: -20 }[event.key];
      if (delta) {
        const pane = metaBoxesMainRef.current.resizable;
        const fromHeight = isAutoHeight ? pane.offsetHeight : openHeight;
        const nextHeight = delta + fromHeight;
        applyHeight(nextHeight, true, true);
        persistIsOpen(nextHeight > min);
        event.preventDefault();
      }
    };
    const paneLabel = (0, import_i18n14.__)("Meta Boxes");
    const toggle = /* @__PURE__ */ (0, import_jsx_runtime23.jsxs)(
      "button",
      {
        "aria-expanded": isOpen,
        onClick: ({ detail }) => {
          const { isToggleInferred } = resizeDataRef.current;
          if (isShort || !detail || isToggleInferred) {
            persistIsOpen();
            const usedOpenHeight = isShort ? "auto" : openHeight;
            const usedHeight = isOpen ? min : usedOpenHeight;
            applyHeight(usedHeight, false, true);
          }
        },
        ...isShort && {
          onMouseDown: (event) => event.stopPropagation(),
          onTouchStart: (event) => event.stopPropagation()
        },
        children: [
          paneLabel,
          /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_components9.Icon, { icon: isOpen ? chevron_up_default : chevron_down_default })
        ]
      }
    );
    const separator = !isShort && /* @__PURE__ */ (0, import_jsx_runtime23.jsxs)(import_jsx_runtime23.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_components9.Tooltip, { text: (0, import_i18n14.__)("Drag to resize"), children: /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
        "button",
        {
          ref: separatorRef,
          role: "separator",
          "aria-valuenow": usedAriaValueNow,
          "aria-label": (0, import_i18n14.__)("Drag to resize"),
          "aria-describedby": separatorHelpId,
          onKeyDown: onSeparatorKeyDown
        }
      ) }),
      /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_components9.VisuallyHidden, { id: separatorHelpId, children: (0, import_i18n14.__)(
        "Use up and down arrow keys to resize the meta box panel."
      ) })
    ] });
    const paneProps = (
      /** @type {Parameters<typeof ResizableBox>[0]} */
      {
        as: navigable_region_default,
        ref: metaBoxesMainRef,
        className: "edit-post-meta-boxes-main",
        defaultSize: { height: isOpen ? openHeight : 0 },
        minHeight: min,
        maxHeight: max,
        enable: { top: true },
        handleClasses: { top: "edit-post-meta-boxes-main__presenter" },
        handleComponent: {
          top: /* @__PURE__ */ (0, import_jsx_runtime23.jsxs)(import_jsx_runtime23.Fragment, { children: [
            toggle,
            separator
          ] })
        },
        // Avoids hiccups while dragging over objects like iframes and ensures that
        // the event to end the drag is captured by the target (resize handle)
        // whether or not it’s under the pointer.
        onPointerDown: ({ pointerId, target }) => {
          if (separatorRef.current?.parentElement.contains(target)) {
            target.setPointerCapture(pointerId);
          }
        },
        onResizeStart: ({ timeStamp }, direction, elementRef) => {
          if (isAutoHeight) {
            applyHeight(elementRef.offsetHeight, false, true);
          }
          elementRef.classList.add("is-resizing");
          resizeDataRef.current = { timeStamp, maxDelta: 0 };
        },
        onResize: (event, direction, elementRef, delta) => {
          const { maxDelta } = resizeDataRef.current;
          const newDelta = Math.abs(delta.height);
          resizeDataRef.current.maxDelta = Math.max(maxDelta, newDelta);
          applyHeight(metaBoxesMainRef.current.state.height);
        },
        onResizeStop: (event, direction, elementRef) => {
          elementRef.classList.remove("is-resizing");
          const duration = event.timeStamp - resizeDataRef.current.timeStamp;
          const wasSeparator = event.target === separatorRef.current;
          const { maxDelta } = resizeDataRef.current;
          const isToggleInferred = maxDelta < 1 || duration < 144 && maxDelta < 5;
          if (isShort || !wasSeparator && isToggleInferred) {
            resizeDataRef.current.isToggleInferred = true;
          } else {
            const { height } = metaBoxesMainRef.current.state;
            const nextIsOpen = height > min;
            persistIsOpen(nextIsOpen);
            if (nextIsOpen) {
              applyHeight(height, true);
            }
          }
        }
      }
    );
    return /* @__PURE__ */ (0, import_jsx_runtime23.jsxs)(import_components9.ResizableBox, { "aria-label": paneLabel, ...paneProps, children: [
      /* @__PURE__ */ (0, import_jsx_runtime23.jsx)("meta", { ref: effectSizeConstraints }),
      contents
    ] });
  }
  function Layout({
    postId: initialPostId,
    postType: initialPostType,
    settings,
    initialEdits
  }) {
    useCommands();
    const shouldIframe = useShouldIframe();
    const { createErrorNotice } = (0, import_data25.useDispatch)(import_notices3.store);
    const {
      currentPost: { postId: currentPostId, postType: currentPostType },
      onNavigateToEntityRecord,
      onNavigateToPreviousEntityRecord
    } = useNavigateToEntityRecord(
      initialPostId,
      initialPostType,
      "post-only"
    );
    const isEditingTemplate2 = currentPostType === "wp_template";
    const {
      mode,
      isFullscreenActive,
      hasResolvedMode,
      hasActiveMetaboxes,
      hasBlockSelected,
      showIconLabels,
      isDistractionFree,
      showMetaBoxes,
      isWelcomeGuideVisible,
      templateId,
      isDevicePreview
    } = (0, import_data25.useSelect)(
      (select3) => {
        const { get } = select3(import_preferences10.store);
        const { isFeatureActive: isFeatureActive2, hasMetaBoxes: hasMetaBoxes2 } = select3(store);
        const { canUser, getPostType, getTemplateId } = unlock(
          select3(import_core_data6.store)
        );
        const supportsTemplateMode = settings.supportsTemplateMode;
        const isViewable = getPostType(currentPostType)?.viewable ?? false;
        const canViewTemplate = canUser("read", {
          kind: "postType",
          name: "wp_template"
        });
        const { getBlockSelectionStart, isZoomOut } = unlock(
          select3(import_block_editor2.store)
        );
        const { getEditorMode: getEditorMode2, getDefaultRenderingMode, getDeviceType } = unlock(select3(import_editor18.store));
        const isNotDesignPostType = !DESIGN_POST_TYPES.includes(currentPostType);
        const isDirectlyEditingPattern = currentPostType === "wp_block" && !onNavigateToPreviousEntityRecord;
        const _templateId = getTemplateId(currentPostType, currentPostId);
        const defaultMode = getDefaultRenderingMode(currentPostType);
        return {
          mode: getEditorMode2(),
          isFullscreenActive: isFeatureActive2("fullscreenMode"),
          hasActiveMetaboxes: hasMetaBoxes2(),
          hasResolvedMode: defaultMode === "template-locked" ? !!_templateId : defaultMode !== void 0,
          hasBlockSelected: !!getBlockSelectionStart(),
          showIconLabels: get("core", "showIconLabels"),
          isDistractionFree: get("core", "distractionFree"),
          showMetaBoxes: isNotDesignPostType && !isZoomOut() || isDirectlyEditingPattern,
          isWelcomeGuideVisible: isFeatureActive2("welcomeGuide"),
          templateId: supportsTemplateMode && isViewable && canViewTemplate && !isEditingTemplate2 ? _templateId : null,
          isDevicePreview: getDeviceType() !== "Desktop"
        };
      },
      [
        currentPostType,
        currentPostId,
        isEditingTemplate2,
        settings.supportsTemplateMode,
        onNavigateToPreviousEntityRecord
      ]
    );
    useMetaBoxInitialization(hasActiveMetaboxes && hasResolvedMode);
    const commandContext = hasBlockSelected ? "block-selection-edit" : "entity-edit";
    useCommandContext(commandContext);
    const styles = useEditorStyles(settings);
    const editorSettings = (0, import_element12.useMemo)(
      () => ({
        ...settings,
        styles,
        onNavigateToEntityRecord,
        onNavigateToPreviousEntityRecord,
        defaultRenderingMode: "post-only"
      }),
      [
        settings,
        styles,
        onNavigateToEntityRecord,
        onNavigateToPreviousEntityRecord
      ]
    );
    if (showIconLabels) {
      document.body.classList.add("show-icon-labels");
    } else {
      document.body.classList.remove("show-icon-labels");
    }
    const navigateRegionsProps = (0, import_components9.__unstableUseNavigateRegions)();
    const className = clsx_default("edit-post-layout", "is-mode-" + mode, {
      "has-metaboxes": hasActiveMetaboxes
    });
    function onPluginAreaError(name) {
      createErrorNotice(
        (0, import_i18n14.sprintf)(
          /* translators: %s: plugin name */
          (0, import_i18n14.__)(
            'The "%s" plugin has encountered an error and cannot be rendered.'
          ),
          name
        )
      );
    }
    const { createSuccessNotice } = (0, import_data25.useDispatch)(import_notices3.store);
    const onActionPerformed = (0, import_element12.useCallback)(
      (actionId, items) => {
        switch (actionId) {
          case "move-to-trash":
            {
              document.location.href = (0, import_url5.addQueryArgs)("edit.php", {
                trashed: 1,
                post_type: items[0].type,
                ids: items[0].id
              });
            }
            break;
          case "duplicate-post":
            {
              const newItem = items[0];
              const title = typeof newItem.title === "string" ? newItem.title : newItem.title?.rendered;
              createSuccessNotice(
                (0, import_i18n14.sprintf)(
                  // translators: %s: Title of the created post or template, e.g: "Hello world".
                  (0, import_i18n14.__)('"%s" successfully created.'),
                  (0, import_html_entities.decodeEntities)(title) || (0, import_i18n14.__)("(no title)")
                ),
                {
                  type: "snackbar",
                  id: "duplicate-post-action",
                  actions: [
                    {
                      label: (0, import_i18n14.__)("Edit"),
                      onClick: () => {
                        const postId = newItem.id;
                        document.location.href = (0, import_url5.addQueryArgs)("post.php", {
                          post: postId,
                          action: "edit"
                        });
                      }
                    }
                  ]
                }
              );
            }
            break;
        }
      },
      [createSuccessNotice]
    );
    const initialPost = (0, import_element12.useMemo)(() => {
      return {
        type: initialPostType,
        id: initialPostId
      };
    }, [initialPostType, initialPostId]);
    const backButton = (0, import_compose3.useViewportMatch)("medium") && isFullscreenActive ? /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(back_button_default, { initialPost }) : null;
    return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_components9.SlotFillProvider, { children: /* @__PURE__ */ (0, import_jsx_runtime23.jsxs)(import_editor18.ErrorBoundary, { canCopyContent: true, children: [
      /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(WelcomeGuide, { postType: currentPostType }),
      /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
        "div",
        {
          className: navigateRegionsProps.className,
          ...navigateRegionsProps,
          ref: navigateRegionsProps.ref,
          children: /* @__PURE__ */ (0, import_jsx_runtime23.jsxs)(
            Editor,
            {
              settings: editorSettings,
              initialEdits,
              postType: currentPostType,
              postId: currentPostId,
              templateId,
              className,
              forceIsDirty: hasActiveMetaboxes,
              disableIframe: !shouldIframe,
              autoFocus: !isWelcomeGuideVisible,
              onActionPerformed,
              extraSidebarPanels: showMetaBoxes && /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(MetaBoxes, { location: "side" }),
              extraContent: !isDistractionFree && showMetaBoxes && /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
                MetaBoxesMain,
                {
                  isLegacy: !shouldIframe || isDevicePreview
                }
              ),
              children: [
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_editor18.PostLockedModal, {}),
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(EditorInitialization, {}),
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(FullscreenMode, { isActive: isFullscreenActive }),
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(BrowserURL, {}),
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_editor18.UnsavedChangesWarning, {}),
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_editor18.AutosaveMonitor, {}),
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_editor18.LocalAutosaveMonitor, {}),
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(keyboard_shortcuts_default, {}),
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_editor18.EditorKeyboardShortcutsRegister, {}),
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(BlockKeyboardShortcuts, {}),
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(InitPatternModal, {}),
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_plugins.PluginArea, { onError: onPluginAreaError }),
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(more_menu_default, {}),
                backButton,
                /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(import_editor18.EditorSnackbars, {})
              ]
            }
          )
        }
      )
    ] }) });
  }
  var layout_default = Layout;

  // packages/edit-post/build-module/deprecated.js
  var import_editor19 = __toESM(require_editor());
  var import_url6 = __toESM(require_url());
  var import_deprecated3 = __toESM(require_deprecated());
  var import_jsx_runtime24 = __toESM(require_jsx_runtime());
  var { PluginPostExcerpt } = unlock(import_editor19.privateApis);
  var isSiteEditor = (0, import_url6.getPath)(window.location.href)?.includes(
    "site-editor.php"
  );
  var deprecateSlot = (name) => {
    (0, import_deprecated3.default)(`wp.editPost.${name}`, {
      since: "6.6",
      alternative: `wp.editor.${name}`
    });
  };
  function PluginBlockSettingsMenuItem(props) {
    if (isSiteEditor) {
      return null;
    }
    deprecateSlot("PluginBlockSettingsMenuItem");
    return /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(import_editor19.PluginBlockSettingsMenuItem, { ...props });
  }
  function PluginDocumentSettingPanel(props) {
    if (isSiteEditor) {
      return null;
    }
    deprecateSlot("PluginDocumentSettingPanel");
    return /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(import_editor19.PluginDocumentSettingPanel, { ...props });
  }
  function PluginMoreMenuItem(props) {
    if (isSiteEditor) {
      return null;
    }
    deprecateSlot("PluginMoreMenuItem");
    return /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(import_editor19.PluginMoreMenuItem, { ...props });
  }
  function PluginPrePublishPanel(props) {
    if (isSiteEditor) {
      return null;
    }
    deprecateSlot("PluginPrePublishPanel");
    return /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(import_editor19.PluginPrePublishPanel, { ...props });
  }
  function PluginPostPublishPanel(props) {
    if (isSiteEditor) {
      return null;
    }
    deprecateSlot("PluginPostPublishPanel");
    return /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(import_editor19.PluginPostPublishPanel, { ...props });
  }
  function PluginPostStatusInfo(props) {
    if (isSiteEditor) {
      return null;
    }
    deprecateSlot("PluginPostStatusInfo");
    return /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(import_editor19.PluginPostStatusInfo, { ...props });
  }
  function PluginSidebar(props) {
    if (isSiteEditor) {
      return null;
    }
    deprecateSlot("PluginSidebar");
    return /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(import_editor19.PluginSidebar, { ...props });
  }
  function PluginSidebarMoreMenuItem(props) {
    if (isSiteEditor) {
      return null;
    }
    deprecateSlot("PluginSidebarMoreMenuItem");
    return /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(import_editor19.PluginSidebarMoreMenuItem, { ...props });
  }
  function __experimentalPluginPostExcerpt() {
    if (isSiteEditor) {
      return null;
    }
    (0, import_deprecated3.default)("wp.editPost.__experimentalPluginPostExcerpt", {
      since: "6.6",
      hint: "Core and custom panels can be access programmatically using their panel name.",
      link: "https://developer.wordpress.org/block-editor/reference-guides/slotfills/plugin-document-setting-panel/#accessing-a-panel-programmatically"
    });
    return PluginPostExcerpt;
  }

  // packages/edit-post/build-module/index.js
  var import_jsx_runtime25 = __toESM(require_jsx_runtime());
  var {
    BackButton: __experimentalMainDashboardButton,
    registerCoreBlockBindingsSources
  } = unlock(import_editor20.privateApis);
  function initializeEditor(id, postType, postId, settings, initialEdits) {
    const isMediumOrBigger = window.matchMedia("(min-width: 782px)").matches;
    const target = document.getElementById(id);
    const root = (0, import_element13.createRoot)(target);
    (0, import_data26.dispatch)(import_preferences11.store).setDefaults("core/edit-post", {
      fullscreenMode: true,
      themeStyles: true,
      welcomeGuide: true,
      welcomeGuideTemplate: true
    });
    (0, import_data26.dispatch)(import_preferences11.store).setDefaults("core", {
      allowRightClickOverrides: true,
      editorMode: "visual",
      editorTool: "edit",
      fixedToolbar: false,
      hiddenBlockTypes: [],
      inactivePanels: [],
      openPanels: ["post-status"],
      showBlockBreadcrumbs: true,
      showIconLabels: false,
      showListViewByDefault: false,
      enableChoosePatternModal: true,
      isPublishSidebarEnabled: true
    });
    if (window.__experimentalMediaProcessing) {
      (0, import_data26.dispatch)(import_preferences11.store).setDefaults("core/media", {
        requireApproval: true,
        optimizeOnUpload: true
      });
    }
    (0, import_data26.dispatch)(import_blocks3.store).reapplyBlockTypeFilters();
    if (isMediumOrBigger && (0, import_data26.select)(import_preferences11.store).get("core", "showListViewByDefault") && !(0, import_data26.select)(import_preferences11.store).get("core", "distractionFree")) {
      (0, import_data26.dispatch)(import_editor20.store).setIsListViewOpened(true);
    }
    (0, import_block_library2.registerCoreBlocks)();
    registerCoreBlockBindingsSources();
    (0, import_widgets.registerLegacyWidgetBlock)({ inserter: false });
    (0, import_widgets.registerWidgetGroupBlock)({ inserter: false });
    if (true) {
      (0, import_block_library2.__experimentalRegisterExperimentalCoreBlocks)({
        enableFSEBlocks: settings.__unstableEnableFullSiteEditingBlocks
      });
    }
    const documentMode = document.compatMode === "CSS1Compat" ? "Standards" : "Quirks";
    if (documentMode !== "Standards") {
      console.warn(
        "Your browser is using Quirks Mode. \nThis can cause rendering issues such as blocks overlaying meta boxes in the editor. Quirks Mode can be triggered by PHP errors or HTML code appearing before the opening <!DOCTYPE html>. Try checking the raw page source or your site's PHP error log and resolving errors there, removing any HTML before the doctype, or disabling plugins."
      );
    }
    const isIphone = window.navigator.userAgent.indexOf("iPhone") !== -1;
    if (isIphone) {
      window.addEventListener("scroll", (event) => {
        const editorScrollContainer = document.getElementsByClassName(
          "interface-interface-skeleton__body"
        )[0];
        if (event.target === document) {
          if (window.scrollY > 100) {
            editorScrollContainer.scrollTop = editorScrollContainer.scrollTop + window.scrollY;
          }
          if (document.getElementsByClassName("is-mode-visual")[0]) {
            window.scrollTo(0, 0);
          }
        }
      });
    }
    window.addEventListener("dragover", (e) => e.preventDefault(), false);
    window.addEventListener("drop", (e) => e.preventDefault(), false);
    root.render(
      /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(import_element13.StrictMode, { children: /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(
        layout_default,
        {
          settings,
          postId,
          postType,
          initialEdits
        }
      ) })
    );
    return root;
  }
  function reinitializeEditor() {
    (0, import_deprecated4.default)("wp.editPost.reinitializeEditor", {
      since: "6.2",
      version: "6.3"
    });
  }
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
