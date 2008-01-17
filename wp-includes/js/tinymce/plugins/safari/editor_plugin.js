/**
 * $Id: editor_plugin_src.js 264 2007-04-26 20:53:09Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	var Event = tinymce.dom.Event, grep = tinymce.grep, each = tinymce.each, inArray = tinymce.inArray, isOldWebKit = tinymce.isOldWebKit;

	tinymce.create('tinymce.plugins.Safari', {
		init : function(ed) {
			var t = this, dom;

			// Ignore on non webkit
			if (!tinymce.isWebKit)
				return;

			t.editor = ed;
			t.webKitFontSizes = ['x-small', 'small', 'medium', 'large', 'x-large', 'xx-large', '-webkit-xxx-large'];
			t.namedFontSizes = ['xx-small', 'x-small','small','medium','large','x-large', 'xx-large'];

			// Safari will crash if the build in createlink command is used
/*			ed.addCommand('CreateLink', function(u, v) {
				ed.execCommand("mceInsertContent", false, '<a href="' + dom.encode(v) + '">' + ed.selection.getContent() + '</a>');
			});*/

			// Workaround for FormatBlock bug, http://bugs.webkit.org/show_bug.cgi?id=16004
			ed.addCommand('FormatBlock', function(u, v) {
				var dom = ed.dom, e = dom.getParent(ed.selection.getNode(), dom.isBlock);

				if (e)
					dom.replace(dom.create(v), e, 1);
				else
					ed.getDoc().execCommand("FormatBlock", false, v);
			});

			// Workaround for InsertHTML bug, http://bugs.webkit.org/show_bug.cgi?id=16382
			ed.addCommand('mceInsertContent', function(u, v) {
				ed.getDoc().execCommand("InsertText", false, 'mce_marker');
				ed.getBody().innerHTML = ed.getBody().innerHTML.replace(/mce_marker/g, v + '<span id="_mce_tmp">XX</span>');
				ed.selection.select(ed.dom.get('_mce_tmp'));
				ed.getDoc().execCommand("Delete", false, ' ');
			});

			// Workaround for List ID bug, http://bugs.webkit.org/show_bug.cgi?id=16004
