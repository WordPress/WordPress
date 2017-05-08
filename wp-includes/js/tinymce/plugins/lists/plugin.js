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
["tinymce.plugins.lists.Plugin","tinymce.core.PluginManager","tinymce.core.util.Tools","tinymce.core.util.VK","tinymce.plugins.lists.actions.Indent","tinymce.plugins.lists.actions.Outdent","tinymce.plugins.lists.actions.ToggleList","tinymce.plugins.lists.core.Delete","tinymce.plugins.lists.core.NodeType","global!tinymce.util.Tools.resolve","tinymce.core.dom.DOMUtils","tinymce.plugins.lists.core.Bookmark","tinymce.plugins.lists.core.Selection","tinymce.plugins.lists.core.NormalizeLists","tinymce.plugins.lists.core.SplitList","tinymce.plugins.lists.core.TextBlock","tinymce.core.dom.BookmarkManager","tinymce.core.dom.RangeUtils","tinymce.core.dom.TreeWalker","tinymce.plugins.lists.core.Range","tinymce.core.Env"]
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
 * ResolveGlobal.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.core.dom.DOMUtils',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.dom.DOMUtils');
  }
);

/**
 * NodeType.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.lists.core.NodeType',
  [
  ],
  function () {
    var isTextNode = function (node) {
      return node && node.nodeType === 3;
    };

    var isListNode = function (node) {
      return node && (/^(OL|UL|DL)$/).test(node.nodeName);
    };

    var isListItemNode = function (node) {
      return node && /^(LI|DT|DD)$/.test(node.nodeName);
    };

    var isBr = function (node) {
      return node && node.nodeName === 'BR';
    };

    var isFirstChild = function (node) {
      return node.parentNode.firstChild === node;
    };

    var isLastChild = function (node) {
      return node.parentNode.lastChild === node;
    };

    var isTextBlock = function (editor, node) {
      return node && !!editor.schema.getTextBlockElements()[node.nodeName];
    };

    var isBogusBr = function (dom, node) {
      if (!isBr(node)) {
        return false;
      }

      if (dom.isBlock(node.nextSibling) && !isBr(node.previousSibling)) {
        return true;
      }

      return false;
    };

    var isEmpty = function (dom, elm, keepBookmarks) {
      var empty = dom.isEmpty(elm);

      if (keepBookmarks && dom.select('span[data-mce-type=bookmark]', elm).length > 0) {
        return false;
      }

      return empty;
    };

    var isChildOfBody = function (dom, elm) {
      return dom.isChildOf(elm, dom.getRoot());
    };

    return {
      isTextNode: isTextNode,
      isListNode: isListNode,
      isListItemNode: isListItemNode,
      isBr: isBr,
      isFirstChild: isFirstChild,
      isLastChild: isLastChild,
      isTextBlock: isTextBlock,
      isBogusBr: isBogusBr,
      isEmpty: isEmpty,
      isChildOfBody: isChildOfBody
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
 * Range.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.lists.core.Range',
  [
    'tinymce.core.dom.RangeUtils',
    'tinymce.plugins.lists.core.NodeType'
  ],
  function (RangeUtils, NodeType) {
    var getNormalizedEndPoint = function (container, offset) {
      var node = RangeUtils.getNode(container, offset);

      if (NodeType.isListItemNode(container) && NodeType.isTextNode(node)) {
        var textNodeOffset = offset >= container.childNodes.length ? node.data.length : 0;
        return { container: node, offset: textNodeOffset };
      }

      return { container: container, offset: offset };
    };

    var normalizeRange = function (rng) {
      var outRng = rng.cloneRange();

      var rangeStart = getNormalizedEndPoint(rng.startContainer, rng.startOffset);
      outRng.setStart(rangeStart.container, rangeStart.offset);

      var rangeEnd = getNormalizedEndPoint(rng.endContainer, rng.endOffset);
      outRng.setEnd(rangeEnd.container, rangeEnd.offset);

      return outRng;
    };

    return {
      getNormalizedEndPoint: getNormalizedEndPoint,
      normalizeRange: normalizeRange
    };
  }
);


/**
 * Bookmark.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.lists.core.Bookmark',
  [
    'tinymce.core.dom.DOMUtils',
    'tinymce.plugins.lists.core.NodeType',
    'tinymce.plugins.lists.core.Range'
  ],
  function (DOMUtils, NodeType, Range) {
    var DOM = DOMUtils.DOM;

    /**
     * Returns a range bookmark. This will convert indexed bookmarks into temporary span elements with
     * index 0 so that they can be restored properly after the DOM has been modified. Text bookmarks will not have spans
     * added to them since they can be restored after a dom operation.
     *
     * So this: <p><b>|</b><b>|</b></p>
     * becomes: <p><b><span data-mce-type="bookmark">|</span></b><b data-mce-type="bookmark">|</span></b></p>
     *
     * @param  {DOMRange} rng DOM Range to get bookmark on.
     * @return {Object} Bookmark object.
     */
    var createBookmark = function (rng) {
      var bookmark = {};

      var setupEndPoint = function (start) {
        var offsetNode, container, offset;

        container = rng[start ? 'startContainer' : 'endContainer'];
        offset = rng[start ? 'startOffset' : 'endOffset'];

        if (container.nodeType === 1) {
          offsetNode = DOM.create('span', { 'data-mce-type': 'bookmark' });

          if (container.hasChildNodes()) {
            offset = Math.min(offset, container.childNodes.length - 1);

            if (start) {
              container.insertBefore(offsetNode, container.childNodes[offset]);
            } else {
              DOM.insertAfter(offsetNode, container.childNodes[offset]);
            }
          } else {
            container.appendChild(offsetNode);
          }

          container = offsetNode;
          offset = 0;
        }

        bookmark[start ? 'startContainer' : 'endContainer'] = container;
        bookmark[start ? 'startOffset' : 'endOffset'] = offset;
      };

      setupEndPoint(true);

      if (!rng.collapsed) {
        setupEndPoint();
      }

      return bookmark;
    };

    var resolveBookmark = function (bookmark) {
      function restoreEndPoint(start) {
        var container, offset, node;

        var nodeIndex = function (container) {
          var node = container.parentNode.firstChild, idx = 0;

          while (node) {
            if (node === container) {
              return idx;
            }

            // Skip data-mce-type=bookmark nodes
            if (node.nodeType !== 1 || node.getAttribute('data-mce-type') !== 'bookmark') {
              idx++;
            }

            node = node.nextSibling;
          }

          return -1;
        };

        container = node = bookmark[start ? 'startContainer' : 'endContainer'];
        offset = bookmark[start ? 'startOffset' : 'endOffset'];

        if (!container) {
          return;
        }

        if (container.nodeType === 1) {
          offset = nodeIndex(container);
          container = container.parentNode;
          DOM.remove(node);
        }

        bookmark[start ? 'startContainer' : 'endContainer'] = container;
        bookmark[start ? 'startOffset' : 'endOffset'] = offset;
      }

      restoreEndPoint(true);
      restoreEndPoint();

      var rng = DOM.createRng();

      rng.setStart(bookmark.startContainer, bookmark.startOffset);

      if (bookmark.endContainer) {
        rng.setEnd(bookmark.endContainer, bookmark.endOffset);
      }

      return Range.normalizeRange(rng);
    };

    return {
      createBookmark: createBookmark,
      resolveBookmark: resolveBookmark
    };
  }
);


