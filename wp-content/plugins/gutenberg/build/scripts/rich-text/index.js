"use strict";
var wp;
(wp ||= {}).richText = (() => {
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

  // package-external:@wordpress/data
  var require_data = __commonJS({
    "package-external:@wordpress/data"(exports, module) {
      module.exports = window.wp.data;
    }
  });

  // package-external:@wordpress/escape-html
  var require_escape_html = __commonJS({
    "package-external:@wordpress/escape-html"(exports, module) {
      module.exports = window.wp.escapeHtml;
    }
  });

  // package-external:@wordpress/a11y
  var require_a11y = __commonJS({
    "package-external:@wordpress/a11y"(exports, module) {
      module.exports = window.wp.a11y;
    }
  });

  // package-external:@wordpress/i18n
  var require_i18n = __commonJS({
    "package-external:@wordpress/i18n"(exports, module) {
      module.exports = window.wp.i18n;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/deprecated
  var require_deprecated = __commonJS({
    "package-external:@wordpress/deprecated"(exports, module) {
      module.exports = window.wp.deprecated;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/keycodes
  var require_keycodes = __commonJS({
    "package-external:@wordpress/keycodes"(exports, module) {
      module.exports = window.wp.keycodes;
    }
  });

  // packages/rich-text/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    RichTextData: () => RichTextData,
    __experimentalRichText: () => __experimentalRichText,
    __unstableCreateElement: () => createElement,
    __unstableToDom: () => toDom,
    __unstableUseRichText: () => useRichText,
    applyFormat: () => applyFormat,
    concat: () => concat,
    create: () => create,
    getActiveFormat: () => getActiveFormat,
    getActiveFormats: () => getActiveFormats,
    getActiveObject: () => getActiveObject,
    getTextContent: () => getTextContent,
    insert: () => insert,
    insertObject: () => insertObject,
    isCollapsed: () => isCollapsed,
    isEmpty: () => isEmpty,
    join: () => join,
    registerFormatType: () => registerFormatType,
    remove: () => remove2,
    removeFormat: () => removeFormat,
    replace: () => replace2,
    slice: () => slice,
    split: () => split,
    store: () => store,
    toHTMLString: () => toHTMLString,
    toggleFormat: () => toggleFormat,
    unregisterFormatType: () => unregisterFormatType,
    useAnchor: () => useAnchor,
    useAnchorRef: () => useAnchorRef
  });

  // packages/rich-text/build-module/store/index.js
  var import_data3 = __toESM(require_data());

  // packages/rich-text/build-module/store/reducer.js
  var import_data = __toESM(require_data());
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
  var reducer_default = (0, import_data.combineReducers)({ formatTypes });

  // packages/rich-text/build-module/store/selectors.js
  var selectors_exports = {};
  __export(selectors_exports, {
    getFormatType: () => getFormatType,
    getFormatTypeForBareElement: () => getFormatTypeForBareElement,
    getFormatTypeForClassName: () => getFormatTypeForClassName,
    getFormatTypes: () => getFormatTypes
  });
  var import_data2 = __toESM(require_data());
  var getFormatTypes = (0, import_data2.createSelector)(
    (state) => Object.values(state.formatTypes),
    (state) => [state.formatTypes]
  );
  function getFormatType(state, name) {
    return state.formatTypes[name];
  }
  function getFormatTypeForBareElement(state, bareElementTagName) {
    const formatTypes2 = getFormatTypes(state);
    return formatTypes2.find(({ className, tagName }) => {
      return className === null && bareElementTagName === tagName;
    }) || formatTypes2.find(({ className, tagName }) => {
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

  // packages/rich-text/build-module/store/actions.js
  var actions_exports = {};
  __export(actions_exports, {
    addFormatTypes: () => addFormatTypes,
    removeFormatTypes: () => removeFormatTypes
  });
  function addFormatTypes(formatTypes2) {
    return {
      type: "ADD_FORMAT_TYPES",
      formatTypes: Array.isArray(formatTypes2) ? formatTypes2 : [formatTypes2]
    };
  }
  function removeFormatTypes(names) {
    return {
      type: "REMOVE_FORMAT_TYPES",
      names: Array.isArray(names) ? names : [names]
    };
  }

  // packages/rich-text/build-module/store/index.js
  var STORE_NAME = "core/rich-text";
  var store = (0, import_data3.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    selectors: selectors_exports,
    actions: actions_exports
  });
  (0, import_data3.register)(store);

  // packages/rich-text/build-module/is-format-equal.js
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
    for (let i2 = 0; i2 < length; i2++) {
      const name = keys1[i2];
      if (attributes1[name] !== attributes2[name]) {
        return false;
      }
    }
    return true;
  }

  // packages/rich-text/build-module/normalise-formats.js
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

  // packages/rich-text/build-module/apply-format.js
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

  // packages/rich-text/build-module/create.js
  var import_data5 = __toESM(require_data());

  // packages/rich-text/build-module/create-element.js
  function createElement({ implementation }, html) {
    if (!createElement.body) {
      createElement.body = implementation.createHTMLDocument("").body;
    }
    createElement.body.innerHTML = html;
    return createElement.body;
  }

  // packages/rich-text/build-module/special-characters.js
  var OBJECT_REPLACEMENT_CHARACTER = "\uFFFC";
  var ZWNBSP = "\uFEFF";

  // packages/rich-text/build-module/to-html-string.js
  var import_escape_html = __toESM(require_escape_html());

  // packages/rich-text/build-module/get-active-formats.js
  function getActiveFormats(value, EMPTY_ACTIVE_FORMATS3 = []) {
    const { formats, start, end, activeFormats } = value;
    if (start === void 0) {
      return EMPTY_ACTIVE_FORMATS3;
    }
    if (start === end) {
      if (activeFormats) {
        return activeFormats;
      }
      const formatsBefore = formats[start - 1] || EMPTY_ACTIVE_FORMATS3;
      const formatsAfter = formats[start] || EMPTY_ACTIVE_FORMATS3;
      if (formatsBefore.length < formatsAfter.length) {
        return formatsBefore;
      }
      return formatsAfter;
    }
    if (!formats[start]) {
      return EMPTY_ACTIVE_FORMATS3;
    }
    const selectedFormats = formats.slice(start, end);
    const _activeFormats = [...selectedFormats[0]];
    let i2 = selectedFormats.length;
    while (i2--) {
      const formatsAtIndex = selectedFormats[i2];
      if (!formatsAtIndex) {
        return EMPTY_ACTIVE_FORMATS3;
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
        return EMPTY_ACTIVE_FORMATS3;
      }
    }
    return _activeFormats || EMPTY_ACTIVE_FORMATS3;
  }

  // packages/rich-text/build-module/get-format-type.js
  var import_data4 = __toESM(require_data());
  function getFormatType2(name) {
    return (0, import_data4.select)(store).getFormatType(name);
  }

  // packages/rich-text/build-module/to-tree.js
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
    const formatType = getFormatType2(type);
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
  function isEqualUntil(a2, b2, index) {
    do {
      if (a2[index] !== b2[index]) {
        return false;
      }
    } while (index--);
    return true;
  }
  function toTree({
    value,
    preserveWhiteSpace,
    createEmpty: createEmpty2,
    append: append3,
    getLastChild: getLastChild3,
    getParent: getParent3,
    isText: isText3,
    getText: getText3,
    remove: remove4,
    appendText: appendText3,
    onStartIndex,
    onEndIndex,
    isEditableTree,
    placeholder
  }) {
    const { formats, replacements, text, start, end } = value;
    const formatsLength = formats.length + 1;
    const tree = createEmpty2();
    const activeFormats = getActiveFormats(value);
    const deepestActiveFormat = activeFormats[activeFormats.length - 1];
    let lastCharacterFormats;
    let lastCharacter;
    append3(tree, "");
    for (let i2 = 0; i2 < formatsLength; i2++) {
      const character = text.charAt(i2);
      const shouldInsertPadding = isEditableTree && // Pad the line if the line is empty.
      (!lastCharacter || // Pad the line if the previous character is a line break, otherwise
      // the line break won't be visible.
      lastCharacter === "\n");
      const characterFormats = formats[i2];
      let pointer = getLastChild3(tree);
      if (characterFormats) {
        characterFormats.forEach((format, formatIndex) => {
          if (pointer && lastCharacterFormats && // Reuse the last element if all formats remain the same.
          isEqualUntil(
            characterFormats,
            lastCharacterFormats,
            formatIndex
          )) {
            pointer = getLastChild3(pointer);
            return;
          }
          const { type, tagName, attributes, unregisteredAttributes } = format;
          const boundaryClass = isEditableTree && format === deepestActiveFormat;
          const parent = getParent3(pointer);
          const newNode = append3(
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
          if (isText3(pointer) && getText3(pointer).length === 0) {
            remove4(pointer);
          }
          pointer = append3(newNode, "");
        });
      }
      if (i2 === 0) {
        if (onStartIndex && start === 0) {
          onStartIndex(tree, pointer);
        }
        if (onEndIndex && end === 0) {
          onEndIndex(tree, pointer);
        }
      }
      if (character === OBJECT_REPLACEMENT_CHARACTER) {
        const replacement = replacements[i2];
        if (!replacement) {
          continue;
        }
        const { type, attributes, innerHTML } = replacement;
        const formatType = getFormatType2(type);
        if (isEditableTree && type === "#comment") {
          pointer = append3(getParent3(pointer), {
            type: "span",
            attributes: {
              contenteditable: "false",
              "data-rich-text-comment": attributes["data-rich-text-comment"]
            }
          });
          append3(
            append3(pointer, { type: "span" }),
            attributes["data-rich-text-comment"].trim()
          );
        } else if (!isEditableTree && type === "script") {
          pointer = append3(
            getParent3(pointer),
            fromFormat({
              type: "script",
              isEditableTree
            })
          );
          append3(pointer, {
            html: decodeURIComponent(
              attributes["data-rich-text-script"]
            )
          });
        } else if (formatType?.contentEditable === false) {
          if (innerHTML || isEditableTree) {
            pointer = getParent3(pointer);
            if (isEditableTree) {
              const attrs = {
                contenteditable: "false",
                "data-rich-text-bogus": true
              };
              if (start === i2 && end === i2 + 1) {
                attrs["data-rich-text-format-boundary"] = true;
              }
              pointer = append3(pointer, {
                type: "span",
                attributes: attrs
              });
              if (isEditableTree && i2 + 1 === text.length) {
                append3(getParent3(pointer), ZWNBSP);
              }
            }
            pointer = append3(
              pointer,
              fromFormat({
                ...replacement,
                isEditableTree
              })
            );
            if (innerHTML) {
              append3(pointer, {
                html: innerHTML
              });
            }
          }
        } else {
          pointer = append3(
            getParent3(pointer),
            fromFormat({
              ...replacement,
              object: true,
              isEditableTree
            })
          );
        }
        pointer = append3(getParent3(pointer), "");
      } else if (!preserveWhiteSpace && character === "\n") {
        pointer = append3(getParent3(pointer), {
          type: "br",
          attributes: isEditableTree ? {
            "data-rich-text-line-break": "true"
          } : void 0,
          object: true
        });
        pointer = append3(getParent3(pointer), "");
      } else if (!isText3(pointer)) {
        pointer = append3(getParent3(pointer), character);
      } else {
        appendText3(pointer, character);
      }
      if (onStartIndex && start === i2 + 1) {
        onStartIndex(tree, pointer);
      }
      if (onEndIndex && end === i2 + 1) {
        onEndIndex(tree, pointer);
      }
      if (shouldInsertPadding && i2 === text.length) {
        append3(getParent3(pointer), ZWNBSP);
        if (placeholder && text.length === 0) {
          append3(getParent3(pointer), {
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

  // packages/rich-text/build-module/to-html-string.js
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
      if (!(0, import_escape_html.isValidAttributeName)(key)) {
        continue;
      }
      attributeString += ` ${key}="${(0, import_escape_html.escapeAttribute)(
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
      return child.text === void 0 ? createElementHTML(child) : (0, import_escape_html.escapeEditableHTML)(child.text);
    }).join("");
  }

  // packages/rich-text/build-module/get-text-content.js
  function getTextContent({ text }) {
    return text.replace(OBJECT_REPLACEMENT_CHARACTER, "");
  }

  // packages/rich-text/build-module/create.js
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
      formatType = (0, import_data5.select)(store).getFormatTypeForClassName(
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
      formatType = (0, import_data5.select)(store).getFormatTypeForBareElement(tagName);
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
  var RichTextData = class _RichTextData {
    #value;
    static empty() {
      return new _RichTextData();
    }
    static fromPlainText(text) {
      return new _RichTextData(create({ text }));
    }
    static fromHTMLString(html) {
      return new _RichTextData(create({ html }));
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
      const richTextData = new _RichTextData(create({ element }));
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
  };
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
    Array.from(clone.childNodes).forEach((node, i2, nodes) => {
      if (node.nodeType === node.TEXT_NODE) {
        let newNodeValue = node.nodeValue;
        if (/[\n\t\r\f]/.test(newNodeValue)) {
          newNodeValue = newNodeValue.replace(/[\n\t\r\f]+/g, " ");
        }
        if (newNodeValue.indexOf("  ") !== -1) {
          newNodeValue = newNodeValue.replace(/ {2,}/g, " ");
        }
        if (i2 === 0 && newNodeValue.startsWith(" ")) {
          newNodeValue = newNodeValue.slice(1);
        } else if (isRoot && i2 === nodes.length - 1 && newNodeValue.endsWith(" ")) {
          newNodeValue = newNodeValue.slice(0, -1);
        }
        node.nodeValue = newNodeValue;
      } else if (node.nodeType === node.ELEMENT_NODE) {
        node.replaceWith(collapseWhiteSpace(node, false));
      }
    });
    return clone;
  }
  var CARRIAGE_RETURN = "\r";
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
    for (let i2 = 0; i2 < length; i2++) {
      const { name, value } = element.attributes[i2];
      if (name.indexOf("data-rich-text-") === 0) {
        continue;
      }
      const safeName = /^on/i.test(name) ? "data-disable-rich-text-" + name : name;
      accumulator = accumulator || {};
      accumulator[safeName] = value;
    }
    return accumulator;
  }

  // packages/rich-text/build-module/concat.js
  function mergePair(a2, b2) {
    a2.formats = a2.formats.concat(b2.formats);
    a2.replacements = a2.replacements.concat(b2.replacements);
    a2.text += b2.text;
    return a2;
  }
  function concat(...values) {
    return normaliseFormats(values.reduce(mergePair, create()));
  }

  // packages/rich-text/build-module/get-active-format.js
  function getActiveFormat(value, formatType) {
    return getActiveFormats(value).find(
      ({ type }) => type === formatType
    );
  }

  // packages/rich-text/build-module/get-active-object.js
  function getActiveObject({ start, end, replacements, text }) {
    if (start + 1 !== end || text[start] !== OBJECT_REPLACEMENT_CHARACTER) {
      return;
    }
    return replacements[start];
  }

  // packages/rich-text/build-module/is-collapsed.js
  function isCollapsed({
    start,
    end
  }) {
    if (start === void 0 || end === void 0) {
      return;
    }
    return start === end;
  }

  // packages/rich-text/build-module/is-empty.js
  function isEmpty({ text }) {
    return text.length === 0;
  }

  // packages/rich-text/build-module/join.js
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

  // packages/rich-text/build-module/register-format-type.js
  var import_data6 = __toESM(require_data());
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
    if ((0, import_data6.select)(store).getFormatType(settings.name)) {
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
      const formatTypeForBareElement = (0, import_data6.select)(
        store
      ).getFormatTypeForBareElement(settings.tagName);
      if (formatTypeForBareElement && formatTypeForBareElement.name !== "core/unknown") {
        window.console.error(
          `Format "${formatTypeForBareElement.name}" is already registered to handle bare tag name "${settings.tagName}".`
        );
        return;
      }
    } else {
      const formatTypeForClassName = (0, import_data6.select)(
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
    (0, import_data6.dispatch)(store).addFormatTypes(settings);
    return settings;
  }

  // packages/rich-text/build-module/remove-format.js
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
      for (let i2 = startIndex; i2 < endIndex; i2++) {
        if (newFormats[i2]) {
          filterFormats(newFormats, i2, formatType);
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

  // packages/rich-text/build-module/insert.js
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

  // packages/rich-text/build-module/remove.js
  function remove2(value, startIndex, endIndex) {
    return insert(value, create(), startIndex, endIndex);
  }

  // packages/rich-text/build-module/replace.js
  function replace2({ formats, replacements, text, start, end }, pattern, replacement) {
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

  // packages/rich-text/build-module/insert-object.js
  function insertObject(value, formatToInsert, startIndex, endIndex) {
    const valueToInsert = {
      formats: [,],
      replacements: [formatToInsert],
      text: OBJECT_REPLACEMENT_CHARACTER
    };
    return insert(value, valueToInsert, startIndex, endIndex);
  }

  // packages/rich-text/build-module/slice.js
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

  // packages/rich-text/build-module/split.js
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

  // packages/rich-text/build-module/is-range-equal.js
  function isRangeEqual(a2, b2) {
    return a2 === b2 || a2 && b2 && a2.startContainer === b2.startContainer && a2.startOffset === b2.startOffset && a2.endContainer === b2.endContainer && a2.endOffset === b2.endOffset;
  }

  // packages/rich-text/build-module/to-dom.js
  var MATHML_NAMESPACE = "http://www.w3.org/1998/Math/MathML";
  function createPathToNode(node, rootNode, path) {
    const parentNode = node.parentNode;
    let i2 = 0;
    while (node = node.previousSibling) {
      i2++;
    }
    path = [i2, ...path];
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
  function append2(element, child) {
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
  function appendText2(node, text) {
    node.appendData(text);
  }
  function getLastChild2({ lastChild }) {
    return lastChild;
  }
  function getParent2({ parentNode }) {
    return parentNode;
  }
  function isText2(node) {
    return node.nodeType === node.TEXT_NODE;
  }
  function getText2({ nodeValue }) {
    return nodeValue;
  }
  function remove3(node) {
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
    const createEmpty2 = () => createElement(doc, "");
    const tree = toTree({
      value,
      createEmpty: createEmpty2,
      append: append2,
      getLastChild: getLastChild2,
      getParent: getParent2,
      isText: isText2,
      getText: getText2,
      remove: remove3,
      appendText: appendText2,
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
    let i2 = 0;
    let futureChild;
    while (futureChild = future.firstChild) {
      const currentChild = current.childNodes[i2];
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
      i2++;
    }
    while (current.childNodes[i2]) {
      current.removeChild(current.childNodes[i2]);
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

  // packages/rich-text/build-module/toggle-format.js
  var import_a11y = __toESM(require_a11y());
  var import_i18n = __toESM(require_i18n());
  function toggleFormat(value, format) {
    if (getActiveFormat(value, format.type)) {
      if (format.title) {
        (0, import_a11y.speak)((0, import_i18n.sprintf)((0, import_i18n.__)("%s removed."), format.title), "assertive");
      }
      return removeFormat(value, format.type);
    }
    if (format.title) {
      (0, import_a11y.speak)((0, import_i18n.sprintf)((0, import_i18n.__)("%s applied."), format.title), "assertive");
    }
    return applyFormat(value, format);
  }

  // packages/rich-text/build-module/unregister-format-type.js
  var import_data7 = __toESM(require_data());
  function unregisterFormatType(name) {
    const oldFormat = (0, import_data7.select)(store).getFormatType(name);
    if (!oldFormat) {
      window.console.error(`Format ${name} is not registered.`);
      return;
    }
    (0, import_data7.dispatch)(store).removeFormatTypes(name);
    return oldFormat;
  }

  // packages/rich-text/build-module/component/use-anchor-ref.js
  var import_element = __toESM(require_element());
  var import_deprecated = __toESM(require_deprecated());
  function useAnchorRef({ ref, value, settings = {} }) {
    (0, import_deprecated.default)("`useAnchorRef` hook", {
      since: "6.1",
      alternative: "`useAnchor` hook"
    });
    const { tagName, className, name } = settings;
    const activeFormat = name ? getActiveFormat(value, name) : void 0;
    return (0, import_element.useMemo)(() => {
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

  // packages/rich-text/build-module/component/use-anchor.js
  var import_compose = __toESM(require_compose());
  var import_element2 = __toESM(require_element());
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
    const [anchor, setAnchor] = (0, import_element2.useState)(
      () => getAnchor(editableContentElement, tagName, className)
    );
    const wasActive = (0, import_compose.usePrevious)(isActive);
    (0, import_element2.useLayoutEffect)(() => {
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

  // packages/rich-text/build-module/component/index.js
  var import_element6 = __toESM(require_element());
  var import_compose3 = __toESM(require_compose());
  var import_data8 = __toESM(require_data());

  // packages/rich-text/build-module/component/use-default-style.js
  var import_element3 = __toESM(require_element());
  var whiteSpace = "pre-wrap";
  var minWidth = "1px";
  function useDefaultStyle() {
    return (0, import_element3.useCallback)((element) => {
      if (!element) {
        return;
      }
      element.style.whiteSpace = whiteSpace;
      element.style.minWidth = minWidth;
    }, []);
  }

  // node_modules/colord/index.mjs
  var r = { grad: 0.9, turn: 360, rad: 360 / (2 * Math.PI) };
  var t = function(r2) {
    return "string" == typeof r2 ? r2.length > 0 : "number" == typeof r2;
  };
  var n = function(r2, t2, n2) {
    return void 0 === t2 && (t2 = 0), void 0 === n2 && (n2 = Math.pow(10, t2)), Math.round(n2 * r2) / n2 + 0;
  };
  var e = function(r2, t2, n2) {
    return void 0 === t2 && (t2 = 0), void 0 === n2 && (n2 = 1), r2 > n2 ? n2 : r2 > t2 ? r2 : t2;
  };
  var u = function(r2) {
    return (r2 = isFinite(r2) ? r2 % 360 : 0) > 0 ? r2 : r2 + 360;
  };
  var a = function(r2) {
    return { r: e(r2.r, 0, 255), g: e(r2.g, 0, 255), b: e(r2.b, 0, 255), a: e(r2.a) };
  };
  var o = function(r2) {
    return { r: n(r2.r), g: n(r2.g), b: n(r2.b), a: n(r2.a, 3) };
  };
  var i = /^#([0-9a-f]{3,8})$/i;
  var s = function(r2) {
    var t2 = r2.toString(16);
    return t2.length < 2 ? "0" + t2 : t2;
  };
  var h = function(r2) {
    var t2 = r2.r, n2 = r2.g, e2 = r2.b, u2 = r2.a, a2 = Math.max(t2, n2, e2), o2 = a2 - Math.min(t2, n2, e2), i2 = o2 ? a2 === t2 ? (n2 - e2) / o2 : a2 === n2 ? 2 + (e2 - t2) / o2 : 4 + (t2 - n2) / o2 : 0;
    return { h: 60 * (i2 < 0 ? i2 + 6 : i2), s: a2 ? o2 / a2 * 100 : 0, v: a2 / 255 * 100, a: u2 };
  };
  var b = function(r2) {
    var t2 = r2.h, n2 = r2.s, e2 = r2.v, u2 = r2.a;
    t2 = t2 / 360 * 6, n2 /= 100, e2 /= 100;
    var a2 = Math.floor(t2), o2 = e2 * (1 - n2), i2 = e2 * (1 - (t2 - a2) * n2), s2 = e2 * (1 - (1 - t2 + a2) * n2), h2 = a2 % 6;
    return { r: 255 * [e2, i2, o2, o2, s2, e2][h2], g: 255 * [s2, e2, e2, i2, o2, o2][h2], b: 255 * [o2, o2, s2, e2, e2, i2][h2], a: u2 };
  };
  var g = function(r2) {
    return { h: u(r2.h), s: e(r2.s, 0, 100), l: e(r2.l, 0, 100), a: e(r2.a) };
  };
  var d = function(r2) {
    return { h: n(r2.h), s: n(r2.s), l: n(r2.l), a: n(r2.a, 3) };
  };
  var f = function(r2) {
    return b((n2 = (t2 = r2).s, { h: t2.h, s: (n2 *= ((e2 = t2.l) < 50 ? e2 : 100 - e2) / 100) > 0 ? 2 * n2 / (e2 + n2) * 100 : 0, v: e2 + n2, a: t2.a }));
    var t2, n2, e2;
  };
  var c = function(r2) {
    return { h: (t2 = h(r2)).h, s: (u2 = (200 - (n2 = t2.s)) * (e2 = t2.v) / 100) > 0 && u2 < 200 ? n2 * e2 / 100 / (u2 <= 100 ? u2 : 200 - u2) * 100 : 0, l: u2 / 2, a: t2.a };
    var t2, n2, e2, u2;
  };
  var l = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s*,\s*([+-]?\d*\.?\d+)%\s*,\s*([+-]?\d*\.?\d+)%\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
  var p = /^hsla?\(\s*([+-]?\d*\.?\d+)(deg|rad|grad|turn)?\s+([+-]?\d*\.?\d+)%\s+([+-]?\d*\.?\d+)%\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
  var v = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*,\s*([+-]?\d*\.?\d+)(%)?\s*(?:,\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
  var m = /^rgba?\(\s*([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s+([+-]?\d*\.?\d+)(%)?\s*(?:\/\s*([+-]?\d*\.?\d+)(%)?\s*)?\)$/i;
  var y = { string: [[function(r2) {
    var t2 = i.exec(r2);
    return t2 ? (r2 = t2[1]).length <= 4 ? { r: parseInt(r2[0] + r2[0], 16), g: parseInt(r2[1] + r2[1], 16), b: parseInt(r2[2] + r2[2], 16), a: 4 === r2.length ? n(parseInt(r2[3] + r2[3], 16) / 255, 2) : 1 } : 6 === r2.length || 8 === r2.length ? { r: parseInt(r2.substr(0, 2), 16), g: parseInt(r2.substr(2, 2), 16), b: parseInt(r2.substr(4, 2), 16), a: 8 === r2.length ? n(parseInt(r2.substr(6, 2), 16) / 255, 2) : 1 } : null : null;
  }, "hex"], [function(r2) {
    var t2 = v.exec(r2) || m.exec(r2);
    return t2 ? t2[2] !== t2[4] || t2[4] !== t2[6] ? null : a({ r: Number(t2[1]) / (t2[2] ? 100 / 255 : 1), g: Number(t2[3]) / (t2[4] ? 100 / 255 : 1), b: Number(t2[5]) / (t2[6] ? 100 / 255 : 1), a: void 0 === t2[7] ? 1 : Number(t2[7]) / (t2[8] ? 100 : 1) }) : null;
  }, "rgb"], [function(t2) {
    var n2 = l.exec(t2) || p.exec(t2);
    if (!n2) return null;
    var e2, u2, a2 = g({ h: (e2 = n2[1], u2 = n2[2], void 0 === u2 && (u2 = "deg"), Number(e2) * (r[u2] || 1)), s: Number(n2[3]), l: Number(n2[4]), a: void 0 === n2[5] ? 1 : Number(n2[5]) / (n2[6] ? 100 : 1) });
    return f(a2);
  }, "hsl"]], object: [[function(r2) {
    var n2 = r2.r, e2 = r2.g, u2 = r2.b, o2 = r2.a, i2 = void 0 === o2 ? 1 : o2;
    return t(n2) && t(e2) && t(u2) ? a({ r: Number(n2), g: Number(e2), b: Number(u2), a: Number(i2) }) : null;
  }, "rgb"], [function(r2) {
    var n2 = r2.h, e2 = r2.s, u2 = r2.l, a2 = r2.a, o2 = void 0 === a2 ? 1 : a2;
    if (!t(n2) || !t(e2) || !t(u2)) return null;
    var i2 = g({ h: Number(n2), s: Number(e2), l: Number(u2), a: Number(o2) });
    return f(i2);
  }, "hsl"], [function(r2) {
    var n2 = r2.h, a2 = r2.s, o2 = r2.v, i2 = r2.a, s2 = void 0 === i2 ? 1 : i2;
    if (!t(n2) || !t(a2) || !t(o2)) return null;
    var h2 = (function(r3) {
      return { h: u(r3.h), s: e(r3.s, 0, 100), v: e(r3.v, 0, 100), a: e(r3.a) };
    })({ h: Number(n2), s: Number(a2), v: Number(o2), a: Number(s2) });
    return b(h2);
  }, "hsv"]] };
  var N = function(r2, t2) {
    for (var n2 = 0; n2 < t2.length; n2++) {
      var e2 = t2[n2][0](r2);
      if (e2) return [e2, t2[n2][1]];
    }
    return [null, void 0];
  };
  var x = function(r2) {
    return "string" == typeof r2 ? N(r2.trim(), y.string) : "object" == typeof r2 && null !== r2 ? N(r2, y.object) : [null, void 0];
  };
  var M = function(r2, t2) {
    var n2 = c(r2);
    return { h: n2.h, s: e(n2.s + 100 * t2, 0, 100), l: n2.l, a: n2.a };
  };
  var H = function(r2) {
    return (299 * r2.r + 587 * r2.g + 114 * r2.b) / 1e3 / 255;
  };
  var $ = function(r2, t2) {
    var n2 = c(r2);
    return { h: n2.h, s: n2.s, l: e(n2.l + 100 * t2, 0, 100), a: n2.a };
  };
  var j = (function() {
    function r2(r3) {
      this.parsed = x(r3)[0], this.rgba = this.parsed || { r: 0, g: 0, b: 0, a: 1 };
    }
    return r2.prototype.isValid = function() {
      return null !== this.parsed;
    }, r2.prototype.brightness = function() {
      return n(H(this.rgba), 2);
    }, r2.prototype.isDark = function() {
      return H(this.rgba) < 0.5;
    }, r2.prototype.isLight = function() {
      return H(this.rgba) >= 0.5;
    }, r2.prototype.toHex = function() {
      return r3 = o(this.rgba), t2 = r3.r, e2 = r3.g, u2 = r3.b, i2 = (a2 = r3.a) < 1 ? s(n(255 * a2)) : "", "#" + s(t2) + s(e2) + s(u2) + i2;
      var r3, t2, e2, u2, a2, i2;
    }, r2.prototype.toRgb = function() {
      return o(this.rgba);
    }, r2.prototype.toRgbString = function() {
      return r3 = o(this.rgba), t2 = r3.r, n2 = r3.g, e2 = r3.b, (u2 = r3.a) < 1 ? "rgba(" + t2 + ", " + n2 + ", " + e2 + ", " + u2 + ")" : "rgb(" + t2 + ", " + n2 + ", " + e2 + ")";
      var r3, t2, n2, e2, u2;
    }, r2.prototype.toHsl = function() {
      return d(c(this.rgba));
    }, r2.prototype.toHslString = function() {
      return r3 = d(c(this.rgba)), t2 = r3.h, n2 = r3.s, e2 = r3.l, (u2 = r3.a) < 1 ? "hsla(" + t2 + ", " + n2 + "%, " + e2 + "%, " + u2 + ")" : "hsl(" + t2 + ", " + n2 + "%, " + e2 + "%)";
      var r3, t2, n2, e2, u2;
    }, r2.prototype.toHsv = function() {
      return r3 = h(this.rgba), { h: n(r3.h), s: n(r3.s), v: n(r3.v), a: n(r3.a, 3) };
      var r3;
    }, r2.prototype.invert = function() {
      return w({ r: 255 - (r3 = this.rgba).r, g: 255 - r3.g, b: 255 - r3.b, a: r3.a });
      var r3;
    }, r2.prototype.saturate = function(r3) {
      return void 0 === r3 && (r3 = 0.1), w(M(this.rgba, r3));
    }, r2.prototype.desaturate = function(r3) {
      return void 0 === r3 && (r3 = 0.1), w(M(this.rgba, -r3));
    }, r2.prototype.grayscale = function() {
      return w(M(this.rgba, -1));
    }, r2.prototype.lighten = function(r3) {
      return void 0 === r3 && (r3 = 0.1), w($(this.rgba, r3));
    }, r2.prototype.darken = function(r3) {
      return void 0 === r3 && (r3 = 0.1), w($(this.rgba, -r3));
    }, r2.prototype.rotate = function(r3) {
      return void 0 === r3 && (r3 = 15), this.hue(this.hue() + r3);
    }, r2.prototype.alpha = function(r3) {
      return "number" == typeof r3 ? w({ r: (t2 = this.rgba).r, g: t2.g, b: t2.b, a: r3 }) : n(this.rgba.a, 3);
      var t2;
    }, r2.prototype.hue = function(r3) {
      var t2 = c(this.rgba);
      return "number" == typeof r3 ? w({ h: r3, s: t2.s, l: t2.l, a: t2.a }) : n(t2.h);
    }, r2.prototype.isEqual = function(r3) {
      return this.toHex() === w(r3).toHex();
    }, r2;
  })();
  var w = function(r2) {
    return r2 instanceof j ? r2 : new j(r2);
  };

  // packages/rich-text/build-module/component/use-boundary-style.js
  var import_element4 = __toESM(require_element());
  function useBoundaryStyle({ record }) {
    const ref = (0, import_element4.useRef)();
    const { activeFormats = [], replacements, start } = record.current;
    const activeReplacement = replacements[start];
    (0, import_element4.useEffect)(() => {
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

  // packages/rich-text/build-module/component/event-listeners/index.js
  var import_element5 = __toESM(require_element());
  var import_compose2 = __toESM(require_compose());

  // packages/rich-text/build-module/component/event-listeners/copy-handler.js
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

  // packages/rich-text/build-module/component/event-listeners/select-object.js
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

  // packages/rich-text/build-module/component/event-listeners/format-boundaries.js
  var import_keycodes = __toESM(require_keycodes());
  var EMPTY_ACTIVE_FORMATS = [];
  var format_boundaries_default = (props) => (element) => {
    function onKeyDown(event) {
      const { keyCode, shiftKey, altKey, metaKey, ctrlKey } = event;
      if (
        // Only override left and right keys without modifiers pressed.
        shiftKey || altKey || metaKey || ctrlKey || keyCode !== import_keycodes.LEFT && keyCode !== import_keycodes.RIGHT
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
      const reverseKey = direction === "rtl" ? import_keycodes.RIGHT : import_keycodes.LEFT;
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

  // packages/rich-text/build-module/component/event-listeners/delete.js
  var import_keycodes2 = __toESM(require_keycodes());
  var delete_default = (props) => (element) => {
    function onKeyDown(event) {
      const { keyCode } = event;
      const { createRecord, handleChange } = props.current;
      if (event.defaultPrevented) {
        return;
      }
      if (keyCode !== import_keycodes2.DELETE && keyCode !== import_keycodes2.BACKSPACE) {
        return;
      }
      const currentValue = createRecord();
      const { start, end, text } = currentValue;
      if (start === 0 && end !== 0 && end === text.length) {
        handleChange(remove2(currentValue));
        event.preventDefault();
      }
    }
    element.addEventListener("keydown", onKeyDown);
    return () => {
      element.removeEventListener("keydown", onKeyDown);
    };
  };

  // packages/rich-text/build-module/update-formats.js
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

  // packages/rich-text/build-module/component/event-listeners/input-and-selection.js
  var INSERTION_INPUT_TYPES_TO_IGNORE = /* @__PURE__ */ new Set([
    "insertParagraph",
    "insertOrderedList",
    "insertUnorderedList",
    "insertHorizontalRule",
    "insertLink"
  ]);
  var EMPTY_ACTIVE_FORMATS2 = [];
  var PLACEHOLDER_ATTR_NAME = "data-rich-text-placeholder";
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
        EMPTY_ACTIVE_FORMATS2
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
          activeFormats: EMPTY_ACTIVE_FORMATS2
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

  // packages/rich-text/build-module/component/event-listeners/selection-change-compat.js
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

  // packages/rich-text/build-module/component/event-listeners/prevent-focus-capture.js
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

  // packages/rich-text/build-module/component/event-listeners/index.js
  var allEventListeners = [
    copy_handler_default,
    select_object_default,
    format_boundaries_default,
    delete_default,
    input_and_selection_default,
    selection_change_compat_default,
    preventFocusCapture
  ];
  function useEventListeners(props) {
    const propsRef = (0, import_element5.useRef)(props);
    (0, import_element5.useInsertionEffect)(() => {
      propsRef.current = props;
    });
    const refEffects = (0, import_element5.useMemo)(
      () => allEventListeners.map((refEffect) => refEffect(propsRef)),
      [propsRef]
    );
    return (0, import_compose2.useRefEffect)(
      (element) => {
        const cleanups = refEffects.map((effect) => effect(element));
        return () => {
          cleanups.forEach((cleanup) => cleanup());
        };
      },
      [refEffects]
    );
  }

  // packages/rich-text/build-module/component/index.js
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
    const registry = (0, import_data8.useRegistry)();
    const [, forceRender] = (0, import_element6.useReducer)(() => ({}));
    const ref = (0, import_element6.useRef)();
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
    const _valueRef = (0, import_element6.useRef)(value);
    const recordRef = (0, import_element6.useRef)();
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
    const hadSelectionUpdateRef = (0, import_element6.useRef)(false);
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
    const didMountRef = (0, import_element6.useRef)(false);
    (0, import_element6.useLayoutEffect)(() => {
      if (didMountRef.current && value !== _valueRef.current) {
        applyFromProps();
        forceRender();
      }
    }, [value]);
    (0, import_element6.useLayoutEffect)(() => {
      if (!hadSelectionUpdateRef.current) {
        return;
      }
      if (ref.current.ownerDocument.activeElement !== ref.current) {
        ref.current.focus();
      }
      applyRecord(recordRef.current);
      hadSelectionUpdateRef.current = false;
    }, [hadSelectionUpdateRef.current]);
    const mergedRefs = (0, import_compose3.useMergeRefs)([
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
      (0, import_compose3.useRefEffect)(() => {
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
  return __toCommonJS(index_exports);
})();
//# sourceMappingURL=index.js.map
