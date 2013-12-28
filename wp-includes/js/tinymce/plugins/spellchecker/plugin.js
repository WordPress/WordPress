/**
 * Compiled inline version. (Library mode)
 */

/*jshint smarttabs:true, undef:true, latedef:true, curly:true, bitwise:true, camelcase:true */
/*globals $code */

(function(exports, undefined) {
	"use strict";

	var modules = {};

	function require(ids, callback) {
		var module, defs = [];

		for (var i = 0; i < ids.length; ++i) {
			module = modules[ids[i]] || resolve(ids[i]);
			if (!module) {
				throw 'module definition dependecy not found: ' + ids[i];
			}

			defs.push(module);
		}

		callback.apply(null, defs);
	}

	function define(id, dependencies, definition) {
		if (typeof id !== 'string') {
			throw 'invalid module definition, module id must be defined and be a string';
		}

		if (dependencies === undefined) {
			throw 'invalid module definition, dependencies must be specified';
		}

		if (definition === undefined) {
			throw 'invalid module definition, definition function must be specified';
		}

		require(dependencies, function() {
			modules[id] = definition.apply(null, arguments);
		});
	}

	function defined(id) {
		return !!modules[id];
	}

	function resolve(id) {
		var target = exports;
		var fragments = id.split(/[.\/]/);

		for (var fi = 0; fi < fragments.length; ++fi) {
			if (!target[fragments[fi]]) {
				return;
			}

			target = target[fragments[fi]];
		}

		return target;
	}

	function expose(ids) {
		for (var i = 0; i < ids.length; i++) {
			var target = exports;
			var id = ids[i];
			var fragments = id.split(/[.\/]/);

			for (var fi = 0; fi < fragments.length - 1; ++fi) {
				if (target[fragments[fi]] === undefined) {
					target[fragments[fi]] = {};
				}

				target = target[fragments[fi]];
			}

			target[fragments[fragments.length - 1]] = modules[id];
		}
	}

// Included from: js/tinymce/plugins/spellchecker/classes/DomTextMatcher.js

/**
 * DomTextMatcher.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * This class logic for filtering text and matching words.
 *
 * @class tinymce.spellcheckerplugin.TextFilter
 * @private
 */
define("tinymce/spellcheckerplugin/DomTextMatcher", [], function() {
	// Based on work developed by: James Padolsey http://james.padolsey.com
	// released under UNLICENSE that is compatible with LGPL
	// TODO: Handle contentEditable edgecase:
	// <p>text<span contentEditable="false">text<span contentEditable="true">text</span>text</span>text</p>
	return function(regex, node, schema) {
		var m, matches = [], text, count = 0, doc;
		var blockElementsMap, hiddenTextElementsMap, shortEndedElementsMap;

		doc = node.ownerDocument;
		blockElementsMap = schema.getBlockElements(); // H1-H6, P, TD etc
		hiddenTextElementsMap = schema.getWhiteSpaceElements(); // TEXTAREA, PRE, STYLE, SCRIPT
		shortEndedElementsMap = schema.getShortEndedElements(); // BR, IMG, INPUT

		function getMatchIndexes(m) {
			if (!m[0]) {
				throw 'findAndReplaceDOMText cannot handle zero-length matches';
			}

			var index = m.index;

			return [index, index + m[0].length, [m[0]]];
		}

		function getText(node) {
			var txt;

			if (node.nodeType === 3) {
				return node.data;
			}

			if (hiddenTextElementsMap[node.nodeName] && !blockElementsMap[node.nodeName]) {
				return '';
			}

			txt = '';

			if (blockElementsMap[node.nodeName] || shortEndedElementsMap[node.nodeName]) {
				txt += '\n';
			}

			if ((node = node.firstChild)) {
				do {
					txt += getText(node);
				} while ((node = node.nextSibling));
			}

			return txt;
		}

		function stepThroughMatches(node, matches, replaceFn) {
			var startNode, endNode, startNodeIndex,
				endNodeIndex, innerNodes = [], atIndex = 0, curNode = node,
				matchLocation = matches.shift(), matchIndex = 0;

			out: while (true) {
				if (blockElementsMap[curNode.nodeName] || shortEndedElementsMap[curNode.nodeName]) {
					atIndex++;
				}

				if (curNode.nodeType === 3) {
					if (!endNode && curNode.length + atIndex >= matchLocation[1]) {
						// We've found the ending
						endNode = curNode;
						endNodeIndex = matchLocation[1] - atIndex;
					} else if (startNode) {
						// Intersecting node
						innerNodes.push(curNode);
					}

					if (!startNode && curNode.length + atIndex > matchLocation[0]) {
						// We've found the match start
						startNode = curNode;
						startNodeIndex = matchLocation[0] - atIndex;
					}

					atIndex += curNode.length;
				}

				if (startNode && endNode) {
					curNode = replaceFn({
						startNode: startNode,
						startNodeIndex: startNodeIndex,
						endNode: endNode,
						endNodeIndex: endNodeIndex,
						innerNodes: innerNodes,
						match: matchLocation[2],
						matchIndex: matchIndex
					});

					// replaceFn has to return the node that replaced the endNode
					// and then we step back so we can continue from the end of the
					// match:
					atIndex -= (endNode.length - endNodeIndex);
					startNode = null;
					endNode = null;
					innerNodes = [];
					matchLocation = matches.shift();
					matchIndex++;

					if (!matchLocation) {
						break; // no more matches
					}
				} else if ((!hiddenTextElementsMap[curNode.nodeName] || blockElementsMap[curNode.nodeName]) && curNode.firstChild) {
					// Move down
					curNode = curNode.firstChild;
					continue;
				} else if (curNode.nextSibling) {
					// Move forward:
					curNode = curNode.nextSibling;
					continue;
				}

				// Move forward or up:
				while (true) {
					if (curNode.nextSibling) {
						curNode = curNode.nextSibling;
						break;
					} else if (curNode.parentNode !== node) {
						curNode = curNode.parentNode;
					} else {
						break out;
					}
				}
			}
		}

		/**
		* Generates the actual replaceFn which splits up text nodes
		* and inserts the replacement element.
		*/
		function genReplacer(nodeName) {
			var makeReplacementNode;

			if (typeof nodeName != 'function') {
				var stencilNode = nodeName.nodeType ? nodeName : doc.createElement(nodeName);

				makeReplacementNode = function(fill, matchIndex) {
					var clone = stencilNode.cloneNode(false);

					clone.setAttribute('data-mce-index', matchIndex);

					if (fill) {
						clone.appendChild(doc.createTextNode(fill));
					}

					return clone;
				};
			} else {
				makeReplacementNode = nodeName;
			}

			return function replace(range) {
				var before, after, parentNode, startNode = range.startNode,
					endNode = range.endNode, matchIndex = range.matchIndex;

				if (startNode === endNode) {
					var node = startNode;

					parentNode = node.parentNode;
					if (range.startNodeIndex > 0) {
						// Add `before` text node (before the match)
						before = doc.createTextNode(node.data.substring(0, range.startNodeIndex));
						parentNode.insertBefore(before, node);
					}

					// Create the replacement node:
					var el = makeReplacementNode(range.match[0], matchIndex);
					parentNode.insertBefore(el, node);
					if (range.endNodeIndex < node.length) {
						// Add `after` text node (after the match)
						after = doc.createTextNode(node.data.substring(range.endNodeIndex));
						parentNode.insertBefore(after, node);
					}

					node.parentNode.removeChild(node);

					return el;
				} else {
					// Replace startNode -> [innerNodes...] -> endNode (in that order)
					before = doc.createTextNode(startNode.data.substring(0, range.startNodeIndex));
					after = doc.createTextNode(endNode.data.substring(range.endNodeIndex));
					var elA = makeReplacementNode(startNode.data.substring(range.startNodeIndex), matchIndex);
					var innerEls = [];

					for (var i = 0, l = range.innerNodes.length; i < l; ++i) {
						var innerNode = range.innerNodes[i];
						var innerEl = makeReplacementNode(innerNode.data, matchIndex);
						innerNode.parentNode.replaceChild(innerEl, innerNode);
						innerEls.push(innerEl);
					}

					var elB = makeReplacementNode(endNode.data.substring(0, range.endNodeIndex), matchIndex);

					parentNode = startNode.parentNode;
					parentNode.insertBefore(before, startNode);
					parentNode.insertBefore(elA, startNode);
					parentNode.removeChild(startNode);

					parentNode = endNode.parentNode;
					parentNode.insertBefore(elB, endNode);
					parentNode.insertBefore(after, endNode);
					parentNode.removeChild(endNode);

					return elB;
				}
			};
		}

		text = getText(node);
		if (text && regex.global) {
			while ((m = regex.exec(text))) {
				matches.push(getMatchIndexes(m));
			}
		}

		function filter(callback) {
			var filteredMatches = [];

			each(function(match, i) {
				if (callback(match, i)) {
					filteredMatches.push(match);
				}
			});

			matches = filteredMatches;

			/*jshint validthis:true*/
			return this;
		}

		function each(callback) {
			for (var i = 0, l = matches.length; i < l; i++) {
				if (callback(matches[i], i) === false) {
					break;
				}
			}

			/*jshint validthis:true*/
			return this;
		}

		function mark(replacementNode) {
			if (matches.length) {
				count = matches.length;
				stepThroughMatches(node, matches, genReplacer(replacementNode));
			}

			/*jshint validthis:true*/
			return this;
		}

		return {
			text: text,
			count: count,
			matches: matches,
			each: each,
			filter: filter,
			mark: mark
		};
	};
});

// Included from: js/tinymce/plugins/spellchecker/classes/Plugin.js

/**
 * Plugin.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*jshint camelcase:false */

/**
 * This class contains all core logic for the spellchecker plugin.
 *
 * @class tinymce.spellcheckerplugin.Plugin
 * @private
 */
define("tinymce/spellcheckerplugin/Plugin", [
	"tinymce/spellcheckerplugin/DomTextMatcher",
	"tinymce/PluginManager",
	"tinymce/util/Tools",
	"tinymce/ui/Menu",
	"tinymce/dom/DOMUtils",
	"tinymce/util/JSONRequest",
	"tinymce/util/URI"
], function(DomTextMatcher, PluginManager, Tools, Menu, DOMUtils, JSONRequest, URI) {
	PluginManager.add('spellchecker', function(editor, url) {
		var lastSuggestions, started, suggestionsMenu, settings = editor.settings;

		function isEmpty(obj) {
			/*jshint unused:false*/
			for (var name in obj) {
				return false;
			}

			return true;
		}

		function showSuggestions(target, word) {
			var items = [], suggestions = lastSuggestions[word];

			Tools.each(suggestions, function(suggestion) {
				items.push({
					text: suggestion,
					onclick: function() {
						editor.insertContent(suggestion);
						checkIfFinished();
					}
				});
			});

			items.push.apply(items, [
				{text: '-'},

				{text: 'Ignore', onclick: function() {
					ignoreWord(target, word);
				}},

				{text: 'Ignore all', onclick: function() {
					ignoreWord(target, word, true);
				}},

				{text: 'Finish', onclick: finish}
			]);

			// Render menu
			suggestionsMenu = new Menu({
				items: items,
				context: 'contextmenu',
				onautohide: function(e) {
					if (e.target.className.indexOf('spellchecker') != -1) {
						e.preventDefault();
					}
				},
				onhide: function() {
					suggestionsMenu.remove();
					suggestionsMenu = null;
				}
			});

			suggestionsMenu.renderTo(document.body);

			// Position menu
			var pos = DOMUtils.DOM.getPos(editor.getContentAreaContainer());
			var targetPos = editor.dom.getPos(target);

			pos.x += targetPos.x;
			pos.y += targetPos.y;

			suggestionsMenu.moveTo(pos.x, pos.y + target.offsetHeight);
		}

		function spellcheck() {
			var textFilter, words = [], uniqueWords = {};

			if (started) {
				finish();
				return;
			}

			started = true;

			function doneCallback(suggestions) {
				editor.setProgressState(false);

				if (isEmpty(suggestions)) {
					editor.windowManager.alert('No misspellings found');
					started = false;
					return;
				}

				lastSuggestions = suggestions;

				textFilter.filter(function(match) {
					return !!suggestions[match[2][0]];
				}).mark(editor.dom.create('span', {
					"class": 'mce-spellchecker-word',
					"data-mce-bogus": 1
				}));

				textFilter = null;
				editor.fire('SpellcheckStart');
			}

			// Regexp for finding word specific characters this will split words by
			// spaces, quotes, copy right characters etc. It's escaped with unicode characters
			// to make it easier to output scripts on servers using different encodings
			// so if you add any characters outside the 128 byte range make sure to escape it
			var nonWordSeparatorCharacters = editor.getParam('spellchecker_wordchar_pattern') || new RegExp("[^" +
				"\\s!\"#$%&()*+,-./:;<=>?@[\\]^_{|}`" +
				"\u00a7\u00a9\u00ab\u00ae\u00b1\u00b6\u00b7\u00b8\u00bb" +
				"\u00bc\u00bd\u00be\u00bf\u00d7\u00f7\u00a4\u201d\u201c\u201e" +
			"]+", "g");

			// Find all words and make an unique words array
			textFilter = new DomTextMatcher(nonWordSeparatorCharacters, editor.getBody(), editor.schema).each(function(match) {
				var word = match[2][0];

				// TODO: Fix so it remembers correctly spelled words
				if (!uniqueWords[word]) {
					// Ignore numbers and single character words
					if (/^\d+$/.test(word) || word.length == 1) {
						return;
					}

					words.push(word);
					uniqueWords[word] = true;
				}
			});

			function defaultSpellcheckCallback(method, words, doneCallback) {
				JSONRequest.sendRPC({
					url: new URI(url).toAbsolute(settings.spellchecker_rpc_url),
					method: method,
					params: {
						lang: settings.spellchecker_language || "en",
						words: words
					},
					success: function(result) {
						doneCallback(result);
					},
					error: function(error, xhr) {
						if (error == "JSON Parse error.") {
							error = "Non JSON response:" + xhr.responseText;
						} else {
							error = "Error: " + error;
						}

						editor.windowManager.alert(error);
						editor.setProgressState(false);
						textFilter = null;
						started = false;
					}
				});
			}

			editor.setProgressState(true);

			var spellCheckCallback = settings.spellchecker_callback || defaultSpellcheckCallback;
			spellCheckCallback("spellcheck", words, doneCallback);
		}

		function checkIfFinished() {
			if (!editor.dom.select('span.mce-spellchecker-word').length) {
				finish();
			}
		}

		function unwrap(node) {
			var parentNode = node.parentNode;
			parentNode.insertBefore(node.firstChild, node);
			node.parentNode.removeChild(node);
		}

		function ignoreWord(target, word, all) {
			if (all) {
				Tools.each(editor.dom.select('span.mce-spellchecker-word'), function(item) {
					var text = item.innerText || item.textContent;

					if (text == word) {
						unwrap(item);
					}
				});
			} else {
				unwrap(target);
			}

			checkIfFinished();
		}

		function finish() {
			var i, nodes, node;

			started = false;
			node = editor.getBody();
			nodes = node.getElementsByTagName('span');
			i = nodes.length;
			while (i--) {
				node = nodes[i];
				if (node.getAttribute('data-mce-index')) {
					unwrap(node);
				}
			}

			editor.fire('SpellcheckEnd');
		}

		function selectMatch(index) {
			var nodes, i, spanElm, spanIndex = -1, startContainer, endContainer;

			index = "" + index;
			nodes = editor.getBody().getElementsByTagName("span");
			for (i = 0; i < nodes.length; i++) {
				spanElm = nodes[i];
				if (spanElm.className == "mce-spellchecker-word") {
					spanIndex = spanElm.getAttribute('data-mce-index');
					if (spanIndex === index) {
						spanIndex = index;

						if (!startContainer) {
							startContainer = spanElm.firstChild;
						}

						endContainer = spanElm.firstChild;
					}

					if (spanIndex !== index && endContainer) {
						break;
					}
				}
			}

			var rng = editor.dom.createRng();
			rng.setStart(startContainer, 0);
			rng.setEnd(endContainer, endContainer.length);
			editor.selection.setRng(rng);

			return rng;
		}

		editor.on('click', function(e) {
			if (e.target.className == "mce-spellchecker-word") {
				e.preventDefault();

				var rng = selectMatch(e.target.getAttribute('data-mce-index'));
				showSuggestions(e.target, rng.toString());
			}
		});

		editor.addMenuItem('spellchecker', {
			text: 'Spellcheck',
			context: 'tools',
			onclick: spellcheck,
			selectable: true,
			onPostRender: function() {
				var self = this;

				editor.on('SpellcheckStart SpellcheckEnd', function() {
					self.active(started);
				});
			}
		});

		editor.addButton('spellchecker', {
			tooltip: 'Spellcheck',
			onclick: spellcheck,
			onPostRender: function() {
				var self = this;

				editor.on('SpellcheckStart SpellcheckEnd', function() {
					self.active(started);
				});
			}
		});

		editor.on('remove', function() {
			if (suggestionsMenu) {
				suggestionsMenu.remove();
				suggestionsMenu = null;
			}
		});
	});
});

expose(["tinymce/spellcheckerplugin/DomTextMatcher","tinymce/spellcheckerplugin/Plugin"]);
})(this);