<<<<<<< HEAD
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ 4470:
/***/ ((__unused_webpack_module, exports, __webpack_require__) => {



var m = __webpack_require__(1850);
if (true) {
  exports.s = m.createRoot;
  exports.a = m.hydrateRoot;
} else { var i; }


/***/ }),

/***/ 1850:
/***/ ((module) => {

module.exports = window["ReactDOM"];

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
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  Children: () => (/* reexport */ external_React_namespaceObject.Children),
  Component: () => (/* reexport */ external_React_namespaceObject.Component),
  Fragment: () => (/* reexport */ external_React_namespaceObject.Fragment),
  Platform: () => (/* reexport */ platform),
  RawHTML: () => (/* reexport */ RawHTML),
  StrictMode: () => (/* reexport */ external_React_namespaceObject.StrictMode),
  Suspense: () => (/* reexport */ external_React_namespaceObject.Suspense),
  cloneElement: () => (/* reexport */ external_React_namespaceObject.cloneElement),
  concatChildren: () => (/* reexport */ concatChildren),
  createContext: () => (/* reexport */ external_React_namespaceObject.createContext),
  createElement: () => (/* reexport */ external_React_namespaceObject.createElement),
  createInterpolateElement: () => (/* reexport */ create_interpolate_element),
  createPortal: () => (/* reexport */ external_ReactDOM_.createPortal),
  createRef: () => (/* reexport */ external_React_namespaceObject.createRef),
  createRoot: () => (/* reexport */ client/* createRoot */.s),
  findDOMNode: () => (/* reexport */ external_ReactDOM_.findDOMNode),
  flushSync: () => (/* reexport */ external_ReactDOM_.flushSync),
  forwardRef: () => (/* reexport */ external_React_namespaceObject.forwardRef),
  hydrate: () => (/* reexport */ external_ReactDOM_.hydrate),
  hydrateRoot: () => (/* reexport */ client/* hydrateRoot */.a),
  isEmptyElement: () => (/* reexport */ isEmptyElement),
  isValidElement: () => (/* reexport */ external_React_namespaceObject.isValidElement),
  lazy: () => (/* reexport */ external_React_namespaceObject.lazy),
  memo: () => (/* reexport */ external_React_namespaceObject.memo),
  render: () => (/* reexport */ external_ReactDOM_.render),
  renderToString: () => (/* reexport */ serialize),
  startTransition: () => (/* reexport */ external_React_namespaceObject.startTransition),
  switchChildrenNodeName: () => (/* reexport */ switchChildrenNodeName),
  unmountComponentAtNode: () => (/* reexport */ external_ReactDOM_.unmountComponentAtNode),
  useCallback: () => (/* reexport */ external_React_namespaceObject.useCallback),
  useContext: () => (/* reexport */ external_React_namespaceObject.useContext),
  useDebugValue: () => (/* reexport */ external_React_namespaceObject.useDebugValue),
  useDeferredValue: () => (/* reexport */ external_React_namespaceObject.useDeferredValue),
  useEffect: () => (/* reexport */ external_React_namespaceObject.useEffect),
  useId: () => (/* reexport */ external_React_namespaceObject.useId),
  useImperativeHandle: () => (/* reexport */ external_React_namespaceObject.useImperativeHandle),
  useInsertionEffect: () => (/* reexport */ external_React_namespaceObject.useInsertionEffect),
  useLayoutEffect: () => (/* reexport */ external_React_namespaceObject.useLayoutEffect),
  useMemo: () => (/* reexport */ external_React_namespaceObject.useMemo),
  useReducer: () => (/* reexport */ external_React_namespaceObject.useReducer),
  useRef: () => (/* reexport */ external_React_namespaceObject.useRef),
  useState: () => (/* reexport */ external_React_namespaceObject.useState),
  useSyncExternalStore: () => (/* reexport */ external_React_namespaceObject.useSyncExternalStore),
  useTransition: () => (/* reexport */ external_React_namespaceObject.useTransition)
});

