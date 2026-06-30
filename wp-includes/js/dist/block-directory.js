var wp;
(wp ||= {}).blockDirectory = (() => {
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

  // package-external:@wordpress/plugins
  var require_plugins = __commonJS({
    "package-external:@wordpress/plugins"(exports, module) {
      module.exports = window.wp.plugins;
    }
  });

  // package-external:@wordpress/hooks
  var require_hooks = __commonJS({
    "package-external:@wordpress/hooks"(exports, module) {
      module.exports = window.wp.hooks;
    }
  });

  // package-external:@wordpress/blocks
  var require_blocks = __commonJS({
    "package-external:@wordpress/blocks"(exports, module) {
      module.exports = window.wp.blocks;
    }
  });

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/editor
  var require_editor = __commonJS({
    "package-external:@wordpress/editor"(exports, module) {
      module.exports = window.wp.editor;
    }
  });

  // package-external:@wordpress/block-editor
  var require_block_editor = __commonJS({
    "package-external:@wordpress/block-editor"(exports, module) {
      module.exports = window.wp.blockEditor;
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

  // package-external:@wordpress/notices
  var require_notices = __commonJS({
    "package-external:@wordpress/notices"(exports, module) {
      module.exports = window.wp.notices;
    }
  });

  // package-external:@wordpress/url
  var require_url = __commonJS({
    "package-external:@wordpress/url"(exports, module) {
      module.exports = window.wp.url;
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

  // vendor-external:react
  var require_react = __commonJS({
    "vendor-external:react"(exports, module) {
      module.exports = window.React;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // package-external:@wordpress/a11y
  var require_a11y = __commonJS({
    "package-external:@wordpress/a11y"(exports, module) {
      module.exports = window.wp.a11y;
    }
  });

  // package-external:@wordpress/primitives
  var require_primitives = __commonJS({
    "package-external:@wordpress/primitives"(exports, module) {
      module.exports = window.wp.primitives;
    }
  });

  // packages/block-directory/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    store: () => store
  });

  // packages/block-directory/build-module/plugins/index.mjs
  var import_plugins = __toESM(require_plugins(), 1);
  var import_hooks = __toESM(require_hooks(), 1);

  // packages/block-directory/build-module/components/auto-block-uninstaller/index.mjs
  var import_blocks2 = __toESM(require_blocks(), 1);
  var import_data4 = __toESM(require_data(), 1);
  var import_element = __toESM(require_element(), 1);
  var import_editor = __toESM(require_editor(), 1);

  // packages/block-directory/build-module/store/index.mjs
  var import_data3 = __toESM(require_data(), 1);

  // packages/block-directory/build-module/store/reducer.mjs
  var import_data = __toESM(require_data(), 1);
  var downloadableBlocks = (state = {}, action) => {
    switch (action.type) {
      case "FETCH_DOWNLOADABLE_BLOCKS":
        return {
          ...state,
          [action.filterValue]: {
            isRequesting: true
          }
        };
      case "RECEIVE_DOWNLOADABLE_BLOCKS":
        return {
          ...state,
          [action.filterValue]: {
            results: action.downloadableBlocks,
            isRequesting: false
          }
        };
    }
    return state;
  };
  var blockManagement = (state = {
    installedBlockTypes: [],
    isInstalling: {}
  }, action) => {
    switch (action.type) {
      case "ADD_INSTALLED_BLOCK_TYPE":
        return {
          ...state,
          installedBlockTypes: [
            ...state.installedBlockTypes,
            action.item
          ]
        };
      case "REMOVE_INSTALLED_BLOCK_TYPE":
        return {
          ...state,
          installedBlockTypes: state.installedBlockTypes.filter(
            (blockType) => blockType.name !== action.item.name
          )
        };
      case "SET_INSTALLING_BLOCK":
        return {
          ...state,
          isInstalling: {
            ...state.isInstalling,
            [action.blockId]: action.isInstalling
          }
        };
    }
    return state;
  };
  var errorNotices = (state = {}, action) => {
    switch (action.type) {
      case "SET_ERROR_NOTICE":
        return {
          ...state,
          [action.blockId]: {
            message: action.message,
            isFatal: action.isFatal
          }
        };
      case "CLEAR_ERROR_NOTICE":
        const { [action.blockId]: blockId, ...restState } = state;
        return restState;
    }
    return state;
  };
  var reducer_default = (0, import_data.combineReducers)({
    downloadableBlocks,
    blockManagement,
    errorNotices
  });

  // packages/block-directory/build-module/store/selectors.mjs
  var selectors_exports = {};
  __export(selectors_exports, {
    getDownloadableBlocks: () => getDownloadableBlocks,
    getErrorNoticeForBlock: () => getErrorNoticeForBlock,
    getErrorNotices: () => getErrorNotices,
    getInstalledBlockTypes: () => getInstalledBlockTypes,
    getNewBlockTypes: () => getNewBlockTypes,
    getUnusedBlockTypes: () => getUnusedBlockTypes,
    isInstalling: () => isInstalling,
    isRequestingDownloadableBlocks: () => isRequestingDownloadableBlocks
  });
  var import_data2 = __toESM(require_data(), 1);
  var import_block_editor = __toESM(require_block_editor(), 1);
  var EMPTY_ARRAY = [];
  function isRequestingDownloadableBlocks(state, filterValue) {
    return state.downloadableBlocks[filterValue]?.isRequesting ?? false;
  }
  function getDownloadableBlocks(state, filterValue) {
    return state.downloadableBlocks[filterValue]?.results ?? EMPTY_ARRAY;
  }
  function getInstalledBlockTypes(state) {
    return state.blockManagement.installedBlockTypes;
  }
  var getNewBlockTypes = (0, import_data2.createRegistrySelector)(
    (select) => (0, import_data2.createSelector)(
      (state) => {
        const installedBlockTypes = getInstalledBlockTypes(state);
        if (!installedBlockTypes.length) {
          return EMPTY_ARRAY;
        }
        const { getBlockName, getClientIdsWithDescendants } = select(import_block_editor.store);
        const installedBlockNames = installedBlockTypes.map(
          (blockType) => blockType.name
        );
        const foundBlockNames = getClientIdsWithDescendants().flatMap(
          (clientId) => {
            const blockName = getBlockName(clientId);
            return installedBlockNames.includes(blockName) ? blockName : [];
          }
        );
        const newBlockTypes = installedBlockTypes.filter(
          (blockType) => foundBlockNames.includes(blockType.name)
        );
        return newBlockTypes.length > 0 ? newBlockTypes : EMPTY_ARRAY;
      },
      (state) => [
        getInstalledBlockTypes(state),
        select(import_block_editor.store).getClientIdsWithDescendants()
      ]
    )
  );
  var getUnusedBlockTypes = (0, import_data2.createRegistrySelector)(
    (select) => (0, import_data2.createSelector)(
      (state) => {
        const installedBlockTypes = getInstalledBlockTypes(state);
        if (!installedBlockTypes.length) {
          return EMPTY_ARRAY;
        }
        const { getBlockName, getClientIdsWithDescendants } = select(import_block_editor.store);
        const installedBlockNames = installedBlockTypes.map(
          (blockType) => blockType.name
        );
        const foundBlockNames = getClientIdsWithDescendants().flatMap(
          (clientId) => {
            const blockName = getBlockName(clientId);
            return installedBlockNames.includes(blockName) ? blockName : [];
          }
        );
        const unusedBlockTypes = installedBlockTypes.filter(
          (blockType) => !foundBlockNames.includes(blockType.name)
        );
        return unusedBlockTypes.length > 0 ? unusedBlockTypes : EMPTY_ARRAY;
      },
      (state) => [
        getInstalledBlockTypes(state),
        select(import_block_editor.store).getClientIdsWithDescendants()
      ]
    )
  );
  function isInstalling(state, blockId) {
    return state.blockManagement.isInstalling[blockId] || false;
  }
  function getErrorNotices(state) {
    return state.errorNotices;
  }
  function getErrorNoticeForBlock(state, blockId) {
    return state.errorNotices[blockId];
  }

  // packages/block-directory/build-module/store/actions.mjs
  var actions_exports = {};
  __export(actions_exports, {
    addInstalledBlockType: () => addInstalledBlockType,
    clearErrorNotice: () => clearErrorNotice,
    fetchDownloadableBlocks: () => fetchDownloadableBlocks,
    installBlockType: () => installBlockType,
    receiveDownloadableBlocks: () => receiveDownloadableBlocks,
    removeInstalledBlockType: () => removeInstalledBlockType,
    setErrorNotice: () => setErrorNotice,
    setIsInstalling: () => setIsInstalling,
    uninstallBlockType: () => uninstallBlockType
  });
  var import_blocks = __toESM(require_blocks(), 1);
  var import_i18n = __toESM(require_i18n(), 1);
  var import_api_fetch2 = __toESM(require_api_fetch(), 1);
  var import_notices = __toESM(require_notices(), 1);
  var import_url = __toESM(require_url(), 1);

  // packages/block-directory/build-module/store/load-assets.mjs
  var import_api_fetch = __toESM(require_api_fetch(), 1);
  var loadAsset = (el) => {
    return new Promise((resolve, reject) => {
      const newNode = document.createElement(el.nodeName);
      ["id", "rel", "src", "href", "type"].forEach((attr) => {
        if (el[attr]) {
          newNode[attr] = el[attr];
        }
      });
      if (el.innerHTML) {
        newNode.appendChild(document.createTextNode(el.innerHTML));
      }
      newNode.onload = () => resolve(true);
      newNode.onerror = () => reject(new Error("Error loading asset."));
      document.body.appendChild(newNode);
      if ("link" === newNode.nodeName.toLowerCase() || "script" === newNode.nodeName.toLowerCase() && !newNode.src) {
        resolve();
      }
    });
  };
  async function loadAssets() {
    const response = await (0, import_api_fetch.default)({
      url: document.location.href,
      parse: false
    });
    const data = await response.text();
    const doc = new window.DOMParser().parseFromString(data, "text/html");
    const newAssets = Array.from(
      doc.querySelectorAll('link[rel="stylesheet"],script')
    ).filter((asset) => asset.id && !document.getElementById(asset.id));
    for (const newAsset of newAssets) {
      await loadAsset(newAsset);
    }
  }

  // packages/block-directory/build-module/store/utils/get-plugin-url.mjs
  function getPluginUrl(block) {
    if (!block) {
      return false;
    }
    const link = block.links["wp:plugin"] || block.links.self;
    if (link && link.length) {
      return link[0].href;
    }
    return false;
  }

  // packages/block-directory/build-module/store/actions.mjs
  function fetchDownloadableBlocks(filterValue) {
    return { type: "FETCH_DOWNLOADABLE_BLOCKS", filterValue };
  }
  function receiveDownloadableBlocks(downloadableBlocks2, filterValue) {
    return {
      type: "RECEIVE_DOWNLOADABLE_BLOCKS",
      downloadableBlocks: downloadableBlocks2,
      filterValue
    };
  }
  var installBlockType = (block) => async ({ registry, dispatch }) => {
    const { id, name } = block;
    let success = false;
    dispatch.clearErrorNotice(id);
    try {
      dispatch.setIsInstalling(id, true);
      const url = getPluginUrl(block);
      let links = {};
      if (url) {
        await (0, import_api_fetch2.default)({
          method: "PUT",
          url,
          data: { status: "active" }
        });
      } else {
        const response = await (0, import_api_fetch2.default)({
          method: "POST",
          path: "wp/v2/plugins",
          data: { slug: id, status: "active" }
        });
        links = response._links;
      }
      dispatch.addInstalledBlockType({
        ...block,
        links: { ...block.links, ...links }
      });
      const metadataFields = [
        "api_version",
        "title",
        "category",
        "parent",
        "ancestor",
        "icon",
        "description",
        "keywords",
        "attributes",
        "provides_context",
        "uses_context",
        "selectors",
        "supports",
        "styles",
        "example",
        "variations",
        "allowed_blocks",
        "block_hooks"
      ];
      await (0, import_api_fetch2.default)({
        path: (0, import_url.addQueryArgs)(`/wp/v2/block-types/${name}`, {
          _fields: metadataFields
        })
      }).catch(() => {
      }).then((response) => {
        if (!response) {
          return;
        }
        (0, import_blocks.unstable__bootstrapServerSideBlockDefinitions)({
          [name]: Object.fromEntries(
            Object.entries(response).filter(
              ([key]) => metadataFields.includes(key)
            )
          )
        });
      });
      await loadAssets();
      const registeredBlocks = registry.select(import_blocks.store).getBlockTypes();
      if (!registeredBlocks.some((i) => i.name === name)) {
        throw new Error(
          (0, import_i18n.__)("Error registering block. Try reloading the page.")
        );
      }
      registry.dispatch(import_notices.store).createInfoNotice(
        (0, import_i18n.sprintf)(
          // translators: %s is the block title.
          (0, import_i18n.__)("Block %s installed and added."),
          block.title
        ),
        {
          speak: true,
          type: "snackbar"
        }
      );
      success = true;
    } catch (error) {
      let message = error.message || (0, import_i18n.__)("An error occurred.");
      let isFatal = error instanceof Error;
      const fatalAPIErrors = {
        folder_exists: (0, import_i18n.__)(
          "This block is already installed. Try reloading the page."
        ),
        unable_to_connect_to_filesystem: (0, import_i18n.__)(
          "Error installing block. You can reload the page and try again."
        )
      };
      if (fatalAPIErrors[error.code]) {
        isFatal = true;
        message = fatalAPIErrors[error.code];
      }
      dispatch.setErrorNotice(id, message, isFatal);
      registry.dispatch(import_notices.store).createErrorNotice(message, {
        speak: true,
        isDismissible: true
      });
    }
    dispatch.setIsInstalling(id, false);
    return success;
  };
  var uninstallBlockType = (block) => async ({ registry, dispatch }) => {
    try {
      const url = getPluginUrl(block);
      await (0, import_api_fetch2.default)({
        method: "PUT",
        url,
        data: { status: "inactive" }
      });
      await (0, import_api_fetch2.default)({
        method: "DELETE",
        url
      });
      dispatch.removeInstalledBlockType(block);
    } catch (error) {
      registry.dispatch(import_notices.store).createErrorNotice(
        error.message || (0, import_i18n.__)("An error occurred.")
      );
    }
  };
  function addInstalledBlockType(item) {
    return {
      type: "ADD_INSTALLED_BLOCK_TYPE",
      item
    };
  }
  function removeInstalledBlockType(item) {
    return {
      type: "REMOVE_INSTALLED_BLOCK_TYPE",
      item
    };
  }
  function setIsInstalling(blockId, isInstalling2) {
    return {
      type: "SET_INSTALLING_BLOCK",
      blockId,
      isInstalling: isInstalling2
    };
  }
  function setErrorNotice(blockId, message, isFatal = false) {
    return {
      type: "SET_ERROR_NOTICE",
      blockId,
      message,
      isFatal
    };
  }
  function clearErrorNotice(blockId) {
    return {
      type: "CLEAR_ERROR_NOTICE",
      blockId
    };
  }

  // packages/block-directory/build-module/store/resolvers.mjs
  var resolvers_exports = {};
  __export(resolvers_exports, {
    getDownloadableBlocks: () => getDownloadableBlocks2
  });

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

  // node_modules/pascal-case/dist.es2015/index.js
  function pascalCaseTransform(input, index) {
    var firstChar = input.charAt(0);
    var lowerChars = input.substr(1).toLowerCase();
    if (index > 0 && firstChar >= "0" && firstChar <= "9") {
      return "_" + firstChar + lowerChars;
    }
    return "" + firstChar.toUpperCase() + lowerChars;
  }
  function pascalCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    return noCase(input, __assign({ delimiter: "", transform: pascalCaseTransform }, options));
  }

  // node_modules/camel-case/dist.es2015/index.js
  function camelCaseTransform(input, index) {
    if (index === 0)
      return input.toLowerCase();
    return pascalCaseTransform(input, index);
  }
  function camelCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    return pascalCase(input, __assign({ transform: camelCaseTransform }, options));
  }

  // packages/block-directory/build-module/store/resolvers.mjs
  var import_api_fetch3 = __toESM(require_api_fetch(), 1);
  var getDownloadableBlocks2 = (filterValue) => async ({ dispatch }) => {
    if (!filterValue) {
      return;
    }
    try {
      dispatch(fetchDownloadableBlocks(filterValue));
      const results = await (0, import_api_fetch3.default)({
        path: `wp/v2/block-directory/search?term=${filterValue}`
      });
      const blocks = results.map(
        (result) => Object.fromEntries(
          Object.entries(result).map(([key, value]) => [
            camelCase(key),
            value
          ])
        )
      );
      dispatch(receiveDownloadableBlocks(blocks, filterValue));
    } catch {
      dispatch(receiveDownloadableBlocks([], filterValue));
    }
  };

  // packages/block-directory/build-module/store/index.mjs
  var STORE_NAME = "core/block-directory";
  var storeConfig = {
    reducer: reducer_default,
    selectors: selectors_exports,
    actions: actions_exports,
    resolvers: resolvers_exports
  };
  var store = (0, import_data3.createReduxStore)(STORE_NAME, storeConfig);
  (0, import_data3.register)(store);

  // packages/block-directory/build-module/components/auto-block-uninstaller/index.mjs
  function AutoBlockUninstaller() {
    const { uninstallBlockType: uninstallBlockType2 } = (0, import_data4.useDispatch)(store);
    const shouldRemoveBlockTypes = (0, import_data4.useSelect)((select) => {
      const { isAutosavingPost, isSavingPost } = select(import_editor.store);
      return isSavingPost() && !isAutosavingPost();
    }, []);
    const unusedBlockTypes = (0, import_data4.useSelect)(
      (select) => select(store).getUnusedBlockTypes(),
      []
    );
    (0, import_element.useEffect)(() => {
      if (shouldRemoveBlockTypes && unusedBlockTypes.length) {
        unusedBlockTypes.forEach((blockType) => {
          uninstallBlockType2(blockType);
          (0, import_blocks2.unregisterBlockType)(blockType.name);
        });
      }
    }, [shouldRemoveBlockTypes]);
    return null;
  }

  // packages/block-directory/build-module/plugins/inserter-menu-downloadable-blocks-panel/index.mjs
  var import_block_editor3 = __toESM(require_block_editor(), 1);
  var import_compose = __toESM(require_compose(), 1);
  var import_element6 = __toESM(require_element(), 1);

  // packages/block-directory/build-module/components/downloadable-blocks-panel/index.mjs
  var import_i18n8 = __toESM(require_i18n(), 1);
  var import_components4 = __toESM(require_components(), 1);
  var import_core_data = __toESM(require_core_data(), 1);
  var import_data8 = __toESM(require_data(), 1);
  var import_blocks5 = __toESM(require_blocks(), 1);

  // packages/block-directory/build-module/components/downloadable-blocks-list/index.mjs
  var import_i18n5 = __toESM(require_i18n(), 1);
  var import_components2 = __toESM(require_components(), 1);
  var import_blocks4 = __toESM(require_blocks(), 1);
  var import_data7 = __toESM(require_data(), 1);

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

  // packages/block-directory/build-module/components/downloadable-block-list-item/index.mjs
  var import_i18n4 = __toESM(require_i18n(), 1);
  var import_components = __toESM(require_components(), 1);
  var import_element4 = __toESM(require_element(), 1);
  var import_html_entities = __toESM(require_html_entities(), 1);
  var import_blocks3 = __toESM(require_blocks(), 1);
  var import_data6 = __toESM(require_data(), 1);

  // node_modules/@base-ui/utils/esm/useRefWithInit.js
  var React = __toESM(require_react(), 1);
  var UNINITIALIZED = {};
  function useRefWithInit(init, initArg) {
    const ref = React.useRef(UNINITIALIZED);
    if (ref.current === UNINITIALIZED) {
      ref.current = init(initArg);
    }
    return ref;
  }

  // node_modules/@base-ui/utils/esm/warn.js
  var set;
  if (true) {
    set = /* @__PURE__ */ new Set();
  }
  function warn(...messages) {
    if (true) {
      const messageKey = messages.join(" ");
      if (!set.has(messageKey)) {
        set.add(messageKey);
        console.warn(`Base UI: ${messageKey}`);
      }
    }
  }

  // node_modules/@base-ui/react/esm/internals/useRenderElement.js
  var React4 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/esm/useMergedRefs.js
  function useMergedRefs(a, b, c, d) {
    const forkRef = useRefWithInit(createForkRef).current;
    if (didChange(forkRef, a, b, c, d)) {
      update(forkRef, [a, b, c, d]);
    }
    return forkRef.callback;
  }
  function useMergedRefsN(refs) {
    const forkRef = useRefWithInit(createForkRef).current;
    if (didChangeN(forkRef, refs)) {
      update(forkRef, refs);
    }
    return forkRef.callback;
  }
  function createForkRef() {
    return {
      callback: null,
      cleanup: null,
      refs: []
    };
  }
  function didChange(forkRef, a, b, c, d) {
    return forkRef.refs[0] !== a || forkRef.refs[1] !== b || forkRef.refs[2] !== c || forkRef.refs[3] !== d;
  }
  function didChangeN(forkRef, newRefs) {
    return forkRef.refs.length !== newRefs.length || forkRef.refs.some((ref, index) => ref !== newRefs[index]);
  }
  function update(forkRef, refs) {
    forkRef.refs = refs;
    if (refs.every((ref) => ref == null)) {
      forkRef.callback = null;
      return;
    }
    forkRef.callback = (instance) => {
      if (forkRef.cleanup) {
        forkRef.cleanup();
        forkRef.cleanup = null;
      }
      if (instance != null) {
        const cleanupCallbacks = Array(refs.length).fill(null);
        for (let i = 0; i < refs.length; i += 1) {
          const ref = refs[i];
          if (ref == null) {
            continue;
          }
          switch (typeof ref) {
            case "function": {
              const refCleanup = ref(instance);
              if (typeof refCleanup === "function") {
                cleanupCallbacks[i] = refCleanup;
              }
              break;
            }
            case "object": {
              ref.current = instance;
              break;
            }
            default:
          }
        }
        forkRef.cleanup = () => {
          for (let i = 0; i < refs.length; i += 1) {
            const ref = refs[i];
            if (ref == null) {
              continue;
            }
            switch (typeof ref) {
              case "function": {
                const cleanupCallback = cleanupCallbacks[i];
                if (typeof cleanupCallback === "function") {
                  cleanupCallback();
                } else {
                  ref(null);
                }
                break;
              }
              case "object": {
                ref.current = null;
                break;
              }
              default:
            }
          }
        };
      }
    };
  }

  // node_modules/@base-ui/utils/esm/getReactElementRef.js
  var React3 = __toESM(require_react(), 1);

  // node_modules/@base-ui/utils/esm/reactVersion.js
  var React2 = __toESM(require_react(), 1);
  var majorVersion = parseInt(React2.version, 10);
  function isReactVersionAtLeast(reactVersionToCheck) {
    return majorVersion >= reactVersionToCheck;
  }

  // node_modules/@base-ui/utils/esm/getReactElementRef.js
  function getReactElementRef(element) {
    if (!/* @__PURE__ */ React3.isValidElement(element)) {
      return null;
    }
    const reactElement = element;
    const propsWithRef = reactElement.props;
    return (isReactVersionAtLeast(19) ? propsWithRef?.ref : reactElement.ref) ?? null;
  }

  // node_modules/@base-ui/utils/esm/mergeObjects.js
  function mergeObjects(a, b) {
    if (a && !b) {
      return a;
    }
    if (!a && b) {
      return b;
    }
    if (a || b) {
      return {
        ...a,
        ...b
      };
    }
    return void 0;
  }

  // node_modules/@base-ui/utils/esm/empty.js
  var EMPTY_ARRAY2 = Object.freeze([]);
  var EMPTY_OBJECT = Object.freeze({});

  // node_modules/@base-ui/react/esm/internals/getStateAttributesProps.js
  function getStateAttributesProps(state, customMapping) {
    const props = {};
    for (const key in state) {
      const value = state[key];
      if (customMapping?.hasOwnProperty(key)) {
        const customProps = customMapping[key](value);
        if (customProps != null) {
          Object.assign(props, customProps);
        }
        continue;
      }
      if (value === true) {
        props[`data-${key.toLowerCase()}`] = "";
      } else if (value) {
        props[`data-${key.toLowerCase()}`] = value.toString();
      }
    }
    return props;
  }

  // node_modules/@base-ui/react/esm/utils/resolveClassName.js
  function resolveClassName(className, state) {
    return typeof className === "function" ? className(state) : className;
  }

  // node_modules/@base-ui/react/esm/utils/resolveStyle.js
  function resolveStyle(style, state) {
    return typeof style === "function" ? style(state) : style;
  }

  // node_modules/@base-ui/react/esm/merge-props/mergeProps.js
  var EMPTY_PROPS = {};
  function mergeProps(a, b, c, d, e) {
    if (!c && !d && !e && !a) {
      return createInitialMergedProps(b);
    }
    let merged = createInitialMergedProps(a);
    if (b) {
      merged = mergeInto(merged, b);
    }
    if (c) {
      merged = mergeInto(merged, c);
    }
    if (d) {
      merged = mergeInto(merged, d);
    }
    if (e) {
      merged = mergeInto(merged, e);
    }
    return merged;
  }
  function mergePropsN(props) {
    if (props.length === 0) {
      return EMPTY_PROPS;
    }
    if (props.length === 1) {
      return createInitialMergedProps(props[0]);
    }
    let merged = createInitialMergedProps(props[0]);
    for (let i = 1; i < props.length; i += 1) {
      merged = mergeInto(merged, props[i]);
    }
    return merged;
  }
  function createInitialMergedProps(inputProps) {
    if (isPropsGetter(inputProps)) {
      return {
        ...resolvePropsGetter(inputProps, EMPTY_PROPS)
      };
    }
    return copyInitialProps(inputProps);
  }
  function mergeInto(merged, inputProps) {
    if (isPropsGetter(inputProps)) {
      return resolvePropsGetter(inputProps, merged);
    }
    return mutablyMergeInto(merged, inputProps);
  }
  function copyInitialProps(inputProps) {
    const copiedProps = {
      ...inputProps
    };
    for (const propName in copiedProps) {
      const propValue = copiedProps[propName];
      if (isEventHandler(propName, propValue)) {
        copiedProps[propName] = wrapEventHandler(propValue);
      }
    }
    return copiedProps;
  }
  function mutablyMergeInto(mergedProps, externalProps) {
    if (!externalProps) {
      return mergedProps;
    }
    for (const propName in externalProps) {
      const externalPropValue = externalProps[propName];
      switch (propName) {
        case "style": {
          mergedProps[propName] = mergeObjects(mergedProps.style, externalPropValue);
          break;
        }
        case "className": {
          mergedProps[propName] = mergeClassNames(mergedProps.className, externalPropValue);
          break;
        }
        default: {
          if (isEventHandler(propName, externalPropValue)) {
            mergedProps[propName] = mergeEventHandlers(mergedProps[propName], externalPropValue);
          } else {
            mergedProps[propName] = externalPropValue;
          }
        }
      }
    }
    return mergedProps;
  }
  function isEventHandler(key, value) {
    const code0 = key.charCodeAt(0);
    const code1 = key.charCodeAt(1);
    const code2 = key.charCodeAt(2);
    return code0 === 111 && code1 === 110 && code2 >= 65 && code2 <= 90 && (typeof value === "function" || typeof value === "undefined");
  }
  function isPropsGetter(inputProps) {
    return typeof inputProps === "function";
  }
  function resolvePropsGetter(inputProps, previousProps) {
    if (isPropsGetter(inputProps)) {
      return inputProps(previousProps);
    }
    return inputProps ?? EMPTY_PROPS;
  }
  function mergeEventHandlers(ourHandler, theirHandler) {
    if (!theirHandler) {
      return ourHandler;
    }
    if (!ourHandler) {
      return wrapEventHandler(theirHandler);
    }
    return (...args) => {
      const event = args[0];
      if (isSyntheticEvent(event)) {
        const baseUIEvent = event;
        makeEventPreventable(baseUIEvent);
        const result2 = theirHandler(...args);
        if (!baseUIEvent.baseUIHandlerPrevented) {
          ourHandler?.(...args);
        }
        return result2;
      }
      const result = theirHandler(...args);
      ourHandler?.(...args);
      return result;
    };
  }
  function wrapEventHandler(handler) {
    if (!handler) {
      return handler;
    }
    return (...args) => {
      const event = args[0];
      if (isSyntheticEvent(event)) {
        makeEventPreventable(event);
      }
      return handler(...args);
    };
  }
  function makeEventPreventable(event) {
    event.preventBaseUIHandler = () => {
      event.baseUIHandlerPrevented = true;
    };
    return event;
  }
  function mergeClassNames(ourClassName, theirClassName) {
    if (theirClassName) {
      if (ourClassName) {
        return theirClassName + " " + ourClassName;
      }
      return theirClassName;
    }
    return ourClassName;
  }
  function isSyntheticEvent(event) {
    return event != null && typeof event === "object" && "nativeEvent" in event;
  }

  // node_modules/@base-ui/react/esm/internals/useRenderElement.js
  var import_react = __toESM(require_react(), 1);
  function useRenderElement(element, componentProps, params = {}) {
    const renderProp = componentProps.render;
    const outProps = useRenderElementProps(componentProps, params);
    if (params.enabled === false) {
      return null;
    }
    const state = params.state ?? EMPTY_OBJECT;
    return evaluateRenderProp(element, renderProp, outProps, state);
  }
  function useRenderElementProps(componentProps, params = {}) {
    const {
      className: classNameProp,
      style: styleProp,
      render: renderProp
    } = componentProps;
    const {
      state = EMPTY_OBJECT,
      ref,
      props,
      stateAttributesMapping,
      enabled = true
    } = params;
    const className = enabled ? resolveClassName(classNameProp, state) : void 0;
    const style = enabled ? resolveStyle(styleProp, state) : void 0;
    const stateProps = enabled ? getStateAttributesProps(state, stateAttributesMapping) : EMPTY_OBJECT;
    const resolvedProps = enabled && props ? resolveRenderFunctionProps(props) : void 0;
    const outProps = enabled ? mergeObjects(stateProps, resolvedProps) ?? {} : EMPTY_OBJECT;
    if (typeof document !== "undefined") {
      if (!enabled) {
        useMergedRefs(null, null);
      } else if (Array.isArray(ref)) {
        outProps.ref = useMergedRefsN([outProps.ref, getReactElementRef(renderProp), ...ref]);
      } else {
        outProps.ref = useMergedRefs(outProps.ref, getReactElementRef(renderProp), ref);
      }
    }
    if (!enabled) {
      return EMPTY_OBJECT;
    }
    if (className !== void 0) {
      outProps.className = mergeClassNames(outProps.className, className);
    }
    if (style !== void 0) {
      outProps.style = mergeObjects(outProps.style, style);
    }
    return outProps;
  }
  function resolveRenderFunctionProps(props) {
    if (Array.isArray(props)) {
      return mergePropsN(props);
    }
    return mergeProps(void 0, props);
  }
  var REACT_LAZY_TYPE = /* @__PURE__ */ Symbol.for("react.lazy");
  var COMPONENT_IDENTIFIER_PATTERN = /^[A-Z][A-Za-z0-9$]*$/;
  var LOWERCASE_CHARACTER_PATTERN = /[a-z]/;
  function evaluateRenderProp(element, render, props, state) {
    if (render) {
      if (typeof render === "function") {
        if (true) {
          warnIfRenderPropLooksLikeComponent(render);
        }
        return render(props, state);
      }
      const mergedProps = mergeProps(props, render.props);
      mergedProps.ref = props.ref;
      let newElement = render;
      if (newElement?.$$typeof === REACT_LAZY_TYPE) {
        const children = React4.Children.toArray(render);
        newElement = children[0];
      }
      if (true) {
        if (!/* @__PURE__ */ React4.isValidElement(newElement)) {
          throw new Error(["Base UI: The `render` prop was provided an invalid React element as `React.isValidElement(render)` is `false`.", "A valid React element must be provided to the `render` prop because it is cloned with props to replace the default element.", "https://base-ui.com/r/invalid-render-prop"].join("\n"));
        }
      }
      return /* @__PURE__ */ React4.cloneElement(newElement, mergedProps);
    }
    if (element) {
      if (typeof element === "string") {
        return renderTag(element, props);
      }
    }
    throw new Error(true ? "Base UI: Render element or function are not defined." : formatErrorMessage_default(8));
  }
  function warnIfRenderPropLooksLikeComponent(renderFn) {
    const functionName = renderFn.name;
    if (functionName.length === 0) {
      return;
    }
    if (!COMPONENT_IDENTIFIER_PATTERN.test(functionName)) {
      return;
    }
    if (!LOWERCASE_CHARACTER_PATTERN.test(functionName)) {
      return;
    }
    warn(`The \`render\` prop received a function named \`${functionName}\` that starts with an uppercase letter.`, "This usually means a React component was passed directly as `render={Component}`.", "Base UI calls `render` as a plain function, which can break the Rules of Hooks during reconciliation.", "If this is an intentional render callback, rename it to start with a lowercase letter.", "Use `render={<Component />}` or `render={(props) => <Component {...props} />}` instead.", "https://base-ui.com/r/invalid-render-prop");
  }
  function renderTag(Tag, props) {
    if (Tag === "button") {
      return /* @__PURE__ */ (0, import_react.createElement)("button", {
        type: "button",
        ...props,
        key: props.key
      });
    }
    if (Tag === "img") {
      return /* @__PURE__ */ (0, import_react.createElement)("img", {
        alt: "",
        ...props,
        key: props.key
      });
    }
    return /* @__PURE__ */ React4.createElement(Tag, props);
  }

  // node_modules/@base-ui/react/esm/use-render/useRender.js
  function useRender(params) {
    return useRenderElement(params.defaultTagName ?? "div", params, params);
  }

  // packages/icons/build-module/icon/index.mjs
  var import_element2 = __toESM(require_element(), 1);
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

  // packages/icons/build-module/library/star-empty.mjs
  var import_primitives = __toESM(require_primitives(), 1);
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
  var star_empty_default = /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime.jsx)(import_primitives.Path, { fillRule: "evenodd", clipRule: "evenodd", d: "M9.706 8.646a.25.25 0 01-.188.137l-4.626.672a.25.25 0 00-.139.427l3.348 3.262a.25.25 0 01.072.222l-.79 4.607a.25.25 0 00.362.264l4.138-2.176a.25.25 0 01.233 0l4.137 2.175a.25.25 0 00.363-.263l-.79-4.607a.25.25 0 01.072-.222l3.347-3.262a.25.25 0 00-.139-.427l-4.626-.672a.25.25 0 01-.188-.137l-2.069-4.192a.25.25 0 00-.448 0L9.706 8.646zM12 7.39l-.948 1.921a1.75 1.75 0 01-1.317.957l-2.12.308 1.534 1.495c.412.402.6.982.503 1.55l-.362 2.11 1.896-.997a1.75 1.75 0 011.629 0l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39z" }) });

  // packages/icons/build-module/library/star-filled.mjs
  var import_primitives2 = __toESM(require_primitives(), 1);
  var import_jsx_runtime2 = __toESM(require_jsx_runtime(), 1);
  var star_filled_default = /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime2.jsx)(import_primitives2.Path, { d: "M11.776 4.454a.25.25 0 01.448 0l2.069 4.192a.25.25 0 00.188.137l4.626.672a.25.25 0 01.139.426l-3.348 3.263a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.362.263l-4.138-2.175a.25.25 0 00-.232 0l-4.138 2.175a.25.25 0 01-.363-.263l.79-4.607a.25.25 0 00-.071-.222L4.754 9.881a.25.25 0 01.139-.426l4.626-.672a.25.25 0 00.188-.137l2.069-4.192z" }) });

  // packages/icons/build-module/library/star-half.mjs
  var import_primitives3 = __toESM(require_primitives(), 1);
  var import_jsx_runtime3 = __toESM(require_jsx_runtime(), 1);
  var star_half_default = /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.SVG, { xmlns: "http://www.w3.org/2000/svg", viewBox: "0 0 24 24", children: /* @__PURE__ */ (0, import_jsx_runtime3.jsx)(import_primitives3.Path, { d: "M9.518 8.783a.25.25 0 00.188-.137l2.069-4.192a.25.25 0 01.448 0l2.07 4.192a.25.25 0 00.187.137l4.626.672a.25.25 0 01.139.427l-3.347 3.262a.25.25 0 00-.072.222l.79 4.607a.25.25 0 01-.363.264l-4.137-2.176a.25.25 0 00-.233 0l-4.138 2.175a.25.25 0 01-.362-.263l.79-4.607a.25.25 0 00-.072-.222L4.753 9.882a.25.25 0 01.14-.427l4.625-.672zM12 14.533c.28 0 .559.067.814.2l1.895.997-.362-2.11a1.75 1.75 0 01.504-1.55l1.533-1.495-2.12-.308a1.75 1.75 0 01-1.317-.957L12 7.39v7.143z" }) });

  // packages/ui/build-module/visually-hidden/visually-hidden.mjs
  var import_element3 = __toESM(require_element(), 1);
  var STYLE_HASH_ATTRIBUTE = "data-wp-hash";
  function getRuntime() {
    const globalScope = globalThis;
    if (globalScope.__wpStyleRuntime) {
      return globalScope.__wpStyleRuntime;
    }
    globalScope.__wpStyleRuntime = {
      documents: /* @__PURE__ */ new Map(),
      styles: /* @__PURE__ */ new Map(),
      injectedStyles: /* @__PURE__ */ new WeakMap()
    };
    if (typeof document !== "undefined") {
      registerDocument(document);
    }
    return globalScope.__wpStyleRuntime;
  }
  function documentContainsStyleHash(targetDocument, hash) {
    if (!targetDocument.head) {
      return false;
    }
    for (const style of targetDocument.head.querySelectorAll(
      `style[${STYLE_HASH_ATTRIBUTE}]`
    )) {
      if (style.getAttribute(STYLE_HASH_ATTRIBUTE) === hash) {
        return true;
      }
    }
    return false;
  }
  function injectStyle(targetDocument, hash, css) {
    if (!targetDocument.head) {
      return;
    }
    const runtime = getRuntime();
    let injectedStyles = runtime.injectedStyles.get(targetDocument);
    if (!injectedStyles) {
      injectedStyles = /* @__PURE__ */ new Set();
      runtime.injectedStyles.set(targetDocument, injectedStyles);
    }
    if (injectedStyles.has(hash)) {
      return;
    }
    if (documentContainsStyleHash(targetDocument, hash)) {
      injectedStyles.add(hash);
      return;
    }
    const style = targetDocument.createElement("style");
    style.setAttribute(STYLE_HASH_ATTRIBUTE, hash);
    style.appendChild(targetDocument.createTextNode(css));
    targetDocument.head.appendChild(style);
    injectedStyles.add(hash);
  }
  function registerDocument(targetDocument) {
    const runtime = getRuntime();
    runtime.documents.set(
      targetDocument,
      (runtime.documents.get(targetDocument) ?? 0) + 1
    );
    for (const [hash, css] of runtime.styles) {
      injectStyle(targetDocument, hash, css);
    }
    return () => {
      const count = runtime.documents.get(targetDocument);
      if (count === void 0) {
        return;
      }
      if (count <= 1) {
        runtime.documents.delete(targetDocument);
        return;
      }
      runtime.documents.set(targetDocument, count - 1);
    };
  }
  function registerStyle(hash, css) {
    const runtime = getRuntime();
    runtime.styles.set(hash, css);
    for (const targetDocument of runtime.documents.keys()) {
      injectStyle(targetDocument, hash, css);
    }
  }
  if (typeof process === "undefined" || true) {
    registerStyle("c46e8cb841", "@layer wp-ui-utilities, wp-ui-components, wp-ui-compositions, wp-ui-overrides;@layer wp-ui-components{.f37b9e2e191ebd66__visually-hidden{word-wrap:normal;border:0;clip-path:inset(50%);height:1px;margin:-1px;overflow:hidden;padding:0;position:absolute;width:1px;word-break:normal}}");
  }
  var style_default = { "visually-hidden": "f37b9e2e191ebd66__visually-hidden" };
  var VisuallyHidden = (0, import_element3.forwardRef)(
    function VisuallyHidden2({ render, ...restProps }, ref) {
      const element = useRender({
        render,
        ref,
        props: mergeProps(
          { className: style_default["visually-hidden"] },
          restProps,
          {
            // @ts-expect-error Arbitrary data-* attributes aren't indexable on the typed div props. Kept hardcoded so consumers can't change or remove it.
            "data-visually-hidden": ""
          }
        )
      });
      return element;
    }
  );

  // packages/block-directory/build-module/components/block-ratings/stars.mjs
  var import_i18n2 = __toESM(require_i18n(), 1);
  var import_jsx_runtime4 = __toESM(require_jsx_runtime(), 1);
  function Stars({ rating }) {
    const stars = Math.round(rating / 0.5) * 0.5;
    const fullStarCount = Math.floor(rating);
    const halfStarCount = Math.ceil(rating - fullStarCount);
    const emptyStarCount = 5 - (fullStarCount + halfStarCount);
    return /* @__PURE__ */ (0, import_jsx_runtime4.jsxs)(
      "span",
      {
        "aria-label": (0, import_i18n2.sprintf)(
          /* translators: %s: number of stars. */
          (0, import_i18n2.__)("%s out of 5 stars"),
          stars
        ),
        children: [
          Array.from({ length: fullStarCount }).map((_, i) => /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
            icon_default,
            {
              className: "block-directory-block-ratings__star-full",
              icon: star_filled_default,
              size: 16
            },
            `full_stars_${i}`
          )),
          Array.from({ length: halfStarCount }).map((_, i) => /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
            icon_default,
            {
              className: "block-directory-block-ratings__star-half-full",
              icon: star_half_default,
              size: 16
            },
            `half_stars_${i}`
          )),
          Array.from({ length: emptyStarCount }).map((_, i) => /* @__PURE__ */ (0, import_jsx_runtime4.jsx)(
            icon_default,
            {
              className: "block-directory-block-ratings__star-empty",
              icon: star_empty_default,
              size: 16
            },
            `empty_stars_${i}`
          ))
        ]
      }
    );
  }
  var stars_default = Stars;

  // packages/block-directory/build-module/components/block-ratings/index.mjs
  var import_jsx_runtime5 = __toESM(require_jsx_runtime(), 1);
  var BlockRatings = ({ rating }) => /* @__PURE__ */ (0, import_jsx_runtime5.jsx)("span", { className: "block-directory-block-ratings", children: /* @__PURE__ */ (0, import_jsx_runtime5.jsx)(stars_default, { rating }) });
  var block_ratings_default = BlockRatings;

  // packages/block-directory/build-module/components/downloadable-block-icon/index.mjs
  var import_block_editor2 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime6 = __toESM(require_jsx_runtime(), 1);
  function DownloadableBlockIcon({ icon }) {
    const className = "block-directory-downloadable-block-icon";
    return icon.match(/\.(jpeg|jpg|gif|png|svg)(?:\?.*)?$/) !== null ? /* @__PURE__ */ (0, import_jsx_runtime6.jsx)("img", { className, src: icon, alt: "" }) : /* @__PURE__ */ (0, import_jsx_runtime6.jsx)(import_block_editor2.BlockIcon, { className, icon, showColors: true });
  }
  var downloadable_block_icon_default = DownloadableBlockIcon;

  // packages/block-directory/build-module/components/downloadable-block-notice/index.mjs
  var import_i18n3 = __toESM(require_i18n(), 1);
  var import_data5 = __toESM(require_data(), 1);
  var import_jsx_runtime7 = __toESM(require_jsx_runtime(), 1);
  var DownloadableBlockNotice = ({ block }) => {
    const errorNotice = (0, import_data5.useSelect)(
      (select) => select(store).getErrorNoticeForBlock(block.id),
      [block]
    );
    if (!errorNotice) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime7.jsx)("div", { className: "block-directory-downloadable-block-notice", children: /* @__PURE__ */ (0, import_jsx_runtime7.jsxs)("div", { className: "block-directory-downloadable-block-notice__content", children: [
      errorNotice.message,
      errorNotice.isFatal ? " " + (0, import_i18n3.__)("Try reloading the page.") : null
    ] }) });
  };
  var downloadable_block_notice_default = DownloadableBlockNotice;

  // packages/block-directory/build-module/components/downloadable-block-list-item/index.mjs
  var import_jsx_runtime8 = __toESM(require_jsx_runtime(), 1);
  function getDownloadableBlockLabel({ title, rating, ratingCount }, { hasNotice, isInstalled, isInstalling: isInstalling2 }) {
    const stars = Math.round(rating / 0.5) * 0.5;
    if (!isInstalled && hasNotice) {
      return (0, import_i18n4.sprintf)("Retry installing %s.", (0, import_html_entities.decodeEntities)(title));
    }
    if (isInstalled) {
      return (0, import_i18n4.sprintf)("Add %s.", (0, import_html_entities.decodeEntities)(title));
    }
    if (isInstalling2) {
      return (0, import_i18n4.sprintf)("Installing %s.", (0, import_html_entities.decodeEntities)(title));
    }
    if (ratingCount < 1) {
      return (0, import_i18n4.sprintf)("Install %s.", (0, import_html_entities.decodeEntities)(title));
    }
    return (0, import_i18n4.sprintf)(
      /* translators: 1: block title, 2: average rating, 3: total ratings count. */
      (0, import_i18n4._n)(
        "Install %1$s. %2$s stars with %3$s review.",
        "Install %1$s. %2$s stars with %3$s reviews.",
        ratingCount
      ),
      (0, import_html_entities.decodeEntities)(title),
      stars,
      ratingCount
    );
  }
  function DownloadableBlockListItem({ item, onClick }) {
    const { author, description, icon, rating, title } = item;
    const isInstalled = !!(0, import_blocks3.getBlockType)(item.name);
    const { hasNotice, isInstalling: isInstalling2, isInstallable } = (0, import_data6.useSelect)(
      (select) => {
        const { getErrorNoticeForBlock: getErrorNoticeForBlock2, isInstalling: isBlockInstalling } = select(store);
        const notice = getErrorNoticeForBlock2(item.id);
        const hasFatal = notice && notice.isFatal;
        return {
          hasNotice: !!notice,
          isInstalling: isBlockInstalling(item.id),
          isInstallable: !hasFatal
        };
      },
      [item]
    );
    let statusText = "";
    if (isInstalled) {
      statusText = (0, import_i18n4.__)("Installed!");
    } else if (isInstalling2) {
      statusText = (0, import_i18n4.__)("Installing\u2026");
    }
    const itemLabel = getDownloadableBlockLabel(item, {
      hasNotice,
      isInstalled,
      isInstalling: isInstalling2
    });
    return /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_components.Tooltip, { placement: "top", text: itemLabel, children: /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)(
      import_components.Composite.Item,
      {
        className: clsx_default(
          "block-directory-downloadable-block-list-item",
          isInstalling2 && "is-installing"
        ),
        accessibleWhenDisabled: true,
        disabled: isInstalling2 || !isInstallable,
        onClick: (event) => {
          event.preventDefault();
          onClick();
        },
        "aria-label": itemLabel,
        type: "button",
        role: "option",
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)("div", { className: "block-directory-downloadable-block-list-item__icon", children: [
            /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(downloadable_block_icon_default, { icon, title }),
            isInstalling2 ? /* @__PURE__ */ (0, import_jsx_runtime8.jsx)("span", { className: "block-directory-downloadable-block-list-item__spinner", children: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(import_components.Spinner, {}) }) : /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(block_ratings_default, { rating })
          ] }),
          /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)("span", { className: "block-directory-downloadable-block-list-item__details", children: [
            /* @__PURE__ */ (0, import_jsx_runtime8.jsx)("span", { className: "block-directory-downloadable-block-list-item__title", children: (0, import_element4.createInterpolateElement)(
              (0, import_i18n4.sprintf)(
                /* translators: 1: block title. 2: author name. */
                (0, import_i18n4.__)("%1$s <span>by %2$s</span>"),
                (0, import_html_entities.decodeEntities)(title),
                author
              ),
              {
                span: /* @__PURE__ */ (0, import_jsx_runtime8.jsx)("span", { className: "block-directory-downloadable-block-list-item__author" })
              }
            ) }),
            hasNotice ? /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(downloadable_block_notice_default, { block: item }) : /* @__PURE__ */ (0, import_jsx_runtime8.jsxs)(import_jsx_runtime8.Fragment, { children: [
              /* @__PURE__ */ (0, import_jsx_runtime8.jsx)("span", { className: "block-directory-downloadable-block-list-item__desc", children: !!statusText ? statusText : (0, import_html_entities.decodeEntities)(description) }),
              isInstallable && !(isInstalled || isInstalling2) && /* @__PURE__ */ (0, import_jsx_runtime8.jsx)(VisuallyHidden, { children: (0, import_i18n4.__)("Install block") })
            ] })
          ] })
        ]
      }
    ) });
  }
  var downloadable_block_list_item_default = DownloadableBlockListItem;

  // packages/block-directory/build-module/components/downloadable-blocks-list/index.mjs
  var import_jsx_runtime9 = __toESM(require_jsx_runtime(), 1);
  var noop = () => {
  };
  function DownloadableBlocksList({ items, onHover = noop, onSelect }) {
    const { installBlockType: installBlockType2 } = (0, import_data7.useDispatch)(store);
    if (!items.length) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
      import_components2.Composite,
      {
        role: "listbox",
        className: "block-directory-downloadable-blocks-list",
        "aria-label": (0, import_i18n5.__)("Blocks available for install"),
        children: items.map((item) => {
          return /* @__PURE__ */ (0, import_jsx_runtime9.jsx)(
            downloadable_block_list_item_default,
            {
              onClick: () => {
                if ((0, import_blocks4.getBlockType)(item.name)) {
                  onSelect(item);
                } else {
                  installBlockType2(item).then((success) => {
                    if (success) {
                      onSelect(item);
                    }
                  });
                }
                onHover(null);
              },
              onHover,
              item
            },
            item.id
          );
        })
      }
    );
  }
  var downloadable_blocks_list_default = DownloadableBlocksList;

  // packages/block-directory/build-module/components/downloadable-blocks-panel/inserter-panel.mjs
  var import_i18n6 = __toESM(require_i18n(), 1);
  var import_element5 = __toESM(require_element(), 1);
  var import_a11y = __toESM(require_a11y(), 1);
  var import_jsx_runtime10 = __toESM(require_jsx_runtime(), 1);
  function DownloadableBlocksInserterPanel({
    children,
    downloadableItems,
    hasLocalBlocks
  }) {
    const count = downloadableItems.length;
    (0, import_element5.useEffect)(() => {
      (0, import_a11y.speak)(
        (0, import_i18n6.sprintf)(
          /* translators: %d: number of available blocks. */
          (0, import_i18n6._n)(
            "%d additional block is available to install.",
            "%d additional blocks are available to install.",
            count
          ),
          count
        )
      );
    }, [count]);
    return /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)(import_jsx_runtime10.Fragment, { children: [
      !hasLocalBlocks && /* @__PURE__ */ (0, import_jsx_runtime10.jsx)("p", { className: "block-directory-downloadable-blocks-panel__no-local", children: (0, import_i18n6.__)("No results available from your installed blocks.") }),
      /* @__PURE__ */ (0, import_jsx_runtime10.jsx)("div", { className: "block-editor-inserter__quick-inserter-separator" }),
      /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)("div", { className: "block-directory-downloadable-blocks-panel", children: [
        /* @__PURE__ */ (0, import_jsx_runtime10.jsxs)("div", { className: "block-directory-downloadable-blocks-panel__header", children: [
          /* @__PURE__ */ (0, import_jsx_runtime10.jsx)("h2", { className: "block-directory-downloadable-blocks-panel__title", children: (0, import_i18n6.__)("Available to install") }),
          /* @__PURE__ */ (0, import_jsx_runtime10.jsx)("p", { className: "block-directory-downloadable-blocks-panel__description", children: (0, import_i18n6.__)(
            "Select a block to install and add it to your post."
          ) })
        ] }),
        children
      ] })
    ] });
  }
  var inserter_panel_default = DownloadableBlocksInserterPanel;

  // packages/block-directory/build-module/components/downloadable-blocks-panel/no-results.mjs
  var import_i18n7 = __toESM(require_i18n(), 1);
  var import_components3 = __toESM(require_components(), 1);
  var import_jsx_runtime11 = __toESM(require_jsx_runtime(), 1);
  function DownloadableBlocksNoResults() {
    return /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(import_jsx_runtime11.Fragment, { children: [
      /* @__PURE__ */ (0, import_jsx_runtime11.jsx)("div", { className: "block-editor-inserter__no-results", children: /* @__PURE__ */ (0, import_jsx_runtime11.jsx)("p", { children: (0, import_i18n7.__)("No results found.") }) }),
      /* @__PURE__ */ (0, import_jsx_runtime11.jsx)("div", { className: "block-editor-inserter__tips", children: /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(import_components3.Tip, { children: [
        (0, import_i18n7.__)("Interested in creating your own block?"),
        /* @__PURE__ */ (0, import_jsx_runtime11.jsx)("br", {}),
        /* @__PURE__ */ (0, import_jsx_runtime11.jsxs)(import_components3.ExternalLink, { href: "https://developer.wordpress.org/block-editor/", children: [
          (0, import_i18n7.__)("Get started here"),
          "."
        ] })
      ] }) })
    ] });
  }
  var no_results_default = DownloadableBlocksNoResults;

  // packages/block-directory/build-module/components/downloadable-blocks-panel/index.mjs
  var import_jsx_runtime12 = __toESM(require_jsx_runtime(), 1);
  var EMPTY_ARRAY3 = [];
  var useDownloadableBlocks = (filterValue) => (0, import_data8.useSelect)(
    (select) => {
      const {
        getDownloadableBlocks: getDownloadableBlocks3,
        isRequestingDownloadableBlocks: isRequestingDownloadableBlocks2,
        getInstalledBlockTypes: getInstalledBlockTypes2
      } = select(store);
      const hasPermission = select(import_core_data.store).canUser(
        "read",
        "block-directory/search"
      );
      let downloadableBlocks2 = EMPTY_ARRAY3;
      if (hasPermission) {
        downloadableBlocks2 = getDownloadableBlocks3(filterValue);
        const installedBlockTypes = getInstalledBlockTypes2();
        const installableBlocks = downloadableBlocks2.filter(
          ({ name }) => {
            const isJustInstalled = installedBlockTypes.some(
              (blockType) => blockType.name === name
            );
            const isPreviouslyInstalled = (0, import_blocks5.getBlockType)(name);
            return isJustInstalled || !isPreviouslyInstalled;
          }
        );
        if (installableBlocks.length !== downloadableBlocks2.length) {
          downloadableBlocks2 = installableBlocks;
        }
        if (downloadableBlocks2.length === 0) {
          downloadableBlocks2 = EMPTY_ARRAY3;
        }
      }
      return {
        hasPermission,
        downloadableBlocks: downloadableBlocks2,
        isLoading: isRequestingDownloadableBlocks2(filterValue)
      };
    },
    [filterValue]
  );
  function DownloadableBlocksPanel({
    onSelect,
    onHover,
    hasLocalBlocks,
    isTyping,
    filterValue
  }) {
    const { hasPermission, downloadableBlocks: downloadableBlocks2, isLoading } = useDownloadableBlocks(filterValue);
    if (hasPermission === void 0 || isLoading || isTyping) {
      return /* @__PURE__ */ (0, import_jsx_runtime12.jsxs)(import_jsx_runtime12.Fragment, { children: [
        hasPermission && !hasLocalBlocks && /* @__PURE__ */ (0, import_jsx_runtime12.jsxs)(import_jsx_runtime12.Fragment, { children: [
          /* @__PURE__ */ (0, import_jsx_runtime12.jsx)("p", { className: "block-directory-downloadable-blocks-panel__no-local", children: (0, import_i18n8.__)(
            "No results available from your installed blocks."
          ) }),
          /* @__PURE__ */ (0, import_jsx_runtime12.jsx)("div", { className: "block-editor-inserter__quick-inserter-separator" })
        ] }),
        /* @__PURE__ */ (0, import_jsx_runtime12.jsx)("div", { className: "block-directory-downloadable-blocks-panel has-blocks-loading", children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(import_components4.Spinner, {}) })
      ] });
    }
    if (false === hasPermission) {
      if (!hasLocalBlocks) {
        return /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(no_results_default, {});
      }
      return null;
    }
    if (downloadableBlocks2.length === 0) {
      return hasLocalBlocks ? null : /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(no_results_default, {});
    }
    return /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
      inserter_panel_default,
      {
        downloadableItems: downloadableBlocks2,
        hasLocalBlocks,
        children: /* @__PURE__ */ (0, import_jsx_runtime12.jsx)(
          downloadable_blocks_list_default,
          {
            items: downloadableBlocks2,
            onSelect,
            onHover
          }
        )
      }
    );
  }

  // packages/block-directory/build-module/plugins/inserter-menu-downloadable-blocks-panel/index.mjs
  var import_jsx_runtime13 = __toESM(require_jsx_runtime(), 1);
  function InserterMenuDownloadableBlocksPanel() {
    const [debouncedFilterValue, setFilterValue] = (0, import_element6.useState)("");
    const debouncedSetFilterValue = (0, import_compose.debounce)(setFilterValue, 400);
    return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(import_block_editor3.__unstableInserterMenuExtension, { children: ({ onSelect, onHover, filterValue, hasItems }) => {
      if (debouncedFilterValue !== filterValue) {
        debouncedSetFilterValue(filterValue);
      }
      if (!debouncedFilterValue) {
        return null;
      }
      return /* @__PURE__ */ (0, import_jsx_runtime13.jsx)(
        DownloadableBlocksPanel,
        {
          onSelect,
          onHover,
          filterValue: debouncedFilterValue,
          hasLocalBlocks: hasItems,
          isTyping: filterValue !== debouncedFilterValue
        }
      );
    } });
  }
  var inserter_menu_downloadable_blocks_panel_default = InserterMenuDownloadableBlocksPanel;

  // packages/block-directory/build-module/plugins/installed-blocks-pre-publish-panel/index.mjs
  var import_i18n10 = __toESM(require_i18n(), 1);
  var import_data9 = __toESM(require_data(), 1);
  var import_editor2 = __toESM(require_editor(), 1);

  // packages/block-directory/build-module/components/compact-list/index.mjs
  var import_i18n9 = __toESM(require_i18n(), 1);
  var import_jsx_runtime14 = __toESM(require_jsx_runtime(), 1);
  function CompactList({ items }) {
    if (!items.length) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime14.jsx)("ul", { className: "block-directory-compact-list", children: items.map(({ icon, id, title, author }) => /* @__PURE__ */ (0, import_jsx_runtime14.jsxs)("li", { className: "block-directory-compact-list__item", children: [
      /* @__PURE__ */ (0, import_jsx_runtime14.jsx)(downloadable_block_icon_default, { icon, title }),
      /* @__PURE__ */ (0, import_jsx_runtime14.jsxs)("div", { className: "block-directory-compact-list__item-details", children: [
        /* @__PURE__ */ (0, import_jsx_runtime14.jsx)("div", { className: "block-directory-compact-list__item-title", children: title }),
        /* @__PURE__ */ (0, import_jsx_runtime14.jsx)("div", { className: "block-directory-compact-list__item-author", children: (0, import_i18n9.sprintf)(
          /* translators: %s: Name of the block author. */
          (0, import_i18n9.__)("By %s"),
          author
        ) })
      ] })
    ] }, id)) });
  }

  // packages/block-directory/build-module/plugins/installed-blocks-pre-publish-panel/index.mjs
  var import_jsx_runtime15 = __toESM(require_jsx_runtime(), 1);
  function InstalledBlocksPrePublishPanel() {
    const newBlockTypes = (0, import_data9.useSelect)(
      (select) => select(store).getNewBlockTypes(),
      []
    );
    if (!newBlockTypes.length) {
      return null;
    }
    return /* @__PURE__ */ (0, import_jsx_runtime15.jsxs)(
      import_editor2.PluginPrePublishPanel,
      {
        title: (0, import_i18n10.sprintf)(
          // translators: %d: number of blocks (number).
          (0, import_i18n10._n)(
            "Added: %d block",
            "Added: %d blocks",
            newBlockTypes.length
          ),
          newBlockTypes.length
        ),
        initialOpen: true,
        children: [
          /* @__PURE__ */ (0, import_jsx_runtime15.jsx)("p", { className: "installed-blocks-pre-publish-panel__copy", children: (0, import_i18n10._n)(
            "The following block has been added to your site.",
            "The following blocks have been added to your site.",
            newBlockTypes.length
          ) }),
          /* @__PURE__ */ (0, import_jsx_runtime15.jsx)(CompactList, { items: newBlockTypes })
        ]
      }
    );
  }

  // packages/block-directory/build-module/plugins/get-install-missing/index.mjs
  var import_i18n12 = __toESM(require_i18n(), 1);
  var import_components6 = __toESM(require_components(), 1);
  var import_blocks7 = __toESM(require_blocks(), 1);
  var import_element7 = __toESM(require_element(), 1);
  var import_data11 = __toESM(require_data(), 1);
  var import_core_data2 = __toESM(require_core_data(), 1);
  var import_block_editor5 = __toESM(require_block_editor(), 1);

  // packages/block-directory/build-module/plugins/get-install-missing/install-button.mjs
  var import_i18n11 = __toESM(require_i18n(), 1);
  var import_components5 = __toESM(require_components(), 1);
  var import_blocks6 = __toESM(require_blocks(), 1);
  var import_data10 = __toESM(require_data(), 1);
  var import_block_editor4 = __toESM(require_block_editor(), 1);
  var import_jsx_runtime16 = __toESM(require_jsx_runtime(), 1);
  function InstallButton({ attributes, block, clientId }) {
    const isInstallingBlock = (0, import_data10.useSelect)(
      (select) => select(store).isInstalling(block.id),
      [block.id]
    );
    const { installBlockType: installBlockType2 } = (0, import_data10.useDispatch)(store);
    const { replaceBlock } = (0, import_data10.useDispatch)(import_block_editor4.store);
    return /* @__PURE__ */ (0, import_jsx_runtime16.jsx)(
      import_components5.Button,
      {
        __next40pxDefaultSize: true,
        onClick: () => installBlockType2(block).then((success) => {
          if (success) {
            const blockType = (0, import_blocks6.getBlockType)(block.name);
            const [originalBlock] = (0, import_blocks6.parse)(
              attributes.originalContent
            );
            if (originalBlock && blockType) {
              replaceBlock(
                clientId,
                (0, import_blocks6.createBlock)(
                  blockType.name,
                  originalBlock.attributes,
                  originalBlock.innerBlocks
                )
              );
            }
          }
        }),
        accessibleWhenDisabled: true,
        disabled: isInstallingBlock,
        isBusy: isInstallingBlock,
        variant: "primary",
        children: (0, import_i18n11.sprintf)(
          /* translators: %s: block name */
          (0, import_i18n11.__)("Install %s"),
          block.title
        )
      }
    );
  }

  // packages/block-directory/build-module/plugins/get-install-missing/index.mjs
  var import_jsx_runtime17 = __toESM(require_jsx_runtime(), 1);
  var getInstallMissing = (OriginalComponent) => (props) => {
    const { originalName } = props.attributes;
    const { block, hasPermission } = (0, import_data11.useSelect)(
      (select) => {
        const { getDownloadableBlocks: getDownloadableBlocks3 } = select(store);
        const blocks = getDownloadableBlocks3(
          "block:" + originalName
        ).filter(({ name }) => originalName === name);
        return {
          hasPermission: select(import_core_data2.store).canUser(
            "read",
            "block-directory/search"
          ),
          block: blocks.length && blocks[0]
        };
      },
      [originalName]
    );
    if (!hasPermission || !block) {
      return /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(OriginalComponent, { ...props });
    }
    return /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(ModifiedWarning, { ...props, originalBlock: block });
  };
  var ModifiedWarning = ({ originalBlock, ...props }) => {
    const { originalName, originalUndelimitedContent, clientId } = props.attributes;
    const { replaceBlock } = (0, import_data11.useDispatch)(import_block_editor5.store);
    const convertToHTML = () => {
      replaceBlock(
        props.clientId,
        (0, import_blocks7.createBlock)("core/html", {
          content: originalUndelimitedContent
        })
      );
    };
    const hasContent = !!originalUndelimitedContent;
    const hasHTMLBlock = (0, import_data11.useSelect)(
      (select) => {
        const { canInsertBlockType, getBlockRootClientId } = select(import_block_editor5.store);
        return canInsertBlockType(
          "core/html",
          getBlockRootClientId(clientId)
        );
      },
      [clientId]
    );
    let messageHTML = (0, import_i18n12.sprintf)(
      /* translators: %s: block name */
      (0, import_i18n12.__)(
        "Your site doesn\u2019t include support for the %s block. You can try installing the block or remove it entirely."
      ),
      originalBlock.title || originalName
    );
    const actions = [
      /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
        InstallButton,
        {
          block: originalBlock,
          attributes: props.attributes,
          clientId: props.clientId
        },
        "install"
      )
    ];
    if (hasContent && hasHTMLBlock) {
      messageHTML = (0, import_i18n12.sprintf)(
        /* translators: %s: block name */
        (0, import_i18n12.__)(
          "Your site doesn\u2019t include support for the %s block. You can try installing the block, convert it to a Custom HTML block, or remove it entirely."
        ),
        originalBlock.title || originalName
      );
      actions.push(
        /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(
          import_components6.Button,
          {
            __next40pxDefaultSize: true,
            onClick: convertToHTML,
            variant: "tertiary",
            children: (0, import_i18n12.__)("Keep as HTML")
          },
          "convert"
        )
      );
    }
    return /* @__PURE__ */ (0, import_jsx_runtime17.jsxs)("div", { ...(0, import_block_editor5.useBlockProps)(), children: [
      /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_block_editor5.Warning, { actions, children: messageHTML }),
      /* @__PURE__ */ (0, import_jsx_runtime17.jsx)(import_element7.RawHTML, { children: originalUndelimitedContent })
    ] });
  };
  var get_install_missing_default = getInstallMissing;

  // packages/block-directory/build-module/plugins/index.mjs
  var import_jsx_runtime18 = __toESM(require_jsx_runtime(), 1);
  (0, import_plugins.registerPlugin)("block-directory", {
    // The icon is explicitly set to undefined to prevent PluginPrePublishPanel
    // from rendering the fallback icon pluginIcon.
    icon: void 0,
    render() {
      return /* @__PURE__ */ (0, import_jsx_runtime18.jsxs)(import_jsx_runtime18.Fragment, { children: [
        /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(AutoBlockUninstaller, {}),
        /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(inserter_menu_downloadable_blocks_panel_default, {}),
        /* @__PURE__ */ (0, import_jsx_runtime18.jsx)(InstalledBlocksPrePublishPanel, {})
      ] });
    }
  });
  (0, import_hooks.addFilter)(
    "blocks.registerBlockType",
    "block-directory/fallback",
    (settings, name) => {
      if (name !== "core/missing") {
        return settings;
      }
      settings.edit = get_install_missing_default(settings.edit);
      return settings;
    }
  );
  return __toCommonJS(index_exports);
})();
