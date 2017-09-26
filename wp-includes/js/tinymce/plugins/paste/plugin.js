(function () {

var defs = {}; // id -> {dependencies, definition, instance (possibly undefined)}

// Used when there is no 'main' module.
// The name is probably (hopefully) unique so minification removes for releases.
var register_3795 = function (id) {
  var module = dem(id);
  var fragments = id.split('.');
  var target = Function('return this;')();
  for (var i = 0; i < fragments.length - 1; ++i) {
    if (target[fragments[i]] === undefined)
      target[fragments[i]] = {};
    target = target[fragments[i]];
  }
  target[fragments[fragments.length - 1]] = module;
};

var instantiate = function (id) {
  var actual = defs[id];
  var dependencies = actual.deps;
  var definition = actual.defn;
  var len = dependencies.length;
  var instances = new Array(len);
  for (var i = 0; i < len; ++i)
    instances[i] = dem(dependencies[i]);
  var defResult = definition.apply(null, instances);
  if (defResult === undefined)
     throw 'module [' + id + '] returned undefined';
  actual.instance = defResult;
};

var def = function (id, dependencies, definition) {
  if (typeof id !== 'string')
    throw 'module id must be a string';
  else if (dependencies === undefined)
    throw 'no dependencies for ' + id;
  else if (definition === undefined)
    throw 'no definition function for ' + id;
  defs[id] = {
    deps: dependencies,
    defn: definition,
    instance: undefined
  };
};

var dem = function (id) {
  var actual = defs[id];
  if (actual === undefined)
    throw 'module [' + id + '] was undefined';
  else if (actual.instance === undefined)
    instantiate(id);
  return actual.instance;
};

var req = function (ids, callback) {
  var len = ids.length;
  var instances = new Array(len);
  for (var i = 0; i < len; ++i)
    instances.push(dem(ids[i]));
  callback.apply(null, callback);
};

var ephox = {};

ephox.bolt = {
  module: {
    api: {
      define: def,
      require: req,
      demand: dem
    }
  }
};

var define = def;
var require = req;
var demand = dem;
// this helps with minificiation when using a lot of global references
var defineGlobal = function (id, ref) {
  define(id, [], function () { return ref; });
};
/*jsc
["tinymce.plugins.paste.Plugin","tinymce.core.PluginManager","tinymce.plugins.paste.api.Events","tinymce.plugins.paste.core.Clipboard","tinymce.plugins.paste.core.CutCopy","tinymce.plugins.paste.core.Quirks","global!tinymce.util.Tools.resolve","tinymce.core.dom.RangeUtils","tinymce.core.Env","tinymce.core.util.Delay","tinymce.core.util.Tools","tinymce.core.util.VK","tinymce.plugins.paste.core.InternalHtml","tinymce.plugins.paste.core.Newlines","tinymce.plugins.paste.core.PasteBin","tinymce.plugins.paste.core.ProcessFilters","tinymce.plugins.paste.core.SmartPaste","tinymce.plugins.paste.core.Utils","tinymce.plugins.paste.core.WordFilter","tinymce.core.html.Entities","tinymce.core.html.DomParser","tinymce.core.html.Schema","tinymce.core.html.Serializer","tinymce.core.html.Node"]
jsc*/
defineGlobal("global!tinymce.util.Tools.resolve", tinymce.util.Tools.resolve);
/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.PluginManager',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.PluginManager');
  }
);

/**
 * Events.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.paste.api.Events',
  [
  ],
  function () {
    var firePastePreProcess = function (editor, html, internal, isWordHtml) {
      return editor.fire('PastePreProcess', { content: html, internal: internal, wordContent: isWordHtml });
    };

    var firePastePostProcess = function (editor, node, internal, isWordHtml) {
      return editor.fire('PastePostProcess', { node: node, internal: internal, wordContent: isWordHtml });
    };

    var firePastePlainTextToggle = function (editor, state) {
      return editor.fire('PastePlainTextToggle', { state: state });
    };

    return {
      firePastePreProcess: firePastePreProcess,
      firePastePostProcess: firePastePostProcess,
      firePastePlainTextToggle: firePastePlainTextToggle
    };
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.dom.RangeUtils',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.dom.RangeUtils');
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.Env',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.Env');
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.util.Delay',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.util.Delay');
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.util.Tools',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.util.Tools');
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.util.VK',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.util.VK');
  }
);

/**
 * InternalHtml.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.paste.core.InternalHtml',
  [
  ],
  function () {
    var internalMimeType = 'x-tinymce/html';
    var internalMark = '<!-- ' + internalMimeType + ' -->';

    var mark = function (html) {
      return internalMark + html;
    };

    var unmark = function (html) {
      return html.replace(internalMark, '');
    };

    var isMarked = function (html) {
      return html.indexOf(internalMark) !== -1;
    };

    return {
      mark: mark,
      unmark: unmark,
      isMarked: isMarked,
      internalHtmlMime: function () {
        return internalMimeType;
      }
    };
  }
);
/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.html.Entities',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.html.Entities');
  }
);

/**
 * Newlines.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * Newlines class contains utilities to convert newlines (\n or \r\n) tp BRs or to a combination of the specified block element and BRs
 *
 * @class tinymce.Newlines
 * @private
 */
define(
  'tinymce.plugins.paste.core.Newlines',
  [
    'tinymce.core.util.Tools',
    'tinymce.core.html.Entities'
  ],
  function (Tools, Entities) {

    var isPlainText = function (text) {
      // so basically any tag that is not one of the "p, div, span, br", or is one of them, but is followed
      // by some additional characters qualifies the text as not a plain text (having some HTML tags)
      // <span style="white-space:pre"> and <br /> are added as separate exceptions to the rule
      return !/<(?:\/?(?!(?:div|p|br|span)>)\w+|(?:(?!(?:span style="white-space:\s?pre;?">)|br\s?\/>))\w+\s[^>]+)>/i.test(text);
    };


    var toBRs = function (text) {
      return text.replace(/\r?\n/g, '<br>');
    };


    var openContainer = function (rootTag, rootAttrs) {
      var key, attrs = [];
      var tag = '<' + rootTag;

      if (typeof rootAttrs === 'object') {
        for (key in rootAttrs) {
          if (rootAttrs.hasOwnProperty(key)) {
            attrs.push(key + '="' + Entities.encodeAllRaw(rootAttrs[key]) + '"');
          }
        }

        if (attrs.length) {
          tag += ' ' + attrs.join(' ');
        }
      }
      return tag + '>';
    };


    var toBlockElements = function (text, rootTag, rootAttrs) {
      var blocks = text.split(/\n\n/);
      var tagOpen = openContainer(rootTag, rootAttrs);
      var tagClose = '</' + rootTag + '>';

      var paragraphs = Tools.map(blocks, function (p) {
        return p.split(/\n/).join('<br />');
      });

      var stitch = function (p) {
        return tagOpen + p + tagClose;
      };

      return paragraphs.length === 1 ? paragraphs[0] : Tools.map(paragraphs, stitch).join('');
    };


    var convert = function (text, rootTag, rootAttrs) {
      return rootTag ? toBlockElements(text, rootTag, rootAttrs) : toBRs(text);
    };


    return {
      isPlainText: isPlainText,
      convert: convert,
      toBRs: toBRs,
      toBlockElements: toBlockElements
    };
  }
);
/**
 * PasteBin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * @class tinymce.pasteplugin.PasteBin
 * @private
 */
