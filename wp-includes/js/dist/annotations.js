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

(function() { module.exports = this["wp"]["data"]; }());

/***/ }),

/***/ "23Y4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// NAMESPACE OBJECT: ./node_modules/@wordpress/annotations/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetAnnotationsForBlock", function() { return __experimentalGetAnnotationsForBlock; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetAllAnnotationsForBlock", function() { return selectors_experimentalGetAllAnnotationsForBlock; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetAnnotationsForRichText", function() { return __experimentalGetAnnotationsForRichText; });
__webpack_require__.d(selectors_namespaceObject, "__experimentalGetAnnotations", function() { return __experimentalGetAnnotations; });

// NAMESPACE OBJECT: ./node_modules/@wordpress/annotations/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, "__experimentalAddAnnotation", function() { return __experimentalAddAnnotation; });
__webpack_require__.d(actions_namespaceObject, "__experimentalRemoveAnnotation", function() { return __experimentalRemoveAnnotation; });
__webpack_require__.d(actions_namespaceObject, "__experimentalUpdateAnnotationRange", function() { return __experimentalUpdateAnnotationRange; });
__webpack_require__.d(actions_namespaceObject, "__experimentalRemoveAnnotationsBySource", function() { return __experimentalRemoveAnnotationsBySource; });

// EXTERNAL MODULE: external {"this":["wp","data"]}
var external_this_wp_data_ = __webpack_require__("1ZqX");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/defineProperty.js
var defineProperty = __webpack_require__("rePB");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js + 2 modules
var toConsumableArray = __webpack_require__("KQm4");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__("vpQ4");

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
  var filteredCollection = collection.filter(predicate);
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
 * @param {Array} state The annotations currently shown in the editor.
 * @param {Object} action Dispatched action.
 *
 * @return {Array} Updated state.
 */


function reducer_annotations() {
  var state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  var action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'ANNOTATION_ADD':
      var blockClientId = action.blockClientId;
      var newAnnotation = {
        id: action.id,
        blockClientId: blockClientId,
        richTextIdentifier: action.richTextIdentifier,
        source: action.source,
        selector: action.selector,
        range: action.range
      };

      if (newAnnotation.selector === 'range' && !isValidAnnotationRange(newAnnotation.range)) {
        return state;
      }

      var previousAnnotationsForBlock = Object(external_lodash_["get"])(state, blockClientId, []);
      return Object(objectSpread["a" /* default */])({}, state, Object(defineProperty["a" /* default */])({}, blockClientId, Object(toConsumableArray["a" /* default */])(previousAnnotationsForBlock).concat([newAnnotation])));

    case 'ANNOTATION_REMOVE':
      return Object(external_lodash_["mapValues"])(state, function (annotationsForBlock) {
        return filterWithReference(annotationsForBlock, function (annotation) {
          return annotation.id !== action.annotationId;
        });
      });

    case 'ANNOTATION_UPDATE_RANGE':
      return Object(external_lodash_["mapValues"])(state, function (annotationsForBlock) {
        var hasChangedRange = false;
        var newAnnotations = annotationsForBlock.map(function (annotation) {
          if (annotation.id === action.annotationId) {
            hasChangedRange = true;
            return Object(objectSpread["a" /* default */])({}, annotation, {
              range: {
                start: action.start,
                end: action.end
              }
            });
          }

          return annotation;
        });
        return hasChangedRange ? newAnnotations : annotationsForBlock;
      });

    case 'ANNOTATION_REMOVE_SOURCE':
      return Object(external_lodash_["mapValues"])(state, function (annotationsForBlock) {
        return filterWithReference(annotationsForBlock, function (annotation) {
          return annotation.source !== action.source;
        });
      });
  }

  return state;
}
/* harmony default export */ var reducer = (reducer_annotations);

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js + 1 modules
var objectWithoutProperties = __webpack_require__("Ff2n");

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

var EMPTY_ARRAY = [];
/**
 * Returns the annotations for a specific client ID.
 *
 * @param {Object} state Editor state.
 * @param {string} clientId The ID of the block to get the annotations for.
 *
 * @return {Array} The annotations applicable to this block.
 */

