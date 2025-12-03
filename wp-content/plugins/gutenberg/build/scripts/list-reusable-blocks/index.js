var wp;
(wp ||= {}).listReusableBlocks = (() => {
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

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/api-fetch
  var require_api_fetch = __commonJS({
    "package-external:@wordpress/api-fetch"(exports, module) {
      module.exports = window.wp.apiFetch;
    }
  });

  // package-external:@wordpress/blob
  var require_blob = __commonJS({
    "package-external:@wordpress/blob"(exports, module) {
      module.exports = window.wp.blob;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
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

  // packages/list-reusable-blocks/build-module/index.js
  var import_element2 = __toESM(require_element());
  var import_i18n3 = __toESM(require_i18n());

  // node_modules/tslib/tslib.es6.mjs
  var __assign = function() {
    __assign = Object.assign || function __assign2(t) {
      for (var s, i = 1, n = arguments.length; i < n; i++) {
        s = arguments[i];
        for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
      }
      return t;
    };
    return __assign.apply(this, arguments);
  };

  // node_modules/lower-case/dist.es2015/index.js
  function lowerCase(str) {
    return str.toLowerCase();
  }

  // node_modules/no-case/dist.es2015/index.js
  var DEFAULT_SPLIT_REGEXP = [/([a-z0-9])([A-Z])/g, /([A-Z])([A-Z][a-z])/g];
  var DEFAULT_STRIP_REGEXP = /[^A-Z0-9]+/gi;
  function noCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    var _a = options.splitRegexp, splitRegexp = _a === void 0 ? DEFAULT_SPLIT_REGEXP : _a, _b = options.stripRegexp, stripRegexp = _b === void 0 ? DEFAULT_STRIP_REGEXP : _b, _c = options.transform, transform = _c === void 0 ? lowerCase : _c, _d = options.delimiter, delimiter = _d === void 0 ? " " : _d;
    var result = replace(replace(input, splitRegexp, "$1\0$2"), stripRegexp, "\0");
    var start = 0;
    var end = result.length;
    while (result.charAt(start) === "\0")
      start++;
    while (result.charAt(end - 1) === "\0")
      end--;
    return result.slice(start, end).split("\0").map(transform).join(delimiter);
  }
  function replace(input, re, value) {
    if (re instanceof RegExp)
      return input.replace(re, value);
    return re.reduce(function(input2, re2) {
      return input2.replace(re2, value);
    }, input);
  }

  // node_modules/dot-case/dist.es2015/index.js
  function dotCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    return noCase(input, __assign({ delimiter: "." }, options));
  }

  // node_modules/param-case/dist.es2015/index.js
  function paramCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    return dotCase(input, __assign({ delimiter: "-" }, options));
  }

  // packages/list-reusable-blocks/build-module/utils/export.js
  var import_api_fetch = __toESM(require_api_fetch());
  var import_blob = __toESM(require_blob());
  async function exportReusableBlock(id) {
    const postType = await (0, import_api_fetch.default)({ path: `/wp/v2/types/wp_block` });
    const post = await (0, import_api_fetch.default)({
      path: `/wp/v2/${postType.rest_base}/${id}?context=edit`
    });
    const title = post.title.raw;
    const content = post.content.raw;
    const syncStatus = post.wp_pattern_sync_status;
    const fileContent = JSON.stringify(
      {
        __file: "wp_block",
        title,
        content,
        syncStatus
      },
      null,
      2
    );
    const fileName = paramCase(title) + ".json";
    (0, import_blob.downloadBlob)(fileName, fileContent, "application/json");
  }
  var export_default = exportReusableBlock;

  // packages/list-reusable-blocks/build-module/components/import-dropdown/index.js
  var import_compose2 = __toESM(require_compose());
  var import_i18n2 = __toESM(require_i18n());
  var import_components2 = __toESM(require_components());

  // packages/list-reusable-blocks/build-module/components/import-form/index.js
  var import_element = __toESM(require_element());
  var import_compose = __toESM(require_compose());
  var import_i18n = __toESM(require_i18n());
  var import_components = __toESM(require_components());

  // packages/list-reusable-blocks/build-module/utils/import.js
  var import_api_fetch2 = __toESM(require_api_fetch());

  // packages/list-reusable-blocks/build-module/utils/file.js
  function readTextFile(file) {
    const reader = new window.FileReader();
    return new Promise((resolve) => {
      reader.onload = () => {
        resolve(reader.result);
      };
      reader.readAsText(file);
    });
  }

  // packages/list-reusable-blocks/build-module/utils/import.js
  async function importReusableBlock(file) {
    const fileContent = await readTextFile(file);
    let parsedContent;
    try {
      parsedContent = JSON.parse(fileContent);
    } catch (e) {
      throw new Error("Invalid JSON file");
    }
    if (parsedContent.__file !== "wp_block" || !parsedContent.title || !parsedContent.content || typeof parsedContent.title !== "string" || typeof parsedContent.content !== "string" || parsedContent.syncStatus && typeof parsedContent.syncStatus !== "string") {
      throw new Error("Invalid pattern JSON file");
    }
    const postType = await (0, import_api_fetch2.default)({ path: `/wp/v2/types/wp_block` });
    const reusableBlock = await (0, import_api_fetch2.default)({
      path: `/wp/v2/${postType.rest_base}`,
      data: {
        title: parsedContent.title,
        content: parsedContent.content,
        status: "publish",
        meta: parsedContent.syncStatus === "unsynced" ? { wp_pattern_sync_status: parsedContent.syncStatus } : void 0
      },
      method: "POST"
    });
    return reusableBlock;
  }
  var import_default = importReusableBlock;

  // packages/list-reusable-blocks/build-module/components/import-form/index.js
  var import_jsx_runtime = __toESM(require_jsx_runtime());
  function ImportForm({ instanceId, onUpload }) {
    const inputId = "list-reusable-blocks-import-form-" + instanceId;
    const formRef = (0, import_element.useRef)();
    const [isLoading, setIsLoading] = (0, import_element.useState)(false);
    const [error, setError] = (0, import_element.useState)(null);
    const [file, setFile] = (0, import_element.useState)(null);
    const onChangeFile = (event) => {
      setFile(event.target.files[0]);
      setError(null);
    };
    const onSubmit = (event) => {
      event.preventDefault();
      if (!file) {
        return;
      }
      setIsLoading({ isLoading: true });
      import_default(file).then((reusableBlock) => {
        if (!formRef) {
          return;
        }
        setIsLoading(false);
        onUpload(reusableBlock);
      }).catch((errors) => {
        if (!formRef) {
          return;
        }
        let uiMessage;
        switch (errors.message) {
          case "Invalid JSON file":
            uiMessage = (0, import_i18n.__)("Invalid JSON file");
            break;
          case "Invalid pattern JSON file":
            uiMessage = (0, import_i18n.__)("Invalid pattern JSON file");
            break;
          default:
            uiMessage = (0, import_i18n.__)("Unknown error");
        }
        setIsLoading(false);
        setError(uiMessage);
      });
    };
    const onDismissError = () => {
      setError(null);
    };
    return /* @__PURE__ */ (0, import_jsx_runtime.jsxs)(
      "form",
      {
        className: "list-reusable-blocks-import-form",
        onSubmit,
        ref: formRef,
        children: [
          error && /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_components.Notice, { status: "error", onRemove: () => onDismissError(), children: error }),
          /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
            "label",
            {
              htmlFor: inputId,
              className: "list-reusable-blocks-import-form__label",
              children: (0, import_i18n.__)("File")
            }
          ),
          /* @__PURE__ */ (0, import_jsx_runtime.jsx)("input", { id: inputId, type: "file", onChange: onChangeFile }),
          /* @__PURE__ */ (0, import_jsx_runtime.jsx)(
            import_components.Button,
            {
              __next40pxDefaultSize: true,
              type: "submit",
              isBusy: isLoading,
              accessibleWhenDisabled: true,
              disabled: !file || isLoading,
              variant: "secondary",
              className: "list-reusable-blocks-import-form__button",
              children: (0, import_i18n._x)("Import", "button label")
            }
          )
        ]
      }
    );
  }
  var import_form_default = (0, import_compose.withInstanceId)(ImportForm);

  // packages/list-reusable-blocks/build-module/components/import-dropdown/index.js
  var import_jsx_runtime2 = __toESM(require_jsx_runtime());
  function ImportDropdown({ onUpload }) {
    return /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
      import_components2.Dropdown,
      {
        popoverProps: { placement: "bottom-start" },
        contentClassName: "list-reusable-blocks-import-dropdown__content",
        renderToggle: ({ isOpen, onToggle }) => /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(
          import_components2.Button,
          {
            size: "compact",
            className: "list-reusable-blocks-import-dropdown__button",
            "aria-expanded": isOpen,
            onClick: onToggle,
            variant: "primary",
            children: (0, import_i18n2.__)("Import from JSON")
          }
        ),
        renderContent: ({ onClose }) => /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_form_default, { onUpload: (0, import_compose2.pipe)(onClose, onUpload) })
      }
    );
  }
  var import_dropdown_default = ImportDropdown;

  // packages/list-reusable-blocks/build-module/index.js
  var import_jsx_runtime3 = __toESM(require_jsx_runtime());
  document.body.addEventListener("click", (event) => {
    if (!event.target.classList.contains("wp-list-reusable-blocks__export")) {
      return;
    }
    event.preventDefault();
    export_default(event.target.dataset.id);
  });
  document.addEventListener("DOMContentLoaded", () => {
    const button = document.querySelector(".page-title-action");
    if (!button) {
      return;
    }
    const showNotice = () => {
      const notice = document.createElement("div");
      notice.className = "notice notice-success is-dismissible";
      notice.innerHTML = `<p>${(0, import_i18n3.__)("Pattern imported successfully!")}</p>`;
      const headerEnd = document.querySelector(".wp-header-end");
      if (!headerEnd) {
        return;
      }
      headerEnd.parentNode.insertBefore(notice, headerEnd);
    };
    const container = document.createElement("div");
    container.className = "list-reusable-blocks__container";
    button.parentNode.insertBefore(container, button);
    (0, import_element2.createRoot)(container).render(
      /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_element2.StrictMode, { children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_dropdown_default, { onUpload: showNotice }) })
    );
  });
})();
//# sourceMappingURL=index.js.map
