/******/ (function() { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/define property getters */
/******/ 	!function() {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = function(exports, definition) {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	!function() {
/******/ 		__webpack_require__.o = function(obj, prop) { return Object.prototype.hasOwnProperty.call(obj, prop); }
/******/ 	}();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	!function() {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = function(exports) {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	}();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "__UNSTABLE_LINE_SEPARATOR": function() { return /* reexport */ LINE_SEPARATOR; },
  "__experimentalRichText": function() { return /* reexport */ __experimentalRichText; },
  "__unstableCanIndentListItems": function() { return /* reexport */ canIndentListItems; },
  "__unstableCanOutdentListItems": function() { return /* reexport */ canOutdentListItems; },
  "__unstableChangeListType": function() { return /* reexport */ changeListType; },
  "__unstableCreateElement": function() { return /* reexport */ createElement; },
  "__unstableFormatEdit": function() { return /* reexport */ FormatEdit; },
  "__unstableIndentListItems": function() { return /* reexport */ indentListItems; },
  "__unstableInsertLineSeparator": function() { return /* reexport */ insertLineSeparator; },
  "__unstableIsActiveListType": function() { return /* reexport */ isActiveListType; },
  "__unstableIsEmptyLine": function() { return /* reexport */ isEmptyLine; },
  "__unstableIsListRootSelected": function() { return /* reexport */ isListRootSelected; },
  "__unstableOutdentListItems": function() { return /* reexport */ outdentListItems; },
  "__unstableRemoveLineSeparator": function() { return /* reexport */ removeLineSeparator; },
  "__unstableToDom": function() { return /* reexport */ toDom; },
  "__unstableUseRichText": function() { return /* reexport */ useRichText; },
  "applyFormat": function() { return /* reexport */ applyFormat; },
  "concat": function() { return /* reexport */ concat; },
  "create": function() { return /* reexport */ create; },
  "getActiveFormat": function() { return /* reexport */ getActiveFormat; },
  "getActiveObject": function() { return /* reexport */ getActiveObject; },
  "getTextContent": function() { return /* reexport */ getTextContent; },
  "insert": function() { return /* reexport */ insert; },
  "insertObject": function() { return /* reexport */ insertObject; },
  "isCollapsed": function() { return /* reexport */ isCollapsed; },
  "isEmpty": function() { return /* reexport */ isEmpty; },
  "join": function() { return /* reexport */ join; },
  "registerFormatType": function() { return /* reexport */ registerFormatType; },
  "remove": function() { return /* reexport */ remove; },
  "removeFormat": function() { return /* reexport */ removeFormat; },
  "replace": function() { return /* reexport */ replace_replace; },
  "slice": function() { return /* reexport */ slice; },
  "split": function() { return /* reexport */ split; },
  "store": function() { return /* reexport */ store; },
  "toHTMLString": function() { return /* reexport */ toHTMLString; },
  "toggleFormat": function() { return /* reexport */ toggleFormat; },
  "unregisterFormatType": function() { return /* reexport */ unregisterFormatType; },
  "useAnchorRef": function() { return /* reexport */ useAnchorRef; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/rich-text/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  "getFormatType": function() { return getFormatType; },
  "getFormatTypeForBareElement": function() { return getFormatTypeForBareElement; },
  "getFormatTypeForClassName": function() { return getFormatTypeForClassName; },
  "getFormatTypes": function() { return getFormatTypes; }
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/rich-text/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  "addFormatTypes": function() { return addFormatTypes; },
  "removeFormatTypes": function() { return removeFormatTypes; }
});

;// CONCATENATED MODULE: external ["wp","data"]
var external_wp_data_namespaceObject = window["wp"]["data"];
;// CONCATENATED MODULE: external "lodash"
var external_lodash_namespaceObject = window["lodash"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/reducer.js
/**
 * External dependencies
 */

/**
 * WordPress dependencies
 */


/**
 * Reducer managing the format types
 *
 * @param {Object} state  Current state.
 * @param {Object} action Dispatched action.
 *
 * @return {Object} Updated state.
 */

function formatTypes() {
  let state = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};
  let action = arguments.length > 1 ? arguments[1] : undefined;

  switch (action.type) {
    case 'ADD_FORMAT_TYPES':
      return { ...state,
        ...(0,external_lodash_namespaceObject.keyBy)(action.formatTypes, 'name')
      };

    case 'REMOVE_FORMAT_TYPES':
      return (0,external_lodash_namespaceObject.omit)(state, action.names);
  }

  return state;
}
/* harmony default export */ var reducer = ((0,external_wp_data_namespaceObject.combineReducers)({
  formatTypes
}));

;// CONCATENATED MODULE: ./node_modules/rememo/es/rememo.js


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
/* harmony default export */ function rememo(selector, getDependants ) {
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
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/selectors.js
/**
 * External dependencies
 */


/**
 * Returns all the available format types.
 *
 * @param {Object} state Data state.
 *
 * @return {Array} Format types.
 */

const getFormatTypes = rememo(state => Object.values(state.formatTypes), state => [state.formatTypes]);
/**
 * Returns a format type by name.
 *
 * @param {Object} state Data state.
 * @param {string} name  Format type name.
 *
 * @return {Object?} Format type.
 */

function getFormatType(state, name) {
  return state.formatTypes[name];
}
/**
 * Gets the format type, if any, that can handle a bare element (without a
 * data-format-type attribute), given the tag name of this element.
 *
 * @param {Object} state              Data state.
 * @param {string} bareElementTagName The tag name of the element to find a
 *                                    format type for.
 * @return {?Object} Format type.
 */

function getFormatTypeForBareElement(state, bareElementTagName) {
  return (0,external_lodash_namespaceObject.find)(getFormatTypes(state), _ref => {
    let {
      className,
      tagName
    } = _ref;
    return className === null && bareElementTagName === tagName;
  });
}
/**
 * Gets the format type, if any, that can handle an element, given its classes.
 *
 * @param {Object} state            Data state.
 * @param {string} elementClassName The classes of the element to find a format
 *                                  type for.
 * @return {?Object} Format type.
 */

function getFormatTypeForClassName(state, elementClassName) {
  return (0,external_lodash_namespaceObject.find)(getFormatTypes(state), _ref2 => {
    let {
      className
    } = _ref2;

    if (className === null) {
      return false;
    }

    return ` ${elementClassName} `.indexOf(` ${className} `) >= 0;
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/actions.js
/**
 * External dependencies
 */

/**
 * Returns an action object used in signalling that format types have been
 * added.
 *
 * @param {Array|Object} formatTypes Format types received.
 *
 * @return {Object} Action object.
 */

function addFormatTypes(formatTypes) {
  return {
    type: 'ADD_FORMAT_TYPES',
    formatTypes: (0,external_lodash_namespaceObject.castArray)(formatTypes)
  };
}
/**
 * Returns an action object used to remove a registered format type.
 *
 * @param {string|Array} names Format name.
 *
 * @return {Object} Action object.
 */

function removeFormatTypes(names) {
  return {
    type: 'REMOVE_FORMAT_TYPES',
    names: (0,external_lodash_namespaceObject.castArray)(names)
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/store/index.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */




const STORE_NAME = 'core/rich-text';
/**
 * Store definition for the rich-text namespace.
 *
 * @see https://github.com/WordPress/gutenberg/blob/HEAD/packages/data/README.md#createReduxStore
 *
 * @type {Object}
 */

const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-format-equal.js
/** @typedef {import('./create').RichTextFormat} RichTextFormat */

/**
 * Optimised equality check for format objects.
 *
 * @param {?RichTextFormat} format1 Format to compare.
 * @param {?RichTextFormat} format2 Format to compare.
 *
 * @return {boolean} True if formats are equal, false if not.
 */
function isFormatEqual(format1, format2) {
  // Both not defined.
  if (format1 === format2) {
    return true;
  } // Either not defined.


  if (!format1 || !format2) {
    return false;
  }

  if (format1.type !== format2.type) {
    return false;
  }

  const attributes1 = format1.attributes;
  const attributes2 = format2.attributes; // Both not defined.

  if (attributes1 === attributes2) {
    return true;
  } // Either not defined.


  if (!attributes1 || !attributes2) {
    return false;
  }

  const keys1 = Object.keys(attributes1);
  const keys2 = Object.keys(attributes2);

  if (keys1.length !== keys2.length) {
    return false;
  }

  const length = keys1.length; // Optimise for speed.

  for (let i = 0; i < length; i++) {
    const name = keys1[i];

    if (attributes1[name] !== attributes2[name]) {
      return false;
    }
  }

  return true;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/normalise-formats.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Normalises formats: ensures subsequent adjacent equal formats have the same
 * reference.
 *
 * @param {RichTextValue} value Value to normalise formats of.
 *
 * @return {RichTextValue} New value with normalised formats.
 */

function normaliseFormats(value) {
  const newFormats = value.formats.slice();
  newFormats.forEach((formatsAtIndex, index) => {
    const formatsAtPreviousIndex = newFormats[index - 1];

    if (formatsAtPreviousIndex) {
      const newFormatsAtIndex = formatsAtIndex.slice();
      newFormatsAtIndex.forEach((format, formatIndex) => {
        const previousFormat = formatsAtPreviousIndex[formatIndex];

        if (isFormatEqual(format, previousFormat)) {
          newFormatsAtIndex[formatIndex] = previousFormat;
        }
      });
      newFormats[index] = newFormatsAtIndex;
    }
  });
  return { ...value,
    formats: newFormats
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/apply-format.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/** @typedef {import('./create').RichTextValue} RichTextValue */

/** @typedef {import('./create').RichTextFormat} RichTextFormat */

function replace(array, index, value) {
  array = array.slice();
  array[index] = value;
  return array;
}
/**
 * Apply a format object to a Rich Text value from the given `startIndex` to the
 * given `endIndex`. Indices are retrieved from the selection if none are
 * provided.
 *
 * @param {RichTextValue}  value        Value to modify.
 * @param {RichTextFormat} format       Format to apply.
 * @param {number}         [startIndex] Start index.
 * @param {number}         [endIndex]   End index.
 *
 * @return {RichTextValue} A new value with the format applied.
 */


function applyFormat(value, format) {
  let startIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : value.start;
  let endIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : value.end;
  const {
    formats,
    activeFormats
  } = value;
  const newFormats = formats.slice(); // The selection is collapsed.

  if (startIndex === endIndex) {
    const startFormat = (0,external_lodash_namespaceObject.find)(newFormats[startIndex], {
      type: format.type
    }); // If the caret is at a format of the same type, expand start and end to
    // the edges of the format. This is useful to apply new attributes.

    if (startFormat) {
      const index = newFormats[startIndex].indexOf(startFormat);

      while (newFormats[startIndex] && newFormats[startIndex][index] === startFormat) {
        newFormats[startIndex] = replace(newFormats[startIndex], index, format);
        startIndex--;
      }

      endIndex++;

      while (newFormats[endIndex] && newFormats[endIndex][index] === startFormat) {
        newFormats[endIndex] = replace(newFormats[endIndex], index, format);
        endIndex++;
      }
    }
  } else {
    // Determine the highest position the new format can be inserted at.
    let position = +Infinity;

    for (let index = startIndex; index < endIndex; index++) {
      if (newFormats[index]) {
        newFormats[index] = newFormats[index].filter(_ref => {
          let {
            type
          } = _ref;
          return type !== format.type;
        });
        const length = newFormats[index].length;

        if (length < position) {
          position = length;
        }
      } else {
        newFormats[index] = [];
        position = 0;
      }
    }

    for (let index = startIndex; index < endIndex; index++) {
      newFormats[index].splice(position, 0, format);
    }
  }

  return normaliseFormats({ ...value,
    formats: newFormats,
    // Always revise active formats. This serves as a placeholder for new
    // inputs with the format so new input appears with the format applied,
    // and ensures a format of the same type uses the latest values.
    activeFormats: [...(0,external_lodash_namespaceObject.reject)(activeFormats, {
      type: format.type
    }), format]
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/create-element.js
/**
 * Parse the given HTML into a body element.
 *
 * Note: The current implementation will return a shared reference, reset on
 * each call to `createElement`. Therefore, you should not hold a reference to
 * the value to operate upon asynchronously, as it may have unexpected results.
 *
 * @param {HTMLDocument} document The HTML document to use to parse.
 * @param {string}       html     The HTML to parse.
 *
 * @return {HTMLBodyElement} Body element with parsed HTML.
 */
function createElement(_ref, html) {
  let {
    implementation
  } = _ref;

  // Because `createHTMLDocument` is an expensive operation, and with this
  // function being internal to `rich-text` (full control in avoiding a risk
  // of asynchronous operations on the shared reference), a single document
  // is reused and reset for each call to the function.
  if (!createElement.body) {
    createElement.body = implementation.createHTMLDocument('').body;
  }

  createElement.body.innerHTML = html;
  return createElement.body;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/special-characters.js
/**
 * Line separator character, used for multiline text.
 */
const LINE_SEPARATOR = '\u2028';
/**
 * Object replacement character, used as a placeholder for objects.
 */

const OBJECT_REPLACEMENT_CHARACTER = '\ufffc';
/**
 * Zero width non-breaking space, used as padding in the editable DOM tree when
 * it is empty otherwise.
 */

const ZWNBSP = '\ufeff';

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/create.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */





/**
 * @typedef {Object} RichTextFormat
 *
 * @property {string} type Format type.
 */

/**
 * @typedef {Array<RichTextFormat>} RichTextFormatList
 */

/**
 * @typedef {Object} RichTextValue
 *
 * @property {string}                    text         Text.
 * @property {Array<RichTextFormatList>} formats      Formats.
 * @property {Array<RichTextFormat>}     replacements Replacements.
 * @property {number|undefined}          start        Selection start.
 * @property {number|undefined}          end          Selection end.
 */

function createEmptyValue() {
  return {
    formats: [],
    replacements: [],
    text: ''
  };
}

function toFormat(_ref) {
  let {
    type,
    attributes
  } = _ref;
  let formatType;

  if (attributes && attributes.class) {
    formatType = (0,external_wp_data_namespaceObject.select)(store).getFormatTypeForClassName(attributes.class);

    if (formatType) {
      // Preserve any additional classes.
      attributes.class = ` ${attributes.class} `.replace(` ${formatType.className} `, ' ').trim();

      if (!attributes.class) {
        delete attributes.class;
      }
    }
  }

  if (!formatType) {
    formatType = (0,external_wp_data_namespaceObject.select)(store).getFormatTypeForBareElement(type);
  }

  if (!formatType) {
    return attributes ? {
      type,
      attributes
    } : {
      type
    };
  }

  if (formatType.__experimentalCreatePrepareEditableTree && !formatType.__experimentalCreateOnChangeEditableValue) {
    return null;
  }

  if (!attributes) {
    return {
      type: formatType.name
    };
  }

  const registeredAttributes = {};
  const unregisteredAttributes = {};
  const _attributes = { ...attributes
  };

  for (const key in formatType.attributes) {
    const name = formatType.attributes[key];
    registeredAttributes[key] = _attributes[name];

    if (formatType.__unstableFilterAttributeValue) {
      registeredAttributes[key] = formatType.__unstableFilterAttributeValue(key, registeredAttributes[key]);
    } // delete the attribute and what's left is considered
    // to be unregistered.


    delete _attributes[name];

    if (typeof registeredAttributes[key] === 'undefined') {
      delete registeredAttributes[key];
    }
  }

  for (const name in _attributes) {
    unregisteredAttributes[name] = attributes[name];
  }

  return {
    type: formatType.name,
    attributes: registeredAttributes,
    unregisteredAttributes
  };
}
/**
 * Create a RichText value from an `Element` tree (DOM), an HTML string or a
 * plain text string, with optionally a `Range` object to set the selection. If
 * called without any input, an empty value will be created. If
 * `multilineTag` is provided, any content of direct children whose type matches
 * `multilineTag` will be separated by two newlines. The optional functions can
 * be used to filter out content.
 *
 * A value will have the following shape, which you are strongly encouraged not
 * to modify without the use of helper functions:
 *
 * ```js
 * {
 *   text: string,
 *   formats: Array,
 *   replacements: Array,
 *   ?start: number,
 *   ?end: number,
 * }
 * ```
 *
 * As you can see, text and formatting are separated. `text` holds the text,
 * including any replacement characters for objects and lines. `formats`,
 * `objects` and `lines` are all sparse arrays of the same length as `text`. It
 * holds information about the formatting at the relevant text indices. Finally
 * `start` and `end` state which text indices are selected. They are only
 * provided if a `Range` was given.
 *
 * @param {Object}  [$1]                          Optional named arguments.
 * @param {Element} [$1.element]                  Element to create value from.
 * @param {string}  [$1.text]                     Text to create value from.
 * @param {string}  [$1.html]                     HTML to create value from.
 * @param {Range}   [$1.range]                    Range to create value from.
 * @param {string}  [$1.multilineTag]             Multiline tag if the structure is
 *                                                multiline.
 * @param {Array}   [$1.multilineWrapperTags]     Tags where lines can be found if
 *                                                nesting is possible.
 * @param {boolean} [$1.preserveWhiteSpace]       Whether or not to collapse white
 *                                                space characters.
 * @param {boolean} [$1.__unstableIsEditableTree]
 *
 * @return {RichTextValue} A rich text value.
 */


function create() {
  let {
    element,
    text,
    html,
    range,
    multilineTag,
    multilineWrapperTags,
    __unstableIsEditableTree: isEditableTree,
    preserveWhiteSpace
  } = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : {};

  if (typeof text === 'string' && text.length > 0) {
    return {
      formats: Array(text.length),
      replacements: Array(text.length),
      text
    };
  }

  if (typeof html === 'string' && html.length > 0) {
    // It does not matter which document this is, we're just using it to
    // parse.
    element = createElement(document, html);
  }

  if (typeof element !== 'object') {
    return createEmptyValue();
  }

  if (!multilineTag) {
    return createFromElement({
      element,
      range,
      isEditableTree,
      preserveWhiteSpace
    });
  }

  return createFromMultilineElement({
    element,
    range,
    multilineTag,
    multilineWrapperTags,
    isEditableTree,
    preserveWhiteSpace
  });
}
/**
 * Helper to accumulate the value's selection start and end from the current
 * node and range.
 *
 * @param {Object} accumulator Object to accumulate into.
 * @param {Node}   node        Node to create value with.
 * @param {Range}  range       Range to create value with.
 * @param {Object} value       Value that is being accumulated.
 */

function accumulateSelection(accumulator, node, range, value) {
  if (!range) {
    return;
  }

  const {
    parentNode
  } = node;
  const {
    startContainer,
    startOffset,
    endContainer,
    endOffset
  } = range;
  const currentLength = accumulator.text.length; // Selection can be extracted from value.

  if (value.start !== undefined) {
    accumulator.start = currentLength + value.start; // Range indicates that the current node has selection.
  } else if (node === startContainer && node.nodeType === node.TEXT_NODE) {
    accumulator.start = currentLength + startOffset; // Range indicates that the current node is selected.
  } else if (parentNode === startContainer && node === startContainer.childNodes[startOffset]) {
    accumulator.start = currentLength; // Range indicates that the selection is after the current node.
  } else if (parentNode === startContainer && node === startContainer.childNodes[startOffset - 1]) {
    accumulator.start = currentLength + value.text.length; // Fallback if no child inside handled the selection.
  } else if (node === startContainer) {
    accumulator.start = currentLength;
  } // Selection can be extracted from value.


  if (value.end !== undefined) {
    accumulator.end = currentLength + value.end; // Range indicates that the current node has selection.
  } else if (node === endContainer && node.nodeType === node.TEXT_NODE) {
    accumulator.end = currentLength + endOffset; // Range indicates that the current node is selected.
  } else if (parentNode === endContainer && node === endContainer.childNodes[endOffset - 1]) {
    accumulator.end = currentLength + value.text.length; // Range indicates that the selection is before the current node.
  } else if (parentNode === endContainer && node === endContainer.childNodes[endOffset]) {
    accumulator.end = currentLength; // Fallback if no child inside handled the selection.
  } else if (node === endContainer) {
    accumulator.end = currentLength + endOffset;
  }
}
/**
 * Adjusts the start and end offsets from a range based on a text filter.
 *
 * @param {Node}     node   Node of which the text should be filtered.
 * @param {Range}    range  The range to filter.
 * @param {Function} filter Function to use to filter the text.
 *
 * @return {Object|void} Object containing range properties.
 */


function filterRange(node, range, filter) {
  if (!range) {
    return;
  }

  const {
    startContainer,
    endContainer
  } = range;
  let {
    startOffset,
    endOffset
  } = range;

  if (node === startContainer) {
    startOffset = filter(node.nodeValue.slice(0, startOffset)).length;
  }

  if (node === endContainer) {
    endOffset = filter(node.nodeValue.slice(0, endOffset)).length;
  }

  return {
    startContainer,
    startOffset,
    endContainer,
    endOffset
  };
}
/**
 * Collapse any whitespace used for HTML formatting to one space character,
 * because it will also be displayed as such by the browser.
 *
 * @param {string} string
 */


function collapseWhiteSpace(string) {
  return string.replace(/[\n\r\t]+/g, ' ');
}
/**
 * Removes reserved characters used by rich-text (zero width non breaking spaces added by `toTree` and object replacement characters).
 *
 * @param {string} string
 */


function removeReservedCharacters(string) {
  // with the global flag, note that we should create a new regex each time OR reset lastIndex state.
  return string.replace(new RegExp(`[${ZWNBSP}${OBJECT_REPLACEMENT_CHARACTER}]`, 'gu'), '');
}
/**
 * Creates a Rich Text value from a DOM element and range.
 *
 * @param {Object}  $1                        Named argements.
 * @param {Element} [$1.element]              Element to create value from.
 * @param {Range}   [$1.range]                Range to create value from.
 * @param {string}  [$1.multilineTag]         Multiline tag if the structure is
 *                                            multiline.
 * @param {Array}   [$1.multilineWrapperTags] Tags where lines can be found if
 *                                            nesting is possible.
 * @param {boolean} [$1.preserveWhiteSpace]   Whether or not to collapse white
 *                                            space characters.
 * @param {Array}   [$1.currentWrapperTags]
 * @param {boolean} [$1.isEditableTree]
 *
 * @return {RichTextValue} A rich text value.
 */

function createFromElement(_ref2) {
  let {
    element,
    range,
    multilineTag,
    multilineWrapperTags,
    currentWrapperTags = [],
    isEditableTree,
    preserveWhiteSpace
  } = _ref2;
  const accumulator = createEmptyValue();

  if (!element) {
    return accumulator;
  }

  if (!element.hasChildNodes()) {
    accumulateSelection(accumulator, element, range, createEmptyValue());
    return accumulator;
  }

  const length = element.childNodes.length; // Optimise for speed.

  for (let index = 0; index < length; index++) {
    const node = element.childNodes[index];
    const type = node.nodeName.toLowerCase();

    if (node.nodeType === node.TEXT_NODE) {
      let filter = removeReservedCharacters;

      if (!preserveWhiteSpace) {
        filter = string => removeReservedCharacters(collapseWhiteSpace(string));
      }

      const text = filter(node.nodeValue);
      range = filterRange(node, range, filter);
      accumulateSelection(accumulator, node, range, {
        text
      }); // Create a sparse array of the same length as `text`, in which
      // formats can be added.

      accumulator.formats.length += text.length;
      accumulator.replacements.length += text.length;
      accumulator.text += text;
      continue;
    }

    if (node.nodeType !== node.ELEMENT_NODE) {
      continue;
    }

    if (isEditableTree && ( // Ignore any placeholders.
    node.getAttribute('data-rich-text-placeholder') || // Ignore any line breaks that are not inserted by us.
    type === 'br' && !node.getAttribute('data-rich-text-line-break'))) {
      accumulateSelection(accumulator, node, range, createEmptyValue());
      continue;
    }

    if (type === 'script') {
      const value = {
        formats: [,],
        replacements: [{
          type,
          attributes: {
            'data-rich-text-script': node.getAttribute('data-rich-text-script') || encodeURIComponent(node.innerHTML)
          }
        }],
        text: OBJECT_REPLACEMENT_CHARACTER
      };
      accumulateSelection(accumulator, node, range, value);
      mergePair(accumulator, value);
      continue;
    }

    if (type === 'br') {
      accumulateSelection(accumulator, node, range, createEmptyValue());
      mergePair(accumulator, create({
        text: '\n'
      }));
      continue;
    }

    const format = toFormat({
      type,
      attributes: getAttributes({
        element: node
      })
    });

    if (multilineWrapperTags && multilineWrapperTags.indexOf(type) !== -1) {
      const value = createFromMultilineElement({
        element: node,
        range,
        multilineTag,
        multilineWrapperTags,
        currentWrapperTags: [...currentWrapperTags, format],
        isEditableTree,
        preserveWhiteSpace
      });
      accumulateSelection(accumulator, node, range, value);
      mergePair(accumulator, value);
      continue;
    }

    const value = createFromElement({
      element: node,
      range,
      multilineTag,
      multilineWrapperTags,
      isEditableTree,
      preserveWhiteSpace
    });
    accumulateSelection(accumulator, node, range, value);

    if (!format) {
      mergePair(accumulator, value);
    } else if (value.text.length === 0) {
      if (format.attributes) {
        mergePair(accumulator, {
          formats: [,],
          replacements: [format],
          text: OBJECT_REPLACEMENT_CHARACTER
        });
      }
    } else {
      // Indices should share a reference to the same formats array.
      // Only create a new reference if `formats` changes.
      function mergeFormats(formats) {
        if (mergeFormats.formats === formats) {
          return mergeFormats.newFormats;
        }

        const newFormats = formats ? [format, ...formats] : [format];
        mergeFormats.formats = formats;
        mergeFormats.newFormats = newFormats;
        return newFormats;
      } // Since the formats parameter can be `undefined`, preset
      // `mergeFormats` with a new reference.


      mergeFormats.newFormats = [format];
      mergePair(accumulator, { ...value,
        formats: Array.from(value.formats, mergeFormats)
      });
    }
  }

  return accumulator;
}
/**
 * Creates a rich text value from a DOM element and range that should be
 * multiline.
 *
 * @param {Object}  $1                        Named argements.
 * @param {Element} [$1.element]              Element to create value from.
 * @param {Range}   [$1.range]                Range to create value from.
 * @param {string}  [$1.multilineTag]         Multiline tag if the structure is
 *                                            multiline.
 * @param {Array}   [$1.multilineWrapperTags] Tags where lines can be found if
 *                                            nesting is possible.
 * @param {Array}   [$1.currentWrapperTags]   Whether to prepend a line
 *                                            separator.
 * @param {boolean} [$1.preserveWhiteSpace]   Whether or not to collapse white
 *                                            space characters.
 * @param {boolean} [$1.isEditableTree]
 *
 * @return {RichTextValue} A rich text value.
 */


function createFromMultilineElement(_ref3) {
  let {
    element,
    range,
    multilineTag,
    multilineWrapperTags,
    currentWrapperTags = [],
    isEditableTree,
    preserveWhiteSpace
  } = _ref3;
  const accumulator = createEmptyValue();

  if (!element || !element.hasChildNodes()) {
    return accumulator;
  }

  const length = element.children.length; // Optimise for speed.

  for (let index = 0; index < length; index++) {
    const node = element.children[index];

    if (node.nodeName.toLowerCase() !== multilineTag) {
      continue;
    }

    const value = createFromElement({
      element: node,
      range,
      multilineTag,
      multilineWrapperTags,
      currentWrapperTags,
      isEditableTree,
      preserveWhiteSpace
    }); // Multiline value text should be separated by a line separator.

    if (index !== 0 || currentWrapperTags.length > 0) {
      mergePair(accumulator, {
        formats: [,],
        replacements: currentWrapperTags.length > 0 ? [currentWrapperTags] : [,],
        text: LINE_SEPARATOR
      });
    }

    accumulateSelection(accumulator, node, range, value);
    mergePair(accumulator, value);
  }

  return accumulator;
}
/**
 * Gets the attributes of an element in object shape.
 *
 * @param {Object}  $1         Named argements.
 * @param {Element} $1.element Element to get attributes from.
 *
 * @return {Object|void} Attribute object or `undefined` if the element has no
 *                       attributes.
 */


function getAttributes(_ref4) {
  let {
    element
  } = _ref4;

  if (!element.hasAttributes()) {
    return;
  }

  const length = element.attributes.length;
  let accumulator; // Optimise for speed.

  for (let i = 0; i < length; i++) {
    const {
      name,
      value
    } = element.attributes[i];

    if (name.indexOf('data-rich-text-') === 0) {
      continue;
    }

    const safeName = /^on/i.test(name) ? 'data-disable-rich-text-' + name : name;
    accumulator = accumulator || {};
    accumulator[safeName] = value;
  }

  return accumulator;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/concat.js
/**
 * Internal dependencies
 */


/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Concats a pair of rich text values. Not that this mutates `a` and does NOT
 * normalise formats!
 *
 * @param {Object} a Value to mutate.
 * @param {Object} b Value to add read from.
 *
 * @return {Object} `a`, mutated.
 */

function mergePair(a, b) {
  a.formats = a.formats.concat(b.formats);
  a.replacements = a.replacements.concat(b.replacements);
  a.text += b.text;
  return a;
}
/**
 * Combine all Rich Text values into one. This is similar to
 * `String.prototype.concat`.
 *
 * @param {...RichTextValue} values Objects to combine.
 *
 * @return {RichTextValue} A new value combining all given records.
 */

function concat() {
  for (var _len = arguments.length, values = new Array(_len), _key = 0; _key < _len; _key++) {
    values[_key] = arguments[_key];
  }

  return normaliseFormats(values.reduce(mergePair, create()));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-active-formats.js
/** @typedef {import('./create').RichTextValue} RichTextValue */

/** @typedef {import('./create').RichTextFormatList} RichTextFormatList */

/**
 * Gets the all format objects at the start of the selection.
 *
 * @param {RichTextValue} value                Value to inspect.
 * @param {Array}         EMPTY_ACTIVE_FORMATS Array to return if there are no
 *                                             active formats.
 *
 * @return {RichTextFormatList} Active format objects.
 */
function getActiveFormats(_ref) {
  let {
    formats,
    start,
    end,
    activeFormats
  } = _ref;
  let EMPTY_ACTIVE_FORMATS = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : [];

  if (start === undefined) {
    return EMPTY_ACTIVE_FORMATS;
  }

  if (start === end) {
    // For a collapsed caret, it is possible to override the active formats.
    if (activeFormats) {
      return activeFormats;
    }

    const formatsBefore = formats[start - 1] || EMPTY_ACTIVE_FORMATS;
    const formatsAfter = formats[start] || EMPTY_ACTIVE_FORMATS; // By default, select the lowest amount of formats possible (which means
    // the caret is positioned outside the format boundary). The user can
    // then use arrow keys to define `activeFormats`.

    if (formatsBefore.length < formatsAfter.length) {
      return formatsBefore;
    }

    return formatsAfter;
  }

  return formats[start] || EMPTY_ACTIVE_FORMATS;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-active-format.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/** @typedef {import('./create').RichTextValue} RichTextValue */

/** @typedef {import('./create').RichTextFormat} RichTextFormat */

/**
 * Gets the format object by type at the start of the selection. This can be
 * used to get e.g. the URL of a link format at the current selection, but also
 * to check if a format is active at the selection. Returns undefined if there
 * is no format at the selection.
 *
 * @param {RichTextValue} value      Value to inspect.
 * @param {string}        formatType Format type to look for.
 *
 * @return {RichTextFormat|undefined} Active format object of the specified
 *                                    type, or undefined.
 */

function getActiveFormat(value, formatType) {
  return (0,external_lodash_namespaceObject.find)(getActiveFormats(value), {
    type: formatType
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-active-object.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/** @typedef {import('./create').RichTextFormat} RichTextFormat */

/**
 * Gets the active object, if there is any.
 *
 * @param {RichTextValue} value Value to inspect.
 *
 * @return {RichTextFormat|void} Active object, or undefined.
 */

function getActiveObject(_ref) {
  let {
    start,
    end,
    replacements,
    text
  } = _ref;

  if (start + 1 !== end || text[start] !== OBJECT_REPLACEMENT_CHARACTER) {
    return;
  }

  return replacements[start];
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-text-content.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Get the textual content of a Rich Text value. This is similar to
 * `Element.textContent`.
 *
 * @param {RichTextValue} value Value to use.
 *
 * @return {string} The text content.
 */

function getTextContent(_ref) {
  let {
    text
  } = _ref;
  return text.replace(new RegExp(OBJECT_REPLACEMENT_CHARACTER, 'g'), '').replace(new RegExp(LINE_SEPARATOR, 'g'), '\n');
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-line-index.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Gets the currently selected line index, or the first line index if the
 * selection spans over multiple items.
 *
 * @param {RichTextValue} value      Value to get the line index from.
 * @param {boolean}       startIndex Optional index that should be contained by
 *                                   the line. Defaults to the selection start
 *                                   of the value.
 *
 * @return {number|void} The line index. Undefined if not found.
 */

function getLineIndex(_ref) {
  let {
    start,
    text
  } = _ref;
  let startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : start;
  let index = startIndex;

  while (index--) {
    if (text[index] === LINE_SEPARATOR) {
      return index;
    }
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-list-root-selected.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Whether or not the root list is selected.
 *
 * @param {RichTextValue} value The value to check.
 *
 * @return {boolean} True if the root list or nothing is selected, false if an
 *                   inner list is selected.
 */

function isListRootSelected(value) {
  const {
    replacements,
    start
  } = value;
  const lineIndex = getLineIndex(value, start);
  const replacement = replacements[lineIndex];
  return !replacement || replacement.length < 1;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-active-list-type.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Whether or not the selected list has the given tag name.
 *
 * @param {RichTextValue} value    The value to check.
 * @param {string}        type     The tag name the list should have.
 * @param {string}        rootType The current root tag name, to compare with in
 *                                 case nothing is selected.
 *
 * @return {boolean} True if the current list type matches `type`, false if not.
 */

function isActiveListType(value, type, rootType) {
  const {
    replacements,
    start
  } = value;
  const lineIndex = getLineIndex(value, start);
  const replacement = replacements[lineIndex];

  if (!replacement || replacement.length === 0) {
    return type === rootType;
  }

  const lastFormat = replacement[replacement.length - 1];
  return lastFormat.type === type;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-collapsed.js
/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Check if the selection of a Rich Text value is collapsed or not. Collapsed
 * means that no characters are selected, but there is a caret present. If there
 * is no selection, `undefined` will be returned. This is similar to
 * `window.getSelection().isCollapsed()`.
 *
 * @param {RichTextValue} value The rich text value to check.
 *
 * @return {boolean|undefined} True if the selection is collapsed, false if not,
 *                             undefined if there is no selection.
 */
function isCollapsed(_ref) {
  let {
    start,
    end
  } = _ref;

  if (start === undefined || end === undefined) {
    return;
  }

  return start === end;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/is-empty.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Check if a Rich Text value is Empty, meaning it contains no text or any
 * objects (such as images).
 *
 * @param {RichTextValue} value Value to use.
 *
 * @return {boolean} True if the value is empty, false if not.
 */

function isEmpty(_ref) {
  let {
    text
  } = _ref;
  return text.length === 0;
}
/**
 * Check if the current collapsed selection is on an empty line in case of a
 * multiline value.
 *
 * @param {RichTextValue} value Value te check.
 *
 * @return {boolean} True if the line is empty, false if not.
 */

function isEmptyLine(_ref2) {
  let {
    text,
    start,
    end
  } = _ref2;

  if (start !== end) {
    return false;
  }

  if (text.length === 0) {
    return true;
  }

  if (start === 0 && text.slice(0, 1) === LINE_SEPARATOR) {
    return true;
  }

  if (start === text.length && text.slice(-1) === LINE_SEPARATOR) {
    return true;
  }

  return text.slice(start - 1, end + 1) === `${LINE_SEPARATOR}${LINE_SEPARATOR}`;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/join.js
/**
 * Internal dependencies
 */


/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Combine an array of Rich Text values into one, optionally separated by
 * `separator`, which can be a Rich Text value, HTML string, or plain text
 * string. This is similar to `Array.prototype.join`.
 *
 * @param {Array<RichTextValue>} values      An array of values to join.
 * @param {string|RichTextValue} [separator] Separator string or value.
 *
 * @return {RichTextValue} A new combined value.
 */

function join(values) {
  let separator = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : '';

  if (typeof separator === 'string') {
    separator = create({
      text: separator
    });
  }

  return normaliseFormats(values.reduce((accumlator, _ref) => {
    let {
      formats,
      replacements,
      text
    } = _ref;
    return {
      formats: accumlator.formats.concat(separator.formats, formats),
      replacements: accumlator.replacements.concat(separator.replacements, replacements),
      text: accumlator.text + separator.text + text
    };
  }));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/register-format-type.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/**
 * @typedef {Object} WPFormat
 *
 * @property {string}   name        A string identifying the format. Must be
 *                                  unique across all registered formats.
 * @property {string}   tagName     The HTML tag this format will wrap the
 *                                  selection with.
 * @property {string}   [className] A class to match the format.
 * @property {string}   title       Name of the format.
 * @property {Function} edit        Should return a component for the user to
 *                                  interact with the new registered format.
 */

/**
 * Registers a new format provided a unique name and an object defining its
 * behavior.
 *
 * @param {string}   name     Format name.
 * @param {WPFormat} settings Format settings.
 *
 * @return {WPFormat|undefined} The format, if it has been successfully
 *                              registered; otherwise `undefined`.
 */

function registerFormatType(name, settings) {
  settings = {
    name,
    ...settings
  };

  if (typeof settings.name !== 'string') {
    window.console.error('Format names must be strings.');
    return;
  }

  if (!/^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/.test(settings.name)) {
    window.console.error('Format names must contain a namespace prefix, include only lowercase alphanumeric characters or dashes, and start with a letter. Example: my-plugin/my-custom-format');
    return;
  }

  if ((0,external_wp_data_namespaceObject.select)(store).getFormatType(settings.name)) {
    window.console.error('Format "' + settings.name + '" is already registered.');
    return;
  }

  if (typeof settings.tagName !== 'string' || settings.tagName === '') {
    window.console.error('Format tag names must be a string.');
    return;
  }

  if ((typeof settings.className !== 'string' || settings.className === '') && settings.className !== null) {
    window.console.error('Format class names must be a string, or null to handle bare elements.');
    return;
  }

  if (!/^[_a-zA-Z]+[a-zA-Z0-9-]*$/.test(settings.className)) {
    window.console.error('A class name must begin with a letter, followed by any number of hyphens, letters, or numbers.');
    return;
  }

  if (settings.className === null) {
    const formatTypeForBareElement = (0,external_wp_data_namespaceObject.select)(store).getFormatTypeForBareElement(settings.tagName);

    if (formatTypeForBareElement) {
      window.console.error(`Format "${formatTypeForBareElement.name}" is already registered to handle bare tag name "${settings.tagName}".`);
      return;
    }
  } else {
    const formatTypeForClassName = (0,external_wp_data_namespaceObject.select)(store).getFormatTypeForClassName(settings.className);

    if (formatTypeForClassName) {
      window.console.error(`Format "${formatTypeForClassName.name}" is already registered to handle class name "${settings.className}".`);
      return;
    }
  }

  if (!('title' in settings) || settings.title === '') {
    window.console.error('The format "' + settings.name + '" must have a title.');
    return;
  }

  if ('keywords' in settings && settings.keywords.length > 3) {
    window.console.error('The format "' + settings.name + '" can have a maximum of 3 keywords.');
    return;
  }

  if (typeof settings.title !== 'string') {
    window.console.error('Format titles must be strings.');
    return;
  }

  (0,external_wp_data_namespaceObject.dispatch)(store).addFormatTypes(settings);
  return settings;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/remove-format.js
/**
 * External dependencies
 */

/**
 * Internal dependencies
 */


/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Remove any format object from a Rich Text value by type from the given
 * `startIndex` to the given `endIndex`. Indices are retrieved from the
 * selection if none are provided.
 *
 * @param {RichTextValue} value        Value to modify.
 * @param {string}        formatType   Format type to remove.
 * @param {number}        [startIndex] Start index.
 * @param {number}        [endIndex]   End index.
 *
 * @return {RichTextValue} A new value with the format applied.
 */

function removeFormat(value, formatType) {
  let startIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : value.start;
  let endIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : value.end;
  const {
    formats,
    activeFormats
  } = value;
  const newFormats = formats.slice(); // If the selection is collapsed, expand start and end to the edges of the
  // format.

  if (startIndex === endIndex) {
    const format = (0,external_lodash_namespaceObject.find)(newFormats[startIndex], {
      type: formatType
    });

    if (format) {
      while ((0,external_lodash_namespaceObject.find)(newFormats[startIndex], format)) {
        filterFormats(newFormats, startIndex, formatType);
        startIndex--;
      }

      endIndex++;

      while ((0,external_lodash_namespaceObject.find)(newFormats[endIndex], format)) {
        filterFormats(newFormats, endIndex, formatType);
        endIndex++;
      }
    }
  } else {
    for (let i = startIndex; i < endIndex; i++) {
      if (newFormats[i]) {
        filterFormats(newFormats, i, formatType);
      }
    }
  }

  return normaliseFormats({ ...value,
    formats: newFormats,
    activeFormats: (0,external_lodash_namespaceObject.reject)(activeFormats, {
      type: formatType
    })
  });
}

function filterFormats(formats, index, formatType) {
  const newFormats = formats[index].filter(_ref => {
    let {
      type
    } = _ref;
    return type !== formatType;
  });

  if (newFormats.length) {
    formats[index] = newFormats;
  } else {
    delete formats[index];
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/insert.js
/**
 * Internal dependencies
 */


/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Insert a Rich Text value, an HTML string, or a plain text string, into a
 * Rich Text value at the given `startIndex`. Any content between `startIndex`
 * and `endIndex` will be removed. Indices are retrieved from the selection if
 * none are provided.
 *
 * @param {RichTextValue}        value         Value to modify.
 * @param {RichTextValue|string} valueToInsert Value to insert.
 * @param {number}               [startIndex]  Start index.
 * @param {number}               [endIndex]    End index.
 *
 * @return {RichTextValue} A new value with the value inserted.
 */

function insert(value, valueToInsert) {
  let startIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : value.start;
  let endIndex = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : value.end;
  const {
    formats,
    replacements,
    text
  } = value;

  if (typeof valueToInsert === 'string') {
    valueToInsert = create({
      text: valueToInsert
    });
  }

  const index = startIndex + valueToInsert.text.length;
  return normaliseFormats({
    formats: formats.slice(0, startIndex).concat(valueToInsert.formats, formats.slice(endIndex)),
    replacements: replacements.slice(0, startIndex).concat(valueToInsert.replacements, replacements.slice(endIndex)),
    text: text.slice(0, startIndex) + valueToInsert.text + text.slice(endIndex),
    start: index,
    end: index
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/remove.js
/**
 * Internal dependencies
 */


/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Remove content from a Rich Text value between the given `startIndex` and
 * `endIndex`. Indices are retrieved from the selection if none are provided.
 *
 * @param {RichTextValue} value        Value to modify.
 * @param {number}        [startIndex] Start index.
 * @param {number}        [endIndex]   End index.
 *
 * @return {RichTextValue} A new value with the content removed.
 */

function remove(value, startIndex, endIndex) {
  return insert(value, create(), startIndex, endIndex);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/replace.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Search a Rich Text value and replace the match(es) with `replacement`. This
 * is similar to `String.prototype.replace`.
 *
 * @param {RichTextValue}   value       The value to modify.
 * @param {RegExp|string}   pattern     A RegExp object or literal. Can also be
 *                                      a string. It is treated as a verbatim
 *                                      string and is not interpreted as a
 *                                      regular expression. Only the first
 *                                      occurrence will be replaced.
 * @param {Function|string} replacement The match or matches are replaced with
 *                                      the specified or the value returned by
 *                                      the specified function.
 *
 * @return {RichTextValue} A new value with replacements applied.
 */

function replace_replace(_ref, pattern, replacement) {
  let {
    formats,
    replacements,
    text,
    start,
    end
  } = _ref;
  text = text.replace(pattern, function (match) {
    for (var _len = arguments.length, rest = new Array(_len > 1 ? _len - 1 : 0), _key = 1; _key < _len; _key++) {
      rest[_key - 1] = arguments[_key];
    }

    const offset = rest[rest.length - 2];
    let newText = replacement;
    let newFormats;
    let newReplacements;

    if (typeof newText === 'function') {
      newText = replacement(match, ...rest);
    }

    if (typeof newText === 'object') {
      newFormats = newText.formats;
      newReplacements = newText.replacements;
      newText = newText.text;
    } else {
      newFormats = Array(newText.length);
      newReplacements = Array(newText.length);

      if (formats[offset]) {
        newFormats = newFormats.fill(formats[offset]);
      }
    }

    formats = formats.slice(0, offset).concat(newFormats, formats.slice(offset + match.length));
    replacements = replacements.slice(0, offset).concat(newReplacements, replacements.slice(offset + match.length));

    if (start) {
      start = end = offset + newText.length;
    }

    return newText;
  });
  return normaliseFormats({
    formats,
    replacements,
    text,
    start,
    end
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/insert-line-separator.js
/**
 * Internal dependencies
 */


/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Insert a line break character into a Rich Text value at the given
 * `startIndex`. Any content between `startIndex` and `endIndex` will be
 * removed. Indices are retrieved from the selection if none are provided.
 *
 * @param {RichTextValue} value        Value to modify.
 * @param {number}        [startIndex] Start index.
 * @param {number}        [endIndex]   End index.
 *
 * @return {RichTextValue} A new value with the value inserted.
 */

function insertLineSeparator(value) {
  let startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : value.start;
  let endIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : value.end;
  const beforeText = value.text.slice(0, startIndex);
  const previousLineSeparatorIndex = beforeText.lastIndexOf(LINE_SEPARATOR);
  const previousLineSeparatorFormats = value.replacements[previousLineSeparatorIndex];
  let replacements = [,];

  if (previousLineSeparatorFormats) {
    replacements = [previousLineSeparatorFormats];
  }

  const valueToInsert = {
    formats: [,],
    replacements,
    text: LINE_SEPARATOR
  };
  return insert(value, valueToInsert, startIndex, endIndex);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/remove-line-separator.js
/**
 * Internal dependencies
 */



/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Removes a line separator character, if existing, from a Rich Text value at
 * the current indices. If no line separator exists on the indices it will
 * return undefined.
 *
 * @param {RichTextValue} value    Value to modify.
 * @param {boolean}       backward Indicates if are removing from the start
 *                                 index or the end index.
 *
 * @return {RichTextValue|undefined} A new value with the line separator
 *                                   removed. Or undefined if no line separator
 *                                   is found on the position.
 */

function removeLineSeparator(value) {
  let backward = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : true;
  const {
    replacements,
    text,
    start,
    end
  } = value;
  const collapsed = isCollapsed(value);
  let index = start - 1;
  let removeStart = collapsed ? start - 1 : start;
  let removeEnd = end;

  if (!backward) {
    index = end;
    removeStart = start;
    removeEnd = collapsed ? end + 1 : end;
  }

  if (text[index] !== LINE_SEPARATOR) {
    return;
  }

  let newValue; // If the line separator that is about te be removed
  // contains wrappers, remove the wrappers first.

  if (collapsed && replacements[index] && replacements[index].length) {
    const newReplacements = replacements.slice();
    newReplacements[index] = replacements[index].slice(0, -1);
    newValue = { ...value,
      replacements: newReplacements
    };
  } else {
    newValue = remove(value, removeStart, removeEnd);
  }

  return newValue;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/insert-object.js
/**
 * Internal dependencies
 */


/** @typedef {import('./create').RichTextValue} RichTextValue */

/** @typedef {import('./create').RichTextFormat} RichTextFormat */

/**
 * Insert a format as an object into a Rich Text value at the given
 * `startIndex`. Any content between `startIndex` and `endIndex` will be
 * removed. Indices are retrieved from the selection if none are provided.
 *
 * @param {RichTextValue}  value          Value to modify.
 * @param {RichTextFormat} formatToInsert Format to insert as object.
 * @param {number}         [startIndex]   Start index.
 * @param {number}         [endIndex]     End index.
 *
 * @return {RichTextValue} A new value with the object inserted.
 */

function insertObject(value, formatToInsert, startIndex, endIndex) {
  const valueToInsert = {
    formats: [,],
    replacements: [formatToInsert],
    text: OBJECT_REPLACEMENT_CHARACTER
  };
  return insert(value, valueToInsert, startIndex, endIndex);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/slice.js
/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Slice a Rich Text value from `startIndex` to `endIndex`. Indices are
 * retrieved from the selection if none are provided. This is similar to
 * `String.prototype.slice`.
 *
 * @param {RichTextValue} value        Value to modify.
 * @param {number}        [startIndex] Start index.
 * @param {number}        [endIndex]   End index.
 *
 * @return {RichTextValue} A new extracted value.
 */
function slice(value) {
  let startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : value.start;
  let endIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : value.end;
  const {
    formats,
    replacements,
    text
  } = value;

  if (startIndex === undefined || endIndex === undefined) {
    return { ...value
    };
  }

  return {
    formats: formats.slice(startIndex, endIndex),
    replacements: replacements.slice(startIndex, endIndex),
    text: text.slice(startIndex, endIndex)
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/split.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Split a Rich Text value in two at the given `startIndex` and `endIndex`, or
 * split at the given separator. This is similar to `String.prototype.split`.
 * Indices are retrieved from the selection if none are provided.
 *
 * @param {RichTextValue} value
 * @param {number|string} [string] Start index, or string at which to split.
 *
 * @return {Array<RichTextValue>|undefined} An array of new values.
 */

function split(_ref, string) {
  let {
    formats,
    replacements,
    text,
    start,
    end
  } = _ref;

  if (typeof string !== 'string') {
    return splitAtSelection(...arguments);
  }

  let nextStart = 0;
  return text.split(string).map(substring => {
    const startIndex = nextStart;
    const value = {
      formats: formats.slice(startIndex, startIndex + substring.length),
      replacements: replacements.slice(startIndex, startIndex + substring.length),
      text: substring
    };
    nextStart += string.length + substring.length;

    if (start !== undefined && end !== undefined) {
      if (start >= startIndex && start < nextStart) {
        value.start = start - startIndex;
      } else if (start < startIndex && end > startIndex) {
        value.start = 0;
      }

      if (end >= startIndex && end < nextStart) {
        value.end = end - startIndex;
      } else if (start < nextStart && end > nextStart) {
        value.end = substring.length;
      }
    }

    return value;
  });
}

function splitAtSelection(_ref2) {
  let {
    formats,
    replacements,
    text,
    start,
    end
  } = _ref2;
  let startIndex = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : start;
  let endIndex = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : end;

  if (start === undefined || end === undefined) {
    return;
  }

  const before = {
    formats: formats.slice(0, startIndex),
    replacements: replacements.slice(0, startIndex),
    text: text.slice(0, startIndex)
  };
  const after = {
    formats: formats.slice(endIndex),
    replacements: replacements.slice(endIndex),
    text: text.slice(endIndex),
    start: 0,
    end: 0
  };
  return [// Ensure newlines are trimmed.
  replace_replace(before, /\u2028+$/, ''), replace_replace(after, /^\u2028+/, '')];
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-format-type.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/** @typedef {import('./register-format-type').RichTextFormatType} RichTextFormatType */

/**
 * Returns a registered format type.
 *
 * @param {string} name Format name.
 *
 * @return {RichTextFormatType|undefined} Format type.
 */

function get_format_type_getFormatType(name) {
  return (0,external_wp_data_namespaceObject.select)(store).getFormatType(name);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/to-tree.js
/**
 * Internal dependencies
 */




function restoreOnAttributes(attributes, isEditableTree) {
  if (isEditableTree) {
    return attributes;
  }

  const newAttributes = {};

  for (const key in attributes) {
    let newKey = key;

    if (key.startsWith('data-disable-rich-text-')) {
      newKey = key.slice('data-disable-rich-text-'.length);
    }

    newAttributes[newKey] = attributes[key];
  }

  return newAttributes;
}
/**
 * Converts a format object to information that can be used to create an element
 * from (type, attributes and object).
 *
 * @param {Object}  $1                        Named parameters.
 * @param {string}  $1.type                   The format type.
 * @param {Object}  $1.attributes             The format attributes.
 * @param {Object}  $1.unregisteredAttributes The unregistered format
 *                                            attributes.
 * @param {boolean} $1.object                 Whether or not it is an object
 *                                            format.
 * @param {boolean} $1.boundaryClass          Whether or not to apply a boundary
 *                                            class.
 * @param {boolean} $1.isEditableTree
 *
 * @return {Object} Information to be used for element creation.
 */


function fromFormat(_ref) {
  let {
    type,
    attributes,
    unregisteredAttributes,
    object,
    boundaryClass,
    isEditableTree
  } = _ref;
  const formatType = get_format_type_getFormatType(type);
  let elementAttributes = {};

  if (boundaryClass) {
    elementAttributes['data-rich-text-format-boundary'] = 'true';
  }

  if (!formatType) {
    if (attributes) {
      elementAttributes = { ...attributes,
        ...elementAttributes
      };
    }

    return {
      type,
      attributes: restoreOnAttributes(elementAttributes, isEditableTree),
      object
    };
  }

  elementAttributes = { ...unregisteredAttributes,
    ...elementAttributes
  };

  for (const name in attributes) {
    const key = formatType.attributes ? formatType.attributes[name] : false;

    if (key) {
      elementAttributes[key] = attributes[name];
    } else {
      elementAttributes[name] = attributes[name];
    }
  }

  if (formatType.className) {
    if (elementAttributes.class) {
      elementAttributes.class = `${formatType.className} ${elementAttributes.class}`;
    } else {
      elementAttributes.class = formatType.className;
    }
  }

  return {
    type: formatType.tagName,
    object: formatType.object,
    attributes: restoreOnAttributes(elementAttributes, isEditableTree)
  };
}
/**
 * Checks if both arrays of formats up until a certain index are equal.
 *
 * @param {Array}  a     Array of formats to compare.
 * @param {Array}  b     Array of formats to compare.
 * @param {number} index Index to check until.
 */


function isEqualUntil(a, b, index) {
  do {
    if (a[index] !== b[index]) {
      return false;
    }
  } while (index--);

  return true;
}

function toTree(_ref2) {
  let {
    value,
    multilineTag,
    preserveWhiteSpace,
    createEmpty,
    append,
    getLastChild,
    getParent,
    isText,
    getText,
    remove,
    appendText,
    onStartIndex,
    onEndIndex,
    isEditableTree,
    placeholder
  } = _ref2;
  const {
    formats,
    replacements,
    text,
    start,
    end
  } = value;
  const formatsLength = formats.length + 1;
  const tree = createEmpty();
  const multilineFormat = {
    type: multilineTag
  };
  const activeFormats = getActiveFormats(value);
  const deepestActiveFormat = activeFormats[activeFormats.length - 1];
  let lastSeparatorFormats;
  let lastCharacterFormats;
  let lastCharacter; // If we're building a multiline tree, start off with a multiline element.

  if (multilineTag) {
    append(append(tree, {
      type: multilineTag
    }), '');
    lastCharacterFormats = lastSeparatorFormats = [multilineFormat];
  } else {
    append(tree, '');
  }

  for (let i = 0; i < formatsLength; i++) {
    const character = text.charAt(i);
    const shouldInsertPadding = isEditableTree && ( // Pad the line if the line is empty.
    !lastCharacter || lastCharacter === LINE_SEPARATOR || // Pad the line if the previous character is a line break, otherwise
    // the line break won't be visible.
    lastCharacter === '\n');
    let characterFormats = formats[i]; // Set multiline tags in queue for building the tree.

    if (multilineTag) {
      if (character === LINE_SEPARATOR) {
        characterFormats = lastSeparatorFormats = (replacements[i] || []).reduce((accumulator, format) => {
          accumulator.push(format, multilineFormat);
          return accumulator;
        }, [multilineFormat]);
      } else {
        characterFormats = [...lastSeparatorFormats, ...(characterFormats || [])];
      }
    }

    let pointer = getLastChild(tree);

    if (shouldInsertPadding && character === LINE_SEPARATOR) {
      let node = pointer;

      while (!isText(node)) {
        node = getLastChild(node);
      }

      append(getParent(node), ZWNBSP);
    } // Set selection for the start of line.


    if (lastCharacter === LINE_SEPARATOR) {
      let node = pointer;

      while (!isText(node)) {
        node = getLastChild(node);
      }

      if (onStartIndex && start === i) {
        onStartIndex(tree, node);
      }

      if (onEndIndex && end === i) {
        onEndIndex(tree, node);
      }
    }

    if (characterFormats) {
      characterFormats.forEach((format, formatIndex) => {
        if (pointer && lastCharacterFormats && // Reuse the last element if all formats remain the same.
        isEqualUntil(characterFormats, lastCharacterFormats, formatIndex) && ( // Do not reuse the last element if the character is a
        // line separator.
        character !== LINE_SEPARATOR || characterFormats.length - 1 !== formatIndex)) {
          pointer = getLastChild(pointer);
          return;
        }

        const {
          type,
          attributes,
          unregisteredAttributes
        } = format;
        const boundaryClass = isEditableTree && character !== LINE_SEPARATOR && format === deepestActiveFormat;
        const parent = getParent(pointer);
        const newNode = append(parent, fromFormat({
          type,
          attributes,
          unregisteredAttributes,
          boundaryClass,
          isEditableTree
        }));

        if (isText(pointer) && getText(pointer).length === 0) {
          remove(pointer);
        }

        pointer = append(newNode, '');
      });
    } // No need for further processing if the character is a line separator.


    if (character === LINE_SEPARATOR) {
      lastCharacterFormats = characterFormats;
      lastCharacter = character;
      continue;
    } // If there is selection at 0, handle it before characters are inserted.


    if (i === 0) {
      if (onStartIndex && start === 0) {
        onStartIndex(tree, pointer);
      }

      if (onEndIndex && end === 0) {
        onEndIndex(tree, pointer);
      }
    }

    if (character === OBJECT_REPLACEMENT_CHARACTER) {
      var _replacements$i;

      if (!isEditableTree && ((_replacements$i = replacements[i]) === null || _replacements$i === void 0 ? void 0 : _replacements$i.type) === 'script') {
        pointer = append(getParent(pointer), fromFormat({
          type: 'script',
          isEditableTree
        }));
        append(pointer, {
          html: decodeURIComponent(replacements[i].attributes['data-rich-text-script'])
        });
      } else {
        pointer = append(getParent(pointer), fromFormat({ ...replacements[i],
          object: true,
          isEditableTree
        }));
      } // Ensure pointer is text node.


      pointer = append(getParent(pointer), '');
    } else if (!preserveWhiteSpace && character === '\n') {
      pointer = append(getParent(pointer), {
        type: 'br',
        attributes: isEditableTree ? {
          'data-rich-text-line-break': 'true'
        } : undefined,
        object: true
      }); // Ensure pointer is text node.

      pointer = append(getParent(pointer), '');
    } else if (!isText(pointer)) {
      pointer = append(getParent(pointer), character);
    } else {
      appendText(pointer, character);
    }

    if (onStartIndex && start === i + 1) {
      onStartIndex(tree, pointer);
    }

    if (onEndIndex && end === i + 1) {
      onEndIndex(tree, pointer);
    }

    if (shouldInsertPadding && i === text.length) {
      append(getParent(pointer), ZWNBSP);

      if (placeholder && text.length === 0) {
        append(getParent(pointer), {
          type: 'span',
          attributes: {
            'data-rich-text-placeholder': placeholder,
            // Necessary to prevent the placeholder from catching
            // selection. The placeholder is also not editable after
            // all.
            contenteditable: 'false',
            style: 'pointer-events:none;user-select:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;'
          }
        });
      }
    }

    lastCharacterFormats = characterFormats;
    lastCharacter = character;
  }

  return tree;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/to-dom.js
/**
 * Internal dependencies
 */


/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Creates a path as an array of indices from the given root node to the given
 * node.
 *
 * @param {Node}        node     Node to find the path of.
 * @param {HTMLElement} rootNode Root node to find the path from.
 * @param {Array}       path     Initial path to build on.
 *
 * @return {Array} The path from the root node to the node.
 */

function createPathToNode(node, rootNode, path) {
  const parentNode = node.parentNode;
  let i = 0;

  while (node = node.previousSibling) {
    i++;
  }

  path = [i, ...path];

  if (parentNode !== rootNode) {
    path = createPathToNode(parentNode, rootNode, path);
  }

  return path;
}
/**
 * Gets a node given a path (array of indices) from the given node.
 *
 * @param {HTMLElement} node Root node to find the wanted node in.
 * @param {Array}       path Path (indices) to the wanted node.
 *
 * @return {Object} Object with the found node and the remaining offset (if any).
 */


function getNodeByPath(node, path) {
  path = [...path];

  while (node && path.length > 1) {
    node = node.childNodes[path.shift()];
  }

  return {
    node,
    offset: path[0]
  };
}

function append(element, child) {
  if (typeof child === 'string') {
    child = element.ownerDocument.createTextNode(child);
  }

  const {
    type,
    attributes
  } = child;

  if (type) {
    child = element.ownerDocument.createElement(type);

    for (const key in attributes) {
      child.setAttribute(key, attributes[key]);
    }
  }

  return element.appendChild(child);
}

function appendText(node, text) {
  node.appendData(text);
}

function getLastChild(_ref) {
  let {
    lastChild
  } = _ref;
  return lastChild;
}

function getParent(_ref2) {
  let {
    parentNode
  } = _ref2;
  return parentNode;
}

function isText(node) {
  return node.nodeType === node.TEXT_NODE;
}

function getText(_ref3) {
  let {
    nodeValue
  } = _ref3;
  return nodeValue;
}

function to_dom_remove(node) {
  return node.parentNode.removeChild(node);
}

function toDom(_ref4) {
  let {
    value,
    multilineTag,
    prepareEditableTree,
    isEditableTree = true,
    placeholder,
    doc = document
  } = _ref4;
  let startPath = [];
  let endPath = [];

  if (prepareEditableTree) {
    value = { ...value,
      formats: prepareEditableTree(value)
    };
  }
  /**
   * Returns a new instance of a DOM tree upon which RichText operations can be
   * applied.
   *
   * Note: The current implementation will return a shared reference, reset on
   * each call to `createEmpty`. Therefore, you should not hold a reference to
   * the value to operate upon asynchronously, as it may have unexpected results.
   *
   * @return {Object} RichText tree.
   */


  const createEmpty = () => createElement(doc, '');

  const tree = toTree({
    value,
    multilineTag,
    createEmpty,
    append,
    getLastChild,
    getParent,
    isText,
    getText,
    remove: to_dom_remove,
    appendText,

    onStartIndex(body, pointer) {
      startPath = createPathToNode(pointer, body, [pointer.nodeValue.length]);
    },

    onEndIndex(body, pointer) {
      endPath = createPathToNode(pointer, body, [pointer.nodeValue.length]);
    },

    isEditableTree,
    placeholder
  });
  return {
    body: tree,
    selection: {
      startPath,
      endPath
    }
  };
}
/**
 * Create an `Element` tree from a Rich Text value and applies the difference to
 * the `Element` tree contained by `current`. If a `multilineTag` is provided,
 * text separated by two new lines will be wrapped in an `Element` of that type.
 *
 * @param {Object}        $1                       Named arguments.
 * @param {RichTextValue} $1.value                 Value to apply.
 * @param {HTMLElement}   $1.current               The live root node to apply the element tree to.
 * @param {string}        [$1.multilineTag]        Multiline tag.
 * @param {Function}      [$1.prepareEditableTree] Function to filter editorable formats.
 * @param {boolean}       [$1.__unstableDomOnly]   Only apply elements, no selection.
 * @param {string}        [$1.placeholder]         Placeholder text.
 */

function apply(_ref5) {
  let {
    value,
    current,
    multilineTag,
    prepareEditableTree,
    __unstableDomOnly,
    placeholder
  } = _ref5;
  // Construct a new element tree in memory.
  const {
    body,
    selection
  } = toDom({
    value,
    multilineTag,
    prepareEditableTree,
    placeholder,
    doc: current.ownerDocument
  });
  applyValue(body, current);

  if (value.start !== undefined && !__unstableDomOnly) {
    applySelection(selection, current);
  }
}
function applyValue(future, current) {
  let i = 0;
  let futureChild;

  while (futureChild = future.firstChild) {
    const currentChild = current.childNodes[i];

    if (!currentChild) {
      current.appendChild(futureChild);
    } else if (!currentChild.isEqualNode(futureChild)) {
      if (currentChild.nodeName !== futureChild.nodeName || currentChild.nodeType === currentChild.TEXT_NODE && currentChild.data !== futureChild.data) {
        current.replaceChild(futureChild, currentChild);
      } else {
        const currentAttributes = currentChild.attributes;
        const futureAttributes = futureChild.attributes;

        if (currentAttributes) {
          let ii = currentAttributes.length; // Reverse loop because `removeAttribute` on `currentChild`
          // changes `currentAttributes`.

          while (ii--) {
            const {
              name
            } = currentAttributes[ii];

            if (!futureChild.getAttribute(name)) {
              currentChild.removeAttribute(name);
            }
          }
        }

        if (futureAttributes) {
          for (let ii = 0; ii < futureAttributes.length; ii++) {
            const {
              name,
              value
            } = futureAttributes[ii];

            if (currentChild.getAttribute(name) !== value) {
              currentChild.setAttribute(name, value);
            }
          }
        }

        applyValue(futureChild, currentChild);
        future.removeChild(futureChild);
      }
    } else {
      future.removeChild(futureChild);
    }

    i++;
  }

  while (current.childNodes[i]) {
    current.removeChild(current.childNodes[i]);
  }
}
/**
 * Returns true if two ranges are equal, or false otherwise. Ranges are
 * considered equal if their start and end occur in the same container and
 * offset.
 *
 * @param {Range} a First range object to test.
 * @param {Range} b First range object to test.
 *
 * @return {boolean} Whether the two ranges are equal.
 */

function isRangeEqual(a, b) {
  return a.startContainer === b.startContainer && a.startOffset === b.startOffset && a.endContainer === b.endContainer && a.endOffset === b.endOffset;
}

function applySelection(_ref6, current) {
  let {
    startPath,
    endPath
  } = _ref6;
  const {
    node: startContainer,
    offset: startOffset
  } = getNodeByPath(current, startPath);
  const {
    node: endContainer,
    offset: endOffset
  } = getNodeByPath(current, endPath);
  const {
    ownerDocument
  } = current;
  const {
    defaultView
  } = ownerDocument;
  const selection = defaultView.getSelection();
  const range = ownerDocument.createRange();
  range.setStart(startContainer, startOffset);
  range.setEnd(endContainer, endOffset);
  const {
    activeElement
  } = ownerDocument;

  if (selection.rangeCount > 0) {
    // If the to be added range and the live range are the same, there's no
    // need to remove the live range and add the equivalent range.
    if (isRangeEqual(range, selection.getRangeAt(0))) {
      return;
    }

    selection.removeAllRanges();
  }

  selection.addRange(range); // This function is not intended to cause a shift in focus. Since the above
  // selection manipulations may shift focus, ensure that focus is restored to
  // its previous state.

  if (activeElement !== ownerDocument.activeElement) {
    // The `instanceof` checks protect against edge cases where the focused
    // element is not of the interface HTMLElement (does not have a `focus`
    // or `blur` property).
    //
    // See: https://github.com/Microsoft/TypeScript/issues/5901#issuecomment-431649653
    if (activeElement instanceof defaultView.HTMLElement) {
      activeElement.focus();
    }
  }
}

;// CONCATENATED MODULE: external ["wp","escapeHtml"]
var external_wp_escapeHtml_namespaceObject = window["wp"]["escapeHtml"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/to-html-string.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Create an HTML string from a Rich Text value. If a `multilineTag` is
 * provided, text separated by a line separator will be wrapped in it.
 *
 * @param {Object}        $1                      Named argements.
 * @param {RichTextValue} $1.value                Rich text value.
 * @param {string}        [$1.multilineTag]       Multiline tag.
 * @param {boolean}       [$1.preserveWhiteSpace] Whether or not to use newline
 *                                                characters for line breaks.
 *
 * @return {string} HTML string.
 */

function toHTMLString(_ref) {
  let {
    value,
    multilineTag,
    preserveWhiteSpace
  } = _ref;
  const tree = toTree({
    value,
    multilineTag,
    preserveWhiteSpace,
    createEmpty,
    append: to_html_string_append,
    getLastChild: to_html_string_getLastChild,
    getParent: to_html_string_getParent,
    isText: to_html_string_isText,
    getText: to_html_string_getText,
    remove: to_html_string_remove,
    appendText: to_html_string_appendText
  });
  return createChildrenHTML(tree.children);
}

function createEmpty() {
  return {};
}

function to_html_string_getLastChild(_ref2) {
  let {
    children
  } = _ref2;
  return children && children[children.length - 1];
}

function to_html_string_append(parent, object) {
  if (typeof object === 'string') {
    object = {
      text: object
    };
  }

  object.parent = parent;
  parent.children = parent.children || [];
  parent.children.push(object);
  return object;
}

function to_html_string_appendText(object, text) {
  object.text += text;
}

function to_html_string_getParent(_ref3) {
  let {
    parent
  } = _ref3;
  return parent;
}

function to_html_string_isText(_ref4) {
  let {
    text
  } = _ref4;
  return typeof text === 'string';
}

function to_html_string_getText(_ref5) {
  let {
    text
  } = _ref5;
  return text;
}

function to_html_string_remove(object) {
  const index = object.parent.children.indexOf(object);

  if (index !== -1) {
    object.parent.children.splice(index, 1);
  }

  return object;
}

function createElementHTML(_ref6) {
  let {
    type,
    attributes,
    object,
    children
  } = _ref6;
  let attributeString = '';

  for (const key in attributes) {
    if (!(0,external_wp_escapeHtml_namespaceObject.isValidAttributeName)(key)) {
      continue;
    }

    attributeString += ` ${key}="${(0,external_wp_escapeHtml_namespaceObject.escapeAttribute)(attributes[key])}"`;
  }

  if (object) {
    return `<${type}${attributeString}>`;
  }

  return `<${type}${attributeString}>${createChildrenHTML(children)}</${type}>`;
}

function createChildrenHTML() {
  let children = arguments.length > 0 && arguments[0] !== undefined ? arguments[0] : [];
  return children.map(child => {
    if (child.html !== undefined) {
      return child.html;
    }

    return child.text === undefined ? createElementHTML(child) : (0,external_wp_escapeHtml_namespaceObject.escapeEditableHTML)(child.text);
  }).join('');
}

;// CONCATENATED MODULE: external ["wp","a11y"]
var external_wp_a11y_namespaceObject = window["wp"]["a11y"];
;// CONCATENATED MODULE: external ["wp","i18n"]
var external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/toggle-format.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */




/** @typedef {import('./create').RichTextValue} RichTextValue */

/** @typedef {import('./create').RichTextFormat} RichTextFormat */

/**
 * Toggles a format object to a Rich Text value at the current selection.
 *
 * @param {RichTextValue}  value  Value to modify.
 * @param {RichTextFormat} format Format to apply or remove.
 *
 * @return {RichTextValue} A new value with the format applied or removed.
 */

function toggleFormat(value, format) {
  if (getActiveFormat(value, format.type)) {
    // For screen readers, will announce if formatting control is disabled.
    if (format.title) {
      // translators: %s: title of the formatting control
      (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)('%s removed.'), format.title), 'assertive');
    }

    return removeFormat(value, format.type);
  } // For screen readers, will announce if formatting control is enabled.


  if (format.title) {
    // translators: %s: title of the formatting control
    (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)('%s applied.'), format.title), 'assertive');
  }

  return applyFormat(value, format);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/unregister-format-type.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/** @typedef {import('./register-format-type').RichTextFormatType} RichTextFormatType */

/**
 * Unregisters a format.
 *
 * @param {string} name Format name.
 *
 * @return {RichTextFormatType|undefined} The previous format value, if it has
 *                                        been successfully unregistered;
 *                                        otherwise `undefined`.
 */

function unregisterFormatType(name) {
  const oldFormat = (0,external_wp_data_namespaceObject.select)(store).getFormatType(name);

  if (!oldFormat) {
    window.console.error(`Format ${name} is not registered.`);
    return;
  }

  (0,external_wp_data_namespaceObject.dispatch)(store).removeFormatTypes(name);
  return oldFormat;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/can-indent-list-items.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Checks if the selected list item can be indented.
 *
 * @param {RichTextValue} value Value to check.
 *
 * @return {boolean} Whether or not the selected list item can be indented.
 */

function canIndentListItems(value) {
  const lineIndex = getLineIndex(value); // There is only one line, so the line cannot be indented.

  if (lineIndex === undefined) {
    return false;
  }

  const {
    replacements
  } = value;
  const previousLineIndex = getLineIndex(value, lineIndex);
  const formatsAtLineIndex = replacements[lineIndex] || [];
  const formatsAtPreviousLineIndex = replacements[previousLineIndex] || []; // If the indentation of the current line is greater than previous line,
  // then the line cannot be furter indented.

  return formatsAtLineIndex.length <= formatsAtPreviousLineIndex.length;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/can-outdent-list-items.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Checks if the selected list item can be outdented.
 *
 * @param {RichTextValue} value Value to check.
 *
 * @return {boolean} Whether or not the selected list item can be outdented.
 */

function canOutdentListItems(value) {
  const {
    replacements,
    start
  } = value;
  const startingLineIndex = getLineIndex(value, start);
  return replacements[startingLineIndex] !== undefined;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/indent-list-items.js
/**
 * Internal dependencies
 */



/** @typedef {import('./create').RichTextValue} RichTextValue */

/** @typedef {import('./create').RichTextFormat} RichTextFormat */

/**
 * Gets the line index of the first previous list item with higher indentation.
 *
 * @param {RichTextValue} value     Value to search.
 * @param {number}        lineIndex Line index of the list item to compare
 *                                  with.
 *
 * @return {number|void} The line index.
 */

function getTargetLevelLineIndex(_ref, lineIndex) {
  let {
    text,
    replacements
  } = _ref;
  const startFormats = replacements[lineIndex] || [];
  let index = lineIndex;

  while (index-- >= 0) {
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    }

    const formatsAtIndex = replacements[index] || []; // Return the first line index that is one level higher. If the level is
    // lower or equal, there is no result.

    if (formatsAtIndex.length === startFormats.length + 1) {
      return index;
    } else if (formatsAtIndex.length <= startFormats.length) {
      return;
    }
  }
}
/**
 * Indents any selected list items if possible.
 *
 * @param {RichTextValue}  value      Value to change.
 * @param {RichTextFormat} rootFormat Root format.
 *
 * @return {RichTextValue} The changed value.
 */


function indentListItems(value, rootFormat) {
  if (!canIndentListItems(value)) {
    return value;
  }

  const lineIndex = getLineIndex(value);
  const previousLineIndex = getLineIndex(value, lineIndex);
  const {
    text,
    replacements,
    end
  } = value;
  const newFormats = replacements.slice();
  const targetLevelLineIndex = getTargetLevelLineIndex(value, lineIndex);

  for (let index = lineIndex; index < end; index++) {
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    } // Get the previous list, and if there's a child list, take over the
    // formats. If not, duplicate the last level and create a new level.


    if (targetLevelLineIndex) {
      const targetFormats = replacements[targetLevelLineIndex] || [];
      newFormats[index] = targetFormats.concat((newFormats[index] || []).slice(targetFormats.length - 1));
    } else {
      const targetFormats = replacements[previousLineIndex] || [];
      const lastformat = targetFormats[targetFormats.length - 1] || rootFormat;
      newFormats[index] = targetFormats.concat([lastformat], (newFormats[index] || []).slice(targetFormats.length));
    }
  }

  return { ...value,
    replacements: newFormats
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-parent-line-index.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Gets the index of the first parent list. To get the parent list formats, we
 * go through every list item until we find one with exactly one format type
 * less.
 *
 * @param {RichTextValue} value     Value to search.
 * @param {number}        lineIndex Line index of a child list item.
 *
 * @return {number|void} The parent list line index.
 */

function getParentLineIndex(_ref, lineIndex) {
  let {
    text,
    replacements
  } = _ref;
  const startFormats = replacements[lineIndex] || [];
  let index = lineIndex;

  while (index-- >= 0) {
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    }

    const formatsAtIndex = replacements[index] || [];

    if (formatsAtIndex.length === startFormats.length - 1) {
      return index;
    }
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/get-last-child-index.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Gets the line index of the last child in the list.
 *
 * @param {RichTextValue} value     Value to search.
 * @param {number}        lineIndex Line index of a list item in the list.
 *
 * @return {number} The index of the last child.
 */

function getLastChildIndex(_ref, lineIndex) {
  let {
    text,
    replacements
  } = _ref;
  const lineFormats = replacements[lineIndex] || []; // Use the given line index in case there are no next children.

  let childIndex = lineIndex; // `lineIndex` could be `undefined` if it's the first line.

  for (let index = lineIndex || 0; index < text.length; index++) {
    // We're only interested in line indices.
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    }

    const formatsAtIndex = replacements[index] || []; // If the amout of formats is equal or more, store it, then return the
    // last one if the amount of formats is less.

    if (formatsAtIndex.length >= lineFormats.length) {
      childIndex = index;
    } else {
      return childIndex;
    }
  } // If the end of the text is reached, return the last child index.


  return childIndex;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/outdent-list-items.js
/**
 * Internal dependencies
 */





/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Outdents any selected list items if possible.
 *
 * @param {RichTextValue} value Value to change.
 *
 * @return {RichTextValue} The changed value.
 */

function outdentListItems(value) {
  if (!canOutdentListItems(value)) {
    return value;
  }

  const {
    text,
    replacements,
    start,
    end
  } = value;
  const startingLineIndex = getLineIndex(value, start);
  const newFormats = replacements.slice(0);
  const parentFormats = replacements[getParentLineIndex(value, startingLineIndex)] || [];
  const endingLineIndex = getLineIndex(value, end);
  const lastChildIndex = getLastChildIndex(value, endingLineIndex); // Outdent all list items from the starting line index until the last child
  // index of the ending list. All children of the ending list need to be
  // outdented, otherwise they'll be orphaned.

  for (let index = startingLineIndex; index <= lastChildIndex; index++) {
    // Skip indices that are not line separators.
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    } // In the case of level 0, the formats at the index are undefined.


    const currentFormats = newFormats[index] || []; // Omit the indentation level where the selection starts.

    newFormats[index] = parentFormats.concat(currentFormats.slice(parentFormats.length + 1));

    if (newFormats[index].length === 0) {
      delete newFormats[index];
    }
  }

  return { ...value,
    replacements: newFormats
  };
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/change-list-type.js
/**
 * Internal dependencies
 */



/** @typedef {import('./create').RichTextValue} RichTextValue */

/** @typedef {import('./create').RichTextFormat} RichTextFormat */

/**
 * Changes the list type of the selected indented list, if any. Looks at the
 * currently selected list item and takes the parent list, then changes the list
 * type of this list. When multiple lines are selected, the parent lists are
 * takes and changed.
 *
 * @param {RichTextValue}  value     Value to change.
 * @param {RichTextFormat} newFormat The new list format object. Choose between
 *                                   `{ type: 'ol' }` and `{ type: 'ul' }`.
 *
 * @return {RichTextValue} The changed value.
 */

function changeListType(value, newFormat) {
  const {
    text,
    replacements,
    start,
    end
  } = value;
  const startingLineIndex = getLineIndex(value, start);
  const startLineFormats = replacements[startingLineIndex] || [];
  const endLineFormats = replacements[getLineIndex(value, end)] || [];
  const startIndex = getParentLineIndex(value, startingLineIndex);
  const newReplacements = replacements.slice();
  const startCount = startLineFormats.length - 1;
  const endCount = endLineFormats.length - 1;
  let changed;

  for (let index = startIndex + 1 || 0; index < text.length; index++) {
    if (text[index] !== LINE_SEPARATOR) {
      continue;
    }

    if ((newReplacements[index] || []).length <= startCount) {
      break;
    }

    if (!newReplacements[index]) {
      continue;
    }

    changed = true;
    newReplacements[index] = newReplacements[index].map((format, i) => {
      return i < startCount || i > endCount ? format : newFormat;
    });
  }

  if (!changed) {
    return value;
  }

  return { ...value,
    replacements: newReplacements
  };
}

;// CONCATENATED MODULE: external ["wp","element"]
var external_wp_element_namespaceObject = window["wp"]["element"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-anchor-ref.js
/**
 * WordPress dependencies
 */

/**
 * Internal dependencies
 */


/** @typedef {import('@wordpress/element').RefObject} RefObject */

/** @typedef {import('../register-format-type').RichTextFormatType} RichTextFormatType */

/** @typedef {import('../create').RichTextValue} RichTextValue */

/**
 * This hook, to be used in a format type's Edit component, returns the active
 * element that is formatted, or the selection range if no format is active.
 * The returned value is meant to be used for positioning UI, e.g. by passing it
 * to the `Popover` component.
 *
 * @param {Object}                 $1          Named parameters.
 * @param {RefObject<HTMLElement>} $1.ref      React ref of the element
 *                                             containing  the editable content.
 * @param {RichTextValue}          $1.value    Value to check for selection.
 * @param {RichTextFormatType}     $1.settings The format type's settings.
 *
 * @return {Element|Range} The active element or selection range.
 */

function useAnchorRef(_ref) {
  let {
    ref,
    value,
    settings = {}
  } = _ref;
  const {
    tagName,
    className,
    name
  } = settings;
  const activeFormat = name ? getActiveFormat(value, name) : undefined;
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!ref.current) return;
    const {
      ownerDocument: {
        defaultView
      }
    } = ref.current;
    const selection = defaultView.getSelection();

    if (!selection.rangeCount) {
      return;
    }

    const range = selection.getRangeAt(0);

    if (!activeFormat) {
      return range;
    }

    let element = range.startContainer; // If the caret is right before the element, select the next element.

    element = element.nextElementSibling || element;

    while (element.nodeType !== element.ELEMENT_NODE) {
      element = element.parentNode;
    }

    return element.closest(tagName + (className ? '.' + className : ''));
  }, [activeFormat, value.start, value.end, tagName, className]);
}

;// CONCATENATED MODULE: external ["wp","compose"]
var external_wp_compose_namespaceObject = window["wp"]["compose"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-default-style.js
/**
 * WordPress dependencies
 */

/**
 * In HTML, leading and trailing spaces are not visible, and multiple spaces
 * elsewhere are visually reduced to one space. This rule prevents spaces from
 * collapsing so all space is visible in the editor and can be removed. It also
 * prevents some browsers from inserting non-breaking spaces at the end of a
 * line to prevent the space from visually disappearing. Sometimes these non
 * breaking spaces can linger in the editor causing unwanted non breaking spaces
 * in between words. If also prevent Firefox from inserting a trailing `br` node
 * to visualise any trailing space, causing the element to be saved.
 *
 * > Authors are encouraged to set the 'white-space' property on editing hosts
 * > and on markup that was originally created through these editing mechanisms
 * > to the value 'pre-wrap'. Default HTML whitespace handling is not well
 * > suited to WYSIWYG editing, and line wrapping will not work correctly in
 * > some corner cases if 'white-space' is left at its default value.
 *
 * https://html.spec.whatwg.org/multipage/interaction.html#best-practices-for-in-page-editors
 *
 * @type {string}
 */

const whiteSpace = 'pre-wrap';
/**
 * A minimum width of 1px will prevent the rich text container from collapsing
 * to 0 width and hiding the caret. This is useful for inline containers.
 */

const minWidth = '1px';
function useDefaultStyle() {
  return (0,external_wp_element_namespaceObject.useCallback)(element => {
    if (!element) return;
    element.style.whiteSpace = whiteSpace;
    element.style.minWidth = minWidth;
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-boundary-style.js
/**
 * WordPress dependencies
 */

/*
 * Calculates and renders the format boundary style when the active formats
 * change.
 */

function useBoundaryStyle(_ref) {
  let {
    record
  } = _ref;
  const ref = (0,external_wp_element_namespaceObject.useRef)();
  const {
    activeFormats = []
  } = record.current;
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    // There's no need to recalculate the boundary styles if no formats are
    // active, because no boundary styles will be visible.
    if (!activeFormats || !activeFormats.length) {
      return;
    }

    const boundarySelector = '*[data-rich-text-format-boundary]';
    const element = ref.current.querySelector(boundarySelector);

    if (!element) {
      return;
    }

    const {
      ownerDocument
    } = element;
    const {
      defaultView
    } = ownerDocument;
    const computedStyle = defaultView.getComputedStyle(element);
    const newColor = computedStyle.color.replace(')', ', 0.2)').replace('rgb', 'rgba');
    const selector = `.rich-text:focus ${boundarySelector}`;
    const rule = `background-color: ${newColor}`;
    const style = `${selector} {${rule}}`;
    const globalStyleId = 'rich-text-boundary-style';
    let globalStyle = ownerDocument.getElementById(globalStyleId);

    if (!globalStyle) {
      globalStyle = ownerDocument.createElement('style');
      globalStyle.id = globalStyleId;
      ownerDocument.head.appendChild(globalStyle);
    }

    if (globalStyle.innerHTML !== style) {
      globalStyle.innerHTML = style;
    }
  }, [activeFormats]);
  return ref;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-copy-handler.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */





function useCopyHandler(props) {
  const propsRef = (0,external_wp_element_namespaceObject.useRef)(props);
  propsRef.current = props;
  return (0,external_wp_compose_namespaceObject.useRefEffect)(element => {
    function onCopy(event) {
      const {
        record,
        multilineTag,
        preserveWhiteSpace
      } = propsRef.current;

      if (isCollapsed(record.current) || !element.contains(element.ownerDocument.activeElement)) {
        return;
      }

      const selectedRecord = slice(record.current);
      const plainText = getTextContent(selectedRecord);
      const html = toHTMLString({
        value: selectedRecord,
        multilineTag,
        preserveWhiteSpace
      });
      event.clipboardData.setData('text/plain', plainText);
      event.clipboardData.setData('text/html', html);
      event.clipboardData.setData('rich-text', 'true');
      event.clipboardData.setData('rich-text-multi-line-tag', multilineTag || '');
      event.preventDefault();
    }

    element.addEventListener('copy', onCopy);
    return () => {
      element.removeEventListener('copy', onCopy);
    };
  }, []);
}

;// CONCATENATED MODULE: external ["wp","keycodes"]
var external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-format-boundaries.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */


const EMPTY_ACTIVE_FORMATS = [];
function useFormatBoundaries(props) {
  const [, forceRender] = (0,external_wp_element_namespaceObject.useReducer)(() => ({}));
  const propsRef = (0,external_wp_element_namespaceObject.useRef)(props);
  propsRef.current = props;
  return (0,external_wp_compose_namespaceObject.useRefEffect)(element => {
    function onKeyDown(event) {
      const {
        keyCode,
        shiftKey,
        altKey,
        metaKey,
        ctrlKey
      } = event;

      if ( // Only override left and right keys without modifiers pressed.
      shiftKey || altKey || metaKey || ctrlKey || keyCode !== external_wp_keycodes_namespaceObject.LEFT && keyCode !== external_wp_keycodes_namespaceObject.RIGHT) {
        return;
      }

      const {
        record,
        applyRecord
      } = propsRef.current;
      const {
        text,
        formats,
        start,
        end,
        activeFormats: currentActiveFormats = []
      } = record.current;
      const collapsed = isCollapsed(record.current);
      const {
        ownerDocument
      } = element;
      const {
        defaultView
      } = ownerDocument; // To do: ideally, we should look at visual position instead.

      const {
        direction
      } = defaultView.getComputedStyle(element);
      const reverseKey = direction === 'rtl' ? external_wp_keycodes_namespaceObject.RIGHT : external_wp_keycodes_namespaceObject.LEFT;
      const isReverse = event.keyCode === reverseKey; // If the selection is collapsed and at the very start, do nothing if
      // navigating backward.
      // If the selection is collapsed and at the very end, do nothing if
      // navigating forward.

      if (collapsed && currentActiveFormats.length === 0) {
        if (start === 0 && isReverse) {
          return;
        }

        if (end === text.length && !isReverse) {
          return;
        }
      } // If the selection is not collapsed, let the browser handle collapsing
      // the selection for now. Later we could expand this logic to set
      // boundary positions if needed.


      if (!collapsed) {
        return;
      }

      const formatsBefore = formats[start - 1] || EMPTY_ACTIVE_FORMATS;
      const formatsAfter = formats[start] || EMPTY_ACTIVE_FORMATS;
      const destination = isReverse ? formatsBefore : formatsAfter;
      const isIncreasing = currentActiveFormats.every((format, index) => format === destination[index]);
      let newActiveFormatsLength = currentActiveFormats.length;

      if (!isIncreasing) {
        newActiveFormatsLength--;
      } else if (newActiveFormatsLength < destination.length) {
        newActiveFormatsLength++;
      }

      if (newActiveFormatsLength === currentActiveFormats.length) {
        record.current._newActiveFormats = destination;
        return;
      }

      event.preventDefault();
      const origin = isReverse ? formatsAfter : formatsBefore;
      const source = isIncreasing ? destination : origin;
      const newActiveFormats = source.slice(0, newActiveFormatsLength);
      const newValue = { ...record.current,
        activeFormats: newActiveFormats
      };
      record.current = newValue;
      applyRecord(newValue);
      forceRender();
    }

    element.addEventListener('keydown', onKeyDown);
    return () => {
      element.removeEventListener('keydown', onKeyDown);
    };
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-select-object.js
/**
 * WordPress dependencies
 */

function useSelectObject() {
  return (0,external_wp_compose_namespaceObject.useRefEffect)(element => {
    function onClick(event) {
      const {
        target
      } = event; // If the child element has no text content, it must be an object.

      if (target === element || target.textContent) {
        return;
      }

      const {
        ownerDocument
      } = target;
      const {
        defaultView
      } = ownerDocument;
      const range = ownerDocument.createRange();
      const selection = defaultView.getSelection();
      range.selectNode(target);
      selection.removeAllRanges();
      selection.addRange(range);
    }

    element.addEventListener('click', onClick);
    return () => {
      element.removeEventListener('click', onClick);
    };
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-indent-list-item-on-space.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




function useIndentListItemOnSpace(props) {
  const propsRef = (0,external_wp_element_namespaceObject.useRef)(props);
  propsRef.current = props;
  return (0,external_wp_compose_namespaceObject.useRefEffect)(element => {
    function onKeyDown(event) {
      const {
        keyCode,
        shiftKey,
        altKey,
        metaKey,
        ctrlKey
      } = event;
      const {
        multilineTag,
        createRecord,
        handleChange
      } = propsRef.current;

      if ( // Only override when no modifiers are pressed.
      shiftKey || altKey || metaKey || ctrlKey || keyCode !== external_wp_keycodes_namespaceObject.SPACE || multilineTag !== 'li') {
        return;
      }

      const currentValue = createRecord();

      if (!isCollapsed(currentValue)) {
        return;
      }

      const {
        text,
        start
      } = currentValue;
      const characterBefore = text[start - 1]; // The caret must be at the start of a line.

      if (characterBefore && characterBefore !== LINE_SEPARATOR) {
        return;
      }

      handleChange(indentListItems(currentValue, {
        type: element.tagName.toLowerCase()
      }));
      event.preventDefault();
    }

    element.addEventListener('keydown', onKeyDown);
    return () => {
      element.removeEventListener('keydown', onKeyDown);
    };
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/update-formats.js
/**
 * Internal dependencies
 */

/** @typedef {import('./create').RichTextValue} RichTextValue */

/**
 * Efficiently updates all the formats from `start` (including) until `end`
 * (excluding) with the active formats. Mutates `value`.
 *
 * @param {Object}        $1         Named paramentes.
 * @param {RichTextValue} $1.value   Value te update.
 * @param {number}        $1.start   Index to update from.
 * @param {number}        $1.end     Index to update until.
 * @param {Array}         $1.formats Replacement formats.
 *
 * @return {RichTextValue} Mutated value.
 */

function updateFormats(_ref) {
  let {
    value,
    start,
    end,
    formats
  } = _ref;
  // Start and end may be switched in case of delete.
  const min = Math.min(start, end);
  const max = Math.max(start, end);
  const formatsBefore = value.formats[min - 1] || [];
  const formatsAfter = value.formats[max] || []; // First, fix the references. If any format right before or after are
  // equal, the replacement format should use the same reference.

  value.activeFormats = formats.map((format, index) => {
    if (formatsBefore[index]) {
      if (isFormatEqual(format, formatsBefore[index])) {
        return formatsBefore[index];
      }
    } else if (formatsAfter[index]) {
      if (isFormatEqual(format, formatsAfter[index])) {
        return formatsAfter[index];
      }
    }

    return format;
  });

  while (--end >= start) {
    if (value.activeFormats.length > 0) {
      value.formats[end] = value.activeFormats;
    } else {
      delete value.formats[end];
    }
  }

  return value;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-input-and-selection.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



/**
 * All inserting input types that would insert HTML into the DOM.
 *
 * @see https://www.w3.org/TR/input-events-2/#interface-InputEvent-Attributes
 *
 * @type {Set}
 */

const INSERTION_INPUT_TYPES_TO_IGNORE = new Set(['insertParagraph', 'insertOrderedList', 'insertUnorderedList', 'insertHorizontalRule', 'insertLink']);
const use_input_and_selection_EMPTY_ACTIVE_FORMATS = [];
/**
 * If the selection is set on the placeholder element, collapse the selection to
 * the start (before the placeholder).
 *
 * @param {Window} defaultView
 */

function fixPlaceholderSelection(defaultView) {
  const selection = defaultView.getSelection();
  const {
    anchorNode,
    anchorOffset
  } = selection;

  if (anchorNode.nodeType !== anchorNode.ELEMENT_NODE) {
    return;
  }

  const targetNode = anchorNode.childNodes[anchorOffset];

  if (!targetNode || targetNode.nodeType !== targetNode.ELEMENT_NODE || !targetNode.getAttribute('data-rich-text-placeholder')) {
    return;
  }

  selection.collapseToStart();
}

function useInputAndSelection(props) {
  const propsRef = (0,external_wp_element_namespaceObject.useRef)(props);
  propsRef.current = props;
  return (0,external_wp_compose_namespaceObject.useRefEffect)(element => {
    const {
      ownerDocument
    } = element;
    const {
      defaultView
    } = ownerDocument;
    let isComposing = false;
    let rafId;

    function onInput(event) {
      // Do not trigger a change if characters are being composed.
      // Browsers  will usually emit a final `input` event when the
      // characters are composed.
      // As of December 2019, Safari doesn't support
      // nativeEvent.isComposing.
      if (isComposing) {
        return;
      }

      let inputType;

      if (event) {
        inputType = event.inputType;
      }

      const {
        record,
        applyRecord,
        createRecord,
        handleChange
      } = propsRef.current; // The browser formatted something or tried to insert HTML.
      // Overwrite it. It will be handled later by the format library if
      // needed.

      if (inputType && (inputType.indexOf('format') === 0 || INSERTION_INPUT_TYPES_TO_IGNORE.has(inputType))) {
        applyRecord(record.current);
        return;
      }

      const currentValue = createRecord();
      const {
        start,
        activeFormats: oldActiveFormats = []
      } = record.current; // Update the formats between the last and new caret position.

      const change = updateFormats({
        value: currentValue,
        start,
        end: currentValue.start,
        formats: oldActiveFormats
      });
      handleChange(change);
    }
    /**
     * Syncs the selection to local state. A callback for the `selectionchange`
     * native events, `keyup`, `mouseup` and `touchend` synthetic events, and
     * animation frames after the `focus` event.
     *
     * @param {Event|DOMHighResTimeStamp} event
     */


    function handleSelectionChange(event) {
      const {
        record,
        applyRecord,
        createRecord,
        isSelected,
        onSelectionChange
      } = propsRef.current; // Check if the implementor disabled editing. `contentEditable`
      // does disable input, but not text selection, so we must ignore
      // selection changes.

      if (element.contentEditable !== 'true') {
        return;
      } // If the selection changes where the active element is a parent of
      // the rich text instance (writing flow), call `onSelectionChange`
      // for the rich text instance that contains the start or end of the
      // selection.


      if (ownerDocument.activeElement !== element) {
        if (!ownerDocument.activeElement.contains(element)) {
          return;
        }

        const selection = defaultView.getSelection();
        const {
          anchorNode,
          focusNode
        } = selection;

        if (element.contains(anchorNode) && element !== anchorNode && element.contains(focusNode) && element !== focusNode) {
          const {
            start,
            end
          } = createRecord();
          record.current.activeFormats = use_input_and_selection_EMPTY_ACTIVE_FORMATS;
          onSelectionChange(start, end);
        } else if (element.contains(anchorNode) && element !== anchorNode) {
          const {
            start,
            end: offset = start
          } = createRecord();
          record.current.activeFormats = use_input_and_selection_EMPTY_ACTIVE_FORMATS;
          onSelectionChange(offset);
        } else if (element.contains(focusNode) && element !== focusNode) {
          const {
            start,
            end: offset = start
          } = createRecord();
          record.current.activeFormats = use_input_and_selection_EMPTY_ACTIVE_FORMATS;
          onSelectionChange(undefined, offset);
        }

        return;
      }

      if (event.type !== 'selectionchange' && !isSelected) {
        return;
      } // In case of a keyboard event, ignore selection changes during
      // composition.


      if (isComposing) {
        return;
      }

      const {
        start,
        end,
        text
      } = createRecord();
      const oldRecord = record.current; // Fallback mechanism for IE11, which doesn't support the input event.
      // Any input results in a selection change.

      if (text !== oldRecord.text) {
        onInput();
        return;
      }

      if (start === oldRecord.start && end === oldRecord.end) {
        // Sometimes the browser may set the selection on the placeholder
        // element, in which case the caret is not visible. We need to set
        // the caret before the placeholder if that's the case.
        if (oldRecord.text.length === 0 && start === 0) {
          fixPlaceholderSelection(defaultView);
        }

        return;
      }

      const newValue = { ...oldRecord,
        start,
        end,
        // _newActiveFormats may be set on arrow key navigation to control
        // the right boundary position. If undefined, getActiveFormats will
        // give the active formats according to the browser.
        activeFormats: oldRecord._newActiveFormats,
        _newActiveFormats: undefined
      };
      const newActiveFormats = getActiveFormats(newValue, use_input_and_selection_EMPTY_ACTIVE_FORMATS); // Update the value with the new active formats.

      newValue.activeFormats = newActiveFormats; // It is important that the internal value is updated first,
      // otherwise the value will be wrong on render!

      record.current = newValue;
      applyRecord(newValue, {
        domOnly: true
      });
      onSelectionChange(start, end);
    }

    function onCompositionStart() {
      isComposing = true; // Do not update the selection when characters are being composed as
      // this rerenders the component and might destroy internal browser
      // editing state.

      ownerDocument.removeEventListener('selectionchange', handleSelectionChange);
    }

    function onCompositionEnd() {
      isComposing = false; // Ensure the value is up-to-date for browsers that don't emit a final
      // input event after composition.

      onInput({
        inputType: 'insertText'
      }); // Tracking selection changes can be resumed.

      ownerDocument.addEventListener('selectionchange', handleSelectionChange);
    }

    function onFocus() {
      const {
        record,
        isSelected,
        onSelectionChange,
        applyRecord
      } = propsRef.current; // When the whole editor is editable, let writing flow handle
      // selection.

      if (element.parentElement.closest('[contenteditable="true"]')) {
        return;
      }

      if (!isSelected) {
        // We know for certain that on focus, the old selection is invalid.
        // It will be recalculated on the next mouseup, keyup, or touchend
        // event.
        const index = undefined;
        record.current = { ...record.current,
          start: index,
          end: index,
          activeFormats: use_input_and_selection_EMPTY_ACTIVE_FORMATS
        };
        onSelectionChange(index, index);
      } else {
        applyRecord(record.current);
        onSelectionChange(record.current.start, record.current.end);
      } // Update selection as soon as possible, which is at the next animation
      // frame. The event listener for selection changes may be added too late
      // at this point, but this focus event is still too early to calculate
      // the selection.


      rafId = defaultView.requestAnimationFrame(handleSelectionChange);
    }

    element.addEventListener('input', onInput);
    element.addEventListener('compositionstart', onCompositionStart);
    element.addEventListener('compositionend', onCompositionEnd);
    element.addEventListener('focus', onFocus); // Selection updates must be done at these events as they
    // happen before the `selectionchange` event. In some cases,
    // the `selectionchange` event may not even fire, for
    // example when the window receives focus again on click.

    element.addEventListener('keyup', handleSelectionChange);
    element.addEventListener('mouseup', handleSelectionChange);
    element.addEventListener('touchend', handleSelectionChange);
    ownerDocument.addEventListener('selectionchange', handleSelectionChange);
    return () => {
      element.removeEventListener('input', onInput);
      element.removeEventListener('compositionstart', onCompositionStart);
      element.removeEventListener('compositionend', onCompositionEnd);
      element.removeEventListener('focus', onFocus);
      element.removeEventListener('keyup', handleSelectionChange);
      element.removeEventListener('mouseup', handleSelectionChange);
      element.removeEventListener('touchend', handleSelectionChange);
      ownerDocument.removeEventListener('selectionchange', handleSelectionChange);
      defaultView.cancelAnimationFrame(rafId);
    };
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-delete.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */




function useDelete(props) {
  const propsRef = (0,external_wp_element_namespaceObject.useRef)(props);
  propsRef.current = props;
  return (0,external_wp_compose_namespaceObject.useRefEffect)(element => {
    function onKeyDown(event) {
      const {
        keyCode
      } = event;
      const {
        createRecord,
        handleChange,
        multilineTag
      } = propsRef.current;

      if (event.defaultPrevented) {
        return;
      }

      if (keyCode !== external_wp_keycodes_namespaceObject.DELETE && keyCode !== external_wp_keycodes_namespaceObject.BACKSPACE) {
        return;
      }

      const currentValue = createRecord();
      const {
        start,
        end,
        text
      } = currentValue;
      const isReverse = keyCode === external_wp_keycodes_namespaceObject.BACKSPACE; // Always handle full content deletion ourselves.

      if (start === 0 && end !== 0 && end === text.length) {
        handleChange(remove(currentValue));
        event.preventDefault();
        return;
      }

      if (multilineTag) {
        let newValue; // Check to see if we should remove the first item if empty.

        if (isReverse && currentValue.start === 0 && currentValue.end === 0 && isEmptyLine(currentValue)) {
          newValue = removeLineSeparator(currentValue, !isReverse);
        } else {
          newValue = removeLineSeparator(currentValue, isReverse);
        }

        if (newValue) {
          handleChange(newValue);
          event.preventDefault();
        }
      }
    }

    element.addEventListener('keydown', onKeyDown);
    return () => {
      element.removeEventListener('keydown', onKeyDown);
    };
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/use-space.js
/**
 * WordPress dependencies
 */


/**
 * For some elements like BUTTON and SUMMARY, the space key doesn't insert a
 * space character in some browsers even though the element is editable. We have
 * to manually insert a space and prevent default behaviour.
 *
 * DO NOT limit this behaviour to specific tag names! It would mean that this
 * behaviour is not widely tested. If there's ever any problems, we should find
 * a different solution entirely or remove it entirely.
 */

function useSpace() {
  return (0,external_wp_compose_namespaceObject.useRefEffect)(element => {
    function onKeyDown(event) {
      // Don't insert a space if default behaviour is prevented.
      if (event.defaultPrevented) {
        return;
      }

      const {
        keyCode,
        altKey,
        metaKey,
        ctrlKey,
        key
      } = event; // Only consider the space key without modifiers pressed.

      if (keyCode !== external_wp_keycodes_namespaceObject.SPACE || altKey || metaKey || ctrlKey) {
        return;
      } // Disregard character composition that involves the Space key.
      //
      // @see https://github.com/WordPress/gutenberg/issues/35086
      //
      // For example, to input a standalone diacritic (like ´ or `) using a
      // keyboard with dead keys, one must first press the dead key and then
      // press the Space key.
      //
      // Many operating systems handle this in such a way that the second
      // KeyboardEvent contains the property `keyCode: 229`. According to the
      // spec, 229 allows the system to indicate that an Input Method Editor
      // (IDE) is processing some key input.
      //
      // However, Windows doesn't use `keyCode: 229` for dead key composition,
      // instead emitting an event with values `keyCode: SPACE` and `key: '´'`.
      // That is why checking the `key` property for values other than `SPACE`
      // is important.
      //
      // This should serve as a reminder that the `KeyboardEvent.keyCode`
      // attribute is officially deprecated and that we should consider more
      // consistent interfaces.


      if (key !== ' ') {
        return;
      }

      event.target.ownerDocument.execCommand('insertText', false, ' ');
      event.preventDefault();
    }

    element.addEventListener('keydown', onKeyDown);
    return () => {
      element.removeEventListener('keydown', onKeyDown);
    };
  }, []);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/index.js
/**
 * WordPress dependencies
 */



/**
 * Internal dependencies
 */













function useRichText(_ref) {
  let {
    value = '',
    selectionStart,
    selectionEnd,
    placeholder,
    preserveWhiteSpace,
    onSelectionChange,
    onChange,
    __unstableMultilineTag: multilineTag,
    __unstableDisableFormats: disableFormats,
    __unstableIsSelected: isSelected,
    __unstableDependencies = [],
    __unstableAfterParse,
    __unstableBeforeSerialize,
    __unstableAddInvisibleFormats
  } = _ref;
  const registry = (0,external_wp_data_namespaceObject.useRegistry)();
  const [, forceRender] = (0,external_wp_element_namespaceObject.useReducer)(() => ({}));
  const ref = (0,external_wp_element_namespaceObject.useRef)();

  function createRecord() {
    const {
      ownerDocument: {
        defaultView
      }
    } = ref.current;
    const selection = defaultView.getSelection();
    const range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
    return create({
      element: ref.current,
      range,
      multilineTag,
      multilineWrapperTags: multilineTag === 'li' ? ['ul', 'ol'] : undefined,
      __unstableIsEditableTree: true,
      preserveWhiteSpace
    });
  }

  function applyRecord(newRecord) {
    let {
      domOnly
    } = arguments.length > 1 && arguments[1] !== undefined ? arguments[1] : {};
    apply({
      value: newRecord,
      current: ref.current,
      multilineTag,
      multilineWrapperTags: multilineTag === 'li' ? ['ul', 'ol'] : undefined,
      prepareEditableTree: __unstableAddInvisibleFormats,
      __unstableDomOnly: domOnly,
      placeholder
    });
  } // Internal values are updated synchronously, unlike props and state.


  const _value = (0,external_wp_element_namespaceObject.useRef)(value);

  const record = (0,external_wp_element_namespaceObject.useRef)();

  function setRecordFromProps() {
    _value.current = value;
    record.current = create({
      html: value,
      multilineTag,
      multilineWrapperTags: multilineTag === 'li' ? ['ul', 'ol'] : undefined,
      preserveWhiteSpace
    });

    if (disableFormats) {
      record.current.formats = Array(value.length);
      record.current.replacements = Array(value.length);
    }

    if (__unstableAfterParse) {
      record.current.formats = __unstableAfterParse(record.current);
    }

    record.current.start = selectionStart;
    record.current.end = selectionEnd;
  }

  const hadSelectionUpdate = (0,external_wp_element_namespaceObject.useRef)(false);

  if (!record.current) {
    var _record$current, _record$current$forma, _record$current$forma2;

    setRecordFromProps(); // Sometimes formats are added programmatically and we need to make
    // sure it's persisted to the block store / markup. If these formats
    // are not applied, they could cause inconsistencies between the data
    // in the visual editor and the frontend. Right now, it's only relevant
    // to the `core/text-color` format, which is applied at runtime in
    // certain circunstances. See the `__unstableFilterAttributeValue`
    // function in `packages/format-library/src/text-color/index.js`.
    // @todo find a less-hacky way of solving this.

    const hasRelevantInitFormat = ((_record$current = record.current) === null || _record$current === void 0 ? void 0 : (_record$current$forma = _record$current.formats[0]) === null || _record$current$forma === void 0 ? void 0 : (_record$current$forma2 = _record$current$forma[0]) === null || _record$current$forma2 === void 0 ? void 0 : _record$current$forma2.type) === 'core/text-color';

    if (hasRelevantInitFormat) {
      handleChangesUponInit(record.current);
    }
  } else if (selectionStart !== record.current.start || selectionEnd !== record.current.end) {
    hadSelectionUpdate.current = isSelected;
    record.current = { ...record.current,
      start: selectionStart,
      end: selectionEnd
    };
  }
  /**
   * Sync the value to global state. The node tree and selection will also be
   * updated if differences are found.
   *
   * @param {Object} newRecord The record to sync and apply.
   */


  function handleChange(newRecord) {
    record.current = newRecord;
    applyRecord(newRecord);

    if (disableFormats) {
      _value.current = newRecord.text;
    } else {
      _value.current = toHTMLString({
        value: __unstableBeforeSerialize ? { ...newRecord,
          formats: __unstableBeforeSerialize(newRecord)
        } : newRecord,
        multilineTag,
        preserveWhiteSpace
      });
    }

    const {
      start,
      end,
      formats,
      text
    } = newRecord; // Selection must be updated first, so it is recorded in history when
    // the content change happens.
    // We batch both calls to only attempt to rerender once.

    registry.batch(() => {
      onSelectionChange(start, end);
      onChange(_value.current, {
        __unstableFormats: formats,
        __unstableText: text
      });
    });
    forceRender();
  }

  function handleChangesUponInit(newRecord) {
    record.current = newRecord;
    _value.current = toHTMLString({
      value: __unstableBeforeSerialize ? { ...newRecord,
        formats: __unstableBeforeSerialize(newRecord)
      } : newRecord,
      multilineTag,
      preserveWhiteSpace
    });
    const {
      formats,
      text
    } = newRecord;
    registry.batch(() => {
      onChange(_value.current, {
        __unstableFormats: formats,
        __unstableText: text
      });
    });
    forceRender();
  }

  function applyFromProps() {
    setRecordFromProps();
    applyRecord(record.current);
  }

  const didMount = (0,external_wp_element_namespaceObject.useRef)(false); // Value updates must happen synchonously to avoid overwriting newer values.

  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    if (didMount.current && value !== _value.current) {
      applyFromProps();
      forceRender();
    }
  }, [value]); // Value updates must happen synchonously to avoid overwriting newer values.

  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    if (!hadSelectionUpdate.current) {
      return;
    }

    if (ref.current.ownerDocument.activeElement !== ref.current) {
      ref.current.focus();
    }

    applyFromProps();
    hadSelectionUpdate.current = false;
  }, [hadSelectionUpdate.current]);
  const mergedRefs = (0,external_wp_compose_namespaceObject.useMergeRefs)([ref, useDefaultStyle(), useBoundaryStyle({
    record
  }), useCopyHandler({
    record,
    multilineTag,
    preserveWhiteSpace
  }), useSelectObject(), useFormatBoundaries({
    record,
    applyRecord
  }), useDelete({
    createRecord,
    handleChange,
    multilineTag
  }), useIndentListItemOnSpace({
    multilineTag,
    createRecord,
    handleChange
  }), useInputAndSelection({
    record,
    applyRecord,
    createRecord,
    handleChange,
    isSelected,
    onSelectionChange
  }), useSpace(), (0,external_wp_compose_namespaceObject.useRefEffect)(() => {
    applyFromProps();
    didMount.current = true;
  }, [placeholder, ...__unstableDependencies])]);
  return {
    value: record.current,
    onChange: handleChange,
    ref: mergedRefs
  };
}
function __experimentalRichText() {}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/component/format-edit.js


/**
 * Internal dependencies
 */


function FormatEdit(_ref) {
  let {
    formatTypes,
    onChange,
    onFocus,
    value,
    forwardedRef
  } = _ref;
  return formatTypes.map(settings => {
    const {
      name,
      edit: Edit
    } = settings;

    if (!Edit) {
      return null;
    }

    const activeFormat = getActiveFormat(value, name);
    const isActive = activeFormat !== undefined;
    const activeObject = getActiveObject(value);
    const isObjectActive = activeObject !== undefined && activeObject.type === name;
    return (0,external_wp_element_namespaceObject.createElement)(Edit, {
      key: name,
      isActive: isActive,
      activeAttributes: isActive ? activeFormat.attributes || {} : {},
      isObjectActive: isObjectActive,
      activeObjectAttributes: isObjectActive ? activeObject.attributes || {} : {},
      value: value,
      onChange: onChange,
      onFocus: onFocus,
      contentRef: forwardedRef
    });
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/rich-text/build-module/index.js





































(window.wp = window.wp || {}).richText = __webpack_exports__;
/******/ })()
;