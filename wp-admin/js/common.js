addLoadEvent( function() {
	// pulse
	jQuery('.fade').animate( { backgroundColor: '#ffffe0' }, 300).animate( { backgroundColor: '#fffbcc' }, 300).animate( { backgroundColor: '#ffffe0' }, 300).animate( { backgroundColor: '#fffbcc' }, 300); 

	jQuery('.wp-no-js-hidden').removeClass( 'wp-no-js-hidden' );
});
