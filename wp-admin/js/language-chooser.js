/**
 * @output wp-admin/js/language-chooser.js
 */

jQuery( function($) {
/*
 * Set the correct translation to the continue button and show a spinner
 * when downloading a language.
 */
var select = $( '#language' ),
	submit = $( '#language-continue' );

if ( ! $( 'body' ).hasClass( 'language-chooser' ) ) {
	return;
}

select.trigger( 'focus' ).on( 'change', function() {
	/*
	 * When a language is selected, set matching translation to continue button
	 * and attach the language attribute.
	 */
	var option = select.children( 'option:selected' );
	submit.attr({
		value: option.data( 'continue' ),
		lang: option.attr( 'lang' )
	});
});

$( 'form' ).on( 'submit', function() {
	// Show spinner for languages that need to be downloaded.
	if ( ! select.children( 'option:selected' ).data( 'installed' ) ) {
		$( this ).find( '.step .spinner' ).css( 'visibility', 'visible' );
	}
});

});
