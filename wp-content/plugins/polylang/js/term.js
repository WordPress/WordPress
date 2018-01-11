// quick edit
(function( $ ) {
	$( document ).bind( 'DOMNodeInserted', function( e ) {
		var t = $( e.target );

		// WP inserts the quick edit from
		if ( 'inline-edit' == t.attr( 'id' ) ) {
			var term_id = t.prev().attr( 'id' ).replace( "tag-", "" );

			if ( term_id > 0 ) {
				// language dropdown
				var select = t.find( ':input[name="inline_lang_choice"]' );
				var lang = $( '#lang_' + term_id ).html();
				select.val( lang ); // populates the dropdown

				// disable the language dropdown for default categories
				var default_cat = $( '#default_cat_' + term_id ).html();
				if ( term_id == default_cat ) {
					select.prop( 'disabled', true );
				}
			}
		}
	});
})( jQuery );


// update rows of translated terms when adding / deleting a translation or when the language is modified in quick edit
// acts on ajaxSuccess event
(function( $ ) {
	$( document ).ajaxSuccess(function( event, xhr, settings ) {
		function update_rows( term_id ) {
			// collect old translations
			var translations = new Array;
			$( '.translation_' + term_id ).each(function() {
				translations.push( $( this ).parent().parent().attr( 'id' ).substring( 4 ) );
			});

			var data = {
				action:       'pll_update_term_rows',
				term_id:      term_id,
				translations: translations.join( ',' ),
				taxonomy:     $( "input[name='taxonomy']" ).val(),
				post_type:    $( "input[name='post_type']" ).val(),
				screen:       $( "input[name='screen']" ).val(),
				_pll_nonce:   $( '#_pll_nonce' ).val()
			}

			// get the modified rows in ajax and update them
			$.post( ajaxurl, data, function( response ) {
				if ( response ) {
					var res = wpAjax.parseAjaxResponse( response, 'ajax-response' );
					$.each( res.responses, function() {
						if ( 'row' == this.what ) {
							$( "#tag-" + this.supplemental.term_id ).replaceWith( this.data );
						}
					});
				}
			});
		}

		var data = wpAjax.unserialize( settings.data ); // what were the data sent by the ajax request?
		if ( 'undefined' != typeof( data['action'] ) ) {
			switch ( data['action'] ) {
				// when adding a term, the new term_id is in the ajax response
				case 'add-tag':
					res = wpAjax.parseAjaxResponse( xhr.responseXML, 'ajax-response' );
					$.each( res.responses, function() {
						if ( 'term' == this.what ) {
							update_rows( this.supplemental.term_id );
						}
					});

					// and also reset translations hidden input fields
					$( '.htr_lang' ).val( 0 );
				break;

				// when deleting a term
				case 'delete-tag':
					update_rows( data['tag_ID'] );
				break;

				// in case the language is modified in quick edit and breaks translations
				case 'inline-save-tax':
					update_rows( data['tax_ID'] );
				break;
			}
		}
	});
})( jQuery );

jQuery( document ).ready(function( $ ) {
	// translations autocomplete input box
	function init_translations() {
		$( '.tr_lang' ).each(function(){
			var tr_lang = $( this ).attr( 'id' ).substring( 8 );
			var td = $( this ).parent().parent().siblings( '.pll-edit-column' );

			$( this ).autocomplete({
				minLength: 0,

				source: ajaxurl + '?action=pll_terms_not_translated' +
					'&term_language=' + $( '#term_lang_choice' ).val() +
					'&term_id=' + $( "input[name='tag_ID']" ).val() +
					'&taxonomy=' + $( "input[name='taxonomy']" ).val() +
					'&translation_language=' + tr_lang +
					'&post_type=' + typenow +
					'&_pll_nonce=' + $( '#_pll_nonce' ).val(),

				select: function( event, ui ) {
					$( '#htr_lang_' + tr_lang ).val( ui.item.id );
					td.html( ui.item.link );
				},
			});

			// when the input box is emptied
			$( this ).blur(function() {
				if ( ! $( this ).val() ) {
					$( '#htr_lang_' + tr_lang ).val( 0 );
					td.html( td.siblings( '.hidden' ).children().clone() );
				}
			});
		});
	}

	init_translations();

	// ajax for changing the term's language
	$( '#term_lang_choice' ).change(function() {
		var value = $( this ).val();
		var lang  = $( this ).children( 'option[value="' + value + '"]' ).attr( 'lang' );
		var dir   = $( '.pll-translation-column > span[lang="' + lang + '"]' ).attr( 'dir' );

		var data = {
			action:     'term_lang_choice',
			lang:       value,
			from_tag:   $( "input[name='from_tag']" ).val(),
			term_id:    $( "input[name='tag_ID']" ).val(),
			taxonomy:   $( "input[name='taxonomy']" ).val(),
			post_type:  typenow,
			_pll_nonce: $( '#_pll_nonce' ).val()
		}

		$.post( ajaxurl, data, function( response ) {
			var res = wpAjax.parseAjaxResponse( response, 'ajax-response' );
			$.each( res.responses, function() {
				switch ( this.what ) {
					case 'translations': // translations fields
						$( "#term-translations" ).html( this.data );
						init_translations();
					break;
					case 'parent': // parent dropdown list for hierarchical taxonomies
						$( '#parent' ).replaceWith( this.data );
					break;
					case 'tag_cloud': // popular items
						$( '.tagcloud' ).replaceWith( this.data );
					break;
					case 'flag': // flag in front of the select dropdown
						$( '.pll-select-flag' ).html( this.data );
					break;
				}
			});

			// Modifies the text direction
			$( 'body' ).removeClass( 'pll-dir-rtl' ).removeClass( 'pll-dir-ltr' ).addClass( 'pll-dir-' + dir );
		});
	});
});
