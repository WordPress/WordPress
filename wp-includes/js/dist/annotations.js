/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
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
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  store: () => (/* reexport */ store)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/annotations/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  __experimentalGetAllAnnotationsForBlock: () => (__experimentalGetAllAnnotationsForBlock),
  __experimentalGetAnnotations: () => (__experimentalGetAnnotations),
  __experimentalGetAnnotationsForBlock: () => (__experimentalGetAnnotationsForBlock),
  __experimentalGetAnnotationsForRichText: () => (__experimentalGetAnnotationsForRichText)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/annotations/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  __experimentalAddAnnotation: () => (__experimentalAddAnnotation),
  __experimentalRemoveAnnotation: () => (__experimentalRemoveAnnotation),
  __experimentalRemoveAnnotationsBySource: () => (__experimentalRemoveAnnotationsBySource),
  __experimentalUpdateAnnotationRange: () => (__experimentalUpdateAnnotationRange)
});

;// external ["wp","richText"]
const external_wp_richText_namespaceObject = window["wp"]["richText"];
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// ./node_modules/@wordpress/annotations/build-module/store/constants.js
const STORE_NAME = "core/annotations";


;// ./node_modules/@wordpress/annotations/build-module/format/annotation.js


const FORMAT_NAME = "core/annotation";
const ANNOTATION_ATTRIBUTE_PREFIX = "annotation-text-";

function applyAnnotations(record, annotations = []) {
  annotations.forEach((annotation2) => {
    let { start, end } = annotation2;
    if (start > record.text.length) {
      start = record.text.length;
    }
    if (end > record.text.length) {
      end = record.text.length;
    }
    const className = ANNOTATION_ATTRIBUTE_PREFIX + annotation2.source;
    const id = ANNOTATION_ATTRIBUTE_PREFIX + annotation2.id;
    record = (0,external_wp_richText_namespaceObject.applyFormat)(
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
function removeAnnotations(record) {
  return removeFormat(record, "core/annotation", 0, record.text.length);
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
function updateAnnotationsWithPositions(annotations, positions, { removeAnnotation, updateAnnotationRange }) {
  annotations.forEach((currentAnnotation) => {
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
const annotation = {
  name: FORMAT_NAME,
  title: (0,external_wp_i18n_namespaceObject.__)("Annotation"),
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
  __experimentalCreatePrepareEditableTree({ annotations }) {
    return (formats, text) => {
      if (annotations.length === 0) {
        return formats;
      }
      let record = { formats, text };
      record = applyAnnotations(record, annotations);
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
      const { removeAnnotation, updateAnnotationRange, annotations } = props;
      updateAnnotationsWithPositions(annotations, positions, {
        removeAnnotation,
        updateAnnotationRange
      });
    };
  }
};


;// ./node_modules/@wordpress/annotations/build-module/format/index.js


const { name: format_name, ...settings } = annotation;
(0,external_wp_richText_namespaceObject.registerFormatType)(format_name, settings);

;// external ["wp","hooks"]
const external_wp_hooks_namespaceObject = window["wp"]["hooks"];
;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// ./node_modules/@wordpress/annotations/build-module/block/index.js



const addAnnotationClassName = (OriginalComponent) => {
  return (0,external_wp_data_namespaceObject.withSelect)((select, { clientId, className }) => {
    const annotations = select(STORE_NAME).__experimentalGetAnnotationsForBlock(
      clientId
    );
    return {
      className: annotations.map((annotation) => {
        return "is-annotated-by-" + annotation.source;
      }).concat(className).filter(Boolean).join(" ")
    };
  })(OriginalComponent);
};
(0,external_wp_hooks_namespaceObject.addFilter)(
  "editor.BlockListBlock",
  "core/annotations",
  addAnnotationClassName
);

;// ./node_modules/@wordpress/annotations/build-module/store/reducer.js
function filterWithReference(collection, predicate) {
  const filteredCollection = collection.filter(predicate);
  return collection.length === filteredCollection.length ? collection : filteredCollection;
}
const mapValues = (obj, callback) => Object.entries(obj).reduce(
  (acc, [key, value]) => ({
    ...acc,
    [key]: callback(value)
  }),
  {}
);
function isValidAnnotationRange(annotation) {
  return typeof annotation.start === "number" && typeof annotation.end === "number" && annotation.start <= annotation.end;
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
          (annotation) => {
            return annotation.id !== action.annotationId;
          }
        );
      });
    case "ANNOTATION_UPDATE_RANGE":
      return mapValues(state, (annotationsForBlock) => {
        let hasChangedRange = false;
        const newAnnotations = annotationsForBlock.map(
          (annotation) => {
            if (annotation.id === action.annotationId) {
              hasChangedRange = true;
              return {
                ...annotation,
                range: {
                  start: action.start,
                  end: action.end
                }
              };
            }
            return annotation;
          }
        );
        return hasChangedRange ? newAnnotations : annotationsForBlock;
      });
    case "ANNOTATION_REMOVE_SOURCE":
      return mapValues(state, (annotationsForBlock) => {
        return filterWithReference(
          annotationsForBlock,
          (annotation) => {
            return annotation.source !== action.source;
          }
        );
      });
  }
  return state;
}
var reducer_default = annotations;


;// ./node_modules/@wordpress/annotations/build-module/store/selectors.js

const EMPTY_ARRAY = [];
const __experimentalGetAnnotationsForBlock = (0,external_wp_data_namespaceObject.createSelector)(
  (state, blockClientId) => {
    return (state?.[blockClientId] ?? []).filter((annotation) => {
      return annotation.selector === "block";
    });
  },
  (state, blockClientId) => [state?.[blockClientId] ?? EMPTY_ARRAY]
);
function __experimentalGetAllAnnotationsForBlock(state, blockClientId) {
  return state?.[blockClientId] ?? EMPTY_ARRAY;
}
const __experimentalGetAnnotationsForRichText = (0,external_wp_data_namespaceObject.createSelector)(
  (state, blockClientId, richTextIdentifier) => {
    return (state?.[blockClientId] ?? []).filter((annotation) => {
      return annotation.selector === "range" && richTextIdentifier === annotation.richTextIdentifier;
    }).map((annotation) => {
      const { range, ...other } = annotation;
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


;// ./node_modules/@wordpress/annotations/node_modules/uuid/dist/esm-browser/native.js
const randomUUID = typeof crypto !== 'undefined' && crypto.randomUUID && crypto.randomUUID.bind(crypto);
/* harmony default export */ const esm_browser_native = ({
  randomUUID
});
;// ./node_modules/@wordpress/annotations/node_modules/uuid/dist/esm-browser/rng.js
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
;// ./node_modules/@wordpress/annotations/node_modules/uuid/dist/esm-browser/stringify.js

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
;// ./node_modules/@wordpress/annotations/node_modules/uuid/dist/esm-browser/v4.js




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
;// ./node_modules/@wordpress/annotations/build-module/store/actions.js

function __experimentalAddAnnotation({
  blockClientId,
  richTextIdentifier = null,
  range = null,
  selector = "range",
  source = "default",
  id = esm_browser_v4()
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


;// ./node_modules/@wordpress/annotations/build-module/store/index.js





const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer_default,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);


;// ./node_modules/@wordpress/annotations/build-module/index.js





(window.wp = window.wp || {}).annotations = __webpack_exports__;
/******/ })()
;