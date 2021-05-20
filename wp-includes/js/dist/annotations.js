this["wp"] = this["wp"] || {}; this["wp"]["annotations"] =
/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "23Y4");
/******/ })
/************************************************************************/
/******/ ({

/***/ "1ZqX":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["data"]; }());

/***/ }),

/***/ "23Y4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "store", function() { return /* reexport */ store; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/annotations/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetAnnotationsForBlock", function() { return __experimentalGetAnnotationsForBlock; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetAllAnnotationsForBlock", function() { return __experimentalGetAllAnnotationsForBlock; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetAnnotationsForRichText", function() { return __experimentalGetAnnotationsForRichText; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetAnnotations", function() { return __experimentalGetAnnotations; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/annotations/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "__experimentalAddAnnotation", function() { return __experimentalAddAnnotation; });
__webpack_require__.d(actions_namespaceObject, "__experimentalRemoveAnnotation", function() { return __experimentalRemoveAnnotation; });
__webpack_require__.d(actions_namespaceObject, "__experimentalUpdateAnnotationRange", function() { return __experimentalUpdateAnnotationRange; });
__webpack_require__.d(actions_namespaceObject, "__experimentalRemoveAnnotationsBySource", function() { return __experimentalRemoveAnnotationsBySource; });

// EXTERNAL MODULE: external ["wp","richText"]
var external_wp_richText_ = __webpack_require__("qRz9");

// EXTERNAL MODULE: external ["wp","i18n"]
var external_wp_i18n_ = __webpack_require__("l3Sj");

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/store/constants.js
/**
 * The identifier for the data store.
 *
 * @type {string}
 */
const STORE_NAME = 'core/annotations';

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/format/annotation.js
/**
 * WordPress dependencies
 */


const FORMAT_NAME = 'core/annotation';
const ANNOTATION_ATTRIBUTE_PREFIX = 'annotation-text-';
/**
 * Internal dependencies
 */


/**
 * Applies given annotations to the given record.
 *
 * @param {Object} record The record to apply annotations to.
 * @param {Array} annotations The annotation to apply.
 * @return {Object} A record with the annotations applied.
 */

function applyAnnotations(record, annotations = []) {
  annotations.forEach(annotation => {
    let {
      start,
      end
    } = annotation;

    if (start > record.text.length) {
      start = record.text.length;
    }

    if (end > record.text.length) {
      end = record.text.length;
    }

    const className = ANNOTATION_ATTRIBUTE_PREFIX + annotation.source;
    const id = ANNOTATION_ATTRIBUTE_PREFIX + annotation.id;
    record = Object(external_wp_richText_["applyFormat"])(record, {
      type: FORMAT_NAME,
      attributes: {
        className,
        id
      }
    }, start, end);
  });
  return record;
}
/**
 * Removes annotations from the given record.
 *
 * @param {Object} record Record to remove annotations from.
 * @return {Object} The cleaned record.
 */

function removeAnnotations(record) {
  return Object(external_wp_richText_["removeFormat"])(record, 'core/annotation', 0, record.text.length);
}
/**
 * Retrieves the positions of annotations inside an array of formats.
 *
 * @param {Array} formats Formats with annotations in there.
 * @return {Object} ID keyed positions of annotations.
 */

function retrieveAnnotationPositions(formats) {
  const positions = {};
  formats.forEach((characterFormats, i) => {
    characterFormats = characterFormats || [];
    characterFormats = characterFormats.filter(format => format.type === FORMAT_NAME);
    characterFormats.forEach(format => {
      let {
        id
      } = format.attributes;
      id = id.replace(ANNOTATION_ATTRIBUTE_PREFIX, '');

      if (!positions.hasOwnProperty(id)) {
        positions[id] = {
          start: i
        };
      } // Annotations refer to positions between characters.
      // Formats refer to the character themselves.
      // So we need to adjust for that here.


      positions[id].end = i + 1;
    });
  });
  return positions;
}
/**
 * Updates annotations in the state based on positions retrieved from RichText.
 *
 * @param {Array}    annotations                   The annotations that are currently applied.
 * @param {Array}    positions                     The current positions of the given annotations.
 * @param {Object}   actions
 * @param {Function} actions.removeAnnotation      Function to remove an annotation from the state.
 * @param {Function} actions.updateAnnotationRange Function to update an annotation range in the state.
 */


function updateAnnotationsWithPositions(annotations, positions, {
  removeAnnotation,
  updateAnnotationRange
}) {
  annotations.forEach(currentAnnotation => {
    const position = positions[currentAnnotation.id]; // If we cannot find an annotation, delete it.

    if (!position) {
      // Apparently the annotation has been removed, so remove it from the state:
      // Remove...
      removeAnnotation(currentAnnotation.id);
      return;
    }

    const {
      start,
      end
    } = currentAnnotation;

    if (start !== position.start || end !== position.end) {
      updateAnnotationRange(currentAnnotation.id, position.start, position.end);
    }
  });
}

const annotation_annotation = {
  name: FORMAT_NAME,
  title: Object(external_wp_i18n_["__"])('Annotation'),
  tagName: 'mark',
  className: 'annotation-text',
  attributes: {
    className: 'class',
    id: 'id'
  },

  edit() {
    return null;
  },

  __experimentalGetPropsForEditableTreePreparation(select, {
    richTextIdentifier,
    blockClientId
  }) {
    return {
      annotations: select(STORE_NAME).__experimentalGetAnnotationsForRichText(blockClientId, richTextIdentifier)
    };
  },

  __experimentalCreatePrepareEditableTree({
    annotations
  }) {
    return (formats, text) => {
      if (annotations.length === 0) {
        return formats;
      }

      let record = {
        formats,
        text
      };
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
    return formats => {
      const positions = retrieveAnnotationPositions(formats);
      const {
        removeAnnotation,
        updateAnnotationRange,
        annotations
      } = props;
      updateAnnotationsWithPositions(annotations, positions, {
        removeAnnotation,
        updateAnnotationRange
      });
    };
  }

};

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/format/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


const {
  name: format_name,
  ...settings
} = annotation_annotation;
Object(external_wp_richText_["registerFormatType"])(format_name, settings);

// EXTERNAL MODULE: external ["wp","hooks"]
var external_wp_hooks_ = __webpack_require__("g56x");

// EXTERNAL MODULE: external ["wp","data"]
var external_wp_data_ = __webpack_require__("1ZqX");

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/block/index.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/**
 * Adds annotation className to the block-list-block component.
 *
 * @param {Object} OriginalComponent The original BlockListBlock component.
 * @return {Object} The enhanced component.
 */

const addAnnotationClassName = OriginalComponent => {
  return Object(external_wp_data_["withSelect"])((select, {
    clientId,
    className
  }) => {
    const annotations = select(STORE_NAME).__experimentalGetAnnotationsForBlock(clientId);

    return {
      className: annotations.map(annotation => {
        return 'is-annotated-by-' + annotation.source;
      }).concat(className).filter(Boolean).join(' ')
    };
  })(OriginalComponent);
};

Object(external_wp_hooks_["addFilter"])('editor.BlockListBlock', 'core/annotations', addAnnotationClassName);

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/store/reducer.js
/**
 * External dependencies
 */

/**
 * Filters an array based on the predicate, but keeps the reference the same if
 * the array hasn't changed.
 *
 * @param {Array}    collection The collection to filter.
 * @param {Function} predicate  Function that determines if the item should stay
 *                              in the array.
 * @return {Array} Filtered array.
 */

function filterWithReference(collection, predicate) {
  const filteredCollection = collection.filter(predicate);
  return collection.length === filteredCollection.length ? collection : filteredCollection;
}
/**
 * Verifies whether the given annotations is a valid annotation.
 *
 * @param {Object} annotation The annotation to verify.
 * @return {boolean} Whether the given annotation is valid.
 */


function isValidAnnotationRange(annotation) {
  return Object(external_lodash_["isNumber"])(annotation.start) && Object(external_lodash_["isNumber"])(annotation.end) && annotation.start <= annotation.end;
}
/**
 * Reducer managing annotations.
 *
 * @param {Object} state  The annotations currently shown in the editor.
 * @param {Object} action Dispatched action.
 *
 * @return {Array} Updated state.
 */


function reducer_annotations(state = {}, action) {
  var _state$blockClientId;

  switch (action.type) {
    case 'ANNOTATION_ADD':
      const blockClientId = action.blockClientId;
      const newAnnotation = {
        id: action.id,
        blockClientId,
        richTextIdentifier: action.richTextIdentifier,
        source: action.source,
        selector: action.selector,
        range: action.range
      };

      if (newAnnotation.selector === 'range' && !isValidAnnotationRange(newAnnotation.range)) {
        return state;
      }

      const previousAnnotationsForBlock = (_state$blockClientId = state === null || state === void 0 ? void 0 : state[blockClientId]) !== null && _state$blockClientId !== void 0 ? _state$blockClientId : [];
      return { ...state,
        [blockClientId]: [...previousAnnotationsForBlock, newAnnotation]
      };

    case 'ANNOTATION_REMOVE':
      return Object(external_lodash_["mapValues"])(state, annotationsForBlock => {
        return filterWithReference(annotationsForBlock, annotation => {
          return annotation.id !== action.annotationId;
        });
      });

    case 'ANNOTATION_UPDATE_RANGE':
      return Object(external_lodash_["mapValues"])(state, annotationsForBlock => {
        let hasChangedRange = false;
        const newAnnotations = annotationsForBlock.map(annotation => {
          if (annotation.id === action.annotationId) {
            hasChangedRange = true;
            return { ...annotation,
              range: {
                start: action.start,
                end: action.end
              }
            };
          }

          return annotation;
        });
        return hasChangedRange ? newAnnotations : annotationsForBlock;
      });

    case 'ANNOTATION_REMOVE_SOURCE':
      return Object(external_lodash_["mapValues"])(state, annotationsForBlock => {
        return filterWithReference(annotationsForBlock, annotation => {
          return annotation.source !== action.source;
        });
      });
  }

  return state;
}
/* harmony default export */ var reducer = (reducer_annotations);

// EXTERNAL MODULE: ./node_modules/rememo/es/rememo.js
var rememo = __webpack_require__("pPDe");

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/store/selectors.js
/**
 * External dependencies
 */


/**
 * Shared reference to an empty array for cases where it is important to avoid
 * returning a new array reference on every invocation, as in a connected or
 * other pure component which performs `shouldComponentUpdate` check on props.
 * This should be used as a last resort, since the normalized data should be
 * maintained by the reducer result in state.
 *
 * @type {Array}
 */

const EMPTY_ARRAY = [];
/**
 * Returns the annotations for a specific client ID.
 *
 * @param {Object} state Editor state.
 * @param {string} clientId The ID of the block to get the annotations for.
 *
 * @return {Array} The annotations applicable to this block.
 */

const __experimentalGetAnnotationsForBlock = Object(rememo["a" /* default */])((state, blockClientId) => {
  var _state$blockClientId;

  return ((_state$blockClientId = state === null || state === void 0 ? void 0 : state[blockClientId]) !== null && _state$blockClientId !== void 0 ? _state$blockClientId : []).filter(annotation => {
    return annotation.selector === 'block';
  });
}, (state, blockClientId) => {
  var _state$blockClientId2;

  return [(_state$blockClientId2 = state === null || state === void 0 ? void 0 : state[blockClientId]) !== null && _state$blockClientId2 !== void 0 ? _state$blockClientId2 : EMPTY_ARRAY];
});
function __experimentalGetAllAnnotationsForBlock(state, blockClientId) {
  var _state$blockClientId3;

  return (_state$blockClientId3 = state === null || state === void 0 ? void 0 : state[blockClientId]) !== null && _state$blockClientId3 !== void 0 ? _state$blockClientId3 : EMPTY_ARRAY;
}
/**
 * Returns the annotations that apply to the given RichText instance.
 *
 * Both a blockClientId and a richTextIdentifier are required. This is because
 * a block might have multiple `RichText` components. This does mean that every
 * block needs to implement annotations itself.
 *
 * @param {Object} state              Editor state.
 * @param {string} blockClientId      The client ID for the block.
 * @param {string} richTextIdentifier Unique identifier that identifies the given RichText.
 * @return {Array} All the annotations relevant for the `RichText`.
 */

const __experimentalGetAnnotationsForRichText = Object(rememo["a" /* default */])((state, blockClientId, richTextIdentifier) => {
  var _state$blockClientId4;

  return ((_state$blockClientId4 = state === null || state === void 0 ? void 0 : state[blockClientId]) !== null && _state$blockClientId4 !== void 0 ? _state$blockClientId4 : []).filter(annotation => {
    return annotation.selector === 'range' && richTextIdentifier === annotation.richTextIdentifier;
  }).map(annotation => {
    const {
      range,
      ...other
    } = annotation;
    return { ...range,
      ...other
    };
  });
}, (state, blockClientId) => {
  var _state$blockClientId5;

  return [(_state$blockClientId5 = state === null || state === void 0 ? void 0 : state[blockClientId]) !== null && _state$blockClientId5 !== void 0 ? _state$blockClientId5 : EMPTY_ARRAY];
});
/**
 * Returns all annotations in the editor state.
 *
 * @param {Object} state Editor state.
 * @return {Array} All annotations currently applied.
 */

function __experimentalGetAnnotations(state) {
  return Object(external_lodash_["flatMap"])(state, annotations => {
    return annotations;
  });
}

// EXTERNAL MODULE: ./node_modules/uuid/dist/esm-browser/v4.js + 4 modules
var v4 = __webpack_require__("7Cbv");

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/store/actions.js
/**
 * External dependencies
 */

/**
 * @typedef WPAnnotationRange
 *
 * @property {number} start The offset where the annotation should start.
 * @property {number} end   The offset where the annotation should end.
 */

/**
 * Adds an annotation to a block.
 *
 * The `block` attribute refers to a block ID that needs to be annotated.
 * `isBlockAnnotation` controls whether or not the annotation is a block
 * annotation. The `source` is the source of the annotation, this will be used
 * to identity groups of annotations.
 *
 * The `range` property is only relevant if the selector is 'range'.
 *
 * @param {Object}            annotation                    The annotation to add.
 * @param {string}            annotation.blockClientId      The blockClientId to add the annotation to.
 * @param {string}            annotation.richTextIdentifier Identifier for the RichText instance the annotation applies to.
 * @param {WPAnnotationRange} annotation.range              The range at which to apply this annotation.
 * @param {string}            [annotation.selector="range"] The way to apply this annotation.
 * @param {string}            [annotation.source="default"] The source that added the annotation.
 * @param {string}            [annotation.id]               The ID the annotation should have. Generates a UUID by default.
 *
 * @return {Object} Action object.
 */

function __experimentalAddAnnotation({
  blockClientId,
  richTextIdentifier = null,
  range = null,
  selector = 'range',
  source = 'default',
  id = Object(v4["a" /* default */])()
}) {
  const action = {
    type: 'ANNOTATION_ADD',
    id,
    blockClientId,
    richTextIdentifier,
    source,
    selector
  };

  if (selector === 'range') {
    action.range = range;
  }

  return action;
}
/**
 * Removes an annotation with a specific ID.
 *
 * @param {string} annotationId The annotation to remove.
 *
 * @return {Object} Action object.
 */

function __experimentalRemoveAnnotation(annotationId) {
  return {
    type: 'ANNOTATION_REMOVE',
    annotationId
  };
}
/**
 * Updates the range of an annotation.
 *
 * @param {string} annotationId ID of the annotation to update.
 * @param {number} start The start of the new range.
 * @param {number} end The end of the new range.
 *
 * @return {Object} Action object.
 */

function __experimentalUpdateAnnotationRange(annotationId, start, end) {
  return {
    type: 'ANNOTATION_UPDATE_RANGE',
    annotationId,
    start,
    end
  };
}
/**
 * Removes all annotations of a specific source.
 *
 * @param {string} source The source to remove.
 *
 * @return {Object} Action object.
 */

function __experimentalRemoveAnnotationsBySource(source) {
  return {
    type: 'ANNOTATION_REMOVE_SOURCE',
    source
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




/**
 * Module Constants
 */


/**
 * Store definition for the annotations namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = Object(external_wp_data_["createReduxStore"])(STORE_NAME, {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject
});
Object(external_wp_data_["register"])(store);

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/index.js
/**
 * Internal dependencies
 */





/***/ }),

/***/ "7Cbv":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/rng.js
// Unique ID creation requires a high quality random # generator. In the browser we therefore
// require the crypto API and do not support built-in fallback to lower quality random number
// generators (like Math.random()).
var getRandomValues;
var rnds8 = new Uint8Array(16);
function rng() {
  // lazy load so that environments that need to polyfill have a chance to do so
  if (!getRandomValues) {
    // getRandomValues needs to be invoked in a context where "this" is a Crypto implementation. Also,
    // find the complete implementation of crypto (msCrypto) on IE11.
    getRandomValues = typeof crypto !== 'undefined' && crypto.getRandomValues && crypto.getRandomValues.bind(crypto) || typeof msCrypto !== 'undefined' && typeof msCrypto.getRandomValues === 'function' && msCrypto.getRandomValues.bind(msCrypto);

    if (!getRandomValues) {
      throw new Error('crypto.getRandomValues() not supported. See https://github.com/uuidjs/uuid#getrandomvalues-not-supported');
    }
  }

  return getRandomValues(rnds8);
}
// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/regex.js
/* harmony default export */ var regex = (/^(?:[0-9a-f]{8}-[0-9a-f]{4}-[1-5][0-9a-f]{3}-[89ab][0-9a-f]{3}-[0-9a-f]{12}|00000000-0000-0000-0000-000000000000)$/i);
// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/validate.js


function validate(uuid) {
  return typeof uuid === 'string' && regex.test(uuid);
}

/* harmony default export */ var esm_browser_validate = (validate);
// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/stringify.js

/**
 * Convert array of 16 byte values to UUID string format of the form:
 * XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
 */

var byteToHex = [];

for (var stringify_i = 0; stringify_i < 256; ++stringify_i) {
  byteToHex.push((stringify_i + 0x100).toString(16).substr(1));
}

function stringify(arr) {
  var offset = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : 0;
  // Note: Be careful editing this code!  It's been tuned for performance
  // and works in ways you may not expect. See https://github.com/uuidjs/uuid/pull/434
  var uuid = (byteToHex[arr[offset + 0]] + byteToHex[arr[offset + 1]] + byteToHex[arr[offset + 2]] + byteToHex[arr[offset + 3]] + '-' + byteToHex[arr[offset + 4]] + byteToHex[arr[offset + 5]] + '-' + byteToHex[arr[offset + 6]] + byteToHex[arr[offset + 7]] + '-' + byteToHex[arr[offset + 8]] + byteToHex[arr[offset + 9]] + '-' + byteToHex[arr[offset + 10]] + byteToHex[arr[offset + 11]] + byteToHex[arr[offset + 12]] + byteToHex[arr[offset + 13]] + byteToHex[arr[offset + 14]] + byteToHex[arr[offset + 15]]).toLowerCase(); // Consistency check for valid UUID.  If this throws, it's likely due to one
  // of the following:
  // - One or more input array values don't map to a hex octet (leading to
  // "undefined" in the uuid)
  // - Invalid input values for the RFC `version` or `variant` fields

  if (!esm_browser_validate(uuid)) {
    throw TypeError('Stringified UUID is invalid');
  }

  return uuid;
}

/* harmony default export */ var esm_browser_stringify = (stringify);
// CONCATENATED MODULE: ./node_modules/uuid/dist/esm-browser/v4.js



function v4(options, buf, offset) {
  options = options || {};
  var rnds = options.random || (options.rng || rng)(); // Per 4.4, set bits for version and `clock_seq_hi_and_reserved`

  rnds[6] = rnds[6] & 0x0f | 0x40;
  rnds[8] = rnds[8] & 0x3f | 0x80; // Copy bytes to buffer, if provided

  if (buf) {
    offset = offset || 0;

    for (var i = 0; i < 16; ++i) {
      buf[offset + i] = rnds[i];
    }

    return buf;
  }

  return esm_browser_stringify(rnds);
}

/* harmony default export */ var esm_browser_v4 = __webpack_exports__["a"] = (v4);

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = window["lodash"]; }());

/***/ }),

