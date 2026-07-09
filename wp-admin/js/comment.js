/**
 * @output wp-admin/js/comment.js
 */

/* global postboxes */

/**
 * Binds to the document ready event.
 *
 * @since 2.5.0
 *
 * @param {jQuery} $ The jQuery object.
 */
jQuery( function($) {

	postboxes.add_postbox_toggles('comment');

	var $timestampdiv = $('#timestampdiv'),
		$timestamp = $( '#timestamp' ),
		stamp = $timestamp.html(),
		$timestampwrap = $timestampdiv.find( '.timestamp-wrap' ),
		$edittimestamp = $timestampdiv.siblings( 'a.edit-timestamp' );

	/**
	 * Adds event that opens the time stamp form if the form is hidden.
	 *
	 * @listens $edittimestamp:click
	 *
	 * @param {Event} event The event object.
	 * @return {void}
	 */
	$edittimestamp.on( 'click', function( event ) {
		if ( $timestampdiv.is( ':hidden' ) ) {
			// Slide down the form and set focus on the first field.
			$timestampdiv.slideDown( 'fast', function() {
				$( 'input, select', $timestampwrap ).first().trigger( 'focus' );
			} );
			$(this).hide();
		}
		event.preventDefault();
	});

	/**
	 * Resets the time stamp values when the cancel button is clicked.
	 *
	 * @listens .cancel-timestamp:click
	 *
	 * @param {Event} event The event object.
	 * @return {void}
	 */

	$timestampdiv.find('.cancel-timestamp').on( 'click', function( event ) {
		// Move focus back to the Edit link.
		$edittimestamp.show().trigger( 'focus' );
		$timestampdiv.slideUp( 'fast' );
		$('#mm').val($('#hidden_mm').val());
		$('#jj').val($('#hidden_jj').val());
		$('#aa').val($('#hidden_aa').val());
		$('#hh').val($('#hidden_hh').val());
		$('#mn').val($('#hidden_mn').val());
		$timestamp.html( stamp );
		event.preventDefault();
	});

	/**
	 * Sets the time stamp values when the ok button is clicked.
	 *
	 * @listens .save-timestamp:click
	 *
	 * @param {Event} event The event object.
	 * @return {void}
	 */
	$timestampdiv.find('.save-timestamp').on( 'click', function( event ) { // Crazyhorse branch - multiple OK cancels.
		var aa = $('#aa').val(), mm = $('#mm').val(), jj = $('#jj').val(), hh = $('#hh').val(), mn = $('#mn').val(),
			newD = new Date( aa, mm - 1, jj, hh, mn );

		event.preventDefault();

		if ( newD.getFullYear() != aa || (1 + newD.getMonth()) != mm || newD.getDate() != jj || newD.getMinutes() != mn ) {
			$timestampwrap.addClass( 'form-invalid' );
			return;
		} else {
			$timestampwrap.removeClass( 'form-invalid' );
		}

		$timestamp.html(
			wp.i18n.__( 'Submitted on:' ) + ' <b>' +
			/* translators: 1: Month, 2: Day, 3: Year, 4: Hour, 5: Minute. */
			wp.i18n.__( '%1$s %2$s, %3$s at %4$s:%5$s' )
				.replace( '%1$s', $( 'option[value="' + mm + '"]', '#mm' ).attr( 'data-text' ) )
				.replace( '%2$s', parseInt( jj, 10 ) )
				.replace( '%3$s', aa )
				.replace( '%4$s', ( '00' + hh ).slice( -2 ) )
				.replace( '%5$s', ( '00' + mn ).slice( -2 ) ) +
				'</b> '
		);

		// Move focus back to the Edit link.
		$edittimestamp.show().trigger( 'focus' );
		$timestampdiv.slideUp( 'fast' );
	});

	var $commentparentdiv = $( '#comment-parent-div' ),
		$commentparentdisplay = $( '#comment-parent-display' ),
		originalcommentparentdisplay = $commentparentdisplay.html(),
		$editcommentparent = $commentparentdiv.siblings( 'a.edit-comment-parent' );

	/**
	 * Adds event that opens the parent comment form if the form is hidden.
	 *
	 * @since 7.1.0
	 *
	 * @listens $editcommentparent:click
	 *
	 * @param {Event} event The event object.
	 * @return {void}
	 */
	$editcommentparent.on( 'click', function( event ) {
		if ( $commentparentdiv.is( ':hidden' ) ) {
			// Slide down the form and set focus on the parent field.
			$commentparentdiv.slideDown( 'fast', function() {
				$( '#comment_parent' ).trigger( 'focus' );
			} );
			$(this).hide();
		}
		event.preventDefault();
	});

	/**
	 * Resets the parent comment value when the cancel button is clicked.
	 *
	 * @since 7.1.0
	 *
	 * @listens .cancel-comment-parent:click
	 *
	 * @param {Event} event The event object.
	 * @return {void}
	 */
	$commentparentdiv.find( '.cancel-comment-parent' ).on( 'click', function( event ) {
		// Move focus back to the Edit link.
		$editcommentparent.show().trigger( 'focus' );
		$commentparentdiv.slideUp( 'fast' );
		$( '#comment_parent' ).val( $( '#hidden_comment_parent' ).val() );
		$commentparentdisplay.html( originalcommentparentdisplay );
		event.preventDefault();
	});

	/**
	 * Updates the parent comment display when the ok button is clicked.
	 *
	 * @since 7.1.0
	 *
	 * @listens .save-comment-parent:click
	 *
	 * @param {Event} event The event object.
	 * @return {void}
	 */
	$commentparentdiv.find( '.save-comment-parent' ).on( 'click', function( event ) {
		var $selected = $( '#comment_parent option:selected' ),
			parentLabel = '0' === $selected.val() ?
				/* translators: Displayed when a comment has no parent. */
				wp.i18n.__( 'None' ) :
				$selected.data( 'author' ) || $selected.text();

		event.preventDefault();

		$commentparentdisplay.html(
			wp.i18n.sprintf(
				/* translators: %s: Parent comment link, or 'None'. */
				wp.i18n.__( 'In reply to: %s' ),
				$( '<b />' ).text( parentLabel ).prop( 'outerHTML' )
			)
		);

		// Move focus back to the Edit link.
		$editcommentparent.show().trigger( 'focus' );
		$commentparentdiv.slideUp( 'fast' );
	});
});
