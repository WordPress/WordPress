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
["tinymce/inlite/Theme","global!tinymce.ThemeManager","global!tinymce.util.Delay","tinymce/inlite/ui/Panel","tinymce/inlite/ui/Buttons","tinymce/inlite/core/SkinLoader","tinymce/inlite/core/SelectionMatcher","tinymce/inlite/core/ElementMatcher","tinymce/inlite/core/Matcher","tinymce/inlite/alien/Arr","tinymce/inlite/core/PredicateId","global!tinymce.util.Tools","global!tinymce.ui.Factory","global!tinymce.DOM","tinymce/inlite/ui/Toolbar","tinymce/inlite/ui/Forms","tinymce/inlite/core/Measure","tinymce/inlite/core/Layout","tinymce/inlite/file/Conversions","tinymce/inlite/file/Picker","tinymce/inlite/core/Actions","global!tinymce.EditorManager","global!tinymce.util.Promise","tinymce/inlite/alien/Uuid","tinymce/inlite/alien/Unlink","tinymce/inlite/core/UrlType","global!tinymce.geom.Rect","tinymce/inlite/core/Convert","tinymce/inlite/alien/Bookmark","global!tinymce.dom.TreeWalker","global!tinymce.dom.RangeUtils"]
jsc*/
defineGlobal("global!tinymce.ThemeManager", tinymce.ThemeManager);
defineGlobal("global!tinymce.util.Delay", tinymce.util.Delay);
defineGlobal("global!tinymce.util.Tools", tinymce.util.Tools);
defineGlobal("global!tinymce.ui.Factory", tinymce.ui.Factory);
defineGlobal("global!tinymce.DOM", tinymce.DOM);
/**
 * Toolbar.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/ui/Toolbar', [
	'global!tinymce.util.Tools',
	'global!tinymce.ui.Factory'
], function (Tools, Factory) {
	var setActiveItem = function (item, name) {
		return function(state, args) {
			var nodeName, i = args.parents.length;

			while (i--) {
				nodeName = args.parents[i].nodeName;
				if (nodeName == 'OL' || nodeName == 'UL') {
					break;
				}
			}

			item.active(state && nodeName == name);
		};
	};

	var getSelectorStateResult = function (itemName, item) {
		var result = function (selector, handler) {
			return {
				selector: selector,
				handler: handler
			};
		};

		var activeHandler = function(state) {
			item.active(state);
		};

		var disabledHandler = function (state) {
			item.disabled(state);
		};

		if (itemName == 'bullist') {
			return result('ul > li', setActiveItem(item, 'UL'));
		}

		if (itemName == 'numlist') {
			return result('ol > li', setActiveItem(item, 'OL'));
		}

		if (item.settings.stateSelector) {
			return result(item.settings.stateSelector, activeHandler);
		}

		if (item.settings.disabledStateSelector) {
			return result(item.settings.disabledStateSelector, disabledHandler);
		}

		return null;
	};

	var bindSelectorChanged = function (editor, itemName, item) {
		return function () {
			var result = getSelectorStateResult(itemName, item);
			if (result !== null) {
				editor.selection.selectorChanged(result.selector, result.handler);
			}
		};
	};

	var create = function (editor, name, items) {
		var toolbarItems = [], buttonGroup;

		if (!items) {
			return;
		}

		Tools.each(items.split(/[ ,]/), function(item) {
			var itemName;

			if (item == '|') {
				buttonGroup = null;
			} else {
				if (Factory.has(item)) {
					item = {type: item};
					toolbarItems.push(item);
					buttonGroup = null;
				} else {
					if (!buttonGroup) {
						buttonGroup = {type: 'buttongroup', items: []};
						toolbarItems.push(buttonGroup);
					}

					if (editor.buttons[item]) {
						itemName = item;
						item = editor.buttons[itemName];

						if (typeof item == 'function') {
							item = item();
						}

						item.type = item.type || 'button';

						item = Factory.create(item);
						item.on('postRender', bindSelectorChanged(editor, itemName, item));
						buttonGroup.items.push(item);
					}
				}
			}
		});

		return Factory.create({
			type: 'toolbar',
			layout: 'flow',
			name: name,
			items: toolbarItems
		});
	};

	return {
		create: create
	};
});

defineGlobal("global!tinymce.util.Promise", tinymce.util.Promise);
/**
 * Uuid.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * Generates unique ids this is the same as in core but since
 * it's not exposed as a global we can't access it.
 */
define("tinymce/inlite/alien/Uuid", [
], function() {
	var count = 0;

	var seed = function () {
		var rnd = function () {
			return Math.round(Math.random() * 0xFFFFFFFF).toString(36);
		};

		return 's' + Date.now().toString(36) + rnd() + rnd() + rnd();
	};

	var uuid = function (prefix) {
		return prefix + (count++) + seed();
	};

	return {
		uuid: uuid
	};
});

