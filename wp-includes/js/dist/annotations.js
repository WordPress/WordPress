"use strict";
var wp;
(wp ||= {}).annotations = (() => {
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
    for (var name2 in all)
      __defProp(target, name2, { get: all[name2], enumerable: true });
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

  // package-external:@wordpress/hooks
  var require_hooks = __commonJS({
    "package-external:@wordpress/hooks"(exports, module) {
      module.exports = window.wp.hooks;
    }
  });

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // packages/annotations/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    store: () => store
  });

  // packages/annotations/build-module/format/index.mjs
  var import_rich_text2 = __toESM(require_rich_text(), 1);

  // packages/annotations/build-module/format/annotation.mjs
  var import_i18n = __toESM(require_i18n(), 1);
  var import_rich_text = __toESM(require_rich_text(), 1);

  // packages/annotations/build-module/store/constants.mjs
  var STORE_NAME = "core/annotations";

  // packages/annotations/build-module/format/annotation.mjs
  var FORMAT_NAME = "core/annotation";
  var ANNOTATION_ATTRIBUTE_PREFIX = "annotation-text-";
  function applyAnnotations(record, annotations2 = []) {
    annotations2.forEach((annotation2) => {
      let { start, end } = annotation2;
      if (typeof start !== "number" || typeof end !== "number") {
        return;
      }
      if (start > record.text.length) {
        start = record.text.length;
      }
      if (end > record.text.length) {
        end = record.text.length;
      }
      const className = ANNOTATION_ATTRIBUTE_PREFIX + annotation2.source;
      const id = ANNOTATION_ATTRIBUTE_PREFIX + annotation2.id;
      record = (0, import_rich_text.applyFormat)(
        record,
        {
          type: FORMAT_NAME,
          attributes: {
            className,
            id
          }
        },
        start,
        end
      );
    });
    return record;
  }
  function retrieveAnnotationPositions(formats) {
    const positions = {};
    formats.forEach((characterFormats, i) => {
      characterFormats = characterFormats || [];
      characterFormats = characterFormats.filter(
        (format) => format.type === FORMAT_NAME
      );
      characterFormats.forEach((format) => {
        let { id } = format.attributes;
        id = id.replace(ANNOTATION_ATTRIBUTE_PREFIX, "");
        if (!positions.hasOwnProperty(id)) {
          positions[id] = {
            start: i
          };
        }
        positions[id].end = i + 1;
      });
    });
    return positions;
  }
  function updateAnnotationsWithPositions(annotations2, positions, {
    removeAnnotation,
    updateAnnotationRange
  }) {
    annotations2.forEach((currentAnnotation) => {
      const position = positions[currentAnnotation.id];
      if (!position) {
        removeAnnotation(currentAnnotation.id);
        return;
      }
      const { start, end } = currentAnnotation;
      if (typeof start === "number" && typeof end === "number" && (start !== position.start || end !== (position.end ?? position.start))) {
        updateAnnotationRange(
          currentAnnotation.id,
          position.start,
          position.end ?? position.start
        );
      }
    });
  }
  var annotation = {
    name: FORMAT_NAME,
    title: (0, import_i18n.__)("Annotation"),
    tagName: "mark",
    className: "annotation-text",
    attributes: {
      className: "class",
      id: "id"
    },
    interactive: false,
    object: false,
    edit: () => {
      return null;
    },
    __experimentalGetPropsForEditableTreePreparation: (select, { richTextIdentifier, blockClientId }) => {
      return {
        annotations: select(
          STORE_NAME
        ).__experimentalGetAnnotationsForRichText(
          blockClientId,
          richTextIdentifier
        )
      };
    },
    __experimentalCreatePrepareEditableTree: ({ annotations: annotations2 }) => {
      return (formats, text) => {
        if (annotations2.length === 0) {
          return formats;
        }
        let record = {
          formats,
          text,
          replacements: [],
          start: 0,
          end: 0
        };
        record = applyAnnotations(record, annotations2);
        return record.formats;
      };
    },
    __experimentalGetPropsForEditableTreeChangeHandler: (dispatch) => {
      return {
        removeAnnotation: dispatch(STORE_NAME).__experimentalRemoveAnnotation,
        updateAnnotationRange: dispatch(STORE_NAME).__experimentalUpdateAnnotationRange
      };
    },
    __experimentalCreateOnChangeEditableValue: (props) => {
      return (formats) => {
        const positions = retrieveAnnotationPositions(formats);
        const { removeAnnotation, updateAnnotationRange, annotations: annotations2 } = props;
        updateAnnotationsWithPositions(annotations2, positions, {
          removeAnnotation,
          updateAnnotationRange
        });
      };
    }
  };

  // packages/annotations/build-module/format/index.mjs
  var { name, ...settings } = annotation;
  (0, import_rich_text2.registerFormatType)(name, settings);

  // packages/annotations/build-module/block/index.mjs
  var import_hooks = __toESM(require_hooks(), 1);
  var import_data3 = __toESM(require_data(), 1);

  // packages/annotations/build-module/store/index.mjs
  var import_data2 = __toESM(require_data(), 1);

  // packages/annotations/build-module/store/reducer.mjs
  function filterWithReference(collection, predicate) {
    const filteredCollection = collection.filter(predicate);
    return collection.length === filteredCollection.length ? collection : filteredCollection;
  }
  var mapValues = (obj, callback) => Object.entries(obj).reduce((acc, [key, value]) => {
    if (value === void 0) {
      return acc;
    }
    return {
      ...acc,
      [key]: callback(value)
    };
  }, {});
  function isValidAnnotationRange(annotation2) {
    return Boolean(
      annotation2.range && typeof annotation2.range.start === "number" && typeof annotation2.range.end === "number" && annotation2.range.start <= annotation2.range.end
    );
  }
  function annotations(state = {}, action) {
    switch (action.type) {
      case "ANNOTATION_ADD":
        const blockClientId = action.blockClientId;
        const newAnnotation = {
          id: action.id,
          blockClientId,
          richTextIdentifier: action.richTextIdentifier,
          source: action.source,
          selector: action.selector,
          range: action.range
        };
        if (newAnnotation.selector === "range" && !isValidAnnotationRange(newAnnotation)) {
          return state;
        }
        const previousAnnotationsForBlock = state?.[blockClientId] ?? [];
        return {
          ...state,
          [blockClientId]: [
            ...previousAnnotationsForBlock,
            newAnnotation
          ]
        };
      case "ANNOTATION_REMOVE":
        return mapValues(state, (annotationsForBlock) => {
          return filterWithReference(
            annotationsForBlock,
            (annotation2) => {
              return annotation2.id !== action.annotationId;
            }
          );
        });
      case "ANNOTATION_UPDATE_RANGE":
        return mapValues(state, (annotationsForBlock) => {
          let hasChangedRange = false;
          const newAnnotations = annotationsForBlock.map(
            (annotation2) => {
              if (annotation2.id === action.annotationId) {
                hasChangedRange = true;
                return {
                  ...annotation2,
                  range: {
                    start: action.start,
                    end: action.end
                  }
                };
              }
              return annotation2;
            }
          );
          return hasChangedRange ? newAnnotations : annotationsForBlock;
        });
      case "ANNOTATION_REMOVE_SOURCE":
        return mapValues(state, (annotationsForBlock) => {
          return filterWithReference(
            annotationsForBlock,
            (annotation2) => {
              return annotation2.source !== action.source;
            }
          );
        });
    }
    return state;
  }
  var reducer_default = annotations;

  // packages/annotations/build-module/store/selectors.mjs
  var selectors_exports = {};
  __export(selectors_exports, {
    __experimentalGetAllAnnotationsForBlock: () => __experimentalGetAllAnnotationsForBlock,
    __experimentalGetAnnotations: () => __experimentalGetAnnotations,
    __experimentalGetAnnotationsForBlock: () => __experimentalGetAnnotationsForBlock,
    __experimentalGetAnnotationsForRichText: () => __experimentalGetAnnotationsForRichText
  });
  var import_data = __toESM(require_data(), 1);
  var EMPTY_ARRAY = [];
  var __experimentalGetAnnotationsForBlock = (0, import_data.createSelector)(
    (state, blockClientId) => {
      return (state?.[blockClientId] ?? []).filter((annotation2) => {
        return annotation2.selector === "block";
      });
    },
    (state, blockClientId) => [
      state?.[blockClientId] ?? EMPTY_ARRAY
    ]
  );
  function __experimentalGetAllAnnotationsForBlock(state, blockClientId) {
    return state?.[blockClientId] ?? EMPTY_ARRAY;
  }
  var __experimentalGetAnnotationsForRichText = (0, import_data.createSelector)(
    (state, blockClientId, richTextIdentifier) => {
      return (state?.[blockClientId] ?? []).filter((annotation2) => {
        return annotation2.selector === "range" && richTextIdentifier === annotation2.richTextIdentifier;
      }).map((annotation2) => {
        const { range, ...other } = annotation2;
        return {
          ...range,
          ...other
        };
      });
    },
    (state, blockClientId) => [
      state?.[blockClientId] ?? EMPTY_ARRAY
    ]
  );
  function __experimentalGetAnnotations(state) {
    return Object.values(state).filter((arr) => Boolean(arr)).flat();
  }

  // packages/annotations/build-module/store/actions.mjs
  var actions_exports = {};
  __export(actions_exports, {
    __experimentalAddAnnotation: () => __experimentalAddAnnotation,
    __experimentalRemoveAnnotation: () => __experimentalRemoveAnnotation,
    __experimentalRemoveAnnotationsBySource: () => __experimentalRemoveAnnotationsBySource,
    __experimentalUpdateAnnotationRange: () => __experimentalUpdateAnnotationRange
  });

  // node_modules/uuid/dist/stringify.js
  var byteToHex = [];
  for (let i = 0; i < 256; ++i) {
    byteToHex.push((i + 256).toString(16).slice(1));
  }
  function unsafeStringify(arr, offset = 0) {
    return (byteToHex[arr[offset + 0]] + byteToHex[arr[offset + 1]] + byteToHex[arr[offset + 2]] + byteToHex[arr[offset + 3]] + "-" + byteToHex[arr[offset + 4]] + byteToHex[arr[offset + 5]] + "-" + byteToHex[arr[offset + 6]] + byteToHex[arr[offset + 7]] + "-" + byteToHex[arr[offset + 8]] + byteToHex[arr[offset + 9]] + "-" + byteToHex[arr[offset + 10]] + byteToHex[arr[offset + 11]] + byteToHex[arr[offset + 12]] + byteToHex[arr[offset + 13]] + byteToHex[arr[offset + 14]] + byteToHex[arr[offset + 15]]).toLowerCase();
  }

  // node_modules/uuid/dist/rng.js
  var rnds8 = new Uint8Array(16);
  function rng() {
    return crypto.getRandomValues(rnds8);
  }

  // node_modules/uuid/dist/v4.js
  function v4(options, buf, offset) {
    if (!buf && !options && crypto.randomUUID) {
      return crypto.randomUUID();
    }
    return _v4(options, buf, offset);
  }
  function _v4(options, buf, offset) {
    options = options || {};
    const rnds = options.random ?? options.rng?.() ?? rng();
    if (rnds.length < 16) {
      throw new Error("Random bytes length must be >= 16");
    }
    rnds[6] = rnds[6] & 15 | 64;
    rnds[8] = rnds[8] & 63 | 128;
    if (buf) {
      offset = offset || 0;
      if (offset < 0 || offset + 16 > buf.length) {
        throw new RangeError(`UUID byte range ${offset}:${offset + 15} is out of buffer bounds`);
      }
      for (let i = 0; i < 16; ++i) {
        buf[offset + i] = rnds[i];
      }
      return buf;
    }
    return unsafeStringify(rnds);
  }
  var v4_default = v4;

  // packages/annotations/build-module/store/actions.mjs
  function __experimentalAddAnnotation({
    blockClientId,
    richTextIdentifier = null,
    range = null,
    selector = "range",
    source = "default",
    id = v4_default()
  }) {
    const action = {
      type: "ANNOTATION_ADD",
      id,
      blockClientId,
      richTextIdentifier,
      source,
      selector
    };
    if (selector === "range" && range !== null) {
      action.range = range;
    }
    return action;
  }
  function __experimentalRemoveAnnotation(annotationId) {
    return {
      type: "ANNOTATION_REMOVE",
      annotationId
    };
  }
  function __experimentalUpdateAnnotationRange(annotationId, start, end) {
    return {
      type: "ANNOTATION_UPDATE_RANGE",
      annotationId,
      start,
      end
    };
  }
  function __experimentalRemoveAnnotationsBySource(source) {
    return {
      type: "ANNOTATION_REMOVE_SOURCE",
      source
    };
  }

  // packages/annotations/build-module/store/index.mjs
  var store = (0, import_data2.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    selectors: selectors_exports,
    actions: actions_exports
  });
  (0, import_data2.register)(store);

  // packages/annotations/build-module/block/index.mjs
  var addAnnotationClassName = (OriginalComponent) => {
    return (0, import_data3.withSelect)((select, ownProps) => {
      const { clientId, className } = ownProps;
      const annotations2 = select(store).__experimentalGetAnnotationsForBlock(clientId);
      return {
        className: annotations2.map((annotation2) => {
          return "is-annotated-by-" + annotation2.source;
        }).concat(className || "").filter(Boolean).join(" ")
      };
    })(OriginalComponent);
  };
  (0, import_hooks.addFilter)(
    "editor.BlockListBlock",
    "core/annotations",
    addAnnotationClassName
  );
  return __toCommonJS(index_exports);
})();
if(wp.annotations&&typeof wp.annotations==='object'){wp.annotations=Object.assign({},wp.annotations);}
