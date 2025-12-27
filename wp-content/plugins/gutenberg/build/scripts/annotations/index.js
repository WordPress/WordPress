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

  // packages/annotations/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    store: () => store
  });

  // packages/annotations/build-module/format/index.js
  var import_rich_text2 = __toESM(require_rich_text());

  // packages/annotations/build-module/format/annotation.js
  var import_i18n = __toESM(require_i18n());
  var import_rich_text = __toESM(require_rich_text());

  // packages/annotations/build-module/store/constants.js
  var STORE_NAME = "core/annotations";

  // packages/annotations/build-module/format/annotation.js
  var FORMAT_NAME = "core/annotation";
  var ANNOTATION_ATTRIBUTE_PREFIX = "annotation-text-";
  function applyAnnotations(record, annotations2 = []) {
    annotations2.forEach((annotation2) => {
      let { start, end } = annotation2;
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
  function updateAnnotationsWithPositions(annotations2, positions, { removeAnnotation, updateAnnotationRange }) {
    annotations2.forEach((currentAnnotation) => {
      const position = positions[currentAnnotation.id];
      if (!position) {
        removeAnnotation(currentAnnotation.id);
        return;
      }
      const { start, end } = currentAnnotation;
      if (start !== position.start || end !== position.end) {
        updateAnnotationRange(
          currentAnnotation.id,
          position.start,
          position.end
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
    edit() {
      return null;
    },
    __experimentalGetPropsForEditableTreePreparation(select, { richTextIdentifier, blockClientId }) {
      return {
        annotations: select(
          STORE_NAME
        ).__experimentalGetAnnotationsForRichText(
          blockClientId,
          richTextIdentifier
        )
      };
    },
    __experimentalCreatePrepareEditableTree({ annotations: annotations2 }) {
      return (formats, text) => {
        if (annotations2.length === 0) {
          return formats;
        }
        let record = { formats, text };
        record = applyAnnotations(record, annotations2);
        return record.formats;
      };
    },
    __experimentalGetPropsForEditableTreeChangeHandler(dispatch) {
      return {
        removeAnnotation: dispatch(STORE_NAME).__experimentalRemoveAnnotation,
        updateAnnotationRange: dispatch(STORE_NAME).__experimentalUpdateAnnotationRange
      };
    },
    __experimentalCreateOnChangeEditableValue(props) {
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

  // packages/annotations/build-module/format/index.js
  var { name, ...settings } = annotation;
  (0, import_rich_text2.registerFormatType)(name, settings);

  // packages/annotations/build-module/block/index.js
  var import_hooks = __toESM(require_hooks());
  var import_data = __toESM(require_data());
  var addAnnotationClassName = (OriginalComponent) => {
    return (0, import_data.withSelect)((select, { clientId, className }) => {
      const annotations2 = select(STORE_NAME).__experimentalGetAnnotationsForBlock(
        clientId
      );
      return {
        className: annotations2.map((annotation2) => {
          return "is-annotated-by-" + annotation2.source;
        }).concat(className).filter(Boolean).join(" ")
      };
    })(OriginalComponent);
  };
  (0, import_hooks.addFilter)(
    "editor.BlockListBlock",
    "core/annotations",
    addAnnotationClassName
  );

  // packages/annotations/build-module/store/index.js
  var import_data3 = __toESM(require_data());

  // packages/annotations/build-module/store/reducer.js
  function filterWithReference(collection, predicate) {
    const filteredCollection = collection.filter(predicate);
    return collection.length === filteredCollection.length ? collection : filteredCollection;
  }
  var mapValues = (obj, callback) => Object.entries(obj).reduce(
    (acc, [key, value]) => ({
      ...acc,
      [key]: callback(value)
    }),
    {}
  );
  function isValidAnnotationRange(annotation2) {
    return typeof annotation2.start === "number" && typeof annotation2.end === "number" && annotation2.start <= annotation2.end;
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
        if (newAnnotation.selector === "range" && !isValidAnnotationRange(newAnnotation.range)) {
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

  // packages/annotations/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    __experimentalGetAllAnnotationsForBlock: () => __experimentalGetAllAnnotationsForBlock,
    __experimentalGetAnnotations: () => __experimentalGetAnnotations,
    __experimentalGetAnnotationsForBlock: () => __experimentalGetAnnotationsForBlock,
    __experimentalGetAnnotationsForRichText: () => __experimentalGetAnnotationsForRichText
  });
  var import_data2 = __toESM(require_data());
  var EMPTY_ARRAY = [];
  var __experimentalGetAnnotationsForBlock = (0, import_data2.createSelector)(
    (state, blockClientId) => {
      return (state?.[blockClientId] ?? []).filter((annotation2) => {
        return annotation2.selector === "block";
      });
    },
    (state, blockClientId) => [state?.[blockClientId] ?? EMPTY_ARRAY]
  );
  function __experimentalGetAllAnnotationsForBlock(state, blockClientId) {
    return state?.[blockClientId] ?? EMPTY_ARRAY;
  }
  var __experimentalGetAnnotationsForRichText = (0, import_data2.createSelector)(
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
    (state, blockClientId) => [state?.[blockClientId] ?? EMPTY_ARRAY]
  );
  function __experimentalGetAnnotations(state) {
    return Object.values(state).flat();
  }

  // packages/annotations/build-module/store/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    __experimentalAddAnnotation: () => __experimentalAddAnnotation,
    __experimentalRemoveAnnotation: () => __experimentalRemoveAnnotation,
    __experimentalRemoveAnnotationsBySource: () => __experimentalRemoveAnnotationsBySource,
    __experimentalUpdateAnnotationRange: () => __experimentalUpdateAnnotationRange
  });

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

  // packages/annotations/build-module/store/actions.js
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
    if (selector === "range") {
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

  // packages/annotations/build-module/store/index.js
  var store = (0, import_data3.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    selectors: selectors_exports,
    actions: actions_exports
  });
  (0, import_data3.register)(store);
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