define(
  'tinymce.plugins.paste.core.PasteBin',
  [
    'tinymce.core.util.Tools',
    'tinymce.core.Env'
  ],
  function (Tools, Env) {
    return function (editor) {
      var lastRng;
      var pasteBinDefaultContent = '%MCEPASTEBIN%';

      /**
       * Creates a paste bin element as close as possible to the current caret location and places the focus inside that element
       * so that when the real paste event occurs the contents gets inserted into this element
       * instead of the current editor selection element.
       */
      var create = function () {
        var dom = editor.dom, body = editor.getBody();
        var viewport = editor.dom.getViewPort(editor.getWin()), scrollTop = viewport.y, top = 20;
        var pasteBinElm;
        var scrollContainer;

        lastRng = editor.selection.getRng();

        if (editor.inline) {
          scrollContainer = editor.selection.getScrollContainer();

          // Can't always rely on scrollTop returning a useful value.
          // It returns 0 if the browser doesn't support scrollTop for the element or is non-scrollable
          if (scrollContainer && scrollContainer.scrollTop > 0) {
            scrollTop = scrollContainer.scrollTop;
          }
        }

        /**
         * Returns the rect of the current caret if the caret is in an empty block before a
         * BR we insert a temporary invisible character that we get the rect this way we always get a proper rect.
         *
         * TODO: This might be useful in core.
         */
        function getCaretRect(rng) {
          var rects, textNode, node, container = rng.startContainer;

          rects = rng.getClientRects();
          if (rects.length) {
            return rects[0];
          }

          if (!rng.collapsed || container.nodeType != 1) {
            return;
          }

          node = container.childNodes[lastRng.startOffset];

          // Skip empty whitespace nodes
          while (node && node.nodeType == 3 && !node.data.length) {
            node = node.nextSibling;
          }

          if (!node) {
            return;
          }

          // Check if the location is |<br>
          // TODO: Might need to expand this to say |<table>
          if (node.tagName == 'BR') {
            textNode = dom.doc.createTextNode('\uFEFF');
            node.parentNode.insertBefore(textNode, node);

            rng = dom.createRng();
            rng.setStartBefore(textNode);
            rng.setEndAfter(textNode);

            rects = rng.getClientRects();
            dom.remove(textNode);
          }

          if (rects.length) {
            return rects[0];
          }
        }

        // Calculate top cordinate this is needed to avoid scrolling to top of document
        // We want the paste bin to be as close to the caret as possible to avoid scrolling
        if (lastRng.getClientRects) {
          var rect = getCaretRect(lastRng);

          if (rect) {
            // Client rects gets us closes to the actual
            // caret location in for example a wrapped paragraph block
            top = scrollTop + (rect.top - dom.getPos(body).y);
          } else {
            top = scrollTop;

            // Check if we can find a closer location by checking the range element
            var container = lastRng.startContainer;
            if (container) {
              if (container.nodeType == 3 && container.parentNode != body) {
                container = container.parentNode;
              }

              if (container.nodeType == 1) {
                top = dom.getPos(container, scrollContainer || body).y;
              }
            }
          }
        }

        // Create a pastebin
        pasteBinElm = editor.dom.add(editor.getBody(), 'div', {
          id: "mcepastebin",
          contentEditable: true,
          "data-mce-bogus": "all",
          style: 'position: absolute; top: ' + top + 'px; width: 10px; height: 10px; overflow: hidden; opacity: 0'
        }, pasteBinDefaultContent);

        // Move paste bin out of sight since the controlSelection rect gets displayed otherwise on IE and Gecko
        if (Env.ie || Env.gecko) {
          dom.setStyle(pasteBinElm, 'left', dom.getStyle(body, 'direction', true) == 'rtl' ? 0xFFFF : -0xFFFF);
        }

        // Prevent focus events from bubbeling fixed FocusManager issues
        dom.bind(pasteBinElm, 'beforedeactivate focusin focusout', function (e) {
          e.stopPropagation();
        });

        pasteBinElm.focus();
        editor.selection.select(pasteBinElm, true);
      };

      /**
       * Removes the paste bin if it exists.
       */
      var remove = function () {
        if (getEl()) {
          var pasteBinClone;

          // WebKit/Blink might clone the div so
          // lets make sure we remove all clones
          // TODO: Man o man is this ugly. WebKit is the new IE! Remove this if they ever fix it!
          while ((pasteBinClone = editor.dom.get('mcepastebin'))) {
            editor.dom.remove(pasteBinClone);
            editor.dom.unbind(pasteBinClone);
          }

          if (lastRng) {
            editor.selection.setRng(lastRng);
          }
        }

        lastRng = null;
      };


      var getEl = function () {
        return editor.dom.get('mcepastebin');
      };

      /**
       * Returns the contents of the paste bin as a HTML string.
       *
       * @return {String} Get the contents of the paste bin.
       */
      var getHtml = function () {
        var pasteBinElm, pasteBinClones, i, dirtyWrappers, cleanWrapper;

        // Since WebKit/Chrome might clone the paste bin when pasting
        // for example: <img style="float: right"> we need to check if any of them contains some useful html.
        // TODO: Man o man is this ugly. WebKit is the new IE! Remove this if they ever fix it!

        var copyAndRemove = function (toElm, fromElm) {
          toElm.appendChild(fromElm);
          editor.dom.remove(fromElm, true); // remove, but keep children
        };

        // find only top level elements (there might be more nested inside them as well, see TINY-1162)
        pasteBinClones = Tools.grep(editor.getBody().childNodes, function (elm) {
          return elm.id === 'mcepastebin';
        });
        pasteBinElm = pasteBinClones.shift();

        // if clones were found, move their content into the first bin
        Tools.each(pasteBinClones, function (pasteBinClone) {
          copyAndRemove(pasteBinElm, pasteBinClone);
        });

        // TINY-1162: when copying plain text (from notepad for example) WebKit clones
        // paste bin (with styles and attributes) and uses it as a default  wrapper for
        // the chunks of the content, here we cycle over the whole paste bin and replace
        // those wrappers with a basic div
        dirtyWrappers = editor.dom.select('div[id=mcepastebin]', pasteBinElm);
        for (i = dirtyWrappers.length - 1; i >= 0; i--) {
          cleanWrapper = editor.dom.create('div');
          pasteBinElm.insertBefore(cleanWrapper, dirtyWrappers[i]);
          copyAndRemove(cleanWrapper, dirtyWrappers[i]);
        }

        return pasteBinElm ? pasteBinElm.innerHTML : '';
      };


      var getLastRng = function () {
        return lastRng;
      };


      var isDefaultContent = function (content) {
        return content === pasteBinDefaultContent;
      };


      var isPasteBin = function (elm) {
        return elm && elm.id === 'mcepastebin';
      };


      var isDefault = function () {
        var pasteBinElm = getEl();
        return isPasteBin(pasteBinElm) && isDefaultContent(pasteBinElm.innerHTML);
      };

      return {
        create: create,
        remove: remove,
        getEl: getEl,
        getHtml: getHtml,
        getLastRng: getLastRng,
        isDefault: isDefault,
        isDefaultContent: isDefaultContent
      };
    };
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.html.DomParser',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.html.DomParser');
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.html.Schema',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.html.Schema');
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.html.Serializer',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.html.Serializer');
  }
);

/**
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.html.Node',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.html.Node');
  }
);

/**
 * Utils.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * This class contails various utility functions for the paste plugin.
 *
 * @class tinymce.pasteplugin.Utils
 */
