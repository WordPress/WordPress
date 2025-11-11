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
  RichTextData: () => (/* reexport */ RichTextData),
  __experimentalRichText: () => (/* reexport */ __experimentalRichText),
  __unstableCreateElement: () => (/* reexport */ createElement),
  __unstableToDom: () => (/* reexport */ toDom),
  __unstableUseRichText: () => (/* reexport */ useRichText),
  applyFormat: () => (/* reexport */ applyFormat),
  concat: () => (/* reexport */ concat),
  create: () => (/* reexport */ create),
  getActiveFormat: () => (/* reexport */ getActiveFormat),
  getActiveFormats: () => (/* reexport */ getActiveFormats),
  getActiveObject: () => (/* reexport */ getActiveObject),
  getTextContent: () => (/* reexport */ getTextContent),
  insert: () => (/* reexport */ insert),
  insertObject: () => (/* reexport */ insertObject),
  isCollapsed: () => (/* reexport */ isCollapsed),
  isEmpty: () => (/* reexport */ isEmpty),
  join: () => (/* reexport */ join),
  registerFormatType: () => (/* reexport */ registerFormatType),
  remove: () => (/* reexport */ remove_remove),
  removeFormat: () => (/* reexport */ removeFormat),
  replace: () => (/* reexport */ replace_replace),
  slice: () => (/* reexport */ slice),
  split: () => (/* reexport */ split),
  store: () => (/* reexport */ store),
  toHTMLString: () => (/* reexport */ toHTMLString),
  toggleFormat: () => (/* reexport */ toggleFormat),
  unregisterFormatType: () => (/* reexport */ unregisterFormatType),
  useAnchor: () => (/* reexport */ useAnchor),
  useAnchorRef: () => (/* reexport */ useAnchorRef)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/rich-text/build-module/store/selectors.js
var selectors_namespaceObject = {};
__webpack_require__.r(selectors_namespaceObject);
__webpack_require__.d(selectors_namespaceObject, {
  getFormatType: () => (getFormatType),
  getFormatTypeForBareElement: () => (getFormatTypeForBareElement),
  getFormatTypeForClassName: () => (getFormatTypeForClassName),
  getFormatTypes: () => (getFormatTypes)
});

// NAMESPACE OBJECT: ./node_modules/@wordpress/rich-text/build-module/store/actions.js
var actions_namespaceObject = {};
__webpack_require__.r(actions_namespaceObject);
__webpack_require__.d(actions_namespaceObject, {
  addFormatTypes: () => (addFormatTypes),
  removeFormatTypes: () => (removeFormatTypes)
});

;// external ["wp","data"]
const external_wp_data_namespaceObject = window["wp"]["data"];
;// ./node_modules/@wordpress/rich-text/build-module/store/reducer.js

function formatTypes(state = {}, action) {
  switch (action.type) {
    case "ADD_FORMAT_TYPES":
      return {
        ...state,
        // Key format types by their name.
        ...action.formatTypes.reduce(
          (newFormatTypes, type) => ({
            ...newFormatTypes,
            [type.name]: type
          }),
          {}
        )
      };
    case "REMOVE_FORMAT_TYPES":
      return Object.fromEntries(
        Object.entries(state).filter(
          ([key]) => !action.names.includes(key)
        )
      );
  }
  return state;
}
var reducer_default = (0,external_wp_data_namespaceObject.combineReducers)({ formatTypes });


;// ./node_modules/@wordpress/rich-text/build-module/store/selectors.js

const getFormatTypes = (0,external_wp_data_namespaceObject.createSelector)(
  (state) => Object.values(state.formatTypes),
  (state) => [state.formatTypes]
);
function getFormatType(state, name) {
  return state.formatTypes[name];
}
function getFormatTypeForBareElement(state, bareElementTagName) {
  const formatTypes = getFormatTypes(state);
  return formatTypes.find(({ className, tagName }) => {
    return className === null && bareElementTagName === tagName;
  }) || formatTypes.find(({ className, tagName }) => {
    return className === null && "*" === tagName;
  });
}
function getFormatTypeForClassName(state, elementClassName) {
  return getFormatTypes(state).find(({ className }) => {
    if (className === null) {
      return false;
    }
    return ` ${elementClassName} `.indexOf(` ${className} `) >= 0;
  });
}


;// ./node_modules/@wordpress/rich-text/build-module/store/actions.js
function addFormatTypes(formatTypes) {
  return {
    type: "ADD_FORMAT_TYPES",
    formatTypes: Array.isArray(formatTypes) ? formatTypes : [formatTypes]
  };
}
function removeFormatTypes(names) {
  return {
    type: "REMOVE_FORMAT_TYPES",
    names: Array.isArray(names) ? names : [names]
  };
}


;// ./node_modules/@wordpress/rich-text/build-module/store/index.js




const STORE_NAME = "core/rich-text";
const store = (0,external_wp_data_namespaceObject.createReduxStore)(STORE_NAME, {
  reducer: reducer_default,
  selectors: selectors_namespaceObject,
  actions: actions_namespaceObject
});
(0,external_wp_data_namespaceObject.register)(store);


;// ./node_modules/@wordpress/rich-text/build-module/is-format-equal.js
function isFormatEqual(format1, format2) {
  if (format1 === format2) {
    return true;
  }
  if (!format1 || !format2) {
    return false;
  }
  if (format1.type !== format2.type) {
    return false;
  }
  const attributes1 = format1.attributes;
  const attributes2 = format2.attributes;
  if (attributes1 === attributes2) {
    return true;
  }
  if (!attributes1 || !attributes2) {
    return false;
  }
  const keys1 = Object.keys(attributes1);
  const keys2 = Object.keys(attributes2);
  if (keys1.length !== keys2.length) {
    return false;
  }
  const length = keys1.length;
  for (let i = 0; i < length; i++) {
    const name = keys1[i];
    if (attributes1[name] !== attributes2[name]) {
      return false;
    }
  }
  return true;
}


;// ./node_modules/@wordpress/rich-text/build-module/normalise-formats.js

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
  return {
    ...value,
    formats: newFormats
  };
}


;// ./node_modules/@wordpress/rich-text/build-module/apply-format.js

