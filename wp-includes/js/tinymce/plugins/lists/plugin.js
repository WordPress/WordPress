/**
 * plugin.js
 *
 * Released under LGPL License.
 * Copyright (c) 1999-2015 Ephox Corp. All rights reserved
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true */
/*eslint consistent-this:0 */

tinymce.PluginManager.add('lists', function(editor) {
	var self = this;

	function isChildOfBody(elm) {
		return editor.$.contains(editor.getBody(), elm);
	}

	function isBr(node) {
		return node && node.nodeName == 'BR';
	}

	function isListNode(node) {
		return node && (/^(OL|UL|DL)$/).test(node.nodeName) && isChildOfBody(node);
	}

	function isFirstChild(node) {
		return node.parentNode.firstChild == node;
	}

	function isLastChild(node) {
		return node.parentNode.lastChild == node;
	}

	function isTextBlock(node) {
		return node && !!editor.schema.getTextBlockElements()[node.nodeName];
	}

	function isEditorBody(elm) {
		return elm === editor.getBody();
	}

	editor.on('init', function() {
		var dom = editor.dom, selection = editor.selection;

		function isEmpty(elm, keepBookmarks) {
			var empty = dom.isEmpty(elm);

			if (keepBookmarks && dom.select('span[data-mce-type=bookmark]').length > 0) {
				return false;
			}

			return empty;
		}

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
		function createBookmark(rng) {
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
		}

		/**
		 * Moves the selection to the current bookmark and removes any selection container wrappers.
		 *
		 * @param {Object} bookmark Bookmark object to move selection to.
		 */
		function moveToBookmark(bookmark) {
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

			selection.setRng(rng);
		}

		function createNewTextBlock(contentNode, blockName) {
			var node, textBlock, fragment = dom.createFragment(), hasContentNode;
			var blockElements = editor.schema.getBlockElements();

			if (editor.settings.forced_root_block) {
				blockName = blockName || editor.settings.forced_root_block;
			}

			if (blockName) {
				textBlock = dom.create(blockName);

				if (textBlock.tagName === editor.settings.forced_root_block) {
					dom.setAttribs(textBlock, editor.settings.forced_root_block_attrs);
				}

				fragment.appendChild(textBlock);
			}

			if (contentNode) {
				while ((node = contentNode.firstChild)) {
					var nodeName = node.nodeName;

					if (!hasContentNode && (nodeName != 'SPAN' || node.getAttribute('data-mce-type') != 'bookmark')) {
						hasContentNode = true;
					}

					if (blockElements[nodeName]) {
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
				// BR is needed in empty blocks on non IE browsers
				if (!hasContentNode && (!tinymce.Env.ie || tinymce.Env.ie > 10)) {
					textBlock.appendChild(dom.create('br', {'data-mce-bogus': '1'}));
				}
			}

			return fragment;
		}

		function getSelectedListItems() {
			return tinymce.grep(selection.getSelectedBlocks(), function(block) {
				return /^(LI|DT|DD)$/.test(block.nodeName);
			});
		}

		function splitList(ul, li, newBlock) {
			var tmpRng, fragment, bookmarks, node;

			function removeAndKeepBookmarks(targetNode) {
				tinymce.each(bookmarks, function(node) {
					targetNode.parentNode.insertBefore(node, li.parentNode);
				});

				dom.remove(targetNode);
			}

			bookmarks = dom.select('span[data-mce-type="bookmark"]', ul);
			newBlock = newBlock || createNewTextBlock(li);
			tmpRng = dom.createRng();
			tmpRng.setStartAfter(li);
			tmpRng.setEndAfter(ul);
			fragment = tmpRng.extractContents();

			for (node = fragment.firstChild; node; node = node.firstChild) {
				if (node.nodeName == 'LI' && dom.isEmpty(node)) {
					dom.remove(node);
					break;
				}
			}

			if (!dom.isEmpty(fragment)) {
				dom.insertAfter(fragment, ul);
			}

			dom.insertAfter(newBlock, ul);

			if (isEmpty(li.parentNode)) {
				removeAndKeepBookmarks(li.parentNode);
			}

			dom.remove(li);

			if (isEmpty(ul)) {
				dom.remove(ul);
			}
		}

		var shouldMerge = function (listBlock, sibling) {
			var targetStyle = editor.dom.getStyle(listBlock, 'list-style-type', true);
			var style = editor.dom.getStyle(sibling, 'list-style-type', true);
			return targetStyle === style;
		};

		function mergeWithAdjacentLists(listBlock) {
			var sibling, node;

			sibling = listBlock.nextSibling;
			if (sibling && isListNode(sibling) && sibling.nodeName == listBlock.nodeName && shouldMerge(listBlock, sibling)) {
				while ((node = sibling.firstChild)) {
					listBlock.appendChild(node);
				}

				dom.remove(sibling);
			}

			sibling = listBlock.previousSibling;
			if (sibling && isListNode(sibling) && sibling.nodeName == listBlock.nodeName && shouldMerge(listBlock, sibling)) {
				while ((node = sibling.firstChild)) {
					listBlock.insertBefore(node, listBlock.firstChild);
				}

				dom.remove(sibling);
			}
		}

		/**
		 * Normalizes the all lists in the specified element.
		 */
		function normalizeList(element) {
			tinymce.each(tinymce.grep(dom.select('ol,ul', element)), function(ul) {
				var sibling, parentNode = ul.parentNode;

				// Move UL/OL to previous LI if it's the only child of a LI
				if (parentNode.nodeName == 'LI' && parentNode.firstChild == ul) {
					sibling = parentNode.previousSibling;
					if (sibling && sibling.nodeName == 'LI') {
						sibling.appendChild(ul);

						if (isEmpty(parentNode)) {
							dom.remove(parentNode);
						}
					}
				}

				// Append OL/UL to previous LI if it's in a parent OL/UL i.e. old HTML4
				if (isListNode(parentNode)) {
					sibling = parentNode.previousSibling;
					if (sibling && sibling.nodeName == 'LI') {
						sibling.appendChild(ul);
					}
				}
			});
		}

		function outdent(li) {
			var ul = li.parentNode, ulParent = ul.parentNode, newBlock;

			function removeEmptyLi(li) {
				if (isEmpty(li)) {
					dom.remove(li);
				}
			}

			if (isEditorBody(ul)) {
				return true;
			}

			if (li.nodeName == 'DD') {
				dom.rename(li, 'DT');
				return true;
			}

			if (isFirstChild(li) && isLastChild(li)) {
				if (ulParent.nodeName == "LI") {
					dom.insertAfter(li, ulParent);
					removeEmptyLi(ulParent);
					dom.remove(ul);
				} else if (isListNode(ulParent)) {
					dom.remove(ul, true);
				} else {
					ulParent.insertBefore(createNewTextBlock(li), ul);
					dom.remove(ul);
				}

				return true;
			} else if (isFirstChild(li)) {
				if (ulParent.nodeName == "LI") {
					dom.insertAfter(li, ulParent);
					li.appendChild(ul);
					removeEmptyLi(ulParent);
				} else if (isListNode(ulParent)) {
					ulParent.insertBefore(li, ul);
				} else {
					ulParent.insertBefore(createNewTextBlock(li), ul);
					dom.remove(li);
				}

				return true;
			} else if (isLastChild(li)) {
				if (ulParent.nodeName == "LI") {
					dom.insertAfter(li, ulParent);
				} else if (isListNode(ulParent)) {
					dom.insertAfter(li, ul);
				} else {
					dom.insertAfter(createNewTextBlock(li), ul);
					dom.remove(li);
				}

				return true;
			}

			if (ulParent.nodeName == 'LI') {
				ul = ulParent;
				newBlock = createNewTextBlock(li, 'LI');
			} else if (isListNode(ulParent)) {
				newBlock = createNewTextBlock(li, 'LI');
			} else {
				newBlock = createNewTextBlock(li);
			}

			splitList(ul, li, newBlock);
			normalizeList(ul.parentNode);

			return true;
		}

		function indent(li) {
			var sibling, newList, listStyle;

			function mergeLists(from, to) {
				var node;

				if (isListNode(from)) {
					while ((node = li.lastChild.firstChild)) {
						to.appendChild(node);
					}

					dom.remove(from);
				}
			}

			if (li.nodeName == 'DT') {
				dom.rename(li, 'DD');
				return true;
			}

			sibling = li.previousSibling;

			if (sibling && isListNode(sibling)) {
				sibling.appendChild(li);
				return true;
			}

			if (sibling && sibling.nodeName == 'LI' && isListNode(sibling.lastChild)) {
				sibling.lastChild.appendChild(li);
				mergeLists(li.lastChild, sibling.lastChild);
				return true;
			}

			sibling = li.nextSibling;

			if (sibling && isListNode(sibling)) {
				sibling.insertBefore(li, sibling.firstChild);
				return true;
			}

			/*if (sibling && sibling.nodeName == 'LI' && isListNode(li.lastChild)) {
				return false;
			}*/

			sibling = li.previousSibling;
			if (sibling && sibling.nodeName == 'LI') {
				newList = dom.create(li.parentNode.nodeName);
				listStyle = dom.getStyle(li.parentNode, 'listStyleType');
				if (listStyle) {
					dom.setStyle(newList, 'listStyleType', listStyle);
				}
				sibling.appendChild(newList);
				newList.appendChild(li);
				mergeLists(li.lastChild, newList);
				return true;
			}

			return false;
		}

		function indentSelection() {
			var listElements = getSelectedListItems();

			if (listElements.length) {
				var bookmark = createBookmark(selection.getRng(true));

				for (var i = 0; i < listElements.length; i++) {
					if (!indent(listElements[i]) && i === 0) {
						break;
					}
				}

				moveToBookmark(bookmark);
				editor.nodeChanged();

				return true;
			}
		}

		function outdentSelection() {
			var listElements = getSelectedListItems();

			if (listElements.length) {
				var bookmark = createBookmark(selection.getRng(true));
				var i, y, root = editor.getBody();

				i = listElements.length;
				while (i--) {
					var node = listElements[i].parentNode;

					while (node && node != root) {
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
					if (!outdent(listElements[i]) && i === 0) {
						break;
					}
				}

				moveToBookmark(bookmark);
				editor.nodeChanged();

				return true;
			}
		}

		function applyList(listName, detail) {
			var rng = selection.getRng(true), bookmark, listItemName = 'LI';

			if (dom.getContentEditable(selection.getNode()) === "false") {
				return;
			}

			listName = listName.toUpperCase();

			if (listName == 'DL') {
				listItemName = 'DT';
			}

			function getSelectedTextBlocks() {
				var textBlocks = [], root = editor.getBody();

				function getEndPointNode(start) {
					var container, offset;

					container = rng[start ? 'startContainer' : 'endContainer'];
					offset = rng[start ? 'startOffset' : 'endOffset'];

					// Resolve node index
					if (container.nodeType == 1) {
						container = container.childNodes[Math.min(offset, container.childNodes.length - 1)] || container;
					}

					while (container.parentNode != root) {
						if (isTextBlock(container)) {
							return container;
						}

						if (/^(TD|TH)$/.test(container.parentNode.nodeName)) {
							return container;
						}

						container = container.parentNode;
					}

					return container;
				}

				var startNode = getEndPointNode(true);
				var endNode = getEndPointNode();
				var block, siblings = [];

				for (var node = startNode; node; node = node.nextSibling) {
					siblings.push(node);

					if (node == endNode) {
						break;
					}
				}

				tinymce.each(siblings, function(node) {
					if (isTextBlock(node)) {
						textBlocks.push(node);
						block = null;
						return;
					}

					if (dom.isBlock(node) || isBr(node)) {
						if (isBr(node)) {
							dom.remove(node);
						}

						block = null;
						return;
					}

					var nextSibling = node.nextSibling;
					if (tinymce.dom.BookmarkManager.isBookmarkNode(node)) {
						if (isTextBlock(nextSibling) || (!nextSibling && node.parentNode == root)) {
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
			}

			bookmark = createBookmark(rng);

			tinymce.each(getSelectedTextBlocks(), function(block) {
				var listBlock, sibling;

				var hasCompatibleStyle = function (sib) {
					var sibStyle = dom.getStyle(sib, 'list-style-type');
					var detailStyle = detail ? detail['list-style-type'] : '';

					detailStyle = detailStyle === null ? '' : detailStyle;

					return sibStyle === detailStyle;
				};

				sibling = block.previousSibling;
				if (sibling && isListNode(sibling) && sibling.nodeName == listName && hasCompatibleStyle(sibling)) {
					listBlock = sibling;
					block = dom.rename(block, listItemName);
					sibling.appendChild(block);
				} else {
					listBlock = dom.create(listName);
					block.parentNode.insertBefore(listBlock, block);
					listBlock.appendChild(block);
					block = dom.rename(block, listItemName);
				}

				updateListStyle(listBlock, detail);
				mergeWithAdjacentLists(listBlock);
			});

			moveToBookmark(bookmark);
		}

		var updateListStyle = function (el, detail) {
			dom.setStyle(el, 'list-style-type', detail ? detail['list-style-type'] : null);
		};

		function removeList() {
			var bookmark = createBookmark(selection.getRng(true)), root = editor.getBody();

			tinymce.each(getSelectedListItems(), function(li) {
				var node, rootList;

				if (isEditorBody(li.parentNode)) {
					return;
				}

				if (isEmpty(li)) {
					outdent(li);
					return;
				}

				for (node = li; node && node != root; node = node.parentNode) {
					if (isListNode(node)) {
						rootList = node;
					}
				}

				splitList(rootList, li);
			});

			moveToBookmark(bookmark);
		}

		function toggleList(listName, detail) {
			var parentList = dom.getParent(selection.getStart(), 'OL,UL,DL');

			if (isEditorBody(parentList)) {
				return;
			}

			if (parentList) {
				if (parentList.nodeName == listName) {
					removeList(listName);
				} else {
					var bookmark = createBookmark(selection.getRng(true));
					updateListStyle(parentList, detail);
					mergeWithAdjacentLists(dom.rename(parentList, listName));

					moveToBookmark(bookmark);
				}
			} else {
				applyList(listName, detail);
			}
		}

		function queryListCommandState(listName) {
			return function() {
				var parentList = dom.getParent(editor.selection.getStart(), 'UL,OL,DL');

				return parentList && parentList.nodeName == listName;
			};
		}

		function isBogusBr(node) {
			if (!isBr(node)) {
				return false;
			}

			if (dom.isBlock(node.nextSibling) && !isBr(node.previousSibling)) {
				return true;
			}

			return false;
		}

		self.backspaceDelete = function(isForward) {
			function findNextCaretContainer(rng, isForward) {
				var node = rng.startContainer, offset = rng.startOffset;
				var nonEmptyBlocks, walker;

				if (node.nodeType == 3 && (isForward ? offset < node.data.length : offset > 0)) {
					return node;
				}

				nonEmptyBlocks = editor.schema.getNonEmptyElements();
				if (node.nodeType == 1) {
					node = tinymce.dom.RangeUtils.getNode(node, offset);
				}

				walker = new tinymce.dom.TreeWalker(node, editor.getBody());

				// Delete at <li>|<br></li> then jump over the bogus br
				if (isForward) {
					if (isBogusBr(node)) {
						walker.next();
					}
				}

				while ((node = walker[isForward ? 'next' : 'prev2']())) {
					if (node.nodeName == 'LI' && !node.hasChildNodes()) {
						return node;
					}

					if (nonEmptyBlocks[node.nodeName]) {
						return node;
					}

					if (node.nodeType == 3 && node.data.length > 0) {
						return node;
					}
				}
			}

			function mergeLiElements(fromElm, toElm) {
				var node, listNode, ul = fromElm.parentNode;

				if (!isChildOfBody(fromElm) || !isChildOfBody(toElm)) {
					return;
				}

				if (isListNode(toElm.lastChild)) {
					listNode = toElm.lastChild;
				}

				if (ul == toElm.lastChild) {
					if (isBr(ul.previousSibling)) {
						dom.remove(ul.previousSibling);
					}
				}

				node = toElm.lastChild;
				if (node && isBr(node) && fromElm.hasChildNodes()) {
					dom.remove(node);
				}

				if (isEmpty(toElm, true)) {
					dom.$(toElm).empty();
				}

				if (!isEmpty(fromElm, true)) {
					while ((node = fromElm.firstChild)) {
						toElm.appendChild(node);
					}
				}

				if (listNode) {
					toElm.appendChild(listNode);
				}

				dom.remove(fromElm);

				if (isEmpty(ul) && !isEditorBody(ul)) {
					dom.remove(ul);
				}
			}

			if (selection.isCollapsed()) {
				var li = dom.getParent(selection.getStart(), 'LI'), ul, rng, otherLi;

				if (li) {
					ul = li.parentNode;
					if (isEditorBody(ul) && dom.isEmpty(ul)) {
						return true;
					}

					rng = selection.getRng(true);
					otherLi = dom.getParent(findNextCaretContainer(rng, isForward), 'LI');

					if (otherLi && otherLi != li) {
						var bookmark = createBookmark(rng);

						if (isForward) {
							mergeLiElements(otherLi, li);
						} else {
							mergeLiElements(li, otherLi);
						}

						moveToBookmark(bookmark);

						return true;
					} else if (!otherLi) {
						if (!isForward && removeList(ul.nodeName)) {
							return true;
						}
					}
				}
			}
		};

		editor.on('BeforeExecCommand', function(e) {
			var cmd = e.command.toLowerCase(), isHandled;

			if (cmd == "indent") {
				if (indentSelection()) {
					isHandled = true;
				}
			} else if (cmd == "outdent") {
				if (outdentSelection()) {
					isHandled = true;
				}
			}

			if (isHandled) {
				editor.fire('ExecCommand', {command: e.command});
				e.preventDefault();
				return true;
			}
		});

		editor.addCommand('InsertUnorderedList', function(ui, detail) {
			toggleList('UL', detail);
		});

		editor.addCommand('InsertOrderedList', function(ui, detail) {
			toggleList('OL', detail);
		});

		editor.addCommand('InsertDefinitionList', function(ui, detail) {
			toggleList('DL', detail);
		});

		editor.addQueryStateHandler('InsertUnorderedList', queryListCommandState('UL'));
		editor.addQueryStateHandler('InsertOrderedList', queryListCommandState('OL'));
		editor.addQueryStateHandler('InsertDefinitionList', queryListCommandState('DL'));

		editor.on('keydown', function(e) {
			// Check for tab but not ctrl/cmd+tab since it switches browser tabs
			if (e.keyCode != 9 || tinymce.util.VK.metaKeyPressed(e)) {
				return;
			}

			if (editor.dom.getParent(editor.selection.getStart(), 'LI,DT,DD')) {
				e.preventDefault();

				if (e.shiftKey) {
					outdentSelection();
				} else {
					indentSelection();
				}
			}
		});
	});

	editor.addButton('indent', {
		icon: 'indent',
		title: 'Increase indent',
		cmd: 'Indent',
		onPostRender: function() {
			var ctrl = this;

			editor.on('nodechange', function() {
				var blocks = editor.selection.getSelectedBlocks();
				var disable = false;

				for (var i = 0, l = blocks.length; !disable && i < l; i++) {
					var tag = blocks[i].nodeName;

					disable = (tag == 'LI' && isFirstChild(blocks[i]) || tag == 'UL' || tag == 'OL' || tag == 'DD');
				}

				ctrl.disabled(disable);
			});
		}
	});

	editor.on('keydown', function(e) {
		if (e.keyCode == tinymce.util.VK.BACKSPACE) {
			if (self.backspaceDelete()) {
				e.preventDefault();
			}
		} else if (e.keyCode == tinymce.util.VK.DELETE) {
			if (self.backspaceDelete(true)) {
				e.preventDefault();
			}
		}
	});
});