define(
  'tinymce.plugins.paste.core.Utils',
  [
    'tinymce.core.util.Tools',
    'tinymce.core.html.DomParser',
    'tinymce.core.html.Schema'
  ],
  function (Tools, DomParser, Schema) {
    function filter(content, items) {
      Tools.each(items, function (v) {
        if (v.constructor == RegExp) {
          content = content.replace(v, '');
        } else {
          content = content.replace(v[0], v[1]);
        }
      });

      return content;
    }

    /**
     * Gets the innerText of the specified element. It will handle edge cases
     * and works better than textContent on Gecko.
     *
     * @param {String} html HTML string to get text from.
     * @return {String} String of text with line feeds.
     */
    function innerText(html) {
      var schema = new Schema(), domParser = new DomParser({}, schema), text = '';
      var shortEndedElements = schema.getShortEndedElements();
      var ignoreElements = Tools.makeMap('script noscript style textarea video audio iframe object', ' ');
      var blockElements = schema.getBlockElements();

      function walk(node) {
        var name = node.name, currentNode = node;

        if (name === 'br') {
          text += '\n';
          return;
        }

        // img/input/hr
        if (shortEndedElements[name]) {
          text += ' ';
        }

        // Ingore script, video contents
        if (ignoreElements[name]) {
          text += ' ';
          return;
        }

        if (node.type == 3) {
          text += node.value;
        }

        // Walk all children
        if (!node.shortEnded) {
          if ((node = node.firstChild)) {
            do {
              walk(node);
            } while ((node = node.next));
          }
        }

        // Add \n or \n\n for blocks or P
        if (blockElements[name] && currentNode.next) {
          text += '\n';

          if (name == 'p') {
            text += '\n';
          }
        }
      }

      html = filter(html, [
        /<!\[[^\]]+\]>/g // Conditional comments
      ]);

      walk(domParser.parse(html));

      return text;
    }

    /**
     * Trims the specified HTML by removing all WebKit fragments, all elements wrapping the body trailing BR elements etc.
     *
     * @param {String} html Html string to trim contents on.
     * @return {String} Html contents that got trimmed.
     */
    function trimHtml(html) {
      function trimSpaces(all, s1, s2) {
        // WebKit &nbsp; meant to preserve multiple spaces but instead inserted around all inline tags,
        // including the spans with inline styles created on paste
        if (!s1 && !s2) {
          return ' ';
        }

        return '\u00a0';
      }

      html = filter(html, [
        /^[\s\S]*<body[^>]*>\s*|\s*<\/body[^>]*>[\s\S]*$/ig, // Remove anything but the contents within the BODY element
        /<!--StartFragment-->|<!--EndFragment-->/g, // Inner fragments (tables from excel on mac)
        [/( ?)<span class="Apple-converted-space">\u00a0<\/span>( ?)/g, trimSpaces],
        /<br class="Apple-interchange-newline">/g,
        /<br>$/i // Trailing BR elements
      ]);

      return html;
    }

    // TODO: Should be in some global class
    function createIdGenerator(prefix) {
      var count = 0;

      return function () {
        return prefix + (count++);
      };
    }

    var isMsEdge = function () {
      return navigator.userAgent.indexOf(' Edge/') !== -1;
    };

    return {
      filter: filter,
      innerText: innerText,
      trimHtml: trimHtml,
      createIdGenerator: createIdGenerator,
      isMsEdge: isMsEdge
    };
  }
);

/**
 * WordFilter.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * This class parses word HTML into proper TinyMCE markup.
 *
 * @class tinymce.pasteplugin.WordFilter
 * @private
 */
