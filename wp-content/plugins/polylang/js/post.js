// tag suggest
// valid for both tag metabox and quick edit
(function( $ ){
	$.ajaxPrefilter(function( options, originalOptions, jqXHR ) {
		if ( 'string' === typeof options.data && ( -1 !== options.url.indexOf( 'action=ajax-tag-search' ) || -1 !== options.data.indexOf( 'action=ajax-tag-search' ) ) && ( ( lang = $( '.post_lang_choice' ).val() ) || ( lang = $( ':input[name="inline_lang_choice"]' ).val() ) ) ) {
			options.data = 'lang=' + lang + '&' + options.data;
		}
	});
})( jQuery );

// overrides tagBox.get
(function( $ ){
	// overrides function to add the language
	tagBox.get = function( id ) {
		var tax = id.substr( id.indexOf( '-' ) + 1 );

		// add the language in the $_POST variable
		var data = {
			action: 'get-tagcloud',
			lang:   $( '.post_lang_choice' ).val(),
			tax:    tax
		}

		$.post( ajaxurl, data, function( r, stat ) {
			if ( 0 == r || 'success' != stat ) {
				r = wpAjax.broken;
			}

			r = $( '<div id="tagcloud-' + tax + '" class="the-tagcloud">' + r + '</div>' );
			$( 'a', r ).click(function(){
				tagBox.flushTags( $( this ).closest( '.inside' ).children( '.tagsdiv' ), this );
				return false;
			});

			// add an if else condition to allow modifying the tags outputed when switching the language
			if ( v = $( '.the-tagcloud' ).css( 'display' ) ) {
				$( '.the-tagcloud' ).replaceWith( r );
				$( '.the-tagcloud' ).css( 'display', v );
			}
			else {
				$( '#' + id ).after( r );
			}
		});
	}
})( jQuery );

// quick edit
(function( $ ) {
	$( document ).bind( 'DOMNodeInserted', function( e ) {
		var t = $( e.target );

		// WP inserts the quick edit from
		if ( 'inline-edit' == t.attr( 'id' ) ) {
			var post_id = t.prev().attr( 'id' ).replace( "post-", "" );

			if ( post_id > 0 ) {
				// language dropdown
				var select = t.find( ':input[name="inline_lang_choice"]' );
				var lang = $( '#lang_' + post_id ).html();
				select.val( lang ); // populates the dropdown

				filter_terms( lang ); // initial filter for category checklist
				filter_pages( lang ); // initial filter for parent dropdown

				// modify category checklist an parent dropdown on language change
				select.change(function() {
					filter_terms( $( this ).val() );
					filter_pages( $( this ).val() );
				});
			}
		}

		// filter category checklist
		function filter_terms( lang ) {
			if ( "undefined" != typeof( pll_term_languages ) ) {
				$.each( pll_term_languages, function( lg, term_tax ) {
					$.each( term_tax, function( tax, terms ) {
						$.each( terms, function( i ) {
							id = '#' + tax + '-' + pll_term_languages[ lg ][ tax ][ i ];
							lang == lg ? $( id ).show() : $( id ).hide();
						});
					});
				});
			}
		}

		// filter parent page dropdown list
		function filter_pages( lang ) {
			if ( "undefined" != typeof( pll_page_languages ) ) {
				$.each( pll_page_languages, function( lg, pages ) {
					$.each( pages, function( i ) {
						v = $( '#post_parent option[value="' + pll_page_languages[ lg ][ i ] + '"]' );
						lang == lg ? v.show() : v.hide();
					});
				});
			}
		}
	});
})( jQuery );

// update rows of translated posts when the language is modified in quick edit
// acts on ajaxSuccess event
(function( $ ) {
	$( document ).ajaxSuccess(function( event, xhr, settings ) {
		function update_rows( post_id ) {
			// collect old translations
			var translations = new Array;
			$( '.translation_' + post_id ).each(function() {
				translations.push( $( this ).parent().parent().attr( 'id' ).substring( 5 ) );
			});

			var data = {
				action:       'pll_update_post_rows',
				post_id:      post_id,
				translations: translations.join( ',' ),
				post_type:    $( "input[name='post_type']" ).val(),
				screen:       $( "input[name='screen']" ).val(),
				_pll_nonce:   $( "input[name='_inline_edit']" ).val() // reuse quick edit nonce
			}

			// get the modified rows in ajax and update them
			$.post( ajaxurl, data, function( response ) {
				if ( response ) {
					var res = wpAjax.parseAjaxResponse( response, 'ajax-response' );
					$.each( res.responses, function() {
						if ( 'row' == this.what ) {
							$( "#post-" + this.supplemental.post_id ).replaceWith( this.data );
						}
					});
				}
			});
		}

		var data = wpAjax.unserialize( settings.data ); // what were the data sent by the ajax request?
		if ( 'undefined' != typeof( data['action'] ) && 'inline-save' == data['action'] ) {
			update_rows( data['post_ID'] );
		}
	});
})( jQuery );