/**
 * Bookmark.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/alien/Bookmark', [
], function () {
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
	var create = function (dom, rng) {
		var bookmark = {};

		function setupEndPoint(start) {
			var offsetNode, container, offset;

			container = rng[start ? 'startContainer' : 'endContainer'];
			offset = rng[start ? 'startOffset' : 'endOffset'];

			if (container.nodeType == 1) {
				offsetNode = dom.create('span', {'data-mce-type': 'bookmark'});

				if (container.hasChildNodes()) {
					offset = Math.min(offset, container.childNodes.length - 1);

					if (start) {
						container.insertBefore(offsetNode, container.childNodes[offset]);
					} else {
						dom.insertAfter(offsetNode, container.childNodes[offset]);
					}
				} else {
					container.appendChild(offsetNode);
				}

				container = offsetNode;
				offset = 0;
			}

			bookmark[start ? 'startContainer' : 'endContainer'] = container;
			bookmark[start ? 'startOffset' : 'endOffset'] = offset;
		}

		setupEndPoint(true);

		if (!rng.collapsed) {
			setupEndPoint();
		}

		return bookmark;
	};

	/**
	 * Moves the selection to the current bookmark and removes any selection container wrappers.
	 *
	 * @param {Object} bookmark Bookmark object to move selection to.
	 */
	var resolve = function (dom, bookmark) {
		function restoreEndPoint(start) {
			var container, offset, node;

			function nodeIndex(container) {
				var node = container.parentNode.firstChild, idx = 0;

				while (node) {
					if (node == container) {
						return idx;
					}

					// Skip data-mce-type=bookmark nodes
					if (node.nodeType != 1 || node.getAttribute('data-mce-type') != 'bookmark') {
						idx++;
					}

					node = node.nextSibling;
				}

				return -1;
			}

			container = node = bookmark[start ? 'startContainer' : 'endContainer'];
			offset = bookmark[start ? 'startOffset' : 'endOffset'];

			if (!container) {
				return;
			}

			if (container.nodeType == 1) {
				offset = nodeIndex(container);
				container = container.parentNode;
				dom.remove(node);
			}

			bookmark[start ? 'startContainer' : 'endContainer'] = container;
			bookmark[start ? 'startOffset' : 'endOffset'] = offset;
		}

		restoreEndPoint(true);
		restoreEndPoint();

		var rng = dom.createRng();

		rng.setStart(bookmark.startContainer, bookmark.startOffset);

		if (bookmark.endContainer) {
			rng.setEnd(bookmark.endContainer, bookmark.endOffset);
		}

		return rng;
	};

	return {
		create: create,
		resolve: resolve
	};
});



defineGlobal("global!tinymce.dom.TreeWalker", tinymce.dom.TreeWalker);
defineGlobal("global!tinymce.dom.RangeUtils", tinymce.dom.RangeUtils);
/**
 * Unlink.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * Unlink implementation that doesn't leave partial links for example it would produce:
 *  a[b<a href="x">c]d</a>e -> a[bc]de
 * instead of:
 *  a[b<a href="x">c]d</a>e -> a[bc]<a href="x">d</a>e
 */
define("tinymce/inlite/alien/Unlink", [
	'tinymce/inlite/alien/Bookmark',
	'global!tinymce.util.Tools',
	'global!tinymce.dom.TreeWalker',
	'global!tinymce.dom.RangeUtils'
], function (Bookmark, Tools, TreeWalker, RangeUtils) {
	var getSelectedElements = function (rootElm, startNode, endNode) {
		var walker, node, elms = [];

		walker = new TreeWalker(startNode, rootElm);
		for (node = startNode; node; node = walker.next()) {
			if (node.nodeType === 1) {
				elms.push(node);
			}

			if (node === endNode) {
				break;
			}
		}

		return elms;
	};

	var unwrapElements = function (editor, elms) {
		var bookmark, dom, selection;

		dom = editor.dom;
		selection = editor.selection;
		bookmark = Bookmark.create(dom, selection.getRng());

		Tools.each(elms, function (elm) {
			editor.dom.remove(elm, true);
		});

		selection.setRng(Bookmark.resolve(dom, bookmark));
	};

	var isLink = function (elm) {
		return elm.nodeName === 'A' && elm.hasAttribute('href');
	};

	var getParentAnchorOrSelf = function (dom, elm) {
		var anchorElm = dom.getParent(elm, isLink);
		return anchorElm ? anchorElm : elm;
	};

	var getSelectedAnchors = function (editor) {
		var startElm, endElm, rootElm, anchorElms, selection, dom, rng;

		selection = editor.selection;
		dom = editor.dom;
		rng = selection.getRng();
		startElm = getParentAnchorOrSelf(dom, RangeUtils.getNode(rng.startContainer, rng.startOffset));
		endElm = RangeUtils.getNode(rng.endContainer, rng.endOffset);
		rootElm = editor.getBody();
		anchorElms = Tools.grep(getSelectedElements(rootElm, startElm, endElm), isLink);

		return anchorElms;
	};

	var unlinkSelection = function (editor) {
		unwrapElements(editor, getSelectedAnchors(editor));
	};

	return {
		unlinkSelection: unlinkSelection
	};
});