define(
  'tinymce.plugins.paste.core.WordFilter',
  [
    'tinymce.core.util.Tools',
    'tinymce.core.html.DomParser',
    'tinymce.core.html.Schema',
    'tinymce.core.html.Serializer',
    'tinymce.core.html.Node',
    'tinymce.plugins.paste.core.Utils'
  ],
  function (Tools, DomParser, Schema, Serializer, Node, Utils) {
    /**
     * Checks if the specified content is from any of the following sources: MS Word/Office 365/Google docs.
     */
    function isWordContent(content) {
      return (
        (/<font face="Times New Roman"|class="?Mso|style="[^"]*\bmso-|style='[^'']*\bmso-|w:WordDocument/i).test(content) ||
        (/class="OutlineElement/).test(content) ||
        (/id="?docs\-internal\-guid\-/.test(content))
      );
    }

    /**
     * Checks if the specified text starts with "1. " or "a. " etc.
     */
    function isNumericList(text) {
      var found, patterns;

      patterns = [
        /^[IVXLMCD]{1,2}\.[ \u00a0]/,  // Roman upper case
        /^[ivxlmcd]{1,2}\.[ \u00a0]/,  // Roman lower case
        /^[a-z]{1,2}[\.\)][ \u00a0]/,  // Alphabetical a-z
        /^[A-Z]{1,2}[\.\)][ \u00a0]/,  // Alphabetical A-Z
        /^[0-9]+\.[ \u00a0]/,          // Numeric lists
        /^[\u3007\u4e00\u4e8c\u4e09\u56db\u4e94\u516d\u4e03\u516b\u4e5d]+\.[ \u00a0]/, // Japanese
        /^[\u58f1\u5f10\u53c2\u56db\u4f0d\u516d\u4e03\u516b\u4e5d\u62fe]+\.[ \u00a0]/  // Chinese
      ];

      text = text.replace(/^[\u00a0 ]+/, '');

      Tools.each(patterns, function (pattern) {
        if (pattern.test(text)) {
          found = true;
          return false;
        }
      });

      return found;
    }

    function isBulletList(text) {
      return /^[\s\u00a0]*[\u2022\u00b7\u00a7\u25CF]\s*/.test(text);
    }

    /**
     * Converts fake bullet and numbered lists to real semantic OL/UL.
     *
     * @param {tinymce.html.Node} node Root node to convert children of.
     */
    function convertFakeListsToProperLists(node) {
      var currentListNode, prevListNode, lastLevel = 1;

      function getText(node) {
        var txt = '';

        if (node.type === 3) {
          return node.value;
        }

        if ((node = node.firstChild)) {
          do {
            txt += getText(node);
          } while ((node = node.next));
        }

        return txt;
      }

      function trimListStart(node, regExp) {
        if (node.type === 3) {
          if (regExp.test(node.value)) {
            node.value = node.value.replace(regExp, '');
            return false;
          }
        }

        if ((node = node.firstChild)) {
          do {
            if (!trimListStart(node, regExp)) {
              return false;
            }
          } while ((node = node.next));
        }

        return true;
      }

      function removeIgnoredNodes(node) {
        if (node._listIgnore) {
          node.remove();
          return;
        }

        if ((node = node.firstChild)) {
          do {
            removeIgnoredNodes(node);
          } while ((node = node.next));
        }
      }

      function convertParagraphToLi(paragraphNode, listName, start) {
        var level = paragraphNode._listLevel || lastLevel;

        // Handle list nesting
        if (level != lastLevel) {
          if (level < lastLevel) {
            // Move to parent list
            if (currentListNode) {
              currentListNode = currentListNode.parent.parent;
            }
          } else {
            // Create new list
            prevListNode = currentListNode;
            currentListNode = null;
          }
        }

        if (!currentListNode || currentListNode.name != listName) {
          prevListNode = prevListNode || currentListNode;
          currentListNode = new Node(listName, 1);

          if (start > 1) {
            currentListNode.attr('start', '' + start);
          }

          paragraphNode.wrap(currentListNode);
        } else {
          currentListNode.append(paragraphNode);
        }

        paragraphNode.name = 'li';

        // Append list to previous list if it exists
        if (level > lastLevel && prevListNode) {
          prevListNode.lastChild.append(currentListNode);
        }

        lastLevel = level;

        // Remove start of list item "1. " or "&middot; " etc
        removeIgnoredNodes(paragraphNode);
        trimListStart(paragraphNode, /^\u00a0+/);
        trimListStart(paragraphNode, /^\s*([\u2022\u00b7\u00a7\u25CF]|\w+\.)/);
        trimListStart(paragraphNode, /^\u00a0+/);
      }

      // Build a list of all root level elements before we start
      // altering them in the loop below.
      var elements = [], child = node.firstChild;
      while (typeof child !== 'undefined' && child !== null) {
        elements.push(child);

        child = child.walk();
        if (child !== null) {
          while (typeof child !== 'undefined' && child.parent !== node) {
            child = child.walk();
          }
        }
      }

      for (var i = 0; i < elements.length; i++) {
        node = elements[i];

        if (node.name == 'p' && node.firstChild) {
          // Find first text node in paragraph
          var nodeText = getText(node);

          // Detect unordered lists look for bullets
          if (isBulletList(nodeText)) {
            convertParagraphToLi(node, 'ul');
            continue;
          }

          // Detect ordered lists 1., a. or ixv.
          if (isNumericList(nodeText)) {
            // Parse OL start number
            var matches = /([0-9]+)\./.exec(nodeText);
            var start = 1;
            if (matches) {
              start = parseInt(matches[1], 10);
            }

            convertParagraphToLi(node, 'ol', start);
            continue;
          }

          // Convert paragraphs marked as lists but doesn't look like anything
          if (node._listLevel) {
            convertParagraphToLi(node, 'ul', 1);
            continue;
          }

          currentListNode = null;
        } else {
          // If the root level element isn't a p tag which can be
          // processed by convertParagraphToLi, it interrupts the
          // lists, causing a new list to start instead of having
          // elements from the next list inserted above this tag.
          prevListNode = currentListNode;
          currentListNode = null;
        }
      }
    }

    function filterStyles(editor, validStyles, node, styleValue) {
      var outputStyles = {}, matches, styles = editor.dom.parseStyle(styleValue);

      Tools.each(styles, function (value, name) {
        // Convert various MS styles to W3C styles
        switch (name) {
          case 'mso-list':
            // Parse out list indent level for lists
            matches = /\w+ \w+([0-9]+)/i.exec(styleValue);
            if (matches) {
              node._listLevel = parseInt(matches[1], 10);
            }

            // Remove these nodes <span style="mso-list:Ignore">o</span>
            // Since the span gets removed we mark the text node and the span
            if (/Ignore/i.test(value) && node.firstChild) {
              node._listIgnore = true;
              node.firstChild._listIgnore = true;
            }

            break;

          case "horiz-align":
            name = "text-align";
            break;

          case "vert-align":
            name = "vertical-align";
            break;

          case "font-color":
          case "mso-foreground":
            name = "color";
            break;

          case "mso-background":
          case "mso-highlight":
            name = "background";
            break;

          case "font-weight":
          case "font-style":
            if (value != "normal") {
              outputStyles[name] = value;
            }
            return;

          case "mso-element":
            // Remove track changes code
            if (/^(comment|comment-list)$/i.test(value)) {
              node.remove();
              return;
            }

            break;
        }

        if (name.indexOf('mso-comment') === 0) {
          node.remove();
          return;
        }

        // Never allow mso- prefixed names
        if (name.indexOf('mso-') === 0) {
          return;
        }

        // Output only valid styles
        if (editor.settings.paste_retain_style_properties == "all" || (validStyles && validStyles[name])) {
          outputStyles[name] = value;
        }
      });

      // Convert bold style to "b" element
      if (/(bold)/i.test(outputStyles["font-weight"])) {
        delete outputStyles["font-weight"];
        node.wrap(new Node("b", 1));
      }

      // Convert italic style to "i" element
      if (/(italic)/i.test(outputStyles["font-style"])) {
        delete outputStyles["font-style"];
        node.wrap(new Node("i", 1));
      }

      // Serialize the styles and see if there is something left to keep
      outputStyles = editor.dom.serializeStyle(outputStyles, node.name);
      if (outputStyles) {
        return outputStyles;
      }

      return null;
    }

    var filterWordContent = function (editor, content) {
      var retainStyleProperties, validStyles;

      retainStyleProperties = editor.settings.paste_retain_style_properties;
      if (retainStyleProperties) {
        validStyles = Tools.makeMap(retainStyleProperties.split(/[, ]/));
      }

      // Remove basic Word junk
      content = Utils.filter(content, [
        // Remove apple new line markers
        /<br class="?Apple-interchange-newline"?>/gi,

        // Remove google docs internal guid markers
        /<b[^>]+id="?docs-internal-[^>]*>/gi,

        // Word comments like conditional comments etc
        /<!--[\s\S]+?-->/gi,

        // Remove comments, scripts (e.g., msoShowComment), XML tag, VML content,
        // MS Office namespaced tags, and a few other tags
        /<(!|script[^>]*>.*?<\/script(?=[>\s])|\/?(\?xml(:\w+)?|img|meta|link|style|\w:\w+)(?=[\s\/>]))[^>]*>/gi,

        // Convert <s> into <strike> for line-though
        [/<(\/?)s>/gi, "<$1strike>"],

        // Replace nsbp entites to char since it's easier to handle
        [/&nbsp;/gi, "\u00a0"],

        // Convert <span style="mso-spacerun:yes">___</span> to string of alternating
        // breaking/non-breaking spaces of same length
        [/<span\s+style\s*=\s*"\s*mso-spacerun\s*:\s*yes\s*;?\s*"\s*>([\s\u00a0]*)<\/span>/gi,
          function (str, spaces) {
            return (spaces.length > 0) ?
              spaces.replace(/./, " ").slice(Math.floor(spaces.length / 2)).split("").join("\u00a0") : "";
          }
        ]
      ]);

      var validElements = editor.settings.paste_word_valid_elements;
      if (!validElements) {
        validElements = (
          '-strong/b,-em/i,-u,-span,-p,-ol,-ul,-li,-h1,-h2,-h3,-h4,-h5,-h6,' +
          '-p/div,-a[href|name],sub,sup,strike,br,del,table[width],tr,' +
          'td[colspan|rowspan|width],th[colspan|rowspan|width],thead,tfoot,tbody'
        );
      }

      // Setup strict schema
      var schema = new Schema({
        valid_elements: validElements,
        valid_children: '-li[p]'
      });

      // Add style/class attribute to all element rules since the user might have removed them from
      // paste_word_valid_elements config option and we need to check them for properties
      Tools.each(schema.elements, function (rule) {
        /*eslint dot-notation:0*/
        if (!rule.attributes["class"]) {
          rule.attributes["class"] = {};
          rule.attributesOrder.push("class");
        }

        if (!rule.attributes.style) {
          rule.attributes.style = {};
          rule.attributesOrder.push("style");
        }
      });

      // Parse HTML into DOM structure
      var domParser = new DomParser({}, schema);

      // Filter styles to remove "mso" specific styles and convert some of them
      domParser.addAttributeFilter('style', function (nodes) {
        var i = nodes.length, node;

        while (i--) {
          node = nodes[i];
          node.attr('style', filterStyles(editor, validStyles, node, node.attr('style')));

          // Remove pointess spans
          if (node.name == 'span' && node.parent && !node.attributes.length) {
            node.unwrap();
          }
        }
      });

      // Check the class attribute for comments or del items and remove those
      domParser.addAttributeFilter('class', function (nodes) {
        var i = nodes.length, node, className;

        while (i--) {
          node = nodes[i];

          className = node.attr('class');
          if (/^(MsoCommentReference|MsoCommentText|msoDel)$/i.test(className)) {
            node.remove();
          }

          node.attr('class', null);
        }
      });

      // Remove all del elements since we don't want the track changes code in the editor
      domParser.addNodeFilter('del', function (nodes) {
        var i = nodes.length;

        while (i--) {
          nodes[i].remove();
        }
      });

      // Keep some of the links and anchors
      domParser.addNodeFilter('a', function (nodes) {
        var i = nodes.length, node, href, name;

        while (i--) {
          node = nodes[i];
          href = node.attr('href');
          name = node.attr('name');

          if (href && href.indexOf('#_msocom_') != -1) {
            node.remove();
            continue;
          }

          if (href && href.indexOf('file://') === 0) {
            href = href.split('#')[1];
            if (href) {
              href = '#' + href;
            }
          }

          if (!href && !name) {
            node.unwrap();
          } else {
            // Remove all named anchors that aren't specific to TOC, Footnotes or Endnotes
            if (name && !/^_?(?:toc|edn|ftn)/i.test(name)) {
              node.unwrap();
              continue;
            }

            node.attr({
              href: href,
              name: name
            });
          }
        }
      });

      // Parse into DOM structure
      var rootNode = domParser.parse(content);

      // Process DOM
      if (editor.settings.paste_convert_word_fake_lists !== false) {
        convertFakeListsToProperLists(rootNode);
      }

      // Serialize DOM back to HTML
      content = new Serializer({
        validate: editor.settings.validate
      }, schema).serialize(rootNode);

      return content;
    };

    var preProcess = function (editor, content) {
      return editor.settings.paste_enable_default_filters === false ? content : filterWordContent(editor, content);
    };

    return {
      preProcess: preProcess,
      isWordContent: isWordContent
    };
  }
);

/**
 * ProcessFilters.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.paste.core.ProcessFilters',
  [
    'tinymce.plugins.paste.api.Events',
    'tinymce.plugins.paste.core.WordFilter'
  ],
  function (Events, WordFilter) {
    var processResult = function (content, cancelled) {
      return { content: content, cancelled: cancelled };
    };

    var postProcessFilter = function (editor, html, internal, isWordHtml) {
      var tempBody = editor.dom.create('div', { style: 'display:none' }, html);
      var postProcessArgs = Events.firePastePostProcess(editor, tempBody, internal, isWordHtml);
      return processResult(postProcessArgs.node.innerHTML, postProcessArgs.isDefaultPrevented());
    };

    var filterContent = function (editor, content, internal, isWordHtml) {
      var preProcessArgs = Events.firePastePreProcess(editor, content, internal, isWordHtml);

      if (editor.hasEventListeners('PastePostProcess') && !preProcessArgs.isDefaultPrevented()) {
        return postProcessFilter(editor, preProcessArgs.content, internal, isWordHtml);
      } else {
        return processResult(preProcessArgs.content, preProcessArgs.isDefaultPrevented());
      }
    };

    var process = function (editor, html, internal) {
      var isWordHtml = WordFilter.isWordContent(html);
      var content = isWordHtml ? WordFilter.preProcess(editor, html) : html;

      return filterContent(editor, content, internal, isWordHtml);
    };

    return {
      process: process
    };
  }
);

/**
 * SmartPaste.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * Tries to be smart depending on what the user pastes if it looks like an url
 * it will make a link out of the current selection. If it's an image url that looks
 * like an image it will check if it's an image and insert it as an image.
 *
 * @class tinymce.pasteplugin.SmartPaste
 * @private
 */
define(
  'tinymce.plugins.paste.core.SmartPaste',
  [
    'tinymce.core.util.Tools'
  ],
  function (Tools) {
    var isAbsoluteUrl = function (url) {
      return /^https?:\/\/[\w\?\-\/+=.&%@~#]+$/i.test(url);
    };

    var isImageUrl = function (url) {
      return isAbsoluteUrl(url) && /.(gif|jpe?g|png)$/.test(url);
    };

    var createImage = function (editor, url, pasteHtml) {
      editor.undoManager.extra(function () {
        pasteHtml(editor, url);
      }, function () {
        editor.insertContent('<img src="' + url + '">');
      });

      return true;
    };

    var createLink = function (editor, url, pasteHtml) {
      editor.undoManager.extra(function () {
        pasteHtml(editor, url);
      }, function () {
        editor.execCommand('mceInsertLink', false, url);
      });

      return true;
    };

    var linkSelection = function (editor, html, pasteHtml) {
      return editor.selection.isCollapsed() === false && isAbsoluteUrl(html) ? createLink(editor, html, pasteHtml) : false;
    };

    var insertImage = function (editor, html, pasteHtml) {
      return isImageUrl(html) ? createImage(editor, html, pasteHtml) : false;
    };

    var pasteHtml = function (editor, html) {
      editor.insertContent(html, {
        merge: editor.settings.paste_merge_formats !== false,
        paste: true
      });

      return true;
    };

    var smartInsertContent = function (editor, html) {
      Tools.each([
        linkSelection,
        insertImage,
        pasteHtml
      ], function (action) {
        return action(editor, html, pasteHtml) !== true;
      });
    };

    var insertContent = function (editor, html) {
      if (editor.settings.smart_paste === false) {
        pasteHtml(editor, html);
      } else {
        smartInsertContent(editor, html);
      }
    };

    return {
      isImageUrl: isImageUrl,
      isAbsoluteUrl: isAbsoluteUrl,
      insertContent: insertContent
    };
  }
);

/**
 * Clipboard.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * This class contains logic for getting HTML contents out of the clipboard.
 *
 * We need to make a lot of ugly hacks to get the contents out of the clipboard since
 * the W3C Clipboard API is broken in all browsers that have it: Gecko/WebKit/Blink.
 * We might rewrite this the way those API:s stabilize. Browsers doesn't handle pasting
 * from applications like Word the same way as it does when pasting into a contentEditable area
 * so we need to do lots of extra work to try to get to this clipboard data.
 *
 * Current implementation steps:
 *  1. On keydown with paste keys Ctrl+V or Shift+Insert create
 *     a paste bin element and move focus to that element.
 *  2. Wait for the browser to fire a "paste" event and get the contents out of the paste bin.
 *  3. Check if the paste was successful if true, process the HTML.
 *  (4). If the paste was unsuccessful use IE execCommand, Clipboard API, document.dataTransfer old WebKit API etc.
 *
 * @class tinymce.pasteplugin.Clipboard
 * @private
 */
define(
  'tinymce.plugins.paste.core.Clipboard',
  [
    'tinymce.core.dom.RangeUtils',
    'tinymce.core.Env',
    'tinymce.core.util.Delay',
    'tinymce.core.util.Tools',
    'tinymce.core.util.VK',
    'tinymce.plugins.paste.core.InternalHtml',
    'tinymce.plugins.paste.core.Newlines',
    'tinymce.plugins.paste.core.PasteBin',
    'tinymce.plugins.paste.core.ProcessFilters',
    'tinymce.plugins.paste.core.SmartPaste',
    'tinymce.plugins.paste.core.Utils'
  ],
  function (RangeUtils, Env, Delay, Tools, VK, InternalHtml, Newlines, PasteBin, ProcessFilters, SmartPaste, Utils) {
    return function (editor) {
      var self = this, keyboardPasteTimeStamp = 0, draggingInternally = false;
      var pasteBin = new PasteBin(editor);
      var keyboardPastePlainTextState;
      var mceInternalUrlPrefix = 'data:text/mce-internal,';
      var uniqueId = Utils.createIdGenerator("mceclip");

      /**
       * Pastes the specified HTML. This means that the HTML is filtered and then
       * inserted at the current selection in the editor. It will also fire paste events
       * for custom user filtering.
       *
       * @param {String} html HTML code to paste into the current selection.
       * @param {Boolean?} internalFlag Optional true/false flag if the contents is internal or external.
       */
      function pasteHtml(html, internalFlag) {
        var internal = internalFlag ? internalFlag : InternalHtml.isMarked(html);
        var args = ProcessFilters.process(editor, InternalHtml.unmark(html), internal);

        if (args.cancelled === false) {
          SmartPaste.insertContent(editor, args.content);
        }
      }

      /**
       * Pastes the specified text. This means that the plain text is processed
       * and converted into BR and P elements. It will fire paste events for custom filtering.
       *
       * @param {String} text Text to paste as the current selection location.
       */
      function pasteText(text) {
        text = editor.dom.encode(text).replace(/\r\n/g, '\n');
        text = Newlines.convert(text, editor.settings.forced_root_block, editor.settings.forced_root_block_attrs);

        pasteHtml(text, false);
      }


      /**
       * Gets various content types out of a datatransfer object.
       *
       * @param {DataTransfer} dataTransfer Event fired on paste.
       * @return {Object} Object with mime types and data for those mime types.
       */
      function getDataTransferItems(dataTransfer) {
        var items = {};

        if (dataTransfer) {
          // Use old WebKit/IE API
          if (dataTransfer.getData) {
            var legacyText = dataTransfer.getData('Text');
            if (legacyText && legacyText.length > 0) {
              if (legacyText.indexOf(mceInternalUrlPrefix) == -1) {
                items['text/plain'] = legacyText;
              }
            }
          }

          if (dataTransfer.types) {
            for (var i = 0; i < dataTransfer.types.length; i++) {
              var contentType = dataTransfer.types[i];
              try { // IE11 throws exception when contentType is Files (type is present but data cannot be retrieved via getData())
                items[contentType] = dataTransfer.getData(contentType);
              } catch (ex) {
                items[contentType] = ""; // useless in general, but for consistency across browsers
              }
            }
          }
        }

        return items;
      }

      /**
       * Gets various content types out of the Clipboard API. It will also get the
       * plain text using older IE and WebKit API:s.
       *
       * @param {ClipboardEvent} clipboardEvent Event fired on paste.
       * @return {Object} Object with mime types and data for those mime types.
       */
      function getClipboardContent(clipboardEvent) {
        var content = getDataTransferItems(clipboardEvent.clipboardData || editor.getDoc().dataTransfer);

        // Edge 15 has a broken HTML Clipboard API see https://developer.microsoft.com/en-us/microsoft-edge/platform/issues/11877517/
        return Utils.isMsEdge() ? Tools.extend(content, { 'text/html': '' }) : content;
      }

      function hasHtmlOrText(content) {
        return hasContentType(content, 'text/html') || hasContentType(content, 'text/plain');
      }

      function getBase64FromUri(uri) {
        var idx;

        idx = uri.indexOf(',');
        if (idx !== -1) {
          return uri.substr(idx + 1);
        }

        return null;
      }

      function isValidDataUriImage(settings, imgElm) {
        return settings.images_dataimg_filter ? settings.images_dataimg_filter(imgElm) : true;
      }

      function extractFilename(str) {
        var m = str.match(/([\s\S]+?)\.(?:jpeg|jpg|png|gif)$/i);
        return m ? editor.dom.encode(m[1]) : null;
      }

      function pasteImage(rng, reader, blob) {
        if (rng) {
          editor.selection.setRng(rng);
          rng = null;
        }

        var dataUri = reader.result;
        var base64 = getBase64FromUri(dataUri);
        var id = uniqueId();
        var name = editor.settings.images_reuse_filename && blob.name ? extractFilename(blob.name) : id;
        var img = new Image();

        img.src = dataUri;

        // TODO: Move the bulk of the cache logic to EditorUpload
        if (isValidDataUriImage(editor.settings, img)) {
          var blobCache = editor.editorUpload.blobCache;
          var blobInfo, existingBlobInfo;

          existingBlobInfo = blobCache.findFirst(function (cachedBlobInfo) {
            return cachedBlobInfo.base64() === base64;
          });

          if (!existingBlobInfo) {
            blobInfo = blobCache.create(id, blob, base64, name);
            blobCache.add(blobInfo);
          } else {
            blobInfo = existingBlobInfo;
          }

          pasteHtml('<img src="' + blobInfo.blobUri() + '">', false);
        } else {
          pasteHtml('<img src="' + dataUri + '">', false);
        }
      }

      /**
       * Checks if the clipboard contains image data if it does it will take that data
       * and convert it into a data url image and paste that image at the caret location.
       *
       * @param  {ClipboardEvent} e Paste/drop event object.
       * @param  {DOMRange} rng Rng object to move selection to.
       * @return {Boolean} true/false if the image data was found or not.
       */
      function pasteImageData(e, rng) {
        var dataTransfer = e.clipboardData || e.dataTransfer;

        function processItems(items) {
          var i, item, reader, hadImage = false;

          if (items) {
            for (i = 0; i < items.length; i++) {
              item = items[i];

              if (/^image\/(jpeg|png|gif|bmp)$/.test(item.type)) {
                var blob = item.getAsFile ? item.getAsFile() : item;

                reader = new FileReader();
                reader.onload = pasteImage.bind(null, rng, reader, blob);
                reader.readAsDataURL(blob);

                e.preventDefault();
                hadImage = true;
              }
            }
          }

          return hadImage;
        }

        if (editor.settings.paste_data_images && dataTransfer) {
          return processItems(dataTransfer.items) || processItems(dataTransfer.files);
        }
      }

      /**
       * Chrome on Android doesn't support proper clipboard access so we have no choice but to allow the browser default behavior.
       *
       * @param {Event} e Paste event object to check if it contains any data.
       * @return {Boolean} true/false if the clipboard is empty or not.
       */
      function isBrokenAndroidClipboardEvent(e) {
        var clipboardData = e.clipboardData;

        return navigator.userAgent.indexOf('Android') != -1 && clipboardData && clipboardData.items && clipboardData.items.length === 0;
      }

      function getCaretRangeFromEvent(e) {
        return RangeUtils.getCaretRangeFromPoint(e.clientX, e.clientY, editor.getDoc());
      }

      function hasContentType(clipboardContent, mimeType) {
        return mimeType in clipboardContent && clipboardContent[mimeType].length > 0;
      }

      function isKeyboardPasteEvent(e) {
        return (VK.metaKeyPressed(e) && e.keyCode == 86) || (e.shiftKey && e.keyCode == 45);
      }

      function registerEventHandlers() {
        editor.on('keydown', function (e) {
          function removePasteBinOnKeyUp(e) {
            // Ctrl+V or Shift+Insert
            if (isKeyboardPasteEvent(e) && !e.isDefaultPrevented()) {
              pasteBin.remove();
            }
          }

          // Ctrl+V or Shift+Insert
          if (isKeyboardPasteEvent(e) && !e.isDefaultPrevented()) {
            keyboardPastePlainTextState = e.shiftKey && e.keyCode == 86;

            // Edge case on Safari on Mac where it doesn't handle Cmd+Shift+V correctly
            // it fires the keydown but no paste or keyup so we are left with a paste bin
            if (keyboardPastePlainTextState && Env.webkit && navigator.userAgent.indexOf('Version/') != -1) {
              return;
            }

            // Prevent undoManager keydown handler from making an undo level with the pastebin in it
            e.stopImmediatePropagation();

            keyboardPasteTimeStamp = new Date().getTime();

            // IE doesn't support Ctrl+Shift+V and it doesn't even produce a paste event
            // so lets fake a paste event and let IE use the execCommand/dataTransfer methods
            if (Env.ie && keyboardPastePlainTextState) {
              e.preventDefault();
              editor.fire('paste', { ieFake: true });
              return;
            }

            pasteBin.remove();
            pasteBin.create();

            // Remove pastebin if we get a keyup and no paste event
            // For example pasting a file in IE 11 will not produce a paste event
            editor.once('keyup', removePasteBinOnKeyUp);
            editor.once('paste', function () {
              editor.off('keyup', removePasteBinOnKeyUp);
            });
          }
        });

        function insertClipboardContent(clipboardContent, isKeyBoardPaste, plainTextMode, internal) {
          var content, isPlainTextHtml;

          // Grab HTML from Clipboard API or paste bin as a fallback
          if (hasContentType(clipboardContent, 'text/html')) {
            content = clipboardContent['text/html'];
          } else {
            content = pasteBin.getHtml();
            internal = internal ? internal : InternalHtml.isMarked(content);

            // If paste bin is empty try using plain text mode
            // since that is better than nothing right
            if (pasteBin.isDefaultContent(content)) {
              plainTextMode = true;
            }
          }

          content = Utils.trimHtml(content);

          pasteBin.remove();

          isPlainTextHtml = (internal === false && Newlines.isPlainText(content));

          // If we got nothing from clipboard API and pastebin or the content is a plain text (with only
          // some BRs, Ps or DIVs as newlines) then we fallback to plain/text
          if (!content.length || isPlainTextHtml) {
            plainTextMode = true;
          }

          // Grab plain text from Clipboard API or convert existing HTML to plain text
          if (plainTextMode) {
            // Use plain text contents from Clipboard API unless the HTML contains paragraphs then
            // we should convert the HTML to plain text since works better when pasting HTML/Word contents as plain text
            if (hasContentType(clipboardContent, 'text/plain') && isPlainTextHtml) {
              content = clipboardContent['text/plain'];
            } else {
              content = Utils.innerText(content);
            }
          }

          // If the content is the paste bin default HTML then it was
          // impossible to get the cliboard data out.
          if (pasteBin.isDefaultContent(content)) {
            if (!isKeyBoardPaste) {
              editor.windowManager.alert('Please use Ctrl+V/Cmd+V keyboard shortcuts to paste contents.');
            }

            return;
          }

          if (plainTextMode) {
            pasteText(content);
          } else {
            pasteHtml(content, internal);
          }
        }

        var getLastRng = function () {
          return pasteBin.getLastRng() || editor.selection.getRng();
        };

        editor.on('paste', function (e) {
          // Getting content from the Clipboard can take some time
          var clipboardTimer = new Date().getTime();
          var clipboardContent = getClipboardContent(e);
          var clipboardDelay = new Date().getTime() - clipboardTimer;

          var isKeyBoardPaste = (new Date().getTime() - keyboardPasteTimeStamp - clipboardDelay) < 1000;
          var plainTextMode = self.pasteFormat == "text" || keyboardPastePlainTextState;
          var internal = hasContentType(clipboardContent, InternalHtml.internalHtmlMime());

          keyboardPastePlainTextState = false;

          if (e.isDefaultPrevented() || isBrokenAndroidClipboardEvent(e)) {
            pasteBin.remove();
            return;
          }

          if (!hasHtmlOrText(clipboardContent) && pasteImageData(e, getLastRng())) {
            pasteBin.remove();
            return;
          }

          // Not a keyboard paste prevent default paste and try to grab the clipboard contents using different APIs
          if (!isKeyBoardPaste) {
            e.preventDefault();
          }

          // Try IE only method if paste isn't a keyboard paste
          if (Env.ie && (!isKeyBoardPaste || e.ieFake) && !hasContentType(clipboardContent, 'text/html')) {
            pasteBin.create();

            editor.dom.bind(pasteBin.getEl(), 'paste', function (e) {
              e.stopPropagation();
            });

            editor.getDoc().execCommand('Paste', false, null);
            clipboardContent["text/html"] = pasteBin.getHtml();
          }

          // If clipboard API has HTML then use that directly
          if (hasContentType(clipboardContent, 'text/html')) {
            e.preventDefault();

            // if clipboard lacks internal mime type, inspect html for internal markings
            if (!internal) {
              internal = InternalHtml.isMarked(clipboardContent['text/html']);
            }

            insertClipboardContent(clipboardContent, isKeyBoardPaste, plainTextMode, internal);
          } else {
            Delay.setEditorTimeout(editor, function () {
              insertClipboardContent(clipboardContent, isKeyBoardPaste, plainTextMode, internal);
            }, 0);
          }
        });

        editor.on('dragstart dragend', function (e) {
          draggingInternally = e.type == 'dragstart';
        });

        function isPlainTextFileUrl(content) {
          var plainTextContent = content['text/plain'];
          return plainTextContent ? plainTextContent.indexOf('file://') === 0 : false;
        }

        editor.on('drop', function (e) {
          var dropContent, rng;

          rng = getCaretRangeFromEvent(e);

          if (e.isDefaultPrevented() || draggingInternally) {
            return;
          }

          dropContent = getDataTransferItems(e.dataTransfer);
          var internal = hasContentType(dropContent, InternalHtml.internalHtmlMime());

          if ((!hasHtmlOrText(dropContent) || isPlainTextFileUrl(dropContent)) && pasteImageData(e, rng)) {
            return;
          }

          if (rng && editor.settings.paste_filter_drop !== false) {
            var content = dropContent['mce-internal'] || dropContent['text/html'] || dropContent['text/plain'];

            if (content) {
              e.preventDefault();

              // FF 45 doesn't paint a caret when dragging in text in due to focus call by execCommand
              Delay.setEditorTimeout(editor, function () {
                editor.undoManager.transact(function () {
                  if (dropContent['mce-internal']) {
                    editor.execCommand('Delete');
                  }

                  editor.selection.setRng(rng);

                  content = Utils.trimHtml(content);

                  if (!dropContent['text/html']) {
                    pasteText(content);
                  } else {
                    pasteHtml(content, internal);
                  }
                });
              });
            }
          }
        });

        editor.on('dragover dragend', function (e) {
          if (editor.settings.paste_data_images) {
            e.preventDefault();
          }
        });
      }

      self.pasteHtml = pasteHtml;
      self.pasteText = pasteText;
      self.pasteImageData = pasteImageData;

      editor.on('preInit', function () {
        registerEventHandlers();

        // Remove all data images from paste for example from Gecko
        // except internal images like video elements
        editor.parser.addNodeFilter('img', function (nodes, name, args) {
          function isPasteInsert(args) {
            return args.data && args.data.paste === true;
          }

          function remove(node) {
            if (!node.attr('data-mce-object') && src !== Env.transparentSrc) {
              node.remove();
            }
          }

          function isWebKitFakeUrl(src) {
            return src.indexOf("webkit-fake-url") === 0;
          }

          function isDataUri(src) {
            return src.indexOf("data:") === 0;
          }

          if (!editor.settings.paste_data_images && isPasteInsert(args)) {
            var i = nodes.length;

            while (i--) {
              var src = nodes[i].attributes.map.src;

              if (!src) {
                continue;
              }

              // Safari on Mac produces webkit-fake-url see: https://bugs.webkit.org/show_bug.cgi?id=49141
              if (isWebKitFakeUrl(src)) {
                remove(nodes[i]);
              } else if (!editor.settings.allow_html_data_urls && isDataUri(src)) {
                remove(nodes[i]);
              }
            }
          }
        });
      });
    };
  }
);

/**
 * CutCopy.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.paste.core.CutCopy',
  [
    'tinymce.core.Env',
    'tinymce.plugins.paste.core.InternalHtml',
    'tinymce.plugins.paste.core.Utils'
  ],
  function (Env, InternalHtml, Utils) {
    var noop = function () {
    };

    var hasWorkingClipboardApi = function (clipboardData) {
      // iOS supports the clipboardData API but it doesn't do anything for cut operations
      // Edge 15 has a broken HTML Clipboard API see https://developer.microsoft.com/en-us/microsoft-edge/platform/issues/11780845/
      return Env.iOS === false && clipboardData !== undefined && typeof clipboardData.setData === 'function' && Utils.isMsEdge() !== true;
    };

    var setHtml5Clipboard = function (clipboardData, html, text) {
      if (hasWorkingClipboardApi(clipboardData)) {
        try {
          clipboardData.clearData();
          clipboardData.setData('text/html', html);
          clipboardData.setData('text/plain', text);
          clipboardData.setData(InternalHtml.internalHtmlMime(), html);
          return true;
        } catch (e) {
          return false;
        }
      } else {
        return false;
      }
    };

    var setClipboardData = function (evt, data, fallback, done) {
      if (setHtml5Clipboard(evt.clipboardData, data.html, data.text)) {
        evt.preventDefault();
        done();
      } else {
        fallback(data.html, done);
      }
    };

    var fallback = function (editor) {
      return function (html, done) {
        var markedHtml = InternalHtml.mark(html);
        var outer = editor.dom.create('div', {
          contenteditable: "false",
          "data-mce-bogus": "all"
        });
        var inner = editor.dom.create('div', { contenteditable: "true" }, markedHtml);
        editor.dom.setStyles(outer, {
          position: 'fixed',
          left: '-3000px',
          width: '1000px',
          overflow: 'hidden'
        });
        outer.appendChild(inner);
        editor.dom.add(editor.getBody(), outer);

        var range = editor.selection.getRng();
        inner.focus();

        var offscreenRange = editor.dom.createRng();
        offscreenRange.selectNodeContents(inner);
        editor.selection.setRng(offscreenRange);

        setTimeout(function () {
          outer.parentNode.removeChild(outer);
          editor.selection.setRng(range);
          done();
        }, 0);
      };
    };

    var getData = function (editor) {
      return {
        html: editor.selection.getContent({ contextual: true }),
        text: editor.selection.getContent({ format: 'text' })
      };
    };

    var cut = function (editor) {
      return function (evt) {
        if (editor.selection.isCollapsed() === false) {
          setClipboardData(evt, getData(editor), fallback(editor), function () {
            // Chrome fails to execCommand from another execCommand with this message:
            // "We don't execute document.execCommand() this time, because it is called recursively.""
            setTimeout(function () { // detach
              editor.execCommand('Delete');
            }, 0);
          });
        }
      };
    };

    var copy = function (editor) {
      return function (evt) {
        if (editor.selection.isCollapsed() === false) {
          setClipboardData(evt, getData(editor), fallback(editor), noop);
        }
      };
    };

    var register = function (editor) {
      editor.on('cut', cut(editor));
      editor.on('copy', copy(editor));
    };

    return {
      register: register
    };
  }
);
/**
 * Quirks.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * This class contains various fixes for browsers. These issues can not be feature
 * detected since we have no direct control over the clipboard. However we might be able
 * to remove some of these fixes once the browsers gets updated/fixed.
 *
 * @class tinymce.pasteplugin.Quirks
 * @private
 */
define(
  'tinymce.plugins.paste.core.Quirks',
  [
    'tinymce.core.Env',
    'tinymce.core.util.Tools',
    'tinymce.plugins.paste.core.WordFilter',
    'tinymce.plugins.paste.core.Utils'
  ],
  function (Env, Tools, WordFilter, Utils) {
    function addPreProcessFilter(editor, filterFunc) {
      editor.on('PastePreProcess', function (e) {
        e.content = filterFunc(editor, e.content, e.internal, e.wordContent);
      });
    }

    function addPostProcessFilter(editor, filterFunc) {
      editor.on('PastePostProcess', function (e) {
        filterFunc(editor, e.node);
      });
    }

    /**
     * Removes BR elements after block elements. IE9 has a nasty bug where it puts a BR element after each
     * block element when pasting from word. This removes those elements.
     *
     * This:
     *  <p>a</p><br><p>b</p>
     *
     * Becomes:
     *  <p>a</p><p>b</p>
     */
    function removeExplorerBrElementsAfterBlocks(editor, html) {
      // Only filter word specific content
      if (!WordFilter.isWordContent(html)) {
        return html;
      }

      // Produce block regexp based on the block elements in schema
      var blockElements = [];

      Tools.each(editor.schema.getBlockElements(), function (block, blockName) {
        blockElements.push(blockName);
      });

      var explorerBlocksRegExp = new RegExp(
        '(?:<br>&nbsp;[\\s\\r\\n]+|<br>)*(<\\/?(' + blockElements.join('|') + ')[^>]*>)(?:<br>&nbsp;[\\s\\r\\n]+|<br>)*',
        'g'
      );

      // Remove BR:s from: <BLOCK>X</BLOCK><BR>
      html = Utils.filter(html, [
        [explorerBlocksRegExp, '$1']
      ]);

      // IE9 also adds an extra BR element for each soft-linefeed and it also adds a BR for each word wrap break
      html = Utils.filter(html, [
        [/<br><br>/g, '<BR><BR>'], // Replace multiple BR elements with uppercase BR to keep them intact
        [/<br>/g, ' '],            // Replace single br elements with space since they are word wrap BR:s
        [/<BR><BR>/g, '<br>']      // Replace back the double brs but into a single BR
      ]);

      return html;
    }

    /**
     * WebKit has a nasty bug where the all computed styles gets added to style attributes when copy/pasting contents.
     * This fix solves that by simply removing the whole style attribute.
     *
     * The paste_webkit_styles option can be set to specify what to keep:
     *  paste_webkit_styles: "none" // Keep no styles
     *  paste_webkit_styles: "all", // Keep all of them
     *  paste_webkit_styles: "font-weight color" // Keep specific ones
     */
    function removeWebKitStyles(editor, content, internal, isWordHtml) {
      // WordFilter has already processed styles at this point and internal doesn't need any processing
      if (isWordHtml || internal) {
        return content;
      }

      // Filter away styles that isn't matching the target node
      var webKitStyles = editor.settings.paste_webkit_styles;

      if (editor.settings.paste_remove_styles_if_webkit === false || webKitStyles == "all") {
        return content;
      }

      if (webKitStyles) {
        webKitStyles = webKitStyles.split(/[, ]/);
      }

      // Keep specific styles that doesn't match the current node computed style
      if (webKitStyles) {
        var dom = editor.dom, node = editor.selection.getNode();

        content = content.replace(/(<[^>]+) style="([^"]*)"([^>]*>)/gi, function (all, before, value, after) {
          var inputStyles = dom.parseStyle(dom.decode(value), 'span');
          var outputStyles = {};

          if (webKitStyles === "none") {
            return before + after;
          }

          for (var i = 0; i < webKitStyles.length; i++) {
            var inputValue = inputStyles[webKitStyles[i]], currentValue = dom.getStyle(node, webKitStyles[i], true);

            if (/color/.test(webKitStyles[i])) {
              inputValue = dom.toHex(inputValue);
              currentValue = dom.toHex(currentValue);
            }

            if (currentValue != inputValue) {
              outputStyles[webKitStyles[i]] = inputValue;
            }
          }

          outputStyles = dom.serializeStyle(outputStyles, 'span');
          if (outputStyles) {
            return before + ' style="' + outputStyles + '"' + after;
          }

          return before + after;
        });
      } else {
        // Remove all external styles
        content = content.replace(/(<[^>]+) style="([^"]*)"([^>]*>)/gi, '$1$3');
      }

      // Keep internal styles
      content = content.replace(/(<[^>]+) data-mce-style="([^"]+)"([^>]*>)/gi, function (all, before, value, after) {
        return before + ' style="' + value + '"' + after;
      });

      return content;
    }

    function removeUnderlineAndFontInAnchor(editor, root) {
      editor.$('a', root).find('font,u').each(function (i, node) {
        editor.dom.remove(node, true);
      });
    }

    var setup = function (editor) {
      if (Env.webkit) {
        addPreProcessFilter(editor, removeWebKitStyles);
      }

      if (Env.ie) {
        addPreProcessFilter(editor, removeExplorerBrElementsAfterBlocks);
        addPostProcessFilter(editor, removeUnderlineAndFontInAnchor);
      }
    };

    return {
      setup: setup
    };
  }
);
/**
 * Plugin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * This class contains all core logic for the paste plugin.
 *
 * @class tinymce.paste.Plugin
 * @private
 */