jQuery( document ).ready(function( $ ) {
	// collect taxonomies - code partly copied from WordPress
	var taxonomies = new Array();
	$( '.categorydiv' ).each(function(){
		var this_id = $( this ).attr( 'id' ), taxonomyParts, taxonomy;

		taxonomyParts = this_id.split( '-' );
		taxonomyParts.shift();
		taxonomy = taxonomyParts.join( '-' );
		taxonomies.push( taxonomy ); // store the taxonomy for future use

		// add our hidden field in the new category form - for each hierarchical taxonomy
		// to set the language when creating a new category
		$( '#' + taxonomy + '-add-submit' ).before( $( '<input />' )
			.attr( 'type', 'hidden' )
			.attr( 'id', taxonomy + '-lang' )
			.attr( 'name', 'term_lang_choice' )
			.attr( 'value', $( '.post_lang_choice' ).val() )
		);
	});

	// ajax for changing the post's language in the languages metabox
	$( '.post_lang_choice' ).change(function() {
		var value = $( this ).val();
		var lang  = $( this ).children( 'option[value="' + value + '"]' ).attr( 'lang' );
		var dir   = $( '.pll-translation-column > span[lang="' + lang + '"]' ).attr( 'dir' );

		var data = {
			action:     'post_lang_choice',
			lang:       value,
			post_type:  $( '#post_type' ).val(),
			taxonomies: taxonomies,
			post_id:    $( '#post_ID' ).val(),
			_pll_nonce: $( '#_pll_nonce' ).val()
		}

		$.post( ajaxurl, data , function( response ) {
			var res = wpAjax.parseAjaxResponse( response, 'ajax-response' );
			$.each( res.responses, function() {
				switch ( this.what ) {
					case 'translations': // translations fields
						$( '.translations' ).html( this.data );
						init_translations();
					break;
					case 'taxonomy': // categories metabox for posts
						var tax = this.data;
						$( '#' + tax + 'checklist' ).html( this.supplemental.all );
						$( '#' + tax + 'checklist-pop' ).html( this.supplemental.populars );
						$( '#new' + tax + '_parent' ).replaceWith( this.supplemental.dropdown );
						$( '#' + tax + '-lang' ).val( $( '.post_lang_choice' ).val() ); // hidden field
					break;
					case 'pages': // parent dropdown list for pages
						$( '#parent_id' ).html( this.data );
					break;
					case 'flag': // flag in front of the select dropdown
						$( '.pll-select-flag' ).html( this.data );
					break;
					case 'permalink': // Sample permalink
						var div = $( '#edit-slug-box' );
						if ( '-1' != this.data && div.children().length ) {
							div.html( this.data );
						}
					break;
				}
			});

			// modifies the language in the tag cloud
			$( '.tagcloud-link' ).each(function() {
				var id = $( this ).attr( 'id' );
				tagBox.get( id );
			});

			// Modifies the text direction
			$( 'body' ).removeClass( 'pll-dir-rtl' ).removeClass( 'pll-dir-ltr' ).addClass( 'pll-dir-' + dir );
			$( '#content_ifr' ).contents().find( 'html' ).attr( 'lang', lang ).attr( 'dir', dir );
			$( '#content_ifr' ).contents().find( 'body' ).attr( 'dir', dir );
		});
	});

	// translations autocomplete input box
	function init_translations() {
		$( '.tr_lang' ).each(function(){
			var tr_lang = $( this ).attr( 'id' ).substring( 8 );
			var td = $( this ).parent().parent().siblings( '.pll-edit-column' );

			$( this ).autocomplete({
				minLength: 0,

				source: ajaxurl + '?action=pll_posts_not_translated' +
					'&post_language=' + $( '.post_lang_choice' ).val() +
					'&translation_language=' + tr_lang +
					'&post_type=' + $( '#post_type' ).val() +
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
});
