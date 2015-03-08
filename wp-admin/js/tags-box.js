/* jshint curly: false, eqeqeq: false */
/* global ajaxurl */

var tagBox, array_unique_noempty;

( function( $ ) {
	// Return an array with any duplicate, whitespace or empty values removed
	array_unique_noempty = function( array ) {
		var out = [];

		$.each( array, function( key, val ) {
			val = $.trim( val );

			if ( val && $.inArray( val, out ) === -1 ) {
				out.push( val );
			}
		} );

		return out;
	};

	tagBox = {
		clean : function(tags) {
			var comma = window.tagsBoxL10n.tagDelimiter;
			if ( ',' !== comma )
				tags = tags.replace(new RegExp(comma, 'g'), ',');
			tags = tags.replace(/\s*,\s*/g, ',').replace(/,+/g, ',').replace(/[,\s]+$/, '').replace(/^[,\s]+/, '');
			if ( ',' !== comma )
				tags = tags.replace(/,/g, comma);
			return tags;
		},

		parseTags : function(el) {
			var id = el.id,
				num = id.split('-check-num-')[1],
				taxbox = $(el).closest('.tagsdiv'),
				thetags = taxbox.find('.the-tags'),
				comma = window.tagsBoxL10n.tagDelimiter,
				current_tags = thetags.val().split( comma ),
				new_tags = [];

			delete current_tags[num];

			$.each( current_tags, function( key, val ) {
				val = $.trim( val );
				if ( val ) {
					new_tags.push( val );
				}
			});

			thetags.val( this.clean( new_tags.join( comma ) ) );

			this.quickClicks( taxbox );
			return false;
		},

		quickClicks : function( el ) {
			var thetags = $('.the-tags', el),
				tagchecklist = $('.tagchecklist', el),
				id = $(el).attr('id'),
				current_tags, disabled;

			if ( ! thetags.length )
				return;

			disabled = thetags.prop('disabled');

			current_tags = thetags.val().split( window.tagsBoxL10n.tagDelimiter );
			tagchecklist.empty();

			$.each( current_tags, function( key, val ) {
				var span, xbutton;

				val = $.trim( val );

				if ( ! val )
					return;

				// Create a new span, and ensure the text is properly escaped.
				span = $('<span />').text( val );

				// If tags editing isn't disabled, create the X button.
				if ( ! disabled ) {
					xbutton = $( '<a id="' + id + '-check-num-' + key + '" class="ntdelbutton" tabindex="0">X</a>' );

					xbutton.on( 'click keypress', function( e ) {
						// Trigger function if pressed Enter - keyboard navigation
						if ( e.type === 'click' || e.keyCode === 13 ) {
							// When using keyboard, move focus back to the new tag field.
							if ( e.keyCode === 13 ) {
								$( this ).closest( '.tagsdiv' ).find( 'input.newtag' ).focus();
							}

							tagBox.parseTags( this );
						}
					});

					span.prepend( '&nbsp;' ).prepend( xbutton );
				}

				// Append the span to the tag list.
				tagchecklist.append( span );
			});
		},

		flushTags : function( el, a, f ) {
			var tagsval, newtags, text,
				tags = $( '.the-tags', el ),
				newtag = $( 'input.newtag', el ),
				comma = window.tagsBoxL10n.tagDelimiter;

			a = a || false;

			text = a ? $(a).text() : newtag.val();
			tagsval = tags.val();
			newtags = tagsval ? tagsval + comma + text : text;

			newtags = this.clean( newtags );
			newtags = array_unique_noempty( newtags.split( comma ) ).join( comma );
			tags.val( newtags );
			this.quickClicks( el );

			if ( ! a )
				newtag.val('');
			if ( 'undefined' == typeof( f ) )
				newtag.focus();

			return false;
		},

		get : function( id ) {
			var tax = id.substr( id.indexOf('-') + 1 );

			$.post( ajaxurl, { 'action': 'get-tagcloud', 'tax': tax }, function( r, stat ) {
				if ( 0 === r || 'success' != stat ) {
					return;
				}

				r = $( '<p id="tagcloud-' + tax + '" class="the-tagcloud">' + r + '</p>' );

				$( 'a', r ).click( function() {
					tagBox.flushTags( $( '#' + tax ), this );
					return false;
				});

				$( '#' + id ).after( r );
			});
		},

		init : function() {
			var t = this, ajaxtag = $('div.ajaxtag');

			$('.tagsdiv').each( function() {
				tagBox.quickClicks(this);
			});

			$('.tagadd', ajaxtag).click(function(){
				t.flushTags( $(this).closest('.tagsdiv') );
			});

			$('input.newtag', ajaxtag).keyup(function(e){
				if ( 13 == e.which ) {
					tagBox.flushTags( $(this).closest('.tagsdiv') );
					return false;
				}
			}).keypress(function(e){
				if ( 13 == e.which ) {
					e.preventDefault();
					return false;
				}
			}).each( function() {
				var tax = $(this).closest('div.tagsdiv').attr('id');
				$(this).suggest(
					ajaxurl + '?action=ajax-tag-search&tax=' + tax,
					{ delay: 500, minchars: 2, multiple: true, multipleSep: window.tagsBoxL10n.tagDelimiter + ' ' }
				);
			});

			// save tags on post save/publish
			$('#post').submit(function(){
				$('div.tagsdiv').each( function() {
					tagBox.flushTags(this, false, 1);
				});
			});

			// tag cloud
			$('.tagcloud-link').click(function(){
				tagBox.get( $(this).attr('id') );
				$(this).unbind().click(function(){
					$(this).siblings('.the-tagcloud').toggle();
					return false;
				});
				return false;
			});
		}
	};
}( jQuery ));
