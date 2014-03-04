/**
 * theme.js
 *
 * Copyright, Moxiecode Systems AB
 * Released under LGPL License.
 *
 * License: http://www.tinymce.com/license
 * Contributing: http://www.tinymce.com/contributing
 */

/*global tinymce:true */

tinymce.ThemeManager.add('modern', function(editor) {
	var self = this, settings = editor.settings, Factory = tinymce.ui.Factory, each = tinymce.each, DOM = tinymce.DOM;

	// Default menus
	var defaultMenus = {
		file: {title: 'File', items: 'newdocument'},
		edit: {title: 'Edit', items: 'undo redo | cut copy paste pastetext | selectall'},
		insert: {title: 'Insert', items: '|'},
		view: {title: 'View', items: 'visualaid |'},
		format: {title: 'Format', items: 'bold italic underline strikethrough superscript subscript | formats | removeformat'},
		table: {title: 'Table'},
		tools: {title: 'Tools'}
	};

	var defaultToolbar = "undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | " +
		"bullist numlist outdent indent | link image";

	/**
	 * Creates the toolbars from config and returns a toolbar array.
	 *
	 * @return {Array} Array with toolbars.
	 */
	function createToolbars() {
		var toolbars = [];

		function addToolbar(items) {
			var toolbarItems = [], buttonGroup;

			if (!items) {
				return;
			}

			each(items.split(/[ ,]/), function(item) {
				var itemName;

				function bindSelectorChanged() {
					var selection = editor.selection;

					if (itemName == "bullist") {
						selection.selectorChanged('ul > li', function(state, args) {
							var nodeName, i = args.parents.length;

							while (i--) {
								nodeName = args.parents[i].nodeName;
								if (nodeName == "OL" || nodeName == "UL") {
									break;
								}
							}

							item.active(state && nodeName == "UL");
						});
					}

					if (itemName == "numlist") {
						selection.selectorChanged('ol > li', function(state, args) {
							var nodeName, i = args.parents.length;

							while (i--) {
								nodeName = args.parents[i].nodeName;
								if (nodeName == "OL" || nodeName == "UL") {
									break;
								}
							}

							item.active(state && nodeName == "OL");
						});
					}

					if (item.settings.stateSelector) {
						selection.selectorChanged(item.settings.stateSelector, function(state) {
							item.active(state);
						}, true);
					}

					if (item.settings.disabledStateSelector) {
						selection.selectorChanged(item.settings.disabledStateSelector, function(state) {
							item.disabled(state);
						});
					}
				}

				if (item == "|") {
					buttonGroup = null;
				} else {
					if (Factory.has(item)) {
						item = {type: item};

						if (settings.toolbar_items_size) {
							item.size = settings.toolbar_items_size;
						}

						toolbarItems.push(item);
						buttonGroup = null;
					} else {
						if (!buttonGroup) {
							buttonGroup = {type: 'buttongroup', items: []};
							toolbarItems.push(buttonGroup);
						}

						if (editor.buttons[item]) {
							// TODO: Move control creation to some UI class
							itemName = item;
							item = editor.buttons[itemName];

							if (typeof(item) == "function") {
								item = item();
							}

							item.type = item.type || 'button';

							if (settings.toolbar_items_size) {
								item.size = settings.toolbar_items_size;
							}

							item = Factory.create(item);
							buttonGroup.items.push(item);

							if (editor.initialized) {
								bindSelectorChanged();
							} else {
								editor.on('init', bindSelectorChanged);
							}
						}
					}
				}
			});

			toolbars.push({type: 'toolbar', layout: 'flow', items: toolbarItems});

			return true;
		}

		// Generate toolbar<n>
		for (var i = 1; i < 10; i++) {
			if (!addToolbar(settings["toolbar" + i])) {
				break;
			}
		}

		// Generate toolbar or default toolbar
		if (!toolbars.length) {
			addToolbar(settings.toolbar || defaultToolbar);
		}

		return toolbars;
	}

	/**
	 * Creates the menu buttons based on config.
	 *
	 * @return {Array} Menu buttons array.
	 */
	function createMenuButtons() {
		var name, menuButtons = [];

		function createMenuItem(name) {
			var menuItem;

			if (name == '|') {
				return {text: '|'};
			}

			menuItem = editor.menuItems[name];

			return menuItem;
		}

		function createMenu(context) {
			var menuButton, menu, menuItems, isUserDefined, removedMenuItems;

			removedMenuItems = tinymce.makeMap((settings.removed_menuitems || '').split(/[ ,]/));

			// User defined menu
			if (settings.menu) {
				menu = settings.menu[context];
				isUserDefined = true;
			} else {
				menu = defaultMenus[context];
			}

			if (menu) {
				menuButton = {text: menu.title};
				menuItems = [];

				// Default/user defined items
				each((menu.items || '').split(/[ ,]/), function(item) {
					var menuItem = createMenuItem(item);

					if (menuItem && !removedMenuItems[item]) {
						menuItems.push(createMenuItem(item));
					}
				});

				// Added though context
				if (!isUserDefined) {
					each(editor.menuItems, function(menuItem) {
						if (menuItem.context == context) {
							if (menuItem.separator == 'before') {
								menuItems.push({text: '|'});
							}

							if (menuItem.prependToContext) {
								menuItems.unshift(menuItem);
							} else {
								menuItems.push(menuItem);
							}

							if (menuItem.separator == 'after') {
								menuItems.push({text: '|'});
							}
						}
					});
				}

				for (var i = 0; i < menuItems.length; i++) {
					if (menuItems[i].text == '|') {
						if (i === 0 || i == menuItems.length - 1) {
							menuItems.splice(i, 1);
						}
					}
				}

				menuButton.menu = menuItems;

				if (!menuButton.menu.length) {
					return null;
				}
			}

			return menuButton;
		}

		var defaultMenuBar = [];
		if (settings.menu) {
			for (name in settings.menu) {
				defaultMenuBar.push(name);
			}
		} else {
			for (name in defaultMenus) {
				defaultMenuBar.push(name);
			}
		}

		var enabledMenuNames = typeof(settings.menubar) == "string" ? settings.menubar.split(/[ ,]/) : defaultMenuBar;
		for (var i = 0; i < enabledMenuNames.length; i++) {
			var menu = enabledMenuNames[i];
			menu = createMenu(menu);

			if (menu) {
				menuButtons.push(menu);
			}
		}

		return menuButtons;
	}

	/**
	 * Adds accessibility shortcut keys to panel.
	 *
	 * @param {tinymce.ui.Panel} panel Panel to add focus to.
	 */
	function addAccessibilityKeys(panel) {
		function focus(type) {
			var item = panel.find(type)[0];

			if (item) {
				item.focus(true);
			}
		}

		editor.shortcuts.add('Alt+F9', '', function() {
			focus('menubar');
		});

		editor.shortcuts.add('Alt+F10', '', function() {
			focus('toolbar');
		});

		editor.shortcuts.add('Alt+F11', '', function() {
			focus('elementpath');
		});

		panel.on('cancel', function() {
			editor.focus();
		});
	}

	/**
	 * Resizes the editor to the specified width, height.
	 */
	function resizeTo(width, height) {
		var containerElm, iframeElm, containerSize, iframeSize;

		function getSize(elm) {
			return {
				width: elm.clientWidth,
				height: elm.clientHeight
			};
		}

		containerElm = editor.getContainer();
		iframeElm = editor.getContentAreaContainer().firstChild;
		containerSize = getSize(containerElm);
		iframeSize = getSize(iframeElm);

		if (width !== null) {
			width = Math.max(settings.min_width || 100, width);
			width = Math.min(settings.max_width || 0xFFFF, width);

			DOM.css(containerElm, 'width', width + (containerSize.width - iframeSize.width));
			DOM.css(iframeElm, 'width', width);
		}

		height = Math.max(settings.min_height || 100, height);
		height = Math.min(settings.max_height || 0xFFFF, height);
		DOM.css(iframeElm, 'height', height);

		editor.fire('ResizeEditor');
	}

	function resizeBy(dw, dh) {
		var elm = editor.getContentAreaContainer();
		self.resizeTo(elm.clientWidth + dw, elm.clientHeight + dh);
	}

	/**
	 * Renders the inline editor UI.
	 *
	 * @return {Object} Name/value object with theme data.
	 */
	function renderInlineUI(args) {
		var panel, inlineToolbarContainer;

		if (settings.fixed_toolbar_container) {
			inlineToolbarContainer = DOM.select(settings.fixed_toolbar_container)[0];
		}

		function reposition() {
			if (panel && panel.moveRel && panel.visible() && !panel._fixed) {
				// TODO: This is kind of ugly and doesn't handle multiple scrollable elements
				var scrollContainer = editor.selection.getScrollContainer(), body = editor.getBody();
				var deltaX = 0, deltaY = 0;

				if (scrollContainer) {
					var bodyPos = DOM.getPos(body), scrollContainerPos = DOM.getPos(scrollContainer);

					deltaX = Math.max(0, scrollContainerPos.x - bodyPos.x);
					deltaY = Math.max(0, scrollContainerPos.y - bodyPos.y);
				}

				panel.fixed(false).moveRel(body, editor.rtl ? ['tr-br', 'br-tr'] : ['tl-bl', 'bl-tl']).moveBy(deltaX, deltaY);
			}
		}

		function show() {
			if (panel) {
				panel.show();
				reposition();
				DOM.addClass(editor.getBody(), 'mce-edit-focus');
			}
		}

		function hide() {
			if (panel) {
				panel.hide();
				DOM.removeClass(editor.getBody(), 'mce-edit-focus');
			}
		}

		function render() {
			if (panel) {
				if (!panel.visible()) {
					show();
				}

				return;
			}

			// Render a plain panel inside the inlineToolbarContainer if it's defined
			panel = self.panel = Factory.create({
				type: inlineToolbarContainer ? 'panel' : 'floatpanel',
				role: 'application',
				classes: 'tinymce tinymce-inline',
				layout: 'flex',
				direction: 'column',
				align: 'stretch',
				autohide: false,
				autofix: true,
				fixed: !!inlineToolbarContainer,
				border: 1,
				items: [
					settings.menubar === false ? null : {type: 'menubar', border: '0 0 1 0', items: createMenuButtons()},
					settings.toolbar === false ? null : {
						type: 'panel', layout: 'stack', classes: "toolbar-grp", ariaRoot: true, ariaRemember: true, items: createToolbars()
					}
				]
			});

			// Add statusbar
			/*if (settings.statusbar !== false) {
				panel.add({type: 'panel', classes: 'statusbar', layout: 'flow', border: '1 0 0 0', items: [
					{type: 'elementpath'}
				]});
			}*/

			editor.fire('BeforeRenderUI');
			panel.renderTo(inlineToolbarContainer || document.body).reflow();

			addAccessibilityKeys(panel);
			show();

			editor.on('nodeChange', reposition);
			editor.on('activate', show);
			editor.on('deactivate', hide);

			editor.nodeChanged();
		}

		settings.content_editable = true;

		editor.on('focus', function() {
			// Render only when the CSS file has been loaded
			if (args.skinUiCss) {
				tinymce.DOM.styleSheetLoader.load(args.skinUiCss, render, render);
			} else {
				render();
			}
		});

		editor.on('blur', hide);

		// Remove the panel when the editor is removed
		editor.on('remove', function() {
			if (panel) {
				panel.remove();
				panel = null;
			}
		});

		// Preload skin css
		if (args.skinUiCss) {
			tinymce.DOM.styleSheetLoader.load(args.skinUiCss);
		}

		return {};
	}

	/**
	 * Renders the iframe editor UI.
	 *
	 * @param {Object} args Details about target element etc.
	 * @return {Object} Name/value object with theme data.
	 */
	function renderIframeUI(args) {
		var panel, resizeHandleCtrl, startSize;

		if (args.skinUiCss) {
			tinymce.DOM.loadCSS(args.skinUiCss);
		}

		// Basic UI layout
		panel = self.panel = Factory.create({
			type: 'panel',
			role: 'application',
			classes: 'tinymce',
			style: 'visibility: hidden',
			layout: 'stack',
			border: 1,
			items: [
				settings.menubar === false ? null : {type: 'menubar', border: '0 0 1 0', items: createMenuButtons()},
				settings.toolbar === false ? null : {
					type: 'panel', layout: 'stack', classes: "toolbar-grp", ariaRoot: true, ariaRemember: true, items: createToolbars()
				},
				{type: 'panel', name: 'iframe', layout: 'stack', classes: 'edit-area', html: '', border: '1 0 0 0'}
			]
		});

		if (settings.resize !== false) {
			resizeHandleCtrl = {
				type: 'resizehandle',
				direction: settings.resize,

				onResizeStart: function() {
					var elm = editor.getContentAreaContainer().firstChild;

					startSize = {
						width: elm.clientWidth,
						height: elm.clientHeight
					};
				},

				onResize: function(e) {
					if (settings.resize == 'both') {
						resizeTo(startSize.width + e.deltaX, startSize.height + e.deltaY);
					} else {
						resizeTo(null, startSize.height + e.deltaY);
					}
				}
			};
		}

		// Add statusbar if needed
		if (settings.statusbar !== false) {
			panel.add({type: 'panel', name: 'statusbar', classes: 'statusbar', layout: 'flow', border: '1 0 0 0', ariaRoot: true, items: [
				{type: 'elementpath'},
				resizeHandleCtrl
			]});
		}

		if (settings.readonly) {
			panel.find('*').disabled(true);
		}

		editor.fire('BeforeRenderUI');
		panel.renderBefore(args.targetNode).reflow();

		if (settings.width) {
			tinymce.DOM.setStyle(panel.getEl(), 'width', settings.width);
		}

		// Remove the panel when the editor is removed
		editor.on('remove', function() {
			panel.remove();
			panel = null;
		});

		// Add accesibility shortkuts
		addAccessibilityKeys(panel);

		return {
			iframeContainer: panel.find('#iframe')[0].getEl(),
			editorContainer: panel.getEl()
		};
	}

	/**
	 * Renders the UI for the theme. This gets called by the editor.
	 *
	 * @param {Object} args Details about target element etc.
	 * @return {Object} Theme UI data items.
	 */
	self.renderUI = function(args) {
		var skin = settings.skin !== false ? settings.skin || 'lightgray' : false;

		if (skin) {
			var skinUrl = settings.skin_url;

			if (skinUrl) {
				skinUrl = editor.documentBaseURI.toAbsolute(skinUrl);
			} else {
				skinUrl = tinymce.baseURL + '/skins/' + skin;
			}

			// Load special skin for IE7
			// TODO: Remove this when we drop IE7 support
			if (tinymce.Env.documentMode <= 7) {
				args.skinUiCss = skinUrl + '/skin.ie7.min.css';
			} else {
				args.skinUiCss = skinUrl + '/skin.min.css';
			}

			// Load content.min.css or content.inline.min.css
			editor.contentCSS.push(skinUrl + '/content' + (editor.inline ? '.inline' : '') + '.min.css');
		}

		// Handle editor setProgressState change
		editor.on('ProgressState', function(e) {
			self.throbber = self.throbber || new tinymce.ui.Throbber(self.panel.getEl('body'));

			if (e.state) {
				self.throbber.show(e.time);
			} else {
				self.throbber.hide();
			}
		});

		if (settings.inline) {
			return renderInlineUI(args);
		}

		return renderIframeUI(args);
	};

	self.resizeTo = resizeTo;
	self.resizeBy = resizeBy;
});
