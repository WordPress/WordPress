/**
 * Contains global functions for the media upload within the post edit screen.
 *
 * Updates the ThickBox anchor href and the ThickBox's own properties in order
 * to set the size and position on every resize event. Also adds a function to
 * send HTML or text to the currently active editor.
 *
 * @file
 * @since 2.5.0
 * @output wp-admin/js/media-upload.js
 *
 * @requires jQuery
 */

/* global tinymce, QTags, wpActiveEditor, tb_position */

/**
 * Sends the HTML passed in the parameters to TinyMCE.
 *
 * @since 2.5.0
 *
 * @global
 *
 * @param {string} html The HTML to be sent to the editor.
 * @returns {void|boolean} Returns false when both TinyMCE and QTags instances
 *                         are unavailable. This means that the HTML was not
 *                         sent to the editor.
 */
window.send_to_editor = function( html ) {
	var editor,
		hasTinymce = typeof tinymce !== 'undefined',
		hasQuicktags = typeof QTags !== 'undefined';

	// If no active editor is set, try to set it.
	if ( ! wpActiveEditor ) {
		if ( hasTinymce && tinymce.activeEditor ) {
			editor = tinymce.activeEditor;
			window.wpActiveEditor = editor.id;
		} else if ( ! hasQuicktags ) {
			return false;
		}
	} else if ( hasTinymce ) {
		editor = tinymce.get( wpActiveEditor );
	}

	// If the editor is set and not hidden, insert the HTML into the content of the
	// editor.
	if ( editor && ! editor.isHidden() ) {
		editor.execCommand( 'mceInsertContent', false, html );
	} else if ( hasQuicktags ) {
		// If quick tags are available, insert the HTML into its content.
		QTags.insertContent( html );
	} else {
		// If neither the TinyMCE editor and the quick tags are available, add the HTML
		// to the current active editor.
		document.getElementById( wpActiveEditor ).value += html;
	}

	// If the old thickbox remove function exists, call it.
	if ( window.tb_remove ) {
		try { window.tb_remove(); } catch( e ) {}
	}
};

(function($) {
	/**
	 * Recalculates and applies the new ThickBox position based on the current
	 * window size.
	 *
	 * @since 2.6.0
	 *
	 * @global
	 *
	 * @returns {Object[]} Array containing jQuery objects for all the found
	 *                     ThickBox anchors.
	 */
	window.tb_position = function() {
		var tbWindow = $('#TB_window'),
			width = $(window).width(),
			H = $(window).height(),
			W = ( 833 < width ) ? 833 : width,
			adminbar_height = 0;

		if ( $('#wpadminbar').length ) {
			adminbar_height = parseInt( $('#wpadminbar').css('height'), 10 );
		}

		if ( tbWindow.length ) {
			tbWindow.width( W - 50 ).height( H - 45 - adminbar_height );
			$('#TB_iframeContent').width( W - 50 ).height( H - 75 - adminbar_height );
			tbWindow.css({'margin-left': '-' + parseInt( ( ( W - 50 ) / 2 ), 10 ) + 'px'});
			if ( typeof document.body.style.maxWidth !== 'undefined' )
				tbWindow.css({'top': 20 + adminbar_height + 'px', 'margin-top': '0'});
		}

		/**
		 * Recalculates the new height and width for all links with a ThickBox class.
		 *
		 * @since 2.6.0
		 */
		return $('a.thickbox').each( function() {
			var href = $(this).attr('href');
			if ( ! href ) return;
			href = href.replace(/&width=[0-9]+/g, '');
			href = href.replace(/&height=[0-9]+/g, '');
			$(this).attr( 'href', href + '&width=' + ( W - 80 ) + '&height=' + ( H - 85 - adminbar_height ) );
		});
	};

	// Add handler to recalculates the ThickBox position when the window is resized.
	$(window).resize(function(){ tb_position(); });

})(jQuery);
