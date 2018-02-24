/**
 * JavaScript code for the CodeMirror handling on the "Options" screen
 *
 * @package TablePress
 * @subpackage Views JavaScript
 * @author Tobias Bäthge
 * @since 1.9.0
 */

/* global wp, tablepress_codemirror_settings */

jQuery( document ).ready( function( $ ) {

	'use strict';

	/**
	 * Invoke CodeMirror on the "Custom CSS" textarea.
	 *
	 * @since 1.9.0
	 */
	var CM_custom_css = wp.codeEditor.initialize( document.getElementById( 'option-custom-css' ), tablepress_codemirror_settings );

	/**
	 * Make the CodeMirror textarea vertically resizable.
	 *
	 * @since 1.7.0
	 */
	$( CM_custom_css.codemirror.getWrapperElement() ).resizable( {
		handles: 's',
		resize: function() {
			var $this = $(this);
			CM_custom_css.codemirror.setSize( $this.width(), $this.height() );
		}
	} );

	/**
	 * Let CodeMirror textarea grow on first focus, if it is not disabled.
	 *
	 * @since 1.0.0
	 */
	$( '#tablepress-page' ).find( '.CodeMirror' ).on( 'mousedown.codemirror', function() {
		var $this = $(this);
		if ( ! $this.hasClass( 'disabled' ) ) {
			$this.addClass( 'large' );
			CM_custom_css.codemirror.refresh();
			$this.off( 'mousedown.codemirror' );
		}
	} );

	/**
	 * Enable/disable CodeMirror according to state of "Load Custom CSS" checkbox.
	 *
	 * @since 1.0.0
	 */
	$( '#option-use-custom-css' ).on( 'change', function() {
		var use_custom_css = $(this).prop( 'checked' );
		CM_custom_css.codemirror.setOption( 'readOnly', ! use_custom_css );
		$( '#tablepress-page' ).find( '.CodeMirror' ).toggleClass( 'disabled', ! use_custom_css );
	} ).change();

} );
