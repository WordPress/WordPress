var wp;
(wp ||= {}).formatLibrary = (() => {
  var __create = Object.create;
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __getProtoOf = Object.getPrototypeOf;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
  var __require = /* @__PURE__ */ ((x) => typeof require !== "undefined" ? require : typeof Proxy !== "undefined" ? new Proxy(x, {
    get: (a, b) => (typeof require !== "undefined" ? require : a)[b]
  }) : x)(function(x) {
    if (typeof require !== "undefined") return require.apply(this, arguments);
    throw Error('Dynamic require of "' + x + '" is not supported');
  });
  var __commonJS = (cb, mod) => function __require2() {
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

  // package-external:@wordpress/rich-text
  var require_rich_text = __commonJS({
    "package-external:@wordpress/rich-text"(exports, module) {
      module.exports = window.wp.richText;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/block-editor
  var require_block_editor = __commonJS({
    "package-external:@wordpress/block-editor"(exports, module) {
      module.exports = window.wp.blockEditor;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/primitives
  var require_primitives = __commonJS({
    "package-external:@wordpress/primitives"(exports, module) {
      module.exports = window.wp.primitives;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
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

  // package-external:@wordpress/a11y
  var require_a11y = __commonJS({
    "package-external:@wordpress/a11y"(exports, module) {
      module.exports = window.wp.a11y;
    }
  });

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // packages/format-library/build-module/index.js
  var import_rich_text18 = __toESM(require_rich_text());

  // packages/format-library/build-module/bold/index.js
  var import_i18n = __toESM(require_i18n());
  var import_rich_text = __toESM(require_rich_text());
  var import_block_editor = __toESM(require_block_editor());

  // packages/icons/build-module/icon/index.js
  var import_element = __toESM(require_element());
  var icon_default = (0, import_element.forwardRef)(
    ({ icon, size = 24, ...props }, ref) => {
      return (0, import_element.cloneElement)(icon, {
        width: size,
        height: size,
        ...props,
        ref
      });
    }
  );

  // packages/icons/build-module/library/button.js
  var import_primitives = __toESM(require_primitives());
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  var button_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { d: "M8 12.5h8V11H8v1.5Z M19 6.5H5a2 2 0 0 0-2 2V15a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V8.5a2 2 0 0 0-2-2ZM5 8h14a.5.5 0 0 1 .5.5V15a.5.5 0 0 1-.5.5H5a.5.5 0 0 1-.5-.5V8.5A.5.5 0 0 1 5 8Z" }) });

  // packages/icons/build-module/library/code.js
  var import_primitives2 = __toESM(require_primitives());
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  var code_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M20.8 10.7l-4.3-4.3-1.1 1.1 4.3 4.3c.1.1.1.3 0 .4l-4.3 4.3 1.1 1.1 4.3-4.3c.7-.8.7-1.9 0-2.6zM4.2 11.8l4.3-4.3-1-1-4.3 4.3c-.7.7-.7 1.8 0 2.5l4.3 4.3 1.1-1.1-4.3-4.3c-.2-.1-.2-.3-.1-.4z" }) });

  // packages/icons/build-module/library/color.js
  var import_primitives3 = __toESM(require_primitives());
  var import_jsx_runtime3 = __toESM(require_jsx_runtime());
  var color_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.SVG, { viewBox: "0 0 24 24", xmlns: "http://www.w3.org/2000/svg", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M17.2 10.9c-.5-1-1.2-2.1-2.1-3.2-.6-.9-1.3-1.7-2.1-2.6L12 4l-1 1.1c-.6.9-1.3 1.7-2 2.6-.8 1.2-1.5 2.3-2 3.2-.6 1.2-1 2.2-1 3 0 3.4 2.7 6.1 6.1 6.1s6.1-2.7 6.1-6.1c0-.8-.3-1.8-1-3zm-5.1 7.6c-2.5 0-4.6-2.1-4.6-4.6 0-.3.1-1 .8-2.3.5-.9 1.1-1.9 2-3.1.7-.9 1.3-1.7 1.8-2.3.7.8 1.3 1.6 1.8 2.3.8 1.1 1.5 2.2 2 3.1.7 1.3.8 2 .8 2.3 0 2.5-2.1 4.6-4.6 4.6z" }) });

  // packages/icons/build-module/library/format-bold.js
  var import_primitives4 = __toESM(require_primitives());
  var import_jsx_runtime4 = __toESM(require_jsx_runtime());
  var format_bold_default = /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(import_primitives4.Path, { d: "M14.7 11.3c1-.6 1.5-1.6 1.5-3 0-2.3-1.3-3.4-4-3.4H7v14h5.8c1.4 0 2.5-.3 3.3-1 .8-.7 1.2-1.7 1.2-2.9.1-1.9-.8-3.1-2.6-3.7zm-5.1-4h2.3c.6 0 1.1.1 1.4.4.3.3.5.7.5 1.2s-.2 1-.5 1.2c-.3.3-.8.4-1.4.4H9.6V7.3zm4.6 9c-.4.3-1 .4-1.7.4H9.6v-3.9h2.9c.7 0 1.3.2 1.7.5.4.3.6.8.6 1.5s-.2 1.2-.6 1.5z" }) });

  // packages/icons/build-module/library/format-italic.js
  var import_primitives5 = __toESM(require_primitives());
  var import_jsx_runtime5 = __toESM(require_jsx_runtime());
  var format_italic_default = /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(import_primitives5.Path, { d: "M12.5 5L10 19h1.9l2.5-14z" }) });

  // packages/icons/build-module/library/format-strikethrough.js
  var import_primitives6 = __toESM(require_primitives());
  var import_jsx_runtime6 = __toESM(require_jsx_runtime());
  var format_strikethrough_default = /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_primitives6.Path, { d: "M9.1 9v-.5c0-.6.2-1.1.7-1.4.5-.3 1.2-.5 2-.5.7 0 1.4.1 2.1.3.7.2 1.4.5 2.1.9l.2-1.9c-.6-.3-1.2-.5-1.9-.7-.8-.1-1.6-.2-2.4-.2-1.5 0-2.7.3-3.6 1-.8.7-1.2 1.5-1.2 2.6V9h2zM20 12H4v1h8.3c.3.1.6.2.8.3.5.2.9.5 1.1.8.3.3.4.7.4 1.2 0 .7-.2 1.1-.8 1.5-.5.3-1.2.5-2.1.5-.8 0-1.6-.1-2.4-.3-.8-.2-1.5-.5-2.2-.8L7 18.1c.5.2 1.2.4 2 .6.8.2 1.6.3 2.4.3 1.7 0 3-.3 3.9-1 .9-.7 1.3-1.6 1.3-2.8 0-.9-.2-1.7-.7-2.2H20v-1z" }) });

  // packages/icons/build-module/library/help.js
  var import_primitives7 = __toESM(require_primitives());
  var import_jsx_runtime7 = __toESM(require_jsx_runtime());
  var help_default = /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsx)(import_primitives7.Path, { d: "M12 4a8 8 0 1 1 .001 16.001A8 8 0 0 1 12 4Zm0 1.5a6.5 6.5 0 1 0-.001 13.001A6.5 6.5 0 0 0 12 5.5Zm.75 11h-1.5V15h1.5v1.5Zm-.445-9.234a3 3 0 0 1 .445 5.89V14h-1.5v-1.25c0-.57.452-.958.917-1.01A1.5 1.5 0 0 0 12 8.75a1.5 1.5 0 0 0-1.5 1.5H9a3 3 0 0 1 3.305-2.984Z" }) });

  // packages/icons/build-module/library/language.js
  var import_primitives8 = __toESM(require_primitives());
  var import_jsx_runtime8 = __toESM(require_jsx_runtime());
  var language_default = /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_primitives8.Path, { d: "M17.5 10h-1.7l-3.7 10.5h1.7l.9-2.6h3.9l.9 2.6h1.7L17.5 10zm-2.2 6.3 1.4-4 1.4 4h-2.8zm-4.8-3.8c1.6-1.8 2.9-3.6 3.7-5.7H16V5.2h-5.8V3H8.8v2.2H3v1.5h9.6c-.7 1.6-1.8 3.1-3.1 4.6C8.6 10.2 7.8 9 7.2 8H5.6c.6 1.4 1.7 2.9 2.9 4.4l-2.4 2.4c-.3.4-.7.8-1.1 1.2l1 1 1.2-1.2c.8-.8 1.6-1.5 2.3-2.3.8.9 1.7 1.7 2.5 2.5l.6-1.5c-.7-.6-1.4-1.3-2.1-2z" }) });

  // packages/icons/build-module/library/link.js
  var import_primitives9 = __toESM(require_primitives());
  var import_jsx_runtime9 = __toESM(require_jsx_runtime());
  var link_default = /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives9.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(import_primitives9.Path, { d: "M10 17.389H8.444A5.194 5.194 0 1 1 8.444 7H10v1.5H8.444a3.694 3.694 0 0 0 0 7.389H10v1.5ZM14 7h1.556a5.194 5.194 0 0 1 0 10.39H14v-1.5h1.556a3.694 3.694 0 0 0 0-7.39H14V7Zm-4.5 6h5v-1.5h-5V13Z" }) });

  // packages/icons/build-module/library/math.js
  var import_primitives10 = __toESM(require_primitives());
  var import_jsx_runtime10 = __toESM(require_jsx_runtime());
  var math_default = /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives10.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime10.jsx)(import_primitives10.Path, { d: "M11.2 6.8c-.7 0-1.4.5-1.6 1.1l-2.8 7.5-1.2-1.8c-.1-.2-.4-.3-.6-.3H3v1.5h1.6l1.2 1.8c.6.9 1.9.7 2.2-.3l2.9-7.9s.1-.2.2-.2h7.8V6.7h-7.8Zm5.3 3.4-1.9 1.9-1.9-1.9-1.1 1.1 1.9 1.9-1.9 1.9 1.1 1.1 1.9-1.9 1.9 1.9 1.1-1.1-1.9-1.9 1.9-1.9-1.1-1.1Z" }) });

  // packages/icons/build-module/library/subscript.js
  var import_primitives11 = __toESM(require_primitives());
  var import_jsx_runtime11 = __toESM(require_jsx_runtime());
  var subscript_default = /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives11.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)(import_primitives11.Path, { d: "M16.9 18.3l.8-1.2c.4-.6.7-1.2.9-1.6.2-.4.3-.8.3-1.2 0-.3-.1-.7-.2-1-.1-.3-.4-.5-.6-.7-.3-.2-.6-.3-1-.3s-.8.1-1.1.2c-.3.1-.7.3-1 .6l.2 1.3c.3-.3.5-.5.8-.6s.6-.2.9-.2c.3 0 .5.1.7.2.2.2.2.4.2.7 0 .3-.1.5-.2.8-.1.3-.4.7-.8 1.3L15 19.4h4.3v-1.2h-2.4zM14.1 7.2h-2L9.5 11 6.9 7.2h-2l3.6 5.3L4.7 18h2l2.7-4 2.7 4h2l-3.8-5.5 3.8-5.3z" }) });

  // packages/icons/build-module/library/superscript.js
  var import_primitives12 = __toESM(require_primitives());
  var import_jsx_runtime12 = __toESM(require_jsx_runtime());
  var superscript_default = /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives12.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_primitives12.Path, { d: "M16.9 10.3l.8-1.3c.4-.6.7-1.2.9-1.6.2-.4.3-.8.3-1.2 0-.3-.1-.7-.2-1-.2-.2-.4-.4-.7-.6-.3-.2-.6-.3-1-.3s-.8.1-1.1.2c-.3.1-.7.3-1 .6l.1 1.3c.3-.3.5-.5.8-.6s.6-.2.9-.2c.3 0 .5.1.7.2.2.2.2.4.2.7 0 .3-.1.5-.2.8-.1.3-.4.7-.8 1.3l-1.8 2.8h4.3v-1.2h-2.2zm-2.8-3.1h-2L9.5 11 6.9 7.2h-2l3.6 5.3L4.7 18h2l2.7-4 2.7 4h2l-3.8-5.5 3.8-5.3z" }) });

  // packages/icons/build-module/library/text-color.js
  var import_primitives13 = __toESM(require_primitives());
  var import_jsx_runtime13 = __toESM(require_jsx_runtime());
  var text_color_default = /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives13.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_primitives13.Path, { d: "M12.9 6h-2l-4 11h1.9l1.1-3h4.2l1.1 3h1.9L12.9 6zm-2.5 6.5l1.5-4.9 1.7 4.9h-3.2z" }) });

  // packages/format-library/build-module/lock-unlock.js
  var import_private_apis = __toESM(require_private_apis());
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/format-library"
  );

  // packages/format-library/build-module/bold/index.js
  var import_jsx_runtime14 = __toESM(require_jsx_runtime());
  var { essentialFormatKey } = unlock(import_block_editor.privateApis);
  var name = "core/bold";
  var title = (0, import_i18n.__)("Bold");
  var bold = {
    name,
    title,
    tagName: "strong",
    className: null,
    [essentialFormatKey]: true,
    edit({ isActive, value, onChange, onFocus }) {
      function onToggle() {
        onChange((0, import_rich_text.toggleFormat)(value, { type: name, title }));
      }
      function onClick() {
        onChange((0, import_rich_text.toggleFormat)(value, { type: name }));
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime14.jsxs)(import_jsx_runtime14.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
          import_block_editor.RichTextShortcut,
          {
            type: "primary",
            character: "b",
            onUse: onToggle
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
          import_block_editor.RichTextToolbarButton,
          {
            name: "bold",
            icon: format_bold_default,
            title,
            onClick,
            isActive,
            shortcutType: "primary",
            shortcutCharacter: "b"
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(
          import_block_editor.__unstableRichTextInputEvent,
          {
            inputType: "formatBold",
            onInput: onToggle
          }
        )
      ] });
    }
  };

  // packages/format-library/build-module/code/index.js
  var import_i18n2 = __toESM(require_i18n());
  var import_rich_text2 = __toESM(require_rich_text());
  var import_block_editor2 = __toESM(require_block_editor());
  var import_jsx_runtime15 = __toESM(require_jsx_runtime());
  var name2 = "core/code";
  var title2 = (0, import_i18n2.__)("Inline code");
  var code = {
    name: name2,
    title: title2,
    tagName: "code",
    className: null,
    __unstableInputRule(value) {
      const BACKTICK = "`";
      const { start, text } = value;
      const characterBefore = text[start - 1];
      if (characterBefore !== BACKTICK) {
        return value;
      }
      if (start - 2 < 0) {
        return value;
      }
      const indexBefore = text.lastIndexOf(BACKTICK, start - 2);
      if (indexBefore === -1) {
        return value;
      }
      const startIndex = indexBefore;
      const endIndex = start - 2;
      if (startIndex === endIndex) {
        return value;
      }
      value = (0, import_rich_text2.remove)(value, startIndex, startIndex + 1);
      value = (0, import_rich_text2.remove)(value, endIndex, endIndex + 1);
      value = (0, import_rich_text2.applyFormat)(value, { type: name2 }, startIndex, endIndex);
      return value;
    },
    edit({ value, onChange, onFocus, isActive }) {
      function onClick() {
        onChange((0, import_rich_text2.toggleFormat)(value, { type: name2, title: title2 }));
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime15.jsxs)(import_jsx_runtime15.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(
          import_block_editor2.RichTextShortcut,
          {
            type: "access",
            character: "x",
            onUse: onClick
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(
          import_block_editor2.RichTextToolbarButton,
          {
            icon: code_default,
            title: title2,
            onClick,
            isActive,
            role: "menuitemcheckbox"
          }
        )
      ] });
    }
  };

  // packages/format-library/build-module/image/index.js
  var import_components = __toESM(require_components());
  var import_i18n3 = __toESM(require_i18n());
  var import_element2 = __toESM(require_element());
  var import_rich_text3 = __toESM(require_rich_text());
  var import_block_editor3 = __toESM(require_block_editor());
  var import_jsx_runtime16 = __toESM(require_jsx_runtime());
  var ALLOWED_MEDIA_TYPES = ["image"];
  var name3 = "core/image";
  var title3 = (0, import_i18n3.__)("Inline image");
  function getCurrentImageId(activeObjectAttributes) {
    if (!activeObjectAttributes?.className) {
      return void 0;
    }
    const [, id] = activeObjectAttributes.className.match(/wp-image-(\d+)/) ?? [];
    return id ? parseInt(id, 10) : void 0;
  }
  var image = {
    name: name3,
    title: title3,
    keywords: [(0, import_i18n3.__)("photo"), (0, import_i18n3.__)("media")],
    object: true,
    tagName: "img",
    className: null,
    attributes: {
      className: "class",
      style: "style",
      url: "src",
      alt: "alt"
    },
    edit: Edit
  };
  function InlineUI({ value, onChange, activeObjectAttributes, contentRef }) {
    const { style, alt } = activeObjectAttributes;
    const width = style?.replace(/\D/g, "");
    const [editedWidth, setEditedWidth] = (0, import_element2.useState)(width);
    const [editedAlt, setEditedAlt] = (0, import_element2.useState)(alt);
    const hasChanged = editedWidth !== width || editedAlt !== alt;
    const popoverAnchor = (0, import_rich_text3.useAnchor)({
      editableContentElement: contentRef.current,
      settings: image
    });
    return /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
      import_components.Popover,
      {
        focusOnMount: false,
        anchor: popoverAnchor,
        className: "block-editor-format-toolbar__image-popover",
        children: /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
          "form",
          {
            className: "block-editor-format-toolbar__image-container-content",
            onSubmit: (event) => {
              const newReplacements = value.replacements.slice();
              newReplacements[value.start] = {
                type: name3,
                attributes: {
                  ...activeObjectAttributes,
                  style: editedWidth ? `width: ${editedWidth}px;` : "",
                  alt: editedAlt
                }
              };
              onChange({
                ...value,
                replacements: newReplacements
              });
              event.preventDefault();
            },
            children: /* @__PURE__ */ (0, import_jsx_runtime16.jsxs)(import_components.__experimentalVStack, { spacing: 4, children: [
              /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
                import_components.__experimentalNumberControl,
                {
                  __next40pxDefaultSize: true,
                  label: (0, import_i18n3.__)("Width"),
                  value: editedWidth,
                  min: 1,
                  onChange: (newWidth) => {
                    setEditedWidth(newWidth);
                  }
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
                import_components.TextareaControl,
                {
                  label: (0, import_i18n3.__)("Alternative text"),
                  __nextHasNoMarginBottom: true,
                  value: editedAlt,
                  onChange: (newAlt) => {
                    setEditedAlt(newAlt);
                  },
                  help: /* @__PURE__ */ (0, import_jsx_runtime16.jsxs)(import_jsx_runtime16.Fragment, { children: [
                    /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
                      import_components.ExternalLink,
                      {
                        href: (
                          // translators: Localized tutorial, if one exists. W3C Web Accessibility Initiative link has list of existing translations.
                          (0, import_i18n3.__)(
                            "https://www.w3.org/WAI/tutorials/images/decision-tree/"
                          )
                        ),
                        children: (0, import_i18n3.__)(
                          "Describe the purpose of the image."
                        )
                      }
                    ),
                    /* @__PURE__ */ (0, import_jsx_runtime16.jsx)("br", {}),
                    (0, import_i18n3.__)("Leave empty if decorative.")
                  ] })
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_components.__experimentalHStack, { justify: "right", children: /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
                import_components.Button,
                {
                  disabled: !hasChanged,
                  accessibleWhenDisabled: true,
                  variant: "primary",
                  type: "submit",
                  size: "compact",
                  children: (0, import_i18n3.__)("Apply")
                }
              ) })
            ] })
          }
        )
      }
    );
  }
  function Edit({
    value,
    onChange,
    onFocus,
    isObjectActive,
    activeObjectAttributes,
    contentRef
  }) {
    return /* @__PURE__ */ (0, import_jsx_runtime16.jsxs)(import_block_editor3.MediaUploadCheck, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
        import_block_editor3.MediaUpload,
        {
          allowedTypes: ALLOWED_MEDIA_TYPES,
          value: getCurrentImageId(activeObjectAttributes),
          onSelect: ({ id, url, alt, width: imgWidth }) => {
            onChange(
              (0, import_rich_text3.insertObject)(value, {
                type: name3,
                attributes: {
                  className: `wp-image-${id}`,
                  style: `width: ${Math.min(
                    imgWidth,
                    150
                  )}px;`,
                  url,
                  alt
                }
              })
            );
            onFocus();
          },
          render: ({ open }) => /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
            import_block_editor3.RichTextToolbarButton,
            {
              icon: /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
                import_components.SVG,
                {
                  xmlns: "http://www.w3.org/2000/svg",
                  viewBox: "0 0 24 24",
                  children: /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(import_components.Path, { d: "M4 18.5h16V17H4v1.5zM16 13v1.5h4V13h-4zM5.1 15h7.8c.6 0 1.1-.5 1.1-1.1V6.1c0-.6-.5-1.1-1.1-1.1H5.1C4.5 5 4 5.5 4 6.1v7.8c0 .6.5 1.1 1.1 1.1zm.4-8.5h7V10l-1-1c-.3-.3-.8-.3-1 0l-1.6 1.5-1.2-.7c-.3-.2-.6-.2-.9 0l-1.3 1V6.5zm0 6.1l1.8-1.3 1.3.8c.3.2.7.2.9-.1l1.5-1.4 1.5 1.4v1.5h-7v-.9z" })
                }
              ),
              title: isObjectActive ? (0, import_i18n3.__)("Replace image") : title3,
              onClick: open,
              isActive: isObjectActive
            }
          )
        }
      ),
      isObjectActive && /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
        InlineUI,
        {
          value,
          onChange,
          activeObjectAttributes,
          contentRef
        }
      )
    ] });
  }

  // packages/format-library/build-module/italic/index.js
  var import_i18n4 = __toESM(require_i18n());
  var import_rich_text4 = __toESM(require_rich_text());
  var import_block_editor4 = __toESM(require_block_editor());
  var import_jsx_runtime17 = __toESM(require_jsx_runtime());
  var { essentialFormatKey: essentialFormatKey2 } = unlock(import_block_editor4.privateApis);
  var name4 = "core/italic";
  var title4 = (0, import_i18n4.__)("Italic");
  var italic = {
    name: name4,
    title: title4,
    tagName: "em",
    className: null,
    [essentialFormatKey2]: true,
    edit({ isActive, value, onChange, onFocus }) {
      function onToggle() {
        onChange((0, import_rich_text4.toggleFormat)(value, { type: name4, title: title4 }));
      }
      function onClick() {
        onChange((0, import_rich_text4.toggleFormat)(value, { type: name4 }));
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime17.jsxs)(import_jsx_runtime17.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
          import_block_editor4.RichTextShortcut,
          {
            type: "primary",
            character: "i",
            onUse: onToggle
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
          import_block_editor4.RichTextToolbarButton,
          {
            name: "italic",
            icon: format_italic_default,
            title: title4,
            onClick,
            isActive,
            shortcutType: "primary",
            shortcutCharacter: "i"
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
          import_block_editor4.__unstableRichTextInputEvent,
          {
            inputType: "formatItalic",
            onInput: onToggle
          }
        )
      ] });
    }
  };

  // packages/format-library/build-module/link/index.js
  var import_i18n7 = __toESM(require_i18n());
  var import_element5 = __toESM(require_element());
  var import_rich_text6 = __toESM(require_rich_text());
  var import_url3 = __toESM(require_url());
  var import_block_editor6 = __toESM(require_block_editor());
  var import_html_entities = __toESM(require_html_entities());
  var import_a11y2 = __toESM(require_a11y());

  // packages/format-library/build-module/link/inline.js
  var import_element4 = __toESM(require_element());
  var import_i18n6 = __toESM(require_i18n());
  var import_a11y = __toESM(require_a11y());
  var import_components3 = __toESM(require_components());
  var import_url2 = __toESM(require_url());
  var import_rich_text5 = __toESM(require_rich_text());
  var import_block_editor5 = __toESM(require_block_editor());
  var import_data = __toESM(require_data());

  // packages/format-library/build-module/link/utils.js
  var import_url = __toESM(require_url());
  function isValidHref(href) {
    if (!href) {
      return false;
    }
    const trimmedHref = href.trim();
    if (!trimmedHref) {
      return false;
    }
    if (/^\S+:/.test(trimmedHref)) {
      const protocol = (0, import_url.getProtocol)(trimmedHref);
      if (!(0, import_url.isValidProtocol)(protocol)) {
        return false;
      }
      if (protocol.startsWith("http") && !/^https?:\/\/[^\/\s]/i.test(trimmedHref)) {
        return false;
      }
      const authority = (0, import_url.getAuthority)(trimmedHref);
      if (!(0, import_url.isValidAuthority)(authority)) {
        return false;
      }
      const path = (0, import_url.getPath)(trimmedHref);
      if (path && !(0, import_url.isValidPath)(path)) {
        return false;
      }
      const queryString = (0, import_url.getQueryString)(trimmedHref);
      if (queryString && !(0, import_url.isValidQueryString)(queryString)) {
        return false;
      }
      const fragment = (0, import_url.getFragment)(trimmedHref);
      if (fragment && !(0, import_url.isValidFragment)(fragment)) {
        return false;
      }
    }
    if (trimmedHref.startsWith("#") && !(0, import_url.isValidFragment)(trimmedHref)) {
      return false;
    }
    return true;
  }
  function createLinkFormat({
    url,
    type,
    id,
    opensInNewWindow,
    nofollow,
    cssClasses
  }) {
    const format = {
      type: "core/link",
      attributes: {
        url
      }
    };
    if (type) {
      format.attributes.type = type;
    }
    if (id) {
      format.attributes.id = id;
    }
    if (opensInNewWindow) {
      format.attributes.target = "_blank";
      format.attributes.rel = format.attributes.rel ? format.attributes.rel + " noreferrer noopener" : "noreferrer noopener";
    }
    if (nofollow) {
      format.attributes.rel = format.attributes.rel ? format.attributes.rel + " nofollow" : "nofollow";
    }
    const trimmedCssClasses = cssClasses?.trim();
    if (trimmedCssClasses?.length) {
      format.attributes.class = trimmedCssClasses;
    }
    return format;
  }
  function getFormatBoundary(value, format, startIndex = value.start, endIndex = value.end) {
    const EMPTY_BOUNDARIES = {
      start: null,
      end: null
    };
    const { formats } = value;
    let targetFormat;
    let initialIndex;
    if (!formats?.length) {
      return EMPTY_BOUNDARIES;
    }
    const newFormats = formats.slice();
    const formatAtStart = newFormats[startIndex]?.find(
      ({ type }) => type === format.type
    );
    const formatAtEnd = newFormats[endIndex]?.find(
      ({ type }) => type === format.type
    );
    const formatAtEndMinusOne = newFormats[endIndex - 1]?.find(
      ({ type }) => type === format.type
    );
    if (!!formatAtStart) {
      targetFormat = formatAtStart;
      initialIndex = startIndex;
    } else if (!!formatAtEnd) {
      targetFormat = formatAtEnd;
      initialIndex = endIndex;
    } else if (!!formatAtEndMinusOne) {
      targetFormat = formatAtEndMinusOne;
      initialIndex = endIndex - 1;
    } else {
      return EMPTY_BOUNDARIES;
    }
    const index = newFormats[initialIndex].indexOf(targetFormat);
    const walkingArgs = [newFormats, initialIndex, targetFormat, index];
    startIndex = walkToStart(...walkingArgs);
    endIndex = walkToEnd(...walkingArgs);
    startIndex = startIndex < 0 ? 0 : startIndex;
    return {
      start: startIndex,
      end: endIndex
    };
  }
  function walkToBoundary(formats, initialIndex, targetFormatRef, formatIndex, direction) {
    let index = initialIndex;
    const directions = {
      forwards: 1,
      backwards: -1
    };
    const directionIncrement = directions[direction] || 1;
    const inverseDirectionIncrement = directionIncrement * -1;
    while (formats[index] && formats[index][formatIndex] === targetFormatRef) {
      index = index + directionIncrement;
    }
    index = index + inverseDirectionIncrement;
    return index;
  }
  var partialRight = (fn, ...partialArgs) => (...args) => fn(...args, ...partialArgs);
  var walkToStart = partialRight(walkToBoundary, "backwards");
  var walkToEnd = partialRight(walkToBoundary, "forwards");

  // packages/format-library/build-module/link/css-classes-setting.js
  var import_element3 = __toESM(require_element());
  var import_compose = __toESM(require_compose());
  var import_i18n5 = __toESM(require_i18n());
  var import_components2 = __toESM(require_components());
  var import_jsx_runtime18 = __toESM(require_jsx_runtime());
  var CSSClassesSettingComponent = ({ setting, value, onChange }) => {
    const hasValue = value ? value?.cssClasses?.length > 0 : false;
    const [isSettingActive, setIsSettingActive] = (0, import_element3.useState)(hasValue);
    const instanceId = (0, import_compose.useInstanceId)(CSSClassesSettingComponent);
    const controlledRegionId = `css-classes-setting-${instanceId}`;
    const handleSettingChange = (newValue) => {
      const sanitizedValue = typeof newValue === "string" ? newValue.replace(/,/g, " ").replace(/\s+/g, " ").trim() : newValue;
      onChange({
        ...value,
        [setting.id]: sanitizedValue
      });
    };
    const handleCheckboxChange = () => {
      if (isSettingActive) {
        if (hasValue) {
          handleSettingChange("");
        }
        setIsSettingActive(false);
      } else {
        setIsSettingActive(true);
      }
    };
    return /* @__PURE__ */ (0, import_jsx_runtime18.jsxs)("fieldset", { children: [
      /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(import_components2.VisuallyHidden, { as: "legend", children: setting.title }),
      /* @__PURE__ */ (0, import_jsx_runtime18.jsxs)(import_components2.__experimentalVStack, { spacing: 3, children: [
        /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
          import_components2.CheckboxControl,
          {
            __nextHasNoMarginBottom: true,
            label: setting.title,
            onChange: handleCheckboxChange,
            checked: isSettingActive || hasValue,
            "aria-expanded": isSettingActive,
            "aria-controls": isSettingActive ? controlledRegionId : void 0
          }
        ),
        isSettingActive && /* @__PURE__ */ (0, import_jsx_runtime18.jsx)("div", { id: controlledRegionId, children: /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(
          import_components2.__experimentalInputControl,
          {
            label: (0, import_i18n5.__)("CSS classes"),
            value: value?.cssClasses,
            onChange: handleSettingChange,
            help: (0, import_i18n5.__)(
              "Separate multiple classes with spaces."
            ),
            __unstableInputWidth: "100%",
            __next40pxDefaultSize: true
          }
        ) })
      ] })
    ] });
  };
  var css_classes_setting_default = CSSClassesSettingComponent;

  // packages/format-library/build-module/link/inline.js
  var import_jsx_runtime19 = __toESM(require_jsx_runtime());
  var LINK_SETTINGS = [
    ...import_block_editor5.LinkControl.DEFAULT_LINK_SETTINGS,
    {
      id: "nofollow",
      title: (0, import_i18n6.__)("Mark as nofollow")
    },
    {
      id: "cssClasses",
      title: (0, import_i18n6.__)("Additional CSS class(es)"),
      render: (setting, value, onChange) => {
        return /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
          css_classes_setting_default,
          {
            setting,
            value,
            onChange
          }
        );
      }
    }
  ];
  function InlineLinkUI({
    isActive,
    activeAttributes,
    value,
    onChange,
    onFocusOutside,
    stopAddingLink,
    contentRef,
    focusOnMount
  }) {
    const richLinkTextValue = getRichTextValueFromSelection(value, isActive);
    const richTextText = richLinkTextValue.text;
    const { selectionChange } = (0, import_data.useDispatch)(import_block_editor5.store);
    const { createPageEntity, userCanCreatePages, selectionStart } = (0, import_data.useSelect)(
      (select) => {
        const { getSettings, getSelectionStart } = select(import_block_editor5.store);
        const _settings = getSettings();
        return {
          createPageEntity: _settings.__experimentalCreatePageEntity,
          userCanCreatePages: _settings.__experimentalUserCanCreatePages,
          selectionStart: getSelectionStart()
        };
      },
      []
    );
    const linkValue = (0, import_element4.useMemo)(
      () => ({
        url: activeAttributes.url,
        type: activeAttributes.type,
        id: activeAttributes.id,
        opensInNewTab: activeAttributes.target === "_blank",
        nofollow: activeAttributes.rel?.includes("nofollow"),
        title: richTextText,
        cssClasses: activeAttributes.class
      }),
      [
        activeAttributes.class,
        activeAttributes.id,
        activeAttributes.rel,
        activeAttributes.target,
        activeAttributes.type,
        activeAttributes.url,
        richTextText
      ]
    );
    function removeLink() {
      const newValue = (0, import_rich_text5.removeFormat)(value, "core/link");
      onChange(newValue);
      stopAddingLink();
      (0, import_a11y.speak)((0, import_i18n6.__)("Link removed."), "assertive");
    }
    function onChangeLink(nextValue) {
      const hasLink = linkValue?.url;
      const isNewLink = !hasLink;
      nextValue = {
        ...linkValue,
        ...nextValue
      };
      const newUrl = (0, import_url2.prependHTTP)(nextValue.url);
      const linkFormat = createLinkFormat({
        url: newUrl,
        type: nextValue.type,
        id: nextValue.id !== void 0 && nextValue.id !== null ? String(nextValue.id) : void 0,
        opensInNewWindow: nextValue.opensInNewTab,
        nofollow: nextValue.nofollow,
        cssClasses: nextValue.cssClasses
      });
      const newText = nextValue.title || newUrl;
      let newValue;
      if ((0, import_rich_text5.isCollapsed)(value) && !isActive) {
        const inserted = (0, import_rich_text5.insert)(value, newText);
        newValue = (0, import_rich_text5.applyFormat)(
          inserted,
          linkFormat,
          value.start,
          value.start + newText.length
        );
        onChange(newValue);
        stopAddingLink();
        selectionChange({
          clientId: selectionStart.clientId,
          identifier: selectionStart.attributeKey,
          start: value.start + newText.length + 1
        });
        return;
      } else if (newText === richTextText) {
        newValue = (0, import_rich_text5.applyFormat)(value, linkFormat);
      } else {
        newValue = (0, import_rich_text5.create)({ text: newText });
        newValue = (0, import_rich_text5.applyFormat)(newValue, linkFormat, 0, newText.length);
        const boundary = getFormatBoundary(value, {
          type: "core/link"
        });
        const [valBefore, valAfter] = (0, import_rich_text5.split)(
          value,
          boundary.start,
          boundary.start
        );
        const newValAfter = (0, import_rich_text5.replace)(valAfter, richTextText, newValue);
        newValue = (0, import_rich_text5.concat)(valBefore, newValAfter);
      }
      onChange(newValue);
      if (!isNewLink) {
        stopAddingLink();
      }
      if (!isValidHref(newUrl)) {
        (0, import_a11y.speak)(
          (0, import_i18n6.__)(
            "Warning: the link has been inserted but may have errors. Please test it."
          ),
          "assertive"
        );
      } else if (isActive) {
        (0, import_a11y.speak)((0, import_i18n6.__)("Link edited."), "assertive");
      } else {
        (0, import_a11y.speak)((0, import_i18n6.__)("Link inserted."), "assertive");
      }
    }
    const popoverAnchor = (0, import_rich_text5.useAnchor)({
      editableContentElement: contentRef.current,
      settings: {
        ...link,
        isActive
      }
    });
    async function handleCreate(pageTitle) {
      const page = await createPageEntity({
        title: pageTitle,
        status: "draft"
      });
      return {
        id: page.id,
        type: page.type,
        title: page.title.rendered,
        url: page.link,
        kind: "post-type"
      };
    }
    function createButtonText(searchTerm) {
      return (0, import_element4.createInterpolateElement)(
        (0, import_i18n6.sprintf)(
          /* translators: %s: search term. */
          (0, import_i18n6.__)("Create page: <mark>%s</mark>"),
          searchTerm
        ),
        { mark: /* @__PURE__ */ (0, import_jsx_runtime19.jsx)("mark", {}) }
      );
    }
    return /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
      import_components3.Popover,
      {
        anchor: popoverAnchor,
        animate: false,
        onClose: stopAddingLink,
        onFocusOutside,
        placement: "bottom",
        offset: 8,
        shift: true,
        focusOnMount,
        constrainTabbing: true,
        children: /* @__PURE__ */ (0, import_jsx_runtime19.jsx)(
          import_block_editor5.LinkControl,
          {
            value: linkValue,
            onChange: onChangeLink,
            onRemove: removeLink,
            hasRichPreviews: true,
            createSuggestion: createPageEntity && handleCreate,
            withCreateSuggestion: userCanCreatePages,
            createSuggestionButtonText: createButtonText,
            hasTextControl: true,
            settings: LINK_SETTINGS,
            showInitialSuggestions: true,
            suggestionsQuery: {
              // always show Pages as initial suggestions
              initialSuggestionsSearchOptions: {
                type: "post",
                subtype: "page",
                perPage: 20
              }
            }
          }
        )
      }
    );
  }
  function getRichTextValueFromSelection(value, isActive) {
    let textStart = value.start;
    let textEnd = value.end;
    if (isActive) {
      const boundary = getFormatBoundary(value, {
        type: "core/link"
      });
      textStart = boundary.start;
      textEnd = boundary.end + 1;
    }
    return (0, import_rich_text5.slice)(value, textStart, textEnd);
  }
  var inline_default = InlineLinkUI;

  // packages/format-library/build-module/link/index.js
  var import_jsx_runtime20 = __toESM(require_jsx_runtime());
  var { essentialFormatKey: essentialFormatKey3 } = unlock(import_block_editor6.privateApis);
  var name5 = "core/link";
  var title5 = (0, import_i18n7.__)("Link");
  function Edit2({
    isActive,
    activeAttributes,
    value,
    onChange,
    onFocus,
    contentRef
  }) {
    const [addingLink, setAddingLink] = (0, import_element5.useState)(false);
    const [openedBy, setOpenedBy] = (0, import_element5.useState)(null);
    (0, import_element5.useEffect)(() => {
      if (!isActive) {
        setAddingLink(false);
      }
    }, [isActive]);
    (0, import_element5.useLayoutEffect)(() => {
      const editableContentElement = contentRef.current;
      if (!editableContentElement) {
        return;
      }
      function handleClick(event) {
        const link2 = event.target.closest("[contenteditable] a");
        if (!link2 || // other formats (e.g. bold) may be nested within the link.
        !isActive) {
          return;
        }
        setAddingLink(true);
        setOpenedBy({
          el: link2,
          action: "click"
        });
      }
      editableContentElement.addEventListener("click", handleClick);
      return () => {
        editableContentElement.removeEventListener("click", handleClick);
      };
    }, [contentRef, isActive]);
    function addLink(target) {
      const text = (0, import_rich_text6.getTextContent)((0, import_rich_text6.slice)(value));
      if (!isActive && text && (0, import_url3.isURL)(text) && isValidHref(text)) {
        onChange(
          (0, import_rich_text6.applyFormat)(value, {
            type: name5,
            attributes: { url: text }
          })
        );
      } else if (!isActive && text && (0, import_url3.isEmail)(text)) {
        onChange(
          (0, import_rich_text6.applyFormat)(value, {
            type: name5,
            attributes: { url: `mailto:${text}` }
          })
        );
      } else if (!isActive && text && (0, import_url3.isPhoneNumber)(text)) {
        onChange(
          (0, import_rich_text6.applyFormat)(value, {
            type: name5,
            attributes: { url: `tel:${text.replace(/\D/g, "")}` }
          })
        );
      } else {
        if (target) {
          setOpenedBy({
            el: target,
            action: null
            // We don't need to distinguish between click or keyboard here
          });
        }
        setAddingLink(true);
      }
    }
    function stopAddingLink() {
      setAddingLink(false);
      if (openedBy?.el?.tagName === "BUTTON") {
        openedBy.el.focus();
      } else {
        onFocus();
      }
      setOpenedBy(null);
    }
    function onFocusOutside() {
      setAddingLink(false);
      setOpenedBy(null);
    }
    function onRemoveFormat() {
      onChange((0, import_rich_text6.removeFormat)(value, name5));
      (0, import_a11y2.speak)((0, import_i18n7.__)("Link removed."), "assertive");
    }
    const shouldAutoFocus = !(openedBy?.el?.tagName === "A" && openedBy?.action === "click");
    const hasSelection = !(0, import_rich_text6.isCollapsed)(value);
    return /* @__PURE__ */ (0, import_jsx_runtime20.jsxs)(import_jsx_runtime20.Fragment, { children: [
      hasSelection && /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
        import_block_editor6.RichTextShortcut,
        {
          type: "primary",
          character: "k",
          onUse: addLink
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
        import_block_editor6.RichTextShortcut,
        {
          type: "primaryShift",
          character: "k",
          onUse: onRemoveFormat
        }
      ),
      /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
        import_block_editor6.RichTextToolbarButton,
        {
          name: "link",
          icon: link_default,
          title: isActive ? (0, import_i18n7.__)("Link") : title5,
          onClick: (event) => {
            addLink(event.currentTarget);
          },
          isActive: isActive || addingLink,
          shortcutType: "primary",
          shortcutCharacter: "k",
          "aria-haspopup": "true",
          "aria-expanded": addingLink
        }
      ),
      addingLink && /* @__PURE__ */ (0, import_jsx_runtime20.jsx)(
        inline_default,
        {
          stopAddingLink,
          onFocusOutside,
          isActive,
          activeAttributes,
          value,
          onChange,
          contentRef,
          focusOnMount: shouldAutoFocus ? "firstElement" : false
        }
      )
    ] });
  }
  var link = {
    name: name5,
    title: title5,
    tagName: "a",
    className: null,
    attributes: {
      url: "href",
      type: "data-type",
      id: "data-id",
      _id: "id",
      target: "target",
      rel: "rel",
      class: "class"
    },
    [essentialFormatKey3]: true,
    __unstablePasteRule(value, { html, plainText }) {
      const pastedText = (html || plainText).replace(/<[^>]+>/g, "").trim();
      if (!(0, import_url3.isURL)(pastedText) || !/^https?:/.test(pastedText)) {
        return value;
      }
      window.console.log("Created link:\n\n", pastedText);
      const format = {
        type: name5,
        attributes: {
          url: (0, import_html_entities.decodeEntities)(pastedText)
        }
      };
      if ((0, import_rich_text6.isCollapsed)(value)) {
        return (0, import_rich_text6.insert)(
          value,
          (0, import_rich_text6.applyFormat)(
            (0, import_rich_text6.create)({ text: plainText }),
            format,
            0,
            plainText.length
          )
        );
      }
      return (0, import_rich_text6.applyFormat)(value, format);
    },
    edit: Edit2
  };

  // packages/format-library/build-module/strikethrough/index.js
  var import_i18n8 = __toESM(require_i18n());
  var import_rich_text7 = __toESM(require_rich_text());
  var import_block_editor7 = __toESM(require_block_editor());
  var import_jsx_runtime21 = __toESM(require_jsx_runtime());
  var name6 = "core/strikethrough";
  var title6 = (0, import_i18n8.__)("Strikethrough");
  var strikethrough = {
    name: name6,
    title: title6,
    tagName: "s",
    className: null,
    edit({ isActive, value, onChange, onFocus }) {
      function onClick() {
        onChange((0, import_rich_text7.toggleFormat)(value, { type: name6, title: title6 }));
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime21.jsxs)(import_jsx_runtime21.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
          import_block_editor7.RichTextShortcut,
          {
            type: "access",
            character: "d",
            onUse: onClick
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime21.jsx)(
          import_block_editor7.RichTextToolbarButton,
          {
            icon: format_strikethrough_default,
            title: title6,
            onClick,
            isActive,
            role: "menuitemcheckbox"
          }
        )
      ] });
    }
  };

  // packages/format-library/build-module/underline/index.js
  var import_i18n9 = __toESM(require_i18n());
  var import_rich_text8 = __toESM(require_rich_text());
  var import_block_editor8 = __toESM(require_block_editor());
  var import_jsx_runtime22 = __toESM(require_jsx_runtime());
  var name7 = "core/underline";
  var title7 = (0, import_i18n9.__)("Underline");
  var underline = {
    name: name7,
    title: title7,
    tagName: "span",
    className: null,
    attributes: {
      style: "style"
    },
    edit({ value, onChange }) {
      const onToggle = () => {
        onChange(
          (0, import_rich_text8.toggleFormat)(value, {
            type: name7,
            attributes: {
              style: "text-decoration: underline;"
            },
            title: title7
          })
        );
      };
      return /* @__PURE__ */ (0, import_jsx_runtime22.jsxs)(import_jsx_runtime22.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
          import_block_editor8.RichTextShortcut,
          {
            type: "primary",
            character: "u",
            onUse: onToggle
          }
        ),
        /* @__PURE__ */ (0, import_jsx_runtime22.jsx)(
          import_block_editor8.__unstableRichTextInputEvent,
          {
            inputType: "formatUnderline",
            onInput: onToggle
          }
        )
      ] });
    }
  };

  // packages/format-library/build-module/text-color/index.js
  var import_i18n11 = __toESM(require_i18n());
  var import_element7 = __toESM(require_element());
  var import_block_editor10 = __toESM(require_block_editor());
  var import_rich_text10 = __toESM(require_rich_text());

  // packages/format-library/build-module/text-color/inline.js
  var import_element6 = __toESM(require_element());
  var import_data2 = __toESM(require_data());
  var import_rich_text9 = __toESM(require_rich_text());
  var import_block_editor9 = __toESM(require_block_editor());
  var import_components4 = __toESM(require_components());
  var import_i18n10 = __toESM(require_i18n());
  var import_jsx_runtime23 = __toESM(require_jsx_runtime());
  var { Tabs } = unlock(import_components4.privateApis);
  var TABS = [
    { name: "color", title: (0, import_i18n10.__)("Text") },
    { name: "backgroundColor", title: (0, import_i18n10.__)("Background") }
  ];
  function parseCSS(css = "") {
    return css.split(";").reduce((accumulator, rule) => {
      if (rule) {
        const [property, value] = rule.split(":");
        if (property === "color") {
          accumulator.color = value;
        }
        if (property === "background-color" && value !== transparentValue) {
          accumulator.backgroundColor = value;
        }
      }
      return accumulator;
    }, {});
  }
  function parseClassName(className = "", colorSettings) {
    return className.split(" ").reduce((accumulator, name16) => {
      if (name16.startsWith("has-") && name16.endsWith("-color")) {
        const colorSlug = name16.replace(/^has-/, "").replace(/-color$/, "");
        const colorObject = (0, import_block_editor9.getColorObjectByAttributeValues)(
          colorSettings,
          colorSlug
        );
        accumulator.color = colorObject.color;
      }
      return accumulator;
    }, {});
  }
  function getActiveColors(value, name16, colorSettings) {
    const activeColorFormat = (0, import_rich_text9.getActiveFormat)(value, name16);
    if (!activeColorFormat) {
      return {};
    }
    return {
      ...parseCSS(activeColorFormat.attributes.style),
      ...parseClassName(activeColorFormat.attributes.class, colorSettings)
    };
  }
  function setColors(value, name16, colorSettings, colors) {
    const { color, backgroundColor } = {
      ...getActiveColors(value, name16, colorSettings),
      ...colors
    };
    if (!color && !backgroundColor) {
      return (0, import_rich_text9.removeFormat)(value, name16);
    }
    const styles = [];
    const classNames = [];
    const attributes = {};
    if (backgroundColor) {
      styles.push(["background-color", backgroundColor].join(":"));
    } else {
      styles.push(["background-color", transparentValue].join(":"));
    }
    if (color) {
      const colorObject = (0, import_block_editor9.getColorObjectByColorValue)(colorSettings, color);
      if (colorObject) {
        classNames.push((0, import_block_editor9.getColorClassName)("color", colorObject.slug));
      } else {
        styles.push(["color", color].join(":"));
      }
    }
    if (styles.length) {
      attributes.style = styles.join(";");
    }
    if (classNames.length) {
      attributes.class = classNames.join(" ");
    }
    return (0, import_rich_text9.applyFormat)(value, { type: name16, attributes });
  }
  function ColorPicker({ name: name16, property, value, onChange }) {
    const colors = (0, import_data2.useSelect)((select) => {
      const { getSettings } = select(import_block_editor9.store);
      return getSettings().colors ?? [];
    }, []);
    const activeColors = (0, import_element6.useMemo)(
      () => getActiveColors(value, name16, colors),
      [name16, value, colors]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
      import_block_editor9.ColorPalette,
      {
        value: activeColors[property],
        onChange: (color) => {
          onChange(
            setColors(value, name16, colors, { [property]: color })
          );
        },
        enableAlpha: true,
        __experimentalIsRenderedInSidebar: true
      }
    );
  }
  function InlineColorUI({
    name: name16,
    value,
    onChange,
    onClose,
    contentRef,
    isActive
  }) {
    const popoverAnchor = (0, import_rich_text9.useAnchor)({
      editableContentElement: contentRef.current,
      settings: { ...textColor, isActive }
    });
    return /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
      import_components4.Popover,
      {
        onClose,
        className: "format-library__inline-color-popover",
        anchor: popoverAnchor,
        children: /* @__PURE__ */ (0, import_jsx_runtime23.jsxs)(Tabs, { children: [
          /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(Tabs.TabList, { children: TABS.map((tab) => /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(Tabs.Tab, { tabId: tab.name, children: tab.title }, tab.name)) }),
          TABS.map((tab) => /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
            Tabs.TabPanel,
            {
              tabId: tab.name,
              focusable: false,
              children: /* @__PURE__ */ (0, import_jsx_runtime23.jsx)(
                ColorPicker,
                {
                  name: name16,
                  property: tab.name,
                  value,
                  onChange
                }
              )
            },
            tab.name
          ))
        ] })
      }
    );
  }

  // packages/format-library/build-module/text-color/index.js
  var import_jsx_runtime24 = __toESM(require_jsx_runtime());
  var transparentValue = "rgba(0, 0, 0, 0)";
  var name8 = "core/text-color";
  var title8 = (0, import_i18n11.__)("Highlight");
  var EMPTY_ARRAY = [];
  function getComputedStyleProperty(element, property) {
    const { ownerDocument } = element;
    const { defaultView } = ownerDocument;
    const style = defaultView.getComputedStyle(element);
    const value = style.getPropertyValue(property);
    if (property === "background-color" && value === transparentValue && element.parentElement) {
      return getComputedStyleProperty(element.parentElement, property);
    }
    return value;
  }
  function fillComputedColors(element, { color, backgroundColor }) {
    if (!color && !backgroundColor) {
      return;
    }
    return {
      color: color || getComputedStyleProperty(element, "color"),
      backgroundColor: backgroundColor === transparentValue ? getComputedStyleProperty(element, "background-color") : backgroundColor
    };
  }
  function TextColorEdit({
    value,
    onChange,
    isActive,
    activeAttributes,
    contentRef
  }) {
    const [allowCustomControl, colors = EMPTY_ARRAY] = (0, import_block_editor10.useSettings)(
      "color.custom",
      "color.palette"
    );
    const [isAddingColor, setIsAddingColor] = (0, import_element7.useState)(false);
    const colorIndicatorStyle = (0, import_element7.useMemo)(
      () => fillComputedColors(
        contentRef.current,
        getActiveColors(value, name8, colors)
      ),
      [contentRef, value, colors]
    );
    const hasColorsToChoose = !!colors.length || allowCustomControl;
    if (!hasColorsToChoose && !isActive) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime24.jsxs)(import_jsx_runtime24.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
        import_block_editor10.RichTextToolbarButton,
        {
          className: "format-library-text-color-button",
          isActive,
          icon: /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
            icon_default,
            {
              icon: Object.keys(activeAttributes).length ? text_color_default : color_default,
              style: colorIndicatorStyle
            }
          ),
          title: title8,
          onClick: hasColorsToChoose ? () => setIsAddingColor(true) : () => onChange((0, import_rich_text10.removeFormat)(value, name8)),
          role: "menuitemcheckbox"
        }
      ),
      isAddingColor && /* @__PURE__ */ (0, import_jsx_runtime24.jsx)(
        InlineColorUI,
        {
          name: name8,
          onClose: () => setIsAddingColor(false),
          activeAttributes,
          value,
          onChange,
          contentRef,
          isActive
        }
      )
    ] });
  }
  var textColor = {
    name: name8,
    title: title8,
    tagName: "mark",
    className: "has-inline-color",
    attributes: {
      style: "style",
      class: "class"
    },
    edit: TextColorEdit
  };

  // packages/format-library/build-module/subscript/index.js
  var import_i18n12 = __toESM(require_i18n());
  var import_rich_text11 = __toESM(require_rich_text());
  var import_block_editor11 = __toESM(require_block_editor());
  var import_jsx_runtime25 = __toESM(require_jsx_runtime());
  var name9 = "core/subscript";
  var title9 = (0, import_i18n12.__)("Subscript");
  var subscript = {
    name: name9,
    title: title9,
    tagName: "sub",
    className: null,
    edit({ isActive, value, onChange, onFocus }) {
      function onToggle() {
        onChange((0, import_rich_text11.toggleFormat)(value, { type: name9, title: title9 }));
      }
      function onClick() {
        onToggle();
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime25.jsx)(
        import_block_editor11.RichTextToolbarButton,
        {
          icon: subscript_default,
          title: title9,
          onClick,
          isActive,
          role: "menuitemcheckbox"
        }
      );
    }
  };

  // packages/format-library/build-module/superscript/index.js
  var import_i18n13 = __toESM(require_i18n());
  var import_rich_text12 = __toESM(require_rich_text());
  var import_block_editor12 = __toESM(require_block_editor());
  var import_jsx_runtime26 = __toESM(require_jsx_runtime());
  var name10 = "core/superscript";
  var title10 = (0, import_i18n13.__)("Superscript");
  var superscript = {
    name: name10,
    title: title10,
    tagName: "sup",
    className: null,
    edit({ isActive, value, onChange, onFocus }) {
      function onToggle() {
        onChange((0, import_rich_text12.toggleFormat)(value, { type: name10, title: title10 }));
      }
      function onClick() {
        onToggle();
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime26.jsx)(
        import_block_editor12.RichTextToolbarButton,
        {
          icon: superscript_default,
          title: title10,
          onClick,
          isActive,
          role: "menuitemcheckbox"
        }
      );
    }
  };

  // packages/format-library/build-module/keyboard/index.js
  var import_i18n14 = __toESM(require_i18n());
  var import_rich_text13 = __toESM(require_rich_text());
  var import_block_editor13 = __toESM(require_block_editor());
  var import_jsx_runtime27 = __toESM(require_jsx_runtime());
  var name11 = "core/keyboard";
  var title11 = (0, import_i18n14.__)("Keyboard input");
  var keyboard = {
    name: name11,
    title: title11,
    tagName: "kbd",
    className: null,
    edit({ isActive, value, onChange, onFocus }) {
      function onToggle() {
        onChange((0, import_rich_text13.toggleFormat)(value, { type: name11, title: title11 }));
      }
      function onClick() {
        onToggle();
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime27.jsx)(
        import_block_editor13.RichTextToolbarButton,
        {
          icon: button_default,
          title: title11,
          onClick,
          isActive,
          role: "menuitemcheckbox"
        }
      );
    }
  };

  // packages/format-library/build-module/unknown/index.js
  var import_i18n15 = __toESM(require_i18n());
  var import_rich_text14 = __toESM(require_rich_text());
  var import_block_editor14 = __toESM(require_block_editor());
  var import_jsx_runtime28 = __toESM(require_jsx_runtime());
  var name12 = "core/unknown";
  var title12 = (0, import_i18n15.__)("Clear Unknown Formatting");
  function selectionContainsUnknownFormats(value) {
    if ((0, import_rich_text14.isCollapsed)(value)) {
      return false;
    }
    const selectedValue = (0, import_rich_text14.slice)(value);
    return selectedValue.formats.some((formats) => {
      return formats.some((format) => format.type === name12);
    });
  }
  var unknown = {
    name: name12,
    title: title12,
    tagName: "*",
    className: null,
    edit({ isActive, value, onChange, onFocus }) {
      if (!isActive && !selectionContainsUnknownFormats(value)) {
        return null;
      }
      function onClick() {
        onChange((0, import_rich_text14.removeFormat)(value, name12));
        onFocus();
      }
      return /* @__PURE__ */ (0, import_jsx_runtime28.jsx)(
        import_block_editor14.RichTextToolbarButton,
        {
          name: "unknown",
          icon: help_default,
          title: title12,
          onClick,
          isActive: true
        }
      );
    }
  };

  // packages/format-library/build-module/language/index.js
  var import_i18n16 = __toESM(require_i18n());
  var import_block_editor15 = __toESM(require_block_editor());
  var import_components5 = __toESM(require_components());
  var import_element8 = __toESM(require_element());
  var import_rich_text15 = __toESM(require_rich_text());
  var import_jsx_runtime29 = __toESM(require_jsx_runtime());
  var name13 = "core/language";
  var title13 = (0, import_i18n16.__)("Language");
  var language = {
    name: name13,
    tagName: "bdo",
    className: null,
    edit: Edit3,
    title: title13
  };
  function Edit3({ isActive, value, onChange, contentRef }) {
    const [isPopoverVisible, setIsPopoverVisible] = (0, import_element8.useState)(false);
    const togglePopover = () => {
      setIsPopoverVisible((state) => !state);
    };
    return /* @__PURE__ */ (0, import_jsx_runtime29.jsxs)(import_jsx_runtime29.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
        import_block_editor15.RichTextToolbarButton,
        {
          icon: language_default,
          label: title13,
          title: title13,
          onClick: () => {
            if (isActive) {
              onChange((0, import_rich_text15.removeFormat)(value, name13));
            } else {
              togglePopover();
            }
          },
          isActive,
          role: "menuitemcheckbox"
        }
      ),
      isPopoverVisible && /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
        InlineLanguageUI,
        {
          value,
          onChange,
          onClose: togglePopover,
          contentRef
        }
      )
    ] });
  }
  function InlineLanguageUI({ value, contentRef, onChange, onClose }) {
    const popoverAnchor = (0, import_rich_text15.useAnchor)({
      editableContentElement: contentRef.current,
      settings: language
    });
    const [lang, setLang] = (0, import_element8.useState)("");
    const [dir, setDir] = (0, import_element8.useState)("ltr");
    return /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
      import_components5.Popover,
      {
        className: "block-editor-format-toolbar__language-popover",
        anchor: popoverAnchor,
        onClose,
        children: /* @__PURE__ */ (0, import_jsx_runtime29.jsxs)(
          import_components5.__experimentalVStack,
          {
            as: "form",
            spacing: 4,
            className: "block-editor-format-toolbar__language-container-content",
            onSubmit: (event) => {
              event.preventDefault();
              onChange(
                (0, import_rich_text15.applyFormat)(value, {
                  type: name13,
                  attributes: {
                    lang,
                    dir
                  }
                })
              );
              onClose();
            },
            children: [
              /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
                import_components5.TextControl,
                {
                  __next40pxDefaultSize: true,
                  __nextHasNoMarginBottom: true,
                  label: title13,
                  value: lang,
                  onChange: (val) => setLang(val),
                  help: (0, import_i18n16.__)(
                    'A valid language attribute, like "en" or "fr".'
                  )
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
                import_components5.SelectControl,
                {
                  __next40pxDefaultSize: true,
                  __nextHasNoMarginBottom: true,
                  label: (0, import_i18n16.__)("Text direction"),
                  value: dir,
                  options: [
                    {
                      label: (0, import_i18n16.__)("Left to right"),
                      value: "ltr"
                    },
                    {
                      label: (0, import_i18n16.__)("Right to left"),
                      value: "rtl"
                    }
                  ],
                  onChange: (val) => setDir(val)
                }
              ),
              /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(import_components5.__experimentalHStack, { alignment: "right", children: /* @__PURE__ */ (0, import_jsx_runtime29.jsx)(
                import_components5.Button,
                {
                  __next40pxDefaultSize: true,
                  variant: "primary",
                  type: "submit",
                  text: (0, import_i18n16.__)("Apply")
                }
              ) })
            ]
          }
        )
      }
    );
  }

  // packages/format-library/build-module/math/index.js
  var import_i18n17 = __toESM(require_i18n());
  var import_element9 = __toESM(require_element());
  var import_rich_text16 = __toESM(require_rich_text());
  var import_block_editor16 = __toESM(require_block_editor());
  var import_components6 = __toESM(require_components());
  var import_jsx_runtime30 = __toESM(require_jsx_runtime());
  var { Badge } = unlock(import_components6.privateApis);
  var name14 = "core/math";
  var title14 = (0, import_i18n17.__)("Math");
  function InlineUI2({
    value,
    onChange,
    activeAttributes,
    contentRef,
    latexToMathML
  }) {
    const [latex, setLatex] = (0, import_element9.useState)(
      activeAttributes?.["data-latex"] || ""
    );
    const [error, setError] = (0, import_element9.useState)(null);
    const popoverAnchor = (0, import_rich_text16.useAnchor)({
      editableContentElement: contentRef.current,
      settings: math
    });
    const handleLatexChange = (newLatex) => {
      let mathML = "";
      setLatex(newLatex);
      if (newLatex) {
        try {
          mathML = latexToMathML(newLatex, { displayMode: false });
          setError(null);
        } catch (err) {
          setError(err.message);
          return;
        }
      }
      const newReplacements = value.replacements.slice();
      newReplacements[value.start] = {
        type: name14,
        attributes: {
          "data-latex": newLatex
        },
        innerHTML: mathML
      };
      onChange({
        ...value,
        replacements: newReplacements
      });
    };
    return /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
      import_components6.Popover,
      {
        placement: "bottom-start",
        offset: 8,
        focusOnMount: false,
        anchor: popoverAnchor,
        className: "block-editor-format-toolbar__math-popover",
        children: /* @__PURE__ */ (0, import_jsx_runtime30.jsx)("div", { style: { minWidth: "300px", padding: "4px" }, children: /* @__PURE__ */ (0, import_jsx_runtime30.jsxs)(import_components6.__experimentalVStack, { spacing: 1, children: [
          /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
            import_components6.TextControl,
            {
              __nextHasNoMarginBottom: true,
              __next40pxDefaultSize: true,
              hideLabelFromVision: true,
              label: (0, import_i18n17.__)("LaTeX math syntax"),
              value: latex,
              onChange: handleLatexChange,
              placeholder: (0, import_i18n17.__)("e.g., x^2, \\frac{a}{b}"),
              autoComplete: "off",
              className: "block-editor-format-toolbar__math-input"
            }
          ),
          error && /* @__PURE__ */ (0, import_jsx_runtime30.jsxs)(import_jsx_runtime30.Fragment, { children: [
            /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
              Badge,
              {
                intent: "error",
                className: "wp-block-math__error",
                children: error
              }
            ),
            /* @__PURE__ */ (0, import_jsx_runtime30.jsx)("style", { children: ".wp-block-math__error .components-badge__content{white-space:normal}" })
          ] })
        ] }) })
      }
    );
  }
  function Edit4({
    value,
    onChange,
    onFocus,
    isObjectActive,
    activeObjectAttributes,
    contentRef
  }) {
    const [latexToMathML, setLatexToMathML] = (0, import_element9.useState)();
    (0, import_element9.useEffect)(() => {
      import("@wordpress/latex-to-mathml").then((module) => {
        setLatexToMathML(() => module.default);
      });
    }, []);
    return /* @__PURE__ */ (0, import_jsx_runtime30.jsxs)(import_jsx_runtime30.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
        import_block_editor16.RichTextToolbarButton,
        {
          icon: math_default,
          title: title14,
          onClick: () => {
            const newValue = (0, import_rich_text16.insertObject)(value, {
              type: name14,
              attributes: {
                "data-latex": ""
              },
              innerHTML: ""
            });
            newValue.start = newValue.end - 1;
            onChange(newValue);
            onFocus();
          },
          isActive: isObjectActive
        }
      ),
      isObjectActive && /* @__PURE__ */ (0, import_jsx_runtime30.jsx)(
        InlineUI2,
        {
          value,
          onChange,
          activeAttributes: activeObjectAttributes,
          contentRef,
          latexToMathML
        }
      )
    ] });
  }
  var math = {
    name: name14,
    title: title14,
    tagName: "math",
    className: null,
    attributes: {
      "data-latex": "data-latex"
    },
    contentEditable: false,
    edit: Edit4
  };

  // packages/format-library/build-module/non-breaking-space/index.js
  var import_i18n18 = __toESM(require_i18n());
  var import_rich_text17 = __toESM(require_rich_text());
  var import_block_editor17 = __toESM(require_block_editor());
  var import_jsx_runtime31 = __toESM(require_jsx_runtime());
  var name15 = "core/non-breaking-space";
  var title15 = (0, import_i18n18.__)("Non breaking space");
  var nonBreakingSpace = {
    name: name15,
    title: title15,
    tagName: "nbsp",
    className: null,
    edit({ value, onChange }) {
      function addNonBreakingSpace() {
        onChange((0, import_rich_text17.insert)(value, "\xA0"));
      }
      return /* @__PURE__ */ (0, import_jsx_runtime31.jsx)(
        import_block_editor17.RichTextShortcut,
        {
          type: "primaryShift",
          character: " ",
          onUse: addNonBreakingSpace
        }
      );
    }
  };

  // packages/format-library/build-module/default-formats.js
  var default_formats_default = [
    bold,
    code,
    image,
    italic,
    link,
    strikethrough,
    underline,
    textColor,
    subscript,
    superscript,
    keyboard,
    unknown,
    language,
    math,
    nonBreakingSpace
  ];

  // packages/format-library/build-module/index.js
  default_formats_default.forEach(
    ({ name: name16, ...settings }) => (0, import_rich_text18.registerFormatType)(name16, settings)
  );
})();
//# sourceMappingURL=index.js.map