function replace(array, index, value) {
  array = array.slice();
  array[index] = value;
  return array;
}
function applyFormat(value, format, startIndex = value.start, endIndex = value.end) {
  const { formats, activeFormats } = value;
  const newFormats = formats.slice();
  if (startIndex === endIndex) {
    const startFormat = newFormats[startIndex]?.find(
      ({ type }) => type === format.type
    );
    if (startFormat) {
      const index = newFormats[startIndex].indexOf(startFormat);
      while (newFormats[startIndex] && newFormats[startIndex][index] === startFormat) {
        newFormats[startIndex] = replace(
          newFormats[startIndex],
          index,
          format
        );
        startIndex--;
      }
      endIndex++;
      while (newFormats[endIndex] && newFormats[endIndex][index] === startFormat) {
        newFormats[endIndex] = replace(
          newFormats[endIndex],
          index,
          format
        );
        endIndex++;
      }
    }
  } else {
    let position = Infinity;
    for (let index = startIndex; index < endIndex; index++) {
      if (newFormats[index]) {
        newFormats[index] = newFormats[index].filter(
          ({ type }) => type !== format.type
        );
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
  return normaliseFormats({
    ...value,
    formats: newFormats,
    // Always revise active formats. This serves as a placeholder for new
    // inputs with the format so new input appears with the format applied,
    // and ensures a format of the same type uses the latest values.
    activeFormats: [
      ...activeFormats?.filter(
        ({ type }) => type !== format.type
      ) || [],
      format
    ]
  });
}


;// ./node_modules/@wordpress/rich-text/build-module/create-element.js
function createElement({ implementation }, html) {
  if (!createElement.body) {
    createElement.body = implementation.createHTMLDocument("").body;
  }
  createElement.body.innerHTML = html;
  return createElement.body;
}


;// ./node_modules/@wordpress/rich-text/build-module/special-characters.js
const OBJECT_REPLACEMENT_CHARACTER = "\uFFFC";
const ZWNBSP = "\uFEFF";


;// external ["wp","escapeHtml"]
const external_wp_escapeHtml_namespaceObject = window["wp"]["escapeHtml"];
;// ./node_modules/@wordpress/rich-text/build-module/get-active-formats.js

function getActiveFormats(value, EMPTY_ACTIVE_FORMATS = []) {
  const { formats, start, end, activeFormats } = value;
  if (start === void 0) {
    return EMPTY_ACTIVE_FORMATS;
  }
  if (start === end) {
    if (activeFormats) {
      return activeFormats;
    }
    const formatsBefore = formats[start - 1] || EMPTY_ACTIVE_FORMATS;
    const formatsAfter = formats[start] || EMPTY_ACTIVE_FORMATS;
    if (formatsBefore.length < formatsAfter.length) {
      return formatsBefore;
    }
    return formatsAfter;
  }
  if (!formats[start]) {
    return EMPTY_ACTIVE_FORMATS;
  }
  const selectedFormats = formats.slice(start, end);
  const _activeFormats = [...selectedFormats[0]];
  let i = selectedFormats.length;
  while (i--) {
    const formatsAtIndex = selectedFormats[i];
    if (!formatsAtIndex) {
      return EMPTY_ACTIVE_FORMATS;
    }
    let ii = _activeFormats.length;
    while (ii--) {
      const format = _activeFormats[ii];
      if (!formatsAtIndex.find(
        (_format) => isFormatEqual(format, _format)
      )) {
        _activeFormats.splice(ii, 1);
      }
    }
    if (_activeFormats.length === 0) {
      return EMPTY_ACTIVE_FORMATS;
    }
  }
  return _activeFormats || EMPTY_ACTIVE_FORMATS;
}


;// ./node_modules/@wordpress/rich-text/build-module/get-format-type.js


function get_format_type_getFormatType(name) {
  return (0,external_wp_data_namespaceObject.select)(store).getFormatType(name);
}


;// ./node_modules/@wordpress/rich-text/build-module/to-tree.js



function restoreOnAttributes(attributes, isEditableTree) {
  if (isEditableTree) {
    return attributes;
  }
  const newAttributes = {};
  for (const key in attributes) {
    let newKey = key;
    if (key.startsWith("data-disable-rich-text-")) {
      newKey = key.slice("data-disable-rich-text-".length);
    }
    newAttributes[newKey] = attributes[key];
  }
  return newAttributes;
}
function fromFormat({
  type,
  tagName,
  attributes,
  unregisteredAttributes,
  object,
  boundaryClass,
  isEditableTree
}) {
  const formatType = get_format_type_getFormatType(type);
  let elementAttributes = {};
  if (boundaryClass && isEditableTree) {
    elementAttributes["data-rich-text-format-boundary"] = "true";
  }
  if (!formatType) {
    if (attributes) {
      elementAttributes = { ...attributes, ...elementAttributes };
    }
    return {
      type,
      attributes: restoreOnAttributes(
        elementAttributes,
        isEditableTree
      ),
      object
    };
  }
  elementAttributes = { ...unregisteredAttributes, ...elementAttributes };
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
    type: tagName || formatType.tagName,
    object: formatType.object,
    attributes: restoreOnAttributes(elementAttributes, isEditableTree)
  };
}
function isEqualUntil(a, b, index) {
  do {
    if (a[index] !== b[index]) {
      return false;
    }
  } while (index--);
  return true;
}
function toTree({
  value,
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
}) {
  const { formats, replacements, text, start, end } = value;
  const formatsLength = formats.length + 1;
  const tree = createEmpty();
  const activeFormats = getActiveFormats(value);
  const deepestActiveFormat = activeFormats[activeFormats.length - 1];
  let lastCharacterFormats;
  let lastCharacter;
  append(tree, "");
  for (let i = 0; i < formatsLength; i++) {
    const character = text.charAt(i);
    const shouldInsertPadding = isEditableTree && // Pad the line if the line is empty.
    (!lastCharacter || // Pad the line if the previous character is a line break, otherwise
    // the line break won't be visible.
    lastCharacter === "\n");
    const characterFormats = formats[i];
    let pointer = getLastChild(tree);
    if (characterFormats) {
      characterFormats.forEach((format, formatIndex) => {
        if (pointer && lastCharacterFormats && // Reuse the last element if all formats remain the same.
        isEqualUntil(
          characterFormats,
          lastCharacterFormats,
          formatIndex
        )) {
          pointer = getLastChild(pointer);
          return;
        }
        const { type, tagName, attributes, unregisteredAttributes } = format;
        const boundaryClass = isEditableTree && format === deepestActiveFormat;
        const parent = getParent(pointer);
        const newNode = append(
          parent,
          fromFormat({
            type,
            tagName,
            attributes,
            unregisteredAttributes,
            boundaryClass,
            isEditableTree
          })
        );
        if (isText(pointer) && getText(pointer).length === 0) {
          remove(pointer);
        }
        pointer = append(newNode, "");
      });
    }
    if (i === 0) {
      if (onStartIndex && start === 0) {
        onStartIndex(tree, pointer);
      }
      if (onEndIndex && end === 0) {
        onEndIndex(tree, pointer);
      }
    }
    if (character === OBJECT_REPLACEMENT_CHARACTER) {
      const replacement = replacements[i];
      if (!replacement) {
        continue;
      }
      const { type, attributes, innerHTML } = replacement;
      const formatType = get_format_type_getFormatType(type);
      if (isEditableTree && type === "#comment") {
        pointer = append(getParent(pointer), {
          type: "span",
          attributes: {
            contenteditable: "false",
            "data-rich-text-comment": attributes["data-rich-text-comment"]
          }
        });
        append(
          append(pointer, { type: "span" }),
          attributes["data-rich-text-comment"].trim()
        );
      } else if (!isEditableTree && type === "script") {
        pointer = append(
          getParent(pointer),
          fromFormat({
            type: "script",
            isEditableTree
          })
        );
        append(pointer, {
          html: decodeURIComponent(
            attributes["data-rich-text-script"]
          )
        });
      } else if (formatType?.contentEditable === false) {
        if (innerHTML || isEditableTree) {
          pointer = getParent(pointer);
          if (isEditableTree) {
            const attrs = {
              contenteditable: "false",
              "data-rich-text-bogus": true
            };
            if (start === i && end === i + 1) {
              attrs["data-rich-text-format-boundary"] = true;
            }
            pointer = append(pointer, {
              type: "span",
              attributes: attrs
            });
            if (isEditableTree && i + 1 === text.length) {
              append(getParent(pointer), ZWNBSP);
            }
          }
          pointer = append(
            pointer,
            fromFormat({
              ...replacement,
              isEditableTree
            })
          );
          if (innerHTML) {
            append(pointer, {
              html: innerHTML
            });
          }
        }
      } else {
        pointer = append(
          getParent(pointer),
          fromFormat({
            ...replacement,
            object: true,
            isEditableTree
          })
        );
      }
      pointer = append(getParent(pointer), "");
    } else if (!preserveWhiteSpace && character === "\n") {
      pointer = append(getParent(pointer), {
        type: "br",
        attributes: isEditableTree ? {
          "data-rich-text-line-break": "true"
        } : void 0,
        object: true
      });
      pointer = append(getParent(pointer), "");
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
          type: "span",
          attributes: {
            "data-rich-text-placeholder": placeholder,
            // Necessary to prevent the placeholder from catching
            // selection and being editable.
            style: "pointer-events:none;user-select:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;"
          }
        });
      }
    }
    lastCharacterFormats = characterFormats;
    lastCharacter = character;
  }
  return tree;
}


;// ./node_modules/@wordpress/rich-text/build-module/to-html-string.js


function toHTMLString({ value, preserveWhiteSpace }) {
  const tree = toTree({
    value,
    preserveWhiteSpace,
    createEmpty,
    append,
    getLastChild,
    getParent,
    isText,
    getText,
    remove,
    appendText
  });
  return createChildrenHTML(tree.children);
}
function createEmpty() {
  return {};
}
function getLastChild({ children }) {
  return children && children[children.length - 1];
}
function append(parent, object) {
  if (typeof object === "string") {
    object = { text: object };
  }
  object.parent = parent;
  parent.children = parent.children || [];
  parent.children.push(object);
  return object;
}
function appendText(object, text) {
  object.text += text;
}
function getParent({ parent }) {
  return parent;
}
function isText({ text }) {
  return typeof text === "string";
}
function getText({ text }) {
  return text;
}
function remove(object) {
  const index = object.parent.children.indexOf(object);
  if (index !== -1) {
    object.parent.children.splice(index, 1);
  }
  return object;
}
function createElementHTML({ type, attributes, object, children }) {
  if (type === "#comment") {
    return `<!--${attributes["data-rich-text-comment"]}-->`;
  }
  let attributeString = "";
  for (const key in attributes) {
    if (!(0,external_wp_escapeHtml_namespaceObject.isValidAttributeName)(key)) {
      continue;
    }
    attributeString += ` ${key}="${(0,external_wp_escapeHtml_namespaceObject.escapeAttribute)(
      attributes[key]
    )}"`;
  }
  if (object) {
    return `<${type}${attributeString}>`;
  }
  return `<${type}${attributeString}>${createChildrenHTML(
    children
  )}</${type}>`;
}
function createChildrenHTML(children = []) {
  return children.map((child) => {
    if (child.html !== void 0) {
      return child.html;
    }
    return child.text === void 0 ? createElementHTML(child) : (0,external_wp_escapeHtml_namespaceObject.escapeEditableHTML)(child.text);
  }).join("");
}


;// ./node_modules/@wordpress/rich-text/build-module/get-text-content.js

function getTextContent({ text }) {
  return text.replace(OBJECT_REPLACEMENT_CHARACTER, "");
}


;// ./node_modules/@wordpress/rich-text/build-module/create.js