/***/ "g56x":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["hooks"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["i18n"]; }());

/***/ }),

/***/ "pPDe":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";


var LEAF_KEY, hasWeakMap;

/**
 * Arbitrary value used as key for referencing cache object in WeakMap tree.
 *
 * @type {Object}
 */
LEAF_KEY = {};

/**
 * Whether environment supports WeakMap.
 *
 * @type {boolean}
 */
hasWeakMap = typeof WeakMap !== 'undefined';

/**
 * Returns the first argument as the sole entry in an array.
 *
 * @param {*} value Value to return.
 *
 * @return {Array} Value returned as entry in array.
 */
function arrayOf( value ) {
	return [ value ];
}

/**
 * Returns true if the value passed is object-like, or false otherwise. A value
 * is object-like if it can support property assignment, e.g. object or array.
 *
 * @param {*} value Value to test.
 *
 * @return {boolean} Whether value is object-like.
 */
function isObjectLike( value ) {
	return !! value && 'object' === typeof value;
}

/**
 * Creates and returns a new cache object.
 *
 * @return {Object} Cache object.
 */
function createCache() {
	var cache = {
		clear: function() {
			cache.head = null;
		},
	};

	return cache;
}

/**
 * Returns true if entries within the two arrays are strictly equal by
 * reference from a starting index.
 *
 * @param {Array}  a         First array.
 * @param {Array}  b         Second array.
 * @param {number} fromIndex Index from which to start comparison.
 *
 * @return {boolean} Whether arrays are shallowly equal.
 */
