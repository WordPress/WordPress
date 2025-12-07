"use strict";
var wp;
(wp ||= {}).shortcode = (() => {
  var __defProp = Object.defineProperty;
  var __getOwnPropDesc = Object.getOwnPropertyDescriptor;
  var __getOwnPropNames = Object.getOwnPropertyNames;
  var __hasOwnProp = Object.prototype.hasOwnProperty;
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
  var __toCommonJS = (mod) => __copyProps(__defProp({}, "__esModule", { value: true }), mod);

  // packages/shortcode/build-module/index.js
  var index_exports = {};
  __export(index_exports, {
    attrs: () => attrs,
    default: () => index_default,
    fromMatch: () => fromMatch,
    next: () => next,
    regexp: () => regexp,
    replace: () => replace,
    string: () => string
  });

  // node_modules/memize/dist/index.js
  function memize(fn, options) {
    var size = 0;
    var head;
    var tail;
    options = options || {};
    function memoized() {
      var node = head, len = arguments.length, args, i;
      searchCache: while (node) {
        if (node.args.length !== arguments.length) {
          node = node.next;
          continue;
        }
        for (i = 0; i < len; i++) {
          if (node.args[i] !== arguments[i]) {
            node = node.next;
            continue searchCache;
          }
        }
        if (node !== head) {
          if (node === tail) {
            tail = node.prev;
          }
          node.prev.next = node.next;
          if (node.next) {
            node.next.prev = node.prev;
          }
          node.next = head;
          node.prev = null;
          head.prev = node;
          head = node;
        }
        return node.val;
      }
      args = new Array(len);
      for (i = 0; i < len; i++) {
        args[i] = arguments[i];
      }
      node = {
        args,
        // Generate the result from original function
        val: fn.apply(null, args)
      };
      if (head) {
        head.prev = node;
        node.next = head;
      } else {
        tail = node;
      }
      if (size === /** @type {MemizeOptions} */
      options.maxSize) {
        tail = /** @type {MemizeCacheNode} */
        tail.prev;
        tail.next = null;
      } else {
        size++;
      }
      head = node;
      return node.val;
    }
    memoized.clear = function() {
      head = null;
      tail = null;
      size = 0;
    };
    return memoized;
  }

  // packages/shortcode/build-module/index.js
  function next(tag, text, index = 0) {
    const re = regexp(tag);
    re.lastIndex = index;
    const match = re.exec(text);
    if (!match) {
      return;
    }
    if ("[" === match[1] && "]" === match[7]) {
      return next(tag, text, re.lastIndex);
    }
    const result = {
      index: match.index,
      content: match[0],
      shortcode: fromMatch(match)
    };
    if (match[1]) {
      result.content = result.content.slice(1);
      result.index++;
    }
    if (match[7]) {
      result.content = result.content.slice(0, -1);
    }
    return result;
  }
  function replace(tag, text, callback) {
    return text.replace(
      regexp(tag),
      function(match, left, $3, attrs2, slash, content, closing, right) {
        if (left === "[" && right === "]") {
          return match;
        }
        const result = callback(fromMatch(arguments));
        return result || result === "" ? left + result + right : match;
      }
    );
  }
  function string(options) {
    return new shortcode(options).string();
  }
  function regexp(tag) {
    return new RegExp(
      "\\[(\\[?)(" + tag + ")(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)",
      "g"
    );
  }
  var attrs = memize((text) => {
    const named = {};
    const numeric = [];
    const pattern = /([\w-]+)\s*=\s*"([^"]*)"(?:\s|$)|([\w-]+)\s*=\s*'([^']*)'(?:\s|$)|([\w-]+)\s*=\s*([^\s'"]+)(?:\s|$)|"([^"]*)"(?:\s|$)|'([^']*)'(?:\s|$)|(\S+)(?:\s|$)/g;
    text = text.replace(/[\u00a0\u200b]/g, " ");
    let match;
    while (match = pattern.exec(text)) {
      if (match[1]) {
        named[match[1].toLowerCase()] = match[2];
      } else if (match[3]) {
        named[match[3].toLowerCase()] = match[4];
      } else if (match[5]) {
        named[match[5].toLowerCase()] = match[6];
      } else if (match[7]) {
        numeric.push(match[7]);
      } else if (match[8]) {
        numeric.push(match[8]);
      } else if (match[9]) {
        numeric.push(match[9]);
      }
    }
    return { named, numeric };
  });
  function fromMatch(match) {
    let type;
    if (match[4]) {
      type = "self-closing";
    } else if (match[6]) {
      type = "closed";
    } else {
      type = "single";
    }
    return new shortcode({
      tag: match[2],
      attrs: match[3],
      type,
      content: match[5]
    });
  }
  var shortcode = Object.assign(
    function(options) {
      const { tag, attrs: attributes, type, content } = options || {};
      Object.assign(this, { tag, type, content });
      this.attrs = {
        named: {},
        numeric: []
      };
      if (!attributes) {
        return;
      }
      const attributeTypes = ["named", "numeric"];
      if (typeof attributes === "string") {
        this.attrs = attrs(attributes);
      } else if (attributes.length === attributeTypes.length && attributeTypes.every((t, key) => t === attributes[key])) {
        this.attrs = attributes;
      } else {
        Object.entries(attributes).forEach(([key, value]) => {
          this.set(key, value);
        });
      }
    },
    {
      next,
      replace,
      string,
      regexp,
      attrs,
      fromMatch
    }
  );
  Object.assign(shortcode.prototype, {
    /**
     * Get a shortcode attribute.
     *
     * Automatically detects whether `attr` is named or numeric and routes it
     * accordingly.
     *
     * @param {(number|string)} attr Attribute key.
     *
     * @return {string} Attribute value.
     */
    get(attr) {
      return this.attrs[typeof attr === "number" ? "numeric" : "named"][attr];
    },
    /**
     * Set a shortcode attribute.
     *
     * Automatically detects whether `attr` is named or numeric and routes it
     * accordingly.
     *
     * @param {(number|string)} attr  Attribute key.
     * @param {string}          value Attribute value.
     *
     * @return {InstanceType< import('./types').shortcode >} Shortcode instance.
     */
    set(attr, value) {
      this.attrs[typeof attr === "number" ? "numeric" : "named"][attr] = value;
      return this;
    },
    /**
     * Transform the shortcode into a string.
     *
     * @return {string} String representation of the shortcode.
     */
    string() {
      let text = "[" + this.tag;
      this.attrs.numeric.forEach((value) => {
        if (/\s/.test(value)) {
          text += ' "' + value + '"';
        } else {
          text += " " + value;
        }
      });
      Object.entries(this.attrs.named).forEach(([name, value]) => {
        text += " " + name + '="' + value + '"';
      });
      if ("single" === this.type) {
        return text + "]";
      } else if ("self-closing" === this.type) {
        return text + " /]";
      }
      text += "]";
      if (this.content) {
        text += this.content;
      }
      return text + "[/" + this.tag + "]";
    }
  });
  var index_default = shortcode;
  return __toCommonJS(index_exports);
})();
if (typeof wp.shortcode === 'object' && wp.shortcode.default) { wp.shortcode = wp.shortcode.default; }
//# sourceMappingURL=index.js.map