;// CONCATENATED MODULE: external "React"
const external_React_namespaceObject = window["React"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/create-interpolate-element.js
/**
 * Internal dependencies
 */

/** @typedef {import('./react').WPElement} WPElement */

let indoc, offset, output, stack;
/**
 * Matches tags in the localized string
 *
 * This is used for extracting the tag pattern groups for parsing the localized
 * string and along with the map converting it to a react element.
 *
 * There are four references extracted using this tokenizer:
 *
 * match: Full match of the tag (i.e. <strong>, </strong>, <br/>)
 * isClosing: The closing slash, if it exists.
 * name: The name portion of the tag (strong, br) (if )
 * isSelfClosed: The slash on a self closing tag, if it exists.
 *
 * @type {RegExp}
 */

const tokenizer = /<(\/)?(\w+)\s*(\/)?>/g;
/**
 * The stack frame tracking parse progress.
 *
 * @typedef Frame
 *
 * @property {WPElement}   element            A parent element which may still have
 * @property {number}      tokenStart         Offset at which parent element first
 *                                            appears.
 * @property {number}      tokenLength        Length of string marking start of parent
 *                                            element.
 * @property {number}      [prevOffset]       Running offset at which parsing should
 *                                            continue.
 * @property {number}      [leadingTextStart] Offset at which last closing element
 *                                            finished, used for finding text between
 *                                            elements.
 * @property {WPElement[]} children           Children.
 */

/**
 * Tracks recursive-descent parse state.
 *
 * This is a Stack frame holding parent elements until all children have been
 * parsed.
 *
 * @private
 * @param {WPElement} element            A parent element which may still have
 *                                       nested children not yet parsed.
 * @param {number}    tokenStart         Offset at which parent element first
 *                                       appears.
 * @param {number}    tokenLength        Length of string marking start of parent
 *                                       element.
 * @param {number}    [prevOffset]       Running offset at which parsing should
 *                                       continue.
 * @param {number}    [leadingTextStart] Offset at which last closing element
 *                                       finished, used for finding text between
 *                                       elements.
 *
 * @return {Frame} The stack frame tracking parse progress.
 */

function createFrame(element, tokenStart, tokenLength, prevOffset, leadingTextStart) {
  return {
    element,
    tokenStart,
    tokenLength,
    prevOffset,
    leadingTextStart,
    children: []
  };
}
/**
 * This function creates an interpolated element from a passed in string with
 * specific tags matching how the string should be converted to an element via
 * the conversion map value.
 *
 * @example
 * For example, for the given string:
 *
 * "This is a <span>string</span> with <a>a link</a> and a self-closing
 * <CustomComponentB/> tag"
 *
 * You would have something like this as the conversionMap value:
 *
 * ```js
 * {
 *     span: <span />,
 *     a: <a href={ 'https://github.com' } />,
 *     CustomComponentB: <CustomComponent />,
 * }
 * ```
 *
 * @param {string}                    interpolatedString The interpolation string to be parsed.
 * @param {Record<string, WPElement>} conversionMap      The map used to convert the string to
 *                                                       a react element.
 * @throws {TypeError}
 * @return {WPElement}  A wp element.
 */


const createInterpolateElement = (interpolatedString, conversionMap) => {
  indoc = interpolatedString;
  offset = 0;
  output = [];
  stack = [];
  tokenizer.lastIndex = 0;

  if (!isValidConversionMap(conversionMap)) {
    throw new TypeError('The conversionMap provided is not valid. It must be an object with values that are WPElements');
  }

  do {// twiddle our thumbs
  } while (proceed(conversionMap));

  return (0,external_React_namespaceObject.createElement)(external_React_namespaceObject.Fragment, null, ...output);
};
/**
 * Validate conversion map.
 *
 * A map is considered valid if it's an object and every value in the object
 * is a WPElement
 *
 * @private
 *
 * @param {Object} conversionMap The map being validated.
 *
 * @return {boolean}  True means the map is valid.
 */


const isValidConversionMap = conversionMap => {
  const isObject = typeof conversionMap === 'object';
  const values = isObject && Object.values(conversionMap);
  return isObject && values.length && values.every(element => (0,external_React_namespaceObject.isValidElement)(element));
};
/**
 * This is the iterator over the matches in the string.
 *
 * @private
 *
 * @param {Object} conversionMap The conversion map for the string.
 *
 * @return {boolean} true for continuing to iterate, false for finished.
 */


function proceed(conversionMap) {
  const next = nextToken();
  const [tokenType, name, startOffset, tokenLength] = next;
  const stackDepth = stack.length;
  const leadingTextStart = startOffset > offset ? offset : null;

  if (!conversionMap[name]) {
    addText();
    return false;
  }

  switch (tokenType) {
    case 'no-more-tokens':
      if (stackDepth !== 0) {
        const {
          leadingTextStart: stackLeadingText,
          tokenStart
        } = stack.pop();
        output.push(indoc.substr(stackLeadingText, tokenStart));
      }

      addText();
      return false;

    case 'self-closed':
      if (0 === stackDepth) {
        if (null !== leadingTextStart) {
          output.push(indoc.substr(leadingTextStart, startOffset - leadingTextStart));
        }

        output.push(conversionMap[name]);
        offset = startOffset + tokenLength;
        return true;
      } // Otherwise we found an inner element.


      addChild(createFrame(conversionMap[name], startOffset, tokenLength));
      offset = startOffset + tokenLength;
      return true;

    case 'opener':
      stack.push(createFrame(conversionMap[name], startOffset, tokenLength, startOffset + tokenLength, leadingTextStart));
      offset = startOffset + tokenLength;
      return true;

    case 'closer':
      // If we're not nesting then this is easy - close the block.
      if (1 === stackDepth) {
        closeOuterElement(startOffset);
        offset = startOffset + tokenLength;
        return true;
      } // Otherwise we're nested and we have to close out the current
      // block and add it as a innerBlock to the parent.


      const stackTop = stack.pop();
      const text = indoc.substr(stackTop.prevOffset, startOffset - stackTop.prevOffset);
      stackTop.children.push(text);
      stackTop.prevOffset = startOffset + tokenLength;
      const frame = createFrame(stackTop.element, stackTop.tokenStart, stackTop.tokenLength, startOffset + tokenLength);
      frame.children = stackTop.children;
      addChild(frame);
      offset = startOffset + tokenLength;
      return true;

    default:
      addText();
      return false;
  }
}
/**
 * Grabs the next token match in the string and returns it's details.
 *
 * @private
 *
 * @return {Array}  An array of details for the token matched.
 */


function nextToken() {
  const matches = tokenizer.exec(indoc); // We have no more tokens.

  if (null === matches) {
    return ['no-more-tokens'];
  }

  const startedAt = matches.index;
  const [match, isClosing, name, isSelfClosed] = matches;
  const length = match.length;

  if (isSelfClosed) {
    return ['self-closed', name, startedAt, length];
  }

  if (isClosing) {
    return ['closer', name, startedAt, length];
  }

  return ['opener', name, startedAt, length];
}
/**
 * Pushes text extracted from the indoc string to the output stack given the
 * current rawLength value and offset (if rawLength is provided ) or the
 * indoc.length and offset.
 *
 * @private
 */


function addText() {
  const length = indoc.length - offset;

  if (0 === length) {
    return;
  }

  output.push(indoc.substr(offset, length));
}
/**
 * Pushes a child element to the associated parent element's children for the
 * parent currently active in the stack.
 *
 * @private
 *
 * @param {Frame} frame The Frame containing the child element and it's
 *                      token information.
 */


function addChild(frame) {
  const {
    element,
    tokenStart,
    tokenLength,
    prevOffset,
    children
  } = frame;
  const parent = stack[stack.length - 1];
  const text = indoc.substr(parent.prevOffset, tokenStart - parent.prevOffset);

  if (text) {
    parent.children.push(text);
  }

  parent.children.push((0,external_React_namespaceObject.cloneElement)(element, null, ...children));
  parent.prevOffset = prevOffset ? prevOffset : tokenStart + tokenLength;
}
/**
 * This is called for closing tags. It creates the element currently active in
 * the stack.
 *
 * @private
 *
 * @param {number} endOffset Offset at which the closing tag for the element
 *                           begins in the string. If this is greater than the
 *                           prevOffset attached to the element, then this
 *                           helps capture any remaining nested text nodes in
 *                           the element.
 */


function closeOuterElement(endOffset) {
  const {
    element,
    leadingTextStart,
    prevOffset,
    tokenStart,
    children
  } = stack.pop();
  const text = endOffset ? indoc.substr(prevOffset, endOffset - prevOffset) : indoc.substr(prevOffset);

  if (text) {
    children.push(text);
  }

  if (null !== leadingTextStart) {
    output.push(indoc.substr(leadingTextStart, tokenStart - leadingTextStart));
  }

  output.push((0,external_React_namespaceObject.cloneElement)(element, null, ...children));
}

/* harmony default export */ const create_interpolate_element = (createInterpolateElement);

;// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/react.js
/**
 * External dependencies
 */
// eslint-disable-next-line @typescript-eslint/no-restricted-imports

/**
 * Object containing a React element.
 *
 * @typedef {import('react').ReactElement} WPElement
 */

/**
 * Object containing a React component.
 *
 * @typedef {import('react').ComponentType} WPComponent
 */

/**
 * Object containing a React synthetic event.
 *
 * @typedef {import('react').SyntheticEvent} WPSyntheticEvent
 */

/**
 * Object containing a React synthetic event.
 *
 * @template T
 * @typedef {import('react').RefObject<T>} RefObject<T>
 */

/**
 * Object that provides utilities for dealing with React children.
 */
=======
this["wp"] = this["wp"] || {}; this["wp"]["element"] =
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
/******/ 	return __webpack_require__(__webpack_require__.s = "o/Ny");
/******/ })
/************************************************************************/
/******/ ({

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

/***/ "U8pU":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
/* harmony export (binding) */ __webpack_require__.d(__webpack_exports__, "a", function() { return _typeof; });
function _typeof(obj) {
  "@babel/helpers - typeof";

  if (typeof Symbol === "function" && typeof Symbol.iterator === "symbol") {
    _typeof = function _typeof(obj) {
      return typeof obj;
    };
  } else {
    _typeof = function _typeof(obj) {
      return obj && typeof Symbol === "function" && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
    };
  }

  return _typeof(obj);
}

/***/ }),

/***/ "Vx3V":
/***/ (function(module, exports) {

(function() { module.exports = this["wp"]["escapeHtml"]; }());

/***/ }),

/***/ "YLtl":
/***/ (function(module, exports) {

(function() { module.exports = this["lodash"]; }());

/***/ }),

/***/ "cDcd":
/***/ (function(module, exports) {

(function() { module.exports = this["React"]; }());

/***/ }),

/***/ "faye":
/***/ (function(module, exports) {

(function() { module.exports = this["ReactDOM"]; }());

/***/ }),

