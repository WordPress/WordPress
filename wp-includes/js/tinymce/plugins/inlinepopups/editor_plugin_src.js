/**
 * editor_plugin_src.js
 *
 * Copyright 2009, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://tinymce.moxiecode.com/license
 * Contributing: http://tinymce.moxiecode.com/contributing
 */

(function() {
	var DOM = tinymce.DOM, Element = tinymce.dom.Element, Event = tinymce.dom.Event, each = tinymce.each, is = tinymce.is;

	tinymce.create('tinymce.plugins.InlinePopups', {
		init : function(ed, url) {
			// Replace window manager
			ed.onBeforeRenderUI.add(function() {
				ed.windowManager = new tinymce.InlineWindowManager(ed);
				DOM.loadCSS(url + '/skins/' + (ed.settings.inlinepopups_skin || 'clearlooks2') + "/window.css");
			});
		},

		getInfo : function() {
			return {
				longname : 'InlinePopups',
				author : 'Moxiecode Systems AB',
				authorurl : 'http://tinymce.moxiecode.com',
				infourl : 'http://wiki.moxiecode.com/index.php/TinyMCE:Plugins/inlinepopups',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	tinymce.create('tinymce.InlineWindowManager:tinymce.WindowManager', {
		InlineWindowManager : function(ed) {
			var t = this;

			t.parent(ed);
			t.zIndex = 300000;
			t.count = 0;
			t.windows = {};
		},

		open : function(f, p) {
			var t = this, id, opt = '', ed = t.editor, dw = 0, dh = 0, vp, po, mdf, clf, we, w, u, parentWindow;

			f = f || {};
			p = p || {};

			// Run native windows
			if (!f.inline)
				return t.parent(f, p);

			parentWindow = t._frontWindow();
			if (parentWindow && DOM.get(parentWindow.id + '_ifr')) {
				parentWindow.focussedElement = DOM.get(parentWindow.id + '_ifr').contentWindow.document.activeElement;
			}
			
			// Only store selection if the type is a normal window
			if (!f.type)
				t.bookmark = ed.selection.getBookmark(1);

			id = DOM.uniqueId();
			vp = DOM.getViewPort();
			f.width = parseInt(f.width || 320);
			f.height = parseInt(f.height || 240) + (tinymce.isIE ? 8 : 0);
			f.min_width = parseInt(f.min_width || 150);
			f.min_height = parseInt(f.min_height || 100);
			f.max_width = parseInt(f.max_width || 2000);
			f.max_height = parseInt(f.max_height || 2000);
			f.left = f.left || Math.round(Math.max(vp.x, vp.x + (vp.w / 2.0) - (f.width / 2.0)));
			f.top = f.top || Math.round(Math.max(vp.y, vp.y + (vp.h / 2.0) - (f.height / 2.0)));
			f.movable = f.resizable = true;
			p.mce_width = f.width;
			p.mce_height = f.height;
			p.mce_inline = true;
			p.mce_window_id = id;
			p.mce_auto_focus = f.auto_focus;

			// Transpose
//			po = DOM.getPos(ed.getContainer());
//			f.left -= po.x;
//			f.top -= po.y;

			t.features = f;
			t.params = p;
			t.onOpen.dispatch(t, f, p);

			if (f.type) {
				opt += ' mceModal';

				if (f.type)
					opt += ' mce' + f.type.substring(0, 1).toUpperCase() + f.type.substring(1);

				f.resizable = false;
			}

			if (f.statusbar)
				opt += ' mceStatusbar';

			if (f.resizable)
				opt += ' mceResizable';

			if (f.minimizable)
				opt += ' mceMinimizable';

			if (f.maximizable)
				opt += ' mceMaximizable';

			if (f.movable)
				opt += ' mceMovable';

			// Create DOM objects
			t._addAll(DOM.doc.body, 
				['div', {id : id, role : 'dialog', 'aria-labelledby': f.type ? id + '_content' : id + '_title', 'class' : (ed.settings.inlinepopups_skin || 'clearlooks2') + (tinymce.isIE && window.getSelection ? ' ie9' : ''), style : 'width:100px;height:100px'}, 
					['div', {id : id + '_wrapper', 'class' : 'mceWrapper' + opt},
						['div', {id : id + '_top', 'class' : 'mceTop'}, 
							['div', {'class' : 'mceLeft'}],
							['div', {'class' : 'mceCenter'}],
							['div', {'class' : 'mceRight'}],
							['span', {id : id + '_title'}, f.title || '']
						],

						['div', {id : id + '_middle', 'class' : 'mceMiddle'}, 
							['div', {id : id + '_left', 'class' : 'mceLeft', tabindex : '0'}],
							['span', {id : id + '_content'}],
							['div', {id : id + '_right', 'class' : 'mceRight', tabindex : '0'}]
						],

						['div', {id : id + '_bottom', 'class' : 'mceBottom'},
							['div', {'class' : 'mceLeft'}],
							['div', {'class' : 'mceCenter'}],
							['div', {'class' : 'mceRight'}],
							['span', {id : id + '_status'}, 'Content']
						],

						['a', {'class' : 'mceMove', tabindex : '-1', href : 'javascript:;'}],
						['a', {'class' : 'mceMin', tabindex : '-1', href : 'javascript:;', onmousedown : 'return false;'}],
						['a', {'class' : 'mceMax', tabindex : '-1', href : 'javascript:;', onmousedown : 'return false;'}],
						['a', {'class' : 'mceMed', tabindex : '-1', href : 'javascript:;', onmousedown : 'return false;'}],
						['a', {'class' : 'mceClose', tabindex : '-1', href : 'javascript:;', onmousedown : 'return false;'}],
						['a', {id : id + '_resize_n', 'class' : 'mceResize mceResizeN', tabindex : '-1', href : 'javascript:;'}],
						['a', {id : id + '_resize_s', 'class' : 'mceResize mceResizeS', tabindex : '-1', href : 'javascript:;'}],
						['a', {id : id + '_resize_w', 'class' : 'mceResize mceResizeW', tabindex : '-1', href : 'javascript:;'}],
						['a', {id : id + '_resize_e', 'class' : 'mceResize mceResizeE', tabindex : '-1', href : 'javascript:;'}],
						['a', {id : id + '_resize_nw', 'class' : 'mceResize mceResizeNW', tabindex : '-1', href : 'javascript:;'}],
						['a', {id : id + '_resize_ne', 'class' : 'mceResize mceResizeNE', tabindex : '-1', href : 'javascript:;'}],
						['a', {id : id + '_resize_sw', 'class' : 'mceResize mceResizeSW', tabindex : '-1', href : 'javascript:;'}],
						['a', {id : id + '_resize_se', 'class' : 'mceResize mceResizeSE', tabindex : '-1', href : 'javascript:;'}]
					]
				]
			);

			DOM.setStyles(id, {top : -10000, left : -10000});

			// Fix gecko rendering bug, where the editors iframe messed with window contents
			if (tinymce.isGecko)
				DOM.setStyle(id, 'overflow', 'auto');

			// Measure borders
			if (!f.type) {
				dw += DOM.get(id + '_left').clientWidth;
				dw += DOM.get(id + '_right').clientWidth;
				dh += DOM.get(id + '_top').clientHeight;
				dh += DOM.get(id + '_bottom').clientHeight;
			}

			// Resize window
			DOM.setStyles(id, {top : f.top, left : f.left, width : f.width + dw, height : f.height + dh});

			u = f.url || f.file;
			if (u) {
				if (tinymce.relaxedDomain)
					u += (u.indexOf('?') == -1 ? '?' : '&') + 'mce_rdomain=' + tinymce.relaxedDomain;

				u = tinymce._addVer(u);
			}

			if (!f.type) {
				DOM.add(id + '_content', 'iframe', {id : id + '_ifr', src : 'javascript:""', frameBorder : 0, style : 'border:0;width:10px;height:10px'});
				DOM.setStyles(id + '_ifr', {width : f.width, height : f.height});
				DOM.setAttrib(id + '_ifr', 'src', u);
			} else {
				DOM.add(id + '_wrapper', 'a', {id : id + '_ok', 'class' : 'mceButton mceOk', href : 'javascript:;', onmousedown : 'return false;'}, 'Ok');

				if (f.type == 'confirm')
					DOM.add(id + '_wrapper', 'a', {'class' : 'mceButton mceCancel', href : 'javascript:;', onmousedown : 'return false;'}, 'Cancel');

				DOM.add(id + '_middle', 'div', {'class' : 'mceIcon'});
				DOM.setHTML(id + '_content', f.content.replace('\n', '<br />'));
				
				Event.add(id, 'keyup', function(evt) {
					var VK_ESCAPE = 27;
					if (evt.keyCode === VK_ESCAPE) {
						f.button_func(false);
						return Event.cancel(evt);
					}
				});

				Event.add(id, 'keydown', function(evt) {
					var cancelButton, VK_TAB = 9;
					if (evt.keyCode === VK_TAB) {
						cancelButton = DOM.select('a.mceCancel', id + '_wrapper')[0];
						if (cancelButton && cancelButton !== evt.target) {
							cancelButton.focus();
						} else {
							DOM.get(id + '_ok').focus();
						}
						return Event.cancel(evt);
					}
				});
			}

			// Register events
			mdf = Event.add(id, 'mousedown', function(e) {
				var n = e.target, w, vp;

				w = t.windows[id];
				t.focus(id);

				if (n.nodeName == 'A' || n.nodeName == 'a') {
					if (n.className == 'mceClose') {
						t.close(null, id);
						return Event.cancel(e);
					} else if (n.className == 'mceMax') {
						w.oldPos = w.element.getXY();
						w.oldSize = w.element.getSize();

						vp = DOM.getViewPort();

						// Reduce viewport size to avoid scrollbars
						vp.w -= 2;
						vp.h -= 2;

						w.element.moveTo(vp.x, vp.y);
						w.element.resizeTo(vp.w, vp.h);
						DOM.setStyles(id + '_ifr', {width : vp.w - w.deltaWidth, height : vp.h - w.deltaHeight});
						DOM.addClass(id + '_wrapper', 'mceMaximized');
					} else if (n.className == 'mceMed') {
						// Reset to old size
						w.element.moveTo(w.oldPos.x, w.oldPos.y);
						w.element.resizeTo(w.oldSize.w, w.oldSize.h);
						w.iframeElement.resizeTo(w.oldSize.w - w.deltaWidth, w.oldSize.h - w.deltaHeight);

						DOM.removeClass(id + '_wrapper', 'mceMaximized');
					} else if (n.className == 'mceMove')
						return t._startDrag(id, e, n.className);
					else if (DOM.hasClass(n, 'mceResize'))
						return t._startDrag(id, e, n.className.substring(13));
				}
			});

			clf = Event.add(id, 'click', function(e) {
				var n = e.target;

				t.focus(id);

				if (n.nodeName == 'A' || n.nodeName == 'a') {
					switch (n.className) {
						case 'mceClose':
							t.close(null, id);
							return Event.cancel(e);

						case 'mceButton mceOk':
						case 'mceButton mceCancel':
							f.button_func(n.className == 'mceButton mceOk');
							return Event.cancel(e);
					}
				}
			});
			
			// Make sure the tab order loops within the dialog.
			Event.add([id + '_left', id + '_right'], 'focus', function(evt) {
				var iframe = DOM.get(id + '_ifr');
				if (iframe) {
					var body = iframe.contentWindow.document.body;
					var focusable = DOM.select(':input:enabled,*[tabindex=0]', body);
					if (evt.target.id === (id + '_left')) {
						focusable[focusable.length - 1].focus();
					} else {
						focusable[0].focus();
					}
				} else {
					DOM.get(id + '_ok').focus();
				}
			});
			
			// Add window
			w = t.windows[id] = {
				id : id,
				mousedown_func : mdf,
				click_func : clf,
				element : new Element(id, {blocker : 1, container : ed.getContainer()}),
				iframeElement : new Element(id + '_ifr'),
				features : f,
				deltaWidth : dw,
				deltaHeight : dh
			};

			w.iframeElement.on('focus', function() {
				t.focus(id);
			});

			// Setup blocker
			if (t.count == 0 && t.editor.getParam('dialog_type', 'modal') == 'modal') {
				DOM.add(DOM.doc.body, 'div', {
					id : 'mceModalBlocker',
					'class' : (t.editor.settings.inlinepopups_skin || 'clearlooks2') + '_modalBlocker',
					style : {zIndex : t.zIndex - 1}
				});

				DOM.show('mceModalBlocker'); // Reduces flicker in IE
				DOM.setAttrib(DOM.doc.body, 'aria-hidden', 'true');
			} else
				DOM.setStyle('mceModalBlocker', 'z-index', t.zIndex - 1);

			if (tinymce.isIE6 || /Firefox\/2\./.test(navigator.userAgent) || (tinymce.isIE && !DOM.boxModel))
				DOM.setStyles('mceModalBlocker', {position : 'absolute', left : vp.x, top : vp.y, width : vp.w - 2, height : vp.h - 2});

			DOM.setAttrib(id, 'aria-hidden', 'false');
			t.focus(id);
			t._fixIELayout(id, 1);

			// Focus ok button
			if (DOM.get(id + '_ok'))
				DOM.get(id + '_ok').focus();
			t.count++;

			return w;
		},

		focus : function(id) {
			var t = this, w;

			if (w = t.windows[id]) {
				w.zIndex = this.zIndex++;
				w.element.setStyle('zIndex', w.zIndex);
				w.element.update();

				id = id + '_wrapper';
				DOM.removeClass(t.lastId, 'mceFocus');
				DOM.addClass(id, 'mceFocus');
				t.lastId = id;
				
				if (w.focussedElement) {
					w.focussedElement.focus();
				} else if (DOM.get(id + '_ok')) {
					DOM.get(w.id + '_ok').focus();
				} else if (DOM.get(w.id + '_ifr')) {
					DOM.get(w.id + '_ifr').focus();
				}
			}
		},

		_addAll : function(te, ne) {
			var i, n, t = this, dom = tinymce.DOM;

			if (is(ne, 'string'))
				te.appendChild(dom.doc.createTextNode(ne));
			else if (ne.length) {
				te = te.appendChild(dom.create(ne[0], ne[1]));

				for (i=2; i<ne.length; i++)
					t._addAll(te, ne[i]);
			}
		},

		_startDrag : function(id, se, ac) {
			var t = this, mu, mm, d = DOM.doc, eb, w = t.windows[id], we = w.element, sp = we.getXY(), p, sz, ph, cp, vp, sx, sy, sex, sey, dx, dy, dw, dh;

			// Get positons and sizes
//			cp = DOM.getPos(t.editor.getContainer());
			cp = {x : 0, y : 0};
			vp = DOM.getViewPort();

			// Reduce viewport size to avoid scrollbars while dragging
			vp.w -= 2;
			vp.h -= 2;

			sex = se.screenX;
			sey = se.screenY;
			dx = dy = dw = dh = 0;

			// Handle mouse up
			mu = Event.add(d, 'mouseup', function(e) {
				Event.remove(d, 'mouseup', mu);
				Event.remove(d, 'mousemove', mm);

				if (eb)
					eb.remove();

				we.moveBy(dx, dy);
				we.resizeBy(dw, dh);
				sz = we.getSize();
				DOM.setStyles(id + '_ifr', {width : sz.w - w.deltaWidth, height : sz.h - w.deltaHeight});
				t._fixIELayout(id, 1);

				return Event.cancel(e);
			});

			if (ac != 'Move')
				startMove();

			function startMove() {
				if (eb)
					return;

				t._fixIELayout(id, 0);

				// Setup event blocker
				DOM.add(d.body, 'div', {
					id : 'mceEventBlocker',
					'class' : 'mceEventBlocker ' + (t.editor.settings.inlinepopups_skin || 'clearlooks2'),
					style : {zIndex : t.zIndex + 1}
				});

				if (tinymce.isIE6 || (tinymce.isIE && !DOM.boxModel))
					DOM.setStyles('mceEventBlocker', {position : 'absolute', left : vp.x, top : vp.y, width : vp.w - 2, height : vp.h - 2});

				eb = new Element('mceEventBlocker');
				eb.update();

				// Setup placeholder
				p = we.getXY();
				sz = we.getSize();
				sx = cp.x + p.x - vp.x;
				sy = cp.y + p.y - vp.y;
				DOM.add(eb.get(), 'div', {id : 'mcePlaceHolder', 'class' : 'mcePlaceHolder', style : {left : sx, top : sy, width : sz.w, height : sz.h}});
				ph = new Element('mcePlaceHolder');
			};

			// Handle mouse move/drag
			mm = Event.add(d, 'mousemove', function(e) {
				var x, y, v;

				startMove();

				x = e.screenX - sex;
				y = e.screenY - sey;

				switch (ac) {
					case 'ResizeW':
						dx = x;
						dw = 0 - x;
						break;

					case 'ResizeE':
						dw = x;
						break;

					case 'ResizeN':
					case 'ResizeNW':
					case 'ResizeNE':
						if (ac == "ResizeNW") {
							dx = x;
							dw = 0 - x;
						} else if (ac == "ResizeNE")
							dw = x;

						dy = y;
						dh = 0 - y;
						break;

					case 'ResizeS':
					case 'ResizeSW':
					case 'ResizeSE':
						if (ac == "ResizeSW") {
							dx = x;
							dw = 0 - x;
						} else if (ac == "ResizeSE")
							dw = x;

						dh = y;
						break;

					case 'mceMove':
						dx = x;
						dy = y;
						break;
				}

				// Boundary check
				if (dw < (v = w.features.min_width - sz.w)) {
					if (dx !== 0)
						dx += dw - v;

					dw = v;
				}
	
				if (dh < (v = w.features.min_height - sz.h)) {
					if (dy !== 0)
						dy += dh - v;

					dh = v;
				}

				dw = Math.min(dw, w.features.max_width - sz.w);
				dh = Math.min(dh, w.features.max_height - sz.h);
				dx = Math.max(dx, vp.x - (sx + vp.x));
				dy = Math.max(dy, vp.y - (sy + vp.y));
				dx = Math.min(dx, (vp.w + vp.x) - (sx + sz.w + vp.x));
				dy = Math.min(dy, (vp.h + vp.y) - (sy + sz.h + vp.y));

				// Move if needed
				if (dx + dy !== 0) {
					if (sx + dx < 0)
						dx = 0;
	
					if (sy + dy < 0)
						dy = 0;

					ph.moveTo(sx + dx, sy + dy);
				}

				// Resize if needed
				if (dw + dh !== 0)
					ph.resizeTo(sz.w + dw, sz.h + dh);

				return Event.cancel(e);
			});

			return Event.cancel(se);
		},

		resizeBy : function(dw, dh, id) {
			var w = this.windows[id];

			if (w) {
				w.element.resizeBy(dw, dh);
				w.iframeElement.resizeBy(dw, dh);
			}
		},

		close : function(win, id) {
			var t = this, w, d = DOM.doc, fw, id;

			id = t._findId(id || win);

			// Probably not inline
			if (!t.windows[id]) {
				t.parent(win);
				return;
			}

			t.count--;

			if (t.count == 0) {
				DOM.remove('mceModalBlocker');
				DOM.setAttrib(DOM.doc.body, 'aria-hidden', 'false');
				t.editor.focus();
			}

			if (w = t.windows[id]) {
				t.onClose.dispatch(t);
				Event.remove(d, 'mousedown', w.mousedownFunc);
				Event.remove(d, 'click', w.clickFunc);
				Event.clear(id);
				Event.clear(id + '_ifr');

				DOM.setAttrib(id + '_ifr', 'src', 'javascript:""'); // Prevent leak
				w.element.remove();
				delete t.windows[id];

				fw = t._frontWindow();

				if (fw)
					t.focus(fw.id);
			}
		},
		
		// Find front most window
		_frontWindow : function() {
			var fw, ix = 0;
			// Find front most window and focus that
			each (this.windows, function(w) {
				if (w.zIndex > ix) {
					fw = w;
					ix = w.zIndex;
				}
			});
			return fw;
		},

		setTitle : function(w, ti) {
			var e;

			w = this._findId(w);

			if (e = DOM.get(w + '_title'))
				e.innerHTML = DOM.encode(ti);
		},

		alert : function(txt, cb, s) {
			var t = this, w;

			w = t.open({
				title : t,
				type : 'alert',
				button_func : function(s) {
					if (cb)
						cb.call(s || t, s);

					t.close(null, w.id);
				},
				content : DOM.encode(t.editor.getLang(txt, txt)),
				inline : 1,
				width : 400,
				height : 130
			});
		},

		confirm : function(txt, cb, s) {
			var t = this, w;

			w = t.open({
				title : t,
				type : 'confirm',
				button_func : function(s) {
					if (cb)
						cb.call(s || t, s);

					t.close(null, w.id);
				},
				content : DOM.encode(t.editor.getLang(txt, txt)),
				inline : 1,
				width : 400,
				height : 130
			});
		},

		// Internal functions

		_findId : function(w) {
			var t = this;

			if (typeof(w) == 'string')
				return w;

			each(t.windows, function(wo) {
				var ifr = DOM.get(wo.id + '_ifr');

				if (ifr && w == ifr.contentWindow) {
					w = wo.id;
					return false;
				}
			});

			return w;
		},

		_fixIELayout : function(id, s) {
			var w, img;

			if (!tinymce.isIE6)
				return;

			// Fixes the bug where hover flickers and does odd things in IE6
			each(['n','s','w','e','nw','ne','sw','se'], function(v) {
				var e = DOM.get(id + '_resize_' + v);

				DOM.setStyles(e, {
					width : s ? e.clientWidth : '',
					height : s ? e.clientHeight : '',
					cursor : DOM.getStyle(e, 'cursor', 1)
				});

				DOM.setStyle(id + "_bottom", 'bottom', '-1px');

				e = 0;
			});

			// Fixes graphics glitch
			if (w = this.windows[id]) {
				// Fixes rendering bug after resize
				w.element.hide();
				w.element.show();

				// Forced a repaint of the window
				//DOM.get(id).style.filter = '';

				// IE has a bug where images used in CSS won't get loaded
				// sometimes when the cache in the browser is disabled
				// This fix tries to solve it by loading the images using the image object
				each(DOM.select('div,a', id), function(e, i) {
					if (e.currentStyle.backgroundImage != 'none') {
						img = new Image();
						img.src = e.currentStyle.backgroundImage.replace(/url\(\"(.+)\"\)/, '$1');
					}
				});

				DOM.get(id).style.filter = '';
			}
		}
	});

	// Register plugin
	tinymce.PluginManager.add('inlinepopups', tinymce.plugins.InlinePopups);
})();

