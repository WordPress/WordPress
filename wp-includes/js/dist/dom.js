/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
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
var __webpack_exports__ = {};
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  __unstableStripHTML: () => (/* reexport */ stripHTML),
  computeCaretRect: () => (/* reexport */ computeCaretRect),
  documentHasSelection: () => (/* reexport */ documentHasSelection),
  documentHasTextSelection: () => (/* reexport */ documentHasTextSelection),
  documentHasUncollapsedSelection: () => (/* reexport */ documentHasUncollapsedSelection),
  focus: () => (/* binding */ build_module_focus),
  getFilesFromDataTransfer: () => (/* reexport */ getFilesFromDataTransfer),
  getOffsetParent: () => (/* reexport */ getOffsetParent),
  getPhrasingContentSchema: () => (/* reexport */ getPhrasingContentSchema),
  getRectangleFromRange: () => (/* reexport */ getRectangleFromRange),
  getScrollContainer: () => (/* reexport */ getScrollContainer),
  insertAfter: () => (/* reexport */ insertAfter),
  isEmpty: () => (/* reexport */ isEmpty),
  isEntirelySelected: () => (/* reexport */ isEntirelySelected),
  isFormElement: () => (/* reexport */ isFormElement),
  isHorizontalEdge: () => (/* reexport */ isHorizontalEdge),
  isNumberInput: () => (/* reexport */ isNumberInput),
  isPhrasingContent: () => (/* reexport */ isPhrasingContent),
  isRTL: () => (/* reexport */ isRTL),
  isTextContent: () => (/* reexport */ isTextContent),
  isTextField: () => (/* reexport */ isTextField),
  isVerticalEdge: () => (/* reexport */ isVerticalEdge),
  placeCaretAtHorizontalEdge: () => (/* reexport */ placeCaretAtHorizontalEdge),
  placeCaretAtVerticalEdge: () => (/* reexport */ placeCaretAtVerticalEdge),
  remove: () => (/* reexport */ remove),
  removeInvalidHTML: () => (/* reexport */ removeInvalidHTML),
  replace: () => (/* reexport */ replace),
  replaceTag: () => (/* reexport */ replaceTag),
  safeHTML: () => (/* reexport */ safeHTML),
  unwrap: () => (/* reexport */ unwrap),
  wrap: () => (/* reexport */ wrap)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/dom/build-module/focusable.js
