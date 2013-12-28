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

// Included from: js/tinymce/plugins/paste/classes/Utils.js

/**
 * Utils.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * This class contails various utility functions for the paste plugin.
 *
 * @class tinymce.pasteplugin.Clipboard
 * @private
 */
define("tinymce/pasteplugin/Utils", [
	"tinymce/util/Tools",
	"tinymce/html/DomParser",
	"tinymce/html/Schema"
], function(Tools, DomParser, Schema) {
	function filter(content, items) {
		Tools.each(items, function(v) {
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

		walk(domParser.parse(html));

		return text;
	}

	return {
		filter: filter,
		innerText: innerText
	};
});

// Included from: js/tinymce/plugins/paste/classes/Clipboard.js

/**
 * Clipboard.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * This class contains logic for getting HTML contents out of the clipboard.
 *
 * We need to make a lot of ugly hacks to get the contents out of the clipboard since
 * the W3C Clipboard API is broken in all browsers: Gecko/WebKit/Blink. We might rewrite
 * this the way those API:s stabilize.
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
define("tinymce/pasteplugin/Clipboard", [
	"tinymce/Env",
	"tinymce/util/VK",
	"tinymce/pasteplugin/Utils"
], function(Env, VK, Utils) {
	return function(editor) {
		var self = this, pasteBinElm, lastRng, keyboardPasteTimeStamp = 0;
		var pasteBinDefaultContent = '%MCEPASTEBIN%', keyboardPastePlainTextState;

		/**
		 * Pastes the specified HTML. This means that the HTML is filtered and then
		 * inserted at the current selection in the editor. It will also fire paste events
		 * for custom user filtering.
		 *
		 * @param {String} html HTML code to paste into the current selection.
		 */
		function pasteHtml(html) {
			var args, dom = editor.dom;

			args = editor.fire('BeforePastePreProcess', {content: html}); // Internal event used by Quirks
			args = editor.fire('PastePreProcess', args);
			html = args.content;

			if (!args.isDefaultPrevented()) {
				// User has bound PastePostProcess events then we need to pass it through a DOM node
				// This is not ideal but we don't want to let the browser mess up the HTML for example
				// some browsers add &nbsp; to P tags etc
				if (editor.hasEventListeners('PastePostProcess') && !args.isDefaultPrevented()) {
					// We need to attach the element to the DOM so Sizzle selectors work on the contents
					var tempBody = dom.add(editor.getBody(), 'div', {style: 'display:none'}, html);
					args = editor.fire('PastePostProcess', {node: tempBody});
					dom.remove(tempBody);
					html = args.node.innerHTML;
				}

				if (!args.isDefaultPrevented()) {
					editor.insertContent(html);
				}
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

			var startBlock = editor.dom.getParent(editor.selection.getStart(), editor.dom.isBlock);

			// Create start block html for example <p attr="value">
			var forcedRootBlockName = editor.settings.forced_root_block;
			var forcedRootBlockStartHtml;
			if (forcedRootBlockName) {
				forcedRootBlockStartHtml = editor.dom.createHTML(forcedRootBlockName, editor.settings.forced_root_block_attrs);
				forcedRootBlockStartHtml = forcedRootBlockStartHtml.substr(0, forcedRootBlockStartHtml.length - 3) + '>';
			}

			if ((startBlock && /^(PRE|DIV)$/.test(startBlock.nodeName)) || !forcedRootBlockName) {
				text = Utils.filter(text, [
					[/\n/g, "<br>"]
				]);
			} else {
				text = Utils.filter(text, [
					[/\n\n/g, "</p>" + forcedRootBlockStartHtml],
					[/^(.*<\/p>)(<p>)$/, forcedRootBlockStartHtml + '$1'],
					[/\n/g, "<br />"]
				]);

				if (text.indexOf('<p>') != -1) {
					text = forcedRootBlockStartHtml + text;
				}
			}

			pasteHtml(text);
		}

		/**
		 * Creates a paste bin element and moves the selection into that element. It will also move the element offscreen
		 * so that resize handles doesn't get produced on IE or Drag handles or Firefox.
		 */
		function createPasteBin() {
			var dom = editor.dom, body = editor.getBody(), viewport = editor.dom.getViewPort(editor.getWin());
			var height = editor.inline ? body.clientHeight : viewport.h;

			removePasteBin();

			// Create a pastebin
			pasteBinElm = dom.add(editor.getBody(), 'div', {
				id: "mcepastebin",
				contentEditable: true,
				"data-mce-bogus": "1",
				style: 'position: fixed; top: 20px;' +
					'width: 10px; height: ' + (height - 40) + 'px; overflow: hidden; opacity: 0'
			}, pasteBinDefaultContent);

			// Move paste bin out of sight since the controlSelection rect gets displayed otherwise
			dom.setStyle(pasteBinElm, 'left', dom.getStyle(body, 'direction', true) == 'rtl' ? 0xFFFF : -0xFFFF);

			// Prevent focus events from bubbeling fixed FocusManager issues
			dom.bind(pasteBinElm, 'beforedeactivate focusin focusout', function(e) {
				e.stopPropagation();
			});

			lastRng = editor.selection.getRng();
			pasteBinElm.focus();
			editor.selection.select(pasteBinElm, true);
		}

		/**
		 * Removes the paste bin if it exists.
		 */
		function removePasteBin() {
			if (pasteBinElm) {
				editor.dom.unbind(pasteBinElm);
				editor.dom.remove(pasteBinElm);

				if (lastRng) {
					editor.selection.setRng(lastRng);
				}
			}

			keyboardPastePlainTextState = false;
			pasteBinElm = lastRng = null;
		}

		/**
		 * Returns the contents of the paste bin as a HTML string.
		 *
		 * @return {String} Get the contents of the paste bin.
		 */
		function getPasteBinHtml() {
			return pasteBinElm ? pasteBinElm.innerHTML : pasteBinDefaultContent;
		}

		/**
		 * Gets various content types out of a datatransfer object.
		 *
		 * @param {DataTransfer} dataTransfer Event fired on paste.
		 * @return {Object} Object with mime types and data for those mime types.
		 */
		function getDataTransferItems(dataTransfer) {
			var data = {};

			if (dataTransfer && dataTransfer.types) {
				data['text/plain'] = dataTransfer.getData('Text');

				for (var i = 0; i < dataTransfer.types.length; i++) {
					var contentType = dataTransfer.types[i];
					data[contentType] = dataTransfer.getData(contentType);
				}
			}

			return data;
		}

		/**
		 * Gets various content types out of the Clipboard API. It will also get the
		 * plain text using older IE and WebKit API:s.
		 *
		 * @param {ClipboardEvent} clipboardEvent Event fired on paste.
		 * @return {Object} Object with mime types and data for those mime types.
		 */
		function getClipboardContent(clipboardEvent) {
			return getDataTransferItems(clipboardEvent.clipboardData || editor.getDoc().dataTransfer);
		}

		function getCaretRangeFromEvent(e) {
			var doc = editor.getDoc(), rng;

			if (doc.caretPositionFromPoint) {
				var point = doc.caretPositionFromPoint(e.pageX, e.pageY);
				rng = doc.createRange();
				rng.setStart(point.offsetNode, point.offset);
				rng.collapse(true);
			} else if (doc.caretRangeFromPoint) {
				rng = doc.caretRangeFromPoint(e.pageX, e.pageY);
			}

			return rng;
		}

		editor.on('keydown', function(e) {
			if (e.isDefaultPrevented()) {
				return;
			}

			// Ctrl+V or Shift+Insert
			if ((VK.metaKeyPressed(e) && e.keyCode == 86) || (e.shiftKey && e.keyCode == 45)) {
				keyboardPastePlainTextState = e.shiftKey && e.keyCode == 86;

				// Prevent undoManager keydown handler from making an undo level with the pastebin in it
				e.stopImmediatePropagation();

				keyboardPasteTimeStamp = new Date().getTime();

				// IE doesn't support Ctrl+Shift+V and it doesn't even produce a paste event
				// so lets fake a paste event and let IE use the execCommand/dataTransfer methods
				if (Env.ie && keyboardPastePlainTextState) {
					e.preventDefault();
					editor.fire('paste', {ieFake: true});
					return;
				}

				createPasteBin();
			}
		});

		editor.on('paste', function(e) {
			var clipboardContent = getClipboardContent(e);
			var isKeyBoardPaste = new Date().getTime() - keyboardPasteTimeStamp < 1000;
			var plainTextMode = self.pasteFormat == "text" || keyboardPastePlainTextState;

			// Not a keyboard paste prevent default paste and try to grab the clipboard contents using different APIs
			if (!isKeyBoardPaste) {
				e.preventDefault();
			}

			// Try IE only method if paste isn't a keyboard paste
			if (Env.ie && (!isKeyBoardPaste || e.ieFake)) {
				createPasteBin();

				editor.dom.bind(pasteBinElm, 'paste', function(e) {
					e.stopPropagation();
				});

				editor.getDoc().execCommand('Paste', false, null);
				clipboardContent["text/html"] = getPasteBinHtml();
			}

			setTimeout(function() {
				var html = getPasteBinHtml();

				// WebKit has a nice bug where it clones the paste bin if you paste from for example notepad
				if (pasteBinElm && pasteBinElm.firstChild && pasteBinElm.firstChild.id === 'mcepastebin') {
					plainTextMode = true;
				}

				removePasteBin();

				if (html == pasteBinDefaultContent || !isKeyBoardPaste) {
					html = clipboardContent['text/html'] || clipboardContent['text/plain'] || pasteBinDefaultContent;

					if (html == pasteBinDefaultContent) {
						if (!isKeyBoardPaste) {
							editor.windowManager.alert('Please use Ctrl+V/Cmd+V keyboard shortcuts to paste contents.');
						}

						return;
					}
				}

				if (plainTextMode) {
					pasteText(clipboardContent['text/plain'] || Utils.innerText(html));
				} else {
					pasteHtml(html);
				}
			}, 0);
		});

		editor.on('dragstart', function(e) {
			if (e.dataTransfer.types) {
				e.dataTransfer.setData('mce-internal', editor.selection.getContent());
			}
		});

		editor.on('drop', function(e) {
			var rng = getCaretRangeFromEvent(e);

			if (rng) {
				var dropContent = getDataTransferItems(e.dataTransfer);
				var content = dropContent['mce-internal'] || dropContent['text/html'] || dropContent['text/plain'];

				if (content) {
					e.preventDefault();

					editor.undoManager.transact(function() {
						if (dropContent['mce-internal']) {
							editor.execCommand('Delete');
						}

						editor.selection.setRng(rng);

						if (!dropContent['text/html']) {
							pasteText(content);
						} else {
							pasteHtml(content);
						}
					});
				}
			}
		});

		self.pasteHtml = pasteHtml;
		self.pasteText = pasteText;

		// Remove all data images from paste for example from Gecko
		// except internal images like video elements
		editor.on('preInit', function() {
			editor.parser.addNodeFilter('img', function(nodes) {
				if (!editor.settings.paste_data_images) {
					var i = nodes.length;

					while (i--) {
						var src = nodes[i].attributes.map.src;
						if (src && src.indexOf('data:image') === 0) {
							if (!nodes[i].attr('data-mce-object') && src !== Env.transparentSrc) {
								nodes[i].remove();
							}
						}
					}
				}
			});
		});

		// Fix for #6504 we need to remove the paste bin on IE if the user paste in a file
		editor.on('PreProcess', function() {
			editor.dom.remove(editor.dom.get('mcepastebin'));
		});
	};
});

