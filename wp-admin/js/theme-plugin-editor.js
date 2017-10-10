/* eslint no-magic-numbers: ["error", { "ignore": [-1, 0, 1] }] */

if ( ! window.wp ) {
	window.wp = {};
}

wp.themePluginEditor = (function( $ ) {
	'use strict';

	var component = {
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
			component.warning.find( '.file-editor-warning-dismiss' ).focus();
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

		// hide modal
		component.warning.remove();
		$( 'body' ).removeClass( 'modal-open' );

		// return focus - is this a trap?
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

		request.done( function ( response ) {
			component.lastSaveNoticeCode = 'file_saved';
			component.addNotice({
				code: component.lastSaveNoticeCode,
				type: 'success',
				message: response.message,
				dismissible: true
			});
			component.dirty = false;
		} );

		request.fail( function ( response ) {
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

	return component;
})( jQuery );
