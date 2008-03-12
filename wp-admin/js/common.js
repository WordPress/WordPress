addLoadEvent( function() {
	// pulse
	jQuery('.fade').animate( { backgroundColor: '#ffffe0' }, 300).animate( { backgroundColor: '#fffbcc' }, 300).animate( { backgroundColor: '#ffffe0' }, 300).animate( { backgroundColor: '#fffbcc' }, 300);

	// Reveal
	jQuery('.wp-no-js-hidden').removeClass( 'wp-no-js-hidden' );

	// Basic form validation
	if ( ( 'undefined' != typeof wpAjax ) && jQuery.isFunction( wpAjax.validateForm ) ) {
		jQuery('form').submit( function() { return wpAjax.validateForm( jQuery(this) ); } );
	}
});
