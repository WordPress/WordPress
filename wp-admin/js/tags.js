/**
 * Contains logic for deleting and adding tags.
 *
 * For deleting tags it makes a request to the server to delete the tag.
 * For adding tags it makes a request to the server to add the tag.
 *
 * @output wp-admin/js/tags.js
 */

 /* global ajaxurl, wpAjax, showNotice, validateForm */

jQuery( function($) {

	var addingTerm = false;

	/**
	 * Adds an event handler to the delete term link on the term overview page.
	 *
	 * Cancels default event handling and event bubbling.
	 *
	 * @since 2.8.0
	 *
	 * @return {boolean} Always returns false to cancel the default event handling.
	 */
	$( '#the-list' ).on( 'click', '.delete-tag', function() {
		var t = $(this), tr = t.parents('tr'), r = true, data;

		if ( 'undefined' != showNotice )
			r = showNotice.warn();

		if ( r ) {
			data = t.attr('href').replace(/[^?]*\?/, '').replace(/action=delete/, 'action=delete-tag');

			/**
			 * Makes a request to the server to delete the term that corresponds to the
			 * delete term button.
			 *
			 * @param {string} r The response from the server.
			 *
			 * @return {void}
			 */
			$.post(ajaxurl, data, function(r){
				if ( '1' == r ) {
					$('#ajax-response').empty();
					tr.fadeOut('normal', function(){ tr.remove(); });

					/**
					 * Removes the term from the parent box and the tag cloud.
					 *
					 * `data.match(/tag_ID=(\d+)/)[1]` matches the term ID from the data variable.
					 * This term ID is then used to select the relevant HTML elements:
					 * The parent box and the tag cloud.
					 */
					$('select#parent option[value="' + data.match(/tag_ID=(\d+)/)[1] + '"]').remove();
					$('a.tag-link-' + data.match(/tag_ID=(\d+)/)[1]).remove();

				} else if ( '-1' == r ) {
					$('#ajax-response').empty().append('<div class="notice notice-error"><p>' + wp.i18n.__( 'Sorry, you are not allowed to do that.' ) + '</p></div>');
					tr.children().css('backgroundColor', '');

				} else {
					$('#ajax-response').empty().append('<div class="notice notice-error"><p>' + wp.i18n.__( 'An error occurred while processing your request. Please try again later.' ) + '</p></div>');
					tr.children().css('backgroundColor', '');
				}
			});

			tr.children().css('backgroundColor', '#f33');
		}

		return false;
	});

	/**
	 * Adds a deletion confirmation when removing a tag.
	 *
	 * @since 4.8.0
	 *
	 * @return {void}
	 */
	$( '#edittag' ).on( 'click', '.delete', function( e ) {
		if ( 'undefined' === typeof showNotice ) {
			return true;
		}

		// Confirms the deletion, a negative response means the deletion must not be executed.
		var response = showNotice.warn();
		if ( ! response ) {
			e.preventDefault();
		}
	});

	/**
	 * Adds an event handler to the form submit on the term overview page.
	 *
	 * Cancels default event handling and event bubbling.
	 *
	 * @since 2.8.0
	 *
	 * @return {boolean} Always returns false to cancel the default event handling.
	 */
	$('#submit').on( 'click', function(){
		var form = $(this).parents('form');

		if ( addingTerm ) {
			// If we're adding a term, noop the button to avoid duplicate requests.
			return false;
		}

		addingTerm = true;
		form.find( '.submit .spinner' ).addClass( 'is-active' );

		/**
		 * Does a request to the server to add a new term to the database
		 *
		 * @param {string} r The response from the server.
		 *
		 * @return {void}
		 */
		$.post(ajaxurl, $('#addtag').serialize(), function(r){
			var res, parent, term, indent, i;

			addingTerm = false;
			form.find( '.submit .spinner' ).removeClass( 'is-active' );

			$('#ajax-response').empty();
			res = wpAjax.parseAjaxResponse( r, 'ajax-response' );

			if ( res.errors && res.responses[0].errors[0].code === 'empty_term_name' ) {
				validateForm( form );
			}

			if ( ! res || res.errors ) {
				return;
			}

			parent = form.find( 'select#parent' ).val();

			// If the parent exists on this page, insert it below. Else insert it at the top of the list.
			if ( parent > 0 && $('#tag-' + parent ).length > 0 ) {
				// As the parent exists, insert the version with - - - prefixed.
				$( '.tags #tag-' + parent ).after( res.responses[0].supplemental.noparents );
			} else {
				// As the parent is not visible, insert the version with Parent - Child - ThisTerm.
				$( '.tags' ).prepend( res.responses[0].supplemental.parents );
			}

			$('.tags .no-items').remove();

			if ( form.find('select#parent') ) {
				// Parents field exists, Add new term to the list.
				term = res.responses[1].supplemental;

				// Create an indent for the Parent field.
				indent = '';
				for ( i = 0; i < res.responses[1].position; i++ )
					indent += '&nbsp;&nbsp;&nbsp;';

				form.find( 'select#parent option:selected' ).after( '<option value="' + term.term_id + '">' + indent + term.name + '</option>' );
			}

			$('input:not([type="checkbox"]):not([type="radio"]):not([type="button"]):not([type="submit"]):not([type="reset"]):visible, textarea:visible', form).val('');
		});

		return false;
	});

});
