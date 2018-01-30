/**
 * Default settings for jQuery UI Autocomplete for use with non-hierarchical taxonomies.
 */
( function( $ ) {
	if ( typeof window.tagsSuggestL10n === 'undefined' || typeof window.uiAutocompleteL10n === 'undefined' ) {
		return;
	}

	var tempID = 0;
	var separator = window.tagsSuggestL10n.tagDelimiter || ',';

	function split( val ) {
		return val.split( new RegExp( separator + '\\s*' ) );
	}

	function getLast( term ) {
		return split( term ).pop();
	}

	/**
	 * Add UI Autocomplete to an input or textarea element with presets for use
	 * with non-hierarchical taxonomies.
	 *
	 * Example: `$( element ).wpTagsSuggest( options )`.
	 *
	 * The taxonomy can be passed in a `data-wp-taxonomy` attribute on the element or
	 * can be in `options.taxonomy`.
	 *
	 * @since 4.7.0
	 *
	 * @param {object} options Options that are passed to UI Autocomplete. Can be used to override the default settings.
	 * @returns {object} jQuery instance.
	 */
	$.fn.wpTagsSuggest = function( options ) {
		var cache;
		var last;
		var $element = $( this );

		options = options || {};

		var taxonomy = options.taxonomy || $element.attr( 'data-wp-taxonomy' ) || 'post_tag';

		delete( options.taxonomy );

		options = $.extend( {
			source: function( request, response ) {
				var term;

				if ( last === request.term ) {
					response( cache );
					return;
				}

				term = getLast( request.term );

				$.get( window.tagsSuggestL10n.restURL, {
					_fields: [ 'id', 'name' ],
					taxonomy: taxonomy,
					search: term
				} ).always( function() {
					$element.removeClass( 'ui-autocomplete-loading' ); // UI fails to remove this sometimes?
				} ).done( function( data ) {
					cache = data;
					response( data );
				} );

				last = request.term;
			},
			focus: function( event, ui ) {
				$element.attr( 'aria-activedescendant', 'wp-tags-autocomplete-' + ui.item.id );

				// Don't empty the input field when using the arrow keys to
				// highlight items. See api.jqueryui.com/autocomplete/#event-focus
				event.preventDefault();
			},
			select: function( event, ui ) {
				var tags = split( $element.val() );
				// Remove the last user input.
				tags.pop();
				// Append the new tag and an empty element to get one more separator at the end.
				tags.push( ui.item.name, '' );

				$element.val( tags.join( separator + ' ' ) );

				if ( $.ui.keyCode.TAB === event.keyCode ) {
					// Audible confirmation message when a tag has been selected.
					window.wp.a11y.speak( window.tagsSuggestL10n.termSelected, 'assertive' );
					event.preventDefault();
				} else if ( $.ui.keyCode.ENTER === event.keyCode ) {
					// Do not close Quick Edit / Bulk Edit
					event.preventDefault();
					event.stopPropagation();
				}

				return false;
			},
			open: function() {
				$element.attr( 'aria-expanded', 'true' );
			},
			close: function() {
				$element.attr( 'aria-expanded', 'false' );
			},
			minLength: window.tagsSuggestL10n.minChars,
			position: {
				my: 'left top+2',
				at: 'left bottom',
				collision: 'none'
			},
			messages: {
				noResults: window.uiAutocompleteL10n.noResults,
				results: function( number ) {
					if ( number > 1 ) {
						return window.uiAutocompleteL10n.manyResults.replace( '%d', number );
					}

					return window.uiAutocompleteL10n.oneResult;
				}
			}
		}, options );

		$element.on( 'keydown', function() {
			$element.removeAttr( 'aria-activedescendant' );
		} )
		.autocomplete( options )
		.autocomplete( 'instance' )._renderItem = function( ul, item ) {
			return $( '<li role="option" id="wp-tags-autocomplete-' + item.id + '">' )
				.text( item.name )
				.appendTo( ul );
		};

		$element.attr( {
			'role': 'combobox',
			'aria-autocomplete': 'list',
			'aria-expanded': 'false',
			'aria-owns': $element.autocomplete( 'widget' ).attr( 'id' )
		} )
		.on( 'focus', function() {
			var inputValue = split( $element.val() ).pop();

			// Don't trigger a search if the field is empty.
			// Also, avoids screen readers announce `No search results`.
			if ( inputValue ) {
				$element.autocomplete( 'search' );
			}
		} )
		// Returns a jQuery object containing the menu element.
		.autocomplete( 'widget' )
			.addClass( 'wp-tags-autocomplete' )
			.attr( 'role', 'listbox' )
			.removeAttr( 'tabindex' ) // Remove the `tabindex=0` attribute added by jQuery UI.

			// Looks like Safari and VoiceOver need an `aria-selected` attribute. See ticket #33301.
			// The `menufocus` and `menublur` events are the same events used to add and remove
			// the `ui-state-focus` CSS class on the menu items. See jQuery UI Menu Widget.
			.on( 'menufocus', function( event, ui ) {
				ui.item.attr( 'aria-selected', 'true' );
			})
			.on( 'menublur', function() {
				// The `menublur` event returns an object where the item is `null`
				// so we need to find the active item with other means.
				$( this ).find( '[aria-selected="true"]' ).removeAttr( 'aria-selected' );
			});

		return this;
	};

}( jQuery ) );
