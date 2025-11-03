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
/************************************************************************/
var __webpack_exports__ = {};

// EXPORTS
__webpack_require__.d(__webpack_exports__, {
  "default": () => (/* binding */ index_default)
});

// UNUSED EXPORTS: attrs, fromMatch, next, regexp, replace, string

;// ./node_modules/memize/dist/index.js
/**
 * Memize options object.
 *
 * @typedef MemizeOptions
 *
 * @property {number} [maxSize] Maximum size of the cache.
 */

/**
 * Internal cache entry.
 *
 * @typedef MemizeCacheNode
 *
 * @property {?MemizeCacheNode|undefined} [prev] Previous node.
 * @property {?MemizeCacheNode|undefined} [next] Next node.
 * @property {Array<*>}                   args   Function arguments for cache
 *                                               entry.
 * @property {*}                          val    Function result.
 */

/**
 * Properties of the enhanced function for controlling cache.
 *
 * @typedef MemizeMemoizedFunction
 *
 * @property {()=>void} clear Clear the cache.
 */

/**
 * Accepts a function to be memoized, and returns a new memoized function, with
 * optional options.
 *
 * @template {(...args: any[]) => any} F
 *
 * @param {F}             fn        Function to memoize.
 * @param {MemizeOptions} [options] Options object.
 *
 * @return {((...args: Parameters<F>) => ReturnType<F>) & MemizeMemoizedFunction} Memoized function.
 */
function memize(fn, options) {
	var size = 0;

	/** @type {?MemizeCacheNode|undefined} */
	var head;

	/** @type {?MemizeCacheNode|undefined} */
	var tail;

	options = options || {};

	function memoized(/* ...args */) {
		var node = head,
			len = arguments.length,
			args,
			i;

		searchCache: while (node) {
			// Perform a shallow equality test to confirm that whether the node
			// under test is a candidate for the arguments passed. Two arrays
			// are shallowly equal if their length matches and each entry is
			// strictly equal between the two sets. Avoid abstracting to a
			// function which could incur an arguments leaking deoptimization.

			// Check whether node arguments match arguments length
			if (node.args.length !== arguments.length) {
				node = node.next;
				continue;
			}

			// Check whether node arguments match arguments values
			for (i = 0; i < len; i++) {
				if (node.args[i] !== arguments[i]) {
					node = node.next;
					continue searchCache;
				}
			}

			// At this point we can assume we've found a match

			// Surface matched node to head if not already
			if (node !== head) {
				// As tail, shift to previous. Must only shift if not also
				// head, since if both head and tail, there is no previous.
				if (node === tail) {
					tail = node.prev;
				}

				// Adjust siblings to point to each other. If node was tail,
				// this also handles new tail's empty `next` assignment.
				/** @type {MemizeCacheNode} */ (node.prev).next = node.next;
				if (node.next) {
					node.next.prev = node.prev;
				}

				node.next = head;
				node.prev = null;
				/** @type {MemizeCacheNode} */ (head).prev = node;
				head = node;
			}

			// Return immediately
			return node.val;
		}

		// No cached value found. Continue to insertion phase:

		// Create a copy of arguments (avoid leaking deoptimization)
		args = new Array(len);
		for (i = 0; i < len; i++) {
			args[i] = arguments[i];
		}

		node = {
			args: args,

			// Generate the result from original function
			val: fn.apply(null, args),
		};

		// Don't need to check whether node is already head, since it would
		// have been returned above already if it was

		// Shift existing head down list
		if (head) {
			head.prev = node;
			node.next = head;
		} else {
			// If no head, follows that there's no tail (at initial or reset)
			tail = node;
		}

		// Trim tail if we're reached max size and are pending cache insertion
		if (size === /** @type {MemizeOptions} */ (options).maxSize) {
			tail = /** @type {MemizeCacheNode} */ (tail).prev;
			/** @type {MemizeCacheNode} */ (tail).next = null;
		} else {
			size++;
		}

		head = node;

		return node.val;
	}

	memoized.clear = function () {
		head = null;
		tail = null;
		size = 0;
	};

	// Ignore reason: There's not a clear solution to create an intersection of
	// the function with additional properties, where the goal is to retain the
	// function signature of the incoming argument and add control properties
	// on the return value.

	// @ts-ignore
	return memoized;
}



;// ./node_modules/@wordpress/shortcode/build-module/index.js


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
const attrs = memize((text) => {
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
const shortcode = Object.assign(
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


(window.wp = window.wp || {}).shortcode = __webpack_exports__["default"];
/******/ })()
;