define(
  'tinymce.plugins.paste.Plugin',
  [
    'tinymce.core.PluginManager',
    'tinymce.plugins.paste.api.Events',
    'tinymce.plugins.paste.core.Clipboard',
    'tinymce.plugins.paste.core.CutCopy',
    'tinymce.plugins.paste.core.Quirks'
  ],
  function (PluginManager, Events, Clipboard, CutCopy, Quirks) {
    var userIsInformed;

    PluginManager.add('paste', function (editor) {
      var self = this, clipboard, settings = editor.settings;

      function isUserInformedAboutPlainText() {
        return userIsInformed || editor.settings.paste_plaintext_inform === false;
      }

      function togglePlainTextPaste() {
        if (clipboard.pasteFormat == "text") {
          clipboard.pasteFormat = "html";
          Events.firePastePlainTextToggle(editor, false);
        } else {
          clipboard.pasteFormat = "text";
          Events.firePastePlainTextToggle(editor, true);

          if (!isUserInformedAboutPlainText()) {
            var message = editor.translate('Paste is now in plain text mode. Contents will now ' +
              'be pasted as plain text until you toggle this option off.');

            editor.notificationManager.open({
              text: message,
              type: 'info'
            });

            userIsInformed = true;
          }
        }

        editor.focus();
      }

      function stateChange() {
        var self = this;

        self.active(clipboard.pasteFormat === 'text');

        editor.on('PastePlainTextToggle', function (e) {
          self.active(e.state);
        });
      }

      // draw back if power version is requested and registered
      if (/(^|[ ,])powerpaste([, ]|$)/.test(settings.plugins) && PluginManager.get('powerpaste')) {
        /*eslint no-console:0 */
        if (typeof console !== "undefined" && console.log) {
          console.log("PowerPaste is incompatible with Paste plugin! Remove 'paste' from the 'plugins' option.");
        }
        return;
      }

      self.clipboard = clipboard = new Clipboard(editor);
      self.quirks = Quirks.setup(editor);

      if (editor.settings.paste_as_text) {
        self.clipboard.pasteFormat = "text";
      }

      if (settings.paste_preprocess) {
        editor.on('PastePreProcess', function (e) {
          settings.paste_preprocess.call(self, self, e);
        });
      }

      if (settings.paste_postprocess) {
        editor.on('PastePostProcess', function (e) {
          settings.paste_postprocess.call(self, self, e);
        });
      }

      editor.addCommand('mceInsertClipboardContent', function (ui, value) {
        if (value.content) {
          self.clipboard.pasteHtml(value.content, value.internal);
        }

        if (value.text) {
          self.clipboard.pasteText(value.text);
        }
      });

      // Block all drag/drop events
      if (editor.settings.paste_block_drop) {
        editor.on('dragend dragover draggesture dragdrop drop drag', function (e) {
          e.preventDefault();
          e.stopPropagation();
        });
      }

      // Prevent users from dropping data images on Gecko
      if (!editor.settings.paste_data_images) {
        editor.on('drop', function (e) {
          var dataTransfer = e.dataTransfer;

          if (dataTransfer && dataTransfer.files && dataTransfer.files.length > 0) {
            e.preventDefault();
          }
        });
      }

      editor.addCommand('mceTogglePlainTextPaste', togglePlainTextPaste);

      editor.addButton('pastetext', {
        icon: 'pastetext',
        tooltip: 'Paste as text',
        onclick: togglePlainTextPaste,
        onPostRender: stateChange
      });

      editor.addMenuItem('pastetext', {
        text: 'Paste as text',
        selectable: true,
        active: clipboard.pasteFormat,
        onclick: togglePlainTextPaste,
        onPostRender: stateChange
      });

      CutCopy.register(editor);
    });

    return function () { };
  }
);
dem('tinymce.plugins.paste.Plugin')();
})();
