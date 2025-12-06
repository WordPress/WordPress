"use strict";
var wp;
(wp ||= {}).dom = (() => {
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
    for (var name in all)
      __defProp(target, name, { get: all[name], enumerable: true });
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

  // package-external:@wordpress/deprecated
  var require_deprecated = __commonJS({
    "package-external:@wordpress/deprecated"(exports, module) {
      module.exports = window.wp.deprecated;
    }
  });

  // packages/dom/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    __unstableStripHTML: () => stripHTML,
    computeCaretRect: () => computeCaretRect,
    documentHasSelection: () => documentHasSelection,
    documentHasTextSelection: () => documentHasTextSelection,
    documentHasUncollapsedSelection: () => documentHasUncollapsedSelection,
    focus: () => focus,
    getFilesFromDataTransfer: () => getFilesFromDataTransfer,
    getOffsetParent: () => getOffsetParent,
    getPhrasingContentSchema: () => getPhrasingContentSchema,
    getRectangleFromRange: () => getRectangleFromRange,
    getScrollContainer: () => getScrollContainer,
    insertAfter: () => insertAfter,
    isEmpty: () => isEmpty,
    isEntirelySelected: () => isEntirelySelected,
    isFormElement: () => isFormElement,
    isHorizontalEdge: () => isHorizontalEdge,
    isNumberInput: () => isNumberInput,
    isPhrasingContent: () => isPhrasingContent,
    isRTL: () => isRTL,
    isSelectionForward: () => isSelectionForward,
    isTextContent: () => isTextContent,
    isTextField: () => isTextField,
    isVerticalEdge: () => isVerticalEdge,
    placeCaretAtHorizontalEdge: () => placeCaretAtHorizontalEdge,
    placeCaretAtVerticalEdge: () => placeCaretAtVerticalEdge,
    remove: () => remove,
    removeInvalidHTML: () => removeInvalidHTML,
    replace: () => replace,
    replaceTag: () => replaceTag,
    safeHTML: () => safeHTML,
    unwrap: () => unwrap,
    wrap: () => wrap
  });

  // packages/dom/build-module/focusable.js
  var focusable_exports = {};
  __export(focusable_exports, {
    find: () => find
  });
  function buildSelector(sequential) {
    return [
      sequential ? '[tabindex]:not([tabindex^="-"])' : "[tabindex]",
      "a[href]",
      "button:not([disabled])",
      'input:not([type="hidden"]):not([disabled])',
      "select:not([disabled])",
      "textarea:not([disabled])",
      'iframe:not([tabindex^="-"])',
      "object",
      "embed",
      "summary",
      "area[href]",
      "[contenteditable]:not([contenteditable=false])"
    ].join(",");
  }
  function isVisible(element) {
    return element.offsetWidth > 0 || element.offsetHeight > 0 || element.getClientRects().length > 0;
  }
  function isValidFocusableArea(element) {
    const map = element.closest("map[name]");
    if (!map) {
      return false;
    }
    const img = element.ownerDocument.querySelector(
      'img[usemap="#' + map.name + '"]'
    );
    return !!img && isVisible(img);
  }
  function find(context, { sequential = false } = {}) {
    const elements = context.querySelectorAll(buildSelector(sequential));
    return Array.from(elements).filter((element) => {
      if (!isVisible(element)) {
        return false;
      }
      const { nodeName } = element;
      if ("AREA" === nodeName) {
        return isValidFocusableArea(
          /** @type {HTMLAreaElement} */
          element
        );
      }
      return true;
    });
  }

  // packages/dom/build-module/tabbable.js
  var tabbable_exports = {};
  __export(tabbable_exports, {
    find: () => find2,
    findNext: () => findNext,
    findPrevious: () => findPrevious,
    isTabbableIndex: () => isTabbableIndex
  });
  function getTabIndex(element) {
    const tabIndex = element.getAttribute("tabindex");
    return tabIndex === null ? 0 : parseInt(tabIndex, 10);
  }
  function isTabbableIndex(element) {
    return getTabIndex(element) !== -1;
  }
  function createStatefulCollapseRadioGroup() {
    const CHOSEN_RADIO_BY_NAME = {};
    return function collapseRadioGroup(result, element) {
      const { nodeName, type, checked, name } = element;
      if (nodeName !== "INPUT" || type !== "radio" || !name) {
        return result.concat(element);
      }
      const hasChosen = CHOSEN_RADIO_BY_NAME.hasOwnProperty(name);
      const isChosen = checked || !hasChosen;
      if (!isChosen) {
        return result;
      }
      if (hasChosen) {
        const hadChosenElement = CHOSEN_RADIO_BY_NAME[name];
        result = result.filter((e) => e !== hadChosenElement);
      }
      CHOSEN_RADIO_BY_NAME[name] = element;
      return result.concat(element);
    };
  }
  function mapElementToObjectTabbable(element, index) {
    return { element, index };
  }
  function mapObjectTabbableToElement(object) {
    return object.element;
  }
  function compareObjectTabbables(a, b) {
    const aTabIndex = getTabIndex(a.element);
    const bTabIndex = getTabIndex(b.element);
    if (aTabIndex === bTabIndex) {
      return a.index - b.index;
    }
    return aTabIndex - bTabIndex;
  }
  function filterTabbable(focusables) {
    return focusables.filter(isTabbableIndex).map(mapElementToObjectTabbable).sort(compareObjectTabbables).map(mapObjectTabbableToElement).reduce(createStatefulCollapseRadioGroup(), []);
  }
  function find2(context) {
    return filterTabbable(find(context));
  }
  function findPrevious(element) {
    return filterTabbable(find(element.ownerDocument.body)).reverse().find(
      (focusable) => (
        // eslint-disable-next-line no-bitwise
        element.compareDocumentPosition(focusable) & element.DOCUMENT_POSITION_PRECEDING
      )
    );
  }
  function findNext(element) {
    return filterTabbable(find(element.ownerDocument.body)).find(
      (focusable) => (
        // eslint-disable-next-line no-bitwise
        element.compareDocumentPosition(focusable) & element.DOCUMENT_POSITION_FOLLOWING
      )
    );
  }

  // packages/dom/build-module/utils/assert-is-defined.js
  function assertIsDefined(val, name) {
    if (val === void 0 || val === null) {
      throw new Error(
        `Expected '${name}' to be defined, but received ${val}`
      );
    }
  }

  // packages/dom/build-module/dom/get-rectangle-from-range.js
  function getRectangleFromRange(range) {
    if (!range.collapsed) {
      const rects2 = Array.from(range.getClientRects());
      if (rects2.length === 1) {
        return rects2[0];
      }
      const filteredRects = rects2.filter(({ width }) => width > 1);
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
      for (const { top, bottom, left, right } of filteredRects) {
        if (top < furthestTop) {
          furthestTop = top;
        }
        if (bottom > furthestBottom) {
          furthestBottom = bottom;
        }
        if (left < furthestLeft) {
          furthestLeft = left;
        }
        if (right > furthestRight) {
          furthestRight = right;
        }
      }
      return new window.DOMRect(
        furthestLeft,
        furthestTop,
        furthestRight - furthestLeft,
        furthestBottom - furthestTop
      );
    }
    const { startContainer } = range;
    const { ownerDocument } = startContainer;
    if (startContainer.nodeName === "BR") {
      const { parentNode } = startContainer;
      assertIsDefined(parentNode, "parentNode");
      const index = (
        /** @type {Node[]} */
        Array.from(parentNode.childNodes).indexOf(startContainer)
      );
      assertIsDefined(ownerDocument, "ownerDocument");
      range = ownerDocument.createRange();
      range.setStart(parentNode, index);
      range.setEnd(parentNode, index);
    }
    const rects = range.getClientRects();
    if (rects.length > 1) {
      return null;
    }
    let rect = rects[0];
    if (!rect || rect.height === 0) {
      assertIsDefined(ownerDocument, "ownerDocument");
      const padNode = ownerDocument.createTextNode("\u200B");
      range = range.cloneRange();
      range.insertNode(padNode);
      rect = range.getClientRects()[0];
      assertIsDefined(padNode.parentNode, "padNode.parentNode");
      padNode.parentNode.removeChild(padNode);
    }
    return rect;
  }

  // packages/dom/build-module/dom/compute-caret-rect.js
  function computeCaretRect(win) {
    const selection = win.getSelection();
    assertIsDefined(selection, "selection");
    const range = selection.rangeCount ? selection.getRangeAt(0) : null;
    if (!range) {
      return null;
    }
    return getRectangleFromRange(range);
  }

  // packages/dom/build-module/dom/document-has-text-selection.js
  function documentHasTextSelection(doc) {
    assertIsDefined(doc.defaultView, "doc.defaultView");
    const selection = doc.defaultView.getSelection();
    assertIsDefined(selection, "selection");
    const range = selection.rangeCount ? selection.getRangeAt(0) : null;
    return !!range && !range.collapsed;
  }

  // packages/dom/build-module/dom/is-html-input-element.js
  function isHTMLInputElement(node) {
    return node?.nodeName === "INPUT";
  }

  // packages/dom/build-module/dom/is-text-field.js
  function isTextField(node) {
    const nonTextInputs = [
      "button",
      "checkbox",
      "hidden",
      "file",
      "radio",
      "image",
      "range",
      "reset",
      "submit",
      "number",
      "email",
      "time"
    ];
    return isHTMLInputElement(node) && node.type && !nonTextInputs.includes(node.type) || node.nodeName === "TEXTAREA" || /** @type {HTMLElement} */
    node.contentEditable === "true";
  }

  // packages/dom/build-module/dom/input-field-has-uncollapsed-selection.js
  function inputFieldHasUncollapsedSelection(element) {
    if (!isHTMLInputElement(element) && !isTextField(element)) {
      return false;
    }
    try {
      const { selectionStart, selectionEnd } = (
        /** @type {HTMLInputElement | HTMLTextAreaElement} */
        element
      );
      return (
        // `null` means the input type doesn't implement selection, thus we
        // cannot determine whether the selection is collapsed, so we
        // default to true.
        selectionStart === null || // when not null, compare the two points
        selectionStart !== selectionEnd
      );
    } catch (error) {
      return true;
    }
  }

  // packages/dom/build-module/dom/document-has-uncollapsed-selection.js
  function documentHasUncollapsedSelection(doc) {
    return documentHasTextSelection(doc) || !!doc.activeElement && inputFieldHasUncollapsedSelection(doc.activeElement);
  }

  // packages/dom/build-module/dom/document-has-selection.js
  function documentHasSelection(doc) {
    return !!doc.activeElement && (isHTMLInputElement(doc.activeElement) || isTextField(doc.activeElement) || documentHasTextSelection(doc));
  }

  // packages/dom/build-module/dom/get-computed-style.js
  function getComputedStyle(element) {
    assertIsDefined(
      element.ownerDocument.defaultView,
      "element.ownerDocument.defaultView"
    );
    return element.ownerDocument.defaultView.getComputedStyle(element);
  }

  // packages/dom/build-module/dom/get-scroll-container.js
  function getScrollContainer(node, direction = "vertical") {
    if (!node) {
      return void 0;
    }
    if (direction === "vertical" || direction === "all") {
      if (node.scrollHeight > node.clientHeight) {
        const { overflowY } = getComputedStyle(node);
        if (/(auto|scroll)/.test(overflowY)) {
          return node;
        }
      }
    }
    if (direction === "horizontal" || direction === "all") {
      if (node.scrollWidth > node.clientWidth) {
        const { overflowX } = getComputedStyle(node);
        if (/(auto|scroll)/.test(overflowX)) {
          return node;
        }
      }
    }
    if (node.ownerDocument === node.parentNode) {
      return node;
    }
    return getScrollContainer(
      /** @type {Element} */
      node.parentNode,
      direction
    );
  }

  // packages/dom/build-module/dom/get-offset-parent.js
  function getOffsetParent(node) {
    let closestElement;
    while (closestElement = /** @type {Node} */
    node.parentNode) {
      if (closestElement.nodeType === closestElement.ELEMENT_NODE) {
        break;
      }
    }
    if (!closestElement) {
      return null;
    }
    if (getComputedStyle(
      /** @type {Element} */
      closestElement
    ).position !== "static") {
      return closestElement;
    }
    return (
      /** @type {Node & { offsetParent: Node }} */
      closestElement.offsetParent
    );
  }

  // packages/dom/build-module/dom/is-input-or-text-area.js
  function isInputOrTextArea(element) {
    return element.tagName === "INPUT" || element.tagName === "TEXTAREA";
  }

  // packages/dom/build-module/dom/is-entirely-selected.js
  function isEntirelySelected(element) {
    if (isInputOrTextArea(element)) {
      return element.selectionStart === 0 && element.value.length === element.selectionEnd;
    }
    if (!element.isContentEditable) {
      return true;
    }
    const { ownerDocument } = element;
    const { defaultView } = ownerDocument;
    assertIsDefined(defaultView, "defaultView");
    const selection = defaultView.getSelection();
    assertIsDefined(selection, "selection");
    const range = selection.rangeCount ? selection.getRangeAt(0) : null;
    if (!range) {
      return true;
    }
    const { startContainer, endContainer, startOffset, endOffset } = range;
    if (startContainer === element && endContainer === element && startOffset === 0 && endOffset === element.childNodes.length) {
      return true;
    }
    const lastChild = element.lastChild;
    assertIsDefined(lastChild, "lastChild");
    const endContainerContentLength = endContainer.nodeType === endContainer.TEXT_NODE ? (
      /** @type {Text} */
      endContainer.data.length
    ) : endContainer.childNodes.length;
    return isDeepChild(startContainer, element, "firstChild") && isDeepChild(endContainer, element, "lastChild") && startOffset === 0 && endOffset === endContainerContentLength;
  }
  function isDeepChild(query, container, propName) {
    let candidate = container;
    do {
      if (query === candidate) {
        return true;
      }
      candidate = candidate[propName];
    } while (candidate);
    return false;
  }

  // packages/dom/build-module/dom/is-form-element.js
  function isFormElement(element) {
    if (!element) {
      return false;
    }
    const { tagName } = element;
    const checkForInputTextarea = isInputOrTextArea(element);
    return checkForInputTextarea || tagName === "BUTTON" || tagName === "SELECT";
  }

  // packages/dom/build-module/dom/is-rtl.js
  function isRTL(element) {
    return getComputedStyle(element).direction === "rtl";
  }

  // packages/dom/build-module/dom/get-range-height.js
  function getRangeHeight(range) {
    const rects = Array.from(range.getClientRects());
    if (!rects.length) {
      return;
    }
    const highestTop = Math.min(...rects.map(({ top }) => top));
    const lowestBottom = Math.max(...rects.map(({ bottom }) => bottom));
    return lowestBottom - highestTop;
  }

  // packages/dom/build-module/dom/is-selection-forward.js
  function isSelectionForward(selection) {
    const { anchorNode, focusNode, anchorOffset, focusOffset } = selection;
    assertIsDefined(anchorNode, "anchorNode");
    assertIsDefined(focusNode, "focusNode");
    const position = anchorNode.compareDocumentPosition(focusNode);
    if (position & anchorNode.DOCUMENT_POSITION_PRECEDING) {
      return false;
    }
    if (position & anchorNode.DOCUMENT_POSITION_FOLLOWING) {
      return true;
    }
    if (position === 0) {
      return anchorOffset <= focusOffset;
    }
    return true;
  }

  // packages/dom/build-module/dom/caret-range-from-point.js
  function caretRangeFromPoint(doc, x, y) {
    if (doc.caretRangeFromPoint) {
      return doc.caretRangeFromPoint(x, y);
    }
    if (!doc.caretPositionFromPoint) {
      return null;
    }
    const point = doc.caretPositionFromPoint(x, y);
    if (!point) {
      return null;
    }
    const range = doc.createRange();
    range.setStart(point.offsetNode, point.offset);
    range.collapse(true);
    return range;
  }

  // packages/dom/build-module/dom/hidden-caret-range-from-point.js
  function hiddenCaretRangeFromPoint(doc, x, y, container) {
    const originalZIndex = container.style.zIndex;
    const originalPosition = container.style.position;
    const { position = "static" } = getComputedStyle(container);
    if (position === "static") {
      container.style.position = "relative";
    }
    container.style.zIndex = "10000";
    const range = caretRangeFromPoint(doc, x, y);
    container.style.zIndex = originalZIndex;
    container.style.position = originalPosition;
    return range;
  }

  // packages/dom/build-module/dom/scroll-if-no-range.js
  function scrollIfNoRange(container, alignToTop, callback) {
    let range = callback();
    if (!range || !range.startContainer || !container.contains(range.startContainer)) {
      container.scrollIntoView(alignToTop);
      range = callback();
      if (!range || !range.startContainer || !container.contains(range.startContainer)) {
        return null;
      }
    }
    return range;
  }

  // packages/dom/build-module/dom/is-edge.js
  function isEdge(container, isReverse, onlyVertical = false) {
    if (isInputOrTextArea(container) && typeof container.selectionStart === "number") {
      if (container.selectionStart !== container.selectionEnd) {
        return false;
      }
      if (isReverse) {
        return container.selectionStart === 0;
      }
      return container.value.length === container.selectionStart;
    }
    if (!container.isContentEditable) {
      return true;
    }
    const { ownerDocument } = container;
    const { defaultView } = ownerDocument;
    assertIsDefined(defaultView, "defaultView");
    const selection = defaultView.getSelection();
    if (!selection || !selection.rangeCount) {
      return false;
    }
    const range = selection.getRangeAt(0);
    const collapsedRange = range.cloneRange();
    const isForward = isSelectionForward(selection);
    const isCollapsed = selection.isCollapsed;
    if (!isCollapsed) {
      collapsedRange.collapse(!isForward);
    }
    const collapsedRangeRect = getRectangleFromRange(collapsedRange);
    const rangeRect = getRectangleFromRange(range);
    if (!collapsedRangeRect || !rangeRect) {
      return false;
    }
    const rangeHeight = getRangeHeight(range);
    if (!isCollapsed && rangeHeight && rangeHeight > collapsedRangeRect.height && isForward === isReverse) {
      return false;
    }
    const isReverseDir = isRTL(container) ? !isReverse : isReverse;
    const containerRect = container.getBoundingClientRect();
    const x = isReverseDir ? containerRect.left + 1 : containerRect.right - 1;
    const y = isReverse ? containerRect.top + 1 : containerRect.bottom - 1;
    const testRange = scrollIfNoRange(
      container,
      isReverse,
      () => hiddenCaretRangeFromPoint(ownerDocument, x, y, container)
    );
    if (!testRange) {
      return false;
    }
    const testRect = getRectangleFromRange(testRange);
    if (!testRect) {
      return false;
    }
    const verticalSide = isReverse ? "top" : "bottom";
    const horizontalSide = isReverseDir ? "left" : "right";
    const verticalDiff = testRect[verticalSide] - rangeRect[verticalSide];
    const horizontalDiff = testRect[horizontalSide] - collapsedRangeRect[horizontalSide];
    const hasVerticalDiff = Math.abs(verticalDiff) <= 1;
    const hasHorizontalDiff = Math.abs(horizontalDiff) <= 1;
    return onlyVertical ? hasVerticalDiff : hasVerticalDiff && hasHorizontalDiff;
  }

  // packages/dom/build-module/dom/is-horizontal-edge.js
  function isHorizontalEdge(container, isReverse) {
    return isEdge(container, isReverse);
  }

  // packages/dom/build-module/dom/is-number-input.js
  var import_deprecated = __toESM(require_deprecated());
  function isNumberInput(node) {
    (0, import_deprecated.default)("wp.dom.isNumberInput", {
      since: "6.1",
      version: "6.5"
    });
    return isHTMLInputElement(node) && node.type === "number" && !isNaN(node.valueAsNumber);
  }

  // packages/dom/build-module/dom/is-vertical-edge.js
  function isVerticalEdge(container, isReverse) {
    return isEdge(container, isReverse, true);
  }

  // packages/dom/build-module/dom/place-caret-at-edge.js
  function getRange(container, isReverse, x) {
    const { ownerDocument } = container;
    const isReverseDir = isRTL(container) ? !isReverse : isReverse;
    const containerRect = container.getBoundingClientRect();
    if (x === void 0) {
      x = isReverse ? containerRect.right - 1 : containerRect.left + 1;
    } else if (x <= containerRect.left) {
      x = containerRect.left + 1;
    } else if (x >= containerRect.right) {
      x = containerRect.right - 1;
    }
    const y = isReverseDir ? containerRect.bottom - 1 : containerRect.top + 1;
    return hiddenCaretRangeFromPoint(ownerDocument, x, y, container);
  }
  function placeCaretAtEdge(container, isReverse, x) {
    if (!container) {
      return;
    }
    container.focus();
    if (isInputOrTextArea(container)) {
      if (typeof container.selectionStart !== "number") {
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
    const range = scrollIfNoRange(
      container,
      isReverse,
      () => getRange(container, isReverse, x)
    );
    if (!range) {
      return;
    }
    const { ownerDocument } = container;
    const { defaultView } = ownerDocument;
    assertIsDefined(defaultView, "defaultView");
    const selection = defaultView.getSelection();
    assertIsDefined(selection, "selection");
    selection.removeAllRanges();
    selection.addRange(range);
  }

  // packages/dom/build-module/dom/place-caret-at-horizontal-edge.js
  function placeCaretAtHorizontalEdge(container, isReverse) {
    return placeCaretAtEdge(container, isReverse, void 0);
  }

  // packages/dom/build-module/dom/place-caret-at-vertical-edge.js
  function placeCaretAtVerticalEdge(container, isReverse, rect) {
    return placeCaretAtEdge(container, isReverse, rect?.left);
  }

  // packages/dom/build-module/dom/insert-after.js
  function insertAfter(newNode, referenceNode) {
    assertIsDefined(referenceNode.parentNode, "referenceNode.parentNode");
    referenceNode.parentNode.insertBefore(newNode, referenceNode.nextSibling);
  }

  // packages/dom/build-module/dom/remove.js
  function remove(node) {
    assertIsDefined(node.parentNode, "node.parentNode");
    node.parentNode.removeChild(node);
  }

  // packages/dom/build-module/dom/replace.js
  function replace(processedNode, newNode) {
    assertIsDefined(processedNode.parentNode, "processedNode.parentNode");
    insertAfter(newNode, processedNode.parentNode);
    remove(processedNode);
  }

  // packages/dom/build-module/dom/unwrap.js
  function unwrap(node) {
    const parent = node.parentNode;
    assertIsDefined(parent, "node.parentNode");
    while (node.firstChild) {
      parent.insertBefore(node.firstChild, node);
    }
    parent.removeChild(node);
  }

  // packages/dom/build-module/dom/replace-tag.js
  function replaceTag(node, tagName) {
    const newNode = node.ownerDocument.createElement(tagName);
    while (node.firstChild) {
      newNode.appendChild(node.firstChild);
    }
    assertIsDefined(node.parentNode, "node.parentNode");
    node.parentNode.replaceChild(newNode, node);
    return newNode;
  }

  // packages/dom/build-module/dom/wrap.js
  function wrap(newNode, referenceNode) {
    assertIsDefined(referenceNode.parentNode, "referenceNode.parentNode");
    referenceNode.parentNode.insertBefore(newNode, referenceNode);
    newNode.appendChild(referenceNode);
  }

  // packages/dom/build-module/dom/safe-html.js
  function safeHTML(html) {
    const { body } = document.implementation.createHTMLDocument("");
    body.innerHTML = html;
    const elements = body.getElementsByTagName("*");
    let elementIndex = elements.length;
    while (elementIndex--) {
      const element = elements[elementIndex];
      if (element.tagName === "SCRIPT") {
        remove(element);
      } else {
        let attributeIndex = element.attributes.length;
        while (attributeIndex--) {
          const { name: key } = element.attributes[attributeIndex];
          if (key.startsWith("on")) {
            element.removeAttribute(key);
          }
        }
      }
    }
    return body.innerHTML;
  }

  // packages/dom/build-module/dom/strip-html.js
  function stripHTML(html) {
    html = safeHTML(html);
    const doc = document.implementation.createHTMLDocument("");
    doc.body.innerHTML = html;
    return doc.body.textContent || "";
  }

  // packages/dom/build-module/dom/is-empty.js
  function isEmpty(element) {
    switch (element.nodeType) {
      case element.TEXT_NODE:
        return /^[ \f\n\r\t\v\u00a0]*$/.test(element.nodeValue || "");
      case element.ELEMENT_NODE:
        if (element.hasAttributes()) {
          return false;
        } else if (!element.hasChildNodes()) {
          return true;
        }
        return (
          /** @type {Element[]} */
          Array.from(element.childNodes).every(isEmpty)
        );
      default:
        return true;
    }
  }

  // packages/dom/build-module/phrasing-content.js
  var textContentSchema = {
    strong: {},
    em: {},
    s: {},
    del: {},
    ins: {},
    a: { attributes: ["href", "target", "rel", "id"] },
    code: {},
    abbr: { attributes: ["title"] },
    sub: {},
    sup: {},
    br: {},
    small: {},
    // To do: fix blockquote.
    // cite: {},
    q: { attributes: ["cite"] },
    dfn: { attributes: ["title"] },
    data: { attributes: ["value"] },
    time: { attributes: ["datetime"] },
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
    bdi: { attributes: ["dir"] },
    bdo: { attributes: ["dir"] },
    wbr: {},
    "#text": {}
  };
  var excludedElements = ["#text", "br"];
  Object.keys(textContentSchema).filter((element) => !excludedElements.includes(element)).forEach((tag) => {
    const { [tag]: removedTag, ...restSchema } = textContentSchema;
    textContentSchema[tag].children = restSchema;
  });
  var embeddedContentSchema = {
    audio: {
      attributes: [
        "src",
        "preload",
        "autoplay",
        "mediagroup",
        "loop",
        "muted"
      ]
    },
    canvas: { attributes: ["width", "height"] },
    embed: { attributes: ["src", "type", "width", "height"] },
    img: {
      attributes: [
        "alt",
        "src",
        "srcset",
        "usemap",
        "ismap",
        "width",
        "height"
      ]
    },
    object: {
      attributes: [
        "data",
        "type",
        "name",
        "usemap",
        "form",
        "width",
        "height"
      ]
    },
    video: {
      attributes: [
        "src",
        "poster",
        "preload",
        "playsinline",
        "autoplay",
        "mediagroup",
        "loop",
        "muted",
        "controls",
        "width",
        "height"
      ]
    },
    math: {
      attributes: ["display", "xmlns"],
      children: "*"
    }
  };
  var phrasingContentSchema = {
    ...textContentSchema,
    ...embeddedContentSchema
  };
  function getPhrasingContentSchema(context) {
    if (context !== "paste") {
      return phrasingContentSchema;
    }
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
      ins: { children: phrasingContentSchema.ins.children },
      del: { children: phrasingContentSchema.del.children }
    };
    return remainingContentSchema;
  }
  function isPhrasingContent(node) {
    const tag = node.nodeName.toLowerCase();
    return getPhrasingContentSchema().hasOwnProperty(tag) || tag === "span";
  }
  function isTextContent(node) {
    const tag = node.nodeName.toLowerCase();
    return textContentSchema.hasOwnProperty(tag) || tag === "span";
  }

  // packages/dom/build-module/dom/is-element.js
  function isElement(node) {
    return !!node && node.nodeType === node.ELEMENT_NODE;
  }

  // packages/dom/build-module/dom/clean-node-list.js
  var noop = () => {
  };
  function cleanNodeList(nodeList, doc, schema, inline) {
    Array.from(nodeList).forEach(
      (node) => {
        const tag = node.nodeName.toLowerCase();
        if (schema.hasOwnProperty(tag) && (!schema[tag].isMatch || schema[tag].isMatch?.(node))) {
          if (isElement(node)) {
            const {
              attributes = [],
              classes = [],
              children,
              require: require2 = [],
              allowEmpty
            } = schema[tag];
            if (children && !allowEmpty && isEmpty(node)) {
              remove(node);
              return;
            }
            if (node.hasAttributes()) {
              Array.from(node.attributes).forEach(({ name }) => {
                if (name !== "class" && !attributes.includes(name)) {
                  node.removeAttribute(name);
                }
              });
              if (node.classList && node.classList.length) {
                const mattchers = classes.map((item) => {
                  if (item === "*") {
                    return () => true;
                  } else if (typeof item === "string") {
                    return (className) => className === item;
                  } else if (item instanceof RegExp) {
                    return (className) => item.test(className);
                  }
                  return noop;
                });
                Array.from(node.classList).forEach((name) => {
                  if (!mattchers.some(
                    (isMatch) => isMatch(name)
                  )) {
                    node.classList.remove(name);
                  }
                });
                if (!node.classList.length) {
                  node.removeAttribute("class");
                }
              }
            }
            if (node.hasChildNodes()) {
              if (children === "*") {
                return;
              }
              if (children) {
                if (require2.length && !node.querySelector(require2.join(","))) {
                  cleanNodeList(
                    node.childNodes,
                    doc,
                    schema,
                    inline
                  );
                  unwrap(node);
                } else if (node.parentNode && node.parentNode.nodeName === "BODY" && isPhrasingContent(node)) {
                  cleanNodeList(
                    node.childNodes,
                    doc,
                    schema,
                    inline
                  );
                  if (Array.from(node.childNodes).some(
                    (child) => !isPhrasingContent(child)
                  )) {
                    unwrap(node);
                  }
                } else {
                  cleanNodeList(
                    node.childNodes,
                    doc,
                    children,
                    inline
                  );
                }
              } else {
                while (node.firstChild) {
                  remove(node.firstChild);
                }
              }
            }
          }
        } else {
          cleanNodeList(node.childNodes, doc, schema, inline);
          if (inline && !isPhrasingContent(node) && node.nextElementSibling) {
            insertAfter(doc.createElement("br"), node);
          }
          unwrap(node);
        }
      }
    );
  }

  // packages/dom/build-module/dom/remove-invalid-html.js
  function removeInvalidHTML(HTML, schema, inline) {
    const doc = document.implementation.createHTMLDocument("");
    doc.body.innerHTML = HTML;
    cleanNodeList(doc.body.childNodes, doc, schema, inline);
    return doc.body.innerHTML;
  }

  // packages/dom/build-module/data-transfer.js
  function getFilesFromDataTransfer(dataTransfer) {
    const files = Array.from(dataTransfer.files);
    Array.from(dataTransfer.items).forEach((item) => {
      const file = item.getAsFile();
      if (file && !files.find(
        ({ name, type, size }) => name === file.name && type === file.type && size === file.size
      )) {
        files.push(file);
      }
    });
    return files;
  }

  // packages/dom/build-module/index.js
  var focus = { focusable: focusable_exports, tabbable: tabbable_exports };
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