var __experimentalGetAnnotationsForBlock = Object(rememo["a" /* default */])(function (state, blockClientId) {
  return Object(external_lodash_["get"])(state, blockClientId, []).filter(function (annotation) {
    return annotation.selector === 'block';
  });
}, function (state, blockClientId) {
  return [Object(external_lodash_["get"])(state, blockClientId, EMPTY_ARRAY)];
});
var selectors_experimentalGetAllAnnotationsForBlock = function __experimentalGetAllAnnotationsForBlock(state, blockClientId) {
  return Object(external_lodash_["get"])(state, blockClientId, EMPTY_ARRAY);
};
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

var __experimentalGetAnnotationsForRichText = Object(rememo["a" /* default */])(function (state, blockClientId, richTextIdentifier) {
  return Object(external_lodash_["get"])(state, blockClientId, []).filter(function (annotation) {
    return annotation.selector === 'range' && richTextIdentifier === annotation.richTextIdentifier;
  }).map(function (annotation) {
    var range = annotation.range,
        other = Object(objectWithoutProperties["a" /* default */])(annotation, ["range"]);

    return Object(objectSpread["a" /* default */])({}, range, other);
  });
}, function (state, blockClientId) {
  return [Object(external_lodash_["get"])(state, blockClientId, EMPTY_ARRAY)];
});
/**
 * Returns all annotations in the editor state.
 *
 * @param {Object} state Editor state.
 * @return {Array} All annotations currently applied.
 */

function __experimentalGetAnnotations(state) {
  return Object(external_lodash_["flatMap"])(state, function (annotations) {
    return annotations;
  });
}

// EXTERNAL MODULE: ./node_modules/uuid/v4.js
var v4 = __webpack_require__("xk4V");
var v4_default = /*#__PURE__*/__webpack_require__.n(v4);

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/store/actions.js
/**
 * External dependencies
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
 * @param {Object} annotation         The annotation to add.
 * @param {string} blockClientId      The blockClientId to add the annotation to.
 * @param {string} richTextIdentifier Identifier for the RichText instance the annotation applies to.
 * @param {Object} range              The range at which to apply this annotation.
 * @param {number} range.start        The offset where the annotation should start.
 * @param {number} range.end          The offset where the annotation should end.
 * @param {string} [selector="range"] The way to apply this annotation.
 * @param {string} [source="default"] The source that added the annotation.
 * @param {string} [id=uuid()]        The ID the annotation should have.
 *                                    Generates a UUID by default.
 *
 * @return {Object} Action object.
 */

function __experimentalAddAnnotation(_ref) {
  var blockClientId = _ref.blockClientId,
      _ref$richTextIdentifi = _ref.richTextIdentifier,
      richTextIdentifier = _ref$richTextIdentifi === void 0 ? null : _ref$richTextIdentifi,
      _ref$range = _ref.range,
      range = _ref$range === void 0 ? null : _ref$range,
      _ref$selector = _ref.selector,
      selector = _ref$selector === void 0 ? 'range' : _ref$selector,
      _ref$source = _ref.source,
      source = _ref$source === void 0 ? 'default' : _ref$source,
      _ref$id = _ref.id,
      id = _ref$id === void 0 ? v4_default()() : _ref$id;
  var action = {
    type: 'ANNOTATION_ADD',
    id: id,
    blockClientId: blockClientId,
    richTextIdentifier: richTextIdentifier,
    source: source,
    selector: selector
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
    annotationId: annotationId
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
    annotationId: annotationId,
    start: start,
    end: end
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
    source: source
  };
}

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/store/index.js
/**
 * WordPress Dependencies
 */

/**
 * Internal dependencies
 */




/**
 * Module Constants
 */

var MODULE_KEY = 'core/annotations';
var store = Object(external_this_wp_data_["registerStore"])(MODULE_KEY, {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject
});
/* harmony default export */ var build_module_store = (store);

// EXTERNAL MODULE: external {"this":["wp","richText"]}
var external_this_wp_richText_ = __webpack_require__("qRz9");

// EXTERNAL MODULE: ./node_modules/memize/index.js
var memize = __webpack_require__("4eJC");
var memize_default = /*#__PURE__*/__webpack_require__.n(memize);

