/**
 * @output wp-admin/js/tags-box.js
 */

/* jshint curly: false, eqeqeq: false */
/* global ajaxurl, tagBox, array_unique_noempty */

( function( $ ) {
	var tagDelimiter = ( window.tagsSuggestL10n && window.tagsSuggestL10n.tagDelimiter ) || ',';

	/**
	 * Filters unique items and returns a new array.
	 *
	 * Filters all items from an array into a new array containing only the unique
	 * items. This also excludes whitespace or empty values.
	 *
	 * @since 2.8.0
	 *
	 * @global
	 *
	 * @param {Array} array The array to filter through.
	 *
	 * @return {Array} A new array containing only the unique items.
	 */
	window.array_unique_noempty = function( array ) {
		var out = [];

		// Trim the values and ensure they are unique.
		$.each( array, function( key, val ) {
			val = $.trim( val );

			if ( val && $.inArray( val, out ) === -1 ) {
				out.push( val );
			}
		} );

		return out;
	};

	/**
	 * The TagBox object.
	 *
	 * Contains functions to create and manage tags that can be associated with a
	 * post.
	 *
	 * @since 2.9.0
	 *
	 * @global
	 */
	window.tagBox = {
		/**
		 * Cleans up tags by removing redundant characters.
		 *
		 * @since 2.9.0
		 * @memberOf tagBox
		 *
		 * @param {string} tags Comma separated tags that need to be cleaned up.
		 *
		 * @return {string} The cleaned up tags.
		 */
		clean : function( tags ) {
			if ( ',' !== tagDelimiter ) {
				tags = tags.replace( new RegExp( tagDelimiter, 'g' ), ',' );
			}

			tags = tags.replace(/\s*,\s*/g, ',').replace(/,+/g, ',').replace(/[,\s]+$/, '').replace(/^[,\s]+/, '');

			if ( ',' !== tagDelimiter ) {
				tags = tags.replace( /,/g, tagDelimiter );
			}

			return tags;
		},

		/**
		 * Parses tags and makes them editable.
		 *
		 * @since 2.9.0
		 * @memberOf tagBox
		 *
		 * @param {Object} el The tag element to retrieve the ID from.
		 *
		 * @return {boolean} Always returns false.
		 */
		parseTags : function(el) {
			var id = el.id,
				num = id.split('-check-num-')[1],
				taxbox = $(el).closest('.tagsdiv'),
				thetags = taxbox.find('.the-tags'),
				current_tags = thetags.val().split( tagDelimiter ),
				new_tags = [];

			delete current_tags[num];

			// Sanitize the current tags and push them as if they're new tags.
			$.each( current_tags, function( key, val ) {
				val = $.trim( val );
				if ( val ) {
					new_tags.push( val );
				}
			});

			thetags.val( this.clean( new_tags.join( tagDelimiter ) ) );

			this.quickClicks( taxbox );
			return false;
		},

		/**
		 * Creates clickable links, buttons and fields for adding or editing tags.
		 *
		 * @since 2.9.0
		 * @memberOf tagBox
		 *
		 * @param {Object} el The container HTML element.
		 *
		 * @return {void}
		 */
		quickClicks : function( el ) {
			var thetags = $('.the-tags', el),
				tagchecklist = $('.tagchecklist', el),
				id = $(el).attr('id'),
				current_tags, disabled;

			if ( ! thetags.length )
				return;

			disabled = thetags.prop('disabled');

			current_tags = thetags.val().split( tagDelimiter );
			tagchecklist.empty();

			/**
			 * Creates a delete button if tag editing is enabled, before adding it to the tag list.
			 *
			 * @since 2.5.0
			 * @memberOf tagBox
			 *
			 * @param {string} key The index of the current tag.
			 * @param {string} val The value of the current tag.
			 *
			 * @return {void}
			 */
			$.each( current_tags, function( key, val ) {
				var listItem, xbutton;

				val = $.trim( val );

				if ( ! val )
					return;

				// Create a new list item, and ensure the text is properly escaped.
				listItem = $( '<li />' ).text( val );

				// If tags editing isn't disabled, create the X button.
				if ( ! disabled ) {
					/*
					 * Build the X buttons, hide the X icon with aria-hidden and
					 * use visually hidden text for screen readers.
					 */
					xbutton = $( '<button type="button" id="' + id + '-check-num-' + key + '" class="ntdelbutton">' +
						'<span class="remove-tag-icon" aria-hidden="true"></span>' +
						'<span class="screen-reader-text">' + window.tagsSuggestL10n.removeTerm + ' ' + listItem.html() + '</span>' +
						'</button>' );

					/**
					 * Handles the click and keypress event of the tag remove button.
					 *
					 * Makes sure the focus ends up in the tag input field when using
					 * the keyboard to delete the tag.
					 *
					 * @since 4.2.0
					 *
					 * @param {Event} e The click or keypress event to handle.
					 *
					 * @return {void}
					 */
					xbutton.on( 'click keypress', function( e ) {
						// On click or when using the Enter/Spacebar keys.
						if ( 'click' === e.type || 13 === e.keyCode || 32 === e.keyCode ) {
							/*
							 * When using the keyboard, move focus back to the
							 * add new tag field. Note: when releasing the pressed
							 * key this will fire the `keyup` event on the input.
							 */
							if ( 13 === e.keyCode || 32 === e.keyCode ) {
 								$( this ).closest( '.tagsdiv' ).find( 'input.newtag' ).focus();
 							}

							tagBox.userAction = 'remove';
							tagBox.parseTags( this );
						}
					});

					listItem.prepend( '&nbsp;' ).prepend( xbutton );
				}

				// Append the list item to the tag list.
				tagchecklist.append( listItem );
			});

			// The buttons list is built now, give feedback to screen reader users.
			tagBox.screenReadersMessage();
		},

		/**
		 * Adds a new tag.
		 *
		 * Also ensures that the quick links are properly generated.
		 *
		 * @since 2.9.0
		 * @memberOf tagBox
		 *
		 * @param {Object} el The container HTML element.
		 * @param {Object|boolean} a When this is an HTML element the text of that
		 *                           element will be used for the new tag.
		 * @param {number|boolean} f If this value is not passed then the tag input
		 *                           field is focused.
		 *
		 * @return {boolean} Always returns false.
		 */
		flushTags : function( el, a, f ) {
			var tagsval, newtags, text,
				tags = $( '.the-tags', el ),
				newtag = $( 'input.newtag', el );

			a = a || false;

			text = a ? $(a).text() : newtag.val();

			/*
			 * Return if there's no new tag or if the input field is empty.
			 * Note: when using the keyboard to add tags, focus is moved back to
			 * the input field and the `keyup` event attached on this field will
			 * fire when releasing the pressed key. Checking also for the field
			 * emptiness avoids to set the tags and call quickClicks() again.
			 */
			if ( 'undefined' == typeof( text ) || '' === text ) {
				return false;
			}

			tagsval = tags.val();
			newtags = tagsval ? tagsval + tagDelimiter + text : text;

			newtags = this.clean( newtags );
			newtags = array_unique_noempty( newtags.split( tagDelimiter ) ).join( tagDelimiter );
			tags.val( newtags );
			this.quickClicks( el );

			if ( ! a )
				newtag.val('');
			if ( 'undefined' == typeof( f ) )
				newtag.focus();

			return false;
		},

		/**
		 * Retrieves the available tags and creates a tagcloud.
		 *
		 * Retrieves the available tags from the database and creates an interactive
		 * tagcloud. Clicking a tag will add it.
		 *
		 * @since 2.9.0
		 * @memberOf tagBox
		 *
		 * @param {string} id The ID to extract the taxonomy from.
		 *
		 * @return {void}
		 */
		get : function( id ) {
			var tax = id.substr( id.indexOf('-') + 1 );

			/**
			 * Puts a received tag cloud into a DOM element.
			 *
			 * The tag cloud HTML is generated on the server.
			 *
			 * @since 2.9.0
			 *
			 * @param {number|string} r The response message from the AJAX call.
			 * @param {string} stat The status of the AJAX request.
			 *
			 * @return {void}
			 */
			$.post( ajaxurl, { 'action': 'get-tagcloud', 'tax': tax }, function( r, stat ) {
				if ( 0 === r || 'success' != stat ) {
					return;
				}

				r = $( '<div id="tagcloud-' + tax + '" class="the-tagcloud">' + r + '</div>' );

				/**
				 * Adds a new tag when a tag in the tagcloud is clicked.
				 *
				 * @since 2.9.0
				 *
				 * @return {boolean} Returns false to prevent the default action.
				 */
				$( 'a', r ).click( function() {
					tagBox.userAction = 'add';
					tagBox.flushTags( $( '#' + tax ), this );
					return false;
				});

				$( '#' + id ).after( r );
			});
		},

		/**
		 * Track the user's last action.
		 *
		 * @since 4.7.0
		 */
		userAction: '',

		/**
		 * Dispatches an audible message to screen readers.
		 *
		 * This will inform the user when a tag has been added or removed.
		 *
		 * @since 4.7.0
		 *
		 * @return {void}
		 */
		screenReadersMessage: function() {
			var message;

			switch ( this.userAction ) {
				case 'remove':
					message = window.tagsSuggestL10n.termRemoved;
					break;

				case 'add':
					message = window.tagsSuggestL10n.termAdded;
					break;

				default:
					return;
			}

			window.wp.a11y.speak( message, 'assertive' );
		},

		/**
		 * Initializes the tags box by setting up the links, buttons. Sets up event
		 * handling.
		 *
		 * This includes handling of pressing the enter key in the input field and the
		 * retrieval of tag suggestions.
		 *
		 * @since 2.9.0
		 * @memberOf tagBox
		 *
		 * @return {void}
		 */
		init : function() {
			var ajaxtag = $('div.ajaxtag');

			$('.tagsdiv').each( function() {
				tagBox.quickClicks( this );
			});

			$( '.tagadd', ajaxtag ).click( function() {
				tagBox.userAction = 'add';
				tagBox.flushTags( $( this ).closest( '.tagsdiv' ) );
			});

			/**
			 * Handles pressing enter on the new tag input field.
			 *
			 * Prevents submitting the post edit form. Uses `keypress` to take
			 * into account Input Method Editor (IME) converters.
			 *
			 * @since 2.9.0
			 *
			 * @param {Event} event The keypress event that occurred.
			 *
			 * @return {void}
			 */
			$( 'input.newtag', ajaxtag ).keypress( function( event ) {
				if ( 13 == event.which ) {
					tagBox.userAction = 'add';
					tagBox.flushTags( $( this ).closest( '.tagsdiv' ) );
					event.preventDefault();
					event.stopPropagation();
				}
			}).each( function( i, element ) {
				$( element ).wpTagsSuggest();
			});

			/**
			 * Before a post is saved the value currently in the new tag input field will be
			 * added as a tag.
			 *
			 * @since 2.9.0
			 *
			 * @return {void}
			 */
			$('#post').submit(function(){
				$('div.tagsdiv').each( function() {
					tagBox.flushTags(this, false, 1);
				});
			});

			/**
			 * Handles clicking on the tag cloud link.
			 *
			 * Makes sure the ARIA attributes are set correctly.
			 *
			 * @since 2.9.0
			 *
			 * @return {void}
			 */
			$('.tagcloud-link').click(function(){
				// On the first click, fetch the tag cloud and insert it in the DOM.
				tagBox.get( $( this ).attr( 'id' ) );
				// Update button state, remove previous click event and attach a new one to toggle the cloud.
				$( this )
					.attr( 'aria-expanded', 'true' )
					.unbind()
					.click( function() {
						$( this )
							.attr( 'aria-expanded', 'false' === $( this ).attr( 'aria-expanded' ) ? 'true' : 'false' )
							.siblings( '.the-tagcloud' ).toggle();
					});
			});
		}
	};
}( jQuery ));
