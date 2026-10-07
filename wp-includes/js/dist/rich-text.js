(function() {
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

  // package-external:@wordpress/private-apis
  var require_private_apis = __commonJS({
    "package-external:@wordpress/private-apis"(exports, module) {
      module.exports = window.wp.privateApis;
    }
  });

  // package-external:@wordpress/element
  var require_element = __commonJS({
    "package-external:@wordpress/element"(exports, module) {
      module.exports = window.wp.element;
    }
  });

  // package-external:@wordpress/compose
  var require_compose = __commonJS({
    "package-external:@wordpress/compose"(exports, module) {
      module.exports = window.wp.compose;
    }
  });

  // package-external:@wordpress/deprecated
  var require_deprecated = __commonJS({
    "package-external:@wordpress/deprecated"(exports, module) {
      module.exports = window.wp.deprecated;
    }
  });

  // package-external:@wordpress/keycodes
  var require_keycodes = __commonJS({
    "package-external:@wordpress/keycodes"(exports, module) {
      module.exports = window.wp.keycodes;
    }
  });

  // package-external:@wordpress/dom
  var require_dom = __commonJS({
    "package-external:@wordpress/dom"(exports, module) {
      module.exports = window.wp.dom;
    }
  });

  // packages/rich-text/build-module/index.mjs
  var index_exports = {};
  __export(index_exports, {
    RichTextData: () => RichTextData,
    __experimentalRichText: () => __experimentalRichText,
    __unstableCreateElement: () => createElement,
    __unstableToDom: () => toDom,
    __unstableUseRichText: () => useDeprecatedRichText,
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
    privateApis: () => privateApis,
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

  // packages/rich-text/build-module/store/index.mjs
  var import_data3 = __toESM(require_data(), 1);

  // packages/rich-text/build-module/store/reducer.mjs
  var import_data = __toESM(require_data(), 1);
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

  // packages/rich-text/build-module/store/selectors.mjs
  var selectors_exports = {};
  __export(selectors_exports, {
    getFormatType: () => getFormatType,
    getFormatTypeForBareElement: () => getFormatTypeForBareElement,
    getFormatTypeForClassName: () => getFormatTypeForClassName,
    getFormatTypes: () => getFormatTypes
  });
  var import_data2 = __toESM(require_data(), 1);
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

  // packages/rich-text/build-module/store/actions.mjs
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

  // packages/rich-text/build-module/store/index.mjs
  var STORE_NAME = "core/rich-text";
  var store = (0, import_data3.createReduxStore)(STORE_NAME, {
    reducer: reducer_default,
    selectors: selectors_exports,
    actions: actions_exports
  });
  (0, import_data3.register)(store);

  // packages/rich-text/build-module/is-format-equal.mjs
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
    let attributes1 = format1.attributes;
    let attributes2 = format2.attributes;
    if (format1.unregisteredAttributes || format2.unregisteredAttributes) {
      attributes1 = { ...attributes1, ...format1.unregisteredAttributes };
      attributes2 = { ...attributes2, ...format2.unregisteredAttributes };
    }
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

  // packages/rich-text/build-module/normalise-formats.mjs
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

  // packages/rich-text/build-module/apply-format.mjs
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

  // packages/rich-text/build-module/create.mjs
  var import_data5 = __toESM(require_data(), 1);

  // packages/rich-text/build-module/create-element.mjs
  function createElement({ implementation }, html) {
    if (!createElement.body) {
      createElement.body = implementation.createHTMLDocument("").body;
    }
    createElement.body.innerHTML = html;
    return createElement.body;
  }

  // packages/rich-text/build-module/special-characters.mjs
  var OBJECT_REPLACEMENT_CHARACTER = "\uFFFC";
  var ZWNBSP = "\uFEFF";

  // packages/rich-text/build-module/to-html-string.mjs
  var import_escape_html = __toESM(require_escape_html(), 1);

  // packages/rich-text/build-module/get-active-formats.mjs
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
    let i = selectedFormats.length;
    while (i--) {
      const formatsAtIndex = selectedFormats[i];
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

  // packages/rich-text/build-module/get-format-type.mjs
  var import_data4 = __toESM(require_data(), 1);
  function getFormatType2(name) {
    return (0, import_data4.select)(store).getFormatType(name);
  }

  // packages/rich-text/build-module/to-tree.mjs
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
    for (let i = 0; i < formatsLength; i++) {
      const character = text.charAt(i);
      const shouldInsertPadding = isEditableTree && // Pad the line if the line is empty.
      (!lastCharacter || // Pad the line if the previous character is a line break, otherwise
      // the line break won't be visible.
      lastCharacter === "\n");
      const characterFormats = formats[i];
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
              if (start === i && end === i + 1) {
                attrs["data-rich-text-format-boundary"] = true;
              }
              pointer = append3(pointer, {
                type: "span",
                attributes: attrs
              });
              if (isEditableTree && i + 1 === text.length) {
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
      if (onStartIndex && start === i + 1) {
        onStartIndex(tree, pointer);
      }
      if (onEndIndex && end === i + 1) {
        onEndIndex(tree, pointer);
      }
      if (shouldInsertPadding && i === text.length) {
        appendText3(pointer, ZWNBSP);
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

  // packages/rich-text/build-module/to-html-string.mjs
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

  // packages/rich-text/build-module/get-text-content.mjs
  function getTextContent({ text }) {
    return text.replace(OBJECT_REPLACEMENT_CHARACTER, "");
  }

  // packages/rich-text/build-module/create.mjs
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
    const format = {
      formatType,
      type: formatType.name,
      tagName
    };
    if (Object.keys(registeredAttributes).length) {
      format.attributes = registeredAttributes;
    }
    if (Object.keys(unregisteredAttributes).length) {
      format.unregisteredAttributes = unregisteredAttributes;
    }
    return format;
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
  function collapseWhiteSpace(element, isRoot = true, hasPrecedingSpace = false, hasTrailingSpace = false) {
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
        if (i === 0 && newNodeValue.startsWith(" ") && (isRoot || hasPrecedingSpace)) {
          newNodeValue = newNodeValue.slice(1);
        }
        if (i === nodes.length - 1 && newNodeValue.endsWith(" ") && (isRoot || hasTrailingSpace)) {
          newNodeValue = newNodeValue.slice(0, -1);
        }
        node.nodeValue = newNodeValue;
      } else if (node.nodeType === node.ELEMENT_NODE) {
        const { previousSibling, nextSibling } = node;
        const prevHasSpace = previousSibling?.textContent.endsWith(" ");
        const nextHasSpace = nextSibling?.textContent.startsWith(" ");
        node.replaceWith(
          collapseWhiteSpace(
            node,
            false,
            previousSibling ? prevHasSpace : isRoot || hasPrecedingSpace,
            nextSibling ? nextHasSpace : isRoot || hasTrailingSpace
          )
        );
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

  // packages/rich-text/build-module/concat.mjs
  function mergePair(a, b) {
    a.formats = a.formats.concat(b.formats);
    a.replacements = a.replacements.concat(b.replacements);
    a.text += b.text;
    return a;
  }
  function concat(...values) {
    return normaliseFormats(values.reduce(mergePair, create()));
  }

  // packages/rich-text/build-module/get-active-format.mjs
  function getActiveFormat(value, formatType) {
    return getActiveFormats(value).find(
      ({ type }) => type === formatType
    );
  }

  // packages/rich-text/build-module/get-active-object.mjs
  function getActiveObject({ start, end, replacements, text }) {
    if (start + 1 !== end || text[start] !== OBJECT_REPLACEMENT_CHARACTER) {
      return;
    }
    return replacements[start];
  }

  // packages/rich-text/build-module/is-collapsed.mjs
  function isCollapsed({
    start,
    end
  }) {
    if (start === void 0 || end === void 0) {
      return;
    }
    return start === end;
  }

  // packages/rich-text/build-module/is-empty.mjs
  function isEmpty({ text }) {
    return text.length === 0;
  }

  // packages/rich-text/build-module/join.mjs
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

  // packages/rich-text/build-module/register-format-type.mjs
  var import_data6 = __toESM(require_data(), 1);
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

  // packages/rich-text/build-module/remove-format.mjs
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

  // packages/rich-text/build-module/insert.mjs
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

  // packages/rich-text/build-module/remove.mjs
  function remove2(value, startIndex, endIndex) {
    return insert(value, create(), startIndex, endIndex);
  }

  // packages/rich-text/build-module/replace.mjs
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

  // packages/rich-text/build-module/insert-object.mjs
  function insertObject(value, formatToInsert, startIndex, endIndex) {
    const valueToInsert = {
      formats: [,],
      replacements: [formatToInsert],
      text: OBJECT_REPLACEMENT_CHARACTER
    };
    return insert(value, valueToInsert, startIndex, endIndex);
  }

  // packages/rich-text/build-module/slice.mjs
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

  // packages/rich-text/build-module/split.mjs
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

  // packages/rich-text/build-module/is-range-equal.mjs
  function isRangeEqual(a, b) {
    return a === b || a && b && a.startContainer === b.startContainer && a.startOffset === b.startOffset && a.endContainer === b.endContainer && a.endOffset === b.endOffset;
  }

  // packages/rich-text/build-module/to-dom.mjs
  var MATHML_NAMESPACE = "http://www.w3.org/1998/Math/MathML";
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

  // packages/rich-text/build-module/toggle-format.mjs
  var import_a11y = __toESM(require_a11y(), 1);
  var import_i18n = __toESM(require_i18n(), 1);
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

  // packages/rich-text/build-module/unregister-format-type.mjs
  var import_data7 = __toESM(require_data(), 1);
  function unregisterFormatType(name) {
    const oldFormat = (0, import_data7.select)(store).getFormatType(name);
    if (!oldFormat) {
      window.console.error(`Format ${name} is not registered.`);
      return;
    }
    (0, import_data7.dispatch)(store).removeFormatTypes(name);
    return oldFormat;
  }

  // packages/rich-text/build-module/lock-unlock.mjs
  var import_private_apis = __toESM(require_private_apis(), 1);
  var { lock, unlock } = (0, import_private_apis.__dangerousOptInToUnstableAPIsOnlyForCoreModules)(
    "I acknowledge private features are not for use in themes or plugins and doing so will break in the next version of WordPress.",
    "@wordpress/rich-text"
  );

  // packages/rich-text/build-module/hook/index.mjs
  var import_element5 = __toESM(require_element(), 1);
  var import_compose8 = __toESM(require_compose(), 1);
  var import_data9 = __toESM(require_data(), 1);
  var import_deprecated = __toESM(require_deprecated(), 1);

  // packages/rich-text/build-module/hook/use-default-style.mjs
  var import_element = __toESM(require_element(), 1);
  var whiteSpace = "pre-wrap";
  function useDefaultStyle() {
    return (0, import_element.useCallback)((element) => {
      if (!element) {
        return;
      }
      element.style.whiteSpace = element.style.whiteSpace || whiteSpace;
    }, []);
  }

  // node_modules/colord/index.mjs
  for (r = { grad: 0.9, turn: 360, rad: 360 / (2 * Math.PI) }, t = function(r2) {
    return "string" == typeof r2 ? r2.length > 0 : "number" == typeof r2;
  }, n = function(r2, t2, n2) {
    return void 0 === t2 && (t2 = 0), void 0 === n2 && (n2 = Math.pow(10, t2)), Math.round(n2 * r2) / n2 + 0;
  }, u = function(r2, t2, n2) {
    return void 0 === t2 && (t2 = 0), void 0 === n2 && (n2 = 1), r2 > n2 ? n2 : r2 > t2 ? r2 : t2;
  }, e = function(r2) {
    return (r2 = isFinite(r2) ? r2 % 360 : 0) < 0 ? r2 + 360 : r2;
  }, o = function(r2, t2) {
    return void 0 === t2 && (t2 = 0), n(r2, t2) % 360;
  }, a = function(r2) {
    return { r: u(r2.r, 0, 255), g: u(r2.g, 0, 255), b: u(r2.b, 0, 255), a: u(r2.a) };
  }, i = function(r2) {
    return { r: n(r2.r), g: n(r2.g), b: n(r2.b), a: n(r2.a, 3) };
  }, s = /^#([0-9a-f]{3,8})$/i, d = function(r2, t2) {
    var n2 = r2.charCodeAt(t2);
    return (15 & n2) + 9 * (n2 >> 6);
  }, h = function(r2, t2) {
    return d(r2, t2) << 4 | d(r2, t2 + 1);
  }, b = [], f = 0; f < 256; f++) b.push((f < 16 ? "0" : "") + f.toString(16));
  var r;
  var t;
  var n;
  var u;
  var e;
  var o;
  var a;
  var i;
  var s;
  var d;
  var h;
  var b;
  var f;
  var g = function(r) {
    return b[u(r, 0, 255)];
  };
  var c = function(r) {
    var t = r.r, n = r.g, u = r.b, e = r.a, o = Math.max(t, n, u), a = o - Math.min(t, n, u), i = a ? o === t ? (n - u) / a : o === n ? 2 + (u - t) / a : 4 + (t - n) / a : 0;
    return { h: 60 * (i < 0 ? i + 6 : i), s: o ? a / o * 100 : 0, v: o / 255 * 100, a: e };
  };
  var v = function(r) {
    var t = r.h, n = r.s, u = r.v, e = r.a;
    t = t / 360 * 6, n /= 100, u /= 100;
    var o = Math.floor(t), a = u * (1 - n), i = u * (1 - (t - o) * n), s = u * (1 - (1 - t + o) * n), d = o % 6;
    return { r: 255 * [u, i, a, a, s, u][d], g: 255 * [s, u, u, i, a, a][d], b: 255 * [a, a, s, u, u, i][d], a: e };
  };
  var l = function(r) {
    return { h: e(r.h), s: u(r.s, 0, 100), l: u(r.l, 0, 100), a: u(r.a) };
  };
  var p = function(r) {
    return { h: o(r.h), s: n(r.s), l: n(r.l), a: n(r.a, 3) };
  };
  var m = function(r) {
    return v((n = (t = r).s, { h: t.h, s: (n *= ((u = t.l) < 50 ? u : 100 - u) / 100) > 0 ? 2 * n / (u + n) * 100 : 0, v: u + n, a: t.a }));
    var t, n, u;
  };
  var y = function(r) {
    return { h: (t = c(r)).h, s: (e = (200 - (n = t.s)) * (u = t.v) / 100) > 0 && e < 200 ? n * u / 100 / (e <= 100 ? e : 200 - e) * 100 : 0, l: e / 2, a: t.a };
    var t, n, u, e;
  };
  var N = /^hsla?\(\s*([+-]?(?:\d*\.\d+|\d+))(deg|rad|grad|turn)?\s*,\s*([+-]?(?:\d*\.\d+|\d+))%\s*,\s*([+-]?(?:\d*\.\d+|\d+))%\s*(?:,\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*)?\)$/i;
  var x = /^hsla?\(\s*([+-]?(?:\d*\.\d+|\d+))(deg|rad|grad|turn)?\s+([+-]?(?:\d*\.\d+|\d+))%\s+([+-]?(?:\d*\.\d+|\d+))%\s*(?:\/\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*)?\)$/i;
  var M = /^rgba?\(\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*,\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*,\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*(?:,\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*)?\)$/i;
  var H = /^rgba?\(\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s+([+-]?(?:\d*\.\d+|\d+))(%)?\s+([+-]?(?:\d*\.\d+|\d+))(%)?\s*(?:\/\s*([+-]?(?:\d*\.\d+|\d+))(%)?\s*)?\)$/i;
  var $ = { string: [[function(r) {
    if (!s.test(r)) return null;
    var t = r.length;
    return t <= 5 ? { r: 17 * d(r, 1), g: 17 * d(r, 2), b: 17 * d(r, 3), a: 5 === t ? n(17 * d(r, 4) / 255, 2) : 1 } : 7 === t || 9 === t ? { r: h(r, 1), g: h(r, 3), b: h(r, 5), a: 9 === t ? n(h(r, 7) / 255, 2) : 1 } : null;
  }, "hex"], [function(r) {
    var t = M.exec(r) || H.exec(r);
    return t ? t[2] !== t[4] || t[4] !== t[6] ? null : a({ r: Number(t[1]) / (t[2] ? 100 / 255 : 1), g: Number(t[3]) / (t[4] ? 100 / 255 : 1), b: Number(t[5]) / (t[6] ? 100 / 255 : 1), a: void 0 === t[7] ? 1 : Number(t[7]) / (t[8] ? 100 : 1) }) : null;
  }, "rgb"], [function(t) {
    var n = N.exec(t) || x.exec(t);
    if (!n) return null;
    var u, e, o = l({ h: (u = n[1], e = n[2], void 0 === e && (e = "deg"), Number(u) * (r[e] || 1)), s: Number(n[3]), l: Number(n[4]), a: void 0 === n[5] ? 1 : Number(n[5]) / (n[6] ? 100 : 1) });
    return m(o);
  }, "hsl"]], object: [[function(r) {
    var n = r.r, u = r.g, e = r.b, o = r.a, i = void 0 === o ? 1 : o;
    return t(n) && t(u) && t(e) ? a({ r: Number(n), g: Number(u), b: Number(e), a: Number(i) }) : null;
  }, "rgb"], [function(r) {
    var n = r.h, u = r.s, e = r.l, o = r.a, a = void 0 === o ? 1 : o;
    if (!t(n) || !t(u) || !t(e)) return null;
    var i = l({ h: Number(n), s: Number(u), l: Number(e), a: Number(a) });
    return m(i);
  }, "hsl"], [function(r) {
    var n = r.h, o = r.s, a = r.v, i = r.a, s = void 0 === i ? 1 : i;
    if (!t(n) || !t(o) || !t(a)) return null;
    var d = (function(r2) {
      return { h: e(r2.h), s: u(r2.s, 0, 100), v: u(r2.v, 0, 100), a: u(r2.a) };
    })({ h: Number(n), s: Number(o), v: Number(a), a: Number(s) });
    return v(d);
  }, "hsv"]] };
  var j = function(r, t) {
    for (var n = 0; n < t.length; n++) {
      var u = t[n][0](r);
      if (u) return [u, t[n][1]];
    }
    return [null, void 0];
  };
  var w = function(r) {
    return "string" == typeof r ? j(r.trim(), $.string) : "object" == typeof r && null !== r ? j(r, $.object) : [null, void 0];
  };
  var k = function(r, t) {
    var n = y(r);
    return { h: n.h, s: u(n.s + 100 * t, 0, 100), l: n.l, a: n.a };
  };
  var E = function(r) {
    return (299 * r.r + 587 * r.g + 114 * r.b) / 1e3 / 255;
  };
  var R = function(r, t) {
    var n = y(r);
    return { h: n.h, s: n.s, l: u(n.l + 100 * t, 0, 100), a: n.a };
  };
  var q = (function() {
    function r(r2) {
      this.parsed = w(r2)[0], this.rgba = this.parsed || { r: 0, g: 0, b: 0, a: 1 };
    }
    return r.prototype.isValid = function() {
      return null !== this.parsed;
    }, r.prototype.brightness = function() {
      return n(E(this.rgba), 2);
    }, r.prototype.isDark = function() {
      return E(this.rgba) < 0.5;
    }, r.prototype.isLight = function() {
      return E(this.rgba) >= 0.5;
    }, r.prototype.toHex = function() {
      return r2 = i(this.rgba), t = r2.r, u = r2.g, e = r2.b, a = (o = r2.a) < 1 ? g(n(255 * o)) : "", "#" + g(t) + g(u) + g(e) + a;
      var r2, t, u, e, o, a;
    }, r.prototype.toRgb = function() {
      return i(this.rgba);
    }, r.prototype.toRgbString = function() {
      return r2 = i(this.rgba), t = r2.r, n = r2.g, u = r2.b, (e = r2.a) < 1 ? "rgba(" + t + ", " + n + ", " + u + ", " + e + ")" : "rgb(" + t + ", " + n + ", " + u + ")";
      var r2, t, n, u, e;
    }, r.prototype.toHsl = function() {
      return p(y(this.rgba));
    }, r.prototype.toHslString = function() {
      return r2 = p(y(this.rgba)), t = r2.h, n = r2.s, u = r2.l, (e = r2.a) < 1 ? "hsla(" + t + ", " + n + "%, " + u + "%, " + e + ")" : "hsl(" + t + ", " + n + "%, " + u + "%)";
      var r2, t, n, u, e;
    }, r.prototype.toHsv = function() {
      return r2 = c(this.rgba), { h: o(r2.h), s: n(r2.s), v: n(r2.v), a: n(r2.a, 3) };
      var r2;
    }, r.prototype.invert = function() {
      return A({ r: 255 - (r2 = this.rgba).r, g: 255 - r2.g, b: 255 - r2.b, a: r2.a });
      var r2;
    }, r.prototype.saturate = function(r2) {
      return void 0 === r2 && (r2 = 0.1), A(k(this.rgba, r2));
    }, r.prototype.desaturate = function(r2) {
      return void 0 === r2 && (r2 = 0.1), A(k(this.rgba, -r2));
    }, r.prototype.grayscale = function() {
      return A(k(this.rgba, -1));
    }, r.prototype.lighten = function(r2) {
      return void 0 === r2 && (r2 = 0.1), A(R(this.rgba, r2));
    }, r.prototype.darken = function(r2) {
      return void 0 === r2 && (r2 = 0.1), A(R(this.rgba, -r2));
    }, r.prototype.rotate = function(r2) {
      return void 0 === r2 && (r2 = 15), this.hue(y(this.rgba).h + r2);
    }, r.prototype.alpha = function(r2) {
      return "number" == typeof r2 ? A({ r: (t = this.rgba).r, g: t.g, b: t.b, a: r2 }) : n(this.rgba.a, 3);
      var t;
    }, r.prototype.hue = function(r2) {
      var t = y(this.rgba);
      return "number" == typeof r2 ? A({ h: r2, s: t.s, l: t.l, a: t.a }) : o(t.h);
    }, r.prototype.isEqual = function(r2) {
      return this.toHex() === A(r2).toHex();
    }, r;
  })();
  var A = function(r) {
    return r instanceof q ? r : new q(r);
  };

  // packages/rich-text/build-module/hook/use-boundary-style.mjs
  var import_element2 = __toESM(require_element(), 1);
  function useBoundaryStyle({ record }) {
    const ref = (0, import_element2.useRef)();
    const { activeFormats = [], replacements, start } = record.current;
    const activeReplacement = replacements[start];
    (0, import_element2.useEffect)(() => {
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
      const newColor = A(computedStyle.color).alpha(0.2).toRgbString();
      const selector = `[contenteditable="true"]:focus ${boundarySelector}`;
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

  // packages/rich-text/build-module/hook/event-listeners/index.mjs
  var import_element3 = __toESM(require_element(), 1);
  var import_compose7 = __toESM(require_compose(), 1);

  // packages/rich-text/build-module/hook/event-listeners/copy-handler.mjs
  var import_compose = __toESM(require_compose(), 1);

  // packages/rich-text/build-module/owns-selection.mjs
  function ownsSelection(element) {
    const { ownerDocument } = element;
    const { activeElement } = ownerDocument;
    if (activeElement === element) {
      return true;
    }
    if (!activeElement || activeElement.contentEditable !== "true" || element.contentEditable !== "true" || !activeElement.contains(element)) {
      return false;
    }
    const selection = ownerDocument.defaultView.getSelection();
    const { anchorNode, focusNode } = selection;
    return !!anchorNode && !!focusNode && element.contains(anchorNode) && element.contains(focusNode);
  }

  // packages/rich-text/build-module/hook/event-listeners/copy-handler.mjs
  var { subscribeDelegatedListener } = unlock(import_compose.privateApis);
  var copy_handler_default = (props) => (element) => {
    function onCopy(event) {
      const { record, handleChange } = props.current;
      const { ownerDocument } = element;
      if (
        // Another handler may have already claimed the clipboard, e.g.
        // the block editor copying the whole block when its entire
        // text is selected.
        event.defaultPrevented || isCollapsed(record.current) || !element.contains(ownerDocument.activeElement) && !ownsSelection(element)
      ) {
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
        handleChange(remove2(record.current));
      }
    }
    const { defaultView } = element.ownerDocument;
    const unsubscribeCopy = subscribeDelegatedListener(
      defaultView,
      "copy",
      onCopy
    );
    const unsubscribeCut = subscribeDelegatedListener(
      defaultView,
      "cut",
      onCopy
    );
    return () => {
      unsubscribeCopy();
      unsubscribeCut();
    };
  };

  // packages/rich-text/build-module/hook/event-listeners/select-object.mjs
  var import_compose2 = __toESM(require_compose(), 1);
  var { subscribeDelegatedListener: subscribeDelegatedListener2 } = unlock(import_compose2.privateApis);
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
      if (event.relatedTarget && !element.contains(event.relatedTarget)) {
        onClick(event);
      }
    }
    const unsubscribeClick = subscribeDelegatedListener2(
      element,
      "click",
      onClick
    );
    const unsubscribeFocusIn = subscribeDelegatedListener2(
      element,
      "focusin",
      onFocusIn
    );
    return () => {
      unsubscribeClick();
      unsubscribeFocusIn();
    };
  };

  // packages/rich-text/build-module/hook/event-listeners/format-boundaries.mjs
  var import_keycodes = __toESM(require_keycodes(), 1);

  // packages/rich-text/build-module/subscribe-owned-listener.mjs
  var import_compose3 = __toESM(require_compose(), 1);
  var { subscribeDelegatedListener: subscribeDelegatedListener3 } = unlock(import_compose3.privateApis);
  var registries = /* @__PURE__ */ new WeakMap();
  function subscribeOwnedListener(element, eventType, callback, capture = false) {
    const { ownerDocument } = element;
    let byEvent = registries.get(ownerDocument);
    if (!byEvent) {
      byEvent = /* @__PURE__ */ new Map();
      registries.set(ownerDocument, byEvent);
    }
    const key = capture ? `${eventType}:capture` : eventType;
    let elements = byEvent.get(key);
    if (!elements) {
      elements = /* @__PURE__ */ new WeakMap();
      byEvent.set(key, elements);
      subscribeDelegatedListener3(
        ownerDocument,
        eventType,
        (event) => {
          const { defaultView, activeElement } = ownerDocument;
          const anchorNode = defaultView?.getSelection()?.anchorNode;
          for (let node = anchorNode ?? activeElement; node; node = node.parentNode) {
            const callbacks = elements.get(node);
            if (callbacks && ownsSelection(node)) {
              for (const cb of callbacks) {
                cb(event);
              }
            }
          }
        },
        capture
      );
    }
    let set = elements.get(element);
    if (!set) {
      set = /* @__PURE__ */ new Set();
      elements.set(element, set);
    }
    set.add(callback);
    return () => {
      set.delete(callback);
    };
  }

  // packages/rich-text/build-module/hook/event-listeners/format-boundaries.mjs
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
      const { defaultView } = element.ownerDocument;
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
    return subscribeOwnedListener(element, "keydown", onKeyDown, true);
  };

  // packages/rich-text/build-module/hook/event-listeners/delete.mjs
  var import_keycodes2 = __toESM(require_keycodes(), 1);
  var delete_default = (props) => (element) => {
    function onKeyDown(event) {
      const { keyCode } = event;
      if (event.defaultPrevented) {
        return;
      }
      if (keyCode !== import_keycodes2.DELETE && keyCode !== import_keycodes2.BACKSPACE) {
        return;
      }
      const { createRecord, handleChange } = props.current;
      const currentValue = createRecord();
      const { start, end, text } = currentValue;
      if (start === 0 && end !== 0 && end === text.length) {
        handleChange(remove2(currentValue));
        event.preventDefault();
      }
    }
    return subscribeOwnedListener(element, "keydown", onKeyDown);
  };

  // packages/rich-text/build-module/hook/event-listeners/input-and-selection.mjs
  var import_compose4 = __toESM(require_compose(), 1);

  // packages/rich-text/build-module/update-formats.mjs
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

  // packages/rich-text/build-module/hook/event-listeners/input-and-selection.mjs
  var { subscribeDelegatedListener: subscribeDelegatedListener4 } = unlock(import_compose4.privateApis);
  var INSERTION_INPUT_TYPES_TO_IGNORE = /* @__PURE__ */ new Set([
    "insertParagraph",
    "insertOrderedList",
    "insertUnorderedList",
    "insertHorizontalRule",
    "insertLink"
  ]);
  var EMPTY_ACTIVE_FORMATS2 = [];
  var PLACEHOLDER_ATTR_NAME = "data-rich-text-placeholder";
  var input_and_selection_default = (props) => (element) => {
    const { ownerDocument } = element;
    const { defaultView } = ownerDocument;
    let isComposing = false;
    let isPointerDown = false;
    function onPointerDown() {
      isPointerDown = true;
    }
    function onPointerUp() {
      isPointerDown = false;
    }
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
      const clearFormats = !isCollapsed(record.current) && currentValue.start <= start;
      const change = updateFormats({
        value: currentValue,
        start,
        end: currentValue.start,
        formats: clearFormats ? [] : oldActiveFormats
      });
      handleChange(change);
    }
    let selectionSnapshot;
    function handleSelectionChange() {
      const { record, applyRecord, createRecord, onSelectionChange } = props.current;
      if (element.contentEditable !== "true") {
        return;
      }
      if (ownerDocument.activeElement !== element && !ownsSelection(element)) {
        return;
      }
      if (isComposing) {
        return;
      }
      const selection = defaultView.getSelection();
      if (selectionSnapshot && selectionSnapshot.anchorNode === selection.anchorNode && selectionSnapshot.anchorOffset === selection.anchorOffset && selectionSnapshot.focusNode === selection.focusNode && selectionSnapshot.focusOffset === selection.focusOffset && selectionSnapshot.processedStart === record.current.start && selectionSnapshot.processedEnd === record.current.end) {
        return;
      }
      const { start, end, text } = createRecord();
      const oldRecord = record.current;
      if (text.length === 0) {
        applyRecord({ ...oldRecord, start, end });
      }
      selectionSnapshot = {
        anchorNode: selection.anchorNode,
        anchorOffset: selection.anchorOffset,
        focusNode: selection.focusNode,
        focusOffset: selection.focusOffset,
        processedStart: start,
        processedEnd: end
      };
      if (text !== oldRecord.text) {
        onInput();
        return;
      }
      if (start === oldRecord.start && end === oldRecord.end) {
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
      element.querySelector(`[${PLACEHOLDER_ATTR_NAME}]`)?.remove();
    }
    function onCompositionEnd() {
      isComposing = false;
      onInput({ inputType: "insertText" });
    }
    function onFocus(event) {
      if (event.target !== element) {
        return;
      }
      if (element.contentEditable !== "true") {
        return;
      }
      const { record, isSelected, onSelectionChange, applyRecord } = props.current;
      if (element.parentElement.closest('[contenteditable="true"]')) {
        const selection = defaultView.getSelection();
        if (!selection.anchorNode || !element.contains(selection.anchorNode)) {
          if (isSelected && record.current.start !== void 0) {
            applyRecord(record.current);
          } else {
            selection.collapse(element, 0);
          }
        }
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
        selectionSnapshot = void 0;
      } else if (!isPointerDown) {
        applyRecord(record.current);
      }
      onSelectionChange(record.current.start, record.current.end);
      window.queueMicrotask(handleSelectionChange);
    }
    const unsubscribeInput = subscribeOwnedListener(
      element,
      "input",
      onInput,
      true
    );
    const unsubscribeCompositionStart = subscribeOwnedListener(
      element,
      "compositionstart",
      onCompositionStart
    );
    const unsubscribeCompositionEnd = subscribeOwnedListener(
      element,
      "compositionend",
      onCompositionEnd,
      true
    );
    const unsubscribeFocus = subscribeDelegatedListener4(
      element,
      "focusin",
      onFocus
    );
    const unsubscribePointerDown = subscribeDelegatedListener4(
      element,
      "pointerdown",
      onPointerDown
    );
    const unsubscribePointerUp = subscribeDelegatedListener4(
      defaultView,
      "pointerup",
      onPointerUp
    );
    const unsubscribePointerCancel = subscribeDelegatedListener4(
      defaultView,
      "pointercancel",
      onPointerUp
    );
    const unsubscribeSelectionChange = subscribeOwnedListener(
      element,
      "selectionchange",
      handleSelectionChange
    );
    const unsubscribeEnsureSelectionSync = [
      "keydown",
      "beforeinput",
      "copy",
      "cut",
      "paste"
    ].map(
      (eventType) => subscribeOwnedListener(
        element,
        eventType,
        handleSelectionChange,
        true
      )
    );
    return () => {
      unsubscribeInput();
      unsubscribeCompositionStart();
      unsubscribeCompositionEnd();
      unsubscribeFocus();
      unsubscribePointerDown();
      unsubscribePointerUp();
      unsubscribePointerCancel();
      unsubscribeSelectionChange();
      unsubscribeEnsureSelectionSync.forEach(
        (unsubscribe) => unsubscribe()
      );
    };
  };

  // packages/rich-text/build-module/hook/event-listeners/selection-change-compat.mjs
  var import_compose5 = __toESM(require_compose(), 1);
  var { subscribeDelegatedListener: subscribeDelegatedListener5 } = unlock(import_compose5.privateApis);
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
    const unsubscribePointerDown = subscribeDelegatedListener5(
      element,
      "pointerdown",
      onDown
    );
    const unsubscribeKeyDown = subscribeDelegatedListener5(
      element,
      "keydown",
      onDown
    );
    return () => {
      unsubscribePointerDown();
      unsubscribeKeyDown();
    };
  };

  // packages/rich-text/build-module/hook/event-listeners/prevent-focus-capture.mjs
  var import_compose6 = __toESM(require_compose(), 1);
  var { subscribeDelegatedListener: subscribeDelegatedListener6 } = unlock(import_compose6.privateApis);
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
        if (!event.target.closest("[data-block]")) {
          return;
        }
        value = element.getAttribute("contenteditable");
        defaultView.getSelection().removeAllRanges();
        element.setAttribute("contenteditable", "false");
      }
      function onPointerUp() {
        if (value !== null) {
          element.setAttribute("contenteditable", value);
          value = null;
          const selection = defaultView.getSelection();
          if (selection.isCollapsed && element.contains(selection.anchorNode)) {
            selection.removeAllRanges();
          }
        }
      }
      const unsubscribePointerDown = subscribeDelegatedListener6(
        defaultView,
        "pointerdown",
        onPointerDown
      );
      const unsubscribePointerUp = subscribeDelegatedListener6(
        defaultView,
        "pointerup",
        onPointerUp
      );
      const unsubscribePointerCancel = subscribeDelegatedListener6(
        defaultView,
        "pointercancel",
        onPointerUp
      );
      return () => {
        unsubscribePointerDown();
        unsubscribePointerUp();
        unsubscribePointerCancel();
      };
    };
  }

  // packages/rich-text/build-module/hook/event-listeners/index.mjs
  var allEventListeners = [
    input_and_selection_default,
    copy_handler_default,
    select_object_default,
    format_boundaries_default,
    delete_default,
    selection_change_compat_default,
    preventFocusCapture
  ];
  function useEventListeners(props) {
    const propsRef = (0, import_element3.useRef)(props);
    (0, import_element3.useInsertionEffect)(() => {
      propsRef.current = props;
    });
    const refEffects = (0, import_element3.useMemo)(
      () => allEventListeners.map((refEffect) => refEffect(propsRef)),
      [propsRef]
    );
    return (0, import_compose7.useRefEffect)(
      (element) => {
        const cleanups = refEffects.map((effect) => effect(element));
        return () => {
          cleanups.forEach((cleanup) => cleanup());
        };
      },
      [refEffects]
    );
  }

  // packages/rich-text/build-module/hook/use-format-types.mjs
  var import_element4 = __toESM(require_element(), 1);
  var import_data8 = __toESM(require_data(), 1);
  function formatTypesSelector(select5) {
    return select5(store).getFormatTypes();
  }
  var interactiveContentTags = /* @__PURE__ */ new Set([
    "a",
    "audio",
    "button",
    "details",
    "embed",
    "iframe",
    "input",
    "label",
    "select",
    "textarea",
    "video"
  ]);
  function prefixSelectKeys(selected, prefix) {
    if (typeof selected !== "object") {
      return { [prefix]: selected };
    }
    return Object.fromEntries(
      Object.entries(selected).map(([key, value]) => [
        `${prefix}.${key}`,
        value
      ])
    );
  }
  function getPrefixedSelectKeys(selected, prefix) {
    if (selected[prefix]) {
      return selected[prefix];
    }
    return Object.keys(selected).filter((key) => key.startsWith(prefix + ".")).reduce((accumulator, key) => {
      accumulator[key.slice(prefix.length + 1)] = selected[key];
      return accumulator;
    }, {});
  }
  function useFormatTypes({
    allowedFormats,
    withoutInteractiveFormatting,
    __unstableFormatTypeHandlerContext
  }) {
    const allFormatTypes = (0, import_data8.useSelect)(formatTypesSelector, []);
    const formatTypes2 = (0, import_element4.useMemo)(() => {
      return allFormatTypes.filter(({ name, interactive, tagName }) => {
        if (allowedFormats && !allowedFormats.includes(name)) {
          return false;
        }
        if (withoutInteractiveFormatting && (interactive || interactiveContentTags.has(tagName))) {
          return false;
        }
        return true;
      });
    }, [allFormatTypes, allowedFormats, withoutInteractiveFormatting]);
    const keyedSelected = (0, import_data8.useSelect)(
      (select5) => formatTypes2.reduce((accumulator, type) => {
        if (!type.__experimentalGetPropsForEditableTreePreparation || !__unstableFormatTypeHandlerContext) {
          return accumulator;
        }
        return {
          ...accumulator,
          ...prefixSelectKeys(
            type.__experimentalGetPropsForEditableTreePreparation(
              select5,
              __unstableFormatTypeHandlerContext
            ),
            type.name
          )
        };
      }, {}),
      [formatTypes2, __unstableFormatTypeHandlerContext]
    );
    const dispatch3 = (0, import_data8.useDispatch)();
    const prepareHandlers = [];
    const valueHandlers = [];
    const changeHandlers = [];
    const dependencies = [];
    for (const key in keyedSelected) {
      dependencies.push(keyedSelected[key]);
    }
    formatTypes2.forEach((type) => {
      if (type.__experimentalCreatePrepareEditableTree && __unstableFormatTypeHandlerContext) {
        const handler = type.__experimentalCreatePrepareEditableTree(
          getPrefixedSelectKeys(keyedSelected, type.name),
          __unstableFormatTypeHandlerContext
        );
        if (type.__experimentalCreateOnChangeEditableValue) {
          valueHandlers.push(handler);
        } else {
          prepareHandlers.push(handler);
        }
      }
      if (type.__experimentalCreateOnChangeEditableValue && __unstableFormatTypeHandlerContext) {
        let dispatchers = {};
        if (type.__experimentalGetPropsForEditableTreeChangeHandler) {
          dispatchers = type.__experimentalGetPropsForEditableTreeChangeHandler(
            dispatch3,
            __unstableFormatTypeHandlerContext
          );
        }
        const selected = getPrefixedSelectKeys(keyedSelected, type.name);
        changeHandlers.push(
          type.__experimentalCreateOnChangeEditableValue(
            {
              ...typeof selected === "object" ? selected : {},
              ...dispatchers
            },
            __unstableFormatTypeHandlerContext
          )
        );
      }
    });
    return {
      formatTypes: formatTypes2,
      prepareHandlers,
      valueHandlers,
      changeHandlers,
      dependencies
    };
  }

  // packages/rich-text/build-module/hook/index.mjs
  function hasFocus(element) {
    const { activeElement } = element.ownerDocument;
    return activeElement === element || activeElement?.contentEditable === "true" && activeElement.contains(element);
  }
  function useRichTextBase({
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
    const registry = (0, import_data9.useRegistry)();
    const [, forceRender] = (0, import_element5.useReducer)(() => ({}));
    const ref = (0, import_element5.useRef)();
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
    const _valueRef = (0, import_element5.useRef)(value);
    const recordRef = (0, import_element5.useRef)();
    function setRecordFromProps() {
      const activeFormats = recordRef.current?.activeFormats;
      _valueRef.current = value;
      recordRef.current = value;
      if (!(value instanceof RichTextData)) {
        recordRef.current = value ? RichTextData.fromHTMLString(value, { preserveWhiteSpace }) : RichTextData.empty();
      }
      recordRef.current = {
        text: recordRef.current.text,
        formats: recordRef.current.formats,
        replacements: recordRef.current.replacements,
        activeFormats
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
    if (!recordRef.current) {
      setRecordFromProps();
    } else if (selectionStart !== recordRef.current.start || selectionEnd !== recordRef.current.end) {
      recordRef.current = {
        ...recordRef.current,
        start: selectionStart,
        end: selectionEnd,
        activeFormats: void 0
      };
    }
    const sentSelectionRef = (0, import_element5.useRef)([]);
    function sendSelection(start, end) {
      sentSelectionRef.current = [start, end];
      onSelectionChange(start, end);
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
        sendSelection(start, end);
        onChange(_valueRef.current, {
          __unstableFormats: formats,
          __unstableText: text
        });
      });
      forceRender();
    }
    (0, import_element5.useLayoutEffect)(() => {
      if (value === _valueRef.current) {
        return;
      }
      setRecordFromProps();
      applyRecord(recordRef.current, {
        domOnly: !hasFocus(ref.current)
      });
      forceRender();
    }, [value]);
    (0, import_element5.useLayoutEffect)(() => {
      const [sentStart, sentEnd] = sentSelectionRef.current;
      sentSelectionRef.current = [];
      if (isSelected && (selectionStart !== sentStart || selectionEnd !== sentEnd) && hasFocus(ref.current)) {
        applyRecord(recordRef.current);
      }
    }, [selectionStart, selectionEnd, isSelected]);
    const mergedRefs = (0, import_compose8.useMergeRefs)([
      ref,
      useDefaultStyle(),
      useBoundaryStyle({ record: recordRef }),
      useEventListeners({
        record: recordRef,
        handleChange,
        applyRecord,
        createRecord,
        isSelected,
        onSelectionChange: sendSelection,
        forceRender
      }),
      (0, import_compose8.useRefEffect)(
        (element) => {
          setRecordFromProps();
          applyRecord(recordRef.current, {
            domOnly: !hasFocus(element)
          });
        },
        [placeholder, ...__unstableDependencies]
      )
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
  function useRichText({
    allowedFormats,
    withoutInteractiveFormatting,
    onChange,
    __unstableDependencies = [],
    __unstableFormatTypeHandlerContext,
    ...props
  }) {
    const {
      formatTypes: formatTypes2,
      prepareHandlers,
      valueHandlers,
      changeHandlers,
      dependencies
    } = useFormatTypes({
      allowedFormats,
      withoutInteractiveFormatting,
      __unstableFormatTypeHandlerContext
    });
    function addEditorOnlyFormats(record) {
      return valueHandlers.reduce(
        (accumulator, fn) => fn(accumulator, record.text),
        record.formats
      );
    }
    function removeEditorOnlyFormats(record) {
      formatTypes2.forEach((formatType) => {
        if (formatType.__experimentalCreatePrepareEditableTree) {
          record = removeFormat(
            record,
            formatType.name,
            0,
            record.text.length
          );
        }
      });
      return record.formats;
    }
    function addInvisibleFormats(record) {
      return prepareHandlers.reduce(
        (accumulator, fn) => fn(accumulator, record.text),
        record.formats
      );
    }
    const result = useRichTextBase({
      ...props,
      onChange(value, { __unstableFormats, __unstableText }) {
        onChange(value, { __unstableFormats, __unstableText });
        Object.values(changeHandlers).forEach((changeHandler) => {
          changeHandler(__unstableFormats, __unstableText);
        });
      },
      __unstableDependencies: [...dependencies, ...__unstableDependencies],
      __unstableAfterParse: addEditorOnlyFormats,
      __unstableBeforeSerialize: removeEditorOnlyFormats,
      __unstableAddInvisibleFormats: addInvisibleFormats
    });
    return { ...result, formatTypes: formatTypes2 };
  }
  function useDeprecatedRichText(props) {
    (0, import_deprecated.default)("`__unstableUseRichText` hook", {
      since: "7.0"
    });
    return useRichTextBase(props);
  }

  // packages/rich-text/build-module/contexts.mjs
  var import_element6 = __toESM(require_element(), 1);
  var KeyboardShortcutContext = (0, import_element6.createContext)();
  KeyboardShortcutContext.displayName = "KeyboardShortcutContext";
  var InputEventContext = (0, import_element6.createContext)();
  InputEventContext.displayName = "InputEventContext";

  // packages/rich-text/build-module/keyboard-shortcut.mjs
  var import_keycodes3 = __toESM(require_keycodes(), 1);
  var import_element7 = __toESM(require_element(), 1);
  var import_compose9 = __toESM(require_compose(), 1);
  function RichTextShortcut({ character, type, onUse }) {
    const keyboardShortcuts = (0, import_element7.useContext)(KeyboardShortcutContext);
    const stableOnUse = (0, import_compose9.useEvent)(onUse);
    (0, import_element7.useEffect)(() => {
      const shortcuts = keyboardShortcuts.current;
      function callback(event) {
        if (import_keycodes3.isKeyboardEvent[type](event, character)) {
          stableOnUse();
          event.preventDefault();
        }
      }
      shortcuts.add(callback);
      return () => {
        shortcuts.delete(callback);
      };
    }, [character, type, keyboardShortcuts, stableOnUse]);
    return null;
  }

  // packages/rich-text/build-module/input-event.mjs
  var import_element8 = __toESM(require_element(), 1);
  var import_compose10 = __toESM(require_compose(), 1);
  function RichTextInputEvent({ inputType, onInput }) {
    const callbacks = (0, import_element8.useContext)(InputEventContext);
    const stableOnInput = (0, import_compose10.useEvent)(onInput);
    (0, import_element8.useEffect)(() => {
      const inputCallbacks = callbacks.current;
      function callback(event) {
        if (event.inputType === inputType) {
          stableOnInput();
          event.preventDefault();
        }
      }
      inputCallbacks.add(callback);
      return () => {
        inputCallbacks.delete(callback);
      };
    }, [inputType, callbacks, stableOnInput]);
    return null;
  }

  // packages/rich-text/build-module/event-listeners.mjs
  var shortcutsListener = (props) => (element) => {
    const { keyboardShortcuts } = props.current;
    function onKeyDown(event) {
      for (const keyboardShortcut of keyboardShortcuts.current) {
        keyboardShortcut(event);
      }
    }
    element.addEventListener("keydown", onKeyDown);
    return () => {
      element.removeEventListener("keydown", onKeyDown);
    };
  };
  var inputEventsListener = (props) => (element) => {
    const { inputEvents } = props.current;
    function onInput(event) {
      for (const inputEventHandler of inputEvents.current) {
        inputEventHandler(event);
      }
    }
    element.addEventListener("input", onInput);
    return () => {
      element.removeEventListener("input", onInput);
    };
  };

  // packages/rich-text/build-module/private-apis.mjs
  var privateApis = {};
  lock(privateApis, {
    useRichText,
    KeyboardShortcutContext,
    InputEventContext,
    RichTextShortcut,
    RichTextInputEvent,
    shortcutsListener,
    inputEventsListener,
    ownsSelection,
    subscribeOwnedListener
  });

  // packages/rich-text/build-module/hook/use-anchor-ref.mjs
  var import_element9 = __toESM(require_element(), 1);
  var import_deprecated2 = __toESM(require_deprecated(), 1);
  function useAnchorRef({ ref, value, settings = {} }) {
    (0, import_deprecated2.default)("`useAnchorRef` hook", {
      since: "6.1",
      alternative: "`useAnchor` hook"
    });
    const { tagName, className, name } = settings;
    const activeFormat = name ? getActiveFormat(value, name) : void 0;
    return (0, import_element9.useMemo)(() => {
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

  // packages/rich-text/build-module/hook/use-anchor.mjs
  var import_compose11 = __toESM(require_compose(), 1);
  var import_element10 = __toESM(require_element(), 1);
  var import_dom = __toESM(require_dom(), 1);
  function getFormatElement(range, editableContentElement, tagName, className) {
    let element = range.startContainer;
    if (element.nodeType === element.TEXT_NODE && element instanceof window.Text && range.startOffset === element.length && element.nextSibling) {
      element = element.nextSibling;
      while (element.firstChild) {
        element = element.firstChild;
      }
    }
    if (element.nodeType !== element.ELEMENT_NODE) {
      if (!element.parentElement) {
        return;
      }
      element = element.parentElement;
    }
    if (element === editableContentElement) {
      return;
    }
    if (!editableContentElement.contains(element)) {
      return;
    }
    const selector = tagName + (className ? "." + className : "");
    if (!selector) {
      return;
    }
    if (!(element instanceof window.HTMLElement)) {
      return;
    }
    let closestElement = element;
    while (closestElement && closestElement !== editableContentElement) {
      if (closestElement.matches(selector)) {
        return closestElement;
      }
      closestElement = closestElement.parentElement;
    }
    return void 0;
  }
  function createVirtualAnchorElement(range, editableContentElement) {
    return {
      contextElement: editableContentElement,
      getBoundingClientRect() {
        if (editableContentElement.contains(range.startContainer)) {
          return (0, import_dom.getRectangleFromRange)(range) ?? range.getBoundingClientRect();
        }
        return editableContentElement.getBoundingClientRect();
      }
    };
  }
  function getAnchor(editableContentElement, tagName, className) {
    if (!editableContentElement) {
      return;
    }
    const { ownerDocument } = editableContentElement;
    const { defaultView } = ownerDocument;
    const selection = defaultView?.getSelection();
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
    if (!tagName && !className) {
      return createVirtualAnchorElement(range, editableContentElement);
    }
    return getFormatElement(range, editableContentElement, tagName, className) ?? createVirtualAnchorElement(range, editableContentElement);
  }
  var DEFAULT_SETTINGS = {
    tagName: "",
    className: ""
  };
  function useAnchor({
    editableContentElement,
    settings
  }) {
    const { tagName, className } = settings ?? DEFAULT_SETTINGS;
    const isActive = !!(settings && "isActive" in settings && settings.isActive);
    const [anchor, setAnchor] = (0, import_element10.useState)(
      () => getAnchor(editableContentElement, tagName, className ?? "")
    );
    const wasActive = (0, import_compose11.usePrevious)(isActive);
    (0, import_element10.useLayoutEffect)(() => {
      if (!editableContentElement) {
        return;
      }
      function callback() {
        setAnchor(
          getAnchor(editableContentElement, tagName, className ?? "")
        );
      }
      function attach() {
        ownerDocument.addEventListener("selectionchange", callback);
      }
      function detach() {
        ownerDocument.removeEventListener("selectionchange", callback);
      }
      const { ownerDocument } = editableContentElement;
      if (ownsSelection(editableContentElement) || // When a link is created, we need to attach the popover to the newly created anchor.
      !wasActive && isActive || // Sometimes we're _removing_ an active anchor, such as the inline color popover.
      // When we add the color, it switches from a virtual anchor to a `<mark>` element.
      // When we _remove_ the color, it switches from a `<mark>` element to a virtual anchor.
      wasActive && !isActive) {
        setAnchor(
          getAnchor(editableContentElement, tagName, className ?? "")
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

  // packages/rich-text/build-module/index.mjs
  function __experimentalRichText() {
  }
  return __toCommonJS(index_exports);
})();
(window.wp ||= {}).richText = wp.richText;
})();
