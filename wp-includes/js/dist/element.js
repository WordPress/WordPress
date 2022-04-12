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
  "Children": function() { return /* reexport */ external_React_namespaceObject.Children; },
  "Component": function() { return /* reexport */ external_React_namespaceObject.Component; },
  "Fragment": function() { return /* reexport */ external_React_namespaceObject.Fragment; },
  "Platform": function() { return /* reexport */ platform; },
  "RawHTML": function() { return /* reexport */ RawHTML; },
  "StrictMode": function() { return /* reexport */ external_React_namespaceObject.StrictMode; },
  "Suspense": function() { return /* reexport */ external_React_namespaceObject.Suspense; },
  "cloneElement": function() { return /* reexport */ external_React_namespaceObject.cloneElement; },
  "concatChildren": function() { return /* reexport */ concatChildren; },
  "createContext": function() { return /* reexport */ external_React_namespaceObject.createContext; },
  "createElement": function() { return /* reexport */ external_React_namespaceObject.createElement; },
  "createInterpolateElement": function() { return /* reexport */ create_interpolate_element; },
  "createPortal": function() { return /* reexport */ external_ReactDOM_namespaceObject.createPortal; },
  "createRef": function() { return /* reexport */ external_React_namespaceObject.createRef; },
  "findDOMNode": function() { return /* reexport */ external_ReactDOM_namespaceObject.findDOMNode; },
  "forwardRef": function() { return /* reexport */ external_React_namespaceObject.forwardRef; },
  "isEmptyElement": function() { return /* reexport */ isEmptyElement; },
  "isValidElement": function() { return /* reexport */ external_React_namespaceObject.isValidElement; },
  "lazy": function() { return /* reexport */ external_React_namespaceObject.lazy; },
  "memo": function() { return /* reexport */ external_React_namespaceObject.memo; },
  "render": function() { return /* reexport */ external_ReactDOM_namespaceObject.render; },
  "renderToString": function() { return /* reexport */ serialize; },
  "switchChildrenNodeName": function() { return /* reexport */ switchChildrenNodeName; },
  "unmountComponentAtNode": function() { return /* reexport */ external_ReactDOM_namespaceObject.unmountComponentAtNode; },
  "useCallback": function() { return /* reexport */ external_React_namespaceObject.useCallback; },
  "useContext": function() { return /* reexport */ external_React_namespaceObject.useContext; },
  "useDebugValue": function() { return /* reexport */ external_React_namespaceObject.useDebugValue; },
  "useEffect": function() { return /* reexport */ external_React_namespaceObject.useEffect; },
  "useImperativeHandle": function() { return /* reexport */ external_React_namespaceObject.useImperativeHandle; },
  "useLayoutEffect": function() { return /* reexport */ external_React_namespaceObject.useLayoutEffect; },
  "useMemo": function() { return /* reexport */ external_React_namespaceObject.useMemo; },
  "useReducer": function() { return /* reexport */ external_React_namespaceObject.useReducer; },
  "useRef": function() { return /* reexport */ external_React_namespaceObject.useRef; },
  "useState": function() { return /* reexport */ external_React_namespaceObject.useState; }
});

;// CONCATENATED MODULE: external "React"
var external_React_namespaceObject = window["React"];
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
 * isClosing: The closing slash, it it exists.
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
 * @param {string} interpolatedString The interpolation string to be parsed.
 * @param {Object} conversionMap      The map used to convert the string to
 *                                    a react element.
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

/* harmony default export */ var create_interpolate_element = (createInterpolateElement);

;// CONCATENATED MODULE: external "lodash"
var external_lodash_namespaceObject = window["lodash"];
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
 * Object that provides utilities for dealing with React children.
 */


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
 *                                      set to apply to DOM node or values to
 *                                      pass through to element creator
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
 * Checks if an object is a valid WPElement.
 *
 * @param {Object} objectToCheck The object to be checked.
 *
 * @return {boolean} true if objectToTest is a valid WPElement and false otherwise.
 */


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
 * @see https://reactjs.org/docs/hooks-reference.html#useeffect
 */


/**
 * @see https://reactjs.org/docs/hooks-reference.html#useimperativehandle
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
 * @see https://reactjs.org/docs/react-api.html#reactlazy
 */


/**
 * @see https://reactjs.org/docs/react-api.html#reactsuspense
 */