// EXTERNAL MODULE: external {"this":["wp","i18n"]}
var external_this_wp_i18n_ = __webpack_require__("l3Sj");

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/format/annotation.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */



var FORMAT_NAME = 'core/annotation';
var ANNOTATION_ATTRIBUTE_PREFIX = 'annotation-text-';
var STORE_KEY = 'core/annotations';
/**
 * Applies given annotations to the given record.
 *
 * @param {Object} record The record to apply annotations to.
 * @param {Array} annotations The annotation to apply.
 * @return {Object} A record with the annotations applied.
 */

function applyAnnotations(record) {
  var annotations = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];
  annotations.forEach(function (annotation) {
    var start = annotation.start,
        end = annotation.end;

    if (start > record.text.length) {
      start = record.text.length;
    }

    if (end > record.text.length) {
      end = record.text.length;
    }

    var className = ANNOTATION_ATTRIBUTE_PREFIX + annotation.source;
    var id = ANNOTATION_ATTRIBUTE_PREFIX + annotation.id;
    record = Object(external_this_wp_richText_["applyFormat"])(record, {
      type: FORMAT_NAME,
      attributes: {
        className: className,
        id: id
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
  return Object(external_this_wp_richText_["removeFormat"])(record, 'core/annotation', 0, record.text.length);
}
/**
 * Retrieves the positions of annotations inside an array of formats.
 *
 * @param {Array} formats Formats with annotations in there.
 * @return {Object} ID keyed positions of annotations.
 */

function retrieveAnnotationPositions(formats) {
  var positions = {};
  formats.forEach(function (characterFormats, i) {
    characterFormats = characterFormats || [];
    characterFormats = characterFormats.filter(function (format) {
      return format.type === FORMAT_NAME;
    });
    characterFormats.forEach(function (format) {
      var id = format.attributes.id;
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
 * @param {Array}    annotations           The annotations that are currently applied.
 * @param {Array}    positions             The current positions of the given annotations.
 * @param {Function} removeAnnotation      Function to remove an annotation from the state.
 * @param {Function} updateAnnotationRange Function to update an annotation range in the state.
 */


function updateAnnotationsWithPositions(annotations, positions, _ref) {
  var removeAnnotation = _ref.removeAnnotation,
      updateAnnotationRange = _ref.updateAnnotationRange;
  annotations.forEach(function (currentAnnotation) {
    var position = positions[currentAnnotation.id]; // If we cannot find an annotation, delete it.

    if (!position) {
      // Apparently the annotation has been removed, so remove it from the state:
      // Remove...
      removeAnnotation(currentAnnotation.id);
      return;
    }

    var start = currentAnnotation.start,
        end = currentAnnotation.end;

    if (start !== position.start || end !== position.end) {
      updateAnnotationRange(currentAnnotation.id, position.start, position.end);
    }
  });
}
/**
 * Create prepareEditableTree memoized based on the annotation props.
 *
 * @param {Object} The props with annotations in them.
 *
 * @return {Function} The prepareEditableTree.
 */


var createPrepareEditableTree = memize_default()(function (props) {
  var annotations = props.annotations;
  return function (formats, text) {
    if (annotations.length === 0) {
      return formats;
    }

    var record = {
      formats: formats,
      text: text
    };
    record = applyAnnotations(record, annotations);
    return record.formats;
  };
});
/**
 * Returns the annotations as a props object. Memoized to prevent re-renders.
 *
 * @param {Array} The annotations to put in the object.
 *
 * @return {Object} The annotations props object.
 */

var getAnnotationObject = memize_default()(function (annotations) {
  return {
    annotations: annotations
  };
});
var annotation_annotation = {
  name: FORMAT_NAME,
  title: Object(external_this_wp_i18n_["__"])('Annotation'),
  tagName: 'mark',
  className: 'annotation-text',
  attributes: {
    className: 'class',
    id: 'id'
  },
  edit: function edit() {
    return null;
  },
  __experimentalGetPropsForEditableTreePreparation: function __experimentalGetPropsForEditableTreePreparation(select, _ref2) {
    var richTextIdentifier = _ref2.richTextIdentifier,
        blockClientId = _ref2.blockClientId;
    return getAnnotationObject(select(STORE_KEY).__experimentalGetAnnotationsForRichText(blockClientId, richTextIdentifier));
  },
  __experimentalCreatePrepareEditableTree: createPrepareEditableTree,
  __experimentalGetPropsForEditableTreeChangeHandler: function __experimentalGetPropsForEditableTreeChangeHandler(dispatch) {
    return {
      removeAnnotation: dispatch(STORE_KEY).__experimentalRemoveAnnotation,
      updateAnnotationRange: dispatch(STORE_KEY).__experimentalUpdateAnnotationRange
    };
  },
  __experimentalCreateOnChangeEditableValue: function __experimentalCreateOnChangeEditableValue(props) {
    return function (formats) {
      var positions = retrieveAnnotationPositions(formats);
      var removeAnnotation = props.removeAnnotation,
          updateAnnotationRange = props.updateAnnotationRange,
          annotations = props.annotations;
      updateAnnotationsWithPositions(annotations, positions, {
        removeAnnotation: removeAnnotation,
        updateAnnotationRange: updateAnnotationRange
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



var format_name = annotation_annotation.name,
    settings = Object(objectWithoutProperties["a" /* default */])(annotation_annotation, ["name"]);

Object(external_this_wp_richText_["registerFormatType"])(format_name, settings);

// EXTERNAL MODULE: external {"this":["wp","hooks"]}
var external_this_wp_hooks_ = __webpack_require__("g56x");

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/block/index.js
/**
 * WordPress dependencies
 */


/**
 * Adds annotation className to the block-list-block component.
 *
 * @param {Object} OriginalComponent The original BlockListBlock component.
 * @return {Object} The enhanced component.
 */

var block_addAnnotationClassName = function addAnnotationClassName(OriginalComponent) {
  return Object(external_this_wp_data_["withSelect"])(function (select, _ref) {
    var clientId = _ref.clientId;

    var annotations = select('core/annotations').__experimentalGetAnnotationsForBlock(clientId);

    return {
      className: annotations.map(function (annotation) {
        return 'is-annotated-by-' + annotation.source;
      }).join(' ')
    };
  })(OriginalComponent);
};

Object(external_this_wp_hooks_["addFilter"])('editor.BlockListBlock', 'core/annotations', block_addAnnotationClassName);

// CONCATENATED MODULE: ./node_modules/@wordpress/annotations/build-module/index.js
/**
 * Internal dependencies
 */





/***/ }),

/***/ "25BE":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _iterableToArray; });
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && Symbol.iterator in Object(iter)) return Array.from(iter);
}

/***/ }),

/***/ "4eJC":
/***/ (function(module, exports, __webpack_require__) {

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
 * @template {Function} F
 *
 * @param {F}             fn        Function to memoize.
 * @param {MemizeOptions} [options] Options object.
 *
 * @return {F & MemizeMemoizedFunction} Memoized function.
 */
function memize( fn, options ) {
	var size = 0;

	/** @type {?MemizeCacheNode|undefined} */
	var head;

	/** @type {?MemizeCacheNode|undefined} */
	var tail;

	options = options || {};

	function memoized( /* ...args */ ) {
		var node = head,
			len = arguments.length,
			args, i;

		searchCache: while ( node ) {
			// Perform a shallow equality test to confirm that whether the node
			// under test is a candidate for the arguments passed. Two arrays
			// are shallowly equal if their length matches and each entry is
			// strictly equal between the two sets. Avoid abstracting to a
			// function which could incur an arguments leaking deoptimization.

			// Check whether node arguments match arguments length
			if ( node.args.length !== arguments.length ) {
				node = node.next;
				continue;
			}

			// Check whether node arguments match arguments values
			for ( i = 0; i < len; i++ ) {
				if ( node.args[ i ] !== arguments[ i ] ) {
					node = node.next;
					continue searchCache;
				}
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if ( node !== head ) {
				// As tail, shift to previous. Must only shift if not also
				// head, since if both head and tail, there is no previous.
				if ( node === tail ) {
					tail = node.prev;
				}

				// Adjust siblings to point to each other. If node was tail,
				// this also handles new tail's empty `next` assignment.
				/** @type {MemizeCacheNode} */ ( node.prev ).next = node.next;
				if ( node.next ) {
					node.next.prev = node.prev;
				}

				node.next = head;
				node.prev = null;
				/** @type {MemizeCacheNode} */ ( head ).prev = node;
				head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		// Create a copy of arguments (avoid leaking deoptimization)
		args = new Array( len );
		for ( i = 0; i < len; i++ ) {
			args[ i ] = arguments[ i ];
		}

		node = {
			args: args,

			// Generate the result from original function
			val: fn.apply( null, args ),
		};

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if ( head ) {
			head.prev = node;
			node.next = head;
		} else {
			// If no head, follows that there's no tail (at initial or reset)
			tail = node;
		}

		// Trim tail if we're reached max size and are pending cache insertion
		if ( size === /** @type {MemizeOptions} */ ( options ).maxSize ) {
			tail = /** @type {MemizeCacheNode} */ ( tail ).prev;
			/** @type {MemizeCacheNode} */ ( tail ).next = null;
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

	if ( false ) {}

	// Ignore reason: There's not a clear solution to create an intersection of
	// the function with additional properties, where the goal is to retain the
	// function signature of the incoming argument and add control properties
	// on the return value.

	// @ts-ignore
	return memoized;
}

module.exports = memize;


/***/ }),

/***/ "4fRq":
/***/ (function(module, exports) {

// Unique ID creation requires a high quality random # generator.  In the
// browser this is a little complicated due to unknown quality of Math.random()
// and inconsistent support for the `crypto` API.  We do the best we can via
// feature-detection

// getRandomValues needs to be invoked in a context where "this" is a Crypto
// implementation. Also, find the complete implementation of crypto on IE11.
var getRandomValues = (typeof(crypto) != 'undefined' && crypto.getRandomValues && crypto.getRandomValues.bind(crypto)) ||
                      (typeof(msCrypto) != 'undefined' && typeof window.msCrypto.getRandomValues == 'function' && msCrypto.getRandomValues.bind(msCrypto));

if (getRandomValues) {
  // WHATWG crypto RNG - http://wiki.whatwg.org/wiki/Crypto
  var rnds8 = new Uint8Array(16); // eslint-disable-line no-undef

  module.exports = function whatwgRNG() {
    getRandomValues(rnds8);
    return rnds8;
  };
} else {
  // Math.random()-based (RNG)
  //
  // If all else fails, use Math.random().  It's fast, but is of unspecified
  // quality.
  var rnds = new Array(16);

  module.exports = function mathRNG() {
    for (var i = 0, r; i < 16; i++) {
      if ((i & 0x03) === 0) r = Math.random() * 0x100000000;
      rnds[i] = r >>> ((i & 0x03) << 3) & 0xff;
    }

    return rnds;
  };
}


/***/ }),

/***/ "BsWD":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _unsupportedIterableToArray; });
/* harmony import */ var _babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("a3WO");

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return Object(_babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return Object(_babel_runtime_helpers_esm_arrayLikeToArray__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(o, minLen);
}

/***/ }),

/***/ "Ff2n":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _objectWithoutProperties; });

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutPropertiesLoose.js
function _objectWithoutPropertiesLoose(source, excluded) {
  if (source == null) return {};
  var target = {};
  var sourceKeys = Object.keys(source);
  var key, i;

  for (i = 0; i < sourceKeys.length; i++) {
    key = sourceKeys[i];
    if (excluded.indexOf(key) >= 0) continue;
    target[key] = source[key];
  }

  return target;
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js

function _objectWithoutProperties(source, excluded) {
  if (source == null) return {};
  var target = _objectWithoutPropertiesLoose(source, excluded);
  var key, i;

  if (Object.getOwnPropertySymbols) {
    var sourceSymbolKeys = Object.getOwnPropertySymbols(source);

    for (i = 0; i < sourceSymbolKeys.length; i++) {
      key = sourceSymbolKeys[i];
      if (excluded.indexOf(key) >= 0) continue;
      if (!Object.prototype.propertyIsEnumerable.call(source, key)) continue;
      target[key] = source[key];
    }
  }

  return target;
}

/***/ }),

/***/ "I2ZF":
/***/ (function(module, exports) {

/**
 * Convert array of 16 byte values to UUID string format of the form:
 * XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
 */
var byteToHex = [];
for (var i = 0; i < 256; ++i) {
  byteToHex[i] = (i + 0x100).toString(16).substr(1);
}

function bytesToUuid(buf, offset) {
  var i = offset || 0;
  var bth = byteToHex;
  // join used to fix memory issue caused by concatenation: https://bugs.chromium.org/p/v8/issues/detail?id=3175#c4
  return ([
    bth[buf[i++]], bth[buf[i++]],
    bth[buf[i++]], bth[buf[i++]], '-',
    bth[buf[i++]], bth[buf[i++]], '-',
    bth[buf[i++]], bth[buf[i++]], '-',
    bth[buf[i++]], bth[buf[i++]], '-',
    bth[buf[i++]], bth[buf[i++]],
    bth[buf[i++]], bth[buf[i++]],
    bth[buf[i++]], bth[buf[i++]]
  ]).join('');
}

module.exports = bytesToUuid;


/***/ }),

/***/ "KQm4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";

// EXPORTS
__webpack_require__.d(__webpack_exports__, "a", function() { return /* binding */ _toConsumableArray; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js
var arrayLikeToArray = __webpack_require__("a3WO");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return Object(arrayLikeToArray["a" /* default */])(arr);
}
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js
var iterableToArray = __webpack_require__("25BE");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js
var unsupportedIterableToArray = __webpack_require__("BsWD");

// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}
// CONCATENATED MODULE: ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js




function _toConsumableArray(arr) {
  return _arrayWithoutHoles(arr) || Object(iterableToArray["a" /* default */])(arr) || Object(unsupportedIterableToArray["a" /* default */])(arr) || _nonIterableSpread();
}

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ "a3WO":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _arrayLikeToArray; });
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}

/***/ }),

/***/ "g56x":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["hooks"]; }());

