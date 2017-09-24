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
			}
		},
		instance: null
	};

	/**
	 * Initialize component.
	 *
	 * @param {object} settings Settings.
	 * @returns {void}
	 */
	component.init = function( settings ) {
		var codeEditorSettings, noticeContainer, errorNotice = [], editor;

		codeEditorSettings = $.extend( {}, settings );

		/**
		 * Handle tabbing to the field before the editor.
		 *
		 * @returns {void}
		 */
		codeEditorSettings.onTabPrevious = function() {
			$( '#templateside' ).find( ':tabbable' ).last().focus();
		};

		/**
		 * Handle tabbing to the field after the editor.
		 *
		 * @returns {void}
		 */
		codeEditorSettings.onTabNext = function() {
			$( '#template' ).find( ':tabbable:not(.CodeMirror-code)' ).first().focus();
		};

		// Create the error notice container.
		noticeContainer = $( '<div id="file-editor-linting-error"></div>' );
		errorNotice = $( '<div class="inline notice notice-error"></div>' );
		noticeContainer.append( errorNotice );
		noticeContainer.hide();
		$( 'p.submit' ).before( noticeContainer );

		/**
		 * Update error notice.
		 *
		 * @param {Array} errorAnnotations - Error annotations.
		 * @returns {void}
		 */
		codeEditorSettings.onUpdateErrorNotice = function onUpdateErrorNotice( errorAnnotations ) {
			var message;

			$( '#submit' ).prop( 'disabled', 0 !== errorAnnotations.length );

			if ( 0 !== errorAnnotations.length ) {
				errorNotice.empty();
				if ( 1 === errorAnnotations.length ) {
					message = component.l10n.singular.replace( '%d', '1' );
				} else {
					message = component.l10n.plural.replace( '%d', String( errorAnnotations.length ) );
				}
				errorNotice.append( $( '<p></p>', {
					text: message
				} ) );
				noticeContainer.slideDown( 'fast' );
				wp.a11y.speak( message );
			} else {
				noticeContainer.slideUp( 'fast' );
			}
		};

		editor = wp.codeEditor.initialize( $( '#newcontent' ), codeEditorSettings );

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
