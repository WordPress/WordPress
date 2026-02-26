"use strict";
var wp;
(wp ||= {}).coreData = (() => {
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

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // node_modules/fast-deep-equal/es6/index.js
  var require_es6 = __commonJS({
    "node_modules/fast-deep-equal/es6/index.js"(exports, module) {
      "use strict";
      module.exports = function equal(a, b) {
        if (a === b) return true;
        if (a && b && typeof a == "object" && typeof b == "object") {
          if (a.constructor !== b.constructor) return false;
          var length, i, keys;
          if (Array.isArray(a)) {
            length = a.length;
            if (length != b.length) return false;
            for (i = length; i-- !== 0; )
              if (!equal(a[i], b[i])) return false;
            return true;
          }
          if (a instanceof Map && b instanceof Map) {
            if (a.size !== b.size) return false;
            for (i of a.entries())
              if (!b.has(i[0])) return false;
            for (i of a.entries())
              if (!equal(i[1], b.get(i[0]))) return false;
            return true;
          }
          if (a instanceof Set && b instanceof Set) {
            if (a.size !== b.size) return false;
            for (i of a.entries())
              if (!b.has(i[0])) return false;
            return true;
          }
          if (ArrayBuffer.isView(a) && ArrayBuffer.isView(b)) {
            length = a.length;
            if (length != b.length) return false;
            for (i = length; i-- !== 0; )
              if (a[i] !== b[i]) return false;
            return true;
          }
          if (a.constructor === RegExp) return a.source === b.source && a.flags === b.flags;
          if (a.valueOf !== Object.prototype.valueOf) return a.valueOf() === b.valueOf();
          if (a.toString !== Object.prototype.toString) return a.toString() === b.toString();
          keys = Object.keys(a);
          length = keys.length;
          if (length !== Object.keys(b).length) return false;
          for (i = length; i-- !== 0; )
            if (!Object.prototype.hasOwnProperty.call(b, keys[i])) return false;
          for (i = length; i-- !== 0; ) {
            var key = keys[i];
            if (!equal(a[key], b[key])) return false;
          }
          return true;
        }
        return a !== a && b !== b;
      };
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/undo-manager
  var require_undo_manager = __commonJS({
    "package-external:@wordpress/undo-manager"(exports, module) {
      module.exports = window.wp.undoManager;
    }
  });

  // node_modules/equivalent-key-map/equivalent-key-map.js
  var require_equivalent_key_map = __commonJS({
    "node_modules/equivalent-key-map/equivalent-key-map.js"(exports, module) {
      "use strict";
      function _typeof(obj) {
        if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
          _typeof = function(obj2) {
            return typeof obj2;
          };
        } else {
          _typeof = function(obj2) {
            return obj2 && typeof Symbol === "function" && obj2.constructor === Symbol && obj2 !== Symbol.prototype ? "symbol" : typeof obj2;
          };
        }
        return _typeof(obj);
      }
      function _classCallCheck(instance, Constructor) {
        if (!(instance instanceof Constructor)) {
          throw new TypeError("Cannot call a class as a function");
        }
      }
      function _defineProperties(target, props) {
        for (var i = 0; i < props.length; i++) {
          var descriptor = props[i];
          descriptor.enumerable = descriptor.enumerable || false;
          descriptor.configurable = true;
          if ("value" in descriptor) descriptor.writable = true;
          Object.defineProperty(target, descriptor.key, descriptor);
        }
      }
      function _createClass(Constructor, protoProps, staticProps) {
        if (protoProps) _defineProperties(Constructor.prototype, protoProps);
        if (staticProps) _defineProperties(Constructor, staticProps);
        return Constructor;
      }
      function getValuePair(instance, key) {
        var _map = instance._map, _arrayTreeMap = instance._arrayTreeMap, _objectTreeMap = instance._objectTreeMap;
        if (_map.has(key)) {
          return _map.get(key);
        }
        var properties = Object.keys(key).sort();
        var map = Array.isArray(key) ? _arrayTreeMap : _objectTreeMap;
        for (var i = 0; i < properties.length; i++) {
          var property = properties[i];
          map = map.get(property);
          if (map === void 0) {
            return;
          }
          var propertyValue = key[property];
          map = map.get(propertyValue);
          if (map === void 0) {
            return;
          }
        }
        var valuePair = map.get("_ekm_value");
        if (!valuePair) {
          return;
        }
        _map.delete(valuePair[0]);
        valuePair[0] = key;
        map.set("_ekm_value", valuePair);
        _map.set(key, valuePair);
        return valuePair;
      }
      var EquivalentKeyMap2 = /* @__PURE__ */ (function() {
        function EquivalentKeyMap3(iterable) {
          _classCallCheck(this, EquivalentKeyMap3);
          this.clear();
          if (iterable instanceof EquivalentKeyMap3) {
            var iterablePairs = [];
            iterable.forEach(function(value, key) {
              iterablePairs.push([key, value]);
            });
            iterable = iterablePairs;
          }
          if (iterable != null) {
            for (var i = 0; i < iterable.length; i++) {
              this.set(iterable[i][0], iterable[i][1]);
            }
          }
        }
        _createClass(EquivalentKeyMap3, [{
          key: "set",
          /**
           * Add or update an element with a specified key and value.
           *
           * @param {*} key   The key of the element to add.
           * @param {*} value The value of the element to add.
           *
           * @return {EquivalentKeyMap} Map instance.
           */
          value: function set(key, value) {
            if (key === null || _typeof(key) !== "object") {
              this._map.set(key, value);
              return this;
            }
            var properties = Object.keys(key).sort();
            var valuePair = [key, value];
            var map = Array.isArray(key) ? this._arrayTreeMap : this._objectTreeMap;
            for (var i = 0; i < properties.length; i++) {
              var property = properties[i];
              if (!map.has(property)) {
                map.set(property, new EquivalentKeyMap3());
              }
              map = map.get(property);
              var propertyValue = key[property];
              if (!map.has(propertyValue)) {
                map.set(propertyValue, new EquivalentKeyMap3());
              }
              map = map.get(propertyValue);
            }
            var previousValuePair = map.get("_ekm_value");
            if (previousValuePair) {
              this._map.delete(previousValuePair[0]);
            }
            map.set("_ekm_value", valuePair);
            this._map.set(key, valuePair);
            return this;
          }
          /**
           * Returns a specified element.
           *
           * @param {*} key The key of the element to return.
           *
           * @return {?*} The element associated with the specified key or undefined
           *              if the key can't be found.
           */
        }, {
          key: "get",
          value: function get(key) {
            if (key === null || _typeof(key) !== "object") {
              return this._map.get(key);
            }
            var valuePair = getValuePair(this, key);
            if (valuePair) {
              return valuePair[1];
            }
          }
          /**
           * Returns a boolean indicating whether an element with the specified key
           * exists or not.
           *
           * @param {*} key The key of the element to test for presence.
           *
           * @return {boolean} Whether an element with the specified key exists.
           */
        }, {
          key: "has",
          value: function has(key) {
            if (key === null || _typeof(key) !== "object") {
              return this._map.has(key);
            }
            return getValuePair(this, key) !== void 0;
          }
          /**
           * Removes the specified element.
           *
           * @param {*} key The key of the element to remove.
           *
           * @return {boolean} Returns true if an element existed and has been
           *                   removed, or false if the element does not exist.
           */
        }, {
          key: "delete",
          value: function _delete(key) {
            if (!this.has(key)) {
              return false;
            }
            this.set(key, void 0);
            return true;
          }
          /**
           * Executes a provided function once per each key/value pair, in insertion
           * order.
           *
           * @param {Function} callback Function to execute for each element.
           * @param {*}        thisArg  Value to use as `this` when executing
           *                            `callback`.
           */
        }, {
          key: "forEach",
          value: function forEach(callback) {
            var _this = this;
            var thisArg = arguments.length > 1 && arguments[1] !== void 0 ? arguments[1] : this;
            this._map.forEach(function(value, key) {
              if (key !== null && _typeof(key) === "object") {
                value = value[1];
              }
              callback.call(thisArg, value, key, _this);
            });
          }
          /**
           * Removes all elements.
           */
        }, {
          key: "clear",
          value: function clear() {
            this._map = /* @__PURE__ */ new Map();
            this._arrayTreeMap = /* @__PURE__ */ new Map();
            this._objectTreeMap = /* @__PURE__ */ new Map();
          }
        }, {
          key: "size",
          get: function get() {
            return this._map.size;
          }
        }]);
        return EquivalentKeyMap3;
      })();
      module.exports = EquivalentKeyMap2;
    }
  });

  // package-external:@wordpress/url
  var require_url = __commonJS({
    "package-external:@wordpress/url"(exports, module) {
      module.exports = window.wp.url;
    }
  });

  // package-external:@wordpress/api-fetch
  var require_api_fetch = __commonJS({
    "package-external:@wordpress/api-fetch"(exports, module) {
      module.exports = window.wp.apiFetch;
    }
  });

  // package-external:@wordpress/blocks
  var require_blocks = __commonJS({
    "package-external:@wordpress/blocks"(exports, module) {
      module.exports = window.wp.blocks;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/sync
  var require_sync = __commonJS({
    "package-external:@wordpress/sync"(exports, module) {
      module.exports = window.wp.sync;
    }
  });

  // package-external:@wordpress/block-editor
  var require_block_editor = __commonJS({
    "package-external:@wordpress/block-editor"(exports, module) {
      module.exports = window.wp.blockEditor;
    }
  });

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // package-external:@wordpress/rich-text
  var require_rich_text = __commonJS({
    "package-external:@wordpress/rich-text"(exports, module) {
      module.exports = window.wp.richText;
    }
  });

  // package-external:@wordpress/deprecated
  var require_deprecated = __commonJS({
    "package-external:@wordpress/deprecated"(exports, module) {
      module.exports = window.wp.deprecated;
    }
  });

  // package-external:@wordpress/html-entities
  var require_html_entities = __commonJS({
    "package-external:@wordpress/html-entities"(exports, module) {
      module.exports = window.wp.htmlEntities;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // vendor-external:react/jsx-runtime
  var require_jsx_runtime = __commonJS({
    "vendor-external:react/jsx-runtime"(exports, module) {
      module.exports = window.ReactJSXRuntime;
    }
  });

  // package-external:@wordpress/warning
  var require_warning = __commonJS({
    "package-external:@wordpress/warning"(exports, module) {
      module.exports = window.wp.warning;
    }
  });

  // packages/core-data/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    EntityProvider: () => EntityProvider,
    SelectionType: () => SelectionType,
    __experimentalFetchLinkSuggestions: () => fetchLinkSuggestions,
    __experimentalFetchUrlData: () => experimental_fetch_url_data_default,
    __experimentalUseEntityRecord: () => __experimentalUseEntityRecord,
    __experimentalUseEntityRecords: () => __experimentalUseEntityRecords,
    __experimentalUseResourcePermissions: () => __experimentalUseResourcePermissions,
    fetchBlockPatterns: () => fetchBlockPatterns,
    privateApis: () => privateApis,
    store: () => store,
    useEntityBlockEditor: () => useEntityBlockEditor,
    useEntityId: () => useEntityId,
    useEntityProp: () => useEntityProp,
    useEntityRecord: () => useEntityRecord,
    useEntityRecords: () => useEntityRecords,
    useResourcePermissions: () => use_resource_permissions_default
  });
  var import_data14 = __toESM(require_data(), 1);

  // packages/core-data/build-module/reducer.mjs
  var import_es64 = __toESM(require_es6(), 1);
  var import_compose2 = __toESM(require_compose(), 1);
  var import_data6 = __toESM(require_data(), 1);
  var import_undo_manager = __toESM(require_undo_manager(), 1);

  // packages/core-data/build-module/utils/conservative-map-item.mjs
  var import_es6 = __toESM(require_es6(), 1);
  function conservativeMapItem(item, nextItem) {
    if (!item) {
      return nextItem;
    }
    let hasChanges = false;
    const result = {};
    for (const key in nextItem) {
      if ((0, import_es6.default)(item[key], nextItem[key])) {
        result[key] = item[key];
      } else {
        hasChanges = true;
        result[key] = nextItem[key];
      }
    }
    if (!hasChanges) {
      return item;
    }
    for (const key in item) {
      if (!result.hasOwnProperty(key)) {
        result[key] = item[key];
      }
    }
    return result;
  }

  // packages/core-data/build-module/utils/get-normalized-comma-separable.mjs
  function getNormalizedCommaSeparable(value) {
    if (typeof value === "string") {
      return value.split(",");
    } else if (Array.isArray(value)) {
      return value;
    }
    return null;
  }
  var get_normalized_comma_separable_default = getNormalizedCommaSeparable;

  // packages/core-data/build-module/utils/if-matching-action.mjs
  var ifMatchingAction = (isMatch) => (reducer) => (state, action) => {
    if (state === void 0 || isMatch(action)) {
      return reducer(state, action);
    }
    return state;
  };
  var if_matching_action_default = ifMatchingAction;

  // packages/core-data/build-module/utils/forward-resolver.mjs
  var forwardResolver = (resolverName) => (...args) => async ({ resolveSelect: resolveSelect2 }) => {
    await resolveSelect2[resolverName](...args);
  };
  var forward_resolver_default = forwardResolver;

  // packages/core-data/build-module/utils/on-sub-key.mjs
  var onSubKey = (actionProperty) => (reducer) => (state = {}, action) => {
    const key = action[actionProperty];
    if (key === void 0) {
      return state;
    }
    const nextKeyState = reducer(state[key], action);
    if (nextKeyState === state[key]) {
      return state;
    }
    return {
      ...state,
      [key]: nextKeyState
    };
  };
  var on_sub_key_default = onSubKey;

  // packages/core-data/build-module/utils/replace-action.mjs
  var replaceAction = (replacer) => (reducer) => (state, action) => {
    return reducer(state, replacer(action));
  };
  var replace_action_default = replaceAction;

  // packages/core-data/build-module/utils/with-weak-map-cache.mjs
  function withWeakMapCache(fn) {
    const cache3 = /* @__PURE__ */ new WeakMap();
    return (key) => {
      let value;
      if (cache3.has(key)) {
        value = cache3.get(key);
      } else {
        value = fn(key);
        if (key !== null && typeof key === "object") {
          cache3.set(key, value);
        }
      }
      return value;
    };
  }
  var with_weak_map_cache_default = withWeakMapCache;

  // packages/core-data/build-module/utils/is-raw-attribute.mjs
  function isRawAttribute(entity2, attribute) {
    return (entity2.rawAttributes || []).includes(attribute);
  }

  // packages/core-data/build-module/utils/set-nested-value.mjs
  function setNestedValue(object, path, value) {
    if (!object || typeof object !== "object") {
      return object;
    }
    const normalizedPath = Array.isArray(path) ? path : path.split(".");
    normalizedPath.reduce((acc, key, idx) => {
      if (acc[key] === void 0) {
        if (Number.isInteger(normalizedPath[idx + 1])) {
          acc[key] = [];
        } else {
          acc[key] = {};
        }
      }
      if (idx === normalizedPath.length - 1) {
        acc[key] = value;
      }
      return acc[key];
    }, object);
    return object;
  }

  // packages/core-data/build-module/utils/get-nested-value.mjs
  function getNestedValue(object, path, defaultValue) {
    if (!object || typeof object !== "object" || typeof path !== "string" && !Array.isArray(path)) {
      return object;
    }
    const normalizedPath = Array.isArray(path) ? path : path.split(".");
    let value = object;
    normalizedPath.forEach((fieldName) => {
      value = value?.[fieldName];
    });
    return value !== void 0 ? value : defaultValue;
  }

  // packages/core-data/build-module/utils/is-numeric-id.mjs
  function isNumericID(id) {
    return /^\s*\d+\s*$/.test(id);
  }

  // packages/core-data/build-module/utils/user-permissions.mjs
  var ALLOWED_RESOURCE_ACTIONS = [
    "create",
    "read",
    "update",
    "delete"
  ];
  function getUserPermissionsFromAllowHeader(allowedMethods) {
    const permissions = {};
    if (!allowedMethods) {
      return permissions;
    }
    const methods = {
      create: "POST",
      read: "GET",
      update: "PUT",
      delete: "DELETE"
    };
    for (const [actionName, methodName] of Object.entries(methods)) {
      permissions[actionName] = allowedMethods.includes(methodName);
    }
    return permissions;
  }
  function getUserPermissionCacheKey(action, resource, id) {
    const key = (typeof resource === "object" ? [action, resource.kind, resource.name, resource.id] : [action, resource, id]).filter(Boolean).join("/");
    return key;
  }

  // packages/core-data/build-module/utils/receive-intermediate-results.mjs
  var RECEIVE_INTERMEDIATE_RESULTS = /* @__PURE__ */ Symbol(
    "RECEIVE_INTERMEDIATE_RESULTS"
  );

  // packages/core-data/build-module/queried-data/actions.mjs
  function receiveItems(items2, edits, meta) {
    return {
      type: "RECEIVE_ITEMS",
      items: Array.isArray(items2) ? items2 : [items2],
      persistedEdits: edits,
      meta
    };
  }
  function removeItems(kind, name, records, invalidateCache = false) {
    return {
      type: "REMOVE_ITEMS",
      itemIds: Array.isArray(records) ? records : [records],
      kind,
      name,
      invalidateCache
    };
  }
  function receiveQueriedItems(items2, query = {}, edits, meta) {
    return {
      ...receiveItems(items2, edits, meta),
      query
    };
  }

  // packages/core-data/build-module/queried-data/selectors.mjs
  var import_equivalent_key_map = __toESM(require_equivalent_key_map(), 1);
  var import_data = __toESM(require_data(), 1);

  // packages/core-data/build-module/queried-data/get-query-parts.mjs
  var import_url = __toESM(require_url(), 1);
  function getQueryParts(query) {
    const parts = {
      stableKey: "",
      page: 1,
      perPage: 10,
      fields: null,
      include: null,
      context: "default"
    };
    const keys = Object.keys(query).sort();
    for (let i = 0; i < keys.length; i++) {
      const key = keys[i];
      let value = query[key];
      switch (key) {
        case "page":
          parts[key] = Number(value);
          break;
        case "per_page":
          parts.perPage = Number(value);
          break;
        case "context":
          parts.context = value;
          break;
        default:
          if (key === "_fields") {
            parts.fields = get_normalized_comma_separable_default(value) ?? [];
            value = parts.fields.join();
          }
          if (key === "include") {
            if (typeof value === "number") {
              value = value.toString();
            }
            parts.include = (get_normalized_comma_separable_default(value) ?? []).map(Number);
            value = parts.include.join();
          }
          parts.stableKey += (parts.stableKey ? "&" : "") + (0, import_url.addQueryArgs)("", { [key]: value }).slice(1);
      }
    }
    return parts;
  }
  var get_query_parts_default = with_weak_map_cache_default(getQueryParts);

  // packages/core-data/build-module/queried-data/selectors.mjs
  var queriedItemsCacheByState = /* @__PURE__ */ new WeakMap();
  function getQueriedItemsUncached(state, query) {
    const { stableKey, page, perPage, include, fields, context } = get_query_parts_default(query);
    let itemIds;
    if (state.queries?.[context]?.[stableKey]) {
      itemIds = state.queries[context][stableKey].itemIds;
    }
    if (!itemIds) {
      return null;
    }
    const startOffset = perPage === -1 ? 0 : (page - 1) * perPage;
    const endOffset = perPage === -1 ? itemIds.length : Math.min(startOffset + perPage, itemIds.length);
    const items2 = [];
    for (let i = startOffset; i < endOffset; i++) {
      const itemId = itemIds[i];
      if (Array.isArray(include) && !include.includes(itemId)) {
        continue;
      }
      if (itemId === void 0) {
        continue;
      }
      if (!state.items[context]?.hasOwnProperty(itemId)) {
        return null;
      }
      const item = state.items[context][itemId];
      let filteredItem;
      if (Array.isArray(fields)) {
        filteredItem = {};
        for (let f = 0; f < fields.length; f++) {
          const field = fields[f].split(".");
          let value = item;
          field.forEach((fieldName) => {
            value = value?.[fieldName];
          });
          setNestedValue(filteredItem, field, value);
        }
      } else {
        if (!state.itemIsComplete[context]?.[itemId]) {
          return null;
        }
        filteredItem = item;
      }
      items2.push(filteredItem);
    }
    return items2;
  }
  var getQueriedItems = (0, import_data.createSelector)((state, query = {}) => {
    let queriedItemsCache = queriedItemsCacheByState.get(state);
    if (queriedItemsCache) {
      const queriedItems = queriedItemsCache.get(query);
      if (queriedItems !== void 0) {
        return queriedItems;
      }
    } else {
      queriedItemsCache = new import_equivalent_key_map.default();
      queriedItemsCacheByState.set(state, queriedItemsCache);
    }
    const items2 = getQueriedItemsUncached(state, query);
    queriedItemsCache.set(query, items2);
    return items2;
  });
  function getQueriedTotalItems(state, query = {}) {
    const { stableKey, context } = get_query_parts_default(query);
    return state.queries?.[context]?.[stableKey]?.meta?.totalItems ?? null;
  }
  function getQueriedTotalPages(state, query = {}) {
    const { stableKey, context } = get_query_parts_default(query);
    return state.queries?.[context]?.[stableKey]?.meta?.totalPages ?? null;
  }

  // packages/core-data/build-module/queried-data/reducer.mjs
  var import_data5 = __toESM(require_data(), 1);
  var import_compose = __toESM(require_compose(), 1);

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

  // node_modules/upper-case-first/dist.es2015/index.js
  function upperCaseFirst(input) {
    return input.charAt(0).toUpperCase() + input.substr(1);
  }

  // node_modules/capital-case/dist.es2015/index.js
  function capitalCaseTransform(input) {
    return upperCaseFirst(input.toLowerCase());
  }
  function capitalCase(input, options) {
    if (options === void 0) {
      options = {};
    }
    return noCase(input, __assign({ delimiter: " ", transform: capitalCaseTransform }, options));
  }

  // packages/core-data/build-module/entities.mjs
  var import_api_fetch = __toESM(require_api_fetch(), 1);
  var import_blocks4 = __toESM(require_blocks(), 1);
  var import_i18n = __toESM(require_i18n(), 1);

  // packages/core-data/build-module/awareness/post-editor-awareness.mjs
  var import_data3 = __toESM(require_data(), 1);
  var import_sync7 = __toESM(require_sync(), 1);
  var import_block_editor = __toESM(require_block_editor(), 1);

  // packages/core-data/build-module/awareness/base-awareness.mjs
  var import_data2 = __toESM(require_data(), 1);

  // packages/core-data/build-module/awareness/config.mjs
  var AWARENESS_CURSOR_UPDATE_THROTTLE_IN_MS = 100;
  var LOCAL_CURSOR_UPDATE_DEBOUNCE_IN_MS = 5;
  var REMOVAL_DELAY_IN_MS = 5e3;

  // packages/core-data/build-module/awareness/typed-awareness.mjs
  var import_sync = __toESM(require_sync(), 1);

  // packages/core-data/build-module/awareness/utils.mjs
  function getBrowserName() {
    const userAgent = window.navigator.userAgent;
    let browserName = "Unknown";
    if (userAgent.includes("Firefox")) {
      browserName = "Firefox";
    } else if (userAgent.includes("Edg")) {
      browserName = "Microsoft Edge";
    } else if (userAgent.includes("Chrome") && !userAgent.includes("Edg")) {
      browserName = "Chrome";
    } else if (userAgent.includes("Safari") && !userAgent.includes("Chrome")) {
      browserName = "Safari";
    } else if (userAgent.includes("MSIE") || userAgent.includes("Trident")) {
      browserName = "Internet Explorer";
    } else if (userAgent.includes("Opera") || userAgent.includes("OPR")) {
      browserName = "Opera";
    }
    return browserName;
  }
  function areMapsEqual(map1, map2, comparatorFn) {
    if (map1.size !== map2.size) {
      return false;
    }
    for (const [key, value1] of map1.entries()) {
      if (!map2.has(key)) {
        return false;
      }
      if (!comparatorFn(value1, map2.get(key))) {
        return false;
      }
    }
    return true;
  }
  function areCollaboratorInfosEqual(collaboratorInfo1, collaboratorInfo2) {
    if (!collaboratorInfo1 || !collaboratorInfo2) {
      return collaboratorInfo1 === collaboratorInfo2;
    }
    if (Object.keys(collaboratorInfo1).length !== Object.keys(collaboratorInfo2).length) {
      return false;
    }
    return Object.entries(collaboratorInfo1).every(([key, value]) => {
      return value === collaboratorInfo2[key];
    });
  }
  function generateCollaboratorInfo(currentCollaborator) {
    const { avatar_urls, id, name, slug } = currentCollaborator;
    return {
      avatar_urls,
      // eslint-disable-line camelcase
      browserType: getBrowserName(),
      enteredAt: Date.now(),
      id,
      name,
      slug
    };
  }
  function getRecordValue(obj, key) {
    if ("object" === typeof obj && null !== obj && key in obj) {
      return obj[key];
    }
    return null;
  }
  function getTypedKeys(obj) {
    return Object.keys(obj);
  }

  // packages/core-data/build-module/awareness/typed-awareness.mjs
  var TypedAwareness = class extends import_sync.Awareness {
    /**
     * Get the states from an awareness document.
     */
    getStates() {
      return super.getStates();
    }
    /**
     * Get a local state field from an awareness document.
     * @param field
     */
    getLocalStateField(field) {
      const state = this.getLocalState();
      return getRecordValue(state, field);
    }
    /**
     * Set a local state field on an awareness document.
     * @param field
     * @param value
     */
    setLocalStateField(field, value) {
      super.setLocalStateField(field, value);
    }
  };

  // packages/core-data/build-module/awareness/awareness-state.mjs
  var AwarenessWithEqualityChecks = class extends TypedAwareness {
    /** OVERRIDDEN METHODS */
    /**
     * Set a local state field on an awareness document. Calling this method may
     * trigger rerenders of any subscribed components.
     *
     * Equality checks are provided by the abstract `equalityFieldChecks` property.
     * @param field - The field to set.
     * @param value - The value to set.
     */
    setLocalStateField(field, value) {
      if (this.isFieldEqual(
        field,
        value,
        this.getLocalStateField(field) ?? void 0
      )) {
        return;
      }
      super.setLocalStateField(field, value);
    }
    /** CUSTOM METHODS */
    /**
     * Determine if a field value has changed using the provided equality checks.
     * @param field  - The field to check.
     * @param value1 - The first value to compare.
     * @param value2 - The second value to compare.
     */
    isFieldEqual(field, value1, value2) {
      if (["clientId", "isConnected", "isMe"].includes(field)) {
        return value1 === value2;
      }
      if (field in this.equalityFieldChecks) {
        const fn = this.equalityFieldChecks[field];
        return fn(value1, value2);
      }
      throw new Error(
        `No equality check implemented for awareness state field "${field.toString()}".`
      );
    }
    /**
     * Determine if two states are equal by comparing each field using the
     * provided equality checks.
     * @param state1 - The first state to compare.
     * @param state2 - The second state to compare.
     */
    isStateEqual(state1, state2) {
      return [
        .../* @__PURE__ */ new Set([
          ...getTypedKeys(state1),
          ...getTypedKeys(state2)
        ])
      ].every((field) => {
        const value1 = state1[field];
        const value2 = state2[field];
        return this.isFieldEqual(field, value1, value2);
      });
    }
  };
  var AwarenessState = class extends AwarenessWithEqualityChecks {
    /** CUSTOM PROPERTIES */
    /**
     * Whether the setUp method has been called, to avoid running it multiple
     * times.
     */
    hasSetupRun = false;
    /**
     * We keep track of all seen states during the current session for two reasons:
     *
     * 1. So that we can represent recently disconnected collaborators in our UI, even
     *    after they have been removed from the awareness document.
     * 2. So that we can provide debug information about all collaborators seen during
     *    the session.
     */
    disconnectedCollaborators = /* @__PURE__ */ new Set();
    seenStates = /* @__PURE__ */ new Map();
    /**
     * Hold a snapshot of the previous awareness state allows us to compare the
     * state values and avoid unnecessary updates to subscribers.
     */
    previousSnapshot = /* @__PURE__ */ new Map();
    stateSubscriptions = [];
    /**
     * In some cases, we may want to throttle setting local state fields to avoid
     * overwhelming the awareness document with rapid updates. At the same time, we
     * want to ensure that when we read our own state locally, we get the latest
     * value -- even if it hasn't yet been set on the awareness instance.
     */
    myThrottledState = {};
    throttleTimeouts = /* @__PURE__ */ new Map();
    /** CUSTOM METHODS */
    /**
     * Set up the awareness state. This method is idempotent and will only run
     * once. Subclasses should override `onSetUp()` instead of this method to
     * add their own setup logic.
     *
     * This is defined as a readonly arrow function property to prevent
     * subclasses from overriding it.
     */
    setUp = () => {
      if (this.hasSetupRun) {
        return;
      }
      this.hasSetupRun = true;
      this.onSetUp();
      this.on(
        "change",
        ({ added, removed, updated }) => {
          [...added, ...updated].forEach((id) => {
            this.disconnectedCollaborators.delete(id);
          });
          removed.forEach((id) => {
            this.disconnectedCollaborators.add(id);
            setTimeout(() => {
              this.disconnectedCollaborators.delete(id);
              this.updateSubscribers(
                true
                /* force update */
              );
            }, REMOVAL_DELAY_IN_MS);
          });
          this.updateSubscribers();
        }
      );
    };
    /**
     * Get the most recent state from the last processed change event.
     *
     * @return An array of EnhancedState< State >.
     */
    getCurrentState() {
      return Array.from(this.previousSnapshot.values());
    }
    /**
     * Get all seen states in this session to enable debug reporting.
     */
    getSeenStates() {
      return this.seenStates;
    }
    /**
     * Allow external code to subscribe to awareness state changes.
     * @param callback - The callback to subscribe to.
     */
    onStateChange(callback) {
      this.stateSubscriptions.push(callback);
      return () => {
        this.stateSubscriptions = this.stateSubscriptions.filter(
          (cb) => cb !== callback
        );
      };
    }
    /**
     * Set a local state field on an awareness document with throttle. See caveats
     * of this.setLocalStateField.
     * @param field - The field to set.
     * @param value - The value to set.
     * @param wait  - The wait time in milliseconds.
     */
    setThrottledLocalStateField(field, value, wait) {
      this.setLocalStateField(field, value);
      this.throttleTimeouts.set(
        field,
        setTimeout(() => {
          this.throttleTimeouts.delete(field);
          if (this.myThrottledState[field]) {
            this.setLocalStateField(
              field,
              this.myThrottledState[field]
            );
            delete this.myThrottledState[field];
          }
        }, wait)
      );
    }
    /**
     * Set the current collaborator's connection status as awareness state.
     * @param isConnected - The connection status.
     */
    setConnectionStatus(isConnected) {
      if (isConnected) {
        this.disconnectedCollaborators.delete(this.clientID);
      } else {
        this.disconnectedCollaborators.add(this.clientID);
      }
      this.updateSubscribers(
        true
        /* force update */
      );
    }
    /**
     * Update all subscribed listeners with the latest awareness state.
     * @param forceUpdate - Whether to force an update.
     */
    updateSubscribers(forceUpdate = false) {
      if (!this.stateSubscriptions.length) {
        return;
      }
      const states = this.getStates();
      this.seenStates = new Map([
        ...this.seenStates.entries(),
        ...states.entries()
      ]);
      const updatedStates = new Map(
        [...this.disconnectedCollaborators, ...states.keys()].filter((clientId) => {
          return Object.keys(this.seenStates.get(clientId) ?? {}).length > 0;
        }).map((clientId) => {
          const rawState = this.seenStates.get(clientId);
          const isConnected = !this.disconnectedCollaborators.has(clientId);
          const isMe = clientId === this.clientID;
          const myState = isMe ? this.myThrottledState : {};
          const state = {
            ...rawState,
            ...myState,
            clientId,
            isConnected,
            isMe
          };
          return [clientId, state];
        })
      );
      if (!forceUpdate) {
        if (areMapsEqual(
          this.previousSnapshot,
          updatedStates,
          this.isStateEqual.bind(this)
        )) {
          return;
        }
      }
      this.previousSnapshot = updatedStates;
      this.stateSubscriptions.forEach((callback) => {
        callback(Array.from(updatedStates.values()));
      });
    }
  };

  // packages/core-data/build-module/name.mjs
  var STORE_NAME = "core";

  // packages/core-data/build-module/awareness/base-awareness.mjs
  var BaseAwarenessState = class extends AwarenessState {
    onSetUp() {
      void this.setCurrentCollaboratorInfo();
    }
    /**
     * Set the current collaborator info in the local state.
     */
    async setCurrentCollaboratorInfo() {
      const currentUser2 = await (0, import_data2.resolveSelect)(STORE_NAME).getCurrentUser();
      const collaboratorInfo = generateCollaboratorInfo(currentUser2);
      this.setLocalStateField("collaboratorInfo", collaboratorInfo);
    }
  };
  var baseEqualityFieldChecks = {
    collaboratorInfo: areCollaboratorInfosEqual
  };
  var BaseAwareness = class extends BaseAwarenessState {
    equalityFieldChecks = baseEqualityFieldChecks;
  };

  // packages/core-data/build-module/utils/crdt-user-selections.mjs
  var import_sync5 = __toESM(require_sync(), 1);

  // packages/core-data/build-module/sync.mjs
  var import_sync2 = __toESM(require_sync(), 1);

  // packages/core-data/build-module/lock-unlock.mjs
  var import_private_apis = __toESM(require_private_apis(), 1);
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/core-data"
  );

  // packages/core-data/build-module/sync.mjs
  var {
    createSyncManager,
    Delta,
    CRDT_DOC_META_PERSISTENCE_KEY,
    CRDT_RECORD_MAP_KEY,
    LOCAL_EDITOR_ORIGIN
  } = unlock(import_sync2.privateApis);
  var syncManager;
  function getSyncManager() {
    if (syncManager) {
      return syncManager;
    }
    syncManager = createSyncManager();
    return syncManager;
  }

  // packages/core-data/build-module/utils/crdt-utils.mjs
  var import_sync3 = __toESM(require_sync(), 1);
  function getRootMap(doc, key) {
    return doc.getMap(key);
  }
  function createYMap(partial = {}) {
    return new import_sync3.Y.Map(Object.entries(partial));
  }
  function isYMap(value) {
    return value instanceof import_sync3.Y.Map;
  }
  function findBlockByClientIdInDoc(blockId, ydoc) {
    const ymap = getRootMap(ydoc, CRDT_RECORD_MAP_KEY);
    const blocks = ymap.get("blocks");
    if (!(blocks instanceof import_sync3.Y.Array)) {
      return null;
    }
    return findBlockByClientIdInBlocks(blockId, blocks);
  }
  function findBlockByClientIdInBlocks(blockId, blocks) {
    for (const block of blocks) {
      if (block.get("clientId") === blockId) {
        return block;
      }
      const innerBlocks = block.get("innerBlocks");
      if (innerBlocks && innerBlocks.length > 0) {
        const innerBlock = findBlockByClientIdInBlocks(
          blockId,
          innerBlocks
        );
        if (innerBlock) {
          return innerBlock;
        }
      }
    }
    return null;
  }

  // packages/core-data/build-module/utils/crdt-user-selections.mjs
  var SelectionType = /* @__PURE__ */ ((SelectionType2) => {
    SelectionType2["None"] = "none";
    SelectionType2["Cursor"] = "cursor";
    SelectionType2["SelectionInOneBlock"] = "selection-in-one-block";
    SelectionType2["SelectionInMultipleBlocks"] = "selection-in-multiple-blocks";
    SelectionType2["WholeBlock"] = "whole-block";
    return SelectionType2;
  })(SelectionType || {});
  function getSelectionState(selectionStart, selectionEnd, yDoc) {
    const ymap = getRootMap(yDoc, CRDT_RECORD_MAP_KEY);
    const yBlocks = ymap.get("blocks") ?? new import_sync5.Y.Array();
    const isSelectionEmpty = Object.keys(selectionStart).length === 0;
    const noSelection = {
      type: "none"
      /* None */
    };
    if (isSelectionEmpty) {
      return noSelection;
    }
    const isSelectionInOneBlock = selectionStart.clientId === selectionEnd.clientId;
    const isCursorOnly = isSelectionInOneBlock && selectionStart.offset === selectionEnd.offset;
    const isSelectionAWholeBlock = isSelectionInOneBlock && selectionStart.offset === void 0 && selectionEnd.offset === void 0;
    if (isSelectionAWholeBlock) {
      return {
        type: "whole-block",
        blockId: selectionStart.clientId
      };
    } else if (isCursorOnly) {
      const cursorPosition = getCursorPosition(selectionStart, yBlocks);
      if (!cursorPosition) {
        return noSelection;
      }
      return {
        type: "cursor",
        blockId: selectionStart.clientId,
        cursorPosition
      };
    } else if (isSelectionInOneBlock) {
      const cursorStartPosition2 = getCursorPosition(
        selectionStart,
        yBlocks
      );
      const cursorEndPosition2 = getCursorPosition(selectionEnd, yBlocks);
      if (!cursorStartPosition2 || !cursorEndPosition2) {
        return noSelection;
      }
      return {
        type: "selection-in-one-block",
        blockId: selectionStart.clientId,
        cursorStartPosition: cursorStartPosition2,
        cursorEndPosition: cursorEndPosition2
      };
    }
    const cursorStartPosition = getCursorPosition(selectionStart, yBlocks);
    const cursorEndPosition = getCursorPosition(selectionEnd, yBlocks);
    if (!cursorStartPosition || !cursorEndPosition) {
      return noSelection;
    }
    return {
      type: "selection-in-multiple-blocks",
      blockStartId: selectionStart.clientId,
      blockEndId: selectionEnd.clientId,
      cursorStartPosition,
      cursorEndPosition
    };
  }
  function getCursorPosition(selection, blocks) {
    const block = findBlockByClientId(selection.clientId, blocks);
    if (!block || !selection.attributeKey || void 0 === selection.offset) {
      return null;
    }
    const attributes = block.get("attributes");
    const currentYText = attributes?.get(selection.attributeKey);
    const relativePosition = import_sync5.Y.createRelativePositionFromTypeIndex(
      currentYText,
      selection.offset
    );
    return {
      relativePosition,
      absoluteOffset: selection.offset
    };
  }
  function findBlockByClientId(blockId, blocks) {
    for (const block of blocks) {
      if (block.get("clientId") === blockId) {
        return block;
      }
      const innerBlocks = block.get("innerBlocks");
      if (innerBlocks && innerBlocks.length > 0) {
        const innerBlock = findBlockByClientId(blockId, innerBlocks);
        if (innerBlock) {
          return innerBlock;
        }
      }
    }
    return null;
  }
  function areSelectionsStatesEqual(selection1, selection2) {
    if (selection1.type !== selection2.type) {
      return false;
    }
    switch (selection1.type) {
      case "none":
        return true;
      case "cursor":
        return selection1.blockId === selection2.blockId && areCursorPositionsEqual(
          selection1.cursorPosition,
          selection2.cursorPosition
        );
      case "selection-in-one-block":
        return selection1.blockId === selection2.blockId && areCursorPositionsEqual(
          selection1.cursorStartPosition,
          selection2.cursorStartPosition
        ) && areCursorPositionsEqual(
          selection1.cursorEndPosition,
          selection2.cursorEndPosition
        );
      case "selection-in-multiple-blocks":
        return selection1.blockStartId === selection2.blockStartId && selection1.blockEndId === selection2.blockEndId && areCursorPositionsEqual(
          selection1.cursorStartPosition,
          selection2.cursorStartPosition
        ) && areCursorPositionsEqual(
          selection1.cursorEndPosition,
          selection2.cursorEndPosition
        );
      case "whole-block":
        return selection1.blockId === selection2.blockId;
      default:
        return false;
    }
  }
  function areCursorPositionsEqual(cursorPosition1, cursorPosition2) {
    const isRelativePositionEqual = JSON.stringify(cursorPosition1.relativePosition) === JSON.stringify(cursorPosition2.relativePosition);
    const isAbsoluteOffsetEqual = cursorPosition1.absoluteOffset === cursorPosition2.absoluteOffset;
    return isRelativePositionEqual && isAbsoluteOffsetEqual;
  }

  // packages/core-data/build-module/awareness/post-editor-awareness.mjs
  var PostEditorAwareness = class extends BaseAwarenessState {
    constructor(doc, kind, name, postId) {
      super(doc);
      this.kind = kind;
      this.name = name;
      this.postId = postId;
    }
    equalityFieldChecks = {
      ...baseEqualityFieldChecks,
      editorState: this.areEditorStatesEqual
    };
    onSetUp() {
      super.onSetUp();
      this.subscribeToCollaboratorSelectionChanges();
    }
    /**
     * Subscribe to collaborator selection changes and update the selection state.
     */
    subscribeToCollaboratorSelectionChanges() {
      const {
        getSelectionStart,
        getSelectionEnd,
        getSelectedBlocksInitialCaretPosition
      } = (0, import_data3.select)(import_block_editor.store);
      let selectionStart = getSelectionStart();
      let selectionEnd = getSelectionEnd();
      let localCursorTimeout = null;
      (0, import_data3.subscribe)(() => {
        const newSelectionStart = getSelectionStart();
        const newSelectionEnd = getSelectionEnd();
        if (newSelectionStart === selectionStart && newSelectionEnd === selectionEnd) {
          return;
        }
        selectionStart = newSelectionStart;
        selectionEnd = newSelectionEnd;
        const initialPosition = getSelectedBlocksInitialCaretPosition();
        void this.updateSelectionInEntityRecord(
          selectionStart,
          selectionEnd,
          initialPosition
        );
        if (localCursorTimeout) {
          clearTimeout(localCursorTimeout);
        }
        localCursorTimeout = setTimeout(() => {
          const selectionState = getSelectionState(
            selectionStart,
            selectionEnd,
            this.doc
          );
          this.setThrottledLocalStateField(
            "editorState",
            { selection: selectionState },
            AWARENESS_CURSOR_UPDATE_THROTTLE_IN_MS
          );
        }, LOCAL_CURSOR_UPDATE_DEBOUNCE_IN_MS);
      });
    }
    /**
     * Update the entity record with the current collaborator's selection.
     *
     * @param selectionStart  - The start position of the selection.
     * @param selectionEnd    - The end position of the selection.
     * @param initialPosition - The initial position of the selection.
     */
    async updateSelectionInEntityRecord(selectionStart, selectionEnd, initialPosition) {
      const edits = {
        selection: { selectionStart, selectionEnd, initialPosition }
      };
      const options = {
        undoIgnore: true
      };
      (0, import_data3.dispatch)(STORE_NAME).editEntityRecord(
        this.kind,
        this.name,
        this.postId,
        edits,
        options
      );
    }
    /**
     * Check if two editor states are equal.
     *
     * @param state1 - The first editor state.
     * @param state2 - The second editor state.
     * @return True if the editor states are equal, false otherwise.
     */
    areEditorStatesEqual(state1, state2) {
      if (!state1 || !state2) {
        return state1 === state2;
      }
      return areSelectionsStatesEqual(state1.selection, state2.selection);
    }
    /**
     * Get the absolute position index from a selection cursor.
     *
     * @param selection - The selection cursor.
     * @return The absolute position index, or null if not found.
     */
    getAbsolutePositionIndex(selection) {
      return import_sync7.Y.createAbsolutePositionFromRelativePosition(
        selection.cursorPosition.relativePosition,
        this.doc
      )?.index ?? null;
    }
    /**
     * Type guard to check if a struct is a Y.Item (not Y.GC)
     * @param struct - The struct to check.
     * @return True if the struct is a Y.Item, false otherwise.
     */
    isYItem(struct) {
      return "content" in struct;
    }
    /**
     * Get data for debugging, using the awareness state.
     *
     * @return {YDocDebugData} The debug data.
     */
    getDebugData() {
      const ydoc = this.doc;
      const docData = Object.fromEntries(
        Array.from(ydoc.share, ([key, value]) => [
          key,
          value.toJSON()
        ])
      );
      const collaboratorMapData = new Map(
        Array.from(this.getSeenStates().entries()).map(
          ([clientId, collaboratorState]) => [
            String(clientId),
            {
              name: collaboratorState.collaboratorInfo.name,
              wpUserId: collaboratorState.collaboratorInfo.id
            }
          ]
        )
      );
      const serializableClientItems = {};
      ydoc.store.clients.forEach((structs, clientId) => {
        const items2 = structs.filter(this.isYItem);
        serializableClientItems[clientId] = items2.map((item) => {
          const { left, right, ...rest } = item;
          return {
            ...rest,
            left: left ? {
              id: left.id,
              length: left.length,
              origin: left.origin,
              content: left.content
            } : null,
            right: right ? {
              id: right.id,
              length: right.length,
              origin: right.origin,
              content: right.content
            } : null
          };
        });
      });
      return {
        doc: docData,
        clients: serializableClientItems,
        collaboratorMap: Object.fromEntries(collaboratorMapData)
      };
    }
  };

  // packages/core-data/build-module/utils/crdt.mjs
  var import_es63 = __toESM(require_es6(), 1);
  var import_blocks3 = __toESM(require_blocks(), 1);
  var import_sync12 = __toESM(require_sync(), 1);

  // node_modules/uuid/dist/esm-browser/rng.js
  var getRandomValues;
  var rnds8 = new Uint8Array(16);
  function rng() {
    if (!getRandomValues) {
      getRandomValues = typeof crypto !== "undefined" && crypto.getRandomValues && crypto.getRandomValues.bind(crypto);
      if (!getRandomValues) {
        throw new Error("crypto.getRandomValues() not supported. See https://github.com/uuidjs/uuid#getrandomvalues-not-supported");
      }
    }
    return getRandomValues(rnds8);
  }

  // node_modules/uuid/dist/esm-browser/stringify.js
  var byteToHex = [];
  for (let i = 0; i < 256; ++i) {
    byteToHex.push((i + 256).toString(16).slice(1));
  }
  function unsafeStringify(arr, offset = 0) {
    return byteToHex[arr[offset + 0]] + byteToHex[arr[offset + 1]] + byteToHex[arr[offset + 2]] + byteToHex[arr[offset + 3]] + "-" + byteToHex[arr[offset + 4]] + byteToHex[arr[offset + 5]] + "-" + byteToHex[arr[offset + 6]] + byteToHex[arr[offset + 7]] + "-" + byteToHex[arr[offset + 8]] + byteToHex[arr[offset + 9]] + "-" + byteToHex[arr[offset + 10]] + byteToHex[arr[offset + 11]] + byteToHex[arr[offset + 12]] + byteToHex[arr[offset + 13]] + byteToHex[arr[offset + 14]] + byteToHex[arr[offset + 15]];
  }

  // node_modules/uuid/dist/esm-browser/native.js
  var randomUUID = typeof crypto !== "undefined" && crypto.randomUUID && crypto.randomUUID.bind(crypto);
  var native_default = {
    randomUUID
  };

  // node_modules/uuid/dist/esm-browser/v4.js
  function v4(options, buf, offset) {
    if (native_default.randomUUID && !buf && !options) {
      return native_default.randomUUID();
    }
    options = options || {};
    const rnds = options.random || (options.rng || rng)();
    rnds[6] = rnds[6] & 15 | 64;
    rnds[8] = rnds[8] & 63 | 128;
    if (buf) {
      offset = offset || 0;
      for (let i = 0; i < 16; ++i) {
        buf[offset + i] = rnds[i];
      }
      return buf;
    }
    return unsafeStringify(rnds);
  }
  var v4_default = v4;

  // packages/core-data/build-module/utils/crdt-blocks.mjs
  var import_es62 = __toESM(require_es6(), 1);
  var import_blocks = __toESM(require_blocks(), 1);
  var import_rich_text = __toESM(require_rich_text(), 1);
  var import_sync8 = __toESM(require_sync(), 1);
  var serializableBlocksCache = /* @__PURE__ */ new WeakMap();
  function makeBlockAttributesSerializable(attributes) {
    const newAttributes = { ...attributes };
    for (const [key, value] of Object.entries(attributes)) {
      if (value instanceof import_rich_text.RichTextData) {
        newAttributes[key] = value.valueOf();
      }
    }
    return newAttributes;
  }
  function makeBlocksSerializable(blocks) {
    return blocks.map((block) => {
      const { name, innerBlocks, attributes, ...rest } = block;
      delete rest.validationIssues;
      return {
        ...rest,
        name,
        attributes: makeBlockAttributesSerializable(attributes),
        innerBlocks: makeBlocksSerializable(innerBlocks)
      };
    });
  }
  function areBlocksEqual(gblock, yblock) {
    const yblockAsJson = yblock.toJSON();
    const overwrites = {
      innerBlocks: null,
      clientId: null
    };
    const res = (0, import_es62.default)(
      Object.assign({}, gblock, overwrites),
      Object.assign({}, yblockAsJson, overwrites)
    );
    const inners = gblock.innerBlocks || [];
    const yinners = yblock.get("innerBlocks");
    return res && inners.length === yinners?.length && inners.every(
      (block, i) => areBlocksEqual(block, yinners.get(i))
    );
  }
  function createNewYAttributeMap(blockName, attributes) {
    return new import_sync8.Y.Map(
      Object.entries(attributes).map(
        ([attributeName, attributeValue]) => {
          return [
            attributeName,
            createNewYAttributeValue(
              blockName,
              attributeName,
              attributeValue
            )
          ];
        }
      )
    );
  }
  function createNewYAttributeValue(blockName, attributeName, attributeValue) {
    const isRichText = isRichTextAttribute(blockName, attributeName);
    if (isRichText) {
      return new import_sync8.Y.Text(attributeValue?.toString() ?? "");
    }
    return attributeValue;
  }
  function createNewYBlock(block) {
    return createYMap(
      Object.fromEntries(
        Object.entries(block).map(([key, value]) => {
          switch (key) {
            case "attributes": {
              return [
                key,
                createNewYAttributeMap(block.name, value)
              ];
            }
            case "innerBlocks": {
              const innerBlocks = new import_sync8.Y.Array();
              if (!Array.isArray(value)) {
                return [key, innerBlocks];
              }
              innerBlocks.insert(
                0,
                value.map(
                  (innerBlock) => createNewYBlock(innerBlock)
                )
              );
              return [key, innerBlocks];
            }
            default:
              return [key, value];
          }
        })
      )
    );
  }
  function mergeCrdtBlocks(yblocks, incomingBlocks, cursorPosition) {
    if (!serializableBlocksCache.has(incomingBlocks)) {
      serializableBlocksCache.set(
        incomingBlocks,
        makeBlocksSerializable(incomingBlocks)
      );
    }
    const allBlocks = serializableBlocksCache.get(incomingBlocks) ?? [];
    const blocksToSync = allBlocks.filter(
      (block) => shouldBlockBeSynced(block)
    );
    const numOfCommonEntries = Math.min(
      blocksToSync.length ?? 0,
      yblocks.length
    );
    let left = 0;
    let right = 0;
    for (; left < numOfCommonEntries && areBlocksEqual(blocksToSync[left], yblocks.get(left)); left++) {
    }
    for (; right < numOfCommonEntries - left && areBlocksEqual(
      blocksToSync[blocksToSync.length - right - 1],
      yblocks.get(yblocks.length - right - 1)
    ); right++) {
    }
    const numOfUpdatesNeeded = numOfCommonEntries - left - right;
    const numOfInsertionsNeeded = Math.max(
      0,
      blocksToSync.length - yblocks.length
    );
    const numOfDeletionsNeeded = Math.max(
      0,
      yblocks.length - blocksToSync.length
    );
    for (let i = 0; i < numOfUpdatesNeeded; i++, left++) {
      const block = blocksToSync[left];
      const yblock = yblocks.get(left);
      Object.entries(block).forEach(([key, value]) => {
        switch (key) {
          case "attributes": {
            const currentAttributes = yblock.get(key);
            if (!currentAttributes) {
              yblock.set(
                key,
                createNewYAttributeMap(block.name, value)
              );
              break;
            }
            Object.entries(value).forEach(
              ([attributeName, attributeValue]) => {
                const currentAttribute = currentAttributes?.get(attributeName);
                const isExpectedType = isExpectedAttributeType(
                  block.name,
                  attributeName,
                  currentAttribute
                );
                const isAttributeChanged = !isExpectedType || !(0, import_es62.default)(
                  currentAttribute,
                  attributeValue
                );
                if (isAttributeChanged) {
                  updateYBlockAttribute(
                    block.name,
                    attributeName,
                    attributeValue,
                    currentAttributes,
                    cursorPosition
                  );
                }
              }
            );
            currentAttributes.forEach(
              (_attrValue, attrName) => {
                if (!value.hasOwnProperty(attrName)) {
                  currentAttributes.delete(attrName);
                }
              }
            );
            break;
          }
          case "innerBlocks": {
            let yInnerBlocks = yblock.get(key);
            if (!(yInnerBlocks instanceof import_sync8.Y.Array)) {
              yInnerBlocks = new import_sync8.Y.Array();
              yblock.set(key, yInnerBlocks);
            }
            mergeCrdtBlocks(
              yInnerBlocks,
              value ?? [],
              cursorPosition
            );
            break;
          }
          default:
            if (!(0, import_es62.default)(block[key], yblock.get(key))) {
              yblock.set(key, value);
            }
        }
      });
      yblock.forEach((_v, k) => {
        if (!block.hasOwnProperty(k)) {
          yblock.delete(k);
        }
      });
    }
    yblocks.delete(left, numOfDeletionsNeeded);
    for (let i = 0; i < numOfInsertionsNeeded; i++, left++) {
      const newBlock = [createNewYBlock(blocksToSync[left])];
      yblocks.insert(left, newBlock);
    }
    const knownClientIds = /* @__PURE__ */ new Set();
    for (let j = 0; j < yblocks.length; j++) {
      const yblock = yblocks.get(j);
      let clientId = yblock.get("clientId");
      if (!clientId) {
        continue;
      }
      if (knownClientIds.has(clientId)) {
        clientId = v4_default();
        yblock.set("clientId", clientId);
      }
      knownClientIds.add(clientId);
    }
  }
  function shouldBlockBeSynced(block) {
    if ("core/gallery" === block.name) {
      return !block.innerBlocks.some(
        (innerBlock) => innerBlock.attributes && innerBlock.attributes.blob
      );
    }
    return true;
  }
  function updateYBlockAttribute(blockName, attributeName, attributeValue, currentAttributes, cursorPosition) {
    const isRichText = isRichTextAttribute(blockName, attributeName);
    const currentAttribute = currentAttributes.get(attributeName);
    if (isRichText && "string" === typeof attributeValue && currentAttributes.has(attributeName) && currentAttribute instanceof import_sync8.Y.Text) {
      mergeRichTextUpdate(currentAttribute, attributeValue, cursorPosition);
    } else {
      currentAttributes.set(
        attributeName,
        createNewYAttributeValue(blockName, attributeName, attributeValue)
      );
    }
  }
  var cachedBlockAttributeTypes;
  function getBlockAttributeType(blockName, attributeName) {
    if (!cachedBlockAttributeTypes) {
      cachedBlockAttributeTypes = /* @__PURE__ */ new Map();
      for (const blockType of (0, import_blocks.getBlockTypes)()) {
        const blockAttributeTypeMap = /* @__PURE__ */ new Map();
        for (const [name, definition] of Object.entries(
          blockType.attributes ?? {}
        )) {
          if (definition.type) {
            blockAttributeTypeMap.set(name, definition.type);
          }
        }
        cachedBlockAttributeTypes.set(
          blockType.name,
          blockAttributeTypeMap
        );
      }
    }
    return cachedBlockAttributeTypes.get(blockName)?.get(attributeName);
  }
  function isExpectedAttributeType(blockName, attributeName, attributeValue) {
    const expectedAttributeType = getBlockAttributeType(
      blockName,
      attributeName
    );
    if (expectedAttributeType === "rich-text") {
      return attributeValue instanceof import_sync8.Y.Text;
    } else if (expectedAttributeType === "string") {
      return typeof attributeValue === "string";
    }
    return true;
  }
  function isRichTextAttribute(blockName, attributeName) {
    return "rich-text" === getBlockAttributeType(blockName, attributeName);
  }
  var localDoc;
  function mergeRichTextUpdate(blockYText, updatedValue, cursorPosition = null) {
    if (!localDoc) {
      localDoc = new import_sync8.Y.Doc();
    }
    const localYText = localDoc.getText("temporary-text");
    localYText.delete(0, localYText.length);
    localYText.insert(0, updatedValue);
    const currentValueAsDelta = new Delta(blockYText.toDelta());
    const updatedValueAsDelta = new Delta(localYText.toDelta());
    const deltaDiff = currentValueAsDelta.diffWithCursor(
      updatedValueAsDelta,
      cursorPosition
    );
    blockYText.applyDelta(deltaDiff.ops);
  }

  // packages/core-data/build-module/utils/crdt-selection.mjs
  var import_data4 = __toESM(require_data(), 1);
  var import_block_editor2 = __toESM(require_block_editor(), 1);
  var import_blocks2 = __toESM(require_blocks(), 1);
  var import_sync11 = __toESM(require_sync(), 1);

  // packages/core-data/build-module/utils/block-selection-history.mjs
  var import_sync10 = __toESM(require_sync(), 1);
  var SELECTION_HISTORY_DEFAULT_SIZE = 5;
  var YSelectionType = /* @__PURE__ */ ((YSelectionType2) => {
    YSelectionType2["RelativeSelection"] = "RelativeSelection";
    YSelectionType2["BlockSelection"] = "BlockSelection";
    return YSelectionType2;
  })(YSelectionType || {});
  function createBlockSelectionHistory(ydoc, historySize = SELECTION_HISTORY_DEFAULT_SIZE) {
    let history = [];
    const getSelectionHistory2 = () => {
      return history.slice(0);
    };
    const updateSelection = (newSelection) => {
      if (!newSelection?.selectionStart?.clientId || !newSelection?.selectionEnd?.clientId) {
        return;
      }
      const { selectionStart, selectionEnd } = newSelection;
      const start = convertWPBlockSelectionToSelection(
        selectionStart,
        ydoc
      );
      const end = convertWPBlockSelectionToSelection(selectionEnd, ydoc);
      addToHistory({ start, end });
    };
    const addToHistory = (yFullSelection) => {
      const startClientId = yFullSelection.start.clientId;
      const endClientId = yFullSelection.end.clientId;
      history = history.filter((entry) => {
        const isSameBlockCombination = entry.start.clientId === startClientId && entry.end.clientId === endClientId;
        return !isSameBlockCombination;
      });
      history.unshift(yFullSelection);
      if (history.length > historySize + 1) {
        history = history.slice(0, historySize + 1);
      }
    };
    return {
      getSelectionHistory: getSelectionHistory2,
      updateSelection
    };
  }
  function convertWPBlockSelectionToSelection(selection, ydoc) {
    const clientId = selection.clientId;
    const block = findBlockByClientIdInDoc(clientId, ydoc);
    const attributes = block?.get("attributes");
    const attributeKey = selection.attributeKey;
    const changedYText = attributeKey ? attributes?.get(attributeKey) : void 0;
    const isYText = changedYText instanceof import_sync10.Y.Text;
    const isFullyDefinedSelection = attributeKey && clientId;
    if (!isYText || !isFullyDefinedSelection) {
      return {
        type: "BlockSelection",
        clientId
      };
    }
    const offset = selection.offset ?? 0;
    const relativePosition = import_sync10.Y.createRelativePositionFromTypeIndex(
      changedYText,
      offset
    );
    return {
      type: "RelativeSelection",
      attributeKey,
      relativePosition,
      clientId,
      offset
    };
  }

  // packages/core-data/build-module/utils/crdt-selection.mjs
  var selectionHistoryMap = /* @__PURE__ */ new WeakMap();
  function getBlockSelectionHistory(ydoc) {
    let history = selectionHistoryMap.get(ydoc);
    if (!history) {
      history = createBlockSelectionHistory(ydoc);
      selectionHistoryMap.set(ydoc, history);
    }
    return history;
  }
  function getSelectionHistory(ydoc) {
    return getBlockSelectionHistory(ydoc).getSelectionHistory();
  }
  function updateSelectionHistory(ydoc, wpSelection) {
    return getBlockSelectionHistory(ydoc).updateSelection(wpSelection);
  }
  function convertYSelectionToBlockSelection(ySelection, ydoc) {
    if (ySelection.type === YSelectionType.RelativeSelection) {
      const { relativePosition, attributeKey, clientId } = ySelection;
      const absolutePosition = import_sync11.Y.createAbsolutePositionFromRelativePosition(
        relativePosition,
        ydoc
      );
      if (absolutePosition) {
        return {
          clientId,
          attributeKey,
          offset: absolutePosition.index
        };
      }
    } else if (ySelection.type === YSelectionType.BlockSelection) {
      return {
        clientId: ySelection.clientId,
        attributeKey: void 0,
        offset: void 0
      };
    }
    return null;
  }
  function convertYFullSelectionToWPSelection(yFullSelection, ydoc) {
    const { start, end } = yFullSelection;
    const startBlock = findBlockByClientIdInDoc(start.clientId, ydoc);
    const endBlock = findBlockByClientIdInDoc(end.clientId, ydoc);
    if (!startBlock || !endBlock) {
      return null;
    }
    const startBlockSelection = convertYSelectionToBlockSelection(
      start,
      ydoc
    );
    const endBlockSelection = convertYSelectionToBlockSelection(end, ydoc);
    if (startBlockSelection === null || endBlockSelection === null) {
      return null;
    }
    return {
      selectionStart: startBlockSelection,
      selectionEnd: endBlockSelection
    };
  }
  function findSelectionFromHistory(ydoc, selectionHistory) {
    for (const positionToTry of selectionHistory) {
      const result = convertYFullSelectionToWPSelection(
        positionToTry,
        ydoc
      );
      if (result !== null) {
        return result;
      }
    }
    return null;
  }
  function restoreSelection(selectionHistory, ydoc) {
    const selectionToRestore = findSelectionFromHistory(
      ydoc,
      selectionHistory
    );
    if (selectionToRestore === null) {
      return;
    }
    const { getBlock } = (0, import_data4.select)(import_block_editor2.store);
    const { resetSelection } = (0, import_data4.dispatch)(import_block_editor2.store);
    const { selectionStart, selectionEnd } = selectionToRestore;
    const isSelectionInSameBlock = selectionStart.clientId === selectionEnd.clientId;
    if (isSelectionInSameBlock) {
      const block = getBlock(selectionStart.clientId);
      const isBlockEmpty = block && (0, import_blocks2.isUnmodifiedBlock)(block);
      const isBeginningOfEmptyBlock = 0 === selectionStart.offset && 0 === selectionEnd.offset && isBlockEmpty && !selectionStart.attributeKey && !selectionEnd.attributeKey;
      if (isBeginningOfEmptyBlock) {
        const selectionStartWithoutOffset = {
          clientId: selectionStart.clientId
        };
        const selectionEndWithoutOffset = {
          clientId: selectionEnd.clientId
        };
        resetSelection(
          selectionStartWithoutOffset,
          selectionEndWithoutOffset,
          0
        );
      } else {
        resetSelection(selectionStart, selectionEnd, 0);
      }
    } else {
      resetSelection(selectionEnd, selectionEnd, 0);
    }
  }
  function getShiftedSelection(ydoc, selectionHistory) {
    if (selectionHistory.length === 0) {
      return null;
    }
    const { start, end } = selectionHistory[0];
    if (start.type === YSelectionType.BlockSelection || end.type === YSelectionType.BlockSelection) {
      return null;
    }
    const selectionStart = convertYSelectionToBlockSelection(start, ydoc);
    const selectionEnd = convertYSelectionToBlockSelection(end, ydoc);
    if (!selectionStart || !selectionEnd) {
      return null;
    }
    const startShifted = selectionStart.offset !== start.offset;
    const endShifted = selectionEnd.offset !== end.offset;
    if (!startShifted && !endShifted) {
      return null;
    }
    return { selectionStart, selectionEnd };
  }

  // packages/core-data/build-module/utils/crdt.mjs
  var POST_META_KEY_FOR_CRDT_DOC_PERSISTENCE = "_crdt_document";
  var allowedPostProperties = /* @__PURE__ */ new Set([
    "author",
    "blocks",
    "content",
    "categories",
    "comment_status",
    "date",
    "excerpt",
    "featured_media",
    "format",
    "meta",
    "ping_status",
    "slug",
    "status",
    "sticky",
    "tags",
    "template",
    "title"
  ]);
  var disallowedPostMetaKeys = /* @__PURE__ */ new Set([
    POST_META_KEY_FOR_CRDT_DOC_PERSISTENCE
  ]);
  function defaultApplyChangesToCRDTDoc(ydoc, changes) {
    const ymap = getRootMap(ydoc, CRDT_RECORD_MAP_KEY);
    Object.entries(changes).forEach(([key, newValue]) => {
      if ("function" === typeof newValue) {
        return;
      }
      switch (key) {
        // Add support for additional data types here.
        default: {
          const currentValue = ymap.get(key);
          updateMapValue(ymap, key, currentValue, newValue);
        }
      }
    });
  }
  function applyPostChangesToCRDTDoc(ydoc, changes, _postType) {
    const ymap = getRootMap(ydoc, CRDT_RECORD_MAP_KEY);
    Object.keys(changes).forEach((key) => {
      if (!allowedPostProperties.has(key)) {
        return;
      }
      const newValue = changes[key];
      if ("function" === typeof newValue) {
        return;
      }
      switch (key) {
        case "blocks": {
          if (!newValue) {
            ymap.set(key, void 0);
            break;
          }
          let currentBlocks = ymap.get(key);
          if (!(currentBlocks instanceof import_sync12.Y.Array)) {
            currentBlocks = new import_sync12.Y.Array();
            ymap.set(key, currentBlocks);
          }
          const cursorPosition = changes.selection?.selectionStart?.offset ?? null;
          mergeCrdtBlocks(currentBlocks, newValue, cursorPosition);
          break;
        }
        case "content":
        case "excerpt":
        case "title": {
          const currentValue = ymap.get(key);
          let rawValue = getRawValue(newValue);
          if (key === "title" && !currentValue?.toString() && "Auto Draft" === rawValue) {
            rawValue = "";
          }
          if (currentValue instanceof import_sync12.Y.Text) {
            mergeRichTextUpdate(currentValue, rawValue ?? "");
          } else {
            const newYText = new import_sync12.Y.Text(rawValue ?? "");
            ymap.set(key, newYText);
          }
          break;
        }
        // "Meta" is overloaded term; here, it refers to post meta.
        case "meta": {
          let metaMap = ymap.get("meta");
          if (!isYMap(metaMap)) {
            metaMap = createYMap();
            ymap.set("meta", metaMap);
          }
          Object.entries(newValue ?? {}).forEach(
            ([metaKey, metaValue]) => {
              if (disallowedPostMetaKeys.has(metaKey)) {
                return;
              }
              updateMapValue(
                metaMap,
                metaKey,
                metaMap.get(metaKey),
                // current value in CRDT
                metaValue
                // new value from changes
              );
            }
          );
          break;
        }
        case "slug": {
          if (!newValue) {
            break;
          }
          const currentValue = ymap.get(key);
          updateMapValue(ymap, key, currentValue, newValue);
          break;
        }
        // Add support for additional properties here.
        default: {
          const currentValue = ymap.get(key);
          updateMapValue(ymap, key, currentValue, newValue);
        }
      }
    });
    if (changes.selection) {
      const selection = changes.selection;
      setTimeout(() => {
        updateSelectionHistory(ydoc, selection);
      }, 0);
    }
  }
  function defaultGetChangesFromCRDTDoc(crdtDoc) {
    return getRootMap(crdtDoc, CRDT_RECORD_MAP_KEY).toJSON();
  }
  function getPostChangesFromCRDTDoc(ydoc, editedRecord, _postType) {
    const ymap = getRootMap(ydoc, CRDT_RECORD_MAP_KEY);
    let allowedMetaChanges = {};
    const changes = Object.fromEntries(
      Object.entries(ymap.toJSON()).filter(([key, newValue]) => {
        if (!allowedPostProperties.has(key)) {
          return false;
        }
        const currentValue = editedRecord[key];
        switch (key) {
          case "blocks": {
            if (ydoc.meta?.get(CRDT_DOC_META_PERSISTENCE_KEY) && editedRecord.content) {
              const blocksJson = ymap.get("blocks")?.toJSON() ?? [];
              return (0, import_blocks3.__unstableSerializeAndClean)(blocksJson).trim() !== getRawValue(editedRecord.content);
            }
            return true;
          }
          case "date": {
            const currentDateIsFloating = ["draft", "auto-draft", "pending"].includes(
              ymap.get("status")
            ) && (null === currentValue || editedRecord.modified === currentValue);
            if (currentDateIsFloating) {
              return false;
            }
            return haveValuesChanged(currentValue, newValue);
          }
          case "meta": {
            allowedMetaChanges = Object.fromEntries(
              Object.entries(newValue ?? {}).filter(
                ([metaKey]) => !disallowedPostMetaKeys.has(metaKey)
              )
            );
            const mergedValue = {
              ...currentValue,
              ...allowedMetaChanges
            };
            return haveValuesChanged(currentValue, mergedValue);
          }
          case "status": {
            if ("auto-draft" === newValue) {
              return false;
            }
            return haveValuesChanged(currentValue, newValue);
          }
          case "content":
          case "excerpt":
          case "title": {
            return haveValuesChanged(
              getRawValue(currentValue),
              newValue
            );
          }
          // Add support for additional data types here.
          default: {
            return haveValuesChanged(currentValue, newValue);
          }
        }
      })
    );
    if ("object" === typeof changes.meta) {
      changes.meta = {
        ...editedRecord.meta,
        ...allowedMetaChanges
      };
    }
    const selectionHistory = getSelectionHistory(ydoc);
    const shiftedSelection = getShiftedSelection(ydoc, selectionHistory);
    if (shiftedSelection) {
      changes.selection = {
        ...shiftedSelection,
        initialPosition: 0
      };
    }
    return changes;
  }
  var defaultSyncConfig = {
    applyChangesToCRDTDoc: defaultApplyChangesToCRDTDoc,
    createAwareness: (ydoc) => new BaseAwareness(ydoc),
    getChangesFromCRDTDoc: defaultGetChangesFromCRDTDoc
  };
  function getRawValue(value) {
    if ("string" === typeof value) {
      return value;
    }
    if (value && "object" === typeof value && "raw" in value && "string" === typeof value.raw) {
      return value.raw;
    }
    return void 0;
  }
  function haveValuesChanged(currentValue, newValue) {
    return !(0, import_es63.default)(currentValue, newValue);
  }
  function updateMapValue(map, key, currentValue, newValue) {
    if (void 0 === newValue) {
      map.delete(key);
      return;
    }
    if (haveValuesChanged(currentValue, newValue)) {
      map.set(key, newValue);
    }
  }

  // packages/core-data/build-module/entities.mjs
  var DEFAULT_ENTITY_KEY = "id";
  var POST_RAW_ATTRIBUTES = ["title", "excerpt", "content"];
  var blocksTransientEdits = {
    blocks: {
      read: (record) => (0, import_blocks4.parse)(record.content?.raw ?? ""),
      write: (record) => ({
        content: (0, import_blocks4.__unstableSerializeAndClean)(record.blocks)
      })
    }
  };
  var rootEntitiesConfig = [
    {
      label: (0, import_i18n.__)("Base"),
      kind: "root",
      key: false,
      name: "__unstableBase",
      baseURL: "/",
      baseURLParams: {
        // Please also change the preload path when changing this.
        // @see lib/compat/wordpress-7.0/preload.php
        _fields: [
          "description",
          "gmt_offset",
          "home",
          "image_sizes",
          "image_size_threshold",
          "image_output_formats",
          "jpeg_interlaced",
          "png_interlaced",
          "gif_interlaced",
          "name",
          "site_icon",
          "site_icon_url",
          "site_logo",
          "timezone_string",
          "url",
          "page_for_posts",
          "page_on_front",
          "show_on_front"
        ].join(",")
      },
      // The entity doesn't support selecting multiple records.
      // The property is maintained for backward compatibility.
      plural: "__unstableBases"
    },
    {
      label: (0, import_i18n.__)("Post Type"),
      name: "postType",
      kind: "root",
      key: "slug",
      baseURL: "/wp/v2/types",
      baseURLParams: { context: "edit" },
      plural: "postTypes"
    },
    {
      name: "media",
      kind: "root",
      baseURL: "/wp/v2/media",
      baseURLParams: { context: "edit" },
      plural: "mediaItems",
      label: (0, import_i18n.__)("Media"),
      rawAttributes: ["caption", "title", "description"],
      supportsPagination: true
    },
    {
      name: "taxonomy",
      kind: "root",
      key: "slug",
      baseURL: "/wp/v2/taxonomies",
      baseURLParams: { context: "edit" },
      plural: "taxonomies",
      label: (0, import_i18n.__)("Taxonomy")
    },
    {
      name: "sidebar",
      kind: "root",
      baseURL: "/wp/v2/sidebars",
      baseURLParams: { context: "edit" },
      plural: "sidebars",
      transientEdits: { blocks: true },
      label: (0, import_i18n.__)("Widget areas")
    },
    {
      name: "widget",
      kind: "root",
      baseURL: "/wp/v2/widgets",
      baseURLParams: { context: "edit" },
      plural: "widgets",
      transientEdits: { blocks: true },
      label: (0, import_i18n.__)("Widgets")
    },
    {
      name: "widgetType",
      kind: "root",
      baseURL: "/wp/v2/widget-types",
      baseURLParams: { context: "edit" },
      plural: "widgetTypes",
      label: (0, import_i18n.__)("Widget types")
    },
    {
      label: (0, import_i18n.__)("User"),
      name: "user",
      kind: "root",
      baseURL: "/wp/v2/users",
      getTitle: (record) => record?.name || record?.slug,
      baseURLParams: { context: "edit" },
      plural: "users",
      supportsPagination: true
    },
    {
      name: "comment",
      kind: "root",
      baseURL: "/wp/v2/comments",
      baseURLParams: { context: "edit" },
      plural: "comments",
      label: (0, import_i18n.__)("Comment"),
      supportsPagination: true
    },
    {
      name: "menu",
      kind: "root",
      baseURL: "/wp/v2/menus",
      baseURLParams: { context: "edit" },
      plural: "menus",
      label: (0, import_i18n.__)("Menu"),
      supportsPagination: true
    },
    {
      name: "menuItem",
      kind: "root",
      baseURL: "/wp/v2/menu-items",
      baseURLParams: { context: "edit" },
      plural: "menuItems",
      label: (0, import_i18n.__)("Menu Item"),
      rawAttributes: ["title"],
      supportsPagination: true
    },
    {
      name: "menuLocation",
      kind: "root",
      baseURL: "/wp/v2/menu-locations",
      baseURLParams: { context: "edit" },
      plural: "menuLocations",
      label: (0, import_i18n.__)("Menu Location"),
      key: "name"
    },
    {
      label: (0, import_i18n.__)("Global Styles"),
      name: "globalStyles",
      kind: "root",
      baseURL: "/wp/v2/global-styles",
      baseURLParams: { context: "edit" },
      plural: "globalStylesVariations",
      // Should be different from name.
      getTitle: () => (0, import_i18n.__)("Custom Styles"),
      getRevisionsUrl: (parentId, revisionId) => `/wp/v2/global-styles/${parentId}/revisions${revisionId ? "/" + revisionId : ""}`,
      supportsPagination: true
    },
    {
      label: (0, import_i18n.__)("Themes"),
      name: "theme",
      kind: "root",
      baseURL: "/wp/v2/themes",
      baseURLParams: { context: "edit" },
      plural: "themes",
      key: "stylesheet"
    },
    {
      label: (0, import_i18n.__)("Plugins"),
      name: "plugin",
      kind: "root",
      baseURL: "/wp/v2/plugins",
      baseURLParams: { context: "edit" },
      plural: "plugins",
      key: "plugin"
    },
    {
      label: (0, import_i18n.__)("Status"),
      name: "status",
      kind: "root",
      baseURL: "/wp/v2/statuses",
      baseURLParams: { context: "edit" },
      plural: "statuses",
      key: "slug"
    },
    {
      label: (0, import_i18n.__)("Registered Templates"),
      name: "registeredTemplate",
      kind: "root",
      baseURL: "/wp/v2/registered-templates",
      key: "id"
    },
    {
      label: (0, import_i18n.__)("Font Collections"),
      name: "fontCollection",
      kind: "root",
      baseURL: "/wp/v2/font-collections",
      baseURLParams: { context: "view" },
      plural: "fontCollections",
      key: "slug"
    },
    {
      label: (0, import_i18n.__)("Icons"),
      name: "icon",
      kind: "root",
      baseURL: "/wp/v2/icons",
      baseURLParams: { context: "view" },
      plural: "icons",
      key: "name"
    }
  ].map((entity2) => {
    const syncEnabledRootEntities = /* @__PURE__ */ new Set(["comment"]);
    if (syncEnabledRootEntities.has(entity2.name)) {
      entity2.syncConfig = defaultSyncConfig;
    }
    return entity2;
  });
  var deprecatedEntities = {
    root: {
      media: {
        since: "6.9",
        alternative: {
          kind: "postType",
          name: "attachment"
        }
      }
    }
  };
  var additionalEntityConfigLoaders = [
    { kind: "postType", loadEntities: loadPostTypeEntities },
    { kind: "taxonomy", loadEntities: loadTaxonomyEntities },
    {
      kind: "root",
      name: "site",
      plural: "sites",
      loadEntities: loadSiteEntity
    }
  ];
  var prePersistPostType = (persistedRecord, edits, name, isTemplate) => {
    const newEdits = {};
    if (!isTemplate && persistedRecord?.status === "auto-draft") {
      if (!edits.status && !newEdits.status) {
        newEdits.status = "draft";
      }
      if ((!edits.title || edits.title === "Auto Draft") && !newEdits.title && (!persistedRecord?.title || persistedRecord?.title === "Auto Draft")) {
        newEdits.title = "";
      }
    }
    if (persistedRecord) {
      const objectType = `postType/${name}`;
      const objectId = persistedRecord.id;
      const serializedDoc = getSyncManager()?.createPersistedCRDTDoc(
        objectType,
        objectId
      );
      if (serializedDoc) {
        newEdits.meta = {
          ...edits.meta,
          [POST_META_KEY_FOR_CRDT_DOC_PERSISTENCE]: serializedDoc
        };
      }
    }
    return newEdits;
  };
  async function loadPostTypeEntities() {
    const postTypes = await (0, import_api_fetch.default)({
      path: "/wp/v2/types?context=view"
    });
    return Object.entries(postTypes ?? {}).map(([name, postType]) => {
      const isTemplate = ["wp_template", "wp_template_part"].includes(
        name
      );
      const namespace = postType?.rest_namespace ?? "wp/v2";
      const entity2 = {
        kind: "postType",
        baseURL: `/${namespace}/${postType.rest_base}`,
        baseURLParams: { context: "edit" },
        name,
        label: postType.name,
        transientEdits: {
          ...blocksTransientEdits,
          selection: true
        },
        mergedEdits: { meta: true },
        rawAttributes: POST_RAW_ATTRIBUTES,
        getTitle: (record) => record?.title?.rendered || record?.title || (isTemplate ? capitalCase(record.slug ?? "") : String(record.id)),
        __unstablePrePersist: (persistedRecord, edits) => prePersistPostType(persistedRecord, edits, name, isTemplate),
        __unstable_rest_base: postType.rest_base,
        supportsPagination: true,
        getRevisionsUrl: (parentId, revisionId) => `/${namespace}/${postType.rest_base}/${parentId}/revisions${revisionId ? "/" + revisionId : ""}`,
        revisionKey: isTemplate && !window?.__experimentalTemplateActivate ? "wp_id" : DEFAULT_ENTITY_KEY
      };
      entity2.syncConfig = {
        /**
         * Apply changes from the local editor to the local CRDT document so
         * that those changes can be synced to other peers (via the provider).
         *
         * @param {import('@wordpress/sync').CRDTDoc}               crdtDoc
         * @param {Partial< import('@wordpress/sync').ObjectData >} changes
         * @return {void}
         */
        applyChangesToCRDTDoc: (crdtDoc, changes) => applyPostChangesToCRDTDoc(crdtDoc, changes, postType),
        /**
         * Create the awareness instance for the entity's CRDT document.
         *
         * @param {import('@wordpress/sync').CRDTDoc}  ydoc
         * @param {import('@wordpress/sync').ObjectID} objectId
         * @return {import('@wordpress/sync').Awareness} Awareness instance
         */
        createAwareness: (ydoc, objectId) => {
          const kind = "postType";
          const id = parseInt(objectId, 10);
          return new PostEditorAwareness(ydoc, kind, name, id);
        },
        /**
         * Extract changes from a CRDT document that can be used to update the
         * local editor state.
         *
         * @param {import('@wordpress/sync').CRDTDoc}    crdtDoc
         * @param {import('@wordpress/sync').ObjectData} editedRecord
         * @return {Partial< import('@wordpress/sync').ObjectData >} Changes to record
         */
        getChangesFromCRDTDoc: (crdtDoc, editedRecord) => getPostChangesFromCRDTDoc(crdtDoc, editedRecord, postType),
        /**
         * Extract changes from a CRDT document that can be used to update the
         * local editor state.
         *
         * @param {import('@wordpress/sync').ObjectData} record
         * @return {Partial< import('@wordpress/sync').ObjectData >} Changes to record
         */
        getPersistedCRDTDoc: (record) => {
          return record?.meta[POST_META_KEY_FOR_CRDT_DOC_PERSISTENCE] || null;
        }
      };
      return entity2;
    });
  }
  async function loadTaxonomyEntities() {
    const taxonomies = await (0, import_api_fetch.default)({
      path: "/wp/v2/taxonomies?context=view"
    });
    return Object.entries(taxonomies ?? {}).map(([name, taxonomy]) => {
      const namespace = taxonomy?.rest_namespace ?? "wp/v2";
      const entity2 = {
        kind: "taxonomy",
        baseURL: `/${namespace}/${taxonomy.rest_base}`,
        baseURLParams: { context: "edit" },
        name,
        label: taxonomy.name,
        getTitle: (record) => record?.name,
        supportsPagination: true
      };
      entity2.syncConfig = defaultSyncConfig;
      return entity2;
    });
  }
  async function loadSiteEntity() {
    const entity2 = {
      label: (0, import_i18n.__)("Site"),
      name: "site",
      kind: "root",
      key: false,
      baseURL: "/wp/v2/settings",
      meta: {}
    };
    const site = await (0, import_api_fetch.default)({
      path: entity2.baseURL,
      method: "OPTIONS"
    });
    const labels = {};
    Object.entries(site?.schema?.properties ?? {}).forEach(
      ([key, value]) => {
        if (typeof value === "object" && value.title) {
          labels[key] = value.title;
        }
      }
    );
    return [{ ...entity2, meta: { labels } }];
  }
  var getMethodName = (kind, name, prefix = "get") => {
    const kindPrefix = kind === "root" ? "" : pascalCase(kind);
    const suffix = pascalCase(name);
    return `${prefix}${kindPrefix}${suffix}`;
  };

  // packages/core-data/build-module/queried-data/reducer.mjs
  function getContextFromAction(action) {
    const { query } = action;
    if (!query) {
      return "default";
    }
    const queryParts = get_query_parts_default(query);
    return queryParts.context;
  }
  function getMergedItemIds(itemIds, nextItemIds, page, perPage) {
    const receivedAllIds = page === 1 && perPage === -1;
    if (receivedAllIds) {
      return nextItemIds;
    }
    const nextItemIdsStartIndex = (page - 1) * perPage;
    const size = Math.max(
      itemIds?.length ?? 0,
      nextItemIdsStartIndex + nextItemIds.length
    );
    const mergedItemIds = new Array(size);
    for (let i = 0; i < size; i++) {
      const isInNextItemsRange = i >= nextItemIdsStartIndex && i < nextItemIdsStartIndex + perPage;
      mergedItemIds[i] = isInNextItemsRange ? nextItemIds[i - nextItemIdsStartIndex] : itemIds?.[i];
    }
    return mergedItemIds;
  }
  function removeEntitiesById(entities2, ids) {
    return Object.fromEntries(
      Object.entries(entities2).filter(
        ([id]) => !ids.some((itemId) => {
          if (Number.isInteger(itemId)) {
            return itemId === +id;
          }
          return itemId === id;
        })
      )
    );
  }
  function items(state = {}, action) {
    switch (action.type) {
      case "RECEIVE_ITEMS": {
        const context = getContextFromAction(action);
        const key = action.key || DEFAULT_ENTITY_KEY;
        return {
          ...state,
          [context]: {
            ...state[context],
            ...action.items.reduce((accumulator, value) => {
              const itemId = value?.[key];
              accumulator[itemId] = conservativeMapItem(
                state?.[context]?.[itemId],
                value
              );
              return accumulator;
            }, {})
          }
        };
      }
      case "REMOVE_ITEMS":
        return Object.fromEntries(
          Object.entries(state).map(([itemId, contextState]) => [
            itemId,
            removeEntitiesById(contextState, action.itemIds)
          ])
        );
    }
    return state;
  }
  function itemIsComplete(state = {}, action) {
    switch (action.type) {
      case "RECEIVE_ITEMS": {
        const context = getContextFromAction(action);
        const { query, key = DEFAULT_ENTITY_KEY } = action;
        const queryParts = query ? get_query_parts_default(query) : {};
        const isCompleteQuery = !query || !Array.isArray(queryParts.fields);
        return {
          ...state,
          [context]: {
            ...state[context],
            ...action.items.reduce((result, item) => {
              const itemId = item?.[key];
              result[itemId] = state?.[context]?.[itemId] || isCompleteQuery;
              return result;
            }, {})
          }
        };
      }
      case "REMOVE_ITEMS":
        return Object.fromEntries(
          Object.entries(state).map(([itemId, contextState]) => [
            itemId,
            removeEntitiesById(contextState, action.itemIds)
          ])
        );
    }
    return state;
  }
  var receiveQueries = (0, import_compose.compose)([
    // Limit to matching action type so we don't attempt to replace action on
    // an unhandled action.
    if_matching_action_default((action) => "query" in action),
    // Inject query parts into action for use both in `onSubKey` and reducer.
    replace_action_default((action) => {
      if (action.query) {
        return {
          ...action,
          ...get_query_parts_default(action.query)
        };
      }
      return action;
    }),
    on_sub_key_default("context"),
    // Queries shape is shared, but keyed by query `stableKey` part. Original
    // reducer tracks only a single query object.
    on_sub_key_default("stableKey")
  ])((state = {}, action) => {
    const { type, page, perPage, key = DEFAULT_ENTITY_KEY } = action;
    if (type !== "RECEIVE_ITEMS") {
      return state;
    }
    return {
      itemIds: getMergedItemIds(
        state?.itemIds || [],
        action.items.map((item) => item?.[key]).filter(Boolean),
        page,
        perPage
      ),
      meta: action.meta
    };
  });
  var queries = (state = {}, action) => {
    switch (action.type) {
      case "RECEIVE_ITEMS":
        return receiveQueries(state, action);
      case "REMOVE_ITEMS":
        const removedItems = action.itemIds.reduce((result, itemId) => {
          result[itemId] = true;
          return result;
        }, {});
        return Object.fromEntries(
          Object.entries(state).map(
            ([queryGroup, contextQueries]) => [
              queryGroup,
              Object.fromEntries(
                Object.entries(contextQueries).map(
                  ([query, queryItems]) => [
                    query,
                    {
                      ...queryItems,
                      itemIds: queryItems.itemIds.filter(
                        (queryId) => !removedItems[queryId]
                      )
                    }
                  ]
                )
              )
            ]
          )
        );
      default:
        return state;
    }
  };
  var reducer_default = (0, import_data5.combineReducers)({
    items,
    itemIsComplete,
    queries
  });

  // packages/core-data/build-module/reducer.mjs
  function users(state = { byId: {}, queries: {} }, action) {
    switch (action.type) {
      case "RECEIVE_USER_QUERY":
        return {
          byId: {
            ...state.byId,
            // Key users by their ID.
            ...action.users.reduce(
              (newUsers, user) => ({
                ...newUsers,
                [user.id]: user
              }),
              {}
            )
          },
          queries: {
            ...state.queries,
            [action.queryID]: action.users.map((user) => user.id)
          }
        };
    }
    return state;
  }
  function currentUser(state = {}, action) {
    switch (action.type) {
      case "RECEIVE_CURRENT_USER":
        return action.currentUser;
    }
    return state;
  }
  function currentTheme(state = void 0, action) {
    switch (action.type) {
      case "RECEIVE_CURRENT_THEME":
        return action.currentTheme.stylesheet;
    }
    return state;
  }
  function currentGlobalStylesId(state = void 0, action) {
    switch (action.type) {
      case "RECEIVE_CURRENT_GLOBAL_STYLES_ID":
        return action.id;
    }
    return state;
  }
  function themeBaseGlobalStyles(state = {}, action) {
    switch (action.type) {
      case "RECEIVE_THEME_GLOBAL_STYLES":
        return {
          ...state,
          [action.stylesheet]: action.globalStyles
        };
    }
    return state;
  }
  function themeGlobalStyleVariations(state = {}, action) {
    switch (action.type) {
      case "RECEIVE_THEME_GLOBAL_STYLE_VARIATIONS":
        return {
          ...state,
          [action.stylesheet]: action.variations
        };
    }
    return state;
  }
  var withMultiEntityRecordEdits = (reducer) => (state, action) => {
    if (action.type === "UNDO" || action.type === "REDO") {
      const { record } = action;
      let newState = state;
      record.forEach(({ id: { kind, name, recordId }, changes }) => {
        newState = reducer(newState, {
          type: "EDIT_ENTITY_RECORD",
          kind,
          name,
          recordId,
          edits: Object.entries(changes).reduce(
            (acc, [key, value]) => {
              acc[key] = action.type === "UNDO" ? value.from : value.to;
              return acc;
            },
            {}
          )
        });
      });
      return newState;
    }
    return reducer(state, action);
  };
  function entity(entityConfig) {
    return (0, import_compose2.compose)([
      withMultiEntityRecordEdits,
      // Limit to matching action type so we don't attempt to replace action on
      // an unhandled action.
      if_matching_action_default(
        (action) => action.name && action.kind && action.name === entityConfig.name && action.kind === entityConfig.kind
      ),
      // Inject the entity config into the action.
      replace_action_default((action) => {
        return {
          key: entityConfig.key || DEFAULT_ENTITY_KEY,
          ...action
        };
      })
    ])(
      (0, import_data6.combineReducers)({
        queriedData: reducer_default,
        edits: (state = {}, action) => {
          switch (action.type) {
            case "RECEIVE_ITEMS":
              const context = action?.query?.context ?? "default";
              if (context !== "default") {
                return state;
              }
              const nextState = { ...state };
              for (const record of action.items) {
                const recordId = record?.[action.key];
                const edits = nextState[recordId];
                if (!edits) {
                  continue;
                }
                const nextEdits2 = Object.keys(edits).reduce(
                  (acc, key) => {
                    if (
                      // Edits are the "raw" attribute values, but records may have
                      // objects with more properties, so we use `get` here for the
                      // comparison.
                      !(0, import_es64.default)(
                        edits[key],
                        record[key]?.raw ?? record[key]
                      ) && // Sometimes the server alters the sent value which means
                      // we need to also remove the edits before the api request.
                      (!action.persistedEdits || !(0, import_es64.default)(
                        edits[key],
                        action.persistedEdits[key]
                      ))
                    ) {
                      acc[key] = edits[key];
                    }
                    return acc;
                  },
                  {}
                );
                if (Object.keys(nextEdits2).length) {
                  nextState[recordId] = nextEdits2;
                } else {
                  delete nextState[recordId];
                }
              }
              return nextState;
            case "EDIT_ENTITY_RECORD":
              const nextEdits = {
                ...state[action.recordId],
                ...action.edits
              };
              Object.keys(nextEdits).forEach((key) => {
                if (nextEdits[key] === void 0) {
                  delete nextEdits[key];
                }
              });
              return {
                ...state,
                [action.recordId]: nextEdits
              };
          }
          return state;
        },
        saving: (state = {}, action) => {
          switch (action.type) {
            case "SAVE_ENTITY_RECORD_START":
            case "SAVE_ENTITY_RECORD_FINISH":
              return {
                ...state,
                [action.recordId]: {
                  pending: action.type === "SAVE_ENTITY_RECORD_START",
                  error: action.error,
                  isAutosave: action.isAutosave
                }
              };
          }
          return state;
        },
        deleting: (state = {}, action) => {
          switch (action.type) {
            case "DELETE_ENTITY_RECORD_START":
            case "DELETE_ENTITY_RECORD_FINISH":
              return {
                ...state,
                [action.recordId]: {
                  pending: action.type === "DELETE_ENTITY_RECORD_START",
                  error: action.error
                }
              };
          }
          return state;
        },
        revisions: (state = {}, action) => {
          if (action.type === "RECEIVE_ITEM_REVISIONS") {
            const recordKey = action.recordKey;
            delete action.recordKey;
            const newState = reducer_default(state[recordKey], {
              ...action,
              type: "RECEIVE_ITEMS"
            });
            return {
              ...state,
              [recordKey]: newState
            };
          }
          if (action.type === "REMOVE_ITEMS") {
            return Object.fromEntries(
              Object.entries(state).filter(
                ([id]) => !action.itemIds.some((itemId) => {
                  if (Number.isInteger(itemId)) {
                    return itemId === +id;
                  }
                  return itemId === id;
                })
              )
            );
          }
          return state;
        }
      })
    );
  }
  function entitiesConfig(state = rootEntitiesConfig, action) {
    switch (action.type) {
      case "ADD_ENTITIES":
        return [...state, ...action.entities];
    }
    return state;
  }
  var entities = (state = {}, action) => {
    const newConfig = entitiesConfig(state.config, action);
    let entitiesDataReducer = state.reducer;
    if (!entitiesDataReducer || newConfig !== state.config) {
      const entitiesByKind = newConfig.reduce((acc, record) => {
        const { kind } = record;
        if (!acc[kind]) {
          acc[kind] = [];
        }
        acc[kind].push(record);
        return acc;
      }, {});
      entitiesDataReducer = (0, import_data6.combineReducers)(
        Object.fromEntries(
          Object.entries(entitiesByKind).map(
            ([kind, subEntities]) => {
              const kindReducer = (0, import_data6.combineReducers)(
                Object.fromEntries(
                  subEntities.map((entityConfig) => [
                    entityConfig.name,
                    entity(entityConfig)
                  ])
                )
              );
              return [kind, kindReducer];
            }
          )
        )
      );
    }
    const newData = entitiesDataReducer(state.records, action);
    if (newData === state.records && newConfig === state.config && entitiesDataReducer === state.reducer) {
      return state;
    }
    return {
      reducer: entitiesDataReducer,
      records: newData,
      config: newConfig
    };
  };
  function undoManager(state = (0, import_undo_manager.createUndoManager)()) {
    return state;
  }
  function editsReference(state = {}, action) {
    switch (action.type) {
      case "EDIT_ENTITY_RECORD":
      case "UNDO":
      case "REDO":
        return {};
    }
    return state;
  }
  function embedPreviews(state = {}, action) {
    switch (action.type) {
      case "RECEIVE_EMBED_PREVIEW":
        const { url, preview } = action;
        return {
          ...state,
          [url]: preview
        };
    }
    return state;
  }
  function userPermissions(state = {}, action) {
    switch (action.type) {
      case "RECEIVE_USER_PERMISSION":
        return {
          ...state,
          [action.key]: action.isAllowed
        };
      case "RECEIVE_USER_PERMISSIONS":
        return {
          ...state,
          ...action.permissions
        };
    }
    return state;
  }
  function autosaves(state = {}, action) {
    switch (action.type) {
      case "RECEIVE_AUTOSAVES":
        const { postId, autosaves: autosavesData } = action;
        return {
          ...state,
          [postId]: autosavesData
        };
    }
    return state;
  }
  function blockPatterns(state = [], action) {
    switch (action.type) {
      case "RECEIVE_BLOCK_PATTERNS":
        return action.patterns;
    }
    return state;
  }
  function blockPatternCategories(state = [], action) {
    switch (action.type) {
      case "RECEIVE_BLOCK_PATTERN_CATEGORIES":
        return action.categories;
    }
    return state;
  }
  function userPatternCategories(state = [], action) {
    switch (action.type) {
      case "RECEIVE_USER_PATTERN_CATEGORIES":
        return action.patternCategories;
    }
    return state;
  }
  function navigationFallbackId(state = null, action) {
    switch (action.type) {
      case "RECEIVE_NAVIGATION_FALLBACK_ID":
        return action.fallbackId;
    }
    return state;
  }
  function themeGlobalStyleRevisions(state = {}, action) {
    switch (action.type) {
      case "RECEIVE_THEME_GLOBAL_STYLE_REVISIONS":
        return {
          ...state,
          [action.currentId]: action.revisions
        };
    }
    return state;
  }
  function defaultTemplates(state = {}, action) {
    switch (action.type) {
      case "RECEIVE_DEFAULT_TEMPLATE":
        return {
          ...state,
          [JSON.stringify(action.query)]: action.templateId
        };
    }
    return state;
  }
  function registeredPostMeta(state = {}, action) {
    switch (action.type) {
      case "RECEIVE_REGISTERED_POST_META":
        return {
          ...state,
          [action.postType]: action.registeredPostMeta
        };
    }
    return state;
  }
  function editorSettings(state = null, action) {
    switch (action.type) {
      case "RECEIVE_EDITOR_SETTINGS":
        return action.settings;
    }
    return state;
  }
  function editorAssets(state = null, action) {
    switch (action.type) {
      case "RECEIVE_EDITOR_ASSETS":
        return action.assets;
    }
    return state;
  }
  function syncConnectionStatuses(state = {}, action) {
    switch (action.type) {
      case "SET_SYNC_CONNECTION_STATUS": {
        const key = `${action.kind}/${action.name}:${action.key}`;
        return {
          ...state,
          [key]: action.status
        };
      }
      case "CLEAR_SYNC_CONNECTION_STATUS": {
        const key = `${action.kind}/${action.name}:${action.key}`;
        const { [key]: _, ...rest } = state;
        return rest;
      }
    }
    return state;
  }
  var reducer_default2 = (0, import_data6.combineReducers)({
    users,
    currentTheme,
    currentGlobalStylesId,
    currentUser,
    themeGlobalStyleVariations,
    themeBaseGlobalStyles,
    themeGlobalStyleRevisions,
    entities,
    editsReference,
    undoManager,
    embedPreviews,
    userPermissions,
    autosaves,
    blockPatterns,
    blockPatternCategories,
    userPatternCategories,
    navigationFallbackId,
    defaultTemplates,
    registeredPostMeta,
    editorSettings,
    editorAssets,
    syncConnectionStatuses
  });

  // packages/core-data/build-module/selectors.mjs
  var selectors_exports = {};
  __export(selectors_exports, {
    __experimentalGetCurrentGlobalStylesId: () => __experimentalGetCurrentGlobalStylesId,
    __experimentalGetCurrentThemeBaseGlobalStyles: () => __experimentalGetCurrentThemeBaseGlobalStyles,
    __experimentalGetCurrentThemeGlobalStylesVariations: () => __experimentalGetCurrentThemeGlobalStylesVariations,
    __experimentalGetDirtyEntityRecords: () => __experimentalGetDirtyEntityRecords,
    __experimentalGetEntitiesBeingSaved: () => __experimentalGetEntitiesBeingSaved,
    __experimentalGetEntityRecordNoResolver: () => __experimentalGetEntityRecordNoResolver,
    canUser: () => canUser,
    canUserEditEntityRecord: () => canUserEditEntityRecord,
    getAuthors: () => getAuthors,
    getAutosave: () => getAutosave,
    getAutosaves: () => getAutosaves,
    getBlockPatternCategories: () => getBlockPatternCategories,
    getBlockPatterns: () => getBlockPatterns,
    getCurrentTheme: () => getCurrentTheme,
    getCurrentThemeGlobalStylesRevisions: () => getCurrentThemeGlobalStylesRevisions,
    getCurrentUser: () => getCurrentUser,
    getDefaultTemplateId: () => getDefaultTemplateId,
    getEditedEntityRecord: () => getEditedEntityRecord,
    getEmbedPreview: () => getEmbedPreview,
    getEntitiesByKind: () => getEntitiesByKind,
    getEntitiesConfig: () => getEntitiesConfig,
    getEntity: () => getEntity,
    getEntityConfig: () => getEntityConfig,
    getEntityRecord: () => getEntityRecord,
    getEntityRecordEdits: () => getEntityRecordEdits,
    getEntityRecordNonTransientEdits: () => getEntityRecordNonTransientEdits,
    getEntityRecords: () => getEntityRecords,
    getEntityRecordsTotalItems: () => getEntityRecordsTotalItems,
    getEntityRecordsTotalPages: () => getEntityRecordsTotalPages,
    getLastEntityDeleteError: () => getLastEntityDeleteError,
    getLastEntitySaveError: () => getLastEntitySaveError,
    getRawEntityRecord: () => getRawEntityRecord,
    getRedoEdit: () => getRedoEdit,
    getReferenceByDistinctEdits: () => getReferenceByDistinctEdits,
    getRevision: () => getRevision,
    getRevisions: () => getRevisions,
    getSyncConnectionStatus: () => getSyncConnectionStatus,
    getThemeSupports: () => getThemeSupports,
    getUndoEdit: () => getUndoEdit,
    getUserPatternCategories: () => getUserPatternCategories,
    getUserQueryResults: () => getUserQueryResults,
    hasEditsForEntityRecord: () => hasEditsForEntityRecord,
    hasEntityRecord: () => hasEntityRecord,
    hasEntityRecords: () => hasEntityRecords,
    hasFetchedAutosaves: () => hasFetchedAutosaves,
    hasRedo: () => hasRedo,
    hasUndo: () => hasUndo,
    isAutosavingEntityRecord: () => isAutosavingEntityRecord,
    isDeletingEntityRecord: () => isDeletingEntityRecord,
    isPreviewEmbedFallback: () => isPreviewEmbedFallback,
    isRequestingEmbedPreview: () => isRequestingEmbedPreview,
    isSavingEntityRecord: () => isSavingEntityRecord
  });
  var import_data8 = __toESM(require_data(), 1);
  var import_url2 = __toESM(require_url(), 1);
  var import_deprecated2 = __toESM(require_deprecated(), 1);

  // packages/core-data/build-module/private-selectors.mjs
  var private_selectors_exports = {};
  __export(private_selectors_exports, {
    getBlockPatternsForPostType: () => getBlockPatternsForPostType,
    getEditorAssets: () => getEditorAssets,
    getEditorSettings: () => getEditorSettings,
    getEntityRecordPermissions: () => getEntityRecordPermissions,
    getEntityRecordsPermissions: () => getEntityRecordsPermissions,
    getHomePage: () => getHomePage,
    getNavigationFallbackId: () => getNavigationFallbackId,
    getPostsPageId: () => getPostsPageId,
    getRegisteredPostMeta: () => getRegisteredPostMeta,
    getTemplateId: () => getTemplateId,
    getUndoManager: () => getUndoManager
  });
  var import_data7 = __toESM(require_data(), 1);

  // packages/core-data/build-module/utils/log-entity-deprecation.mjs
  var import_deprecated = __toESM(require_deprecated(), 1);
  var loggedAlready = false;
  function logEntityDeprecation(kind, name, functionName, {
    alternativeFunctionName,
    isShorthandSelector = false
  } = {}) {
    const deprecation = deprecatedEntities[kind]?.[name];
    if (!deprecation) {
      return;
    }
    if (!loggedAlready) {
      const { alternative } = deprecation;
      const message = isShorthandSelector ? `'${functionName}'` : `The '${kind}', '${name}' entity (used via '${functionName}')`;
      let alternativeMessage = `the '${alternative.kind}', '${alternative.name}' entity`;
      if (alternativeFunctionName) {
        alternativeMessage += ` via the '${alternativeFunctionName}' function`;
      }
      (0, import_deprecated.default)(message, {
        ...deprecation,
        alternative: alternativeMessage
      });
    }
    loggedAlready = true;
    setTimeout(() => {
      loggedAlready = false;
    }, 0);
  }

  // packages/core-data/build-module/private-selectors.mjs
  function getUndoManager(state) {
    return getSyncManager()?.undoManager ?? state.undoManager;
  }
  function getNavigationFallbackId(state) {
    return state.navigationFallbackId;
  }
  var getBlockPatternsForPostType = (0, import_data7.createRegistrySelector)(
    (select3) => (0, import_data7.createSelector)(
      (state, postType) => select3(STORE_NAME).getBlockPatterns().filter(
        ({ postTypes }) => !postTypes || Array.isArray(postTypes) && postTypes.includes(postType)
      ),
      () => [select3(STORE_NAME).getBlockPatterns()]
    )
  );
  var getEntityRecordsPermissions = (0, import_data7.createRegistrySelector)(
    (select3) => (0, import_data7.createSelector)(
      (state, kind, name, ids) => {
        const normalizedIds = Array.isArray(ids) ? ids : [ids];
        return normalizedIds.map((id) => ({
          delete: select3(STORE_NAME).canUser("delete", {
            kind,
            name,
            id
          }),
          update: select3(STORE_NAME).canUser("update", {
            kind,
            name,
            id
          })
        }));
      },
      (state) => [state.userPermissions]
    )
  );
  function getEntityRecordPermissions(state, kind, name, id) {
    logEntityDeprecation(kind, name, "getEntityRecordPermissions");
    return getEntityRecordsPermissions(state, kind, name, id)[0];
  }
  function getRegisteredPostMeta(state, postType) {
    return state.registeredPostMeta?.[postType] ?? {};
  }
  function normalizePageId(value) {
    if (!value || !["number", "string"].includes(typeof value)) {
      return null;
    }
    if (Number(value) === 0) {
      return null;
    }
    return value.toString();
  }
  var getHomePage = (0, import_data7.createRegistrySelector)(
    (select3) => (0, import_data7.createSelector)(
      () => {
        const siteData = select3(STORE_NAME).getEntityRecord(
          "root",
          "__unstableBase"
        );
        if (!siteData) {
          return null;
        }
        const homepageId = siteData?.show_on_front === "page" ? normalizePageId(siteData.page_on_front) : null;
        if (homepageId) {
          return { postType: "page", postId: homepageId };
        }
        const frontPageTemplateId = select3(
          STORE_NAME
        ).getDefaultTemplateId({
          slug: "front-page"
        });
        if (!frontPageTemplateId) {
          return null;
        }
        return { postType: "wp_template", postId: frontPageTemplateId };
      },
      (state) => [
        // Even though getDefaultTemplateId.shouldInvalidate returns true when root/site changes,
        // it doesn't seem to invalidate this cache, I'm not sure why.
        getEntityRecord(state, "root", "site"),
        getEntityRecord(state, "root", "__unstableBase"),
        getDefaultTemplateId(state, {
          slug: "front-page"
        })
      ]
    )
  );
  var getPostsPageId = (0, import_data7.createRegistrySelector)((select3) => () => {
    const siteData = select3(STORE_NAME).getEntityRecord(
      "root",
      "__unstableBase"
    );
    return siteData?.show_on_front === "page" ? normalizePageId(siteData.page_for_posts) : null;
  });
  var getTemplateId = (0, import_data7.createRegistrySelector)(
    (select3) => (state, postType, postId) => {
      const homepage = unlock(select3(STORE_NAME)).getHomePage();
      if (!homepage) {
        return;
      }
      if (postType === "page" && postType === homepage?.postType && postId.toString() === homepage?.postId) {
        const templates = select3(STORE_NAME).getEntityRecords(
          "postType",
          "wp_template",
          {
            per_page: -1
          }
        );
        if (!templates) {
          return;
        }
        const id = templates.find(({ slug }) => slug === "front-page")?.id;
        if (id) {
          return id;
        }
      }
      const editedEntity = select3(STORE_NAME).getEditedEntityRecord(
        "postType",
        postType,
        postId
      );
      if (!editedEntity) {
        return;
      }
      const postsPageId = unlock(select3(STORE_NAME)).getPostsPageId();
      if (postType === "page" && postsPageId === postId.toString()) {
        return select3(STORE_NAME).getDefaultTemplateId({
          slug: "home"
        });
      }
      const currentTemplateSlug = editedEntity.template;
      if (currentTemplateSlug) {
        const currentTemplate = select3(STORE_NAME).getEntityRecords("postType", "wp_template", {
          per_page: -1
        })?.find(({ slug }) => slug === currentTemplateSlug);
        if (currentTemplate) {
          return currentTemplate.id;
        }
      }
      let slugToCheck;
      if (editedEntity.slug) {
        slugToCheck = postType === "page" ? `${postType}-${editedEntity.slug}` : `single-${postType}-${editedEntity.slug}`;
      } else {
        slugToCheck = postType === "page" ? "page" : `single-${postType}`;
      }
      return select3(STORE_NAME).getDefaultTemplateId({
        slug: slugToCheck
      });
    }
  );
  function getEditorSettings(state) {
    return state.editorSettings;
  }
  function getEditorAssets(state) {
    return state.editorAssets;
  }

  // packages/core-data/build-module/selectors.mjs
  var EMPTY_OBJECT = {};
  var isRequestingEmbedPreview = (0, import_data8.createRegistrySelector)(
    (select3) => (state, url) => {
      return select3(STORE_NAME).isResolving("getEmbedPreview", [
        url
      ]);
    }
  );
  function getAuthors(state, query) {
    (0, import_deprecated2.default)("select( 'core' ).getAuthors()", {
      since: "5.9",
      alternative: "select( 'core' ).getUsers({ who: 'authors' })"
    });
    const path = (0, import_url2.addQueryArgs)(
      "/wp/v2/users/?who=authors&per_page=100",
      query
    );
    return getUserQueryResults(state, path);
  }
  function getCurrentUser(state) {
    return state.currentUser;
  }
  var getUserQueryResults = (0, import_data8.createSelector)(
    (state, queryID) => {
      const queryResults = state.users.queries[queryID] ?? [];
      return queryResults.map((id) => state.users.byId[id]);
    },
    (state, queryID) => [
      state.users.queries[queryID],
      state.users.byId
    ]
  );
  function getEntitiesByKind(state, kind) {
    (0, import_deprecated2.default)("wp.data.select( 'core' ).getEntitiesByKind()", {
      since: "6.0",
      alternative: "wp.data.select( 'core' ).getEntitiesConfig()"
    });
    return getEntitiesConfig(state, kind);
  }
  var getEntitiesConfig = (0, import_data8.createSelector)(
    (state, kind) => state.entities.config.filter((entity2) => entity2.kind === kind),
    /* eslint-disable @typescript-eslint/no-unused-vars */
    (state, kind) => state.entities.config
    /* eslint-enable @typescript-eslint/no-unused-vars */
  );
  function getEntity(state, kind, name) {
    (0, import_deprecated2.default)("wp.data.select( 'core' ).getEntity()", {
      since: "6.0",
      alternative: "wp.data.select( 'core' ).getEntityConfig()"
    });
    return getEntityConfig(state, kind, name);
  }
  function getEntityConfig(state, kind, name) {
    logEntityDeprecation(kind, name, "getEntityConfig");
    return state.entities.config?.find(
      (config) => config.kind === kind && config.name === name
    );
  }
  var getEntityRecord = (0, import_data8.createSelector)(
    ((state, kind, name, key, query) => {
      logEntityDeprecation(kind, name, "getEntityRecord");
      const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
      if (!queriedState) {
        return void 0;
      }
      const context = query?.context ?? "default";
      if (!query || !query._fields) {
        if (!queriedState.itemIsComplete[context]?.[key]) {
          return void 0;
        }
        return queriedState.items[context][key];
      }
      const item = queriedState.items[context]?.[key];
      if (!item) {
        return item;
      }
      const filteredItem = {};
      const fields = get_normalized_comma_separable_default(query._fields) ?? [];
      for (let f = 0; f < fields.length; f++) {
        const field = fields[f].split(".");
        let value = item;
        field.forEach((fieldName) => {
          value = value?.[fieldName];
        });
        setNestedValue(filteredItem, field, value);
      }
      return filteredItem;
    }),
    (state, kind, name, recordId, query) => {
      const context = query?.context ?? "default";
      const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
      return [
        queriedState?.items[context]?.[recordId],
        queriedState?.itemIsComplete[context]?.[recordId]
      ];
    }
  );
  getEntityRecord.__unstableNormalizeArgs = (args) => {
    const newArgs = [...args];
    const recordKey = newArgs?.[2];
    newArgs[2] = isNumericID(recordKey) ? Number(recordKey) : recordKey;
    return newArgs;
  };
  function hasEntityRecord(state, kind, name, key, query) {
    const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
    if (!queriedState) {
      return false;
    }
    const context = query?.context ?? "default";
    if (!query || !query._fields) {
      return !!queriedState.itemIsComplete[context]?.[key];
    }
    const item = queriedState.items[context]?.[key];
    if (!item) {
      return false;
    }
    const fields = get_normalized_comma_separable_default(query._fields) ?? [];
    for (let i = 0; i < fields.length; i++) {
      const path = fields[i].split(".");
      let value = item;
      for (let p = 0; p < path.length; p++) {
        const part = path[p];
        if (!value || !Object.hasOwn(value, part)) {
          return false;
        }
        value = value[part];
      }
    }
    return true;
  }
  function __experimentalGetEntityRecordNoResolver(state, kind, name, key) {
    return getEntityRecord(state, kind, name, key);
  }
  var getRawEntityRecord = (0, import_data8.createSelector)(
    (state, kind, name, key) => {
      logEntityDeprecation(kind, name, "getRawEntityRecord");
      const record = getEntityRecord(
        state,
        kind,
        name,
        key
      );
      return record && Object.keys(record).reduce((accumulator, _key) => {
        if (isRawAttribute(getEntityConfig(state, kind, name), _key)) {
          accumulator[_key] = record[_key]?.raw !== void 0 ? record[_key]?.raw : record[_key];
        } else {
          accumulator[_key] = record[_key];
        }
        return accumulator;
      }, {});
    },
    (state, kind, name, recordId, query) => {
      const context = query?.context ?? "default";
      return [
        state.entities.config,
        state.entities.records?.[kind]?.[name]?.queriedData?.items[context]?.[recordId],
        state.entities.records?.[kind]?.[name]?.queriedData?.itemIsComplete[context]?.[recordId]
      ];
    }
  );
  function hasEntityRecords(state, kind, name, query) {
    logEntityDeprecation(kind, name, "hasEntityRecords");
    return Array.isArray(getEntityRecords(state, kind, name, query));
  }
  var getEntityRecords = ((state, kind, name, query) => {
    logEntityDeprecation(kind, name, "getEntityRecords");
    const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
    if (!queriedState) {
      return null;
    }
    return getQueriedItems(queriedState, query);
  });
  var getEntityRecordsTotalItems = (state, kind, name, query) => {
    logEntityDeprecation(kind, name, "getEntityRecordsTotalItems");
    const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
    if (!queriedState) {
      return null;
    }
    return getQueriedTotalItems(queriedState, query);
  };
  var getEntityRecordsTotalPages = (state, kind, name, query) => {
    logEntityDeprecation(kind, name, "getEntityRecordsTotalPages");
    const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
    if (!queriedState) {
      return null;
    }
    if (query?.per_page === -1) {
      return 1;
    }
    const totalItems = getQueriedTotalItems(queriedState, query);
    if (!totalItems) {
      return totalItems;
    }
    if (!query?.per_page) {
      return getQueriedTotalPages(queriedState, query);
    }
    return Math.ceil(totalItems / query.per_page);
  };
  var __experimentalGetDirtyEntityRecords = (0, import_data8.createSelector)(
    (state) => {
      const {
        entities: { records }
      } = state;
      const dirtyRecords = [];
      Object.keys(records).forEach((kind) => {
        Object.keys(records[kind]).forEach((name) => {
          const primaryKeys = Object.keys(records[kind][name].edits).filter(
            (primaryKey) => (
              // The entity record must exist (not be deleted),
              // and it must have edits.
              getEntityRecord(state, kind, name, primaryKey) && hasEditsForEntityRecord(state, kind, name, primaryKey)
            )
          );
          if (primaryKeys.length) {
            const entityConfig = getEntityConfig(state, kind, name);
            primaryKeys.forEach((primaryKey) => {
              const entityRecord = getEditedEntityRecord(
                state,
                kind,
                name,
                primaryKey
              );
              dirtyRecords.push({
                // We avoid using primaryKey because it's transformed into a string
                // when it's used as an object key.
                key: entityRecord ? entityRecord[entityConfig.key || DEFAULT_ENTITY_KEY] : void 0,
                title: entityConfig?.getTitle?.(entityRecord) || "",
                name,
                kind
              });
            });
          }
        });
      });
      return dirtyRecords;
    },
    (state) => [state.entities.records]
  );
  var __experimentalGetEntitiesBeingSaved = (0, import_data8.createSelector)(
    (state) => {
      const {
        entities: { records }
      } = state;
      const recordsBeingSaved = [];
      Object.keys(records).forEach((kind) => {
        Object.keys(records[kind]).forEach((name) => {
          const primaryKeys = Object.keys(records[kind][name].saving).filter(
            (primaryKey) => isSavingEntityRecord(state, kind, name, primaryKey)
          );
          if (primaryKeys.length) {
            const entityConfig = getEntityConfig(state, kind, name);
            primaryKeys.forEach((primaryKey) => {
              const entityRecord = getEditedEntityRecord(
                state,
                kind,
                name,
                primaryKey
              );
              recordsBeingSaved.push({
                // We avoid using primaryKey because it's transformed into a string
                // when it's used as an object key.
                key: entityRecord ? entityRecord[entityConfig.key || DEFAULT_ENTITY_KEY] : void 0,
                title: entityConfig?.getTitle?.(entityRecord) || "",
                name,
                kind
              });
            });
          }
        });
      });
      return recordsBeingSaved;
    },
    (state) => [state.entities.records]
  );
  function getEntityRecordEdits(state, kind, name, recordId) {
    logEntityDeprecation(kind, name, "getEntityRecordEdits");
    return state.entities.records?.[kind]?.[name]?.edits?.[recordId];
  }
  var getEntityRecordNonTransientEdits = (0, import_data8.createSelector)(
    (state, kind, name, recordId) => {
      logEntityDeprecation(kind, name, "getEntityRecordNonTransientEdits");
      const { transientEdits } = getEntityConfig(state, kind, name) || {};
      const edits = getEntityRecordEdits(state, kind, name, recordId) || {};
      if (!transientEdits) {
        return edits;
      }
      return Object.keys(edits).reduce((acc, key) => {
        if (!transientEdits[key]) {
          acc[key] = edits[key];
        }
        return acc;
      }, {});
    },
    (state, kind, name, recordId) => [
      state.entities.config,
      state.entities.records?.[kind]?.[name]?.edits?.[recordId]
    ]
  );
  function hasEditsForEntityRecord(state, kind, name, recordId) {
    logEntityDeprecation(kind, name, "hasEditsForEntityRecord");
    return isSavingEntityRecord(state, kind, name, recordId) || Object.keys(
      getEntityRecordNonTransientEdits(state, kind, name, recordId)
    ).length > 0;
  }
  var getEditedEntityRecord = (0, import_data8.createSelector)(
    (state, kind, name, recordId) => {
      logEntityDeprecation(kind, name, "getEditedEntityRecord");
      const raw = getRawEntityRecord(state, kind, name, recordId);
      const edited = getEntityRecordEdits(state, kind, name, recordId);
      if (!raw && !edited) {
        return false;
      }
      return {
        ...raw,
        ...edited
      };
    },
    (state, kind, name, recordId, query) => {
      const context = query?.context ?? "default";
      return [
        state.entities.config,
        state.entities.records?.[kind]?.[name]?.queriedData.items[context]?.[recordId],
        state.entities.records?.[kind]?.[name]?.queriedData.itemIsComplete[context]?.[recordId],
        state.entities.records?.[kind]?.[name]?.edits?.[recordId]
      ];
    }
  );
  function isAutosavingEntityRecord(state, kind, name, recordId) {
    logEntityDeprecation(kind, name, "isAutosavingEntityRecord");
    const { pending, isAutosave } = state.entities.records?.[kind]?.[name]?.saving?.[recordId] ?? {};
    return Boolean(pending && isAutosave);
  }
  function isSavingEntityRecord(state, kind, name, recordId) {
    logEntityDeprecation(kind, name, "isSavingEntityRecord");
    return state.entities.records?.[kind]?.[name]?.saving?.[recordId]?.pending ?? false;
  }
  function isDeletingEntityRecord(state, kind, name, recordId) {
    logEntityDeprecation(kind, name, "isDeletingEntityRecord");
    return state.entities.records?.[kind]?.[name]?.deleting?.[recordId]?.pending ?? false;
  }
  function getLastEntitySaveError(state, kind, name, recordId) {
    logEntityDeprecation(kind, name, "getLastEntitySaveError");
    return state.entities.records?.[kind]?.[name]?.saving?.[recordId]?.error;
  }
  function getLastEntityDeleteError(state, kind, name, recordId) {
    logEntityDeprecation(kind, name, "getLastEntityDeleteError");
    return state.entities.records?.[kind]?.[name]?.deleting?.[recordId]?.error;
  }
  function getUndoEdit(state) {
    (0, import_deprecated2.default)("select( 'core' ).getUndoEdit()", {
      since: "6.3"
    });
    return void 0;
  }
  function getRedoEdit(state) {
    (0, import_deprecated2.default)("select( 'core' ).getRedoEdit()", {
      since: "6.3"
    });
    return void 0;
  }
  function hasUndo(state) {
    return getUndoManager(state).hasUndo();
  }
  function hasRedo(state) {
    return getUndoManager(state).hasRedo();
  }
  function getCurrentTheme(state) {
    if (!state.currentTheme) {
      return null;
    }
    return getEntityRecord(state, "root", "theme", state.currentTheme);
  }
  function __experimentalGetCurrentGlobalStylesId(state) {
    return state.currentGlobalStylesId;
  }
  function getThemeSupports(state) {
    return getCurrentTheme(state)?.theme_supports ?? EMPTY_OBJECT;
  }
  function getEmbedPreview(state, url) {
    return state.embedPreviews[url];
  }
  function isPreviewEmbedFallback(state, url) {
    const preview = state.embedPreviews[url];
    const oEmbedLinkCheck = '<a href="' + url + '">' + url + "</a>";
    if (!preview) {
      return false;
    }
    return preview.html === oEmbedLinkCheck;
  }
  function canUser(state, action, resource, id) {
    const isEntity = typeof resource === "object";
    if (isEntity && (!resource.kind || !resource.name)) {
      return false;
    }
    if (isEntity) {
      logEntityDeprecation(resource.kind, resource.name, "canUser");
    }
    const key = getUserPermissionCacheKey(action, resource, id);
    return state.userPermissions[key];
  }
  function canUserEditEntityRecord(state, kind, name, recordId) {
    (0, import_deprecated2.default)(`wp.data.select( 'core' ).canUserEditEntityRecord()`, {
      since: "6.7",
      alternative: `wp.data.select( 'core' ).canUser( 'update', { kind, name, id } )`
    });
    return canUser(state, "update", { kind, name, id: recordId });
  }
  function getAutosaves(state, postType, postId) {
    return state.autosaves[postId];
  }
  function getAutosave(state, postType, postId, authorId) {
    if (authorId === void 0) {
      return;
    }
    const autosaves2 = state.autosaves[postId];
    return autosaves2?.find(
      (autosave) => autosave.author === authorId
    );
  }
  var hasFetchedAutosaves = (0, import_data8.createRegistrySelector)(
    (select3) => (state, postType, postId) => {
      return select3(STORE_NAME).hasFinishedResolution("getAutosaves", [
        postType,
        postId
      ]);
    }
  );
  function getReferenceByDistinctEdits(state) {
    return state.editsReference;
  }
  function __experimentalGetCurrentThemeBaseGlobalStyles(state) {
    const currentTheme2 = getCurrentTheme(state);
    if (!currentTheme2) {
      return null;
    }
    return state.themeBaseGlobalStyles[currentTheme2.stylesheet];
  }
  function __experimentalGetCurrentThemeGlobalStylesVariations(state) {
    const currentTheme2 = getCurrentTheme(state);
    if (!currentTheme2) {
      return null;
    }
    return state.themeGlobalStyleVariations[currentTheme2.stylesheet];
  }
  function getBlockPatterns(state) {
    return state.blockPatterns;
  }
  function getBlockPatternCategories(state) {
    return state.blockPatternCategories;
  }
  function getUserPatternCategories(state) {
    return state.userPatternCategories;
  }
  function getCurrentThemeGlobalStylesRevisions(state) {
    (0, import_deprecated2.default)("select( 'core' ).getCurrentThemeGlobalStylesRevisions()", {
      since: "6.5.0",
      alternative: "select( 'core' ).getRevisions( 'root', 'globalStyles', ${ recordKey } )"
    });
    const currentGlobalStylesId2 = __experimentalGetCurrentGlobalStylesId(state);
    if (!currentGlobalStylesId2) {
      return null;
    }
    return state.themeGlobalStyleRevisions[currentGlobalStylesId2];
  }
  function getDefaultTemplateId(state, query) {
    return state.defaultTemplates[JSON.stringify(query)];
  }
  var getRevisions = (state, kind, name, recordKey, query) => {
    logEntityDeprecation(kind, name, "getRevisions");
    const queriedStateRevisions = state.entities.records?.[kind]?.[name]?.revisions?.[recordKey];
    if (!queriedStateRevisions) {
      return null;
    }
    return getQueriedItems(queriedStateRevisions, query);
  };
  var getRevision = (0, import_data8.createSelector)(
    (state, kind, name, recordKey, revisionKey, query) => {
      logEntityDeprecation(kind, name, "getRevision");
      const queriedState = state.entities.records?.[kind]?.[name]?.revisions?.[recordKey];
      if (!queriedState) {
        return void 0;
      }
      const context = query?.context ?? "default";
      if (!query || !query._fields) {
        if (!queriedState.itemIsComplete[context]?.[revisionKey]) {
          return void 0;
        }
        return queriedState.items[context][revisionKey];
      }
      const item = queriedState.items[context]?.[revisionKey];
      if (!item) {
        return item;
      }
      const filteredItem = {};
      const fields = get_normalized_comma_separable_default(query._fields) ?? [];
      for (let f = 0; f < fields.length; f++) {
        const field = fields[f].split(".");
        let value = item;
        field.forEach((fieldName) => {
          value = value?.[fieldName];
        });
        setNestedValue(filteredItem, field, value);
      }
      return filteredItem;
    },
    (state, kind, name, recordKey, revisionKey, query) => {
      const context = query?.context ?? "default";
      const queriedState = state.entities.records?.[kind]?.[name]?.revisions?.[recordKey];
      return [
        queriedState?.items?.[context]?.[revisionKey],
        queriedState?.itemIsComplete?.[context]?.[revisionKey]
      ];
    }
  );
  function getSyncConnectionStatus(state) {
    if (!state.syncConnectionStatuses) {
      return void 0;
    }
    const PRIORITIZED_STATUSES = ["disconnected", "connecting", "connected"];
    let coalesced;
    for (const status of Object.values(state.syncConnectionStatuses)) {
      if (!coalesced || PRIORITIZED_STATUSES.indexOf(status.status) < PRIORITIZED_STATUSES.indexOf(coalesced.status)) {
        coalesced = status;
      }
    }
    return coalesced;
  }

  // packages/core-data/build-module/actions.mjs
  var actions_exports = {};
  __export(actions_exports, {
    __experimentalBatch: () => __experimentalBatch,
    __experimentalReceiveCurrentGlobalStylesId: () => __experimentalReceiveCurrentGlobalStylesId,
    __experimentalReceiveThemeBaseGlobalStyles: () => __experimentalReceiveThemeBaseGlobalStyles,
    __experimentalReceiveThemeGlobalStyleVariations: () => __experimentalReceiveThemeGlobalStyleVariations,
    __experimentalSaveSpecifiedEntityEdits: () => __experimentalSaveSpecifiedEntityEdits,
    __unstableCreateUndoLevel: () => __unstableCreateUndoLevel,
    addEntities: () => addEntities,
    clearEntityRecordEdits: () => clearEntityRecordEdits,
    deleteEntityRecord: () => deleteEntityRecord,
    editEntityRecord: () => editEntityRecord,
    receiveAutosaves: () => receiveAutosaves,
    receiveCurrentTheme: () => receiveCurrentTheme,
    receiveCurrentUser: () => receiveCurrentUser,
    receiveDefaultTemplateId: () => receiveDefaultTemplateId,
    receiveEmbedPreview: () => receiveEmbedPreview,
    receiveEntityRecords: () => receiveEntityRecords,
    receiveNavigationFallbackId: () => receiveNavigationFallbackId,
    receiveRevisions: () => receiveRevisions,
    receiveThemeGlobalStyleRevisions: () => receiveThemeGlobalStyleRevisions,
    receiveThemeSupports: () => receiveThemeSupports,
    receiveUploadPermissions: () => receiveUploadPermissions,
    receiveUserPermission: () => receiveUserPermission,
    receiveUserPermissions: () => receiveUserPermissions,
    receiveUserQuery: () => receiveUserQuery,
    redo: () => redo,
    saveEditedEntityRecord: () => saveEditedEntityRecord,
    saveEntityRecord: () => saveEntityRecord,
    setSyncConnectionStatus: () => setSyncConnectionStatus,
    undo: () => undo
  });
  var import_es65 = __toESM(require_es6(), 1);
  var import_api_fetch3 = __toESM(require_api_fetch(), 1);
  var import_url3 = __toESM(require_url(), 1);
  var import_deprecated3 = __toESM(require_deprecated(), 1);

  // packages/core-data/build-module/batch/default-processor.mjs
  var import_api_fetch2 = __toESM(require_api_fetch(), 1);
  var maxItems = null;
  function chunk(arr, chunkSize) {
    const tmp = [...arr];
    const cache3 = [];
    while (tmp.length) {
      cache3.push(tmp.splice(0, chunkSize));
    }
    return cache3;
  }
  async function defaultProcessor(requests) {
    if (maxItems === null) {
      const preflightResponse = await (0, import_api_fetch2.default)({
        path: "/batch/v1",
        method: "OPTIONS"
      });
      maxItems = preflightResponse.endpoints[0].args.requests.maxItems;
    }
    const results = [];
    for (const batchRequests of chunk(requests, maxItems)) {
      const batchResponse = await (0, import_api_fetch2.default)({
        path: "/batch/v1",
        method: "POST",
        data: {
          validation: "require-all-validate",
          requests: batchRequests.map((request) => ({
            path: request.path,
            body: request.data,
            // Rename 'data' to 'body'.
            method: request.method,
            headers: request.headers
          }))
        }
      });
      let batchResults;
      if (batchResponse.failed) {
        batchResults = batchResponse.responses.map((response) => ({
          error: response?.body
        }));
      } else {
        batchResults = batchResponse.responses.map((response) => {
          const result = {};
          if (response.status >= 200 && response.status < 300) {
            result.output = response.body;
          } else {
            result.error = response.body;
          }
          return result;
        });
      }
      results.push(...batchResults);
    }
    return results;
  }

  // packages/core-data/build-module/batch/create-batch.mjs
  function createBatch(processor = defaultProcessor) {
    let lastId = 0;
    let queue = [];
    const pending = new ObservableSet();
    return {
      /**
       * Adds an input to the batch and returns a promise that is resolved or
       * rejected when the input is processed by `batch.run()`.
       *
       * You may also pass a thunk which allows inputs to be added
       * asynchronously.
       *
       * ```
       * // Both are allowed:
       * batch.add( { path: '/v1/books', ... } );
       * batch.add( ( add ) => add( { path: '/v1/books', ... } ) );
       * ```
       *
       * If a thunk is passed, `batch.run()` will pause until either:
       *
       * - The thunk calls its `add` argument, or;
       * - The thunk returns a promise and that promise resolves, or;
       * - The thunk returns a non-promise.
       *
       * @param {any|Function} inputOrThunk Input to add or thunk to execute.
       *
       * @return {Promise|any} If given an input, returns a promise that
       *                       is resolved or rejected when the batch is
       *                       processed. If given a thunk, returns the return
       *                       value of that thunk.
       */
      add(inputOrThunk) {
        const id = ++lastId;
        pending.add(id);
        const add = (input) => new Promise((resolve, reject) => {
          queue.push({
            input,
            resolve,
            reject
          });
          pending.delete(id);
        });
        if (typeof inputOrThunk === "function") {
          return Promise.resolve(inputOrThunk(add)).finally(() => {
            pending.delete(id);
          });
        }
        return add(inputOrThunk);
      },
      /**
       * Runs the batch. This calls `batchProcessor` and resolves or rejects
       * all promises returned by `add()`.
       *
       * @return {Promise<boolean>} A promise that resolves to a boolean that is true
       *                   if the processor returned no errors.
       */
      async run() {
        if (pending.size) {
          await new Promise((resolve) => {
            const unsubscribe = pending.subscribe(() => {
              if (!pending.size) {
                unsubscribe();
                resolve(void 0);
              }
            });
          });
        }
        let results;
        try {
          results = await processor(
            queue.map(({ input }) => input)
          );
          if (results.length !== queue.length) {
            throw new Error(
              "run: Array returned by processor must be same size as input array."
            );
          }
        } catch (error) {
          for (const { reject } of queue) {
            reject(error);
          }
          throw error;
        }
        let isSuccess = true;
        results.forEach((result, key) => {
          const queueItem = queue[key];
          if (result?.error) {
            queueItem?.reject(result.error);
            isSuccess = false;
          } else {
            queueItem?.resolve(result?.output ?? result);
          }
        });
        queue = [];
        return isSuccess;
      }
    };
  }
  var ObservableSet = class {
    constructor(...args) {
      this.set = new Set(...args);
      this.subscribers = /* @__PURE__ */ new Set();
    }
    get size() {
      return this.set.size;
    }
    add(value) {
      this.set.add(value);
      this.subscribers.forEach((subscriber) => subscriber());
      return this;
    }
    delete(value) {
      const isSuccess = this.set.delete(value);
      this.subscribers.forEach((subscriber) => subscriber());
      return isSuccess;
    }
    subscribe(subscriber) {
      this.subscribers.add(subscriber);
      return () => {
        this.subscribers.delete(subscriber);
      };
    }
  };

  // packages/core-data/build-module/actions.mjs
  function receiveUserQuery(queryID, users2) {
    return {
      type: "RECEIVE_USER_QUERY",
      users: Array.isArray(users2) ? users2 : [users2],
      queryID
    };
  }
  function receiveCurrentUser(currentUser2) {
    return {
      type: "RECEIVE_CURRENT_USER",
      currentUser: currentUser2
    };
  }
  function addEntities(entities2) {
    return {
      type: "ADD_ENTITIES",
      entities: entities2
    };
  }
  function receiveEntityRecords(kind, name, records, query = void 0, invalidateCache = false, edits = void 0, meta = void 0) {
    if (kind === "postType") {
      records = (Array.isArray(records) ? records : [records]).map(
        (record) => record.status === "auto-draft" ? { ...record, title: "" } : record
      );
    }
    let action;
    if (query) {
      action = receiveQueriedItems(records, query, edits, meta);
    } else {
      action = receiveItems(records, edits, meta);
    }
    return {
      ...action,
      kind,
      name,
      invalidateCache
    };
  }
  function receiveCurrentTheme(currentTheme2) {
    return {
      type: "RECEIVE_CURRENT_THEME",
      currentTheme: currentTheme2
    };
  }
  function __experimentalReceiveCurrentGlobalStylesId(currentGlobalStylesId2) {
    return {
      type: "RECEIVE_CURRENT_GLOBAL_STYLES_ID",
      id: currentGlobalStylesId2
    };
  }
  function __experimentalReceiveThemeBaseGlobalStyles(stylesheet, globalStyles) {
    return {
      type: "RECEIVE_THEME_GLOBAL_STYLES",
      stylesheet,
      globalStyles
    };
  }
  function __experimentalReceiveThemeGlobalStyleVariations(stylesheet, variations) {
    return {
      type: "RECEIVE_THEME_GLOBAL_STYLE_VARIATIONS",
      stylesheet,
      variations
    };
  }
  function receiveThemeSupports() {
    (0, import_deprecated3.default)("wp.data.dispatch( 'core' ).receiveThemeSupports", {
      since: "5.9"
    });
    return {
      type: "DO_NOTHING"
    };
  }
  function receiveThemeGlobalStyleRevisions(currentId, revisions) {
    (0, import_deprecated3.default)(
      "wp.data.dispatch( 'core' ).receiveThemeGlobalStyleRevisions()",
      {
        since: "6.5.0",
        alternative: "wp.data.dispatch( 'core' ).receiveRevisions"
      }
    );
    return {
      type: "RECEIVE_THEME_GLOBAL_STYLE_REVISIONS",
      currentId,
      revisions
    };
  }
  function receiveEmbedPreview(url, preview) {
    return {
      type: "RECEIVE_EMBED_PREVIEW",
      url,
      preview
    };
  }
  var deleteEntityRecord = (kind, name, recordId, query, { __unstableFetch = import_api_fetch3.default, throwOnError = false } = {}) => async ({ dispatch: dispatch3, resolveSelect: resolveSelect2 }) => {
    logEntityDeprecation(kind, name, "deleteEntityRecord");
    const configs = await resolveSelect2.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    let error;
    let deletedRecord = false;
    if (!entityConfig) {
      return;
    }
    const lock2 = await dispatch3.__unstableAcquireStoreLock(
      STORE_NAME,
      ["entities", "records", kind, name, recordId],
      { exclusive: true }
    );
    try {
      dispatch3({
        type: "DELETE_ENTITY_RECORD_START",
        kind,
        name,
        recordId
      });
      let hasError = false;
      let { baseURL } = entityConfig;
      if (kind === "postType" && name === "wp_template" && (recordId && typeof recordId === "string" && !/^\d+$/.test(recordId) || !window?.__experimentalTemplateActivate)) {
        baseURL = baseURL.slice(0, baseURL.lastIndexOf("/")) + "/templates";
      }
      try {
        let path = `${baseURL}/${recordId}`;
        if (query) {
          path = (0, import_url3.addQueryArgs)(path, query);
        }
        deletedRecord = await __unstableFetch({
          path,
          method: "DELETE"
        });
        await dispatch3(removeItems(kind, name, recordId, true));
        if (entityConfig.syncConfig) {
          const objectType = `${kind}/${name}`;
          const objectId = recordId;
          getSyncManager()?.unload(objectType, objectId);
        }
      } catch (_error) {
        hasError = true;
        error = _error;
      }
      dispatch3({
        type: "DELETE_ENTITY_RECORD_FINISH",
        kind,
        name,
        recordId,
        error
      });
      if (hasError && throwOnError) {
        throw error;
      }
      return deletedRecord;
    } finally {
      dispatch3.__unstableReleaseStoreLock(lock2);
    }
  };
  var editEntityRecord = (kind, name, recordId, edits, options = {}) => ({ select: select3, dispatch: dispatch3 }) => {
    logEntityDeprecation(kind, name, "editEntityRecord");
    const entityConfig = select3.getEntityConfig(kind, name);
    if (!entityConfig) {
      throw new Error(
        `The entity being edited (${kind}, ${name}) does not have a loaded config.`
      );
    }
    const { mergedEdits = {} } = entityConfig;
    const record = select3.getRawEntityRecord(kind, name, recordId);
    const editedRecord = select3.getEditedEntityRecord(
      kind,
      name,
      recordId
    );
    const editsWithMerges = Object.keys(edits).reduce((acc, key) => {
      acc[key] = mergedEdits[key] ? { ...editedRecord[key], ...edits[key] } : edits[key];
      return acc;
    }, {});
    const edit = {
      kind,
      name,
      recordId,
      // Clear edits when they are equal to their persisted counterparts
      // so that the property is not considered dirty.
      edits: Object.keys(edits).reduce((acc, key) => {
        const recordValue = record[key];
        const value = editsWithMerges[key];
        acc[key] = (0, import_es65.default)(recordValue, value) ? void 0 : value;
        return acc;
      }, {})
    };
    if (entityConfig.syncConfig) {
      const objectType = `${kind}/${name}`;
      const objectId = recordId;
      const isNewUndoLevel = options.undoIgnore ? false : !options.isCached;
      getSyncManager()?.update(
        objectType,
        objectId,
        editsWithMerges,
        LOCAL_EDITOR_ORIGIN,
        { isNewUndoLevel }
      );
    }
    if (!options.undoIgnore) {
      select3.getUndoManager().addRecord(
        [
          {
            id: { kind, name, recordId },
            changes: Object.keys(edits).reduce((acc, key) => {
              acc[key] = {
                from: editedRecord[key],
                to: edits[key]
              };
              return acc;
            }, {})
          }
        ],
        options.isCached
      );
    }
    dispatch3({
      type: "EDIT_ENTITY_RECORD",
      ...edit
    });
  };
  var clearEntityRecordEdits = (kind, name, recordId) => ({ select: select3, dispatch: dispatch3 }) => {
    const entityConfig = select3.getEntityConfig(kind, name);
    logEntityDeprecation(kind, name, "clearEntityRecordEdits");
    if (!entityConfig) {
      throw new Error(
        `The entity being edited (${kind}, ${name}) does not have a loaded config.`
      );
    }
    const currentEdits = select3.getEntityRecordEdits(
      kind,
      name,
      recordId
    );
    if (!currentEdits) {
      return;
    }
    const clearedEdits = Object.keys(currentEdits).reduce(
      (acc, key) => {
        acc[key] = void 0;
        return acc;
      },
      {}
    );
    dispatch3({
      type: "EDIT_ENTITY_RECORD",
      kind,
      name,
      recordId,
      edits: clearedEdits
    });
  };
  var undo = () => ({ select: select3, dispatch: dispatch3 }) => {
    const undoRecord = select3.getUndoManager().undo();
    if (!undoRecord) {
      return;
    }
    dispatch3({
      type: "UNDO",
      record: undoRecord
    });
  };
  var redo = () => ({ select: select3, dispatch: dispatch3 }) => {
    const redoRecord = select3.getUndoManager().redo();
    if (!redoRecord) {
      return;
    }
    dispatch3({
      type: "REDO",
      record: redoRecord
    });
  };
  var __unstableCreateUndoLevel = () => ({ select: select3 }) => {
    select3.getUndoManager().addRecord();
  };
  var saveEntityRecord = (kind, name, record, {
    isAutosave = false,
    __unstableFetch = import_api_fetch3.default,
    throwOnError = false
  } = {}) => async ({ select: select3, resolveSelect: resolveSelect2, dispatch: dispatch3 }) => {
    logEntityDeprecation(kind, name, "saveEntityRecord");
    const configs = await resolveSelect2.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    if (!entityConfig) {
      return;
    }
    const entityIdKey = entityConfig.key ?? DEFAULT_ENTITY_KEY;
    const recordId = record[entityIdKey];
    const isNewRecord = !!entityIdKey && !recordId;
    const lock2 = await dispatch3.__unstableAcquireStoreLock(
      STORE_NAME,
      ["entities", "records", kind, name, recordId || v4_default()],
      { exclusive: true }
    );
    try {
      for (const [key, value] of Object.entries(record)) {
        if (typeof value === "function") {
          const evaluatedValue = value(
            select3.getEditedEntityRecord(kind, name, recordId)
          );
          dispatch3.editEntityRecord(
            kind,
            name,
            recordId,
            {
              [key]: evaluatedValue
            },
            { undoIgnore: true }
          );
          record[key] = evaluatedValue;
        }
      }
      dispatch3({
        type: "SAVE_ENTITY_RECORD_START",
        kind,
        name,
        recordId,
        isAutosave
      });
      let updatedRecord;
      let error;
      let hasError = false;
      let { baseURL } = entityConfig;
      if (kind === "postType" && name === "wp_template" && (recordId && typeof recordId === "string" && !/^\d+$/.test(recordId) || !window?.__experimentalTemplateActivate)) {
        baseURL = baseURL.slice(0, baseURL.lastIndexOf("/")) + "/templates";
      }
      try {
        const path = `${baseURL}${recordId ? "/" + recordId : ""}`;
        const persistedRecord = !isNewRecord ? select3.getRawEntityRecord(kind, name, recordId) : {};
        if (isAutosave) {
          const currentUser2 = select3.getCurrentUser();
          const currentUserId = currentUser2 ? currentUser2.id : void 0;
          const autosavePost = await resolveSelect2.getAutosave(
            persistedRecord.type,
            persistedRecord.id,
            currentUserId
          );
          let data = {
            ...persistedRecord,
            ...autosavePost,
            ...record
          };
          data = Object.keys(data).reduce(
            (acc, key) => {
              if ([
                "title",
                "excerpt",
                "content",
                "meta"
              ].includes(key)) {
                acc[key] = data[key];
              }
              return acc;
            },
            {
              // Do not update the `status` if we have edited it when auto saving.
              // It's very important to let the user explicitly save this change,
              // because it can lead to unexpected results. An example would be to
              // have a draft post and change the status to publish.
              status: data.status === "auto-draft" ? "draft" : void 0
            }
          );
          updatedRecord = await __unstableFetch({
            path: `${path}/autosaves`,
            method: "POST",
            data
          });
          if (persistedRecord.id === updatedRecord.id) {
            let newRecord = {
              ...persistedRecord,
              ...data,
              ...updatedRecord
            };
            newRecord = Object.keys(newRecord).reduce(
              (acc, key) => {
                if (["title", "excerpt", "content"].includes(
                  key
                )) {
                  acc[key] = newRecord[key];
                } else if (key === "status") {
                  acc[key] = persistedRecord.status === "auto-draft" && newRecord.status === "draft" ? newRecord.status : persistedRecord.status;
                } else {
                  acc[key] = persistedRecord[key];
                }
                return acc;
              },
              {}
            );
            dispatch3.receiveEntityRecords(
              kind,
              name,
              newRecord,
              void 0,
              true
            );
          } else {
            dispatch3.receiveAutosaves(
              persistedRecord.id,
              updatedRecord
            );
          }
        } else {
          let edits = record;
          if (entityConfig.__unstablePrePersist) {
            edits = {
              ...edits,
              ...entityConfig.__unstablePrePersist(
                persistedRecord,
                edits
              )
            };
          }
          updatedRecord = await __unstableFetch({
            path,
            method: recordId ? "PUT" : "POST",
            data: edits
          });
          dispatch3.receiveEntityRecords(
            kind,
            name,
            updatedRecord,
            void 0,
            true,
            edits
          );
          if (entityConfig.syncConfig) {
            getSyncManager()?.update(
              `${kind}/${name}`,
              recordId,
              updatedRecord,
              LOCAL_EDITOR_ORIGIN,
              { isSave: true }
            );
          }
        }
      } catch (_error) {
        hasError = true;
        error = _error;
      }
      dispatch3({
        type: "SAVE_ENTITY_RECORD_FINISH",
        kind,
        name,
        recordId,
        error,
        isAutosave
      });
      if (hasError && throwOnError) {
        throw error;
      }
      return updatedRecord;
    } finally {
      dispatch3.__unstableReleaseStoreLock(lock2);
    }
  };
  var __experimentalBatch = (requests) => async ({ dispatch: dispatch3 }) => {
    const batch = createBatch();
    const api = {
      saveEntityRecord(kind, name, record, options) {
        return batch.add(
          (add) => dispatch3.saveEntityRecord(kind, name, record, {
            ...options,
            __unstableFetch: add
          })
        );
      },
      saveEditedEntityRecord(kind, name, recordId, options) {
        return batch.add(
          (add) => dispatch3.saveEditedEntityRecord(kind, name, recordId, {
            ...options,
            __unstableFetch: add
          })
        );
      },
      deleteEntityRecord(kind, name, recordId, query, options) {
        return batch.add(
          (add) => dispatch3.deleteEntityRecord(kind, name, recordId, query, {
            ...options,
            __unstableFetch: add
          })
        );
      }
    };
    const resultPromises = requests.map((request) => request(api));
    const [, ...results] = await Promise.all([
      batch.run(),
      ...resultPromises
    ]);
    return results;
  };
  var saveEditedEntityRecord = (kind, name, recordId, options) => async ({ select: select3, dispatch: dispatch3, resolveSelect: resolveSelect2 }) => {
    logEntityDeprecation(kind, name, "saveEditedEntityRecord");
    if (!select3.hasEditsForEntityRecord(kind, name, recordId)) {
      return;
    }
    const configs = await resolveSelect2.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    if (!entityConfig) {
      return;
    }
    const entityIdKey = entityConfig.key || DEFAULT_ENTITY_KEY;
    const edits = select3.getEntityRecordNonTransientEdits(
      kind,
      name,
      recordId
    );
    const record = { [entityIdKey]: recordId, ...edits };
    return await dispatch3.saveEntityRecord(kind, name, record, options);
  };
  var __experimentalSaveSpecifiedEntityEdits = (kind, name, recordId, itemsToSave, options) => async ({ select: select3, dispatch: dispatch3, resolveSelect: resolveSelect2 }) => {
    logEntityDeprecation(
      kind,
      name,
      "__experimentalSaveSpecifiedEntityEdits"
    );
    if (!select3.hasEditsForEntityRecord(kind, name, recordId)) {
      return;
    }
    const edits = select3.getEntityRecordNonTransientEdits(
      kind,
      name,
      recordId
    );
    const editsToSave = {};
    for (const item of itemsToSave) {
      setNestedValue(editsToSave, item, getNestedValue(edits, item));
    }
    const configs = await resolveSelect2.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    const entityIdKey = entityConfig?.key || DEFAULT_ENTITY_KEY;
    if (recordId) {
      editsToSave[entityIdKey] = recordId;
    }
    return await dispatch3.saveEntityRecord(
      kind,
      name,
      editsToSave,
      options
    );
  };
  function receiveUploadPermissions(hasUploadPermissions) {
    (0, import_deprecated3.default)("wp.data.dispatch( 'core' ).receiveUploadPermissions", {
      since: "5.9",
      alternative: "receiveUserPermission"
    });
    return receiveUserPermission("create/media", hasUploadPermissions);
  }
  function receiveUserPermission(key, isAllowed) {
    return {
      type: "RECEIVE_USER_PERMISSION",
      key,
      isAllowed
    };
  }
  function receiveUserPermissions(permissions) {
    return {
      type: "RECEIVE_USER_PERMISSIONS",
      permissions
    };
  }
  function receiveAutosaves(postId, autosaves2) {
    return {
      type: "RECEIVE_AUTOSAVES",
      postId,
      autosaves: Array.isArray(autosaves2) ? autosaves2 : [autosaves2]
    };
  }
  function receiveNavigationFallbackId(fallbackId) {
    return {
      type: "RECEIVE_NAVIGATION_FALLBACK_ID",
      fallbackId
    };
  }
  function receiveDefaultTemplateId(query, templateId) {
    return {
      type: "RECEIVE_DEFAULT_TEMPLATE",
      query,
      templateId
    };
  }
  var receiveRevisions = (kind, name, recordKey, records, query, invalidateCache = false, meta) => async ({ dispatch: dispatch3, resolveSelect: resolveSelect2 }) => {
    logEntityDeprecation(kind, name, "receiveRevisions");
    const configs = await resolveSelect2.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    const key = entityConfig && entityConfig?.revisionKey ? entityConfig.revisionKey : DEFAULT_ENTITY_KEY;
    dispatch3({
      type: "RECEIVE_ITEM_REVISIONS",
      key,
      items: Array.isArray(records) ? records : [records],
      recordKey,
      meta,
      query,
      kind,
      name,
      invalidateCache
    });
  };
  function setSyncConnectionStatus(kind, name, key, status) {
    if (!status) {
      return {
        type: "CLEAR_SYNC_CONNECTION_STATUS",
        kind,
        name,
        key
      };
    }
    return {
      type: "SET_SYNC_CONNECTION_STATUS",
      kind,
      name,
      key,
      status
    };
  }

  // packages/core-data/build-module/private-actions.mjs
  var private_actions_exports = {};
  __export(private_actions_exports, {
    editMediaEntity: () => editMediaEntity,
    receiveEditorAssets: () => receiveEditorAssets,
    receiveEditorSettings: () => receiveEditorSettings,
    receiveRegisteredPostMeta: () => receiveRegisteredPostMeta
  });
  var import_api_fetch4 = __toESM(require_api_fetch(), 1);
  function receiveRegisteredPostMeta(postType, registeredPostMeta2) {
    return {
      type: "RECEIVE_REGISTERED_POST_META",
      postType,
      registeredPostMeta: registeredPostMeta2
    };
  }
  var editMediaEntity = (recordId, edits = {}, { __unstableFetch = import_api_fetch4.default, throwOnError = false } = {}) => async ({ dispatch: dispatch3, resolveSelect: resolveSelect2 }) => {
    if (!recordId) {
      return;
    }
    const kind = "postType";
    const name = "attachment";
    const configs = await resolveSelect2.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    if (!entityConfig) {
      return;
    }
    const lock2 = await dispatch3.__unstableAcquireStoreLock(
      STORE_NAME,
      ["entities", "records", kind, name, recordId],
      { exclusive: true }
    );
    let updatedRecord;
    let error;
    let hasError = false;
    try {
      dispatch3({
        type: "SAVE_ENTITY_RECORD_START",
        kind,
        name,
        recordId
      });
      try {
        const path = `${entityConfig.baseURL}/${recordId}/edit`;
        const newRecord = await __unstableFetch({
          path,
          method: "POST",
          data: {
            ...edits
          }
        });
        if (newRecord) {
          dispatch3.receiveEntityRecords(
            kind,
            name,
            [newRecord],
            void 0,
            true,
            void 0,
            void 0
          );
          updatedRecord = newRecord;
        }
      } catch (e) {
        error = e;
        hasError = true;
      }
      dispatch3({
        type: "SAVE_ENTITY_RECORD_FINISH",
        kind,
        name,
        recordId,
        error
      });
      if (hasError && throwOnError) {
        throw error;
      }
      return updatedRecord;
    } finally {
      dispatch3.__unstableReleaseStoreLock(lock2);
    }
  };
  function receiveEditorSettings(settings) {
    return {
      type: "RECEIVE_EDITOR_SETTINGS",
      settings
    };
  }
  function receiveEditorAssets(assets) {
    return {
      type: "RECEIVE_EDITOR_ASSETS",
      assets
    };
  }

  // packages/core-data/build-module/resolvers.mjs
  var resolvers_exports = {};
  __export(resolvers_exports, {
    __experimentalGetCurrentGlobalStylesId: () => __experimentalGetCurrentGlobalStylesId2,
    __experimentalGetCurrentThemeBaseGlobalStyles: () => __experimentalGetCurrentThemeBaseGlobalStyles2,
    __experimentalGetCurrentThemeGlobalStylesVariations: () => __experimentalGetCurrentThemeGlobalStylesVariations2,
    canUser: () => canUser2,
    canUserEditEntityRecord: () => canUserEditEntityRecord2,
    getAuthors: () => getAuthors2,
    getAutosave: () => getAutosave2,
    getAutosaves: () => getAutosaves2,
    getBlockPatternCategories: () => getBlockPatternCategories2,
    getBlockPatterns: () => getBlockPatterns2,
    getCurrentTheme: () => getCurrentTheme2,
    getCurrentThemeGlobalStylesRevisions: () => getCurrentThemeGlobalStylesRevisions2,
    getCurrentUser: () => getCurrentUser2,
    getDefaultTemplateId: () => getDefaultTemplateId2,
    getEditedEntityRecord: () => getEditedEntityRecord2,
    getEditorAssets: () => getEditorAssets2,
    getEditorSettings: () => getEditorSettings2,
    getEmbedPreview: () => getEmbedPreview2,
    getEntitiesConfig: () => getEntitiesConfig2,
    getEntityRecord: () => getEntityRecord2,
    getEntityRecords: () => getEntityRecords2,
    getEntityRecordsTotalItems: () => getEntityRecordsTotalItems2,
    getEntityRecordsTotalPages: () => getEntityRecordsTotalPages2,
    getNavigationFallbackId: () => getNavigationFallbackId2,
    getRawEntityRecord: () => getRawEntityRecord2,
    getRegisteredPostMeta: () => getRegisteredPostMeta2,
    getRevision: () => getRevision2,
    getRevisions: () => getRevisions2,
    getThemeSupports: () => getThemeSupports2,
    getUserPatternCategories: () => getUserPatternCategories2
  });
  var import_url6 = __toESM(require_url(), 1);
  var import_html_entities2 = __toESM(require_html_entities(), 1);
  var import_api_fetch8 = __toESM(require_api_fetch(), 1);

  // packages/core-data/build-module/fetch/index.mjs
  var import_api_fetch7 = __toESM(require_api_fetch(), 1);

  // packages/core-data/build-module/fetch/__experimental-fetch-link-suggestions.mjs
  var import_api_fetch5 = __toESM(require_api_fetch(), 1);
  var import_url4 = __toESM(require_url(), 1);
  var import_html_entities = __toESM(require_html_entities(), 1);
  var import_i18n2 = __toESM(require_i18n(), 1);
  async function fetchLinkSuggestions(search, searchOptions = {}, editorSettings2 = {}) {
    const searchOptionsToUse = searchOptions.isInitialSuggestions && searchOptions.initialSuggestionsSearchOptions ? {
      ...searchOptions,
      ...searchOptions.initialSuggestionsSearchOptions
    } : searchOptions;
    const {
      type,
      subtype,
      page,
      perPage = searchOptions.isInitialSuggestions ? 3 : 20
    } = searchOptionsToUse;
    const { disablePostFormats = false } = editorSettings2;
    const queries2 = [];
    if (!type || type === "post") {
      queries2.push(
        (0, import_api_fetch5.default)({
          path: (0, import_url4.addQueryArgs)("/wp/v2/search", {
            search,
            page,
            per_page: perPage,
            type: "post",
            subtype
          })
        }).then((results2) => {
          return results2.map((result) => {
            return {
              id: result.id,
              url: result.url,
              title: (0, import_html_entities.decodeEntities)(result.title || "") || (0, import_i18n2.__)("(no title)"),
              type: result.subtype || result.type,
              kind: "post-type"
            };
          });
        }).catch(() => [])
        // Fail by returning no results.
      );
    }
    if (!type || type === "term") {
      queries2.push(
        (0, import_api_fetch5.default)({
          path: (0, import_url4.addQueryArgs)("/wp/v2/search", {
            search,
            page,
            per_page: perPage,
            type: "term",
            subtype
          })
        }).then((results2) => {
          return results2.map((result) => {
            return {
              id: result.id,
              url: result.url,
              title: (0, import_html_entities.decodeEntities)(result.title || "") || (0, import_i18n2.__)("(no title)"),
              type: result.subtype || result.type,
              kind: "taxonomy"
            };
          });
        }).catch(() => [])
        // Fail by returning no results.
      );
    }
    if (!disablePostFormats && (!type || type === "post-format")) {
      queries2.push(
        (0, import_api_fetch5.default)({
          path: (0, import_url4.addQueryArgs)("/wp/v2/search", {
            search,
            page,
            per_page: perPage,
            type: "post-format",
            subtype
          })
        }).then((results2) => {
          return results2.map((result) => {
            return {
              id: result.id,
              url: result.url,
              title: (0, import_html_entities.decodeEntities)(result.title || "") || (0, import_i18n2.__)("(no title)"),
              type: result.subtype || result.type,
              kind: "taxonomy"
            };
          });
        }).catch(() => [])
        // Fail by returning no results.
      );
    }
    if (!type || type === "attachment") {
      queries2.push(
        (0, import_api_fetch5.default)({
          path: (0, import_url4.addQueryArgs)("/wp/v2/media", {
            search,
            page,
            per_page: perPage
          })
        }).then((results2) => {
          return results2.map((result) => {
            return {
              id: result.id,
              url: result.source_url,
              title: (0, import_html_entities.decodeEntities)(result.title.rendered || "") || (0, import_i18n2.__)("(no title)"),
              type: result.type,
              kind: "media"
            };
          });
        }).catch(() => [])
        // Fail by returning no results.
      );
    }
    const responses = await Promise.all(queries2);
    let results = responses.flat();
    results = results.filter((result) => !!result.id);
    results = sortResults(results, search);
    results = results.slice(0, perPage);
    return results;
  }
  function sortResults(results, search) {
    const searchTokens = tokenize(search);
    const scores = {};
    for (const result of results) {
      if (result.title) {
        const titleTokens = tokenize(result.title);
        const exactMatchingTokens = titleTokens.filter(
          (titleToken) => searchTokens.some(
            (searchToken) => titleToken === searchToken
          )
        );
        const subMatchingTokens = titleTokens.filter(
          (titleToken) => searchTokens.some(
            (searchToken) => titleToken !== searchToken && titleToken.includes(searchToken)
          )
        );
        const exactMatchScore = exactMatchingTokens.length / titleTokens.length * 10;
        const subMatchScore = subMatchingTokens.length / titleTokens.length;
        scores[result.id] = exactMatchScore + subMatchScore;
      } else {
        scores[result.id] = 0;
      }
    }
    return results.sort((a, b) => scores[b.id] - scores[a.id]);
  }
  function tokenize(text) {
    return text.toLowerCase().match(/[\p{L}\p{N}]+/gu) || [];
  }

  // packages/core-data/build-module/fetch/__experimental-fetch-url-data.mjs
  var import_api_fetch6 = __toESM(require_api_fetch(), 1);
  var import_url5 = __toESM(require_url(), 1);
  var CACHE = /* @__PURE__ */ new Map();
  var fetchUrlData = async (url, options = {}) => {
    const endpoint = "/wp-block-editor/v1/url-details";
    const args = {
      url: (0, import_url5.prependHTTP)(url)
    };
    if (!(0, import_url5.isURL)(url)) {
      return Promise.reject(`${url} is not a valid URL.`);
    }
    const protocol = (0, import_url5.getProtocol)(url);
    if (!protocol || !(0, import_url5.isValidProtocol)(protocol) || !protocol.startsWith("http") || !/^https?:\/\/[^\/\s]/i.test(url)) {
      return Promise.reject(
        `${url} does not have a valid protocol. URLs must be "http" based`
      );
    }
    if (CACHE.has(url)) {
      return CACHE.get(url);
    }
    return (0, import_api_fetch6.default)({
      path: (0, import_url5.addQueryArgs)(endpoint, args),
      ...options
    }).then((res) => {
      CACHE.set(url, res);
      return res;
    });
  };
  var experimental_fetch_url_data_default = fetchUrlData;

  // packages/core-data/build-module/fetch/index.mjs
  async function fetchBlockPatterns() {
    const restPatterns = await (0, import_api_fetch7.default)({
      path: "/wp/v2/block-patterns/patterns"
    });
    if (!restPatterns) {
      return [];
    }
    return restPatterns.map(
      (pattern) => Object.fromEntries(
        Object.entries(pattern).map(([key, value]) => [
          camelCase(key),
          value
        ])
      )
    );
  }

  // packages/core-data/build-module/resolvers.mjs
  var getAuthors2 = (query) => async ({ dispatch: dispatch3 }) => {
    const path = (0, import_url6.addQueryArgs)(
      "/wp/v2/users/?who=authors&per_page=100",
      query
    );
    const users2 = await (0, import_api_fetch8.default)({ path });
    dispatch3.receiveUserQuery(path, users2);
  };
  var getCurrentUser2 = () => async ({ dispatch: dispatch3 }) => {
    const currentUser2 = await (0, import_api_fetch8.default)({ path: "/wp/v2/users/me" });
    dispatch3.receiveCurrentUser(currentUser2);
  };
  var getEntityRecord2 = (kind, name, key = "", query) => async ({ select: select3, dispatch: dispatch3, registry, resolveSelect: resolveSelect2 }) => {
    const configs = await resolveSelect2.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.name === name && config.kind === kind
    );
    if (!entityConfig) {
      return;
    }
    const lock2 = await dispatch3.__unstableAcquireStoreLock(
      STORE_NAME,
      ["entities", "records", kind, name, key],
      { exclusive: false }
    );
    try {
      if (query !== void 0 && query._fields) {
        query = {
          ...query,
          _fields: [
            .../* @__PURE__ */ new Set([
              ...get_normalized_comma_separable_default(query._fields) || [],
              entityConfig.key || DEFAULT_ENTITY_KEY
            ])
          ].join()
        };
      }
      if (query !== void 0 && query._fields) {
        const hasRecord = select3.hasEntityRecord(
          kind,
          name,
          key,
          query
        );
        if (hasRecord) {
          return;
        }
      }
      let { baseURL } = entityConfig;
      if (kind === "postType" && name === "wp_template" && (key && typeof key === "string" && !/^\d+$/.test(key) || !window?.__experimentalTemplateActivate)) {
        baseURL = baseURL.slice(0, baseURL.lastIndexOf("/")) + "/templates";
      }
      const path = (0, import_url6.addQueryArgs)(baseURL + (key ? "/" + key : ""), {
        ...entityConfig.baseURLParams,
        ...query
      });
      const response = await (0, import_api_fetch8.default)({ path, parse: false });
      const record = await response.json();
      const permissions = getUserPermissionsFromAllowHeader(
        response.headers?.get("allow")
      );
      const canUserResolutionsArgs = [];
      const receiveUserPermissionArgs = {};
      for (const action of ALLOWED_RESOURCE_ACTIONS) {
        receiveUserPermissionArgs[getUserPermissionCacheKey(action, {
          kind,
          name,
          id: key
        })] = permissions[action];
        canUserResolutionsArgs.push([
          action,
          { kind, name, id: key }
        ]);
      }
      if (entityConfig.syncConfig && isNumericID(key) && !query) {
        const objectType = `${kind}/${name}`;
        const objectId = key;
        const recordWithTransients = { ...record };
        Object.entries(entityConfig.transientEdits ?? {}).filter(
          ([propName, transientConfig]) => void 0 === recordWithTransients[propName] && transientConfig && "object" === typeof transientConfig && "read" in transientConfig && "function" === typeof transientConfig.read
        ).forEach(([propName, transientConfig]) => {
          recordWithTransients[propName] = transientConfig.read(recordWithTransients);
        });
        void getSyncManager()?.load(
          entityConfig.syncConfig,
          objectType,
          objectId,
          recordWithTransients,
          {
            // Handle edits sourced from the sync manager.
            editRecord: (edits, options = {}) => {
              if (!Object.keys(edits).length) {
                return;
              }
              dispatch3({
                type: "EDIT_ENTITY_RECORD",
                kind,
                name,
                recordId: key,
                edits,
                meta: {
                  undo: void 0
                },
                options
              });
            },
            // Get the current entity record (with edits)
            getEditedRecord: async () => await resolveSelect2.getEditedEntityRecord(
              kind,
              name,
              key
            ),
            // Handle sync connection status changes.
            onStatusChange: (status) => {
              dispatch3.setSyncConnectionStatus(
                kind,
                name,
                key,
                status
              );
            },
            // Refetch the current entity record from the database.
            refetchRecord: async () => {
              dispatch3.receiveEntityRecords(
                kind,
                name,
                await (0, import_api_fetch8.default)({ path, parse: true }),
                query
              );
            },
            // Save the current entity record, whether or not it has unsaved
            // edits. This is used to trigger a persisted CRDT document.
            saveRecord: () => {
              resolveSelect2.getEditedEntityRecord(kind, name, key).then((editedRecord) => {
                const { status } = editedRecord;
                if ("auto-draft" === status) {
                  return;
                }
                dispatch3.saveEntityRecord(
                  kind,
                  name,
                  editedRecord
                );
              });
            },
            addUndoMeta: (ydoc, meta) => {
              const selectionHistory = getSelectionHistory(ydoc);
              if (selectionHistory) {
                meta.set(
                  "selectionHistory",
                  selectionHistory
                );
              }
            },
            restoreUndoMeta: (ydoc, meta) => {
              const selectionHistory = meta.get("selectionHistory");
              if (selectionHistory) {
                setTimeout(() => {
                  restoreSelection(selectionHistory, ydoc);
                }, 0);
              }
            }
          }
        );
      }
      registry.batch(() => {
        dispatch3.receiveEntityRecords(kind, name, record, query);
        dispatch3.receiveUserPermissions(receiveUserPermissionArgs);
        dispatch3.finishResolutions("canUser", canUserResolutionsArgs);
      });
    } finally {
      dispatch3.__unstableReleaseStoreLock(lock2);
    }
  };
  getEntityRecord2.shouldInvalidate = (action, kind, name) => {
    return kind === "root" && name === "site" && (action.type === "RECEIVE_ITEMS" && // Making sure persistedEdits is set seems to be the only way of
    // knowing whether it's an update or fetch. Only an update would
    // have persistedEdits.
    action.persistedEdits && action.persistedEdits.status !== "auto-draft" || action.type === "REMOVE_ITEMS") && action.kind === "postType" && action.name === "wp_template";
  };
  var getRawEntityRecord2 = forward_resolver_default("getEntityRecord");
  var getEditedEntityRecord2 = forward_resolver_default("getEntityRecord");
  var getEntityRecords2 = (kind, name, query = {}) => async ({ dispatch: dispatch3, registry, resolveSelect: resolveSelect2 }) => {
    const configs = await resolveSelect2.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.name === name && config.kind === kind
    );
    if (!entityConfig) {
      return;
    }
    const lock2 = await dispatch3.__unstableAcquireStoreLock(
      STORE_NAME,
      ["entities", "records", kind, name],
      { exclusive: false }
    );
    const rawQuery = { ...query };
    const key = entityConfig.key || DEFAULT_ENTITY_KEY;
    function getResolutionsArgs(records, recordsQuery) {
      const queryArgs = Object.fromEntries(
        Object.entries(recordsQuery).filter(([k, v]) => {
          return ["context", "_fields"].includes(k) && !!v;
        })
      );
      return records.filter((record) => record?.[key]).map((record) => [
        kind,
        name,
        record[key],
        Object.keys(queryArgs).length > 0 ? queryArgs : void 0
      ]);
    }
    try {
      if (query._fields) {
        query = {
          ...query,
          _fields: [
            .../* @__PURE__ */ new Set([
              ...get_normalized_comma_separable_default(query._fields) || [],
              key
            ])
          ].join()
        };
      }
      let { baseURL } = entityConfig;
      const { combinedTemplates = true } = query;
      if (kind === "postType" && name === "wp_template" && combinedTemplates) {
        baseURL = baseURL.slice(0, baseURL.lastIndexOf("/")) + "/templates";
      }
      const path = (0, import_url6.addQueryArgs)(baseURL, {
        ...entityConfig.baseURLParams,
        ...query
      });
      let records = [], meta;
      if (entityConfig.supportsPagination && query.per_page !== -1) {
        const response = await (0, import_api_fetch8.default)({ path, parse: false });
        records = Object.values(await response.json());
        meta = {
          totalItems: parseInt(
            response.headers.get("X-WP-Total")
          ),
          totalPages: parseInt(
            response.headers.get("X-WP-TotalPages")
          )
        };
      } else if (query.per_page === -1 && query[RECEIVE_INTERMEDIATE_RESULTS] === true) {
        let page = 1;
        let totalPages;
        do {
          const response = await (0, import_api_fetch8.default)({
            path: (0, import_url6.addQueryArgs)(path, { page, per_page: 100 }),
            parse: false
          });
          const pageRecords = Object.values(await response.json());
          totalPages = parseInt(
            response.headers.get("X-WP-TotalPages")
          );
          if (!meta) {
            meta = {
              totalItems: parseInt(
                response.headers.get("X-WP-Total")
              ),
              totalPages: 1
            };
          }
          records.push(...pageRecords);
          registry.batch(() => {
            dispatch3.receiveEntityRecords(
              kind,
              name,
              records,
              query,
              false,
              void 0,
              meta
            );
            dispatch3.finishResolutions(
              "getEntityRecord",
              getResolutionsArgs(pageRecords, rawQuery)
            );
          });
          page++;
        } while (page <= totalPages);
      } else {
        records = Object.values(await (0, import_api_fetch8.default)({ path }));
        meta = {
          totalItems: records.length,
          totalPages: 1
        };
      }
      if (entityConfig.syncConfig && -1 === query.per_page) {
        const objectType = `${kind}/${name}`;
        getSyncManager()?.loadCollection(
          entityConfig.syncConfig,
          objectType,
          {
            onStatusChange: (status) => {
              dispatch3.setSyncConnectionStatus(
                kind,
                name,
                null,
                status
              );
            },
            refetchRecords: async () => {
              dispatch3.receiveEntityRecords(
                kind,
                name,
                await (0, import_api_fetch8.default)({ path, parse: true }),
                query
              );
            }
          }
        );
      }
      if (query._fields) {
        records = records.map((record) => {
          query._fields.split(",").forEach((field) => {
            if (!record.hasOwnProperty(field)) {
              record[field] = void 0;
            }
          });
          return record;
        });
      }
      registry.batch(() => {
        dispatch3.receiveEntityRecords(
          kind,
          name,
          records,
          query,
          false,
          void 0,
          meta
        );
        const targetHints = records.filter(
          (record) => !!record?.[key] && !!record?._links?.self?.[0]?.targetHints?.allow
        ).map((record) => ({
          id: record[key],
          permissions: getUserPermissionsFromAllowHeader(
            record._links.self[0].targetHints.allow
          )
        }));
        const canUserResolutionsArgs = [];
        const receiveUserPermissionArgs = {};
        for (const targetHint of targetHints) {
          for (const action of ALLOWED_RESOURCE_ACTIONS) {
            canUserResolutionsArgs.push([
              action,
              { kind, name, id: targetHint.id }
            ]);
            receiveUserPermissionArgs[getUserPermissionCacheKey(action, {
              kind,
              name,
              id: targetHint.id
            })] = targetHint.permissions[action];
          }
        }
        if (targetHints.length > 0) {
          dispatch3.receiveUserPermissions(
            receiveUserPermissionArgs
          );
          dispatch3.finishResolutions(
            "canUser",
            canUserResolutionsArgs
          );
        }
        dispatch3.finishResolutions(
          "getEntityRecord",
          getResolutionsArgs(records, rawQuery)
        );
        dispatch3.__unstableReleaseStoreLock(lock2);
      });
    } catch (e) {
      dispatch3.__unstableReleaseStoreLock(lock2);
    }
  };
  getEntityRecords2.shouldInvalidate = (action, kind, name) => {
    return (action.type === "RECEIVE_ITEMS" || action.type === "REMOVE_ITEMS") && action.invalidateCache && kind === action.kind && name === action.name;
  };
  var getEntityRecordsTotalItems2 = forward_resolver_default("getEntityRecords");
  var getEntityRecordsTotalPages2 = forward_resolver_default("getEntityRecords");
  var getCurrentTheme2 = () => async ({ dispatch: dispatch3, resolveSelect: resolveSelect2 }) => {
    const activeThemes = await resolveSelect2.getEntityRecords(
      "root",
      "theme",
      { status: "active" }
    );
    dispatch3.receiveCurrentTheme(activeThemes[0]);
  };
  var getThemeSupports2 = forward_resolver_default("getCurrentTheme");
  var getEmbedPreview2 = (url) => async ({ dispatch: dispatch3 }) => {
    try {
      const embedProxyResponse = await (0, import_api_fetch8.default)({
        path: (0, import_url6.addQueryArgs)("/oembed/1.0/proxy", { url })
      });
      dispatch3.receiveEmbedPreview(url, embedProxyResponse);
    } catch (error) {
      dispatch3.receiveEmbedPreview(url, false);
    }
  };
  var canUser2 = (requestedAction, resource, id) => async ({ dispatch: dispatch3, registry, resolveSelect: resolveSelect2 }) => {
    if (!ALLOWED_RESOURCE_ACTIONS.includes(requestedAction)) {
      throw new Error(`'${requestedAction}' is not a valid action.`);
    }
    const { hasStartedResolution } = registry.select(STORE_NAME);
    for (const relatedAction of ALLOWED_RESOURCE_ACTIONS) {
      if (relatedAction === requestedAction) {
        continue;
      }
      const isAlreadyResolving = hasStartedResolution("canUser", [
        relatedAction,
        resource,
        id
      ]);
      if (isAlreadyResolving) {
        return;
      }
    }
    let resourcePath = null;
    if (typeof resource === "object") {
      if (!resource.kind || !resource.name) {
        throw new Error("The entity resource object is not valid.");
      }
      const configs = await resolveSelect2.getEntitiesConfig(
        resource.kind
      );
      const entityConfig = configs.find(
        (config) => config.name === resource.name && config.kind === resource.kind
      );
      if (!entityConfig) {
        return;
      }
      resourcePath = entityConfig.baseURL + (resource.id ? "/" + resource.id : "");
    } else {
      resourcePath = `/wp/v2/${resource}` + (id ? "/" + id : "");
    }
    let response;
    try {
      response = await (0, import_api_fetch8.default)({
        path: resourcePath,
        method: "OPTIONS",
        parse: false
      });
    } catch (error) {
      return;
    }
    const permissions = getUserPermissionsFromAllowHeader(
      response.headers?.get("allow")
    );
    registry.batch(() => {
      for (const action of ALLOWED_RESOURCE_ACTIONS) {
        const key = getUserPermissionCacheKey(action, resource, id);
        dispatch3.receiveUserPermission(key, permissions[action]);
        if (action !== requestedAction) {
          dispatch3.finishResolution("canUser", [
            action,
            resource,
            id
          ]);
        }
      }
    });
  };
  var canUserEditEntityRecord2 = (kind, name, recordId) => async ({ dispatch: dispatch3 }) => {
    await dispatch3(canUser2("update", { kind, name, id: recordId }));
  };
  var getAutosaves2 = (postType, postId) => async ({ dispatch: dispatch3, resolveSelect: resolveSelect2 }) => {
    const {
      rest_base: restBase,
      rest_namespace: restNamespace = "wp/v2",
      supports
    } = await resolveSelect2.getPostType(postType);
    if (!supports?.autosave) {
      return;
    }
    const autosaves2 = await (0, import_api_fetch8.default)({
      path: `/${restNamespace}/${restBase}/${postId}/autosaves?context=edit`
    });
    if (autosaves2 && autosaves2.length) {
      dispatch3.receiveAutosaves(postId, autosaves2);
    }
  };
  var getAutosave2 = (postType, postId) => async ({ resolveSelect: resolveSelect2 }) => {
    await resolveSelect2.getAutosaves(postType, postId);
  };
  var __experimentalGetCurrentGlobalStylesId2 = () => async ({ dispatch: dispatch3, resolveSelect: resolveSelect2 }) => {
    const activeThemes = await resolveSelect2.getEntityRecords(
      "root",
      "theme",
      { status: "active" }
    );
    const globalStylesURL = activeThemes?.[0]?._links?.["wp:user-global-styles"]?.[0]?.href;
    if (!globalStylesURL) {
      return;
    }
    const matches = globalStylesURL.match(/\/(\d+)(?:\?|$)/);
    const id = matches ? Number(matches[1]) : null;
    if (id) {
      dispatch3.__experimentalReceiveCurrentGlobalStylesId(id);
    }
  };
  var __experimentalGetCurrentThemeBaseGlobalStyles2 = () => async ({ resolveSelect: resolveSelect2, dispatch: dispatch3 }) => {
    const currentTheme2 = await resolveSelect2.getCurrentTheme();
    const themeGlobalStyles = await (0, import_api_fetch8.default)({
      path: `/wp/v2/global-styles/themes/${currentTheme2.stylesheet}?context=view`
    });
    dispatch3.__experimentalReceiveThemeBaseGlobalStyles(
      currentTheme2.stylesheet,
      themeGlobalStyles
    );
  };
  var __experimentalGetCurrentThemeGlobalStylesVariations2 = () => async ({ resolveSelect: resolveSelect2, dispatch: dispatch3 }) => {
    const currentTheme2 = await resolveSelect2.getCurrentTheme();
    const variations = await (0, import_api_fetch8.default)({
      path: `/wp/v2/global-styles/themes/${currentTheme2.stylesheet}/variations?context=view`
    });
    dispatch3.__experimentalReceiveThemeGlobalStyleVariations(
      currentTheme2.stylesheet,
      variations
    );
  };
  var getCurrentThemeGlobalStylesRevisions2 = () => async ({ resolveSelect: resolveSelect2, dispatch: dispatch3 }) => {
    const globalStylesId = await resolveSelect2.__experimentalGetCurrentGlobalStylesId();
    const record = globalStylesId ? await resolveSelect2.getEntityRecord(
      "root",
      "globalStyles",
      globalStylesId
    ) : void 0;
    const revisionsURL = record?._links?.["version-history"]?.[0]?.href;
    if (revisionsURL) {
      const resetRevisions = await (0, import_api_fetch8.default)({
        url: revisionsURL
      });
      const revisions = resetRevisions?.map(
        (revision) => Object.fromEntries(
          Object.entries(revision).map(([key, value]) => [
            camelCase(key),
            value
          ])
        )
      );
      dispatch3.receiveThemeGlobalStyleRevisions(
        globalStylesId,
        revisions
      );
    }
  };
  getCurrentThemeGlobalStylesRevisions2.shouldInvalidate = (action) => {
    return action.type === "SAVE_ENTITY_RECORD_FINISH" && action.kind === "root" && !action.error && action.name === "globalStyles";
  };
  var getBlockPatterns2 = () => async ({ dispatch: dispatch3 }) => {
    const patterns = await fetchBlockPatterns();
    dispatch3({ type: "RECEIVE_BLOCK_PATTERNS", patterns });
  };
  var getBlockPatternCategories2 = () => async ({ dispatch: dispatch3 }) => {
    const categories = await (0, import_api_fetch8.default)({
      path: "/wp/v2/block-patterns/categories"
    });
    dispatch3({ type: "RECEIVE_BLOCK_PATTERN_CATEGORIES", categories });
  };
  var getUserPatternCategories2 = () => async ({ dispatch: dispatch3, resolveSelect: resolveSelect2 }) => {
    const patternCategories = await resolveSelect2.getEntityRecords(
      "taxonomy",
      "wp_pattern_category",
      {
        per_page: -1,
        _fields: "id,name,description,slug",
        context: "view"
      }
    );
    const mappedPatternCategories = patternCategories?.map((userCategory) => ({
      ...userCategory,
      label: (0, import_html_entities2.decodeEntities)(userCategory.name),
      name: userCategory.slug
    })) || [];
    dispatch3({
      type: "RECEIVE_USER_PATTERN_CATEGORIES",
      patternCategories: mappedPatternCategories
    });
  };
  var getNavigationFallbackId2 = () => async ({ dispatch: dispatch3, select: select3, registry }) => {
    const fallback = await (0, import_api_fetch8.default)({
      path: (0, import_url6.addQueryArgs)("/wp-block-editor/v1/navigation-fallback", {
        _embed: true
      })
    });
    const record = fallback?._embedded?.self;
    registry.batch(() => {
      dispatch3.receiveNavigationFallbackId(fallback?.id);
      if (!record) {
        return;
      }
      const existingFallbackEntityRecord = select3.getEntityRecord(
        "postType",
        "wp_navigation",
        fallback.id
      );
      const invalidateNavigationQueries = !existingFallbackEntityRecord;
      dispatch3.receiveEntityRecords(
        "postType",
        "wp_navigation",
        record,
        void 0,
        invalidateNavigationQueries
      );
      dispatch3.finishResolution("getEntityRecord", [
        "postType",
        "wp_navigation",
        fallback.id
      ]);
    });
  };
  var getDefaultTemplateId2 = (query) => async ({ dispatch: dispatch3, registry, resolveSelect: resolveSelect2 }) => {
    const template = await (0, import_api_fetch8.default)({
      path: (0, import_url6.addQueryArgs)("/wp/v2/templates/lookup", query)
    });
    await resolveSelect2.getEntitiesConfig("postType");
    const id = window?.__experimentalTemplateActivate ? template?.wp_id || template?.id : template?.id;
    if (id) {
      template.id = id;
      registry.batch(() => {
        dispatch3.receiveDefaultTemplateId(query, id);
        dispatch3.receiveEntityRecords("postType", template.type, [
          template
        ]);
        dispatch3.finishResolution("getEntityRecord", [
          "postType",
          template.type,
          id
        ]);
      });
    }
  };
  getDefaultTemplateId2.shouldInvalidate = (action) => {
    return action.type === "RECEIVE_ITEMS" && action.kind === "root" && action.name === "site";
  };
  var getRevisions2 = (kind, name, recordKey, query = {}) => async ({ dispatch: dispatch3, registry, resolveSelect: resolveSelect2 }) => {
    const configs = await resolveSelect2.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.name === name && config.kind === kind
    );
    if (!entityConfig) {
      return;
    }
    if (query._fields) {
      query = {
        ...query,
        _fields: [
          .../* @__PURE__ */ new Set([
            ...get_normalized_comma_separable_default(query._fields) || [],
            entityConfig.revisionKey || DEFAULT_ENTITY_KEY
          ])
        ].join()
      };
    }
    const path = (0, import_url6.addQueryArgs)(
      entityConfig.getRevisionsUrl(recordKey),
      query
    );
    let records, response;
    const meta = {};
    const isPaginated = entityConfig.supportsPagination && query.per_page !== -1;
    try {
      response = await (0, import_api_fetch8.default)({ path, parse: !isPaginated });
    } catch (error) {
      return;
    }
    if (response) {
      if (isPaginated) {
        records = Object.values(await response.json());
        meta.totalItems = parseInt(
          response.headers.get("X-WP-Total")
        );
      } else {
        records = Object.values(response);
      }
      if (query._fields) {
        records = records.map((record) => {
          query._fields.split(",").forEach((field) => {
            if (!record.hasOwnProperty(field)) {
              record[field] = void 0;
            }
          });
          return record;
        });
      }
      registry.batch(() => {
        dispatch3.receiveRevisions(
          kind,
          name,
          recordKey,
          records,
          query,
          false,
          meta
        );
        if (!query?._fields && !query.context) {
          const key = entityConfig.revisionKey || DEFAULT_ENTITY_KEY;
          const resolutionsArgs = records.filter((record) => record[key]).map((record) => [
            kind,
            name,
            recordKey,
            record[key]
          ]);
          dispatch3.finishResolutions(
            "getRevision",
            resolutionsArgs
          );
        }
      });
    }
  };
  getRevisions2.shouldInvalidate = (action, kind, name, recordKey) => action.type === "SAVE_ENTITY_RECORD_FINISH" && name === action.name && kind === action.kind && !action.error && recordKey === action.recordId;
  var getRevision2 = (kind, name, recordKey, revisionKey, query) => async ({ dispatch: dispatch3, resolveSelect: resolveSelect2 }) => {
    const configs = await resolveSelect2.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.name === name && config.kind === kind
    );
    if (!entityConfig) {
      return;
    }
    if (query !== void 0 && query._fields) {
      query = {
        ...query,
        _fields: [
          .../* @__PURE__ */ new Set([
            ...get_normalized_comma_separable_default(query._fields) || [],
            entityConfig.revisionKey || DEFAULT_ENTITY_KEY
          ])
        ].join()
      };
    }
    const path = (0, import_url6.addQueryArgs)(
      entityConfig.getRevisionsUrl(recordKey, revisionKey),
      query
    );
    let record;
    try {
      record = await (0, import_api_fetch8.default)({ path });
    } catch (error) {
      return;
    }
    if (record) {
      dispatch3.receiveRevisions(kind, name, recordKey, record, query);
    }
  };
  var getRegisteredPostMeta2 = (postType) => async ({ dispatch: dispatch3, resolveSelect: resolveSelect2 }) => {
    let options;
    try {
      const {
        rest_namespace: restNamespace = "wp/v2",
        rest_base: restBase
      } = await resolveSelect2.getPostType(postType) || {};
      options = await (0, import_api_fetch8.default)({
        path: `${restNamespace}/${restBase}/?context=edit`,
        method: "OPTIONS"
      });
    } catch (error) {
      return;
    }
    if (options) {
      dispatch3.receiveRegisteredPostMeta(
        postType,
        options?.schema?.properties?.meta?.properties
      );
    }
  };
  var getEntitiesConfig2 = (kind) => async ({ dispatch: dispatch3 }) => {
    const loader = additionalEntityConfigLoaders.find(
      (l) => l.kind === kind
    );
    if (!loader) {
      return;
    }
    try {
      const configs = await loader.loadEntities();
      if (!configs.length) {
        return;
      }
      dispatch3.addEntities(configs);
    } catch {
    }
  };
  var getEditorSettings2 = () => async ({ dispatch: dispatch3 }) => {
    const settings = await (0, import_api_fetch8.default)({
      path: "/wp-block-editor/v1/settings"
    });
    dispatch3.receiveEditorSettings(settings);
  };
  var getEditorAssets2 = () => async ({ dispatch: dispatch3 }) => {
    const assets = await (0, import_api_fetch8.default)({
      path: "/wp-block-editor/v1/assets"
    });
    dispatch3.receiveEditorAssets(assets);
  };

  // packages/core-data/build-module/locks/utils.mjs
  function deepCopyLocksTreePath(tree, path) {
    const newTree = { ...tree };
    let currentNode = newTree;
    for (const branchName of path) {
      currentNode.children = {
        ...currentNode.children,
        [branchName]: {
          locks: [],
          children: {},
          ...currentNode.children[branchName]
        }
      };
      currentNode = currentNode.children[branchName];
    }
    return newTree;
  }
  function getNode(tree, path) {
    let currentNode = tree;
    for (const branchName of path) {
      const nextNode = currentNode.children[branchName];
      if (!nextNode) {
        return null;
      }
      currentNode = nextNode;
    }
    return currentNode;
  }
  function* iteratePath(tree, path) {
    let currentNode = tree;
    yield currentNode;
    for (const branchName of path) {
      const nextNode = currentNode.children[branchName];
      if (!nextNode) {
        break;
      }
      yield nextNode;
      currentNode = nextNode;
    }
  }
  function* iterateDescendants(node) {
    const stack = Object.values(node.children);
    while (stack.length) {
      const childNode = stack.pop();
      yield childNode;
      stack.push(...Object.values(childNode.children));
    }
  }
  function hasConflictingLock({ exclusive }, locks2) {
    if (exclusive && locks2.length) {
      return true;
    }
    if (!exclusive && locks2.filter((lock2) => lock2.exclusive).length) {
      return true;
    }
    return false;
  }

  // packages/core-data/build-module/locks/reducer.mjs
  var DEFAULT_STATE = {
    requests: [],
    tree: {
      locks: [],
      children: {}
    }
  };
  function locks(state = DEFAULT_STATE, action) {
    switch (action.type) {
      case "ENQUEUE_LOCK_REQUEST": {
        const { request } = action;
        return {
          ...state,
          requests: [request, ...state.requests]
        };
      }
      case "GRANT_LOCK_REQUEST": {
        const { lock: lock2, request } = action;
        const { store: store2, path } = request;
        const storePath = [store2, ...path];
        const newTree = deepCopyLocksTreePath(state.tree, storePath);
        const node = getNode(newTree, storePath);
        node.locks = [...node.locks, lock2];
        return {
          ...state,
          requests: state.requests.filter((r) => r !== request),
          tree: newTree
        };
      }
      case "RELEASE_LOCK": {
        const { lock: lock2 } = action;
        const storePath = [lock2.store, ...lock2.path];
        const newTree = deepCopyLocksTreePath(state.tree, storePath);
        const node = getNode(newTree, storePath);
        node.locks = node.locks.filter((l) => l !== lock2);
        return {
          ...state,
          tree: newTree
        };
      }
    }
    return state;
  }

  // packages/core-data/build-module/locks/selectors.mjs
  function getPendingLockRequests(state) {
    return state.requests;
  }
  function isLockAvailable(state, store2, path, { exclusive }) {
    const storePath = [store2, ...path];
    const locks2 = state.tree;
    for (const node2 of iteratePath(locks2, storePath)) {
      if (hasConflictingLock({ exclusive }, node2.locks)) {
        return false;
      }
    }
    const node = getNode(locks2, storePath);
    if (!node) {
      return true;
    }
    for (const descendant of iterateDescendants(node)) {
      if (hasConflictingLock({ exclusive }, descendant.locks)) {
        return false;
      }
    }
    return true;
  }

  // packages/core-data/build-module/locks/engine.mjs
  function createLocks() {
    let state = locks(void 0, { type: "@@INIT" });
    function processPendingLockRequests() {
      for (const request of getPendingLockRequests(state)) {
        const { store: store2, path, exclusive, notifyAcquired } = request;
        if (isLockAvailable(state, store2, path, { exclusive })) {
          const lock2 = { store: store2, path, exclusive };
          state = locks(state, {
            type: "GRANT_LOCK_REQUEST",
            lock: lock2,
            request
          });
          notifyAcquired(lock2);
        }
      }
    }
    function acquire(store2, path, exclusive) {
      return new Promise((resolve) => {
        state = locks(state, {
          type: "ENQUEUE_LOCK_REQUEST",
          request: { store: store2, path, exclusive, notifyAcquired: resolve }
        });
        processPendingLockRequests();
      });
    }
    function release(lock2) {
      state = locks(state, {
        type: "RELEASE_LOCK",
        lock: lock2
      });
      processPendingLockRequests();
    }
    return { acquire, release };
  }

  // packages/core-data/build-module/locks/actions.mjs
  function createLocksActions() {
    const locks2 = createLocks();
    function __unstableAcquireStoreLock(store2, path, { exclusive }) {
      return () => locks2.acquire(store2, path, exclusive);
    }
    function __unstableReleaseStoreLock(lock2) {
      return () => locks2.release(lock2);
    }
    return { __unstableAcquireStoreLock, __unstableReleaseStoreLock };
  }

  // packages/core-data/build-module/dynamic-entities.mjs
  var dynamicActions;
  var dynamicSelectors;

  // packages/core-data/build-module/entity-provider.mjs
  var import_element2 = __toESM(require_element(), 1);

  // packages/core-data/build-module/entity-context.mjs
  var import_element = __toESM(require_element(), 1);
  var EntityContext = (0, import_element.createContext)({});
  EntityContext.displayName = "EntityContext";

  // packages/core-data/build-module/entity-provider.mjs
  var import_jsx_runtime = __toESM(require_jsx_runtime(), 1);
  function EntityProvider({ kind, type: name, id, children }) {
    const parent = (0, import_element2.useContext)(EntityContext);
    const childContext = (0, import_element2.useMemo)(
      () => ({
        ...parent,
        [kind]: {
          ...parent?.[kind],
          [name]: id
        }
      }),
      [parent, kind, name, id]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(EntityContext.Provider, { value: childContext, children });
  }

  // packages/core-data/build-module/hooks/use-entity-record.mjs
  var import_data10 = __toESM(require_data(), 1);
  var import_deprecated4 = __toESM(require_deprecated(), 1);
  var import_element3 = __toESM(require_element(), 1);

  // packages/core-data/build-module/hooks/use-query-select.mjs
  var import_data9 = __toESM(require_data(), 1);

  // node_modules/memize/dist/index.js
  function memize(fn, options) {
    var size = 0;
    var head;
    var tail;
    options = options || {};
    function memoized() {
      var node = head, len = arguments.length, args, i;
      searchCache: while (node) {
        if (node.args.length !== arguments.length) {
          node = node.next;
          continue;
        }
        for (i = 0; i < len; i++) {
          if (node.args[i] !== arguments[i]) {
            node = node.next;
            continue searchCache;
          }
        }
        if (node !== head) {
          if (node === tail) {
            tail = node.prev;
          }
          node.prev.next = node.next;
          if (node.next) {
            node.next.prev = node.prev;
          }
          node.next = head;
          node.prev = null;
          head.prev = node;
          head = node;
        }
        return node.val;
      }
      args = new Array(len);
      for (i = 0; i < len; i++) {
        args[i] = arguments[i];
      }
      node = {
        args,
        // Generate the result from original function
        val: fn.apply(null, args)
      };
      if (head) {
        head.prev = node;
        node.next = head;
      } else {
        tail = node;
      }
      if (size === /** @type {MemizeOptions} */
      options.maxSize) {
        tail = /** @type {MemizeCacheNode} */
        tail.prev;
        tail.next = null;
      } else {
        size++;
      }
      head = node;
      return node.val;
    }
    memoized.clear = function() {
      head = null;
      tail = null;
      size = 0;
    };
    return memoized;
  }

  // packages/core-data/build-module/hooks/memoize.mjs
  var memoize_default = memize;

  // packages/core-data/build-module/hooks/constants.mjs
  var Status = /* @__PURE__ */ ((Status2) => {
    Status2["Idle"] = "IDLE";
    Status2["Resolving"] = "RESOLVING";
    Status2["Error"] = "ERROR";
    Status2["Success"] = "SUCCESS";
    return Status2;
  })(Status || {});

  // packages/core-data/build-module/hooks/use-query-select.mjs
  var META_SELECTORS = [
    "getIsResolving",
    "hasStartedResolution",
    "hasFinishedResolution",
    "isResolving",
    "getCachedResolvers"
  ];
  function useQuerySelect(mapQuerySelect, deps) {
    return (0, import_data9.useSelect)((select3, registry) => {
      const resolve = (store2) => enrichSelectors(select3(store2));
      return mapQuerySelect(resolve, registry);
    }, deps);
  }
  var enrichSelectors = memoize_default(((selectors) => {
    const resolvers = {};
    for (const selectorName in selectors) {
      if (META_SELECTORS.includes(selectorName)) {
        continue;
      }
      Object.defineProperty(resolvers, selectorName, {
        get: () => (...args) => {
          const data = selectors[selectorName](...args);
          const resolutionStatus = selectors.getResolutionState(
            selectorName,
            args
          )?.status;
          let status;
          switch (resolutionStatus) {
            case "resolving":
              status = Status.Resolving;
              break;
            case "finished":
              status = Status.Success;
              break;
            case "error":
              status = Status.Error;
              break;
            case void 0:
              status = Status.Idle;
              break;
          }
          return {
            data,
            status,
            isResolving: status === Status.Resolving,
            hasStarted: status !== Status.Idle,
            hasResolved: status === Status.Success || status === Status.Error
          };
        }
      });
    }
    return resolvers;
  }));

  // packages/core-data/build-module/hooks/use-entity-record.mjs
  var EMPTY_OBJECT2 = {};
  function useEntityRecord(kind, name, recordId, options = { enabled: true }) {
    const { editEntityRecord: editEntityRecord2, saveEditedEntityRecord: saveEditedEntityRecord2 } = (0, import_data10.useDispatch)(store);
    const mutations = (0, import_element3.useMemo)(
      () => ({
        edit: (record2, editOptions = {}) => editEntityRecord2(kind, name, recordId, record2, editOptions),
        save: (saveOptions = {}) => saveEditedEntityRecord2(kind, name, recordId, {
          throwOnError: true,
          ...saveOptions
        })
      }),
      [editEntityRecord2, kind, name, recordId, saveEditedEntityRecord2]
    );
    const { editedRecord, hasEdits, edits } = (0, import_data10.useSelect)(
      (select3) => {
        if (!options.enabled) {
          return {
            editedRecord: EMPTY_OBJECT2,
            hasEdits: false,
            edits: EMPTY_OBJECT2
          };
        }
        return {
          editedRecord: select3(store).getEditedEntityRecord(
            kind,
            name,
            recordId
          ),
          hasEdits: select3(store).hasEditsForEntityRecord(
            kind,
            name,
            recordId
          ),
          edits: select3(store).getEntityRecordNonTransientEdits(
            kind,
            name,
            recordId
          )
        };
      },
      [kind, name, recordId, options.enabled]
    );
    const { data: record, ...querySelectRest } = useQuerySelect(
      (query) => {
        if (!options.enabled) {
          return {
            data: null
          };
        }
        return query(store).getEntityRecord(kind, name, recordId);
      },
      [kind, name, recordId, options.enabled]
    );
    return {
      record,
      editedRecord,
      hasEdits,
      edits,
      ...querySelectRest,
      ...mutations
    };
  }
  function __experimentalUseEntityRecord(kind, name, recordId, options) {
    (0, import_deprecated4.default)(`wp.data.__experimentalUseEntityRecord`, {
      alternative: "wp.data.useEntityRecord",
      since: "6.1"
    });
    return useEntityRecord(kind, name, recordId, options);
  }

  // packages/core-data/build-module/hooks/use-entity-records.mjs
  var import_url7 = __toESM(require_url(), 1);
  var import_deprecated5 = __toESM(require_deprecated(), 1);
  var import_data11 = __toESM(require_data(), 1);
  var import_element4 = __toESM(require_element(), 1);
  var EMPTY_ARRAY = [];
  function useEntityRecords(kind, name, queryArgs = {}, options = { enabled: true }) {
    const queryAsString = (0, import_url7.addQueryArgs)("", queryArgs);
    const { data: records, ...rest } = useQuerySelect(
      (query) => {
        if (!options.enabled) {
          return {
            // Avoiding returning a new reference on every execution.
            data: EMPTY_ARRAY
          };
        }
        return query(store).getEntityRecords(kind, name, queryArgs);
      },
      [kind, name, queryAsString, options.enabled]
    );
    const { totalItems, totalPages } = (0, import_data11.useSelect)(
      (select3) => {
        if (!options.enabled) {
          return {
            totalItems: null,
            totalPages: null
          };
        }
        return {
          totalItems: select3(store).getEntityRecordsTotalItems(
            kind,
            name,
            queryArgs
          ),
          totalPages: select3(store).getEntityRecordsTotalPages(
            kind,
            name,
            queryArgs
          )
        };
      },
      [kind, name, queryAsString, options.enabled]
    );
    return {
      records,
      totalItems,
      totalPages,
      ...rest
    };
  }
  function __experimentalUseEntityRecords(kind, name, queryArgs, options) {
    (0, import_deprecated5.default)(`wp.data.__experimentalUseEntityRecords`, {
      alternative: "wp.data.useEntityRecords",
      since: "6.1"
    });
    return useEntityRecords(kind, name, queryArgs, options);
  }
  function useEntityRecordsWithPermissions(kind, name, queryArgs = {}, options = { enabled: true }) {
    const entityConfig = (0, import_data11.useSelect)(
      (select3) => select3(store).getEntityConfig(kind, name),
      [kind, name]
    );
    const { records: data, ...ret } = useEntityRecords(
      kind,
      name,
      {
        ...queryArgs,
        // If _fields is provided, we need to include _links in the request for permission caching to work.
        ...queryArgs._fields ? {
          _fields: [
            .../* @__PURE__ */ new Set([
              ...get_normalized_comma_separable_default(
                queryArgs._fields
              ) || [],
              "_links"
            ])
          ].join()
        } : {}
      },
      options
    );
    const ids = (0, import_element4.useMemo)(
      () => data?.map(
        // @ts-ignore
        (record) => record[entityConfig?.key ?? "id"]
      ) ?? [],
      [data, entityConfig?.key]
    );
    const permissions = (0, import_data11.useSelect)(
      (select3) => {
        const { getEntityRecordsPermissions: getEntityRecordsPermissions2 } = unlock(
          select3(store)
        );
        return getEntityRecordsPermissions2(kind, name, ids);
      },
      [ids, kind, name]
    );
    const dataWithPermissions = (0, import_element4.useMemo)(
      () => data?.map((record, index) => ({
        // @ts-ignore
        ...record,
        permissions: permissions[index]
      })) ?? [],
      [data, permissions]
    );
    return { records: dataWithPermissions, ...ret };
  }

  // packages/core-data/build-module/hooks/use-resource-permissions.mjs
  var import_deprecated6 = __toESM(require_deprecated(), 1);
  var import_warning = __toESM(require_warning(), 1);
  function useResourcePermissions(resource, id) {
    const isEntity = typeof resource === "object";
    const resourceAsString = isEntity ? JSON.stringify(resource) : resource;
    if (isEntity && typeof id !== "undefined") {
      (0, import_warning.default)(
        `When 'resource' is an entity object, passing 'id' as a separate argument isn't supported.`
      );
    }
    return useQuerySelect(
      (resolve) => {
        const hasId = isEntity ? !!resource.id : !!id;
        const { canUser: canUser3 } = resolve(store);
        const create2 = canUser3(
          "create",
          isEntity ? { kind: resource.kind, name: resource.name } : resource
        );
        if (!hasId) {
          const read2 = canUser3("read", resource);
          const isResolving2 = create2.isResolving || read2.isResolving;
          const hasResolved2 = create2.hasResolved && read2.hasResolved;
          let status2 = Status.Idle;
          if (isResolving2) {
            status2 = Status.Resolving;
          } else if (hasResolved2) {
            status2 = Status.Success;
          }
          return {
            status: status2,
            isResolving: isResolving2,
            hasResolved: hasResolved2,
            canCreate: create2.hasResolved && create2.data,
            canRead: read2.hasResolved && read2.data
          };
        }
        const read = canUser3("read", resource, id);
        const update = canUser3("update", resource, id);
        const _delete = canUser3("delete", resource, id);
        const isResolving = read.isResolving || create2.isResolving || update.isResolving || _delete.isResolving;
        const hasResolved = read.hasResolved && create2.hasResolved && update.hasResolved && _delete.hasResolved;
        let status = Status.Idle;
        if (isResolving) {
          status = Status.Resolving;
        } else if (hasResolved) {
          status = Status.Success;
        }
        return {
          status,
          isResolving,
          hasResolved,
          canRead: hasResolved && read.data,
          canCreate: hasResolved && create2.data,
          canUpdate: hasResolved && update.data,
          canDelete: hasResolved && _delete.data
        };
      },
      [resourceAsString, id]
    );
  }
  var use_resource_permissions_default = useResourcePermissions;
  function __experimentalUseResourcePermissions(resource, id) {
    (0, import_deprecated6.default)(`wp.data.__experimentalUseResourcePermissions`, {
      alternative: "wp.data.useResourcePermissions",
      since: "6.1"
    });
    return useResourcePermissions(resource, id);
  }

  // packages/core-data/build-module/hooks/use-entity-block-editor.mjs
  var import_element6 = __toESM(require_element(), 1);
  var import_data12 = __toESM(require_data(), 1);
  var import_blocks5 = __toESM(require_blocks(), 1);

  // packages/core-data/build-module/hooks/use-entity-id.mjs
  var import_element5 = __toESM(require_element(), 1);
  function useEntityId(kind, name) {
    const context = (0, import_element5.useContext)(EntityContext);
    return context?.[kind]?.[name];
  }

  // packages/core-data/build-module/footnotes/index.mjs
  var import_rich_text2 = __toESM(require_rich_text(), 1);

  // packages/core-data/build-module/footnotes/get-rich-text-values-cached.mjs
  var import_block_editor3 = __toESM(require_block_editor(), 1);
  var unlockedApis;
  var cache = /* @__PURE__ */ new WeakMap();
  function getRichTextValuesCached(block) {
    if (!unlockedApis) {
      unlockedApis = unlock(import_block_editor3.privateApis);
    }
    if (!cache.has(block)) {
      const values = unlockedApis.getRichTextValues([block]);
      cache.set(block, values);
    }
    return cache.get(block);
  }

  // packages/core-data/build-module/footnotes/get-footnotes-order.mjs
  var cache2 = /* @__PURE__ */ new WeakMap();
  function getBlockFootnotesOrder(block) {
    if (!cache2.has(block)) {
      const order = [];
      for (const value of getRichTextValuesCached(block)) {
        if (!value) {
          continue;
        }
        value.replacements.forEach(({ type, attributes }) => {
          if (type === "core/footnote") {
            order.push(attributes["data-fn"]);
          }
        });
      }
      cache2.set(block, order);
    }
    return cache2.get(block);
  }
  function getFootnotesOrder(blocks) {
    return blocks.flatMap(getBlockFootnotesOrder);
  }

  // packages/core-data/build-module/footnotes/index.mjs
  var oldFootnotes = {};
  function updateFootnotesFromMeta(blocks, meta) {
    const output = { blocks };
    if (!meta) {
      return output;
    }
    if (meta.footnotes === void 0) {
      return output;
    }
    const newOrder = getFootnotesOrder(blocks);
    const footnotes = meta.footnotes ? JSON.parse(meta.footnotes) : [];
    const currentOrder = footnotes.map((fn) => fn.id);
    if (currentOrder.join("") === newOrder.join("")) {
      return output;
    }
    const newFootnotes = newOrder.map(
      (fnId) => footnotes.find((fn) => fn.id === fnId) || oldFootnotes[fnId] || {
        id: fnId,
        content: ""
      }
    );
    function updateAttributes(attributes) {
      if (!attributes || Array.isArray(attributes) || typeof attributes !== "object") {
        return attributes;
      }
      attributes = { ...attributes };
      for (const key in attributes) {
        const value = attributes[key];
        if (Array.isArray(value)) {
          attributes[key] = value.map(updateAttributes);
          continue;
        }
        if (typeof value !== "string" && !(value instanceof import_rich_text2.RichTextData)) {
          continue;
        }
        const richTextValue = typeof value === "string" ? import_rich_text2.RichTextData.fromHTMLString(value) : new import_rich_text2.RichTextData(value);
        let hasFootnotes = false;
        richTextValue.replacements.forEach((replacement) => {
          if (replacement.type === "core/footnote") {
            const id = replacement.attributes["data-fn"];
            const index = newOrder.indexOf(id);
            const countValue = (0, import_rich_text2.create)({
              html: replacement.innerHTML
            });
            countValue.text = String(index + 1);
            countValue.formats = Array.from(
              { length: countValue.text.length },
              () => countValue.formats[0]
            );
            countValue.replacements = Array.from(
              { length: countValue.text.length },
              () => countValue.replacements[0]
            );
            replacement.innerHTML = (0, import_rich_text2.toHTMLString)({
              value: countValue
            });
            hasFootnotes = true;
          }
        });
        if (hasFootnotes) {
          attributes[key] = typeof value === "string" ? richTextValue.toHTMLString() : richTextValue;
        }
      }
      return attributes;
    }
    function updateBlocksAttributes(__blocks) {
      return __blocks.map((block) => {
        return {
          ...block,
          attributes: updateAttributes(block.attributes),
          innerBlocks: updateBlocksAttributes(block.innerBlocks)
        };
      });
    }
    const newBlocks = updateBlocksAttributes(blocks);
    oldFootnotes = {
      ...oldFootnotes,
      ...footnotes.reduce((acc, fn) => {
        if (!newOrder.includes(fn.id)) {
          acc[fn.id] = fn;
        }
        return acc;
      }, {})
    };
    return {
      meta: {
        ...meta,
        footnotes: JSON.stringify(newFootnotes)
      },
      blocks: newBlocks
    };
  }

  // packages/core-data/build-module/hooks/use-entity-block-editor.mjs
  var EMPTY_ARRAY2 = [];
  var parsedBlocksCache = /* @__PURE__ */ new Map();
  function useEntityBlockEditor(kind, name, { id: _id } = {}) {
    const providerId = useEntityId(kind, name);
    const id = _id ?? providerId;
    const { content, editedBlocks, meta } = (0, import_data12.useSelect)(
      (select3) => {
        if (!id) {
          return {};
        }
        const { getEditedEntityRecord: getEditedEntityRecord3 } = select3(STORE_NAME);
        const editedRecord = getEditedEntityRecord3(kind, name, id);
        return {
          editedBlocks: editedRecord.blocks,
          content: editedRecord.content,
          meta: editedRecord.meta
        };
      },
      [kind, name, id]
    );
    const { __unstableCreateUndoLevel: __unstableCreateUndoLevel2, editEntityRecord: editEntityRecord2 } = (0, import_data12.useDispatch)(STORE_NAME);
    const blocks = (0, import_element6.useMemo)(() => {
      if (!id) {
        return void 0;
      }
      if (editedBlocks) {
        return editedBlocks;
      }
      if (!content || typeof content !== "string") {
        return EMPTY_ARRAY2;
      }
      const cacheKey = `${kind}:${name}:${id}`;
      const cached = parsedBlocksCache.get(cacheKey);
      let _blocks;
      if (cached && cached.content === content) {
        _blocks = cached.blocks;
      } else {
        _blocks = (0, import_blocks5.parse)(content);
        parsedBlocksCache.set(cacheKey, { content, blocks: _blocks });
      }
      return _blocks;
    }, [kind, name, id, editedBlocks, content]);
    const onChange = (0, import_element6.useCallback)(
      (newBlocks, options) => {
        const noChange = blocks === newBlocks;
        if (noChange) {
          return __unstableCreateUndoLevel2(kind, name, id);
        }
        const { selection, ...rest } = options;
        const edits = {
          selection,
          content: ({ blocks: blocksForSerialization = [] }) => (0, import_blocks5.__unstableSerializeAndClean)(blocksForSerialization),
          ...updateFootnotesFromMeta(newBlocks, meta)
        };
        editEntityRecord2(kind, name, id, edits, {
          isCached: false,
          ...rest
        });
      },
      [
        kind,
        name,
        id,
        blocks,
        meta,
        __unstableCreateUndoLevel2,
        editEntityRecord2
      ]
    );
    const onInput = (0, import_element6.useCallback)(
      (newBlocks, options) => {
        const { selection, ...rest } = options;
        const edits = {
          selection,
          ...updateFootnotesFromMeta(newBlocks, meta)
        };
        editEntityRecord2(kind, name, id, edits, {
          isCached: true,
          ...rest
        });
      },
      [kind, name, id, meta, editEntityRecord2]
    );
    return [blocks, onInput, onChange];
  }

  // packages/core-data/build-module/hooks/use-entity-prop.mjs
  var import_element7 = __toESM(require_element(), 1);
  var import_data13 = __toESM(require_data(), 1);
  function useEntityProp(kind, name, prop, _id) {
    const providerId = useEntityId(kind, name);
    const id = _id ?? providerId;
    const { value, fullValue } = (0, import_data13.useSelect)(
      (select3) => {
        const { getEntityRecord: getEntityRecord3, getEditedEntityRecord: getEditedEntityRecord3 } = select3(STORE_NAME);
        const record = getEntityRecord3(kind, name, id);
        const editedRecord = getEditedEntityRecord3(kind, name, id);
        return record && editedRecord ? {
          value: editedRecord[prop],
          fullValue: record[prop]
        } : {};
      },
      [kind, name, id, prop]
    );
    const { editEntityRecord: editEntityRecord2 } = (0, import_data13.useDispatch)(STORE_NAME);
    const setValue = (0, import_element7.useCallback)(
      (newValue) => {
        editEntityRecord2(kind, name, id, {
          [prop]: newValue
        });
      },
      [editEntityRecord2, kind, name, id, prop]
    );
    return [value, setValue, fullValue];
  }

  // packages/core-data/build-module/hooks/use-post-editor-awareness-state.mjs
  var import_element8 = __toESM(require_element(), 1);
  var defaultState = {
    activeCollaborators: [],
    getAbsolutePositionIndex: () => null,
    getDebugData: () => ({
      doc: {},
      clients: {},
      collaboratorMap: {}
    }),
    isCurrentCollaboratorDisconnected: false
  };
  function getAwarenessState(awareness, newState) {
    const activeCollaborators = newState ?? awareness.getCurrentState();
    return {
      activeCollaborators,
      getAbsolutePositionIndex: (selection) => awareness.getAbsolutePositionIndex(selection),
      getDebugData: () => awareness.getDebugData(),
      isCurrentCollaboratorDisconnected: activeCollaborators.find((collaborator) => collaborator.isMe)?.isConnected === false
    };
  }
  function usePostEditorAwarenessState(postId, postType) {
    const [state, setState] = (0, import_element8.useState)(defaultState);
    (0, import_element8.useEffect)(() => {
      if (null === postId || null === postType) {
        setState(defaultState);
        return;
      }
      const objectType = `postType/${postType}`;
      const objectId = postId.toString();
      const awareness = getSyncManager()?.getAwareness(
        objectType,
        objectId
      );
      if (!awareness) {
        setState(defaultState);
        return;
      }
      awareness.setUp();
      setState(getAwarenessState(awareness));
      const unsubscribe = awareness?.onStateChange(
        (newState) => {
          setState(getAwarenessState(awareness, newState));
        }
      );
      return unsubscribe;
    }, [postId, postType]);
    return state;
  }
  function useActiveCollaborators(postId, postType) {
    return usePostEditorAwarenessState(postId, postType).activeCollaborators;
  }
  function useGetAbsolutePositionIndex(postId, postType) {
    return usePostEditorAwarenessState(postId, postType).getAbsolutePositionIndex;
  }

  // packages/core-data/build-module/private-apis.mjs
  var privateApis = {};
  lock(privateApis, {
    useEntityRecordsWithPermissions,
    RECEIVE_INTERMEDIATE_RESULTS,
    useActiveCollaborators,
    useGetAbsolutePositionIndex
  });

  // packages/core-data/build-module/index.mjs
  var entitiesConfig2 = [
    ...rootEntitiesConfig,
    ...additionalEntityConfigLoaders.filter((config) => !!config.name)
  ];
  var entitySelectors = entitiesConfig2.reduce((result, entity2) => {
    const { kind, name, plural } = entity2;
    const getEntityRecordMethodName = getMethodName(kind, name);
    result[getEntityRecordMethodName] = (state, key, query) => {
      logEntityDeprecation(kind, name, getEntityRecordMethodName, {
        isShorthandSelector: true,
        alternativeFunctionName: "getEntityRecord"
      });
      return getEntityRecord(state, kind, name, key, query);
    };
    if (plural) {
      const getEntityRecordsMethodName = getMethodName(kind, plural, "get");
      result[getEntityRecordsMethodName] = (state, query) => {
        logEntityDeprecation(kind, name, getEntityRecordsMethodName, {
          isShorthandSelector: true,
          alternativeFunctionName: "getEntityRecords"
        });
        return getEntityRecords(state, kind, name, query);
      };
    }
    return result;
  }, {});
  var entityResolvers = entitiesConfig2.reduce((result, entity2) => {
    const { kind, name, plural } = entity2;
    const getEntityRecordMethodName = getMethodName(kind, name);
    result[getEntityRecordMethodName] = (key, query) => {
      logEntityDeprecation(kind, name, getEntityRecordMethodName, {
        isShorthandSelector: true,
        alternativeFunctionName: "getEntityRecord"
      });
      return getEntityRecord2(kind, name, key, query);
    };
    if (plural) {
      const getEntityRecordsMethodName = getMethodName(kind, plural, "get");
      result[getEntityRecordsMethodName] = (...args) => {
        logEntityDeprecation(kind, plural, getEntityRecordsMethodName, {
          isShorthandSelector: true,
          alternativeFunctionName: "getEntityRecords"
        });
        return getEntityRecords2(kind, name, ...args);
      };
      result[getEntityRecordsMethodName].shouldInvalidate = (action) => getEntityRecords2.shouldInvalidate(action, kind, name);
    }
    return result;
  }, {});
  var entityActions = entitiesConfig2.reduce((result, entity2) => {
    const { kind, name } = entity2;
    const saveEntityRecordMethodName = getMethodName(kind, name, "save");
    result[saveEntityRecordMethodName] = (record, options) => {
      logEntityDeprecation(kind, name, saveEntityRecordMethodName, {
        isShorthandSelector: true,
        alternativeFunctionName: "saveEntityRecord"
      });
      return saveEntityRecord(kind, name, record, options);
    };
    const deleteEntityRecordMethodName = getMethodName(kind, name, "delete");
    result[deleteEntityRecordMethodName] = (key, query, options) => {
      logEntityDeprecation(kind, name, deleteEntityRecordMethodName, {
        isShorthandSelector: true,
        alternativeFunctionName: "deleteEntityRecord"
      });
      return deleteEntityRecord(kind, name, key, query, options);
    };
    return result;
  }, {});
  var storeConfig = () => ({
    reducer: reducer_default2,
    actions: {
      ...dynamicActions,
      ...actions_exports,
      ...entityActions,
      ...createLocksActions()
    },
    selectors: {
      ...dynamicSelectors,
      ...selectors_exports,
      ...entitySelectors
    },
    resolvers: { ...resolvers_exports, ...entityResolvers }
  });
  var store = (0, import_data14.createReduxStore)(STORE_NAME, storeConfig());
  unlock(store).registerPrivateSelectors(private_selectors_exports);
  unlock(store).registerPrivateActions(private_actions_exports);
  (0, import_data14.register)(store);
  return __toCommonJS(index_exports);
})();