/***/ "o/Ny":
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, "Children", function() { return /* reexport */ external_React_["Children"]; });
__webpack_require__.d(__webpack_exports__, "cloneElement", function() { return /* reexport */ external_React_["cloneElement"]; });
__webpack_require__.d(__webpack_exports__, "Component", function() { return /* reexport */ external_React_["Component"]; });
__webpack_require__.d(__webpack_exports__, "createContext", function() { return /* reexport */ external_React_["createContext"]; });
__webpack_require__.d(__webpack_exports__, "createElement", function() { return /* reexport */ external_React_["createElement"]; });
__webpack_require__.d(__webpack_exports__, "createRef", function() { return /* reexport */ external_React_["createRef"]; });
__webpack_require__.d(__webpack_exports__, "forwardRef", function() { return /* reexport */ external_React_["forwardRef"]; });
__webpack_require__.d(__webpack_exports__, "Fragment", function() { return /* reexport */ external_React_["Fragment"]; });
__webpack_require__.d(__webpack_exports__, "isValidElement", function() { return /* reexport */ external_React_["isValidElement"]; });
__webpack_require__.d(__webpack_exports__, "StrictMode", function() { return /* reexport */ external_React_["StrictMode"]; });
__webpack_require__.d(__webpack_exports__, "concatChildren", function() { return /* reexport */ concatChildren; });
__webpack_require__.d(__webpack_exports__, "switchChildrenNodeName", function() { return /* reexport */ switchChildrenNodeName; });
__webpack_require__.d(__webpack_exports__, "createPortal", function() { return /* reexport */ external_ReactDOM_["createPortal"]; });
__webpack_require__.d(__webpack_exports__, "findDOMNode", function() { return /* reexport */ external_ReactDOM_["findDOMNode"]; });
__webpack_require__.d(__webpack_exports__, "render", function() { return /* reexport */ external_ReactDOM_["render"]; });
__webpack_require__.d(__webpack_exports__, "unmountComponentAtNode", function() { return /* reexport */ external_ReactDOM_["unmountComponentAtNode"]; });
__webpack_require__.d(__webpack_exports__, "isEmptyElement", function() { return /* reexport */ utils_isEmptyElement; });
__webpack_require__.d(__webpack_exports__, "renderToString", function() { return /* reexport */ serialize; });
__webpack_require__.d(__webpack_exports__, "RawHTML", function() { return /* reexport */ RawHTML; });

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectSpread.js
var objectSpread = __webpack_require__("vpQ4");

// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/objectWithoutProperties.js + 1 modules
var objectWithoutProperties = __webpack_require__("Ff2n");

// EXTERNAL MODULE: external "React"
var external_React_ = __webpack_require__("cDcd");

// EXTERNAL MODULE: external "lodash"
var external_lodash_ = __webpack_require__("YLtl");

// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/react.js



/**
 * External dependencies
 */

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9


/**
 * Creates a copy of an element with extended props.
 *
 * @param {WPElement} element Element
 * @param {?Object}   props   Props to apply to cloned element
 *
 * @return {WPElement} Cloned element.
 */


/**
 * A base class to create WordPress Components (Refs, state and lifecycle hooks)
 */


/**
 * Creates a context object containing two components: a provider and consumer.
 *
 * @param {Object} defaultValue A default data stored in the context.
 *
 * @return {Object} Context object.
 */


/**
 * Returns a new element of given type. Type can be either a string tag name or
 * another function which itself returns an element.
 *
 * @param {?(string|Function)} type     Tag name or element creator
 * @param {Object}             props    Element properties, either attribute
<<<<<<< HEAD
 *                                      set to apply to DOM node or values to
 *                                      pass through to element creator
=======
 *                                       set to apply to DOM node or values to
 *                                       pass through to element creator
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 * @param {...WPElement}       children Descendant elements
 *
 * @return {WPElement} Element.
 */


/**
 * Returns an object tracking a reference to a rendered element via its
 * `current` property as either a DOMElement or Element, dependent upon the
 * type of element rendered with the ref attribute.
 *
 * @return {Object} Ref object.
 */


/**
 * Component enhancer used to enable passing a ref to its wrapped component.
 * Pass a function argument which receives `props` and `ref` as its arguments,
 * returning an element using the forwarded ref. The return value is a new
 * component which forwards its ref.
 *
 * @param {Function} forwarder Function passed `props` and `ref`, expected to
 *                             return an element.
 *
 * @return {WPComponent} Enhanced component.
 */


/**
 * A component which renders its children without any wrapping element.
 */


/**
<<<<<<< HEAD
 * Checks if an object is a valid WPElement.
=======
 * Checks if an object is a valid WPElement
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @param {Object} objectToCheck The object to be checked.
 *
 * @return {boolean} true if objectToTest is a valid WPElement and false otherwise.
 */


<<<<<<< HEAD
/**
 * @see https://reactjs.org/docs/react-api.html#reactmemo
 */


/**
 * Component that activates additional checks and warnings for its descendants.
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#usecallback
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#usecontext
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#usedebugvalue
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#usedeferredvalue
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#useeffect
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#useid
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#useimperativehandle
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#useinsertioneffect
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#uselayouteffect
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#usememo
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#usereducer
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#useref
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#usestate
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#usesyncexternalstore
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#usetransition
 */


/**
 * @see https://reactjs.org/docs/react-api.html#starttransition
 */


/**
 * @see https://reactjs.org/docs/react-api.html#reactlazy
 */


/**
 * @see https://reactjs.org/docs/react-api.html#reactsuspense
 */

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

/**
 * Concatenate two or more React children objects.
 *
 * @param {...?Object} childrenArguments Array of children arguments (array of arrays/strings/objects) to concatenate.
 *
 * @return {Array} The concatenated value.
 */