function createEmptyValue() {
  return {
    formats: [],
    replacements: [],
    text: ""
  };
}
function toFormat({ tagName, attributes }) {
  let formatType;
  if (attributes && attributes.class) {
    formatType = (0,external_wp_data_namespaceObject.select)(store).getFormatTypeForClassName(
      attributes.class
    );
    if (formatType) {
      attributes.class = ` ${attributes.class} `.replace(` ${formatType.className} `, " ").trim();
      if (!attributes.class) {
        delete attributes.class;
      }
    }
  }
  if (!formatType) {
    formatType = (0,external_wp_data_namespaceObject.select)(store).getFormatTypeForBareElement(tagName);
  }
  if (!formatType) {
    return attributes ? { type: tagName, attributes } : { type: tagName };
  }
  if (formatType.__experimentalCreatePrepareEditableTree && !formatType.__experimentalCreateOnChangeEditableValue) {
    return null;
  }
  if (!attributes) {
    return { formatType, type: formatType.name, tagName };
  }
  const registeredAttributes = {};
  const unregisteredAttributes = {};
  const _attributes = { ...attributes };
  for (const key in formatType.attributes) {
    const name = formatType.attributes[key];
    registeredAttributes[key] = _attributes[name];
    delete _attributes[name];
    if (typeof registeredAttributes[key] === "undefined") {
      delete registeredAttributes[key];
    }
  }
  for (const name in _attributes) {
    unregisteredAttributes[name] = attributes[name];
  }
  if (formatType.contentEditable === false) {
    delete unregisteredAttributes.contenteditable;
  }
  return {
    formatType,
    type: formatType.name,
    tagName,
    attributes: registeredAttributes,
    unregisteredAttributes
  };
}
class RichTextData {
  #value;
  static empty() {
    return new RichTextData();
  }
  static fromPlainText(text) {
    return new RichTextData(create({ text }));
  }
  static fromHTMLString(html) {
    return new RichTextData(create({ html }));
  }
  /**
   * Create a RichTextData instance from an HTML element.
   *
   * @param {HTMLElement}                    htmlElement The HTML element to create the instance from.
   * @param {{preserveWhiteSpace?: boolean}} options     Options.
   * @return {RichTextData} The RichTextData instance.
   */
  static fromHTMLElement(htmlElement, options = {}) {
    const { preserveWhiteSpace = false } = options;
    const element = preserveWhiteSpace ? htmlElement : collapseWhiteSpace(htmlElement);
    const richTextData = new RichTextData(create({ element }));
    Object.defineProperty(richTextData, "originalHTML", {
      value: htmlElement.innerHTML
    });
    return richTextData;
  }
  constructor(init = createEmptyValue()) {
    this.#value = init;
  }
  toPlainText() {
    return getTextContent(this.#value);
  }
  // We could expose `toHTMLElement` at some point as well, but we'd only use
  // it internally.
  /**
   * Convert the rich text value to an HTML string.
   *
   * @param {{preserveWhiteSpace?: boolean}} options Options.
   * @return {string} The HTML string.
   */
  toHTMLString({ preserveWhiteSpace } = {}) {
    return this.originalHTML || toHTMLString({ value: this.#value, preserveWhiteSpace });
  }
  valueOf() {
    return this.toHTMLString();
  }
  toString() {
    return this.toHTMLString();
  }
  toJSON() {
    return this.toHTMLString();
  }
  get length() {
    return this.text.length;
  }
  get formats() {
    return this.#value.formats;
  }
  get replacements() {
    return this.#value.replacements;
  }
  get text() {
    return this.#value.text;
  }
}
for (const name of Object.getOwnPropertyNames(String.prototype)) {
  if (RichTextData.prototype.hasOwnProperty(name)) {
    continue;
  }
  Object.defineProperty(RichTextData.prototype, name, {
    value(...args) {
      return this.toHTMLString()[name](...args);
    }
  });
}
function create({
  element,
  text,
  html,
  range,
  __unstableIsEditableTree: isEditableTree
} = {}) {
  if (html instanceof RichTextData) {
    return {
      text: html.text,
      formats: html.formats,
      replacements: html.replacements
    };
  }
  if (typeof text === "string" && text.length > 0) {
    return {
      formats: Array(text.length),
      replacements: Array(text.length),
      text
    };
  }
  if (typeof html === "string" && html.length > 0) {
    element = createElement(document, html);
  }
  if (typeof element !== "object") {
    return createEmptyValue();
  }
  return createFromElement({
    element,
    range,
    isEditableTree
  });
}
function accumulateSelection(accumulator, node, range, value) {
  if (!range) {
    return;
  }
  const { parentNode } = node;
  const { startContainer, startOffset, endContainer, endOffset } = range;
  const currentLength = accumulator.text.length;
  if (value.start !== void 0) {
    accumulator.start = currentLength + value.start;
  } else if (node === startContainer && node.nodeType === node.TEXT_NODE) {
    accumulator.start = currentLength + startOffset;
  } else if (parentNode === startContainer && node === startContainer.childNodes[startOffset]) {
    accumulator.start = currentLength;
  } else if (parentNode === startContainer && node === startContainer.childNodes[startOffset - 1]) {
    accumulator.start = currentLength + value.text.length;
  } else if (node === startContainer) {
    accumulator.start = currentLength;
  }
  if (value.end !== void 0) {
    accumulator.end = currentLength + value.end;
  } else if (node === endContainer && node.nodeType === node.TEXT_NODE) {
    accumulator.end = currentLength + endOffset;
  } else if (parentNode === endContainer && node === endContainer.childNodes[endOffset - 1]) {
    accumulator.end = currentLength + value.text.length;
  } else if (parentNode === endContainer && node === endContainer.childNodes[endOffset]) {
    accumulator.end = currentLength;
  } else if (node === endContainer) {
    accumulator.end = currentLength + endOffset;
  }
}
function filterRange(node, range, filter) {
  if (!range) {
    return;
  }
  const { startContainer, endContainer } = range;
  let { startOffset, endOffset } = range;
  if (node === startContainer) {
    startOffset = filter(node.nodeValue.slice(0, startOffset)).length;
  }
  if (node === endContainer) {
    endOffset = filter(node.nodeValue.slice(0, endOffset)).length;
  }
  return { startContainer, startOffset, endContainer, endOffset };
}
function collapseWhiteSpace(element, isRoot = true) {
  const clone = element.cloneNode(true);
  clone.normalize();
  Array.from(clone.childNodes).forEach((node, i, nodes) => {
    if (node.nodeType === node.TEXT_NODE) {
      let newNodeValue = node.nodeValue;
      if (/[\n\t\r\f]/.test(newNodeValue)) {
        newNodeValue = newNodeValue.replace(/[\n\t\r\f]+/g, " ");
      }
      if (newNodeValue.indexOf("  ") !== -1) {
        newNodeValue = newNodeValue.replace(/ {2,}/g, " ");
      }
      if (i === 0 && newNodeValue.startsWith(" ")) {
        newNodeValue = newNodeValue.slice(1);
      } else if (isRoot && i === nodes.length - 1 && newNodeValue.endsWith(" ")) {
        newNodeValue = newNodeValue.slice(0, -1);
      }
      node.nodeValue = newNodeValue;
    } else if (node.nodeType === node.ELEMENT_NODE) {
      node.replaceWith(collapseWhiteSpace(node, false));
    }
  });
  return clone;
}
const CARRIAGE_RETURN = "\r";
function removeReservedCharacters(string) {
  return string.replace(
    new RegExp(
      `[${ZWNBSP}${OBJECT_REPLACEMENT_CHARACTER}${CARRIAGE_RETURN}]`,
      "gu"
    ),
    ""
  );
}
function createFromElement({ element, range, isEditableTree }) {
  const accumulator = createEmptyValue();
  if (!element) {
    return accumulator;
  }
  if (!element.hasChildNodes()) {
    accumulateSelection(accumulator, element, range, createEmptyValue());
    return accumulator;
  }
  const length = element.childNodes.length;
  for (let index = 0; index < length; index++) {
    const node = element.childNodes[index];
    const tagName = node.nodeName.toLowerCase();
    if (node.nodeType === node.TEXT_NODE) {
      const text = removeReservedCharacters(node.nodeValue);
      range = filterRange(node, range, removeReservedCharacters);
      accumulateSelection(accumulator, node, range, { text });
      accumulator.formats.length += text.length;
      accumulator.replacements.length += text.length;
      accumulator.text += text;
      continue;
    }
    if (node.nodeType === node.COMMENT_NODE || node.nodeType === node.ELEMENT_NODE && node.tagName === "SPAN" && node.hasAttribute("data-rich-text-comment")) {
      const value2 = {
        formats: [,],
        replacements: [
          {
            type: "#comment",
            attributes: {
              "data-rich-text-comment": node.nodeType === node.COMMENT_NODE ? node.nodeValue : node.getAttribute(
                "data-rich-text-comment"
              )
            }
          }
        ],
        text: OBJECT_REPLACEMENT_CHARACTER
      };
      accumulateSelection(accumulator, node, range, value2);
      mergePair(accumulator, value2);
      continue;
    }
    if (node.nodeType !== node.ELEMENT_NODE) {
      continue;
    }
    if (isEditableTree && // Ignore any line breaks that are not inserted by us.
    tagName === "br" && !node.getAttribute("data-rich-text-line-break")) {
      accumulateSelection(accumulator, node, range, createEmptyValue());
      continue;
    }
    if (tagName === "script") {
      const value2 = {
        formats: [,],
        replacements: [
          {
            type: tagName,
            attributes: {
              "data-rich-text-script": node.getAttribute("data-rich-text-script") || encodeURIComponent(node.innerHTML)
            }
          }
        ],
        text: OBJECT_REPLACEMENT_CHARACTER
      };
      accumulateSelection(accumulator, node, range, value2);
      mergePair(accumulator, value2);
      continue;
    }
    if (tagName === "br") {
      accumulateSelection(accumulator, node, range, createEmptyValue());
      mergePair(accumulator, create({ text: "\n" }));
      continue;
    }
    const format = toFormat({
      tagName,
      attributes: getAttributes({ element: node })
    });
    if (format?.formatType?.contentEditable === false) {
      delete format.formatType;
      accumulateSelection(accumulator, node, range, createEmptyValue());
      mergePair(accumulator, {
        formats: [,],
        replacements: [
          {
            ...format,
            innerHTML: node.innerHTML
          }
        ],
        text: OBJECT_REPLACEMENT_CHARACTER
      });
      continue;
    }
    if (format) {
      delete format.formatType;
    }
    const value = createFromElement({
      element: node,
      range,
      isEditableTree
    });
    accumulateSelection(accumulator, node, range, value);
    if (!format || node.getAttribute("data-rich-text-placeholder") || node.getAttribute("data-rich-text-bogus")) {
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
      let mergeFormats2 = function(formats) {
        if (mergeFormats2.formats === formats) {
          return mergeFormats2.newFormats;
        }
        const newFormats = formats ? [format, ...formats] : [format];
        mergeFormats2.formats = formats;
        mergeFormats2.newFormats = newFormats;
        return newFormats;
      };
      var mergeFormats = mergeFormats2;
      mergeFormats2.newFormats = [format];
      mergePair(accumulator, {
        ...value,
        formats: Array.from(value.formats, mergeFormats2)
      });
    }
  }
  return accumulator;
}
function getAttributes({ element }) {
  if (!element.hasAttributes()) {
    return;
  }
  const length = element.attributes.length;
  let accumulator;
  for (let i = 0; i < length; i++) {
    const { name, value } = element.attributes[i];
    if (name.indexOf("data-rich-text-") === 0) {
      continue;
    }
    const safeName = /^on/i.test(name) ? "data-disable-rich-text-" + name : name;
    accumulator = accumulator || {};
    accumulator[safeName] = value;
  }
  return accumulator;
}


;// ./node_modules/@wordpress/rich-text/build-module/concat.js


function mergePair(a, b) {
  a.formats = a.formats.concat(b.formats);
  a.replacements = a.replacements.concat(b.replacements);
  a.text += b.text;
  return a;
}
function concat(...values) {
  return normaliseFormats(values.reduce(mergePair, create()));
}


;// ./node_modules/@wordpress/rich-text/build-module/get-active-format.js

function getActiveFormat(value, formatType) {
  return getActiveFormats(value).find(
    ({ type }) => type === formatType
  );
}


;// ./node_modules/@wordpress/rich-text/build-module/get-active-object.js

function getActiveObject({ start, end, replacements, text }) {
  if (start + 1 !== end || text[start] !== OBJECT_REPLACEMENT_CHARACTER) {
    return;
  }
  return replacements[start];
}


;// ./node_modules/@wordpress/rich-text/build-module/is-collapsed.js
function isCollapsed({
  start,
  end
}) {
  if (start === void 0 || end === void 0) {
    return;
  }
  return start === end;
}


;// ./node_modules/@wordpress/rich-text/build-module/is-empty.js
function isEmpty({ text }) {
  return text.length === 0;
}


;// ./node_modules/@wordpress/rich-text/build-module/join.js


function join(values, separator = "") {
  if (typeof separator === "string") {
    separator = create({ text: separator });
  }
  return normaliseFormats(
    values.reduce((accumulator, { formats, replacements, text }) => ({
      formats: accumulator.formats.concat(separator.formats, formats),
      replacements: accumulator.replacements.concat(
        separator.replacements,
        replacements
      ),
      text: accumulator.text + separator.text + text
    }))
  );
}


;// ./node_modules/@wordpress/rich-text/build-module/register-format-type.js


function registerFormatType(name, settings) {
  settings = {
    name,
    ...settings
  };
  if (typeof settings.name !== "string") {
    window.console.error("Format names must be strings.");
    return;
  }
  if (!/^[a-z][a-z0-9-]*\/[a-z][a-z0-9-]*$/.test(settings.name)) {
    window.console.error(
      "Format names must contain a namespace prefix, include only lowercase alphanumeric characters or dashes, and start with a letter. Example: my-plugin/my-custom-format"
    );
    return;
  }
  if ((0,external_wp_data_namespaceObject.select)(store).getFormatType(settings.name)) {
    window.console.error(
      'Format "' + settings.name + '" is already registered.'
    );
    return;
  }
  if (typeof settings.tagName !== "string" || settings.tagName === "") {
    window.console.error("Format tag names must be a string.");
    return;
  }
  if ((typeof settings.className !== "string" || settings.className === "") && settings.className !== null) {
    window.console.error(
      "Format class names must be a string, or null to handle bare elements."
    );
    return;
  }
  if (!/^[_a-zA-Z]+[a-zA-Z0-9_-]*$/.test(settings.className)) {
    window.console.error(
      "A class name must begin with a letter, followed by any number of hyphens, underscores, letters, or numbers."
    );
    return;
  }
  if (settings.className === null) {
    const formatTypeForBareElement = (0,external_wp_data_namespaceObject.select)(
      store
    ).getFormatTypeForBareElement(settings.tagName);
    if (formatTypeForBareElement && formatTypeForBareElement.name !== "core/unknown") {
      window.console.error(
        `Format "${formatTypeForBareElement.name}" is already registered to handle bare tag name "${settings.tagName}".`
      );
      return;
    }
  } else {
    const formatTypeForClassName = (0,external_wp_data_namespaceObject.select)(
      store
    ).getFormatTypeForClassName(settings.className);
    if (formatTypeForClassName) {
      window.console.error(
        `Format "${formatTypeForClassName.name}" is already registered to handle class name "${settings.className}".`
      );
      return;
    }
  }
  if (!("title" in settings) || settings.title === "") {
    window.console.error(
      'The format "' + settings.name + '" must have a title.'
    );
    return;
  }
  if ("keywords" in settings && settings.keywords.length > 3) {
    window.console.error(
      'The format "' + settings.name + '" can have a maximum of 3 keywords.'
    );
    return;
  }
  if (typeof settings.title !== "string") {
    window.console.error("Format titles must be strings.");
    return;
  }
  (0,external_wp_data_namespaceObject.dispatch)(store).addFormatTypes(settings);
  return settings;
}


;// ./node_modules/@wordpress/rich-text/build-module/remove-format.js

function removeFormat(value, formatType, startIndex = value.start, endIndex = value.end) {
  const { formats, activeFormats } = value;
  const newFormats = formats.slice();
  if (startIndex === endIndex) {
    const format = newFormats[startIndex]?.find(
      ({ type }) => type === formatType
    );
    if (format) {
      while (newFormats[startIndex]?.find(
        (newFormat) => newFormat === format
      )) {
        filterFormats(newFormats, startIndex, formatType);
        startIndex--;
      }
      endIndex++;
      while (newFormats[endIndex]?.find(
        (newFormat) => newFormat === format
      )) {
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
  return normaliseFormats({
    ...value,
    formats: newFormats,
    activeFormats: activeFormats?.filter(({ type }) => type !== formatType) || []
  });
}
function filterFormats(formats, index, formatType) {
  const newFormats = formats[index].filter(
    ({ type }) => type !== formatType
  );
  if (newFormats.length) {
    formats[index] = newFormats;
  } else {
    delete formats[index];
  }
}


;// ./node_modules/@wordpress/rich-text/build-module/insert.js


function insert(value, valueToInsert, startIndex = value.start, endIndex = value.end) {
  const { formats, replacements, text } = value;
  if (typeof valueToInsert === "string") {
    valueToInsert = create({ text: valueToInsert });
  }
  const index = startIndex + valueToInsert.text.length;
  return normaliseFormats({
    formats: formats.slice(0, startIndex).concat(valueToInsert.formats, formats.slice(endIndex)),
    replacements: replacements.slice(0, startIndex).concat(
      valueToInsert.replacements,
      replacements.slice(endIndex)
    ),
    text: text.slice(0, startIndex) + valueToInsert.text + text.slice(endIndex),
    start: index,
    end: index
  });
}


;// ./node_modules/@wordpress/rich-text/build-module/remove.js


function remove_remove(value, startIndex, endIndex) {
  return insert(value, create(), startIndex, endIndex);
}


;// ./node_modules/@wordpress/rich-text/build-module/replace.js

function replace_replace({ formats, replacements, text, start, end }, pattern, replacement) {
  text = text.replace(pattern, (match, ...rest) => {
    const offset = rest[rest.length - 2];
    let newText = replacement;
    let newFormats;
    let newReplacements;
    if (typeof newText === "function") {
      newText = replacement(match, ...rest);
    }
    if (typeof newText === "object") {
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
    replacements = replacements.slice(0, offset).concat(
      newReplacements,
      replacements.slice(offset + match.length)
    );
    if (start) {
      start = end = offset + newText.length;
    }
    return newText;
  });
  return normaliseFormats({ formats, replacements, text, start, end });
}


;// ./node_modules/@wordpress/rich-text/build-module/insert-object.js


function insertObject(value, formatToInsert, startIndex, endIndex) {
  const valueToInsert = {
    formats: [,],
    replacements: [formatToInsert],
    text: OBJECT_REPLACEMENT_CHARACTER
  };
  return insert(value, valueToInsert, startIndex, endIndex);
}


;// ./node_modules/@wordpress/rich-text/build-module/slice.js
function slice(value, startIndex = value.start, endIndex = value.end) {
  const { formats, replacements, text } = value;
  if (startIndex === void 0 || endIndex === void 0) {
    return { ...value };
  }
  return {
    formats: formats.slice(startIndex, endIndex),
    replacements: replacements.slice(startIndex, endIndex),
    text: text.slice(startIndex, endIndex)
  };
}


;// ./node_modules/@wordpress/rich-text/build-module/split.js
function split({ formats, replacements, text, start, end }, string) {
  if (typeof string !== "string") {
    return splitAtSelection(...arguments);
  }
  let nextStart = 0;
  return text.split(string).map((substring) => {
    const startIndex = nextStart;
    const value = {
      formats: formats.slice(startIndex, startIndex + substring.length),
      replacements: replacements.slice(
        startIndex,
        startIndex + substring.length
      ),
      text: substring
    };
    nextStart += string.length + substring.length;
    if (start !== void 0 && end !== void 0) {
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
function splitAtSelection({ formats, replacements, text, start, end }, startIndex = start, endIndex = end) {
  if (start === void 0 || end === void 0) {
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
  return [before, after];
}


;// ./node_modules/@wordpress/rich-text/build-module/is-range-equal.js
function isRangeEqual(a, b) {
  return a === b || a && b && a.startContainer === b.startContainer && a.startOffset === b.startOffset && a.endContainer === b.endContainer && a.endOffset === b.endOffset;
}


;// ./node_modules/@wordpress/rich-text/build-module/to-dom.js



const MATHML_NAMESPACE = "http://www.w3.org/1998/Math/MathML";
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
function to_dom_append(element, child) {
  if (child.html !== void 0) {
    return element.innerHTML += child.html;
  }
  if (typeof child === "string") {
    child = element.ownerDocument.createTextNode(child);
  }
  const { type, attributes } = child;
  if (type) {
    if (type === "#comment") {
      child = element.ownerDocument.createComment(
        attributes["data-rich-text-comment"]
      );
    } else {
      const parentNamespace = element.namespaceURI;
      if (type === "math") {
        child = element.ownerDocument.createElementNS(
          MATHML_NAMESPACE,
          type
        );
      } else if (parentNamespace === MATHML_NAMESPACE) {
        if (element.tagName === "MTEXT") {
          child = element.ownerDocument.createElement(type);
        } else {
          child = element.ownerDocument.createElementNS(
            MATHML_NAMESPACE,
            type
          );
        }
      } else {
        child = element.ownerDocument.createElement(type);
      }
      for (const key in attributes) {
        child.setAttribute(key, attributes[key]);
      }
    }
  }
  return element.appendChild(child);
}
function to_dom_appendText(node, text) {
  node.appendData(text);
}
function to_dom_getLastChild({ lastChild }) {
  return lastChild;
}
function to_dom_getParent({ parentNode }) {
  return parentNode;
}
function to_dom_isText(node) {
  return node.nodeType === node.TEXT_NODE;
}
function to_dom_getText({ nodeValue }) {
  return nodeValue;
}
function to_dom_remove(node) {
  return node.parentNode.removeChild(node);
}
function toDom({
  value,
  prepareEditableTree,
  isEditableTree = true,
  placeholder,
  doc = document
}) {
  let startPath = [];
  let endPath = [];
  if (prepareEditableTree) {
    value = {
      ...value,
      formats: prepareEditableTree(value)
    };
  }
  const createEmpty = () => createElement(doc, "");
  const tree = toTree({
    value,
    createEmpty,
    append: to_dom_append,
    getLastChild: to_dom_getLastChild,
    getParent: to_dom_getParent,
    isText: to_dom_isText,
    getText: to_dom_getText,
    remove: to_dom_remove,
    appendText: to_dom_appendText,
    onStartIndex(body, pointer) {
      startPath = createPathToNode(pointer, body, [
        pointer.nodeValue.length
      ]);
    },
    onEndIndex(body, pointer) {
      endPath = createPathToNode(pointer, body, [
        pointer.nodeValue.length
      ]);
    },
    isEditableTree,
    placeholder
  });
  return {
    body: tree,
    selection: { startPath, endPath }
  };
}
function apply({
  value,
  current,
  prepareEditableTree,
  __unstableDomOnly,
  placeholder
}) {
  const { body, selection } = toDom({
    value,
    prepareEditableTree,
    placeholder,
    doc: current.ownerDocument
  });
  applyValue(body, current);
  if (value.start !== void 0 && !__unstableDomOnly) {
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
          let ii = currentAttributes.length;
          while (ii--) {
            const { name } = currentAttributes[ii];
            if (!futureChild.getAttribute(name)) {
              currentChild.removeAttribute(name);
            }
          }
        }
        if (futureAttributes) {
          for (let ii = 0; ii < futureAttributes.length; ii++) {
            const { name, value } = futureAttributes[ii];
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
function applySelection({ startPath, endPath }, current) {
  const { node: startContainer, offset: startOffset } = getNodeByPath(
    current,
    startPath
  );
  const { node: endContainer, offset: endOffset } = getNodeByPath(
    current,
    endPath
  );
  const { ownerDocument } = current;
  const { defaultView } = ownerDocument;
  const selection = defaultView.getSelection();
  const range = ownerDocument.createRange();
  range.setStart(startContainer, startOffset);
  range.setEnd(endContainer, endOffset);
  const { activeElement } = ownerDocument;
  if (selection.rangeCount > 0) {
    if (isRangeEqual(range, selection.getRangeAt(0))) {
      return;
    }
    selection.removeAllRanges();
  }
  selection.addRange(range);
  if (activeElement !== ownerDocument.activeElement) {
    if (activeElement instanceof defaultView.HTMLElement) {
      activeElement.focus();
    }
  }
}


;// external ["wp","a11y"]
const external_wp_a11y_namespaceObject = window["wp"]["a11y"];
;// external ["wp","i18n"]
const external_wp_i18n_namespaceObject = window["wp"]["i18n"];
;// ./node_modules/@wordpress/rich-text/build-module/toggle-format.js





function toggleFormat(value, format) {
  if (getActiveFormat(value, format.type)) {
    if (format.title) {
      (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)("%s removed."), format.title), "assertive");
    }
    return removeFormat(value, format.type);
  }
  if (format.title) {
    (0,external_wp_a11y_namespaceObject.speak)((0,external_wp_i18n_namespaceObject.sprintf)((0,external_wp_i18n_namespaceObject.__)("%s applied."), format.title), "assertive");
  }
  return applyFormat(value, format);
}


;// ./node_modules/@wordpress/rich-text/build-module/unregister-format-type.js


function unregisterFormatType(name) {
  const oldFormat = (0,external_wp_data_namespaceObject.select)(store).getFormatType(name);
  if (!oldFormat) {
    window.console.error(`Format ${name} is not registered.`);
    return;
  }
  (0,external_wp_data_namespaceObject.dispatch)(store).removeFormatTypes(name);
  return oldFormat;
}


;// external ["wp","element"]
const external_wp_element_namespaceObject = window["wp"]["element"];
;// external ["wp","deprecated"]
const external_wp_deprecated_namespaceObject = window["wp"]["deprecated"];
var external_wp_deprecated_default = /*#__PURE__*/__webpack_require__.n(external_wp_deprecated_namespaceObject);
;// ./node_modules/@wordpress/rich-text/build-module/component/use-anchor-ref.js



function useAnchorRef({ ref, value, settings = {} }) {
  external_wp_deprecated_default()("`useAnchorRef` hook", {
    since: "6.1",
    alternative: "`useAnchor` hook"
  });
  const { tagName, className, name } = settings;
  const activeFormat = name ? getActiveFormat(value, name) : void 0;
  return (0,external_wp_element_namespaceObject.useMemo)(() => {
    if (!ref.current) {
      return;
    }
    const {
      ownerDocument: { defaultView }
    } = ref.current;
    const selection = defaultView.getSelection();
    if (!selection.rangeCount) {
      return;
    }
    const range = selection.getRangeAt(0);
    if (!activeFormat) {
      return range;
    }
    let element = range.startContainer;
    element = element.nextElementSibling || element;
    while (element.nodeType !== element.ELEMENT_NODE) {
      element = element.parentNode;
    }
    return element.closest(
      tagName + (className ? "." + className : "")
    );
  }, [activeFormat, value.start, value.end, tagName, className]);
}


;// external ["wp","compose"]
const external_wp_compose_namespaceObject = window["wp"]["compose"];
;// ./node_modules/@wordpress/rich-text/build-module/component/use-anchor.js


function getFormatElement(range, editableContentElement, tagName, className) {
  let element = range.startContainer;
  if (element.nodeType === element.TEXT_NODE && range.startOffset === element.length && element.nextSibling) {
    element = element.nextSibling;
    while (element.firstChild) {
      element = element.firstChild;
    }
  }
  if (element.nodeType !== element.ELEMENT_NODE) {
    element = element.parentElement;
  }
  if (!element) {
    return;
  }
  if (element === editableContentElement) {
    return;
  }
  if (!editableContentElement.contains(element)) {
    return;
  }
  const selector = tagName + (className ? "." + className : "");
  while (element !== editableContentElement) {
    if (element.matches(selector)) {
      return element;
    }
    element = element.parentElement;
  }
}
function createVirtualAnchorElement(range, editableContentElement) {
  return {
    contextElement: editableContentElement,
    getBoundingClientRect() {
      return editableContentElement.contains(range.startContainer) ? range.getBoundingClientRect() : editableContentElement.getBoundingClientRect();
    }
  };
}
function getAnchor(editableContentElement, tagName, className) {
  if (!editableContentElement) {
    return;
  }
  const { ownerDocument } = editableContentElement;
  const { defaultView } = ownerDocument;
  const selection = defaultView.getSelection();
  if (!selection) {
    return;
  }
  if (!selection.rangeCount) {
    return;
  }
  const range = selection.getRangeAt(0);
  if (!range || !range.startContainer) {
    return;
  }
  const formatElement = getFormatElement(
    range,
    editableContentElement,
    tagName,
    className
  );
  if (formatElement) {
    return formatElement;
  }
  return createVirtualAnchorElement(range, editableContentElement);
}
function useAnchor({ editableContentElement, settings = {} }) {
  const { tagName, className, isActive } = settings;
  const [anchor, setAnchor] = (0,external_wp_element_namespaceObject.useState)(
    () => getAnchor(editableContentElement, tagName, className)
  );
  const wasActive = (0,external_wp_compose_namespaceObject.usePrevious)(isActive);
  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    if (!editableContentElement) {
      return;
    }
    function callback() {
      setAnchor(
        getAnchor(editableContentElement, tagName, className)
      );
    }
    function attach() {
      ownerDocument.addEventListener("selectionchange", callback);
    }
    function detach() {
      ownerDocument.removeEventListener("selectionchange", callback);
    }
    const { ownerDocument } = editableContentElement;
    if (editableContentElement === ownerDocument.activeElement || // When a link is created, we need to attach the popover to the newly created anchor.
    !wasActive && isActive || // Sometimes we're _removing_ an active anchor, such as the inline color popover.
    // When we add the color, it switches from a virtual anchor to a `<mark>` element.
    // When we _remove_ the color, it switches from a `<mark>` element to a virtual anchor.
    wasActive && !isActive) {
      setAnchor(
        getAnchor(editableContentElement, tagName, className)
      );
      attach();
    }
    editableContentElement.addEventListener("focusin", attach);
    editableContentElement.addEventListener("focusout", detach);
    return () => {
      detach();
      editableContentElement.removeEventListener("focusin", attach);
      editableContentElement.removeEventListener("focusout", detach);
    };
  }, [editableContentElement, tagName, className, isActive, wasActive]);
  return anchor;
}


;// ./node_modules/@wordpress/rich-text/build-module/component/use-default-style.js

const whiteSpace = "pre-wrap";
const minWidth = "1px";
function useDefaultStyle() {
  return (0,external_wp_element_namespaceObject.useCallback)((element) => {
    if (!element) {
      return;
    }
    element.style.whiteSpace = whiteSpace;
    element.style.minWidth = minWidth;
  }, []);
}


;// ./node_modules/colord/index.mjs
var r={grad:.9,turn:360,rad:360/(2*Math.PI)},t=function(r){return"string"==typeof r?r.length>0:"number"==typeof r},n=function(r,t,n){return void 0===t&&(t=0),void 0===n&&(n=Math.pow(10,t)),Math.round(n*r)/n+0},e=function(r,t,n){return void 0===t&&(t=0),void 0===n&&(n=1),r>n?n:r>t?r:t},u=function(r){return(r=isFinite(r)?r%360:0)>0?r:r+360},a=function(r){return{r:e(r.r,0,255),g:e(r.g,0,255),b:e(r.b,0,255),a:e(r.a)}},o=function(r){return{r:n(r.r),g:n(r.g),b:n(r.b),a:n(r.a,3)}},i=/^#([0-9a-f]{3,8})$/i,s=function(r){var t=r.toString(16);return t.length<2?"0"+t:t},h=function(r){var t=r.r,n=r.g,e=r.b,u=r.a,a=Math.max(t,n,e),o=a-Math.min(t,n,e),i=o?a===t?(n-e)/o:a===n?2+(e-t)/o:4+(t-n)/o:0;return{h:60*(i<0?i+6:i),s:a?o/a*100:0,v:a/255*100,a:u}},b=function(r){var t=r.h,n=r.s,e=r.v,u=r.a;t=t/360*6,n/=100,e/=100;var a=Math.floor(t),o=e*(1-n),i=e*(1-(t-a)*n),s=e*(1-(1-t+a)*n),h=a%6;return{r:255*[e,i,o,o,s,e][h],g:255*[s,e,e,i,o,o][h],b:255*[o,o,s,e,e,i][h],a:u}},g=function(r){return{h:u(r.h),s:e(r.s,0,100),l:e(r.l,0,100),a:e(r.a)}},d=function(r){return{h:n(r.h),s:n(r.s),l:n(r.l),a:n(r.a,3)}},f=function(r){return b((n=(t=r).s,{h:t.h,s:(n*=((e=t.l)<50?e:100-e)/100)>0?2*n/(e+n)*100:0,v:e+n,a:t.a}));var t,n,e},c=function(r){return{h:(t=h(r)).h,s:(u=(200-(n=t.s))*(e=t.v)/100)>0&&u<200?n*e/100/(u<=100?u:200-u)*100:0,l:u/2,a:t.a};var t,n,e,u},l=/^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s*,\s*([+-]?\d*\.?\d+)%\s*,\s*([+-]?\d*\.?\d+)%\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i,p=/^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s+([+-]?\d*\.?\d+)%\s+([+-]?\d*\.?\d+)%\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i,v=/^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i,m=/^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i,y={string:[[function(r){var t=i.exec(r);return t?(r=t[1]).length<=4?{r:parseInt(r[0]+r[0],16),g:parseInt(r[1]+r[1],16),b:parseInt(r[2]+r[2],16),a:4===r.length?n(parseInt(r[3]+r[3],16)/255,2):1}:6===r.length||8===r.length?{r:parseInt(r.substr(0,2),16),g:parseInt(r.substr(2,2),16),b:parseInt(r.substr(4,2),16),a:8===r.length?n(parseInt(r.substr(6,2),16)/255,2):1}:null:null},"hex"],[function(r){var t=v.exec(r)||m.exec(r);return t?t[2]!==t[4]||t[4]!==t[6]?null:a({r:Number(t[1])/(t[2]?100/255:1),g:Number(t[3])/(t[4]?100/255:1),b:Number(t[5])/(t[6]?100/255:1),a:void 0===t[7]?1:Number(t[7])/(t[8]?100:1)}):null},"rgb"],[function(t){var n=l.exec(t)||p.exec(t);if(!n)return null;var e,u,a=g({h:(e=n[1],u=n[2],void 0===u&&(u="deg"),Number(e)*(r[u]||1)),s:Number(n[3]),l:Number(n[4]),a:void 0===n[5]?1:Number(n[5])/(n[6]?100:1)});return f(a)},"hsl"]],object:[[function(r){var n=r.r,e=r.g,u=r.b,o=r.a,i=void 0===o?1:o;return t(n)&&t(e)&&t(u)?a({r:Number(n),g:Number(e),b:Number(u),a:Number(i)}):null},"rgb"],[function(r){var n=r.h,e=r.s,u=r.l,a=r.a,o=void 0===a?1:a;if(!t(n)||!t(e)||!t(u))return null;var i=g({h:Number(n),s:Number(e),l:Number(u),a:Number(o)});return f(i)},"hsl"],[function(r){var n=r.h,a=r.s,o=r.v,i=r.a,s=void 0===i?1:i;if(!t(n)||!t(a)||!t(o))return null;var h=function(r){return{h:u(r.h),s:e(r.s,0,100),v:e(r.v,0,100),a:e(r.a)}}({h:Number(n),s:Number(a),v:Number(o),a:Number(s)});return b(h)},"hsv"]]},N=function(r,t){for(var n=0;n<t.length;n++){var e=t[n][0](r);if(e)return[e,t[n][1]]}return[null,void 0]},x=function(r){return"string"==typeof r?N(r.trim(),y.string):"object"==typeof r&&null!==r?N(r,y.object):[null,void 0]},I=function(r){return x(r)[1]},M=function(r,t){var n=c(r);return{h:n.h,s:e(n.s+100*t,0,100),l:n.l,a:n.a}},H=function(r){return(299*r.r+587*r.g+114*r.b)/1e3/255},$=function(r,t){var n=c(r);return{h:n.h,s:n.s,l:e(n.l+100*t,0,100),a:n.a}},j=function(){function r(r){this.parsed=x(r)[0],this.rgba=this.parsed||{r:0,g:0,b:0,a:1}}return r.prototype.isValid=function(){return null!==this.parsed},r.prototype.brightness=function(){return n(H(this.rgba),2)},r.prototype.isDark=function(){return H(this.rgba)<.5},r.prototype.isLight=function(){return H(this.rgba)>=.5},r.prototype.toHex=function(){return r=o(this.rgba),t=r.r,e=r.g,u=r.b,i=(a=r.a)<1?s(n(255*a)):"","#"+s(t)+s(e)+s(u)+i;var r,t,e,u,a,i},r.prototype.toRgb=function(){return o(this.rgba)},r.prototype.toRgbString=function(){return r=o(this.rgba),t=r.r,n=r.g,e=r.b,(u=r.a)<1?"rgba("+t+", "+n+", "+e+", "+u+")":"rgb("+t+", "+n+", "+e+")";var r,t,n,e,u},r.prototype.toHsl=function(){return d(c(this.rgba))},r.prototype.toHslString=function(){return r=d(c(this.rgba)),t=r.h,n=r.s,e=r.l,(u=r.a)<1?"hsla("+t+", "+n+"%, "+e+"%, "+u+")":"hsl("+t+", "+n+"%, "+e+"%)";var r,t,n,e,u},r.prototype.toHsv=function(){return r=h(this.rgba),{h:n(r.h),s:n(r.s),v:n(r.v),a:n(r.a,3)};var r},r.prototype.invert=function(){return w({r:255-(r=this.rgba).r,g:255-r.g,b:255-r.b,a:r.a});var r},r.prototype.saturate=function(r){return void 0===r&&(r=.1),w(M(this.rgba,r))},r.prototype.desaturate=function(r){return void 0===r&&(r=.1),w(M(this.rgba,-r))},r.prototype.grayscale=function(){return w(M(this.rgba,-1))},r.prototype.lighten=function(r){return void 0===r&&(r=.1),w($(this.rgba,r))},r.prototype.darken=function(r){return void 0===r&&(r=.1),w($(this.rgba,-r))},r.prototype.rotate=function(r){return void 0===r&&(r=15),this.hue(this.hue()+r)},r.prototype.alpha=function(r){return"number"==typeof r?w({r:(t=this.rgba).r,g:t.g,b:t.b,a:r}):n(this.rgba.a,3);var t},r.prototype.hue=function(r){var t=c(this.rgba);return"number"==typeof r?w({h:r,s:t.s,l:t.l,a:t.a}):n(t.h)},r.prototype.isEqual=function(r){return this.toHex()===w(r).toHex()},r}(),w=function(r){return r instanceof j?r:new j(r)},S=(/* unused pure expression or super */ null && ([])),k=function(r){r.forEach(function(r){S.indexOf(r)<0&&(r(j,y),S.push(r))})},E=function(){return new j({r:255*Math.random(),g:255*Math.random(),b:255*Math.random()})};

;// ./node_modules/@wordpress/rich-text/build-module/component/use-boundary-style.js


function useBoundaryStyle({ record }) {
  const ref = (0,external_wp_element_namespaceObject.useRef)();
  const { activeFormats = [], replacements, start } = record.current;
  const activeReplacement = replacements[start];
  (0,external_wp_element_namespaceObject.useEffect)(() => {
    if ((!activeFormats || !activeFormats.length) && !activeReplacement) {
      return;
    }
    const boundarySelector = "*[data-rich-text-format-boundary]";
    const element = ref.current.querySelector(boundarySelector);
    if (!element) {
      return;
    }
    const { ownerDocument } = element;
    const { defaultView } = ownerDocument;
    const computedStyle = defaultView.getComputedStyle(element);
    const newColor = w(computedStyle.color).alpha(0.2).toRgbString();
    const selector = `.rich-text:focus ${boundarySelector}`;
    const rule = `background-color: ${newColor}`;
    const style = `${selector} {${rule}}`;
    const globalStyleId = "rich-text-boundary-style";
    let globalStyle = ownerDocument.getElementById(globalStyleId);
    if (!globalStyle) {
      globalStyle = ownerDocument.createElement("style");
      globalStyle.id = globalStyleId;
      ownerDocument.head.appendChild(globalStyle);
    }
    if (globalStyle.innerHTML !== style) {
      globalStyle.innerHTML = style;
    }
  }, [activeFormats, activeReplacement]);
  return ref;
}


;// ./node_modules/@wordpress/rich-text/build-module/component/event-listeners/copy-handler.js




var copy_handler_default = (props) => (element) => {
  function onCopy(event) {
    const { record } = props.current;
    const { ownerDocument } = element;
    if (isCollapsed(record.current) || !element.contains(ownerDocument.activeElement)) {
      return;
    }
    const selectedRecord = slice(record.current);
    const plainText = getTextContent(selectedRecord);
    const html = toHTMLString({ value: selectedRecord });
    event.clipboardData.setData("text/plain", plainText);
    event.clipboardData.setData("text/html", html);
    event.clipboardData.setData("rich-text", "true");
    event.preventDefault();
    if (event.type === "cut") {
      ownerDocument.execCommand("delete");
    }
  }
  const { defaultView } = element.ownerDocument;
  defaultView.addEventListener("copy", onCopy);
  defaultView.addEventListener("cut", onCopy);
  return () => {
    defaultView.removeEventListener("copy", onCopy);
    defaultView.removeEventListener("cut", onCopy);
  };
};


;// ./node_modules/@wordpress/rich-text/build-module/component/event-listeners/select-object.js
var select_object_default = () => (element) => {
  function onClick(event) {
    const { target } = event;
    if (target === element || target.textContent && target.isContentEditable) {
      return;
    }
    const { ownerDocument } = target;
    const { defaultView } = ownerDocument;
    const selection = defaultView.getSelection();
    if (selection.containsNode(target)) {
      return;
    }
    const range = ownerDocument.createRange();
    const nodeToSelect = target.isContentEditable ? target : target.closest("[contenteditable]");
    range.selectNode(nodeToSelect);
    selection.removeAllRanges();
    selection.addRange(range);
    event.preventDefault();
  }
  function onFocusIn(event) {
    if (event.relatedTarget && !element.contains(event.relatedTarget) && event.relatedTarget.tagName === "A") {
      onClick(event);
    }
  }
  element.addEventListener("click", onClick);
  element.addEventListener("focusin", onFocusIn);
  return () => {
    element.removeEventListener("click", onClick);
    element.removeEventListener("focusin", onFocusIn);
  };
};


;// external ["wp","keycodes"]
const external_wp_keycodes_namespaceObject = window["wp"]["keycodes"];
;// ./node_modules/@wordpress/rich-text/build-module/component/event-listeners/format-boundaries.js


const EMPTY_ACTIVE_FORMATS = [];
var format_boundaries_default = (props) => (element) => {
  function onKeyDown(event) {
    const { keyCode, shiftKey, altKey, metaKey, ctrlKey } = event;
    if (
      // Only override left and right keys without modifiers pressed.
      shiftKey || altKey || metaKey || ctrlKey || keyCode !== external_wp_keycodes_namespaceObject.LEFT && keyCode !== external_wp_keycodes_namespaceObject.RIGHT
    ) {
      return;
    }
    const { record, applyRecord, forceRender } = props.current;
    const {
      text,
      formats,
      start,
      end,
      activeFormats: currentActiveFormats = []
    } = record.current;
    const collapsed = isCollapsed(record.current);
    const { ownerDocument } = element;
    const { defaultView } = ownerDocument;
    const { direction } = defaultView.getComputedStyle(element);
    const reverseKey = direction === "rtl" ? external_wp_keycodes_namespaceObject.RIGHT : external_wp_keycodes_namespaceObject.LEFT;
    const isReverse = event.keyCode === reverseKey;
    if (collapsed && currentActiveFormats.length === 0) {
      if (start === 0 && isReverse) {
        return;
      }
      if (end === text.length && !isReverse) {
        return;
      }
    }
    if (!collapsed) {
      return;
    }
    const formatsBefore = formats[start - 1] || EMPTY_ACTIVE_FORMATS;
    const formatsAfter = formats[start] || EMPTY_ACTIVE_FORMATS;
    const destination = isReverse ? formatsBefore : formatsAfter;
    const isIncreasing = currentActiveFormats.every(
      (format, index) => format === destination[index]
    );
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
    const newValue = {
      ...record.current,
      activeFormats: newActiveFormats
    };
    record.current = newValue;
    applyRecord(newValue);
    forceRender();
  }
  element.addEventListener("keydown", onKeyDown);
  return () => {
    element.removeEventListener("keydown", onKeyDown);
  };
};


;// ./node_modules/@wordpress/rich-text/build-module/component/event-listeners/delete.js


var delete_default = (props) => (element) => {
  function onKeyDown(event) {
    const { keyCode } = event;
    const { createRecord, handleChange } = props.current;
    if (event.defaultPrevented) {
      return;
    }
    if (keyCode !== external_wp_keycodes_namespaceObject.DELETE && keyCode !== external_wp_keycodes_namespaceObject.BACKSPACE) {
      return;
    }
    const currentValue = createRecord();
    const { start, end, text } = currentValue;
    if (start === 0 && end !== 0 && end === text.length) {
      handleChange(remove_remove(currentValue));
      event.preventDefault();
    }
  }
  element.addEventListener("keydown", onKeyDown);
  return () => {
    element.removeEventListener("keydown", onKeyDown);
  };
};


;// ./node_modules/@wordpress/rich-text/build-module/update-formats.js

function updateFormats({ value, start, end, formats }) {
  const min = Math.min(start, end);
  const max = Math.max(start, end);
  const formatsBefore = value.formats[min - 1] || [];
  const formatsAfter = value.formats[max] || [];
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


;// ./node_modules/@wordpress/rich-text/build-module/component/event-listeners/input-and-selection.js


const INSERTION_INPUT_TYPES_TO_IGNORE = /* @__PURE__ */ new Set([
  "insertParagraph",
  "insertOrderedList",
  "insertUnorderedList",
  "insertHorizontalRule",
  "insertLink"
]);
const input_and_selection_EMPTY_ACTIVE_FORMATS = [];
const PLACEHOLDER_ATTR_NAME = "data-rich-text-placeholder";
function fixPlaceholderSelection(defaultView) {
  const selection = defaultView.getSelection();
  const { anchorNode, anchorOffset } = selection;
  if (anchorNode.nodeType !== anchorNode.ELEMENT_NODE) {
    return;
  }
  const targetNode = anchorNode.childNodes[anchorOffset];
  if (!targetNode || targetNode.nodeType !== targetNode.ELEMENT_NODE || !targetNode.hasAttribute(PLACEHOLDER_ATTR_NAME)) {
    return;
  }
  selection.collapseToStart();
}
var input_and_selection_default = (props) => (element) => {
  const { ownerDocument } = element;
  const { defaultView } = ownerDocument;
  let isComposing = false;
  function onInput(event) {
    if (isComposing) {
      return;
    }
    let inputType;
    if (event) {
      inputType = event.inputType;
    }
    const { record, applyRecord, createRecord, handleChange } = props.current;
    if (inputType && (inputType.indexOf("format") === 0 || INSERTION_INPUT_TYPES_TO_IGNORE.has(inputType))) {
      applyRecord(record.current);
      return;
    }
    const currentValue = createRecord();
    const { start, activeFormats: oldActiveFormats = [] } = record.current;
    const change = updateFormats({
      value: currentValue,
      start,
      end: currentValue.start,
      formats: oldActiveFormats
    });
    handleChange(change);
  }
  function handleSelectionChange() {
    const { record, applyRecord, createRecord, onSelectionChange } = props.current;
    if (element.contentEditable !== "true") {
      return;
    }
    if (ownerDocument.activeElement !== element) {
      ownerDocument.removeEventListener(
        "selectionchange",
        handleSelectionChange
      );
      return;
    }
    if (isComposing) {
      return;
    }
    const { start, end, text } = createRecord();
    const oldRecord = record.current;
    if (text !== oldRecord.text) {
      onInput();
      return;
    }
    if (start === oldRecord.start && end === oldRecord.end) {
      if (oldRecord.text.length === 0 && start === 0) {
        fixPlaceholderSelection(defaultView);
      }
      return;
    }
    const newValue = {
      ...oldRecord,
      start,
      end,
      // _newActiveFormats may be set on arrow key navigation to control
      // the right boundary position. If undefined, getActiveFormats will
      // give the active formats according to the browser.
      activeFormats: oldRecord._newActiveFormats,
      _newActiveFormats: void 0
    };
    const newActiveFormats = getActiveFormats(
      newValue,
      input_and_selection_EMPTY_ACTIVE_FORMATS
    );
    newValue.activeFormats = newActiveFormats;
    record.current = newValue;
    applyRecord(newValue, { domOnly: true });
    onSelectionChange(start, end);
  }
  function onCompositionStart() {
    isComposing = true;
    ownerDocument.removeEventListener(
      "selectionchange",
      handleSelectionChange
    );
    element.querySelector(`[${PLACEHOLDER_ATTR_NAME}]`)?.remove();
  }
  function onCompositionEnd() {
    isComposing = false;
    onInput({ inputType: "insertText" });
    ownerDocument.addEventListener(
      "selectionchange",
      handleSelectionChange
    );
  }
  function onFocus() {
    const { record, isSelected, onSelectionChange, applyRecord } = props.current;
    if (element.parentElement.closest('[contenteditable="true"]')) {
      return;
    }
    if (!isSelected) {
      const index = void 0;
      record.current = {
        ...record.current,
        start: index,
        end: index,
        activeFormats: input_and_selection_EMPTY_ACTIVE_FORMATS
      };
    } else {
      applyRecord(record.current, { domOnly: true });
    }
    onSelectionChange(record.current.start, record.current.end);
    window.queueMicrotask(handleSelectionChange);
    ownerDocument.addEventListener(
      "selectionchange",
      handleSelectionChange
    );
  }
  element.addEventListener("input", onInput);
  element.addEventListener("compositionstart", onCompositionStart);
  element.addEventListener("compositionend", onCompositionEnd);
  element.addEventListener("focus", onFocus);
  return () => {
    element.removeEventListener("input", onInput);
    element.removeEventListener("compositionstart", onCompositionStart);
    element.removeEventListener("compositionend", onCompositionEnd);
    element.removeEventListener("focus", onFocus);
  };
};


;// ./node_modules/@wordpress/rich-text/build-module/component/event-listeners/selection-change-compat.js

var selection_change_compat_default = () => (element) => {
  const { ownerDocument } = element;
  const { defaultView } = ownerDocument;
  const selection = defaultView?.getSelection();
  let range;
  function getRange() {
    return selection.rangeCount ? selection.getRangeAt(0) : null;
  }
  function onDown(event) {
    const type = event.type === "keydown" ? "keyup" : "pointerup";
    function onCancel() {
      ownerDocument.removeEventListener(type, onUp);
      ownerDocument.removeEventListener("selectionchange", onCancel);
      ownerDocument.removeEventListener("input", onCancel);
    }
    function onUp() {
      onCancel();
      if (isRangeEqual(range, getRange())) {
        return;
      }
      ownerDocument.dispatchEvent(new Event("selectionchange"));
    }
    ownerDocument.addEventListener(type, onUp);
    ownerDocument.addEventListener("selectionchange", onCancel);
    ownerDocument.addEventListener("input", onCancel);
    range = getRange();
  }
  element.addEventListener("pointerdown", onDown);
  element.addEventListener("keydown", onDown);
  return () => {
    element.removeEventListener("pointerdown", onDown);
    element.removeEventListener("keydown", onDown);
  };
};


;// ./node_modules/@wordpress/rich-text/build-module/component/event-listeners/prevent-focus-capture.js
function preventFocusCapture() {
  return (element) => {
    const { ownerDocument } = element;
    const { defaultView } = ownerDocument;
    let value = null;
    function onPointerDown(event) {
      if (event.defaultPrevented) {
        return;
      }
      if (event.target === element) {
        return;
      }
      if (!event.target.contains(element)) {
        return;
      }
      value = element.getAttribute("contenteditable");
      element.setAttribute("contenteditable", "false");
      defaultView.getSelection().removeAllRanges();
    }
    function onPointerUp() {
      if (value !== null) {
        element.setAttribute("contenteditable", value);
        value = null;
      }
    }
    defaultView.addEventListener("pointerdown", onPointerDown);
    defaultView.addEventListener("pointerup", onPointerUp);
    return () => {
      defaultView.removeEventListener("pointerdown", onPointerDown);
      defaultView.removeEventListener("pointerup", onPointerUp);
    };
  };
}


;// ./node_modules/@wordpress/rich-text/build-module/component/event-listeners/index.js









const allEventListeners = [
  copy_handler_default,
  select_object_default,
  format_boundaries_default,
  delete_default,
  input_and_selection_default,
  selection_change_compat_default,
  preventFocusCapture
];
function useEventListeners(props) {
  const propsRef = (0,external_wp_element_namespaceObject.useRef)(props);
  (0,external_wp_element_namespaceObject.useInsertionEffect)(() => {
    propsRef.current = props;
  });
  const refEffects = (0,external_wp_element_namespaceObject.useMemo)(
    () => allEventListeners.map((refEffect) => refEffect(propsRef)),
    [propsRef]
  );
  return (0,external_wp_compose_namespaceObject.useRefEffect)(
    (element) => {
      const cleanups = refEffects.map((effect) => effect(element));
      return () => {
        cleanups.forEach((cleanup) => cleanup());
      };
    },
    [refEffects]
  );
}


;// ./node_modules/@wordpress/rich-text/build-module/component/index.js









function useRichText({
  value = "",
  selectionStart,
  selectionEnd,
  placeholder,
  onSelectionChange,
  preserveWhiteSpace,
  onChange,
  __unstableDisableFormats: disableFormats,
  __unstableIsSelected: isSelected,
  __unstableDependencies = [],
  __unstableAfterParse,
  __unstableBeforeSerialize,
  __unstableAddInvisibleFormats
}) {
  const registry = (0,external_wp_data_namespaceObject.useRegistry)();
  const [, forceRender] = (0,external_wp_element_namespaceObject.useReducer)(() => ({}));
  const ref = (0,external_wp_element_namespaceObject.useRef)();
  function createRecord() {
    const {
      ownerDocument: { defaultView }
    } = ref.current;
    const selection = defaultView.getSelection();
    const range = selection.rangeCount > 0 ? selection.getRangeAt(0) : null;
    return create({
      element: ref.current,
      range,
      __unstableIsEditableTree: true
    });
  }
  function applyRecord(newRecord, { domOnly } = {}) {
    apply({
      value: newRecord,
      current: ref.current,
      prepareEditableTree: __unstableAddInvisibleFormats,
      __unstableDomOnly: domOnly,
      placeholder
    });
  }
  const _valueRef = (0,external_wp_element_namespaceObject.useRef)(value);
  const recordRef = (0,external_wp_element_namespaceObject.useRef)();
  function setRecordFromProps() {
    _valueRef.current = value;
    recordRef.current = value;
    if (!(value instanceof RichTextData)) {
      recordRef.current = value ? RichTextData.fromHTMLString(value, { preserveWhiteSpace }) : RichTextData.empty();
    }
    recordRef.current = {
      text: recordRef.current.text,
      formats: recordRef.current.formats,
      replacements: recordRef.current.replacements
    };
    if (disableFormats) {
      recordRef.current.formats = Array(value.length);
      recordRef.current.replacements = Array(value.length);
    }
    if (__unstableAfterParse) {
      recordRef.current.formats = __unstableAfterParse(
        recordRef.current
      );
    }
    recordRef.current.start = selectionStart;
    recordRef.current.end = selectionEnd;
  }
  const hadSelectionUpdateRef = (0,external_wp_element_namespaceObject.useRef)(false);
  if (!recordRef.current) {
    hadSelectionUpdateRef.current = isSelected;
    setRecordFromProps();
  } else if (selectionStart !== recordRef.current.start || selectionEnd !== recordRef.current.end) {
    hadSelectionUpdateRef.current = isSelected;
    recordRef.current = {
      ...recordRef.current,
      start: selectionStart,
      end: selectionEnd,
      activeFormats: void 0
    };
  }
  function handleChange(newRecord) {
    recordRef.current = newRecord;
    applyRecord(newRecord);
    if (disableFormats) {
      _valueRef.current = newRecord.text;
    } else {
      const newFormats = __unstableBeforeSerialize ? __unstableBeforeSerialize(newRecord) : newRecord.formats;
      newRecord = { ...newRecord, formats: newFormats };
      if (typeof value === "string") {
        _valueRef.current = toHTMLString({
          value: newRecord,
          preserveWhiteSpace
        });
      } else {
        _valueRef.current = new RichTextData(newRecord);
      }
    }
    const { start, end, formats, text } = recordRef.current;
    registry.batch(() => {
      onSelectionChange(start, end);
      onChange(_valueRef.current, {
        __unstableFormats: formats,
        __unstableText: text
      });
    });
    forceRender();
  }
  function applyFromProps() {
    const previousValue = _valueRef.current;
    setRecordFromProps();
    const contentLengthChanged = previousValue && typeof previousValue === "string" && typeof value === "string" && previousValue.length !== value.length;
    const hasFocus = ref.current?.contains(
      ref.current.ownerDocument.activeElement
    );
    const skipSelection = contentLengthChanged && !hasFocus;
    applyRecord(recordRef.current, { domOnly: skipSelection });
  }
  const didMountRef = (0,external_wp_element_namespaceObject.useRef)(false);
  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    if (didMountRef.current && value !== _valueRef.current) {
      applyFromProps();
      forceRender();
    }
  }, [value]);
  (0,external_wp_element_namespaceObject.useLayoutEffect)(() => {
    if (!hadSelectionUpdateRef.current) {
      return;
    }
    if (ref.current.ownerDocument.activeElement !== ref.current) {
      ref.current.focus();
    }
    applyRecord(recordRef.current);
    hadSelectionUpdateRef.current = false;
  }, [hadSelectionUpdateRef.current]);
  const mergedRefs = (0,external_wp_compose_namespaceObject.useMergeRefs)([
    ref,
    useDefaultStyle(),
    useBoundaryStyle({ record: recordRef }),
    useEventListeners({
      record: recordRef,
      handleChange,
      applyRecord,
      createRecord,
      isSelected,
      onSelectionChange,
      forceRender
    }),
    (0,external_wp_compose_namespaceObject.useRefEffect)(() => {
      applyFromProps();
      didMountRef.current = true;
    }, [placeholder, ...__unstableDependencies])
  ]);
  return {
    value: recordRef.current,
    // A function to get the most recent value so event handlers in
    // useRichText implementations have access to it. For example when
    // listening to input events, we internally update the state, but this
    // state is not yet available to the input event handler because React
    // may re-render asynchronously.
    getValue: () => recordRef.current,
    onChange: handleChange,
    ref: mergedRefs
  };
}
function __experimentalRichText() {
}


;// ./node_modules/@wordpress/rich-text/build-module/index.js





























(window.wp = window.wp || {}).richText = __webpack_exports__;
/******/ })()
;