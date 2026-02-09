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
  var __export = (target, all2) => {
    for (var name in all2)
      __defProp(target, name, { get: all2[name], enumerable: true });
  };
  var __copyProps = (to, from2, except, desc) => {
    if (from2 && typeof from2 === "object" || typeof from2 === "function") {
      for (let key of __getOwnPropNames(from2))
        if (!__hasOwnProp.call(to, key) && key !== except)
          __defProp(to, key, { get: () => from2[key], enumerable: !(desc = __getOwnPropDesc(from2, key)) || desc.enumerable });
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
          var length2, i, keys2;
          if (Array.isArray(a)) {
            length2 = a.length;
            if (length2 != b.length) return false;
            for (i = length2; i-- !== 0; )
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
            length2 = a.length;
            if (length2 != b.length) return false;
            for (i = length2; i-- !== 0; )
              if (a[i] !== b[i]) return false;
            return true;
          }
          if (a.constructor === RegExp) return a.source === b.source && a.flags === b.flags;
          if (a.valueOf !== Object.prototype.valueOf) return a.valueOf() === b.valueOf();
          if (a.toString !== Object.prototype.toString) return a.toString() === b.toString();
          keys2 = Object.keys(a);
          length2 = keys2.length;
          if (length2 !== Object.keys(b).length) return false;
          for (i = length2; i-- !== 0; )
            if (!Object.prototype.hasOwnProperty.call(b, keys2[i])) return false;
          for (i = length2; i-- !== 0; ) {
            var key = keys2[i];
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
        var map2 = Array.isArray(key) ? _arrayTreeMap : _objectTreeMap;
        for (var i = 0; i < properties.length; i++) {
          var property = properties[i];
          map2 = map2.get(property);
          if (map2 === void 0) {
            return;
          }
          var propertyValue = key[property];
          map2 = map2.get(propertyValue);
          if (map2 === void 0) {
            return;
          }
        }
        var valuePair = map2.get("_ekm_value");
        if (!valuePair) {
          return;
        }
        _map.delete(valuePair[0]);
        valuePair[0] = key;
        map2.set("_ekm_value", valuePair);
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
            var map2 = Array.isArray(key) ? this._arrayTreeMap : this._objectTreeMap;
            for (var i = 0; i < properties.length; i++) {
              var property = properties[i];
              if (!map2.has(property)) {
                map2.set(property, new EquivalentKeyMap3());
              }
              map2 = map2.get(property);
              var propertyValue = key[property];
              if (!map2.has(propertyValue)) {
                map2.set(propertyValue, new EquivalentKeyMap3());
              }
              map2 = map2.get(propertyValue);
            }
            var previousValuePair = map2.get("_ekm_value");
            if (previousValuePair) {
              this._map.delete(previousValuePair[0]);
            }
            map2.set("_ekm_value", valuePair);
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
          value: function forEach2(callback) {
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

  // package-external:@wordpress/hooks
  var require_hooks = __commonJS({
    "package-external:@wordpress/hooks"(exports, module) {
      module.exports = window.wp.hooks;
    }
  });

  // package-external:@wordpress/block-editor
  var require_block_editor = __commonJS({
    "package-external:@wordpress/block-editor"(exports, module) {
      module.exports = window.wp.blockEditor;
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

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
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
  var import_data11 = __toESM(require_data(), 1);

  // packages/core-data/build-module/reducer.mjs
  var import_es62 = __toESM(require_es6(), 1);
  var import_compose2 = __toESM(require_compose(), 1);
  var import_data3 = __toESM(require_data(), 1);
  var import_undo_manager2 = __toESM(require_undo_manager(), 1);

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
  var forwardResolver = (resolverName) => (...args2) => async ({ resolveSelect }) => {
    await resolveSelect[resolverName](...args2);
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
  function isNumericID(id2) {
    return /^\s*\d+\s*$/.test(id2);
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
  function getUserPermissionCacheKey(action, resource, id2) {
    const key = (typeof resource === "object" ? [action, resource.kind, resource.name, resource.id] : [action, resource, id2]).filter(Boolean).join("/");
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
    const keys2 = Object.keys(query).sort();
    for (let i = 0; i < keys2.length; i++) {
      const key = keys2[i];
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
  var import_data2 = __toESM(require_data(), 1);
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
  var import_blocks = __toESM(require_blocks(), 1);
  var import_i18n = __toESM(require_i18n(), 1);

  // node_modules/lib0/map.js
  var create = () => /* @__PURE__ */ new Map();
  var copy = (m) => {
    const r = create();
    m.forEach((v, k) => {
      r.set(k, v);
    });
    return r;
  };
  var setIfUndefined = (map2, key, createT) => {
    let set = map2.get(key);
    if (set === void 0) {
      map2.set(key, set = createT());
    }
    return set;
  };
  var map = (m, f) => {
    const res = [];
    for (const [key, value] of m) {
      res.push(f(value, key));
    }
    return res;
  };
  var any = (m, f) => {
    for (const [key, value] of m) {
      if (f(value, key)) {
        return true;
      }
    }
    return false;
  };

  // node_modules/lib0/set.js
  var create2 = () => /* @__PURE__ */ new Set();

  // node_modules/lib0/array.js
  var last = (arr) => arr[arr.length - 1];
  var appendTo = (dest, src) => {
    for (let i = 0; i < src.length; i++) {
      dest.push(src[i]);
    }
  };
  var from = Array.from;
  var some = (arr, f) => {
    for (let i = 0; i < arr.length; i++) {
      if (f(arr[i], i, arr)) {
        return true;
      }
    }
    return false;
  };
  var isArray = Array.isArray;

  // node_modules/lib0/observable.js
  var ObservableV2 = class {
    constructor() {
      this._observers = create();
    }
    /**
     * @template {keyof EVENTS & string} NAME
     * @param {NAME} name
     * @param {EVENTS[NAME]} f
     */
    on(name, f) {
      setIfUndefined(
        this._observers,
        /** @type {string} */
        name,
        create2
      ).add(f);
      return f;
    }
    /**
     * @template {keyof EVENTS & string} NAME
     * @param {NAME} name
     * @param {EVENTS[NAME]} f
     */
    once(name, f) {
      const _f = (...args2) => {
        this.off(
          name,
          /** @type {any} */
          _f
        );
        f(...args2);
      };
      this.on(
        name,
        /** @type {any} */
        _f
      );
    }
    /**
     * @template {keyof EVENTS & string} NAME
     * @param {NAME} name
     * @param {EVENTS[NAME]} f
     */
    off(name, f) {
      const observers = this._observers.get(name);
      if (observers !== void 0) {
        observers.delete(f);
        if (observers.size === 0) {
          this._observers.delete(name);
        }
      }
    }
    /**
     * Emit a named event. All registered event listeners that listen to the
     * specified name will receive the event.
     *
     * @todo This should catch exceptions
     *
     * @template {keyof EVENTS & string} NAME
     * @param {NAME} name The event name.
     * @param {Parameters<EVENTS[NAME]>} args The arguments that are applied to the event listener.
     */
    emit(name, args2) {
      return from((this._observers.get(name) || create()).values()).forEach((f) => f(...args2));
    }
    destroy() {
      this._observers = create();
    }
  };
  var Observable = class {
    constructor() {
      this._observers = create();
    }
    /**
     * @param {N} name
     * @param {function} f
     */
    on(name, f) {
      setIfUndefined(this._observers, name, create2).add(f);
    }
    /**
     * @param {N} name
     * @param {function} f
     */
    once(name, f) {
      const _f = (...args2) => {
        this.off(name, _f);
        f(...args2);
      };
      this.on(name, _f);
    }
    /**
     * @param {N} name
     * @param {function} f
     */
    off(name, f) {
      const observers = this._observers.get(name);
      if (observers !== void 0) {
        observers.delete(f);
        if (observers.size === 0) {
          this._observers.delete(name);
        }
      }
    }
    /**
     * Emit a named event. All registered event listeners that listen to the
     * specified name will receive the event.
     *
     * @todo This should catch exceptions
     *
     * @param {N} name The event name.
     * @param {Array<any>} args The arguments that are applied to the event listener.
     */
    emit(name, args2) {
      return from((this._observers.get(name) || create()).values()).forEach((f) => f(...args2));
    }
    destroy() {
      this._observers = create();
    }
  };

  // node_modules/lib0/math.js
  var floor = Math.floor;
  var abs = Math.abs;
  var min = (a, b) => a < b ? a : b;
  var max = (a, b) => a > b ? a : b;
  var isNaN = Number.isNaN;
  var isNegativeZero = (n) => n !== 0 ? n < 0 : 1 / n < 0;

  // node_modules/lib0/binary.js
  var BIT1 = 1;
  var BIT2 = 2;
  var BIT3 = 4;
  var BIT4 = 8;
  var BIT6 = 32;
  var BIT7 = 64;
  var BIT8 = 128;
  var BIT18 = 1 << 17;
  var BIT19 = 1 << 18;
  var BIT20 = 1 << 19;
  var BIT21 = 1 << 20;
  var BIT22 = 1 << 21;
  var BIT23 = 1 << 22;
  var BIT24 = 1 << 23;
  var BIT25 = 1 << 24;
  var BIT26 = 1 << 25;
  var BIT27 = 1 << 26;
  var BIT28 = 1 << 27;
  var BIT29 = 1 << 28;
  var BIT30 = 1 << 29;
  var BIT31 = 1 << 30;
  var BIT32 = 1 << 31;
  var BITS5 = 31;
  var BITS6 = 63;
  var BITS7 = 127;
  var BITS17 = BIT18 - 1;
  var BITS18 = BIT19 - 1;
  var BITS19 = BIT20 - 1;
  var BITS20 = BIT21 - 1;
  var BITS21 = BIT22 - 1;
  var BITS22 = BIT23 - 1;
  var BITS23 = BIT24 - 1;
  var BITS24 = BIT25 - 1;
  var BITS25 = BIT26 - 1;
  var BITS26 = BIT27 - 1;
  var BITS27 = BIT28 - 1;
  var BITS28 = BIT29 - 1;
  var BITS29 = BIT30 - 1;
  var BITS30 = BIT31 - 1;
  var BITS31 = 2147483647;

  // node_modules/lib0/number.js
  var MAX_SAFE_INTEGER = Number.MAX_SAFE_INTEGER;
  var MIN_SAFE_INTEGER = Number.MIN_SAFE_INTEGER;
  var LOWEST_INT32 = 1 << 31;
  var isInteger = Number.isInteger || ((num) => typeof num === "number" && isFinite(num) && floor(num) === num);
  var isNaN2 = Number.isNaN;
  var parseInt2 = Number.parseInt;

  // node_modules/lib0/string.js
  var fromCharCode = String.fromCharCode;
  var fromCodePoint = String.fromCodePoint;
  var MAX_UTF16_CHARACTER = fromCharCode(65535);
  var toLowerCase = (s) => s.toLowerCase();
  var trimLeftRegex = /^\s*/g;
  var trimLeft = (s) => s.replace(trimLeftRegex, "");
  var fromCamelCaseRegex = /([A-Z])/g;
  var fromCamelCase = (s, separator) => trimLeft(s.replace(fromCamelCaseRegex, (match) => `${separator}${toLowerCase(match)}`));
  var _encodeUtf8Polyfill = (str) => {
    const encodedString = unescape(encodeURIComponent(str));
    const len = encodedString.length;
    const buf = new Uint8Array(len);
    for (let i = 0; i < len; i++) {
      buf[i] = /** @type {number} */
      encodedString.codePointAt(i);
    }
    return buf;
  };
  var utf8TextEncoder = (
    /** @type {TextEncoder} */
    typeof TextEncoder !== "undefined" ? new TextEncoder() : null
  );
  var _encodeUtf8Native = (str) => utf8TextEncoder.encode(str);
  var encodeUtf8 = utf8TextEncoder ? _encodeUtf8Native : _encodeUtf8Polyfill;
  var utf8TextDecoder = typeof TextDecoder === "undefined" ? null : new TextDecoder("utf-8", { fatal: true, ignoreBOM: true });
  if (utf8TextDecoder && utf8TextDecoder.decode(new Uint8Array()).length === 1) {
    utf8TextDecoder = null;
  }

  // node_modules/lib0/encoding.js
  var Encoder = class {
    constructor() {
      this.cpos = 0;
      this.cbuf = new Uint8Array(100);
      this.bufs = [];
    }
  };
  var createEncoder = () => new Encoder();
  var length = (encoder) => {
    let len = encoder.cpos;
    for (let i = 0; i < encoder.bufs.length; i++) {
      len += encoder.bufs[i].length;
    }
    return len;
  };
  var toUint8Array = (encoder) => {
    const uint8arr = new Uint8Array(length(encoder));
    let curPos = 0;
    for (let i = 0; i < encoder.bufs.length; i++) {
      const d = encoder.bufs[i];
      uint8arr.set(d, curPos);
      curPos += d.length;
    }
    uint8arr.set(new Uint8Array(encoder.cbuf.buffer, 0, encoder.cpos), curPos);
    return uint8arr;
  };
  var verifyLen = (encoder, len) => {
    const bufferLen = encoder.cbuf.length;
    if (bufferLen - encoder.cpos < len) {
      encoder.bufs.push(new Uint8Array(encoder.cbuf.buffer, 0, encoder.cpos));
      encoder.cbuf = new Uint8Array(max(bufferLen, len) * 2);
      encoder.cpos = 0;
    }
  };
  var write = (encoder, num) => {
    const bufferLen = encoder.cbuf.length;
    if (encoder.cpos === bufferLen) {
      encoder.bufs.push(encoder.cbuf);
      encoder.cbuf = new Uint8Array(bufferLen * 2);
      encoder.cpos = 0;
    }
    encoder.cbuf[encoder.cpos++] = num;
  };
  var writeUint8 = write;
  var writeVarUint = (encoder, num) => {
    while (num > BITS7) {
      write(encoder, BIT8 | BITS7 & num);
      num = floor(num / 128);
    }
    write(encoder, BITS7 & num);
  };
  var writeVarInt = (encoder, num) => {
    const isNegative = isNegativeZero(num);
    if (isNegative) {
      num = -num;
    }
    write(encoder, (num > BITS6 ? BIT8 : 0) | (isNegative ? BIT7 : 0) | BITS6 & num);
    num = floor(num / 64);
    while (num > 0) {
      write(encoder, (num > BITS7 ? BIT8 : 0) | BITS7 & num);
      num = floor(num / 128);
    }
  };
  var _strBuffer = new Uint8Array(3e4);
  var _maxStrBSize = _strBuffer.length / 3;
  var _writeVarStringNative = (encoder, str) => {
    if (str.length < _maxStrBSize) {
      const written = utf8TextEncoder.encodeInto(str, _strBuffer).written || 0;
      writeVarUint(encoder, written);
      for (let i = 0; i < written; i++) {
        write(encoder, _strBuffer[i]);
      }
    } else {
      writeVarUint8Array(encoder, encodeUtf8(str));
    }
  };
  var _writeVarStringPolyfill = (encoder, str) => {
    const encodedString = unescape(encodeURIComponent(str));
    const len = encodedString.length;
    writeVarUint(encoder, len);
    for (let i = 0; i < len; i++) {
      write(
        encoder,
        /** @type {number} */
        encodedString.codePointAt(i)
      );
    }
  };
  var writeVarString = utf8TextEncoder && /** @type {any} */
  utf8TextEncoder.encodeInto ? _writeVarStringNative : _writeVarStringPolyfill;
  var writeUint8Array = (encoder, uint8Array) => {
    const bufferLen = encoder.cbuf.length;
    const cpos = encoder.cpos;
    const leftCopyLen = min(bufferLen - cpos, uint8Array.length);
    const rightCopyLen = uint8Array.length - leftCopyLen;
    encoder.cbuf.set(uint8Array.subarray(0, leftCopyLen), cpos);
    encoder.cpos += leftCopyLen;
    if (rightCopyLen > 0) {
      encoder.bufs.push(encoder.cbuf);
      encoder.cbuf = new Uint8Array(max(bufferLen * 2, rightCopyLen));
      encoder.cbuf.set(uint8Array.subarray(leftCopyLen));
      encoder.cpos = rightCopyLen;
    }
  };
  var writeVarUint8Array = (encoder, uint8Array) => {
    writeVarUint(encoder, uint8Array.byteLength);
    writeUint8Array(encoder, uint8Array);
  };
  var writeOnDataView = (encoder, len) => {
    verifyLen(encoder, len);
    const dview = new DataView(encoder.cbuf.buffer, encoder.cpos, len);
    encoder.cpos += len;
    return dview;
  };
  var writeFloat32 = (encoder, num) => writeOnDataView(encoder, 4).setFloat32(0, num, false);
  var writeFloat64 = (encoder, num) => writeOnDataView(encoder, 8).setFloat64(0, num, false);
  var writeBigInt64 = (encoder, num) => (
    /** @type {any} */
    writeOnDataView(encoder, 8).setBigInt64(0, num, false)
  );
  var floatTestBed = new DataView(new ArrayBuffer(4));
  var isFloat32 = (num) => {
    floatTestBed.setFloat32(0, num);
    return floatTestBed.getFloat32(0) === num;
  };
  var writeAny = (encoder, data) => {
    switch (typeof data) {
      case "string":
        write(encoder, 119);
        writeVarString(encoder, data);
        break;
      case "number":
        if (isInteger(data) && abs(data) <= BITS31) {
          write(encoder, 125);
          writeVarInt(encoder, data);
        } else if (isFloat32(data)) {
          write(encoder, 124);
          writeFloat32(encoder, data);
        } else {
          write(encoder, 123);
          writeFloat64(encoder, data);
        }
        break;
      case "bigint":
        write(encoder, 122);
        writeBigInt64(encoder, data);
        break;
      case "object":
        if (data === null) {
          write(encoder, 126);
        } else if (isArray(data)) {
          write(encoder, 117);
          writeVarUint(encoder, data.length);
          for (let i = 0; i < data.length; i++) {
            writeAny(encoder, data[i]);
          }
        } else if (data instanceof Uint8Array) {
          write(encoder, 116);
          writeVarUint8Array(encoder, data);
        } else {
          write(encoder, 118);
          const keys2 = Object.keys(data);
          writeVarUint(encoder, keys2.length);
          for (let i = 0; i < keys2.length; i++) {
            const key = keys2[i];
            writeVarString(encoder, key);
            writeAny(encoder, data[key]);
          }
        }
        break;
      case "boolean":
        write(encoder, data ? 120 : 121);
        break;
      default:
        write(encoder, 127);
    }
  };
  var RleEncoder = class extends Encoder {
    /**
     * @param {function(Encoder, T):void} writer
     */
    constructor(writer) {
      super();
      this.w = writer;
      this.s = null;
      this.count = 0;
    }
    /**
     * @param {T} v
     */
    write(v) {
      if (this.s === v) {
        this.count++;
      } else {
        if (this.count > 0) {
          writeVarUint(this, this.count - 1);
        }
        this.count = 1;
        this.w(this, v);
        this.s = v;
      }
    }
  };
  var flushUintOptRleEncoder = (encoder) => {
    if (encoder.count > 0) {
      writeVarInt(encoder.encoder, encoder.count === 1 ? encoder.s : -encoder.s);
      if (encoder.count > 1) {
        writeVarUint(encoder.encoder, encoder.count - 2);
      }
    }
  };
  var UintOptRleEncoder = class {
    constructor() {
      this.encoder = new Encoder();
      this.s = 0;
      this.count = 0;
    }
    /**
     * @param {number} v
     */
    write(v) {
      if (this.s === v) {
        this.count++;
      } else {
        flushUintOptRleEncoder(this);
        this.count = 1;
        this.s = v;
      }
    }
    /**
     * Flush the encoded state and transform this to a Uint8Array.
     *
     * Note that this should only be called once.
     */
    toUint8Array() {
      flushUintOptRleEncoder(this);
      return toUint8Array(this.encoder);
    }
  };
  var flushIntDiffOptRleEncoder = (encoder) => {
    if (encoder.count > 0) {
      const encodedDiff = encoder.diff * 2 + (encoder.count === 1 ? 0 : 1);
      writeVarInt(encoder.encoder, encodedDiff);
      if (encoder.count > 1) {
        writeVarUint(encoder.encoder, encoder.count - 2);
      }
    }
  };
  var IntDiffOptRleEncoder = class {
    constructor() {
      this.encoder = new Encoder();
      this.s = 0;
      this.count = 0;
      this.diff = 0;
    }
    /**
     * @param {number} v
     */
    write(v) {
      if (this.diff === v - this.s) {
        this.s = v;
        this.count++;
      } else {
        flushIntDiffOptRleEncoder(this);
        this.count = 1;
        this.diff = v - this.s;
        this.s = v;
      }
    }
    /**
     * Flush the encoded state and transform this to a Uint8Array.
     *
     * Note that this should only be called once.
     */
    toUint8Array() {
      flushIntDiffOptRleEncoder(this);
      return toUint8Array(this.encoder);
    }
  };
  var StringEncoder = class {
    constructor() {
      this.sarr = [];
      this.s = "";
      this.lensE = new UintOptRleEncoder();
    }
    /**
     * @param {string} string
     */
    write(string) {
      this.s += string;
      if (this.s.length > 19) {
        this.sarr.push(this.s);
        this.s = "";
      }
      this.lensE.write(string.length);
    }
    toUint8Array() {
      const encoder = new Encoder();
      this.sarr.push(this.s);
      this.s = "";
      writeVarString(encoder, this.sarr.join(""));
      writeUint8Array(encoder, this.lensE.toUint8Array());
      return toUint8Array(encoder);
    }
  };

  // node_modules/lib0/error.js
  var create3 = (s) => new Error(s);
  var methodUnimplemented = () => {
    throw create3("Method unimplemented");
  };
  var unexpectedCase = () => {
    throw create3("Unexpected case");
  };

  // node_modules/lib0/decoding.js
  var errorUnexpectedEndOfArray = create3("Unexpected end of array");
  var errorIntegerOutOfRange = create3("Integer out of Range");
  var Decoder = class {
    /**
     * @param {Uint8Array} uint8Array Binary data to decode
     */
    constructor(uint8Array) {
      this.arr = uint8Array;
      this.pos = 0;
    }
  };
  var createDecoder = (uint8Array) => new Decoder(uint8Array);
  var hasContent = (decoder) => decoder.pos !== decoder.arr.length;
  var readUint8Array = (decoder, len) => {
    const view = new Uint8Array(decoder.arr.buffer, decoder.pos + decoder.arr.byteOffset, len);
    decoder.pos += len;
    return view;
  };
  var readVarUint8Array = (decoder) => readUint8Array(decoder, readVarUint(decoder));
  var readUint8 = (decoder) => decoder.arr[decoder.pos++];
  var readVarUint = (decoder) => {
    let num = 0;
    let mult = 1;
    const len = decoder.arr.length;
    while (decoder.pos < len) {
      const r = decoder.arr[decoder.pos++];
      num = num + (r & BITS7) * mult;
      mult *= 128;
      if (r < BIT8) {
        return num;
      }
      if (num > MAX_SAFE_INTEGER) {
        throw errorIntegerOutOfRange;
      }
    }
    throw errorUnexpectedEndOfArray;
  };
  var readVarInt = (decoder) => {
    let r = decoder.arr[decoder.pos++];
    let num = r & BITS6;
    let mult = 64;
    const sign = (r & BIT7) > 0 ? -1 : 1;
    if ((r & BIT8) === 0) {
      return sign * num;
    }
    const len = decoder.arr.length;
    while (decoder.pos < len) {
      r = decoder.arr[decoder.pos++];
      num = num + (r & BITS7) * mult;
      mult *= 128;
      if (r < BIT8) {
        return sign * num;
      }
      if (num > MAX_SAFE_INTEGER) {
        throw errorIntegerOutOfRange;
      }
    }
    throw errorUnexpectedEndOfArray;
  };
  var _readVarStringPolyfill = (decoder) => {
    let remainingLen = readVarUint(decoder);
    if (remainingLen === 0) {
      return "";
    } else {
      let encodedString = String.fromCodePoint(readUint8(decoder));
      if (--remainingLen < 100) {
        while (remainingLen--) {
          encodedString += String.fromCodePoint(readUint8(decoder));
        }
      } else {
        while (remainingLen > 0) {
          const nextLen = remainingLen < 1e4 ? remainingLen : 1e4;
          const bytes = decoder.arr.subarray(decoder.pos, decoder.pos + nextLen);
          decoder.pos += nextLen;
          encodedString += String.fromCodePoint.apply(
            null,
            /** @type {any} */
            bytes
          );
          remainingLen -= nextLen;
        }
      }
      return decodeURIComponent(escape(encodedString));
    }
  };
  var _readVarStringNative = (decoder) => (
    /** @type any */
    utf8TextDecoder.decode(readVarUint8Array(decoder))
  );
  var readVarString = utf8TextDecoder ? _readVarStringNative : _readVarStringPolyfill;
  var readFromDataView = (decoder, len) => {
    const dv = new DataView(decoder.arr.buffer, decoder.arr.byteOffset + decoder.pos, len);
    decoder.pos += len;
    return dv;
  };
  var readFloat32 = (decoder) => readFromDataView(decoder, 4).getFloat32(0, false);
  var readFloat64 = (decoder) => readFromDataView(decoder, 8).getFloat64(0, false);
  var readBigInt64 = (decoder) => (
    /** @type {any} */
    readFromDataView(decoder, 8).getBigInt64(0, false)
  );
  var readAnyLookupTable = [
    (decoder) => void 0,
    // CASE 127: undefined
    (decoder) => null,
    // CASE 126: null
    readVarInt,
    // CASE 125: integer
    readFloat32,
    // CASE 124: float32
    readFloat64,
    // CASE 123: float64
    readBigInt64,
    // CASE 122: bigint
    (decoder) => false,
    // CASE 121: boolean (false)
    (decoder) => true,
    // CASE 120: boolean (true)
    readVarString,
    // CASE 119: string
    (decoder) => {
      const len = readVarUint(decoder);
      const obj = {};
      for (let i = 0; i < len; i++) {
        const key = readVarString(decoder);
        obj[key] = readAny(decoder);
      }
      return obj;
    },
    (decoder) => {
      const len = readVarUint(decoder);
      const arr = [];
      for (let i = 0; i < len; i++) {
        arr.push(readAny(decoder));
      }
      return arr;
    },
    readVarUint8Array
    // CASE 116: Uint8Array
  ];
  var readAny = (decoder) => readAnyLookupTable[127 - readUint8(decoder)](decoder);
  var RleDecoder = class extends Decoder {
    /**
     * @param {Uint8Array} uint8Array
     * @param {function(Decoder):T} reader
     */
    constructor(uint8Array, reader) {
      super(uint8Array);
      this.reader = reader;
      this.s = null;
      this.count = 0;
    }
    read() {
      if (this.count === 0) {
        this.s = this.reader(this);
        if (hasContent(this)) {
          this.count = readVarUint(this) + 1;
        } else {
          this.count = -1;
        }
      }
      this.count--;
      return (
        /** @type {T} */
        this.s
      );
    }
  };
  var UintOptRleDecoder = class extends Decoder {
    /**
     * @param {Uint8Array} uint8Array
     */
    constructor(uint8Array) {
      super(uint8Array);
      this.s = 0;
      this.count = 0;
    }
    read() {
      if (this.count === 0) {
        this.s = readVarInt(this);
        const isNegative = isNegativeZero(this.s);
        this.count = 1;
        if (isNegative) {
          this.s = -this.s;
          this.count = readVarUint(this) + 2;
        }
      }
      this.count--;
      return (
        /** @type {number} */
        this.s
      );
    }
  };
  var IntDiffOptRleDecoder = class extends Decoder {
    /**
     * @param {Uint8Array} uint8Array
     */
    constructor(uint8Array) {
      super(uint8Array);
      this.s = 0;
      this.count = 0;
      this.diff = 0;
    }
    /**
     * @return {number}
     */
    read() {
      if (this.count === 0) {
        const diff = readVarInt(this);
        const hasCount = diff & 1;
        this.diff = floor(diff / 2);
        this.count = 1;
        if (hasCount) {
          this.count = readVarUint(this) + 2;
        }
      }
      this.s += this.diff;
      this.count--;
      return this.s;
    }
  };
  var StringDecoder = class {
    /**
     * @param {Uint8Array} uint8Array
     */
    constructor(uint8Array) {
      this.decoder = new UintOptRleDecoder(uint8Array);
      this.str = readVarString(this.decoder);
      this.spos = 0;
    }
    /**
     * @return {string}
     */
    read() {
      const end = this.spos + this.decoder.read();
      const res = this.str.slice(this.spos, end);
      this.spos = end;
      return res;
    }
  };

  // node_modules/lib0/webcrypto.js
  var subtle = crypto.subtle;
  var getRandomValues = crypto.getRandomValues.bind(crypto);

  // node_modules/lib0/random.js
  var uint32 = () => getRandomValues(new Uint32Array(1))[0];
  var uuidv4Template = "10000000-1000-4000-8000" + -1e11;
  var uuidv4 = () => uuidv4Template.replace(
    /[018]/g,
    /** @param {number} c */
    (c) => (c ^ uint32() & 15 >> c / 4).toString(16)
  );

  // node_modules/lib0/time.js
  var getUnixTime = Date.now;

  // node_modules/lib0/promise.js
  var create4 = (f) => (
    /** @type {Promise<T>} */
    new Promise(f)
  );
  var all = Promise.all.bind(Promise);

  // node_modules/lib0/conditions.js
  var undefinedToNull = (v) => v === void 0 ? null : v;

  // node_modules/lib0/storage.js
  var VarStoragePolyfill = class {
    constructor() {
      this.map = /* @__PURE__ */ new Map();
    }
    /**
     * @param {string} key
     * @param {any} newValue
     */
    setItem(key, newValue) {
      this.map.set(key, newValue);
    }
    /**
     * @param {string} key
     */
    getItem(key) {
      return this.map.get(key);
    }
  };
  var _localStorage = new VarStoragePolyfill();
  var usePolyfill = true;
  try {
    if (typeof localStorage !== "undefined" && localStorage) {
      _localStorage = localStorage;
      usePolyfill = false;
    }
  } catch (e) {
  }
  var varStorage = _localStorage;

  // node_modules/lib0/object.js
  var assign = Object.assign;
  var keys = Object.keys;
  var forEach = (obj, f) => {
    for (const key in obj) {
      f(obj[key], key);
    }
  };
  var size = (obj) => keys(obj).length;
  var isEmpty = (obj) => {
    for (const _k in obj) {
      return false;
    }
    return true;
  };
  var every = (obj, f) => {
    for (const key in obj) {
      if (!f(obj[key], key)) {
        return false;
      }
    }
    return true;
  };
  var hasProperty = (obj, key) => Object.prototype.hasOwnProperty.call(obj, key);
  var equalFlat = (a, b) => a === b || size(a) === size(b) && every(a, (val, key) => (val !== void 0 || hasProperty(b, key)) && b[key] === val);
  var freeze = Object.freeze;
  var deepFreeze = (o) => {
    for (const key in o) {
      const c = o[key];
      if (typeof c === "object" || typeof c === "function") {
        deepFreeze(o[key]);
      }
    }
    return freeze(o);
  };

  // node_modules/lib0/function.js
  var callAll = (fs, args2, i = 0) => {
    try {
      for (; i < fs.length; i++) {
        fs[i](...args2);
      }
    } finally {
      if (i < fs.length) {
        callAll(fs, args2, i + 1);
      }
    }
  };
  var id = (a) => a;
  var isOneOf = (value, options) => options.includes(value);

  // node_modules/lib0/environment.js
  var isNode = typeof process !== "undefined" && process.release && /node|io\.js/.test(process.release.name) && Object.prototype.toString.call(typeof process !== "undefined" ? process : 0) === "[object process]";
  var isBrowser = typeof window !== "undefined" && typeof document !== "undefined" && !isNode;
  var isMac = typeof navigator !== "undefined" ? /Mac/.test(navigator.platform) : false;
  var params;
  var args = [];
  var computeParams = () => {
    if (params === void 0) {
      if (isNode) {
        params = create();
        const pargs = process.argv;
        let currParamName = null;
        for (let i = 0; i < pargs.length; i++) {
          const parg = pargs[i];
          if (parg[0] === "-") {
            if (currParamName !== null) {
              params.set(currParamName, "");
            }
            currParamName = parg;
          } else {
            if (currParamName !== null) {
              params.set(currParamName, parg);
              currParamName = null;
            } else {
              args.push(parg);
            }
          }
        }
        if (currParamName !== null) {
          params.set(currParamName, "");
        }
      } else if (typeof location === "object") {
        params = create();
        (location.search || "?").slice(1).split("&").forEach((kv) => {
          if (kv.length !== 0) {
            const [key, value] = kv.split("=");
            params.set(`--${fromCamelCase(key, "-")}`, value);
            params.set(`-${fromCamelCase(key, "-")}`, value);
          }
        });
      } else {
        params = create();
      }
    }
    return params;
  };
  var hasParam = (name) => computeParams().has(name);
  var getVariable = (name) => isNode ? undefinedToNull(process.env[name.toUpperCase().replaceAll("-", "_")]) : undefinedToNull(varStorage.getItem(name));
  var hasConf = (name) => hasParam("--" + name) || getVariable(name) !== null;
  var production = hasConf("production");
  var forceColor = isNode && isOneOf(process.env.FORCE_COLOR, ["true", "1", "2"]);
  var supportsColor = forceColor || !hasParam("--no-colors") && // @todo deprecate --no-colors
  !hasConf("no-color") && (!isNode || process.stdout.isTTY) && (!isNode || hasParam("--color") || getVariable("COLORTERM") !== null || (getVariable("TERM") || "").includes("color"));

  // node_modules/lib0/buffer.js
  var createUint8ArrayFromLen = (len) => new Uint8Array(len);
  var createUint8ArrayViewFromArrayBuffer = (buffer, byteOffset, length2) => new Uint8Array(buffer, byteOffset, length2);
  var toBase64Browser = (bytes) => {
    let s = "";
    for (let i = 0; i < bytes.byteLength; i++) {
      s += fromCharCode(bytes[i]);
    }
    return btoa(s);
  };
  var toBase64Node = (bytes) => Buffer.from(bytes.buffer, bytes.byteOffset, bytes.byteLength).toString("base64");
  var fromBase64Browser = (s) => {
    const a = atob(s);
    const bytes = createUint8ArrayFromLen(a.length);
    for (let i = 0; i < a.length; i++) {
      bytes[i] = a.charCodeAt(i);
    }
    return bytes;
  };
  var fromBase64Node = (s) => {
    const buf = Buffer.from(s, "base64");
    return createUint8ArrayViewFromArrayBuffer(buf.buffer, buf.byteOffset, buf.byteLength);
  };
  var toBase64 = isBrowser ? toBase64Browser : toBase64Node;
  var fromBase64 = isBrowser ? fromBase64Browser : fromBase64Node;
  var copyUint8Array = (uint8Array) => {
    const newBuf = createUint8ArrayFromLen(uint8Array.byteLength);
    newBuf.set(uint8Array);
    return newBuf;
  };

  // node_modules/lib0/pair.js
  var Pair = class {
    /**
     * @param {L} left
     * @param {R} right
     */
    constructor(left, right) {
      this.left = left;
      this.right = right;
    }
  };
  var create5 = (left, right) => new Pair(left, right);

  // node_modules/lib0/dom.js
  var doc = (
    /** @type {Document} */
    typeof document !== "undefined" ? document : {}
  );
  var domParser = (
    /** @type {DOMParser} */
    typeof DOMParser !== "undefined" ? new DOMParser() : null
  );
  var mapToStyleString = (m) => map(m, (value, key) => `${key}:${value};`).join("");
  var ELEMENT_NODE = doc.ELEMENT_NODE;
  var TEXT_NODE = doc.TEXT_NODE;
  var CDATA_SECTION_NODE = doc.CDATA_SECTION_NODE;
  var COMMENT_NODE = doc.COMMENT_NODE;
  var DOCUMENT_NODE = doc.DOCUMENT_NODE;
  var DOCUMENT_TYPE_NODE = doc.DOCUMENT_TYPE_NODE;
  var DOCUMENT_FRAGMENT_NODE = doc.DOCUMENT_FRAGMENT_NODE;

  // node_modules/lib0/symbol.js
  var create6 = Symbol;

  // node_modules/lib0/logging.common.js
  var BOLD = create6();
  var UNBOLD = create6();
  var BLUE = create6();
  var GREY = create6();
  var GREEN = create6();
  var RED = create6();
  var PURPLE = create6();
  var ORANGE = create6();
  var UNCOLOR = create6();
  var computeNoColorLoggingArgs = (args2) => {
    if (args2.length === 1 && args2[0]?.constructor === Function) {
      args2 = /** @type {Array<string|Symbol|Object|number>} */
      /** @type {[function]} */
      args2[0]();
    }
    const strBuilder = [];
    const logArgs = [];
    let i = 0;
    for (; i < args2.length; i++) {
      const arg = args2[i];
      if (arg === void 0) {
        break;
      } else if (arg.constructor === String || arg.constructor === Number) {
        strBuilder.push(arg);
      } else if (arg.constructor === Object) {
        break;
      }
    }
    if (i > 0) {
      logArgs.push(strBuilder.join(""));
    }
    for (; i < args2.length; i++) {
      const arg = args2[i];
      if (!(arg instanceof Symbol)) {
        logArgs.push(arg);
      }
    }
    return logArgs;
  };
  var lastLoggingTime = getUnixTime();

  // node_modules/lib0/logging.js
  var _browserStyleMap = {
    [BOLD]: create5("font-weight", "bold"),
    [UNBOLD]: create5("font-weight", "normal"),
    [BLUE]: create5("color", "blue"),
    [GREEN]: create5("color", "green"),
    [GREY]: create5("color", "grey"),
    [RED]: create5("color", "red"),
    [PURPLE]: create5("color", "purple"),
    [ORANGE]: create5("color", "orange"),
    // not well supported in chrome when debugging node with inspector - TODO: deprecate
    [UNCOLOR]: create5("color", "black")
  };
  var computeBrowserLoggingArgs = (args2) => {
    if (args2.length === 1 && args2[0]?.constructor === Function) {
      args2 = /** @type {Array<string|Symbol|Object|number>} */
      /** @type {[function]} */
      args2[0]();
    }
    const strBuilder = [];
    const styles = [];
    const currentStyle = create();
    let logArgs = [];
    let i = 0;
    for (; i < args2.length; i++) {
      const arg = args2[i];
      const style = _browserStyleMap[arg];
      if (style !== void 0) {
        currentStyle.set(style.left, style.right);
      } else {
        if (arg === void 0) {
          break;
        }
        if (arg.constructor === String || arg.constructor === Number) {
          const style2 = mapToStyleString(currentStyle);
          if (i > 0 || style2.length > 0) {
            strBuilder.push("%c" + arg);
            styles.push(style2);
          } else {
            strBuilder.push(arg);
          }
        } else {
          break;
        }
      }
    }
    if (i > 0) {
      logArgs = styles;
      logArgs.unshift(strBuilder.join(""));
    }
    for (; i < args2.length; i++) {
      const arg = args2[i];
      if (!(arg instanceof Symbol)) {
        logArgs.push(arg);
      }
    }
    return logArgs;
  };
  var computeLoggingArgs = supportsColor ? computeBrowserLoggingArgs : computeNoColorLoggingArgs;
  var print = (...args2) => {
    console.log(...computeLoggingArgs(args2));
    vconsoles.forEach((vc) => vc.print(args2));
  };
  var warn = (...args2) => {
    console.warn(...computeLoggingArgs(args2));
    args2.unshift(ORANGE);
    vconsoles.forEach((vc) => vc.print(args2));
  };
  var vconsoles = create2();

  // node_modules/lib0/iterator.js
  var createIterator = (next) => ({
    /**
     * @return {IterableIterator<T>}
     */
    [Symbol.iterator]() {
      return this;
    },
    // @ts-ignore
    next
  });
  var iteratorFilter = (iterator, filter) => createIterator(() => {
    let res;
    do {
      res = iterator.next();
    } while (!res.done && !filter(res.value));
    return res;
  });
  var iteratorMap = (iterator, fmap) => createIterator(() => {
    const { done, value } = iterator.next();
    return { done, value: done ? void 0 : fmap(value) };
  });

  // node_modules/yjs/dist/yjs.mjs
  var DeleteItem = class {
    /**
     * @param {number} clock
     * @param {number} len
     */
    constructor(clock, len) {
      this.clock = clock;
      this.len = len;
    }
  };
  var DeleteSet = class {
    constructor() {
      this.clients = /* @__PURE__ */ new Map();
    }
  };
  var iterateDeletedStructs = (transaction, ds, f) => ds.clients.forEach((deletes, clientid) => {
    const structs = (
      /** @type {Array<GC|Item>} */
      transaction.doc.store.clients.get(clientid)
    );
    if (structs != null) {
      const lastStruct = structs[structs.length - 1];
      const clockState = lastStruct.id.clock + lastStruct.length;
      for (let i = 0, del = deletes[i]; i < deletes.length && del.clock < clockState; del = deletes[++i]) {
        iterateStructs(transaction, structs, del.clock, del.len, f);
      }
    }
  });
  var findIndexDS = (dis, clock) => {
    let left = 0;
    let right = dis.length - 1;
    while (left <= right) {
      const midindex = floor((left + right) / 2);
      const mid = dis[midindex];
      const midclock = mid.clock;
      if (midclock <= clock) {
        if (clock < midclock + mid.len) {
          return midindex;
        }
        left = midindex + 1;
      } else {
        right = midindex - 1;
      }
    }
    return null;
  };
  var isDeleted = (ds, id2) => {
    const dis = ds.clients.get(id2.client);
    return dis !== void 0 && findIndexDS(dis, id2.clock) !== null;
  };
  var sortAndMergeDeleteSet = (ds) => {
    ds.clients.forEach((dels) => {
      dels.sort((a, b) => a.clock - b.clock);
      let i, j;
      for (i = 1, j = 1; i < dels.length; i++) {
        const left = dels[j - 1];
        const right = dels[i];
        if (left.clock + left.len >= right.clock) {
          left.len = max(left.len, right.clock + right.len - left.clock);
        } else {
          if (j < i) {
            dels[j] = right;
          }
          j++;
        }
      }
      dels.length = j;
    });
  };
  var mergeDeleteSets = (dss) => {
    const merged = new DeleteSet();
    for (let dssI = 0; dssI < dss.length; dssI++) {
      dss[dssI].clients.forEach((delsLeft, client) => {
        if (!merged.clients.has(client)) {
          const dels = delsLeft.slice();
          for (let i = dssI + 1; i < dss.length; i++) {
            appendTo(dels, dss[i].clients.get(client) || []);
          }
          merged.clients.set(client, dels);
        }
      });
    }
    sortAndMergeDeleteSet(merged);
    return merged;
  };
  var addToDeleteSet = (ds, client, clock, length2) => {
    setIfUndefined(ds.clients, client, () => (
      /** @type {Array<DeleteItem>} */
      []
    )).push(new DeleteItem(clock, length2));
  };
  var createDeleteSet = () => new DeleteSet();
  var createDeleteSetFromStructStore = (ss) => {
    const ds = createDeleteSet();
    ss.clients.forEach((structs, client) => {
      const dsitems = [];
      for (let i = 0; i < structs.length; i++) {
        const struct = structs[i];
        if (struct.deleted) {
          const clock = struct.id.clock;
          let len = struct.length;
          if (i + 1 < structs.length) {
            for (let next = structs[i + 1]; i + 1 < structs.length && next.deleted; next = structs[++i + 1]) {
              len += next.length;
            }
          }
          dsitems.push(new DeleteItem(clock, len));
        }
      }
      if (dsitems.length > 0) {
        ds.clients.set(client, dsitems);
      }
    });
    return ds;
  };
  var writeDeleteSet = (encoder, ds) => {
    writeVarUint(encoder.restEncoder, ds.clients.size);
    from(ds.clients.entries()).sort((a, b) => b[0] - a[0]).forEach(([client, dsitems]) => {
      encoder.resetDsCurVal();
      writeVarUint(encoder.restEncoder, client);
      const len = dsitems.length;
      writeVarUint(encoder.restEncoder, len);
      for (let i = 0; i < len; i++) {
        const item = dsitems[i];
        encoder.writeDsClock(item.clock);
        encoder.writeDsLen(item.len);
      }
    });
  };
  var readDeleteSet = (decoder) => {
    const ds = new DeleteSet();
    const numClients = readVarUint(decoder.restDecoder);
    for (let i = 0; i < numClients; i++) {
      decoder.resetDsCurVal();
      const client = readVarUint(decoder.restDecoder);
      const numberOfDeletes = readVarUint(decoder.restDecoder);
      if (numberOfDeletes > 0) {
        const dsField = setIfUndefined(ds.clients, client, () => (
          /** @type {Array<DeleteItem>} */
          []
        ));
        for (let i2 = 0; i2 < numberOfDeletes; i2++) {
          dsField.push(new DeleteItem(decoder.readDsClock(), decoder.readDsLen()));
        }
      }
    }
    return ds;
  };
  var readAndApplyDeleteSet = (decoder, transaction, store2) => {
    const unappliedDS = new DeleteSet();
    const numClients = readVarUint(decoder.restDecoder);
    for (let i = 0; i < numClients; i++) {
      decoder.resetDsCurVal();
      const client = readVarUint(decoder.restDecoder);
      const numberOfDeletes = readVarUint(decoder.restDecoder);
      const structs = store2.clients.get(client) || [];
      const state = getState(store2, client);
      for (let i2 = 0; i2 < numberOfDeletes; i2++) {
        const clock = decoder.readDsClock();
        const clockEnd = clock + decoder.readDsLen();
        if (clock < state) {
          if (state < clockEnd) {
            addToDeleteSet(unappliedDS, client, state, clockEnd - state);
          }
          let index = findIndexSS(structs, clock);
          let struct = structs[index];
          if (!struct.deleted && struct.id.clock < clock) {
            structs.splice(index + 1, 0, splitItem(transaction, struct, clock - struct.id.clock));
            index++;
          }
          while (index < structs.length) {
            struct = structs[index++];
            if (struct.id.clock < clockEnd) {
              if (!struct.deleted) {
                if (clockEnd < struct.id.clock + struct.length) {
                  structs.splice(index, 0, splitItem(transaction, struct, clockEnd - struct.id.clock));
                }
                struct.delete(transaction);
              }
            } else {
              break;
            }
          }
        } else {
          addToDeleteSet(unappliedDS, client, clock, clockEnd - clock);
        }
      }
    }
    if (unappliedDS.clients.size > 0) {
      const ds = new UpdateEncoderV2();
      writeVarUint(ds.restEncoder, 0);
      writeDeleteSet(ds, unappliedDS);
      return ds.toUint8Array();
    }
    return null;
  };
  var generateNewClientId = uint32;
  var Doc = class _Doc extends ObservableV2 {
    /**
     * @param {DocOpts} opts configuration
     */
    constructor({ guid = uuidv4(), collectionid = null, gc = true, gcFilter = () => true, meta = null, autoLoad = false, shouldLoad = true } = {}) {
      super();
      this.gc = gc;
      this.gcFilter = gcFilter;
      this.clientID = generateNewClientId();
      this.guid = guid;
      this.collectionid = collectionid;
      this.share = /* @__PURE__ */ new Map();
      this.store = new StructStore();
      this._transaction = null;
      this._transactionCleanups = [];
      this.subdocs = /* @__PURE__ */ new Set();
      this._item = null;
      this.shouldLoad = shouldLoad;
      this.autoLoad = autoLoad;
      this.meta = meta;
      this.isLoaded = false;
      this.isSynced = false;
      this.isDestroyed = false;
      this.whenLoaded = create4((resolve) => {
        this.on("load", () => {
          this.isLoaded = true;
          resolve(this);
        });
      });
      const provideSyncedPromise = () => create4((resolve) => {
        const eventHandler = (isSynced) => {
          if (isSynced === void 0 || isSynced === true) {
            this.off("sync", eventHandler);
            resolve();
          }
        };
        this.on("sync", eventHandler);
      });
      this.on("sync", (isSynced) => {
        if (isSynced === false && this.isSynced) {
          this.whenSynced = provideSyncedPromise();
        }
        this.isSynced = isSynced === void 0 || isSynced === true;
        if (this.isSynced && !this.isLoaded) {
          this.emit("load", [this]);
        }
      });
      this.whenSynced = provideSyncedPromise();
    }
    /**
     * Notify the parent document that you request to load data into this subdocument (if it is a subdocument).
     *
     * `load()` might be used in the future to request any provider to load the most current data.
     *
     * It is safe to call `load()` multiple times.
     */
    load() {
      const item = this._item;
      if (item !== null && !this.shouldLoad) {
        transact(
          /** @type {any} */
          item.parent.doc,
          (transaction) => {
            transaction.subdocsLoaded.add(this);
          },
          null,
          true
        );
      }
      this.shouldLoad = true;
    }
    getSubdocs() {
      return this.subdocs;
    }
    getSubdocGuids() {
      return new Set(from(this.subdocs).map((doc2) => doc2.guid));
    }
    /**
     * Changes that happen inside of a transaction are bundled. This means that
     * the observer fires _after_ the transaction is finished and that all changes
     * that happened inside of the transaction are sent as one message to the
     * other peers.
     *
     * @template T
     * @param {function(Transaction):T} f The function that should be executed as a transaction
     * @param {any} [origin] Origin of who started the transaction. Will be stored on transaction.origin
     * @return T
     *
     * @public
     */
    transact(f, origin2 = null) {
      return transact(this, f, origin2);
    }
    /**
     * Define a shared data type.
     *
     * Multiple calls of `ydoc.get(name, TypeConstructor)` yield the same result
     * and do not overwrite each other. I.e.
     * `ydoc.get(name, Y.Array) === ydoc.get(name, Y.Array)`
     *
     * After this method is called, the type is also available on `ydoc.share.get(name)`.
     *
     * *Best Practices:*
     * Define all types right after the Y.Doc instance is created and store them in a separate object.
     * Also use the typed methods `getText(name)`, `getArray(name)`, ..
     *
     * @template {typeof AbstractType<any>} Type
     * @example
     *   const ydoc = new Y.Doc(..)
     *   const appState = {
     *     document: ydoc.getText('document')
     *     comments: ydoc.getArray('comments')
     *   }
     *
     * @param {string} name
     * @param {Type} TypeConstructor The constructor of the type definition. E.g. Y.Text, Y.Array, Y.Map, ...
     * @return {InstanceType<Type>} The created type. Constructed with TypeConstructor
     *
     * @public
     */
    get(name, TypeConstructor = (
      /** @type {any} */
      AbstractType
    )) {
      const type = setIfUndefined(this.share, name, () => {
        const t = new TypeConstructor();
        t._integrate(this, null);
        return t;
      });
      const Constr = type.constructor;
      if (TypeConstructor !== AbstractType && Constr !== TypeConstructor) {
        if (Constr === AbstractType) {
          const t = new TypeConstructor();
          t._map = type._map;
          type._map.forEach(
            /** @param {Item?} n */
            (n) => {
              for (; n !== null; n = n.left) {
                n.parent = t;
              }
            }
          );
          t._start = type._start;
          for (let n = t._start; n !== null; n = n.right) {
            n.parent = t;
          }
          t._length = type._length;
          this.share.set(name, t);
          t._integrate(this, null);
          return (
            /** @type {InstanceType<Type>} */
            t
          );
        } else {
          throw new Error(`Type with the name ${name} has already been defined with a different constructor`);
        }
      }
      return (
        /** @type {InstanceType<Type>} */
        type
      );
    }
    /**
     * @template T
     * @param {string} [name]
     * @return {YArray<T>}
     *
     * @public
     */
    getArray(name = "") {
      return (
        /** @type {YArray<T>} */
        this.get(name, YArray)
      );
    }
    /**
     * @param {string} [name]
     * @return {YText}
     *
     * @public
     */
    getText(name = "") {
      return this.get(name, YText);
    }
    /**
     * @template T
     * @param {string} [name]
     * @return {YMap<T>}
     *
     * @public
     */
    getMap(name = "") {
      return (
        /** @type {YMap<T>} */
        this.get(name, YMap)
      );
    }
    /**
     * @param {string} [name]
     * @return {YXmlElement}
     *
     * @public
     */
    getXmlElement(name = "") {
      return (
        /** @type {YXmlElement<{[key:string]:string}>} */
        this.get(name, YXmlElement)
      );
    }
    /**
     * @param {string} [name]
     * @return {YXmlFragment}
     *
     * @public
     */
    getXmlFragment(name = "") {
      return this.get(name, YXmlFragment);
    }
    /**
     * Converts the entire document into a js object, recursively traversing each yjs type
     * Doesn't log types that have not been defined (using ydoc.getType(..)).
     *
     * @deprecated Do not use this method and rather call toJSON directly on the shared types.
     *
     * @return {Object<string, any>}
     */
    toJSON() {
      const doc2 = {};
      this.share.forEach((value, key) => {
        doc2[key] = value.toJSON();
      });
      return doc2;
    }
    /**
     * Emit `destroy` event and unregister all event handlers.
     */
    destroy() {
      this.isDestroyed = true;
      from(this.subdocs).forEach((subdoc) => subdoc.destroy());
      const item = this._item;
      if (item !== null) {
        this._item = null;
        const content = (
          /** @type {ContentDoc} */
          item.content
        );
        content.doc = new _Doc({ guid: this.guid, ...content.opts, shouldLoad: false });
        content.doc._item = item;
        transact(
          /** @type {any} */
          item.parent.doc,
          (transaction) => {
            const doc2 = content.doc;
            if (!item.deleted) {
              transaction.subdocsAdded.add(doc2);
            }
            transaction.subdocsRemoved.add(this);
          },
          null,
          true
        );
      }
      this.emit("destroyed", [true]);
      this.emit("destroy", [this]);
      super.destroy();
    }
  };
  var DSDecoderV1 = class {
    /**
     * @param {decoding.Decoder} decoder
     */
    constructor(decoder) {
      this.restDecoder = decoder;
    }
    resetDsCurVal() {
    }
    /**
     * @return {number}
     */
    readDsClock() {
      return readVarUint(this.restDecoder);
    }
    /**
     * @return {number}
     */
    readDsLen() {
      return readVarUint(this.restDecoder);
    }
  };
  var UpdateDecoderV1 = class extends DSDecoderV1 {
    /**
     * @return {ID}
     */
    readLeftID() {
      return createID(readVarUint(this.restDecoder), readVarUint(this.restDecoder));
    }
    /**
     * @return {ID}
     */
    readRightID() {
      return createID(readVarUint(this.restDecoder), readVarUint(this.restDecoder));
    }
    /**
     * Read the next client id.
     * Use this in favor of readID whenever possible to reduce the number of objects created.
     */
    readClient() {
      return readVarUint(this.restDecoder);
    }
    /**
     * @return {number} info An unsigned 8-bit integer
     */
    readInfo() {
      return readUint8(this.restDecoder);
    }
    /**
     * @return {string}
     */
    readString() {
      return readVarString(this.restDecoder);
    }
    /**
     * @return {boolean} isKey
     */
    readParentInfo() {
      return readVarUint(this.restDecoder) === 1;
    }
    /**
     * @return {number} info An unsigned 8-bit integer
     */
    readTypeRef() {
      return readVarUint(this.restDecoder);
    }
    /**
     * Write len of a struct - well suited for Opt RLE encoder.
     *
     * @return {number} len
     */
    readLen() {
      return readVarUint(this.restDecoder);
    }
    /**
     * @return {any}
     */
    readAny() {
      return readAny(this.restDecoder);
    }
    /**
     * @return {Uint8Array}
     */
    readBuf() {
      return copyUint8Array(readVarUint8Array(this.restDecoder));
    }
    /**
     * Legacy implementation uses JSON parse. We use any-decoding in v2.
     *
     * @return {any}
     */
    readJSON() {
      return JSON.parse(readVarString(this.restDecoder));
    }
    /**
     * @return {string}
     */
    readKey() {
      return readVarString(this.restDecoder);
    }
  };
  var DSDecoderV2 = class {
    /**
     * @param {decoding.Decoder} decoder
     */
    constructor(decoder) {
      this.dsCurrVal = 0;
      this.restDecoder = decoder;
    }
    resetDsCurVal() {
      this.dsCurrVal = 0;
    }
    /**
     * @return {number}
     */
    readDsClock() {
      this.dsCurrVal += readVarUint(this.restDecoder);
      return this.dsCurrVal;
    }
    /**
     * @return {number}
     */
    readDsLen() {
      const diff = readVarUint(this.restDecoder) + 1;
      this.dsCurrVal += diff;
      return diff;
    }
  };
  var UpdateDecoderV2 = class extends DSDecoderV2 {
    /**
     * @param {decoding.Decoder} decoder
     */
    constructor(decoder) {
      super(decoder);
      this.keys = [];
      readVarUint(decoder);
      this.keyClockDecoder = new IntDiffOptRleDecoder(readVarUint8Array(decoder));
      this.clientDecoder = new UintOptRleDecoder(readVarUint8Array(decoder));
      this.leftClockDecoder = new IntDiffOptRleDecoder(readVarUint8Array(decoder));
      this.rightClockDecoder = new IntDiffOptRleDecoder(readVarUint8Array(decoder));
      this.infoDecoder = new RleDecoder(readVarUint8Array(decoder), readUint8);
      this.stringDecoder = new StringDecoder(readVarUint8Array(decoder));
      this.parentInfoDecoder = new RleDecoder(readVarUint8Array(decoder), readUint8);
      this.typeRefDecoder = new UintOptRleDecoder(readVarUint8Array(decoder));
      this.lenDecoder = new UintOptRleDecoder(readVarUint8Array(decoder));
    }
    /**
     * @return {ID}
     */
    readLeftID() {
      return new ID(this.clientDecoder.read(), this.leftClockDecoder.read());
    }
    /**
     * @return {ID}
     */
    readRightID() {
      return new ID(this.clientDecoder.read(), this.rightClockDecoder.read());
    }
    /**
     * Read the next client id.
     * Use this in favor of readID whenever possible to reduce the number of objects created.
     */
    readClient() {
      return this.clientDecoder.read();
    }
    /**
     * @return {number} info An unsigned 8-bit integer
     */
    readInfo() {
      return (
        /** @type {number} */
        this.infoDecoder.read()
      );
    }
    /**
     * @return {string}
     */
    readString() {
      return this.stringDecoder.read();
    }
    /**
     * @return {boolean}
     */
    readParentInfo() {
      return this.parentInfoDecoder.read() === 1;
    }
    /**
     * @return {number} An unsigned 8-bit integer
     */
    readTypeRef() {
      return this.typeRefDecoder.read();
    }
    /**
     * Write len of a struct - well suited for Opt RLE encoder.
     *
     * @return {number}
     */
    readLen() {
      return this.lenDecoder.read();
    }
    /**
     * @return {any}
     */
    readAny() {
      return readAny(this.restDecoder);
    }
    /**
     * @return {Uint8Array}
     */
    readBuf() {
      return readVarUint8Array(this.restDecoder);
    }
    /**
     * This is mainly here for legacy purposes.
     *
     * Initial we incoded objects using JSON. Now we use the much faster lib0/any-encoder. This method mainly exists for legacy purposes for the v1 encoder.
     *
     * @return {any}
     */
    readJSON() {
      return readAny(this.restDecoder);
    }
    /**
     * @return {string}
     */
    readKey() {
      const keyClock = this.keyClockDecoder.read();
      if (keyClock < this.keys.length) {
        return this.keys[keyClock];
      } else {
        const key = this.stringDecoder.read();
        this.keys.push(key);
        return key;
      }
    }
  };
  var DSEncoderV1 = class {
    constructor() {
      this.restEncoder = createEncoder();
    }
    toUint8Array() {
      return toUint8Array(this.restEncoder);
    }
    resetDsCurVal() {
    }
    /**
     * @param {number} clock
     */
    writeDsClock(clock) {
      writeVarUint(this.restEncoder, clock);
    }
    /**
     * @param {number} len
     */
    writeDsLen(len) {
      writeVarUint(this.restEncoder, len);
    }
  };
  var UpdateEncoderV1 = class extends DSEncoderV1 {
    /**
     * @param {ID} id
     */
    writeLeftID(id2) {
      writeVarUint(this.restEncoder, id2.client);
      writeVarUint(this.restEncoder, id2.clock);
    }
    /**
     * @param {ID} id
     */
    writeRightID(id2) {
      writeVarUint(this.restEncoder, id2.client);
      writeVarUint(this.restEncoder, id2.clock);
    }
    /**
     * Use writeClient and writeClock instead of writeID if possible.
     * @param {number} client
     */
    writeClient(client) {
      writeVarUint(this.restEncoder, client);
    }
    /**
     * @param {number} info An unsigned 8-bit integer
     */
    writeInfo(info) {
      writeUint8(this.restEncoder, info);
    }
    /**
     * @param {string} s
     */
    writeString(s) {
      writeVarString(this.restEncoder, s);
    }
    /**
     * @param {boolean} isYKey
     */
    writeParentInfo(isYKey) {
      writeVarUint(this.restEncoder, isYKey ? 1 : 0);
    }
    /**
     * @param {number} info An unsigned 8-bit integer
     */
    writeTypeRef(info) {
      writeVarUint(this.restEncoder, info);
    }
    /**
     * Write len of a struct - well suited for Opt RLE encoder.
     *
     * @param {number} len
     */
    writeLen(len) {
      writeVarUint(this.restEncoder, len);
    }
    /**
     * @param {any} any
     */
    writeAny(any2) {
      writeAny(this.restEncoder, any2);
    }
    /**
     * @param {Uint8Array} buf
     */
    writeBuf(buf) {
      writeVarUint8Array(this.restEncoder, buf);
    }
    /**
     * @param {any} embed
     */
    writeJSON(embed) {
      writeVarString(this.restEncoder, JSON.stringify(embed));
    }
    /**
     * @param {string} key
     */
    writeKey(key) {
      writeVarString(this.restEncoder, key);
    }
  };
  var DSEncoderV2 = class {
    constructor() {
      this.restEncoder = createEncoder();
      this.dsCurrVal = 0;
    }
    toUint8Array() {
      return toUint8Array(this.restEncoder);
    }
    resetDsCurVal() {
      this.dsCurrVal = 0;
    }
    /**
     * @param {number} clock
     */
    writeDsClock(clock) {
      const diff = clock - this.dsCurrVal;
      this.dsCurrVal = clock;
      writeVarUint(this.restEncoder, diff);
    }
    /**
     * @param {number} len
     */
    writeDsLen(len) {
      if (len === 0) {
        unexpectedCase();
      }
      writeVarUint(this.restEncoder, len - 1);
      this.dsCurrVal += len;
    }
  };
  var UpdateEncoderV2 = class extends DSEncoderV2 {
    constructor() {
      super();
      this.keyMap = /* @__PURE__ */ new Map();
      this.keyClock = 0;
      this.keyClockEncoder = new IntDiffOptRleEncoder();
      this.clientEncoder = new UintOptRleEncoder();
      this.leftClockEncoder = new IntDiffOptRleEncoder();
      this.rightClockEncoder = new IntDiffOptRleEncoder();
      this.infoEncoder = new RleEncoder(writeUint8);
      this.stringEncoder = new StringEncoder();
      this.parentInfoEncoder = new RleEncoder(writeUint8);
      this.typeRefEncoder = new UintOptRleEncoder();
      this.lenEncoder = new UintOptRleEncoder();
    }
    toUint8Array() {
      const encoder = createEncoder();
      writeVarUint(encoder, 0);
      writeVarUint8Array(encoder, this.keyClockEncoder.toUint8Array());
      writeVarUint8Array(encoder, this.clientEncoder.toUint8Array());
      writeVarUint8Array(encoder, this.leftClockEncoder.toUint8Array());
      writeVarUint8Array(encoder, this.rightClockEncoder.toUint8Array());
      writeVarUint8Array(encoder, toUint8Array(this.infoEncoder));
      writeVarUint8Array(encoder, this.stringEncoder.toUint8Array());
      writeVarUint8Array(encoder, toUint8Array(this.parentInfoEncoder));
      writeVarUint8Array(encoder, this.typeRefEncoder.toUint8Array());
      writeVarUint8Array(encoder, this.lenEncoder.toUint8Array());
      writeUint8Array(encoder, toUint8Array(this.restEncoder));
      return toUint8Array(encoder);
    }
    /**
     * @param {ID} id
     */
    writeLeftID(id2) {
      this.clientEncoder.write(id2.client);
      this.leftClockEncoder.write(id2.clock);
    }
    /**
     * @param {ID} id
     */
    writeRightID(id2) {
      this.clientEncoder.write(id2.client);
      this.rightClockEncoder.write(id2.clock);
    }
    /**
     * @param {number} client
     */
    writeClient(client) {
      this.clientEncoder.write(client);
    }
    /**
     * @param {number} info An unsigned 8-bit integer
     */
    writeInfo(info) {
      this.infoEncoder.write(info);
    }
    /**
     * @param {string} s
     */
    writeString(s) {
      this.stringEncoder.write(s);
    }
    /**
     * @param {boolean} isYKey
     */
    writeParentInfo(isYKey) {
      this.parentInfoEncoder.write(isYKey ? 1 : 0);
    }
    /**
     * @param {number} info An unsigned 8-bit integer
     */
    writeTypeRef(info) {
      this.typeRefEncoder.write(info);
    }
    /**
     * Write len of a struct - well suited for Opt RLE encoder.
     *
     * @param {number} len
     */
    writeLen(len) {
      this.lenEncoder.write(len);
    }
    /**
     * @param {any} any
     */
    writeAny(any2) {
      writeAny(this.restEncoder, any2);
    }
    /**
     * @param {Uint8Array} buf
     */
    writeBuf(buf) {
      writeVarUint8Array(this.restEncoder, buf);
    }
    /**
     * This is mainly here for legacy purposes.
     *
     * Initial we incoded objects using JSON. Now we use the much faster lib0/any-encoder. This method mainly exists for legacy purposes for the v1 encoder.
     *
     * @param {any} embed
     */
    writeJSON(embed) {
      writeAny(this.restEncoder, embed);
    }
    /**
     * Property keys are often reused. For example, in y-prosemirror the key `bold` might
     * occur very often. For a 3d application, the key `position` might occur very often.
     *
     * We cache these keys in a Map and refer to them via a unique number.
     *
     * @param {string} key
     */
    writeKey(key) {
      const clock = this.keyMap.get(key);
      if (clock === void 0) {
        this.keyClockEncoder.write(this.keyClock++);
        this.stringEncoder.write(key);
      } else {
        this.keyClockEncoder.write(clock);
      }
    }
  };
  var writeStructs = (encoder, structs, client, clock) => {
    clock = max(clock, structs[0].id.clock);
    const startNewStructs = findIndexSS(structs, clock);
    writeVarUint(encoder.restEncoder, structs.length - startNewStructs);
    encoder.writeClient(client);
    writeVarUint(encoder.restEncoder, clock);
    const firstStruct = structs[startNewStructs];
    firstStruct.write(encoder, clock - firstStruct.id.clock);
    for (let i = startNewStructs + 1; i < structs.length; i++) {
      structs[i].write(encoder, 0);
    }
  };
  var writeClientsStructs = (encoder, store2, _sm) => {
    const sm = /* @__PURE__ */ new Map();
    _sm.forEach((clock, client) => {
      if (getState(store2, client) > clock) {
        sm.set(client, clock);
      }
    });
    getStateVector(store2).forEach((_clock, client) => {
      if (!_sm.has(client)) {
        sm.set(client, 0);
      }
    });
    writeVarUint(encoder.restEncoder, sm.size);
    from(sm.entries()).sort((a, b) => b[0] - a[0]).forEach(([client, clock]) => {
      writeStructs(
        encoder,
        /** @type {Array<GC|Item>} */
        store2.clients.get(client),
        client,
        clock
      );
    });
  };
  var readClientsStructRefs = (decoder, doc2) => {
    const clientRefs = create();
    const numOfStateUpdates = readVarUint(decoder.restDecoder);
    for (let i = 0; i < numOfStateUpdates; i++) {
      const numberOfStructs = readVarUint(decoder.restDecoder);
      const refs = new Array(numberOfStructs);
      const client = decoder.readClient();
      let clock = readVarUint(decoder.restDecoder);
      clientRefs.set(client, { i: 0, refs });
      for (let i2 = 0; i2 < numberOfStructs; i2++) {
        const info = decoder.readInfo();
        switch (BITS5 & info) {
          case 0: {
            const len = decoder.readLen();
            refs[i2] = new GC(createID(client, clock), len);
            clock += len;
            break;
          }
          case 10: {
            const len = readVarUint(decoder.restDecoder);
            refs[i2] = new Skip(createID(client, clock), len);
            clock += len;
            break;
          }
          default: {
            const cantCopyParentInfo = (info & (BIT7 | BIT8)) === 0;
            const struct = new Item(
              createID(client, clock),
              null,
              // left
              (info & BIT8) === BIT8 ? decoder.readLeftID() : null,
              // origin
              null,
              // right
              (info & BIT7) === BIT7 ? decoder.readRightID() : null,
              // right origin
              cantCopyParentInfo ? decoder.readParentInfo() ? doc2.get(decoder.readString()) : decoder.readLeftID() : null,
              // parent
              cantCopyParentInfo && (info & BIT6) === BIT6 ? decoder.readString() : null,
              // parentSub
              readItemContent(decoder, info)
              // item content
            );
            refs[i2] = struct;
            clock += struct.length;
          }
        }
      }
    }
    return clientRefs;
  };
  var integrateStructs = (transaction, store2, clientsStructRefs) => {
    const stack = [];
    let clientsStructRefsIds = from(clientsStructRefs.keys()).sort((a, b) => a - b);
    if (clientsStructRefsIds.length === 0) {
      return null;
    }
    const getNextStructTarget = () => {
      if (clientsStructRefsIds.length === 0) {
        return null;
      }
      let nextStructsTarget = (
        /** @type {{i:number,refs:Array<GC|Item>}} */
        clientsStructRefs.get(clientsStructRefsIds[clientsStructRefsIds.length - 1])
      );
      while (nextStructsTarget.refs.length === nextStructsTarget.i) {
        clientsStructRefsIds.pop();
        if (clientsStructRefsIds.length > 0) {
          nextStructsTarget = /** @type {{i:number,refs:Array<GC|Item>}} */
          clientsStructRefs.get(clientsStructRefsIds[clientsStructRefsIds.length - 1]);
        } else {
          return null;
        }
      }
      return nextStructsTarget;
    };
    let curStructsTarget = getNextStructTarget();
    if (curStructsTarget === null) {
      return null;
    }
    const restStructs = new StructStore();
    const missingSV = /* @__PURE__ */ new Map();
    const updateMissingSv = (client, clock) => {
      const mclock = missingSV.get(client);
      if (mclock == null || mclock > clock) {
        missingSV.set(client, clock);
      }
    };
    let stackHead = (
      /** @type {any} */
      curStructsTarget.refs[
        /** @type {any} */
        curStructsTarget.i++
      ]
    );
    const state = /* @__PURE__ */ new Map();
    const addStackToRestSS = () => {
      for (const item of stack) {
        const client = item.id.client;
        const inapplicableItems = clientsStructRefs.get(client);
        if (inapplicableItems) {
          inapplicableItems.i--;
          restStructs.clients.set(client, inapplicableItems.refs.slice(inapplicableItems.i));
          clientsStructRefs.delete(client);
          inapplicableItems.i = 0;
          inapplicableItems.refs = [];
        } else {
          restStructs.clients.set(client, [item]);
        }
        clientsStructRefsIds = clientsStructRefsIds.filter((c) => c !== client);
      }
      stack.length = 0;
    };
    while (true) {
      if (stackHead.constructor !== Skip) {
        const localClock = setIfUndefined(state, stackHead.id.client, () => getState(store2, stackHead.id.client));
        const offset = localClock - stackHead.id.clock;
        if (offset < 0) {
          stack.push(stackHead);
          updateMissingSv(stackHead.id.client, stackHead.id.clock - 1);
          addStackToRestSS();
        } else {
          const missing = stackHead.getMissing(transaction, store2);
          if (missing !== null) {
            stack.push(stackHead);
            const structRefs = clientsStructRefs.get(
              /** @type {number} */
              missing
            ) || { refs: [], i: 0 };
            if (structRefs.refs.length === structRefs.i) {
              updateMissingSv(
                /** @type {number} */
                missing,
                getState(store2, missing)
              );
              addStackToRestSS();
            } else {
              stackHead = structRefs.refs[structRefs.i++];
              continue;
            }
          } else if (offset === 0 || offset < stackHead.length) {
            stackHead.integrate(transaction, offset);
            state.set(stackHead.id.client, stackHead.id.clock + stackHead.length);
          }
        }
      }
      if (stack.length > 0) {
        stackHead = /** @type {GC|Item} */
        stack.pop();
      } else if (curStructsTarget !== null && curStructsTarget.i < curStructsTarget.refs.length) {
        stackHead = /** @type {GC|Item} */
        curStructsTarget.refs[curStructsTarget.i++];
      } else {
        curStructsTarget = getNextStructTarget();
        if (curStructsTarget === null) {
          break;
        } else {
          stackHead = /** @type {GC|Item} */
          curStructsTarget.refs[curStructsTarget.i++];
        }
      }
    }
    if (restStructs.clients.size > 0) {
      const encoder = new UpdateEncoderV2();
      writeClientsStructs(encoder, restStructs, /* @__PURE__ */ new Map());
      writeVarUint(encoder.restEncoder, 0);
      return { missing: missingSV, update: encoder.toUint8Array() };
    }
    return null;
  };
  var writeStructsFromTransaction = (encoder, transaction) => writeClientsStructs(encoder, transaction.doc.store, transaction.beforeState);
  var readUpdateV2 = (decoder, ydoc, transactionOrigin, structDecoder = new UpdateDecoderV2(decoder)) => transact(ydoc, (transaction) => {
    transaction.local = false;
    let retry = false;
    const doc2 = transaction.doc;
    const store2 = doc2.store;
    const ss = readClientsStructRefs(structDecoder, doc2);
    const restStructs = integrateStructs(transaction, store2, ss);
    const pending = store2.pendingStructs;
    if (pending) {
      for (const [client, clock] of pending.missing) {
        if (clock < getState(store2, client)) {
          retry = true;
          break;
        }
      }
      if (restStructs) {
        for (const [client, clock] of restStructs.missing) {
          const mclock = pending.missing.get(client);
          if (mclock == null || mclock > clock) {
            pending.missing.set(client, clock);
          }
        }
        pending.update = mergeUpdatesV2([pending.update, restStructs.update]);
      }
    } else {
      store2.pendingStructs = restStructs;
    }
    const dsRest = readAndApplyDeleteSet(structDecoder, transaction, store2);
    if (store2.pendingDs) {
      const pendingDSUpdate = new UpdateDecoderV2(createDecoder(store2.pendingDs));
      readVarUint(pendingDSUpdate.restDecoder);
      const dsRest2 = readAndApplyDeleteSet(pendingDSUpdate, transaction, store2);
      if (dsRest && dsRest2) {
        store2.pendingDs = mergeUpdatesV2([dsRest, dsRest2]);
      } else {
        store2.pendingDs = dsRest || dsRest2;
      }
    } else {
      store2.pendingDs = dsRest;
    }
    if (retry) {
      const update = (
        /** @type {{update: Uint8Array}} */
        store2.pendingStructs.update
      );
      store2.pendingStructs = null;
      applyUpdateV2(transaction.doc, update);
    }
  }, transactionOrigin, false);
  var applyUpdateV2 = (ydoc, update, transactionOrigin, YDecoder = UpdateDecoderV2) => {
    const decoder = createDecoder(update);
    readUpdateV2(decoder, ydoc, transactionOrigin, new YDecoder(decoder));
  };
  var writeStateAsUpdate = (encoder, doc2, targetStateVector = /* @__PURE__ */ new Map()) => {
    writeClientsStructs(encoder, doc2.store, targetStateVector);
    writeDeleteSet(encoder, createDeleteSetFromStructStore(doc2.store));
  };
  var encodeStateAsUpdateV2 = (doc2, encodedTargetStateVector = new Uint8Array([0]), encoder = new UpdateEncoderV2()) => {
    const targetStateVector = decodeStateVector(encodedTargetStateVector);
    writeStateAsUpdate(encoder, doc2, targetStateVector);
    const updates = [encoder.toUint8Array()];
    if (doc2.store.pendingDs) {
      updates.push(doc2.store.pendingDs);
    }
    if (doc2.store.pendingStructs) {
      updates.push(diffUpdateV2(doc2.store.pendingStructs.update, encodedTargetStateVector));
    }
    if (updates.length > 1) {
      if (encoder.constructor === UpdateEncoderV1) {
        return mergeUpdates(updates.map((update, i) => i === 0 ? update : convertUpdateFormatV2ToV1(update)));
      } else if (encoder.constructor === UpdateEncoderV2) {
        return mergeUpdatesV2(updates);
      }
    }
    return updates[0];
  };
  var readStateVector = (decoder) => {
    const ss = /* @__PURE__ */ new Map();
    const ssLength = readVarUint(decoder.restDecoder);
    for (let i = 0; i < ssLength; i++) {
      const client = readVarUint(decoder.restDecoder);
      const clock = readVarUint(decoder.restDecoder);
      ss.set(client, clock);
    }
    return ss;
  };
  var decodeStateVector = (decodedState) => readStateVector(new DSDecoderV1(createDecoder(decodedState)));
  var EventHandler = class {
    constructor() {
      this.l = [];
    }
  };
  var createEventHandler = () => new EventHandler();
  var addEventHandlerListener = (eventHandler, f) => eventHandler.l.push(f);
  var removeEventHandlerListener = (eventHandler, f) => {
    const l = eventHandler.l;
    const len = l.length;
    eventHandler.l = l.filter((g) => f !== g);
    if (len === eventHandler.l.length) {
      console.error("[yjs] Tried to remove event handler that doesn't exist.");
    }
  };
  var callEventHandlerListeners = (eventHandler, arg0, arg1) => callAll(eventHandler.l, [arg0, arg1]);
  var ID = class {
    /**
     * @param {number} client client id
     * @param {number} clock unique per client id, continuous number
     */
    constructor(client, clock) {
      this.client = client;
      this.clock = clock;
    }
  };
  var compareIDs = (a, b) => a === b || a !== null && b !== null && a.client === b.client && a.clock === b.clock;
  var createID = (client, clock) => new ID(client, clock);
  var findRootTypeKey = (type) => {
    for (const [key, value] of type.doc.share.entries()) {
      if (value === type) {
        return key;
      }
    }
    throw unexpectedCase();
  };
  var isParentOf = (parent, child) => {
    while (child !== null) {
      if (child.parent === parent) {
        return true;
      }
      child = /** @type {AbstractType<any>} */
      child.parent._item;
    }
    return false;
  };
  var Snapshot = class {
    /**
     * @param {DeleteSet} ds
     * @param {Map<number,number>} sv state map
     */
    constructor(ds, sv) {
      this.ds = ds;
      this.sv = sv;
    }
  };
  var createSnapshot = (ds, sm) => new Snapshot(ds, sm);
  var emptySnapshot = createSnapshot(createDeleteSet(), /* @__PURE__ */ new Map());
  var isVisible = (item, snapshot) => snapshot === void 0 ? !item.deleted : snapshot.sv.has(item.id.client) && (snapshot.sv.get(item.id.client) || 0) > item.id.clock && !isDeleted(snapshot.ds, item.id);
  var splitSnapshotAffectedStructs = (transaction, snapshot) => {
    const meta = setIfUndefined(transaction.meta, splitSnapshotAffectedStructs, create2);
    const store2 = transaction.doc.store;
    if (!meta.has(snapshot)) {
      snapshot.sv.forEach((clock, client) => {
        if (clock < getState(store2, client)) {
          getItemCleanStart(transaction, createID(client, clock));
        }
      });
      iterateDeletedStructs(transaction, snapshot.ds, (_item) => {
      });
      meta.add(snapshot);
    }
  };
  var StructStore = class {
    constructor() {
      this.clients = /* @__PURE__ */ new Map();
      this.pendingStructs = null;
      this.pendingDs = null;
    }
  };
  var getStateVector = (store2) => {
    const sm = /* @__PURE__ */ new Map();
    store2.clients.forEach((structs, client) => {
      const struct = structs[structs.length - 1];
      sm.set(client, struct.id.clock + struct.length);
    });
    return sm;
  };
  var getState = (store2, client) => {
    const structs = store2.clients.get(client);
    if (structs === void 0) {
      return 0;
    }
    const lastStruct = structs[structs.length - 1];
    return lastStruct.id.clock + lastStruct.length;
  };
  var addStruct = (store2, struct) => {
    let structs = store2.clients.get(struct.id.client);
    if (structs === void 0) {
      structs = [];
      store2.clients.set(struct.id.client, structs);
    } else {
      const lastStruct = structs[structs.length - 1];
      if (lastStruct.id.clock + lastStruct.length !== struct.id.clock) {
        throw unexpectedCase();
      }
    }
    structs.push(struct);
  };
  var findIndexSS = (structs, clock) => {
    let left = 0;
    let right = structs.length - 1;
    let mid = structs[right];
    let midclock = mid.id.clock;
    if (midclock === clock) {
      return right;
    }
    let midindex = floor(clock / (midclock + mid.length - 1) * right);
    while (left <= right) {
      mid = structs[midindex];
      midclock = mid.id.clock;
      if (midclock <= clock) {
        if (clock < midclock + mid.length) {
          return midindex;
        }
        left = midindex + 1;
      } else {
        right = midindex - 1;
      }
      midindex = floor((left + right) / 2);
    }
    throw unexpectedCase();
  };
  var find = (store2, id2) => {
    const structs = store2.clients.get(id2.client);
    return structs[findIndexSS(structs, id2.clock)];
  };
  var getItem = (
    /** @type {function(StructStore,ID):Item} */
    find
  );
  var findIndexCleanStart = (transaction, structs, clock) => {
    const index = findIndexSS(structs, clock);
    const struct = structs[index];
    if (struct.id.clock < clock && struct instanceof Item) {
      structs.splice(index + 1, 0, splitItem(transaction, struct, clock - struct.id.clock));
      return index + 1;
    }
    return index;
  };
  var getItemCleanStart = (transaction, id2) => {
    const structs = (
      /** @type {Array<Item>} */
      transaction.doc.store.clients.get(id2.client)
    );
    return structs[findIndexCleanStart(transaction, structs, id2.clock)];
  };
  var getItemCleanEnd = (transaction, store2, id2) => {
    const structs = store2.clients.get(id2.client);
    const index = findIndexSS(structs, id2.clock);
    const struct = structs[index];
    if (id2.clock !== struct.id.clock + struct.length - 1 && struct.constructor !== GC) {
      structs.splice(index + 1, 0, splitItem(transaction, struct, id2.clock - struct.id.clock + 1));
    }
    return struct;
  };
  var replaceStruct = (store2, struct, newStruct) => {
    const structs = (
      /** @type {Array<GC|Item>} */
      store2.clients.get(struct.id.client)
    );
    structs[findIndexSS(structs, struct.id.clock)] = newStruct;
  };
  var iterateStructs = (transaction, structs, clockStart, len, f) => {
    if (len === 0) {
      return;
    }
    const clockEnd = clockStart + len;
    let index = findIndexCleanStart(transaction, structs, clockStart);
    let struct;
    do {
      struct = structs[index++];
      if (clockEnd < struct.id.clock + struct.length) {
        findIndexCleanStart(transaction, structs, clockEnd);
      }
      f(struct);
    } while (index < structs.length && structs[index].id.clock < clockEnd);
  };
  var Transaction = class {
    /**
     * @param {Doc} doc
     * @param {any} origin
     * @param {boolean} local
     */
    constructor(doc2, origin2, local) {
      this.doc = doc2;
      this.deleteSet = new DeleteSet();
      this.beforeState = getStateVector(doc2.store);
      this.afterState = /* @__PURE__ */ new Map();
      this.changed = /* @__PURE__ */ new Map();
      this.changedParentTypes = /* @__PURE__ */ new Map();
      this._mergeStructs = [];
      this.origin = origin2;
      this.meta = /* @__PURE__ */ new Map();
      this.local = local;
      this.subdocsAdded = /* @__PURE__ */ new Set();
      this.subdocsRemoved = /* @__PURE__ */ new Set();
      this.subdocsLoaded = /* @__PURE__ */ new Set();
      this._needFormattingCleanup = false;
    }
  };
  var writeUpdateMessageFromTransaction = (encoder, transaction) => {
    if (transaction.deleteSet.clients.size === 0 && !any(transaction.afterState, (clock, client) => transaction.beforeState.get(client) !== clock)) {
      return false;
    }
    sortAndMergeDeleteSet(transaction.deleteSet);
    writeStructsFromTransaction(encoder, transaction);
    writeDeleteSet(encoder, transaction.deleteSet);
    return true;
  };
  var addChangedTypeToTransaction = (transaction, type, parentSub) => {
    const item = type._item;
    if (item === null || item.id.clock < (transaction.beforeState.get(item.id.client) || 0) && !item.deleted) {
      setIfUndefined(transaction.changed, type, create2).add(parentSub);
    }
  };
  var tryToMergeWithLefts = (structs, pos) => {
    let right = structs[pos];
    let left = structs[pos - 1];
    let i = pos;
    for (; i > 0; right = left, left = structs[--i - 1]) {
      if (left.deleted === right.deleted && left.constructor === right.constructor) {
        if (left.mergeWith(right)) {
          if (right instanceof Item && right.parentSub !== null && /** @type {AbstractType<any>} */
          right.parent._map.get(right.parentSub) === right) {
            right.parent._map.set(
              right.parentSub,
              /** @type {Item} */
              left
            );
          }
          continue;
        }
      }
      break;
    }
    const merged = pos - i;
    if (merged) {
      structs.splice(pos + 1 - merged, merged);
    }
    return merged;
  };
  var tryGcDeleteSet = (ds, store2, gcFilter) => {
    for (const [client, deleteItems] of ds.clients.entries()) {
      const structs = (
        /** @type {Array<GC|Item>} */
        store2.clients.get(client)
      );
      for (let di = deleteItems.length - 1; di >= 0; di--) {
        const deleteItem = deleteItems[di];
        const endDeleteItemClock = deleteItem.clock + deleteItem.len;
        for (let si = findIndexSS(structs, deleteItem.clock), struct = structs[si]; si < structs.length && struct.id.clock < endDeleteItemClock; struct = structs[++si]) {
          const struct2 = structs[si];
          if (deleteItem.clock + deleteItem.len <= struct2.id.clock) {
            break;
          }
          if (struct2 instanceof Item && struct2.deleted && !struct2.keep && gcFilter(struct2)) {
            struct2.gc(store2, false);
          }
        }
      }
    }
  };
  var tryMergeDeleteSet = (ds, store2) => {
    ds.clients.forEach((deleteItems, client) => {
      const structs = (
        /** @type {Array<GC|Item>} */
        store2.clients.get(client)
      );
      for (let di = deleteItems.length - 1; di >= 0; di--) {
        const deleteItem = deleteItems[di];
        const mostRightIndexToCheck = min(structs.length - 1, 1 + findIndexSS(structs, deleteItem.clock + deleteItem.len - 1));
        for (let si = mostRightIndexToCheck, struct = structs[si]; si > 0 && struct.id.clock >= deleteItem.clock; struct = structs[si]) {
          si -= 1 + tryToMergeWithLefts(structs, si);
        }
      }
    });
  };
  var cleanupTransactions = (transactionCleanups, i) => {
    if (i < transactionCleanups.length) {
      const transaction = transactionCleanups[i];
      const doc2 = transaction.doc;
      const store2 = doc2.store;
      const ds = transaction.deleteSet;
      const mergeStructs = transaction._mergeStructs;
      try {
        sortAndMergeDeleteSet(ds);
        transaction.afterState = getStateVector(transaction.doc.store);
        doc2.emit("beforeObserverCalls", [transaction, doc2]);
        const fs = [];
        transaction.changed.forEach(
          (subs, itemtype) => fs.push(() => {
            if (itemtype._item === null || !itemtype._item.deleted) {
              itemtype._callObserver(transaction, subs);
            }
          })
        );
        fs.push(() => {
          transaction.changedParentTypes.forEach((events, type) => {
            if (type._dEH.l.length > 0 && (type._item === null || !type._item.deleted)) {
              events = events.filter(
                (event) => event.target._item === null || !event.target._item.deleted
              );
              events.forEach((event) => {
                event.currentTarget = type;
                event._path = null;
              });
              events.sort((event1, event2) => event1.path.length - event2.path.length);
              fs.push(() => {
                callEventHandlerListeners(type._dEH, events, transaction);
              });
            }
          });
          fs.push(() => doc2.emit("afterTransaction", [transaction, doc2]));
          fs.push(() => {
            if (transaction._needFormattingCleanup) {
              cleanupYTextAfterTransaction(transaction);
            }
          });
        });
        callAll(fs, []);
      } finally {
        if (doc2.gc) {
          tryGcDeleteSet(ds, store2, doc2.gcFilter);
        }
        tryMergeDeleteSet(ds, store2);
        transaction.afterState.forEach((clock, client) => {
          const beforeClock = transaction.beforeState.get(client) || 0;
          if (beforeClock !== clock) {
            const structs = (
              /** @type {Array<GC|Item>} */
              store2.clients.get(client)
            );
            const firstChangePos = max(findIndexSS(structs, beforeClock), 1);
            for (let i2 = structs.length - 1; i2 >= firstChangePos; ) {
              i2 -= 1 + tryToMergeWithLefts(structs, i2);
            }
          }
        });
        for (let i2 = mergeStructs.length - 1; i2 >= 0; i2--) {
          const { client, clock } = mergeStructs[i2].id;
          const structs = (
            /** @type {Array<GC|Item>} */
            store2.clients.get(client)
          );
          const replacedStructPos = findIndexSS(structs, clock);
          if (replacedStructPos + 1 < structs.length) {
            if (tryToMergeWithLefts(structs, replacedStructPos + 1) > 1) {
              continue;
            }
          }
          if (replacedStructPos > 0) {
            tryToMergeWithLefts(structs, replacedStructPos);
          }
        }
        if (!transaction.local && transaction.afterState.get(doc2.clientID) !== transaction.beforeState.get(doc2.clientID)) {
          print(ORANGE, BOLD, "[yjs] ", UNBOLD, RED, "Changed the client-id because another client seems to be using it.");
          doc2.clientID = generateNewClientId();
        }
        doc2.emit("afterTransactionCleanup", [transaction, doc2]);
        if (doc2._observers.has("update")) {
          const encoder = new UpdateEncoderV1();
          const hasContent2 = writeUpdateMessageFromTransaction(encoder, transaction);
          if (hasContent2) {
            doc2.emit("update", [encoder.toUint8Array(), transaction.origin, doc2, transaction]);
          }
        }
        if (doc2._observers.has("updateV2")) {
          const encoder = new UpdateEncoderV2();
          const hasContent2 = writeUpdateMessageFromTransaction(encoder, transaction);
          if (hasContent2) {
            doc2.emit("updateV2", [encoder.toUint8Array(), transaction.origin, doc2, transaction]);
          }
        }
        const { subdocsAdded, subdocsLoaded, subdocsRemoved } = transaction;
        if (subdocsAdded.size > 0 || subdocsRemoved.size > 0 || subdocsLoaded.size > 0) {
          subdocsAdded.forEach((subdoc) => {
            subdoc.clientID = doc2.clientID;
            if (subdoc.collectionid == null) {
              subdoc.collectionid = doc2.collectionid;
            }
            doc2.subdocs.add(subdoc);
          });
          subdocsRemoved.forEach((subdoc) => doc2.subdocs.delete(subdoc));
          doc2.emit("subdocs", [{ loaded: subdocsLoaded, added: subdocsAdded, removed: subdocsRemoved }, doc2, transaction]);
          subdocsRemoved.forEach((subdoc) => subdoc.destroy());
        }
        if (transactionCleanups.length <= i + 1) {
          doc2._transactionCleanups = [];
          doc2.emit("afterAllTransactions", [doc2, transactionCleanups]);
        } else {
          cleanupTransactions(transactionCleanups, i + 1);
        }
      }
    }
  };
  var transact = (doc2, f, origin2 = null, local = true) => {
    const transactionCleanups = doc2._transactionCleanups;
    let initialCall = false;
    let result = null;
    if (doc2._transaction === null) {
      initialCall = true;
      doc2._transaction = new Transaction(doc2, origin2, local);
      transactionCleanups.push(doc2._transaction);
      if (transactionCleanups.length === 1) {
        doc2.emit("beforeAllTransactions", [doc2]);
      }
      doc2.emit("beforeTransaction", [doc2._transaction, doc2]);
    }
    try {
      result = f(doc2._transaction);
    } finally {
      if (initialCall) {
        const finishCleanup = doc2._transaction === transactionCleanups[0];
        doc2._transaction = null;
        if (finishCleanup) {
          cleanupTransactions(transactionCleanups, 0);
        }
      }
    }
    return result;
  };
  var StackItem = class {
    /**
     * @param {DeleteSet} deletions
     * @param {DeleteSet} insertions
     */
    constructor(deletions, insertions) {
      this.insertions = insertions;
      this.deletions = deletions;
      this.meta = /* @__PURE__ */ new Map();
    }
  };
  var clearUndoManagerStackItem = (tr, um, stackItem) => {
    iterateDeletedStructs(tr, stackItem.deletions, (item) => {
      if (item instanceof Item && um.scope.some((type) => type === tr.doc || isParentOf(
        /** @type {AbstractType<any>} */
        type,
        item
      ))) {
        keepItem(item, false);
      }
    });
  };
  var popStackItem = (undoManager2, stack, eventType) => {
    let _tr = null;
    const doc2 = undoManager2.doc;
    const scope = undoManager2.scope;
    transact(doc2, (transaction) => {
      while (stack.length > 0 && undoManager2.currStackItem === null) {
        const store2 = doc2.store;
        const stackItem = (
          /** @type {StackItem} */
          stack.pop()
        );
        const itemsToRedo = /* @__PURE__ */ new Set();
        const itemsToDelete = [];
        let performedChange = false;
        iterateDeletedStructs(transaction, stackItem.insertions, (struct) => {
          if (struct instanceof Item) {
            if (struct.redone !== null) {
              let { item, diff } = followRedone(store2, struct.id);
              if (diff > 0) {
                item = getItemCleanStart(transaction, createID(item.id.client, item.id.clock + diff));
              }
              struct = item;
            }
            if (!struct.deleted && scope.some((type) => type === transaction.doc || isParentOf(
              /** @type {AbstractType<any>} */
              type,
              /** @type {Item} */
              struct
            ))) {
              itemsToDelete.push(struct);
            }
          }
        });
        iterateDeletedStructs(transaction, stackItem.deletions, (struct) => {
          if (struct instanceof Item && scope.some((type) => type === transaction.doc || isParentOf(
            /** @type {AbstractType<any>} */
            type,
            struct
          )) && // Never redo structs in stackItem.insertions because they were created and deleted in the same capture interval.
          !isDeleted(stackItem.insertions, struct.id)) {
            itemsToRedo.add(struct);
          }
        });
        itemsToRedo.forEach((struct) => {
          performedChange = redoItem(transaction, struct, itemsToRedo, stackItem.insertions, undoManager2.ignoreRemoteMapChanges, undoManager2) !== null || performedChange;
        });
        for (let i = itemsToDelete.length - 1; i >= 0; i--) {
          const item = itemsToDelete[i];
          if (undoManager2.deleteFilter(item)) {
            item.delete(transaction);
            performedChange = true;
          }
        }
        undoManager2.currStackItem = performedChange ? stackItem : null;
      }
      transaction.changed.forEach((subProps, type) => {
        if (subProps.has(null) && type._searchMarker) {
          type._searchMarker.length = 0;
        }
      });
      _tr = transaction;
    }, undoManager2);
    const res = undoManager2.currStackItem;
    if (res != null) {
      const changedParentTypes = _tr.changedParentTypes;
      undoManager2.emit("stack-item-popped", [{ stackItem: res, type: eventType, changedParentTypes, origin: undoManager2 }, undoManager2]);
      undoManager2.currStackItem = null;
    }
    return res;
  };
  var UndoManager = class extends ObservableV2 {
    /**
     * @param {Doc|AbstractType<any>|Array<AbstractType<any>>} typeScope Limits the scope of the UndoManager. If this is set to a ydoc instance, all changes on that ydoc will be undone. If set to a specific type, only changes on that type or its children will be undone. Also accepts an array of types.
     * @param {UndoManagerOptions} options
     */
    constructor(typeScope, {
      captureTimeout = 500,
      captureTransaction = (_tr) => true,
      deleteFilter = () => true,
      trackedOrigins = /* @__PURE__ */ new Set([null]),
      ignoreRemoteMapChanges = false,
      doc: doc2 = (
        /** @type {Doc} */
        isArray(typeScope) ? typeScope[0].doc : typeScope instanceof Doc ? typeScope : typeScope.doc
      )
    } = {}) {
      super();
      this.scope = [];
      this.doc = doc2;
      this.addToScope(typeScope);
      this.deleteFilter = deleteFilter;
      trackedOrigins.add(this);
      this.trackedOrigins = trackedOrigins;
      this.captureTransaction = captureTransaction;
      this.undoStack = [];
      this.redoStack = [];
      this.undoing = false;
      this.redoing = false;
      this.currStackItem = null;
      this.lastChange = 0;
      this.ignoreRemoteMapChanges = ignoreRemoteMapChanges;
      this.captureTimeout = captureTimeout;
      this.afterTransactionHandler = (transaction) => {
        if (!this.captureTransaction(transaction) || !this.scope.some((type) => transaction.changedParentTypes.has(
          /** @type {AbstractType<any>} */
          type
        ) || type === this.doc) || !this.trackedOrigins.has(transaction.origin) && (!transaction.origin || !this.trackedOrigins.has(transaction.origin.constructor))) {
          return;
        }
        const undoing = this.undoing;
        const redoing = this.redoing;
        const stack = undoing ? this.redoStack : this.undoStack;
        if (undoing) {
          this.stopCapturing();
        } else if (!redoing) {
          this.clear(false, true);
        }
        const insertions = new DeleteSet();
        transaction.afterState.forEach((endClock, client) => {
          const startClock = transaction.beforeState.get(client) || 0;
          const len = endClock - startClock;
          if (len > 0) {
            addToDeleteSet(insertions, client, startClock, len);
          }
        });
        const now = getUnixTime();
        let didAdd = false;
        if (this.lastChange > 0 && now - this.lastChange < this.captureTimeout && stack.length > 0 && !undoing && !redoing) {
          const lastOp = stack[stack.length - 1];
          lastOp.deletions = mergeDeleteSets([lastOp.deletions, transaction.deleteSet]);
          lastOp.insertions = mergeDeleteSets([lastOp.insertions, insertions]);
        } else {
          stack.push(new StackItem(transaction.deleteSet, insertions));
          didAdd = true;
        }
        if (!undoing && !redoing) {
          this.lastChange = now;
        }
        iterateDeletedStructs(
          transaction,
          transaction.deleteSet,
          /** @param {Item|GC} item */
          (item) => {
            if (item instanceof Item && this.scope.some((type) => type === transaction.doc || isParentOf(
              /** @type {AbstractType<any>} */
              type,
              item
            ))) {
              keepItem(item, true);
            }
          }
        );
        const changeEvent = [{ stackItem: stack[stack.length - 1], origin: transaction.origin, type: undoing ? "redo" : "undo", changedParentTypes: transaction.changedParentTypes }, this];
        if (didAdd) {
          this.emit("stack-item-added", changeEvent);
        } else {
          this.emit("stack-item-updated", changeEvent);
        }
      };
      this.doc.on("afterTransaction", this.afterTransactionHandler);
      this.doc.on("destroy", () => {
        this.destroy();
      });
    }
    /**
     * Extend the scope.
     *
     * @param {Array<AbstractType<any> | Doc> | AbstractType<any> | Doc} ytypes
     */
    addToScope(ytypes) {
      const tmpSet = new Set(this.scope);
      ytypes = isArray(ytypes) ? ytypes : [ytypes];
      ytypes.forEach((ytype) => {
        if (!tmpSet.has(ytype)) {
          tmpSet.add(ytype);
          if (ytype instanceof AbstractType ? ytype.doc !== this.doc : ytype !== this.doc) warn("[yjs#509] Not same Y.Doc");
          this.scope.push(ytype);
        }
      });
    }
    /**
     * @param {any} origin
     */
    addTrackedOrigin(origin2) {
      this.trackedOrigins.add(origin2);
    }
    /**
     * @param {any} origin
     */
    removeTrackedOrigin(origin2) {
      this.trackedOrigins.delete(origin2);
    }
    clear(clearUndoStack = true, clearRedoStack = true) {
      if (clearUndoStack && this.canUndo() || clearRedoStack && this.canRedo()) {
        this.doc.transact((tr) => {
          if (clearUndoStack) {
            this.undoStack.forEach((item) => clearUndoManagerStackItem(tr, this, item));
            this.undoStack = [];
          }
          if (clearRedoStack) {
            this.redoStack.forEach((item) => clearUndoManagerStackItem(tr, this, item));
            this.redoStack = [];
          }
          this.emit("stack-cleared", [{ undoStackCleared: clearUndoStack, redoStackCleared: clearRedoStack }]);
        });
      }
    }
    /**
     * UndoManager merges Undo-StackItem if they are created within time-gap
     * smaller than `options.captureTimeout`. Call `um.stopCapturing()` so that the next
     * StackItem won't be merged.
     *
     *
     * @example
     *     // without stopCapturing
     *     ytext.insert(0, 'a')
     *     ytext.insert(1, 'b')
     *     um.undo()
     *     ytext.toString() // => '' (note that 'ab' was removed)
     *     // with stopCapturing
     *     ytext.insert(0, 'a')
     *     um.stopCapturing()
     *     ytext.insert(0, 'b')
     *     um.undo()
     *     ytext.toString() // => 'a' (note that only 'b' was removed)
     *
     */
    stopCapturing() {
      this.lastChange = 0;
    }
    /**
     * Undo last changes on type.
     *
     * @return {StackItem?} Returns StackItem if a change was applied
     */
    undo() {
      this.undoing = true;
      let res;
      try {
        res = popStackItem(this, this.undoStack, "undo");
      } finally {
        this.undoing = false;
      }
      return res;
    }
    /**
     * Redo last undo operation.
     *
     * @return {StackItem?} Returns StackItem if a change was applied
     */
    redo() {
      this.redoing = true;
      let res;
      try {
        res = popStackItem(this, this.redoStack, "redo");
      } finally {
        this.redoing = false;
      }
      return res;
    }
    /**
     * Are undo steps available?
     *
     * @return {boolean} `true` if undo is possible
     */
    canUndo() {
      return this.undoStack.length > 0;
    }
    /**
     * Are redo steps available?
     *
     * @return {boolean} `true` if redo is possible
     */
    canRedo() {
      return this.redoStack.length > 0;
    }
    destroy() {
      this.trackedOrigins.delete(this);
      this.doc.off("afterTransaction", this.afterTransactionHandler);
      super.destroy();
    }
  };
  function* lazyStructReaderGenerator(decoder) {
    const numOfStateUpdates = readVarUint(decoder.restDecoder);
    for (let i = 0; i < numOfStateUpdates; i++) {
      const numberOfStructs = readVarUint(decoder.restDecoder);
      const client = decoder.readClient();
      let clock = readVarUint(decoder.restDecoder);
      for (let i2 = 0; i2 < numberOfStructs; i2++) {
        const info = decoder.readInfo();
        if (info === 10) {
          const len = readVarUint(decoder.restDecoder);
          yield new Skip(createID(client, clock), len);
          clock += len;
        } else if ((BITS5 & info) !== 0) {
          const cantCopyParentInfo = (info & (BIT7 | BIT8)) === 0;
          const struct = new Item(
            createID(client, clock),
            null,
            // left
            (info & BIT8) === BIT8 ? decoder.readLeftID() : null,
            // origin
            null,
            // right
            (info & BIT7) === BIT7 ? decoder.readRightID() : null,
            // right origin
            // @ts-ignore Force writing a string here.
            cantCopyParentInfo ? decoder.readParentInfo() ? decoder.readString() : decoder.readLeftID() : null,
            // parent
            cantCopyParentInfo && (info & BIT6) === BIT6 ? decoder.readString() : null,
            // parentSub
            readItemContent(decoder, info)
            // item content
          );
          yield struct;
          clock += struct.length;
        } else {
          const len = decoder.readLen();
          yield new GC(createID(client, clock), len);
          clock += len;
        }
      }
    }
  }
  var LazyStructReader = class {
    /**
     * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
     * @param {boolean} filterSkips
     */
    constructor(decoder, filterSkips) {
      this.gen = lazyStructReaderGenerator(decoder);
      this.curr = null;
      this.done = false;
      this.filterSkips = filterSkips;
      this.next();
    }
    /**
     * @return {Item | GC | Skip |null}
     */
    next() {
      do {
        this.curr = this.gen.next().value || null;
      } while (this.filterSkips && this.curr !== null && this.curr.constructor === Skip);
      return this.curr;
    }
  };
  var LazyStructWriter = class {
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     */
    constructor(encoder) {
      this.currClient = 0;
      this.startClock = 0;
      this.written = 0;
      this.encoder = encoder;
      this.clientStructs = [];
    }
  };
  var mergeUpdates = (updates) => mergeUpdatesV2(updates, UpdateDecoderV1, UpdateEncoderV1);
  var sliceStruct = (left, diff) => {
    if (left.constructor === GC) {
      const { client, clock } = left.id;
      return new GC(createID(client, clock + diff), left.length - diff);
    } else if (left.constructor === Skip) {
      const { client, clock } = left.id;
      return new Skip(createID(client, clock + diff), left.length - diff);
    } else {
      const leftItem = (
        /** @type {Item} */
        left
      );
      const { client, clock } = leftItem.id;
      return new Item(
        createID(client, clock + diff),
        null,
        createID(client, clock + diff - 1),
        null,
        leftItem.rightOrigin,
        leftItem.parent,
        leftItem.parentSub,
        leftItem.content.splice(diff)
      );
    }
  };
  var mergeUpdatesV2 = (updates, YDecoder = UpdateDecoderV2, YEncoder = UpdateEncoderV2) => {
    if (updates.length === 1) {
      return updates[0];
    }
    const updateDecoders = updates.map((update) => new YDecoder(createDecoder(update)));
    let lazyStructDecoders = updateDecoders.map((decoder) => new LazyStructReader(decoder, true));
    let currWrite = null;
    const updateEncoder = new YEncoder();
    const lazyStructEncoder = new LazyStructWriter(updateEncoder);
    while (true) {
      lazyStructDecoders = lazyStructDecoders.filter((dec) => dec.curr !== null);
      lazyStructDecoders.sort(
        /** @type {function(any,any):number} */
        (dec1, dec2) => {
          if (dec1.curr.id.client === dec2.curr.id.client) {
            const clockDiff = dec1.curr.id.clock - dec2.curr.id.clock;
            if (clockDiff === 0) {
              return dec1.curr.constructor === dec2.curr.constructor ? 0 : dec1.curr.constructor === Skip ? 1 : -1;
            } else {
              return clockDiff;
            }
          } else {
            return dec2.curr.id.client - dec1.curr.id.client;
          }
        }
      );
      if (lazyStructDecoders.length === 0) {
        break;
      }
      const currDecoder = lazyStructDecoders[0];
      const firstClient = (
        /** @type {Item | GC} */
        currDecoder.curr.id.client
      );
      if (currWrite !== null) {
        let curr = (
          /** @type {Item | GC | null} */
          currDecoder.curr
        );
        let iterated = false;
        while (curr !== null && curr.id.clock + curr.length <= currWrite.struct.id.clock + currWrite.struct.length && curr.id.client >= currWrite.struct.id.client) {
          curr = currDecoder.next();
          iterated = true;
        }
        if (curr === null || // current decoder is empty
        curr.id.client !== firstClient || // check whether there is another decoder that has has updates from `firstClient`
        iterated && curr.id.clock > currWrite.struct.id.clock + currWrite.struct.length) {
          continue;
        }
        if (firstClient !== currWrite.struct.id.client) {
          writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
          currWrite = { struct: curr, offset: 0 };
          currDecoder.next();
        } else {
          if (currWrite.struct.id.clock + currWrite.struct.length < curr.id.clock) {
            if (currWrite.struct.constructor === Skip) {
              currWrite.struct.length = curr.id.clock + curr.length - currWrite.struct.id.clock;
            } else {
              writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
              const diff = curr.id.clock - currWrite.struct.id.clock - currWrite.struct.length;
              const struct = new Skip(createID(firstClient, currWrite.struct.id.clock + currWrite.struct.length), diff);
              currWrite = { struct, offset: 0 };
            }
          } else {
            const diff = currWrite.struct.id.clock + currWrite.struct.length - curr.id.clock;
            if (diff > 0) {
              if (currWrite.struct.constructor === Skip) {
                currWrite.struct.length -= diff;
              } else {
                curr = sliceStruct(curr, diff);
              }
            }
            if (!currWrite.struct.mergeWith(
              /** @type {any} */
              curr
            )) {
              writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
              currWrite = { struct: curr, offset: 0 };
              currDecoder.next();
            }
          }
        }
      } else {
        currWrite = { struct: (
          /** @type {Item | GC} */
          currDecoder.curr
        ), offset: 0 };
        currDecoder.next();
      }
      for (let next = currDecoder.curr; next !== null && next.id.client === firstClient && next.id.clock === currWrite.struct.id.clock + currWrite.struct.length && next.constructor !== Skip; next = currDecoder.next()) {
        writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
        currWrite = { struct: next, offset: 0 };
      }
    }
    if (currWrite !== null) {
      writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
      currWrite = null;
    }
    finishLazyStructWriting(lazyStructEncoder);
    const dss = updateDecoders.map((decoder) => readDeleteSet(decoder));
    const ds = mergeDeleteSets(dss);
    writeDeleteSet(updateEncoder, ds);
    return updateEncoder.toUint8Array();
  };
  var diffUpdateV2 = (update, sv, YDecoder = UpdateDecoderV2, YEncoder = UpdateEncoderV2) => {
    const state = decodeStateVector(sv);
    const encoder = new YEncoder();
    const lazyStructWriter = new LazyStructWriter(encoder);
    const decoder = new YDecoder(createDecoder(update));
    const reader = new LazyStructReader(decoder, false);
    while (reader.curr) {
      const curr = reader.curr;
      const currClient = curr.id.client;
      const svClock = state.get(currClient) || 0;
      if (reader.curr.constructor === Skip) {
        reader.next();
        continue;
      }
      if (curr.id.clock + curr.length > svClock) {
        writeStructToLazyStructWriter(lazyStructWriter, curr, max(svClock - curr.id.clock, 0));
        reader.next();
        while (reader.curr && reader.curr.id.client === currClient) {
          writeStructToLazyStructWriter(lazyStructWriter, reader.curr, 0);
          reader.next();
        }
      } else {
        while (reader.curr && reader.curr.id.client === currClient && reader.curr.id.clock + reader.curr.length <= svClock) {
          reader.next();
        }
      }
    }
    finishLazyStructWriting(lazyStructWriter);
    const ds = readDeleteSet(decoder);
    writeDeleteSet(encoder, ds);
    return encoder.toUint8Array();
  };
  var flushLazyStructWriter = (lazyWriter) => {
    if (lazyWriter.written > 0) {
      lazyWriter.clientStructs.push({ written: lazyWriter.written, restEncoder: toUint8Array(lazyWriter.encoder.restEncoder) });
      lazyWriter.encoder.restEncoder = createEncoder();
      lazyWriter.written = 0;
    }
  };
  var writeStructToLazyStructWriter = (lazyWriter, struct, offset) => {
    if (lazyWriter.written > 0 && lazyWriter.currClient !== struct.id.client) {
      flushLazyStructWriter(lazyWriter);
    }
    if (lazyWriter.written === 0) {
      lazyWriter.currClient = struct.id.client;
      lazyWriter.encoder.writeClient(struct.id.client);
      writeVarUint(lazyWriter.encoder.restEncoder, struct.id.clock + offset);
    }
    struct.write(lazyWriter.encoder, offset);
    lazyWriter.written++;
  };
  var finishLazyStructWriting = (lazyWriter) => {
    flushLazyStructWriter(lazyWriter);
    const restEncoder = lazyWriter.encoder.restEncoder;
    writeVarUint(restEncoder, lazyWriter.clientStructs.length);
    for (let i = 0; i < lazyWriter.clientStructs.length; i++) {
      const partStructs = lazyWriter.clientStructs[i];
      writeVarUint(restEncoder, partStructs.written);
      writeUint8Array(restEncoder, partStructs.restEncoder);
    }
  };
  var convertUpdateFormat = (update, blockTransformer, YDecoder, YEncoder) => {
    const updateDecoder = new YDecoder(createDecoder(update));
    const lazyDecoder = new LazyStructReader(updateDecoder, false);
    const updateEncoder = new YEncoder();
    const lazyWriter = new LazyStructWriter(updateEncoder);
    for (let curr = lazyDecoder.curr; curr !== null; curr = lazyDecoder.next()) {
      writeStructToLazyStructWriter(lazyWriter, blockTransformer(curr), 0);
    }
    finishLazyStructWriting(lazyWriter);
    const ds = readDeleteSet(updateDecoder);
    writeDeleteSet(updateEncoder, ds);
    return updateEncoder.toUint8Array();
  };
  var convertUpdateFormatV2ToV1 = (update) => convertUpdateFormat(update, id, UpdateDecoderV2, UpdateEncoderV1);
  var errorComputeChanges = "You must not compute changes after the event-handler fired.";
  var YEvent = class {
    /**
     * @param {T} target The changed type.
     * @param {Transaction} transaction
     */
    constructor(target, transaction) {
      this.target = target;
      this.currentTarget = target;
      this.transaction = transaction;
      this._changes = null;
      this._keys = null;
      this._delta = null;
      this._path = null;
    }
    /**
     * Computes the path from `y` to the changed type.
     *
     * @todo v14 should standardize on path: Array<{parent, index}> because that is easier to work with.
     *
     * The following property holds:
     * @example
     *   let type = y
     *   event.path.forEach(dir => {
     *     type = type.get(dir)
     *   })
     *   type === event.target // => true
     */
    get path() {
      return this._path || (this._path = getPathTo(this.currentTarget, this.target));
    }
    /**
     * Check if a struct is deleted by this event.
     *
     * In contrast to change.deleted, this method also returns true if the struct was added and then deleted.
     *
     * @param {AbstractStruct} struct
     * @return {boolean}
     */
    deletes(struct) {
      return isDeleted(this.transaction.deleteSet, struct.id);
    }
    /**
     * @type {Map<string, { action: 'add' | 'update' | 'delete', oldValue: any }>}
     */
    get keys() {
      if (this._keys === null) {
        if (this.transaction.doc._transactionCleanups.length === 0) {
          throw create3(errorComputeChanges);
        }
        const keys2 = /* @__PURE__ */ new Map();
        const target = this.target;
        const changed = (
          /** @type Set<string|null> */
          this.transaction.changed.get(target)
        );
        changed.forEach((key) => {
          if (key !== null) {
            const item = (
              /** @type {Item} */
              target._map.get(key)
            );
            let action;
            let oldValue;
            if (this.adds(item)) {
              let prev = item.left;
              while (prev !== null && this.adds(prev)) {
                prev = prev.left;
              }
              if (this.deletes(item)) {
                if (prev !== null && this.deletes(prev)) {
                  action = "delete";
                  oldValue = last(prev.content.getContent());
                } else {
                  return;
                }
              } else {
                if (prev !== null && this.deletes(prev)) {
                  action = "update";
                  oldValue = last(prev.content.getContent());
                } else {
                  action = "add";
                  oldValue = void 0;
                }
              }
            } else {
              if (this.deletes(item)) {
                action = "delete";
                oldValue = last(
                  /** @type {Item} */
                  item.content.getContent()
                );
              } else {
                return;
              }
            }
            keys2.set(key, { action, oldValue });
          }
        });
        this._keys = keys2;
      }
      return this._keys;
    }
    /**
     * This is a computed property. Note that this can only be safely computed during the
     * event call. Computing this property after other changes happened might result in
     * unexpected behavior (incorrect computation of deltas). A safe way to collect changes
     * is to store the `changes` or the `delta` object. Avoid storing the `transaction` object.
     *
     * @type {Array<{insert?: string | Array<any> | object | AbstractType<any>, retain?: number, delete?: number, attributes?: Object<string, any>}>}
     */
    get delta() {
      return this.changes.delta;
    }
    /**
     * Check if a struct is added by this event.
     *
     * In contrast to change.deleted, this method also returns true if the struct was added and then deleted.
     *
     * @param {AbstractStruct} struct
     * @return {boolean}
     */
    adds(struct) {
      return struct.id.clock >= (this.transaction.beforeState.get(struct.id.client) || 0);
    }
    /**
     * This is a computed property. Note that this can only be safely computed during the
     * event call. Computing this property after other changes happened might result in
     * unexpected behavior (incorrect computation of deltas). A safe way to collect changes
     * is to store the `changes` or the `delta` object. Avoid storing the `transaction` object.
     *
     * @type {{added:Set<Item>,deleted:Set<Item>,keys:Map<string,{action:'add'|'update'|'delete',oldValue:any}>,delta:Array<{insert?:Array<any>|string, delete?:number, retain?:number}>}}
     */
    get changes() {
      let changes = this._changes;
      if (changes === null) {
        if (this.transaction.doc._transactionCleanups.length === 0) {
          throw create3(errorComputeChanges);
        }
        const target = this.target;
        const added = create2();
        const deleted = create2();
        const delta = [];
        changes = {
          added,
          deleted,
          delta,
          keys: this.keys
        };
        const changed = (
          /** @type Set<string|null> */
          this.transaction.changed.get(target)
        );
        if (changed.has(null)) {
          let lastOp = null;
          const packOp = () => {
            if (lastOp) {
              delta.push(lastOp);
            }
          };
          for (let item = target._start; item !== null; item = item.right) {
            if (item.deleted) {
              if (this.deletes(item) && !this.adds(item)) {
                if (lastOp === null || lastOp.delete === void 0) {
                  packOp();
                  lastOp = { delete: 0 };
                }
                lastOp.delete += item.length;
                deleted.add(item);
              }
            } else {
              if (this.adds(item)) {
                if (lastOp === null || lastOp.insert === void 0) {
                  packOp();
                  lastOp = { insert: [] };
                }
                lastOp.insert = lastOp.insert.concat(item.content.getContent());
                added.add(item);
              } else {
                if (lastOp === null || lastOp.retain === void 0) {
                  packOp();
                  lastOp = { retain: 0 };
                }
                lastOp.retain += item.length;
              }
            }
          }
          if (lastOp !== null && lastOp.retain === void 0) {
            packOp();
          }
        }
        this._changes = changes;
      }
      return (
        /** @type {any} */
        changes
      );
    }
  };
  var getPathTo = (parent, child) => {
    const path = [];
    while (child._item !== null && child !== parent) {
      if (child._item.parentSub !== null) {
        path.unshift(child._item.parentSub);
      } else {
        let i = 0;
        let c = (
          /** @type {AbstractType<any>} */
          child._item.parent._start
        );
        while (c !== child._item && c !== null) {
          if (!c.deleted && c.countable) {
            i += c.length;
          }
          c = c.right;
        }
        path.unshift(i);
      }
      child = /** @type {AbstractType<any>} */
      child._item.parent;
    }
    return path;
  };
  var warnPrematureAccess = () => {
    warn("Invalid access: Add Yjs type to a document before reading data.");
  };
  var maxSearchMarker = 80;
  var globalSearchMarkerTimestamp = 0;
  var ArraySearchMarker = class {
    /**
     * @param {Item} p
     * @param {number} index
     */
    constructor(p, index) {
      p.marker = true;
      this.p = p;
      this.index = index;
      this.timestamp = globalSearchMarkerTimestamp++;
    }
  };
  var refreshMarkerTimestamp = (marker) => {
    marker.timestamp = globalSearchMarkerTimestamp++;
  };
  var overwriteMarker = (marker, p, index) => {
    marker.p.marker = false;
    marker.p = p;
    p.marker = true;
    marker.index = index;
    marker.timestamp = globalSearchMarkerTimestamp++;
  };
  var markPosition = (searchMarker, p, index) => {
    if (searchMarker.length >= maxSearchMarker) {
      const marker = searchMarker.reduce((a, b) => a.timestamp < b.timestamp ? a : b);
      overwriteMarker(marker, p, index);
      return marker;
    } else {
      const pm = new ArraySearchMarker(p, index);
      searchMarker.push(pm);
      return pm;
    }
  };
  var findMarker = (yarray, index) => {
    if (yarray._start === null || index === 0 || yarray._searchMarker === null) {
      return null;
    }
    const marker = yarray._searchMarker.length === 0 ? null : yarray._searchMarker.reduce((a, b) => abs(index - a.index) < abs(index - b.index) ? a : b);
    let p = yarray._start;
    let pindex = 0;
    if (marker !== null) {
      p = marker.p;
      pindex = marker.index;
      refreshMarkerTimestamp(marker);
    }
    while (p.right !== null && pindex < index) {
      if (!p.deleted && p.countable) {
        if (index < pindex + p.length) {
          break;
        }
        pindex += p.length;
      }
      p = p.right;
    }
    while (p.left !== null && pindex > index) {
      p = p.left;
      if (!p.deleted && p.countable) {
        pindex -= p.length;
      }
    }
    while (p.left !== null && p.left.id.client === p.id.client && p.left.id.clock + p.left.length === p.id.clock) {
      p = p.left;
      if (!p.deleted && p.countable) {
        pindex -= p.length;
      }
    }
    if (marker !== null && abs(marker.index - pindex) < /** @type {YText|YArray<any>} */
    p.parent.length / maxSearchMarker) {
      overwriteMarker(marker, p, pindex);
      return marker;
    } else {
      return markPosition(yarray._searchMarker, p, pindex);
    }
  };
  var updateMarkerChanges = (searchMarker, index, len) => {
    for (let i = searchMarker.length - 1; i >= 0; i--) {
      const m = searchMarker[i];
      if (len > 0) {
        let p = m.p;
        p.marker = false;
        while (p && (p.deleted || !p.countable)) {
          p = p.left;
          if (p && !p.deleted && p.countable) {
            m.index -= p.length;
          }
        }
        if (p === null || p.marker === true) {
          searchMarker.splice(i, 1);
          continue;
        }
        m.p = p;
        p.marker = true;
      }
      if (index < m.index || len > 0 && index === m.index) {
        m.index = max(index, m.index + len);
      }
    }
  };
  var callTypeObservers = (type, transaction, event) => {
    const changedType = type;
    const changedParentTypes = transaction.changedParentTypes;
    while (true) {
      setIfUndefined(changedParentTypes, type, () => []).push(event);
      if (type._item === null) {
        break;
      }
      type = /** @type {AbstractType<any>} */
      type._item.parent;
    }
    callEventHandlerListeners(changedType._eH, event, transaction);
  };
  var AbstractType = class {
    constructor() {
      this._item = null;
      this._map = /* @__PURE__ */ new Map();
      this._start = null;
      this.doc = null;
      this._length = 0;
      this._eH = createEventHandler();
      this._dEH = createEventHandler();
      this._searchMarker = null;
    }
    /**
     * @return {AbstractType<any>|null}
     */
    get parent() {
      return this._item ? (
        /** @type {AbstractType<any>} */
        this._item.parent
      ) : null;
    }
    /**
     * Integrate this type into the Yjs instance.
     *
     * * Save this struct in the os
     * * This type is sent to other client
     * * Observer functions are fired
     *
     * @param {Doc} y The Yjs instance
     * @param {Item|null} item
     */
    _integrate(y, item) {
      this.doc = y;
      this._item = item;
    }
    /**
     * @return {AbstractType<EventType>}
     */
    _copy() {
      throw methodUnimplemented();
    }
    /**
     * Makes a copy of this data type that can be included somewhere else.
     *
     * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
     *
     * @return {AbstractType<EventType>}
     */
    clone() {
      throw methodUnimplemented();
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} _encoder
     */
    _write(_encoder) {
    }
    /**
     * The first non-deleted item
     */
    get _first() {
      let n = this._start;
      while (n !== null && n.deleted) {
        n = n.right;
      }
      return n;
    }
    /**
     * Creates YEvent and calls all type observers.
     * Must be implemented by each type.
     *
     * @param {Transaction} transaction
     * @param {Set<null|string>} _parentSubs Keys changed on this type. `null` if list was modified.
     */
    _callObserver(transaction, _parentSubs) {
      if (!transaction.local && this._searchMarker) {
        this._searchMarker.length = 0;
      }
    }
    /**
     * Observe all events that are created on this type.
     *
     * @param {function(EventType, Transaction):void} f Observer function
     */
    observe(f) {
      addEventHandlerListener(this._eH, f);
    }
    /**
     * Observe all events that are created by this type and its children.
     *
     * @param {function(Array<YEvent<any>>,Transaction):void} f Observer function
     */
    observeDeep(f) {
      addEventHandlerListener(this._dEH, f);
    }
    /**
     * Unregister an observer function.
     *
     * @param {function(EventType,Transaction):void} f Observer function
     */
    unobserve(f) {
      removeEventHandlerListener(this._eH, f);
    }
    /**
     * Unregister an observer function.
     *
     * @param {function(Array<YEvent<any>>,Transaction):void} f Observer function
     */
    unobserveDeep(f) {
      removeEventHandlerListener(this._dEH, f);
    }
    /**
     * @abstract
     * @return {any}
     */
    toJSON() {
    }
  };
  var typeListSlice = (type, start, end) => {
    type.doc ?? warnPrematureAccess();
    if (start < 0) {
      start = type._length + start;
    }
    if (end < 0) {
      end = type._length + end;
    }
    let len = end - start;
    const cs = [];
    let n = type._start;
    while (n !== null && len > 0) {
      if (n.countable && !n.deleted) {
        const c = n.content.getContent();
        if (c.length <= start) {
          start -= c.length;
        } else {
          for (let i = start; i < c.length && len > 0; i++) {
            cs.push(c[i]);
            len--;
          }
          start = 0;
        }
      }
      n = n.right;
    }
    return cs;
  };
  var typeListToArray = (type) => {
    type.doc ?? warnPrematureAccess();
    const cs = [];
    let n = type._start;
    while (n !== null) {
      if (n.countable && !n.deleted) {
        const c = n.content.getContent();
        for (let i = 0; i < c.length; i++) {
          cs.push(c[i]);
        }
      }
      n = n.right;
    }
    return cs;
  };
  var typeListForEach = (type, f) => {
    let index = 0;
    let n = type._start;
    type.doc ?? warnPrematureAccess();
    while (n !== null) {
      if (n.countable && !n.deleted) {
        const c = n.content.getContent();
        for (let i = 0; i < c.length; i++) {
          f(c[i], index++, type);
        }
      }
      n = n.right;
    }
  };
  var typeListMap = (type, f) => {
    const result = [];
    typeListForEach(type, (c, i) => {
      result.push(f(c, i, type));
    });
    return result;
  };
  var typeListCreateIterator = (type) => {
    let n = type._start;
    let currentContent = null;
    let currentContentIndex = 0;
    return {
      [Symbol.iterator]() {
        return this;
      },
      next: () => {
        if (currentContent === null) {
          while (n !== null && n.deleted) {
            n = n.right;
          }
          if (n === null) {
            return {
              done: true,
              value: void 0
            };
          }
          currentContent = n.content.getContent();
          currentContentIndex = 0;
          n = n.right;
        }
        const value = currentContent[currentContentIndex++];
        if (currentContent.length <= currentContentIndex) {
          currentContent = null;
        }
        return {
          done: false,
          value
        };
      }
    };
  };
  var typeListGet = (type, index) => {
    type.doc ?? warnPrematureAccess();
    const marker = findMarker(type, index);
    let n = type._start;
    if (marker !== null) {
      n = marker.p;
      index -= marker.index;
    }
    for (; n !== null; n = n.right) {
      if (!n.deleted && n.countable) {
        if (index < n.length) {
          return n.content.getContent()[index];
        }
        index -= n.length;
      }
    }
  };
  var typeListInsertGenericsAfter = (transaction, parent, referenceItem, content) => {
    let left = referenceItem;
    const doc2 = transaction.doc;
    const ownClientId = doc2.clientID;
    const store2 = doc2.store;
    const right = referenceItem === null ? parent._start : referenceItem.right;
    let jsonContent = [];
    const packJsonContent = () => {
      if (jsonContent.length > 0) {
        left = new Item(createID(ownClientId, getState(store2, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentAny(jsonContent));
        left.integrate(transaction, 0);
        jsonContent = [];
      }
    };
    content.forEach((c) => {
      if (c === null) {
        jsonContent.push(c);
      } else {
        switch (c.constructor) {
          case Number:
          case Object:
          case Boolean:
          case Array:
          case String:
            jsonContent.push(c);
            break;
          default:
            packJsonContent();
            switch (c.constructor) {
              case Uint8Array:
              case ArrayBuffer:
                left = new Item(createID(ownClientId, getState(store2, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentBinary(new Uint8Array(
                  /** @type {Uint8Array} */
                  c
                )));
                left.integrate(transaction, 0);
                break;
              case Doc:
                left = new Item(createID(ownClientId, getState(store2, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentDoc(
                  /** @type {Doc} */
                  c
                ));
                left.integrate(transaction, 0);
                break;
              default:
                if (c instanceof AbstractType) {
                  left = new Item(createID(ownClientId, getState(store2, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentType(c));
                  left.integrate(transaction, 0);
                } else {
                  throw new Error("Unexpected content type in insert operation");
                }
            }
        }
      }
    });
    packJsonContent();
  };
  var lengthExceeded = () => create3("Length exceeded!");
  var typeListInsertGenerics = (transaction, parent, index, content) => {
    if (index > parent._length) {
      throw lengthExceeded();
    }
    if (index === 0) {
      if (parent._searchMarker) {
        updateMarkerChanges(parent._searchMarker, index, content.length);
      }
      return typeListInsertGenericsAfter(transaction, parent, null, content);
    }
    const startIndex = index;
    const marker = findMarker(parent, index);
    let n = parent._start;
    if (marker !== null) {
      n = marker.p;
      index -= marker.index;
      if (index === 0) {
        n = n.prev;
        index += n && n.countable && !n.deleted ? n.length : 0;
      }
    }
    for (; n !== null; n = n.right) {
      if (!n.deleted && n.countable) {
        if (index <= n.length) {
          if (index < n.length) {
            getItemCleanStart(transaction, createID(n.id.client, n.id.clock + index));
          }
          break;
        }
        index -= n.length;
      }
    }
    if (parent._searchMarker) {
      updateMarkerChanges(parent._searchMarker, startIndex, content.length);
    }
    return typeListInsertGenericsAfter(transaction, parent, n, content);
  };
  var typeListPushGenerics = (transaction, parent, content) => {
    const marker = (parent._searchMarker || []).reduce((maxMarker, currMarker) => currMarker.index > maxMarker.index ? currMarker : maxMarker, { index: 0, p: parent._start });
    let n = marker.p;
    if (n) {
      while (n.right) {
        n = n.right;
      }
    }
    return typeListInsertGenericsAfter(transaction, parent, n, content);
  };
  var typeListDelete = (transaction, parent, index, length2) => {
    if (length2 === 0) {
      return;
    }
    const startIndex = index;
    const startLength = length2;
    const marker = findMarker(parent, index);
    let n = parent._start;
    if (marker !== null) {
      n = marker.p;
      index -= marker.index;
    }
    for (; n !== null && index > 0; n = n.right) {
      if (!n.deleted && n.countable) {
        if (index < n.length) {
          getItemCleanStart(transaction, createID(n.id.client, n.id.clock + index));
        }
        index -= n.length;
      }
    }
    while (length2 > 0 && n !== null) {
      if (!n.deleted) {
        if (length2 < n.length) {
          getItemCleanStart(transaction, createID(n.id.client, n.id.clock + length2));
        }
        n.delete(transaction);
        length2 -= n.length;
      }
      n = n.right;
    }
    if (length2 > 0) {
      throw lengthExceeded();
    }
    if (parent._searchMarker) {
      updateMarkerChanges(
        parent._searchMarker,
        startIndex,
        -startLength + length2
        /* in case we remove the above exception */
      );
    }
  };
  var typeMapDelete = (transaction, parent, key) => {
    const c = parent._map.get(key);
    if (c !== void 0) {
      c.delete(transaction);
    }
  };
  var typeMapSet = (transaction, parent, key, value) => {
    const left = parent._map.get(key) || null;
    const doc2 = transaction.doc;
    const ownClientId = doc2.clientID;
    let content;
    if (value == null) {
      content = new ContentAny([value]);
    } else {
      switch (value.constructor) {
        case Number:
        case Object:
        case Boolean:
        case Array:
        case String:
        case Date:
        case BigInt:
          content = new ContentAny([value]);
          break;
        case Uint8Array:
          content = new ContentBinary(
            /** @type {Uint8Array} */
            value
          );
          break;
        case Doc:
          content = new ContentDoc(
            /** @type {Doc} */
            value
          );
          break;
        default:
          if (value instanceof AbstractType) {
            content = new ContentType(value);
          } else {
            throw new Error("Unexpected content type");
          }
      }
    }
    new Item(createID(ownClientId, getState(doc2.store, ownClientId)), left, left && left.lastId, null, null, parent, key, content).integrate(transaction, 0);
  };
  var typeMapGet = (parent, key) => {
    parent.doc ?? warnPrematureAccess();
    const val = parent._map.get(key);
    return val !== void 0 && !val.deleted ? val.content.getContent()[val.length - 1] : void 0;
  };
  var typeMapGetAll = (parent) => {
    const res = {};
    parent.doc ?? warnPrematureAccess();
    parent._map.forEach((value, key) => {
      if (!value.deleted) {
        res[key] = value.content.getContent()[value.length - 1];
      }
    });
    return res;
  };
  var typeMapHas = (parent, key) => {
    parent.doc ?? warnPrematureAccess();
    const val = parent._map.get(key);
    return val !== void 0 && !val.deleted;
  };
  var typeMapGetAllSnapshot = (parent, snapshot) => {
    const res = {};
    parent._map.forEach((value, key) => {
      let v = value;
      while (v !== null && (!snapshot.sv.has(v.id.client) || v.id.clock >= (snapshot.sv.get(v.id.client) || 0))) {
        v = v.left;
      }
      if (v !== null && isVisible(v, snapshot)) {
        res[key] = v.content.getContent()[v.length - 1];
      }
    });
    return res;
  };
  var createMapIterator = (type) => {
    type.doc ?? warnPrematureAccess();
    return iteratorFilter(
      type._map.entries(),
      /** @param {any} entry */
      (entry) => !entry[1].deleted
    );
  };
  var YArrayEvent = class extends YEvent {
  };
  var YArray = class _YArray extends AbstractType {
    constructor() {
      super();
      this._prelimContent = [];
      this._searchMarker = [];
    }
    /**
     * Construct a new YArray containing the specified items.
     * @template {Object<string,any>|Array<any>|number|null|string|Uint8Array} T
     * @param {Array<T>} items
     * @return {YArray<T>}
     */
    static from(items2) {
      const a = new _YArray();
      a.push(items2);
      return a;
    }
    /**
     * Integrate this type into the Yjs instance.
     *
     * * Save this struct in the os
     * * This type is sent to other client
     * * Observer functions are fired
     *
     * @param {Doc} y The Yjs instance
     * @param {Item} item
     */
    _integrate(y, item) {
      super._integrate(y, item);
      this.insert(
        0,
        /** @type {Array<any>} */
        this._prelimContent
      );
      this._prelimContent = null;
    }
    /**
     * @return {YArray<T>}
     */
    _copy() {
      return new _YArray();
    }
    /**
     * Makes a copy of this data type that can be included somewhere else.
     *
     * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
     *
     * @return {YArray<T>}
     */
    clone() {
      const arr = new _YArray();
      arr.insert(0, this.toArray().map(
        (el) => el instanceof AbstractType ? (
          /** @type {typeof el} */
          el.clone()
        ) : el
      ));
      return arr;
    }
    get length() {
      this.doc ?? warnPrematureAccess();
      return this._length;
    }
    /**
     * Creates YArrayEvent and calls observers.
     *
     * @param {Transaction} transaction
     * @param {Set<null|string>} parentSubs Keys changed on this type. `null` if list was modified.
     */
    _callObserver(transaction, parentSubs) {
      super._callObserver(transaction, parentSubs);
      callTypeObservers(this, transaction, new YArrayEvent(this, transaction));
    }
    /**
     * Inserts new content at an index.
     *
     * Important: This function expects an array of content. Not just a content
     * object. The reason for this "weirdness" is that inserting several elements
     * is very efficient when it is done as a single operation.
     *
     * @example
     *  // Insert character 'a' at position 0
     *  yarray.insert(0, ['a'])
     *  // Insert numbers 1, 2 at position 1
     *  yarray.insert(1, [1, 2])
     *
     * @param {number} index The index to insert content at.
     * @param {Array<T>} content The array of content
     */
    insert(index, content) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeListInsertGenerics(
            transaction,
            this,
            index,
            /** @type {any} */
            content
          );
        });
      } else {
        this._prelimContent.splice(index, 0, ...content);
      }
    }
    /**
     * Appends content to this YArray.
     *
     * @param {Array<T>} content Array of content to append.
     *
     * @todo Use the following implementation in all types.
     */
    push(content) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeListPushGenerics(
            transaction,
            this,
            /** @type {any} */
            content
          );
        });
      } else {
        this._prelimContent.push(...content);
      }
    }
    /**
     * Prepends content to this YArray.
     *
     * @param {Array<T>} content Array of content to prepend.
     */
    unshift(content) {
      this.insert(0, content);
    }
    /**
     * Deletes elements starting from an index.
     *
     * @param {number} index Index at which to start deleting elements
     * @param {number} length The number of elements to remove. Defaults to 1.
     */
    delete(index, length2 = 1) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeListDelete(transaction, this, index, length2);
        });
      } else {
        this._prelimContent.splice(index, length2);
      }
    }
    /**
     * Returns the i-th element from a YArray.
     *
     * @param {number} index The index of the element to return from the YArray
     * @return {T}
     */
    get(index) {
      return typeListGet(this, index);
    }
    /**
     * Transforms this YArray to a JavaScript Array.
     *
     * @return {Array<T>}
     */
    toArray() {
      return typeListToArray(this);
    }
    /**
     * Returns a portion of this YArray into a JavaScript Array selected
     * from start to end (end not included).
     *
     * @param {number} [start]
     * @param {number} [end]
     * @return {Array<T>}
     */
    slice(start = 0, end = this.length) {
      return typeListSlice(this, start, end);
    }
    /**
     * Transforms this Shared Type to a JSON object.
     *
     * @return {Array<any>}
     */
    toJSON() {
      return this.map((c) => c instanceof AbstractType ? c.toJSON() : c);
    }
    /**
     * Returns an Array with the result of calling a provided function on every
     * element of this YArray.
     *
     * @template M
     * @param {function(T,number,YArray<T>):M} f Function that produces an element of the new Array
     * @return {Array<M>} A new array with each element being the result of the
     *                 callback function
     */
    map(f) {
      return typeListMap(
        this,
        /** @type {any} */
        f
      );
    }
    /**
     * Executes a provided function once on every element of this YArray.
     *
     * @param {function(T,number,YArray<T>):void} f A function to execute on every element of this YArray.
     */
    forEach(f) {
      typeListForEach(this, f);
    }
    /**
     * @return {IterableIterator<T>}
     */
    [Symbol.iterator]() {
      return typeListCreateIterator(this);
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     */
    _write(encoder) {
      encoder.writeTypeRef(YArrayRefID);
    }
  };
  var readYArray = (_decoder) => new YArray();
  var YMapEvent = class extends YEvent {
    /**
     * @param {YMap<T>} ymap The YArray that changed.
     * @param {Transaction} transaction
     * @param {Set<any>} subs The keys that changed.
     */
    constructor(ymap, transaction, subs) {
      super(ymap, transaction);
      this.keysChanged = subs;
    }
  };
  var YMap = class _YMap extends AbstractType {
    /**
     *
     * @param {Iterable<readonly [string, any]>=} entries - an optional iterable to initialize the YMap
     */
    constructor(entries) {
      super();
      this._prelimContent = null;
      if (entries === void 0) {
        this._prelimContent = /* @__PURE__ */ new Map();
      } else {
        this._prelimContent = new Map(entries);
      }
    }
    /**
     * Integrate this type into the Yjs instance.
     *
     * * Save this struct in the os
     * * This type is sent to other client
     * * Observer functions are fired
     *
     * @param {Doc} y The Yjs instance
     * @param {Item} item
     */
    _integrate(y, item) {
      super._integrate(y, item);
      this._prelimContent.forEach((value, key) => {
        this.set(key, value);
      });
      this._prelimContent = null;
    }
    /**
     * @return {YMap<MapType>}
     */
    _copy() {
      return new _YMap();
    }
    /**
     * Makes a copy of this data type that can be included somewhere else.
     *
     * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
     *
     * @return {YMap<MapType>}
     */
    clone() {
      const map2 = new _YMap();
      this.forEach((value, key) => {
        map2.set(key, value instanceof AbstractType ? (
          /** @type {typeof value} */
          value.clone()
        ) : value);
      });
      return map2;
    }
    /**
     * Creates YMapEvent and calls observers.
     *
     * @param {Transaction} transaction
     * @param {Set<null|string>} parentSubs Keys changed on this type. `null` if list was modified.
     */
    _callObserver(transaction, parentSubs) {
      callTypeObservers(this, transaction, new YMapEvent(this, transaction, parentSubs));
    }
    /**
     * Transforms this Shared Type to a JSON object.
     *
     * @return {Object<string,any>}
     */
    toJSON() {
      this.doc ?? warnPrematureAccess();
      const map2 = {};
      this._map.forEach((item, key) => {
        if (!item.deleted) {
          const v = item.content.getContent()[item.length - 1];
          map2[key] = v instanceof AbstractType ? v.toJSON() : v;
        }
      });
      return map2;
    }
    /**
     * Returns the size of the YMap (count of key/value pairs)
     *
     * @return {number}
     */
    get size() {
      return [...createMapIterator(this)].length;
    }
    /**
     * Returns the keys for each element in the YMap Type.
     *
     * @return {IterableIterator<string>}
     */
    keys() {
      return iteratorMap(
        createMapIterator(this),
        /** @param {any} v */
        (v) => v[0]
      );
    }
    /**
     * Returns the values for each element in the YMap Type.
     *
     * @return {IterableIterator<MapType>}
     */
    values() {
      return iteratorMap(
        createMapIterator(this),
        /** @param {any} v */
        (v) => v[1].content.getContent()[v[1].length - 1]
      );
    }
    /**
     * Returns an Iterator of [key, value] pairs
     *
     * @return {IterableIterator<[string, MapType]>}
     */
    entries() {
      return iteratorMap(
        createMapIterator(this),
        /** @param {any} v */
        (v) => (
          /** @type {any} */
          [v[0], v[1].content.getContent()[v[1].length - 1]]
        )
      );
    }
    /**
     * Executes a provided function on once on every key-value pair.
     *
     * @param {function(MapType,string,YMap<MapType>):void} f A function to execute on every element of this YArray.
     */
    forEach(f) {
      this.doc ?? warnPrematureAccess();
      this._map.forEach((item, key) => {
        if (!item.deleted) {
          f(item.content.getContent()[item.length - 1], key, this);
        }
      });
    }
    /**
     * Returns an Iterator of [key, value] pairs
     *
     * @return {IterableIterator<[string, MapType]>}
     */
    [Symbol.iterator]() {
      return this.entries();
    }
    /**
     * Remove a specified element from this YMap.
     *
     * @param {string} key The key of the element to remove.
     */
    delete(key) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeMapDelete(transaction, this, key);
        });
      } else {
        this._prelimContent.delete(key);
      }
    }
    /**
     * Adds or updates an element with a specified key and value.
     * @template {MapType} VAL
     *
     * @param {string} key The key of the element to add to this YMap
     * @param {VAL} value The value of the element to add
     * @return {VAL}
     */
    set(key, value) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeMapSet(
            transaction,
            this,
            key,
            /** @type {any} */
            value
          );
        });
      } else {
        this._prelimContent.set(key, value);
      }
      return value;
    }
    /**
     * Returns a specified element from this YMap.
     *
     * @param {string} key
     * @return {MapType|undefined}
     */
    get(key) {
      return (
        /** @type {any} */
        typeMapGet(this, key)
      );
    }
    /**
     * Returns a boolean indicating whether the specified key exists or not.
     *
     * @param {string} key The key to test.
     * @return {boolean}
     */
    has(key) {
      return typeMapHas(this, key);
    }
    /**
     * Removes all elements from this YMap.
     */
    clear() {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          this.forEach(function(_value, key, map2) {
            typeMapDelete(transaction, map2, key);
          });
        });
      } else {
        this._prelimContent.clear();
      }
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     */
    _write(encoder) {
      encoder.writeTypeRef(YMapRefID);
    }
  };
  var readYMap = (_decoder) => new YMap();
  var equalAttrs = (a, b) => a === b || typeof a === "object" && typeof b === "object" && a && b && equalFlat(a, b);
  var ItemTextListPosition = class {
    /**
     * @param {Item|null} left
     * @param {Item|null} right
     * @param {number} index
     * @param {Map<string,any>} currentAttributes
     */
    constructor(left, right, index, currentAttributes) {
      this.left = left;
      this.right = right;
      this.index = index;
      this.currentAttributes = currentAttributes;
    }
    /**
     * Only call this if you know that this.right is defined
     */
    forward() {
      if (this.right === null) {
        unexpectedCase();
      }
      switch (this.right.content.constructor) {
        case ContentFormat:
          if (!this.right.deleted) {
            updateCurrentAttributes(
              this.currentAttributes,
              /** @type {ContentFormat} */
              this.right.content
            );
          }
          break;
        default:
          if (!this.right.deleted) {
            this.index += this.right.length;
          }
          break;
      }
      this.left = this.right;
      this.right = this.right.right;
    }
  };
  var findNextPosition = (transaction, pos, count) => {
    while (pos.right !== null && count > 0) {
      switch (pos.right.content.constructor) {
        case ContentFormat:
          if (!pos.right.deleted) {
            updateCurrentAttributes(
              pos.currentAttributes,
              /** @type {ContentFormat} */
              pos.right.content
            );
          }
          break;
        default:
          if (!pos.right.deleted) {
            if (count < pos.right.length) {
              getItemCleanStart(transaction, createID(pos.right.id.client, pos.right.id.clock + count));
            }
            pos.index += pos.right.length;
            count -= pos.right.length;
          }
          break;
      }
      pos.left = pos.right;
      pos.right = pos.right.right;
    }
    return pos;
  };
  var findPosition = (transaction, parent, index, useSearchMarker) => {
    const currentAttributes = /* @__PURE__ */ new Map();
    const marker = useSearchMarker ? findMarker(parent, index) : null;
    if (marker) {
      const pos = new ItemTextListPosition(marker.p.left, marker.p, marker.index, currentAttributes);
      return findNextPosition(transaction, pos, index - marker.index);
    } else {
      const pos = new ItemTextListPosition(null, parent._start, 0, currentAttributes);
      return findNextPosition(transaction, pos, index);
    }
  };
  var insertNegatedAttributes = (transaction, parent, currPos, negatedAttributes) => {
    while (currPos.right !== null && (currPos.right.deleted === true || currPos.right.content.constructor === ContentFormat && equalAttrs(
      negatedAttributes.get(
        /** @type {ContentFormat} */
        currPos.right.content.key
      ),
      /** @type {ContentFormat} */
      currPos.right.content.value
    ))) {
      if (!currPos.right.deleted) {
        negatedAttributes.delete(
          /** @type {ContentFormat} */
          currPos.right.content.key
        );
      }
      currPos.forward();
    }
    const doc2 = transaction.doc;
    const ownClientId = doc2.clientID;
    negatedAttributes.forEach((val, key) => {
      const left = currPos.left;
      const right = currPos.right;
      const nextFormat = new Item(createID(ownClientId, getState(doc2.store, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentFormat(key, val));
      nextFormat.integrate(transaction, 0);
      currPos.right = nextFormat;
      currPos.forward();
    });
  };
  var updateCurrentAttributes = (currentAttributes, format) => {
    const { key, value } = format;
    if (value === null) {
      currentAttributes.delete(key);
    } else {
      currentAttributes.set(key, value);
    }
  };
  var minimizeAttributeChanges = (currPos, attributes) => {
    while (true) {
      if (currPos.right === null) {
        break;
      } else if (currPos.right.deleted || currPos.right.content.constructor === ContentFormat && equalAttrs(
        attributes[
          /** @type {ContentFormat} */
          currPos.right.content.key
        ] ?? null,
        /** @type {ContentFormat} */
        currPos.right.content.value
      )) ;
      else {
        break;
      }
      currPos.forward();
    }
  };
  var insertAttributes = (transaction, parent, currPos, attributes) => {
    const doc2 = transaction.doc;
    const ownClientId = doc2.clientID;
    const negatedAttributes = /* @__PURE__ */ new Map();
    for (const key in attributes) {
      const val = attributes[key];
      const currentVal = currPos.currentAttributes.get(key) ?? null;
      if (!equalAttrs(currentVal, val)) {
        negatedAttributes.set(key, currentVal);
        const { left, right } = currPos;
        currPos.right = new Item(createID(ownClientId, getState(doc2.store, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentFormat(key, val));
        currPos.right.integrate(transaction, 0);
        currPos.forward();
      }
    }
    return negatedAttributes;
  };
  var insertText = (transaction, parent, currPos, text2, attributes) => {
    currPos.currentAttributes.forEach((_val, key) => {
      if (attributes[key] === void 0) {
        attributes[key] = null;
      }
    });
    const doc2 = transaction.doc;
    const ownClientId = doc2.clientID;
    minimizeAttributeChanges(currPos, attributes);
    const negatedAttributes = insertAttributes(transaction, parent, currPos, attributes);
    const content = text2.constructor === String ? new ContentString(
      /** @type {string} */
      text2
    ) : text2 instanceof AbstractType ? new ContentType(text2) : new ContentEmbed(text2);
    let { left, right, index } = currPos;
    if (parent._searchMarker) {
      updateMarkerChanges(parent._searchMarker, currPos.index, content.getLength());
    }
    right = new Item(createID(ownClientId, getState(doc2.store, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, content);
    right.integrate(transaction, 0);
    currPos.right = right;
    currPos.index = index;
    currPos.forward();
    insertNegatedAttributes(transaction, parent, currPos, negatedAttributes);
  };
  var formatText = (transaction, parent, currPos, length2, attributes) => {
    const doc2 = transaction.doc;
    const ownClientId = doc2.clientID;
    minimizeAttributeChanges(currPos, attributes);
    const negatedAttributes = insertAttributes(transaction, parent, currPos, attributes);
    iterationLoop: while (currPos.right !== null && (length2 > 0 || negatedAttributes.size > 0 && (currPos.right.deleted || currPos.right.content.constructor === ContentFormat))) {
      if (!currPos.right.deleted) {
        switch (currPos.right.content.constructor) {
          case ContentFormat: {
            const { key, value } = (
              /** @type {ContentFormat} */
              currPos.right.content
            );
            const attr = attributes[key];
            if (attr !== void 0) {
              if (equalAttrs(attr, value)) {
                negatedAttributes.delete(key);
              } else {
                if (length2 === 0) {
                  break iterationLoop;
                }
                negatedAttributes.set(key, value);
              }
              currPos.right.delete(transaction);
            } else {
              currPos.currentAttributes.set(key, value);
            }
            break;
          }
          default:
            if (length2 < currPos.right.length) {
              getItemCleanStart(transaction, createID(currPos.right.id.client, currPos.right.id.clock + length2));
            }
            length2 -= currPos.right.length;
            break;
        }
      }
      currPos.forward();
    }
    if (length2 > 0) {
      let newlines = "";
      for (; length2 > 0; length2--) {
        newlines += "\n";
      }
      currPos.right = new Item(createID(ownClientId, getState(doc2.store, ownClientId)), currPos.left, currPos.left && currPos.left.lastId, currPos.right, currPos.right && currPos.right.id, parent, null, new ContentString(newlines));
      currPos.right.integrate(transaction, 0);
      currPos.forward();
    }
    insertNegatedAttributes(transaction, parent, currPos, negatedAttributes);
  };
  var cleanupFormattingGap = (transaction, start, curr, startAttributes, currAttributes) => {
    let end = start;
    const endFormats = create();
    while (end && (!end.countable || end.deleted)) {
      if (!end.deleted && end.content.constructor === ContentFormat) {
        const cf = (
          /** @type {ContentFormat} */
          end.content
        );
        endFormats.set(cf.key, cf);
      }
      end = end.right;
    }
    let cleanups = 0;
    let reachedCurr = false;
    while (start !== end) {
      if (curr === start) {
        reachedCurr = true;
      }
      if (!start.deleted) {
        const content = start.content;
        switch (content.constructor) {
          case ContentFormat: {
            const { key, value } = (
              /** @type {ContentFormat} */
              content
            );
            const startAttrValue = startAttributes.get(key) ?? null;
            if (endFormats.get(key) !== content || startAttrValue === value) {
              start.delete(transaction);
              cleanups++;
              if (!reachedCurr && (currAttributes.get(key) ?? null) === value && startAttrValue !== value) {
                if (startAttrValue === null) {
                  currAttributes.delete(key);
                } else {
                  currAttributes.set(key, startAttrValue);
                }
              }
            }
            if (!reachedCurr && !start.deleted) {
              updateCurrentAttributes(
                currAttributes,
                /** @type {ContentFormat} */
                content
              );
            }
            break;
          }
        }
      }
      start = /** @type {Item} */
      start.right;
    }
    return cleanups;
  };
  var cleanupContextlessFormattingGap = (transaction, item) => {
    while (item && item.right && (item.right.deleted || !item.right.countable)) {
      item = item.right;
    }
    const attrs = /* @__PURE__ */ new Set();
    while (item && (item.deleted || !item.countable)) {
      if (!item.deleted && item.content.constructor === ContentFormat) {
        const key = (
          /** @type {ContentFormat} */
          item.content.key
        );
        if (attrs.has(key)) {
          item.delete(transaction);
        } else {
          attrs.add(key);
        }
      }
      item = item.left;
    }
  };
  var cleanupYTextFormatting = (type) => {
    let res = 0;
    transact(
      /** @type {Doc} */
      type.doc,
      (transaction) => {
        let start = (
          /** @type {Item} */
          type._start
        );
        let end = type._start;
        let startAttributes = create();
        const currentAttributes = copy(startAttributes);
        while (end) {
          if (end.deleted === false) {
            switch (end.content.constructor) {
              case ContentFormat:
                updateCurrentAttributes(
                  currentAttributes,
                  /** @type {ContentFormat} */
                  end.content
                );
                break;
              default:
                res += cleanupFormattingGap(transaction, start, end, startAttributes, currentAttributes);
                startAttributes = copy(currentAttributes);
                start = end;
                break;
            }
          }
          end = end.right;
        }
      }
    );
    return res;
  };
  var cleanupYTextAfterTransaction = (transaction) => {
    const needFullCleanup = /* @__PURE__ */ new Set();
    const doc2 = transaction.doc;
    for (const [client, afterClock] of transaction.afterState.entries()) {
      const clock = transaction.beforeState.get(client) || 0;
      if (afterClock === clock) {
        continue;
      }
      iterateStructs(
        transaction,
        /** @type {Array<Item|GC>} */
        doc2.store.clients.get(client),
        clock,
        afterClock,
        (item) => {
          if (!item.deleted && /** @type {Item} */
          item.content.constructor === ContentFormat && item.constructor !== GC) {
            needFullCleanup.add(
              /** @type {any} */
              item.parent
            );
          }
        }
      );
    }
    transact(doc2, (t) => {
      iterateDeletedStructs(transaction, transaction.deleteSet, (item) => {
        if (item instanceof GC || !/** @type {YText} */
        item.parent._hasFormatting || needFullCleanup.has(
          /** @type {YText} */
          item.parent
        )) {
          return;
        }
        const parent = (
          /** @type {YText} */
          item.parent
        );
        if (item.content.constructor === ContentFormat) {
          needFullCleanup.add(parent);
        } else {
          cleanupContextlessFormattingGap(t, item);
        }
      });
      for (const yText of needFullCleanup) {
        cleanupYTextFormatting(yText);
      }
    });
  };
  var deleteText = (transaction, currPos, length2) => {
    const startLength = length2;
    const startAttrs = copy(currPos.currentAttributes);
    const start = currPos.right;
    while (length2 > 0 && currPos.right !== null) {
      if (currPos.right.deleted === false) {
        switch (currPos.right.content.constructor) {
          case ContentType:
          case ContentEmbed:
          case ContentString:
            if (length2 < currPos.right.length) {
              getItemCleanStart(transaction, createID(currPos.right.id.client, currPos.right.id.clock + length2));
            }
            length2 -= currPos.right.length;
            currPos.right.delete(transaction);
            break;
        }
      }
      currPos.forward();
    }
    if (start) {
      cleanupFormattingGap(transaction, start, currPos.right, startAttrs, currPos.currentAttributes);
    }
    const parent = (
      /** @type {AbstractType<any>} */
      /** @type {Item} */
      (currPos.left || currPos.right).parent
    );
    if (parent._searchMarker) {
      updateMarkerChanges(parent._searchMarker, currPos.index, -startLength + length2);
    }
    return currPos;
  };
  var YTextEvent = class extends YEvent {
    /**
     * @param {YText} ytext
     * @param {Transaction} transaction
     * @param {Set<any>} subs The keys that changed
     */
    constructor(ytext, transaction, subs) {
      super(ytext, transaction);
      this.childListChanged = false;
      this.keysChanged = /* @__PURE__ */ new Set();
      subs.forEach((sub) => {
        if (sub === null) {
          this.childListChanged = true;
        } else {
          this.keysChanged.add(sub);
        }
      });
    }
    /**
     * @type {{added:Set<Item>,deleted:Set<Item>,keys:Map<string,{action:'add'|'update'|'delete',oldValue:any}>,delta:Array<{insert?:Array<any>|string, delete?:number, retain?:number}>}}
     */
    get changes() {
      if (this._changes === null) {
        const changes = {
          keys: this.keys,
          delta: this.delta,
          added: /* @__PURE__ */ new Set(),
          deleted: /* @__PURE__ */ new Set()
        };
        this._changes = changes;
      }
      return (
        /** @type {any} */
        this._changes
      );
    }
    /**
     * Compute the changes in the delta format.
     * A {@link https://quilljs.com/docs/delta/|Quill Delta}) that represents the changes on the document.
     *
     * @type {Array<{insert?:string|object|AbstractType<any>, delete?:number, retain?:number, attributes?: Object<string,any>}>}
     *
     * @public
     */
    get delta() {
      if (this._delta === null) {
        const y = (
          /** @type {Doc} */
          this.target.doc
        );
        const delta = [];
        transact(y, (transaction) => {
          const currentAttributes = /* @__PURE__ */ new Map();
          const oldAttributes = /* @__PURE__ */ new Map();
          let item = this.target._start;
          let action = null;
          const attributes = {};
          let insert = "";
          let retain = 0;
          let deleteLen = 0;
          const addOp = () => {
            if (action !== null) {
              let op = null;
              switch (action) {
                case "delete":
                  if (deleteLen > 0) {
                    op = { delete: deleteLen };
                  }
                  deleteLen = 0;
                  break;
                case "insert":
                  if (typeof insert === "object" || insert.length > 0) {
                    op = { insert };
                    if (currentAttributes.size > 0) {
                      op.attributes = {};
                      currentAttributes.forEach((value, key) => {
                        if (value !== null) {
                          op.attributes[key] = value;
                        }
                      });
                    }
                  }
                  insert = "";
                  break;
                case "retain":
                  if (retain > 0) {
                    op = { retain };
                    if (!isEmpty(attributes)) {
                      op.attributes = assign({}, attributes);
                    }
                  }
                  retain = 0;
                  break;
              }
              if (op) delta.push(op);
              action = null;
            }
          };
          while (item !== null) {
            switch (item.content.constructor) {
              case ContentType:
              case ContentEmbed:
                if (this.adds(item)) {
                  if (!this.deletes(item)) {
                    addOp();
                    action = "insert";
                    insert = item.content.getContent()[0];
                    addOp();
                  }
                } else if (this.deletes(item)) {
                  if (action !== "delete") {
                    addOp();
                    action = "delete";
                  }
                  deleteLen += 1;
                } else if (!item.deleted) {
                  if (action !== "retain") {
                    addOp();
                    action = "retain";
                  }
                  retain += 1;
                }
                break;
              case ContentString:
                if (this.adds(item)) {
                  if (!this.deletes(item)) {
                    if (action !== "insert") {
                      addOp();
                      action = "insert";
                    }
                    insert += /** @type {ContentString} */
                    item.content.str;
                  }
                } else if (this.deletes(item)) {
                  if (action !== "delete") {
                    addOp();
                    action = "delete";
                  }
                  deleteLen += item.length;
                } else if (!item.deleted) {
                  if (action !== "retain") {
                    addOp();
                    action = "retain";
                  }
                  retain += item.length;
                }
                break;
              case ContentFormat: {
                const { key, value } = (
                  /** @type {ContentFormat} */
                  item.content
                );
                if (this.adds(item)) {
                  if (!this.deletes(item)) {
                    const curVal = currentAttributes.get(key) ?? null;
                    if (!equalAttrs(curVal, value)) {
                      if (action === "retain") {
                        addOp();
                      }
                      if (equalAttrs(value, oldAttributes.get(key) ?? null)) {
                        delete attributes[key];
                      } else {
                        attributes[key] = value;
                      }
                    } else if (value !== null) {
                      item.delete(transaction);
                    }
                  }
                } else if (this.deletes(item)) {
                  oldAttributes.set(key, value);
                  const curVal = currentAttributes.get(key) ?? null;
                  if (!equalAttrs(curVal, value)) {
                    if (action === "retain") {
                      addOp();
                    }
                    attributes[key] = curVal;
                  }
                } else if (!item.deleted) {
                  oldAttributes.set(key, value);
                  const attr = attributes[key];
                  if (attr !== void 0) {
                    if (!equalAttrs(attr, value)) {
                      if (action === "retain") {
                        addOp();
                      }
                      if (value === null) {
                        delete attributes[key];
                      } else {
                        attributes[key] = value;
                      }
                    } else if (attr !== null) {
                      item.delete(transaction);
                    }
                  }
                }
                if (!item.deleted) {
                  if (action === "insert") {
                    addOp();
                  }
                  updateCurrentAttributes(
                    currentAttributes,
                    /** @type {ContentFormat} */
                    item.content
                  );
                }
                break;
              }
            }
            item = item.right;
          }
          addOp();
          while (delta.length > 0) {
            const lastOp = delta[delta.length - 1];
            if (lastOp.retain !== void 0 && lastOp.attributes === void 0) {
              delta.pop();
            } else {
              break;
            }
          }
        });
        this._delta = delta;
      }
      return (
        /** @type {any} */
        this._delta
      );
    }
  };
  var YText = class _YText extends AbstractType {
    /**
     * @param {String} [string] The initial value of the YText.
     */
    constructor(string) {
      super();
      this._pending = string !== void 0 ? [() => this.insert(0, string)] : [];
      this._searchMarker = [];
      this._hasFormatting = false;
    }
    /**
     * Number of characters of this text type.
     *
     * @type {number}
     */
    get length() {
      this.doc ?? warnPrematureAccess();
      return this._length;
    }
    /**
     * @param {Doc} y
     * @param {Item} item
     */
    _integrate(y, item) {
      super._integrate(y, item);
      try {
        this._pending.forEach((f) => f());
      } catch (e) {
        console.error(e);
      }
      this._pending = null;
    }
    _copy() {
      return new _YText();
    }
    /**
     * Makes a copy of this data type that can be included somewhere else.
     *
     * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
     *
     * @return {YText}
     */
    clone() {
      const text2 = new _YText();
      text2.applyDelta(this.toDelta());
      return text2;
    }
    /**
     * Creates YTextEvent and calls observers.
     *
     * @param {Transaction} transaction
     * @param {Set<null|string>} parentSubs Keys changed on this type. `null` if list was modified.
     */
    _callObserver(transaction, parentSubs) {
      super._callObserver(transaction, parentSubs);
      const event = new YTextEvent(this, transaction, parentSubs);
      callTypeObservers(this, transaction, event);
      if (!transaction.local && this._hasFormatting) {
        transaction._needFormattingCleanup = true;
      }
    }
    /**
     * Returns the unformatted string representation of this YText type.
     *
     * @public
     */
    toString() {
      this.doc ?? warnPrematureAccess();
      let str = "";
      let n = this._start;
      while (n !== null) {
        if (!n.deleted && n.countable && n.content.constructor === ContentString) {
          str += /** @type {ContentString} */
          n.content.str;
        }
        n = n.right;
      }
      return str;
    }
    /**
     * Returns the unformatted string representation of this YText type.
     *
     * @return {string}
     * @public
     */
    toJSON() {
      return this.toString();
    }
    /**
     * Apply a {@link Delta} on this shared YText type.
     *
     * @param {Array<any>} delta The changes to apply on this element.
     * @param {object}  opts
     * @param {boolean} [opts.sanitize] Sanitize input delta. Removes ending newlines if set to true.
     *
     *
     * @public
     */
    applyDelta(delta, { sanitize = true } = {}) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          const currPos = new ItemTextListPosition(null, this._start, 0, /* @__PURE__ */ new Map());
          for (let i = 0; i < delta.length; i++) {
            const op = delta[i];
            if (op.insert !== void 0) {
              const ins = !sanitize && typeof op.insert === "string" && i === delta.length - 1 && currPos.right === null && op.insert.slice(-1) === "\n" ? op.insert.slice(0, -1) : op.insert;
              if (typeof ins !== "string" || ins.length > 0) {
                insertText(transaction, this, currPos, ins, op.attributes || {});
              }
            } else if (op.retain !== void 0) {
              formatText(transaction, this, currPos, op.retain, op.attributes || {});
            } else if (op.delete !== void 0) {
              deleteText(transaction, currPos, op.delete);
            }
          }
        });
      } else {
        this._pending.push(() => this.applyDelta(delta));
      }
    }
    /**
     * Returns the Delta representation of this YText type.
     *
     * @param {Snapshot} [snapshot]
     * @param {Snapshot} [prevSnapshot]
     * @param {function('removed' | 'added', ID):any} [computeYChange]
     * @return {any} The Delta representation of this type.
     *
     * @public
     */
    toDelta(snapshot, prevSnapshot, computeYChange) {
      this.doc ?? warnPrematureAccess();
      const ops = [];
      const currentAttributes = /* @__PURE__ */ new Map();
      const doc2 = (
        /** @type {Doc} */
        this.doc
      );
      let str = "";
      let n = this._start;
      function packStr() {
        if (str.length > 0) {
          const attributes = {};
          let addAttributes = false;
          currentAttributes.forEach((value, key) => {
            addAttributes = true;
            attributes[key] = value;
          });
          const op = { insert: str };
          if (addAttributes) {
            op.attributes = attributes;
          }
          ops.push(op);
          str = "";
        }
      }
      const computeDelta = () => {
        while (n !== null) {
          if (isVisible(n, snapshot) || prevSnapshot !== void 0 && isVisible(n, prevSnapshot)) {
            switch (n.content.constructor) {
              case ContentString: {
                const cur = currentAttributes.get("ychange");
                if (snapshot !== void 0 && !isVisible(n, snapshot)) {
                  if (cur === void 0 || cur.user !== n.id.client || cur.type !== "removed") {
                    packStr();
                    currentAttributes.set("ychange", computeYChange ? computeYChange("removed", n.id) : { type: "removed" });
                  }
                } else if (prevSnapshot !== void 0 && !isVisible(n, prevSnapshot)) {
                  if (cur === void 0 || cur.user !== n.id.client || cur.type !== "added") {
                    packStr();
                    currentAttributes.set("ychange", computeYChange ? computeYChange("added", n.id) : { type: "added" });
                  }
                } else if (cur !== void 0) {
                  packStr();
                  currentAttributes.delete("ychange");
                }
                str += /** @type {ContentString} */
                n.content.str;
                break;
              }
              case ContentType:
              case ContentEmbed: {
                packStr();
                const op = {
                  insert: n.content.getContent()[0]
                };
                if (currentAttributes.size > 0) {
                  const attrs = (
                    /** @type {Object<string,any>} */
                    {}
                  );
                  op.attributes = attrs;
                  currentAttributes.forEach((value, key) => {
                    attrs[key] = value;
                  });
                }
                ops.push(op);
                break;
              }
              case ContentFormat:
                if (isVisible(n, snapshot)) {
                  packStr();
                  updateCurrentAttributes(
                    currentAttributes,
                    /** @type {ContentFormat} */
                    n.content
                  );
                }
                break;
            }
          }
          n = n.right;
        }
        packStr();
      };
      if (snapshot || prevSnapshot) {
        transact(doc2, (transaction) => {
          if (snapshot) {
            splitSnapshotAffectedStructs(transaction, snapshot);
          }
          if (prevSnapshot) {
            splitSnapshotAffectedStructs(transaction, prevSnapshot);
          }
          computeDelta();
        }, "cleanup");
      } else {
        computeDelta();
      }
      return ops;
    }
    /**
     * Insert text at a given index.
     *
     * @param {number} index The index at which to start inserting.
     * @param {String} text The text to insert at the specified position.
     * @param {TextAttributes} [attributes] Optionally define some formatting
     *                                    information to apply on the inserted
     *                                    Text.
     * @public
     */
    insert(index, text2, attributes) {
      if (text2.length <= 0) {
        return;
      }
      const y = this.doc;
      if (y !== null) {
        transact(y, (transaction) => {
          const pos = findPosition(transaction, this, index, !attributes);
          if (!attributes) {
            attributes = {};
            pos.currentAttributes.forEach((v, k) => {
              attributes[k] = v;
            });
          }
          insertText(transaction, this, pos, text2, attributes);
        });
      } else {
        this._pending.push(() => this.insert(index, text2, attributes));
      }
    }
    /**
     * Inserts an embed at a index.
     *
     * @param {number} index The index to insert the embed at.
     * @param {Object | AbstractType<any>} embed The Object that represents the embed.
     * @param {TextAttributes} [attributes] Attribute information to apply on the
     *                                    embed
     *
     * @public
     */
    insertEmbed(index, embed, attributes) {
      const y = this.doc;
      if (y !== null) {
        transact(y, (transaction) => {
          const pos = findPosition(transaction, this, index, !attributes);
          insertText(transaction, this, pos, embed, attributes || {});
        });
      } else {
        this._pending.push(() => this.insertEmbed(index, embed, attributes || {}));
      }
    }
    /**
     * Deletes text starting from an index.
     *
     * @param {number} index Index at which to start deleting.
     * @param {number} length The number of characters to remove. Defaults to 1.
     *
     * @public
     */
    delete(index, length2) {
      if (length2 === 0) {
        return;
      }
      const y = this.doc;
      if (y !== null) {
        transact(y, (transaction) => {
          deleteText(transaction, findPosition(transaction, this, index, true), length2);
        });
      } else {
        this._pending.push(() => this.delete(index, length2));
      }
    }
    /**
     * Assigns properties to a range of text.
     *
     * @param {number} index The position where to start formatting.
     * @param {number} length The amount of characters to assign properties to.
     * @param {TextAttributes} attributes Attribute information to apply on the
     *                                    text.
     *
     * @public
     */
    format(index, length2, attributes) {
      if (length2 === 0) {
        return;
      }
      const y = this.doc;
      if (y !== null) {
        transact(y, (transaction) => {
          const pos = findPosition(transaction, this, index, false);
          if (pos.right === null) {
            return;
          }
          formatText(transaction, this, pos, length2, attributes);
        });
      } else {
        this._pending.push(() => this.format(index, length2, attributes));
      }
    }
    /**
     * Removes an attribute.
     *
     * @note Xml-Text nodes don't have attributes. You can use this feature to assign properties to complete text-blocks.
     *
     * @param {String} attributeName The attribute name that is to be removed.
     *
     * @public
     */
    removeAttribute(attributeName) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeMapDelete(transaction, this, attributeName);
        });
      } else {
        this._pending.push(() => this.removeAttribute(attributeName));
      }
    }
    /**
     * Sets or updates an attribute.
     *
     * @note Xml-Text nodes don't have attributes. You can use this feature to assign properties to complete text-blocks.
     *
     * @param {String} attributeName The attribute name that is to be set.
     * @param {any} attributeValue The attribute value that is to be set.
     *
     * @public
     */
    setAttribute(attributeName, attributeValue) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeMapSet(transaction, this, attributeName, attributeValue);
        });
      } else {
        this._pending.push(() => this.setAttribute(attributeName, attributeValue));
      }
    }
    /**
     * Returns an attribute value that belongs to the attribute name.
     *
     * @note Xml-Text nodes don't have attributes. You can use this feature to assign properties to complete text-blocks.
     *
     * @param {String} attributeName The attribute name that identifies the
     *                               queried value.
     * @return {any} The queried attribute value.
     *
     * @public
     */
    getAttribute(attributeName) {
      return (
        /** @type {any} */
        typeMapGet(this, attributeName)
      );
    }
    /**
     * Returns all attribute name/value pairs in a JSON Object.
     *
     * @note Xml-Text nodes don't have attributes. You can use this feature to assign properties to complete text-blocks.
     *
     * @return {Object<string, any>} A JSON Object that describes the attributes.
     *
     * @public
     */
    getAttributes() {
      return typeMapGetAll(this);
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     */
    _write(encoder) {
      encoder.writeTypeRef(YTextRefID);
    }
  };
  var readYText = (_decoder) => new YText();
  var YXmlTreeWalker = class {
    /**
     * @param {YXmlFragment | YXmlElement} root
     * @param {function(AbstractType<any>):boolean} [f]
     */
    constructor(root, f = () => true) {
      this._filter = f;
      this._root = root;
      this._currentNode = /** @type {Item} */
      root._start;
      this._firstCall = true;
      root.doc ?? warnPrematureAccess();
    }
    [Symbol.iterator]() {
      return this;
    }
    /**
     * Get the next node.
     *
     * @return {IteratorResult<YXmlElement|YXmlText|YXmlHook>} The next node.
     *
     * @public
     */
    next() {
      let n = this._currentNode;
      let type = n && n.content && /** @type {any} */
      n.content.type;
      if (n !== null && (!this._firstCall || n.deleted || !this._filter(type))) {
        do {
          type = /** @type {any} */
          n.content.type;
          if (!n.deleted && (type.constructor === YXmlElement || type.constructor === YXmlFragment) && type._start !== null) {
            n = type._start;
          } else {
            while (n !== null) {
              const nxt = n.next;
              if (nxt !== null) {
                n = nxt;
                break;
              } else if (n.parent === this._root) {
                n = null;
              } else {
                n = /** @type {AbstractType<any>} */
                n.parent._item;
              }
            }
          }
        } while (n !== null && (n.deleted || !this._filter(
          /** @type {ContentType} */
          n.content.type
        )));
      }
      this._firstCall = false;
      if (n === null) {
        return { value: void 0, done: true };
      }
      this._currentNode = n;
      return { value: (
        /** @type {any} */
        n.content.type
      ), done: false };
    }
  };
  var YXmlFragment = class _YXmlFragment extends AbstractType {
    constructor() {
      super();
      this._prelimContent = [];
    }
    /**
     * @type {YXmlElement|YXmlText|null}
     */
    get firstChild() {
      const first = this._first;
      return first ? first.content.getContent()[0] : null;
    }
    /**
     * Integrate this type into the Yjs instance.
     *
     * * Save this struct in the os
     * * This type is sent to other client
     * * Observer functions are fired
     *
     * @param {Doc} y The Yjs instance
     * @param {Item} item
     */
    _integrate(y, item) {
      super._integrate(y, item);
      this.insert(
        0,
        /** @type {Array<any>} */
        this._prelimContent
      );
      this._prelimContent = null;
    }
    _copy() {
      return new _YXmlFragment();
    }
    /**
     * Makes a copy of this data type that can be included somewhere else.
     *
     * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
     *
     * @return {YXmlFragment}
     */
    clone() {
      const el = new _YXmlFragment();
      el.insert(0, this.toArray().map((item) => item instanceof AbstractType ? item.clone() : item));
      return el;
    }
    get length() {
      this.doc ?? warnPrematureAccess();
      return this._prelimContent === null ? this._length : this._prelimContent.length;
    }
    /**
     * Create a subtree of childNodes.
     *
     * @example
     * const walker = elem.createTreeWalker(dom => dom.nodeName === 'div')
     * for (let node in walker) {
     *   // `node` is a div node
     *   nop(node)
     * }
     *
     * @param {function(AbstractType<any>):boolean} filter Function that is called on each child element and
     *                          returns a Boolean indicating whether the child
     *                          is to be included in the subtree.
     * @return {YXmlTreeWalker} A subtree and a position within it.
     *
     * @public
     */
    createTreeWalker(filter) {
      return new YXmlTreeWalker(this, filter);
    }
    /**
     * Returns the first YXmlElement that matches the query.
     * Similar to DOM's {@link querySelector}.
     *
     * Query support:
     *   - tagname
     * TODO:
     *   - id
     *   - attribute
     *
     * @param {CSS_Selector} query The query on the children.
     * @return {YXmlElement|YXmlText|YXmlHook|null} The first element that matches the query or null.
     *
     * @public
     */
    querySelector(query) {
      query = query.toUpperCase();
      const iterator = new YXmlTreeWalker(this, (element2) => element2.nodeName && element2.nodeName.toUpperCase() === query);
      const next = iterator.next();
      if (next.done) {
        return null;
      } else {
        return next.value;
      }
    }
    /**
     * Returns all YXmlElements that match the query.
     * Similar to Dom's {@link querySelectorAll}.
     *
     * @todo Does not yet support all queries. Currently only query by tagName.
     *
     * @param {CSS_Selector} query The query on the children
     * @return {Array<YXmlElement|YXmlText|YXmlHook|null>} The elements that match this query.
     *
     * @public
     */
    querySelectorAll(query) {
      query = query.toUpperCase();
      return from(new YXmlTreeWalker(this, (element2) => element2.nodeName && element2.nodeName.toUpperCase() === query));
    }
    /**
     * Creates YXmlEvent and calls observers.
     *
     * @param {Transaction} transaction
     * @param {Set<null|string>} parentSubs Keys changed on this type. `null` if list was modified.
     */
    _callObserver(transaction, parentSubs) {
      callTypeObservers(this, transaction, new YXmlEvent(this, parentSubs, transaction));
    }
    /**
     * Get the string representation of all the children of this YXmlFragment.
     *
     * @return {string} The string representation of all children.
     */
    toString() {
      return typeListMap(this, (xml) => xml.toString()).join("");
    }
    /**
     * @return {string}
     */
    toJSON() {
      return this.toString();
    }
    /**
     * Creates a Dom Element that mirrors this YXmlElement.
     *
     * @param {Document} [_document=document] The document object (you must define
     *                                        this when calling this method in
     *                                        nodejs)
     * @param {Object<string, any>} [hooks={}] Optional property to customize how hooks
     *                                             are presented in the DOM
     * @param {any} [binding] You should not set this property. This is
     *                               used if DomBinding wants to create a
     *                               association to the created DOM type.
     * @return {Node} The {@link https://developer.mozilla.org/en-US/docs/Web/API/Element|Dom Element}
     *
     * @public
     */
    toDOM(_document = document, hooks = {}, binding) {
      const fragment = _document.createDocumentFragment();
      if (binding !== void 0) {
        binding._createAssociation(fragment, this);
      }
      typeListForEach(this, (xmlType) => {
        fragment.insertBefore(xmlType.toDOM(_document, hooks, binding), null);
      });
      return fragment;
    }
    /**
     * Inserts new content at an index.
     *
     * @example
     *  // Insert character 'a' at position 0
     *  xml.insert(0, [new Y.XmlText('text')])
     *
     * @param {number} index The index to insert content at
     * @param {Array<YXmlElement|YXmlText>} content The array of content
     */
    insert(index, content) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeListInsertGenerics(transaction, this, index, content);
        });
      } else {
        this._prelimContent.splice(index, 0, ...content);
      }
    }
    /**
     * Inserts new content at an index.
     *
     * @example
     *  // Insert character 'a' at position 0
     *  xml.insert(0, [new Y.XmlText('text')])
     *
     * @param {null|Item|YXmlElement|YXmlText} ref The index to insert content at
     * @param {Array<YXmlElement|YXmlText>} content The array of content
     */
    insertAfter(ref, content) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          const refItem = ref && ref instanceof AbstractType ? ref._item : ref;
          typeListInsertGenericsAfter(transaction, this, refItem, content);
        });
      } else {
        const pc = (
          /** @type {Array<any>} */
          this._prelimContent
        );
        const index = ref === null ? 0 : pc.findIndex((el) => el === ref) + 1;
        if (index === 0 && ref !== null) {
          throw create3("Reference item not found");
        }
        pc.splice(index, 0, ...content);
      }
    }
    /**
     * Deletes elements starting from an index.
     *
     * @param {number} index Index at which to start deleting elements
     * @param {number} [length=1] The number of elements to remove. Defaults to 1.
     */
    delete(index, length2 = 1) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeListDelete(transaction, this, index, length2);
        });
      } else {
        this._prelimContent.splice(index, length2);
      }
    }
    /**
     * Transforms this YArray to a JavaScript Array.
     *
     * @return {Array<YXmlElement|YXmlText|YXmlHook>}
     */
    toArray() {
      return typeListToArray(this);
    }
    /**
     * Appends content to this YArray.
     *
     * @param {Array<YXmlElement|YXmlText>} content Array of content to append.
     */
    push(content) {
      this.insert(this.length, content);
    }
    /**
     * Prepends content to this YArray.
     *
     * @param {Array<YXmlElement|YXmlText>} content Array of content to prepend.
     */
    unshift(content) {
      this.insert(0, content);
    }
    /**
     * Returns the i-th element from a YArray.
     *
     * @param {number} index The index of the element to return from the YArray
     * @return {YXmlElement|YXmlText}
     */
    get(index) {
      return typeListGet(this, index);
    }
    /**
     * Returns a portion of this YXmlFragment into a JavaScript Array selected
     * from start to end (end not included).
     *
     * @param {number} [start]
     * @param {number} [end]
     * @return {Array<YXmlElement|YXmlText>}
     */
    slice(start = 0, end = this.length) {
      return typeListSlice(this, start, end);
    }
    /**
     * Executes a provided function on once on every child element.
     *
     * @param {function(YXmlElement|YXmlText,number, typeof self):void} f A function to execute on every element of this YArray.
     */
    forEach(f) {
      typeListForEach(this, f);
    }
    /**
     * Transform the properties of this type to binary and write it to an
     * BinaryEncoder.
     *
     * This is called when this Item is sent to a remote peer.
     *
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder The encoder to write data to.
     */
    _write(encoder) {
      encoder.writeTypeRef(YXmlFragmentRefID);
    }
  };
  var readYXmlFragment = (_decoder) => new YXmlFragment();
  var YXmlElement = class _YXmlElement extends YXmlFragment {
    constructor(nodeName = "UNDEFINED") {
      super();
      this.nodeName = nodeName;
      this._prelimAttrs = /* @__PURE__ */ new Map();
    }
    /**
     * @type {YXmlElement|YXmlText|null}
     */
    get nextSibling() {
      const n = this._item ? this._item.next : null;
      return n ? (
        /** @type {YXmlElement|YXmlText} */
        /** @type {ContentType} */
        n.content.type
      ) : null;
    }
    /**
     * @type {YXmlElement|YXmlText|null}
     */
    get prevSibling() {
      const n = this._item ? this._item.prev : null;
      return n ? (
        /** @type {YXmlElement|YXmlText} */
        /** @type {ContentType} */
        n.content.type
      ) : null;
    }
    /**
     * Integrate this type into the Yjs instance.
     *
     * * Save this struct in the os
     * * This type is sent to other client
     * * Observer functions are fired
     *
     * @param {Doc} y The Yjs instance
     * @param {Item} item
     */
    _integrate(y, item) {
      super._integrate(y, item);
      /** @type {Map<string, any>} */
      this._prelimAttrs.forEach((value, key) => {
        this.setAttribute(key, value);
      });
      this._prelimAttrs = null;
    }
    /**
     * Creates an Item with the same effect as this Item (without position effect)
     *
     * @return {YXmlElement}
     */
    _copy() {
      return new _YXmlElement(this.nodeName);
    }
    /**
     * Makes a copy of this data type that can be included somewhere else.
     *
     * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
     *
     * @return {YXmlElement<KV>}
     */
    clone() {
      const el = new _YXmlElement(this.nodeName);
      const attrs = this.getAttributes();
      forEach(attrs, (value, key) => {
        el.setAttribute(
          key,
          /** @type {any} */
          value
        );
      });
      el.insert(0, this.toArray().map((v) => v instanceof AbstractType ? v.clone() : v));
      return el;
    }
    /**
     * Returns the XML serialization of this YXmlElement.
     * The attributes are ordered by attribute-name, so you can easily use this
     * method to compare YXmlElements
     *
     * @return {string} The string representation of this type.
     *
     * @public
     */
    toString() {
      const attrs = this.getAttributes();
      const stringBuilder = [];
      const keys2 = [];
      for (const key in attrs) {
        keys2.push(key);
      }
      keys2.sort();
      const keysLen = keys2.length;
      for (let i = 0; i < keysLen; i++) {
        const key = keys2[i];
        stringBuilder.push(key + '="' + attrs[key] + '"');
      }
      const nodeName = this.nodeName.toLocaleLowerCase();
      const attrsString = stringBuilder.length > 0 ? " " + stringBuilder.join(" ") : "";
      return `<${nodeName}${attrsString}>${super.toString()}</${nodeName}>`;
    }
    /**
     * Removes an attribute from this YXmlElement.
     *
     * @param {string} attributeName The attribute name that is to be removed.
     *
     * @public
     */
    removeAttribute(attributeName) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeMapDelete(transaction, this, attributeName);
        });
      } else {
        this._prelimAttrs.delete(attributeName);
      }
    }
    /**
     * Sets or updates an attribute.
     *
     * @template {keyof KV & string} KEY
     *
     * @param {KEY} attributeName The attribute name that is to be set.
     * @param {KV[KEY]} attributeValue The attribute value that is to be set.
     *
     * @public
     */
    setAttribute(attributeName, attributeValue) {
      if (this.doc !== null) {
        transact(this.doc, (transaction) => {
          typeMapSet(transaction, this, attributeName, attributeValue);
        });
      } else {
        this._prelimAttrs.set(attributeName, attributeValue);
      }
    }
    /**
     * Returns an attribute value that belongs to the attribute name.
     *
     * @template {keyof KV & string} KEY
     *
     * @param {KEY} attributeName The attribute name that identifies the
     *                               queried value.
     * @return {KV[KEY]|undefined} The queried attribute value.
     *
     * @public
     */
    getAttribute(attributeName) {
      return (
        /** @type {any} */
        typeMapGet(this, attributeName)
      );
    }
    /**
     * Returns whether an attribute exists
     *
     * @param {string} attributeName The attribute name to check for existence.
     * @return {boolean} whether the attribute exists.
     *
     * @public
     */
    hasAttribute(attributeName) {
      return (
        /** @type {any} */
        typeMapHas(this, attributeName)
      );
    }
    /**
     * Returns all attribute name/value pairs in a JSON Object.
     *
     * @param {Snapshot} [snapshot]
     * @return {{ [Key in Extract<keyof KV,string>]?: KV[Key]}} A JSON Object that describes the attributes.
     *
     * @public
     */
    getAttributes(snapshot) {
      return (
        /** @type {any} */
        snapshot ? typeMapGetAllSnapshot(this, snapshot) : typeMapGetAll(this)
      );
    }
    /**
     * Creates a Dom Element that mirrors this YXmlElement.
     *
     * @param {Document} [_document=document] The document object (you must define
     *                                        this when calling this method in
     *                                        nodejs)
     * @param {Object<string, any>} [hooks={}] Optional property to customize how hooks
     *                                             are presented in the DOM
     * @param {any} [binding] You should not set this property. This is
     *                               used if DomBinding wants to create a
     *                               association to the created DOM type.
     * @return {Node} The {@link https://developer.mozilla.org/en-US/docs/Web/API/Element|Dom Element}
     *
     * @public
     */
    toDOM(_document = document, hooks = {}, binding) {
      const dom = _document.createElement(this.nodeName);
      const attrs = this.getAttributes();
      for (const key in attrs) {
        const value = attrs[key];
        if (typeof value === "string") {
          dom.setAttribute(key, value);
        }
      }
      typeListForEach(this, (yxml) => {
        dom.appendChild(yxml.toDOM(_document, hooks, binding));
      });
      if (binding !== void 0) {
        binding._createAssociation(dom, this);
      }
      return dom;
    }
    /**
     * Transform the properties of this type to binary and write it to an
     * BinaryEncoder.
     *
     * This is called when this Item is sent to a remote peer.
     *
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder The encoder to write data to.
     */
    _write(encoder) {
      encoder.writeTypeRef(YXmlElementRefID);
      encoder.writeKey(this.nodeName);
    }
  };
  var readYXmlElement = (decoder) => new YXmlElement(decoder.readKey());
  var YXmlEvent = class extends YEvent {
    /**
     * @param {YXmlElement|YXmlText|YXmlFragment} target The target on which the event is created.
     * @param {Set<string|null>} subs The set of changed attributes. `null` is included if the
     *                   child list changed.
     * @param {Transaction} transaction The transaction instance with which the
     *                                  change was created.
     */
    constructor(target, subs, transaction) {
      super(target, transaction);
      this.childListChanged = false;
      this.attributesChanged = /* @__PURE__ */ new Set();
      subs.forEach((sub) => {
        if (sub === null) {
          this.childListChanged = true;
        } else {
          this.attributesChanged.add(sub);
        }
      });
    }
  };
  var YXmlHook = class _YXmlHook extends YMap {
    /**
     * @param {string} hookName nodeName of the Dom Node.
     */
    constructor(hookName) {
      super();
      this.hookName = hookName;
    }
    /**
     * Creates an Item with the same effect as this Item (without position effect)
     */
    _copy() {
      return new _YXmlHook(this.hookName);
    }
    /**
     * Makes a copy of this data type that can be included somewhere else.
     *
     * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
     *
     * @return {YXmlHook}
     */
    clone() {
      const el = new _YXmlHook(this.hookName);
      this.forEach((value, key) => {
        el.set(key, value);
      });
      return el;
    }
    /**
     * Creates a Dom Element that mirrors this YXmlElement.
     *
     * @param {Document} [_document=document] The document object (you must define
     *                                        this when calling this method in
     *                                        nodejs)
     * @param {Object.<string, any>} [hooks] Optional property to customize how hooks
     *                                             are presented in the DOM
     * @param {any} [binding] You should not set this property. This is
     *                               used if DomBinding wants to create a
     *                               association to the created DOM type
     * @return {Element} The {@link https://developer.mozilla.org/en-US/docs/Web/API/Element|Dom Element}
     *
     * @public
     */
    toDOM(_document = document, hooks = {}, binding) {
      const hook = hooks[this.hookName];
      let dom;
      if (hook !== void 0) {
        dom = hook.createDom(this);
      } else {
        dom = document.createElement(this.hookName);
      }
      dom.setAttribute("data-yjs-hook", this.hookName);
      if (binding !== void 0) {
        binding._createAssociation(dom, this);
      }
      return dom;
    }
    /**
     * Transform the properties of this type to binary and write it to an
     * BinaryEncoder.
     *
     * This is called when this Item is sent to a remote peer.
     *
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder The encoder to write data to.
     */
    _write(encoder) {
      encoder.writeTypeRef(YXmlHookRefID);
      encoder.writeKey(this.hookName);
    }
  };
  var readYXmlHook = (decoder) => new YXmlHook(decoder.readKey());
  var YXmlText = class _YXmlText extends YText {
    /**
     * @type {YXmlElement|YXmlText|null}
     */
    get nextSibling() {
      const n = this._item ? this._item.next : null;
      return n ? (
        /** @type {YXmlElement|YXmlText} */
        /** @type {ContentType} */
        n.content.type
      ) : null;
    }
    /**
     * @type {YXmlElement|YXmlText|null}
     */
    get prevSibling() {
      const n = this._item ? this._item.prev : null;
      return n ? (
        /** @type {YXmlElement|YXmlText} */
        /** @type {ContentType} */
        n.content.type
      ) : null;
    }
    _copy() {
      return new _YXmlText();
    }
    /**
     * Makes a copy of this data type that can be included somewhere else.
     *
     * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
     *
     * @return {YXmlText}
     */
    clone() {
      const text2 = new _YXmlText();
      text2.applyDelta(this.toDelta());
      return text2;
    }
    /**
     * Creates a Dom Element that mirrors this YXmlText.
     *
     * @param {Document} [_document=document] The document object (you must define
     *                                        this when calling this method in
     *                                        nodejs)
     * @param {Object<string, any>} [hooks] Optional property to customize how hooks
     *                                             are presented in the DOM
     * @param {any} [binding] You should not set this property. This is
     *                               used if DomBinding wants to create a
     *                               association to the created DOM type.
     * @return {Text} The {@link https://developer.mozilla.org/en-US/docs/Web/API/Element|Dom Element}
     *
     * @public
     */
    toDOM(_document = document, hooks, binding) {
      const dom = _document.createTextNode(this.toString());
      if (binding !== void 0) {
        binding._createAssociation(dom, this);
      }
      return dom;
    }
    toString() {
      return this.toDelta().map((delta) => {
        const nestedNodes = [];
        for (const nodeName in delta.attributes) {
          const attrs = [];
          for (const key in delta.attributes[nodeName]) {
            attrs.push({ key, value: delta.attributes[nodeName][key] });
          }
          attrs.sort((a, b) => a.key < b.key ? -1 : 1);
          nestedNodes.push({ nodeName, attrs });
        }
        nestedNodes.sort((a, b) => a.nodeName < b.nodeName ? -1 : 1);
        let str = "";
        for (let i = 0; i < nestedNodes.length; i++) {
          const node = nestedNodes[i];
          str += `<${node.nodeName}`;
          for (let j = 0; j < node.attrs.length; j++) {
            const attr = node.attrs[j];
            str += ` ${attr.key}="${attr.value}"`;
          }
          str += ">";
        }
        str += delta.insert;
        for (let i = nestedNodes.length - 1; i >= 0; i--) {
          str += `</${nestedNodes[i].nodeName}>`;
        }
        return str;
      }).join("");
    }
    /**
     * @return {string}
     */
    toJSON() {
      return this.toString();
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     */
    _write(encoder) {
      encoder.writeTypeRef(YXmlTextRefID);
    }
  };
  var readYXmlText = (decoder) => new YXmlText();
  var AbstractStruct = class {
    /**
     * @param {ID} id
     * @param {number} length
     */
    constructor(id2, length2) {
      this.id = id2;
      this.length = length2;
    }
    /**
     * @type {boolean}
     */
    get deleted() {
      throw methodUnimplemented();
    }
    /**
     * Merge this struct with the item to the right.
     * This method is already assuming that `this.id.clock + this.length === this.id.clock`.
     * Also this method does *not* remove right from StructStore!
     * @param {AbstractStruct} right
     * @return {boolean} whether this merged with right
     */
    mergeWith(right) {
      return false;
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder The encoder to write data to.
     * @param {number} offset
     * @param {number} encodingRef
     */
    write(encoder, offset, encodingRef) {
      throw methodUnimplemented();
    }
    /**
     * @param {Transaction} transaction
     * @param {number} offset
     */
    integrate(transaction, offset) {
      throw methodUnimplemented();
    }
  };
  var structGCRefNumber = 0;
  var GC = class extends AbstractStruct {
    get deleted() {
      return true;
    }
    delete() {
    }
    /**
     * @param {GC} right
     * @return {boolean}
     */
    mergeWith(right) {
      if (this.constructor !== right.constructor) {
        return false;
      }
      this.length += right.length;
      return true;
    }
    /**
     * @param {Transaction} transaction
     * @param {number} offset
     */
    integrate(transaction, offset) {
      if (offset > 0) {
        this.id.clock += offset;
        this.length -= offset;
      }
      addStruct(transaction.doc.store, this);
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeInfo(structGCRefNumber);
      encoder.writeLen(this.length - offset);
    }
    /**
     * @param {Transaction} transaction
     * @param {StructStore} store
     * @return {null | number}
     */
    getMissing(transaction, store2) {
      return null;
    }
  };
  var ContentBinary = class _ContentBinary {
    /**
     * @param {Uint8Array} content
     */
    constructor(content) {
      this.content = content;
    }
    /**
     * @return {number}
     */
    getLength() {
      return 1;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return [this.content];
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentBinary}
     */
    copy() {
      return new _ContentBinary(this.content);
    }
    /**
     * @param {number} offset
     * @return {ContentBinary}
     */
    splice(offset) {
      throw methodUnimplemented();
    }
    /**
     * @param {ContentBinary} right
     * @return {boolean}
     */
    mergeWith(right) {
      return false;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeBuf(this.content);
    }
    /**
     * @return {number}
     */
    getRef() {
      return 3;
    }
  };
  var readContentBinary = (decoder) => new ContentBinary(decoder.readBuf());
  var ContentDeleted = class _ContentDeleted {
    /**
     * @param {number} len
     */
    constructor(len) {
      this.len = len;
    }
    /**
     * @return {number}
     */
    getLength() {
      return this.len;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return [];
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return false;
    }
    /**
     * @return {ContentDeleted}
     */
    copy() {
      return new _ContentDeleted(this.len);
    }
    /**
     * @param {number} offset
     * @return {ContentDeleted}
     */
    splice(offset) {
      const right = new _ContentDeleted(this.len - offset);
      this.len = offset;
      return right;
    }
    /**
     * @param {ContentDeleted} right
     * @return {boolean}
     */
    mergeWith(right) {
      this.len += right.len;
      return true;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
      addToDeleteSet(transaction.deleteSet, item.id.client, item.id.clock, this.len);
      item.markDeleted();
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeLen(this.len - offset);
    }
    /**
     * @return {number}
     */
    getRef() {
      return 1;
    }
  };
  var readContentDeleted = (decoder) => new ContentDeleted(decoder.readLen());
  var createDocFromOpts = (guid, opts) => new Doc({ guid, ...opts, shouldLoad: opts.shouldLoad || opts.autoLoad || false });
  var ContentDoc = class _ContentDoc {
    /**
     * @param {Doc} doc
     */
    constructor(doc2) {
      if (doc2._item) {
        console.error("This document was already integrated as a sub-document. You should create a second instance instead with the same guid.");
      }
      this.doc = doc2;
      const opts = {};
      this.opts = opts;
      if (!doc2.gc) {
        opts.gc = false;
      }
      if (doc2.autoLoad) {
        opts.autoLoad = true;
      }
      if (doc2.meta !== null) {
        opts.meta = doc2.meta;
      }
    }
    /**
     * @return {number}
     */
    getLength() {
      return 1;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return [this.doc];
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentDoc}
     */
    copy() {
      return new _ContentDoc(createDocFromOpts(this.doc.guid, this.opts));
    }
    /**
     * @param {number} offset
     * @return {ContentDoc}
     */
    splice(offset) {
      throw methodUnimplemented();
    }
    /**
     * @param {ContentDoc} right
     * @return {boolean}
     */
    mergeWith(right) {
      return false;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
      this.doc._item = item;
      transaction.subdocsAdded.add(this.doc);
      if (this.doc.shouldLoad) {
        transaction.subdocsLoaded.add(this.doc);
      }
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
      if (transaction.subdocsAdded.has(this.doc)) {
        transaction.subdocsAdded.delete(this.doc);
      } else {
        transaction.subdocsRemoved.add(this.doc);
      }
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeString(this.doc.guid);
      encoder.writeAny(this.opts);
    }
    /**
     * @return {number}
     */
    getRef() {
      return 9;
    }
  };
  var readContentDoc = (decoder) => new ContentDoc(createDocFromOpts(decoder.readString(), decoder.readAny()));
  var ContentEmbed = class _ContentEmbed {
    /**
     * @param {Object} embed
     */
    constructor(embed) {
      this.embed = embed;
    }
    /**
     * @return {number}
     */
    getLength() {
      return 1;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return [this.embed];
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentEmbed}
     */
    copy() {
      return new _ContentEmbed(this.embed);
    }
    /**
     * @param {number} offset
     * @return {ContentEmbed}
     */
    splice(offset) {
      throw methodUnimplemented();
    }
    /**
     * @param {ContentEmbed} right
     * @return {boolean}
     */
    mergeWith(right) {
      return false;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeJSON(this.embed);
    }
    /**
     * @return {number}
     */
    getRef() {
      return 5;
    }
  };
  var readContentEmbed = (decoder) => new ContentEmbed(decoder.readJSON());
  var ContentFormat = class _ContentFormat {
    /**
     * @param {string} key
     * @param {Object} value
     */
    constructor(key, value) {
      this.key = key;
      this.value = value;
    }
    /**
     * @return {number}
     */
    getLength() {
      return 1;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return [];
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return false;
    }
    /**
     * @return {ContentFormat}
     */
    copy() {
      return new _ContentFormat(this.key, this.value);
    }
    /**
     * @param {number} _offset
     * @return {ContentFormat}
     */
    splice(_offset) {
      throw methodUnimplemented();
    }
    /**
     * @param {ContentFormat} _right
     * @return {boolean}
     */
    mergeWith(_right) {
      return false;
    }
    /**
     * @param {Transaction} _transaction
     * @param {Item} item
     */
    integrate(_transaction, item) {
      const p = (
        /** @type {YText} */
        item.parent
      );
      p._searchMarker = null;
      p._hasFormatting = true;
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeKey(this.key);
      encoder.writeJSON(this.value);
    }
    /**
     * @return {number}
     */
    getRef() {
      return 6;
    }
  };
  var readContentFormat = (decoder) => new ContentFormat(decoder.readKey(), decoder.readJSON());
  var ContentJSON = class _ContentJSON {
    /**
     * @param {Array<any>} arr
     */
    constructor(arr) {
      this.arr = arr;
    }
    /**
     * @return {number}
     */
    getLength() {
      return this.arr.length;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return this.arr;
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentJSON}
     */
    copy() {
      return new _ContentJSON(this.arr);
    }
    /**
     * @param {number} offset
     * @return {ContentJSON}
     */
    splice(offset) {
      const right = new _ContentJSON(this.arr.slice(offset));
      this.arr = this.arr.slice(0, offset);
      return right;
    }
    /**
     * @param {ContentJSON} right
     * @return {boolean}
     */
    mergeWith(right) {
      this.arr = this.arr.concat(right.arr);
      return true;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      const len = this.arr.length;
      encoder.writeLen(len - offset);
      for (let i = offset; i < len; i++) {
        const c = this.arr[i];
        encoder.writeString(c === void 0 ? "undefined" : JSON.stringify(c));
      }
    }
    /**
     * @return {number}
     */
    getRef() {
      return 2;
    }
  };
  var readContentJSON = (decoder) => {
    const len = decoder.readLen();
    const cs = [];
    for (let i = 0; i < len; i++) {
      const c = decoder.readString();
      if (c === "undefined") {
        cs.push(void 0);
      } else {
        cs.push(JSON.parse(c));
      }
    }
    return new ContentJSON(cs);
  };
  var isDevMode = getVariable("node_env") === "development";
  var ContentAny = class _ContentAny {
    /**
     * @param {Array<any>} arr
     */
    constructor(arr) {
      this.arr = arr;
      isDevMode && deepFreeze(arr);
    }
    /**
     * @return {number}
     */
    getLength() {
      return this.arr.length;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return this.arr;
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentAny}
     */
    copy() {
      return new _ContentAny(this.arr);
    }
    /**
     * @param {number} offset
     * @return {ContentAny}
     */
    splice(offset) {
      const right = new _ContentAny(this.arr.slice(offset));
      this.arr = this.arr.slice(0, offset);
      return right;
    }
    /**
     * @param {ContentAny} right
     * @return {boolean}
     */
    mergeWith(right) {
      this.arr = this.arr.concat(right.arr);
      return true;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      const len = this.arr.length;
      encoder.writeLen(len - offset);
      for (let i = offset; i < len; i++) {
        const c = this.arr[i];
        encoder.writeAny(c);
      }
    }
    /**
     * @return {number}
     */
    getRef() {
      return 8;
    }
  };
  var readContentAny = (decoder) => {
    const len = decoder.readLen();
    const cs = [];
    for (let i = 0; i < len; i++) {
      cs.push(decoder.readAny());
    }
    return new ContentAny(cs);
  };
  var ContentString = class _ContentString {
    /**
     * @param {string} str
     */
    constructor(str) {
      this.str = str;
    }
    /**
     * @return {number}
     */
    getLength() {
      return this.str.length;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return this.str.split("");
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentString}
     */
    copy() {
      return new _ContentString(this.str);
    }
    /**
     * @param {number} offset
     * @return {ContentString}
     */
    splice(offset) {
      const right = new _ContentString(this.str.slice(offset));
      this.str = this.str.slice(0, offset);
      const firstCharCode = this.str.charCodeAt(offset - 1);
      if (firstCharCode >= 55296 && firstCharCode <= 56319) {
        this.str = this.str.slice(0, offset - 1) + "\uFFFD";
        right.str = "\uFFFD" + right.str.slice(1);
      }
      return right;
    }
    /**
     * @param {ContentString} right
     * @return {boolean}
     */
    mergeWith(right) {
      this.str += right.str;
      return true;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeString(offset === 0 ? this.str : this.str.slice(offset));
    }
    /**
     * @return {number}
     */
    getRef() {
      return 4;
    }
  };
  var readContentString = (decoder) => new ContentString(decoder.readString());
  var typeRefs = [
    readYArray,
    readYMap,
    readYText,
    readYXmlElement,
    readYXmlFragment,
    readYXmlHook,
    readYXmlText
  ];
  var YArrayRefID = 0;
  var YMapRefID = 1;
  var YTextRefID = 2;
  var YXmlElementRefID = 3;
  var YXmlFragmentRefID = 4;
  var YXmlHookRefID = 5;
  var YXmlTextRefID = 6;
  var ContentType = class _ContentType {
    /**
     * @param {AbstractType<any>} type
     */
    constructor(type) {
      this.type = type;
    }
    /**
     * @return {number}
     */
    getLength() {
      return 1;
    }
    /**
     * @return {Array<any>}
     */
    getContent() {
      return [this.type];
    }
    /**
     * @return {boolean}
     */
    isCountable() {
      return true;
    }
    /**
     * @return {ContentType}
     */
    copy() {
      return new _ContentType(this.type._copy());
    }
    /**
     * @param {number} offset
     * @return {ContentType}
     */
    splice(offset) {
      throw methodUnimplemented();
    }
    /**
     * @param {ContentType} right
     * @return {boolean}
     */
    mergeWith(right) {
      return false;
    }
    /**
     * @param {Transaction} transaction
     * @param {Item} item
     */
    integrate(transaction, item) {
      this.type._integrate(transaction.doc, item);
    }
    /**
     * @param {Transaction} transaction
     */
    delete(transaction) {
      let item = this.type._start;
      while (item !== null) {
        if (!item.deleted) {
          item.delete(transaction);
        } else if (item.id.clock < (transaction.beforeState.get(item.id.client) || 0)) {
          transaction._mergeStructs.push(item);
        }
        item = item.right;
      }
      this.type._map.forEach((item2) => {
        if (!item2.deleted) {
          item2.delete(transaction);
        } else if (item2.id.clock < (transaction.beforeState.get(item2.id.client) || 0)) {
          transaction._mergeStructs.push(item2);
        }
      });
      transaction.changed.delete(this.type);
    }
    /**
     * @param {StructStore} store
     */
    gc(store2) {
      let item = this.type._start;
      while (item !== null) {
        item.gc(store2, true);
        item = item.right;
      }
      this.type._start = null;
      this.type._map.forEach(
        /** @param {Item | null} item */
        (item2) => {
          while (item2 !== null) {
            item2.gc(store2, true);
            item2 = item2.left;
          }
        }
      );
      this.type._map = /* @__PURE__ */ new Map();
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      this.type._write(encoder);
    }
    /**
     * @return {number}
     */
    getRef() {
      return 7;
    }
  };
  var readContentType = (decoder) => new ContentType(typeRefs[decoder.readTypeRef()](decoder));
  var followRedone = (store2, id2) => {
    let nextID = id2;
    let diff = 0;
    let item;
    do {
      if (diff > 0) {
        nextID = createID(nextID.client, nextID.clock + diff);
      }
      item = getItem(store2, nextID);
      diff = nextID.clock - item.id.clock;
      nextID = item.redone;
    } while (nextID !== null && item instanceof Item);
    return {
      item,
      diff
    };
  };
  var keepItem = (item, keep) => {
    while (item !== null && item.keep !== keep) {
      item.keep = keep;
      item = /** @type {AbstractType<any>} */
      item.parent._item;
    }
  };
  var splitItem = (transaction, leftItem, diff) => {
    const { client, clock } = leftItem.id;
    const rightItem = new Item(
      createID(client, clock + diff),
      leftItem,
      createID(client, clock + diff - 1),
      leftItem.right,
      leftItem.rightOrigin,
      leftItem.parent,
      leftItem.parentSub,
      leftItem.content.splice(diff)
    );
    if (leftItem.deleted) {
      rightItem.markDeleted();
    }
    if (leftItem.keep) {
      rightItem.keep = true;
    }
    if (leftItem.redone !== null) {
      rightItem.redone = createID(leftItem.redone.client, leftItem.redone.clock + diff);
    }
    leftItem.right = rightItem;
    if (rightItem.right !== null) {
      rightItem.right.left = rightItem;
    }
    transaction._mergeStructs.push(rightItem);
    if (rightItem.parentSub !== null && rightItem.right === null) {
      rightItem.parent._map.set(rightItem.parentSub, rightItem);
    }
    leftItem.length = diff;
    return rightItem;
  };
  var isDeletedByUndoStack = (stack, id2) => some(
    stack,
    /** @param {StackItem} s */
    (s) => isDeleted(s.deletions, id2)
  );
  var redoItem = (transaction, item, redoitems, itemsToDelete, ignoreRemoteMapChanges, um) => {
    const doc2 = transaction.doc;
    const store2 = doc2.store;
    const ownClientID = doc2.clientID;
    const redone = item.redone;
    if (redone !== null) {
      return getItemCleanStart(transaction, redone);
    }
    let parentItem = (
      /** @type {AbstractType<any>} */
      item.parent._item
    );
    let left = null;
    let right;
    if (parentItem !== null && parentItem.deleted === true) {
      if (parentItem.redone === null && (!redoitems.has(parentItem) || redoItem(transaction, parentItem, redoitems, itemsToDelete, ignoreRemoteMapChanges, um) === null)) {
        return null;
      }
      while (parentItem.redone !== null) {
        parentItem = getItemCleanStart(transaction, parentItem.redone);
      }
    }
    const parentType = parentItem === null ? (
      /** @type {AbstractType<any>} */
      item.parent
    ) : (
      /** @type {ContentType} */
      parentItem.content.type
    );
    if (item.parentSub === null) {
      left = item.left;
      right = item;
      while (left !== null) {
        let leftTrace = left;
        while (leftTrace !== null && /** @type {AbstractType<any>} */
        leftTrace.parent._item !== parentItem) {
          leftTrace = leftTrace.redone === null ? null : getItemCleanStart(transaction, leftTrace.redone);
        }
        if (leftTrace !== null && /** @type {AbstractType<any>} */
        leftTrace.parent._item === parentItem) {
          left = leftTrace;
          break;
        }
        left = left.left;
      }
      while (right !== null) {
        let rightTrace = right;
        while (rightTrace !== null && /** @type {AbstractType<any>} */
        rightTrace.parent._item !== parentItem) {
          rightTrace = rightTrace.redone === null ? null : getItemCleanStart(transaction, rightTrace.redone);
        }
        if (rightTrace !== null && /** @type {AbstractType<any>} */
        rightTrace.parent._item === parentItem) {
          right = rightTrace;
          break;
        }
        right = right.right;
      }
    } else {
      right = null;
      if (item.right && !ignoreRemoteMapChanges) {
        left = item;
        while (left !== null && left.right !== null && (left.right.redone || isDeleted(itemsToDelete, left.right.id) || isDeletedByUndoStack(um.undoStack, left.right.id) || isDeletedByUndoStack(um.redoStack, left.right.id))) {
          left = left.right;
          while (left.redone) left = getItemCleanStart(transaction, left.redone);
        }
        if (left && left.right !== null) {
          return null;
        }
      } else {
        left = parentType._map.get(item.parentSub) || null;
      }
    }
    const nextClock = getState(store2, ownClientID);
    const nextId = createID(ownClientID, nextClock);
    const redoneItem = new Item(
      nextId,
      left,
      left && left.lastId,
      right,
      right && right.id,
      parentType,
      item.parentSub,
      item.content.copy()
    );
    item.redone = nextId;
    keepItem(redoneItem, true);
    redoneItem.integrate(transaction, 0);
    return redoneItem;
  };
  var Item = class _Item extends AbstractStruct {
    /**
     * @param {ID} id
     * @param {Item | null} left
     * @param {ID | null} origin
     * @param {Item | null} right
     * @param {ID | null} rightOrigin
     * @param {AbstractType<any>|ID|null} parent Is a type if integrated, is null if it is possible to copy parent from left or right, is ID before integration to search for it.
     * @param {string | null} parentSub
     * @param {AbstractContent} content
     */
    constructor(id2, left, origin2, right, rightOrigin, parent, parentSub, content) {
      super(id2, content.getLength());
      this.origin = origin2;
      this.left = left;
      this.right = right;
      this.rightOrigin = rightOrigin;
      this.parent = parent;
      this.parentSub = parentSub;
      this.redone = null;
      this.content = content;
      this.info = this.content.isCountable() ? BIT2 : 0;
    }
    /**
     * This is used to mark the item as an indexed fast-search marker
     *
     * @type {boolean}
     */
    set marker(isMarked) {
      if ((this.info & BIT4) > 0 !== isMarked) {
        this.info ^= BIT4;
      }
    }
    get marker() {
      return (this.info & BIT4) > 0;
    }
    /**
     * If true, do not garbage collect this Item.
     */
    get keep() {
      return (this.info & BIT1) > 0;
    }
    set keep(doKeep) {
      if (this.keep !== doKeep) {
        this.info ^= BIT1;
      }
    }
    get countable() {
      return (this.info & BIT2) > 0;
    }
    /**
     * Whether this item was deleted or not.
     * @type {Boolean}
     */
    get deleted() {
      return (this.info & BIT3) > 0;
    }
    set deleted(doDelete) {
      if (this.deleted !== doDelete) {
        this.info ^= BIT3;
      }
    }
    markDeleted() {
      this.info |= BIT3;
    }
    /**
     * Return the creator clientID of the missing op or define missing items and return null.
     *
     * @param {Transaction} transaction
     * @param {StructStore} store
     * @return {null | number}
     */
    getMissing(transaction, store2) {
      if (this.origin && this.origin.client !== this.id.client && this.origin.clock >= getState(store2, this.origin.client)) {
        return this.origin.client;
      }
      if (this.rightOrigin && this.rightOrigin.client !== this.id.client && this.rightOrigin.clock >= getState(store2, this.rightOrigin.client)) {
        return this.rightOrigin.client;
      }
      if (this.parent && this.parent.constructor === ID && this.id.client !== this.parent.client && this.parent.clock >= getState(store2, this.parent.client)) {
        return this.parent.client;
      }
      if (this.origin) {
        this.left = getItemCleanEnd(transaction, store2, this.origin);
        this.origin = this.left.lastId;
      }
      if (this.rightOrigin) {
        this.right = getItemCleanStart(transaction, this.rightOrigin);
        this.rightOrigin = this.right.id;
      }
      if (this.left && this.left.constructor === GC || this.right && this.right.constructor === GC) {
        this.parent = null;
      } else if (!this.parent) {
        if (this.left && this.left.constructor === _Item) {
          this.parent = this.left.parent;
          this.parentSub = this.left.parentSub;
        } else if (this.right && this.right.constructor === _Item) {
          this.parent = this.right.parent;
          this.parentSub = this.right.parentSub;
        }
      } else if (this.parent.constructor === ID) {
        const parentItem = getItem(store2, this.parent);
        if (parentItem.constructor === GC) {
          this.parent = null;
        } else {
          this.parent = /** @type {ContentType} */
          parentItem.content.type;
        }
      }
      return null;
    }
    /**
     * @param {Transaction} transaction
     * @param {number} offset
     */
    integrate(transaction, offset) {
      if (offset > 0) {
        this.id.clock += offset;
        this.left = getItemCleanEnd(transaction, transaction.doc.store, createID(this.id.client, this.id.clock - 1));
        this.origin = this.left.lastId;
        this.content = this.content.splice(offset);
        this.length -= offset;
      }
      if (this.parent) {
        if (!this.left && (!this.right || this.right.left !== null) || this.left && this.left.right !== this.right) {
          let left = this.left;
          let o;
          if (left !== null) {
            o = left.right;
          } else if (this.parentSub !== null) {
            o = /** @type {AbstractType<any>} */
            this.parent._map.get(this.parentSub) || null;
            while (o !== null && o.left !== null) {
              o = o.left;
            }
          } else {
            o = /** @type {AbstractType<any>} */
            this.parent._start;
          }
          const conflictingItems = /* @__PURE__ */ new Set();
          const itemsBeforeOrigin = /* @__PURE__ */ new Set();
          while (o !== null && o !== this.right) {
            itemsBeforeOrigin.add(o);
            conflictingItems.add(o);
            if (compareIDs(this.origin, o.origin)) {
              if (o.id.client < this.id.client) {
                left = o;
                conflictingItems.clear();
              } else if (compareIDs(this.rightOrigin, o.rightOrigin)) {
                break;
              }
            } else if (o.origin !== null && itemsBeforeOrigin.has(getItem(transaction.doc.store, o.origin))) {
              if (!conflictingItems.has(getItem(transaction.doc.store, o.origin))) {
                left = o;
                conflictingItems.clear();
              }
            } else {
              break;
            }
            o = o.right;
          }
          this.left = left;
        }
        if (this.left !== null) {
          const right = this.left.right;
          this.right = right;
          this.left.right = this;
        } else {
          let r;
          if (this.parentSub !== null) {
            r = /** @type {AbstractType<any>} */
            this.parent._map.get(this.parentSub) || null;
            while (r !== null && r.left !== null) {
              r = r.left;
            }
          } else {
            r = /** @type {AbstractType<any>} */
            this.parent._start;
            this.parent._start = this;
          }
          this.right = r;
        }
        if (this.right !== null) {
          this.right.left = this;
        } else if (this.parentSub !== null) {
          this.parent._map.set(this.parentSub, this);
          if (this.left !== null) {
            this.left.delete(transaction);
          }
        }
        if (this.parentSub === null && this.countable && !this.deleted) {
          this.parent._length += this.length;
        }
        addStruct(transaction.doc.store, this);
        this.content.integrate(transaction, this);
        addChangedTypeToTransaction(
          transaction,
          /** @type {AbstractType<any>} */
          this.parent,
          this.parentSub
        );
        if (
          /** @type {AbstractType<any>} */
          this.parent._item !== null && /** @type {AbstractType<any>} */
          this.parent._item.deleted || this.parentSub !== null && this.right !== null
        ) {
          this.delete(transaction);
        }
      } else {
        new GC(this.id, this.length).integrate(transaction, 0);
      }
    }
    /**
     * Returns the next non-deleted item
     */
    get next() {
      let n = this.right;
      while (n !== null && n.deleted) {
        n = n.right;
      }
      return n;
    }
    /**
     * Returns the previous non-deleted item
     */
    get prev() {
      let n = this.left;
      while (n !== null && n.deleted) {
        n = n.left;
      }
      return n;
    }
    /**
     * Computes the last content address of this Item.
     */
    get lastId() {
      return this.length === 1 ? this.id : createID(this.id.client, this.id.clock + this.length - 1);
    }
    /**
     * Try to merge two items
     *
     * @param {Item} right
     * @return {boolean}
     */
    mergeWith(right) {
      if (this.constructor === right.constructor && compareIDs(right.origin, this.lastId) && this.right === right && compareIDs(this.rightOrigin, right.rightOrigin) && this.id.client === right.id.client && this.id.clock + this.length === right.id.clock && this.deleted === right.deleted && this.redone === null && right.redone === null && this.content.constructor === right.content.constructor && this.content.mergeWith(right.content)) {
        const searchMarker = (
          /** @type {AbstractType<any>} */
          this.parent._searchMarker
        );
        if (searchMarker) {
          searchMarker.forEach((marker) => {
            if (marker.p === right) {
              marker.p = this;
              if (!this.deleted && this.countable) {
                marker.index -= this.length;
              }
            }
          });
        }
        if (right.keep) {
          this.keep = true;
        }
        this.right = right.right;
        if (this.right !== null) {
          this.right.left = this;
        }
        this.length += right.length;
        return true;
      }
      return false;
    }
    /**
     * Mark this Item as deleted.
     *
     * @param {Transaction} transaction
     */
    delete(transaction) {
      if (!this.deleted) {
        const parent = (
          /** @type {AbstractType<any>} */
          this.parent
        );
        if (this.countable && this.parentSub === null) {
          parent._length -= this.length;
        }
        this.markDeleted();
        addToDeleteSet(transaction.deleteSet, this.id.client, this.id.clock, this.length);
        addChangedTypeToTransaction(transaction, parent, this.parentSub);
        this.content.delete(transaction);
      }
    }
    /**
     * @param {StructStore} store
     * @param {boolean} parentGCd
     */
    gc(store2, parentGCd) {
      if (!this.deleted) {
        throw unexpectedCase();
      }
      this.content.gc(store2);
      if (parentGCd) {
        replaceStruct(store2, this, new GC(this.id, this.length));
      } else {
        this.content = new ContentDeleted(this.length);
      }
    }
    /**
     * Transform the properties of this type to binary and write it to an
     * BinaryEncoder.
     *
     * This is called when this Item is sent to a remote peer.
     *
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder The encoder to write data to.
     * @param {number} offset
     */
    write(encoder, offset) {
      const origin2 = offset > 0 ? createID(this.id.client, this.id.clock + offset - 1) : this.origin;
      const rightOrigin = this.rightOrigin;
      const parentSub = this.parentSub;
      const info = this.content.getRef() & BITS5 | (origin2 === null ? 0 : BIT8) | // origin is defined
      (rightOrigin === null ? 0 : BIT7) | // right origin is defined
      (parentSub === null ? 0 : BIT6);
      encoder.writeInfo(info);
      if (origin2 !== null) {
        encoder.writeLeftID(origin2);
      }
      if (rightOrigin !== null) {
        encoder.writeRightID(rightOrigin);
      }
      if (origin2 === null && rightOrigin === null) {
        const parent = (
          /** @type {AbstractType<any>} */
          this.parent
        );
        if (parent._item !== void 0) {
          const parentItem = parent._item;
          if (parentItem === null) {
            const ykey = findRootTypeKey(parent);
            encoder.writeParentInfo(true);
            encoder.writeString(ykey);
          } else {
            encoder.writeParentInfo(false);
            encoder.writeLeftID(parentItem.id);
          }
        } else if (parent.constructor === String) {
          encoder.writeParentInfo(true);
          encoder.writeString(parent);
        } else if (parent.constructor === ID) {
          encoder.writeParentInfo(false);
          encoder.writeLeftID(parent);
        } else {
          unexpectedCase();
        }
        if (parentSub !== null) {
          encoder.writeString(parentSub);
        }
      }
      this.content.write(encoder, offset);
    }
  };
  var readItemContent = (decoder, info) => contentRefs[info & BITS5](decoder);
  var contentRefs = [
    () => {
      unexpectedCase();
    },
    // GC is not ItemContent
    readContentDeleted,
    // 1
    readContentJSON,
    // 2
    readContentBinary,
    // 3
    readContentString,
    // 4
    readContentEmbed,
    // 5
    readContentFormat,
    // 6
    readContentType,
    // 7
    readContentAny,
    // 8
    readContentDoc,
    // 9
    () => {
      unexpectedCase();
    }
    // 10 - Skip is not ItemContent
  ];
  var structSkipRefNumber = 10;
  var Skip = class extends AbstractStruct {
    get deleted() {
      return true;
    }
    delete() {
    }
    /**
     * @param {Skip} right
     * @return {boolean}
     */
    mergeWith(right) {
      if (this.constructor !== right.constructor) {
        return false;
      }
      this.length += right.length;
      return true;
    }
    /**
     * @param {Transaction} transaction
     * @param {number} offset
     */
    integrate(transaction, offset) {
      unexpectedCase();
    }
    /**
     * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
     * @param {number} offset
     */
    write(encoder, offset) {
      encoder.writeInfo(structSkipRefNumber);
      writeVarUint(encoder.restEncoder, this.length - offset);
    }
    /**
     * @param {Transaction} transaction
     * @param {StructStore} store
     * @return {null | number}
     */
    getMissing(transaction, store2) {
      return null;
    }
  };
  var glo = (
    /** @type {any} */
    typeof globalThis !== "undefined" ? globalThis : typeof window !== "undefined" ? window : typeof global !== "undefined" ? global : {}
  );
  var importIdentifier = "__ $YJS$ __";
  if (glo[importIdentifier] === true) {
    console.error("Yjs was already imported. This breaks constructor checks and will lead to issues! - https://github.com/yjs/yjs/issues/438");
  }
  glo[importIdentifier] = true;

  // packages/sync/build-module/config.mjs
  var CRDT_DOC_VERSION = 1;
  var CRDT_DOC_META_PERSISTENCE_KEY = "fromPersistence";
  var CRDT_RECORD_MAP_KEY = "document";
  var CRDT_RECORD_METADATA_MAP_KEY = "documentMeta";
  var CRDT_RECORD_METADATA_SAVED_AT_KEY = "savedAt";
  var CRDT_RECORD_METADATA_SAVED_BY_KEY = "savedBy";
  var CRDT_STATE_MAP_KEY = "state";
  var CRDT_STATE_VERSION_KEY = "version";
  var LOCAL_EDITOR_ORIGIN = "gutenberg";
  var LOCAL_SYNC_MANAGER_ORIGIN = "syncManager";
  var WORDPRESS_META_KEY_FOR_CRDT_DOC_PERSISTENCE = "_crdt_document";

  // packages/sync/build-module/utils.mjs
  function createYjsDoc(documentMeta = {}) {
    const metaMap = new Map(
      Object.entries(documentMeta)
    );
    const ydoc = new Doc({ meta: metaMap });
    const stateMap = ydoc.getMap(CRDT_STATE_MAP_KEY);
    stateMap.set(CRDT_STATE_VERSION_KEY, CRDT_DOC_VERSION);
    return ydoc;
  }
  function markEntityAsSaved(ydoc) {
    const recordMeta = ydoc.getMap(CRDT_RECORD_METADATA_MAP_KEY);
    recordMeta.set(CRDT_RECORD_METADATA_SAVED_AT_KEY, Date.now());
    recordMeta.set(CRDT_RECORD_METADATA_SAVED_BY_KEY, ydoc.clientID);
  }
  function serializeCrdtDoc(crdtDoc) {
    return JSON.stringify({
      document: toBase64(encodeStateAsUpdateV2(crdtDoc))
    });
  }
  function deserializeCrdtDoc(serializedCrdtDoc) {
    try {
      const { document: document2 } = JSON.parse(serializedCrdtDoc);
      const docMeta = {
        [CRDT_DOC_META_PERSISTENCE_KEY]: true
      };
      const ydoc = createYjsDoc(docMeta);
      const yupdate = fromBase64(document2);
      applyUpdateV2(ydoc, yupdate);
      ydoc.clientID = Math.floor(Math.random() * 1e9);
      return ydoc;
    } catch (e) {
      return null;
    }
  }

  // packages/sync/build-module/persistence.mjs
  function getPersistedCrdtDoc(record) {
    const serializedCrdtDoc = record.meta?.[WORDPRESS_META_KEY_FOR_CRDT_DOC_PERSISTENCE];
    if (serializedCrdtDoc) {
      return deserializeCrdtDoc(serializedCrdtDoc);
    }
    return null;
  }
  function createPersistedCRDTDoc(ydoc) {
    return {
      [WORDPRESS_META_KEY_FOR_CRDT_DOC_PERSISTENCE]: serializeCrdtDoc(ydoc)
    };
  }

  // packages/sync/build-module/providers/index.mjs
  var import_hooks = __toESM(require_hooks(), 1);
  var providerCreators = null;
  function isProviderCreator(creator) {
    return "function" === typeof creator;
  }
  function getProviderCreators() {
    if (providerCreators) {
      return providerCreators;
    }
    const filteredProviderCreators = (0, import_hooks.applyFilters)(
      "sync.providers",
      []
      // Replace with `getDefaultProviderCreators()` to enable sync
    );
    if (!Array.isArray(filteredProviderCreators)) {
      providerCreators = [];
      return providerCreators;
    }
    providerCreators = filteredProviderCreators.filter(isProviderCreator);
    return providerCreators;
  }

  // packages/sync/build-module/y-utilities/y-multidoc-undomanager.mjs
  var popStackItem2 = (mum, type) => {
    const stack = type === "undo" ? mum.undoStack : mum.redoStack;
    while (stack.length > 0) {
      const um = (
        /** @type {Y.UndoManager} */
        stack.pop()
      );
      const prevUmStack = type === "undo" ? um.undoStack : um.redoStack;
      const stackItem = (
        /** @type {any} */
        prevUmStack.pop()
      );
      let actionPerformed = false;
      if (type === "undo") {
        um.undoStack = [stackItem];
        actionPerformed = um.undo() !== null;
        um.undoStack = prevUmStack;
      } else {
        um.redoStack = [stackItem];
        actionPerformed = um.redo() !== null;
        um.redoStack = prevUmStack;
      }
      if (actionPerformed) {
        return stackItem;
      }
    }
    return null;
  };
  var YMultiDocUndoManager = class extends Observable {
    /**
     * @param {Y.AbstractType<any>|Array<Y.AbstractType<any>>} typeScope Accepts either a single type, or an array of types
     * @param {ConstructorParameters<typeof Y.UndoManager>[1]} opts
     */
    constructor(typeScope = [], opts = {}) {
      super();
      this.docs = /* @__PURE__ */ new Map();
      this.trackedOrigins = opts.trackedOrigins || /* @__PURE__ */ new Set([null]);
      opts.trackedOrigins = this.trackedOrigins;
      this._defaultOpts = opts;
      this.undoStack = [];
      this.redoStack = [];
      this.addToScope(typeScope);
    }
    /**
     * @param {Array<Y.AbstractType<any>> | Y.AbstractType<any>} ytypes
     */
    addToScope(ytypes) {
      ytypes = isArray(ytypes) ? ytypes : [ytypes];
      ytypes.forEach((ytype) => {
        const ydoc = (
          /** @type {Y.Doc} */
          ytype.doc
        );
        const um = setIfUndefined(this.docs, ydoc, () => {
          const um2 = new UndoManager([ytype], this._defaultOpts);
          um2.on(
            "stack-cleared",
            /** @param {any} opts */
            ({
              undoStackCleared,
              redoStackCleared
            }) => {
              this.clear(undoStackCleared, redoStackCleared);
            }
          );
          ydoc.on("destroy", () => {
            this.docs.delete(ydoc);
            this.undoStack = this.undoStack.filter(
              (um3) => um3.doc !== ydoc
            );
            this.redoStack = this.redoStack.filter(
              (um3) => um3.doc !== ydoc
            );
          });
          um2.on(
            "stack-item-added",
            /** @param {any} change */
            (change) => {
              const stack = change.type === "undo" ? this.undoStack : this.redoStack;
              stack.push(um2);
              this.emit("stack-item-added", [
                { ...change, ydoc },
                this
              ]);
            }
          );
          um2.on(
            "stack-item-updated",
            /** @param {any} change */
            (change) => {
              this.emit("stack-item-updated", [
                { ...change, ydoc },
                this
              ]);
            }
          );
          um2.on(
            "stack-item-popped",
            /** @param {any} change */
            (change) => {
              this.emit("stack-item-popped", [
                { ...change, ydoc },
                this
              ]);
            }
          );
          return um2;
        });
        if (um.scope.every((yt) => yt !== ytype)) {
          um.scope.push(ytype);
        }
      });
    }
    /**
     * @param {any} origin
     */
    /* c8 ignore next 3 */
    addTrackedOrigin(origin2) {
      this.trackedOrigins.add(origin2);
    }
    /**
     * @param {any} origin
     */
    /* c8 ignore next 3 */
    removeTrackedOrigin(origin2) {
      this.trackedOrigins.delete(origin2);
    }
    /**
     * Undo last changes on type.
     *
     * @return {any?} Returns StackItem if a change was applied
     */
    undo() {
      return popStackItem2(this, "undo");
    }
    /**
     * Redo last undo operation.
     *
     * @return {any?} Returns StackItem if a change was applied
     */
    redo() {
      return popStackItem2(this, "redo");
    }
    clear(clearUndoStack = true, clearRedoStack = true) {
      if (clearUndoStack && this.canUndo() || clearRedoStack && this.canRedo()) {
        this.docs.forEach((um) => {
          clearUndoStack && (this.undoStack = []);
          clearRedoStack && (this.redoStack = []);
          um.clear(clearUndoStack, clearRedoStack);
        });
        this.emit("stack-cleared", [
          {
            undoStackCleared: clearUndoStack,
            redoStackCleared: clearRedoStack
          }
        ]);
      }
    }
    /* c8 ignore next 5 */
    stopCapturing() {
      this.docs.forEach((um) => {
        um.stopCapturing();
      });
    }
    /**
     * Are undo steps available?
     *
     * @return {boolean} `true` if undo is possible
     */
    canUndo() {
      return this.undoStack.length > 0;
    }
    /**
     * Are redo steps available?
     *
     * @return {boolean} `true` if redo is possible
     */
    canRedo() {
      return this.redoStack.length > 0;
    }
    destroy() {
      this.docs.forEach((um) => um.destroy());
      super.destroy();
    }
  };

  // packages/sync/build-module/undo-manager.mjs
  function createUndoManager() {
    const yUndoManager = new YMultiDocUndoManager([], {
      // Throttle undo/redo captures after 500ms of inactivity.
      // 500 was selected from subjective local UX testing, shorter timeouts
      // may cause mid-word undo stack items.
      captureTimeout: 500,
      // Ensure that we only scope the undo/redo to the current editor.
      // The yjs document's clientID is added once it's available.
      trackedOrigins: /* @__PURE__ */ new Set([LOCAL_EDITOR_ORIGIN])
    });
    return {
      /**
       * Record changes into the history.
       * Since Yjs automatically tracks changes, this method translates the WordPress
       * HistoryRecord format into Yjs operations.
       *
       * @param _record   A record of changes to record.
       * @param _isStaged Whether to immediately create an undo point or not.
       */
      addRecord(_record, _isStaged = false) {
      },
      /**
       * Add a Yjs map to the scope of the undo manager.
       *
       * @param {Y.Map< any >} ymap                     The Yjs map to add to the scope.
       * @param                handlers
       * @param                handlers.addUndoMeta
       * @param                handlers.restoreUndoMeta
       */
      addToScope(ymap, handlers) {
        if (ymap.doc === null) {
          return;
        }
        const ydoc = ymap.doc;
        yUndoManager.addToScope(ymap);
        const { addUndoMeta, restoreUndoMeta } = handlers;
        yUndoManager.on("stack-item-added", (event) => {
          addUndoMeta(ydoc, event.stackItem.meta);
        });
        yUndoManager.on("stack-item-popped", (event) => {
          restoreUndoMeta(ydoc, event.stackItem.meta);
        });
      },
      /**
       * Undo the last recorded changes.
       *
       */
      undo() {
        if (!yUndoManager.canUndo()) {
          return;
        }
        yUndoManager.undo();
        return [];
      },
      /**
       * Redo the last undone changes.
       */
      redo() {
        if (!yUndoManager.canRedo()) {
          return;
        }
        yUndoManager.redo();
        return [];
      },
      /**
       * Check if there are changes that can be undone.
       *
       * @return {boolean} Whether there are changes to undo.
       */
      hasUndo() {
        return yUndoManager.canUndo();
      },
      /**
       * Check if there are changes that can be redone.
       *
       * @return {boolean} Whether there are changes to redo.
       */
      hasRedo() {
        return yUndoManager.canRedo();
      },
      /**
       * Stop capturing changes into the current undo item.
       * The next change will create a new undo item.
       */
      stopCapturing() {
        yUndoManager.stopCapturing();
      }
    };
  }

  // packages/sync/build-module/manager.mjs
  function createSyncManager() {
    const collectionStates = /* @__PURE__ */ new Map();
    const entityStates = /* @__PURE__ */ new Map();
    let undoManager2;
    async function loadEntity(syncConfig, objectType, objectId, record, handlers) {
      const providerCreators2 = getProviderCreators();
      if (0 === providerCreators2.length) {
        return;
      }
      const entityId = getEntityId(objectType, objectId);
      if (entityStates.has(entityId)) {
        return;
      }
      const ydoc = createYjsDoc({ objectType });
      const recordMap = ydoc.getMap(CRDT_RECORD_MAP_KEY);
      const recordMetaMap = ydoc.getMap(CRDT_RECORD_METADATA_MAP_KEY);
      const now = Date.now();
      const unload = () => {
        providerResults.forEach((result) => result.destroy());
        recordMap.unobserveDeep(onRecordUpdate);
        recordMetaMap.unobserve(onRecordMetaUpdate);
        ydoc.destroy();
        entityStates.delete(entityId);
      };
      const awareness = syncConfig.createAwareness?.(ydoc, objectId);
      const onRecordUpdate = (_events, transaction) => {
        if (transaction.local && !(transaction.origin instanceof UndoManager)) {
          return;
        }
        void updateEntityRecord(objectType, objectId);
      };
      const onRecordMetaUpdate = (event, transaction) => {
        if (transaction.local) {
          return;
        }
        event.keysChanged.forEach((key) => {
          switch (key) {
            case CRDT_RECORD_METADATA_SAVED_AT_KEY:
              const newValue = recordMetaMap.get(CRDT_RECORD_METADATA_SAVED_AT_KEY);
              if ("number" === typeof newValue && newValue > now) {
                void handlers.refetchRecord().catch(() => {
                });
              }
              break;
          }
        });
      };
      if (!undoManager2) {
        undoManager2 = createUndoManager();
      }
      const { addUndoMeta, restoreUndoMeta } = handlers;
      undoManager2.addToScope(recordMap, {
        addUndoMeta,
        restoreUndoMeta
      });
      const entityState = {
        awareness,
        handlers,
        objectId,
        objectType,
        syncConfig,
        unload,
        ydoc
      };
      entityStates.set(entityId, entityState);
      const providerResults = await Promise.all(
        providerCreators2.map(
          (create8) => create8({ objectType, objectId, ydoc, awareness })
        )
      );
      recordMap.observeDeep(onRecordUpdate);
      recordMetaMap.observe(onRecordMetaUpdate);
      applyPersistedCrdtDoc(objectType, objectId, record);
    }
    async function loadCollection(syncConfig, objectType, handlers) {
      const providerCreators2 = getProviderCreators();
      if (0 === providerCreators2.length) {
        return;
      }
      if (collectionStates.has(objectType)) {
        return;
      }
      const ydoc = createYjsDoc({ collection: true, objectType });
      const recordMetaMap = ydoc.getMap(CRDT_RECORD_METADATA_MAP_KEY);
      const now = Date.now();
      const unload = () => {
        providerResults.forEach((result) => result.destroy());
        recordMetaMap.unobserve(onRecordMetaUpdate);
        ydoc.destroy();
        collectionStates.delete(objectType);
      };
      const onRecordMetaUpdate = (event, transaction) => {
        if (transaction.local) {
          return;
        }
        event.keysChanged.forEach((key) => {
          switch (key) {
            case CRDT_RECORD_METADATA_SAVED_AT_KEY:
              const newValue = recordMetaMap.get(CRDT_RECORD_METADATA_SAVED_AT_KEY);
              if ("number" === typeof newValue && newValue > now) {
                void handlers.refetchRecords().catch(() => {
                });
              }
              break;
          }
        });
      };
      const awareness = syncConfig.createAwareness?.(ydoc);
      awareness?.setUp();
      const collectionState = {
        awareness,
        handlers,
        syncConfig,
        unload,
        ydoc
      };
      collectionStates.set(objectType, collectionState);
      const providerResults = await Promise.all(
        providerCreators2.map((create8) => {
          return create8({
            awareness,
            objectType,
            objectId: null,
            ydoc
          });
        })
      );
      recordMetaMap.observe(onRecordMetaUpdate);
    }
    function unloadEntity(objectType, objectId) {
      entityStates.get(getEntityId(objectType, objectId))?.unload();
      updateCRDTDoc(objectType, null, {}, origin, { isSave: true });
    }
    function getEntityId(objectType, objectId) {
      return `${objectType}_${objectId}`;
    }
    function getAwareness(objectType, objectId) {
      const entityId = getEntityId(objectType, objectId);
      const entityState = entityStates.get(entityId);
      if (!entityState || !entityState.awareness) {
        return void 0;
      }
      return entityState.awareness;
    }
    function applyPersistedCrdtDoc(objectType, objectId, record) {
      const entityId = getEntityId(objectType, objectId);
      const entityState = entityStates.get(entityId);
      if (!entityState) {
        return;
      }
      const {
        handlers,
        syncConfig: {
          applyChangesToCRDTDoc,
          getChangesFromCRDTDoc,
          supports
        },
        ydoc: targetDoc
      } = entityState;
      if (!supports?.crdtPersistence) {
        targetDoc.transact(() => {
          applyChangesToCRDTDoc(targetDoc, record);
        }, LOCAL_SYNC_MANAGER_ORIGIN);
        return;
      }
      const tempDoc = getPersistedCrdtDoc(record);
      if (!tempDoc) {
        targetDoc.transact(() => {
          applyChangesToCRDTDoc(targetDoc, record);
          handlers.saveRecord();
        }, LOCAL_SYNC_MANAGER_ORIGIN);
        return;
      }
      const update = encodeStateAsUpdateV2(tempDoc);
      applyUpdateV2(targetDoc, update);
      const invalidations = getChangesFromCRDTDoc(tempDoc, record);
      const invalidatedKeys = Object.keys(invalidations);
      tempDoc.destroy();
      if (0 === invalidatedKeys.length) {
        return;
      }
      const changes = invalidatedKeys.reduce(
        (acc, key) => Object.assign(acc, {
          [key]: record[key]
        }),
        {}
      );
      targetDoc.transact(() => {
        applyChangesToCRDTDoc(targetDoc, changes);
        handlers.saveRecord();
      }, LOCAL_SYNC_MANAGER_ORIGIN);
    }
    function updateCRDTDoc(objectType, objectId, changes, origin2, options = {}) {
      const { isSave = false, isNewUndoLevel = false } = options;
      const entityId = getEntityId(objectType, objectId);
      const entityState = entityStates.get(entityId);
      const collectionState = collectionStates.get(objectType);
      if (entityState) {
        const { syncConfig, ydoc } = entityState;
        if (isNewUndoLevel && undoManager2) {
          undoManager2.stopCapturing?.();
        }
        ydoc.transact(() => {
          syncConfig.applyChangesToCRDTDoc(ydoc, changes);
          if (isSave) {
            markEntityAsSaved(ydoc);
          }
        }, origin2);
      }
      if (collectionState && isSave) {
        collectionState.ydoc.transact(() => {
          markEntityAsSaved(collectionState.ydoc);
        }, origin2);
      }
    }
    async function updateEntityRecord(objectType, objectId) {
      const entityId = getEntityId(objectType, objectId);
      const entityState = entityStates.get(entityId);
      if (!entityState) {
        return;
      }
      const { handlers, syncConfig, ydoc } = entityState;
      const changes = syncConfig.getChangesFromCRDTDoc(
        ydoc,
        await handlers.getEditedRecord()
      );
      if (0 === Object.keys(changes).length) {
        return;
      }
      handlers.editRecord(changes);
    }
    function createEntityMeta(objectType, objectId) {
      const entityId = getEntityId(objectType, objectId);
      const entityState = entityStates.get(entityId);
      if (!entityState?.syncConfig.supports?.crdtPersistence) {
        return {};
      }
      return createPersistedCRDTDoc(entityState.ydoc);
    }
    return {
      createMeta: createEntityMeta,
      getAwareness,
      load: loadEntity,
      loadCollection,
      // Use getter to ensure we always return the current value of `undoManager`.
      get undoManager() {
        return undoManager2;
      },
      unload: unloadEntity,
      update: updateCRDTDoc
    };
  }

  // packages/core-data/build-module/name.mjs
  var STORE_NAME = "core";

  // packages/core-data/build-module/sync.mjs
  var syncManager;
  function getSyncManager() {
    if (syncManager) {
      return syncManager;
    }
    syncManager = createSyncManager();
    return syncManager;
  }

  // node_modules/uuid/dist/esm-browser/rng.js
  var getRandomValues2;
  var rnds8 = new Uint8Array(16);
  function rng() {
    if (!getRandomValues2) {
      getRandomValues2 = typeof crypto !== "undefined" && crypto.getRandomValues && crypto.getRandomValues.bind(crypto);
      if (!getRandomValues2) {
        throw new Error("crypto.getRandomValues() not supported. See https://github.com/uuidjs/uuid#getrandomvalues-not-supported");
      }
    }
    return getRandomValues2(rnds8);
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

  // packages/core-data/build-module/entities.mjs
  var DEFAULT_ENTITY_KEY = "id";
  var POST_RAW_ATTRIBUTES = ["title", "excerpt", "content"];
  var blocksTransientEdits = {
    blocks: {
      read: (record) => (0, import_blocks.parse)(record.content?.raw ?? ""),
      write: (record) => ({
        content: (0, import_blocks.__unstableSerializeAndClean)(record.blocks)
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
        // @see lib/compat/wordpress-6.8/preload.php
        _fields: [
          "description",
          "gmt_offset",
          "home",
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
    }
  ].map((entity2) => {
    const syncEnabledRootEntities = /* @__PURE__ */ new Set(["comment"]);
    if (false) {
      if (syncEnabledRootEntities.has(entity2.name)) {
        entity2.syncConfig = defaultSyncConfig;
      }
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
    if (false) {
      if (persistedRecord) {
        const objectType = `postType/${name}`;
        const objectId = persistedRecord.id;
        const meta = getSyncManager()?.createMeta(objectType, objectId);
        newEdits.meta = {
          ...edits.meta,
          ...meta
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
      if (false) {
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
           * @return {import('@wordpress/sync').AwarenessState} AwarenessState instance
           */
          createAwareness: (ydoc, objectId) => {
            const kind = "postType";
            const id2 = parseInt(objectId, 10);
            return new PostEditorAwareness(ydoc, kind, name, id2);
          },
          /**
           * Extract changes from a CRDT document that can be used to update the
           * local editor state.
           *
           * @param {import('@wordpress/sync').CRDTDoc}    crdtDoc
           * @param {import('@wordpress/sync').ObjectData} editedRecord
           * @return {Partial< import('@wordpress/sync').ObjectData >} Changes to record
           */
          getChangesFromCRDTDoc: (crdtDoc, editedRecord) => getPostChangesFromCRDTDoc(
            crdtDoc,
            editedRecord,
            postType
          ),
          /**
           * Sync features supported by the entity.
           *
           * @type {Record< string, boolean >}
           */
          supports: {
            crdtPersistence: true
          }
        };
      }
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
      if (false) {
        entity2.syncConfig = defaultSyncConfig;
      }
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
    const size2 = Math.max(
      itemIds?.length ?? 0,
      nextItemIdsStartIndex + nextItemIds.length
    );
    const mergedItemIds = new Array(size2);
    for (let i = 0; i < size2; i++) {
      const isInNextItemsRange = i >= nextItemIdsStartIndex && i < nextItemIdsStartIndex + perPage;
      mergedItemIds[i] = isInNextItemsRange ? nextItemIds[i - nextItemIdsStartIndex] : itemIds?.[i];
    }
    return mergedItemIds;
  }
  function removeEntitiesById(entities2, ids) {
    return Object.fromEntries(
      Object.entries(entities2).filter(
        ([id2]) => !ids.some((itemId) => {
          if (Number.isInteger(itemId)) {
            return itemId === +id2;
          }
          return itemId === id2;
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
  var reducer_default = (0, import_data2.combineReducers)({
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
      (0, import_data3.combineReducers)({
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
                      !(0, import_es62.default)(
                        edits[key],
                        record[key]?.raw ?? record[key]
                      ) && // Sometimes the server alters the sent value which means
                      // we need to also remove the edits before the api request.
                      (!action.persistedEdits || !(0, import_es62.default)(
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
                ([id2]) => !action.itemIds.some((itemId) => {
                  if (Number.isInteger(itemId)) {
                    return itemId === +id2;
                  }
                  return itemId === id2;
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
      entitiesDataReducer = (0, import_data3.combineReducers)(
        Object.fromEntries(
          Object.entries(entitiesByKind).map(
            ([kind, subEntities]) => {
              const kindReducer = (0, import_data3.combineReducers)(
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
  function undoManager(state = (0, import_undo_manager2.createUndoManager)()) {
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
  var reducer_default2 = (0, import_data3.combineReducers)({
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
    editorAssets
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
  var import_data5 = __toESM(require_data(), 1);
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
  var import_data4 = __toESM(require_data(), 1);

  // packages/core-data/build-module/lock-unlock.mjs
  var import_private_apis = __toESM(require_private_apis(), 1);
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/core-data"
  );

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
    if (false) {
      return getSyncManager()?.undoManager ?? state.undoManager;
    }
    return state.undoManager;
  }
  function getNavigationFallbackId(state) {
    return state.navigationFallbackId;
  }
  var getBlockPatternsForPostType = (0, import_data4.createRegistrySelector)(
    (select) => (0, import_data4.createSelector)(
      (state, postType) => select(STORE_NAME).getBlockPatterns().filter(
        ({ postTypes }) => !postTypes || Array.isArray(postTypes) && postTypes.includes(postType)
      ),
      () => [select(STORE_NAME).getBlockPatterns()]
    )
  );
  var getEntityRecordsPermissions = (0, import_data4.createRegistrySelector)(
    (select) => (0, import_data4.createSelector)(
      (state, kind, name, ids) => {
        const normalizedIds = Array.isArray(ids) ? ids : [ids];
        return normalizedIds.map((id2) => ({
          delete: select(STORE_NAME).canUser("delete", {
            kind,
            name,
            id: id2
          }),
          update: select(STORE_NAME).canUser("update", {
            kind,
            name,
            id: id2
          })
        }));
      },
      (state) => [state.userPermissions]
    )
  );
  function getEntityRecordPermissions(state, kind, name, id2) {
    logEntityDeprecation(kind, name, "getEntityRecordPermissions");
    return getEntityRecordsPermissions(state, kind, name, id2)[0];
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
  var getHomePage = (0, import_data4.createRegistrySelector)(
    (select) => (0, import_data4.createSelector)(
      () => {
        const siteData = select(STORE_NAME).getEntityRecord(
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
        const frontPageTemplateId = select(
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
  var getPostsPageId = (0, import_data4.createRegistrySelector)((select) => () => {
    const siteData = select(STORE_NAME).getEntityRecord(
      "root",
      "__unstableBase"
    );
    return siteData?.show_on_front === "page" ? normalizePageId(siteData.page_for_posts) : null;
  });
  var getTemplateId = (0, import_data4.createRegistrySelector)(
    (select) => (state, postType, postId) => {
      const homepage = unlock(select(STORE_NAME)).getHomePage();
      if (!homepage) {
        return;
      }
      if (postType === "page" && postType === homepage?.postType && postId.toString() === homepage?.postId) {
        const templates = select(STORE_NAME).getEntityRecords(
          "postType",
          "wp_template",
          {
            per_page: -1
          }
        );
        if (!templates) {
          return;
        }
        const id2 = templates.find(({ slug }) => slug === "front-page")?.id;
        if (id2) {
          return id2;
        }
      }
      const editedEntity = select(STORE_NAME).getEditedEntityRecord(
        "postType",
        postType,
        postId
      );
      if (!editedEntity) {
        return;
      }
      const postsPageId = unlock(select(STORE_NAME)).getPostsPageId();
      if (postType === "page" && postsPageId === postId.toString()) {
        return select(STORE_NAME).getDefaultTemplateId({
          slug: "home"
        });
      }
      const currentTemplateSlug = editedEntity.template;
      if (currentTemplateSlug) {
        const currentTemplate = select(STORE_NAME).getEntityRecords("postType", "wp_template", {
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
      return select(STORE_NAME).getDefaultTemplateId({
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
  var isRequestingEmbedPreview = (0, import_data5.createRegistrySelector)(
    (select) => (state, url) => {
      return select(STORE_NAME).isResolving("getEmbedPreview", [
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
  var getUserQueryResults = (0, import_data5.createSelector)(
    (state, queryID) => {
      const queryResults = state.users.queries[queryID] ?? [];
      return queryResults.map((id2) => state.users.byId[id2]);
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
  var getEntitiesConfig = (0, import_data5.createSelector)(
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
  var getEntityRecord = (0, import_data5.createSelector)(
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
  getEntityRecord.__unstableNormalizeArgs = (args2) => {
    const newArgs = [...args2];
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
  var getRawEntityRecord = (0, import_data5.createSelector)(
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
  var __experimentalGetDirtyEntityRecords = (0, import_data5.createSelector)(
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
  var __experimentalGetEntitiesBeingSaved = (0, import_data5.createSelector)(
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
  var getEntityRecordNonTransientEdits = (0, import_data5.createSelector)(
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
  var getEditedEntityRecord = (0, import_data5.createSelector)(
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
  function canUser(state, action, resource, id2) {
    const isEntity = typeof resource === "object";
    if (isEntity && (!resource.kind || !resource.name)) {
      return false;
    }
    if (isEntity) {
      logEntityDeprecation(resource.kind, resource.name, "canUser");
    }
    const key = getUserPermissionCacheKey(action, resource, id2);
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
  var hasFetchedAutosaves = (0, import_data5.createRegistrySelector)(
    (select) => (state, postType, postId) => {
      return select(STORE_NAME).hasFinishedResolution("getAutosaves", [
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
  var getRevision = (0, import_data5.createSelector)(
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
    undo: () => undo
  });
  var import_es63 = __toESM(require_es6(), 1);
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
        const id2 = ++lastId;
        pending.add(id2);
        const add = (input) => new Promise((resolve, reject) => {
          queue.push({
            input,
            resolve,
            reject
          });
          pending.delete(id2);
        });
        if (typeof inputOrThunk === "function") {
          return Promise.resolve(inputOrThunk(add)).finally(() => {
            pending.delete(id2);
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
    constructor(...args2) {
      this.set = new Set(...args2);
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
  var deleteEntityRecord = (kind, name, recordId, query, { __unstableFetch = import_api_fetch3.default, throwOnError = false } = {}) => async ({ dispatch, resolveSelect }) => {
    logEntityDeprecation(kind, name, "deleteEntityRecord");
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    let error;
    let deletedRecord = false;
    if (!entityConfig) {
      return;
    }
    const lock2 = await dispatch.__unstableAcquireStoreLock(
      STORE_NAME,
      ["entities", "records", kind, name, recordId],
      { exclusive: true }
    );
    try {
      dispatch({
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
        await dispatch(removeItems(kind, name, recordId, true));
        if (false) {
          if (entityConfig.syncConfig) {
            const objectType = `${kind}/${name}`;
            const objectId = recordId;
            getSyncManager()?.unload(objectType, objectId);
          }
        }
      } catch (_error) {
        hasError = true;
        error = _error;
      }
      dispatch({
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
      dispatch.__unstableReleaseStoreLock(lock2);
    }
  };
  var editEntityRecord = (kind, name, recordId, edits, options = {}) => ({ select, dispatch }) => {
    logEntityDeprecation(kind, name, "editEntityRecord");
    const entityConfig = select.getEntityConfig(kind, name);
    if (!entityConfig) {
      throw new Error(
        `The entity being edited (${kind}, ${name}) does not have a loaded config.`
      );
    }
    const { mergedEdits = {} } = entityConfig;
    const record = select.getRawEntityRecord(kind, name, recordId);
    const editedRecord = select.getEditedEntityRecord(
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
        acc[key] = (0, import_es63.default)(recordValue, value) ? void 0 : value;
        return acc;
      }, {})
    };
    if (false) {
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
    }
    if (!options.undoIgnore) {
      select.getUndoManager().addRecord(
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
    dispatch({
      type: "EDIT_ENTITY_RECORD",
      ...edit
    });
  };
  var undo = () => ({ select, dispatch }) => {
    const undoRecord = select.getUndoManager().undo();
    if (!undoRecord) {
      return;
    }
    dispatch({
      type: "UNDO",
      record: undoRecord
    });
  };
  var redo = () => ({ select, dispatch }) => {
    const redoRecord = select.getUndoManager().redo();
    if (!redoRecord) {
      return;
    }
    dispatch({
      type: "REDO",
      record: redoRecord
    });
  };
  var __unstableCreateUndoLevel = () => ({ select }) => {
    select.getUndoManager().addRecord();
  };
  var saveEntityRecord = (kind, name, record, {
    isAutosave = false,
    __unstableFetch = import_api_fetch3.default,
    throwOnError = false
  } = {}) => async ({ select, resolveSelect, dispatch }) => {
    logEntityDeprecation(kind, name, "saveEntityRecord");
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    if (!entityConfig) {
      return;
    }
    const entityIdKey = entityConfig.key ?? DEFAULT_ENTITY_KEY;
    const recordId = record[entityIdKey];
    const isNewRecord = !!entityIdKey && !recordId;
    const lock2 = await dispatch.__unstableAcquireStoreLock(
      STORE_NAME,
      ["entities", "records", kind, name, recordId || v4_default()],
      { exclusive: true }
    );
    try {
      for (const [key, value] of Object.entries(record)) {
        if (typeof value === "function") {
          const evaluatedValue = value(
            select.getEditedEntityRecord(kind, name, recordId)
          );
          dispatch.editEntityRecord(
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
      dispatch({
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
        const persistedRecord = !isNewRecord ? select.getRawEntityRecord(kind, name, recordId) : {};
        if (isAutosave) {
          const currentUser2 = select.getCurrentUser();
          const currentUserId = currentUser2 ? currentUser2.id : void 0;
          const autosavePost = await resolveSelect.getAutosave(
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
            dispatch.receiveEntityRecords(
              kind,
              name,
              newRecord,
              void 0,
              true
            );
          } else {
            dispatch.receiveAutosaves(
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
          dispatch.receiveEntityRecords(
            kind,
            name,
            updatedRecord,
            void 0,
            true,
            edits
          );
          if (false) {
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
        }
      } catch (_error) {
        hasError = true;
        error = _error;
      }
      dispatch({
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
      dispatch.__unstableReleaseStoreLock(lock2);
    }
  };
  var __experimentalBatch = (requests) => async ({ dispatch }) => {
    const batch = createBatch();
    const api = {
      saveEntityRecord(kind, name, record, options) {
        return batch.add(
          (add) => dispatch.saveEntityRecord(kind, name, record, {
            ...options,
            __unstableFetch: add
          })
        );
      },
      saveEditedEntityRecord(kind, name, recordId, options) {
        return batch.add(
          (add) => dispatch.saveEditedEntityRecord(kind, name, recordId, {
            ...options,
            __unstableFetch: add
          })
        );
      },
      deleteEntityRecord(kind, name, recordId, query, options) {
        return batch.add(
          (add) => dispatch.deleteEntityRecord(kind, name, recordId, query, {
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
  var saveEditedEntityRecord = (kind, name, recordId, options) => async ({ select, dispatch, resolveSelect }) => {
    logEntityDeprecation(kind, name, "saveEditedEntityRecord");
    if (!select.hasEditsForEntityRecord(kind, name, recordId)) {
      return;
    }
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    if (!entityConfig) {
      return;
    }
    const entityIdKey = entityConfig.key || DEFAULT_ENTITY_KEY;
    const edits = select.getEntityRecordNonTransientEdits(
      kind,
      name,
      recordId
    );
    const record = { [entityIdKey]: recordId, ...edits };
    return await dispatch.saveEntityRecord(kind, name, record, options);
  };
  var __experimentalSaveSpecifiedEntityEdits = (kind, name, recordId, itemsToSave, options) => async ({ select, dispatch, resolveSelect }) => {
    logEntityDeprecation(
      kind,
      name,
      "__experimentalSaveSpecifiedEntityEdits"
    );
    if (!select.hasEditsForEntityRecord(kind, name, recordId)) {
      return;
    }
    const edits = select.getEntityRecordNonTransientEdits(
      kind,
      name,
      recordId
    );
    const editsToSave = {};
    for (const item of itemsToSave) {
      setNestedValue(editsToSave, item, getNestedValue(edits, item));
    }
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    const entityIdKey = entityConfig?.key || DEFAULT_ENTITY_KEY;
    if (recordId) {
      editsToSave[entityIdKey] = recordId;
    }
    return await dispatch.saveEntityRecord(
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
  var receiveRevisions = (kind, name, recordKey, records, query, invalidateCache = false, meta) => async ({ dispatch, resolveSelect }) => {
    logEntityDeprecation(kind, name, "receiveRevisions");
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    const key = entityConfig && entityConfig?.revisionKey ? entityConfig.revisionKey : DEFAULT_ENTITY_KEY;
    dispatch({
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
  var editMediaEntity = (recordId, edits = {}, { __unstableFetch = import_api_fetch4.default, throwOnError = false } = {}) => async ({ dispatch, resolveSelect }) => {
    if (!recordId) {
      return;
    }
    const kind = "postType";
    const name = "attachment";
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.kind === kind && config.name === name
    );
    if (!entityConfig) {
      return;
    }
    const lock2 = await dispatch.__unstableAcquireStoreLock(
      STORE_NAME,
      ["entities", "records", kind, name, recordId],
      { exclusive: true }
    );
    let updatedRecord;
    let error;
    let hasError = false;
    try {
      dispatch({
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
          dispatch.receiveEntityRecords(
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
      dispatch({
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
      dispatch.__unstableReleaseStoreLock(lock2);
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
  function tokenize(text2) {
    return text2.toLowerCase().match(/[\p{L}\p{N}]+/gu) || [];
  }

  // packages/core-data/build-module/fetch/__experimental-fetch-url-data.mjs
  var import_api_fetch6 = __toESM(require_api_fetch(), 1);
  var import_url5 = __toESM(require_url(), 1);
  var CACHE = /* @__PURE__ */ new Map();
  var fetchUrlData = async (url, options = {}) => {
    const endpoint = "/wp-block-editor/v1/url-details";
    const args2 = {
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
      path: (0, import_url5.addQueryArgs)(endpoint, args2),
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
  var getAuthors2 = (query) => async ({ dispatch }) => {
    const path = (0, import_url6.addQueryArgs)(
      "/wp/v2/users/?who=authors&per_page=100",
      query
    );
    const users2 = await (0, import_api_fetch8.default)({ path });
    dispatch.receiveUserQuery(path, users2);
  };
  var getCurrentUser2 = () => async ({ dispatch }) => {
    const currentUser2 = await (0, import_api_fetch8.default)({ path: "/wp/v2/users/me" });
    dispatch.receiveCurrentUser(currentUser2);
  };
  var getEntityRecord2 = (kind, name, key = "", query) => async ({ select, dispatch, registry, resolveSelect }) => {
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.name === name && config.kind === kind
    );
    if (!entityConfig) {
      return;
    }
    const lock2 = await dispatch.__unstableAcquireStoreLock(
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
        const hasRecord = select.hasEntityRecord(
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
      if (false) {
        if (entityConfig.syncConfig && isNumericID(key) && !query) {
          const objectType = `${kind}/${name}`;
          const objectId = key;
          const recordWithTransients = { ...record };
          Object.entries(entityConfig.transientEdits ?? {}).filter(
            ([propName, transientConfig]) => void 0 === recordWithTransients[propName] && transientConfig && "object" === typeof transientConfig && "read" in transientConfig && "function" === typeof transientConfig.read
          ).forEach(([propName, transientConfig]) => {
            recordWithTransients[propName] = transientConfig.read(recordWithTransients);
          });
          await getSyncManager()?.load(
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
                dispatch({
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
              getEditedRecord: async () => await resolveSelect.getEditedEntityRecord(
                kind,
                name,
                key
              ),
              // Refetch the current entity record from the database.
              refetchRecord: async () => {
                dispatch.receiveEntityRecords(
                  kind,
                  name,
                  await (0, import_api_fetch8.default)({ path, parse: true }),
                  query
                );
              },
              // Save the current entity record's unsaved edits.
              saveRecord: () => {
                dispatch.saveEditedEntityRecord(
                  kind,
                  name,
                  key
                );
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
                    restoreSelection(
                      selectionHistory,
                      ydoc
                    );
                  }, 0);
                }
              }
            }
          );
        }
      }
      registry.batch(() => {
        dispatch.receiveEntityRecords(kind, name, record, query);
        dispatch.receiveUserPermissions(receiveUserPermissionArgs);
        dispatch.finishResolutions("canUser", canUserResolutionsArgs);
      });
    } finally {
      dispatch.__unstableReleaseStoreLock(lock2);
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
  var getEntityRecords2 = (kind, name, query = {}) => async ({ dispatch, registry, resolveSelect }) => {
    const configs = await resolveSelect.getEntitiesConfig(kind);
    const entityConfig = configs.find(
      (config) => config.name === name && config.kind === kind
    );
    if (!entityConfig) {
      return;
    }
    const lock2 = await dispatch.__unstableAcquireStoreLock(
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
            dispatch.receiveEntityRecords(
              kind,
              name,
              records,
              query,
              false,
              void 0,
              meta
            );
            dispatch.finishResolutions(
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
      if (false) {
        if (entityConfig.syncConfig && -1 === query.per_page) {
          const objectType = `${kind}/${name}`;
          getSyncManager()?.loadCollection(
            entityConfig.syncConfig,
            objectType,
            {
              refetchRecords: async () => {
                dispatch.receiveEntityRecords(
                  kind,
                  name,
                  await (0, import_api_fetch8.default)({ path, parse: true }),
                  query
                );
              }
            }
          );
        }
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
        dispatch.receiveEntityRecords(
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
          dispatch.receiveUserPermissions(
            receiveUserPermissionArgs
          );
          dispatch.finishResolutions(
            "canUser",
            canUserResolutionsArgs
          );
        }
        dispatch.finishResolutions(
          "getEntityRecord",
          getResolutionsArgs(records, rawQuery)
        );
        dispatch.__unstableReleaseStoreLock(lock2);
      });
    } catch (e) {
      dispatch.__unstableReleaseStoreLock(lock2);
    }
  };
  getEntityRecords2.shouldInvalidate = (action, kind, name) => {
    return (action.type === "RECEIVE_ITEMS" || action.type === "REMOVE_ITEMS") && action.invalidateCache && kind === action.kind && name === action.name;
  };
  var getEntityRecordsTotalItems2 = forward_resolver_default("getEntityRecords");
  var getEntityRecordsTotalPages2 = forward_resolver_default("getEntityRecords");
  var getCurrentTheme2 = () => async ({ dispatch, resolveSelect }) => {
    const activeThemes = await resolveSelect.getEntityRecords(
      "root",
      "theme",
      { status: "active" }
    );
    dispatch.receiveCurrentTheme(activeThemes[0]);
  };
  var getThemeSupports2 = forward_resolver_default("getCurrentTheme");
  var getEmbedPreview2 = (url) => async ({ dispatch }) => {
    try {
      const embedProxyResponse = await (0, import_api_fetch8.default)({
        path: (0, import_url6.addQueryArgs)("/oembed/1.0/proxy", { url })
      });
      dispatch.receiveEmbedPreview(url, embedProxyResponse);
    } catch (error) {
      dispatch.receiveEmbedPreview(url, false);
    }
  };
  var canUser2 = (requestedAction, resource, id2) => async ({ dispatch, registry, resolveSelect }) => {
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
        id2
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
      const configs = await resolveSelect.getEntitiesConfig(
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
      resourcePath = `/wp/v2/${resource}` + (id2 ? "/" + id2 : "");
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
        const key = getUserPermissionCacheKey(action, resource, id2);
        dispatch.receiveUserPermission(key, permissions[action]);
        if (action !== requestedAction) {
          dispatch.finishResolution("canUser", [
            action,
            resource,
            id2
          ]);
        }
      }
    });
  };
  var canUserEditEntityRecord2 = (kind, name, recordId) => async ({ dispatch }) => {
    await dispatch(canUser2("update", { kind, name, id: recordId }));
  };
  var getAutosaves2 = (postType, postId) => async ({ dispatch, resolveSelect }) => {
    const {
      rest_base: restBase,
      rest_namespace: restNamespace = "wp/v2",
      supports
    } = await resolveSelect.getPostType(postType);
    if (!supports?.autosave) {
      return;
    }
    const autosaves2 = await (0, import_api_fetch8.default)({
      path: `/${restNamespace}/${restBase}/${postId}/autosaves?context=edit`
    });
    if (autosaves2 && autosaves2.length) {
      dispatch.receiveAutosaves(postId, autosaves2);
    }
  };
  var getAutosave2 = (postType, postId) => async ({ resolveSelect }) => {
    await resolveSelect.getAutosaves(postType, postId);
  };
  var __experimentalGetCurrentGlobalStylesId2 = () => async ({ dispatch, resolveSelect }) => {
    const activeThemes = await resolveSelect.getEntityRecords(
      "root",
      "theme",
      { status: "active" }
    );
    const globalStylesURL = activeThemes?.[0]?._links?.["wp:user-global-styles"]?.[0]?.href;
    if (!globalStylesURL) {
      return;
    }
    const matches = globalStylesURL.match(/\/(\d+)(?:\?|$)/);
    const id2 = matches ? Number(matches[1]) : null;
    if (id2) {
      dispatch.__experimentalReceiveCurrentGlobalStylesId(id2);
    }
  };
  var __experimentalGetCurrentThemeBaseGlobalStyles2 = () => async ({ resolveSelect, dispatch }) => {
    const currentTheme2 = await resolveSelect.getCurrentTheme();
    const themeGlobalStyles = await (0, import_api_fetch8.default)({
      path: `/wp/v2/global-styles/themes/${currentTheme2.stylesheet}?context=view`
    });
    dispatch.__experimentalReceiveThemeBaseGlobalStyles(
      currentTheme2.stylesheet,
      themeGlobalStyles
    );
  };
  var __experimentalGetCurrentThemeGlobalStylesVariations2 = () => async ({ resolveSelect, dispatch }) => {
    const currentTheme2 = await resolveSelect.getCurrentTheme();
    const variations = await (0, import_api_fetch8.default)({
      path: `/wp/v2/global-styles/themes/${currentTheme2.stylesheet}/variations?context=view`
    });
    dispatch.__experimentalReceiveThemeGlobalStyleVariations(
      currentTheme2.stylesheet,
      variations
    );
  };
  var getCurrentThemeGlobalStylesRevisions2 = () => async ({ resolveSelect, dispatch }) => {
    const globalStylesId = await resolveSelect.__experimentalGetCurrentGlobalStylesId();
    const record = globalStylesId ? await resolveSelect.getEntityRecord(
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
      dispatch.receiveThemeGlobalStyleRevisions(
        globalStylesId,
        revisions
      );
    }
  };
  getCurrentThemeGlobalStylesRevisions2.shouldInvalidate = (action) => {
    return action.type === "SAVE_ENTITY_RECORD_FINISH" && action.kind === "root" && !action.error && action.name === "globalStyles";
  };
  var getBlockPatterns2 = () => async ({ dispatch }) => {
    const patterns = await fetchBlockPatterns();
    dispatch({ type: "RECEIVE_BLOCK_PATTERNS", patterns });
  };
  var getBlockPatternCategories2 = () => async ({ dispatch }) => {
    const categories = await (0, import_api_fetch8.default)({
      path: "/wp/v2/block-patterns/categories"
    });
    dispatch({ type: "RECEIVE_BLOCK_PATTERN_CATEGORIES", categories });
  };
  var getUserPatternCategories2 = () => async ({ dispatch, resolveSelect }) => {
    const patternCategories = await resolveSelect.getEntityRecords(
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
    dispatch({
      type: "RECEIVE_USER_PATTERN_CATEGORIES",
      patternCategories: mappedPatternCategories
    });
  };
  var getNavigationFallbackId2 = () => async ({ dispatch, select, registry }) => {
    const fallback = await (0, import_api_fetch8.default)({
      path: (0, import_url6.addQueryArgs)("/wp-block-editor/v1/navigation-fallback", {
        _embed: true
      })
    });
    const record = fallback?._embedded?.self;
    registry.batch(() => {
      dispatch.receiveNavigationFallbackId(fallback?.id);
      if (!record) {
        return;
      }
      const existingFallbackEntityRecord = select.getEntityRecord(
        "postType",
        "wp_navigation",
        fallback.id
      );
      const invalidateNavigationQueries = !existingFallbackEntityRecord;
      dispatch.receiveEntityRecords(
        "postType",
        "wp_navigation",
        record,
        void 0,
        invalidateNavigationQueries
      );
      dispatch.finishResolution("getEntityRecord", [
        "postType",
        "wp_navigation",
        fallback.id
      ]);
    });
  };
  var getDefaultTemplateId2 = (query) => async ({ dispatch, registry, resolveSelect }) => {
    const template = await (0, import_api_fetch8.default)({
      path: (0, import_url6.addQueryArgs)("/wp/v2/templates/lookup", query)
    });
    await resolveSelect.getEntitiesConfig("postType");
    const id2 = window?.__experimentalTemplateActivate ? template?.wp_id || template?.id : template?.id;
    if (id2) {
      template.id = id2;
      registry.batch(() => {
        dispatch.receiveDefaultTemplateId(query, id2);
        dispatch.receiveEntityRecords("postType", template.type, [
          template
        ]);
        dispatch.finishResolution("getEntityRecord", [
          "postType",
          template.type,
          id2
        ]);
      });
    }
  };
  getDefaultTemplateId2.shouldInvalidate = (action) => {
    return action.type === "RECEIVE_ITEMS" && action.kind === "root" && action.name === "site";
  };
  var getRevisions2 = (kind, name, recordKey, query = {}) => async ({ dispatch, registry, resolveSelect }) => {
    const configs = await resolveSelect.getEntitiesConfig(kind);
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
        dispatch.receiveRevisions(
          kind,
          name,
          recordKey,
          records,
          query,
          false,
          meta
        );
        if (!query?._fields && !query.context) {
          const key = entityConfig.key || DEFAULT_ENTITY_KEY;
          const resolutionsArgs = records.filter((record) => record[key]).map((record) => [
            kind,
            name,
            recordKey,
            record[key]
          ]);
          dispatch.finishResolutions(
            "getRevision",
            resolutionsArgs
          );
        }
      });
    }
  };
  getRevisions2.shouldInvalidate = (action, kind, name, recordKey) => action.type === "SAVE_ENTITY_RECORD_FINISH" && name === action.name && kind === action.kind && !action.error && recordKey === action.recordId;
  var getRevision2 = (kind, name, recordKey, revisionKey, query) => async ({ dispatch, resolveSelect }) => {
    const configs = await resolveSelect.getEntitiesConfig(kind);
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
      dispatch.receiveRevisions(kind, name, recordKey, record, query);
    }
  };
  var getRegisteredPostMeta2 = (postType) => async ({ dispatch, resolveSelect }) => {
    let options;
    try {
      const {
        rest_namespace: restNamespace = "wp/v2",
        rest_base: restBase
      } = await resolveSelect.getPostType(postType) || {};
      options = await (0, import_api_fetch8.default)({
        path: `${restNamespace}/${restBase}/?context=edit`,
        method: "OPTIONS"
      });
    } catch (error) {
      return;
    }
    if (options) {
      dispatch.receiveRegisteredPostMeta(
        postType,
        options?.schema?.properties?.meta?.properties
      );
    }
  };
  var getEntitiesConfig2 = (kind) => async ({ dispatch }) => {
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
      dispatch.addEntities(configs);
    } catch {
    }
  };
  var getEditorSettings2 = () => async ({ dispatch }) => {
    const settings = await (0, import_api_fetch8.default)({
      path: "/wp-block-editor/v1/settings"
    });
    dispatch.receiveEditorSettings(settings);
  };
  var getEditorAssets2 = () => async ({ dispatch }) => {
    const assets = await (0, import_api_fetch8.default)({
      path: "/wp-block-editor/v1/assets"
    });
    dispatch.receiveEditorAssets(assets);
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
  function EntityProvider({ kind, type: name, id: id2, children }) {
    const parent = (0, import_element2.useContext)(EntityContext);
    const childContext = (0, import_element2.useMemo)(
      () => ({
        ...parent,
        [kind]: {
          ...parent?.[kind],
          [name]: id2
        }
      }),
      [parent, kind, name, id2]
    );
    return /* @__PURE__ */ (0, import_jsx_runtime.jsx)(EntityContext.Provider, { value: childContext, children });
  }

  // packages/core-data/build-module/hooks/use-entity-record.mjs
  var import_data7 = __toESM(require_data(), 1);
  var import_deprecated4 = __toESM(require_deprecated(), 1);
  var import_element3 = __toESM(require_element(), 1);

  // packages/core-data/build-module/hooks/use-query-select.mjs
  var import_data6 = __toESM(require_data(), 1);

  // node_modules/memize/dist/index.js
  function memize(fn, options) {
    var size2 = 0;
    var head;
    var tail;
    options = options || {};
    function memoized() {
      var node = head, len = arguments.length, args2, i;
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
      args2 = new Array(len);
      for (i = 0; i < len; i++) {
        args2[i] = arguments[i];
      }
      node = {
        args: args2,
        // Generate the result from original function
        val: fn.apply(null, args2)
      };
      if (head) {
        head.prev = node;
        node.next = head;
      } else {
        tail = node;
      }
      if (size2 === /** @type {MemizeOptions} */
      options.maxSize) {
        tail = /** @type {MemizeCacheNode} */
        tail.prev;
        tail.next = null;
      } else {
        size2++;
      }
      head = node;
      return node.val;
    }
    memoized.clear = function() {
      head = null;
      tail = null;
      size2 = 0;
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
    return (0, import_data6.useSelect)((select, registry) => {
      const resolve = (store2) => enrichSelectors(select(store2));
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
        get: () => (...args2) => {
          const data = selectors[selectorName](...args2);
          const resolutionStatus = selectors.getResolutionState(
            selectorName,
            args2
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
    const { editEntityRecord: editEntityRecord2, saveEditedEntityRecord: saveEditedEntityRecord2 } = (0, import_data7.useDispatch)(store);
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
    const { editedRecord, hasEdits, edits } = (0, import_data7.useSelect)(
      (select) => {
        if (!options.enabled) {
          return {
            editedRecord: EMPTY_OBJECT2,
            hasEdits: false,
            edits: EMPTY_OBJECT2
          };
        }
        return {
          editedRecord: select(store).getEditedEntityRecord(
            kind,
            name,
            recordId
          ),
          hasEdits: select(store).hasEditsForEntityRecord(
            kind,
            name,
            recordId
          ),
          edits: select(store).getEntityRecordNonTransientEdits(
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
  var import_data8 = __toESM(require_data(), 1);
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
    const { totalItems, totalPages } = (0, import_data8.useSelect)(
      (select) => {
        if (!options.enabled) {
          return {
            totalItems: null,
            totalPages: null
          };
        }
        return {
          totalItems: select(store).getEntityRecordsTotalItems(
            kind,
            name,
            queryArgs
          ),
          totalPages: select(store).getEntityRecordsTotalPages(
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
    const entityConfig = (0, import_data8.useSelect)(
      (select) => select(store).getEntityConfig(kind, name),
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
    const permissions = (0, import_data8.useSelect)(
      (select) => {
        const { getEntityRecordsPermissions: getEntityRecordsPermissions2 } = unlock(
          select(store)
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
  function useResourcePermissions(resource, id2) {
    const isEntity = typeof resource === "object";
    const resourceAsString = isEntity ? JSON.stringify(resource) : resource;
    if (isEntity && typeof id2 !== "undefined") {
      (0, import_warning.default)(
        `When 'resource' is an entity object, passing 'id' as a separate argument isn't supported.`
      );
    }
    return useQuerySelect(
      (resolve) => {
        const hasId = isEntity ? !!resource.id : !!id2;
        const { canUser: canUser3 } = resolve(store);
        const create8 = canUser3(
          "create",
          isEntity ? { kind: resource.kind, name: resource.name } : resource
        );
        if (!hasId) {
          const read2 = canUser3("read", resource);
          const isResolving2 = create8.isResolving || read2.isResolving;
          const hasResolved2 = create8.hasResolved && read2.hasResolved;
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
            canCreate: create8.hasResolved && create8.data,
            canRead: read2.hasResolved && read2.data
          };
        }
        const read = canUser3("read", resource, id2);
        const update = canUser3("update", resource, id2);
        const _delete = canUser3("delete", resource, id2);
        const isResolving = read.isResolving || create8.isResolving || update.isResolving || _delete.isResolving;
        const hasResolved = read.hasResolved && create8.hasResolved && update.hasResolved && _delete.hasResolved;
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
          canCreate: hasResolved && create8.data,
          canUpdate: hasResolved && update.data,
          canDelete: hasResolved && _delete.data
        };
      },
      [resourceAsString, id2]
    );
  }
  var use_resource_permissions_default = useResourcePermissions;
  function __experimentalUseResourcePermissions(resource, id2) {
    (0, import_deprecated6.default)(`wp.data.__experimentalUseResourcePermissions`, {
      alternative: "wp.data.useResourcePermissions",
      since: "6.1"
    });
    return useResourcePermissions(resource, id2);
  }

  // packages/core-data/build-module/hooks/use-entity-block-editor.mjs
  var import_element6 = __toESM(require_element(), 1);
  var import_data9 = __toESM(require_data(), 1);
  var import_blocks2 = __toESM(require_blocks(), 1);

  // packages/core-data/build-module/hooks/use-entity-id.mjs
  var import_element5 = __toESM(require_element(), 1);
  function useEntityId(kind, name) {
    const context = (0, import_element5.useContext)(EntityContext);
    return context?.[kind]?.[name];
  }

  // packages/core-data/build-module/footnotes/index.mjs
  var import_rich_text = __toESM(require_rich_text(), 1);

  // packages/core-data/build-module/footnotes/get-rich-text-values-cached.mjs
  var import_block_editor = __toESM(require_block_editor(), 1);
  var unlockedApis;
  var cache = /* @__PURE__ */ new WeakMap();
  function getRichTextValuesCached(block) {
    if (!unlockedApis) {
      unlockedApis = unlock(import_block_editor.privateApis);
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
        if (typeof value !== "string" && !(value instanceof import_rich_text.RichTextData)) {
          continue;
        }
        const richTextValue = typeof value === "string" ? import_rich_text.RichTextData.fromHTMLString(value) : new import_rich_text.RichTextData(value);
        let hasFootnotes = false;
        richTextValue.replacements.forEach((replacement) => {
          if (replacement.type === "core/footnote") {
            const id2 = replacement.attributes["data-fn"];
            const index = newOrder.indexOf(id2);
            const countValue = (0, import_rich_text.create)({
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
            replacement.innerHTML = (0, import_rich_text.toHTMLString)({
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
  var parsedBlocksCache = /* @__PURE__ */ new WeakMap();
  function useEntityBlockEditor(kind, name, { id: _id } = {}) {
    const providerId = useEntityId(kind, name);
    const id2 = _id ?? providerId;
    const { getEntityRecord: getEntityRecord3, getEntityRecordEdits: getEntityRecordEdits2 } = (0, import_data9.useSelect)(STORE_NAME);
    const { content, editedBlocks, meta } = (0, import_data9.useSelect)(
      (select) => {
        if (!id2) {
          return {};
        }
        const { getEditedEntityRecord: getEditedEntityRecord3 } = select(STORE_NAME);
        const editedRecord = getEditedEntityRecord3(kind, name, id2);
        return {
          editedBlocks: editedRecord.blocks,
          content: editedRecord.content,
          meta: editedRecord.meta
        };
      },
      [kind, name, id2]
    );
    const { __unstableCreateUndoLevel: __unstableCreateUndoLevel2, editEntityRecord: editEntityRecord2 } = (0, import_data9.useDispatch)(STORE_NAME);
    const blocks = (0, import_element6.useMemo)(() => {
      if (!id2) {
        return void 0;
      }
      if (editedBlocks) {
        return editedBlocks;
      }
      if (!content || typeof content !== "string") {
        return EMPTY_ARRAY2;
      }
      const edits = getEntityRecordEdits2(kind, name, id2);
      const isUnedited = !edits || !Object.keys(edits).length;
      const cackeKey = isUnedited ? getEntityRecord3(kind, name, id2) : edits;
      let _blocks = parsedBlocksCache.get(cackeKey);
      if (!_blocks) {
        _blocks = (0, import_blocks2.parse)(content);
        parsedBlocksCache.set(cackeKey, _blocks);
      }
      return _blocks;
    }, [
      kind,
      name,
      id2,
      editedBlocks,
      content,
      getEntityRecord3,
      getEntityRecordEdits2
    ]);
    const onChange = (0, import_element6.useCallback)(
      (newBlocks, options) => {
        const noChange = blocks === newBlocks;
        if (noChange) {
          return __unstableCreateUndoLevel2(kind, name, id2);
        }
        const { selection, ...rest } = options;
        const edits = {
          selection,
          content: ({ blocks: blocksForSerialization = [] }) => (0, import_blocks2.__unstableSerializeAndClean)(blocksForSerialization),
          ...updateFootnotesFromMeta(newBlocks, meta)
        };
        editEntityRecord2(kind, name, id2, edits, {
          isCached: false,
          ...rest
        });
      },
      [
        kind,
        name,
        id2,
        blocks,
        meta,
        __unstableCreateUndoLevel2,
        editEntityRecord2
      ]
    );
    const onInput = (0, import_element6.useCallback)(
      (newBlocks, options) => {
        const { selection, ...rest } = options;
        const footnotesChanges = updateFootnotesFromMeta(newBlocks, meta);
        const edits = { selection, ...footnotesChanges };
        editEntityRecord2(kind, name, id2, edits, {
          isCached: true,
          ...rest
        });
      },
      [kind, name, id2, meta, editEntityRecord2]
    );
    return [blocks, onInput, onChange];
  }

  // packages/core-data/build-module/hooks/use-entity-prop.mjs
  var import_element7 = __toESM(require_element(), 1);
  var import_data10 = __toESM(require_data(), 1);
  function useEntityProp(kind, name, prop, _id) {
    const providerId = useEntityId(kind, name);
    const id2 = _id ?? providerId;
    const { value, fullValue } = (0, import_data10.useSelect)(
      (select) => {
        const { getEntityRecord: getEntityRecord3, getEditedEntityRecord: getEditedEntityRecord3 } = select(STORE_NAME);
        const record = getEntityRecord3(kind, name, id2);
        const editedRecord = getEditedEntityRecord3(kind, name, id2);
        return record && editedRecord ? {
          value: editedRecord[prop],
          fullValue: record[prop]
        } : {};
      },
      [kind, name, id2, prop]
    );
    const { editEntityRecord: editEntityRecord2 } = (0, import_data10.useDispatch)(STORE_NAME);
    const setValue = (0, import_element7.useCallback)(
      (newValue) => {
        editEntityRecord2(kind, name, id2, {
          [prop]: newValue
        });
      },
      [editEntityRecord2, kind, name, id2, prop]
    );
    return [value, setValue, fullValue];
  }

  // packages/core-data/build-module/hooks/use-post-editor-awareness-state.mjs
  var import_element8 = __toESM(require_element(), 1);
  var defaultState = {
    activeUsers: [],
    getAbsolutePositionIndex: () => null,
    getDebugData: () => ({
      doc: {},
      clients: {},
      userMap: {}
    }),
    isCurrentUserDisconnected: false
  };
  function getAwarenessState(awareness, newState) {
    const activeUsers = newState ?? awareness.getCurrentState();
    return {
      activeUsers,
      getAbsolutePositionIndex: (selection) => awareness.getAbsolutePositionIndex(selection),
      getDebugData: () => awareness.getDebugData(),
      isCurrentUserDisconnected: activeUsers.find((user) => user.isMe)?.isConnected === false
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
    return usePostEditorAwarenessState(postId, postType).activeUsers;
  }

  // packages/core-data/build-module/private-apis.mjs
  var privateApis = {};
  lock(privateApis, {
    useEntityRecordsWithPermissions,
    RECEIVE_INTERMEDIATE_RESULTS,
    useActiveCollaborators
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
      result[getEntityRecordsMethodName] = (...args2) => {
        logEntityDeprecation(kind, plural, getEntityRecordsMethodName, {
          isShorthandSelector: true,
          alternativeFunctionName: "getEntityRecords"
        });
        return getEntityRecords2(kind, name, ...args2);
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
  var store = (0, import_data11.createReduxStore)(STORE_NAME, storeConfig());
  unlock(store).registerPrivateSelectors(private_selectors_exports);
  unlock(store).registerPrivateActions(private_actions_exports);
  (0, import_data11.register)(store);
  return __toCommonJS(index_exports);
})();