/**
 * Actions.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/core/Actions', [
	'tinymce/inlite/alien/Uuid',
	'tinymce/inlite/alien/Unlink'
], function (Uuid, Unlink) {
	var createTableHtml = function (cols, rows) {
		var x, y, html;

		html = '<table data-mce-id="mce" style="width: 100%">';
		html += '<tbody>';

		for (y = 0; y < rows; y++) {
			html += '<tr>';

			for (x = 0; x < cols; x++) {
				html += '<td><br></td>';
			}

			html += '</tr>';
		}

		html += '</tbody>';
		html += '</table>';

		return html;
	};

	var getInsertedElement = function (editor) {
		var elms = editor.dom.select('*[data-mce-id]');
		return elms[0];
	};

	var insertTable = function (editor, cols, rows) {
		editor.undoManager.transact(function () {
			var tableElm, cellElm;

			editor.insertContent(createTableHtml(cols, rows));

			tableElm = getInsertedElement(editor);
			tableElm.removeAttribute('data-mce-id');
			cellElm = editor.dom.select('td,th', tableElm);
			editor.selection.setCursorLocation(cellElm[0], 0);
		});
	};

	var formatBlock = function (editor, formatName) {
		editor.execCommand('FormatBlock', false, formatName);
	};

	var insertBlob = function (editor, base64, blob) {
		var blobCache, blobInfo;

		blobCache = editor.editorUpload.blobCache;
		blobInfo = blobCache.create(Uuid.uuid('mceu'), blob, base64);
		blobCache.add(blobInfo);

		editor.insertContent(editor.dom.createHTML('img', {src: blobInfo.blobUri()}));
	};

	var collapseSelectionToEnd = function (editor) {
		editor.selection.collapse(false);
	};

	var unlink = function (editor) {
		editor.focus();
		Unlink.unlinkSelection(editor);
		collapseSelectionToEnd(editor);
	};

	var changeHref = function (editor, elm, url) {
		editor.focus();
		editor.dom.setAttrib(elm, 'href', url);
		collapseSelectionToEnd(editor);
	};

	var insertLink = function (editor, url) {
		editor.execCommand('mceInsertLink', false, {href: url});
		collapseSelectionToEnd(editor);
	};

	var updateOrInsertLink = function (editor, url) {
		var elm = editor.dom.getParent(editor.selection.getStart(), 'a[href]');
		elm ? changeHref(editor, elm, url) : insertLink(editor, url);
	};

	var createLink = function (editor, url) {
		url.trim().length === 0 ? unlink(editor) : updateOrInsertLink(editor, url);
	};

	return {
		insertTable: insertTable,
		formatBlock: formatBlock,
		insertBlob: insertBlob,
		createLink: createLink,
		unlink: unlink
	};
});

/**
 * UrlType.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/core/UrlType', [
], function () {
	var isDomainLike = function (href) {
		return /^www\.|\.(com|org|edu|gov|uk|net|ca|de|jp|fr|au|us|ru|ch|it|nl|se|no|es|mil)$/i.test(href.trim());
	};

	return {
		isDomainLike: isDomainLike
	};
});



/**
 * Forms.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/ui/Forms', [
	'global!tinymce.util.Tools',
	'global!tinymce.ui.Factory',
	'global!tinymce.util.Promise',
	'tinymce/inlite/core/Actions',
	'tinymce/inlite/core/UrlType'
], function (Tools, Factory, Promise, Actions, UrlType) {
	var focusFirstTextBox = function (form) {
		form.find('textbox').eq(0).each(function (ctrl) {
			ctrl.focus();
		});
	};

	var createForm = function (name, spec) {
		var form = Factory.create(
			Tools.extend({
				type: 'form',
				layout: 'flex',
				direction: 'row',
				padding: 5,
				name: name,
				spacing: 3
			}, spec)
		);

		form.on('show', function () {
			focusFirstTextBox(form);
		});

		return form;
	};

	var toggleVisibility = function (ctrl, state) {
		return state ? ctrl.show() : ctrl.hide();
	};

	var askAboutPrefix = function (editor, href) {
		return new Promise(function (resolve) {
			editor.windowManager.confirm(
				'The URL you entered seems to be an external link. Do you want to add the required http:// prefix?',
				function (result) {
					var output = result === true ? 'http://' + href : href;
					resolve(output);
				}
			);
		});
	};

	var convertLinkToAbsolute = function (editor, href) {
		return UrlType.isDomainLike(href) ? askAboutPrefix(editor, href) : Promise.resolve(href);
	};

	var createQuickLinkForm = function (editor, hide) {
		var unlink = function () {
			editor.focus();
			Actions.unlink(editor);
			hide();
		};

		return createForm('quicklink', {
			items: [
				{type: 'button', name: 'unlink', icon: 'unlink', onclick: unlink, tooltip: 'Remove link'},
				{type: 'textbox', name: 'linkurl', placeholder: 'Paste or type a link'},
				{type: 'button', icon: 'checkmark', subtype: 'primary', tooltip: 'Ok', onclick: 'submit'}
			],
			onshow: function () {
				var elm, linkurl = '';

				elm = editor.dom.getParent(editor.selection.getStart(), 'a[href]');
				if (elm) {
					linkurl = editor.dom.getAttrib(elm, 'href');
				}

				this.fromJSON({
					linkurl: linkurl
				});

				toggleVisibility(this.find('#unlink'), elm);
			},
			onsubmit: function (e) {
				convertLinkToAbsolute(editor, e.data.linkurl).then(function (url) {
					Actions.createLink(editor, url);
					hide();
				});
			}
		});
	};

	return {
		createQuickLinkForm: createQuickLinkForm
	};
});

defineGlobal("global!tinymce.geom.Rect", tinymce.geom.Rect);
/**
 * Convert.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/core/Convert', [
], function () {
	var fromClientRect = function (clientRect) {
		return {
			x: clientRect.left,
			y: clientRect.top,
			w: clientRect.width,
			h: clientRect.height
		};
	};

	var toClientRect = function (geomRect) {
		return {
			left: geomRect.x,
			top: geomRect.y,
			width: geomRect.w,
			height: geomRect.h,
			right: geomRect.x + geomRect.w,
			bottom: geomRect.y + geomRect.h
		};
	};

	return {
		fromClientRect: fromClientRect,
		toClientRect: toClientRect
	};
});

/**
 * Measure.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/core/Measure', [
	'global!tinymce.DOM',
	'global!tinymce.geom.Rect',
	'tinymce/inlite/core/Convert'
], function (DOM, Rect, Convert) {
	var toAbsolute = function (rect) {
		var vp = DOM.getViewPort();

		return {
			x: rect.x + vp.x,
			y: rect.y + vp.y,
			w: rect.w,
			h: rect.h
		};
	};

	var getElementRect = function (editor, elm) {
		var pos, targetRect, root;

		pos = DOM.getPos(editor.getContentAreaContainer());
		targetRect = editor.dom.getRect(elm);
		root = editor.dom.getRoot();

		// Adjust targetPos for scrolling in the editor
		if (root.nodeName == 'BODY') {
			targetRect.x -= root.ownerDocument.documentElement.scrollLeft || root.scrollLeft;
			targetRect.y -= root.ownerDocument.documentElement.scrollTop || root.scrollTop;
		}

		targetRect.x += pos.x;
		targetRect.y += pos.y;

		// We need to use these instead of the rect values since the style
		// size properites might not be the same as the real size for a table
		targetRect.w = elm.clientWidth > 0 ? elm.clientWidth : elm.offsetWidth;
		targetRect.h = elm.clientHeight > 0 ? elm.clientHeight : elm.offsetHeight;

		return targetRect;
	};

	var getPageAreaRect = function (editor) {
		return DOM.getRect(editor.getElement().ownerDocument.body);
	};

	var getContentAreaRect = function (editor) {
		return toAbsolute(DOM.getRect(editor.getContentAreaContainer() || editor.getBody()));
	};

	var getSelectionRect = function (editor) {
		var clientRect = editor.selection.getBoundingClientRect();
		return clientRect ? toAbsolute(Convert.fromClientRect(clientRect)) : null;
	};

	return {
		getElementRect: getElementRect,
		getPageAreaRect: getPageAreaRect,
		getContentAreaRect: getContentAreaRect,
		getSelectionRect: getSelectionRect
	};
});

/**
 * Layout.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/core/Layout', [
	'global!tinymce.geom.Rect',
	'tinymce/inlite/core/Convert'
], function (Rect, Convert) {
	var result = function (rect, position) {
		return {
			rect: rect,
			position: position
		};
	};

	var moveTo = function (rect, toRect) {
		return {x: toRect.x, y: toRect.y, w: rect.w, h: rect.h};
	};

	var calcByPositions = function (testPositions1, testPositions2, targetRect, contentAreaRect, panelRect) {
		var relPos, relRect, outputPanelRect;

		relPos = Rect.findBestRelativePosition(panelRect, targetRect, contentAreaRect, testPositions1);
		targetRect = Rect.clamp(targetRect, contentAreaRect);

		if (relPos) {
			relRect = Rect.relativePosition(panelRect, targetRect, relPos);
			outputPanelRect = moveTo(panelRect, relRect);
			return result(outputPanelRect, relPos);
		}

		targetRect = Rect.intersect(contentAreaRect, targetRect);
		if (targetRect) {
			relPos = Rect.findBestRelativePosition(panelRect, targetRect, contentAreaRect, testPositions2);
			if (relPos) {
				relRect = Rect.relativePosition(panelRect, targetRect, relPos);
				outputPanelRect = moveTo(panelRect, relRect);
				return result(outputPanelRect, relPos);
			}

			outputPanelRect = moveTo(panelRect, targetRect);
			return result(outputPanelRect, relPos);
		}

		return null;
	};

	var calcInsert = function (targetRect, contentAreaRect, panelRect) {
		return calcByPositions(
			['cr-cl', 'cl-cr'],
			['bc-tc', 'bl-tl', 'br-tr'],
			targetRect,
			contentAreaRect,
			panelRect
		);
	};

	var calc = function (targetRect, contentAreaRect, panelRect) {
		return calcByPositions(
			['tc-bc', 'bc-tc', 'tl-bl', 'bl-tl', 'tr-br', 'br-tr'],
			['bc-tc', 'bl-tl', 'br-tr'],
			targetRect,
			contentAreaRect,
			panelRect
		);
	};

	var userConstrain = function (handler, targetRect, contentAreaRect, panelRect) {
		var userConstrainedPanelRect;

		if (typeof handler === 'function') {
			userConstrainedPanelRect = handler({
				elementRect: Convert.toClientRect(targetRect),
				contentAreaRect: Convert.toClientRect(contentAreaRect),
				panelRect: Convert.toClientRect(panelRect)
			});

			return Convert.fromClientRect(userConstrainedPanelRect);
		}

		return panelRect;
	};

	return {
		calcInsert: calcInsert,
		calc: calc,
		userConstrain: userConstrain
	};
});

/**
 * Panel.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/ui/Panel', [
	'global!tinymce.util.Tools',
	'global!tinymce.ui.Factory',
	'global!tinymce.DOM',
	'tinymce/inlite/ui/Toolbar',
	'tinymce/inlite/ui/Forms',
	'tinymce/inlite/core/Measure',
	'tinymce/inlite/core/Layout'
], function (Tools, Factory, DOM, Toolbar, Forms, Measure, Layout) {
	var DEFAULT_TEXT_SELECTION_ITEMS = 'bold italic | quicklink h2 h3 blockquote';
	var DEFAULT_INSERT_TOOLBAR_ITEMS = 'quickimage quicktable';
	var panel, currentRect;

	var createToolbars = function (editor, toolbars) {
		return Tools.map(toolbars, function (toolbar) {
			return Toolbar.create(editor, toolbar.id, toolbar.items);
		});
	};

	var getTextSelectionToolbarItems = function (settings) {
		var value = settings.selection_toolbar;
		return value ? value : DEFAULT_TEXT_SELECTION_ITEMS;
	};

	var getInsertToolbarItems = function (settings) {
		var value = settings.insert_toolbar;
		return value ? value : DEFAULT_INSERT_TOOLBAR_ITEMS;
	};

	var create = function (editor, toolbars) {
		var items, settings = editor.settings;

		items = createToolbars(editor, toolbars);
		items = items.concat([
			Toolbar.create(editor, 'text', getTextSelectionToolbarItems(settings)),
			Toolbar.create(editor, 'insert', getInsertToolbarItems(settings)),
			Forms.createQuickLinkForm(editor, hide)
		]);

		return Factory.create({
			type: 'floatpanel',
			role: 'dialog',
			classes: 'tinymce tinymce-inline arrow',
			ariaLabel: 'Inline toolbar',
			layout: 'flex',
			direction: 'column',
			align: 'stretch',
			autohide: false,
			autofix: true,
			fixed: true,
			border: 1,
			items: items,
			oncancel: function() {
				editor.focus();
			}
		});
	};

	var showPanel = function (panel) {
		if (panel) {
			panel.show();
		}
	};

	var movePanelTo = function (panel, pos) {
		panel.moveTo(pos.x, pos.y);
	};

	var togglePositionClass = function (panel, relPos) {
		relPos = relPos ? relPos.substr(0, 2) : '';

		Tools.each({
			t: 'down',
			b: 'up',
			c: 'center'
		}, function(cls, pos) {
			panel.classes.toggle('arrow-' + cls, pos === relPos.substr(0, 1));
		});

		if (relPos === 'cr') {
			panel.classes.toggle('arrow-left', true);
			panel.classes.toggle('arrow-right', false);
		} else if (relPos === 'cl') {
			panel.classes.toggle('arrow-left', true);
			panel.classes.toggle('arrow-right', true);
		} else {
			Tools.each({
				l: 'left',
				r: 'right'
			}, function(cls, pos) {
				panel.classes.toggle('arrow-' + cls, pos === relPos.substr(1, 1));
			});
		}
	};

	var showToolbar = function (panel, id) {
		var toolbars = panel.items().filter('#' + id);

		if (toolbars.length > 0) {
			toolbars[0].show();
			panel.reflow();
		}
	};

	var showPanelAt = function (panel, id, editor, targetRect) {
		var contentAreaRect, panelRect, result, userConstainHandler;

		showPanel(panel);
		panel.items().hide();
		showToolbar(panel, id);

		userConstainHandler = editor.settings.inline_toolbar_position_handler;
		contentAreaRect = Measure.getContentAreaRect(editor);
		panelRect = DOM.getRect(panel.getEl());

		if (id === 'insert') {
			result = Layout.calcInsert(targetRect, contentAreaRect, panelRect);
		} else {
			result = Layout.calc(targetRect, contentAreaRect, panelRect);
		}

		if (result) {
			panelRect = result.rect;
			currentRect = targetRect;
			movePanelTo(panel, Layout.userConstrain(userConstainHandler, targetRect, contentAreaRect, panelRect));

			togglePositionClass(panel, result.position);
		} else {
			hide(panel);
		}
	};

	var hasFormVisible = function () {
		return panel.items().filter('form:visible').length > 0;
	};

	var showForm = function (editor, id) {
		if (panel) {
			panel.items().hide();
			showToolbar(panel, id);

			var contentAreaRect, panelRect, result, userConstainHandler;

			showPanel(panel);
			panel.items().hide();
			showToolbar(panel, id);

			userConstainHandler = editor.settings.inline_toolbar_position_handler;
			contentAreaRect = Measure.getContentAreaRect(editor);
			panelRect = DOM.getRect(panel.getEl());

			result = Layout.calc(currentRect, contentAreaRect, panelRect);

			if (result) {
				panelRect = result.rect;
				movePanelTo(panel, Layout.userConstrain(userConstainHandler, currentRect, contentAreaRect, panelRect));

				togglePositionClass(panel, result.position);
			}
		}
	};

	var show = function (editor, id, targetRect, toolbars) {
		if (!panel) {
			panel = create(editor, toolbars);
			panel.renderTo(document.body).reflow().moveTo(targetRect.x, targetRect.y);
			editor.nodeChanged();
		}

		showPanelAt(panel, id, editor, targetRect);
	};

	var hide = function () {
		if (panel) {
			panel.hide();
		}
	};

	var focus = function () {
		if (panel) {
			panel.find('toolbar:visible').eq(0).each(function (item) {
				item.focus(true);
			});
		}
	};

	var remove = function () {
		if (panel) {
			panel.remove();
			panel = null;
		}
	};

	var inForm = function () {
		return panel && panel.visible() && hasFormVisible();
	};

	return {
		show: show,
		showForm: showForm,
		inForm: inForm,
		hide: hide,
		focus: focus,
		remove: remove
	};
});

/**
 * Conversions.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/file/Conversions', [
	'global!tinymce.util.Promise'
], function (Promise) {
	var blobToBase64 = function (blob) {
		return new Promise(function(resolve) {
			var reader = new FileReader();

			reader.onloadend = function() {
				resolve(reader.result.split(',')[1]);
			};

			reader.readAsDataURL(blob);
		});
	};

	return {
		blobToBase64: blobToBase64
	};
});



/**
 * Picker.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/file/Picker', [
	'global!tinymce.util.Promise'
], function (Promise) {
	var pickFile = function () {
		return new Promise(function (resolve) {
			var fileInput;

			fileInput = document.createElement("input");
			fileInput.type = "file";
			fileInput.style.position = 'fixed';
			fileInput.style.left = 0;
			fileInput.style.top = 0;
			fileInput.style.opacity = 0.001;
			document.body.appendChild(fileInput);

			fileInput.onchange = function(e) {
				resolve(Array.prototype.slice.call(e.target.files));
			};

			fileInput.click();
			fileInput.parentNode.removeChild(fileInput);
		});
	};

	return {
		pickFile: pickFile
	};
});



/**
 * Buttons.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/ui/Buttons', [
	'tinymce/inlite/ui/Panel',
	'tinymce/inlite/file/Conversions',
	'tinymce/inlite/file/Picker',
	'tinymce/inlite/core/Actions'
], function (Panel, Conversions, Picker, Actions) {
	var addHeaderButtons = function (editor) {
		var formatBlock = function (name) {
			return function () {
				Actions.formatBlock(editor, name);
			};
		};

		for (var i = 1; i < 6; i++) {
			var name = 'h' + i;

			editor.addButton(name, {
				text: name.toUpperCase(),
				tooltip: 'Heading ' + i,
				stateSelector: name,
				onclick: formatBlock(name),
				onPostRender: function () {
					// TODO: Remove this hack that produces bold H1-H6 when we have proper icons
					var span = this.getEl().firstChild.firstChild;
					span.style.fontWeight = 'bold';
				}
			});
		}
	};

	var addToEditor = function (editor) {
		editor.addButton('quicklink', {
			icon: 'link',
			tooltip: 'Insert/Edit link',
			stateSelector: 'a[href]',
			onclick: function () {
				Panel.showForm(editor, 'quicklink');
			}
		});

		editor.addButton('quickimage', {
			icon: 'image',
			tooltip: 'Insert image',
			onclick: function () {
				Picker.pickFile().then(function (files) {
					var blob = files[0];

					Conversions.blobToBase64(blob).then(function (base64) {
						Actions.insertBlob(editor, base64, blob);
					});
				});
			}
		});

		editor.addButton('quicktable', {
			icon: 'table',
			tooltip: 'Insert table',
			onclick: function () {
				Panel.hide();
				Actions.insertTable(editor, 2, 2);
			}
		});

		addHeaderButtons(editor);
	};

	return {
		addToEditor: addToEditor
	};
});

defineGlobal("global!tinymce.EditorManager", tinymce.EditorManager);
/**
 * SkinLoader.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/core/SkinLoader', [
	'global!tinymce.EditorManager',
	'global!tinymce.DOM'
], function (EditorManager, DOM) {
	var fireSkinLoaded = function (editor, callback) {
		var done = function () {
			editor.fire('SkinLoaded');
			callback();
		};

		if (editor.initialized) {
			done();
		} else {
			editor.on('init', done);
		}
	};

	var load = function (editor, skin, callback) {
		var baseUrl = EditorManager.baseURL;
		var skinUrl = baseUrl + '/skins/' + skin;

		var done = function () {
			fireSkinLoaded(editor, callback);
		};

		DOM.styleSheetLoader.load(skinUrl + '/skin.min.css', done);
		editor.contentCSS.push(skinUrl + '/content.inline.min.css');
	};

	return {
		load: load
	};
});



/**
 * Matcher.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/core/Matcher', [
], function () {
	// result :: String, Rect -> Matcher.result
	var result = function (id, rect) {
		return {
			id: id,
			rect: rect
		};
	};

	// match :: Editor, [(Editor -> Matcher.result | Null)] -> Matcher.result | Null
	var match = function (editor, matchers) {
		for (var i = 0; i < matchers.length; i++) {
			var f = matchers[i];
			var result = f(editor);

			if (result) {
				return result;
			}
		}

		return null;
	};

	return {
		match: match,
		result: result
	};
});

/**
 * SelectionMatcher.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/core/SelectionMatcher', [
	'tinymce/inlite/core/Matcher',
	'tinymce/inlite/core/Measure'
], function (Matcher, Measure) {
	// textSelection :: String -> (Editor -> Matcher.result | Null)
	var textSelection = function (id) {
		return function (editor) {
			if (!editor.selection.isCollapsed()) {
				return Matcher.result(id, Measure.getSelectionRect(editor));
			}

			return null;
		};
	};

	// emptyTextBlock :: [Elements], String -> (Editor -> Matcher.result | Null)
	var emptyTextBlock = function (elements, id) {
		return function (editor) {
			var i, textBlockElementsMap = editor.schema.getTextBlockElements();

			for (i = 0; i < elements.length; i++) {
				if (elements[i].nodeName === 'TABLE') {
					return null;
				}
			}

			for (i = 0; i < elements.length; i++) {
				if (elements[i].nodeName in textBlockElementsMap) {
					if (editor.dom.isEmpty(elements[i])) {
						return Matcher.result(id, Measure.getSelectionRect(editor));
					}

					return null;
				}
			}

			return null;
		};
	};

	return {
		textSelection: textSelection,
		emptyTextBlock: emptyTextBlock
	};
});

/**
 * ElementMatcher.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/core/ElementMatcher', [
	'tinymce/inlite/core/Matcher',
	'tinymce/inlite/core/Measure'
], function (Matcher, Measure) {
	// element :: Element, [PredicateId] -> (Editor -> Matcher.result | Null)
	var element = function (element, predicateIds) {
		return function (editor) {
			for (var i = 0; i < predicateIds.length; i++) {
				if (predicateIds[i].predicate(element)) {
					return Matcher.result(predicateIds[i].id, Measure.getElementRect(editor, element));
				}
			}

			return null;
		};
	};

	// parent :: [Elements], [PredicateId] -> (Editor -> Matcher.result | Null)
	var parent = function (elements, predicateIds) {
		return function (editor) {
			for (var i = 0; i < elements.length; i++) {
				for (var x = 0; x < predicateIds.length; x++) {
					if (predicateIds[x].predicate(elements[i])) {
						return Matcher.result(predicateIds[x].id, Measure.getElementRect(editor, elements[i]));
					}
				}
			}

			return null;
		};
	};

	return {
		element: element,
		parent: parent
	};
});

/**
 * Arr.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/alien/Arr', [
], function () {
	var flatten = function (arr) {
		return arr.reduce(function (results, item) {
			return Array.isArray(item) ? results.concat(flatten(item)) : results.concat(item);
		}, []);
	};

	return {
		flatten: flatten
	};
});

/**
 * PredicateId.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/core/PredicateId', [
	'global!tinymce.util.Tools'
], function (Tools) {
	var create = function (id, predicate) {
		return {
			id: id,
			predicate: predicate
		};
	};

	// fromContextToolbars :: [ContextToolbar] -> [PredicateId]
	var fromContextToolbars = function (toolbars) {
		return Tools.map(toolbars, function (toolbar) {
			return create(toolbar.id, toolbar.predicate);
		});
	};

	return {
		create: create,
		fromContextToolbars: fromContextToolbars
	};
});

/**
 * Theme.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2016 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

define('tinymce/inlite/Theme', [
	'global!tinymce.ThemeManager',
	'global!tinymce.util.Delay',
	'tinymce/inlite/ui/Panel',
	'tinymce/inlite/ui/Buttons',
	'tinymce/inlite/core/SkinLoader',
	'tinymce/inlite/core/SelectionMatcher',
	'tinymce/inlite/core/ElementMatcher',
	'tinymce/inlite/core/Matcher',
	'tinymce/inlite/alien/Arr',
	'tinymce/inlite/core/PredicateId'
], function(ThemeManager, Delay, Panel, Buttons, SkinLoader, SelectionMatcher, ElementMatcher, Matcher, Arr, PredicateId) {
	var getSelectionElements = function (editor) {
		var node = editor.selection.getNode();
		var elms = editor.dom.getParents(node);
		return elms;
	};

	var createToolbar = function (editor, selector, id, items) {
		var selectorPredicate = function (elm) {
			return editor.dom.is(elm, selector);
		};

		return {
			predicate: selectorPredicate,
			id: id,
			items: items
		};
	};

	var getToolbars = function (editor) {
		var contextToolbars = editor.contextToolbars;

		return Arr.flatten([
			contextToolbars ? contextToolbars : [],
			createToolbar(editor, 'img', 'image', 'alignleft aligncenter alignright')
		]);
	};

	var findMatchResult = function (editor, toolbars) {
		var result, elements, contextToolbarsPredicateIds;

		elements = getSelectionElements(editor);
		contextToolbarsPredicateIds = PredicateId.fromContextToolbars(toolbars);

		result = Matcher.match(editor, [
			ElementMatcher.element(elements[0], contextToolbarsPredicateIds),
			SelectionMatcher.textSelection('text'),
			SelectionMatcher.emptyTextBlock(elements, 'insert'),
			ElementMatcher.parent(elements, contextToolbarsPredicateIds)
		]);

		return result && result.rect ? result : null;
	};

	var togglePanel = function (editor) {
		var toggle = function () {
			var toolbars = getToolbars(editor);
			var result = findMatchResult(editor, toolbars);
			result ? Panel.show(editor, result.id, result.rect, toolbars) : Panel.hide();
		};

		return function () {
			if (!editor.removed) {
				toggle();
			}
		};
	};

	var ignoreWhenFormIsVisible = function (f) {
		return function () {
			if (!Panel.inForm()) {
				f();
			}
		};
	};

	var bindContextualToolbarsEvents = function (editor) {
		var throttledTogglePanel = Delay.throttle(togglePanel(editor), 0);
		var throttledTogglePanelWhenNotInForm = Delay.throttle(ignoreWhenFormIsVisible(togglePanel(editor)), 0);

		editor.on('blur hide ObjectResizeStart', Panel.hide);
		editor.on('click', throttledTogglePanel);
		editor.on('nodeChange mouseup', throttledTogglePanelWhenNotInForm);
		editor.on('ResizeEditor ResizeWindow keyup', throttledTogglePanel);
		editor.on('remove', Panel.remove);

		editor.shortcuts.add('Alt+F10', '', Panel.focus);
	};

	var overrideLinkShortcut = function (editor) {
		editor.shortcuts.remove('meta+k');
		editor.shortcuts.add('meta+k', '', function () {
			var toolbars = getToolbars(editor);
			var result = result = Matcher.match(editor, [
				SelectionMatcher.textSelection('quicklink')
			]);

			if (result) {
				Panel.show(editor, result.id, result.rect, toolbars);
			}
		});
	};

	var renderInlineUI = function (editor) {
		var skinName = editor.settings.skin || 'lightgray';

		SkinLoader.load(editor, skinName, function () {
			bindContextualToolbarsEvents(editor);
			overrideLinkShortcut(editor);
		});

		return {};
	};

	var fail = function (message) {
		throw new Error(message);
	};

	ThemeManager.add('inlite', function (editor) {
		Buttons.addToEditor(editor);

		var renderUI = function () {
			return editor.inline ? renderInlineUI(editor) : fail('inlite theme only supports inline mode.');
		};

		return {
			renderUI: renderUI
		};
	});

	return function() {};
});

dem('tinymce/inlite/Theme')();
})();
