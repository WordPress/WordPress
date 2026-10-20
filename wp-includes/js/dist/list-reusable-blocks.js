(function() {
"use strict";
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

  // package-external:@wordpress/components
  var require_components = __commonJS({
    "package-external:@wordpress/components"(exports, module) {
      module.exports = window.wp.components;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/api-fetch
  var require_api_fetch = __commonJS({
    "package-external:@wordpress/api-fetch"(exports, module) {
      module.exports = window.wp.apiFetch;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // node_modules/.store/remove-accents@0.5.0-S_lnImruWgc6kBSjIJlPgw/node_modules/remove-accents/index.js
  var require_remove_accents = __commonJS({
    "node_modules/.store/remove-accents@0.5.0-S_lnImruWgc6kBSjIJlPgw/node_modules/remove-accents/index.js"(exports, module) {
      var characterMap = {
        "\xC0": "A",
        "\xC1": "A",
        "\xC2": "A",
        "\xC3": "A",
        "\xC4": "A",
        "\xC5": "A",
        "\u1EA4": "A",
        "\u1EAE": "A",
        "\u1EB2": "A",
        "\u1EB4": "A",
        "\u1EB6": "A",
        "\xC6": "AE",
        "\u1EA6": "A",
        "\u1EB0": "A",
        "\u0202": "A",
        "\u1EA2": "A",
        "\u1EA0": "A",
        "\u1EA8": "A",
        "\u1EAA": "A",
        "\u1EAC": "A",
        "\xC7": "C",
        "\u1E08": "C",
        "\xC8": "E",
        "\xC9": "E",
        "\xCA": "E",
        "\xCB": "E",
        "\u1EBE": "E",
        "\u1E16": "E",
        "\u1EC0": "E",
        "\u1E14": "E",
        "\u1E1C": "E",
        "\u0206": "E",
        "\u1EBA": "E",
        "\u1EBC": "E",
        "\u1EB8": "E",
        "\u1EC2": "E",
        "\u1EC4": "E",
        "\u1EC6": "E",
        "\xCC": "I",
        "\xCD": "I",
        "\xCE": "I",
        "\xCF": "I",
        "\u1E2E": "I",
        "\u020A": "I",
        "\u1EC8": "I",
        "\u1ECA": "I",
        "\xD0": "D",
        "\xD1": "N",
        "\xD2": "O",
        "\xD3": "O",
        "\xD4": "O",
        "\xD5": "O",
        "\xD6": "O",
        "\xD8": "O",
        "\u1ED0": "O",
        "\u1E4C": "O",
        "\u1E52": "O",
        "\u020E": "O",
        "\u1ECE": "O",
        "\u1ECC": "O",
        "\u1ED4": "O",
        "\u1ED6": "O",
        "\u1ED8": "O",
        "\u1EDC": "O",
        "\u1EDE": "O",
        "\u1EE0": "O",
        "\u1EDA": "O",
        "\u1EE2": "O",
        "\xD9": "U",
        "\xDA": "U",
        "\xDB": "U",
        "\xDC": "U",
        "\u1EE6": "U",
        "\u1EE4": "U",
        "\u1EEC": "U",
        "\u1EEE": "U",
        "\u1EF0": "U",
        "\xDD": "Y",
        "\xE0": "a",
        "\xE1": "a",
        "\xE2": "a",
        "\xE3": "a",
        "\xE4": "a",
        "\xE5": "a",
        "\u1EA5": "a",
        "\u1EAF": "a",
        "\u1EB3": "a",
        "\u1EB5": "a",
        "\u1EB7": "a",
        "\xE6": "ae",
        "\u1EA7": "a",
        "\u1EB1": "a",
        "\u0203": "a",
        "\u1EA3": "a",
        "\u1EA1": "a",
        "\u1EA9": "a",
        "\u1EAB": "a",
        "\u1EAD": "a",
        "\xE7": "c",
        "\u1E09": "c",
        "\xE8": "e",
        "\xE9": "e",
        "\xEA": "e",
        "\xEB": "e",
        "\u1EBF": "e",
        "\u1E17": "e",
        "\u1EC1": "e",
        "\u1E15": "e",
        "\u1E1D": "e",
        "\u0207": "e",
        "\u1EBB": "e",
        "\u1EBD": "e",
        "\u1EB9": "e",
        "\u1EC3": "e",
        "\u1EC5": "e",
        "\u1EC7": "e",
        "\xEC": "i",
        "\xED": "i",
        "\xEE": "i",
        "\xEF": "i",
        "\u1E2F": "i",
        "\u020B": "i",
        "\u1EC9": "i",
        "\u1ECB": "i",
        "\xF0": "d",
        "\xF1": "n",
        "\xF2": "o",
        "\xF3": "o",
        "\xF4": "o",
        "\xF5": "o",
        "\xF6": "o",
        "\xF8": "o",
        "\u1ED1": "o",
        "\u1E4D": "o",
        "\u1E53": "o",
        "\u020F": "o",
        "\u1ECF": "o",
        "\u1ECD": "o",
        "\u1ED5": "o",
        "\u1ED7": "o",
        "\u1ED9": "o",
        "\u1EDD": "o",
        "\u1EDF": "o",
        "\u1EE1": "o",
        "\u1EDB": "o",
        "\u1EE3": "o",
        "\xF9": "u",
        "\xFA": "u",
        "\xFB": "u",
        "\xFC": "u",
        "\u1EE7": "u",
        "\u1EE5": "u",
        "\u1EED": "u",
        "\u1EEF": "u",
        "\u1EF1": "u",
        "\xFD": "y",
        "\xFF": "y",
        "\u0100": "A",
        "\u0101": "a",
        "\u0102": "A",
        "\u0103": "a",
        "\u0104": "A",
        "\u0105": "a",
        "\u0106": "C",
        "\u0107": "c",
        "\u0108": "C",
        "\u0109": "c",
        "\u010A": "C",
        "\u010B": "c",
        "\u010C": "C",
        "\u010D": "c",
        "C\u0306": "C",
        "c\u0306": "c",
        "\u010E": "D",
        "\u010F": "d",
        "\u0110": "D",
        "\u0111": "d",
        "\u0112": "E",
        "\u0113": "e",
        "\u0114": "E",
        "\u0115": "e",
        "\u0116": "E",
        "\u0117": "e",
        "\u0118": "E",
        "\u0119": "e",
        "\u011A": "E",
        "\u011B": "e",
        "\u011C": "G",
        "\u01F4": "G",
        "\u011D": "g",
        "\u01F5": "g",
        "\u011E": "G",
        "\u011F": "g",
        "\u0120": "G",
        "\u0121": "g",
        "\u0122": "G",
        "\u0123": "g",
        "\u0124": "H",
        "\u0125": "h",
        "\u0126": "H",
        "\u0127": "h",
        "\u1E2A": "H",
        "\u1E2B": "h",
        "\u0128": "I",
        "\u0129": "i",
        "\u012A": "I",
        "\u012B": "i",
        "\u012C": "I",
        "\u012D": "i",
        "\u012E": "I",
        "\u012F": "i",
        "\u0130": "I",
        "\u0131": "i",
        "\u0132": "IJ",
        "\u0133": "ij",
        "\u0134": "J",
        "\u0135": "j",
        "\u0136": "K",
        "\u0137": "k",
        "\u1E30": "K",
        "\u1E31": "k",
        "K\u0306": "K",
        "k\u0306": "k",
        "\u0139": "L",
        "\u013A": "l",
        "\u013B": "L",
        "\u013C": "l",
        "\u013D": "L",
        "\u013E": "l",
        "\u013F": "L",
        "\u0140": "l",
        "\u0141": "l",
        "\u0142": "l",
        "\u1E3E": "M",
        "\u1E3F": "m",
        "M\u0306": "M",
        "m\u0306": "m",
        "\u0143": "N",
        "\u0144": "n",
        "\u0145": "N",
        "\u0146": "n",
        "\u0147": "N",
        "\u0148": "n",
        "\u0149": "n",
        "N\u0306": "N",
        "n\u0306": "n",
        "\u014C": "O",
        "\u014D": "o",
        "\u014E": "O",
        "\u014F": "o",
        "\u0150": "O",
        "\u0151": "o",
        "\u0152": "OE",
        "\u0153": "oe",
        "P\u0306": "P",
        "p\u0306": "p",
        "\u0154": "R",
        "\u0155": "r",
        "\u0156": "R",
        "\u0157": "r",
        "\u0158": "R",
        "\u0159": "r",
        "R\u0306": "R",
        "r\u0306": "r",
        "\u0212": "R",
        "\u0213": "r",
        "\u015A": "S",
        "\u015B": "s",
        "\u015C": "S",
        "\u015D": "s",
        "\u015E": "S",
        "\u0218": "S",
        "\u0219": "s",
        "\u015F": "s",
        "\u0160": "S",
        "\u0161": "s",
        "\u0162": "T",
        "\u0163": "t",
        "\u021B": "t",
        "\u021A": "T",
        "\u0164": "T",
        "\u0165": "t",
        "\u0166": "T",
        "\u0167": "t",
        "T\u0306": "T",
        "t\u0306": "t",
        "\u0168": "U",
        "\u0169": "u",
        "\u016A": "U",
        "\u016B": "u",
        "\u016C": "U",
        "\u016D": "u",
        "\u016E": "U",
        "\u016F": "u",
        "\u0170": "U",
        "\u0171": "u",
        "\u0172": "U",
        "\u0173": "u",
        "\u0216": "U",
        "\u0217": "u",
        "V\u0306": "V",
        "v\u0306": "v",
        "\u0174": "W",
        "\u0175": "w",
        "\u1E82": "W",
        "\u1E83": "w",
        "X\u0306": "X",
        "x\u0306": "x",
        "\u0176": "Y",
        "\u0177": "y",
        "\u0178": "Y",
        "Y\u0306": "Y",
        "y\u0306": "y",
        "\u0179": "Z",
        "\u017A": "z",
        "\u017B": "Z",
        "\u017C": "z",
        "\u017D": "Z",
        "\u017E": "z",
        "\u017F": "s",
        "\u0192": "f",
        "\u01A0": "O",
        "\u01A1": "o",
        "\u01AF": "U",
        "\u01B0": "u",
        "\u01CD": "A",
        "\u01CE": "a",
        "\u01CF": "I",
        "\u01D0": "i",
        "\u01D1": "O",
        "\u01D2": "o",
        "\u01D3": "U",
        "\u01D4": "u",
        "\u01D5": "U",
        "\u01D6": "u",
        "\u01D7": "U",
        "\u01D8": "u",
        "\u01D9": "U",
        "\u01DA": "u",
        "\u01DB": "U",
        "\u01DC": "u",
        "\u1EE8": "U",
        "\u1EE9": "u",
        "\u1E78": "U",
        "\u1E79": "u",
        "\u01FA": "A",
        "\u01FB": "a",
        "\u01FC": "AE",
        "\u01FD": "ae",
        "\u01FE": "O",
        "\u01FF": "o",
        "\xDE": "TH",
        "\xFE": "th",
        "\u1E54": "P",
        "\u1E55": "p",
        "\u1E64": "S",
        "\u1E65": "s",
        "X\u0301": "X",
        "x\u0301": "x",
        "\u0403": "\u0413",
        "\u0453": "\u0433",
        "\u040C": "\u041A",
        "\u045C": "\u043A",
        "A\u030B": "A",
        "a\u030B": "a",
        "E\u030B": "E",
        "e\u030B": "e",
        "I\u030B": "I",
        "i\u030B": "i",
        "\u01F8": "N",
        "\u01F9": "n",
        "\u1ED2": "O",
        "\u1ED3": "o",
        "\u1E50": "O",
        "\u1E51": "o",
        "\u1EEA": "U",
        "\u1EEB": "u",
        "\u1E80": "W",
        "\u1E81": "w",
        "\u1EF2": "Y",
        "\u1EF3": "y",
        "\u0200": "A",
        "\u0201": "a",
        "\u0204": "E",
        "\u0205": "e",
        "\u0208": "I",
        "\u0209": "i",
        "\u020C": "O",
        "\u020D": "o",
        "\u0210": "R",
        "\u0211": "r",
        "\u0214": "U",
        "\u0215": "u",
        "B\u030C": "B",
        "b\u030C": "b",
        "\u010C\u0323": "C",
        "\u010D\u0323": "c",
        "\xCA\u030C": "E",
        "\xEA\u030C": "e",
        "F\u030C": "F",
        "f\u030C": "f",
        "\u01E6": "G",
        "\u01E7": "g",
        "\u021E": "H",
        "\u021F": "h",
        "J\u030C": "J",
        "\u01F0": "j",
        "\u01E8": "K",
        "\u01E9": "k",
        "M\u030C": "M",
        "m\u030C": "m",
        "P\u030C": "P",
        "p\u030C": "p",
        "Q\u030C": "Q",
        "q\u030C": "q",
        "\u0158\u0329": "R",
        "\u0159\u0329": "r",
        "\u1E66": "S",
        "\u1E67": "s",
        "V\u030C": "V",
        "v\u030C": "v",
        "W\u030C": "W",
        "w\u030C": "w",
        "X\u030C": "X",
        "x\u030C": "x",
        "Y\u030C": "Y",
        "y\u030C": "y",
        "A\u0327": "A",
        "a\u0327": "a",
        "B\u0327": "B",
        "b\u0327": "b",
        "\u1E10": "D",
        "\u1E11": "d",
        "\u0228": "E",
        "\u0229": "e",
        "\u0190\u0327": "E",
        "\u025B\u0327": "e",
        "\u1E28": "H",
        "\u1E29": "h",
        "I\u0327": "I",
        "i\u0327": "i",
        "\u0197\u0327": "I",
        "\u0268\u0327": "i",
        "M\u0327": "M",
        "m\u0327": "m",
        "O\u0327": "O",
        "o\u0327": "o",
        "Q\u0327": "Q",
        "q\u0327": "q",
        "U\u0327": "U",
        "u\u0327": "u",
        "X\u0327": "X",
        "x\u0327": "x",
        "Z\u0327": "Z",
        "z\u0327": "z",
        "\u0439": "\u0438",
        "\u0419": "\u0418",
        "\u0451": "\u0435",
        "\u0401": "\u0415"
      };
      var chars = Object.keys(characterMap).join("|");
      var allAccents = new RegExp(chars, "g");
      var firstAccent = new RegExp(chars, "");
      function matcher(match) {
        return characterMap[match];
      }
      var removeAccents2 = function(string) {
        return string.replace(allAccents, matcher);
      };
      var hasAccents = function(string) {
        return !!string.match(firstAccent);
      };
      module.exports = removeAccents2;
      module.exports.has = hasAccents;
      module.exports.remove = removeAccents2;
    }
  });

  // package-external:@wordpress/blob
  var require_blob = __commonJS({
    "package-external:@wordpress/blob"(exports, module) {
      module.exports = window.wp.blob;
    }
  });

  // packages/list-reusable-blocks/build-module/index.mjs
  var import_element2 = __toESM(require_element(), 1);
  var import_i18n3 = __toESM(require_i18n(), 1);

  // packages/list-reusable-blocks/build-module/components/import-dropdown/index.mjs
  var import_components2 = __toESM(require_components(), 1);
  var import_compose2 = __toESM(require_compose(), 1);
  var import_i18n2 = __toESM(require_i18n(), 1);

  // packages/list-reusable-blocks/build-module/components/import-form/index.mjs
  var import_components = __toESM(require_components(), 1);
  var import_compose = __toESM(require_compose(), 1);
  var import_element = __toESM(require_element(), 1);
  var import_i18n = __toESM(require_i18n(), 1);

  // packages/list-reusable-blocks/build-module/utils/import.mjs
  var import_api_fetch = __toESM(require_api_fetch(), 1);

  // packages/list-reusable-blocks/build-module/utils/file.mjs
  function readTextFile(file) {
    const reader = new window.FileReader();
    return new Promise((resolve) => {
      reader.onload = () => {
        resolve(reader.result);
      };
      reader.readAsText(file);
    });
  }

  // packages/list-reusable-blocks/build-module/utils/import.mjs
  async function importReusableBlock(file) {
    const fileContent = await readTextFile(file);
    let parsedContent;
    try {
      parsedContent = JSON.parse(fileContent);
    } catch {
      throw new Error("Invalid JSON file");
    }
    if (parsedContent.__file !== "wp_block" || !parsedContent.title || !parsedContent.content || typeof parsedContent.title !== "string" || typeof parsedContent.content !== "string" || parsedContent.syncStatus && typeof parsedContent.syncStatus !== "string") {
      throw new Error("Invalid pattern JSON file");
    }
    const postType = await (0, import_api_fetch.default)({
      path: `/wp/v2/types/wp_block`
    });
    const reusableBlock = await (0, import_api_fetch.default)({
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

  // packages/list-reusable-blocks/build-module/components/import-form/index.mjs
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
  function ImportForm({ instanceId, onUpload }) {
    const inputId = "list-reusable-blocks-import-form-" + instanceId;
    const formRef = (0, import_element.useRef)(null);
    const [isLoading, setIsLoading] = (0, import_element.useState)(false);
    const [error, setError] = (0, import_element.useState)(null);
    const [file, setFile] = (0, import_element.useState)(null);
    const onChangeFile = (event) => {
      setFile(event.target.files?.[0] || null);
      setError(null);
    };
    const onSubmit = (event) => {
      event.preventDefault();
      if (!file) {
        return;
      }
      setIsLoading(true);
      import_default(file).then((reusableBlock) => {
        if (!formRef.current) {
          return;
        }
        setIsLoading(false);
        onUpload(reusableBlock);
      }).catch((errors) => {
        if (!formRef.current) {
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

  // packages/list-reusable-blocks/build-module/components/import-dropdown/index.mjs
  var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
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

  // node_modules/.store/tslib@2.8.1-LT-dxu1EkVy7OUDyVf5glQ/node_modules/tslib/tslib.es6.mjs
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

  // node_modules/.store/lower-case@2.0.2-5YZP5nczP5_f_8Vt5KBPjA/node_modules/lower-case/dist.es2015/index.js
  function lowerCase(str) {
    return str.toLowerCase();
  }

  // node_modules/.store/no-case@3.0.4-R4Hzt6qn-erEsTak7ojDpg/node_modules/no-case/dist.es2015/index.js
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

  // node_modules/.store/dot-case@3.0.4-hi-rLWlWmiZ169n4dWcbUA/node_modules/dot-case/dist.es2015/index.js
  function dotCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    return noCase(input, __assign({ delimiter: "." }, options));
  }

  // node_modules/.store/param-case@3.0.4-rdAht_VwpGpefPRylN2bTA/node_modules/param-case/dist.es2015/index.js
  function paramCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    return dotCase(input, __assign({ delimiter: "-" }, options));
  }

  // packages/list-reusable-blocks/build-module/utils/export.mjs
  var import_remove_accents = __toESM(require_remove_accents(), 1);
  var import_api_fetch2 = __toESM(require_api_fetch(), 1);
  var import_blob = __toESM(require_blob(), 1);
  async function exportReusableBlock(id) {
    const postType = await (0, import_api_fetch2.default)({
      path: `/wp/v2/types/wp_block`
    });
    const post = await (0, import_api_fetch2.default)({
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
    const fileName = paramCase((0, import_remove_accents.default)(title)) + ".json";
    (0, import_blob.downloadBlob)(fileName, fileContent, "application/json");
  }
  var export_default = exportReusableBlock;

  // packages/list-reusable-blocks/build-module/index.mjs
  var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
  document.body.addEventListener("click", (event) => {
    const target = event.target;
    if (!target.classList.contains("wp-list-reusable-blocks__export")) {
      return;
    }
    event.preventDefault();
    const blockId = target.dataset.id;
    if (blockId) {
      export_default(Number(blockId));
    }
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
      if (!headerEnd || !headerEnd.parentNode) {
        return;
      }
      headerEnd.parentNode.insertBefore(notice, headerEnd);
    };
    const container = document.createElement("div");
    container.className = "list-reusable-blocks__container";
    if (!button.parentNode) {
      return;
    }
    button.parentNode.insertBefore(container, button);
    (0, import_element2.createRoot)(container).render(
      /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_element2.StrictMode, { children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_dropdown_default, { onUpload: showNotice }) })
    );
  });
})();
(window.wp ||= {}).listReusableBlocks = wp.listReusableBlocks;
})();
