/**
 * This file is used on the term overview page to power quick-editing terms.
 *
 * @output wp-admin/js/inline-edit-tax.js
 */

/* global ajaxurl, inlineEditTax */

window.wp = window.wp || {};

/**
 * Consists of functions relevant to the inline taxonomy editor.
 *
 * @namespace inlineEditTax
 *
 * @property {string} type The type of inline edit we are currently on.
 * @property {string} what The type property with a hash prefixed and a dash
 *                         suffixed.
 */
( function( $, wp ) {

window.inlineEditTax = {

	/**
	 * Initializes the inline taxonomy editor by adding event handlers to be able to
	 * quick edit.
	 *
	 * @since 2.7.0
	 *
	 * @this inlineEditTax
	 * @memberof inlineEditTax
	 * @return {void}
	 */
	init : function() {
		var t = this, row = $('#inline-edit');

		t.type = $('#the-list').attr('data-wp-lists').substr(5);
		t.what = '#'+t.type+'-';

		$( '#the-list' ).on( 'click', '.editinline', function() {
			$( this ).attr( 'aria-expanded', 'true' );
			inlineEditTax.edit( this );
		});

		/**
		 * Cancels inline editing when pressing Escape inside the inline editor.
		 *
		 * @param {Object} e The keyup event that has been triggered.
		 */
		row.on( 'keyup', function( e ) {
			// 27 = [Escape].
			if ( e.which === 27 ) {
				return inlineEditTax.revert();
			}
		});

		/**
		 * Cancels inline editing when clicking the cancel button.
		 */
		$( '.cancel', row ).on( 'click', function() {
			return inlineEditTax.revert();
		});

		/**
		 * Saves the inline edits when clicking the save button.
		 */
		$( '.save', row ).on( 'click', function() {
			return inlineEditTax.save(this);
		});

		/**
		 * Saves the inline edits when pressing Enter inside the inline editor.
		 */
		$( 'input, select', row ).on( 'keydown', function( e ) {
			// 13 = [Enter].
			if ( e.which === 13 ) {
				return inlineEditTax.save( this );
			}
		});

		/**
		 * Saves the inline edits on submitting the inline edit form.
		 */
		$( '#posts-filter input[type="submit"]' ).on( 'mousedown', function() {
			t.revert();
		});
	},

	/**
	 * Toggles the quick edit based on if it is currently shown or hidden.
	 *
	 * @since 2.7.0
	 *
	 * @this inlineEditTax
	 * @memberof inlineEditTax
	 *
	 * @param {HTMLElement} el An element within the table row or the table row
	 *                         itself that we want to quick edit.
	 * @return {void}
	 */
	toggle : function(el) {
		var t = this;

		$(t.what+t.getId(el)).css('display') === 'none' ? t.revert() : t.edit(el);
	},

	/**
	 * Shows the quick editor
	 *
	 * @since 2.7.0
	 *
	 * @this inlineEditTax
	 * @memberof inlineEditTax
	 *
	 * @param {string|HTMLElement} id The ID of the term we want to quick edit or an
	 *                                element within the table row or the
	 * table row itself.
	 * @return {boolean} Always returns false.
	 */
	edit : function(id) {
		var editRow, rowData, val,
			t = this;
		t.revert();

		// Makes sure we can pass an HTMLElement as the ID.
		if ( typeof(id) === 'object' ) {
			id = t.getId(id);
		}

		editRow = $('#inline-edit').clone(true), rowData = $('#inline_'+id);
		$( 'td', editRow ).attr( 'colspan', $( 'th:visible, td:visible', '.wp-list-table.widefat:first thead' ).length );

		$(t.what+id).hide().after(editRow).after('<tr class="hidden"></tr>');

		val = $('.name', rowData);
		val.find( 'img' ).replaceWith( function() { return this.alt; } );
		val = val.text();
		$(':input[name="name"]', editRow).val( val );

		val = $('.slug', rowData);
		val.find( 'img' ).replaceWith( function() { return this.alt; } );
		val = val.text();
		$(':input[name="slug"]', editRow).val( val );

		$(editRow).attr('id', 'edit-'+id).addClass('inline-editor').show();
		$('.ptitle', editRow).eq(0).trigger( 'focus' );

		return false;
	},

	/**
	 * Saves the quick edit data.
	 *
	 * Saves the quick edit data to the server and replaces the table row with the
	 * HTML retrieved from the server.
	 *
	 * @since 2.7.0
	 *
	 * @this inlineEditTax
	 * @memberof inlineEditTax
	 *
	 * @param {string|HTMLElement} id The ID of the term we want to quick edit or an
	 *                                element within the table row or the
	 * table row itself.
	 * @return {boolean} Always returns false.
	 */
	save : function(id) {
		var params, fields, tax = $('input[name="taxonomy"]').val() || '';

		// Makes sure we can pass an HTMLElement as the ID.
		if( typeof(id) === 'object' ) {
			id = this.getId(id);
		}

		$( 'table.widefat .spinner' ).addClass( 'is-active' );

		params = {
			action: 'inline-save-tax',
			tax_type: this.type,
			tax_ID: id,
			taxonomy: tax
		};

		fields = $('#edit-'+id).find(':input').serialize();
		params = fields + '&' + $.param(params);

		// Do the Ajax request to save the data to the server.
		$.post( ajaxurl, params,
			/**
			 * Handles the response from the server
			 *
			 * Handles the response from the server, replaces the table row with the response
			 * from the server.
			 *
			 * @param {string} r The string with which to replace the table row.
			 */
			function(r) {
				var row, new_id, option_value,
					$errorNotice = $( '#edit-' + id + ' .inline-edit-save .notice-error' ),
					$error = $errorNotice.find( '.error' );

				$( 'table.widefat .spinner' ).removeClass( 'is-active' );

				if (r) {
					if ( -1 !== r.indexOf( '<tr' ) ) {
						$(inlineEditTax.what+id).siblings('tr.hidden').addBack().remove();
						new_id = $(r).attr('id');

						$('#edit-'+id).before(r).remove();

						if ( new_id ) {
							option_value = new_id.replace( inlineEditTax.type + '-', '' );
							row = $( '#' + new_id );
						} else {
							option_value = id;
							row = $( inlineEditTax.what + id );
						}

						// Update the value in the Parent dropdown.
						$( '#parent' ).find( 'option[value=' + option_value + ']' ).text( row.find( '.row-title' ).text() );

						row.hide().fadeIn( 400, function() {
							// Move focus back to the Quick Edit button.
							row.find( '.editinline' )
								.attr( 'aria-expanded', 'false' )
								.trigger( 'focus' );
							wp.a11y.speak( wp.i18n.__( 'Changes saved.' ) );
						});

					} else {
						$errorNotice.removeClass( 'hidden' );
						$error.html( r );
						/*
						 * Some error strings may contain HTML entities (e.g. `&#8220`), let's use
						 * the HTML element's text.
						 */
						wp.a11y.speak( $error.text() );
					}
				} else {
					$errorNotice.removeClass( 'hidden' );
					$error.text( wp.i18n.__( 'Error while saving the changes.' ) );
					wp.a11y.speak( wp.i18n.__( 'Error while saving the changes.' ) );
				}
			}
		);

		// Prevent submitting the form when pressing Enter on a focused field.
		return false;
	},

	/**
	 * Closes the quick edit form.
	 *
	 * @since 2.7.0
	 *
	 * @this inlineEditTax
	 * @memberof inlineEditTax
	 * @return {void}
	 */
	revert : function() {
		var id = $('table.widefat tr.inline-editor').attr('id');

		if ( id ) {
			$( 'table.widefat .spinner' ).removeClass( 'is-active' );
			$('#'+id).siblings('tr.hidden').addBack().remove();
			id = id.substr( id.lastIndexOf('-') + 1 );

			// Show the taxonomy row and move focus back to the Quick Edit button.
			$( this.what + id ).show().find( '.editinline' )
				.attr( 'aria-expanded', 'false' )
				.trigger( 'focus' );
		}
	},

	/**
	 * Retrieves the ID of the term of the element inside the table row.
	 *
	 * @since 2.7.0
	 *
	 * @memberof inlineEditTax
	 *
	 * @param {HTMLElement} o An element within the table row or the table row itself.
	 * @return {string} The ID of the term based on the element.
	 */
	getId : function(o) {
		var id = o.tagName === 'TR' ? o.id : $(o).parents('tr').attr('id'), parts = id.split('-');

		return parts[parts.length - 1];
	}
};

$(document).ready(function(){inlineEditTax.init();});

})( jQuery, window.wp );
