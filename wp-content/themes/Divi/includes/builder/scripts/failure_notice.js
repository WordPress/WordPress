(function($){
	$( document ).ready( function() {

		$( 'body' ).on( 'click', '.et_pb_prompt_dont_proceed', function() {
			var $modal_overlay = $( this ).closest( '.et_pb_modal_overlay' );

			// Unlock body scroll
			$( 'body' ).removeClass( 'et_pb_stop_scroll' );

			// add class to apply the closing animation to modal
			$modal_overlay.addClass( 'et_pb_modal_closing' );

			//remove the modal with overlay when animation complete
			setTimeout( function() {
				$modal_overlay.remove();
			}, 600 );

			return false;
		} );

	});
})(jQuery)