/**
 * Concatenate two or more React children objects.
 *
 * @param {...?Object} childrenArguments Array of children arguments (array of arrays/strings/objects) to concatenate.
 *
 * @return {Array} The concatenated value.
 */

function concatChildren() {
  for (var _len = arguments.length, childrenArguments = new Array(_len), _key = 0; _key < _len; _key++) {
    childrenArguments[_key] = arguments[_key];
  }

  return childrenArguments.reduce((accumulator, children, i) => {
    external_React_namespaceObject.Children.forEach(children, (child, j) => {
      if (child && 'string' !== typeof child) {
        child = (0,external_React_namespaceObject.cloneElement)(child, {
          key: [i, j].join()
        });
      }

      accumulator.push(child);
    });
    return accumulator;
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
  return children && external_React_namespaceObject.Children.map(children, (elt, index) => {
    if ((0,external_lodash_namespaceObject.isString)(elt)) {
      return (0,external_React_namespaceObject.createElement)(nodeName, {
        key: index
      }, elt);
    }

    const {
      children: childrenProp,
      ...props
    } = elt.props;
    return (0,external_React_namespaceObject.createElement)(nodeName, {
      key: index,
      ...props
    }, childrenProp);
  });
}

;// CONCATENATED MODULE: external "ReactDOM"
var external_ReactDOM_namespaceObject = window["ReactDOM"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/react-platform.js
/**
 * External dependencies
 */

/**
 * Creates a portal into which a component can be rendered.
 *
 * @see https://github.com/facebook/react/issues/10309#issuecomment-318433235
 *
 * @param {import('./react').WPElement} child     Any renderable child, such as an element,
 *                                                string, or fragment.
 * @param {HTMLElement}                 container DOM node into which element should be rendered.
 */


/**
 * Finds the dom node of a React component.
 *
 * @param {import('./react').WPComponent} component Component's instance.
 */


/**
 * Renders a given element into the target DOM node.
 *
 * @param {import('./react').WPElement} element Element to render.
 * @param {HTMLElement}                 target  DOM node into which element should be rendered.
 */


/**
 * Removes any mounted element from the target DOM node.
 *
 * @param {Element} target DOM node in which element is to be removed
 */



;// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/utils.js
/**
 * External dependencies
 */

/**
 * Checks if the provided WP element is empty.
 *
 * @param {*} element WP element to check.
 * @return {boolean} True when an element is considered empty.
 */

const isEmptyElement = element => {
  if ((0,external_lodash_namespaceObject.isNumber)(element)) {
    return false;
  }

  if ((0,external_lodash_namespaceObject.isString)(element) || (0,external_lodash_namespaceObject.isArray)(element)) {
    return !element.length;
  }

  return !element;
};

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

/* harmony default export */ var platform = (Platform);

;// CONCATENATED MODULE: external ["wp","escapeHtml"]
var external_wp_escapeHtml_namespaceObject = window["wp"]["escapeHtml"];
;// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/raw-html.js
/**
 * Internal dependencies
 */
 // Disable reason: JSDoc linter doesn't seem to parse the union (`&`) correctly.

/** @typedef {{children: string} & import('react').ComponentPropsWithoutRef<'div'>} RawHTMLProps */

/**
 * Component used as equivalent of Fragment with unescaped HTML, in cases where
 * it is desirable to render dangerous HTML without needing a wrapper element.
 * To preserve additional props, a `div` wrapper _will_ be created if any props
 * aside from `children` are passed.
 *
 * @param {RawHTMLProps} props Children should be a string of HTML or an array
 *                             of strings. Other props will be passed through
 *                             to the div wrapper.
 *
 * @return {JSX.Element} Dangerously-rendering component.
 */

function RawHTML(_ref) {
  let {
    children,
    ...props
  } = _ref;
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

/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */



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
 * @type {Set<string>}
 */

const BOOLEAN_ATTRIBUTES = new Set(['allowfullscreen', 'allowpaymentrequest', 'allowusermedia', 'async', 'autofocus', 'autoplay', 'checked', 'controls', 'default', 'defer', 'disabled', 'download', 'formnovalidate', 'hidden', 'ismap', 'itemscope', 'loop', 'multiple', 'muted', 'nomodule', 'novalidate', 'open', 'playsinline', 'readonly', 'required', 'reversed', 'selected', 'typemustmatch']);
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
 * @type {Set<string>}
 */

const ENUMERATED_ATTRIBUTES = new Set(['autocapitalize', 'autocomplete', 'charset', 'contenteditable', 'crossorigin', 'decoding', 'dir', 'draggable', 'enctype', 'formenctype', 'formmethod', 'http-equiv', 'inputmode', 'kind', 'method', 'preload', 'scope', 'shape', 'spellcheck', 'translate', 'type', 'wrap']);
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
 * @type {Set<string>}
 */

const CSS_PROPERTIES_SUPPORTS_UNITLESS = new Set(['animation', 'animationIterationCount', 'baselineShift', 'borderImageOutset', 'borderImageSlice', 'borderImageWidth', 'columnCount', 'cx', 'cy', 'fillOpacity', 'flexGrow', 'flexShrink', 'floodOpacity', 'fontWeight', 'gridColumnEnd', 'gridColumnStart', 'gridRowEnd', 'gridRowStart', 'lineHeight', 'opacity', 'order', 'orphans', 'r', 'rx', 'ry', 'shapeImageThreshold', 'stopOpacity', 'strokeDasharray', 'strokeDashoffset', 'strokeMiterlimit', 'strokeOpacity', 'strokeWidth', 'tabSize', 'widows', 'x', 'y', 'zIndex', 'zoom']);
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
  return prefixes.some(prefix => string.indexOf(prefix) === 0);
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
 * @return {*} Normalized attribute value.
 */


function getNormalAttributeValue(attribute, value) {
  switch (attribute) {
    case 'style':
      return renderStyle(value);
  }

  return value;
}
/**
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
 * Returns the normal form of the element's attribute name for HTML.
 *
 * @param {string} attribute Non-normalized attribute name.
 *
 * @return {string} Normalized attribute name.
 */

function getNormalAttributeName(attribute) {
  switch (attribute) {
    case 'htmlFor':
      return 'for';

    case 'className':
      return 'class';
  }

  const attributeLowerCase = attribute.toLowerCase();

  if (CASE_SENSITIVE_SVG_ATTRIBUTES[attributeLowerCase]) {
    return CASE_SENSITIVE_SVG_ATTRIBUTES[attributeLowerCase];
  } else if (SVG_ATTRIBUTE_WITH_DASHES_LIST[attributeLowerCase]) {
    return (0,external_lodash_namespaceObject.kebabCase)(SVG_ATTRIBUTE_WITH_DASHES_LIST[attributeLowerCase]);
  } else if (SVG_ATTRIBUTES_WITH_COLONS[attributeLowerCase]) {
    return SVG_ATTRIBUTES_WITH_COLONS[attributeLowerCase];
  }

  return attributeLowerCase;
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
  if ((0,external_lodash_namespaceObject.startsWith)(property, '--')) {
    return property;
  }

  if (hasPrefix(property, ['ms', 'O', 'Moz', 'Webkit'])) {
    return '-' + (0,external_lodash_namespaceObject.kebabCase)(property);
  }

  return (0,external_lodash_namespaceObject.kebabCase)(property);
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
 * @param {import('react').ReactNode} element         Element to serialize.
 * @param {Object}                    [context]       Context object.
 * @param {Object}                    [legacyContext] Legacy context object.
 *
 * @return {string} Serialized element.
 */


function renderElement(element, context) {
  let legacyContext = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};

  if (null === element || undefined === element || false === element) {
    return '';
  }

  if (Array.isArray(element)) {
    return renderChildren(element, context, legacyContext);
  }

  switch (typeof element) {
    case 'string':
      return (0,external_wp_escapeHtml_namespaceObject.escapeHTML)(element);

    case 'number':
      return element.toString();
  }

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
      return renderNativeComponent((0,external_lodash_namespaceObject.isEmpty)(wrapperProps) ? null : 'div', { ...wrapperProps,
        dangerouslySetInnerHTML: {
          __html: children
        }
      }, context, legacyContext);
  }

  switch (typeof type) {
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

    case ForwardRef.$$typeof:
      return renderElement(type.render(props), context, legacyContext);
  }

  return '';
}
/**
 * Serializes a native component type to string.
 *
 * @param {?string} type            Native component type to serialize, or null if
 *                                  rendering as fragment of children content.
 * @param {Object}  props           Props object.
 * @param {Object}  [context]       Context object.
 * @param {Object}  [legacyContext] Legacy context object.
 *
 * @return {string} Serialized element.
 */

function renderNativeComponent(type, props, context) {
  let legacyContext = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
  let content = '';

  if (type === 'textarea' && props.hasOwnProperty('value')) {
    // Textarea children can be assigned as value prop. If it is, render in
    // place of children. Ensure to omit so it is not assigned as attribute
    // as well.
    content = renderChildren(props.value, context, legacyContext);
    props = (0,external_lodash_namespaceObject.omit)(props, 'value');
  } else if (props.dangerouslySetInnerHTML && typeof props.dangerouslySetInnerHTML.__html === 'string') {
    // Dangerous content is left unescaped.
    content = props.dangerouslySetInnerHTML.__html;
  } else if (typeof props.children !== 'undefined') {
    content = renderChildren(props.children, context, legacyContext);
  }

  if (!type) {
    return content;
  }

  const attributes = renderAttributes(props);

  if (SELF_CLOSING_TAGS.has(type)) {
    return '<' + type + attributes + '/>';
  }

  return '<' + type + attributes + '>' + content + '</' + type + '>';
}
/** @typedef {import('./react').WPComponent} WPComponent */

/**
 * Serializes a non-native component type to string.
 *
 * @param {WPComponent} Component       Component type to serialize.
 * @param {Object}      props           Props object.
 * @param {Object}      [context]       Context object.
 * @param {Object}      [legacyContext] Legacy context object.
 *
 * @return {string} Serialized element
 */

function renderComponent(Component, props, context) {
  let legacyContext = arguments.length > 3 && arguments[3] !== undefined ? arguments[3] : {};
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
  return html;
}
/**
 * Serializes an array of children to string.
 *
 * @param {import('react').ReactNodeArray} children        Children to serialize.
 * @param {Object}                         [context]       Context object.
 * @param {Object}                         [legacyContext] Legacy context object.
 *
 * @return {string} Serialized children.
 */

function renderChildren(children, context) {
  let legacyContext = arguments.length > 2 && arguments[2] !== undefined ? arguments[2] : {};
  let result = '';
  children = (0,external_lodash_namespaceObject.castArray)(children);

  for (let i = 0; i < children.length; i++) {
    const child = children[i];
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
  let result = '';

  for (const key in props) {
    const attribute = getNormalAttributeName(key);

    if (!(0,external_wp_escapeHtml_namespaceObject.isValidAttributeName)(attribute)) {
      continue;
    }

    let value = getNormalAttributeValue(key, props[key]); // If value is not of serializeable type, skip.

    if (!ATTRIBUTES_TYPES.has(typeof value)) {
      continue;
    } // Don't render internal attribute names.


    if (isInternalAttribute(key)) {
      continue;
    }

    const isBooleanAttribute = BOOLEAN_ATTRIBUTES.has(attribute); // Boolean attribute should be omitted outright if its value is false.

    if (isBooleanAttribute && value === false) {
      continue;
    }

    const isMeaningfulAttribute = isBooleanAttribute || hasPrefix(key, ['data-', 'aria-']) || ENUMERATED_ATTRIBUTES.has(attribute); // Only write boolean value as attribute if meaningful.

    if (typeof value === 'boolean' && !isMeaningfulAttribute) {
      continue;
    }

    result += ' ' + attribute; // Boolean attributes should write attribute name, but without value.
    // Mere presence of attribute name is effective truthiness.

    if (isBooleanAttribute) {
      continue;
    }

    if (typeof value === 'string') {
      value = (0,external_wp_escapeHtml_namespaceObject.escapeAttribute)(value);
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
  if (!(0,external_lodash_namespaceObject.isPlainObject)(style)) {
    return style;
  }

  let result;

  for (const property in style) {
    const value = style[property];

    if (null === value || undefined === value) {
      continue;
    }

    if (result) {
      result += ';';
    } else {
      result = '';
    }

    const normalName = getNormalStylePropertyName(property);
    const normalValue = getNormalStylePropertyValue(property, value);
    result += normalName + ':' + normalValue;
  }

  return result;
}
/* harmony default export */ var serialize = (renderElement);

;// CONCATENATED MODULE: ./node_modules/@wordpress/element/build-module/index.js








(window.wp = window.wp || {}).element = __webpack_exports__;
/******/ })()
;