<<<<<<< HEAD
function concatChildren(...childrenArguments) {
  return childrenArguments.reduce((accumulator, children, i) => {
    external_React_namespaceObject.Children.forEach(children, (child, j) => {
      if (child && 'string' !== typeof child) {
        child = (0,external_React_namespaceObject.cloneElement)(child, {
=======
function concatChildren() {
  for (var _len = arguments.length, childrenArguments = new Array(_len), _key = 0; _key < _len; _key++) {
    childrenArguments[_key] = arguments[_key];
  }

  return childrenArguments.reduce(function (memo, children, i) {
    external_React_["Children"].forEach(children, function (child, j) {
      if (child && 'string' !== typeof child) {
        child = Object(external_React_["cloneElement"])(child, {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
          key: [i, j].join()
        });
      }

<<<<<<< HEAD
      accumulator.push(child);
    });
    return accumulator;
=======
      memo.push(child);
    });
    return memo;
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  }, []);
}
/**
 * Switches the nodeName of all the elements in the children object.
 *
 * @param {?Object} children Children object.
 * @param {string}  nodeName Node name.
 *
 * @return {?Object} The updated children object.
 */

function switchChildrenNodeName(children, nodeName) {
<<<<<<< HEAD
  return children && external_React_namespaceObject.Children.map(children, (elt, index) => {
    if (typeof elt?.valueOf() === 'string') {
      return (0,external_React_namespaceObject.createElement)(nodeName, {
=======
  return children && external_React_["Children"].map(children, function (elt, index) {
    if (Object(external_lodash_["isString"])(elt)) {
      return Object(external_React_["createElement"])(nodeName, {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
        key: index
      }, elt);
    }

<<<<<<< HEAD
    const {
      children: childrenProp,
      ...props
    } = elt.props;
    return (0,external_React_namespaceObject.createElement)(nodeName, {
      key: index,
      ...props
    }, childrenProp);
=======
    var _elt$props = elt.props,
        childrenProp = _elt$props.children,
        props = Object(objectWithoutProperties["a" /* default */])(_elt$props, ["children"]);

    return Object(external_React_["createElement"])(nodeName, Object(objectSpread["a" /* default */])({
      key: index
    }, props), childrenProp);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  });
}

// EXTERNAL MODULE: external "ReactDOM"
<<<<<<< HEAD
var external_ReactDOM_ = __webpack_require__(1850);
// EXTERNAL MODULE: ./node_modules/react-dom/client.js
var client = __webpack_require__(4470);
;// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/react-platform.js
=======
var external_ReactDOM_ = __webpack_require__("faye");

// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/react-platform.js
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * External dependencies
 */

<<<<<<< HEAD

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Creates a portal into which a component can be rendered.
 *
 * @see https://github.com/facebook/react/issues/10309#issuecomment-318433235
 *
<<<<<<< HEAD
 * @param {import('./react').WPElement} child     Any renderable child, such as an element,
 *                                                string, or fragment.
 * @param {HTMLElement}                 container DOM node into which element should be rendered.
 */


/**
 * Finds the dom node of a React component.
 *
 * @param {import('./react').WPComponent} component Component's instance.
=======
 * @param {Component} component Component
 * @param {Element}   target    DOM node into which element should be rendered
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */


/**
<<<<<<< HEAD
 * Forces React to flush any updates inside the provided callback synchronously.
 *
 * @param {Function} callback Callback to run synchronously.
=======
 * Finds the dom node of a React component
 *
 * @param {Component} component component's instance
 * @param {Element}   target    DOM node into which element should be rendered
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */


/**
 * Renders a given element into the target DOM node.
 *
<<<<<<< HEAD
 * @deprecated since WordPress 6.2.0. Use `createRoot` instead.
 * @see https://react.dev/reference/react-dom/render
=======
 * @param {WPElement} element Element to render
 * @param {Element}   target  DOM node into which element should be rendered
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */


/**
<<<<<<< HEAD
 * Hydrates a given element into the target DOM node.
 *
 * @deprecated since WordPress 6.2.0. Use `hydrateRoot` instead.
 * @see https://react.dev/reference/react-dom/hydrate
 */


/**
 * Creates a new React root for the target DOM node.
 *
 * @since 6.2.0 Introduced in WordPress core.
 * @see https://react.dev/reference/react-dom/client/createRoot
 */


/**
 * Creates a new React root for the target DOM node and hydrates it with a pre-generated markup.
 *
 * @since 6.2.0 Introduced in WordPress core.
 * @see https://react.dev/reference/react-dom/client/hydrateRoot
 */


/**
 * Removes any mounted element from the target DOM node.
 *
 * @deprecated since WordPress 6.2.0. Use `root.unmount()` instead.
 * @see https://react.dev/reference/react-dom/unmountComponentAtNode
 */



;// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/utils.js
=======
 * Removes any mounted element from the target DOM node.
 *
 * @param {Element} target DOM node in which element is to be removed
 */



// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/utils.js
/**
 * External dependencies
 */

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Checks if the provided WP element is empty.
 *
 * @param {*} element WP element to check.
 * @return {boolean} True when an element is considered empty.
 */
<<<<<<< HEAD
const isEmptyElement = element => {
  if (typeof element === 'number') {
    return false;
  }

  if (typeof element?.valueOf() === 'string' || Array.isArray(element)) {
=======

var utils_isEmptyElement = function isEmptyElement(element) {
  if (Object(external_lodash_["isNumber"])(element)) {
    return false;
  }

  if (Object(external_lodash_["isString"])(element) || Object(external_lodash_["isArray"])(element)) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    return !element.length;
  }

  return !element;
};

<<<<<<< HEAD
;// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/platform.js
/**
 * Parts of this source were derived and modified from react-native-web,
 * released under the MIT license.
 *
 * Copyright (c) 2016-present, Nicolas Gallagher.
 * Copyright (c) 2015-present, Facebook, Inc.
 *
 */
const Platform = {
  OS: 'web',
  select: spec => 'web' in spec ? spec.web : spec.default,
  isWeb: true
};
/**
 * Component used to detect the current Platform being used.
 * Use Platform.OS === 'web' to detect if running on web enviroment.
 *
 * This is the same concept as the React Native implementation.
 *
 * @see https://facebook.github.io/react-native/docs/platform-specific-code#platform-module
 *
 * Here is an example of how to use the select method:
 * @example
 * ```js
 * import { Platform } from '@wordpress/element';
 *
 * const placeholderLabel = Platform.select( {
 *   native: __( 'Add media' ),
 *   web: __( 'Drag images, upload new ones or select files from your library.' ),
 * } );
 * ```
 */

/* harmony default export */ const platform = (Platform);

;// CONCATENATED MODULE: ./node_modules/is-plain-object/dist/is-plain-object.mjs
/*!
 * is-plain-object <https://github.com/jonschlinkert/is-plain-object>
 *
 * Copyright (c) 2014-2017, Jon Schlinkert.
 * Released under the MIT License.
 */

function isObject(o) {
  return Object.prototype.toString.call(o) === '[object Object]';
}

function isPlainObject(o) {
  var ctor,prot;

  if (isObject(o) === false) return false;

  // If has modified constructor
  ctor = o.constructor;
  if (ctor === undefined) return true;

  // If has modified prototype
  prot = ctor.prototype;
  if (isObject(prot) === false) return false;

  // If constructor does not have an Object-specific method
  if (prot.hasOwnProperty('isPrototypeOf') === false) {
    return false;
  }

  // Most likely a plain Object
  return true;
}



;// CONCATENATED MODULE: ./node_modules/tslib/tslib.es6.mjs
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
/* global Reflect, Promise, SuppressedError, Symbol */

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
  var _ = { label: 0, sent: function() { if (t[0] & 1) throw t[1]; return t[1]; }, trys: [], ops: [] }, f, y, t, g;
  return g = { next: verb(0), "throw": verb(1), "return": verb(2) }, typeof Symbol === "function" && (g[Symbol.iterator] = function() { return this; }), g;
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
  return i = {}, verb("next"), verb("throw"), verb("return"), i[Symbol.asyncIterator] = function () { return this; }, i;
  function verb(n) { if (g[n]) i[n] = function (v) { return new Promise(function (a, b) { q.push([n, v, a, b]) > 1 || resume(n, v); }); }; }
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

function __importStar(mod) {
  if (mod && mod.__esModule) return mod;
  var result = {};
  if (mod != null) for (var k in mod) if (k !== "default" && Object.prototype.hasOwnProperty.call(mod, k)) __createBinding(result, mod, k);
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
    if (typeof value !== "object") throw new TypeError("Object expected.");
    var dispose;
    if (async) {
        if (!Symbol.asyncDispose) throw new TypeError("Symbol.asyncDispose is not defined.");
        dispose = value[Symbol.asyncDispose];
    }
    if (dispose === void 0) {
        if (!Symbol.dispose) throw new TypeError("Symbol.dispose is not defined.");
        dispose = value[Symbol.dispose];
    }
    if (typeof dispose !== "function") throw new TypeError("Object not disposable.");
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
  function next() {
    while (env.stack.length) {
      var rec = env.stack.pop();
      try {
        var result = rec.dispose && rec.dispose.call(rec.value);
        if (rec.async) return Promise.resolve(result).then(next, function(e) { fail(e); return next(); });
      }
      catch (e) {
          fail(e);
      }
    }
    if (env.hasError) throw env.error;
  }
  return next();
}

/* harmony default export */ const tslib_es6 = ({
  __extends,
  __assign,
  __rest,
  __decorate,
  __param,
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
});

;// CONCATENATED MODULE: ./node_modules/lower-case/dist.es2015/index.js
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

;// CONCATENATED MODULE: ./node_modules/no-case/dist.es2015/index.js

// Support camel case ("camelCase" -> "camel Case" and "CAMELCase" -> "CAMEL Case").
var DEFAULT_SPLIT_REGEXP = [/([a-z0-9])([A-Z])/g, /([A-Z])([A-Z][a-z])/g];
// Remove all non-word characters.
var DEFAULT_STRIP_REGEXP = /[^A-Z0-9]+/gi;
/**
 * Normalize the string into something other libraries can manipulate easier.
 */
function noCase(input, options) {
    if (options === void 0) { options = {}; }
    var _a = options.splitRegexp, splitRegexp = _a === void 0 ? DEFAULT_SPLIT_REGEXP : _a, _b = options.stripRegexp, stripRegexp = _b === void 0 ? DEFAULT_STRIP_REGEXP : _b, _c = options.transform, transform = _c === void 0 ? lowerCase : _c, _d = options.delimiter, delimiter = _d === void 0 ? " " : _d;
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

;// CONCATENATED MODULE: ./node_modules/dot-case/dist.es2015/index.js


function dotCase(input, options) {
    if (options === void 0) { options = {}; }
    return noCase(input, __assign({ delimiter: "." }, options));
}

;// CONCATENATED MODULE: ./node_modules/param-case/dist.es2015/index.js


function paramCase(input, options) {
    if (options === void 0) { options = {}; }
    return dotCase(input, __assign({ delimiter: "-" }, options));
}

;// CONCATENATED MODULE: external ["wp","escapeHtml"]
const external_wp_escapeHtml_namespaceObject = window["wp"]["escapeHtml"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/raw-html.js
/**
 * Internal dependencies
 */

/** @typedef {{children: string} & import('react').ComponentPropsWithoutRef<'div'>} RawHTMLProps */

=======
// EXTERNAL MODULE: ./node_modules/@babel/runtime/helpers/esm/typeof.js
var esm_typeof = __webpack_require__("U8pU");

// EXTERNAL MODULE: external {"this":["wp","escapeHtml"]}
var external_this_wp_escapeHtml_ = __webpack_require__("Vx3V");

// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/raw-html.js



/**
 * External dependencies
 */

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Component used as equivalent of Fragment with unescaped HTML, in cases where
 * it is desirable to render dangerous HTML without needing a wrapper element.
 * To preserve additional props, a `div` wrapper _will_ be created if any props
 * aside from `children` are passed.
 *
<<<<<<< HEAD
 * @param {RawHTMLProps} props Children should be a string of HTML or an array
 *                             of strings. Other props will be passed through
 *                             to the div wrapper.
 *
 * @return {JSX.Element} Dangerously-rendering component.
 */

function RawHTML({
  children,
  ...props
}) {
  let rawHtml = ''; // Cast children as an array, and concatenate each element if it is a string.

  external_React_namespaceObject.Children.toArray(children).forEach(child => {
    if (typeof child === 'string' && child.trim() !== '') {
      rawHtml += child;
    }
  }); // The `div` wrapper will be stripped by the `renderElement` serializer in
  // `./serialize.js` unless there are non-children props present.

  return (0,external_React_namespaceObject.createElement)('div', {
    dangerouslySetInnerHTML: {
      __html: rawHtml
    },
    ...props
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/serialize.js
=======
 * @param {string} props.children HTML to render.
 *
 * @return {WPElement} Dangerously-rendering element.
 */

function RawHTML(_ref) {
  var children = _ref.children,
      props = Object(objectWithoutProperties["a" /* default */])(_ref, ["children"]);

  // The DIV wrapper will be stripped by serializer, unless there are
  // non-children props present.
  return Object(external_React_["createElement"])('div', Object(objectSpread["a" /* default */])({
    dangerouslySetInnerHTML: {
      __html: children
    }
  }, props));
}

// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/serialize.js




>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Parts of this source were derived and modified from fast-react-render,
 * released under the MIT license.
 *
 * https://github.com/alt-j/fast-react-render
 *
 * Copyright (c) 2016 Andrey Morozov
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 */

/**
 * External dependencies
 */

<<<<<<< HEAD

=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



<<<<<<< HEAD
/** @typedef {import('./react').WPElement} WPElement */

const {
  Provider,
  Consumer
} = (0,external_React_namespaceObject.createContext)(undefined);
const ForwardRef = (0,external_React_namespaceObject.forwardRef)(() => {
  return null;
});
/**
 * Valid attribute types.
 *
 * @type {Set<string>}
 */

const ATTRIBUTES_TYPES = new Set(['string', 'boolean', 'number']);
/**
 * Element tags which can be self-closing.
 *
 * @type {Set<string>}
 */

const SELF_CLOSING_TAGS = new Set(['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr']);
=======

var _createContext = Object(external_React_["createContext"])(),
    Provider = _createContext.Provider,
    Consumer = _createContext.Consumer;
/**
 * Valid attribute types.
 *
 * @type {Set}
 */


var ATTRIBUTES_TYPES = new Set(['string', 'boolean', 'number']);
/**
 * Element tags which can be self-closing.
 *
 * @type {Set}
 */

var SELF_CLOSING_TAGS = new Set(['area', 'base', 'br', 'col', 'command', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta', 'param', 'source', 'track', 'wbr']);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Boolean attributes are attributes whose presence as being assigned is
 * meaningful, even if only empty.
 *
 * See: https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#boolean-attributes
 * Extracted from: https://html.spec.whatwg.org/multipage/indices.html#attributes-3
 *
 * Object.keys( [ ...document.querySelectorAll( '#attributes-1 > tbody > tr' ) ]
 *     .filter( ( tr ) => tr.lastChild.textContent.indexOf( 'Boolean attribute' ) !== -1 )
 *     .reduce( ( result, tr ) => Object.assign( result, {
 *         [ tr.firstChild.textContent.trim() ]: true
 *     } ), {} ) ).sort();
 *
<<<<<<< HEAD
 * @type {Set<string>}
 */

const BOOLEAN_ATTRIBUTES = new Set(['allowfullscreen', 'allowpaymentrequest', 'allowusermedia', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default', 'defer', 'disabled', 'download', 'formnovalidate', 'hidden', 'ismap', 'itemscope', 'loop', 'multiple', 'muted', 'nomodule', 'novalidate', 'open', 'playsinline', 'readonly', 'required', 'reversed', 'selected', 'typemustmatch']);
=======
 * @type {Set}
 */

var BOOLEAN_ATTRIBUTES = new Set(['allowfullscreen', 'allowpaymentrequest', 'allowusermedia', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default', 'defer', 'disabled', 'download', 'formnovalidate', 'hidden', 'ismap', 'itemscope', 'loop', 'multiple', 'muted', 'nomodule', 'novalidate', 'open', 'playsinline', 'readonly', 'required', 'reversed', 'selected', 'typemustmatch']);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Enumerated attributes are attributes which must be of a specific value form.
 * Like boolean attributes, these are meaningful if specified, even if not of a
 * valid enumerated value.
 *
 * See: https://html.spec.whatwg.org/multipage/common-microsyntaxes.html#enumerated-attribute
 * Extracted from: https://html.spec.whatwg.org/multipage/indices.html#attributes-3
 *
 * Object.keys( [ ...document.querySelectorAll( '#attributes-1 > tbody > tr' ) ]
 *     .filter( ( tr ) => /^("(.+?)";?\s*)+/.test( tr.lastChild.textContent.trim() ) )
 *     .reduce( ( result, tr ) => Object.assign( result, {
 *         [ tr.firstChild.textContent.trim() ]: true
 *     } ), {} ) ).sort();
 *
 * Some notable omissions:
 *
 *  - `alt`: https://blog.whatwg.org/omit-alt
 *
<<<<<<< HEAD
 * @type {Set<string>}
 */

const ENUMERATED_ATTRIBUTES = new Set(['autocapitalize', 'autocomplete', 'charset', 'contenteditable', 'crossorigin', 'decoding', 'dir', 'draggable', 'enctype', 'formenctype', 'formmethod', 'http-equiv', 'inputmode', 'kind', 'method', 'preload', 'scope', 'shape', 'spellcheck', 'translate', 'type', 'wrap']);
=======
 * @type {Set}
 */

var ENUMERATED_ATTRIBUTES = new Set(['autocapitalize', 'autocomplete', 'charset', 'contenteditable', 'crossorigin', 'decoding', 'dir', 'draggable', 'enctype', 'formenctype', 'formmethod', 'http-equiv', 'inputmode', 'kind', 'method', 'preload', 'scope', 'shape', 'spellcheck', 'translate', 'type', 'wrap']);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Set of CSS style properties which support assignment of unitless numbers.
 * Used in rendering of style properties, where `px` unit is assumed unless
 * property is included in this set or value is zero.
 *
 * Generated via:
 *
 * Object.entries( document.createElement( 'div' ).style )
 *     .filter( ( [ key ] ) => (
 *         ! /^(webkit|ms|moz)/.test( key ) &&
 *         ( e.style[ key ] = 10 ) &&
 *         e.style[ key ] === '10'
 *     ) )
 *     .map( ( [ key ] ) => key )
 *     .sort();
 *
<<<<<<< HEAD
 * @type {Set<string>}
 */

const CSS_PROPERTIES_SUPPORTS_UNITLESS = new Set(['animation', 'animationIterationCount', 'baselineShift', 'borderImageOutset', 'borderImageSlice', 'borderImageWidth', 'columnCount', 'cx', 'cy', 'fillOpacity', 'flexGrow', 'flexShrink', 'floodOpacity', 'fontWeight', 'gridColumnEnd', 'gridColumnStart', 'gridRowEnd', 'gridRowStart', 'lineHeight', 'opacity', 'order', 'orphans', 'r', 'rx', 'ry', 'shapeImageThreshold', 'stopOpacity', 'strokeDasharray', 'strokeDashoffset', 'strokeMiterlimit', 'strokeOpacity', 'strokeWidth', 'tabSize', 'widows', 'x', 'y', 'zIndex', 'zoom']);
=======
 * @type {Set}
 */

var CSS_PROPERTIES_SUPPORTS_UNITLESS = new Set(['animation', 'animationIterationCount', 'baselineShift', 'borderImageOutset', 'borderImageSlice', 'borderImageWidth', 'columnCount', 'cx', 'cy', 'fillOpacity', 'flexGrow', 'flexShrink', 'floodOpacity', 'fontWeight', 'gridColumnEnd', 'gridColumnStart', 'gridRowEnd', 'gridRowStart', 'lineHeight', 'opacity', 'order', 'orphans', 'r', 'rx', 'ry', 'shapeImageThreshold', 'stopOpacity', 'strokeDasharray', 'strokeDashoffset', 'strokeMiterlimit', 'strokeOpacity', 'strokeWidth', 'tabSize', 'widows', 'x', 'y', 'zIndex', 'zoom']);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
/**
 * Returns true if the specified string is prefixed by one of an array of
 * possible prefixes.
 *
 * @param {string}   string   String to check.
 * @param {string[]} prefixes Possible prefixes.
 *
 * @return {boolean} Whether string has prefix.
 */

function hasPrefix(string, prefixes) {
<<<<<<< HEAD
  return prefixes.some(prefix => string.indexOf(prefix) === 0);
=======
  return prefixes.some(function (prefix) {
    return string.indexOf(prefix) === 0;
  });
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
}
/**
 * Returns true if the given prop name should be ignored in attributes
 * serialization, or false otherwise.
 *
 * @param {string} attribute Attribute to check.
 *
 * @return {boolean} Whether attribute should be ignored.
 */

function isInternalAttribute(attribute) {
  return 'key' === attribute || 'children' === attribute;
}
/**
 * Returns the normal form of the element's attribute value for HTML.
 *
 * @param {string} attribute Attribute name.
 * @param {*}      value     Non-normalized attribute value.
 *
<<<<<<< HEAD
 * @return {*} Normalized attribute value.
=======
 * @return {string} Normalized attribute value.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 */


function getNormalAttributeValue(attribute, value) {
  switch (attribute) {
    case 'style':
      return renderStyle(value);
  }

  return value;
}
/**
<<<<<<< HEAD
 * This is a map of all SVG attributes that have dashes. Map(lower case prop => dashed lower case attribute).
 * We need this to render e.g strokeWidth as stroke-width.
 *
 * List from: https://developer.mozilla.org/en-US/docs/Web/SVG/Attribute.
 */


const SVG_ATTRIBUTE_WITH_DASHES_LIST = ['accentHeight', 'alignmentBaseline', 'arabicForm', 'baselineShift', 'capHeight', 'clipPath', 'clipRule', 'colorInterpolation', 'colorInterpolationFilters', 'colorProfile', 'colorRendering', 'dominantBaseline', 'enableBackground', 'fillOpacity', 'fillRule', 'floodColor', 'floodOpacity', 'fontFamily', 'fontSize', 'fontSizeAdjust', 'fontStretch', 'fontStyle', 'fontVariant', 'fontWeight', 'glyphName', 'glyphOrientationHorizontal', 'glyphOrientationVertical', 'horizAdvX', 'horizOriginX', 'imageRendering', 'letterSpacing', 'lightingColor', 'markerEnd', 'markerMid', 'markerStart', 'overlinePosition', 'overlineThickness', 'paintOrder', 'panose1', 'pointerEvents', 'renderingIntent', 'shapeRendering', 'stopColor', 'stopOpacity', 'strikethroughPosition', 'strikethroughThickness', 'strokeDasharray', 'strokeDashoffset', 'strokeLinecap', 'strokeLinejoin', 'strokeMiterlimit', 'strokeOpacity', 'strokeWidth', 'textAnchor', 'textDecoration', 'textRendering', 'underlinePosition', 'underlineThickness', 'unicodeBidi', 'unicodeRange', 'unitsPerEm', 'vAlphabetic', 'vHanging', 'vIdeographic', 'vMathematical', 'vectorEffect', 'vertAdvY', 'vertOriginX', 'vertOriginY', 'wordSpacing', 'writingMode', 'xmlnsXlink', 'xHeight'].reduce((map, attribute) => {
  // The keys are lower-cased for more robust lookup.
  map[attribute.toLowerCase()] = attribute;
  return map;
}, {});
/**
 * This is a map of all case-sensitive SVG attributes. Map(lowercase key => proper case attribute).
 * The keys are lower-cased for more robust lookup.
 * Note that this list only contains attributes that contain at least one capital letter.
 * Lowercase attributes don't need mapping, since we lowercase all attributes by default.
 */

const CASE_SENSITIVE_SVG_ATTRIBUTES = ['allowReorder', 'attributeName', 'attributeType', 'autoReverse', 'baseFrequency', 'baseProfile', 'calcMode', 'clipPathUnits', 'contentScriptType', 'contentStyleType', 'diffuseConstant', 'edgeMode', 'externalResourcesRequired', 'filterRes', 'filterUnits', 'glyphRef', 'gradientTransform', 'gradientUnits', 'kernelMatrix', 'kernelUnitLength', 'keyPoints', 'keySplines', 'keyTimes', 'lengthAdjust', 'limitingConeAngle', 'markerHeight', 'markerUnits', 'markerWidth', 'maskContentUnits', 'maskUnits', 'numOctaves', 'pathLength', 'patternContentUnits', 'patternTransform', 'patternUnits', 'pointsAtX', 'pointsAtY', 'pointsAtZ', 'preserveAlpha', 'preserveAspectRatio', 'primitiveUnits', 'refX', 'refY', 'repeatCount', 'repeatDur', 'requiredExtensions', 'requiredFeatures', 'specularConstant', 'specularExponent', 'spreadMethod', 'startOffset', 'stdDeviation', 'stitchTiles', 'suppressContentEditableWarning', 'suppressHydrationWarning', 'surfaceScale', 'systemLanguage', 'tableValues', 'targetX', 'targetY', 'textLength', 'viewBox', 'viewTarget', 'xChannelSelector', 'yChannelSelector'].reduce((map, attribute) => {
  // The keys are lower-cased for more robust lookup.
  map[attribute.toLowerCase()] = attribute;
  return map;
}, {});
/**
 * This is a map of all SVG attributes that have colons.
 * Keys are lower-cased and stripped of their colons for more robust lookup.
 */

const SVG_ATTRIBUTES_WITH_COLONS = ['xlink:actuate', 'xlink:arcrole', 'xlink:href', 'xlink:role', 'xlink:show', 'xlink:title', 'xlink:type', 'xml:base', 'xml:lang', 'xml:space', 'xmlns:xlink'].reduce((map, attribute) => {
  map[attribute.replace(':', '').toLowerCase()] = attribute;
  return map;
}, {});
/**
=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 * Returns the normal form of the element's attribute name for HTML.
 *
 * @param {string} attribute Non-normalized attribute name.
 *
 * @return {string} Normalized attribute name.
 */

<<<<<<< HEAD
=======

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
function getNormalAttributeName(attribute) {
  switch (attribute) {
    case 'htmlFor':
      return 'for';

    case 'className':
      return 'class';
  }

<<<<<<< HEAD
  const attributeLowerCase = attribute.toLowerCase();

  if (CASE_SENSITIVE_SVG_ATTRIBUTES[attributeLowerCase]) {
    return CASE_SENSITIVE_SVG_ATTRIBUTES[attributeLowerCase];
  } else if (SVG_ATTRIBUTE_WITH_DASHES_LIST[attributeLowerCase]) {
    return paramCase(SVG_ATTRIBUTE_WITH_DASHES_LIST[attributeLowerCase]);
  } else if (SVG_ATTRIBUTES_WITH_COLONS[attributeLowerCase]) {
    return SVG_ATTRIBUTES_WITH_COLONS[attributeLowerCase];
  }

  return attributeLowerCase;
=======
  return attribute.toLowerCase();
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
}
/**
 * Returns the normal form of the style property name for HTML.
 *
 * - Converts property names to kebab-case, e.g. 'backgroundColor' → 'background-color'
 * - Leaves custom attributes alone, e.g. '--myBackgroundColor' → '--myBackgroundColor'
 * - Converts vendor-prefixed property names to -kebab-case, e.g. 'MozTransform' → '-moz-transform'
 *
 * @param {string} property Property name.
 *
 * @return {string} Normalized property name.
 */


function getNormalStylePropertyName(property) {
<<<<<<< HEAD
  if (property.startsWith('--')) {
=======
  if (Object(external_lodash_["startsWith"])(property, '--')) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    return property;
  }

  if (hasPrefix(property, ['ms', 'O', 'Moz', 'Webkit'])) {
<<<<<<< HEAD
    return '-' + paramCase(property);
  }

  return paramCase(property);
=======
    return '-' + Object(external_lodash_["kebabCase"])(property);
  }

  return Object(external_lodash_["kebabCase"])(property);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
}
/**
 * Returns the normal form of the style property value for HTML. Appends a
 * default pixel unit if numeric, not a unitless property, and not zero.
 *
 * @param {string} property Property name.
 * @param {*}      value    Non-normalized property value.
 *
 * @return {*} Normalized property value.
 */


function getNormalStylePropertyValue(property, value) {
  if (typeof value === 'number' && 0 !== value && !CSS_PROPERTIES_SUPPORTS_UNITLESS.has(property)) {
    return value + 'px';
  }

  return value;
}
/**
 * Serializes a React element to string.
 *
<<<<<<< HEAD
 * @param {import('react').ReactNode} element         Element to serialize.
 * @param {Object}                    [context]       Context object.
 * @param {Object}                    [legacyContext] Legacy context object.
=======
 * @param {WPElement} element       Element to serialize.
 * @param {?Object}   context       Context object.
 * @param {?Object}   legacyContext Legacy context object.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {string} Serialized element.
 */


<<<<<<< HEAD
function renderElement(element, context, legacyContext = {}) {
=======
function renderElement(element, context) {
  var legacyContext = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};

>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  if (null === element || undefined === element || false === element) {
    return '';
  }

  if (Array.isArray(element)) {
    return renderChildren(element, context, legacyContext);
  }

<<<<<<< HEAD
  switch (typeof element) {
    case 'string':
      return (0,external_wp_escapeHtml_namespaceObject.escapeHTML)(element);
=======
  switch (Object(esm_typeof["a" /* default */])(element)) {
    case 'string':
      return Object(external_this_wp_escapeHtml_["escapeHTML"])(element);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

    case 'number':
      return element.toString();
  }

<<<<<<< HEAD
  const {
    type,
    props
  } =
  /** @type {{type?: any, props?: any}} */
  element;

  switch (type) {
    case external_React_namespaceObject.StrictMode:
    case external_React_namespaceObject.Fragment:
      return renderChildren(props.children, context, legacyContext);

    case RawHTML:
      const {
        children,
        ...wrapperProps
      } = props;
      return renderNativeComponent(!Object.keys(wrapperProps).length ? null : 'div', { ...wrapperProps,
        dangerouslySetInnerHTML: {
          __html: children
        }
      }, context, legacyContext);
  }

  switch (typeof type) {
=======
  var type = element.type,
      props = element.props;

  switch (type) {
    case external_React_["StrictMode"]:
    case external_React_["Fragment"]:
      return renderChildren(props.children, context, legacyContext);

    case RawHTML:
      var children = props.children,
          wrapperProps = Object(objectWithoutProperties["a" /* default */])(props, ["children"]);

      return renderNativeComponent(Object(external_lodash_["isEmpty"])(wrapperProps) ? null : 'div', Object(objectSpread["a" /* default */])({}, wrapperProps, {
        dangerouslySetInnerHTML: {
          __html: children
        }
      }), context, legacyContext);
  }

  switch (Object(esm_typeof["a" /* default */])(type)) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    case 'string':
      return renderNativeComponent(type, props, context, legacyContext);

    case 'function':
      if (type.prototype && typeof type.prototype.render === 'function') {
        return renderComponent(type, props, context, legacyContext);
      }

      return renderElement(type(props, legacyContext), context, legacyContext);
  }

  switch (type && type.$$typeof) {
    case Provider.$$typeof:
      return renderChildren(props.children, props.value, legacyContext);

    case Consumer.$$typeof:
      return renderElement(props.children(context || type._currentValue), context, legacyContext);
<<<<<<< HEAD

    case ForwardRef.$$typeof:
      return renderElement(type.render(props), context, legacyContext);
=======
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  }

  return '';
}
/**
 * Serializes a native component type to string.
 *
<<<<<<< HEAD
 * @param {?string} type            Native component type to serialize, or null if
 *                                  rendering as fragment of children content.
 * @param {Object}  props           Props object.
 * @param {Object}  [context]       Context object.
 * @param {Object}  [legacyContext] Legacy context object.
=======
 * @param {?string} type          Native component type to serialize, or null if
 *                                rendering as fragment of children content.
 * @param {Object}  props         Props object.
 * @param {?Object} context       Context object.
 * @param {?Object} legacyContext Legacy context object.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {string} Serialized element.
 */

<<<<<<< HEAD
function renderNativeComponent(type, props, context, legacyContext = {}) {
  let content = '';
=======
function renderNativeComponent(type, props, context) {
  var legacyContext = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
  var content = '';
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (type === 'textarea' && props.hasOwnProperty('value')) {
    // Textarea children can be assigned as value prop. If it is, render in
    // place of children. Ensure to omit so it is not assigned as attribute
    // as well.
    content = renderChildren(props.value, context, legacyContext);
<<<<<<< HEAD
    const {
      value,
      ...restProps
    } = props;
    props = restProps;
=======
    props = Object(external_lodash_["omit"])(props, 'value');
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  } else if (props.dangerouslySetInnerHTML && typeof props.dangerouslySetInnerHTML.__html === 'string') {
    // Dangerous content is left unescaped.
    content = props.dangerouslySetInnerHTML.__html;
  } else if (typeof props.children !== 'undefined') {
    content = renderChildren(props.children, context, legacyContext);
  }

  if (!type) {
    return content;
  }

<<<<<<< HEAD
  const attributes = renderAttributes(props);
=======
  var attributes = renderAttributes(props);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

  if (SELF_CLOSING_TAGS.has(type)) {
    return '<' + type + attributes + '/>';
  }

  return '<' + type + attributes + '>' + content + '</' + type + '>';
}
<<<<<<< HEAD
/** @typedef {import('./react').WPComponent} WPComponent */

/**
 * Serializes a non-native component type to string.
 *
 * @param {WPComponent} Component       Component type to serialize.
 * @param {Object}      props           Props object.
 * @param {Object}      [context]       Context object.
 * @param {Object}      [legacyContext] Legacy context object.
=======
/**
 * Serializes a non-native component type to string.
 *
 * @param {Function} Component     Component type to serialize.
 * @param {Object}   props         Props object.
 * @param {?Object}  context       Context object.
 * @param {?Object}  legacyContext Legacy context object.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {string} Serialized element
 */

<<<<<<< HEAD
function renderComponent(Component, props, context, legacyContext = {}) {
  const instance = new
  /** @type {import('react').ComponentClass} */
  Component(props, legacyContext);

  if (typeof // Ignore reason: Current prettier reformats parens and mangles type assertion
  // prettier-ignore

  /** @type {{getChildContext?: () => unknown}} */
  instance.getChildContext === 'function') {
    Object.assign(legacyContext,
    /** @type {{getChildContext?: () => unknown}} */
    instance.getChildContext());
  }

  const html = renderElement(instance.render(), context, legacyContext);
=======
function renderComponent(Component, props, context) {
  var legacyContext = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
  var instance = new Component(props, legacyContext);

  if (typeof instance.getChildContext === 'function') {
    Object.assign(legacyContext, instance.getChildContext());
  }

  var html = renderElement(instance.render(), context, legacyContext);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
  return html;
}
/**
 * Serializes an array of children to string.
 *
<<<<<<< HEAD
 * @param {import('react').ReactNodeArray} children        Children to serialize.
 * @param {Object}                         [context]       Context object.
 * @param {Object}                         [legacyContext] Legacy context object.
=======
 * @param {Array}   children      Children to serialize.
 * @param {?Object} context       Context object.
 * @param {?Object} legacyContext Legacy context object.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
 *
 * @return {string} Serialized children.
 */

<<<<<<< HEAD
function renderChildren(children, context, legacyContext = {}) {
  let result = '';
  children = Array.isArray(children) ? children : [children];

  for (let i = 0; i < children.length; i++) {
    const child = children[i];
=======
function renderChildren(children, context) {
  var legacyContext = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  var result = '';
  children = Object(external_lodash_["castArray"])(children);

  for (var i = 0; i < children.length; i++) {
    var child = children[i];
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    result += renderElement(child, context, legacyContext);
  }

  return result;
}
/**
 * Renders a props object as a string of HTML attributes.
 *
 * @param {Object} props Props object.
 *
 * @return {string} Attributes string.
 */


function renderAttributes(props) {
<<<<<<< HEAD
  let result = '';

  for (const key in props) {
    const attribute = getNormalAttributeName(key);

    if (!(0,external_wp_escapeHtml_namespaceObject.isValidAttributeName)(attribute)) {
      continue;
    }

    let value = getNormalAttributeValue(key, props[key]); // If value is not of serializeable type, skip.

    if (!ATTRIBUTES_TYPES.has(typeof value)) {
=======
  var result = '';

  for (var key in props) {
    var attribute = getNormalAttributeName(key);

    if (!Object(external_this_wp_escapeHtml_["isValidAttributeName"])(attribute)) {
      continue;
    }

    var value = getNormalAttributeValue(key, props[key]); // If value is not of serializeable type, skip.

    if (!ATTRIBUTES_TYPES.has(Object(esm_typeof["a" /* default */])(value))) {
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
      continue;
    } // Don't render internal attribute names.


    if (isInternalAttribute(key)) {
      continue;
    }

<<<<<<< HEAD
    const isBooleanAttribute = BOOLEAN_ATTRIBUTES.has(attribute); // Boolean attribute should be omitted outright if its value is false.
=======
    var isBooleanAttribute = BOOLEAN_ATTRIBUTES.has(attribute); // Boolean attribute should be omitted outright if its value is false.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

    if (isBooleanAttribute && value === false) {
      continue;
    }

<<<<<<< HEAD
    const isMeaningfulAttribute = isBooleanAttribute || hasPrefix(key, ['data-', 'aria-']) || ENUMERATED_ATTRIBUTES.has(attribute); // Only write boolean value as attribute if meaningful.
=======
    var isMeaningfulAttribute = isBooleanAttribute || hasPrefix(key, ['data-', 'aria-']) || ENUMERATED_ATTRIBUTES.has(attribute); // Only write boolean value as attribute if meaningful.
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

    if (typeof value === 'boolean' && !isMeaningfulAttribute) {
      continue;
    }

    result += ' ' + attribute; // Boolean attributes should write attribute name, but without value.
    // Mere presence of attribute name is effective truthiness.

    if (isBooleanAttribute) {
      continue;
    }

    if (typeof value === 'string') {
<<<<<<< HEAD
      value = (0,external_wp_escapeHtml_namespaceObject.escapeAttribute)(value);
=======
      value = Object(external_this_wp_escapeHtml_["escapeAttribute"])(value);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    }

    result += '="' + value + '"';
  }

  return result;
}
/**
 * Renders a style object as a string attribute value.
 *
 * @param {Object} style Style object.
 *
 * @return {string} Style attribute value.
 */

function renderStyle(style) {
  // Only generate from object, e.g. tolerate string value.
<<<<<<< HEAD
  if (!isPlainObject(style)) {
    return style;
  }

  let result;

  for (const property in style) {
    const value = style[property];
=======
  if (!Object(external_lodash_["isPlainObject"])(style)) {
    return style;
  }

  var result;

  for (var property in style) {
    var value = style[property];
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9

    if (null === value || undefined === value) {
      continue;
    }

    if (result) {
      result += ';';
    } else {
      result = '';
    }

<<<<<<< HEAD
    const normalName = getNormalStylePropertyName(property);
    const normalValue = getNormalStylePropertyValue(property, value);
=======
    var normalName = getNormalStylePropertyName(property);
    var normalValue = getNormalStylePropertyValue(property, value);
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
    result += normalName + ':' + normalValue;
  }

  return result;
}
<<<<<<< HEAD
/* harmony default export */ const serialize = (renderElement);

;// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/index.js








})();

(window.wp = window.wp || {}).element = __webpack_exports__;
/******/ })()
;
=======
/* harmony default export */ var serialize = (renderElement);

// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/index.js







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

/***/ })

/******/ });
>>>>>>> cc0aa4e659209bc2ca7c9df37dc56696d84621c9
