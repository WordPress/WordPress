/**
 * Function for turning a hex color into an RGB string.
 */
function stargazer_hex_to_rgb( hex ) {
	var color = /^#?([a-f\d]{2})([a-f\d]{2})([a-f\d]{2})$/i.exec( hex );

	return parseInt( color[1], 16 ) + ", " + parseInt( color[2], 16 ) + ", " + parseInt( color[3], 16 );
}

/**
 * Handles the customizer live preview settings.
 */
jQuery( document ).ready( function() {

	/*
	 * Shows a live preview of changing the site title.
	 */
	wp.customize( 'blogname', function( value ) {

		value.bind( function( to ) {

			jQuery( '#site-title a' ).html( to );

		} ); // value.bind

	} ); // wp.customize

	/*
	 * Shows a live preview of changing the site description.
	 */
	wp.customize( 'blogdescription', function( value ) {

		value.bind( function( to ) {

			jQuery( '#site-description' ).html( to );

		} ); // value.bind

	} ); // wp.customize

	/*
	 * Handles the header textcolor.  This code also accounts for the possibility that the header text color
	 * may be set to 'blank', in which case, any text in the header is hidden.
	 */
	wp.customize( 'header_textcolor', function( value ) {

		value.bind( function( to ) {

			/* If set to 'blank', hide the branding section and secondary menu. */
			if ( 'blank' === to ) {

				/* Hides branding and menu-secondary. */
				jQuery( '#branding' ).
					css( 'display', 'none' );

				/* Removes the 'display-header-text' <body> class. */
				jQuery( 'body' ).
					removeClass( 'display-header-text' );
			}

			/* Change the header and secondary menu colors. */
			else {

				/* Adds the 'display-header-text' <body> class. */
				jQuery( 'body' ).
					addClass( 'display-header-text' );

				/* Makes sures both branding and menu-secondary display. */
				jQuery( '#branding' ).
					css( 'display', 'block' );

				/* Changes the color of the site title link. */
				jQuery( '#site-title a' ).
					css( 'color', to );
			} // endif

		} ); // value.bind

	} ); // wp.customize

	/*
	 * Handes the header image.  This code replaces the "src" attribute for the image.
	 */
	wp.customize( 'header_image', function( value ) {

		value.bind( function( to ) {

			/* If removing the header image, make sure to hide it so there's not an error image. */
			if ( 'remove-header' === to ) {
				jQuery( '.header-image' ).hide();
			}

			/* Else, make sure to show the image and change the source. */
			else {
				jQuery( '.header-image' ).show();
				jQuery( '.header-image' ).attr( 'src', to );
			}

		} ); // value.bind

	} ); // wp.customize

	/*
	 * Handles the Primary color for the theme.  This color is used for various elements and at different 
	 * shades. It must set an rgba color value to handle the "shades".
	 */
	wp.customize( 'color_primary', function( value ) {

		value.bind( function( to ) {

			var rgb = stargazer_hex_to_rgb( to );

			/* special case: hover */

			jQuery( 'a, .mejs-button button' ).
				not( '#header a, .menu a, .entry-title a, #footer a, .media-info-toggle, .comment-reply-link, .comment-reply-login, .wp-playlist-item, .wp-playlist-caption' ).
				hover(
					function() {
						jQuery( this ).css( 'color', to );

					},
					function() {
						jQuery( this ).css( 'color', 'rgba( ' + rgb + ', 0.75 )' );
					}
			); // .hover

			jQuery( '.wp-playlist-light .wp-playlist-item, .wp-playlist-light .wp-playlist-caption' ).
				hover(
					function() {
						jQuery( this ).css( 'color', to );

					},
					function() {
						jQuery( this ).css( 'color', 'inherit' );
					}
			); // .hover

			/* color */

			jQuery( 'a, .wp-playlist-light .wp-playlist-playing' ).
				not( '#header a, .menu a, .entry-title a, #footer a, .media-info-toggle, .comment-reply-link, .comment-reply-login, .wp-playlist-caption' ).
				css( 'color', 'rgba( ' + rgb + ', 0.75 )' );

			jQuery( '#menu-secondary-items > li > a' ).
				css( 'color', 'rgba( ' + rgb + ', 0.75 )' );

			jQuery( 'legend, mark, .comment-respond .required, pre, .form-allowed-tags code, pre code' ).
				css( 'color', to );

			/* background-color */

			jQuery( "input[type='submit'], input[type='reset'], input[type='button'], button, .page-links a, .comment-reply-link, .comment-reply-login, .wp-calendar td.has-posts a, #menu-sub-terms li a" ).
				not( '.mejs-button button' ).
				css( 'background-color', 'rgba( ' + rgb + ', 0.8 )' );

			jQuery( 'blockquote' ).
				css( 'background-color', 'rgba( ' + rgb + ', 0.85 )' );

			jQuery( 'blockquote blockquote' ).
				css( 'background-color', 'rgba( ' + rgb + ', 0.9 )' );

			jQuery( 'legend, mark, pre, .form-allowed-tags code' ).
				css( 'background-color', 'rgba( ' + rgb + ', 0.1 )' );

			jQuery( '.widget-title > .wrap, #comments-number > .wrap, #reply-title > .wrap, .attachment-meta-title > .wrap, .widget_search > .search-form, .mejs-time-rail .mejs-time-loaded, .skip-link .screen-reader-text' ).
				css( 'background-color', to );

			/* border-color */

			jQuery( 'legend' ).css( 'border-color', 'rgba( ' + rgb + ', 0.15 )' );

			/* border-top-color */

			jQuery( 'body' ).css( 'border-top-color', to );

			/* border-bottom-color */

			jQuery( '.entry-content a, .entry-summary a, .comment-content a' ).
				css( 'border-bottom-color', 'rgba( ' + rgb + ', 0.15 )' );

			jQuery( 'body, .widget-title, #comments-number, #reply-title, .attachment-meta-title' ).
				css( 'border-bottom-color', to );

			/* outline-color */

			jQuery( 'blockquote' ).
				css( 'outline-color', 'rgba( ' + rgb + ', 0.85 )' );

		} ); // value.bind

	} ); // wp.customize

} ); // jQuery( document ).ready