// Included from: js/tinymce/plugins/paste/classes/WordFilter.js

/**
 * WordFilter.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * This class parses word HTML into proper TinyMCE markup.
 *
 * @class tinymce.pasteplugin.Quirks
 * @private
 */
define("tinymce/pasteplugin/WordFilter", [
	"tinymce/util/Tools",
	"tinymce/html/DomParser",
	"tinymce/html/Schema",
	"tinymce/html/Serializer",
	"tinymce/html/Node",
	"tinymce/pasteplugin/Utils"
], function(Tools, DomParser, Schema, Serializer, Node, Utils) {
	function isWordContent(content) {
		return (/<font face="Times New Roman"|class="?Mso|style="[^"]*\bmso-|style='[^'']*\bmso-|w:WordDocument/i).test(content);
	}

	function WordFilter(editor) {
		var settings = editor.settings;

		editor.on('BeforePastePreProcess', function(e) {
			var content = e.content, retainStyleProperties, validStyles;

			retainStyleProperties = settings.paste_retain_style_properties;
			if (retainStyleProperties) {
				validStyles = Tools.makeMap(retainStyleProperties);
			}

			/**
			 * Converts fake bullet and numbered lists to real semantic OL/UL.
			 *
			 * @param {tinymce.html.Node} node Root node to convert children of.
			 */
			function convertFakeListsToProperLists(node) {
				var currentListNode, prevListNode, lastLevel = 1;

				function convertParagraphToLi(paragraphNode, listStartTextNode, listName, start) {
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
					listStartTextNode.value = '';

					var nextNode = listStartTextNode.next;
					if (nextNode && nextNode.type == 3) {
						nextNode.value = nextNode.value.replace(/^\u00a0+/, '');
					}

					// Append list to previous list if it exists
					if (level > lastLevel && prevListNode) {
						prevListNode.lastChild.append(currentListNode);
					}

					lastLevel = level;
				}

				var paragraphs = node.getAll('p');

				for (var i = 0; i < paragraphs.length; i++) {
					node = paragraphs[i];

					if (node.name == 'p' && node.firstChild) {
						// Find first text node in paragraph
						var nodeText = '';
						var listStartTextNode = node.firstChild;

						while (listStartTextNode) {
							nodeText = listStartTextNode.value;
							if (nodeText) {
								break;
							}

							listStartTextNode = listStartTextNode.firstChild;
						}

						// Detect unordered lists look for bullets
						if (/^\s*[\u2022\u00b7\u00a7\u00d8\u25CF]\s*$/.test(nodeText)) {
							convertParagraphToLi(node, listStartTextNode, 'ul');
							continue;
						}

						// Detect ordered lists 1., a. or ixv.
						if (/^\s*\w+\.$/.test(nodeText)) {
							// Parse OL start number
							var matches = /([0-9])\./.exec(nodeText);
							var start = 1;
							if (matches) {
								start = parseInt(matches[1], 10);
							}

							convertParagraphToLi(node, listStartTextNode, 'ol', start);
							continue;
						}

						currentListNode = null;
					}
				}
			}

			function filterStyles(node, styleValue) {
				// Parse out list indent level for lists
				if (node.name === 'p') {
					var matches = /mso-list:\w+ \w+([0-9]+)/.exec(styleValue);

					if (matches) {
						node._listLevel = parseInt(matches[1], 10);
					}
				}

				if (editor.getParam("paste_retain_style_properties", "none")) {
					var outputStyle = "";

					Tools.each(editor.dom.parseStyle(styleValue), function(value, name) {
						// Convert various MS styles to W3C styles
						switch (name) {
							case "horiz-align":
								name = "text-align";
								return;

							case "vert-align":
								name = "vertical-align";
								return;

							case "font-color":
							case "mso-foreground":
								name = "color";
								return;

							case "mso-background":
							case "mso-highlight":
								name = "background";
								break;
						}

						// Output only valid styles
						if (retainStyleProperties == "all" || (validStyles && validStyles[name])) {
							outputStyle += name + ':' + value + ';';
						}
					});

					if (outputStyle) {
						return outputStyle;
					}
				}

				return null;
			}

			if (settings.paste_enable_default_filters === false) {
				return;
			}

			// Detect is the contents is Word junk HTML
			if (isWordContent(e.content)) {
				e.wordContent = true; // Mark it for other processors

				// Remove basic Word junk
				content = Utils.filter(content, [
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
						function(str, spaces) {
							return (spaces.length > 0) ?
								spaces.replace(/./, " ").slice(Math.floor(spaces.length/2)).split("").join("\u00a0") : "";
						}
					]
				]);

				var validElements = settings.paste_word_valid_elements;
				if (!validElements) {
					validElements = '@[style],-strong/b,-em/i,-span,-p,-ol,-ul,-li,-h1,-h2,-h3,-h4,-h5,-h6,' +
						'-table,-tr,-td[colspan|rowspan],-th,-thead,-tfoot,-tbody,-a[href|name],sub,sup,strike,br';
				}

				// Setup strict schema
				var schema = new Schema({
					valid_elements: validElements
				});

				// Parse HTML into DOM structure
				var domParser = new DomParser({}, schema);

				domParser.addAttributeFilter('style', function(nodes) {
					var i = nodes.length, node;

					while (i--) {
						node = nodes[i];
						node.attr('style', filterStyles(node, node.attr('style')));

						// Remove pointess spans
						if (node.name == 'span' && !node.attributes.length) {
							node.unwrap();
						}
					}
				});

				domParser.addNodeFilter('a', function(nodes) {
					var i = nodes.length, node, href, name;

					while (i--) {
						node = nodes[i];
						href = node.attr('href');
						name = node.attr('name');

						if (href && href.indexOf('file://') === 0) {
							href = href.split('#')[1];
							if (href) {
								href = '#' + href;
							}
						}

						if (!href && !name) {
							node.unwrap();
						} else {
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
				convertFakeListsToProperLists(rootNode);

				// Serialize DOM back to HTML
				e.content = new Serializer({}, schema).serialize(rootNode);
			}
		});
	}

	WordFilter.isWordContent = isWordContent;

	return WordFilter;
});

// Included from: js/tinymce/plugins/paste/classes/Quirks.js

/**
 * Quirks.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
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
define("tinymce/pasteplugin/Quirks", [
	"tinymce/Env",
	"tinymce/util/Tools",
	"tinymce/pasteplugin/WordFilter",
	"tinymce/pasteplugin/Utils"
], function(Env, Tools, WordFilter, Utils) {
	"use strict";

	return function(editor) {
		function addPreProcessFilter(filterFunc) {
			editor.on('BeforePastePreProcess', function(e) {
				e.content = filterFunc(e.content);
			});
		}

		/**
		 * Removes WebKit fragment comments and converted-space spans.
		 *
		 * This:
		 *   <!--StartFragment-->a<span class="Apple-converted-space">&nbsp;</span>b<!--EndFragment-->
		 *
		 * Becomes:
		 *   a&nbsp;b
		 */
		function removeWebKitFragments(html) {
			html = Utils.filter(html, [
				/^[\s\S]*<!--StartFragment-->|<!--EndFragment-->[\s\S]*$/g,        // WebKit fragment
				[/<span class="Apple-converted-space">\u00a0<\/span>/g, '\u00a0'], // WebKit &nbsp;
				/<br>$/															   // Traling BR elements
			]);

			return html;
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
		function removeExplorerBrElementsAfterBlocks(html) {
			// Only filter word specific content
			if (!WordFilter.isWordContent(html)) {
				return html;
			}

			// Produce block regexp based on the block elements in schema
			var blockElements = [];

			Tools.each(editor.schema.getBlockElements(), function(block, blockName) {
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
		 * WebKit has a nasty bug where the all runtime styles gets added to style attributes when copy/pasting contents.
		 * This fix solves that by simply removing the whole style attribute.
		 *
		 * Todo: This can be made smarter. Keeping styles that override existing ones etc.
		 *
		 * @param {String} content Content that needs to be processed.
		 * @return {String} Processed contents.
		 */
		function removeWebKitStyles(content) {
			if (editor.settings.paste_remove_styles || editor.settings.paste_remove_styles_if_webkit !== false) {
				content = content.replace(/ style=\"[^\"]+\"/g, '');
			}

			return content;
		}

		// Sniff browsers and apply fixes since we can't feature detect
		if (Env.webkit) {
			addPreProcessFilter(removeWebKitStyles);
			addPreProcessFilter(removeWebKitFragments);
		}

		if (Env.ie) {
			addPreProcessFilter(removeExplorerBrElementsAfterBlocks);
		}
	};
});

// Included from: js/tinymce/plugins/paste/classes/Plugin.js

/**
 * Plugin.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/**
 * This class contains the tinymce plugin logic for the paste plugin.
 *
 * @class tinymce.pasteplugin.Plugin
 * @private
 */
define("tinymce/pasteplugin/Plugin", [
	"tinymce/PluginManager",
	"tinymce/pasteplugin/Clipboard",
	"tinymce/pasteplugin/WordFilter",
	"tinymce/pasteplugin/Quirks"
], function(PluginManager, Clipboard, WordFilter, Quirks) {
	var userIsInformed;

	PluginManager.add('paste', function(editor) {
		var self = this, clipboard, settings = editor.settings;

		function togglePlainTextPaste() {
			if (clipboard.pasteFormat == "text") {
				this.active(false);
				clipboard.pasteFormat = "html";
			} else {
				clipboard.pasteFormat = "text";
				this.active(true);

				if (!userIsInformed) {
					editor.windowManager.alert(
						'Paste is now in plain text mode. Contents will now ' +
						'be pasted as plain text until you toggle this option off.'
					);

					userIsInformed = true;
				}
			}
		}

		self.clipboard = clipboard = new Clipboard(editor);
		self.quirks = new Quirks(editor);
		self.wordFilter = new WordFilter(editor);

		if (editor.settings.paste_as_text) {
			self.clipboard.pasteFormat = "text";
		}

		if (settings.paste_preprocess) {
			editor.on('PastePreProcess', function(e) {
				settings.paste_preprocess.call(self, self, e);
			});
		}

		if (settings.paste_postprocess) {
			editor.on('PastePostProcess', function(e) {
				settings.paste_postprocess.call(self, self, e);
			});
		}

		editor.addCommand('mceInsertClipboardContent', function(ui, value) {
			if (value.content) {
				self.clipboard.pasteHtml(value.content);
			}

			if (value.text) {
				self.clipboard.pasteText(value.text);
			}
		});

		// Block all drag/drop events
		if (editor.paste_block_drop) {
			editor.on('dragend dragover draggesture dragdrop drop drag', function(e) {
				e.preventDefault();
				e.stopPropagation();
			});
		}

		// Prevent users from dropping data images on Gecko
		if (!editor.settings.paste_data_images) {
			editor.on('drop', function(e) {
				var dataTransfer = e.dataTransfer;

				if (dataTransfer && dataTransfer.files && dataTransfer.files.length > 0) {
					e.preventDefault();
				}
			});
		}

		editor.addButton('pastetext', {
			icon: 'pastetext',
			tooltip: 'Paste as text',
			onclick: togglePlainTextPaste,
			active: self.clipboard.pasteFormat == "text"
		});

		editor.addMenuItem('pastetext', {
			text: 'Paste as text',
			selectable: true,
			active: clipboard.pasteFormat,
			onclick: togglePlainTextPaste
		});
	});
});

expose(["tinymce/pasteplugin/Utils","tinymce/pasteplugin/Clipboard","tinymce/pasteplugin/WordFilter","tinymce/pasteplugin/Quirks","tinymce/pasteplugin/Plugin"]);
})(this);