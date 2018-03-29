/* global ajaxurl, wpAjax, tagsl10n, showNotice, validateForm */
/**
 * Contains logic for both adding and deleting tags. For deleting tags it makes a request
 * to the server to delete the tag. For adding tags it makes a request to the server to
 * add the tag.
 *
 * @summary Contains logic for deleting and adding tags
 */

jQuery(document).ready(function($) {

	/**
	 * @summary Adds an event handler to the delete term link on the term overview page.
	 *
	 * Adds an event handler to the delete term link on the term overview page.
	 * Cancels default event handling and event bubbling.
	 *
	 * @since 2.8.0
	 *
	 * @returns boolean Always returns false to cancel the default event handling.
	 */
	$( '#the-list' ).on( 'click', '.delete-tag', function() {
		var t = $(this), tr = t.parents('tr'), r = true, data;

		if ( 'undefined' != showNotice )
			r = showNotice.warn();

		if ( r ) {
			data = t.attr('href').replace(/[^?]*\?/, '').replace(/action=delete/, 'action=delete-tag');

			/**
			 * @summary Makes a request to the server to delete the term that
			 * corresponds to the delete term button.
			 *
			 * @param {string} r The response from the server.
			 *
			 * @returns {void}
			 */
			$.post(ajaxurl, data, function(r){
				if ( '1' == r ) {
					$('#ajax-response').empty();
					tr.fadeOut('normal', function(){ tr.remove(); });

					/**
					 * @summary Remove the term from the parent box and the tag cloud
					 *
					 * `data.match(/tag_ID=(\d+)/)[1]` matches the term id from the data variable.
					 * This term id is then used to select the relevant HTML elements:
					 * The parent box and the tag cloud.
					 */
					$('select#parent option[value="' + data.match(/tag_ID=(\d+)/)[1] + '"]').remove();
					$('a.tag-link-' + data.match(/tag_ID=(\d+)/)[1]).remove();

				} else if ( '-1' == r ) {
					$('#ajax-response').empty().append('<div class="error"><p>' + tagsl10n.noPerm + '</p></div>');
					tr.children().css('backgroundColor', '');

				} else {
					$('#ajax-response').empty().append('<div class="error"><p>' + tagsl10n.broken + '</p></div>');
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
	 * @returns {void}
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
	 * @summary Adds an event handler tot he form submit on the term overview page.
	 *
	 * Cancels default event handling and event bubbling.
	 *
	 * @since 2.8.0
	 *
	 * @returns boolean Always returns false to cancel the default event handling.
	 */
	$('#submit').click(function(){
		var form = $(this).parents('form');

		if ( ! validateForm( form ) )
			return false;

		/**
		 * Does a request to the server to add a new term to the database
		 *
		 * @param {string} r The response from the server.
		 *
		 * @returns {void}
		 */
		$.post(ajaxurl, $('#addtag').serialize(), function(r){
			var res, parent, term, indent, i;

			$('#ajax-response').empty();
			res = wpAjax.parseAjaxResponse( r, 'ajax-response' );
			if ( ! res || res.errors )
				return;

			parent = form.find( 'select#parent' ).val();

			if ( parent > 0 && $('#tag-' + parent ).length > 0 ) // If the parent exists on this page, insert it below. Else insert it at the top of the list.
				$( '.tags #tag-' + parent ).after( res.responses[0].supplemental.noparents ); // As the parent exists, Insert the version with - - - prefixed
			else
				$( '.tags' ).prepend( res.responses[0].supplemental.parents ); // As the parent is not visible, Insert the version with Parent - Child - ThisTerm

			$('.tags .no-items').remove();

			if ( form.find('select#parent') ) {
				// Parents field exists, Add new term to the list.
				term = res.responses[1].supplemental;

				// Create an indent for the Parent field
				indent = '';
				for ( i = 0; i < res.responses[1].position; i++ )
					indent += '&nbsp;&nbsp;&nbsp;';

				form.find( 'select#parent option:selected' ).after( '<option value="' + term.term_id + '">' + indent + term.name + '</option>' );
			}

			$('input[type="text"]:visible, textarea:visible', form).val('');
		});

		return false;
	});

});
