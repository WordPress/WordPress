/**
 * Javascript to make Customizer preview reflect changes without refresh.
 * @package Twenty8teen
 */

( function( $ ) {
	var rootCustomProperty = function( setting ) {
		var bStyle = document.createElement( 'style' );
		document.head.appendChild( bStyle );
		setting.bind( function( newval ) {
			bStyle.innerHTML = ':root { --' + setting.id + ': ' + newval + ' }';
		} );
	};

	// Header text color.
	wp.customize( 'header_textcolor', function( setting ) {
		var hStyle = document.createElement( 'style' );
		document.head.appendChild( hStyle );
		setting.bind( function( newval ) {
			var css = '.site-title, .site-description';
			if ( 'blank' === newval ) {
				css += ' { clip: rect(1px, 1px, 1px, 1px); position: absolute; } :root { --header_textcolor: var(--body_textcolor) }';
			} else {
				css += ' { clip: auto; position: relative } :root { --header_textcolor: ' + newval + ' } ';
			};
			hStyle.innerHTML = css;
		} );
	} );
	wp.customize( 'background_color', rootCustomProperty );
	wp.customize( 'accent_color', rootCustomProperty );
	wp.customize( 'body_textcolor', rootCustomProperty );
	wp.customize( 'link_color', rootCustomProperty );

	wp.customize( 'identimage_alpha', rootCustomProperty );
	wp.customize( 'font_size_adjust', function( setting ) {
		var bStyle = document.createElement( 'style' );
		document.head.appendChild( bStyle );
		setting.bind( function( newval ) {
			bStyle.innerHTML = ':root { --font_size_adjust: ' + newval * 2 + ' }';
		} );
	} );
	wp.customize( 'show_vignette', function( setting ) {
		setting.bind( function( show ) {
			$( 'body' ).toggleClass( 'vignette', show );
		} );
	} );
	wp.customize( 'show_header_imagebehind', function( setting ) {
		setting.bind( function( show ) {
			$( 'body' ).toggleClass( 'header-behind', show );
			$( '.header-image' ).toggleClass( 'image-behind', show );
		} );
	} );
	wp.customize( 'show_as_cards', function( setting ) {
		setting.bind( function( show ) {
			$( '.entry' ).toggleClass( 'cards', show );
		} );
	} );
	wp.customize( 'switch_sidebar', function( setting ) {
		setting.bind( function( show ) {
			$( 'body' ).toggleClass( 'sidebar-leading', show );
		} );
	} );
	wp.customize( 'start_in_tableview', function( setting ) {
		setting.bind( function( view ) {
			$( '.archive main, .search main, .blog main' ).toggleClass( 'table-view', view );
		} );
	} );

	var areaToggle = function( selector, classes ) {
		if ( classes.indexOf( ',' ) === -1 ) {
			classes = ',' + classes;
		}
		var list = classes.split( ',' );
		if ( list[0] ) {
			$( selector ).removeClass( list[0] );
		}
		if ( list[1] ) {
			$( selector ).addClass( list[1] );
		}
	};
	wp.customize( 'featured_image_classes', function( setting ) {
		setting.bind( function( classes ) {
			areaToggle( '.wp-post-image-identimage-wrap, .wp-post-image:not(.identimage)', classes );
		} );
	} );
	wp.customize( 'area_classes[header]', function( setting ) {
		setting.bind( function( classes ) {
			areaToggle( '#masthead', classes );
		} );
	} );
	wp.customize( 'area_classes[main]', function( setting ) {
		setting.bind( function( classes ) {
			areaToggle( 'main', classes );
		} );
	} );
	wp.customize( 'area_classes[content]', function( setting ) {
		setting.bind( function( classes ) {
			areaToggle( '#content', classes );
		} );
	} );
	wp.customize( 'area_classes[comments]', function( setting ) {
		setting.bind( function( classes ) {
			areaToggle( '#comments', classes );
		} );
	} );
	wp.customize( 'area_classes[sidebar]', function( setting ) {
		setting.bind( function( classes ) {
			areaToggle( '#sidebar', classes );
		} );
	} );
	wp.customize( 'area_classes[footer]', function( setting ) {
		setting.bind( function( classes ) {
			areaToggle( '#footer', classes );
		} );
	} );
	wp.customize( 'area_classes[widgets]', function( setting ) {
		setting.bind( function( classes ) {
			areaToggle( '.widget', classes );
		} );
	} );
/*
 * For pages with presets, disable the options in the preset so the preview is correct.
 * Child theme options need to load before this to ensure they are disabled.
 */
	if ( typeof twenty8teenPagePreset !== 'undefined' ) {
		_.each( twenty8teenPagePreset.vars, function( id ) {
			wp.customize( id, function( setting ) {
				setting.callbacks.disable();
			} );
		}	);
	}

} )( jQuery );
