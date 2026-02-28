// node_modules/temml/dist/temml.mjs
var ParseError = class _ParseError {
  constructor(message, token) {
    let error = " " + message;
    let start;
    const loc = token && token.loc;
    if (loc && loc.start <= loc.end) {
      const input = loc.lexer.input;
      start = loc.start;
      const end = loc.end;
      if (start === input.length) {
        error += " at end of input: ";
      } else {
        error += " at position " + (start + 1) + ": ";
      }
      const underlined = input.slice(start, end).replace(/[^]/g, "$&\u0332");
      let left;
      if (start > 15) {
        left = "\u2026" + input.slice(start - 15, start);
      } else {
        left = input.slice(0, start);
      }
      let right;
      if (end + 15 < input.length) {
        right = input.slice(end, end + 15) + "\u2026";
      } else {
        right = input.slice(end);
      }
      error += left + underlined + right;
    }
    const self = new Error(error);
    self.name = "ParseError";
    self.__proto__ = _ParseError.prototype;
    self.position = start;
    return self;
  }
};
ParseError.prototype.__proto__ = Error.prototype;
var deflt = function(setting, defaultIfUndefined) {
  return setting === void 0 ? defaultIfUndefined : setting;
};
var uppercase = /([A-Z])/g;
var hyphenate = function(str) {
  return str.replace(uppercase, "-$1").toLowerCase();
};
var ESCAPE_LOOKUP = {
  "&": "&amp;",
  ">": "&gt;",
  "<": "&lt;",
  '"': "&quot;",
  "'": "&#x27;"
};
var ESCAPE_REGEX = /[&><"']/g;
function escape(text2) {
  return String(text2).replace(ESCAPE_REGEX, (match) => ESCAPE_LOOKUP[match]);
}
var getBaseElem = function(group) {
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
var isCharacterBox = function(group) {
  const baseElem = getBaseElem(group);
  return baseElem.type === "mathord" || baseElem.type === "textord" || baseElem.type === "atom";
};
var assert = function(value) {
  if (!value) {
    throw new Error("Expected non-null, but got " + String(value));
  }
  return value;
};
var protocolFromUrl = function(url) {
  const protocol = /^[\x00-\x20]*([^\\/#?]*?)(:|&#0*58|&#x0*3a|&colon)/i.exec(url);
  if (!protocol) {
    return "_relative";
  }
  if (protocol[2] !== ":") {
    return null;
  }
  if (!/^[a-zA-Z][a-zA-Z0-9+\-.]*$/.test(protocol[1])) {
    return null;
  }
  return protocol[1].toLowerCase();
};
var round = function(n) {
  return +n.toFixed(4);
};
var utils = {
  deflt,
  escape,
  hyphenate,
  getBaseElem,
  isCharacterBox,
  protocolFromUrl,
  round
};
var Settings = class {
  constructor(options) {
    options = options || {};
    this.displayMode = utils.deflt(options.displayMode, false);
    this.annotate = utils.deflt(options.annotate, false);
    this.leqno = utils.deflt(options.leqno, false);
    this.throwOnError = utils.deflt(options.throwOnError, false);
    this.errorColor = utils.deflt(options.errorColor, "#b22222");
    this.macros = options.macros || {};
    this.wrap = utils.deflt(options.wrap, "tex");
    this.xml = utils.deflt(options.xml, false);
    this.colorIsTextColor = utils.deflt(options.colorIsTextColor, false);
    this.strict = utils.deflt(options.strict, false);
    this.trust = utils.deflt(options.trust, false);
    this.maxSize = options.maxSize === void 0 ? [Infinity, Infinity] : Array.isArray(options.maxSize) ? options.maxSize : [Infinity, Infinity];
    this.maxExpand = Math.max(0, utils.deflt(options.maxExpand, 1e3));
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
        return false;
      }
      context.protocol = protocol;
    }
    const trust = typeof this.trust === "function" ? this.trust(context) : this.trust;
    return Boolean(trust);
  }
};
var _functions = {};
var _mathmlGroupBuilders = {};
function defineFunction({
  type,
  names,
  props,
  handler,
  mathmlBuilder: mathmlBuilder2
}) {
  const data = {
    type,
    numArgs: props.numArgs,
    argTypes: props.argTypes,
    allowedInArgument: !!props.allowedInArgument,
    allowedInText: !!props.allowedInText,
    allowedInMath: props.allowedInMath === void 0 ? true : props.allowedInMath,
    numOptionalArgs: props.numOptionalArgs || 0,
    infix: !!props.infix,
    primitive: !!props.primitive,
    handler
  };
  for (let i = 0; i < names.length; ++i) {
    _functions[names[i]] = data;
  }
  if (type) {
    if (mathmlBuilder2) {
      _mathmlGroupBuilders[type] = mathmlBuilder2;
    }
  }
}
function defineFunctionBuilders({ type, mathmlBuilder: mathmlBuilder2 }) {
  defineFunction({
    type,
    names: [],
    props: { numArgs: 0 },
    handler() {
      throw new Error("Should never be called.");
    },
    mathmlBuilder: mathmlBuilder2
  });
}
var normalizeArgument = function(arg) {
  return arg.type === "ordgroup" && arg.body.length === 1 ? arg.body[0] : arg;
};
var ordargument = function(arg) {
  return arg.type === "ordgroup" ? arg.body : [arg];
};
var DocumentFragment = class {
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
    const toText = (child) => child.toText();
    return this.children.map(toText).join("");
  }
};
var createClass = function(classes) {
  return classes.filter((cls) => cls).join(" ");
};
var initNode = function(classes, style) {
  this.classes = classes || [];
  this.attributes = {};
  this.style = style || {};
};
var toNode = function(tagName) {
  const node = document.createElement(tagName);
  node.className = createClass(this.classes);
  for (const style in this.style) {
    if (Object.prototype.hasOwnProperty.call(this.style, style)) {
      node.style[style] = this.style[style];
    }
  }
  for (const attr in this.attributes) {
    if (Object.prototype.hasOwnProperty.call(this.attributes, attr)) {
      node.setAttribute(attr, this.attributes[attr]);
    }
  }
  for (let i = 0; i < this.children.length; i++) {
    node.appendChild(this.children[i].toNode());
  }
  return node;
};
var toMarkup = function(tagName) {
  let markup = `<${tagName}`;
  if (this.classes.length) {
    markup += ` class="${utils.escape(createClass(this.classes))}"`;
  }
  let styles = "";
  for (const style in this.style) {
    if (Object.prototype.hasOwnProperty.call(this.style, style)) {
      styles += `${utils.hyphenate(style)}:${this.style[style]};`;
    }
  }
  if (styles) {
    markup += ` style="${styles}"`;
  }
  for (const attr in this.attributes) {
    if (Object.prototype.hasOwnProperty.call(this.attributes, attr)) {
      markup += ` ${attr}="${utils.escape(this.attributes[attr])}"`;
    }
  }
  markup += ">";
  for (let i = 0; i < this.children.length; i++) {
    markup += this.children[i].toMarkup();
  }
  markup += `</${tagName}>`;
  return markup;
};
var Span = class {
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
};
var TextNode$1 = class TextNode {
  constructor(text2) {
    this.text = text2;
  }
  toNode() {
    return document.createTextNode(this.text);
  }
  toMarkup() {
    return utils.escape(this.text);
  }
};
var AnchorNode = class {
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
    return node;
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
    return markup;
  }
};
var Img = class {
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
    for (const style in this.style) {
      if (Object.prototype.hasOwnProperty.call(this.style, style)) {
        node.style[style] = this.style[style];
      }
    }
    return node;
  }
  toMarkup() {
    let markup = `<img src='${this.src}' alt='${this.alt}'`;
    let styles = "";
    for (const style in this.style) {
      if (Object.prototype.hasOwnProperty.call(this.style, style)) {
        styles += `${utils.hyphenate(style)}:${this.style[style]};`;
      }
    }
    if (styles) {
      markup += ` style="${utils.escape(styles)}"`;
    }
    markup += ">";
    return markup;
  }
};
function newDocumentFragment(children) {
  return new DocumentFragment(children);
}
var MathNode = class {
  constructor(type, children, classes, style) {
    this.type = type;
    this.attributes = {};
    this.children = children || [];
    this.classes = classes || [];
    this.style = style || {};
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
    for (const style in this.style) {
      if (Object.prototype.hasOwnProperty.call(this.style, style)) {
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
    for (const style in this.style) {
      if (Object.prototype.hasOwnProperty.call(this.style, style)) {
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
};
var TextNode2 = class {
  constructor(text2) {
    this.text = text2;
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
};
var wrapWithMstyle = (expression) => {
  let node;
  if (expression.length === 1 && expression[0].type === "mrow") {
    node = expression.pop();
    node.type = "mstyle";
  } else {
    node = new MathNode("mstyle", expression);
  }
  return node;
};
var mathMLTree = {
  MathNode,
  TextNode: TextNode2,
  newDocumentFragment
};
var estimatedWidth = (node) => {
  let width = 0;
  if (node.body) {
    for (const item of node.body) {
      width += estimatedWidth(item);
    }
  } else if (node.type === "supsub") {
    width += estimatedWidth(node.base);
    if (node.sub) {
      width += 0.7 * estimatedWidth(node.sub);
    }
    if (node.sup) {
      width += 0.7 * estimatedWidth(node.sup);
    }
  } else if (node.type === "mathord" || node.type === "textord") {
    for (const ch of node.text.split("")) {
      const codePoint = ch.codePointAt(0);
      if (96 < codePoint && codePoint < 123 || 944 < codePoint && codePoint < 970) {
        width += 0.56;
      } else if (47 < codePoint && codePoint < 58) {
        width += 0.5;
      } else {
        width += 0.92;
      }
    }
  } else {
    width += 1;
  }
  return width;
};
var stretchyCodePoint = {
  widehat: "^",
  widecheck: "\u02C7",
  widetilde: "~",
  wideparen: "\u23DC",
  // \u23dc
  utilde: "~",
  overleftarrow: "\u2190",
  underleftarrow: "\u2190",
  xleftarrow: "\u2190",
  overrightarrow: "\u2192",
  underrightarrow: "\u2192",
  xrightarrow: "\u2192",
  underbrace: "\u23DF",
  overbrace: "\u23DE",
  overgroup: "\u23E0",
  overparen: "\u23DC",
  undergroup: "\u23E1",
  underparen: "\u23DD",
  overleftrightarrow: "\u2194",
  underleftrightarrow: "\u2194",
  xleftrightarrow: "\u2194",
  Overrightarrow: "\u21D2",
  xRightarrow: "\u21D2",
  overleftharpoon: "\u21BC",
  xleftharpoonup: "\u21BC",
  overrightharpoon: "\u21C0",
  xrightharpoonup: "\u21C0",
  xLeftarrow: "\u21D0",
  xLeftrightarrow: "\u21D4",
  xhookleftarrow: "\u21A9",
  xhookrightarrow: "\u21AA",
  xmapsto: "\u21A6",
  xrightharpoondown: "\u21C1",
  xleftharpoondown: "\u21BD",
  xtwoheadleftarrow: "\u219E",
  xtwoheadrightarrow: "\u21A0",
  xlongequal: "=",
  xrightleftarrows: "\u21C4",
  yields: "\u2192",
  yieldsLeft: "\u2190",
  mesomerism: "\u2194",
  longrightharpoonup: "\u21C0",
  longleftharpoondown: "\u21BD",
  eqrightharpoonup: "\u21C0",
  eqleftharpoondown: "\u21BD",
  "\\cdrightarrow": "\u2192",
  "\\cdleftarrow": "\u2190",
  "\\cdlongequal": "="
};
var mathMLnode = function(label) {
  const child = new mathMLTree.TextNode(stretchyCodePoint[label.slice(1)]);
  const node = new mathMLTree.MathNode("mo", [child]);
  node.setAttribute("stretchy", "true");
  return node;
};
var crookedWides = ["\\widetilde", "\\widehat", "\\widecheck", "\\utilde"];
var accentNode = (group) => {
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
  return mo;
};
var stretchy = {
  mathMLnode,
  accentNode
};
var ATOMS = {
  bin: 1,
  close: 1,
  inner: 1,
  open: 1,
  punct: 1,
  rel: 1
};
var NON_ATOMS = {
  "accent-token": 1,
  mathord: 1,
  "op-token": 1,
  spacing: 1,
  textord: 1
};
var symbols = {
  math: {},
  text: {}
};
function defineSymbol(mode, group, replace, name, acceptUnicodeChar) {
  symbols[mode][name] = { group, replace };
  if (acceptUnicodeChar && replace) {
    symbols[mode][replace] = symbols[mode][name];
  }
}
var math = "math";
var text = "text";
var accent = "accent-token";
var bin = "bin";
var close = "close";
var inner = "inner";
var mathord = "mathord";
var op = "op-token";
var open = "open";
var punct = "punct";
var rel = "rel";
var spacing = "spacing";
var textord = "textord";
defineSymbol(math, rel, "\u2261", "\\equiv", true);
defineSymbol(math, rel, "\u227A", "\\prec", true);
defineSymbol(math, rel, "\u227B", "\\succ", true);
defineSymbol(math, rel, "\u223C", "\\sim", true);
defineSymbol(math, rel, "\u27C2", "\\perp", true);
defineSymbol(math, rel, "\u2AAF", "\\preceq", true);
defineSymbol(math, rel, "\u2AB0", "\\succeq", true);
defineSymbol(math, rel, "\u2243", "\\simeq", true);
defineSymbol(math, rel, "\u224C", "\\backcong", true);
defineSymbol(math, rel, "|", "\\mid", true);
defineSymbol(math, rel, "\u226A", "\\ll", true);
defineSymbol(math, rel, "\u226B", "\\gg", true);
defineSymbol(math, rel, "\u224D", "\\asymp", true);
defineSymbol(math, rel, "\u2225", "\\parallel");
defineSymbol(math, rel, "\u2323", "\\smile", true);
defineSymbol(math, rel, "\u2291", "\\sqsubseteq", true);
defineSymbol(math, rel, "\u2292", "\\sqsupseteq", true);
defineSymbol(math, rel, "\u2250", "\\doteq", true);
defineSymbol(math, rel, "\u2322", "\\frown", true);
defineSymbol(math, rel, "\u220B", "\\ni", true);
defineSymbol(math, rel, "\u220C", "\\notni", true);
defineSymbol(math, rel, "\u221D", "\\propto", true);
defineSymbol(math, rel, "\u22A2", "\\vdash", true);
defineSymbol(math, rel, "\u22A3", "\\dashv", true);
defineSymbol(math, rel, "\u220B", "\\owns");
defineSymbol(math, rel, "\u2258", "\\arceq", true);
defineSymbol(math, rel, "\u2259", "\\wedgeq", true);
defineSymbol(math, rel, "\u225A", "\\veeeq", true);
defineSymbol(math, rel, "\u225B", "\\stareq", true);
defineSymbol(math, rel, "\u225D", "\\eqdef", true);
defineSymbol(math, rel, "\u225E", "\\measeq", true);
defineSymbol(math, rel, "\u225F", "\\questeq", true);
defineSymbol(math, rel, "\u2260", "\\ne", true);
defineSymbol(math, rel, "\u2260", "\\neq");
defineSymbol(math, rel, "\u2A75", "\\eqeq", true);
defineSymbol(math, rel, "\u2A76", "\\eqeqeq", true);
defineSymbol(math, rel, "\u2237", "\\dblcolon", true);
defineSymbol(math, rel, "\u2254", "\\coloneqq", true);
defineSymbol(math, rel, "\u2255", "\\eqqcolon", true);
defineSymbol(math, rel, "\u2239", "\\eqcolon", true);
defineSymbol(math, rel, "\u2A74", "\\Coloneqq", true);
defineSymbol(math, punct, ".", "\\ldotp");
defineSymbol(math, punct, "\xB7", "\\cdotp");
defineSymbol(math, textord, "#", "\\#");
defineSymbol(text, textord, "#", "\\#");
defineSymbol(math, textord, "&", "\\&");
defineSymbol(text, textord, "&", "\\&");
defineSymbol(math, textord, "\u2135", "\\aleph", true);
defineSymbol(math, textord, "\u2200", "\\forall", true);
defineSymbol(math, textord, "\u210F", "\\hbar", true);
defineSymbol(math, textord, "\u2203", "\\exists", true);
defineSymbol(math, bin, "\u2207", "\\nabla", true);
defineSymbol(math, textord, "\u266D", "\\flat", true);
defineSymbol(math, textord, "\u2113", "\\ell", true);
defineSymbol(math, textord, "\u266E", "\\natural", true);
defineSymbol(math, textord, "\u212B", "\\Angstrom", true);
defineSymbol(text, textord, "\u212B", "\\Angstrom", true);
defineSymbol(math, textord, "\u2663", "\\clubsuit", true);
defineSymbol(math, textord, "\u2667", "\\varclubsuit", true);
defineSymbol(math, textord, "\u2118", "\\wp", true);
defineSymbol(math, textord, "\u266F", "\\sharp", true);
defineSymbol(math, textord, "\u2662", "\\diamondsuit", true);
defineSymbol(math, textord, "\u2666", "\\vardiamondsuit", true);
defineSymbol(math, textord, "\u211C", "\\Re", true);
defineSymbol(math, textord, "\u2661", "\\heartsuit", true);
defineSymbol(math, textord, "\u2665", "\\varheartsuit", true);
defineSymbol(math, textord, "\u2111", "\\Im", true);
defineSymbol(math, textord, "\u2660", "\\spadesuit", true);
defineSymbol(math, textord, "\u2664", "\\varspadesuit", true);
defineSymbol(math, textord, "\u2640", "\\female", true);
defineSymbol(math, textord, "\u2642", "\\male", true);
defineSymbol(math, textord, "\xA7", "\\S", true);
defineSymbol(text, textord, "\xA7", "\\S");
defineSymbol(math, textord, "\xB6", "\\P", true);
defineSymbol(text, textord, "\xB6", "\\P");
defineSymbol(text, textord, "\u263A", "\\smiley", true);
defineSymbol(math, textord, "\u263A", "\\smiley", true);
defineSymbol(math, textord, "\u2020", "\\dag");
defineSymbol(text, textord, "\u2020", "\\dag");
defineSymbol(text, textord, "\u2020", "\\textdagger");
defineSymbol(math, textord, "\u2021", "\\ddag");
defineSymbol(text, textord, "\u2021", "\\ddag");
defineSymbol(text, textord, "\u2021", "\\textdaggerdbl");
defineSymbol(math, close, "\u23B1", "\\rmoustache", true);
defineSymbol(math, open, "\u23B0", "\\lmoustache", true);
defineSymbol(math, close, "\u27EF", "\\rgroup", true);
defineSymbol(math, open, "\u27EE", "\\lgroup", true);
defineSymbol(math, bin, "\u2213", "\\mp", true);
defineSymbol(math, bin, "\u2296", "\\ominus", true);
defineSymbol(math, bin, "\u228E", "\\uplus", true);
defineSymbol(math, bin, "\u2293", "\\sqcap", true);
defineSymbol(math, bin, "\u2217", "\\ast");
defineSymbol(math, bin, "\u2294", "\\sqcup", true);
defineSymbol(math, bin, "\u25EF", "\\bigcirc", true);
defineSymbol(math, bin, "\u2219", "\\bullet", true);
defineSymbol(math, bin, "\u2021", "\\ddagger");
defineSymbol(math, bin, "\u2240", "\\wr", true);
defineSymbol(math, bin, "\u2A3F", "\\amalg");
defineSymbol(math, bin, "&", "\\And");
defineSymbol(math, bin, "\u2AFD", "\\sslash", true);
defineSymbol(math, rel, "\u27F5", "\\longleftarrow", true);
defineSymbol(math, rel, "\u21D0", "\\Leftarrow", true);
defineSymbol(math, rel, "\u27F8", "\\Longleftarrow", true);
defineSymbol(math, rel, "\u27F6", "\\longrightarrow", true);
defineSymbol(math, rel, "\u21D2", "\\Rightarrow", true);
defineSymbol(math, rel, "\u27F9", "\\Longrightarrow", true);
defineSymbol(math, rel, "\u2194", "\\leftrightarrow", true);
defineSymbol(math, rel, "\u27F7", "\\longleftrightarrow", true);
defineSymbol(math, rel, "\u21D4", "\\Leftrightarrow", true);
defineSymbol(math, rel, "\u27FA", "\\Longleftrightarrow", true);
defineSymbol(math, rel, "\u21A4", "\\mapsfrom", true);
defineSymbol(math, rel, "\u21A6", "\\mapsto", true);
defineSymbol(math, rel, "\u27FC", "\\longmapsto", true);
defineSymbol(math, rel, "\u2197", "\\nearrow", true);
defineSymbol(math, rel, "\u21A9", "\\hookleftarrow", true);
defineSymbol(math, rel, "\u21AA", "\\hookrightarrow", true);
defineSymbol(math, rel, "\u2198", "\\searrow", true);
defineSymbol(math, rel, "\u21BC", "\\leftharpoonup", true);
defineSymbol(math, rel, "\u21C0", "\\rightharpoonup", true);
defineSymbol(math, rel, "\u2199", "\\swarrow", true);
defineSymbol(math, rel, "\u21BD", "\\leftharpoondown", true);
defineSymbol(math, rel, "\u21C1", "\\rightharpoondown", true);
defineSymbol(math, rel, "\u2196", "\\nwarrow", true);
defineSymbol(math, rel, "\u21CC", "\\rightleftharpoons", true);
defineSymbol(math, mathord, "\u21AF", "\\lightning", true);
defineSymbol(math, mathord, "\u220E", "\\QED", true);
defineSymbol(math, mathord, "\u2030", "\\permil", true);
defineSymbol(text, textord, "\u2030", "\\permil");
defineSymbol(math, mathord, "\u2609", "\\astrosun", true);
defineSymbol(math, mathord, "\u263C", "\\sun", true);
defineSymbol(math, mathord, "\u263E", "\\leftmoon", true);
defineSymbol(math, mathord, "\u263D", "\\rightmoon", true);
defineSymbol(math, mathord, "\u2295", "\\Earth");
defineSymbol(math, rel, "\u226E", "\\nless", true);
defineSymbol(math, rel, "\u2A87", "\\lneq", true);
defineSymbol(math, rel, "\u2268", "\\lneqq", true);
defineSymbol(math, rel, "\u2268\uFE00", "\\lvertneqq");
defineSymbol(math, rel, "\u22E6", "\\lnsim", true);
defineSymbol(math, rel, "\u2A89", "\\lnapprox", true);
defineSymbol(math, rel, "\u2280", "\\nprec", true);
defineSymbol(math, rel, "\u22E0", "\\npreceq", true);
defineSymbol(math, rel, "\u22E8", "\\precnsim", true);
defineSymbol(math, rel, "\u2AB9", "\\precnapprox", true);
defineSymbol(math, rel, "\u2241", "\\nsim", true);
defineSymbol(math, rel, "\u2224", "\\nmid", true);
defineSymbol(math, rel, "\u2224", "\\nshortmid");
defineSymbol(math, rel, "\u22AC", "\\nvdash", true);
defineSymbol(math, rel, "\u22AD", "\\nvDash", true);
defineSymbol(math, rel, "\u22EA", "\\ntriangleleft");
defineSymbol(math, rel, "\u22EC", "\\ntrianglelefteq", true);
defineSymbol(math, rel, "\u2284", "\\nsubset", true);
defineSymbol(math, rel, "\u2285", "\\nsupset", true);
defineSymbol(math, rel, "\u228A", "\\subsetneq", true);
defineSymbol(math, rel, "\u228A\uFE00", "\\varsubsetneq");
defineSymbol(math, rel, "\u2ACB", "\\subsetneqq", true);
defineSymbol(math, rel, "\u2ACB\uFE00", "\\varsubsetneqq");
defineSymbol(math, rel, "\u226F", "\\ngtr", true);
defineSymbol(math, rel, "\u2A88", "\\gneq", true);
defineSymbol(math, rel, "\u2269", "\\gneqq", true);
defineSymbol(math, rel, "\u2269\uFE00", "\\gvertneqq");
defineSymbol(math, rel, "\u22E7", "\\gnsim", true);
defineSymbol(math, rel, "\u2A8A", "\\gnapprox", true);
defineSymbol(math, rel, "\u2281", "\\nsucc", true);
defineSymbol(math, rel, "\u22E1", "\\nsucceq", true);
defineSymbol(math, rel, "\u22E9", "\\succnsim", true);
defineSymbol(math, rel, "\u2ABA", "\\succnapprox", true);
defineSymbol(math, rel, "\u2246", "\\ncong", true);
defineSymbol(math, rel, "\u2226", "\\nparallel", true);
defineSymbol(math, rel, "\u2226", "\\nshortparallel");
defineSymbol(math, rel, "\u22AF", "\\nVDash", true);
defineSymbol(math, rel, "\u22EB", "\\ntriangleright");
defineSymbol(math, rel, "\u22ED", "\\ntrianglerighteq", true);
defineSymbol(math, rel, "\u228B", "\\supsetneq", true);
defineSymbol(math, rel, "\u228B", "\\varsupsetneq");
defineSymbol(math, rel, "\u2ACC", "\\supsetneqq", true);
defineSymbol(math, rel, "\u2ACC\uFE00", "\\varsupsetneqq");
defineSymbol(math, rel, "\u22AE", "\\nVdash", true);
defineSymbol(math, rel, "\u2AB5", "\\precneqq", true);
defineSymbol(math, rel, "\u2AB6", "\\succneqq", true);
defineSymbol(math, bin, "\u22B4", "\\unlhd");
defineSymbol(math, bin, "\u22B5", "\\unrhd");
defineSymbol(math, rel, "\u219A", "\\nleftarrow", true);
defineSymbol(math, rel, "\u219B", "\\nrightarrow", true);
defineSymbol(math, rel, "\u21CD", "\\nLeftarrow", true);
defineSymbol(math, rel, "\u21CF", "\\nRightarrow", true);
defineSymbol(math, rel, "\u21AE", "\\nleftrightarrow", true);
defineSymbol(math, rel, "\u21CE", "\\nLeftrightarrow", true);
defineSymbol(math, rel, "\u25B3", "\\vartriangle");
defineSymbol(math, textord, "\u210F", "\\hslash");
defineSymbol(math, textord, "\u25BD", "\\triangledown");
defineSymbol(math, textord, "\u25CA", "\\lozenge");
defineSymbol(math, textord, "\u24C8", "\\circledS");
defineSymbol(math, textord, "\xAE", "\\circledR", true);
defineSymbol(text, textord, "\xAE", "\\circledR");
defineSymbol(text, textord, "\xAE", "\\textregistered");
defineSymbol(math, textord, "\u2221", "\\measuredangle", true);
defineSymbol(math, textord, "\u2204", "\\nexists");
defineSymbol(math, textord, "\u2127", "\\mho");
defineSymbol(math, textord, "\u2132", "\\Finv", true);
defineSymbol(math, textord, "\u2141", "\\Game", true);
defineSymbol(math, textord, "\u2035", "\\backprime");
defineSymbol(math, textord, "\u2036", "\\backdprime");
defineSymbol(math, textord, "\u2037", "\\backtrprime");
defineSymbol(math, textord, "\u25B2", "\\blacktriangle");
defineSymbol(math, textord, "\u25BC", "\\blacktriangledown");
defineSymbol(math, textord, "\u25A0", "\\blacksquare");
defineSymbol(math, textord, "\u29EB", "\\blacklozenge");
defineSymbol(math, textord, "\u2605", "\\bigstar");
defineSymbol(math, textord, "\u2222", "\\sphericalangle", true);
defineSymbol(math, textord, "\u2201", "\\complement", true);
defineSymbol(math, textord, "\xF0", "\\eth", true);
defineSymbol(text, textord, "\xF0", "\xF0");
defineSymbol(math, textord, "\u2571", "\\diagup");
defineSymbol(math, textord, "\u2572", "\\diagdown");
defineSymbol(math, textord, "\u25A1", "\\square");
defineSymbol(math, textord, "\u25A1", "\\Box");
defineSymbol(math, textord, "\u25CA", "\\Diamond");
defineSymbol(math, textord, "\xA5", "\\yen", true);
defineSymbol(text, textord, "\xA5", "\\yen", true);
defineSymbol(math, textord, "\u2713", "\\checkmark", true);
defineSymbol(text, textord, "\u2713", "\\checkmark");
defineSymbol(math, textord, "\u2717", "\\ballotx", true);
defineSymbol(text, textord, "\u2717", "\\ballotx");
defineSymbol(text, textord, "\u2022", "\\textbullet");
defineSymbol(math, textord, "\u2136", "\\beth", true);
defineSymbol(math, textord, "\u2138", "\\daleth", true);
defineSymbol(math, textord, "\u2137", "\\gimel", true);
defineSymbol(math, textord, "\u03DD", "\\digamma", true);
defineSymbol(math, textord, "\u03F0", "\\varkappa");
defineSymbol(math, open, "\u231C", "\\ulcorner", true);
defineSymbol(math, close, "\u231D", "\\urcorner", true);
defineSymbol(math, open, "\u231E", "\\llcorner", true);
defineSymbol(math, close, "\u231F", "\\lrcorner", true);
defineSymbol(math, rel, "\u2266", "\\leqq", true);
defineSymbol(math, rel, "\u2A7D", "\\leqslant", true);
defineSymbol(math, rel, "\u2A95", "\\eqslantless", true);
defineSymbol(math, rel, "\u2272", "\\lesssim", true);
defineSymbol(math, rel, "\u2A85", "\\lessapprox", true);
defineSymbol(math, rel, "\u224A", "\\approxeq", true);
defineSymbol(math, bin, "\u22D6", "\\lessdot");
defineSymbol(math, rel, "\u22D8", "\\lll", true);
defineSymbol(math, rel, "\u2276", "\\lessgtr", true);
defineSymbol(math, rel, "\u22DA", "\\lesseqgtr", true);
defineSymbol(math, rel, "\u2A8B", "\\lesseqqgtr", true);
defineSymbol(math, rel, "\u2251", "\\doteqdot");
defineSymbol(math, rel, "\u2253", "\\risingdotseq", true);
defineSymbol(math, rel, "\u2252", "\\fallingdotseq", true);
defineSymbol(math, rel, "\u223D", "\\backsim", true);
defineSymbol(math, rel, "\u22CD", "\\backsimeq", true);
defineSymbol(math, rel, "\u2AC5", "\\subseteqq", true);
defineSymbol(math, rel, "\u22D0", "\\Subset", true);
defineSymbol(math, rel, "\u228F", "\\sqsubset", true);
defineSymbol(math, rel, "\u227C", "\\preccurlyeq", true);
defineSymbol(math, rel, "\u22DE", "\\curlyeqprec", true);
defineSymbol(math, rel, "\u227E", "\\precsim", true);
defineSymbol(math, rel, "\u2AB7", "\\precapprox", true);
defineSymbol(math, rel, "\u22B2", "\\vartriangleleft");
defineSymbol(math, rel, "\u22B4", "\\trianglelefteq");
defineSymbol(math, rel, "\u22A8", "\\vDash", true);
defineSymbol(math, rel, "\u22AB", "\\VDash", true);
defineSymbol(math, rel, "\u22AA", "\\Vvdash", true);
defineSymbol(math, rel, "\u2323", "\\smallsmile");
defineSymbol(math, rel, "\u2322", "\\smallfrown");
defineSymbol(math, rel, "\u224F", "\\bumpeq", true);
defineSymbol(math, rel, "\u224E", "\\Bumpeq", true);
defineSymbol(math, rel, "\u2267", "\\geqq", true);
defineSymbol(math, rel, "\u2A7E", "\\geqslant", true);
defineSymbol(math, rel, "\u2A96", "\\eqslantgtr", true);
defineSymbol(math, rel, "\u2273", "\\gtrsim", true);
defineSymbol(math, rel, "\u2A86", "\\gtrapprox", true);
defineSymbol(math, bin, "\u22D7", "\\gtrdot");
defineSymbol(math, rel, "\u22D9", "\\ggg", true);
defineSymbol(math, rel, "\u2277", "\\gtrless", true);
defineSymbol(math, rel, "\u22DB", "\\gtreqless", true);
defineSymbol(math, rel, "\u2A8C", "\\gtreqqless", true);
defineSymbol(math, rel, "\u2256", "\\eqcirc", true);
defineSymbol(math, rel, "\u2257", "\\circeq", true);
defineSymbol(math, rel, "\u225C", "\\triangleq", true);
defineSymbol(math, rel, "\u223C", "\\thicksim");
defineSymbol(math, rel, "\u2248", "\\thickapprox");
defineSymbol(math, rel, "\u2AC6", "\\supseteqq", true);
defineSymbol(math, rel, "\u22D1", "\\Supset", true);
defineSymbol(math, rel, "\u2290", "\\sqsupset", true);
defineSymbol(math, rel, "\u227D", "\\succcurlyeq", true);
defineSymbol(math, rel, "\u22DF", "\\curlyeqsucc", true);
defineSymbol(math, rel, "\u227F", "\\succsim", true);
defineSymbol(math, rel, "\u2AB8", "\\succapprox", true);
defineSymbol(math, rel, "\u22B3", "\\vartriangleright");
defineSymbol(math, rel, "\u22B5", "\\trianglerighteq");
defineSymbol(math, rel, "\u22A9", "\\Vdash", true);
defineSymbol(math, rel, "\u2223", "\\shortmid");
defineSymbol(math, rel, "\u2225", "\\shortparallel");
defineSymbol(math, rel, "\u226C", "\\between", true);
defineSymbol(math, rel, "\u22D4", "\\pitchfork", true);
defineSymbol(math, rel, "\u221D", "\\varpropto");
defineSymbol(math, rel, "\u25C0", "\\blacktriangleleft");
defineSymbol(math, rel, "\u2234", "\\therefore", true);
defineSymbol(math, rel, "\u220D", "\\backepsilon");
defineSymbol(math, rel, "\u25B6", "\\blacktriangleright");
defineSymbol(math, rel, "\u2235", "\\because", true);
defineSymbol(math, rel, "\u22D8", "\\llless");
defineSymbol(math, rel, "\u22D9", "\\gggtr");
defineSymbol(math, bin, "\u22B2", "\\lhd");
defineSymbol(math, bin, "\u22B3", "\\rhd");
defineSymbol(math, rel, "\u2242", "\\eqsim", true);
defineSymbol(math, rel, "\u2251", "\\Doteq", true);
defineSymbol(math, rel, "\u297D", "\\strictif", true);
defineSymbol(math, rel, "\u297C", "\\strictfi", true);
defineSymbol(math, bin, "\u2214", "\\dotplus", true);
defineSymbol(math, bin, "\u2216", "\\smallsetminus");
defineSymbol(math, bin, "\u22D2", "\\Cap", true);
defineSymbol(math, bin, "\u22D3", "\\Cup", true);
defineSymbol(math, bin, "\u2A5E", "\\doublebarwedge", true);
defineSymbol(math, bin, "\u229F", "\\boxminus", true);
defineSymbol(math, bin, "\u229E", "\\boxplus", true);
defineSymbol(math, bin, "\u29C4", "\\boxslash", true);
defineSymbol(math, bin, "\u22C7", "\\divideontimes", true);
defineSymbol(math, bin, "\u22C9", "\\ltimes", true);
defineSymbol(math, bin, "\u22CA", "\\rtimes", true);
defineSymbol(math, bin, "\u22CB", "\\leftthreetimes", true);
defineSymbol(math, bin, "\u22CC", "\\rightthreetimes", true);
defineSymbol(math, bin, "\u22CF", "\\curlywedge", true);
defineSymbol(math, bin, "\u22CE", "\\curlyvee", true);
defineSymbol(math, bin, "\u229D", "\\circleddash", true);
defineSymbol(math, bin, "\u229B", "\\circledast", true);
defineSymbol(math, bin, "\u22BA", "\\intercal", true);
defineSymbol(math, bin, "\u22D2", "\\doublecap");
defineSymbol(math, bin, "\u22D3", "\\doublecup");
defineSymbol(math, bin, "\u22A0", "\\boxtimes", true);
defineSymbol(math, bin, "\u22C8", "\\bowtie", true);
defineSymbol(math, bin, "\u22C8", "\\Join");
defineSymbol(math, bin, "\u27D5", "\\leftouterjoin", true);
defineSymbol(math, bin, "\u27D6", "\\rightouterjoin", true);
defineSymbol(math, bin, "\u27D7", "\\fullouterjoin", true);
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
defineSymbol(math, rel, "\u21E2", "\\dashrightarrow", true);
defineSymbol(math, rel, "\u21E0", "\\dashleftarrow", true);
defineSymbol(math, rel, "\u21C7", "\\leftleftarrows", true);
defineSymbol(math, rel, "\u21C6", "\\leftrightarrows", true);
defineSymbol(math, rel, "\u21DA", "\\Lleftarrow", true);
defineSymbol(math, rel, "\u219E", "\\twoheadleftarrow", true);
defineSymbol(math, rel, "\u21A2", "\\leftarrowtail", true);
defineSymbol(math, rel, "\u21AB", "\\looparrowleft", true);
defineSymbol(math, rel, "\u21CB", "\\leftrightharpoons", true);
defineSymbol(math, rel, "\u21B6", "\\curvearrowleft", true);
defineSymbol(math, rel, "\u21BA", "\\circlearrowleft", true);
defineSymbol(math, rel, "\u21B0", "\\Lsh", true);
defineSymbol(math, rel, "\u21C8", "\\upuparrows", true);
defineSymbol(math, rel, "\u21BF", "\\upharpoonleft", true);
defineSymbol(math, rel, "\u21C3", "\\downharpoonleft", true);
defineSymbol(math, rel, "\u22B6", "\\origof", true);
defineSymbol(math, rel, "\u22B7", "\\imageof", true);
defineSymbol(math, rel, "\u22B8", "\\multimap", true);
defineSymbol(math, rel, "\u21AD", "\\leftrightsquigarrow", true);
defineSymbol(math, rel, "\u21C9", "\\rightrightarrows", true);
defineSymbol(math, rel, "\u21C4", "\\rightleftarrows", true);
defineSymbol(math, rel, "\u21A0", "\\twoheadrightarrow", true);
defineSymbol(math, rel, "\u21A3", "\\rightarrowtail", true);
defineSymbol(math, rel, "\u21AC", "\\looparrowright", true);
defineSymbol(math, rel, "\u21B7", "\\curvearrowright", true);
defineSymbol(math, rel, "\u21BB", "\\circlearrowright", true);
defineSymbol(math, rel, "\u21B1", "\\Rsh", true);
defineSymbol(math, rel, "\u21CA", "\\downdownarrows", true);
defineSymbol(math, rel, "\u21BE", "\\upharpoonright", true);
defineSymbol(math, rel, "\u21C2", "\\downharpoonright", true);
defineSymbol(math, rel, "\u21DD", "\\rightsquigarrow", true);
defineSymbol(math, rel, "\u21DD", "\\leadsto");
defineSymbol(math, rel, "\u21DB", "\\Rrightarrow", true);
defineSymbol(math, rel, "\u21BE", "\\restriction");
defineSymbol(math, textord, "\u2018", "`");
defineSymbol(math, textord, "$", "\\$");
defineSymbol(text, textord, "$", "\\$");
defineSymbol(text, textord, "$", "\\textdollar");
defineSymbol(math, textord, "\xA2", "\\cent");
defineSymbol(text, textord, "\xA2", "\\cent");
defineSymbol(math, textord, "%", "\\%");
defineSymbol(text, textord, "%", "\\%");
defineSymbol(math, textord, "_", "\\_");
defineSymbol(text, textord, "_", "\\_");
defineSymbol(text, textord, "_", "\\textunderscore");
defineSymbol(text, textord, "\u2423", "\\textvisiblespace", true);
defineSymbol(math, textord, "\u2220", "\\angle", true);
defineSymbol(math, textord, "\u221E", "\\infty", true);
defineSymbol(math, textord, "\u2032", "\\prime");
defineSymbol(math, textord, "\u2033", "\\dprime");
defineSymbol(math, textord, "\u2034", "\\trprime");
defineSymbol(math, textord, "\u2057", "\\qprime");
defineSymbol(math, textord, "\u25B3", "\\triangle");
defineSymbol(text, textord, "\u0391", "\\Alpha", true);
defineSymbol(text, textord, "\u0392", "\\Beta", true);
defineSymbol(text, textord, "\u0393", "\\Gamma", true);
defineSymbol(text, textord, "\u0394", "\\Delta", true);
defineSymbol(text, textord, "\u0395", "\\Epsilon", true);
defineSymbol(text, textord, "\u0396", "\\Zeta", true);
defineSymbol(text, textord, "\u0397", "\\Eta", true);
defineSymbol(text, textord, "\u0398", "\\Theta", true);
defineSymbol(text, textord, "\u0399", "\\Iota", true);
defineSymbol(text, textord, "\u039A", "\\Kappa", true);
defineSymbol(text, textord, "\u039B", "\\Lambda", true);
defineSymbol(text, textord, "\u039C", "\\Mu", true);
defineSymbol(text, textord, "\u039D", "\\Nu", true);
defineSymbol(text, textord, "\u039E", "\\Xi", true);
defineSymbol(text, textord, "\u039F", "\\Omicron", true);
defineSymbol(text, textord, "\u03A0", "\\Pi", true);
defineSymbol(text, textord, "\u03A1", "\\Rho", true);
defineSymbol(text, textord, "\u03A3", "\\Sigma", true);
defineSymbol(text, textord, "\u03A4", "\\Tau", true);
defineSymbol(text, textord, "\u03A5", "\\Upsilon", true);
defineSymbol(text, textord, "\u03A6", "\\Phi", true);
defineSymbol(text, textord, "\u03A7", "\\Chi", true);
defineSymbol(text, textord, "\u03A8", "\\Psi", true);
defineSymbol(text, textord, "\u03A9", "\\Omega", true);
defineSymbol(math, mathord, "\u0391", "\\Alpha", true);
defineSymbol(math, mathord, "\u0392", "\\Beta", true);
defineSymbol(math, mathord, "\u0393", "\\Gamma", true);
defineSymbol(math, mathord, "\u0394", "\\Delta", true);
defineSymbol(math, mathord, "\u0395", "\\Epsilon", true);
defineSymbol(math, mathord, "\u0396", "\\Zeta", true);
defineSymbol(math, mathord, "\u0397", "\\Eta", true);
defineSymbol(math, mathord, "\u0398", "\\Theta", true);
defineSymbol(math, mathord, "\u0399", "\\Iota", true);
defineSymbol(math, mathord, "\u039A", "\\Kappa", true);
defineSymbol(math, mathord, "\u039B", "\\Lambda", true);
defineSymbol(math, mathord, "\u039C", "\\Mu", true);
defineSymbol(math, mathord, "\u039D", "\\Nu", true);
defineSymbol(math, mathord, "\u039E", "\\Xi", true);
defineSymbol(math, mathord, "\u039F", "\\Omicron", true);
defineSymbol(math, mathord, "\u03A0", "\\Pi", true);
defineSymbol(math, mathord, "\u03A1", "\\Rho", true);
defineSymbol(math, mathord, "\u03A3", "\\Sigma", true);
defineSymbol(math, mathord, "\u03A4", "\\Tau", true);
defineSymbol(math, mathord, "\u03A5", "\\Upsilon", true);
defineSymbol(math, mathord, "\u03A6", "\\Phi", true);
defineSymbol(math, mathord, "\u03A7", "\\Chi", true);
defineSymbol(math, mathord, "\u03A8", "\\Psi", true);
defineSymbol(math, mathord, "\u03A9", "\\Omega", true);
defineSymbol(math, open, "\xAC", "\\neg", true);
defineSymbol(math, open, "\xAC", "\\lnot");
defineSymbol(math, textord, "\u22A4", "\\top");
defineSymbol(math, textord, "\u22A5", "\\bot");
defineSymbol(math, textord, "\u2205", "\\emptyset");
defineSymbol(math, textord, "\u2300", "\\varnothing");
defineSymbol(math, mathord, "\u03B1", "\\alpha", true);
defineSymbol(math, mathord, "\u03B2", "\\beta", true);
defineSymbol(math, mathord, "\u03B3", "\\gamma", true);
defineSymbol(math, mathord, "\u03B4", "\\delta", true);
defineSymbol(math, mathord, "\u03F5", "\\epsilon", true);
defineSymbol(math, mathord, "\u03B6", "\\zeta", true);
defineSymbol(math, mathord, "\u03B7", "\\eta", true);
defineSymbol(math, mathord, "\u03B8", "\\theta", true);
defineSymbol(math, mathord, "\u03B9", "\\iota", true);
defineSymbol(math, mathord, "\u03BA", "\\kappa", true);
defineSymbol(math, mathord, "\u03BB", "\\lambda", true);
defineSymbol(math, mathord, "\u03BC", "\\mu", true);
defineSymbol(math, mathord, "\u03BD", "\\nu", true);
defineSymbol(math, mathord, "\u03BE", "\\xi", true);
defineSymbol(math, mathord, "\u03BF", "\\omicron", true);
defineSymbol(math, mathord, "\u03C0", "\\pi", true);
defineSymbol(math, mathord, "\u03C1", "\\rho", true);
defineSymbol(math, mathord, "\u03C3", "\\sigma", true);
defineSymbol(math, mathord, "\u03C4", "\\tau", true);
defineSymbol(math, mathord, "\u03C5", "\\upsilon", true);
defineSymbol(math, mathord, "\u03D5", "\\phi", true);
defineSymbol(math, mathord, "\u03C7", "\\chi", true);
defineSymbol(math, mathord, "\u03C8", "\\psi", true);
defineSymbol(math, mathord, "\u03C9", "\\omega", true);
defineSymbol(math, mathord, "\u03B5", "\\varepsilon", true);
defineSymbol(math, mathord, "\u03D1", "\\vartheta", true);
defineSymbol(math, mathord, "\u03D6", "\\varpi", true);
defineSymbol(math, mathord, "\u03F1", "\\varrho", true);
defineSymbol(math, mathord, "\u03C2", "\\varsigma", true);
defineSymbol(math, mathord, "\u03C6", "\\varphi", true);
defineSymbol(math, mathord, "\u03D8", "\\Coppa", true);
defineSymbol(math, mathord, "\u03D9", "\\coppa", true);
defineSymbol(math, mathord, "\u03D9", "\\varcoppa", true);
defineSymbol(math, mathord, "\u03DE", "\\Koppa", true);
defineSymbol(math, mathord, "\u03DF", "\\koppa", true);
defineSymbol(math, mathord, "\u03E0", "\\Sampi", true);
defineSymbol(math, mathord, "\u03E1", "\\sampi", true);
defineSymbol(math, mathord, "\u03DA", "\\Stigma", true);
defineSymbol(math, mathord, "\u03DB", "\\stigma", true);
defineSymbol(math, mathord, "\u2AEB", "\\Bot");
defineSymbol(math, bin, "\u2217", "\u2217", true);
defineSymbol(math, bin, "+", "+");
defineSymbol(math, bin, "\u2217", "*");
defineSymbol(math, bin, "\u2044", "/", true);
defineSymbol(math, bin, "\u2044", "\u2044");
defineSymbol(math, bin, "\u2212", "-", true);
defineSymbol(math, bin, "\u22C5", "\\cdot", true);
defineSymbol(math, bin, "\u2218", "\\circ", true);
defineSymbol(math, bin, "\xF7", "\\div", true);
defineSymbol(math, bin, "\xB1", "\\pm", true);
defineSymbol(math, bin, "\xD7", "\\times", true);
defineSymbol(math, bin, "\u2229", "\\cap", true);
defineSymbol(math, bin, "\u222A", "\\cup", true);
defineSymbol(math, bin, "\u2216", "\\setminus", true);
defineSymbol(math, bin, "\u2227", "\\land");
defineSymbol(math, bin, "\u2228", "\\lor");
defineSymbol(math, bin, "\u2227", "\\wedge", true);
defineSymbol(math, bin, "\u2228", "\\vee", true);
defineSymbol(math, open, "\u27E6", "\\llbracket", true);
defineSymbol(math, close, "\u27E7", "\\rrbracket", true);
defineSymbol(math, open, "\u27E8", "\\langle", true);
defineSymbol(math, open, "\u27EA", "\\lAngle", true);
defineSymbol(math, open, "\u2989", "\\llangle", true);
defineSymbol(math, open, "|", "\\lvert");
defineSymbol(math, open, "\u2016", "\\lVert", true);
defineSymbol(math, textord, "!", "\\oc");
defineSymbol(math, textord, "?", "\\wn");
defineSymbol(math, textord, "\u2193", "\\shpos");
defineSymbol(math, textord, "\u2195", "\\shift");
defineSymbol(math, textord, "\u2191", "\\shneg");
defineSymbol(math, close, "?", "?");
defineSymbol(math, close, "!", "!");
defineSymbol(math, close, "\u203C", "\u203C");
defineSymbol(math, close, "\u27E9", "\\rangle", true);
defineSymbol(math, close, "\u27EB", "\\rAngle", true);
defineSymbol(math, close, "\u298A", "\\rrangle", true);
defineSymbol(math, close, "|", "\\rvert");
defineSymbol(math, close, "\u2016", "\\rVert");
defineSymbol(math, open, "\u2983", "\\lBrace", true);
defineSymbol(math, close, "\u2984", "\\rBrace", true);
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
defineSymbol(math, rel, "\uE020", "\\@not");
defineSymbol(math, rel, "\u2282", "\\subset", true);
defineSymbol(math, rel, "\u2283", "\\supset", true);
defineSymbol(math, rel, "\u2286", "\\subseteq", true);
defineSymbol(math, rel, "\u2287", "\\supseteq", true);
defineSymbol(math, rel, "\u2288", "\\nsubseteq", true);
defineSymbol(math, rel, "\u2288", "\\nsubseteqq");
defineSymbol(math, rel, "\u2289", "\\nsupseteq", true);
defineSymbol(math, rel, "\u2289", "\\nsupseteqq");
defineSymbol(math, rel, "\u22A8", "\\models");
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
defineSymbol(math, rel, "\u2AEB", "\\Perp", true);
defineSymbol(math, spacing, "\xA0", "\\ ");
defineSymbol(math, spacing, "\xA0", "\\space");
defineSymbol(math, spacing, "\xA0", "\\nobreakspace");
defineSymbol(text, spacing, "\xA0", "\\ ");
defineSymbol(text, spacing, "\xA0", " ");
defineSymbol(text, spacing, "\xA0", "\\space");
defineSymbol(text, spacing, "\xA0", "\\nobreakspace");
defineSymbol(math, spacing, null, "\\nobreak");
defineSymbol(math, spacing, null, "\\allowbreak");
defineSymbol(math, punct, ",", ",");
defineSymbol(text, punct, ":", ":");
defineSymbol(math, punct, ";", ";");
defineSymbol(math, bin, "\u22BC", "\\barwedge");
defineSymbol(math, bin, "\u22BB", "\\veebar");
defineSymbol(math, bin, "\u2299", "\\odot", true);
defineSymbol(math, bin, "\u2295\uFE0E", "\\oplus");
defineSymbol(math, bin, "\u2297", "\\otimes", true);
defineSymbol(math, textord, "\u2202", "\\partial", true);
defineSymbol(math, bin, "\u2298", "\\oslash", true);
defineSymbol(math, bin, "\u229A", "\\circledcirc", true);
defineSymbol(math, bin, "\u22A1", "\\boxdot", true);
defineSymbol(math, bin, "\u25B3", "\\bigtriangleup");
defineSymbol(math, bin, "\u25BD", "\\bigtriangledown");
defineSymbol(math, bin, "\u2020", "\\dagger");
defineSymbol(math, bin, "\u22C4", "\\diamond");
defineSymbol(math, bin, "\u25C3", "\\triangleleft");
defineSymbol(math, bin, "\u25B9", "\\triangleright");
defineSymbol(math, open, "{", "\\{");
defineSymbol(text, textord, "{", "\\{");
defineSymbol(text, textord, "{", "\\textbraceleft");
defineSymbol(math, close, "}", "\\}");
defineSymbol(text, textord, "}", "\\}");
defineSymbol(text, textord, "}", "\\textbraceright");
defineSymbol(math, open, "{", "\\lbrace");
defineSymbol(math, close, "}", "\\rbrace");
defineSymbol(math, open, "[", "\\lbrack", true);
defineSymbol(text, textord, "[", "\\lbrack", true);
defineSymbol(math, close, "]", "\\rbrack", true);
defineSymbol(text, textord, "]", "\\rbrack", true);
defineSymbol(math, open, "(", "\\lparen", true);
defineSymbol(math, close, ")", "\\rparen", true);
defineSymbol(math, open, "\u2987", "\\llparenthesis", true);
defineSymbol(math, close, "\u2988", "\\rrparenthesis", true);
defineSymbol(text, textord, "<", "\\textless", true);
defineSymbol(text, textord, ">", "\\textgreater", true);
defineSymbol(math, open, "\u230A", "\\lfloor", true);
defineSymbol(math, close, "\u230B", "\\rfloor", true);
defineSymbol(math, open, "\u2308", "\\lceil", true);
defineSymbol(math, close, "\u2309", "\\rceil", true);
defineSymbol(math, textord, "\\", "\\backslash");
defineSymbol(math, textord, "|", "|");
defineSymbol(math, textord, "|", "\\vert");
defineSymbol(text, textord, "|", "\\textbar", true);
defineSymbol(math, textord, "\u2016", "\\|");
defineSymbol(math, textord, "\u2016", "\\Vert");
defineSymbol(text, textord, "\u2016", "\\textbardbl");
defineSymbol(text, textord, "~", "\\textasciitilde");
defineSymbol(text, textord, "\\", "\\textbackslash");
defineSymbol(text, textord, "^", "\\textasciicircum");
defineSymbol(math, rel, "\u2191", "\\uparrow", true);
defineSymbol(math, rel, "\u21D1", "\\Uparrow", true);
defineSymbol(math, rel, "\u2193", "\\downarrow", true);
defineSymbol(math, rel, "\u21D3", "\\Downarrow", true);
defineSymbol(math, rel, "\u2195", "\\updownarrow", true);
defineSymbol(math, rel, "\u21D5", "\\Updownarrow", true);
defineSymbol(math, op, "\u2210", "\\coprod");
defineSymbol(math, op, "\u22C1", "\\bigvee");
defineSymbol(math, op, "\u22C0", "\\bigwedge");
defineSymbol(math, op, "\u2A04", "\\biguplus");
defineSymbol(math, op, "\u2A04", "\\bigcupplus");
defineSymbol(math, op, "\u2A03", "\\bigcupdot");
defineSymbol(math, op, "\u2A07", "\\bigdoublevee");
defineSymbol(math, op, "\u2A08", "\\bigdoublewedge");
defineSymbol(math, op, "\u22C2", "\\bigcap");
defineSymbol(math, op, "\u22C3", "\\bigcup");
defineSymbol(math, op, "\u222B", "\\int");
defineSymbol(math, op, "\u222B", "\\intop");
defineSymbol(math, op, "\u222C", "\\iint");
defineSymbol(math, op, "\u222D", "\\iiint");
defineSymbol(math, op, "\u220F", "\\prod");
defineSymbol(math, op, "\u2211", "\\sum");
defineSymbol(math, op, "\u2A02", "\\bigotimes");
defineSymbol(math, op, "\u2A01", "\\bigoplus");
defineSymbol(math, op, "\u2A00", "\\bigodot");
defineSymbol(math, op, "\u2A09", "\\bigtimes");
defineSymbol(math, op, "\u222E", "\\oint");
defineSymbol(math, op, "\u222F", "\\oiint");
defineSymbol(math, op, "\u2230", "\\oiiint");
defineSymbol(math, op, "\u2231", "\\intclockwise");
defineSymbol(math, op, "\u2232", "\\varointclockwise");
defineSymbol(math, op, "\u2A0C", "\\iiiint");
defineSymbol(math, op, "\u2A0D", "\\intbar");
defineSymbol(math, op, "\u2A0E", "\\intBar");
defineSymbol(math, op, "\u2A0F", "\\fint");
defineSymbol(math, op, "\u2A12", "\\rppolint");
defineSymbol(math, op, "\u2A13", "\\scpolint");
defineSymbol(math, op, "\u2A15", "\\pointint");
defineSymbol(math, op, "\u2A16", "\\sqint");
defineSymbol(math, op, "\u2A17", "\\intlarhk");
defineSymbol(math, op, "\u2A18", "\\intx");
defineSymbol(math, op, "\u2A19", "\\intcap");
defineSymbol(math, op, "\u2A1A", "\\intcup");
defineSymbol(math, op, "\u2A05", "\\bigsqcap");
defineSymbol(math, op, "\u2A06", "\\bigsqcup");
defineSymbol(math, op, "\u222B", "\\smallint");
defineSymbol(text, inner, "\u2026", "\\textellipsis");
defineSymbol(math, inner, "\u2026", "\\mathellipsis");
defineSymbol(text, inner, "\u2026", "\\ldots", true);
defineSymbol(math, inner, "\u2026", "\\ldots", true);
defineSymbol(math, inner, "\u22F0", "\\iddots", true);
defineSymbol(math, inner, "\u22EF", "\\@cdots", true);
defineSymbol(math, inner, "\u22F1", "\\ddots", true);
defineSymbol(math, textord, "\u22EE", "\\varvdots");
defineSymbol(text, textord, "\u22EE", "\\varvdots");
defineSymbol(math, accent, "\u02CA", "\\acute");
defineSymbol(math, accent, "`", "\\grave");
defineSymbol(math, accent, "\xA8", "\\ddot");
defineSymbol(math, accent, "\u2026", "\\dddot");
defineSymbol(math, accent, "\u2026.", "\\ddddot");
defineSymbol(math, accent, "~", "\\tilde");
defineSymbol(math, accent, "\u203E", "\\bar");
defineSymbol(math, accent, "\u02D8", "\\breve");
defineSymbol(math, accent, "\u02C7", "\\check");
defineSymbol(math, accent, "^", "\\hat");
defineSymbol(math, accent, "\u2192", "\\vec");
defineSymbol(math, accent, "\u02D9", "\\dot");
defineSymbol(math, accent, "\u02DA", "\\mathring");
defineSymbol(math, mathord, "\u0131", "\\imath", true);
defineSymbol(math, mathord, "\u0237", "\\jmath", true);
defineSymbol(math, textord, "\u0131", "\u0131");
defineSymbol(math, textord, "\u0237", "\u0237");
defineSymbol(text, textord, "\u0131", "\\i", true);
defineSymbol(text, textord, "\u0237", "\\j", true);
defineSymbol(text, textord, "\xDF", "\\ss", true);
defineSymbol(text, textord, "\xE6", "\\ae", true);
defineSymbol(text, textord, "\u0153", "\\oe", true);
defineSymbol(text, textord, "\xF8", "\\o", true);
defineSymbol(math, mathord, "\xF8", "\\o", true);
defineSymbol(text, textord, "\xC6", "\\AE", true);
defineSymbol(text, textord, "\u0152", "\\OE", true);
defineSymbol(text, textord, "\xD8", "\\O", true);
defineSymbol(math, mathord, "\xD8", "\\O", true);
defineSymbol(text, accent, "\u02CA", "\\'");
defineSymbol(text, accent, "\u02CB", "\\`");
defineSymbol(text, accent, "\u02C6", "\\^");
defineSymbol(text, accent, "\u02DC", "\\~");
defineSymbol(text, accent, "\u02C9", "\\=");
defineSymbol(text, accent, "\u02D8", "\\u");
defineSymbol(text, accent, "\u02D9", "\\.");
defineSymbol(text, accent, "\xB8", "\\c");
defineSymbol(text, accent, "\u02DA", "\\r");
defineSymbol(text, accent, "\u02C7", "\\v");
defineSymbol(text, accent, "\xA8", '\\"');
defineSymbol(text, accent, "\u02DD", "\\H");
defineSymbol(math, accent, "\u02CA", "\\'");
defineSymbol(math, accent, "\u02CB", "\\`");
defineSymbol(math, accent, "\u02C6", "\\^");
defineSymbol(math, accent, "\u02DC", "\\~");
defineSymbol(math, accent, "\u02C9", "\\=");
defineSymbol(math, accent, "\u02D8", "\\u");
defineSymbol(math, accent, "\u02D9", "\\.");
defineSymbol(math, accent, "\xB8", "\\c");
defineSymbol(math, accent, "\u02DA", "\\r");
defineSymbol(math, accent, "\u02C7", "\\v");
defineSymbol(math, accent, "\xA8", '\\"');
defineSymbol(math, accent, "\u02DD", "\\H");
var ligatures = {
  "--": true,
  "---": true,
  "``": true,
  "''": true
};
defineSymbol(text, textord, "\u2013", "--", true);
defineSymbol(text, textord, "\u2013", "\\textendash");
defineSymbol(text, textord, "\u2014", "---", true);
defineSymbol(text, textord, "\u2014", "\\textemdash");
defineSymbol(text, textord, "\u2018", "`", true);
defineSymbol(text, textord, "\u2018", "\\textquoteleft");
defineSymbol(text, textord, "\u2019", "'", true);
defineSymbol(text, textord, "\u2019", "\\textquoteright");
defineSymbol(text, textord, "\u201C", "``", true);
defineSymbol(text, textord, "\u201C", "\\textquotedblleft");
defineSymbol(text, textord, "\u201D", "''", true);
defineSymbol(text, textord, "\u201D", "\\textquotedblright");
defineSymbol(math, textord, "\xB0", "\\degree", true);
defineSymbol(text, textord, "\xB0", "\\degree");
defineSymbol(text, textord, "\xB0", "\\textdegree", true);
defineSymbol(math, textord, "\xA3", "\\pounds");
defineSymbol(math, textord, "\xA3", "\\mathsterling", true);
defineSymbol(text, textord, "\xA3", "\\pounds");
defineSymbol(text, textord, "\xA3", "\\textsterling", true);
defineSymbol(math, textord, "\u2720", "\\maltese");
defineSymbol(text, textord, "\u2720", "\\maltese");
defineSymbol(math, textord, "\u20AC", "\\euro", true);
defineSymbol(text, textord, "\u20AC", "\\euro", true);
defineSymbol(text, textord, "\u20AC", "\\texteuro");
defineSymbol(math, textord, "\xA9", "\\copyright", true);
defineSymbol(text, textord, "\xA9", "\\textcopyright");
defineSymbol(math, textord, "\u2300", "\\diameter", true);
defineSymbol(text, textord, "\u2300", "\\diameter");
defineSymbol(math, textord, "\u{1D6E4}", "\\varGamma");
defineSymbol(math, textord, "\u{1D6E5}", "\\varDelta");
defineSymbol(math, textord, "\u{1D6E9}", "\\varTheta");
defineSymbol(math, textord, "\u{1D6EC}", "\\varLambda");
defineSymbol(math, textord, "\u{1D6EF}", "\\varXi");
defineSymbol(math, textord, "\u{1D6F1}", "\\varPi");
defineSymbol(math, textord, "\u{1D6F4}", "\\varSigma");
defineSymbol(math, textord, "\u{1D6F6}", "\\varUpsilon");
defineSymbol(math, textord, "\u{1D6F7}", "\\varPhi");
defineSymbol(math, textord, "\u{1D6F9}", "\\varPsi");
defineSymbol(math, textord, "\u{1D6FA}", "\\varOmega");
defineSymbol(text, textord, "\u{1D6E4}", "\\varGamma");
defineSymbol(text, textord, "\u{1D6E5}", "\\varDelta");
defineSymbol(text, textord, "\u{1D6E9}", "\\varTheta");
defineSymbol(text, textord, "\u{1D6EC}", "\\varLambda");
defineSymbol(text, textord, "\u{1D6EF}", "\\varXi");
defineSymbol(text, textord, "\u{1D6F1}", "\\varPi");
defineSymbol(text, textord, "\u{1D6F4}", "\\varSigma");
defineSymbol(text, textord, "\u{1D6F6}", "\\varUpsilon");
defineSymbol(text, textord, "\u{1D6F7}", "\\varPhi");
defineSymbol(text, textord, "\u{1D6F9}", "\\varPsi");
defineSymbol(text, textord, "\u{1D6FA}", "\\varOmega");
var mathTextSymbols = '0123456789/@."';
for (let i = 0; i < mathTextSymbols.length; i++) {
  const ch = mathTextSymbols.charAt(i);
  defineSymbol(math, textord, ch, ch);
}
var textSymbols = '0123456789!@*()-=+";:?/.,';
for (let i = 0; i < textSymbols.length; i++) {
  const ch = textSymbols.charAt(i);
  defineSymbol(text, textord, ch, ch);
}
var letters = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz";
for (let i = 0; i < letters.length; i++) {
  const ch = letters.charAt(i);
  defineSymbol(math, mathord, ch, ch);
  defineSymbol(text, textord, ch, ch);
}
var narrow = "\xC7\xD0\xDE\xE7\xFE\u2102\u210D\u2115\u2119\u211A\u211D\u2124\u210E\u210F\u210A\u210B\u210C\u2110\u2111\u2112\u2113\u2118\u211B\u211C\u212C\u2130\u2131\u2133\u212D\u2128";
for (let i = 0; i < narrow.length; i++) {
  const ch = narrow.charAt(i);
  defineSymbol(math, mathord, ch, ch);
  defineSymbol(text, textord, ch, ch);
}
var wideChar = "";
for (let i = 0; i < letters.length; i++) {
  wideChar = String.fromCharCode(55349, 56320 + i);
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(text, textord, wideChar, wideChar);
  wideChar = String.fromCharCode(55349, 56372 + i);
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(text, textord, wideChar, wideChar);
  wideChar = String.fromCharCode(55349, 56424 + i);
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(text, textord, wideChar, wideChar);
  wideChar = String.fromCharCode(55349, 56580 + i);
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(text, textord, wideChar, wideChar);
  wideChar = String.fromCharCode(55349, 56736 + i);
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(text, textord, wideChar, wideChar);
  wideChar = String.fromCharCode(55349, 56788 + i);
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(text, textord, wideChar, wideChar);
  wideChar = String.fromCharCode(55349, 56840 + i);
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(text, textord, wideChar, wideChar);
  wideChar = String.fromCharCode(55349, 56944 + i);
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(text, textord, wideChar, wideChar);
  wideChar = String.fromCharCode(55349, 56632 + i);
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(text, textord, wideChar, wideChar);
  const ch = letters.charAt(i);
  wideChar = String.fromCharCode(55349, 56476 + i);
  defineSymbol(math, mathord, ch, wideChar);
  defineSymbol(text, textord, ch, wideChar);
}
for (let i = 0; i < 10; i++) {
  wideChar = String.fromCharCode(55349, 57294 + i);
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(text, textord, wideChar, wideChar);
  wideChar = String.fromCharCode(55349, 57314 + i);
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(text, textord, wideChar, wideChar);
  wideChar = String.fromCharCode(55349, 57324 + i);
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(text, textord, wideChar, wideChar);
  wideChar = String.fromCharCode(55349, 57334 + i);
  defineSymbol(math, mathord, wideChar, wideChar);
  defineSymbol(text, textord, wideChar, wideChar);
}
var openDelims = "([{\u230A\u2308\u27E8\u27EE\u23B0\u27E6\u2983";
var closeDelims = ")]}\u230B\u2309\u27E9\u27EF\u23B1\u27E6\u2984";
function setLineBreaks(expression, wrapMode, isDisplayMode) {
  const mtrs = [];
  let mrows = [];
  let block = [];
  let numTopLevelEquals = 0;
  let i = 0;
  let level = 0;
  while (i < expression.length) {
    while (expression[i] instanceof DocumentFragment) {
      expression.splice(i, 1, ...expression[i].children);
    }
    const node = expression[i];
    if (node.attributes && node.attributes.linebreak && node.attributes.linebreak === "newline") {
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
      continue;
    }
    block.push(node);
    if (node.type && node.type === "mo" && node.children.length === 1 && !Object.hasOwn(node.attributes, "movablelimits")) {
      const ch = node.children[0].text;
      if (openDelims.indexOf(ch) > -1) {
        level += 1;
      } else if (closeDelims.indexOf(ch) > -1) {
        level -= 1;
      } else if (level === 0 && wrapMode === "=" && ch === "=") {
        numTopLevelEquals += 1;
        if (numTopLevelEquals > 1) {
          block.pop();
          const element = new mathMLTree.MathNode("mrow", block);
          mrows.push(element);
          block = [node];
        }
      } else if (level === 0 && wrapMode === "tex" && ch !== "\u2207") {
        const next = i < expression.length - 1 ? expression[i + 1] : null;
        let glueIsFreeOfNobreak = true;
        if (!(next && next.type === "mtext" && next.attributes.linebreak && next.attributes.linebreak === "nobreak")) {
          for (let j = i + 1; j < expression.length; j++) {
            const nd = expression[j];
            if (nd.type && nd.type === "mspace" && !(nd.attributes.linebreak && nd.attributes.linebreak === "newline")) {
              block.push(nd);
              i += 1;
              if (nd.attributes && nd.attributes.linebreak && nd.attributes.linebreak === "nobreak") {
                glueIsFreeOfNobreak = false;
              }
            } else {
              break;
            }
          }
        }
        if (glueIsFreeOfNobreak) {
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
    return mtable;
  }
  return mathMLTree.newDocumentFragment(mrows);
}
var makeText = function(text2, mode, style) {
  if (symbols[mode][text2] && symbols[mode][text2].replace && text2.charCodeAt(0) !== 55349 && !(Object.prototype.hasOwnProperty.call(ligatures, text2) && style && (style.fontFamily && style.fontFamily.slice(4, 6) === "tt" || style.font && style.font.slice(4, 6) === "tt"))) {
    text2 = symbols[mode][text2].replace;
  }
  return new mathMLTree.TextNode(text2);
};
var copyChar = (newRow, child) => {
  if (newRow.children.length === 0 || newRow.children[newRow.children.length - 1].type !== "mtext") {
    const mtext = new mathMLTree.MathNode(
      "mtext",
      [new mathMLTree.TextNode(child.children[0].text)]
    );
    newRow.children.push(mtext);
  } else {
    newRow.children[newRow.children.length - 1].children[0].text += child.children[0].text;
  }
};
var consolidateText = (mrow) => {
  if (mrow.type !== "mrow" && mrow.type !== "mstyle") {
    return mrow;
  }
  if (mrow.children.length === 0) {
    return mrow;
  }
  const newRow = new mathMLTree.MathNode("mrow");
  for (let i = 0; i < mrow.children.length; i++) {
    const child = mrow.children[i];
    if (child.type === "mtext" && Object.keys(child.attributes).length === 0) {
      copyChar(newRow, child);
    } else if (child.type === "mrow") {
      let canConsolidate = true;
      for (let j = 0; j < child.children.length; j++) {
        const grandChild = child.children[j];
        if (grandChild.type !== "mtext" || Object.keys(child.attributes).length !== 0) {
          canConsolidate = false;
          break;
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
      if (mtext.children[0].text.charAt(0) === " ") {
        mtext.children[0].text = "\xA0" + mtext.children[0].text.slice(1);
      }
      const L = mtext.children[0].text.length;
      if (L > 0 && mtext.children[0].text.charAt(L - 1) === " ") {
        mtext.children[0].text = mtext.children[0].text.slice(0, -1) + "\xA0";
      }
      for (const [key, value] of Object.entries(mrow.attributes)) {
        mtext.attributes[key] = value;
      }
    }
  }
  if (newRow.children.length === 1 && newRow.children[0].type === "mtext") {
    return newRow.children[0];
  } else {
    return newRow;
  }
};
var makeRow = function(body, semisimple = false) {
  if (body.length === 1 && !(body[0] instanceof DocumentFragment)) {
    return body[0];
  } else if (!semisimple) {
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
function isNumberPunctuation(group) {
  if (!group) {
    return false;
  }
  if (group.type === "mi" && group.children.length === 1) {
    const child = group.children[0];
    return child instanceof TextNode2 && child.text === ".";
  } else if (group.type === "mtext" && group.children.length === 1) {
    const child = group.children[0];
    return child instanceof TextNode2 && child.text === "\u2008";
  } else if (group.type === "mo" && group.children.length === 1 && group.getAttribute("separator") === "true" && group.getAttribute("lspace") === "0em" && group.getAttribute("rspace") === "0em") {
    const child = group.children[0];
    return child instanceof TextNode2 && child.text === ",";
  } else {
    return false;
  }
}
var isComma = (expression, i) => {
  const node = expression[i];
  const followingNode = expression[i + 1];
  return node.type === "atom" && node.text === "," && // Don't consolidate if there is a space after the comma.
  node.loc && followingNode.loc && node.loc.end === followingNode.loc.start;
};
var isRel = (item) => {
  return item.type === "atom" && item.family === "rel" || item.type === "mclass" && item.mclass === "mrel";
};
var buildExpression = function(expression, style, semisimple = false) {
  if (!semisimple && expression.length === 1) {
    const group = buildGroup$1(expression[0], style);
    if (group instanceof MathNode && group.type === "mo") {
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
    if (i < expression.length - 1 && isRel(expression[i]) && isRel(expression[i + 1])) {
      group.setAttribute("rspace", "0em");
    }
    if (i > 0 && isRel(expression[i]) && isRel(expression[i - 1])) {
      group.setAttribute("lspace", "0em");
    }
    if (group.type === "mn" && lastGroup && lastGroup.type === "mn") {
      lastGroup.children.push(...group.children);
      continue;
    } else if (isNumberPunctuation(group) && lastGroup && lastGroup.type === "mn") {
      lastGroup.children.push(...group.children);
      continue;
    } else if (lastGroup && lastGroup.type === "mn" && i < groupArray.length - 1 && groupArray[i + 1].type === "mn" && isComma(expression, i)) {
      lastGroup.children.push(...group.children);
      continue;
    } else if (group.type === "mn" && isNumberPunctuation(lastGroup)) {
      group.children = [...lastGroup.children, ...group.children];
      groups.pop();
    } else if ((group.type === "msup" || group.type === "msub") && group.children.length >= 1 && lastGroup && (lastGroup.type === "mn" || isNumberPunctuation(lastGroup))) {
      const base = group.children[0];
      if (base instanceof MathNode && base.type === "mn" && lastGroup) {
        base.children = [...lastGroup.children, ...base.children];
        groups.pop();
      }
    }
    groups.push(group);
    lastGroup = group;
  }
  return groups;
};
var buildExpressionRow = function(expression, style, semisimple = false) {
  return makeRow(buildExpression(expression, style, semisimple), semisimple);
};
var buildGroup$1 = function(group, style) {
  if (!group) {
    return new mathMLTree.MathNode("mrow");
  }
  if (_mathmlGroupBuilders[group.type]) {
    const result = _mathmlGroupBuilders[group.type](group, style);
    return result;
  } else {
    throw new ParseError("Got group of unknown type: '" + group.type + "'");
  }
};
var glue$1 = (_) => {
  return new mathMLTree.MathNode("mtd", [], [], { padding: "0", width: "50%" });
};
var labelContainers = ["mrow", "mtd", "mtable", "mtr"];
var getLabel = (parent) => {
  for (const node of parent.children) {
    if (node.type && labelContainers.includes(node.type)) {
      if (node.classes && node.classes[0] === "tml-label") {
        const label = node.label;
        return label;
      } else {
        const label = getLabel(node);
        if (label) {
          return label;
        }
      }
    } else if (!node.type) {
      const label = getLabel(node);
      if (label) {
        return label;
      }
    }
  }
};
var taggedExpression = (expression, tag, style, leqno) => {
  tag = buildExpressionRow(tag[0].body, style);
  tag = consolidateText(tag);
  tag.classes.push("tml-tag");
  const label = getLabel(expression);
  expression = new mathMLTree.MathNode("mtd", [expression]);
  const rowArray = [glue$1(), expression, glue$1()];
  rowArray[leqno ? 0 : 2].classes.push(leqno ? "tml-left" : "tml-right");
  rowArray[leqno ? 0 : 2].children.push(tag);
  const mtr = new mathMLTree.MathNode("mtr", rowArray, ["tml-tageqn"]);
  if (label) {
    mtr.setAttribute("id", label);
  }
  const table = new mathMLTree.MathNode("mtable", [mtr]);
  table.style.width = "100%";
  table.setAttribute("displaystyle", "true");
  return table;
};
function buildMathML(tree, texExpression, style, settings) {
  let tag = null;
  if (tree.length === 1 && tree[0].type === "tag") {
    tag = tree[0].tag;
    tree = tree[0].body;
  }
  const expression = buildExpression(tree, style);
  if (expression.length === 1 && expression[0] instanceof AnchorNode) {
    return expression[0];
  }
  const wrap = settings.displayMode || settings.annotate ? "none" : settings.wrap;
  const n1 = expression.length === 0 ? null : expression[0];
  let wrapper = expression.length === 1 && tag === null && n1 instanceof MathNode ? expression[0] : setLineBreaks(expression, wrap, settings.displayMode);
  if (tag) {
    wrapper = taggedExpression(wrapper, tag, style, settings.leqno);
  }
  if (settings.annotate) {
    const annotation = new mathMLTree.MathNode(
      "annotation",
      [new mathMLTree.TextNode(texExpression)]
    );
    annotation.setAttribute("encoding", "application/x-tex");
    wrapper = new mathMLTree.MathNode("semantics", [wrapper, annotation]);
  }
  const math2 = new mathMLTree.MathNode("math", [wrapper]);
  if (settings.xml) {
    math2.setAttribute("xmlns", "http://www.w3.org/1998/Math/MathML");
  }
  if (wrapper.style.width) {
    math2.style.width = "100%";
  }
  if (settings.displayMode) {
    math2.setAttribute("display", "block");
    math2.style.display = "block math";
    math2.classes = ["tml-display"];
  }
  return math2;
}
var smalls = "aceg\u0131\u0237mnopqrsuvwxyz\u03B1\u03B3\u03B5\u03B7\u03B9\u03BA\u03BC\u03BD\u03BF\u03C0\u03C1\u03C2\u03C3\u03C4\u03C5\u03C7\u03C9\u03D5\u{1D41A}\u{1D41C}\u{1D41E}\u{1D420}\u{1D426}\u{1D427}\u{1D428}\u{1D429}\u{1D42A}\u{1D42B}\u{1D42C}\u{1D42E}\u{1D42F}\u{1D430}\u{1D431}\u{1D432}\u{1D433}";
var talls = "ABCDEFGHIJKLMNOPQRSTUVWXYZbdfhklt\u0391\u0392\u0393\u0394\u0395\u0396\u0397\u0398\u0399\u039A\u039B\u039C\u039D\u039E\u039F\u03A0\u03A1\u03A3\u03A4\u03A5\u03A6\u03A7\u03A8\u03A9\u03B2\u03B4\u03BB\u03B6\u03C6\u03B8\u03C8\u{1D400}\u{1D401}\u{1D402}\u{1D403}\u{1D404}\u{1D405}\u{1D406}\u{1D407}\u{1D408}\u{1D409}\u{1D40A}\u{1D40B}\u{1D40C}\u{1D40D}\u{1D40E}\u{1D40F}\u{1D410}\u{1D411}\u{1D412}\u{1D413}\u{1D414}\u{1D415}\u{1D416}\u{1D417}\u{1D418}\u{1D419}\u{1D41B}\u{1D41D}\u{1D41F}\u{1D421}\u{1D424}\u{1D425}\u{1D42D}";
var longSmalls = /* @__PURE__ */ new Set([
  "\\alpha",
  "\\gamma",
  "\\delta",
  "\\epsilon",
  "\\eta",
  "\\iota",
  "\\kappa",
  "\\mu",
  "\\nu",
  "\\pi",
  "\\rho",
  "\\sigma",
  "\\tau",
  "\\upsilon",
  "\\chi",
  "\\psi",
  "\\omega",
  "\\imath",
  "\\jmath"
]);
var longTalls = /* @__PURE__ */ new Set([
  "\\Gamma",
  "\\Delta",
  "\\Sigma",
  "\\Omega",
  "\\beta",
  "\\delta",
  "\\lambda",
  "\\theta",
  "\\psi"
]);
var mathmlBuilder$a = (group, style) => {
  const accentNode2 = group.isStretchy ? stretchy.accentNode(group) : new mathMLTree.MathNode("mo", [makeText(group.label, group.mode)]);
  if (group.label === "\\vec") {
    accentNode2.style.transform = "scale(0.75) translate(10%, 30%)";
  } else {
    accentNode2.style.mathStyle = "normal";
    accentNode2.style.mathDepth = "0";
    if (needWebkitShift.has(group.label) && utils.isCharacterBox(group.base)) {
      let shift = "";
      const ch = group.base.text;
      if (smalls.indexOf(ch) > -1 || longSmalls.has(ch)) {
        shift = "tml-xshift";
      }
      if (talls.indexOf(ch) > -1 || longTalls.has(ch)) {
        shift = "tml-capshift";
      }
      if (shift) {
        accentNode2.classes.push(shift);
      }
    }
  }
  if (!group.isStretchy) {
    accentNode2.setAttribute("stretchy", "false");
  }
  const node = new mathMLTree.MathNode(
    group.label === "\\c" ? "munder" : "mover",
    [buildGroup$1(group.base, style), accentNode2]
  );
  return node;
};
var nonStretchyAccents = /* @__PURE__ */ new Set([
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
var needWebkitShift = /* @__PURE__ */ new Set([
  "\\acute",
  "\\bar",
  "\\breve",
  "\\check",
  "\\dot",
  "\\ddot",
  "\\grave",
  "\\hat",
  "\\mathring",
  "\\'",
  "\\^",
  "\\~",
  "\\=",
  "\\u",
  "\\.",
  '\\"',
  "\\r",
  "\\H",
  "\\v"
]);
var combiningChar = {
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
      isStretchy,
      base
    };
  },
  mathmlBuilder: mathmlBuilder$a
});
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
      console.log(`Temml parse error: Command ${context.funcName} is invalid in math mode.`);
    }
    if (mode === "text" && base.text && base.text.length === 1 && context.funcName in combiningChar && smalls.indexOf(base.text) > -1) {
      return {
        type: "textord",
        mode: "text",
        text: base.text + combiningChar[context.funcName]
      };
    } else {
      return {
        type: "accent",
        mode,
        label: context.funcName,
        isStretchy: false,
        base
      };
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
      base
    };
  },
  mathmlBuilder: (group, style) => {
    const accentNode2 = stretchy.accentNode(group);
    accentNode2.style["math-depth"] = 0;
    const node = new mathMLTree.MathNode("munder", [
      buildGroup$1(group.base, style),
      accentNode2
    ]);
    return node;
  }
});
var ptPerUnit = {
  // Convert to CSS (Postscipt) points, not TeX points
  // https://en.wikibooks.org/wiki/LaTeX/Lengths and
  // https://tex.stackexchange.com/a/8263
  pt: 800 / 803,
  // convert TeX point to CSS (Postscript) point
  pc: 12 * 800 / 803,
  // pica
  dd: 1238 / 1157 * 800 / 803,
  // didot
  cc: 14856 / 1157 * 800 / 803,
  // cicero (12 didot)
  nd: 685 / 642 * 800 / 803,
  // new didot
  nc: 1370 / 107 * 800 / 803,
  // new cicero (12 new didot)
  sp: 1 / 65536 * 800 / 803,
  // scaled point (TeX's internal smallest unit)
  mm: 25.4 / 72,
  cm: 2.54 / 72,
  in: 1 / 72,
  px: 96 / 72
};
var validUnits = [
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
var validUnit = function(unit) {
  if (typeof unit !== "string") {
    unit = unit.unit;
  }
  return validUnits.indexOf(unit) > -1;
};
var emScale = (styleLevel) => {
  const scriptLevel2 = Math.max(styleLevel - 1, 0);
  return [1, 0.7, 0.5][scriptLevel2];
};
var calculateSize = function(sizeValue, style) {
  let number = sizeValue.number;
  if (style.maxSize[0] < 0 && number > 0) {
    return { number: 0, unit: "em" };
  }
  const unit = sizeValue.unit;
  switch (unit) {
    case "mm":
    case "cm":
    case "in":
    case "px": {
      const numInCssPts = number * ptPerUnit[unit];
      if (numInCssPts > style.maxSize[1]) {
        return { number: style.maxSize[1], unit: "pt" };
      }
      return { number, unit };
    }
    case "em":
    case "ex": {
      if (unit === "ex") {
        number *= 0.431;
      }
      number = Math.min(number / emScale(style.level), style.maxSize[0]);
      return { number: utils.round(number), unit: "em" };
    }
    case "bp": {
      if (number > style.maxSize[1]) {
        number = style.maxSize[1];
      }
      return { number, unit: "pt" };
    }
    case "pt":
    case "pc":
    case "dd":
    case "cc":
    case "nd":
    case "nc":
    case "sp": {
      number = Math.min(number * ptPerUnit[unit], style.maxSize[1]);
      return { number: utils.round(number), unit: "pt" };
    }
    case "mu": {
      number = Math.min(number / 18, style.maxSize[0]);
      return { number: utils.round(number), unit: "em" };
    }
    default:
      throw new ParseError("Invalid unit: '" + unit + "'");
  }
};
var padding$2 = (width) => {
  const node = new mathMLTree.MathNode("mspace");
  node.setAttribute("width", width + "em");
  return node;
};
var paddedNode = (group, lspace = 0.3, rspace = 0, mustSmash = false) => {
  if (group == null && rspace === 0) {
    return padding$2(lspace);
  }
  const row = group ? [group] : [];
  if (lspace !== 0) {
    row.unshift(padding$2(lspace));
  }
  if (rspace > 0) {
    row.push(padding$2(rspace));
  }
  if (mustSmash) {
    const mpadded = new mathMLTree.MathNode("mpadded", row);
    mpadded.setAttribute("height", "0");
    return mpadded;
  } else {
    return new mathMLTree.MathNode("mrow", row);
  }
};
var labelSize = (size, scriptLevel2) => Number(size) / emScale(scriptLevel2);
var munderoverNode = (fName, body, below, style) => {
  const arrowNode = stretchy.mathMLnode(fName);
  const isEq = fName.slice(1, 3) === "eq";
  const minWidth = fName.charAt(1) === "x" ? "1.75" : fName.slice(2, 4) === "cd" ? "3.0" : isEq ? "1.0" : "2.0";
  arrowNode.setAttribute("lspace", "0");
  arrowNode.setAttribute("rspace", isEq ? "0.5em" : "0");
  const labelStyle = style.withLevel(style.level < 2 ? 2 : 3);
  const minArrowWidth = labelSize(minWidth, labelStyle.level);
  const dummyWidth = labelSize(minWidth, 3);
  const emptyLabel = paddedNode(null, minArrowWidth.toFixed(4), 0);
  const dummyNode = paddedNode(null, dummyWidth.toFixed(4), 0);
  const space = labelSize(isEq ? 0 : 0.3, labelStyle.level).toFixed(4);
  let upperNode;
  let lowerNode;
  const gotUpper = body && body.body && // \hphantom        visible content
  (body.body.body || body.body.length > 0);
  if (gotUpper) {
    let label = buildGroup$1(body, labelStyle);
    const mustSmash = fName === "\\\\cdrightarrow" || fName === "\\\\cdleftarrow";
    label = paddedNode(label, space, space, mustSmash);
    upperNode = new mathMLTree.MathNode("mover", [label, dummyNode]);
  }
  const gotLower = below && below.body && (below.body.body || below.body.length > 0);
  if (gotLower) {
    let label = buildGroup$1(below, labelStyle);
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
  if (minWidth === "3.0") {
    node.style.height = "1em";
  }
  node.setAttribute("accent", "false");
  return node;
};
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
    const node = munderoverNode(group.name, group.body, group.below, style);
    const row = [node];
    row.unshift(padding$2(0.2778));
    row.push(padding$2(0.2778));
    return new mathMLTree.MathNode("mrow", row);
  }
});
var arrowComponent = {
  "\\xtofrom": ["\\xrightarrow", "\\xleftarrow"],
  "\\xleftrightharpoons": ["\\xleftharpoonup", "\\xrightharpoondown"],
  "\\xrightleftharpoons": ["\\xrightharpoonup", "\\xleftharpoondown"],
  "\\yieldsLeftRight": ["\\yields", "\\yieldsLeft"],
  // The next three all get the same harpoon glyphs. Only the lengths and paddings differ.
  "\\equilibrium": ["\\longrightharpoonup", "\\longleftharpoondown"],
  "\\equilibriumRight": ["\\longrightharpoonup", "\\eqleftharpoondown"],
  "\\equilibriumLeft": ["\\eqrightharpoonup", "\\longleftharpoondown"]
};
defineFunction({
  type: "stackedArrow",
  names: [
    "\\xtofrom",
    // expfeil
    "\\xleftrightharpoons",
    // mathtools
    "\\xrightleftharpoons",
    // mathtools
    "\\yieldsLeftRight",
    // mhchem
    "\\equilibrium",
    // mhchem
    "\\equilibriumRight",
    "\\equilibriumLeft"
  ],
  props: {
    numArgs: 1,
    numOptionalArgs: 1
  },
  handler({ parser, funcName }, args, optArgs) {
    const lowerArrowBody = args[0] ? {
      type: "hphantom",
      mode: parser.mode,
      body: args[0]
    } : null;
    const upperArrowBelow = optArgs[0] ? {
      type: "hphantom",
      mode: parser.mode,
      body: optArgs[0]
    } : null;
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
    if (group.name === "\\equilibriumLeft") {
      const botNode = new mathMLTree.MathNode("mpadded", [botArrow]);
      botNode.setAttribute("width", "0.5em");
      wrapper = new mathMLTree.MathNode(
        "mpadded",
        [padding$2(0.2778), botNode, raiseNode, padding$2(0.2778)]
      );
    } else {
      raiseNode.setAttribute("width", group.name === "\\equilibriumRight" ? "0.5em" : "0");
      wrapper = new mathMLTree.MathNode(
        "mpadded",
        [padding$2(0.2778), raiseNode, botArrow, padding$2(0.2778)]
      );
    }
    wrapper.setAttribute("voffset", "-0.18em");
    wrapper.setAttribute("height", "-0.18em");
    wrapper.setAttribute("depth", "+0.18em");
    return wrapper;
  }
});
function assertNodeType(node, type) {
  if (!node || node.type !== type) {
    throw new Error(
      `Expected node of type ${type}, but got ` + (node ? `node of type ${node.type}` : String(node))
    );
  }
  return node;
}
function assertSymbolNodeType(node) {
  const typedNode = checkSymbolNodeType(node);
  if (!typedNode) {
    throw new Error(
      `Expected node of symbol group type, but got ` + (node ? `node of type ${node.type}` : String(node))
    );
  }
  return typedNode;
}
function checkSymbolNodeType(node) {
  if (node && (node.type === "atom" || Object.prototype.hasOwnProperty.call(NON_ATOMS, node.type))) {
    return node;
  }
  return null;
}
var cdArrowFunctionName = {
  ">": "\\\\cdrightarrow",
  "<": "\\\\cdleftarrow",
  "=": "\\\\cdlongequal",
  A: "\\uparrow",
  V: "\\downarrow",
  "|": "\\Vert",
  ".": "no arrow"
};
var newCell = () => {
  return { type: "styling", body: [], mode: "math", scriptLevel: "display" };
};
var isStartOfArrow = (node) => {
  return node.type === "textord" && node.text === "@";
};
var isLabelEnd = (node, endChar) => {
  return (node.type === "mathord" || node.type === "atom") && node.text === endChar;
};
function cdArrow(arrowChar, labels, parser) {
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
  const parsedRows = [];
  parser.gullet.beginGroup();
  parser.gullet.macros.set("\\cr", "\\\\\\relax");
  parser.gullet.beginGroup();
  while (true) {
    parsedRows.push(parser.parseExpression(false, "\\\\"));
    parser.gullet.endGroup();
    parser.gullet.beginGroup();
    const next = parser.fetch().text;
    if (next === "&" || next === "\\\\") {
      parser.consume();
    } else if (next === "\\end") {
      if (parsedRows[parsedRows.length - 1].length === 0) {
        parsedRows.pop();
      }
      break;
    } else {
      throw new ParseError("Expected \\\\ or \\cr or \\end", parser.nextToken);
    }
  }
  let row = [];
  const body = [row];
  for (let i = 0; i < parsedRows.length; i++) {
    const rowNodes = parsedRows[i];
    let cell = newCell();
    for (let j = 0; j < rowNodes.length; j++) {
      if (!isStartOfArrow(rowNodes[j])) {
        cell.body.push(rowNodes[j]);
      } else {
        row.push(cell);
        j += 1;
        const arrowChar = assertSymbolNodeType(rowNodes[j]).text;
        const labels = new Array(2);
        labels[0] = { type: "ordgroup", mode: "math", body: [] };
        labels[1] = { type: "ordgroup", mode: "math", body: [] };
        if ("=|.".indexOf(arrowChar) > -1) ;
        else if ("<>AV".indexOf(arrowChar) > -1) {
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
              throw new ParseError(
                "Missing a " + arrowChar + " character to complete a CD arrow.",
                rowNodes[j]
              );
            }
          }
        } else {
          throw new ParseError(`Expected one of "<>AV=|." after @.`);
        }
        const arrow = cdArrow(arrowChar, labels, parser);
        row.push(arrow);
        cell = newCell();
      }
    }
    if (i % 2 === 0) {
      row.push(cell);
    } else {
      row.shift();
    }
    row = [];
    body.push(row);
  }
  body.pop();
  parser.gullet.endGroup();
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
      return new mathMLTree.MathNode("mrow", style);
    }
    const mtd = new mathMLTree.MathNode("mtd", [buildGroup$1(group.label, style)]);
    mtd.style.padding = "0";
    const mtr = new mathMLTree.MathNode("mtr", [mtd]);
    const mtable = new mathMLTree.MathNode("mtable", [mtr]);
    const label = new mathMLTree.MathNode("mpadded", [mtable]);
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
      throw new ParseError(`\\@char has non-numeric argument ${number}`, token);
    }
    return {
      type: "textord",
      mode: parser.mode,
      text: String.fromCodePoint(code)
    };
  }
});
var htmlRegEx = /^(#[a-f0-9]{3}|#?[a-f0-9]{6})$/i;
var htmlOrNameRegEx = /^(#[a-f0-9]{3}|#?[a-f0-9]{6}|[a-z]+)$/i;
var RGBregEx = /^ *\d{1,3} *(?:, *\d{1,3} *){2}$/;
var rgbRegEx = /^ *[10](?:\.\d*)? *(?:, *[10](?:\.\d*)? *){2}$/;
var xcolorHtmlRegEx = /^[a-f0-9]{6}$/i;
var toHex = (num) => {
  let str = num.toString(16);
  if (str.length === 1) {
    str = "0" + str;
  }
  return str;
};
var xcolors = JSON.parse(`{
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
var colorFromSpec = (model, spec) => {
  let color = "";
  if (model === "HTML") {
    if (!htmlRegEx.test(spec)) {
      throw new ParseError("Invalid HTML input.");
    }
    color = spec;
  } else if (model === "RGB") {
    if (!RGBregEx.test(spec)) {
      throw new ParseError("Invalid RGB input.");
    }
    spec.split(",").map((e) => {
      color += toHex(Number(e.trim()));
    });
  } else {
    if (!rgbRegEx.test(spec)) {
      throw new ParseError("Invalid rbg input.");
    }
    spec.split(",").map((e) => {
      const num = Number(e.trim());
      if (num > 1) {
        throw new ParseError("Color rgb input must be < 1.");
      }
      color += toHex(Number((num * 255).toFixed(0)));
    });
  }
  if (color.charAt(0) !== "#") {
    color = "#" + color;
  }
  return color;
};
var validateColor = (color, macros2, token) => {
  const macroName = `\\\\color@${color}`;
  const match = htmlOrNameRegEx.exec(color);
  if (!match) {
    throw new ParseError("Invalid color: '" + color + "'", token);
  }
  if (xcolorHtmlRegEx.test(color)) {
    return "#" + color;
  } else if (color.charAt(0) === "#") {
    return color;
  } else if (macros2.has(macroName)) {
    color = macros2.get(macroName).tokens[0].text;
  } else if (xcolors[color]) {
    color = xcolors[color];
  }
  return color;
};
var mathmlBuilder$9 = (group, style) => {
  let expr = buildExpression(group.body, style.withColor(group.color));
  expr = expr.map((e) => {
    e.style.color = group.color;
    return e;
  });
  return mathMLTree.newDocumentFragment(expr);
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
    };
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
    const body = parser.parseExpression(true, breakOnTokenText, true);
    return {
      type: "color",
      mode: parser.mode,
      color,
      isTextColor: false,
      body
    };
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
      throw new ParseError("Color name must be latin letters.", token);
    }
    const model = assertNodeType(args[1], "raw").string;
    if (!["HTML", "RGB", "rgb"].includes(model)) {
      throw new ParseError("Color model must be HTML, RGB, or rgb.", token);
    }
    const spec = assertNodeType(args[2], "raw").string;
    const color = colorFromSpec(model, spec);
    parser.gullet.macros.set(`\\\\color@${name}`, { tokens: [{ text: color }], numArgs: 0 });
    return { type: "internal", mode: parser.mode };
  }
  // No mathmlBuilder. The point of \definecolor is to set a macro.
});
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
    };
  },
  // The following builder is called only at the top level,
  // not within tabular/array environments.
  mathmlBuilder(group, style) {
    const node = new mathMLTree.MathNode("mo");
    if (group.newLine) {
      node.setAttribute("linebreak", "newline");
      if (group.size) {
        const size = calculateSize(group.size, style);
        node.setAttribute("height", size.number + size.unit);
      }
    }
    return node;
  }
});
var globalMap = {
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
var checkControlSequence = (tok) => {
  const name = tok.text;
  if (/^(?:[\\{}$&#^_]|EOF)$/.test(name)) {
    throw new ParseError("Expected a control sequence", tok);
  }
  return name;
};
var getRHS = (parser) => {
  let tok = parser.gullet.popToken();
  if (tok.text === "=") {
    tok = parser.gullet.popToken();
    if (tok.text === " ") {
      tok = parser.gullet.popToken();
    }
  }
  return tok;
};
var letCommand = (parser, name, tok, global) => {
  let macro = parser.gullet.macros.get(tok.text);
  if (macro == null) {
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
defineFunction({
  type: "internal",
  names: [
    "\\global",
    "\\long",
    "\\\\globallong"
    // can’t be entered directly
  ],
  props: {
    numArgs: 0,
    allowedInText: true
  },
  handler({ parser, funcName }) {
    parser.consumeSpaces();
    const token = parser.fetch();
    if (globalMap[token.text]) {
      if (funcName === "\\global" || funcName === "\\\\globallong") {
        token.text = globalMap[token.text];
      }
      return assertNodeType(parser.parseFunction(), "internal");
    }
    throw new ParseError(`Invalid token after macro prefix`, token);
  }
});
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
    const delimiters2 = [[]];
    while (parser.gullet.future().text !== "{") {
      tok = parser.gullet.popToken();
      if (tok.text === "#") {
        if (parser.gullet.future().text === "{") {
          insert = parser.gullet.future();
          delimiters2[numArgs].push("{");
          break;
        }
        tok = parser.gullet.popToken();
        if (!/^[1-9]$/.test(tok.text)) {
          throw new ParseError(`Invalid argument number "${tok.text}"`);
        }
        if (parseInt(tok.text) !== numArgs + 1) {
          throw new ParseError(`Argument number "${tok.text}" out of order`);
        }
        numArgs++;
        delimiters2.push([]);
      } else if (tok.text === "EOF") {
        throw new ParseError("Expected a macro definition");
      } else {
        delimiters2[numArgs].push(tok.text);
      }
    }
    let { tokens } = parser.gullet.consumeArg();
    if (insert) {
      tokens.unshift(insert);
    }
    if (funcName === "\\edef" || funcName === "\\xdef") {
      tokens = parser.gullet.expandTokens(tokens);
      if (tokens.length > parser.gullet.settings.maxExpand) {
        throw new ParseError("Too many expansions in an " + funcName);
      }
      tokens.reverse();
    }
    parser.gullet.macros.set(
      name,
      { tokens, numArgs, delimiters: delimiters2 },
      funcName === globalMap[funcName]
    );
    return { type: "internal", mode: parser.mode };
  }
});
defineFunction({
  type: "internal",
  names: [
    "\\let",
    "\\\\globallet"
    // can’t be entered directly
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
defineFunction({
  type: "internal",
  names: [
    "\\futurelet",
    "\\\\globalfuture"
    // can’t be entered directly
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
      let tok2 = parser.gullet.popToken();
      tok2 = parser.gullet.popToken();
      if (!/^[0-9]$/.test(tok2.text)) {
        throw new ParseError(`Invalid number of arguments: "${tok2.text}"`);
      }
      numArgs = parseInt(tok2.text);
      tok2 = parser.gullet.popToken();
      if (tok2.text !== "]") {
        throw new ParseError(`Invalid argument "${tok2.text}"`);
      }
    }
    const { tokens } = parser.gullet.consumeArg();
    if (!(funcName === "\\providecommand" && parser.gullet.macros.has(name))) {
      parser.gullet.macros.set(
        name,
        { tokens, numArgs }
      );
    }
    return { type: "internal", mode: parser.mode };
  }
});
var delimiterSizes = {
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
var delimiters = [
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
  "\u2987",
  "\\llparenthesis",
  "\u2988",
  "\\rrparenthesis",
  "\\lfloor",
  "\\rfloor",
  "\u230A",
  "\u230B",
  "\\lceil",
  "\\rceil",
  "\u2308",
  "\u2309",
  "<",
  ">",
  "\\langle",
  "\u27E8",
  "\\rangle",
  "\u27E9",
  "\\lAngle",
  "\u27EA",
  "\\rAngle",
  "\u27EB",
  "\\llangle",
  "\u2989",
  "\\rrangle",
  "\u298A",
  "\\lt",
  "\\gt",
  "\\lvert",
  "\\rvert",
  "\\lVert",
  "\\rVert",
  "\\lgroup",
  "\\rgroup",
  "\u27EE",
  "\u27EF",
  "\\lmoustache",
  "\\rmoustache",
  "\u23B0",
  "\u23B1",
  "\\llbracket",
  "\\rrbracket",
  "\u27E6",
  "\u27E6",
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
var dels = ["}", "\\left", "\\middle", "\\right"];
var isDelimiter = (str) => str.length > 0 && (delimiters.includes(str) || delimiterSizes[str] || dels.includes(str));
var sizeToMaxHeight = [0, 1.2, 1.8, 2.4, 3];
function checkDelimiter(delim, context) {
  const symDelim = checkSymbolNodeType(delim);
  if (symDelim && delimiters.includes(symDelim.text)) {
    if (["<", "\\lt"].includes(symDelim.text)) {
      symDelim.text = "\u27E8";
    }
    if ([">", "\\gt"].includes(symDelim.text)) {
      symDelim.text = "\u27E9";
    }
    return symDelim;
  } else if (symDelim) {
    throw new ParseError(`Invalid delimiter '${symDelim.text}' after '${context.funcName}'`, delim);
  } else {
    throw new ParseError(`Invalid delimiter type '${delim.type}'`, delim);
  }
}
var needExplicitStretch = ["/", "\\", "\\backslash", "\\vert", "|"];
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
    if (group.delim === ".") {
      group.delim = "";
    }
    children.push(makeText(group.delim, group.mode));
    const node = new mathMLTree.MathNode("mo", children);
    if (group.mclass === "mopen" || group.mclass === "mclose") {
      node.setAttribute("fence", "true");
    } else {
      node.setAttribute("fence", "false");
    }
    if (needExplicitStretch.includes(group.delim) || group.delim.indexOf("arrow") > -1) {
      node.setAttribute("stretchy", "true");
    }
    node.setAttribute("symmetric", "true");
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
    ++parser.leftrightDepth;
    let body = parser.parseExpression(false, null, true);
    let nextToken = parser.fetch();
    while (nextToken.text === "\\middle") {
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
    const inner2 = buildExpression(group.body, style);
    if (group.left === ".") {
      group.left = "";
    }
    const leftNode = new mathMLTree.MathNode("mo", [makeText(group.left, group.mode)]);
    leftNode.setAttribute("fence", "true");
    leftNode.setAttribute("form", "prefix");
    if (group.left === "/" || group.left === "\\" || group.left.indexOf("arrow") > -1) {
      leftNode.setAttribute("stretchy", "true");
    }
    inner2.unshift(leftNode);
    if (group.right === ".") {
      group.right = "";
    }
    const rightNode = new mathMLTree.MathNode("mo", [makeText(group.right, group.mode)]);
    rightNode.setAttribute("fence", "true");
    rightNode.setAttribute("form", "postfix");
    if (group.right === "\u2216" || group.right.indexOf("arrow") > -1) {
      rightNode.setAttribute("stretchy", "true");
    }
    if (group.body.length > 0) {
      const lastElement = group.body[group.body.length - 1];
      if (lastElement.type === "color" && !lastElement.isTextColor) {
        rightNode.setAttribute("mathcolor", lastElement.color);
      }
    }
    inner2.push(rightNode);
    return makeRow(inner2);
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
    middleNode.setAttribute("form", "prefix");
    middleNode.setAttribute("lspace", "0.05em");
    middleNode.setAttribute("rspace", "0.05em");
    return middleNode;
  }
});
var padding$1 = (_) => {
  const node = new mathMLTree.MathNode("mspace");
  node.setAttribute("width", "3pt");
  return node;
};
var mathmlBuilder$8 = (group, style) => {
  let node;
  if (group.label.indexOf("colorbox") > -1 || group.label === "\\boxed") {
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
      node.setAttribute("notation", "top");
      node.classes.push("tml-overline");
      break;
    case "\\underline":
      node.setAttribute("notation", "bottom");
      node.classes.push("tml-underline");
      break;
    case "\\cancel":
      node.setAttribute("notation", "updiagonalstrike");
      node.children.push(new mathMLTree.MathNode("mrow", [], ["tml-cancel", "upstrike"]));
      break;
    case "\\bcancel":
      node.setAttribute("notation", "downdiagonalstrike");
      node.children.push(new mathMLTree.MathNode("mrow", [], ["tml-cancel", "downstrike"]));
      break;
    case "\\sout":
      node.setAttribute("notation", "horizontalstrike");
      node.children.push(new mathMLTree.MathNode("mrow", [], ["tml-cancel", "sout"]));
      break;
    case "\\xcancel":
      node.setAttribute("notation", "updiagonalstrike downdiagonalstrike");
      node.classes.push("tml-xcancel");
      break;
    case "\\longdiv":
      node.setAttribute("notation", "longdiv");
      node.classes.push("longdiv-top");
      node.children.push(new mathMLTree.MathNode("mrow", [], ["longdiv-arc"]));
      break;
    case "\\phase":
      node.setAttribute("notation", "phasorangle");
      node.classes.push("phasor-bottom");
      node.children.push(new mathMLTree.MathNode("mrow", [], ["phasor-angle"]));
      break;
    case "\\textcircled":
      node.setAttribute("notation", "circle");
      node.classes.push("circle-pad");
      node.children.push(new mathMLTree.MathNode("mrow", [], ["textcircle"]));
      break;
    case "\\angl":
      node.setAttribute("notation", "actuarial");
      node.classes.push("actuarial");
      break;
    case "\\boxed":
      node.setAttribute("notation", "box");
      node.classes.push("tml-box");
      node.setAttribute("scriptlevel", "0");
      node.setAttribute("displaystyle", "true");
      break;
    case "\\fbox":
      node.setAttribute("notation", "box");
      node.classes.push("tml-fbox");
      break;
    case "\\fcolorbox":
    case "\\colorbox": {
      const style2 = { padding: "3pt 0 3pt 0" };
      if (group.label === "\\fcolorbox") {
        style2.border = "0.0667em solid " + String(group.borderColor);
      }
      node.style = style2;
      break;
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
  names: [
    "\\angl",
    "\\cancel",
    "\\bcancel",
    "\\xcancel",
    "\\sout",
    "\\overline",
    "\\boxed",
    "\\longdiv",
    "\\phase"
  ],
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
var _environments = {};
function defineEnvironment({ type, names, props, handler, mathmlBuilder: mathmlBuilder2 }) {
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
  if (mathmlBuilder2) {
    _mathmlGroupBuilders[type] = mathmlBuilder2;
  }
}
var SourceLocation = class _SourceLocation {
  constructor(lexer, start, end) {
    this.lexer = lexer;
    this.start = start;
    this.end = end;
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
      return new _SourceLocation(first.loc.lexer, first.loc.start, second.loc.end);
    }
  }
};
var Token = class _Token {
  constructor(text2, loc) {
    this.text = text2;
    this.loc = loc;
  }
  /**
   * Given a pair of tokens (this and endToken), compute a `Token` encompassing
   * the whole input range enclosed by these two.
   */
  range(endToken, text2) {
    return new _Token(text2, SourceLocation.range(this, endToken));
  }
};
var StyleLevel = {
  DISPLAY: 0,
  TEXT: 1,
  SCRIPT: 2,
  SCRIPTSCRIPT: 3
};
var _macros = {};
function defineMacro(name, body) {
  _macros[name] = body;
}
var macros = _macros;
defineMacro("\\noexpand", function(context) {
  const t = context.popToken();
  if (context.isExpandable(t.text)) {
    t.noexpand = true;
    t.treatAsRelax = true;
  }
  return { tokens: [t], numArgs: 0 };
});
defineMacro("\\expandafter", function(context) {
  const t = context.popToken();
  context.expandOnce(true);
  return { tokens: [t], numArgs: 0 };
});
defineMacro("\\@firstoftwo", function(context) {
  const args = context.consumeArgs(2);
  return { tokens: args[0], numArgs: 0 };
});
defineMacro("\\@secondoftwo", function(context) {
  const args = context.consumeArgs(2);
  return { tokens: args[1], numArgs: 0 };
});
defineMacro("\\@ifnextchar", function(context) {
  const args = context.consumeArgs(3);
  context.consumeSpaces();
  const nextToken = context.future();
  if (args[0].length === 1 && args[0][0].text === nextToken.text) {
    return { tokens: args[1], numArgs: 0 };
  } else {
    return { tokens: args[2], numArgs: 0 };
  }
});
defineMacro("\\@ifstar", "\\@ifnextchar *{\\@firstoftwo{#1}}");
defineMacro("\\TextOrMath", function(context) {
  const args = context.consumeArgs(2);
  if (context.mode === "text") {
    return { tokens: args[0], numArgs: 0 };
  } else {
    return { tokens: args[1], numArgs: 0 };
  }
});
var stringFromArg = (arg) => {
  let str = "";
  for (let i = arg.length - 1; i > -1; i--) {
    str += arg[i].text;
  }
  return str;
};
var digitToNumber = {
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
var nextCharNumber = (context) => {
  const numStr = context.future().text;
  if (numStr === "EOF") {
    return [null, ""];
  }
  return [digitToNumber[numStr.charAt(0)], numStr];
};
var appendCharNumbers = (number, numStr, base) => {
  for (let i = 1; i < numStr.length; i++) {
    const digit = digitToNumber[numStr.charAt(i)];
    number *= base;
    number += digit;
  }
  return number;
};
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
  const tokens = context.consumeArgs(1)[0];
  let str = "";
  let expectedLoc = tokens[tokens.length - 1].loc.start;
  for (let i = tokens.length - 1; i >= 0; i--) {
    const actualLoc = tokens[i].loc.start;
    if (actualLoc > expectedLoc) {
      str += " ";
      expectedLoc = actualLoc;
    }
    str += tokens[i].text;
    expectedLoc += tokens[i].text.length;
  }
  return str;
}
defineMacro("\\surd", "\\sqrt{\\vphantom{|}}");
defineMacro("\u2295", "\\oplus");
defineMacro("\\long", "");
defineMacro("\\bgroup", "{");
defineMacro("\\egroup", "}");
defineMacro("~", "\\nobreakspace");
defineMacro("\\lq", "`");
defineMacro("\\rq", "'");
defineMacro("\\aa", "\\r a");
defineMacro("\\Bbbk", "\\Bbb{k}");
defineMacro("\\mathstrut", "\\vphantom{(}");
defineMacro("\\underbar", "\\underline{\\text{#1}}");
defineMacro("\\vdots", "{\\varvdots\\rule{0pt}{15pt}}");
defineMacro("\u22EE", "\\vdots");
defineMacro("\\arraystretch", "1");
defineMacro("\\arraycolsep", "6pt");
defineMacro("\\substack", "\\begin{subarray}{c}#1\\end{subarray}");
defineMacro("\\iff", "\\DOTSB\\;\\Longleftrightarrow\\;");
defineMacro("\\implies", "\\DOTSB\\;\\Longrightarrow\\;");
defineMacro("\\impliedby", "\\DOTSB\\;\\Longleftarrow\\;");
var dotsByToken = {
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
var spaceAfterDots = {
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
defineMacro("\\dotsx", "\\ldots\\,");
defineMacro("\\DOTSI", "\\relax");
defineMacro("\\DOTSB", "\\relax");
defineMacro("\\DOTSX", "\\relax");
defineMacro("\\tmspace", "\\TextOrMath{\\kern#1#3}{\\mskip#1#2}\\relax");
defineMacro("\\,", "{\\tmspace+{3mu}{.1667em}}");
defineMacro("\\thinspace", "\\,");
defineMacro("\\>", "\\mskip{4mu}");
defineMacro("\\:", "{\\tmspace+{4mu}{.2222em}}");
defineMacro("\\medspace", "\\:");
defineMacro("\\;", "{\\tmspace+{5mu}{.2777em}}");
defineMacro("\\thickspace", "\\;");
defineMacro("\\!", "{\\tmspace-{3mu}{.1667em}}");
defineMacro("\\negthinspace", "\\!");
defineMacro("\\negmedspace", "{\\tmspace-{4mu}{.2222em}}");
defineMacro("\\negthickspace", "{\\tmspace-{5mu}{.277em}}");
defineMacro("\\enspace", "\\kern.5em ");
defineMacro("\\enskip", "\\hskip.5em\\relax");
defineMacro("\\quad", "\\hskip1em\\relax");
defineMacro("\\qquad", "\\hskip2em\\relax");
defineMacro("\\AA", "\\TextOrMath{\\Angstrom}{\\mathring{A}}\\relax");
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
defineMacro("\\bmod", "\\mathbin{\\text{mod}}");
defineMacro(
  "\\pod",
  "\\allowbreak\\mathchoice{\\mkern18mu}{\\mkern8mu}{\\mkern8mu}{\\mkern8mu}(#1)"
);
defineMacro("\\pmod", "\\pod{{\\rm mod}\\mkern6mu#1}");
defineMacro(
  "\\mod",
  "\\allowbreak\\mathchoice{\\mkern18mu}{\\mkern12mu}{\\mkern12mu}{\\mkern12mu}{\\rm mod}\\,\\,#1"
);
defineMacro("\\newline", "\\\\\\relax");
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
defineMacro("\\hspace", "\\@ifstar\\@hspacer\\@hspace");
defineMacro("\\@hspace", "\\hskip #1\\relax");
defineMacro("\\@hspacer", "\\rule{0pt}{0pt}\\hskip #1\\relax");
defineMacro("\\colon", `\\mathpunct{\\char"3a}`);
defineMacro("\\prescript", "\\pres@cript{_{#1}^{#2}}{}{#3}");
defineMacro("\\ordinarycolon", `\\char"3a`);
defineMacro("\\vcentcolon", "\\mathrel{\\raisebox{0.035em}{\\ordinarycolon}}");
defineMacro("\\coloneq", '\\mathrel{\\raisebox{0.035em}{\\ordinarycolon}\\char"2212}');
defineMacro("\\Coloneq", '\\mathrel{\\char"2237\\char"2212}');
defineMacro("\\Eqqcolon", '\\mathrel{\\char"3d\\char"2237}');
defineMacro("\\Eqcolon", '\\mathrel{\\char"2212\\char"2237}');
defineMacro("\\colonapprox", '\\mathrel{\\raisebox{0.035em}{\\ordinarycolon}\\char"2248}');
defineMacro("\\Colonapprox", '\\mathrel{\\char"2237\\char"2248}');
defineMacro("\\colonsim", '\\mathrel{\\raisebox{0.035em}{\\ordinarycolon}\\char"223c}');
defineMacro("\\Colonsim", '\\mathrel{\\raisebox{0.035em}{\\ordinarycolon}\\char"223c}');
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
defineMacro("\\coloncolonapprox", "\\Colonapprox");
defineMacro("\\coloncolonsim", "\\Colonsim");
defineMacro("\\notni", "\\mathrel{\\char`\u220C}");
defineMacro("\\limsup", "\\DOTSB\\operatorname*{lim\\,sup}");
defineMacro("\\liminf", "\\DOTSB\\operatorname*{lim\\,inf}");
defineMacro("\\injlim", "\\DOTSB\\operatorname*{inj\\,lim}");
defineMacro("\\projlim", "\\DOTSB\\operatorname*{proj\\,lim}");
defineMacro("\\varlimsup", "\\DOTSB\\operatorname*{\\overline{\\text{lim}}}");
defineMacro("\\varliminf", "\\DOTSB\\operatorname*{\\underline{\\text{lim}}}");
defineMacro("\\varinjlim", "\\DOTSB\\operatorname*{\\underrightarrow{\\text{lim}}}");
defineMacro("\\varprojlim", "\\DOTSB\\operatorname*{\\underleftarrow{\\text{lim}}}");
defineMacro("\\centerdot", "{\\medspace\\rule{0.167em}{0.189em}\\medspace}");
defineMacro("\\argmin", "\\DOTSB\\operatorname*{arg\\,min}");
defineMacro("\\argmax", "\\DOTSB\\operatorname*{arg\\,max}");
defineMacro("\\plim", "\\DOTSB\\operatorname*{plim}");
defineMacro("\\leftmodels", "\\mathop{\\reflectbox{$\\models$}}");
defineMacro("\\bra", "\\mathinner{\\langle{#1}|}");
defineMacro("\\ket", "\\mathinner{|{#1}\\rangle}");
defineMacro("\\braket", "\\mathinner{\\langle{#1}\\rangle}");
defineMacro("\\Bra", "\\left\\langle#1\\right|");
defineMacro("\\Ket", "\\left|#1\\right\\rangle");
var replaceVert = (argStr, match) => {
  const ch = match[0] === "|" ? "\\vert" : "\\Vert";
  const replaceStr = `}\\,\\middle${ch}\\,{`;
  return argStr.slice(0, match.index) + replaceStr + argStr.slice(match.index + match[0].length);
};
defineMacro("\\Braket", function(context) {
  let argStr = recreateArgStr(context);
  const regEx = /\|\||\||\\\|/g;
  let match;
  while ((match = regEx.exec(argStr)) !== null) {
    argStr = replaceVert(argStr, match);
  }
  return "\\left\\langle{" + argStr + "}\\right\\rangle";
});
defineMacro("\\Set", function(context) {
  let argStr = recreateArgStr(context);
  const match = /\|\||\||\\\|/.exec(argStr);
  if (match) {
    argStr = replaceVert(argStr, match);
  }
  return "\\left\\{\\:{" + argStr + "}\\:\\right\\}";
});
defineMacro("\\set", function(context) {
  const argStr = recreateArgStr(context);
  return "\\{{" + argStr.replace(/\|/, "}\\mid{") + "}\\}";
});
defineMacro("\\angln", "{\\angl n}");
defineMacro("\\odv", "\\@ifstar\\odv@next\\odv@numerator");
defineMacro("\\odv@numerator", "\\frac{\\mathrm{d}#1}{\\mathrm{d}#2}");
defineMacro("\\odv@next", "\\frac{\\mathrm{d}}{\\mathrm{d}#2}#1");
defineMacro("\\pdv", "\\@ifstar\\pdv@next\\pdv@numerator");
var pdvHelper = (args) => {
  const numerator = args[0][0].text;
  const denoms = stringFromArg(args[1]).split(",");
  const power = String(denoms.length);
  const numOp = power === "1" ? "\\partial" : `\\partial^${power}`;
  let denominator = "";
  denoms.map((e) => {
    denominator += "\\partial " + e.trim() + "\\,";
  });
  return [numerator, numOp, denominator.replace(/\\,$/, "")];
};
defineMacro("\\pdv@numerator", function(context) {
  const [numerator, numOp, denominator] = pdvHelper(context.consumeArgs(2));
  return `\\frac{${numOp} ${numerator}}{${denominator}}`;
});
defineMacro("\\pdv@next", function(context) {
  const [numerator, numOp, denominator] = pdvHelper(context.consumeArgs(2));
  return `\\frac{${numOp}}{${denominator}} ${numerator}`;
});
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
defineMacro("\\standardstate", "\\text{\\tiny\\char`\u29B5}");
defineMacro("\\ce", function(context) {
  return chemParse(context.consumeArgs(1)[0], "ce");
});
defineMacro("\\pu", function(context) {
  return chemParse(context.consumeArgs(1)[0], "pu");
});
defineMacro("\\uniDash", `{\\rule{0.672em}{0.06em}}`);
defineMacro("\\triDash", `{\\rule{0.15em}{0.06em}\\kern2mu\\rule{0.15em}{0.06em}\\kern2mu\\rule{0.15em}{0.06em}}`);
defineMacro("\\tripleDash", `\\kern0.075em\\raise0.25em{\\triDash}\\kern0.075em`);
defineMacro("\\tripleDashOverLine", `\\kern0.075em\\mathrlap{\\raise0.125em{\\uniDash}}\\raise0.34em{\\triDash}\\kern0.075em`);
defineMacro("\\tripleDashOverDoubleLine", `\\kern0.075em\\mathrlap{\\mathrlap{\\raise0.48em{\\triDash}}\\raise0.27em{\\uniDash}}{\\raise0.05em{\\uniDash}}\\kern0.075em`);
defineMacro("\\tripleDashBetweenDoubleLine", `\\kern0.075em\\mathrlap{\\mathrlap{\\raise0.48em{\\uniDash}}\\raise0.27em{\\triDash}}{\\raise0.05em{\\uniDash}}\\kern0.075em`);
var chemParse = function(tokens, stateMachine) {
  var str = "";
  var expectedLoc = tokens.length && tokens[tokens.length - 1].loc.start;
  for (var i = tokens.length - 1; i >= 0; i--) {
    if (tokens[i].loc.start > expectedLoc) {
      str += " ";
      expectedLoc = tokens[i].loc.start;
    }
    str += tokens[i].text;
    expectedLoc += tokens[i].text.length;
  }
  var tex = texify.go(mhchemParser.go(str, stateMachine));
  return tex;
};
var mhchemParser = {
  //
  // Parses mchem \ce syntax
  //
  // Call like
  //   go("H2O");
  //
  go: function(input, stateMachine) {
    if (!input) {
      return [];
    }
    if (stateMachine === void 0) {
      stateMachine = "ce";
    }
    var state = "0";
    var buffer = {};
    buffer["parenthesisLevel"] = 0;
    input = input.replace(/\n/g, " ");
    input = input.replace(/[\u2212\u2013\u2014\u2010]/g, "-");
    input = input.replace(/[\u2026]/g, "...");
    var lastInput;
    var watchdog = 10;
    var output = [];
    while (true) {
      if (lastInput !== input) {
        watchdog = 10;
        lastInput = input;
      } else {
        watchdog--;
      }
      var machine = mhchemParser.stateMachines[stateMachine];
      var t = machine.transitions[state] || machine.transitions["*"];
      iterateTransitions:
        for (var i = 0; i < t.length; i++) {
          var matches = mhchemParser.patterns.match_(t[i].pattern, input);
          if (matches) {
            var task = t[i].task;
            for (var iA = 0; iA < task.action_.length; iA++) {
              var o;
              if (machine.actions[task.action_[iA].type_]) {
                o = machine.actions[task.action_[iA].type_](buffer, matches.match_, task.action_[iA].option);
              } else if (mhchemParser.actions[task.action_[iA].type_]) {
                o = mhchemParser.actions[task.action_[iA].type_](buffer, matches.match_, task.action_[iA].option);
              } else {
                throw ["MhchemBugA", "mhchem bug A. Please report. (" + task.action_[iA].type_ + ")"];
              }
              mhchemParser.concatArray(output, o);
            }
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
      if (watchdog <= 0) {
        throw ["MhchemBugU", "mhchem bug U. Please report."];
      }
    }
  },
  concatArray: function(a, b) {
    if (b) {
      if (Array.isArray(b)) {
        for (var iB = 0; iB < b.length; iB++) {
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
      "empty": /^$/,
      "else": /^./,
      "else2": /^./,
      "space": /^\s/,
      "space A": /^\s(?=[A-Z\\$])/,
      "space$": /^\s$/,
      "a-z": /^[a-z]/,
      "x": /^x/,
      "x$": /^x$/,
      "i$": /^i$/,
      "letters": /^(?:[a-zA-Z\u03B1-\u03C9\u0391-\u03A9?@]|(?:\\(?:alpha|beta|gamma|delta|epsilon|zeta|eta|theta|iota|kappa|lambda|mu|nu|xi|omicron|pi|rho|sigma|tau|upsilon|phi|chi|psi|omega|Gamma|Delta|Theta|Lambda|Xi|Pi|Sigma|Upsilon|Phi|Psi|Omega)(?:\s+|\{\}|(?![a-zA-Z]))))+/,
      "\\greek": /^\\(?:alpha|beta|gamma|delta|epsilon|zeta|eta|theta|iota|kappa|lambda|mu|nu|xi|omicron|pi|rho|sigma|tau|upsilon|phi|chi|psi|omega|Gamma|Delta|Theta|Lambda|Xi|Pi|Sigma|Upsilon|Phi|Psi|Omega)(?:\s+|\{\}|(?![a-zA-Z]))/,
      "one lowercase latin letter $": /^(?:([a-z])(?:$|[^a-zA-Z]))$/,
      "$one lowercase latin letter$ $": /^\$(?:([a-z])(?:$|[^a-zA-Z]))\$$/,
      "one lowercase greek letter $": /^(?:\$?[\u03B1-\u03C9]\$?|\$?\\(?:alpha|beta|gamma|delta|epsilon|zeta|eta|theta|iota|kappa|lambda|mu|nu|xi|omicron|pi|rho|sigma|tau|upsilon|phi|chi|psi|omega)\s*\$?)(?:\s+|\{\}|(?![a-zA-Z]))$/,
      "digits": /^[0-9]+/,
      "-9.,9": /^[+\-]?(?:[0-9]+(?:[,.][0-9]+)?|[0-9]*(?:\.[0-9]+))/,
      "-9.,9 no missing 0": /^[+\-]?[0-9]+(?:[.,][0-9]+)?/,
      "(-)(9.,9)(e)(99)": function(input) {
        var m = input.match(/^(\+\-|\+\/\-|\+|\-|\\pm\s?)?([0-9]+(?:[,.][0-9]+)?|[0-9]*(?:\.[0-9]+))?(\((?:[0-9]+(?:[,.][0-9]+)?|[0-9]*(?:\.[0-9]+))\))?(?:([eE]|\s*(\*|x|\\times|\u00D7)\s*10\^)([+\-]?[0-9]+|\{[+\-]?[0-9]+\}))?/);
        if (m && m[0]) {
          return { match_: m.splice(1), remainder: input.substr(m[0].length) };
        }
        return null;
      },
      "(-)(9)^(-9)": function(input) {
        var m = input.match(/^(\+\-|\+\/\-|\+|\-|\\pm\s?)?([0-9]+(?:[,.][0-9]+)?|[0-9]*(?:\.[0-9]+)?)\^([+\-]?[0-9]+|\{[+\-]?[0-9]+\})/);
        if (m && m[0]) {
          return { match_: m.splice(1), remainder: input.substr(m[0].length) };
        }
        return null;
      },
      "state of aggregation $": function(input) {
        var a = mhchemParser.patterns.findObserveGroups(input, "", /^\([a-z]{1,3}(?=[\),])/, ")", "");
        if (a && a.remainder.match(/^($|[\s,;\)\]\}])/)) {
          return a;
        }
        var m = input.match(/^(?:\((?:\\ca\s?)?\$[amothc]\$\))/);
        if (m) {
          return { match_: m[0], remainder: input.substr(m[0].length) };
        }
        return null;
      },
      "_{(state of aggregation)}$": /^_\{(\([a-z]{1,3}\))\}/,
      "{[(": /^(?:\\\{|\[|\()/,
      ")]}": /^(?:\)|\]|\\\})/,
      ", ": /^[,;]\s*/,
      ",": /^[,;]/,
      ".": /^[.]/,
      ". ": /^([.\u22C5\u00B7\u2022])\s*/,
      "...": /^\.\.\.(?=$|[^.])/,
      "* ": /^([*])\s*/,
      "^{(...)}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "^{", "", "", "}");
      },
      "^($...$)": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "^", "$", "$", "");
      },
      "^a": /^\^([0-9]+|[^\\_])/,
      "^\\x{}{}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "^", /^\\[a-zA-Z]+\{/, "}", "", "", "{", "}", "", true);
      },
      "^\\x{}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "^", /^\\[a-zA-Z]+\{/, "}", "");
      },
      "^\\x": /^\^(\\[a-zA-Z]+)\s*/,
      "^(-1)": /^\^(-?\d+)/,
      "'": /^'/,
      "_{(...)}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "_{", "", "", "}");
      },
      "_($...$)": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "_", "$", "$", "");
      },
      "_9": /^_([+\-]?[0-9]+|[^\\])/,
      "_\\x{}{}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "_", /^\\[a-zA-Z]+\{/, "}", "", "", "{", "}", "", true);
      },
      "_\\x{}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "_", /^\\[a-zA-Z]+\{/, "}", "");
      },
      "_\\x": /^_(\\[a-zA-Z]+)\s*/,
      "^_": /^(?:\^(?=_)|\_(?=\^)|[\^_]$)/,
      "{}": /^\{\}/,
      "{...}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "", "{", "}", "");
      },
      "{(...)}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "{", "", "", "}");
      },
      "$...$": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "", "$", "$", "");
      },
      "${(...)}$": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "${", "", "", "}$");
      },
      "$(...)$": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "$", "", "", "$");
      },
      "=<>": /^[=<>]/,
      "#": /^[#\u2261]/,
      "+": /^\+/,
      "-$": /^-(?=[\s_},;\]/]|$|\([a-z]+\))/,
      // -space -, -; -] -/ -$ -state-of-aggregation
      "-9": /^-(?=[0-9])/,
      "- orbital overlap": /^-(?=(?:[spd]|sp)(?:$|[\s,;\)\]\}]))/,
      "-": /^-/,
      "pm-operator": /^(?:\\pm|\$\\pm\$|\+-|\+\/-)/,
      "operator": /^(?:\+|(?:[\-=<>]|<<|>>|\\approx|\$\\approx\$)(?=\s|$|-?[0-9]))/,
      "arrowUpDown": /^(?:v|\(v\)|\^|\(\^\))(?=$|[\s,;\)\]\}])/,
      "\\bond{(...)}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "\\bond{", "", "", "}");
      },
      "->": /^(?:<->|<-->|->|<-|<=>>|<<=>|<=>|[\u2192\u27F6\u21CC])/,
      "CMT": /^[CMT](?=\[)/,
      "[(...)]": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "[", "", "", "]");
      },
      "1st-level escape": /^(&|\\\\|\\hline)\s*/,
      "\\,": /^(?:\\[,\ ;:])/,
      // \\x - but output no space before
      "\\x{}{}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "", /^\\[a-zA-Z]+\{/, "}", "", "", "{", "}", "", true);
      },
      "\\x{}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "", /^\\[a-zA-Z]+\{/, "}", "");
      },
      "\\ca": /^\\ca(?:\s+|(?![a-zA-Z]))/,
      "\\x": /^(?:\\[a-zA-Z]+\s*|\\[_&{}%])/,
      "orbital": /^(?:[0-9]{1,2}[spdfgh]|[0-9]{0,2}sp)(?=$|[^a-zA-Z])/,
      // only those with numbers in front, because the others will be formatted correctly anyway
      "others": /^[\/~|]/,
      "\\frac{(...)}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "\\frac{", "", "", "}", "{", "", "", "}");
      },
      "\\overset{(...)}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "\\overset{", "", "", "}", "{", "", "", "}");
      },
      "\\underset{(...)}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "\\underset{", "", "", "}", "{", "", "", "}");
      },
      "\\underbrace{(...)}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "\\underbrace{", "", "", "}_", "{", "", "", "}");
      },
      "\\color{(...)}0": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "\\color{", "", "", "}");
      },
      "\\color{(...)}{(...)}1": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "\\color{", "", "", "}", "{", "", "", "}");
      },
      "\\color(...){(...)}2": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "\\color", "\\", "", /^(?=\{)/, "{", "", "", "}");
      },
      "\\ce{(...)}": function(input) {
        return mhchemParser.patterns.findObserveGroups(input, "\\ce{", "", "", "}");
      },
      "oxidation$": /^(?:[+-][IVX]+|\\pm\s*0|\$\\pm\$\s*0)$/,
      "d-oxidation$": /^(?:[+-]?\s?[IVX]+|\\pm\s*0|\$\\pm\$\s*0)$/,
      // 0 could be oxidation or charge
      "roman numeral": /^[IVX]+/,
      "1/2$": /^[+\-]?(?:[0-9]+|\$[a-z]\$|[a-z])\/[0-9]+(?:\$[a-z]\$|[a-z])?$/,
      "amount": function(input) {
        var match;
        match = input.match(/^(?:(?:(?:\([+\-]?[0-9]+\/[0-9]+\)|[+\-]?(?:[0-9]+|\$[a-z]\$|[a-z])\/[0-9]+|[+\-]?[0-9]+[.,][0-9]+|[+\-]?\.[0-9]+|[+\-]?[0-9]+)(?:[a-z](?=\s*[A-Z]))?)|[+\-]?[a-z](?=\s*[A-Z])|\+(?!\s))/);
        if (match) {
          return { match_: match[0], remainder: input.substr(match[0].length) };
        }
        var a = mhchemParser.patterns.findObserveGroups(input, "", "$", "$", "");
        if (a) {
          match = a.match_.match(/^\$(?:\(?[+\-]?(?:[0-9]*[a-z]?[+\-])?[0-9]*[a-z](?:[+\-][0-9]*[a-z]?)?\)?|\+|-)\$$/);
          if (match) {
            return { match_: match[0], remainder: input.substr(match[0].length) };
          }
        }
        return null;
      },
      "amount2": function(input) {
        return this["amount"](input);
      },
      "(KV letters),": /^(?:[A-Z][a-z]{0,2}|i)(?=,)/,
      "formula$": function(input) {
        if (input.match(/^\([a-z]+\)$/)) {
          return null;
        }
        var match = input.match(/^(?:[a-z]|(?:[0-9\ \+\-\,\.\(\)]+[a-z])+[0-9\ \+\-\,\.\(\)]*|(?:[a-z][0-9\ \+\-\,\.\(\)]+)+[a-z]?)$/);
        if (match) {
          return { match_: match[0], remainder: input.substr(match[0].length) };
        }
        return null;
      },
      "uprightEntities": /^(?:pH|pOH|pC|pK|iPr|iBu)(?=$|[^a-zA-Z])/,
      "/": /^\s*(\/)\s*/,
      "//": /^\s*(\/\/)\s*/,
      "*": /^\s*[*.]\s*/
    },
    findObserveGroups: function(input, begExcl, begIncl, endIncl, endExcl, beg2Excl, beg2Incl, end2Incl, end2Excl, combine) {
      var _match = function(input2, pattern) {
        if (typeof pattern === "string") {
          if (input2.indexOf(pattern) !== 0) {
            return null;
          }
          return pattern;
        } else {
          var match2 = input2.match(pattern);
          if (!match2) {
            return null;
          }
          return match2[0];
        }
      };
      var _findObserveGroups = function(input2, i, endChars) {
        var braces = 0;
        while (i < input2.length) {
          var a = input2.charAt(i);
          var match2 = _match(input2.substr(i), endChars);
          if (match2 !== null && braces === 0) {
            return { endMatchBegin: i, endMatchEnd: i + match2.length };
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
      if (match === null) {
        return null;
      }
      input = input.substr(match.length);
      match = _match(input, begIncl);
      if (match === null) {
        return null;
      }
      var e = _findObserveGroups(input, match.length, endIncl || endExcl);
      if (e === null) {
        return null;
      }
      var match1 = input.substring(0, endIncl ? e.endMatchEnd : e.endMatchBegin);
      if (!(beg2Excl || beg2Incl)) {
        return {
          match_: match1,
          remainder: input.substr(e.endMatchEnd)
        };
      } else {
        var group2 = this.findObserveGroups(input.substr(e.endMatchEnd), beg2Excl, beg2Incl, end2Incl, end2Excl);
        if (group2 === null) {
          return null;
        }
        var matchRet = [match1, group2.match_];
        return {
          match_: combine ? matchRet.join("") : matchRet,
          remainder: group2.remainder
        };
      }
    },
    //
    // Matching function
    // e.g. match("a", input) will look for the regexp called "a" and see if it matches
    // returns null or {match_:"a", remainder:"bc"}
    //
    match_: function(m, input) {
      var pattern = mhchemParser.patterns.patterns[m];
      if (pattern === void 0) {
        throw ["MhchemBugP", "mhchem bug P. Please report. (" + m + ")"];
      } else if (typeof pattern === "function") {
        return mhchemParser.patterns.patterns[m](input);
      } else {
        var match = input.match(pattern);
        if (match) {
          var mm;
          if (match[2]) {
            mm = [match[1], match[2]];
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
    "a=": function(buffer, m) {
      buffer.a = (buffer.a || "") + m;
    },
    "b=": function(buffer, m) {
      buffer.b = (buffer.b || "") + m;
    },
    "p=": function(buffer, m) {
      buffer.p = (buffer.p || "") + m;
    },
    "o=": function(buffer, m) {
      buffer.o = (buffer.o || "") + m;
    },
    "q=": function(buffer, m) {
      buffer.q = (buffer.q || "") + m;
    },
    "d=": function(buffer, m) {
      buffer.d = (buffer.d || "") + m;
    },
    "rm=": function(buffer, m) {
      buffer.rm = (buffer.rm || "") + m;
    },
    "text=": function(buffer, m) {
      buffer.text_ = (buffer.text_ || "") + m;
    },
    "insert": function(buffer, m, a) {
      return { type_: a };
    },
    "insert+p1": function(buffer, m, a) {
      return { type_: a, p1: m };
    },
    "insert+p1+p2": function(buffer, m, a) {
      return { type_: a, p1: m[0], p2: m[1] };
    },
    "copy": function(buffer, m) {
      return m;
    },
    "rm": function(buffer, m) {
      return { type_: "rm", p1: m || "" };
    },
    "text": function(buffer, m) {
      return mhchemParser.go(m, "text");
    },
    "{text}": function(buffer, m) {
      var ret = ["{"];
      mhchemParser.concatArray(ret, mhchemParser.go(m, "text"));
      ret.push("}");
      return ret;
    },
    "tex-math": function(buffer, m) {
      return mhchemParser.go(m, "tex-math");
    },
    "tex-math tight": function(buffer, m) {
      return mhchemParser.go(m, "tex-math tight");
    },
    "bond": function(buffer, m, k) {
      return { type_: "bond", kind_: k || m };
    },
    "color0-output": function(buffer, m) {
      return { type_: "color0", color: m[0] };
    },
    "ce": function(buffer, m) {
      return mhchemParser.go(m);
    },
    "1/2": function(buffer, m) {
      var ret = [];
      if (m.match(/^[+\-]/)) {
        ret.push(m.substr(0, 1));
        m = m.substr(1);
      }
      var n = m.match(/^([0-9]+|\$[a-z]\$|[a-z])\/([0-9]+)(\$[a-z]\$|[a-z])?$/);
      n[1] = n[1].replace(/\$/g, "");
      ret.push({ type_: "frac", p1: n[1], p2: n[2] });
      if (n[3]) {
        n[3] = n[3].replace(/\$/g, "");
        ret.push({ type_: "tex-math", p1: n[3] });
      }
      return ret;
    },
    "9,9": function(buffer, m) {
      return mhchemParser.go(m, "9,9");
    }
  },
  //
  // createTransitions
  // convert  { 'letter': { 'state': { action_: 'output' } } }  to  { 'state' => [ { pattern: 'letter', task: { action_: [{type_: 'output'}] } } ] }
  // with expansion of 'a|b' to 'a' and 'b' (at 2 places)
  //
  createTransitions: function(o) {
    var pattern, state;
    var stateArray;
    var i;
    var transitions = {};
    for (pattern in o) {
      for (state in o[pattern]) {
        stateArray = state.split("|");
        o[pattern][state].stateArray = stateArray;
        for (i = 0; i < stateArray.length; i++) {
          transitions[stateArray[i]] = [];
        }
      }
    }
    for (pattern in o) {
      for (state in o[pattern]) {
        stateArray = o[pattern][state].stateArray || [];
        for (i = 0; i < stateArray.length; i++) {
          var p = o[pattern][state];
          if (p.action_) {
            p.action_ = [].concat(p.action_);
            for (var k = 0; k < p.action_.length; k++) {
              if (typeof p.action_[k] === "string") {
                p.action_[k] = { type_: p.action_[k] };
              }
            }
          } else {
            p.action_ = [];
          }
          var patternArray = pattern.split("|");
          for (var j = 0; j < patternArray.length; j++) {
            if (stateArray[i] === "*") {
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
mhchemParser.stateMachines = {
  //
  // \ce state machines
  //
  //#region ce
  "ce": {
    // main parser
    transitions: mhchemParser.createTransitions({
      "empty": {
        "*": { action_: "output" }
      },
      "else": {
        "0|1|2": { action_: "beginsWithBond=false", revisit: true, toContinue: true }
      },
      "oxidation$": {
        "0": { action_: "oxidation-output" }
      },
      "CMT": {
        "r": { action_: "rdt=", nextState: "rt" },
        "rd": { action_: "rqt=", nextState: "rdt" }
      },
      "arrowUpDown": {
        "0|1|2|as": { action_: ["sb=false", "output", "operator"], nextState: "1" }
      },
      "uprightEntities": {
        "0|1|2": { action_: ["o=", "output"], nextState: "1" }
      },
      "orbital": {
        "0|1|2|3": { action_: "o=", nextState: "o" }
      },
      "->": {
        "0|1|2|3": { action_: "r=", nextState: "r" },
        "a|as": { action_: ["output", "r="], nextState: "r" },
        "*": { action_: ["output", "r="], nextState: "r" }
      },
      "+": {
        "o": { action_: "d= kv", nextState: "d" },
        "d|D": { action_: "d=", nextState: "d" },
        "q": { action_: "d=", nextState: "qd" },
        "qd|qD": { action_: "d=", nextState: "qd" },
        "dq": { action_: ["output", "d="], nextState: "d" },
        "3": { action_: ["sb=false", "output", "operator"], nextState: "0" }
      },
      "amount": {
        "0|2": { action_: "a=", nextState: "a" }
      },
      "pm-operator": {
        "0|1|2|a|as": { action_: ["sb=false", "output", { type_: "operator", option: "\\pm" }], nextState: "0" }
      },
      "operator": {
        "0|1|2|a|as": { action_: ["sb=false", "output", "operator"], nextState: "0" }
      },
      "-$": {
        "o|q": { action_: ["charge or bond", "output"], nextState: "qd" },
        "d": { action_: "d=", nextState: "d" },
        "D": { action_: ["output", { type_: "bond", option: "-" }], nextState: "3" },
        "q": { action_: "d=", nextState: "qd" },
        "qd": { action_: "d=", nextState: "qd" },
        "qD|dq": { action_: ["output", { type_: "bond", option: "-" }], nextState: "3" }
      },
      "-9": {
        "3|o": { action_: ["output", { type_: "insert", option: "hyphen" }], nextState: "3" }
      },
      "- orbital overlap": {
        "o": { action_: ["output", { type_: "insert", option: "hyphen" }], nextState: "2" },
        "d": { action_: ["output", { type_: "insert", option: "hyphen" }], nextState: "2" }
      },
      "-": {
        "0|1|2": { action_: [{ type_: "output", option: 1 }, "beginsWithBond=true", { type_: "bond", option: "-" }], nextState: "3" },
        "3": { action_: { type_: "bond", option: "-" } },
        "a": { action_: ["output", { type_: "insert", option: "hyphen" }], nextState: "2" },
        "as": { action_: [{ type_: "output", option: 2 }, { type_: "bond", option: "-" }], nextState: "3" },
        "b": { action_: "b=" },
        "o": { action_: { type_: "- after o/d", option: false }, nextState: "2" },
        "q": { action_: { type_: "- after o/d", option: false }, nextState: "2" },
        "d|qd|dq": { action_: { type_: "- after o/d", option: true }, nextState: "2" },
        "D|qD|p": { action_: ["output", { type_: "bond", option: "-" }], nextState: "3" }
      },
      "amount2": {
        "1|3": { action_: "a=", nextState: "a" }
      },
      "letters": {
        "0|1|2|3|a|as|b|p|bp|o": { action_: "o=", nextState: "o" },
        "q|dq": { action_: ["output", "o="], nextState: "o" },
        "d|D|qd|qD": { action_: "o after d", nextState: "o" }
      },
      "digits": {
        "o": { action_: "q=", nextState: "q" },
        "d|D": { action_: "q=", nextState: "dq" },
        "q": { action_: ["output", "o="], nextState: "o" },
        "a": { action_: "o=", nextState: "o" }
      },
      "space A": {
        "b|p|bp": {}
      },
      "space": {
        "a": { nextState: "as" },
        "0": { action_: "sb=false" },
        "1|2": { action_: "sb=true" },
        "r|rt|rd|rdt|rdq": { action_: "output", nextState: "0" },
        "*": { action_: ["output", "sb=true"], nextState: "1" }
      },
      "1st-level escape": {
        "1|2": { action_: ["output", { type_: "insert+p1", option: "1st-level escape" }] },
        "*": { action_: ["output", { type_: "insert+p1", option: "1st-level escape" }], nextState: "0" }
      },
      "[(...)]": {
        "r|rt": { action_: "rd=", nextState: "rd" },
        "rd|rdt": { action_: "rq=", nextState: "rdq" }
      },
      "...": {
        "o|d|D|dq|qd|qD": { action_: ["output", { type_: "bond", option: "..." }], nextState: "3" },
        "*": { action_: [{ type_: "output", option: 1 }, { type_: "insert", option: "ellipsis" }], nextState: "1" }
      },
      ". |* ": {
        "*": { action_: ["output", { type_: "insert", option: "addition compound" }], nextState: "1" }
      },
      "state of aggregation $": {
        "*": { action_: ["output", "state of aggregation"], nextState: "1" }
      },
      "{[(": {
        "a|as|o": { action_: ["o=", "output", "parenthesisLevel++"], nextState: "2" },
        "0|1|2|3": { action_: ["o=", "output", "parenthesisLevel++"], nextState: "2" },
        "*": { action_: ["output", "o=", "output", "parenthesisLevel++"], nextState: "2" }
      },
      ")]}": {
        "0|1|2|3|b|p|bp|o": { action_: ["o=", "parenthesisLevel--"], nextState: "o" },
        "a|as|d|D|q|qd|qD|dq": { action_: ["output", "o=", "parenthesisLevel--"], nextState: "o" }
      },
      ", ": {
        "*": { action_: ["output", "comma"], nextState: "0" }
      },
      "^_": {
        // ^ and _ without a sensible argument
        "*": {}
      },
      "^{(...)}|^($...$)": {
        "0|1|2|as": { action_: "b=", nextState: "b" },
        "p": { action_: "b=", nextState: "bp" },
        "3|o": { action_: "d= kv", nextState: "D" },
        "q": { action_: "d=", nextState: "qD" },
        "d|D|qd|qD|dq": { action_: ["output", "d="], nextState: "D" }
      },
      "^a|^\\x{}{}|^\\x{}|^\\x|'": {
        "0|1|2|as": { action_: "b=", nextState: "b" },
        "p": { action_: "b=", nextState: "bp" },
        "3|o": { action_: "d= kv", nextState: "d" },
        "q": { action_: "d=", nextState: "qd" },
        "d|qd|D|qD": { action_: "d=" },
        "dq": { action_: ["output", "d="], nextState: "d" }
      },
      "_{(state of aggregation)}$": {
        "d|D|q|qd|qD|dq": { action_: ["output", "q="], nextState: "q" }
      },
      "_{(...)}|_($...$)|_9|_\\x{}{}|_\\x{}|_\\x": {
        "0|1|2|as": { action_: "p=", nextState: "p" },
        "b": { action_: "p=", nextState: "bp" },
        "3|o": { action_: "q=", nextState: "q" },
        "d|D": { action_: "q=", nextState: "dq" },
        "q|qd|qD|dq": { action_: ["output", "q="], nextState: "q" }
      },
      "=<>": {
        "0|1|2|3|a|as|o|q|d|D|qd|qD|dq": { action_: [{ type_: "output", option: 2 }, "bond"], nextState: "3" }
      },
      "#": {
        "0|1|2|3|a|as|o": { action_: [{ type_: "output", option: 2 }, { type_: "bond", option: "#" }], nextState: "3" }
      },
      "{}": {
        "*": { action_: { type_: "output", option: 1 }, nextState: "1" }
      },
      "{...}": {
        "0|1|2|3|a|as|b|p|bp": { action_: "o=", nextState: "o" },
        "o|d|D|q|qd|qD|dq": { action_: ["output", "o="], nextState: "o" }
      },
      "$...$": {
        "a": { action_: "a=" },
        // 2$n$
        "0|1|2|3|as|b|p|bp|o": { action_: "o=", nextState: "o" },
        // not 'amount'
        "as|o": { action_: "o=" },
        "q|d|D|qd|qD|dq": { action_: ["output", "o="], nextState: "o" }
      },
      "\\bond{(...)}": {
        "*": { action_: [{ type_: "output", option: 2 }, "bond"], nextState: "3" }
      },
      "\\frac{(...)}": {
        "*": { action_: [{ type_: "output", option: 1 }, "frac-output"], nextState: "3" }
      },
      "\\overset{(...)}": {
        "*": { action_: [{ type_: "output", option: 2 }, "overset-output"], nextState: "3" }
      },
      "\\underset{(...)}": {
        "*": { action_: [{ type_: "output", option: 2 }, "underset-output"], nextState: "3" }
      },
      "\\underbrace{(...)}": {
        "*": { action_: [{ type_: "output", option: 2 }, "underbrace-output"], nextState: "3" }
      },
      "\\color{(...)}{(...)}1|\\color(...){(...)}2": {
        "*": { action_: [{ type_: "output", option: 2 }, "color-output"], nextState: "3" }
      },
      "\\color{(...)}0": {
        "*": { action_: [{ type_: "output", option: 2 }, "color0-output"] }
      },
      "\\ce{(...)}": {
        "*": { action_: [{ type_: "output", option: 2 }, "ce"], nextState: "3" }
      },
      "\\,": {
        "*": { action_: [{ type_: "output", option: 1 }, "copy"], nextState: "1" }
      },
      "\\x{}{}|\\x{}|\\x": {
        "0|1|2|3|a|as|b|p|bp|o|c0": { action_: ["o=", "output"], nextState: "3" },
        "*": { action_: ["output", "o=", "output"], nextState: "3" }
      },
      "others": {
        "*": { action_: [{ type_: "output", option: 1 }, "copy"], nextState: "3" }
      },
      "else2": {
        "a": { action_: "a to o", nextState: "o", revisit: true },
        "as": { action_: ["output", "sb=true"], nextState: "1", revisit: true },
        "r|rt|rd|rdt|rdq": { action_: ["output"], nextState: "0", revisit: true },
        "*": { action_: ["output", "copy"], nextState: "3" }
      }
    }),
    actions: {
      "o after d": function(buffer, m) {
        var ret;
        if ((buffer.d || "").match(/^[0-9]+$/)) {
          var tmp = buffer.d;
          buffer.d = void 0;
          ret = this["output"](buffer);
          buffer.b = tmp;
        } else {
          ret = this["output"](buffer);
        }
        mhchemParser.actions["o="](buffer, m);
        return ret;
      },
      "d= kv": function(buffer, m) {
        buffer.d = m;
        buffer.dType = "kv";
      },
      "charge or bond": function(buffer, m) {
        if (buffer["beginsWithBond"]) {
          var ret = [];
          mhchemParser.concatArray(ret, this["output"](buffer));
          mhchemParser.concatArray(ret, mhchemParser.actions["bond"](buffer, m, "-"));
          return ret;
        } else {
          buffer.d = m;
        }
      },
      "- after o/d": function(buffer, m, isAfterD) {
        var c1 = mhchemParser.patterns.match_("orbital", buffer.o || "");
        var c2 = mhchemParser.patterns.match_("one lowercase greek letter $", buffer.o || "");
        var c3 = mhchemParser.patterns.match_("one lowercase latin letter $", buffer.o || "");
        var c4 = mhchemParser.patterns.match_("$one lowercase latin letter$ $", buffer.o || "");
        var hyphenFollows = m === "-" && (c1 && c1.remainder === "" || c2 || c3 || c4);
        if (hyphenFollows && !buffer.a && !buffer.b && !buffer.p && !buffer.d && !buffer.q && !c1 && c3) {
          buffer.o = "$" + buffer.o + "$";
        }
        var ret = [];
        if (hyphenFollows) {
          mhchemParser.concatArray(ret, this["output"](buffer));
          ret.push({ type_: "hyphen" });
        } else {
          c1 = mhchemParser.patterns.match_("digits", buffer.d || "");
          if (isAfterD && c1 && c1.remainder === "") {
            mhchemParser.concatArray(ret, mhchemParser.actions["d="](buffer, m));
            mhchemParser.concatArray(ret, this["output"](buffer));
          } else {
            mhchemParser.concatArray(ret, this["output"](buffer));
            mhchemParser.concatArray(ret, mhchemParser.actions["bond"](buffer, m, "-"));
          }
        }
        return ret;
      },
      "a to o": function(buffer) {
        buffer.o = buffer.a;
        buffer.a = void 0;
      },
      "sb=true": function(buffer) {
        buffer.sb = true;
      },
      "sb=false": function(buffer) {
        buffer.sb = false;
      },
      "beginsWithBond=true": function(buffer) {
        buffer["beginsWithBond"] = true;
      },
      "beginsWithBond=false": function(buffer) {
        buffer["beginsWithBond"] = false;
      },
      "parenthesisLevel++": function(buffer) {
        buffer["parenthesisLevel"]++;
      },
      "parenthesisLevel--": function(buffer) {
        buffer["parenthesisLevel"]--;
      },
      "state of aggregation": function(buffer, m) {
        return { type_: "state of aggregation", p1: mhchemParser.go(m, "o") };
      },
      "comma": function(buffer, m) {
        var a = m.replace(/\s*$/, "");
        var withSpace = a !== m;
        if (withSpace && buffer["parenthesisLevel"] === 0) {
          return { type_: "comma enumeration L", p1: a };
        } else {
          return { type_: "comma enumeration M", p1: a };
        }
      },
      "output": function(buffer, m, entityFollows) {
        var ret;
        if (!buffer.r) {
          ret = [];
          if (!buffer.a && !buffer.b && !buffer.p && !buffer.o && !buffer.q && !buffer.d && !entityFollows) {
          } else {
            if (buffer.sb) {
              ret.push({ type_: "entitySkip" });
            }
            if (!buffer.o && !buffer.q && !buffer.d && !buffer.b && !buffer.p && entityFollows !== 2) {
              buffer.o = buffer.a;
              buffer.a = void 0;
            } else if (!buffer.o && !buffer.q && !buffer.d && (buffer.b || buffer.p)) {
              buffer.o = buffer.a;
              buffer.d = buffer.b;
              buffer.q = buffer.p;
              buffer.a = buffer.b = buffer.p = void 0;
            } else {
              if (buffer.o && buffer.dType === "kv" && mhchemParser.patterns.match_("d-oxidation$", buffer.d || "")) {
                buffer.dType = "oxidation";
              } else if (buffer.o && buffer.dType === "kv" && !buffer.q) {
                buffer.dType = void 0;
              }
            }
            ret.push({
              type_: "chemfive",
              a: mhchemParser.go(buffer.a, "a"),
              b: mhchemParser.go(buffer.b, "bd"),
              p: mhchemParser.go(buffer.p, "pq"),
              o: mhchemParser.go(buffer.o, "o"),
              q: mhchemParser.go(buffer.q, "pq"),
              d: mhchemParser.go(buffer.d, buffer.dType === "oxidation" ? "oxidation" : "bd"),
              dType: buffer.dType
            });
          }
        } else {
          var rd;
          if (buffer.rdt === "M") {
            rd = mhchemParser.go(buffer.rd, "tex-math");
          } else if (buffer.rdt === "T") {
            rd = [{ type_: "text", p1: buffer.rd || "" }];
          } else {
            rd = mhchemParser.go(buffer.rd);
          }
          var rq;
          if (buffer.rqt === "M") {
            rq = mhchemParser.go(buffer.rq, "tex-math");
          } else if (buffer.rqt === "T") {
            rq = [{ type_: "text", p1: buffer.rq || "" }];
          } else {
            rq = mhchemParser.go(buffer.rq);
          }
          ret = {
            type_: "arrow",
            r: buffer.r,
            rd,
            rq
          };
        }
        for (var p in buffer) {
          if (p !== "parenthesisLevel" && p !== "beginsWithBond") {
            delete buffer[p];
          }
        }
        return ret;
      },
      "oxidation-output": function(buffer, m) {
        var ret = ["{"];
        mhchemParser.concatArray(ret, mhchemParser.go(m, "oxidation"));
        ret.push("}");
        return ret;
      },
      "frac-output": function(buffer, m) {
        return { type_: "frac-ce", p1: mhchemParser.go(m[0]), p2: mhchemParser.go(m[1]) };
      },
      "overset-output": function(buffer, m) {
        return { type_: "overset", p1: mhchemParser.go(m[0]), p2: mhchemParser.go(m[1]) };
      },
      "underset-output": function(buffer, m) {
        return { type_: "underset", p1: mhchemParser.go(m[0]), p2: mhchemParser.go(m[1]) };
      },
      "underbrace-output": function(buffer, m) {
        return { type_: "underbrace", p1: mhchemParser.go(m[0]), p2: mhchemParser.go(m[1]) };
      },
      "color-output": function(buffer, m) {
        return { type_: "color", color1: m[0], color2: mhchemParser.go(m[1]) };
      },
      "r=": function(buffer, m) {
        buffer.r = m;
      },
      "rdt=": function(buffer, m) {
        buffer.rdt = m;
      },
      "rd=": function(buffer, m) {
        buffer.rd = m;
      },
      "rqt=": function(buffer, m) {
        buffer.rqt = m;
      },
      "rq=": function(buffer, m) {
        buffer.rq = m;
      },
      "operator": function(buffer, m, p1) {
        return { type_: "operator", kind_: p1 || m };
      }
    }
  },
  "a": {
    transitions: mhchemParser.createTransitions({
      "empty": {
        "*": {}
      },
      "1/2$": {
        "0": { action_: "1/2" }
      },
      "else": {
        "0": { nextState: "1", revisit: true }
      },
      "$(...)$": {
        "*": { action_: "tex-math tight", nextState: "1" }
      },
      ",": {
        "*": { action_: { type_: "insert", option: "commaDecimal" } }
      },
      "else2": {
        "*": { action_: "copy" }
      }
    }),
    actions: {}
  },
  "o": {
    transitions: mhchemParser.createTransitions({
      "empty": {
        "*": {}
      },
      "1/2$": {
        "0": { action_: "1/2" }
      },
      "else": {
        "0": { nextState: "1", revisit: true }
      },
      "letters": {
        "*": { action_: "rm" }
      },
      "\\ca": {
        "*": { action_: { type_: "insert", option: "circa" } }
      },
      "\\x{}{}|\\x{}|\\x": {
        "*": { action_: "copy" }
      },
      "${(...)}$|$(...)$": {
        "*": { action_: "tex-math" }
      },
      "{(...)}": {
        "*": { action_: "{text}" }
      },
      "else2": {
        "*": { action_: "copy" }
      }
    }),
    actions: {}
  },
  "text": {
    transitions: mhchemParser.createTransitions({
      "empty": {
        "*": { action_: "output" }
      },
      "{...}": {
        "*": { action_: "text=" }
      },
      "${(...)}$|$(...)$": {
        "*": { action_: "tex-math" }
      },
      "\\greek": {
        "*": { action_: ["output", "rm"] }
      },
      "\\,|\\x{}{}|\\x{}|\\x": {
        "*": { action_: ["output", "copy"] }
      },
      "else": {
        "*": { action_: "text=" }
      }
    }),
    actions: {
      "output": function(buffer) {
        if (buffer.text_) {
          var ret = { type_: "text", p1: buffer.text_ };
          for (var p in buffer) {
            delete buffer[p];
          }
          return ret;
        }
      }
    }
  },
  "pq": {
    transitions: mhchemParser.createTransitions({
      "empty": {
        "*": {}
      },
      "state of aggregation $": {
        "*": { action_: "state of aggregation" }
      },
      "i$": {
        "0": { nextState: "!f", revisit: true }
      },
      "(KV letters),": {
        "0": { action_: "rm", nextState: "0" }
      },
      "formula$": {
        "0": { nextState: "f", revisit: true }
      },
      "1/2$": {
        "0": { action_: "1/2" }
      },
      "else": {
        "0": { nextState: "!f", revisit: true }
      },
      "${(...)}$|$(...)$": {
        "*": { action_: "tex-math" }
      },
      "{(...)}": {
        "*": { action_: "text" }
      },
      "a-z": {
        "f": { action_: "tex-math" }
      },
      "letters": {
        "*": { action_: "rm" }
      },
      "-9.,9": {
        "*": { action_: "9,9" }
      },
      ",": {
        "*": { action_: { type_: "insert+p1", option: "comma enumeration S" } }
      },
      "\\color{(...)}{(...)}1|\\color(...){(...)}2": {
        "*": { action_: "color-output" }
      },
      "\\color{(...)}0": {
        "*": { action_: "color0-output" }
      },
      "\\ce{(...)}": {
        "*": { action_: "ce" }
      },
      "\\,|\\x{}{}|\\x{}|\\x": {
        "*": { action_: "copy" }
      },
      "else2": {
        "*": { action_: "copy" }
      }
    }),
    actions: {
      "state of aggregation": function(buffer, m) {
        return { type_: "state of aggregation subscript", p1: mhchemParser.go(m, "o") };
      },
      "color-output": function(buffer, m) {
        return { type_: "color", color1: m[0], color2: mhchemParser.go(m[1], "pq") };
      }
    }
  },
  "bd": {
    transitions: mhchemParser.createTransitions({
      "empty": {
        "*": {}
      },
      "x$": {
        "0": { nextState: "!f", revisit: true }
      },
      "formula$": {
        "0": { nextState: "f", revisit: true }
      },
      "else": {
        "0": { nextState: "!f", revisit: true }
      },
      "-9.,9 no missing 0": {
        "*": { action_: "9,9" }
      },
      ".": {
        "*": { action_: { type_: "insert", option: "electron dot" } }
      },
      "a-z": {
        "f": { action_: "tex-math" }
      },
      "x": {
        "*": { action_: { type_: "insert", option: "KV x" } }
      },
      "letters": {
        "*": { action_: "rm" }
      },
      "'": {
        "*": { action_: { type_: "insert", option: "prime" } }
      },
      "${(...)}$|$(...)$": {
        "*": { action_: "tex-math" }
      },
      "{(...)}": {
        "*": { action_: "text" }
      },
      "\\color{(...)}{(...)}1|\\color(...){(...)}2": {
        "*": { action_: "color-output" }
      },
      "\\color{(...)}0": {
        "*": { action_: "color0-output" }
      },
      "\\ce{(...)}": {
        "*": { action_: "ce" }
      },
      "\\,|\\x{}{}|\\x{}|\\x": {
        "*": { action_: "copy" }
      },
      "else2": {
        "*": { action_: "copy" }
      }
    }),
    actions: {
      "color-output": function(buffer, m) {
        return { type_: "color", color1: m[0], color2: mhchemParser.go(m[1], "bd") };
      }
    }
  },
  "oxidation": {
    transitions: mhchemParser.createTransitions({
      "empty": {
        "*": {}
      },
      "roman numeral": {
        "*": { action_: "roman-numeral" }
      },
      "${(...)}$|$(...)$": {
        "*": { action_: "tex-math" }
      },
      "else": {
        "*": { action_: "copy" }
      }
    }),
    actions: {
      "roman-numeral": function(buffer, m) {
        return { type_: "roman numeral", p1: m || "" };
      }
    }
  },
  "tex-math": {
    transitions: mhchemParser.createTransitions({
      "empty": {
        "*": { action_: "output" }
      },
      "\\ce{(...)}": {
        "*": { action_: ["output", "ce"] }
      },
      "{...}|\\,|\\x{}{}|\\x{}|\\x": {
        "*": { action_: "o=" }
      },
      "else": {
        "*": { action_: "o=" }
      }
    }),
    actions: {
      "output": function(buffer) {
        if (buffer.o) {
          var ret = { type_: "tex-math", p1: buffer.o };
          for (var p in buffer) {
            delete buffer[p];
          }
          return ret;
        }
      }
    }
  },
  "tex-math tight": {
    transitions: mhchemParser.createTransitions({
      "empty": {
        "*": { action_: "output" }
      },
      "\\ce{(...)}": {
        "*": { action_: ["output", "ce"] }
      },
      "{...}|\\,|\\x{}{}|\\x{}|\\x": {
        "*": { action_: "o=" }
      },
      "-|+": {
        "*": { action_: "tight operator" }
      },
      "else": {
        "*": { action_: "o=" }
      }
    }),
    actions: {
      "tight operator": function(buffer, m) {
        buffer.o = (buffer.o || "") + "{" + m + "}";
      },
      "output": function(buffer) {
        if (buffer.o) {
          var ret = { type_: "tex-math", p1: buffer.o };
          for (var p in buffer) {
            delete buffer[p];
          }
          return ret;
        }
      }
    }
  },
  "9,9": {
    transitions: mhchemParser.createTransitions({
      "empty": {
        "*": {}
      },
      ",": {
        "*": { action_: "comma" }
      },
      "else": {
        "*": { action_: "copy" }
      }
    }),
    actions: {
      "comma": function() {
        return { type_: "commaDecimal" };
      }
    }
  },
  //#endregion
  //
  // \pu state machines
  //
  //#region pu
  "pu": {
    transitions: mhchemParser.createTransitions({
      "empty": {
        "*": { action_: "output" }
      },
      "space$": {
        "*": { action_: ["output", "space"] }
      },
      "{[(|)]}": {
        "0|a": { action_: "copy" }
      },
      "(-)(9)^(-9)": {
        "0": { action_: "number^", nextState: "a" }
      },
      "(-)(9.,9)(e)(99)": {
        "0": { action_: "enumber", nextState: "a" }
      },
      "space": {
        "0|a": {}
      },
      "pm-operator": {
        "0|a": { action_: { type_: "operator", option: "\\pm" }, nextState: "0" }
      },
      "operator": {
        "0|a": { action_: "copy", nextState: "0" }
      },
      "//": {
        "d": { action_: "o=", nextState: "/" }
      },
      "/": {
        "d": { action_: "o=", nextState: "/" }
      },
      "{...}|else": {
        "0|d": { action_: "d=", nextState: "d" },
        "a": { action_: ["space", "d="], nextState: "d" },
        "/|q": { action_: "q=", nextState: "q" }
      }
    }),
    actions: {
      "enumber": function(buffer, m) {
        var ret = [];
        if (m[0] === "+-" || m[0] === "+/-") {
          ret.push("\\pm ");
        } else if (m[0]) {
          ret.push(m[0]);
        }
        if (m[1]) {
          mhchemParser.concatArray(ret, mhchemParser.go(m[1], "pu-9,9"));
          if (m[2]) {
            if (m[2].match(/[,.]/)) {
              mhchemParser.concatArray(ret, mhchemParser.go(m[2], "pu-9,9"));
            } else {
              ret.push(m[2]);
            }
          }
          m[3] = m[4] || m[3];
          if (m[3]) {
            m[3] = m[3].trim();
            if (m[3] === "e" || m[3].substr(0, 1) === "*") {
              ret.push({ type_: "cdot" });
            } else {
              ret.push({ type_: "times" });
            }
          }
        }
        if (m[3]) {
          ret.push("10^{" + m[5] + "}");
        }
        return ret;
      },
      "number^": function(buffer, m) {
        var ret = [];
        if (m[0] === "+-" || m[0] === "+/-") {
          ret.push("\\pm ");
        } else if (m[0]) {
          ret.push(m[0]);
        }
        mhchemParser.concatArray(ret, mhchemParser.go(m[1], "pu-9,9"));
        ret.push("^{" + m[2] + "}");
        return ret;
      },
      "operator": function(buffer, m, p1) {
        return { type_: "operator", kind_: p1 || m };
      },
      "space": function() {
        return { type_: "pu-space-1" };
      },
      "output": function(buffer) {
        var ret;
        var md = mhchemParser.patterns.match_("{(...)}", buffer.d || "");
        if (md && md.remainder === "") {
          buffer.d = md.match_;
        }
        var mq = mhchemParser.patterns.match_("{(...)}", buffer.q || "");
        if (mq && mq.remainder === "") {
          buffer.q = mq.match_;
        }
        if (buffer.d) {
          buffer.d = buffer.d.replace(/\u00B0C|\^oC|\^{o}C/g, "{}^{\\circ}C");
          buffer.d = buffer.d.replace(/\u00B0F|\^oF|\^{o}F/g, "{}^{\\circ}F");
        }
        if (buffer.q) {
          buffer.q = buffer.q.replace(/\u00B0C|\^oC|\^{o}C/g, "{}^{\\circ}C");
          buffer.q = buffer.q.replace(/\u00B0F|\^oF|\^{o}F/g, "{}^{\\circ}F");
          var b5 = {
            d: mhchemParser.go(buffer.d, "pu"),
            q: mhchemParser.go(buffer.q, "pu")
          };
          if (buffer.o === "//") {
            ret = { type_: "pu-frac", p1: b5.d, p2: b5.q };
          } else {
            ret = b5.d;
            if (b5.d.length > 1 || b5.q.length > 1) {
              ret.push({ type_: " / " });
            } else {
              ret.push({ type_: "/" });
            }
            mhchemParser.concatArray(ret, b5.q);
          }
        } else {
          ret = mhchemParser.go(buffer.d, "pu-2");
        }
        for (var p in buffer) {
          delete buffer[p];
        }
        return ret;
      }
    }
  },
  "pu-2": {
    transitions: mhchemParser.createTransitions({
      "empty": {
        "*": { action_: "output" }
      },
      "*": {
        "*": { action_: ["output", "cdot"], nextState: "0" }
      },
      "\\x": {
        "*": { action_: "rm=" }
      },
      "space": {
        "*": { action_: ["output", "space"], nextState: "0" }
      },
      "^{(...)}|^(-1)": {
        "1": { action_: "^(-1)" }
      },
      "-9.,9": {
        "0": { action_: "rm=", nextState: "0" },
        "1": { action_: "^(-1)", nextState: "0" }
      },
      "{...}|else": {
        "*": { action_: "rm=", nextState: "1" }
      }
    }),
    actions: {
      "cdot": function() {
        return { type_: "tight cdot" };
      },
      "^(-1)": function(buffer, m) {
        buffer.rm += "^{" + m + "}";
      },
      "space": function() {
        return { type_: "pu-space-2" };
      },
      "output": function(buffer) {
        var ret = [];
        if (buffer.rm) {
          var mrm = mhchemParser.patterns.match_("{(...)}", buffer.rm || "");
          if (mrm && mrm.remainder === "") {
            ret = mhchemParser.go(mrm.match_, "pu");
          } else {
            ret = { type_: "rm", p1: buffer.rm };
          }
        }
        for (var p in buffer) {
          delete buffer[p];
        }
        return ret;
      }
    }
  },
  "pu-9,9": {
    transitions: mhchemParser.createTransitions({
      "empty": {
        "0": { action_: "output-0" },
        "o": { action_: "output-o" }
      },
      ",": {
        "0": { action_: ["output-0", "comma"], nextState: "o" }
      },
      ".": {
        "0": { action_: ["output-0", "copy"], nextState: "o" }
      },
      "else": {
        "*": { action_: "text=" }
      }
    }),
    actions: {
      "comma": function() {
        return { type_: "commaDecimal" };
      },
      "output-0": function(buffer) {
        var ret = [];
        buffer.text_ = buffer.text_ || "";
        if (buffer.text_.length > 4) {
          var a = buffer.text_.length % 3;
          if (a === 0) {
            a = 3;
          }
          for (var i = buffer.text_.length - 3; i > 0; i -= 3) {
            ret.push(buffer.text_.substr(i, 3));
            ret.push({ type_: "1000 separator" });
          }
          ret.push(buffer.text_.substr(0, a));
          ret.reverse();
        } else {
          ret.push(buffer.text_);
        }
        for (var p in buffer) {
          delete buffer[p];
        }
        return ret;
      },
      "output-o": function(buffer) {
        var ret = [];
        buffer.text_ = buffer.text_ || "";
        if (buffer.text_.length > 4) {
          var a = buffer.text_.length - 3;
          for (var i = 0; i < a; i += 3) {
            ret.push(buffer.text_.substr(i, 3));
            ret.push({ type_: "1000 separator" });
          }
          ret.push(buffer.text_.substr(i));
        } else {
          ret.push(buffer.text_);
        }
        for (var p in buffer) {
          delete buffer[p];
        }
        return ret;
      }
    }
  }
  //#endregion
};
var texify = {
  go: function(input, isInner) {
    if (!input) {
      return "";
    }
    var res = "";
    var cee = false;
    for (var i = 0; i < input.length; i++) {
      var inputi = input[i];
      if (typeof inputi === "string") {
        res += inputi;
      } else {
        res += texify._go2(inputi);
        if (inputi.type_ === "1st-level escape") {
          cee = true;
        }
      }
    }
    if (!isInner && !cee && res) {
      res = "{" + res + "}";
    }
    return res;
  },
  _goInner: function(input) {
    if (!input) {
      return input;
    }
    return texify.go(input, true);
  },
  _go2: function(buf) {
    var res;
    switch (buf.type_) {
      case "chemfive":
        res = "";
        var b5 = {
          a: texify._goInner(buf.a),
          b: texify._goInner(buf.b),
          p: texify._goInner(buf.p),
          o: texify._goInner(buf.o),
          q: texify._goInner(buf.q),
          d: texify._goInner(buf.d)
        };
        if (b5.a) {
          if (b5.a.match(/^[+\-]/)) {
            b5.a = "{" + b5.a + "}";
          }
          res += b5.a + "\\,";
        }
        if (b5.b || b5.p) {
          res += "{\\vphantom{X}}";
          res += "^{\\hphantom{" + (b5.b || "") + "}}_{\\hphantom{" + (b5.p || "") + "}}";
          res += "{\\vphantom{X}}";
          res += "^{\\vphantom{2}\\mathllap{" + (b5.b || "") + "}}";
          res += "_{\\vphantom{2}\\mathllap{" + (b5.p || "") + "}}";
        }
        if (b5.o) {
          if (b5.o.match(/^[+\-]/)) {
            b5.o = "{" + b5.o + "}";
          }
          res += b5.o;
        }
        if (buf.dType === "kv") {
          if (b5.d || b5.q) {
            res += "{\\vphantom{X}}";
          }
          if (b5.d) {
            res += "^{" + b5.d + "}";
          }
          if (b5.q) {
            res += "_{" + b5.q + "}";
          }
        } else if (buf.dType === "oxidation") {
          if (b5.d) {
            res += "{\\vphantom{X}}";
            res += "^{" + b5.d + "}";
          }
          if (b5.q) {
            res += "{{}}";
            res += "_{" + b5.q + "}";
          }
        } else {
          if (b5.q) {
            res += "{{}}";
            res += "_{" + b5.q + "}";
          }
          if (b5.d) {
            res += "{{}}";
            res += "^{" + b5.d + "}";
          }
        }
        break;
      case "rm":
        res = "\\mathrm{" + buf.p1 + "}";
        break;
      case "text":
        if (buf.p1.match(/[\^_]/)) {
          buf.p1 = buf.p1.replace(" ", "~").replace("-", "\\text{-}");
          res = "\\mathrm{" + buf.p1 + "}";
        } else {
          res = "\\text{" + buf.p1 + "}";
        }
        break;
      case "roman numeral":
        res = "\\mathrm{" + buf.p1 + "}";
        break;
      case "state of aggregation":
        res = "\\mskip2mu " + texify._goInner(buf.p1);
        break;
      case "state of aggregation subscript":
        res = "\\mskip1mu " + texify._goInner(buf.p1);
        break;
      case "bond":
        res = texify._getBond(buf.kind_);
        if (!res) {
          throw ["MhchemErrorBond", "mhchem Error. Unknown bond type (" + buf.kind_ + ")"];
        }
        break;
      case "frac":
        var c = "\\frac{" + buf.p1 + "}{" + buf.p2 + "}";
        res = "\\mathchoice{\\textstyle" + c + "}{" + c + "}{" + c + "}{" + c + "}";
        break;
      case "pu-frac":
        var d = "\\frac{" + texify._goInner(buf.p1) + "}{" + texify._goInner(buf.p2) + "}";
        res = "\\mathchoice{\\textstyle" + d + "}{" + d + "}{" + d + "}{" + d + "}";
        break;
      case "tex-math":
        res = buf.p1 + " ";
        break;
      case "frac-ce":
        res = "\\frac{" + texify._goInner(buf.p1) + "}{" + texify._goInner(buf.p2) + "}";
        break;
      case "overset":
        res = "\\overset{" + texify._goInner(buf.p1) + "}{" + texify._goInner(buf.p2) + "}";
        break;
      case "underset":
        res = "\\underset{" + texify._goInner(buf.p1) + "}{" + texify._goInner(buf.p2) + "}";
        break;
      case "underbrace":
        res = "\\underbrace{" + texify._goInner(buf.p1) + "}_{" + texify._goInner(buf.p2) + "}";
        break;
      case "color":
        res = "{\\color{" + buf.color1 + "}{" + texify._goInner(buf.color2) + "}}";
        break;
      case "color0":
        res = "\\color{" + buf.color + "}";
        break;
      case "arrow":
        var b6 = {
          rd: texify._goInner(buf.rd),
          rq: texify._goInner(buf.rq)
        };
        var arrow = texify._getArrow(buf.r);
        if (b6.rq) {
          arrow += "[{\\rm " + b6.rq + "}]";
        }
        if (b6.rd) {
          arrow += "{\\rm " + b6.rd + "}";
        } else {
          arrow += "{}";
        }
        res = arrow;
        break;
      case "operator":
        res = texify._getOperator(buf.kind_);
        break;
      case "1st-level escape":
        res = buf.p1 + " ";
        break;
      case "space":
        res = " ";
        break;
      case "entitySkip":
        res = "~";
        break;
      case "pu-space-1":
        res = "~";
        break;
      case "pu-space-2":
        res = "\\mkern3mu ";
        break;
      case "1000 separator":
        res = "\\mkern2mu ";
        break;
      case "commaDecimal":
        res = "{,}";
        break;
      case "comma enumeration L":
        res = "{" + buf.p1 + "}\\mkern6mu ";
        break;
      case "comma enumeration M":
        res = "{" + buf.p1 + "}\\mkern3mu ";
        break;
      case "comma enumeration S":
        res = "{" + buf.p1 + "}\\mkern1mu ";
        break;
      case "hyphen":
        res = "\\text{-}";
        break;
      case "addition compound":
        res = "\\,{\\cdot}\\,";
        break;
      case "electron dot":
        res = "\\mkern1mu \\text{\\textbullet}\\mkern1mu ";
        break;
      case "KV x":
        res = "{\\times}";
        break;
      case "prime":
        res = "\\prime ";
        break;
      case "cdot":
        res = "\\cdot ";
        break;
      case "tight cdot":
        res = "\\mkern1mu{\\cdot}\\mkern1mu ";
        break;
      case "times":
        res = "\\times ";
        break;
      case "circa":
        res = "{\\sim}";
        break;
      case "^":
        res = "uparrow";
        break;
      case "v":
        res = "downarrow";
        break;
      case "ellipsis":
        res = "\\ldots ";
        break;
      case "/":
        res = "/";
        break;
      case " / ":
        res = "\\,/\\,";
        break;
      default:
        assertNever(buf);
        throw ["MhchemBugT", "mhchem bug T. Please report."];
    }
    assertString(res);
    return res;
  },
  _getArrow: function(a) {
    switch (a) {
      case "->":
        return "\\yields";
      case "\u2192":
        return "\\yields";
      case "\u27F6":
        return "\\yields";
      case "<-":
        return "\\yieldsLeft";
      case "<->":
        return "\\mesomerism";
      case "<-->":
        return "\\yieldsLeftRight";
      case "<=>":
        return "\\equilibrium";
      case "\u21CC":
        return "\\equilibrium";
      case "<=>>":
        return "\\equilibriumRight";
      case "<<=>":
        return "\\equilibriumLeft";
      default:
        assertNever(a);
        throw ["MhchemBugT", "mhchem bug T. Please report."];
    }
  },
  _getBond: function(a) {
    switch (a) {
      case "-":
        return "{-}";
      case "1":
        return "{-}";
      case "=":
        return "{=}";
      case "2":
        return "{=}";
      case "#":
        return "{\\equiv}";
      case "3":
        return "{\\equiv}";
      case "~":
        return "{\\tripleDash}";
      case "~-":
        return "{\\tripleDashOverLine}";
      case "~=":
        return "{\\tripleDashOverDoubleLine}";
      case "~--":
        return "{\\tripleDashOverDoubleLine}";
      case "-~-":
        return "{\\tripleDashBetweenDoubleLine}";
      case "...":
        return "{{\\cdot}{\\cdot}{\\cdot}}";
      case "....":
        return "{{\\cdot}{\\cdot}{\\cdot}{\\cdot}}";
      case "->":
        return "{\\rightarrow}";
      case "<-":
        return "{\\leftarrow}";
      case "<":
        return "{<}";
      case ">":
        return "{>}";
      default:
        assertNever(a);
        throw ["MhchemBugT", "mhchem bug T. Please report."];
    }
  },
  _getOperator: function(a) {
    switch (a) {
      case "+":
        return " {}+{} ";
      case "-":
        return " {}-{} ";
      case "=":
        return " {}={} ";
      case "<":
        return " {}<{} ";
      case ">":
        return " {}>{} ";
      case "<<":
        return " {}\\ll{} ";
      case ">>":
        return " {}\\gg{} ";
      case "\\pm":
        return " {}\\pm{} ";
      case "\\approx":
        return " {}\\approx{} ";
      case "$\\approx$":
        return " {}\\approx{} ";
      case "v":
        return " \\downarrow{} ";
      case "(v)":
        return " \\downarrow{} ";
      case "^":
        return " \\uparrow{} ";
      case "(^)":
        return " \\uparrow{} ";
      default:
        assertNever(a);
        throw ["MhchemBugT", "mhchem bug T. Please report."];
    }
  }
};
function assertNever(a) {
}
function assertString(a) {
}
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
defineMacro(
  "\\outerproduct",
  "{\\left\\vert { #1 } \\right\\rangle\\left\\langle { #2} \\right\\vert}"
);
defineMacro(
  "\\dyad",
  "{\\left\\vert { #1 } \\right\\rangle\\left\\langle { #2} \\right\\vert}"
);
defineMacro(
  "\\ketbra",
  "{\\left\\vert { #1 } \\right\\rangle\\left\\langle { #2} \\right\\vert}"
);
defineMacro(
  "\\op",
  "{\\left\\vert { #1 } \\right\\rangle\\left\\langle { #2} \\right\\vert}"
);
defineMacro("\\expectationvalue", "{\\left\\langle {#1 } \\right\\rangle}");
defineMacro("\\expval", "{\\left\\langle {#1 } \\right\\rangle}");
defineMacro("\\ev", "{\\left\\langle {#1 } \\right\\rangle}");
defineMacro(
  "\\matrixelement",
  "{\\left\\langle{ #1 }\\right\\vert{ #2 }\\left\\vert{#3}\\right\\rangle}"
);
defineMacro(
  "\\matrixel",
  "{\\left\\langle{ #1 }\\right\\vert{ #2 }\\left\\vert{#3}\\right\\rangle}"
);
defineMacro(
  "\\mel",
  "{\\left\\langle{ #1 }\\right\\vert{ #2 }\\left\\vert{#3}\\right\\rangle}"
);
function getHLines(parser) {
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
var validateAmsEnvironmentContext = (context) => {
  const settings = context.parser.settings;
  if (!settings.displayMode) {
    throw new ParseError(`{${context.envName}} can be used only in display mode.`);
  }
};
var sizeRegEx$1 = /([-+]?) *(\d+(?:\.\d*)?|\.\d+) *([a-z]{2})/;
var arrayGaps = (macros2) => {
  let arraystretch = macros2.get("\\arraystretch");
  if (typeof arraystretch !== "string") {
    arraystretch = stringFromArg(arraystretch.tokens);
  }
  arraystretch = isNaN(arraystretch) ? null : Number(arraystretch);
  let arraycolsepStr = macros2.get("\\arraycolsep");
  if (typeof arraycolsepStr !== "string") {
    arraycolsepStr = stringFromArg(arraycolsepStr.tokens);
  }
  const match = sizeRegEx$1.exec(arraycolsepStr);
  const arraycolsep = match ? { number: +(match[1] + match[2]), unit: match[3] } : null;
  return [arraystretch, arraycolsep];
};
var checkCellForLabels = (cell) => {
  let rowLabel = "";
  for (let i = 0; i < cell.length; i++) {
    if (cell[i].type === "label") {
      if (rowLabel) {
        throw new ParseError("Multiple \\labels in one row");
      }
      rowLabel = cell[i].string;
    }
  }
  return rowLabel;
};
function getAutoTag(name) {
  if (name.indexOf("ed") === -1) {
    return name.indexOf("*") === -1;
  }
}
function parseArray(parser, {
  cols,
  // [{ type: string , align: l|c|r|null }]
  envClasses,
  // align(ed|at|edat) | array | cases | cd | small | multline
  autoTag,
  // boolean
  singleRow,
  // boolean
  emptySingleRow,
  // boolean
  maxNumCols,
  // number
  leqno,
  // boolean
  arraystretch,
  // number  | null
  arraycolsep
  // size value | null
}, scriptLevel2) {
  parser.gullet.beginGroup();
  if (!singleRow) {
    parser.gullet.macros.set("\\cr", "\\\\\\relax");
  }
  parser.gullet.beginGroup();
  let row = [];
  const body = [row];
  const rowGaps = [];
  const labels = [];
  const hLinesBeforeRow = [];
  const tags = autoTag != null ? [] : void 0;
  function beginRow() {
    if (autoTag) {
      parser.gullet.macros.set("\\@eqnsw", "1", true);
    }
  }
  function endRow() {
    if (tags) {
      if (parser.gullet.macros.get("\\df@tag")) {
        tags.push(parser.subparse([new Token("\\df@tag")]));
        parser.gullet.macros.set("\\df@tag", void 0, true);
      } else {
        tags.push(Boolean(autoTag) && parser.gullet.macros.get("\\@eqnsw") === "1");
      }
    }
  }
  beginRow();
  hLinesBeforeRow.push(getHLines(parser));
  while (true) {
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
            throw new ParseError(
              "Too few columns specified in the {array} column argument.",
              parser.nextToken
            );
          }
        } else if (maxNumCols === 2) {
          throw new ParseError(
            "The split environment accepts no more than two columns",
            parser.nextToken
          );
        } else {
          throw new ParseError(
            "The equation environment accepts only one column",
            parser.nextToken
          );
        }
      }
      parser.consume();
    } else if (next === "\\end") {
      endRow();
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
      if (parser.gullet.future().text !== " ") {
        size = parser.parseSizeGroup(true);
      }
      rowGaps.push(size ? size.value : null);
      endRow();
      labels.push(checkCellForLabels(cell.body));
      hLinesBeforeRow.push(getHLines(parser));
      row = [];
      body.push(row);
      beginRow();
    } else {
      throw new ParseError("Expected & or \\\\ or \\cr or \\end", parser.nextToken);
    }
  }
  parser.gullet.endGroup();
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
    scriptLevel: scriptLevel2,
    tags,
    labels,
    leqno,
    arraystretch,
    arraycolsep
  };
}
function dCellStyle(envName) {
  return envName.slice(0, 1) === "d" ? "display" : "text";
}
var alignMap = {
  c: "center ",
  l: "left ",
  r: "right "
};
var glue = (group) => {
  const glueNode = new mathMLTree.MathNode("mtd", []);
  glueNode.style = { padding: "0", width: "50%" };
  if (group.envClasses.includes("multline")) {
    glueNode.style.width = "7.5%";
  }
  return glueNode;
};
var mathmlBuilder$7 = function(group, style) {
  const tbl = [];
  const numRows = group.body.length;
  const hlines = group.hLinesBeforeRow;
  for (let i = 0; i < numRows; i++) {
    const rw = group.body[i];
    const row = [];
    const cellLevel = group.scriptLevel === "text" ? StyleLevel.TEXT : group.scriptLevel === "script" ? StyleLevel.SCRIPT : StyleLevel.DISPLAY;
    for (let j = 0; j < rw.length; j++) {
      const mtd = new mathMLTree.MathNode(
        "mtd",
        [buildGroup$1(rw[j], style.withLevel(cellLevel))]
      );
      if (group.envClasses.includes("multline")) {
        const align2 = i === 0 ? "left" : i === numRows - 1 ? "right" : "center";
        mtd.setAttribute("columnalign", align2);
        if (align2 !== "center") {
          mtd.classes.push("tml-" + align2);
        }
      }
      row.push(mtd);
    }
    const numColumns = group.body[0].length;
    for (let k = 0; k < numColumns - rw.length; k++) {
      row.push(new mathMLTree.MathNode("mtd", [], style));
    }
    if (group.autoTag) {
      const tag = group.tags[i];
      let tagElement;
      if (tag === true) {
        tagElement = new mathMLTree.MathNode("mtext", [new Span(["tml-eqn"])]);
      } else if (tag === false) {
        tagElement = new mathMLTree.MathNode("mtext", [], []);
      } else {
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
      if (Array.isArray(group.tags[i])) {
        mtr.classes.push("tml-tageqn");
      }
    }
    if (i === 0 && hlines[0].length > 0) {
      if (hlines[0].length === 2) {
        mtr.children.forEach((cell) => {
          cell.style.borderTop = "0.15em double";
        });
      } else {
        mtr.children.forEach((cell) => {
          cell.style.borderTop = hlines[0][0] ? "0.06em dashed" : "0.06em solid";
        });
      }
    }
    if (hlines[i + 1].length > 0) {
      if (hlines[i + 1].length === 2) {
        mtr.children.forEach((cell) => {
          cell.style.borderBottom = "0.15em double";
        });
      } else {
        mtr.children.forEach((cell) => {
          cell.style.borderBottom = hlines[i + 1][0] ? "0.06em dashed" : "0.06em solid";
        });
      }
    }
    tbl.push(mtr);
  }
  if (group.envClasses.length > 0) {
    if (group.arraystretch && group.arraystretch !== 1) {
      const pad = String(1.4 * group.arraystretch - 0.8) + "ex";
      for (let i = 0; i < tbl.length; i++) {
        for (let j = 0; j < tbl[i].children.length; j++) {
          tbl[i].children[j].style.paddingTop = pad;
          tbl[i].children[j].style.paddingBottom = pad;
        }
      }
    }
    let sidePadding = group.envClasses.includes("abut") ? "0" : group.envClasses.includes("cases") ? "0" : group.envClasses.includes("small") ? "0.1389" : group.envClasses.includes("cd") ? "0.25" : "0.4";
    let sidePadUnit = "em";
    if (group.arraycolsep) {
      const arraySidePad = calculateSize(group.arraycolsep, style);
      sidePadding = arraySidePad.number.toFixed(4);
      sidePadUnit = arraySidePad.unit;
    }
    const numCols = tbl.length === 0 ? 0 : tbl[0].children.length;
    const sidePad = (j, hand) => {
      if (j === 0 && hand === 0) {
        return "0";
      }
      if (j === numCols - 1 && hand === 1) {
        return "0";
      }
      if (group.envClasses[0] !== "align") {
        return sidePadding;
      }
      if (hand === 1) {
        return "0";
      }
      if (group.autoTag) {
        return j % 2 ? "1" : "0";
      } else {
        return j % 2 ? "0" : "1";
      }
    };
    for (let i = 0; i < tbl.length; i++) {
      for (let j = 0; j < tbl[i].children.length; j++) {
        tbl[i].children[j].style.paddingLeft = `${sidePad(j, 0)}${sidePadUnit}`;
        tbl[i].children[j].style.paddingRight = `${sidePad(j, 1)}${sidePadUnit}`;
      }
    }
    const align2 = group.envClasses.includes("align") || group.envClasses.includes("alignat");
    for (let i = 0; i < tbl.length; i++) {
      const row = tbl[i];
      if (align2) {
        for (let j = 0; j < row.children.length; j++) {
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
    for (let i = 0; i < tbl.length; i++) {
      tbl[i].children[0].style.paddingLeft = "0em";
      if (tbl[i].children.length === tbl[0].children.length) {
        tbl[i].children[tbl[i].children.length - 1].style.paddingRight = "0em";
      }
    }
  }
  let table = new mathMLTree.MathNode("mtable", tbl);
  if (group.envClasses.length > 0) {
    if (group.envClasses.includes("jot")) {
      table.classes.push("tml-jot");
    } else if (group.envClasses.includes("small")) {
      table.classes.push("tml-small");
    }
  }
  if (group.scriptLevel === "display") {
    table.setAttribute("displaystyle", "true");
  }
  if (group.autoTag || group.envClasses.includes("multline")) {
    table.style.width = "100%";
  }
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
      const sep = cols[1].type === "separator" ? "0.15em double" : cols[0].separator === "|" ? "0.06em solid " : "0.06em dashed ";
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
        if (prevTypeWasAlign) {
          const sep = cols[i + 1].type === "separator" ? "0.15em double" : cols[i].separator === "|" ? "0.06em solid" : "0.06em dashed";
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
      const sep = cols[cols.length - 2].type === "separator" ? "0.15em double" : cols[cols.length - 1].separator === "|" ? "0.06em solid" : "0.06em dashed";
      for (const row of table.children) {
        row.children[row.children.length - 1].style.borderRight = sep;
        row.children[row.children.length - 1].style.paddingRight = "0.4em";
      }
    }
  }
  if (group.autoTag) {
    align = "left " + (align.length > 0 ? align : "center ") + "right ";
  }
  if (align) {
    table.setAttribute("columnalign", align.trim());
  }
  if (group.envClasses.includes("small")) {
    table = new mathMLTree.MathNode("mstyle", [table]);
    table.setAttribute("scriptlevel", "1");
  }
  return table;
};
var alignedHandler = function(context, args) {
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
      autoTag: isSplit ? void 0 : getAutoTag(context.envName),
      envClasses: ["abut", "jot"],
      // set row spacing & provisional column spacing
      maxNumCols: context.envName === "split" ? 2 : void 0,
      leqno: context.parser.settings.leqno
    },
    "display"
  );
  let numMaths;
  let numCols = 0;
  const isAlignedAt = context.envName.indexOf("at") > -1;
  if (args[0] && isAlignedAt) {
    let arg0 = "";
    for (let i = 0; i < args[0].body.length; i++) {
      const textord2 = assertNodeType(args[0].body[i], "textord");
      arg0 += textord2.text;
    }
    if (isNaN(arg0)) {
      throw new ParseError("The alignat enviroment requires a numeric first argument.");
    }
    numMaths = Number(arg0);
    numCols = numMaths * 2;
  }
  res.body.forEach(function(row) {
    if (isAlignedAt) {
      const curMaths = row.length / 2;
      if (numMaths < curMaths) {
        throw new ParseError(
          `Too many math in a row: expected ${numMaths}, but got ${curMaths}`,
          row[0]
        );
      }
    } else if (numCols < row.length) {
      numCols = row.length;
    }
  });
  for (let i = 0; i < numCols; ++i) {
    let align = "r";
    if (i % 2 === 1) {
      align = "l";
    }
    cols[i] = {
      type: "align",
      align
    };
  }
  if (context.envName === "split") ;
  else if (isAlignedAt) {
    res.envClasses.push("alignat");
  } else {
    res.envClasses[0] = "align";
  }
  return res;
};
defineEnvironment({
  type: "array",
  names: ["array", "darray"],
  props: {
    numArgs: 1
  },
  handler(context, args) {
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
    const delimiters2 = {
      matrix: null,
      pmatrix: ["(", ")"],
      bmatrix: ["[", "]"],
      Bmatrix: ["\\{", "\\}"],
      vmatrix: ["|", "|"],
      Vmatrix: ["\\Vert", "\\Vert"]
    }[context.envName.replace("*", "")];
    let colAlign = "c";
    const payload = {
      envClasses: [],
      cols: []
    };
    if (context.envName.charAt(context.envName.length - 1) === "*") {
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
    return delimiters2 ? {
      type: "leftright",
      mode: context.mode,
      body: [res],
      left: delimiters2[0],
      right: delimiters2[1],
      rightColor: void 0,
      // \right uninfluenced by \color in array
      arraystretch,
      arraycolsep
    } : res;
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
    const symNode = checkSymbolNodeType(args[0]);
    const colalign = symNode ? [args[0]] : assertNodeType(args[0], "ordgroup").body;
    const cols = colalign.map(function(nde) {
      const node = assertSymbolNodeType(nde);
      const ca = node.text;
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
      rightColor: void 0
    };
  },
  mathmlBuilder: mathmlBuilder$7
});
defineEnvironment({
  type: "array",
  names: ["align", "align*", "aligned", "split"],
  props: {
    numArgs: 0
  },
  handler: alignedHandler,
  mathmlBuilder: mathmlBuilder$7
});
defineEnvironment({
  type: "array",
  names: ["alignat", "alignat*", "alignedat"],
  props: {
    numArgs: 1
  },
  handler: alignedHandler,
  mathmlBuilder: mathmlBuilder$7
});
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
defineFunction({
  type: "text",
  // Doesn't matter what this is.
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
var environments = _environments;
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
      if (!Object.prototype.hasOwnProperty.call(environments, envName)) {
        throw new ParseError("No such environment: " + envName, nameGroup);
      }
      const env = environments[envName];
      const { args: args2, optArgs } = parser.parseArguments("\\begin{" + envName + "}", env);
      const context = {
        mode: parser.mode,
        envName,
        parser
      };
      const result = env.handler(context, args2, optArgs);
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
var isLongVariableName = (group, font) => {
  if (font !== "mathrm" || group.body.type !== "ordgroup" || group.body.body.length === 1) {
    return false;
  }
  if (group.body.body[0].type !== "mathord") {
    return false;
  }
  for (let i = 1; i < group.body.body.length; i++) {
    const parseNodeType = group.body.body[i].type;
    if (!(parseNodeType === "mathord" || parseNodeType === "textord" && !isNaN(group.body.body[i].text))) {
      return false;
    }
  }
  return true;
};
var mathmlBuilder$6 = (group, style) => {
  const font = group.font;
  const newStyle = style.withFont(font);
  const mathGroup = buildGroup$1(group.body, newStyle);
  if (mathGroup.children.length === 0) {
    return mathGroup;
  }
  if (font === "boldsymbol" && ["mo", "mpadded", "mrow"].includes(mathGroup.type)) {
    mathGroup.style.fontWeight = "bold";
    return mathGroup;
  }
  if (isLongVariableName(group, font)) {
    const mi2 = mathGroup.children[0].children[0];
    delete mi2.attributes.mathvariant;
    for (let i = 1; i < mathGroup.children.length; i++) {
      mi2.children[0].text += mathGroup.children[i].type === "mn" ? mathGroup.children[i].children[0].text : mathGroup.children[i].children[0].children[0].text;
    }
    const bogus = new mathMLTree.MathNode("mtext", new mathMLTree.TextNode("\u200B"));
    return new mathMLTree.MathNode("mrow", [bogus, mi2]);
  }
  let canConsolidate = mathGroup.children[0].type === "mo";
  for (let i = 1; i < mathGroup.children.length; i++) {
    if (mathGroup.children[i].type === "mo" && font === "boldsymbol") {
      mathGroup.children[i].style.fontWeight = "bold";
    }
    if (mathGroup.children[i].type !== "mi") {
      canConsolidate = false;
    }
    const localVariant = mathGroup.children[i].attributes && mathGroup.children[i].attributes.mathvariant || "";
    if (localVariant !== "normal") {
      canConsolidate = false;
    }
  }
  if (!canConsolidate) {
    return mathGroup;
  }
  const mi = mathGroup.children[0];
  for (let i = 1; i < mathGroup.children.length; i++) {
    mi.children.push(mathGroup.children[i].children[0]);
  }
  if (mi.attributes.mathvariant && mi.attributes.mathvariant === "normal") {
    const bogus = new mathMLTree.MathNode("mtext", new mathMLTree.TextNode("\u200B"));
    return new mathMLTree.MathNode("mrow", [bogus, mi]);
  }
  return mi;
};
var fontAliases = {
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
      mode,
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
var stylArray = ["display", "text", "script", "scriptscript"];
var scriptLevel = { auto: -1, display: 0, text: 0, script: 1, scriptscript: 2 };
var mathmlBuilder$5 = (group, style) => {
  const childOptions = group.scriptLevel === "auto" ? style.incrementLevel() : group.scriptLevel === "display" ? style.withLevel(StyleLevel.TEXT) : group.scriptLevel === "text" ? style.withLevel(StyleLevel.SCRIPT) : style.withLevel(StyleLevel.SCRIPTSCRIPT);
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
    "\\\\atopfrac",
    // can’t be entered directly
    "\\\\bracefrac",
    "\\\\brackfrac"
    // ditto
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
    let scriptLevel2 = "auto";
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
        scriptLevel2 = "display";
        break;
      case "\\tfrac":
      case "\\tbinom":
        scriptLevel2 = "text";
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
      scriptLevel: scriptLevel2,
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
var delimFromValue = function(delimString) {
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
    const leftNode = normalizeArgument(args[0]);
    const leftDelim = leftNode.type === "atom" && leftNode.family === "open" ? delimFromValue(leftNode.text) : null;
    const rightNode = normalizeArgument(args[1]);
    const rightDelim = rightNode.type === "atom" && rightNode.family === "close" ? delimFromValue(rightNode.text) : null;
    const barNode = assertNodeType(args[2], "size");
    let hasBarLine;
    let barSize = null;
    if (barNode.isBlank) {
      hasBarLine = true;
    } else {
      barSize = barNode.value;
      hasBarLine = barSize.number > 0;
    }
    let scriptLevel2 = "auto";
    let styl = args[3];
    if (styl.type === "ordgroup") {
      if (styl.body.length > 0) {
        const textOrd = assertNodeType(styl.body[0], "textord");
        scriptLevel2 = stylArray[Number(textOrd.text)];
      }
    } else {
      styl = assertNodeType(styl, "textord");
      scriptLevel2 = stylArray[Number(styl.text)];
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
      scriptLevel: scriptLevel2
    };
  },
  mathmlBuilder: mathmlBuilder$5
});
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
    return consolidateText(mrow);
  }
});
var mathmlBuilder$4 = (group, style) => {
  const accentNode2 = stretchy.mathMLnode(group.label);
  accentNode2.style["math-depth"] = 0;
  return new mathMLTree.MathNode(group.isOver ? "mover" : "munder", [
    buildGroup$1(group.base, style),
    accentNode2
  ]);
};
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
    if (!parser.settings.isTrusted({
      command: "\\href",
      url: href
    })) {
      throw new ParseError(`Function "\\href" is not trusted`, token);
    }
    return {
      type: "href",
      mode: parser.mode,
      href,
      body: ordargument(body)
    };
  },
  mathmlBuilder: (group, style) => {
    const math2 = new MathNode("math", [buildExpressionRow(group.body, style)]);
    const anchorNode = new AnchorNode(group.href, [], [math2]);
    return anchorNode;
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
    if (!parser.settings.isTrusted({
      command: "\\url",
      url: href
    })) {
      throw new ParseError(`Function "\\url" is not trusted`, token);
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
      throw new ParseError(`Function "${funcName}" is disabled in strict mode`, token);
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
      throw new ParseError(`Function "${funcName}" is not trusted`, token);
    }
    return {
      type: "html",
      mode: parser.mode,
      attributes,
      body: ordargument(body)
    };
  },
  mathmlBuilder: (group, style) => {
    const element = buildExpressionRow(group.body, style);
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
var sizeData = function(str) {
  if (/^[-+]? *(\d+(\.\d*)?|\.\d+)$/.test(str)) {
    return { number: +str, unit: "bp" };
  } else {
    const match = /([-+]?) *(\d+(?:\.\d*)?|\.\d+) *([a-z]{2})/.exec(str);
    if (!match) {
      throw new ParseError("Invalid size: '" + str + "' in \\includegraphics");
    }
    const data = {
      number: +(match[1] + match[2]),
      // sign + magnitude, cast to number
      unit: match[3]
    };
    if (!validUnit(data)) {
      throw new ParseError("Invalid unit: '" + data.unit + "' in \\includegraphics.");
    }
    return data;
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
    let height = { number: 0.9, unit: "em" };
    let totalheight = { number: 0, unit: "em" };
    let alt = "";
    if (optArgs[0]) {
      const attributeStr = assertNodeType(optArgs[0], "raw").string;
      const attributes = attributeStr.split(",");
      for (let i = 0; i < attributes.length; i++) {
        const keyVal = attributes[i].split("=");
        if (keyVal.length === 2) {
          const str = keyVal[1].trim();
          switch (keyVal[0].trim()) {
            case "alt":
              alt = str;
              break;
            case "width":
              width = sizeData(str);
              break;
            case "height":
              height = sizeData(str);
              break;
            case "totalheight":
              totalheight = sizeData(str);
              break;
            default:
              throw new ParseError("Invalid key: '" + keyVal[0] + "' in \\includegraphics.");
          }
        }
      }
    }
    const src = assertNodeType(args[0], "url").url;
    if (alt === "") {
      alt = src;
      alt = alt.replace(/^.*[\\/]/, "");
      alt = alt.substring(0, alt.lastIndexOf("."));
    }
    if (!parser.settings.isTrusted({
      command: "\\includegraphics",
      url: src
    })) {
      throw new ParseError(`Function "\\includegraphics" is not trusted`, token);
    }
    return {
      type: "includegraphics",
      mode: parser.mode,
      alt,
      width,
      height,
      totalheight,
      src
    };
  },
  mathmlBuilder: (group, style) => {
    const height = calculateSize(group.height, style);
    const depth = { number: 0, unit: "em" };
    if (group.totalheight.number > 0) {
      if (group.totalheight.unit === height.unit && group.totalheight.number > height.number) {
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
    return new mathMLTree.MathNode("mtext", [node]);
  }
});
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
      const mathFunction = funcName[1] === "m";
      const muUnit = size.value.unit === "mu";
      if (mathFunction) {
        if (!muUnit) {
          throw new ParseError(`LaTeX's ${funcName} supports only mu units, not ${size.value.unit} units`, token);
        }
        if (parser.mode !== "math") {
          throw new ParseError(`LaTeX's ${funcName} works only in math mode`, token);
        }
      } else {
        if (muUnit) {
          throw new ParseError(`LaTeX's ${funcName} doesn't support mu units`, token);
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
var spaceCharacter = function(width) {
  if (width >= 0.05555 && width <= 0.05556) {
    return "\u200A";
  } else if (width >= 0.1666 && width <= 0.1667) {
    return "\u2009";
  } else if (width >= 0.2222 && width <= 0.2223) {
    return "\u2005";
  } else if (width >= 0.2777 && width <= 0.2778) {
    return "\u2005\u200A";
  } else {
    return "";
  }
};
var invalidIdRegEx = /[^A-Za-z_0-9-]/g;
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
    const node = new mathMLTree.MathNode("mrow", [], ["tml-label"]);
    if (group.string.length > 0) {
      node.setLabel(group.string);
    }
    return node;
  }
});
var textModeLap = ["\\clap", "\\llap", "\\rlap"];
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
 Try \\math${funcName.slice(1)}`, token);
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
    };
  },
  mathmlBuilder: (group, style) => {
    let strut;
    if (group.alignment === "llap") {
      const phantomInner = buildExpression(ordargument(group.body), style);
      const phantom = new mathMLTree.MathNode("mphantom", phantomInner);
      strut = new mathMLTree.MathNode("mpadded", [phantom]);
      strut.setAttribute("width", "0px");
    }
    const inner2 = buildGroup$1(group.body, style);
    let node;
    if (group.alignment === "llap") {
      inner2.style.position = "absolute";
      inner2.style.right = "0";
      inner2.style.bottom = `0`;
      node = new mathMLTree.MathNode("mpadded", [strut, inner2]);
    } else {
      node = new mathMLTree.MathNode("mpadded", [inner2]);
    }
    if (group.alignment === "rlap") {
      if (group.body.body.length > 0 && group.body.body[0].type === "genfrac") {
        node.setAttribute("lspace", "0.16667em");
      }
    } else {
      const offset2 = group.alignment === "llap" ? "-1" : "-0.5";
      node.setAttribute("lspace", offset2 + "width");
      if (group.alignment === "llap") {
        node.style.position = "relative";
      } else {
        node.style.display = "flex";
        node.style.justifyContent = "center";
      }
    }
    node.setAttribute("width", "0px");
    return node;
  }
});
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
    const close2 = funcName === "\\(" ? "\\)" : "$";
    const body = parser.parseExpression(false, close2);
    parser.expect(close2);
    parser.switchMode(outerMode);
    return {
      type: "ordgroup",
      mode: parser.mode,
      body
    };
  }
});
defineFunction({
  type: "text",
  // Doesn't matter what this is.
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
var chooseStyle = (group, style) => {
  switch (style.level) {
    case StyleLevel.DISPLAY:
      return group.display;
    case StyleLevel.TEXT:
      return group.text;
    case StyleLevel.SCRIPT:
      return group.script;
    case StyleLevel.SCRIPTSCRIPT:
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
var textAtomTypes = ["text", "textord", "mathord", "atom"];
var padding = (width) => {
  const node = new mathMLTree.MathNode("mspace");
  node.setAttribute("width", width + "em");
  return node;
};
function mathmlBuilder$3(group, style) {
  let node;
  const inner2 = buildExpression(group.body, style);
  if (group.mclass === "minner") {
    node = new mathMLTree.MathNode("mpadded", inner2);
  } else if (group.mclass === "mord") {
    if (group.isCharacterBox || inner2[0].type === "mathord") {
      node = inner2[0];
      node.type = "mi";
      if (node.children.length === 1 && node.children[0].text && node.children[0].text === "\u2207") {
        node.setAttribute("mathvariant", "normal");
      }
    } else {
      node = new mathMLTree.MathNode("mi", inner2);
    }
  } else {
    node = new mathMLTree.MathNode("mrow", inner2);
    if (group.mustPromote) {
      node = inner2[0];
      node.type = "mo";
      if (group.isCharacterBox && group.body[0].text && /[A-Za-z]/.test(group.body[0].text)) {
        node.setAttribute("mathvariant", "italic");
      }
    } else {
      node = new mathMLTree.MathNode("mrow", inner2);
    }
    const doSpacing = style.level < 2;
    if (node.type === "mrow") {
      if (doSpacing) {
        if (group.mclass === "mbin") {
          node.children.unshift(padding(0.2222));
          node.children.push(padding(0.2222));
        } else if (group.mclass === "mrel") {
          node.children.unshift(padding(0.2778));
          node.children.push(padding(0.2778));
        } else if (group.mclass === "mpunct") {
          node.children.push(padding(0.1667));
        } else if (group.mclass === "minner") {
          node.children.unshift(padding(0.0556));
          node.children.push(padding(0.0556));
        }
      }
    } else {
      if (group.mclass === "mbin") {
        node.attributes.lspace = doSpacing ? "0.2222em" : "0";
        node.attributes.rspace = doSpacing ? "0.2222em" : "0";
      } else if (group.mclass === "mrel") {
        node.attributes.lspace = doSpacing ? "0.2778em" : "0";
        node.attributes.rspace = doSpacing ? "0.2778em" : "0";
      } else if (group.mclass === "mpunct") {
        node.attributes.lspace = "0em";
        node.attributes.rspace = doSpacing ? "0.1667em" : "0";
      } else if (group.mclass === "mopen" || group.mclass === "mclose") {
        node.attributes.lspace = "0em";
        node.attributes.rspace = "0em";
      } else if (group.mclass === "minner" && doSpacing) {
        node.attributes.lspace = "0.0556em";
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
    const isCharacterBox2 = utils.isCharacterBox(body);
    let mustPromote = true;
    const mord = { type: "mathord", text: "", mode: parser.mode };
    const arr = body.body ? body.body : [body];
    for (const arg of arr) {
      if (textAtomTypes.includes(arg.type)) {
        if (symbols[parser.mode][arg.text]) {
          mord.text += symbols[parser.mode][arg.text].replace;
        } else if (arg.text) {
          mord.text += arg.text;
        } else if (arg.body) {
          arg.body.map((e) => {
            mord.text += e.text;
          });
        }
      } else {
        mustPromote = false;
        break;
      }
    }
    return {
      type: "mclass",
      mode: parser.mode,
      mclass: "m" + funcName.slice(5),
      body: ordargument(mustPromote ? mord : body),
      isCharacterBox: isCharacterBox2,
      mustPromote
    };
  },
  mathmlBuilder: mathmlBuilder$3
});
var binrelClass = (arg) => {
  const atom = arg.type === "ordgroup" && arg.body.length ? arg.body[0] : arg;
  if (atom.type === "atom" && (atom.family === "bin" || atom.family === "rel")) {
    return "m" + atom.family;
  } else {
    return "mord";
  }
};
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
var buildGroup = (el, style, noneNode) => {
  if (!el) {
    return noneNode;
  }
  const node = buildGroup$1(el, style);
  if (node.type === "mrow" && node.children.length === 0) {
    return noneNode;
  }
  return node;
};
defineFunction({
  type: "multiscript",
  names: ["\\sideset", "\\pres@cript"],
  // See macros.js for \prescript
  props: {
    numArgs: 3
  },
  handler({ parser, funcName, token }, args) {
    if (args[2].body.length === 0) {
      throw new ParseError(funcName + `cannot parse an empty base.`);
    }
    const base = args[2].body[0];
    if (parser.settings.strict && funcName === "\\sideset" && !base.symbol) {
      throw new ParseError(`The base of \\sideset must be a big operator. Try \\prescript.`);
    }
    if (args[0].body.length > 0 && args[0].body[0].type !== "supsub" || args[1].body.length > 0 && args[1].body[0].type !== "supsub") {
      throw new ParseError("\\sideset can parse only subscripts and superscripts in its first two arguments", token);
    }
    const prescripts = args[0].body.length > 0 ? args[0].body[0] : null;
    const postscripts = args[1].body.length > 0 ? args[1].body[0] : null;
    if (!prescripts && !postscripts) {
      return base;
    } else if (!prescripts) {
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
      };
    } else {
      return {
        type: "multiscript",
        mode: parser.mode,
        isSideset: funcName === "\\sideset",
        prescripts,
        postscripts,
        base
      };
    }
  },
  mathmlBuilder(group, style) {
    const base = buildGroup$1(group.base, style);
    const prescriptsNode = new mathMLTree.MathNode("mprescripts");
    const noneNode = new mathMLTree.MathNode("none");
    let children = [];
    const preSub = buildGroup(group.prescripts.sub, style, noneNode);
    const preSup = buildGroup(group.prescripts.sup, style, noneNode);
    if (group.isSideset) {
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
    const isCharacterBox2 = utils.isCharacterBox(args[0]);
    let body;
    if (isCharacterBox2) {
      body = ordargument(args[0]);
      if (body[0].text.charAt(0) === "\\") {
        body[0].text = symbols.math[body[0].text].replace;
      }
      body[0].text = body[0].text.slice(0, 1) + "\u0338" + body[0].text.slice(1);
    } else {
      const notNode = { type: "textord", mode: "math", text: "\u0338" };
      const kernNode = { type: "kern", mode: "math", dimension: { number: -0.6, unit: "em" } };
      body = [notNode, kernNode, args[0]];
    }
    return {
      type: "not",
      mode: parser.mode,
      body,
      isCharacterBox: isCharacterBox2
    };
  },
  mathmlBuilder(group, style) {
    if (group.isCharacterBox) {
      const inner2 = buildExpression(group.body, style, true);
      return inner2[0];
    } else {
      return buildExpressionRow(group.body, style);
    }
  }
});
var ordAtomTypes = ["textord", "mathord", "atom"];
var noSuccessor = ["\\smallint"];
var ordTypes = ["textord", "mathord", "ordgroup", "close", "leftright", "font"];
var setSpacing = (node) => {
  node.attributes.lspace = "0.1667em";
  node.attributes.rspace = "0.1667em";
};
var mathmlBuilder$2 = (group, style) => {
  let node;
  if (group.symbol) {
    node = new MathNode("mo", [makeText(group.name, group.mode)]);
    if (noSuccessor.includes(group.name)) {
      node.setAttribute("largeop", "false");
    } else {
      node.setAttribute("movablelimits", "false");
    }
    if (group.fromMathOp) {
      setSpacing(node);
    }
  } else if (group.body) {
    node = new MathNode("mo", buildExpression(group.body, style));
    if (group.fromMathOp) {
      setSpacing(node);
    }
  } else {
    node = new MathNode("mi", [new TextNode2(group.name.slice(1))]);
    if (!group.parentIsSupSub) {
      const operator = new MathNode("mo", [makeText("\u2061", "text")]);
      const row = [node, operator];
      if (group.needsLeadingSpace) {
        const lead = new MathNode("mspace");
        lead.setAttribute("width", "0.1667em");
        row.unshift(lead);
      }
      if (!group.isFollowedByDelimiter) {
        const trail = new MathNode("mspace");
        trail.setAttribute("width", "0.1667em");
        row.push(trail);
      }
      node = new MathNode("mrow", row);
    }
  }
  return node;
};
var singleCharBigOps = {
  "\u220F": "\\prod",
  "\u2210": "\\coprod",
  "\u2211": "\\sum",
  "\u22C0": "\\bigwedge",
  "\u22C1": "\\bigvee",
  "\u22C2": "\\bigcap",
  "\u22C3": "\\bigcup",
  "\u2A00": "\\bigodot",
  "\u2A01": "\\bigoplus",
  "\u2A02": "\\bigotimes",
  "\u2A04": "\\biguplus",
  "\u2A05": "\\bigsqcap",
  "\u2A06": "\\bigsqcup",
  "\u2A03": "\\bigcupdot",
  "\u2A07": "\\bigdoublevee",
  "\u2A08": "\\bigdoublewedge",
  "\u2A09": "\\bigtimes"
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
    "\u22C0",
    "\u22C1",
    "\u22C2",
    "\u22C3",
    "\u2A00",
    "\u2A01",
    "\u2A02",
    "\u2A04",
    "\u2A06"
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
      stack: false,
      // This is true for \stackrel{}, not here.
      name: fName
    };
  },
  mathmlBuilder: mathmlBuilder$2
});
defineFunction({
  type: "op",
  names: ["\\mathop"],
  props: {
    numArgs: 1,
    primitive: true
  },
  handler: ({ parser }, args) => {
    const body = args[0];
    const arr = body.body ? body.body : [body];
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
var singleCharIntegrals = {
  "\u222B": "\\int",
  "\u222C": "\\iint",
  "\u222D": "\\iiint",
  "\u222E": "\\oint",
  "\u222F": "\\oiint",
  "\u2230": "\\oiiint",
  "\u2231": "\\intclockwise",
  "\u2232": "\\varointclockwise",
  "\u2A0C": "\\iiiint",
  "\u2A0D": "\\intbar",
  "\u2A0E": "\\intBar",
  "\u2A0F": "\\fint",
  "\u2A12": "\\rppolint",
  "\u2A13": "\\scpolint",
  "\u2A15": "\\pointint",
  "\u2A16": "\\sqint",
  "\u2A17": "\\intlarhk",
  "\u2A18": "\\intx",
  "\u2A19": "\\intcap",
  "\u2A1A": "\\intcup"
};
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
    "\u222B",
    "\u222C",
    "\u222D",
    "\u222E",
    "\u222F",
    "\u2230",
    "\u2231",
    "\u2232",
    "\u2A0C",
    "\u2A0D",
    "\u2A0E",
    "\u2A0F",
    "\u2A12",
    "\u2A13",
    "\u2A15",
    "\u2A16",
    "\u2A17",
    "\u2A18",
    "\u2A19",
    "\u2A1A"
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
var mathmlBuilder$1 = (group, style) => {
  let expression = buildExpression(group.body, style.withFont("mathrm"));
  let isAllString = true;
  for (let i = 0; i < expression.length; i++) {
    let node = expression[i];
    if (node instanceof mathMLTree.MathNode) {
      if (node.type === "mrow" && node.children.length === 1 && node.children[0] instanceof mathMLTree.MathNode) {
        node = node.children[0];
      }
      switch (node.type) {
        case "mi":
        case "mn":
        case "ms":
        case "mtext":
          break;
        // Do nothing yet.
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
          break;
        case "mo": {
          const child = node.children[0];
          if (node.children.length === 1 && child instanceof mathMLTree.TextNode) {
            child.text = child.text.replace(/\u2212/, "-").replace(/\u2217/, "*");
          } else {
            isAllString = false;
          }
          break;
        }
        default:
          isAllString = false;
      }
    } else {
      isAllString = false;
    }
  }
  if (isAllString) {
    const word = expression.map((node) => node.toText()).join("");
    expression = [new mathMLTree.TextNode(word)];
  } else if (expression.length === 1 && ["mover", "munder"].includes(expression[0].type) && (expression[0].children[0].type === "mi" || expression[0].children[0].type === "mtext")) {
    expression[0].children[0].type = "mi";
    if (group.parentIsSupSub) {
      return new mathMLTree.MathNode("mrow", expression);
    } else {
      const operator = new mathMLTree.MathNode("mo", [makeText("\u2061", "text")]);
      return mathMLTree.newDocumentFragment([expression[0], operator]);
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
    const operator = new mathMLTree.MathNode("mo", [makeText("\u2061", "text")]);
    const fragment = [wrapper, operator];
    if (group.needsLeadingSpace) {
      const space = new mathMLTree.MathNode("mspace");
      space.setAttribute("width", "0.1667em");
      fragment.unshift(space);
    }
    if (!group.isFollowedByDelimiter) {
      const trail = new mathMLTree.MathNode("mspace");
      trail.setAttribute("width", "0.1667em");
      fragment.push(trail);
    }
    return mathMLTree.newDocumentFragment(fragment);
  }
  return wrapper;
};
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
      alwaysHandleSupSub: funcName === "\\operatornamewithlimits",
      limits: false,
      parentIsSupSub: false,
      isFollowedByDelimiter: isDelimiter(next),
      needsLeadingSpace: prevAtomType.length > 0 && ordTypes.includes(prevAtomType)
    };
  },
  mathmlBuilder: mathmlBuilder$1
});
defineMacro(
  "\\operatorname",
  "\\@ifstar\\operatornamewithlimits\\operatorname@"
);
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
    const inner2 = buildExpression(group.body, style);
    return new mathMLTree.MathNode("mphantom", inner2);
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
    const inner2 = buildExpression(ordargument(group.body), style);
    const phantom = new mathMLTree.MathNode("mphantom", inner2);
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
    const inner2 = buildExpression(ordargument(group.body), style);
    const phantom = new mathMLTree.MathNode("mphantom", inner2);
    const node = new mathMLTree.MathNode("mpadded", [phantom]);
    node.setAttribute("width", "0px");
    return node;
  }
});
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
    };
  },
  mathmlBuilder(group, style) {
    const inner2 = buildExpression(group.body, style);
    const node = wrapWithMstyle(inner2);
    node.setAttribute("style", "font-weight:bold");
    return node;
  }
});
var mathmlBuilder = (group, style) => {
  const newStyle = style.withLevel(StyleLevel.TEXT);
  const node = new mathMLTree.MathNode("mpadded", [buildGroup$1(group.body, newStyle)]);
  const dy = calculateSize(group.dy, style);
  node.setAttribute("voffset", dy.number + dy.unit);
  if (dy.number > 0) {
    node.style.padding = dy.number + dy.unit + " 0 0 0";
  } else {
    node.style.padding = "0 0 " + Math.abs(dy.number) + dy.unit + " 0";
  }
  return node;
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
    if (funcName === "\\lower") {
      amount.number *= -1;
    }
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
    const classes = group.funcName === "\\ref" ? ["tml-ref"] : ["tml-ref", "tml-eqref"];
    return new AnchorNode("#" + group.string, classes, null);
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
    return node;
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
    const shift = group.shift ? calculateSize(group.shift, style) : { number: 0, unit: "em" };
    const color = style.color && style.getColor() || "black";
    const rule = new mathMLTree.MathNode("mspace");
    if (width.number > 0 && height.number > 0) {
      rule.setAttribute("mathbackground", color);
    }
    rule.setAttribute("width", width.number + width.unit);
    rule.setAttribute("height", height.number + height.unit);
    if (shift.number === 0) {
      return rule;
    }
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
var sizeMap = {
  "\\tiny": 0.5,
  "\\sixptsize": 0.6,
  "\\Tiny": 0.6,
  "\\scriptsize": 0.7,
  "\\footnotesize": 0.8,
  "\\small": 0.9,
  "\\normalsize": 1,
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
    const inner2 = buildExpression(group.body, newStyle);
    const node = wrapWithMstyle(inner2);
    const factor = (sizeMap[group.funcName] / style.fontSize).toFixed(4);
    node.setAttribute("mathsize", factor + "em");
    return node;
  }
});
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
      let letter = "";
      for (let i = 0; i < tbArg.body.length; ++i) {
        const node = tbArg.body[i];
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
    return index ? new mathMLTree.MathNode("mroot", [
      buildGroup$1(body, style),
      buildGroup$1(index, style.incrementLevel())
    ]) : new mathMLTree.MathNode("msqrt", [buildGroup$1(body, style)]);
  }
});
var styleMap = {
  display: 0,
  text: 1,
  script: 2,
  scriptscript: 3
};
var styleAttributes = {
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
    const body = parser.parseExpression(true, breakOnTokenText, true);
    const scriptLevel2 = funcName.slice(1, funcName.length - 5);
    return {
      type: "styling",
      mode: parser.mode,
      // Figure out what scriptLevel to use by pulling out the scriptLevel from
      // the function name
      scriptLevel: scriptLevel2,
      body
    };
  },
  mathmlBuilder(group, style) {
    const newStyle = style.withLevel(styleMap[group.scriptLevel]);
    const inner2 = buildExpression(group.body, newStyle);
    const node = wrapWithMstyle(inner2);
    const attr = styleAttributes[group.scriptLevel];
    node.setAttribute("scriptlevel", attr[0]);
    node.setAttribute("displaystyle", attr[1]);
    return node;
  }
});
var symbolRegEx = /^m(over|under|underover)$/;
defineFunctionBuilders({
  type: "supsub",
  mathmlBuilder(group, style) {
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
    if (group.base && !group.base.stack && (group.base.type === "op" || group.base.type === "operatorname")) {
      group.base.parentIsSupSub = true;
      appendApplyFunction = !group.base.symbol;
      appendSpace = appendApplyFunction && !group.isFollowedByDelimiter;
      needsLeadingSpace = group.base.needsLeadingSpace;
    }
    const children = group.base && group.base.stack ? [buildGroup$1(group.base.body[0], style)] : [buildGroup$1(group.base, style)];
    const childStyle = style.inSubOrSup();
    if (group.sub) {
      const sub = buildGroup$1(group.sub, childStyle);
      if (style.level === 3) {
        sub.setAttribute("scriptlevel", "2");
      }
      children.push(sub);
    }
    if (group.sup) {
      const sup = buildGroup$1(group.sup, childStyle);
      if (style.level === 3) {
        sup.setAttribute("scriptlevel", "2");
      }
      const testNode = sup.type === "mrow" ? sup.children[0] : sup;
      if (testNode && testNode.type === "mo" && testNode.classes.includes("tml-prime") && group.base && group.base.text && "fF".indexOf(group.base.text) > -1) {
        testNode.classes.push("prime-pad");
      }
      children.push(sup);
    }
    let nodeType;
    if (isBrace) {
      nodeType = isOver ? "mover" : "munder";
    } else if (!group.sub) {
      const base = group.base;
      if (base && base.type === "op" && base.limits && (style.level === StyleLevel.DISPLAY || base.alwaysHandleSupSub)) {
        nodeType = "mover";
      } else if (base && base.type === "operatorname" && base.alwaysHandleSupSub && (base.limits || style.level === StyleLevel.DISPLAY)) {
        nodeType = "mover";
      } else {
        nodeType = "msup";
      }
    } else if (!group.sup) {
      const base = group.base;
      if (base && base.type === "op" && base.limits && (style.level === StyleLevel.DISPLAY || base.alwaysHandleSupSub)) {
        nodeType = "munder";
      } else if (base && base.type === "operatorname" && base.alwaysHandleSupSub && (base.limits || style.level === StyleLevel.DISPLAY)) {
        nodeType = "munder";
      } else {
        nodeType = "msub";
      }
    } else {
      const base = group.base;
      if (base && (base.type === "op" && base.limits || base.type === "multiscript") && (style.level === StyleLevel.DISPLAY || base.alwaysHandleSupSub)) {
        nodeType = "munderover";
      } else if (base && base.type === "operatorname" && base.alwaysHandleSupSub && (style.level === StyleLevel.DISPLAY || base.limits)) {
        nodeType = "munderover";
      } else {
        nodeType = "msubsup";
      }
    }
    let node = new mathMLTree.MathNode(nodeType, children);
    if (appendApplyFunction) {
      const operator = new mathMLTree.MathNode("mo", [makeText("\u2061", "text")]);
      if (needsLeadingSpace) {
        const space = new mathMLTree.MathNode("mspace");
        space.setAttribute("width", "0.1667em");
        node = mathMLTree.newDocumentFragment([space, node, operator]);
      } else {
        node = mathMLTree.newDocumentFragment([node, operator]);
      }
      if (appendSpace) {
        const space = new mathMLTree.MathNode("mspace");
        space.setAttribute("width", "0.1667em");
        node.children.push(space);
      }
    } else if (symbolRegEx.test(nodeType)) {
      node = new mathMLTree.MathNode("mrow", [node]);
    }
    return node;
  }
});
var short = [
  "\\shortmid",
  "\\nshortmid",
  "\\shortparallel",
  "\\nshortparallel",
  "\\smallsetminus"
];
var arrows = ["\\Rsh", "\\Lsh", "\\restriction"];
var isArrow = (str) => {
  if (str.length === 1) {
    const codePoint = str.codePointAt(0);
    return 8591 < codePoint && codePoint < 8704;
  }
  return str.indexOf("arrow") > -1 || str.indexOf("harpoon") > -1 || arrows.includes(str);
};
defineFunctionBuilders({
  type: "atom",
  mathmlBuilder(group, style) {
    const node = new mathMLTree.MathNode("mo", [makeText(group.text, group.mode)]);
    if (group.family === "punct") {
      node.setAttribute("separator", "true");
    } else if (group.family === "open" || group.family === "close") {
      if (group.family === "open") {
        node.setAttribute("form", "prefix");
        node.setAttribute("stretchy", "false");
      } else if (group.family === "close") {
        node.setAttribute("form", "postfix");
        node.setAttribute("stretchy", "false");
      }
    } else if (group.text === "\\mid") {
      node.setAttribute("lspace", "0.22em");
      node.setAttribute("rspace", "0.22em");
      node.setAttribute("stretchy", "false");
    } else if (group.family === "rel" && isArrow(group.text)) {
      node.setAttribute("stretchy", "false");
    } else if (short.includes(group.text)) {
      node.setAttribute("mathsize", "70%");
    } else if (group.text === ":") {
      node.attributes.lspace = "0.2222em";
      node.attributes.rspace = "0.2222em";
    }
    return node;
  }
});
var fontMap = {
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
var getVariant = function(group, style) {
  if (style.fontFamily === "texttt") {
    return "monospace";
  } else if (style.fontFamily === "textsc") {
    return "normal";
  } else if (style.fontFamily === "textsf") {
    if (style.fontShape === "textit" && style.fontWeight === "textbf") {
      return "sans-serif-bold-italic";
    } else if (style.fontShape === "textit") {
      return "sans-serif-italic";
    } else if (style.fontWeight === "textbf") {
      return "sans-serif-bold";
    } else {
      return "sans-serif";
    }
  } else if (style.fontShape === "textit" && style.fontWeight === "textbf") {
    return "bold-italic";
  } else if (style.fontShape === "textit") {
    return "italic";
  } else if (style.fontWeight === "textbf") {
    return "bold";
  }
  const font = style.font;
  if (!font || font === "mathnormal") {
    return null;
  }
  const mode = group.mode;
  switch (font) {
    case "mathit":
      return "italic";
    case "mathrm": {
      const codePoint = group.text.codePointAt(0);
      return 939 < codePoint && codePoint < 975 ? "italic" : "normal";
    }
    case "greekItalic":
      return "italic";
    case "up@greek":
      return "normal";
    case "boldsymbol":
    case "mathboldsymbol":
      return "bold-italic";
    case "mathbf":
      return "bold";
    case "mathbb":
      return "double-struck";
    case "mathfrak":
      return "fraktur";
    case "mathscr":
    case "mathcal":
      return "script";
    case "mathsf":
      return "sans-serif";
    case "mathsfit":
      return "sans-serif-italic";
    case "mathtt":
      return "monospace";
  }
  let text2 = group.text;
  if (symbols[mode][text2] && symbols[mode][text2].replace) {
    text2 = symbols[mode][text2].replace;
  }
  return Object.prototype.hasOwnProperty.call(fontMap, font) ? fontMap[font] : null;
};
var script = Object.freeze({
  B: 8426,
  // Offset from ASCII B to Unicode script B
  E: 8427,
  F: 8427,
  H: 8387,
  I: 8391,
  L: 8390,
  M: 8422,
  R: 8393,
  e: 8394,
  g: 8355,
  o: 8389
});
var frak = Object.freeze({
  C: 8426,
  H: 8388,
  I: 8392,
  R: 8394,
  Z: 8398
});
var bbb = Object.freeze({
  C: 8383,
  // blackboard bold
  H: 8389,
  N: 8391,
  P: 8393,
  Q: 8393,
  R: 8395,
  Z: 8394
});
var bold = Object.freeze({
  "\u03F5": 119527,
  // lunate epsilon
  "\u03D1": 119564,
  // vartheta
  "\u03F0": 119534,
  // varkappa
  "\u03C6": 119577,
  // varphi
  "\u03F1": 119535,
  // varrho
  "\u03D6": 119563
  // varpi
});
var boldItalic = Object.freeze({
  "\u03F5": 119643,
  // lunate epsilon
  "\u03D1": 119680,
  // vartheta
  "\u03F0": 119650,
  // varkappa
  "\u03C6": 119693,
  // varphi
  "\u03F1": 119651,
  // varrho
  "\u03D6": 119679
  // varpi
});
var boldsf = Object.freeze({
  "\u03F5": 119701,
  // lunate epsilon
  "\u03D1": 119738,
  // vartheta
  "\u03F0": 119708,
  // varkappa
  "\u03C6": 119751,
  // varphi
  "\u03F1": 119709,
  // varrho
  "\u03D6": 119737
  // varpi
});
var bisf = Object.freeze({
  "\u03F5": 119759,
  // lunate epsilon
  "\u03D1": 119796,
  // vartheta
  "\u03F0": 119766,
  // varkappa
  "\u03C6": 119809,
  // varphi
  "\u03F1": 119767,
  // varrho
  "\u03D6": 119795
  // varpi
});
var offset = Object.freeze({
  upperCaseLatin: {
    // A-Z
    "normal": (ch) => {
      return 0;
    },
    "bold": (ch) => {
      return 119743;
    },
    "italic": (ch) => {
      return 119795;
    },
    "bold-italic": (ch) => {
      return 119847;
    },
    "script": (ch) => {
      return script[ch] || 119899;
    },
    "script-bold": (ch) => {
      return 119951;
    },
    "fraktur": (ch) => {
      return frak[ch] || 120003;
    },
    "fraktur-bold": (ch) => {
      return 120107;
    },
    "double-struck": (ch) => {
      return bbb[ch] || 120055;
    },
    "sans-serif": (ch) => {
      return 120159;
    },
    "sans-serif-bold": (ch) => {
      return 120211;
    },
    "sans-serif-italic": (ch) => {
      return 120263;
    },
    "sans-serif-bold-italic": (ch) => {
      return 120380;
    },
    "monospace": (ch) => {
      return 120367;
    }
  },
  lowerCaseLatin: {
    // a-z
    "normal": (ch) => {
      return 0;
    },
    "bold": (ch) => {
      return 119737;
    },
    "italic": (ch) => {
      return ch === "h" ? 8358 : 119789;
    },
    "bold-italic": (ch) => {
      return 119841;
    },
    "script": (ch) => {
      return script[ch] || 119893;
    },
    "script-bold": (ch) => {
      return 119945;
    },
    "fraktur": (ch) => {
      return 119997;
    },
    "fraktur-bold": (ch) => {
      return 120101;
    },
    "double-struck": (ch) => {
      return 120049;
    },
    "sans-serif": (ch) => {
      return 120153;
    },
    "sans-serif-bold": (ch) => {
      return 120205;
    },
    "sans-serif-italic": (ch) => {
      return 120257;
    },
    "sans-serif-bold-italic": (ch) => {
      return 120309;
    },
    "monospace": (ch) => {
      return 120361;
    }
  },
  upperCaseGreek: {
    // A-Ω
    "normal": (ch) => {
      return 0;
    },
    "bold": (ch) => {
      return 119575;
    },
    "italic": (ch) => {
      return 119633;
    },
    // \boldsymbol actually returns upright bold for upperCaseGreek
    "bold-italic": (ch) => {
      return 119575;
    },
    "script": (ch) => {
      return 0;
    },
    "script-bold": (ch) => {
      return 0;
    },
    "fraktur": (ch) => {
      return 0;
    },
    "fraktur-bold": (ch) => {
      return 0;
    },
    "double-struck": (ch) => {
      return 0;
    },
    // Unicode has no code points for regular-weight san-serif Greek. Use bold.
    "sans-serif": (ch) => {
      return 119749;
    },
    "sans-serif-bold": (ch) => {
      return 119749;
    },
    "sans-serif-italic": (ch) => {
      return 0;
    },
    "sans-serif-bold-italic": (ch) => {
      return 119807;
    },
    "monospace": (ch) => {
      return 0;
    }
  },
  lowerCaseGreek: {
    // α-ω
    "normal": (ch) => {
      return 0;
    },
    "bold": (ch) => {
      return 119569;
    },
    "italic": (ch) => {
      return 119627;
    },
    "bold-italic": (ch) => {
      return ch === "\u03D5" ? 119678 : 119685;
    },
    "script": (ch) => {
      return 0;
    },
    "script-bold": (ch) => {
      return 0;
    },
    "fraktur": (ch) => {
      return 0;
    },
    "fraktur-bold": (ch) => {
      return 0;
    },
    "double-struck": (ch) => {
      return 0;
    },
    // Unicode has no code points for regular-weight san-serif Greek. Use bold.
    "sans-serif": (ch) => {
      return 119743;
    },
    "sans-serif-bold": (ch) => {
      return 119743;
    },
    "sans-serif-italic": (ch) => {
      return 0;
    },
    "sans-serif-bold-italic": (ch) => {
      return 119801;
    },
    "monospace": (ch) => {
      return 0;
    }
  },
  varGreek: {
    // \varGamma, etc
    "normal": (ch) => {
      return 0;
    },
    "bold": (ch) => {
      return bold[ch] || -51;
    },
    "italic": (ch) => {
      return 0;
    },
    "bold-italic": (ch) => {
      return boldItalic[ch] || 58;
    },
    "script": (ch) => {
      return 0;
    },
    "script-bold": (ch) => {
      return 0;
    },
    "fraktur": (ch) => {
      return 0;
    },
    "fraktur-bold": (ch) => {
      return 0;
    },
    "double-struck": (ch) => {
      return 0;
    },
    "sans-serif": (ch) => {
      return boldsf[ch] || 116;
    },
    "sans-serif-bold": (ch) => {
      return boldsf[ch] || 116;
    },
    "sans-serif-italic": (ch) => {
      return 0;
    },
    "sans-serif-bold-italic": (ch) => {
      return bisf[ch] || 174;
    },
    "monospace": (ch) => {
      return 0;
    }
  },
  numeral: {
    // 0-9
    "normal": (ch) => {
      return 0;
    },
    "bold": (ch) => {
      return 120734;
    },
    "italic": (ch) => {
      return 0;
    },
    "bold-italic": (ch) => {
      return 0;
    },
    "script": (ch) => {
      return 0;
    },
    "script-bold": (ch) => {
      return 0;
    },
    "fraktur": (ch) => {
      return 0;
    },
    "fraktur-bold": (ch) => {
      return 0;
    },
    "double-struck": (ch) => {
      return 120744;
    },
    "sans-serif": (ch) => {
      return 120754;
    },
    "sans-serif-bold": (ch) => {
      return 120764;
    },
    "sans-serif-italic": (ch) => {
      return 0;
    },
    "sans-serif-bold-italic": (ch) => {
      return 0;
    },
    "monospace": (ch) => {
      return 120774;
    }
  }
});
var variantChar = (ch, variant) => {
  const codePoint = ch.codePointAt(0);
  const block = 64 < codePoint && codePoint < 91 ? "upperCaseLatin" : 96 < codePoint && codePoint < 123 ? "lowerCaseLatin" : 912 < codePoint && codePoint < 938 ? "upperCaseGreek" : 944 < codePoint && codePoint < 970 || ch === "\u03D5" ? "lowerCaseGreek" : 120545 < codePoint && codePoint < 120572 || bold[ch] ? "varGreek" : 47 < codePoint && codePoint < 58 ? "numeral" : "other";
  return block === "other" ? ch : String.fromCodePoint(codePoint + offset[block][variant](ch));
};
var smallCaps = Object.freeze({
  a: "\u1D00",
  b: "\u0299",
  c: "\u1D04",
  d: "\u1D05",
  e: "\u1D07",
  f: "\uA730",
  g: "\u0262",
  h: "\u029C",
  i: "\u026A",
  j: "\u1D0A",
  k: "\u1D0B",
  l: "\u029F",
  m: "\u1D0D",
  n: "\u0274",
  o: "\u1D0F",
  p: "\u1D18",
  q: "\u01EB",
  r: "\u0280",
  s: "s",
  t: "\u1D1B",
  u: "\u1D1C",
  v: "\u1D20",
  w: "\u1D21",
  x: "x",
  y: "\u028F",
  z: "\u1D22"
});
var numberRegEx = /^\d(?:[\d,.]*\d)?$/;
var latinRegEx = /[A-Ba-z]/;
var primes = /* @__PURE__ */ new Set([
  "\\prime",
  "\\dprime",
  "\\trprime",
  "\\qprime",
  "\\backprime",
  "\\backdprime",
  "\\backtrprime"
]);
var italicNumber = (text2, variant, tag) => {
  const mn = new mathMLTree.MathNode(tag, [text2]);
  const wrapper = new mathMLTree.MathNode("mstyle", [mn]);
  wrapper.style["font-style"] = "italic";
  wrapper.style["font-family"] = "Cambria, 'Times New Roman', serif";
  if (variant === "bold-italic") {
    wrapper.style["font-weight"] = "bold";
  }
  return wrapper;
};
defineFunctionBuilders({
  type: "mathord",
  mathmlBuilder(group, style) {
    const text2 = makeText(group.text, group.mode, style);
    const codePoint = text2.text.codePointAt(0);
    const defaultVariant = 912 < codePoint && codePoint < 938 ? "normal" : "italic";
    const variant = getVariant(group, style) || defaultVariant;
    if (variant === "script") {
      text2.text = variantChar(text2.text, variant);
      return new mathMLTree.MathNode("mi", [text2], [style.font]);
    } else if (variant !== "italic") {
      text2.text = variantChar(text2.text, variant);
    }
    let node = new mathMLTree.MathNode("mi", [text2]);
    if (variant === "normal") {
      node.setAttribute("mathvariant", "normal");
      if (text2.text.length === 1) {
        node = new mathMLTree.MathNode("mrow", [node]);
      }
    }
    return node;
  }
});
defineFunctionBuilders({
  type: "textord",
  mathmlBuilder(group, style) {
    let ch = group.text;
    const codePoint = ch.codePointAt(0);
    if (style.fontFamily === "textsc") {
      if (96 < codePoint && codePoint < 123) {
        ch = smallCaps[ch];
      }
    }
    const text2 = makeText(ch, group.mode, style);
    const variant = getVariant(group, style) || "normal";
    let node;
    if (numberRegEx.test(group.text)) {
      const tag = group.mode === "text" ? "mtext" : "mn";
      if (variant === "italic" || variant === "bold-italic") {
        return italicNumber(text2, variant, tag);
      } else {
        if (variant !== "normal") {
          text2.text = text2.text.split("").map((c) => variantChar(c, variant)).join("");
        }
        node = new mathMLTree.MathNode(tag, [text2]);
      }
    } else if (group.mode === "text") {
      if (variant !== "normal") {
        text2.text = variantChar(text2.text, variant);
      }
      node = new mathMLTree.MathNode("mtext", [text2]);
    } else if (primes.has(group.text)) {
      node = new mathMLTree.MathNode("mo", [text2]);
      node.classes.push("tml-prime");
    } else {
      const origText = text2.text;
      if (variant !== "italic") {
        text2.text = variantChar(text2.text, variant);
      }
      node = new mathMLTree.MathNode("mi", [text2]);
      if (text2.text === origText && latinRegEx.test(origText)) {
        node.setAttribute("mathvariant", "italic");
      }
    }
    return node;
  }
});
var cssSpace = {
  "\\nobreak": "nobreak",
  "\\allowbreak": "allowbreak"
};
var regularSpace = {
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
defineFunctionBuilders({
  type: "spacing",
  mathmlBuilder(group, style) {
    let node;
    if (Object.prototype.hasOwnProperty.call(regularSpace, group.text)) {
      node = new mathMLTree.MathNode("mtext", [new mathMLTree.TextNode("\xA0")]);
    } else if (Object.prototype.hasOwnProperty.call(cssSpace, group.text)) {
      node = new mathMLTree.MathNode("mo");
      if (group.text === "\\nobreak") {
        node.setAttribute("linebreak", "nobreak");
      }
    } else {
      throw new ParseError(`Unknown type of space "${group.text}"`);
    }
    return node;
  }
});
defineFunctionBuilders({
  type: "tag"
});
var textFontFamilies = {
  "\\text": void 0,
  "\\textrm": "textrm",
  "\\textsf": "textsf",
  "\\texttt": "texttt",
  "\\textnormal": "textrm",
  "\\textsc": "textsc"
  // small caps
};
var textFontWeights = {
  "\\textbf": "textbf",
  "\\textmd": "textmd"
};
var textFontShapes = {
  "\\textit": "textit",
  "\\textup": "textup"
};
var styleWithFont = (group, style) => {
  const font = group.font;
  if (!font) {
    return style;
  } else if (textFontFamilies[font]) {
    return style.withTextFontFamily(textFontFamilies[font]);
  } else if (textFontWeights[font]) {
    return style.withTextFontWeight(textFontWeights[font]);
  } else if (font === "\\emph") {
    return style.fontShape === "textit" ? style.withTextFontShape("textup") : style.withTextFontShape("textit");
  }
  return style.withTextFontShape(textFontShapes[font]);
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
    return consolidateText(mrow);
  }
});
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
    const mtd = new mathMLTree.MathNode("mtd", [buildGroup$1(group.body, style)]);
    mtd.style.padding = "0";
    const mtr = new mathMLTree.MathNode("mtr", [mtd]);
    return new mathMLTree.MathNode("mtable", [mtr]);
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
    throw new ParseError("\\verb ended by end of line instead of matching delimiter");
  },
  mathmlBuilder(group, style) {
    const text2 = new mathMLTree.TextNode(makeVerb(group));
    const node = new mathMLTree.MathNode("mtext", [text2]);
    node.setAttribute("mathvariant", "monospace");
    return node;
  }
});
var makeVerb = (group) => group.body.replace(/ /g, group.star ? "\u2423" : "\xA0");
var functions = _functions;
var spaceRegexString = "[ \r\n	]";
var controlWordRegexString = "\\\\[a-zA-Z@]+";
var controlSymbolRegexString = "\\\\[^\uD800-\uDFFF]";
var controlWordWhitespaceRegexString = `(${controlWordRegexString})${spaceRegexString}*`;
var controlSpaceRegexString = "\\\\(\n|[ \r	]+\n?)[ \r	]*";
var combiningDiacriticalMarkString = "[\u0300-\u036F]";
var combiningDiacriticalMarksEndRegex = new RegExp(`${combiningDiacriticalMarkString}+$`);
var tokenRegexString = `(${spaceRegexString}+)|${controlSpaceRegexString}|([!-\\[\\]-\u2027\u202A-\uD7FF\uF900-\uFFFF]${combiningDiacriticalMarkString}*|[\uD800-\uDBFF][\uDC00-\uDFFF]${combiningDiacriticalMarkString}*|\\\\verb\\*([^]).*?\\4|\\\\verb([^*a-zA-Z]).*?\\5|${controlWordWhitespaceRegexString}|${controlSymbolRegexString})`;
var Lexer = class {
  constructor(input, settings) {
    this.input = input;
    this.settings = settings;
    this.tokenRegex = new RegExp(tokenRegexString, "g");
    this.catcodes = {
      "%": 14,
      // comment character
      "~": 13
      // active character
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
    const text2 = match[6] || match[3] || (match[2] ? "\\ " : " ");
    if (this.catcodes[text2] === 14) {
      const nlIndex = input.indexOf("\n", this.tokenRegex.lastIndex);
      if (nlIndex === -1) {
        this.tokenRegex.lastIndex = input.length;
        if (this.settings.strict) {
          throw new ParseError("% comment has no terminating newline; LaTeX would fail because of commenting the end of math mode");
        }
      } else {
        this.tokenRegex.lastIndex = nlIndex + 1;
      }
      return this.lex();
    }
    return new Token(text2, new SourceLocation(this, pos, this.tokenRegex.lastIndex));
  }
};
var Namespace = class {
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
        "Unbalanced namespace destruction: attempt to pop global namespace; please report this as a bug"
      );
    }
    const undefs = this.undefStack.pop();
    for (const undef in undefs) {
      if (Object.prototype.hasOwnProperty.call(undefs, undef)) {
        if (undefs[undef] === void 0) {
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
    return Object.prototype.hasOwnProperty.call(this.current, name) || Object.prototype.hasOwnProperty.call(this.builtins, name);
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
    if (Object.prototype.hasOwnProperty.call(this.current, name)) {
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
      for (let i = 0; i < this.undefStack.length; i++) {
        delete this.undefStack[i][name];
      }
      if (this.undefStack.length > 0) {
        this.undefStack[this.undefStack.length - 1][name] = value;
      }
    } else {
      const top = this.undefStack[this.undefStack.length - 1];
      if (top && !Object.prototype.hasOwnProperty.call(top, name)) {
        top[name] = this.current[name];
      }
    }
    this.current[name] = value;
  }
};
var implicitCommands = {
  "^": true,
  // Parser.js
  _: true,
  // Parser.js
  "\\limits": true,
  // Parser.js
  "\\nolimits": true
  // Parser.js
};
var MacroExpander = class {
  constructor(input, settings, mode) {
    this.settings = settings;
    this.expansionCount = 0;
    this.feed(input);
    this.macros = new Namespace(macros, settings.macros);
    this.mode = mode;
    this.stack = [];
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
    return this.stack[this.stack.length - 1];
  }
  /**
   * Remove and return the next unexpanded token.
   */
  popToken() {
    this.future();
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
      this.consumeSpaces();
      if (this.future().text !== "[") {
        return null;
      }
      start = this.popToken();
      ({ tokens, end } = this.consumeArg(["]"]));
    } else {
      ({ tokens, start, end } = this.consumeArg());
    }
    this.pushToken(new Token("EOF", end.loc));
    this.pushTokens(tokens);
    return start.range(end, "");
  }
  /**
   * Consume all following space tokens, without expansion.
   */
  consumeSpaces() {
    for (; ; ) {
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
    const tokens = [];
    const isDelimited = delims && delims.length > 0;
    if (!isDelimited) {
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
          "Unexpected end of input in a macro argument, expected '" + (delims && isDelimited ? delims[match] : "}") + "'",
          tok
        );
      }
      if (delims && isDelimited) {
        if ((depth === 0 || depth === 1 && delims[match] === "{") && tok.text === delims[match]) {
          ++match;
          if (match === delims.length) {
            tokens.splice(-match, match);
            break;
          }
        } else {
          match = 0;
        }
      }
    } while (depth !== 0 || isDelimited);
    if (start.text === "{" && tokens[tokens.length - 1].text === "}") {
      tokens.pop();
      tokens.shift();
    }
    tokens.reverse();
    return { tokens, start, end: tok };
  }
  /**
   * Consume the specified number of (delimited) arguments from the token
   * stream and return the resulting array of arguments.
   */
  consumeArgs(numArgs, delimiters2) {
    if (delimiters2) {
      if (delimiters2.length !== numArgs + 1) {
        throw new ParseError("The length of delimiters doesn't match the number of args!");
      }
      const delims = delimiters2[0];
      for (let i = 0; i < delims.length; i++) {
        const tok = this.popToken();
        if (delims[i] !== tok.text) {
          throw new ParseError("Use of the macro doesn't match its definition", tok);
        }
      }
    }
    const args = [];
    for (let i = 0; i < numArgs; i++) {
      args.push(this.consumeArg(delimiters2 && delimiters2[i + 1]).tokens);
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
    if (expansion == null || expandableOnly && expansion.unexpandable) {
      if (expandableOnly && expansion == null && name[0] === "\\" && !this.isDefined(name)) {
        throw new ParseError("Undefined control sequence: " + name);
      }
      this.pushToken(topToken);
      return false;
    }
    this.expansionCount++;
    if (this.expansionCount > this.settings.maxExpand) {
      throw new ParseError(
        "Too many expansions: infinite loop or need to increase maxExpand setting"
      );
    }
    let tokens = expansion.tokens;
    const args = this.consumeArgs(expansion.numArgs, expansion.delimiters);
    if (expansion.numArgs) {
      tokens = tokens.slice();
      for (let i = tokens.length - 1; i >= 0; --i) {
        let tok = tokens[i];
        if (tok.text === "#") {
          if (i === 0) {
            throw new ParseError("Incomplete placeholder at end of macro body", tok);
          }
          tok = tokens[--i];
          if (tok.text === "#") {
            tokens.splice(i + 1, 1);
          } else if (/^[1-9]$/.test(tok.text)) {
            tokens.splice(i, 2, ...args[+tok.text - 1]);
          } else {
            throw new ParseError("Not a valid argument number", tok);
          }
        }
      }
    }
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
    for (; ; ) {
      if (this.expandOnce() === false) {
        const token = this.stack.pop();
        if (token.treatAsRelax) {
          token.text = "\\relax";
        }
        return token;
      }
    }
    throw new Error();
  }
  /**
   * Fully expand the given macro name and return the resulting list of
   * tokens, or return `undefined` if no such macro is defined.
   */
  expandMacro(name) {
    return this.macros.has(name) ? this.expandTokens([new Token(name)]) : void 0;
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
      if (this.expandOnce(true) === false) {
        const token = this.stack.pop();
        if (token.treatAsRelax) {
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
      return definition;
    }
    if (name.length === 1) {
      const catcode = this.lexer.catcodes[name];
      if (catcode != null && catcode !== 13) {
        return;
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
      tokens.reverse();
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
    return this.macros.has(name) || Object.prototype.hasOwnProperty.call(functions, name) || Object.prototype.hasOwnProperty.call(symbols.math, name) || Object.prototype.hasOwnProperty.call(symbols.text, name) || Object.prototype.hasOwnProperty.call(implicitCommands, name);
  }
  /**
   * Determine whether a command is expandable.
   */
  isExpandable(name) {
    const macro = this.macros.get(name);
    return macro != null ? typeof macro === "string" || typeof macro === "function" || !macro.unexpandable : Object.prototype.hasOwnProperty.call(functions, name) && !functions[name].primitive;
  }
};
var unicodeSubRegEx = /^[₊₋₌₍₎₀₁₂₃₄₅₆₇₈₉ₐₑₕᵢⱼₖₗₘₙₒₚᵣₛₜᵤᵥₓᵦᵧᵨᵩᵪ]/;
var uSubsAndSups = Object.freeze({
  "\u208A": "+",
  "\u208B": "-",
  "\u208C": "=",
  "\u208D": "(",
  "\u208E": ")",
  "\u2080": "0",
  "\u2081": "1",
  "\u2082": "2",
  "\u2083": "3",
  "\u2084": "4",
  "\u2085": "5",
  "\u2086": "6",
  "\u2087": "7",
  "\u2088": "8",
  "\u2089": "9",
  "\u2090": "a",
  "\u2091": "e",
  "\u2095": "h",
  "\u1D62": "i",
  "\u2C7C": "j",
  "\u2096": "k",
  "\u2097": "l",
  "\u2098": "m",
  "\u2099": "n",
  "\u2092": "o",
  "\u209A": "p",
  "\u1D63": "r",
  "\u209B": "s",
  "\u209C": "t",
  "\u1D64": "u",
  "\u1D65": "v",
  "\u2093": "x",
  "\u1D66": "\u03B2",
  "\u1D67": "\u03B3",
  "\u1D68": "\u03C1",
  "\u1D69": "\u03D5",
  "\u1D6A": "\u03C7",
  "\u207A": "+",
  "\u207B": "-",
  "\u207C": "=",
  "\u207D": "(",
  "\u207E": ")",
  "\u2070": "0",
  "\xB9": "1",
  "\xB2": "2",
  "\xB3": "3",
  "\u2074": "4",
  "\u2075": "5",
  "\u2076": "6",
  "\u2077": "7",
  "\u2078": "8",
  "\u2079": "9",
  "\u1D2C": "A",
  "\u1D2E": "B",
  "\u1D30": "D",
  "\u1D31": "E",
  "\u1D33": "G",
  "\u1D34": "H",
  "\u1D35": "I",
  "\u1D36": "J",
  "\u1D37": "K",
  "\u1D38": "L",
  "\u1D39": "M",
  "\u1D3A": "N",
  "\u1D3C": "O",
  "\u1D3E": "P",
  "\u1D3F": "R",
  "\u1D40": "T",
  "\u1D41": "U",
  "\u2C7D": "V",
  "\u1D42": "W",
  "\u1D43": "a",
  "\u1D47": "b",
  "\u1D9C": "c",
  "\u1D48": "d",
  "\u1D49": "e",
  "\u1DA0": "f",
  "\u1D4D": "g",
  "\u02B0": "h",
  "\u2071": "i",
  "\u02B2": "j",
  "\u1D4F": "k",
  "\u02E1": "l",
  "\u1D50": "m",
  "\u207F": "n",
  "\u1D52": "o",
  "\u1D56": "p",
  "\u02B3": "r",
  "\u02E2": "s",
  "\u1D57": "t",
  "\u1D58": "u",
  "\u1D5B": "v",
  "\u02B7": "w",
  "\u02E3": "x",
  "\u02B8": "y",
  "\u1DBB": "z",
  "\u1D5D": "\u03B2",
  "\u1D5E": "\u03B3",
  "\u1D5F": "\u03B4",
  "\u1D60": "\u03D5",
  "\u1D61": "\u03C7",
  "\u1DBF": "\u03B8"
});
var asciiFromScript = Object.freeze({
  "\u{1D49C}": "A",
  "\u212C": "B",
  "\u{1D49E}": "C",
  "\u{1D49F}": "D",
  "\u2130": "E",
  "\u2131": "F",
  "\u{1D4A2}": "G",
  "\u210B": "H",
  "\u2110": "I",
  "\u{1D4A5}": "J",
  "\u{1D4A6}": "K",
  "\u2112": "L",
  "\u2133": "M",
  "\u{1D4A9}": "N",
  "\u{1D4AA}": "O",
  "\u{1D4AB}": "P",
  "\u{1D4AC}": "Q",
  "\u211B": "R",
  "\u{1D4AE}": "S",
  "\u{1D4AF}": "T",
  "\u{1D4B0}": "U",
  "\u{1D4B1}": "V",
  "\u{1D4B2}": "W",
  "\u{1D4B3}": "X",
  "\u{1D4B4}": "Y",
  "\u{1D4B5}": "Z"
});
var unicodeAccents = {
  "\u0301": { text: "\\'", math: "\\acute" },
  "\u0300": { text: "\\`", math: "\\grave" },
  "\u0308": { text: '\\"', math: "\\ddot" },
  "\u0303": { text: "\\~", math: "\\tilde" },
  "\u0304": { text: "\\=", math: "\\bar" },
  "\u0306": { text: "\\u", math: "\\breve" },
  "\u030C": { text: "\\v", math: "\\check" },
  "\u0302": { text: "\\^", math: "\\hat" },
  "\u0307": { text: "\\.", math: "\\dot" },
  "\u030A": { text: "\\r", math: "\\mathring" },
  "\u030B": { text: "\\H" },
  "\u0327": { text: "\\c" }
};
var unicodeSymbols = {
  "\xE1": "a\u0301",
  "\xE0": "a\u0300",
  "\xE4": "a\u0308",
  "\u01DF": "a\u0308\u0304",
  "\xE3": "a\u0303",
  "\u0101": "a\u0304",
  "\u0103": "a\u0306",
  "\u1EAF": "a\u0306\u0301",
  "\u1EB1": "a\u0306\u0300",
  "\u1EB5": "a\u0306\u0303",
  "\u01CE": "a\u030C",
  "\xE2": "a\u0302",
  "\u1EA5": "a\u0302\u0301",
  "\u1EA7": "a\u0302\u0300",
  "\u1EAB": "a\u0302\u0303",
  "\u0227": "a\u0307",
  "\u01E1": "a\u0307\u0304",
  "\xE5": "a\u030A",
  "\u01FB": "a\u030A\u0301",
  "\u1E03": "b\u0307",
  "\u0107": "c\u0301",
  "\u010D": "c\u030C",
  "\u0109": "c\u0302",
  "\u010B": "c\u0307",
  "\u010F": "d\u030C",
  "\u1E0B": "d\u0307",
  "\xE9": "e\u0301",
  "\xE8": "e\u0300",
  "\xEB": "e\u0308",
  "\u1EBD": "e\u0303",
  "\u0113": "e\u0304",
  "\u1E17": "e\u0304\u0301",
  "\u1E15": "e\u0304\u0300",
  "\u0115": "e\u0306",
  "\u011B": "e\u030C",
  "\xEA": "e\u0302",
  "\u1EBF": "e\u0302\u0301",
  "\u1EC1": "e\u0302\u0300",
  "\u1EC5": "e\u0302\u0303",
  "\u0117": "e\u0307",
  "\u1E1F": "f\u0307",
  "\u01F5": "g\u0301",
  "\u1E21": "g\u0304",
  "\u011F": "g\u0306",
  "\u01E7": "g\u030C",
  "\u011D": "g\u0302",
  "\u0121": "g\u0307",
  "\u1E27": "h\u0308",
  "\u021F": "h\u030C",
  "\u0125": "h\u0302",
  "\u1E23": "h\u0307",
  "\xED": "i\u0301",
  "\xEC": "i\u0300",
  "\xEF": "i\u0308",
  "\u1E2F": "i\u0308\u0301",
  "\u0129": "i\u0303",
  "\u012B": "i\u0304",
  "\u012D": "i\u0306",
  "\u01D0": "i\u030C",
  "\xEE": "i\u0302",
  "\u01F0": "j\u030C",
  "\u0135": "j\u0302",
  "\u1E31": "k\u0301",
  "\u01E9": "k\u030C",
  "\u013A": "l\u0301",
  "\u013E": "l\u030C",
  "\u1E3F": "m\u0301",
  "\u1E41": "m\u0307",
  "\u0144": "n\u0301",
  "\u01F9": "n\u0300",
  "\xF1": "n\u0303",
  "\u0148": "n\u030C",
  "\u1E45": "n\u0307",
  "\xF3": "o\u0301",
  "\xF2": "o\u0300",
  "\xF6": "o\u0308",
  "\u022B": "o\u0308\u0304",
  "\xF5": "o\u0303",
  "\u1E4D": "o\u0303\u0301",
  "\u1E4F": "o\u0303\u0308",
  "\u022D": "o\u0303\u0304",
  "\u014D": "o\u0304",
  "\u1E53": "o\u0304\u0301",
  "\u1E51": "o\u0304\u0300",
  "\u014F": "o\u0306",
  "\u01D2": "o\u030C",
  "\xF4": "o\u0302",
  "\u1ED1": "o\u0302\u0301",
  "\u1ED3": "o\u0302\u0300",
  "\u1ED7": "o\u0302\u0303",
  "\u022F": "o\u0307",
  "\u0231": "o\u0307\u0304",
  "\u0151": "o\u030B",
  "\u1E55": "p\u0301",
  "\u1E57": "p\u0307",
  "\u0155": "r\u0301",
  "\u0159": "r\u030C",
  "\u1E59": "r\u0307",
  "\u015B": "s\u0301",
  "\u1E65": "s\u0301\u0307",
  "\u0161": "s\u030C",
  "\u1E67": "s\u030C\u0307",
  "\u015D": "s\u0302",
  "\u1E61": "s\u0307",
  "\u1E97": "t\u0308",
  "\u0165": "t\u030C",
  "\u1E6B": "t\u0307",
  "\xFA": "u\u0301",
  "\xF9": "u\u0300",
  "\xFC": "u\u0308",
  "\u01D8": "u\u0308\u0301",
  "\u01DC": "u\u0308\u0300",
  "\u01D6": "u\u0308\u0304",
  "\u01DA": "u\u0308\u030C",
  "\u0169": "u\u0303",
  "\u1E79": "u\u0303\u0301",
  "\u016B": "u\u0304",
  "\u1E7B": "u\u0304\u0308",
  "\u016D": "u\u0306",
  "\u01D4": "u\u030C",
  "\xFB": "u\u0302",
  "\u016F": "u\u030A",
  "\u0171": "u\u030B",
  "\u1E7D": "v\u0303",
  "\u1E83": "w\u0301",
  "\u1E81": "w\u0300",
  "\u1E85": "w\u0308",
  "\u0175": "w\u0302",
  "\u1E87": "w\u0307",
  "\u1E98": "w\u030A",
  "\u1E8D": "x\u0308",
  "\u1E8B": "x\u0307",
  "\xFD": "y\u0301",
  "\u1EF3": "y\u0300",
  "\xFF": "y\u0308",
  "\u1EF9": "y\u0303",
  "\u0233": "y\u0304",
  "\u0177": "y\u0302",
  "\u1E8F": "y\u0307",
  "\u1E99": "y\u030A",
  "\u017A": "z\u0301",
  "\u017E": "z\u030C",
  "\u1E91": "z\u0302",
  "\u017C": "z\u0307",
  "\xC1": "A\u0301",
  "\xC0": "A\u0300",
  "\xC4": "A\u0308",
  "\u01DE": "A\u0308\u0304",
  "\xC3": "A\u0303",
  "\u0100": "A\u0304",
  "\u0102": "A\u0306",
  "\u1EAE": "A\u0306\u0301",
  "\u1EB0": "A\u0306\u0300",
  "\u1EB4": "A\u0306\u0303",
  "\u01CD": "A\u030C",
  "\xC2": "A\u0302",
  "\u1EA4": "A\u0302\u0301",
  "\u1EA6": "A\u0302\u0300",
  "\u1EAA": "A\u0302\u0303",
  "\u0226": "A\u0307",
  "\u01E0": "A\u0307\u0304",
  "\xC5": "A\u030A",
  "\u01FA": "A\u030A\u0301",
  "\u1E02": "B\u0307",
  "\u0106": "C\u0301",
  "\u010C": "C\u030C",
  "\u0108": "C\u0302",
  "\u010A": "C\u0307",
  "\u010E": "D\u030C",
  "\u1E0A": "D\u0307",
  "\xC9": "E\u0301",
  "\xC8": "E\u0300",
  "\xCB": "E\u0308",
  "\u1EBC": "E\u0303",
  "\u0112": "E\u0304",
  "\u1E16": "E\u0304\u0301",
  "\u1E14": "E\u0304\u0300",
  "\u0114": "E\u0306",
  "\u011A": "E\u030C",
  "\xCA": "E\u0302",
  "\u1EBE": "E\u0302\u0301",
  "\u1EC0": "E\u0302\u0300",
  "\u1EC4": "E\u0302\u0303",
  "\u0116": "E\u0307",
  "\u1E1E": "F\u0307",
  "\u01F4": "G\u0301",
  "\u1E20": "G\u0304",
  "\u011E": "G\u0306",
  "\u01E6": "G\u030C",
  "\u011C": "G\u0302",
  "\u0120": "G\u0307",
  "\u1E26": "H\u0308",
  "\u021E": "H\u030C",
  "\u0124": "H\u0302",
  "\u1E22": "H\u0307",
  "\xCD": "I\u0301",
  "\xCC": "I\u0300",
  "\xCF": "I\u0308",
  "\u1E2E": "I\u0308\u0301",
  "\u0128": "I\u0303",
  "\u012A": "I\u0304",
  "\u012C": "I\u0306",
  "\u01CF": "I\u030C",
  "\xCE": "I\u0302",
  "\u0130": "I\u0307",
  "\u0134": "J\u0302",
  "\u1E30": "K\u0301",
  "\u01E8": "K\u030C",
  "\u0139": "L\u0301",
  "\u013D": "L\u030C",
  "\u1E3E": "M\u0301",
  "\u1E40": "M\u0307",
  "\u0143": "N\u0301",
  "\u01F8": "N\u0300",
  "\xD1": "N\u0303",
  "\u0147": "N\u030C",
  "\u1E44": "N\u0307",
  "\xD3": "O\u0301",
  "\xD2": "O\u0300",
  "\xD6": "O\u0308",
  "\u022A": "O\u0308\u0304",
  "\xD5": "O\u0303",
  "\u1E4C": "O\u0303\u0301",
  "\u1E4E": "O\u0303\u0308",
  "\u022C": "O\u0303\u0304",
  "\u014C": "O\u0304",
  "\u1E52": "O\u0304\u0301",
  "\u1E50": "O\u0304\u0300",
  "\u014E": "O\u0306",
  "\u01D1": "O\u030C",
  "\xD4": "O\u0302",
  "\u1ED0": "O\u0302\u0301",
  "\u1ED2": "O\u0302\u0300",
  "\u1ED6": "O\u0302\u0303",
  "\u022E": "O\u0307",
  "\u0230": "O\u0307\u0304",
  "\u0150": "O\u030B",
  "\u1E54": "P\u0301",
  "\u1E56": "P\u0307",
  "\u0154": "R\u0301",
  "\u0158": "R\u030C",
  "\u1E58": "R\u0307",
  "\u015A": "S\u0301",
  "\u1E64": "S\u0301\u0307",
  "\u0160": "S\u030C",
  "\u1E66": "S\u030C\u0307",
  "\u015C": "S\u0302",
  "\u1E60": "S\u0307",
  "\u0164": "T\u030C",
  "\u1E6A": "T\u0307",
  "\xDA": "U\u0301",
  "\xD9": "U\u0300",
  "\xDC": "U\u0308",
  "\u01D7": "U\u0308\u0301",
  "\u01DB": "U\u0308\u0300",
  "\u01D5": "U\u0308\u0304",
  "\u01D9": "U\u0308\u030C",
  "\u0168": "U\u0303",
  "\u1E78": "U\u0303\u0301",
  "\u016A": "U\u0304",
  "\u1E7A": "U\u0304\u0308",
  "\u016C": "U\u0306",
  "\u01D3": "U\u030C",
  "\xDB": "U\u0302",
  "\u016E": "U\u030A",
  "\u0170": "U\u030B",
  "\u1E7C": "V\u0303",
  "\u1E82": "W\u0301",
  "\u1E80": "W\u0300",
  "\u1E84": "W\u0308",
  "\u0174": "W\u0302",
  "\u1E86": "W\u0307",
  "\u1E8C": "X\u0308",
  "\u1E8A": "X\u0307",
  "\xDD": "Y\u0301",
  "\u1EF2": "Y\u0300",
  "\u0178": "Y\u0308",
  "\u1EF8": "Y\u0303",
  "\u0232": "Y\u0304",
  "\u0176": "Y\u0302",
  "\u1E8E": "Y\u0307",
  "\u0179": "Z\u0301",
  "\u017D": "Z\u030C",
  "\u1E90": "Z\u0302",
  "\u017B": "Z\u0307",
  "\u03AC": "\u03B1\u0301",
  "\u1F70": "\u03B1\u0300",
  "\u1FB1": "\u03B1\u0304",
  "\u1FB0": "\u03B1\u0306",
  "\u03AD": "\u03B5\u0301",
  "\u1F72": "\u03B5\u0300",
  "\u03AE": "\u03B7\u0301",
  "\u1F74": "\u03B7\u0300",
  "\u03AF": "\u03B9\u0301",
  "\u1F76": "\u03B9\u0300",
  "\u03CA": "\u03B9\u0308",
  "\u0390": "\u03B9\u0308\u0301",
  "\u1FD2": "\u03B9\u0308\u0300",
  "\u1FD1": "\u03B9\u0304",
  "\u1FD0": "\u03B9\u0306",
  "\u03CC": "\u03BF\u0301",
  "\u1F78": "\u03BF\u0300",
  "\u03CD": "\u03C5\u0301",
  "\u1F7A": "\u03C5\u0300",
  "\u03CB": "\u03C5\u0308",
  "\u03B0": "\u03C5\u0308\u0301",
  "\u1FE2": "\u03C5\u0308\u0300",
  "\u1FE1": "\u03C5\u0304",
  "\u1FE0": "\u03C5\u0306",
  "\u03CE": "\u03C9\u0301",
  "\u1F7C": "\u03C9\u0300",
  "\u038E": "\u03A5\u0301",
  "\u1FEA": "\u03A5\u0300",
  "\u03AB": "\u03A5\u0308",
  "\u1FE9": "\u03A5\u0304",
  "\u1FE8": "\u03A5\u0306",
  "\u038F": "\u03A9\u0301",
  "\u1FFA": "\u03A9\u0300"
};
var binLeftCancellers = ["bin", "op", "open", "punct", "rel"];
var sizeRegEx = /([-+]?) *(\d+(?:\.\d*)?|\.\d+) *([a-z]{2})/;
var Parser = class _Parser {
  constructor(input, settings, isPreamble = false) {
    this.mode = "math";
    this.gullet = new MacroExpander(input, settings, this.mode);
    this.settings = settings;
    this.isPreamble = isPreamble;
    this.leftrightDepth = 0;
    this.prevAtomType = "";
  }
  /**
   * Checks a result to make sure it has the right type, and throws an
   * appropriate error otherwise.
   */
  expect(text2, consume = true) {
    if (this.fetch().text !== text2) {
      throw new ParseError(`Expected '${text2}', got '${this.fetch().text}'`, this.fetch());
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
    this.gullet.beginGroup();
    if (this.settings.colorIsTextColor) {
      this.gullet.macros.set("\\color", "\\textcolor");
    }
    const parse = this.parseExpression(false);
    this.expect("EOF");
    if (this.isPreamble) {
      const macros2 = /* @__PURE__ */ Object.create(null);
      Object.entries(this.gullet.macros.current).forEach(([key, value]) => {
        macros2[key] = value;
      });
      this.gullet.endGroup();
      return macros2;
    }
    const tag = this.gullet.macros.get("\\df@tag");
    this.gullet.endGroup();
    if (tag) {
      this.gullet.macros.current["\\df@tag"] = tag;
    }
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
    const oldToken = this.nextToken;
    this.consume();
    this.gullet.pushToken(new Token("}"));
    this.gullet.pushTokens(tokens);
    const parse = this.parseExpression(false);
    this.expect("}");
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
    while (true) {
      if (this.mode === "math") {
        this.consumeSpaces();
      }
      const lex = this.fetch();
      if (_Parser.endOfExpression.indexOf(lex.text) !== -1) {
        break;
      }
      if (breakOnTokenText && lex.text === breakOnTokenText) {
        break;
      }
      if (breakOnMiddle && lex.text === "\\middle") {
        break;
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
  handleSupSubscript(name) {
    const symbolToken = this.fetch();
    const symbol = symbolToken.text;
    this.consume();
    this.consumeSpaces();
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
  formatUnsupportedCmd(text2) {
    const textordArray = [];
    for (let i = 0; i < text2.length; i++) {
      textordArray.push({ type: "textord", mode: "text", text: text2[i] });
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
    const base = this.parseGroup("atom", breakOnTokenText);
    if (this.mode === "text") {
      return base;
    }
    let superscript;
    let subscript;
    while (true) {
      this.consumeSpaces();
      const lex = this.fetch();
      if (lex.text === "\\limits" || lex.text === "\\nolimits") {
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
        if (superscript) {
          throw new ParseError("Double superscript", lex);
        }
        superscript = this.handleSupSubscript("superscript");
      } else if (lex.text === "_") {
        if (subscript) {
          throw new ParseError("Double subscript", lex);
        }
        subscript = this.handleSupSubscript("subscript");
      } else if (lex.text === "'") {
        if (superscript) {
          throw new ParseError("Double superscript", lex);
        }
        const prime = { type: "textord", mode: this.mode, text: "\\prime" };
        const primes2 = [prime];
        this.consume();
        while (this.fetch().text === "'") {
          primes2.push(prime);
          this.consume();
        }
        if (this.fetch().text === "^") {
          primes2.push(this.handleSupSubscript("superscript"));
        }
        superscript = { type: "ordgroup", mode: this.mode, body: primes2 };
      } else if (uSubsAndSups[lex.text]) {
        const isSub = unicodeSubRegEx.test(lex.text);
        const subsupTokens = [];
        subsupTokens.push(new Token(uSubsAndSups[lex.text]));
        this.consume();
        while (true) {
          const token = this.fetch().text;
          if (!uSubsAndSups[token]) {
            break;
          }
          if (unicodeSubRegEx.test(token) !== isSub) {
            break;
          }
          subsupTokens.unshift(new Token(uSubsAndSups[token]));
          this.consume();
        }
        const body = this.subparse(subsupTokens);
        if (isSub) {
          subscript = { type: "ordgroup", mode: "math", body };
        } else {
          superscript = { type: "ordgroup", mode: "math", body };
        }
      } else {
        break;
      }
    }
    if (superscript || subscript) {
      if (base && base.type === "multiscript" && !base.postscripts) {
        base.postscripts = { sup: superscript, sub: subscript };
        return base;
      } else {
        const isFollowedByDelimiter = !base || base.type !== "op" && base.type !== "operatorname" ? void 0 : isDelimiter(this.nextToken.text);
        return {
          type: "supsub",
          mode: this.mode,
          base,
          sup: superscript,
          sub: subscript,
          isFollowedByDelimiter
        };
      }
    } else {
      return base;
    }
  }
  /**
   * Parses an entire function, including its base and all of its arguments.
   */
  parseFunction(breakOnTokenText, name) {
    const token = this.fetch();
    const func = token.text;
    const funcData = functions[func];
    if (!funcData) {
      return null;
    }
    this.consume();
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
  parseArguments(func, funcData) {
    const totalArgs = funcData.numArgs + funcData.numOptionalArgs;
    if (totalArgs === 0) {
      return { args: [], optArgs: [] };
    }
    const args = [];
    const optArgs = [];
    for (let i = 0; i < totalArgs; i++) {
      let argType = funcData.argTypes && funcData.argTypes[i];
      const isOptional = i < funcData.numOptionalArgs;
      if (funcData.primitive && argType == null || // \sqrt expands into primitive if optional argument doesn't exist
      funcData.type === "sqrt" && i === 1 && optArgs[0] == null) {
        argType = "primitive";
      }
      const arg = this.parseGroupOfType(`argument to '${func}'`, argType, isOptional);
      if (isOptional) {
        optArgs.push(arg);
      } else if (arg != null) {
        args.push(arg);
      } else {
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
        const group = this.parseArgumentGroup(optional, "text");
        return group != null ? {
          type: "styling",
          mode: group.mode,
          body: [group],
          scriptLevel: "text"
          // simulate \textstyle
        } : null;
      }
      case "raw": {
        const token = this.parseStringGroup("raw", optional);
        return token != null ? {
          type: "raw",
          mode: "text",
          string: token.text
        } : null;
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
      case void 0:
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
      if (ch === " " || ch === "\xA0" || ch === "\uFE0E") {
        this.consume();
      } else {
        break;
      }
    }
  }
  /**
   * Parses a group, essentially returning the string formed by the
   * brace-enclosed tokens plus some position information.
   */
  parseStringGroup(modeName, optional) {
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
    this.consume();
    argToken.text = str;
    return argToken;
  }
  /**
   * Parses a regex-delimited group: the largest sequence of tokens
   * whose concatenated strings match `regex`. Returns the string
   * formed by the tokens plus some position information.
   */
  parseRegexGroup(regex, modeName) {
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
      res.text = "0pt";
      isBlank = true;
    }
    const match = sizeRegEx.exec(res.text);
    if (!match) {
      throw new ParseError("Invalid size: '" + res.text + "'", res);
    }
    const data = {
      number: +(match[1] + match[2]),
      // sign + magnitude, cast to number
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
    this.gullet.lexer.setCatcode("%", 13);
    this.gullet.lexer.setCatcode("~", 12);
    const res = this.parseStringGroup("url", optional);
    this.gullet.lexer.setCatcode("%", 14);
    this.gullet.lexer.setCatcode("~", 13);
    if (res == null) {
      return null;
    }
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
      this.switchMode(mode);
    }
    this.gullet.beginGroup();
    const expression = this.parseExpression(false, "EOF");
    this.expect("EOF");
    this.gullet.endGroup();
    const result = {
      type: "ordgroup",
      mode: this.mode,
      loc: argToken.loc,
      body: expression
    };
    if (mode) {
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
  parseGroup(name, breakOnTokenText) {
    const firstToken = this.fetch();
    const text2 = firstToken.text;
    let result;
    if (text2 === "{" || text2 === "\\begingroup" || text2 === "\\toggle") {
      this.consume();
      const groupEnd = text2 === "{" ? "}" : text2 === "\\begingroup" ? "\\endgroup" : "\\endtoggle";
      this.gullet.beginGroup();
      const expression = this.parseExpression(false, groupEnd);
      const lastToken = this.fetch();
      this.expect(groupEnd);
      this.gullet.endGroup();
      result = {
        type: lastToken.text === "\\endtoggle" ? "toggle" : "ordgroup",
        mode: this.mode,
        loc: SourceLocation.range(firstToken, lastToken),
        body: expression,
        // A group formed by \begingroup...\endgroup is a semi-simple group
        // which doesn't affect spacing in math mode, i.e., is transparent.
        // https://tex.stackexchange.com/questions/1930/
        semisimple: text2 === "\\begingroup" || void 0
      };
    } else {
      result = this.parseFunction(breakOnTokenText, name) || this.parseSymbol();
      if (result == null && text2[0] === "\\" && !Object.prototype.hasOwnProperty.call(implicitCommands, text2)) {
        result = this.formatUnsupportedCmd(text2);
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
    let text2 = nucleus.text;
    if (/^\\verb[^a-zA-Z]/.test(text2)) {
      this.consume();
      let arg = text2.slice(5);
      const star = arg.charAt(0) === "*";
      if (star) {
        arg = arg.slice(1);
      }
      if (arg.length < 2 || arg.charAt(0) !== arg.slice(-1)) {
        throw new ParseError(`\\verb assertion failed --
                    please report what input caused this bug`);
      }
      arg = arg.slice(1, -1);
      return {
        type: "verb",
        mode: "text",
        body: arg,
        star
      };
    }
    if (Object.prototype.hasOwnProperty.call(unicodeSymbols, text2[0]) && this.mode === "math" && !symbols[this.mode][text2[0]]) {
      if (this.settings.strict && this.mode === "math") {
        throw new ParseError(
          `Accented Unicode text character "${text2[0]}" used in math mode`,
          nucleus
        );
      }
      text2 = unicodeSymbols[text2[0]] + text2.slice(1);
    }
    const match = this.mode === "math" ? combiningDiacriticalMarksEndRegex.exec(text2) : null;
    if (match) {
      text2 = text2.substring(0, match.index);
      if (text2 === "i") {
        text2 = "\u0131";
      } else if (text2 === "j") {
        text2 = "\u0237";
      }
    }
    let symbol;
    if (symbols[this.mode][text2]) {
      let group = symbols[this.mode][text2].group;
      if (group === "bin" && binLeftCancellers.includes(this.prevAtomType)) {
        group = "open";
      }
      const loc = SourceLocation.range(nucleus);
      let s;
      if (Object.prototype.hasOwnProperty.call(ATOMS, group)) {
        const family = group;
        s = {
          type: "atom",
          mode: this.mode,
          family,
          loc,
          text: text2
        };
      } else {
        if (asciiFromScript[text2]) {
          this.consume();
          const nextCode = this.fetch().text.charCodeAt(0);
          const font = nextCode === 65025 ? "mathscr" : "mathcal";
          if (nextCode === 65024 || nextCode === 65025) {
            this.consume();
          }
          return {
            type: "font",
            mode: "math",
            font,
            body: { type: "mathord", mode: "math", loc, text: asciiFromScript[text2] }
          };
        }
        s = {
          type: group,
          mode: this.mode,
          loc,
          text: text2
        };
      }
      symbol = s;
    } else if (text2.charCodeAt(0) >= 128 || combiningDiacriticalMarksEndRegex.exec(text2)) {
      if (this.settings.strict && this.mode === "math") {
        throw new ParseError(`Unicode text character "${text2[0]}" used in math mode`, nucleus);
      }
      symbol = {
        type: "textord",
        mode: "text",
        loc: SourceLocation.range(nucleus),
        text: text2
      };
    } else {
      return null;
    }
    this.consume();
    if (match) {
      for (let i = 0; i < match[0].length; i++) {
        const accent2 = match[0][i];
        if (!unicodeAccents[accent2]) {
          throw new ParseError(`Unknown accent ' ${accent2}'`, nucleus);
        }
        const command = unicodeAccents[accent2][this.mode] || unicodeAccents[accent2].text;
        if (!command) {
          throw new ParseError(`Accent ${accent2} unsupported in ${this.mode} mode`, nucleus);
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
};
var parseTree = function(toParse, settings) {
  if (!(typeof toParse === "string" || toParse instanceof String)) {
    throw new TypeError("Temml can only parse string typed expression");
  }
  const parser = new Parser(toParse, settings);
  delete parser.gullet.macros.current["\\df@tag"];
  let tree = parser.parse();
  if (!(tree.length > 0 && tree[0].type && tree[0].type === "array" && tree[0].addEqnNum)) {
    if (parser.gullet.macros.get("\\df@tag")) {
      if (!settings.displayMode) {
        throw new ParseError("\\tag works only in display mode");
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
  return tree;
};
var subOrSupLevel = [2, 2, 3, 3];
var Style = class _Style {
  constructor(data) {
    this.level = data.level;
    this.color = data.color;
    this.font = data.font || "";
    this.fontFamily = data.fontFamily || "";
    this.fontSize = data.fontSize || 1;
    this.fontWeight = data.fontWeight || "";
    this.fontShape = data.fontShape || "";
    this.maxSize = data.maxSize;
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
    return new _Style(data);
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
    });
  }
  /**
   * Create a new style object with the given color.
   */
  withColor(color) {
    return this.extend({
      color
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
};
var version = "0.10.34";
function postProcess(block) {
  const labelMap = {};
  let i = 0;
  const amsEqns = document.getElementsByClassName("tml-eqn");
  for (let parent of amsEqns) {
    i += 1;
    parent.setAttribute("id", "tml-eqn-" + String(i));
    while (true) {
      if (parent.tagName === "mtable") {
        break;
      }
      const labels = parent.getElementsByClassName("tml-label");
      if (labels.length > 0) {
        const id = parent.attributes.id.value;
        labelMap[id] = String(i);
        break;
      } else {
        parent = parent.parentElement;
      }
    }
  }
  const taggedEqns = document.getElementsByClassName("tml-tageqn");
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
  const refs = block.getElementsByClassName("tml-ref");
  [...refs].forEach((ref) => {
    const attr = ref.getAttribute("href");
    let str = labelMap[attr.slice(1)];
    if (ref.className.indexOf("tml-eqref") === -1) {
      str = str.replace(/^\(/, "");
      str = str.replace(/\)$/, "");
    } else {
      if (str.charAt(0) !== "(") {
        str = "(" + str;
      }
      if (str.slice(-1) !== ")") {
        str = str + ")";
      }
    }
    const mtext = document.createElementNS("http://www.w3.org/1998/Math/MathML", "mtext");
    mtext.appendChild(document.createTextNode(str));
    const math2 = document.createElementNS("http://www.w3.org/1998/Math/MathML", "math");
    math2.appendChild(mtext);
    ref.appendChild(math2);
  });
}
var render = function(expression, baseNode, options = {}) {
  baseNode.textContent = "";
  const alreadyInMathElement = baseNode.tagName.toLowerCase() === "math";
  if (alreadyInMathElement) {
    options.wrap = "none";
  }
  const math2 = renderToMathMLTree(expression, options);
  if (alreadyInMathElement) {
    baseNode.textContent = "";
    math2.children.forEach((e) => {
      baseNode.appendChild(e.toNode());
    });
  } else if (math2.children.length > 1) {
    baseNode.textContent = "";
    math2.children.forEach((e) => {
      baseNode.appendChild(e.toNode());
    });
  } else {
    baseNode.appendChild(math2.toNode());
  }
};
if (typeof document !== "undefined") {
  if (document.compatMode !== "CSS1Compat") {
    typeof console !== "undefined" && console.warn(
      "Warning: Temml doesn't work in quirks mode. Make sure your website has a suitable doctype."
    );
    render = function() {
      throw new ParseError("Temml doesn't work in quirks mode.");
    };
  }
}
var renderToString = function(expression, options) {
  const markup = renderToMathMLTree(expression, options).toMarkup();
  return markup;
};
var generateParseTree = function(expression, options) {
  const settings = new Settings(options);
  return parseTree(expression, settings);
};
var definePreamble = function(expression, options) {
  const settings = new Settings(options);
  settings.macros = {};
  if (!(typeof expression === "string" || expression instanceof String)) {
    throw new TypeError("Temml can only parse string typed expression");
  }
  const parser = new Parser(expression, settings, true);
  delete parser.gullet.macros.current["\\df@tag"];
  const macros2 = parser.parse();
  return macros2;
};
var renderError = function(error, expression, options) {
  if (options.throwOnError || !(error instanceof ParseError)) {
    throw error;
  }
  const node = new Span(["temml-error"], [new TextNode$1(expression + "\n" + error.toString())]);
  node.style.color = options.errorColor;
  node.style.whiteSpace = "pre-line";
  return node;
};
var renderToMathMLTree = function(expression, options) {
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
var temml = {
  /**
   * Current Temml version
   */
  version,
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

// packages/latex-to-mathml/build-module/index.js
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
export {
  latexToMathML as default
};
//# sourceMappingURL=index.js.map
