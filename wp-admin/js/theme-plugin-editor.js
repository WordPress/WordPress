/* eslint no-magic-numbers: ["error", { "ignore": [-1, 0, 1] }] */

if ( ! window.wp ) {
	window.wp = {};
}

wp.themePluginEditor = (function( $ ) {
	'use strict';
	var component, TreeLinks;

	component = {
		l10n: {
			lintError: {
				singular: '',
				plural: ''
			},
			saveAlert: '',
			saveError: ''
		},
		codeEditor: {},
		instance: null,
		noticeElements: {},
		dirty: false,
		lintErrors: []
	};

	/**
	 * Initialize component.
	 *
	 * @since 4.9.0
	 *
	 * @param {jQuery}         form - Form element.
	 * @param {object}         settings - Settings.
	 * @param {object|boolean} settings.codeEditor - Code editor settings (or `false` if syntax highlighting is disabled).
	 * @returns {void}
	 */
	component.init = function init( form, settings ) {

		component.form = form;
		if ( settings ) {
			$.extend( component, settings );
		}

		component.noticeTemplate = wp.template( 'wp-file-editor-notice' );
		component.noticesContainer = component.form.find( '.editor-notices' );
		component.submitButton = component.form.find( ':input[name=submit]' );
		component.spinner = component.form.find( '.submit .spinner' );
		component.form.on( 'submit', component.submit );
		component.textarea = component.form.find( '#newcontent' );
		component.textarea.on( 'change', component.onChange );
		component.warning = $( '.file-editor-warning' );

		if ( component.warning.length > 0 ) {
			$( 'body' ).addClass( 'modal-open' );
			component.warning.find( '.file-editor-warning-go-back' ).focus();
			component.warning.on( 'click', '.file-editor-warning-dismiss', component.dismissWarning );
		}

		if ( false !== component.codeEditor ) {
			/*
			 * Defer adding notices until after DOM ready as workaround for WP Admin injecting
			 * its own managed dismiss buttons and also to prevent the editor from showing a notice
			 * when the file had linting errors to begin with.
			 */
			_.defer( function() {
				component.initCodeEditor();
			} );
		}

		$( component.initFileBrowser );

		$( window ).on( 'beforeunload', function() {
			if ( component.dirty ) {
				return component.l10n.saveAlert;
			}
			return undefined;
		} );
	};

	/**
	 * Dismiss the warning modal.
	 *
	 * @since 4.9.0
	 * @returns {void}
	 */
	component.dismissWarning = function() {

		wp.ajax.post( 'dismiss-wp-pointer', {
			pointer: component.themeOrPlugin + '_editor_notice'
		});

		// Hide modal.
		component.warning.remove();
		$( 'body' ).removeClass( 'modal-open' );

		// Return focus - is this a trap?
		component.instance.codemirror.focus();
	};

	/**
	 * Callback for when a change happens.
	 *
	 * @since 4.9.0
	 * @returns {void}
	 */
	component.onChange = function() {
		component.dirty = true;
		component.removeNotice( 'file_saved' );
	};

	/**
	 * Submit file via Ajax.
	 *
	 * @since 4.9.0
	 * @param {jQuery.Event} event - Event.
	 * @returns {void}
	 */
	component.submit = function( event ) {
		var data = {}, request;
		event.preventDefault(); // Prevent form submission in favor of Ajax below.
		$.each( component.form.serializeArray(), function() {
			data[ this.name ] = this.value;
		} );

		// Use value from codemirror if present.
		if ( component.instance ) {
			data.newcontent = component.instance.codemirror.getValue();
		}

		if ( component.isSaving ) {
			return;
		}

		// Scroll ot the line that has the error.
		if ( component.lintErrors.length ) {
			component.instance.codemirror.setCursor( component.lintErrors[0].from.line );
			return;
		}

		component.isSaving = true;
		component.textarea.prop( 'readonly', true );
		if ( component.instance ) {
			component.instance.codemirror.setOption( 'readOnly', true );
		}

		component.spinner.addClass( 'is-active' );
		request = wp.ajax.post( 'edit-theme-plugin-file', data );

		// Remove previous save notice before saving.
		if ( component.lastSaveNoticeCode ) {
			component.removeNotice( component.lastSaveNoticeCode );
		}

		request.done( function( response ) {
			component.lastSaveNoticeCode = 'file_saved';
			component.addNotice({
				code: component.lastSaveNoticeCode,
				type: 'success',
				message: response.message,
				dismissible: true
			});
			component.dirty = false;
		} );

		request.fail( function( response ) {
			var notice = $.extend(
				{
					code: 'save_error',
					message: component.l10n.saveError
				},
				response,
				{
					type: 'error',
					dismissible: true
				}
			);
			component.lastSaveNoticeCode = notice.code;
			component.addNotice( notice );
		} );

		request.always( function() {
			component.spinner.removeClass( 'is-active' );
			component.isSaving = false;

			component.textarea.prop( 'readonly', false );
			if ( component.instance ) {
				component.instance.codemirror.setOption( 'readOnly', false );
			}
		} );
	};

	/**
	 * Add notice.
	 *
	 * @since 4.9.0
	 *
	 * @param {object}   notice - Notice.
	 * @param {string}   notice.code - Code.
	 * @param {string}   notice.type - Type.
	 * @param {string}   notice.message - Message.
	 * @param {boolean}  [notice.dismissible=false] - Dismissible.
	 * @param {Function} [notice.onDismiss] - Callback for when a user dismisses the notice.
	 * @returns {jQuery} Notice element.
	 */
	component.addNotice = function( notice ) {
		var noticeElement;

		if ( ! notice.code ) {
			throw new Error( 'Missing code.' );
		}

		// Only let one notice of a given type be displayed at a time.
		component.removeNotice( notice.code );

		noticeElement = $( component.noticeTemplate( notice ) );
		noticeElement.hide();

		noticeElement.find( '.notice-dismiss' ).on( 'click', function() {
			component.removeNotice( notice.code );
			if ( notice.onDismiss ) {
				notice.onDismiss( notice );
			}
		} );

		wp.a11y.speak( notice.message );

		component.noticesContainer.append( noticeElement );
		noticeElement.slideDown( 'fast' );
		component.noticeElements[ notice.code ] = noticeElement;
		return noticeElement;
	};

	/**
	 * Remove notice.
	 *
	 * @since 4.9.0
	 *
	 * @param {string} code - Notice code.
	 * @returns {boolean} Whether a notice was removed.
	 */
	component.removeNotice = function( code ) {
		if ( component.noticeElements[ code ] ) {
			component.noticeElements[ code ].slideUp( 'fast', function() {
				$( this ).remove();
			} );
			delete component.noticeElements[ code ];
			return true;
		}
		return false;
	};

	/**
	 * Initialize code editor.
	 *
	 * @since 4.9.0
	 * @returns {void}
	 */
	component.initCodeEditor = function initCodeEditor() {
		var codeEditorSettings, editor;

		codeEditorSettings = $.extend( {}, component.codeEditor );

		/**
		 * Handle tabbing to the field before the editor.
		 *
		 * @since 4.9.0
		 *
		 * @returns {void}
		 */
		codeEditorSettings.onTabPrevious = function() {
			$( '#templateside' ).find( ':tabbable' ).last().focus();
		};

		/**
		 * Handle tabbing to the field after the editor.
		 *
		 * @since 4.9.0
		 *
		 * @returns {void}
		 */
		codeEditorSettings.onTabNext = function() {
			$( '#template' ).find( ':tabbable:not(.CodeMirror-code)' ).first().focus();
		};

		/**
		 * Handle change to the linting errors.
		 *
		 * @since 4.9.0
		 *
		 * @param {Array} errors - List of linting errors.
		 * @returns {void}
		 */
		codeEditorSettings.onChangeLintingErrors = function( errors ) {
			component.lintErrors = errors;

			// Only disable the button in onUpdateErrorNotice when there are errors so users can still feel they can click the button.
			if ( 0 === errors.length ) {
				component.submitButton.toggleClass( 'disabled', false );
			}
		};

		/**
		 * Update error notice.
		 *
		 * @since 4.9.0
		 *
		 * @param {Array} errorAnnotations - Error annotations.
		 * @returns {void}
		 */
		codeEditorSettings.onUpdateErrorNotice = function onUpdateErrorNotice( errorAnnotations ) {
			var message, noticeElement;

			component.submitButton.toggleClass( 'disabled', errorAnnotations.length > 0 );

			if ( 0 !== errorAnnotations.length ) {
				if ( 1 === errorAnnotations.length ) {
					message = component.l10n.lintError.singular.replace( '%d', '1' );
				} else {
					message = component.l10n.lintError.plural.replace( '%d', String( errorAnnotations.length ) );
				}
				noticeElement = component.addNotice({
					code: 'lint_errors',
					type: 'error',
					message: message,
					dismissible: false
				});
				noticeElement.find( 'input[type=checkbox]' ).on( 'click', function() {
					codeEditorSettings.onChangeLintingErrors( [] );
					component.removeNotice( 'lint_errors' );
				} );
			} else {
				component.removeNotice( 'lint_errors' );
			}
		};

		editor = wp.codeEditor.initialize( $( '#newcontent' ), codeEditorSettings );
		editor.codemirror.on( 'change', component.onChange );

		// Improve the editor accessibility.
		$( editor.codemirror.display.lineDiv )
			.attr({
				role: 'textbox',
				'aria-multiline': 'true',
				'aria-labelledby': 'theme-plugin-editor-label',
				'aria-describedby': 'editor-keyboard-trap-help-1 editor-keyboard-trap-help-2 editor-keyboard-trap-help-3 editor-keyboard-trap-help-4'
			});

		// Focus the editor when clicking on its label.
		$( '#theme-plugin-editor-label' ).on( 'click', function() {
			editor.codemirror.focus();
		});

		component.instance = editor;
	};

	/**
	 * Initialization of the file browser's folder states.
	 *
	 * @since 4.9.0
	 * @returns {void}
	 */
	component.initFileBrowser = function initFileBrowser() {

		var $templateside = $( '#templateside' );

		// Collapse all folders.
		$templateside.find( '[role="group"]' ).parent().attr( 'aria-expanded', false );

		// Expand ancestors to the current file.
		$templateside.find( '.notice' ).parents( '[aria-expanded]' ).attr( 'aria-expanded', true );

		// Find Tree elements and enhance them.
		$templateside.find( '[role="tree"]' ).each( function() {
			var treeLinks = new TreeLinks( this );
			treeLinks.init();
		} );

		// Scroll the current file into view.
		$templateside.find( '.current-file' ).each( function() {
			this.scrollIntoView( false );
		} );
	};

	/* jshint ignore:start */
	/* jscs:disable */
	/* eslint-disable */

	/**
	 * Creates a new TreeitemLink.
	 *
	 * @since 4.9.0
	 * @class
	 * @private
	 * @see {@link https://www.w3.org/TR/wai-aria-practices-1.1/examples/treeview/treeview-2/treeview-2b.html|W3C Treeview Example}
	 * @license W3C-20150513
	 */
	var TreeitemLink = (function () {
		/**
		 *   This content is licensed according to the W3C Software License at
		 *   https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
		 *
		 *   File:   TreeitemLink.js
		 *
		 *   Desc:   Treeitem widget that implements ARIA Authoring Practices
		 *           for a tree being used as a file viewer
		 *
		 *   Author: Jon Gunderson, Ku Ja Eun and Nicholas Hoyt
		 */

		/**
		 *   @constructor
		 *
		 *   @desc
		 *       Treeitem object for representing the state and user interactions for a
		 *       treeItem widget
		 *
		 *   @param node
		 *       An element with the role=tree attribute
		 */

		var TreeitemLink = function (node, treeObj, group) {

			// Check whether node is a DOM element
			if (typeof node !== 'object') {
				return;
			}

			node.tabIndex = -1;
			this.tree = treeObj;
			this.groupTreeitem = group;
			this.domNode = node;
			this.label = node.textContent.trim();
			this.stopDefaultClick = false;

			if (node.getAttribute('aria-label')) {
				this.label = node.getAttribute('aria-label').trim();
			}

			this.isExpandable = false;
			this.isVisible = false;
			this.inGroup = false;

			if (group) {
				this.inGroup = true;
			}

			var elem = node.firstElementChild;

			while (elem) {

				if (elem.tagName.toLowerCase() == 'ul') {
					elem.setAttribute('role', 'group');
					this.isExpandable = true;
					break;
				}

				elem = elem.nextElementSibling;
			}

			this.keyCode = Object.freeze({
				RETURN: 13,
				SPACE: 32,
				PAGEUP: 33,
				PAGEDOWN: 34,
				END: 35,
				HOME: 36,
				LEFT: 37,
				UP: 38,
				RIGHT: 39,
				DOWN: 40
			});
		};

		TreeitemLink.prototype.init = function () {
			this.domNode.tabIndex = -1;

			if (!this.domNode.getAttribute('role')) {
				this.domNode.setAttribute('role', 'treeitem');
			}

			this.domNode.addEventListener('keydown', this.handleKeydown.bind(this));
			this.domNode.addEventListener('click', this.handleClick.bind(this));
			this.domNode.addEventListener('focus', this.handleFocus.bind(this));
			this.domNode.addEventListener('blur', this.handleBlur.bind(this));

			if (this.isExpandable) {
				this.domNode.firstElementChild.addEventListener('mouseover', this.handleMouseOver.bind(this));
				this.domNode.firstElementChild.addEventListener('mouseout', this.handleMouseOut.bind(this));
			}
			else {
				this.domNode.addEventListener('mouseover', this.handleMouseOver.bind(this));
				this.domNode.addEventListener('mouseout', this.handleMouseOut.bind(this));
			}
		};

		TreeitemLink.prototype.isExpanded = function () {

			if (this.isExpandable) {
				return this.domNode.getAttribute('aria-expanded') === 'true';
			}

			return false;

		};

		/* EVENT HANDLERS */

		TreeitemLink.prototype.handleKeydown = function (event) {
			var tgt = event.currentTarget,
				flag = false,
				_char = event.key,
				clickEvent;

			function isPrintableCharacter(str) {
				return str.length === 1 && str.match(/\S/);
			}

			function printableCharacter(item) {
				if (_char == '*') {
					item.tree.expandAllSiblingItems(item);
					flag = true;
				}
				else {
					if (isPrintableCharacter(_char)) {
						item.tree.setFocusByFirstCharacter(item, _char);
						flag = true;
					}
				}
			}

			this.stopDefaultClick = false;

			if (event.altKey || event.ctrlKey || event.metaKey) {
				return;
			}

			if (event.shift) {
				if (event.keyCode == this.keyCode.SPACE || event.keyCode == this.keyCode.RETURN) {
					event.stopPropagation();
					this.stopDefaultClick = true;
				}
				else {
					if (isPrintableCharacter(_char)) {
						printableCharacter(this);
					}
				}
			}
			else {
				switch (event.keyCode) {
					case this.keyCode.SPACE:
					case this.keyCode.RETURN:
						if (this.isExpandable) {
							if (this.isExpanded()) {
								this.tree.collapseTreeitem(this);
							}
							else {
								this.tree.expandTreeitem(this);
							}
							flag = true;
						}
						else {
							event.stopPropagation();
							this.stopDefaultClick = true;
						}
						break;

					case this.keyCode.UP:
						this.tree.setFocusToPreviousItem(this);
						flag = true;
						break;

					case this.keyCode.DOWN:
						this.tree.setFocusToNextItem(this);
						flag = true;
						break;

					case this.keyCode.RIGHT:
						if (this.isExpandable) {
							if (this.isExpanded()) {
								this.tree.setFocusToNextItem(this);
							}
							else {
								this.tree.expandTreeitem(this);
							}
						}
						flag = true;
						break;

					case this.keyCode.LEFT:
						if (this.isExpandable && this.isExpanded()) {
							this.tree.collapseTreeitem(this);
							flag = true;
						}
						else {
							if (this.inGroup) {
								this.tree.setFocusToParentItem(this);
								flag = true;
							}
						}
						break;

					case this.keyCode.HOME:
						this.tree.setFocusToFirstItem();
						flag = true;
						break;

					case this.keyCode.END:
						this.tree.setFocusToLastItem();
						flag = true;
						break;

					default:
						if (isPrintableCharacter(_char)) {
							printableCharacter(this);
						}
						break;
				}
			}

			if (flag) {
				event.stopPropagation();
				event.preventDefault();
			}
		};

		TreeitemLink.prototype.handleClick = function (event) {

			// only process click events that directly happened on this treeitem
			if (event.target !== this.domNode && event.target !== this.domNode.firstElementChild) {
				return;
			}

			if (this.isExpandable) {
				if (this.isExpanded()) {
					this.tree.collapseTreeitem(this);
				}
				else {
					this.tree.expandTreeitem(this);
				}
				event.stopPropagation();
			}
		};

		TreeitemLink.prototype.handleFocus = function (event) {
			var node = this.domNode;
			if (this.isExpandable) {
				node = node.firstElementChild;
			}
			node.classList.add('focus');
		};

		TreeitemLink.prototype.handleBlur = function (event) {
			var node = this.domNode;
			if (this.isExpandable) {
				node = node.firstElementChild;
			}
			node.classList.remove('focus');
		};

		TreeitemLink.prototype.handleMouseOver = function (event) {
			event.currentTarget.classList.add('hover');
		};

		TreeitemLink.prototype.handleMouseOut = function (event) {
			event.currentTarget.classList.remove('hover');
		};

		return TreeitemLink;
	})();

	/**
	 * Creates a new TreeLinks.
	 *
	 * @since 4.9.0
	 * @class
	 * @private
	 * @see {@link https://www.w3.org/TR/wai-aria-practices-1.1/examples/treeview/treeview-2/treeview-2b.html|W3C Treeview Example}
	 * @license W3C-20150513
	 */
	TreeLinks = (function () {
		/*
		 *   This content is licensed according to the W3C Software License at
		 *   https://www.w3.org/Consortium/Legal/2015/copyright-software-and-document
		 *
		 *   File:   TreeLinks.js
		 *
		 *   Desc:   Tree widget that implements ARIA Authoring Practices
		 *           for a tree being used as a file viewer
		 *
		 *   Author: Jon Gunderson, Ku Ja Eun and Nicholas Hoyt
		 */

		/*
		 *   @constructor
		 *
		 *   @desc
		 *       Tree item object for representing the state and user interactions for a
		 *       tree widget
		 *
		 *   @param node
		 *       An element with the role=tree attribute
		 */

		var TreeLinks = function (node) {
			// Check whether node is a DOM element
			if (typeof node !== 'object') {
				return;
			}

			this.domNode = node;

			this.treeitems = [];
			this.firstChars = [];

			this.firstTreeitem = null;
			this.lastTreeitem = null;

		};

		TreeLinks.prototype.init = function () {

			function findTreeitems(node, tree, group) {

				var elem = node.firstElementChild;
				var ti = group;

				while (elem) {

					if ((elem.tagName.toLowerCase() === 'li' && elem.firstElementChild.tagName.toLowerCase() === 'span') || elem.tagName.toLowerCase() === 'a') {
						ti = new TreeitemLink(elem, tree, group);
						ti.init();
						tree.treeitems.push(ti);
						tree.firstChars.push(ti.label.substring(0, 1).toLowerCase());
					}

					if (elem.firstElementChild) {
						findTreeitems(elem, tree, ti);
					}

					elem = elem.nextElementSibling;
				}
			}

			// initialize pop up menus
			if (!this.domNode.getAttribute('role')) {
				this.domNode.setAttribute('role', 'tree');
			}

			findTreeitems(this.domNode, this, false);

			this.updateVisibleTreeitems();

			this.firstTreeitem.domNode.tabIndex = 0;

		};

		TreeLinks.prototype.setFocusToItem = function (treeitem) {

			for (var i = 0; i < this.treeitems.length; i++) {
				var ti = this.treeitems[i];

				if (ti === treeitem) {
					ti.domNode.tabIndex = 0;
					ti.domNode.focus();
				}
				else {
					ti.domNode.tabIndex = -1;
				}
			}

		};

		TreeLinks.prototype.setFocusToNextItem = function (currentItem) {

			var nextItem = false;

			for (var i = (this.treeitems.length - 1); i >= 0; i--) {
				var ti = this.treeitems[i];
				if (ti === currentItem) {
					break;
				}
				if (ti.isVisible) {
					nextItem = ti;
				}
			}

			if (nextItem) {
				this.setFocusToItem(nextItem);
			}

		};

		TreeLinks.prototype.setFocusToPreviousItem = function (currentItem) {

			var prevItem = false;

			for (var i = 0; i < this.treeitems.length; i++) {
				var ti = this.treeitems[i];
				if (ti === currentItem) {
					break;
				}
				if (ti.isVisible) {
					prevItem = ti;
				}
			}

			if (prevItem) {
				this.setFocusToItem(prevItem);
			}
		};

		TreeLinks.prototype.setFocusToParentItem = function (currentItem) {

			if (currentItem.groupTreeitem) {
				this.setFocusToItem(currentItem.groupTreeitem);
			}
		};

		TreeLinks.prototype.setFocusToFirstItem = function () {
			this.setFocusToItem(this.firstTreeitem);
		};

		TreeLinks.prototype.setFocusToLastItem = function () {
			this.setFocusToItem(this.lastTreeitem);
		};

		TreeLinks.prototype.expandTreeitem = function (currentItem) {

			if (currentItem.isExpandable) {
				currentItem.domNode.setAttribute('aria-expanded', true);
				this.updateVisibleTreeitems();
			}

		};

		TreeLinks.prototype.expandAllSiblingItems = function (currentItem) {
			for (var i = 0; i < this.treeitems.length; i++) {
				var ti = this.treeitems[i];

				if ((ti.groupTreeitem === currentItem.groupTreeitem) && ti.isExpandable) {
					this.expandTreeitem(ti);
				}
			}

		};

		TreeLinks.prototype.collapseTreeitem = function (currentItem) {

			var groupTreeitem = false;

			if (currentItem.isExpanded()) {
				groupTreeitem = currentItem;
			}
			else {
				groupTreeitem = currentItem.groupTreeitem;
			}

			if (groupTreeitem) {
				groupTreeitem.domNode.setAttribute('aria-expanded', false);
				this.updateVisibleTreeitems();
				this.setFocusToItem(groupTreeitem);
			}

		};

		TreeLinks.prototype.updateVisibleTreeitems = function () {

			this.firstTreeitem = this.treeitems[0];

			for (var i = 0; i < this.treeitems.length; i++) {
				var ti = this.treeitems[i];

				var parent = ti.domNode.parentNode;

				ti.isVisible = true;

				while (parent && (parent !== this.domNode)) {

					if (parent.getAttribute('aria-expanded') == 'false') {
						ti.isVisible = false;
					}
					parent = parent.parentNode;
				}

				if (ti.isVisible) {
					this.lastTreeitem = ti;
				}
			}

		};

		TreeLinks.prototype.setFocusByFirstCharacter = function (currentItem, _char) {
			var start, index;
			_char = _char.toLowerCase();

			// Get start index for search based on position of currentItem
			start = this.treeitems.indexOf(currentItem) + 1;
			if (start === this.treeitems.length) {
				start = 0;
			}

			// Check remaining slots in the menu
			index = this.getIndexFirstChars(start, _char);

			// If not found in remaining slots, check from beginning
			if (index === -1) {
				index = this.getIndexFirstChars(0, _char);
			}

			// If match was found...
			if (index > -1) {
				this.setFocusToItem(this.treeitems[index]);
			}
		};

		TreeLinks.prototype.getIndexFirstChars = function (startIndex, _char) {
			for (var i = startIndex; i < this.firstChars.length; i++) {
				if (this.treeitems[i].isVisible) {
					if (_char === this.firstChars[i]) {
						return i;
					}
				}
			}
			return -1;
		};

		return TreeLinks;
	})();

	/* jshint ignore:end */
	/* jscs:enable */
	/* eslint-enable */

	return component;
})( jQuery );
