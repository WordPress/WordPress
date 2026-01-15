(function () {
var lists = (function (domGlobals) {
    'use strict';

    var global = tinymce.util.Tools.resolve('tinymce.PluginManager');

    var global$1 = tinymce.util.Tools.resolve('tinymce.dom.RangeUtils');

    var global$2 = tinymce.util.Tools.resolve('tinymce.dom.TreeWalker');

    var global$3 = tinymce.util.Tools.resolve('tinymce.util.VK');

    var global$4 = tinymce.util.Tools.resolve('tinymce.dom.BookmarkManager');

    var global$5 = tinymce.util.Tools.resolve('tinymce.util.Tools');

    var global$6 = tinymce.util.Tools.resolve('tinymce.dom.DOMUtils');

    var isTextNode = function (node) {
      return node && node.nodeType === 3;
    };
    var isListNode = function (node) {
      return node && /^(OL|UL|DL)$/.test(node.nodeName);
    };
    var isOlUlNode = function (node) {
      return node && /^(OL|UL)$/.test(node.nodeName);
    };
    var isListItemNode = function (node) {
      return node && /^(LI|DT|DD)$/.test(node.nodeName);
    };
    var isDlItemNode = function (node) {
      return node && /^(DT|DD)$/.test(node.nodeName);
    };
    var isTableCellNode = function (node) {
      return node && /^(TH|TD)$/.test(node.nodeName);
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
    var isBlock = function (node, blockElements) {
      return node && node.nodeName in blockElements;
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
    var NodeType = {
      isTextNode: isTextNode,
      isListNode: isListNode,
      isOlUlNode: isOlUlNode,
      isDlItemNode: isDlItemNode,
      isListItemNode: isListItemNode,
      isTableCellNode: isTableCellNode,
      isBr: isBr,
      isFirstChild: isFirstChild,
      isLastChild: isLastChild,
      isTextBlock: isTextBlock,
      isBlock: isBlock,
      isBogusBr: isBogusBr,
      isEmpty: isEmpty,
      isChildOfBody: isChildOfBody
    };

    var getNormalizedPoint = function (container, offset) {
      if (NodeType.isTextNode(container)) {
        return {
          container: container,
          offset: offset
        };
      }
      var node = global$1.getNode(container, offset);
      if (NodeType.isTextNode(node)) {
        return {
          container: node,
          offset: offset >= container.childNodes.length ? node.data.length : 0
        };
      } else if (node.previousSibling && NodeType.isTextNode(node.previousSibling)) {
        return {
          container: node.previousSibling,
          offset: node.previousSibling.data.length
        };
      } else if (node.nextSibling && NodeType.isTextNode(node.nextSibling)) {
        return {
          container: node.nextSibling,
          offset: 0
        };
      }
      return {
        container: container,
        offset: offset
      };
    };
    var normalizeRange = function (rng) {
      var outRng = rng.cloneRange();
      var rangeStart = getNormalizedPoint(rng.startContainer, rng.startOffset);
      outRng.setStart(rangeStart.container, rangeStart.offset);
      var rangeEnd = getNormalizedPoint(rng.endContainer, rng.endOffset);
      outRng.setEnd(rangeEnd.container, rangeEnd.offset);
      return outRng;
    };
    var Range = {
      getNormalizedPoint: getNormalizedPoint,
      normalizeRange: normalizeRange
    };

    var DOM = global$6.DOM;
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
          if (!container.hasChildNodes() && DOM.isBlock(container)) {
            container.appendChild(DOM.create('br'));
          }
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
    var Bookmark = {
      createBookmark: createBookmark,
      resolveBookmark: resolveBookmark
    };

    var noop = function () {
    };
    var constant = function (value) {
      return function () {
        return value;
      };
    };
    var not = function (f) {
      return function () {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
          args[_i] = arguments[_i];
        }
        return !f.apply(null, args);
      };
    };
    var never = constant(false);
    var always = constant(true);

    var none = function () {
      return NONE;
    };
    var NONE = function () {
      var eq = function (o) {
        return o.isNone();
      };
      var call = function (thunk) {
        return thunk();
      };
      var id = function (n) {
        return n;
      };
      var me = {
        fold: function (n, s) {
          return n();
        },
        is: never,
        isSome: never,
        isNone: always,
        getOr: id,
        getOrThunk: call,
        getOrDie: function (msg) {
          throw new Error(msg || 'error: getOrDie called on none.');
        },
        getOrNull: constant(null),
        getOrUndefined: constant(undefined),
        or: id,
        orThunk: call,
        map: none,
        each: noop,
        bind: none,
        exists: never,
        forall: always,
        filter: none,
        equals: eq,
        equals_: eq,
        toArray: function () {
          return [];
        },
        toString: constant('none()')
      };
      if (Object.freeze) {
        Object.freeze(me);
      }
      return me;
    }();
    var some = function (a) {
      var constant_a = constant(a);
      var self = function () {
        return me;
      };
      var bind = function (f) {
        return f(a);
      };
      var me = {
        fold: function (n, s) {
          return s(a);
        },
        is: function (v) {
          return a === v;
        },
        isSome: always,
        isNone: never,
        getOr: constant_a,
        getOrThunk: constant_a,
        getOrDie: constant_a,
        getOrNull: constant_a,
        getOrUndefined: constant_a,
        or: self,
        orThunk: self,
        map: function (f) {
          return some(f(a));
        },
        each: function (f) {
          f(a);
        },
        bind: bind,
        exists: bind,
        forall: bind,
        filter: function (f) {
          return f(a) ? me : NONE;
        },
        toArray: function () {
          return [a];
        },
        toString: function () {
          return 'some(' + a + ')';
        },
        equals: function (o) {
          return o.is(a);
        },
        equals_: function (o, elementEq) {
          return o.fold(never, function (b) {
            return elementEq(a, b);
          });
        }
      };
      return me;
    };
    var from = function (value) {
      return value === null || value === undefined ? NONE : some(value);
    };
    var Option = {
      some: some,
      none: none,
      from: from
    };

    var typeOf = function (x) {
      if (x === null) {
        return 'null';
      }
      var t = typeof x;
      if (t === 'object' && (Array.prototype.isPrototypeOf(x) || x.constructor && x.constructor.name === 'Array')) {
        return 'array';
      }
      if (t === 'object' && (String.prototype.isPrototypeOf(x) || x.constructor && x.constructor.name === 'String')) {
        return 'string';
      }
      return t;
    };
    var isType = function (type) {
      return function (value) {
        return typeOf(value) === type;
      };
    };
    var isString = isType('string');
    var isArray = isType('array');
    var isBoolean = isType('boolean');
    var isFunction = isType('function');
    var isNumber = isType('number');

    var nativeSlice = Array.prototype.slice;
    var nativePush = Array.prototype.push;
    var map = function (xs, f) {
      var len = xs.length;
      var r = new Array(len);
      for (var i = 0; i < len; i++) {
        var x = xs[i];
        r[i] = f(x, i);
      }
      return r;
    };
    var each = function (xs, f) {
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        f(x, i);
      }
    };
    var filter = function (xs, pred) {
      var r = [];
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        if (pred(x, i)) {
          r.push(x);
        }
      }
      return r;
    };
    var groupBy = function (xs, f) {
      if (xs.length === 0) {
        return [];
      } else {
        var wasType = f(xs[0]);
        var r = [];
        var group = [];
        for (var i = 0, len = xs.length; i < len; i++) {
          var x = xs[i];
          var type = f(x);
          if (type !== wasType) {
            r.push(group);
            group = [];
          }
          wasType = type;
          group.push(x);
        }
        if (group.length !== 0) {
          r.push(group);
        }
        return r;
      }
    };
    var foldl = function (xs, f, acc) {
      each(xs, function (x) {
        acc = f(acc, x);
      });
      return acc;
    };
    var find = function (xs, pred) {
      for (var i = 0, len = xs.length; i < len; i++) {
        var x = xs[i];
        if (pred(x, i)) {
          return Option.some(x);
        }
      }
      return Option.none();
    };
    var flatten = function (xs) {
      var r = [];
      for (var i = 0, len = xs.length; i < len; ++i) {
        if (!isArray(xs[i])) {
          throw new Error('Arr.flatten item ' + i + ' was not an array, input: ' + xs);
        }
        nativePush.apply(r, xs[i]);
      }
      return r;
    };
    var bind = function (xs, f) {
      var output = map(xs, f);
      return flatten(output);
    };
    var reverse = function (xs) {
      var r = nativeSlice.call(xs, 0);
      r.reverse();
      return r;
    };
    var head = function (xs) {
      return xs.length === 0 ? Option.none() : Option.some(xs[0]);
    };
    var last = function (xs) {
      return xs.length === 0 ? Option.none() : Option.some(xs[xs.length - 1]);
    };
    var from$1 = isFunction(Array.from) ? Array.from : function (x) {
      return nativeSlice.call(x);
    };

    var Global = typeof domGlobals.window !== 'undefined' ? domGlobals.window : Function('return this;')();

    var path = function (parts, scope) {
      var o = scope !== undefined && scope !== null ? scope : Global;
      for (var i = 0; i < parts.length && o !== undefined && o !== null; ++i) {
        o = o[parts[i]];
      }
      return o;
    };
    var resolve = function (p, scope) {
      var parts = p.split('.');
      return path(parts, scope);
    };

    var unsafe = function (name, scope) {
      return resolve(name, scope);
    };
    var getOrDie = function (name, scope) {
      var actual = unsafe(name, scope);
      if (actual === undefined || actual === null) {
        throw new Error(name + ' not available on this browser');
      }
      return actual;
    };
    var Global$1 = { getOrDie: getOrDie };

    var htmlElement = function (scope) {
      return Global$1.getOrDie('HTMLElement', scope);
    };
    var isPrototypeOf = function (x) {
      var scope = resolve('ownerDocument.defaultView', x);
      return htmlElement(scope).prototype.isPrototypeOf(x);
    };
    var HTMLElement = { isPrototypeOf: isPrototypeOf };

    var global$7 = tinymce.util.Tools.resolve('tinymce.dom.DomQuery');

    var getParentList = function (editor) {
      var selectionStart = editor.selection.getStart(true);
      return editor.dom.getParent(selectionStart, 'OL,UL,DL', getClosestListRootElm(editor, selectionStart));
    };
    var isParentListSelected = function (parentList, selectedBlocks) {
      return parentList && selectedBlocks.length === 1 && selectedBlocks[0] === parentList;
    };
    var findSubLists = function (parentList) {
      return global$5.grep(parentList.querySelectorAll('ol,ul,dl'), function (elm) {
        return NodeType.isListNode(elm);
      });
    };
    var getSelectedSubLists = function (editor) {
      var parentList = getParentList(editor);
      var selectedBlocks = editor.selection.getSelectedBlocks();
      if (isParentListSelected(parentList, selectedBlocks)) {
        return findSubLists(parentList);
      } else {
        return global$5.grep(selectedBlocks, function (elm) {
          return NodeType.isListNode(elm) && parentList !== elm;
        });
      }
    };
    var findParentListItemsNodes = function (editor, elms) {
      var listItemsElms = global$5.map(elms, function (elm) {
        var parentLi = editor.dom.getParent(elm, 'li,dd,dt', getClosestListRootElm(editor, elm));
        return parentLi ? parentLi : elm;
      });
      return global$7.unique(listItemsElms);
    };
    var getSelectedListItems = function (editor) {
      var selectedBlocks = editor.selection.getSelectedBlocks();
      return global$5.grep(findParentListItemsNodes(editor, selectedBlocks), function (block) {
        return NodeType.isListItemNode(block);
      });
    };
    var getSelectedDlItems = function (editor) {
      return filter(getSelectedListItems(editor), NodeType.isDlItemNode);
    };
    var getClosestListRootElm = function (editor, elm) {
      var parentTableCell = editor.dom.getParents(elm, 'TD,TH');
      var root = parentTableCell.length > 0 ? parentTableCell[0] : editor.getBody();
      return root;
    };
    var findLastParentListNode = function (editor, elm) {
      var parentLists = editor.dom.getParents(elm, 'ol,ul', getClosestListRootElm(editor, elm));
      return last(parentLists);
    };
    var getSelectedLists = function (editor) {
      var firstList = findLastParentListNode(editor, editor.selection.getStart());
      var subsequentLists = filter(editor.selection.getSelectedBlocks(), NodeType.isOlUlNode);
      return firstList.toArray().concat(subsequentLists);
    };
    var getSelectedListRoots = function (editor) {
      var selectedLists = getSelectedLists(editor);
      return getUniqueListRoots(editor, selectedLists);
    };
    var getUniqueListRoots = function (editor, lists) {
      var listRoots = map(lists, function (list) {
        return findLastParentListNode(editor, list).getOr(list);
      });
      return global$7.unique(listRoots);
    };
    var isList = function (editor) {
      var list = getParentList(editor);
      return HTMLElement.isPrototypeOf(list);
    };
    var Selection = {
      isList: isList,
      getParentList: getParentList,
      getSelectedSubLists: getSelectedSubLists,
      getSelectedListItems: getSelectedListItems,
      getClosestListRootElm: getClosestListRootElm,
      getSelectedDlItems: getSelectedDlItems,
      getSelectedListRoots: getSelectedListRoots
    };

    var fromHtml = function (html, scope) {
      var doc = scope || domGlobals.document;
      var div = doc.createElement('div');
      div.innerHTML = html;
      if (!div.hasChildNodes() || div.childNodes.length > 1) {
        domGlobals.console.error('HTML does not have a single root node', html);
        throw new Error('HTML must have a single root node');
      }
      return fromDom(div.childNodes[0]);
    };
    var fromTag = function (tag, scope) {
      var doc = scope || domGlobals.document;
      var node = doc.createElement(tag);
      return fromDom(node);
    };
    var fromText = function (text, scope) {
      var doc = scope || domGlobals.document;
      var node = doc.createTextNode(text);
      return fromDom(node);
    };
    var fromDom = function (node) {
      if (node === null || node === undefined) {
        throw new Error('Node cannot be null or undefined');
      }
      return { dom: constant(node) };
    };
    var fromPoint = function (docElm, x, y) {
      var doc = docElm.dom();
      return Option.from(doc.elementFromPoint(x, y)).map(fromDom);
    };
    var Element = {
      fromHtml: fromHtml,
      fromTag: fromTag,
      fromText: fromText,
      fromDom: fromDom,
      fromPoint: fromPoint
    };

    var lift2 = function (oa, ob, f) {
      return oa.isSome() && ob.isSome() ? Option.some(f(oa.getOrDie(), ob.getOrDie())) : Option.none();
    };

    var fromElements = function (elements, scope) {
      var doc = scope || domGlobals.document;
      var fragment = doc.createDocumentFragment();
      each(elements, function (element) {
        fragment.appendChild(element.dom());
      });
      return Element.fromDom(fragment);
    };

    var Immutable = function () {
      var fields = [];
      for (var _i = 0; _i < arguments.length; _i++) {
        fields[_i] = arguments[_i];
      }
      return function () {
        var values = [];
        for (var _i = 0; _i < arguments.length; _i++) {
          values[_i] = arguments[_i];
        }
        if (fields.length !== values.length) {
          throw new Error('Wrong number of arguments to struct. Expected "[' + fields.length + ']", got ' + values.length + ' arguments');
        }
        var struct = {};
        each(fields, function (name, i) {
          struct[name] = constant(values[i]);
        });
        return struct;
      };
    };

    var keys = Object.keys;
    var each$1 = function (obj, f) {
      var props = keys(obj);
      for (var k = 0, len = props.length; k < len; k++) {
        var i = props[k];
        var x = obj[i];
        f(x, i);
      }
    };

    var node = function () {
      var f = Global$1.getOrDie('Node');
      return f;
    };
    var compareDocumentPosition = function (a, b, match) {
      return (a.compareDocumentPosition(b) & match) !== 0;
    };
    var documentPositionPreceding = function (a, b) {
      return compareDocumentPosition(a, b, node().DOCUMENT_POSITION_PRECEDING);
    };
    var documentPositionContainedBy = function (a, b) {
      return compareDocumentPosition(a, b, node().DOCUMENT_POSITION_CONTAINED_BY);
    };
    var Node = {
      documentPositionPreceding: documentPositionPreceding,
      documentPositionContainedBy: documentPositionContainedBy
    };

    var cached = function (f) {
      var called = false;
      var r;
      return function () {
        var args = [];
        for (var _i = 0; _i < arguments.length; _i++) {
          args[_i] = arguments[_i];
        }
        if (!called) {
          called = true;
          r = f.apply(null, args);
        }
        return r;
      };
    };

    var firstMatch = function (regexes, s) {
      for (var i = 0; i < regexes.length; i++) {
        var x = regexes[i];
        if (x.test(s)) {
          return x;
        }
      }
      return undefined;
    };
    var find$1 = function (regexes, agent) {
      var r = firstMatch(regexes, agent);
      if (!r) {
        return {
          major: 0,
          minor: 0
        };
      }
      var group = function (i) {
        return Number(agent.replace(r, '$' + i));
      };
      return nu(group(1), group(2));
    };
    var detect = function (versionRegexes, agent) {
      var cleanedAgent = String(agent).toLowerCase();
      if (versionRegexes.length === 0) {
        return unknown();
      }
      return find$1(versionRegexes, cleanedAgent);
    };
    var unknown = function () {
      return nu(0, 0);
    };
    var nu = function (major, minor) {
      return {
        major: major,
        minor: minor
      };
    };
    var Version = {
      nu: nu,
      detect: detect,
      unknown: unknown
    };

    var edge = 'Edge';
    var chrome = 'Chrome';
    var ie = 'IE';
    var opera = 'Opera';
    var firefox = 'Firefox';
    var safari = 'Safari';
    var isBrowser = function (name, current) {
      return function () {
        return current === name;
      };
    };
    var unknown$1 = function () {
      return nu$1({
        current: undefined,
        version: Version.unknown()
      });
    };
    var nu$1 = function (info) {
      var current = info.current;
      var version = info.version;
      return {
        current: current,
        version: version,
        isEdge: isBrowser(edge, current),
        isChrome: isBrowser(chrome, current),
        isIE: isBrowser(ie, current),
        isOpera: isBrowser(opera, current),
        isFirefox: isBrowser(firefox, current),
        isSafari: isBrowser(safari, current)
      };
    };
    var Browser = {
      unknown: unknown$1,
      nu: nu$1,
      edge: constant(edge),
      chrome: constant(chrome),
      ie: constant(ie),
      opera: constant(opera),
      firefox: constant(firefox),
      safari: constant(safari)
    };

    var windows = 'Windows';
    var ios = 'iOS';
    var android = 'Android';
    var linux = 'Linux';
    var osx = 'OSX';
    var solaris = 'Solaris';
    var freebsd = 'FreeBSD';
    var isOS = function (name, current) {
      return function () {
        return current === name;
      };
    };
    var unknown$2 = function () {
      return nu$2({
        current: undefined,
        version: Version.unknown()
      });
    };
    var nu$2 = function (info) {
      var current = info.current;
      var version = info.version;
      return {
        current: current,
        version: version,
        isWindows: isOS(windows, current),
        isiOS: isOS(ios, current),
        isAndroid: isOS(android, current),
        isOSX: isOS(osx, current),
        isLinux: isOS(linux, current),
        isSolaris: isOS(solaris, current),
        isFreeBSD: isOS(freebsd, current)
      };
    };
    var OperatingSystem = {
      unknown: unknown$2,
      nu: nu$2,
      windows: constant(windows),
      ios: constant(ios),
      android: constant(android),
      linux: constant(linux),
      osx: constant(osx),
      solaris: constant(solaris),
      freebsd: constant(freebsd)
    };

    var DeviceType = function (os, browser, userAgent) {
      var isiPad = os.isiOS() && /ipad/i.test(userAgent) === true;
      var isiPhone = os.isiOS() && !isiPad;
      var isAndroid3 = os.isAndroid() && os.version.major === 3;
      var isAndroid4 = os.isAndroid() && os.version.major === 4;
      var isTablet = isiPad || isAndroid3 || isAndroid4 && /mobile/i.test(userAgent) === true;
      var isTouch = os.isiOS() || os.isAndroid();
      var isPhone = isTouch && !isTablet;
      var iOSwebview = browser.isSafari() && os.isiOS() && /safari/i.test(userAgent) === false;
      return {
        isiPad: constant(isiPad),
        isiPhone: constant(isiPhone),
        isTablet: constant(isTablet),
        isPhone: constant(isPhone),
        isTouch: constant(isTouch),
        isAndroid: os.isAndroid,
        isiOS: os.isiOS,
        isWebView: constant(iOSwebview)
      };
    };

    var detect$1 = function (candidates, userAgent) {
      var agent = String(userAgent).toLowerCase();
      return find(candidates, function (candidate) {
        return candidate.search(agent);
      });
    };
    var detectBrowser = function (browsers, userAgent) {
      return detect$1(browsers, userAgent).map(function (browser) {
        var version = Version.detect(browser.versionRegexes, userAgent);
        return {
          current: browser.name,
          version: version
        };
      });
    };
    var detectOs = function (oses, userAgent) {
      return detect$1(oses, userAgent).map(function (os) {
        var version = Version.detect(os.versionRegexes, userAgent);
        return {
          current: os.name,
          version: version
        };
      });
    };
    var UaString = {
      detectBrowser: detectBrowser,
      detectOs: detectOs
    };

    var contains = function (str, substr) {
      return str.indexOf(substr) !== -1;
    };

    var normalVersionRegex = /.*?version\/\ ?([0-9]+)\.([0-9]+).*/;
    var checkContains = function (target) {
      return function (uastring) {
        return contains(uastring, target);
      };
    };
    var browsers = [
      {
        name: 'Edge',
        versionRegexes: [/.*?edge\/ ?([0-9]+)\.([0-9]+)$/],
        search: function (uastring) {
          return contains(uastring, 'edge/') && contains(uastring, 'chrome') && contains(uastring, 'safari') && contains(uastring, 'applewebkit');
        }
      },
      {
        name: 'Chrome',
        versionRegexes: [
          /.*?chrome\/([0-9]+)\.([0-9]+).*/,
          normalVersionRegex
        ],
        search: function (uastring) {
          return contains(uastring, 'chrome') && !contains(uastring, 'chromeframe');
        }
      },
      {
        name: 'IE',
        versionRegexes: [
          /.*?msie\ ?([0-9]+)\.([0-9]+).*/,
          /.*?rv:([0-9]+)\.([0-9]+).*/
        ],
        search: function (uastring) {
          return contains(uastring, 'msie') || contains(uastring, 'trident');
        }
      },
      {
        name: 'Opera',
        versionRegexes: [
          normalVersionRegex,
          /.*?opera\/([0-9]+)\.([0-9]+).*/
        ],
        search: checkContains('opera')
      },
      {
        name: 'Firefox',
        versionRegexes: [/.*?firefox\/\ ?([0-9]+)\.([0-9]+).*/],
        search: checkContains('firefox')
      },
      {
        name: 'Safari',
        versionRegexes: [
          normalVersionRegex,
          /.*?cpu os ([0-9]+)_([0-9]+).*/
        ],
        search: function (uastring) {
          return (contains(uastring, 'safari') || contains(uastring, 'mobile/')) && contains(uastring, 'applewebkit');
        }
      }
    ];
    var oses = [
      {
        name: 'Windows',
        search: checkContains('win'),
        versionRegexes: [/.*?windows\ nt\ ?([0-9]+)\.([0-9]+).*/]
      },
      {
        name: 'iOS',
        search: function (uastring) {
          return contains(uastring, 'iphone') || contains(uastring, 'ipad');
        },
        versionRegexes: [
          /.*?version\/\ ?([0-9]+)\.([0-9]+).*/,
          /.*cpu os ([0-9]+)_([0-9]+).*/,
          /.*cpu iphone os ([0-9]+)_([0-9]+).*/
        ]
      },
      {
        name: 'Android',
        search: checkContains('android'),
        versionRegexes: [/.*?android\ ?([0-9]+)\.([0-9]+).*/]
      },
      {
        name: 'OSX',
        search: checkContains('os x'),
        versionRegexes: [/.*?os\ x\ ?([0-9]+)_([0-9]+).*/]
      },
      {
        name: 'Linux',
        search: checkContains('linux'),
        versionRegexes: []
      },
      {
        name: 'Solaris',
        search: checkContains('sunos'),
        versionRegexes: []
      },
      {
        name: 'FreeBSD',
        search: checkContains('freebsd'),
        versionRegexes: []
      }
    ];
    var PlatformInfo = {
      browsers: constant(browsers),
      oses: constant(oses)
    };

    var detect$2 = function (userAgent) {
      var browsers = PlatformInfo.browsers();
      var oses = PlatformInfo.oses();
      var browser = UaString.detectBrowser(browsers, userAgent).fold(Browser.unknown, Browser.nu);
      var os = UaString.detectOs(oses, userAgent).fold(OperatingSystem.unknown, OperatingSystem.nu);
      var deviceType = DeviceType(os, browser, userAgent);
      return {
        browser: browser,
        os: os,
        deviceType: deviceType
      };
    };
    var PlatformDetection = { detect: detect$2 };

    var detect$3 = cached(function () {
      var userAgent = domGlobals.navigator.userAgent;
      return PlatformDetection.detect(userAgent);
    });
    var PlatformDetection$1 = { detect: detect$3 };

    var ATTRIBUTE = domGlobals.Node.ATTRIBUTE_NODE;
    var CDATA_SECTION = domGlobals.Node.CDATA_SECTION_NODE;
    var COMMENT = domGlobals.Node.COMMENT_NODE;
    var DOCUMENT = domGlobals.Node.DOCUMENT_NODE;
    var DOCUMENT_TYPE = domGlobals.Node.DOCUMENT_TYPE_NODE;
    var DOCUMENT_FRAGMENT = domGlobals.Node.DOCUMENT_FRAGMENT_NODE;
    var ELEMENT = domGlobals.Node.ELEMENT_NODE;
    var TEXT = domGlobals.Node.TEXT_NODE;
    var PROCESSING_INSTRUCTION = domGlobals.Node.PROCESSING_INSTRUCTION_NODE;
    var ENTITY_REFERENCE = domGlobals.Node.ENTITY_REFERENCE_NODE;
    var ENTITY = domGlobals.Node.ENTITY_NODE;
    var NOTATION = domGlobals.Node.NOTATION_NODE;

    var ELEMENT$1 = ELEMENT;
    var is = function (element, selector) {
      var dom = element.dom();
      if (dom.nodeType !== ELEMENT$1) {
        return false;
      } else {
        var elem = dom;
        if (elem.matches !== undefined) {
          return elem.matches(selector);
        } else if (elem.msMatchesSelector !== undefined) {
          return elem.msMatchesSelector(selector);
        } else if (elem.webkitMatchesSelector !== undefined) {
          return elem.webkitMatchesSelector(selector);
        } else if (elem.mozMatchesSelector !== undefined) {
          return elem.mozMatchesSelector(selector);
        } else {
          throw new Error('Browser lacks native selectors');
        }
      }
    };

    var eq = function (e1, e2) {
      return e1.dom() === e2.dom();
    };
    var regularContains = function (e1, e2) {
      var d1 = e1.dom();
      var d2 = e2.dom();
      return d1 === d2 ? false : d1.contains(d2);
    };
    var ieContains = function (e1, e2) {
      return Node.documentPositionContainedBy(e1.dom(), e2.dom());
    };
    var browser = PlatformDetection$1.detect().browser;
    var contains$1 = browser.isIE() ? ieContains : regularContains;
    var is$1 = is;

    var parent = function (element) {
      return Option.from(element.dom().parentNode).map(Element.fromDom);
    };
    var children = function (element) {
      return map(element.dom().childNodes, Element.fromDom);
    };
    var child = function (element, index) {
      var cs = element.dom().childNodes;
      return Option.from(cs[index]).map(Element.fromDom);
    };
    var firstChild = function (element) {
      return child(element, 0);
    };
    var lastChild = function (element) {
      return child(element, element.dom().childNodes.length - 1);
    };
    var spot = Immutable('element', 'offset');

    var before = function (marker, element) {
      var parent$1 = parent(marker);
      parent$1.each(function (v) {
        v.dom().insertBefore(element.dom(), marker.dom());
      });
    };
    var append = function (parent, element) {
      parent.dom().appendChild(element.dom());
    };

    var before$1 = function (marker, elements) {
      each(elements, function (x) {
        before(marker, x);
      });
    };
    var append$1 = function (parent, elements) {
      each(elements, function (x) {
        append(parent, x);
      });
    };

    var remove = function (element) {
      var dom = element.dom();
      if (dom.parentNode !== null) {
        dom.parentNode.removeChild(dom);
      }
    };

    var name = function (element) {
      var r = element.dom().nodeName;
      return r.toLowerCase();
    };
    var type = function (element) {
      return element.dom().nodeType;
    };
    var isType$1 = function (t) {
      return function (element) {
        return type(element) === t;
      };
    };
    var isElement = isType$1(ELEMENT);

    var rawSet = function (dom, key, value) {
      if (isString(value) || isBoolean(value) || isNumber(value)) {
        dom.setAttribute(key, value + '');
      } else {
        domGlobals.console.error('Invalid call to Attr.set. Key ', key, ':: Value ', value, ':: Element ', dom);
        throw new Error('Attribute value was not simple');
      }
    };
    var setAll = function (element, attrs) {
      var dom = element.dom();
      each$1(attrs, function (v, k) {
        rawSet(dom, k, v);
      });
    };
    var clone = function (element) {
      return foldl(element.dom().attributes, function (acc, attr) {
        acc[attr.name] = attr.value;
        return acc;
      }, {});
    };

    var isSupported = function (dom) {
      return dom.style !== undefined && isFunction(dom.style.getPropertyValue);
    };

    var internalSet = function (dom, property, value) {
      if (!isString(value)) {
        domGlobals.console.error('Invalid call to CSS.set. Property ', property, ':: Value ', value, ':: Element ', dom);
        throw new Error('CSS value must be a string: ' + value);
      }
      if (isSupported(dom)) {
        dom.style.setProperty(property, value);
      }
    };
    var set = function (element, property, value) {
      var dom = element.dom();
      internalSet(dom, property, value);
    };

    var clone$1 = function (original, isDeep) {
      return Element.fromDom(original.dom().cloneNode(isDeep));
    };
    var deep = function (original) {
      return clone$1(original, true);
    };
    var shallowAs = function (original, tag) {
      var nu = Element.fromTag(tag);
      var attributes = clone(original);
      setAll(nu, attributes);
      return nu;
    };
    var mutate = function (original, tag) {
      var nu = shallowAs(original, tag);
      before(original, nu);
      var children$1 = children(original);
      append$1(nu, children$1);
      remove(original);
      return nu;
    };

    var joinSegment = function (parent, child) {
      append(parent.item, child.list);
    };
    var joinSegments = function (segments) {
      for (var i = 1; i < segments.length; i++) {
        joinSegment(segments[i - 1], segments[i]);
      }
    };
    var appendSegments = function (head$1, tail) {
      lift2(last(head$1), head(tail), joinSegment);
    };
    var createSegment = function (scope, listType) {
      var segment = {
        list: Element.fromTag(listType, scope),
        item: Element.fromTag('li', scope)
      };
      append(segment.list, segment.item);
      return segment;
    };
    var createSegments = function (scope, entry, size) {
      var segments = [];
      for (var i = 0; i < size; i++) {
        segments.push(createSegment(scope, entry.listType));
      }
      return segments;
    };
    var populateSegments = function (segments, entry) {
      for (var i = 0; i < segments.length - 1; i++) {
        set(segments[i].item, 'list-style-type', 'none');
      }
      last(segments).each(function (segment) {
        setAll(segment.list, entry.listAttributes);
        setAll(segment.item, entry.itemAttributes);
        append$1(segment.item, entry.content);
      });
    };
    var normalizeSegment = function (segment, entry) {
      if (name(segment.list) !== entry.listType) {
        segment.list = mutate(segment.list, entry.listType);
      }
      setAll(segment.list, entry.listAttributes);
    };
    var createItem = function (scope, attr, content) {
      var item = Element.fromTag('li', scope);
      setAll(item, attr);
      append$1(item, content);
      return item;
    };
    var appendItem = function (segment, item) {
      append(segment.list, item);
      segment.item = item;
    };
    var writeShallow = function (scope, cast, entry) {
      var newCast = cast.slice(0, entry.depth);
      last(newCast).each(function (segment) {
        var item = createItem(scope, entry.itemAttributes, entry.content);
        appendItem(segment, item);
        normalizeSegment(segment, entry);
      });
      return newCast;
    };
    var writeDeep = function (scope, cast, entry) {
      var segments = createSegments(scope, entry, entry.depth - cast.length);
      joinSegments(segments);
      populateSegments(segments, entry);
      appendSegments(cast, segments);
      return cast.concat(segments);
    };
    var composeList = function (scope, entries) {
      var cast = foldl(entries, function (cast, entry) {
        return entry.depth > cast.length ? writeDeep(scope, cast, entry) : writeShallow(scope, cast, entry);
      }, []);
      return head(cast).map(function (segment) {
        return segment.list;
      });
    };

    var isList$1 = function (el) {
      return is$1(el, 'OL,UL');
    };
    var hasFirstChildList = function (el) {
      return firstChild(el).map(isList$1).getOr(false);
    };
    var hasLastChildList = function (el) {
      return lastChild(el).map(isList$1).getOr(false);
    };

    var isIndented = function (entry) {
      return entry.depth > 0;
    };
    var isSelected = function (entry) {
      return entry.isSelected;
    };
    var cloneItemContent = function (li) {
      var children$1 = children(li);
      var content = hasLastChildList(li) ? children$1.slice(0, -1) : children$1;
      return map(content, deep);
    };
    var createEntry = function (li, depth, isSelected) {
      return parent(li).filter(isElement).map(function (list) {
        return {
          depth: depth,
          isSelected: isSelected,
          content: cloneItemContent(li),
          itemAttributes: clone(li),
          listAttributes: clone(list),
          listType: name(list)
        };
      });
    };

    var indentEntry = function (indentation, entry) {
      switch (indentation) {
      case 'Indent':
        entry.depth++;
        break;
      case 'Outdent':
        entry.depth--;
        break;
      case 'Flatten':
        entry.depth = 0;
      }
    };

    var hasOwnProperty = Object.prototype.hasOwnProperty;
    var shallow = function (old, nu) {
      return nu;
    };
    var baseMerge = function (merger) {
      return function () {
        var objects = new Array(arguments.length);
        for (var i = 0; i < objects.length; i++) {
          objects[i] = arguments[i];
        }
        if (objects.length === 0) {
          throw new Error('Can\'t merge zero objects');
        }
        var ret = {};
        for (var j = 0; j < objects.length; j++) {
          var curObject = objects[j];
          for (var key in curObject) {
            if (hasOwnProperty.call(curObject, key)) {
              ret[key] = merger(ret[key], curObject[key]);
            }
          }
        }
        return ret;
      };
    };
    var merge = baseMerge(shallow);

    var cloneListProperties = function (target, source) {
      target.listType = source.listType;
      target.listAttributes = merge({}, source.listAttributes);
    };
    var previousSiblingEntry = function (entries, start) {
      var depth = entries[start].depth;
      for (var i = start - 1; i >= 0; i--) {
        if (entries[i].depth === depth) {
          return Option.some(entries[i]);
        }
        if (entries[i].depth < depth) {
          break;
        }
      }
      return Option.none();
    };
    var normalizeEntries = function (entries) {
      each(entries, function (entry, i) {
        previousSiblingEntry(entries, i).each(function (matchingEntry) {
          cloneListProperties(entry, matchingEntry);
        });
      });
    };

    var Cell = function (initial) {
      var value = initial;
      var get = function () {
        return value;
      };
      var set = function (v) {
        value = v;
      };
      var clone = function () {
        return Cell(get());
      };
      return {
        get: get,
        set: set,
        clone: clone
      };
    };

    var parseItem = function (depth, itemSelection, selectionState, item) {
      return firstChild(item).filter(isList$1).fold(function () {
        itemSelection.each(function (selection) {
          if (eq(selection.start, item)) {
            selectionState.set(true);
          }
        });
        var currentItemEntry = createEntry(item, depth, selectionState.get());
        itemSelection.each(function (selection) {
          if (eq(selection.end, item)) {
            selectionState.set(false);
          }
        });
        var childListEntries = lastChild(item).filter(isList$1).map(function (list) {
          return parseList(depth, itemSelection, selectionState, list);
        }).getOr([]);
        return currentItemEntry.toArray().concat(childListEntries);
      }, function (list) {
        return parseList(depth, itemSelection, selectionState, list);
      });
    };
    var parseList = function (depth, itemSelection, selectionState, list) {
      return bind(children(list), function (element) {
        var parser = isList$1(element) ? parseList : parseItem;
        var newDepth = depth + 1;
        return parser(newDepth, itemSelection, selectionState, element);
      });
    };
    var parseLists = function (lists, itemSelection) {
      var selectionState = Cell(false);
      var initialDepth = 0;
      return map(lists, function (list) {
        return {
          sourceList: list,
          entries: parseList(initialDepth, itemSelection, selectionState, list)
        };
      });
    };

    var global$8 = tinymce.util.Tools.resolve('tinymce.Env');

    var createTextBlock = function (editor, contentNode) {
      var dom = editor.dom;
      var blockElements = editor.schema.getBlockElements();
      var fragment = dom.createFragment();
      var node, textBlock, blockName, hasContentNode;
      if (editor.settings.forced_root_block) {
        blockName = editor.settings.forced_root_block;
      }
      if (blockName) {
        textBlock = dom.create(blockName);
        if (textBlock.tagName === editor.settings.forced_root_block) {
          dom.setAttribs(textBlock, editor.settings.forced_root_block_attrs);
        }
        if (!NodeType.isBlock(contentNode.firstChild, blockElements)) {
          fragment.appendChild(textBlock);
        }
      }
      if (contentNode) {
        while (node = contentNode.firstChild) {
          var nodeName = node.nodeName;
          if (!hasContentNode && (nodeName !== 'SPAN' || node.getAttribute('data-mce-type') !== 'bookmark')) {
            hasContentNode = true;
          }
          if (NodeType.isBlock(node, blockElements)) {
            fragment.appendChild(node);
            textBlock = null;
          } else {
            if (blockName) {
              if (!textBlock) {
                textBlock = dom.create(blockName);
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
        fragment.appendChild(dom.create('br'));
      } else {
        if (!hasContentNode && (!global$8.ie || global$8.ie > 10)) {
          textBlock.appendChild(dom.create('br', { 'data-mce-bogus': '1' }));
        }
      }
      return fragment;
    };

    var outdentedComposer = function (editor, entries) {
      return map(entries, function (entry) {
        var content = fromElements(entry.content);
        return Element.fromDom(createTextBlock(editor, content.dom()));
      });
    };
    var indentedComposer = function (editor, entries) {
      normalizeEntries(entries);
      return composeList(editor.contentDocument, entries).toArray();
    };
    var composeEntries = function (editor, entries) {
      return bind(groupBy(entries, isIndented), function (entries) {
        var groupIsIndented = head(entries).map(isIndented).getOr(false);
        return groupIsIndented ? indentedComposer(editor, entries) : outdentedComposer(editor, entries);
      });
    };
    var indentSelectedEntries = function (entries, indentation) {
      each(filter(entries, isSelected), function (entry) {
        return indentEntry(indentation, entry);
      });
    };
    var getItemSelection = function (editor) {
      var selectedListItems = map(Selection.getSelectedListItems(editor), Element.fromDom);
      return lift2(find(selectedListItems, not(hasFirstChildList)), find(reverse(selectedListItems), not(hasFirstChildList)), function (start, end) {
        return {
          start: start,
          end: end
        };
      });
    };
    var listsIndentation = function (editor, lists, indentation) {
      var entrySets = parseLists(lists, getItemSelection(editor));
      each(entrySets, function (entrySet) {
        indentSelectedEntries(entrySet.entries, indentation);
        before$1(entrySet.sourceList, composeEntries(editor, entrySet.entries));
        remove(entrySet.sourceList);
      });
    };

    var DOM$1 = global$6.DOM;
    var splitList = function (editor, ul, li) {
      var tmpRng, fragment, bookmarks, node, newBlock;
      var removeAndKeepBookmarks = function (targetNode) {
        global$5.each(bookmarks, function (node) {
          targetNode.parentNode.insertBefore(node, li.parentNode);
        });
        DOM$1.remove(targetNode);
      };
      bookmarks = DOM$1.select('span[data-mce-type="bookmark"]', ul);
      newBlock = createTextBlock(editor, li);
      tmpRng = DOM$1.createRng();
      tmpRng.setStartAfter(li);
      tmpRng.setEndAfter(ul);
      fragment = tmpRng.extractContents();
      for (node = fragment.firstChild; node; node = node.firstChild) {
        if (node.nodeName === 'LI' && editor.dom.isEmpty(node)) {
          DOM$1.remove(node);
          break;
        }
      }
      if (!editor.dom.isEmpty(fragment)) {
        DOM$1.insertAfter(fragment, ul);
      }
      DOM$1.insertAfter(newBlock, ul);
      if (NodeType.isEmpty(editor.dom, li.parentNode)) {
        removeAndKeepBookmarks(li.parentNode);
      }
      DOM$1.remove(li);
      if (NodeType.isEmpty(editor.dom, ul)) {
        DOM$1.remove(ul);
      }
    };
    var SplitList = { splitList: splitList };

    var outdentDlItem = function (editor, item) {
      if (is$1(item, 'dd')) {
        mutate(item, 'dt');
      } else if (is$1(item, 'dt')) {
        parent(item).each(function (dl) {
          return SplitList.splitList(editor, dl.dom(), item.dom());
        });
      }
    };
    var indentDlItem = function (item) {
      if (is$1(item, 'dt')) {
        mutate(item, 'dd');
      }
    };
    var dlIndentation = function (editor, indentation, dlItems) {
      if (indentation === 'Indent') {
        each(dlItems, indentDlItem);
      } else {
        each(dlItems, function (item) {
          return outdentDlItem(editor, item);
        });
      }
    };

    var selectionIndentation = function (editor, indentation) {
      var lists = map(Selection.getSelectedListRoots(editor), Element.fromDom);
      var dlItems = map(Selection.getSelectedDlItems(editor), Element.fromDom);
      var isHandled = false;
      if (lists.length || dlItems.length) {
        var bookmark = editor.selection.getBookmark();
        listsIndentation(editor, lists, indentation);
        dlIndentation(editor, indentation, dlItems);
        editor.selection.moveToBookmark(bookmark);
        editor.selection.setRng(Range.normalizeRange(editor.selection.getRng()));
        editor.nodeChanged();
        isHandled = true;
      }
      return isHandled;
    };
    var indentListSelection = function (editor) {
      return selectionIndentation(editor, 'Indent');
    };
    var outdentListSelection = function (editor) {
      return selectionIndentation(editor, 'Outdent');
    };
    var flattenListSelection = function (editor) {
      return selectionIndentation(editor, 'Flatten');
    };

    var updateListStyle = function (dom, el, detail) {
      var type = detail['list-style-type'] ? detail['list-style-type'] : null;
      dom.setStyle(el, 'list-style-type', type);
    };
    var setAttribs = function (elm, attrs) {
      global$5.each(attrs, function (value, key) {
        elm.setAttribute(key, value);
      });
    };
    var updateListAttrs = function (dom, el, detail) {
      setAttribs(el, detail['list-attributes']);
      global$5.each(dom.select('li', el), function (li) {
        setAttribs(li, detail['list-item-attributes']);
      });
    };
    var updateListWithDetails = function (dom, el, detail) {
      updateListStyle(dom, el, detail);
      updateListAttrs(dom, el, detail);
    };
    var removeStyles = function (dom, element, styles) {
      global$5.each(styles, function (style) {
        var _a;
        return dom.setStyle(element, (_a = {}, _a[style] = '', _a));
      });
    };
    var getEndPointNode = function (editor, rng, start, root) {
      var container, offset;
      container = rng[start ? 'startContainer' : 'endContainer'];
      offset = rng[start ? 'startOffset' : 'endOffset'];
      if (container.nodeType === 1) {
        container = container.childNodes[Math.min(offset, container.childNodes.length - 1)] || container;
      }
      if (!start && NodeType.isBr(container.nextSibling)) {
        container = container.nextSibling;
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
    var getSelectedTextBlocks = function (editor, rng, root) {
      var textBlocks = [], dom = editor.dom;
      var startNode = getEndPointNode(editor, rng, true, root);
      var endNode = getEndPointNode(editor, rng, false, root);
      var block;
      var siblings = [];
      for (var node = startNode; node; node = node.nextSibling) {
        siblings.push(node);
        if (node === endNode) {
          break;
        }
      }
      global$5.each(siblings, function (node) {
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
        if (global$4.isBookmarkNode(node)) {
          if (NodeType.isTextBlock(editor, nextSibling) || !nextSibling && node.parentNode === root) {
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
    var hasCompatibleStyle = function (dom, sib, detail) {
      var sibStyle = dom.getStyle(sib, 'list-style-type');
      var detailStyle = detail ? detail['list-style-type'] : '';
      detailStyle = detailStyle === null ? '' : detailStyle;
      return sibStyle === detailStyle;
    };
    var applyList = function (editor, listName, detail) {
      if (detail === void 0) {
        detail = {};
      }
      var rng = editor.selection.getRng(true);
      var bookmark;
      var listItemName = 'LI';
      var root = Selection.getClosestListRootElm(editor, editor.selection.getStart(true));
      var dom = editor.dom;
      if (dom.getContentEditable(editor.selection.getNode()) === 'false') {
        return;
      }
      listName = listName.toUpperCase();
      if (listName === 'DL') {
        listItemName = 'DT';
      }
      bookmark = Bookmark.createBookmark(rng);
      global$5.each(getSelectedTextBlocks(editor, rng, root), function (block) {
        var listBlock, sibling;
        sibling = block.previousSibling;
        if (sibling && NodeType.isListNode(sibling) && sibling.nodeName === listName && hasCompatibleStyle(dom, sibling, detail)) {
          listBlock = sibling;
          block = dom.rename(block, listItemName);
          sibling.appendChild(block);
        } else {
          listBlock = dom.create(listName);
          block.parentNode.insertBefore(listBlock, block);
          listBlock.appendChild(block);
          block = dom.rename(block, listItemName);
        }
        removeStyles(dom, block, [
          'margin',
          'margin-right',
          'margin-bottom',
          'margin-left',
          'margin-top',
          'padding',
          'padding-right',
          'padding-bottom',
          'padding-left',
          'padding-top'
        ]);
        updateListWithDetails(dom, listBlock, detail);
        mergeWithAdjacentLists(editor.dom, listBlock);
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
        while (node = sibling.firstChild) {
          listBlock.appendChild(node);
        }
        dom.remove(sibling);
      }
      sibling = listBlock.previousSibling;
      if (shouldMerge(dom, listBlock, sibling)) {
        while (node = sibling.lastChild) {
          listBlock.insertBefore(node, listBlock.firstChild);
        }
        dom.remove(sibling);
      }
    };
    var updateList = function (dom, list, listName, detail) {
      if (list.nodeName !== listName) {
        var newList = dom.rename(list, listName);
        updateListWithDetails(dom, newList, detail);
      } else {
        updateListWithDetails(dom, list, detail);
      }
    };
    var toggleMultipleLists = function (editor, parentList, lists, listName, detail) {
      if (parentList.nodeName === listName && !hasListStyleDetail(detail)) {
        flattenListSelection(editor);
      } else {
        var bookmark = Bookmark.createBookmark(editor.selection.getRng(true));
        global$5.each([parentList].concat(lists), function (elm) {
          updateList(editor.dom, elm, listName, detail);
        });
        editor.selection.setRng(Bookmark.resolveBookmark(bookmark));
      }
    };
    var hasListStyleDetail = function (detail) {
      return 'list-style-type' in detail;
    };
    var toggleSingleList = function (editor, parentList, listName, detail) {
      if (parentList === editor.getBody()) {
        return;
      }
      if (parentList) {
        if (parentList.nodeName === listName && !hasListStyleDetail(detail)) {
          flattenListSelection(editor);
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
    var toggleList = function (editor, listName, detail) {
      var parentList = Selection.getParentList(editor);
      var selectedSubLists = Selection.getSelectedSubLists(editor);
      detail = detail ? detail : {};
      if (parentList && selectedSubLists.length > 0) {
        toggleMultipleLists(editor, parentList, selectedSubLists, listName, detail);
      } else {
        toggleSingleList(editor, parentList, listName, detail);
      }
    };
    var ToggleList = {
      toggleList: toggleList,
      mergeWithAdjacentLists: mergeWithAdjacentLists
    };

    var DOM$2 = global$6.DOM;
    var normalizeList = function (dom, ul) {
      var sibling;
      var parentNode = ul.parentNode;
      if (parentNode.nodeName === 'LI' && parentNode.firstChild === ul) {
        sibling = parentNode.previousSibling;
        if (sibling && sibling.nodeName === 'LI') {
          sibling.appendChild(ul);
          if (NodeType.isEmpty(dom, parentNode)) {
            DOM$2.remove(parentNode);
          }
        } else {
          DOM$2.setStyle(parentNode, 'listStyleType', 'none');
        }
      }
      if (NodeType.isListNode(parentNode)) {
        sibling = parentNode.previousSibling;
        if (sibling && sibling.nodeName === 'LI') {
          sibling.appendChild(ul);
        }
      }
    };
    var normalizeLists = function (dom, element) {
      global$5.each(global$5.grep(dom.select('ol,ul', element)), function (ul) {
        normalizeList(dom, ul);
      });
    };
    var NormalizeLists = {
      normalizeList: normalizeList,
      normalizeLists: normalizeLists
    };

    var findNextCaretContainer = function (editor, rng, isForward, root) {
      var node = rng.startContainer;
      var offset = rng.startOffset;
      var nonEmptyBlocks, walker;
      if (node.nodeType === 3 && (isForward ? offset < node.data.length : offset > 0)) {
        return node;
      }
      nonEmptyBlocks = editor.schema.getNonEmptyElements();
      if (node.nodeType === 1) {
        node = global$1.getNode(node, offset);
      }
      walker = new global$2(node, root);
      if (isForward) {
        if (NodeType.isBogusBr(editor.dom, node)) {
          walker.next();
        }
      }
      while (node = walker[isForward ? 'next' : 'prev2']()) {
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
    var hasOnlyOneBlockChild = function (dom, elm) {
      var childNodes = elm.childNodes;
      return childNodes.length === 1 && !NodeType.isListNode(childNodes[0]) && dom.isBlock(childNodes[0]);
    };
    var unwrapSingleBlockChild = function (dom, elm) {
      if (hasOnlyOneBlockChild(dom, elm)) {
        dom.remove(elm.firstChild, true);
      }
    };
    var moveChildren = function (dom, fromElm, toElm) {
      var node, targetElm;
      targetElm = hasOnlyOneBlockChild(dom, toElm) ? toElm.firstChild : toElm;
      unwrapSingleBlockChild(dom, fromElm);
      if (!NodeType.isEmpty(dom, fromElm, true)) {
        while (node = fromElm.firstChild) {
          targetElm.appendChild(node);
        }
      }
    };
    var mergeLiElements = function (dom, fromElm, toElm) {
      var node, listNode;
      var ul = fromElm.parentNode;
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
      moveChildren(dom, fromElm, toElm);
      if (listNode) {
        toElm.appendChild(listNode);
      }
      var contains = contains$1(Element.fromDom(toElm), Element.fromDom(fromElm));
      var nestedLists = contains ? dom.getParents(fromElm, NodeType.isListNode, toElm) : [];
      dom.remove(fromElm);
      each(nestedLists, function (list) {
        if (NodeType.isEmpty(dom, list) && list !== dom.getRoot()) {
          dom.remove(list);
        }
      });
    };
    var mergeIntoEmptyLi = function (editor, fromLi, toLi) {
      editor.dom.$(toLi).empty();
      mergeLiElements(editor.dom, fromLi, toLi);
      editor.selection.setCursorLocation(toLi);
    };
    var mergeForward = function (editor, rng, fromLi, toLi) {
      var dom = editor.dom;
      if (dom.isEmpty(toLi)) {
        mergeIntoEmptyLi(editor, fromLi, toLi);
      } else {
        var bookmark = Bookmark.createBookmark(rng);
        mergeLiElements(dom, fromLi, toLi);
        editor.selection.setRng(Bookmark.resolveBookmark(bookmark));
      }
    };
    var mergeBackward = function (editor, rng, fromLi, toLi) {
      var bookmark = Bookmark.createBookmark(rng);
      mergeLiElements(editor.dom, fromLi, toLi);
      var resolvedBookmark = Bookmark.resolveBookmark(bookmark);
      editor.selection.setRng(resolvedBookmark);
    };
    var backspaceDeleteFromListToListCaret = function (editor, isForward) {
      var dom = editor.dom, selection = editor.selection;
      var selectionStartElm = selection.getStart();
      var root = Selection.getClosestListRootElm(editor, selectionStartElm);
      var li = dom.getParent(selection.getStart(), 'LI', root);
      var ul, rng, otherLi;
      if (li) {
        ul = li.parentNode;
        if (ul === editor.getBody() && NodeType.isEmpty(dom, ul)) {
          return true;
        }
        rng = Range.normalizeRange(selection.getRng(true));
        otherLi = dom.getParent(findNextCaretContainer(editor, rng, isForward, root), 'LI', root);
        if (otherLi && otherLi !== li) {
          if (isForward) {
            mergeForward(editor, rng, otherLi, li);
          } else {
            mergeBackward(editor, rng, li, otherLi);
          }
          return true;
        } else if (!otherLi) {
          if (!isForward) {
            flattenListSelection(editor);
            return true;
          }
        }
      }
      return false;
    };
    var removeBlock = function (dom, block, root) {
      var parentBlock = dom.getParent(block.parentNode, dom.isBlock, root);
      dom.remove(block);
      if (parentBlock && dom.isEmpty(parentBlock)) {
        dom.remove(parentBlock);
      }
    };
    var backspaceDeleteIntoListCaret = function (editor, isForward) {
      var dom = editor.dom;
      var selectionStartElm = editor.selection.getStart();
      var root = Selection.getClosestListRootElm(editor, selectionStartElm);
      var block = dom.getParent(selectionStartElm, dom.isBlock, root);
      if (block && dom.isEmpty(block)) {
        var rng = Range.normalizeRange(editor.selection.getRng(true));
        var otherLi_1 = dom.getParent(findNextCaretContainer(editor, rng, isForward, root), 'LI', root);
        if (otherLi_1) {
          editor.undoManager.transact(function () {
            removeBlock(dom, block, root);
            ToggleList.mergeWithAdjacentLists(dom, otherLi_1.parentNode);
            editor.selection.select(otherLi_1, true);
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
      var selectionStartElm = editor.selection.getStart();
      var root = Selection.getClosestListRootElm(editor, selectionStartElm);
      var startListParent = editor.dom.getParent(selectionStartElm, 'LI,DT,DD', root);
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
        if (e.keyCode === global$3.BACKSPACE) {
          if (backspaceDelete(editor, false)) {
            e.preventDefault();
          }
        } else if (e.keyCode === global$3.DELETE) {
          if (backspaceDelete(editor, true)) {
            e.preventDefault();
          }
        }
      });
    };
    var Delete = {
      setup: setup,
      backspaceDelete: backspaceDelete
    };

    var get = function (editor) {
      return {
        backspaceDelete: function (isForward) {
          Delete.backspaceDelete(editor, isForward);
        }
      };
    };
    var Api = { get: get };

    var queryListCommandState = function (editor, listName) {
      return function () {
        var parentList = editor.dom.getParent(editor.selection.getStart(), 'UL,OL,DL');
        return parentList && parentList.nodeName === listName;
      };
    };
    var register = function (editor) {
      editor.on('BeforeExecCommand', function (e) {
        var cmd = e.command.toLowerCase();
        if (cmd === 'indent') {
          indentListSelection(editor);
        } else if (cmd === 'outdent') {
          outdentListSelection(editor);
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
      editor.addCommand('RemoveList', function () {
        flattenListSelection(editor);
      });
      editor.addQueryStateHandler('InsertUnorderedList', queryListCommandState(editor, 'UL'));
      editor.addQueryStateHandler('InsertOrderedList', queryListCommandState(editor, 'OL'));
      editor.addQueryStateHandler('InsertDefinitionList', queryListCommandState(editor, 'DL'));
    };
    var Commands = { register: register };

    var shouldIndentOnTab = function (editor) {
      return editor.getParam('lists_indent_on_tab', true);
    };
    var Settings = { shouldIndentOnTab: shouldIndentOnTab };

    var setupTabKey = function (editor) {
      editor.on('keydown', function (e) {
        if (e.keyCode !== global$3.TAB || global$3.metaKeyPressed(e)) {
          return;
        }
        editor.undoManager.transact(function () {
          if (e.shiftKey ? outdentListSelection(editor) : indentListSelection(editor)) {
            e.preventDefault();
          }
        });
      });
    };
    var setup$1 = function (editor) {
      if (Settings.shouldIndentOnTab(editor)) {
        setupTabKey(editor);
      }
      Delete.setup(editor);
    };
    var Keyboard = { setup: setup$1 };

    var findIndex = function (list, predicate) {
      for (var index = 0; index < list.length; index++) {
        var element = list[index];
        if (predicate(element)) {
          return index;
        }
      }
      return -1;
    };
    var listState = function (editor, listName) {
      return function (e) {
        var ctrl = e.control;
        editor.on('NodeChange', function (e) {
          var tableCellIndex = findIndex(e.parents, NodeType.isTableCellNode);
          var parents = tableCellIndex !== -1 ? e.parents.slice(0, tableCellIndex) : e.parents;
          var lists = global$5.grep(parents, NodeType.isListNode);
          ctrl.active(lists.length > 0 && lists[0].nodeName === listName);
        });
      };
    };
    var register$1 = function (editor) {
      var hasPlugin = function (editor, plugin) {
        var plugins = editor.settings.plugins ? editor.settings.plugins : '';
        return global$5.inArray(plugins.split(/[ ,]/), plugin) !== -1;
      };
      if (!hasPlugin(editor, 'advlist')) {
        editor.addButton('numlist', {
          active: false,
          title: 'Numbered list',
          cmd: 'InsertOrderedList',
          onPostRender: listState(editor, 'OL')
        });
        editor.addButton('bullist', {
          active: false,
          title: 'Bullet list',
          cmd: 'InsertUnorderedList',
          onPostRender: listState(editor, 'UL')
        });
      }
      editor.addButton('indent', {
        icon: 'indent',
        title: 'Increase indent',
        cmd: 'Indent'
      });
    };
    var Buttons = { register: register$1 };

    global.add('lists', function (editor) {
      Keyboard.setup(editor);
      Buttons.register(editor);
      Commands.register(editor);
      return Api.get(editor);
    });
    function Plugin () {
    }

    return Plugin;

}(window));
})();
