/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ 287:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   fL: () => (/* binding */ pascalCase),
/* harmony export */   l3: () => (/* binding */ pascalCaseTransform)
/* harmony export */ });
/* unused harmony export pascalCaseTransformMerge */
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(1635);
/* harmony import */ var no_case__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(2226);


function pascalCaseTransform(input, index) {
    var firstChar = input.charAt(0);
    var lowerChars = input.substr(1).toLowerCase();
    if (index > 0 && firstChar >= "0" && firstChar <= "9") {
        return "_" + firstChar + lowerChars;
    }
    return "" + firstChar.toUpperCase() + lowerChars;
}
function pascalCaseTransformMerge(input) {
    return input.charAt(0).toUpperCase() + input.slice(1).toLowerCase();
}
function pascalCase(input, options) {
    if (options === void 0) { options = {}; }
    return (0,no_case__WEBPACK_IMPORTED_MODULE_0__/* .noCase */ .W)(input, (0,tslib__WEBPACK_IMPORTED_MODULE_1__/* .__assign */ .Cl)({ delimiter: "", transform: pascalCaseTransform }, options));
}


/***/ }),

/***/ 533:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ get_normalized_comma_separable_default)
/* harmony export */ });
function getNormalizedCommaSeparable(value) {
  if (typeof value === "string") {
    return value.split(",");
  } else if (Array.isArray(value)) {
    return value;
  }
  return null;
}
var get_normalized_comma_separable_default = getNormalizedCommaSeparable;



/***/ }),

/***/ 644:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  qh: () => (/* reexport */ __experimentalUseEntityRecord),
  bM: () => (/* reexport */ use_entity_records/* __experimentalUseEntityRecords */.bM),
  _: () => (/* reexport */ __experimentalUseResourcePermissions),
  hg: () => (/* reexport */ useEntityBlockEditor),
  mV: () => (/* reexport */ useEntityId),
  S$: () => (/* reexport */ useEntityProp),
  MA: () => (/* reexport */ useEntityRecord),
  $u: () => (/* reexport */ use_entity_records/* default */.Ay),
  qs: () => (/* reexport */ use_resource_permissions_default)
});

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__(7143);
// EXTERNAL MODULE: external ["wp","deprecated"]
var external_wp_deprecated_ = __webpack_require__(4040);
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_);
// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__(6087);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/hooks/use-query-select.js + 2 modules
var use_query_select = __webpack_require__(7541);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/index.js
var build_module = __webpack_require__(4565);
;// ./node_modules/@wordpress/core-data/build-module/hooks/use-entity-record.js





const EMPTY_OBJECT = {};
function useEntityRecord(kind, name, recordId, options = { enabled: true }) {
  const { editEntityRecord, saveEditedEntityRecord } = (0,external_wp_data_.useDispatch)(build_module.store);
  const mutations = (0,external_wp_element_.useMemo)(
    () => ({
      edit: (record2, editOptions = {}) => editEntityRecord(kind, name, recordId, record2, editOptions),
      save: (saveOptions = {}) => saveEditedEntityRecord(kind, name, recordId, {
        throwOnError: true,
        ...saveOptions
      })
    }),
    [editEntityRecord, kind, name, recordId, saveEditedEntityRecord]
  );
  const { editedRecord, hasEdits, edits } = (0,external_wp_data_.useSelect)(
    (select) => {
      if (!options.enabled) {
        return {
          editedRecord: EMPTY_OBJECT,
          hasEdits: false,
          edits: EMPTY_OBJECT
        };
      }
      return {
        editedRecord: select(build_module.store).getEditedEntityRecord(
          kind,
          name,
          recordId
        ),
        hasEdits: select(build_module.store).hasEditsForEntityRecord(
          kind,
          name,
          recordId
        ),
        edits: select(build_module.store).getEntityRecordNonTransientEdits(
          kind,
          name,
          recordId
        )
      };
    },
    [kind, name, recordId, options.enabled]
  );
  const { data: record, ...querySelectRest } = (0,use_query_select/* default */.A)(
    (query) => {
      if (!options.enabled) {
        return {
          data: null
        };
      }
      return query(build_module.store).getEntityRecord(kind, name, recordId);
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
  external_wp_deprecated_default()(`wp.data.__experimentalUseEntityRecord`, {
    alternative: "wp.data.useEntityRecord",
    since: "6.1"
  });
  return useEntityRecord(kind, name, recordId, options);
}


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/hooks/use-entity-records.js
var use_entity_records = __webpack_require__(7078);
;// external ["wp","warning"]
const external_wp_warning_namespaceObject = window["wp"]["warning"];
var external_wp_warning_default = /*#__PURE__*/__webpack_require__.n(external_wp_warning_namespaceObject);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/hooks/constants.js
var constants = __webpack_require__(2859);
;// ./node_modules/@wordpress/core-data/build-module/hooks/use-resource-permissions.js





function useResourcePermissions(resource, id) {
  const isEntity = typeof resource === "object";
  const resourceAsString = isEntity ? JSON.stringify(resource) : resource;
  if (isEntity && typeof id !== "undefined") {
    external_wp_warning_default()(
      `When 'resource' is an entity object, passing 'id' as a separate argument isn't supported.`
    );
  }
  return (0,use_query_select/* default */.A)(
    (resolve) => {
      const hasId = isEntity ? !!resource.id : !!id;
      const { canUser } = resolve(build_module.store);
      const create = canUser(
        "create",
        isEntity ? { kind: resource.kind, name: resource.name } : resource
      );
      if (!hasId) {
        const read2 = canUser("read", resource);
        const isResolving2 = create.isResolving || read2.isResolving;
        const hasResolved2 = create.hasResolved && read2.hasResolved;
        let status2 = constants/* Status */.n.Idle;
        if (isResolving2) {
          status2 = constants/* Status */.n.Resolving;
        } else if (hasResolved2) {
          status2 = constants/* Status */.n.Success;
        }
        return {
          status: status2,
          isResolving: isResolving2,
          hasResolved: hasResolved2,
          canCreate: create.hasResolved && create.data,
          canRead: read2.hasResolved && read2.data
        };
      }
      const read = canUser("read", resource, id);
      const update = canUser("update", resource, id);
      const _delete = canUser("delete", resource, id);
      const isResolving = read.isResolving || create.isResolving || update.isResolving || _delete.isResolving;
      const hasResolved = read.hasResolved && create.hasResolved && update.hasResolved && _delete.hasResolved;
      let status = constants/* Status */.n.Idle;
      if (isResolving) {
        status = constants/* Status */.n.Resolving;
      } else if (hasResolved) {
        status = constants/* Status */.n.Success;
      }
      return {
        status,
        isResolving,
        hasResolved,
        canRead: hasResolved && read.data,
        canCreate: hasResolved && create.data,
        canUpdate: hasResolved && update.data,
        canDelete: hasResolved && _delete.data
      };
    },
    [resourceAsString, id]
  );
}
var use_resource_permissions_default = useResourcePermissions;
function __experimentalUseResourcePermissions(resource, id) {
  external_wp_deprecated_default()(`wp.data.__experimentalUseResourcePermissions`, {
    alternative: "wp.data.useResourcePermissions",
    since: "6.1"
  });
  return useResourcePermissions(resource, id);
}


// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__(4997);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/name.js
var build_module_name = __webpack_require__(2278);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/entity-context.js
var entity_context = __webpack_require__(8843);
;// ./node_modules/@wordpress/core-data/build-module/hooks/use-entity-id.js


function useEntityId(kind, name) {
  const context = (0,external_wp_element_.useContext)(entity_context/* EntityContext */.D);
  return context?.[kind]?.[name];
}


// EXTERNAL MODULE: external ["wp","richText"]
var external_wp_richText_ = __webpack_require__(876);
;// external ["wp","blockEditor"]
const external_wp_blockEditor_namespaceObject = window["wp"]["blockEditor"];
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/lock-unlock.js + 1 modules
var lock_unlock = __webpack_require__(6378);
;// ./node_modules/@wordpress/core-data/build-module/footnotes/get-rich-text-values-cached.js


let unlockedApis;
const cache = /* @__PURE__ */ new WeakMap();
function getRichTextValuesCached(block) {
  if (!unlockedApis) {
    unlockedApis = (0,lock_unlock/* unlock */.T)(external_wp_blockEditor_namespaceObject.privateApis);
  }
  if (!cache.has(block)) {
    const values = unlockedApis.getRichTextValues([block]);
    cache.set(block, values);
  }
  return cache.get(block);
}


;// ./node_modules/@wordpress/core-data/build-module/footnotes/get-footnotes-order.js

const get_footnotes_order_cache = /* @__PURE__ */ new WeakMap();
function getBlockFootnotesOrder(block) {
  if (!get_footnotes_order_cache.has(block)) {
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
    get_footnotes_order_cache.set(block, order);
  }
  return get_footnotes_order_cache.get(block);
}
function getFootnotesOrder(blocks) {
  return blocks.flatMap(getBlockFootnotesOrder);
}


;// ./node_modules/@wordpress/core-data/build-module/footnotes/index.js


let oldFootnotes = {};
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
      if (typeof value !== "string" && !(value instanceof external_wp_richText_.RichTextData)) {
        continue;
      }
      const richTextValue = typeof value === "string" ? external_wp_richText_.RichTextData.fromHTMLString(value) : new external_wp_richText_.RichTextData(value);
      let hasFootnotes = false;
      richTextValue.replacements.forEach((replacement) => {
        if (replacement.type === "core/footnote") {
          const id = replacement.attributes["data-fn"];
          const index = newOrder.indexOf(id);
          const countValue = (0,external_wp_richText_.create)({
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
          replacement.innerHTML = (0,external_wp_richText_.toHTMLString)({
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


;// ./node_modules/@wordpress/core-data/build-module/hooks/use-entity-block-editor.js






const EMPTY_ARRAY = [];
const parsedBlocksCache = /* @__PURE__ */ new WeakMap();
function useEntityBlockEditor(kind, name, { id: _id } = {}) {
  const providerId = useEntityId(kind, name);
  const id = _id ?? providerId;
  const { getEntityRecord, getEntityRecordEdits } = (0,external_wp_data_.useSelect)(build_module_name/* STORE_NAME */.E);
  const { content, editedBlocks, meta } = (0,external_wp_data_.useSelect)(
    (select) => {
      if (!id) {
        return {};
      }
      const { getEditedEntityRecord } = select(build_module_name/* STORE_NAME */.E);
      const editedRecord = getEditedEntityRecord(kind, name, id);
      return {
        editedBlocks: editedRecord.blocks,
        content: editedRecord.content,
        meta: editedRecord.meta
      };
    },
    [kind, name, id]
  );
  const { __unstableCreateUndoLevel, editEntityRecord } = (0,external_wp_data_.useDispatch)(build_module_name/* STORE_NAME */.E);
  const blocks = (0,external_wp_element_.useMemo)(() => {
    if (!id) {
      return void 0;
    }
    if (editedBlocks) {
      return editedBlocks;
    }
    if (!content || typeof content !== "string") {
      return EMPTY_ARRAY;
    }
    const edits = getEntityRecordEdits(kind, name, id);
    const isUnedited = !edits || !Object.keys(edits).length;
    const cackeKey = isUnedited ? getEntityRecord(kind, name, id) : edits;
    let _blocks = parsedBlocksCache.get(cackeKey);
    if (!_blocks) {
      _blocks = (0,external_wp_blocks_.parse)(content);
      parsedBlocksCache.set(cackeKey, _blocks);
    }
    return _blocks;
  }, [
    kind,
    name,
    id,
    editedBlocks,
    content,
    getEntityRecord,
    getEntityRecordEdits
  ]);
  const onChange = (0,external_wp_element_.useCallback)(
    (newBlocks, options) => {
      const noChange = blocks === newBlocks;
      if (noChange) {
        return __unstableCreateUndoLevel(kind, name, id);
      }
      const { selection, ...rest } = options;
      const edits = {
        selection,
        content: ({ blocks: blocksForSerialization = [] }) => (0,external_wp_blocks_.__unstableSerializeAndClean)(blocksForSerialization),
        ...updateFootnotesFromMeta(newBlocks, meta)
      };
      editEntityRecord(kind, name, id, edits, {
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
      __unstableCreateUndoLevel,
      editEntityRecord
    ]
  );
  const onInput = (0,external_wp_element_.useCallback)(
    (newBlocks, options) => {
      const { selection, ...rest } = options;
      const footnotesChanges = updateFootnotesFromMeta(newBlocks, meta);
      const edits = { selection, ...footnotesChanges };
      editEntityRecord(kind, name, id, edits, {
        isCached: true,
        ...rest
      });
    },
    [kind, name, id, meta, editEntityRecord]
  );
  return [blocks, onInput, onChange];
}


;// ./node_modules/@wordpress/core-data/build-module/hooks/use-entity-prop.js




function useEntityProp(kind, name, prop, _id) {
  const providerId = useEntityId(kind, name);
  const id = _id ?? providerId;
  const { value, fullValue } = (0,external_wp_data_.useSelect)(
    (select) => {
      const { getEntityRecord, getEditedEntityRecord } = select(build_module_name/* STORE_NAME */.E);
      const record = getEntityRecord(kind, name, id);
      const editedRecord = getEditedEntityRecord(kind, name, id);
      return record && editedRecord ? {
        value: editedRecord[prop],
        fullValue: record[prop]
      } : {};
    },
    [kind, name, id, prop]
  );
  const { editEntityRecord } = (0,external_wp_data_.useDispatch)(build_module_name/* STORE_NAME */.E);
  const setValue = (0,external_wp_element_.useCallback)(
    (newValue) => {
      editEntityRecord(kind, name, id, {
        [prop]: newValue
      });
    },
    [editEntityRecord, kind, name, id, prop]
  );
  return [value, setValue, fullValue];
}


;// ./node_modules/@wordpress/core-data/build-module/hooks/index.js









/***/ }),

/***/ 876:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["richText"];

/***/ }),

/***/ 1455:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["apiFetch"];

/***/ }),

/***/ 1569:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  A: () => (/* binding */ esm_browser_v4)
});

;// ./node_modules/@wordpress/core-data/node_modules/uuid/dist/esm-browser/native.js
const randomUUID = typeof crypto !== 'undefined' && crypto.randomUUID && crypto.randomUUID.bind(crypto);
/* harmony default export */ const esm_browser_native = ({
  randomUUID
});
;// ./node_modules/@wordpress/core-data/node_modules/uuid/dist/esm-browser/rng.js
// Unique ID creation requires a high quality random # generator. In the browser we therefore
// require the crypto API and do not support built-in fallback to lower quality random number
// generators (like Math.random()).
let getRandomValues;
const rnds8 = new Uint8Array(16);
function rng() {
  // lazy load so that environments that need to polyfill have a chance to do so
  if (!getRandomValues) {
    // getRandomValues needs to be invoked in a context where "this" is a Crypto implementation.
    getRandomValues = typeof crypto !== 'undefined' && crypto.getRandomValues && crypto.getRandomValues.bind(crypto);

    if (!getRandomValues) {
      throw new Error('crypto.getRandomValues() not supported. See https://github.com/uuidjs/uuid#getrandomvalues-not-supported');
    }
  }

  return getRandomValues(rnds8);
}
;// ./node_modules/@wordpress/core-data/node_modules/uuid/dist/esm-browser/stringify.js

/**
 * Convert array of 16 byte values to UUID string format of the form:
 * XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
 */

const byteToHex = [];

for (let i = 0; i < 256; ++i) {
  byteToHex.push((i + 0x100).toString(16).slice(1));
}

function unsafeStringify(arr, offset = 0) {
  // Note: Be careful editing this code!  It's been tuned for performance
  // and works in ways you may not expect. See https://github.com/uuidjs/uuid/pull/434
  return byteToHex[arr[offset + 0]] + byteToHex[arr[offset + 1]] + byteToHex[arr[offset + 2]] + byteToHex[arr[offset + 3]] + '-' + byteToHex[arr[offset + 4]] + byteToHex[arr[offset + 5]] + '-' + byteToHex[arr[offset + 6]] + byteToHex[arr[offset + 7]] + '-' + byteToHex[arr[offset + 8]] + byteToHex[arr[offset + 9]] + '-' + byteToHex[arr[offset + 10]] + byteToHex[arr[offset + 11]] + byteToHex[arr[offset + 12]] + byteToHex[arr[offset + 13]] + byteToHex[arr[offset + 14]] + byteToHex[arr[offset + 15]];
}

function stringify(arr, offset = 0) {
  const uuid = unsafeStringify(arr, offset); // Consistency check for valid UUID.  If this throws, it's likely due to one
  // of the following:
  // - One or more input array values don't map to a hex octet (leading to
  // "undefined" in the uuid)
  // - Invalid input values for the RFC `version` or `variant` fields

  if (!validate(uuid)) {
    throw TypeError('Stringified UUID is invalid');
  }

  return uuid;
}

/* harmony default export */ const esm_browser_stringify = ((/* unused pure expression or super */ null && (stringify)));
;// ./node_modules/@wordpress/core-data/node_modules/uuid/dist/esm-browser/v4.js




function v4(options, buf, offset) {
  if (esm_browser_native.randomUUID && !buf && !options) {
    return esm_browser_native.randomUUID();
  }

  options = options || {};
  const rnds = options.random || (options.rng || rng)(); // Per 4.4, set bits for version and `clock_seq_hi_and_reserved`

  rnds[6] = rnds[6] & 0x0f | 0x40;
  rnds[8] = rnds[8] & 0x3f | 0x80; // Copy bytes to buffer, if provided

  if (buf) {
    offset = offset || 0;

    for (let i = 0; i < 16; ++i) {
      buf[offset + i] = rnds[i];
    }

    return buf;
  }

  return unsafeStringify(rnds);
}

/* harmony default export */ const esm_browser_v4 = (v4);

/***/ }),

/***/ 1635:
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Cl: () => (/* binding */ __assign)
/* harmony export */ });
/* unused harmony exports __extends, __rest, __decorate, __param, __esDecorate, __runInitializers, __propKey, __setFunctionName, __metadata, __awaiter, __generator, __createBinding, __exportStar, __values, __read, __spread, __spreadArrays, __spreadArray, __await, __asyncGenerator, __asyncDelegator, __asyncValues, __makeTemplateObject, __importStar, __importDefault, __classPrivateFieldGet, __classPrivateFieldSet, __classPrivateFieldIn, __addDisposableResource, __disposeResources, __rewriteRelativeImportExtension */
/******************************************************************************
Copyright (c) Microsoft Corporation.

Permission to use, copy, modify, and/or distribute this software for any
purpose with or without fee is hereby granted.

THE SOFTWARE IS PROVIDED "AS IS" AND THE AUTHOR DISCLAIMS ALL WARRANTIES WITH
REGARD TO THIS SOFTWARE INCLUDING ALL IMPLIED WARRANTIES OF MERCHANTABILITY
AND FITNESS. IN NO EVENT SHALL THE AUTHOR BE LIABLE FOR ANY SPECIAL, DIRECT,
INDIRECT, OR CONSEQUENTIAL DAMAGES OR ANY DAMAGES WHATSOEVER RESULTING FROM
LOSS OF USE, DATA OR PROFITS, WHETHER IN AN ACTION OF CONTRACT, NEGLIGENCE OR
OTHER TORTIOUS ACTION, ARISING OUT OF OR IN CONNECTION WITH THE USE OR
PERFORMANCE OF THIS SOFTWARE.
***************************************************************************** */
/* global Reflect, Promise, SuppressedError, Symbol, Iterator */

var extendStatics = function(d, b) {
  extendStatics = Object.setPrototypeOf ||
      ({ __proto__: [] } instanceof Array && function (d, b) { d.__proto__ = b; }) ||
      function (d, b) { for (var p in b) if (Object.prototype.hasOwnProperty.call(b, p)) d[p] = b[p]; };
  return extendStatics(d, b);
};

function __extends(d, b) {
  if (typeof b !== "function" && b !== null)
      throw new TypeError("Class extends value " + String(b) + " is not a constructor or null");
  extendStatics(d, b);
  function __() { this.constructor = d; }
  d.prototype = b === null ? Object.create(b) : (__.prototype = b.prototype, new __());
}

var __assign = function() {
  __assign = Object.assign || function __assign(t) {
      for (var s, i = 1, n = arguments.length; i < n; i++) {
          s = arguments[i];
          for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p)) t[p] = s[p];
      }
      return t;
  }
  return __assign.apply(this, arguments);
}

function __rest(s, e) {
  var t = {};
  for (var p in s) if (Object.prototype.hasOwnProperty.call(s, p) && e.indexOf(p) < 0)
      t[p] = s[p];
  if (s != null && typeof Object.getOwnPropertySymbols === "function")
      for (var i = 0, p = Object.getOwnPropertySymbols(s); i < p.length; i++) {
          if (e.indexOf(p[i]) < 0 && Object.prototype.propertyIsEnumerable.call(s, p[i]))
              t[p[i]] = s[p[i]];
      }
  return t;
}

function __decorate(decorators, target, key, desc) {
  var c = arguments.length, r = c < 3 ? target : desc === null ? desc = Object.getOwnPropertyDescriptor(target, key) : desc, d;
  if (typeof Reflect === "object" && typeof Reflect.decorate === "function") r = Reflect.decorate(decorators, target, key, desc);
  else for (var i = decorators.length - 1; i >= 0; i--) if (d = decorators[i]) r = (c < 3 ? d(r) : c > 3 ? d(target, key, r) : d(target, key)) || r;
  return c > 3 && r && Object.defineProperty(target, key, r), r;
}

function __param(paramIndex, decorator) {
  return function (target, key) { decorator(target, key, paramIndex); }
}

function __esDecorate(ctor, descriptorIn, decorators, contextIn, initializers, extraInitializers) {
  function accept(f) { if (f !== void 0 && typeof f !== "function") throw new TypeError("Function expected"); return f; }
  var kind = contextIn.kind, key = kind === "getter" ? "get" : kind === "setter" ? "set" : "value";
  var target = !descriptorIn && ctor ? contextIn["static"] ? ctor : ctor.prototype : null;
  var descriptor = descriptorIn || (target ? Object.getOwnPropertyDescriptor(target, contextIn.name) : {});
  var _, done = false;
  for (var i = decorators.length - 1; i >= 0; i--) {
      var context = {};
      for (var p in contextIn) context[p] = p === "access" ? {} : contextIn[p];
      for (var p in contextIn.access) context.access[p] = contextIn.access[p];
      context.addInitializer = function (f) { if (done) throw new TypeError("Cannot add initializers after decoration has completed"); extraInitializers.push(accept(f || null)); };
      var result = (0, decorators[i])(kind === "accessor" ? { get: descriptor.get, set: descriptor.set } : descriptor[key], context);
      if (kind === "accessor") {
          if (result === void 0) continue;
          if (result === null || typeof result !== "object") throw new TypeError("Object expected");
          if (_ = accept(result.get)) descriptor.get = _;
          if (_ = accept(result.set)) descriptor.set = _;
          if (_ = accept(result.init)) initializers.unshift(_);
      }
      else if (_ = accept(result)) {
          if (kind === "field") initializers.unshift(_);
          else descriptor[key] = _;
      }
  }
  if (target) Object.defineProperty(target, contextIn.name, descriptor);
  done = true;
};

function __runInitializers(thisArg, initializers, value) {
  var useValue = arguments.length > 2;
  for (var i = 0; i < initializers.length; i++) {
      value = useValue ? initializers[i].call(thisArg, value) : initializers[i].call(thisArg);
  }
  return useValue ? value : void 0;
};

function __propKey(x) {
  return typeof x === "symbol" ? x : "".concat(x);
};

function __setFunctionName(f, name, prefix) {
  if (typeof name === "symbol") name = name.description ? "[".concat(name.description, "]") : "";
  return Object.defineProperty(f, "name", { configurable: true, value: prefix ? "".concat(prefix, " ", name) : name });
};

function __metadata(metadataKey, metadataValue) {
  if (typeof Reflect === "object" && typeof Reflect.metadata === "function") return Reflect.metadata(metadataKey, metadataValue);
}

function __awaiter(thisArg, _arguments, P, generator) {
  function adopt(value) { return value instanceof P ? value : new P(function (resolve) { resolve(value); }); }
  return new (P || (P = Promise))(function (resolve, reject) {
      function fulfilled(value) { try { step(generator.next(value)); } catch (e) { reject(e); } }
      function rejected(value) { try { step(generator["throw"](value)); } catch (e) { reject(e); } }
      function step(result) { result.done ? resolve(result.value) : adopt(result.value).then(fulfilled, rejected); }
      step((generator = generator.apply(thisArg, _arguments || [])).next());
  });
}

function __generator(thisArg, body) {
  var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g = Object.create((typeof Iterator === "function" ? Iterator : Object).prototype);
  return g.next = verb(0), g["throw"] = verb(1), g["return"] = verb(2), typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
  function verb(n) { return function (v) { return step([n, v]); }; }
  function step(op) {
      if (f) throw new TypeError("Generator is already executing.");
      while (g && (g = 0, op[0] && (_ = 0)), _) try {
          if (f = 1, y && (t = op[0] & 2 ? y["return"] : op[0] ? y["throw"] || ((t = y["return"]) && t.call(y), 0) : y.next) && !(t = t.call(y, op[1])).done) return t;
          if (y = 0, t) op = [op[0] & 2, t.value];
          switch (op[0]) {
              case 0: case 1: t = op; break;
              case 4: _.label++; return { value: op[1], done: false };
              case 5: _.label++; y = op[1]; op = [0]; continue;
              case 7: op = _.ops.pop(); _.trys.pop(); continue;
              default:
                  if (!(t = _.trys, t = t.length > 0 && t[t.length - 1]) && (op[0] === 6 || op[0] === 2)) { _ = 0; continue; }
                  if (op[0] === 3 && (!t || (op[1] > t[0] && op[1] < t[3]))) { _.label = op[1]; break; }
                  if (op[0] === 6 && _.label < t[1]) { _.label = t[1]; t = op; break; }
                  if (t && _.label < t[2]) { _.label = t[2]; _.ops.push(op); break; }
                  if (t[2]) _.ops.pop();
                  _.trys.pop(); continue;
          }
          op = body.call(thisArg, _);
      } catch (e) { op = [6, e]; y = 0; } finally { f = t = 0; }
      if (op[0] & 5) throw op[1]; return { value: op[0] ? op[1] : void 0, done: true };
  }
}

var __createBinding = Object.create ? (function(o, m, k, k2) {
  if (k2 === undefined) k2 = k;
  var desc = Object.getOwnPropertyDescriptor(m, k);
  if (!desc || ("get" in desc ? !m.__esModule : desc.writable || desc.configurable)) {
      desc = { enumerable: true, get: function() { return m[k]; } };
  }
  Object.defineProperty(o, k2, desc);
}) : (function(o, m, k, k2) {
  if (k2 === undefined) k2 = k;
  o[k2] = m[k];
});

function __exportStar(m, o) {
  for (var p in m) if (p !== "default" && !Object.prototype.hasOwnProperty.call(o, p)) __createBinding(o, m, p);
}

function __values(o) {
  var s = typeof Symbol === "function" && Symbol.iterator, m = s && o[s], i = 0;
  if (m) return m.call(o);
  if (o && typeof o.length === "number") return {
      next: function () {
          if (o && i >= o.length) o = void 0;
          return { value: o && o[i++], done: !o };
      }
  };
  throw new TypeError(s ? "Object is not iterable." : "Symbol.iterator is not defined.");
}

function __read(o, n) {
  var m = typeof Symbol === "function" && o[Symbol.iterator];
  if (!m) return o;
  var i = m.call(o), r, ar = [], e;
  try {
      while ((n === void 0 || n-- > 0) && !(r = i.next()).done) ar.push(r.value);
  }
  catch (error) { e = { error: error }; }
  finally {
      try {
          if (r && !r.done && (m = i["return"])) m.call(i);
      }
      finally { if (e) throw e.error; }
  }
  return ar;
}

/** @deprecated */
function __spread() {
  for (var ar = [], i = 0; i < arguments.length; i++)
      ar = ar.concat(__read(arguments[i]));
  return ar;
}

/** @deprecated */
function __spreadArrays() {
  for (var s = 0, i = 0, il = arguments.length; i < il; i++) s += arguments[i].length;
  for (var r = Array(s), k = 0, i = 0; i < il; i++)
      for (var a = arguments[i], j = 0, jl = a.length; j < jl; j++, k++)
          r[k] = a[j];
  return r;
}

function __spreadArray(to, from, pack) {
  if (pack || arguments.length === 2) for (var i = 0, l = from.length, ar; i < l; i++) {
      if (ar || !(i in from)) {
          if (!ar) ar = Array.prototype.slice.call(from, 0, i);
          ar[i] = from[i];
      }
  }
  return to.concat(ar || Array.prototype.slice.call(from));
}

function __await(v) {
  return this instanceof __await ? (this.v = v, this) : new __await(v);
}

function __asyncGenerator(thisArg, _arguments, generator) {
  if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
  var g = generator.apply(thisArg, _arguments || []), i, q = [];
  return i = Object.create((typeof AsyncIterator === "function" ? AsyncIterator : Object).prototype), verb("next"), verb("throw"), verb("return", awaitReturn), i[Symbol.asyncIterator] = function () { return this; }, i;
  function awaitReturn(f) { return function (v) { return Promise.resolve(v).then(f, reject); }; }
  function verb(n, f) { if (g[n]) { i[n] = function (v) { return new Promise(function (a, b) { q.push([n, v, a, b]) > 1 || resume(n, v); }); }; if (f) i[n] = f(i[n]); } }
  function resume(n, v) { try { step(g[n](v)); } catch (e) { settle(q[0][3], e); } }
  function step(r) { r.value instanceof __await ? Promise.resolve(r.value.v).then(fulfill, reject) : settle(q[0][2], r); }
  function fulfill(value) { resume("next", value); }
  function reject(value) { resume("throw", value); }
  function settle(f, v) { if (f(v), q.shift(), q.length) resume(q[0][0], q[0][1]); }
}

function __asyncDelegator(o) {
  var i, p;
  return i = {}, verb("next"), verb("throw", function (e) { throw e; }), verb("return"), i[Symbol.iterator] = function () { return this; }, i;
  function verb(n, f) { i[n] = o[n] ? function (v) { return (p = !p) ? { value: __await(o[n](v)), done: false } : f ? f(v) : v; } : f; }
}

function __asyncValues(o) {
  if (!Symbol.asyncIterator) throw new TypeError("Symbol.asyncIterator is not defined.");
  var m = o[Symbol.asyncIterator], i;
  return m ? m.call(o) : (o = typeof __values === "function" ? __values(o) : o[Symbol.iterator](), i = {}, verb("next"), verb("throw"), verb("return"), i[Symbol.asyncIterator] = function () { return this; }, i);
  function verb(n) { i[n] = o[n] && function (v) { return new Promise(function (resolve, reject) { v = o[n](v), settle(resolve, reject, v.done, v.value); }); }; }
  function settle(resolve, reject, d, v) { Promise.resolve(v).then(function(v) { resolve({ value: v, done: d }); }, reject); }
}

function __makeTemplateObject(cooked, raw) {
  if (Object.defineProperty) { Object.defineProperty(cooked, "raw", { value: raw }); } else { cooked.raw = raw; }
  return cooked;
};

var __setModuleDefault = Object.create ? (function(o, v) {
  Object.defineProperty(o, "default", { enumerable: true, value: v });
}) : function(o, v) {
  o["default"] = v;
};

var ownKeys = function(o) {
  ownKeys = Object.getOwnPropertyNames || function (o) {
    var ar = [];
    for (var k in o) if (Object.prototype.hasOwnProperty.call(o, k)) ar[ar.length] = k;
    return ar;
  };
  return ownKeys(o);
};

function __importStar(mod) {
  if (mod && mod.__esModule) return mod;
  var result = {};
  if (mod != null) for (var k = ownKeys(mod), i = 0; i < k.length; i++) if (k[i] !== "default") __createBinding(result, mod, k[i]);
  __setModuleDefault(result, mod);
  return result;
}

function __importDefault(mod) {
  return (mod && mod.__esModule) ? mod : { default: mod };
}

function __classPrivateFieldGet(receiver, state, kind, f) {
  if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a getter");
  if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot read private member from an object whose class did not declare it");
  return kind === "m" ? f : kind === "a" ? f.call(receiver) : f ? f.value : state.get(receiver);
}

function __classPrivateFieldSet(receiver, state, value, kind, f) {
  if (kind === "m") throw new TypeError("Private method is not writable");
  if (kind === "a" && !f) throw new TypeError("Private accessor was defined without a setter");
  if (typeof state === "function" ? receiver !== state || !f : !state.has(receiver)) throw new TypeError("Cannot write private member to an object whose class did not declare it");
  return (kind === "a" ? f.call(receiver, value) : f ? f.value = value : state.set(receiver, value)), value;
}

function __classPrivateFieldIn(state, receiver) {
  if (receiver === null || (typeof receiver !== "object" && typeof receiver !== "function")) throw new TypeError("Cannot use 'in' operator on non-object");
  return typeof state === "function" ? receiver === state : state.has(receiver);
}

function __addDisposableResource(env, value, async) {
  if (value !== null && value !== void 0) {
    if (typeof value !== "object" && typeof value !== "function") throw new TypeError("Object expected.");
    var dispose, inner;
    if (async) {
      if (!Symbol.asyncDispose) throw new TypeError("Symbol.asyncDispose is not defined.");
      dispose = value[Symbol.asyncDispose];
    }
    if (dispose === void 0) {
      if (!Symbol.dispose) throw new TypeError("Symbol.dispose is not defined.");
      dispose = value[Symbol.dispose];
      if (async) inner = dispose;
    }
    if (typeof dispose !== "function") throw new TypeError("Object not disposable.");
    if (inner) dispose = function() { try { inner.call(this); } catch (e) { return Promise.reject(e); } };
    env.stack.push({ value: value, dispose: dispose, async: async });
  }
  else if (async) {
    env.stack.push({ async: true });
  }
  return value;
}

var _SuppressedError = typeof SuppressedError === "function" ? SuppressedError : function (error, suppressed, message) {
  var e = new Error(message);
  return e.name = "SuppressedError", e.error = error, e.suppressed = suppressed, e;
};

function __disposeResources(env) {
  function fail(e) {
    env.error = env.hasError ? new _SuppressedError(e, env.error, "An error was suppressed during disposal.") : e;
    env.hasError = true;
  }
  var r, s = 0;
  function next() {
    while (r = env.stack.pop()) {
      try {
        if (!r.async && s === 1) return s = 0, env.stack.push(r), Promise.resolve().then(next);
        if (r.dispose) {
          var result = r.dispose.call(r.value);
          if (r.async) return s |= 2, Promise.resolve(result).then(next, function(e) { fail(e); return next(); });
        }
        else s |= 1;
      }
      catch (e) {
        fail(e);
      }
    }
    if (s === 1) return env.hasError ? Promise.reject(env.error) : Promise.resolve();
    if (env.hasError) throw env.error;
  }
  return next();
}

function __rewriteRelativeImportExtension(path, preserveJsx) {
  if (typeof path === "string" && /^\.\.?\//.test(path)) {
      return path.replace(/\.(tsx)$|((?:\.d)?)((?:\.[^./]+?)?)\.([cm]?)ts$/i, function (m, tsx, d, ext, cm) {
          return tsx ? preserveJsx ? ".jsx" : ".js" : d && (!ext || !cm) ? m : (d + ext + "." + cm.toLowerCase() + "js");
      });
  }
  return path;
}

/* unused harmony default export */ var __WEBPACK_DEFAULT_EXPORT__ = ({
  __extends,
  __assign,
  __rest,
  __decorate,
  __param,
  __esDecorate,
  __runInitializers,
  __propKey,
  __setFunctionName,
  __metadata,
  __awaiter,
  __generator,
  __createBinding,
  __exportStar,
  __values,
  __read,
  __spread,
  __spreadArrays,
  __spreadArray,
  __await,
  __asyncGenerator,
  __asyncDelegator,
  __asyncValues,
  __makeTemplateObject,
  __importStar,
  __importDefault,
  __classPrivateFieldGet,
  __classPrivateFieldSet,
  __classPrivateFieldIn,
  __addDisposableResource,
  __disposeResources,
  __rewriteRelativeImportExtension,
});


/***/ }),

/***/ 2226:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   W: () => (/* binding */ noCase)
/* harmony export */ });
/* harmony import */ var lower_case__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(7314);

// Support camel case ("camelCase" -> "camel Case" and "CAMELCase" -> "CAMEL Case").
var DEFAULT_SPLIT_REGEXP = [/([a-z0-9])([A-Z])/g, /([A-Z])([A-Z][a-z])/g];
// Remove all non-word characters.
var DEFAULT_STRIP_REGEXP = /[^A-Z0-9]+/gi;
/**
 * Normalize the string into something other libraries can manipulate easier.
 */
function noCase(input, options) {
    if (options === void 0) { options = {}; }
    var _a = options.splitRegexp, splitRegexp = _a === void 0 ? DEFAULT_SPLIT_REGEXP : _a, _b = options.stripRegexp, stripRegexp = _b === void 0 ? DEFAULT_STRIP_REGEXP : _b, _c = options.transform, transform = _c === void 0 ? lower_case__WEBPACK_IMPORTED_MODULE_0__/* .lowerCase */ .g : _c, _d = options.delimiter, delimiter = _d === void 0 ? " " : _d;
    var result = replace(replace(input, splitRegexp, "$1\0$2"), stripRegexp, "\0");
    var start = 0;
    var end = result.length;
    // Trim the delimiter from around the output string.
    while (result.charAt(start) === "\0")
        start++;
    while (result.charAt(end - 1) === "\0")
        end--;
    // Transform each token independently.
    return result.slice(start, end).split("\0").map(transform).join(delimiter);
}
/**
 * Replace `re` in the input string with the replacement value.
 */
function replace(input, re, value) {
    if (re instanceof RegExp)
        return input.replace(re, value);
    return re.reduce(function (input, re) { return input.replace(re, value); }, input);
}


/***/ }),

/***/ 2239:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  A: () => (/* binding */ createLocksActions)
});

;// ./node_modules/@wordpress/core-data/build-module/locks/utils.js
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
function hasConflictingLock({ exclusive }, locks) {
  if (exclusive && locks.length) {
    return true;
  }
  if (!exclusive && locks.filter((lock) => lock.exclusive).length) {
    return true;
  }
  return false;
}


;// ./node_modules/@wordpress/core-data/build-module/locks/reducer.js

const DEFAULT_STATE = {
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
      const { lock, request } = action;
      const { store, path } = request;
      const storePath = [store, ...path];
      const newTree = deepCopyLocksTreePath(state.tree, storePath);
      const node = getNode(newTree, storePath);
      node.locks = [...node.locks, lock];
      return {
        ...state,
        requests: state.requests.filter((r) => r !== request),
        tree: newTree
      };
    }
    case "RELEASE_LOCK": {
      const { lock } = action;
      const storePath = [lock.store, ...lock.path];
      const newTree = deepCopyLocksTreePath(state.tree, storePath);
      const node = getNode(newTree, storePath);
      node.locks = node.locks.filter((l) => l !== lock);
      return {
        ...state,
        tree: newTree
      };
    }
  }
  return state;
}


;// ./node_modules/@wordpress/core-data/build-module/locks/selectors.js

function getPendingLockRequests(state) {
  return state.requests;
}
function isLockAvailable(state, store, path, { exclusive }) {
  const storePath = [store, ...path];
  const locks = state.tree;
  for (const node2 of iteratePath(locks, storePath)) {
    if (hasConflictingLock({ exclusive }, node2.locks)) {
      return false;
    }
  }
  const node = getNode(locks, storePath);
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


;// ./node_modules/@wordpress/core-data/build-module/locks/engine.js


function createLocks() {
  let state = locks(void 0, { type: "@@INIT" });
  function processPendingLockRequests() {
    for (const request of getPendingLockRequests(state)) {
      const { store, path, exclusive, notifyAcquired } = request;
      if (isLockAvailable(state, store, path, { exclusive })) {
        const lock = { store, path, exclusive };
        state = locks(state, {
          type: "GRANT_LOCK_REQUEST",
          lock,
          request
        });
        notifyAcquired(lock);
      }
    }
  }
  function acquire(store, path, exclusive) {
    return new Promise((resolve) => {
      state = locks(state, {
        type: "ENQUEUE_LOCK_REQUEST",
        request: { store, path, exclusive, notifyAcquired: resolve }
      });
      processPendingLockRequests();
    });
  }
  function release(lock) {
    state = locks(state, {
      type: "RELEASE_LOCK",
      lock
    });
    processPendingLockRequests();
  }
  return { acquire, release };
}


;// ./node_modules/@wordpress/core-data/build-module/locks/actions.js

function createLocksActions() {
  const locks = createLocks();
  function __unstableAcquireStoreLock(store, path, { exclusive }) {
    return () => locks.acquire(store, path, exclusive);
  }
  function __unstableReleaseStoreLock(lock) {
    return () => locks.release(lock);
  }
  return { __unstableAcquireStoreLock, __unstableReleaseStoreLock };
}



/***/ }),

/***/ 2278:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   E: () => (/* binding */ STORE_NAME)
/* harmony export */ });
const STORE_NAME = "core";



/***/ }),

/***/ 2577:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   CO: () => (/* binding */ ALLOWED_RESOURCE_ACTIONS),
/* harmony export */   kC: () => (/* binding */ getUserPermissionCacheKey),
/* harmony export */   qY: () => (/* binding */ getUserPermissionsFromAllowHeader)
/* harmony export */ });
const ALLOWED_RESOURCE_ACTIONS = [
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



/***/ }),

/***/ 2859:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   n: () => (/* binding */ Status)
/* harmony export */ });
var Status = /* @__PURE__ */ ((Status2) => {
  Status2["Idle"] = "IDLE";
  Status2["Resolving"] = "RESOLVING";
  Status2["Error"] = "ERROR";
  Status2["Success"] = "SUCCESS";
  return Status2;
})(Status || {});



/***/ }),

/***/ 3213:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  __experimentalBatch: () => (/* binding */ __experimentalBatch),
  __experimentalReceiveCurrentGlobalStylesId: () => (/* binding */ __experimentalReceiveCurrentGlobalStylesId),
  __experimentalReceiveThemeBaseGlobalStyles: () => (/* binding */ __experimentalReceiveThemeBaseGlobalStyles),
  __experimentalReceiveThemeGlobalStyleVariations: () => (/* binding */ __experimentalReceiveThemeGlobalStyleVariations),
  __experimentalSaveSpecifiedEntityEdits: () => (/* binding */ __experimentalSaveSpecifiedEntityEdits),
  __unstableCreateUndoLevel: () => (/* binding */ __unstableCreateUndoLevel),
  addEntities: () => (/* binding */ addEntities),
  deleteEntityRecord: () => (/* binding */ deleteEntityRecord),
  editEntityRecord: () => (/* binding */ editEntityRecord),
  receiveAutosaves: () => (/* binding */ receiveAutosaves),
  receiveCurrentTheme: () => (/* binding */ receiveCurrentTheme),
  receiveCurrentUser: () => (/* binding */ receiveCurrentUser),
  receiveDefaultTemplateId: () => (/* binding */ receiveDefaultTemplateId),
  receiveEmbedPreview: () => (/* binding */ receiveEmbedPreview),
  receiveEntityRecords: () => (/* binding */ receiveEntityRecords),
  receiveNavigationFallbackId: () => (/* binding */ receiveNavigationFallbackId),
  receiveRevisions: () => (/* binding */ receiveRevisions),
  receiveThemeGlobalStyleRevisions: () => (/* binding */ receiveThemeGlobalStyleRevisions),
  receiveThemeSupports: () => (/* binding */ receiveThemeSupports),
  receiveUploadPermissions: () => (/* binding */ receiveUploadPermissions),
  receiveUserPermission: () => (/* binding */ receiveUserPermission),
  receiveUserPermissions: () => (/* binding */ receiveUserPermissions),
  receiveUserQuery: () => (/* binding */ receiveUserQuery),
  redo: () => (/* binding */ redo),
  saveEditedEntityRecord: () => (/* binding */ saveEditedEntityRecord),
  saveEntityRecord: () => (/* binding */ saveEntityRecord),
  undo: () => (/* binding */ undo)
});

// EXTERNAL MODULE: ./node_modules/fast-deep-equal/es6/index.js
var es6 = __webpack_require__(7734);
var es6_default = /*#__PURE__*/__webpack_require__.n(es6);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/node_modules/uuid/dist/esm-browser/v4.js + 3 modules
var v4 = __webpack_require__(1569);
// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__(1455);
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);
// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__(3832);
// EXTERNAL MODULE: external ["wp","deprecated"]
var external_wp_deprecated_ = __webpack_require__(4040);
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/set-nested-value.js
var set_nested_value = __webpack_require__(5003);
;// ./node_modules/@wordpress/core-data/build-module/utils/get-nested-value.js
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


;// ./node_modules/@wordpress/core-data/build-module/queried-data/actions.js
function receiveItems(items, edits, meta) {
  return {
    type: "RECEIVE_ITEMS",
    items: Array.isArray(items) ? items : [items],
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
function receiveQueriedItems(items, query = {}, edits, meta) {
  return {
    ...receiveItems(items, edits, meta),
    query
  };
}


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/entities.js + 32 modules
var entities = __webpack_require__(4767);
;// ./node_modules/@wordpress/core-data/build-module/batch/default-processor.js

let maxItems = null;
function chunk(arr, chunkSize) {
  const tmp = [...arr];
  const cache = [];
  while (tmp.length) {
    cache.push(tmp.splice(0, chunkSize));
  }
  return cache;
}
async function defaultProcessor(requests) {
  if (maxItems === null) {
    const preflightResponse = await external_wp_apiFetch_default()({
      path: "/batch/v1",
      method: "OPTIONS"
    });
    maxItems = preflightResponse.endpoints[0].args.requests.maxItems;
  }
  const results = [];
  for (const batchRequests of chunk(requests, maxItems)) {
    const batchResponse = await external_wp_apiFetch_default()({
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


;// ./node_modules/@wordpress/core-data/build-module/batch/create-batch.js

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
class ObservableSet {
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
}


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/name.js
var build_module_name = __webpack_require__(2278);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/log-entity-deprecation.js
var log_entity_deprecation = __webpack_require__(9410);
;// ./node_modules/@wordpress/core-data/build-module/actions.js












function receiveUserQuery(queryID, users) {
  return {
    type: "RECEIVE_USER_QUERY",
    users: Array.isArray(users) ? users : [users],
    queryID
  };
}
function receiveCurrentUser(currentUser) {
  return {
    type: "RECEIVE_CURRENT_USER",
    currentUser
  };
}
function addEntities(entities) {
  return {
    type: "ADD_ENTITIES",
    entities
  };
}
function receiveEntityRecords(kind, name, records, query, invalidateCache = false, edits, meta) {
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
function receiveCurrentTheme(currentTheme) {
  return {
    type: "RECEIVE_CURRENT_THEME",
    currentTheme
  };
}
function __experimentalReceiveCurrentGlobalStylesId(currentGlobalStylesId) {
  return {
    type: "RECEIVE_CURRENT_GLOBAL_STYLES_ID",
    id: currentGlobalStylesId
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
  external_wp_deprecated_default()("wp.data.dispatch( 'core' ).receiveThemeSupports", {
    since: "5.9"
  });
  return {
    type: "DO_NOTHING"
  };
}
function receiveThemeGlobalStyleRevisions(currentId, revisions) {
  external_wp_deprecated_default()(
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
const deleteEntityRecord = (kind, name, recordId, query, { __unstableFetch = (external_wp_apiFetch_default()), throwOnError = false } = {}) => async ({ dispatch, resolveSelect }) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "deleteEntityRecord");
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.kind === kind && config.name === name
  );
  let error;
  let deletedRecord = false;
  if (!entityConfig) {
    return;
  }
  const lock = await dispatch.__unstableAcquireStoreLock(
    build_module_name/* STORE_NAME */.E,
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
    if (kind === "postType" && name === "wp_template" && recordId && typeof recordId === "string" && !/^\d+$/.test(recordId)) {
      baseURL = baseURL.slice(0, baseURL.lastIndexOf("/")) + "/templates";
    }
    try {
      let path = `${baseURL}/${recordId}`;
      if (query) {
        path = (0,external_wp_url_.addQueryArgs)(path, query);
      }
      deletedRecord = await __unstableFetch({
        path,
        method: "DELETE"
      });
      await dispatch(removeItems(kind, name, recordId, true));
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
    dispatch.__unstableReleaseStoreLock(lock);
  }
};
const editEntityRecord = (kind, name, recordId, edits, options = {}) => ({ select, dispatch }) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "editEntityRecord");
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
  const edit = {
    kind,
    name,
    recordId,
    // Clear edits when they are equal to their persisted counterparts
    // so that the property is not considered dirty.
    edits: Object.keys(edits).reduce((acc, key) => {
      const recordValue = record[key];
      const editedRecordValue = editedRecord[key];
      const value = mergedEdits[key] ? { ...editedRecordValue, ...edits[key] } : edits[key];
      acc[key] = es6_default()(recordValue, value) ? void 0 : value;
      return acc;
    }, {})
  };
  if (window.__experimentalEnableSync && entityConfig.syncConfig) {
    if (false) {}
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
const undo = () => ({ select, dispatch }) => {
  const undoRecord = select.getUndoManager().undo();
  if (!undoRecord) {
    return;
  }
  dispatch({
    type: "UNDO",
    record: undoRecord
  });
};
const redo = () => ({ select, dispatch }) => {
  const redoRecord = select.getUndoManager().redo();
  if (!redoRecord) {
    return;
  }
  dispatch({
    type: "REDO",
    record: redoRecord
  });
};
const __unstableCreateUndoLevel = () => ({ select }) => {
  select.getUndoManager().addRecord();
};
const saveEntityRecord = (kind, name, record, {
  isAutosave = false,
  __unstableFetch = (external_wp_apiFetch_default()),
  throwOnError = false
} = {}) => async ({ select, resolveSelect, dispatch }) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "saveEntityRecord");
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.kind === kind && config.name === name
  );
  if (!entityConfig) {
    return;
  }
  const entityIdKey = entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_;
  const recordId = record[entityIdKey];
  const lock = await dispatch.__unstableAcquireStoreLock(
    build_module_name/* STORE_NAME */.E,
    ["entities", "records", kind, name, recordId || (0,v4/* default */.A)()],
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
    if (kind === "postType" && name === "wp_template" && recordId && typeof recordId === "string" && !/^\d+$/.test(recordId)) {
      baseURL = baseURL.slice(0, baseURL.lastIndexOf("/")) + "/templates";
    }
    try {
      const path = `${baseURL}${recordId ? "/" + recordId : ""}`;
      const persistedRecord = select.getRawEntityRecord(
        kind,
        name,
        recordId
      );
      if (isAutosave) {
        const currentUser = select.getCurrentUser();
        const currentUserId = currentUser ? currentUser.id : void 0;
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
    dispatch.__unstableReleaseStoreLock(lock);
  }
};
const __experimentalBatch = (requests) => async ({ dispatch }) => {
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
const saveEditedEntityRecord = (kind, name, recordId, options) => async ({ select, dispatch, resolveSelect }) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "saveEditedEntityRecord");
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
  const entityIdKey = entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_;
  const edits = select.getEntityRecordNonTransientEdits(
    kind,
    name,
    recordId
  );
  const record = { [entityIdKey]: recordId, ...edits };
  return await dispatch.saveEntityRecord(kind, name, record, options);
};
const __experimentalSaveSpecifiedEntityEdits = (kind, name, recordId, itemsToSave, options) => async ({ select, dispatch, resolveSelect }) => {
  (0,log_entity_deprecation/* default */.A)(
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
    (0,set_nested_value/* default */.A)(editsToSave, item, getNestedValue(edits, item));
  }
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.kind === kind && config.name === name
  );
  const entityIdKey = entityConfig?.key || entities/* DEFAULT_ENTITY_KEY */.C_;
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
  external_wp_deprecated_default()("wp.data.dispatch( 'core' ).receiveUploadPermissions", {
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
function receiveAutosaves(postId, autosaves) {
  return {
    type: "RECEIVE_AUTOSAVES",
    postId,
    autosaves: Array.isArray(autosaves) ? autosaves : [autosaves]
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
const receiveRevisions = (kind, name, recordKey, records, query, invalidateCache = false, meta) => async ({ dispatch, resolveSelect }) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "receiveRevisions");
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.kind === kind && config.name === name
  );
  const key = entityConfig && entityConfig?.revisionKey ? entityConfig.revisionKey : entities/* DEFAULT_ENTITY_KEY */.C_;
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



/***/ }),

/***/ 3249:
/***/ ((module) => {

"use strict";


function _typeof(obj) {
  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function (obj) {
      return typeof obj;
    };
  } else {
    _typeof = function (obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
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

/**
 * Given an instance of EquivalentKeyMap, returns its internal value pair tuple
 * for a key, if one exists. The tuple members consist of the last reference
 * value for the key (used in efficient subsequent lookups) and the value
 * assigned for the key at the leaf node.
 *
 * @param {EquivalentKeyMap} instance EquivalentKeyMap instance.
 * @param {*} key                     The key for which to return value pair.
 *
 * @return {?Array} Value pair, if exists.
 */
function getValuePair(instance, key) {
  var _map = instance._map,
      _arrayTreeMap = instance._arrayTreeMap,
      _objectTreeMap = instance._objectTreeMap; // Map keeps a reference to the last object-like key used to set the
  // value, which can be used to shortcut immediately to the value.

  if (_map.has(key)) {
    return _map.get(key);
  } // Sort keys to ensure stable retrieval from tree.


  var properties = Object.keys(key).sort(); // Tree by type to avoid conflicts on numeric object keys, empty value.

  var map = Array.isArray(key) ? _arrayTreeMap : _objectTreeMap;

  for (var i = 0; i < properties.length; i++) {
    var property = properties[i];
    map = map.get(property);

    if (map === undefined) {
      return;
    }

    var propertyValue = key[property];
    map = map.get(propertyValue);

    if (map === undefined) {
      return;
    }
  }

  var valuePair = map.get('_ekm_value');

  if (!valuePair) {
    return;
  } // If reached, it implies that an object-like key was set with another
  // reference, so delete the reference and replace with the current.


  _map.delete(valuePair[0]);

  valuePair[0] = key;
  map.set('_ekm_value', valuePair);

  _map.set(key, valuePair);

  return valuePair;
}
/**
 * Variant of a Map object which enables lookup by equivalent (deeply equal)
 * object and array keys.
 */


var EquivalentKeyMap =
/*#__PURE__*/
function () {
  /**
   * Constructs a new instance of EquivalentKeyMap.
   *
   * @param {Iterable.<*>} iterable Initial pair of key, value for map.
   */
  function EquivalentKeyMap(iterable) {
    _classCallCheck(this, EquivalentKeyMap);

    this.clear();

    if (iterable instanceof EquivalentKeyMap) {
      // Map#forEach is only means of iterating with support for IE11.
      var iterablePairs = [];
      iterable.forEach(function (value, key) {
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
  /**
   * Accessor property returning the number of elements.
   *
   * @return {number} Number of elements.
   */


  _createClass(EquivalentKeyMap, [{
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
      // Shortcut non-object-like to set on internal Map.
      if (key === null || _typeof(key) !== 'object') {
        this._map.set(key, value);

        return this;
      } // Sort keys to ensure stable assignment into tree.


      var properties = Object.keys(key).sort();
      var valuePair = [key, value]; // Tree by type to avoid conflicts on numeric object keys, empty value.

      var map = Array.isArray(key) ? this._arrayTreeMap : this._objectTreeMap;

      for (var i = 0; i < properties.length; i++) {
        var property = properties[i];

        if (!map.has(property)) {
          map.set(property, new EquivalentKeyMap());
        }

        map = map.get(property);
        var propertyValue = key[property];

        if (!map.has(propertyValue)) {
          map.set(propertyValue, new EquivalentKeyMap());
        }

        map = map.get(propertyValue);
      } // If an _ekm_value exists, there was already an equivalent key. Before
      // overriding, ensure that the old key reference is removed from map to
      // avoid memory leak of accumulating equivalent keys. This is, in a
      // sense, a poor man's WeakMap, while still enabling iterability.


      var previousValuePair = map.get('_ekm_value');

      if (previousValuePair) {
        this._map.delete(previousValuePair[0]);
      }

      map.set('_ekm_value', valuePair);

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
      // Shortcut non-object-like to get from internal Map.
      if (key === null || _typeof(key) !== 'object') {
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
      if (key === null || _typeof(key) !== 'object') {
        return this._map.has(key);
      } // Test on the _presence_ of the pair, not its value, as even undefined
      // can be a valid member value for a key.


      return getValuePair(this, key) !== undefined;
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
      } // This naive implementation will leave orphaned child trees. A better
      // implementation should traverse and remove orphans.


      this.set(key, undefined);
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

      var thisArg = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : this;

      this._map.forEach(function (value, key) {
        // Unwrap value from object-like value pair.
        if (key !== null && _typeof(key) === 'object') {
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
      this._map = new Map();
      this._arrayTreeMap = new Map();
      this._objectTreeMap = new Map();
    }
  }, {
    key: "size",
    get: function get() {
      return this._map.size;
    }
  }]);

  return EquivalentKeyMap;
}();

module.exports = EquivalentKeyMap;


/***/ }),

/***/ 3377:
/***/ (() => {



/***/ }),

/***/ 3832:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["url"];

/***/ }),

/***/ 4027:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  A: () => (/* binding */ get_query_parts_default)
});

// UNUSED EXPORTS: getQueryParts

// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__(3832);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/get-normalized-comma-separable.js
var get_normalized_comma_separable = __webpack_require__(533);
;// ./node_modules/@wordpress/core-data/build-module/utils/with-weak-map-cache.js
function withWeakMapCache(fn) {
  const cache = /* @__PURE__ */ new WeakMap();
  return (key) => {
    let value;
    if (cache.has(key)) {
      value = cache.get(key);
    } else {
      value = fn(key);
      if (key !== null && typeof key === "object") {
        cache.set(key, value);
      }
    }
    return value;
  };
}
var with_weak_map_cache_default = withWeakMapCache;


;// ./node_modules/@wordpress/core-data/build-module/queried-data/get-query-parts.js


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
          parts.fields = (0,get_normalized_comma_separable/* default */.A)(value) ?? [];
          value = parts.fields.join();
        }
        if (key === "include") {
          if (typeof value === "number") {
            value = value.toString();
          }
          parts.include = ((0,get_normalized_comma_separable/* default */.A)(value) ?? []).map(Number);
          value = parts.include.join();
        }
        parts.stableKey += (parts.stableKey ? "&" : "") + (0,external_wp_url_.addQueryArgs)("", { [key]: value }).slice(1);
    }
  }
  return parts;
}
var get_query_parts_default = with_weak_map_cache_default(getQueryParts);



/***/ }),

/***/ 4040:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["deprecated"];

/***/ }),

/***/ 4460:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  A: () => (/* binding */ EntityProvider)
});

;// external "ReactJSXRuntime"
const external_ReactJSXRuntime_namespaceObject = window["ReactJSXRuntime"];
// EXTERNAL MODULE: external ["wp","element"]
var external_wp_element_ = __webpack_require__(6087);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/entity-context.js
var entity_context = __webpack_require__(8843);
;// ./node_modules/@wordpress/core-data/build-module/entity-provider.js



function EntityProvider({ kind, type: name, id, children }) {
  const parent = (0,external_wp_element_.useContext)(entity_context/* EntityContext */.D);
  const childContext = (0,external_wp_element_.useMemo)(
    () => ({
      ...parent,
      [kind]: {
        ...parent?.[kind],
        [name]: id
      }
    }),
    [parent, kind, name, id]
  );
  return /* @__PURE__ */ (0,external_ReactJSXRuntime_namespaceObject.jsx)(entity_context/* EntityContext */.D.Provider, { value: childContext, children });
}



/***/ }),

/***/ 4565:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   EntityProvider: () => (/* reexport safe */ _entity_provider__WEBPACK_IMPORTED_MODULE_17__.A),
/* harmony export */   __experimentalFetchLinkSuggestions: () => (/* reexport safe */ _fetch__WEBPACK_IMPORTED_MODULE_14__.Y3),
/* harmony export */   __experimentalFetchUrlData: () => (/* reexport safe */ _fetch__WEBPACK_IMPORTED_MODULE_14__.gr),
/* harmony export */   __experimentalUseEntityRecord: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.qh),
/* harmony export */   __experimentalUseEntityRecords: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.bM),
/* harmony export */   __experimentalUseResourcePermissions: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__._),
/* harmony export */   fetchBlockPatterns: () => (/* reexport safe */ _fetch__WEBPACK_IMPORTED_MODULE_14__.l$),
/* harmony export */   privateApis: () => (/* reexport safe */ _private_apis__WEBPACK_IMPORTED_MODULE_16__.j),
/* harmony export */   store: () => (/* binding */ store),
/* harmony export */   useEntityBlockEditor: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.hg),
/* harmony export */   useEntityId: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.mV),
/* harmony export */   useEntityProp: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.S$),
/* harmony export */   useEntityRecord: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.MA),
/* harmony export */   useEntityRecords: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.$u),
/* harmony export */   useResourcePermissions: () => (/* reexport safe */ _hooks__WEBPACK_IMPORTED_MODULE_15__.qs)
/* harmony export */ });
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(7143);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _reducer__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(5469);
/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(8368);
/* harmony import */ var _private_selectors__WEBPACK_IMPORTED_MODULE_11__ = __webpack_require__(8741);
/* harmony import */ var _actions__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(3213);
/* harmony import */ var _private_actions__WEBPACK_IMPORTED_MODULE_12__ = __webpack_require__(9424);
/* harmony import */ var _resolvers__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(6384);
/* harmony import */ var _locks_actions__WEBPACK_IMPORTED_MODULE_8__ = __webpack_require__(2239);
/* harmony import */ var _entities__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(4767);
/* harmony import */ var _name__WEBPACK_IMPORTED_MODULE_9__ = __webpack_require__(2278);
/* harmony import */ var _lock_unlock__WEBPACK_IMPORTED_MODULE_10__ = __webpack_require__(6378);
/* harmony import */ var _dynamic_entities__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(8582);
/* harmony import */ var _utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(9410);
/* harmony import */ var _entity_provider__WEBPACK_IMPORTED_MODULE_17__ = __webpack_require__(4460);
/* harmony import */ var _entity_types__WEBPACK_IMPORTED_MODULE_13__ = __webpack_require__(3377);
/* harmony import */ var _entity_types__WEBPACK_IMPORTED_MODULE_13___default = /*#__PURE__*/__webpack_require__.n(_entity_types__WEBPACK_IMPORTED_MODULE_13__);
/* harmony reexport (unknown) */ var __WEBPACK_REEXPORT_OBJECT__ = {};
/* harmony reexport (unknown) */ for(const __WEBPACK_IMPORT_KEY__ in _entity_types__WEBPACK_IMPORTED_MODULE_13__) if(["default","EntityProvider","store"].indexOf(__WEBPACK_IMPORT_KEY__) < 0) __WEBPACK_REEXPORT_OBJECT__[__WEBPACK_IMPORT_KEY__] = () => _entity_types__WEBPACK_IMPORTED_MODULE_13__[__WEBPACK_IMPORT_KEY__]
/* harmony reexport (unknown) */ __webpack_require__.d(__webpack_exports__, __WEBPACK_REEXPORT_OBJECT__);
/* harmony import */ var _fetch__WEBPACK_IMPORTED_MODULE_14__ = __webpack_require__(7006);
/* harmony import */ var _hooks__WEBPACK_IMPORTED_MODULE_15__ = __webpack_require__(644);
/* harmony import */ var _private_apis__WEBPACK_IMPORTED_MODULE_16__ = __webpack_require__(7826);













const entitiesConfig = [
  ..._entities__WEBPACK_IMPORTED_MODULE_1__/* .rootEntitiesConfig */ .Mr,
  ..._entities__WEBPACK_IMPORTED_MODULE_1__/* .additionalEntityConfigLoaders */ .L2.filter((config) => !!config.name)
];
const entitySelectors = entitiesConfig.reduce((result, entity) => {
  const { kind, name, plural } = entity;
  const getEntityRecordMethodName = (0,_entities__WEBPACK_IMPORTED_MODULE_1__/* .getMethodName */ .zD)(kind, name);
  result[getEntityRecordMethodName] = (state, key, query) => {
    (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, name, getEntityRecordMethodName, {
      isShorthandSelector: true,
      alternativeFunctionName: "getEntityRecord"
    });
    return _selectors__WEBPACK_IMPORTED_MODULE_3__.getEntityRecord(state, kind, name, key, query);
  };
  if (plural) {
    const getEntityRecordsMethodName = (0,_entities__WEBPACK_IMPORTED_MODULE_1__/* .getMethodName */ .zD)(kind, plural, "get");
    result[getEntityRecordsMethodName] = (state, query) => {
      (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, name, getEntityRecordsMethodName, {
        isShorthandSelector: true,
        alternativeFunctionName: "getEntityRecords"
      });
      return _selectors__WEBPACK_IMPORTED_MODULE_3__.getEntityRecords(state, kind, name, query);
    };
  }
  return result;
}, {});
const entityResolvers = entitiesConfig.reduce((result, entity) => {
  const { kind, name, plural } = entity;
  const getEntityRecordMethodName = (0,_entities__WEBPACK_IMPORTED_MODULE_1__/* .getMethodName */ .zD)(kind, name);
  result[getEntityRecordMethodName] = (key, query) => {
    (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, name, getEntityRecordMethodName, {
      isShorthandSelector: true,
      alternativeFunctionName: "getEntityRecord"
    });
    return _resolvers__WEBPACK_IMPORTED_MODULE_4__.getEntityRecord(kind, name, key, query);
  };
  if (plural) {
    const getEntityRecordsMethodName = (0,_entities__WEBPACK_IMPORTED_MODULE_1__/* .getMethodName */ .zD)(kind, plural, "get");
    result[getEntityRecordsMethodName] = (...args) => {
      (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, plural, getEntityRecordsMethodName, {
        isShorthandSelector: true,
        alternativeFunctionName: "getEntityRecords"
      });
      return _resolvers__WEBPACK_IMPORTED_MODULE_4__.getEntityRecords(kind, name, ...args);
    };
    result[getEntityRecordsMethodName].shouldInvalidate = (action) => _resolvers__WEBPACK_IMPORTED_MODULE_4__.getEntityRecords.shouldInvalidate(action, kind, name);
  }
  return result;
}, {});
const entityActions = entitiesConfig.reduce((result, entity) => {
  const { kind, name } = entity;
  const saveEntityRecordMethodName = (0,_entities__WEBPACK_IMPORTED_MODULE_1__/* .getMethodName */ .zD)(kind, name, "save");
  result[saveEntityRecordMethodName] = (record, options) => {
    (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, name, saveEntityRecordMethodName, {
      isShorthandSelector: true,
      alternativeFunctionName: "saveEntityRecord"
    });
    return _actions__WEBPACK_IMPORTED_MODULE_5__.saveEntityRecord(kind, name, record, options);
  };
  const deleteEntityRecordMethodName = (0,_entities__WEBPACK_IMPORTED_MODULE_1__/* .getMethodName */ .zD)(kind, name, "delete");
  result[deleteEntityRecordMethodName] = (key, query, options) => {
    (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, name, deleteEntityRecordMethodName, {
      isShorthandSelector: true,
      alternativeFunctionName: "deleteEntityRecord"
    });
    return _actions__WEBPACK_IMPORTED_MODULE_5__.deleteEntityRecord(kind, name, key, query, options);
  };
  return result;
}, {});
const storeConfig = () => ({
  reducer: _reducer__WEBPACK_IMPORTED_MODULE_6__/* ["default"] */ .Ay,
  actions: {
    ..._dynamic_entities__WEBPACK_IMPORTED_MODULE_7__/* .dynamicActions */ .B,
    ..._actions__WEBPACK_IMPORTED_MODULE_5__,
    ...entityActions,
    ...(0,_locks_actions__WEBPACK_IMPORTED_MODULE_8__/* ["default"] */ .A)()
  },
  selectors: {
    ..._dynamic_entities__WEBPACK_IMPORTED_MODULE_7__/* .dynamicSelectors */ .A,
    ..._selectors__WEBPACK_IMPORTED_MODULE_3__,
    ...entitySelectors
  },
  resolvers: { ..._resolvers__WEBPACK_IMPORTED_MODULE_4__, ...entityResolvers }
});
const store = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createReduxStore)(_name__WEBPACK_IMPORTED_MODULE_9__/* .STORE_NAME */ .E, storeConfig());
(0,_lock_unlock__WEBPACK_IMPORTED_MODULE_10__/* .unlock */ .T)(store).registerPrivateSelectors(_private_selectors__WEBPACK_IMPORTED_MODULE_11__);
(0,_lock_unlock__WEBPACK_IMPORTED_MODULE_10__/* .unlock */ .T)(store).registerPrivateActions(_private_actions__WEBPACK_IMPORTED_MODULE_12__);
(0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.register)(store);









/***/ }),

/***/ 4767:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  C_: () => (/* binding */ DEFAULT_ENTITY_KEY),
  L2: () => (/* binding */ additionalEntityConfigLoaders),
  TK: () => (/* binding */ deprecatedEntities),
  zD: () => (/* binding */ getMethodName),
  Mr: () => (/* binding */ rootEntitiesConfig)
});

// UNUSED EXPORTS: prePersistPostType

// EXTERNAL MODULE: ./node_modules/tslib/tslib.es6.mjs
var tslib_es6 = __webpack_require__(1635);
// EXTERNAL MODULE: ./node_modules/no-case/dist.es2015/index.js
var dist_es2015 = __webpack_require__(2226);
;// ./node_modules/upper-case-first/dist.es2015/index.js
/**
 * Upper case the first character of an input string.
 */
function upperCaseFirst(input) {
    return input.charAt(0).toUpperCase() + input.substr(1);
}

;// ./node_modules/capital-case/dist.es2015/index.js



function capitalCaseTransform(input) {
    return upperCaseFirst(input.toLowerCase());
}
function capitalCase(input, options) {
    if (options === void 0) { options = {}; }
    return (0,dist_es2015/* noCase */.W)(input, (0,tslib_es6/* __assign */.Cl)({ delimiter: " ", transform: capitalCaseTransform }, options));
}

// EXTERNAL MODULE: ./node_modules/pascal-case/dist.es2015/index.js
var pascal_case_dist_es2015 = __webpack_require__(287);
// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__(1455);
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);
// EXTERNAL MODULE: external ["wp","blocks"]
var external_wp_blocks_ = __webpack_require__(4997);
// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__(7723);
// EXTERNAL MODULE: ./node_modules/fast-deep-equal/es6/index.js
var es6 = __webpack_require__(7734);
var es6_default = /*#__PURE__*/__webpack_require__.n(es6);
;// ./node_modules/lib0/map.js
/**
 * Utility module to work with key-value stores.
 *
 * @module map
 */

/**
 * Creates a new Map instance.
 *
 * @function
 * @return {Map<any, any>}
 *
 * @function
 */
const create = () => new Map()

/**
 * Copy a Map object into a fresh Map object.
 *
 * @function
 * @template K,V
 * @param {Map<K,V>} m
 * @return {Map<K,V>}
 */
const copy = m => {
  const r = create()
  m.forEach((v, k) => { r.set(k, v) })
  return r
}

/**
 * Get map property. Create T if property is undefined and set T on map.
 *
 * ```js
 * const listeners = map.setIfUndefined(events, 'eventName', set.create)
 * listeners.add(listener)
 * ```
 *
 * @function
 * @template {Map<any, any>} MAP
 * @template {MAP extends Map<any,infer V> ? function():V : unknown} CF
 * @param {MAP} map
 * @param {MAP extends Map<infer K,any> ? K : unknown} key
 * @param {CF} createT
 * @return {ReturnType<CF>}
 */
const setIfUndefined = (map, key, createT) => {
  let set = map.get(key)
  if (set === undefined) {
    map.set(key, set = createT())
  }
  return set
}

/**
 * Creates an Array and populates it with the content of all key-value pairs using the `f(value, key)` function.
 *
 * @function
 * @template K
 * @template V
 * @template R
 * @param {Map<K,V>} m
 * @param {function(V,K):R} f
 * @return {Array<R>}
 */
const map_map = (m, f) => {
  const res = []
  for (const [key, value] of m) {
    res.push(f(value, key))
  }
  return res
}

/**
 * Tests whether any key-value pairs pass the test implemented by `f(value, key)`.
 *
 * @todo should rename to some - similarly to Array.some
 *
 * @function
 * @template K
 * @template V
 * @param {Map<K,V>} m
 * @param {function(V,K):boolean} f
 * @return {boolean}
 */
const any = (m, f) => {
  for (const [key, value] of m) {
    if (f(value, key)) {
      return true
    }
  }
  return false
}

/**
 * Tests whether all key-value pairs pass the test implemented by `f(value, key)`.
 *
 * @function
 * @template K
 * @template V
 * @param {Map<K,V>} m
 * @param {function(V,K):boolean} f
 * @return {boolean}
 */
const map_all = (m, f) => {
  for (const [key, value] of m) {
    if (!f(value, key)) {
      return false
    }
  }
  return true
}

;// ./node_modules/lib0/set.js
/**
 * Utility module to work with sets.
 *
 * @module set
 */

const set_create = () => new Set()

/**
 * @template T
 * @param {Set<T>} set
 * @return {Array<T>}
 */
const toArray = set => Array.from(set)

/**
 * @template T
 * @param {Set<T>} set
 * @return {T|undefined}
 */
const first = set => set.values().next().value

/**
 * @template T
 * @param {Iterable<T>} entries
 * @return {Set<T>}
 */
const from = entries => new Set(entries)

;// ./node_modules/lib0/array.js
/**
 * Utility module to work with Arrays.
 *
 * @module array
 */



/**
 * Return the last element of an array. The element must exist
 *
 * @template L
 * @param {ArrayLike<L>} arr
 * @return {L}
 */
const last = arr => arr[arr.length - 1]

/**
 * @template C
 * @return {Array<C>}
 */
const array_create = () => /** @type {Array<C>} */ ([])

/**
 * @template D
 * @param {Array<D>} a
 * @return {Array<D>}
 */
const array_copy = a => /** @type {Array<D>} */ (a.slice())

/**
 * Append elements from src to dest
 *
 * @template M
 * @param {Array<M>} dest
 * @param {Array<M>} src
 */
const appendTo = (dest, src) => {
  for (let i = 0; i < src.length; i++) {
    dest.push(src[i])
  }
}

/**
 * Transforms something array-like to an actual Array.
 *
 * @function
 * @template T
 * @param {ArrayLike<T>|Iterable<T>} arraylike
 * @return {T}
 */
const array_from = Array.from

/**
 * True iff condition holds on every element in the Array.
 *
 * @function
 * @template {ArrayLike<any>} ARR
 *
 * @param {ARR} arr
 * @param {ARR extends ArrayLike<infer S> ? ((value:S, index:number, arr:ARR) => boolean) : any} f
 * @return {boolean}
 */
const every = (arr, f) => {
  for (let i = 0; i < arr.length; i++) {
    if (!f(arr[i], i, arr)) {
      return false
    }
  }
  return true
}

/**
 * True iff condition holds on some element in the Array.
 *
 * @function
 * @template {ArrayLike<any>} ARR
 *
 * @param {ARR} arr
 * @param {ARR extends ArrayLike<infer S> ? ((value:S, index:number, arr:ARR) => boolean) : never} f
 * @return {boolean}
 */
const some = (arr, f) => {
  for (let i = 0; i < arr.length; i++) {
    if (f(arr[i], i, arr)) {
      return true
    }
  }
  return false
}

/**
 * @template ELEM
 *
 * @param {ArrayLike<ELEM>} a
 * @param {ArrayLike<ELEM>} b
 * @return {boolean}
 */
const equalFlat = (a, b) => a.length === b.length && every(a, (item, index) => item === b[index])

/**
 * @template ELEM
 * @param {Array<Array<ELEM>>} arr
 * @return {Array<ELEM>}
 */
const flatten = arr => fold(arr, /** @type {Array<ELEM>} */ ([]), (acc, val) => acc.concat(val))

/**
 * @template T
 * @param {number} len
 * @param {function(number, Array<T>):T} f
 * @return {Array<T>}
 */
const unfold = (len, f) => {
  const array = new Array(len)
  for (let i = 0; i < len; i++) {
    array[i] = f(i, array)
  }
  return array
}

/**
 * @template T
 * @template RESULT
 * @param {Array<T>} arr
 * @param {RESULT} seed
 * @param {function(RESULT, T, number):RESULT} folder
 */
const fold = (arr, seed, folder) => arr.reduce(folder, seed)

const isArray = Array.isArray

/**
 * @template T
 * @param {Array<T>} arr
 * @return {Array<T>}
 */
const unique = arr => array_from(set.from(arr))

/**
 * @template T
 * @template M
 * @param {ArrayLike<T>} arr
 * @param {function(T):M} mapper
 * @return {Array<T>}
 */
const uniqueBy = (arr, mapper) => {
  /**
   * @type {Set<M>}
   */
  const happened = set.create()
  /**
   * @type {Array<T>}
   */
  const result = []
  for (let i = 0; i < arr.length; i++) {
    const el = arr[i]
    const mapped = mapper(el)
    if (!happened.has(mapped)) {
      happened.add(mapped)
      result.push(el)
    }
  }
  return result
}

/**
 * @template {ArrayLike<any>} ARR
 * @template {function(ARR extends ArrayLike<infer T> ? T : never, number, ARR):any} MAPPER
 * @param {ARR} arr
 * @param {MAPPER} mapper
 * @return {Array<MAPPER extends function(...any): infer M ? M : never>}
 */
const array_map = (arr, mapper) => {
  /**
   * @type {Array<any>}
   */
  const res = Array(arr.length)
  for (let i = 0; i < arr.length; i++) {
    res[i] = mapper(/** @type {any} */ (arr[i]), i, /** @type {any} */ (arr))
  }
  return /** @type {any} */ (res)
}

/**
 * This function bubble-sorts a single item to the correct position. The sort happens in-place and
 * might be useful to ensure that a single item is at the correct position in an otherwise sorted
 * array.
 *
 * @example
 *  const arr = [3, 2, 5]
 *  arr.sort((a, b) => a - b)
 *  arr // => [2, 3, 5]
 *  arr.splice(1, 0, 7)
 *  array.bubbleSortItem(arr, 1, (a, b) => a - b)
 *  arr // => [2, 3, 5, 7]
 *
 * @template T
 * @param {Array<T>} arr
 * @param {number} i
 * @param {(a:T,b:T) => number} compareFn
 */
const bubblesortItem = (arr, i, compareFn) => {
  const n = arr[i]
  let j = i
  // try to sort to the right
  while (j + 1 < arr.length && compareFn(n, arr[j + 1]) > 0) {
    arr[j] = arr[j + 1]
    arr[++j] = n
  }
  if (i === j && j > 0) { // no change yet
    // sort to the left
    while (j > 0 && compareFn(arr[j - 1], n) > 0) {
      arr[j] = arr[j - 1]
      arr[--j] = n
    }
  }
  return j
}

;// ./node_modules/lib0/observable.js
/**
 * Observable class prototype.
 *
 * @module observable
 */





/**
 * Handles named events.
 * @experimental
 *
 * This is basically a (better typed) duplicate of Observable, which will replace Observable in the
 * next release.
 *
 * @template {{[key in keyof EVENTS]: function(...any):void}} EVENTS
 */
class ObservableV2 {
  constructor () {
    /**
     * Some desc.
     * @type {Map<string, Set<any>>}
     */
    this._observers = create()
  }

  /**
   * @template {keyof EVENTS & string} NAME
   * @param {NAME} name
   * @param {EVENTS[NAME]} f
   */
  on (name, f) {
    setIfUndefined(this._observers, /** @type {string} */ (name), set_create).add(f)
    return f
  }

  /**
   * @template {keyof EVENTS & string} NAME
   * @param {NAME} name
   * @param {EVENTS[NAME]} f
   */
  once (name, f) {
    /**
     * @param  {...any} args
     */
    const _f = (...args) => {
      this.off(name, /** @type {any} */ (_f))
      f(...args)
    }
    this.on(name, /** @type {any} */ (_f))
  }

  /**
   * @template {keyof EVENTS & string} NAME
   * @param {NAME} name
   * @param {EVENTS[NAME]} f
   */
  off (name, f) {
    const observers = this._observers.get(name)
    if (observers !== undefined) {
      observers.delete(f)
      if (observers.size === 0) {
        this._observers.delete(name)
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
  emit (name, args) {
    // copy all listeners to an array first to make sure that no event is emitted to listeners that are subscribed while the event handler is called.
    return array_from((this._observers.get(name) || create()).values()).forEach(f => f(...args))
  }

  destroy () {
    this._observers = create()
  }
}

/* c8 ignore start */
/**
 * Handles named events.
 *
 * @deprecated
 * @template N
 */
class Observable {
  constructor () {
    /**
     * Some desc.
     * @type {Map<N, any>}
     */
    this._observers = map.create()
  }

  /**
   * @param {N} name
   * @param {function} f
   */
  on (name, f) {
    map.setIfUndefined(this._observers, name, set.create).add(f)
  }

  /**
   * @param {N} name
   * @param {function} f
   */
  once (name, f) {
    /**
     * @param  {...any} args
     */
    const _f = (...args) => {
      this.off(name, _f)
      f(...args)
    }
    this.on(name, _f)
  }

  /**
   * @param {N} name
   * @param {function} f
   */
  off (name, f) {
    const observers = this._observers.get(name)
    if (observers !== undefined) {
      observers.delete(f)
      if (observers.size === 0) {
        this._observers.delete(name)
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
  emit (name, args) {
    // copy all listeners to an array first to make sure that no event is emitted to listeners that are subscribed while the event handler is called.
    return array.from((this._observers.get(name) || map.create()).values()).forEach(f => f(...args))
  }

  destroy () {
    this._observers = map.create()
  }
}
/* c8 ignore end */

;// ./node_modules/lib0/math.js
/**
 * Common Math expressions.
 *
 * @module math
 */

const floor = Math.floor
const ceil = Math.ceil
const abs = Math.abs
const imul = Math.imul
const round = Math.round
const log10 = Math.log10
const log2 = Math.log2
const log = Math.log
const sqrt = Math.sqrt

/**
 * @function
 * @param {number} a
 * @param {number} b
 * @return {number} The sum of a and b
 */
const add = (a, b) => a + b

/**
 * @function
 * @param {number} a
 * @param {number} b
 * @return {number} The smaller element of a and b
 */
const min = (a, b) => a < b ? a : b

/**
 * @function
 * @param {number} a
 * @param {number} b
 * @return {number} The bigger element of a and b
 */
const max = (a, b) => a > b ? a : b

const math_isNaN = Number.isNaN

const pow = Math.pow
/**
 * Base 10 exponential function. Returns the value of 10 raised to the power of pow.
 *
 * @param {number} exp
 * @return {number}
 */
const exp10 = exp => Math.pow(10, exp)

const sign = Math.sign

/**
 * @param {number} n
 * @return {boolean} Wether n is negative. This function also differentiates between -0 and +0
 */
const isNegativeZero = n => n !== 0 ? n < 0 : 1 / n < 0

;// ./node_modules/lib0/binary.js
/* eslint-env browser */

/**
 * Binary data constants.
 *
 * @module binary
 */

/**
 * n-th bit activated.
 *
 * @type {number}
 */
const BIT1 = 1
const BIT2 = 2
const BIT3 = 4
const BIT4 = 8
const BIT5 = 16
const BIT6 = 32
const BIT7 = 64
const BIT8 = 128
const BIT9 = 256
const BIT10 = 512
const BIT11 = 1024
const BIT12 = 2048
const BIT13 = 4096
const BIT14 = 8192
const BIT15 = 16384
const BIT16 = 32768
const BIT17 = 65536
const BIT18 = 1 << 17
const BIT19 = 1 << 18
const BIT20 = 1 << 19
const BIT21 = 1 << 20
const BIT22 = 1 << 21
const BIT23 = 1 << 22
const BIT24 = 1 << 23
const BIT25 = 1 << 24
const BIT26 = 1 << 25
const BIT27 = 1 << 26
const BIT28 = 1 << 27
const BIT29 = 1 << 28
const BIT30 = 1 << 29
const BIT31 = 1 << 30
const BIT32 = (/* unused pure expression or super */ null && (1 << 31))

/**
 * First n bits activated.
 *
 * @type {number}
 */
const BITS0 = 0
const BITS1 = 1
const BITS2 = 3
const BITS3 = 7
const BITS4 = 15
const BITS5 = 31
const BITS6 = 63
const BITS7 = 127
const BITS8 = 255
const BITS9 = 511
const BITS10 = 1023
const BITS11 = 2047
const BITS12 = 4095
const BITS13 = 8191
const BITS14 = 16383
const BITS15 = 32767
const BITS16 = 65535
const BITS17 = BIT18 - 1
const BITS18 = BIT19 - 1
const BITS19 = BIT20 - 1
const BITS20 = BIT21 - 1
const BITS21 = BIT22 - 1
const BITS22 = BIT23 - 1
const BITS23 = BIT24 - 1
const BITS24 = BIT25 - 1
const BITS25 = BIT26 - 1
const BITS26 = BIT27 - 1
const BITS27 = BIT28 - 1
const BITS28 = BIT29 - 1
const BITS29 = BIT30 - 1
const BITS30 = BIT31 - 1
/**
 * @type {number}
 */
const BITS31 = 0x7FFFFFFF
/**
 * @type {number}
 */
const BITS32 = 0xFFFFFFFF

;// ./node_modules/lib0/number.js
/**
 * Utility helpers for working with numbers.
 *
 * @module number
 */




const MAX_SAFE_INTEGER = Number.MAX_SAFE_INTEGER
const MIN_SAFE_INTEGER = Number.MIN_SAFE_INTEGER

const LOWEST_INT32 = (/* unused pure expression or super */ null && (1 << 31))
const HIGHEST_INT32 = BITS31
const HIGHEST_UINT32 = BITS32

/* c8 ignore next */
const isInteger = Number.isInteger || (num => typeof num === 'number' && isFinite(num) && floor(num) === num)
const number_isNaN = Number.isNaN
const number_parseInt = Number.parseInt

/**
 * Count the number of "1" bits in an unsigned 32bit number.
 *
 * Super fun bitcount algorithm by Brian Kernighan.
 *
 * @param {number} n
 */
const countBits = n => {
  n &= binary.BITS32
  let count = 0
  while (n) {
    n &= (n - 1)
    count++
  }
  return count
}

;// ./node_modules/lib0/string.js


/**
 * Utility module to work with strings.
 *
 * @module string
 */

const fromCharCode = String.fromCharCode
const fromCodePoint = String.fromCodePoint

/**
 * The largest utf16 character.
 * Corresponds to Uint8Array([255, 255]) or charcodeof(2x2^8)
 */
const MAX_UTF16_CHARACTER = fromCharCode(65535)

/**
 * @param {string} s
 * @return {string}
 */
const toLowerCase = s => s.toLowerCase()

const trimLeftRegex = /^\s*/g

/**
 * @param {string} s
 * @return {string}
 */
const trimLeft = s => s.replace(trimLeftRegex, '')

const fromCamelCaseRegex = /([A-Z])/g

/**
 * @param {string} s
 * @param {string} separator
 * @return {string}
 */
const fromCamelCase = (s, separator) => trimLeft(s.replace(fromCamelCaseRegex, match => `${separator}${toLowerCase(match)}`))

/**
 * Compute the utf8ByteLength
 * @param {string} str
 * @return {number}
 */
const utf8ByteLength = str => unescape(encodeURIComponent(str)).length

/**
 * @param {string} str
 * @return {Uint8Array}
 */
const _encodeUtf8Polyfill = str => {
  const encodedString = unescape(encodeURIComponent(str))
  const len = encodedString.length
  const buf = new Uint8Array(len)
  for (let i = 0; i < len; i++) {
    buf[i] = /** @type {number} */ (encodedString.codePointAt(i))
  }
  return buf
}

/* c8 ignore next */
const utf8TextEncoder = /** @type {TextEncoder} */ (typeof TextEncoder !== 'undefined' ? new TextEncoder() : null)

/**
 * @param {string} str
 * @return {Uint8Array}
 */
const _encodeUtf8Native = str => utf8TextEncoder.encode(str)

/**
 * @param {string} str
 * @return {Uint8Array}
 */
/* c8 ignore next */
const encodeUtf8 = utf8TextEncoder ? _encodeUtf8Native : _encodeUtf8Polyfill

/**
 * @param {Uint8Array} buf
 * @return {string}
 */
const _decodeUtf8Polyfill = buf => {
  let remainingLen = buf.length
  let encodedString = ''
  let bufPos = 0
  while (remainingLen > 0) {
    const nextLen = remainingLen < 10000 ? remainingLen : 10000
    const bytes = buf.subarray(bufPos, bufPos + nextLen)
    bufPos += nextLen
    // Starting with ES5.1 we can supply a generic array-like object as arguments
    encodedString += String.fromCodePoint.apply(null, /** @type {any} */ (bytes))
    remainingLen -= nextLen
  }
  return decodeURIComponent(escape(encodedString))
}

/* c8 ignore next */
let utf8TextDecoder = typeof TextDecoder === 'undefined' ? null : new TextDecoder('utf-8', { fatal: true, ignoreBOM: true })

/* c8 ignore start */
if (utf8TextDecoder && utf8TextDecoder.decode(new Uint8Array()).length === 1) {
  // Safari doesn't handle BOM correctly.
  // This fixes a bug in Safari 13.0.5 where it produces a BOM the first time it is called.
  // utf8TextDecoder.decode(new Uint8Array()).length === 1 on the first call and
  // utf8TextDecoder.decode(new Uint8Array()).length === 1 on the second call
  // Another issue is that from then on no BOM chars are recognized anymore
  /* c8 ignore next */
  utf8TextDecoder = null
}
/* c8 ignore stop */

/**
 * @param {Uint8Array} buf
 * @return {string}
 */
const _decodeUtf8Native = buf => /** @type {TextDecoder} */ (utf8TextDecoder).decode(buf)

/**
 * @param {Uint8Array} buf
 * @return {string}
 */
/* c8 ignore next */
const decodeUtf8 = (/* unused pure expression or super */ null && (utf8TextDecoder ? _decodeUtf8Native : _decodeUtf8Polyfill))

/**
 * @param {string} str The initial string
 * @param {number} index Starting position
 * @param {number} remove Number of characters to remove
 * @param {string} insert New content to insert
 */
const splice = (str, index, remove, insert = '') => str.slice(0, index) + insert + str.slice(index + remove)

/**
 * @param {string} source
 * @param {number} n
 */
const repeat = (source, n) => array.unfold(n, () => source).join('')

/**
 * Escape HTML characters &,<,>,'," to their respective HTML entities &amp;,&lt;,&gt;,&#39;,&quot;
 *
 * @param {string} str
 */
const escapeHTML = str =>
  str.replace(/[&<>'"]/g, r => /** @type {string} */ ({
    '&': '&amp;',
    '<': '&lt;',
    '>': '&gt;',
    "'": '&#39;',
    '"': '&quot;'
  }[r]))

/**
 * Reverse of `escapeHTML`
 *
 * @param {string} str
 */
const unescapeHTML = str =>
  str.replace(/&amp;|&lt;|&gt;|&#39;|&quot;/g, r => /** @type {string} */ ({
    '&amp;': '&',
    '&lt;': '<',
    '&gt;': '>',
    '&#39;': "'",
    '&quot;': '"'
  }[r]))

;// ./node_modules/lib0/encoding.js
/**
 * Efficient schema-less binary encoding with support for variable length encoding.
 *
 * Use [lib0/encoding] with [lib0/decoding]. Every encoding function has a corresponding decoding function.
 *
 * Encodes numbers in little-endian order (least to most significant byte order)
 * and is compatible with Golang's binary encoding (https://golang.org/pkg/encoding/binary/)
 * which is also used in Protocol Buffers.
 *
 * ```js
 * // encoding step
 * const encoder = encoding.createEncoder()
 * encoding.writeVarUint(encoder, 256)
 * encoding.writeVarString(encoder, 'Hello world!')
 * const buf = encoding.toUint8Array(encoder)
 * ```
 *
 * ```js
 * // decoding step
 * const decoder = decoding.createDecoder(buf)
 * decoding.readVarUint(decoder) // => 256
 * decoding.readVarString(decoder) // => 'Hello world!'
 * decoding.hasContent(decoder) // => false - all data is read
 * ```
 *
 * @module encoding
 */







/**
 * A BinaryEncoder handles the encoding to an Uint8Array.
 */
class Encoder {
  constructor () {
    this.cpos = 0
    this.cbuf = new Uint8Array(100)
    /**
     * @type {Array<Uint8Array>}
     */
    this.bufs = []
  }
}

/**
 * @function
 * @return {Encoder}
 */
const createEncoder = () => new Encoder()

/**
 * @param {function(Encoder):void} f
 */
const encode = (f) => {
  const encoder = createEncoder()
  f(encoder)
  return toUint8Array(encoder)
}

/**
 * The current length of the encoded data.
 *
 * @function
 * @param {Encoder} encoder
 * @return {number}
 */
const encoding_length = encoder => {
  let len = encoder.cpos
  for (let i = 0; i < encoder.bufs.length; i++) {
    len += encoder.bufs[i].length
  }
  return len
}

/**
 * Check whether encoder is empty.
 *
 * @function
 * @param {Encoder} encoder
 * @return {boolean}
 */
const hasContent = encoder => encoder.cpos > 0 || encoder.bufs.length > 0

/**
 * Transform to Uint8Array.
 *
 * @function
 * @param {Encoder} encoder
 * @return {Uint8Array} The created ArrayBuffer.
 */
const toUint8Array = encoder => {
  const uint8arr = new Uint8Array(encoding_length(encoder))
  let curPos = 0
  for (let i = 0; i < encoder.bufs.length; i++) {
    const d = encoder.bufs[i]
    uint8arr.set(d, curPos)
    curPos += d.length
  }
  uint8arr.set(new Uint8Array(encoder.cbuf.buffer, 0, encoder.cpos), curPos)
  return uint8arr
}

/**
 * Verify that it is possible to write `len` bytes wtihout checking. If
 * necessary, a new Buffer with the required length is attached.
 *
 * @param {Encoder} encoder
 * @param {number} len
 */
const verifyLen = (encoder, len) => {
  const bufferLen = encoder.cbuf.length
  if (bufferLen - encoder.cpos < len) {
    encoder.bufs.push(new Uint8Array(encoder.cbuf.buffer, 0, encoder.cpos))
    encoder.cbuf = new Uint8Array(max(bufferLen, len) * 2)
    encoder.cpos = 0
  }
}

/**
 * Write one byte to the encoder.
 *
 * @function
 * @param {Encoder} encoder
 * @param {number} num The byte that is to be encoded.
 */
const write = (encoder, num) => {
  const bufferLen = encoder.cbuf.length
  if (encoder.cpos === bufferLen) {
    encoder.bufs.push(encoder.cbuf)
    encoder.cbuf = new Uint8Array(bufferLen * 2)
    encoder.cpos = 0
  }
  encoder.cbuf[encoder.cpos++] = num
}

/**
 * Write one byte at a specific position.
 * Position must already be written (i.e. encoder.length > pos)
 *
 * @function
 * @param {Encoder} encoder
 * @param {number} pos Position to which to write data
 * @param {number} num Unsigned 8-bit integer
 */
const encoding_set = (encoder, pos, num) => {
  let buffer = null
  // iterate all buffers and adjust position
  for (let i = 0; i < encoder.bufs.length && buffer === null; i++) {
    const b = encoder.bufs[i]
    if (pos < b.length) {
      buffer = b // found buffer
    } else {
      pos -= b.length
    }
  }
  if (buffer === null) {
    // use current buffer
    buffer = encoder.cbuf
  }
  buffer[pos] = num
}

/**
 * Write one byte as an unsigned integer.
 *
 * @function
 * @param {Encoder} encoder
 * @param {number} num The number that is to be encoded.
 */
const writeUint8 = write

/**
 * Write one byte as an unsigned Integer at a specific location.
 *
 * @function
 * @param {Encoder} encoder
 * @param {number} pos The location where the data will be written.
 * @param {number} num The number that is to be encoded.
 */
const setUint8 = (/* unused pure expression or super */ null && (encoding_set))

/**
 * Write two bytes as an unsigned integer.
 *
 * @function
 * @param {Encoder} encoder
 * @param {number} num The number that is to be encoded.
 */
const writeUint16 = (encoder, num) => {
  write(encoder, num & binary.BITS8)
  write(encoder, (num >>> 8) & binary.BITS8)
}
/**
 * Write two bytes as an unsigned integer at a specific location.
 *
 * @function
 * @param {Encoder} encoder
 * @param {number} pos The location where the data will be written.
 * @param {number} num The number that is to be encoded.
 */
const setUint16 = (encoder, pos, num) => {
  encoding_set(encoder, pos, num & binary.BITS8)
  encoding_set(encoder, pos + 1, (num >>> 8) & binary.BITS8)
}

/**
 * Write two bytes as an unsigned integer
 *
 * @function
 * @param {Encoder} encoder
 * @param {number} num The number that is to be encoded.
 */
const writeUint32 = (encoder, num) => {
  for (let i = 0; i < 4; i++) {
    write(encoder, num & binary.BITS8)
    num >>>= 8
  }
}

/**
 * Write two bytes as an unsigned integer in big endian order.
 * (most significant byte first)
 *
 * @function
 * @param {Encoder} encoder
 * @param {number} num The number that is to be encoded.
 */
const writeUint32BigEndian = (encoder, num) => {
  for (let i = 3; i >= 0; i--) {
    write(encoder, (num >>> (8 * i)) & binary.BITS8)
  }
}

/**
 * Write two bytes as an unsigned integer at a specific location.
 *
 * @function
 * @param {Encoder} encoder
 * @param {number} pos The location where the data will be written.
 * @param {number} num The number that is to be encoded.
 */
const setUint32 = (encoder, pos, num) => {
  for (let i = 0; i < 4; i++) {
    encoding_set(encoder, pos + i, num & binary.BITS8)
    num >>>= 8
  }
}

/**
 * Write a variable length unsigned integer. Max encodable integer is 2^53.
 *
 * @function
 * @param {Encoder} encoder
 * @param {number} num The number that is to be encoded.
 */
const writeVarUint = (encoder, num) => {
  while (num > BITS7) {
    write(encoder, BIT8 | (BITS7 & num))
    num = floor(num / 128) // shift >>> 7
  }
  write(encoder, BITS7 & num)
}

/**
 * Write a variable length integer.
 *
 * We use the 7th bit instead for signaling that this is a negative number.
 *
 * @function
 * @param {Encoder} encoder
 * @param {number} num The number that is to be encoded.
 */
const writeVarInt = (encoder, num) => {
  const isNegative = isNegativeZero(num)
  if (isNegative) {
    num = -num
  }
  //             |- whether to continue reading         |- whether is negative     |- number
  write(encoder, (num > BITS6 ? BIT8 : 0) | (isNegative ? BIT7 : 0) | (BITS6 & num))
  num = floor(num / 64) // shift >>> 6
  // We don't need to consider the case of num === 0 so we can use a different
  // pattern here than above.
  while (num > 0) {
    write(encoder, (num > BITS7 ? BIT8 : 0) | (BITS7 & num))
    num = floor(num / 128) // shift >>> 7
  }
}

/**
 * A cache to store strings temporarily
 */
const _strBuffer = new Uint8Array(30000)
const _maxStrBSize = _strBuffer.length / 3

/**
 * Write a variable length string.
 *
 * @function
 * @param {Encoder} encoder
 * @param {String} str The string that is to be encoded.
 */
const _writeVarStringNative = (encoder, str) => {
  if (str.length < _maxStrBSize) {
    // We can encode the string into the existing buffer
    /* c8 ignore next */
    const written = utf8TextEncoder.encodeInto(str, _strBuffer).written || 0
    writeVarUint(encoder, written)
    for (let i = 0; i < written; i++) {
      write(encoder, _strBuffer[i])
    }
  } else {
    writeVarUint8Array(encoder, encodeUtf8(str))
  }
}

/**
 * Write a variable length string.
 *
 * @function
 * @param {Encoder} encoder
 * @param {String} str The string that is to be encoded.
 */
const _writeVarStringPolyfill = (encoder, str) => {
  const encodedString = unescape(encodeURIComponent(str))
  const len = encodedString.length
  writeVarUint(encoder, len)
  for (let i = 0; i < len; i++) {
    write(encoder, /** @type {number} */ (encodedString.codePointAt(i)))
  }
}

/**
 * Write a variable length string.
 *
 * @function
 * @param {Encoder} encoder
 * @param {String} str The string that is to be encoded.
 */
/* c8 ignore next */
const writeVarString = (utf8TextEncoder && /** @type {any} */ (utf8TextEncoder).encodeInto) ? _writeVarStringNative : _writeVarStringPolyfill

/**
 * Write a string terminated by a special byte sequence. This is not very performant and is
 * generally discouraged. However, the resulting byte arrays are lexiographically ordered which
 * makes this a nice feature for databases.
 *
 * The string will be encoded using utf8 and then terminated and escaped using writeTerminatingUint8Array.
 *
 * @function
 * @param {Encoder} encoder
 * @param {String} str The string that is to be encoded.
 */
const writeTerminatedString = (encoder, str) =>
  writeTerminatedUint8Array(encoder, string.encodeUtf8(str))

/**
 * Write a terminating Uint8Array. Note that this is not performant and is generally
 * discouraged. There are few situations when this is needed.
 *
 * We use 0x0 as a terminating character. 0x1 serves as an escape character for 0x0 and 0x1.
 *
 * Example: [0,1,2] is encoded to [1,0,1,1,2,0]. 0x0, and 0x1 needed to be escaped using 0x1. Then
 * the result is terminated using the 0x0 character.
 *
 * This is basically how many systems implement null terminated strings. However, we use an escape
 * character 0x1 to avoid issues and potenial attacks on our database (if this is used as a key
 * encoder for NoSql databases).
 *
 * @function
 * @param {Encoder} encoder
 * @param {Uint8Array} buf The string that is to be encoded.
 */
const writeTerminatedUint8Array = (encoder, buf) => {
  for (let i = 0; i < buf.length; i++) {
    const b = buf[i]
    if (b === 0 || b === 1) {
      write(encoder, 1)
    }
    write(encoder, buf[i])
  }
  write(encoder, 0)
}

/**
 * Write the content of another Encoder.
 *
 * @TODO: can be improved!
 *        - Note: Should consider that when appending a lot of small Encoders, we should rather clone than referencing the old structure.
 *                Encoders start with a rather big initial buffer.
 *
 * @function
 * @param {Encoder} encoder The enUint8Arr
 * @param {Encoder} append The BinaryEncoder to be written.
 */
const writeBinaryEncoder = (encoder, append) => writeUint8Array(encoder, toUint8Array(append))

/**
 * Append fixed-length Uint8Array to the encoder.
 *
 * @function
 * @param {Encoder} encoder
 * @param {Uint8Array} uint8Array
 */
const writeUint8Array = (encoder, uint8Array) => {
  const bufferLen = encoder.cbuf.length
  const cpos = encoder.cpos
  const leftCopyLen = min(bufferLen - cpos, uint8Array.length)
  const rightCopyLen = uint8Array.length - leftCopyLen
  encoder.cbuf.set(uint8Array.subarray(0, leftCopyLen), cpos)
  encoder.cpos += leftCopyLen
  if (rightCopyLen > 0) {
    // Still something to write, write right half..
    // Append new buffer
    encoder.bufs.push(encoder.cbuf)
    // must have at least size of remaining buffer
    encoder.cbuf = new Uint8Array(max(bufferLen * 2, rightCopyLen))
    // copy array
    encoder.cbuf.set(uint8Array.subarray(leftCopyLen))
    encoder.cpos = rightCopyLen
  }
}

/**
 * Append an Uint8Array to Encoder.
 *
 * @function
 * @param {Encoder} encoder
 * @param {Uint8Array} uint8Array
 */
const writeVarUint8Array = (encoder, uint8Array) => {
  writeVarUint(encoder, uint8Array.byteLength)
  writeUint8Array(encoder, uint8Array)
}

/**
 * Create an DataView of the next `len` bytes. Use it to write data after
 * calling this function.
 *
 * ```js
 * // write float32 using DataView
 * const dv = writeOnDataView(encoder, 4)
 * dv.setFloat32(0, 1.1)
 * // read float32 using DataView
 * const dv = readFromDataView(encoder, 4)
 * dv.getFloat32(0) // => 1.100000023841858 (leaving it to the reader to find out why this is the correct result)
 * ```
 *
 * @param {Encoder} encoder
 * @param {number} len
 * @return {DataView}
 */
const writeOnDataView = (encoder, len) => {
  verifyLen(encoder, len)
  const dview = new DataView(encoder.cbuf.buffer, encoder.cpos, len)
  encoder.cpos += len
  return dview
}

/**
 * @param {Encoder} encoder
 * @param {number} num
 */
const writeFloat32 = (encoder, num) => writeOnDataView(encoder, 4).setFloat32(0, num, false)

/**
 * @param {Encoder} encoder
 * @param {number} num
 */
const writeFloat64 = (encoder, num) => writeOnDataView(encoder, 8).setFloat64(0, num, false)

/**
 * @param {Encoder} encoder
 * @param {bigint} num
 */
const writeBigInt64 = (encoder, num) => /** @type {any} */ (writeOnDataView(encoder, 8)).setBigInt64(0, num, false)

/**
 * @param {Encoder} encoder
 * @param {bigint} num
 */
const writeBigUint64 = (encoder, num) => /** @type {any} */ (writeOnDataView(encoder, 8)).setBigUint64(0, num, false)

const floatTestBed = new DataView(new ArrayBuffer(4))
/**
 * Check if a number can be encoded as a 32 bit float.
 *
 * @param {number} num
 * @return {boolean}
 */
const isFloat32 = num => {
  floatTestBed.setFloat32(0, num)
  return floatTestBed.getFloat32(0) === num
}

/**
 * Encode data with efficient binary format.
 *
 * Differences to JSON:
 * • Transforms data to a binary format (not to a string)
 * • Encodes undefined, NaN, and ArrayBuffer (these can't be represented in JSON)
 * • Numbers are efficiently encoded either as a variable length integer, as a
 *   32 bit float, as a 64 bit float, or as a 64 bit bigint.
 *
 * Encoding table:
 *
 * | Data Type           | Prefix   | Encoding Method    | Comment |
 * | ------------------- | -------- | ------------------ | ------- |
 * | undefined           | 127      |                    | Functions, symbol, and everything that cannot be identified is encoded as undefined |
 * | null                | 126      |                    | |
 * | integer             | 125      | writeVarInt        | Only encodes 32 bit signed integers |
 * | float32             | 124      | writeFloat32       | |
 * | float64             | 123      | writeFloat64       | |
 * | bigint              | 122      | writeBigInt64      | |
 * | boolean (false)     | 121      |                    | True and false are different data types so we save the following byte |
 * | boolean (true)      | 120      |                    | - 0b01111000 so the last bit determines whether true or false |
 * | string              | 119      | writeVarString     | |
 * | object<string,any>  | 118      | custom             | Writes {length} then {length} key-value pairs |
 * | array<any>          | 117      | custom             | Writes {length} then {length} json values |
 * | Uint8Array          | 116      | writeVarUint8Array | We use Uint8Array for any kind of binary data |
 *
 * Reasons for the decreasing prefix:
 * We need the first bit for extendability (later we may want to encode the
 * prefix with writeVarUint). The remaining 7 bits are divided as follows:
 * [0-30]   the beginning of the data range is used for custom purposes
 *          (defined by the function that uses this library)
 * [31-127] the end of the data range is used for data encoding by
 *          lib0/encoding.js
 *
 * @param {Encoder} encoder
 * @param {undefined|null|number|bigint|boolean|string|Object<string,any>|Array<any>|Uint8Array} data
 */
const writeAny = (encoder, data) => {
  switch (typeof data) {
    case 'string':
      // TYPE 119: STRING
      write(encoder, 119)
      writeVarString(encoder, data)
      break
    case 'number':
      if (isInteger(data) && abs(data) <= BITS31) {
        // TYPE 125: INTEGER
        write(encoder, 125)
        writeVarInt(encoder, data)
      } else if (isFloat32(data)) {
        // TYPE 124: FLOAT32
        write(encoder, 124)
        writeFloat32(encoder, data)
      } else {
        // TYPE 123: FLOAT64
        write(encoder, 123)
        writeFloat64(encoder, data)
      }
      break
    case 'bigint':
      // TYPE 122: BigInt
      write(encoder, 122)
      writeBigInt64(encoder, data)
      break
    case 'object':
      if (data === null) {
        // TYPE 126: null
        write(encoder, 126)
      } else if (isArray(data)) {
        // TYPE 117: Array
        write(encoder, 117)
        writeVarUint(encoder, data.length)
        for (let i = 0; i < data.length; i++) {
          writeAny(encoder, data[i])
        }
      } else if (data instanceof Uint8Array) {
        // TYPE 116: ArrayBuffer
        write(encoder, 116)
        writeVarUint8Array(encoder, data)
      } else {
        // TYPE 118: Object
        write(encoder, 118)
        const keys = Object.keys(data)
        writeVarUint(encoder, keys.length)
        for (let i = 0; i < keys.length; i++) {
          const key = keys[i]
          writeVarString(encoder, key)
          writeAny(encoder, data[key])
        }
      }
      break
    case 'boolean':
      // TYPE 120/121: boolean (true/false)
      write(encoder, data ? 120 : 121)
      break
    default:
      // TYPE 127: undefined
      write(encoder, 127)
  }
}

/**
 * Now come a few stateful encoder that have their own classes.
 */

/**
 * Basic Run Length Encoder - a basic compression implementation.
 *
 * Encodes [1,1,1,7] to [1,3,7,1] (3 times 1, 1 time 7). This encoder might do more harm than good if there are a lot of values that are not repeated.
 *
 * It was originally used for image compression. Cool .. article http://csbruce.com/cbm/transactor/pdfs/trans_v7_i06.pdf
 *
 * @note T must not be null!
 *
 * @template T
 */
class RleEncoder extends Encoder {
  /**
   * @param {function(Encoder, T):void} writer
   */
  constructor (writer) {
    super()
    /**
     * The writer
     */
    this.w = writer
    /**
     * Current state
     * @type {T|null}
     */
    this.s = null
    this.count = 0
  }

  /**
   * @param {T} v
   */
  write (v) {
    if (this.s === v) {
      this.count++
    } else {
      if (this.count > 0) {
        // flush counter, unless this is the first value (count = 0)
        writeVarUint(this, this.count - 1) // since count is always > 0, we can decrement by one. non-standard encoding ftw
      }
      this.count = 1
      // write first value
      this.w(this, v)
      this.s = v
    }
  }
}

/**
 * Basic diff decoder using variable length encoding.
 *
 * Encodes the values [3, 1100, 1101, 1050, 0] to [3, 1097, 1, -51, -1050] using writeVarInt.
 */
class IntDiffEncoder extends Encoder {
  /**
   * @param {number} start
   */
  constructor (start) {
    super()
    /**
     * Current state
     * @type {number}
     */
    this.s = start
  }

  /**
   * @param {number} v
   */
  write (v) {
    writeVarInt(this, v - this.s)
    this.s = v
  }
}

/**
 * A combination of IntDiffEncoder and RleEncoder.
 *
 * Basically first writes the IntDiffEncoder and then counts duplicate diffs using RleEncoding.
 *
 * Encodes the values [1,1,1,2,3,4,5,6] as [1,1,0,2,1,5] (RLE([1,0,0,1,1,1,1,1]) ⇒ RleIntDiff[1,1,0,2,1,5])
 */
class RleIntDiffEncoder extends Encoder {
  /**
   * @param {number} start
   */
  constructor (start) {
    super()
    /**
     * Current state
     * @type {number}
     */
    this.s = start
    this.count = 0
  }

  /**
   * @param {number} v
   */
  write (v) {
    if (this.s === v && this.count > 0) {
      this.count++
    } else {
      if (this.count > 0) {
        // flush counter, unless this is the first value (count = 0)
        writeVarUint(this, this.count - 1) // since count is always > 0, we can decrement by one. non-standard encoding ftw
      }
      this.count = 1
      // write first value
      writeVarInt(this, v - this.s)
      this.s = v
    }
  }
}

/**
 * @param {UintOptRleEncoder} encoder
 */
const flushUintOptRleEncoder = encoder => {
  if (encoder.count > 0) {
    // flush counter, unless this is the first value (count = 0)
    // case 1: just a single value. set sign to positive
    // case 2: write several values. set sign to negative to indicate that there is a length coming
    writeVarInt(encoder.encoder, encoder.count === 1 ? encoder.s : -encoder.s)
    if (encoder.count > 1) {
      writeVarUint(encoder.encoder, encoder.count - 2) // since count is always > 1, we can decrement by one. non-standard encoding ftw
    }
  }
}

/**
 * Optimized Rle encoder that does not suffer from the mentioned problem of the basic Rle encoder.
 *
 * Internally uses VarInt encoder to write unsigned integers. If the input occurs multiple times, we write
 * write it as a negative number. The UintOptRleDecoder then understands that it needs to read a count.
 *
 * Encodes [1,2,3,3,3] as [1,2,-3,3] (once 1, once 2, three times 3)
 */
class UintOptRleEncoder {
  constructor () {
    this.encoder = new Encoder()
    /**
     * @type {number}
     */
    this.s = 0
    this.count = 0
  }

  /**
   * @param {number} v
   */
  write (v) {
    if (this.s === v) {
      this.count++
    } else {
      flushUintOptRleEncoder(this)
      this.count = 1
      this.s = v
    }
  }

  /**
   * Flush the encoded state and transform this to a Uint8Array.
   *
   * Note that this should only be called once.
   */
  toUint8Array () {
    flushUintOptRleEncoder(this)
    return toUint8Array(this.encoder)
  }
}

/**
 * Increasing Uint Optimized RLE Encoder
 *
 * The RLE encoder counts the number of same occurences of the same value.
 * The IncUintOptRle encoder counts if the value increases.
 * I.e. 7, 8, 9, 10 will be encoded as [-7, 4]. 1, 3, 5 will be encoded
 * as [1, 3, 5].
 */
class IncUintOptRleEncoder {
  constructor () {
    this.encoder = new Encoder()
    /**
     * @type {number}
     */
    this.s = 0
    this.count = 0
  }

  /**
   * @param {number} v
   */
  write (v) {
    if (this.s + this.count === v) {
      this.count++
    } else {
      flushUintOptRleEncoder(this)
      this.count = 1
      this.s = v
    }
  }

  /**
   * Flush the encoded state and transform this to a Uint8Array.
   *
   * Note that this should only be called once.
   */
  toUint8Array () {
    flushUintOptRleEncoder(this)
    return toUint8Array(this.encoder)
  }
}

/**
 * @param {IntDiffOptRleEncoder} encoder
 */
const flushIntDiffOptRleEncoder = encoder => {
  if (encoder.count > 0) {
    //          31 bit making up the diff | wether to write the counter
    // const encodedDiff = encoder.diff << 1 | (encoder.count === 1 ? 0 : 1)
    const encodedDiff = encoder.diff * 2 + (encoder.count === 1 ? 0 : 1)
    // flush counter, unless this is the first value (count = 0)
    // case 1: just a single value. set first bit to positive
    // case 2: write several values. set first bit to negative to indicate that there is a length coming
    writeVarInt(encoder.encoder, encodedDiff)
    if (encoder.count > 1) {
      writeVarUint(encoder.encoder, encoder.count - 2) // since count is always > 1, we can decrement by one. non-standard encoding ftw
    }
  }
}

/**
 * A combination of the IntDiffEncoder and the UintOptRleEncoder.
 *
 * The count approach is similar to the UintDiffOptRleEncoder, but instead of using the negative bitflag, it encodes
 * in the LSB whether a count is to be read. Therefore this Encoder only supports 31 bit integers!
 *
 * Encodes [1, 2, 3, 2] as [3, 1, 6, -1] (more specifically [(1 << 1) | 1, (3 << 0) | 0, -1])
 *
 * Internally uses variable length encoding. Contrary to normal UintVar encoding, the first byte contains:
 * * 1 bit that denotes whether the next value is a count (LSB)
 * * 1 bit that denotes whether this value is negative (MSB - 1)
 * * 1 bit that denotes whether to continue reading the variable length integer (MSB)
 *
 * Therefore, only five bits remain to encode diff ranges.
 *
 * Use this Encoder only when appropriate. In most cases, this is probably a bad idea.
 */
class IntDiffOptRleEncoder {
  constructor () {
    this.encoder = new Encoder()
    /**
     * @type {number}
     */
    this.s = 0
    this.count = 0
    this.diff = 0
  }

  /**
   * @param {number} v
   */
  write (v) {
    if (this.diff === v - this.s) {
      this.s = v
      this.count++
    } else {
      flushIntDiffOptRleEncoder(this)
      this.count = 1
      this.diff = v - this.s
      this.s = v
    }
  }

  /**
   * Flush the encoded state and transform this to a Uint8Array.
   *
   * Note that this should only be called once.
   */
  toUint8Array () {
    flushIntDiffOptRleEncoder(this)
    return toUint8Array(this.encoder)
  }
}

/**
 * Optimized String Encoder.
 *
 * Encoding many small strings in a simple Encoder is not very efficient. The function call to decode a string takes some time and creates references that must be eventually deleted.
 * In practice, when decoding several million small strings, the GC will kick in more and more often to collect orphaned string objects (or maybe there is another reason?).
 *
 * This string encoder solves the above problem. All strings are concatenated and written as a single string using a single encoding call.
 *
 * The lengths are encoded using a UintOptRleEncoder.
 */
class StringEncoder {
  constructor () {
    /**
     * @type {Array<string>}
     */
    this.sarr = []
    this.s = ''
    this.lensE = new UintOptRleEncoder()
  }

  /**
   * @param {string} string
   */
  write (string) {
    this.s += string
    if (this.s.length > 19) {
      this.sarr.push(this.s)
      this.s = ''
    }
    this.lensE.write(string.length)
  }

  toUint8Array () {
    const encoder = new Encoder()
    this.sarr.push(this.s)
    this.s = ''
    writeVarString(encoder, this.sarr.join(''))
    writeUint8Array(encoder, this.lensE.toUint8Array())
    return toUint8Array(encoder)
  }
}

;// ./node_modules/lib0/error.js
/**
 * Error helpers.
 *
 * @module error
 */

/**
 * @param {string} s
 * @return {Error}
 */
/* c8 ignore next */
const error_create = s => new Error(s)

/**
 * @throws {Error}
 * @return {never}
 */
/* c8 ignore next 3 */
const methodUnimplemented = () => {
  throw error_create('Method unimplemented')
}

/**
 * @throws {Error}
 * @return {never}
 */
/* c8 ignore next 3 */
const unexpectedCase = () => {
  throw error_create('Unexpected case')
}

;// ./node_modules/lib0/decoding.js
/**
 * Efficient schema-less binary decoding with support for variable length encoding.
 *
 * Use [lib0/decoding] with [lib0/encoding]. Every encoding function has a corresponding decoding function.
 *
 * Encodes numbers in little-endian order (least to most significant byte order)
 * and is compatible with Golang's binary encoding (https://golang.org/pkg/encoding/binary/)
 * which is also used in Protocol Buffers.
 *
 * ```js
 * // encoding step
 * const encoder = encoding.createEncoder()
 * encoding.writeVarUint(encoder, 256)
 * encoding.writeVarString(encoder, 'Hello world!')
 * const buf = encoding.toUint8Array(encoder)
 * ```
 *
 * ```js
 * // decoding step
 * const decoder = decoding.createDecoder(buf)
 * decoding.readVarUint(decoder) // => 256
 * decoding.readVarString(decoder) // => 'Hello world!'
 * decoding.hasContent(decoder) // => false - all data is read
 * ```
 *
 * @module decoding
 */








const errorUnexpectedEndOfArray = error_create('Unexpected end of array')
const errorIntegerOutOfRange = error_create('Integer out of Range')

/**
 * A Decoder handles the decoding of an Uint8Array.
 */
class Decoder {
  /**
   * @param {Uint8Array} uint8Array Binary data to decode
   */
  constructor (uint8Array) {
    /**
     * Decoding target.
     *
     * @type {Uint8Array}
     */
    this.arr = uint8Array
    /**
     * Current decoding position.
     *
     * @type {number}
     */
    this.pos = 0
  }
}

/**
 * @function
 * @param {Uint8Array} uint8Array
 * @return {Decoder}
 */
const createDecoder = uint8Array => new Decoder(uint8Array)

/**
 * @function
 * @param {Decoder} decoder
 * @return {boolean}
 */
const decoding_hasContent = decoder => decoder.pos !== decoder.arr.length

/**
 * Clone a decoder instance.
 * Optionally set a new position parameter.
 *
 * @function
 * @param {Decoder} decoder The decoder instance
 * @param {number} [newPos] Defaults to current position
 * @return {Decoder} A clone of `decoder`
 */
const clone = (decoder, newPos = decoder.pos) => {
  const _decoder = createDecoder(decoder.arr)
  _decoder.pos = newPos
  return _decoder
}

/**
 * Create an Uint8Array view of the next `len` bytes and advance the position by `len`.
 *
 * Important: The Uint8Array still points to the underlying ArrayBuffer. Make sure to discard the result as soon as possible to prevent any memory leaks.
 *            Use `buffer.copyUint8Array` to copy the result into a new Uint8Array.
 *
 * @function
 * @param {Decoder} decoder The decoder instance
 * @param {number} len The length of bytes to read
 * @return {Uint8Array}
 */
const readUint8Array = (decoder, len) => {
  const view = new Uint8Array(decoder.arr.buffer, decoder.pos + decoder.arr.byteOffset, len)
  decoder.pos += len
  return view
}

/**
 * Read variable length Uint8Array.
 *
 * Important: The Uint8Array still points to the underlying ArrayBuffer. Make sure to discard the result as soon as possible to prevent any memory leaks.
 *            Use `buffer.copyUint8Array` to copy the result into a new Uint8Array.
 *
 * @function
 * @param {Decoder} decoder
 * @return {Uint8Array}
 */
const readVarUint8Array = decoder => readUint8Array(decoder, readVarUint(decoder))

/**
 * Read the rest of the content as an ArrayBuffer
 * @function
 * @param {Decoder} decoder
 * @return {Uint8Array}
 */
const readTailAsUint8Array = decoder => readUint8Array(decoder, decoder.arr.length - decoder.pos)

/**
 * Skip one byte, jump to the next position.
 * @function
 * @param {Decoder} decoder The decoder instance
 * @return {number} The next position
 */
const skip8 = decoder => decoder.pos++

/**
 * Read one byte as unsigned integer.
 * @function
 * @param {Decoder} decoder The decoder instance
 * @return {number} Unsigned 8-bit integer
 */
const readUint8 = decoder => decoder.arr[decoder.pos++]

/**
 * Read 2 bytes as unsigned integer.
 *
 * @function
 * @param {Decoder} decoder
 * @return {number} An unsigned integer.
 */
const readUint16 = decoder => {
  const uint =
    decoder.arr[decoder.pos] +
    (decoder.arr[decoder.pos + 1] << 8)
  decoder.pos += 2
  return uint
}

/**
 * Read 4 bytes as unsigned integer.
 *
 * @function
 * @param {Decoder} decoder
 * @return {number} An unsigned integer.
 */
const readUint32 = decoder => {
  const uint =
    (decoder.arr[decoder.pos] +
    (decoder.arr[decoder.pos + 1] << 8) +
    (decoder.arr[decoder.pos + 2] << 16) +
    (decoder.arr[decoder.pos + 3] << 24)) >>> 0
  decoder.pos += 4
  return uint
}

/**
 * Read 4 bytes as unsigned integer in big endian order.
 * (most significant byte first)
 *
 * @function
 * @param {Decoder} decoder
 * @return {number} An unsigned integer.
 */
const readUint32BigEndian = decoder => {
  const uint =
    (decoder.arr[decoder.pos + 3] +
    (decoder.arr[decoder.pos + 2] << 8) +
    (decoder.arr[decoder.pos + 1] << 16) +
    (decoder.arr[decoder.pos] << 24)) >>> 0
  decoder.pos += 4
  return uint
}

/**
 * Look ahead without incrementing the position
 * to the next byte and read it as unsigned integer.
 *
 * @function
 * @param {Decoder} decoder
 * @return {number} An unsigned integer.
 */
const peekUint8 = decoder => decoder.arr[decoder.pos]

/**
 * Look ahead without incrementing the position
 * to the next byte and read it as unsigned integer.
 *
 * @function
 * @param {Decoder} decoder
 * @return {number} An unsigned integer.
 */
const peekUint16 = decoder =>
  decoder.arr[decoder.pos] +
  (decoder.arr[decoder.pos + 1] << 8)

/**
 * Look ahead without incrementing the position
 * to the next byte and read it as unsigned integer.
 *
 * @function
 * @param {Decoder} decoder
 * @return {number} An unsigned integer.
 */
const peekUint32 = decoder => (
  decoder.arr[decoder.pos] +
  (decoder.arr[decoder.pos + 1] << 8) +
  (decoder.arr[decoder.pos + 2] << 16) +
  (decoder.arr[decoder.pos + 3] << 24)
) >>> 0

/**
 * Read unsigned integer (32bit) with variable length.
 * 1/8th of the storage is used as encoding overhead.
 *  * numbers < 2^7 is stored in one bytlength
 *  * numbers < 2^14 is stored in two bylength
 *
 * @function
 * @param {Decoder} decoder
 * @return {number} An unsigned integer.length
 */
const readVarUint = decoder => {
  let num = 0
  let mult = 1
  const len = decoder.arr.length
  while (decoder.pos < len) {
    const r = decoder.arr[decoder.pos++]
    // num = num | ((r & binary.BITS7) << len)
    num = num + (r & BITS7) * mult // shift $r << (7*#iterations) and add it to num
    mult *= 128 // next iteration, shift 7 "more" to the left
    if (r < BIT8) {
      return num
    }
    /* c8 ignore start */
    if (num > MAX_SAFE_INTEGER) {
      throw errorIntegerOutOfRange
    }
    /* c8 ignore stop */
  }
  throw errorUnexpectedEndOfArray
}

/**
 * Read signed integer (32bit) with variable length.
 * 1/8th of the storage is used as encoding overhead.
 *  * numbers < 2^7 is stored in one bytlength
 *  * numbers < 2^14 is stored in two bylength
 * @todo This should probably create the inverse ~num if number is negative - but this would be a breaking change.
 *
 * @function
 * @param {Decoder} decoder
 * @return {number} An unsigned integer.length
 */
const readVarInt = decoder => {
  let r = decoder.arr[decoder.pos++]
  let num = r & BITS6
  let mult = 64
  const sign = (r & BIT7) > 0 ? -1 : 1
  if ((r & BIT8) === 0) {
    // don't continue reading
    return sign * num
  }
  const len = decoder.arr.length
  while (decoder.pos < len) {
    r = decoder.arr[decoder.pos++]
    // num = num | ((r & binary.BITS7) << len)
    num = num + (r & BITS7) * mult
    mult *= 128
    if (r < BIT8) {
      return sign * num
    }
    /* c8 ignore start */
    if (num > MAX_SAFE_INTEGER) {
      throw errorIntegerOutOfRange
    }
    /* c8 ignore stop */
  }
  throw errorUnexpectedEndOfArray
}

/**
 * Look ahead and read varUint without incrementing position
 *
 * @function
 * @param {Decoder} decoder
 * @return {number}
 */
const peekVarUint = decoder => {
  const pos = decoder.pos
  const s = readVarUint(decoder)
  decoder.pos = pos
  return s
}

/**
 * Look ahead and read varUint without incrementing position
 *
 * @function
 * @param {Decoder} decoder
 * @return {number}
 */
const peekVarInt = decoder => {
  const pos = decoder.pos
  const s = readVarInt(decoder)
  decoder.pos = pos
  return s
}

/**
 * We don't test this function anymore as we use native decoding/encoding by default now.
 * Better not modify this anymore..
 *
 * Transforming utf8 to a string is pretty expensive. The code performs 10x better
 * when String.fromCodePoint is fed with all characters as arguments.
 * But most environments have a maximum number of arguments per functions.
 * For effiency reasons we apply a maximum of 10000 characters at once.
 *
 * @function
 * @param {Decoder} decoder
 * @return {String} The read String.
 */
/* c8 ignore start */
const _readVarStringPolyfill = decoder => {
  let remainingLen = readVarUint(decoder)
  if (remainingLen === 0) {
    return ''
  } else {
    let encodedString = String.fromCodePoint(readUint8(decoder)) // remember to decrease remainingLen
    if (--remainingLen < 100) { // do not create a Uint8Array for small strings
      while (remainingLen--) {
        encodedString += String.fromCodePoint(readUint8(decoder))
      }
    } else {
      while (remainingLen > 0) {
        const nextLen = remainingLen < 10000 ? remainingLen : 10000
        // this is dangerous, we create a fresh array view from the existing buffer
        const bytes = decoder.arr.subarray(decoder.pos, decoder.pos + nextLen)
        decoder.pos += nextLen
        // Starting with ES5.1 we can supply a generic array-like object as arguments
        encodedString += String.fromCodePoint.apply(null, /** @type {any} */ (bytes))
        remainingLen -= nextLen
      }
    }
    return decodeURIComponent(escape(encodedString))
  }
}
/* c8 ignore stop */

/**
 * @function
 * @param {Decoder} decoder
 * @return {String} The read String
 */
const _readVarStringNative = decoder =>
  /** @type any */ (utf8TextDecoder).decode(readVarUint8Array(decoder))

/**
 * Read string of variable length
 * * varUint is used to store the length of the string
 *
 * @function
 * @param {Decoder} decoder
 * @return {String} The read String
 *
 */
/* c8 ignore next */
const readVarString = utf8TextDecoder ? _readVarStringNative : _readVarStringPolyfill

/**
 * @param {Decoder} decoder
 * @return {Uint8Array}
 */
const readTerminatedUint8Array = decoder => {
  const encoder = encoding.createEncoder()
  let b
  while (true) {
    b = readUint8(decoder)
    if (b === 0) {
      return encoding.toUint8Array(encoder)
    }
    if (b === 1) {
      b = readUint8(decoder)
    }
    encoding.write(encoder, b)
  }
}

/**
 * @param {Decoder} decoder
 * @return {string}
 */
const readTerminatedString = decoder => string.decodeUtf8(readTerminatedUint8Array(decoder))

/**
 * Look ahead and read varString without incrementing position
 *
 * @function
 * @param {Decoder} decoder
 * @return {string}
 */
const peekVarString = decoder => {
  const pos = decoder.pos
  const s = readVarString(decoder)
  decoder.pos = pos
  return s
}

/**
 * @param {Decoder} decoder
 * @param {number} len
 * @return {DataView}
 */
const readFromDataView = (decoder, len) => {
  const dv = new DataView(decoder.arr.buffer, decoder.arr.byteOffset + decoder.pos, len)
  decoder.pos += len
  return dv
}

/**
 * @param {Decoder} decoder
 */
const readFloat32 = decoder => readFromDataView(decoder, 4).getFloat32(0, false)

/**
 * @param {Decoder} decoder
 */
const readFloat64 = decoder => readFromDataView(decoder, 8).getFloat64(0, false)

/**
 * @param {Decoder} decoder
 */
const readBigInt64 = decoder => /** @type {any} */ (readFromDataView(decoder, 8)).getBigInt64(0, false)

/**
 * @param {Decoder} decoder
 */
const readBigUint64 = decoder => /** @type {any} */ (readFromDataView(decoder, 8)).getBigUint64(0, false)

/**
 * @type {Array<function(Decoder):any>}
 */
const readAnyLookupTable = [
  decoder => undefined, // CASE 127: undefined
  decoder => null, // CASE 126: null
  readVarInt, // CASE 125: integer
  readFloat32, // CASE 124: float32
  readFloat64, // CASE 123: float64
  readBigInt64, // CASE 122: bigint
  decoder => false, // CASE 121: boolean (false)
  decoder => true, // CASE 120: boolean (true)
  readVarString, // CASE 119: string
  decoder => { // CASE 118: object<string,any>
    const len = readVarUint(decoder)
    /**
     * @type {Object<string,any>}
     */
    const obj = {}
    for (let i = 0; i < len; i++) {
      const key = readVarString(decoder)
      obj[key] = readAny(decoder)
    }
    return obj
  },
  decoder => { // CASE 117: array<any>
    const len = readVarUint(decoder)
    const arr = []
    for (let i = 0; i < len; i++) {
      arr.push(readAny(decoder))
    }
    return arr
  },
  readVarUint8Array // CASE 116: Uint8Array
]

/**
 * @param {Decoder} decoder
 */
const readAny = decoder => readAnyLookupTable[127 - readUint8(decoder)](decoder)

/**
 * T must not be null.
 *
 * @template T
 */
class RleDecoder extends Decoder {
  /**
   * @param {Uint8Array} uint8Array
   * @param {function(Decoder):T} reader
   */
  constructor (uint8Array, reader) {
    super(uint8Array)
    /**
     * The reader
     */
    this.reader = reader
    /**
     * Current state
     * @type {T|null}
     */
    this.s = null
    this.count = 0
  }

  read () {
    if (this.count === 0) {
      this.s = this.reader(this)
      if (decoding_hasContent(this)) {
        this.count = readVarUint(this) + 1 // see encoder implementation for the reason why this is incremented
      } else {
        this.count = -1 // read the current value forever
      }
    }
    this.count--
    return /** @type {T} */ (this.s)
  }
}

class IntDiffDecoder extends Decoder {
  /**
   * @param {Uint8Array} uint8Array
   * @param {number} start
   */
  constructor (uint8Array, start) {
    super(uint8Array)
    /**
     * Current state
     * @type {number}
     */
    this.s = start
  }

  /**
   * @return {number}
   */
  read () {
    this.s += readVarInt(this)
    return this.s
  }
}

class RleIntDiffDecoder extends Decoder {
  /**
   * @param {Uint8Array} uint8Array
   * @param {number} start
   */
  constructor (uint8Array, start) {
    super(uint8Array)
    /**
     * Current state
     * @type {number}
     */
    this.s = start
    this.count = 0
  }

  /**
   * @return {number}
   */
  read () {
    if (this.count === 0) {
      this.s += readVarInt(this)
      if (decoding_hasContent(this)) {
        this.count = readVarUint(this) + 1 // see encoder implementation for the reason why this is incremented
      } else {
        this.count = -1 // read the current value forever
      }
    }
    this.count--
    return /** @type {number} */ (this.s)
  }
}

class UintOptRleDecoder extends Decoder {
  /**
   * @param {Uint8Array} uint8Array
   */
  constructor (uint8Array) {
    super(uint8Array)
    /**
     * @type {number}
     */
    this.s = 0
    this.count = 0
  }

  read () {
    if (this.count === 0) {
      this.s = readVarInt(this)
      // if the sign is negative, we read the count too, otherwise count is 1
      const isNegative = isNegativeZero(this.s)
      this.count = 1
      if (isNegative) {
        this.s = -this.s
        this.count = readVarUint(this) + 2
      }
    }
    this.count--
    return /** @type {number} */ (this.s)
  }
}

class IncUintOptRleDecoder extends Decoder {
  /**
   * @param {Uint8Array} uint8Array
   */
  constructor (uint8Array) {
    super(uint8Array)
    /**
     * @type {number}
     */
    this.s = 0
    this.count = 0
  }

  read () {
    if (this.count === 0) {
      this.s = readVarInt(this)
      // if the sign is negative, we read the count too, otherwise count is 1
      const isNegative = isNegativeZero(this.s)
      this.count = 1
      if (isNegative) {
        this.s = -this.s
        this.count = readVarUint(this) + 2
      }
    }
    this.count--
    return /** @type {number} */ (this.s++)
  }
}

class IntDiffOptRleDecoder extends Decoder {
  /**
   * @param {Uint8Array} uint8Array
   */
  constructor (uint8Array) {
    super(uint8Array)
    /**
     * @type {number}
     */
    this.s = 0
    this.count = 0
    this.diff = 0
  }

  /**
   * @return {number}
   */
  read () {
    if (this.count === 0) {
      const diff = readVarInt(this)
      // if the first bit is set, we read more data
      const hasCount = diff & 1
      this.diff = floor(diff / 2) // shift >> 1
      this.count = 1
      if (hasCount) {
        this.count = readVarUint(this) + 2
      }
    }
    this.s += this.diff
    this.count--
    return this.s
  }
}

class StringDecoder {
  /**
   * @param {Uint8Array} uint8Array
   */
  constructor (uint8Array) {
    this.decoder = new UintOptRleDecoder(uint8Array)
    this.str = readVarString(this.decoder)
    /**
     * @type {number}
     */
    this.spos = 0
  }

  /**
   * @return {string}
   */
  read () {
    const end = this.spos + this.decoder.read()
    const res = this.str.slice(this.spos, end)
    this.spos = end
    return res
  }
}

;// ./node_modules/lib0/webcrypto.js
/* eslint-env browser */

const subtle = crypto.subtle
const webcrypto_getRandomValues = crypto.getRandomValues.bind(crypto)

;// ./node_modules/lib0/random.js
/**
 * Isomorphic module for true random numbers / buffers / uuids.
 *
 * Attention: falls back to Math.random if the browser does not support crypto.
 *
 * @module random
 */





const rand = Math.random

const uint32 = () => webcrypto_getRandomValues(new Uint32Array(1))[0]

const uint53 = () => {
  const arr = getRandomValues(new Uint32Array(8))
  return (arr[0] & binary.BITS21) * (binary.BITS32 + 1) + (arr[1] >>> 0)
}

/**
 * @template T
 * @param {Array<T>} arr
 * @return {T}
 */
const oneOf = arr => arr[math.floor(rand() * arr.length)]

// @ts-ignore
const uuidv4Template = [1e7] + -1e3 + -4e3 + -8e3 + -1e11

/**
 * @return {string}
 */
const uuidv4 = () => uuidv4Template.replace(/[018]/g, /** @param {number} c */ c =>
  (c ^ uint32() & 15 >> c / 4).toString(16)
)

;// ./node_modules/lib0/promise.js
/**
 * Utility helpers to work with promises.
 *
 * @module promise
 */



/**
 * @template T
 * @callback PromiseResolve
 * @param {T|PromiseLike<T>} [result]
 */

/**
 * @template T
 * @param {function(PromiseResolve<T>,function(Error):void):any} f
 * @return {Promise<T>}
 */
const promise_create = f => /** @type {Promise<T>} */ (new Promise(f))

/**
 * @param {function(function():void,function(Error):void):void} f
 * @return {Promise<void>}
 */
const createEmpty = f => new Promise(f)

/**
 * `Promise.all` wait for all promises in the array to resolve and return the result
 * @template {unknown[] | []} PS
 *
 * @param {PS} ps
 * @return {Promise<{ -readonly [P in keyof PS]: Awaited<PS[P]> }>}
 */
const promise_all = Promise.all.bind(Promise)

/**
 * @param {Error} [reason]
 * @return {Promise<never>}
 */
const reject = reason => Promise.reject(reason)

/**
 * @template T
 * @param {T|void} res
 * @return {Promise<T|void>}
 */
const resolve = res => Promise.resolve(res)

/**
 * @template T
 * @param {T} res
 * @return {Promise<T>}
 */
const resolveWith = res => Promise.resolve(res)

/**
 * @todo Next version, reorder parameters: check, [timeout, [intervalResolution]]
 * @deprecated use untilAsync instead
 *
 * @param {number} timeout
 * @param {function():boolean} check
 * @param {number} [intervalResolution]
 * @return {Promise<void>}
 */
const until = (timeout, check, intervalResolution = 10) => promise_create((resolve, reject) => {
  const startTime = time.getUnixTime()
  const hasTimeout = timeout > 0
  const untilInterval = () => {
    if (check()) {
      clearInterval(intervalHandle)
      resolve()
    } else if (hasTimeout) {
      /* c8 ignore else */
      if (time.getUnixTime() - startTime > timeout) {
        clearInterval(intervalHandle)
        reject(new Error('Timeout'))
      }
    }
  }
  const intervalHandle = setInterval(untilInterval, intervalResolution)
})

/**
 * @param {()=>Promise<boolean>|boolean} check
 * @param {number} timeout
 * @param {number} intervalResolution
 * @return {Promise<void>}
 */
const untilAsync = async (check, timeout = 0, intervalResolution = 10) => {
  const startTime = time.getUnixTime()
  const noTimeout = timeout <= 0
  // eslint-disable-next-line no-unmodified-loop-condition
  while (noTimeout || time.getUnixTime() - startTime <= timeout) {
    if (await check()) return
    await wait(intervalResolution)
  }
  throw new Error('Timeout')
}

/**
 * @param {number} timeout
 * @return {Promise<undefined>}
 */
const wait = timeout => promise_create((resolve, _reject) => setTimeout(resolve, timeout))

/**
 * Checks if an object is a promise using ducktyping.
 *
 * Promises are often polyfilled, so it makes sense to add some additional guarantees if the user of this
 * library has some insane environment where global Promise objects are overwritten.
 *
 * @param {any} p
 * @return {boolean}
 */
const isPromise = p => p instanceof Promise || (p && p.then && p.catch && p.finally)

;// ./node_modules/lib0/function.js
/**
 * Common functions and function call helpers.
 *
 * @module function
 */





/**
 * Calls all functions in `fs` with args. Only throws after all functions were called.
 *
 * @param {Array<function>} fs
 * @param {Array<any>} args
 */
const callAll = (fs, args, i = 0) => {
  try {
    for (; i < fs.length; i++) {
      fs[i](...args)
    }
  } finally {
    if (i < fs.length) {
      callAll(fs, args, i + 1)
    }
  }
}

const nop = () => {}

/**
 * @template T
 * @param {function():T} f
 * @return {T}
 */
const apply = f => f()

/**
 * @template A
 *
 * @param {A} a
 * @return {A}
 */
const id = a => a

/**
 * @template T
 *
 * @param {T} a
 * @param {T} b
 * @return {boolean}
 */
const equalityStrict = (a, b) => a === b

/**
 * @template T
 *
 * @param {Array<T>|object} a
 * @param {Array<T>|object} b
 * @return {boolean}
 */
const equalityFlat = (a, b) => a === b || (a != null && b != null && a.constructor === b.constructor && ((array.isArray(a) && array.equalFlat(a, /** @type {Array<T>} */ (b))) || (typeof a === 'object' && object.equalFlat(a, b))))

/* c8 ignore start */

/**
 * @param {any} a
 * @param {any} b
 * @return {boolean}
 */
const equalityDeep = (a, b) => {
  if (a === b) {
    return true
  }
  if (a == null || b == null || a.constructor !== b.constructor) {
    return false
  }
  if (a[traits.EqualityTraitSymbol] != null) {
    return a[traits.EqualityTraitSymbol](b)
  }
  switch (a.constructor) {
    case ArrayBuffer:
      a = new Uint8Array(a)
      b = new Uint8Array(b)
    // eslint-disable-next-line no-fallthrough
    case Uint8Array: {
      if (a.byteLength !== b.byteLength) {
        return false
      }
      for (let i = 0; i < a.length; i++) {
        if (a[i] !== b[i]) {
          return false
        }
      }
      break
    }
    case Set: {
      if (a.size !== b.size) {
        return false
      }
      for (const value of a) {
        if (!b.has(value)) {
          return false
        }
      }
      break
    }
    case Map: {
      if (a.size !== b.size) {
        return false
      }
      for (const key of a.keys()) {
        if (!b.has(key) || !equalityDeep(a.get(key), b.get(key))) {
          return false
        }
      }
      break
    }
    case Object:
      if (object.length(a) !== object.length(b)) {
        return false
      }
      for (const key in a) {
        if (!object.hasProperty(a, key) || !equalityDeep(a[key], b[key])) {
          return false
        }
      }
      break
    case Array:
      if (a.length !== b.length) {
        return false
      }
      for (let i = 0; i < a.length; i++) {
        if (!equalityDeep(a[i], b[i])) {
          return false
        }
      }
      break
    default:
      return false
  }
  return true
}

/**
 * @template V
 * @template {V} OPTS
 *
 * @param {V} value
 * @param {Array<OPTS>} options
 */
// @ts-ignore
const isOneOf = (value, options) => options.includes(value)
/* c8 ignore stop */

const function_isArray = isArray

/**
 * @param {any} s
 * @return {s is String}
 */
const isString = (s) => s && s.constructor === String

/**
 * @param {any} n
 * @return {n is Number}
 */
const isNumber = n => n != null && n.constructor === Number

/**
 * @template {abstract new (...args: any) => any} TYPE
 * @param {any} n
 * @param {TYPE} T
 * @return {n is InstanceType<TYPE>}
 */
const is = (n, T) => n && n.constructor === T

/**
 * @template {abstract new (...args: any) => any} TYPE
 * @param {TYPE} T
 */
const isTemplate = (T) =>
  /**
   * @param {any} n
   * @return {n is InstanceType<TYPE>}
   **/
  n => n && n.constructor === T

;// ./node_modules/lib0/conditions.js
/**
 * Often used conditions.
 *
 * @module conditions
 */

/**
 * @template T
 * @param {T|null|undefined} v
 * @return {T|null}
 */
/* c8 ignore next */
const undefinedToNull = v => v === undefined ? null : v

;// ./node_modules/lib0/storage.js
/* eslint-env browser */

/**
 * Isomorphic variable storage.
 *
 * Uses LocalStorage in the browser and falls back to in-memory storage.
 *
 * @module storage
 */

/* c8 ignore start */
class VarStoragePolyfill {
  constructor () {
    this.map = new Map()
  }

  /**
   * @param {string} key
   * @param {any} newValue
   */
  setItem (key, newValue) {
    this.map.set(key, newValue)
  }

  /**
   * @param {string} key
   */
  getItem (key) {
    return this.map.get(key)
  }
}
/* c8 ignore stop */

/**
 * @type {any}
 */
let _localStorage = new VarStoragePolyfill()
let usePolyfill = true

/* c8 ignore start */
try {
  // if the same-origin rule is violated, accessing localStorage might thrown an error
  if (typeof localStorage !== 'undefined' && localStorage) {
    _localStorage = localStorage
    usePolyfill = false
  }
} catch (e) { }
/* c8 ignore stop */

/**
 * This is basically localStorage in browser, or a polyfill in nodejs
 */
/* c8 ignore next */
const varStorage = _localStorage

/**
 * A polyfill for `addEventListener('storage', event => {..})` that does nothing if the polyfill is being used.
 *
 * @param {function({ key: string, newValue: string, oldValue: string }): void} eventHandler
 * @function
 */
/* c8 ignore next */
const onChange = eventHandler => usePolyfill || addEventListener('storage', /** @type {any} */ (eventHandler))

/**
 * A polyfill for `removeEventListener('storage', event => {..})` that does nothing if the polyfill is being used.
 *
 * @param {function({ key: string, newValue: string, oldValue: string }): void} eventHandler
 * @function
 */
/* c8 ignore next */
const offChange = eventHandler => usePolyfill || removeEventListener('storage', /** @type {any} */ (eventHandler))

;// ./node_modules/lib0/environment.js
/**
 * Isomorphic module to work access the environment (query params, env variables).
 *
 * @module environment
 */







/* c8 ignore next 2 */
// @ts-ignore
const isNode = typeof process !== 'undefined' && process.release && /node|io\.js/.test(process.release.name) && Object.prototype.toString.call(typeof process !== 'undefined' ? process : 0) === '[object process]'

/* c8 ignore next */
const isBrowser = typeof window !== 'undefined' && typeof document !== 'undefined' && !isNode
/* c8 ignore next 3 */
const isMac = typeof navigator !== 'undefined'
  ? /Mac/.test(navigator.platform)
  : false

/**
 * @type {Map<string,string>}
 */
let params
const args = []

/* c8 ignore start */
const computeParams = () => {
  if (params === undefined) {
    if (isNode) {
      params = create()
      const pargs = process.argv
      let currParamName = null
      for (let i = 0; i < pargs.length; i++) {
        const parg = pargs[i]
        if (parg[0] === '-') {
          if (currParamName !== null) {
            params.set(currParamName, '')
          }
          currParamName = parg
        } else {
          if (currParamName !== null) {
            params.set(currParamName, parg)
            currParamName = null
          } else {
            args.push(parg)
          }
        }
      }
      if (currParamName !== null) {
        params.set(currParamName, '')
      }
      // in ReactNative for example this would not be true (unless connected to the Remote Debugger)
    } else if (typeof location === 'object') {
      params = create(); // eslint-disable-next-line no-undef
      (location.search || '?').slice(1).split('&').forEach((kv) => {
        if (kv.length !== 0) {
          const [key, value] = kv.split('=')
          params.set(`--${fromCamelCase(key, '-')}`, value)
          params.set(`-${fromCamelCase(key, '-')}`, value)
        }
      })
    } else {
      params = create()
    }
  }
  return params
}
/* c8 ignore stop */

/**
 * @param {string} name
 * @return {boolean}
 */
/* c8 ignore next */
const hasParam = (name) => computeParams().has(name)

/**
 * @param {string} name
 * @param {string} defaultVal
 * @return {string}
 */
/* c8 ignore next 2 */
const getParam = (name, defaultVal) =>
  computeParams().get(name) || defaultVal

/**
 * @param {string} name
 * @return {string|null}
 */
/* c8 ignore next 4 */
const getVariable = (name) =>
  isNode
    ? undefinedToNull(process.env[name.toUpperCase().replaceAll('-', '_')])
    : undefinedToNull(varStorage.getItem(name))

/**
 * @param {string} name
 * @return {string|null}
 */
/* c8 ignore next 2 */
const getConf = (name) =>
  computeParams().get('--' + name) || getVariable(name)

/**
 * @param {string} name
 * @return {string}
 */
/* c8 ignore next 5 */
const ensureConf = (name) => {
  const c = getConf(name)
  if (c == null) throw new Error(`Expected configuration "${name.toUpperCase().replaceAll('-', '_')}"`)
  return c
}

/**
 * @param {string} name
 * @return {boolean}
 */
/* c8 ignore next 2 */
const hasConf = (name) =>
  hasParam('--' + name) || getVariable(name) !== null

/* c8 ignore next */
const production = hasConf('production')

/* c8 ignore next 2 */
const forceColor = isNode &&
  isOneOf(process.env.FORCE_COLOR, ['true', '1', '2'])

/* c8 ignore start */
/**
 * Color is enabled by default if the terminal supports it.
 *
 * Explicitly enable color using `--color` parameter
 * Disable color using `--no-color` parameter or using `NO_COLOR=1` environment variable.
 * `FORCE_COLOR=1` enables color and takes precedence over all.
 */
const supportsColor = forceColor || (
  !hasParam('--no-colors') && // @todo deprecate --no-colors
  !hasConf('no-color') &&
  (!isNode || process.stdout.isTTY) && (
    !isNode ||
    hasParam('--color') ||
    getVariable('COLORTERM') !== null ||
    (getVariable('TERM') || '').includes('color')
  )
)
/* c8 ignore stop */

;// ./node_modules/lib0/pair.js
/**
 * Working with value pairs.
 *
 * @module pair
 */

/**
 * @template L,R
 */
class Pair {
  /**
   * @param {L} left
   * @param {R} right
   */
  constructor (left, right) {
    this.left = left
    this.right = right
  }
}

/**
 * @template L,R
 * @param {L} left
 * @param {R} right
 * @return {Pair<L,R>}
 */
const pair_create = (left, right) => new Pair(left, right)

/**
 * @template L,R
 * @param {R} right
 * @param {L} left
 * @return {Pair<L,R>}
 */
const createReversed = (right, left) => new Pair(left, right)

/**
 * @template L,R
 * @param {Array<Pair<L,R>>} arr
 * @param {function(L, R):any} f
 */
const forEach = (arr, f) => arr.forEach(p => f(p.left, p.right))

/**
 * @template L,R,X
 * @param {Array<Pair<L,R>>} arr
 * @param {function(L, R):X} f
 * @return {Array<X>}
 */
const pair_map = (arr, f) => arr.map(p => f(p.left, p.right))

;// ./node_modules/lib0/dom.js
/* eslint-env browser */

/**
 * Utility module to work with the DOM.
 *
 * @module dom
 */




/* c8 ignore start */
/**
 * @type {Document}
 */
const doc = /** @type {Document} */ (typeof document !== 'undefined' ? document : {})

/**
 * @param {string} name
 * @return {HTMLElement}
 */
const createElement = name => doc.createElement(name)

/**
 * @return {DocumentFragment}
 */
const createDocumentFragment = () => doc.createDocumentFragment()

/**
 * @param {string} text
 * @return {Text}
 */
const createTextNode = text => doc.createTextNode(text)

const domParser = /** @type {DOMParser} */ (typeof DOMParser !== 'undefined' ? new DOMParser() : null)

/**
 * @param {HTMLElement} el
 * @param {string} name
 * @param {Object} opts
 */
const emitCustomEvent = (el, name, opts) => el.dispatchEvent(new CustomEvent(name, opts))

/**
 * @param {Element} el
 * @param {Array<pair.Pair<string,string|boolean>>} attrs Array of key-value pairs
 * @return {Element}
 */
const setAttributes = (el, attrs) => {
  pair.forEach(attrs, (key, value) => {
    if (value === false) {
      el.removeAttribute(key)
    } else if (value === true) {
      el.setAttribute(key, '')
    } else {
      // @ts-ignore
      el.setAttribute(key, value)
    }
  })
  return el
}

/**
 * @param {Element} el
 * @param {Map<string, string>} attrs Array of key-value pairs
 * @return {Element}
 */
const setAttributesMap = (el, attrs) => {
  attrs.forEach((value, key) => { el.setAttribute(key, value) })
  return el
}

/**
 * @param {Array<Node>|HTMLCollection} children
 * @return {DocumentFragment}
 */
const fragment = children => {
  const fragment = createDocumentFragment()
  for (let i = 0; i < children.length; i++) {
    appendChild(fragment, children[i])
  }
  return fragment
}

/**
 * @param {Element} parent
 * @param {Array<Node>} nodes
 * @return {Element}
 */
const append = (parent, nodes) => {
  appendChild(parent, fragment(nodes))
  return parent
}

/**
 * @param {HTMLElement} el
 */
const remove = el => el.remove()

/**
 * @param {EventTarget} el
 * @param {string} name
 * @param {EventListener} f
 */
const dom_addEventListener = (el, name, f) => el.addEventListener(name, f)

/**
 * @param {EventTarget} el
 * @param {string} name
 * @param {EventListener} f
 */
const dom_removeEventListener = (el, name, f) => el.removeEventListener(name, f)

/**
 * @param {Node} node
 * @param {Array<pair.Pair<string,EventListener>>} listeners
 * @return {Node}
 */
const addEventListeners = (node, listeners) => {
  pair.forEach(listeners, (name, f) => dom_addEventListener(node, name, f))
  return node
}

/**
 * @param {Node} node
 * @param {Array<pair.Pair<string,EventListener>>} listeners
 * @return {Node}
 */
const removeEventListeners = (node, listeners) => {
  pair.forEach(listeners, (name, f) => dom_removeEventListener(node, name, f))
  return node
}

/**
 * @param {string} name
 * @param {Array<pair.Pair<string,string>|pair.Pair<string,boolean>>} attrs Array of key-value pairs
 * @param {Array<Node>} children
 * @return {Element}
 */
const dom_element = (name, attrs = [], children = []) =>
  append(setAttributes(createElement(name), attrs), children)

/**
 * @param {number} width
 * @param {number} height
 */
const canvas = (width, height) => {
  const c = /** @type {HTMLCanvasElement} */ (createElement('canvas'))
  c.height = height
  c.width = width
  return c
}

/**
 * @param {string} t
 * @return {Text}
 */
const dom_text = (/* unused pure expression or super */ null && (createTextNode))

/**
 * @param {pair.Pair<string,string>} pair
 */
const pairToStyleString = pair => `${pair.left}:${pair.right};`

/**
 * @param {Array<pair.Pair<string,string>>} pairs
 * @return {string}
 */
const pairsToStyleString = pairs => pairs.map(pairToStyleString).join('')

/**
 * @param {Map<string,string>} m
 * @return {string}
 */
const mapToStyleString = m => map_map(m, (value, key) => `${key}:${value};`).join('')

/**
 * @todo should always query on a dom element
 *
 * @param {HTMLElement|ShadowRoot} el
 * @param {string} query
 * @return {HTMLElement | null}
 */
const querySelector = (el, query) => el.querySelector(query)

/**
 * @param {HTMLElement|ShadowRoot} el
 * @param {string} query
 * @return {NodeListOf<HTMLElement>}
 */
const querySelectorAll = (el, query) => el.querySelectorAll(query)

/**
 * @param {string} id
 * @return {HTMLElement}
 */
const getElementById = id => /** @type {HTMLElement} */ (doc.getElementById(id))

/**
 * @param {string} html
 * @return {HTMLElement}
 */
const _parse = html => domParser.parseFromString(`<html><body>${html}</body></html>`, 'text/html').body

/**
 * @param {string} html
 * @return {DocumentFragment}
 */
const parseFragment = html => fragment(/** @type {any} */ (_parse(html).childNodes))

/**
 * @param {string} html
 * @return {HTMLElement}
 */
const parseElement = html => /** @type HTMLElement */ (_parse(html).firstElementChild)

/**
 * @param {HTMLElement} oldEl
 * @param {HTMLElement|DocumentFragment} newEl
 */
const replaceWith = (oldEl, newEl) => oldEl.replaceWith(newEl)

/**
 * @param {HTMLElement} parent
 * @param {HTMLElement} el
 * @param {Node|null} ref
 * @return {HTMLElement}
 */
const insertBefore = (parent, el, ref) => parent.insertBefore(el, ref)

/**
 * @param {Node} parent
 * @param {Node} child
 * @return {Node}
 */
const appendChild = (parent, child) => parent.appendChild(child)

const ELEMENT_NODE = doc.ELEMENT_NODE
const TEXT_NODE = doc.TEXT_NODE
const CDATA_SECTION_NODE = doc.CDATA_SECTION_NODE
const COMMENT_NODE = doc.COMMENT_NODE
const DOCUMENT_NODE = doc.DOCUMENT_NODE
const DOCUMENT_TYPE_NODE = doc.DOCUMENT_TYPE_NODE
const DOCUMENT_FRAGMENT_NODE = doc.DOCUMENT_FRAGMENT_NODE

/**
 * @param {any} node
 * @param {number} type
 */
const checkNodeType = (node, type) => node.nodeType === type

/**
 * @param {Node} parent
 * @param {HTMLElement} child
 */
const isParentOf = (parent, child) => {
  let p = child.parentNode
  while (p && p !== parent) {
    p = p.parentNode
  }
  return p === parent
}
/* c8 ignore stop */

;// ./node_modules/lib0/symbol.js
/**
 * Utility module to work with EcmaScript Symbols.
 *
 * @module symbol
 */

/**
 * Return fresh symbol.
 */
const symbol_create = Symbol

/**
 * @param {any} s
 * @return {boolean}
 */
const isSymbol = s => typeof s === 'symbol'

;// ./node_modules/lib0/time.js
/**
 * Utility module to work with time.
 *
 * @module time
 */




/**
 * Return current time.
 *
 * @return {Date}
 */
const getDate = () => new Date()

/**
 * Return current unix time.
 *
 * @return {number}
 */
const getUnixTime = Date.now

/**
 * Transform time (in ms) to a human readable format. E.g. 1100 => 1.1s. 60s => 1min. .001 => 10μs.
 *
 * @param {number} d duration in milliseconds
 * @return {string} humanized approximation of time
 */
const humanizeDuration = d => {
  if (d < 60000) {
    const p = metric.prefix(d, -1)
    return math.round(p.n * 100) / 100 + p.prefix + 's'
  }
  d = math.floor(d / 1000)
  const seconds = d % 60
  const minutes = math.floor(d / 60) % 60
  const hours = math.floor(d / 3600) % 24
  const days = math.floor(d / 86400)
  if (days > 0) {
    return days + 'd' + ((hours > 0 || minutes > 30) ? ' ' + (minutes > 30 ? hours + 1 : hours) + 'h' : '')
  }
  if (hours > 0) {
    /* c8 ignore next */
    return hours + 'h' + ((minutes > 0 || seconds > 30) ? ' ' + (seconds > 30 ? minutes + 1 : minutes) + 'min' : '')
  }
  return minutes + 'min' + (seconds > 0 ? ' ' + seconds + 's' : '')
}

;// ./node_modules/lib0/logging.common.js






const BOLD = symbol_create()
const UNBOLD = symbol_create()
const BLUE = symbol_create()
const GREY = symbol_create()
const GREEN = symbol_create()
const RED = symbol_create()
const PURPLE = symbol_create()
const ORANGE = symbol_create()
const UNCOLOR = symbol_create()

/* c8 ignore start */
/**
 * @param {Array<undefined|string|Symbol|Object|number|function():any>} args
 * @return {Array<string|object|number|undefined>}
 */
const computeNoColorLoggingArgs = args => {
  if (args.length === 1 && args[0]?.constructor === Function) {
    args = /** @type {Array<string|Symbol|Object|number>} */ (/** @type {[function]} */ (args)[0]())
  }
  const strBuilder = []
  const logArgs = []
  // try with formatting until we find something unsupported
  let i = 0
  for (; i < args.length; i++) {
    const arg = args[i]
    if (arg === undefined) {
      break
    } else if (arg.constructor === String || arg.constructor === Number) {
      strBuilder.push(arg)
    } else if (arg.constructor === Object) {
      break
    }
  }
  if (i > 0) {
    // create logArgs with what we have so far
    logArgs.push(strBuilder.join(''))
  }
  // append the rest
  for (; i < args.length; i++) {
    const arg = args[i]
    if (!(arg instanceof Symbol)) {
      logArgs.push(arg)
    }
  }
  return logArgs
}
/* c8 ignore stop */

const loggingColors = [GREEN, PURPLE, ORANGE, BLUE]
let nextColor = 0
let lastLoggingTime = getUnixTime()

/* c8 ignore start */
/**
 * @param {function(...any):void} _print
 * @param {string} moduleName
 * @return {function(...any):void}
 */
const createModuleLogger = (_print, moduleName) => {
  const color = loggingColors[nextColor]
  const debugRegexVar = env.getVariable('log')
  const doLogging = debugRegexVar !== null &&
    (debugRegexVar === '*' || debugRegexVar === 'true' ||
      new RegExp(debugRegexVar, 'gi').test(moduleName))
  nextColor = (nextColor + 1) % loggingColors.length
  moduleName += ': '
  return !doLogging
    ? func.nop
    : (...args) => {
        if (args.length === 1 && args[0]?.constructor === Function) {
          args = args[0]()
        }
        const timeNow = time.getUnixTime()
        const timeDiff = timeNow - lastLoggingTime
        lastLoggingTime = timeNow
        _print(
          color,
          moduleName,
          UNCOLOR,
          ...args.map((arg) => {
            if (arg != null && arg.constructor === Uint8Array) {
              arg = Array.from(arg)
            }
            const t = typeof arg
            switch (t) {
              case 'string':
              case 'symbol':
                return arg
              default: {
                return json.stringify(arg)
              }
            }
          }),
          color,
          ' +' + timeDiff + 'ms'
        )
      }
}
/* c8 ignore stop */

;// ./node_modules/lib0/logging.js
/**
 * Isomorphic logging module with support for colors!
 *
 * @module logging
 */













/**
 * @type {Object<Symbol,pair.Pair<string,string>>}
 */
const _browserStyleMap = {
  [BOLD]: pair_create('font-weight', 'bold'),
  [UNBOLD]: pair_create('font-weight', 'normal'),
  [BLUE]: pair_create('color', 'blue'),
  [GREEN]: pair_create('color', 'green'),
  [GREY]: pair_create('color', 'grey'),
  [RED]: pair_create('color', 'red'),
  [PURPLE]: pair_create('color', 'purple'),
  [ORANGE]: pair_create('color', 'orange'), // not well supported in chrome when debugging node with inspector - TODO: deprecate
  [UNCOLOR]: pair_create('color', 'black')
}

/**
 * @param {Array<string|Symbol|Object|number|function():any>} args
 * @return {Array<string|object|number>}
 */
/* c8 ignore start */
const computeBrowserLoggingArgs = (args) => {
  if (args.length === 1 && args[0]?.constructor === Function) {
    args = /** @type {Array<string|Symbol|Object|number>} */ (/** @type {[function]} */ (args)[0]())
  }
  const strBuilder = []
  const styles = []
  const currentStyle = create()
  /**
   * @type {Array<string|Object|number>}
   */
  let logArgs = []
  // try with formatting until we find something unsupported
  let i = 0
  for (; i < args.length; i++) {
    const arg = args[i]
    // @ts-ignore
    const style = _browserStyleMap[arg]
    if (style !== undefined) {
      currentStyle.set(style.left, style.right)
    } else {
      if (arg === undefined) {
        break
      }
      if (arg.constructor === String || arg.constructor === Number) {
        const style = mapToStyleString(currentStyle)
        if (i > 0 || style.length > 0) {
          strBuilder.push('%c' + arg)
          styles.push(style)
        } else {
          strBuilder.push(arg)
        }
      } else {
        break
      }
    }
  }
  if (i > 0) {
    // create logArgs with what we have so far
    logArgs = styles
    logArgs.unshift(strBuilder.join(''))
  }
  // append the rest
  for (; i < args.length; i++) {
    const arg = args[i]
    if (!(arg instanceof Symbol)) {
      logArgs.push(arg)
    }
  }
  return logArgs
}
/* c8 ignore stop */

/* c8 ignore start */
const computeLoggingArgs = supportsColor
  ? computeBrowserLoggingArgs
  : computeNoColorLoggingArgs
/* c8 ignore stop */

/**
 * @param {Array<string|Symbol|Object|number>} args
 */
const print = (...args) => {
  console.log(...computeLoggingArgs(args))
  /* c8 ignore next */
  vconsoles.forEach((vc) => vc.print(args))
}

/* c8 ignore start */
/**
 * @param {Array<string|Symbol|Object|number>} args
 */
const warn = (...args) => {
  console.warn(...computeLoggingArgs(args))
  args.unshift(ORANGE)
  vconsoles.forEach((vc) => vc.print(args))
}
/* c8 ignore stop */

/**
 * @param {Error} err
 */
/* c8 ignore start */
const printError = (err) => {
  console.error(err)
  vconsoles.forEach((vc) => vc.printError(err))
}
/* c8 ignore stop */

/**
 * @param {string} url image location
 * @param {number} height height of the image in pixel
 */
/* c8 ignore start */
const printImg = (url, height) => {
  if (env.isBrowser) {
    console.log(
      '%c                      ',
      `font-size: ${height}px; background-size: contain; background-repeat: no-repeat; background-image: url(${url})`
    )
    // console.log('%c                ', `font-size: ${height}x; background: url(${url}) no-repeat;`)
  }
  vconsoles.forEach((vc) => vc.printImg(url, height))
}
/* c8 ignore stop */

/**
 * @param {string} base64
 * @param {number} height
 */
/* c8 ignore next 2 */
const printImgBase64 = (base64, height) =>
  printImg(`data:image/gif;base64,${base64}`, height)

/**
 * @param {Array<string|Symbol|Object|number>} args
 */
const group = (...args) => {
  console.group(...computeLoggingArgs(args))
  /* c8 ignore next */
  vconsoles.forEach((vc) => vc.group(args))
}

/**
 * @param {Array<string|Symbol|Object|number>} args
 */
const groupCollapsed = (...args) => {
  console.groupCollapsed(...computeLoggingArgs(args))
  /* c8 ignore next */
  vconsoles.forEach((vc) => vc.groupCollapsed(args))
}

const groupEnd = () => {
  console.groupEnd()
  /* c8 ignore next */
  vconsoles.forEach((vc) => vc.groupEnd())
}

/**
 * @param {function():Node} createNode
 */
/* c8 ignore next 2 */
const printDom = (createNode) =>
  vconsoles.forEach((vc) => vc.printDom(createNode()))

/**
 * @param {HTMLCanvasElement} canvas
 * @param {number} height
 */
/* c8 ignore next 2 */
const printCanvas = (canvas, height) =>
  printImg(canvas.toDataURL(), height)

const vconsoles = set_create()

/**
 * @param {Array<string|Symbol|Object|number>} args
 * @return {Array<Element>}
 */
/* c8 ignore start */
const _computeLineSpans = (args) => {
  const spans = []
  const currentStyle = new Map()
  // try with formatting until we find something unsupported
  let i = 0
  for (; i < args.length; i++) {
    let arg = args[i]
    // @ts-ignore
    const style = _browserStyleMap[arg]
    if (style !== undefined) {
      currentStyle.set(style.left, style.right)
    } else {
      if (arg === undefined) {
        arg = 'undefined '
      }
      if (arg.constructor === String || arg.constructor === Number) {
        // @ts-ignore
        const span = dom.element('span', [
          pair.create('style', dom.mapToStyleString(currentStyle))
        ], [dom.text(arg.toString())])
        if (span.innerHTML === '') {
          span.innerHTML = '&nbsp;'
        }
        spans.push(span)
      } else {
        break
      }
    }
  }
  // append the rest
  for (; i < args.length; i++) {
    let content = args[i]
    if (!(content instanceof Symbol)) {
      if (content.constructor !== String && content.constructor !== Number) {
        content = ' ' + json.stringify(content) + ' '
      }
      spans.push(
        dom.element('span', [], [dom.text(/** @type {string} */ (content))])
      )
    }
  }
  return spans
}
/* c8 ignore stop */

const lineStyle =
  'font-family:monospace;border-bottom:1px solid #e2e2e2;padding:2px;'

/* c8 ignore start */
class VConsole {
  /**
   * @param {Element} dom
   */
  constructor (dom) {
    this.dom = dom
    /**
     * @type {Element}
     */
    this.ccontainer = this.dom
    this.depth = 0
    vconsoles.add(this)
  }

  /**
   * @param {Array<string|Symbol|Object|number>} args
   * @param {boolean} collapsed
   */
  group (args, collapsed = false) {
    eventloop.enqueue(() => {
      const triangleDown = dom.element('span', [
        pair.create('hidden', collapsed),
        pair.create('style', 'color:grey;font-size:120%;')
      ], [dom.text('▼')])
      const triangleRight = dom.element('span', [
        pair.create('hidden', !collapsed),
        pair.create('style', 'color:grey;font-size:125%;')
      ], [dom.text('▶')])
      const content = dom.element(
        'div',
        [pair.create(
          'style',
          `${lineStyle};padding-left:${this.depth * 10}px`
        )],
        [triangleDown, triangleRight, dom.text(' ')].concat(
          _computeLineSpans(args)
        )
      )
      const nextContainer = dom.element('div', [
        pair.create('hidden', collapsed)
      ])
      const nextLine = dom.element('div', [], [content, nextContainer])
      dom.append(this.ccontainer, [nextLine])
      this.ccontainer = nextContainer
      this.depth++
      // when header is clicked, collapse/uncollapse container
      dom.addEventListener(content, 'click', (_event) => {
        nextContainer.toggleAttribute('hidden')
        triangleDown.toggleAttribute('hidden')
        triangleRight.toggleAttribute('hidden')
      })
    })
  }

  /**
   * @param {Array<string|Symbol|Object|number>} args
   */
  groupCollapsed (args) {
    this.group(args, true)
  }

  groupEnd () {
    eventloop.enqueue(() => {
      if (this.depth > 0) {
        this.depth--
        // @ts-ignore
        this.ccontainer = this.ccontainer.parentElement.parentElement
      }
    })
  }

  /**
   * @param {Array<string|Symbol|Object|number>} args
   */
  print (args) {
    eventloop.enqueue(() => {
      dom.append(this.ccontainer, [
        dom.element('div', [
          pair.create(
            'style',
            `${lineStyle};padding-left:${this.depth * 10}px`
          )
        ], _computeLineSpans(args))
      ])
    })
  }

  /**
   * @param {Error} err
   */
  printError (err) {
    this.print([common.RED, common.BOLD, err.toString()])
  }

  /**
   * @param {string} url
   * @param {number} height
   */
  printImg (url, height) {
    eventloop.enqueue(() => {
      dom.append(this.ccontainer, [
        dom.element('img', [
          pair.create('src', url),
          pair.create('height', `${math.round(height * 1.5)}px`)
        ])
      ])
    })
  }

  /**
   * @param {Node} node
   */
  printDom (node) {
    eventloop.enqueue(() => {
      dom.append(this.ccontainer, [node])
    })
  }

  destroy () {
    eventloop.enqueue(() => {
      vconsoles.delete(this)
    })
  }
}
/* c8 ignore stop */

/**
 * @param {Element} dom
 */
/* c8 ignore next */
const createVConsole = (dom) => new VConsole(dom)

/**
 * @param {string} moduleName
 * @return {function(...any):void}
 */
const logging_createModuleLogger = (moduleName) => common.createModuleLogger(print, moduleName)

;// ./node_modules/lib0/iterator.js
/**
 * Utility module to create and manipulate Iterators.
 *
 * @module iterator
 */

/**
 * @template T,R
 * @param {Iterator<T>} iterator
 * @param {function(T):R} f
 * @return {IterableIterator<R>}
 */
const mapIterator = (iterator, f) => ({
  [Symbol.iterator] () {
    return this
  },
  // @ts-ignore
  next () {
    const r = iterator.next()
    return { value: r.done ? undefined : f(r.value), done: r.done }
  }
})

/**
 * @template T
 * @param {function():IteratorResult<T>} next
 * @return {IterableIterator<T>}
 */
const createIterator = next => ({
  /**
   * @return {IterableIterator<T>}
   */
  [Symbol.iterator] () {
    return this
  },
  // @ts-ignore
  next
})

/**
 * @template T
 * @param {Iterator<T>} iterator
 * @param {function(T):boolean} filter
 */
const iteratorFilter = (iterator, filter) => createIterator(() => {
  let res
  do {
    res = iterator.next()
  } while (!res.done && !filter(res.value))
  return res
})

/**
 * @template T,M
 * @param {Iterator<T>} iterator
 * @param {function(T):M} fmap
 */
const iteratorMap = (iterator, fmap) => createIterator(() => {
  const { done, value } = iterator.next()
  return { done, value: done ? undefined : fmap(value) }
})

;// ./node_modules/lib0/object.js
/**
 * Utility functions for working with EcmaScript objects.
 *
 * @module object
 */

/**
 * @return {Object<string,any>} obj
 */
const object_create = () => Object.create(null)

/**
 * Object.assign
 */
const object_assign = Object.assign

/**
 * @param {Object<string,any>} obj
 */
const keys = Object.keys

/**
 * @template V
 * @param {{[key:string]: V}} obj
 * @return {Array<V>}
 */
const values = Object.values

/**
 * @template V
 * @param {{[k:string]:V}} obj
 * @param {function(V,string):any} f
 */
const object_forEach = (obj, f) => {
  for (const key in obj) {
    f(obj[key], key)
  }
}

/**
 * @todo implement mapToArray & map
 *
 * @template R
 * @param {Object<string,any>} obj
 * @param {function(any,string):R} f
 * @return {Array<R>}
 */
const object_map = (obj, f) => {
  const results = []
  for (const key in obj) {
    results.push(f(obj[key], key))
  }
  return results
}

/**
 * @deprecated use object.size instead
 * @param {Object<string,any>} obj
 * @return {number}
 */
const object_length = obj => keys(obj).length

/**
 * @param {Object<string,any>} obj
 * @return {number}
 */
const size = obj => keys(obj).length

/**
 * @template {{ [key:string|number|symbol]: any }} T
 * @param {T} obj
 * @param {(v:T[keyof T],k:keyof T)=>boolean} f
 * @return {boolean}
 */
const object_some = (obj, f) => {
  for (const key in obj) {
    if (f(obj[key], key)) {
      return true
    }
  }
  return false
}

/**
 * @param {Object|null|undefined} obj
 */
const isEmpty = obj => {
  // eslint-disable-next-line no-unreachable-loop
  for (const _k in obj) {
    return false
  }
  return true
}

/**
 * @template {{ [key:string|number|symbol]: any }} T
 * @param {T} obj
 * @param {(v:T[keyof T],k:keyof T)=>boolean} f
 * @return {boolean}
 */
const object_every = (obj, f) => {
  for (const key in obj) {
    if (!f(obj[key], key)) {
      return false
    }
  }
  return true
}

/**
 * Calls `Object.prototype.hasOwnProperty`.
 *
 * @param {any} obj
 * @param {string|number|symbol} key
 * @return {boolean}
 */
const hasProperty = (obj, key) => Object.prototype.hasOwnProperty.call(obj, key)

/**
 * @param {Object<string,any>} a
 * @param {Object<string,any>} b
 * @return {boolean}
 */
const object_equalFlat = (a, b) => a === b || (size(a) === size(b) && object_every(a, (val, key) => (val !== undefined || hasProperty(b, key)) && b[key] === val))

/**
 * Make an object immutable. This hurts performance and is usually not needed if you perform good
 * coding practices.
 */
const freeze = Object.freeze

/**
 * Make an object and all its children immutable.
 * This *really* hurts performance and is usually not needed if you perform good coding practices.
 *
 * @template {any} T
 * @param {T} o
 * @return {Readonly<T>}
 */
const deepFreeze = (o) => {
  for (const key in o) {
    const c = o[key]
    if (typeof c === 'object' || typeof c === 'function') {
      deepFreeze(o[key])
    }
  }
  return freeze(o)
}

/**
 * Get object property. Create T if property is undefined and set T on object.
 *
 * @function
 * @template {object} KV
 * @template {keyof KV} [K=keyof KV]
 * @param {KV} o
 * @param {K} key
 * @param {() => KV[K]} createT
 * @return {KV[K]}
 */
const object_setIfUndefined = (o, key, createT) => hasProperty(o, key) ? o[key] : (o[key] = createT())

;// ./node_modules/yjs/dist/yjs.mjs





















/**
 * This is an abstract interface that all Connectors should implement to keep them interchangeable.
 *
 * @note This interface is experimental and it is not advised to actually inherit this class.
 *       It just serves as typing information.
 *
 * @extends {ObservableV2<any>}
 */
class AbstractConnector extends ObservableV2 {
  /**
   * @param {Doc} ydoc
   * @param {any} awareness
   */
  constructor (ydoc, awareness) {
    super();
    this.doc = ydoc;
    this.awareness = awareness;
  }
}

class DeleteItem {
  /**
   * @param {number} clock
   * @param {number} len
   */
  constructor (clock, len) {
    /**
     * @type {number}
     */
    this.clock = clock;
    /**
     * @type {number}
     */
    this.len = len;
  }
}

/**
 * We no longer maintain a DeleteStore. DeleteSet is a temporary object that is created when needed.
 * - When created in a transaction, it must only be accessed after sorting, and merging
 *   - This DeleteSet is send to other clients
 * - We do not create a DeleteSet when we send a sync message. The DeleteSet message is created directly from StructStore
 * - We read a DeleteSet as part of a sync/update message. In this case the DeleteSet is already sorted and merged.
 */
class DeleteSet {
  constructor () {
    /**
     * @type {Map<number,Array<DeleteItem>>}
     */
    this.clients = new Map();
  }
}

/**
 * Iterate over all structs that the DeleteSet gc's.
 *
 * @param {Transaction} transaction
 * @param {DeleteSet} ds
 * @param {function(GC|Item):void} f
 *
 * @function
 */
const iterateDeletedStructs = (transaction, ds, f) =>
  ds.clients.forEach((deletes, clientid) => {
    const structs = /** @type {Array<GC|Item>} */ (transaction.doc.store.clients.get(clientid));
    if (structs != null) {
      const lastStruct = structs[structs.length - 1];
      const clockState = lastStruct.id.clock + lastStruct.length;
      for (let i = 0, del = deletes[i]; i < deletes.length && del.clock < clockState; del = deletes[++i]) {
        iterateStructs(transaction, structs, del.clock, del.len, f);
      }
    }
  });

/**
 * @param {Array<DeleteItem>} dis
 * @param {number} clock
 * @return {number|null}
 *
 * @private
 * @function
 */
const findIndexDS = (dis, clock) => {
  let left = 0;
  let right = dis.length - 1;
  while (left <= right) {
    const midindex = floor((left + right) / 2);
    const mid = dis[midindex];
    const midclock = mid.clock;
    if (midclock <= clock) {
      if (clock < midclock + mid.len) {
        return midindex
      }
      left = midindex + 1;
    } else {
      right = midindex - 1;
    }
  }
  return null
};

/**
 * @param {DeleteSet} ds
 * @param {ID} id
 * @return {boolean}
 *
 * @private
 * @function
 */
const isDeleted = (ds, id) => {
  const dis = ds.clients.get(id.client);
  return dis !== undefined && findIndexDS(dis, id.clock) !== null
};

/**
 * @param {DeleteSet} ds
 *
 * @private
 * @function
 */
const sortAndMergeDeleteSet = ds => {
  ds.clients.forEach(dels => {
    dels.sort((a, b) => a.clock - b.clock);
    // merge items without filtering or splicing the array
    // i is the current pointer
    // j refers to the current insert position for the pointed item
    // try to merge dels[i] into dels[j-1] or set dels[j]=dels[i]
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

/**
 * @param {Array<DeleteSet>} dss
 * @return {DeleteSet} A fresh DeleteSet
 */
const mergeDeleteSets = dss => {
  const merged = new DeleteSet();
  for (let dssI = 0; dssI < dss.length; dssI++) {
    dss[dssI].clients.forEach((delsLeft, client) => {
      if (!merged.clients.has(client)) {
        // Write all missing keys from current ds and all following.
        // If merged already contains `client` current ds has already been added.
        /**
         * @type {Array<DeleteItem>}
         */
        const dels = delsLeft.slice();
        for (let i = dssI + 1; i < dss.length; i++) {
          appendTo(dels, dss[i].clients.get(client) || []);
        }
        merged.clients.set(client, dels);
      }
    });
  }
  sortAndMergeDeleteSet(merged);
  return merged
};

/**
 * @param {DeleteSet} ds
 * @param {number} client
 * @param {number} clock
 * @param {number} length
 *
 * @private
 * @function
 */
const addToDeleteSet = (ds, client, clock, length) => {
  setIfUndefined(ds.clients, client, () => /** @type {Array<DeleteItem>} */ ([])).push(new DeleteItem(clock, length));
};

const createDeleteSet = () => new DeleteSet();

/**
 * @param {StructStore} ss
 * @return {DeleteSet} Merged and sorted DeleteSet
 *
 * @private
 * @function
 */
const createDeleteSetFromStructStore = ss => {
  const ds = createDeleteSet();
  ss.clients.forEach((structs, client) => {
    /**
     * @type {Array<DeleteItem>}
     */
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
  return ds
};

/**
 * @param {DSEncoderV1 | DSEncoderV2} encoder
 * @param {DeleteSet} ds
 *
 * @private
 * @function
 */
const writeDeleteSet = (encoder, ds) => {
  writeVarUint(encoder.restEncoder, ds.clients.size);

  // Ensure that the delete set is written in a deterministic order
  array_from(ds.clients.entries())
    .sort((a, b) => b[0] - a[0])
    .forEach(([client, dsitems]) => {
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

/**
 * @param {DSDecoderV1 | DSDecoderV2} decoder
 * @return {DeleteSet}
 *
 * @private
 * @function
 */
const readDeleteSet = decoder => {
  const ds = new DeleteSet();
  const numClients = decoding.readVarUint(decoder.restDecoder);
  for (let i = 0; i < numClients; i++) {
    decoder.resetDsCurVal();
    const client = decoding.readVarUint(decoder.restDecoder);
    const numberOfDeletes = decoding.readVarUint(decoder.restDecoder);
    if (numberOfDeletes > 0) {
      const dsField = map.setIfUndefined(ds.clients, client, () => /** @type {Array<DeleteItem>} */ ([]));
      for (let i = 0; i < numberOfDeletes; i++) {
        dsField.push(new DeleteItem(decoder.readDsClock(), decoder.readDsLen()));
      }
    }
  }
  return ds
};

/**
 * @todo YDecoder also contains references to String and other Decoders. Would make sense to exchange YDecoder.toUint8Array for YDecoder.DsToUint8Array()..
 */

/**
 * @param {DSDecoderV1 | DSDecoderV2} decoder
 * @param {Transaction} transaction
 * @param {StructStore} store
 * @return {Uint8Array|null} Returns a v2 update containing all deletes that couldn't be applied yet; or null if all deletes were applied successfully.
 *
 * @private
 * @function
 */
const readAndApplyDeleteSet = (decoder, transaction, store) => {
  const unappliedDS = new DeleteSet();
  const numClients = decoding.readVarUint(decoder.restDecoder);
  for (let i = 0; i < numClients; i++) {
    decoder.resetDsCurVal();
    const client = decoding.readVarUint(decoder.restDecoder);
    const numberOfDeletes = decoding.readVarUint(decoder.restDecoder);
    const structs = store.clients.get(client) || [];
    const state = getState(store, client);
    for (let i = 0; i < numberOfDeletes; i++) {
      const clock = decoder.readDsClock();
      const clockEnd = clock + decoder.readDsLen();
      if (clock < state) {
        if (state < clockEnd) {
          addToDeleteSet(unappliedDS, client, state, clockEnd - state);
        }
        let index = findIndexSS(structs, clock);
        /**
         * We can ignore the case of GC and Delete structs, because we are going to skip them
         * @type {Item}
         */
        // @ts-ignore
        let struct = structs[index];
        // split the first item if necessary
        if (!struct.deleted && struct.id.clock < clock) {
          structs.splice(index + 1, 0, splitItem(transaction, struct, clock - struct.id.clock));
          index++; // increase we now want to use the next struct
        }
        while (index < structs.length) {
          // @ts-ignore
          struct = structs[index++];
          if (struct.id.clock < clockEnd) {
            if (!struct.deleted) {
              if (clockEnd < struct.id.clock + struct.length) {
                structs.splice(index, 0, splitItem(transaction, struct, clockEnd - struct.id.clock));
              }
              struct.delete(transaction);
            }
          } else {
            break
          }
        }
      } else {
        addToDeleteSet(unappliedDS, client, clock, clockEnd - clock);
      }
    }
  }
  if (unappliedDS.clients.size > 0) {
    const ds = new UpdateEncoderV2();
    encoding.writeVarUint(ds.restEncoder, 0); // encode 0 structs
    writeDeleteSet(ds, unappliedDS);
    return ds.toUint8Array()
  }
  return null
};

/**
 * @param {DeleteSet} ds1
 * @param {DeleteSet} ds2
 */
const equalDeleteSets = (ds1, ds2) => {
  if (ds1.clients.size !== ds2.clients.size) return false
  for (const [client, deleteItems1] of ds1.clients.entries()) {
    const deleteItems2 = /** @type {Array<import('../internals.js').DeleteItem>} */ (ds2.clients.get(client));
    if (deleteItems2 === undefined || deleteItems1.length !== deleteItems2.length) return false
    for (let i = 0; i < deleteItems1.length; i++) {
      const di1 = deleteItems1[i];
      const di2 = deleteItems2[i];
      if (di1.clock !== di2.clock || di1.len !== di2.len) {
        return false
      }
    }
  }
  return true
};

/**
 * @module Y
 */


const generateNewClientId = uint32;

/**
 * @typedef {Object} DocOpts
 * @property {boolean} [DocOpts.gc=true] Disable garbage collection (default: gc=true)
 * @property {function(Item):boolean} [DocOpts.gcFilter] Will be called before an Item is garbage collected. Return false to keep the Item.
 * @property {string} [DocOpts.guid] Define a globally unique identifier for this document
 * @property {string | null} [DocOpts.collectionid] Associate this document with a collection. This only plays a role if your provider has a concept of collection.
 * @property {any} [DocOpts.meta] Any kind of meta information you want to associate with this document. If this is a subdocument, remote peers will store the meta information as well.
 * @property {boolean} [DocOpts.autoLoad] If a subdocument, automatically load document. If this is a subdocument, remote peers will load the document as well automatically.
 * @property {boolean} [DocOpts.shouldLoad] Whether the document should be synced by the provider now. This is toggled to true when you call ydoc.load()
 */

/**
 * @typedef {Object} DocEvents
 * @property {function(Doc):void} DocEvents.destroy
 * @property {function(Doc):void} DocEvents.load
 * @property {function(boolean, Doc):void} DocEvents.sync
 * @property {function(Uint8Array, any, Doc, Transaction):void} DocEvents.update
 * @property {function(Uint8Array, any, Doc, Transaction):void} DocEvents.updateV2
 * @property {function(Doc):void} DocEvents.beforeAllTransactions
 * @property {function(Transaction, Doc):void} DocEvents.beforeTransaction
 * @property {function(Transaction, Doc):void} DocEvents.beforeObserverCalls
 * @property {function(Transaction, Doc):void} DocEvents.afterTransaction
 * @property {function(Transaction, Doc):void} DocEvents.afterTransactionCleanup
 * @property {function(Doc, Array<Transaction>):void} DocEvents.afterAllTransactions
 * @property {function({ loaded: Set<Doc>, added: Set<Doc>, removed: Set<Doc> }, Doc, Transaction):void} DocEvents.subdocs
 */

/**
 * A Yjs instance handles the state of shared data.
 * @extends ObservableV2<DocEvents>
 */
class Doc extends ObservableV2 {
  /**
   * @param {DocOpts} opts configuration
   */
  constructor ({ guid = uuidv4(), collectionid = null, gc = true, gcFilter = () => true, meta = null, autoLoad = false, shouldLoad = true } = {}) {
    super();
    this.gc = gc;
    this.gcFilter = gcFilter;
    this.clientID = generateNewClientId();
    this.guid = guid;
    this.collectionid = collectionid;
    /**
     * @type {Map<string, AbstractType<YEvent<any>>>}
     */
    this.share = new Map();
    this.store = new StructStore();
    /**
     * @type {Transaction | null}
     */
    this._transaction = null;
    /**
     * @type {Array<Transaction>}
     */
    this._transactionCleanups = [];
    /**
     * @type {Set<Doc>}
     */
    this.subdocs = new Set();
    /**
     * If this document is a subdocument - a document integrated into another document - then _item is defined.
     * @type {Item?}
     */
    this._item = null;
    this.shouldLoad = shouldLoad;
    this.autoLoad = autoLoad;
    this.meta = meta;
    /**
     * This is set to true when the persistence provider loaded the document from the database or when the `sync` event fires.
     * Note that not all providers implement this feature. Provider authors are encouraged to fire the `load` event when the doc content is loaded from the database.
     *
     * @type {boolean}
     */
    this.isLoaded = false;
    /**
     * This is set to true when the connection provider has successfully synced with a backend.
     * Note that when using peer-to-peer providers this event may not provide very useful.
     * Also note that not all providers implement this feature. Provider authors are encouraged to fire
     * the `sync` event when the doc has been synced (with `true` as a parameter) or if connection is
     * lost (with false as a parameter).
     */
    this.isSynced = false;
    this.isDestroyed = false;
    /**
     * Promise that resolves once the document has been loaded from a persistence provider.
     */
    this.whenLoaded = promise_create(resolve => {
      this.on('load', () => {
        this.isLoaded = true;
        resolve(this);
      });
    });
    const provideSyncedPromise = () => promise_create(resolve => {
      /**
       * @param {boolean} isSynced
       */
      const eventHandler = (isSynced) => {
        if (isSynced === undefined || isSynced === true) {
          this.off('sync', eventHandler);
          resolve();
        }
      };
      this.on('sync', eventHandler);
    });
    this.on('sync', isSynced => {
      if (isSynced === false && this.isSynced) {
        this.whenSynced = provideSyncedPromise();
      }
      this.isSynced = isSynced === undefined || isSynced === true;
      if (this.isSynced && !this.isLoaded) {
        this.emit('load', [this]);
      }
    });
    /**
     * Promise that resolves once the document has been synced with a backend.
     * This promise is recreated when the connection is lost.
     * Note the documentation about the `isSynced` property.
     */
    this.whenSynced = provideSyncedPromise();
  }

  /**
   * Notify the parent document that you request to load data into this subdocument (if it is a subdocument).
   *
   * `load()` might be used in the future to request any provider to load the most current data.
   *
   * It is safe to call `load()` multiple times.
   */
  load () {
    const item = this._item;
    if (item !== null && !this.shouldLoad) {
      transact(/** @type {any} */ (item.parent).doc, transaction => {
        transaction.subdocsLoaded.add(this);
      }, null, true);
    }
    this.shouldLoad = true;
  }

  getSubdocs () {
    return this.subdocs
  }

  getSubdocGuids () {
    return new Set(array_from(this.subdocs).map(doc => doc.guid))
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
  transact (f, origin = null) {
    return transact(this, f, origin)
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
  get (name, TypeConstructor = /** @type {any} */ (AbstractType)) {
    const type = setIfUndefined(this.share, name, () => {
      // @ts-ignore
      const t = new TypeConstructor();
      t._integrate(this, null);
      return t
    });
    const Constr = type.constructor;
    if (TypeConstructor !== AbstractType && Constr !== TypeConstructor) {
      if (Constr === AbstractType) {
        // @ts-ignore
        const t = new TypeConstructor();
        t._map = type._map;
        type._map.forEach(/** @param {Item?} n */ n => {
          for (; n !== null; n = n.left) {
            // @ts-ignore
            n.parent = t;
          }
        });
        t._start = type._start;
        for (let n = t._start; n !== null; n = n.right) {
          n.parent = t;
        }
        t._length = type._length;
        this.share.set(name, t);
        t._integrate(this, null);
        return /** @type {InstanceType<Type>} */ (t)
      } else {
        throw new Error(`Type with the name ${name} has already been defined with a different constructor`)
      }
    }
    return /** @type {InstanceType<Type>} */ (type)
  }

  /**
   * @template T
   * @param {string} [name]
   * @return {YArray<T>}
   *
   * @public
   */
  getArray (name = '') {
    return /** @type {YArray<T>} */ (this.get(name, YArray))
  }

  /**
   * @param {string} [name]
   * @return {YText}
   *
   * @public
   */
  getText (name = '') {
    return this.get(name, YText)
  }

  /**
   * @template T
   * @param {string} [name]
   * @return {YMap<T>}
   *
   * @public
   */
  getMap (name = '') {
    return /** @type {YMap<T>} */ (this.get(name, YMap))
  }

  /**
   * @param {string} [name]
   * @return {YXmlElement}
   *
   * @public
   */
  getXmlElement (name = '') {
    return /** @type {YXmlElement<{[key:string]:string}>} */ (this.get(name, YXmlElement))
  }

  /**
   * @param {string} [name]
   * @return {YXmlFragment}
   *
   * @public
   */
  getXmlFragment (name = '') {
    return this.get(name, YXmlFragment)
  }

  /**
   * Converts the entire document into a js object, recursively traversing each yjs type
   * Doesn't log types that have not been defined (using ydoc.getType(..)).
   *
   * @deprecated Do not use this method and rather call toJSON directly on the shared types.
   *
   * @return {Object<string, any>}
   */
  toJSON () {
    /**
     * @type {Object<string, any>}
     */
    const doc = {};

    this.share.forEach((value, key) => {
      doc[key] = value.toJSON();
    });

    return doc
  }

  /**
   * Emit `destroy` event and unregister all event handlers.
   */
  destroy () {
    this.isDestroyed = true;
    array_from(this.subdocs).forEach(subdoc => subdoc.destroy());
    const item = this._item;
    if (item !== null) {
      this._item = null;
      const content = /** @type {ContentDoc} */ (item.content);
      content.doc = new Doc({ guid: this.guid, ...content.opts, shouldLoad: false });
      content.doc._item = item;
      transact(/** @type {any} */ (item).parent.doc, transaction => {
        const doc = content.doc;
        if (!item.deleted) {
          transaction.subdocsAdded.add(doc);
        }
        transaction.subdocsRemoved.add(this);
      }, null, true);
    }
    // @ts-ignore
    this.emit('destroyed', [true]); // DEPRECATED!
    this.emit('destroy', [this]);
    super.destroy();
  }
}

class DSDecoderV1 {
  /**
   * @param {decoding.Decoder} decoder
   */
  constructor (decoder) {
    this.restDecoder = decoder;
  }

  resetDsCurVal () {
    // nop
  }

  /**
   * @return {number}
   */
  readDsClock () {
    return decoding.readVarUint(this.restDecoder)
  }

  /**
   * @return {number}
   */
  readDsLen () {
    return decoding.readVarUint(this.restDecoder)
  }
}

class UpdateDecoderV1 extends (/* unused pure expression or super */ null && (DSDecoderV1)) {
  /**
   * @return {ID}
   */
  readLeftID () {
    return createID(decoding.readVarUint(this.restDecoder), decoding.readVarUint(this.restDecoder))
  }

  /**
   * @return {ID}
   */
  readRightID () {
    return createID(decoding.readVarUint(this.restDecoder), decoding.readVarUint(this.restDecoder))
  }

  /**
   * Read the next client id.
   * Use this in favor of readID whenever possible to reduce the number of objects created.
   */
  readClient () {
    return decoding.readVarUint(this.restDecoder)
  }

  /**
   * @return {number} info An unsigned 8-bit integer
   */
  readInfo () {
    return decoding.readUint8(this.restDecoder)
  }

  /**
   * @return {string}
   */
  readString () {
    return decoding.readVarString(this.restDecoder)
  }

  /**
   * @return {boolean} isKey
   */
  readParentInfo () {
    return decoding.readVarUint(this.restDecoder) === 1
  }

  /**
   * @return {number} info An unsigned 8-bit integer
   */
  readTypeRef () {
    return decoding.readVarUint(this.restDecoder)
  }

  /**
   * Write len of a struct - well suited for Opt RLE encoder.
   *
   * @return {number} len
   */
  readLen () {
    return decoding.readVarUint(this.restDecoder)
  }

  /**
   * @return {any}
   */
  readAny () {
    return decoding.readAny(this.restDecoder)
  }

  /**
   * @return {Uint8Array}
   */
  readBuf () {
    return buffer.copyUint8Array(decoding.readVarUint8Array(this.restDecoder))
  }

  /**
   * Legacy implementation uses JSON parse. We use any-decoding in v2.
   *
   * @return {any}
   */
  readJSON () {
    return JSON.parse(decoding.readVarString(this.restDecoder))
  }

  /**
   * @return {string}
   */
  readKey () {
    return decoding.readVarString(this.restDecoder)
  }
}

class DSDecoderV2 {
  /**
   * @param {decoding.Decoder} decoder
   */
  constructor (decoder) {
    /**
     * @private
     */
    this.dsCurrVal = 0;
    this.restDecoder = decoder;
  }

  resetDsCurVal () {
    this.dsCurrVal = 0;
  }

  /**
   * @return {number}
   */
  readDsClock () {
    this.dsCurrVal += readVarUint(this.restDecoder);
    return this.dsCurrVal
  }

  /**
   * @return {number}
   */
  readDsLen () {
    const diff = readVarUint(this.restDecoder) + 1;
    this.dsCurrVal += diff;
    return diff
  }
}

class UpdateDecoderV2 extends DSDecoderV2 {
  /**
   * @param {decoding.Decoder} decoder
   */
  constructor (decoder) {
    super(decoder);
    /**
     * List of cached keys. If the keys[id] does not exist, we read a new key
     * from stringEncoder and push it to keys.
     *
     * @type {Array<string>}
     */
    this.keys = [];
    readVarUint(decoder); // read feature flag - currently unused
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
  readLeftID () {
    return new ID(this.clientDecoder.read(), this.leftClockDecoder.read())
  }

  /**
   * @return {ID}
   */
  readRightID () {
    return new ID(this.clientDecoder.read(), this.rightClockDecoder.read())
  }

  /**
   * Read the next client id.
   * Use this in favor of readID whenever possible to reduce the number of objects created.
   */
  readClient () {
    return this.clientDecoder.read()
  }

  /**
   * @return {number} info An unsigned 8-bit integer
   */
  readInfo () {
    return /** @type {number} */ (this.infoDecoder.read())
  }

  /**
   * @return {string}
   */
  readString () {
    return this.stringDecoder.read()
  }

  /**
   * @return {boolean}
   */
  readParentInfo () {
    return this.parentInfoDecoder.read() === 1
  }

  /**
   * @return {number} An unsigned 8-bit integer
   */
  readTypeRef () {
    return this.typeRefDecoder.read()
  }

  /**
   * Write len of a struct - well suited for Opt RLE encoder.
   *
   * @return {number}
   */
  readLen () {
    return this.lenDecoder.read()
  }

  /**
   * @return {any}
   */
  readAny () {
    return readAny(this.restDecoder)
  }

  /**
   * @return {Uint8Array}
   */
  readBuf () {
    return readVarUint8Array(this.restDecoder)
  }

  /**
   * This is mainly here for legacy purposes.
   *
   * Initial we incoded objects using JSON. Now we use the much faster lib0/any-encoder. This method mainly exists for legacy purposes for the v1 encoder.
   *
   * @return {any}
   */
  readJSON () {
    return readAny(this.restDecoder)
  }

  /**
   * @return {string}
   */
  readKey () {
    const keyClock = this.keyClockDecoder.read();
    if (keyClock < this.keys.length) {
      return this.keys[keyClock]
    } else {
      const key = this.stringDecoder.read();
      this.keys.push(key);
      return key
    }
  }
}

class DSEncoderV1 {
  constructor () {
    this.restEncoder = createEncoder();
  }

  toUint8Array () {
    return toUint8Array(this.restEncoder)
  }

  resetDsCurVal () {
    // nop
  }

  /**
   * @param {number} clock
   */
  writeDsClock (clock) {
    writeVarUint(this.restEncoder, clock);
  }

  /**
   * @param {number} len
   */
  writeDsLen (len) {
    writeVarUint(this.restEncoder, len);
  }
}

class UpdateEncoderV1 extends DSEncoderV1 {
  /**
   * @param {ID} id
   */
  writeLeftID (id) {
    writeVarUint(this.restEncoder, id.client);
    writeVarUint(this.restEncoder, id.clock);
  }

  /**
   * @param {ID} id
   */
  writeRightID (id) {
    writeVarUint(this.restEncoder, id.client);
    writeVarUint(this.restEncoder, id.clock);
  }

  /**
   * Use writeClient and writeClock instead of writeID if possible.
   * @param {number} client
   */
  writeClient (client) {
    writeVarUint(this.restEncoder, client);
  }

  /**
   * @param {number} info An unsigned 8-bit integer
   */
  writeInfo (info) {
    writeUint8(this.restEncoder, info);
  }

  /**
   * @param {string} s
   */
  writeString (s) {
    writeVarString(this.restEncoder, s);
  }

  /**
   * @param {boolean} isYKey
   */
  writeParentInfo (isYKey) {
    writeVarUint(this.restEncoder, isYKey ? 1 : 0);
  }

  /**
   * @param {number} info An unsigned 8-bit integer
   */
  writeTypeRef (info) {
    writeVarUint(this.restEncoder, info);
  }

  /**
   * Write len of a struct - well suited for Opt RLE encoder.
   *
   * @param {number} len
   */
  writeLen (len) {
    writeVarUint(this.restEncoder, len);
  }

  /**
   * @param {any} any
   */
  writeAny (any) {
    writeAny(this.restEncoder, any);
  }

  /**
   * @param {Uint8Array} buf
   */
  writeBuf (buf) {
    writeVarUint8Array(this.restEncoder, buf);
  }

  /**
   * @param {any} embed
   */
  writeJSON (embed) {
    writeVarString(this.restEncoder, JSON.stringify(embed));
  }

  /**
   * @param {string} key
   */
  writeKey (key) {
    writeVarString(this.restEncoder, key);
  }
}

class DSEncoderV2 {
  constructor () {
    this.restEncoder = createEncoder(); // encodes all the rest / non-optimized
    this.dsCurrVal = 0;
  }

  toUint8Array () {
    return toUint8Array(this.restEncoder)
  }

  resetDsCurVal () {
    this.dsCurrVal = 0;
  }

  /**
   * @param {number} clock
   */
  writeDsClock (clock) {
    const diff = clock - this.dsCurrVal;
    this.dsCurrVal = clock;
    writeVarUint(this.restEncoder, diff);
  }

  /**
   * @param {number} len
   */
  writeDsLen (len) {
    if (len === 0) {
      unexpectedCase();
    }
    writeVarUint(this.restEncoder, len - 1);
    this.dsCurrVal += len;
  }
}

class UpdateEncoderV2 extends DSEncoderV2 {
  constructor () {
    super();
    /**
     * @type {Map<string,number>}
     */
    this.keyMap = new Map();
    /**
     * Refers to the next unique key-identifier to me used.
     * See writeKey method for more information.
     *
     * @type {number}
     */
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

  toUint8Array () {
    const encoder = createEncoder();
    writeVarUint(encoder, 0); // this is a feature flag that we might use in the future
    writeVarUint8Array(encoder, this.keyClockEncoder.toUint8Array());
    writeVarUint8Array(encoder, this.clientEncoder.toUint8Array());
    writeVarUint8Array(encoder, this.leftClockEncoder.toUint8Array());
    writeVarUint8Array(encoder, this.rightClockEncoder.toUint8Array());
    writeVarUint8Array(encoder, toUint8Array(this.infoEncoder));
    writeVarUint8Array(encoder, this.stringEncoder.toUint8Array());
    writeVarUint8Array(encoder, toUint8Array(this.parentInfoEncoder));
    writeVarUint8Array(encoder, this.typeRefEncoder.toUint8Array());
    writeVarUint8Array(encoder, this.lenEncoder.toUint8Array());
    // @note The rest encoder is appended! (note the missing var)
    writeUint8Array(encoder, toUint8Array(this.restEncoder));
    return toUint8Array(encoder)
  }

  /**
   * @param {ID} id
   */
  writeLeftID (id) {
    this.clientEncoder.write(id.client);
    this.leftClockEncoder.write(id.clock);
  }

  /**
   * @param {ID} id
   */
  writeRightID (id) {
    this.clientEncoder.write(id.client);
    this.rightClockEncoder.write(id.clock);
  }

  /**
   * @param {number} client
   */
  writeClient (client) {
    this.clientEncoder.write(client);
  }

  /**
   * @param {number} info An unsigned 8-bit integer
   */
  writeInfo (info) {
    this.infoEncoder.write(info);
  }

  /**
   * @param {string} s
   */
  writeString (s) {
    this.stringEncoder.write(s);
  }

  /**
   * @param {boolean} isYKey
   */
  writeParentInfo (isYKey) {
    this.parentInfoEncoder.write(isYKey ? 1 : 0);
  }

  /**
   * @param {number} info An unsigned 8-bit integer
   */
  writeTypeRef (info) {
    this.typeRefEncoder.write(info);
  }

  /**
   * Write len of a struct - well suited for Opt RLE encoder.
   *
   * @param {number} len
   */
  writeLen (len) {
    this.lenEncoder.write(len);
  }

  /**
   * @param {any} any
   */
  writeAny (any) {
    writeAny(this.restEncoder, any);
  }

  /**
   * @param {Uint8Array} buf
   */
  writeBuf (buf) {
    writeVarUint8Array(this.restEncoder, buf);
  }

  /**
   * This is mainly here for legacy purposes.
   *
   * Initial we incoded objects using JSON. Now we use the much faster lib0/any-encoder. This method mainly exists for legacy purposes for the v1 encoder.
   *
   * @param {any} embed
   */
  writeJSON (embed) {
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
  writeKey (key) {
    const clock = this.keyMap.get(key);
    if (clock === undefined) {
      /**
       * @todo uncomment to introduce this feature finally
       *
       * Background. The ContentFormat object was always encoded using writeKey, but the decoder used to use readString.
       * Furthermore, I forgot to set the keyclock. So everything was working fine.
       *
       * However, this feature here is basically useless as it is not being used (it actually only consumes extra memory).
       *
       * I don't know yet how to reintroduce this feature..
       *
       * Older clients won't be able to read updates when we reintroduce this feature. So this should probably be done using a flag.
       *
       */
      // this.keyMap.set(key, this.keyClock)
      this.keyClockEncoder.write(this.keyClock++);
      this.stringEncoder.write(key);
    } else {
      this.keyClockEncoder.write(clock);
    }
  }
}

/**
 * @module encoding
 */
/*
 * We use the first five bits in the info flag for determining the type of the struct.
 *
 * 0: GC
 * 1: Item with Deleted content
 * 2: Item with JSON content
 * 3: Item with Binary content
 * 4: Item with String content
 * 5: Item with Embed content (for richtext content)
 * 6: Item with Format content (a formatting marker for richtext content)
 * 7: Item with Type
 */


/**
 * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
 * @param {Array<GC|Item>} structs All structs by `client`
 * @param {number} client
 * @param {number} clock write structs starting with `ID(client,clock)`
 *
 * @function
 */
const writeStructs = (encoder, structs, client, clock) => {
  // write first id
  clock = max(clock, structs[0].id.clock); // make sure the first id exists
  const startNewStructs = findIndexSS(structs, clock);
  // write # encoded structs
  writeVarUint(encoder.restEncoder, structs.length - startNewStructs);
  encoder.writeClient(client);
  writeVarUint(encoder.restEncoder, clock);
  const firstStruct = structs[startNewStructs];
  // write first struct with an offset
  firstStruct.write(encoder, clock - firstStruct.id.clock);
  for (let i = startNewStructs + 1; i < structs.length; i++) {
    structs[i].write(encoder, 0);
  }
};

/**
 * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
 * @param {StructStore} store
 * @param {Map<number,number>} _sm
 *
 * @private
 * @function
 */
const writeClientsStructs = (encoder, store, _sm) => {
  // we filter all valid _sm entries into sm
  const sm = new Map();
  _sm.forEach((clock, client) => {
    // only write if new structs are available
    if (getState(store, client) > clock) {
      sm.set(client, clock);
    }
  });
  getStateVector(store).forEach((_clock, client) => {
    if (!_sm.has(client)) {
      sm.set(client, 0);
    }
  });
  // write # states that were updated
  writeVarUint(encoder.restEncoder, sm.size);
  // Write items with higher client ids first
  // This heavily improves the conflict algorithm.
  array_from(sm.entries()).sort((a, b) => b[0] - a[0]).forEach(([client, clock]) => {
    writeStructs(encoder, /** @type {Array<GC|Item>} */ (store.clients.get(client)), client, clock);
  });
};

/**
 * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder The decoder object to read data from.
 * @param {Doc} doc
 * @return {Map<number, { i: number, refs: Array<Item | GC> }>}
 *
 * @private
 * @function
 */
const readClientsStructRefs = (decoder, doc) => {
  /**
   * @type {Map<number, { i: number, refs: Array<Item | GC> }>}
   */
  const clientRefs = map.create();
  const numOfStateUpdates = decoding.readVarUint(decoder.restDecoder);
  for (let i = 0; i < numOfStateUpdates; i++) {
    const numberOfStructs = decoding.readVarUint(decoder.restDecoder);
    /**
     * @type {Array<GC|Item>}
     */
    const refs = new Array(numberOfStructs);
    const client = decoder.readClient();
    let clock = decoding.readVarUint(decoder.restDecoder);
    // const start = performance.now()
    clientRefs.set(client, { i: 0, refs });
    for (let i = 0; i < numberOfStructs; i++) {
      const info = decoder.readInfo();
      switch (binary.BITS5 & info) {
        case 0: { // GC
          const len = decoder.readLen();
          refs[i] = new GC(createID(client, clock), len);
          clock += len;
          break
        }
        case 10: { // Skip Struct (nothing to apply)
          // @todo we could reduce the amount of checks by adding Skip struct to clientRefs so we know that something is missing.
          const len = decoding.readVarUint(decoder.restDecoder);
          refs[i] = new Skip(createID(client, clock), len);
          clock += len;
          break
        }
        default: { // Item with content
          /**
           * The optimized implementation doesn't use any variables because inlining variables is faster.
           * Below a non-optimized version is shown that implements the basic algorithm with
           * a few comments
           */
          const cantCopyParentInfo = (info & (binary.BIT7 | binary.BIT8)) === 0;
          // If parent = null and neither left nor right are defined, then we know that `parent` is child of `y`
          // and we read the next string as parentYKey.
          // It indicates how we store/retrieve parent from `y.share`
          // @type {string|null}
          const struct = new Item(
            createID(client, clock),
            null, // left
            (info & binary.BIT8) === binary.BIT8 ? decoder.readLeftID() : null, // origin
            null, // right
            (info & binary.BIT7) === binary.BIT7 ? decoder.readRightID() : null, // right origin
            cantCopyParentInfo ? (decoder.readParentInfo() ? doc.get(decoder.readString()) : decoder.readLeftID()) : null, // parent
            cantCopyParentInfo && (info & binary.BIT6) === binary.BIT6 ? decoder.readString() : null, // parentSub
            readItemContent(decoder, info) // item content
          );
          /* A non-optimized implementation of the above algorithm:

          // The item that was originally to the left of this item.
          const origin = (info & binary.BIT8) === binary.BIT8 ? decoder.readLeftID() : null
          // The item that was originally to the right of this item.
          const rightOrigin = (info & binary.BIT7) === binary.BIT7 ? decoder.readRightID() : null
          const cantCopyParentInfo = (info & (binary.BIT7 | binary.BIT8)) === 0
          const hasParentYKey = cantCopyParentInfo ? decoder.readParentInfo() : false
          // If parent = null and neither left nor right are defined, then we know that `parent` is child of `y`
          // and we read the next string as parentYKey.
          // It indicates how we store/retrieve parent from `y.share`
          // @type {string|null}
          const parentYKey = cantCopyParentInfo && hasParentYKey ? decoder.readString() : null

          const struct = new Item(
            createID(client, clock),
            null, // left
            origin, // origin
            null, // right
            rightOrigin, // right origin
            cantCopyParentInfo && !hasParentYKey ? decoder.readLeftID() : (parentYKey !== null ? doc.get(parentYKey) : null), // parent
            cantCopyParentInfo && (info & binary.BIT6) === binary.BIT6 ? decoder.readString() : null, // parentSub
            readItemContent(decoder, info) // item content
          )
          */
          refs[i] = struct;
          clock += struct.length;
        }
      }
    }
    // console.log('time to read: ', performance.now() - start) // @todo remove
  }
  return clientRefs
};

/**
 * Resume computing structs generated by struct readers.
 *
 * While there is something to do, we integrate structs in this order
 * 1. top element on stack, if stack is not empty
 * 2. next element from current struct reader (if empty, use next struct reader)
 *
 * If struct causally depends on another struct (ref.missing), we put next reader of
 * `ref.id.client` on top of stack.
 *
 * At some point we find a struct that has no causal dependencies,
 * then we start emptying the stack.
 *
 * It is not possible to have circles: i.e. struct1 (from client1) depends on struct2 (from client2)
 * depends on struct3 (from client1). Therefore the max stack size is equal to `structReaders.length`.
 *
 * This method is implemented in a way so that we can resume computation if this update
 * causally depends on another update.
 *
 * @param {Transaction} transaction
 * @param {StructStore} store
 * @param {Map<number, { i: number, refs: (GC | Item)[] }>} clientsStructRefs
 * @return { null | { update: Uint8Array, missing: Map<number,number> } }
 *
 * @private
 * @function
 */
const integrateStructs = (transaction, store, clientsStructRefs) => {
  /**
   * @type {Array<Item | GC>}
   */
  const stack = [];
  // sort them so that we take the higher id first, in case of conflicts the lower id will probably not conflict with the id from the higher user.
  let clientsStructRefsIds = array.from(clientsStructRefs.keys()).sort((a, b) => a - b);
  if (clientsStructRefsIds.length === 0) {
    return null
  }
  const getNextStructTarget = () => {
    if (clientsStructRefsIds.length === 0) {
      return null
    }
    let nextStructsTarget = /** @type {{i:number,refs:Array<GC|Item>}} */ (clientsStructRefs.get(clientsStructRefsIds[clientsStructRefsIds.length - 1]));
    while (nextStructsTarget.refs.length === nextStructsTarget.i) {
      clientsStructRefsIds.pop();
      if (clientsStructRefsIds.length > 0) {
        nextStructsTarget = /** @type {{i:number,refs:Array<GC|Item>}} */ (clientsStructRefs.get(clientsStructRefsIds[clientsStructRefsIds.length - 1]));
      } else {
        return null
      }
    }
    return nextStructsTarget
  };
  let curStructsTarget = getNextStructTarget();
  if (curStructsTarget === null) {
    return null
  }

  /**
   * @type {StructStore}
   */
  const restStructs = new StructStore();
  const missingSV = new Map();
  /**
   * @param {number} client
   * @param {number} clock
   */
  const updateMissingSv = (client, clock) => {
    const mclock = missingSV.get(client);
    if (mclock == null || mclock > clock) {
      missingSV.set(client, clock);
    }
  };
  /**
   * @type {GC|Item}
   */
  let stackHead = /** @type {any} */ (curStructsTarget).refs[/** @type {any} */ (curStructsTarget).i++];
  // caching the state because it is used very often
  const state = new Map();

  const addStackToRestSS = () => {
    for (const item of stack) {
      const client = item.id.client;
      const inapplicableItems = clientsStructRefs.get(client);
      if (inapplicableItems) {
        // decrement because we weren't able to apply previous operation
        inapplicableItems.i--;
        restStructs.clients.set(client, inapplicableItems.refs.slice(inapplicableItems.i));
        clientsStructRefs.delete(client);
        inapplicableItems.i = 0;
        inapplicableItems.refs = [];
      } else {
        // item was the last item on clientsStructRefs and the field was already cleared. Add item to restStructs and continue
        restStructs.clients.set(client, [item]);
      }
      // remove client from clientsStructRefsIds to prevent users from applying the same update again
      clientsStructRefsIds = clientsStructRefsIds.filter(c => c !== client);
    }
    stack.length = 0;
  };

  // iterate over all struct readers until we are done
  while (true) {
    if (stackHead.constructor !== Skip) {
      const localClock = map.setIfUndefined(state, stackHead.id.client, () => getState(store, stackHead.id.client));
      const offset = localClock - stackHead.id.clock;
      if (offset < 0) {
        // update from the same client is missing
        stack.push(stackHead);
        updateMissingSv(stackHead.id.client, stackHead.id.clock - 1);
        // hid a dead wall, add all items from stack to restSS
        addStackToRestSS();
      } else {
        const missing = stackHead.getMissing(transaction, store);
        if (missing !== null) {
          stack.push(stackHead);
          // get the struct reader that has the missing struct
          /**
           * @type {{ refs: Array<GC|Item>, i: number }}
           */
          const structRefs = clientsStructRefs.get(/** @type {number} */ (missing)) || { refs: [], i: 0 };
          if (structRefs.refs.length === structRefs.i) {
            // This update message causally depends on another update message that doesn't exist yet
            updateMissingSv(/** @type {number} */ (missing), getState(store, missing));
            addStackToRestSS();
          } else {
            stackHead = structRefs.refs[structRefs.i++];
            continue
          }
        } else if (offset === 0 || offset < stackHead.length) {
          // all fine, apply the stackhead
          stackHead.integrate(transaction, offset);
          state.set(stackHead.id.client, stackHead.id.clock + stackHead.length);
        }
      }
    }
    // iterate to next stackHead
    if (stack.length > 0) {
      stackHead = /** @type {GC|Item} */ (stack.pop());
    } else if (curStructsTarget !== null && curStructsTarget.i < curStructsTarget.refs.length) {
      stackHead = /** @type {GC|Item} */ (curStructsTarget.refs[curStructsTarget.i++]);
    } else {
      curStructsTarget = getNextStructTarget();
      if (curStructsTarget === null) {
        // we are done!
        break
      } else {
        stackHead = /** @type {GC|Item} */ (curStructsTarget.refs[curStructsTarget.i++]);
      }
    }
  }
  if (restStructs.clients.size > 0) {
    const encoder = new UpdateEncoderV2();
    writeClientsStructs(encoder, restStructs, new Map());
    // write empty deleteset
    // writeDeleteSet(encoder, new DeleteSet())
    encoding.writeVarUint(encoder.restEncoder, 0); // => no need for an extra function call, just write 0 deletes
    return { missing: missingSV, update: encoder.toUint8Array() }
  }
  return null
};

/**
 * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
 * @param {Transaction} transaction
 *
 * @private
 * @function
 */
const writeStructsFromTransaction = (encoder, transaction) => writeClientsStructs(encoder, transaction.doc.store, transaction.beforeState);

/**
 * Read and apply a document update.
 *
 * This function has the same effect as `applyUpdate` but accepts a decoder.
 *
 * @param {decoding.Decoder} decoder
 * @param {Doc} ydoc
 * @param {any} [transactionOrigin] This will be stored on `transaction.origin` and `.on('update', (update, origin))`
 * @param {UpdateDecoderV1 | UpdateDecoderV2} [structDecoder]
 *
 * @function
 */
const readUpdateV2 = (decoder, ydoc, transactionOrigin, structDecoder = new UpdateDecoderV2(decoder)) =>
  transact(ydoc, transaction => {
    // force that transaction.local is set to non-local
    transaction.local = false;
    let retry = false;
    const doc = transaction.doc;
    const store = doc.store;
    // let start = performance.now()
    const ss = readClientsStructRefs(structDecoder, doc);
    // console.log('time to read structs: ', performance.now() - start) // @todo remove
    // start = performance.now()
    // console.log('time to merge: ', performance.now() - start) // @todo remove
    // start = performance.now()
    const restStructs = integrateStructs(transaction, store, ss);
    const pending = store.pendingStructs;
    if (pending) {
      // check if we can apply something
      for (const [client, clock] of pending.missing) {
        if (clock < getState(store, client)) {
          retry = true;
          break
        }
      }
      if (restStructs) {
        // merge restStructs into store.pending
        for (const [client, clock] of restStructs.missing) {
          const mclock = pending.missing.get(client);
          if (mclock == null || mclock > clock) {
            pending.missing.set(client, clock);
          }
        }
        pending.update = mergeUpdatesV2([pending.update, restStructs.update]);
      }
    } else {
      store.pendingStructs = restStructs;
    }
    // console.log('time to integrate: ', performance.now() - start) // @todo remove
    // start = performance.now()
    const dsRest = readAndApplyDeleteSet(structDecoder, transaction, store);
    if (store.pendingDs) {
      // @todo we could make a lower-bound state-vector check as we do above
      const pendingDSUpdate = new UpdateDecoderV2(decoding.createDecoder(store.pendingDs));
      decoding.readVarUint(pendingDSUpdate.restDecoder); // read 0 structs, because we only encode deletes in pendingdsupdate
      const dsRest2 = readAndApplyDeleteSet(pendingDSUpdate, transaction, store);
      if (dsRest && dsRest2) {
        // case 1: ds1 != null && ds2 != null
        store.pendingDs = mergeUpdatesV2([dsRest, dsRest2]);
      } else {
        // case 2: ds1 != null
        // case 3: ds2 != null
        // case 4: ds1 == null && ds2 == null
        store.pendingDs = dsRest || dsRest2;
      }
    } else {
      // Either dsRest == null && pendingDs == null OR dsRest != null
      store.pendingDs = dsRest;
    }
    // console.log('time to cleanup: ', performance.now() - start) // @todo remove
    // start = performance.now()

    // console.log('time to resume delete readers: ', performance.now() - start) // @todo remove
    // start = performance.now()
    if (retry) {
      const update = /** @type {{update: Uint8Array}} */ (store.pendingStructs).update;
      store.pendingStructs = null;
      applyUpdateV2(transaction.doc, update);
    }
  }, transactionOrigin, false);

/**
 * Read and apply a document update.
 *
 * This function has the same effect as `applyUpdate` but accepts a decoder.
 *
 * @param {decoding.Decoder} decoder
 * @param {Doc} ydoc
 * @param {any} [transactionOrigin] This will be stored on `transaction.origin` and `.on('update', (update, origin))`
 *
 * @function
 */
const readUpdate = (decoder, ydoc, transactionOrigin) => readUpdateV2(decoder, ydoc, transactionOrigin, new UpdateDecoderV1(decoder));

/**
 * Apply a document update created by, for example, `y.on('update', update => ..)` or `update = encodeStateAsUpdate()`.
 *
 * This function has the same effect as `readUpdate` but accepts an Uint8Array instead of a Decoder.
 *
 * @param {Doc} ydoc
 * @param {Uint8Array} update
 * @param {any} [transactionOrigin] This will be stored on `transaction.origin` and `.on('update', (update, origin))`
 * @param {typeof UpdateDecoderV1 | typeof UpdateDecoderV2} [YDecoder]
 *
 * @function
 */
const applyUpdateV2 = (ydoc, update, transactionOrigin, YDecoder = UpdateDecoderV2) => {
  const decoder = decoding.createDecoder(update);
  readUpdateV2(decoder, ydoc, transactionOrigin, new YDecoder(decoder));
};

/**
 * Apply a document update created by, for example, `y.on('update', update => ..)` or `update = encodeStateAsUpdate()`.
 *
 * This function has the same effect as `readUpdate` but accepts an Uint8Array instead of a Decoder.
 *
 * @param {Doc} ydoc
 * @param {Uint8Array} update
 * @param {any} [transactionOrigin] This will be stored on `transaction.origin` and `.on('update', (update, origin))`
 *
 * @function
 */
const applyUpdate = (ydoc, update, transactionOrigin) => applyUpdateV2(ydoc, update, transactionOrigin, UpdateDecoderV1);

/**
 * Write all the document as a single update message. If you specify the state of the remote client (`targetStateVector`) it will
 * only write the operations that are missing.
 *
 * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
 * @param {Doc} doc
 * @param {Map<number,number>} [targetStateVector] The state of the target that receives the update. Leave empty to write all known structs
 *
 * @function
 */
const writeStateAsUpdate = (encoder, doc, targetStateVector = new Map()) => {
  writeClientsStructs(encoder, doc.store, targetStateVector);
  writeDeleteSet(encoder, createDeleteSetFromStructStore(doc.store));
};

/**
 * Write all the document as a single update message that can be applied on the remote document. If you specify the state of the remote client (`targetState`) it will
 * only write the operations that are missing.
 *
 * Use `writeStateAsUpdate` instead if you are working with lib0/encoding.js#Encoder
 *
 * @param {Doc} doc
 * @param {Uint8Array} [encodedTargetStateVector] The state of the target that receives the update. Leave empty to write all known structs
 * @param {UpdateEncoderV1 | UpdateEncoderV2} [encoder]
 * @return {Uint8Array}
 *
 * @function
 */
const encodeStateAsUpdateV2 = (doc, encodedTargetStateVector = new Uint8Array([0]), encoder = new UpdateEncoderV2()) => {
  const targetStateVector = decodeStateVector(encodedTargetStateVector);
  writeStateAsUpdate(encoder, doc, targetStateVector);
  const updates = [encoder.toUint8Array()];
  // also add the pending updates (if there are any)
  if (doc.store.pendingDs) {
    updates.push(doc.store.pendingDs);
  }
  if (doc.store.pendingStructs) {
    updates.push(diffUpdateV2(doc.store.pendingStructs.update, encodedTargetStateVector));
  }
  if (updates.length > 1) {
    if (encoder.constructor === UpdateEncoderV1) {
      return mergeUpdates(updates.map((update, i) => i === 0 ? update : convertUpdateFormatV2ToV1(update)))
    } else if (encoder.constructor === UpdateEncoderV2) {
      return mergeUpdatesV2(updates)
    }
  }
  return updates[0]
};

/**
 * Write all the document as a single update message that can be applied on the remote document. If you specify the state of the remote client (`targetState`) it will
 * only write the operations that are missing.
 *
 * Use `writeStateAsUpdate` instead if you are working with lib0/encoding.js#Encoder
 *
 * @param {Doc} doc
 * @param {Uint8Array} [encodedTargetStateVector] The state of the target that receives the update. Leave empty to write all known structs
 * @return {Uint8Array}
 *
 * @function
 */
const encodeStateAsUpdate = (doc, encodedTargetStateVector) => encodeStateAsUpdateV2(doc, encodedTargetStateVector, new UpdateEncoderV1());

/**
 * Read state vector from Decoder and return as Map
 *
 * @param {DSDecoderV1 | DSDecoderV2} decoder
 * @return {Map<number,number>} Maps `client` to the number next expected `clock` from that client.
 *
 * @function
 */
const readStateVector = decoder => {
  const ss = new Map();
  const ssLength = decoding.readVarUint(decoder.restDecoder);
  for (let i = 0; i < ssLength; i++) {
    const client = decoding.readVarUint(decoder.restDecoder);
    const clock = decoding.readVarUint(decoder.restDecoder);
    ss.set(client, clock);
  }
  return ss
};

/**
 * Read decodedState and return State as Map.
 *
 * @param {Uint8Array} decodedState
 * @return {Map<number,number>} Maps `client` to the number next expected `clock` from that client.
 *
 * @function
 */
// export const decodeStateVectorV2 = decodedState => readStateVector(new DSDecoderV2(decoding.createDecoder(decodedState)))

/**
 * Read decodedState and return State as Map.
 *
 * @param {Uint8Array} decodedState
 * @return {Map<number,number>} Maps `client` to the number next expected `clock` from that client.
 *
 * @function
 */
const decodeStateVector = decodedState => readStateVector(new DSDecoderV1(decoding.createDecoder(decodedState)));

/**
 * @param {DSEncoderV1 | DSEncoderV2} encoder
 * @param {Map<number,number>} sv
 * @function
 */
const writeStateVector = (encoder, sv) => {
  encoding.writeVarUint(encoder.restEncoder, sv.size);
  array.from(sv.entries()).sort((a, b) => b[0] - a[0]).forEach(([client, clock]) => {
    encoding.writeVarUint(encoder.restEncoder, client); // @todo use a special client decoder that is based on mapping
    encoding.writeVarUint(encoder.restEncoder, clock);
  });
  return encoder
};

/**
 * @param {DSEncoderV1 | DSEncoderV2} encoder
 * @param {Doc} doc
 *
 * @function
 */
const writeDocumentStateVector = (encoder, doc) => writeStateVector(encoder, getStateVector(doc.store));

/**
 * Encode State as Uint8Array.
 *
 * @param {Doc|Map<number,number>} doc
 * @param {DSEncoderV1 | DSEncoderV2} [encoder]
 * @return {Uint8Array}
 *
 * @function
 */
const encodeStateVectorV2 = (doc, encoder = new DSEncoderV2()) => {
  if (doc instanceof Map) {
    writeStateVector(encoder, doc);
  } else {
    writeDocumentStateVector(encoder, doc);
  }
  return encoder.toUint8Array()
};

/**
 * Encode State as Uint8Array.
 *
 * @param {Doc|Map<number,number>} doc
 * @return {Uint8Array}
 *
 * @function
 */
const encodeStateVector = doc => encodeStateVectorV2(doc, new DSEncoderV1());

/**
 * General event handler implementation.
 *
 * @template ARG0, ARG1
 *
 * @private
 */
class EventHandler {
  constructor () {
    /**
     * @type {Array<function(ARG0, ARG1):void>}
     */
    this.l = [];
  }
}

/**
 * @template ARG0,ARG1
 * @returns {EventHandler<ARG0,ARG1>}
 *
 * @private
 * @function
 */
const createEventHandler = () => new EventHandler();

/**
 * Adds an event listener that is called when
 * {@link EventHandler#callEventListeners} is called.
 *
 * @template ARG0,ARG1
 * @param {EventHandler<ARG0,ARG1>} eventHandler
 * @param {function(ARG0,ARG1):void} f The event handler.
 *
 * @private
 * @function
 */
const addEventHandlerListener = (eventHandler, f) =>
  eventHandler.l.push(f);

/**
 * Removes an event listener.
 *
 * @template ARG0,ARG1
 * @param {EventHandler<ARG0,ARG1>} eventHandler
 * @param {function(ARG0,ARG1):void} f The event handler that was added with
 *                     {@link EventHandler#addEventListener}
 *
 * @private
 * @function
 */
const removeEventHandlerListener = (eventHandler, f) => {
  const l = eventHandler.l;
  const len = l.length;
  eventHandler.l = l.filter(g => f !== g);
  if (len === eventHandler.l.length) {
    console.error('[yjs] Tried to remove event handler that doesn\'t exist.');
  }
};

/**
 * Call all event listeners that were added via
 * {@link EventHandler#addEventListener}.
 *
 * @template ARG0,ARG1
 * @param {EventHandler<ARG0,ARG1>} eventHandler
 * @param {ARG0} arg0
 * @param {ARG1} arg1
 *
 * @private
 * @function
 */
const callEventHandlerListeners = (eventHandler, arg0, arg1) =>
  callAll(eventHandler.l, [arg0, arg1]);

class ID {
  /**
   * @param {number} client client id
   * @param {number} clock unique per client id, continuous number
   */
  constructor (client, clock) {
    /**
     * Client id
     * @type {number}
     */
    this.client = client;
    /**
     * unique per client id, continuous number
     * @type {number}
     */
    this.clock = clock;
  }
}

/**
 * @param {ID | null} a
 * @param {ID | null} b
 * @return {boolean}
 *
 * @function
 */
const compareIDs = (a, b) => a === b || (a !== null && b !== null && a.client === b.client && a.clock === b.clock);

/**
 * @param {number} client
 * @param {number} clock
 *
 * @private
 * @function
 */
const createID = (client, clock) => new ID(client, clock);

/**
 * @param {encoding.Encoder} encoder
 * @param {ID} id
 *
 * @private
 * @function
 */
const writeID = (encoder, id) => {
  encoding.writeVarUint(encoder, id.client);
  encoding.writeVarUint(encoder, id.clock);
};

/**
 * Read ID.
 * * If first varUint read is 0xFFFFFF a RootID is returned.
 * * Otherwise an ID is returned
 *
 * @param {decoding.Decoder} decoder
 * @return {ID}
 *
 * @private
 * @function
 */
const readID = decoder =>
  createID(decoding.readVarUint(decoder), decoding.readVarUint(decoder));

/**
 * The top types are mapped from y.share.get(keyname) => type.
 * `type` does not store any information about the `keyname`.
 * This function finds the correct `keyname` for `type` and throws otherwise.
 *
 * @param {AbstractType<any>} type
 * @return {string}
 *
 * @private
 * @function
 */
const findRootTypeKey = type => {
  // @ts-ignore _y must be defined, otherwise unexpected case
  for (const [key, value] of type.doc.share.entries()) {
    if (value === type) {
      return key
    }
  }
  throw unexpectedCase()
};

/**
 * Check if `parent` is a parent of `child`.
 *
 * @param {AbstractType<any>} parent
 * @param {Item|null} child
 * @return {Boolean} Whether `parent` is a parent of `child`.
 *
 * @private
 * @function
 */
const yjs_isParentOf = (parent, child) => {
  while (child !== null) {
    if (child.parent === parent) {
      return true
    }
    child = /** @type {AbstractType<any>} */ (child.parent)._item;
  }
  return false
};

/**
 * Convenient helper to log type information.
 *
 * Do not use in productive systems as the output can be immense!
 *
 * @param {AbstractType<any>} type
 */
const logType = type => {
  const res = [];
  let n = type._start;
  while (n) {
    res.push(n);
    n = n.right;
  }
  console.log('Children: ', res);
  console.log('Children content: ', res.filter(m => !m.deleted).map(m => m.content));
};

class PermanentUserData {
  /**
   * @param {Doc} doc
   * @param {YMap<any>} [storeType]
   */
  constructor (doc, storeType = doc.getMap('users')) {
    /**
     * @type {Map<string,DeleteSet>}
     */
    const dss = new Map();
    this.yusers = storeType;
    this.doc = doc;
    /**
     * Maps from clientid to userDescription
     *
     * @type {Map<number,string>}
     */
    this.clients = new Map();
    this.dss = dss;
    /**
     * @param {YMap<any>} user
     * @param {string} userDescription
     */
    const initUser = (user, userDescription) => {
      /**
       * @type {YArray<Uint8Array>}
       */
      const ds = user.get('ds');
      const ids = user.get('ids');
      const addClientId = /** @param {number} clientid */ clientid => this.clients.set(clientid, userDescription);
      ds.observe(/** @param {YArrayEvent<any>} event */ event => {
        event.changes.added.forEach(item => {
          item.content.getContent().forEach(encodedDs => {
            if (encodedDs instanceof Uint8Array) {
              this.dss.set(userDescription, mergeDeleteSets([this.dss.get(userDescription) || createDeleteSet(), readDeleteSet(new DSDecoderV1(decoding.createDecoder(encodedDs)))]));
            }
          });
        });
      });
      this.dss.set(userDescription, mergeDeleteSets(ds.map(encodedDs => readDeleteSet(new DSDecoderV1(decoding.createDecoder(encodedDs))))));
      ids.observe(/** @param {YArrayEvent<any>} event */ event =>
        event.changes.added.forEach(item => item.content.getContent().forEach(addClientId))
      );
      ids.forEach(addClientId);
    };
    // observe users
    storeType.observe(event => {
      event.keysChanged.forEach(userDescription =>
        initUser(storeType.get(userDescription), userDescription)
      );
    });
    // add initial data
    storeType.forEach(initUser);
  }

  /**
   * @param {Doc} doc
   * @param {number} clientid
   * @param {string} userDescription
   * @param {Object} conf
   * @param {function(Transaction, DeleteSet):boolean} [conf.filter]
   */
  setUserMapping (doc, clientid, userDescription, { filter = () => true } = {}) {
    const users = this.yusers;
    let user = users.get(userDescription);
    if (!user) {
      user = new YMap();
      user.set('ids', new YArray());
      user.set('ds', new YArray());
      users.set(userDescription, user);
    }
    user.get('ids').push([clientid]);
    users.observe(_event => {
      setTimeout(() => {
        const userOverwrite = users.get(userDescription);
        if (userOverwrite !== user) {
          // user was overwritten, port all data over to the next user object
          // @todo Experiment with Y.Sets here
          user = userOverwrite;
          // @todo iterate over old type
          this.clients.forEach((_userDescription, clientid) => {
            if (userDescription === _userDescription) {
              user.get('ids').push([clientid]);
            }
          });
          const encoder = new DSEncoderV1();
          const ds = this.dss.get(userDescription);
          if (ds) {
            writeDeleteSet(encoder, ds);
            user.get('ds').push([encoder.toUint8Array()]);
          }
        }
      }, 0);
    });
    doc.on('afterTransaction', /** @param {Transaction} transaction */ transaction => {
      setTimeout(() => {
        const yds = user.get('ds');
        const ds = transaction.deleteSet;
        if (transaction.local && ds.clients.size > 0 && filter(transaction, ds)) {
          const encoder = new DSEncoderV1();
          writeDeleteSet(encoder, ds);
          yds.push([encoder.toUint8Array()]);
        }
      });
    });
  }

  /**
   * @param {number} clientid
   * @return {any}
   */
  getUserByClientId (clientid) {
    return this.clients.get(clientid) || null
  }

  /**
   * @param {ID} id
   * @return {string | null}
   */
  getUserByDeletedId (id) {
    for (const [userDescription, ds] of this.dss.entries()) {
      if (isDeleted(ds, id)) {
        return userDescription
      }
    }
    return null
  }
}

/**
 * A relative position is based on the Yjs model and is not affected by document changes.
 * E.g. If you place a relative position before a certain character, it will always point to this character.
 * If you place a relative position at the end of a type, it will always point to the end of the type.
 *
 * A numeric position is often unsuited for user selections, because it does not change when content is inserted
 * before or after.
 *
 * ```Insert(0, 'x')('a|bc') = 'xa|bc'``` Where | is the relative position.
 *
 * One of the properties must be defined.
 *
 * @example
 *   // Current cursor position is at position 10
 *   const relativePosition = createRelativePositionFromIndex(yText, 10)
 *   // modify yText
 *   yText.insert(0, 'abc')
 *   yText.delete(3, 10)
 *   // Compute the cursor position
 *   const absolutePosition = createAbsolutePositionFromRelativePosition(y, relativePosition)
 *   absolutePosition.type === yText // => true
 *   console.log('cursor location is ' + absolutePosition.index) // => cursor location is 3
 *
 */
class RelativePosition {
  /**
   * @param {ID|null} type
   * @param {string|null} tname
   * @param {ID|null} item
   * @param {number} assoc
   */
  constructor (type, tname, item, assoc = 0) {
    /**
     * @type {ID|null}
     */
    this.type = type;
    /**
     * @type {string|null}
     */
    this.tname = tname;
    /**
     * @type {ID | null}
     */
    this.item = item;
    /**
     * A relative position is associated to a specific character. By default
     * assoc >= 0, the relative position is associated to the character
     * after the meant position.
     * I.e. position 1 in 'ab' is associated to character 'b'.
     *
     * If assoc < 0, then the relative position is associated to the character
     * before the meant position.
     *
     * @type {number}
     */
    this.assoc = assoc;
  }
}

/**
 * @param {RelativePosition} rpos
 * @return {any}
 */
const relativePositionToJSON = rpos => {
  const json = {};
  if (rpos.type) {
    json.type = rpos.type;
  }
  if (rpos.tname) {
    json.tname = rpos.tname;
  }
  if (rpos.item) {
    json.item = rpos.item;
  }
  if (rpos.assoc != null) {
    json.assoc = rpos.assoc;
  }
  return json
};

/**
 * @param {any} json
 * @return {RelativePosition}
 *
 * @function
 */
const createRelativePositionFromJSON = json => new RelativePosition(json.type == null ? null : createID(json.type.client, json.type.clock), json.tname ?? null, json.item == null ? null : createID(json.item.client, json.item.clock), json.assoc == null ? 0 : json.assoc);

class AbsolutePosition {
  /**
   * @param {AbstractType<any>} type
   * @param {number} index
   * @param {number} [assoc]
   */
  constructor (type, index, assoc = 0) {
    /**
     * @type {AbstractType<any>}
     */
    this.type = type;
    /**
     * @type {number}
     */
    this.index = index;
    this.assoc = assoc;
  }
}

/**
 * @param {AbstractType<any>} type
 * @param {number} index
 * @param {number} [assoc]
 *
 * @function
 */
const createAbsolutePosition = (type, index, assoc = 0) => new AbsolutePosition(type, index, assoc);

/**
 * @param {AbstractType<any>} type
 * @param {ID|null} item
 * @param {number} [assoc]
 *
 * @function
 */
const createRelativePosition = (type, item, assoc) => {
  let typeid = null;
  let tname = null;
  if (type._item === null) {
    tname = findRootTypeKey(type);
  } else {
    typeid = createID(type._item.id.client, type._item.id.clock);
  }
  return new RelativePosition(typeid, tname, item, assoc)
};

/**
 * Create a relativePosition based on a absolute position.
 *
 * @param {AbstractType<any>} type The base type (e.g. YText or YArray).
 * @param {number} index The absolute position.
 * @param {number} [assoc]
 * @return {RelativePosition}
 *
 * @function
 */
const createRelativePositionFromTypeIndex = (type, index, assoc = 0) => {
  let t = type._start;
  if (assoc < 0) {
    // associated to the left character or the beginning of a type, increment index if possible.
    if (index === 0) {
      return createRelativePosition(type, null, assoc)
    }
    index--;
  }
  while (t !== null) {
    if (!t.deleted && t.countable) {
      if (t.length > index) {
        // case 1: found position somewhere in the linked list
        return createRelativePosition(type, createID(t.id.client, t.id.clock + index), assoc)
      }
      index -= t.length;
    }
    if (t.right === null && assoc < 0) {
      // left-associated position, return last available id
      return createRelativePosition(type, t.lastId, assoc)
    }
    t = t.right;
  }
  return createRelativePosition(type, null, assoc)
};

/**
 * @param {encoding.Encoder} encoder
 * @param {RelativePosition} rpos
 *
 * @function
 */
const writeRelativePosition = (encoder, rpos) => {
  const { type, tname, item, assoc } = rpos;
  if (item !== null) {
    encoding.writeVarUint(encoder, 0);
    writeID(encoder, item);
  } else if (tname !== null) {
    // case 2: found position at the end of the list and type is stored in y.share
    encoding.writeUint8(encoder, 1);
    encoding.writeVarString(encoder, tname);
  } else if (type !== null) {
    // case 3: found position at the end of the list and type is attached to an item
    encoding.writeUint8(encoder, 2);
    writeID(encoder, type);
  } else {
    throw error.unexpectedCase()
  }
  encoding.writeVarInt(encoder, assoc);
  return encoder
};

/**
 * @param {RelativePosition} rpos
 * @return {Uint8Array}
 */
const encodeRelativePosition = rpos => {
  const encoder = encoding.createEncoder();
  writeRelativePosition(encoder, rpos);
  return encoding.toUint8Array(encoder)
};

/**
 * @param {decoding.Decoder} decoder
 * @return {RelativePosition}
 *
 * @function
 */
const readRelativePosition = decoder => {
  let type = null;
  let tname = null;
  let itemID = null;
  switch (decoding.readVarUint(decoder)) {
    case 0:
      // case 1: found position somewhere in the linked list
      itemID = readID(decoder);
      break
    case 1:
      // case 2: found position at the end of the list and type is stored in y.share
      tname = decoding.readVarString(decoder);
      break
    case 2: {
      // case 3: found position at the end of the list and type is attached to an item
      type = readID(decoder);
    }
  }
  const assoc = decoding.hasContent(decoder) ? decoding.readVarInt(decoder) : 0;
  return new RelativePosition(type, tname, itemID, assoc)
};

/**
 * @param {Uint8Array} uint8Array
 * @return {RelativePosition}
 */
const decodeRelativePosition = uint8Array => readRelativePosition(decoding.createDecoder(uint8Array));

/**
 * @param {StructStore} store
 * @param {ID} id
 */
const getItemWithOffset = (store, id) => {
  const item = getItem(store, id);
  const diff = id.clock - item.id.clock;
  return {
    item, diff
  }
};

/**
 * Transform a relative position to an absolute position.
 *
 * If you want to share the relative position with other users, you should set
 * `followUndoneDeletions` to false to get consistent results across all clients.
 *
 * When calculating the absolute position, we try to follow the "undone deletions". This yields
 * better results for the user who performed undo. However, only the user who performed the undo
 * will get the better results, the other users don't know which operations recreated a deleted
 * range of content. There is more information in this ticket: https://github.com/yjs/yjs/issues/638
 *
 * @param {RelativePosition} rpos
 * @param {Doc} doc
 * @param {boolean} followUndoneDeletions - whether to follow undone deletions - see https://github.com/yjs/yjs/issues/638
 * @return {AbsolutePosition|null}
 *
 * @function
 */
const createAbsolutePositionFromRelativePosition = (rpos, doc, followUndoneDeletions = true) => {
  const store = doc.store;
  const rightID = rpos.item;
  const typeID = rpos.type;
  const tname = rpos.tname;
  const assoc = rpos.assoc;
  let type = null;
  let index = 0;
  if (rightID !== null) {
    if (getState(store, rightID.client) <= rightID.clock) {
      return null
    }
    const res = followUndoneDeletions ? followRedone(store, rightID) : getItemWithOffset(store, rightID);
    const right = res.item;
    if (!(right instanceof Item)) {
      return null
    }
    type = /** @type {AbstractType<any>} */ (right.parent);
    if (type._item === null || !type._item.deleted) {
      index = (right.deleted || !right.countable) ? 0 : (res.diff + (assoc >= 0 ? 0 : 1)); // adjust position based on left association if necessary
      let n = right.left;
      while (n !== null) {
        if (!n.deleted && n.countable) {
          index += n.length;
        }
        n = n.left;
      }
    }
  } else {
    if (tname !== null) {
      type = doc.get(tname);
    } else if (typeID !== null) {
      if (getState(store, typeID.client) <= typeID.clock) {
        // type does not exist yet
        return null
      }
      const { item } = followUndoneDeletions ? followRedone(store, typeID) : { item: getItem(store, typeID) };
      if (item instanceof Item && item.content instanceof ContentType) {
        type = item.content.type;
      } else {
        // struct is garbage collected
        return null
      }
    } else {
      throw error.unexpectedCase()
    }
    if (assoc >= 0) {
      index = type._length;
    } else {
      index = 0;
    }
  }
  return createAbsolutePosition(type, index, rpos.assoc)
};

/**
 * @param {RelativePosition|null} a
 * @param {RelativePosition|null} b
 * @return {boolean}
 *
 * @function
 */
const compareRelativePositions = (a, b) => a === b || (
  a !== null && b !== null && a.tname === b.tname && compareIDs(a.item, b.item) && compareIDs(a.type, b.type) && a.assoc === b.assoc
);

class Snapshot {
  /**
   * @param {DeleteSet} ds
   * @param {Map<number,number>} sv state map
   */
  constructor (ds, sv) {
    /**
     * @type {DeleteSet}
     */
    this.ds = ds;
    /**
     * State Map
     * @type {Map<number,number>}
     */
    this.sv = sv;
  }
}

/**
 * @param {Snapshot} snap1
 * @param {Snapshot} snap2
 * @return {boolean}
 */
const equalSnapshots = (snap1, snap2) => {
  const ds1 = snap1.ds.clients;
  const ds2 = snap2.ds.clients;
  const sv1 = snap1.sv;
  const sv2 = snap2.sv;
  if (sv1.size !== sv2.size || ds1.size !== ds2.size) {
    return false
  }
  for (const [key, value] of sv1.entries()) {
    if (sv2.get(key) !== value) {
      return false
    }
  }
  for (const [client, dsitems1] of ds1.entries()) {
    const dsitems2 = ds2.get(client) || [];
    if (dsitems1.length !== dsitems2.length) {
      return false
    }
    for (let i = 0; i < dsitems1.length; i++) {
      const dsitem1 = dsitems1[i];
      const dsitem2 = dsitems2[i];
      if (dsitem1.clock !== dsitem2.clock || dsitem1.len !== dsitem2.len) {
        return false
      }
    }
  }
  return true
};

/**
 * @param {Snapshot} snapshot
 * @param {DSEncoderV1 | DSEncoderV2} [encoder]
 * @return {Uint8Array}
 */
const encodeSnapshotV2 = (snapshot, encoder = new DSEncoderV2()) => {
  writeDeleteSet(encoder, snapshot.ds);
  writeStateVector(encoder, snapshot.sv);
  return encoder.toUint8Array()
};

/**
 * @param {Snapshot} snapshot
 * @return {Uint8Array}
 */
const encodeSnapshot = snapshot => encodeSnapshotV2(snapshot, new DSEncoderV1());

/**
 * @param {Uint8Array} buf
 * @param {DSDecoderV1 | DSDecoderV2} [decoder]
 * @return {Snapshot}
 */
const decodeSnapshotV2 = (buf, decoder = new DSDecoderV2(decoding.createDecoder(buf))) => {
  return new Snapshot(readDeleteSet(decoder), readStateVector(decoder))
};

/**
 * @param {Uint8Array} buf
 * @return {Snapshot}
 */
const decodeSnapshot = buf => decodeSnapshotV2(buf, new DSDecoderV1(decoding.createDecoder(buf)));

/**
 * @param {DeleteSet} ds
 * @param {Map<number,number>} sm
 * @return {Snapshot}
 */
const createSnapshot = (ds, sm) => new Snapshot(ds, sm);

const emptySnapshot = createSnapshot(createDeleteSet(), new Map());

/**
 * @param {Doc} doc
 * @return {Snapshot}
 */
const snapshot = doc => createSnapshot(createDeleteSetFromStructStore(doc.store), getStateVector(doc.store));

/**
 * @param {Item} item
 * @param {Snapshot|undefined} snapshot
 *
 * @protected
 * @function
 */
const isVisible = (item, snapshot) => snapshot === undefined
  ? !item.deleted
  : snapshot.sv.has(item.id.client) && (snapshot.sv.get(item.id.client) || 0) > item.id.clock && !isDeleted(snapshot.ds, item.id);

/**
 * @param {Transaction} transaction
 * @param {Snapshot} snapshot
 */
const splitSnapshotAffectedStructs = (transaction, snapshot) => {
  const meta = setIfUndefined(transaction.meta, splitSnapshotAffectedStructs, set_create);
  const store = transaction.doc.store;
  // check if we already split for this snapshot
  if (!meta.has(snapshot)) {
    snapshot.sv.forEach((clock, client) => {
      if (clock < getState(store, client)) {
        getItemCleanStart(transaction, createID(client, clock));
      }
    });
    iterateDeletedStructs(transaction, snapshot.ds, _item => {});
    meta.add(snapshot);
  }
};

/**
 * @example
 *  const ydoc = new Y.Doc({ gc: false })
 *  ydoc.getText().insert(0, 'world!')
 *  const snapshot = Y.snapshot(ydoc)
 *  ydoc.getText().insert(0, 'hello ')
 *  const restored = Y.createDocFromSnapshot(ydoc, snapshot)
 *  assert(restored.getText().toString() === 'world!')
 *
 * @param {Doc} originDoc
 * @param {Snapshot} snapshot
 * @param {Doc} [newDoc] Optionally, you may define the Yjs document that receives the data from originDoc
 * @return {Doc}
 */
const createDocFromSnapshot = (originDoc, snapshot, newDoc = new Doc()) => {
  if (originDoc.gc) {
    // we should not try to restore a GC-ed document, because some of the restored items might have their content deleted
    throw new Error('Garbage-collection must be disabled in `originDoc`!')
  }
  const { sv, ds } = snapshot;

  const encoder = new UpdateEncoderV2();
  originDoc.transact(transaction => {
    let size = 0;
    sv.forEach(clock => {
      if (clock > 0) {
        size++;
      }
    });
    encoding.writeVarUint(encoder.restEncoder, size);
    // splitting the structs before writing them to the encoder
    for (const [client, clock] of sv) {
      if (clock === 0) {
        continue
      }
      if (clock < getState(originDoc.store, client)) {
        getItemCleanStart(transaction, createID(client, clock));
      }
      const structs = originDoc.store.clients.get(client) || [];
      const lastStructIndex = findIndexSS(structs, clock - 1);
      // write # encoded structs
      encoding.writeVarUint(encoder.restEncoder, lastStructIndex + 1);
      encoder.writeClient(client);
      // first clock written is 0
      encoding.writeVarUint(encoder.restEncoder, 0);
      for (let i = 0; i <= lastStructIndex; i++) {
        structs[i].write(encoder, 0);
      }
    }
    writeDeleteSet(encoder, ds);
  });

  applyUpdateV2(newDoc, encoder.toUint8Array(), 'snapshot');
  return newDoc
};

/**
 * @param {Snapshot} snapshot
 * @param {Uint8Array} update
 * @param {typeof UpdateDecoderV2 | typeof UpdateDecoderV1} [YDecoder]
 */
const snapshotContainsUpdateV2 = (snapshot, update, YDecoder = UpdateDecoderV2) => {
  const updateDecoder = new YDecoder(decoding.createDecoder(update));
  const lazyDecoder = new LazyStructReader(updateDecoder, false);
  for (let curr = lazyDecoder.curr; curr !== null; curr = lazyDecoder.next()) {
    if ((snapshot.sv.get(curr.id.client) || 0) < curr.id.clock + curr.length) {
      return false
    }
  }
  const mergedDS = mergeDeleteSets([snapshot.ds, readDeleteSet(updateDecoder)]);
  return equalDeleteSets(snapshot.ds, mergedDS)
};

/**
 * @param {Snapshot} snapshot
 * @param {Uint8Array} update
 */
const snapshotContainsUpdate = (snapshot, update) => snapshotContainsUpdateV2(snapshot, update, UpdateDecoderV1);

class StructStore {
  constructor () {
    /**
     * @type {Map<number,Array<GC|Item>>}
     */
    this.clients = new Map();
    /**
     * @type {null | { missing: Map<number, number>, update: Uint8Array }}
     */
    this.pendingStructs = null;
    /**
     * @type {null | Uint8Array}
     */
    this.pendingDs = null;
  }
}

/**
 * Return the states as a Map<client,clock>.
 * Note that clock refers to the next expected clock id.
 *
 * @param {StructStore} store
 * @return {Map<number,number>}
 *
 * @public
 * @function
 */
const getStateVector = store => {
  const sm = new Map();
  store.clients.forEach((structs, client) => {
    const struct = structs[structs.length - 1];
    sm.set(client, struct.id.clock + struct.length);
  });
  return sm
};

/**
 * @param {StructStore} store
 * @param {number} client
 * @return {number}
 *
 * @public
 * @function
 */
const getState = (store, client) => {
  const structs = store.clients.get(client);
  if (structs === undefined) {
    return 0
  }
  const lastStruct = structs[structs.length - 1];
  return lastStruct.id.clock + lastStruct.length
};

/**
 * @param {StructStore} store
 * @param {GC|Item} struct
 *
 * @private
 * @function
 */
const addStruct = (store, struct) => {
  let structs = store.clients.get(struct.id.client);
  if (structs === undefined) {
    structs = [];
    store.clients.set(struct.id.client, structs);
  } else {
    const lastStruct = structs[structs.length - 1];
    if (lastStruct.id.clock + lastStruct.length !== struct.id.clock) {
      throw unexpectedCase()
    }
  }
  structs.push(struct);
};

/**
 * Perform a binary search on a sorted array
 * @param {Array<Item|GC>} structs
 * @param {number} clock
 * @return {number}
 *
 * @private
 * @function
 */
const findIndexSS = (structs, clock) => {
  let left = 0;
  let right = structs.length - 1;
  let mid = structs[right];
  let midclock = mid.id.clock;
  if (midclock === clock) {
    return right
  }
  // @todo does it even make sense to pivot the search?
  // If a good split misses, it might actually increase the time to find the correct item.
  // Currently, the only advantage is that search with pivoting might find the item on the first try.
  let midindex = floor((clock / (midclock + mid.length - 1)) * right); // pivoting the search
  while (left <= right) {
    mid = structs[midindex];
    midclock = mid.id.clock;
    if (midclock <= clock) {
      if (clock < midclock + mid.length) {
        return midindex
      }
      left = midindex + 1;
    } else {
      right = midindex - 1;
    }
    midindex = floor((left + right) / 2);
  }
  // Always check state before looking for a struct in StructStore
  // Therefore the case of not finding a struct is unexpected
  throw unexpectedCase()
};

/**
 * Expects that id is actually in store. This function throws or is an infinite loop otherwise.
 *
 * @param {StructStore} store
 * @param {ID} id
 * @return {GC|Item}
 *
 * @private
 * @function
 */
const find = (store, id) => {
  /**
   * @type {Array<GC|Item>}
   */
  // @ts-ignore
  const structs = store.clients.get(id.client);
  return structs[findIndexSS(structs, id.clock)]
};

/**
 * Expects that id is actually in store. This function throws or is an infinite loop otherwise.
 * @private
 * @function
 */
const getItem = /** @type {function(StructStore,ID):Item} */ (find);

/**
 * @param {Transaction} transaction
 * @param {Array<Item|GC>} structs
 * @param {number} clock
 */
const findIndexCleanStart = (transaction, structs, clock) => {
  const index = findIndexSS(structs, clock);
  const struct = structs[index];
  if (struct.id.clock < clock && struct instanceof Item) {
    structs.splice(index + 1, 0, splitItem(transaction, struct, clock - struct.id.clock));
    return index + 1
  }
  return index
};

/**
 * Expects that id is actually in store. This function throws or is an infinite loop otherwise.
 *
 * @param {Transaction} transaction
 * @param {ID} id
 * @return {Item}
 *
 * @private
 * @function
 */
const getItemCleanStart = (transaction, id) => {
  const structs = /** @type {Array<Item>} */ (transaction.doc.store.clients.get(id.client));
  return structs[findIndexCleanStart(transaction, structs, id.clock)]
};

/**
 * Expects that id is actually in store. This function throws or is an infinite loop otherwise.
 *
 * @param {Transaction} transaction
 * @param {StructStore} store
 * @param {ID} id
 * @return {Item}
 *
 * @private
 * @function
 */
const getItemCleanEnd = (transaction, store, id) => {
  /**
   * @type {Array<Item>}
   */
  // @ts-ignore
  const structs = store.clients.get(id.client);
  const index = findIndexSS(structs, id.clock);
  const struct = structs[index];
  if (id.clock !== struct.id.clock + struct.length - 1 && struct.constructor !== GC) {
    structs.splice(index + 1, 0, splitItem(transaction, struct, id.clock - struct.id.clock + 1));
  }
  return struct
};

/**
 * Replace `item` with `newitem` in store
 * @param {StructStore} store
 * @param {GC|Item} struct
 * @param {GC|Item} newStruct
 *
 * @private
 * @function
 */
const replaceStruct = (store, struct, newStruct) => {
  const structs = /** @type {Array<GC|Item>} */ (store.clients.get(struct.id.client));
  structs[findIndexSS(structs, struct.id.clock)] = newStruct;
};

/**
 * Iterate over a range of structs
 *
 * @param {Transaction} transaction
 * @param {Array<Item|GC>} structs
 * @param {number} clockStart Inclusive start
 * @param {number} len
 * @param {function(GC|Item):void} f
 *
 * @function
 */
const iterateStructs = (transaction, structs, clockStart, len, f) => {
  if (len === 0) {
    return
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
  } while (index < structs.length && structs[index].id.clock < clockEnd)
};

/**
 * A transaction is created for every change on the Yjs model. It is possible
 * to bundle changes on the Yjs model in a single transaction to
 * minimize the number on messages sent and the number of observer calls.
 * If possible the user of this library should bundle as many changes as
 * possible. Here is an example to illustrate the advantages of bundling:
 *
 * @example
 * const ydoc = new Y.Doc()
 * const map = ydoc.getMap('map')
 * // Log content when change is triggered
 * map.observe(() => {
 *   console.log('change triggered')
 * })
 * // Each change on the map type triggers a log message:
 * map.set('a', 0) // => "change triggered"
 * map.set('b', 0) // => "change triggered"
 * // When put in a transaction, it will trigger the log after the transaction:
 * ydoc.transact(() => {
 *   map.set('a', 1)
 *   map.set('b', 1)
 * }) // => "change triggered"
 *
 * @public
 */
class Transaction {
  /**
   * @param {Doc} doc
   * @param {any} origin
   * @param {boolean} local
   */
  constructor (doc, origin, local) {
    /**
     * The Yjs instance.
     * @type {Doc}
     */
    this.doc = doc;
    /**
     * Describes the set of deleted items by ids
     * @type {DeleteSet}
     */
    this.deleteSet = new DeleteSet();
    /**
     * Holds the state before the transaction started.
     * @type {Map<Number,Number>}
     */
    this.beforeState = getStateVector(doc.store);
    /**
     * Holds the state after the transaction.
     * @type {Map<Number,Number>}
     */
    this.afterState = new Map();
    /**
     * All types that were directly modified (property added or child
     * inserted/deleted). New types are not included in this Set.
     * Maps from type to parentSubs (`item.parentSub = null` for YArray)
     * @type {Map<AbstractType<YEvent<any>>,Set<String|null>>}
     */
    this.changed = new Map();
    /**
     * Stores the events for the types that observe also child elements.
     * It is mainly used by `observeDeep`.
     * @type {Map<AbstractType<YEvent<any>>,Array<YEvent<any>>>}
     */
    this.changedParentTypes = new Map();
    /**
     * @type {Array<AbstractStruct>}
     */
    this._mergeStructs = [];
    /**
     * @type {any}
     */
    this.origin = origin;
    /**
     * Stores meta information on the transaction
     * @type {Map<any,any>}
     */
    this.meta = new Map();
    /**
     * Whether this change originates from this doc.
     * @type {boolean}
     */
    this.local = local;
    /**
     * @type {Set<Doc>}
     */
    this.subdocsAdded = new Set();
    /**
     * @type {Set<Doc>}
     */
    this.subdocsRemoved = new Set();
    /**
     * @type {Set<Doc>}
     */
    this.subdocsLoaded = new Set();
    /**
     * @type {boolean}
     */
    this._needFormattingCleanup = false;
  }
}

/**
 * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
 * @param {Transaction} transaction
 * @return {boolean} Whether data was written.
 */
const writeUpdateMessageFromTransaction = (encoder, transaction) => {
  if (transaction.deleteSet.clients.size === 0 && !any(transaction.afterState, (clock, client) => transaction.beforeState.get(client) !== clock)) {
    return false
  }
  sortAndMergeDeleteSet(transaction.deleteSet);
  writeStructsFromTransaction(encoder, transaction);
  writeDeleteSet(encoder, transaction.deleteSet);
  return true
};

/**
 * If `type.parent` was added in current transaction, `type` technically
 * did not change, it was just added and we should not fire events for `type`.
 *
 * @param {Transaction} transaction
 * @param {AbstractType<YEvent<any>>} type
 * @param {string|null} parentSub
 */
const addChangedTypeToTransaction = (transaction, type, parentSub) => {
  const item = type._item;
  if (item === null || (item.id.clock < (transaction.beforeState.get(item.id.client) || 0) && !item.deleted)) {
    setIfUndefined(transaction.changed, type, set_create).add(parentSub);
  }
};

/**
 * @param {Array<AbstractStruct>} structs
 * @param {number} pos
 * @return {number} # of merged structs
 */
const tryToMergeWithLefts = (structs, pos) => {
  let right = structs[pos];
  let left = structs[pos - 1];
  let i = pos;
  for (; i > 0; right = left, left = structs[--i - 1]) {
    if (left.deleted === right.deleted && left.constructor === right.constructor) {
      if (left.mergeWith(right)) {
        if (right instanceof Item && right.parentSub !== null && /** @type {AbstractType<any>} */ (right.parent)._map.get(right.parentSub) === right) {
          /** @type {AbstractType<any>} */ (right.parent)._map.set(right.parentSub, /** @type {Item} */ (left));
        }
        continue
      }
    }
    break
  }
  const merged = pos - i;
  if (merged) {
    // remove all merged structs from the array
    structs.splice(pos + 1 - merged, merged);
  }
  return merged
};

/**
 * @param {DeleteSet} ds
 * @param {StructStore} store
 * @param {function(Item):boolean} gcFilter
 */
const tryGcDeleteSet = (ds, store, gcFilter) => {
  for (const [client, deleteItems] of ds.clients.entries()) {
    const structs = /** @type {Array<GC|Item>} */ (store.clients.get(client));
    for (let di = deleteItems.length - 1; di >= 0; di--) {
      const deleteItem = deleteItems[di];
      const endDeleteItemClock = deleteItem.clock + deleteItem.len;
      for (
        let si = findIndexSS(structs, deleteItem.clock), struct = structs[si];
        si < structs.length && struct.id.clock < endDeleteItemClock;
        struct = structs[++si]
      ) {
        const struct = structs[si];
        if (deleteItem.clock + deleteItem.len <= struct.id.clock) {
          break
        }
        if (struct instanceof Item && struct.deleted && !struct.keep && gcFilter(struct)) {
          struct.gc(store, false);
        }
      }
    }
  }
};

/**
 * @param {DeleteSet} ds
 * @param {StructStore} store
 */
const tryMergeDeleteSet = (ds, store) => {
  // try to merge deleted / gc'd items
  // merge from right to left for better efficiency and so we don't miss any merge targets
  ds.clients.forEach((deleteItems, client) => {
    const structs = /** @type {Array<GC|Item>} */ (store.clients.get(client));
    for (let di = deleteItems.length - 1; di >= 0; di--) {
      const deleteItem = deleteItems[di];
      // start with merging the item next to the last deleted item
      const mostRightIndexToCheck = min(structs.length - 1, 1 + findIndexSS(structs, deleteItem.clock + deleteItem.len - 1));
      for (
        let si = mostRightIndexToCheck, struct = structs[si];
        si > 0 && struct.id.clock >= deleteItem.clock;
        struct = structs[si]
      ) {
        si -= 1 + tryToMergeWithLefts(structs, si);
      }
    }
  });
};

/**
 * @param {DeleteSet} ds
 * @param {StructStore} store
 * @param {function(Item):boolean} gcFilter
 */
const tryGc = (ds, store, gcFilter) => {
  tryGcDeleteSet(ds, store, gcFilter);
  tryMergeDeleteSet(ds, store);
};

/**
 * @param {Array<Transaction>} transactionCleanups
 * @param {number} i
 */
const cleanupTransactions = (transactionCleanups, i) => {
  if (i < transactionCleanups.length) {
    const transaction = transactionCleanups[i];
    const doc = transaction.doc;
    const store = doc.store;
    const ds = transaction.deleteSet;
    const mergeStructs = transaction._mergeStructs;
    try {
      sortAndMergeDeleteSet(ds);
      transaction.afterState = getStateVector(transaction.doc.store);
      doc.emit('beforeObserverCalls', [transaction, doc]);
      /**
       * An array of event callbacks.
       *
       * Each callback is called even if the other ones throw errors.
       *
       * @type {Array<function():void>}
       */
      const fs = [];
      // observe events on changed types
      transaction.changed.forEach((subs, itemtype) =>
        fs.push(() => {
          if (itemtype._item === null || !itemtype._item.deleted) {
            itemtype._callObserver(transaction, subs);
          }
        })
      );
      fs.push(() => {
        // deep observe events
        transaction.changedParentTypes.forEach((events, type) => {
          // We need to think about the possibility that the user transforms the
          // Y.Doc in the event.
          if (type._dEH.l.length > 0 && (type._item === null || !type._item.deleted)) {
            events = events
              .filter(event =>
                event.target._item === null || !event.target._item.deleted
              );
            events
              .forEach(event => {
                event.currentTarget = type;
                // path is relative to the current target
                event._path = null;
              });
            // sort events by path length so that top-level events are fired first.
            events
              .sort((event1, event2) => event1.path.length - event2.path.length);
            // We don't need to check for events.length
            // because we know it has at least one element
            callEventHandlerListeners(type._dEH, events, transaction);
          }
        });
      });
      fs.push(() => doc.emit('afterTransaction', [transaction, doc]));
      callAll(fs, []);
      if (transaction._needFormattingCleanup) {
        cleanupYTextAfterTransaction(transaction);
      }
    } finally {
      // Replace deleted items with ItemDeleted / GC.
      // This is where content is actually remove from the Yjs Doc.
      if (doc.gc) {
        tryGcDeleteSet(ds, store, doc.gcFilter);
      }
      tryMergeDeleteSet(ds, store);

      // on all affected store.clients props, try to merge
      transaction.afterState.forEach((clock, client) => {
        const beforeClock = transaction.beforeState.get(client) || 0;
        if (beforeClock !== clock) {
          const structs = /** @type {Array<GC|Item>} */ (store.clients.get(client));
          // we iterate from right to left so we can safely remove entries
          const firstChangePos = max(findIndexSS(structs, beforeClock), 1);
          for (let i = structs.length - 1; i >= firstChangePos;) {
            i -= 1 + tryToMergeWithLefts(structs, i);
          }
        }
      });
      // try to merge mergeStructs
      // @todo: it makes more sense to transform mergeStructs to a DS, sort it, and merge from right to left
      //        but at the moment DS does not handle duplicates
      for (let i = mergeStructs.length - 1; i >= 0; i--) {
        const { client, clock } = mergeStructs[i].id;
        const structs = /** @type {Array<GC|Item>} */ (store.clients.get(client));
        const replacedStructPos = findIndexSS(structs, clock);
        if (replacedStructPos + 1 < structs.length) {
          if (tryToMergeWithLefts(structs, replacedStructPos + 1) > 1) {
            continue // no need to perform next check, both are already merged
          }
        }
        if (replacedStructPos > 0) {
          tryToMergeWithLefts(structs, replacedStructPos);
        }
      }
      if (!transaction.local && transaction.afterState.get(doc.clientID) !== transaction.beforeState.get(doc.clientID)) {
        print(ORANGE, BOLD, '[yjs] ', UNBOLD, RED, 'Changed the client-id because another client seems to be using it.');
        doc.clientID = generateNewClientId();
      }
      // @todo Merge all the transactions into one and provide send the data as a single update message
      doc.emit('afterTransactionCleanup', [transaction, doc]);
      if (doc._observers.has('update')) {
        const encoder = new UpdateEncoderV1();
        const hasContent = writeUpdateMessageFromTransaction(encoder, transaction);
        if (hasContent) {
          doc.emit('update', [encoder.toUint8Array(), transaction.origin, doc, transaction]);
        }
      }
      if (doc._observers.has('updateV2')) {
        const encoder = new UpdateEncoderV2();
        const hasContent = writeUpdateMessageFromTransaction(encoder, transaction);
        if (hasContent) {
          doc.emit('updateV2', [encoder.toUint8Array(), transaction.origin, doc, transaction]);
        }
      }
      const { subdocsAdded, subdocsLoaded, subdocsRemoved } = transaction;
      if (subdocsAdded.size > 0 || subdocsRemoved.size > 0 || subdocsLoaded.size > 0) {
        subdocsAdded.forEach(subdoc => {
          subdoc.clientID = doc.clientID;
          if (subdoc.collectionid == null) {
            subdoc.collectionid = doc.collectionid;
          }
          doc.subdocs.add(subdoc);
        });
        subdocsRemoved.forEach(subdoc => doc.subdocs.delete(subdoc));
        doc.emit('subdocs', [{ loaded: subdocsLoaded, added: subdocsAdded, removed: subdocsRemoved }, doc, transaction]);
        subdocsRemoved.forEach(subdoc => subdoc.destroy());
      }

      if (transactionCleanups.length <= i + 1) {
        doc._transactionCleanups = [];
        doc.emit('afterAllTransactions', [doc, transactionCleanups]);
      } else {
        cleanupTransactions(transactionCleanups, i + 1);
      }
    }
  }
};

/**
 * Implements the functionality of `y.transact(()=>{..})`
 *
 * @template T
 * @param {Doc} doc
 * @param {function(Transaction):T} f
 * @param {any} [origin=true]
 * @return {T}
 *
 * @function
 */
const transact = (doc, f, origin = null, local = true) => {
  const transactionCleanups = doc._transactionCleanups;
  let initialCall = false;
  /**
   * @type {any}
   */
  let result = null;
  if (doc._transaction === null) {
    initialCall = true;
    doc._transaction = new Transaction(doc, origin, local);
    transactionCleanups.push(doc._transaction);
    if (transactionCleanups.length === 1) {
      doc.emit('beforeAllTransactions', [doc]);
    }
    doc.emit('beforeTransaction', [doc._transaction, doc]);
  }
  try {
    result = f(doc._transaction);
  } finally {
    if (initialCall) {
      const finishCleanup = doc._transaction === transactionCleanups[0];
      doc._transaction = null;
      if (finishCleanup) {
        // The first transaction ended, now process observer calls.
        // Observer call may create new transactions for which we need to call the observers and do cleanup.
        // We don't want to nest these calls, so we execute these calls one after
        // another.
        // Also we need to ensure that all cleanups are called, even if the
        // observes throw errors.
        // This file is full of hacky try {} finally {} blocks to ensure that an
        // event can throw errors and also that the cleanup is called.
        cleanupTransactions(transactionCleanups, 0);
      }
    }
  }
  return result
};

class StackItem {
  /**
   * @param {DeleteSet} deletions
   * @param {DeleteSet} insertions
   */
  constructor (deletions, insertions) {
    this.insertions = insertions;
    this.deletions = deletions;
    /**
     * Use this to save and restore metadata like selection range
     */
    this.meta = new Map();
  }
}
/**
 * @param {Transaction} tr
 * @param {UndoManager} um
 * @param {StackItem} stackItem
 */
const clearUndoManagerStackItem = (tr, um, stackItem) => {
  iterateDeletedStructs(tr, stackItem.deletions, item => {
    if (item instanceof Item && um.scope.some(type => type === tr.doc || yjs_isParentOf(/** @type {AbstractType<any>} */ (type), item))) {
      keepItem(item, false);
    }
  });
};

/**
 * @param {UndoManager} undoManager
 * @param {Array<StackItem>} stack
 * @param {'undo'|'redo'} eventType
 * @return {StackItem?}
 */
const popStackItem = (undoManager, stack, eventType) => {
  /**
   * Keep a reference to the transaction so we can fire the event with the changedParentTypes
   * @type {any}
   */
  let _tr = null;
  const doc = undoManager.doc;
  const scope = undoManager.scope;
  transact(doc, transaction => {
    while (stack.length > 0 && undoManager.currStackItem === null) {
      const store = doc.store;
      const stackItem = /** @type {StackItem} */ (stack.pop());
      /**
       * @type {Set<Item>}
       */
      const itemsToRedo = new Set();
      /**
       * @type {Array<Item>}
       */
      const itemsToDelete = [];
      let performedChange = false;
      iterateDeletedStructs(transaction, stackItem.insertions, struct => {
        if (struct instanceof Item) {
          if (struct.redone !== null) {
            let { item, diff } = followRedone(store, struct.id);
            if (diff > 0) {
              item = getItemCleanStart(transaction, createID(item.id.client, item.id.clock + diff));
            }
            struct = item;
          }
          if (!struct.deleted && scope.some(type => type === transaction.doc || yjs_isParentOf(/** @type {AbstractType<any>} */ (type), /** @type {Item} */ (struct)))) {
            itemsToDelete.push(struct);
          }
        }
      });
      iterateDeletedStructs(transaction, stackItem.deletions, struct => {
        if (
          struct instanceof Item &&
          scope.some(type => type === transaction.doc || yjs_isParentOf(/** @type {AbstractType<any>} */ (type), struct)) &&
          // Never redo structs in stackItem.insertions because they were created and deleted in the same capture interval.
          !isDeleted(stackItem.insertions, struct.id)
        ) {
          itemsToRedo.add(struct);
        }
      });
      itemsToRedo.forEach(struct => {
        performedChange = redoItem(transaction, struct, itemsToRedo, stackItem.insertions, undoManager.ignoreRemoteMapChanges, undoManager) !== null || performedChange;
      });
      // We want to delete in reverse order so that children are deleted before
      // parents, so we have more information available when items are filtered.
      for (let i = itemsToDelete.length - 1; i >= 0; i--) {
        const item = itemsToDelete[i];
        if (undoManager.deleteFilter(item)) {
          item.delete(transaction);
          performedChange = true;
        }
      }
      undoManager.currStackItem = performedChange ? stackItem : null;
    }
    transaction.changed.forEach((subProps, type) => {
      // destroy search marker if necessary
      if (subProps.has(null) && type._searchMarker) {
        type._searchMarker.length = 0;
      }
    });
    _tr = transaction;
  }, undoManager);
  const res = undoManager.currStackItem;
  if (res != null) {
    const changedParentTypes = _tr.changedParentTypes;
    undoManager.emit('stack-item-popped', [{ stackItem: res, type: eventType, changedParentTypes, origin: undoManager }, undoManager]);
    undoManager.currStackItem = null;
  }
  return res
};

/**
 * @typedef {Object} UndoManagerOptions
 * @property {number} [UndoManagerOptions.captureTimeout=500]
 * @property {function(Transaction):boolean} [UndoManagerOptions.captureTransaction] Do not capture changes of a Transaction if result false.
 * @property {function(Item):boolean} [UndoManagerOptions.deleteFilter=()=>true] Sometimes
 * it is necessary to filter what an Undo/Redo operation can delete. If this
 * filter returns false, the type/item won't be deleted even it is in the
 * undo/redo scope.
 * @property {Set<any>} [UndoManagerOptions.trackedOrigins=new Set([null])]
 * @property {boolean} [ignoreRemoteMapChanges] Experimental. By default, the UndoManager will never overwrite remote changes. Enable this property to enable overwriting remote changes on key-value changes (Y.Map, properties on Y.Xml, etc..).
 * @property {Doc} [doc] The document that this UndoManager operates on. Only needed if typeScope is empty.
 */

/**
 * @typedef {Object} StackItemEvent
 * @property {StackItem} StackItemEvent.stackItem
 * @property {any} StackItemEvent.origin
 * @property {'undo'|'redo'} StackItemEvent.type
 * @property {Map<AbstractType<YEvent<any>>,Array<YEvent<any>>>} StackItemEvent.changedParentTypes
 */

/**
 * Fires 'stack-item-added' event when a stack item was added to either the undo- or
 * the redo-stack. You may store additional stack information via the
 * metadata property on `event.stackItem.meta` (it is a `Map` of metadata properties).
 * Fires 'stack-item-popped' event when a stack item was popped from either the
 * undo- or the redo-stack. You may restore the saved stack information from `event.stackItem.meta`.
 *
 * @extends {ObservableV2<{'stack-item-added':function(StackItemEvent, UndoManager):void, 'stack-item-popped': function(StackItemEvent, UndoManager):void, 'stack-cleared': function({ undoStackCleared: boolean, redoStackCleared: boolean }):void, 'stack-item-updated': function(StackItemEvent, UndoManager):void }>}
 */
class UndoManager extends ObservableV2 {
  /**
   * @param {Doc|AbstractType<any>|Array<AbstractType<any>>} typeScope Limits the scope of the UndoManager. If this is set to a ydoc instance, all changes on that ydoc will be undone. If set to a specific type, only changes on that type or its children will be undone. Also accepts an array of types.
   * @param {UndoManagerOptions} options
   */
  constructor (typeScope, {
    captureTimeout = 500,
    captureTransaction = _tr => true,
    deleteFilter = () => true,
    trackedOrigins = new Set([null]),
    ignoreRemoteMapChanges = false,
    doc = /** @type {Doc} */ (isArray(typeScope) ? typeScope[0].doc : typeScope instanceof Doc ? typeScope : typeScope.doc)
  } = {}) {
    super();
    /**
     * @type {Array<AbstractType<any> | Doc>}
     */
    this.scope = [];
    this.doc = doc;
    this.addToScope(typeScope);
    this.deleteFilter = deleteFilter;
    trackedOrigins.add(this);
    this.trackedOrigins = trackedOrigins;
    this.captureTransaction = captureTransaction;
    /**
     * @type {Array<StackItem>}
     */
    this.undoStack = [];
    /**
     * @type {Array<StackItem>}
     */
    this.redoStack = [];
    /**
     * Whether the client is currently undoing (calling UndoManager.undo)
     *
     * @type {boolean}
     */
    this.undoing = false;
    this.redoing = false;
    /**
     * The currently popped stack item if UndoManager.undoing or UndoManager.redoing
     *
     * @type {StackItem|null}
     */
    this.currStackItem = null;
    this.lastChange = 0;
    this.ignoreRemoteMapChanges = ignoreRemoteMapChanges;
    this.captureTimeout = captureTimeout;
    /**
     * @param {Transaction} transaction
     */
    this.afterTransactionHandler = transaction => {
      // Only track certain transactions
      if (
        !this.captureTransaction(transaction) ||
        !this.scope.some(type => transaction.changedParentTypes.has(/** @type {AbstractType<any>} */ (type)) || type === this.doc) ||
        (!this.trackedOrigins.has(transaction.origin) && (!transaction.origin || !this.trackedOrigins.has(transaction.origin.constructor)))
      ) {
        return
      }
      const undoing = this.undoing;
      const redoing = this.redoing;
      const stack = undoing ? this.redoStack : this.undoStack;
      if (undoing) {
        this.stopCapturing(); // next undo should not be appended to last stack item
      } else if (!redoing) {
        // neither undoing nor redoing: delete redoStack
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
        // append change to last stack op
        const lastOp = stack[stack.length - 1];
        lastOp.deletions = mergeDeleteSets([lastOp.deletions, transaction.deleteSet]);
        lastOp.insertions = mergeDeleteSets([lastOp.insertions, insertions]);
      } else {
        // create a new stack op
        stack.push(new StackItem(transaction.deleteSet, insertions));
        didAdd = true;
      }
      if (!undoing && !redoing) {
        this.lastChange = now;
      }
      // make sure that deleted structs are not gc'd
      iterateDeletedStructs(transaction, transaction.deleteSet, /** @param {Item|GC} item */ item => {
        if (item instanceof Item && this.scope.some(type => type === transaction.doc || yjs_isParentOf(/** @type {AbstractType<any>} */ (type), item))) {
          keepItem(item, true);
        }
      });
      /**
       * @type {[StackItemEvent, UndoManager]}
       */
      const changeEvent = [{ stackItem: stack[stack.length - 1], origin: transaction.origin, type: undoing ? 'redo' : 'undo', changedParentTypes: transaction.changedParentTypes }, this];
      if (didAdd) {
        this.emit('stack-item-added', changeEvent);
      } else {
        this.emit('stack-item-updated', changeEvent);
      }
    };
    this.doc.on('afterTransaction', this.afterTransactionHandler);
    this.doc.on('destroy', () => {
      this.destroy();
    });
  }

  /**
   * Extend the scope.
   *
   * @param {Array<AbstractType<any> | Doc> | AbstractType<any> | Doc} ytypes
   */
  addToScope (ytypes) {
    const tmpSet = new Set(this.scope);
    ytypes = isArray(ytypes) ? ytypes : [ytypes];
    ytypes.forEach(ytype => {
      if (!tmpSet.has(ytype)) {
        tmpSet.add(ytype);
        if (ytype instanceof AbstractType ? ytype.doc !== this.doc : ytype !== this.doc) warn('[yjs#509] Not same Y.Doc'); // use MultiDocUndoManager instead. also see https://github.com/yjs/yjs/issues/509
        this.scope.push(ytype);
      }
    });
  }

  /**
   * @param {any} origin
   */
  addTrackedOrigin (origin) {
    this.trackedOrigins.add(origin);
  }

  /**
   * @param {any} origin
   */
  removeTrackedOrigin (origin) {
    this.trackedOrigins.delete(origin);
  }

  clear (clearUndoStack = true, clearRedoStack = true) {
    if ((clearUndoStack && this.canUndo()) || (clearRedoStack && this.canRedo())) {
      this.doc.transact(tr => {
        if (clearUndoStack) {
          this.undoStack.forEach(item => clearUndoManagerStackItem(tr, this, item));
          this.undoStack = [];
        }
        if (clearRedoStack) {
          this.redoStack.forEach(item => clearUndoManagerStackItem(tr, this, item));
          this.redoStack = [];
        }
        this.emit('stack-cleared', [{ undoStackCleared: clearUndoStack, redoStackCleared: clearRedoStack }]);
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
  stopCapturing () {
    this.lastChange = 0;
  }

  /**
   * Undo last changes on type.
   *
   * @return {StackItem?} Returns StackItem if a change was applied
   */
  undo () {
    this.undoing = true;
    let res;
    try {
      res = popStackItem(this, this.undoStack, 'undo');
    } finally {
      this.undoing = false;
    }
    return res
  }

  /**
   * Redo last undo operation.
   *
   * @return {StackItem?} Returns StackItem if a change was applied
   */
  redo () {
    this.redoing = true;
    let res;
    try {
      res = popStackItem(this, this.redoStack, 'redo');
    } finally {
      this.redoing = false;
    }
    return res
  }

  /**
   * Are undo steps available?
   *
   * @return {boolean} `true` if undo is possible
   */
  canUndo () {
    return this.undoStack.length > 0
  }

  /**
   * Are redo steps available?
   *
   * @return {boolean} `true` if redo is possible
   */
  canRedo () {
    return this.redoStack.length > 0
  }

  destroy () {
    this.trackedOrigins.delete(this);
    this.doc.off('afterTransaction', this.afterTransactionHandler);
    super.destroy();
  }
}

/**
 * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
 */
function * lazyStructReaderGenerator (decoder) {
  const numOfStateUpdates = decoding.readVarUint(decoder.restDecoder);
  for (let i = 0; i < numOfStateUpdates; i++) {
    const numberOfStructs = decoding.readVarUint(decoder.restDecoder);
    const client = decoder.readClient();
    let clock = decoding.readVarUint(decoder.restDecoder);
    for (let i = 0; i < numberOfStructs; i++) {
      const info = decoder.readInfo();
      // @todo use switch instead of ifs
      if (info === 10) {
        const len = decoding.readVarUint(decoder.restDecoder);
        yield new Skip(createID(client, clock), len);
        clock += len;
      } else if ((binary.BITS5 & info) !== 0) {
        const cantCopyParentInfo = (info & (binary.BIT7 | binary.BIT8)) === 0;
        // If parent = null and neither left nor right are defined, then we know that `parent` is child of `y`
        // and we read the next string as parentYKey.
        // It indicates how we store/retrieve parent from `y.share`
        // @type {string|null}
        const struct = new Item(
          createID(client, clock),
          null, // left
          (info & binary.BIT8) === binary.BIT8 ? decoder.readLeftID() : null, // origin
          null, // right
          (info & binary.BIT7) === binary.BIT7 ? decoder.readRightID() : null, // right origin
          // @ts-ignore Force writing a string here.
          cantCopyParentInfo ? (decoder.readParentInfo() ? decoder.readString() : decoder.readLeftID()) : null, // parent
          cantCopyParentInfo && (info & binary.BIT6) === binary.BIT6 ? decoder.readString() : null, // parentSub
          readItemContent(decoder, info) // item content
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

class LazyStructReader {
  /**
   * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
   * @param {boolean} filterSkips
   */
  constructor (decoder, filterSkips) {
    this.gen = lazyStructReaderGenerator(decoder);
    /**
     * @type {null | Item | Skip | GC}
     */
    this.curr = null;
    this.done = false;
    this.filterSkips = filterSkips;
    this.next();
  }

  /**
   * @return {Item | GC | Skip |null}
   */
  next () {
    // ignore "Skip" structs
    do {
      this.curr = this.gen.next().value || null;
    } while (this.filterSkips && this.curr !== null && this.curr.constructor === Skip)
    return this.curr
  }
}

/**
 * @param {Uint8Array} update
 *
 */
const logUpdate = update => logUpdateV2(update, UpdateDecoderV1);

/**
 * @param {Uint8Array} update
 * @param {typeof UpdateDecoderV2 | typeof UpdateDecoderV1} [YDecoder]
 *
 */
const logUpdateV2 = (update, YDecoder = UpdateDecoderV2) => {
  const structs = [];
  const updateDecoder = new YDecoder(decoding.createDecoder(update));
  const lazyDecoder = new LazyStructReader(updateDecoder, false);
  for (let curr = lazyDecoder.curr; curr !== null; curr = lazyDecoder.next()) {
    structs.push(curr);
  }
  logging.print('Structs: ', structs);
  const ds = readDeleteSet(updateDecoder);
  logging.print('DeleteSet: ', ds);
};

/**
 * @param {Uint8Array} update
 *
 */
const decodeUpdate = (update) => decodeUpdateV2(update, UpdateDecoderV1);

/**
 * @param {Uint8Array} update
 * @param {typeof UpdateDecoderV2 | typeof UpdateDecoderV1} [YDecoder]
 *
 */
const decodeUpdateV2 = (update, YDecoder = UpdateDecoderV2) => {
  const structs = [];
  const updateDecoder = new YDecoder(decoding.createDecoder(update));
  const lazyDecoder = new LazyStructReader(updateDecoder, false);
  for (let curr = lazyDecoder.curr; curr !== null; curr = lazyDecoder.next()) {
    structs.push(curr);
  }
  return {
    structs,
    ds: readDeleteSet(updateDecoder)
  }
};

class LazyStructWriter {
  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   */
  constructor (encoder) {
    this.currClient = 0;
    this.startClock = 0;
    this.written = 0;
    this.encoder = encoder;
    /**
     * We want to write operations lazily, but also we need to know beforehand how many operations we want to write for each client.
     *
     * This kind of meta-information (#clients, #structs-per-client-written) is written to the restEncoder.
     *
     * We fragment the restEncoder and store a slice of it per-client until we know how many clients there are.
     * When we flush (toUint8Array) we write the restEncoder using the fragments and the meta-information.
     *
     * @type {Array<{ written: number, restEncoder: Uint8Array }>}
     */
    this.clientStructs = [];
  }
}

/**
 * @param {Array<Uint8Array>} updates
 * @return {Uint8Array}
 */
const mergeUpdates = updates => mergeUpdatesV2(updates, UpdateDecoderV1, UpdateEncoderV1);

/**
 * @param {Uint8Array} update
 * @param {typeof DSEncoderV1 | typeof DSEncoderV2} YEncoder
 * @param {typeof UpdateDecoderV1 | typeof UpdateDecoderV2} YDecoder
 * @return {Uint8Array}
 */
const encodeStateVectorFromUpdateV2 = (update, YEncoder = DSEncoderV2, YDecoder = UpdateDecoderV2) => {
  const encoder = new YEncoder();
  const updateDecoder = new LazyStructReader(new YDecoder(decoding.createDecoder(update)), false);
  let curr = updateDecoder.curr;
  if (curr !== null) {
    let size = 0;
    let currClient = curr.id.client;
    let stopCounting = curr.id.clock !== 0; // must start at 0
    let currClock = stopCounting ? 0 : curr.id.clock + curr.length;
    for (; curr !== null; curr = updateDecoder.next()) {
      if (currClient !== curr.id.client) {
        if (currClock !== 0) {
          size++;
          // We found a new client
          // write what we have to the encoder
          encoding.writeVarUint(encoder.restEncoder, currClient);
          encoding.writeVarUint(encoder.restEncoder, currClock);
        }
        currClient = curr.id.client;
        currClock = 0;
        stopCounting = curr.id.clock !== 0;
      }
      // we ignore skips
      if (curr.constructor === Skip) {
        stopCounting = true;
      }
      if (!stopCounting) {
        currClock = curr.id.clock + curr.length;
      }
    }
    // write what we have
    if (currClock !== 0) {
      size++;
      encoding.writeVarUint(encoder.restEncoder, currClient);
      encoding.writeVarUint(encoder.restEncoder, currClock);
    }
    // prepend the size of the state vector
    const enc = encoding.createEncoder();
    encoding.writeVarUint(enc, size);
    encoding.writeBinaryEncoder(enc, encoder.restEncoder);
    encoder.restEncoder = enc;
    return encoder.toUint8Array()
  } else {
    encoding.writeVarUint(encoder.restEncoder, 0);
    return encoder.toUint8Array()
  }
};

/**
 * @param {Uint8Array} update
 * @return {Uint8Array}
 */
const encodeStateVectorFromUpdate = update => encodeStateVectorFromUpdateV2(update, DSEncoderV1, UpdateDecoderV1);

/**
 * @param {Uint8Array} update
 * @param {typeof UpdateDecoderV1 | typeof UpdateDecoderV2} YDecoder
 * @return {{ from: Map<number,number>, to: Map<number,number> }}
 */
const parseUpdateMetaV2 = (update, YDecoder = UpdateDecoderV2) => {
  /**
   * @type {Map<number, number>}
   */
  const from = new Map();
  /**
   * @type {Map<number, number>}
   */
  const to = new Map();
  const updateDecoder = new LazyStructReader(new YDecoder(decoding.createDecoder(update)), false);
  let curr = updateDecoder.curr;
  if (curr !== null) {
    let currClient = curr.id.client;
    let currClock = curr.id.clock;
    // write the beginning to `from`
    from.set(currClient, currClock);
    for (; curr !== null; curr = updateDecoder.next()) {
      if (currClient !== curr.id.client) {
        // We found a new client
        // write the end to `to`
        to.set(currClient, currClock);
        // write the beginning to `from`
        from.set(curr.id.client, curr.id.clock);
        // update currClient
        currClient = curr.id.client;
      }
      currClock = curr.id.clock + curr.length;
    }
    // write the end to `to`
    to.set(currClient, currClock);
  }
  return { from, to }
};

/**
 * @param {Uint8Array} update
 * @return {{ from: Map<number,number>, to: Map<number,number> }}
 */
const parseUpdateMeta = update => parseUpdateMetaV2(update, UpdateDecoderV1);

/**
 * This method is intended to slice any kind of struct and retrieve the right part.
 * It does not handle side-effects, so it should only be used by the lazy-encoder.
 *
 * @param {Item | GC | Skip} left
 * @param {number} diff
 * @return {Item | GC}
 */
const sliceStruct = (left, diff) => {
  if (left.constructor === GC) {
    const { client, clock } = left.id;
    return new GC(createID(client, clock + diff), left.length - diff)
  } else if (left.constructor === Skip) {
    const { client, clock } = left.id;
    return new Skip(createID(client, clock + diff), left.length - diff)
  } else {
    const leftItem = /** @type {Item} */ (left);
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
    )
  }
};

/**
 *
 * This function works similarly to `readUpdateV2`.
 *
 * @param {Array<Uint8Array>} updates
 * @param {typeof UpdateDecoderV1 | typeof UpdateDecoderV2} [YDecoder]
 * @param {typeof UpdateEncoderV1 | typeof UpdateEncoderV2} [YEncoder]
 * @return {Uint8Array}
 */
const mergeUpdatesV2 = (updates, YDecoder = UpdateDecoderV2, YEncoder = UpdateEncoderV2) => {
  if (updates.length === 1) {
    return updates[0]
  }
  const updateDecoders = updates.map(update => new YDecoder(decoding.createDecoder(update)));
  let lazyStructDecoders = updateDecoders.map(decoder => new LazyStructReader(decoder, true));

  /**
   * @todo we don't need offset because we always slice before
   * @type {null | { struct: Item | GC | Skip, offset: number }}
   */
  let currWrite = null;

  const updateEncoder = new YEncoder();
  // write structs lazily
  const lazyStructEncoder = new LazyStructWriter(updateEncoder);

  // Note: We need to ensure that all lazyStructDecoders are fully consumed
  // Note: Should merge document updates whenever possible - even from different updates
  // Note: Should handle that some operations cannot be applied yet ()

  while (true) {
    // Write higher clients first ⇒ sort by clientID & clock and remove decoders without content
    lazyStructDecoders = lazyStructDecoders.filter(dec => dec.curr !== null);
    lazyStructDecoders.sort(
      /** @type {function(any,any):number} */ (dec1, dec2) => {
        if (dec1.curr.id.client === dec2.curr.id.client) {
          const clockDiff = dec1.curr.id.clock - dec2.curr.id.clock;
          if (clockDiff === 0) {
            // @todo remove references to skip since the structDecoders must filter Skips.
            return dec1.curr.constructor === dec2.curr.constructor
              ? 0
              : dec1.curr.constructor === Skip ? 1 : -1 // we are filtering skips anyway.
          } else {
            return clockDiff
          }
        } else {
          return dec2.curr.id.client - dec1.curr.id.client
        }
      }
    );
    if (lazyStructDecoders.length === 0) {
      break
    }
    const currDecoder = lazyStructDecoders[0];
    // write from currDecoder until the next operation is from another client or if filler-struct
    // then we need to reorder the decoders and find the next operation to write
    const firstClient = /** @type {Item | GC} */ (currDecoder.curr).id.client;

    if (currWrite !== null) {
      let curr = /** @type {Item | GC | null} */ (currDecoder.curr);
      let iterated = false;

      // iterate until we find something that we haven't written already
      // remember: first the high client-ids are written
      while (curr !== null && curr.id.clock + curr.length <= currWrite.struct.id.clock + currWrite.struct.length && curr.id.client >= currWrite.struct.id.client) {
        curr = currDecoder.next();
        iterated = true;
      }
      if (
        curr === null || // current decoder is empty
        curr.id.client !== firstClient || // check whether there is another decoder that has has updates from `firstClient`
        (iterated && curr.id.clock > currWrite.struct.id.clock + currWrite.struct.length) // the above while loop was used and we are potentially missing updates
      ) {
        continue
      }

      if (firstClient !== currWrite.struct.id.client) {
        writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
        currWrite = { struct: curr, offset: 0 };
        currDecoder.next();
      } else {
        if (currWrite.struct.id.clock + currWrite.struct.length < curr.id.clock) {
          // @todo write currStruct & set currStruct = Skip(clock = currStruct.id.clock + currStruct.length, length = curr.id.clock - self.clock)
          if (currWrite.struct.constructor === Skip) {
            // extend existing skip
            currWrite.struct.length = curr.id.clock + curr.length - currWrite.struct.id.clock;
          } else {
            writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
            const diff = curr.id.clock - currWrite.struct.id.clock - currWrite.struct.length;
            /**
             * @type {Skip}
             */
            const struct = new Skip(createID(firstClient, currWrite.struct.id.clock + currWrite.struct.length), diff);
            currWrite = { struct, offset: 0 };
          }
        } else { // if (currWrite.struct.id.clock + currWrite.struct.length >= curr.id.clock) {
          const diff = currWrite.struct.id.clock + currWrite.struct.length - curr.id.clock;
          if (diff > 0) {
            if (currWrite.struct.constructor === Skip) {
              // prefer to slice Skip because the other struct might contain more information
              currWrite.struct.length -= diff;
            } else {
              curr = sliceStruct(curr, diff);
            }
          }
          if (!currWrite.struct.mergeWith(/** @type {any} */ (curr))) {
            writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
            currWrite = { struct: curr, offset: 0 };
            currDecoder.next();
          }
        }
      }
    } else {
      currWrite = { struct: /** @type {Item | GC} */ (currDecoder.curr), offset: 0 };
      currDecoder.next();
    }
    for (
      let next = currDecoder.curr;
      next !== null && next.id.client === firstClient && next.id.clock === currWrite.struct.id.clock + currWrite.struct.length && next.constructor !== Skip;
      next = currDecoder.next()
    ) {
      writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
      currWrite = { struct: next, offset: 0 };
    }
  }
  if (currWrite !== null) {
    writeStructToLazyStructWriter(lazyStructEncoder, currWrite.struct, currWrite.offset);
    currWrite = null;
  }
  finishLazyStructWriting(lazyStructEncoder);

  const dss = updateDecoders.map(decoder => readDeleteSet(decoder));
  const ds = mergeDeleteSets(dss);
  writeDeleteSet(updateEncoder, ds);
  return updateEncoder.toUint8Array()
};

/**
 * @param {Uint8Array} update
 * @param {Uint8Array} sv
 * @param {typeof UpdateDecoderV1 | typeof UpdateDecoderV2} [YDecoder]
 * @param {typeof UpdateEncoderV1 | typeof UpdateEncoderV2} [YEncoder]
 */
const diffUpdateV2 = (update, sv, YDecoder = UpdateDecoderV2, YEncoder = UpdateEncoderV2) => {
  const state = decodeStateVector(sv);
  const encoder = new YEncoder();
  const lazyStructWriter = new LazyStructWriter(encoder);
  const decoder = new YDecoder(decoding.createDecoder(update));
  const reader = new LazyStructReader(decoder, false);
  while (reader.curr) {
    const curr = reader.curr;
    const currClient = curr.id.client;
    const svClock = state.get(currClient) || 0;
    if (reader.curr.constructor === Skip) {
      // the first written struct shouldn't be a skip
      reader.next();
      continue
    }
    if (curr.id.clock + curr.length > svClock) {
      writeStructToLazyStructWriter(lazyStructWriter, curr, math.max(svClock - curr.id.clock, 0));
      reader.next();
      while (reader.curr && reader.curr.id.client === currClient) {
        writeStructToLazyStructWriter(lazyStructWriter, reader.curr, 0);
        reader.next();
      }
    } else {
      // read until something new comes up
      while (reader.curr && reader.curr.id.client === currClient && reader.curr.id.clock + reader.curr.length <= svClock) {
        reader.next();
      }
    }
  }
  finishLazyStructWriting(lazyStructWriter);
  // write ds
  const ds = readDeleteSet(decoder);
  writeDeleteSet(encoder, ds);
  return encoder.toUint8Array()
};

/**
 * @param {Uint8Array} update
 * @param {Uint8Array} sv
 */
const diffUpdate = (update, sv) => diffUpdateV2(update, sv, UpdateDecoderV1, UpdateEncoderV1);

/**
 * @param {LazyStructWriter} lazyWriter
 */
const flushLazyStructWriter = lazyWriter => {
  if (lazyWriter.written > 0) {
    lazyWriter.clientStructs.push({ written: lazyWriter.written, restEncoder: encoding.toUint8Array(lazyWriter.encoder.restEncoder) });
    lazyWriter.encoder.restEncoder = encoding.createEncoder();
    lazyWriter.written = 0;
  }
};

/**
 * @param {LazyStructWriter} lazyWriter
 * @param {Item | GC} struct
 * @param {number} offset
 */
const writeStructToLazyStructWriter = (lazyWriter, struct, offset) => {
  // flush curr if we start another client
  if (lazyWriter.written > 0 && lazyWriter.currClient !== struct.id.client) {
    flushLazyStructWriter(lazyWriter);
  }
  if (lazyWriter.written === 0) {
    lazyWriter.currClient = struct.id.client;
    // write next client
    lazyWriter.encoder.writeClient(struct.id.client);
    // write startClock
    encoding.writeVarUint(lazyWriter.encoder.restEncoder, struct.id.clock + offset);
  }
  struct.write(lazyWriter.encoder, offset);
  lazyWriter.written++;
};
/**
 * Call this function when we collected all parts and want to
 * put all the parts together. After calling this method,
 * you can continue using the UpdateEncoder.
 *
 * @param {LazyStructWriter} lazyWriter
 */
const finishLazyStructWriting = (lazyWriter) => {
  flushLazyStructWriter(lazyWriter);

  // this is a fresh encoder because we called flushCurr
  const restEncoder = lazyWriter.encoder.restEncoder;

  /**
   * Now we put all the fragments together.
   * This works similarly to `writeClientsStructs`
   */

  // write # states that were updated - i.e. the clients
  encoding.writeVarUint(restEncoder, lazyWriter.clientStructs.length);

  for (let i = 0; i < lazyWriter.clientStructs.length; i++) {
    const partStructs = lazyWriter.clientStructs[i];
    /**
     * Works similarly to `writeStructs`
     */
    // write # encoded structs
    encoding.writeVarUint(restEncoder, partStructs.written);
    // write the rest of the fragment
    encoding.writeUint8Array(restEncoder, partStructs.restEncoder);
  }
};

/**
 * @param {Uint8Array} update
 * @param {function(Item|GC|Skip):Item|GC|Skip} blockTransformer
 * @param {typeof UpdateDecoderV2 | typeof UpdateDecoderV1} YDecoder
 * @param {typeof UpdateEncoderV2 | typeof UpdateEncoderV1 } YEncoder
 */
const convertUpdateFormat = (update, blockTransformer, YDecoder, YEncoder) => {
  const updateDecoder = new YDecoder(decoding.createDecoder(update));
  const lazyDecoder = new LazyStructReader(updateDecoder, false);
  const updateEncoder = new YEncoder();
  const lazyWriter = new LazyStructWriter(updateEncoder);
  for (let curr = lazyDecoder.curr; curr !== null; curr = lazyDecoder.next()) {
    writeStructToLazyStructWriter(lazyWriter, blockTransformer(curr), 0);
  }
  finishLazyStructWriting(lazyWriter);
  const ds = readDeleteSet(updateDecoder);
  writeDeleteSet(updateEncoder, ds);
  return updateEncoder.toUint8Array()
};

/**
 * @typedef {Object} ObfuscatorOptions
 * @property {boolean} [ObfuscatorOptions.formatting=true]
 * @property {boolean} [ObfuscatorOptions.subdocs=true]
 * @property {boolean} [ObfuscatorOptions.yxml=true] Whether to obfuscate nodeName / hookName
 */

/**
 * @param {ObfuscatorOptions} obfuscator
 */
const createObfuscator = ({ formatting = true, subdocs = true, yxml = true } = {}) => {
  let i = 0;
  const mapKeyCache = map.create();
  const nodeNameCache = map.create();
  const formattingKeyCache = map.create();
  const formattingValueCache = map.create();
  formattingValueCache.set(null, null); // end of a formatting range should always be the end of a formatting range
  /**
   * @param {Item|GC|Skip} block
   * @return {Item|GC|Skip}
   */
  return block => {
    switch (block.constructor) {
      case GC:
      case Skip:
        return block
      case Item: {
        const item = /** @type {Item} */ (block);
        const content = item.content;
        switch (content.constructor) {
          case ContentDeleted:
            break
          case ContentType: {
            if (yxml) {
              const type = /** @type {ContentType} */ (content).type;
              if (type instanceof YXmlElement) {
                type.nodeName = map.setIfUndefined(nodeNameCache, type.nodeName, () => 'node-' + i);
              }
              if (type instanceof YXmlHook) {
                type.hookName = map.setIfUndefined(nodeNameCache, type.hookName, () => 'hook-' + i);
              }
            }
            break
          }
          case ContentAny: {
            const c = /** @type {ContentAny} */ (content);
            c.arr = c.arr.map(() => i);
            break
          }
          case ContentBinary: {
            const c = /** @type {ContentBinary} */ (content);
            c.content = new Uint8Array([i]);
            break
          }
          case ContentDoc: {
            const c = /** @type {ContentDoc} */ (content);
            if (subdocs) {
              c.opts = {};
              c.doc.guid = i + '';
            }
            break
          }
          case ContentEmbed: {
            const c = /** @type {ContentEmbed} */ (content);
            c.embed = {};
            break
          }
          case ContentFormat: {
            const c = /** @type {ContentFormat} */ (content);
            if (formatting) {
              c.key = map.setIfUndefined(formattingKeyCache, c.key, () => i + '');
              c.value = map.setIfUndefined(formattingValueCache, c.value, () => ({ i }));
            }
            break
          }
          case ContentJSON: {
            const c = /** @type {ContentJSON} */ (content);
            c.arr = c.arr.map(() => i);
            break
          }
          case ContentString: {
            const c = /** @type {ContentString} */ (content);
            c.str = string.repeat((i % 10) + '', c.str.length);
            break
          }
          default:
            // unknown content type
            error.unexpectedCase();
        }
        if (item.parentSub) {
          item.parentSub = map.setIfUndefined(mapKeyCache, item.parentSub, () => i + '');
        }
        i++;
        return block
      }
      default:
        // unknown block-type
        error.unexpectedCase();
    }
  }
};

/**
 * This function obfuscates the content of a Yjs update. This is useful to share
 * buggy Yjs documents while significantly limiting the possibility that a
 * developer can on the user. Note that it might still be possible to deduce
 * some information by analyzing the "structure" of the document or by analyzing
 * the typing behavior using the CRDT-related metadata that is still kept fully
 * intact.
 *
 * @param {Uint8Array} update
 * @param {ObfuscatorOptions} [opts]
 */
const obfuscateUpdate = (update, opts) => convertUpdateFormat(update, createObfuscator(opts), UpdateDecoderV1, UpdateEncoderV1);

/**
 * @param {Uint8Array} update
 * @param {ObfuscatorOptions} [opts]
 */
const obfuscateUpdateV2 = (update, opts) => convertUpdateFormat(update, createObfuscator(opts), UpdateDecoderV2, UpdateEncoderV2);

/**
 * @param {Uint8Array} update
 */
const convertUpdateFormatV1ToV2 = update => convertUpdateFormat(update, f.id, UpdateDecoderV1, UpdateEncoderV2);

/**
 * @param {Uint8Array} update
 */
const convertUpdateFormatV2ToV1 = update => convertUpdateFormat(update, f.id, UpdateDecoderV2, UpdateEncoderV1);

const errorComputeChanges = 'You must not compute changes after the event-handler fired.';

/**
 * @template {AbstractType<any>} T
 * YEvent describes the changes on a YType.
 */
class YEvent {
  /**
   * @param {T} target The changed type.
   * @param {Transaction} transaction
   */
  constructor (target, transaction) {
    /**
     * The type on which this event was created on.
     * @type {T}
     */
    this.target = target;
    /**
     * The current target on which the observe callback is called.
     * @type {AbstractType<any>}
     */
    this.currentTarget = target;
    /**
     * The transaction that triggered this event.
     * @type {Transaction}
     */
    this.transaction = transaction;
    /**
     * @type {Object|null}
     */
    this._changes = null;
    /**
     * @type {null | Map<string, { action: 'add' | 'update' | 'delete', oldValue: any, newValue: any }>}
     */
    this._keys = null;
    /**
     * @type {null | Array<{ insert?: string | Array<any> | object | AbstractType<any>, retain?: number, delete?: number, attributes?: Object<string, any> }>}
     */
    this._delta = null;
    /**
     * @type {Array<string|number>|null}
     */
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
  get path () {
    return this._path || (this._path = getPathTo(this.currentTarget, this.target))
  }

  /**
   * Check if a struct is deleted by this event.
   *
   * In contrast to change.deleted, this method also returns true if the struct was added and then deleted.
   *
   * @param {AbstractStruct} struct
   * @return {boolean}
   */
  deletes (struct) {
    return isDeleted(this.transaction.deleteSet, struct.id)
  }

  /**
   * @type {Map<string, { action: 'add' | 'update' | 'delete', oldValue: any, newValue: any }>}
   */
  get keys () {
    if (this._keys === null) {
      if (this.transaction.doc._transactionCleanups.length === 0) {
        throw error_create(errorComputeChanges)
      }
      const keys = new Map();
      const target = this.target;
      const changed = /** @type Set<string|null> */ (this.transaction.changed.get(target));
      changed.forEach(key => {
        if (key !== null) {
          const item = /** @type {Item} */ (target._map.get(key));
          /**
           * @type {'delete' | 'add' | 'update'}
           */
          let action;
          let oldValue;
          if (this.adds(item)) {
            let prev = item.left;
            while (prev !== null && this.adds(prev)) {
              prev = prev.left;
            }
            if (this.deletes(item)) {
              if (prev !== null && this.deletes(prev)) {
                action = 'delete';
                oldValue = last(prev.content.getContent());
              } else {
                return
              }
            } else {
              if (prev !== null && this.deletes(prev)) {
                action = 'update';
                oldValue = last(prev.content.getContent());
              } else {
                action = 'add';
                oldValue = undefined;
              }
            }
          } else {
            if (this.deletes(item)) {
              action = 'delete';
              oldValue = last(/** @type {Item} */ item.content.getContent());
            } else {
              return // nop
            }
          }
          keys.set(key, { action, oldValue });
        }
      });
      this._keys = keys;
    }
    return this._keys
  }

  /**
   * This is a computed property. Note that this can only be safely computed during the
   * event call. Computing this property after other changes happened might result in
   * unexpected behavior (incorrect computation of deltas). A safe way to collect changes
   * is to store the `changes` or the `delta` object. Avoid storing the `transaction` object.
   *
   * @type {Array<{insert?: string | Array<any> | object | AbstractType<any>, retain?: number, delete?: number, attributes?: Object<string, any>}>}
   */
  get delta () {
    return this.changes.delta
  }

  /**
   * Check if a struct is added by this event.
   *
   * In contrast to change.deleted, this method also returns true if the struct was added and then deleted.
   *
   * @param {AbstractStruct} struct
   * @return {boolean}
   */
  adds (struct) {
    return struct.id.clock >= (this.transaction.beforeState.get(struct.id.client) || 0)
  }

  /**
   * This is a computed property. Note that this can only be safely computed during the
   * event call. Computing this property after other changes happened might result in
   * unexpected behavior (incorrect computation of deltas). A safe way to collect changes
   * is to store the `changes` or the `delta` object. Avoid storing the `transaction` object.
   *
   * @type {{added:Set<Item>,deleted:Set<Item>,keys:Map<string,{action:'add'|'update'|'delete',oldValue:any}>,delta:Array<{insert?:Array<any>|string, delete?:number, retain?:number}>}}
   */
  get changes () {
    let changes = this._changes;
    if (changes === null) {
      if (this.transaction.doc._transactionCleanups.length === 0) {
        throw error_create(errorComputeChanges)
      }
      const target = this.target;
      const added = set_create();
      const deleted = set_create();
      /**
       * @type {Array<{insert:Array<any>}|{delete:number}|{retain:number}>}
       */
      const delta = [];
      changes = {
        added,
        deleted,
        delta,
        keys: this.keys
      };
      const changed = /** @type Set<string|null> */ (this.transaction.changed.get(target));
      if (changed.has(null)) {
        /**
         * @type {any}
         */
        let lastOp = null;
        const packOp = () => {
          if (lastOp) {
            delta.push(lastOp);
          }
        };
        for (let item = target._start; item !== null; item = item.right) {
          if (item.deleted) {
            if (this.deletes(item) && !this.adds(item)) {
              if (lastOp === null || lastOp.delete === undefined) {
                packOp();
                lastOp = { delete: 0 };
              }
              lastOp.delete += item.length;
              deleted.add(item);
            } // else nop
          } else {
            if (this.adds(item)) {
              if (lastOp === null || lastOp.insert === undefined) {
                packOp();
                lastOp = { insert: [] };
              }
              lastOp.insert = lastOp.insert.concat(item.content.getContent());
              added.add(item);
            } else {
              if (lastOp === null || lastOp.retain === undefined) {
                packOp();
                lastOp = { retain: 0 };
              }
              lastOp.retain += item.length;
            }
          }
        }
        if (lastOp !== null && lastOp.retain === undefined) {
          packOp();
        }
      }
      this._changes = changes;
    }
    return /** @type {any} */ (changes)
  }
}

/**
 * Compute the path from this type to the specified target.
 *
 * @example
 *   // `child` should be accessible via `type.get(path[0]).get(path[1])..`
 *   const path = type.getPathTo(child)
 *   // assuming `type instanceof YArray`
 *   console.log(path) // might look like => [2, 'key1']
 *   child === type.get(path[0]).get(path[1])
 *
 * @param {AbstractType<any>} parent
 * @param {AbstractType<any>} child target
 * @return {Array<string|number>} Path to the target
 *
 * @private
 * @function
 */
const getPathTo = (parent, child) => {
  const path = [];
  while (child._item !== null && child !== parent) {
    if (child._item.parentSub !== null) {
      // parent is map-ish
      path.unshift(child._item.parentSub);
    } else {
      // parent is array-ish
      let i = 0;
      let c = /** @type {AbstractType<any>} */ (child._item.parent)._start;
      while (c !== child._item && c !== null) {
        if (!c.deleted && c.countable) {
          i += c.length;
        }
        c = c.right;
      }
      path.unshift(i);
    }
    child = /** @type {AbstractType<any>} */ (child._item.parent);
  }
  return path
};

/**
 * https://docs.yjs.dev/getting-started/working-with-shared-types#caveats
 */
const warnPrematureAccess = () => { warn('Invalid access: Add Yjs type to a document before reading data.'); };

const maxSearchMarker = 80;

/**
 * A unique timestamp that identifies each marker.
 *
 * Time is relative,.. this is more like an ever-increasing clock.
 *
 * @type {number}
 */
let globalSearchMarkerTimestamp = 0;

class ArraySearchMarker {
  /**
   * @param {Item} p
   * @param {number} index
   */
  constructor (p, index) {
    p.marker = true;
    this.p = p;
    this.index = index;
    this.timestamp = globalSearchMarkerTimestamp++;
  }
}

/**
 * @param {ArraySearchMarker} marker
 */
const refreshMarkerTimestamp = marker => { marker.timestamp = globalSearchMarkerTimestamp++; };

/**
 * This is rather complex so this function is the only thing that should overwrite a marker
 *
 * @param {ArraySearchMarker} marker
 * @param {Item} p
 * @param {number} index
 */
const overwriteMarker = (marker, p, index) => {
  marker.p.marker = false;
  marker.p = p;
  p.marker = true;
  marker.index = index;
  marker.timestamp = globalSearchMarkerTimestamp++;
};

/**
 * @param {Array<ArraySearchMarker>} searchMarker
 * @param {Item} p
 * @param {number} index
 */
const markPosition = (searchMarker, p, index) => {
  if (searchMarker.length >= maxSearchMarker) {
    // override oldest marker (we don't want to create more objects)
    const marker = searchMarker.reduce((a, b) => a.timestamp < b.timestamp ? a : b);
    overwriteMarker(marker, p, index);
    return marker
  } else {
    // create new marker
    const pm = new ArraySearchMarker(p, index);
    searchMarker.push(pm);
    return pm
  }
};

/**
 * Search marker help us to find positions in the associative array faster.
 *
 * They speed up the process of finding a position without much bookkeeping.
 *
 * A maximum of `maxSearchMarker` objects are created.
 *
 * This function always returns a refreshed marker (updated timestamp)
 *
 * @param {AbstractType<any>} yarray
 * @param {number} index
 */
const findMarker = (yarray, index) => {
  if (yarray._start === null || index === 0 || yarray._searchMarker === null) {
    return null
  }
  const marker = yarray._searchMarker.length === 0 ? null : yarray._searchMarker.reduce((a, b) => abs(index - a.index) < abs(index - b.index) ? a : b);
  let p = yarray._start;
  let pindex = 0;
  if (marker !== null) {
    p = marker.p;
    pindex = marker.index;
    refreshMarkerTimestamp(marker); // we used it, we might need to use it again
  }
  // iterate to right if possible
  while (p.right !== null && pindex < index) {
    if (!p.deleted && p.countable) {
      if (index < pindex + p.length) {
        break
      }
      pindex += p.length;
    }
    p = p.right;
  }
  // iterate to left if necessary (might be that pindex > index)
  while (p.left !== null && pindex > index) {
    p = p.left;
    if (!p.deleted && p.countable) {
      pindex -= p.length;
    }
  }
  // we want to make sure that p can't be merged with left, because that would screw up everything
  // in that cas just return what we have (it is most likely the best marker anyway)
  // iterate to left until p can't be merged with left
  while (p.left !== null && p.left.id.client === p.id.client && p.left.id.clock + p.left.length === p.id.clock) {
    p = p.left;
    if (!p.deleted && p.countable) {
      pindex -= p.length;
    }
  }

  // @todo remove!
  // assure position
  // {
  //   let start = yarray._start
  //   let pos = 0
  //   while (start !== p) {
  //     if (!start.deleted && start.countable) {
  //       pos += start.length
  //     }
  //     start = /** @type {Item} */ (start.right)
  //   }
  //   if (pos !== pindex) {
  //     debugger
  //     throw new Error('Gotcha position fail!')
  //   }
  // }
  // if (marker) {
  //   if (window.lengths == null) {
  //     window.lengths = []
  //     window.getLengths = () => window.lengths.sort((a, b) => a - b)
  //   }
  //   window.lengths.push(marker.index - pindex)
  //   console.log('distance', marker.index - pindex, 'len', p && p.parent.length)
  // }
  if (marker !== null && abs(marker.index - pindex) < /** @type {YText|YArray<any>} */ (p.parent).length / maxSearchMarker) {
    // adjust existing marker
    overwriteMarker(marker, p, pindex);
    return marker
  } else {
    // create new marker
    return markPosition(yarray._searchMarker, p, pindex)
  }
};

/**
 * Update markers when a change happened.
 *
 * This should be called before doing a deletion!
 *
 * @param {Array<ArraySearchMarker>} searchMarker
 * @param {number} index
 * @param {number} len If insertion, len is positive. If deletion, len is negative.
 */
const updateMarkerChanges = (searchMarker, index, len) => {
  for (let i = searchMarker.length - 1; i >= 0; i--) {
    const m = searchMarker[i];
    if (len > 0) {
      /**
       * @type {Item|null}
       */
      let p = m.p;
      p.marker = false;
      // Ideally we just want to do a simple position comparison, but this will only work if
      // search markers don't point to deleted items for formats.
      // Iterate marker to prev undeleted countable position so we know what to do when updating a position
      while (p && (p.deleted || !p.countable)) {
        p = p.left;
        if (p && !p.deleted && p.countable) {
          // adjust position. the loop should break now
          m.index -= p.length;
        }
      }
      if (p === null || p.marker === true) {
        // remove search marker if updated position is null or if position is already marked
        searchMarker.splice(i, 1);
        continue
      }
      m.p = p;
      p.marker = true;
    }
    if (index < m.index || (len > 0 && index === m.index)) { // a simple index <= m.index check would actually suffice
      m.index = max(index, m.index + len);
    }
  }
};

/**
 * Accumulate all (list) children of a type and return them as an Array.
 *
 * @param {AbstractType<any>} t
 * @return {Array<Item>}
 */
const getTypeChildren = t => {
  t.doc ?? warnPrematureAccess();
  let s = t._start;
  const arr = [];
  while (s) {
    arr.push(s);
    s = s.right;
  }
  return arr
};

/**
 * Call event listeners with an event. This will also add an event to all
 * parents (for `.observeDeep` handlers).
 *
 * @template EventType
 * @param {AbstractType<EventType>} type
 * @param {Transaction} transaction
 * @param {EventType} event
 */
const callTypeObservers = (type, transaction, event) => {
  const changedType = type;
  const changedParentTypes = transaction.changedParentTypes;
  while (true) {
    // @ts-ignore
    setIfUndefined(changedParentTypes, type, () => []).push(event);
    if (type._item === null) {
      break
    }
    type = /** @type {AbstractType<any>} */ (type._item.parent);
  }
  callEventHandlerListeners(changedType._eH, event, transaction);
};

/**
 * @template EventType
 * Abstract Yjs Type class
 */
class AbstractType {
  constructor () {
    /**
     * @type {Item|null}
     */
    this._item = null;
    /**
     * @type {Map<string,Item>}
     */
    this._map = new Map();
    /**
     * @type {Item|null}
     */
    this._start = null;
    /**
     * @type {Doc|null}
     */
    this.doc = null;
    this._length = 0;
    /**
     * Event handlers
     * @type {EventHandler<EventType,Transaction>}
     */
    this._eH = createEventHandler();
    /**
     * Deep event handlers
     * @type {EventHandler<Array<YEvent<any>>,Transaction>}
     */
    this._dEH = createEventHandler();
    /**
     * @type {null | Array<ArraySearchMarker>}
     */
    this._searchMarker = null;
  }

  /**
   * @return {AbstractType<any>|null}
   */
  get parent () {
    return this._item ? /** @type {AbstractType<any>} */ (this._item.parent) : null
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
  _integrate (y, item) {
    this.doc = y;
    this._item = item;
  }

  /**
   * @return {AbstractType<EventType>}
   */
  _copy () {
    throw methodUnimplemented()
  }

  /**
   * Makes a copy of this data type that can be included somewhere else.
   *
   * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
   *
   * @return {AbstractType<EventType>}
   */
  clone () {
    throw methodUnimplemented()
  }

  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} _encoder
   */
  _write (_encoder) { }

  /**
   * The first non-deleted item
   */
  get _first () {
    let n = this._start;
    while (n !== null && n.deleted) {
      n = n.right;
    }
    return n
  }

  /**
   * Creates YEvent and calls all type observers.
   * Must be implemented by each type.
   *
   * @param {Transaction} transaction
   * @param {Set<null|string>} _parentSubs Keys changed on this type. `null` if list was modified.
   */
  _callObserver (transaction, _parentSubs) {
    if (!transaction.local && this._searchMarker) {
      this._searchMarker.length = 0;
    }
  }

  /**
   * Observe all events that are created on this type.
   *
   * @param {function(EventType, Transaction):void} f Observer function
   */
  observe (f) {
    addEventHandlerListener(this._eH, f);
  }

  /**
   * Observe all events that are created by this type and its children.
   *
   * @param {function(Array<YEvent<any>>,Transaction):void} f Observer function
   */
  observeDeep (f) {
    addEventHandlerListener(this._dEH, f);
  }

  /**
   * Unregister an observer function.
   *
   * @param {function(EventType,Transaction):void} f Observer function
   */
  unobserve (f) {
    removeEventHandlerListener(this._eH, f);
  }

  /**
   * Unregister an observer function.
   *
   * @param {function(Array<YEvent<any>>,Transaction):void} f Observer function
   */
  unobserveDeep (f) {
    removeEventHandlerListener(this._dEH, f);
  }

  /**
   * @abstract
   * @return {any}
   */
  toJSON () {}
}

/**
 * @param {AbstractType<any>} type
 * @param {number} start
 * @param {number} end
 * @return {Array<any>}
 *
 * @private
 * @function
 */
const typeListSlice = (type, start, end) => {
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
  return cs
};

/**
 * @param {AbstractType<any>} type
 * @return {Array<any>}
 *
 * @private
 * @function
 */
const typeListToArray = type => {
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
  return cs
};

/**
 * @param {AbstractType<any>} type
 * @param {Snapshot} snapshot
 * @return {Array<any>}
 *
 * @private
 * @function
 */
const typeListToArraySnapshot = (type, snapshot) => {
  const cs = [];
  let n = type._start;
  while (n !== null) {
    if (n.countable && isVisible(n, snapshot)) {
      const c = n.content.getContent();
      for (let i = 0; i < c.length; i++) {
        cs.push(c[i]);
      }
    }
    n = n.right;
  }
  return cs
};

/**
 * Executes a provided function on once on every element of this YArray.
 *
 * @param {AbstractType<any>} type
 * @param {function(any,number,any):void} f A function to execute on every element of this YArray.
 *
 * @private
 * @function
 */
const typeListForEach = (type, f) => {
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

/**
 * @template C,R
 * @param {AbstractType<any>} type
 * @param {function(C,number,AbstractType<any>):R} f
 * @return {Array<R>}
 *
 * @private
 * @function
 */
const typeListMap = (type, f) => {
  /**
   * @type {Array<any>}
   */
  const result = [];
  typeListForEach(type, (c, i) => {
    result.push(f(c, i, type));
  });
  return result
};

/**
 * @param {AbstractType<any>} type
 * @return {IterableIterator<any>}
 *
 * @private
 * @function
 */
const typeListCreateIterator = type => {
  let n = type._start;
  /**
   * @type {Array<any>|null}
   */
  let currentContent = null;
  let currentContentIndex = 0;
  return {
    [Symbol.iterator] () {
      return this
    },
    next: () => {
      // find some content
      if (currentContent === null) {
        while (n !== null && n.deleted) {
          n = n.right;
        }
        // check if we reached the end, no need to check currentContent, because it does not exist
        if (n === null) {
          return {
            done: true,
            value: undefined
          }
        }
        // we found n, so we can set currentContent
        currentContent = n.content.getContent();
        currentContentIndex = 0;
        n = n.right; // we used the content of n, now iterate to next
      }
      const value = currentContent[currentContentIndex++];
      // check if we need to empty currentContent
      if (currentContent.length <= currentContentIndex) {
        currentContent = null;
      }
      return {
        done: false,
        value
      }
    }
  }
};

/**
 * @param {AbstractType<any>} type
 * @param {number} index
 * @return {any}
 *
 * @private
 * @function
 */
const typeListGet = (type, index) => {
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
        return n.content.getContent()[index]
      }
      index -= n.length;
    }
  }
};

/**
 * @param {Transaction} transaction
 * @param {AbstractType<any>} parent
 * @param {Item?} referenceItem
 * @param {Array<Object<string,any>|Array<any>|boolean|number|null|string|Uint8Array>} content
 *
 * @private
 * @function
 */
const typeListInsertGenericsAfter = (transaction, parent, referenceItem, content) => {
  let left = referenceItem;
  const doc = transaction.doc;
  const ownClientId = doc.clientID;
  const store = doc.store;
  const right = referenceItem === null ? parent._start : referenceItem.right;
  /**
   * @type {Array<Object|Array<any>|number|null>}
   */
  let jsonContent = [];
  const packJsonContent = () => {
    if (jsonContent.length > 0) {
      left = new Item(createID(ownClientId, getState(store, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentAny(jsonContent));
      left.integrate(transaction, 0);
      jsonContent = [];
    }
  };
  content.forEach(c => {
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
          break
        default:
          packJsonContent();
          switch (c.constructor) {
            case Uint8Array:
            case ArrayBuffer:
              left = new Item(createID(ownClientId, getState(store, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentBinary(new Uint8Array(/** @type {Uint8Array} */ (c))));
              left.integrate(transaction, 0);
              break
            case Doc:
              left = new Item(createID(ownClientId, getState(store, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentDoc(/** @type {Doc} */ (c)));
              left.integrate(transaction, 0);
              break
            default:
              if (c instanceof AbstractType) {
                left = new Item(createID(ownClientId, getState(store, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentType(c));
                left.integrate(transaction, 0);
              } else {
                throw new Error('Unexpected content type in insert operation')
              }
          }
      }
    }
  });
  packJsonContent();
};

const lengthExceeded = () => error_create('Length exceeded!');

/**
 * @param {Transaction} transaction
 * @param {AbstractType<any>} parent
 * @param {number} index
 * @param {Array<Object<string,any>|Array<any>|number|null|string|Uint8Array>} content
 *
 * @private
 * @function
 */
const typeListInsertGenerics = (transaction, parent, index, content) => {
  if (index > parent._length) {
    throw lengthExceeded()
  }
  if (index === 0) {
    if (parent._searchMarker) {
      updateMarkerChanges(parent._searchMarker, index, content.length);
    }
    return typeListInsertGenericsAfter(transaction, parent, null, content)
  }
  const startIndex = index;
  const marker = findMarker(parent, index);
  let n = parent._start;
  if (marker !== null) {
    n = marker.p;
    index -= marker.index;
    // we need to iterate one to the left so that the algorithm works
    if (index === 0) {
      // @todo refactor this as it actually doesn't consider formats
      n = n.prev; // important! get the left undeleted item so that we can actually decrease index
      index += (n && n.countable && !n.deleted) ? n.length : 0;
    }
  }
  for (; n !== null; n = n.right) {
    if (!n.deleted && n.countable) {
      if (index <= n.length) {
        if (index < n.length) {
          // insert in-between
          getItemCleanStart(transaction, createID(n.id.client, n.id.clock + index));
        }
        break
      }
      index -= n.length;
    }
  }
  if (parent._searchMarker) {
    updateMarkerChanges(parent._searchMarker, startIndex, content.length);
  }
  return typeListInsertGenericsAfter(transaction, parent, n, content)
};

/**
 * Pushing content is special as we generally want to push after the last item. So we don't have to update
 * the search marker.
 *
 * @param {Transaction} transaction
 * @param {AbstractType<any>} parent
 * @param {Array<Object<string,any>|Array<any>|number|null|string|Uint8Array>} content
 *
 * @private
 * @function
 */
const typeListPushGenerics = (transaction, parent, content) => {
  // Use the marker with the highest index and iterate to the right.
  const marker = (parent._searchMarker || []).reduce((maxMarker, currMarker) => currMarker.index > maxMarker.index ? currMarker : maxMarker, { index: 0, p: parent._start });
  let n = marker.p;
  if (n) {
    while (n.right) {
      n = n.right;
    }
  }
  return typeListInsertGenericsAfter(transaction, parent, n, content)
};

/**
 * @param {Transaction} transaction
 * @param {AbstractType<any>} parent
 * @param {number} index
 * @param {number} length
 *
 * @private
 * @function
 */
const typeListDelete = (transaction, parent, index, length) => {
  if (length === 0) { return }
  const startIndex = index;
  const startLength = length;
  const marker = findMarker(parent, index);
  let n = parent._start;
  if (marker !== null) {
    n = marker.p;
    index -= marker.index;
  }
  // compute the first item to be deleted
  for (; n !== null && index > 0; n = n.right) {
    if (!n.deleted && n.countable) {
      if (index < n.length) {
        getItemCleanStart(transaction, createID(n.id.client, n.id.clock + index));
      }
      index -= n.length;
    }
  }
  // delete all items until done
  while (length > 0 && n !== null) {
    if (!n.deleted) {
      if (length < n.length) {
        getItemCleanStart(transaction, createID(n.id.client, n.id.clock + length));
      }
      n.delete(transaction);
      length -= n.length;
    }
    n = n.right;
  }
  if (length > 0) {
    throw lengthExceeded()
  }
  if (parent._searchMarker) {
    updateMarkerChanges(parent._searchMarker, startIndex, -startLength + length /* in case we remove the above exception */);
  }
};

/**
 * @param {Transaction} transaction
 * @param {AbstractType<any>} parent
 * @param {string} key
 *
 * @private
 * @function
 */
const typeMapDelete = (transaction, parent, key) => {
  const c = parent._map.get(key);
  if (c !== undefined) {
    c.delete(transaction);
  }
};

/**
 * @param {Transaction} transaction
 * @param {AbstractType<any>} parent
 * @param {string} key
 * @param {Object|number|null|Array<any>|string|Uint8Array|AbstractType<any>} value
 *
 * @private
 * @function
 */
const typeMapSet = (transaction, parent, key, value) => {
  const left = parent._map.get(key) || null;
  const doc = transaction.doc;
  const ownClientId = doc.clientID;
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
        break
      case Uint8Array:
        content = new ContentBinary(/** @type {Uint8Array} */ (value));
        break
      case Doc:
        content = new ContentDoc(/** @type {Doc} */ (value));
        break
      default:
        if (value instanceof AbstractType) {
          content = new ContentType(value);
        } else {
          throw new Error('Unexpected content type')
        }
    }
  }
  new Item(createID(ownClientId, getState(doc.store, ownClientId)), left, left && left.lastId, null, null, parent, key, content).integrate(transaction, 0);
};

/**
 * @param {AbstractType<any>} parent
 * @param {string} key
 * @return {Object<string,any>|number|null|Array<any>|string|Uint8Array|AbstractType<any>|undefined}
 *
 * @private
 * @function
 */
const typeMapGet = (parent, key) => {
  parent.doc ?? warnPrematureAccess();
  const val = parent._map.get(key);
  return val !== undefined && !val.deleted ? val.content.getContent()[val.length - 1] : undefined
};

/**
 * @param {AbstractType<any>} parent
 * @return {Object<string,Object<string,any>|number|null|Array<any>|string|Uint8Array|AbstractType<any>|undefined>}
 *
 * @private
 * @function
 */
const typeMapGetAll = (parent) => {
  /**
   * @type {Object<string,any>}
   */
  const res = {};
  parent.doc ?? warnPrematureAccess();
  parent._map.forEach((value, key) => {
    if (!value.deleted) {
      res[key] = value.content.getContent()[value.length - 1];
    }
  });
  return res
};

/**
 * @param {AbstractType<any>} parent
 * @param {string} key
 * @return {boolean}
 *
 * @private
 * @function
 */
const typeMapHas = (parent, key) => {
  parent.doc ?? warnPrematureAccess();
  const val = parent._map.get(key);
  return val !== undefined && !val.deleted
};

/**
 * @param {AbstractType<any>} parent
 * @param {string} key
 * @param {Snapshot} snapshot
 * @return {Object<string,any>|number|null|Array<any>|string|Uint8Array|AbstractType<any>|undefined}
 *
 * @private
 * @function
 */
const typeMapGetSnapshot = (parent, key, snapshot) => {
  let v = parent._map.get(key) || null;
  while (v !== null && (!snapshot.sv.has(v.id.client) || v.id.clock >= (snapshot.sv.get(v.id.client) || 0))) {
    v = v.left;
  }
  return v !== null && isVisible(v, snapshot) ? v.content.getContent()[v.length - 1] : undefined
};

/**
 * @param {AbstractType<any>} parent
 * @param {Snapshot} snapshot
 * @return {Object<string,Object<string,any>|number|null|Array<any>|string|Uint8Array|AbstractType<any>|undefined>}
 *
 * @private
 * @function
 */
const typeMapGetAllSnapshot = (parent, snapshot) => {
  /**
   * @type {Object<string,any>}
   */
  const res = {};
  parent._map.forEach((value, key) => {
    /**
     * @type {Item|null}
     */
    let v = value;
    while (v !== null && (!snapshot.sv.has(v.id.client) || v.id.clock >= (snapshot.sv.get(v.id.client) || 0))) {
      v = v.left;
    }
    if (v !== null && isVisible(v, snapshot)) {
      res[key] = v.content.getContent()[v.length - 1];
    }
  });
  return res
};

/**
 * @param {AbstractType<any> & { _map: Map<string, Item> }} type
 * @return {IterableIterator<Array<any>>}
 *
 * @private
 * @function
 */
const createMapIterator = type => {
  type.doc ?? warnPrematureAccess();
  return iteratorFilter(type._map.entries(), /** @param {any} entry */ entry => !entry[1].deleted)
};

/**
 * @module YArray
 */


/**
 * Event that describes the changes on a YArray
 * @template T
 * @extends YEvent<YArray<T>>
 */
class YArrayEvent extends YEvent {}

/**
 * A shared Array implementation.
 * @template T
 * @extends AbstractType<YArrayEvent<T>>
 * @implements {Iterable<T>}
 */
class YArray extends AbstractType {
  constructor () {
    super();
    /**
     * @type {Array<any>?}
     * @private
     */
    this._prelimContent = [];
    /**
     * @type {Array<ArraySearchMarker>}
     */
    this._searchMarker = [];
  }

  /**
   * Construct a new YArray containing the specified items.
   * @template {Object<string,any>|Array<any>|number|null|string|Uint8Array} T
   * @param {Array<T>} items
   * @return {YArray<T>}
   */
  static from (items) {
    /**
     * @type {YArray<T>}
     */
    const a = new YArray();
    a.push(items);
    return a
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
  _integrate (y, item) {
    super._integrate(y, item);
    this.insert(0, /** @type {Array<any>} */ (this._prelimContent));
    this._prelimContent = null;
  }

  /**
   * @return {YArray<T>}
   */
  _copy () {
    return new YArray()
  }

  /**
   * Makes a copy of this data type that can be included somewhere else.
   *
   * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
   *
   * @return {YArray<T>}
   */
  clone () {
    /**
     * @type {YArray<T>}
     */
    const arr = new YArray();
    arr.insert(0, this.toArray().map(el =>
      el instanceof AbstractType ? /** @type {typeof el} */ (el.clone()) : el
    ));
    return arr
  }

  get length () {
    this.doc ?? warnPrematureAccess();
    return this._length
  }

  /**
   * Creates YArrayEvent and calls observers.
   *
   * @param {Transaction} transaction
   * @param {Set<null|string>} parentSubs Keys changed on this type. `null` if list was modified.
   */
  _callObserver (transaction, parentSubs) {
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
  insert (index, content) {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        typeListInsertGenerics(transaction, this, index, /** @type {any} */ (content));
      });
    } else {
      /** @type {Array<any>} */ (this._prelimContent).splice(index, 0, ...content);
    }
  }

  /**
   * Appends content to this YArray.
   *
   * @param {Array<T>} content Array of content to append.
   *
   * @todo Use the following implementation in all types.
   */
  push (content) {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        typeListPushGenerics(transaction, this, /** @type {any} */ (content));
      });
    } else {
      /** @type {Array<any>} */ (this._prelimContent).push(...content);
    }
  }

  /**
   * Prepends content to this YArray.
   *
   * @param {Array<T>} content Array of content to prepend.
   */
  unshift (content) {
    this.insert(0, content);
  }

  /**
   * Deletes elements starting from an index.
   *
   * @param {number} index Index at which to start deleting elements
   * @param {number} length The number of elements to remove. Defaults to 1.
   */
  delete (index, length = 1) {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        typeListDelete(transaction, this, index, length);
      });
    } else {
      /** @type {Array<any>} */ (this._prelimContent).splice(index, length);
    }
  }

  /**
   * Returns the i-th element from a YArray.
   *
   * @param {number} index The index of the element to return from the YArray
   * @return {T}
   */
  get (index) {
    return typeListGet(this, index)
  }

  /**
   * Transforms this YArray to a JavaScript Array.
   *
   * @return {Array<T>}
   */
  toArray () {
    return typeListToArray(this)
  }

  /**
   * Returns a portion of this YArray into a JavaScript Array selected
   * from start to end (end not included).
   *
   * @param {number} [start]
   * @param {number} [end]
   * @return {Array<T>}
   */
  slice (start = 0, end = this.length) {
    return typeListSlice(this, start, end)
  }

  /**
   * Transforms this Shared Type to a JSON object.
   *
   * @return {Array<any>}
   */
  toJSON () {
    return this.map(c => c instanceof AbstractType ? c.toJSON() : c)
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
  map (f) {
    return typeListMap(this, /** @type {any} */ (f))
  }

  /**
   * Executes a provided function once on every element of this YArray.
   *
   * @param {function(T,number,YArray<T>):void} f A function to execute on every element of this YArray.
   */
  forEach (f) {
    typeListForEach(this, f);
  }

  /**
   * @return {IterableIterator<T>}
   */
  [Symbol.iterator] () {
    return typeListCreateIterator(this)
  }

  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   */
  _write (encoder) {
    encoder.writeTypeRef(YArrayRefID);
  }
}

/**
 * @param {UpdateDecoderV1 | UpdateDecoderV2} _decoder
 *
 * @private
 * @function
 */
const readYArray = _decoder => new YArray();

/**
 * @module YMap
 */


/**
 * @template T
 * @extends YEvent<YMap<T>>
 * Event that describes the changes on a YMap.
 */
class YMapEvent extends YEvent {
  /**
   * @param {YMap<T>} ymap The YArray that changed.
   * @param {Transaction} transaction
   * @param {Set<any>} subs The keys that changed.
   */
  constructor (ymap, transaction, subs) {
    super(ymap, transaction);
    this.keysChanged = subs;
  }
}

/**
 * @template MapType
 * A shared Map implementation.
 *
 * @extends AbstractType<YMapEvent<MapType>>
 * @implements {Iterable<[string, MapType]>}
 */
class YMap extends AbstractType {
  /**
   *
   * @param {Iterable<readonly [string, any]>=} entries - an optional iterable to initialize the YMap
   */
  constructor (entries) {
    super();
    /**
     * @type {Map<string,any>?}
     * @private
     */
    this._prelimContent = null;

    if (entries === undefined) {
      this._prelimContent = new Map();
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
  _integrate (y, item) {
    super._integrate(y, item)
    ;/** @type {Map<string, any>} */ (this._prelimContent).forEach((value, key) => {
      this.set(key, value);
    });
    this._prelimContent = null;
  }

  /**
   * @return {YMap<MapType>}
   */
  _copy () {
    return new YMap()
  }

  /**
   * Makes a copy of this data type that can be included somewhere else.
   *
   * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
   *
   * @return {YMap<MapType>}
   */
  clone () {
    /**
     * @type {YMap<MapType>}
     */
    const map = new YMap();
    this.forEach((value, key) => {
      map.set(key, value instanceof AbstractType ? /** @type {typeof value} */ (value.clone()) : value);
    });
    return map
  }

  /**
   * Creates YMapEvent and calls observers.
   *
   * @param {Transaction} transaction
   * @param {Set<null|string>} parentSubs Keys changed on this type. `null` if list was modified.
   */
  _callObserver (transaction, parentSubs) {
    callTypeObservers(this, transaction, new YMapEvent(this, transaction, parentSubs));
  }

  /**
   * Transforms this Shared Type to a JSON object.
   *
   * @return {Object<string,any>}
   */
  toJSON () {
    this.doc ?? warnPrematureAccess();
    /**
     * @type {Object<string,MapType>}
     */
    const map = {};
    this._map.forEach((item, key) => {
      if (!item.deleted) {
        const v = item.content.getContent()[item.length - 1];
        map[key] = v instanceof AbstractType ? v.toJSON() : v;
      }
    });
    return map
  }

  /**
   * Returns the size of the YMap (count of key/value pairs)
   *
   * @return {number}
   */
  get size () {
    return [...createMapIterator(this)].length
  }

  /**
   * Returns the keys for each element in the YMap Type.
   *
   * @return {IterableIterator<string>}
   */
  keys () {
    return iteratorMap(createMapIterator(this), /** @param {any} v */ v => v[0])
  }

  /**
   * Returns the values for each element in the YMap Type.
   *
   * @return {IterableIterator<MapType>}
   */
  values () {
    return iteratorMap(createMapIterator(this), /** @param {any} v */ v => v[1].content.getContent()[v[1].length - 1])
  }

  /**
   * Returns an Iterator of [key, value] pairs
   *
   * @return {IterableIterator<[string, MapType]>}
   */
  entries () {
    return iteratorMap(createMapIterator(this), /** @param {any} v */ v => /** @type {any} */ ([v[0], v[1].content.getContent()[v[1].length - 1]]))
  }

  /**
   * Executes a provided function on once on every key-value pair.
   *
   * @param {function(MapType,string,YMap<MapType>):void} f A function to execute on every element of this YArray.
   */
  forEach (f) {
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
  [Symbol.iterator] () {
    return this.entries()
  }

  /**
   * Remove a specified element from this YMap.
   *
   * @param {string} key The key of the element to remove.
   */
  delete (key) {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        typeMapDelete(transaction, this, key);
      });
    } else {
      /** @type {Map<string, any>} */ (this._prelimContent).delete(key);
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
  set (key, value) {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        typeMapSet(transaction, this, key, /** @type {any} */ (value));
      });
    } else {
      /** @type {Map<string, any>} */ (this._prelimContent).set(key, value);
    }
    return value
  }

  /**
   * Returns a specified element from this YMap.
   *
   * @param {string} key
   * @return {MapType|undefined}
   */
  get (key) {
    return /** @type {any} */ (typeMapGet(this, key))
  }

  /**
   * Returns a boolean indicating whether the specified key exists or not.
   *
   * @param {string} key The key to test.
   * @return {boolean}
   */
  has (key) {
    return typeMapHas(this, key)
  }

  /**
   * Removes all elements from this YMap.
   */
  clear () {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        this.forEach(function (_value, key, map) {
          typeMapDelete(transaction, map, key);
        });
      });
    } else {
      /** @type {Map<string, any>} */ (this._prelimContent).clear();
    }
  }

  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   */
  _write (encoder) {
    encoder.writeTypeRef(YMapRefID);
  }
}

/**
 * @param {UpdateDecoderV1 | UpdateDecoderV2} _decoder
 *
 * @private
 * @function
 */
const readYMap = _decoder => new YMap();

/**
 * @module YText
 */


/**
 * @param {any} a
 * @param {any} b
 * @return {boolean}
 */
const equalAttrs = (a, b) => a === b || (typeof a === 'object' && typeof b === 'object' && a && b && object_equalFlat(a, b));

class ItemTextListPosition {
  /**
   * @param {Item|null} left
   * @param {Item|null} right
   * @param {number} index
   * @param {Map<string,any>} currentAttributes
   */
  constructor (left, right, index, currentAttributes) {
    this.left = left;
    this.right = right;
    this.index = index;
    this.currentAttributes = currentAttributes;
  }

  /**
   * Only call this if you know that this.right is defined
   */
  forward () {
    if (this.right === null) {
      unexpectedCase();
    }
    switch (this.right.content.constructor) {
      case ContentFormat:
        if (!this.right.deleted) {
          updateCurrentAttributes(this.currentAttributes, /** @type {ContentFormat} */ (this.right.content));
        }
        break
      default:
        if (!this.right.deleted) {
          this.index += this.right.length;
        }
        break
    }
    this.left = this.right;
    this.right = this.right.right;
  }
}

/**
 * @param {Transaction} transaction
 * @param {ItemTextListPosition} pos
 * @param {number} count steps to move forward
 * @return {ItemTextListPosition}
 *
 * @private
 * @function
 */
const findNextPosition = (transaction, pos, count) => {
  while (pos.right !== null && count > 0) {
    switch (pos.right.content.constructor) {
      case ContentFormat:
        if (!pos.right.deleted) {
          updateCurrentAttributes(pos.currentAttributes, /** @type {ContentFormat} */ (pos.right.content));
        }
        break
      default:
        if (!pos.right.deleted) {
          if (count < pos.right.length) {
            // split right
            getItemCleanStart(transaction, createID(pos.right.id.client, pos.right.id.clock + count));
          }
          pos.index += pos.right.length;
          count -= pos.right.length;
        }
        break
    }
    pos.left = pos.right;
    pos.right = pos.right.right;
    // pos.forward() - we don't forward because that would halve the performance because we already do the checks above
  }
  return pos
};

/**
 * @param {Transaction} transaction
 * @param {AbstractType<any>} parent
 * @param {number} index
 * @param {boolean} useSearchMarker
 * @return {ItemTextListPosition}
 *
 * @private
 * @function
 */
const findPosition = (transaction, parent, index, useSearchMarker) => {
  const currentAttributes = new Map();
  const marker = useSearchMarker ? findMarker(parent, index) : null;
  if (marker) {
    const pos = new ItemTextListPosition(marker.p.left, marker.p, marker.index, currentAttributes);
    return findNextPosition(transaction, pos, index - marker.index)
  } else {
    const pos = new ItemTextListPosition(null, parent._start, 0, currentAttributes);
    return findNextPosition(transaction, pos, index)
  }
};

/**
 * Negate applied formats
 *
 * @param {Transaction} transaction
 * @param {AbstractType<any>} parent
 * @param {ItemTextListPosition} currPos
 * @param {Map<string,any>} negatedAttributes
 *
 * @private
 * @function
 */
const insertNegatedAttributes = (transaction, parent, currPos, negatedAttributes) => {
  // check if we really need to remove attributes
  while (
    currPos.right !== null && (
      currPos.right.deleted === true || (
        currPos.right.content.constructor === ContentFormat &&
        equalAttrs(negatedAttributes.get(/** @type {ContentFormat} */ (currPos.right.content).key), /** @type {ContentFormat} */ (currPos.right.content).value)
      )
    )
  ) {
    if (!currPos.right.deleted) {
      negatedAttributes.delete(/** @type {ContentFormat} */ (currPos.right.content).key);
    }
    currPos.forward();
  }
  const doc = transaction.doc;
  const ownClientId = doc.clientID;
  negatedAttributes.forEach((val, key) => {
    const left = currPos.left;
    const right = currPos.right;
    const nextFormat = new Item(createID(ownClientId, getState(doc.store, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentFormat(key, val));
    nextFormat.integrate(transaction, 0);
    currPos.right = nextFormat;
    currPos.forward();
  });
};

/**
 * @param {Map<string,any>} currentAttributes
 * @param {ContentFormat} format
 *
 * @private
 * @function
 */
const updateCurrentAttributes = (currentAttributes, format) => {
  const { key, value } = format;
  if (value === null) {
    currentAttributes.delete(key);
  } else {
    currentAttributes.set(key, value);
  }
};

/**
 * @param {ItemTextListPosition} currPos
 * @param {Object<string,any>} attributes
 *
 * @private
 * @function
 */
const minimizeAttributeChanges = (currPos, attributes) => {
  // go right while attributes[right.key] === right.value (or right is deleted)
  while (true) {
    if (currPos.right === null) {
      break
    } else if (currPos.right.deleted || (currPos.right.content.constructor === ContentFormat && equalAttrs(attributes[(/** @type {ContentFormat} */ (currPos.right.content)).key] ?? null, /** @type {ContentFormat} */ (currPos.right.content).value))) ; else {
      break
    }
    currPos.forward();
  }
};

/**
 * @param {Transaction} transaction
 * @param {AbstractType<any>} parent
 * @param {ItemTextListPosition} currPos
 * @param {Object<string,any>} attributes
 * @return {Map<string,any>}
 *
 * @private
 * @function
 **/
const insertAttributes = (transaction, parent, currPos, attributes) => {
  const doc = transaction.doc;
  const ownClientId = doc.clientID;
  const negatedAttributes = new Map();
  // insert format-start items
  for (const key in attributes) {
    const val = attributes[key];
    const currentVal = currPos.currentAttributes.get(key) ?? null;
    if (!equalAttrs(currentVal, val)) {
      // save negated attribute (set null if currentVal undefined)
      negatedAttributes.set(key, currentVal);
      const { left, right } = currPos;
      currPos.right = new Item(createID(ownClientId, getState(doc.store, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, new ContentFormat(key, val));
      currPos.right.integrate(transaction, 0);
      currPos.forward();
    }
  }
  return negatedAttributes
};

/**
 * @param {Transaction} transaction
 * @param {AbstractType<any>} parent
 * @param {ItemTextListPosition} currPos
 * @param {string|object|AbstractType<any>} text
 * @param {Object<string,any>} attributes
 *
 * @private
 * @function
 **/
const insertText = (transaction, parent, currPos, text, attributes) => {
  currPos.currentAttributes.forEach((_val, key) => {
    if (attributes[key] === undefined) {
      attributes[key] = null;
    }
  });
  const doc = transaction.doc;
  const ownClientId = doc.clientID;
  minimizeAttributeChanges(currPos, attributes);
  const negatedAttributes = insertAttributes(transaction, parent, currPos, attributes);
  // insert content
  const content = text.constructor === String ? new ContentString(/** @type {string} */ (text)) : (text instanceof AbstractType ? new ContentType(text) : new ContentEmbed(text));
  let { left, right, index } = currPos;
  if (parent._searchMarker) {
    updateMarkerChanges(parent._searchMarker, currPos.index, content.getLength());
  }
  right = new Item(createID(ownClientId, getState(doc.store, ownClientId)), left, left && left.lastId, right, right && right.id, parent, null, content);
  right.integrate(transaction, 0);
  currPos.right = right;
  currPos.index = index;
  currPos.forward();
  insertNegatedAttributes(transaction, parent, currPos, negatedAttributes);
};

/**
 * @param {Transaction} transaction
 * @param {AbstractType<any>} parent
 * @param {ItemTextListPosition} currPos
 * @param {number} length
 * @param {Object<string,any>} attributes
 *
 * @private
 * @function
 */
const formatText = (transaction, parent, currPos, length, attributes) => {
  const doc = transaction.doc;
  const ownClientId = doc.clientID;
  minimizeAttributeChanges(currPos, attributes);
  const negatedAttributes = insertAttributes(transaction, parent, currPos, attributes);
  // iterate until first non-format or null is found
  // delete all formats with attributes[format.key] != null
  // also check the attributes after the first non-format as we do not want to insert redundant negated attributes there
  // eslint-disable-next-line no-labels
  iterationLoop: while (
    currPos.right !== null &&
    (length > 0 ||
      (
        negatedAttributes.size > 0 &&
        (currPos.right.deleted || currPos.right.content.constructor === ContentFormat)
      )
    )
  ) {
    if (!currPos.right.deleted) {
      switch (currPos.right.content.constructor) {
        case ContentFormat: {
          const { key, value } = /** @type {ContentFormat} */ (currPos.right.content);
          const attr = attributes[key];
          if (attr !== undefined) {
            if (equalAttrs(attr, value)) {
              negatedAttributes.delete(key);
            } else {
              if (length === 0) {
                // no need to further extend negatedAttributes
                // eslint-disable-next-line no-labels
                break iterationLoop
              }
              negatedAttributes.set(key, value);
            }
            currPos.right.delete(transaction);
          } else {
            currPos.currentAttributes.set(key, value);
          }
          break
        }
        default:
          if (length < currPos.right.length) {
            getItemCleanStart(transaction, createID(currPos.right.id.client, currPos.right.id.clock + length));
          }
          length -= currPos.right.length;
          break
      }
    }
    currPos.forward();
  }
  // Quill just assumes that the editor starts with a newline and that it always
  // ends with a newline. We only insert that newline when a new newline is
  // inserted - i.e when length is bigger than type.length
  if (length > 0) {
    let newlines = '';
    for (; length > 0; length--) {
      newlines += '\n';
    }
    currPos.right = new Item(createID(ownClientId, getState(doc.store, ownClientId)), currPos.left, currPos.left && currPos.left.lastId, currPos.right, currPos.right && currPos.right.id, parent, null, new ContentString(newlines));
    currPos.right.integrate(transaction, 0);
    currPos.forward();
  }
  insertNegatedAttributes(transaction, parent, currPos, negatedAttributes);
};

/**
 * Call this function after string content has been deleted in order to
 * clean up formatting Items.
 *
 * @param {Transaction} transaction
 * @param {Item} start
 * @param {Item|null} curr exclusive end, automatically iterates to the next Content Item
 * @param {Map<string,any>} startAttributes
 * @param {Map<string,any>} currAttributes
 * @return {number} The amount of formatting Items deleted.
 *
 * @function
 */
const cleanupFormattingGap = (transaction, start, curr, startAttributes, currAttributes) => {
  /**
   * @type {Item|null}
   */
  let end = start;
  /**
   * @type {Map<string,ContentFormat>}
   */
  const endFormats = create();
  while (end && (!end.countable || end.deleted)) {
    if (!end.deleted && end.content.constructor === ContentFormat) {
      const cf = /** @type {ContentFormat} */ (end.content);
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
          const { key, value } = /** @type {ContentFormat} */ (content);
          const startAttrValue = startAttributes.get(key) ?? null;
          if (endFormats.get(key) !== content || startAttrValue === value) {
            // Either this format is overwritten or it is not necessary because the attribute already existed.
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
            updateCurrentAttributes(currAttributes, /** @type {ContentFormat} */ (content));
          }
          break
        }
      }
    }
    start = /** @type {Item} */ (start.right);
  }
  return cleanups
};

/**
 * @param {Transaction} transaction
 * @param {Item | null} item
 */
const cleanupContextlessFormattingGap = (transaction, item) => {
  // iterate until item.right is null or content
  while (item && item.right && (item.right.deleted || !item.right.countable)) {
    item = item.right;
  }
  const attrs = new Set();
  // iterate back until a content item is found
  while (item && (item.deleted || !item.countable)) {
    if (!item.deleted && item.content.constructor === ContentFormat) {
      const key = /** @type {ContentFormat} */ (item.content).key;
      if (attrs.has(key)) {
        item.delete(transaction);
      } else {
        attrs.add(key);
      }
    }
    item = item.left;
  }
};

/**
 * This function is experimental and subject to change / be removed.
 *
 * Ideally, we don't need this function at all. Formatting attributes should be cleaned up
 * automatically after each change. This function iterates twice over the complete YText type
 * and removes unnecessary formatting attributes. This is also helpful for testing.
 *
 * This function won't be exported anymore as soon as there is confidence that the YText type works as intended.
 *
 * @param {YText} type
 * @return {number} How many formatting attributes have been cleaned up.
 */
const cleanupYTextFormatting = type => {
  let res = 0;
  transact(/** @type {Doc} */ (type.doc), transaction => {
    let start = /** @type {Item} */ (type._start);
    let end = type._start;
    let startAttributes = create();
    const currentAttributes = copy(startAttributes);
    while (end) {
      if (end.deleted === false) {
        switch (end.content.constructor) {
          case ContentFormat:
            updateCurrentAttributes(currentAttributes, /** @type {ContentFormat} */ (end.content));
            break
          default:
            res += cleanupFormattingGap(transaction, start, end, startAttributes, currentAttributes);
            startAttributes = copy(currentAttributes);
            start = end;
            break
        }
      }
      end = end.right;
    }
  });
  return res
};

/**
 * This will be called by the transaction once the event handlers are called to potentially cleanup
 * formatting attributes.
 *
 * @param {Transaction} transaction
 */
const cleanupYTextAfterTransaction = transaction => {
  /**
   * @type {Set<YText>}
   */
  const needFullCleanup = new Set();
  // check if another formatting item was inserted
  const doc = transaction.doc;
  for (const [client, afterClock] of transaction.afterState.entries()) {
    const clock = transaction.beforeState.get(client) || 0;
    if (afterClock === clock) {
      continue
    }
    iterateStructs(transaction, /** @type {Array<Item|GC>} */ (doc.store.clients.get(client)), clock, afterClock, item => {
      if (
        !item.deleted && /** @type {Item} */ (item).content.constructor === ContentFormat && item.constructor !== GC
      ) {
        needFullCleanup.add(/** @type {any} */ (item).parent);
      }
    });
  }
  // cleanup in a new transaction
  transact(doc, (t) => {
    iterateDeletedStructs(transaction, transaction.deleteSet, item => {
      if (item instanceof GC || !(/** @type {YText} */ (item.parent)._hasFormatting) || needFullCleanup.has(/** @type {YText} */ (item.parent))) {
        return
      }
      const parent = /** @type {YText} */ (item.parent);
      if (item.content.constructor === ContentFormat) {
        needFullCleanup.add(parent);
      } else {
        // If no formatting attribute was inserted or deleted, we can make due with contextless
        // formatting cleanups.
        // Contextless: it is not necessary to compute currentAttributes for the affected position.
        cleanupContextlessFormattingGap(t, item);
      }
    });
    // If a formatting item was inserted, we simply clean the whole type.
    // We need to compute currentAttributes for the current position anyway.
    for (const yText of needFullCleanup) {
      cleanupYTextFormatting(yText);
    }
  });
};

/**
 * @param {Transaction} transaction
 * @param {ItemTextListPosition} currPos
 * @param {number} length
 * @return {ItemTextListPosition}
 *
 * @private
 * @function
 */
const deleteText = (transaction, currPos, length) => {
  const startLength = length;
  const startAttrs = copy(currPos.currentAttributes);
  const start = currPos.right;
  while (length > 0 && currPos.right !== null) {
    if (currPos.right.deleted === false) {
      switch (currPos.right.content.constructor) {
        case ContentType:
        case ContentEmbed:
        case ContentString:
          if (length < currPos.right.length) {
            getItemCleanStart(transaction, createID(currPos.right.id.client, currPos.right.id.clock + length));
          }
          length -= currPos.right.length;
          currPos.right.delete(transaction);
          break
      }
    }
    currPos.forward();
  }
  if (start) {
    cleanupFormattingGap(transaction, start, currPos.right, startAttrs, currPos.currentAttributes);
  }
  const parent = /** @type {AbstractType<any>} */ (/** @type {Item} */ (currPos.left || currPos.right).parent);
  if (parent._searchMarker) {
    updateMarkerChanges(parent._searchMarker, currPos.index, -startLength + length);
  }
  return currPos
};

/**
 * The Quill Delta format represents changes on a text document with
 * formatting information. For more information visit {@link https://quilljs.com/docs/delta/|Quill Delta}
 *
 * @example
 *   {
 *     ops: [
 *       { insert: 'Gandalf', attributes: { bold: true } },
 *       { insert: ' the ' },
 *       { insert: 'Grey', attributes: { color: '#cccccc' } }
 *     ]
 *   }
 *
 */

/**
  * Attributes that can be assigned to a selection of text.
  *
  * @example
  *   {
  *     bold: true,
  *     font-size: '40px'
  *   }
  *
  * @typedef {Object} TextAttributes
  */

/**
 * @extends YEvent<YText>
 * Event that describes the changes on a YText type.
 */
class YTextEvent extends YEvent {
  /**
   * @param {YText} ytext
   * @param {Transaction} transaction
   * @param {Set<any>} subs The keys that changed
   */
  constructor (ytext, transaction, subs) {
    super(ytext, transaction);
    /**
     * Whether the children changed.
     * @type {Boolean}
     * @private
     */
    this.childListChanged = false;
    /**
     * Set of all changed attributes.
     * @type {Set<string>}
     */
    this.keysChanged = new Set();
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
  get changes () {
    if (this._changes === null) {
      /**
       * @type {{added:Set<Item>,deleted:Set<Item>,keys:Map<string,{action:'add'|'update'|'delete',oldValue:any}>,delta:Array<{insert?:Array<any>|string|AbstractType<any>|object, delete?:number, retain?:number}>}}
       */
      const changes = {
        keys: this.keys,
        delta: this.delta,
        added: new Set(),
        deleted: new Set()
      };
      this._changes = changes;
    }
    return /** @type {any} */ (this._changes)
  }

  /**
   * Compute the changes in the delta format.
   * A {@link https://quilljs.com/docs/delta/|Quill Delta}) that represents the changes on the document.
   *
   * @type {Array<{insert?:string|object|AbstractType<any>, delete?:number, retain?:number, attributes?: Object<string,any>}>}
   *
   * @public
   */
  get delta () {
    if (this._delta === null) {
      const y = /** @type {Doc} */ (this.target.doc);
      /**
       * @type {Array<{insert?:string|object|AbstractType<any>, delete?:number, retain?:number, attributes?: Object<string,any>}>}
       */
      const delta = [];
      transact(y, transaction => {
        const currentAttributes = new Map(); // saves all current attributes for insert
        const oldAttributes = new Map();
        let item = this.target._start;
        /**
         * @type {string?}
         */
        let action = null;
        /**
         * @type {Object<string,any>}
         */
        const attributes = {}; // counts added or removed new attributes for retain
        /**
         * @type {string|object}
         */
        let insert = '';
        let retain = 0;
        let deleteLen = 0;
        const addOp = () => {
          if (action !== null) {
            /**
             * @type {any}
             */
            let op = null;
            switch (action) {
              case 'delete':
                if (deleteLen > 0) {
                  op = { delete: deleteLen };
                }
                deleteLen = 0;
                break
              case 'insert':
                if (typeof insert === 'object' || insert.length > 0) {
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
                insert = '';
                break
              case 'retain':
                if (retain > 0) {
                  op = { retain };
                  if (!isEmpty(attributes)) {
                    op.attributes = object_assign({}, attributes);
                  }
                }
                retain = 0;
                break
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
                  action = 'insert';
                  insert = item.content.getContent()[0];
                  addOp();
                }
              } else if (this.deletes(item)) {
                if (action !== 'delete') {
                  addOp();
                  action = 'delete';
                }
                deleteLen += 1;
              } else if (!item.deleted) {
                if (action !== 'retain') {
                  addOp();
                  action = 'retain';
                }
                retain += 1;
              }
              break
            case ContentString:
              if (this.adds(item)) {
                if (!this.deletes(item)) {
                  if (action !== 'insert') {
                    addOp();
                    action = 'insert';
                  }
                  insert += /** @type {ContentString} */ (item.content).str;
                }
              } else if (this.deletes(item)) {
                if (action !== 'delete') {
                  addOp();
                  action = 'delete';
                }
                deleteLen += item.length;
              } else if (!item.deleted) {
                if (action !== 'retain') {
                  addOp();
                  action = 'retain';
                }
                retain += item.length;
              }
              break
            case ContentFormat: {
              const { key, value } = /** @type {ContentFormat} */ (item.content);
              if (this.adds(item)) {
                if (!this.deletes(item)) {
                  const curVal = currentAttributes.get(key) ?? null;
                  if (!equalAttrs(curVal, value)) {
                    if (action === 'retain') {
                      addOp();
                    }
                    if (equalAttrs(value, (oldAttributes.get(key) ?? null))) {
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
                  if (action === 'retain') {
                    addOp();
                  }
                  attributes[key] = curVal;
                }
              } else if (!item.deleted) {
                oldAttributes.set(key, value);
                const attr = attributes[key];
                if (attr !== undefined) {
                  if (!equalAttrs(attr, value)) {
                    if (action === 'retain') {
                      addOp();
                    }
                    if (value === null) {
                      delete attributes[key];
                    } else {
                      attributes[key] = value;
                    }
                  } else if (attr !== null) { // this will be cleaned up automatically by the contextless cleanup function
                    item.delete(transaction);
                  }
                }
              }
              if (!item.deleted) {
                if (action === 'insert') {
                  addOp();
                }
                updateCurrentAttributes(currentAttributes, /** @type {ContentFormat} */ (item.content));
              }
              break
            }
          }
          item = item.right;
        }
        addOp();
        while (delta.length > 0) {
          const lastOp = delta[delta.length - 1];
          if (lastOp.retain !== undefined && lastOp.attributes === undefined) {
            // retain delta's if they don't assign attributes
            delta.pop();
          } else {
            break
          }
        }
      });
      this._delta = delta;
    }
    return /** @type {any} */ (this._delta)
  }
}

/**
 * Type that represents text with formatting information.
 *
 * This type replaces y-richtext as this implementation is able to handle
 * block formats (format information on a paragraph), embeds (complex elements
 * like pictures and videos), and text formats (**bold**, *italic*).
 *
 * @extends AbstractType<YTextEvent>
 */
class YText extends AbstractType {
  /**
   * @param {String} [string] The initial value of the YText.
   */
  constructor (string) {
    super();
    /**
     * Array of pending operations on this type
     * @type {Array<function():void>?}
     */
    this._pending = string !== undefined ? [() => this.insert(0, string)] : [];
    /**
     * @type {Array<ArraySearchMarker>|null}
     */
    this._searchMarker = [];
    /**
     * Whether this YText contains formatting attributes.
     * This flag is updated when a formatting item is integrated (see ContentFormat.integrate)
     */
    this._hasFormatting = false;
  }

  /**
   * Number of characters of this text type.
   *
   * @type {number}
   */
  get length () {
    this.doc ?? warnPrematureAccess();
    return this._length
  }

  /**
   * @param {Doc} y
   * @param {Item} item
   */
  _integrate (y, item) {
    super._integrate(y, item);
    try {
      /** @type {Array<function>} */ (this._pending).forEach(f => f());
    } catch (e) {
      console.error(e);
    }
    this._pending = null;
  }

  _copy () {
    return new YText()
  }

  /**
   * Makes a copy of this data type that can be included somewhere else.
   *
   * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
   *
   * @return {YText}
   */
  clone () {
    const text = new YText();
    text.applyDelta(this.toDelta());
    return text
  }

  /**
   * Creates YTextEvent and calls observers.
   *
   * @param {Transaction} transaction
   * @param {Set<null|string>} parentSubs Keys changed on this type. `null` if list was modified.
   */
  _callObserver (transaction, parentSubs) {
    super._callObserver(transaction, parentSubs);
    const event = new YTextEvent(this, transaction, parentSubs);
    callTypeObservers(this, transaction, event);
    // If a remote change happened, we try to cleanup potential formatting duplicates.
    if (!transaction.local && this._hasFormatting) {
      transaction._needFormattingCleanup = true;
    }
  }

  /**
   * Returns the unformatted string representation of this YText type.
   *
   * @public
   */
  toString () {
    this.doc ?? warnPrematureAccess();
    let str = '';
    /**
     * @type {Item|null}
     */
    let n = this._start;
    while (n !== null) {
      if (!n.deleted && n.countable && n.content.constructor === ContentString) {
        str += /** @type {ContentString} */ (n.content).str;
      }
      n = n.right;
    }
    return str
  }

  /**
   * Returns the unformatted string representation of this YText type.
   *
   * @return {string}
   * @public
   */
  toJSON () {
    return this.toString()
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
  applyDelta (delta, { sanitize = true } = {}) {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        const currPos = new ItemTextListPosition(null, this._start, 0, new Map());
        for (let i = 0; i < delta.length; i++) {
          const op = delta[i];
          if (op.insert !== undefined) {
            // Quill assumes that the content starts with an empty paragraph.
            // Yjs/Y.Text assumes that it starts empty. We always hide that
            // there is a newline at the end of the content.
            // If we omit this step, clients will see a different number of
            // paragraphs, but nothing bad will happen.
            const ins = (!sanitize && typeof op.insert === 'string' && i === delta.length - 1 && currPos.right === null && op.insert.slice(-1) === '\n') ? op.insert.slice(0, -1) : op.insert;
            if (typeof ins !== 'string' || ins.length > 0) {
              insertText(transaction, this, currPos, ins, op.attributes || {});
            }
          } else if (op.retain !== undefined) {
            formatText(transaction, this, currPos, op.retain, op.attributes || {});
          } else if (op.delete !== undefined) {
            deleteText(transaction, currPos, op.delete);
          }
        }
      });
    } else {
      /** @type {Array<function>} */ (this._pending).push(() => this.applyDelta(delta));
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
  toDelta (snapshot, prevSnapshot, computeYChange) {
    this.doc ?? warnPrematureAccess();
    /**
     * @type{Array<any>}
     */
    const ops = [];
    const currentAttributes = new Map();
    const doc = /** @type {Doc} */ (this.doc);
    let str = '';
    let n = this._start;
    function packStr () {
      if (str.length > 0) {
        // pack str with attributes to ops
        /**
         * @type {Object<string,any>}
         */
        const attributes = {};
        let addAttributes = false;
        currentAttributes.forEach((value, key) => {
          addAttributes = true;
          attributes[key] = value;
        });
        /**
         * @type {Object<string,any>}
         */
        const op = { insert: str };
        if (addAttributes) {
          op.attributes = attributes;
        }
        ops.push(op);
        str = '';
      }
    }
    const computeDelta = () => {
      while (n !== null) {
        if (isVisible(n, snapshot) || (prevSnapshot !== undefined && isVisible(n, prevSnapshot))) {
          switch (n.content.constructor) {
            case ContentString: {
              const cur = currentAttributes.get('ychange');
              if (snapshot !== undefined && !isVisible(n, snapshot)) {
                if (cur === undefined || cur.user !== n.id.client || cur.type !== 'removed') {
                  packStr();
                  currentAttributes.set('ychange', computeYChange ? computeYChange('removed', n.id) : { type: 'removed' });
                }
              } else if (prevSnapshot !== undefined && !isVisible(n, prevSnapshot)) {
                if (cur === undefined || cur.user !== n.id.client || cur.type !== 'added') {
                  packStr();
                  currentAttributes.set('ychange', computeYChange ? computeYChange('added', n.id) : { type: 'added' });
                }
              } else if (cur !== undefined) {
                packStr();
                currentAttributes.delete('ychange');
              }
              str += /** @type {ContentString} */ (n.content).str;
              break
            }
            case ContentType:
            case ContentEmbed: {
              packStr();
              /**
               * @type {Object<string,any>}
               */
              const op = {
                insert: n.content.getContent()[0]
              };
              if (currentAttributes.size > 0) {
                const attrs = /** @type {Object<string,any>} */ ({});
                op.attributes = attrs;
                currentAttributes.forEach((value, key) => {
                  attrs[key] = value;
                });
              }
              ops.push(op);
              break
            }
            case ContentFormat:
              if (isVisible(n, snapshot)) {
                packStr();
                updateCurrentAttributes(currentAttributes, /** @type {ContentFormat} */ (n.content));
              }
              break
          }
        }
        n = n.right;
      }
      packStr();
    };
    if (snapshot || prevSnapshot) {
      // snapshots are merged again after the transaction, so we need to keep the
      // transaction alive until we are done
      transact(doc, transaction => {
        if (snapshot) {
          splitSnapshotAffectedStructs(transaction, snapshot);
        }
        if (prevSnapshot) {
          splitSnapshotAffectedStructs(transaction, prevSnapshot);
        }
        computeDelta();
      }, 'cleanup');
    } else {
      computeDelta();
    }
    return ops
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
  insert (index, text, attributes) {
    if (text.length <= 0) {
      return
    }
    const y = this.doc;
    if (y !== null) {
      transact(y, transaction => {
        const pos = findPosition(transaction, this, index, !attributes);
        if (!attributes) {
          attributes = {};
          // @ts-ignore
          pos.currentAttributes.forEach((v, k) => { attributes[k] = v; });
        }
        insertText(transaction, this, pos, text, attributes);
      });
    } else {
      /** @type {Array<function>} */ (this._pending).push(() => this.insert(index, text, attributes));
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
  insertEmbed (index, embed, attributes) {
    const y = this.doc;
    if (y !== null) {
      transact(y, transaction => {
        const pos = findPosition(transaction, this, index, !attributes);
        insertText(transaction, this, pos, embed, attributes || {});
      });
    } else {
      /** @type {Array<function>} */ (this._pending).push(() => this.insertEmbed(index, embed, attributes || {}));
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
  delete (index, length) {
    if (length === 0) {
      return
    }
    const y = this.doc;
    if (y !== null) {
      transact(y, transaction => {
        deleteText(transaction, findPosition(transaction, this, index, true), length);
      });
    } else {
      /** @type {Array<function>} */ (this._pending).push(() => this.delete(index, length));
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
  format (index, length, attributes) {
    if (length === 0) {
      return
    }
    const y = this.doc;
    if (y !== null) {
      transact(y, transaction => {
        const pos = findPosition(transaction, this, index, false);
        if (pos.right === null) {
          return
        }
        formatText(transaction, this, pos, length, attributes);
      });
    } else {
      /** @type {Array<function>} */ (this._pending).push(() => this.format(index, length, attributes));
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
  removeAttribute (attributeName) {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        typeMapDelete(transaction, this, attributeName);
      });
    } else {
      /** @type {Array<function>} */ (this._pending).push(() => this.removeAttribute(attributeName));
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
  setAttribute (attributeName, attributeValue) {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        typeMapSet(transaction, this, attributeName, attributeValue);
      });
    } else {
      /** @type {Array<function>} */ (this._pending).push(() => this.setAttribute(attributeName, attributeValue));
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
  getAttribute (attributeName) {
    return /** @type {any} */ (typeMapGet(this, attributeName))
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
  getAttributes () {
    return typeMapGetAll(this)
  }

  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   */
  _write (encoder) {
    encoder.writeTypeRef(YTextRefID);
  }
}

/**
 * @param {UpdateDecoderV1 | UpdateDecoderV2} _decoder
 * @return {YText}
 *
 * @private
 * @function
 */
const readYText = _decoder => new YText();

/**
 * @module YXml
 */


/**
 * Define the elements to which a set of CSS queries apply.
 * {@link https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_Selectors|CSS_Selectors}
 *
 * @example
 *   query = '.classSelector'
 *   query = 'nodeSelector'
 *   query = '#idSelector'
 *
 * @typedef {string} CSS_Selector
 */

/**
 * Dom filter function.
 *
 * @callback domFilter
 * @param {string} nodeName The nodeName of the element
 * @param {Map} attributes The map of attributes.
 * @return {boolean} Whether to include the Dom node in the YXmlElement.
 */

/**
 * Represents a subset of the nodes of a YXmlElement / YXmlFragment and a
 * position within them.
 *
 * Can be created with {@link YXmlFragment#createTreeWalker}
 *
 * @public
 * @implements {Iterable<YXmlElement|YXmlText|YXmlElement|YXmlHook>}
 */
class YXmlTreeWalker {
  /**
   * @param {YXmlFragment | YXmlElement} root
   * @param {function(AbstractType<any>):boolean} [f]
   */
  constructor (root, f = () => true) {
    this._filter = f;
    this._root = root;
    /**
     * @type {Item}
     */
    this._currentNode = /** @type {Item} */ (root._start);
    this._firstCall = true;
    root.doc ?? warnPrematureAccess();
  }

  [Symbol.iterator] () {
    return this
  }

  /**
   * Get the next node.
   *
   * @return {IteratorResult<YXmlElement|YXmlText|YXmlHook>} The next node.
   *
   * @public
   */
  next () {
    /**
     * @type {Item|null}
     */
    let n = this._currentNode;
    let type = n && n.content && /** @type {any} */ (n.content).type;
    if (n !== null && (!this._firstCall || n.deleted || !this._filter(type))) { // if first call, we check if we can use the first item
      do {
        type = /** @type {any} */ (n.content).type;
        if (!n.deleted && (type.constructor === YXmlElement || type.constructor === YXmlFragment) && type._start !== null) {
          // walk down in the tree
          n = type._start;
        } else {
          // walk right or up in the tree
          while (n !== null) {
            /**
             * @type {Item | null}
             */
            const nxt = n.next;
            if (nxt !== null) {
              n = nxt;
              break
            } else if (n.parent === this._root) {
              n = null;
            } else {
              n = /** @type {AbstractType<any>} */ (n.parent)._item;
            }
          }
        }
      } while (n !== null && (n.deleted || !this._filter(/** @type {ContentType} */ (n.content).type)))
    }
    this._firstCall = false;
    if (n === null) {
      // @ts-ignore
      return { value: undefined, done: true }
    }
    this._currentNode = n;
    return { value: /** @type {any} */ (n.content).type, done: false }
  }
}

/**
 * Represents a list of {@link YXmlElement}.and {@link YXmlText} types.
 * A YxmlFragment is similar to a {@link YXmlElement}, but it does not have a
 * nodeName and it does not have attributes. Though it can be bound to a DOM
 * element - in this case the attributes and the nodeName are not shared.
 *
 * @public
 * @extends AbstractType<YXmlEvent>
 */
class YXmlFragment extends AbstractType {
  constructor () {
    super();
    /**
     * @type {Array<any>|null}
     */
    this._prelimContent = [];
  }

  /**
   * @type {YXmlElement|YXmlText|null}
   */
  get firstChild () {
    const first = this._first;
    return first ? first.content.getContent()[0] : null
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
  _integrate (y, item) {
    super._integrate(y, item);
    this.insert(0, /** @type {Array<any>} */ (this._prelimContent));
    this._prelimContent = null;
  }

  _copy () {
    return new YXmlFragment()
  }

  /**
   * Makes a copy of this data type that can be included somewhere else.
   *
   * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
   *
   * @return {YXmlFragment}
   */
  clone () {
    const el = new YXmlFragment();
    // @ts-ignore
    el.insert(0, this.toArray().map(item => item instanceof AbstractType ? item.clone() : item));
    return el
  }

  get length () {
    this.doc ?? warnPrematureAccess();
    return this._prelimContent === null ? this._length : this._prelimContent.length
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
  createTreeWalker (filter) {
    return new YXmlTreeWalker(this, filter)
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
  querySelector (query) {
    query = query.toUpperCase();
    // @ts-ignore
    const iterator = new YXmlTreeWalker(this, element => element.nodeName && element.nodeName.toUpperCase() === query);
    const next = iterator.next();
    if (next.done) {
      return null
    } else {
      return next.value
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
  querySelectorAll (query) {
    query = query.toUpperCase();
    // @ts-ignore
    return array_from(new YXmlTreeWalker(this, element => element.nodeName && element.nodeName.toUpperCase() === query))
  }

  /**
   * Creates YXmlEvent and calls observers.
   *
   * @param {Transaction} transaction
   * @param {Set<null|string>} parentSubs Keys changed on this type. `null` if list was modified.
   */
  _callObserver (transaction, parentSubs) {
    callTypeObservers(this, transaction, new YXmlEvent(this, parentSubs, transaction));
  }

  /**
   * Get the string representation of all the children of this YXmlFragment.
   *
   * @return {string} The string representation of all children.
   */
  toString () {
    return typeListMap(this, xml => xml.toString()).join('')
  }

  /**
   * @return {string}
   */
  toJSON () {
    return this.toString()
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
  toDOM (_document = document, hooks = {}, binding) {
    const fragment = _document.createDocumentFragment();
    if (binding !== undefined) {
      binding._createAssociation(fragment, this);
    }
    typeListForEach(this, xmlType => {
      fragment.insertBefore(xmlType.toDOM(_document, hooks, binding), null);
    });
    return fragment
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
  insert (index, content) {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        typeListInsertGenerics(transaction, this, index, content);
      });
    } else {
      // @ts-ignore _prelimContent is defined because this is not yet integrated
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
  insertAfter (ref, content) {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        const refItem = (ref && ref instanceof AbstractType) ? ref._item : ref;
        typeListInsertGenericsAfter(transaction, this, refItem, content);
      });
    } else {
      const pc = /** @type {Array<any>} */ (this._prelimContent);
      const index = ref === null ? 0 : pc.findIndex(el => el === ref) + 1;
      if (index === 0 && ref !== null) {
        throw error_create('Reference item not found')
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
  delete (index, length = 1) {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        typeListDelete(transaction, this, index, length);
      });
    } else {
      // @ts-ignore _prelimContent is defined because this is not yet integrated
      this._prelimContent.splice(index, length);
    }
  }

  /**
   * Transforms this YArray to a JavaScript Array.
   *
   * @return {Array<YXmlElement|YXmlText|YXmlHook>}
   */
  toArray () {
    return typeListToArray(this)
  }

  /**
   * Appends content to this YArray.
   *
   * @param {Array<YXmlElement|YXmlText>} content Array of content to append.
   */
  push (content) {
    this.insert(this.length, content);
  }

  /**
   * Prepends content to this YArray.
   *
   * @param {Array<YXmlElement|YXmlText>} content Array of content to prepend.
   */
  unshift (content) {
    this.insert(0, content);
  }

  /**
   * Returns the i-th element from a YArray.
   *
   * @param {number} index The index of the element to return from the YArray
   * @return {YXmlElement|YXmlText}
   */
  get (index) {
    return typeListGet(this, index)
  }

  /**
   * Returns a portion of this YXmlFragment into a JavaScript Array selected
   * from start to end (end not included).
   *
   * @param {number} [start]
   * @param {number} [end]
   * @return {Array<YXmlElement|YXmlText>}
   */
  slice (start = 0, end = this.length) {
    return typeListSlice(this, start, end)
  }

  /**
   * Executes a provided function on once on every child element.
   *
   * @param {function(YXmlElement|YXmlText,number, typeof self):void} f A function to execute on every element of this YArray.
   */
  forEach (f) {
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
  _write (encoder) {
    encoder.writeTypeRef(YXmlFragmentRefID);
  }
}

/**
 * @param {UpdateDecoderV1 | UpdateDecoderV2} _decoder
 * @return {YXmlFragment}
 *
 * @private
 * @function
 */
const readYXmlFragment = _decoder => new YXmlFragment();

/**
 * @typedef {Object|number|null|Array<any>|string|Uint8Array|AbstractType<any>} ValueTypes
 */

/**
 * An YXmlElement imitates the behavior of a
 * https://developer.mozilla.org/en-US/docs/Web/API/Element|Dom Element
 *
 * * An YXmlElement has attributes (key value pairs)
 * * An YXmlElement has childElements that must inherit from YXmlElement
 *
 * @template {{ [key: string]: ValueTypes }} [KV={ [key: string]: string }]
 */
class YXmlElement extends YXmlFragment {
  constructor (nodeName = 'UNDEFINED') {
    super();
    this.nodeName = nodeName;
    /**
     * @type {Map<string, any>|null}
     */
    this._prelimAttrs = new Map();
  }

  /**
   * @type {YXmlElement|YXmlText|null}
   */
  get nextSibling () {
    const n = this._item ? this._item.next : null;
    return n ? /** @type {YXmlElement|YXmlText} */ (/** @type {ContentType} */ (n.content).type) : null
  }

  /**
   * @type {YXmlElement|YXmlText|null}
   */
  get prevSibling () {
    const n = this._item ? this._item.prev : null;
    return n ? /** @type {YXmlElement|YXmlText} */ (/** @type {ContentType} */ (n.content).type) : null
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
  _integrate (y, item) {
    super._integrate(y, item)
    ;(/** @type {Map<string, any>} */ (this._prelimAttrs)).forEach((value, key) => {
      this.setAttribute(key, value);
    });
    this._prelimAttrs = null;
  }

  /**
   * Creates an Item with the same effect as this Item (without position effect)
   *
   * @return {YXmlElement}
   */
  _copy () {
    return new YXmlElement(this.nodeName)
  }

  /**
   * Makes a copy of this data type that can be included somewhere else.
   *
   * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
   *
   * @return {YXmlElement<KV>}
   */
  clone () {
    /**
     * @type {YXmlElement<KV>}
     */
    const el = new YXmlElement(this.nodeName);
    const attrs = this.getAttributes();
    object_forEach(attrs, (value, key) => {
      if (typeof value === 'string') {
        el.setAttribute(key, value);
      }
    });
    // @ts-ignore
    el.insert(0, this.toArray().map(item => item instanceof AbstractType ? item.clone() : item));
    return el
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
  toString () {
    const attrs = this.getAttributes();
    const stringBuilder = [];
    const keys = [];
    for (const key in attrs) {
      keys.push(key);
    }
    keys.sort();
    const keysLen = keys.length;
    for (let i = 0; i < keysLen; i++) {
      const key = keys[i];
      stringBuilder.push(key + '="' + attrs[key] + '"');
    }
    const nodeName = this.nodeName.toLocaleLowerCase();
    const attrsString = stringBuilder.length > 0 ? ' ' + stringBuilder.join(' ') : '';
    return `<${nodeName}${attrsString}>${super.toString()}</${nodeName}>`
  }

  /**
   * Removes an attribute from this YXmlElement.
   *
   * @param {string} attributeName The attribute name that is to be removed.
   *
   * @public
   */
  removeAttribute (attributeName) {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        typeMapDelete(transaction, this, attributeName);
      });
    } else {
      /** @type {Map<string,any>} */ (this._prelimAttrs).delete(attributeName);
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
  setAttribute (attributeName, attributeValue) {
    if (this.doc !== null) {
      transact(this.doc, transaction => {
        typeMapSet(transaction, this, attributeName, attributeValue);
      });
    } else {
      /** @type {Map<string, any>} */ (this._prelimAttrs).set(attributeName, attributeValue);
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
  getAttribute (attributeName) {
    return /** @type {any} */ (typeMapGet(this, attributeName))
  }

  /**
   * Returns whether an attribute exists
   *
   * @param {string} attributeName The attribute name to check for existence.
   * @return {boolean} whether the attribute exists.
   *
   * @public
   */
  hasAttribute (attributeName) {
    return /** @type {any} */ (typeMapHas(this, attributeName))
  }

  /**
   * Returns all attribute name/value pairs in a JSON Object.
   *
   * @param {Snapshot} [snapshot]
   * @return {{ [Key in Extract<keyof KV,string>]?: KV[Key]}} A JSON Object that describes the attributes.
   *
   * @public
   */
  getAttributes (snapshot) {
    return /** @type {any} */ (snapshot ? typeMapGetAllSnapshot(this, snapshot) : typeMapGetAll(this))
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
  toDOM (_document = document, hooks = {}, binding) {
    const dom = _document.createElement(this.nodeName);
    const attrs = this.getAttributes();
    for (const key in attrs) {
      const value = attrs[key];
      if (typeof value === 'string') {
        dom.setAttribute(key, value);
      }
    }
    typeListForEach(this, yxml => {
      dom.appendChild(yxml.toDOM(_document, hooks, binding));
    });
    if (binding !== undefined) {
      binding._createAssociation(dom, this);
    }
    return dom
  }

  /**
   * Transform the properties of this type to binary and write it to an
   * BinaryEncoder.
   *
   * This is called when this Item is sent to a remote peer.
   *
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder The encoder to write data to.
   */
  _write (encoder) {
    encoder.writeTypeRef(YXmlElementRefID);
    encoder.writeKey(this.nodeName);
  }
}

/**
 * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
 * @return {YXmlElement}
 *
 * @function
 */
const readYXmlElement = decoder => new YXmlElement(decoder.readKey());

/**
 * @extends YEvent<YXmlElement|YXmlText|YXmlFragment>
 * An Event that describes changes on a YXml Element or Yxml Fragment
 */
class YXmlEvent extends YEvent {
  /**
   * @param {YXmlElement|YXmlText|YXmlFragment} target The target on which the event is created.
   * @param {Set<string|null>} subs The set of changed attributes. `null` is included if the
   *                   child list changed.
   * @param {Transaction} transaction The transaction instance with which the
   *                                  change was created.
   */
  constructor (target, subs, transaction) {
    super(target, transaction);
    /**
     * Whether the children changed.
     * @type {Boolean}
     * @private
     */
    this.childListChanged = false;
    /**
     * Set of all changed attributes.
     * @type {Set<string>}
     */
    this.attributesChanged = new Set();
    subs.forEach((sub) => {
      if (sub === null) {
        this.childListChanged = true;
      } else {
        this.attributesChanged.add(sub);
      }
    });
  }
}

/**
 * You can manage binding to a custom type with YXmlHook.
 *
 * @extends {YMap<any>}
 */
class YXmlHook extends YMap {
  /**
   * @param {string} hookName nodeName of the Dom Node.
   */
  constructor (hookName) {
    super();
    /**
     * @type {string}
     */
    this.hookName = hookName;
  }

  /**
   * Creates an Item with the same effect as this Item (without position effect)
   */
  _copy () {
    return new YXmlHook(this.hookName)
  }

  /**
   * Makes a copy of this data type that can be included somewhere else.
   *
   * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
   *
   * @return {YXmlHook}
   */
  clone () {
    const el = new YXmlHook(this.hookName);
    this.forEach((value, key) => {
      el.set(key, value);
    });
    return el
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
  toDOM (_document = document, hooks = {}, binding) {
    const hook = hooks[this.hookName];
    let dom;
    if (hook !== undefined) {
      dom = hook.createDom(this);
    } else {
      dom = document.createElement(this.hookName);
    }
    dom.setAttribute('data-yjs-hook', this.hookName);
    if (binding !== undefined) {
      binding._createAssociation(dom, this);
    }
    return dom
  }

  /**
   * Transform the properties of this type to binary and write it to an
   * BinaryEncoder.
   *
   * This is called when this Item is sent to a remote peer.
   *
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder The encoder to write data to.
   */
  _write (encoder) {
    encoder.writeTypeRef(YXmlHookRefID);
    encoder.writeKey(this.hookName);
  }
}

/**
 * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
 * @return {YXmlHook}
 *
 * @private
 * @function
 */
const readYXmlHook = decoder =>
  new YXmlHook(decoder.readKey());

/**
 * Represents text in a Dom Element. In the future this type will also handle
 * simple formatting information like bold and italic.
 */
class YXmlText extends YText {
  /**
   * @type {YXmlElement|YXmlText|null}
   */
  get nextSibling () {
    const n = this._item ? this._item.next : null;
    return n ? /** @type {YXmlElement|YXmlText} */ (/** @type {ContentType} */ (n.content).type) : null
  }

  /**
   * @type {YXmlElement|YXmlText|null}
   */
  get prevSibling () {
    const n = this._item ? this._item.prev : null;
    return n ? /** @type {YXmlElement|YXmlText} */ (/** @type {ContentType} */ (n.content).type) : null
  }

  _copy () {
    return new YXmlText()
  }

  /**
   * Makes a copy of this data type that can be included somewhere else.
   *
   * Note that the content is only readable _after_ it has been included somewhere in the Ydoc.
   *
   * @return {YXmlText}
   */
  clone () {
    const text = new YXmlText();
    text.applyDelta(this.toDelta());
    return text
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
  toDOM (_document = document, hooks, binding) {
    const dom = _document.createTextNode(this.toString());
    if (binding !== undefined) {
      binding._createAssociation(dom, this);
    }
    return dom
  }

  toString () {
    // @ts-ignore
    return this.toDelta().map(delta => {
      const nestedNodes = [];
      for (const nodeName in delta.attributes) {
        const attrs = [];
        for (const key in delta.attributes[nodeName]) {
          attrs.push({ key, value: delta.attributes[nodeName][key] });
        }
        // sort attributes to get a unique order
        attrs.sort((a, b) => a.key < b.key ? -1 : 1);
        nestedNodes.push({ nodeName, attrs });
      }
      // sort node order to get a unique order
      nestedNodes.sort((a, b) => a.nodeName < b.nodeName ? -1 : 1);
      // now convert to dom string
      let str = '';
      for (let i = 0; i < nestedNodes.length; i++) {
        const node = nestedNodes[i];
        str += `<${node.nodeName}`;
        for (let j = 0; j < node.attrs.length; j++) {
          const attr = node.attrs[j];
          str += ` ${attr.key}="${attr.value}"`;
        }
        str += '>';
      }
      str += delta.insert;
      for (let i = nestedNodes.length - 1; i >= 0; i--) {
        str += `</${nestedNodes[i].nodeName}>`;
      }
      return str
    }).join('')
  }

  /**
   * @return {string}
   */
  toJSON () {
    return this.toString()
  }

  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   */
  _write (encoder) {
    encoder.writeTypeRef(YXmlTextRefID);
  }
}

/**
 * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
 * @return {YXmlText}
 *
 * @private
 * @function
 */
const readYXmlText = decoder => new YXmlText();

class AbstractStruct {
  /**
   * @param {ID} id
   * @param {number} length
   */
  constructor (id, length) {
    this.id = id;
    this.length = length;
  }

  /**
   * @type {boolean}
   */
  get deleted () {
    throw methodUnimplemented()
  }

  /**
   * Merge this struct with the item to the right.
   * This method is already assuming that `this.id.clock + this.length === this.id.clock`.
   * Also this method does *not* remove right from StructStore!
   * @param {AbstractStruct} right
   * @return {boolean} whether this merged with right
   */
  mergeWith (right) {
    return false
  }

  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder The encoder to write data to.
   * @param {number} offset
   * @param {number} encodingRef
   */
  write (encoder, offset, encodingRef) {
    throw methodUnimplemented()
  }

  /**
   * @param {Transaction} transaction
   * @param {number} offset
   */
  integrate (transaction, offset) {
    throw methodUnimplemented()
  }
}

const structGCRefNumber = 0;

/**
 * @private
 */
class GC extends AbstractStruct {
  get deleted () {
    return true
  }

  delete () {}

  /**
   * @param {GC} right
   * @return {boolean}
   */
  mergeWith (right) {
    if (this.constructor !== right.constructor) {
      return false
    }
    this.length += right.length;
    return true
  }

  /**
   * @param {Transaction} transaction
   * @param {number} offset
   */
  integrate (transaction, offset) {
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
  write (encoder, offset) {
    encoder.writeInfo(structGCRefNumber);
    encoder.writeLen(this.length - offset);
  }

  /**
   * @param {Transaction} transaction
   * @param {StructStore} store
   * @return {null | number}
   */
  getMissing (transaction, store) {
    return null
  }
}

class ContentBinary {
  /**
   * @param {Uint8Array} content
   */
  constructor (content) {
    this.content = content;
  }

  /**
   * @return {number}
   */
  getLength () {
    return 1
  }

  /**
   * @return {Array<any>}
   */
  getContent () {
    return [this.content]
  }

  /**
   * @return {boolean}
   */
  isCountable () {
    return true
  }

  /**
   * @return {ContentBinary}
   */
  copy () {
    return new ContentBinary(this.content)
  }

  /**
   * @param {number} offset
   * @return {ContentBinary}
   */
  splice (offset) {
    throw methodUnimplemented()
  }

  /**
   * @param {ContentBinary} right
   * @return {boolean}
   */
  mergeWith (right) {
    return false
  }

  /**
   * @param {Transaction} transaction
   * @param {Item} item
   */
  integrate (transaction, item) {}
  /**
   * @param {Transaction} transaction
   */
  delete (transaction) {}
  /**
   * @param {StructStore} store
   */
  gc (store) {}
  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   * @param {number} offset
   */
  write (encoder, offset) {
    encoder.writeBuf(this.content);
  }

  /**
   * @return {number}
   */
  getRef () {
    return 3
  }
}

/**
 * @param {UpdateDecoderV1 | UpdateDecoderV2 } decoder
 * @return {ContentBinary}
 */
const readContentBinary = decoder => new ContentBinary(decoder.readBuf());

class ContentDeleted {
  /**
   * @param {number} len
   */
  constructor (len) {
    this.len = len;
  }

  /**
   * @return {number}
   */
  getLength () {
    return this.len
  }

  /**
   * @return {Array<any>}
   */
  getContent () {
    return []
  }

  /**
   * @return {boolean}
   */
  isCountable () {
    return false
  }

  /**
   * @return {ContentDeleted}
   */
  copy () {
    return new ContentDeleted(this.len)
  }

  /**
   * @param {number} offset
   * @return {ContentDeleted}
   */
  splice (offset) {
    const right = new ContentDeleted(this.len - offset);
    this.len = offset;
    return right
  }

  /**
   * @param {ContentDeleted} right
   * @return {boolean}
   */
  mergeWith (right) {
    this.len += right.len;
    return true
  }

  /**
   * @param {Transaction} transaction
   * @param {Item} item
   */
  integrate (transaction, item) {
    addToDeleteSet(transaction.deleteSet, item.id.client, item.id.clock, this.len);
    item.markDeleted();
  }

  /**
   * @param {Transaction} transaction
   */
  delete (transaction) {}
  /**
   * @param {StructStore} store
   */
  gc (store) {}
  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   * @param {number} offset
   */
  write (encoder, offset) {
    encoder.writeLen(this.len - offset);
  }

  /**
   * @return {number}
   */
  getRef () {
    return 1
  }
}

/**
 * @private
 *
 * @param {UpdateDecoderV1 | UpdateDecoderV2 } decoder
 * @return {ContentDeleted}
 */
const readContentDeleted = decoder => new ContentDeleted(decoder.readLen());

/**
 * @param {string} guid
 * @param {Object<string, any>} opts
 */
const createDocFromOpts = (guid, opts) => new Doc({ guid, ...opts, shouldLoad: opts.shouldLoad || opts.autoLoad || false });

/**
 * @private
 */
class ContentDoc {
  /**
   * @param {Doc} doc
   */
  constructor (doc) {
    if (doc._item) {
      console.error('This document was already integrated as a sub-document. You should create a second instance instead with the same guid.');
    }
    /**
     * @type {Doc}
     */
    this.doc = doc;
    /**
     * @type {any}
     */
    const opts = {};
    this.opts = opts;
    if (!doc.gc) {
      opts.gc = false;
    }
    if (doc.autoLoad) {
      opts.autoLoad = true;
    }
    if (doc.meta !== null) {
      opts.meta = doc.meta;
    }
  }

  /**
   * @return {number}
   */
  getLength () {
    return 1
  }

  /**
   * @return {Array<any>}
   */
  getContent () {
    return [this.doc]
  }

  /**
   * @return {boolean}
   */
  isCountable () {
    return true
  }

  /**
   * @return {ContentDoc}
   */
  copy () {
    return new ContentDoc(createDocFromOpts(this.doc.guid, this.opts))
  }

  /**
   * @param {number} offset
   * @return {ContentDoc}
   */
  splice (offset) {
    throw methodUnimplemented()
  }

  /**
   * @param {ContentDoc} right
   * @return {boolean}
   */
  mergeWith (right) {
    return false
  }

  /**
   * @param {Transaction} transaction
   * @param {Item} item
   */
  integrate (transaction, item) {
    // this needs to be reflected in doc.destroy as well
    this.doc._item = item;
    transaction.subdocsAdded.add(this.doc);
    if (this.doc.shouldLoad) {
      transaction.subdocsLoaded.add(this.doc);
    }
  }

  /**
   * @param {Transaction} transaction
   */
  delete (transaction) {
    if (transaction.subdocsAdded.has(this.doc)) {
      transaction.subdocsAdded.delete(this.doc);
    } else {
      transaction.subdocsRemoved.add(this.doc);
    }
  }

  /**
   * @param {StructStore} store
   */
  gc (store) { }

  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   * @param {number} offset
   */
  write (encoder, offset) {
    encoder.writeString(this.doc.guid);
    encoder.writeAny(this.opts);
  }

  /**
   * @return {number}
   */
  getRef () {
    return 9
  }
}

/**
 * @private
 *
 * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
 * @return {ContentDoc}
 */
const readContentDoc = decoder => new ContentDoc(createDocFromOpts(decoder.readString(), decoder.readAny()));

/**
 * @private
 */
class ContentEmbed {
  /**
   * @param {Object} embed
   */
  constructor (embed) {
    this.embed = embed;
  }

  /**
   * @return {number}
   */
  getLength () {
    return 1
  }

  /**
   * @return {Array<any>}
   */
  getContent () {
    return [this.embed]
  }

  /**
   * @return {boolean}
   */
  isCountable () {
    return true
  }

  /**
   * @return {ContentEmbed}
   */
  copy () {
    return new ContentEmbed(this.embed)
  }

  /**
   * @param {number} offset
   * @return {ContentEmbed}
   */
  splice (offset) {
    throw methodUnimplemented()
  }

  /**
   * @param {ContentEmbed} right
   * @return {boolean}
   */
  mergeWith (right) {
    return false
  }

  /**
   * @param {Transaction} transaction
   * @param {Item} item
   */
  integrate (transaction, item) {}
  /**
   * @param {Transaction} transaction
   */
  delete (transaction) {}
  /**
   * @param {StructStore} store
   */
  gc (store) {}
  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   * @param {number} offset
   */
  write (encoder, offset) {
    encoder.writeJSON(this.embed);
  }

  /**
   * @return {number}
   */
  getRef () {
    return 5
  }
}

/**
 * @private
 *
 * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
 * @return {ContentEmbed}
 */
const readContentEmbed = decoder => new ContentEmbed(decoder.readJSON());

/**
 * @private
 */
class ContentFormat {
  /**
   * @param {string} key
   * @param {Object} value
   */
  constructor (key, value) {
    this.key = key;
    this.value = value;
  }

  /**
   * @return {number}
   */
  getLength () {
    return 1
  }

  /**
   * @return {Array<any>}
   */
  getContent () {
    return []
  }

  /**
   * @return {boolean}
   */
  isCountable () {
    return false
  }

  /**
   * @return {ContentFormat}
   */
  copy () {
    return new ContentFormat(this.key, this.value)
  }

  /**
   * @param {number} _offset
   * @return {ContentFormat}
   */
  splice (_offset) {
    throw methodUnimplemented()
  }

  /**
   * @param {ContentFormat} _right
   * @return {boolean}
   */
  mergeWith (_right) {
    return false
  }

  /**
   * @param {Transaction} _transaction
   * @param {Item} item
   */
  integrate (_transaction, item) {
    // @todo searchmarker are currently unsupported for rich text documents
    const p = /** @type {YText} */ (item.parent);
    p._searchMarker = null;
    p._hasFormatting = true;
  }

  /**
   * @param {Transaction} transaction
   */
  delete (transaction) {}
  /**
   * @param {StructStore} store
   */
  gc (store) {}
  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   * @param {number} offset
   */
  write (encoder, offset) {
    encoder.writeKey(this.key);
    encoder.writeJSON(this.value);
  }

  /**
   * @return {number}
   */
  getRef () {
    return 6
  }
}

/**
 * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
 * @return {ContentFormat}
 */
const readContentFormat = decoder => new ContentFormat(decoder.readKey(), decoder.readJSON());

/**
 * @private
 */
class ContentJSON {
  /**
   * @param {Array<any>} arr
   */
  constructor (arr) {
    /**
     * @type {Array<any>}
     */
    this.arr = arr;
  }

  /**
   * @return {number}
   */
  getLength () {
    return this.arr.length
  }

  /**
   * @return {Array<any>}
   */
  getContent () {
    return this.arr
  }

  /**
   * @return {boolean}
   */
  isCountable () {
    return true
  }

  /**
   * @return {ContentJSON}
   */
  copy () {
    return new ContentJSON(this.arr)
  }

  /**
   * @param {number} offset
   * @return {ContentJSON}
   */
  splice (offset) {
    const right = new ContentJSON(this.arr.slice(offset));
    this.arr = this.arr.slice(0, offset);
    return right
  }

  /**
   * @param {ContentJSON} right
   * @return {boolean}
   */
  mergeWith (right) {
    this.arr = this.arr.concat(right.arr);
    return true
  }

  /**
   * @param {Transaction} transaction
   * @param {Item} item
   */
  integrate (transaction, item) {}
  /**
   * @param {Transaction} transaction
   */
  delete (transaction) {}
  /**
   * @param {StructStore} store
   */
  gc (store) {}
  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   * @param {number} offset
   */
  write (encoder, offset) {
    const len = this.arr.length;
    encoder.writeLen(len - offset);
    for (let i = offset; i < len; i++) {
      const c = this.arr[i];
      encoder.writeString(c === undefined ? 'undefined' : JSON.stringify(c));
    }
  }

  /**
   * @return {number}
   */
  getRef () {
    return 2
  }
}

/**
 * @private
 *
 * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
 * @return {ContentJSON}
 */
const readContentJSON = decoder => {
  const len = decoder.readLen();
  const cs = [];
  for (let i = 0; i < len; i++) {
    const c = decoder.readString();
    if (c === 'undefined') {
      cs.push(undefined);
    } else {
      cs.push(JSON.parse(c));
    }
  }
  return new ContentJSON(cs)
};

const isDevMode = getVariable('node_env') === 'development';

class ContentAny {
  /**
   * @param {Array<any>} arr
   */
  constructor (arr) {
    /**
     * @type {Array<any>}
     */
    this.arr = arr;
    isDevMode && deepFreeze(arr);
  }

  /**
   * @return {number}
   */
  getLength () {
    return this.arr.length
  }

  /**
   * @return {Array<any>}
   */
  getContent () {
    return this.arr
  }

  /**
   * @return {boolean}
   */
  isCountable () {
    return true
  }

  /**
   * @return {ContentAny}
   */
  copy () {
    return new ContentAny(this.arr)
  }

  /**
   * @param {number} offset
   * @return {ContentAny}
   */
  splice (offset) {
    const right = new ContentAny(this.arr.slice(offset));
    this.arr = this.arr.slice(0, offset);
    return right
  }

  /**
   * @param {ContentAny} right
   * @return {boolean}
   */
  mergeWith (right) {
    this.arr = this.arr.concat(right.arr);
    return true
  }

  /**
   * @param {Transaction} transaction
   * @param {Item} item
   */
  integrate (transaction, item) {}
  /**
   * @param {Transaction} transaction
   */
  delete (transaction) {}
  /**
   * @param {StructStore} store
   */
  gc (store) {}
  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   * @param {number} offset
   */
  write (encoder, offset) {
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
  getRef () {
    return 8
  }
}

/**
 * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
 * @return {ContentAny}
 */
const readContentAny = decoder => {
  const len = decoder.readLen();
  const cs = [];
  for (let i = 0; i < len; i++) {
    cs.push(decoder.readAny());
  }
  return new ContentAny(cs)
};

/**
 * @private
 */
class ContentString {
  /**
   * @param {string} str
   */
  constructor (str) {
    /**
     * @type {string}
     */
    this.str = str;
  }

  /**
   * @return {number}
   */
  getLength () {
    return this.str.length
  }

  /**
   * @return {Array<any>}
   */
  getContent () {
    return this.str.split('')
  }

  /**
   * @return {boolean}
   */
  isCountable () {
    return true
  }

  /**
   * @return {ContentString}
   */
  copy () {
    return new ContentString(this.str)
  }

  /**
   * @param {number} offset
   * @return {ContentString}
   */
  splice (offset) {
    const right = new ContentString(this.str.slice(offset));
    this.str = this.str.slice(0, offset);

    // Prevent encoding invalid documents because of splitting of surrogate pairs: https://github.com/yjs/yjs/issues/248
    const firstCharCode = this.str.charCodeAt(offset - 1);
    if (firstCharCode >= 0xD800 && firstCharCode <= 0xDBFF) {
      // Last character of the left split is the start of a surrogate utf16/ucs2 pair.
      // We don't support splitting of surrogate pairs because this may lead to invalid documents.
      // Replace the invalid character with a unicode replacement character (� / U+FFFD)
      this.str = this.str.slice(0, offset - 1) + '�';
      // replace right as well
      right.str = '�' + right.str.slice(1);
    }
    return right
  }

  /**
   * @param {ContentString} right
   * @return {boolean}
   */
  mergeWith (right) {
    this.str += right.str;
    return true
  }

  /**
   * @param {Transaction} transaction
   * @param {Item} item
   */
  integrate (transaction, item) {}
  /**
   * @param {Transaction} transaction
   */
  delete (transaction) {}
  /**
   * @param {StructStore} store
   */
  gc (store) {}
  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   * @param {number} offset
   */
  write (encoder, offset) {
    encoder.writeString(offset === 0 ? this.str : this.str.slice(offset));
  }

  /**
   * @return {number}
   */
  getRef () {
    return 4
  }
}

/**
 * @private
 *
 * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
 * @return {ContentString}
 */
const readContentString = decoder => new ContentString(decoder.readString());

/**
 * @type {Array<function(UpdateDecoderV1 | UpdateDecoderV2):AbstractType<any>>}
 * @private
 */
const typeRefs = [
  readYArray,
  readYMap,
  readYText,
  readYXmlElement,
  readYXmlFragment,
  readYXmlHook,
  readYXmlText
];

const YArrayRefID = 0;
const YMapRefID = 1;
const YTextRefID = 2;
const YXmlElementRefID = 3;
const YXmlFragmentRefID = 4;
const YXmlHookRefID = 5;
const YXmlTextRefID = 6;

/**
 * @private
 */
class ContentType {
  /**
   * @param {AbstractType<any>} type
   */
  constructor (type) {
    /**
     * @type {AbstractType<any>}
     */
    this.type = type;
  }

  /**
   * @return {number}
   */
  getLength () {
    return 1
  }

  /**
   * @return {Array<any>}
   */
  getContent () {
    return [this.type]
  }

  /**
   * @return {boolean}
   */
  isCountable () {
    return true
  }

  /**
   * @return {ContentType}
   */
  copy () {
    return new ContentType(this.type._copy())
  }

  /**
   * @param {number} offset
   * @return {ContentType}
   */
  splice (offset) {
    throw methodUnimplemented()
  }

  /**
   * @param {ContentType} right
   * @return {boolean}
   */
  mergeWith (right) {
    return false
  }

  /**
   * @param {Transaction} transaction
   * @param {Item} item
   */
  integrate (transaction, item) {
    this.type._integrate(transaction.doc, item);
  }

  /**
   * @param {Transaction} transaction
   */
  delete (transaction) {
    let item = this.type._start;
    while (item !== null) {
      if (!item.deleted) {
        item.delete(transaction);
      } else if (item.id.clock < (transaction.beforeState.get(item.id.client) || 0)) {
        // This will be gc'd later and we want to merge it if possible
        // We try to merge all deleted items after each transaction,
        // but we have no knowledge about that this needs to be merged
        // since it is not in transaction.ds. Hence we add it to transaction._mergeStructs
        transaction._mergeStructs.push(item);
      }
      item = item.right;
    }
    this.type._map.forEach(item => {
      if (!item.deleted) {
        item.delete(transaction);
      } else if (item.id.clock < (transaction.beforeState.get(item.id.client) || 0)) {
        // same as above
        transaction._mergeStructs.push(item);
      }
    });
    transaction.changed.delete(this.type);
  }

  /**
   * @param {StructStore} store
   */
  gc (store) {
    let item = this.type._start;
    while (item !== null) {
      item.gc(store, true);
      item = item.right;
    }
    this.type._start = null;
    this.type._map.forEach(/** @param {Item | null} item */ (item) => {
      while (item !== null) {
        item.gc(store, true);
        item = item.left;
      }
    });
    this.type._map = new Map();
  }

  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   * @param {number} offset
   */
  write (encoder, offset) {
    this.type._write(encoder);
  }

  /**
   * @return {number}
   */
  getRef () {
    return 7
  }
}

/**
 * @private
 *
 * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
 * @return {ContentType}
 */
const readContentType = decoder => new ContentType(typeRefs[decoder.readTypeRef()](decoder));

/**
 * @todo This should return several items
 *
 * @param {StructStore} store
 * @param {ID} id
 * @return {{item:Item, diff:number}}
 */
const followRedone = (store, id) => {
  /**
   * @type {ID|null}
   */
  let nextID = id;
  let diff = 0;
  let item;
  do {
    if (diff > 0) {
      nextID = createID(nextID.client, nextID.clock + diff);
    }
    item = getItem(store, nextID);
    diff = nextID.clock - item.id.clock;
    nextID = item.redone;
  } while (nextID !== null && item instanceof Item)
  return {
    item, diff
  }
};

/**
 * Make sure that neither item nor any of its parents is ever deleted.
 *
 * This property does not persist when storing it into a database or when
 * sending it to other peers
 *
 * @param {Item|null} item
 * @param {boolean} keep
 */
const keepItem = (item, keep) => {
  while (item !== null && item.keep !== keep) {
    item.keep = keep;
    item = /** @type {AbstractType<any>} */ (item.parent)._item;
  }
};

/**
 * Split leftItem into two items
 * @param {Transaction} transaction
 * @param {Item} leftItem
 * @param {number} diff
 * @return {Item}
 *
 * @function
 * @private
 */
const splitItem = (transaction, leftItem, diff) => {
  // create rightItem
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
  // update left (do not set leftItem.rightOrigin as it will lead to problems when syncing)
  leftItem.right = rightItem;
  // update right
  if (rightItem.right !== null) {
    rightItem.right.left = rightItem;
  }
  // right is more specific.
  transaction._mergeStructs.push(rightItem);
  // update parent._map
  if (rightItem.parentSub !== null && rightItem.right === null) {
    /** @type {AbstractType<any>} */ (rightItem.parent)._map.set(rightItem.parentSub, rightItem);
  }
  leftItem.length = diff;
  return rightItem
};

/**
 * @param {Array<StackItem>} stack
 * @param {ID} id
 */
const isDeletedByUndoStack = (stack, id) => some(stack, /** @param {StackItem} s */ s => isDeleted(s.deletions, id));

/**
 * Redoes the effect of this operation.
 *
 * @param {Transaction} transaction The Yjs instance.
 * @param {Item} item
 * @param {Set<Item>} redoitems
 * @param {DeleteSet} itemsToDelete
 * @param {boolean} ignoreRemoteMapChanges
 * @param {import('../utils/UndoManager.js').UndoManager} um
 *
 * @return {Item|null}
 *
 * @private
 */
const redoItem = (transaction, item, redoitems, itemsToDelete, ignoreRemoteMapChanges, um) => {
  const doc = transaction.doc;
  const store = doc.store;
  const ownClientID = doc.clientID;
  const redone = item.redone;
  if (redone !== null) {
    return getItemCleanStart(transaction, redone)
  }
  let parentItem = /** @type {AbstractType<any>} */ (item.parent)._item;
  /**
   * @type {Item|null}
   */
  let left = null;
  /**
   * @type {Item|null}
   */
  let right;
  // make sure that parent is redone
  if (parentItem !== null && parentItem.deleted === true) {
    // try to undo parent if it will be undone anyway
    if (parentItem.redone === null && (!redoitems.has(parentItem) || redoItem(transaction, parentItem, redoitems, itemsToDelete, ignoreRemoteMapChanges, um) === null)) {
      return null
    }
    while (parentItem.redone !== null) {
      parentItem = getItemCleanStart(transaction, parentItem.redone);
    }
  }
  const parentType = parentItem === null ? /** @type {AbstractType<any>} */ (item.parent) : /** @type {ContentType} */ (parentItem.content).type;

  if (item.parentSub === null) {
    // Is an array item. Insert at the old position
    left = item.left;
    right = item;
    // find next cloned_redo items
    while (left !== null) {
      /**
       * @type {Item|null}
       */
      let leftTrace = left;
      // trace redone until parent matches
      while (leftTrace !== null && /** @type {AbstractType<any>} */ (leftTrace.parent)._item !== parentItem) {
        leftTrace = leftTrace.redone === null ? null : getItemCleanStart(transaction, leftTrace.redone);
      }
      if (leftTrace !== null && /** @type {AbstractType<any>} */ (leftTrace.parent)._item === parentItem) {
        left = leftTrace;
        break
      }
      left = left.left;
    }
    while (right !== null) {
      /**
       * @type {Item|null}
       */
      let rightTrace = right;
      // trace redone until parent matches
      while (rightTrace !== null && /** @type {AbstractType<any>} */ (rightTrace.parent)._item !== parentItem) {
        rightTrace = rightTrace.redone === null ? null : getItemCleanStart(transaction, rightTrace.redone);
      }
      if (rightTrace !== null && /** @type {AbstractType<any>} */ (rightTrace.parent)._item === parentItem) {
        right = rightTrace;
        break
      }
      right = right.right;
    }
  } else {
    right = null;
    if (item.right && !ignoreRemoteMapChanges) {
      left = item;
      // Iterate right while right is in itemsToDelete
      // If it is intended to delete right while item is redone, we can expect that item should replace right.
      while (left !== null && left.right !== null && (left.right.redone || isDeleted(itemsToDelete, left.right.id) || isDeletedByUndoStack(um.undoStack, left.right.id) || isDeletedByUndoStack(um.redoStack, left.right.id))) {
        left = left.right;
        // follow redone
        while (left.redone) left = getItemCleanStart(transaction, left.redone);
      }
      if (left && left.right !== null) {
        // It is not possible to redo this item because it conflicts with a
        // change from another client
        return null
      }
    } else {
      left = parentType._map.get(item.parentSub) || null;
    }
  }
  const nextClock = getState(store, ownClientID);
  const nextId = createID(ownClientID, nextClock);
  const redoneItem = new Item(
    nextId,
    left, left && left.lastId,
    right, right && right.id,
    parentType,
    item.parentSub,
    item.content.copy()
  );
  item.redone = nextId;
  keepItem(redoneItem, true);
  redoneItem.integrate(transaction, 0);
  return redoneItem
};

/**
 * Abstract class that represents any content.
 */
class Item extends AbstractStruct {
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
  constructor (id, left, origin, right, rightOrigin, parent, parentSub, content) {
    super(id, content.getLength());
    /**
     * The item that was originally to the left of this item.
     * @type {ID | null}
     */
    this.origin = origin;
    /**
     * The item that is currently to the left of this item.
     * @type {Item | null}
     */
    this.left = left;
    /**
     * The item that is currently to the right of this item.
     * @type {Item | null}
     */
    this.right = right;
    /**
     * The item that was originally to the right of this item.
     * @type {ID | null}
     */
    this.rightOrigin = rightOrigin;
    /**
     * @type {AbstractType<any>|ID|null}
     */
    this.parent = parent;
    /**
     * If the parent refers to this item with some kind of key (e.g. YMap, the
     * key is specified here. The key is then used to refer to the list in which
     * to insert this item. If `parentSub = null` type._start is the list in
     * which to insert to. Otherwise it is `parent._map`.
     * @type {String | null}
     */
    this.parentSub = parentSub;
    /**
     * If this type's effect is redone this type refers to the type that undid
     * this operation.
     * @type {ID | null}
     */
    this.redone = null;
    /**
     * @type {AbstractContent}
     */
    this.content = content;
    /**
     * bit1: keep
     * bit2: countable
     * bit3: deleted
     * bit4: mark - mark node as fast-search-marker
     * @type {number} byte
     */
    this.info = this.content.isCountable() ? BIT2 : 0;
  }

  /**
   * This is used to mark the item as an indexed fast-search marker
   *
   * @type {boolean}
   */
  set marker (isMarked) {
    if (((this.info & BIT4) > 0) !== isMarked) {
      this.info ^= BIT4;
    }
  }

  get marker () {
    return (this.info & BIT4) > 0
  }

  /**
   * If true, do not garbage collect this Item.
   */
  get keep () {
    return (this.info & BIT1) > 0
  }

  set keep (doKeep) {
    if (this.keep !== doKeep) {
      this.info ^= BIT1;
    }
  }

  get countable () {
    return (this.info & BIT2) > 0
  }

  /**
   * Whether this item was deleted or not.
   * @type {Boolean}
   */
  get deleted () {
    return (this.info & BIT3) > 0
  }

  set deleted (doDelete) {
    if (this.deleted !== doDelete) {
      this.info ^= BIT3;
    }
  }

  markDeleted () {
    this.info |= BIT3;
  }

  /**
   * Return the creator clientID of the missing op or define missing items and return null.
   *
   * @param {Transaction} transaction
   * @param {StructStore} store
   * @return {null | number}
   */
  getMissing (transaction, store) {
    if (this.origin && this.origin.client !== this.id.client && this.origin.clock >= getState(store, this.origin.client)) {
      return this.origin.client
    }
    if (this.rightOrigin && this.rightOrigin.client !== this.id.client && this.rightOrigin.clock >= getState(store, this.rightOrigin.client)) {
      return this.rightOrigin.client
    }
    if (this.parent && this.parent.constructor === ID && this.id.client !== this.parent.client && this.parent.clock >= getState(store, this.parent.client)) {
      return this.parent.client
    }

    // We have all missing ids, now find the items

    if (this.origin) {
      this.left = getItemCleanEnd(transaction, store, this.origin);
      this.origin = this.left.lastId;
    }
    if (this.rightOrigin) {
      this.right = getItemCleanStart(transaction, this.rightOrigin);
      this.rightOrigin = this.right.id;
    }
    if ((this.left && this.left.constructor === GC) || (this.right && this.right.constructor === GC)) {
      this.parent = null;
    } else if (!this.parent) {
      // only set parent if this shouldn't be garbage collected
      if (this.left && this.left.constructor === Item) {
        this.parent = this.left.parent;
        this.parentSub = this.left.parentSub;
      } else if (this.right && this.right.constructor === Item) {
        this.parent = this.right.parent;
        this.parentSub = this.right.parentSub;
      }
    } else if (this.parent.constructor === ID) {
      const parentItem = getItem(store, this.parent);
      if (parentItem.constructor === GC) {
        this.parent = null;
      } else {
        this.parent = /** @type {ContentType} */ (parentItem.content).type;
      }
    }
    return null
  }

  /**
   * @param {Transaction} transaction
   * @param {number} offset
   */
  integrate (transaction, offset) {
    if (offset > 0) {
      this.id.clock += offset;
      this.left = getItemCleanEnd(transaction, transaction.doc.store, createID(this.id.client, this.id.clock - 1));
      this.origin = this.left.lastId;
      this.content = this.content.splice(offset);
      this.length -= offset;
    }

    if (this.parent) {
      if ((!this.left && (!this.right || this.right.left !== null)) || (this.left && this.left.right !== this.right)) {
        /**
         * @type {Item|null}
         */
        let left = this.left;

        /**
         * @type {Item|null}
         */
        let o;
        // set o to the first conflicting item
        if (left !== null) {
          o = left.right;
        } else if (this.parentSub !== null) {
          o = /** @type {AbstractType<any>} */ (this.parent)._map.get(this.parentSub) || null;
          while (o !== null && o.left !== null) {
            o = o.left;
          }
        } else {
          o = /** @type {AbstractType<any>} */ (this.parent)._start;
        }
        // TODO: use something like DeleteSet here (a tree implementation would be best)
        // @todo use global set definitions
        /**
         * @type {Set<Item>}
         */
        const conflictingItems = new Set();
        /**
         * @type {Set<Item>}
         */
        const itemsBeforeOrigin = new Set();
        // Let c in conflictingItems, b in itemsBeforeOrigin
        // ***{origin}bbbb{this}{c,b}{c,b}{o}***
        // Note that conflictingItems is a subset of itemsBeforeOrigin
        while (o !== null && o !== this.right) {
          itemsBeforeOrigin.add(o);
          conflictingItems.add(o);
          if (compareIDs(this.origin, o.origin)) {
            // case 1
            if (o.id.client < this.id.client) {
              left = o;
              conflictingItems.clear();
            } else if (compareIDs(this.rightOrigin, o.rightOrigin)) {
              // this and o are conflicting and point to the same integration points. The id decides which item comes first.
              // Since this is to the left of o, we can break here
              break
            } // else, o might be integrated before an item that this conflicts with. If so, we will find it in the next iterations
          } else if (o.origin !== null && itemsBeforeOrigin.has(getItem(transaction.doc.store, o.origin))) { // use getItem instead of getItemCleanEnd because we don't want / need to split items.
            // case 2
            if (!conflictingItems.has(getItem(transaction.doc.store, o.origin))) {
              left = o;
              conflictingItems.clear();
            }
          } else {
            break
          }
          o = o.right;
        }
        this.left = left;
      }
      // reconnect left/right + update parent map/start if necessary
      if (this.left !== null) {
        const right = this.left.right;
        this.right = right;
        this.left.right = this;
      } else {
        let r;
        if (this.parentSub !== null) {
          r = /** @type {AbstractType<any>} */ (this.parent)._map.get(this.parentSub) || null;
          while (r !== null && r.left !== null) {
            r = r.left;
          }
        } else {
          r = /** @type {AbstractType<any>} */ (this.parent)._start
          ;/** @type {AbstractType<any>} */ (this.parent)._start = this;
        }
        this.right = r;
      }
      if (this.right !== null) {
        this.right.left = this;
      } else if (this.parentSub !== null) {
        // set as current parent value if right === null and this is parentSub
        /** @type {AbstractType<any>} */ (this.parent)._map.set(this.parentSub, this);
        if (this.left !== null) {
          // this is the current attribute value of parent. delete right
          this.left.delete(transaction);
        }
      }
      // adjust length of parent
      if (this.parentSub === null && this.countable && !this.deleted) {
        /** @type {AbstractType<any>} */ (this.parent)._length += this.length;
      }
      addStruct(transaction.doc.store, this);
      this.content.integrate(transaction, this);
      // add parent to transaction.changed
      addChangedTypeToTransaction(transaction, /** @type {AbstractType<any>} */ (this.parent), this.parentSub);
      if ((/** @type {AbstractType<any>} */ (this.parent)._item !== null && /** @type {AbstractType<any>} */ (this.parent)._item.deleted) || (this.parentSub !== null && this.right !== null)) {
        // delete if parent is deleted or if this is not the current attribute value of parent
        this.delete(transaction);
      }
    } else {
      // parent is not defined. Integrate GC struct instead
      new GC(this.id, this.length).integrate(transaction, 0);
    }
  }

  /**
   * Returns the next non-deleted item
   */
  get next () {
    let n = this.right;
    while (n !== null && n.deleted) {
      n = n.right;
    }
    return n
  }

  /**
   * Returns the previous non-deleted item
   */
  get prev () {
    let n = this.left;
    while (n !== null && n.deleted) {
      n = n.left;
    }
    return n
  }

  /**
   * Computes the last content address of this Item.
   */
  get lastId () {
    // allocating ids is pretty costly because of the amount of ids created, so we try to reuse whenever possible
    return this.length === 1 ? this.id : createID(this.id.client, this.id.clock + this.length - 1)
  }

  /**
   * Try to merge two items
   *
   * @param {Item} right
   * @return {boolean}
   */
  mergeWith (right) {
    if (
      this.constructor === right.constructor &&
      compareIDs(right.origin, this.lastId) &&
      this.right === right &&
      compareIDs(this.rightOrigin, right.rightOrigin) &&
      this.id.client === right.id.client &&
      this.id.clock + this.length === right.id.clock &&
      this.deleted === right.deleted &&
      this.redone === null &&
      right.redone === null &&
      this.content.constructor === right.content.constructor &&
      this.content.mergeWith(right.content)
    ) {
      const searchMarker = /** @type {AbstractType<any>} */ (this.parent)._searchMarker;
      if (searchMarker) {
        searchMarker.forEach(marker => {
          if (marker.p === right) {
            // right is going to be "forgotten" so we need to update the marker
            marker.p = this;
            // adjust marker index
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
      return true
    }
    return false
  }

  /**
   * Mark this Item as deleted.
   *
   * @param {Transaction} transaction
   */
  delete (transaction) {
    if (!this.deleted) {
      const parent = /** @type {AbstractType<any>} */ (this.parent);
      // adjust the length of parent
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
  gc (store, parentGCd) {
    if (!this.deleted) {
      throw unexpectedCase()
    }
    this.content.gc(store);
    if (parentGCd) {
      replaceStruct(store, this, new GC(this.id, this.length));
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
  write (encoder, offset) {
    const origin = offset > 0 ? createID(this.id.client, this.id.clock + offset - 1) : this.origin;
    const rightOrigin = this.rightOrigin;
    const parentSub = this.parentSub;
    const info = (this.content.getRef() & BITS5) |
      (origin === null ? 0 : BIT8) | // origin is defined
      (rightOrigin === null ? 0 : BIT7) | // right origin is defined
      (parentSub === null ? 0 : BIT6); // parentSub is non-null
    encoder.writeInfo(info);
    if (origin !== null) {
      encoder.writeLeftID(origin);
    }
    if (rightOrigin !== null) {
      encoder.writeRightID(rightOrigin);
    }
    if (origin === null && rightOrigin === null) {
      const parent = /** @type {AbstractType<any>} */ (this.parent);
      if (parent._item !== undefined) {
        const parentItem = parent._item;
        if (parentItem === null) {
          // parent type on y._map
          // find the correct key
          const ykey = findRootTypeKey(parent);
          encoder.writeParentInfo(true); // write parentYKey
          encoder.writeString(ykey);
        } else {
          encoder.writeParentInfo(false); // write parent id
          encoder.writeLeftID(parentItem.id);
        }
      } else if (parent.constructor === String) { // this edge case was added by differential updates
        encoder.writeParentInfo(true); // write parentYKey
        encoder.writeString(parent);
      } else if (parent.constructor === ID) {
        encoder.writeParentInfo(false); // write parent id
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
}

/**
 * @param {UpdateDecoderV1 | UpdateDecoderV2} decoder
 * @param {number} info
 */
const readItemContent = (decoder, info) => contentRefs[info & binary.BITS5](decoder);

/**
 * A lookup map for reading Item content.
 *
 * @type {Array<function(UpdateDecoderV1 | UpdateDecoderV2):AbstractContent>}
 */
const contentRefs = [
  () => { unexpectedCase(); }, // GC is not ItemContent
  readContentDeleted, // 1
  readContentJSON, // 2
  readContentBinary, // 3
  readContentString, // 4
  readContentEmbed, // 5
  readContentFormat, // 6
  readContentType, // 7
  readContentAny, // 8
  readContentDoc, // 9
  () => { unexpectedCase(); } // 10 - Skip is not ItemContent
];

const structSkipRefNumber = 10;

/**
 * @private
 */
class Skip extends (/* unused pure expression or super */ null && (AbstractStruct)) {
  get deleted () {
    return true
  }

  delete () {}

  /**
   * @param {Skip} right
   * @return {boolean}
   */
  mergeWith (right) {
    if (this.constructor !== right.constructor) {
      return false
    }
    this.length += right.length;
    return true
  }

  /**
   * @param {Transaction} transaction
   * @param {number} offset
   */
  integrate (transaction, offset) {
    // skip structs cannot be integrated
    error.unexpectedCase();
  }

  /**
   * @param {UpdateEncoderV1 | UpdateEncoderV2} encoder
   * @param {number} offset
   */
  write (encoder, offset) {
    encoder.writeInfo(structSkipRefNumber);
    // write as VarUint because Skips can't make use of predictable length-encoding
    encoding.writeVarUint(encoder.restEncoder, this.length - offset);
  }

  /**
   * @param {Transaction} transaction
   * @param {StructStore} store
   * @return {null | number}
   */
  getMissing (transaction, store) {
    return null
  }
}

/** eslint-env browser */


const glo = /** @type {any} */ (typeof globalThis !== 'undefined'
  ? globalThis
  : typeof window !== 'undefined'
    ? window
    // @ts-ignore
    : typeof global !== 'undefined' ? global : {});

const importIdentifier = '__ $YJS$ __';

if (glo[importIdentifier] === true) {
  /**
   * Dear reader of this message. Please take this seriously.
   *
   * If you see this message, make sure that you only import one version of Yjs. In many cases,
   * your package manager installs two versions of Yjs that are used by different packages within your project.
   * Another reason for this message is that some parts of your project use the commonjs version of Yjs
   * and others use the EcmaScript version of Yjs.
   *
   * This often leads to issues that are hard to debug. We often need to perform constructor checks,
   * e.g. `struct instanceof GC`. If you imported different versions of Yjs, it is impossible for us to
   * do the constructor checks anymore - which might break the CRDT algorithm.
   *
   * https://github.com/yjs/yjs/issues/438
   */
  console.error('Yjs was already imported. This breaks constructor checks and will lead to issues! - https://github.com/yjs/yjs/issues/438');
}
glo[importIdentifier] = true;


//# sourceMappingURL=yjs.mjs.map

// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/node_modules/uuid/dist/esm-browser/v4.js + 3 modules
var v4 = __webpack_require__(1569);
// EXTERNAL MODULE: external ["wp","richText"]
var external_wp_richText_ = __webpack_require__(876);
;// ./node_modules/@wordpress/core-data/build-module/utils/crdt-blocks.js





const serializableBlocksCache = /* @__PURE__ */ new WeakMap();
function makeBlockAttributesSerializable(attributes) {
  const newAttributes = { ...attributes };
  for (const [key, value] of Object.entries(attributes)) {
    if (value instanceof external_wp_richText_.RichTextData) {
      newAttributes[key] = value.valueOf();
    }
  }
  return newAttributes;
}
function makeBlocksSerializable(blocks) {
  return blocks.map((block) => {
    const blockAsJson = block instanceof YMap ? block.toJSON() : block;
    const { name, innerBlocks, attributes, ...rest } = blockAsJson;
    delete rest.validationIssues;
    delete rest.originalContent;
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
  const res = es6_default()(
    Object.assign({}, gblock, overwrites),
    Object.assign({}, yblockAsJson, overwrites)
  );
  const inners = gblock.innerBlocks || [];
  const yinners = yblock.get("innerBlocks");
  return res && inners.length === yinners.length && inners.every(
    (block, i) => areBlocksEqual(block, yinners.get(i))
  );
}
function createNewYAttributeMap(blockName, attributes) {
  return new YMap(
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
    return new YText(attributeValue?.toString() ?? "");
  }
  return attributeValue;
}
function createNewYBlock(block) {
  return new YMap(
    Object.entries(block).map(([key, value]) => {
      switch (key) {
        case "attributes": {
          return [key, createNewYAttributeMap(block.name, value)];
        }
        case "innerBlocks": {
          const innerBlocks = new YArray();
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
  );
}
function mergeCrdtBlocks(yblocks, incomingBlocks, lastSelection) {
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
          const currentAttributes = yblock.get(
            key
          );
          if (!currentAttributes) {
            yblock.set(
              key,
              createNewYAttributeMap(block.name, value)
            );
            break;
          }
          Object.entries(value).forEach(
            ([attributeName, attributeValue]) => {
              if (es6_default()(
                currentAttributes?.get(attributeName),
                attributeValue
              )) {
                return;
              }
              const isRichText = isRichTextAttribute(
                block.name,
                attributeName
              );
              if (isRichText && "string" === typeof attributeValue) {
                const blockYText = currentAttributes.get(
                  attributeName
                );
                mergeRichTextUpdate(
                  blockYText,
                  attributeValue,
                  lastSelection
                );
              } else {
                currentAttributes.set(
                  attributeName,
                  createNewYAttributeValue(
                    block.name,
                    attributeName,
                    attributeValue
                  )
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
          const yInnerBlocks = yblock.get(key);
          mergeCrdtBlocks(yInnerBlocks, value ?? [], lastSelection);
          break;
        }
        default:
          if (!es6_default()(block[key], yblock.get(key))) {
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
    if (knownClientIds.has(clientId)) {
      clientId = (0,v4/* default */.A)();
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
let cachedRichTextAttributes;
function isRichTextAttribute(blockName, attributeName) {
  if (!cachedRichTextAttributes) {
    cachedRichTextAttributes = /* @__PURE__ */ new Map();
    for (const blockType of (0,external_wp_blocks_.getBlockTypes)()) {
      const richTextAttributeMap = /* @__PURE__ */ new Map();
      for (const [name, definition] of Object.entries(
        blockType.attributes ?? {}
      )) {
        if ("rich-text" === definition.type) {
          richTextAttributeMap.set(name, true);
        }
      }
      cachedRichTextAttributes.set(
        blockType.name,
        richTextAttributeMap
      );
    }
  }
  return cachedRichTextAttributes.get(blockName)?.has(attributeName) ?? false;
}
function mergeRichTextUpdate(blockYText, updatedValue, lastSelection) {
  blockYText.delete(0, blockYText.toString().length);
  blockYText.insert(0, updatedValue);
}


;// ./node_modules/@wordpress/sync/build-module/config.js
const CRDT_DOC_VERSION = 1;
const CRDT_RECORD_MAP_KEY = "document";
const CRDT_STATE_MAP_KEY = "state";
const CRDT_STATE_VERSION_KEY = "version";
const LOCAL_EDITOR_ORIGIN = "gutenberg";
const LOCAL_SYNC_MANAGER_ORIGIN = "syncManager";


;// ./node_modules/@wordpress/core-data/build-module/utils/crdt.js




let lastSelection = null;
const allowedPostProperties = /* @__PURE__ */ new Set([
  "author",
  "blocks",
  "comment_status",
  "date",
  "excerpt",
  "featured_media",
  "format",
  "ping_status",
  "slug",
  "status",
  "sticky",
  "tags",
  "template",
  "title"
]);
function defaultApplyChangesToCRDTDoc(ydoc, changes) {
  const ymap = ydoc.getMap(CRDT_RECORD_MAP_KEY);
  Object.entries(changes).forEach(([key, newValue]) => {
    if ("function" === typeof newValue) {
      return;
    }
    function setValue(updatedValue) {
      ymap.set(key, updatedValue);
    }
    switch (key) {
      // Add support for additional data types here.
      default: {
        const currentValue = ymap.get(key);
        mergeValue(currentValue, newValue, setValue);
      }
    }
  });
}
function applyPostChangesToCRDTDoc(ydoc, changes, postType) {
  const ymap = ydoc.getMap(CRDT_RECORD_MAP_KEY);
  Object.entries(changes).forEach(([key, newValue]) => {
    if (!allowedPostProperties.has(key)) {
      return;
    }
    if ("function" === typeof newValue) {
      return;
    }
    function setValue(updatedValue) {
      ymap.set(key, updatedValue);
    }
    switch (key) {
      case "blocks": {
        let currentBlocks = ymap.get("blocks");
        if (!(currentBlocks instanceof YArray)) {
          currentBlocks = new YArray();
          setValue(currentBlocks);
        }
        const newBlocks = newValue ?? [];
        mergeCrdtBlocks(currentBlocks, newBlocks, lastSelection);
        break;
      }
      case "excerpt": {
        const currentValue = ymap.get("excerpt");
        const rawNewValue = getRawValue(newValue);
        mergeValue(currentValue, rawNewValue, setValue);
        break;
      }
      case "slug": {
        if (!newValue) {
          break;
        }
        const currentValue = ymap.get("slug");
        mergeValue(currentValue, newValue, setValue);
        break;
      }
      case "title": {
        const currentValue = ymap.get("title");
        let rawNewValue = getRawValue(newValue);
        if (!currentValue && "Auto Draft" === rawNewValue) {
          rawNewValue = "";
        }
        mergeValue(currentValue, rawNewValue, setValue);
        break;
      }
      // Add support for additional data types here.
      default: {
        const currentValue = ymap.get(key);
        mergeValue(currentValue, newValue, setValue);
      }
    }
  });
  if ("selection" in changes) {
    lastSelection = changes.selection?.selectionStart ?? null;
  }
}
function defaultGetChangesFromCRDTDoc(crdtDoc) {
  return crdtDoc.getMap(CRDT_RECORD_MAP_KEY).toJSON();
}
function getPostChangesFromCRDTDoc(ydoc, editedRecord, postType) {
  const ymap = ydoc.getMap(CRDT_RECORD_MAP_KEY);
  return Object.fromEntries(
    Object.entries(ymap.toJSON()).filter(([key, newValue]) => {
      if (!allowedPostProperties.has(key)) {
        return false;
      }
      const currentValue = editedRecord[key];
      switch (key) {
        case "blocks": {
          return true;
        }
        case "date": {
          const currentDateIsFloating = ["draft", "auto-draft", "pending"].includes(
            ymap.get("status")
          ) && (null === currentValue || editedRecord.modified === currentValue);
          if (!newValue && currentDateIsFloating) {
            return false;
          }
          return haveValuesChanged(currentValue, newValue);
        }
        case "status": {
          if ("auto-draft" === newValue) {
            return false;
          }
          return haveValuesChanged(currentValue, newValue);
        }
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
}
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
  return !es6_default()(currentValue, newValue);
}
function mergeValue(currentValue, newValue, setValue) {
  if (haveValuesChanged(currentValue, newValue)) {
    setValue(newValue);
  }
}


;// ./node_modules/@wordpress/core-data/build-module/entities.js





const DEFAULT_ENTITY_KEY = "id";
const POST_RAW_ATTRIBUTES = ["title", "excerpt", "content"];
const blocksTransientEdits = {
  blocks: {
    read: (record) => (0,external_wp_blocks_.parse)(record.content?.raw ?? ""),
    write: (record) => ({
      content: (0,external_wp_blocks_.__unstableSerializeAndClean)(record.blocks)
    })
  }
};
const rootEntitiesConfig = [
  {
    label: (0,external_wp_i18n_.__)("Base"),
    kind: "root",
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
    label: (0,external_wp_i18n_.__)("Post Type"),
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
    label: (0,external_wp_i18n_.__)("Media"),
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
    label: (0,external_wp_i18n_.__)("Taxonomy")
  },
  {
    name: "sidebar",
    kind: "root",
    baseURL: "/wp/v2/sidebars",
    baseURLParams: { context: "edit" },
    plural: "sidebars",
    transientEdits: { blocks: true },
    label: (0,external_wp_i18n_.__)("Widget areas")
  },
  {
    name: "widget",
    kind: "root",
    baseURL: "/wp/v2/widgets",
    baseURLParams: { context: "edit" },
    plural: "widgets",
    transientEdits: { blocks: true },
    label: (0,external_wp_i18n_.__)("Widgets")
  },
  {
    name: "widgetType",
    kind: "root",
    baseURL: "/wp/v2/widget-types",
    baseURLParams: { context: "edit" },
    plural: "widgetTypes",
    label: (0,external_wp_i18n_.__)("Widget types")
  },
  {
    label: (0,external_wp_i18n_.__)("User"),
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
    label: (0,external_wp_i18n_.__)("Comment"),
    supportsPagination: true
  },
  {
    name: "menu",
    kind: "root",
    baseURL: "/wp/v2/menus",
    baseURLParams: { context: "edit" },
    plural: "menus",
    label: (0,external_wp_i18n_.__)("Menu"),
    supportsPagination: true
  },
  {
    name: "menuItem",
    kind: "root",
    baseURL: "/wp/v2/menu-items",
    baseURLParams: { context: "edit" },
    plural: "menuItems",
    label: (0,external_wp_i18n_.__)("Menu Item"),
    rawAttributes: ["title"],
    supportsPagination: true
  },
  {
    name: "menuLocation",
    kind: "root",
    baseURL: "/wp/v2/menu-locations",
    baseURLParams: { context: "edit" },
    plural: "menuLocations",
    label: (0,external_wp_i18n_.__)("Menu Location"),
    key: "name"
  },
  {
    label: (0,external_wp_i18n_.__)("Global Styles"),
    name: "globalStyles",
    kind: "root",
    baseURL: "/wp/v2/global-styles",
    baseURLParams: { context: "edit" },
    plural: "globalStylesVariations",
    // Should be different from name.
    getTitle: () => (0,external_wp_i18n_.__)("Custom Styles"),
    getRevisionsUrl: (parentId, revisionId) => `/wp/v2/global-styles/${parentId}/revisions${revisionId ? "/" + revisionId : ""}`,
    supportsPagination: true
  },
  {
    label: (0,external_wp_i18n_.__)("Themes"),
    name: "theme",
    kind: "root",
    baseURL: "/wp/v2/themes",
    baseURLParams: { context: "edit" },
    plural: "themes",
    key: "stylesheet"
  },
  {
    label: (0,external_wp_i18n_.__)("Plugins"),
    name: "plugin",
    kind: "root",
    baseURL: "/wp/v2/plugins",
    baseURLParams: { context: "edit" },
    plural: "plugins",
    key: "plugin"
  },
  {
    label: (0,external_wp_i18n_.__)("Status"),
    name: "status",
    kind: "root",
    baseURL: "/wp/v2/statuses",
    baseURLParams: { context: "edit" },
    plural: "statuses",
    key: "slug"
  }
];
const deprecatedEntities = {
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
const additionalEntityConfigLoaders = [
  { kind: "postType", loadEntities: loadPostTypeEntities },
  { kind: "taxonomy", loadEntities: loadTaxonomyEntities },
  {
    kind: "root",
    name: "site",
    plural: "sites",
    loadEntities: loadSiteEntity
  }
];
const prePersistPostType = (persistedRecord, edits) => {
  const newEdits = {};
  if (persistedRecord?.status === "auto-draft") {
    if (!edits.status && !newEdits.status) {
      newEdits.status = "draft";
    }
    if ((!edits.title || edits.title === "Auto Draft") && !newEdits.title && (!persistedRecord?.title || persistedRecord?.title === "Auto Draft")) {
      newEdits.title = "";
    }
  }
  return newEdits;
};
async function loadPostTypeEntities() {
  const postTypes = await external_wp_apiFetch_default()({
    path: "/wp/v2/types?context=view"
  });
  return Object.entries(postTypes ?? {}).map(([name, postType]) => {
    const isTemplate = ["wp_template", "wp_template_part"].includes(
      name
    );
    const namespace = postType?.rest_namespace ?? "wp/v2";
    const syncConfig = {
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
       * Extract changes from a CRDT document that can be used to update the
       * local editor state.
       *
       * @param {import('@wordpress/sync').CRDTDoc}    crdtDoc
       * @param {import('@wordpress/sync').ObjectData} editedRecord
       * @return {Partial< import('@wordpress/sync').ObjectData >} Changes to record
       */
      getChangesFromCRDTDoc: (crdtDoc, editedRecord) => getPostChangesFromCRDTDoc(crdtDoc, editedRecord, postType),
      /**
       * Sync features supported by the entity.
       *
       * @type {Record< string, boolean >}
       */
      supports: {}
    };
    return {
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
      __unstablePrePersist: isTemplate ? void 0 : prePersistPostType,
      __unstable_rest_base: postType.rest_base,
      syncConfig,
      supportsPagination: true,
      getRevisionsUrl: (parentId, revisionId) => `/${namespace}/${postType.rest_base}/${parentId}/revisions${revisionId ? "/" + revisionId : ""}`,
      revisionKey: DEFAULT_ENTITY_KEY
    };
  });
}
async function loadTaxonomyEntities() {
  const taxonomies = await external_wp_apiFetch_default()({
    path: "/wp/v2/taxonomies?context=view"
  });
  return Object.entries(taxonomies ?? {}).map(([name, taxonomy]) => {
    const namespace = taxonomy?.rest_namespace ?? "wp/v2";
    return {
      kind: "taxonomy",
      baseURL: `/${namespace}/${taxonomy.rest_base}`,
      baseURLParams: { context: "edit" },
      name,
      label: taxonomy.name,
      getTitle: (record) => record?.name,
      supportsPagination: true
    };
  });
}
async function loadSiteEntity() {
  const entity = {
    label: (0,external_wp_i18n_.__)("Site"),
    name: "site",
    kind: "root",
    baseURL: "/wp/v2/settings",
    syncConfig: {
      applyChangesToCRDTDoc: defaultApplyChangesToCRDTDoc,
      getChangesFromCRDTDoc: defaultGetChangesFromCRDTDoc
    },
    meta: {}
  };
  const site = await external_wp_apiFetch_default()({
    path: entity.baseURL,
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
  return [{ ...entity, meta: { labels } }];
}
const getMethodName = (kind, name, prefix = "get") => {
  const kindPrefix = kind === "root" ? "" : (0,pascal_case_dist_es2015/* pascalCase */.fL)(kind);
  const suffix = (0,pascal_case_dist_es2015/* pascalCase */.fL)(name);
  return `${prefix}${kindPrefix}${suffix}`;
};



/***/ }),

/***/ 4997:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["blocks"];

/***/ }),

/***/ 5003:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ setNestedValue)
/* harmony export */ });
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



/***/ }),

/***/ 5101:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Z: () => (/* binding */ RECEIVE_INTERMEDIATE_RESULTS)
/* harmony export */ });
const RECEIVE_INTERMEDIATE_RESULTS = Symbol(
  "RECEIVE_INTERMEDIATE_RESULTS"
);



/***/ }),

/***/ 5469:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  Ay: () => (/* binding */ reducer_reducer_default)
});

// UNUSED EXPORTS: autosaves, blockPatternCategories, blockPatterns, currentGlobalStylesId, currentTheme, currentUser, defaultTemplates, editsReference, embedPreviews, entities, entitiesConfig, navigationFallbackId, registeredPostMeta, themeBaseGlobalStyles, themeGlobalStyleRevisions, themeGlobalStyleVariations, undoManager, userPatternCategories, userPermissions, users

// EXTERNAL MODULE: ./node_modules/fast-deep-equal/es6/index.js
var es6 = __webpack_require__(7734);
var es6_default = /*#__PURE__*/__webpack_require__.n(es6);
;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__(7143);
;// external ["wp","isShallowEqual"]
const external_wp_isShallowEqual_namespaceObject = window["wp"]["isShallowEqual"];
var external_wp_isShallowEqual_default = /*#__PURE__*/__webpack_require__.n(external_wp_isShallowEqual_namespaceObject);
;// ./node_modules/@wordpress/undo-manager/build-module/index.js

function mergeHistoryChanges(changes1, changes2) {
  const newChanges = { ...changes1 };
  Object.entries(changes2).forEach(([key, value]) => {
    if (newChanges[key]) {
      newChanges[key] = { ...newChanges[key], to: value.to };
    } else {
      newChanges[key] = value;
    }
  });
  return newChanges;
}
const addHistoryChangesIntoRecord = (record, changes) => {
  const existingChangesIndex = record?.findIndex(
    ({ id: recordIdentifier }) => {
      return typeof recordIdentifier === "string" ? recordIdentifier === changes.id : external_wp_isShallowEqual_default()(recordIdentifier, changes.id);
    }
  );
  const nextRecord = [...record];
  if (existingChangesIndex !== -1) {
    nextRecord[existingChangesIndex] = {
      id: changes.id,
      changes: mergeHistoryChanges(
        nextRecord[existingChangesIndex].changes,
        changes.changes
      )
    };
  } else {
    nextRecord.push(changes);
  }
  return nextRecord;
};
function createUndoManager() {
  let history = [];
  let stagedRecord = [];
  let offset = 0;
  const dropPendingRedos = () => {
    history = history.slice(0, offset || void 0);
    offset = 0;
  };
  const appendStagedRecordToLatestHistoryRecord = () => {
    const index = history.length === 0 ? 0 : history.length - 1;
    let latestRecord = history[index] ?? [];
    stagedRecord.forEach((changes) => {
      latestRecord = addHistoryChangesIntoRecord(latestRecord, changes);
    });
    stagedRecord = [];
    history[index] = latestRecord;
  };
  const isRecordEmpty = (record) => {
    const filteredRecord = record.filter(({ changes }) => {
      return Object.values(changes).some(
        ({ from, to }) => typeof from !== "function" && typeof to !== "function" && !external_wp_isShallowEqual_default()(from, to)
      );
    });
    return !filteredRecord.length;
  };
  return {
    addRecord(record, isStaged = false) {
      const isEmpty = !record || isRecordEmpty(record);
      if (isStaged) {
        if (isEmpty) {
          return;
        }
        record.forEach((changes) => {
          stagedRecord = addHistoryChangesIntoRecord(
            stagedRecord,
            changes
          );
        });
      } else {
        dropPendingRedos();
        if (stagedRecord.length) {
          appendStagedRecordToLatestHistoryRecord();
        }
        if (isEmpty) {
          return;
        }
        history.push(record);
      }
    },
    undo() {
      if (stagedRecord.length) {
        dropPendingRedos();
        appendStagedRecordToLatestHistoryRecord();
      }
      const undoRecord = history[history.length - 1 + offset];
      if (!undoRecord) {
        return;
      }
      offset -= 1;
      return undoRecord;
    },
    redo() {
      const redoRecord = history[history.length + offset];
      if (!redoRecord) {
        return;
      }
      offset += 1;
      return redoRecord;
    },
    hasUndo() {
      return !!history[history.length - 1 + offset];
    },
    hasRedo() {
      return !!history[history.length + offset];
    }
  };
}


;// ./node_modules/@wordpress/core-data/build-module/utils/if-matching-action.js
const ifMatchingAction = (isMatch) => (reducer) => (state, action) => {
  if (state === void 0 || isMatch(action)) {
    return reducer(state, action);
  }
  return state;
};
var if_matching_action_default = ifMatchingAction;


;// ./node_modules/@wordpress/core-data/build-module/utils/replace-action.js
const replaceAction = (replacer) => (reducer) => (state, action) => {
  return reducer(state, replacer(action));
};
var replace_action_default = replaceAction;


;// ./node_modules/@wordpress/core-data/build-module/utils/conservative-map-item.js

function conservativeMapItem(item, nextItem) {
  if (!item) {
    return nextItem;
  }
  let hasChanges = false;
  const result = {};
  for (const key in nextItem) {
    if (es6_default()(item[key], nextItem[key])) {
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


;// ./node_modules/@wordpress/core-data/build-module/utils/on-sub-key.js
const onSubKey = (actionProperty) => (reducer) => (state = {}, action) => {
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


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/entities.js + 32 modules
var entities = __webpack_require__(4767);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/queried-data/get-query-parts.js + 1 modules
var get_query_parts = __webpack_require__(4027);
;// ./node_modules/@wordpress/core-data/build-module/queried-data/reducer.js





function getContextFromAction(action) {
  const { query } = action;
  if (!query) {
    return "default";
  }
  const queryParts = (0,get_query_parts/* default */.A)(query);
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
function removeEntitiesById(entities, ids) {
  return Object.fromEntries(
    Object.entries(entities).filter(
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
      const key = action.key || entities/* DEFAULT_ENTITY_KEY */.C_;
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
      const { query, key = entities/* DEFAULT_ENTITY_KEY */.C_ } = action;
      const queryParts = query ? (0,get_query_parts/* default */.A)(query) : {};
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
const receiveQueries = (0,external_wp_compose_namespaceObject.compose)([
  // Limit to matching action type so we don't attempt to replace action on
  // an unhandled action.
  if_matching_action_default((action) => "query" in action),
  // Inject query parts into action for use both in `onSubKey` and reducer.
  replace_action_default((action) => {
    if (action.query) {
      return {
        ...action,
        ...(0,get_query_parts/* default */.A)(action.query)
      };
    }
    return action;
  }),
  on_sub_key_default("context"),
  // Queries shape is shared, but keyed by query `stableKey` part. Original
  // reducer tracks only a single query object.
  on_sub_key_default("stableKey")
])((state = {}, action) => {
  const { type, page, perPage, key = entities/* DEFAULT_ENTITY_KEY */.C_ } = action;
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
const queries = (state = {}, action) => {
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
var reducer_default = (0,external_wp_data_.combineReducers)({
  items,
  itemIsComplete,
  queries
});


;// ./node_modules/@wordpress/core-data/build-module/reducer.js







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
const withMultiEntityRecordEdits = (reducer) => (state, action) => {
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
  return (0,external_wp_compose_namespaceObject.compose)([
    withMultiEntityRecordEdits,
    // Limit to matching action type so we don't attempt to replace action on
    // an unhandled action.
    if_matching_action_default(
      (action) => action.name && action.kind && action.name === entityConfig.name && action.kind === entityConfig.kind
    ),
    // Inject the entity config into the action.
    replace_action_default((action) => {
      return {
        key: entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_,
        ...action
      };
    })
  ])(
    (0,external_wp_data_.combineReducers)({
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
                    !es6_default()(
                      edits[key],
                      record[key]?.raw ?? record[key]
                    ) && // Sometimes the server alters the sent value which means
                    // we need to also remove the edits before the api request.
                    (!action.persistedEdits || !es6_default()(
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
function entitiesConfig(state = entities/* rootEntitiesConfig */.Mr, action) {
  switch (action.type) {
    case "ADD_ENTITIES":
      return [...state, ...action.entities];
  }
  return state;
}
const reducer_entities = (state = {}, action) => {
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
    entitiesDataReducer = (0,external_wp_data_.combineReducers)(
      Object.fromEntries(
        Object.entries(entitiesByKind).map(
          ([kind, subEntities]) => {
            const kindReducer = (0,external_wp_data_.combineReducers)(
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
function undoManager(state = createUndoManager()) {
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
var reducer_reducer_default = (0,external_wp_data_.combineReducers)({
  users,
  currentTheme,
  currentGlobalStylesId,
  currentUser,
  themeGlobalStyleVariations,
  themeBaseGlobalStyles,
  themeGlobalStyleRevisions,
  entities: reducer_entities,
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
  registeredPostMeta
});



/***/ }),

/***/ 5663:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   xQ: () => (/* binding */ camelCase)
/* harmony export */ });
/* unused harmony exports camelCaseTransform, camelCaseTransformMerge */
/* harmony import */ var tslib__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(1635);
/* harmony import */ var pascal_case__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(287);


function camelCaseTransform(input, index) {
    if (index === 0)
        return input.toLowerCase();
    return (0,pascal_case__WEBPACK_IMPORTED_MODULE_0__/* .pascalCaseTransform */ .l3)(input, index);
}
function camelCaseTransformMerge(input, index) {
    if (index === 0)
        return input.toLowerCase();
    return pascalCaseTransformMerge(input);
}
function camelCase(input, options) {
    if (options === void 0) { options = {}; }
    return (0,pascal_case__WEBPACK_IMPORTED_MODULE_0__/* .pascalCase */ .fL)(input, (0,tslib__WEBPACK_IMPORTED_MODULE_1__/* .__assign */ .Cl)({ transform: camelCaseTransform }, options));
}


/***/ }),

/***/ 6087:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["element"];

/***/ }),

/***/ 6378:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  s: () => (/* binding */ lock),
  T: () => (/* binding */ unlock)
});

;// external ["wp","privateApis"]
const external_wp_privateApis_namespaceObject = window["wp"]["privateApis"];
;// ./node_modules/@wordpress/core-data/build-module/lock-unlock.js

const { lock, unlock } = (0,external_wp_privateApis_namespaceObject.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
  "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
  "@wordpress/core-data"
);



/***/ }),

/***/ 6384:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  __experimentalGetCurrentGlobalStylesId: () => (/* binding */ __experimentalGetCurrentGlobalStylesId),
  __experimentalGetCurrentThemeBaseGlobalStyles: () => (/* binding */ __experimentalGetCurrentThemeBaseGlobalStyles),
  __experimentalGetCurrentThemeGlobalStylesVariations: () => (/* binding */ __experimentalGetCurrentThemeGlobalStylesVariations),
  canUser: () => (/* binding */ canUser),
  canUserEditEntityRecord: () => (/* binding */ canUserEditEntityRecord),
  getAuthors: () => (/* binding */ getAuthors),
  getAutosave: () => (/* binding */ getAutosave),
  getAutosaves: () => (/* binding */ getAutosaves),
  getBlockPatternCategories: () => (/* binding */ getBlockPatternCategories),
  getBlockPatterns: () => (/* binding */ getBlockPatterns),
  getCurrentTheme: () => (/* binding */ getCurrentTheme),
  getCurrentThemeGlobalStylesRevisions: () => (/* binding */ getCurrentThemeGlobalStylesRevisions),
  getCurrentUser: () => (/* binding */ getCurrentUser),
  getDefaultTemplateId: () => (/* binding */ getDefaultTemplateId),
  getEditedEntityRecord: () => (/* binding */ getEditedEntityRecord),
  getEmbedPreview: () => (/* binding */ getEmbedPreview),
  getEntitiesConfig: () => (/* binding */ getEntitiesConfig),
  getEntityRecord: () => (/* binding */ getEntityRecord),
  getEntityRecords: () => (/* binding */ getEntityRecords),
  getEntityRecordsTotalItems: () => (/* binding */ getEntityRecordsTotalItems),
  getEntityRecordsTotalPages: () => (/* binding */ getEntityRecordsTotalPages),
  getNavigationFallbackId: () => (/* binding */ getNavigationFallbackId),
  getRawEntityRecord: () => (/* binding */ getRawEntityRecord),
  getRegisteredPostMeta: () => (/* binding */ getRegisteredPostMeta),
  getRevision: () => (/* binding */ getRevision),
  getRevisions: () => (/* binding */ getRevisions),
  getThemeSupports: () => (/* binding */ getThemeSupports),
  getUserPatternCategories: () => (/* binding */ getUserPatternCategories)
});

// EXTERNAL MODULE: ./node_modules/camel-case/dist.es2015/index.js
var dist_es2015 = __webpack_require__(5663);
// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__(3832);
// EXTERNAL MODULE: external ["wp","htmlEntities"]
var external_wp_htmlEntities_ = __webpack_require__(8537);
// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__(1455);
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/name.js
var build_module_name = __webpack_require__(2278);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/entities.js + 32 modules
var entities = __webpack_require__(4767);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/get-normalized-comma-separable.js
var get_normalized_comma_separable = __webpack_require__(533);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/user-permissions.js
var user_permissions = __webpack_require__(2577);
;// ./node_modules/@wordpress/core-data/build-module/utils/forward-resolver.js
const forwardResolver = (resolverName) => (...args) => async ({ resolveSelect }) => {
  await resolveSelect[resolverName](...args);
};
var forward_resolver_default = forwardResolver;


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/receive-intermediate-results.js
var receive_intermediate_results = __webpack_require__(5101);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/fetch/index.js + 2 modules
var fetch = __webpack_require__(7006);
;// ./node_modules/@wordpress/core-data/build-module/resolvers.js









const getAuthors = (query) => async ({ dispatch }) => {
  const path = (0,external_wp_url_.addQueryArgs)(
    "/wp/v2/users/?who=authors&per_page=100",
    query
  );
  const users = await external_wp_apiFetch_default()({ path });
  dispatch.receiveUserQuery(path, users);
};
const getCurrentUser = () => async ({ dispatch }) => {
  const currentUser = await external_wp_apiFetch_default()({ path: "/wp/v2/users/me" });
  dispatch.receiveCurrentUser(currentUser);
};
const getEntityRecord = (kind, name, key = "", query) => async ({ select, dispatch, registry, resolveSelect }) => {
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.name === name && config.kind === kind
  );
  if (!entityConfig) {
    return;
  }
  const lock = await dispatch.__unstableAcquireStoreLock(
    build_module_name/* STORE_NAME */.E,
    ["entities", "records", kind, name, key],
    { exclusive: false }
  );
  try {
    if (query !== void 0 && query._fields) {
      query = {
        ...query,
        _fields: [
          .../* @__PURE__ */ new Set([
            ...(0,get_normalized_comma_separable/* default */.A)(query._fields) || [],
            entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_
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
    if (kind === "postType" && name === "wp_template" && key && typeof key === "string" && !/^\d+$/.test(key)) {
      baseURL = baseURL.slice(0, baseURL.lastIndexOf("/")) + "/templates";
    }
    const path = (0,external_wp_url_.addQueryArgs)(baseURL + (key ? "/" + key : ""), {
      ...entityConfig.baseURLParams,
      ...query
    });
    const response = await external_wp_apiFetch_default()({ path, parse: false });
    const record = await response.json();
    const permissions = (0,user_permissions/* getUserPermissionsFromAllowHeader */.qY)(
      response.headers?.get("allow")
    );
    const canUserResolutionsArgs = [];
    const receiveUserPermissionArgs = {};
    for (const action of user_permissions/* ALLOWED_RESOURCE_ACTIONS */.CO) {
      receiveUserPermissionArgs[(0,user_permissions/* getUserPermissionCacheKey */.kC)(action, {
        kind,
        name,
        id: key
      })] = permissions[action];
      canUserResolutionsArgs.push([
        action,
        { kind, name, id: key }
      ]);
    }
    if (window.__experimentalEnableSync && entityConfig.syncConfig && !query) {
      if (false) {}
    }
    registry.batch(() => {
      dispatch.receiveEntityRecords(kind, name, record, query);
      dispatch.receiveUserPermissions(receiveUserPermissionArgs);
      dispatch.finishResolutions("canUser", canUserResolutionsArgs);
    });
  } finally {
    dispatch.__unstableReleaseStoreLock(lock);
  }
};
getEntityRecord.shouldInvalidate = (action, kind, name) => {
  return kind === "root" && name === "site" && (action.type === "RECEIVE_ITEMS" && // Making sure persistedEdits is set seems to be the only way of
  // knowing whether it's an update or fetch. Only an update would
  // have persistedEdits.
  action.persistedEdits && action.persistedEdits.status !== "auto-draft" || action.type === "REMOVE_ITEMS") && action.kind === "postType" && action.name === "wp_template";
};
const getRawEntityRecord = forward_resolver_default("getEntityRecord");
const getEditedEntityRecord = forward_resolver_default("getEntityRecord");
const getEntityRecords = (kind, name, query = {}) => async ({ dispatch, registry, resolveSelect }) => {
  const configs = await resolveSelect.getEntitiesConfig(kind);
  const entityConfig = configs.find(
    (config) => config.name === name && config.kind === kind
  );
  if (!entityConfig) {
    return;
  }
  const lock = await dispatch.__unstableAcquireStoreLock(
    build_module_name/* STORE_NAME */.E,
    ["entities", "records", kind, name],
    { exclusive: false }
  );
  const rawQuery = { ...query };
  const key = entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_;
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
            ...(0,get_normalized_comma_separable/* default */.A)(query._fields) || [],
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
    const path = (0,external_wp_url_.addQueryArgs)(baseURL, {
      ...entityConfig.baseURLParams,
      ...query
    });
    let records = [], meta;
    if (entityConfig.supportsPagination && query.per_page !== -1) {
      const response = await external_wp_apiFetch_default()({ path, parse: false });
      records = Object.values(await response.json());
      meta = {
        totalItems: parseInt(
          response.headers.get("X-WP-Total")
        ),
        totalPages: parseInt(
          response.headers.get("X-WP-TotalPages")
        )
      };
    } else if (query.per_page === -1 && query[receive_intermediate_results/* RECEIVE_INTERMEDIATE_RESULTS */.Z] === true) {
      let page = 1;
      let totalPages;
      do {
        const response = await external_wp_apiFetch_default()({
          path: (0,external_wp_url_.addQueryArgs)(path, { page, per_page: 100 }),
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
      records = Object.values(await external_wp_apiFetch_default()({ path }));
      meta = {
        totalItems: records.length,
        totalPages: 1
      };
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
        permissions: (0,user_permissions/* getUserPermissionsFromAllowHeader */.qY)(
          record._links.self[0].targetHints.allow
        )
      }));
      const canUserResolutionsArgs = [];
      const receiveUserPermissionArgs = {};
      for (const targetHint of targetHints) {
        for (const action of user_permissions/* ALLOWED_RESOURCE_ACTIONS */.CO) {
          canUserResolutionsArgs.push([
            action,
            { kind, name, id: targetHint.id }
          ]);
          receiveUserPermissionArgs[(0,user_permissions/* getUserPermissionCacheKey */.kC)(action, {
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
      dispatch.__unstableReleaseStoreLock(lock);
    });
  } catch (e) {
    dispatch.__unstableReleaseStoreLock(lock);
  }
};
getEntityRecords.shouldInvalidate = (action, kind, name) => {
  return (action.type === "RECEIVE_ITEMS" || action.type === "REMOVE_ITEMS") && action.invalidateCache && kind === action.kind && name === action.name;
};
const getEntityRecordsTotalItems = forward_resolver_default("getEntityRecords");
const getEntityRecordsTotalPages = forward_resolver_default("getEntityRecords");
const getCurrentTheme = () => async ({ dispatch, resolveSelect }) => {
  const activeThemes = await resolveSelect.getEntityRecords(
    "root",
    "theme",
    { status: "active" }
  );
  dispatch.receiveCurrentTheme(activeThemes[0]);
};
const getThemeSupports = forward_resolver_default("getCurrentTheme");
const getEmbedPreview = (url) => async ({ dispatch }) => {
  try {
    const embedProxyResponse = await external_wp_apiFetch_default()({
      path: (0,external_wp_url_.addQueryArgs)("/oembed/1.0/proxy", { url })
    });
    dispatch.receiveEmbedPreview(url, embedProxyResponse);
  } catch (error) {
    dispatch.receiveEmbedPreview(url, false);
  }
};
const canUser = (requestedAction, resource, id) => async ({ dispatch, registry, resolveSelect }) => {
  if (!user_permissions/* ALLOWED_RESOURCE_ACTIONS */.CO.includes(requestedAction)) {
    throw new Error(`'${requestedAction}' is not a valid action.`);
  }
  const { hasStartedResolution } = registry.select(build_module_name/* STORE_NAME */.E);
  for (const relatedAction of user_permissions/* ALLOWED_RESOURCE_ACTIONS */.CO) {
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
    resourcePath = `/wp/v2/${resource}` + (id ? "/" + id : "");
  }
  let response;
  try {
    response = await external_wp_apiFetch_default()({
      path: resourcePath,
      method: "OPTIONS",
      parse: false
    });
  } catch (error) {
    return;
  }
  const permissions = (0,user_permissions/* getUserPermissionsFromAllowHeader */.qY)(
    response.headers?.get("allow")
  );
  registry.batch(() => {
    for (const action of user_permissions/* ALLOWED_RESOURCE_ACTIONS */.CO) {
      const key = (0,user_permissions/* getUserPermissionCacheKey */.kC)(action, resource, id);
      dispatch.receiveUserPermission(key, permissions[action]);
      if (action !== requestedAction) {
        dispatch.finishResolution("canUser", [
          action,
          resource,
          id
        ]);
      }
    }
  });
};
const canUserEditEntityRecord = (kind, name, recordId) => async ({ dispatch }) => {
  await dispatch(canUser("update", { kind, name, id: recordId }));
};
const getAutosaves = (postType, postId) => async ({ dispatch, resolveSelect }) => {
  const {
    rest_base: restBase,
    rest_namespace: restNamespace = "wp/v2",
    supports
  } = await resolveSelect.getPostType(postType);
  if (!supports?.autosave) {
    return;
  }
  const autosaves = await external_wp_apiFetch_default()({
    path: `/${restNamespace}/${restBase}/${postId}/autosaves?context=edit`
  });
  if (autosaves && autosaves.length) {
    dispatch.receiveAutosaves(postId, autosaves);
  }
};
const getAutosave = (postType, postId) => async ({ resolveSelect }) => {
  await resolveSelect.getAutosaves(postType, postId);
};
const __experimentalGetCurrentGlobalStylesId = () => async ({ dispatch, resolveSelect }) => {
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
  const id = matches ? Number(matches[1]) : null;
  if (id) {
    dispatch.__experimentalReceiveCurrentGlobalStylesId(id);
  }
};
const __experimentalGetCurrentThemeBaseGlobalStyles = () => async ({ resolveSelect, dispatch }) => {
  const currentTheme = await resolveSelect.getCurrentTheme();
  const themeGlobalStyles = await external_wp_apiFetch_default()({
    path: `/wp/v2/global-styles/themes/${currentTheme.stylesheet}?context=view`
  });
  dispatch.__experimentalReceiveThemeBaseGlobalStyles(
    currentTheme.stylesheet,
    themeGlobalStyles
  );
};
const __experimentalGetCurrentThemeGlobalStylesVariations = () => async ({ resolveSelect, dispatch }) => {
  const currentTheme = await resolveSelect.getCurrentTheme();
  const variations = await external_wp_apiFetch_default()({
    path: `/wp/v2/global-styles/themes/${currentTheme.stylesheet}/variations?context=view`
  });
  dispatch.__experimentalReceiveThemeGlobalStyleVariations(
    currentTheme.stylesheet,
    variations
  );
};
const getCurrentThemeGlobalStylesRevisions = () => async ({ resolveSelect, dispatch }) => {
  const globalStylesId = await resolveSelect.__experimentalGetCurrentGlobalStylesId();
  const record = globalStylesId ? await resolveSelect.getEntityRecord(
    "root",
    "globalStyles",
    globalStylesId
  ) : void 0;
  const revisionsURL = record?._links?.["version-history"]?.[0]?.href;
  if (revisionsURL) {
    const resetRevisions = await external_wp_apiFetch_default()({
      url: revisionsURL
    });
    const revisions = resetRevisions?.map(
      (revision) => Object.fromEntries(
        Object.entries(revision).map(([key, value]) => [
          (0,dist_es2015/* camelCase */.xQ)(key),
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
getCurrentThemeGlobalStylesRevisions.shouldInvalidate = (action) => {
  return action.type === "SAVE_ENTITY_RECORD_FINISH" && action.kind === "root" && !action.error && action.name === "globalStyles";
};
const getBlockPatterns = () => async ({ dispatch }) => {
  const patterns = await (0,fetch/* fetchBlockPatterns */.l$)();
  dispatch({ type: "RECEIVE_BLOCK_PATTERNS", patterns });
};
const getBlockPatternCategories = () => async ({ dispatch }) => {
  const categories = await external_wp_apiFetch_default()({
    path: "/wp/v2/block-patterns/categories"
  });
  dispatch({ type: "RECEIVE_BLOCK_PATTERN_CATEGORIES", categories });
};
const getUserPatternCategories = () => async ({ dispatch, resolveSelect }) => {
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
    label: (0,external_wp_htmlEntities_.decodeEntities)(userCategory.name),
    name: userCategory.slug
  })) || [];
  dispatch({
    type: "RECEIVE_USER_PATTERN_CATEGORIES",
    patternCategories: mappedPatternCategories
  });
};
const getNavigationFallbackId = () => async ({ dispatch, select, registry }) => {
  const fallback = await external_wp_apiFetch_default()({
    path: (0,external_wp_url_.addQueryArgs)("/wp-block-editor/v1/navigation-fallback", {
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
const getDefaultTemplateId = (query) => async ({ dispatch, registry, resolveSelect }) => {
  const template = await external_wp_apiFetch_default()({
    path: (0,external_wp_url_.addQueryArgs)("/wp/v2/templates/lookup", query)
  });
  await resolveSelect.getEntitiesConfig("postType");
  const id = template?.wp_id || template?.id;
  if (id) {
    template.id = id;
    template.type = typeof id === "string" ? "wp_registered_template" : "wp_template";
    registry.batch(() => {
      dispatch.receiveDefaultTemplateId(query, id);
      dispatch.receiveEntityRecords("postType", template.type, [
        template
      ]);
      dispatch.finishResolution("getEntityRecord", [
        "postType",
        template.type,
        id
      ]);
    });
  }
};
getDefaultTemplateId.shouldInvalidate = (action) => {
  return action.type === "RECEIVE_ITEMS" && action.kind === "root" && action.name === "site";
};
const getRevisions = (kind, name, recordKey, query = {}) => async ({ dispatch, registry, resolveSelect }) => {
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
          ...(0,get_normalized_comma_separable/* default */.A)(query._fields) || [],
          entityConfig.revisionKey || entities/* DEFAULT_ENTITY_KEY */.C_
        ])
      ].join()
    };
  }
  const path = (0,external_wp_url_.addQueryArgs)(
    entityConfig.getRevisionsUrl(recordKey),
    query
  );
  let records, response;
  const meta = {};
  const isPaginated = entityConfig.supportsPagination && query.per_page !== -1;
  try {
    response = await external_wp_apiFetch_default()({ path, parse: !isPaginated });
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
        const key = entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_;
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
getRevisions.shouldInvalidate = (action, kind, name, recordKey) => action.type === "SAVE_ENTITY_RECORD_FINISH" && name === action.name && kind === action.kind && !action.error && recordKey === action.recordId;
const getRevision = (kind, name, recordKey, revisionKey, query) => async ({ dispatch, resolveSelect }) => {
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
          ...(0,get_normalized_comma_separable/* default */.A)(query._fields) || [],
          entityConfig.revisionKey || entities/* DEFAULT_ENTITY_KEY */.C_
        ])
      ].join()
    };
  }
  const path = (0,external_wp_url_.addQueryArgs)(
    entityConfig.getRevisionsUrl(recordKey, revisionKey),
    query
  );
  let record;
  try {
    record = await external_wp_apiFetch_default()({ path });
  } catch (error) {
    return;
  }
  if (record) {
    dispatch.receiveRevisions(kind, name, recordKey, record, query);
  }
};
const getRegisteredPostMeta = (postType) => async ({ dispatch, resolveSelect }) => {
  let options;
  try {
    const {
      rest_namespace: restNamespace = "wp/v2",
      rest_base: restBase
    } = await resolveSelect.getPostType(postType) || {};
    options = await external_wp_apiFetch_default()({
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
const getEntitiesConfig = (kind) => async ({ dispatch }) => {
  const loader = entities/* additionalEntityConfigLoaders */.L2.find(
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



/***/ }),

/***/ 7006:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  Y3: () => (/* reexport */ fetchLinkSuggestions),
  gr: () => (/* reexport */ experimental_fetch_url_data_default),
  l$: () => (/* binding */ fetchBlockPatterns)
});

// EXTERNAL MODULE: ./node_modules/camel-case/dist.es2015/index.js
var dist_es2015 = __webpack_require__(5663);
// EXTERNAL MODULE: external ["wp","apiFetch"]
var external_wp_apiFetch_ = __webpack_require__(1455);
var external_wp_apiFetch_default = /*#__PURE__*/__webpack_require__.n(external_wp_apiFetch_);
// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__(3832);
// EXTERNAL MODULE: external ["wp","htmlEntities"]
var external_wp_htmlEntities_ = __webpack_require__(8537);
// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__(7723);
;// ./node_modules/@wordpress/core-data/build-module/fetch/__experimental-fetch-link-suggestions.js




async function fetchLinkSuggestions(search, searchOptions = {}, editorSettings = {}) {
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
  const { disablePostFormats = false } = editorSettings;
  const queries = [];
  if (!type || type === "post") {
    queries.push(
      external_wp_apiFetch_default()({
        path: (0,external_wp_url_.addQueryArgs)("/wp/v2/search", {
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
            title: (0,external_wp_htmlEntities_.decodeEntities)(result.title || "") || (0,external_wp_i18n_.__)("(no title)"),
            type: result.subtype || result.type,
            kind: "post-type"
          };
        });
      }).catch(() => [])
      // Fail by returning no results.
    );
  }
  if (!type || type === "term") {
    queries.push(
      external_wp_apiFetch_default()({
        path: (0,external_wp_url_.addQueryArgs)("/wp/v2/search", {
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
            title: (0,external_wp_htmlEntities_.decodeEntities)(result.title || "") || (0,external_wp_i18n_.__)("(no title)"),
            type: result.subtype || result.type,
            kind: "taxonomy"
          };
        });
      }).catch(() => [])
      // Fail by returning no results.
    );
  }
  if (!disablePostFormats && (!type || type === "post-format")) {
    queries.push(
      external_wp_apiFetch_default()({
        path: (0,external_wp_url_.addQueryArgs)("/wp/v2/search", {
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
            title: (0,external_wp_htmlEntities_.decodeEntities)(result.title || "") || (0,external_wp_i18n_.__)("(no title)"),
            type: result.subtype || result.type,
            kind: "taxonomy"
          };
        });
      }).catch(() => [])
      // Fail by returning no results.
    );
  }
  if (!type || type === "attachment") {
    queries.push(
      external_wp_apiFetch_default()({
        path: (0,external_wp_url_.addQueryArgs)("/wp/v2/media", {
          search,
          page,
          per_page: perPage
        })
      }).then((results2) => {
        return results2.map((result) => {
          return {
            id: result.id,
            url: result.source_url,
            title: (0,external_wp_htmlEntities_.decodeEntities)(result.title.rendered || "") || (0,external_wp_i18n_.__)("(no title)"),
            type: result.type,
            kind: "media"
          };
        });
      }).catch(() => [])
      // Fail by returning no results.
    );
  }
  const responses = await Promise.all(queries);
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


;// ./node_modules/@wordpress/core-data/build-module/fetch/__experimental-fetch-url-data.js


const CACHE = /* @__PURE__ */ new Map();
const fetchUrlData = async (url, options = {}) => {
  const endpoint = "/wp-block-editor/v1/url-details";
  const args = {
    url: (0,external_wp_url_.prependHTTP)(url)
  };
  if (!(0,external_wp_url_.isURL)(url)) {
    return Promise.reject(`${url} is not a valid URL.`);
  }
  const protocol = (0,external_wp_url_.getProtocol)(url);
  if (!protocol || !(0,external_wp_url_.isValidProtocol)(protocol) || !protocol.startsWith("http") || !/^https?:\/\/[^\/\s]/i.test(url)) {
    return Promise.reject(
      `${url} does not have a valid protocol. URLs must be "http" based`
    );
  }
  if (CACHE.has(url)) {
    return CACHE.get(url);
  }
  return external_wp_apiFetch_default()({
    path: (0,external_wp_url_.addQueryArgs)(endpoint, args),
    ...options
  }).then((res) => {
    CACHE.set(url, res);
    return res;
  });
};
var experimental_fetch_url_data_default = fetchUrlData;


;// ./node_modules/@wordpress/core-data/build-module/fetch/index.js




async function fetchBlockPatterns() {
  const restPatterns = await external_wp_apiFetch_default()({
    path: "/wp/v2/block-patterns/patterns"
  });
  if (!restPatterns) {
    return [];
  }
  return restPatterns.map(
    (pattern) => Object.fromEntries(
      Object.entries(pattern).map(([key, value]) => [
        (0,dist_es2015/* camelCase */.xQ)(key),
        value
      ])
    )
  );
}



/***/ }),

/***/ 7078:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   Ay: () => (/* binding */ useEntityRecords),
/* harmony export */   bM: () => (/* binding */ __experimentalUseEntityRecords),
/* harmony export */   pU: () => (/* binding */ useEntityRecordsWithPermissions)
/* harmony export */ });
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(3832);
/* harmony import */ var _wordpress_url__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_url__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(4040);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1__);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(7143);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_2___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_2__);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(6087);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_3___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_3__);
/* harmony import */ var _use_query_select__WEBPACK_IMPORTED_MODULE_5__ = __webpack_require__(7541);
/* harmony import */ var ___WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(4565);
/* harmony import */ var _lock_unlock__WEBPACK_IMPORTED_MODULE_7__ = __webpack_require__(6378);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_6__ = __webpack_require__(533);








const EMPTY_ARRAY = [];
function useEntityRecords(kind, name, queryArgs = {}, options = { enabled: true }) {
  const queryAsString = (0,_wordpress_url__WEBPACK_IMPORTED_MODULE_0__.addQueryArgs)("", queryArgs);
  const { data: records, ...rest } = (0,_use_query_select__WEBPACK_IMPORTED_MODULE_5__/* ["default"] */ .A)(
    (query) => {
      if (!options.enabled) {
        return {
          // Avoiding returning a new reference on every execution.
          data: EMPTY_ARRAY
        };
      }
      return query(___WEBPACK_IMPORTED_MODULE_4__.store).getEntityRecords(kind, name, queryArgs);
    },
    [kind, name, queryAsString, options.enabled]
  );
  const { totalItems, totalPages } = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(
    (select) => {
      if (!options.enabled) {
        return {
          totalItems: null,
          totalPages: null
        };
      }
      return {
        totalItems: select(___WEBPACK_IMPORTED_MODULE_4__.store).getEntityRecordsTotalItems(
          kind,
          name,
          queryArgs
        ),
        totalPages: select(___WEBPACK_IMPORTED_MODULE_4__.store).getEntityRecordsTotalPages(
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
  _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_1___default()(`wp.data.__experimentalUseEntityRecords`, {
    alternative: "wp.data.useEntityRecords",
    since: "6.1"
  });
  return useEntityRecords(kind, name, queryArgs, options);
}
function useEntityRecordsWithPermissions(kind, name, queryArgs = {}, options = { enabled: true }) {
  const entityConfig = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(
    (select) => select(___WEBPACK_IMPORTED_MODULE_4__.store).getEntityConfig(kind, name),
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
            ...(0,_utils__WEBPACK_IMPORTED_MODULE_6__/* ["default"] */ .A)(
              queryArgs._fields
            ) || [],
            "_links"
          ])
        ].join()
      } : {}
    },
    options
  );
  const ids = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useMemo)(
    () => data?.map(
      // @ts-ignore
      (record) => record[entityConfig?.key ?? "id"]
    ) ?? [],
    [data, entityConfig?.key]
  );
  const permissions = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_2__.useSelect)(
    (select) => {
      const { getEntityRecordsPermissions } = (0,_lock_unlock__WEBPACK_IMPORTED_MODULE_7__/* .unlock */ .T)(
        select(___WEBPACK_IMPORTED_MODULE_4__.store)
      );
      return getEntityRecordsPermissions(kind, name, ids);
    },
    [ids, kind, name]
  );
  const dataWithPermissions = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_3__.useMemo)(
    () => data?.map((record, index) => ({
      // @ts-ignore
      ...record,
      permissions: permissions[index]
    })) ?? [],
    [data, permissions]
  );
  return { records: dataWithPermissions, ...ret };
}



/***/ }),

/***/ 7143:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["data"];

/***/ }),

/***/ 7314:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   g: () => (/* binding */ lowerCase)
/* harmony export */ });
/* unused harmony export localeLowerCase */
/**
 * Source: ftp://ftp.unicode.org/Public/UCD/latest/ucd/SpecialCasing.txt
 */
var SUPPORTED_LOCALE = {
    tr: {
        regexp: /\u0130|\u0049|\u0049\u0307/g,
        map: {
            İ: "\u0069",
            I: "\u0131",
            İ: "\u0069",
        },
    },
    az: {
        regexp: /\u0130/g,
        map: {
            İ: "\u0069",
            I: "\u0131",
            İ: "\u0069",
        },
    },
    lt: {
        regexp: /\u0049|\u004A|\u012E|\u00CC|\u00CD|\u0128/g,
        map: {
            I: "\u0069\u0307",
            J: "\u006A\u0307",
            Į: "\u012F\u0307",
            Ì: "\u0069\u0307\u0300",
            Í: "\u0069\u0307\u0301",
            Ĩ: "\u0069\u0307\u0303",
        },
    },
};
/**
 * Localized lower case.
 */
function localeLowerCase(str, locale) {
    var lang = SUPPORTED_LOCALE[locale.toLowerCase()];
    if (lang)
        return lowerCase(str.replace(lang.regexp, function (m) { return lang.map[m]; }));
    return lowerCase(str);
}
/**
 * Lower case as a function.
 */
function lowerCase(str) {
    return str.toLowerCase();
}


/***/ }),

/***/ 7541:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  A: () => (/* binding */ useQuerySelect)
});

// UNUSED EXPORTS: META_SELECTORS

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__(7143);
;// ./node_modules/memize/dist/index.js
/**
 * Memize options object.
 *
 * @typedef MemizeOptions
 *
 * @property {number} [maxSize] Maximum size of the cache.
 */

/**
 * Internal cache entry.
 *
 * @typedef MemizeCacheNode
 *
 * @property {?MemizeCacheNode|undefined} [prev] Previous node.
 * @property {?MemizeCacheNode|undefined} [next] Next node.
 * @property {Array<*>}                   args   Function arguments for cache
 *                                               entry.
 * @property {*}                          val    Function result.
 */

/**
 * Properties of the enhanced function for controlling cache.
 *
 * @typedef MemizeMemoizedFunction
 *
 * @property {()=>void} clear Clear the cache.
 */

/**
 * Accepts a function to be memoized, and returns a new memoized function, with
 * optional options.
 *
 * @template {(...args: any[]) => any} F
 *
 * @param {F}             fn        Function to memoize.
 * @param {MemizeOptions} [options] Options object.
 *
 * @return {((...args: Parameters<F>) => ReturnType<F>) & MemizeMemoizedFunction} Memoized function.
 */
function memize(fn, options) {
	var size = 0;

	/** @type {?MemizeCacheNode|undefined} */
	var head;

	/** @type {?MemizeCacheNode|undefined} */
	var tail;

	options = options || {};

	function memoized(/* ...args */) {
		var node = head,
			len = arguments.length,
			args,
			i;

		searchCache: while (node) {
			// Perform a shallow equality test to confirm that whether the node
			// under test is a candidate for the arguments passed. Two arrays
			// are shallowly equal if their length matches and each entry is
			// strictly equal between the two sets. Avoid abstracting to a
			// function which could incur an arguments leaking deoptimization.

			// Check whether node arguments match arguments length
			if (node.args.length !== arguments.length) {
				node = node.next;
				continue;
			}

			// Check whether node arguments match arguments values
			for (i = 0; i < len; i++) {
				if (node.args[i] !== arguments[i]) {
					node = node.next;
					continue searchCache;
				}
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if (node !== head) {
				// As tail, shift to previous. Must only shift if not also
				// head, since if both head and tail, there is no previous.
				if (node === tail) {
					tail = node.prev;
				}

				// Adjust siblings to point to each other. If node was tail,
				// this also handles new tail's empty `next` assignment.
				/** @type {MemizeCacheNode} */ (node.prev).next = node.next;
				if (node.next) {
					node.next.prev = node.prev;
				}

				node.next = head;
				node.prev = null;
				/** @type {MemizeCacheNode} */ (head).prev = node;
				head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		// Create a copy of arguments (avoid leaking deoptimization)
		args = new Array(len);
		for (i = 0; i < len; i++) {
			args[i] = arguments[i];
		}

		node = {
			args: args,

			// Generate the result from original function
			val: fn.apply(null, args),
		};

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if (head) {
			head.prev = node;
			node.next = head;
		} else {
			// If no head, follows that there's no tail (at initial or reset)
			tail = node;
		}

		// Trim tail if we're reached max size and are pending cache insertion
		if (size === /** @type {MemizeOptions} */ (options).maxSize) {
			tail = /** @type {MemizeCacheNode} */ (tail).prev;
			/** @type {MemizeCacheNode} */ (tail).next = null;
		} else {
			size++;
		}

		head = node;

		return node.val;
	}

	memoized.clear = function () {
		head = null;
		tail = null;
		size = 0;
	};

	// Ignore reason: There's not a clear solution to create an intersection of
	// the function with additional properties, where the goal is to retain the
	// function signature of the incoming argument and add control properties
	// on the return value.

	// @ts-ignore
	return memoized;
}



;// ./node_modules/@wordpress/core-data/build-module/hooks/memoize.js

var memoize_default = memize;


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/hooks/constants.js
var constants = __webpack_require__(2859);
;// ./node_modules/@wordpress/core-data/build-module/hooks/use-query-select.js



const META_SELECTORS = [
  "getIsResolving",
  "hasStartedResolution",
  "hasFinishedResolution",
  "isResolving",
  "getCachedResolvers"
];
function useQuerySelect(mapQuerySelect, deps) {
  return (0,external_wp_data_.useSelect)((select, registry) => {
    const resolve = (store) => enrichSelectors(select(store));
    return mapQuerySelect(resolve, registry);
  }, deps);
}
const enrichSelectors = memoize_default(((selectors) => {
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
            status = constants/* Status */.n.Resolving;
            break;
          case "finished":
            status = constants/* Status */.n.Success;
            break;
          case "error":
            status = constants/* Status */.n.Error;
            break;
          case void 0:
            status = constants/* Status */.n.Idle;
            break;
        }
        return {
          data,
          status,
          isResolving: status === constants/* Status */.n.Resolving,
          hasStarted: status !== constants/* Status */.n.Idle,
          hasResolved: status === constants/* Status */.n.Success || status === constants/* Status */.n.Error
        };
      }
    });
  }
  return resolvers;
}));



/***/ }),

/***/ 7723:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["i18n"];

/***/ }),

/***/ 7734:
/***/ ((module) => {

"use strict";


// do not edit .js files directly - edit src/index.jst


  var envHasBigInt64Array = typeof BigInt64Array !== 'undefined';


module.exports = function equal(a, b) {
  if (a === b) return true;

  if (a && b && typeof a == 'object' && typeof b == 'object') {
    if (a.constructor !== b.constructor) return false;

    var length, i, keys;
    if (Array.isArray(a)) {
      length = a.length;
      if (length != b.length) return false;
      for (i = length; i-- !== 0;)
        if (!equal(a[i], b[i])) return false;
      return true;
    }


    if ((a instanceof Map) && (b instanceof Map)) {
      if (a.size !== b.size) return false;
      for (i of a.entries())
        if (!b.has(i[0])) return false;
      for (i of a.entries())
        if (!equal(i[1], b.get(i[0]))) return false;
      return true;
    }

    if ((a instanceof Set) && (b instanceof Set)) {
      if (a.size !== b.size) return false;
      for (i of a.entries())
        if (!b.has(i[0])) return false;
      return true;
    }

    if (ArrayBuffer.isView(a) && ArrayBuffer.isView(b)) {
      length = a.length;
      if (length != b.length) return false;
      for (i = length; i-- !== 0;)
        if (a[i] !== b[i]) return false;
      return true;
    }


    if (a.constructor === RegExp) return a.source === b.source && a.flags === b.flags;
    if (a.valueOf !== Object.prototype.valueOf) return a.valueOf() === b.valueOf();
    if (a.toString !== Object.prototype.toString) return a.toString() === b.toString();

    keys = Object.keys(a);
    length = keys.length;
    if (length !== Object.keys(b).length) return false;

    for (i = length; i-- !== 0;)
      if (!Object.prototype.hasOwnProperty.call(b, keys[i])) return false;

    for (i = length; i-- !== 0;) {
      var key = keys[i];

      if (!equal(a[key], b[key])) return false;
    }

    return true;
  }

  // true if both NaN, false otherwise
  return a!==a && b!==b;
};


/***/ }),

/***/ 7826:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   j: () => (/* binding */ privateApis)
/* harmony export */ });
/* harmony import */ var _hooks_use_entity_records__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(7078);
/* harmony import */ var _utils__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(5101);
/* harmony import */ var _lock_unlock__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(6378);



const privateApis = {};
(0,_lock_unlock__WEBPACK_IMPORTED_MODULE_0__/* .lock */ .s)(privateApis, {
  useEntityRecordsWithPermissions: _hooks_use_entity_records__WEBPACK_IMPORTED_MODULE_1__/* .useEntityRecordsWithPermissions */ .pU,
  RECEIVE_INTERMEDIATE_RESULTS: _utils__WEBPACK_IMPORTED_MODULE_2__/* .RECEIVE_INTERMEDIATE_RESULTS */ .Z
});



/***/ }),

/***/ 8368:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  __experimentalGetCurrentGlobalStylesId: () => (/* binding */ __experimentalGetCurrentGlobalStylesId),
  __experimentalGetCurrentThemeBaseGlobalStyles: () => (/* binding */ __experimentalGetCurrentThemeBaseGlobalStyles),
  __experimentalGetCurrentThemeGlobalStylesVariations: () => (/* binding */ __experimentalGetCurrentThemeGlobalStylesVariations),
  __experimentalGetDirtyEntityRecords: () => (/* binding */ __experimentalGetDirtyEntityRecords),
  __experimentalGetEntitiesBeingSaved: () => (/* binding */ __experimentalGetEntitiesBeingSaved),
  __experimentalGetEntityRecordNoResolver: () => (/* binding */ __experimentalGetEntityRecordNoResolver),
  canUser: () => (/* binding */ canUser),
  canUserEditEntityRecord: () => (/* binding */ canUserEditEntityRecord),
  getAuthors: () => (/* binding */ getAuthors),
  getAutosave: () => (/* binding */ getAutosave),
  getAutosaves: () => (/* binding */ getAutosaves),
  getBlockPatternCategories: () => (/* binding */ getBlockPatternCategories),
  getBlockPatterns: () => (/* binding */ getBlockPatterns),
  getCurrentTheme: () => (/* binding */ getCurrentTheme),
  getCurrentThemeGlobalStylesRevisions: () => (/* binding */ getCurrentThemeGlobalStylesRevisions),
  getCurrentUser: () => (/* binding */ getCurrentUser),
  getDefaultTemplateId: () => (/* binding */ getDefaultTemplateId),
  getEditedEntityRecord: () => (/* binding */ getEditedEntityRecord),
  getEmbedPreview: () => (/* binding */ getEmbedPreview),
  getEntitiesByKind: () => (/* binding */ getEntitiesByKind),
  getEntitiesConfig: () => (/* binding */ getEntitiesConfig),
  getEntity: () => (/* binding */ getEntity),
  getEntityConfig: () => (/* binding */ getEntityConfig),
  getEntityRecord: () => (/* binding */ getEntityRecord),
  getEntityRecordEdits: () => (/* binding */ getEntityRecordEdits),
  getEntityRecordNonTransientEdits: () => (/* binding */ getEntityRecordNonTransientEdits),
  getEntityRecords: () => (/* binding */ getEntityRecords),
  getEntityRecordsTotalItems: () => (/* binding */ getEntityRecordsTotalItems),
  getEntityRecordsTotalPages: () => (/* binding */ getEntityRecordsTotalPages),
  getLastEntityDeleteError: () => (/* binding */ getLastEntityDeleteError),
  getLastEntitySaveError: () => (/* binding */ getLastEntitySaveError),
  getRawEntityRecord: () => (/* binding */ getRawEntityRecord),
  getRedoEdit: () => (/* binding */ getRedoEdit),
  getReferenceByDistinctEdits: () => (/* binding */ getReferenceByDistinctEdits),
  getRevision: () => (/* binding */ getRevision),
  getRevisions: () => (/* binding */ getRevisions),
  getThemeSupports: () => (/* binding */ getThemeSupports),
  getUndoEdit: () => (/* binding */ getUndoEdit),
  getUserPatternCategories: () => (/* binding */ getUserPatternCategories),
  getUserQueryResults: () => (/* binding */ getUserQueryResults),
  hasEditsForEntityRecord: () => (/* binding */ hasEditsForEntityRecord),
  hasEntityRecord: () => (/* binding */ hasEntityRecord),
  hasEntityRecords: () => (/* binding */ hasEntityRecords),
  hasFetchedAutosaves: () => (/* binding */ hasFetchedAutosaves),
  hasRedo: () => (/* binding */ hasRedo),
  hasUndo: () => (/* binding */ hasUndo),
  isAutosavingEntityRecord: () => (/* binding */ isAutosavingEntityRecord),
  isDeletingEntityRecord: () => (/* binding */ isDeletingEntityRecord),
  isPreviewEmbedFallback: () => (/* binding */ isPreviewEmbedFallback),
  isRequestingEmbedPreview: () => (/* binding */ isRequestingEmbedPreview),
  isSavingEntityRecord: () => (/* binding */ isSavingEntityRecord)
});

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__(7143);
// EXTERNAL MODULE: external ["wp","url"]
var external_wp_url_ = __webpack_require__(3832);
// EXTERNAL MODULE: external ["wp","deprecated"]
var external_wp_deprecated_ = __webpack_require__(4040);
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/name.js
var build_module_name = __webpack_require__(2278);
// EXTERNAL MODULE: ./node_modules/equivalent-key-map/equivalent-key-map.js
var equivalent_key_map = __webpack_require__(3249);
var equivalent_key_map_default = /*#__PURE__*/__webpack_require__.n(equivalent_key_map);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/queried-data/get-query-parts.js + 1 modules
var get_query_parts = __webpack_require__(4027);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/set-nested-value.js
var set_nested_value = __webpack_require__(5003);
;// ./node_modules/@wordpress/core-data/build-module/queried-data/selectors.js




const queriedItemsCacheByState = /* @__PURE__ */ new WeakMap();
function getQueriedItemsUncached(state, query) {
  const { stableKey, page, perPage, include, fields, context } = (0,get_query_parts/* default */.A)(query);
  let itemIds;
  if (state.queries?.[context]?.[stableKey]) {
    itemIds = state.queries[context][stableKey].itemIds;
  }
  if (!itemIds) {
    return null;
  }
  const startOffset = perPage === -1 ? 0 : (page - 1) * perPage;
  const endOffset = perPage === -1 ? itemIds.length : Math.min(startOffset + perPage, itemIds.length);
  const items = [];
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
        (0,set_nested_value/* default */.A)(filteredItem, field, value);
      }
    } else {
      if (!state.itemIsComplete[context]?.[itemId]) {
        return null;
      }
      filteredItem = item;
    }
    items.push(filteredItem);
  }
  return items;
}
const getQueriedItems = (0,external_wp_data_.createSelector)((state, query = {}) => {
  let queriedItemsCache = queriedItemsCacheByState.get(state);
  if (queriedItemsCache) {
    const queriedItems = queriedItemsCache.get(query);
    if (queriedItems !== void 0) {
      return queriedItems;
    }
  } else {
    queriedItemsCache = new (equivalent_key_map_default())();
    queriedItemsCacheByState.set(state, queriedItemsCache);
  }
  const items = getQueriedItemsUncached(state, query);
  queriedItemsCache.set(query, items);
  return items;
});
function getQueriedTotalItems(state, query = {}) {
  const { stableKey, context } = (0,get_query_parts/* default */.A)(query);
  return state.queries?.[context]?.[stableKey]?.meta?.totalItems ?? null;
}
function getQueriedTotalPages(state, query = {}) {
  const { stableKey, context } = (0,get_query_parts/* default */.A)(query);
  return state.queries?.[context]?.[stableKey]?.meta?.totalPages ?? null;
}


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/entities.js + 32 modules
var entities = __webpack_require__(4767);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/get-normalized-comma-separable.js
var get_normalized_comma_separable = __webpack_require__(533);
;// ./node_modules/@wordpress/core-data/build-module/utils/is-numeric-id.js
function isNumericID(id) {
  return /^\s*\d+\s*$/.test(id);
}


;// ./node_modules/@wordpress/core-data/build-module/utils/is-raw-attribute.js
function isRawAttribute(entity, attribute) {
  return (entity.rawAttributes || []).includes(attribute);
}


// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/user-permissions.js
var user_permissions = __webpack_require__(2577);
// EXTERNAL MODULE: ./node_modules/@wordpress/core-data/build-module/utils/log-entity-deprecation.js
var log_entity_deprecation = __webpack_require__(9410);
;// ./node_modules/@wordpress/core-data/build-module/selectors.js








const EMPTY_OBJECT = {};
const isRequestingEmbedPreview = (0,external_wp_data_.createRegistrySelector)(
  (select) => (state, url) => {
    return select(build_module_name/* STORE_NAME */.E).isResolving("getEmbedPreview", [
      url
    ]);
  }
);
function getAuthors(state, query) {
  external_wp_deprecated_default()("select( 'core' ).getAuthors()", {
    since: "5.9",
    alternative: "select( 'core' ).getUsers({ who: 'authors' })"
  });
  const path = (0,external_wp_url_.addQueryArgs)(
    "/wp/v2/users/?who=authors&per_page=100",
    query
  );
  return getUserQueryResults(state, path);
}
function getCurrentUser(state) {
  return state.currentUser;
}
const getUserQueryResults = (0,external_wp_data_.createSelector)(
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
  external_wp_deprecated_default()("wp.data.select( 'core' ).getEntitiesByKind()", {
    since: "6.0",
    alternative: "wp.data.select( 'core' ).getEntitiesConfig()"
  });
  return getEntitiesConfig(state, kind);
}
const getEntitiesConfig = (0,external_wp_data_.createSelector)(
  (state, kind) => state.entities.config.filter((entity) => entity.kind === kind),
  /* eslint-disable @typescript-eslint/no-unused-vars */
  (state, kind) => state.entities.config
  /* eslint-enable @typescript-eslint/no-unused-vars */
);
function getEntity(state, kind, name) {
  external_wp_deprecated_default()("wp.data.select( 'core' ).getEntity()", {
    since: "6.0",
    alternative: "wp.data.select( 'core' ).getEntityConfig()"
  });
  return getEntityConfig(state, kind, name);
}
function getEntityConfig(state, kind, name) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityConfig");
  return state.entities.config?.find(
    (config) => config.kind === kind && config.name === name
  );
}
const getEntityRecord = (0,external_wp_data_.createSelector)(
  ((state, kind, name, key, query) => {
    (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityRecord");
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
    const fields = (0,get_normalized_comma_separable/* default */.A)(query._fields) ?? [];
    for (let f = 0; f < fields.length; f++) {
      const field = fields[f].split(".");
      let value = item;
      field.forEach((fieldName) => {
        value = value?.[fieldName];
      });
      (0,set_nested_value/* default */.A)(filteredItem, field, value);
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
  const fields = (0,get_normalized_comma_separable/* default */.A)(query._fields) ?? [];
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
const getRawEntityRecord = (0,external_wp_data_.createSelector)(
  (state, kind, name, key) => {
    (0,log_entity_deprecation/* default */.A)(kind, name, "getRawEntityRecord");
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
  (0,log_entity_deprecation/* default */.A)(kind, name, "hasEntityRecords");
  return Array.isArray(getEntityRecords(state, kind, name, query));
}
const getEntityRecords = ((state, kind, name, query) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityRecords");
  const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
  if (!queriedState) {
    return null;
  }
  return getQueriedItems(queriedState, query);
});
const getEntityRecordsTotalItems = (state, kind, name, query) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityRecordsTotalItems");
  const queriedState = state.entities.records?.[kind]?.[name]?.queriedData;
  if (!queriedState) {
    return null;
  }
  return getQueriedTotalItems(queriedState, query);
};
const getEntityRecordsTotalPages = (state, kind, name, query) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityRecordsTotalPages");
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
const __experimentalGetDirtyEntityRecords = (0,external_wp_data_.createSelector)(
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
              key: entityRecord ? entityRecord[entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_] : void 0,
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
const __experimentalGetEntitiesBeingSaved = (0,external_wp_data_.createSelector)(
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
              key: entityRecord ? entityRecord[entityConfig.key || entities/* DEFAULT_ENTITY_KEY */.C_] : void 0,
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
  (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityRecordEdits");
  return state.entities.records?.[kind]?.[name]?.edits?.[recordId];
}
const getEntityRecordNonTransientEdits = (0,external_wp_data_.createSelector)(
  (state, kind, name, recordId) => {
    (0,log_entity_deprecation/* default */.A)(kind, name, "getEntityRecordNonTransientEdits");
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
  (0,log_entity_deprecation/* default */.A)(kind, name, "hasEditsForEntityRecord");
  return isSavingEntityRecord(state, kind, name, recordId) || Object.keys(
    getEntityRecordNonTransientEdits(state, kind, name, recordId)
  ).length > 0;
}
const getEditedEntityRecord = (0,external_wp_data_.createSelector)(
  (state, kind, name, recordId) => {
    (0,log_entity_deprecation/* default */.A)(kind, name, "getEditedEntityRecord");
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
  (0,log_entity_deprecation/* default */.A)(kind, name, "isAutosavingEntityRecord");
  const { pending, isAutosave } = state.entities.records?.[kind]?.[name]?.saving?.[recordId] ?? {};
  return Boolean(pending && isAutosave);
}
function isSavingEntityRecord(state, kind, name, recordId) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "isSavingEntityRecord");
  return state.entities.records?.[kind]?.[name]?.saving?.[recordId]?.pending ?? false;
}
function isDeletingEntityRecord(state, kind, name, recordId) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "isDeletingEntityRecord");
  return state.entities.records?.[kind]?.[name]?.deleting?.[recordId]?.pending ?? false;
}
function getLastEntitySaveError(state, kind, name, recordId) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getLastEntitySaveError");
  return state.entities.records?.[kind]?.[name]?.saving?.[recordId]?.error;
}
function getLastEntityDeleteError(state, kind, name, recordId) {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getLastEntityDeleteError");
  return state.entities.records?.[kind]?.[name]?.deleting?.[recordId]?.error;
}
function getUndoEdit(state) {
  external_wp_deprecated_default()("select( 'core' ).getUndoEdit()", {
    since: "6.3"
  });
  return void 0;
}
function getRedoEdit(state) {
  external_wp_deprecated_default()("select( 'core' ).getRedoEdit()", {
    since: "6.3"
  });
  return void 0;
}
function hasUndo(state) {
  return state.undoManager.hasUndo();
}
function hasRedo(state) {
  return state.undoManager.hasRedo();
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
    (0,log_entity_deprecation/* default */.A)(resource.kind, resource.name, "canUser");
  }
  const key = (0,user_permissions/* getUserPermissionCacheKey */.kC)(action, resource, id);
  return state.userPermissions[key];
}
function canUserEditEntityRecord(state, kind, name, recordId) {
  external_wp_deprecated_default()(`wp.data.select( 'core' ).canUserEditEntityRecord()`, {
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
  const autosaves = state.autosaves[postId];
  return autosaves?.find(
    (autosave) => autosave.author === authorId
  );
}
const hasFetchedAutosaves = (0,external_wp_data_.createRegistrySelector)(
  (select) => (state, postType, postId) => {
    return select(build_module_name/* STORE_NAME */.E).hasFinishedResolution("getAutosaves", [
      postType,
      postId
    ]);
  }
);
function getReferenceByDistinctEdits(state) {
  return state.editsReference;
}
function __experimentalGetCurrentThemeBaseGlobalStyles(state) {
  const currentTheme = getCurrentTheme(state);
  if (!currentTheme) {
    return null;
  }
  return state.themeBaseGlobalStyles[currentTheme.stylesheet];
}
function __experimentalGetCurrentThemeGlobalStylesVariations(state) {
  const currentTheme = getCurrentTheme(state);
  if (!currentTheme) {
    return null;
  }
  return state.themeGlobalStyleVariations[currentTheme.stylesheet];
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
  external_wp_deprecated_default()("select( 'core' ).getCurrentThemeGlobalStylesRevisions()", {
    since: "6.5.0",
    alternative: "select( 'core' ).getRevisions( 'root', 'globalStyles', ${ recordKey } )"
  });
  const currentGlobalStylesId = __experimentalGetCurrentGlobalStylesId(state);
  if (!currentGlobalStylesId) {
    return null;
  }
  return state.themeGlobalStyleRevisions[currentGlobalStylesId];
}
function getDefaultTemplateId(state, query) {
  return state.defaultTemplates[JSON.stringify(query)];
}
const getRevisions = (state, kind, name, recordKey, query) => {
  (0,log_entity_deprecation/* default */.A)(kind, name, "getRevisions");
  const queriedStateRevisions = state.entities.records?.[kind]?.[name]?.revisions?.[recordKey];
  if (!queriedStateRevisions) {
    return null;
  }
  return getQueriedItems(queriedStateRevisions, query);
};
const getRevision = (0,external_wp_data_.createSelector)(
  (state, kind, name, recordKey, revisionKey, query) => {
    (0,log_entity_deprecation/* default */.A)(kind, name, "getRevision");
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
    const fields = (0,get_normalized_comma_separable/* default */.A)(query._fields) ?? [];
    for (let f = 0; f < fields.length; f++) {
      const field = fields[f].split(".");
      let value = item;
      field.forEach((fieldName) => {
        value = value?.[fieldName];
      });
      (0,set_nested_value/* default */.A)(filteredItem, field, value);
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



/***/ }),

/***/ 8537:
/***/ ((module) => {

"use strict";
module.exports = window["wp"]["htmlEntities"];

/***/ }),

/***/ 8582:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ dynamicSelectors),
/* harmony export */   B: () => (/* binding */ dynamicActions)
/* harmony export */ });
let dynamicActions;
let dynamicSelectors;



/***/ }),

/***/ 8741:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   getBlockPatternsForPostType: () => (/* binding */ getBlockPatternsForPostType),
/* harmony export */   getEntityRecordPermissions: () => (/* binding */ getEntityRecordPermissions),
/* harmony export */   getEntityRecordsPermissions: () => (/* binding */ getEntityRecordsPermissions),
/* harmony export */   getHomePage: () => (/* binding */ getHomePage),
/* harmony export */   getNavigationFallbackId: () => (/* binding */ getNavigationFallbackId),
/* harmony export */   getPostsPageId: () => (/* binding */ getPostsPageId),
/* harmony export */   getRegisteredPostMeta: () => (/* binding */ getRegisteredPostMeta),
/* harmony export */   getTemplateId: () => (/* binding */ getTemplateId),
/* harmony export */   getUndoManager: () => (/* binding */ getUndoManager)
/* harmony export */ });
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(7143);
/* harmony import */ var _wordpress_data__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_data__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _selectors__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(8368);
/* harmony import */ var _name__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(2278);
/* harmony import */ var _lock_unlock__WEBPACK_IMPORTED_MODULE_4__ = __webpack_require__(6378);
/* harmony import */ var _utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(9410);





function getUndoManager(state) {
  return state.undoManager;
}
function getNavigationFallbackId(state) {
  return state.navigationFallbackId;
}
const getBlockPatternsForPostType = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createRegistrySelector)(
  (select) => (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createSelector)(
    (state, postType) => select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getBlockPatterns().filter(
      ({ postTypes }) => !postTypes || Array.isArray(postTypes) && postTypes.includes(postType)
    ),
    () => [select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getBlockPatterns()]
  )
);
const getEntityRecordsPermissions = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createRegistrySelector)(
  (select) => (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createSelector)(
    (state, kind, name, ids) => {
      const normalizedIds = Array.isArray(ids) ? ids : [ids];
      return normalizedIds.map((id) => ({
        delete: select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).canUser("delete", {
          kind,
          name,
          id
        }),
        update: select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).canUser("update", {
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
  (0,_utils_log_entity_deprecation__WEBPACK_IMPORTED_MODULE_2__/* ["default"] */ .A)(kind, name, "getEntityRecordPermissions");
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
const getHomePage = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createRegistrySelector)(
  (select) => (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createSelector)(
    () => {
      const siteData = select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getEntityRecord(
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
        _name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E
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
      (0,_selectors__WEBPACK_IMPORTED_MODULE_3__.getEntityRecord)(state, "root", "site"),
      (0,_selectors__WEBPACK_IMPORTED_MODULE_3__.getEntityRecord)(state, "root", "__unstableBase"),
      (0,_selectors__WEBPACK_IMPORTED_MODULE_3__.getDefaultTemplateId)(state, {
        slug: "front-page"
      })
    ]
  )
);
const getPostsPageId = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createRegistrySelector)((select) => () => {
  const siteData = select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getEntityRecord(
    "root",
    "__unstableBase"
  );
  return siteData?.show_on_front === "page" ? normalizePageId(siteData.page_for_posts) : null;
});
const getTemplateId = (0,_wordpress_data__WEBPACK_IMPORTED_MODULE_0__.createRegistrySelector)(
  (select) => (state, postType, postId) => {
    const homepage = (0,_lock_unlock__WEBPACK_IMPORTED_MODULE_4__/* .unlock */ .T)(select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E)).getHomePage();
    if (!homepage) {
      return;
    }
    if (postType === "page" && postType === homepage?.postType && postId.toString() === homepage?.postId) {
      const templates = select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getEntityRecords(
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
    const editedEntity = select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getEditedEntityRecord(
      "postType",
      postType,
      postId
    );
    if (!editedEntity) {
      return;
    }
    const postsPageId = (0,_lock_unlock__WEBPACK_IMPORTED_MODULE_4__/* .unlock */ .T)(select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E)).getPostsPageId();
    if (postType === "page" && postsPageId === postId.toString()) {
      return select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getDefaultTemplateId({
        slug: "home"
      });
    }
    const currentTemplateSlug = editedEntity.template;
    if (currentTemplateSlug) {
      const userTemplates = select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getEntityRecords(
        "postType",
        "wp_template",
        { per_page: -1 }
      );
      if (!userTemplates) {
        return;
      }
      const userTemplateWithSlug = userTemplates.find(
        ({ slug }) => slug === currentTemplateSlug
      );
      if (userTemplateWithSlug) {
        return userTemplateWithSlug.id;
      }
      const registeredTemplates = select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getEntityRecords(
        "postType",
        "wp_registered_template",
        { per_page: -1 }
      );
      if (!registeredTemplates) {
        return;
      }
      const registeredTemplateWithSlug = registeredTemplates.find(
        ({ slug }) => slug === currentTemplateSlug
      );
      if (registeredTemplateWithSlug) {
        return registeredTemplateWithSlug.id;
      }
    }
    let slugToCheck;
    if (editedEntity.slug) {
      slugToCheck = postType === "page" ? `${postType}-${editedEntity.slug}` : `single-${postType}-${editedEntity.slug}`;
    } else {
      slugToCheck = postType === "page" ? "page" : `single-${postType}`;
    }
    return select(_name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E).getDefaultTemplateId({
      slug: slugToCheck
    });
  }
);



/***/ }),

/***/ 8843:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   D: () => (/* binding */ EntityContext)
/* harmony export */ });
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(6087);
/* harmony import */ var _wordpress_element__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_element__WEBPACK_IMPORTED_MODULE_0__);

const EntityContext = (0,_wordpress_element__WEBPACK_IMPORTED_MODULE_0__.createContext)({});
EntityContext.displayName = "EntityContext";



/***/ }),

/***/ 9410:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   A: () => (/* binding */ logEntityDeprecation)
/* harmony export */ });
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(4040);
/* harmony import */ var _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_deprecated__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _entities__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(4767);


let loggedAlready = false;
function logEntityDeprecation(kind, name, functionName, {
  alternativeFunctionName,
  isShorthandSelector = false
} = {}) {
  const deprecation = _entities__WEBPACK_IMPORTED_MODULE_1__/* .deprecatedEntities */ .TK[kind]?.[name];
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
    _wordpress_deprecated__WEBPACK_IMPORTED_MODULE_0___default()(message, {
      ...deprecation,
      alternative: alternativeMessage
    });
  }
  loggedAlready = true;
  setTimeout(() => {
    loggedAlready = false;
  }, 0);
}



/***/ }),

/***/ 9424:
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   editMediaEntity: () => (/* binding */ editMediaEntity),
/* harmony export */   receiveRegisteredPostMeta: () => (/* binding */ receiveRegisteredPostMeta)
/* harmony export */ });
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(1455);
/* harmony import */ var _wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0__);
/* harmony import */ var _name__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(2278);


function receiveRegisteredPostMeta(postType, registeredPostMeta) {
  return {
    type: "RECEIVE_REGISTERED_POST_META",
    postType,
    registeredPostMeta
  };
}
const editMediaEntity = (recordId, edits = {}, { __unstableFetch = (_wordpress_api_fetch__WEBPACK_IMPORTED_MODULE_0___default()), throwOnError = false } = {}) => async ({ dispatch, resolveSelect }) => {
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
  const lock = await dispatch.__unstableAcquireStoreLock(
    _name__WEBPACK_IMPORTED_MODULE_1__/* .STORE_NAME */ .E,
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
    dispatch.__unstableReleaseStoreLock(lock);
  }
};



/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module is referenced by other modules so it can't be inlined
/******/ 	var __webpack_exports__ = __webpack_require__(4565);
/******/ 	(window.wp = window.wp || {}).coreData = __webpack_exports__;
/******/ 	
/******/ })()
;