/**
 * Selection.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.lists.core.Selection',
  [
    'tinymce.core.util.Tools',
    'tinymce.plugins.lists.core.NodeType'
  ],
  function (Tools, NodeType) {
    var getSelectedListItems = function (editor) {
      return Tools.grep(editor.selection.getSelectedBlocks(), function (block) {
        return NodeType.isListItemNode(block);
      });
    };

    return {
      getSelectedListItems: getSelectedListItems
    };
  }
);


/**
 * Indent.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.lists.actions.Indent',
  [
    'tinymce.core.dom.DOMUtils',
    'tinymce.plugins.lists.core.Bookmark',
    'tinymce.plugins.lists.core.NodeType',
    'tinymce.plugins.lists.core.Selection'
  ],
  function (DOMUtils, Bookmark, NodeType, Selection) {
    var DOM = DOMUtils.DOM;

    var mergeLists = function (from, to) {
      var node;

      if (NodeType.isListNode(from)) {
        while ((node = from.firstChild)) {
          to.appendChild(node);
        }

        DOM.remove(from);
      }
    };

    var indent = function (li) {
      var sibling, newList, listStyle;

      if (li.nodeName === 'DT') {
        DOM.rename(li, 'DD');
        return true;
      }

      sibling = li.previousSibling;

      if (sibling && NodeType.isListNode(sibling)) {
        sibling.appendChild(li);
        return true;
      }

      if (sibling && sibling.nodeName === 'LI' && NodeType.isListNode(sibling.lastChild)) {
        sibling.lastChild.appendChild(li);
        mergeLists(li.lastChild, sibling.lastChild);
        return true;
      }

      sibling = li.nextSibling;

      if (sibling && NodeType.isListNode(sibling)) {
        sibling.insertBefore(li, sibling.firstChild);
        return true;
      }

      /*if (sibling && sibling.nodeName === 'LI' && isListNode(li.lastChild)) {
        return false;
      }*/

      sibling = li.previousSibling;
      if (sibling && sibling.nodeName === 'LI') {
        newList = DOM.create(li.parentNode.nodeName);
        listStyle = DOM.getStyle(li.parentNode, 'listStyleType');
        if (listStyle) {
          DOM.setStyle(newList, 'listStyleType', listStyle);
        }
        sibling.appendChild(newList);
        newList.appendChild(li);
        mergeLists(li.lastChild, newList);
        return true;
      }

      return false;
    };

    var indentSelection = function (editor) {
      var listElements = Selection.getSelectedListItems(editor);

      if (listElements.length) {
        var bookmark = Bookmark.createBookmark(editor.selection.getRng(true));

        for (var i = 0; i < listElements.length; i++) {
          if (!indent(listElements[i]) && i === 0) {
            break;
          }
        }

        editor.selection.setRng(Bookmark.resolveBookmark(bookmark));
        editor.nodeChanged();

        return true;
      }
    };

    return {
      indentSelection: indentSelection
    };
  }
);