var focusable_namespaceObject = {};
__webpack_require__.r(focusable_namespaceObject);
__webpack_require__.d(focusable_namespaceObject, {
  find: () => (find)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/dom/build-module/tabbable.js
var tabbable_namespaceObject = {};
__webpack_require__.r(tabbable_namespaceObject);
__webpack_require__.d(tabbable_namespaceObject, {
  find: () => (tabbable_find),
  findNext: () => (findNext),
  findPrevious: () => (findPrevious),
  isTabbableIndex: () => (isTabbableIndex)
});

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/focusable.js
/**
 * References:
 *
 * Focusable:
 *  - https://www.w3.org/TR/html5/editing.html#focus-management
 *
 * Sequential focus navigation:
 *  - https://www.w3.org/TR/html5/editing.html#sequential-focus-navigation-and-the-tabindex-attribute
 *
 * Disabled elements:
 *  - https://www.w3.org/TR/html5/disabled-elements.html#disabled-elements
 *
 * getClientRects algorithm (requiring layout box):
 *  - https://www.w3.org/TR/cssom-view-1/#extension-to-the-element-interface
 *
 * AREA elements associated with an IMG:
 *  - https://w3c.github.io/html/editing.html#data-model
 */

/**
 * Returns a CSS selector used to query for focusable elements.
 *
 * @param {boolean} sequential If set, only query elements that are sequentially
 *                             focusable. Non-interactive elements with a
 *                             negative `tabindex` are focusable but not
 *                             sequentially focusable.
 *                             https://html.spec.whatwg.org/multipage/interaction.html#the-tabindex-attribute
 *
 * @return {string} CSS selector.
 */
function buildSelector(sequential) {
  return [sequential ? '[tabindex]:not([tabindex^="-"])' : '[tabindex]', 'a[href]', 'button:not([disabled])', 'input:not([type="hidden"]):not([disabled])', 'select:not([disabled])', 'textarea:not([disabled])', 'iframe:not([tabindex^="-"])', 'object', 'embed', 'area[href]', '[contenteditable]:not([contenteditable=false])'].join(',');
}

/**
 * Returns true if the specified element is visible (i.e. neither display: none
 * nor visibility: hidden).
 *
 * @param {HTMLElement} element DOM element to test.
 *
 * @return {boolean} Whether element is visible.
 */
function isVisible(element) {
  return element.offsetWidth > 0 || element.offsetHeight > 0 || element.getClientRects().length > 0;
}

/**
 * Returns true if the specified area element is a valid focusable element, or
 * false otherwise. Area is only focusable if within a map where a named map
 * referenced by an image somewhere in the document.
 *
 * @param {HTMLAreaElement} element DOM area element to test.
 *
 * @return {boolean} Whether area element is valid for focus.
 */
function isValidFocusableArea(element) {
  /** @type {HTMLMapElement | null} */
  const map = element.closest('map[name]');
  if (!map) {
    return false;
  }

  /** @type {HTMLImageElement | null} */
  const img = element.ownerDocument.querySelector('img[usemap="#' + map.name + '"]');
  return !!img && isVisible(img);
}

/**
 * Returns all focusable elements within a given context.
 *
 * @param {Element} context              Element in which to search.
 * @param {Object}  options
 * @param {boolean} [options.sequential] If set, only return elements that are
 *                                       sequentially focusable.
 *                                       Non-interactive elements with a
 *                                       negative `tabindex` are focusable but
 *                                       not sequentially focusable.
 *                                       https://html.spec.whatwg.org/multipage/interaction.html#the-tabindex-attribute
 *
 * @return {HTMLElement[]} Focusable elements.
 */
function find(context, {
  sequential = false
} = {}) {
  /* eslint-disable jsdoc/no-undefined-types */
  /** @type {NodeListOf<HTMLElement>} */
  /* eslint-enable jsdoc/no-undefined-types */
  const elements = context.querySelectorAll(buildSelector(sequential));
  return Array.from(elements).filter(element => {
    if (!isVisible(element)) {
      return false;
    }
    const {
      nodeName
    } = element;
    if ('AREA' === nodeName) {
      return isValidFocusableArea( /** @type {HTMLAreaElement} */element);
    }
    return true;
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/tabbable.js
/**
 * Internal dependencies
 */


/**
 * Returns the tab index of the given element. In contrast with the tabIndex
 * property, this normalizes the default (0) to avoid browser inconsistencies,
 * operating under the assumption that this function is only ever called with a
 * focusable node.
 *
 * @see https://bugzilla.mozilla.org/show_bug.cgi?id=1190261
 *
 * @param {Element} element Element from which to retrieve.
 *
 * @return {number} Tab index of element (default 0).
 */
function getTabIndex(element) {
  const tabIndex = element.getAttribute('tabindex');
  return tabIndex === null ? 0 : parseInt(tabIndex, 10);
}

/**
 * Returns true if the specified element is tabbable, or false otherwise.
 *
 * @param {Element} element Element to test.
 *
 * @return {boolean} Whether element is tabbable.
 */
function isTabbableIndex(element) {
  return getTabIndex(element) !== -1;
}

/** @typedef {Element & { type?: string, checked?: boolean, name?: string }} MaybeHTMLInputElement */

/**
 * Returns a stateful reducer function which constructs a filtered array of
 * tabbable elements, where at most one radio input is selected for a given
 * name, giving priority to checked input, falling back to the first
 * encountered.
 *
 * @return {(acc: MaybeHTMLInputElement[], el: MaybeHTMLInputElement) => MaybeHTMLInputElement[]} Radio group collapse reducer.
 */
function createStatefulCollapseRadioGroup() {
  /** @type {Record<string, MaybeHTMLInputElement>} */
  const CHOSEN_RADIO_BY_NAME = {};
  return function collapseRadioGroup( /** @type {MaybeHTMLInputElement[]} */result, /** @type {MaybeHTMLInputElement} */element) {
    const {
      nodeName,
      type,
      checked,
      name
    } = element;

    // For all non-radio tabbables, construct to array by concatenating.
    if (nodeName !== 'INPUT' || type !== 'radio' || !name) {
      return result.concat(element);
    }
    const hasChosen = CHOSEN_RADIO_BY_NAME.hasOwnProperty(name);

    // Omit by skipping concatenation if the radio element is not chosen.
    const isChosen = checked || !hasChosen;
    if (!isChosen) {
      return result;
    }

    // At this point, if there had been a chosen element, the current
    // element is checked and should take priority. Retroactively remove
    // the element which had previously been considered the chosen one.
    if (hasChosen) {
      const hadChosenElement = CHOSEN_RADIO_BY_NAME[name];
      result = result.filter(e => e !== hadChosenElement);
    }
    CHOSEN_RADIO_BY_NAME[name] = element;
    return result.concat(element);
  };
}

/**
 * An array map callback, returning an object with the element value and its
 * array index location as properties. This is used to emulate a proper stable
 * sort where equal tabIndex should be left in order of their occurrence in the
 * document.
 *
 * @param {Element} element Element.
 * @param {number}  index   Array index of element.
 *
 * @return {{ element: Element, index: number }} Mapped object with element, index.
 */
function mapElementToObjectTabbable(element, index) {
  return {
    element,
    index
  };
}

/**
 * An array map callback, returning an element of the given mapped object's
 * element value.
 *
 * @param {{ element: Element }} object Mapped object with element.
 *
 * @return {Element} Mapped object element.
 */
function mapObjectTabbableToElement(object) {
  return object.element;
}

/**
 * A sort comparator function used in comparing two objects of mapped elements.
 *
 * @see mapElementToObjectTabbable
 *
 * @param {{ element: Element, index: number }} a First object to compare.
 * @param {{ element: Element, index: number }} b Second object to compare.
 *
 * @return {number} Comparator result.
 */
function compareObjectTabbables(a, b) {
  const aTabIndex = getTabIndex(a.element);
  const bTabIndex = getTabIndex(b.element);
  if (aTabIndex === bTabIndex) {
    return a.index - b.index;
  }
  return aTabIndex - bTabIndex;
}

/**
 * Givin focusable elements, filters out tabbable element.
 *
 * @param {Element[]} focusables Focusable elements to filter.
 *
 * @return {Element[]} Tabbable elements.
 */
function filterTabbable(focusables) {
  return focusables.filter(isTabbableIndex).map(mapElementToObjectTabbable).sort(compareObjectTabbables).map(mapObjectTabbableToElement).reduce(createStatefulCollapseRadioGroup(), []);
}

/**
 * @param {Element} context
 * @return {Element[]} Tabbable elements within the context.
 */
function tabbable_find(context) {
  return filterTabbable(find(context));
}

/**
 * Given a focusable element, find the preceding tabbable element.
 *
 * @param {Element} element The focusable element before which to look. Defaults
 *                          to the active element.
 *
 * @return {Element|undefined} Preceding tabbable element.
 */
function findPrevious(element) {
  return filterTabbable(find(element.ownerDocument.body)).reverse().find(focusable => {
    return (
      // eslint-disable-next-line no-bitwise
      element.compareDocumentPosition(focusable) & element.DOCUMENT_POSITION_PRECEDING
    );
  });
}

/**
 * Given a focusable element, find the next tabbable element.
 *
 * @param {Element} element The focusable element after which to look. Defaults
 *                          to the active element.
 *
 * @return {Element|undefined} Next tabbable element.
 */
function findNext(element) {
  return filterTabbable(find(element.ownerDocument.body)).find(focusable => {
    return (
      // eslint-disable-next-line no-bitwise
      element.compareDocumentPosition(focusable) & element.DOCUMENT_POSITION_FOLLOWING
    );
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/utils/assert-is-defined.js
function assertIsDefined(val, name) {
  if (false) {}
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/get-rectangle-from-range.js
/**
 * Internal dependencies
 */


/**
 * Get the rectangle of a given Range. Returns `null` if no suitable rectangle
 * can be found.
 *
 * @param {Range} range The range.
 *
 * @return {DOMRect?} The rectangle.
 */
function getRectangleFromRange(range) {
  // For uncollapsed ranges, get the rectangle that bounds the contents of the
  // range; this a rectangle enclosing the union of the bounding rectangles
  // for all the elements in the range.
  if (!range.collapsed) {
    const rects = Array.from(range.getClientRects());

    // If there's just a single rect, return it.
    if (rects.length === 1) {
      return rects[0];
    }

    // Ignore tiny selection at the edge of a range.
    const filteredRects = rects.filter(({
      width
    }) => width > 1);

    // If it's full of tiny selections, return browser default.
    if (filteredRects.length === 0) {
      return range.getBoundingClientRect();
    }
    if (filteredRects.length === 1) {
      return filteredRects[0];
    }
    let {
      top: furthestTop,
      bottom: furthestBottom,
      left: furthestLeft,
      right: furthestRight
    } = filteredRects[0];
    for (const {
      top,
      bottom,
      left,
      right
    } of filteredRects) {
      if (top < furthestTop) furthestTop = top;
      if (bottom > furthestBottom) furthestBottom = bottom;
      if (left < furthestLeft) furthestLeft = left;
      if (right > furthestRight) furthestRight = right;
    }
    return new window.DOMRect(furthestLeft, furthestTop, furthestRight - furthestLeft, furthestBottom - furthestTop);
  }
  const {
    startContainer
  } = range;
  const {
    ownerDocument
  } = startContainer;

  // Correct invalid "BR" ranges. The cannot contain any children.
  if (startContainer.nodeName === 'BR') {
    const {
      parentNode
    } = startContainer;
    assertIsDefined(parentNode, 'parentNode');
    const index = /** @type {Node[]} */Array.from(parentNode.childNodes).indexOf(startContainer);
    assertIsDefined(ownerDocument, 'ownerDocument');
    range = ownerDocument.createRange();
    range.setStart(parentNode, index);
    range.setEnd(parentNode, index);
  }
  const rects = range.getClientRects();

  // If we have multiple rectangles for a collapsed range, there's no way to
  // know which it is, so don't return anything.
  if (rects.length > 1) {
    return null;
  }
  let rect = rects[0];

  // If the collapsed range starts (and therefore ends) at an element node,
  // `getClientRects` can be empty in some browsers. This can be resolved
  // by adding a temporary text node with zero-width space to the range.
  //
  // See: https://stackoverflow.com/a/6847328/995445
  if (!rect || rect.height === 0) {
    assertIsDefined(ownerDocument, 'ownerDocument');
    const padNode = ownerDocument.createTextNode('\u200b');
    // Do not modify the live range.
    range = range.cloneRange();
    range.insertNode(padNode);
    rect = range.getClientRects()[0];
    assertIsDefined(padNode.parentNode, 'padNode.parentNode');
    padNode.parentNode.removeChild(padNode);
  }
  return rect;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/compute-caret-rect.js
/**
 * Internal dependencies
 */



/**
 * Get the rectangle for the selection in a container.
 *
 * @param {Window} win The window of the selection.
 *
 * @return {DOMRect | null} The rectangle.
 */
function computeCaretRect(win) {
  const selection = win.getSelection();
  assertIsDefined(selection, 'selection');
  const range = selection.rangeCount ? selection.getRangeAt(0) : null;
  if (!range) {
    return null;
  }
  return getRectangleFromRange(range);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/document-has-text-selection.js
/**
 * Internal dependencies
 */


/**
 * Check whether the current document has selected text. This applies to ranges
 * of text in the document, and not selection inside `<input>` and `<textarea>`
 * elements.
 *
 * See: https://developer.mozilla.org/en-US/docs/Web/API/Window/getSelection#Related_objects.
 *
 * @param {Document} doc The document to check.
 *
 * @return {boolean} True if there is selection, false if not.
 */
function documentHasTextSelection(doc) {
  assertIsDefined(doc.defaultView, 'doc.defaultView');
  const selection = doc.defaultView.getSelection();
  assertIsDefined(selection, 'selection');
  const range = selection.rangeCount ? selection.getRangeAt(0) : null;
  return !!range && !range.collapsed;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/is-html-input-element.js
/* eslint-disable jsdoc/valid-types */
/**
 * @param {Node} node
 * @return {node is HTMLInputElement} Whether the node is an HTMLInputElement.
 */
function isHTMLInputElement(node) {
  /* eslint-enable jsdoc/valid-types */
  return node?.nodeName === 'INPUT';
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/is-text-field.js
/**
 * Internal dependencies
 */


/* eslint-disable jsdoc/valid-types */
/**
 * Check whether the given element is a text field, where text field is defined
 * by the ability to select within the input, or that it is contenteditable.
 *
 * See: https://html.spec.whatwg.org/#textFieldSelection
 *
 * @param {Node} node The HTML element.
 * @return {node is HTMLElement} True if the element is an text field, false if not.
 */
function isTextField(node) {
  /* eslint-enable jsdoc/valid-types */
  const nonTextInputs = ['button', 'checkbox', 'hidden', 'file', 'radio', 'image', 'range', 'reset', 'submit', 'number', 'email', 'time'];
  return isHTMLInputElement(node) && node.type && !nonTextInputs.includes(node.type) || node.nodeName === 'TEXTAREA' || /** @type {HTMLElement} */node.contentEditable === 'true';
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/input-field-has-uncollapsed-selection.js
/**
 * Internal dependencies
 */



/**
 * Check whether the given input field or textarea contains a (uncollapsed)
 * selection of text.
 *
 * CAVEAT: Only specific text-based HTML inputs support the selection APIs
 * needed to determine whether they have a collapsed or uncollapsed selection.
 * This function defaults to returning `true` when the selection cannot be
 * inspected, such as with `<input type="time">`. The rationale is that this
 * should cause the block editor to defer to the browser's native selection
 * handling (e.g. copying and pasting), thereby reducing friction for the user.
 *
 * See: https://html.spec.whatwg.org/multipage/input.html#do-not-apply
 *
 * @param {Element} element The HTML element.
 *
 * @return {boolean} Whether the input/textareaa element has some "selection".
 */
function inputFieldHasUncollapsedSelection(element) {
  if (!isHTMLInputElement(element) && !isTextField(element)) {
    return false;
  }

  // Safari throws a type error when trying to get `selectionStart` and
  // `selectionEnd` on non-text <input> elements, so a try/catch construct is
  // necessary.
  try {
    const {
      selectionStart,
      selectionEnd
    } = /** @type {HTMLInputElement | HTMLTextAreaElement} */element;
    return (
      // `null` means the input type doesn't implement selection, thus we
      // cannot determine whether the selection is collapsed, so we
      // default to true.
      selectionStart === null ||
      // when not null, compare the two points
      selectionStart !== selectionEnd
    );
  } catch (error) {
    // This is Safari's way of saying that the input type doesn't implement
    // selection, so we default to true.
    return true;
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/document-has-uncollapsed-selection.js
/**
 * Internal dependencies
 */



/**
 * Check whether the current document has any sort of (uncollapsed) selection.
 * This includes ranges of text across elements and any selection inside
 * textual `<input>` and `<textarea>` elements.
 *
 * @param {Document} doc The document to check.
 *
 * @return {boolean} Whether there is any recognizable text selection in the document.
 */
function documentHasUncollapsedSelection(doc) {
  return documentHasTextSelection(doc) || !!doc.activeElement && inputFieldHasUncollapsedSelection(doc.activeElement);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/document-has-selection.js
/**
 * Internal dependencies
 */




/**
 * Check whether the current document has a selection. This includes focus in
 * input fields, textareas, and general rich-text selection.
 *
 * @param {Document} doc The document to check.
 *
 * @return {boolean} True if there is selection, false if not.
 */
function documentHasSelection(doc) {
  return !!doc.activeElement && (isHTMLInputElement(doc.activeElement) || isTextField(doc.activeElement) || documentHasTextSelection(doc));
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/get-computed-style.js
/**
 * Internal dependencies
 */


/* eslint-disable jsdoc/valid-types */
/**
 * @param {Element} element
 * @return {ReturnType<Window['getComputedStyle']>} The computed style for the element.
 */
function getComputedStyle(element) {
  /* eslint-enable jsdoc/valid-types */
  assertIsDefined(element.ownerDocument.defaultView, 'element.ownerDocument.defaultView');
  return element.ownerDocument.defaultView.getComputedStyle(element);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/get-scroll-container.js
/**
 * Internal dependencies
 */


/**
 * Given a DOM node, finds the closest scrollable container node or the node
 * itself, if scrollable.
 *
 * @param {Element | null} node      Node from which to start.
 * @param {?string}        direction Direction of scrollable container to search for ('vertical', 'horizontal', 'all').
 *                                   Defaults to 'vertical'.
 * @return {Element | undefined} Scrollable container node, if found.
 */
function getScrollContainer(node, direction = 'vertical') {
  if (!node) {
    return undefined;
  }
  if (direction === 'vertical' || direction === 'all') {
    // Scrollable if scrollable height exceeds displayed...
    if (node.scrollHeight > node.clientHeight) {
      // ...except when overflow is defined to be hidden or visible
      const {
        overflowY
      } = getComputedStyle(node);
      if (/(auto|scroll)/.test(overflowY)) {
        return node;
      }
    }
  }
  if (direction === 'horizontal' || direction === 'all') {
    // Scrollable if scrollable width exceeds displayed...
    if (node.scrollWidth > node.clientWidth) {
      // ...except when overflow is defined to be hidden or visible
      const {
        overflowX
      } = getComputedStyle(node);
      if (/(auto|scroll)/.test(overflowX)) {
        return node;
      }
    }
  }
  if (node.ownerDocument === node.parentNode) {
    return node;
  }

  // Continue traversing.
  return getScrollContainer( /** @type {Element} */node.parentNode, direction);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/get-offset-parent.js
/**
 * Internal dependencies
 */


/**
 * Returns the closest positioned element, or null under any of the conditions
 * of the offsetParent specification. Unlike offsetParent, this function is not
 * limited to HTMLElement and accepts any Node (e.g. Node.TEXT_NODE).
 *
 * @see https://drafts.csswg.org/cssom-view/#dom-htmlelement-offsetparent
 *
 * @param {Node} node Node from which to find offset parent.
 *
 * @return {Node | null} Offset parent.
 */
function getOffsetParent(node) {
  // Cannot retrieve computed style or offset parent only anything other than
  // an element node, so find the closest element node.
  let closestElement;
  while (closestElement = /** @type {Node} */node.parentNode) {
    if (closestElement.nodeType === closestElement.ELEMENT_NODE) {
      break;
    }
  }
  if (!closestElement) {
    return null;
  }

  // If the closest element is already positioned, return it, as offsetParent
  // does not otherwise consider the node itself.
  if (getComputedStyle( /** @type {Element} */closestElement).position !== 'static') {
    return closestElement;
  }

  // offsetParent is undocumented/draft.
  return /** @type {Node & { offsetParent: Node }} */closestElement.offsetParent;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/is-input-or-text-area.js
/* eslint-disable jsdoc/valid-types */
/**
 * @param {Element} element
 * @return {element is HTMLInputElement | HTMLTextAreaElement} Whether the element is an input or textarea
 */
function isInputOrTextArea(element) {
  /* eslint-enable jsdoc/valid-types */
  return element.tagName === 'INPUT' || element.tagName === 'TEXTAREA';
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/is-entirely-selected.js
/**
 * Internal dependencies
 */



/**
 * Check whether the contents of the element have been entirely selected.
 * Returns true if there is no possibility of selection.
 *
 * @param {HTMLElement} element The element to check.
 *
 * @return {boolean} True if entirely selected, false if not.
 */
function isEntirelySelected(element) {
  if (isInputOrTextArea(element)) {
    return element.selectionStart === 0 && element.value.length === element.selectionEnd;
  }
  if (!element.isContentEditable) {
    return true;
  }
  const {
    ownerDocument
  } = element;
  const {
    defaultView
  } = ownerDocument;
  assertIsDefined(defaultView, 'defaultView');
  const selection = defaultView.getSelection();
  assertIsDefined(selection, 'selection');
  const range = selection.rangeCount ? selection.getRangeAt(0) : null;
  if (!range) {
    return true;
  }
  const {
    startContainer,
    endContainer,
    startOffset,
    endOffset
  } = range;
  if (startContainer === element && endContainer === element && startOffset === 0 && endOffset === element.childNodes.length) {
    return true;
  }
  const lastChild = element.lastChild;
  assertIsDefined(lastChild, 'lastChild');
  const endContainerContentLength = endContainer.nodeType === endContainer.TEXT_NODE ? /** @type {Text} */endContainer.data.length : endContainer.childNodes.length;
  return isDeepChild(startContainer, element, 'firstChild') && isDeepChild(endContainer, element, 'lastChild') && startOffset === 0 && endOffset === endContainerContentLength;
}

/**
 * Check whether the contents of the element have been entirely selected.
 * Returns true if there is no possibility of selection.
 *
 * @param {HTMLElement|Node}         query     The element to check.
 * @param {HTMLElement}              container The container that we suspect "query" may be a first or last child of.
 * @param {"firstChild"|"lastChild"} propName  "firstChild" or "lastChild"
 *
 * @return {boolean} True if query is a deep first/last child of container, false otherwise.
 */
function isDeepChild(query, container, propName) {
  /** @type {HTMLElement | ChildNode | null} */
  let candidate = container;
  do {
    if (query === candidate) {
      return true;
    }
    candidate = candidate[propName];
  } while (candidate);
  return false;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/is-form-element.js
/**
 * Internal dependencies
 */


/**
 *
 * Detects if element is a form element.
 *
 * @param {Element} element The element to check.
 *
 * @return {boolean} True if form element and false otherwise.
 */
function isFormElement(element) {
  if (!element) {
    return false;
  }
  const {
    tagName
  } = element;
  const checkForInputTextarea = isInputOrTextArea(element);
  return checkForInputTextarea || tagName === 'BUTTON' || tagName === 'SELECT';
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/is-rtl.js
/**
 * Internal dependencies
 */


/**
 * Whether the element's text direction is right-to-left.
 *
 * @param {Element} element The element to check.
 *
 * @return {boolean} True if rtl, false if ltr.
 */
function isRTL(element) {
  return getComputedStyle(element).direction === 'rtl';
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/get-range-height.js
/**
 * Gets the height of the range without ignoring zero width rectangles, which
 * some browsers ignore when creating a union.
 *
 * @param {Range} range The range to check.
 * @return {number | undefined} Height of the range or undefined if the range has no client rectangles.
 */
function getRangeHeight(range) {
  const rects = Array.from(range.getClientRects());
  if (!rects.length) {
    return;
  }
  const highestTop = Math.min(...rects.map(({
    top
  }) => top));
  const lowestBottom = Math.max(...rects.map(({
    bottom
  }) => bottom));
  return lowestBottom - highestTop;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/is-selection-forward.js
/**
 * Internal dependencies
 */


/**
 * Returns true if the given selection object is in the forward direction, or
 * false otherwise.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/API/Node/compareDocumentPosition
 *
 * @param {Selection} selection Selection object to check.
 *
 * @return {boolean} Whether the selection is forward.
 */
function isSelectionForward(selection) {
  const {
    anchorNode,
    focusNode,
    anchorOffset,
    focusOffset
  } = selection;
  assertIsDefined(anchorNode, 'anchorNode');
  assertIsDefined(focusNode, 'focusNode');
  const position = anchorNode.compareDocumentPosition(focusNode);

  // Disable reason: `Node#compareDocumentPosition` returns a bitmask value,
  // so bitwise operators are intended.
  /* eslint-disable no-bitwise */
  // Compare whether anchor node precedes focus node. If focus node (where
  // end of selection occurs) is after the anchor node, it is forward.
  if (position & anchorNode.DOCUMENT_POSITION_PRECEDING) {
    return false;
  }
  if (position & anchorNode.DOCUMENT_POSITION_FOLLOWING) {
    return true;
  }
  /* eslint-enable no-bitwise */

  // `compareDocumentPosition` returns 0 when passed the same node, in which
  // case compare offsets.
  if (position === 0) {
    return anchorOffset <= focusOffset;
  }

  // This should never be reached, but return true as default case.
  return true;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/caret-range-from-point.js
/**
 * Polyfill.
 * Get a collapsed range for a given point.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/API/Document/caretRangeFromPoint
 *
 * @param {DocumentMaybeWithCaretPositionFromPoint} doc The document of the range.
 * @param {number}                                  x   Horizontal position within the current viewport.
 * @param {number}                                  y   Vertical position within the current viewport.
 *
 * @return {Range | null} The best range for the given point.
 */
function caretRangeFromPoint(doc, x, y) {
  if (doc.caretRangeFromPoint) {
    return doc.caretRangeFromPoint(x, y);
  }
  if (!doc.caretPositionFromPoint) {
    return null;
  }
  const point = doc.caretPositionFromPoint(x, y);

  // If x or y are negative, outside viewport, or there is no text entry node.
  // https://developer.mozilla.org/en-US/docs/Web/API/Document/caretRangeFromPoint
  if (!point) {
    return null;
  }
  const range = doc.createRange();
  range.setStart(point.offsetNode, point.offset);
  range.collapse(true);
  return range;
}

/**
 * @typedef {{caretPositionFromPoint?: (x: number, y: number)=> CaretPosition | null} & Document } DocumentMaybeWithCaretPositionFromPoint
 * @typedef {{ readonly offset: number; readonly offsetNode: Node; getClientRect(): DOMRect | null; }} CaretPosition
 */

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/hidden-caret-range-from-point.js
/**
 * Internal dependencies
 */



/**
 * Get a collapsed range for a given point.
 * Gives the container a temporary high z-index (above any UI).
 * This is preferred over getting the UI nodes and set styles there.
 *
 * @param {Document}    doc       The document of the range.
 * @param {number}      x         Horizontal position within the current viewport.
 * @param {number}      y         Vertical position within the current viewport.
 * @param {HTMLElement} container Container in which the range is expected to be found.
 *
 * @return {?Range} The best range for the given point.
 */
function hiddenCaretRangeFromPoint(doc, x, y, container) {
  const originalZIndex = container.style.zIndex;
  const originalPosition = container.style.position;
  const {
    position = 'static'
  } = getComputedStyle(container);

  // A z-index only works if the element position is not static.
  if (position === 'static') {
    container.style.position = 'relative';
  }
  container.style.zIndex = '10000';
  const range = caretRangeFromPoint(doc, x, y);
  container.style.zIndex = originalZIndex;
  container.style.position = originalPosition;
  return range;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/scroll-if-no-range.js
/**
 * If no range range can be created or it is outside the container, the element
 * may be out of view, so scroll it into view and try again.
 *
 * @param {HTMLElement} container  The container to scroll.
 * @param {boolean}     alignToTop True to align to top, false to bottom.
 * @param {Function}    callback   The callback to create the range.
 *
 * @return {?Range} The range returned by the callback.
 */
function scrollIfNoRange(container, alignToTop, callback) {
  let range = callback();

  // If no range range can be created or it is outside the container, the
  // element may be out of view.
  if (!range || !range.startContainer || !container.contains(range.startContainer)) {
    container.scrollIntoView(alignToTop);
    range = callback();
    if (!range || !range.startContainer || !container.contains(range.startContainer)) {
      return null;
    }
  }
  return range;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/is-edge.js
/**
 * Internal dependencies
 */









/**
 * Check whether the selection is at the edge of the container. Checks for
 * horizontal position by default. Set `onlyVertical` to true to check only
 * vertically.
 *
 * @param {HTMLElement} container            Focusable element.
 * @param {boolean}     isReverse            Set to true to check left, false to check right.
 * @param {boolean}     [onlyVertical=false] Set to true to check only vertical position.
 *
 * @return {boolean} True if at the edge, false if not.
 */
function isEdge(container, isReverse, onlyVertical = false) {
  if (isInputOrTextArea(container) && typeof container.selectionStart === 'number') {
    if (container.selectionStart !== container.selectionEnd) {
      return false;
    }
    if (isReverse) {
      return container.selectionStart === 0;
    }
    return container.value.length === container.selectionStart;
  }
  if (!( /** @type {HTMLElement} */container.isContentEditable)) {
    return true;
  }
  const {
    ownerDocument
  } = container;
  const {
    defaultView
  } = ownerDocument;
  assertIsDefined(defaultView, 'defaultView');
  const selection = defaultView.getSelection();
  if (!selection || !selection.rangeCount) {
    return false;
  }
  const range = selection.getRangeAt(0);
  const collapsedRange = range.cloneRange();
  const isForward = isSelectionForward(selection);
  const isCollapsed = selection.isCollapsed;

  // Collapse in direction of selection.
  if (!isCollapsed) {
    collapsedRange.collapse(!isForward);
  }
  const collapsedRangeRect = getRectangleFromRange(collapsedRange);
  const rangeRect = getRectangleFromRange(range);
  if (!collapsedRangeRect || !rangeRect) {
    return false;
  }

  // Only consider the multiline selection at the edge if the direction is
  // towards the edge. The selection is multiline if it is taller than the
  // collapsed  selection.
  const rangeHeight = getRangeHeight(range);
  if (!isCollapsed && rangeHeight && rangeHeight > collapsedRangeRect.height && isForward === isReverse) {
    return false;
  }

  // In the case of RTL scripts, the horizontal edge is at the opposite side.
  const isReverseDir = isRTL(container) ? !isReverse : isReverse;
  const containerRect = container.getBoundingClientRect();

  // To check if a selection is at the edge, we insert a test selection at the
  // edge of the container and check if the selections have the same vertical
  // or horizontal position. If they do, the selection is at the edge.
  // This method proves to be better than a DOM-based calculation for the
  // horizontal edge, since it ignores empty textnodes and a trailing line
  // break element. In other words, we need to check visual positioning, not
  // DOM positioning.
  // It also proves better than using the computed style for the vertical
  // edge, because we cannot know the padding and line height reliably in
  // pixels. `getComputedStyle` may return a value with different units.
  const x = isReverseDir ? containerRect.left + 1 : containerRect.right - 1;
  const y = isReverse ? containerRect.top + 1 : containerRect.bottom - 1;
  const testRange = scrollIfNoRange(container, isReverse, () => hiddenCaretRangeFromPoint(ownerDocument, x, y, container));
  if (!testRange) {
    return false;
  }
  const testRect = getRectangleFromRange(testRange);
  if (!testRect) {
    return false;
  }
  const verticalSide = isReverse ? 'top' : 'bottom';
  const horizontalSide = isReverseDir ? 'left' : 'right';
  const verticalDiff = testRect[verticalSide] - rangeRect[verticalSide];
  const horizontalDiff = testRect[horizontalSide] - collapsedRangeRect[horizontalSide];

  // Allow the position to be 1px off.
  const hasVerticalDiff = Math.abs(verticalDiff) <= 1;
  const hasHorizontalDiff = Math.abs(horizontalDiff) <= 1;
  return onlyVertical ? hasVerticalDiff : hasVerticalDiff && hasHorizontalDiff;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/is-horizontal-edge.js
/**
 * Internal dependencies
 */


/**
 * Check whether the selection is horizontally at the edge of the container.
 *
 * @param {HTMLElement} container Focusable element.
 * @param {boolean}     isReverse Set to true to check left, false for right.
 *
 * @return {boolean} True if at the horizontal edge, false if not.
 */
function isHorizontalEdge(container, isReverse) {
  return isEdge(container, isReverse);
}

;// CONCATENATED MODULE: external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/is-number-input.js
/**
 * WordPress dependencies
 */


/**
 * Internal dependencies
 */


/* eslint-disable jsdoc/valid-types */
/**
 * Check whether the given element is an input field of type number.
 *
 * @param {Node} node The HTML node.
 *
 * @return {node is HTMLInputElement} True if the node is number input.
 */
function isNumberInput(node) {
  external_wp_deprecated_default()('wp.dom.isNumberInput', {
    since: '6.1',
    version: '6.5'
  });
  /* eslint-enable jsdoc/valid-types */
  return isHTMLInputElement(node) && node.type === 'number' && !isNaN(node.valueAsNumber);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/is-vertical-edge.js
/**
 * Internal dependencies
 */


/**
 * Check whether the selection is vertically at the edge of the container.
 *
 * @param {HTMLElement} container Focusable element.
 * @param {boolean}     isReverse Set to true to check top, false for bottom.
 *
 * @return {boolean} True if at the vertical edge, false if not.
 */
function isVerticalEdge(container, isReverse) {
  return isEdge(container, isReverse, true);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/place-caret-at-edge.js
/**
 * Internal dependencies
 */






/**
 * Gets the range to place.
 *
 * @param {HTMLElement}      container Focusable element.
 * @param {boolean}          isReverse True for end, false for start.
 * @param {number|undefined} x         X coordinate to vertically position.
 *
 * @return {Range|null} The range to place.
 */
function getRange(container, isReverse, x) {
  const {
    ownerDocument
  } = container;
  // In the case of RTL scripts, the horizontal edge is at the opposite side.
  const isReverseDir = isRTL(container) ? !isReverse : isReverse;
  const containerRect = container.getBoundingClientRect();
  // When placing at the end (isReverse), find the closest range to the bottom
  // right corner. When placing at the start, to the top left corner.
  // Ensure x is defined and within the container's boundaries. When it's
  // exactly at the boundary, it's not considered within the boundaries.
  if (x === undefined) {
    x = isReverse ? containerRect.right - 1 : containerRect.left + 1;
  } else if (x <= containerRect.left) {
    x = containerRect.left + 1;
  } else if (x >= containerRect.right) {
    x = containerRect.right - 1;
  }
  const y = isReverseDir ? containerRect.bottom - 1 : containerRect.top + 1;
  return hiddenCaretRangeFromPoint(ownerDocument, x, y, container);
}

/**
 * Places the caret at start or end of a given element.
 *
 * @param {HTMLElement}      container Focusable element.
 * @param {boolean}          isReverse True for end, false for start.
 * @param {number|undefined} x         X coordinate to vertically position.
 */
function placeCaretAtEdge(container, isReverse, x) {
  if (!container) {
    return;
  }
  container.focus();
  if (isInputOrTextArea(container)) {
    // The element may not support selection setting.
    if (typeof container.selectionStart !== 'number') {
      return;
    }
    if (isReverse) {
      container.selectionStart = container.value.length;
      container.selectionEnd = container.value.length;
    } else {
      container.selectionStart = 0;
      container.selectionEnd = 0;
    }
    return;
  }
  if (!container.isContentEditable) {
    return;
  }
  const range = scrollIfNoRange(container, isReverse, () => getRange(container, isReverse, x));
  if (!range) return;
  const {
    ownerDocument
  } = container;
  const {
    defaultView
  } = ownerDocument;
  assertIsDefined(defaultView, 'defaultView');
  const selection = defaultView.getSelection();
  assertIsDefined(selection, 'selection');
  selection.removeAllRanges();
  selection.addRange(range);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/place-caret-at-horizontal-edge.js
/**
 * Internal dependencies
 */


/**
 * Places the caret at start or end of a given element.
 *
 * @param {HTMLElement} container Focusable element.
 * @param {boolean}     isReverse True for end, false for start.
 */
function placeCaretAtHorizontalEdge(container, isReverse) {
  return placeCaretAtEdge(container, isReverse, undefined);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/place-caret-at-vertical-edge.js
/**
 * Internal dependencies
 */


/**
 * Places the caret at the top or bottom of a given element.
 *
 * @param {HTMLElement} container Focusable element.
 * @param {boolean}     isReverse True for bottom, false for top.
 * @param {DOMRect}     [rect]    The rectangle to position the caret with.
 */
function placeCaretAtVerticalEdge(container, isReverse, rect) {
  return placeCaretAtEdge(container, isReverse, rect?.left);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/insert-after.js
/**
 * Internal dependencies
 */


/**
 * Given two DOM nodes, inserts the former in the DOM as the next sibling of
 * the latter.
 *
 * @param {Node} newNode       Node to be inserted.
 * @param {Node} referenceNode Node after which to perform the insertion.
 * @return {void}
 */
function insertAfter(newNode, referenceNode) {
  assertIsDefined(referenceNode.parentNode, 'referenceNode.parentNode');
  referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/remove.js
/**
 * Internal dependencies
 */


/**
 * Given a DOM node, removes it from the DOM.
 *
 * @param {Node} node Node to be removed.
 * @return {void}
 */
function remove(node) {
  assertIsDefined(node.parentNode, 'node.parentNode');
  node.parentNode.removeChild(node);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/replace.js
/**
 * Internal dependencies
 */




/**
 * Given two DOM nodes, replaces the former with the latter in the DOM.
 *
 * @param {Element} processedNode Node to be removed.
 * @param {Element} newNode       Node to be inserted in its place.
 * @return {void}
 */
function replace(processedNode, newNode) {
  assertIsDefined(processedNode.parentNode, 'processedNode.parentNode');
  insertAfter(newNode, processedNode.parentNode);
  remove(processedNode);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/unwrap.js
/**
 * Internal dependencies
 */


/**
 * Unwrap the given node. This means any child nodes are moved to the parent.
 *
 * @param {Node} node The node to unwrap.
 *
 * @return {void}
 */
function unwrap(node) {
  const parent = node.parentNode;
  assertIsDefined(parent, 'node.parentNode');
  while (node.firstChild) {
    parent.insertBefore(node.firstChild, node);
  }
  parent.removeChild(node);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/replace-tag.js
/**
 * Internal dependencies
 */


/**
 * Replaces the given node with a new node with the given tag name.
 *
 * @param {Element} node    The node to replace
 * @param {string}  tagName The new tag name.
 *
 * @return {Element} The new node.
 */
function replaceTag(node, tagName) {
  const newNode = node.ownerDocument.createElement(tagName);
  while (node.firstChild) {
    newNode.appendChild(node.firstChild);
  }
  assertIsDefined(node.parentNode, 'node.parentNode');
  node.parentNode.replaceChild(newNode, node);
  return newNode;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/wrap.js
/**
 * Internal dependencies
 */


/**
 * Wraps the given node with a new node with the given tag name.
 *
 * @param {Element} newNode       The node to insert.
 * @param {Element} referenceNode The node to wrap.
 */
function wrap(newNode, referenceNode) {
  assertIsDefined(referenceNode.parentNode, 'referenceNode.parentNode');
  referenceNode.parentNode.insertBefore(newNode, referenceNode);
  newNode.appendChild(referenceNode);
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/safe-html.js
/**
 * Internal dependencies
 */


/**
 * Strips scripts and on* attributes from HTML.
 *
 * @param {string} html HTML to sanitize.
 *
 * @return {string} The sanitized HTML.
 */
function safeHTML(html) {
  const {
    body
  } = document.implementation.createHTMLDocument('');
  body.innerHTML = html;
  const elements = body.getElementsByTagName('*');
  let elementIndex = elements.length;
  while (elementIndex--) {
    const element = elements[elementIndex];
    if (element.tagName === 'SCRIPT') {
      remove(element);
    } else {
      let attributeIndex = element.attributes.length;
      while (attributeIndex--) {
        const {
          name: key
        } = element.attributes[attributeIndex];
        if (key.startsWith('on')) {
          element.removeAttribute(key);
        }
      }
    }
  }
  return body.innerHTML;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/strip-html.js
/**
 * Internal dependencies
 */


/**
 * Removes any HTML tags from the provided string.
 *
 * @param {string} html The string containing html.
 *
 * @return {string} The text content with any html removed.
 */
function stripHTML(html) {
  // Remove any script tags or on* attributes otherwise their *contents* will be left
  // in place following removal of HTML tags.
  html = safeHTML(html);
  const doc = document.implementation.createHTMLDocument('');
  doc.body.innerHTML = html;
  return doc.body.textContent || '';
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/is-empty.js
/**
 * Recursively checks if an element is empty. An element is not empty if it
 * contains text or contains elements with attributes such as images.
 *
 * @param {Element} element The element to check.
 *
 * @return {boolean} Whether or not the element is empty.
 */
function isEmpty(element) {
  switch (element.nodeType) {
    case element.TEXT_NODE:
      // We cannot use \s since it includes special spaces which we want
      // to preserve.
      return /^[ \f\n\r\t\v\u00a0]*$/.test(element.nodeValue || '');
    case element.ELEMENT_NODE:
      if (element.hasAttributes()) {
        return false;
      } else if (!element.hasChildNodes()) {
        return true;
      }
      return /** @type {Element[]} */Array.from(element.childNodes).every(isEmpty);
    default:
      return true;
  }
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/phrasing-content.js
/**
 * All phrasing content elements.
 *
 * @see https://www.w3.org/TR/2011/WD-html5-20110525/content-models.html#phrasing-content-0
 */

/**
 * @typedef {Record<string,SemanticElementDefinition>} ContentSchema
 */

/**
 * @typedef SemanticElementDefinition
 * @property {string[]}      [attributes] Content attributes
 * @property {ContentSchema} [children]   Content attributes
 */

/**
 * All text-level semantic elements.
 *
 * @see https://html.spec.whatwg.org/multipage/text-level-semantics.html
 *
 * @type {ContentSchema}
 */
const textContentSchema = {
  strong: {},
  em: {},
  s: {},
  del: {},
  ins: {},
  a: {
    attributes: ['href', 'target', 'rel', 'id']
  },
  code: {},
  abbr: {
    attributes: ['title']
  },
  sub: {},
  sup: {},
  br: {},
  small: {},
  // To do: fix blockquote.
  // cite: {},
  q: {
    attributes: ['cite']
  },
  dfn: {
    attributes: ['title']
  },
  data: {
    attributes: ['value']
  },
  time: {
    attributes: ['datetime']
  },
  var: {},
  samp: {},
  kbd: {},
  i: {},
  b: {},
  u: {},
  mark: {},
  ruby: {},
  rt: {},
  rp: {},
  bdi: {
    attributes: ['dir']
  },
  bdo: {
    attributes: ['dir']
  },
  wbr: {},
  '#text': {}
};

// Recursion is needed.
// Possible: strong > em > strong.
// Impossible: strong > strong.
const excludedElements = ['#text', 'br'];
Object.keys(textContentSchema).filter(element => !excludedElements.includes(element)).forEach(tag => {
  const {
    [tag]: removedTag,
    ...restSchema
  } = textContentSchema;
  textContentSchema[tag].children = restSchema;
});

/**
 * Embedded content elements.
 *
 * @see https://www.w3.org/TR/2011/WD-html5-20110525/content-models.html#embedded-content-0
 *
 * @type {ContentSchema}
 */
const embeddedContentSchema = {
  audio: {
    attributes: ['src', 'preload', 'autoplay', 'mediagroup', 'loop', 'muted']
  },
  canvas: {
    attributes: ['width', 'height']
  },
  embed: {
    attributes: ['src', 'type', 'width', 'height']
  },
  img: {
    attributes: ['alt', 'src', 'srcset', 'usemap', 'ismap', 'width', 'height']
  },
  object: {
    attributes: ['data', 'type', 'name', 'usemap', 'form', 'width', 'height']
  },
  video: {
    attributes: ['src', 'poster', 'preload', 'playsinline', 'autoplay', 'mediagroup', 'loop', 'muted', 'controls', 'width', 'height']
  }
};

/**
 * Phrasing content elements.
 *
 * @see https://www.w3.org/TR/2011/WD-html5-20110525/content-models.html#phrasing-content-0
 */
const phrasingContentSchema = {
  ...textContentSchema,
  ...embeddedContentSchema
};

/**
 * Get schema of possible paths for phrasing content.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/Content_categories#Phrasing_content
 *
 * @param {string} [context] Set to "paste" to exclude invisible elements and
 *                           sensitive data.
 *
 * @return {Partial<ContentSchema>} Schema.
 */
function getPhrasingContentSchema(context) {
  if (context !== 'paste') {
    return phrasingContentSchema;
  }

  /**
   * @type {Partial<ContentSchema>}
   */
  const {
    u,
    // Used to mark misspelling. Shouldn't be pasted.
    abbr,
    // Invisible.
    data,
    // Invisible.
    time,
    // Invisible.
    wbr,
    // Invisible.
    bdi,
    // Invisible.
    bdo,
    // Invisible.
    ...remainingContentSchema
  } = {
    ...phrasingContentSchema,
    // We shouldn't paste potentially sensitive information which is not
    // visible to the user when pasted, so strip the attributes.
    ins: {
      children: phrasingContentSchema.ins.children
    },
    del: {
      children: phrasingContentSchema.del.children
    }
  };
  return remainingContentSchema;
}

/**
 * Find out whether or not the given node is phrasing content.
 *
 * @see https://developer.mozilla.org/en-US/docs/Web/Guide/HTML/Content_categories#Phrasing_content
 *
 * @param {Node} node The node to test.
 *
 * @return {boolean} True if phrasing content, false if not.
 */
function isPhrasingContent(node) {
  const tag = node.nodeName.toLowerCase();
  return getPhrasingContentSchema().hasOwnProperty(tag) || tag === 'span';
}

/**
 * @param {Node} node
 * @return {boolean} Node is text content
 */
function isTextContent(node) {
  const tag = node.nodeName.toLowerCase();
  return textContentSchema.hasOwnProperty(tag) || tag === 'span';
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/is-element.js
/* eslint-disable jsdoc/valid-types */
/**
 * @param {Node | null | undefined} node
 * @return {node is Element} True if node is an Element node
 */
function isElement(node) {
  /* eslint-enable jsdoc/valid-types */
  return !!node && node.nodeType === node.ELEMENT_NODE;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/clean-node-list.js
/**
 * Internal dependencies
 */






const noop = () => {};

/* eslint-disable jsdoc/valid-types */
/**
 * @typedef SchemaItem
 * @property {string[]}                            [attributes] Attributes.
 * @property {(string | RegExp)[]}                 [classes]    Classnames or RegExp to test against.
 * @property {'*' | { [tag: string]: SchemaItem }} [children]   Child schemas.
 * @property {string[]}                            [require]    Selectors to test required children against. Leave empty or undefined if there are no requirements.
 * @property {boolean}                             allowEmpty   Whether to allow nodes without children.
 * @property {(node: Node) => boolean}             [isMatch]    Function to test whether a node is a match. If left undefined any node will be assumed to match.
 */

/** @typedef {{ [tag: string]: SchemaItem }} Schema */
/* eslint-enable jsdoc/valid-types */

/**
 * Given a schema, unwraps or removes nodes, attributes and classes on a node
 * list.
 *
 * @param {NodeList} nodeList The nodeList to filter.
 * @param {Document} doc      The document of the nodeList.
 * @param {Schema}   schema   An array of functions that can mutate with the provided node.
 * @param {boolean}  inline   Whether to clean for inline mode.
 */
function cleanNodeList(nodeList, doc, schema, inline) {
  Array.from(nodeList).forEach(( /** @type {Node & { nextElementSibling?: unknown }} */node) => {
    const tag = node.nodeName.toLowerCase();

    // It's a valid child, if the tag exists in the schema without an isMatch
    // function, or with an isMatch function that matches the node.
    if (schema.hasOwnProperty(tag) && (!schema[tag].isMatch || schema[tag].isMatch?.(node))) {
      if (isElement(node)) {
        const {
          attributes = [],
          classes = [],
          children,
          require = [],
          allowEmpty
        } = schema[tag];

        // If the node is empty and it's supposed to have children,
        // remove the node.
        if (children && !allowEmpty && isEmpty(node)) {
          remove(node);
          return;
        }
        if (node.hasAttributes()) {
          // Strip invalid attributes.
          Array.from(node.attributes).forEach(({
            name
          }) => {
            if (name !== 'class' && !attributes.includes(name)) {
              node.removeAttribute(name);
            }
          });

          // Strip invalid classes.
          // In jsdom-jscore, 'node.classList' can be undefined.
          // TODO: Explore patching this in jsdom-jscore.
          if (node.classList && node.classList.length) {
            const mattchers = classes.map(item => {
              if (typeof item === 'string') {
                return ( /** @type {string} */className) => className === item;
              } else if (item instanceof RegExp) {
                return ( /** @type {string} */className) => item.test(className);
              }
              return noop;
            });
            Array.from(node.classList).forEach(name => {
              if (!mattchers.some(isMatch => isMatch(name))) {
                node.classList.remove(name);
              }
            });
            if (!node.classList.length) {
              node.removeAttribute('class');
            }
          }
        }
        if (node.hasChildNodes()) {
          // Do not filter any content.
          if (children === '*') {
            return;
          }

          // Continue if the node is supposed to have children.
          if (children) {
            // If a parent requires certain children, but it does
            // not have them, drop the parent and continue.
            if (require.length && !node.querySelector(require.join(','))) {
              cleanNodeList(node.childNodes, doc, schema, inline);
              unwrap(node);
              // If the node is at the top, phrasing content, and
              // contains children that are block content, unwrap
              // the node because it is invalid.
            } else if (node.parentNode && node.parentNode.nodeName === 'BODY' && isPhrasingContent(node)) {
              cleanNodeList(node.childNodes, doc, schema, inline);
              if (Array.from(node.childNodes).some(child => !isPhrasingContent(child))) {
                unwrap(node);
              }
            } else {
              cleanNodeList(node.childNodes, doc, children, inline);
            }
            // Remove children if the node is not supposed to have any.
          } else {
            while (node.firstChild) {
              remove(node.firstChild);
            }
          }
        }
      }
      // Invalid child. Continue with schema at the same place and unwrap.
    } else {
      cleanNodeList(node.childNodes, doc, schema, inline);

      // For inline mode, insert a line break when unwrapping nodes that
      // are not phrasing content.
      if (inline && !isPhrasingContent(node) && node.nextElementSibling) {
        insertAfter(doc.createElement('br'), node);
      }
      unwrap(node);
    }
  });
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/remove-invalid-html.js
/**
 * Internal dependencies
 */


/**
 * Given a schema, unwraps or removes nodes, attributes and classes on HTML.
 *
 * @param {string}                             HTML   The HTML to clean up.
 * @param {import('./clean-node-list').Schema} schema Schema for the HTML.
 * @param {boolean}                            inline Whether to clean for inline mode.
 *
 * @return {string} The cleaned up HTML.
 */
function removeInvalidHTML(HTML, schema, inline) {
  const doc = document.implementation.createHTMLDocument('');
  doc.body.innerHTML = HTML;
  cleanNodeList(doc.body.childNodes, doc, schema, inline);
  return doc.body.innerHTML;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/dom/index.js



























;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/data-transfer.js
/**
 * Gets all files from a DataTransfer object.
 *
 * @param {DataTransfer} dataTransfer DataTransfer object to inspect.
 *
 * @return {File[]} An array containing all files.
 */
function getFilesFromDataTransfer(dataTransfer) {
  const files = Array.from(dataTransfer.files);
  Array.from(dataTransfer.items).forEach(item => {
    const file = item.getAsFile();
    if (file && !files.find(({
      name,
      type,
      size
    }) => name === file.name && type === file.type && size === file.size)) {
      files.push(file);
    }
  });
  return files;
}

;// CONCATENATED MODULE: ./node_modules/@wordpress/dom/build-module/index.js
/**
 * Internal dependencies
 */



/**
 * Object grouping `focusable` and `tabbable` utils
 * under the keys with the same name.
 */
const build_module_focus = {
  focusable: focusable_namespaceObject,
  tabbable: tabbable_namespaceObject
};




(window.wp = window.wp || {}).dom = __webpack_exports__;
/******/ })()
;