function isShallowEqual( a, b, fromIndex ) {
	var i;

	if ( a.length !== b.length ) {
		return false;
	}

	for ( i = fromIndex; i < a.length; i++ ) {
		if ( a[ i ] !== b[ i ] ) {
			return false;
		}
	}

	return true;
}

/**
 * Returns a memoized selector function. The getDependants function argument is
 * called before the memoized selector and is expected to return an immutable
 * reference or array of references on which the selector depends for computing
 * its own return value. The memoize cache is preserved only as long as those
 * dependant references remain the same. If getDependants returns a different
 * reference(s), the cache is cleared and the selector value regenerated.
 *
 * @param {Function} selector      Selector function.
 * @param {Function} getDependants Dependant getter returning an immutable
 *                                 reference or array of reference used in
 *                                 cache bust consideration.
 *
 * @return {Function} Memoized selector.
 */
/* harmony default export */ __webpack_exports__["a"] = (function( selector, getDependants ) {
	var rootCache, getCache;

	// Use object source as dependant if getter not provided
	if ( ! getDependants ) {
		getDependants = arrayOf;
	}

	/**
	 * Returns the root cache. If WeakMap is supported, this is assigned to the
	 * root WeakMap cache set, otherwise it is a shared instance of the default
	 * cache object.
	 *
	 * @return {(WeakMap|Object)} Root cache object.
	 */
	function getRootCache() {
		return rootCache;
	}

	/**
	 * Returns the cache for a given dependants array. When possible, a WeakMap
	 * will be used to create a unique cache for each set of dependants. This
	 * is feasible due to the nature of WeakMap in allowing garbage collection
	 * to occur on entries where the key object is no longer referenced. Since
	 * WeakMap requires the key to be an object, this is only possible when the
	 * dependant is object-like. The root cache is created as a hierarchy where
	 * each top-level key is the first entry in a dependants set, the value a
	 * WeakMap where each key is the next dependant, and so on. This continues
	 * so long as the dependants are object-like. If no dependants are object-
	 * like, then the cache is shared across all invocations.
	 *
	 * @see isObjectLike
	 *
	 * @param {Array} dependants Selector dependants.
	 *
	 * @return {Object} Cache object.
	 */
	function getWeakMapCache( dependants ) {
		var caches = rootCache,
			isUniqueByDependants = true,
			i, dependant, map, cache;

		for ( i = 0; i < dependants.length; i++ ) {
			dependant = dependants[ i ];

			// Can only compose WeakMap from object-like key.
			if ( ! isObjectLike( dependant ) ) {
				isUniqueByDependants = false;
				break;
			}

			// Does current segment of cache already have a WeakMap?
			if ( caches.has( dependant ) ) {
				// Traverse into nested WeakMap.
				caches = caches.get( dependant );
			} else {
				// Create, set, and traverse into a new one.
				map = new WeakMap();
				caches.set( dependant, map );
				caches = map;
			}
		}

		// We use an arbitrary (but consistent) object as key for the last item
		// in the WeakMap to serve as our running cache.
		if ( ! caches.has( LEAF_KEY ) ) {
			cache = createCache();
			cache.isUniqueByDependants = isUniqueByDependants;
			caches.set( LEAF_KEY, cache );
		}

		return caches.get( LEAF_KEY );
	}

	// Assign cache handler by availability of WeakMap
	getCache = hasWeakMap ? getWeakMapCache : getRootCache;

	/**
	 * Resets root memoization cache.
	 */
	function clear() {
		rootCache = hasWeakMap ? new WeakMap() : createCache();
	}

	// eslint-disable-next-line jsdoc/check-param-names
	/**
	 * The augmented selector call, considering first whether dependants have
	 * changed before passing it to underlying memoize function.
	 *
	 * @param {Object} source    Source object for derivation.
	 * @param {...*}   extraArgs Additional arguments to pass to selector.
	 *
	 * @return {*} Selector result.
	 */
	function callSelector( /* source, ...extraArgs */ ) {
		var len = arguments.length,
			cache, node, i, args, dependants;

		// Create copy of arguments (avoid leaking deoptimization).
		args = new Array( len );
		for ( i = 0; i < len; i++ ) {
			args[ i ] = arguments[ i ];
		}

		dependants = getDependants.apply( null, args );
		cache = getCache( dependants );

		// If not guaranteed uniqueness by dependants (primitive type or lack
		// of WeakMap support), shallow compare against last dependants and, if
		// references have changed, destroy cache to recalculate result.
		if ( ! cache.isUniqueByDependants ) {
			if ( cache.lastDependants && ! isShallowEqual( dependants, cache.lastDependants, 0 ) ) {
				cache.clear();
			}

			cache.lastDependants = dependants;
		}

		node = cache.head;
		while ( node ) {
			// Check whether node arguments match arguments
			if ( ! isShallowEqual( node.args, args, 1 ) ) {
				node = node.next;
				continue;
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if ( node !== cache.head ) {
				// Adjust siblings to point to each other.
				node.prev.next = node.next;
				if ( node.next ) {
					node.next.prev = node.prev;
				}

				node.next = cache.head;
				node.prev = null;
				cache.head.prev = node;
				cache.head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		node = {
			// Generate the result from original function
			val: selector.apply( null, args ),
		};

		// Avoid including the source object in the cache.
		args[ 0 ] = null;
		node.args = args;

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if ( cache.head ) {
			cache.head.prev = node;
			node.next = cache.head;
		}

		cache.head = node;

		return node.val;
	}

	callSelector.getDependants = getDependants;
	callSelector.clear = clear;
	clear();

	return callSelector;
});


/***/ }),

/***/ "qRz9":
/***/ (function(module, exports) {

(function() { module.exports = window["wp"]["richText"]; }());

/***/ })

/******/ });