/**
 * NormalizeLists.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.lists.core.NormalizeLists',
  [
    'tinymce.core.dom.DOMUtils',
    'tinymce.core.util.Tools',
    'tinymce.plugins.lists.core.NodeType'
  ],
  function (DOMUtils, Tools, NodeType) {
    var DOM = DOMUtils.DOM;

    var normalizeList = function (dom, ul) {
      var sibling, parentNode = ul.parentNode;

      // Move UL/OL to previous LI if it's the only child of a LI
      if (parentNode.nodeName === 'LI' && parentNode.firstChild === ul) {
        sibling = parentNode.previousSibling;
        if (sibling && sibling.nodeName === 'LI') {
          sibling.appendChild(ul);

          if (NodeType.isEmpty(dom, parentNode)) {
            DOM.remove(parentNode);
          }
        } else {
          DOM.setStyle(parentNode, 'listStyleType', 'none');
        }
      }

      // Append OL/UL to previous LI if it's in a parent OL/UL i.e. old HTML4
      if (NodeType.isListNode(parentNode)) {
        sibling = parentNode.previousSibling;
        if (sibling && sibling.nodeName === 'LI') {
          sibling.appendChild(ul);
        }
      }
    };

    var normalizeLists = function (dom, element) {
      Tools.each(Tools.grep(dom.select('ol,ul', element)), function (ul) {
        normalizeList(dom, ul);
      });
    };

    return {
      normalizeList: normalizeList,
      normalizeLists: normalizeLists
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
  'tinymce.core.Env',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.Env');
  }
);

/**
 * TextBlock.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.lists.core.TextBlock',
  [
    'tinymce.core.dom.DOMUtils',
    'tinymce.core.Env'
  ],
  function (DOMUtils, Env) {
    var DOM = DOMUtils.DOM;

    var createNewTextBlock = function (editor, contentNode, blockName) {
      var node, textBlock, fragment = DOM.createFragment(), hasContentNode;
      var blockElements = editor.schema.getBlockElements();

      if (editor.settings.forced_root_block) {
        blockName = blockName || editor.settings.forced_root_block;
      }

      if (blockName) {
        textBlock = DOM.create(blockName);

        if (textBlock.tagName === editor.settings.forced_root_block) {
          DOM.setAttribs(textBlock, editor.settings.forced_root_block_attrs);
        }

        fragment.appendChild(textBlock);
      }

      if (contentNode) {
        while ((node = contentNode.firstChild)) {
          var nodeName = node.nodeName;

          if (!hasContentNode && (nodeName !== 'SPAN' || node.getAttribute('data-mce-type') !== 'bookmark')) {
            hasContentNode = true;
          }

          if (blockElements[nodeName]) {
            fragment.appendChild(node);
            textBlock = null;
          } else {
            if (blockName) {
              if (!textBlock) {
                textBlock = DOM.create(blockName);
                fragment.appendChild(textBlock);
              }

              textBlock.appendChild(node);
            } else {
              fragment.appendChild(node);
            }
          }
        }
      }

      if (!editor.settings.forced_root_block) {
        fragment.appendChild(DOM.create('br'));
      } else {
        // BR is needed in empty blocks on non IE browsers
        if (!hasContentNode && (!Env.ie || Env.ie > 10)) {
          textBlock.appendChild(DOM.create('br', { 'data-mce-bogus': '1' }));
        }
      }

      return fragment;
    };

    return {
      createNewTextBlock: createNewTextBlock
    };
  }
);

/**
 * SplitList.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.lists.core.SplitList',
  [
    'tinymce.core.dom.DOMUtils',
    'tinymce.plugins.lists.core.NodeType',
    'tinymce.plugins.lists.core.TextBlock',
    'tinymce.core.util.Tools'
  ],
  function (DOMUtils, NodeType, TextBlock, Tools) {
    var DOM = DOMUtils.DOM;

    var splitList = function (editor, ul, li, newBlock) {
      var tmpRng, fragment, bookmarks, node;

      var removeAndKeepBookmarks = function (targetNode) {
        Tools.each(bookmarks, function (node) {
          targetNode.parentNode.insertBefore(node, li.parentNode);
        });

        DOM.remove(targetNode);
      };

      bookmarks = DOM.select('span[data-mce-type="bookmark"]', ul);
      newBlock = newBlock || TextBlock.createNewTextBlock(editor, li);
      tmpRng = DOM.createRng();
      tmpRng.setStartAfter(li);
      tmpRng.setEndAfter(ul);
      fragment = tmpRng.extractContents();

      for (node = fragment.firstChild; node; node = node.firstChild) {
        if (node.nodeName === 'LI' && editor.dom.isEmpty(node)) {
          DOM.remove(node);
          break;
        }
      }

      if (!editor.dom.isEmpty(fragment)) {
        DOM.insertAfter(fragment, ul);
      }

      DOM.insertAfter(newBlock, ul);

      if (NodeType.isEmpty(editor.dom, li.parentNode)) {
        removeAndKeepBookmarks(li.parentNode);
      }

      DOM.remove(li);

      if (NodeType.isEmpty(editor.dom, ul)) {
        DOM.remove(ul);
      }
    };

    return {
      splitList: splitList
    };
  }
);


/**
 * Outdent.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.lists.actions.Outdent',
  [
    'tinymce.core.dom.DOMUtils',
    'tinymce.plugins.lists.core.Bookmark',
    'tinymce.plugins.lists.core.NodeType',
    'tinymce.plugins.lists.core.NormalizeLists',
    'tinymce.plugins.lists.core.Selection',
    'tinymce.plugins.lists.core.SplitList',
    'tinymce.plugins.lists.core.TextBlock'
  ],
  function (DOMUtils, Bookmark, NodeType, NormalizeLists, Selection, SplitList, TextBlock) {
    var DOM = DOMUtils.DOM;

    var removeEmptyLi = function (dom, li) {
      if (NodeType.isEmpty(dom, li)) {
        DOM.remove(li);
      }
    };

    var outdent = function (editor, li) {
      var ul = li.parentNode, ulParent = ul.parentNode, newBlock;

      if (ul === editor.getBody()) {
        return true;
      }

      if (li.nodeName === 'DD') {
        DOM.rename(li, 'DT');
        return true;
      }

      if (NodeType.isFirstChild(li) && NodeType.isLastChild(li)) {
        if (ulParent.nodeName === "LI") {
          DOM.insertAfter(li, ulParent);
          removeEmptyLi(editor.dom, ulParent);
          DOM.remove(ul);
        } else if (NodeType.isListNode(ulParent)) {
          DOM.remove(ul, true);
        } else {
          ulParent.insertBefore(TextBlock.createNewTextBlock(editor, li), ul);
          DOM.remove(ul);
        }

        return true;
      } else if (NodeType.isFirstChild(li)) {
        if (ulParent.nodeName === "LI") {
          DOM.insertAfter(li, ulParent);
          li.appendChild(ul);
          removeEmptyLi(editor.dom, ulParent);
        } else if (NodeType.isListNode(ulParent)) {
          ulParent.insertBefore(li, ul);
        } else {
          ulParent.insertBefore(TextBlock.createNewTextBlock(editor, li), ul);
          DOM.remove(li);
        }

        return true;
      } else if (NodeType.isLastChild(li)) {
        if (ulParent.nodeName === "LI") {
          DOM.insertAfter(li, ulParent);
        } else if (NodeType.isListNode(ulParent)) {
          DOM.insertAfter(li, ul);
        } else {
          DOM.insertAfter(TextBlock.createNewTextBlock(editor, li), ul);
          DOM.remove(li);
        }

        return true;
      }

      if (ulParent.nodeName === 'LI') {
        ul = ulParent;
        newBlock = TextBlock.createNewTextBlock(editor, li, 'LI');
      } else if (NodeType.isListNode(ulParent)) {
        newBlock = TextBlock.createNewTextBlock(editor, li, 'LI');
      } else {
        newBlock = TextBlock.createNewTextBlock(editor, li);
      }

      SplitList.splitList(editor, ul, li, newBlock);
      NormalizeLists.normalizeLists(editor.dom, ul.parentNode);

      return true;
    };

    var outdentSelection = function (editor) {
      var listElements = Selection.getSelectedListItems(editor);

      if (listElements.length) {
        var bookmark = Bookmark.createBookmark(editor.selection.getRng(true));
        var i, y, root = editor.getBody();

        i = listElements.length;
        while (i--) {
          var node = listElements[i].parentNode;

          while (node && node !== root) {
            y = listElements.length;
            while (y--) {
              if (listElements[y] === node) {
                listElements.splice(i, 1);
                break;
              }
            }

            node = node.parentNode;
          }
        }

        for (i = 0; i < listElements.length; i++) {
          if (!outdent(editor, listElements[i]) && i === 0) {
            break;
          }
        }

        editor.selection.setRng(Bookmark.resolveBookmark(bookmark));
        editor.nodeChanged();

        return true;
      }
    };

    return {
      outdent: outdent,
      outdentSelection: outdentSelection
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
  'tinymce.core.dom.BookmarkManager',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.dom.BookmarkManager');
  }
);

/**
 * ToggleList.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.lists.actions.ToggleList',
  [
    'tinymce.core.dom.BookmarkManager',
    'tinymce.core.util.Tools',
    'tinymce.plugins.lists.actions.Outdent',
    'tinymce.plugins.lists.core.Bookmark',
    'tinymce.plugins.lists.core.NodeType',
    'tinymce.plugins.lists.core.NormalizeLists',
    'tinymce.plugins.lists.core.Selection',
    'tinymce.plugins.lists.core.SplitList'
  ],
  function (BookmarkManager, Tools, Outdent, Bookmark, NodeType, NormalizeLists, Selection, SplitList) {
    var updateListStyle = function (dom, el, detail) {
      var type = detail['list-style-type'] ? detail['list-style-type'] : null;
      dom.setStyle(el, 'list-style-type', type);
    };

    var setAttribs = function (elm, attrs) {
      Tools.each(attrs, function (value, key) {
        elm.setAttribute(key, value);
      });
    };

    var updateListAttrs = function (dom, el, detail) {
      setAttribs(el, detail['list-attributes']);
      Tools.each(dom.select('li', el), function (li) {
        setAttribs(li, detail['list-item-attributes']);
      });
    };

    var updateListWithDetails = function (dom, el, detail) {
      updateListStyle(dom, el, detail);
      updateListAttrs(dom, el, detail);
    };

    var getEndPointNode = function (editor, rng, start) {
      var container, offset, root = editor.getBody();

      container = rng[start ? 'startContainer' : 'endContainer'];
      offset = rng[start ? 'startOffset' : 'endOffset'];

      // Resolve node index
      if (container.nodeType === 1) {
        container = container.childNodes[Math.min(offset, container.childNodes.length - 1)] || container;
      }

      while (container.parentNode !== root) {
        if (NodeType.isTextBlock(editor, container)) {
          return container;
        }

        if (/^(TD|TH)$/.test(container.parentNode.nodeName)) {
          return container;
        }

        container = container.parentNode;
      }

      return container;
    };

    var getSelectedTextBlocks = function (editor, rng) {
      var textBlocks = [], root = editor.getBody(), dom = editor.dom;

      var startNode = getEndPointNode(editor, rng, true);
      var endNode = getEndPointNode(editor, rng, false);
      var block, siblings = [];

      for (var node = startNode; node; node = node.nextSibling) {
        siblings.push(node);

        if (node === endNode) {
          break;
        }
      }

      Tools.each(siblings, function (node) {
        if (NodeType.isTextBlock(editor, node)) {
          textBlocks.push(node);
          block = null;
          return;
        }

        if (dom.isBlock(node) || NodeType.isBr(node)) {
          if (NodeType.isBr(node)) {
            dom.remove(node);
          }

          block = null;
          return;
        }

        var nextSibling = node.nextSibling;
        if (BookmarkManager.isBookmarkNode(node)) {
          if (NodeType.isTextBlock(editor, nextSibling) || (!nextSibling && node.parentNode === root)) {
            block = null;
            return;
          }
        }

        if (!block) {
          block = dom.create('p');
          node.parentNode.insertBefore(block, node);
          textBlocks.push(block);
        }

        block.appendChild(node);
      });

      return textBlocks;
    };

    var applyList = function (editor, listName, detail) {
      var rng = editor.selection.getRng(true), bookmark, listItemName = 'LI';
      var dom = editor.dom;

      detail = detail ? detail : {};

      if (dom.getContentEditable(editor.selection.getNode()) === "false") {
        return;
      }

      listName = listName.toUpperCase();

      if (listName === 'DL') {
        listItemName = 'DT';
      }

      bookmark = Bookmark.createBookmark(rng);

      Tools.each(getSelectedTextBlocks(editor, rng), function (block) {
        var listBlock, sibling;

        var hasCompatibleStyle = function (sib) {
          var sibStyle = dom.getStyle(sib, 'list-style-type');
          var detailStyle = detail ? detail['list-style-type'] : '';

          detailStyle = detailStyle === null ? '' : detailStyle;

          return sibStyle === detailStyle;
        };

        sibling = block.previousSibling;
        if (sibling && NodeType.isListNode(sibling) && sibling.nodeName === listName && hasCompatibleStyle(sibling)) {
          listBlock = sibling;
          block = dom.rename(block, listItemName);
          sibling.appendChild(block);
        } else {
          listBlock = dom.create(listName);
          block.parentNode.insertBefore(listBlock, block);
          listBlock.appendChild(block);
          block = dom.rename(block, listItemName);
        }

        updateListWithDetails(dom, listBlock, detail);
        mergeWithAdjacentLists(editor.dom, listBlock);
      });

      editor.selection.setRng(Bookmark.resolveBookmark(bookmark));
    };

    var removeList = function (editor) {
      var bookmark = Bookmark.createBookmark(editor.selection.getRng(true)), root = editor.getBody();
      var listItems = Selection.getSelectedListItems(editor);
      var emptyListItems = Tools.grep(listItems, function (li) {
        return editor.dom.isEmpty(li);
      });

      listItems = Tools.grep(listItems, function (li) {
        return !editor.dom.isEmpty(li);
      });

      Tools.each(emptyListItems, function (li) {
        if (NodeType.isEmpty(editor.dom, li)) {
          Outdent.outdent(editor, li);
          return;
        }
      });

      Tools.each(listItems, function (li) {
        var node, rootList;

        if (li.parentNode === editor.getBody()) {
          return;
        }

        for (node = li; node && node !== root; node = node.parentNode) {
          if (NodeType.isListNode(node)) {
            rootList = node;
          }
        }

        SplitList.splitList(editor, rootList, li);
        NormalizeLists.normalizeLists(editor.dom, rootList.parentNode);
      });

      editor.selection.setRng(Bookmark.resolveBookmark(bookmark));
    };

    var isValidLists = function (list1, list2) {
      return list1 && list2 && NodeType.isListNode(list1) && list1.nodeName === list2.nodeName;
    };

    var hasSameListStyle = function (dom, list1, list2) {
      var targetStyle = dom.getStyle(list1, 'list-style-type', true);
      var style = dom.getStyle(list2, 'list-style-type', true);
      return targetStyle === style;
    };

    var hasSameClasses = function (elm1, elm2) {
      return elm1.className === elm2.className;
    };

    var shouldMerge = function (dom, list1, list2) {
      return isValidLists(list1, list2) && hasSameListStyle(dom, list1, list2) && hasSameClasses(list1, list2);
    };

    var mergeWithAdjacentLists = function (dom, listBlock) {
      var sibling, node;

      sibling = listBlock.nextSibling;
      if (shouldMerge(dom, listBlock, sibling)) {
        while ((node = sibling.firstChild)) {
          listBlock.appendChild(node);
        }

        dom.remove(sibling);
      }

      sibling = listBlock.previousSibling;
      if (shouldMerge(dom, listBlock, sibling)) {
        while ((node = sibling.lastChild)) {
          listBlock.insertBefore(node, listBlock.firstChild);
        }

        dom.remove(sibling);
      }
    };

    var toggleList = function (editor, listName, detail) {
      var parentList = editor.dom.getParent(editor.selection.getStart(), 'OL,UL,DL');

      detail = detail ? detail : {};

      if (parentList === editor.getBody()) {
        return;
      }

      if (parentList) {
        if (parentList.nodeName === listName) {
          removeList(editor, listName);
        } else {
          var bookmark = Bookmark.createBookmark(editor.selection.getRng(true));
          updateListWithDetails(editor.dom, parentList, detail);
          mergeWithAdjacentLists(editor.dom, editor.dom.rename(parentList, listName));
          editor.selection.setRng(Bookmark.resolveBookmark(bookmark));
        }
      } else {
        applyList(editor, listName, detail);
      }
    };

    return {
      toggleList: toggleList,
      removeList: removeList,
      mergeWithAdjacentLists: mergeWithAdjacentLists
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
  'tinymce.core.dom.TreeWalker',
  [
    'global!tinymce.util.Tools.resolve'
  ],
  function (resolve) {
    return resolve('tinymce.dom.TreeWalker');
  }
);

/**
 * Delete.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.lists.core.Delete',
  [
    'tinymce.core.dom.RangeUtils',
    'tinymce.core.dom.TreeWalker',
    'tinymce.core.util.VK',
    'tinymce.plugins.lists.actions.ToggleList',
    'tinymce.plugins.lists.core.Bookmark',
    'tinymce.plugins.lists.core.NodeType',
    'tinymce.plugins.lists.core.NormalizeLists',
    'tinymce.plugins.lists.core.Range',
    'tinymce.plugins.lists.core.Selection'
  ],
  function (RangeUtils, TreeWalker, VK, ToggleList, Bookmark, NodeType, NormalizeLists, Range, Selection) {
    var findNextCaretContainer = function (editor, rng, isForward) {
      var node = rng.startContainer, offset = rng.startOffset;
      var nonEmptyBlocks, walker;

      if (node.nodeType === 3 && (isForward ? offset < node.data.length : offset > 0)) {
        return node;
      }

      nonEmptyBlocks = editor.schema.getNonEmptyElements();
      if (node.nodeType === 1) {
        node = RangeUtils.getNode(node, offset);
      }

      walker = new TreeWalker(node, editor.getBody());

      // Delete at <li>|<br></li> then jump over the bogus br
      if (isForward) {
        if (NodeType.isBogusBr(editor.dom, node)) {
          walker.next();
        }
      }

      while ((node = walker[isForward ? 'next' : 'prev2']())) {
        if (node.nodeName === 'LI' && !node.hasChildNodes()) {
          return node;
        }

        if (nonEmptyBlocks[node.nodeName]) {
          return node;
        }

        if (node.nodeType === 3 && node.data.length > 0) {
          return node;
        }
      }
    };

    var mergeLiElements = function (dom, fromElm, toElm) {
      var node, listNode, ul = fromElm.parentNode;

      if (!NodeType.isChildOfBody(dom, fromElm) || !NodeType.isChildOfBody(dom, toElm)) {
        return;
      }

      if (NodeType.isListNode(toElm.lastChild)) {
        listNode = toElm.lastChild;
      }

      if (ul === toElm.lastChild) {
        if (NodeType.isBr(ul.previousSibling)) {
          dom.remove(ul.previousSibling);
        }
      }

      node = toElm.lastChild;
      if (node && NodeType.isBr(node) && fromElm.hasChildNodes()) {
        dom.remove(node);
      }

      if (NodeType.isEmpty(dom, toElm, true)) {
        dom.$(toElm).empty();
      }

      if (!NodeType.isEmpty(dom, fromElm, true)) {
        while ((node = fromElm.firstChild)) {
          toElm.appendChild(node);
        }
      }

      if (listNode) {
        toElm.appendChild(listNode);
      }

      dom.remove(fromElm);

      if (NodeType.isEmpty(dom, ul) && ul !== dom.getRoot()) {
        dom.remove(ul);
      }
    };

    var backspaceDeleteFromListToListCaret = function (editor, isForward) {
      var dom = editor.dom, selection = editor.selection;
      var li = dom.getParent(selection.getStart(), 'LI'), ul, rng, otherLi;

      if (li) {
        ul = li.parentNode;
        if (ul === editor.getBody() && NodeType.isEmpty(dom, ul)) {
          return true;
        }

        rng = Range.normalizeRange(selection.getRng(true));
        otherLi = dom.getParent(findNextCaretContainer(editor, rng, isForward), 'LI');

        if (otherLi && otherLi !== li) {
          var bookmark = Bookmark.createBookmark(rng);

          if (isForward) {
            mergeLiElements(dom, otherLi, li);
          } else {
            mergeLiElements(dom, li, otherLi);
          }

          editor.selection.setRng(Bookmark.resolveBookmark(bookmark));

          return true;
        } else if (!otherLi) {
          if (!isForward && ToggleList.removeList(editor, ul.nodeName)) {
            return true;
          }
        }
      }

      return false;
    };

    var backspaceDeleteIntoListCaret = function (editor, isForward) {
      var dom = editor.dom;
      var block = dom.getParent(editor.selection.getStart(), dom.isBlock);

      if (block && dom.isEmpty(block)) {
        var rng = Range.normalizeRange(editor.selection.getRng(true));
        var otherLi = dom.getParent(findNextCaretContainer(editor, rng, isForward), 'LI');

        if (otherLi) {
          editor.undoManager.transact(function () {
            dom.remove(block);
            ToggleList.mergeWithAdjacentLists(dom, otherLi.parentNode);
            editor.selection.select(otherLi, true);
            editor.selection.collapse(isForward);
          });

          return true;
        }
      }

      return false;
    };

    var backspaceDeleteCaret = function (editor, isForward) {
      return backspaceDeleteFromListToListCaret(editor, isForward) || backspaceDeleteIntoListCaret(editor, isForward);
    };

    var backspaceDeleteRange = function (editor) {
      var startListParent = editor.dom.getParent(editor.selection.getStart(), 'LI,DT,DD');

      if (startListParent || Selection.getSelectedListItems(editor).length > 0) {
        editor.undoManager.transact(function () {
          editor.execCommand('Delete');
          NormalizeLists.normalizeLists(editor.dom, editor.getBody());
        });

        return true;
      }

      return false;
    };

    var backspaceDelete = function (editor, isForward) {
      return editor.selection.isCollapsed() ? backspaceDeleteCaret(editor, isForward) : backspaceDeleteRange(editor);
    };

    var setup = function (editor) {
      editor.on('keydown', function (e) {
        if (e.keyCode === VK.BACKSPACE) {
          if (backspaceDelete(editor, false)) {
            e.preventDefault();
          }
        } else if (e.keyCode === VK.DELETE) {
          if (backspaceDelete(editor, true)) {
            e.preventDefault();
          }
        }
      });
    };

    return {
      setup: setup,
      backspaceDelete: backspaceDelete
    };
  }
);


/**
 * plugin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2017 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define(
  'tinymce.plugins.lists.Plugin',
  [
    'tinymce.core.PluginManager',
    'tinymce.core.util.Tools',
    'tinymce.core.util.VK',
    'tinymce.plugins.lists.actions.Indent',
    'tinymce.plugins.lists.actions.Outdent',
    'tinymce.plugins.lists.actions.ToggleList',
    'tinymce.plugins.lists.core.Delete',
    'tinymce.plugins.lists.core.NodeType'
  ],
  function (PluginManager, Tools, VK, Indent, Outdent, ToggleList, Delete, NodeType) {
    var queryListCommandState = function (editor, listName) {
      return function () {
        var parentList = editor.dom.getParent(editor.selection.getStart(), 'UL,OL,DL');
        return parentList && parentList.nodeName === listName;
      };
    };

    var setupCommands = function (editor) {
      editor.on('BeforeExecCommand', function (e) {
        var cmd = e.command.toLowerCase(), isHandled;

        if (cmd === "indent") {
          if (Indent.indentSelection(editor)) {
            isHandled = true;
          }
        } else if (cmd === "outdent") {
          if (Outdent.outdentSelection(editor)) {
            isHandled = true;
          }
        }

        if (isHandled) {
          editor.fire('ExecCommand', { command: e.command });
          e.preventDefault();
          return true;
        }
      });

      editor.addCommand('InsertUnorderedList', function (ui, detail) {
        ToggleList.toggleList(editor, 'UL', detail);
      });

      editor.addCommand('InsertOrderedList', function (ui, detail) {
        ToggleList.toggleList(editor, 'OL', detail);
      });

      editor.addCommand('InsertDefinitionList', function (ui, detail) {
        ToggleList.toggleList(editor, 'DL', detail);
      });
    };

    var setupStateHandlers = function (editor) {
      editor.addQueryStateHandler('InsertUnorderedList', queryListCommandState(editor, 'UL'));
      editor.addQueryStateHandler('InsertOrderedList', queryListCommandState(editor, 'OL'));
      editor.addQueryStateHandler('InsertDefinitionList', queryListCommandState(editor, 'DL'));
    };

    var setupTabKey = function (editor) {
      editor.on('keydown', function (e) {
        // Check for tab but not ctrl/cmd+tab since it switches browser tabs
        if (e.keyCode !== 9 || VK.metaKeyPressed(e)) {
          return;
        }

        if (editor.dom.getParent(editor.selection.getStart(), 'LI,DT,DD')) {
          e.preventDefault();

          if (e.shiftKey) {
            Outdent.outdentSelection(editor);
          } else {
            Indent.indentSelection(editor);
          }
        }
      });
    };

    var setupUi = function (editor) {
      var listState = function (listName) {
        return function () {
          var self = this;

          editor.on('NodeChange', function (e) {
            var lists = Tools.grep(e.parents, NodeType.isListNode);
            self.active(lists.length > 0 && lists[0].nodeName === listName);
          });
        };
      };

      var hasPlugin = function (editor, plugin) {
        var plugins = editor.settings.plugins ? editor.settings.plugins : '';
        return Tools.inArray(plugins.split(/[ ,]/), plugin) !== -1;
      };

      if (!hasPlugin(editor, 'advlist')) {
        editor.addButton('numlist', {
          title: 'Numbered list',
          cmd: 'InsertOrderedList',
          onPostRender: listState('OL')
        });

        editor.addButton('bullist', {
          title: 'Bullet list',
          cmd: 'InsertUnorderedList',
          onPostRender: listState('UL')
        });
      }

      editor.addButton('indent', {
        icon: 'indent',
        title: 'Increase indent',
        cmd: 'Indent',
        onPostRender: function (e) {
          var ctrl = e.control;

          editor.on('nodechange', function () {
            var blocks = editor.selection.getSelectedBlocks();
            var disable = false;

            for (var i = 0, l = blocks.length; !disable && i < l; i++) {
              var tag = blocks[i].nodeName;

              disable = (tag === 'LI' && NodeType.isFirstChild(blocks[i]) || tag === 'UL' || tag === 'OL' || tag === 'DD');
            }

            ctrl.disabled(disable);
          });
        }
      });
    };

    PluginManager.add('lists', function (editor) {
      setupUi(editor);
      Delete.setup(editor);

      editor.on('init', function () {
        setupCommands(editor);
        setupStateHandlers(editor);
        setupTabKey(editor);
      });

      return {
        backspaceDelete: function (isForward) {
          Delete.backspaceDelete(editor, isForward);
        }
      };
    });

    return function () { };
  }
);


dem('tinymce.plugins.lists.Plugin')();
})();
