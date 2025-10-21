/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	// The require scope
/******/ 	var __webpack_require__ = {};
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
// ESM COMPAT FLAG
__webpack_require__.r(__webpack_exports__);

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "default": () => (/* binding */ latexToMathML)
});

;// ./node_modules/temml/dist/temml.mjs
/**
 * This is the ParseError class, which is the main error thrown by Temml
 * functions when something has gone wrong. This is used to distinguish internal
 * errors from errors in the expression that the user provided.
 *
 * If possible, a caller should provide a Token or ParseNode with information
 * about where in the source string the problem occurred.
 */
class ParseError {
  constructor(
    message, // The error message
    token // An object providing position information
  ) {
    let error = " " + message;
    let start;

    const loc = token && token.loc;
    if (loc && loc.start <= loc.end) {
      // If we have the input and a position, make the error a bit fancier

      // Get the input
      const input = loc.lexer.input;

      // Prepend some information
      start = loc.start;
      const end = loc.end;
      if (start === input.length) {
        error += " at end of input: ";
      } else {
        error += " at position " + (start + 1) + ": ";
      }

      // Underline token in question using combining underscores
      const underlined = input.slice(start, end).replace(/[^]/g, "$&\u0332");

      // Extract some context from the input and add it to the error
      let left;
      if (start > 15) {
        left = "…" + input.slice(start - 15, start);
      } else {
        left = input.slice(0, start);
      }
      let right;
      if (end + 15 < input.length) {
        right = input.slice(end, end + 15) + "…";
      } else {
        right = input.slice(end);
      }
      error += left + underlined + right;
    }

    // Some hackery to make ParseError a prototype of Error
    // See http://stackoverflow.com/a/8460753
    const self = new Error(error);
    self.name = "ParseError";
    self.__proto__ = ParseError.prototype;
    self.position = start;
    return self;
  }
}

ParseError.prototype.__proto__ = Error.prototype;

//
/**
 * This file contains a list of utility functions which are useful in other
 * files.
 */

/**
 * Provide a default value if a setting is undefined
 */
const deflt = function(setting, defaultIfUndefined) {
  return setting === undefined ? defaultIfUndefined : setting;
};

// hyphenate and escape adapted from Facebook's React under Apache 2 license

const uppercase = /([A-Z])/g;
const hyphenate = function(str) {
  return str.replace(uppercase, "-$1").toLowerCase();
};

const ESCAPE_LOOKUP = {
  "&": "&amp;",
  ">": "&gt;",
  "<": "&lt;",
  '"': "&quot;",
  "'": "&#x27;"
};

const ESCAPE_REGEX = /[&><"']/g;

/**
 * Escapes text to prevent scripting attacks.
 */
function temml_escape(text) {
  return String(text).replace(ESCAPE_REGEX, (match) => ESCAPE_LOOKUP[match]);
}

/**
 * Sometimes we want to pull out the innermost element of a group. In most
 * cases, this will just be the group itself, but when ordgroups and colors have
 * a single element, we want to pull that out.
 */
const getBaseElem = function(group) {
  if (group.type === "ordgroup") {
    if (group.body.length === 1) {
      return getBaseElem(group.body[0]);
    } else {
      return group;
    }
  } else if (group.type === "color") {
    if (group.body.length === 1) {
      return getBaseElem(group.body[0]);
    } else {
      return group;
    }
  } else if (group.type === "font") {
    return getBaseElem(group.body);
  } else {
    return group;
  }
};

/**
 * TeXbook algorithms often reference "character boxes", which are simply groups
 * with a single character in them. To decide if something is a character box,
 * we find its innermost group, and see if it is a single character.
 */
const isCharacterBox = function(group) {
  const baseElem = getBaseElem(group);

  // These are all the types of groups which hold single characters
  return baseElem.type === "mathord" || baseElem.type === "textord" || baseElem.type === "atom"
};

const assert = function(value) {
  if (!value) {
    throw new Error("Expected non-null, but got " + String(value));
  }
  return value;
};

/**
 * Return the protocol of a URL, or "_relative" if the URL does not specify a
 * protocol (and thus is relative), or `null` if URL has invalid protocol
 * (so should be outright rejected).
 */
const protocolFromUrl = function(url) {
  // Check for possible leading protocol.
  // https://url.spec.whatwg.org/#url-parsing strips leading whitespace
  // (\x00) or C0 control (\x00-\x1F) characters.
  // eslint-disable-next-line no-control-regex
  const protocol = /^[\x00-\x20]*([^\\/#?]*?)(:|&#0*58|&#x0*3a|&colon)/i.exec(url);
  if (!protocol) {
    return "_relative";
  }
  // Reject weird colons
  if (protocol[2] !== ":") {
    return null;
  }
  // Reject invalid characters in scheme according to
  // https://datatracker.ietf.org/doc/html/rfc3986#section-3.1
  if (!/^[a-zA-Z][a-zA-Z0-9+\-.]*$/.test(protocol[1])) {
    return null;
  }
  // Lowercase the protocol
  return protocol[1].toLowerCase();
};

/**
 * Round `n` to 4 decimal places, or to the nearest 1/10,000th em. The TeXbook
 * gives an acceptable rounding error of 100sp (which would be the nearest
 * 1/6551.6em with our ptPerEm = 10):
 * http://www.ctex.org/documents/shredder/src/texbook.pdf#page=69
 */
const round = function(n) {
  return +n.toFixed(4);
};

var utils = {
  deflt,
  escape: temml_escape,
  hyphenate,
  getBaseElem,
  isCharacterBox,
  protocolFromUrl,
  round
};

/**
 * This is a module for storing settings passed into Temml. It correctly handles
 * default settings.
 */


/**
 * The main Settings object
 */
class Settings {
  constructor(options) {
    // allow null options
    options = options || {};
    this.displayMode = utils.deflt(options.displayMode, false);    // boolean
    this.annotate = utils.deflt(options.annotate, false);           // boolean
    this.leqno = utils.deflt(options.leqno, false);                // boolean
    this.throwOnError = utils.deflt(options.throwOnError, false);  // boolean
    this.errorColor = utils.deflt(options.errorColor, "#b22222");  // string
    this.macros = options.macros || {};
    this.wrap = utils.deflt(options.wrap, "tex");                    // "tex" | "="
    this.xml = utils.deflt(options.xml, false);                     // boolean
    this.colorIsTextColor = utils.deflt(options.colorIsTextColor, false);  // booelean
    this.strict = utils.deflt(options.strict, false);    // boolean
    this.trust = utils.deflt(options.trust, false);  // trust context. See html.js.
    this.maxSize = (options.maxSize === undefined
      ? [Infinity, Infinity]
      : Array.isArray(options.maxSize)
      ? options.maxSize
      : [Infinity, Infinity]
    );
    this.maxExpand = Math.max(0, utils.deflt(options.maxExpand, 1000)); // number
  }

  /**
   * Check whether to test potentially dangerous input, and return
   * `true` (trusted) or `false` (untrusted).  The sole argument `context`
   * should be an object with `command` field specifying the relevant LaTeX
   * command (as a string starting with `\`), and any other arguments, etc.
   * If `context` has a `url` field, a `protocol` field will automatically
   * get added by this function (changing the specified object).
   */
  isTrusted(context) {
    if (context.url && !context.protocol) {
      const protocol = utils.protocolFromUrl(context.url);
      if (protocol == null) {
        return false
      }
      context.protocol = protocol;
    }
    const trust = typeof this.trust === "function" ? this.trust(context) : this.trust;
    return Boolean(trust);
  }
}

/**
 * All registered functions.
 * `functions.js` just exports this same dictionary again and makes it public.
 * `Parser.js` requires this dictionary.
 */
const _functions = {};

/**
 * All MathML builders. Should be only used in the `define*` and the `build*ML`
 * functions.
 */
const _mathmlGroupBuilders = {};

function defineFunction({
  type,
  names,
  props,
  handler,
  mathmlBuilder
}) {
  // Set default values of functions
  const data = {
    type,
    numArgs: props.numArgs,
    argTypes: props.argTypes,
    allowedInArgument: !!props.allowedInArgument,
    allowedInText: !!props.allowedInText,
    allowedInMath: props.allowedInMath === undefined ? true : props.allowedInMath,
    numOptionalArgs: props.numOptionalArgs || 0,
    infix: !!props.infix,
    primitive: !!props.primitive,
    handler: handler
  };
  for (let i = 0; i < names.length; ++i) {
    _functions[names[i]] = data;
  }
  if (type) {
    if (mathmlBuilder) {
      _mathmlGroupBuilders[type] = mathmlBuilder;
    }
  }
}

/**
 * Use this to register only the MathML builder for a function(e.g.
 * if the function's ParseNode is generated in Parser.js rather than via a
 * stand-alone handler provided to `defineFunction`).
 */
function defineFunctionBuilders({ type, mathmlBuilder }) {
  defineFunction({
    type,
    names: [],
    props: { numArgs: 0 },
    handler() {
      throw new Error("Should never be called.")
    },
    mathmlBuilder
  });
}

const normalizeArgument = function(arg) {
  return arg.type === "ordgroup" && arg.body.length === 1 ? arg.body[0] : arg
};

// Since the corresponding buildMathML function expects a
// list of elements, we normalize for different kinds of arguments
const ordargument = function(arg) {
  return arg.type === "ordgroup" ? arg.body : [arg]
};

/**
 * This node represents a document fragment, which contains elements, but when
 * placed into the DOM doesn't have any representation itself. It only contains
 * children and doesn't have any DOM node properties.
 */
class DocumentFragment {
  constructor(children) {
    this.children = children;
    this.classes = [];
    this.style = {};
  }

  hasClass(className) {
    return this.classes.includes(className);
  }

  /** Convert the fragment into a node. */
  toNode() {
    const frag = document.createDocumentFragment();

    for (let i = 0; i < this.children.length; i++) {
      frag.appendChild(this.children[i].toNode());
    }

    return frag;
  }

  /** Convert the fragment into HTML markup. */
  toMarkup() {
    let markup = "";

    // Simply concatenate the markup for the children together.
    for (let i = 0; i < this.children.length; i++) {
      markup += this.children[i].toMarkup();
    }

    return markup;
  }

  /**
   * Converts the math node into a string, similar to innerText. Applies to
   * MathDomNode's only.
   */
  toText() {
    // To avoid this, we would subclass documentFragment separately for
    // MathML, but polyfills for subclassing is expensive per PR 1469.
    const toText = (child) => child.toText();
    return this.children.map(toText).join("");
  }
}

/**
 * These objects store the data about the DOM nodes we create, as well as some
 * extra data. They can then be transformed into real DOM nodes with the
 * `toNode` function or HTML markup using `toMarkup`. They are useful for both
 * storing extra properties on the nodes, as well as providing a way to easily
 * work with the DOM.
 *
 * Similar functions for working with MathML nodes exist in mathMLTree.js.
 *
 */

/**
 * Create an HTML className based on a list of classes. In addition to joining
 * with spaces, we also remove empty classes.
 */
const createClass = function(classes) {
  return classes.filter((cls) => cls).join(" ");
};

const initNode = function(classes, style) {
  this.classes = classes || [];
  this.attributes = {};
  this.style = style || {};
};

/**
 * Convert into an HTML node
 */
const toNode = function(tagName) {
  const node = document.createElement(tagName);

  // Apply the class
  node.className = createClass(this.classes);

  // Apply inline styles
  for (const style in this.style) {
    if (Object.prototype.hasOwnProperty.call(this.style, style )) {
      node.style[style] = this.style[style];
    }
  }

  // Apply attributes
  for (const attr in this.attributes) {
    if (Object.prototype.hasOwnProperty.call(this.attributes, attr )) {
      node.setAttribute(attr, this.attributes[attr]);
    }
  }

  // Append the children, also as HTML nodes
  for (let i = 0; i < this.children.length; i++) {
    node.appendChild(this.children[i].toNode());
  }

  return node;
};

/**
 * Convert into an HTML markup string
 */
const toMarkup = function(tagName) {
  let markup = `<${tagName}`;

  // Add the class
  if (this.classes.length) {
    markup += ` class="${utils.escape(createClass(this.classes))}"`;
  }

  let styles = "";

  // Add the styles, after hyphenation
  for (const style in this.style) {
    if (Object.prototype.hasOwnProperty.call(this.style, style )) {
      styles += `${utils.hyphenate(style)}:${this.style[style]};`;
    }
  }

  if (styles) {
    markup += ` style="${styles}"`;
  }

  // Add the attributes
  for (const attr in this.attributes) {
    if (Object.prototype.hasOwnProperty.call(this.attributes, attr )) {
      markup += ` ${attr}="${utils.escape(this.attributes[attr])}"`;
    }
  }

  markup += ">";

  // Add the markup of the children, also as markup
  for (let i = 0; i < this.children.length; i++) {
    markup += this.children[i].toMarkup();
  }

  markup += `</${tagName}>`;

  return markup;
};

/**
 * This node represents a span node, with a className, a list of children, and
 * an inline style.
 *
 */
class Span {
  constructor(classes, children, style) {
    initNode.call(this, classes, style);
    this.children = children || [];
  }

  setAttribute(attribute, value) {
    this.attributes[attribute] = value;
  }

  toNode() {
    return toNode.call(this, "span");
  }

  toMarkup() {
    return toMarkup.call(this, "span");
  }
}

let TextNode$1 = class TextNode {
  constructor(text) {
    this.text = text;
  }
  toNode() {
    return document.createTextNode(this.text);
  }
  toMarkup() {
    return utils.escape(this.text);
  }
};

// Create an <a href="…"> node.
class AnchorNode {
  constructor(href, classes, children) {
    this.href = href;
    this.classes = classes;
    this.children = children || [];
  }

  toNode() {
    const node = document.createElement("a");
    node.setAttribute("href", this.href);
    if (this.classes.length > 0) {
      node.className = createClass(this.classes);
    }
    for (let i = 0; i < this.children.length; i++) {
      node.appendChild(this.children[i].toNode());
    }
    return node
  }

  toMarkup() {
    let markup = `<a href='${utils.escape(this.href)}'`;
    if (this.classes.length > 0) {
      markup += ` class="${utils.escape(createClass(this.classes))}"`;
    }
    markup += ">";
    for (let i = 0; i < this.children.length; i++) {
      markup += this.children[i].toMarkup();
    }
    markup += "</a>";
    return markup
  }
}

/*
 * This node represents an image embed (<img>) element.
 */
class Img {
  constructor(src, alt, style) {
    this.alt = alt;
    this.src = src;
    this.classes = ["mord"];
    this.style = style;
  }

  hasClass(className) {
    return this.classes.includes(className);
  }

  toNode() {
    const node = document.createElement("img");
    node.src = this.src;
    node.alt = this.alt;
    node.className = "mord";

    // Apply inline styles
    for (const style in this.style) {
      if (Object.prototype.hasOwnProperty.call(this.style, style )) {
        node.style[style] = this.style[style];
      }
    }

    return node;
  }

  toMarkup() {
    let markup = `<img src='${this.src}' alt='${this.alt}'`;

    // Add the styles, after hyphenation
    let styles = "";
    for (const style in this.style) {
      if (Object.prototype.hasOwnProperty.call(this.style, style )) {
        styles += `${utils.hyphenate(style)}:${this.style[style]};`;
      }
    }
    if (styles) {
      markup += ` style="${utils.escape(styles)}"`;
    }

    markup += ">";
    return markup;
  }
}

//
/**
 * These objects store data about MathML nodes.
 * The `toNode` and `toMarkup` functions  create namespaced DOM nodes and
 * HTML text markup respectively.
 */


function newDocumentFragment(children) {
  return new DocumentFragment(children);
}

/**
 * This node represents a general purpose MathML node of any type,
 * for example, `"mo"` or `"mspace"`, corresponding to `<mo>` and
 * `<mspace>` tags).
 */
class MathNode {
  constructor(type, children, classes, style) {
    this.type = type;
    this.attributes = {};
    this.children = children || [];
    this.classes = classes || [];
    this.style = style || {};   // Used for <mstyle> elements
    this.label = "";
  }

  /**
   * Sets an attribute on a MathML node. MathML depends on attributes to convey a
   * semantic content, so this is used heavily.
   */
  setAttribute(name, value) {
    this.attributes[name] = value;
  }

  /**
   * Gets an attribute on a MathML node.
   */
  getAttribute(name) {
    return this.attributes[name];
  }

  setLabel(value) {
    this.label = value;
  }

  /**
   * Converts the math node into a MathML-namespaced DOM element.
   */
  toNode() {
    const node = document.createElementNS("http://www.w3.org/1998/Math/MathML", this.type);

    for (const attr in this.attributes) {
      if (Object.prototype.hasOwnProperty.call(this.attributes, attr)) {
        node.setAttribute(attr, this.attributes[attr]);
      }
    }

    if (this.classes.length > 0) {
      node.className = createClass(this.classes);
    }

    // Apply inline styles
    for (const style in this.style) {
      if (Object.prototype.hasOwnProperty.call(this.style, style )) {
        node.style[style] = this.style[style];
      }
    }

    for (let i = 0; i < this.children.length; i++) {
      node.appendChild(this.children[i].toNode());
    }

    return node;
  }

  /**
   * Converts the math node into an HTML markup string.
   */
  toMarkup() {
    let markup = "<" + this.type;

    // Add the attributes
    for (const attr in this.attributes) {
      if (Object.prototype.hasOwnProperty.call(this.attributes, attr)) {
        markup += " " + attr + '="';
        markup += utils.escape(this.attributes[attr]);
        markup += '"';
      }
    }

    if (this.classes.length > 0) {
      markup += ` class="${utils.escape(createClass(this.classes))}"`;
    }

    let styles = "";

    // Add the styles, after hyphenation
    for (const style in this.style) {
      if (Object.prototype.hasOwnProperty.call(this.style, style )) {
        styles += `${utils.hyphenate(style)}:${this.style[style]};`;
      }
    }

    if (styles) {
      markup += ` style="${styles}"`;
    }

    markup += ">";

    for (let i = 0; i < this.children.length; i++) {
      markup += this.children[i].toMarkup();
    }

    markup += "</" + this.type + ">";

    return markup;
  }

  /**
   * Converts the math node into a string, similar to innerText, but escaped.
   */
  toText() {
    return this.children.map((child) => child.toText()).join("");
  }
}

/**
 * This node represents a piece of text.
 */
class TextNode {
  constructor(text) {
    this.text = text;
  }

  /**
   * Converts the text node into a DOM text node.
   */
  toNode() {
    return document.createTextNode(this.text);
  }

  /**
   * Converts the text node into escaped HTML markup
   * (representing the text itself).
   */
  toMarkup() {
    return utils.escape(this.toText());
  }

  /**
   * Converts the text node into a string
   * (representing the text itself).
   */
  toText() {
    return this.text;
  }
}

// Do not make an <mrow> the only child of a <mstyle>.
// An <mstyle> acts as its own implicit <mrow>.
const wrapWithMstyle = expression => {
  let node;
  if (expression.length === 1 && expression[0].type === "mrow") {
    node = expression.pop();
    node.type = "mstyle";
  } else {
    node = new MathNode("mstyle", expression);
  }
  return node
};

var mathMLTree = {
  MathNode,
  TextNode,
  newDocumentFragment
};

/**
 * This file provides support for building horizontal stretchy elements.
 */


// TODO: Remove when Chromium stretches \widetilde & \widehat
const estimatedWidth = node => {
  let width = 0;
  if (node.body) {
    for (const item of node.body) {
      width += estimatedWidth(item);
    }
  } else if (node.type === "supsub") {
    width += estimatedWidth(node.base);
    if (node.sub) { width += 0.7 * estimatedWidth(node.sub); }
    if (node.sup) { width += 0.7 * estimatedWidth(node.sup); }
  } else if (node.type === "mathord" || node.type === "textord") {
    for (const ch of node.text.split('')) {
      const codePoint = ch.codePointAt(0);
      if ((0x60 < codePoint && codePoint < 0x7B) || (0x03B0 < codePoint && codePoint < 0x3CA)) {
        width += 0.56; // lower case latin or greek. Use advance width of letter n
      } else if (0x2F < codePoint && codePoint < 0x3A) {
        width += 0.50; // numerals.
      } else {
        width += 0.92; // advance width of letter M
      }
    }
  } else {
    width += 1.0;
  }
  return width
};

const stretchyCodePoint = {
  widehat: "^",
  widecheck: "ˇ",
  widetilde: "~",
  wideparen: "⏜", // \u23dc
  utilde: "~",
  overleftarrow: "\u2190",
  underleftarrow: "\u2190",
  xleftarrow: "\u2190",
  overrightarrow: "\u2192",
  underrightarrow: "\u2192",
  xrightarrow: "\u2192",
  underbrace: "\u23df",
  overbrace: "\u23de",
  overgroup: "\u23e0",
  overparen: "⏜",
  undergroup: "\u23e1",
  underparen: "\u23dd",
  overleftrightarrow: "\u2194",
  underleftrightarrow: "\u2194",
  xleftrightarrow: "\u2194",
  Overrightarrow: "\u21d2",
  xRightarrow: "\u21d2",
  overleftharpoon: "\u21bc",
  xleftharpoonup: "\u21bc",
  overrightharpoon: "\u21c0",
  xrightharpoonup: "\u21c0",
  xLeftarrow: "\u21d0",
  xLeftrightarrow: "\u21d4",
  xhookleftarrow: "\u21a9",
  xhookrightarrow: "\u21aa",
  xmapsto: "\u21a6",
  xrightharpoondown: "\u21c1",
  xleftharpoondown: "\u21bd",
  xtwoheadleftarrow: "\u219e",
  xtwoheadrightarrow: "\u21a0",
  xlongequal: "=",
  xrightleftarrows: "\u21c4",
  yields: "\u2192",
  yieldsLeft: "\u2190",
  mesomerism: "\u2194",
  longrightharpoonup: "\u21c0",
  longleftharpoondown: "\u21bd",
  eqrightharpoonup: "\u21c0",
  eqleftharpoondown: "\u21bd",
  "\\cdrightarrow": "\u2192",
  "\\cdleftarrow": "\u2190",
  "\\cdlongequal": "="
};

const mathMLnode = function(label) {
  const child = new mathMLTree.TextNode(stretchyCodePoint[label.slice(1)]);
  const node = new mathMLTree.MathNode("mo", [child]);
  node.setAttribute("stretchy", "true");
  return node
};

const crookedWides = ["\\widetilde", "\\widehat", "\\widecheck", "\\utilde"];

// TODO: Remove when Chromium stretches \widetilde & \widehat
const accentNode = (group) => {
  const mo = mathMLnode(group.label);
  if (crookedWides.includes(group.label)) {
    const width = estimatedWidth(group.base);
    if (1 < width && width < 1.6) {
      mo.classes.push("tml-crooked-2");
    } else if (1.6 <= width && width < 2.5) {
      mo.classes.push("tml-crooked-3");
    } else if (2.5 <= width) {
      mo.classes.push("tml-crooked-4");
    }
  }
  return mo
};

var stretchy = {
  mathMLnode,
  accentNode
};

/**
 * This file holds a list of all no-argument functions and single-character
 * symbols (like 'a' or ';').
 *
 * For each of the symbols, there are two properties they can have:
 * - group (required): the ParseNode group type the symbol should have (i.e.
     "textord", "mathord", etc).
 * - replace: the character that this symbol or function should be
 *   replaced with (i.e. "\phi" has a replace value of "\u03d5", the phi
 *   character in the main font).
 *
 * The outermost map in the table indicates what mode the symbols should be
 * accepted in (e.g. "math" or "text").
 */

// Some of these have a "-token" suffix since these are also used as `ParseNode`
// types for raw text tokens, and we want to avoid conflicts with higher-level
// `ParseNode` types. These `ParseNode`s are constructed within `Parser` by
// looking up the `symbols` map.
const ATOMS = {
  bin: 1,
  close: 1,
  inner: 1,
  open: 1,
  punct: 1,
  rel: 1
};
const NON_ATOMS = {
  "accent-token": 1,
  mathord: 1,
  "op-token": 1,
  spacing: 1,
  textord: 1
};

const symbols = {
  math: {},
  text: {}
};

/** `acceptUnicodeChar = true` is only applicable if `replace` is set. */
function defineSymbol(mode, group, replace, name, acceptUnicodeChar) {
  symbols[mode][name] = { group, replace };

  if (acceptUnicodeChar && replace) {
    symbols[mode][replace] = symbols[mode][name];
  }
}

// Some abbreviations for commonly used strings.
// This helps minify the code, and also spotting typos using jshint.

// modes:
const math = "math";
const temml_text = "text";

// groups:
const accent = "accent-token";
const bin = "bin";
const temml_close = "close";
const inner = "inner";
const mathord = "mathord";
const op = "op-token";
const temml_open = "open";
const punct = "punct";
const rel = "rel";
const spacing = "spacing";
const textord = "textord";

// Now comes the symbol table

// Relation Symbols
defineSymbol(math, rel, "\u2261", "\\equiv", true);
defineSymbol(math, rel, "\u227a", "\\prec", true);
defineSymbol(math, rel, "\u227b", "\\succ", true);
defineSymbol(math, rel, "\u223c", "\\sim", true);
defineSymbol(math, rel, "\u27c2", "\\perp", true);
defineSymbol(math, rel, "\u2aaf", "\\preceq", true);
defineSymbol(math, rel, "\u2ab0", "\\succeq", true);
defineSymbol(math, rel, "\u2243", "\\simeq", true);
defineSymbol(math, rel, "\u224c", "\\backcong", true);
defineSymbol(math, rel, "|", "\\mid", true);
defineSymbol(math, rel, "\u226a", "\\ll", true);
defineSymbol(math, rel, "\u226b", "\\gg", true);
defineSymbol(math, rel, "\u224d", "\\asymp", true);
defineSymbol(math, rel, "\u2225", "\\parallel");
defineSymbol(math, rel, "\u2323", "\\smile", true);
defineSymbol(math, rel, "\u2291", "\\sqsubseteq", true);
defineSymbol(math, rel, "\u2292", "\\sqsupseteq", true);
defineSymbol(math, rel, "\u2250", "\\doteq", true);
defineSymbol(math, rel, "\u2322", "\\frown", true);
defineSymbol(math, rel, "\u220b", "\\ni", true);
defineSymbol(math, rel, "\u220c", "\\notni", true);
defineSymbol(math, rel, "\u221d", "\\propto", true);
defineSymbol(math, rel, "\u22a2", "\\vdash", true);
defineSymbol(math, rel, "\u22a3", "\\dashv", true);
defineSymbol(math, rel, "\u220b", "\\owns");
defineSymbol(math, rel, "\u2258", "\\arceq", true);
defineSymbol(math, rel, "\u2259", "\\wedgeq", true);
defineSymbol(math, rel, "\u225a", "\\veeeq", true);
defineSymbol(math, rel, "\u225b", "\\stareq", true);
defineSymbol(math, rel, "\u225d", "\\eqdef", true);
defineSymbol(math, rel, "\u225e", "\\measeq", true);
defineSymbol(math, rel, "\u225f", "\\questeq", true);
defineSymbol(math, rel, "\u2260", "\\ne", true);
defineSymbol(math, rel, "\u2260", "\\neq");
// unicodemath
defineSymbol(math, rel, "\u2a75", "\\eqeq", true);
defineSymbol(math, rel, "\u2a76", "\\eqeqeq", true);
// mathtools.sty
defineSymbol(math, rel, "\u2237", "\\dblcolon", true);
defineSymbol(math, rel, "\u2254", "\\coloneqq", true);
defineSymbol(math, rel, "\u2255", "\\eqqcolon", true);
defineSymbol(math, rel, "\u2239", "\\eqcolon", true);
defineSymbol(math, rel, "\u2A74", "\\Coloneqq", true);

// Punctuation
defineSymbol(math, punct, "\u002e", "\\ldotp");
defineSymbol(math, punct, "\u00b7", "\\cdotp");

// Misc Symbols
defineSymbol(math, textord, "\u0023", "\\#");
defineSymbol(temml_text, textord, "\u0023", "\\#");
defineSymbol(math, textord, "\u0026", "\\&");
defineSymbol(temml_text, textord, "\u0026", "\\&");
defineSymbol(math, textord, "\u2135", "\\aleph", true);
defineSymbol(math, textord, "\u2200", "\\forall", true);
defineSymbol(math, textord, "\u210f", "\\hbar", true);
defineSymbol(math, textord, "\u2203", "\\exists", true);
// ∇ is actually a unary operator, not binary. But this works.
defineSymbol(math, bin, "\u2207", "\\nabla", true);
defineSymbol(math, textord, "\u266d", "\\flat", true);
defineSymbol(math, textord, "\u2113", "\\ell", true);
defineSymbol(math, textord, "\u266e", "\\natural", true);
defineSymbol(math, textord, "Å", "\\Angstrom", true);
defineSymbol(temml_text, textord, "Å", "\\Angstrom", true);
defineSymbol(math, textord, "\u2663", "\\clubsuit", true);
defineSymbol(math, textord, "\u2667", "\\varclubsuit", true);
defineSymbol(math, textord, "\u2118", "\\wp", true);
defineSymbol(math, textord, "\u266f", "\\sharp", true);
defineSymbol(math, textord, "\u2662", "\\diamondsuit", true);
defineSymbol(math, textord, "\u2666", "\\vardiamondsuit", true);
defineSymbol(math, textord, "\u211c", "\\Re", true);
defineSymbol(math, textord, "\u2661", "\\heartsuit", true);
defineSymbol(math, textord, "\u2665", "\\varheartsuit", true);
defineSymbol(math, textord, "\u2111", "\\Im", true);
defineSymbol(math, textord, "\u2660", "\\spadesuit", true);
defineSymbol(math, textord, "\u2664", "\\varspadesuit", true);
defineSymbol(math, textord, "\u2640", "\\female", true);
defineSymbol(math, textord, "\u2642", "\\male", true);
defineSymbol(math, textord, "\u00a7", "\\S", true);
defineSymbol(temml_text, textord, "\u00a7", "\\S");
defineSymbol(math, textord, "\u00b6", "\\P", true);
defineSymbol(temml_text, textord, "\u00b6", "\\P");
defineSymbol(temml_text, textord, "\u263a", "\\smiley", true);
defineSymbol(math, textord, "\u263a", "\\smiley", true);

// Math and Text
defineSymbol(math, textord, "\u2020", "\\dag");
defineSymbol(temml_text, textord, "\u2020", "\\dag");
defineSymbol(temml_text, textord, "\u2020", "\\textdagger");
defineSymbol(math, textord, "\u2021", "\\ddag");
defineSymbol(temml_text, textord, "\u2021", "\\ddag");
defineSymbol(temml_text, textord, "\u2021", "\\textdaggerdbl");

// Large Delimiters
defineSymbol(math, temml_close, "\u23b1", "\\rmoustache", true);
defineSymbol(math, temml_open, "\u23b0", "\\lmoustache", true);
defineSymbol(math, temml_close, "\u27ef", "\\rgroup", true);
defineSymbol(math, temml_open, "\u27ee", "\\lgroup", true);

// Binary Operators
defineSymbol(math, bin, "\u2213", "\\mp", true);
defineSymbol(math, bin, "\u2296", "\\ominus", true);
defineSymbol(math, bin, "\u228e", "\\uplus", true);
defineSymbol(math, bin, "\u2293", "\\sqcap", true);
defineSymbol(math, bin, "\u2217", "\\ast");
defineSymbol(math, bin, "\u2294", "\\sqcup", true);
defineSymbol(math, bin, "\u25ef", "\\bigcirc", true);
defineSymbol(math, bin, "\u2219", "\\bullet", true);
defineSymbol(math, bin, "\u2021", "\\ddagger");
defineSymbol(math, bin, "\u2240", "\\wr", true);
defineSymbol(math, bin, "\u2a3f", "\\amalg");
defineSymbol(math, bin, "\u0026", "\\And"); // from amsmath
defineSymbol(math, bin, "\u2AFD", "\\sslash", true); // from stmaryrd

// Arrow Symbols
defineSymbol(math, rel, "\u27f5", "\\longleftarrow", true);
defineSymbol(math, rel, "\u21d0", "\\Leftarrow", true);
defineSymbol(math, rel, "\u27f8", "\\Longleftarrow", true);
defineSymbol(math, rel, "\u27f6", "\\longrightarrow", true);
defineSymbol(math, rel, "\u21d2", "\\Rightarrow", true);
defineSymbol(math, rel, "\u27f9", "\\Longrightarrow", true);
defineSymbol(math, rel, "\u2194", "\\leftrightarrow", true);
defineSymbol(math, rel, "\u27f7", "\\longleftrightarrow", true);
defineSymbol(math, rel, "\u21d4", "\\Leftrightarrow", true);
defineSymbol(math, rel, "\u27fa", "\\Longleftrightarrow", true);
defineSymbol(math, rel, "\u21a4", "\\mapsfrom", true);
defineSymbol(math, rel, "\u21a6", "\\mapsto", true);
defineSymbol(math, rel, "\u27fc", "\\longmapsto", true);
defineSymbol(math, rel, "\u2197", "\\nearrow", true);
defineSymbol(math, rel, "\u21a9", "\\hookleftarrow", true);
defineSymbol(math, rel, "\u21aa", "\\hookrightarrow", true);
defineSymbol(math, rel, "\u2198", "\\searrow", true);
defineSymbol(math, rel, "\u21bc", "\\leftharpoonup", true);
defineSymbol(math, rel, "\u21c0", "\\rightharpoonup", true);
defineSymbol(math, rel, "\u2199", "\\swarrow", true);
defineSymbol(math, rel, "\u21bd", "\\leftharpoondown", true);
defineSymbol(math, rel, "\u21c1", "\\rightharpoondown", true);
defineSymbol(math, rel, "\u2196", "\\nwarrow", true);
defineSymbol(math, rel, "\u21cc", "\\rightleftharpoons", true);
defineSymbol(math, mathord, "\u21af", "\\lightning", true);
defineSymbol(math, mathord, "\u220E", "\\QED", true);
defineSymbol(math, mathord, "\u2030", "\\permil", true);
defineSymbol(temml_text, textord, "\u2030", "\\permil");
defineSymbol(math, mathord, "\u2609", "\\astrosun", true);
defineSymbol(math, mathord, "\u263c", "\\sun", true);
defineSymbol(math, mathord, "\u263e", "\\leftmoon", true);
defineSymbol(math, mathord, "\u263d", "\\rightmoon", true);
defineSymbol(math, mathord, "\u2295", "\\Earth");

// AMS Negated Binary Relations
defineSymbol(math, rel, "\u226e", "\\nless", true);
// Symbol names preceeded by "@" each have a corresponding macro.
defineSymbol(math, rel, "\u2a87", "\\lneq", true);
defineSymbol(math, rel, "\u2268", "\\lneqq", true);
defineSymbol(math, rel, "\u2268\ufe00", "\\lvertneqq");
defineSymbol(math, rel, "\u22e6", "\\lnsim", true);
defineSymbol(math, rel, "\u2a89", "\\lnapprox", true);
defineSymbol(math, rel, "\u2280", "\\nprec", true);
// unicode-math maps \u22e0 to \npreccurlyeq. We'll use the AMS synonym.
defineSymbol(math, rel, "\u22e0", "\\npreceq", true);
defineSymbol(math, rel, "\u22e8", "\\precnsim", true);
defineSymbol(math, rel, "\u2ab9", "\\precnapprox", true);
defineSymbol(math, rel, "\u2241", "\\nsim", true);
defineSymbol(math, rel, "\u2224", "\\nmid", true);
defineSymbol(math, rel, "\u2224", "\\nshortmid");
defineSymbol(math, rel, "\u22ac", "\\nvdash", true);
defineSymbol(math, rel, "\u22ad", "\\nvDash", true);
defineSymbol(math, rel, "\u22ea", "\\ntriangleleft");
defineSymbol(math, rel, "\u22ec", "\\ntrianglelefteq", true);
defineSymbol(math, rel, "\u2284", "\\nsubset", true);
defineSymbol(math, rel, "\u2285", "\\nsupset", true);
defineSymbol(math, rel, "\u228a", "\\subsetneq", true);
defineSymbol(math, rel, "\u228a\ufe00", "\\varsubsetneq");
defineSymbol(math, rel, "\u2acb", "\\subsetneqq", true);
defineSymbol(math, rel, "\u2acb\ufe00", "\\varsubsetneqq");
defineSymbol(math, rel, "\u226f", "\\ngtr", true);
defineSymbol(math, rel, "\u2a88", "\\gneq", true);
defineSymbol(math, rel, "\u2269", "\\gneqq", true);
defineSymbol(math, rel, "\u2269\ufe00", "\\gvertneqq");
defineSymbol(math, rel, "\u22e7", "\\gnsim", true);
defineSymbol(math, rel, "\u2a8a", "\\gnapprox", true);
defineSymbol(math, rel, "\u2281", "\\nsucc", true);
// unicode-math maps \u22e1 to \nsucccurlyeq. We'll use the AMS synonym.
defineSymbol(math, rel, "\u22e1", "\\nsucceq", true);
defineSymbol(math, rel, "\u22e9", "\\succnsim", true);
defineSymbol(math, rel, "\u2aba", "\\succnapprox", true);
// unicode-math maps \u2246 to \simneqq. We'll use the AMS synonym.
defineSymbol(math, rel, "\u2246", "\\ncong", true);
defineSymbol(math, rel, "\u2226", "\\nparallel", true);
defineSymbol(math, rel, "\u2226", "\\nshortparallel");
defineSymbol(math, rel, "\u22af", "\\nVDash", true);
defineSymbol(math, rel, "\u22eb", "\\ntriangleright");
defineSymbol(math, rel, "\u22ed", "\\ntrianglerighteq", true);
defineSymbol(math, rel, "\u228b", "\\supsetneq", true);
defineSymbol(math, rel, "\u228b", "\\varsupsetneq");
defineSymbol(math, rel, "\u2acc", "\\supsetneqq", true);
defineSymbol(math, rel, "\u2acc\ufe00", "\\varsupsetneqq");
defineSymbol(math, rel, "\u22ae", "\\nVdash", true);
defineSymbol(math, rel, "\u2ab5", "\\precneqq", true);
defineSymbol(math, rel, "\u2ab6", "\\succneqq", true);
defineSymbol(math, bin, "\u22b4", "\\unlhd");
defineSymbol(math, bin, "\u22b5", "\\unrhd");

// AMS Negated Arrows
defineSymbol(math, rel, "\u219a", "\\nleftarrow", true);
defineSymbol(math, rel, "\u219b", "\\nrightarrow", true);
defineSymbol(math, rel, "\u21cd", "\\nLeftarrow", true);
defineSymbol(math, rel, "\u21cf", "\\nRightarrow", true);
defineSymbol(math, rel, "\u21ae", "\\nleftrightarrow", true);
defineSymbol(math, rel, "\u21ce", "\\nLeftrightarrow", true);

// AMS Misc
defineSymbol(math, rel, "\u25b3", "\\vartriangle");
defineSymbol(math, textord, "\u210f", "\\hslash");
defineSymbol(math, textord, "\u25bd", "\\triangledown");
defineSymbol(math, textord, "\u25ca", "\\lozenge");
defineSymbol(math, textord, "\u24c8", "\\circledS");
defineSymbol(math, textord, "\u00ae", "\\circledR", true);
defineSymbol(temml_text, textord, "\u00ae", "\\circledR");
defineSymbol(temml_text, textord, "\u00ae", "\\textregistered");
defineSymbol(math, textord, "\u2221", "\\measuredangle", true);
defineSymbol(math, textord, "\u2204", "\\nexists");
defineSymbol(math, textord, "\u2127", "\\mho");
defineSymbol(math, textord, "\u2132", "\\Finv", true);
defineSymbol(math, textord, "\u2141", "\\Game", true);
defineSymbol(math, textord, "\u2035", "\\backprime");
defineSymbol(math, textord, "\u2036", "\\backdprime");
defineSymbol(math, textord, "\u2037", "\\backtrprime");
defineSymbol(math, textord, "\u25b2", "\\blacktriangle");
defineSymbol(math, textord, "\u25bc", "\\blacktriangledown");
defineSymbol(math, textord, "\u25a0", "\\blacksquare");
defineSymbol(math, textord, "\u29eb", "\\blacklozenge");
defineSymbol(math, textord, "\u2605", "\\bigstar");
defineSymbol(math, textord, "\u2222", "\\sphericalangle", true);
defineSymbol(math, textord, "\u2201", "\\complement", true);
// unicode-math maps U+F0 to \matheth. We map to AMS function \eth
defineSymbol(math, textord, "\u00f0", "\\eth", true);
defineSymbol(temml_text, textord, "\u00f0", "\u00f0");
defineSymbol(math, textord, "\u2571", "\\diagup");
defineSymbol(math, textord, "\u2572", "\\diagdown");
defineSymbol(math, textord, "\u25a1", "\\square");
defineSymbol(math, textord, "\u25a1", "\\Box");
defineSymbol(math, textord, "\u25ca", "\\Diamond");
// unicode-math maps U+A5 to \mathyen. We map to AMS function \yen
defineSymbol(math, textord, "\u00a5", "\\yen", true);
defineSymbol(temml_text, textord, "\u00a5", "\\yen", true);
defineSymbol(math, textord, "\u2713", "\\checkmark", true);
defineSymbol(temml_text, textord, "\u2713", "\\checkmark");
defineSymbol(math, textord, "\u2717", "\\ballotx", true);
defineSymbol(temml_text, textord, "\u2717", "\\ballotx");
defineSymbol(temml_text, textord, "\u2022", "\\textbullet");

// AMS Hebrew
defineSymbol(math, textord, "\u2136", "\\beth", true);
defineSymbol(math, textord, "\u2138", "\\daleth", true);
defineSymbol(math, textord, "\u2137", "\\gimel", true);

// AMS Greek
defineSymbol(math, textord, "\u03dd", "\\digamma", true);
defineSymbol(math, textord, "\u03f0", "\\varkappa");

// AMS Delimiters
defineSymbol(math, temml_open, "\u231C", "\\ulcorner", true);
defineSymbol(math, temml_close, "\u231D", "\\urcorner", true);
defineSymbol(math, temml_open, "\u231E", "\\llcorner", true);
defineSymbol(math, temml_close, "\u231F", "\\lrcorner", true);

// AMS Binary Relations
defineSymbol(math, rel, "\u2266", "\\leqq", true);
defineSymbol(math, rel, "\u2a7d", "\\leqslant", true);
defineSymbol(math, rel, "\u2a95", "\\eqslantless", true);
defineSymbol(math, rel, "\u2272", "\\lesssim", true);
defineSymbol(math, rel, "\u2a85", "\\lessapprox", true);
defineSymbol(math, rel, "\u224a", "\\approxeq", true);
defineSymbol(math, bin, "\u22d6", "\\lessdot");
defineSymbol(math, rel, "\u22d8", "\\lll", true);
defineSymbol(math, rel, "\u2276", "\\lessgtr", true);
defineSymbol(math, rel, "\u22da", "\\lesseqgtr", true);
defineSymbol(math, rel, "\u2a8b", "\\lesseqqgtr", true);
defineSymbol(math, rel, "\u2251", "\\doteqdot");
defineSymbol(math, rel, "\u2253", "\\risingdotseq", true);
defineSymbol(math, rel, "\u2252", "\\fallingdotseq", true);
defineSymbol(math, rel, "\u223d", "\\backsim", true);
defineSymbol(math, rel, "\u22cd", "\\backsimeq", true);
defineSymbol(math, rel, "\u2ac5", "\\subseteqq", true);
defineSymbol(math, rel, "\u22d0", "\\Subset", true);
defineSymbol(math, rel, "\u228f", "\\sqsubset", true);
defineSymbol(math, rel, "\u227c", "\\preccurlyeq", true);
defineSymbol(math, rel, "\u22de", "\\curlyeqprec", true);
defineSymbol(math, rel, "\u227e", "\\precsim", true);
defineSymbol(math, rel, "\u2ab7", "\\precapprox", true);
defineSymbol(math, rel, "\u22b2", "\\vartriangleleft");
defineSymbol(math, rel, "\u22b4", "\\trianglelefteq");
defineSymbol(math, rel, "\u22a8", "\\vDash", true);
defineSymbol(math, rel, "\u22ab", "\\VDash", true);
defineSymbol(math, rel, "\u22aa", "\\Vvdash", true);
defineSymbol(math, rel, "\u2323", "\\smallsmile");
defineSymbol(math, rel, "\u2322", "\\smallfrown");
defineSymbol(math, rel, "\u224f", "\\bumpeq", true);
defineSymbol(math, rel, "\u224e", "\\Bumpeq", true);
defineSymbol(math, rel, "\u2267", "\\geqq", true);
defineSymbol(math, rel, "\u2a7e", "\\geqslant", true);
defineSymbol(math, rel, "\u2a96", "\\eqslantgtr", true);
defineSymbol(math, rel, "\u2273", "\\gtrsim", true);
defineSymbol(math, rel, "\u2a86", "\\gtrapprox", true);
defineSymbol(math, bin, "\u22d7", "\\gtrdot");
defineSymbol(math, rel, "\u22d9", "\\ggg", true);
defineSymbol(math, rel, "\u2277", "\\gtrless", true);
defineSymbol(math, rel, "\u22db", "\\gtreqless", true);
defineSymbol(math, rel, "\u2a8c", "\\gtreqqless", true);
defineSymbol(math, rel, "\u2256", "\\eqcirc", true);
defineSymbol(math, rel, "\u2257", "\\circeq", true);
defineSymbol(math, rel, "\u225c", "\\triangleq", true);
defineSymbol(math, rel, "\u223c", "\\thicksim");
defineSymbol(math, rel, "\u2248", "\\thickapprox");
defineSymbol(math, rel, "\u2ac6", "\\supseteqq", true);
defineSymbol(math, rel, "\u22d1", "\\Supset", true);
defineSymbol(math, rel, "\u2290", "\\sqsupset", true);
defineSymbol(math, rel, "\u227d", "\\succcurlyeq", true);
defineSymbol(math, rel, "\u22df", "\\curlyeqsucc", true);
defineSymbol(math, rel, "\u227f", "\\succsim", true);
defineSymbol(math, rel, "\u2ab8", "\\succapprox", true);
defineSymbol(math, rel, "\u22b3", "\\vartriangleright");
defineSymbol(math, rel, "\u22b5", "\\trianglerighteq");
defineSymbol(math, rel, "\u22a9", "\\Vdash", true);
defineSymbol(math, rel, "\u2223", "\\shortmid");
defineSymbol(math, rel, "\u2225", "\\shortparallel");
defineSymbol(math, rel, "\u226c", "\\between", true);
defineSymbol(math, rel, "\u22d4", "\\pitchfork", true);
defineSymbol(math, rel, "\u221d", "\\varpropto");
defineSymbol(math, rel, "\u25c0", "\\blacktriangleleft");
// unicode-math says that \therefore is a mathord atom.
// We kept the amssymb atom type, which is rel.
defineSymbol(math, rel, "\u2234", "\\therefore", true);
defineSymbol(math, rel, "\u220d", "\\backepsilon");
defineSymbol(math, rel, "\u25b6", "\\blacktriangleright");
// unicode-math says that \because is a mathord atom.
// We kept the amssymb atom type, which is rel.
defineSymbol(math, rel, "\u2235", "\\because", true);
defineSymbol(math, rel, "\u22d8", "\\llless");
defineSymbol(math, rel, "\u22d9", "\\gggtr");
defineSymbol(math, bin, "\u22b2", "\\lhd");
defineSymbol(math, bin, "\u22b3", "\\rhd");
defineSymbol(math, rel, "\u2242", "\\eqsim", true);
defineSymbol(math, rel, "\u2251", "\\Doteq", true);
defineSymbol(math, rel, "\u297d", "\\strictif", true);
defineSymbol(math, rel, "\u297c", "\\strictfi", true);

// AMS Binary Operators
defineSymbol(math, bin, "\u2214", "\\dotplus", true);
defineSymbol(math, bin, "\u2216", "\\smallsetminus");
defineSymbol(math, bin, "\u22d2", "\\Cap", true);
defineSymbol(math, bin, "\u22d3", "\\Cup", true);
defineSymbol(math, bin, "\u2a5e", "\\doublebarwedge", true);
defineSymbol(math, bin, "\u229f", "\\boxminus", true);
defineSymbol(math, bin, "\u229e", "\\boxplus", true);
defineSymbol(math, bin, "\u29C4", "\\boxslash", true);
defineSymbol(math, bin, "\u22c7", "\\divideontimes", true);
defineSymbol(math, bin, "\u22c9", "\\ltimes", true);
defineSymbol(math, bin, "\u22ca", "\\rtimes", true);
defineSymbol(math, bin, "\u22cb", "\\leftthreetimes", true);
defineSymbol(math, bin, "\u22cc", "\\rightthreetimes", true);
defineSymbol(math, bin, "\u22cf", "\\curlywedge", true);
defineSymbol(math, bin, "\u22ce", "\\curlyvee", true);
defineSymbol(math, bin, "\u229d", "\\circleddash", true);
defineSymbol(math, bin, "\u229b", "\\circledast", true);
defineSymbol(math, bin, "\u22ba", "\\intercal", true);
defineSymbol(math, bin, "\u22d2", "\\doublecap");
defineSymbol(math, bin, "\u22d3", "\\doublecup");
defineSymbol(math, bin, "\u22a0", "\\boxtimes", true);
defineSymbol(math, bin, "\u22c8", "\\bowtie", true);
defineSymbol(math, bin, "\u22c8", "\\Join");
defineSymbol(math, bin, "\u27d5", "\\leftouterjoin", true);
defineSymbol(math, bin, "\u27d6", "\\rightouterjoin", true);
defineSymbol(math, bin, "\u27d7", "\\fullouterjoin", true);

// stix Binary Operators
defineSymbol(math, bin, "\u2238", "\\dotminus", true);
defineSymbol(math, bin, "\u27D1", "\\wedgedot", true);
defineSymbol(math, bin, "\u27C7", "\\veedot", true);
defineSymbol(math, bin, "\u2A62", "\\doublebarvee", true);
defineSymbol(math, bin, "\u2A63", "\\veedoublebar", true);
defineSymbol(math, bin, "\u2A5F", "\\wedgebar", true);
defineSymbol(math, bin, "\u2A60", "\\wedgedoublebar", true);
defineSymbol(math, bin, "\u2A54", "\\Vee", true);
defineSymbol(math, bin, "\u2A53", "\\Wedge", true);
defineSymbol(math, bin, "\u2A43", "\\barcap", true);
defineSymbol(math, bin, "\u2A42", "\\barcup", true);
defineSymbol(math, bin, "\u2A48", "\\capbarcup", true);
defineSymbol(math, bin, "\u2A40", "\\capdot", true);
defineSymbol(math, bin, "\u2A47", "\\capovercup", true);
defineSymbol(math, bin, "\u2A46", "\\cupovercap", true);
defineSymbol(math, bin, "\u2A4D", "\\closedvarcap", true);
defineSymbol(math, bin, "\u2A4C", "\\closedvarcup", true);
defineSymbol(math, bin, "\u2A2A", "\\minusdot", true);
defineSymbol(math, bin, "\u2A2B", "\\minusfdots", true);
defineSymbol(math, bin, "\u2A2C", "\\minusrdots", true);
defineSymbol(math, bin, "\u22BB", "\\Xor", true);
defineSymbol(math, bin, "\u22BC", "\\Nand", true);
defineSymbol(math, bin, "\u22BD", "\\Nor", true);
defineSymbol(math, bin, "\u22BD", "\\barvee");
defineSymbol(math, bin, "\u2AF4", "\\interleave", true);
defineSymbol(math, bin, "\u29E2", "\\shuffle", true);
defineSymbol(math, bin, "\u2AF6", "\\threedotcolon", true);
defineSymbol(math, bin, "\u2982", "\\typecolon", true);
defineSymbol(math, bin, "\u223E", "\\invlazys", true);
defineSymbol(math, bin, "\u2A4B", "\\twocaps", true);
defineSymbol(math, bin, "\u2A4A", "\\twocups", true);
defineSymbol(math, bin, "\u2A4E", "\\Sqcap", true);
defineSymbol(math, bin, "\u2A4F", "\\Sqcup", true);
defineSymbol(math, bin, "\u2A56", "\\veeonvee", true);
defineSymbol(math, bin, "\u2A55", "\\wedgeonwedge", true);
defineSymbol(math, bin, "\u29D7", "\\blackhourglass", true);
defineSymbol(math, bin, "\u29C6", "\\boxast", true);
defineSymbol(math, bin, "\u29C8", "\\boxbox", true);
defineSymbol(math, bin, "\u29C7", "\\boxcircle", true);
defineSymbol(math, bin, "\u229C", "\\circledequal", true);
defineSymbol(math, bin, "\u29B7", "\\circledparallel", true);
defineSymbol(math, bin, "\u29B6", "\\circledvert", true);
defineSymbol(math, bin, "\u29B5", "\\circlehbar", true);
defineSymbol(math, bin, "\u27E1", "\\concavediamond", true);
defineSymbol(math, bin, "\u27E2", "\\concavediamondtickleft", true);
defineSymbol(math, bin, "\u27E3", "\\concavediamondtickright", true);
defineSymbol(math, bin, "\u22C4", "\\diamond", true);
defineSymbol(math, bin, "\u29D6", "\\hourglass", true);
defineSymbol(math, bin, "\u27E0", "\\lozengeminus", true);
defineSymbol(math, bin, "\u233D", "\\obar", true);
defineSymbol(math, bin, "\u29B8", "\\obslash", true);
defineSymbol(math, bin, "\u2A38", "\\odiv", true);
defineSymbol(math, bin, "\u29C1", "\\ogreaterthan", true);
defineSymbol(math, bin, "\u29C0", "\\olessthan", true);
defineSymbol(math, bin, "\u29B9", "\\operp", true);
defineSymbol(math, bin, "\u2A37", "\\Otimes", true);
defineSymbol(math, bin, "\u2A36", "\\otimeshat", true);
defineSymbol(math, bin, "\u22C6", "\\star", true);
defineSymbol(math, bin, "\u25B3", "\\triangle", true);
defineSymbol(math, bin, "\u2A3A", "\\triangleminus", true);
defineSymbol(math, bin, "\u2A39", "\\triangleplus", true);
defineSymbol(math, bin, "\u2A3B", "\\triangletimes", true);
defineSymbol(math, bin, "\u27E4", "\\whitesquaretickleft", true);
defineSymbol(math, bin, "\u27E5", "\\whitesquaretickright", true);
defineSymbol(math, bin, "\u2A33", "\\smashtimes", true);

// AMS Arrows
// Note: unicode-math maps \u21e2 to their own function \rightdasharrow.
// We'll map it to AMS function \dashrightarrow. It produces the same atom.
defineSymbol(math, rel, "\u21e2", "\\dashrightarrow", true);
// unicode-math maps \u21e0 to \leftdasharrow. We'll use the AMS synonym.
defineSymbol(math, rel, "\u21e0", "\\dashleftarrow", true);
defineSymbol(math, rel, "\u21c7", "\\leftleftarrows", true);
defineSymbol(math, rel, "\u21c6", "\\leftrightarrows", true);
defineSymbol(math, rel, "\u21da", "\\Lleftarrow", true);
defineSymbol(math, rel, "\u219e", "\\twoheadleftarrow", true);
defineSymbol(math, rel, "\u21a2", "\\leftarrowtail", true);
defineSymbol(math, rel, "\u21ab", "\\looparrowleft", true);
defineSymbol(math, rel, "\u21cb", "\\leftrightharpoons", true);
defineSymbol(math, rel, "\u21b6", "\\curvearrowleft", true);
// unicode-math maps \u21ba to \acwopencirclearrow. We'll use the AMS synonym.
defineSymbol(math, rel, "\u21ba", "\\circlearrowleft", true);
defineSymbol(math, rel, "\u21b0", "\\Lsh", true);
defineSymbol(math, rel, "\u21c8", "\\upuparrows", true);
defineSymbol(math, rel, "\u21bf", "\\upharpoonleft", true);
defineSymbol(math, rel, "\u21c3", "\\downharpoonleft", true);
defineSymbol(math, rel, "\u22b6", "\\origof", true);
defineSymbol(math, rel, "\u22b7", "\\imageof", true);
defineSymbol(math, rel, "\u22b8", "\\multimap", true);
defineSymbol(math, rel, "\u21ad", "\\leftrightsquigarrow", true);
defineSymbol(math, rel, "\u21c9", "\\rightrightarrows", true);
defineSymbol(math, rel, "\u21c4", "\\rightleftarrows", true);
defineSymbol(math, rel, "\u21a0", "\\twoheadrightarrow", true);
defineSymbol(math, rel, "\u21a3", "\\rightarrowtail", true);
defineSymbol(math, rel, "\u21ac", "\\looparrowright", true);
defineSymbol(math, rel, "\u21b7", "\\curvearrowright", true);
// unicode-math maps \u21bb to \cwopencirclearrow. We'll use the AMS synonym.
defineSymbol(math, rel, "\u21bb", "\\circlearrowright", true);
defineSymbol(math, rel, "\u21b1", "\\Rsh", true);
defineSymbol(math, rel, "\u21ca", "\\downdownarrows", true);
defineSymbol(math, rel, "\u21be", "\\upharpoonright", true);
defineSymbol(math, rel, "\u21c2", "\\downharpoonright", true);
defineSymbol(math, rel, "\u21dd", "\\rightsquigarrow", true);
defineSymbol(math, rel, "\u21dd", "\\leadsto");
defineSymbol(math, rel, "\u21db", "\\Rrightarrow", true);
defineSymbol(math, rel, "\u21be", "\\restriction");

defineSymbol(math, textord, "\u2018", "`");
defineSymbol(math, textord, "$", "\\$");
defineSymbol(temml_text, textord, "$", "\\$");
defineSymbol(temml_text, textord, "$", "\\textdollar");
defineSymbol(math, textord, "¢", "\\cent");
defineSymbol(temml_text, textord, "¢", "\\cent");
defineSymbol(math, textord, "%", "\\%");
defineSymbol(temml_text, textord, "%", "\\%");
defineSymbol(math, textord, "_", "\\_");
defineSymbol(temml_text, textord, "_", "\\_");
defineSymbol(temml_text, textord, "_", "\\textunderscore");
defineSymbol(temml_text, textord, "\u2423", "\\textvisiblespace", true);
defineSymbol(math, textord, "\u2220", "\\angle", true);
defineSymbol(math, textord, "\u221e", "\\infty", true);
defineSymbol(math, textord, "\u2032", "\\prime");
defineSymbol(math, textord, "\u2033", "\\dprime");
defineSymbol(math, textord, "\u2034", "\\trprime");
defineSymbol(math, textord, "\u2057", "\\qprime");
defineSymbol(math, textord, "\u25b3", "\\triangle");
defineSymbol(temml_text, textord, "\u0391", "\\Alpha", true);
defineSymbol(temml_text, textord, "\u0392", "\\Beta", true);
defineSymbol(temml_text, textord, "\u0393", "\\Gamma", true);
defineSymbol(temml_text, textord, "\u0394", "\\Delta", true);
defineSymbol(temml_text, textord, "\u0395", "\\Epsilon", true);
defineSymbol(temml_text, textord, "\u0396", "\\Zeta", true);
defineSymbol(temml_text, textord, "\u0397", "\\Eta", true);
defineSymbol(temml_text, textord, "\u0398", "\\Theta", true);
defineSymbol(temml_text, textord, "\u0399", "\\Iota", true);
defineSymbol(temml_text, textord, "\u039a", "\\Kappa", true);
defineSymbol(temml_text, textord, "\u039b", "\\Lambda", true);
defineSymbol(temml_text, textord, "\u039c", "\\Mu", true);
defineSymbol(temml_text, textord, "\u039d", "\\Nu", true);
defineSymbol(temml_text, textord, "\u039e", "\\Xi", true);
defineSymbol(temml_text, textord, "\u039f", "\\Omicron", true);
defineSymbol(temml_text, textord, "\u03a0", "\\Pi", true);
defineSymbol(temml_text, textord, "\u03a1", "\\Rho", true);
defineSymbol(temml_text, textord, "\u03a3", "\\Sigma", true);
defineSymbol(temml_text, textord, "\u03a4", "\\Tau", true);
defineSymbol(temml_text, textord, "\u03a5", "\\Upsilon", true);
defineSymbol(temml_text, textord, "\u03a6", "\\Phi", true);
defineSymbol(temml_text, textord, "\u03a7", "\\Chi", true);
defineSymbol(temml_text, textord, "\u03a8", "\\Psi", true);
defineSymbol(temml_text, textord, "\u03a9", "\\Omega", true);
defineSymbol(math, mathord, "\u0391", "\\Alpha", true);
defineSymbol(math, mathord, "\u0392", "\\Beta", true);
defineSymbol(math, mathord, "\u0393", "\\Gamma", true);
defineSymbol(math, mathord, "\u0394", "\\Delta", true);
defineSymbol(math, mathord, "\u0395", "\\Epsilon", true);
defineSymbol(math, mathord, "\u0396", "\\Zeta", true);
defineSymbol(math, mathord, "\u0397", "\\Eta", true);
defineSymbol(math, mathord, "\u0398", "\\Theta", true);
defineSymbol(math, mathord, "\u0399", "\\Iota", true);
defineSymbol(math, mathord, "\u039a", "\\Kappa", true);
defineSymbol(math, mathord, "\u039b", "\\Lambda", true);
defineSymbol(math, mathord, "\u039c", "\\Mu", true);
defineSymbol(math, mathord, "\u039d", "\\Nu", true);
defineSymbol(math, mathord, "\u039e", "\\Xi", true);
defineSymbol(math, mathord, "\u039f", "\\Omicron", true);
defineSymbol(math, mathord, "\u03a0", "\\Pi", true);
defineSymbol(math, mathord, "\u03a1", "\\Rho", true);
defineSymbol(math, mathord, "\u03a3", "\\Sigma", true);
defineSymbol(math, mathord, "\u03a4", "\\Tau", true);
defineSymbol(math, mathord, "\u03a5", "\\Upsilon", true);
defineSymbol(math, mathord, "\u03a6", "\\Phi", true);
defineSymbol(math, mathord, "\u03a7", "\\Chi", true);
defineSymbol(math, mathord, "\u03a8", "\\Psi", true);
defineSymbol(math, mathord, "\u03a9", "\\Omega", true);
defineSymbol(math, temml_open, "\u00ac", "\\neg", true);
defineSymbol(math, temml_open, "\u00ac", "\\lnot");
defineSymbol(math, textord, "\u22a4", "\\top");
defineSymbol(math, textord, "\u22a5", "\\bot");
defineSymbol(math, textord, "\u2205", "\\emptyset");
defineSymbol(math, textord, "\u2300", "\\varnothing");
defineSymbol(math, mathord, "\u03b1", "\\alpha", true);
defineSymbol(math, mathord, "\u03b2", "\\beta", true);
defineSymbol(math, mathord, "\u03b3", "\\gamma", true);
defineSymbol(math, mathord, "\u03b4", "\\delta", true);
defineSymbol(math, mathord, "\u03f5", "\\epsilon", true);
defineSymbol(math, mathord, "\u03b6", "\\zeta", true);
defineSymbol(math, mathord, "\u03b7", "\\eta", true);
defineSymbol(math, mathord, "\u03b8", "\\theta", true);
defineSymbol(math, mathord, "\u03b9", "\\iota", true);
defineSymbol(math, mathord, "\u03ba", "\\kappa", true);
defineSymbol(math, mathord, "\u03bb", "\\lambda", true);
defineSymbol(math, mathord, "\u03bc", "\\mu", true);
defineSymbol(math, mathord, "\u03bd", "\\nu", true);
defineSymbol(math, mathord, "\u03be", "\\xi", true);
defineSymbol(math, mathord, "\u03bf", "\\omicron", true);
defineSymbol(math, mathord, "\u03c0", "\\pi", true);
defineSymbol(math, mathord, "\u03c1", "\\rho", true);
defineSymbol(math, mathord, "\u03c3", "\\sigma", true);
defineSymbol(math, mathord, "\u03c4", "\\tau", true);
defineSymbol(math, mathord, "\u03c5", "\\upsilon", true);
defineSymbol(math, mathord, "\u03d5", "\\phi", true);
defineSymbol(math, mathord, "\u03c7", "\\chi", true);
defineSymbol(math, mathord, "\u03c8", "\\psi", true);
defineSymbol(math, mathord, "\u03c9", "\\omega", true);
defineSymbol(math, mathord, "\u03b5", "\\varepsilon", true);
defineSymbol(math, mathord, "\u03d1", "\\vartheta", true);
defineSymbol(math, mathord, "\u03d6", "\\varpi", true);
defineSymbol(math, mathord, "\u03f1", "\\varrho", true);
defineSymbol(math, mathord, "\u03c2", "\\varsigma", true);
defineSymbol(math, mathord, "\u03c6", "\\varphi", true);
defineSymbol(math, mathord, "\u03d8", "\\Coppa", true);
defineSymbol(math, mathord, "\u03d9", "\\coppa", true);
defineSymbol(math, mathord, "\u03d9", "\\varcoppa", true);
defineSymbol(math, mathord, "\u03de", "\\Koppa", true);
defineSymbol(math, mathord, "\u03df", "\\koppa", true);
defineSymbol(math, mathord, "\u03e0", "\\Sampi", true);
defineSymbol(math, mathord, "\u03e1", "\\sampi", true);
defineSymbol(math, mathord, "\u03da", "\\Stigma", true);
defineSymbol(math, mathord, "\u03db", "\\stigma", true);
defineSymbol(math, mathord, "\u2aeb", "\\Bot");
defineSymbol(math, bin, "\u2217", "\u2217", true);
defineSymbol(math, bin, "+", "+");
defineSymbol(math, bin, "\u2217", "*");
defineSymbol(math, bin, "\u2044", "/", true);
defineSymbol(math, bin, "\u2044", "\u2044");
defineSymbol(math, bin, "\u2212", "-", true);
defineSymbol(math, bin, "\u22c5", "\\cdot", true);
defineSymbol(math, bin, "\u2218", "\\circ", true);
defineSymbol(math, bin, "\u00f7", "\\div", true);
defineSymbol(math, bin, "\u00b1", "\\pm", true);
defineSymbol(math, bin, "\u00d7", "\\times", true);
defineSymbol(math, bin, "\u2229", "\\cap", true);
defineSymbol(math, bin, "\u222a", "\\cup", true);
defineSymbol(math, bin, "\u2216", "\\setminus", true);
defineSymbol(math, bin, "\u2227", "\\land");
defineSymbol(math, bin, "\u2228", "\\lor");
defineSymbol(math, bin, "\u2227", "\\wedge", true);
defineSymbol(math, bin, "\u2228", "\\vee", true);
defineSymbol(math, temml_open, "\u27e6", "\\llbracket", true); // stmaryrd/semantic packages
defineSymbol(math, temml_close, "\u27e7", "\\rrbracket", true);
defineSymbol(math, temml_open, "\u27e8", "\\langle", true);
defineSymbol(math, temml_open, "\u27ea", "\\lAngle", true);
defineSymbol(math, temml_open, "\u2989", "\\llangle", true);
defineSymbol(math, temml_open, "|", "\\lvert");
defineSymbol(math, temml_open, "\u2016", "\\lVert", true);
defineSymbol(math, textord, "!", "\\oc"); // cmll package
defineSymbol(math, textord, "?", "\\wn");
defineSymbol(math, textord, "\u2193", "\\shpos");
defineSymbol(math, textord, "\u2195", "\\shift");
defineSymbol(math, textord, "\u2191", "\\shneg");
defineSymbol(math, temml_close, "?", "?");
defineSymbol(math, temml_close, "!", "!");
defineSymbol(math, temml_close, "‼", "‼");
defineSymbol(math, temml_close, "\u27e9", "\\rangle", true);
defineSymbol(math, temml_close, "\u27eb", "\\rAngle", true);
defineSymbol(math, temml_close, "\u298a", "\\rrangle", true);
defineSymbol(math, temml_close, "|", "\\rvert");
defineSymbol(math, temml_close, "\u2016", "\\rVert");
defineSymbol(math, temml_open, "\u2983", "\\lBrace", true); // stmaryrd/semantic packages
defineSymbol(math, temml_close, "\u2984", "\\rBrace", true);
defineSymbol(math, rel, "=", "\\equal", true);
defineSymbol(math, rel, ":", ":");
defineSymbol(math, rel, "\u2248", "\\approx", true);
defineSymbol(math, rel, "\u2245", "\\cong", true);
defineSymbol(math, rel, "\u2265", "\\ge");
defineSymbol(math, rel, "\u2265", "\\geq", true);
defineSymbol(math, rel, "\u2190", "\\gets");
defineSymbol(math, rel, ">", "\\gt", true);
defineSymbol(math, rel, "\u2208", "\\in", true);
defineSymbol(math, rel, "\u2209", "\\notin", true);
defineSymbol(math, rel, "\ue020", "\\@not");
defineSymbol(math, rel, "\u2282", "\\subset", true);
defineSymbol(math, rel, "\u2283", "\\supset", true);
defineSymbol(math, rel, "\u2286", "\\subseteq", true);
defineSymbol(math, rel, "\u2287", "\\supseteq", true);
defineSymbol(math, rel, "\u2288", "\\nsubseteq", true);
defineSymbol(math, rel, "\u2288", "\\nsubseteqq");
defineSymbol(math, rel, "\u2289", "\\nsupseteq", true);
defineSymbol(math, rel, "\u2289", "\\nsupseteqq");
defineSymbol(math, rel, "\u22a8", "\\models");
defineSymbol(math, rel, "\u2190", "\\leftarrow", true);
defineSymbol(math, rel, "\u2264", "\\le");
defineSymbol(math, rel, "\u2264", "\\leq", true);
defineSymbol(math, rel, "<", "\\lt", true);
defineSymbol(math, rel, "\u2192", "\\rightarrow", true);
defineSymbol(math, rel, "\u2192", "\\to");
defineSymbol(math, rel, "\u2271", "\\ngeq", true);
defineSymbol(math, rel, "\u2271", "\\ngeqq");
defineSymbol(math, rel, "\u2271", "\\ngeqslant");
defineSymbol(math, rel, "\u2270", "\\nleq", true);
defineSymbol(math, rel, "\u2270", "\\nleqq");
defineSymbol(math, rel, "\u2270", "\\nleqslant");
defineSymbol(math, rel, "\u2aeb", "\\Perp", true); //cmll package
defineSymbol(math, spacing, "\u00a0", "\\ ");
defineSymbol(math, spacing, "\u00a0", "\\space");
// Ref: LaTeX Source 2e: \DeclareRobustCommand{\nobreakspace}{%
defineSymbol(math, spacing, "\u00a0", "\\nobreakspace");
defineSymbol(temml_text, spacing, "\u00a0", "\\ ");
defineSymbol(temml_text, spacing, "\u00a0", " ");
defineSymbol(temml_text, spacing, "\u00a0", "\\space");
defineSymbol(temml_text, spacing, "\u00a0", "\\nobreakspace");
defineSymbol(math, spacing, null, "\\nobreak");
defineSymbol(math, spacing, null, "\\allowbreak");
defineSymbol(math, punct, ",", ",");
defineSymbol(temml_text, punct, ":", ":");
defineSymbol(math, punct, ";", ";");
defineSymbol(math, bin, "\u22bc", "\\barwedge");
defineSymbol(math, bin, "\u22bb", "\\veebar");
defineSymbol(math, bin, "\u2299", "\\odot", true);
// Firefox turns ⊕ into an emoji. So append \uFE0E. Define Unicode character in macros, not here.
defineSymbol(math, bin, "\u2295\uFE0E", "\\oplus");
defineSymbol(math, bin, "\u2297", "\\otimes", true);
defineSymbol(math, textord, "\u2202", "\\partial", true);
defineSymbol(math, bin, "\u2298", "\\oslash", true);
defineSymbol(math, bin, "\u229a", "\\circledcirc", true);
defineSymbol(math, bin, "\u22a1", "\\boxdot", true);
defineSymbol(math, bin, "\u25b3", "\\bigtriangleup");
defineSymbol(math, bin, "\u25bd", "\\bigtriangledown");
defineSymbol(math, bin, "\u2020", "\\dagger");
defineSymbol(math, bin, "\u22c4", "\\diamond");
defineSymbol(math, bin, "\u25c3", "\\triangleleft");
defineSymbol(math, bin, "\u25b9", "\\triangleright");
defineSymbol(math, temml_open, "{", "\\{");
defineSymbol(temml_text, textord, "{", "\\{");
defineSymbol(temml_text, textord, "{", "\\textbraceleft");
defineSymbol(math, temml_close, "}", "\\}");
defineSymbol(temml_text, textord, "}", "\\}");
defineSymbol(temml_text, textord, "}", "\\textbraceright");
defineSymbol(math, temml_open, "{", "\\lbrace");
defineSymbol(math, temml_close, "}", "\\rbrace");
defineSymbol(math, temml_open, "[", "\\lbrack", true);
defineSymbol(temml_text, textord, "[", "\\lbrack", true);
defineSymbol(math, temml_close, "]", "\\rbrack", true);
defineSymbol(temml_text, textord, "]", "\\rbrack", true);
defineSymbol(math, temml_open, "(", "\\lparen", true);
defineSymbol(math, temml_close, ")", "\\rparen", true);
defineSymbol(math, temml_open, "⦇", "\\llparenthesis", true);
defineSymbol(math, temml_close, "⦈", "\\rrparenthesis", true);
defineSymbol(temml_text, textord, "<", "\\textless", true); // in T1 fontenc
defineSymbol(temml_text, textord, ">", "\\textgreater", true); // in T1 fontenc
defineSymbol(math, temml_open, "\u230a", "\\lfloor", true);
defineSymbol(math, temml_close, "\u230b", "\\rfloor", true);
defineSymbol(math, temml_open, "\u2308", "\\lceil", true);
defineSymbol(math, temml_close, "\u2309", "\\rceil", true);
defineSymbol(math, textord, "\\", "\\backslash");
defineSymbol(math, textord, "|", "|");
defineSymbol(math, textord, "|", "\\vert");
defineSymbol(temml_text, textord, "|", "\\textbar", true); // in T1 fontenc
defineSymbol(math, textord, "\u2016", "\\|");
defineSymbol(math, textord, "\u2016", "\\Vert");
defineSymbol(temml_text, textord, "\u2016", "\\textbardbl");
defineSymbol(temml_text, textord, "~", "\\textasciitilde");
defineSymbol(temml_text, textord, "\\", "\\textbackslash");
defineSymbol(temml_text, textord, "^", "\\textasciicircum");
defineSymbol(math, rel, "\u2191", "\\uparrow", true);
defineSymbol(math, rel, "\u21d1", "\\Uparrow", true);
defineSymbol(math, rel, "\u2193", "\\downarrow", true);
defineSymbol(math, rel, "\u21d3", "\\Downarrow", true);
defineSymbol(math, rel, "\u2195", "\\updownarrow", true);
defineSymbol(math, rel, "\u21d5", "\\Updownarrow", true);
defineSymbol(math, op, "\u2210", "\\coprod");
defineSymbol(math, op, "\u22c1", "\\bigvee");
defineSymbol(math, op, "\u22c0", "\\bigwedge");
defineSymbol(math, op, "\u2a04", "\\biguplus");
defineSymbol(math, op, "\u2a04", "\\bigcupplus");
defineSymbol(math, op, "\u2a03", "\\bigcupdot");
defineSymbol(math, op, "\u2a07", "\\bigdoublevee");
defineSymbol(math, op, "\u2a08", "\\bigdoublewedge");
defineSymbol(math, op, "\u22c2", "\\bigcap");
defineSymbol(math, op, "\u22c3", "\\bigcup");
defineSymbol(math, op, "\u222b", "\\int");
defineSymbol(math, op, "\u222b", "\\intop");
defineSymbol(math, op, "\u222c", "\\iint");
defineSymbol(math, op, "\u222d", "\\iiint");
defineSymbol(math, op, "\u220f", "\\prod");
defineSymbol(math, op, "\u2211", "\\sum");
defineSymbol(math, op, "\u2a02", "\\bigotimes");
defineSymbol(math, op, "\u2a01", "\\bigoplus");
defineSymbol(math, op, "\u2a00", "\\bigodot");
defineSymbol(math, op, "\u2a09", "\\bigtimes");
defineSymbol(math, op, "\u222e", "\\oint");
defineSymbol(math, op, "\u222f", "\\oiint");
defineSymbol(math, op, "\u2230", "\\oiiint");
defineSymbol(math, op, "\u2231", "\\intclockwise");
defineSymbol(math, op, "\u2232", "\\varointclockwise");
defineSymbol(math, op, "\u2a0c", "\\iiiint");
defineSymbol(math, op, "\u2a0d", "\\intbar");
defineSymbol(math, op, "\u2a0e", "\\intBar");
defineSymbol(math, op, "\u2a0f", "\\fint");
defineSymbol(math, op, "\u2a12", "\\rppolint");
defineSymbol(math, op, "\u2a13", "\\scpolint");
defineSymbol(math, op, "\u2a15", "\\pointint");
defineSymbol(math, op, "\u2a16", "\\sqint");
defineSymbol(math, op, "\u2a17", "\\intlarhk");
defineSymbol(math, op, "\u2a18", "\\intx");
defineSymbol(math, op, "\u2a19", "\\intcap");
defineSymbol(math, op, "\u2a1a", "\\intcup");
defineSymbol(math, op, "\u2a05", "\\bigsqcap");
defineSymbol(math, op, "\u2a06", "\\bigsqcup");
defineSymbol(math, op, "\u222b", "\\smallint");
defineSymbol(temml_text, inner, "\u2026", "\\textellipsis");
defineSymbol(math, inner, "\u2026", "\\mathellipsis");
defineSymbol(temml_text, inner, "\u2026", "\\ldots", true);
defineSymbol(math, inner, "\u2026", "\\ldots", true);
defineSymbol(math, inner, "\u22f0", "\\iddots", true);
defineSymbol(math, inner, "\u22ef", "\\@cdots", true);
defineSymbol(math, inner, "\u22f1", "\\ddots", true);
defineSymbol(math, textord, "\u22ee", "\\varvdots"); // \vdots is a macro
defineSymbol(temml_text, textord, "\u22ee", "\\varvdots");
defineSymbol(math, accent, "\u02ca", "\\acute");
defineSymbol(math, accent, "\u0060", "\\grave");
defineSymbol(math, accent, "\u00a8", "\\ddot");
defineSymbol(math, accent, "\u2026", "\\dddot");
defineSymbol(math, accent, "\u2026\u002e", "\\ddddot");
defineSymbol(math, accent, "\u007e", "\\tilde");
defineSymbol(math, accent, "\u203e", "\\bar");
defineSymbol(math, accent, "\u02d8", "\\breve");
defineSymbol(math, accent, "\u02c7", "\\check");
defineSymbol(math, accent, "\u005e", "\\hat");
defineSymbol(math, accent, "\u2192", "\\vec");
defineSymbol(math, accent, "\u02d9", "\\dot");
defineSymbol(math, accent, "\u02da", "\\mathring");
defineSymbol(math, mathord, "\u0131", "\\imath", true);
defineSymbol(math, mathord, "\u0237", "\\jmath", true);
defineSymbol(math, textord, "\u0131", "\u0131");
defineSymbol(math, textord, "\u0237", "\u0237");
defineSymbol(temml_text, textord, "\u0131", "\\i", true);
defineSymbol(temml_text, textord, "\u0237", "\\j", true);
defineSymbol(temml_text, textord, "\u00df", "\\ss", true);
defineSymbol(temml_text, textord, "\u00e6", "\\ae", true);
defineSymbol(temml_text, textord, "\u0153", "\\oe", true);
defineSymbol(temml_text, textord, "\u00f8", "\\o", true);
defineSymbol(math, mathord, "\u00f8", "\\o", true);
defineSymbol(temml_text, textord, "\u00c6", "\\AE", true);
defineSymbol(temml_text, textord, "\u0152", "\\OE", true);
defineSymbol(temml_text, textord, "\u00d8", "\\O", true);
defineSymbol(math, mathord, "\u00d8", "\\O", true);
defineSymbol(temml_text, accent, "\u02ca", "\\'"); // acute
defineSymbol(temml_text, accent, "\u02cb", "\\`"); // grave
defineSymbol(temml_text, accent, "\u02c6", "\\^"); // circumflex
defineSymbol(temml_text, accent, "\u02dc", "\\~"); // tilde
defineSymbol(temml_text, accent, "\u02c9", "\\="); // macron
defineSymbol(temml_text, accent, "\u02d8", "\\u"); // breve
defineSymbol(temml_text, accent, "\u02d9", "\\."); // dot above
defineSymbol(temml_text, accent, "\u00b8", "\\c"); // cedilla
defineSymbol(temml_text, accent, "\u02da", "\\r"); // ring above
defineSymbol(temml_text, accent, "\u02c7", "\\v"); // caron
defineSymbol(temml_text, accent, "\u00a8", '\\"'); // diaresis
defineSymbol(temml_text, accent, "\u02dd", "\\H"); // double acute
defineSymbol(math, accent, "\u02ca", "\\'"); // acute
defineSymbol(math, accent, "\u02cb", "\\`"); // grave
defineSymbol(math, accent, "\u02c6", "\\^"); // circumflex
defineSymbol(math, accent, "\u02dc", "\\~"); // tilde
defineSymbol(math, accent, "\u02c9", "\\="); // macron
defineSymbol(math, accent, "\u02d8", "\\u"); // breve
defineSymbol(math, accent, "\u02d9", "\\."); // dot above
defineSymbol(math, accent, "\u00b8", "\\c"); // cedilla
defineSymbol(math, accent, "\u02da", "\\r"); // ring above
defineSymbol(math, accent, "\u02c7", "\\v"); // caron
defineSymbol(math, accent, "\u00a8", '\\"'); // diaresis
defineSymbol(math, accent, "\u02dd", "\\H"); // double acute

// These ligatures are detected and created in Parser.js's `formLigatures`.
const ligatures = {
  "--": true,
  "---": true,
  "``": true,
  "''": true
};

defineSymbol(temml_text, textord, "\u2013", "--", true);
defineSymbol(temml_text, textord, "\u2013", "\\textendash");
defineSymbol(temml_text, textord, "\u2014", "---", true);
defineSymbol(temml_text, textord, "\u2014", "\\textemdash");
defineSymbol(temml_text, textord, "\u2018", "`", true);
defineSymbol(temml_text, textord, "\u2018", "\\textquoteleft");
defineSymbol(temml_text, textord, "\u2019", "'", true);
defineSymbol(temml_text, textord, "\u2019", "\\textquoteright");
defineSymbol(temml_text, textord, "\u201c", "``", true);
defineSymbol(temml_text, textord, "\u201c", "\\textquotedblleft");
defineSymbol(temml_text, textord, "\u201d", "''", true);
defineSymbol(temml_text, textord, "\u201d", "\\textquotedblright");
//  \degree from gensymb package
defineSymbol(math, textord, "\u00b0", "\\degree", true);
defineSymbol(temml_text, textord, "\u00b0", "\\degree");
// \textdegree from inputenc package
defineSymbol(temml_text, textord, "\u00b0", "\\textdegree", true);
// TODO: In LaTeX, \pounds can generate a different character in text and math
// mode, but among our fonts, only Main-Regular defines this character "163".
defineSymbol(math, textord, "\u00a3", "\\pounds");
defineSymbol(math, textord, "\u00a3", "\\mathsterling", true);
defineSymbol(temml_text, textord, "\u00a3", "\\pounds");
defineSymbol(temml_text, textord, "\u00a3", "\\textsterling", true);
defineSymbol(math, textord, "\u2720", "\\maltese");
defineSymbol(temml_text, textord, "\u2720", "\\maltese");
defineSymbol(math, textord, "\u20ac", "\\euro", true);
defineSymbol(temml_text, textord, "\u20ac", "\\euro", true);
defineSymbol(temml_text, textord, "\u20ac", "\\texteuro");
defineSymbol(math, textord, "\u00a9", "\\copyright", true);
defineSymbol(temml_text, textord, "\u00a9", "\\textcopyright");
defineSymbol(math, textord, "\u2300", "\\diameter", true);
defineSymbol(temml_text, textord, "\u2300", "\\diameter");

// Italic Greek
defineSymbol(math, textord, "𝛤", "\\varGamma");
defineSymbol(math, textord, "𝛥", "\\varDelta");
defineSymbol(math, textord, "𝛩", "\\varTheta");
defineSymbol(math, textord, "𝛬", "\\varLambda");
defineSymbol(math, textord, "𝛯", "\\varXi");
defineSymbol(math, textord, "𝛱", "\\varPi");
defineSymbol(math, textord, "𝛴", "\\varSigma");
defineSymbol(math, textord, "𝛶", "\\varUpsilon");
defineSymbol(math, textord, "𝛷", "\\varPhi");
defineSymbol(math, textord, "𝛹", "\\varPsi");
defineSymbol(math, textord, "𝛺", "\\varOmega");
defineSymbol(temml_text, textord, "𝛤", "\\varGamma");
defineSymbol(temml_text, textord, "𝛥", "\\varDelta");
defineSymbol(temml_text, textord, "𝛩", "\\varTheta");
defineSymbol(temml_text, textord, "𝛬", "\\varLambda");
defineSymbol(temml_text, textord, "𝛯", "\\varXi");
defineSymbol(temml_text, textord, "𝛱", "\\varPi");
defineSymbol(temml_text, textord, "𝛴", "\\varSigma");
defineSymbol(temml_text, textord, "𝛶", "\\varUpsilon");
defineSymbol(temml_text, textord, "𝛷", "\\varPhi");
defineSymbol(temml_text, textord, "𝛹", "\\varPsi");
defineSymbol(temml_text, textord, "𝛺", "\\varOmega");


// There are lots of symbols which are the same, so we add them in afterwards.
// All of these are textords in math mode
const mathTextSymbols = '0123456789/@."';
for (let i = 0; i < mathTextSymbols.length; i++) {
  const ch = mathTextSymbols.charAt(i);
  defineSymbol(math, textord, ch, ch);
}

// All of these are textords in text mode
const textSymbols = '0123456789!@*()-=+";:?/.,';
for (let i = 0; i < textSymbols.length; i++) {
  const ch = textSymbols.charAt(i);
  defineSymbol(temml_text, textord, ch, ch);
}

// All of these are textords in text mode, and mathords in math mode
const letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
for (let i = 0; i < letters.length; i++) {
  const ch = letters.charAt(i);
  defineSymbol(math, mathord, ch, ch);
  defineSymbol(temml_text, textord, ch, ch);
}

// Some more letters in Unicode Basic Multilingual Plane.
const narrow = "ÇÐÞçþℂℍℕℙℚℝℤℎℏℊℋℌℐℑℒℓ℘ℛℜℬℰℱℳℭℨ";
for (let i = 0; i < narrow.length; i++) {
  const ch = narrow.charAt(i);
  defineSymbol(math, mathord, ch, ch);
  defineSymbol(temml_text, textord, ch, ch);
}

// The next loop loads wide (surrogate pair) characters.
// We support some letters in the Unicode range U+1D400 to U+1D7FF,
// Mathematical Alphanumeric Symbols.
let wideChar = "";
for (let i = 0; i < letters.length; i++) {
  // The hex numbers in the next line are a surrogate pair.
  // 0xD835 is the high surrogate for all letters in the range we support.
  // 0xDC00 is the low surrogate for bold A.
  wideChar = String.fromCharCode(0xd835, 0xdc00 + i); // A-Z a-z bold
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(temml_text, textord, wideChar, wideChar);

  wideChar = String.fromCharCode(0xd835, 0xdc34 + i); // A-Z a-z italic
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(temml_text, textord, wideChar, wideChar);

  wideChar = String.fromCharCode(0xd835, 0xdc68 + i); // A-Z a-z bold italic
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(temml_text, textord, wideChar, wideChar);

  wideChar = String.fromCharCode(0xd835, 0xdd04 + i); // A-Z a-z Fractur
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(temml_text, textord, wideChar, wideChar);

  wideChar = String.fromCharCode(0xd835, 0xdda0 + i); // A-Z a-z sans-serif
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(temml_text, textord, wideChar, wideChar);

  wideChar = String.fromCharCode(0xd835, 0xddd4 + i); // A-Z a-z sans bold
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(temml_text, textord, wideChar, wideChar);

  wideChar = String.fromCharCode(0xd835, 0xde08 + i); // A-Z a-z sans italic
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(temml_text, textord, wideChar, wideChar);

  wideChar = String.fromCharCode(0xd835, 0xde70 + i); // A-Z a-z monospace
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(temml_text, textord, wideChar, wideChar);

  wideChar = String.fromCharCode(0xd835, 0xdd38 + i); // A-Z a-z double struck
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(temml_text, textord, wideChar, wideChar);

  const ch = letters.charAt(i);
  wideChar = String.fromCharCode(0xd835, 0xdc9c + i); // A-Z a-z calligraphic
  defineSymbol(math, mathord, ch, wideChar);
  defineSymbol(temml_text, textord, ch, wideChar);
}

// Next, some wide character numerals
for (let i = 0; i < 10; i++) {
  wideChar = String.fromCharCode(0xd835, 0xdfce + i); // 0-9 bold
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(temml_text, textord, wideChar, wideChar);

  wideChar = String.fromCharCode(0xd835, 0xdfe2 + i); // 0-9 sans serif
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(temml_text, textord, wideChar, wideChar);

  wideChar = String.fromCharCode(0xd835, 0xdfec + i); // 0-9 bold sans
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(temml_text, textord, wideChar, wideChar);

  wideChar = String.fromCharCode(0xd835, 0xdff6 + i); // 0-9 monospace
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(temml_text, textord, wideChar, wideChar);
}

/*
 * Neither Firefox nor Chrome support hard line breaks or soft line breaks.
 * (Despite https://www.w3.org/Math/draft-spec/mathml.html#chapter3_presm.lbattrs)
 * So Temml has work-arounds for both hard and soft breaks.
 * The work-arounds sadly do not work simultaneously. Any top-level hard
 * break makes soft line breaks impossible.
 *
 * Hard breaks are simulated by creating a <mtable> and putting each line in its own <mtr>.
 *
 * To create soft line breaks, Temml avoids using the <semantics> and <annotation> tags.
 * Then the top level of a <math> element can be occupied by <mrow> elements, and the browser
 * will break after a <mrow> if the expression extends beyond the container limit.
 *
 * The default is for soft line breaks after each top-level binary or
 * relational operator, per TeXbook p. 173. So we gather the expression into <mrow>s so that
 * each <mrow> ends in a binary or relational operator.
 *
 * An option is for soft line breaks before an "=" sign. That changes the <mrow>s.
 *
 * Soft line breaks will not work in Chromium and Safari, only Firefox.
 *
 * Hopefully browsers will someday do their own linebreaking and we will be able to delete
 * much of this module.
 */

const openDelims = "([{⌊⌈⟨⟮⎰⟦⦃";
const closeDelims = ")]}⌋⌉⟩⟯⎱⟦⦄";

function setLineBreaks(expression, wrapMode, isDisplayMode) {
  const mtrs = [];
  let mrows = [];
  let block = [];
  let numTopLevelEquals = 0;
  let i = 0;
  let level = 0;
  while (i < expression.length) {
    while (expression[i] instanceof DocumentFragment) {
      expression.splice(i, 1, ...expression[i].children); // Expand the fragment.
    }
    const node = expression[i];
    if (node.attributes && node.attributes.linebreak &&
      node.attributes.linebreak === "newline") {
      // A hard line break. Create a <mtr> for the current block.
      if (block.length > 0) {
        mrows.push(new mathMLTree.MathNode("mrow", block));
      }
      mrows.push(node);
      block = [];
      const mtd = new mathMLTree.MathNode("mtd", mrows);
      mtd.style.textAlign = "left";
      mtrs.push(new mathMLTree.MathNode("mtr", [mtd]));
      mrows = [];
      i += 1;
      continue
    }
    block.push(node);
    if (node.type && node.type === "mo" && node.children.length === 1 &&
        !Object.hasOwn(node.attributes, "movablelimits")) {
      const ch = node.children[0].text;
      if (openDelims.indexOf(ch) > -1) {
        level += 1;
      } else if (closeDelims.indexOf(ch) > -1) {
        level -= 1;
      } else if (level === 0 && wrapMode === "=" && ch === "=") {
        numTopLevelEquals += 1;
        if (numTopLevelEquals > 1) {
          block.pop();
          // Start a new block. (Insert a soft linebreak.)
          const element = new mathMLTree.MathNode("mrow", block);
          mrows.push(element);
          block = [node];
        }
      } else if (level === 0 && wrapMode === "tex" && ch !== "∇") {
        // Check if the following node is a \nobreak text node, e.g. "~""
        const next = i < expression.length - 1 ? expression[i + 1] : null;
        let glueIsFreeOfNobreak = true;
        if (
          !(
            next &&
            next.type === "mtext" &&
            next.attributes.linebreak &&
            next.attributes.linebreak === "nobreak"
          )
        ) {
          // We may need to start a new block.
          // First, put any post-operator glue on same line as operator.
          for (let j = i + 1; j < expression.length; j++) {
            const nd = expression[j];
            if (
              nd.type &&
              nd.type === "mspace" &&
              !(nd.attributes.linebreak && nd.attributes.linebreak === "newline")
            ) {
              block.push(nd);
              i += 1;
              if (
                nd.attributes &&
                nd.attributes.linebreak &&
                nd.attributes.linebreak === "nobreak"
              ) {
                glueIsFreeOfNobreak = false;
              }
            } else {
              break;
            }
          }
        }
        if (glueIsFreeOfNobreak) {
          // Start a new block. (Insert a soft linebreak.)
          const element = new mathMLTree.MathNode("mrow", block);
          mrows.push(element);
          block = [];
        }
      }
    }
    i += 1;
  }
  if (block.length > 0) {
    const element = new mathMLTree.MathNode("mrow", block);
    mrows.push(element);
  }
  if (mtrs.length > 0) {
    const mtd = new mathMLTree.MathNode("mtd", mrows);
    mtd.style.textAlign = "left";
    const mtr = new mathMLTree.MathNode("mtr", [mtd]);
    mtrs.push(mtr);
    const mtable = new mathMLTree.MathNode("mtable", mtrs);
    if (!isDisplayMode) {
      mtable.setAttribute("columnalign", "left");
      mtable.setAttribute("rowspacing", "0em");
    }
    return mtable
  }
  return mathMLTree.newDocumentFragment(mrows);
}

/**
 * This file converts a parse tree into a corresponding MathML tree. The main
 * entry point is the `buildMathML` function, which takes a parse tree from the
 * parser.
 */


/**
 * Takes a symbol and converts it into a MathML text node after performing
 * optional replacement from symbols.js.
 */
const makeText = function(text, mode, style) {
  if (
    symbols[mode][text] &&
    symbols[mode][text].replace &&
    text.charCodeAt(0) !== 0xd835 &&
    !(
      Object.prototype.hasOwnProperty.call(ligatures, text) &&
      style &&
      ((style.fontFamily && style.fontFamily.slice(4, 6) === "tt") ||
        (style.font && style.font.slice(4, 6) === "tt"))
    )
  ) {
    text = symbols[mode][text].replace;
  }

  return new mathMLTree.TextNode(text);
};

const copyChar = (newRow, child) => {
  if (newRow.children.length === 0 ||
      newRow.children[newRow.children.length - 1].type !== "mtext") {
    const mtext = new mathMLTree.MathNode(
      "mtext",
      [new mathMLTree.TextNode(child.children[0].text)]
    );
    newRow.children.push(mtext);
  } else {
    newRow.children[newRow.children.length - 1].children[0].text += child.children[0].text;
  }
};

const consolidateText = mrow => {
  // If possible, consolidate adjacent <mtext> elements into a single element.
  if (mrow.type !== "mrow" && mrow.type !== "mstyle") { return mrow }
  if (mrow.children.length === 0) { return mrow } // empty group, e.g., \text{}
  const newRow = new mathMLTree.MathNode("mrow");
  for (let i = 0; i < mrow.children.length; i++) {
    const child = mrow.children[i];
    if (child.type === "mtext" && Object.keys(child.attributes).length === 0) {
      copyChar(newRow, child);
    } else if (child.type === "mrow") {
      // We'll also check the children of an mrow. One level only. No recursion.
      let canConsolidate = true;
      for (let j = 0; j < child.children.length; j++) {
        const grandChild = child.children[j];
        if (grandChild.type !== "mtext" || Object.keys(child.attributes).length !== 0) {
          canConsolidate = false;
          break
        }
      }
      if (canConsolidate) {
        for (let j = 0; j < child.children.length; j++) {
          const grandChild = child.children[j];
          copyChar(newRow, grandChild);
        }
      } else {
        newRow.children.push(child);
      }
    } else {
      newRow.children.push(child);
    }
  }
  for (let i = 0; i < newRow.children.length; i++) {
    if (newRow.children[i].type === "mtext") {
      const mtext = newRow.children[i];
      // Firefox does not render a space at either end of an <mtext> string.
      // To get proper rendering, we replace leading or trailing spaces with no-break spaces.
      if (mtext.children[0].text.charAt(0) === " ") {
        mtext.children[0].text = "\u00a0" + mtext.children[0].text.slice(1);
      }
      const L = mtext.children[0].text.length;
      if (L > 0 && mtext.children[0].text.charAt(L - 1) === " ") {
        mtext.children[0].text = mtext.children[0].text.slice(0, -1) + "\u00a0";
      }
      for (const [key, value] of Object.entries(mrow.attributes)) {
        mtext.attributes[key] = value;
      }
    }
  }
  if (newRow.children.length === 1 && newRow.children[0].type === "mtext") {
    return newRow.children[0]; // A consolidated <mtext>
  } else {
    return newRow
  }
};

/**
 * Wrap the given array of nodes in an <mrow> node if needed, i.e.,
 * unless the array has length 1.  Always returns a single node.
 */
const makeRow = function(body, semisimple = false) {
  if (body.length === 1 && !(body[0] instanceof DocumentFragment)) {
    return body[0];
  } else if (!semisimple) {
    // Suppress spacing on <mo> nodes at both ends of the row.
    if (body[0] instanceof MathNode && body[0].type === "mo" && !body[0].attributes.fence) {
      body[0].attributes.lspace = "0em";
      body[0].attributes.rspace = "0em";
    }
    const end = body.length - 1;
    if (body[end] instanceof MathNode && body[end].type === "mo" && !body[end].attributes.fence) {
      body[end].attributes.lspace = "0em";
      body[end].attributes.rspace = "0em";
    }
  }
  return new mathMLTree.MathNode("mrow", body);
};

/**
 * Check for <mi>.</mi> which is how a dot renders in MathML,
 * or <mo separator="true" lspace="0em" rspace="0em">,</mo>
 * which is how a braced comma {,} renders in MathML
 */
function isNumberPunctuation(group) {
  if (!group) {
    return false
  }
  if (group.type === 'mi' && group.children.length === 1) {
    const child = group.children[0];
    return child instanceof TextNode && child.text === '.'
  } else if (group.type === "mtext" && group.children.length === 1) {
    const child = group.children[0];
    return child instanceof TextNode && child.text === '\u2008' // punctuation space
  } else if (group.type === 'mo' && group.children.length === 1 &&
    group.getAttribute('separator') === 'true' &&
    group.getAttribute('lspace') === '0em' &&
    group.getAttribute('rspace') === '0em') {
    const child = group.children[0];
    return child instanceof TextNode && child.text === ','
  } else {
    return false
  }
}
const isComma = (expression, i) => {
  const node = expression[i];
  const followingNode = expression[i + 1];
  return (node.type === "atom" && node.text === ",") &&
    // Don't consolidate if there is a space after the comma.
    node.loc && followingNode.loc && node.loc.end === followingNode.loc.start
};

const isRel = item => {
  return (item.type === "atom" && item.family === "rel") ||
      (item.type === "mclass" && item.mclass === "mrel")
};

/**
 * Takes a list of nodes, builds them, and returns a list of the generated
 * MathML nodes.  Also do a couple chores along the way:
 * (1) Suppress spacing when an author wraps an operator w/braces, as in {=}.
 * (2) Suppress spacing between two adjacent relations.
 */
const buildExpression = function(expression, style, semisimple = false) {
  if (!semisimple && expression.length === 1) {
    const group = buildGroup$1(expression[0], style);
    if (group instanceof MathNode && group.type === "mo") {
      // When TeX writers want to suppress spacing on an operator,
      // they often put the operator by itself inside braces.
      group.setAttribute("lspace", "0em");
      group.setAttribute("rspace", "0em");
    }
    return [group];
  }

  const groups = [];
  const groupArray = [];
  let lastGroup;
  for (let i = 0; i < expression.length; i++) {
    groupArray.push(buildGroup$1(expression[i], style));
  }

  for (let i = 0; i < groupArray.length; i++) {
    const group = groupArray[i];

    // Suppress spacing between adjacent relations
    if (i < expression.length - 1 && isRel(expression[i]) && isRel(expression[i + 1])) {
      group.setAttribute("rspace", "0em");
    }
    if (i > 0 && isRel(expression[i]) && isRel(expression[i - 1])) {
      group.setAttribute("lspace", "0em");
    }

    // Concatenate numbers
    if (group.type === 'mn' && lastGroup && lastGroup.type === 'mn') {
      // Concatenate <mn>...</mn> followed by <mi>.</mi>
      lastGroup.children.push(...group.children);
      continue
    } else if (isNumberPunctuation(group) && lastGroup && lastGroup.type === 'mn') {
      // Concatenate <mn>...</mn> followed by <mi>.</mi>
      lastGroup.children.push(...group.children);
      continue
    } else if (lastGroup && lastGroup.type === "mn" && i < groupArray.length - 1 &&
      groupArray[i + 1].type === "mn" && isComma(expression, i)) {
      lastGroup.children.push(...group.children);
      continue
    } else if (group.type === 'mn' && isNumberPunctuation(lastGroup)) {
      // Concatenate <mi>.</mi> followed by <mn>...</mn>
      group.children = [...lastGroup.children, ...group.children];
      groups.pop();
    } else if ((group.type === 'msup' || group.type === 'msub') &&
        group.children.length >= 1 && lastGroup &&
        (lastGroup.type === 'mn' || isNumberPunctuation(lastGroup))) {
      // Put preceding <mn>...</mn> or <mi>.</mi> inside base of
      // <msup><mn>...base...</mn>...exponent...</msup> (or <msub>)
      const base = group.children[0];
      if (base instanceof MathNode && base.type === 'mn' && lastGroup) {
        base.children = [...lastGroup.children, ...base.children];
        groups.pop();
      }
    }
    groups.push(group);
    lastGroup = group;
  }
  return groups
};

/**
 * Equivalent to buildExpression, but wraps the elements in an <mrow>
 * if there's more than one.  Returns a single node instead of an array.
 */
const buildExpressionRow = function(expression, style, semisimple = false) {
  return makeRow(buildExpression(expression, style, semisimple), semisimple);
};

/**
 * Takes a group from the parser and calls the appropriate groupBuilders function
 * on it to produce a MathML node.
 */
const buildGroup$1 = function(group, style) {
  if (!group) {
    return new mathMLTree.MathNode("mrow");
  }

  if (_mathmlGroupBuilders[group.type]) {
    // Call the groupBuilders function
    const result = _mathmlGroupBuilders[group.type](group, style);
    return result;
  } else {
    throw new ParseError("Got group of unknown type: '" + group.type + "'");
  }
};

const glue$1 = _ => {
  return new mathMLTree.MathNode("mtd", [], [], { padding: "0", width: "50%" })
};

const labelContainers = ["mrow", "mtd", "mtable", "mtr"];
const getLabel = parent => {
  for (const node of parent.children) {
    if (node.type && labelContainers.includes(node.type)) {
      if (node.classes && node.classes[0] === "tml-label") {
        const label = node.label;
        return label
      } else {
        const label = getLabel(node);
        if (label) { return label }
      }
    } else if (!node.type) {
      const label = getLabel(node);
      if (label) { return label }
    }
  }
};

const taggedExpression = (expression, tag, style, leqno) => {
  tag = buildExpressionRow(tag[0].body, style);
  tag = consolidateText(tag);
  tag.classes.push("tml-tag");

  const label = getLabel(expression); // from a \label{} function.
  expression = new mathMLTree.MathNode("mtd", [expression]);
  const rowArray = [glue$1(), expression, glue$1()];
  rowArray[leqno ? 0 : 2].classes.push(leqno ? "tml-left" : "tml-right");
  rowArray[leqno ? 0 : 2].children.push(tag);
  const mtr = new mathMLTree.MathNode("mtr", rowArray, ["tml-tageqn"]);
  if (label) { mtr.setAttribute("id", label); }
  const table = new mathMLTree.MathNode("mtable", [mtr]);
  table.style.width = "100%";
  table.setAttribute("displaystyle", "true");
  return table
};

/**
 * Takes a full parse tree and settings and builds a MathML representation of
 * it.
 */
function buildMathML(tree, texExpression, style, settings) {
  // Strip off outer tag wrapper for processing below.
  let tag = null;
  if (tree.length === 1 && tree[0].type === "tag") {
    tag = tree[0].tag;
    tree = tree[0].body;
  }

  const expression = buildExpression(tree, style);

  if (expression.length === 1 && expression[0] instanceof AnchorNode) {
    return expression[0]
  }

  const wrap = (settings.displayMode || settings.annotate) ? "none" : settings.wrap;

  const n1 = expression.length === 0 ? null : expression[0];
  let wrapper = expression.length === 1 && tag === null && (n1 instanceof MathNode)
      ? expression[0]
      : setLineBreaks(expression, wrap, settings.displayMode);

  if (tag) {
    wrapper = taggedExpression(wrapper, tag, style, settings.leqno);
  }

  if (settings.annotate) {
    // Build a TeX annotation of the source
    const annotation = new mathMLTree.MathNode(
      "annotation", [new mathMLTree.TextNode(texExpression)]);
    annotation.setAttribute("encoding", "application/x-tex");
    wrapper = new mathMLTree.MathNode("semantics", [wrapper, annotation]);
  }

  const math = new mathMLTree.MathNode("math", [wrapper]);

  if (settings.xml) {
    math.setAttribute("xmlns", "http://www.w3.org/1998/Math/MathML");
  }
  if (wrapper.style.width) {
    math.style.width = "100%";
  }
  if (settings.displayMode) {
    math.setAttribute("display", "block");
    math.style.display = "block math"; // necessary in Chromium.
    // Firefox and Safari do not recognize display: "block math".
    // Set a class so that the CSS file can set display: block.
    math.classes = ["tml-display"];
  }
  return math;
}

const smalls = "acegıȷmnopqrsuvwxyzαγεηικμνοπρςστυχωϕ𝐚𝐜𝐞𝐠𝐦𝐧𝐨𝐩𝐪𝐫𝐬𝐮𝐯𝐰𝐱𝐲𝐳";
const talls = "ABCDEFGHIJKLMNOPQRSTUVWXYZbdfhkltΑΒΓΔΕΖΗΘΙΚΛΜΝΞΟΠΡΣΤΥΦΧΨΩβδλζφθψ"
             + "𝐀𝐁𝐂𝐃𝐄𝐅𝐆𝐇𝐈𝐉𝐊𝐋𝐌𝐍𝐎𝐏𝐐𝐑𝐒𝐓𝐔𝐕𝐖𝐗𝐘𝐙𝐛𝐝𝐟𝐡𝐤𝐥𝐭";
const longSmalls = new Set(["\\alpha", "\\gamma", "\\delta", "\\epsilon", "\\eta", "\\iota",
  "\\kappa", "\\mu", "\\nu", "\\pi", "\\rho", "\\sigma", "\\tau", "\\upsilon", "\\chi", "\\psi",
  "\\omega", "\\imath", "\\jmath"]);
const longTalls = new Set(["\\Gamma", "\\Delta", "\\Sigma", "\\Omega", "\\beta", "\\delta",
  "\\lambda", "\\theta", "\\psi"]);

const mathmlBuilder$a = (group, style) => {
  const accentNode = group.isStretchy
    ? stretchy.accentNode(group)
    : new mathMLTree.MathNode("mo", [makeText(group.label, group.mode)]);

  if (group.label === "\\vec") {
    accentNode.style.transform = "scale(0.75) translate(10%, 30%)";
  } else {
    accentNode.style.mathStyle = "normal";
    accentNode.style.mathDepth = "0";
    if (needWebkitShift.has(group.label) &&  utils.isCharacterBox(group.base)) {
      let shift = "";
      const ch = group.base.text;
      if (smalls.indexOf(ch) > -1 || longSmalls.has(ch)) { shift = "tml-xshift"; }
      if (talls.indexOf(ch) > -1  || longTalls.has(ch))  { shift = "tml-capshift"; }
      if (shift) { accentNode.classes.push(shift); }
    }
  }
  if (!group.isStretchy) {
    accentNode.setAttribute("stretchy", "false");
  }

  const node = new mathMLTree.MathNode((group.label === "\\c" ? "munder" : "mover"),
    [buildGroup$1(group.base, style), accentNode]
  );

  return node;
};

const nonStretchyAccents = new Set([
  "\\acute",
  "\\grave",
  "\\ddot",
  "\\dddot",
  "\\ddddot",
  "\\tilde",
  "\\bar",
  "\\breve",
  "\\check",
  "\\hat",
  "\\vec",
  "\\dot",
  "\\mathring"
]);

const needWebkitShift = new Set([
  "\\acute",
  "\\bar",
  "\\breve",
  "\\check",
  "\\dot",
  "\\ddot",
  "\\grave",
  "\\hat",
  "\\mathring",
  "\\'", "\\^", "\\~", "\\=", "\\u", "\\.", '\\"', "\\r", "\\H", "\\v"
]);

const combiningChar = {
  "\\`": "\u0300",
  "\\'": "\u0301",
  "\\^": "\u0302",
  "\\~": "\u0303",
  "\\=": "\u0304",
  "\\u": "\u0306",
  "\\.": "\u0307",
  '\\"': "\u0308",
  "\\r": "\u030A",
  "\\H": "\u030B",
  "\\v": "\u030C"
};

// Accents
defineFunction({
  type: "accent",
  names: [
    "\\acute",
    "\\grave",
    "\\ddot",
    "\\dddot",
    "\\ddddot",
    "\\tilde",
    "\\bar",
    "\\breve",
    "\\check",
    "\\hat",
    "\\vec",
    "\\dot",
    "\\mathring",
    "\\overparen",
    "\\widecheck",
    "\\widehat",
    "\\wideparen",
    "\\widetilde",
    "\\overrightarrow",
    "\\overleftarrow",
    "\\Overrightarrow",
    "\\overleftrightarrow",
    "\\overgroup",
    "\\overleftharpoon",
    "\\overrightharpoon"
  ],
  props: {
    numArgs: 1
  },
  handler: (context, args) => {
    const base = normalizeArgument(args[0]);

    const isStretchy = !nonStretchyAccents.has(context.funcName);

    return {
      type: "accent",
      mode: context.parser.mode,
      label: context.funcName,
      isStretchy: isStretchy,
      base: base
    };
  },
  mathmlBuilder: mathmlBuilder$a
});

// Text-mode accents
defineFunction({
  type: "accent",
  names: ["\\'", "\\`", "\\^", "\\~", "\\=", "\\c", "\\u", "\\.", '\\"', "\\r", "\\H", "\\v"],
  props: {
    numArgs: 1,
    allowedInText: true,
    allowedInMath: true,
    argTypes: ["primitive"]
  },
  handler: (context, args) => {
    const base = normalizeArgument(args[0]);
    const mode = context.parser.mode;

    if (mode === "math" && context.parser.settings.strict) {
      // LaTeX only writes a warning. It doesn't stop. We'll issue the same warning.
      // eslint-disable-next-line no-console
      console.log(`Temml parse error: Command ${context.funcName} is invalid in math mode.`);
    }

    if (mode === "text" && base.text && base.text.length === 1
        && context.funcName in combiningChar  && smalls.indexOf(base.text) > -1) {
      // Return a combining accent character
      return {
        type: "textord",
        mode: "text",
        text: base.text + combiningChar[context.funcName]
      }
    } else {
      // Build up the accent
      return {
        type: "accent",
        mode: mode,
        label: context.funcName,
        isStretchy: false,
        base: base
      }
    }
  },
  mathmlBuilder: mathmlBuilder$a
});

defineFunction({
  type: "accentUnder",
  names: [
    "\\underleftarrow",
    "\\underrightarrow",
    "\\underleftrightarrow",
    "\\undergroup",
    "\\underparen",
    "\\utilde"
  ],
  props: {
    numArgs: 1
  },
  handler: ({ parser, funcName }, args) => {
    const base = args[0];
    return {
      type: "accentUnder",
      mode: parser.mode,
      label: funcName,
      base: base
    };
  },
  mathmlBuilder: (group, style) => {
    const accentNode = stretchy.accentNode(group);
    accentNode.style["math-depth"] = 0;
    const node = new mathMLTree.MathNode("munder", [
      buildGroup$1(group.base, style),
      accentNode
    ]);
    return node;
  }
});

/**
 * This file does conversion between units.  In particular, it provides
 * calculateSize to convert other units into CSS units.
 */


const ptPerUnit = {
  // Convert to CSS (Postscipt) points, not TeX points
  // https://en.wikibooks.org/wiki/LaTeX/Lengths and
  // https://tex.stackexchange.com/a/8263
  pt: 800 / 803, // convert TeX point to CSS (Postscript) point
  pc: (12 * 800) / 803, // pica
  dd: ((1238 / 1157) * 800) / 803, // didot
  cc: ((14856 / 1157) * 800) / 803, // cicero (12 didot)
  nd: ((685 / 642) * 800) / 803, // new didot
  nc: ((1370 / 107) * 800) / 803, // new cicero (12 new didot)
  sp: ((1 / 65536) * 800) / 803, // scaled point (TeX's internal smallest unit)
  mm: (25.4 / 72),
  cm: (2.54 / 72),
  in: (1 / 72),
  px: (96 / 72)
};

/**
 * Determine whether the specified unit (either a string defining the unit
 * or a "size" parse node containing a unit field) is valid.
 */
const validUnits = [
  "em",
  "ex",
  "mu",
  "pt",
  "mm",
  "cm",
  "in",
  "px",
  "bp",
  "pc",
  "dd",
  "cc",
  "nd",
  "nc",
  "sp"
];

const validUnit = function(unit) {
  if (typeof unit !== "string") {
    unit = unit.unit;
  }
  return validUnits.indexOf(unit) > -1
};

const emScale = styleLevel => {
  const scriptLevel = Math.max(styleLevel - 1, 0);
  return [1, 0.7, 0.5][scriptLevel]
};

/*
 * Convert a "size" parse node (with numeric "number" and string "unit" fields,
 * as parsed by functions.js argType "size") into a CSS value.
 */
const calculateSize = function(sizeValue, style) {
  let number = sizeValue.number;
  if (style.maxSize[0] < 0 && number > 0) {
    return { number: 0, unit: "em" }
  }
  const unit = sizeValue.unit;
  switch (unit) {
    case "mm":
    case "cm":
    case "in":
    case "px": {
      const numInCssPts = number * ptPerUnit[unit];
      if (numInCssPts > style.maxSize[1]) {
        return { number: style.maxSize[1], unit: "pt" }
      }
      return { number, unit }; // absolute CSS units.
    }
    case "em":
    case "ex": {
      // In TeX, em and ex do not change size in \scriptstyle.
      if (unit === "ex") { number *= 0.431; }
      number = Math.min(number / emScale(style.level), style.maxSize[0]);
      return { number: utils.round(number), unit: "em" };
    }
    case "bp": {
      if (number > style.maxSize[1]) { number = style.maxSize[1]; }
      return { number, unit: "pt" }; // TeX bp is a CSS pt. (1/72 inch).
    }
    case "pt":
    case "pc":
    case "dd":
    case "cc":
    case "nd":
    case "nc":
    case "sp": {
      number = Math.min(number * ptPerUnit[unit], style.maxSize[1]);
      return { number: utils.round(number), unit: "pt" }
    }
    case "mu": {
      number = Math.min(number / 18, style.maxSize[0]);
      return { number: utils.round(number), unit: "em" }
    }
    default:
      throw new ParseError("Invalid unit: '" + unit + "'")
  }
};

// Helper functions

const padding$2 = width => {
  const node = new mathMLTree.MathNode("mspace");
  node.setAttribute("width", width + "em");
  return node
};

const paddedNode = (group, lspace = 0.3, rspace = 0, mustSmash = false) => {
  if (group == null && rspace === 0) { return padding$2(lspace) }
  const row = group ? [group] : [];
  if (lspace !== 0)   { row.unshift(padding$2(lspace)); }
  if (rspace > 0) { row.push(padding$2(rspace)); }
  if (mustSmash) {
    // Used for the bottom arrow in a {CD} environment
    const mpadded = new mathMLTree.MathNode("mpadded", row);
    mpadded.setAttribute("height", "0");
    return mpadded
  } else {
    return new mathMLTree.MathNode("mrow", row)
  }
};

const labelSize = (size, scriptLevel) =>  Number(size) / emScale(scriptLevel);

const munderoverNode = (fName, body, below, style) => {
  const arrowNode = stretchy.mathMLnode(fName);
  // Is this the short part of a mhchem equilibrium arrow?
  const isEq = fName.slice(1, 3) === "eq";
  const minWidth = fName.charAt(1) === "x"
    ? "1.75"  // mathtools extensible arrows are ≥ 1.75em long
    : fName.slice(2, 4) === "cd"
    ? "3.0"  // cd package arrows
    : isEq
    ? "1.0"  // The shorter harpoon of a mhchem equilibrium arrow
    : "2.0"; // other mhchem arrows
  // TODO: When Firefox supports minsize, use the next line.
  //arrowNode.setAttribute("minsize", String(minWidth) + "em")
  arrowNode.setAttribute("lspace", "0");
  arrowNode.setAttribute("rspace", (isEq ? "0.5em" : "0"));

  // <munderover> upper and lower labels are set to scriptlevel by MathML
  // So we have to adjust our label dimensions accordingly.
  const labelStyle = style.withLevel(style.level < 2 ? 2 : 3);
  const minArrowWidth = labelSize(minWidth, labelStyle.level);
  // The dummyNode will be inside a <mover> inside a <mover>
  // So it will be at scriptlevel 3
  const dummyWidth = labelSize(minWidth, 3);
  const emptyLabel = paddedNode(null, minArrowWidth.toFixed(4), 0);
  const dummyNode = paddedNode(null, dummyWidth.toFixed(4), 0);
  // The arrow is a little longer than the label. Set a spacer length.
  const space = labelSize((isEq ? 0 : 0.3), labelStyle.level).toFixed(4);
  let upperNode;
  let lowerNode;

  const gotUpper = (body && body.body &&
    // \hphantom        visible content
    (body.body.body || body.body.length > 0));
  if (gotUpper) {
    let label =  buildGroup$1(body, labelStyle);
    const mustSmash = (fName === "\\\\cdrightarrow" || fName === "\\\\cdleftarrow");
    label = paddedNode(label, space, space, mustSmash);
    // Since Firefox does not support minsize, stack a invisible node
    // on top of the label. Its width will serve as a min-width.
    // TODO: Refactor this after Firefox supports minsize.
    upperNode = new mathMLTree.MathNode("mover", [label, dummyNode]);
  }
  const gotLower = (below && below.body &&
    (below.body.body || below.body.length > 0));
  if (gotLower) {
    let label =  buildGroup$1(below, labelStyle);
    label = paddedNode(label, space, space);
    lowerNode = new mathMLTree.MathNode("munder", [label, dummyNode]);
  }

  let node;
  if (!gotUpper && !gotLower) {
    node = new mathMLTree.MathNode("mover", [arrowNode, emptyLabel]);
  } else if (gotUpper && gotLower) {
    node = new mathMLTree.MathNode("munderover", [arrowNode, lowerNode, upperNode]);
  } else if (gotUpper) {
    node = new mathMLTree.MathNode("mover", [arrowNode, upperNode]);
  } else {
    node = new mathMLTree.MathNode("munder", [arrowNode, lowerNode]);
  }
  if (minWidth === "3.0") { node.style.height = "1em"; } // CD environment
  node.setAttribute("accent", "false"); // Necessary for MS Word
  return node
};

// Stretchy arrows with an optional argument
defineFunction({
  type: "xArrow",
  names: [
    "\\xleftarrow",
    "\\xrightarrow",
    "\\xLeftarrow",
    "\\xRightarrow",
    "\\xleftrightarrow",
    "\\xLeftrightarrow",
    "\\xhookleftarrow",
    "\\xhookrightarrow",
    "\\xmapsto",
    "\\xrightharpoondown",
    "\\xrightharpoonup",
    "\\xleftharpoondown",
    "\\xleftharpoonup",
    "\\xlongequal",
    "\\xtwoheadrightarrow",
    "\\xtwoheadleftarrow",
    // The next 5 functions are here only to support mhchem
    "\\yields",
    "\\yieldsLeft",
    "\\mesomerism",
    "\\longrightharpoonup",
    "\\longleftharpoondown",
    // The next 3 functions are here only to support the {CD} environment.
    "\\\\cdrightarrow",
    "\\\\cdleftarrow",
    "\\\\cdlongequal"
  ],
  props: {
    numArgs: 1,
    numOptionalArgs: 1
  },
  handler({ parser, funcName }, args, optArgs) {
    return {
      type: "xArrow",
      mode: parser.mode,
      name: funcName,
      body: args[0],
      below: optArgs[0]
    };
  },
  mathmlBuilder(group, style) {
    // Build the arrow and its labels.
    const node = munderoverNode(group.name, group.body, group.below, style);
    // Create operator spacing for a relation.
    const row = [node];
    row.unshift(padding$2(0.2778));
    row.push(padding$2(0.2778));
    return new mathMLTree.MathNode("mrow", row)
  }
});

const arrowComponent = {
  "\\xtofrom": ["\\xrightarrow", "\\xleftarrow"],
  "\\xleftrightharpoons": ["\\xleftharpoonup", "\\xrightharpoondown"],
  "\\xrightleftharpoons": ["\\xrightharpoonup", "\\xleftharpoondown"],
  "\\yieldsLeftRight": ["\\yields", "\\yieldsLeft"],
  // The next three all get the same harpoon glyphs. Only the lengths and paddings differ.
  "\\equilibrium": ["\\longrightharpoonup", "\\longleftharpoondown"],
  "\\equilibriumRight": ["\\longrightharpoonup", "\\eqleftharpoondown"],
  "\\equilibriumLeft": ["\\eqrightharpoonup", "\\longleftharpoondown"]
};

// Browsers are not good at stretching a glyph that contains a pair of stacked arrows such as ⇄.
// So we stack a pair of single arrows.
defineFunction({
  type: "stackedArrow",
  names: [
    "\\xtofrom",              // expfeil
    "\\xleftrightharpoons",   // mathtools
    "\\xrightleftharpoons",   // mathtools
    "\\yieldsLeftRight",      // mhchem
    "\\equilibrium",          // mhchem
    "\\equilibriumRight",
    "\\equilibriumLeft"
  ],
  props: {
    numArgs: 1,
    numOptionalArgs: 1
  },
  handler({ parser, funcName }, args, optArgs) {
    const lowerArrowBody = args[0]
      ? {
        type: "hphantom",
        mode: parser.mode,
        body: args[0]
      }
      : null;
    const upperArrowBelow = optArgs[0]
      ? {
        type: "hphantom",
        mode: parser.mode,
        body: optArgs[0]
      }
      : null;
    return {
      type: "stackedArrow",
      mode: parser.mode,
      name: funcName,
      body: args[0],
      upperArrowBelow,
      lowerArrowBody,
      below: optArgs[0]
    };
  },
  mathmlBuilder(group, style) {
    const topLabel = arrowComponent[group.name][0];
    const botLabel = arrowComponent[group.name][1];
    const topArrow = munderoverNode(topLabel, group.body, group.upperArrowBelow, style);
    const botArrow = munderoverNode(botLabel, group.lowerArrowBody, group.below, style);
    let wrapper;

    const raiseNode = new mathMLTree.MathNode("mpadded", [topArrow]);
    raiseNode.setAttribute("voffset", "0.3em");
    raiseNode.setAttribute("height", "+0.3em");
    raiseNode.setAttribute("depth", "-0.3em");
    // One of the arrows is given ~zero width. so the other has the same horzontal alignment.
    if (group.name === "\\equilibriumLeft") {
      const botNode =  new mathMLTree.MathNode("mpadded", [botArrow]);
      botNode.setAttribute("width", "0.5em");
      wrapper = new mathMLTree.MathNode(
        "mpadded",
        [padding$2(0.2778), botNode, raiseNode, padding$2(0.2778)]
      );
    } else {
      raiseNode.setAttribute("width", (group.name === "\\equilibriumRight" ? "0.5em" : "0"));
      wrapper = new mathMLTree.MathNode(
        "mpadded",
        [padding$2(0.2778), raiseNode, botArrow, padding$2(0.2778)]
      );
    }

    wrapper.setAttribute("voffset", "-0.18em");
    wrapper.setAttribute("height", "-0.18em");
    wrapper.setAttribute("depth", "+0.18em");
    return wrapper
  }
});

/**
 * Asserts that the node is of the given type and returns it with stricter
 * typing. Throws if the node's type does not match.
 */
function assertNodeType(node, type) {
  if (!node || node.type !== type) {
    throw new Error(
      `Expected node of type ${type}, but got ` +
        (node ? `node of type ${node.type}` : String(node))
    );
  }
  return node;
}

/**
 * Returns the node more strictly typed iff it is of the given type. Otherwise,
 * returns null.
 */
function assertSymbolNodeType(node) {
  const typedNode = checkSymbolNodeType(node);
  if (!typedNode) {
    throw new Error(
      `Expected node of symbol group type, but got ` +
        (node ? `node of type ${node.type}` : String(node))
    );
  }
  return typedNode;
}

/**
 * Returns the node more strictly typed iff it is of the given type. Otherwise,
 * returns null.
 */
function checkSymbolNodeType(node) {
  if (node && (node.type === "atom" ||
      Object.prototype.hasOwnProperty.call(NON_ATOMS, node.type))) {
    return node;
  }
  return null;
}

const cdArrowFunctionName = {
  ">": "\\\\cdrightarrow",
  "<": "\\\\cdleftarrow",
  "=": "\\\\cdlongequal",
  A: "\\uparrow",
  V: "\\downarrow",
  "|": "\\Vert",
  ".": "no arrow"
};

const newCell = () => {
  // Create an empty cell, to be filled below with parse nodes.
  return { type: "styling", body: [], mode: "math", scriptLevel: "display" };
};

const isStartOfArrow = (node) => {
  return node.type === "textord" && node.text === "@";
};

const isLabelEnd = (node, endChar) => {
  return (node.type === "mathord" || node.type === "atom") && node.text === endChar;
};

function cdArrow(arrowChar, labels, parser) {
  // Return a parse tree of an arrow and its labels.
  // This acts in a way similar to a macro expansion.
  const funcName = cdArrowFunctionName[arrowChar];
  switch (funcName) {
    case "\\\\cdrightarrow":
    case "\\\\cdleftarrow":
      return parser.callFunction(funcName, [labels[0]], [labels[1]]);
    case "\\uparrow":
    case "\\downarrow": {
      const leftLabel = parser.callFunction("\\\\cdleft", [labels[0]], []);
      const bareArrow = {
        type: "atom",
        text: funcName,
        mode: "math",
        family: "rel"
      };
      const sizedArrow = parser.callFunction("\\Big", [bareArrow], []);
      const rightLabel = parser.callFunction("\\\\cdright", [labels[1]], []);
      const arrowGroup = {
        type: "ordgroup",
        mode: "math",
        body: [leftLabel, sizedArrow, rightLabel],
        semisimple: true
      };
      return parser.callFunction("\\\\cdparent", [arrowGroup], []);
    }
    case "\\\\cdlongequal":
      return parser.callFunction("\\\\cdlongequal", [], []);
    case "\\Vert": {
      const arrow = { type: "textord", text: "\\Vert", mode: "math" };
      return parser.callFunction("\\Big", [arrow], []);
    }
    default:
      return { type: "textord", text: " ", mode: "math" };
  }
}

function parseCD(parser) {
  // Get the array's parse nodes with \\ temporarily mapped to \cr.
  const parsedRows = [];
  parser.gullet.beginGroup();
  parser.gullet.macros.set("\\cr", "\\\\\\relax");
  parser.gullet.beginGroup();
  while (true) {
    // Get the parse nodes for the next row.
    parsedRows.push(parser.parseExpression(false, "\\\\"));
    parser.gullet.endGroup();
    parser.gullet.beginGroup();
    const next = parser.fetch().text;
    if (next === "&" || next === "\\\\") {
      parser.consume();
    } else if (next === "\\end") {
      if (parsedRows[parsedRows.length - 1].length === 0) {
        parsedRows.pop(); // final row ended in \\
      }
      break;
    } else {
      throw new ParseError("Expected \\\\ or \\cr or \\end", parser.nextToken);
    }
  }

  let row = [];
  const body = [row];

  // Loop thru the parse nodes. Collect them into cells and arrows.
  for (let i = 0; i < parsedRows.length; i++) {
    // Start a new row.
    const rowNodes = parsedRows[i];
    // Create the first cell.
    let cell = newCell();

    for (let j = 0; j < rowNodes.length; j++) {
      if (!isStartOfArrow(rowNodes[j])) {
        // If a parseNode is not an arrow, it goes into a cell.
        cell.body.push(rowNodes[j]);
      } else {
        // Parse node j is an "@", the start of an arrow.
        // Before starting on the arrow, push the cell into `row`.
        row.push(cell);

        // Now collect parseNodes into an arrow.
        // The character after "@" defines the arrow type.
        j += 1;
        const arrowChar = assertSymbolNodeType(rowNodes[j]).text;

        // Create two empty label nodes. We may or may not use them.
        const labels = new Array(2);
        labels[0] = { type: "ordgroup", mode: "math", body: [] };
        labels[1] = { type: "ordgroup", mode: "math", body: [] };

        // Process the arrow.
        if ("=|.".indexOf(arrowChar) > -1) ; else if ("<>AV".indexOf(arrowChar) > -1) {
          // Four arrows, `@>>>`, `@<<<`, `@AAA`, and `@VVV`, each take
          // two optional labels. E.g. the right-point arrow syntax is
          // really:  @>{optional label}>{optional label}>
          // Collect parseNodes into labels.
          for (let labelNum = 0; labelNum < 2; labelNum++) {
            let inLabel = true;
            for (let k = j + 1; k < rowNodes.length; k++) {
              if (isLabelEnd(rowNodes[k], arrowChar)) {
                inLabel = false;
                j = k;
                break;
              }
              if (isStartOfArrow(rowNodes[k])) {
                throw new ParseError(
                  "Missing a " + arrowChar + " character to complete a CD arrow.",
                  rowNodes[k]
                );
              }

              labels[labelNum].body.push(rowNodes[k]);
            }
            if (inLabel) {
              // isLabelEnd never returned a true.
              throw new ParseError(
                "Missing a " + arrowChar + " character to complete a CD arrow.",
                rowNodes[j]
              );
            }
          }
        } else {
          throw new ParseError(`Expected one of "<>AV=|." after @.`);
        }

        // Now join the arrow to its labels.
        const arrow = cdArrow(arrowChar, labels, parser);

        // Wrap the arrow in a styling node
        row.push(arrow);
        // In CD's syntax, cells are implicit. That is, everything that
        // is not an arrow gets collected into a cell. So create an empty
        // cell now. It will collect upcoming parseNodes.
        cell = newCell();
      }
    }
    if (i % 2 === 0) {
      // Even-numbered rows consist of: cell, arrow, cell, arrow, ... cell
      // The last cell is not yet pushed into `row`, so:
      row.push(cell);
    } else {
      // Odd-numbered rows consist of: vert arrow, empty cell, ... vert arrow
      // Remove the empty cell that was placed at the beginning of `row`.
      row.shift();
    }
    row = [];
    body.push(row);
  }
  body.pop();

  // End row group
  parser.gullet.endGroup();
  // End array group defining \\
  parser.gullet.endGroup();

  return {
    type: "array",
    mode: "math",
    body,
    tags: null,
    labels: new Array(body.length + 1).fill(""),
    envClasses: ["jot", "cd"],
    cols: [],
    hLinesBeforeRow: new Array(body.length + 1).fill([])
  };
}

// The functions below are not available for general use.
// They are here only for internal use by the {CD} environment in placing labels
// next to vertical arrows.

// We don't need any such functions for horizontal arrows because we can reuse
// the functionality that already exists for extensible arrows.

defineFunction({
  type: "cdlabel",
  names: ["\\\\cdleft", "\\\\cdright"],
  props: {
    numArgs: 1
  },
  handler({ parser, funcName }, args) {
    return {
      type: "cdlabel",
      mode: parser.mode,
      side: funcName.slice(4),
      label: args[0]
    };
  },
  mathmlBuilder(group, style) {
    if (group.label.body.length === 0) {
      return new mathMLTree.MathNode("mrow", style)  // empty label
    }
    // Abuse an <mtable> to create vertically centered content.
    const mtd = new mathMLTree.MathNode("mtd", [buildGroup$1(group.label, style)]);
    mtd.style.padding = "0";
    const mtr = new mathMLTree.MathNode("mtr", [mtd]);
    const mtable = new mathMLTree.MathNode("mtable", [mtr]);
    const label = new mathMLTree.MathNode("mpadded", [mtable]);
    // Set the label width to zero so that the arrow will be centered under the corner cell.
    label.setAttribute("width", "0");
    label.setAttribute("displaystyle", "false");
    label.setAttribute("scriptlevel", "1");
    if (group.side === "left") {
      label.style.display = "flex";
      label.style.justifyContent = "flex-end";
    }
    return label;
  }
});

defineFunction({
  type: "cdlabelparent",
  names: ["\\\\cdparent"],
  props: {
    numArgs: 1
  },
  handler({ parser }, args) {
    return {
      type: "cdlabelparent",
      mode: parser.mode,
      fragment: args[0]
    };
  },
  mathmlBuilder(group, style) {
    return new mathMLTree.MathNode("mrow", [buildGroup$1(group.fragment, style)]);
  }
});

// \@char is an internal function that takes a grouped decimal argument like
// {123} and converts into symbol with code 123.  It is used by the *macro*
// \char defined in macros.js.
defineFunction({
  type: "textord",
  names: ["\\@char"],
  props: {
    numArgs: 1,
    allowedInText: true
  },
  handler({ parser, token }, args) {
    const arg = assertNodeType(args[0], "ordgroup");
    const group = arg.body;
    let number = "";
    for (let i = 0; i < group.length; i++) {
      const node = assertNodeType(group[i], "textord");
      number += node.text;
    }
    const code = parseInt(number);
    if (isNaN(code)) {
      throw new ParseError(`\\@char has non-numeric argument ${number}`, token)
    }
    return {
      type: "textord",
      mode: parser.mode,
      text: String.fromCodePoint(code)
    }
  }
});

// Helpers
const htmlRegEx = /^(#[a-f0-9]{3}|#?[a-f0-9]{6})$/i;
const htmlOrNameRegEx = /^(#[a-f0-9]{3}|#?[a-f0-9]{6}|[a-z]+)$/i;
const RGBregEx = /^ *\d{1,3} *(?:, *\d{1,3} *){2}$/;
const rgbRegEx = /^ *[10](?:\.\d*)? *(?:, *[10](?:\.\d*)? *){2}$/;
const xcolorHtmlRegEx = /^[a-f0-9]{6}$/i;
const toHex = num => {
  let str = num.toString(16);
  if (str.length === 1) { str = "0" + str; }
  return str
};

// Colors from Tables 4.1 and 4.2 of the xcolor package.
// Table 4.1 (lower case) RGB values are taken from chroma and xcolor.dtx.
// Table 4.2 (Capitalizzed) values were sampled, because Chroma contains a unreliable
// conversion from cmyk to RGB. See https://tex.stackexchange.com/a/537274.
const xcolors = JSON.parse(`{
  "Apricot": "#ffb484",
  "Aquamarine": "#08b4bc",
  "Bittersweet": "#c84c14",
  "blue": "#0000FF",
  "Blue": "#303494",
  "BlueGreen": "#08b4bc",
  "BlueViolet": "#503c94",
  "BrickRed": "#b8341c",
  "brown": "#BF8040",
  "Brown": "#802404",
  "BurntOrange": "#f8941c",
  "CadetBlue": "#78749c",
  "CarnationPink": "#f884b4",
  "Cerulean": "#08a4e4",
  "CornflowerBlue": "#40ace4",
  "cyan": "#00FFFF",
  "Cyan": "#08acec",
  "Dandelion": "#ffbc44",
  "darkgray": "#404040",
  "DarkOrchid": "#a8548c",
  "Emerald": "#08ac9c",
  "ForestGreen": "#089c54",
  "Fuchsia": "#90348c",
  "Goldenrod": "#ffdc44",
  "gray": "#808080",
  "Gray": "#98949c",
  "green": "#00FF00",
  "Green": "#08a44c",
  "GreenYellow": "#e0e474",
  "JungleGreen": "#08ac9c",
  "Lavender": "#f89cc4",
  "lightgray": "#c0c0c0",
  "lime": "#BFFF00",
  "LimeGreen": "#90c43c",
  "magenta": "#FF00FF",
  "Magenta": "#f0048c",
  "Mahogany": "#b0341c",
  "Maroon": "#b03434",
  "Melon": "#f89c7c",
  "MidnightBlue": "#086494",
  "Mulberry": "#b03c94",
  "NavyBlue": "#086cbc",
  "olive": "#7F7F00",
  "OliveGreen": "#407c34",
  "orange": "#FF8000",
  "Orange": "#f8843c",
  "OrangeRed": "#f0145c",
  "Orchid": "#b074ac",
  "Peach": "#f8945c",
  "Periwinkle": "#8074bc",
  "PineGreen": "#088c74",
  "pink": "#ff7f7f",
  "Plum": "#98248c",
  "ProcessBlue": "#08b4ec",
  "purple": "#BF0040",
  "Purple": "#a0449c",
  "RawSienna": "#983c04",
  "red": "#ff0000",
  "Red": "#f01c24",
  "RedOrange": "#f86434",
  "RedViolet": "#a0246c",
  "Rhodamine": "#f0549c",
  "Royallue": "#0874bc",
  "RoyalPurple": "#683c9c",
  "RubineRed": "#f0047c",
  "Salmon": "#f8948c",
  "SeaGreen": "#30bc9c",
  "Sepia": "#701404",
  "SkyBlue": "#48c4dc",
  "SpringGreen": "#c8dc64",
  "Tan": "#e09c74",
  "teal": "#007F7F",
  "TealBlue": "#08acb4",
  "Thistle": "#d884b4",
  "Turquoise": "#08b4cc",
  "violet": "#800080",
  "Violet": "#60449c",
  "VioletRed": "#f054a4",
  "WildStrawberry": "#f0246c",
  "yellow": "#FFFF00",
  "Yellow": "#fff404",
  "YellowGreen": "#98cc6c",
  "YellowOrange": "#ffa41c"
}`);

const colorFromSpec = (model, spec) => {
  let color = "";
  if (model === "HTML") {
    if (!htmlRegEx.test(spec)) {
      throw new ParseError("Invalid HTML input.")
    }
    color = spec;
  } else if (model === "RGB") {
    if (!RGBregEx.test(spec)) {
      throw new ParseError("Invalid RGB input.")
    }
    spec.split(",").map(e => { color += toHex(Number(e.trim())); });
  } else {
    if (!rgbRegEx.test(spec)) {
      throw new ParseError("Invalid rbg input.")
    }
    spec.split(",").map(e => {
      const num = Number(e.trim());
      if (num > 1) { throw new ParseError("Color rgb input must be < 1.") }
      color += toHex(Number((num * 255).toFixed(0)));
    });
  }
  if (color.charAt(0) !== "#") { color = "#" + color; }
  return color
};

const validateColor = (color, macros, token) => {
  const macroName = `\\\\color@${color}`; // from \defineColor.
  const match = htmlOrNameRegEx.exec(color);
  if (!match) { throw new ParseError("Invalid color: '" + color + "'", token) }
  // We allow a 6-digit HTML color spec without a leading "#".
  // This follows the xcolor package's HTML color model.
  // Predefined color names are all missed by this RegEx pattern.
  if (xcolorHtmlRegEx.test(color)) {
    return "#" + color
  } else if (color.charAt(0) === "#") {
    return color
  } else if (macros.has(macroName)) {
    color = macros.get(macroName).tokens[0].text;
  } else if (xcolors[color]) {
    color = xcolors[color];
  }
  return color
};

const mathmlBuilder$9 = (group, style) => {
  // In LaTeX, color is not supposed to change the spacing of any node.
  // So instead of wrapping the group in an <mstyle>, we apply
  // the color individually to each node and return a document fragment.
  let expr = buildExpression(group.body, style.withColor(group.color));
  expr = expr.map(e => {
    e.style.color = group.color;
    return e
  });
  return mathMLTree.newDocumentFragment(expr)
};

defineFunction({
  type: "color",
  names: ["\\textcolor"],
  props: {
    numArgs: 2,
    numOptionalArgs: 1,
    allowedInText: true,
    argTypes: ["raw", "raw", "original"]
  },
  handler({ parser, token }, args, optArgs) {
    const model = optArgs[0] && assertNodeType(optArgs[0], "raw").string;
    let color = "";
    if (model) {
      const spec = assertNodeType(args[0], "raw").string;
      color = colorFromSpec(model, spec);
    } else {
      color = validateColor(assertNodeType(args[0], "raw").string, parser.gullet.macros, token);
    }
    const body = args[1];
    return {
      type: "color",
      mode: parser.mode,
      color,
      isTextColor: true,
      body: ordargument(body)
    }
  },
  mathmlBuilder: mathmlBuilder$9
});

defineFunction({
  type: "color",
  names: ["\\color"],
  props: {
    numArgs: 1,
    numOptionalArgs: 1,
    allowedInText: true,
    argTypes: ["raw", "raw"]
  },
  handler({ parser, breakOnTokenText, token }, args, optArgs) {
    const model = optArgs[0] && assertNodeType(optArgs[0], "raw").string;
    let color = "";
    if (model) {
      const spec = assertNodeType(args[0], "raw").string;
      color = colorFromSpec(model, spec);
    } else {
      color = validateColor(assertNodeType(args[0], "raw").string, parser.gullet.macros, token);
    }

    // Parse out the implicit body that should be colored.
    const body = parser.parseExpression(true, breakOnTokenText, true);

    return {
      type: "color",
      mode: parser.mode,
      color,
      isTextColor: false,
      body
    }
  },
  mathmlBuilder: mathmlBuilder$9
});

defineFunction({
  type: "color",
  names: ["\\definecolor"],
  props: {
    numArgs: 3,
    allowedInText: true,
    argTypes: ["raw", "raw", "raw"]
  },
  handler({ parser, funcName, token }, args) {
    const name = assertNodeType(args[0], "raw").string;
    if (!/^[A-Za-z]+$/.test(name)) {
      throw new ParseError("Color name must be latin letters.", token)
    }
    const model = assertNodeType(args[1], "raw").string;
    if (!["HTML", "RGB", "rgb"].includes(model)) {
      throw new ParseError("Color model must be HTML, RGB, or rgb.", token)
    }
    const spec = assertNodeType(args[2], "raw").string;
    const color = colorFromSpec(model, spec);
    parser.gullet.macros.set(`\\\\color@${name}`, { tokens: [{ text: color }], numArgs: 0 });
    return { type: "internal", mode: parser.mode }
  }
  // No mathmlBuilder. The point of \definecolor is to set a macro.
});

// Row breaks within tabular environments, and line breaks at top level


// \DeclareRobustCommand\\{...\@xnewline}
defineFunction({
  type: "cr",
  names: ["\\\\"],
  props: {
    numArgs: 0,
    numOptionalArgs: 0,
    allowedInText: true
  },

  handler({ parser }, args, optArgs) {
    const size = parser.gullet.future().text === "[" ? parser.parseSizeGroup(true) : null;
    const newLine = !parser.settings.displayMode;
    return {
      type: "cr",
      mode: parser.mode,
      newLine,
      size: size && assertNodeType(size, "size").value
    }
  },

  // The following builder is called only at the top level,
  // not within tabular/array environments.

  mathmlBuilder(group, style) {
    // MathML 3.0 calls for newline to occur in an <mo> or an <mspace>.
    // Ref: https://www.w3.org/TR/MathML3/chapter3.html#presm.linebreaking
    const node = new mathMLTree.MathNode("mo");
    if (group.newLine) {
      node.setAttribute("linebreak", "newline");
      if (group.size) {
        const size = calculateSize(group.size, style);
        node.setAttribute("height", size.number + size.unit);
      }
    }
    return node
  }
});

const globalMap = {
  "\\global": "\\global",
  "\\long": "\\\\globallong",
  "\\\\globallong": "\\\\globallong",
  "\\def": "\\gdef",
  "\\gdef": "\\gdef",
  "\\edef": "\\xdef",
  "\\xdef": "\\xdef",
  "\\let": "\\\\globallet",
  "\\futurelet": "\\\\globalfuture"
};

const checkControlSequence = (tok) => {
  const name = tok.text;
  if (/^(?:[\\{}$&#^_]|EOF)$/.test(name)) {
    throw new ParseError("Expected a control sequence", tok);
  }
  return name;
};

const getRHS = (parser) => {
  let tok = parser.gullet.popToken();
  if (tok.text === "=") {
    // consume optional equals
    tok = parser.gullet.popToken();
    if (tok.text === " ") {
      // consume one optional space
      tok = parser.gullet.popToken();
    }
  }
  return tok;
};

const letCommand = (parser, name, tok, global) => {
  let macro = parser.gullet.macros.get(tok.text);
  if (macro == null) {
    // don't expand it later even if a macro with the same name is defined
    // e.g., \let\foo=\frac \def\frac{\relax} \frac12
    tok.noexpand = true;
    macro = {
      tokens: [tok],
      numArgs: 0,
      // reproduce the same behavior in expansion
      unexpandable: !parser.gullet.isExpandable(tok.text)
    };
  }
  parser.gullet.macros.set(name, macro, global);
};

// <assignment> -> <non-macro assignment>|<macro assignment>
// <non-macro assignment> -> <simple assignment>|\global<non-macro assignment>
// <macro assignment> -> <definition>|<prefix><macro assignment>
// <prefix> -> \global|\long|\outer
defineFunction({
  type: "internal",
  names: [
    "\\global",
    "\\long",
    "\\\\globallong" // can’t be entered directly
  ],
  props: {
    numArgs: 0,
    allowedInText: true
  },
  handler({ parser, funcName }) {
    parser.consumeSpaces();
    const token = parser.fetch();
    if (globalMap[token.text]) {
      // Temml doesn't have \par, so ignore \long
      if (funcName === "\\global" || funcName === "\\\\globallong") {
        token.text = globalMap[token.text];
      }
      return assertNodeType(parser.parseFunction(), "internal");
    }
    throw new ParseError(`Invalid token after macro prefix`, token);
  }
});

// Basic support for macro definitions: \def, \gdef, \edef, \xdef
// <definition> -> <def><control sequence><definition text>
// <def> -> \def|\gdef|\edef|\xdef
// <definition text> -> <parameter text><left brace><balanced text><right brace>
defineFunction({
  type: "internal",
  names: ["\\def", "\\gdef", "\\edef", "\\xdef"],
  props: {
    numArgs: 0,
    allowedInText: true,
    primitive: true
  },
  handler({ parser, funcName }) {
    let tok = parser.gullet.popToken();
    const name = tok.text;
    if (/^(?:[\\{}$&#^_]|EOF)$/.test(name)) {
      throw new ParseError("Expected a control sequence", tok);
    }

    let numArgs = 0;
    let insert;
    const delimiters = [[]];
    // <parameter text> contains no braces
    while (parser.gullet.future().text !== "{") {
      tok = parser.gullet.popToken();
      if (tok.text === "#") {
        // If the very last character of the <parameter text> is #, so that
        // this # is immediately followed by {, TeX will behave as if the {
        // had been inserted at the right end of both the parameter text
        // and the replacement text.
        if (parser.gullet.future().text === "{") {
          insert = parser.gullet.future();
          delimiters[numArgs].push("{");
          break;
        }

        // A parameter, the first appearance of # must be followed by 1,
        // the next by 2, and so on; up to nine #’s are allowed
        tok = parser.gullet.popToken();
        if (!/^[1-9]$/.test(tok.text)) {
          throw new ParseError(`Invalid argument number "${tok.text}"`);
        }
        if (parseInt(tok.text) !== numArgs + 1) {
          throw new ParseError(`Argument number "${tok.text}" out of order`);
        }
        numArgs++;
        delimiters.push([]);
      } else if (tok.text === "EOF") {
        throw new ParseError("Expected a macro definition");
      } else {
        delimiters[numArgs].push(tok.text);
      }
    }
    // replacement text, enclosed in '{' and '}' and properly nested
    let { tokens } = parser.gullet.consumeArg();
    if (insert) {
      tokens.unshift(insert);
    }

    if (funcName === "\\edef" || funcName === "\\xdef") {
      tokens = parser.gullet.expandTokens(tokens);
      if (tokens.length > parser.gullet.settings.maxExpand) {
        throw new ParseError("Too many expansions in an " + funcName);
      }
      tokens.reverse(); // to fit in with stack order
    }
    // Final arg is the expansion of the macro
    parser.gullet.macros.set(
      name,
      { tokens, numArgs, delimiters },
      funcName === globalMap[funcName]
    );
    return { type: "internal", mode: parser.mode };
  }
});

// <simple assignment> -> <let assignment>
// <let assignment> -> \futurelet<control sequence><token><token>
//     | \let<control sequence><equals><one optional space><token>
// <equals> -> <optional spaces>|<optional spaces>=
defineFunction({
  type: "internal",
  names: [
    "\\let",
    "\\\\globallet" // can’t be entered directly
  ],
  props: {
    numArgs: 0,
    allowedInText: true,
    primitive: true
  },
  handler({ parser, funcName }) {
    const name = checkControlSequence(parser.gullet.popToken());
    parser.gullet.consumeSpaces();
    const tok = getRHS(parser);
    letCommand(parser, name, tok, funcName === "\\\\globallet");
    return { type: "internal", mode: parser.mode };
  }
});

// ref: https://www.tug.org/TUGboat/tb09-3/tb22bechtolsheim.pdf
defineFunction({
  type: "internal",
  names: [
    "\\futurelet",
    "\\\\globalfuture" // can’t be entered directly
  ],
  props: {
    numArgs: 0,
    allowedInText: true,
    primitive: true
  },
  handler({ parser, funcName }) {
    const name = checkControlSequence(parser.gullet.popToken());
    const middle = parser.gullet.popToken();
    const tok = parser.gullet.popToken();
    letCommand(parser, name, tok, funcName === "\\\\globalfuture");
    parser.gullet.pushToken(tok);
    parser.gullet.pushToken(middle);
    return { type: "internal", mode: parser.mode };
  }
});

defineFunction({
  type: "internal",
  names: ["\\newcommand", "\\renewcommand", "\\providecommand"],
  props: {
    numArgs: 0,
    allowedInText: true,
    primitive: true
  },
  handler({ parser, funcName }) {
    let name = "";
    const tok = parser.gullet.popToken();
    if (tok.text === "{") {
      name = checkControlSequence(parser.gullet.popToken());
      parser.gullet.popToken();
    } else {
      name = checkControlSequence(tok);
    }

    const exists = parser.gullet.isDefined(name);
    if (exists && funcName === "\\newcommand") {
      throw new ParseError(
        `\\newcommand{${name}} attempting to redefine ${name}; use \\renewcommand`
      );
    }
    if (!exists && funcName === "\\renewcommand") {
      throw new ParseError(
        `\\renewcommand{${name}} when command ${name} does not yet exist; use \\newcommand`
      );
    }

    let numArgs = 0;
    if (parser.gullet.future().text === "[") {
      let tok = parser.gullet.popToken();
      tok = parser.gullet.popToken();
      if (!/^[0-9]$/.test(tok.text)) {
        throw new ParseError(`Invalid number of arguments: "${tok.text}"`);
      }
      numArgs = parseInt(tok.text);
      tok = parser.gullet.popToken();
      if (tok.text !== "]") {
        throw new ParseError(`Invalid argument "${tok.text}"`);
      }
    }

    // replacement text, enclosed in '{' and '}' and properly nested
    const { tokens } = parser.gullet.consumeArg();

    if (!(funcName === "\\providecommand" && parser.gullet.macros.has(name))) {
      // Ignore \providecommand
      parser.gullet.macros.set(
        name,
        { tokens, numArgs }
      );
    }

    return { type: "internal", mode: parser.mode };

  }
});

// Extra data needed for the delimiter handler down below
const delimiterSizes = {
  "\\bigl": { mclass: "mopen", size: 1 },
  "\\Bigl": { mclass: "mopen", size: 2 },
  "\\biggl": { mclass: "mopen", size: 3 },
  "\\Biggl": { mclass: "mopen", size: 4 },
  "\\bigr": { mclass: "mclose", size: 1 },
  "\\Bigr": { mclass: "mclose", size: 2 },
  "\\biggr": { mclass: "mclose", size: 3 },
  "\\Biggr": { mclass: "mclose", size: 4 },
  "\\bigm": { mclass: "mrel", size: 1 },
  "\\Bigm": { mclass: "mrel", size: 2 },
  "\\biggm": { mclass: "mrel", size: 3 },
  "\\Biggm": { mclass: "mrel", size: 4 },
  "\\big": { mclass: "mord", size: 1 },
  "\\Big": { mclass: "mord", size: 2 },
  "\\bigg": { mclass: "mord", size: 3 },
  "\\Bigg": { mclass: "mord", size: 4 }
};

const delimiters = [
  "(",
  "\\lparen",
  ")",
  "\\rparen",
  "[",
  "\\lbrack",
  "]",
  "\\rbrack",
  "\\{",
  "\\lbrace",
  "\\}",
  "\\rbrace",
  "⦇",
  "\\llparenthesis",
  "⦈",
  "\\rrparenthesis",
  "\\lfloor",
  "\\rfloor",
  "\u230a",
  "\u230b",
  "\\lceil",
  "\\rceil",
  "\u2308",
  "\u2309",
  "<",
  ">",
  "\\langle",
  "\u27e8",
  "\\rangle",
  "\u27e9",
  "\\lAngle",
  "\u27ea",
  "\\rAngle",
  "\u27eb",
  "\\llangle",
  "⦉",
  "\\rrangle",
  "⦊",
  "\\lt",
  "\\gt",
  "\\lvert",
  "\\rvert",
  "\\lVert",
  "\\rVert",
  "\\lgroup",
  "\\rgroup",
  "\u27ee",
  "\u27ef",
  "\\lmoustache",
  "\\rmoustache",
  "\u23b0",
  "\u23b1",
  "\\llbracket",
  "\\rrbracket",
  "\u27e6",
  "\u27e6",
  "\\lBrace",
  "\\rBrace",
  "\u2983",
  "\u2984",
  "/",
  "\\backslash",
  "|",
  "\\vert",
  "\\|",
  "\\Vert",
  "\u2016",
  "\\uparrow",
  "\\Uparrow",
  "\\downarrow",
  "\\Downarrow",
  "\\updownarrow",
  "\\Updownarrow",
  "."
];

// Export isDelimiter for benefit of parser.
const dels = ["}", "\\left", "\\middle", "\\right"];
const isDelimiter = str => str.length > 0 &&
  (delimiters.includes(str) || delimiterSizes[str] || dels.includes(str));

// Metrics of the different sizes. Found by looking at TeX's output of
// $\bigl| // \Bigl| \biggl| \Biggl| \showlists$
// Used to create stacked delimiters of appropriate sizes in makeSizedDelim.
const sizeToMaxHeight = [0, 1.2, 1.8, 2.4, 3.0];

// Delimiter functions
function checkDelimiter(delim, context) {
  const symDelim = checkSymbolNodeType(delim);
  if (symDelim && delimiters.includes(symDelim.text)) {
    // If a character is not in the MathML operator dictionary, it will not stretch.
    // Replace such characters w/characters that will stretch.
    if (["<", "\\lt"].includes(symDelim.text)) { symDelim.text = "⟨"; }
    if ([">", "\\gt"].includes(symDelim.text)) { symDelim.text = "⟩"; }
    return symDelim;
  } else if (symDelim) {
    throw new ParseError(`Invalid delimiter '${symDelim.text}' after '${context.funcName}'`, delim);
  } else {
    throw new ParseError(`Invalid delimiter type '${delim.type}'`, delim);
  }
}

//                               /         \
const needExplicitStretch = ["\u002F", "\u005C", "\\backslash", "\\vert", "|"];

defineFunction({
  type: "delimsizing",
  names: [
    "\\bigl",
    "\\Bigl",
    "\\biggl",
    "\\Biggl",
    "\\bigr",
    "\\Bigr",
    "\\biggr",
    "\\Biggr",
    "\\bigm",
    "\\Bigm",
    "\\biggm",
    "\\Biggm",
    "\\big",
    "\\Big",
    "\\bigg",
    "\\Bigg"
  ],
  props: {
    numArgs: 1,
    argTypes: ["primitive"]
  },
  handler: (context, args) => {
    const delim = checkDelimiter(args[0], context);

    return {
      type: "delimsizing",
      mode: context.parser.mode,
      size: delimiterSizes[context.funcName].size,
      mclass: delimiterSizes[context.funcName].mclass,
      delim: delim.text
    };
  },
  mathmlBuilder: (group) => {
    const children = [];

    if (group.delim === ".") { group.delim = ""; }
    children.push(makeText(group.delim, group.mode));

    const node = new mathMLTree.MathNode("mo", children);

    if (group.mclass === "mopen" || group.mclass === "mclose") {
      // Only some of the delimsizing functions act as fences, and they
      // return "mopen" or "mclose" mclass.
      node.setAttribute("fence", "true");
    } else {
      // Explicitly disable fencing if it's not a fence, to override the
      // defaults.
      node.setAttribute("fence", "false");
    }
    if (needExplicitStretch.includes(group.delim) || group.delim.indexOf("arrow") > -1) {
      // We have to explicitly set stretchy to true.
      node.setAttribute("stretchy", "true");
    }
    node.setAttribute("symmetric", "true"); // Needed for tall arrows in Firefox.
    node.setAttribute("minsize", sizeToMaxHeight[group.size] + "em");
    node.setAttribute("maxsize", sizeToMaxHeight[group.size] + "em");
    return node;
  }
});

function assertParsed(group) {
  if (!group.body) {
    throw new Error("Bug: The leftright ParseNode wasn't fully parsed.");
  }
}

defineFunction({
  type: "leftright-right",
  names: ["\\right"],
  props: {
    numArgs: 1,
    argTypes: ["primitive"]
  },
  handler: (context, args) => {
    return {
      type: "leftright-right",
      mode: context.parser.mode,
      delim: checkDelimiter(args[0], context).text
    };
  }
});

defineFunction({
  type: "leftright",
  names: ["\\left"],
  props: {
    numArgs: 1,
    argTypes: ["primitive"]
  },
  handler: (context, args) => {
    const delim = checkDelimiter(args[0], context);

    const parser = context.parser;
    // Parse out the implicit body
    ++parser.leftrightDepth;
    // parseExpression stops before '\\right' or `\\middle`
    let body = parser.parseExpression(false, null, true);
    let nextToken = parser.fetch();
    while (nextToken.text === "\\middle") {
      // `\middle`, from the ε-TeX package, ends one group and starts another group.
      // We had to parse this expression with `breakOnMiddle` enabled in order
      // to get TeX-compliant parsing of \over.
      // But we do not want, at this point, to end on \middle, so continue
      // to parse until we fetch a `\right`.
      parser.consume();
      const middle = parser.fetch().text;
      if (!symbols.math[middle]) {
        throw new ParseError(`Invalid delimiter '${middle}' after '\\middle'`);
      }
      checkDelimiter({ type: "atom", mode: "math", text: middle }, { funcName: "\\middle" });
      body.push({ type: "middle", mode: "math", delim: middle });
      parser.consume();
      body = body.concat(parser.parseExpression(false, null, true));
      nextToken = parser.fetch();
    }
    --parser.leftrightDepth;
    // Check the next token
    parser.expect("\\right", false);
    const right = assertNodeType(parser.parseFunction(), "leftright-right");
    return {
      type: "leftright",
      mode: parser.mode,
      body,
      left: delim.text,
      right: right.delim
    };
  },
  mathmlBuilder: (group, style) => {
    assertParsed(group);
    const inner = buildExpression(group.body, style);

    if (group.left === ".") { group.left = ""; }
    const leftNode = new mathMLTree.MathNode("mo", [makeText(group.left, group.mode)]);
    leftNode.setAttribute("fence", "true");
    leftNode.setAttribute("form", "prefix");
    if (group.left === "/" || group.left === "\u005C" || group.left.indexOf("arrow") > -1) {
      leftNode.setAttribute("stretchy", "true");
    }
    inner.unshift(leftNode);

    if (group.right === ".") { group.right = ""; }
    const rightNode = new mathMLTree.MathNode("mo", [makeText(group.right, group.mode)]);
    rightNode.setAttribute("fence", "true");
    rightNode.setAttribute("form", "postfix");
    if (group.right === "\u2216" || group.right.indexOf("arrow") > -1) {
      rightNode.setAttribute("stretchy", "true");
    }
    if (group.body.length > 0) {
      const lastElement = group.body[group.body.length - 1];
      if (lastElement.type === "color" && !lastElement.isTextColor) {
        // \color is a switch. If the last element is of type "color" then
        // the user set the \color switch and left it on.
        // A \right delimiter turns the switch off, but the delimiter itself gets the color.
        rightNode.setAttribute("mathcolor", lastElement.color);
      }
    }
    inner.push(rightNode);

    return makeRow(inner);
  }
});

defineFunction({
  type: "middle",
  names: ["\\middle"],
  props: {
    numArgs: 1,
    argTypes: ["primitive"]
  },
  handler: (context, args) => {
    const delim = checkDelimiter(args[0], context);
    if (!context.parser.leftrightDepth) {
      throw new ParseError("\\middle without preceding \\left", delim);
    }

    return {
      type: "middle",
      mode: context.parser.mode,
      delim: delim.text
    };
  },
  mathmlBuilder: (group, style) => {
    const textNode = makeText(group.delim, group.mode);
    const middleNode = new mathMLTree.MathNode("mo", [textNode]);
    middleNode.setAttribute("fence", "true");
    if (group.delim.indexOf("arrow") > -1) {
      middleNode.setAttribute("stretchy", "true");
    }
    // The next line is not semantically correct, but
    // Chromium fails to stretch if it is not there.
    middleNode.setAttribute("form", "prefix");
    // MathML gives 5/18em spacing to each <mo> element.
    // \middle should get delimiter spacing instead.
    middleNode.setAttribute("lspace", "0.05em");
    middleNode.setAttribute("rspace", "0.05em");
    return middleNode;
  }
});

const padding$1 = _ => {
  const node = new mathMLTree.MathNode("mspace");
  node.setAttribute("width", "3pt");
  return node
};

const mathmlBuilder$8 = (group, style) => {
  let node;
  if (group.label.indexOf("colorbox") > -1 || group.label === "\\boxed") {
    // MathML core does not support +width attribute in <mpadded>.
    // Firefox does not reliably add side padding.
    // Insert <mspace>
    node = new mathMLTree.MathNode("mrow", [
      padding$1(),
      buildGroup$1(group.body, style),
      padding$1()
    ]);
  } else {
    node = new mathMLTree.MathNode("menclose", [buildGroup$1(group.body, style)]);
  }
  switch (group.label) {
    case "\\overline":
      node.setAttribute("notation", "top"); // for Firefox & WebKit
      node.classes.push("tml-overline");    // for Chromium
      break
    case "\\underline":
      node.setAttribute("notation", "bottom");
      node.classes.push("tml-underline");
      break
    case "\\cancel":
      node.setAttribute("notation", "updiagonalstrike");
      node.children.push(new mathMLTree.MathNode("mrow", [], ["tml-cancel", "upstrike"]));
      break
    case "\\bcancel":
      node.setAttribute("notation", "downdiagonalstrike");
      node.children.push(new mathMLTree.MathNode("mrow", [], ["tml-cancel", "downstrike"]));
      break
    case "\\sout":
      node.setAttribute("notation", "horizontalstrike");
      node.children.push(new mathMLTree.MathNode("mrow", [], ["tml-cancel", "sout"]));
      break
    case "\\xcancel":
      node.setAttribute("notation", "updiagonalstrike downdiagonalstrike");
      node.classes.push("tml-xcancel");
      break
    case "\\longdiv":
      node.setAttribute("notation", "longdiv");
      node.classes.push("longdiv-top");
      node.children.push(new mathMLTree.MathNode("mrow", [], ["longdiv-arc"]));
      break
    case "\\phase":
      node.setAttribute("notation", "phasorangle");
      node.classes.push("phasor-bottom");
      node.children.push(new mathMLTree.MathNode("mrow", [], ["phasor-angle"]));
      break
    case "\\textcircled":
      node.setAttribute("notation", "circle");
      node.classes.push("circle-pad");
      node.children.push(new mathMLTree.MathNode("mrow", [], ["textcircle"]));
      break
    case "\\angl":
      node.setAttribute("notation", "actuarial");
      node.classes.push("actuarial");
      break
    case "\\boxed":
      // \newcommand{\boxed}[1]{\fbox{\m@th$\displaystyle#1$}} from amsmath.sty
      node.setAttribute("notation", "box");
      node.classes.push("tml-box");
      node.setAttribute("scriptlevel", "0");
      node.setAttribute("displaystyle", "true");
      break
    case "\\fbox":
      node.setAttribute("notation", "box");
      node.classes.push("tml-fbox");
      break
    case "\\fcolorbox":
    case "\\colorbox": {
      // <menclose> doesn't have a good notation option for \colorbox.
      // So use <mpadded> instead. Set some attributes that come
      // included with <menclose>.
      //const fboxsep = 3; // 3 pt from LaTeX source2e
      //node.setAttribute("height", `+${2 * fboxsep}pt`)
      //node.setAttribute("voffset", `${fboxsep}pt`)
      const style = { padding: "3pt 0 3pt 0" };

      if (group.label === "\\fcolorbox") {
        style.border = "0.0667em solid " + String(group.borderColor);
      }
      node.style = style;
      break
    }
  }
  if (group.backgroundColor) {
    node.setAttribute("mathbackground", group.backgroundColor);
  }
  return node;
};

defineFunction({
  type: "enclose",
  names: ["\\colorbox"],
  props: {
    numArgs: 2,
    numOptionalArgs: 1,
    allowedInText: true,
    argTypes: ["raw", "raw", "text"]
  },
  handler({ parser, funcName }, args, optArgs) {
    const model = optArgs[0] && assertNodeType(optArgs[0], "raw").string;
    let color = "";
    if (model) {
      const spec = assertNodeType(args[0], "raw").string;
      color = colorFromSpec(model, spec);
    } else {
      color = validateColor(assertNodeType(args[0], "raw").string, parser.gullet.macros);
    }
    const body = args[1];
    return {
      type: "enclose",
      mode: parser.mode,
      label: funcName,
      backgroundColor: color,
      body
    };
  },
  mathmlBuilder: mathmlBuilder$8
});

defineFunction({
  type: "enclose",
  names: ["\\fcolorbox"],
  props: {
    numArgs: 3,
    numOptionalArgs: 1,
    allowedInText: true,
    argTypes: ["raw", "raw", "raw", "text"]
  },
  handler({ parser, funcName }, args, optArgs) {
    const model = optArgs[0] && assertNodeType(optArgs[0], "raw").string;
    let borderColor = "";
    let backgroundColor;
    if (model) {
      const borderSpec = assertNodeType(args[0], "raw").string;
      const backgroundSpec = assertNodeType(args[0], "raw").string;
      borderColor = colorFromSpec(model, borderSpec);
      backgroundColor = colorFromSpec(model, backgroundSpec);
    } else {
      borderColor = validateColor(assertNodeType(args[0], "raw").string, parser.gullet.macros);
      backgroundColor = validateColor(assertNodeType(args[1], "raw").string, parser.gullet.macros);
    }
    const body = args[2];
    return {
      type: "enclose",
      mode: parser.mode,
      label: funcName,
      backgroundColor,
      borderColor,
      body
    };
  },
  mathmlBuilder: mathmlBuilder$8
});

defineFunction({
  type: "enclose",
  names: ["\\fbox"],
  props: {
    numArgs: 1,
    argTypes: ["hbox"],
    allowedInText: true
  },
  handler({ parser }, args) {
    return {
      type: "enclose",
      mode: parser.mode,
      label: "\\fbox",
      body: args[0]
    };
  }
});

defineFunction({
  type: "enclose",
  names: ["\\angl", "\\cancel", "\\bcancel", "\\xcancel", "\\sout", "\\overline",
    "\\boxed", "\\longdiv", "\\phase"],
  props: {
    numArgs: 1
  },
  handler({ parser, funcName }, args) {
    const body = args[0];
    return {
      type: "enclose",
      mode: parser.mode,
      label: funcName,
      body
    };
  },
  mathmlBuilder: mathmlBuilder$8
});

defineFunction({
  type: "enclose",
  names: ["\\underline"],
  props: {
    numArgs: 1,
    allowedInText: true
  },
  handler({ parser, funcName }, args) {
    const body = args[0];
    return {
      type: "enclose",
      mode: parser.mode,
      label: funcName,
      body
    };
  },
  mathmlBuilder: mathmlBuilder$8
});


defineFunction({
  type: "enclose",
  names: ["\\textcircled"],
  props: {
    numArgs: 1,
    argTypes: ["text"],
    allowedInArgument: true,
    allowedInText: true
  },
  handler({ parser, funcName }, args) {
    const body = args[0];
    return {
      type: "enclose",
      mode: parser.mode,
      label: funcName,
      body
    };
  },
  mathmlBuilder: mathmlBuilder$8
});

/**
 * All registered environments.
 * `environments.js` exports this same dictionary again and makes it public.
 * `Parser.js` requires this dictionary via `environments.js`.
 */
const _environments = {};

function defineEnvironment({ type, names, props, handler, mathmlBuilder }) {
  // Set default values of environments.
  const data = {
    type,
    numArgs: props.numArgs || 0,
    allowedInText: false,
    numOptionalArgs: 0,
    handler
  };
  for (let i = 0; i < names.length; ++i) {
    _environments[names[i]] = data;
  }
  if (mathmlBuilder) {
    _mathmlGroupBuilders[type] = mathmlBuilder;
  }
}

/**
 * Lexing or parsing positional information for error reporting.
 * This object is immutable.
 */
class SourceLocation {
  constructor(lexer, start, end) {
    this.lexer = lexer; // Lexer holding the input string.
    this.start = start; // Start offset, zero-based inclusive.
    this.end = end;     // End offset, zero-based exclusive.
  }

  /**
   * Merges two `SourceLocation`s from location providers, given they are
   * provided in order of appearance.
   * - Returns the first one's location if only the first is provided.
   * - Returns a merged range of the first and the last if both are provided
   *   and their lexers match.
   * - Otherwise, returns null.
   */
  static range(first, second) {
    if (!second) {
      return first && first.loc;
    } else if (!first || !first.loc || !second.loc || first.loc.lexer !== second.loc.lexer) {
      return null;
    } else {
      return new SourceLocation(first.loc.lexer, first.loc.start, second.loc.end);
    }
  }
}

/**
 * Interface required to break circular dependency between Token, Lexer, and
 * ParseError.
 */

/**
 * The resulting token returned from `lex`.
 *
 * It consists of the token text plus some position information.
 * The position information is essentially a range in an input string,
 * but instead of referencing the bare input string, we refer to the lexer.
 * That way it is possible to attach extra metadata to the input string,
 * like for example a file name or similar.
 *
 * The position information is optional, so it is OK to construct synthetic
 * tokens if appropriate. Not providing available position information may
 * lead to degraded error reporting, though.
 */
class Token {
  constructor(
    text, // the text of this token
    loc
  ) {
    this.text = text;
    this.loc = loc;
  }

  /**
   * Given a pair of tokens (this and endToken), compute a `Token` encompassing
   * the whole input range enclosed by these two.
   */
  range(
    endToken, // last token of the range, inclusive
    text // the text of the newly constructed token
  ) {
    return new Token(text, SourceLocation.range(this, endToken));
  }
}

// In TeX, there are actually three sets of dimensions, one for each of
// textstyle, scriptstyle, and scriptscriptstyle.  These are
// provided in the the arrays below, in that order.
//

// Math style is not quite the same thing as script level.
const StyleLevel = {
  DISPLAY: 0,
  TEXT: 1,
  SCRIPT: 2,
  SCRIPTSCRIPT: 3
};

/**
 * All registered global/built-in macros.
 * `macros.js` exports this same dictionary again and makes it public.
 * `Parser.js` requires this dictionary via `macros.js`.
 */
const _macros = {};

// This function might one day accept an additional argument and do more things.
function defineMacro(name, body) {
  _macros[name] = body;
}

/**
 * Predefined macros for Temml.
 * This can be used to define some commands in terms of others.
 */

const macros = _macros;

//////////////////////////////////////////////////////////////////////
// macro tools

defineMacro("\\noexpand", function(context) {
  // The expansion is the token itself; but that token is interpreted
  // as if its meaning were ‘\relax’ if it is a control sequence that
  // would ordinarily be expanded by TeX’s expansion rules.
  const t = context.popToken();
  if (context.isExpandable(t.text)) {
    t.noexpand = true;
    t.treatAsRelax = true;
  }
  return { tokens: [t], numArgs: 0 };
});

defineMacro("\\expandafter", function(context) {
  // TeX first reads the token that comes immediately after \expandafter,
  // without expanding it; let’s call this token t. Then TeX reads the
  // token that comes after t (and possibly more tokens, if that token
  // has an argument), replacing it by its expansion. Finally TeX puts
  // t back in front of that expansion.
  const t = context.popToken();
  context.expandOnce(true); // expand only an expandable token
  return { tokens: [t], numArgs: 0 };
});

// LaTeX's \@firstoftwo{#1}{#2} expands to #1, skipping #2
// TeX source: \long\def\@firstoftwo#1#2{#1}
defineMacro("\\@firstoftwo", function(context) {
  const args = context.consumeArgs(2);
  return { tokens: args[0], numArgs: 0 };
});

// LaTeX's \@secondoftwo{#1}{#2} expands to #2, skipping #1
// TeX source: \long\def\@secondoftwo#1#2{#2}
defineMacro("\\@secondoftwo", function(context) {
  const args = context.consumeArgs(2);
  return { tokens: args[1], numArgs: 0 };
});

// LaTeX's \@ifnextchar{#1}{#2}{#3} looks ahead to the next (unexpanded)
// symbol that isn't a space, consuming any spaces but not consuming the
// first nonspace character.  If that nonspace character matches #1, then
// the macro expands to #2; otherwise, it expands to #3.
defineMacro("\\@ifnextchar", function(context) {
  const args = context.consumeArgs(3); // symbol, if, else
  context.consumeSpaces();
  const nextToken = context.future();
  if (args[0].length === 1 && args[0][0].text === nextToken.text) {
    return { tokens: args[1], numArgs: 0 };
  } else {
    return { tokens: args[2], numArgs: 0 };
  }
});

// LaTeX's \@ifstar{#1}{#2} looks ahead to the next (unexpanded) symbol.
// If it is `*`, then it consumes the symbol, and the macro expands to #1;
// otherwise, the macro expands to #2 (without consuming the symbol).
// TeX source: \def\@ifstar#1{\@ifnextchar *{\@firstoftwo{#1}}}
defineMacro("\\@ifstar", "\\@ifnextchar *{\\@firstoftwo{#1}}");

// LaTeX's \TextOrMath{#1}{#2} expands to #1 in text mode, #2 in math mode
defineMacro("\\TextOrMath", function(context) {
  const args = context.consumeArgs(2);
  if (context.mode === "text") {
    return { tokens: args[0], numArgs: 0 };
  } else {
    return { tokens: args[1], numArgs: 0 };
  }
});

const stringFromArg = arg => {
  // Reverse the order of the arg and return a string.
  let str = "";
  for (let i = arg.length - 1; i > -1; i--) {
    str += arg[i].text;
  }
  return str
};

// Lookup table for parsing numbers in base 8 through 16
const digitToNumber = {
  0: 0,
  1: 1,
  2: 2,
  3: 3,
  4: 4,
  5: 5,
  6: 6,
  7: 7,
  8: 8,
  9: 9,
  a: 10,
  A: 10,
  b: 11,
  B: 11,
  c: 12,
  C: 12,
  d: 13,
  D: 13,
  e: 14,
  E: 14,
  f: 15,
  F: 15
};

const nextCharNumber = context => {
  const numStr = context.future().text;
  if (numStr === "EOF") { return [null, ""] }
  return [digitToNumber[numStr.charAt(0)], numStr]
};

const appendCharNumbers = (number, numStr, base) => {
  for (let i = 1; i < numStr.length; i++) {
    const digit = digitToNumber[numStr.charAt(i)];
    number *= base;
    number += digit;
  }
  return number
};

// TeX \char makes a literal character (catcode 12) using the following forms:
// (see The TeXBook, p. 43)
//   \char123  -- decimal
//   \char'123 -- octal
//   \char"123 -- hex
//   \char`x   -- character that can be written (i.e. isn't active)
//   \char`\x  -- character that cannot be written (e.g. %)
// These all refer to characters from the font, so we turn them into special
// calls to a function \@char dealt with in the Parser.
defineMacro("\\char", function(context) {
  let token = context.popToken();
  let base;
  let number = "";
  if (token.text === "'") {
    base = 8;
    token = context.popToken();
  } else if (token.text === '"') {
    base = 16;
    token = context.popToken();
  } else if (token.text === "`") {
    token = context.popToken();
    if (token.text[0] === "\\") {
      number = token.text.charCodeAt(1);
    } else if (token.text === "EOF") {
      throw new ParseError("\\char` missing argument");
    } else {
      number = token.text.charCodeAt(0);
    }
  } else {
    base = 10;
  }
  if (base) {
    // Parse a number in the given base, starting with first `token`.
    let numStr = token.text;
    number = digitToNumber[numStr.charAt(0)];
    if (number == null || number >= base) {
      throw new ParseError(`Invalid base-${base} digit ${token.text}`);
    }
    number = appendCharNumbers(number, numStr, base);
    let digit;
    [digit, numStr] = nextCharNumber(context);
    while (digit != null && digit < base) {
      number *= base;
      number += digit;
      number = appendCharNumbers(number, numStr, base);
      context.popToken();
      [digit, numStr] = nextCharNumber(context);
    }
  }
  return `\\@char{${number}}`;
});

function recreateArgStr(context) {
  // Recreate the macro's original argument string from the array of parse tokens.
  const tokens = context.consumeArgs(1)[0];
  let str = "";
  let expectedLoc = tokens[tokens.length - 1].loc.start;
  for (let i = tokens.length - 1; i >= 0; i--) {
    const actualLoc = tokens[i].loc.start;
    if (actualLoc > expectedLoc) {
      // context.consumeArgs has eaten a space.
      str += " ";
      expectedLoc = actualLoc;
    }
    str += tokens[i].text;
    expectedLoc += tokens[i].text.length;
  }
  return str
}

// The Latin Modern font renders <mi>√</mi> at the wrong vertical alignment.
// This macro provides a better rendering.
defineMacro("\\surd", '\\sqrt{\\vphantom{|}}');

// See comment for \oplus in symbols.js.
defineMacro("\u2295", "\\oplus");

// Since Temml has no \par, ignore \long.
defineMacro("\\long", "");

//////////////////////////////////////////////////////////////////////
// Grouping
// \let\bgroup={ \let\egroup=}
defineMacro("\\bgroup", "{");
defineMacro("\\egroup", "}");

// Symbols from latex.ltx:
// \def~{\nobreakspace{}}
// \def\lq{`}
// \def\rq{'}
// \def \aa {\r a}
defineMacro("~", "\\nobreakspace");
defineMacro("\\lq", "`");
defineMacro("\\rq", "'");
defineMacro("\\aa", "\\r a");

defineMacro("\\Bbbk", "\\Bbb{k}");

// \mathstrut from the TeXbook, p 360
defineMacro("\\mathstrut", "\\vphantom{(}");

// \underbar from TeXbook p 353
defineMacro("\\underbar", "\\underline{\\text{#1}}");

//////////////////////////////////////////////////////////////////////
// LaTeX_2ε

// \vdots{\vbox{\baselineskip4\p@  \lineskiplimit\z@
// \kern6\p@\hbox{.}\hbox{.}\hbox{.}}}
// We'll call \varvdots, which gets a glyph from symbols.js.
// The zero-width rule gets us an equivalent to the vertical 6pt kern.
defineMacro("\\vdots", "{\\varvdots\\rule{0pt}{15pt}}");
defineMacro("\u22ee", "\\vdots");

// {array} environment gaps
defineMacro("\\arraystretch", "1");     // line spacing factor times 12pt
defineMacro("\\arraycolsep", "6pt");    // half the width separating columns

//////////////////////////////////////////////////////////////////////
// amsmath.sty
// http://mirrors.concertpass.com/tex-archive/macros/latex/required/amsmath/amsmath.pdf

//\newcommand{\substack}[1]{\subarray{c}#1\endsubarray}
defineMacro("\\substack", "\\begin{subarray}{c}#1\\end{subarray}");

// \def\iff{\DOTSB\;\Longleftrightarrow\;}
// \def\implies{\DOTSB\;\Longrightarrow\;}
// \def\impliedby{\DOTSB\;\Longleftarrow\;}
defineMacro("\\iff", "\\DOTSB\\;\\Longleftrightarrow\\;");
defineMacro("\\implies", "\\DOTSB\\;\\Longrightarrow\\;");
defineMacro("\\impliedby", "\\DOTSB\\;\\Longleftarrow\\;");

// AMSMath's automatic \dots, based on \mdots@@ macro.
const dotsByToken = {
  ",": "\\dotsc",
  "\\not": "\\dotsb",
  // \keybin@ checks for the following:
  "+": "\\dotsb",
  "=": "\\dotsb",
  "<": "\\dotsb",
  ">": "\\dotsb",
  "-": "\\dotsb",
  "*": "\\dotsb",
  ":": "\\dotsb",
  // Symbols whose definition starts with \DOTSB:
  "\\DOTSB": "\\dotsb",
  "\\coprod": "\\dotsb",
  "\\bigvee": "\\dotsb",
  "\\bigwedge": "\\dotsb",
  "\\biguplus": "\\dotsb",
  "\\bigcap": "\\dotsb",
  "\\bigcup": "\\dotsb",
  "\\prod": "\\dotsb",
  "\\sum": "\\dotsb",
  "\\bigotimes": "\\dotsb",
  "\\bigoplus": "\\dotsb",
  "\\bigodot": "\\dotsb",
  "\\bigsqcap": "\\dotsb",
  "\\bigsqcup": "\\dotsb",
  "\\bigtimes": "\\dotsb",
  "\\And": "\\dotsb",
  "\\longrightarrow": "\\dotsb",
  "\\Longrightarrow": "\\dotsb",
  "\\longleftarrow": "\\dotsb",
  "\\Longleftarrow": "\\dotsb",
  "\\longleftrightarrow": "\\dotsb",
  "\\Longleftrightarrow": "\\dotsb",
  "\\mapsto": "\\dotsb",
  "\\longmapsto": "\\dotsb",
  "\\hookrightarrow": "\\dotsb",
  "\\doteq": "\\dotsb",
  // Symbols whose definition starts with \mathbin:
  "\\mathbin": "\\dotsb",
  // Symbols whose definition starts with \mathrel:
  "\\mathrel": "\\dotsb",
  "\\relbar": "\\dotsb",
  "\\Relbar": "\\dotsb",
  "\\xrightarrow": "\\dotsb",
  "\\xleftarrow": "\\dotsb",
  // Symbols whose definition starts with \DOTSI:
  "\\DOTSI": "\\dotsi",
  "\\int": "\\dotsi",
  "\\oint": "\\dotsi",
  "\\iint": "\\dotsi",
  "\\iiint": "\\dotsi",
  "\\iiiint": "\\dotsi",
  "\\idotsint": "\\dotsi",
  // Symbols whose definition starts with \DOTSX:
  "\\DOTSX": "\\dotsx"
};

defineMacro("\\dots", function(context) {
  // TODO: If used in text mode, should expand to \textellipsis.
  // However, in Temml, \textellipsis and \ldots behave the same
  // (in text mode), and it's unlikely we'd see any of the math commands
  // that affect the behavior of \dots when in text mode.  So fine for now
  // (until we support \ifmmode ... \else ... \fi).
  let thedots = "\\dotso";
  const next = context.expandAfterFuture().text;
  if (next in dotsByToken) {
    thedots = dotsByToken[next];
  } else if (next.slice(0, 4) === "\\not") {
    thedots = "\\dotsb";
  } else if (next in symbols.math) {
    if (["bin", "rel"].includes(symbols.math[next].group)) {
      thedots = "\\dotsb";
    }
  }
  return thedots;
});

const spaceAfterDots = {
  // \rightdelim@ checks for the following:
  ")": true,
  "]": true,
  "\\rbrack": true,
  "\\}": true,
  "\\rbrace": true,
  "\\rangle": true,
  "\\rceil": true,
  "\\rfloor": true,
  "\\rgroup": true,
  "\\rmoustache": true,
  "\\right": true,
  "\\bigr": true,
  "\\biggr": true,
  "\\Bigr": true,
  "\\Biggr": true,
  // \extra@ also tests for the following:
  $: true,
  // \extrap@ checks for the following:
  ";": true,
  ".": true,
  ",": true
};

defineMacro("\\dotso", function(context) {
  const next = context.future().text;
  if (next in spaceAfterDots) {
    return "\\ldots\\,";
  } else {
    return "\\ldots";
  }
});

defineMacro("\\dotsc", function(context) {
  const next = context.future().text;
  // \dotsc uses \extra@ but not \extrap@, instead specially checking for
  // ';' and '.', but doesn't check for ','.
  if (next in spaceAfterDots && next !== ",") {
    return "\\ldots\\,";
  } else {
    return "\\ldots";
  }
});

defineMacro("\\cdots", function(context) {
  const next = context.future().text;
  if (next in spaceAfterDots) {
    return "\\@cdots\\,";
  } else {
    return "\\@cdots";
  }
});

defineMacro("\\dotsb", "\\cdots");
defineMacro("\\dotsm", "\\cdots");
defineMacro("\\dotsi", "\\!\\cdots");
defineMacro("\\idotsint", "\\dotsi");
// amsmath doesn't actually define \dotsx, but \dots followed by a macro
// starting with \DOTSX implies \dotso, and then \extra@ detects this case
// and forces the added `\,`.
defineMacro("\\dotsx", "\\ldots\\,");

// \let\DOTSI\relax
// \let\DOTSB\relax
// \let\DOTSX\relax
defineMacro("\\DOTSI", "\\relax");
defineMacro("\\DOTSB", "\\relax");
defineMacro("\\DOTSX", "\\relax");

// Spacing, based on amsmath.sty's override of LaTeX defaults
// \DeclareRobustCommand{\tmspace}[3]{%
//   \ifmmode\mskip#1#2\else\kern#1#3\fi\relax}
defineMacro("\\tmspace", "\\TextOrMath{\\kern#1#3}{\\mskip#1#2}\\relax");
// \renewcommand{\,}{\tmspace+\thinmuskip{.1667em}}
// TODO: math mode should use \thinmuskip
defineMacro("\\,", "{\\tmspace+{3mu}{.1667em}}");
// \let\thinspace\,
defineMacro("\\thinspace", "\\,");
// \def\>{\mskip\medmuskip}
// \renewcommand{\:}{\tmspace+\medmuskip{.2222em}}
// TODO: \> and math mode of \: should use \medmuskip = 4mu plus 2mu minus 4mu
defineMacro("\\>", "\\mskip{4mu}");
defineMacro("\\:", "{\\tmspace+{4mu}{.2222em}}");
// \let\medspace\:
defineMacro("\\medspace", "\\:");
// \renewcommand{\;}{\tmspace+\thickmuskip{.2777em}}
// TODO: math mode should use \thickmuskip = 5mu plus 5mu
defineMacro("\\;", "{\\tmspace+{5mu}{.2777em}}");
// \let\thickspace\;
defineMacro("\\thickspace", "\\;");
// \renewcommand{\!}{\tmspace-\thinmuskip{.1667em}}
// TODO: math mode should use \thinmuskip
defineMacro("\\!", "{\\tmspace-{3mu}{.1667em}}");
// \let\negthinspace\!
defineMacro("\\negthinspace", "\\!");
// \newcommand{\negmedspace}{\tmspace-\medmuskip{.2222em}}
// TODO: math mode should use \medmuskip
defineMacro("\\negmedspace", "{\\tmspace-{4mu}{.2222em}}");
// \newcommand{\negthickspace}{\tmspace-\thickmuskip{.2777em}}
// TODO: math mode should use \thickmuskip
defineMacro("\\negthickspace", "{\\tmspace-{5mu}{.277em}}");
// \def\enspace{\kern.5em }
defineMacro("\\enspace", "\\kern.5em ");
// \def\enskip{\hskip.5em\relax}
defineMacro("\\enskip", "\\hskip.5em\\relax");
// \def\quad{\hskip1em\relax}
defineMacro("\\quad", "\\hskip1em\\relax");
// \def\qquad{\hskip2em\relax}
defineMacro("\\qquad", "\\hskip2em\\relax");

defineMacro("\\AA", "\\TextOrMath{\\Angstrom}{\\mathring{A}}\\relax");

// \tag@in@display form of \tag
defineMacro("\\tag", "\\@ifstar\\tag@literal\\tag@paren");
defineMacro("\\tag@paren", "\\tag@literal{({#1})}");
defineMacro("\\tag@literal", (context) => {
  if (context.macros.get("\\df@tag")) {
    throw new ParseError("Multiple \\tag");
  }
  return "\\gdef\\df@tag{\\text{#1}}";
});
defineMacro("\\notag", "\\nonumber");
defineMacro("\\nonumber", "\\gdef\\@eqnsw{0}");

// \renewcommand{\bmod}{\nonscript\mskip-\medmuskip\mkern5mu\mathbin
//   {\operator@font mod}\penalty900
//   \mkern5mu\nonscript\mskip-\medmuskip}
// \newcommand{\pod}[1]{\allowbreak
//   \if@display\mkern18mu\else\mkern8mu\fi(#1)}
// \renewcommand{\pmod}[1]{\pod{{\operator@font mod}\mkern6mu#1}}
// \newcommand{\mod}[1]{\allowbreak\if@display\mkern18mu
//   \else\mkern12mu\fi{\operator@font mod}\,\,#1}
// TODO: math mode should use \medmuskip = 4mu plus 2mu minus 4mu
defineMacro("\\bmod", "\\mathbin{\\text{mod}}");
defineMacro(
  "\\pod",
  "\\allowbreak" + "\\mathchoice{\\mkern18mu}{\\mkern8mu}{\\mkern8mu}{\\mkern8mu}(#1)"
);
defineMacro("\\pmod", "\\pod{{\\rm mod}\\mkern6mu#1}");
defineMacro(
  "\\mod",
  "\\allowbreak" +
    "\\mathchoice{\\mkern18mu}{\\mkern12mu}{\\mkern12mu}{\\mkern12mu}" +
    "{\\rm mod}\\,\\,#1"
);

//////////////////////////////////////////////////////////////////////
// LaTeX source2e

// \expandafter\let\expandafter\@normalcr
//     \csname\expandafter\@gobble\string\\ \endcsname
// \DeclareRobustCommand\newline{\@normalcr\relax}
defineMacro("\\newline", "\\\\\\relax");

// \def\TeX{T\kern-.1667em\lower.5ex\hbox{E}\kern-.125emX\@}
// TODO: Doesn't normally work in math mode because \@ fails.
defineMacro("\\TeX", "\\textrm{T}\\kern-.1667em\\raisebox{-.5ex}{E}\\kern-.125em\\textrm{X}");

defineMacro(
  "\\LaTeX",
    "\\textrm{L}\\kern-.35em\\raisebox{0.2em}{\\scriptstyle A}\\kern-.15em\\TeX"
);

defineMacro(
  "\\Temml",
  // eslint-disable-next-line max-len
  "\\textrm{T}\\kern-0.2em\\lower{0.2em}{\\textrm{E}}\\kern-0.08em{\\textrm{M}\\kern-0.08em\\raise{0.2em}\\textrm{M}\\kern-0.08em\\textrm{L}}"
);

// \DeclareRobustCommand\hspace{\@ifstar\@hspacer\@hspace}
// \def\@hspace#1{\hskip  #1\relax}
// \def\@hspacer#1{\vrule \@width\z@\nobreak
//                 \hskip #1\hskip \z@skip}
defineMacro("\\hspace", "\\@ifstar\\@hspacer\\@hspace");
defineMacro("\\@hspace", "\\hskip #1\\relax");
defineMacro("\\@hspacer", "\\rule{0pt}{0pt}\\hskip #1\\relax");

defineMacro("\\colon", `\\mathpunct{\\char"3a}`);

//////////////////////////////////////////////////////////////////////
// mathtools.sty

defineMacro("\\prescript", "\\pres@cript{_{#1}^{#2}}{}{#3}");

//\providecommand\ordinarycolon{:}
defineMacro("\\ordinarycolon", `\\char"3a`);
// Raise to center on the math axis, as closely as possible.
defineMacro("\\vcentcolon", "\\mathrel{\\raisebox{0.035em}{\\ordinarycolon}}");
// \providecommand*\coloneq{\vcentcolon\mathrel{\mkern-1.2mu}\mathrel{-}}
defineMacro("\\coloneq", '\\mathrel{\\raisebox{0.035em}{\\ordinarycolon}\\char"2212}');
// \providecommand*\Coloneq{\dblcolon\mathrel{\mkern-1.2mu}\mathrel{-}}
defineMacro("\\Coloneq", '\\mathrel{\\char"2237\\char"2212}');
// \providecommand*\Eqqcolon{=\mathrel{\mkern-1.2mu}\dblcolon}
defineMacro("\\Eqqcolon", '\\mathrel{\\char"3d\\char"2237}');
// \providecommand*\Eqcolon{\mathrel{-}\mathrel{\mkern-1.2mu}\dblcolon}
defineMacro("\\Eqcolon", '\\mathrel{\\char"2212\\char"2237}');
// \providecommand*\colonapprox{\vcentcolon\mathrel{\mkern-1.2mu}\approx}
defineMacro("\\colonapprox", '\\mathrel{\\raisebox{0.035em}{\\ordinarycolon}\\char"2248}');
// \providecommand*\Colonapprox{\dblcolon\mathrel{\mkern-1.2mu}\approx}
defineMacro("\\Colonapprox", '\\mathrel{\\char"2237\\char"2248}');
// \providecommand*\colonsim{\vcentcolon\mathrel{\mkern-1.2mu}\sim}
defineMacro("\\colonsim", '\\mathrel{\\raisebox{0.035em}{\\ordinarycolon}\\char"223c}');
// \providecommand*\Colonsim{\dblcolon\mathrel{\mkern-1.2mu}\sim}
defineMacro("\\Colonsim", '\\mathrel{\\raisebox{0.035em}{\\ordinarycolon}\\char"223c}');

//////////////////////////////////////////////////////////////////////
// colonequals.sty

// Alternate names for mathtools's macros:
defineMacro("\\ratio", "\\vcentcolon");
defineMacro("\\coloncolon", "\\dblcolon");
defineMacro("\\colonequals", "\\coloneqq");
defineMacro("\\coloncolonequals", "\\Coloneqq");
defineMacro("\\equalscolon", "\\eqqcolon");
defineMacro("\\equalscoloncolon", "\\Eqqcolon");
defineMacro("\\colonminus", "\\coloneq");
defineMacro("\\coloncolonminus", "\\Coloneq");
defineMacro("\\minuscolon", "\\eqcolon");
defineMacro("\\minuscoloncolon", "\\Eqcolon");
// \colonapprox name is same in mathtools and colonequals.
defineMacro("\\coloncolonapprox", "\\Colonapprox");
// \colonsim name is same in mathtools and colonequals.
defineMacro("\\coloncolonsim", "\\Colonsim");

// Present in newtxmath, pxfonts and txfonts
defineMacro("\\notni", "\\mathrel{\\char`\u220C}");
defineMacro("\\limsup", "\\DOTSB\\operatorname*{lim\\,sup}");
defineMacro("\\liminf", "\\DOTSB\\operatorname*{lim\\,inf}");

//////////////////////////////////////////////////////////////////////
// From amsopn.sty
defineMacro("\\injlim", "\\DOTSB\\operatorname*{inj\\,lim}");
defineMacro("\\projlim", "\\DOTSB\\operatorname*{proj\\,lim}");
defineMacro("\\varlimsup", "\\DOTSB\\operatorname*{\\overline{\\text{lim}}}");
defineMacro("\\varliminf", "\\DOTSB\\operatorname*{\\underline{\\text{lim}}}");
defineMacro("\\varinjlim", "\\DOTSB\\operatorname*{\\underrightarrow{\\text{lim}}}");
defineMacro("\\varprojlim", "\\DOTSB\\operatorname*{\\underleftarrow{\\text{lim}}}");

defineMacro("\\centerdot", "{\\medspace\\rule{0.167em}{0.189em}\\medspace}");

//////////////////////////////////////////////////////////////////////
// statmath.sty
// https://ctan.math.illinois.edu/macros/latex/contrib/statmath/statmath.pdf

defineMacro("\\argmin", "\\DOTSB\\operatorname*{arg\\,min}");
defineMacro("\\argmax", "\\DOTSB\\operatorname*{arg\\,max}");
defineMacro("\\plim", "\\DOTSB\\operatorname*{plim}");

//////////////////////////////////////////////////////////////////////
// MnSymbol.sty

defineMacro("\\leftmodels", "\\mathop{\\reflectbox{$\\models$}}");

//////////////////////////////////////////////////////////////////////
// braket.sty
// http://ctan.math.washington.edu/tex-archive/macros/latex/contrib/braket/braket.pdf

defineMacro("\\bra", "\\mathinner{\\langle{#1}|}");
defineMacro("\\ket", "\\mathinner{|{#1}\\rangle}");
defineMacro("\\braket", "\\mathinner{\\langle{#1}\\rangle}");
defineMacro("\\Bra", "\\left\\langle#1\\right|");
defineMacro("\\Ket", "\\left|#1\\right\\rangle");
// A helper for \Braket and \Set
const replaceVert = (argStr, match) => {
  const ch = match[0] === "|" ? "\\vert" : "\\Vert";
  const replaceStr = `}\\,\\middle${ch}\\,{`;
  return argStr.slice(0, match.index) + replaceStr + argStr.slice(match.index + match[0].length)
};
defineMacro("\\Braket",  function(context) {
  let argStr = recreateArgStr(context);
  const regEx = /\|\||\||\\\|/g;
  let match;
  while ((match = regEx.exec(argStr)) !== null) {
    argStr = replaceVert(argStr, match);
  }
  return "\\left\\langle{" + argStr + "}\\right\\rangle"
});
defineMacro("\\Set",  function(context) {
  let argStr = recreateArgStr(context);
  const match = /\|\||\||\\\|/.exec(argStr);
  if (match) {
    argStr = replaceVert(argStr, match);
  }
  return "\\left\\{\\:{" + argStr + "}\\:\\right\\}"
});
defineMacro("\\set",  function(context) {
  const argStr = recreateArgStr(context);
  return "\\{{" + argStr.replace(/\|/, "}\\mid{") + "}\\}"
});

//////////////////////////////////////////////////////////////////////
// actuarialangle.dtx
defineMacro("\\angln", "{\\angl n}");

//////////////////////////////////////////////////////////////////////
// derivative.sty
defineMacro("\\odv", "\\@ifstar\\odv@next\\odv@numerator");
defineMacro("\\odv@numerator", "\\frac{\\mathrm{d}#1}{\\mathrm{d}#2}");
defineMacro("\\odv@next", "\\frac{\\mathrm{d}}{\\mathrm{d}#2}#1");
defineMacro("\\pdv", "\\@ifstar\\pdv@next\\pdv@numerator");

const pdvHelper = args => {
  const numerator = args[0][0].text;
  const denoms = stringFromArg(args[1]).split(",");
  const power = String(denoms.length);
  const numOp = power === "1" ? "\\partial" : `\\partial^${power}`;
  let denominator = "";
  denoms.map(e => { denominator += "\\partial " + e.trim() +  "\\,";});
  return [numerator, numOp,  denominator.replace(/\\,$/, "")]
};
defineMacro("\\pdv@numerator", function(context) {
  const [numerator, numOp, denominator] = pdvHelper(context.consumeArgs(2));
  return `\\frac{${numOp} ${numerator}}{${denominator}}`
});
defineMacro("\\pdv@next", function(context) {
  const [numerator, numOp, denominator] = pdvHelper(context.consumeArgs(2));
  return `\\frac{${numOp}}{${denominator}} ${numerator}`
});

//////////////////////////////////////////////////////////////////////
// upgreek.dtx
defineMacro("\\upalpha", "\\up@greek{\\alpha}");
defineMacro("\\upbeta", "\\up@greek{\\beta}");
defineMacro("\\upgamma", "\\up@greek{\\gamma}");
defineMacro("\\updelta", "\\up@greek{\\delta}");
defineMacro("\\upepsilon", "\\up@greek{\\epsilon}");
defineMacro("\\upzeta", "\\up@greek{\\zeta}");
defineMacro("\\upeta", "\\up@greek{\\eta}");
defineMacro("\\uptheta", "\\up@greek{\\theta}");
defineMacro("\\upiota", "\\up@greek{\\iota}");
defineMacro("\\upkappa", "\\up@greek{\\kappa}");
defineMacro("\\uplambda", "\\up@greek{\\lambda}");
defineMacro("\\upmu", "\\up@greek{\\mu}");
defineMacro("\\upnu", "\\up@greek{\\nu}");
defineMacro("\\upxi", "\\up@greek{\\xi}");
defineMacro("\\upomicron", "\\up@greek{\\omicron}");
defineMacro("\\uppi", "\\up@greek{\\pi}");
defineMacro("\\upalpha", "\\up@greek{\\alpha}");
defineMacro("\\uprho", "\\up@greek{\\rho}");
defineMacro("\\upsigma", "\\up@greek{\\sigma}");
defineMacro("\\uptau", "\\up@greek{\\tau}");
defineMacro("\\upupsilon", "\\up@greek{\\upsilon}");
defineMacro("\\upphi", "\\up@greek{\\phi}");
defineMacro("\\upchi", "\\up@greek{\\chi}");
defineMacro("\\uppsi", "\\up@greek{\\psi}");
defineMacro("\\upomega", "\\up@greek{\\omega}");

//////////////////////////////////////////////////////////////////////
// cmll package
defineMacro("\\invamp", '\\mathbin{\\char"214b}');
defineMacro("\\parr", '\\mathbin{\\char"214b}');
defineMacro("\\with", '\\mathbin{\\char"26}');
defineMacro("\\multimapinv", '\\mathrel{\\char"27dc}');
defineMacro("\\multimapboth", '\\mathrel{\\char"29df}');
defineMacro("\\scoh", '{\\mkern5mu\\char"2322\\mkern5mu}');
defineMacro("\\sincoh", '{\\mkern5mu\\char"2323\\mkern5mu}');
defineMacro("\\coh", `{\\mkern5mu\\rule{}{0.7em}\\mathrlap{\\smash{\\raise2mu{\\char"2322}}}
{\\smash{\\lower4mu{\\char"2323}}}\\mkern5mu}`);
defineMacro("\\incoh", `{\\mkern5mu\\rule{}{0.7em}\\mathrlap{\\smash{\\raise2mu{\\char"2323}}}
{\\smash{\\lower4mu{\\char"2322}}}\\mkern5mu}`);


//////////////////////////////////////////////////////////////////////
// chemstyle package
defineMacro("\\standardstate", "\\text{\\tiny\\char`⦵}");

﻿/* eslint-disable */
/* -*- Mode: JavaScript; indent-tabs-mode:nil; js-indent-level: 2 -*- */
/* vim: set ts=2 et sw=2 tw=80: */

/*************************************************************
 *
 *  Temml mhchem.js
 *
 *  This file implements a Temml version of mhchem version 3.3.0.
 *  It is adapted from MathJax/extensions/TeX/mhchem.js
 *  It differs from the MathJax version as follows:
 *    1. The interface is changed so that it can be called from Temml, not MathJax.
 *    2. \rlap and \llap are replaced with \mathrlap and \mathllap.
 *    3. The reaction arrow code is simplified. All reaction arrows are rendered
 *       using Temml extensible arrows instead of building non-extensible arrows.
 *    4. The ~bond forms are composed entirely of \rule elements.
 *    5. Two dashes in _getBond are wrapped in braces to suppress spacing. i.e., {-}
 *    6. The electron dot uses \textbullet instead of \bullet.
 *    7. \smash[T] has been removed. (WebKit hides anything inside \smash{…})
 *
 *    This code, as other Temml code, is released under the MIT license.
 * 
 * /*************************************************************
 *
 *  MathJax/extensions/TeX/mhchem.js
 *
 *  Implements the \ce command for handling chemical formulas
 *  from the mhchem LaTeX package.
 *
 *  ---------------------------------------------------------------------
 *
 *  Copyright (c) 2011-2015 The MathJax Consortium
 *  Copyright (c) 2015-2018 Martin Hensel
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 */

//
// Coding Style
//   - use '' for identifiers that can by minified/uglified
//   - use "" for strings that need to stay untouched

// version: "3.3.0" for MathJax and Temml


// Add \ce, \pu, and \tripleDash to the Temml macros.

defineMacro("\\ce", function(context) {
  return chemParse(context.consumeArgs(1)[0], "ce")
});

defineMacro("\\pu", function(context) {
  return chemParse(context.consumeArgs(1)[0], "pu");
});

// Math fonts do not include glyphs for the ~ form of bonds. So we'll send path geometry
// So we'll compose characters built from \rule elements.
defineMacro("\\uniDash", `{\\rule{0.672em}{0.06em}}`)
defineMacro("\\triDash", `{\\rule{0.15em}{0.06em}\\kern2mu\\rule{0.15em}{0.06em}\\kern2mu\\rule{0.15em}{0.06em}}`)
defineMacro("\\tripleDash", `\\kern0.075em\\raise0.25em{\\triDash}\\kern0.075em`)
defineMacro("\\tripleDashOverLine", `\\kern0.075em\\mathrlap{\\raise0.125em{\\uniDash}}\\raise0.34em{\\triDash}\\kern0.075em`)
defineMacro("\\tripleDashOverDoubleLine", `\\kern0.075em\\mathrlap{\\mathrlap{\\raise0.48em{\\triDash}}\\raise0.27em{\\uniDash}}{\\raise0.05em{\\uniDash}}\\kern0.075em`)
defineMacro("\\tripleDashBetweenDoubleLine", `\\kern0.075em\\mathrlap{\\mathrlap{\\raise0.48em{\\uniDash}}\\raise0.27em{\\triDash}}{\\raise0.05em{\\uniDash}}\\kern0.075em`)

  //
  //  This is the main function for handing the \ce and \pu commands.
  //  It takes the argument to \ce or \pu and returns the corresponding TeX string.
  //

  var chemParse = function (tokens, stateMachine) {
    // Recreate the argument string from Temml's array of tokens.
    var str = "";
    var expectedLoc = tokens.length && tokens[tokens.length - 1].loc.start
    for (var i = tokens.length - 1; i >= 0; i--) {
      if(tokens[i].loc.start > expectedLoc) {
        // context.consumeArgs has eaten a space.
        str += " ";
        expectedLoc = tokens[i].loc.start;
      }
      str += tokens[i].text;
      expectedLoc += tokens[i].text.length;
    }
    // Call the mhchem core parser.
    var tex = texify.go(mhchemParser.go(str, stateMachine));
    return tex;
  };

  //
  // Core parser for mhchem syntax  (recursive)
  //
  /** @type {MhchemParser} */
  var mhchemParser = {
    //
    // Parses mchem \ce syntax
    //
    // Call like
    //   go("H2O");
    //
    go: function (input, stateMachine) {
      if (!input) { return []; }
      if (stateMachine === undefined) { stateMachine = 'ce'; }
      var state = '0';

      //
      // String buffers for parsing:
      //
      // buffer.a == amount
      // buffer.o == element
      // buffer.b == left-side superscript
      // buffer.p == left-side subscript
      // buffer.q == right-side subscript
      // buffer.d == right-side superscript
      //
      // buffer.r == arrow
      // buffer.rdt == arrow, script above, type
      // buffer.rd == arrow, script above, content
      // buffer.rqt == arrow, script below, type
      // buffer.rq == arrow, script below, content
      //
      // buffer.text_
      // buffer.rm
      // etc.
      //
      // buffer.parenthesisLevel == int, starting at 0
      // buffer.sb == bool, space before
      // buffer.beginsWithBond == bool
      //
      // These letters are also used as state names.
      //
      // Other states:
      // 0 == begin of main part (arrow/operator unlikely)
      // 1 == next entity
      // 2 == next entity (arrow/operator unlikely)
      // 3 == next atom
      // c == macro
      //
      /** @type {Buffer} */
      var buffer = {};
      buffer['parenthesisLevel'] = 0;

      input = input.replace(/\n/g, " ");
      input = input.replace(/[\u2212\u2013\u2014\u2010]/g, "-");
      input = input.replace(/[\u2026]/g, "...");

      //
      // Looks through mhchemParser.transitions, to execute a matching action
      // (recursive)
      //
      var lastInput;
      var watchdog = 10;
      /** @type {ParserOutput[]} */
      var output = [];
      while (true) {
        if (lastInput !== input) {
          watchdog = 10;
          lastInput = input;
        } else {
          watchdog--;
        }
        //
        // Find actions in transition table
        //
        var machine = mhchemParser.stateMachines[stateMachine];
        var t = machine.transitions[state] || machine.transitions['*'];
        iterateTransitions:
        for (var i=0; i<t.length; i++) {
          var matches = mhchemParser.patterns.match_(t[i].pattern, input);
          if (matches) {
            //
            // Execute actions
            //
            var task = t[i].task;
            for (var iA=0; iA<task.action_.length; iA++) {
              var o;
              //
              // Find and execute action
              //
              if (machine.actions[task.action_[iA].type_]) {
                o = machine.actions[task.action_[iA].type_](buffer, matches.match_, task.action_[iA].option);
              } else if (mhchemParser.actions[task.action_[iA].type_]) {
                o = mhchemParser.actions[task.action_[iA].type_](buffer, matches.match_, task.action_[iA].option);
              } else {
                throw ["MhchemBugA", "mhchem bug A. Please report. (" + task.action_[iA].type_ + ")"];  // Trying to use non-existing action
              }
              //
              // Add output
              //
              mhchemParser.concatArray(output, o);
            }
            //
            // Set next state,
            // Shorten input,
            // Continue with next character
            //   (= apply only one transition per position)
            //
            state = task.nextState || state;
            if (input.length > 0) {
              if (!task.revisit) {
                input = matches.remainder;
              }
              if (!task.toContinue) {
                break iterateTransitions;
              }
            } else {
              return output;
            }
          }
        }
        //
        // Prevent infinite loop
        //
        if (watchdog <= 0) {
          throw ["MhchemBugU", "mhchem bug U. Please report."];  // Unexpected character
        }
      }
    },
    concatArray: function (a, b) {
      if (b) {
        if (Array.isArray(b)) {
          for (var iB=0; iB<b.length; iB++) {
            a.push(b[iB]);
          }
        } else {
          a.push(b);
        }
      }
    },

    patterns: {
      //
      // Matching patterns
      // either regexps or function that return null or {match_:"a", remainder:"bc"}
      //
      patterns: {
        // property names must not look like integers ("2") for correct property traversal order, later on
        'empty': /^$/,
        'else': /^./,
        'else2': /^./,
        'space': /^\s/,
        'space A': /^\s(?=[A-Z\\$])/,
        'space$': /^\s$/,
        'a-z': /^[a-z]/,
        'x': /^x/,
        'x$': /^x$/,
        'i$': /^i$/,
        'letters': /^(?:[a-zA-Z\u03B1-\u03C9\u0391-\u03A9?@]|(?:\\(?:alpha|beta|gamma|delta|epsilon|zeta|eta|theta|iota|kappa|lambda|mu|nu|xi|omicron|pi|rho|sigma|tau|upsilon|phi|chi|psi|omega|Gamma|Delta|Theta|Lambda|Xi|Pi|Sigma|Upsilon|Phi|Psi|Omega)(?:\s+|\{\}|(?![a-zA-Z]))))+/,
        '\\greek': /^\\(?:alpha|beta|gamma|delta|epsilon|zeta|eta|theta|iota|kappa|lambda|mu|nu|xi|omicron|pi|rho|sigma|tau|upsilon|phi|chi|psi|omega|Gamma|Delta|Theta|Lambda|Xi|Pi|Sigma|Upsilon|Phi|Psi|Omega)(?:\s+|\{\}|(?![a-zA-Z]))/,
        'one lowercase latin letter $': /^(?:([a-z])(?:$|[^a-zA-Z]))$/,
        '$one lowercase latin letter$ $': /^\$(?:([a-z])(?:$|[^a-zA-Z]))\$$/,
        'one lowercase greek letter $': /^(?:\$?[\u03B1-\u03C9]\$?|\$?\\(?:alpha|beta|gamma|delta|epsilon|zeta|eta|theta|iota|kappa|lambda|mu|nu|xi|omicron|pi|rho|sigma|tau|upsilon|phi|chi|psi|omega)\s*\$?)(?:\s+|\{\}|(?![a-zA-Z]))$/,
        'digits': /^[0-9]+/,
        '-9.,9': /^[+\-]?(?:[0-9]+(?:[,.][0-9]+)?|[0-9]*(?:\.[0-9]+))/,
        '-9.,9 no missing 0': /^[+\-]?[0-9]+(?:[.,][0-9]+)?/,
        '(-)(9.,9)(e)(99)': function (input) {
          var m = input.match(/^(\+\-|\+\/\-|\+|\-|\\pm\s?)?([0-9]+(?:[,.][0-9]+)?|[0-9]*(?:\.[0-9]+))?(\((?:[0-9]+(?:[,.][0-9]+)?|[0-9]*(?:\.[0-9]+))\))?(?:([eE]|\s*(\*|x|\\times|\u00D7)\s*10\^)([+\-]?[0-9]+|\{[+\-]?[0-9]+\}))?/);
          if (m && m[0]) {
            return { match_: m.splice(1), remainder: input.substr(m[0].length) };
          }
          return null;
        },
        '(-)(9)^(-9)': function (input) {
          var m = input.match(/^(\+\-|\+\/\-|\+|\-|\\pm\s?)?([0-9]+(?:[,.][0-9]+)?|[0-9]*(?:\.[0-9]+)?)\^([+\-]?[0-9]+|\{[+\-]?[0-9]+\})/);
          if (m && m[0]) {
            return { match_: m.splice(1), remainder: input.substr(m[0].length) };
          }
          return null;
        },
        'state of aggregation $': function (input) {  // ... or crystal system
          var a = mhchemParser.patterns.findObserveGroups(input, "", /^\([a-z]{1,3}(?=[\),])/, ")", "");  // (aq), (aq,$\infty$), (aq, sat)
          if (a  &&  a.remainder.match(/^($|[\s,;\)\]\}])/)) { return a; }  //  AND end of 'phrase'
          var m = input.match(/^(?:\((?:\\ca\s?)?\$[amothc]\$\))/);  // OR crystal system ($o$) (\ca$c$)
          if (m) {
            return { match_: m[0], remainder: input.substr(m[0].length) };
          }
          return null;
        },
        '_{(state of aggregation)}$': /^_\{(\([a-z]{1,3}\))\}/,
        '{[(': /^(?:\\\{|\[|\()/,
        ')]}': /^(?:\)|\]|\\\})/,
        ', ': /^[,;]\s*/,
        ',': /^[,;]/,
        '.': /^[.]/,
        '. ': /^([.\u22C5\u00B7\u2022])\s*/,
        '...': /^\.\.\.(?=$|[^.])/,
        '* ': /^([*])\s*/,
        '^{(...)}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "^{", "", "", "}"); },
        '^($...$)': function (input) { return mhchemParser.patterns.findObserveGroups(input, "^", "$", "$", ""); },
        '^a': /^\^([0-9]+|[^\\_])/,
        '^\\x{}{}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "^", /^\\[a-zA-Z]+\{/, "}", "", "", "{", "}", "", true); },
        '^\\x{}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "^", /^\\[a-zA-Z]+\{/, "}", ""); },
        '^\\x': /^\^(\\[a-zA-Z]+)\s*/,
        '^(-1)': /^\^(-?\d+)/,
        '\'': /^'/,
        '_{(...)}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "_{", "", "", "}"); },
        '_($...$)': function (input) { return mhchemParser.patterns.findObserveGroups(input, "_", "$", "$", ""); },
        '_9': /^_([+\-]?[0-9]+|[^\\])/,
        '_\\x{}{}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "_", /^\\[a-zA-Z]+\{/, "}", "", "", "{", "}", "", true); },
        '_\\x{}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "_", /^\\[a-zA-Z]+\{/, "}", ""); },
        '_\\x': /^_(\\[a-zA-Z]+)\s*/,
        '^_': /^(?:\^(?=_)|\_(?=\^)|[\^_]$)/,
        '{}': /^\{\}/,
        '{...}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "", "{", "}", ""); },
        '{(...)}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "{", "", "", "}"); },
        '$...$': function (input) { return mhchemParser.patterns.findObserveGroups(input, "", "$", "$", ""); },
        '${(...)}$': function (input) { return mhchemParser.patterns.findObserveGroups(input, "${", "", "", "}$"); },
        '$(...)$': function (input) { return mhchemParser.patterns.findObserveGroups(input, "$", "", "", "$"); },
        '=<>': /^[=<>]/,
        '#': /^[#\u2261]/,
        '+': /^\+/,
        '-$': /^-(?=[\s_},;\]/]|$|\([a-z]+\))/,  // -space -, -; -] -/ -$ -state-of-aggregation
        '-9': /^-(?=[0-9])/,
        '- orbital overlap': /^-(?=(?:[spd]|sp)(?:$|[\s,;\)\]\}]))/,
        '-': /^-/,
        'pm-operator': /^(?:\\pm|\$\\pm\$|\+-|\+\/-)/,
        'operator': /^(?:\+|(?:[\-=<>]|<<|>>|\\approx|\$\\approx\$)(?=\s|$|-?[0-9]))/,
        'arrowUpDown': /^(?:v|\(v\)|\^|\(\^\))(?=$|[\s,;\)\]\}])/,
        '\\bond{(...)}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "\\bond{", "", "", "}"); },
        '->': /^(?:<->|<-->|->|<-|<=>>|<<=>|<=>|[\u2192\u27F6\u21CC])/,
        'CMT': /^[CMT](?=\[)/,
        '[(...)]': function (input) { return mhchemParser.patterns.findObserveGroups(input, "[", "", "", "]"); },
        '1st-level escape': /^(&|\\\\|\\hline)\s*/,
        '\\,': /^(?:\\[,\ ;:])/,  // \\x - but output no space before
        '\\x{}{}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "", /^\\[a-zA-Z]+\{/, "}", "", "", "{", "}", "", true); },
        '\\x{}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "", /^\\[a-zA-Z]+\{/, "}", ""); },
        '\\ca': /^\\ca(?:\s+|(?![a-zA-Z]))/,
        '\\x': /^(?:\\[a-zA-Z]+\s*|\\[_&{}%])/,
        'orbital': /^(?:[0-9]{1,2}[spdfgh]|[0-9]{0,2}sp)(?=$|[^a-zA-Z])/,  // only those with numbers in front, because the others will be formatted correctly anyway
        'others': /^[\/~|]/,
        '\\frac{(...)}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "\\frac{", "", "", "}", "{", "", "", "}"); },
        '\\overset{(...)}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "\\overset{", "", "", "}", "{", "", "", "}"); },
        '\\underset{(...)}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "\\underset{", "", "", "}", "{", "", "", "}"); },
        '\\underbrace{(...)}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "\\underbrace{", "", "", "}_", "{", "", "", "}"); },
        '\\color{(...)}0': function (input) { return mhchemParser.patterns.findObserveGroups(input, "\\color{", "", "", "}"); },
        '\\color{(...)}{(...)}1': function (input) { return mhchemParser.patterns.findObserveGroups(input, "\\color{", "", "", "}", "{", "", "", "}"); },
        '\\color(...){(...)}2': function (input) { return mhchemParser.patterns.findObserveGroups(input, "\\color", "\\", "", /^(?=\{)/, "{", "", "", "}"); },
        '\\ce{(...)}': function (input) { return mhchemParser.patterns.findObserveGroups(input, "\\ce{", "", "", "}"); },
        'oxidation$': /^(?:[+-][IVX]+|\\pm\s*0|\$\\pm\$\s*0)$/,
        'd-oxidation$': /^(?:[+-]?\s?[IVX]+|\\pm\s*0|\$\\pm\$\s*0)$/,  // 0 could be oxidation or charge
        'roman numeral': /^[IVX]+/,
        '1/2$': /^[+\-]?(?:[0-9]+|\$[a-z]\$|[a-z])\/[0-9]+(?:\$[a-z]\$|[a-z])?$/,
        'amount': function (input) {
          var match;
          // e.g. 2, 0.5, 1/2, -2, n/2, +;  $a$ could be added later in parsing
          match = input.match(/^(?:(?:(?:\([+\-]?[0-9]+\/[0-9]+\)|[+\-]?(?:[0-9]+|\$[a-z]\$|[a-z])\/[0-9]+|[+\-]?[0-9]+[.,][0-9]+|[+\-]?\.[0-9]+|[+\-]?[0-9]+)(?:[a-z](?=\s*[A-Z]))?)|[+\-]?[a-z](?=\s*[A-Z])|\+(?!\s))/);
          if (match) {
            return { match_: match[0], remainder: input.substr(match[0].length) };
          }
          var a = mhchemParser.patterns.findObserveGroups(input, "", "$", "$", "");
          if (a) {  // e.g. $2n-1$, $-$
            match = a.match_.match(/^\$(?:\(?[+\-]?(?:[0-9]*[a-z]?[+\-])?[0-9]*[a-z](?:[+\-][0-9]*[a-z]?)?\)?|\+|-)\$$/);
            if (match) {
              return { match_: match[0], remainder: input.substr(match[0].length) };
            }
          }
          return null;
        },
        'amount2': function (input) { return this['amount'](input); },
        '(KV letters),': /^(?:[A-Z][a-z]{0,2}|i)(?=,)/,
        'formula$': function (input) {
          if (input.match(/^\([a-z]+\)$/)) { return null; }  // state of aggregation = no formula
          var match = input.match(/^(?:[a-z]|(?:[0-9\ \+\-\,\.\(\)]+[a-z])+[0-9\ \+\-\,\.\(\)]*|(?:[a-z][0-9\ \+\-\,\.\(\)]+)+[a-z]?)$/);
          if (match) {
            return { match_: match[0], remainder: input.substr(match[0].length) };
          }
          return null;
        },
        'uprightEntities': /^(?:pH|pOH|pC|pK|iPr|iBu)(?=$|[^a-zA-Z])/,
        '/': /^\s*(\/)\s*/,
        '//': /^\s*(\/\/)\s*/,
        '*': /^\s*[*.]\s*/
      },
      findObserveGroups: function (input, begExcl, begIncl, endIncl, endExcl, beg2Excl, beg2Incl, end2Incl, end2Excl, combine) {
        /** @type {{(input: string, pattern: string | RegExp): string | string[] | null;}} */
        var _match = function (input, pattern) {
          if (typeof pattern === "string") {
            if (input.indexOf(pattern) !== 0) { return null; }
            return pattern;
          } else {
            var match = input.match(pattern);
            if (!match) { return null; }
            return match[0];
          }
        };
        /** @type {{(input: string, i: number, endChars: string | RegExp): {endMatchBegin: number, endMatchEnd: number} | null;}} */
        var _findObserveGroups = function (input, i, endChars) {
          var braces = 0;
          while (i < input.length) {
            var a = input.charAt(i);
            var match = _match(input.substr(i), endChars);
            if (match !== null  &&  braces === 0) {
              return { endMatchBegin: i, endMatchEnd: i + match.length };
            } else if (a === "{") {
              braces++;
            } else if (a === "}") {
              if (braces === 0) {
                throw ["ExtraCloseMissingOpen", "Extra close brace or missing open brace"];
              } else {
                braces--;
              }
            }
            i++;
          }
          if (braces > 0) {
            return null;
          }
          return null;
        };
        var match = _match(input, begExcl);
        if (match === null) { return null; }
        input = input.substr(match.length);
        match = _match(input, begIncl);
        if (match === null) { return null; }
        var e = _findObserveGroups(input, match.length, endIncl || endExcl);
        if (e === null) { return null; }
        var match1 = input.substring(0, (endIncl ? e.endMatchEnd : e.endMatchBegin));
        if (!(beg2Excl || beg2Incl)) {
          return {
            match_: match1,
            remainder: input.substr(e.endMatchEnd)
          };
        } else {
          var group2 = this.findObserveGroups(input.substr(e.endMatchEnd), beg2Excl, beg2Incl, end2Incl, end2Excl);
          if (group2 === null) { return null; }
          /** @type {string[]} */
          var matchRet = [match1, group2.match_];
          return {
            match_: (combine ? matchRet.join("") : matchRet),
            remainder: group2.remainder
          };
        }
      },

      //
      // Matching function
      // e.g. match("a", input) will look for the regexp called "a" and see if it matches
      // returns null or {match_:"a", remainder:"bc"}
      //
      match_: function (m, input) {
        var pattern = mhchemParser.patterns.patterns[m];
        if (pattern === undefined) {
          throw ["MhchemBugP", "mhchem bug P. Please report. (" + m + ")"];  // Trying to use non-existing pattern
        } else if (typeof pattern === "function") {
          return mhchemParser.patterns.patterns[m](input);  // cannot use cached var pattern here, because some pattern functions need this===mhchemParser
        } else {  // RegExp
          var match = input.match(pattern);
          if (match) {
            var mm;
            if (match[2]) {
              mm = [ match[1], match[2] ];
            } else if (match[1]) {
              mm = match[1];
            } else {
              mm = match[0];
            }
            return { match_: mm, remainder: input.substr(match[0].length) };
          }
          return null;
        }
      }
    },

    //
    // Generic state machine actions
    //
    actions: {
      'a=': function (buffer, m) { buffer.a = (buffer.a || "") + m; },
      'b=': function (buffer, m) { buffer.b = (buffer.b || "") + m; },
      'p=': function (buffer, m) { buffer.p = (buffer.p || "") + m; },
      'o=': function (buffer, m) { buffer.o = (buffer.o || "") + m; },
      'q=': function (buffer, m) { buffer.q = (buffer.q || "") + m; },
      'd=': function (buffer, m) { buffer.d = (buffer.d || "") + m; },
      'rm=': function (buffer, m) { buffer.rm = (buffer.rm || "") + m; },
      'text=': function (buffer, m) { buffer.text_ = (buffer.text_ || "") + m; },
      'insert': function (buffer, m, a) { return { type_: a }; },
      'insert+p1': function (buffer, m, a) { return { type_: a, p1: m }; },
      'insert+p1+p2': function (buffer, m, a) { return { type_: a, p1: m[0], p2: m[1] }; },
      'copy': function (buffer, m) { return m; },
      'rm': function (buffer, m) { return { type_: 'rm', p1: m || ""}; },
      'text': function (buffer, m) { return mhchemParser.go(m, 'text'); },
      '{text}': function (buffer, m) {
        var ret = [ "{" ];
        mhchemParser.concatArray(ret, mhchemParser.go(m, 'text'));
        ret.push("}");
        return ret;
      },
      'tex-math': function (buffer, m) { return mhchemParser.go(m, 'tex-math'); },
      'tex-math tight': function (buffer, m) { return mhchemParser.go(m, 'tex-math tight'); },
      'bond': function (buffer, m, k) { return { type_: 'bond', kind_: k || m }; },
      'color0-output': function (buffer, m) { return { type_: 'color0', color: m[0] }; },
      'ce': function (buffer, m) { return mhchemParser.go(m); },
      '1/2': function (buffer, m) {
        /** @type {ParserOutput[]} */
        var ret = [];
        if (m.match(/^[+\-]/)) {
          ret.push(m.substr(0, 1));
          m = m.substr(1);
        }
        var n = m.match(/^([0-9]+|\$[a-z]\$|[a-z])\/([0-9]+)(\$[a-z]\$|[a-z])?$/);
        n[1] = n[1].replace(/\$/g, "");
        ret.push({ type_: 'frac', p1: n[1], p2: n[2] });
        if (n[3]) {
          n[3] = n[3].replace(/\$/g, "");
          ret.push({ type_: 'tex-math', p1: n[3] });
        }
        return ret;
      },
      '9,9': function (buffer, m) { return mhchemParser.go(m, '9,9'); }
    },
    //
    // createTransitions
    // convert  { 'letter': { 'state': { action_: 'output' } } }  to  { 'state' => [ { pattern: 'letter', task: { action_: [{type_: 'output'}] } } ] }
    // with expansion of 'a|b' to 'a' and 'b' (at 2 places)
    //
    createTransitions: function (o) {
      var pattern, state;
      /** @type {string[]} */
      var stateArray;
      var i;
      //
      // 1. Collect all states
      //
      /** @type {Transitions} */
      var transitions = {};
      for (pattern in o) {
        for (state in o[pattern]) {
          stateArray = state.split("|");
          o[pattern][state].stateArray = stateArray;
          for (i=0; i<stateArray.length; i++) {
            transitions[stateArray[i]] = [];
          }
        }
      }
      //
      // 2. Fill states
      //
      for (pattern in o) {
        for (state in o[pattern]) {
          stateArray = o[pattern][state].stateArray || [];
          for (i=0; i<stateArray.length; i++) {
            //
            // 2a. Normalize actions into array:  'text=' ==> [{type_:'text='}]
            // (Note to myself: Resolving the function here would be problematic. It would need .bind (for *this*) and currying (for *option*).)
            //
            /** @type {any} */
            var p = o[pattern][state];
            if (p.action_) {
              p.action_ = [].concat(p.action_);
              for (var k=0; k<p.action_.length; k++) {
                if (typeof p.action_[k] === "string") {
                  p.action_[k] = { type_: p.action_[k] };
                }
              }
            } else {
              p.action_ = [];
            }
            //
            // 2.b Multi-insert
            //
            var patternArray = pattern.split("|");
            for (var j=0; j<patternArray.length; j++) {
              if (stateArray[i] === '*') {  // insert into all
                for (var t in transitions) {
                  transitions[t].push({ pattern: patternArray[j], task: p });
                }
              } else {
                transitions[stateArray[i]].push({ pattern: patternArray[j], task: p });
              }
            }
          }
        }
      }
      return transitions;
    },
    stateMachines: {}
  };

  //
  // Definition of state machines
  //
  mhchemParser.stateMachines = {
    //
    // \ce state machines
    //
    //#region ce
    'ce': {  // main parser
      transitions: mhchemParser.createTransitions({
        'empty': {
          '*': { action_: 'output' } },
        'else':  {
          '0|1|2': { action_: 'beginsWithBond=false', revisit: true, toContinue: true } },
        'oxidation$': {
          '0': { action_: 'oxidation-output' } },
        'CMT': {
          'r': { action_: 'rdt=', nextState: 'rt' },
          'rd': { action_: 'rqt=', nextState: 'rdt' } },
        'arrowUpDown': {
          '0|1|2|as': { action_: [ 'sb=false', 'output', 'operator' ], nextState: '1' } },
        'uprightEntities': {
          '0|1|2': { action_: [ 'o=', 'output' ], nextState: '1' } },
        'orbital': {
          '0|1|2|3': { action_: 'o=', nextState: 'o' } },
        '->': {
          '0|1|2|3': { action_: 'r=', nextState: 'r' },
          'a|as': { action_: [ 'output', 'r=' ], nextState: 'r' },
          '*': { action_: [ 'output', 'r=' ], nextState: 'r' } },
        '+': {
          'o': { action_: 'd= kv',  nextState: 'd' },
          'd|D': { action_: 'd=', nextState: 'd' },
          'q': { action_: 'd=',  nextState: 'qd' },
          'qd|qD': { action_: 'd=', nextState: 'qd' },
          'dq': { action_: [ 'output', 'd=' ], nextState: 'd' },
          '3': { action_: [ 'sb=false', 'output', 'operator' ], nextState: '0' } },
        'amount': {
          '0|2': { action_: 'a=', nextState: 'a' } },
        'pm-operator': {
          '0|1|2|a|as': { action_: [ 'sb=false', 'output', { type_: 'operator', option: '\\pm' } ], nextState: '0' } },
        'operator': {
          '0|1|2|a|as': { action_: [ 'sb=false', 'output', 'operator' ], nextState: '0' } },
        '-$': {
          'o|q': { action_: [ 'charge or bond', 'output' ],  nextState: 'qd' },
          'd': { action_: 'd=', nextState: 'd' },
          'D': { action_: [ 'output', { type_: 'bond', option: "-" } ], nextState: '3' },
          'q': { action_: 'd=',  nextState: 'qd' },
          'qd': { action_: 'd=', nextState: 'qd' },
          'qD|dq': { action_: [ 'output', { type_: 'bond', option: "-" } ], nextState: '3' } },
        '-9': {
          '3|o': { action_: [ 'output', { type_: 'insert', option: 'hyphen' } ], nextState: '3' } },
        '- orbital overlap': {
          'o': { action_: [ 'output', { type_: 'insert', option: 'hyphen' } ], nextState: '2' },
          'd': { action_: [ 'output', { type_: 'insert', option: 'hyphen' } ], nextState: '2' } },
        '-': {
          '0|1|2': { action_: [ { type_: 'output', option: 1 }, 'beginsWithBond=true', { type_: 'bond', option: "-" } ], nextState: '3' },
          '3': { action_: { type_: 'bond', option: "-" } },
          'a': { action_: [ 'output', { type_: 'insert', option: 'hyphen' } ], nextState: '2' },
          'as': { action_: [ { type_: 'output', option: 2 }, { type_: 'bond', option: "-" } ], nextState: '3' },
          'b': { action_: 'b=' },
          'o': { action_: { type_: '- after o/d', option: false }, nextState: '2' },
          'q': { action_: { type_: '- after o/d', option: false }, nextState: '2' },
          'd|qd|dq': { action_: { type_: '- after o/d', option: true }, nextState: '2' },
          'D|qD|p': { action_: [ 'output', { type_: 'bond', option: "-" } ], nextState: '3' } },
        'amount2': {
          '1|3': { action_: 'a=', nextState: 'a' } },
        'letters': {
          '0|1|2|3|a|as|b|p|bp|o': { action_: 'o=', nextState: 'o' },
          'q|dq': { action_: ['output', 'o='], nextState: 'o' },
          'd|D|qd|qD': { action_: 'o after d', nextState: 'o' } },
        'digits': {
          'o': { action_: 'q=', nextState: 'q' },
          'd|D': { action_: 'q=', nextState: 'dq' },
          'q': { action_: [ 'output', 'o=' ], nextState: 'o' },
          'a': { action_: 'o=', nextState: 'o' } },
        'space A': {
          'b|p|bp': {} },
        'space': {
          'a': { nextState: 'as' },
          '0': { action_: 'sb=false' },
          '1|2': { action_: 'sb=true' },
          'r|rt|rd|rdt|rdq': { action_: 'output', nextState: '0' },
          '*': { action_: [ 'output', 'sb=true' ], nextState: '1'} },
        '1st-level escape': {
          '1|2': { action_: [ 'output', { type_: 'insert+p1', option: '1st-level escape' } ] },
          '*': { action_: [ 'output', { type_: 'insert+p1', option: '1st-level escape' } ], nextState: '0' } },
        '[(...)]': {
          'r|rt': { action_: 'rd=', nextState: 'rd' },
          'rd|rdt': { action_: 'rq=', nextState: 'rdq' } },
        '...': {
          'o|d|D|dq|qd|qD': { action_: [ 'output', { type_: 'bond', option: "..." } ], nextState: '3' },
          '*': { action_: [ { type_: 'output', option: 1 }, { type_: 'insert', option: 'ellipsis' } ], nextState: '1' } },
        '. |* ': {
          '*': { action_: [ 'output', { type_: 'insert', option: 'addition compound' } ], nextState: '1' } },
        'state of aggregation $': {
          '*': { action_: [ 'output', 'state of aggregation' ], nextState: '1' } },
        '{[(': {
          'a|as|o': { action_: [ 'o=', 'output', 'parenthesisLevel++' ], nextState: '2' },
          '0|1|2|3': { action_: [ 'o=', 'output', 'parenthesisLevel++' ], nextState: '2' },
          '*': { action_: [ 'output', 'o=', 'output', 'parenthesisLevel++' ], nextState: '2' } },
        ')]}': {
          '0|1|2|3|b|p|bp|o': { action_: [ 'o=', 'parenthesisLevel--' ], nextState: 'o' },
          'a|as|d|D|q|qd|qD|dq': { action_: [ 'output', 'o=', 'parenthesisLevel--' ], nextState: 'o' } },
        ', ': {
          '*': { action_: [ 'output', 'comma' ], nextState: '0' } },
        '^_': {  // ^ and _ without a sensible argument
          '*': { } },
        '^{(...)}|^($...$)': {
          '0|1|2|as': { action_: 'b=', nextState: 'b' },
          'p': { action_: 'b=', nextState: 'bp' },
          '3|o': { action_: 'd= kv', nextState: 'D' },
          'q': { action_: 'd=', nextState: 'qD' },
          'd|D|qd|qD|dq': { action_: [ 'output', 'd=' ], nextState: 'D' } },
        '^a|^\\x{}{}|^\\x{}|^\\x|\'': {
          '0|1|2|as': { action_: 'b=', nextState: 'b' },
          'p': { action_: 'b=', nextState: 'bp' },
          '3|o': { action_: 'd= kv', nextState: 'd' },
          'q': { action_: 'd=', nextState: 'qd' },
          'd|qd|D|qD': { action_: 'd=' },
          'dq': { action_: [ 'output', 'd=' ], nextState: 'd' } },
        '_{(state of aggregation)}$': {
          'd|D|q|qd|qD|dq': { action_: [ 'output', 'q=' ], nextState: 'q' } },
        '_{(...)}|_($...$)|_9|_\\x{}{}|_\\x{}|_\\x': {
          '0|1|2|as': { action_: 'p=', nextState: 'p' },
          'b': { action_: 'p=', nextState: 'bp' },
          '3|o': { action_: 'q=', nextState: 'q' },
          'd|D': { action_: 'q=', nextState: 'dq' },
          'q|qd|qD|dq': { action_: [ 'output', 'q=' ], nextState: 'q' } },
        '=<>': {
          '0|1|2|3|a|as|o|q|d|D|qd|qD|dq': { action_: [ { type_: 'output', option: 2 }, 'bond' ], nextState: '3' } },
        '#': {
          '0|1|2|3|a|as|o': { action_: [ { type_: 'output', option: 2 }, { type_: 'bond', option: "#" } ], nextState: '3' } },
        '{}': {
          '*': { action_: { type_: 'output', option: 1 },  nextState: '1' } },
        '{...}': {
          '0|1|2|3|a|as|b|p|bp': { action_: 'o=', nextState: 'o' },
          'o|d|D|q|qd|qD|dq': { action_: [ 'output', 'o=' ], nextState: 'o' } },
        '$...$': {
          'a': { action_: 'a=' },  // 2$n$
          '0|1|2|3|as|b|p|bp|o': { action_: 'o=', nextState: 'o' },  // not 'amount'
          'as|o': { action_: 'o=' },
          'q|d|D|qd|qD|dq': { action_: [ 'output', 'o=' ], nextState: 'o' } },
        '\\bond{(...)}': {
          '*': { action_: [ { type_: 'output', option: 2 }, 'bond' ], nextState: "3" } },
        '\\frac{(...)}': {
          '*': { action_: [ { type_: 'output', option: 1 }, 'frac-output' ], nextState: '3' } },
        '\\overset{(...)}': {
          '*': { action_: [ { type_: 'output', option: 2 }, 'overset-output' ], nextState: '3' } },
        '\\underset{(...)}': {
          '*': { action_: [ { type_: 'output', option: 2 }, 'underset-output' ], nextState: '3' } },
        '\\underbrace{(...)}': {
          '*': { action_: [ { type_: 'output', option: 2 }, 'underbrace-output' ], nextState: '3' } },
        '\\color{(...)}{(...)}1|\\color(...){(...)}2': {
          '*': { action_: [ { type_: 'output', option: 2 }, 'color-output' ], nextState: '3' } },
        '\\color{(...)}0': {
          '*': { action_: [ { type_: 'output', option: 2 }, 'color0-output' ] } },
        '\\ce{(...)}': {
          '*': { action_: [ { type_: 'output', option: 2 }, 'ce' ], nextState: '3' } },
        '\\,': {
          '*': { action_: [ { type_: 'output', option: 1 }, 'copy' ], nextState: '1' } },
        '\\x{}{}|\\x{}|\\x': {
          '0|1|2|3|a|as|b|p|bp|o|c0': { action_: [ 'o=', 'output' ], nextState: '3' },
          '*': { action_: ['output', 'o=', 'output' ], nextState: '3' } },
        'others': {
          '*': { action_: [ { type_: 'output', option: 1 }, 'copy' ], nextState: '3' } },
        'else2': {
          'a': { action_: 'a to o', nextState: 'o', revisit: true },
          'as': { action_: [ 'output', 'sb=true' ], nextState: '1', revisit: true },
          'r|rt|rd|rdt|rdq': { action_: [ 'output' ], nextState: '0', revisit: true },
          '*': { action_: [ 'output', 'copy' ], nextState: '3' } }
      }),
      actions: {
        'o after d': function (buffer, m) {
          var ret;
          if ((buffer.d || "").match(/^[0-9]+$/)) {
            var tmp = buffer.d;
            buffer.d = undefined;
            ret = this['output'](buffer);
            buffer.b = tmp;
          } else {
            ret = this['output'](buffer);
          }
          mhchemParser.actions['o='](buffer, m);
          return ret;
        },
        'd= kv': function (buffer, m) {
          buffer.d = m;
          buffer.dType = 'kv';
        },
        'charge or bond': function (buffer, m) {
          if (buffer['beginsWithBond']) {
            /** @type {ParserOutput[]} */
            var ret = [];
            mhchemParser.concatArray(ret, this['output'](buffer));
            mhchemParser.concatArray(ret, mhchemParser.actions['bond'](buffer, m, "-"));
            return ret;
          } else {
            buffer.d = m;
          }
        },
        '- after o/d': function (buffer, m, isAfterD) {
          var c1 = mhchemParser.patterns.match_('orbital', buffer.o || "");
          var c2 = mhchemParser.patterns.match_('one lowercase greek letter $', buffer.o || "");
          var c3 = mhchemParser.patterns.match_('one lowercase latin letter $', buffer.o || "");
          var c4 = mhchemParser.patterns.match_('$one lowercase latin letter$ $', buffer.o || "");
          var hyphenFollows =  m==="-" && ( c1 && c1.remainder===""  ||  c2  ||  c3  ||  c4 );
          if (hyphenFollows && !buffer.a && !buffer.b && !buffer.p && !buffer.d && !buffer.q && !c1 && c3) {
            buffer.o = '$' + buffer.o + '$';
          }
          /** @type {ParserOutput[]} */
          var ret = [];
          if (hyphenFollows) {
            mhchemParser.concatArray(ret, this['output'](buffer));
            ret.push({ type_: 'hyphen' });
          } else {
            c1 = mhchemParser.patterns.match_('digits', buffer.d || "");
            if (isAfterD && c1 && c1.remainder==='') {
              mhchemParser.concatArray(ret, mhchemParser.actions['d='](buffer, m));
              mhchemParser.concatArray(ret, this['output'](buffer));
            } else {
              mhchemParser.concatArray(ret, this['output'](buffer));
              mhchemParser.concatArray(ret, mhchemParser.actions['bond'](buffer, m, "-"));
            }
          }
          return ret;
        },
        'a to o': function (buffer) {
          buffer.o = buffer.a;
          buffer.a = undefined;
        },
        'sb=true': function (buffer) { buffer.sb = true; },
        'sb=false': function (buffer) { buffer.sb = false; },
        'beginsWithBond=true': function (buffer) { buffer['beginsWithBond'] = true; },
        'beginsWithBond=false': function (buffer) { buffer['beginsWithBond'] = false; },
        'parenthesisLevel++': function (buffer) { buffer['parenthesisLevel']++; },
        'parenthesisLevel--': function (buffer) { buffer['parenthesisLevel']--; },
        'state of aggregation': function (buffer, m) {
          return { type_: 'state of aggregation', p1: mhchemParser.go(m, 'o') };
        },
        'comma': function (buffer, m) {
          var a = m.replace(/\s*$/, '');
          var withSpace = (a !== m);
          if (withSpace  &&  buffer['parenthesisLevel'] === 0) {
            return { type_: 'comma enumeration L', p1: a };
          } else {
            return { type_: 'comma enumeration M', p1: a };
          }
        },
        'output': function (buffer, m, entityFollows) {
          // entityFollows:
          //   undefined = if we have nothing else to output, also ignore the just read space (buffer.sb)
          //   1 = an entity follows, never omit the space if there was one just read before (can only apply to state 1)
          //   2 = 1 + the entity can have an amount, so output a\, instead of converting it to o (can only apply to states a|as)
          /** @type {ParserOutput | ParserOutput[]} */
          var ret;
          if (!buffer.r) {
            ret = [];
            if (!buffer.a && !buffer.b && !buffer.p && !buffer.o && !buffer.q && !buffer.d && !entityFollows) {
              //ret = [];
            } else {
              if (buffer.sb) {
                ret.push({ type_: 'entitySkip' });
              }
              if (!buffer.o && !buffer.q && !buffer.d && !buffer.b && !buffer.p && entityFollows!==2) {
                buffer.o = buffer.a;
                buffer.a = undefined;
              } else if (!buffer.o && !buffer.q && !buffer.d && (buffer.b || buffer.p)) {
                buffer.o = buffer.a;
                buffer.d = buffer.b;
                buffer.q = buffer.p;
                buffer.a = buffer.b = buffer.p = undefined;
              } else {
                if (buffer.o && buffer.dType==='kv' && mhchemParser.patterns.match_('d-oxidation$', buffer.d || "")) {
                  buffer.dType = 'oxidation';
                } else if (buffer.o && buffer.dType==='kv' && !buffer.q) {
                  buffer.dType = undefined;
                }
              }
              ret.push({
                type_: 'chemfive',
                a: mhchemParser.go(buffer.a, 'a'),
                b: mhchemParser.go(buffer.b, 'bd'),
                p: mhchemParser.go(buffer.p, 'pq'),
                o: mhchemParser.go(buffer.o, 'o'),
                q: mhchemParser.go(buffer.q, 'pq'),
                d: mhchemParser.go(buffer.d, (buffer.dType === 'oxidation' ? 'oxidation' : 'bd')),
                dType: buffer.dType
              });
            }
          } else {  // r
            /** @type {ParserOutput[]} */
            var rd;
            if (buffer.rdt === 'M') {
              rd = mhchemParser.go(buffer.rd, 'tex-math');
            } else if (buffer.rdt === 'T') {
              rd = [ { type_: 'text', p1: buffer.rd || "" } ];
            } else {
              rd = mhchemParser.go(buffer.rd);
            }
            /** @type {ParserOutput[]} */
            var rq;
            if (buffer.rqt === 'M') {
              rq = mhchemParser.go(buffer.rq, 'tex-math');
            } else if (buffer.rqt === 'T') {
              rq = [ { type_: 'text', p1: buffer.rq || ""} ];
            } else {
              rq = mhchemParser.go(buffer.rq);
            }
            ret = {
              type_: 'arrow',
              r: buffer.r,
              rd: rd,
              rq: rq
            };
          }
          for (var p in buffer) {
            if (p !== 'parenthesisLevel'  &&  p !== 'beginsWithBond') {
              delete buffer[p];
            }
          }
          return ret;
        },
        'oxidation-output': function (buffer, m) {
          var ret = [ "{" ];
          mhchemParser.concatArray(ret, mhchemParser.go(m, 'oxidation'));
          ret.push("}");
          return ret;
        },
        'frac-output': function (buffer, m) {
          return { type_: 'frac-ce', p1: mhchemParser.go(m[0]), p2: mhchemParser.go(m[1]) };
        },
        'overset-output': function (buffer, m) {
          return { type_: 'overset', p1: mhchemParser.go(m[0]), p2: mhchemParser.go(m[1]) };
        },
        'underset-output': function (buffer, m) {
          return { type_: 'underset', p1: mhchemParser.go(m[0]), p2: mhchemParser.go(m[1]) };
        },
        'underbrace-output': function (buffer, m) {
          return { type_: 'underbrace', p1: mhchemParser.go(m[0]), p2: mhchemParser.go(m[1]) };
        },
        'color-output': function (buffer, m) {
          return { type_: 'color', color1: m[0], color2: mhchemParser.go(m[1]) };
        },
        'r=': function (buffer, m) { buffer.r = m; },
        'rdt=': function (buffer, m) { buffer.rdt = m; },
        'rd=': function (buffer, m) { buffer.rd = m; },
        'rqt=': function (buffer, m) { buffer.rqt = m; },
        'rq=': function (buffer, m) { buffer.rq = m; },
        'operator': function (buffer, m, p1) { return { type_: 'operator', kind_: (p1 || m) }; }
      }
    },
    'a': {
      transitions: mhchemParser.createTransitions({
        'empty': {
          '*': {} },
        '1/2$': {
          '0': { action_: '1/2' } },
        'else': {
          '0': { nextState: '1', revisit: true } },
        '$(...)$': {
          '*': { action_: 'tex-math tight', nextState: '1' } },
        ',': {
          '*': { action_: { type_: 'insert', option: 'commaDecimal' } } },
        'else2': {
          '*': { action_: 'copy' } }
      }),
      actions: {}
    },
    'o': {
      transitions: mhchemParser.createTransitions({
        'empty': {
          '*': {} },
        '1/2$': {
          '0': { action_: '1/2' } },
        'else': {
          '0': { nextState: '1', revisit: true } },
        'letters': {
          '*': { action_: 'rm' } },
        '\\ca': {
          '*': { action_: { type_: 'insert', option: 'circa' } } },
        '\\x{}{}|\\x{}|\\x': {
          '*': { action_: 'copy' } },
        '${(...)}$|$(...)$': {
          '*': { action_: 'tex-math' } },
        '{(...)}': {
          '*': { action_: '{text}' } },
        'else2': {
          '*': { action_: 'copy' } }
      }),
      actions: {}
    },
    'text': {
      transitions: mhchemParser.createTransitions({
        'empty': {
          '*': { action_: 'output' } },
        '{...}': {
          '*': { action_: 'text=' } },
        '${(...)}$|$(...)$': {
          '*': { action_: 'tex-math' } },
        '\\greek': {
          '*': { action_: [ 'output', 'rm' ] } },
        '\\,|\\x{}{}|\\x{}|\\x': {
          '*': { action_: [ 'output', 'copy' ] } },
        'else': {
          '*': { action_: 'text=' } }
      }),
      actions: {
        'output': function (buffer) {
          if (buffer.text_) {
            /** @type {ParserOutput} */
            var ret = { type_: 'text', p1: buffer.text_ };
            for (var p in buffer) { delete buffer[p]; }
            return ret;
          }
        }
      }
    },
    'pq': {
      transitions: mhchemParser.createTransitions({
        'empty': {
          '*': {} },
        'state of aggregation $': {
          '*': { action_: 'state of aggregation' } },
        'i$': {
          '0': { nextState: '!f', revisit: true } },
        '(KV letters),': {
          '0': { action_: 'rm', nextState: '0' } },
        'formula$': {
          '0': { nextState: 'f', revisit: true } },
        '1/2$': {
          '0': { action_: '1/2' } },
        'else': {
          '0': { nextState: '!f', revisit: true } },
        '${(...)}$|$(...)$': {
          '*': { action_: 'tex-math' } },
        '{(...)}': {
          '*': { action_: 'text' } },
        'a-z': {
          'f': { action_: 'tex-math' } },
        'letters': {
          '*': { action_: 'rm' } },
        '-9.,9': {
          '*': { action_: '9,9'  } },
        ',': {
          '*': { action_: { type_: 'insert+p1', option: 'comma enumeration S' } } },
        '\\color{(...)}{(...)}1|\\color(...){(...)}2': {
          '*': { action_: 'color-output' } },
        '\\color{(...)}0': {
          '*': { action_: 'color0-output' } },
        '\\ce{(...)}': {
          '*': { action_: 'ce' } },
        '\\,|\\x{}{}|\\x{}|\\x': {
          '*': { action_: 'copy' } },
        'else2': {
          '*': { action_: 'copy' } }
      }),
      actions: {
        'state of aggregation': function (buffer, m) {
          return { type_: 'state of aggregation subscript', p1: mhchemParser.go(m, 'o') };
        },
        'color-output': function (buffer, m) {
          return { type_: 'color', color1: m[0], color2: mhchemParser.go(m[1], 'pq') };
        }
      }
    },
    'bd': {
      transitions: mhchemParser.createTransitions({
        'empty': {
          '*': {} },
        'x$': {
          '0': { nextState: '!f', revisit: true } },
        'formula$': {
          '0': { nextState: 'f', revisit: true } },
        'else': {
          '0': { nextState: '!f', revisit: true } },
        '-9.,9 no missing 0': {
          '*': { action_: '9,9' } },
        '.': {
          '*': { action_: { type_: 'insert', option: 'electron dot' } } },
        'a-z': {
          'f': { action_: 'tex-math' } },
        'x': {
          '*': { action_: { type_: 'insert', option: 'KV x' } } },
        'letters': {
          '*': { action_: 'rm' } },
        '\'': {
          '*': { action_: { type_: 'insert', option: 'prime' } } },
        '${(...)}$|$(...)$': {
          '*': { action_: 'tex-math' } },
        '{(...)}': {
          '*': { action_: 'text' } },
        '\\color{(...)}{(...)}1|\\color(...){(...)}2': {
          '*': { action_: 'color-output' } },
        '\\color{(...)}0': {
          '*': { action_: 'color0-output' } },
        '\\ce{(...)}': {
          '*': { action_: 'ce' } },
        '\\,|\\x{}{}|\\x{}|\\x': {
          '*': { action_: 'copy' } },
        'else2': {
          '*': { action_: 'copy' } }
      }),
      actions: {
        'color-output': function (buffer, m) {
          return { type_: 'color', color1: m[0], color2: mhchemParser.go(m[1], 'bd') };
        }
      }
    },
    'oxidation': {
      transitions: mhchemParser.createTransitions({
        'empty': {
          '*': {} },
        'roman numeral': {
          '*': { action_: 'roman-numeral' } },
        '${(...)}$|$(...)$': {
          '*': { action_: 'tex-math' } },
        'else': {
          '*': { action_: 'copy' } }
      }),
      actions: {
        'roman-numeral': function (buffer, m) { return { type_: 'roman numeral', p1: m || "" }; }
      }
    },
    'tex-math': {
      transitions: mhchemParser.createTransitions({
        'empty': {
          '*': { action_: 'output' } },
        '\\ce{(...)}': {
          '*': { action_: [ 'output', 'ce' ] } },
        '{...}|\\,|\\x{}{}|\\x{}|\\x': {
          '*': { action_: 'o=' } },
        'else': {
          '*': { action_: 'o=' } }
      }),
      actions: {
        'output': function (buffer) {
          if (buffer.o) {
            /** @type {ParserOutput} */
            var ret = { type_: 'tex-math', p1: buffer.o };
            for (var p in buffer) { delete buffer[p]; }
            return ret;
          }
        }
      }
    },
    'tex-math tight': {
      transitions: mhchemParser.createTransitions({
        'empty': {
          '*': { action_: 'output' } },
        '\\ce{(...)}': {
          '*': { action_: [ 'output', 'ce' ] } },
        '{...}|\\,|\\x{}{}|\\x{}|\\x': {
          '*': { action_: 'o=' } },
        '-|+': {
          '*': { action_: 'tight operator' } },
        'else': {
          '*': { action_: 'o=' } }
      }),
      actions: {
        'tight operator': function (buffer, m) { buffer.o = (buffer.o || "") + "{"+m+"}"; },
        'output': function (buffer) {
          if (buffer.o) {
            /** @type {ParserOutput} */
            var ret = { type_: 'tex-math', p1: buffer.o };
            for (var p in buffer) { delete buffer[p]; }
            return ret;
          }
        }
      }
    },
    '9,9': {
      transitions: mhchemParser.createTransitions({
        'empty': {
          '*': {} },
        ',': {
          '*': { action_: 'comma' } },
        'else': {
          '*': { action_: 'copy' } }
      }),
      actions: {
        'comma': function () { return { type_: 'commaDecimal' }; }
      }
    },
    //#endregion
    //
    // \pu state machines
    //
    //#region pu
    'pu': {
      transitions: mhchemParser.createTransitions({
        'empty': {
          '*': { action_: 'output' } },
        'space$': {
          '*': { action_: [ 'output', 'space' ] } },
        '{[(|)]}': {
          '0|a': { action_: 'copy' } },
        '(-)(9)^(-9)': {
          '0': { action_: 'number^', nextState: 'a' } },
        '(-)(9.,9)(e)(99)': {
          '0': { action_: 'enumber', nextState: 'a' } },
        'space': {
          '0|a': {} },
        'pm-operator': {
          '0|a': { action_: { type_: 'operator', option: '\\pm' }, nextState: '0' } },
        'operator': {
          '0|a': { action_: 'copy', nextState: '0' } },
        '//': {
          'd': { action_: 'o=', nextState: '/' } },
        '/': {
          'd': { action_: 'o=', nextState: '/' } },
        '{...}|else': {
          '0|d': { action_: 'd=', nextState: 'd' },
          'a': { action_: [ 'space', 'd=' ], nextState: 'd' },
          '/|q': { action_: 'q=', nextState: 'q' } }
      }),
      actions: {
        'enumber': function (buffer, m) {
          /** @type {ParserOutput[]} */
          var ret = [];
          if (m[0] === "+-"  ||  m[0] === "+/-") {
            ret.push("\\pm ");
          } else if (m[0]) {
            ret.push(m[0]);
          }
          if (m[1]) {
            mhchemParser.concatArray(ret, mhchemParser.go(m[1], 'pu-9,9'));
            if (m[2]) {
              if (m[2].match(/[,.]/)) {
                mhchemParser.concatArray(ret, mhchemParser.go(m[2], 'pu-9,9'));
              } else {
                ret.push(m[2]);
              }
            }
            m[3] = m[4] || m[3];
            if (m[3]) {
              m[3] = m[3].trim();
              if (m[3] === "e"  ||  m[3].substr(0, 1) === "*") {
                ret.push({ type_: 'cdot' });
              } else {
                ret.push({ type_: 'times' });
              }
            }
          }
          if (m[3]) {
            ret.push("10^{"+m[5]+"}");
          }
          return ret;
        },
        'number^': function (buffer, m) {
          /** @type {ParserOutput[]} */
          var ret = [];
          if (m[0] === "+-"  ||  m[0] === "+/-") {
            ret.push("\\pm ");
          } else if (m[0]) {
            ret.push(m[0]);
          }
          mhchemParser.concatArray(ret, mhchemParser.go(m[1], 'pu-9,9'));
          ret.push("^{"+m[2]+"}");
          return ret;
        },
        'operator': function (buffer, m, p1) { return { type_: 'operator', kind_: (p1 || m) }; },
        'space': function () { return { type_: 'pu-space-1' }; },
        'output': function (buffer) {
          /** @type {ParserOutput | ParserOutput[]} */
          var ret;
          var md = mhchemParser.patterns.match_('{(...)}', buffer.d || "");
          if (md  &&  md.remainder === '') { buffer.d = md.match_; }
          var mq = mhchemParser.patterns.match_('{(...)}', buffer.q || "");
          if (mq  &&  mq.remainder === '') { buffer.q = mq.match_; }
          if (buffer.d) {
            buffer.d = buffer.d.replace(/\u00B0C|\^oC|\^{o}C/g, "{}^{\\circ}C");
            buffer.d = buffer.d.replace(/\u00B0F|\^oF|\^{o}F/g, "{}^{\\circ}F");
          }
          if (buffer.q) {  // fraction
            buffer.q = buffer.q.replace(/\u00B0C|\^oC|\^{o}C/g, "{}^{\\circ}C");
            buffer.q = buffer.q.replace(/\u00B0F|\^oF|\^{o}F/g, "{}^{\\circ}F");
            var b5 = {
              d: mhchemParser.go(buffer.d, 'pu'),
              q: mhchemParser.go(buffer.q, 'pu')
            };
            if (buffer.o === '//') {
              ret = { type_: 'pu-frac', p1: b5.d, p2: b5.q };
            } else {
              ret = b5.d;
              if (b5.d.length > 1  ||  b5.q.length > 1) {
                ret.push({ type_: ' / ' });
              } else {
                ret.push({ type_: '/' });
              }
              mhchemParser.concatArray(ret, b5.q);
            }
          } else {  // no fraction
            ret = mhchemParser.go(buffer.d, 'pu-2');
          }
          for (var p in buffer) { delete buffer[p]; }
          return ret;
        }
      }
    },
    'pu-2': {
      transitions: mhchemParser.createTransitions({
        'empty': {
          '*': { action_: 'output' } },
        '*': {
          '*': { action_: [ 'output', 'cdot' ], nextState: '0' } },
        '\\x': {
          '*': { action_: 'rm=' } },
        'space': {
          '*': { action_: [ 'output', 'space' ], nextState: '0' } },
        '^{(...)}|^(-1)': {
          '1': { action_: '^(-1)' } },
        '-9.,9': {
          '0': { action_: 'rm=', nextState: '0' },
          '1': { action_: '^(-1)', nextState: '0' } },
        '{...}|else': {
          '*': { action_: 'rm=', nextState: '1' } }
      }),
      actions: {
        'cdot': function () { return { type_: 'tight cdot' }; },
        '^(-1)': function (buffer, m) { buffer.rm += "^{"+m+"}"; },
        'space': function () { return { type_: 'pu-space-2' }; },
        'output': function (buffer) {
          /** @type {ParserOutput | ParserOutput[]} */
          var ret = [];
          if (buffer.rm) {
            var mrm = mhchemParser.patterns.match_('{(...)}', buffer.rm || "");
            if (mrm  &&  mrm.remainder === '') {
              ret = mhchemParser.go(mrm.match_, 'pu');
            } else {
              ret = { type_: 'rm', p1: buffer.rm };
            }
          }
          for (var p in buffer) { delete buffer[p]; }
          return ret;
        }
      }
    },
    'pu-9,9': {
      transitions: mhchemParser.createTransitions({
        'empty': {
          '0': { action_: 'output-0' },
          'o': { action_: 'output-o' } },
        ',': {
          '0': { action_: [ 'output-0', 'comma' ], nextState: 'o' } },
        '.': {
          '0': { action_: [ 'output-0', 'copy' ], nextState: 'o' } },
        'else': {
          '*': { action_: 'text=' } }
      }),
      actions: {
        'comma': function () { return { type_: 'commaDecimal' }; },
        'output-0': function (buffer) {
          /** @type {ParserOutput[]} */
          var ret = [];
          buffer.text_ = buffer.text_ || "";
          if (buffer.text_.length > 4) {
            var a = buffer.text_.length % 3;
            if (a === 0) { a = 3; }
            for (var i=buffer.text_.length-3; i>0; i-=3) {
              ret.push(buffer.text_.substr(i, 3));
              ret.push({ type_: '1000 separator' });
            }
            ret.push(buffer.text_.substr(0, a));
            ret.reverse();
          } else {
            ret.push(buffer.text_);
          }
          for (var p in buffer) { delete buffer[p]; }
          return ret;
        },
        'output-o': function (buffer) {
          /** @type {ParserOutput[]} */
          var ret = [];
          buffer.text_ = buffer.text_ || "";
          if (buffer.text_.length > 4) {
            var a = buffer.text_.length - 3;
            for (var i=0; i<a; i+=3) {
              ret.push(buffer.text_.substr(i, 3));
              ret.push({ type_: '1000 separator' });
            }
            ret.push(buffer.text_.substr(i));
          } else {
            ret.push(buffer.text_);
          }
          for (var p in buffer) { delete buffer[p]; }
          return ret;
        }
      }
    }
    //#endregion
  };

  //
  // texify: Take MhchemParser output and convert it to TeX
  //
  /** @type {Texify} */
  var texify = {
    go: function (input, isInner) {  // (recursive, max 4 levels)
      if (!input) { return ""; }
      var res = "";
      var cee = false;
      for (var i=0; i < input.length; i++) {
        var inputi = input[i];
        if (typeof inputi === "string") {
          res += inputi;
        } else {
          res += texify._go2(inputi);
          if (inputi.type_ === '1st-level escape') { cee = true; }
        }
      }
      if (!isInner && !cee && res) {
        res = "{" + res + "}";
      }
      return res;
    },
    _goInner: function (input) {
      if (!input) { return input; }
      return texify.go(input, true);
    },
    _go2: function (buf) {
      /** @type {undefined | string} */
      var res;
      switch (buf.type_) {
        case 'chemfive':
          res = "";
          var b5 = {
            a: texify._goInner(buf.a),
            b: texify._goInner(buf.b),
            p: texify._goInner(buf.p),
            o: texify._goInner(buf.o),
            q: texify._goInner(buf.q),
            d: texify._goInner(buf.d)
          };
          //
          // a
          //
          if (b5.a) {
            if (b5.a.match(/^[+\-]/)) { b5.a = "{"+b5.a+"}"; }
            res += b5.a + "\\,";
          }
          //
          // b and p
          //
          if (b5.b || b5.p) {
            res += "{\\vphantom{X}}";
            res += "^{\\hphantom{"+(b5.b||"")+"}}_{\\hphantom{"+(b5.p||"")+"}}";
            res += "{\\vphantom{X}}";
            // In the next two lines, I've removed \smash[t] (ron)
            // TODO: Revert \smash[t] when WebKit properly renders <mpadded> w/height="0"
            //res += "^{\\smash[t]{\\vphantom{2}}\\mathllap{"+(b5.b||"")+"}}";
            res += "^{\\vphantom{2}\\mathllap{"+(b5.b||"")+"}}";
            //res += "_{\\vphantom{2}\\mathllap{\\smash[t]{"+(b5.p||"")+"}}}";
            res += "_{\\vphantom{2}\\mathllap{"+(b5.p||"")+"}}";
          }
          //
          // o
          //
          if (b5.o) {
            if (b5.o.match(/^[+\-]/)) { b5.o = "{"+b5.o+"}"; }
            res += b5.o;
          }
          //
          // q and d
          //
          if (buf.dType === 'kv') {
            if (b5.d || b5.q) {
              res += "{\\vphantom{X}}";
            }
            if (b5.d) {
              res += "^{"+b5.d+"}";
            }
            if (b5.q) {
              // In the next line, I've removed \smash[t] (ron)
              // TODO: Revert \smash[t] when WebKit properly renders <mpadded> w/height="0"
              //res += "_{\\smash[t]{"+b5.q+"}}";
              res += "_{"+b5.q+"}";
            }
          } else if (buf.dType === 'oxidation') {
            if (b5.d) {
              res += "{\\vphantom{X}}";
              res += "^{"+b5.d+"}";
            }
            if (b5.q) {
              // A Firefox bug adds a bogus depth to <mphantom>, so we change \vphantom{X} to {}
              // TODO: Reinstate \vphantom{X} when the Firefox bug is fixed.
//              res += "{\\vphantom{X}}";
              res += "{{}}";
              // In the next line, I've removed \smash[t] (ron)
              // TODO: Revert \smash[t] when WebKit properly renders <mpadded> w/height="0"
              //res += "_{\\smash[t]{"+b5.q+"}}";
              res += "_{"+b5.q+"}";
            }
          } else {
            if (b5.q) {
              // TODO: Reinstate \vphantom{X} when the Firefox bug is fixed.
//              res += "{\\vphantom{X}}";
              res += "{{}}";
              // In the next line, I've removed \smash[t] (ron)
              // TODO: Revert \smash[t] when WebKit properly renders <mpadded> w/height="0"
              //res += "_{\\smash[t]{"+b5.q+"}}";
              res += "_{"+b5.q+"}";
            }
            if (b5.d) {
              // TODO: Reinstate \vphantom{X} when the Firefox bug is fixed.
//              res += "{\\vphantom{X}}";
              res += "{{}}";
              res += "^{"+b5.d+"}";
            }
          }
          break;
        case 'rm':
          res = "\\mathrm{"+buf.p1+"}";
          break;
        case 'text':
          if (buf.p1.match(/[\^_]/)) {
            buf.p1 = buf.p1.replace(" ", "~").replace("-", "\\text{-}");
            res = "\\mathrm{"+buf.p1+"}";
          } else {
            res = "\\text{"+buf.p1+"}";
          }
          break;
        case 'roman numeral':
          res = "\\mathrm{"+buf.p1+"}";
          break;
        case 'state of aggregation':
          res = "\\mskip2mu "+texify._goInner(buf.p1);
          break;
        case 'state of aggregation subscript':
          res = "\\mskip1mu "+texify._goInner(buf.p1);
          break;
        case 'bond':
          res = texify._getBond(buf.kind_);
          if (!res) {
            throw ["MhchemErrorBond", "mhchem Error. Unknown bond type (" + buf.kind_ + ")"];
          }
          break;
        case 'frac':
          var c = "\\frac{" + buf.p1 + "}{" + buf.p2 + "}";
          res = "\\mathchoice{\\textstyle"+c+"}{"+c+"}{"+c+"}{"+c+"}";
          break;
        case 'pu-frac':
          var d = "\\frac{" + texify._goInner(buf.p1) + "}{" + texify._goInner(buf.p2) + "}";
          res = "\\mathchoice{\\textstyle"+d+"}{"+d+"}{"+d+"}{"+d+"}";
          break;
        case 'tex-math':
          res = buf.p1 + " ";
          break;
        case 'frac-ce':
          res = "\\frac{" + texify._goInner(buf.p1) + "}{" + texify._goInner(buf.p2) + "}";
          break;
        case 'overset':
          res = "\\overset{" + texify._goInner(buf.p1) + "}{" + texify._goInner(buf.p2) + "}";
          break;
        case 'underset':
          res = "\\underset{" + texify._goInner(buf.p1) + "}{" + texify._goInner(buf.p2) + "}";
          break;
        case 'underbrace':
          res =  "\\underbrace{" + texify._goInner(buf.p1) + "}_{" + texify._goInner(buf.p2) + "}";
          break;
        case 'color':
          res = "{\\color{" + buf.color1 + "}{" + texify._goInner(buf.color2) + "}}";
          break;
        case 'color0':
          res = "\\color{" + buf.color + "}";
          break;
        case 'arrow':
          var b6 = {
            rd: texify._goInner(buf.rd),
            rq: texify._goInner(buf.rq)
          };
          var arrow = texify._getArrow(buf.r);
          if (b6.rq) { arrow += "[{\\rm " + b6.rq + "}]"; }
          if (b6.rd) {
            arrow += "{\\rm " + b6.rd + "}";
          } else {
            arrow += "{}";
          }
          res = arrow;
          break;
        case 'operator':
          res = texify._getOperator(buf.kind_);
          break;
        case '1st-level escape':
          res = buf.p1+" ";  // &, \\\\, \\hlin
          break;
        case 'space':
          res = " ";
          break;
        case 'entitySkip':
          res = "~";
          break;
        case 'pu-space-1':
          res = "~";
          break;
        case 'pu-space-2':
          res = "\\mkern3mu ";
          break;
        case '1000 separator':
          res = "\\mkern2mu ";
          break;
        case 'commaDecimal':
          res = "{,}";
          break;
          case 'comma enumeration L':
          res = "{"+buf.p1+"}\\mkern6mu ";
          break;
        case 'comma enumeration M':
          res = "{"+buf.p1+"}\\mkern3mu ";
          break;
        case 'comma enumeration S':
          res = "{"+buf.p1+"}\\mkern1mu ";
          break;
        case 'hyphen':
          res = "\\text{-}";
          break;
        case 'addition compound':
          res = "\\,{\\cdot}\\,";
          break;
        case 'electron dot':
          res = "\\mkern1mu \\text{\\textbullet}\\mkern1mu ";
          break;
        case 'KV x':
          res = "{\\times}";
          break;
        case 'prime':
          res = "\\prime ";
          break;
        case 'cdot':
          res = "\\cdot ";
          break;
        case 'tight cdot':
          res = "\\mkern1mu{\\cdot}\\mkern1mu ";
          break;
        case 'times':
          res = "\\times ";
          break;
        case 'circa':
          res = "{\\sim}";
          break;
        case '^':
          res = "uparrow";
          break;
        case 'v':
          res = "downarrow";
          break;
        case 'ellipsis':
          res = "\\ldots ";
          break;
        case '/':
          res = "/";
          break;
        case ' / ':
          res = "\\,/\\,";
          break;
        default:
          assertNever(buf);
          throw ["MhchemBugT", "mhchem bug T. Please report."];  // Missing texify rule or unknown MhchemParser output
      }
      assertString(res);
      return res;
    },
    _getArrow: function (a) {
      switch (a) {
        case "->": return "\\yields";
        case "\u2192": return "\\yields";
        case "\u27F6": return "\\yields";
        case "<-": return "\\yieldsLeft";
        case "<->": return "\\mesomerism";
        case "<-->": return "\\yieldsLeftRight";
        case "<=>": return "\\equilibrium";
        case "\u21CC": return "\\equilibrium";
        case "<=>>": return "\\equilibriumRight";
        case "<<=>": return "\\equilibriumLeft";
        default:
          assertNever(a);
          throw ["MhchemBugT", "mhchem bug T. Please report."];
      }
    },
    _getBond: function (a) {
      switch (a) {
        case "-": return "{-}";
        case "1": return "{-}";
        case "=": return "{=}";
        case "2": return "{=}";
        case "#": return "{\\equiv}";
        case "3": return "{\\equiv}";
        case "~": return "{\\tripleDash}";
        case "~-": return "{\\tripleDashOverLine}";
        case "~=": return "{\\tripleDashOverDoubleLine}";
        case "~--": return "{\\tripleDashOverDoubleLine}";
        case "-~-": return "{\\tripleDashBetweenDoubleLine}";
        case "...": return "{{\\cdot}{\\cdot}{\\cdot}}";
        case "....": return "{{\\cdot}{\\cdot}{\\cdot}{\\cdot}}";
        case "->": return "{\\rightarrow}";
        case "<-": return "{\\leftarrow}";
        case "<": return "{<}";
        case ">": return "{>}";
        default:
          assertNever(a);
          throw ["MhchemBugT", "mhchem bug T. Please report."];
      }
    },
    _getOperator: function (a) {
      switch (a) {
        case "+": return " {}+{} ";
        case "-": return " {}-{} ";
        case "=": return " {}={} ";
        case "<": return " {}<{} ";
        case ">": return " {}>{} ";
        case "<<": return " {}\\ll{} ";
        case ">>": return " {}\\gg{} ";
        case "\\pm": return " {}\\pm{} ";
        case "\\approx": return " {}\\approx{} ";
        case "$\\approx$": return " {}\\approx{} ";
        case "v": return " \\downarrow{} ";
        case "(v)": return " \\downarrow{} ";
        case "^": return " \\uparrow{} ";
        case "(^)": return " \\uparrow{} ";
        default:
          assertNever(a);
          throw ["MhchemBugT", "mhchem bug T. Please report."];
      }
    }
  };

  //
  // Helpers for code analysis
  // Will show type error at calling position
  //
  /** @param {number} a */
  function assertNever(a) {}
  /** @param {string} a */
  function assertString(a) {}

/* eslint-disable no-undef */

//////////////////////////////////////////////////////////////////////
// texvc.sty

// The texvc package contains macros available in mediawiki pages.
// We omit the functions deprecated at
// https://en.wikipedia.org/wiki/Help:Displaying_a_formula#Deprecated_syntax

// We also omit texvc's \O, which conflicts with \text{\O}

defineMacro("\\darr", "\\downarrow");
defineMacro("\\dArr", "\\Downarrow");
defineMacro("\\Darr", "\\Downarrow");
defineMacro("\\lang", "\\langle");
defineMacro("\\rang", "\\rangle");
defineMacro("\\uarr", "\\uparrow");
defineMacro("\\uArr", "\\Uparrow");
defineMacro("\\Uarr", "\\Uparrow");
defineMacro("\\N", "\\mathbb{N}");
defineMacro("\\R", "\\mathbb{R}");
defineMacro("\\Z", "\\mathbb{Z}");
defineMacro("\\alef", "\\aleph");
defineMacro("\\alefsym", "\\aleph");
defineMacro("\\bull", "\\bullet");
defineMacro("\\clubs", "\\clubsuit");
defineMacro("\\cnums", "\\mathbb{C}");
defineMacro("\\Complex", "\\mathbb{C}");
defineMacro("\\Dagger", "\\ddagger");
defineMacro("\\diamonds", "\\diamondsuit");
defineMacro("\\empty", "\\emptyset");
defineMacro("\\exist", "\\exists");
defineMacro("\\harr", "\\leftrightarrow");
defineMacro("\\hArr", "\\Leftrightarrow");
defineMacro("\\Harr", "\\Leftrightarrow");
defineMacro("\\hearts", "\\heartsuit");
defineMacro("\\image", "\\Im");
defineMacro("\\infin", "\\infty");
defineMacro("\\isin", "\\in");
defineMacro("\\larr", "\\leftarrow");
defineMacro("\\lArr", "\\Leftarrow");
defineMacro("\\Larr", "\\Leftarrow");
defineMacro("\\lrarr", "\\leftrightarrow");
defineMacro("\\lrArr", "\\Leftrightarrow");
defineMacro("\\Lrarr", "\\Leftrightarrow");
defineMacro("\\natnums", "\\mathbb{N}");
defineMacro("\\plusmn", "\\pm");
defineMacro("\\rarr", "\\rightarrow");
defineMacro("\\rArr", "\\Rightarrow");
defineMacro("\\Rarr", "\\Rightarrow");
defineMacro("\\real", "\\Re");
defineMacro("\\reals", "\\mathbb{R}");
defineMacro("\\Reals", "\\mathbb{R}");
defineMacro("\\sdot", "\\cdot");
defineMacro("\\sect", "\\S");
defineMacro("\\spades", "\\spadesuit");
defineMacro("\\sub", "\\subset");
defineMacro("\\sube", "\\subseteq");
defineMacro("\\supe", "\\supseteq");
defineMacro("\\thetasym", "\\vartheta");
defineMacro("\\weierp", "\\wp");

/* eslint-disable no-undef */

/****************************************************
 *
 *  physics.js
 *
 *  Implements the Physics Package for LaTeX input.
 *
 *  ---------------------------------------------------------------------
 *
 *  The original version of this file is licensed as follows:
 *  Copyright (c) 2015-2016 Kolen Cheung <https://github.com/ickc/MathJax-third-party-extensions>.
 *
 *  Licensed under the Apache License, Version 2.0 (the "License");
 *  you may not use this file except in compliance with the License.
 *  You may obtain a copy of the License at
 *
 *      http://www.apache.org/licenses/LICENSE-2.0
 *
 *  Unless required by applicable law or agreed to in writing, software
 *  distributed under the License is distributed on an "AS IS" BASIS,
 *  WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 *  See the License for the specific language governing permissions and
 *  limitations under the License.
 *
 *  ---------------------------------------------------------------------
 *
 *  This file has been revised from the original in the following ways:
 *  1. The interface is changed so that it can be called from Temml, not MathJax.
 *  2. \Re and \Im are not used, to avoid conflict with existing LaTeX letters.
 *
 *  This revision of the file is released under the MIT license.
 *  https://mit-license.org/
 */
defineMacro("\\quantity", "{\\left\\{ #1 \\right\\}}");
defineMacro("\\qty", "{\\left\\{ #1 \\right\\}}");
defineMacro("\\pqty", "{\\left( #1 \\right)}");
defineMacro("\\bqty", "{\\left[ #1 \\right]}");
defineMacro("\\vqty", "{\\left\\vert #1 \\right\\vert}");
defineMacro("\\Bqty", "{\\left\\{ #1 \\right\\}}");
defineMacro("\\absolutevalue", "{\\left\\vert #1 \\right\\vert}");
defineMacro("\\abs", "{\\left\\vert #1 \\right\\vert}");
defineMacro("\\norm", "{\\left\\Vert #1 \\right\\Vert}");
defineMacro("\\evaluated", "{\\left.#1 \\right\\vert}");
defineMacro("\\eval", "{\\left.#1 \\right\\vert}");
defineMacro("\\order", "{\\mathcal{O} \\left( #1 \\right)}");
defineMacro("\\commutator", "{\\left[ #1 , #2 \\right]}");
defineMacro("\\comm", "{\\left[ #1 , #2 \\right]}");
defineMacro("\\anticommutator", "{\\left\\{ #1 , #2 \\right\\}}");
defineMacro("\\acomm", "{\\left\\{ #1 , #2 \\right\\}}");
defineMacro("\\poissonbracket", "{\\left\\{ #1 , #2 \\right\\}}");
defineMacro("\\pb", "{\\left\\{ #1 , #2 \\right\\}}");
defineMacro("\\vectorbold", "{\\boldsymbol{ #1 }}");
defineMacro("\\vb", "{\\boldsymbol{ #1 }}");
defineMacro("\\vectorarrow", "{\\vec{\\boldsymbol{ #1 }}}");
defineMacro("\\va", "{\\vec{\\boldsymbol{ #1 }}}");
defineMacro("\\vectorunit", "{{\\boldsymbol{\\hat{ #1 }}}}");
defineMacro("\\vu", "{{\\boldsymbol{\\hat{ #1 }}}}");
defineMacro("\\dotproduct", "\\mathbin{\\boldsymbol\\cdot}");
defineMacro("\\vdot", "{\\boldsymbol\\cdot}");
defineMacro("\\crossproduct", "\\mathbin{\\boldsymbol\\times}");
defineMacro("\\cross", "\\mathbin{\\boldsymbol\\times}");
defineMacro("\\cp", "\\mathbin{\\boldsymbol\\times}");
defineMacro("\\gradient", "{\\boldsymbol\\nabla}");
defineMacro("\\grad", "{\\boldsymbol\\nabla}");
defineMacro("\\divergence", "{\\grad\\vdot}");
//defineMacro("\\div", "{\\grad\\vdot}"); Not included in Temml. Conflicts w/LaTeX \div
defineMacro("\\curl", "{\\grad\\cross}");
defineMacro("\\laplacian", "\\nabla^2");
defineMacro("\\tr", "{\\operatorname{tr}}");
defineMacro("\\Tr", "{\\operatorname{Tr}}");
defineMacro("\\rank", "{\\operatorname{rank}}");
defineMacro("\\erf", "{\\operatorname{erf}}");
defineMacro("\\Res", "{\\operatorname{Res}}");
defineMacro("\\principalvalue", "{\\mathcal{P}}");
defineMacro("\\pv", "{\\mathcal{P}}");
defineMacro("\\PV", "{\\operatorname{P.V.}}");
// Temml does not use the next two lines. They conflict with LaTeX letters.
//defineMacro("\\Re", "{\\operatorname{Re} \\left\\{ #1 \\right\\}}");
//defineMacro("\\Im", "{\\operatorname{Im} \\left\\{ #1 \\right\\}}");
defineMacro("\\qqtext", "{\\quad\\text{ #1 }\\quad}");
defineMacro("\\qq", "{\\quad\\text{ #1 }\\quad}");
defineMacro("\\qcomma", "{\\text{,}\\quad}");
defineMacro("\\qc", "{\\text{,}\\quad}");
defineMacro("\\qcc", "{\\quad\\text{c.c.}\\quad}");
defineMacro("\\qif", "{\\quad\\text{if}\\quad}");
defineMacro("\\qthen", "{\\quad\\text{then}\\quad}");
defineMacro("\\qelse", "{\\quad\\text{else}\\quad}");
defineMacro("\\qotherwise", "{\\quad\\text{otherwise}\\quad}");
defineMacro("\\qunless", "{\\quad\\text{unless}\\quad}");
defineMacro("\\qgiven", "{\\quad\\text{given}\\quad}");
defineMacro("\\qusing", "{\\quad\\text{using}\\quad}");
defineMacro("\\qassume", "{\\quad\\text{assume}\\quad}");
defineMacro("\\qsince", "{\\quad\\text{since}\\quad}");
defineMacro("\\qlet", "{\\quad\\text{let}\\quad}");
defineMacro("\\qfor", "{\\quad\\text{for}\\quad}");
defineMacro("\\qall", "{\\quad\\text{all}\\quad}");
defineMacro("\\qeven", "{\\quad\\text{even}\\quad}");
defineMacro("\\qodd", "{\\quad\\text{odd}\\quad}");
defineMacro("\\qinteger", "{\\quad\\text{integer}\\quad}");
defineMacro("\\qand", "{\\quad\\text{and}\\quad}");
defineMacro("\\qor", "{\\quad\\text{or}\\quad}");
defineMacro("\\qas", "{\\quad\\text{as}\\quad}");
defineMacro("\\qin", "{\\quad\\text{in}\\quad}");
defineMacro("\\differential", "{\\text{d}}");
defineMacro("\\dd", "{\\text{d}}");
defineMacro("\\derivative", "{\\frac{\\text{d}{ #1 }}{\\text{d}{ #2 }}}");
defineMacro("\\dv", "{\\frac{\\text{d}{ #1 }}{\\text{d}{ #2 }}}");
defineMacro("\\partialderivative", "{\\frac{\\partial{ #1 }}{\\partial{ #2 }}}");
defineMacro("\\variation", "{\\delta}");
defineMacro("\\var", "{\\delta}");
defineMacro("\\functionalderivative", "{\\frac{\\delta{ #1 }}{\\delta{ #2 }}}");
defineMacro("\\fdv", "{\\frac{\\delta{ #1 }}{\\delta{ #2 }}}");
defineMacro("\\innerproduct", "{\\left\\langle {#1} \\mid { #2} \\right\\rangle}");
defineMacro("\\outerproduct",
  "{\\left\\vert { #1 } \\right\\rangle\\left\\langle { #2} \\right\\vert}");
defineMacro("\\dyad",
  "{\\left\\vert { #1 } \\right\\rangle\\left\\langle { #2} \\right\\vert}");
defineMacro("\\ketbra",
  "{\\left\\vert { #1 } \\right\\rangle\\left\\langle { #2} \\right\\vert}");
defineMacro("\\op",
  "{\\left\\vert { #1 } \\right\\rangle\\left\\langle { #2} \\right\\vert}");
defineMacro("\\expectationvalue", "{\\left\\langle {#1 } \\right\\rangle}");
defineMacro("\\expval", "{\\left\\langle {#1 } \\right\\rangle}");
defineMacro("\\ev", "{\\left\\langle {#1 } \\right\\rangle}");
defineMacro("\\matrixelement",
  "{\\left\\langle{ #1 }\\right\\vert{ #2 }\\left\\vert{#3}\\right\\rangle}");
defineMacro("\\matrixel",
  "{\\left\\langle{ #1 }\\right\\vert{ #2 }\\left\\vert{#3}\\right\\rangle}");
defineMacro("\\mel",
  "{\\left\\langle{ #1 }\\right\\vert{ #2 }\\left\\vert{#3}\\right\\rangle}");

// Helper functions
function getHLines(parser) {
  // Return an array. The array length = number of hlines.
  // Each element in the array tells if the line is dashed.
  const hlineInfo = [];
  parser.consumeSpaces();
  let nxt = parser.fetch().text;
  if (nxt === "\\relax") {
    parser.consume();
    parser.consumeSpaces();
    nxt = parser.fetch().text;
  }
  while (nxt === "\\hline" || nxt === "\\hdashline") {
    parser.consume();
    hlineInfo.push(nxt === "\\hdashline");
    parser.consumeSpaces();
    nxt = parser.fetch().text;
  }
  return hlineInfo;
}

const validateAmsEnvironmentContext = context => {
  const settings = context.parser.settings;
  if (!settings.displayMode) {
    throw new ParseError(`{${context.envName}} can be used only in display mode.`);
  }
};

const sizeRegEx$1 = /([-+]?) *(\d+(?:\.\d*)?|\.\d+) *([a-z]{2})/;
const arrayGaps = macros => {
  let arraystretch = macros.get("\\arraystretch");
  if (typeof arraystretch !== "string") {
    arraystretch = stringFromArg(arraystretch.tokens);
  }
  arraystretch = isNaN(arraystretch) ? null : Number(arraystretch);
  let arraycolsepStr = macros.get("\\arraycolsep");
  if (typeof arraycolsepStr !== "string") {
    arraycolsepStr = stringFromArg(arraycolsepStr.tokens);
  }
  const match = sizeRegEx$1.exec(arraycolsepStr);
  const arraycolsep = match
    ? { number: +(match[1] + match[2]), unit: match[3] }
    : null;
  return [arraystretch, arraycolsep]
};

const checkCellForLabels = cell => {
  // Check if the author wrote a \tag{} inside this cell.
  let rowLabel = "";
  for (let i = 0; i < cell.length; i++) {
    if (cell[i].type === "label") {
      if (rowLabel) { throw new ParseError(("Multiple \\labels in one row")) }
      rowLabel = cell[i].string;
    }
  }
  return rowLabel
};

// autoTag (an argument to parseArray) can be one of three values:
// * undefined: Regular (not-top-level) array; no tags on each row
// * true: Automatic equation numbering, overridable by \tag
// * false: Tags allowed on each row, but no automatic numbering
// This function *doesn't* work with the "split" environment name.
function getAutoTag(name) {
  if (name.indexOf("ed") === -1) {
    return name.indexOf("*") === -1;
  }
  // return undefined;
}

/**
 * Parse the body of the environment, with rows delimited by \\ and
 * columns delimited by &, and create a nested list in row-major order
 * with one group per cell.  If given an optional argument scriptLevel
 * ("text", "display", etc.), then each cell is cast into that scriptLevel.
 */
function parseArray(
  parser,
  {
    cols, // [{ type: string , align: l|c|r|null }]
    envClasses, // align(ed|at|edat) | array | cases | cd | small | multline
    autoTag,        // boolean
    singleRow,      // boolean
    emptySingleRow, // boolean
    maxNumCols,     // number
    leqno,          // boolean
    arraystretch,   // number  | null
    arraycolsep     // size value | null
},
  scriptLevel
) {
  parser.gullet.beginGroup();
  if (!singleRow) {
    // \cr is equivalent to \\ without the optional size argument (see below)
    // TODO: provide helpful error when \cr is used outside array environment
    parser.gullet.macros.set("\\cr", "\\\\\\relax");
  }

  // Start group for first cell
  parser.gullet.beginGroup();

  let row = [];
  const body = [row];
  const rowGaps = [];
  const labels = [];

  const hLinesBeforeRow = [];

  const tags = (autoTag != null ? [] : undefined);

  // amsmath uses \global\@eqnswtrue and \global\@eqnswfalse to represent
  // whether this row should have an equation number.  Simulate this with
  // a \@eqnsw macro set to 1 or 0.
  function beginRow() {
    if (autoTag) {
      parser.gullet.macros.set("\\@eqnsw", "1", true);
    }
  }
  function endRow() {
    if (tags) {
      if (parser.gullet.macros.get("\\df@tag")) {
        tags.push(parser.subparse([new Token("\\df@tag")]));
        parser.gullet.macros.set("\\df@tag", undefined, true);
      } else {
        tags.push(Boolean(autoTag) &&
            parser.gullet.macros.get("\\@eqnsw") === "1");
      }
    }
  }
  beginRow();

  // Test for \hline at the top of the array.
  hLinesBeforeRow.push(getHLines(parser));

  while (true) {
    // Parse each cell in its own group (namespace)
    let cell = parser.parseExpression(false, singleRow ? "\\end" : "\\\\");
    parser.gullet.endGroup();
    parser.gullet.beginGroup();

    cell = {
      type: "ordgroup",
      mode: parser.mode,
      body: cell,
      semisimple: true
    };
    row.push(cell);
    const next = parser.fetch().text;
    if (next === "&") {
      if (maxNumCols && row.length === maxNumCols) {
        if (envClasses.includes("array")) {
          if (parser.settings.strict) {
            throw new ParseError("Too few columns " + "specified in the {array} column argument.",
              parser.nextToken)
          }
        } else if (maxNumCols === 2) {
          throw new ParseError("The split environment accepts no more than two columns",
            parser.nextToken);
        } else {
          throw new ParseError("The equation environment accepts only one column",
            parser.nextToken)
        }
      }
      parser.consume();
    } else if (next === "\\end") {
      endRow();
      // Arrays terminate newlines with `\crcr` which consumes a `\cr` if
      // the last line is empty.  However, AMS environments keep the
      // empty row if it's the only one.
      // NOTE: Currently, `cell` is the last item added into `row`.
      if (row.length === 1 && cell.body.length === 0 && (body.length > 1 || !emptySingleRow)) {
        body.pop();
      }
      labels.push(checkCellForLabels(cell.body));
      if (hLinesBeforeRow.length < body.length + 1) {
        hLinesBeforeRow.push([]);
      }
      break;
    } else if (next === "\\\\") {
      parser.consume();
      let size;
      // \def\Let@{\let\\\math@cr}
      // \def\math@cr{...\math@cr@}
      // \def\math@cr@{\new@ifnextchar[\math@cr@@{\math@cr@@[\z@]}}
      // \def\math@cr@@[#1]{...\math@cr@@@...}
      // \def\math@cr@@@{\cr}
      if (parser.gullet.future().text !== " ") {
        size = parser.parseSizeGroup(true);
      }
      rowGaps.push(size ? size.value : null);
      endRow();

      labels.push(checkCellForLabels(cell.body));

      // check for \hline(s) following the row separator
      hLinesBeforeRow.push(getHLines(parser));

      row = [];
      body.push(row);
      beginRow();
    } else {
      throw new ParseError("Expected & or \\\\ or \\cr or \\end", parser.nextToken);
    }
  }

  // End cell group
  parser.gullet.endGroup();
  // End array group defining \cr
  parser.gullet.endGroup();

  return {
    type: "array",
    mode: parser.mode,
    body,
    cols,
    rowGaps,
    hLinesBeforeRow,
    envClasses,
    autoTag,
    scriptLevel,
    tags,
    labels,
    leqno,
    arraystretch,
    arraycolsep
  };
}

// Decides on a scriptLevel for cells in an array according to whether the given
// environment name starts with the letter 'd'.
function dCellStyle(envName) {
  return envName.slice(0, 1) === "d" ? "display" : "text"
}

const alignMap = {
  c: "center ",
  l: "left ",
  r: "right "
};

const glue = group => {
  const glueNode = new mathMLTree.MathNode("mtd", []);
  glueNode.style = { padding: "0", width: "50%" };
  if (group.envClasses.includes("multline")) {
    glueNode.style.width = "7.5%";
  }
  return glueNode
};

const mathmlBuilder$7 = function(group, style) {
  const tbl = [];
  const numRows = group.body.length;
  const hlines = group.hLinesBeforeRow;

  for (let i = 0; i < numRows; i++) {
    const rw = group.body[i];
    const row = [];
    const cellLevel = group.scriptLevel === "text"
      ? StyleLevel.TEXT
      : group.scriptLevel === "script"
      ? StyleLevel.SCRIPT
      : StyleLevel.DISPLAY;

    for (let j = 0; j < rw.length; j++) {
      const mtd = new mathMLTree.MathNode(
        "mtd",
        [buildGroup$1(rw[j], style.withLevel(cellLevel))]
      );

      if (group.envClasses.includes("multline")) {
        const align = i === 0 ? "left" : i === numRows - 1 ? "right" : "center";
        mtd.setAttribute("columnalign", align);
        if (align !== "center") {
          mtd.classes.push("tml-" + align);
        }
      }
      row.push(mtd);
    }
    const numColumns = group.body[0].length;
    // Fill out a short row with empty <mtd> elements.
    for (let k = 0; k < numColumns - rw.length; k++) {
      row.push(new mathMLTree.MathNode("mtd", [], style));
    }
    if (group.autoTag) {
      const tag = group.tags[i];
      let tagElement;
      if (tag === true) {  // automatic numbering
        tagElement = new mathMLTree.MathNode("mtext", [new Span(["tml-eqn"])]);
      } else if (tag === false) {
        // \nonumber/\notag or starred environment
        tagElement = new mathMLTree.MathNode("mtext", [], []);
      } else {  // manual \tag
        tagElement = buildExpressionRow(tag[0].body, style.withLevel(cellLevel), true);
        tagElement = consolidateText(tagElement);
        tagElement.classes = ["tml-tag"];
      }
      if (tagElement) {
        row.unshift(glue(group));
        row.push(glue(group));
        if (group.leqno) {
          row[0].children.push(tagElement);
          row[0].classes.push("tml-left");
        } else {
          row[row.length - 1].children.push(tagElement);
          row[row.length - 1].classes.push("tml-right");
        }
      }
    }
    const mtr = new mathMLTree.MathNode("mtr", row, []);
    const label = group.labels.shift();
    if (label && group.tags && group.tags[i]) {
      mtr.setAttribute("id", label);
      if (Array.isArray(group.tags[i])) { mtr.classes.push("tml-tageqn"); }
    }

    // Write horizontal rules
    if (i === 0 && hlines[0].length > 0) {
      if (hlines[0].length === 2) {
        mtr.children.forEach(cell => { cell.style.borderTop = "0.15em double"; });
      } else {
        mtr.children.forEach(cell => {
          cell.style.borderTop = hlines[0][0] ? "0.06em dashed" : "0.06em solid";
        });
      }
    }
    if (hlines[i + 1].length > 0) {
      if (hlines[i + 1].length === 2) {
        mtr.children.forEach(cell => { cell.style.borderBottom = "0.15em double"; });
      } else {
        mtr.children.forEach(cell => {
          cell.style.borderBottom = hlines[i + 1][0] ? "0.06em dashed" : "0.06em solid";
        });
      }
    }
    tbl.push(mtr);
  }

  if (group.envClasses.length > 0) {
    if (group.arraystretch && group.arraystretch !== 1) {
      // In LaTeX, \arraystretch is a factor applied to a 12pt strut height.
      // It defines a baseline to baseline distance.
      // Here, we do an approximation of that approach.
      const pad = String(1.4 * group.arraystretch - 0.8) + "ex";
      for (let i = 0; i < tbl.length; i++) {
        for (let j = 0; j < tbl[i].children.length; j++) {
          tbl[i].children[j].style.paddingTop = pad;
          tbl[i].children[j].style.paddingBottom = pad;
        }
      }
    }
    let sidePadding = group.envClasses.includes("abut")
      ? "0"
      : group.envClasses.includes("cases")
      ? "0"
      : group.envClasses.includes("small")
      ? "0.1389"
      : group.envClasses.includes("cd")
      ? "0.25"
      : "0.4"; // default side padding
    let sidePadUnit = "em";
    if (group.arraycolsep) {
      const arraySidePad = calculateSize(group.arraycolsep, style);
      sidePadding = arraySidePad.number.toFixed(4);
      sidePadUnit = arraySidePad.unit;
    }

    const numCols = tbl.length === 0 ? 0 : tbl[0].children.length;

    const sidePad = (j, hand) => {
      if (j === 0 && hand === 0) { return "0" }
      if (j === numCols - 1 && hand === 1) { return "0" }
      if (group.envClasses[0] !== "align") { return sidePadding }
      if (hand === 1) { return "0" }
      if (group.autoTag) {
        return (j % 2) ? "1" : "0"
      } else {
        return (j % 2) ? "0" : "1"
      }
    };

    // Side padding
    for (let i = 0; i < tbl.length; i++) {
      for (let j = 0; j < tbl[i].children.length; j++) {
        tbl[i].children[j].style.paddingLeft = `${sidePad(j, 0)}${sidePadUnit}`;
        tbl[i].children[j].style.paddingRight = `${sidePad(j, 1)}${sidePadUnit}`;
      }
    }

    // Justification
    const align = group.envClasses.includes("align") || group.envClasses.includes("alignat");
    for (let i = 0; i < tbl.length; i++) {
      const row = tbl[i];
      if (align) {
        for (let j = 0; j < row.children.length; j++) {
          // Chromium does not recognize text-align: left. Use -webkit-
          // TODO: Remove -webkit- when Chromium no longer needs it.
          row.children[j].classes = ["tml-" + (j % 2 ? "left" : "right")];
        }
        if (group.autoTag) {
          const k = group.leqno ? 0 : row.children.length - 1;
          row.children[k].classes = ["tml-" + (group.leqno ? "left" : "right")];
        }
      }
      if (row.children.length > 1 && group.envClasses.includes("cases")) {
        row.children[1].style.paddingLeft = "1em";
      }

      if (group.envClasses.includes("cases") || group.envClasses.includes("subarray")) {
        for (const cell of row.children) {
          cell.classes.push("tml-left");
        }
      }
    }
  } else {
    // Set zero padding on side of the matrix
    for (let i = 0; i < tbl.length; i++) {
      tbl[i].children[0].style.paddingLeft = "0em";
      if (tbl[i].children.length === tbl[0].children.length) {
        tbl[i].children[tbl[i].children.length - 1].style.paddingRight = "0em";
      }
    }
  }

  let table = new mathMLTree.MathNode("mtable", tbl);
  if (group.envClasses.length > 0) {
    // Top & bottom padding
    if (group.envClasses.includes("jot")) {
      table.classes.push("tml-jot");
    } else if (group.envClasses.includes("small")) {
      table.classes.push("tml-small");
    }
  }
  if (group.scriptLevel === "display") { table.setAttribute("displaystyle", "true"); }

  if (group.autoTag || group.envClasses.includes("multline")) {
    table.style.width = "100%";
  }

  // Column separator lines and column alignment
  let align = "";

  if (group.cols && group.cols.length > 0) {
    const cols = group.cols;
    let prevTypeWasAlign = false;
    let iStart = 0;
    let iEnd = cols.length;

    while (cols[iStart].type === "separator") {
      iStart += 1;
    }
    while (cols[iEnd - 1].type === "separator") {
      iEnd -= 1;
    }

    if (cols[0].type === "separator") {
      const sep = cols[1].type === "separator"
        ? "0.15em double"
        : cols[0].separator === "|"
        ? "0.06em solid "
        : "0.06em dashed ";
      for (const row of table.children) {
        row.children[0].style.borderLeft = sep;
      }
    }
    let iCol = group.autoTag ? 0 : -1;
    for (let i = iStart; i < iEnd; i++) {
      if (cols[i].type === "align") {
        const colAlign = alignMap[cols[i].align];
        align += colAlign;
        iCol += 1;
        for (const row of table.children) {
          if (colAlign.trim() !== "center" && iCol < row.children.length) {
            row.children[iCol].classes = ["tml-" + colAlign.trim()];
          }
        }
        prevTypeWasAlign = true;
      } else if (cols[i].type === "separator") {
        // MathML accepts only single lines between cells.
        // So we read only the first of consecutive separators.
        if (prevTypeWasAlign) {
          const sep = cols[i + 1].type === "separator"
            ? "0.15em double"
            : cols[i].separator === "|"
            ? "0.06em solid"
            : "0.06em dashed";
          for (const row of table.children) {
            if (iCol < row.children.length) {
              row.children[iCol].style.borderRight = sep;
            }
          }
        }
        prevTypeWasAlign = false;
      }
    }
    if (cols[cols.length - 1].type === "separator") {
      const sep = cols[cols.length - 2].type === "separator"
        ? "0.15em double"
        : cols[cols.length - 1].separator === "|"
        ? "0.06em solid"
        : "0.06em dashed";
      for (const row of table.children) {
        row.children[row.children.length - 1].style.borderRight = sep;
        row.children[row.children.length - 1].style.paddingRight = "0.4em";
      }
    }
  }
  if (group.autoTag) {
     // allow for glue cells on each side
    align = "left " + (align.length > 0 ? align : "center ") + "right ";
  }
  if (align) {
    // Firefox reads this attribute, not the -webkit-left|right written above.
    // TODO: When Chrome no longer needs "-webkit-", use CSS and delete the next line.
    table.setAttribute("columnalign", align.trim());
  }

  if (group.envClasses.includes("small")) {
    // A small array. Wrap in scriptstyle.
    table = new mathMLTree.MathNode("mstyle", [table]);
    table.setAttribute("scriptlevel", "1");
  }

  return table
};

// Convenience function for align, align*, aligned, alignat, alignat*, alignedat, split.
const alignedHandler = function(context, args) {
  if (context.envName.indexOf("ed") === -1) {
    validateAmsEnvironmentContext(context);
  }
  const isSplit = context.envName === "split";
  const cols = [];
  const res = parseArray(
    context.parser,
    {
      cols,
      emptySingleRow: true,
      autoTag: isSplit ? undefined : getAutoTag(context.envName),
      envClasses: ["abut", "jot"], // set row spacing & provisional column spacing
      maxNumCols: context.envName === "split" ? 2 : undefined,
      leqno: context.parser.settings.leqno
    },
    "display"
  );

  // Determining number of columns.
  // 1. If the first argument is given, we use it as a number of columns,
  //    and makes sure that each row doesn't exceed that number.
  // 2. Otherwise, just count number of columns = maximum number
  //    of cells in each row ("aligned" mode -- isAligned will be true).
  //
  // At the same time, prepend empty group {} at beginning of every second
  // cell in each row (starting with second cell) so that operators become
  // binary.  This behavior is implemented in amsmath's \start@aligned.
  let numMaths;
  let numCols = 0;
  const isAlignedAt = context.envName.indexOf("at") > -1;
  if (args[0] && isAlignedAt) {
    // alignat environment takes an argument w/ number of columns
    let arg0 = "";
    for (let i = 0; i < args[0].body.length; i++) {
      const textord = assertNodeType(args[0].body[i], "textord");
      arg0 += textord.text;
    }
    if (isNaN(arg0)) {
      throw new ParseError("The alignat enviroment requires a numeric first argument.")
    }
    numMaths = Number(arg0);
    numCols = numMaths * 2;
  }
  res.body.forEach(function(row) {
    if (isAlignedAt) {
      // Case 1
      const curMaths = row.length / 2;
      if (numMaths < curMaths) {
        throw new ParseError(
          "Too many math in a row: " + `expected ${numMaths}, but got ${curMaths}`,
          row[0]
        );
      }
    } else if (numCols < row.length) {
      // Case 2
      numCols = row.length;
    }
  });

  // Adjusting alignment.
  // In aligned mode, we add one \qquad between columns;
  // otherwise we add nothing.
  for (let i = 0; i < numCols; ++i) {
    let align = "r";
    if (i % 2 === 1) {
      align = "l";
    }
    cols[i] = {
      type: "align",
      align: align
    };
  }
  if (context.envName === "split") ; else if (isAlignedAt) {
    res.envClasses.push("alignat"); // Sets justification
  } else {
    res.envClasses[0] = "align"; // Sets column spacing & justification
  }
  return res;
};

// Arrays are part of LaTeX, defined in lttab.dtx so its documentation
// is part of the source2e.pdf file of LaTeX2e source documentation.
// {darray} is an {array} environment where cells are set in \displaystyle,
// as defined in nccmath.sty.
defineEnvironment({
  type: "array",
  names: ["array", "darray"],
  props: {
    numArgs: 1
  },
  handler(context, args) {
    // Since no types are specified above, the two possibilities are
    // - The argument is wrapped in {} or [], in which case Parser's
    //   parseGroup() returns an "ordgroup" wrapping some symbol node.
    // - The argument is a bare symbol node.
    const symNode = checkSymbolNodeType(args[0]);
    const colalign = symNode ? [args[0]] : assertNodeType(args[0], "ordgroup").body;
    const cols = colalign.map(function(nde) {
      const node = assertSymbolNodeType(nde);
      const ca = node.text;
      if ("lcr".indexOf(ca) !== -1) {
        return {
          type: "align",
          align: ca
        };
      } else if (ca === "|") {
        return {
          type: "separator",
          separator: "|"
        };
      } else if (ca === ":") {
        return {
          type: "separator",
          separator: ":"
        };
      }
      throw new ParseError("Unknown column alignment: " + ca, nde);
    });
    const [arraystretch, arraycolsep] = arrayGaps(context.parser.gullet.macros);
    const res = {
      cols,
      envClasses: ["array"],
      maxNumCols: cols.length,
      arraystretch,
      arraycolsep
    };
    return parseArray(context.parser, res, dCellStyle(context.envName));
  },
  mathmlBuilder: mathmlBuilder$7
});

// The matrix environments of amsmath builds on the array environment
// of LaTeX, which is discussed above.
// The mathtools package adds starred versions of the same environments.
// These have an optional argument to choose left|center|right justification.
defineEnvironment({
  type: "array",
  names: [
    "matrix",
    "pmatrix",
    "bmatrix",
    "Bmatrix",
    "vmatrix",
    "Vmatrix",
    "matrix*",
    "pmatrix*",
    "bmatrix*",
    "Bmatrix*",
    "vmatrix*",
    "Vmatrix*"
  ],
  props: {
    numArgs: 0
  },
  handler(context) {
    const delimiters = {
      matrix: null,
      pmatrix: ["(", ")"],
      bmatrix: ["[", "]"],
      Bmatrix: ["\\{", "\\}"],
      vmatrix: ["|", "|"],
      Vmatrix: ["\\Vert", "\\Vert"]
    }[context.envName.replace("*", "")];
    // \hskip -\arraycolsep in amsmath
    let colAlign = "c";
    const payload = {
      envClasses: [],
      cols: []
    };
    if (context.envName.charAt(context.envName.length - 1) === "*") {
      // It's one of the mathtools starred functions.
      // Parse the optional alignment argument.
      const parser = context.parser;
      parser.consumeSpaces();
      if (parser.fetch().text === "[") {
        parser.consume();
        parser.consumeSpaces();
        colAlign = parser.fetch().text;
        if ("lcr".indexOf(colAlign) === -1) {
          throw new ParseError("Expected l or c or r", parser.nextToken);
        }
        parser.consume();
        parser.consumeSpaces();
        parser.expect("]");
        parser.consume();
        payload.cols = [];
      }
    }
    const res = parseArray(context.parser, payload, "text");
    res.cols = new Array(res.body[0].length).fill({ type: "align", align: colAlign });
    const [arraystretch, arraycolsep] = arrayGaps(context.parser.gullet.macros);
    return delimiters
      ? {
        type: "leftright",
        mode: context.mode,
        body: [res],
        left: delimiters[0],
        right: delimiters[1],
        rightColor: undefined, // \right uninfluenced by \color in array
        arraystretch,
        arraycolsep
      }
      : res;
  },
  mathmlBuilder: mathmlBuilder$7
});

defineEnvironment({
  type: "array",
  names: ["smallmatrix"],
  props: {
    numArgs: 0
  },
  handler(context) {
    const payload = { type: "small" };
    const res = parseArray(context.parser, payload, "script");
    res.envClasses = ["small"];
    return res;
  },
  mathmlBuilder: mathmlBuilder$7
});

defineEnvironment({
  type: "array",
  names: ["subarray"],
  props: {
    numArgs: 1
  },
  handler(context, args) {
    // Parsing of {subarray} is similar to {array}
    const symNode = checkSymbolNodeType(args[0]);
    const colalign = symNode ? [args[0]] : assertNodeType(args[0], "ordgroup").body;
    const cols = colalign.map(function(nde) {
      const node = assertSymbolNodeType(nde);
      const ca = node.text;
      // {subarray} only recognizes "l" & "c"
      if ("lc".indexOf(ca) !== -1) {
        return {
          type: "align",
          align: ca
        };
      }
      throw new ParseError("Unknown column alignment: " + ca, nde);
    });
    if (cols.length > 1) {
      throw new ParseError("{subarray} can contain only one column");
    }
    let res = {
      cols,
      envClasses: ["small"]
    };
    res = parseArray(context.parser, res, "script");
    if (res.body.length > 0 && res.body[0].length > 1) {
      throw new ParseError("{subarray} can contain only one column");
    }
    return res;
  },
  mathmlBuilder: mathmlBuilder$7
});

// A cases environment (in amsmath.sty) is almost equivalent to
// \def
// \left\{\begin{array}{@{}l@{\quad}l@{}} … \end{array}\right.
// {dcases} is a {cases} environment where cells are set in \displaystyle,
// as defined in mathtools.sty.
// {rcases} is another mathtools environment. It's brace is on the right side.
defineEnvironment({
  type: "array",
  names: ["cases", "dcases", "rcases", "drcases"],
  props: {
    numArgs: 0
  },
  handler(context) {
    const payload = {
      cols: [],
      envClasses: ["cases"]
    };
    const res = parseArray(context.parser, payload, dCellStyle(context.envName));
    return {
      type: "leftright",
      mode: context.mode,
      body: [res],
      left: context.envName.indexOf("r") > -1 ? "." : "\\{",
      right: context.envName.indexOf("r") > -1 ? "\\}" : ".",
      rightColor: undefined
    };
  },
  mathmlBuilder: mathmlBuilder$7
});

// In the align environment, one uses ampersands, &, to specify number of
// columns in each row, and to locate spacing between each column.
// align gets automatic numbering. align* and aligned do not.
// The alignedat environment can be used in math mode.
defineEnvironment({
  type: "array",
  names: ["align", "align*", "aligned", "split"],
  props: {
    numArgs: 0
  },
  handler: alignedHandler,
  mathmlBuilder: mathmlBuilder$7
});

// alignat environment is like an align environment, but one must explicitly
// specify maximum number of columns in each row, and can adjust where spacing occurs.
defineEnvironment({
  type: "array",
  names: ["alignat", "alignat*", "alignedat"],
  props: {
    numArgs: 1
  },
  handler: alignedHandler,
  mathmlBuilder: mathmlBuilder$7
});

// A gathered environment is like an array environment with one centered
// column, but where rows are considered lines so get \jot line spacing
// and contents are set in \displaystyle.
defineEnvironment({
  type: "array",
  names: ["gathered", "gather", "gather*"],
  props: {
    numArgs: 0
  },
  handler(context) {
    if (context.envName !== "gathered") {
      validateAmsEnvironmentContext(context);
    }
    const res = {
      cols: [],
      envClasses: ["abut", "jot"],
      autoTag: getAutoTag(context.envName),
      emptySingleRow: true,
      leqno: context.parser.settings.leqno
    };
    return parseArray(context.parser, res, "display");
  },
  mathmlBuilder: mathmlBuilder$7
});

defineEnvironment({
  type: "array",
  names: ["equation", "equation*"],
  props: {
    numArgs: 0
  },
  handler(context) {
    validateAmsEnvironmentContext(context);
    const res = {
      autoTag: getAutoTag(context.envName),
      emptySingleRow: true,
      singleRow: true,
      maxNumCols: 1,
      envClasses: ["align"],
      leqno: context.parser.settings.leqno
    };
    return parseArray(context.parser, res, "display");
  },
  mathmlBuilder: mathmlBuilder$7
});

defineEnvironment({
  type: "array",
  names: ["multline", "multline*"],
  props: {
    numArgs: 0
  },
  handler(context) {
    validateAmsEnvironmentContext(context);
    const res = {
      autoTag: context.envName === "multline",
      maxNumCols: 1,
      envClasses: ["jot", "multline"],
      leqno: context.parser.settings.leqno
    };
    return parseArray(context.parser, res, "display");
  },
  mathmlBuilder: mathmlBuilder$7
});

defineEnvironment({
  type: "array",
  names: ["CD"],
  props: {
    numArgs: 0
  },
  handler(context) {
    validateAmsEnvironmentContext(context);
    return parseCD(context.parser);
  },
  mathmlBuilder: mathmlBuilder$7
});

// Catch \hline outside array environment
defineFunction({
  type: "text", // Doesn't matter what this is.
  names: ["\\hline", "\\hdashline"],
  props: {
    numArgs: 0,
    allowedInText: true,
    allowedInMath: true
  },
  handler(context, args) {
    throw new ParseError(`${context.funcName} valid only within array environment`);
  }
});

const environments = _environments;

// Environment delimiters. HTML/MathML rendering is defined in the corresponding
// defineEnvironment definitions.
defineFunction({
  type: "environment",
  names: ["\\begin", "\\end"],
  props: {
    numArgs: 1,
    argTypes: ["text"]
  },
  handler({ parser, funcName }, args) {
    const nameGroup = args[0];
    if (nameGroup.type !== "ordgroup") {
      throw new ParseError("Invalid environment name", nameGroup);
    }
    let envName = "";
    for (let i = 0; i < nameGroup.body.length; ++i) {
      envName += assertNodeType(nameGroup.body[i], "textord").text;
    }

    if (funcName === "\\begin") {
      // begin...end is similar to left...right
      if (!Object.prototype.hasOwnProperty.call(environments, envName )) {
        throw new ParseError("No such environment: " + envName, nameGroup);
      }
      // Build the environment object. Arguments and other information will
      // be made available to the begin and end methods using properties.
      const env = environments[envName];
      const { args, optArgs } = parser.parseArguments("\\begin{" + envName + "}", env);
      const context = {
        mode: parser.mode,
        envName,
        parser
      };
      const result = env.handler(context, args, optArgs);
      parser.expect("\\end", false);
      const endNameToken = parser.nextToken;
      const end = assertNodeType(parser.parseFunction(), "environment");
      if (end.name !== envName) {
        throw new ParseError(
          `Mismatch: \\begin{${envName}} matched by \\end{${end.name}}`,
          endNameToken
        );
      }
      return result;
    }

    return {
      type: "environment",
      mode: parser.mode,
      name: envName,
      nameGroup
    };
  }
});

defineFunction({
  type: "envTag",
  names: ["\\env@tag"],
  props: {
    numArgs: 1,
    argTypes: ["math"]
  },
  handler({ parser }, args) {
    return {
      type: "envTag",
      mode: parser.mode,
      body: args[0]
    };
  },
  mathmlBuilder(group, style) {
    return new mathMLTree.MathNode("mrow");
  }
});

defineFunction({
  type: "noTag",
  names: ["\\env@notag"],
  props: {
    numArgs: 0
  },
  handler({ parser }) {
    return {
      type: "noTag",
      mode: parser.mode
    };
  },
  mathmlBuilder(group, style) {
    return new mathMLTree.MathNode("mrow");
  }
});

const isLongVariableName = (group, font) => {
  if (font !== "mathrm" || group.body.type !== "ordgroup" || group.body.body.length === 1) {
    return false
  }
  if (group.body.body[0].type !== "mathord") { return false }
  for (let i = 1; i < group.body.body.length; i++) {
    const parseNodeType = group.body.body[i].type;
    if (!(parseNodeType ===  "mathord" ||
    (parseNodeType ===  "textord" && !isNaN(group.body.body[i].text)))) {
      return false
    }
  }
  return true
};

const mathmlBuilder$6 = (group, style) => {
  const font = group.font;
  const newStyle = style.withFont(font);
  const mathGroup = buildGroup$1(group.body, newStyle);

  if (mathGroup.children.length === 0) { return mathGroup } // empty group, e.g., \mathrm{}
  if (font === "boldsymbol" && ["mo", "mpadded", "mrow"].includes(mathGroup.type)) {
    mathGroup.style.fontWeight = "bold";
    return mathGroup
  }
  // Check if it is possible to consolidate elements into a single <mi> element.
  if (isLongVariableName(group, font)) {
    // This is a \mathrm{…} group. It gets special treatment because symbolsOrd.js
    // wraps <mi> elements with <mrow>s to work around a Firefox bug.
    const mi = mathGroup.children[0].children[0];
    delete mi.attributes.mathvariant;
    for (let i = 1; i < mathGroup.children.length; i++) {
      mi.children[0].text += mathGroup.children[i].type === "mn"
        ? mathGroup.children[i].children[0].text
        : mathGroup.children[i].children[0].children[0].text;
    }
    // Wrap in a <mrow> to prevent the same Firefox bug.
    const bogus = new mathMLTree.MathNode("mtext", new mathMLTree.TextNode("\u200b"));
    return new mathMLTree.MathNode("mrow", [bogus, mi])
  }
  let canConsolidate = mathGroup.children[0].type === "mo";
  for (let i = 1; i < mathGroup.children.length; i++) {
    if (mathGroup.children[i].type === "mo" && font === "boldsymbol") {
      mathGroup.children[i].style.fontWeight = "bold";
    }
    if (mathGroup.children[i].type !== "mi") { canConsolidate = false; }
    const localVariant = mathGroup.children[i].attributes &&
      mathGroup.children[i].attributes.mathvariant || "";
    if (localVariant !== "normal") { canConsolidate = false; }
  }
  if (!canConsolidate) { return mathGroup }
  // Consolidate the <mi> elements.
  const mi = mathGroup.children[0];
  for (let i = 1; i < mathGroup.children.length; i++) {
    mi.children.push(mathGroup.children[i].children[0]);
  }
  if (mi.attributes.mathvariant && mi.attributes.mathvariant === "normal") {
    // Workaround for a Firefox bug that renders spurious space around
    // a <mi mathvariant="normal">
    // Ref: https://bugs.webkit.org/show_bug.cgi?id=129097
    // We insert a text node that contains a zero-width space and wrap in an mrow.
    // TODO: Get rid of this <mi> workaround when the Firefox bug is fixed.
    const bogus = new mathMLTree.MathNode("mtext", new mathMLTree.TextNode("\u200b"));
    return new mathMLTree.MathNode("mrow", [bogus, mi])
  }
  return mi
};

const fontAliases = {
  "\\Bbb": "\\mathbb",
  "\\bold": "\\mathbf",
  "\\frak": "\\mathfrak",
  "\\bm": "\\boldsymbol"
};

defineFunction({
  type: "font",
  names: [
    // styles
    "\\mathrm",
    "\\mathit",
    "\\mathbf",
    "\\mathnormal",
    "\\up@greek",
    "\\boldsymbol",

    // families
    "\\mathbb",
    "\\mathcal",
    "\\mathfrak",
    "\\mathscr",
    "\\mathsf",
    "\\mathsfit",
    "\\mathtt",

    // aliases
    "\\Bbb",
    "\\bm",
    "\\bold",
    "\\frak"
  ],
  props: {
    numArgs: 1,
    allowedInArgument: true
  },
  handler: ({ parser, funcName }, args) => {
    const body = normalizeArgument(args[0]);
    let func = funcName;
    if (func in fontAliases) {
      func = fontAliases[func];
    }
    return {
      type: "font",
      mode: parser.mode,
      font: func.slice(1),
      body
    };
  },
  mathmlBuilder: mathmlBuilder$6
});

// Old font changing functions
defineFunction({
  type: "font",
  names: ["\\rm", "\\sf", "\\tt", "\\bf", "\\it", "\\cal"],
  props: {
    numArgs: 0,
    allowedInText: true
  },
  handler: ({ parser, funcName, breakOnTokenText }, args) => {
    const { mode } = parser;
    const body = parser.parseExpression(true, breakOnTokenText, true);
    const fontStyle = `math${funcName.slice(1)}`;

    return {
      type: "font",
      mode: mode,
      font: fontStyle,
      body: {
        type: "ordgroup",
        mode: parser.mode,
        body
      }
    };
  },
  mathmlBuilder: mathmlBuilder$6
});

const stylArray = ["display", "text", "script", "scriptscript"];
const scriptLevel = { auto: -1, display: 0, text: 0, script: 1, scriptscript: 2 };

const mathmlBuilder$5 = (group, style) => {
  // Track the scriptLevel of the numerator and denominator.
  // We may need that info for \mathchoice or for adjusting em dimensions.
  const childOptions = group.scriptLevel === "auto"
    ? style.incrementLevel()
    : group.scriptLevel === "display"
    ? style.withLevel(StyleLevel.TEXT)
    : group.scriptLevel === "text"
    ? style.withLevel(StyleLevel.SCRIPT)
    : style.withLevel(StyleLevel.SCRIPTSCRIPT);

  // Chromium (wrongly) continues to shrink fractions beyond scriptscriptlevel.
  // So we check for levels that Chromium shrinks too small.
  // If necessary, set an explicit fraction depth.
  const numer = buildGroup$1(group.numer, childOptions);
  const denom = buildGroup$1(group.denom, childOptions);
  if (style.level === 3) {
    numer.style.mathDepth = "2";
    numer.setAttribute("scriptlevel", "2");
    denom.style.mathDepth = "2";
    denom.setAttribute("scriptlevel", "2");
  }

  let node = new mathMLTree.MathNode("mfrac", [numer, denom]);

  if (!group.hasBarLine) {
    node.setAttribute("linethickness", "0px");
  } else if (group.barSize) {
    const ruleWidth = calculateSize(group.barSize, style);
    node.setAttribute("linethickness", ruleWidth.number + ruleWidth.unit);
  }

  if (group.leftDelim != null || group.rightDelim != null) {
    const withDelims = [];

    if (group.leftDelim != null) {
      const leftOp = new mathMLTree.MathNode("mo", [
        new mathMLTree.TextNode(group.leftDelim.replace("\\", ""))
      ]);
      leftOp.setAttribute("fence", "true");
      withDelims.push(leftOp);
    }

    withDelims.push(node);

    if (group.rightDelim != null) {
      const rightOp = new mathMLTree.MathNode("mo", [
        new mathMLTree.TextNode(group.rightDelim.replace("\\", ""))
      ]);
      rightOp.setAttribute("fence", "true");
      withDelims.push(rightOp);
    }

    node = makeRow(withDelims);
  }

  if (group.scriptLevel !== "auto") {
    node = new mathMLTree.MathNode("mstyle", [node]);
    node.setAttribute("displaystyle", String(group.scriptLevel === "display"));
    node.setAttribute("scriptlevel", scriptLevel[group.scriptLevel]);
  }

  return node;
};

defineFunction({
  type: "genfrac",
  names: [
    "\\dfrac",
    "\\frac",
    "\\tfrac",
    "\\dbinom",
    "\\binom",
    "\\tbinom",
    "\\\\atopfrac", // can’t be entered directly
    "\\\\bracefrac",
    "\\\\brackfrac" // ditto
  ],
  props: {
    numArgs: 2,
    allowedInArgument: true
  },
  handler: ({ parser, funcName }, args) => {
    const numer = args[0];
    const denom = args[1];
    let hasBarLine = false;
    let leftDelim = null;
    let rightDelim = null;
    let scriptLevel = "auto";

    switch (funcName) {
      case "\\dfrac":
      case "\\frac":
      case "\\tfrac":
        hasBarLine = true;
        break;
      case "\\\\atopfrac":
        hasBarLine = false;
        break;
      case "\\dbinom":
      case "\\binom":
      case "\\tbinom":
        leftDelim = "(";
        rightDelim = ")";
        break;
      case "\\\\bracefrac":
        leftDelim = "\\{";
        rightDelim = "\\}";
        break;
      case "\\\\brackfrac":
        leftDelim = "[";
        rightDelim = "]";
        break;
      default:
        throw new Error("Unrecognized genfrac command");
    }

    switch (funcName) {
      case "\\dfrac":
      case "\\dbinom":
        scriptLevel = "display";
        break;
      case "\\tfrac":
      case "\\tbinom":
        scriptLevel = "text";
        break;
    }

    return {
      type: "genfrac",
      mode: parser.mode,
      continued: false,
      numer,
      denom,
      hasBarLine,
      leftDelim,
      rightDelim,
      scriptLevel,
      barSize: null
    };
  },
  mathmlBuilder: mathmlBuilder$5
});

defineFunction({
  type: "genfrac",
  names: ["\\cfrac"],
  props: {
    numArgs: 2
  },
  handler: ({ parser, funcName }, args) => {
    const numer = args[0];
    const denom = args[1];

    return {
      type: "genfrac",
      mode: parser.mode,
      continued: true,
      numer,
      denom,
      hasBarLine: true,
      leftDelim: null,
      rightDelim: null,
      scriptLevel: "display",
      barSize: null
    };
  }
});

// Infix generalized fractions -- these are not rendered directly, but replaced
// immediately by one of the variants above.
defineFunction({
  type: "infix",
  names: ["\\over", "\\choose", "\\atop", "\\brace", "\\brack"],
  props: {
    numArgs: 0,
    infix: true
  },
  handler({ parser, funcName, token }) {
    let replaceWith;
    switch (funcName) {
      case "\\over":
        replaceWith = "\\frac";
        break;
      case "\\choose":
        replaceWith = "\\binom";
        break;
      case "\\atop":
        replaceWith = "\\\\atopfrac";
        break;
      case "\\brace":
        replaceWith = "\\\\bracefrac";
        break;
      case "\\brack":
        replaceWith = "\\\\brackfrac";
        break;
      default:
        throw new Error("Unrecognized infix genfrac command");
    }
    return {
      type: "infix",
      mode: parser.mode,
      replaceWith,
      token
    };
  }
});

const delimFromValue = function(delimString) {
  let delim = null;
  if (delimString.length > 0) {
    delim = delimString;
    delim = delim === "." ? null : delim;
  }
  return delim;
};

defineFunction({
  type: "genfrac",
  names: ["\\genfrac"],
  props: {
    numArgs: 6,
    allowedInArgument: true,
    argTypes: ["math", "math", "size", "text", "math", "math"]
  },
  handler({ parser }, args) {
    const numer = args[4];
    const denom = args[5];

    // Look into the parse nodes to get the desired delimiters.
    const leftNode = normalizeArgument(args[0]);
    const leftDelim = leftNode.type === "atom" && leftNode.family === "open"
      ? delimFromValue(leftNode.text)
      : null;
    const rightNode = normalizeArgument(args[1]);
    const rightDelim =
      rightNode.type === "atom" && rightNode.family === "close"
        ? delimFromValue(rightNode.text)
        : null;

    const barNode = assertNodeType(args[2], "size");
    let hasBarLine;
    let barSize = null;
    if (barNode.isBlank) {
      // \genfrac acts differently than \above.
      // \genfrac treats an empty size group as a signal to use a
      // standard bar size. \above would see size = 0 and omit the bar.
      hasBarLine = true;
    } else {
      barSize = barNode.value;
      hasBarLine = barSize.number > 0;
    }

    // Find out if we want displaystyle, textstyle, etc.
    let scriptLevel = "auto";
    let styl = args[3];
    if (styl.type === "ordgroup") {
      if (styl.body.length > 0) {
        const textOrd = assertNodeType(styl.body[0], "textord");
        scriptLevel = stylArray[Number(textOrd.text)];
      }
    } else {
      styl = assertNodeType(styl, "textord");
      scriptLevel = stylArray[Number(styl.text)];
    }

    return {
      type: "genfrac",
      mode: parser.mode,
      numer,
      denom,
      continued: false,
      hasBarLine,
      barSize,
      leftDelim,
      rightDelim,
      scriptLevel
    };
  },
  mathmlBuilder: mathmlBuilder$5
});

// \above is an infix fraction that also defines a fraction bar size.
defineFunction({
  type: "infix",
  names: ["\\above"],
  props: {
    numArgs: 1,
    argTypes: ["size"],
    infix: true
  },
  handler({ parser, funcName, token }, args) {
    return {
      type: "infix",
      mode: parser.mode,
      replaceWith: "\\\\abovefrac",
      barSize: assertNodeType(args[0], "size").value,
      token
    };
  }
});

defineFunction({
  type: "genfrac",
  names: ["\\\\abovefrac"],
  props: {
    numArgs: 3,
    argTypes: ["math", "size", "math"]
  },
  handler: ({ parser, funcName }, args) => {
    const numer = args[0];
    const barSize = assert(assertNodeType(args[1], "infix").barSize);
    const denom = args[2];

    const hasBarLine = barSize.number > 0;
    return {
      type: "genfrac",
      mode: parser.mode,
      numer,
      denom,
      continued: false,
      hasBarLine,
      barSize,
      leftDelim: null,
      rightDelim: null,
      scriptLevel: "auto"
    };
  },

  mathmlBuilder: mathmlBuilder$5
});

// \hbox is provided for compatibility with LaTeX functions that act on a box.
// This function by itself doesn't do anything but set scriptlevel to \textstyle
// and prevent a soft line break.

defineFunction({
  type: "hbox",
  names: ["\\hbox"],
  props: {
    numArgs: 1,
    argTypes: ["hbox"],
    allowedInArgument: true,
    allowedInText: false
  },
  handler({ parser }, args) {
    return {
      type: "hbox",
      mode: parser.mode,
      body: ordargument(args[0])
    };
  },
  mathmlBuilder(group, style) {
    const newStyle = style.withLevel(StyleLevel.TEXT);
    const mrow = buildExpressionRow(group.body, newStyle);
    return consolidateText(mrow)
  }
});

const mathmlBuilder$4 = (group, style) => {
  const accentNode = stretchy.mathMLnode(group.label);
  accentNode.style["math-depth"] = 0;
  return new mathMLTree.MathNode(group.isOver ? "mover" : "munder", [
    buildGroup$1(group.base, style),
    accentNode
  ]);
};

// Horizontal stretchy braces
defineFunction({
  type: "horizBrace",
  names: ["\\overbrace", "\\underbrace"],
  props: {
    numArgs: 1
  },
  handler({ parser, funcName }, args) {
    return {
      type: "horizBrace",
      mode: parser.mode,
      label: funcName,
      isOver: /^\\over/.test(funcName),
      base: args[0]
    };
  },
  mathmlBuilder: mathmlBuilder$4
});

defineFunction({
  type: "href",
  names: ["\\href"],
  props: {
    numArgs: 2,
    argTypes: ["url", "original"],
    allowedInText: true
  },
  handler: ({ parser, token }, args) => {
    const body = args[1];
    const href = assertNodeType(args[0], "url").url;

    if (
      !parser.settings.isTrusted({
        command: "\\href",
        url: href
      })
    ) {
      throw new ParseError(`Function "\\href" is not trusted`, token)
    }

    return {
      type: "href",
      mode: parser.mode,
      href,
      body: ordargument(body)
    };
  },
  mathmlBuilder: (group, style) => {
    const math = new MathNode("math", [buildExpressionRow(group.body, style)]);
    const anchorNode = new AnchorNode(group.href, [], [math]);
    return anchorNode
  }
});

defineFunction({
  type: "href",
  names: ["\\url"],
  props: {
    numArgs: 1,
    argTypes: ["url"],
    allowedInText: true
  },
  handler: ({ parser, token }, args) => {
    const href = assertNodeType(args[0], "url").url;

    if (
      !parser.settings.isTrusted({
        command: "\\url",
        url: href
      })
    ) {
      throw new ParseError(`Function "\\url" is not trusted`, token)
    }

    const chars = [];
    for (let i = 0; i < href.length; i++) {
      let c = href[i];
      if (c === "~") {
        c = "\\textasciitilde";
      }
      chars.push({
        type: "textord",
        mode: "text",
        text: c
      });
    }
    const body = {
      type: "text",
      mode: parser.mode,
      font: "\\texttt",
      body: chars
    };
    return {
      type: "href",
      mode: parser.mode,
      href,
      body: ordargument(body)
    };
  }
});

defineFunction({
  type: "html",
  names: ["\\class", "\\id", "\\style", "\\data"],
  props: {
    numArgs: 2,
    argTypes: ["raw", "original"],
    allowedInText: true
  },
  handler: ({ parser, funcName, token }, args) => {
    const value = assertNodeType(args[0], "raw").string;
    const body = args[1];

    if (parser.settings.strict) {
      throw new ParseError(`Function "${funcName}" is disabled in strict mode`, token)
    }

    let trustContext;
    const attributes = {};

    switch (funcName) {
      case "\\class":
        attributes.class = value;
        trustContext = {
          command: "\\class",
          class: value
        };
        break;
      case "\\id":
        attributes.id = value;
        trustContext = {
          command: "\\id",
          id: value
        };
        break;
      case "\\style":
        attributes.style = value;
        trustContext = {
          command: "\\style",
          style: value
        };
        break;
      case "\\data": {
        const data = value.split(",");
        for (let i = 0; i < data.length; i++) {
          const keyVal = data[i].split("=");
          if (keyVal.length !== 2) {
            throw new ParseError("Error parsing key-value for \\data");
          }
          attributes["data-" + keyVal[0].trim()] = keyVal[1].trim();
        }

        trustContext = {
          command: "\\data",
          attributes
        };
        break;
      }
      default:
        throw new Error("Unrecognized html command");
    }

    if (!parser.settings.isTrusted(trustContext)) {
      throw new ParseError(`Function "${funcName}" is not trusted`, token)
    }
    return {
      type: "html",
      mode: parser.mode,
      attributes,
      body: ordargument(body)
    };
  },
  mathmlBuilder: (group, style) => {
    const element =  buildExpressionRow(group.body, style);

    const classes = [];
    if (group.attributes.class) {
      classes.push(...group.attributes.class.trim().split(/\s+/));
    }
    element.classes = classes;

    for (const attr in group.attributes) {
      if (attr !== "class" && Object.prototype.hasOwnProperty.call(group.attributes, attr)) {
        element.setAttribute(attr, group.attributes[attr]);
      }
    }

    return element;
  }
});

const sizeData = function(str) {
  if (/^[-+]? *(\d+(\.\d*)?|\.\d+)$/.test(str)) {
    // str is a number with no unit specified.
    // default unit is bp, per graphix package.
    return { number: +str, unit: "bp" }
  } else {
    const match = /([-+]?) *(\d+(?:\.\d*)?|\.\d+) *([a-z]{2})/.exec(str);
    if (!match) {
      throw new ParseError("Invalid size: '" + str + "' in \\includegraphics");
    }
    const data = {
      number: +(match[1] + match[2]), // sign + magnitude, cast to number
      unit: match[3]
    };
    if (!validUnit(data)) {
      throw new ParseError("Invalid unit: '" + data.unit + "' in \\includegraphics.");
    }
    return data
  }
};

defineFunction({
  type: "includegraphics",
  names: ["\\includegraphics"],
  props: {
    numArgs: 1,
    numOptionalArgs: 1,
    argTypes: ["raw", "url"],
    allowedInText: false
  },
  handler: ({ parser, token }, args, optArgs) => {
    let width = { number: 0, unit: "em" };
    let height = { number: 0.9, unit: "em" };  // sorta character sized.
    let totalheight = { number: 0, unit: "em" };
    let alt = "";

    if (optArgs[0]) {
      const attributeStr = assertNodeType(optArgs[0], "raw").string;

      // Parser.js does not parse key/value pairs. We get a string.
      const attributes = attributeStr.split(",");
      for (let i = 0; i < attributes.length; i++) {
        const keyVal = attributes[i].split("=");
        if (keyVal.length === 2) {
          const str = keyVal[1].trim();
          switch (keyVal[0].trim()) {
            case "alt":
              alt = str;
              break
            case "width":
              width = sizeData(str);
              break
            case "height":
              height = sizeData(str);
              break
            case "totalheight":
              totalheight = sizeData(str);
              break
            default:
              throw new ParseError("Invalid key: '" + keyVal[0] + "' in \\includegraphics.")
          }
        }
      }
    }

    const src = assertNodeType(args[0], "url").url;

    if (alt === "") {
      // No alt given. Use the file name. Strip away the path.
      alt = src;
      alt = alt.replace(/^.*[\\/]/, "");
      alt = alt.substring(0, alt.lastIndexOf("."));
    }

    if (
      !parser.settings.isTrusted({
        command: "\\includegraphics",
        url: src
      })
    ) {
      throw new ParseError(`Function "\\includegraphics" is not trusted`, token)
    }

    return {
      type: "includegraphics",
      mode: parser.mode,
      alt: alt,
      width: width,
      height: height,
      totalheight: totalheight,
      src: src
    }
  },
  mathmlBuilder: (group, style) => {
    const height = calculateSize(group.height, style);
    const depth = { number: 0, unit: "em" };

    if (group.totalheight.number > 0) {
      if (group.totalheight.unit === height.unit &&
        group.totalheight.number > height.number) {
        depth.number = group.totalheight.number - height.number;
        depth.unit = height.unit;
      }
    }

    let width = 0;
    if (group.width.number > 0) {
      width = calculateSize(group.width, style);
    }

    const graphicStyle = { height: height.number + depth.number + "em" };
    if (width.number > 0) {
      graphicStyle.width = width.number + width.unit;
    }
    if (depth.number > 0) {
      graphicStyle.verticalAlign = -depth.number + depth.unit;
    }

    const node = new Img(group.src, group.alt, graphicStyle);
    node.height = height;
    node.depth = depth;
    return new mathMLTree.MathNode("mtext", [node])
  }
});

// Horizontal spacing commands


// TODO: \hskip and \mskip should support plus and minus in lengths

defineFunction({
  type: "kern",
  names: ["\\kern", "\\mkern", "\\hskip", "\\mskip"],
  props: {
    numArgs: 1,
    argTypes: ["size"],
    primitive: true,
    allowedInText: true
  },
  handler({ parser, funcName, token }, args) {
    const size = assertNodeType(args[0], "size");
    if (parser.settings.strict) {
      const mathFunction = funcName[1] === "m"; // \mkern, \mskip
      const muUnit = size.value.unit === "mu";
      if (mathFunction) {
        if (!muUnit) {
          throw new ParseError(`LaTeX's ${funcName} supports only mu units, ` +
            `not ${size.value.unit} units`, token)
        }
        if (parser.mode !== "math") {
          throw new ParseError(`LaTeX's ${funcName} works only in math mode`, token)
        }
      } else {
        // !mathFunction
        if (muUnit) {
          throw new ParseError(`LaTeX's ${funcName} doesn't support mu units`, token)
        }
      }
    }
    return {
      type: "kern",
      mode: parser.mode,
      dimension: size.value
    };
  },
  mathmlBuilder(group, style) {
    const dimension = calculateSize(group.dimension, style);
    const ch = dimension.unit === "em" ? spaceCharacter(dimension.number) : "";
    if (group.mode === "text" && ch.length > 0) {
      const character = new mathMLTree.TextNode(ch);
      return new mathMLTree.MathNode("mtext", [character]);
    } else {
      const node = new mathMLTree.MathNode("mspace");
      node.setAttribute("width", dimension.number + dimension.unit);
      if (dimension.number < 0) {
        node.style.marginLeft = dimension.number + dimension.unit;
      }
      return node;
    }
  }
});

const spaceCharacter = function(width) {
  if (width >= 0.05555 && width <= 0.05556) {
    return "\u200a"; // &VeryThinSpace;
  } else if (width >= 0.1666 && width <= 0.1667) {
    return "\u2009"; // &ThinSpace;
  } else if (width >= 0.2222 && width <= 0.2223) {
    return "\u2005"; // &MediumSpace;
  } else if (width >= 0.2777 && width <= 0.2778) {
    return "\u2005\u200a"; // &ThickSpace;
  } else {
    return "";
  }
};

// Limit valid characters to a small set, for safety.
const invalidIdRegEx = /[^A-Za-z_0-9-]/g;

defineFunction({
  type: "label",
  names: ["\\label"],
  props: {
    numArgs: 1,
    argTypes: ["raw"]
  },
  handler({ parser }, args) {
    return {
      type: "label",
      mode: parser.mode,
      string: args[0].string.replace(invalidIdRegEx, "")
    };
  },
  mathmlBuilder(group, style) {
    // Return a no-width, no-ink element with an HTML id.
    const node = new mathMLTree.MathNode("mrow", [], ["tml-label"]);
    if (group.string.length > 0) {
      node.setLabel(group.string);
    }
    return node
  }
});

// Horizontal overlap functions

const textModeLap = ["\\clap", "\\llap", "\\rlap"];

defineFunction({
  type: "lap",
  names: ["\\mathllap", "\\mathrlap", "\\mathclap", "\\clap", "\\llap", "\\rlap"],
  props: {
    numArgs: 1,
    allowedInText: true
  },
  handler: ({ parser, funcName, token }, args) => {
    if (textModeLap.includes(funcName)) {
      if (parser.settings.strict && parser.mode !== "text") {
        throw new ParseError(`{${funcName}} can be used only in text mode.
 Try \\math${funcName.slice(1)}`, token)
      }
      funcName = funcName.slice(1);
    } else {
      funcName = funcName.slice(5);
    }
    const body = args[0];
    return {
      type: "lap",
      mode: parser.mode,
      alignment: funcName,
      body
    }
  },
  mathmlBuilder: (group, style) => {
    // mathllap, mathrlap, mathclap
    let strut;
    if (group.alignment === "llap") {
      // We need an invisible strut with the same depth as the group.
      // We can't just read the depth, so we use \vphantom methods.
      const phantomInner = buildExpression(ordargument(group.body), style);
      const phantom = new mathMLTree.MathNode("mphantom", phantomInner);
      strut = new mathMLTree.MathNode("mpadded", [phantom]);
      strut.setAttribute("width", "0px");
    }

    const inner = buildGroup$1(group.body, style);
    let node;
    if (group.alignment === "llap") {
      inner.style.position = "absolute";
      inner.style.right = "0";
      inner.style.bottom = `0`; // If we could have read the ink depth, it would go here.
      node = new mathMLTree.MathNode("mpadded", [strut, inner]);
    } else {
      node = new mathMLTree.MathNode("mpadded", [inner]);
    }

    if (group.alignment === "rlap") {
      if (group.body.body.length > 0 && group.body.body[0].type === "genfrac") {
        // In Firefox, a <mpadded> squashes the 3/18em padding of a child \frac. Put it back.
        node.setAttribute("lspace", "0.16667em");
      }
    } else {
      const offset = group.alignment === "llap" ? "-1" : "-0.5";
      node.setAttribute("lspace", offset + "width");
      if (group.alignment === "llap") {
        node.style.position = "relative";
      } else {
        node.style.display = "flex";
        node.style.justifyContent = "center";
      }
    }
    node.setAttribute("width", "0px");
    return node
  }
});

// Switching from text mode back to math mode
defineFunction({
  type: "ordgroup",
  names: ["\\(", "$"],
  props: {
    numArgs: 0,
    allowedInText: true,
    allowedInMath: false
  },
  handler({ funcName, parser }, args) {
    const outerMode = parser.mode;
    parser.switchMode("math");
    const close = funcName === "\\(" ? "\\)" : "$";
    const body = parser.parseExpression(false, close);
    parser.expect(close);
    parser.switchMode(outerMode);
    return {
      type: "ordgroup",
      mode: parser.mode,
      body
    };
  }
});

// Check for extra closing math delimiters
defineFunction({
  type: "text", // Doesn't matter what this is.
  names: ["\\)", "\\]"],
  props: {
    numArgs: 0,
    allowedInText: true,
    allowedInMath: false
  },
  handler(context, token) {
    throw new ParseError(`Mismatched ${context.funcName}`, token);
  }
});

const chooseStyle = (group, style) => {
  switch (style.level) {
    case StyleLevel.DISPLAY:       // 0
      return group.display;
    case StyleLevel.TEXT:          // 1
      return group.text;
    case StyleLevel.SCRIPT:        // 2
      return group.script;
    case StyleLevel.SCRIPTSCRIPT:  // 3
      return group.scriptscript;
    default:
      return group.text;
  }
};

defineFunction({
  type: "mathchoice",
  names: ["\\mathchoice"],
  props: {
    numArgs: 4,
    primitive: true
  },
  handler: ({ parser }, args) => {
    return {
      type: "mathchoice",
      mode: parser.mode,
      display: ordargument(args[0]),
      text: ordargument(args[1]),
      script: ordargument(args[2]),
      scriptscript: ordargument(args[3])
    };
  },
  mathmlBuilder: (group, style) => {
    const body = chooseStyle(group, style);
    return buildExpressionRow(body, style);
  }
});

const textAtomTypes = ["text", "textord", "mathord", "atom"];

const padding = width => {
  const node = new mathMLTree.MathNode("mspace");
  node.setAttribute("width", width + "em");
  return node
};

function mathmlBuilder$3(group, style) {
  let node;
  const inner = buildExpression(group.body, style);

  if (group.mclass === "minner") {
    node = new mathMLTree.MathNode("mpadded", inner);
  } else if (group.mclass === "mord") {
    if (group.isCharacterBox || inner[0].type === "mathord") {
      node = inner[0];
      node.type = "mi";
      if (node.children.length === 1 && node.children[0].text && node.children[0].text === "∇") {
        node.setAttribute("mathvariant", "normal");
      }
    } else {
      node = new mathMLTree.MathNode("mi", inner);
    }
  } else {
    node = new mathMLTree.MathNode("mrow", inner);
    if (group.mustPromote) {
      node = inner[0];
      node.type = "mo";
      if (group.isCharacterBox && group.body[0].text && /[A-Za-z]/.test(group.body[0].text)) {
        node.setAttribute("mathvariant", "italic");
      }
    } else {
      node = new mathMLTree.MathNode("mrow", inner);
    }

    // Set spacing based on what is the most likely adjacent atom type.
    // See TeXbook p170.
    const doSpacing = style.level < 2; // Operator spacing is zero inside a (sub|super)script.
    if (node.type === "mrow") {
      if (doSpacing ) {
        if (group.mclass === "mbin") {
          // medium space
          node.children.unshift(padding(0.2222));
          node.children.push(padding(0.2222));
        } else if (group.mclass === "mrel") {
          // thickspace
          node.children.unshift(padding(0.2778));
          node.children.push(padding(0.2778));
        } else if (group.mclass === "mpunct") {
          node.children.push(padding(0.1667));
        } else if (group.mclass === "minner") {
          node.children.unshift(padding(0.0556));  // 1 mu is the most likely option
          node.children.push(padding(0.0556));
        }
      }
    } else {
      if (group.mclass === "mbin") {
        // medium space
        node.attributes.lspace = (doSpacing ? "0.2222em" : "0");
        node.attributes.rspace = (doSpacing ? "0.2222em" : "0");
      } else if (group.mclass === "mrel") {
        // thickspace
        node.attributes.lspace = (doSpacing ? "0.2778em" : "0");
        node.attributes.rspace = (doSpacing ? "0.2778em" : "0");
      } else if (group.mclass === "mpunct") {
        node.attributes.lspace = "0em";
        node.attributes.rspace = (doSpacing ? "0.1667em" : "0");
      } else if (group.mclass === "mopen" || group.mclass === "mclose") {
        node.attributes.lspace = "0em";
        node.attributes.rspace = "0em";
      } else if (group.mclass === "minner" && doSpacing) {
        node.attributes.lspace = "0.0556em"; // 1 mu is the most likely option
        node.attributes.width = "+0.1111em";
      }
    }

    if (!(group.mclass === "mopen" || group.mclass === "mclose")) {
      delete node.attributes.stretchy;
      delete node.attributes.form;
    }
  }
  return node;
}

// Math class commands except \mathop
defineFunction({
  type: "mclass",
  names: [
    "\\mathord",
    "\\mathbin",
    "\\mathrel",
    "\\mathopen",
    "\\mathclose",
    "\\mathpunct",
    "\\mathinner"
  ],
  props: {
    numArgs: 1,
    primitive: true
  },
  handler({ parser, funcName }, args) {
    const body = args[0];
    const isCharacterBox = utils.isCharacterBox(body);
    // We should not wrap a <mo> around a <mi> or <mord>. That would be invalid MathML.
    // In that case, we instead promote the text contents of the body to the parent.
    let mustPromote = true;
    const mord = { type: "mathord", text: "", mode: parser.mode };
    const arr = (body.body) ? body.body : [body];
    for (const arg of arr) {
      if (textAtomTypes.includes(arg.type)) {
        if (symbols[parser.mode][arg.text]) {
          mord.text += symbols[parser.mode][arg.text].replace;
        } else if (arg.text) {
          mord.text += arg.text;
        } else if (arg.body) {
          arg.body.map(e => { mord.text += e.text; });
        }
      } else {
        mustPromote = false;
        break
      }
    }
    return {
      type: "mclass",
      mode: parser.mode,
      mclass: "m" + funcName.slice(5),
      body: ordargument(mustPromote ? mord : body),
      isCharacterBox,
      mustPromote
    };
  },
  mathmlBuilder: mathmlBuilder$3
});

const binrelClass = (arg) => {
  // \binrel@ spacing varies with (bin|rel|ord) of the atom in the argument.
  // (by rendering separately and with {}s before and after, and measuring
  // the change in spacing).  We'll do roughly the same by detecting the
  // atom type directly.
  const atom = arg.type === "ordgroup" && arg.body.length ? arg.body[0] : arg;
  if (atom.type === "atom" && (atom.family === "bin" || atom.family === "rel")) {
    return "m" + atom.family;
  } else {
    return "mord";
  }
};

// \@binrel{x}{y} renders like y but as mbin/mrel/mord if x is mbin/mrel/mord.
// This is equivalent to \binrel@{x}\binrel@@{y} in AMSTeX.
defineFunction({
  type: "mclass",
  names: ["\\@binrel"],
  props: {
    numArgs: 2
  },
  handler({ parser }, args) {
    return {
      type: "mclass",
      mode: parser.mode,
      mclass: binrelClass(args[0]),
      body: ordargument(args[1]),
      isCharacterBox: utils.isCharacterBox(args[1])
    };
  }
});

// Build a relation or stacked op by placing one symbol on top of another
defineFunction({
  type: "mclass",
  names: ["\\stackrel", "\\overset", "\\underset"],
  props: {
    numArgs: 2
  },
  handler({ parser, funcName }, args) {
    const baseArg = args[1];
    const shiftedArg = args[0];

    const baseOp = {
      type: "op",
      mode: baseArg.mode,
      limits: true,
      alwaysHandleSupSub: true,
      parentIsSupSub: false,
      symbol: false,
      stack: true,
      suppressBaseShift: funcName !== "\\stackrel",
      body: ordargument(baseArg)
    };

    return {
      type: "supsub",
      mode: shiftedArg.mode,
      base: baseOp,
      sup: funcName === "\\underset" ? null : shiftedArg,
      sub: funcName === "\\underset" ? shiftedArg : null
    };
  },
  mathmlBuilder: mathmlBuilder$3
});

// Helper function
const buildGroup = (el, style, noneNode) => {
  if (!el) { return noneNode }
  const node = buildGroup$1(el, style);
  if (node.type === "mrow" && node.children.length === 0) { return noneNode }
  return node
};

defineFunction({
  type: "multiscript",
  names: ["\\sideset", "\\pres@cript"], // See macros.js for \prescript
  props: {
    numArgs: 3
  },
  handler({ parser, funcName, token }, args) {
    if (args[2].body.length === 0) {
      throw new ParseError(funcName + `cannot parse an empty base.`)
    }
    const base = args[2].body[0];
    if (parser.settings.strict && funcName === "\\sideset" && !base.symbol) {
      throw new ParseError(`The base of \\sideset must be a big operator. Try \\prescript.`)
    }

    if ((args[0].body.length > 0 && args[0].body[0].type !== "supsub") ||
        (args[1].body.length > 0 && args[1].body[0].type !== "supsub")) {
      throw new ParseError("\\sideset can parse only subscripts and " +
                            "superscripts in its first two arguments", token)
    }

    // The prescripts and postscripts come wrapped in a supsub.
    const prescripts = args[0].body.length > 0 ? args[0].body[0] : null;
    const postscripts = args[1].body.length > 0 ? args[1].body[0] : null;

    if (!prescripts && !postscripts) {
      return base
    } else if (!prescripts) {
      // It's not a multi-script. Get a \textstyle supsub.
      return {
        type: "styling",
        mode: parser.mode,
        scriptLevel: "text",
        body: [{
          type: "supsub",
          mode: parser.mode,
          base,
          sup: postscripts.sup,
          sub: postscripts.sub
        }]
      }
    } else {
      return {
        type: "multiscript",
        mode: parser.mode,
        isSideset: funcName === "\\sideset",
        prescripts,
        postscripts,
        base
      }
    }
  },
  mathmlBuilder(group, style) {
    const base =  buildGroup$1(group.base, style);

    const prescriptsNode = new mathMLTree.MathNode("mprescripts");
    const noneNode = new mathMLTree.MathNode("none");
    let children = [];

    const preSub = buildGroup(group.prescripts.sub, style, noneNode);
    const preSup = buildGroup(group.prescripts.sup, style, noneNode);
    if (group.isSideset) {
      // This seems silly, but LaTeX does this. Firefox ignores it, which does not make me sad.
      preSub.setAttribute("style", "text-align: left;");
      preSup.setAttribute("style", "text-align: left;");
    }

    if (group.postscripts) {
      const postSub = buildGroup(group.postscripts.sub, style, noneNode);
      const postSup = buildGroup(group.postscripts.sup, style, noneNode);
      children = [base, postSub, postSup, prescriptsNode, preSub, preSup];
    } else {
      children = [base, prescriptsNode, preSub, preSup];
    }

    return new mathMLTree.MathNode("mmultiscripts", children);
  }
});

defineFunction({
  type: "not",
  names: ["\\not"],
  props: {
    numArgs: 1,
    primitive: true,
    allowedInText: false
  },
  handler({ parser }, args) {
    const isCharacterBox = utils.isCharacterBox(args[0]);
    let body;
    if (isCharacterBox) {
      body = ordargument(args[0]);
      if (body[0].text.charAt(0) === "\\") {
        body[0].text = symbols.math[body[0].text].replace;
      }
      // \u0338 is the Unicode Combining Long Solidus Overlay
      body[0].text = body[0].text.slice(0, 1) + "\u0338" + body[0].text.slice(1);
    } else {
      // When the argument is not a character box, TeX does an awkward, poorly placed overlay.
      // We'll do the same.
      const notNode = { type: "textord", mode: "math", text: "\u0338" };
      const kernNode = { type: "kern", mode: "math", dimension: { number: -0.6, unit: "em" } };
      body = [notNode, kernNode, args[0]];
    }
    return {
      type: "not",
      mode: parser.mode,
      body,
      isCharacterBox
    };
  },
  mathmlBuilder(group, style) {
    if (group.isCharacterBox) {
      const inner = buildExpression(group.body, style, true);
      return inner[0]
    } else {
      return buildExpressionRow(group.body, style)
    }
  }
});

// Limits, symbols

// Some helpers

const ordAtomTypes = ["textord", "mathord", "atom"];

// Most operators have a large successor symbol, but these don't.
const noSuccessor = ["\\smallint"];

// Math operators (e.g. \sin) need a space between these types and themselves:
const ordTypes = ["textord", "mathord", "ordgroup", "close", "leftright", "font"];

// NOTE: Unlike most `builders`s, this one handles not only "op", but also
// "supsub" since some of them (like \int) can affect super/subscripting.

const setSpacing = node => {
  // The user wrote a \mathop{…} function. Change spacing from default to OP spacing.
  // The most likely spacing for an OP is a thin space per TeXbook p170.
  node.attributes.lspace = "0.1667em";
  node.attributes.rspace = "0.1667em";
};

const mathmlBuilder$2 = (group, style) => {
  let node;

  if (group.symbol) {
    // This is a symbol. Just add the symbol.
    node = new MathNode("mo", [makeText(group.name, group.mode)]);
    if (noSuccessor.includes(group.name)) {
      node.setAttribute("largeop", "false");
    } else {
      node.setAttribute("movablelimits", "false");
    }
    if (group.fromMathOp) { setSpacing(node); }
  } else if (group.body) {
    // This is an operator with children. Add them.
    node = new MathNode("mo", buildExpression(group.body, style));
    if (group.fromMathOp) { setSpacing(node); }
  } else {
    // This is a text operator. Add all of the characters from the operator's name.
    node = new MathNode("mi", [new TextNode(group.name.slice(1))]);

    if (!group.parentIsSupSub) {
      // Append an invisible <mo>&ApplyFunction;</mo>.
      // ref: https://www.w3.org/TR/REC-MathML/chap3_2.html#sec3.2.4
      const operator = new MathNode("mo", [makeText("\u2061", "text")]);
      const row = [node, operator];
      // Set spacing
      if (group.needsLeadingSpace) {
        const lead = new MathNode("mspace");
        lead.setAttribute("width", "0.1667em"); // thin space.
        row.unshift(lead);
      }
      if (!group.isFollowedByDelimiter) {
        const trail = new MathNode("mspace");
        trail.setAttribute("width", "0.1667em"); // thin space.
        row.push(trail);
      }
      node = new MathNode("mrow", row);
    }
  }

  return node;
};

const singleCharBigOps = {
  "\u220F": "\\prod",
  "\u2210": "\\coprod",
  "\u2211": "\\sum",
  "\u22c0": "\\bigwedge",
  "\u22c1": "\\bigvee",
  "\u22c2": "\\bigcap",
  "\u22c3": "\\bigcup",
  "\u2a00": "\\bigodot",
  "\u2a01": "\\bigoplus",
  "\u2a02": "\\bigotimes",
  "\u2a04": "\\biguplus",
  "\u2a05": "\\bigsqcap",
  "\u2a06": "\\bigsqcup",
  "\u2a03": "\\bigcupdot",
  "\u2a07": "\\bigdoublevee",
  "\u2a08": "\\bigdoublewedge",
  "\u2a09": "\\bigtimes"
};

defineFunction({
  type: "op",
  names: [
    "\\coprod",
    "\\bigvee",
    "\\bigwedge",
    "\\biguplus",
    "\\bigcupplus",
    "\\bigcupdot",
    "\\bigcap",
    "\\bigcup",
    "\\bigdoublevee",
    "\\bigdoublewedge",
    "\\intop",
    "\\prod",
    "\\sum",
    "\\bigotimes",
    "\\bigoplus",
    "\\bigodot",
    "\\bigsqcap",
    "\\bigsqcup",
    "\\bigtimes",
    "\\smallint",
    "\u220F",
    "\u2210",
    "\u2211",
    "\u22c0",
    "\u22c1",
    "\u22c2",
    "\u22c3",
    "\u2a00",
    "\u2a01",
    "\u2a02",
    "\u2a04",
    "\u2a06"
  ],
  props: {
    numArgs: 0
  },
  handler: ({ parser, funcName }, args) => {
    let fName = funcName;
    if (fName.length === 1) {
      fName = singleCharBigOps[fName];
    }
    return {
      type: "op",
      mode: parser.mode,
      limits: true,
      parentIsSupSub: false,
      symbol: true,
      stack: false, // This is true for \stackrel{}, not here.
      name: fName
    };
  },
  mathmlBuilder: mathmlBuilder$2
});

// Note: calling defineFunction with a type that's already been defined only
// works because the same mathmlBuilder is being used.
defineFunction({
  type: "op",
  names: ["\\mathop"],
  props: {
    numArgs: 1,
    primitive: true
  },
  handler: ({ parser }, args) => {
    const body = args[0];
    // It would be convienient to just wrap a <mo> around the argument.
    // But if the argument is a <mi> or <mord>, that would be invalid MathML.
    // In that case, we instead promote the text contents of the body to the parent.
    const arr = (body.body) ? body.body : [body];
    const isSymbol = arr.length === 1 && ordAtomTypes.includes(arr[0].type);
    return {
      type: "op",
      mode: parser.mode,
      limits: true,
      parentIsSupSub: false,
      symbol: isSymbol,
      fromMathOp: true,
      stack: false,
      name: isSymbol ? arr[0].text : null,
      body: isSymbol ? null : ordargument(body)
    };
  },
  mathmlBuilder: mathmlBuilder$2
});

// There are 2 flags for operators; whether they produce limits in
// displaystyle, and whether they are symbols and should grow in
// displaystyle. These four groups cover the four possible choices.

const singleCharIntegrals = {
  "\u222b": "\\int",
  "\u222c": "\\iint",
  "\u222d": "\\iiint",
  "\u222e": "\\oint",
  "\u222f": "\\oiint",
  "\u2230": "\\oiiint",
  "\u2231": "\\intclockwise",
  "\u2232": "\\varointclockwise",
  "\u2a0c": "\\iiiint",
  "\u2a0d": "\\intbar",
  "\u2a0e": "\\intBar",
  "\u2a0f": "\\fint",
  "\u2a12": "\\rppolint",
  "\u2a13": "\\scpolint",
  "\u2a15": "\\pointint",
  "\u2a16": "\\sqint",
  "\u2a17": "\\intlarhk",
  "\u2a18": "\\intx",
  "\u2a19": "\\intcap",
  "\u2a1a": "\\intcup"
};

// No limits, not symbols
defineFunction({
  type: "op",
  names: [
    "\\arcsin",
    "\\arccos",
    "\\arctan",
    "\\arctg",
    "\\arcctg",
    "\\arg",
    "\\ch",
    "\\cos",
    "\\cosec",
    "\\cosh",
    "\\cot",
    "\\cotg",
    "\\coth",
    "\\csc",
    "\\ctg",
    "\\cth",
    "\\deg",
    "\\dim",
    "\\exp",
    "\\hom",
    "\\ker",
    "\\lg",
    "\\ln",
    "\\log",
    "\\sec",
    "\\sin",
    "\\sinh",
    "\\sh",
    "\\sgn",
    "\\tan",
    "\\tanh",
    "\\tg",
    "\\th"
  ],
  props: {
    numArgs: 0
  },
  handler({ parser, funcName }) {
    const prevAtomType = parser.prevAtomType;
    const next = parser.gullet.future().text;
    return {
      type: "op",
      mode: parser.mode,
      limits: false,
      parentIsSupSub: false,
      symbol: false,
      stack: false,
      isFollowedByDelimiter: isDelimiter(next),
      needsLeadingSpace: prevAtomType.length > 0 && ordTypes.includes(prevAtomType),
      name: funcName
    };
  },
  mathmlBuilder: mathmlBuilder$2
});

// Limits, not symbols
defineFunction({
  type: "op",
  names: ["\\det", "\\gcd", "\\inf", "\\lim", "\\max", "\\min", "\\Pr", "\\sup"],
  props: {
    numArgs: 0
  },
  handler({ parser, funcName }) {
    const prevAtomType = parser.prevAtomType;
    const next = parser.gullet.future().text;
    return {
      type: "op",
      mode: parser.mode,
      limits: true,
      parentIsSupSub: false,
      symbol: false,
      stack: false,
      isFollowedByDelimiter: isDelimiter(next),
      needsLeadingSpace: prevAtomType.length > 0 && ordTypes.includes(prevAtomType),
      name: funcName
    };
  },
  mathmlBuilder: mathmlBuilder$2
});

// No limits, symbols
defineFunction({
  type: "op",
  names: [
    "\\int",
    "\\iint",
    "\\iiint",
    "\\iiiint",
    "\\oint",
    "\\oiint",
    "\\oiiint",
    "\\intclockwise",
    "\\varointclockwise",
    "\\intbar",
    "\\intBar",
    "\\fint",
    "\\rppolint",
    "\\scpolint",
    "\\pointint",
    "\\sqint",
    "\\intlarhk",
    "\\intx",
    "\\intcap",
    "\\intcup",
    "\u222b",
    "\u222c",
    "\u222d",
    "\u222e",
    "\u222f",
    "\u2230",
    "\u2231",
    "\u2232",
    "\u2a0c",
    "\u2a0d",
    "\u2a0e",
    "\u2a0f",
    "\u2a12",
    "\u2a13",
    "\u2a15",
    "\u2a16",
    "\u2a17",
    "\u2a18",
    "\u2a19",
    "\u2a1a"
  ],
  props: {
    numArgs: 0
  },
  handler({ parser, funcName }) {
    let fName = funcName;
    if (fName.length === 1) {
      fName = singleCharIntegrals[fName];
    }
    return {
      type: "op",
      mode: parser.mode,
      limits: false,
      parentIsSupSub: false,
      symbol: true,
      stack: false,
      name: fName
    };
  },
  mathmlBuilder: mathmlBuilder$2
});

// NOTE: Unlike most builders, this one handles not only
// "operatorname", but also  "supsub" since \operatorname* can
// affect super/subscripting.

const mathmlBuilder$1 = (group, style) => {
  let expression = buildExpression(group.body, style.withFont("mathrm"));

  // Is expression a string or has it something like a fraction?
  let isAllString = true; // default
  for (let i = 0; i < expression.length; i++) {
    let node = expression[i];
    if (node instanceof mathMLTree.MathNode) {
      if (node.type === "mrow" && node.children.length === 1 &&
          node.children[0] instanceof mathMLTree.MathNode) {
        node = node.children[0];
      }
      switch (node.type) {
        case "mi":
        case "mn":
        case "ms":
        case "mtext":
          break; // Do nothing yet.
        case "mspace":
          {
            if (node.attributes.width) {
              const width = node.attributes.width.replace("em", "");
              const ch = spaceCharacter(Number(width));
              if (ch === "") {
                isAllString = false;
              } else {
                expression[i] = new mathMLTree.MathNode("mtext", [new mathMLTree.TextNode(ch)]);
              }
            }
          }
          break
        case "mo": {
          const child = node.children[0];
          if (node.children.length === 1 && child instanceof mathMLTree.TextNode) {
            child.text = child.text.replace(/\u2212/, "-").replace(/\u2217/, "*");
          } else {
            isAllString = false;
          }
          break
        }
        default:
          isAllString = false;
      }
    } else {
      isAllString = false;
    }
  }

  if (isAllString) {
    // Write a single TextNode instead of multiple nested tags.
    const word = expression.map((node) => node.toText()).join("");
    expression = [new mathMLTree.TextNode(word)];
  } else if (
    expression.length === 1
    && ["mover", "munder"].includes(expression[0].type) &&
    (expression[0].children[0].type === "mi" || expression[0].children[0].type === "mtext")
  ) {
    expression[0].children[0].type = "mi";
    if (group.parentIsSupSub) {
      return new mathMLTree.MathNode("mrow", expression)
    } else {
      const operator = new mathMLTree.MathNode("mo", [makeText("\u2061", "text")]);
      return mathMLTree.newDocumentFragment([expression[0], operator])
    }
  }

  let wrapper;
  if (isAllString) {
    wrapper = new mathMLTree.MathNode("mi", expression);
    if (expression[0].text.length === 1) {
      wrapper.setAttribute("mathvariant", "normal");
    }
  } else {
    wrapper = new mathMLTree.MathNode("mrow", expression);
  }

  if (!group.parentIsSupSub) {
    // Append an <mo>&ApplyFunction;</mo>.
    // ref: https://www.w3.org/TR/REC-MathML/chap3_2.html#sec3.2.4
    const operator = new mathMLTree.MathNode("mo", [makeText("\u2061", "text")]);
    const fragment = [wrapper, operator];
    if (group.needsLeadingSpace) {
      // LaTeX gives operator spacing, but a <mi> gets ord spacing.
      // So add a leading space.
      const space = new mathMLTree.MathNode("mspace");
      space.setAttribute("width", "0.1667em"); // thin space.
      fragment.unshift(space);
    }
    if (!group.isFollowedByDelimiter) {
      const trail = new mathMLTree.MathNode("mspace");
      trail.setAttribute("width", "0.1667em"); // thin space.
      fragment.push(trail);
    }
    return mathMLTree.newDocumentFragment(fragment)
  }

  return wrapper
};

// \operatorname
// amsopn.dtx: \mathop{#1\kern\z@\operator@font#3}\newmcodes@
defineFunction({
  type: "operatorname",
  names: ["\\operatorname@", "\\operatornamewithlimits"],
  props: {
    numArgs: 1,
    allowedInArgument: true
  },
  handler: ({ parser, funcName }, args) => {
    const body = args[0];
    const prevAtomType = parser.prevAtomType;
    const next = parser.gullet.future().text;
    return {
      type: "operatorname",
      mode: parser.mode,
      body: ordargument(body),
      alwaysHandleSupSub: (funcName === "\\operatornamewithlimits"),
      limits: false,
      parentIsSupSub: false,
      isFollowedByDelimiter: isDelimiter(next),
      needsLeadingSpace: prevAtomType.length > 0 && ordTypes.includes(prevAtomType)
    };
  },
  mathmlBuilder: mathmlBuilder$1
});

defineMacro("\\operatorname",
  "\\@ifstar\\operatornamewithlimits\\operatorname@");

defineFunctionBuilders({
  type: "ordgroup",
  mathmlBuilder(group, style) {
    return buildExpressionRow(group.body, style, group.semisimple);
  }
});

defineFunction({
  type: "phantom",
  names: ["\\phantom"],
  props: {
    numArgs: 1,
    allowedInText: true
  },
  handler: ({ parser }, args) => {
    const body = args[0];
    return {
      type: "phantom",
      mode: parser.mode,
      body: ordargument(body)
    };
  },
  mathmlBuilder: (group, style) => {
    const inner = buildExpression(group.body, style);
    return new mathMLTree.MathNode("mphantom", inner);
  }
});

defineFunction({
  type: "hphantom",
  names: ["\\hphantom"],
  props: {
    numArgs: 1,
    allowedInText: true
  },
  handler: ({ parser }, args) => {
    const body = args[0];
    return {
      type: "hphantom",
      mode: parser.mode,
      body
    };
  },
  mathmlBuilder: (group, style) => {
    const inner = buildExpression(ordargument(group.body), style);
    const phantom = new mathMLTree.MathNode("mphantom", inner);
    const node = new mathMLTree.MathNode("mpadded", [phantom]);
    node.setAttribute("height", "0px");
    node.setAttribute("depth", "0px");
    return node;
  }
});

defineFunction({
  type: "vphantom",
  names: ["\\vphantom"],
  props: {
    numArgs: 1,
    allowedInText: true
  },
  handler: ({ parser }, args) => {
    const body = args[0];
    return {
      type: "vphantom",
      mode: parser.mode,
      body
    };
  },
  mathmlBuilder: (group, style) => {
    const inner = buildExpression(ordargument(group.body), style);
    const phantom = new mathMLTree.MathNode("mphantom", inner);
    const node = new mathMLTree.MathNode("mpadded", [phantom]);
    node.setAttribute("width", "0px");
    return node;
  }
});

// In LaTeX, \pmb is a simulation of bold font.
// The version of \pmb in ambsy.sty works by typesetting three copies of the argument
// with small offsets. We use CSS font-weight:bold.

defineFunction({
  type: "pmb",
  names: ["\\pmb"],
  props: {
    numArgs: 1,
    allowedInText: true
  },
  handler({ parser }, args) {
    return {
      type: "pmb",
      mode: parser.mode,
      body: ordargument(args[0])
    }
  },
  mathmlBuilder(group, style) {
    const inner = buildExpression(group.body, style);
    // Wrap with an <mstyle> element.
    const node = wrapWithMstyle(inner);
    node.setAttribute("style", "font-weight:bold");
    return node
  }
});

// \raise, \lower, and \raisebox

const mathmlBuilder = (group, style) => {
  const newStyle = style.withLevel(StyleLevel.TEXT);
  const node = new mathMLTree.MathNode("mpadded", [buildGroup$1(group.body, newStyle)]);
  const dy = calculateSize(group.dy, style);
  node.setAttribute("voffset", dy.number + dy.unit);
  // Add padding, which acts to increase height in Chromium.
  // TODO: Figure out some way to change height in Firefox w/o breaking Chromium.
  if (dy.number > 0) {
    node.style.padding = dy.number + dy.unit + " 0 0 0";
  } else {
    node.style.padding = "0 0 " + Math.abs(dy.number) + dy.unit + " 0";
  }
  return node
};

defineFunction({
  type: "raise",
  names: ["\\raise", "\\lower"],
  props: {
    numArgs: 2,
    argTypes: ["size", "primitive"],
    primitive: true
  },
  handler({ parser, funcName }, args) {
    const amount = assertNodeType(args[0], "size").value;
    if (funcName === "\\lower") { amount.number *= -1; }
    const body = args[1];
    return {
      type: "raise",
      mode: parser.mode,
      dy: amount,
      body
    };
  },
  mathmlBuilder
});


defineFunction({
  type: "raise",
  names: ["\\raisebox"],
  props: {
    numArgs: 2,
    argTypes: ["size", "hbox"],
    allowedInText: true
  },
  handler({ parser, funcName }, args) {
    const amount = assertNodeType(args[0], "size").value;
    const body = args[1];
    return {
      type: "raise",
      mode: parser.mode,
      dy: amount,
      body
    };
  },
  mathmlBuilder
});

defineFunction({
  type: "ref",
  names: ["\\ref", "\\eqref"],
  props: {
    numArgs: 1,
    argTypes: ["raw"]
  },
  handler({ parser, funcName }, args) {
    return {
      type: "ref",
      mode: parser.mode,
      funcName,
      string: args[0].string.replace(invalidIdRegEx, "")
    };
  },
  mathmlBuilder(group, style) {
    // Create an empty <a> node. Set a class and an href attribute.
    // The post-processor will populate with the target's tag or equation number.
    const classes = group.funcName === "\\ref" ? ["tml-ref"] : ["tml-ref", "tml-eqref"];
    return new AnchorNode("#" + group.string, classes, null)
  }
});

defineFunction({
  type: "reflect",
  names: ["\\reflectbox"],
  props: {
    numArgs: 1,
    argTypes: ["hbox"],
    allowedInText: true
  },
  handler({ parser }, args) {
    return {
      type: "reflect",
      mode: parser.mode,
      body: args[0]
    };
  },
  mathmlBuilder(group, style) {
    const node = buildGroup$1(group.body, style);
    node.style.transform = "scaleX(-1)";
    return node
  }
});

defineFunction({
  type: "internal",
  names: ["\\relax"],
  props: {
    numArgs: 0,
    allowedInText: true
  },
  handler({ parser }) {
    return {
      type: "internal",
      mode: parser.mode
    };
  }
});

defineFunction({
  type: "rule",
  names: ["\\rule"],
  props: {
    numArgs: 2,
    numOptionalArgs: 1,
    allowedInText: true,
    allowedInMath: true,
    argTypes: ["size", "size", "size"]
  },
  handler({ parser }, args, optArgs) {
    const shift = optArgs[0];
    const width = assertNodeType(args[0], "size");
    const height = assertNodeType(args[1], "size");
    return {
      type: "rule",
      mode: parser.mode,
      shift: shift && assertNodeType(shift, "size").value,
      width: width.value,
      height: height.value
    };
  },
  mathmlBuilder(group, style) {
    const width = calculateSize(group.width, style);
    const height = calculateSize(group.height, style);
    const shift = group.shift
      ? calculateSize(group.shift, style)
      : { number: 0, unit: "em" };
    const color = (style.color && style.getColor()) || "black";

    const rule = new mathMLTree.MathNode("mspace");
    if (width.number > 0 && height.number > 0) {
      rule.setAttribute("mathbackground", color);
    }
    rule.setAttribute("width", width.number + width.unit);
    rule.setAttribute("height", height.number + height.unit);
    if (shift.number === 0) { return rule }

    const wrapper = new mathMLTree.MathNode("mpadded", [rule]);
    if (shift.number >= 0) {
      wrapper.setAttribute("height", "+" + shift.number + shift.unit);
    } else {
      wrapper.setAttribute("height", shift.number + shift.unit);
      wrapper.setAttribute("depth", "+" + -shift.number + shift.unit);
    }
    wrapper.setAttribute("voffset", shift.number + shift.unit);
    return wrapper;
  }
});

// The size mappings are taken from TeX with \normalsize=10pt.
// We don't have to track script level. MathML does that.
const sizeMap = {
  "\\tiny": 0.5,
  "\\sixptsize": 0.6,
  "\\Tiny": 0.6,
  "\\scriptsize": 0.7,
  "\\footnotesize": 0.8,
  "\\small": 0.9,
  "\\normalsize": 1.0,
  "\\large": 1.2,
  "\\Large": 1.44,
  "\\LARGE": 1.728,
  "\\huge": 2.074,
  "\\Huge": 2.488
};

defineFunction({
  type: "sizing",
  names: [
    "\\tiny",
    "\\sixptsize",
    "\\Tiny",
    "\\scriptsize",
    "\\footnotesize",
    "\\small",
    "\\normalsize",
    "\\large",
    "\\Large",
    "\\LARGE",
    "\\huge",
    "\\Huge"
  ],
  props: {
    numArgs: 0,
    allowedInText: true
  },
  handler: ({ breakOnTokenText, funcName, parser }, args) => {
    if (parser.settings.strict && parser.mode === "math") {
      // eslint-disable-next-line no-console
      console.log(`Temml strict-mode warning: Command ${funcName} is invalid in math mode.`);
    }
    const body = parser.parseExpression(false, breakOnTokenText, true);
    return {
      type: "sizing",
      mode: parser.mode,
      funcName,
      body
    };
  },
  mathmlBuilder: (group, style) => {
    const newStyle = style.withFontSize(sizeMap[group.funcName]);
    const inner = buildExpression(group.body, newStyle);
    // Wrap with an <mstyle> element.
    const node = wrapWithMstyle(inner);
    const factor = (sizeMap[group.funcName] / style.fontSize).toFixed(4);
    node.setAttribute("mathsize", factor + "em");
    return node;
  }
});

// smash, with optional [tb], as in AMS

defineFunction({
  type: "smash",
  names: ["\\smash"],
  props: {
    numArgs: 1,
    numOptionalArgs: 1,
    allowedInText: true
  },
  handler: ({ parser }, args, optArgs) => {
    let smashHeight = false;
    let smashDepth = false;
    const tbArg = optArgs[0] && assertNodeType(optArgs[0], "ordgroup");
    if (tbArg) {
      // Optional [tb] argument is engaged.
      // ref: amsmath: \renewcommand{\smash}[1][tb]{%
      //               def\mb@t{\ht}\def\mb@b{\dp}\def\mb@tb{\ht\z@\z@\dp}%
      let letter = "";
      for (let i = 0; i < tbArg.body.length; ++i) {
        const node = tbArg.body[i];
        // TODO: Write an AssertSymbolNode
        letter = node.text;
        if (letter === "t") {
          smashHeight = true;
        } else if (letter === "b") {
          smashDepth = true;
        } else {
          smashHeight = false;
          smashDepth = false;
          break;
        }
      }
    } else {
      smashHeight = true;
      smashDepth = true;
    }

    const body = args[0];
    return {
      type: "smash",
      mode: parser.mode,
      body,
      smashHeight,
      smashDepth
    };
  },
  mathmlBuilder: (group, style) => {
    const node = new mathMLTree.MathNode("mpadded", [buildGroup$1(group.body, style)]);

    if (group.smashHeight) {
      node.setAttribute("height", "0px");
    }

    if (group.smashDepth) {
      node.setAttribute("depth", "0px");
    }

    return node;
  }
});

defineFunction({
  type: "sqrt",
  names: ["\\sqrt"],
  props: {
    numArgs: 1,
    numOptionalArgs: 1
  },
  handler({ parser }, args, optArgs) {
    const index = optArgs[0];
    const body = args[0];
    return {
      type: "sqrt",
      mode: parser.mode,
      body,
      index
    };
  },
  mathmlBuilder(group, style) {
    const { body, index } = group;
    return index
      ? new mathMLTree.MathNode("mroot", [
        buildGroup$1(body, style),
        buildGroup$1(index, style.incrementLevel())
      ])
    : new mathMLTree.MathNode("msqrt", [buildGroup$1(body, style)]);
  }
});

const styleMap = {
  display: 0,
  text: 1,
  script: 2,
  scriptscript: 3
};

const styleAttributes = {
  display: ["0", "true"],
  text: ["0", "false"],
  script: ["1", "false"],
  scriptscript: ["2", "false"]
};

defineFunction({
  type: "styling",
  names: ["\\displaystyle", "\\textstyle", "\\scriptstyle", "\\scriptscriptstyle"],
  props: {
    numArgs: 0,
    allowedInText: true,
    primitive: true
  },
  handler({ breakOnTokenText, funcName, parser }, args) {
    // parse out the implicit body
    const body = parser.parseExpression(true, breakOnTokenText, true);

    const scriptLevel = funcName.slice(1, funcName.length - 5);
    return {
      type: "styling",
      mode: parser.mode,
      // Figure out what scriptLevel to use by pulling out the scriptLevel from
      // the function name
      scriptLevel,
      body
    };
  },
  mathmlBuilder(group, style) {
    // Figure out what scriptLevel we're changing to.
    const newStyle = style.withLevel(styleMap[group.scriptLevel]);
    // The style argument in the next line does NOT directly set a MathML script level.
    // It just tracks the style level, in case we need to know it for supsub or mathchoice.
    const inner = buildExpression(group.body, newStyle);
    // Wrap with an <mstyle> element.
    const node = wrapWithMstyle(inner);

    const attr = styleAttributes[group.scriptLevel];

    // Here is where we set the MathML script level.
    node.setAttribute("scriptlevel", attr[0]);
    node.setAttribute("displaystyle", attr[1]);

    return node;
  }
});

/**
 * Sometimes, groups perform special rules when they have superscripts or
 * subscripts attached to them. This function lets the `supsub` group know that
 * Sometimes, groups perform special rules when they have superscripts or
 * its inner element should handle the superscripts and subscripts instead of
 * handling them itself.
 */

// Helpers
const symbolRegEx = /^m(over|under|underover)$/;

// Super scripts and subscripts, whose precise placement can depend on other
// functions that precede them.
defineFunctionBuilders({
  type: "supsub",
  mathmlBuilder(group, style) {
    // Is the inner group a relevant horizonal brace?
    let isBrace = false;
    let isOver;
    let isSup;
    let appendApplyFunction = false;
    let appendSpace = false;
    let needsLeadingSpace = false;

    if (group.base && group.base.type === "horizBrace") {
      isSup = !!group.sup;
      if (isSup === group.base.isOver) {
        isBrace = true;
        isOver = group.base.isOver;
      }
    }

    if (group.base && !group.base.stack &&
      (group.base.type === "op" || group.base.type === "operatorname")) {
      group.base.parentIsSupSub = true;
      appendApplyFunction = !group.base.symbol;
      appendSpace = appendApplyFunction && !group.isFollowedByDelimiter;
      needsLeadingSpace = group.base.needsLeadingSpace;
    }

    const children = group.base && group.base.stack
      ? [buildGroup$1(group.base.body[0], style)]
      : [buildGroup$1(group.base, style)];

    // Note regarding scriptstyle level.
    // (Sub|super)scripts should not shrink beyond MathML scriptlevel 2 aka \scriptscriptstyle
    // Ref: https://w3c.github.io/mathml-core/#the-displaystyle-and-scriptlevel-attributes
    // (BTW, MathML scriptlevel 2 is equal to Temml level 3.)
    // But Chromium continues to shrink the (sub|super)scripts. So we explicitly set scriptlevel 2.

    const childStyle = style.inSubOrSup();
    if (group.sub) {
      const sub = buildGroup$1(group.sub, childStyle);
      if (style.level === 3) { sub.setAttribute("scriptlevel", "2"); }
      children.push(sub);
    }

    if (group.sup) {
      const sup = buildGroup$1(group.sup, childStyle);
      if (style.level === 3) { sup.setAttribute("scriptlevel", "2"); }
      const testNode = sup.type === "mrow" ? sup.children[0] : sup;
      if ((testNode && testNode.type === "mo" && testNode.classes.includes("tml-prime"))
        && group.base && group.base.text && "fF".indexOf(group.base.text) > -1) {
        // Chromium does not address italic correction on prime.  Prevent f′ from overlapping.
        testNode.classes.push("prime-pad");
      }
      children.push(sup);
    }

    let nodeType;
    if (isBrace) {
      nodeType = isOver ? "mover" : "munder";
    } else if (!group.sub) {
      const base = group.base;
      if (
        base &&
        base.type === "op" &&
        base.limits &&
        (style.level === StyleLevel.DISPLAY || base.alwaysHandleSupSub)
      ) {
        nodeType = "mover";
      } else if (
        base &&
        base.type === "operatorname" &&
        base.alwaysHandleSupSub &&
        (base.limits || style.level === StyleLevel.DISPLAY)
      ) {
        nodeType = "mover";
      } else {
        nodeType = "msup";
      }
    } else if (!group.sup) {
      const base = group.base;
      if (
        base &&
        base.type === "op" &&
        base.limits &&
        (style.level === StyleLevel.DISPLAY || base.alwaysHandleSupSub)
      ) {
        nodeType = "munder";
      } else if (
        base &&
        base.type === "operatorname" &&
        base.alwaysHandleSupSub &&
        (base.limits || style.level === StyleLevel.DISPLAY)
      ) {
        nodeType = "munder";
      } else {
        nodeType = "msub";
      }
    } else {
      const base = group.base;
      if (base && ((base.type === "op" && base.limits) || base.type === "multiscript") &&
        (style.level === StyleLevel.DISPLAY || base.alwaysHandleSupSub)
      ) {
        nodeType = "munderover";
      } else if (
        base &&
        base.type === "operatorname" &&
        base.alwaysHandleSupSub &&
        (style.level === StyleLevel.DISPLAY || base.limits)
      ) {
        nodeType = "munderover";
      } else {
        nodeType = "msubsup";
      }
    }

    let node = new mathMLTree.MathNode(nodeType, children);
    if (appendApplyFunction) {
      // Append an <mo>&ApplyFunction;</mo>.
      // ref: https://www.w3.org/TR/REC-MathML/chap3_2.html#sec3.2.4
      const operator = new mathMLTree.MathNode("mo", [makeText("\u2061", "text")]);
      if (needsLeadingSpace) {
        const space = new mathMLTree.MathNode("mspace");
        space.setAttribute("width", "0.1667em"); // thin space.
        node = mathMLTree.newDocumentFragment([space, node, operator]);
      } else {
        node = mathMLTree.newDocumentFragment([node, operator]);
      }
      if (appendSpace) {
        const space = new mathMLTree.MathNode("mspace");
        space.setAttribute("width", "0.1667em"); // thin space.
        node.children.push(space);
      }
    } else if (symbolRegEx.test(nodeType)) {
      // Wrap in a <mrow>. Otherwise Firefox stretchy parens will not stretch to include limits.
      node = new mathMLTree.MathNode("mrow", [node]);
    }

    return node
  }
});

// Operator ParseNodes created in Parser.js from symbol Groups in src/symbols.js.

const temml_short = ["\\shortmid", "\\nshortmid", "\\shortparallel",
  "\\nshortparallel", "\\smallsetminus"];

const arrows = ["\\Rsh", "\\Lsh", "\\restriction"];

const isArrow = str => {
  if (str.length === 1) {
    const codePoint = str.codePointAt(0);
    return (0x218f < codePoint && codePoint < 0x2200)
  }
  return str.indexOf("arrow") > -1 || str.indexOf("harpoon") > -1 || arrows.includes(str)
};

defineFunctionBuilders({
  type: "atom",
  mathmlBuilder(group, style) {
    const node = new mathMLTree.MathNode("mo", [makeText(group.text, group.mode)]);
    if (group.family === "punct") {
      node.setAttribute("separator", "true");
    } else if (group.family === "open" || group.family === "close") {
      // Delims built here should not stretch vertically.
      // See delimsizing.js for stretchy delims.
      if (group.family === "open") {
        node.setAttribute("form", "prefix");
        // Set an explicit attribute for stretch. Otherwise Firefox may do it wrong.
        node.setAttribute("stretchy", "false");
      } else if (group.family === "close") {
        node.setAttribute("form", "postfix");
        node.setAttribute("stretchy", "false");
      }
    } else if (group.text === "\\mid") {
      // Firefox messes up this spacing if at the end of an <mrow>. See it explicitly.
      node.setAttribute("lspace", "0.22em"); // medium space
      node.setAttribute("rspace", "0.22em");
      node.setAttribute("stretchy", "false");
    } else if (group.family === "rel" && isArrow(group.text)) {
      node.setAttribute("stretchy", "false");
    } else if (temml_short.includes(group.text)) {
      node.setAttribute("mathsize", "70%");
    } else if (group.text === ":") {
      // ":" is not in the MathML operator dictionary. Give it BIN spacing.
      node.attributes.lspace = "0.2222em";
      node.attributes.rspace = "0.2222em";
    }
    return node;
  }
});

/**
 * Maps TeX font commands to "mathvariant" attribute in buildMathML.js
 */
const fontMap = {
  // styles
  mathbf: "bold",
  mathrm: "normal",
  textit: "italic",
  mathit: "italic",
  mathnormal: "italic",

  // families
  mathbb: "double-struck",
  mathcal: "script",
  mathfrak: "fraktur",
  mathscr: "script",
  mathsf: "sans-serif",
  mathtt: "monospace"
};

/**
 * Returns the math variant as a string or null if none is required.
 */
const getVariant = function(group, style) {
  // Handle font specifiers as best we can.
  // Chromium does not support the MathML mathvariant attribute.
  // So we'll use Unicode replacement characters instead.
  // But first, determine the math variant.

  // Deal with the \textit, \textbf, etc., functions.
  if (style.fontFamily === "texttt") {
    return "monospace"
  } else if (style.fontFamily === "textsc") {
    return "normal"; // handled via character substitution in symbolsOrd.js.
  } else if (style.fontFamily === "textsf") {
    if (style.fontShape === "textit" && style.fontWeight === "textbf") {
      return "sans-serif-bold-italic"
    } else if (style.fontShape === "textit") {
      return "sans-serif-italic"
    } else if (style.fontWeight === "textbf") {
      return "sans-serif-bold"
    } else {
      return "sans-serif"
    }
  } else if (style.fontShape === "textit" && style.fontWeight === "textbf") {
    return "bold-italic"
  } else if (style.fontShape === "textit") {
    return "italic"
  } else if (style.fontWeight === "textbf") {
    return "bold"
  }

  // Deal with the \mathit, mathbf, etc, functions.
  const font = style.font;
  if (!font || font === "mathnormal") {
    return null
  }

  const mode = group.mode;
  switch (font) {
    case "mathit":
      return "italic"
    case "mathrm": {
      const codePoint = group.text.codePointAt(0);
      // LaTeX \mathrm returns italic for Greek characters.
      return  (0x03ab < codePoint && codePoint < 0x03cf) ? "italic" : "normal"
    }
    case "greekItalic":
      return "italic"
    case "up@greek":
      return "normal"
    case "boldsymbol":
    case "mathboldsymbol":
      return "bold-italic"
    case "mathbf":
      return "bold"
    case "mathbb":
      return "double-struck"
    case "mathfrak":
      return "fraktur"
    case "mathscr":
    case "mathcal":
      return "script"
    case "mathsf":
      return "sans-serif"
    case "mathsfit":
      return "sans-serif-italic"
    case "mathtt":
      return "monospace"
  }

  let text = group.text;
  if (symbols[mode][text] && symbols[mode][text].replace) {
    text = symbols[mode][text].replace;
  }

  return Object.prototype.hasOwnProperty.call(fontMap, font) ? fontMap[font] : null
};

// Chromium does not support the MathML `mathvariant` attribute.
// Instead, we replace ASCII characters with Unicode characters that
// are defined in the font as bold, italic, double-struck, etc.
// This module identifies those Unicode code points.

// First, a few helpers.
const script = Object.freeze({
  B: 0x20EA, // Offset from ASCII B to Unicode script B
  E: 0x20EB,
  F: 0x20EB,
  H: 0x20C3,
  I: 0x20C7,
  L: 0x20C6,
  M: 0x20E6,
  R: 0x20C9,
  e: 0x20CA,
  g: 0x20A3,
  o: 0x20C5
});

const frak = Object.freeze({
  C: 0x20EA,
  H: 0x20C4,
  I: 0x20C8,
  R: 0x20CA,
  Z: 0x20CE
});

const bbb = Object.freeze({
  C: 0x20BF, // blackboard bold
  H: 0x20C5,
  N: 0x20C7,
  P: 0x20C9,
  Q: 0x20C9,
  R: 0x20CB,
  Z: 0x20CA
});

const bold = Object.freeze({
  "\u03f5": 0x1D2E7, // lunate epsilon
  "\u03d1": 0x1D30C, // vartheta
  "\u03f0": 0x1D2EE, // varkappa
  "\u03c6": 0x1D319, // varphi
  "\u03f1": 0x1D2EF, // varrho
  "\u03d6": 0x1D30B  // varpi
});

const boldItalic = Object.freeze({
  "\u03f5": 0x1D35B, // lunate epsilon
  "\u03d1": 0x1D380, // vartheta
  "\u03f0": 0x1D362, // varkappa
  "\u03c6": 0x1D38D, // varphi
  "\u03f1": 0x1D363, // varrho
  "\u03d6": 0x1D37F  // varpi
});

const boldsf = Object.freeze({
  "\u03f5": 0x1D395, // lunate epsilon
  "\u03d1": 0x1D3BA, // vartheta
  "\u03f0": 0x1D39C, // varkappa
  "\u03c6": 0x1D3C7, // varphi
  "\u03f1": 0x1D39D, // varrho
  "\u03d6": 0x1D3B9  // varpi
});

const bisf = Object.freeze({
  "\u03f5": 0x1D3CF, // lunate epsilon
  "\u03d1": 0x1D3F4, // vartheta
  "\u03f0": 0x1D3D6, // varkappa
  "\u03c6": 0x1D401, // varphi
  "\u03f1": 0x1D3D7, // varrho
  "\u03d6": 0x1D3F3  // varpi
});

// Code point offsets below are derived from https://www.unicode.org/charts/PDF/U1D400.pdf
const offset = Object.freeze({
  upperCaseLatin: { // A-Z
    "normal": ch =>                 { return 0 },
    "bold": ch =>                   { return 0x1D3BF },
    "italic": ch =>                 { return 0x1D3F3 },
    "bold-italic": ch =>            { return 0x1D427 },
    "script": ch =>                 { return script[ch] || 0x1D45B },
    "script-bold": ch =>            { return 0x1D48F },
    "fraktur": ch =>                { return frak[ch] || 0x1D4C3 },
    "fraktur-bold": ch =>           { return 0x1D52B },
    "double-struck": ch =>          { return bbb[ch] || 0x1D4F7 },
    "sans-serif": ch =>             { return 0x1D55F },
    "sans-serif-bold": ch =>        { return 0x1D593 },
    "sans-serif-italic": ch =>      { return 0x1D5C7 },
    "sans-serif-bold-italic": ch => { return 0x1D63C },
    "monospace": ch =>              { return 0x1D62F }
  },
  lowerCaseLatin: { // a-z
    "normal": ch =>                 { return 0 },
    "bold": ch =>                   { return 0x1D3B9 },
    "italic": ch =>                 { return ch === "h" ? 0x20A6 : 0x1D3ED },
    "bold-italic": ch =>            { return 0x1D421 },
    "script": ch =>                 { return script[ch] || 0x1D455 },
    "script-bold": ch =>            { return 0x1D489 },
    "fraktur": ch =>                { return 0x1D4BD },
    "fraktur-bold": ch =>           { return 0x1D525 },
    "double-struck": ch =>          { return 0x1D4F1 },
    "sans-serif": ch =>             { return 0x1D559 },
    "sans-serif-bold": ch =>        { return 0x1D58D },
    "sans-serif-italic": ch =>      { return 0x1D5C1 },
    "sans-serif-bold-italic": ch => { return 0x1D5F5 },
    "monospace": ch =>              { return 0x1D629 }
  },
  upperCaseGreek: { // A-Ω
    "normal": ch =>                 { return 0 },
    "bold": ch =>                   { return 0x1D317 },
    "italic": ch =>                 { return 0x1D351 },
    // \boldsymbol actually returns upright bold for upperCaseGreek
    "bold-italic": ch =>            { return 0x1D317 },
    "script": ch =>                 { return 0 },
    "script-bold": ch =>            { return 0 },
    "fraktur": ch =>                { return 0 },
    "fraktur-bold": ch =>           { return 0 },
    "double-struck": ch =>          { return 0 },
    // Unicode has no code points for regular-weight san-serif Greek. Use bold.
    "sans-serif": ch =>             { return 0x1D3C5 },
    "sans-serif-bold": ch =>        { return 0x1D3C5 },
    "sans-serif-italic": ch =>      { return 0 },
    "sans-serif-bold-italic": ch => { return 0x1D3FF },
    "monospace": ch =>              { return 0 }
  },
  lowerCaseGreek: { // α-ω
    "normal": ch =>                 { return 0 },
    "bold": ch =>                   { return 0x1D311 },
    "italic": ch =>                 { return 0x1D34B },
    "bold-italic": ch =>            { return ch === "\u03d5" ? 0x1D37E : 0x1D385 },
    "script": ch =>                 { return 0 },
    "script-bold": ch =>            { return 0 },
    "fraktur": ch =>                { return 0 },
    "fraktur-bold": ch =>           { return 0 },
    "double-struck": ch =>          { return 0 },
    // Unicode has no code points for regular-weight san-serif Greek. Use bold.
    "sans-serif": ch =>             { return 0x1D3BF },
    "sans-serif-bold": ch =>        { return 0x1D3BF },
    "sans-serif-italic": ch =>      { return 0 },
    "sans-serif-bold-italic": ch => { return 0x1D3F9 },
    "monospace": ch =>              { return 0 }
  },
  varGreek: { // \varGamma, etc
    "normal": ch =>                 { return 0 },
    "bold": ch =>                   { return  bold[ch] || -51 },
    "italic": ch =>                 { return 0 },
    "bold-italic": ch =>            { return boldItalic[ch] || 0x3A },
    "script": ch =>                 { return 0 },
    "script-bold": ch =>            { return 0 },
    "fraktur": ch =>                { return 0 },
    "fraktur-bold": ch =>           { return 0 },
    "double-struck": ch =>          { return 0 },
    "sans-serif": ch =>             { return boldsf[ch] || 0x74 },
    "sans-serif-bold": ch =>        { return boldsf[ch] || 0x74 },
    "sans-serif-italic": ch =>      { return 0 },
    "sans-serif-bold-italic": ch => { return bisf[ch] || 0xAE },
    "monospace": ch =>              { return 0 }
  },
  numeral: { // 0-9
    "normal": ch =>                 { return 0 },
    "bold": ch =>                   { return 0x1D79E },
    "italic": ch =>                 { return 0 },
    "bold-italic": ch =>            { return 0 },
    "script": ch =>                 { return 0 },
    "script-bold": ch =>            { return 0 },
    "fraktur": ch =>                { return 0 },
    "fraktur-bold": ch =>           { return 0 },
    "double-struck": ch =>          { return 0x1D7A8 },
    "sans-serif": ch =>             { return 0x1D7B2 },
    "sans-serif-bold": ch =>        { return 0x1D7BC },
    "sans-serif-italic": ch =>      { return 0 },
    "sans-serif-bold-italic": ch => { return 0 },
    "monospace": ch =>              { return 0x1D7C6 }
  }
});

const variantChar = (ch, variant) => {
  const codePoint = ch.codePointAt(0);
  const block = 0x40 < codePoint && codePoint < 0x5b
    ? "upperCaseLatin"
    : 0x60 < codePoint && codePoint < 0x7b
    ? "lowerCaseLatin"
    : (0x390  < codePoint && codePoint < 0x3AA)
    ? "upperCaseGreek"
    : 0x3B0 < codePoint && codePoint < 0x3CA || ch === "\u03d5"
    ? "lowerCaseGreek"
    : 0x1D6E1 < codePoint && codePoint < 0x1D6FC  || bold[ch]
    ? "varGreek"
    : (0x2F < codePoint && codePoint <  0x3A)
    ? "numeral"
    : "other";
  return block === "other"
    ? ch
    : String.fromCodePoint(codePoint + offset[block][variant](ch))
};

const smallCaps = Object.freeze({
  a: "ᴀ",
  b: "ʙ",
  c: "ᴄ",
  d: "ᴅ",
  e: "ᴇ",
  f: "ꜰ",
  g: "ɢ",
  h: "ʜ",
  i: "ɪ",
  j: "ᴊ",
  k: "ᴋ",
  l: "ʟ",
  m: "ᴍ",
  n: "ɴ",
  o: "ᴏ",
  p: "ᴘ",
  q: "ǫ",
  r: "ʀ",
  s: "s",
  t: "ᴛ",
  u: "ᴜ",
  v: "ᴠ",
  w: "ᴡ",
  x: "x",
  y: "ʏ",
  z: "ᴢ"
});

// "mathord" and "textord" ParseNodes created in Parser.js from symbol Groups in
// src/symbols.js.

const numberRegEx = /^\d(?:[\d,.]*\d)?$/;
const latinRegEx = /[A-Ba-z]/;
const primes = new Set(["\\prime", "\\dprime", "\\trprime", "\\qprime",
  "\\backprime", "\\backdprime", "\\backtrprime"]);

const italicNumber = (text, variant, tag) => {
  const mn = new mathMLTree.MathNode(tag, [text]);
  const wrapper = new mathMLTree.MathNode("mstyle", [mn]);
  wrapper.style["font-style"] = "italic";
  wrapper.style["font-family"] = "Cambria, 'Times New Roman', serif";
  if (variant === "bold-italic") { wrapper.style["font-weight"] = "bold"; }
  return wrapper
};

defineFunctionBuilders({
  type: "mathord",
  mathmlBuilder(group, style) {
    const text = makeText(group.text, group.mode, style);
    const codePoint = text.text.codePointAt(0);
    // Test for upper-case Greek
    const defaultVariant = (0x0390 < codePoint && codePoint < 0x03aa) ? "normal" : "italic";
    const variant = getVariant(group, style) || defaultVariant;
    if (variant === "script") {
      text.text = variantChar(text.text, variant);
      return new mathMLTree.MathNode("mi", [text], [style.font])
    } else if (variant !== "italic") {
      text.text = variantChar(text.text, variant);
    }
    let node = new mathMLTree.MathNode("mi", [text]);
    // TODO: Handle U+1D49C - U+1D4CF per https://www.unicode.org/charts/PDF/U1D400.pdf
    if (variant === "normal") {
      node.setAttribute("mathvariant", "normal");
      if (text.text.length === 1) {
        // A Firefox bug will apply spacing here, but there should be none. Fix it.
        node = new mathMLTree.MathNode("mrow", [node]);
      }
    }
    return node
  }
});

defineFunctionBuilders({
  type: "textord",
  mathmlBuilder(group, style) {
    let ch = group.text;
    const codePoint = ch.codePointAt(0);
    if (style.fontFamily === "textsc") {
      // Convert small latin letters to small caps.
      if (96 < codePoint && codePoint < 123) {
        ch = smallCaps[ch];
      }
    }
    const text = makeText(ch, group.mode, style);
    const variant = getVariant(group, style) || "normal";

    let node;
    if (numberRegEx.test(group.text)) {
      const tag = group.mode === "text" ? "mtext" : "mn";
      if (variant === "italic" || variant === "bold-italic") {
        return italicNumber(text, variant, tag)
      } else {
        if (variant !== "normal") {
          text.text = text.text.split("").map(c => variantChar(c, variant)).join("");
        }
        node = new mathMLTree.MathNode(tag, [text]);
      }
    } else if (group.mode === "text") {
      if (variant !== "normal") {
        text.text = variantChar(text.text, variant);
      }
      node = new mathMLTree.MathNode("mtext", [text]);
    } else if (primes.has(group.text)) {
      node = new mathMLTree.MathNode("mo", [text]);
      // TODO: If/when Chromium uses ssty variant for prime, remove the next line.
      node.classes.push("tml-prime");
    } else {
      const origText = text.text;
      if (variant !== "italic") {
        text.text = variantChar(text.text, variant);
      }
      node = new mathMLTree.MathNode("mi", [text]);
      if (text.text === origText && latinRegEx.test(origText)) {
        node.setAttribute("mathvariant", "italic");
      }
    }
    return node
  }
});

// A map of CSS-based spacing functions to their CSS class.
const cssSpace = {
  "\\nobreak": "nobreak",
  "\\allowbreak": "allowbreak"
};

// A lookup table to determine whether a spacing function/symbol should be
// treated like a regular space character.  If a symbol or command is a key
// in this table, then it should be a regular space character.  Furthermore,
// the associated value may have a `className` specifying an extra CSS class
// to add to the created `span`.
const regularSpace = {
  " ": {},
  "\\ ": {},
  "~": {
    className: "nobreak"
  },
  "\\space": {},
  "\\nobreakspace": {
    className: "nobreak"
  }
};

// ParseNode<"spacing"> created in Parser.js from the "spacing" symbol Groups in
// src/symbols.js.
defineFunctionBuilders({
  type: "spacing",
  mathmlBuilder(group, style) {
    let node;

    if (Object.prototype.hasOwnProperty.call(regularSpace, group.text)) {
      // Firefox does not render a space in a <mtext> </mtext>. So write a no-break space.
      // TODO: If Firefox fixes that bug, uncomment the next line and write ch into the node.
      //const ch = (regularSpace[group.text].className === "nobreak") ? "\u00a0" : " "
      node = new mathMLTree.MathNode("mtext", [new mathMLTree.TextNode("\u00a0")]);
    } else if (Object.prototype.hasOwnProperty.call(cssSpace, group.text)) {
      // MathML 3.0 calls for nobreak to occur in an <mo>, not an <mtext>
      // Ref: https://www.w3.org/Math/draft-spec/mathml.html#chapter3_presm.lbattrs
      node = new mathMLTree.MathNode("mo");
      if (group.text === "\\nobreak") {
        node.setAttribute("linebreak", "nobreak");
      }
    } else {
      throw new ParseError(`Unknown type of space "${group.text}"`)
    }

    return node
  }
});

defineFunctionBuilders({
  type: "tag"
});

// For a \tag, the work usually done in a mathmlBuilder is instead done in buildMathML.js.
// That way, a \tag can be pulled out of the parse tree and wrapped around the outer node.

// Non-mathy text, possibly in a font
const textFontFamilies = {
  "\\text": undefined,
  "\\textrm": "textrm",
  "\\textsf": "textsf",
  "\\texttt": "texttt",
  "\\textnormal": "textrm",
  "\\textsc": "textsc"      // small caps
};

const textFontWeights = {
  "\\textbf": "textbf",
  "\\textmd": "textmd"
};

const textFontShapes = {
  "\\textit": "textit",
  "\\textup": "textup"
};

const styleWithFont = (group, style) => {
  const font = group.font;
  // Checks if the argument is a font family or a font style.
  if (!font) {
    return style;
  } else if (textFontFamilies[font]) {
    return style.withTextFontFamily(textFontFamilies[font]);
  } else if (textFontWeights[font]) {
    return style.withTextFontWeight(textFontWeights[font]);
  } else if (font === "\\emph") {
    return style.fontShape === "textit"
      ? style.withTextFontShape("textup")
      : style.withTextFontShape("textit")
  }
  return style.withTextFontShape(textFontShapes[font])
};

defineFunction({
  type: "text",
  names: [
    // Font families
    "\\text",
    "\\textrm",
    "\\textsf",
    "\\texttt",
    "\\textnormal",
    "\\textsc",
    // Font weights
    "\\textbf",
    "\\textmd",
    // Font Shapes
    "\\textit",
    "\\textup",
    "\\emph"
  ],
  props: {
    numArgs: 1,
    argTypes: ["text"],
    allowedInArgument: true,
    allowedInText: true
  },
  handler({ parser, funcName }, args) {
    const body = args[0];
    return {
      type: "text",
      mode: parser.mode,
      body: ordargument(body),
      font: funcName
    };
  },
  mathmlBuilder(group, style) {
    const newStyle = styleWithFont(group, style);
    const mrow = buildExpressionRow(group.body, newStyle);
    return consolidateText(mrow)
  }
});

// \vcenter:  Vertically center the argument group on the math axis.

defineFunction({
  type: "vcenter",
  names: ["\\vcenter"],
  props: {
    numArgs: 1,
    argTypes: ["original"],
    allowedInText: false
  },
  handler({ parser }, args) {
    return {
      type: "vcenter",
      mode: parser.mode,
      body: args[0]
    };
  },
  mathmlBuilder(group, style) {
    // Use a math table to create vertically centered content.
    const mtd = new mathMLTree.MathNode("mtd", [buildGroup$1(group.body, style)]);
    mtd.style.padding = "0";
    const mtr = new mathMLTree.MathNode("mtr", [mtd]);
    return new mathMLTree.MathNode("mtable", [mtr])
  }
});

defineFunction({
  type: "verb",
  names: ["\\verb"],
  props: {
    numArgs: 0,
    allowedInText: true
  },
  handler(context, args, optArgs) {
    // \verb and \verb* are dealt with directly in Parser.js.
    // If we end up here, it's because of a failure to match the two delimiters
    // in the regex in Lexer.js.  LaTeX raises the following error when \verb is
    // terminated by end of line (or file).
    throw new ParseError("\\verb ended by end of line instead of matching delimiter");
  },
  mathmlBuilder(group, style) {
    const text = new mathMLTree.TextNode(makeVerb(group));
    const node = new mathMLTree.MathNode("mtext", [text]);
    node.setAttribute("mathvariant", "monospace");
    return node;
  }
});

/**
 * Converts verb group into body string.
 *
 * \verb* replaces each space with an open box \u2423
 * \verb replaces each space with a no-break space \xA0
 */
const makeVerb = (group) => group.body.replace(/ /g, group.star ? "\u2423" : "\xA0");

/** Include this to ensure that all functions are defined. */

const functions = _functions;

/**
 * The Lexer class handles tokenizing the input in various ways. Since our
 * parser expects us to be able to backtrack, the lexer allows lexing from any
 * given starting point.
 *
 * Its main exposed function is the `lex` function, which takes a position to
 * lex from and a type of token to lex. It defers to the appropriate `_innerLex`
 * function.
 *
 * The various `_innerLex` functions perform the actual lexing of different
 * kinds.
 */


/* The following tokenRegex
 * - matches typical whitespace (but not NBSP etc.) using its first two groups
 * - does not match any control character \x00-\x1f except whitespace
 * - does not match a bare backslash
 * - matches any ASCII character except those just mentioned
 * - does not match the BMP private use area \uE000-\uF8FF
 * - does not match bare surrogate code units
 * - matches any BMP character except for those just described
 * - matches any valid Unicode surrogate pair
 * - mathches numerals
 * - matches a backslash followed by one or more whitespace characters
 * - matches a backslash followed by one or more letters then whitespace
 * - matches a backslash followed by any BMP character
 * Capturing groups:
 *   [1] regular whitespace
 *   [2] backslash followed by whitespace
 *   [3] anything else, which may include:
 *     [4] left character of \verb*
 *     [5] left character of \verb
 *     [6] backslash followed by word, excluding any trailing whitespace
 * Just because the Lexer matches something doesn't mean it's valid input:
 * If there is no matching function or symbol definition, the Parser will
 * still reject the input.
 */
const spaceRegexString = "[ \r\n\t]";
const controlWordRegexString = "\\\\[a-zA-Z@]+";
const controlSymbolRegexString = "\\\\[^\uD800-\uDFFF]";
const controlWordWhitespaceRegexString = `(${controlWordRegexString})${spaceRegexString}*`;
const controlSpaceRegexString = "\\\\(\n|[ \r\t]+\n?)[ \r\t]*";
const combiningDiacriticalMarkString = "[\u0300-\u036f]";
const combiningDiacriticalMarksEndRegex = new RegExp(`${combiningDiacriticalMarkString}+$`);
const tokenRegexString =
  `(${spaceRegexString}+)|` + // whitespace
  `${controlSpaceRegexString}|` +  // whitespace
  "([!-\\[\\]-\u2027\u202A-\uD7FF\uF900-\uFFFF]" + // single codepoint
  `${combiningDiacriticalMarkString}*` + // ...plus accents
  "|[\uD800-\uDBFF][\uDC00-\uDFFF]" + // surrogate pair
  `${combiningDiacriticalMarkString}*` + // ...plus accents
  "|\\\\verb\\*([^]).*?\\4" + // \verb*
  "|\\\\verb([^*a-zA-Z]).*?\\5" + // \verb unstarred
  `|${controlWordWhitespaceRegexString}` + // \macroName + spaces
  `|${controlSymbolRegexString})`; // \\, \', etc.

/** Main Lexer class */
class Lexer {
  constructor(input, settings) {
    // Separate accents from characters
    this.input = input;
    this.settings = settings;
    this.tokenRegex = new RegExp(tokenRegexString, 'g');
    // Category codes. The lexer only supports comment characters (14) for now.
    // MacroExpander additionally distinguishes active (13).
    this.catcodes = {
      "%": 14, // comment character
      "~": 13  // active character
    };
  }

  setCatcode(char, code) {
    this.catcodes[char] = code;
  }

  /**
   * This function lexes a single token.
   */
  lex() {
    const input = this.input;
    const pos = this.tokenRegex.lastIndex;
    if (pos === input.length) {
      return new Token("EOF", new SourceLocation(this, pos, pos));
    }
    const match = this.tokenRegex.exec(input);
    if (match === null || match.index !== pos) {
      throw new ParseError(
        `Unexpected character: '${input[pos]}'`,
        new Token(input[pos], new SourceLocation(this, pos, pos + 1))
      );
    }
    const text = match[6] || match[3] || (match[2] ? "\\ " : " ");

    if (this.catcodes[text] === 14) {
      // comment character
      const nlIndex = input.indexOf("\n", this.tokenRegex.lastIndex);
      if (nlIndex === -1) {
        this.tokenRegex.lastIndex = input.length; // EOF
        if (this.settings.strict) {
          throw new ParseError("% comment has no terminating newline; LaTeX would " +
              "fail because of commenting the end of math mode")
        }
      } else {
        this.tokenRegex.lastIndex = nlIndex + 1;
      }
      return this.lex();
    }

    return new Token(text, new SourceLocation(this, pos, this.tokenRegex.lastIndex));
  }
}

/**
 * A `Namespace` refers to a space of nameable things like macros or lengths,
 * which can be `set` either globally or local to a nested group, using an
 * undo stack similar to how TeX implements this functionality.
 * Performance-wise, `get` and local `set` take constant time, while global
 * `set` takes time proportional to the depth of group nesting.
 */


class Namespace {
  /**
   * Both arguments are optional.  The first argument is an object of
   * built-in mappings which never change.  The second argument is an object
   * of initial (global-level) mappings, which will constantly change
   * according to any global/top-level `set`s done.
   */
  constructor(builtins = {}, globalMacros = {}) {
    this.current = globalMacros;
    this.builtins = builtins;
    this.undefStack = [];
  }

  /**
   * Start a new nested group, affecting future local `set`s.
   */
  beginGroup() {
    this.undefStack.push({});
  }

  /**
   * End current nested group, restoring values before the group began.
   */
  endGroup() {
    if (this.undefStack.length === 0) {
      throw new ParseError(
        "Unbalanced namespace destruction: attempt " +
          "to pop global namespace; please report this as a bug"
      );
    }
    const undefs = this.undefStack.pop();
    for (const undef in undefs) {
      if (Object.prototype.hasOwnProperty.call(undefs, undef )) {
        if (undefs[undef] === undefined) {
          delete this.current[undef];
        } else {
          this.current[undef] = undefs[undef];
        }
      }
    }
  }

  /**
   * Detect whether `name` has a definition.  Equivalent to
   * `get(name) != null`.
   */
  has(name) {
    return Object.prototype.hasOwnProperty.call(this.current, name ) ||
    Object.prototype.hasOwnProperty.call(this.builtins, name );
  }

  /**
   * Get the current value of a name, or `undefined` if there is no value.
   *
   * Note: Do not use `if (namespace.get(...))` to detect whether a macro
   * is defined, as the definition may be the empty string which evaluates
   * to `false` in JavaScript.  Use `if (namespace.get(...) != null)` or
   * `if (namespace.has(...))`.
   */
  get(name) {
    if (Object.prototype.hasOwnProperty.call(this.current, name )) {
      return this.current[name];
    } else {
      return this.builtins[name];
    }
  }

  /**
   * Set the current value of a name, and optionally set it globally too.
   * Local set() sets the current value and (when appropriate) adds an undo
   * operation to the undo stack.  Global set() may change the undo
   * operation at every level, so takes time linear in their number.
   */
  set(name, value, global = false) {
    if (global) {
      // Global set is equivalent to setting in all groups.  Simulate this
      // by destroying any undos currently scheduled for this name,
      // and adding an undo with the *new* value (in case it later gets
      // locally reset within this environment).
      for (let i = 0; i < this.undefStack.length; i++) {
        delete this.undefStack[i][name];
      }
      if (this.undefStack.length > 0) {
        this.undefStack[this.undefStack.length - 1][name] = value;
      }
    } else {
      // Undo this set at end of this group (possibly to `undefined`),
      // unless an undo is already in place, in which case that older
      // value is the correct one.
      const top = this.undefStack[this.undefStack.length - 1];
      if (top && !Object.prototype.hasOwnProperty.call(top, name )) {
        top[name] = this.current[name];
      }
    }
    this.current[name] = value;
  }
}

/**
 * This file contains the “gullet” where macros are expanded
 * until only non-macro tokens remain.
 */


// List of commands that act like macros but aren't defined as a macro,
// function, or symbol.  Used in `isDefined`.
const implicitCommands = {
  "^": true, // Parser.js
  _: true, // Parser.js
  "\\limits": true, // Parser.js
  "\\nolimits": true // Parser.js
};

class MacroExpander {
  constructor(input, settings, mode) {
    this.settings = settings;
    this.expansionCount = 0;
    this.feed(input);
    // Make new global namespace
    this.macros = new Namespace(macros, settings.macros);
    this.mode = mode;
    this.stack = []; // contains tokens in REVERSE order
  }

  /**
   * Feed a new input string to the same MacroExpander
   * (with existing macros etc.).
   */
  feed(input) {
    this.lexer = new Lexer(input, this.settings);
  }

  /**
   * Switches between "text" and "math" modes.
   */
  switchMode(newMode) {
    this.mode = newMode;
  }

  /**
   * Start a new group nesting within all namespaces.
   */
  beginGroup() {
    this.macros.beginGroup();
  }

  /**
   * End current group nesting within all namespaces.
   */
  endGroup() {
    this.macros.endGroup();
  }

  /**
   * Returns the topmost token on the stack, without expanding it.
   * Similar in behavior to TeX's `\futurelet`.
   */
  future() {
    if (this.stack.length === 0) {
      this.pushToken(this.lexer.lex());
    }
    return this.stack[this.stack.length - 1]
  }

  /**
   * Remove and return the next unexpanded token.
   */
  popToken() {
    this.future(); // ensure non-empty stack
    return this.stack.pop();
  }

  /**
   * Add a given token to the token stack.  In particular, this get be used
   * to put back a token returned from one of the other methods.
   */
  pushToken(token) {
    this.stack.push(token);
  }

  /**
   * Append an array of tokens to the token stack.
   */
  pushTokens(tokens) {
    this.stack.push(...tokens);
  }

  /**
   * Find an macro argument without expanding tokens and append the array of
   * tokens to the token stack. Uses Token as a container for the result.
   */
  scanArgument(isOptional) {
    let start;
    let end;
    let tokens;
    if (isOptional) {
      this.consumeSpaces(); // \@ifnextchar gobbles any space following it
      if (this.future().text !== "[") {
        return null;
      }
      start = this.popToken(); // don't include [ in tokens
      ({ tokens, end } = this.consumeArg(["]"]));
    } else {
      ({ tokens, start, end } = this.consumeArg());
    }

    // indicate the end of an argument
    this.pushToken(new Token("EOF", end.loc));

    this.pushTokens(tokens);
    return start.range(end, "");
  }

  /**
   * Consume all following space tokens, without expansion.
   */
  consumeSpaces() {
    for (;;) {
      const token = this.future();
      if (token.text === " ") {
        this.stack.pop();
      } else {
        break;
      }
    }
  }

  /**
   * Consume an argument from the token stream, and return the resulting array
   * of tokens and start/end token.
   */
  consumeArg(delims) {
    // The argument for a delimited parameter is the shortest (possibly
    // empty) sequence of tokens with properly nested {...} groups that is
    // followed ... by this particular list of non-parameter tokens.
    // The argument for an undelimited parameter is the next nonblank
    // token, unless that token is ‘{’, when the argument will be the
    // entire {...} group that follows.
    const tokens = [];
    const isDelimited = delims && delims.length > 0;
    if (!isDelimited) {
      // Ignore spaces between arguments.  As the TeXbook says:
      // "After you have said ‘\def\row#1#2{...}’, you are allowed to
      //  put spaces between the arguments (e.g., ‘\row x n’), because
      //  TeX doesn’t use single spaces as undelimited arguments."
      this.consumeSpaces();
    }
    const start = this.future();
    let tok;
    let depth = 0;
    let match = 0;
    do {
      tok = this.popToken();
      tokens.push(tok);
      if (tok.text === "{") {
        ++depth;
      } else if (tok.text === "}") {
        --depth;
        if (depth === -1) {
          throw new ParseError("Extra }", tok);
        }
      } else if (tok.text === "EOF") {
        throw new ParseError(
          "Unexpected end of input in a macro argument" +
            ", expected '" +
            (delims && isDelimited ? delims[match] : "}") +
            "'",
          tok
        );
      }
      if (delims && isDelimited) {
        if ((depth === 0 || (depth === 1 && delims[match] === "{")) && tok.text === delims[match]) {
          ++match;
          if (match === delims.length) {
            // don't include delims in tokens
            tokens.splice(-match, match);
            break;
          }
        } else {
          match = 0;
        }
      }
    } while (depth !== 0 || isDelimited);
    // If the argument found ... has the form ‘{<nested tokens>}’,
    // ... the outermost braces enclosing the argument are removed
    if (start.text === "{" && tokens[tokens.length - 1].text === "}") {
      tokens.pop();
      tokens.shift();
    }
    tokens.reverse(); // to fit in with stack order
    return { tokens, start, end: tok };
  }

  /**
   * Consume the specified number of (delimited) arguments from the token
   * stream and return the resulting array of arguments.
   */
  consumeArgs(numArgs, delimiters) {
    if (delimiters) {
      if (delimiters.length !== numArgs + 1) {
        throw new ParseError("The length of delimiters doesn't match the number of args!");
      }
      const delims = delimiters[0];
      for (let i = 0; i < delims.length; i++) {
        const tok = this.popToken();
        if (delims[i] !== tok.text) {
          throw new ParseError("Use of the macro doesn't match its definition", tok);
        }
      }
    }

    const args = [];
    for (let i = 0; i < numArgs; i++) {
      args.push(this.consumeArg(delimiters && delimiters[i + 1]).tokens);
    }
    return args;
  }

  /**
   * Expand the next token only once if possible.
   *
   * If the token is expanded, the resulting tokens will be pushed onto
   * the stack in reverse order, and the number of such tokens will be
   * returned.  This number might be zero or positive.
   *
   * If not, the return value is `false`, and the next token remains at the
   * top of the stack.
   *
   * In either case, the next token will be on the top of the stack,
   * or the stack will be empty (in case of empty expansion
   * and no other tokens).
   *
   * Used to implement `expandAfterFuture` and `expandNextToken`.
   *
   * If expandableOnly, only expandable tokens are expanded and
   * an undefined control sequence results in an error.
   */
  expandOnce(expandableOnly) {
    const topToken = this.popToken();
    const name = topToken.text;
    const expansion = !topToken.noexpand ? this._getExpansion(name) : null;
    if (expansion == null || (expandableOnly && expansion.unexpandable)) {
      if (expandableOnly && expansion == null && name[0] === "\\" && !this.isDefined(name)) {
        throw new ParseError("Undefined control sequence: " + name);
      }
      this.pushToken(topToken);
      return false;
    }
    this.expansionCount++;
    if (this.expansionCount > this.settings.maxExpand) {
      throw new ParseError(
        "Too many expansions: infinite loop or " + "need to increase maxExpand setting"
      );
    }
    let tokens = expansion.tokens;
    const args = this.consumeArgs(expansion.numArgs, expansion.delimiters);
    if (expansion.numArgs) {
      // paste arguments in place of the placeholders
      tokens = tokens.slice(); // make a shallow copy
      for (let i = tokens.length - 1; i >= 0; --i) {
        let tok = tokens[i];
        if (tok.text === "#") {
          if (i === 0) {
            throw new ParseError("Incomplete placeholder at end of macro body", tok);
          }
          tok = tokens[--i]; // next token on stack
          if (tok.text === "#") {
            // ## → #
            tokens.splice(i + 1, 1); // drop first #
          } else if (/^[1-9]$/.test(tok.text)) {
            // replace the placeholder with the indicated argument
            tokens.splice(i, 2, ...args[+tok.text - 1]);
          } else {
            throw new ParseError("Not a valid argument number", tok);
          }
        }
      }
    }
    // Concatenate expansion onto top of stack.
    this.pushTokens(tokens);
    return tokens.length;
  }

  /**
   * Expand the next token only once (if possible), and return the resulting
   * top token on the stack (without removing anything from the stack).
   * Similar in behavior to TeX's `\expandafter\futurelet`.
   * Equivalent to expandOnce() followed by future().
   */
  expandAfterFuture() {
    this.expandOnce();
    return this.future();
  }

  /**
   * Recursively expand first token, then return first non-expandable token.
   */
  expandNextToken() {
    for (;;) {
      if (this.expandOnce() === false) { // fully expanded
        const token = this.stack.pop();
        // The token after \noexpand is interpreted as if its meaning were ‘\relax’
        if (token.treatAsRelax) {
          token.text = "\\relax";
        }
        return token
      }
    }

    // This pathway is impossible.
    throw new Error(); // eslint-disable-line no-unreachable
  }

  /**
   * Fully expand the given macro name and return the resulting list of
   * tokens, or return `undefined` if no such macro is defined.
   */
  expandMacro(name) {
    return this.macros.has(name) ? this.expandTokens([new Token(name)]) : undefined;
  }

  /**
   * Fully expand the given token stream and return the resulting list of
   * tokens.  Note that the input tokens are in reverse order, but the
   * output tokens are in forward order.
   */
  expandTokens(tokens) {
    const output = [];
    const oldStackLength = this.stack.length;
    this.pushTokens(tokens);
    while (this.stack.length > oldStackLength) {
      // Expand only expandable tokens
      if (this.expandOnce(true) === false) {  // fully expanded
        const token = this.stack.pop();
        if (token.treatAsRelax) {
          // the expansion of \noexpand is the token itself
          token.noexpand = false;
          token.treatAsRelax = false;
        }
        output.push(token);
      }
    }
    return output;
  }

  /**
   * Fully expand the given macro name and return the result as a string,
   * or return `undefined` if no such macro is defined.
   */
  expandMacroAsText(name) {
    const tokens = this.expandMacro(name);
    if (tokens) {
      return tokens.map((token) => token.text).join("");
    } else {
      return tokens;
    }
  }

  /**
   * Returns the expanded macro as a reversed array of tokens and a macro
   * argument count.  Or returns `null` if no such macro.
   */
  _getExpansion(name) {
    const definition = this.macros.get(name);
    if (definition == null) {
      // mainly checking for undefined here
      return definition;
    }
    // If a single character has an associated catcode other than 13
    // (active character), then don't expand it.
    if (name.length === 1) {
      const catcode = this.lexer.catcodes[name];
      if (catcode != null && catcode !== 13) {
        return
      }
    }
    const expansion = typeof definition === "function" ? definition(this) : definition;
    if (typeof expansion === "string") {
      let numArgs = 0;
      if (expansion.indexOf("#") !== -1) {
        const stripped = expansion.replace(/##/g, "");
        while (stripped.indexOf("#" + (numArgs + 1)) !== -1) {
          ++numArgs;
        }
      }
      const bodyLexer = new Lexer(expansion, this.settings);
      const tokens = [];
      let tok = bodyLexer.lex();
      while (tok.text !== "EOF") {
        tokens.push(tok);
        tok = bodyLexer.lex();
      }
      tokens.reverse(); // to fit in with stack using push and pop
      const expanded = { tokens, numArgs };
      return expanded;
    }

    return expansion;
  }

  /**
   * Determine whether a command is currently "defined" (has some
   * functionality), meaning that it's a macro (in the current group),
   * a function, a symbol, or one of the special commands listed in
   * `implicitCommands`.
   */
  isDefined(name) {
    return (
      this.macros.has(name) ||
      Object.prototype.hasOwnProperty.call(functions, name ) ||
      Object.prototype.hasOwnProperty.call(symbols.math, name ) ||
      Object.prototype.hasOwnProperty.call(symbols.text, name ) ||
      Object.prototype.hasOwnProperty.call(implicitCommands, name )
    );
  }

  /**
   * Determine whether a command is expandable.
   */
  isExpandable(name) {
    const macro = this.macros.get(name);
    return macro != null
      ? typeof macro === "string" || typeof macro === "function" || !macro.unexpandable
      : Object.prototype.hasOwnProperty.call(functions, name ) && !functions[name].primitive;
  }
}

// Helpers for Parser.js handling of Unicode (sub|super)script characters.

const unicodeSubRegEx = /^[₊₋₌₍₎₀₁₂₃₄₅₆₇₈₉ₐₑₕᵢⱼₖₗₘₙₒₚᵣₛₜᵤᵥₓᵦᵧᵨᵩᵪ]/;

const uSubsAndSups = Object.freeze({
  '₊': '+',
  '₋': '-',
  '₌': '=',
  '₍': '(',
  '₎': ')',
  '₀': '0',
  '₁': '1',
  '₂': '2',
  '₃': '3',
  '₄': '4',
  '₅': '5',
  '₆': '6',
  '₇': '7',
  '₈': '8',
  '₉': '9',
  '\u2090': 'a',
  '\u2091': 'e',
  '\u2095': 'h',
  '\u1D62': 'i',
  '\u2C7C': 'j',
  '\u2096': 'k',
  '\u2097': 'l',
  '\u2098': 'm',
  '\u2099': 'n',
  '\u2092': 'o',
  '\u209A': 'p',
  '\u1D63': 'r',
  '\u209B': 's',
  '\u209C': 't',
  '\u1D64': 'u',
  '\u1D65': 'v',
  '\u2093': 'x',
  '\u1D66': 'β',
  '\u1D67': 'γ',
  '\u1D68': 'ρ',
  '\u1D69': '\u03d5',
  '\u1D6A': 'χ',
  '⁺': '+',
  '⁻': '-',
  '⁼': '=',
  '⁽': '(',
  '⁾': ')',
  '⁰': '0',
  '¹': '1',
  '²': '2',
  '³': '3',
  '⁴': '4',
  '⁵': '5',
  '⁶': '6',
  '⁷': '7',
  '⁸': '8',
  '⁹': '9',
  '\u1D2C': 'A',
  '\u1D2E': 'B',
  '\u1D30': 'D',
  '\u1D31': 'E',
  '\u1D33': 'G',
  '\u1D34': 'H',
  '\u1D35': 'I',
  '\u1D36': 'J',
  '\u1D37': 'K',
  '\u1D38': 'L',
  '\u1D39': 'M',
  '\u1D3A': 'N',
  '\u1D3C': 'O',
  '\u1D3E': 'P',
  '\u1D3F': 'R',
  '\u1D40': 'T',
  '\u1D41': 'U',
  '\u2C7D': 'V',
  '\u1D42': 'W',
  '\u1D43': 'a',
  '\u1D47': 'b',
  '\u1D9C': 'c',
  '\u1D48': 'd',
  '\u1D49': 'e',
  '\u1DA0': 'f',
  '\u1D4D': 'g',
  '\u02B0': 'h',
  '\u2071': 'i',
  '\u02B2': 'j',
  '\u1D4F': 'k',
  '\u02E1': 'l',
  '\u1D50': 'm',
  '\u207F': 'n',
  '\u1D52': 'o',
  '\u1D56': 'p',
  '\u02B3': 'r',
  '\u02E2': 's',
  '\u1D57': 't',
  '\u1D58': 'u',
  '\u1D5B': 'v',
  '\u02B7': 'w',
  '\u02E3': 'x',
  '\u02B8': 'y',
  '\u1DBB': 'z',
  '\u1D5D': 'β',
  '\u1D5E': 'γ',
  '\u1D5F': 'δ',
  '\u1D60': '\u03d5',
  '\u1D61': 'χ',
  '\u1DBF': 'θ'
});

// Used for Unicode input of calligraphic and script letters
const asciiFromScript = Object.freeze({
  "\ud835\udc9c": "A",
  "\u212c": "B",
  "\ud835\udc9e": "C",
  "\ud835\udc9f": "D",
  "\u2130": "E",
  "\u2131": "F",
  "\ud835\udca2": "G",
  "\u210B": "H",
  "\u2110": "I",
  "\ud835\udca5": "J",
  "\ud835\udca6": "K",
  "\u2112": "L",
  "\u2133": "M",
  "\ud835\udca9": "N",
  "\ud835\udcaa": "O",
  "\ud835\udcab": "P",
  "\ud835\udcac": "Q",
  "\u211B": "R",
  "\ud835\udcae": "S",
  "\ud835\udcaf": "T",
  "\ud835\udcb0": "U",
  "\ud835\udcb1": "V",
  "\ud835\udcb2": "W",
  "\ud835\udcb3": "X",
  "\ud835\udcb4": "Y",
  "\ud835\udcb5": "Z"
});

// Mapping of Unicode accent characters to their LaTeX equivalent in text and
// math mode (when they exist).
var unicodeAccents = {
  "\u0301": { text: "\\'", math: "\\acute" },
  "\u0300": { text: "\\`", math: "\\grave" },
  "\u0308": { text: '\\"', math: "\\ddot" },
  "\u0303": { text: "\\~", math: "\\tilde" },
  "\u0304": { text: "\\=", math: "\\bar" },
  "\u0306": { text: "\\u", math: "\\breve" },
  "\u030c": { text: "\\v", math: "\\check" },
  "\u0302": { text: "\\^", math: "\\hat" },
  "\u0307": { text: "\\.", math: "\\dot" },
  "\u030a": { text: "\\r", math: "\\mathring" },
  "\u030b": { text: "\\H" },
  '\u0327': { text: '\\c' }
};

var unicodeSymbols = {
  "á": "á",
  "à": "à",
  "ä": "ä",
  "ǟ": "ǟ",
  "ã": "ã",
  "ā": "ā",
  "ă": "ă",
  "ắ": "ắ",
  "ằ": "ằ",
  "ẵ": "ẵ",
  "ǎ": "ǎ",
  "â": "â",
  "ấ": "ấ",
  "ầ": "ầ",
  "ẫ": "ẫ",
  "ȧ": "ȧ",
  "ǡ": "ǡ",
  "å": "å",
  "ǻ": "ǻ",
  "ḃ": "ḃ",
  "ć": "ć",
  "č": "č",
  "ĉ": "ĉ",
  "ċ": "ċ",
  "ď": "ď",
  "ḋ": "ḋ",
  "é": "é",
  "è": "è",
  "ë": "ë",
  "ẽ": "ẽ",
  "ē": "ē",
  "ḗ": "ḗ",
  "ḕ": "ḕ",
  "ĕ": "ĕ",
  "ě": "ě",
  "ê": "ê",
  "ế": "ế",
  "ề": "ề",
  "ễ": "ễ",
  "ė": "ė",
  "ḟ": "ḟ",
  "ǵ": "ǵ",
  "ḡ": "ḡ",
  "ğ": "ğ",
  "ǧ": "ǧ",
  "ĝ": "ĝ",
  "ġ": "ġ",
  "ḧ": "ḧ",
  "ȟ": "ȟ",
  "ĥ": "ĥ",
  "ḣ": "ḣ",
  "í": "í",
  "ì": "ì",
  "ï": "ï",
  "ḯ": "ḯ",
  "ĩ": "ĩ",
  "ī": "ī",
  "ĭ": "ĭ",
  "ǐ": "ǐ",
  "î": "î",
  "ǰ": "ǰ",
  "ĵ": "ĵ",
  "ḱ": "ḱ",
  "ǩ": "ǩ",
  "ĺ": "ĺ",
  "ľ": "ľ",
  "ḿ": "ḿ",
  "ṁ": "ṁ",
  "ń": "ń",
  "ǹ": "ǹ",
  "ñ": "ñ",
  "ň": "ň",
  "ṅ": "ṅ",
  "ó": "ó",
  "ò": "ò",
  "ö": "ö",
  "ȫ": "ȫ",
  "õ": "õ",
  "ṍ": "ṍ",
  "ṏ": "ṏ",
  "ȭ": "ȭ",
  "ō": "ō",
  "ṓ": "ṓ",
  "ṑ": "ṑ",
  "ŏ": "ŏ",
  "ǒ": "ǒ",
  "ô": "ô",
  "ố": "ố",
  "ồ": "ồ",
  "ỗ": "ỗ",
  "ȯ": "ȯ",
  "ȱ": "ȱ",
  "ő": "ő",
  "ṕ": "ṕ",
  "ṗ": "ṗ",
  "ŕ": "ŕ",
  "ř": "ř",
  "ṙ": "ṙ",
  "ś": "ś",
  "ṥ": "ṥ",
  "š": "š",
  "ṧ": "ṧ",
  "ŝ": "ŝ",
  "ṡ": "ṡ",
  "ẗ": "ẗ",
  "ť": "ť",
  "ṫ": "ṫ",
  "ú": "ú",
  "ù": "ù",
  "ü": "ü",
  "ǘ": "ǘ",
  "ǜ": "ǜ",
  "ǖ": "ǖ",
  "ǚ": "ǚ",
  "ũ": "ũ",
  "ṹ": "ṹ",
  "ū": "ū",
  "ṻ": "ṻ",
  "ŭ": "ŭ",
  "ǔ": "ǔ",
  "û": "û",
  "ů": "ů",
  "ű": "ű",
  "ṽ": "ṽ",
  "ẃ": "ẃ",
  "ẁ": "ẁ",
  "ẅ": "ẅ",
  "ŵ": "ŵ",
  "ẇ": "ẇ",
  "ẘ": "ẘ",
  "ẍ": "ẍ",
  "ẋ": "ẋ",
  "ý": "ý",
  "ỳ": "ỳ",
  "ÿ": "ÿ",
  "ỹ": "ỹ",
  "ȳ": "ȳ",
  "ŷ": "ŷ",
  "ẏ": "ẏ",
  "ẙ": "ẙ",
  "ź": "ź",
  "ž": "ž",
  "ẑ": "ẑ",
  "ż": "ż",
  "Á": "Á",
  "À": "À",
  "Ä": "Ä",
  "Ǟ": "Ǟ",
  "Ã": "Ã",
  "Ā": "Ā",
  "Ă": "Ă",
  "Ắ": "Ắ",
  "Ằ": "Ằ",
  "Ẵ": "Ẵ",
  "Ǎ": "Ǎ",
  "Â": "Â",
  "Ấ": "Ấ",
  "Ầ": "Ầ",
  "Ẫ": "Ẫ",
  "Ȧ": "Ȧ",
  "Ǡ": "Ǡ",
  "Å": "Å",
  "Ǻ": "Ǻ",
  "Ḃ": "Ḃ",
  "Ć": "Ć",
  "Č": "Č",
  "Ĉ": "Ĉ",
  "Ċ": "Ċ",
  "Ď": "Ď",
  "Ḋ": "Ḋ",
  "É": "É",
  "È": "È",
  "Ë": "Ë",
  "Ẽ": "Ẽ",
  "Ē": "Ē",
  "Ḗ": "Ḗ",
  "Ḕ": "Ḕ",
  "Ĕ": "Ĕ",
  "Ě": "Ě",
  "Ê": "Ê",
  "Ế": "Ế",
  "Ề": "Ề",
  "Ễ": "Ễ",
  "Ė": "Ė",
  "Ḟ": "Ḟ",
  "Ǵ": "Ǵ",
  "Ḡ": "Ḡ",
  "Ğ": "Ğ",
  "Ǧ": "Ǧ",
  "Ĝ": "Ĝ",
  "Ġ": "Ġ",
  "Ḧ": "Ḧ",
  "Ȟ": "Ȟ",
  "Ĥ": "Ĥ",
  "Ḣ": "Ḣ",
  "Í": "Í",
  "Ì": "Ì",
  "Ï": "Ï",
  "Ḯ": "Ḯ",
  "Ĩ": "Ĩ",
  "Ī": "Ī",
  "Ĭ": "Ĭ",
  "Ǐ": "Ǐ",
  "Î": "Î",
  "İ": "İ",
  "Ĵ": "Ĵ",
  "Ḱ": "Ḱ",
  "Ǩ": "Ǩ",
  "Ĺ": "Ĺ",
  "Ľ": "Ľ",
  "Ḿ": "Ḿ",
  "Ṁ": "Ṁ",
  "Ń": "Ń",
  "Ǹ": "Ǹ",
  "Ñ": "Ñ",
  "Ň": "Ň",
  "Ṅ": "Ṅ",
  "Ó": "Ó",
  "Ò": "Ò",
  "Ö": "Ö",
  "Ȫ": "Ȫ",
  "Õ": "Õ",
  "Ṍ": "Ṍ",
  "Ṏ": "Ṏ",
  "Ȭ": "Ȭ",
  "Ō": "Ō",
  "Ṓ": "Ṓ",
  "Ṑ": "Ṑ",
  "Ŏ": "Ŏ",
  "Ǒ": "Ǒ",
  "Ô": "Ô",
  "Ố": "Ố",
  "Ồ": "Ồ",
  "Ỗ": "Ỗ",
  "Ȯ": "Ȯ",
  "Ȱ": "Ȱ",
  "Ő": "Ő",
  "Ṕ": "Ṕ",
  "Ṗ": "Ṗ",
  "Ŕ": "Ŕ",
  "Ř": "Ř",
  "Ṙ": "Ṙ",
  "Ś": "Ś",
  "Ṥ": "Ṥ",
  "Š": "Š",
  "Ṧ": "Ṧ",
  "Ŝ": "Ŝ",
  "Ṡ": "Ṡ",
  "Ť": "Ť",
  "Ṫ": "Ṫ",
  "Ú": "Ú",
  "Ù": "Ù",
  "Ü": "Ü",
  "Ǘ": "Ǘ",
  "Ǜ": "Ǜ",
  "Ǖ": "Ǖ",
  "Ǚ": "Ǚ",
  "Ũ": "Ũ",
  "Ṹ": "Ṹ",
  "Ū": "Ū",
  "Ṻ": "Ṻ",
  "Ŭ": "Ŭ",
  "Ǔ": "Ǔ",
  "Û": "Û",
  "Ů": "Ů",
  "Ű": "Ű",
  "Ṽ": "Ṽ",
  "Ẃ": "Ẃ",
  "Ẁ": "Ẁ",
  "Ẅ": "Ẅ",
  "Ŵ": "Ŵ",
  "Ẇ": "Ẇ",
  "Ẍ": "Ẍ",
  "Ẋ": "Ẋ",
  "Ý": "Ý",
  "Ỳ": "Ỳ",
  "Ÿ": "Ÿ",
  "Ỹ": "Ỹ",
  "Ȳ": "Ȳ",
  "Ŷ": "Ŷ",
  "Ẏ": "Ẏ",
  "Ź": "Ź",
  "Ž": "Ž",
  "Ẑ": "Ẑ",
  "Ż": "Ż",
  "ά": "ά",
  "ὰ": "ὰ",
  "ᾱ": "ᾱ",
  "ᾰ": "ᾰ",
  "έ": "έ",
  "ὲ": "ὲ",
  "ή": "ή",
  "ὴ": "ὴ",
  "ί": "ί",
  "ὶ": "ὶ",
  "ϊ": "ϊ",
  "ΐ": "ΐ",
  "ῒ": "ῒ",
  "ῑ": "ῑ",
  "ῐ": "ῐ",
  "ό": "ό",
  "ὸ": "ὸ",
  "ύ": "ύ",
  "ὺ": "ὺ",
  "ϋ": "ϋ",
  "ΰ": "ΰ",
  "ῢ": "ῢ",
  "ῡ": "ῡ",
  "ῠ": "ῠ",
  "ώ": "ώ",
  "ὼ": "ὼ",
  "Ύ": "Ύ",
  "Ὺ": "Ὺ",
  "Ϋ": "Ϋ",
  "Ῡ": "Ῡ",
  "Ῠ": "Ῠ",
  "Ώ": "Ώ",
  "Ὼ": "Ὼ"
};

/* eslint no-constant-condition:0 */

const binLeftCancellers = ["bin", "op", "open", "punct", "rel"];
const sizeRegEx = /([-+]?) *(\d+(?:\.\d*)?|\.\d+) *([a-z]{2})/;

/**
 * This file contains the parser used to parse out a TeX expression from the
 * input. Since TeX isn't context-free, standard parsers don't work particularly
 * well.
 *
 * The strategy of this parser is as such:
 *
 * The main functions (the `.parse...` ones) take a position in the current
 * parse string to parse tokens from. The lexer (found in Lexer.js, stored at
 * this.gullet.lexer) also supports pulling out tokens at arbitrary places. When
 * individual tokens are needed at a position, the lexer is called to pull out a
 * token, which is then used.
 *
 * The parser has a property called "mode" indicating the mode that
 * the parser is currently in. Currently it has to be one of "math" or
 * "text", which denotes whether the current environment is a math-y
 * one or a text-y one (e.g. inside \text). Currently, this serves to
 * limit the functions which can be used in text mode.
 *
 * The main functions then return an object which contains the useful data that
 * was parsed at its given point, and a new position at the end of the parsed
 * data. The main functions can call each other and continue the parsing by
 * using the returned position as a new starting point.
 *
 * There are also extra `.handle...` functions, which pull out some reused
 * functionality into self-contained functions.
 *
 * The functions return ParseNodes.
 */

class Parser {
  constructor(input, settings, isPreamble = false) {
    // Start in math mode
    this.mode = "math";
    // Create a new macro expander (gullet) and (indirectly via that) also a
    // new lexer (mouth) for this parser (stomach, in the language of TeX)
    this.gullet = new MacroExpander(input, settings, this.mode);
    // Store the settings for use in parsing
    this.settings = settings;
    // Are we defining a preamble?
    this.isPreamble = isPreamble;
    // Count leftright depth (for \middle errors)
    this.leftrightDepth = 0;
    this.prevAtomType = "";
  }

  /**
   * Checks a result to make sure it has the right type, and throws an
   * appropriate error otherwise.
   */
  expect(text, consume = true) {
    if (this.fetch().text !== text) {
      throw new ParseError(`Expected '${text}', got '${this.fetch().text}'`, this.fetch());
    }
    if (consume) {
      this.consume();
    }
  }

  /**
   * Discards the current lookahead token, considering it consumed.
   */
  consume() {
    this.nextToken = null;
  }

  /**
   * Return the current lookahead token, or if there isn't one (at the
   * beginning, or if the previous lookahead token was consume()d),
   * fetch the next token as the new lookahead token and return it.
   */
  fetch() {
    if (this.nextToken == null) {
      this.nextToken = this.gullet.expandNextToken();
    }
    return this.nextToken;
  }

  /**
   * Switches between "text" and "math" modes.
   */
  switchMode(newMode) {
    this.mode = newMode;
    this.gullet.switchMode(newMode);
  }

  /**
   * Main parsing function, which parses an entire input.
   */
  parse() {
    // Create a group namespace for every $...$, $$...$$, \[...\].)
    // A \def is then valid only within that pair of delimiters.
    this.gullet.beginGroup();

    if (this.settings.colorIsTextColor) {
      // Use old \color behavior (same as LaTeX's \textcolor) if requested.
      // We do this within the group for the math expression, so it doesn't
      // pollute settings.macros.
      this.gullet.macros.set("\\color", "\\textcolor");
    }

    // Try to parse the input
    const parse = this.parseExpression(false);

    // If we succeeded, make sure there's an EOF at the end
    this.expect("EOF");

    if (this.isPreamble) {
      const macros = Object.create(null);
      Object.entries(this.gullet.macros.current).forEach(([key, value]) => {
        macros[key] = value;
      });
      this.gullet.endGroup();
      return macros
    }

    // The only local macro that we want to save is from \tag.
    const tag = this.gullet.macros.get("\\df@tag");

    // End the group namespace for the expression
    this.gullet.endGroup();

    if (tag) { this.gullet.macros.current["\\df@tag"] = tag; }

    return parse;
  }

  static get endOfExpression() {
    return ["}", "\\endgroup", "\\end", "\\right", "\\endtoggle", "&"];
  }

  /**
   * Fully parse a separate sequence of tokens as a separate job.
   * Tokens should be specified in reverse order, as in a MacroDefinition.
   */
  subparse(tokens) {
    // Save the next token from the current job.
    const oldToken = this.nextToken;
    this.consume();

    // Run the new job, terminating it with an excess '}'
    this.gullet.pushToken(new Token("}"));
    this.gullet.pushTokens(tokens);
    const parse = this.parseExpression(false);
    this.expect("}");

    // Restore the next token from the current job.
    this.nextToken = oldToken;

    return parse;
  }

/**
   * Parses an "expression", which is a list of atoms.
   *
   * `breakOnInfix`: Should the parsing stop when we hit infix nodes? This
   *                 happens when functions have higher precedence han infix
   *                 nodes in implicit parses.
   *
   * `breakOnTokenText`: The text of the token that the expression should end
   *                     with, or `null` if something else should end the
   *                     expression.
   *
   * `breakOnMiddle`: \color, \over, and old styling functions work on an implicit group.
   *                  These groups end just before the usual tokens, but they also
   *                  end just before `\middle`.
   */
  parseExpression(breakOnInfix, breakOnTokenText, breakOnMiddle) {
    const body = [];
    this.prevAtomType = "";
    // Keep adding atoms to the body until we can't parse any more atoms (either
    // we reached the end, a }, or a \right)
    while (true) {
      // Ignore spaces in math mode
      if (this.mode === "math") {
        this.consumeSpaces();
      }
      const lex = this.fetch();
      if (Parser.endOfExpression.indexOf(lex.text) !== -1) {
        break;
      }
      if (breakOnTokenText && lex.text === breakOnTokenText) {
        break;
      }
      if (breakOnMiddle && lex.text === "\\middle") {
        break
      }
      if (breakOnInfix && functions[lex.text] && functions[lex.text].infix) {
        break;
      }
      const atom = this.parseAtom(breakOnTokenText);
      if (!atom) {
        break;
      } else if (atom.type === "internal") {
        continue;
      }
      body.push(atom);
      // Keep a record of the atom type, so that op.js can set correct spacing.
      this.prevAtomType = atom.type === "atom" ? atom.family : atom.type;
    }
    if (this.mode === "text") {
      this.formLigatures(body);
    }
    return this.handleInfixNodes(body);
  }

  /**
   * Rewrites infix operators such as \over with corresponding commands such
   * as \frac.
   *
   * There can only be one infix operator per group.  If there's more than one
   * then the expression is ambiguous.  This can be resolved by adding {}.
   */
  handleInfixNodes(body) {
    let overIndex = -1;
    let funcName;

    for (let i = 0; i < body.length; i++) {
      if (body[i].type === "infix") {
        if (overIndex !== -1) {
          throw new ParseError("only one infix operator per group", body[i].token);
        }
        overIndex = i;
        funcName = body[i].replaceWith;
      }
    }

    if (overIndex !== -1 && funcName) {
      let numerNode;
      let denomNode;

      const numerBody = body.slice(0, overIndex);
      const denomBody = body.slice(overIndex + 1);

      if (numerBody.length === 1 && numerBody[0].type === "ordgroup") {
        numerNode = numerBody[0];
      } else {
        numerNode = { type: "ordgroup", mode: this.mode, body: numerBody };
      }

      if (denomBody.length === 1 && denomBody[0].type === "ordgroup") {
        denomNode = denomBody[0];
      } else {
        denomNode = { type: "ordgroup", mode: this.mode, body: denomBody };
      }

      let node;
      if (funcName === "\\\\abovefrac") {
        node = this.callFunction(funcName, [numerNode, body[overIndex], denomNode], []);
      } else {
        node = this.callFunction(funcName, [numerNode, denomNode], []);
      }
      return [node];
    } else {
      return body;
    }
  }

  /**
   * Handle a subscript or superscript with nice errors.
   */
  handleSupSubscript(
    name // For error reporting.
  ) {
    const symbolToken = this.fetch();
    const symbol = symbolToken.text;
    this.consume();
    this.consumeSpaces(); // ignore spaces before sup/subscript argument
    const group = this.parseGroup(name);

    if (!group) {
      throw new ParseError("Expected group after '" + symbol + "'", symbolToken);
    }

    return group;
  }

  /**
   * Converts the textual input of an unsupported command into a text node
   * contained within a color node whose color is determined by errorColor
   */
  formatUnsupportedCmd(text) {
    const textordArray = [];

    for (let i = 0; i < text.length; i++) {
      textordArray.push({ type: "textord", mode: "text", text: text[i] });
    }

    const textNode = {
      type: "text",
      mode: this.mode,
      body: textordArray
    };

    const colorNode = {
      type: "color",
      mode: this.mode,
      color: this.settings.errorColor,
      body: [textNode]
    };

    return colorNode;
  }

  /**
   * Parses a group with optional super/subscripts.
   */
  parseAtom(breakOnTokenText) {
    // The body of an atom is an implicit group, so that things like
    // \left(x\right)^2 work correctly.
    const base = this.parseGroup("atom", breakOnTokenText);

    // In text mode, we don't have superscripts or subscripts
    if (this.mode === "text") {
      return base;
    }

    // Note that base may be empty (i.e. null) at this point.

    let superscript;
    let subscript;
    while (true) {
      // Guaranteed in math mode, so eat any spaces first.
      this.consumeSpaces();

      // Lex the first token
      const lex = this.fetch();

      if (lex.text === "\\limits" || lex.text === "\\nolimits") {
        // We got a limit control
        if (base && base.type === "op") {
          const limits = lex.text === "\\limits";
          base.limits = limits;
          base.alwaysHandleSupSub = true;
        } else if (base && base.type === "operatorname") {
          if (base.alwaysHandleSupSub) {
            base.limits = lex.text === "\\limits";
          }
        } else {
          throw new ParseError("Limit controls must follow a math operator", lex);
        }
        this.consume();
      } else if (lex.text === "^") {
        // We got a superscript start
        if (superscript) {
          throw new ParseError("Double superscript", lex);
        }
        superscript = this.handleSupSubscript("superscript");
      } else if (lex.text === "_") {
        // We got a subscript start
        if (subscript) {
          throw new ParseError("Double subscript", lex);
        }
        subscript = this.handleSupSubscript("subscript");
      } else if (lex.text === "'") {
        // We got a prime
        if (superscript) {
          throw new ParseError("Double superscript", lex);
        }
        const prime = { type: "textord", mode: this.mode, text: "\\prime" };

        // Many primes can be grouped together, so we handle this here
        const primes = [prime];
        this.consume();
        // Keep lexing tokens until we get something that's not a prime
        while (this.fetch().text === "'") {
          // For each one, add another prime to the list
          primes.push(prime);
          this.consume();
        }
        // If there's a superscript following the primes, combine that
        // superscript in with the primes.
        if (this.fetch().text === "^") {
          primes.push(this.handleSupSubscript("superscript"));
        }
        // Put everything into an ordgroup as the superscript
        superscript = { type: "ordgroup", mode: this.mode, body: primes };
      } else if (uSubsAndSups[lex.text]) {
        // A Unicode subscript or superscript character.
        // We treat these similarly to the unicode-math package.
        // So we render a string of Unicode (sub|super)scripts the
        // same as a (sub|super)script of regular characters.
        const isSub = unicodeSubRegEx.test(lex.text);
        const subsupTokens = [];
        subsupTokens.push(new Token(uSubsAndSups[lex.text]));
        this.consume();
        // Continue fetching tokens to fill out the group.
        while (true) {
          const token = this.fetch().text;
          if (!(uSubsAndSups[token])) { break }
          if (unicodeSubRegEx.test(token) !== isSub) { break }
          subsupTokens.unshift(new Token(uSubsAndSups[token]));
          this.consume();
        }
        // Now create a (sub|super)script.
        const body = this.subparse(subsupTokens);
        if (isSub) {
          subscript = { type: "ordgroup", mode: "math", body };
        } else {
          superscript = { type: "ordgroup", mode: "math", body };
        }
      } else {
        // If it wasn't ^, _, a Unicode (sub|super)script, or ', stop parsing super/subscripts
        break;
      }
    }

    if (superscript || subscript) {
      if (base && base.type === "multiscript" && !base.postscripts) {
        // base is the result of a \prescript function.
        // Write the sub- & superscripts into the multiscript element.
        base.postscripts = { sup: superscript, sub: subscript };
        return base
      } else {
        // We got either a superscript or subscript, create a supsub
        const isFollowedByDelimiter = (!base || base.type !== "op" && base.type !== "operatorname")
          ? undefined
          : isDelimiter(this.nextToken.text);
        return {
          type: "supsub",
          mode: this.mode,
          base: base,
          sup: superscript,
          sub: subscript,
          isFollowedByDelimiter
        }
      }
    } else {
      // Otherwise return the original body
      return base;
    }
  }

  /**
   * Parses an entire function, including its base and all of its arguments.
   */
  parseFunction(
    breakOnTokenText,
    name // For determining its context
  ) {
    const token = this.fetch();
    const func = token.text;
    const funcData = functions[func];
    if (!funcData) {
      return null;
    }
    this.consume(); // consume command token

    if (name && name !== "atom" && !funcData.allowedInArgument) {
      throw new ParseError(
        "Got function '" + func + "' with no arguments" + (name ? " as " + name : ""),
        token
      );
    } else if (this.mode === "text" && !funcData.allowedInText) {
      throw new ParseError("Can't use function '" + func + "' in text mode", token);
    } else if (this.mode === "math" && funcData.allowedInMath === false) {
      throw new ParseError("Can't use function '" + func + "' in math mode", token);
    }

    const prevAtomType = this.prevAtomType;
    const { args, optArgs } = this.parseArguments(func, funcData);
    this.prevAtomType = prevAtomType;
    return this.callFunction(func, args, optArgs, token, breakOnTokenText);
  }

  /**
   * Call a function handler with a suitable context and arguments.
   */
  callFunction(name, args, optArgs, token, breakOnTokenText) {
    const context = {
      funcName: name,
      parser: this,
      token,
      breakOnTokenText
    };
    const func = functions[name];
    if (func && func.handler) {
      return func.handler(context, args, optArgs);
    } else {
      throw new ParseError(`No function handler for ${name}`);
    }
  }

  /**
   * Parses the arguments of a function or environment
   */
  parseArguments(
    func, // Should look like "\name" or "\begin{name}".
    funcData
  ) {
    const totalArgs = funcData.numArgs + funcData.numOptionalArgs;
    if (totalArgs === 0) {
      return { args: [], optArgs: [] };
    }

    const args = [];
    const optArgs = [];

    for (let i = 0; i < totalArgs; i++) {
      let argType = funcData.argTypes && funcData.argTypes[i];
      const isOptional = i < funcData.numOptionalArgs;

      if (
        (funcData.primitive && argType == null) ||
        // \sqrt expands into primitive if optional argument doesn't exist
        (funcData.type === "sqrt" && i === 1 && optArgs[0] == null)
      ) {
        argType = "primitive";
      }

      const arg = this.parseGroupOfType(`argument to '${func}'`, argType, isOptional);
      if (isOptional) {
        optArgs.push(arg);
      } else if (arg != null) {
        args.push(arg);
      } else {
        // should be unreachable
        throw new ParseError("Null argument, please report this as a bug");
      }
    }

    return { args, optArgs };
  }

  /**
   * Parses a group when the mode is changing.
   */
  parseGroupOfType(name, type, optional) {
    switch (type) {
      case "size":
        return this.parseSizeGroup(optional);
      case "url":
        return this.parseUrlGroup(optional);
      case "math":
      case "text":
        return this.parseArgumentGroup(optional, type);
      case "hbox": {
        // hbox argument type wraps the argument in the equivalent of
        // \hbox, which is like \text but switching to \textstyle size.
        const group = this.parseArgumentGroup(optional, "text");
        return group != null
          ? {
            type: "styling",
            mode: group.mode,
            body: [group],
            scriptLevel: "text" // simulate \textstyle
          }
          : null;
      }
      case "raw": {
        const token = this.parseStringGroup("raw", optional);
        return token != null
          ? {
            type: "raw",
            mode: "text",
            string: token.text
          }
          : null;
      }
      case "primitive": {
        if (optional) {
          throw new ParseError("A primitive argument cannot be optional");
        }
        const group = this.parseGroup(name);
        if (group == null) {
          throw new ParseError("Expected group as " + name, this.fetch());
        }
        return group;
      }
      case "original":
      case null:
      case undefined:
        return this.parseArgumentGroup(optional);
      default:
        throw new ParseError("Unknown group type as " + name, this.fetch());
    }
  }

  /**
   * Discard any space tokens, fetching the next non-space token.
   */
  consumeSpaces() {
    while (true) {
      const ch = this.fetch().text;
      // \ufe0e is the Unicode variation selector to supress emoji. Ignore it.
      if (ch === " " || ch === "\u00a0" || ch === "\ufe0e") {
        this.consume();
      } else {
        break
      }
    }
  }

  /**
   * Parses a group, essentially returning the string formed by the
   * brace-enclosed tokens plus some position information.
   */
  parseStringGroup(
    modeName, // Used to describe the mode in error messages.
    optional
  ) {
    const argToken = this.gullet.scanArgument(optional);
    if (argToken == null) {
      return null;
    }
    let str = "";
    let nextToken;
    while ((nextToken = this.fetch()).text !== "EOF") {
      str += nextToken.text;
      this.consume();
    }
    this.consume(); // consume the end of the argument
    argToken.text = str;
    return argToken;
  }

  /**
   * Parses a regex-delimited group: the largest sequence of tokens
   * whose concatenated strings match `regex`. Returns the string
   * formed by the tokens plus some position information.
   */
  parseRegexGroup(
    regex,
    modeName // Used to describe the mode in error messages.
  ) {
    const firstToken = this.fetch();
    let lastToken = firstToken;
    let str = "";
    let nextToken;
    while ((nextToken = this.fetch()).text !== "EOF" && regex.test(str + nextToken.text)) {
      lastToken = nextToken;
      str += lastToken.text;
      this.consume();
    }
    if (str === "") {
      throw new ParseError("Invalid " + modeName + ": '" + firstToken.text + "'", firstToken);
    }
    return firstToken.range(lastToken, str);
  }

  /**
   * Parses a size specification, consisting of magnitude and unit.
   */
  parseSizeGroup(optional) {
    let res;
    let isBlank = false;
    // don't expand before parseStringGroup
    this.gullet.consumeSpaces();
    if (!optional && this.gullet.future().text !== "{") {
      res = this.parseRegexGroup(/^[-+]? *(?:$|\d+|\d+\.\d*|\.\d*) *[a-z]{0,2} *$/, "size");
    } else {
      res = this.parseStringGroup("size", optional);
    }
    if (!res) {
      return null;
    }
    if (!optional && res.text.length === 0) {
      // Because we've tested for what is !optional, this block won't
      // affect \kern, \hspace, etc. It will capture the mandatory arguments
      // to \genfrac and \above.
      res.text = "0pt"; // Enable \above{}
      isBlank = true; // This is here specifically for \genfrac
    }
    const match = sizeRegEx.exec(res.text);
    if (!match) {
      throw new ParseError("Invalid size: '" + res.text + "'", res);
    }
    const data = {
      number: +(match[1] + match[2]), // sign + magnitude, cast to number
      unit: match[3]
    };
    if (!validUnit(data)) {
      throw new ParseError("Invalid unit: '" + data.unit + "'", res);
    }
    return {
      type: "size",
      mode: this.mode,
      value: data,
      isBlank
    };
  }

  /**
   * Parses an URL, checking escaped letters and allowed protocols,
   * and setting the catcode of % as an active character (as in \hyperref).
   */
  parseUrlGroup(optional) {
    this.gullet.lexer.setCatcode("%", 13); // active character
    this.gullet.lexer.setCatcode("~", 12); // other character
    const res = this.parseStringGroup("url", optional);
    this.gullet.lexer.setCatcode("%", 14); // comment character
    this.gullet.lexer.setCatcode("~", 13); // active character
    if (res == null) {
      return null;
    }
    // hyperref package allows backslashes alone in href, but doesn't
    // generate valid links in such cases; we interpret this as
    // "undefined" behaviour, and keep them as-is. Some browser will
    // replace backslashes with forward slashes.
    let url = res.text.replace(/\\([#$%&~_^{}])/g, "$1");
    url = res.text.replace(/{\u2044}/g, "/");
    return {
      type: "url",
      mode: this.mode,
      url
    };
  }

  /**
   * Parses an argument with the mode specified.
   */
  parseArgumentGroup(optional, mode) {
    const argToken = this.gullet.scanArgument(optional);
    if (argToken == null) {
      return null;
    }
    const outerMode = this.mode;
    if (mode) {
      // Switch to specified mode
      this.switchMode(mode);
    }

    this.gullet.beginGroup();
    const expression = this.parseExpression(false, "EOF");
    // TODO: find an alternative way to denote the end
    this.expect("EOF"); // expect the end of the argument
    this.gullet.endGroup();
    const result = {
      type: "ordgroup",
      mode: this.mode,
      loc: argToken.loc,
      body: expression
    };

    if (mode) {
      // Switch mode back
      this.switchMode(outerMode);
    }
    return result;
  }

  /**
   * Parses an ordinary group, which is either a single nucleus (like "x")
   * or an expression in braces (like "{x+y}") or an implicit group, a group
   * that starts at the current position, and ends right before a higher explicit
   * group ends, or at EOF.
   */
  parseGroup(
    name, // For error reporting.
    breakOnTokenText
  ) {
    const firstToken = this.fetch();
    const text = firstToken.text;

    let result;
    // Try to parse an open brace or \begingroup
    if (text === "{" || text === "\\begingroup" || text === "\\toggle") {
      this.consume();
      const groupEnd = text === "{"
        ? "}"
        : text === "\\begingroup"
        ? "\\endgroup"
        : "\\endtoggle";

      this.gullet.beginGroup();
      // If we get a brace, parse an expression
      const expression = this.parseExpression(false, groupEnd);
      const lastToken = this.fetch();
      this.expect(groupEnd); // Check that we got a matching closing brace
      this.gullet.endGroup();
      result = {
        type: (lastToken.text === "\\endtoggle" ? "toggle" : "ordgroup"),
        mode: this.mode,
        loc: SourceLocation.range(firstToken, lastToken),
        body: expression,
        // A group formed by \begingroup...\endgroup is a semi-simple group
        // which doesn't affect spacing in math mode, i.e., is transparent.
        // https://tex.stackexchange.com/questions/1930/
        semisimple: text === "\\begingroup" || undefined
      };
    } else {
      // If there exists a function with this name, parse the function.
      // Otherwise, just return a nucleus
      result = this.parseFunction(breakOnTokenText, name) || this.parseSymbol();
      if (result == null && text[0] === "\\" &&
          !Object.prototype.hasOwnProperty.call(implicitCommands, text )) {
        result = this.formatUnsupportedCmd(text);
        this.consume();
      }
    }
    return result;
  }

  /**
   * Form ligature-like combinations of characters for text mode.
   * This includes inputs like "--", "---", "``" and "''".
   * The result will simply replace multiple textord nodes with a single
   * character in each value by a single textord node having multiple
   * characters in its value.  The representation is still ASCII source.
   * The group will be modified in place.
   */
  formLigatures(group) {
    let n = group.length - 1;
    for (let i = 0; i < n; ++i) {
      const a = group[i];
      const v = a.text;
      if (v === "-" && group[i + 1].text === "-") {
        if (i + 1 < n && group[i + 2].text === "-") {
          group.splice(i, 3, {
            type: "textord",
            mode: "text",
            loc: SourceLocation.range(a, group[i + 2]),
            text: "---"
          });
          n -= 2;
        } else {
          group.splice(i, 2, {
            type: "textord",
            mode: "text",
            loc: SourceLocation.range(a, group[i + 1]),
            text: "--"
          });
          n -= 1;
        }
      }
      if ((v === "'" || v === "`") && group[i + 1].text === v) {
        group.splice(i, 2, {
          type: "textord",
          mode: "text",
          loc: SourceLocation.range(a, group[i + 1]),
          text: v + v
        });
        n -= 1;
      }
    }
  }

  /**
   * Parse a single symbol out of the string. Here, we handle single character
   * symbols and special functions like \verb.
   */
  parseSymbol() {
    const nucleus = this.fetch();
    let text = nucleus.text;

    if (/^\\verb[^a-zA-Z]/.test(text)) {
      this.consume();
      let arg = text.slice(5);
      const star = arg.charAt(0) === "*";
      if (star) {
        arg = arg.slice(1);
      }
      // Lexer's tokenRegex is constructed to always have matching
      // first/last characters.
      if (arg.length < 2 || arg.charAt(0) !== arg.slice(-1)) {
        throw new ParseError(`\\verb assertion failed --
                    please report what input caused this bug`);
      }
      arg = arg.slice(1, -1); // remove first and last char
      return {
        type: "verb",
        mode: "text",
        body: arg,
        star
      };
    }
    // At this point, we should have a symbol, possibly with accents.
    // First expand any accented base symbol according to unicodeSymbols.
    if (Object.prototype.hasOwnProperty.call(unicodeSymbols, text[0]) &&
      this.mode === "math" && !symbols[this.mode][text[0]]) {
      // This behavior is not strict (XeTeX-compatible) in math mode.
      if (this.settings.strict && this.mode === "math") {
        throw new ParseError(`Accented Unicode text character "${text[0]}" used in ` + `math mode`,
          nucleus
        );
      }
      text = unicodeSymbols[text[0]] + text.slice(1);
    }
    // Strip off any combining characters
    const match = this.mode === "math"
      ? combiningDiacriticalMarksEndRegex.exec(text)
      : null;
    if (match) {
      text = text.substring(0, match.index);
      if (text === "i") {
        text = "\u0131"; // dotless i, in math and text mode
      } else if (text === "j") {
        text = "\u0237"; // dotless j, in math and text mode
      }
    }
    // Recognize base symbol
    let symbol;
    if (symbols[this.mode][text]) {
      let group = symbols[this.mode][text].group;
      if (group === "bin" && binLeftCancellers.includes(this.prevAtomType)) {
        // Change from a binary operator to a unary (prefix) operator
        group = "open";
      }
      const loc = SourceLocation.range(nucleus);
      let s;
      if (Object.prototype.hasOwnProperty.call(ATOMS, group )) {
        const family = group;
        s = {
          type: "atom",
          mode: this.mode,
          family,
          loc,
          text
        };
      } else {
        if (asciiFromScript[text]) {
          // Unicode 14 disambiguates chancery from roundhand.
          // See https://www.unicode.org/charts/PDF/U1D400.pdf
          this.consume();
          const nextCode = this.fetch().text.charCodeAt(0);
          // mathcal is Temml default. Use mathscript if called for.
          const font = nextCode === 0xfe01 ? "mathscr" : "mathcal";
          if (nextCode === 0xfe00 || nextCode === 0xfe01) { this.consume(); }
          return {
            type: "font",
            mode: "math",
            font,
            body: { type: "mathord", mode: "math", loc, text: asciiFromScript[text] }
          }
        }
        // Default ord character. No disambiguation necessary.
        s = {
          type: group,
          mode: this.mode,
          loc,
          text
        };
      }
      symbol = s;
    } else if (text.charCodeAt(0) >= 0x80 || combiningDiacriticalMarksEndRegex.exec(text)) {
      // no symbol for e.g. ^
      if (this.settings.strict && this.mode === "math") {
        throw new ParseError(`Unicode text character "${text[0]}" used in math mode`, nucleus)
      }
      // All nonmathematical Unicode characters are rendered as if they
      // are in text mode (wrapped in \text) because that's what it
      // takes to render them in LaTeX.
      symbol = {
        type: "textord",
        mode: "text",
        loc: SourceLocation.range(nucleus),
        text
      };
    } else {
      return null; // EOF, ^, _, {, }, etc.
    }
    this.consume();
    // Transform combining characters into accents
    if (match) {
      for (let i = 0; i < match[0].length; i++) {
        const accent = match[0][i];
        if (!unicodeAccents[accent]) {
          throw new ParseError(`Unknown accent ' ${accent}'`, nucleus);
        }
        const command = unicodeAccents[accent][this.mode] ||
                        unicodeAccents[accent].text;
        if (!command) {
          throw new ParseError(`Accent ${accent} unsupported in ${this.mode} mode`, nucleus);
        }
        symbol = {
          type: "accent",
          mode: this.mode,
          loc: SourceLocation.range(nucleus),
          label: command,
          isStretchy: false,
          base: symbol
        };
      }
    }
    return symbol;
  }
}

/**
 * Parses an expression using a Parser, then returns the parsed result.
 */
const parseTree = function(toParse, settings) {
  if (!(typeof toParse === "string" || toParse instanceof String)) {
    throw new TypeError("Temml can only parse string typed expression")
  }
  const parser = new Parser(toParse, settings);
  // Blank out any \df@tag to avoid spurious "Duplicate \tag" errors
  delete parser.gullet.macros.current["\\df@tag"];

  let tree = parser.parse();

  // LaTeX ignores a \tag placed outside an AMS environment.
  if (!(tree.length > 0 &&  tree[0].type && tree[0].type === "array" && tree[0].addEqnNum)) {
    // If the input used \tag, it will set the \df@tag macro to the tag.
    // In this case, we separately parse the tag and wrap the tree.
    if (parser.gullet.macros.get("\\df@tag")) {
      if (!settings.displayMode) {
        throw new ParseError("\\tag works only in display mode")
      }
      parser.gullet.feed("\\df@tag");
      tree = [
        {
          type: "tag",
          mode: "text",
          body: tree,
          tag: parser.parse()
        }
      ];
    }
  }

  return tree
};

/**
 * This file contains information about the style that the mathmlBuilder carries
 * around with it. Data is held in an `Style` object, and when
 * recursing, a new `Style` object can be created with the `.with*` functions.
 */

const subOrSupLevel = [2, 2, 3, 3];

/**
 * This is the main Style class. It contains the current style.level, color, and font.
 *
 * Style objects should not be modified. To create a new Style with
 * different properties, call a `.with*` method.
 */
class Style {
  constructor(data) {
    // Style.level can be 0 | 1 | 2 | 3, which correspond to
    //       displaystyle, textstyle, scriptstyle, and scriptscriptstyle.
    // style.level usually does not directly set MathML's script level. MathML does that itself.
    // However, Chromium does not stop shrinking after scriptscriptstyle, so we do explicitly
    // set a scriptlevel attribute in those conditions.
    // We also use style.level to track math style so that we can get the correct
    // scriptlevel when needed in supsub.js, mathchoice.js, or for dimensions in em.
    this.level = data.level;
    this.color = data.color;  // string | void
    // A font family applies to a group of fonts (i.e. SansSerif), while a font
    // represents a specific font (i.e. SansSerif Bold).
    // See: https://tex.stackexchange.com/questions/22350/difference-between-textrm-and-mathrm
    this.font = data.font || "";                // string
    this.fontFamily = data.fontFamily || "";    // string
    this.fontSize = data.fontSize || 1.0;       // number
    this.fontWeight = data.fontWeight || "";
    this.fontShape = data.fontShape || "";
    this.maxSize = data.maxSize;                // [number, number]
  }

  /**
   * Returns a new style object with the same properties as "this".  Properties
   * from "extension" will be copied to the new style object.
   */
  extend(extension) {
    const data = {
      level: this.level,
      color: this.color,
      font: this.font,
      fontFamily: this.fontFamily,
      fontSize: this.fontSize,
      fontWeight: this.fontWeight,
      fontShape: this.fontShape,
      maxSize: this.maxSize
    };

    for (const key in extension) {
      if (Object.prototype.hasOwnProperty.call(extension, key)) {
        data[key] = extension[key];
      }
    }

    return new Style(data);
  }

  withLevel(n) {
    return this.extend({
      level: n
    });
  }

  incrementLevel() {
    return this.extend({
      level: Math.min(this.level + 1, 3)
    });
  }

  inSubOrSup() {
    return this.extend({
      level: subOrSupLevel[this.level]
    })
  }

  /**
   * Create a new style object with the given color.
   */
  withColor(color) {
    return this.extend({
      color: color
    });
  }

  /**
   * Creates a new style object with the given math font or old text font.
   * @type {[type]}
   */
  withFont(font) {
    return this.extend({
      font
    });
  }

  /**
   * Create a new style objects with the given fontFamily.
   */
  withTextFontFamily(fontFamily) {
    return this.extend({
      fontFamily,
      font: ""
    });
  }

  /**
   * Creates a new style object with the given font size
   */
  withFontSize(num) {
    return this.extend({
      fontSize: num
    });
  }

  /**
   * Creates a new style object with the given font weight
   */
  withTextFontWeight(fontWeight) {
    return this.extend({
      fontWeight,
      font: ""
    });
  }

  /**
   * Creates a new style object with the given font weight
   */
  withTextFontShape(fontShape) {
    return this.extend({
      fontShape,
      font: ""
    });
  }

  /**
   * Gets the CSS color of the current style object
   */
  getColor() {
    return this.color;
  }
}

/* Temml Post Process
 * Populate the text contents of each \ref & \eqref
 *
 * As with other Temml code, this file is released under terms of the MIT license.
 * https://mit-license.org/
 */

const version = "0.10.34";

function postProcess(block) {
  const labelMap = {};
  let i = 0;

  // Get a collection of the parents of each \tag & auto-numbered equation
  const amsEqns = document.getElementsByClassName('tml-eqn');
  for (let parent of amsEqns) {
    // AMS automatically numbered equation.
    // Assign an id.
    i += 1;
    parent.setAttribute("id", "tml-eqn-" + String(i));
    // No need to write a number into the text content of the element.
    // A CSS counter has done that even if this postProcess() function is not used.

    // Find any \label that refers to an AMS automatic eqn number.
    while (true) {
      if (parent.tagName === "mtable") { break }
      const labels = parent.getElementsByClassName("tml-label");
      if (labels.length > 0) {
        const id = parent.attributes.id.value;
        labelMap[id] = String(i);
        break
      } else {
        parent = parent.parentElement;
      }
    }
  }

  // Find \labels associated with \tag
  const taggedEqns = document.getElementsByClassName('tml-tageqn');
  for (const parent of taggedEqns) {
    const labels = parent.getElementsByClassName("tml-label");
    if (labels.length > 0) {
      const tags = parent.getElementsByClassName("tml-tag");
      if (tags.length > 0) {
        const id = parent.attributes.id.value;
        labelMap[id] = tags[0].textContent;
      }
    }
  }

  // Populate \ref & \eqref text content
  const refs = block.getElementsByClassName("tml-ref");
  [...refs].forEach(ref => {
    const attr = ref.getAttribute("href");
    let str = labelMap[attr.slice(1)];
    if (ref.className.indexOf("tml-eqref") === -1) {
      // \ref. Omit parens.
      str = str.replace(/^\(/, "");
      str = str.replace(/\)$/, "");
    } else {
      // \eqref. Include parens
      if (str.charAt(0) !== "(") { str = "(" + str; }
      if (str.slice(-1) !== ")") { str =  str + ")"; }
    }
    const mtext = document.createElementNS("http://www.w3.org/1998/Math/MathML", "mtext");
    mtext.appendChild(document.createTextNode(str));
    const math =  document.createElementNS("http://www.w3.org/1998/Math/MathML", "math");
    math.appendChild(mtext);
    ref.appendChild(math);
  });
}

/* eslint no-console:0 */
/**
 * This is the main entry point for Temml. Here, we expose functions for
 * rendering expressions either to DOM nodes or to markup strings.
 *
 * We also expose the ParseError class to check if errors thrown from Temml are
 * errors in the expression, or errors in javascript handling.
 */


/**
 * @type {import('./temml').render}
 * Parse and build an expression, and place that expression in the DOM node
 * given.
 */
let render = function(expression, baseNode, options = {}) {
  baseNode.textContent = "";
  const alreadyInMathElement = baseNode.tagName.toLowerCase() === "math";
  if (alreadyInMathElement) { options.wrap = "none"; }
  const math = renderToMathMLTree(expression, options);
  if (alreadyInMathElement) {
    // The <math> element already exists. Populate it.
    baseNode.textContent = "";
    math.children.forEach(e => { baseNode.appendChild(e.toNode()); });
  } else if (math.children.length > 1) {
    baseNode.textContent = "";
    math.children.forEach(e => { baseNode.appendChild(e.toNode()); });
  } else {
    baseNode.appendChild(math.toNode());
  }
};

// Temml's styles don't work properly in quirks mode. Print out an error, and
// disable rendering.
if (typeof document !== "undefined") {
  if (document.compatMode !== "CSS1Compat") {
    typeof console !== "undefined" &&
      console.warn(
        "Warning: Temml doesn't work in quirks mode. Make sure your " +
          "website has a suitable doctype."
      );

    render = function() {
      throw new ParseError("Temml doesn't work in quirks mode.");
    };
  }
}

/**
 * @type {import('./temml').renderToString}
 * Parse and build an expression, and return the markup for that.
 */
const renderToString = function(expression, options) {
  const markup = renderToMathMLTree(expression, options).toMarkup();
  return markup;
};

/**
 * @type {import('./temml').generateParseTree}
 * Parse an expression and return the parse tree.
 */
const generateParseTree = function(expression, options) {
  const settings = new Settings(options);
  return parseTree(expression, settings);
};

/**
 * @type {import('./temml').definePreamble}
 * Take an expression which contains a preamble.
 * Parse it and return the macros.
 */
const definePreamble = function(expression, options) {
  const settings = new Settings(options);
  settings.macros = {};
  if (!(typeof expression === "string" || expression instanceof String)) {
    throw new TypeError("Temml can only parse string typed expression")
  }
  const parser = new Parser(expression, settings, true);
  // Blank out any \df@tag to avoid spurious "Duplicate \tag" errors
  delete parser.gullet.macros.current["\\df@tag"];
  const macros = parser.parse();
  return macros
};

/**
 * If the given error is a Temml ParseError,
 * renders the invalid LaTeX as a span with hover title giving the Temml
 * error message.  Otherwise, simply throws the error.
 */
const renderError = function(error, expression, options) {
  if (options.throwOnError || !(error instanceof ParseError)) {
    throw error;
  }
  const node = new Span(["temml-error"], [new TextNode$1(expression + "\n" + error.toString())]);
  node.style.color = options.errorColor;
  node.style.whiteSpace = "pre-line";
  return node;
};

/**
 * @type {import('./temml').renderToMathMLTree}
 * Generates and returns the Temml build tree. This is used for advanced
 * use cases (like rendering to custom output).
 */
const renderToMathMLTree = function(expression, options) {
  const settings = new Settings(options);
  try {
    const tree = parseTree(expression, settings);
    const style = new Style({
      level: settings.displayMode ? StyleLevel.DISPLAY : StyleLevel.TEXT,
      maxSize: settings.maxSize
    });
    return buildMathML(tree, expression, style, settings);
  } catch (error) {
    return renderError(error, expression, settings);
  }
};

/** @type {import('./temml').default} */
var temml = {
  /**
   * Current Temml version
   */
  version: version,
  /**
   * Renders the given LaTeX into MathML, and adds
   * it as a child to the specified DOM node.
   */
  render,
  /**
   * Renders the given LaTeX into MathML string,
   * for sending to the client.
   */
  renderToString,
  /**
   * Post-process an entire HTML block.
   * Writes AMS auto-numbers and implements \ref{}.
   * Typcally called once, after a loop has rendered many individual spans.
   */
  postProcess,
  /**
   * Temml error, usually during parsing.
   */
  ParseError,
  /**
   * Creates a set of macros with document-wide scope.
   */
  definePreamble,
  /**
   * Parses the given LaTeX into Temml's internal parse tree structure,
   * without rendering to HTML or MathML.
   *
   * NOTE: This method is not currently recommended for public use.
   * The internal tree representation is unstable and is very likely
   * to change. Use at your own risk.
   */
  __parse: generateParseTree,
  /**
   * Renders the given LaTeX into a MathML internal DOM tree
   * representation, without flattening that representation to a string.
   *
   * NOTE: This method is not currently recommended for public use.
   * The internal tree representation is unstable and is very likely
   * to change. Use at your own risk.
   */
  __renderToMathMLTree: renderToMathMLTree,
  /**
   * adds a new symbol to builtin symbols table
   */
  __defineSymbol: defineSymbol,
  /**
   * adds a new macro to builtin macro list
   */
  __defineMacro: defineMacro
};



;// ./node_modules/@wordpress/latex-to-mathml/build-module/index.js

function latexToMathML(latex, { displayMode = true } = {}) {
  const mathML = temml.renderToString(latex, {
    displayMode,
    annotate: true,
    throwOnError: true
  });
  const doc = document.implementation.createHTMLDocument("");
  doc.body.innerHTML = mathML;
  return doc.body.querySelector("math")?.innerHTML ?? "";
}


(window.wp = window.wp || {}).latexToMathml = __webpack_exports__;
/******/ })()
;