/***/ }),

/***/ "l3Sj":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["i18n"]; }());

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

(function() { module.exports = this["wp"]["richText"]; }());

/***/ }),

/***/ "rePB":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _defineProperty; });
function _defineProperty(obj, key, value) {
  if (key in obj) {
    Object.defineProperty(obj, key, {
      value: value,
      enumerable: true,
      configurable: true,
      writable: true
    });
  } else {
    obj[key] = value;
  }

  return obj;
}

/***/ }),

/***/ "vpQ4":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _objectSpread; });
/* harmony import */ var _babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__("rePB");

function _objectSpread(target) {
  for (var i = 1; i < arguments.length; i++) {
    var source = arguments[i] != null ? Object(arguments[i]) : {};
    var ownKeys = Object.keys(source);

    if (typeof Object.getOwnPropertySymbols === 'function') {
      ownKeys = ownKeys.concat(Object.getOwnPropertySymbols(source).filter(function (sym) {
        return Object.getOwnPropertyDescriptor(source, sym).enumerable;
      }));
    }

    ownKeys.forEach(function (key) {
      Object(_babel_runtime_helpers_esm_defineProperty__WEBPACK_IMPORTED_MODULE_0__[/* default */ "a"])(target, key, source[key]);
    });
  }

  return target;
}

/***/ }),

/***/ "xk4V":
/***/ (function(module, exports, __webpack_require__) {

var rng = __webpack_require__("4fRq");
var bytesToUuid = __webpack_require__("I2ZF");

function v4(options, buf, offset) {
  var i = buf && offset || 0;

  if (typeof(options) == 'string') {
    buf = options === 'binary' ? new Array(16) : null;
    options = null;
  }
  options = options || {};

  var rnds = options.random || (options.rng || rng)();

  // Per 4.4, set bits for version and `clock_seq_hi_and_reserved`
  rnds[6] = (rnds[6] & 0x0f) | 0x40;
  rnds[8] = (rnds[8] & 0x3f) | 0x80;

  // Copy bytes to buffer, if provided
  if (buf) {
    for (var ii = 0; ii < 16; ++ii) {
      buf[i + ii] = rnds[ii];
    }
  }

  return buf || bytesToUuid(rnds);
}

module.exports = v4;


/***/ })

/******/ });