/*			function addList(c) {
				var cb = Event.add(ed.getDoc(), 'DOMNodeInserted', function(e) {
					e = e.target;

					if (e.nodeName == 'OL' || e.nodeName == 'UL')
						e.id = '';
				});

				ed.getDoc().execCommand(c, false, false);
				Event.remove(ed.getDoc(), 'DOMNodeInserted', cb);
			};

			ed.addCommand('InsertUnorderedList', function() {addList('InsertUnorderedList');});
			ed.addCommand('InsertOrderedList', function() {addList('InsertOrderedList');});*/

			// Safari returns incorrect values
			ed.addQueryValueHandler('FontSize', function(u, v) {
				var e, v;

				// Check for the real font size at the start of selection
				if ((e = ed.dom.getParent(ed.selection.getStart(), 'span')) && (v = e.style.fontSize))
					return tinymce.inArray(t.namedFontSizes, v) + 1;

				// Check for the real font size at the end of selection
				if ((e = ed.dom.getParent(ed.selection.getEnd(), 'span')) && (v = e.style.fontSize))
					return tinymce.inArray(t.namedFontSizes, v) + 1;

				// Return default value it's better than nothing right!
				return ed.getDoc().queryCommandValue('FontSize');
			});

			// Safari returns incorrect values
			ed.addQueryValueHandler('FontName', function(u, v) {
				var e, v;

				// Check for the real font name at the start of selection
				if ((e = ed.dom.getParent(ed.selection.getStart(), 'span')) && (v = e.style.fontFamily))
					return v.replace(/, /g, ',');

				// Check for the real font name at the end of selection
				if ((e = ed.dom.getParent(ed.selection.getEnd(), 'span')) && (v = e.style.fontFamily))
					return v.replace(/, /g, ',');

				// Return default value it's better than nothing right!
				return ed.getDoc().queryCommandValue('FontName');
			});

			// Workaround for bug, http://bugs.webkit.org/show_bug.cgi?id=12250
			ed.onClick.add(function(ed, e) {
				e = e.target;

				if (e.nodeName == 'IMG') {
					t.selElm = e;
					ed.selection.select(e);
				} else
					t.selElm = null;
			});

			ed.onBeforeExecCommand.add(function(ed, c, b) {
				var r = t.bookmarkRng;

				// Restore selection
				if (r) {
					ed.selection.setRng(r);
					t.bookmarkRng = null;
					//console.debug('restore', r.startContainer, r.startOffset, r.endContainer, r.endOffset);
				}
			});

			ed.onInit.add(function() {
				t._fixWebKitSpans();

				ed.windowManager.onOpen.add(function() {
					var r = ed.selection.getRng();

					// Store selection if valid
					if (r.startContainer != ed.getDoc()) {
						t.bookmarkRng = r.cloneRange();
						//console.debug('store', r.startContainer, r.startOffset, r.endContainer, r.endOffset);
					}
				});

				ed.windowManager.onClose.add(function() {
					t.bookmarkRng = null;
				});

				if (isOldWebKit)
					t._patchSafari2x(ed);
			});

			ed.onSetContent.add(function() {
				dom = ed.dom;

				// Convert strong,b,em,u,strike to spans
				each(['strong','b','em','u','strike','sub','sup','a'], function(v) {
					each(grep(dom.select(v)).reverse(), function(n) {
						var nn = n.nodeName.toLowerCase(), st;

						// Convert anchors into images
						if (nn == 'a') {
							if (n.name)
								dom.replace(dom.create('img', {mce_name : 'a', name : n.name, 'class' : 'mceItemAnchor'}), n);

							return;
						}

						switch (nn) {
							case 'b':
							case 'strong':
								if (nn == 'b')
									nn = 'strong';

								st = 'font-weight: bold;';
								break;

							case 'em':
								st = 'font-style: italic;';
								break;

							case 'u':
								st = 'text-decoration: underline;';
								break;

							case 'sub':
								st = 'vertical-align: sub;';
								break;

							case 'sup':
								st = 'vertical-align: super;';
								break;

							case 'strike':
								st = 'text-decoration: line-through;';
								break;
						}

						dom.replace(dom.create('span', {mce_name : nn, style : st, 'class' : 'Apple-style-span'}), n, 1);
					});
				});
			});

			ed.onPreProcess.add(function(ed, o) {
				dom = ed.dom;

				each(grep(o.node.getElementsByTagName('span')).reverse(), function(n) {
					var v, bg;

					if (o.get) {
						if (dom.hasClass(n, 'Apple-style-span')) {
							bg = n.style.backgroundColor;

							switch (dom.getAttrib(n, 'mce_name')) {
								case 'font':
									if (!ed.settings.convert_fonts_to_spans)
										dom.setAttrib(n, 'style', '');
									break;

								case 'strong':
								case 'em':
								case 'sub':
								case 'sup':
									dom.setAttrib(n, 'style', '');
									break;

								case 'strike':
								case 'u':
									if (!ed.settings.inline_styles)
										dom.setAttrib(n, 'style', '');
									else
										dom.setAttrib(n, 'mce_name', '');

									break;

								default:
									if (!ed.settings.inline_styles)
										dom.setAttrib(n, 'style', '');
							}


							if (bg)
								n.style.backgroundColor = bg;
						}
					}

					if (dom.hasClass(n, 'mceItemRemoved'))
						dom.remove(n, 1);
				});
			});

			ed.onPostProcess.add(function(ed, o) {
				// Safari adds BR at end of all block elements
				o.content = o.content.replace(/<br \/><\/(h[1-6]|div|p|address|pre)>/g, '</$1>');

				// Safari adds id="undefined" to HR elements
				o.content = o.content.replace(/ id=\"undefined\"/g, '');
			});
		},

		_fixWebKitSpans : function() {
			var t = this, ed = t.editor;

			if (!isOldWebKit) {
				// Use mutator events on new WebKit
				Event.add(ed.getDoc(), 'DOMNodeInserted', function(e) {
					e = e.target;

					if (e && e.nodeType == 1)
						t._fixAppleSpan(e);
				});
			} else {
				// Do post command processing in old WebKit since the browser crashes on Mutator events :(
				ed.onExecCommand.add(function() {
					each(ed.dom.select('span'), function(n) {
						t._fixAppleSpan(n);
					});

					ed.nodeChanged();
				});
			}
		},

		_fixAppleSpan : function(e) {
			var ed = this.editor, dom = ed.dom, fz = this.webKitFontSizes, fzn = this.namedFontSizes, s = ed.settings, st, p;

			if (dom.getAttrib(e, 'mce_fixed'))
				return;

			// Handle Apple style spans
			if (e.nodeName == 'SPAN' && e.className == 'Apple-style-span') {
				st = e.style;

				if (!s.convert_fonts_to_spans) {
					if (st.fontSize) {
						dom.setAttrib(e, 'mce_name', 'font');
						dom.setAttrib(e, 'size', inArray(fz, st.fontSize) + 1);
					}

					if (st.fontFamily) {
						dom.setAttrib(e, 'mce_name', 'font');
						dom.setAttrib(e, 'face', st.fontFamily);
					}

					if (st.color) {
						dom.setAttrib(e, 'mce_name', 'font');
						dom.setAttrib(e, 'color', dom.toHex(st.color));
					}

					if (st.backgroundColor) {
						dom.setAttrib(e, 'mce_name', 'font');
						dom.setStyle(e, 'background-color', st.backgroundColor);
					}
				} else {
					if (st.fontSize)
						dom.setStyle(e, 'fontSize', fzn[inArray(fz, st.fontSize)]);
				}

				if (st.fontWeight == 'bold')
					dom.setAttrib(e, 'mce_name', 'strong');

				if (st.fontStyle == 'italic')
					dom.setAttrib(e, 'mce_name', 'em');

				if (st.textDecoration == 'underline')
					dom.setAttrib(e, 'mce_name', 'u');

				if (st.textDecoration == 'line-through')
					dom.setAttrib(e, 'mce_name', 'strike');

				if (st.verticalAlign == 'super')
					dom.setAttrib(e, 'mce_name', 'sup');

				if (st.verticalAlign == 'sub')
					dom.setAttrib(e, 'mce_name', 'sub');

				dom.setAttrib(e, 'mce_fixed', '1');
			}
		},

		_patchSafari2x : function(ed) {
			var t = this, setContent, getNode, dom = ed.dom, lr;

			// Inline dialogs
			if (ed.windowManager.onBeforeOpen) {
				ed.windowManager.onBeforeOpen.add(function() {
					r = ed.selection.getRng();
				});
			}

			// Fake select on 2.x
			ed.selection.select = function(n) {
				this.getSel().setBaseAndExtent(n, 0, n, 1);
			};

			getNode = ed.selection.getNode;
			ed.selection.getNode = function() {
				return t.selElm || getNode.call(this);
			};

			// Fake range on Safari 2.x
			ed.selection.getRng = function() {
				var t = this, s = t.getSel(), d = ed.getDoc(), r, rb, ra, di;

				// Fake range on Safari 2.x
				if (s.anchorNode) {
					r = d.createRange();

					try {
						// Setup before range
						rb = d.createRange();
						rb.setStart(s.anchorNode, s.anchorOffset);
						rb.collapse(1);

						// Setup after range
						ra = d.createRange();
						ra.setStart(s.focusNode, s.focusOffset);
						ra.collapse(1);

						// Setup start/end points by comparing locations
						di = rb.compareBoundaryPoints(rb.START_TO_END, ra) < 0;
						r.setStart(di ? s.anchorNode : s.focusNode, di ? s.anchorOffset : s.focusOffset);
						r.setEnd(di ? s.focusNode : s.anchorNode, di ? s.focusOffset : s.anchorOffset);

						lr = r;
					} catch (ex) {
						// Sometimes fails, at least we tried to do it by the book. I hope Safari 2.x will go disappear soooon!!!
					}
				}

				return r || lr;
			};

			// Fix setContent so it works
			setContent = ed.selection.setContent;
			ed.selection.setContent = function(h, s) {
				var r = this.getRng(), b;

				try {
					setContent.call(this, h, s);
				} catch (ex) {
					// Workaround for Safari 2.x
					b = dom.create('body');
					b.innerHTML = h;

					each(b.childNodes, function(n) {
						r.insertNode(n.cloneNode(true));
					});
				}
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('safari', tinymce.plugins.Safari);
})();

